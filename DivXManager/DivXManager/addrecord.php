<?php
include("config.php");
include("header.php");
?>

<div align="center"><br>
<?php

// Connect to the MySQL database
$db=mysql_connect($sqlhost,$sqluser,$sqlpass);
mysql_select_db($db2,$db);

   if ($submit) {

   	  $titel = addslashes($titel);
   	  $kwaliteit = addslashes($kwaliteit);
   	  $status = addslashes($status);

      // Create SQL query to add a movie to the database
      $sqltoevoegen = "INSERT INTO divx (titel, kwaliteit, status)
         VALUES ('$titel', '$kwaliteit', '$status')";

      // Execute MySQL query
      mysql_query($sqltoevoegen) or die("<strong>$addingdivxfailed</strong>");

      // The Last words
      echo "<br><strong>$addingdivxgood</strong><br><br>";
          
   }
?>

<table cellspacing="0" cellpadding="0" border="0"">
   <form action="addrecord.php" method="post">
   <input type="hidden" name="submit" value="yes">
<tr>
    <td valign="top"><strong><?echo $divxtitel;?></strong></td>
    <td valign="top"><input type="text" name="titel" size="35"><br>&nbsp;</td>
</tr>
<tr>
    <td><strong><?echo $divxquality;?></strong>&nbsp;&nbsp;</td>
    <td>
<select name="kwaliteit" size="1">
<option selected><?echo $excelent;?>
<option><?echo $good;?>
<option><?echo $normal;?>
<option><?echo $acceptable;?>
<option><?echo $bad;?>
<option><?echo $unknown;?>
</select>
&nbsp;&nbsp;
<strong><?echo $divxstatus;?></strong>
&nbsp;
<select name="status" size="1">
<option selected><?echo $home;?>
<option><?echo $loaned;?>
<option><?echo $lost;?>
<option><?echo $dammaged;?>
<option><?echo $incomplete;?>
</select>
</td>
</tr>
</table>
   <br>
   <input type="submit" class="hexfield1" value="<?echo $adddivxbutton;?>">
   <input type="Reset" class="hexfield1" value="<?echo $clearbutton;?>">
   </form>
<br><br>

<?
mysql_close();
include("footer.php");
?>
