<?php
include("config.php");
include("header.php");
?>

<div align="center"><br>

<b><?echo $searchheader;?><br></b>

   <form action="search.php" method="post">
   <input type="hidden" name="submit" value="yes">
   <input type="text" name="search" size="35"><br><br>
   <input type="submit" class="hexfield1" value="<?echo $searchbutton;?>">
   <input type="Reset" class="hexfield1" value="<?echo $clearbutton;?>">
   </form>

<?php

if ($submit) {

$db=mysql_connect($sqlhost,$sqluser,$sqlpass);
mysql_select_db($db2,$db);

echo "<table border=0>";

$sql="SELECT * FROM divx WHERE titel LIKE '%$search%' ORDER BY titel ASC";
$resultaten=MySQL_query($sql,$db);
while($myrow=MySQL_fetch_array($resultaten))

{

$id=$myrow["id"];
$titel=$myrow["titel"];
$titel = stripslashes($titel);

echo " <tr><td><div align=\"center\"><a href=\"./divxdetails.php?id=".$myrow["id"]."\">$titel</a></div></td></tr>";

}
echo "</table>";
mysql_close();
}
?>

<br><br>
</div>

<?
include("footer.php");
?>
