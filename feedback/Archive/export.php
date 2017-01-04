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
	$modul_data  = $db->fctSelectData ( "bew_modul" , "`modul_id` = " . $uek_data["modul_id"] );
	$person_data = $db->fctSelectData ( "core_person" , "`person_id` = " . $uek_data["person_id"] );
	$ort_data	 = $db->fctSelectData ( "bew_uek_ort" , "`ort_id` = " . $uek_data["ort_id"] );
}
else
{
	// Ungültige ÜK-Nummer für den Excel-Export.
	fctHandleLog ( $db , $sys , "Ungültige ÜK-Nummer für den Excel-Export." );
			
	$sys["script"] 		= 0;
	$sys["page_title"] 	= "Fehler beim Zugriff";
	$error = true;
			
	include ( $sys["root_path"] . "core/login/error.php" );
	include ( $sys["root_path"] . "_global/footer.php" );
		
	die ( );
}

if ( $sys["user"]["role_id"] != 5 )
{
	// Nur Administratoren können die ÜK-Feedbacks exportieren.
	fctHandleLog ( $db , $sys , "Nur Administratoren können die ÜK-Feedbacks exportieren." );
			
	$sys["script"] 		= 0;
	$sys["page_title"] 	= "Fehler beim Zugriff";
	$error = true;
			
	include ( $sys["root_path"] . "core/login/error.php" );
	include ( $sys["root_path"] . "_global/footer.php" );
		
	die ( );
}

// Wir brauchen eine CSV-Datei
header ( "Content-type: application/csv" );
header ( "Content-Disposition: attachment; filename=\"fb_export_" . $modul_data["modul_kurz"] . "_" . $uek_data["uek_jg"] . ".csv\"" );

// CSV-Inhalte
echo ( "Übersicht der Beurteilungen aller Teilnehmer des überbetrieblichen Kurses;\n" );
echo ( ";\n" );
echo ( "ÜK-Bezeichnung;;;;" . $modul_data["modul_kurz"] . ";;;Kursanbieter;;;;;;;" . $ort_data["ort_firma"] . ";\n" );
echo ( "Kursleitung;;;;" . $person_data["person_vorname"] . " " . $person_data["person_name"] . ";;;Kursort;;;;;;;" . $ort_data["ort_adresse"] . ", " . $ort_data["ort_plz"] . " " . $ort_data["ort_ort"] . ";\n" );
echo ( ";\n" );
echo ( ";\n" );
echo ( ";\n" );
echo ( ";i;m;s;n;i;m;s;n;i;m;s;n;i;m;s;n;i;m;s;n;i;m;s;n;i;m;s;n;i;m;s;n;i;m;s;n;i;m;s;n;i;m;s;n;i;m;s;n;i;m;s;n;\n" );

$fb_result = $db->fctSendQuery ( "SELECT bufb.bogen_id, bufb.person_id FROM `bew_uek_fb_bogen` AS bufb WHERE bufb.uek_id = " . $uek_data["uek_id"] );
while ( $fb_data = mysql_fetch_array ( $fb_result ) )
{
	if ( $fb_data["person_id"] != NULL )
	{
		$person_data = mysql_fetch_array ( $db->fctSendQuery ( "SELECT cp.person_vorname, cp.person_name FROM `core_person` AS cp WHERE cp.person_id = " . $fb_data["person_id"] ) );
		
		echo ( $person_data["person_vorname"] . " " . $person_data["person_name"] . ";" );
	}
	else
	{
		echo ( "Feedback anonym;" );
	}
	
	$frage_result = $db->fctSendQuery ( "SELECT buff.frage_id FROM `bew_uek_fb_frage` AS buff" );
	while ( $frage_data = mysql_fetch_array ( $frage_result ) )
	{
		$antwort_data = $db->fctSelectData ( "bew_uek_feedback" , "`bogen_id` = " . $fb_data["bogen_id"] . " AND `frage_id` = " . $frage_data["frage_id"], );
		$antwort_data = $db->fctSelectData ( "bew_uek_fb-bogen" , "`bogen_comment` = " . $fb_data["bogen_comment], );
		
		if ( $antwort_data["antwort_id"] == 1 ) 		echo ( "1;;;;" );
		elseif ( $antwort_data["antwort_id"] == 2 ) 	echo ( ";1;;;" );
		elseif ( $antwort_data["antwort_id"] == 3 ) 	echo ( ";;1;;" );
		elseif ( $antwort_data["antwort_id"] == 4 ) 	echo ( ";;;1;" );

	}
	echo ( "\n" );
}

############################################################################################
include ( $sys["root_path"] . "_global/footer.php" );
############################################################################################
?>