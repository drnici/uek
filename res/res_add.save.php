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
	$uek_id					= mysql_escape_string ( htmlspecialchars ( $_POST["uek_id"] ) );
	$person_id				= mysql_escape_string ( htmlspecialchars ( $_POST["person_id"] ) );
	$res_note				= mysql_escape_string ( htmlspecialchars ( $_POST["res_note"] ) );
	
	if ( $uek_id == "" OR $person_id == "" OR $res_note == "" )
	{
		// Es wurden nicht alle Felder ausgefüllt
		header ( "Location: ./res_add.sta.php?s=mand&uek_id=" . $uek_id . "&person_id=" . $person_id . "&res_note=" . $res_note . "&" );
	}
	else
	{
		// eintragen
		if ( $db->fctSendQuery ( "INSERT INTO `bew_uek_res` (`uek_id`,`person_id`,`res_note`) VALUES ('" . $uek_id . "','" . $person_id . "','" . $res_note . "')" ) )
		{	
			// alles OK
			header ( "Location: res_add.sta.php?s=insert&" );
		}
		else
		{
			// Insert-Query wurde nicht korrekt in der Datenbank ausgeführt
			fctHandleLog ( $db , $sys , "Insert-Query wurde nicht korrekt in der Datenbank ausgeführt" );
			
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