<?php
include("config.php");
include("header.php");
?>

<?php

$db=mysql_connect($sqlhost,$sqluser,$sqlpass);
mysql_select_db($db2,$db);

if (isset($id))
{
    $sql="SELECT * FROM divx WHERE id='$id'";
}
else
{
    echo "$noidgiven";
    exit();
}

$resultaten=MySQL_query($sql);
list($id, $titel, $kwaliteit, $status) = 
MySQL_fetch_row($resultaten);

$titel = stripslashes($titel);
$kwaliteit = stripslashes($kwaliteit);
$status = stripslashes($status);
?>
<br>
<div align=center>
<table cellspacing="0" cellpadding="0" border="0">
   <form action="updaterecordok.php" method="post">
   <input type="hidden" name="submit" value="yes">
<tr>
    <td>ID</td>
    <td><input type="hidden" name="idupdate" value="<?echo $id?>"><?echo $id;?></td>
</tr>
<tr>
    <td><strong><?echo $divxtitel;?></strong></td>
    <td><input type="text" name="titelupdate" value="<?echo $titel?>"></td>
</tr>
<tr>
    <td><strong><?echo $divxquality;?></strong>&nbsp;&nbsp;</td>
    <td>
	<select name="kwaliteitupdate" size="1">
	<option selected><?echo $kwaliteit ;?>
	<option><?echo $excelent;?>
	<option><?echo $good;?>
	<option><?echo $normal;?>
	<option><?echo $acceptable;?>
	<option><?echo $bad;?>
	<option><?echo $unknown;?>
	</select>
	</td>
</tr>
<tr>
    <td><strong><?echo $divxstatus;?></strong></td>
    <td>
<select name="statusupdate" size="1">
<option selected><?echo $status ;?>
<option><?echo $home;?>
<option><?echo $loaned;?>
<option><?echo $lost;?>
<option><?echo $dammaged;?>
<option><?echo $incomplete;?>
</select>	
	</td>
</tr>
</table>
   <br>
   <input type="submit" class="hexfield1" value="<?echo $updatebutton;?>">
   <input type="Reset" class="hexfield1" value="<?echo $clearbutton;?>">
   </form>
</div>
<br><br>
<?
mysql_close();
include("footer.php");
?>
