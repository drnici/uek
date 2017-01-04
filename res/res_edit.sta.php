<?PHP
############################################################################################
$sys["root_path"] 	= "../../../../";
$sys["script"] 		= false;
$sys["nav_id"]		= 28;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );
############################################################################################
$sys["page_title"]	= "Resultat bearbeiten";
############################################################################################
if ( isset ( $_GET["res_id"] ) ) $res_id = mysql_escape_string ( htmlspecialchars ( $_GET["res_id"] ) );
if ( isset ( $res_id ) ) $res_data = $db->fctSelectData ( "bew_uek_res" , "`res_id` = '" . $res_id . "'" );
############################################################################################

if ( $sys["user"]["role_id"] == 5 )
{
	if ( isset ( $_GET["s"] ) )
	{
		if ( $_GET["s"] == "mand" )
		{
			$person_id 			= htmlspecialchars ( mysql_escape_string ( $_GET["person_id"] ) );
			$res_note_methode 	= htmlspecialchars ( mysql_escape_string ( $_GET["res_note_methode"] ) );
			$res_note_fach		= htmlspecialchars ( mysql_escape_string ( $_GET["res_note_fach"] ) );
		}
		else if ( $_GET["s"] == "insert" )
		{
		?>
			<script type="text/javascript">
			parent.location.reload();
			</script>
		<?PHP
		}
	}
	else
	{
		$person_id 			= $res_data["person_id"];
		$res_note_methode 	= $res_data["res_note_methode"];
		$res_note_fach		= $res_data["res_note_fach"];
	}

	if ( !empty ( $res_data["res_id"] ) )
	{
		$uek_data	 = $db->fctSelectData ( "bew_uek" , "`uek_id` = " . $res_data["uek_id"] );
		$modul_data  = $db->fctSelectData ( "bew_modul" , "`modul_id` = " . $uek_data["modul_id"] );
		?>
		<form action="res_edit.save.php" method="post" name="res_edit">
		<input type="hidden" name="res_id" value="<?PHP echo ( $res_data["res_id"] ); ?>" />
		<table>
		<tr>
		<th>&Uuml;K</th>
		<th>&nbsp;</th>
		<td><?PHP echo ( $modul_data["modul_kurz"] . " " . $modul_data["modul_bezeichnung"] ); ?></td>
		</tr>
		<tr>
		<th>Jahrgang</th>
		<th>&nbsp;</th>
		<td><?PHP echo ( $uek_data["uek_jg"] ); ?></td>
		</tr>
		<tr>
		<th>Lernende/r</th>
		<th>*</th>
		<td>
		<select name="person_id" size="1">
		<option value="">..</option>
		<?PHP
		if ( date ( "m" ) < 8 ) $calc_year = date ( "Y" ) - 1;
		else					$calc_year = date ( "Y" );
        $diff = $calc_year - $uek_data["uek_jg"];
		
		$jg_first  = 1 + ( 2 * $diff );
		$jg_second = 2 + ( 2 * $diff );
		
		$person_result = $db->fctSendQuery ( "SELECT cp.person_id, cp.person_vorname, cp.person_name FROM `core_person` AS cp WHERE cp.role_id = 1 AND ( cp.person_s_semester = " . $jg_first . " OR cp.person_s_semester = " . $jg_second . " ) ORDER BY cp.person_vorname ASC, cp.person_name ASC" );
		while ( $person_data = mysql_fetch_array ( $person_result ) )
		{
			$count_bew = $db->fctCountData ( "bew_uek_res" , "`uek_id` = " . $uek_data["uek_id"] . " AND `person_id` = " . $person_data["person_id"] . " AND `res_id` <> " . $res_data["res_id"] );
			
			$s = "";
			if ( $person_id == $person_data["person_id"] ) $s = " selected=\"selected\"";
			
			if ( $count_bew == 0 ) echo ( "<option value=\"" . $person_data["person_id"] . "\"" . $s . ">" . $person_data["person_vorname"] . " " . $person_data["person_name"] . "</option>\n" );
		}
		?>
		</select>
		</td>
		</tr>
		<tr>
		<th>Methodenkompetenz</th>
		<th>*</th>
		<td><input type="text" name="res_note_methode" value="<?PHP echo ( $res_note_methode ); ?>" maxlength="3" style="width: 40px;" /> (<?PHP echo ( $uek_data["uek_faktor_methode"] * 100 ); ?>%)</td>
		</tr>
		<tr>
		<th>Fachkompetenz</th>
		<th>*</th>
		<td><input type="text" name="res_note_fach" value="<?PHP echo ( $res_note_fach ); ?>" maxlength="3" style="width: 40px;" /> (<?PHP echo ( $uek_data["uek_faktor_fach"] * 100 ); ?>%)</td>
		</tr>
		<tr>
		<th>&nbsp;</th>
		<th>&nbsp;</th>
		<td>
		<input type="submit" class="btn" value="Resultat eintragen" />
		<input type="button" class="btn" value="Abbrechen" onclick="self.parent.tb_remove();" />
		</td>
		</tr>
		</table>
		</form>
		<p>&nbsp;</p>
		
		<?PHP
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