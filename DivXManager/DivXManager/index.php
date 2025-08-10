<?php
include("config.php");
include("header.php");
?>

<div align="center"><br>
<table border="0">
<tr>
	<td><a href="index.php" class="a"><img src="images/home.gif" width="30" height="30" border="0" alt="<?echo $mainpage;?>"></a></td>
	<td><strong><?echo $mainpage;?></strong></td>
</tr>
<tr>
	<td><a href="overview.php" class="a"><img src="images/overview.gif" width="30" height="30" border="0" alt="<?echo $overview;?>"></a></td>
	<td><strong><?echo $overview;?></strong></td>
</tr>
<tr>
	<td><a href="search.php" class="a"><img src="images/search.gif" width="30" height="30" border="0" alt="<?echo $searchdivx;?>"></a></td>
	<td><strong><?echo $searchdivx;?></strong></td>
</tr>
<tr>
	<td><a href="addrecord.php" class="a"><img src="images/add.gif" width="30" height="30" border="0" alt="<?echo $adddivx;?>"></a></td>
	<td><strong><?echo $adddivx;?></strong></td>
</tr>
<tr>
	<td><a href="editrecord.php" class="a"><img src="images/edit.gif" width="30" height="30" border="0" alt="<?echo $editdivx;?>"></a></td>
	<td><strong><?echo $editdivx;?></strong></td>
</tr>
<tr>
	<td><a href="contact.php" class="a"><img src="images/contact.gif" width="30" height="30" border="0" alt="<?echo $contact;?>"></a></td>
	<td><strong><?echo $contact;?></strong></td>
</tr>
<tr>
	<td><a href="help.php" class="a"><img src="images/help.gif" width="30" height="30" border="0" alt="<?echo $help;?>"></a></td>
	<td><strong><?echo $help;?></strong></td>
</tr>
</table>

<br><br>
</div>

<?
include("footer.php");
?>
