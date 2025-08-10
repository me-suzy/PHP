<HTML>
<HEAD><?php
if(file_exists("verbannt/$chatuser.txt")){
echo"Banned!";
}else{
if (file_exists("user/$chatuser.daten.php")){
require("einstellungen.php");
require("user/$chatuser.daten.php");
?>
<TITLE><?php print "$chatname" ?></TITLE>
</HEAD>
<?
if ("$passwortlogin" == "$pw"){
$chatbenutzer = "<font face=\"Tahoma\"><span style=\"font-size:10pt;\"><a href=\"user/$chatuser.php\" target=\"neuesfenster\" onclick=\"window.open('','neuesfenster','top=50,screenX=50,left=100,screenY=100,height=250,width=450')\">$chatuser</a></span><br>";
$datei = fopen("online/$chatuser.txt","w");
fputs($datei, "\n");
fputs($datei, $chatbenutzer);
fclose($datei);
echo "<html>
<head>
<title>$chatname</title>
</head>
<frameset rows=\"18%, 56%, 26%\" cols=\"1*\">
<frame name=\"header\" scrolling=\"no\" marginwidth=\"10\" marginheight=\"14\" namo_target_frame=\"contents\" src=\"logo.php?username=$chatuser\">
<frameset rows=\"1*\" cols=\"21%, 79%\">
<frame name=\"contents\" scrolling=\"auto\" marginwidth=\"10\" marginheight=\"14\" namo_target_frame=\"detail\" src=\"aktuell.php\">
<frame name=\"detail\" scrolling=\"yes\" marginwidth=\"10\" marginheight=\"14\" src=\"messages.htm\">
</frameset>
<frame name=\"footer\" scrolling=\"no\" marginwidth=\"10\" marginheight=\"14\" namo_target_frame=\"contents\" src=\"eingabe.php?chatuser=$chatuser\">
<noframes>
<body bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#0000FF\" vlink=\"#800080\" alink=\"#FF0000\">

<p>Diese Seite enthält Frames. Sie benötigen einen Browser, der Frames unterstützt, um diese Seite anzeigen zu können.</p>
</body>
</noframes>
</frameset>
</html>";
}else{
echo "<html>

<head>
<title>ERROR!!!</title>
</head>

<body bgcolor=\"black\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\"><font face=\"Tahoma\" color=\"red\"><span style=\"font-size:72pt;\">Wrong password!</span></font></p>
<p align=\"center\">&nbsp;</p>
</body>

</html>";}
}else
{
echo"<html>

<head>
<title>ERROR!!!</title>
</head>

<body bgcolor=\"black\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\"><font face=\"Tahoma\" color=\"red\"><span style=\"font-size:72pt;\">Unknow Username!</span></font></p>
<p align=\"center\">&nbsp;</p>
</body>

</html>";
}}
?>