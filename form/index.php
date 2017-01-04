<?PHP
############################################################################################
$sys["root_path"] 	= "../../../../";
$sys["script"] 		= false;
$sys["nav_id"]		= 28;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );
############################################################################################

if ( $sys["user"]["role_id"] == 5 )
{
	if ( isset ( $_GET["uek_id"] ) ) $uek_id = mysql_escape_string ( htmlspecialchars ( $_GET["uek_id"] ) );
	if ( !empty ( $uek_id ) ) $uek_data = $db->fctSelectData ( "bew_uek" , "`uek_id` = '" . $uek_id . "'" );
	
	if ( !empty ( $uek_data["uek_id"] ) )
	{
		// UEK bearbeiten
		$sys["page_title"] = "&Uuml;K-Eintrag bearbeiten";
	
		if ( isset ( $_GET["s"] ) AND $_GET["s"] == "mand" )
		{
			$modul_id				= mysql_escape_string ( htmlspecialchars ( $_GET["modul_id"] ) );
			$person_id				= mysql_escape_string ( htmlspecialchars ( $_GET["person_id"] ) );
			$ort_id					= mysql_escape_string ( htmlspecialchars ( $_GET["ort_id"] ) );
			$uek_jg					= mysql_escape_string ( htmlspecialchars ( $_GET["uek_jg"] ) );
			$uek_time_start			= date ( $conf->strDateFormat, mysql_escape_string ( htmlspecialchars ( $_GET["uek_time_start"] ) ) );
			$uek_time_end			= date ( $conf->strDateFormat, mysql_escape_string ( htmlspecialchars ( $_GET["uek_time_end"] ) ) );
			$uek_tage				= mysql_escape_string ( htmlspecialchars ( $_GET["uek_tage"] ) );
			$uek_mp_time			= date ( $conf->strDateFormatFull, mysql_escape_string ( htmlspecialchars ( $_GET["uek_mp_time"] ) ) );
			$uek_mpnr				= mysql_escape_string ( htmlspecialchars ( $_GET["uek_mpnr"] ) );
			$uek_faktor_methode		= mysql_escape_string ( htmlspecialchars ( $_GET["uek_faktor_methode"] ) );
			$uek_faktor_fach		= mysql_escape_string ( htmlspecialchars ( $_GET["uek_faktor_fach"] ) );
		}
		else
		{
			$modul_id				= $uek_data["modul_id"];
			$person_id				= $uek_data["person_id"];
			$ort_id					= $uek_data["ort_id"];
			$uek_jg					= $uek_data["uek_jg"];
			$uek_time_start			= date ( $conf->strDateFormat, $uek_data["uek_time_start"] );
			$uek_time_end			= date ( $conf->strDateFormat, $uek_data["uek_time_end"] );
			$uek_tage				= $uek_data["uek_tage"];
			$uek_mpnr				= $uek_data["uek_mpnr"];
			$uek_mp_time			= date ( $conf->strDateFormatFull, $uek_data["uek_mp_time"] );
		}
	}
	else
	{
		// UEK neu
		$sys["page_title"] = "&Uuml;K-Eintrag erstellen";
	
		if ( isset ( $_GET["s"] ) AND $_GET["s"] == "mand" )
		{
			$modul_id				= mysql_escape_string ( htmlspecialchars ( $_GET["modul_id"] ) );
			$person_id				= mysql_escape_string ( htmlspecialchars ( $_GET["person_id"] ) );
			$ort_id					= mysql_escape_string ( htmlspecialchars ( $_GET["ort_id"] ) );
			$uek_jg					= mysql_escape_string ( htmlspecialchars ( $_GET["uek_jg"] ) );
			$uek_time_start			= date ( $conf->strDateFormat, mysql_escape_string ( htmlspecialchars ( $_GET["uek_time_start"] ) ) );
			$uek_time_end			= date ( $conf->strDateFormat, mysql_escape_string ( htmlspecialchars ( $_GET["uek_time_end"] ) ) );
			$uek_tage				= mysql_escape_string ( htmlspecialchars ( $_GET["uek_tage"] ) );
			$uek_mp_time			= date ( $conf->strDateFormatFull, mysql_escape_string ( htmlspecialchars ( $_GET["uek_mp_time"] ) ) );
			$uek_mpnr				= mysql_escape_string ( htmlspecialchars ( $_GET["uek_mpnr"] ) );
			$uek_faktor_methode		= mysql_escape_string ( htmlspecialchars ( $_GET["uek_faktor_methode"] ) );
			$uek_faktor_fach		= mysql_escape_string ( htmlspecialchars ( $_GET["uek_faktor_fach"] ) );
		}
		else
		{
			$modul_id				= 0;
			$person_id				= 0;
			$ort_id					= 0;
			$uek_jg					= date ( "Y" );
			$uek_time_start			= date ( $conf->strDateFormat );
			$uek_time_end			= date ( $conf->strDateFormat );
			$uek_tage				= "5";
			$uek_mp_time			= date ( $conf->strDateFormatFull );
			$uek_mpnr				= "MP-";
			$uek_faktor_methode		= 0.5;
			$uek_faktor_fach		= 0.5;
		}
	}
	?>
	
	<form action="save.php" name="uek_add" method="post">
	<?PHP
	if ( !empty ( $uek_data["uek_id"] ) ) echo ( "<input type=\"hidden\" name=\"uek_id\" value=\"" . $uek_data["uek_id"] . "\" />\n" );
	?>
	<table>
	<tr>
	<th>Nummer / Bezeichnung</th>
	<th>*</th>
	<td>
	<select name="modul_id" size="1">
	<option value="">..</option>
	<?PHP
	$modul_result = $db->fctSendQuery ( "SELECT bm.modul_id, bm.modul_kurz, bm.modul_bezeichnung FROM `bew_modul` AS bm WHERE bm.modul_uek = 1 ORDER BY bm.modul_kurz" );
	while ( $modul_data = mysql_fetch_array ( $modul_result ) )
	{
		$s = "";
		if ( $modul_id == $modul_data["modul_id"] ) $s = " selected=\"selected\"";
		
		echo ( "<option value=\"" . $modul_data["modul_id"] . "\"" . $s . ">" . $modul_data["modul_kurz"] . " " . $modul_data["modul_bezeichnung"] . "</option>\n" );
	}
	?>
	</select>
	</td>
	</tr>
	<tr>
	<th>Kursleitung</th>
	<th>*</th>
	<td>
	<select name="person_id" size="1">
	<option value="">..</option>
	<?PHP
	$person_result = $db->fctSendQuery ( "SELECT cp.person_id, cp.person_vorname, cp.person_name FROM `core_person` AS cp WHERE cp.role_id = 5 ORDER BY cp.person_vorname" );
	while ( $person_data = mysql_fetch_array ( $person_result ) )
	{
		$s = "";
		if ( $person_id == $person_data["person_id"] ) $s = " selected=\"selected\"";
	
		echo ( "<option value=\"" . $person_data["person_id"] . "\"" . $s . ">" . $person_data["person_vorname"] . " " . $person_data["person_name"] . "</option>\n" );
	}
	?>
	</select>
	</td>
	</tr>
	<tr>
	<th>Firma / Kursort</th>
	<th>*</th>
	<td>
	<select name="ort_id" size="1">
	<option value="">..</option>
	<?PHP
	$ort_result = $db->fctSendQuery ( "SELECT buo.* FROM `bew_uek_ort` AS buo ORDER BY buo.ort_firma" );
	while ( $ort_data = mysql_fetch_array ( $ort_result ) )
	{
		$s = "";
		if ( $ort_id == $ort_data["ort_id"] ) $s = " selected=\"selected\"";
	
		echo ( "<option value=\"" . $ort_data["ort_id"] . "\"" . $s . ">" . $ort_data["ort_firma"] . ", " . $ort_data["ort_adresse"] . ", " . $ort_data["ort_plz"] . " " . $ort_data["ort_ort"] . "</option>\n" );
	}
	?>
	</select>
	</td>
	</tr>
	<tr>
	<th>Lernenden-Jahrgang</th>
	<th>*</th>
	<td><input type="text" name="uek_jg" value="<?PHP echo ( $uek_jg ); ?>" /></td>
	</tr> 
	<tr>
	<th>Datum Start</th>
	<th>*</th>
	<td><input type="text" name="uek_time_start" value="<?PHP echo ( $uek_time_start ); ?>" /></td>
	</tr>
	<tr>
	<th>Datum Ende</th>
	<th>*</th>
	<td><input type="text" name="uek_time_end" value="<?PHP echo ( $uek_time_end ); ?>" /></td>
	</tr>
	<tr>
	<th>&Uuml;K-Tage</th>
	<th>*</th>
	<td><input type="text" name="uek_tage" value="<?PHP echo ( $uek_tage ); ?>" /></td>
	</tr>
	<tr>
	<th>MP-Datum</th>
	<th>*</th>
	<td><input type="text" name="uek_mp_time" value="<?PHP echo ( $uek_mp_time ); ?>" /></td>
	</tr>
	<tr>
	<th>MP-Nummer</th>
	<th>*</th>
	<td><input type="text" name="uek_mpnr" value="<?PHP echo ( $uek_mpnr ); ?>" /></td>
	</tr>
	<tr>
	<th>&nbsp;</th>
	<th>&nbsp;</th>
	<td>
	<input type="submit" name="btn" class="btn" value="&Uuml;K-Eintrag speichern" />
	<input type="button" name="back" class="btn" value="Abbrechen" onclick="self.location.href = '../';" />
	</td>
	</tr>
	</table>
	
	</form>
    
<?PHP
}
else
{
	// Nur Administratoren d�rfen �Ks bearbeiten oder erstellen.
	fctHandleLog ( $db , $sys , "Nur Administratoren d�rfen �Ks bearbeiten oder erstellen." );
		
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