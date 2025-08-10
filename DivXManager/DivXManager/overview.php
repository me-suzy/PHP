<?php
include("config.php");
include("header.php");
?>

<div align="center"><br>
<table border=0>
<?php

$db=mysql_connect($sqlhost,$sqluser,$sqlpass);
mysql_select_db($db2,$db);

$sql="SELECT * FROM divx ORDER BY titel ASC";
$resultaten=MySQL_query($sql,$db);
while($myrow=MySQL_fetch_array($resultaten))

{

$id=$myrow["id"];
$titel=$myrow["titel"];
$titel = stripslashes($titel);

echo " <tr><td><div align=\"center\"><a href=\"./divxdetails.php?id=".$myrow["id"]."\">$titel</a></div></td></tr>";

}

?>
</table>
<br><br>
</div>

<?
mysql_close();
include("footer.php");
?>
