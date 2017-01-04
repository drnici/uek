<?PHP
############################################################################################
$sys["root_path"] 	= "../../../../";
$sys["script"] 		= false;
$sys["nav_id"]		= 28;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );
############################################################################################
$sys["page_title"] = "Feedback";
############################################################################################
if ( isset ( $_GET["bogen_id"] ) ) 	$bogen_id = mysql_escape_string ( htmlspecialchars ( $_GET["bogen_id"] ) );
if ( isset ( $bogen_id ) ) 			$bogen_data = $db->fctSelectData ( "bew_uek_fb_bogen" , "`bogen_id` = '" . $bogen_id . "'" );
############################################################################################

if ( $sys["user"]["role_id"] > 2 )
{
	echo ( "<h3>Allgemeine Informationen</h3>\n" );
	
	echo ( "<table>\n" );
	echo ( "<tr>\n" );
	echo ( "<th>Ausgef&uuml;llt am</th>\n" );
	echo ( "<td>" . date ( $conf->strDateFormatFull , $bogen_data["bogen_time"] ) . "</td>\n" );
	echo ( "</tr>\n" );
	
	$avg_data = mysql_fetch_array ( $db->fctSendQuery ( "SELECT AVG ( uf.antwort_id ) FROM `bew_uek_feedback` AS uf WHERE uf.bogen_id = " . $bogen_data["bogen_id"] ) );
	echo ( "<tr>\n" );
	echo ( "<th>Durchschnitt</th>\n" );
	echo ( "<td>" . bcdiv ( $avg_data[0] , 1 , 1 ) . "</td>\n" );
	echo ( "</tr>\n" );
	
	if ( is_numeric ( $bogen_data["person_id"] ) AND $sys["user"]["role_id"] == 5 )
	{
		$person_data = $db->fctSelectData ( "core_person" , "`person_id` = " . $bogen_data["person_id"] );
		
		echo ( "<tr>\n" );
		echo ( "<th>Lernende/r</th>\n" );
		echo ( "<td><a href=\"../../../../core/person/profile/?person_id=" . $person_data["person_id"] . "&\">" . $person_data["person_vorname"] . " " . $person_data["person_name"] . "</a></td>\n" );
		echo ( "</tr>\n" );
	}
	
	if ( !empty ( $bogen_data["bogen_comment"] ) AND $sys["user"]["role_id"] == 5 )
	{
		echo ( "<tr>\n" );
		echo ( "<th>Bemerkungen</th>\n" );
		echo ( "<td>" . nl2br ( $bogen_data["bogen_comment"] ) . "</td>\n" );
		echo ( "</tr>\n" );
	}
	
	echo ( "<tr>\n" );
	echo ( "<td>&nbsp;</td>\n" );
	echo ( "<td><input type=\"button\" class=\"btn\" value=\"zur&uuml;ck zur &Uuml;K-&Uuml;bersicht\" onclick=\"self.history.back(1);\" /></td>\n" );
	echo ( "</tr>\n" );
	echo ( "</table>\n" );
	
	$kat_result = $db->fctSendQuery ( "SELECT * FROM `bew_uek_fb_kat` ORDER BY `kat_id`" );
	while ( $kat_data = mysql_fetch_array ( $kat_result ) )
	{
		echo ( "<h3>" . $kat_data["kat_name"] . "</h3>\n" );
		
		$frage_result = $db->fctSendQuery ( "SELECT * FROM `bew_uek_fb_frage` WHERE `kat_id` = " . $kat_data["kat_id"] );
		while ( $frage_data = mysql_fetch_array ( $frage_result ) )
		{
			$antwort_data = mysql_fetch_array ( $db->fctSendQuery ( "SELECT ufa.antwort_id, ufa.antwort_color, ufa.antwort_text FROM `bew_uek_feedback` AS uf, `bew_uek_fb_antwort` AS ufa WHERE uf.bogen_id = " . $bogen_data["bogen_id"] . " AND uf.frage_id = " . $frage_data["frage_id"] . " AND uf.antwort_id = ufa.antwort_id" ) );
			
			
			
			echo ( "<p style=\"color: " . $antwort_data["antwort_color"] . "\";>" . $frage_data["frage_text"] . " <b>(" . $antwort_data["antwort_text"] . ")</b></p>" );
		}
		
	}
}
else
{
	// Nur Firmenverantwortliche, Ausbildungsleiter/innen und Administratoren dürfen ausgefüllte Feedback-Bögen ansehen.
	fctHandleLog ( $db , $sys , "Nur Firmenverantwortliche, Ausbildungsleiter/innen und Administratoren dürfen ausgefüllte Feedback-Bögen ansehen." );
		
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