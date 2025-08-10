<?php
include("config.php");
include("header.php");
?>

<div align="center"><br>
<?

$db=mysql_connect($sqlhost,$sqluser,$sqlpass);
mysql_select_db($db2,$db);

   if ($submit) {

$titelupdate = addslashes($titelupdate);
$kwaliteitupdate = addslashes($kwaliteitupdate);
$statusupdate = addslashes($statusupdate);

      $sql="UPDATE divx SET titel='$titelupdate', kwaliteit='$kwaliteitupdate', status='$statusupdate' WHERE id='$idupdate'";

      mysql_query($sql) or die("<strong>$updatefailed</strong>");

      echo "<br><center><strong>$divxupdated</strong></center><br><br>";

   }
?>
<br><br>

<?
mysql_close();
include("footer.php");
?>
