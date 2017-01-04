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
			// Zu diesem �K existieren bereits Feedbacks oder Resultate - L�schen nicht m�glich.
			fctHandleLog ( $db , $sys , "Zu diesem �K existieren bereits Feedbacks oder Resultate - L�schen nicht m�glich." );
				
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
		// Ung�ltige �K-ID wurde �bermittelt
		fctHandleLog ( $db , $sys , "Ung�ltige �K-ID wurde �bermittelt" );
			
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
	// Nur Administratoren d�rfen �Ks l�schen.
	fctHandleLog ( $db , $sys , "Nur Administratoren d�rfen �Ks l�schen." );
		
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