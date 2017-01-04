<?PHP
############################################################################################
$sys["root_path"] 	= "../../../";
$sys["script"] 		= true;
$sys["nav_id"]		= 28;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );

############################################################################################
if ( isset ( $_GET["uek_id"] ) ) 	$uek_id = mysql_escape_string ( htmlspecialchars ( $_GET["uek_id"] ) );
if ( isset ( $uek_id ) ) 			$uek_data = $db->fctSelectData ( "bew_uek" , "`uek_id` = '" . $uek_id . "'" );
############################################################################################

if ( !empty ( $uek_data["uek_id"] ) AND !empty ( $_GET["type"] ) )
{
	$modul_data  = $db->fctSelectData ( "bew_modul" , "`modul_id` = " . $uek_data["modul_id"] );
	$person_data = $db->fctSelectData ( "core_person" , "`person_id` = " . $uek_data["person_id"] );
	$ort_data	 = $db->fctSelectData ( "bew_uek_ort" , "`ort_id` = " . $uek_data["ort_id"] );
	
	$list_type	 = mysql_escape_string ( htmlspecialchars ( $_GET["type"] ) );
}else if(!empty($_GET["jahr"]) AND !empty ( $_GET["type"])){
    $jahr = mysql_escape_string ( htmlspecialchars ( $_GET["jahr"] ) );

    $list_type	 = mysql_escape_string ( htmlspecialchars ( $_GET["type"] ) );
}
else
{
	// Ung�ltige �K-Nummer f�r den Excel-Export.
	fctHandleLog ( $db , $sys , "Ung&uumlltige &UumlK-Nummer f&uumlr den Excel-Export." );
			
	$sys["script"] 		= 0;
	$sys["page_title"] 	= "Fehler beim Zugriff";
	$error = true;
			
	include ( $sys["root_path"] . "core/login/error.php" );
	include ( $sys["root_path"] . "_global/footer.php" );
		
	die ( );
}

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
if ($list_type == "nal" OR $list_type == "nl"){
    header("Content-type: application/xls");
} else{
    header("Content-type: application/csv");
}

if(isset($jahr)){
    if($jahr == date("Y") OR ($jahr+1 == date("Y") AND 8 > date("n"))){
        $where_clause = "(cp.person_s_semester = 1 OR cp.person_s_semester = 2 ) AND `person_deactivation` = 0";
    }else if(8 > date("n")){
        $sem = date("Y")-$jahr ;
        $where_clause= "(cp.person_s_semester = ".$sem." OR cp.person_s_semester = ".($sem+1) ." )";
    }else if(8 <= date("n")){
        $sem = date("Y")-$jahr ;
        $where_clause= "(cp.person_s_semester = ".($sem+1) ." OR cp.person_s_semester = ".($sem+2) ." )";
    };
}

if($list_type == "nal" && isset($jahr)){

    $output = '';
    $filename = "Notenmeldung_login_".$jahr . ".xls";
    $uek_data = $db->fctSendQuery("SELECT bew.* FROM `bew_uek` AS bew WHERE bew.uek_jg = ".$jahr);

    $output .= '<table>  
                     <tr>  
                        <td colspan="3" style="font-family: Calibri; font-size: 14pt;"><b>'.chr(220).'K-Noten - INF/INFP Lehrbeginn '. $uek_data["uek_jg"].'</b></td>
                     </tr>  
                     <tr>
                        <td>login Berufsbildung</td>
                     </tr>
                     <tr></tr>
                     <tr>
                        <th style="text-align: left;background-color: RGB(47,117,181); border: 1px white;">Name</th>
                        <th style="text-align: left;background-color: RGB(47,117,181); border: 1px white;">Vorname</th>';
    while($row = mysql_fetch_array($uek_data))
    {
        $modul_data = $db->fctSendQuery("SELECT bew.modul_kurz FROM `bew_modul` AS bew WHERE bew.modul_id = ".$row["modul_id"]."");

        while($rows = mysql_fetch_array($modul_data)) {
            $output .='<th style="text-align: left;background-color: RGB(47,117,181); border: 1px white;">'.$rows["modul_kurz"].'</th>';
        }
    }
    $output .= '<th style="text-align: left;background-color: RGB(47,117,181); border: 1px white;">Fachrichtung</th>
                <th style="text-align: left;background-color: RGB(47,117,181); border: 1px white;">BLJ-Anbieter</th>
                </tr>';


    $person = $db->fctSendQuery("SELECT cf.firma_name, cp.person_name, cp.person_vorname,cp.person_id,rt.richtung_name FROM `core_person` AS cp INNER JOIN `core_firma` AS cf ON cf.firma_id = cp.firma_id INNER JOIN `core_richtung` AS rt ON rt.richtung_id = cp.richtung_id WHERE  ".$where_clause." AND `beruf_id` = 1 ORDER BY cf.firma_name, cp.person_name, cp.person_vorname");
    print_r($where_clause);
    print_r($where_clause);
    print_r($where_clause);
    print_r($where_clause);
    print_r($where_clause);
    print_r($where_clause);
    $i = 0;
    while($person_result = mysql_fetch_array($person))
    {

        if($i % 2 == 0) {
            $bg = "RGB(189,215,238)";
        }
        else{
            $bg = "RGB(221,235,247)";
        }

        $output .= '<tr><td style="background-color: '.$bg.'); border: 1px white;">'.$person_result["person_name"].'</td>
                    <td style="background-color: '.$bg.'; border: 1px white;">'.$person_result["person_vorname"].'</td>';
        $uek_data = $db->fctSendQuery("SELECT bew.* FROM `bew_uek` AS bew WHERE bew.uek_jg = ".$jahr);
        while($row = mysql_fetch_array($uek_data)) {
            $modul_data = $db->fctSendQuery("SELECT res_note FROM `bew_uek_res`  WHERE uek_id = " . $row["uek_id"] . " AND person_id = " . $person_result["person_id"]);
            $modul_data_r = mysql_fetch_row($modul_data);
            $output .= '<td style="background-color: '.$bg.'; border: 1px white;">' . $modul_data_r[0] . '</td>';

        }
        $output .= '<td style="background-color: '.$bg.'; border: 1px white;">'.$person_result["richtung_name"].'</td>
                    <td style="background-color: '.$bg.'; border: 1px white;">'.$person_result["firma_name"].'</td></tr>';
        $i++;
    }

    $output .= '</table>';
    mb_convert_encoding($output,"UTF-8");
    header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
    echo $output;
}else if($list_type == "nl"){
    $output = '';
    $mpnr_clean = end(explode("-", $uek_data["uek_mpnr"]));
    $filename = "NL_login_".$modul_data["modul_kurz"]."_".$mpnr_clean."_".date("Ymd", $uek_data["uek_mp_time"]) . ".xls";
    $output .= '<table>  
                     <tr>  
                        <td colspan="3" style="font-family: Calibri; font-size: 14pt;"><b>'.chr(220).'K-Noten - INF/INFP Lehrbeginn '. $uek_data["uek_jg"].'</b></td>
                     </tr>  
                     <tr>
                        <td>'.$ort_data["ort_firma"].'</td>
                     </tr>
                     <tr></tr>
                     <tr>
                        <th style="text-align: left;background-color: RGB(47,117,181); border: 1px white;">Name</th>
                        <th style="text-align: left;background-color: RGB(47,117,181); border: 1px white;">Vorname</th>
                        <th style="text-align: left;background-color: RGB(47,117,181); border: 1px white;">'.$modul_data["modul_kurz"].'</th>
                        <th style="text-align: left;background-color: RGB(47,117,181); border: 1px white;">Fachrichtung</th>
                        <th style="text-align: left;background-color: RGB(47,117,181); border: 1px white;">BLJ-Anbieter</th>
                     </tr>
                        
           ';
    $person_result = $db->fctSendQuery("SELECT cf.firma_name, cp.person_name, cp.person_vorname,bur.res_note,rt.richtung_name FROM `core_person` AS cp INNER JOIN `core_firma` AS cf ON cp.firma_id = cf.firma_id INNER JOIN `bew_uek_res` AS bur ON bur.person_id = cp.person_id INNER JOIN `core_richtung` AS rt ON rt.richtung_id = cp.richtung_id WHERE ( cp.person_s_semester = 1 OR cp.person_s_semester = 2 )AND bur.uek_id = " . $uek_data["uek_id"]." AND `person_deactivation` = 0 AND `beruf_id` = 1 ORDER BY cf.firma_name, cp.person_name, cp.person_vorname");
    $i = 0;
    while($row = mysql_fetch_array($person_result))
    {
        if($i % 2 == 0){
            $bg = "RGB(189,215,238)";
        }else{
            $bg = "RGB(221,235,247)";
        }
        $output .= '<tr><td style="background-color: '.$bg.'; border: 1px white;">'.$row["person_name"].'</td>
                        <td style="background-color: '.$bg.'; border: 1px white;">'.$row["person_vorname"].'</td>
                        <td style="background-color: '.$bg.'; border: 1px white;">'.$row["res_note"].'</td>
                        <td style="background-color: '.$bg.'; border: 1px white;">'.$row["richtung_name"].'</td>
                        <td style="background-color: '.$bg.'; border: 1px white;">'.$row["firma_name"].'</td></tr>';
        $i++;
    }

    $output .= '</table>';
    mb_convert_encoding($output,"UTF-8");
    header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
    echo $output;


    //CSV Daten einfügen
    

}else{


// Wir brauchen eine CSV-Datei
    $mpnr_clean = end(explode("-", $uek_data["uek_mpnr"]));
    header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
// CSV-Inhalte
    if ($list_type == "tl") echo("Teilnehmerliste Modulpr�fung\n\n");
    else                        echo("Anwesenheitsliste Modulpr�fung\n\n");

    echo("Modul;" . $modul_data["modul_kurz"] . " " . $modul_data["modul_bezeichnung"] . "\n");
    echo("Pr�fungsdatum / Zeit;" . date($conf->strDateFormatFull, $uek_data["uek_mp_time"]) . "\n");
    echo("Pr�fungsleiter/in;" . $person_data["person_vorname"] . " " . $person_data["person_name"] . ", " . $person_data["person_g_tel"] . "\n\n");

    echo("MP-Nummer;" . $uek_data["uek_mpnr"] . "\n");
    echo("Pr�fungsort;" . $ort_data["ort_firma"] . ", " . $ort_data["ort_adresse"] . ", " . $ort_data["ort_plz"] . " " . $ort_data["ort_ort"] . "\n");
    echo("Pr�fungsraum;Raum login IT Basislehrjahr im Untergeschoss\n");
    echo("Erstellt;Bern, " . date($conf->strDateFormat) . " durch " . $sys["user"]["person_vorname"] . " " . $sys["user"]["person_name"] . "\n\n");

    echo("P. Kand.;Lehrbetrieb;Nachname;Vorname;GebDatum;Lehrjahr;");
    if ($list_type == "al") echo("Anwesend");
    echo("\n");

    $i = 1;
    $person_result = $db->fctSendQuery("SELECT cf.firma_name, cp.person_name, cp.person_vorname, cp.person_birthday FROM `core_person` AS cp, `core_firma` AS cf WHERE cf.firma_id = cp.firma_id AND ( cp.person_s_semester = 1 OR cp.person_s_semester = 2 ) AND `person_deactivation` = 0 AND `beruf_id` = 1 ORDER BY cf.firma_name, cp.person_name, cp.person_vorname");
    while ($person_data = mysql_fetch_array($person_result)) {
        echo($i . ";");
        echo($person_data["firma_name"] . ";");
        echo($person_data["person_name"] . ";");
        echo($person_data["person_vorname"] . ";");
        echo(date($conf->strDateFormat, $person_data["person_birthday"]) . ";");
        echo("1;");
        if ($list_type == "al") echo("Ja");
        echo("\n");

        $i++;
    }
}
############################################################################################
include ( $sys["root_path"] . "_global/footer.php" );
############################################################################################
?>