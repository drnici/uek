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
		
		if ( isset ( $_POST["feedback_anonym"] ) )
		{
			$person_id		= md5($sys["user"]["person_id"]);
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
			header ( "Location: ./?s=mand&uek_id=" . $uek_id . "&feedback_anonym=" . $_POST["feedback_anonym"] . "&" . $header . "&" );
		}
		else
		{
		// alles okay, eintragen!
		    if($_POST["mand"] == "Feedback korrigieren" && is_numeric($_POST["bgid"])){

        // Bogen korrektur wieder auf 0 setzten
                $db->fctSendQuery("UPDATE `bew_uek_fb_bogen` SET `bogen_korrektur` = 0 WHERE `bogen_id` = ".$_POST["bgid"]);

                // Antworten eintragen
                $frage_result = $db->fctSendQuery("SELECT * FROM `bew_uek_fb_frage`");
                while ($frage_data = mysql_fetch_array($frage_result)) {
                    if (isset ($_POST [$frage_data ["frage_id"]])) {
                        $antwort = htmlspecialchars(mysql_escape_string($_POST [$frage_data ["frage_id"]]));

                        //Textbox(kann keine Bemerkung erfassen)
                        if ($frage_data["frage_id"] == 17) {
                            $db->fctSendQuery("UPDATE `bew_uek_fb` SET `antwort_fk_id` = 0,`bemerkung` = '".$antwort."' WHERE `bogen_fk_id` = ".$_POST["bgid"]." AND `frage_fk_id` = ".$frage_data ["frage_id"]);
                        } else if($frage_data["frage_id"] < 17) {
                            //Nicht zu jeder Frage kann eine Antwort verfasst werden
                            if(13 < $frage_data ["frage_id"] AND 17 > $frage_data ["frage_id"]){
                                $db->fctSendQuery("UPDATE `bew_uek_fb` SET `frage_fk_id` = " . $frage_data["frage_id"] . ", `antwort_fk_id` = " . $antwort . ",`bemerkung` = '' WHERE `bogen_fk_id` = " . $_POST["bgid"]." AND `frage_fk_id` = ".$frage_data ["frage_id"]);
                            }else{
                                $db->fctSendQuery("UPDATE `bew_uek_fb` SET `frage_fk_id` = " . $frage_data["frage_id"] . ", `antwort_fk_id` = " . $antwort . " ,`bemerkung` = '" . $_POST[$frage_data["frage_id"] * 88] . "' WHERE `bogen_fk_id` = " . $_POST["bgid"]." AND `frage_fk_id` = ".$frage_data ["frage_id"]);
                            }
                        }
                    }
                }
                header("Location: ../../?feedback=ok&");
            }else if($_POST["mand"] == "Feedback abspeichern") {

                // Bogen mit allgemeinen Infos erstellen
                $db->fctSendQuery("INSERT INTO `bew_uek_fb_bogen` (`uek_fk_id`,`person_fk_id`,`bogen_time`,`bogen_korrektur`,`bogen_korrektur_time`) VALUES (" . $uek_id . ",'" . $person_id . "',CURRENT_TIMESTAMP,0 ,'0000-00-00 00:00:00')");

                //Letzte eingefügt bogen_id ermitteln
                $bogen_id_full = $db->fctSendQuery("SELECT `bogen_id` As result FROM `bew_uek_fb_bogen` ORDER BY result DESC LIMIT 1");
                while ($bogen_data = mysql_fetch_array($bogen_id_full)) {
                    $bogen_id = $bogen_data["result"];
                }

                // Antworten eintragen
                $frage_result = $db->fctSendQuery("SELECT * FROM `bew_uek_fb_frage`");
                while ($frage_data = mysql_fetch_array($frage_result)) {
                    if (isset ($_POST [$frage_data ["frage_id"]])) {
                        $antwort = htmlspecialchars(mysql_escape_string($_POST [$frage_data ["frage_id"]]));

                        //Textbox(kann keine Bemerkung erfassen)
                        if ($frage_data ["frage_id"] == 17) {
                            $db->fctSendQuery("INSERT INTO `bew_uek_fb` (`bogen_fk_id`,`frage_fk_id`,`antwort_fk_id`,`bemerkung`) VALUES (" . $bogen_id . "," . $frage_data ["frage_id"] . ",0,'" . $antwort . " ')");
                        } else {
                            //Nicht zu jeder Frage kann eine Antwort verfasst werden
                            if(13 < $frage_data ["frage_id"] AND 17 > $frage_data ["frage_id"]) {
                                $db->fctSendQuery("INSERT INTO `bew_uek_fb` (`bogen_fk_id`,`frage_fk_id`,`antwort_fk_id`,`bemerkung`) VALUES (" . $bogen_id . "," . $frage_data ["frage_id"] . "," . $antwort . ",'')");
                            }else{
                                $db->fctSendQuery("INSERT INTO `bew_uek_fb` (`bogen_fk_id`,`frage_fk_id`,`antwort_fk_id`,`bemerkung`) VALUES (" . $bogen_id . "," . $frage_data ["frage_id"] . "," . $antwort . ",'" . $_POST[$frage_data["frage_id"] * 88] . "')");
                            }
                        }
                    }
                }

                header("Location: ../../?feedback=ok&");
            }
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