<?PHP
############################################################################################
$sys["root_path"] 	= "../../../../../";
$sys["script"] 		= true;
$sys["nav_id"]		= 28;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );
############################################################################################

if ( $sys["user"]["role_id"] == 5 )
{
	if ( isset ( $_POST["uek_id"] ) ) 	$uek_id 	= mysql_escape_string ( htmlspecialchars ( $_POST["uek_id"] ) );
	if ( !empty ( $uek_id ) ) 			$uek_data   = $db->fctSelectData ( "bew_uek" , "`uek_id` = '" . $uek_id . "'" );
	
	if ( !empty ( $uek_data["uek_id"] ) )
	{
		$bogen_count 	= $db->fctCountData ( "bew_uek_fb_bogen" ,  "`uek_id` = " . $uek_data["uek_id"] );
		$bew_count 		= $db->fctCountData ( "bew_uek_res" , 		"`uek_id` = " . $uek_data["uek_id"] );
		
		if ( $bogen_count == 0 AND $bew_count == 0 )
		{
			$db->fctSendQuery ( "DELETE FROM `bew_uek` WHERE `uek_id` = " . $uek_data["uek_id"] );
			
			header ( "Location: ../../" );
		}
		else
		{
			// Zu diesem ÜK existieren bereits Feedbacks oder Resultate - Löschen nicht möglich.
			fctHandleLog ( $db , $sys , "Zu diesem ÜK existieren bereits Feedbacks oder Resultate - Löschen nicht möglich." );
				
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
		// Ungültige ÜK-ID wurde übermittelt
		fctHandleLog ( $db , $sys , "Ungültige ÜK-ID wurde übermittelt" );
			
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
	// Nur Administratoren dürfen ÜKs löschen.
	fctHandleLog ( $db , $sys , "Nur Administratoren dürfen ÜKs löschen." );
		
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