<?php


 $verbindung = mysql_connect("localhost",
 "loginportal","f04697a1aa"); mysql_select_db("loginportal");

$abfrage = "SELECT bogen_comment FROM bew_uek_fb_bogen";
$ergebnis = mysql_query($abfrage);
while($bogen_comment = mysql_fetch_object($ergebnis))
   {
   if ( $abfrage["bogen_comment"] != NULL )
   
   echo "$bogen_comment->bogen_comment "."<br />";
   
   }
 ?>