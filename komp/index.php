<?PHP
############################################################################################
$sys["root_path"] 	= "../../../../";
$sys["script"] 		= true;
$sys["nav_id"]		= 28;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );
############################################################################################
if ( isset ( $_GET["res_id"] ) ) 	$res_id 	= mysql_escape_string ( htmlspecialchars ( $_GET["res_id"] ) );
if ( isset ( $res_id ) ) 			$res_data 	= $db->fctSelectData  ( "bew_uek_res" , "`res_id` = '" . $res_id . "'" );
############################################################################################

if ( !empty ( $res_data["res_id"] ) )
{
	$uek_data	 		= $db->fctSelectData ( "bew_uek" , 		"`uek_id` = " . $res_data["uek_id"] );
	$ort_data	 		= $db->fctSelectData ( "bew_uek_ort" ,  "`ort_id` = " . $uek_data["ort_id"] );
	$modul_data			= mysql_fetch_array ( $db->fctSendQuery ( "SELECT bm.modul_kurz, bm.modul_bezeichnung FROM `bew_modul` AS bm WHERE bm.modul_id = " . $uek_data["modul_id"] ) );
	$lde_data 	 		= mysql_fetch_array ( $db->fctSendQuery ( "SELECT cp.firma_id, cp.person_vorname, cp.person_name FROM `core_person` AS cp WHERE cp.person_id = " . $res_data["person_id"] ) );
	$lde_firma_data		= mysql_fetch_array ( $db->fctSendQuery ( "SELECT cf.firma_name FROM `core_firma` AS cf WHERE cf.firma_id = " . $lde_data["firma_id"] ) );

	$access				= fctCheckAccess ( $sys["user"] , $res_data["person_id"] , $db );
	
	if ( $access )
	{
		header("Content-type: application/rtf");
		header("Content-Disposition: attachment; filename=\"" . $modul_data["modul_kurz"] . "_" . strtolower ( $lde_data["person_name"] ) . "_" . strtolower ( $lde_data["person_vorname"] ) . ".rtf\"");
			
		$filename = "vorlage/ka_" . $uek_data["person_id"] . ".rtf";
		$handle = fopen($filename, "r");
		$content = fread($handle, filesize($filename));
		fclose($handle);
		
		//$modulnote = fctGetModulNote ( $res_data["res_note_methode"] , $res_data["res_note_fach"] , $uek_data["uek_faktor_methode"] , $uek_data["uek_faktor_fach"] );
		
		$content = str_replace ( "++MODUL_KURZ++" , $modul_data["modul_kurz"] , $content );
		$content = str_replace ( "++MODUL_BEZEICHNUNG++" , $modul_data["modul_bezeichnung"] , $content );
		
		$content = str_replace ( "++PERSON_VORNAME++" , $lde_data["person_vorname"] , $content );
		$content = str_replace ( "++PERSON_NAME++" , $lde_data["person_name"] , $content );
		$content = str_replace ( "++FIRMA_NAME++" , $lde_firma_data["firma_name"] , $content );
		$content = str_replace ( "++UEK_MPDATUM++" , date ( $conf->strDateFormatFull , $uek_data["uek_mp_time"] ) , $content );
		$content = str_replace ( "++UEK_MPNR++" , $uek_data["uek_mpnr"] , $content );
		$content = str_replace ( "++UEK_ZEITRAUM++" , date ( $conf->strDateFormat , $uek_data["uek_time_start"] ) . " - " . date ( $conf->strDateFormat , $uek_data["uek_time_end"] ) , $content );
		
		//$content = str_replace ( "++NOTE_M++" , bcdiv ( $res_data["res_note_methode"] , 1 , 1 ) , $content );
		//$content = str_replace ( "++FAKTOR_M++" , $uek_data["uek_faktor_methode"] * 100 . "%" , $content );
		//$content = str_replace ( "++NOTE_F++" , bcdiv ( $res_data["res_note_fach"] , 1 , 1 ) , $content );
		//$content = str_replace ( "++FAKTOR_F++" , $uek_data["uek_faktor_fach"] * 100 . "%" , $content );
		$content = str_replace ( "++NOTE_TOTAL++" , $res_data["res_note"] , $content );
	
		$content = str_replace ( "++ORT++" , $ort_data["ort_ort"] , $content );
		//$content = str_replace ( "++KURSANBIETER++" , $ort_data["ort_firma"] , $content );
		$content = str_replace ( "++DATUM++" , date ( $conf->strDateFormat ) , $content );
		//$content = str_replace ( "++KURSLEITUNG++" , $leiter_data["person_vorname"] . " " . $leiter_data["person_name"] , $content );
		
		echo ( $content );
	}
	else
	{
		// Benutzer will ein Kompetenzausweis ansehen, den er nicht sehen darf.
		fctHandleLog ( $db , $sys , "Benutzer will ein Kompetenzausweis ansehen, den er nicht sehen darf." );
		
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
	// Ungültige Bewertungs-ID.
	fctHandleLog ( $db , $sys , "Ungültige Bewertungs-ID." );
	
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