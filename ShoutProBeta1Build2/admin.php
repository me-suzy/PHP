<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>

<!-- Begin Scrollbar Styles -->
<style>  
<!-- BODY{ scrollbar-face-color:#666666; scrollbar-arrow-color:#444444; scrollbar-track-color:#333333; scrollbar-shadow-color:'#333333'; scrollbar-highlight-color:''; scrollbar-3dlight-color:'#333333'; scrollbar-darkshadow-Color:'#333333'; } -->   
</style>    
<!-- End Scrollbar Styles -->

<!-- Begin Link Styles -->
<style type="text/css">
 a:link{color:#999999;text-decoration:none;font-weight: bold;} a:visited{color:#999999;text-decoration:none;font-weight: bold;} a:active{color:#999999;text-decoration:none;font-weight: bold;} a:hover{color:#999999;text-decoration:underline;font-weight: bold;}   
</style>
<!-- End Link Styles -->

<title>ShoutPro&#8482; Admin</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor='#333333' text='#DDDDCC'><font face='tahoma'>
<div align="center" style="font-size:20pt">ShoutPro&#8482; Admin</div><br>
<div align="center" style="font-size:10pt">
<?php

require("config.php");

if($action=="clear" && $password==$thepassword){
	$FilePointer=fopen("shouts.php", "w+");
	echo("Shoutbox cleared.");
} else if($action=="clear" && $password!=$thepassword){
	echo("Wrong password!");
} else {
	echo("<form name='clear' method='post' action='admin.php?action=clear'>Clear Shoutbox: <input type='password' name='password' size='10'><input type='submit' value='Submit'></form>");
}
?>

<p>
<div align="center">
<a href="admin-smilies.php">Manage Smilies</a><br>
<a href="admin-profanityfilter.php">Manage Profanity Filter</a><br>
<a href="admin-shoutcode.php">Manage ShoutCode</a><br>
</div>