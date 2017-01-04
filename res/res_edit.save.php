<?PHP
############################################################################################
$sys["root_path"] 	= "../../../../";
$sys["script"] 		= true;
$sys["nav_id"]		= 28;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );
############################################################################################

if ( $sys["user"]["role_id"] == 5 )
{
	$res_id					= mysql_escape_string ( htmlspecialchars ( $_POST["res_id"] ) );
	$person_id				= mysql_escape_string ( htmlspecialchars ( $_POST["person_id"] ) );
	$res_note_methode		= mysql_escape_string ( htmlspecialchars ( $_POST["res_note_methode"] ) );
	$res_note_fach			= mysql_escape_string ( htmlspecialchars ( $_POST["res_note_fach"] ) );
	
	if ( $res_id == "" OR $person_id == "" OR $res_note_methode == "" OR $res_note_fach == "" )
	{
		// Es wurden nicht alle Felder ausgefüllt
		header ( "Location: ./res_edit.sta.php?s=mand&res_id=" . $res_id . "&person_id=" . $person_id . "&res_note_methode=" . $res_note_methode . "&res_note_fach=" . $res_note_fach . "&" );
	}
	else
	{
		// eintragen
		if ( $db->fctSendQuery ( "UPDATE `bew_uek_res` SET `person_id` = '" . $person_id . "', `res_note_methode` = '" . $res_note_methode . "', `res_note_fach` = '" . $res_note_fach . "' WHERE `res_id` = '" . $res_id . "'" ) )
		{	
			// alles OK
			header ( "Location: res_edit.sta.php?s=insert&" );
		}
		else
		{
			// Update-Query wurde nicht korrekt in der Datenbank ausgeführt
			fctHandleLog ( $db , $sys , "Update-Query wurde nicht korrekt in der Datenbank ausgeführt" );
			
			$sys["script"] 		= 0;
			$sys["page_title"] 	= "Fehler beim Zugriff";
			$error = true;
			
			include ( $sys["root_path"] . "core/login/error.php" );
			include ( $sys["root_path"] . "_global/footer.php" );
		
			die ( );
		}
	}
}
else
{
	// Nur Administratoren dürfen ÜK-Resultate verwalten.
	fctHandleLog ( $db , $sys , "Nur Administratoren dürfen ÜK-Resultate verwalten." );
		
	$sys["script"] 		= 0;
	$sys["page_title"] 	= "Fehler beim Zugriff";
	$error = true;
		
	include ( $sys["root_path"] . "core/login/error.php" );
	include ( $sys["root_path"] . "_global/footer.php" );
	
	die ( );
}

############################################################################################
include ( $sys["root_path"] . "_global/footer_sta.php" );
############################################################################################
?>