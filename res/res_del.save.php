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
	$res_id = mysql_escape_string ( htmlspecialchars ( $_POST["res_id"] ) );
	$res_data = $db->fctSelectData ( "bew_uek_res" , "`res_id` = '" . $res_id . "'" );
	
	if ( !empty ( $res_data["res_id"] ) )
	{
		if ( $db->fctSendQuery ( "DELETE FROM `bew_uek_res` WHERE `res_id` = " . $res_data["res_id"] ) )
		{	
			// alles OK
			header ( "Location: res_del.sta.php?s=del&" );
		}
		else
		{
			// Delete-Query wurde nicht korrekt in der Datenbank ausgeführt
			fctHandleLog ( $db , $sys , "Delete-Query wurde nicht korrekt in der Datenbank ausgeführt" );
			
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