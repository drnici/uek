<?PHP
############################################################################################
$sys["root_path"] 	= "../../../../../";
$sys["script"] 		= false;
$sys["nav_id"]		= 28;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );
############################################################################################

if ( $sys["user"]["role_id"] == 1 AND ( $sys["user"]["person_s_semester"] == 1 OR $sys["user"]["person_s_semester"] == 2 ) )
{
	$time_fb_end = time ( ) - 60 * 60 * 120;
	$fb_count = $db->fctCountData ( "bew_uek" , "`uek_mp_time` < " . time ( ) . " AND `uek_mp_time` > " . $time_fb_end . " AND `uek_id` = '" . $_POST["uek_id"] . "'" );

	if ( $fb_count > 0 )
	{
		$uek_id 			= htmlspecialchars ( mysql_escape_string ( $_POST["uek_id"] ) );
		$feedback_comment 	= htmlspecialchars ( mysql_escape_string ( $_POST["feedback_comment"] ) );
		
		if ( isset ( $_POST["feedback_anonym"] ) )
		{
			$person_id		= "NULL";
		}
		else
		{
			$person_id		= $sys["user"]["person_id"];
		}
		
		// pr�fen, ob wirklich alle Fragen ausgef�llt wurden
		$alle_fragen	= true;
		$header			= "";
		$frage_result 	= $db->fctSendQuery ( "SELECT * FROM `bew_uek_fb_frage`" );
		while ( $frage_data = mysql_fetch_array ( $frage_result ) )
		{
			if ( !isset ( $_POST [ $frage_data [ "frage_id" ] ] ) )
			{
				$alle_fragen = false;
				$header 	.= $frage_data [ "frage_id" ] . "=&";
			}
			else
			{
				$header 	.= $frage_data [ "frage_id" ] . "=" . $_POST [ $frage_data [ "frage_id" ] ] . "&";
			}
		}
		
		if ( !$alle_fragen )
		{
			// nicht alles okay
			header ( "Location: ./?s=mand&uek_id=" . $uek_id . "&feedback_anonym=" . $_POST["feedback_anonym"] . "&" . $header . "feedback_comment=" . urlencode ( $_POST["feedback_comment"] ) . "&" );
		}
		else
		{
			// alles okay, eintragen!
		
			// Bogen mit allgemeinen Infos erstellen
			$db->fctSendQuery ( "INSERT INTO `bew_uek_fb_bogen` (`uek_fk_id`,`person_fk_id`) VALUES (" . $uek_id . "," . $person_id . ")" );
			
			$bogen_id = $db->fctSendQuery("SELECT `bogen_id` As result FROM `bew_uek_fb_bogen` ORDER BY result DESC LIMIT 1");
			
			// Antworten eintragen
			$frage_result = $db->fctSendQuery ( "SELECT * FROM `bew_uek_fb_frage`" );
			while ( $frage_data = mysql_fetch_array ( $frage_result ) )
			{
				if ( isset ( $_POST [ $frage_data [ "frage_id" ] ] ) )
				{
                    $antwort = htmlspecialchars ( mysql_escape_string ( $_POST [ $frage_data [ "frage_id" ] ] ) );
				    if($frage_data [ "frage_id" ] == 17 ){
                        $db->fctSendQuery ( "INSERT INTO `bew_uek_fb` (`bogen_fk_id`,`frage_fk_id`,`antwort_fk_id`,`bemerkung`) VALUES (" . $bogen_id . "," . $frage_data [ "frage_id" ] . ",0,'" . $antwort . " ')" );
                    }else {
                        $db->fctSendQuery("INSERT INTO `bew_uek_fb` (`bogen_fk_id`,`frage_fk_id`,`antwort_fk_id`,`bemerkung`) VALUES (" . $bogen_id . "," . $frage_data ["frage_id"] . "," . $antwort . ",'Hans')");
                    }
				}
			}
			
			header ( "Location: ../../?feedback=ok&" );
		}
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