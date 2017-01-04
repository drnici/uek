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
	if ( isset ( $_POST["uek_id"] ) ) $uek_id = mysql_escape_string ( htmlspecialchars ( $_POST["uek_id"] ) );
	
	$modul_id				= mysql_escape_string ( htmlspecialchars ( $_POST["modul_id"] ) );
	$person_id				= mysql_escape_string ( htmlspecialchars ( $_POST["person_id"] ) );
	$ort_id					= mysql_escape_string ( htmlspecialchars ( $_POST["ort_id"] ) );
	$uek_jg					= mysql_escape_string ( htmlspecialchars ( $_POST["uek_jg"] ) );
	$uek_time_start_date	= mysql_escape_string ( htmlspecialchars ( $_POST["uek_time_start"] ) );
	$uek_time_end_date		= mysql_escape_string ( htmlspecialchars ( $_POST["uek_time_end"] ) );
	$uek_tage				= mysql_escape_string ( htmlspecialchars ( $_POST["uek_tage"] ) );
	$uek_mp_time_date		= mysql_escape_string ( htmlspecialchars ( $_POST["uek_mp_time"] ) );
	$uek_mpnr				= mysql_escape_string ( htmlspecialchars ( $_POST["uek_mpnr"] ) );
	
	// aus Startdatum einen Timestamp machen
	if ( !empty ( $uek_time_start_date ) )
	{
		$uek_time_start_temp	= explode ( "." , $uek_time_start_date );
		if ( count ( $uek_time_start_temp ) == 3 ) $uek_time_start = mktime ( 0 , 0 , 0 , $uek_time_start_temp[1] , $uek_time_start_temp[0] , $uek_time_start_temp[2] );
		else $uek_time_start = 0;
	}
	else $uek_time_start = 0;
	
	// aus Enddatum einen Timestamp machen
	if ( !empty ( $uek_time_end_date ) )
	{
		$uek_time_end_temp		= explode ( "." , $uek_time_end_date );
		if ( count ( $uek_time_end_temp ) == 3 ) $uek_time_end = mktime ( 0 , 0 , 0 , $uek_time_end_temp[1] , $uek_time_end_temp[0] , $uek_time_end_temp[2] );
		else $uek_time_end = 0;
	}
	else $uek_time_end = 0;
	
	// aus MP-Datum einen Timestamp machen
	if ( !empty ( $uek_mp_time_date ) )
	{
		$uek_mp_time_temp		= explode ( " " , $uek_mp_time_date );
		$uek_mp_time_temp_date	= explode ( "." , $uek_mp_time_temp[0] );
		$uek_mp_time_temp_time	= explode ( ":" , $uek_mp_time_temp[1] );
		
		if ( count ( $uek_mp_time_temp_date ) == 3 AND count ( $uek_mp_time_temp_time ) == 2 ) $uek_mp_time = mktime ( $uek_mp_time_temp_time[0] , $uek_mp_time_temp_time[1] , 0 , $uek_mp_time_temp_date[1] , $uek_mp_time_temp_date[0] , $uek_mp_time_temp_date[2] );
		else $uek_mp_time = 0;
	}
	else $uek_mp_time = 0;
	
	if ( $modul_id == "" OR $person_id == "" OR $ort_id == "" OR $uek_jg == "" OR $uek_time_start == 0 OR $uek_time_end == 0 OR $uek_tage == "" OR $uek_mp_time == 0 OR $uek_mpnr == "" )
	{
		// Es wurden nicht alle Felder ausgefüllt
		if ( isset ( $_POST["uek_id"] ) )
		{
			// edit
			header ( "Location: ./?s=mand&uek_id=" . $uek_id . "&modul_id=" . $modul_id . "&person_id=" . $person_id . "&ort_id=" . $ort_id . "&uek_jg=" . $uek_jg . "&uek_time_start=" . $uek_time_start . "&uek_time_end=" . $uek_time_end . "&uek_tage=" . $uek_tage . "&uek_mp_time=" . $uek_mp_time . "&uek_mpnr=" . $uek_mpnr . "&" );
		}
		else
		{
			// neu
			header ( "Location: ./?s=mand&modul_id=" . $modul_id . "&person_id=" . $person_id . "&ort_id=" . $ort_id . "&uek_jg=" . $uek_jg . "&uek_time_start=" . $uek_time_start . "&uek_time_end=" . $uek_time_end . "&uek_tage=" . $uek_tage . "&uek_mp_time=" . $uek_mp_time . "&uek_mpnr=" . $uek_mpnr . "&" );
		}	
	}
	else
	{
		if ( isset ( $uek_id ) )
		{
			// Bestehender UEK bearbeiten
			$db->fctSendQuery ( "UPDATE `bew_uek` SET `modul_id` = '" . $modul_id . "',`person_id` = '" . $person_id . "',`ort_id` = '" . $ort_id . "',`uek_jg` = '" . $uek_jg . "',`uek_time_start` = '" . $uek_time_start . "',`uek_time_end` = '" . $uek_time_end . "',`uek_tage` = '" . $uek_tage . "',`uek_mp_time` = " . $uek_mp_time . ",`uek_mpnr` = '" . $uek_mpnr . "' WHERE `uek_id` = '" . $uek_id . "'" );
		}
		else
		{
			// Bestehender UEK bearbeiten
			$db->fctSendQuery ( "INSERT INTO `bew_uek` (`modul_id`,`person_id`,`ort_id`,`uek_jg`,`uek_time_start`,`uek_time_end`,`uek_tage`,`uek_mp_time`,`uek_mpnr`) VALUES ('" . $modul_id . "','" . $person_id . "','" . $ort_id . "','" . $uek_jg . "'," . $uek_time_start . "," . $uek_time_end . ",'" . $uek_tage . "'," . $uek_mp_time . ",'" . $uek_mpnr . "')" );
			
			$uek_id = mysql_insert_id ();
		}
		
		
		if ( !empty ( $uek_id ) )
		{
			header ( "Location: ../?uek_id=" . $uek_id );
		}
		else
		{
			// Insert- oder Update-Query wurde nicht korrekt in der Datenbank ausgeführt
			fctHandleLog ( $db , $sys , "Insert- oder Update-Query wurde nicht korrekt in der Datenbank ausgeführt" );
			
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
	// Nur Administratoren dürfen ÜKs bearbeiten oder erstellen.
	fctHandleLog ( $db , $sys , "Nur Administratoren dürfen ÜKs bearbeiten oder erstellen." );
		
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