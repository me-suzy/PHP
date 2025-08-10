<?
if($schritt == "1"){
echo "
<html>

<head>
<title>Delete Nickname</title>
</head>

<body bgcolor=\"white\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p align=\"center\">&nbsp;</p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\"><b><span style=\"font-size:14pt;\"><font face=\"Tahoma\">Please enter your Nickname!</font></span></b></p>
<form name=\"form1\" action=\"$PHP_SELF?schritt=2\" method=\"post\">
    <p align=\"center\"><b><span style=\"font-size:14pt;\"><font face=\"Tahoma\"><input type=\"text\" name=\"username\" maxlength=\"15\" size=\"20\"></font></span></b></p>
    <p align=\"center\"><b><span style=\"font-size:14pt;\"><font face=\"Tahoma\"><input type=\"submit\" name=\"formbutton1\" value=\"Next\"></font></span></b></p>
</form>
<p align=\"center\">&nbsp;</p>
</body>

</html>";
}
if($schritt == "2"){
if (file_exists("user/$username.php")){
echo "<html>

<head>
<title>Delete $username</title>
</head>

<body bgcolor=\"white\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p align=\"center\">&nbsp;</p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\"><b><span style=\"font-size:14pt;\"><font face=\"Tahoma\">Please enter now your password!</font></span></b></p>
<form name=\"form1\" action=\"$PHP_SELF?schritt=3\" method=\"post\">
    <p align=\"center\"><b><span style=\"font-size:14pt;\"><font face=\"Tahoma\"><input type=\"password\" name=\"passwort\" maxlength=\"15\" size=\"20\"></font></span></b></p>
    <p align=\"center\"><b><span style=\"font-size:14pt;\"><font face=\"Tahoma\"><input type=\"submit\" name=\"formbutton1\" value=\"KILL USERNAME\"></font></span></b></p>
<input type=\"hidden\" name=\"username\" value=\"$username\"></form>
<p align=\"center\">&nbsp;</p>
</body>

</html>";}
else{
echo "<br><br><br><br><br><br><br><br><br><br><p align=\"center\"><b><font color=\"#CC0000\" face=\"Tahoma\"><span style=\"font-size:20pt;\">This Nickname isn't registred!</span></font></b></p><br><br><a href=\"javascript:history.back();\"><span style=\"font-size:12pt;\"><font color=\"#CC0000\" face=\"Tahoma\">&lt;&lt;
Back</font></span></a>";}}
if($schritt == "3"){
require ("user/$username.daten.php");
if ("$passwort" == "$pw"){
unlink("user/$username.php");
unlink("user/$username.daten.php");
echo "<br><br><br><br><br><br><br><br></p>
<p align=\"center\"><b><font color=\"#CC0000\" face=\"Tahoma\"><span style=\"font-size:20pt;\">Your account was delted!<br>Thanks for using my Chat!<br><br><b><a href=http://www.andys-chat.ch.vu>http://www.andys-chat.ch.vu</a></b></span></font></b></p>
";}
else{
echo "<br><br><br><br><br><br><br><br><br><br><p align=\"center\"><b><font color=\"#CC0000\" face=\"Tahoma\"><span style=\"font-size:20pt;\">&nbsp;Wrong
password!<br> </span></font><a href=\"javascript:history.back();\"><span style=\"font-size:12pt;\"><font color=\"#CC0000\" face=\"Tahoma\">&lt;&lt;
Back</font></span></a></b></p>";}}

?>