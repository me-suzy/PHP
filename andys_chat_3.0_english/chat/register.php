<?
require ("einstellungen.php");
file_exists("user/$nickname.php");
if (file_exists("user/$nickname.php")) {
echo "<table align=\"center\" border=\"2\" cellspacing=\"0\" width=\"506\" bordercolordark=\"black\" bordercolorlight=\"black\">
    <tr>
        <td width=\"496\">
            <p align=\"center\"><font face=\"Tahoma\">This nickname is already in use!
            Please choose antoher name!</font></p>
            <p align=\"center\"><a href=\"javascript:history.back()\"><font face=\"Tahoma\">&lt;&lt;
            Back</font></a></p>
        </td>
    </tr>
</table>";
}
else {
$datei = fopen("user/$nickname.daten.php", "w");
fputs($datei, "<?
$"."name = \"$user\";
$"."username = \"$nickname\";
$"."homepage = \"$hp\";
$"."email = \"$email\";
$"."alter = \"$alter\";
$"."about = \"$about\";
$"."pw = \"$passwort\";
?>");
fclose($datei);
$datei2 = fopen("user/$nickname.php", "w");
fputs($datei2, "<?
require \"$nickname.daten.php\";
echo \"<p><span style=font-size:14pt;><font face=Tahoma>$nickname's fact file</font></span><br>&nbsp;</p>
<table border=1 width=366 cellspacing=0 bordercolordark=black bordercolorlight=black>
    <tr>
        <td width=107>
            <p><span style=font-size:10pt;><font face=Tahoma>&nbsp;Name:</font></span></p>
        </td>
        <td width=249>
            <p><span style=font-size:10pt;><font face=Tahoma>$user</font></span></p>
        </td>
    </tr>
    <tr>
        <td width=107>
            <p><span style=font-size:10pt;><font face=Tahoma>&nbsp;Homepage:</font></span></p>
        </td>
        <td width=249>
            <p><a href=$homepage
   target=neuesfenster
   onclick=window.open('','neuesfenster','height=600,width=800,toolbar,location,directories,status,scrollbars,menubar,resizable')><span style=font-size:10pt;><font face=Tahoma>$hp</font></span></a></p>
        </td>
    </tr>
    <tr>
        <td width=107>
            <p><span style=font-size:10pt;><font face=Tahoma>&nbsp;eMail:</font></span></p>
        </td>
        <td width=249>
            <p><a href=mailto:$email><span style=font-size:10pt;><font face=Tahoma>$email</font></span></a></p>
        </td>
    </tr>
    <tr>
        <td width=107>
            <p><span style=font-size:10pt;><font face=Tahoma>&nbsp;Age:</font></span></p>
        </td>
        <td width=249>
            <p><span style=font-size:10pt;><font face=Tahoma>$alter</font></span></p>
        </td>
    </tr><tr>
        <td width=107>
            <p><span style=font-size:10pt;><font face=Tahoma>&nbsp;About:</font></span></p>
        </td>
        <td width=249>
            <p><span style=font-size:10pt;><font face=Tahoma>$about</font></span></p>
        </td>
    </tr>
</table>\"
?>");
$mail_to = "$email";
$mail_betreff = ">> LOG-IN-Daten für $chatname <<";
$mail_text = "Your facts:\n\nUsername: $nickname\nPassword: $passwort\n\n For login, go to $weburl/login.php\n\nAndys Chat 3.0 | http://www.php-scripts.ch.vu";
$mail_header = "From: $webmasteremail";
$mail_gesendet = mail($mail_to, $mail_betreff, $mail_text, $mail_header);
if ($mail_gesendet){
echo "<br><br><br><br><p align=\"center\"><font face=\"Tahoma\"><span style=\"font-size:16pt;\">Welcome! You are registred! Please check your Mail-account!</span></font></p>
<p align=\"center\"><a href=\"$weburl/login.php\">HERE</a> you can login!!</span></font></p>";}
else{
echo "<br><br><br><br><p align=\"center\"><a href=\"$weburl/login.php\"><font face=\"Tahoma\"><span style=\"font-size:16pt;\">HERE</a>
 you can login!!</span></font></p>";
}
}
?>