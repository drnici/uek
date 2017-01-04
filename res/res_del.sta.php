<?PHP
############################################################################################
$sys["root_path"] 	= "../../../../";
$sys["script"] 		= false;
$sys["nav_id"]		= 28;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );
############################################################################################
$sys["page_title"] 	= "Resultat l&ouml;schen";
############################################################################################
if ( isset ( $_GET["res_id"] ) ) $res_id = mysql_escape_string ( htmlspecialchars ( $_GET["res_id"] ) );
if ( isset ( $res_id ) ) $res_data = $db->fctSelectData ( "bew_uek_res" , "`res_id` = '" . $res_id . "'" );
############################################################################################

if ( $sys["user"]["role_id"] == 5 )
{
	if ( isset ( $_GET["s"] ) )
	{
		if ( $_GET["s"] == "del" )
		{
		?>
			<script type="text/javascript">
			parent.location.reload();
			</script>
		<?PHP
		}
	}
	
	if ( !empty ( $res_data["res_id"] ) )
	{
		$uek_data	 = $db->fctSelectData ( "bew_uek" , "`uek_id` = " . $res_data["uek_id"] );
		$modul_data  = $db->fctSelectData ( "bew_modul" , "`modul_id` = " . $uek_data["modul_id"] );
		$person_data = $db->fctSelectData ( "core_person" , "`person_id` = " . $res_data["person_id"] );
	?>
	<form action="res_del.save.php" method="post" name="res_del">
	<input type="hidden" name="res_id" value="<?PHP echo ( $res_data["res_id"] ); ?>" />
	<table>
	<tr>
	<th>&Uuml;K</th>
	<td><?PHP echo ( $modul_data["modul_kurz"] . " " . $modul_data["modul_bezeichnung"] ); ?></td>
	</tr>
	<tr>
	<th>Jahrgang</th>
	<td><?PHP echo ( $uek_data["uek_jg"] ); ?></td>
	</tr>
	<tr>
	<th>Lernende/r</th>
	<td><?PHP echo ( $person_data["person_vorname"] . " " . $person_data["person_name"] ); ?></td>
	</tr>
	<tr>
	<th>Methodenkompetenz</th>
	<td><?PHP echo ( $res_data["res_note_methode"] ); ?></td>
	</tr>
	<tr>
	<th>Fachkompetenz</th>
	<td><?PHP echo ( $res_data["res_note_fach"] ); ?></td>
	</tr>
	<tr>
	<th>&nbsp;</th>
	<td>
	<input type="submit" class="btn" value="Resultat l&ouml;schen" />
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