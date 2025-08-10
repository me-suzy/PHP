<?php
include("config.php");
include("header.php");
?>

<div align="center"><br>
<?php

$db=mysql_connect($sqlhost,$sqluser,$sqlpass);
mysql_select_db($db2,$db);

mysql_query("delete from divx where id='$id'") or die("$removedivxfailed");

echo "<div align=center><br>$divxremoved<br></div><br>";

?>
</div>
<br><br>
<?
mysql_close();
include("footer.php");
?>
