<?PHP
############################################################################################
$sys["root_path"] 	= "../../../../";
$sys["script"] 		= true;
$sys["nav_id"]		= 77;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );
############################################################################################

$bogen_id			= mysql_escape_string ( htmlspecialchars ( $_GET["bogen_id"] ) );
$status				= mysql_escape_string ( htmlspecialchars ( $_GET["state"] ) );

// Infos zur Mutation holen
$bogen_data	  	= $db->fctSelectData ( "bew_uek_fb_bogen" , "`bogen_id` = '" . $bogen_id . "'" );

// Infos zum Antragsteller holen
$person_data 		= mysql_fetch_array ( $db->fctSendQuery ( "SELECT cp.person_id, cp.person_vorname, cp.person_name FROM `core_person` AS cp, `bew_uek_fb_bogen` as bufb WHERE bufb.bogen_id = " . $bogen_id . " AND  bufb.person_fk_id = cp.person_id OR bufb.person_fk_id = MD5(cp.person_id)" ) );

$access = fctCheckAccess ( $sys["user"] , $person_data["person_id"] , $db );

if ( $access )
{
	if ( $status == "ok" )
	{
		// Antrag wurde angenommen
        $db->fctSendQuery("UPDATE `bew_uek_fb_bogen` SET `bogen_korrektur` = 2 WHERE `bogen_id` = ".$bogen_id);

	
		// eMail an Antragsteller senden
		$message_text  = "Liebe/r " . $person_data["person_vorname"] . " " . $person_data["person_name"] . "<br /><br />";
		$message_text .= "Dein Antrag vom " . $bogen_data["bogen_korrektur_time"] . " bez&uumlglich einer Korrektur des Feedbacks wurde angenommen.<br /><br />";
		$message_text .= "Liebe Gr&uumlsse<br />" . $sys["user"]["person_vorname"] . " " . $sys["user"]["person_name"];
		
		fctSendMail ( $db , $person_data["person_id"] , $sys["user"]["person_id"] , "L&oumlschantrag angenommen" , $message_text , "msg" );
		
	}
	elseif ( $status == "nok" )
	{
	
		// Antrag wurde abgelehnt
        $db->fctSendQuery("UPDATE `bew_uek_fb_bogen` SET `bogen_korrektur` = 0 WHERE `bogen_id` = ".$bogen_id);
		
		// E-Mail an Antragsteller senden
		$message_text  = "Liebe/r " . $person_data["person_vorname"] . " " . $person_data["person_name"] . "<br /><br />";
		$message_text .= "Dein Antrag vom " . $bogen_data["bogen_korrektur_time"] . " bez&uumlglich einer Korrektur des Feedbacks wurde abgelehnt.<br /><br />";
		$message_text .= "Bei Fragen dazu stehe ich Dir gerne pers&oumlnlich zur Verf&uumlgung.<br /><br />";
		$message_text .= "Liebe Gr&uumlsse<br />" . $sys["user"]["person_vorname"] . " " . $sys["user"]["person_name"];
		
		fctSendMail ( $db , $person_data["person_id"] , $sys["user"]["person_id"] , "L&oumlschantrag abgelehnt" , $message_text , "msg" );
	
	}
}

header ( "Location: ./?s=ok" );

############################################################################################
include ( $sys["root_path"] . "_global/footer.php" );
############################################################################################
?>