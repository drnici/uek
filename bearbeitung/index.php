<?PHP
############################################################################################
$sys["root_path"] 	= "../../../../";
$sys["script"] 		= false;
$sys["nav_id"]		= 77;
############################################################################################
include ( $sys["root_path"] . "_global/header.php" );
############################################################################################
$sys["page_title"] 	= "Bearbeitungsantr&aumlge";
############################################################################################

if ( isset ( $_GET["s"] ) )
{
	echo ( "<p class=\"notification\"><b>Antrag erfolgreich bearbeitet!</b> Die / der Lernende wurd automatisch auf der Startseite auf Ihre Entscheidung hingewiesen.</p>\n" );
}
$request_count = $db->fctCountData("bew_uek_fb_bogen", "`bogen_korrektur` = 1");

if ( $request_count == 0 )
{
	echo ( "<p>Im Moment sind keine L&oumlschantr&aumlge offen.</p>\n" );
}
else
{
?>
    <table>
    <tr>
    <th>&nbsp;</th>
    <th>Antragsteller/in</th>
    <th>Antrag gestellt am</th>
    <th>&nbsp;</th>
    </tr>
    <?PHP
	$row_highlight = true;
    $request_result = $db->fctSendQuery ( "SELECT bufb.*, cp.person_name, cp.person_vorname FROM `bew_uek_fb_bogen` AS bufb INNER JOIN `core_person` AS cp ON bufb.person_fk_id = cp.person_id OR bufb.person_fk_id = MD5(cp.person_id) WHERE bufb.bogen_korrektur = 1 " );





	while ( $request_data = mysql_fetch_array ( $request_result ) )
    {
		if ( $row_highlight )
		{
			echo ( "<tr class=\"row_highlight\">\n" ); $row_highlight = false;
		}
		else
		{
			echo ( "<tr>\n" ); $row_highlight = true;
		}
		echo ( "<td><img src=\"" . $sys["icon_path"] . "bew_schule_tabelle_note_del_request.gif\" alt=\"L&oumlschantrag\" border=\"0\" /></td>\n" );
		echo ( "<td nowrap=\"nowrap\">" . $request_data["person_vorname"] . " " . $request_data["person_name"] . "</td>\n" );
		echo ( "<td>" . date("d.m.Y", strtotime($request_data["bogen_korrektur_time"])) . "</td>\n");

		echo ( "<td nowrap=\"nowrap\">\n" );

		echo ( "<a href=\"save.php?bogen_id=" . $request_data["bogen_id"] . "&amp;state=ok&amp;\" title=\"Antrag annehmen\"><img src=\"" . $sys["icon_path"] . "global_ok.gif\" alt=\"Antrag annehmen\" border=\"0\" /></a>\n" );
		echo ( "<a href=\"save.php?bogen_id=" . $request_data["bogen_id"] . "&amp;state=nok&amp;\" title=\"Antrag ablehnen\"><img src=\"" . $sys["icon_path"] . "global_nok.gif\" alt=\"Antrag ablehnen\" border=\"0\" /></a>\n" );

		echo ( "</td>\n" );
		echo ( "</tr>\n" );
    }
    ?>
    </table>
<?PHP
}

############################################################################################
include ( $sys["root_path"] . "_global/footer.php" );
############################################################################################
?>