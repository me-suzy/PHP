<?php
include("config.php");
include("header.php");
?>

<div align="center"><br>
<form action="contactmailed.php">
<table cellspacing="0" cellpadding="0" border="0" bordercolor="Black">
<tr>
    <td class="nav">&nbsp;&nbsp;<b><?echo $contactname;?>:</b>&nbsp;&nbsp;</td>
    <td class="nav">&nbsp;&nbsp;<input type="text" name="naam" size="40">&nbsp;&nbsp;</td>
</tr>
<tr>
    <td class="nav">&nbsp;&nbsp;<b><?echo $contactemail;?>:</b>&nbsp;&nbsp;</td>
    <td class="nav">&nbsp;&nbsp;<input type="text" name="email" size="40">&nbsp;&nbsp;</td>
</tr>
<tr>
    <td class="nav">&nbsp;&nbsp;<b><?echo $contactmessage;?>:</b>&nbsp;&nbsp;</td>
    <td class="nav">&nbsp;&nbsp;<textarea name=bericht cols="40" rows="5"></textarea>&nbsp;&nbsp;</td>
</tr>
</table>
<br>
<input type="submit" name="submit" class="hexfield1" value="Versturen">
</form>
</div>

<?
include("footer.php");
?>
