<?PHP
############################################################################################
$sys["root_path"] 	= "../../../../../";
$sys["script"] 		= false;
$sys["nav_id"]		= 28;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );
############################################################################################
if ( isset ( $_GET["uek_id"] ) ) 	$uek_id 	= mysql_escape_string ( htmlspecialchars ( $_GET["uek_id"] ) );
if ( isset ( $uek_id ) ) 			$uek_data 	= $db->fctSelectData ( "bew_uek" , "`uek_id` = '" . $uek_id . "'" );
############################################################################################
$sys["page_title"] 					= "Feedback";
############################################################################################

if ( $sys["user"]["role_id"] == 1 AND ( $sys["user"]["person_s_semester"] == 1 OR $sys["user"]["person_s_semester"] == 2 ) )
{
	$time_fb_end = time ( ) - 60 * 60 * 120;
	$fb_count = $db->fctCountData ( "bew_uek" , "`uek_mp_time` < " . time ( ) . " AND `uek_mp_time` > " . $time_fb_end . " AND `uek_id` = '" . $uek_id . "'" );

	if ( $fb_count > 0 )
	{
		if ( !empty ( $uek_data["uek_id"] ) )
		{
			$modul_data  = $db->fctSelectData ( "bew_modul" , "`modul_id` = '" . $uek_data["modul_id"] . "'" );
			$person_data = $db->fctSelectData ( "core_person" , "`person_id` = '" . $uek_data["person_id"] . "'" );
			
			
			if ( isset ( $_GET["s"] ) )
			{
				if ( $_GET["s"] == "mand" )
				{
					$feedback_anonym = "";
					if ( !empty ( $_GET["feedback_anonym"] ) ) $feedback_anonym = " checked=\"checked\"";
					
					$feedback_comment = htmlspecialchars ( urldecode ( $_GET["feedback_comment"] ) );
					
					$frage_value = array ( );
					$frage_result = $db->fctSendQuery ( "SELECT * FROM `bew_uek_fb_frage`" );
					while ( $frage_data = mysql_fetch_array ( $frage_result ) )
					{
						$frage_value [ $frage_data [ "frage_id" ] ] = htmlspecialchars ( mysql_escape_string ( $_GET [ $frage_data [ "frage_id" ] ] ) );
					}
				}
			}
			else
			{
				$feedback_anonym = " checked=\"checked\"";
				
				$feedback_comment = "";
					
				$frage_value = array ( );
				$frage_result = $db->fctSendQuery ( "SELECT * FROM `bew_uek_fb_frage`" );
				while ( $frage_data = mysql_fetch_array ( $frage_result ) )
				{
					$frage_value [ $frage_data [ "frage_id" ] ] = 0;
				}
			}
			
			if ( isset ( $_GET["s"] ) )
			{
				if ( $_GET["s"] == "mand" )
				{
					echo ( "<p class=\"warning\"><b>Fehler</b>: Ihr Feedback konnte nicht abgespeichert werden, weil Sie nicht alle Fragen beantwortet haben.</p>\n" );
				}
			}
	?>
			<h3>Allgemeine Informationen</h3>
			
			<form action="save.php" name="feedback" method="post">
			<input type="hidden" name="uek_id" value="<?PHP echo ( $uek_data["uek_id"] ); ?>" />
			
			<table>
			<tr>
			<th>&Uuml;K-Nummer / Bezeichnung</th>
			<td><?PHP echo ( $modul_data["modul_kurz"] . " " . $modul_data["modul_bezeichnung"] ); ?></td>
			</tr>
			<tr>
			<th>Anonymit&aumlt</th>
			<td><input type="checkbox" name="feedback_anonym" value="1"<?PHP echo ( $feedback_anonym ); ?> class="width_auto" /> Ich m&oumlchte den &UumlK anonym bewerten.</td>
			</tr>
			</table>

			<link rel="stylesheet" href="style.css" type="text/css">
			<?PHP




			//Alle Fragen werden aus der Datenbank herausgelsen
			$fb_fragen_result = $db->fctSendQuery("SELECT * FROM `bew_uek_fb_frage` ORDER BY `frage_id`");
			while ( $fragen_data = mysql_fetch_array ( $fb_fragen_result ) ) {

				echo('<div class="fragen_group">');

//Radiobutton
					if($fragen_data["art"] == 0){
						//Fieldliste für Radiobuttons
						echo('<fieldset class="radio_button_group">');

						//Frage wird auf Feedbackbogen präsentiert
						echo('<p><b>' . $fragen_data["fragename"] . '</b></p>');

						//Die jweiligen Antworten aus der Datenbank auslesen
						$fb_antwort_result = $db->fctSendQuery("SELECT * FROM `bew_uek_fb_antwort` WHERE `frage_fk_id` = ".$fragen_data["frage_id"]." ORDER BY `antwort_id`");

						//Tabelle um eine schöne struktur zu behalten
						echo("<table style='width: 100%'>");

						//Radiobuttons werden bezüglich der Frage & Antwort erstellt
						while ( $antwort_data = mysql_fetch_array ( $fb_antwort_result ) ) {

							echo("<tr>");
							echo('<td style="vertical-align: middle"><input type="radio" value"1" id="antwort_'.$antwort_data["antwort_id"].'" name="frage_'.$fragen_data["frage_id"].'"></td>');
							echo('<td><label for="antwort_'.$antwort_data["antwort_id"].'">'.$antwort_data["antwortname"].'</label></td>');
							echo('</tr>');

						}

						//Fieldliste für Radiobuttons schliessen
						echo('</table></fieldset>');

						//Höhe der TExtarea durch Anzahl Fragen errechnen
						$var = $fragen_data["anzahl_fragen"]*2;

						//Kommentarfeld für die Bemerkung pro Frage
						echo('<div class="bemerkung_textfield"><b>Bemerkung</b></br><textarea cols="40" rows="'.$var.'"></textarea></div>');


					}
//Sternebarometer
					else if($fragen_data["art"] == 1){
						echo('<p><b>' . $fragen_data["fragename"] . '</b></p><fieldset class="rating">');
						for ($i = 10; 1 <= $i;$i--){
							echo('<input type="radio" id="'.$fragen_data["frage_id"].'star'.$i.'" name="rating'.$fragen_data["frage_id"].'" value="'.$i.'"><label class="full" for="'.$fragen_data["frage_id"].'star'.$i.'"></label>');
						}
						echo('</fieldset>');
					}
//Textarea
					else if($fragen_data["art"] == 2){

						//Fieldliste für die Textarea
						echo('<br/><fieldset class="textarea_fieldlist">');

						echo('<b>'.$fragen_data["fragename"].'</b></br></br><textarea cols="40" rows="4"></textarea>');

					}

				echo('</div>');

			}


/*
			$kat_result = $db->fctSendQuery ( "SELECT * FROM `bew_uek_fb_kat` ORDER BY `kat_id`" );
			while ( $kat_data = mysql_fetch_array ( $kat_result ) )
			{
				echo ( "<h3>" . $kat_data["kat_name"] . "</h3>\n" );
				
				$frage_result = $db->fctSendQuery ( "SELECT * FROM `bew_uek_fb_frage` WHERE `kat_id` = " . $kat_data["kat_id"] );
				while ( $frage_data = mysql_fetch_array ( $frage_result ) )
				{
					echo ( "<p><b>" . $frage_data["frage_text"] . "</b></p>" );
					
					echo ( "<p>\n" );
					
					$antwort_result = $db->fctSendQuery ( "SELECT * FROM `bew_uek_fb_antwort` ORDER BY `antwort_id`" );
					while ( $antwort_data = mysql_fetch_array ( $antwort_result ) )
					{
						$c = "";
						if ( $frage_value [ $frage_data [ "frage_id" ] ] == $antwort_data["antwort_id"] ) $c = " checked=\"checked\"";
						echo ( "<input type=\"radio\" name=\"" . $frage_data["frage_id"] . "\" value=\"" . $antwort_data["antwort_id"] . "\" class=\"width_auto\"" . $c . " /> " . $antwort_data["antwort_text"] . "\n" );
					}
		
					echo ( "</p>\n" );
				}
				
			}*/
			?>
			
			<p> 
				<input type="submit" class="btn" value="Feedback abspeichern" />
				<input type="button" class="btn" value="zur&uuml;ck zur &Uuml;bersicht" onclick="self.history.back(1);" />
			</p> 
			
			</form>
		
			<?PHP
		}
		else
		{
			// Formularaufruf, obwohl momentan keine Feedbacks abgegeben werden k�nnen.
			fctHandleLog ( $db , $sys , "Formularaufruf, obwohl momentan keine Feedbacks abgegeben werden k�nnen." );
				
			$sys["script"] 		= 0;
			$sys["page_title"] 	= "Fehler beim Zugriff";
			$error = true;
				
			include ( $sys["root_path"] . "core/login/error.php" );
			include ( $sys["root_path"] . "_global/footer.php" );
			
			die ( );
		}
	}
	else
	{
		// Keine g�ltige �K-ID �bergeben.
		fctHandleLog ( $db , $sys , "Keine g�ltige �K-ID �bergeben." );
			
		$sys["script"] 		= 0;
		$sys["page_title"] 	= "Fehler beim Zugriff";
		$error = true;
			
		include ( $sys["root_path"] . "core/login/error.php" );
		include ( $sys["root_path"] . "_global/footer.php" );
		
		die ( );
	}
}
else
{
	// Benutzer ist nicht im ersten LJ und / oder kein Lernender - darf keine Feedbacks geben.
	fctHandleLog ( $db , $sys , "Benutzer ist nicht im ersten LJ und / oder kein Lernender - darf keine Feedbacks geben." );
		
	$sys["script"] 		= 0;
	$sys["page_title"] 	= "Fehler beim Zugriff";
	$error = true;
		
	include ( $sys["root_path"] . "core/login/error.php" );
	include ( $sys["root_path"] . "_global/footer.php" );
	
	die ( );
}

############################################################################################
include ( $sys["root_path"] . "_global/footer.php" );
############################################################################################
?>