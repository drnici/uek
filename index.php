<?PHP
############################################################################################
$sys["root_path"] 	= "../../../";
$sys["script"] 		= false;
$sys["nav_id"]		= 28;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );
############################################################################################
if ( isset ( $_GET["uek_id"] ) ) 	$uek_id = mysql_escape_string ( htmlspecialchars ( $_GET["uek_id"] ) );
if ( isset ( $uek_id ) ) 			$uek_data = $db->fctSelectData ( "bew_uek" , "`uek_id` = '" . $uek_id . "'" );
############################################################################################
if ( !empty ( $uek_data["uek_id"] ) )
{
	$modul_data  = $db->fctSelectData ( "bew_modul" , "`modul_id` = " . $uek_data["modul_id"] );
	$person_data = $db->fctSelectData ( "core_person" , "`person_id` = " . $uek_data["person_id"] );
	$ort_data	 = $db->fctSelectData ( "bew_uek_ort" , "`ort_id` = " . $uek_data["ort_id"] );

	$sys["page_title"] = "Details f&uuml;r &Uuml;K " . $modul_data["modul_kurz"] . " JG " . $uek_data["uek_jg"] . "";
}
else
{
	$sys["page_title"] = "&Uumlberbetriebliche Kurse";
}
############################################################################################

// ANZEIGE F�R LERNENDE (ROLE_ID 1)
if ( $sys["user"]["role_id"] == 1 )
{
	if ( $sys["user"]["person_s_semester"] == 1 OR $sys["user"]["person_s_semester"] == 2 )
	{
		if ( isset ( $_GET["feedback"] ) )
		{
			if ( $_GET["feedback"] == "ok" )
			{
				echo ( "<p class=\"notification\"><b>Vielen Dank</b>: Ihr Feedback wurde erfolgreich gespeichert und wird wenn m�glich bei der �berarbeitung des �Ks beachtet.</p>\n" );
			}
		}
		
		// Aktuell aufgeschaltete Feedbacks anzeigen
		$time_fb_end = time ( ) - 60 * 60 * 24;
		$fb_count = $db->fctCountData ( "bew_uek" , "`uek_mp_time` < " . time ( ) . " AND `uek_mp_time` > " . $time_fb_end );

		if ( $fb_count > 0 )
		{
			echo ( "<h3>aktuelle Feedbacks</h3>\n" );
			
			echo ( "<table>\n" );
			echo ( "<tr>\n" );
			echo ( "<th>&nbsp;</th>\n" );
			echo ( "<th>&Uuml;K-Bezeichnung</th>\n" );
			echo ( "<th>Zeitpunkt Modulpr�fung</th>\n" );
			echo ( "</tr>\n" );
			$fb_result = $db->fctSendQuery ( "SELECT bm.modul_kurz, bm.modul_bezeichnung, bu.uek_id, bu.uek_mp_time FROM `bew_modul` AS bm, `bew_uek` AS bu WHERE bu.modul_id = bm.modul_id AND bu.uek_mp_time < " . time ( ) . " AND bu.uek_mp_time > " . $time_fb_end );
			
			while ( $fb_data = mysql_fetch_array ( $fb_result ) )
			{
				echo ( "<tr>\n" );
				echo ( "<td><img src=\"" . $sys["icon_path"] . "bew_uek_feedback.gif\" alt=\"&Uuml;K-Feedback\" border=\"0\" /></td>\n" );
				echo ( "<td><a href=\"./feedback/new/?uek_id=" . $fb_data["uek_id"] . "\">" . $fb_data["modul_kurz"] . " " . $fb_data["modul_bezeichnung"] . "</a></td>\n" );
				echo ( "<td>" . date ( $conf->strDateFormatFull , $fb_data["uek_mp_time"] ) . "</td>\n" );
				echo ( "</tr>\n" );
			}
			echo ( "</table>\n" );
			
			echo ( "<h3>Abgeschlossene �K</h3>\n" );
		}
	}
	
	$lde_count = $db->fctCountData ( "bew_uek_res" , "`person_id` = " . $sys["user"]["person_id"] );
	
	if ( $lde_count == 0 )
	{
		echo ( "<p>Es liegen noch keine Resultate vor. Sobald diese im System eingetragen werden, k&ouml;nnen Sie hier den Kompetenzausweis ansehen und herunterladen.</p>\n" );
	}
	else
	{
		echo ( "<table>\n" );
		echo ( "<tr>\n" );
		echo ( "<th>&nbsp;</th>\n" );
		echo ( "<th>&Uuml;K-Bezeichnung</th>\n" );
		echo ( "<th>Modulnote</th>\n" );
		echo ( "<th>&Uuml;K-Notenschnitt</th>\n" );
		echo ( "<th>KA</th>\n" );
		echo ( "</tr>\n" );
		
		$res_result = $db->fctSendQuery ( "SELECT bur.res_id, bur.res_note, bu.uek_id, bm.modul_kurz, bm.modul_bezeichnung FROM `bew_uek_res` AS bur, `bew_uek` AS bu, `bew_modul` AS bm WHERE bur.uek_id = bu.uek_id AND bu.modul_id = bm.modul_id AND bur.person_id = " . $sys["user"]["person_id"] . " ORDER BY bm.modul_kurz" );
		while ( $res_data = mysql_fetch_array ( $res_result ) )
		{
			echo ( "<tr>\n" );
			echo ( "<td><img src=\"" . $sys["icon_path"] . "bew_uek_res.gif\" alt=\"&Uuml;K-Bewertung\" border=\"0\" /></td>\n" );
			echo ( "<td>" . $res_data["modul_kurz"] . " " . $res_data["modul_bezeichnung"] . "</td>\n" );
			
			// Modulnote berechnen
			echo ( "<td>" . $res_data["res_note"] . "</td>\n" );
			
			// Uek-Notenschnitt berechnen
			$schnitt_data = mysql_fetch_array ( $db->fctSendQuery ( "SELECT SUM(bur.res_note), COUNT(*) FROM `bew_uek_res` AS bur WHERE bur.uek_id = " . $res_data["uek_id"] ) );
			$notenschnitt = bcdiv ( $schnitt_data[0] , $schnitt_data[1] , 2 );
			
			if ( $notenschnitt < $res_data["res_note"] ) 		$vergleich = "+";
			elseif ( $notenschnitt > $res_data["res_note"] ) 	$vergleich = "-";
			else												$vergleich = "=";
	
			echo ( "<td>" . $notenschnitt . " <b>(" . $vergleich . ")</b></td>\n" );
			
			echo ( "<td><a href=\"./komp/?res_id=" . $res_data["res_id"] . "&amp;\" title=\"Kompetenzausweis als RTF herunterladen\"><img src=\"" . $sys["icon_path"] . "global_rtf.gif\" alt=\"Kompetenzausweis als RTF herunterladen\" border=\"0\" /></a></td>\n" );
			echo ( "</tr>\n" );
		}
		echo ( "</table>\n" );
    }
}
// ANZEIGE F�R BERUFSBILDNER (ROLE_ID 2)
elseif ( $sys["user"]["role_id"] == 2 )
{
	$lde_count = $db->fctCountData ( "core_person" , "`berufsbildner_id` = " . $sys["user"]["person_id"] );

	if ( $lde_count == 0 )
	{
		echo ( "<p>Ihnen wurden noch keine Lernenden zugeteilt. Deshalb k�nnen Sie hier keine �K-Resultate einsehen.</p>\n" );
	}
	else
	{
		$uek_count = end ( mysql_fetch_array ( $db->fctSendQuery ( "SELECT count(*) FROM `bew_uek_res` AS bur, `core_person` AS cp WHERE cp.berufsbildner_id = " . $sys["user"]["person_id"] . " AND cp.person_id = bur.person_id" ) ) );
		
		if ( $uek_count == 0 )
		{
			echo ( "<p>Zu ihren Lernenden gibt es bisher noch keine eingetragenen �K-Resulate.</p>\n" );
		}
		else
		{
			echo ( "<table>\n" );
			echo ( "<tr>\n" );
			echo ( "<th>&nbsp;</th>\n" );
			echo ( "<th>Lernende/r</th>\n" );
			echo ( "<th>&Uuml;K-Bezeichnung</th>\n" );
			echo ( "<th>Modulnote</th>\n" );
			echo ( "<th>&Uuml;K-Notenschnitt</th>\n" );
			echo ( "<th>KA</th>\n" );
			echo ( "</tr>\n" );
			
			$res_result = $db->fctSendQuery ( "SELECT cp.person_id, cp.person_vorname, cp.person_name, cp.person_s_semester, bur.res_id, bur.res_note, bu.uek_id, bm.modul_kurz, bm.modul_bezeichnung FROM `bew_uek_res` AS bur, `bew_uek` AS bu, `bew_modul` AS bm, `core_person` AS cp WHERE bur.uek_id = bu.uek_id AND bu.modul_id = bm.modul_id AND bur.person_id = cp.person_id AND cp.berufsbildner_id = " . $sys["user"]["person_id"] . " AND cp.person_deactivation = 0 ORDER BY cp.person_vorname, cp.person_name, bm.modul_kurz" );
			while ( $res_data = mysql_fetch_array ( $res_result ) )
			{
				echo ( "<tr>\n" );
				echo ( "<td><img src=\"" . $sys["icon_path"] . "bew_uek_res.gif\" alt=\"&Uuml;K-Bewertung\" border=\"0\" /></td>\n" );
				echo ( "<td><a href=\"../../../core/person/profile/?person_id=" . $res_data["person_id"] . "&amp;\">" . $res_data["person_vorname"] . " " . $res_data["person_name"] . "</a></td>\n" );
				echo ( "<td>" . $res_data["modul_kurz"] . " " . substr ( $res_data["modul_bezeichnung"] , 0 , 30 ) . "</td>\n" );
				
				// Modulnote berechnen
				echo ( "<td>" . $res_data["res_note"] . "</td>\n" );
				
				// Uek-Notenschnitt berechnen
				$schnitt_data = mysql_fetch_array ( $db->fctSendQuery ( "SELECT SUM(bur.res_note), COUNT(*) FROM `bew_uek_res` AS bur, `bew_uek` AS bu WHERE bu.uek_id = bur.uek_id AND bu.uek_id = " . $res_data["uek_id"] ) );
				
				$notenschnitt = bcdiv ( $schnitt_data[0] , $schnitt_data[1] , 2 );
				
				if ( $notenschnitt < $res_data["res_note"] ) 		$vergleich = "+";
				elseif ( $notenschnitt > $res_data["res_note"] )	$vergleich = "-";
				else												$vergleich = "=";
						
				echo ( "<td>" . $notenschnitt . " <b>(" . $vergleich . ")</b></td>\n" );
				
				echo ( "<td><a href=\"./komp/?res_id=" . $res_data["res_id"] . "&amp;\" title=\"Kompetenzausweis als RTF herunterladen\"><img src=\"" . $sys["icon_path"] . "global_rtf.gif\" alt=\"Kompetenzausweis als RTF herunterladen\" border=\"0\" /></a></td>\n" );
				echo ( "</tr>\n" );
			}
			echo ( "</table>\n" );
		}
    }
}
// ANZEIGE F�R FIRMENVERANTWORTLICHE, AUSBILDUNGSLEITER UND ADMINISTRATOREN (ROLE_ID 3 - 5)
else
{
	if ( !empty ( $uek_data["uek_id"] ) )
	{
	?>
		<h3>Allgemeine Informationen</h3>
		
		<table>
		<tr>
		<th>Nummer / Bezeichnung</th>
		<td><?PHP echo ( $modul_data["modul_kurz"] . " " . $modul_data["modul_bezeichnung"] ); ?></td>
		</tr>
		<tr>
		<th>Lernenden-Jahrgang</th>
		<td><?PHP echo ( $uek_data["uek_jg"] ); ?></td>
		</tr>
		<tr>
		<th>Kursleitung</th>
		<td><a href="../../../core/person/profile/?person_id=<?PHP echo ( $person_data["person_id"] ); ?>"><?PHP echo ( $person_data["person_vorname"] . " " . $person_data["person_name"] ); ?></a></td>
		</tr>
		<tr>
		<th>Kursanbieter</th>
		<td><?PHP echo ( $ort_data["ort_firma"] ); ?></td>
		</tr>
		<tr>
		<th>Kursort</th>
		<td><?PHP echo ( $ort_data["ort_adresse"] ); ?>, <?PHP echo ( $ort_data["ort_plz"] ); ?> <?PHP echo ( $ort_data["ort_ort"] ); ?></td>
		</tr>
		<tr>
		<th>Zeitraum</th>
		<td><?PHP echo ( date ( $conf->strDateFormat , $uek_data["uek_time_start"] ) . " - " . date ( $conf->strDateFormat , $uek_data["uek_time_end"] ) ); ?></td>
		</tr>
		<tr>
		<th>�K-Tage</th>
		<td><?PHP echo ( $uek_data["uek_tage"] ); ?></td>
		</tr>
		<tr>
		<th>MP-Datum</th>
		<td><?PHP echo ( date ( $conf->strDateFormatFull , $uek_data["uek_mp_time"] ) ); ?></td>
		</tr>
		<tr>
		<th>MP-Nummer</th>
		<td><?PHP echo ( $uek_data["uek_mpnr"] ); ?></td>
		</tr>
		</table>
        
        <p>
          <?PHP
		if ( $sys["user"]["role_id"] == 5 )
		{
			echo ( "<p>\n" );
			echo ( "<img src=\"" . $sys["icon_path"] . "global_xls.gif\" alt=\"Teilnehmerliste herunterladen\" border=\"0\" /> <a href=\"./export.php?uek_id=" . $uek_data["uek_id"] . "&amp;type=tl&amp;\">Teilnehmerliste herunterladen</a><br />\n" );
			echo ( "<img src=\"" . $sys["icon_path"] . "global_xls.gif\" alt=\"Anwesenheitsliste herunterladen\" border=\"0\" /> <a href=\"./export.php?uek_id=" . $uek_data["uek_id"] . "&amp;type=al&amp;\">Anwesenheitsliste herunterladen</a><br />\n" );
			echo ( "<img src=\"" . $sys["icon_path"] . "global_xls.gif\" alt=\"Notentabelle herunterladen\" border=\"0\" /> <a href=\"./export.php?uek_id=" . $uek_data["uek_id"] . "&amp;type=nl&amp;\">Notentabelle herunterladen</a>\n" );

			echo ( "</p>\n" );
		}
		
		?>
        </p>
        <h3>Bewertung        </h3>
        <?PHP
		// aktuelles Semester der Lernenden bezogen auf den UEK-Jahrgang berechnen
		if ( date ( "m" ) < 8 ) $calc_year = date ( "Y" ) - 1;
		else					$calc_year = date ( "Y" );
        $diff = $calc_year - $uek_data["uek_jg"];
		$jg_first  = 1 + ( 2 * $diff );
		$jg_second = 2 + ( 2 * $diff );
	
		$bew_count = $db->fctCountData ( "bew_uek_res" , "`uek_id` = " . $uek_data["uek_id"] );
		$lde_count = $db->fctCountData ( "core_person" , "`role_id` = 1 AND (`person_s_semester` = " . $jg_first . " OR `person_s_semester` = " . $jg_second . ") AND `beruf_id` = 1" );
	
		if ( $bew_count < $lde_count AND $sys["user"]["role_id"] == 5 )
		{
			echo ( "<p><img src=\"" . $sys["icon_path"] . "bew_uek_res_add.gif\" alt=\"Neues Resultat eintragen\" border=\"0\" /> <a href=\"res/res_add.sta.php?uek_id=" . $uek_data["uek_id"] . "&amp;KeepThis=true&amp;TB_iframe=true&amp;height=340&amp;width=600&amp;modal=true\" class=\"thickbox\">Neues Resultat eintragen</a></p>\n" );
		}
		
		if ( $bew_count == 0 )
		{
			echo ( "<p>Zu diesem �berbetrieblichen Kurs liegen im Moment noch keine Resultate vor.</p>\n" );
		}
		else
		{
		?>
			<table>
			<tr>
			<th>&nbsp;</th>
			<th>Lernende/r</th>
			<?PHP /* <th>Methodenkomp. <?PHP echo ( $uek_data["uek_faktor_methode"] * 100 ); ?>%</th> */ ?>
			<?PHP /* <th>Fachkomp. <?PHP echo ( $uek_data["uek_faktor_fach"] * 100 ); ?>%</th> */ ?>
			<th>Modulnote</th>
			<th>Kompetenzausweis</th>
			<?PHP if ( $sys["user"]["role_id"] == 5 ) echo ( "<th>&nbsp;</th>\n" ); ?>
			</tr>
			
			<?PHP
			$count_avg_res = 0;
			$j = 0;
			$row_highlight = true;
			$res_result = $db->fctSendQuery ( "SELECT cp.person_id, cp.person_vorname, cp.person_name, bur.res_id, bur.res_note FROM `core_person` AS cp, `bew_uek_res` AS bur WHERE bur.person_id = cp.person_id AND bur.uek_id = " . $uek_data["uek_id"] . " AND cp.role_id = 1 ORDER BY cp.person_vorname ASC, cp.person_name ASC" );
			while ( $res_data = mysql_fetch_array ( $res_result ) )
			{
				// nur resultate von personen anzeigen, die auch wirklich angesehen werden d�rfen
				if ( fctCheckAccess ( $sys["user"] , $res_data["person_id"] , $db ) )
				{
					if ( $row_highlight )
					{
						echo ( "<tr class=\"row_highlight\">\n" ); $row_highlight = false;
					}
					else
					{
						echo ( "<tr>\n" ); $row_highlight = true;
					}
					
					echo ( "<td><img src=\"" . $sys["icon_path"] . "bew_uek_res.gif\" alt=\"&Uuml;K-Bewertung\" border=\"0\" /></td>\n" );
					echo ( "<td><a href=\"../../../core/person/profile/?person_id=" . $res_data["person_id"] . "&\">" . $res_data["person_vorname"] . " " . $res_data["person_name"] . "</a></td>\n" );
					
					if ( $res_data["res_note"] < 4 ) 	echo ( "<td style=\"color: #FF0000;\"><b>" . $res_data["res_note"] . "</b></td>\n" );
					else echo ( "<td><b>" . $res_data["res_note"] . "</b></td>\n" );
					
					echo ( "<td><a href=\"./komp/?res_id=" . $res_data["res_id"] . "&amp;\" title=\"Kompetenzausweis als RTF herunterladen\"><img src=\"" . $sys["icon_path"] . "global_rtf.gif\" alt=\"Kompetenzausweis als RTF herunterladen\" border=\"0\" /></a></td>\n" );
					
					if ( $sys["user"]["role_id"] == 5 )
					{
						echo ( "<td nowrap=\"nowrap\">\n" );
						echo ( "<a href=\"res/res_edit.sta.php?res_id=" . $res_data["res_id"] . "&amp;KeepThis=true&amp;TB_iframe=true&amp;height=340&amp;width=600&amp;modal=true\" class=\"thickbox\" title=\"Resultat bearbeiten\"><img src=\"" . $sys["icon_path"] . "bew_uek_res_edit.gif\" alt=\"Resultat bearbeiten\" border=\"0\" /></a>\n" );
						echo ( "<a href=\"res/res_del.sta.php?res_id=" . $res_data["res_id"] . "&amp;KeepThis=true&amp;TB_iframe=true&amp;height=340&amp;width=460&amp;modal=true\" class=\"thickbox\" title=\"Resultat l&ouml;schen\"><img src=\"" . $sys["icon_path"] . "bew_uek_res_del.gif\" alt=\"Resultat l&ouml;schen\" border=\"0\" /></a>\n" );
						echo ( "</td>\n" );
					}
					
					echo ( "</tr>\n" );
					
					$count_avg_res += $res_data["res_note"];
					$j++;

				}
			}
			?>
			</table>
		<?PHP
			echo ( "<p>Durchschnittliche Bewertung: " . bcdiv ( $count_avg_res , $j , 2 ) . "</p>\n" );

		}
		
		$bogen_count = $db->fctCountData ( "bew_uek_fb_bogen" , "`uek_id` = " . $uek_data["uek_id"] );
		if ( $bogen_count > 0 )
		{
	
			echo ( "<h3>Feedback</h3>\n" );
			
			if ( $sys["user"]["role_id"] == 5 ) echo ( "<p><img src=\"" . $sys["icon_path"] . "global_xls.gif\" alt=\"Feedbacks als CSV-Datei herunterladen\" border=\"0\" /> <a href=\"./feedback/export.php?uek_id=" . $uek_data["uek_id"] . "\">Feedbacks als CSV-Datei herunterladen</a></p>\n" );
		?>
		<table>
		  <tr>
			<th>&nbsp;</th>
			<th>Feedback vom</th>
			<th>Bewertung (Top: 1, Flop: 4)</th>
			<?PHP if ( $sys["user"]["role_id"] == 5 ) echo ( "<th>Lernende/r</th>\n" ); ?>
			</tr>
			
			<?PHP
			$row_highlight 	= true;
			$count_avg		= 0;
			$i				= 0;
			
			$feedback_result = $db->fctSendQuery ( "SELECT bufb.bogen_id, bufb.bogen_time, AVG ( buf.antwort_id ) AS `bogen_schnitt`, bufb.person_id FROM `bew_uek_fb_bogen` AS bufb, `bew_uek_feedback` AS buf WHERE bufb.uek_id = " . $uek_data["uek_id"] . " AND bufb.bogen_id = buf.bogen_id GROUP BY bufb.bogen_id ORDER BY bufb.bogen_time DESC" );
			while ( $feedback_data = mysql_fetch_array ( $feedback_result ) )
			{
				if ( $row_highlight )
				{
					echo ( "<tr class=\"row_highlight\">\n" ); $row_highlight = false;
				}
				else
				{
					echo ( "<tr>\n" ); $row_highlight = true;
				}
				echo ( "<td><img src=\"" . $sys["icon_path"] . "bew_uek_feedback.gif\" alt=\"&Uuml;K-Feedback\" border=\"0\" /></td>\n" );
				echo ( "<td><a href=\"./feedback/?bogen_id=" . $feedback_data["bogen_id"] . "&\">" . date ( $conf->strDateFormatFull , $feedback_data["bogen_time"] ) . "</a></td>\n" );
				
				echo ( "<td>" . bcdiv ( $feedback_data["bogen_schnitt"] , 1 , 1 ) . "</td>\n" );
				
				$count_avg += bcdiv ( $feedback_data["bogen_schnitt"] , 1 , 1 );
				$i++;
				
				if ( $sys["user"]["role_id"] == 5 )
				{
					if ( is_numeric ( $feedback_data["person_id"] ) )
					{
						$person_data = $db->fctSelectData ( "core_person" , "`person_id` = " . $feedback_data["person_id"] );
						echo ( "<td><a href=\"../../../core/person/profile/?person_id=" . $person_data["person_id"] . "&\">" . $person_data["person_vorname"] . " " . $person_data["person_name"] . "</a></td>\n" );
					}
					else echo ( "<td>anonym</td>\n" );
				}
				
				echo ( "</tr>\n" );
			}
			?>
			</table>
		<?PHP
			echo ( "<p>Durchschnittliche Bewertung: " . bcdiv ( $count_avg , $i , 2 ) . "</p>\n" );
		}
		?>
		
		<p>
			<?PHP
            if ( $sys["user"]["role_id"] == 5  )
			{
			?>
            <input type="button" class="btn" value="&Uuml;K-Eintrag bearbeiten" onclick="self.location.href='./form/?uek_id=<?PHP echo ( $uek_data["uek_id"] ); ?>';" />
            <?PHP
			}
			?>
			<input type="button" class="btn" value="zur&uuml;ck zur &Uuml;K-&Uuml;bersicht" onclick="self.location.href='./';" />
		</p>
	<?PHP
	}
	else
	{
		echo ( "<p>\n" );
		
		echo ( "<img src=\"" . $sys["icon_path"] . "global_pdf.gif\" alt=\"Checkliste Modulpr&uuml;fung\" border=\"0\" /> <a href=\"/files/content/20100924_checkliste_pruefung.pdf\">Checkliste Modulpr&uuml;fung</a><br />\n" );




		if ( $sys["user"]["role_id"] == 5 ){
			echo ( "<img src=\"" . $sys["icon_path"] . "global_add.gif\" alt=\"&Uuml;K-Eintrag erstellen\" border=\"0\" /> <a href=\"./form/\">&Uuml;K-Eintrag erstellen</a><br />\n" );
			echo("<img src=\"" . $sys["icon_path"] . "global_xls.gif\" alt=\"Alle Notentabellen\" border=\"0\" /> Notentabelle von </a><input id=\"jahrNT\" type=\"text\" placeholder=\"Jahr\"><button value=\"./export.php?type=nal&amp;jahr=\" onclick=\"fctOpenLink(this.value,document.getElementById('jahrNT').value)\">exportieren</button><br/>\n");

			echo("<img src=\"" . $sys["icon_path"] . "global_xls.gif\" alt=\"ÜK-Perioden\" border=\"0\" /> Feedbackb&oumlgen von ");
			//Mögliche Jahrgänge aus Datenbank auslesen
			$jg_result = $db->fctSendQuery("SELECT uek.uek_jg FROM `bew_uek` AS uek GROUP BY uek.uek_jg ORDER BY uek.uek_jg DESC");
			echo("<select style=\"overflow: hidden\" name=\"jg_uek_periode\" size=\"0\">\n");


			while ($jg = mysql_fetch_array($jg_result)) {
				echo("<option value=\"" . $jg["uek_jg"] . "\">" . $jg["uek_jg"] . "</option>\n");
			}
			echo("</select>\n");


			echo('<script type="text/javascript">function fctOpenLink(href,jahr){
			window.location.href = href + jahr+"&amp";
}</script>');
		}
		echo ( "</p>\n" );
	?>
		<table>
		<tr>
		<th>&nbsp;</th>
		<th>JG</th>
		<th>Nummer / Bezeichnung</th>
		<th>Zeitraum</th>
		<th>Kursleitung</th>
		<?PHP if ( $sys["user"]["role_id"] == 5 ) echo ( "<th>&nbsp;</th>\n" ); ?>
		</tr>
		
		<?PHP
		$row_highlight = true;
		$uek_result = $db->fctSendQuery ( "SELECT bu.uek_jg, bu.uek_id, bm.modul_kurz, bm.modul_bezeichnung, bu.uek_time_start, bu.uek_time_end, cp.person_id, cp.person_vorname, cp.person_name FROM `bew_uek` AS bu, `bew_modul` AS bm, `core_person` AS cp WHERE bu.modul_id = bm.modul_id AND bu.person_id = cp.person_id ORDER BY bu.uek_jg DESC, bu.uek_time_start DESC, bm.modul_kurz" );
		while ( $uek_data = mysql_fetch_array ( $uek_result ) )
		{
			$bogen_count 	= $db->fctCountData ( "bew_uek_fb_bogen" , "`uek_id` = " . $uek_data["uek_id"] );
			$bew_count 		= $db->fctCountData ( "bew_uek_res" , "`uek_id` = " . $uek_data["uek_id"] );
		
			// Zeilenfarbe anpassen
			if ( $row_highlight )
			{
				echo ( "<tr class=\"row_highlight\">\n" ); $row_highlight = false;
			}
			else
			{
				echo ( "<tr>\n" ); $row_highlight = true;
			}
			echo ( "<td><img src=\"" . $sys["icon_path"] . "bew_uek.gif\" alt=\"&Uuml;berbetrieblicher Kurs\" border=\"0\" /></td>\n" );
			echo ( "<td>" . $uek_data["uek_jg"] . "</td>\n" );
			echo ( "<td><a href=\"./?uek_id=" . $uek_data["uek_id"] . "\">" . $uek_data["modul_kurz"] . " " . substr ( $uek_data["modul_bezeichnung"] , 0 , 30 ) . "</a></td>\n" );
			echo ( "<td>" . date ( $conf->strDateFormatShort , $uek_data["uek_time_start"] ) . " - " . date ( $conf->strDateFormatShort , $uek_data["uek_time_end"] ) . "</td>\n" );
			echo ( "<td><a href=\"../../../core/person/profile/?person_id=" . $uek_data["person_id"] . "\">" . $uek_data["person_vorname"] . " " . $uek_data["person_name"] . "</a></td>\n" );
			
			if ( $sys["user"]["role_id"] == 5 )
			{
				echo ( "<td>\n" );
				echo ( "<a href=\"./form/?uek_id=" . $uek_data["uek_id"] . "&amp;\" title=\"&Uuml;K bearbeiten\"><img src=\"" . $sys["icon_path"] . "global_edit.gif\" alt=\"&Uuml;K bearbeiten\" border=\"0\" /></a>\n" );
				if ( $bogen_count == 0 AND $bew_count == 0 )
				{
					echo ( "<a href=\"./form/del/?uek_id=" . $uek_data["uek_id"] . "&amp;\" title=\"&Uuml;K l&ouml;schen\"><img src=\"" . $sys["icon_path"] . "global_del.gif\" alt=\"&Uuml;K l&ouml;schen\" border=\"0\" /></a>\n" );
				}
				echo ( "</td>\n" );
			}
			echo ( "</tr>\n" );
		}
		?>
		
		</table>
		
		<?PHP
		if ( $sys["user"]["role_id"] == 5 ) echo ( "<p><b>Achtung:</b> &Uuml;K-Eintr&auml;ge, welche mindestens ein Feedback oder Resultat haben, k&oumlnnen nicht gel&oumlscht werden.</p>\n" );
	}
}
############################################################################################
include ( $sys["root_path"] . "_global/footer.php" );
############################################################################################
?>