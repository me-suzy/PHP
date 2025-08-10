<?php
include("config.php");
include("header.php");
?>

<div align="center"><br>
<?php

$db=mysql_connect($sqlhost,$sqluser,$sqlpass);
mysql_select_db($db2,$db);   

$sql="SELECT * FROM divx WHERE id='$id'";

$resultaten=MySQL_query($sql);
list($id, $titel, $kwaliteit, $status) =
MySQL_fetch_row($resultaten);
$titel=stripslashes($titel);
$kwaliteit=stripslashes($kwaliteit);
$status=stripslashes($status);

?>

<br>
<table border="0">
<tr>
	<td><b><?echo $divxtitel;?>:</b></td>
	<td><b><?php echo $titel; ?></b></td>
</tr>
<tr>
	<td><b><?echo $divxquality;?>:</b>&nbsp;&nbsp;</td>
	<td><b><?php echo $kwaliteit; ?></b></td>
</tr>
<tr>
	<td><b><?echo $divxstatus;?>:</b></td>
	<td><b><?php echo $status; ?></b></td>
</tr>
</table>
</div>
<br><br>

<?
mysql_close();
include("footer.php");
?>
