<?php
include("config.php");
include("header.php");
?>

<div align="center"><br>
<table border=0>
<?php
$db=mysql_connect($sqlhost,$sqluser,$sqlpass);
mysql_select_db($db2,$db);

$sql="select * FROM divx ORDER BY titel ASC";
$resultaten=MySQL_query($sql,$db);
while($myrow=MySQL_fetch_array($resultaten))

{
$id=$myrow["id"];
$titel=$myrow["titel"];
$titel = stripslashes($titel);
$edit="updaterecord.php?id=".$id;
$delete="removerecord.php?id=".$id;

echo " <tr><td>$titel &nbsp;&nbsp;</td><td><a href=\"$edit\">$recordedit</a></td>
<td><a href=\"$delete\">$recorddelete</a></td></tr>";

}
?>
</table>
</div>
<br><br>
<?
mysql_close();
include("footer.php");
?>
