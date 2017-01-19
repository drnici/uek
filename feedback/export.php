<?PHP
############################################################################################
$sys["root_path"] 	= "../../../../";
$sys["script"] 		= true;
$sys["nav_id"]		= 28;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );
############################################################################################
if ( isset ( $_GET["uek_id"] ) ) 	$uek_id = mysql_escape_string ( htmlspecialchars ( $_GET["uek_id"] ) );
if ( isset ( $uek_id ) ) 			$uek_data = $db->fctSelectData ( "bew_uek" , "`uek_id` = '" . $uek_id . "'" );
############################################################################################

if ( !empty ( $uek_data["uek_id"] ) )
{
    //Aktuelle Daten auslesen
	$modul_data  = $db->fctSelectData ( "bew_modul" , "`modul_id` = " . $uek_data["modul_id"] );
	$person_data = $db->fctSelectData ( "core_person" , "`person_id` = " . $uek_data["person_id"] );
	$ort_data	 = $db->fctSelectData ( "bew_uek_ort" , "`ort_id` = " . $uek_data["ort_id"] );

    //Alle Fragen
    $fragen_result  = $db->fctSendQuery("SELECT * FROM `bew_uek_fb_frage`");

    //Alle User des ÜKs
    $user_result = $db->fctSendQuery("SELECT cp.person_vorname, cp.person_name, cp.person_id
                                            FROM  `core_person` AS cp
                                            INNER JOIN  `bew_uek_fb_bogen` AS bufb ON (md5(cp.person_id) = bufb.person_fk_id OR cp.person_id = bufb.person_fk_id) AND bufb.uek_fk_id =".$uek_data["uek_id"]." WHERE cp.person_id > 800");
}else if(isset($_GET["jahr"])){
    $uek_data_a = $db->fctSendQuery("SELECT * FROM `bew_uek` WHERE `uek_jg` = ".$_GET["jahr"]);

    //Alle Fragen
    $fragen_result  = $db->fctSendQuery("SELECT * FROM `bew_uek_fb_frage`");
}
else
{
	// Ungültige ÜK-Nummer für den Excel-Export.
	fctHandleLog ( $db , $sys , "Ung&uumlltige &UumlK-Nummer f&uumlr den Excel-Export." );
			
	$sys["script"] 		= 0;
	$sys["page_title"] 	= "Fehler beim Zugriff";
	$error = true;
			
	include ( $sys["root_path"] . "core/login/error.php" );
	include ( $sys["root_path"] . "_global/footer.php" );
		
	die ( );
}
############################################################################################
if ( $sys["user"]["role_id"] != 5 )
{
	// Nur Administratoren k�nnen die �K-Feedbacks exportieren.
	fctHandleLog ( $db , $sys , "Nur Administratoren k&oumlnnen die &UumlK-Feedbacks exportieren." );
			
	$sys["script"] 		= 0;
	$sys["page_title"] 	= "Fehler beim Zugriff";
	$error = true;
			
	include ( $sys["root_path"] . "core/login/error.php" );
	include ( $sys["root_path"] . "_global/footer.php" );
		
	die ( );
}
############################################################################################
// Wir brauchen eine XLS-Datei
header ( "Content-type: application/xls" );



############################################################################################
//TABELLENSTRUKTUR -> ANFANG
############################################################################################
$output = "";
$output .= "<table style='font-family: Calibri; font-size: 10pt;text-align: left'>
                <tr><th colspan='5' style='font-size:16pt'><b>ICT-Berufsbildung Bern - Umfrage ".chr(220)."K</b></th></tr>
                <tr></tr>
                <tr><td colspan='5' style='color: RGB(53,126,189);background-color: RGB(204,204,204);'>Multiple Choice Frage</td></tr>
                <tr><td colspan='5' style='color: RGB(212,63,58);background-color: RGB(204,204,204);'>Bewertungsmatrix Frage</td></tr>
                <tr><td colspan='5' style='color: RGB(57,132,57);background-color: RGB(204,204,204);'>Eingabefeld Frage</td></tr>
                <tr></tr>
                <tr>
                    <th style='width: auto; background-color: RGB(238,238,238);'>Vorname</th>
                    <th style='width: auto; background-color: RGB(238,238,238)'>Nachname</th>
                    <th style='width: auto; background-color: RGB(238,238,238)'>Kursleiter/-in</th>
                    <th style='width: auto; background-color: RGB(238,238,238)'>Kursanbieter</th>
                    <th style='width: auto; background-color: RGB(238,238,238);'>Modul</th>";

while($fragen_data = mysql_fetch_array ( $fragen_result )){

    //Je nach Fragenart wird die Frabe geswitcht
    if($fragen_data["frage_id"] < 14){
        $output .= "<th style='color: RGB(53,126,189); background-color: RGB(204,204,204);border-left: 1px black;'>".($fragen_data["frage_id"]+1) ." - " . $fragen_data["fragename"] ."</th>";
    }else if($fragen_data["frage_id"] > 13 && $fragen_data["frage_id"] < 17){
        $output .= "<th style='color: RGB(212,63,58); background-color: RGB(204,204,204);border-left: 1px black;'>".($fragen_data["frage_id"]+1) ." - " . $fragen_data["fragename"] ."</th>";
    }else if($fragen_data["frage_id"] == 17){
        $output .= "<th style='color: RGB(57,132,57); background-color: RGB(204,204,204);border-left: 1px black;'>".($fragen_data["frage_id"]+1) ." - " . $fragen_data["fragename"] ."</th>";
    }

    //Alle Antworten mit der frage_id auslesen
    $antwort_result = $db->fctSendQuery("SELECT * FROM `bew_uek_fb_antwort` WHERE `frage_fk_id` = ". $fragen_data["frage_id"]);
    while($antwort_data = mysql_fetch_array ( $antwort_result )){
        if($fragen_data["frage_id"] < 14)
        $output .= "<th style='background-color: RGB(238,238,238)'>".$antwort_data["antwortname"] ."</th>";
    }

    //Kommentare sind nur für Multiple Choice Fragen
    if($fragen_data["frage_id"]<14){
        $output .= "<th>Kommentar</th>";
    }
}

//Frage & Antwortenzeile schliessen
$output .= "</tr></table>";
############################################################################################
//TABELLENSTRUKTUR -> FERTIG
############################################################################################

############################################################################################
//TABELLENINHALT -> ANFANG ---- ganzes Jahr
############################################################################################
if(isset($_GET["jahr"])){
    $output .= "<table border='1'>";

    while($uek_result = mysql_fetch_array($uek_data_a)){
        $modul_data  = $db->fctSelectData ( "bew_modul" , "`modul_id` = " . $uek_result["modul_id"] );
        $person_data = $db->fctSelectData ( "core_person" , "`person_id` = " . $uek_result["person_id"] );
        $ort_data	 = $db->fctSelectData ( "bew_uek_ort" , "`ort_id` = " . $uek_result["ort_id"] );

        //Alle User des ÜKs
        $user_result = $db->fctSendQuery("SELECT cp.person_vorname, cp.person_name, cp.person_id
                                            FROM  `core_person` AS cp
                                            INNER JOIN  `bew_uek_fb_bogen` AS bufb ON (md5(cp.person_id) = bufb.person_fk_id OR cp.person_id = bufb.person_fk_id) AND bufb.uek_fk_id =".$uek_result["uek_id"]." WHERE cp.person_id > 800");

        while ($user_data = mysql_fetch_array($user_result)) {

            //Fixe linie mit Namen, Kursleiter, Kursanbieter, Modul
            $output .= "<tr><td>" . $user_data["person_vorname"] . "</td><td>" . $user_data["person_name"] . "</td><td>".$person_data["person_vorname"] . " " . $person_data["person_name"]."</td><td>" . $ort_data["ort_firma"] . " AG</td><td>" . $modul_data["modul_kurz"] . "-" . $modul_data["modul_bezeichnung"] . "</td>";

            //Anzahl Antworten pro Frage errechnen
            $count_fragen = 0;

            //Alle Feedbackresultate pro user auslesen
            $fb_result = '';
            $fb_result = $db->fctSendQuery("SELECT buf . * , buff.anzahl_fragen, buff.art, buff.frage_id, bufa.antwortname
                                            FROM  `bew_uek_fb` AS buf
                                            INNER JOIN  `bew_uek_fb_frage` AS buff ON buf.frage_fk_id = buff.frage_id
                                            INNER JOIN  `bew_uek_fb_bogen` AS bufb ON buf.bogen_fk_id = bufb.bogen_id AND bufb.uek_fk_id = " . $uek_result["uek_id"] . "
                                            LEFT JOIN `bew_uek_fb_antwort` As bufa ON buf.antwort_fk_id = bufa.antwort_id
                                            WHERE bufb.person_fk_id = " . $user_data["person_id"] . " OR bufb.person_fk_id = md5(" . $user_data["person_id"] . ") ORDER BY buf.feedback_id");

            while ($fb_data = mysql_fetch_array($fb_result)) {

                //Frage mit Bemerkung
                if ($fb_data["art"] == 0) {
                    $output .= "<td></td>";
                }

                //Zählt die Fragen pro Aufruf auf
                $count_fragen = $count_fragen + $fb_data["anzahl_fragen"];
                $var = $count_fragen - intval($fb_data["antwort_fk_id"]);

                //Fragentypus 1:3:4:5 Anzahl Antworten
                if ($fb_data["anzahl_fragen"] == 1) {
                    if ($fb_data["art"] == 2) {
                        $output .= "<td>" . $fb_data["bemerkung"] . "</td>";
                    } else {
                        $output .= "<td>" . $fb_data["antwortname"] . "</td>";
                    }
                } else if ($fb_data["anzahl_fragen"] == 3) {
                    if ($var == 1) {
                        $output .= "<td></td><td>1</td><td></td>";
                    } else if ($var == 2) {
                        $output .= "<td>1</td><td></td><td></td>";
                    } else if ($var == 0) {
                        $output .= "<td></td><td></td><td>1</td>";
                    }
                } else if ($fb_data["anzahl_fragen"] == 4) {
                    if ($var == 1) {
                        $output .= "<td></td><td></td><td>1</td><td></td>";
                    } else if ($var == 2) {
                        $output .= "<td></td><td>1</td><td></td><td></td>";
                    } else if ($var == 3) {
                        $output .= "<td>1</td><td></td><td></td><td></td>";
                    } else if ($var == 0) {
                        $output .= "<td></td><td></td><td></td><td>1</td>";
                    }
                } else if ($fb_data["anzahl_fragen"] == 5) {
                    if ($var == 1) {
                        $output .= "<td></td><td></td><td></td><td>1</td><td></td>";
                    } else if ($var == 2) {
                        $output .= "<td></td><td></td><td>1</td><td></td><td></td>";
                    } else if ($var == 3) {
                        $output .= "<td></td><td>1</td><td></td><td></td><td></td>";
                    } else if ($var == 4) {
                        $output .= "<td>1</td><td></td><td></td><td></td><td></td>";
                    } else if ($var == 0) {
                        $output .= "<td></td><td></td><td></td><td></td><td>1</td>";
                    }
                }

                //Frage mit Bemerkung
                if ($fb_data["art"] == 0) {
                    $output .= "<td>" . $fb_data["bemerkung"] . "</td>";
                }
            }
            $output .= "</tr>";
        }
    }
    $output .= "</table>";

    mb_convert_encoding($output, "UTF-8");
    header("Content-Disposition: attachment; filename=\"fb_export_" . $_GET["jahr"]. ".xls\"");
    echo $output;
############################################################################################
//TABELLENINHALT -> Fertig ---- ganzes Jahr
############################################################################################

############################################################################################
//TABELLENINHALT -> Anfang ---- ÜK
############################################################################################
}else {
    while ($user_data = mysql_fetch_array($user_result)) {

        //Fixe linie mit Namen, Kursleiter, Kursanbieter, Modul
        $output .= "<table border='1'><tr><td>" . $user_data["person_vorname"] . "</td><td>" . $user_data["person_name"] . "</td><td>".$person_data["person_vorname"] . " " . $person_data["person_name"]."</td><td>" . $ort_data["ort_firma"] . " AG</td><td>" . $modul_data["modul_kurz"] . "-" . $modul_data["modul_bezeichnung"] . "</td>";

        //Anzahl Antworten pro Frage errechnen
        $count_fragen = 0;

        //Alle Feedbackresultate pro user auslesen
        $fb_result = $db->fctSendQuery("SELECT buf . * , bufb.person_fk_id, buff.anzahl_fragen, buff.art, buff.frage_id, bufa.antwortname
                                        FROM  `bew_uek_fb` AS buf
                                        INNER JOIN  `bew_uek_fb_frage` AS buff ON buf.frage_fk_id = buff.frage_id
                                        INNER JOIN  `bew_uek_fb_bogen` AS bufb ON buf.bogen_fk_id = bufb.bogen_id
                                        LEFT JOIN `bew_uek_fb_antwort` As bufa ON buf.antwort_fk_id = bufa.antwort_id
                                        WHERE bufb.uek_fk_id = " . $uek_id . " AND bufb.person_fk_id = " . $user_data["person_id"] . " OR bufb.person_fk_id = md5(" . $user_data["person_id"] . ") ORDER BY buf.feedback_id");

        while ($fb_data = mysql_fetch_array($fb_result)) {

            //Frage mit Bemerkung
            if ($fb_data["art"] == 0) {
                $output .= "<td></td>";
            }

            //Zählt die Fragen pro Aufruf auf
            $count_fragen = $count_fragen + $fb_data["anzahl_fragen"];
            $var = $count_fragen - intval($fb_data["antwort_fk_id"]);

            //Fragentypus 1:3:4:5 Anzahl Antworten
            if ($fb_data["anzahl_fragen"] == 1) {
                if ($fb_data["art"] == 2) {
                    $output .= "<td>" . $fb_data["bemerkung"] . "</td>";
                } else {
                    $output .= "<td>" . $fb_data["antwortname"] . "</td>";
                }
            } else if ($fb_data["anzahl_fragen"] == 3) {
                if ($var == 1) {
                    $output .= "<td></td><td>1</td><td></td>";
                } else if ($var == 2) {
                    $output .= "<td>1</td><td></td><td></td>";
                } else if ($var == 0) {
                    $output .= "<td></td><td></td><td>1</td>";
                }
            } else if ($fb_data["anzahl_fragen"] == 4) {
                if ($var == 1) {
                    $output .= "<td></td><td></td><td>1</td><td></td>";
                } else if ($var == 2) {
                    $output .= "<td></td><td>1</td><td></td><td></td>";
                } else if ($var == 3) {
                    $output .= "<td>1</td><td></td><td></td><td></td>";
                } else if ($var == 0) {
                    $output .= "<td></td><td></td><td></td><td>1</td>";
                }
            } else if ($fb_data["anzahl_fragen"] == 5) {
                if ($var == 1) {
                    $output .= "<td></td><td></td><td></td><td>1</td><td></td>";
                } else if ($var == 2) {
                    $output .= "<td></td><td></td><td>1</td><td></td><td></td>";
                } else if ($var == 3) {
                    $output .= "<td></td><td>1</td><td></td><td></td><td></td>";
                } else if ($var == 4) {
                    $output .= "<td>1</td><td></td><td></td><td></td><td></td>";
                } else if ($var == 0) {
                    $output .= "<td></td><td></td><td></td><td></td><td>1</td>";
                }
            }

            //Frage mit Bemerkung
            if ($fb_data["art"] == 0) {
                $output .= "<td>" . $fb_data["bemerkung"] . "</td>";
            }

        }
        $output .= "</tr>";
    }
    $output .= "</table>";
############################################################################################
//TABELLENINHALT -> FERTIG --- ÜK
############################################################################################
    mb_convert_encoding($output, "UTF-8");
    header("Content-Disposition: attachment; filename=\"fb_export_" . $modul_data["modul_kurz"] . "_" . $uek_data["uek_jg"] . ".xls\"");
    echo $output;
}
############################################################################################
include ( $sys["root_path"] . "_global/footer.php" );
############################################################################################
?>