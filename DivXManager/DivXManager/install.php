<?php
include("config.php");
include("header.php");
?>
<?php

   if ($submit) {

   // Connect to the MySQL database
	$installdb2 = $installdb;
	$installdb=mysql_connect($installhost,$installuser,$installpass);
	mysql_select_db($installdb2,$installdb);

    // Create SQL query to add a movie to the database
    $sqltoevoegen = "CREATE TABLE divx (
	id bigint(20) NOT NULL auto_increment, 
	titel text NOT NULL, 
	kwaliteit text NOT NULL, 
	status text NOT NULL, 
	PRIMARY KEY  (id), 
	UNIQUE KEY id (id), 
	KEY id_2 (id) 
	)TYPE=MyISAM;";
	$sqlexample = "INSERT INTO divx VALUES (1, 'Testmovie', 'Excelent', 'Home');";

    // Execute MySQL query's
    mysql_query($sqltoevoegen) or die("<div align=\"center\"><strong>$addingtablesfailed</strong></div>");
    mysql_query($sqlexample) or die("<div align=\"center\"><strong>$addingtablesfailed</strong></div>");
	
    // The Last words
    echo "<br><div align=\"center\"><strong>$tablesadded<br><br>$deletefile</strong></div><br>";
    mysql_close();
   }
?>
<?
if (!isSet($installhost)){
?>
<div align="center">
<strong><?echo $checkdata;?></strong>
<table cellspacing="0" cellpadding="0" border="0"">
   <form action="install.php" method="post">
   <input type="hidden" name="submit" value="yes">
<tr>
    <td valign="top"><strong>Database</strong></td>
    <td valign="top"><input type="text" name="installdb" size="35" value="<?echo $db;?>"></td>
</tr>
<tr>
    <td valign="top"><strong>Host</strong></td>
    <td valign="top"><input type="text" name="installhost" size="35" value="<?echo $sqlhost;?>"></td>
</tr>
<tr>
    <td valign="top"><strong>User</strong></td>
    <td valign="top"><input type="text" name="installuser" size="35" value="<?echo $sqluser;?>"></td>
</tr>
<tr>
    <td valign="top"><strong>Password</strong></td>
    <td valign="top"><input type="text" name="installpass" size="35" value="<?echo $sqlpass;?>"></td>
</tr>
</table>
   <br>
   <input type="submit" class="hexfield1" value="<?echo $install;?>">
   <input type="Reset" class="hexfield1" value="<?echo $clearbutton;?>">
   </form>
<?
} else {
}
?>
<br><br>
</div>
<?
include("footernomenu.php");
?>
