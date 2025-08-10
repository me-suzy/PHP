<?
if($action == "go"){
echo"<html>

<head>
<title>Administration</title>
</head>

<body bgcolor=\"white\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<form name=\"form2\" action=\"admin.php?action=admin\" method=\"post\">
<p align=\"center\"><span style=\"font-size:20pt;\"><font face=\"Tahoma\"><u>Administration</u></font></span></p>
</form>
<form name=\"form2\" method=\"post\" action=\"admin.php?action=admin\">
    <p align=\"center\"><span style=\"font-size:11pt;\"><font face=\"Tahoma\">Please enter the Admin-Password!!<br></font></span></p>
    <p align=\"center\"><span style=\"font-size:8pt;\"><font face=\"Tahoma\"><i><input type=\"password\" name=\"passwort\" size=\"31\"></i></font></span></p>
    <p align=\"center\"><span style=\"font-size:8pt;\"><font face=\"Tahoma\"><i><input type=\"submit\" name=\"go\" value=\"Enter Admin!\"></i></font></span></p>
</form>
</body>

</html>";}
if($action == "admin"){
if (file_exists("../einstellungen.php")){
require ("../einstellungen.php");}
else{
$adminpw = "admin";}
if("$passwort" == "$adminpw"){
echo "<html><head>
<title>Administration</title>
</head>
<body bgcolor=\"white\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p>&nbsp;</p><form name=\"form2\" action=\"admin.php?action=speichern\" method=\"post\">
<p align=\"center\"><span style=\"font-size:20pt;\"><font face=\"Tahoma\"><u>Administration</u></font></span></p>
<p align=\"center\"><span style=\"font-size:10pt;\"><font face=\"Tahoma\">&nbsp;</font></span></p>
<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"797\" bordercolordark=\"white\" bordercolorlight=\"black\">
    <tr>
        <td width=\"152\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
            <p align=\"right\"><span style=\"font-size:10pt;\"><font face=\"Tahoma\">Chatname:
            &nbsp;</font></span></p>
        </td>
        <td width=\"645\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
                <p><span style=\"font-size:10pt;\"><font face=\"Tahoma\"><input type=\"text\" name=\"chatname\" maxlength=\"20\" size=\"53\" value=\"$chatname\"></font></span></p>
        </td>
    </tr>
    <tr>
        <td width=\"152\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
            <p align=\"right\"><span style=\"font-size:10pt;\"><font face=\"Tahoma\">Teamname:
            &nbsp;</font></span></p>
        </td>
        <td width=\"645\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
                <p><span style=\"font-size:10pt;\"><font face=\"Tahoma\"><input type=\"text\" name=\"teamname\" maxlength=\"20\" size=\"53\" value=\"$teamname\"></font></span></p>
        </td>
    </tr>
    <tr>
        <td width=\"152\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
            <p align=\"right\"><span style=\"font-size:10pt;\"><font face=\"Tahoma\">Url
                to the Chat-Dictionary:
            &nbsp;</font></span></p>
        </td>
        <td width=\"645\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
                <p><span style=\"font-size:10pt;\"><font face=\"Tahoma\"><input type=\"text\" name=\"weburl\" size=\"70\" value=\"$weburl\"></font></span></p>
        </td>
    </tr>
    <tr>
        <td width=\"152\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
            <p align=\"right\"><span style=\"font-size:10pt;\"><font face=\"Tahoma\">Url to your logo.: &nbsp;</font></span></p>
        </td>
        <td width=\"645\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
                <p><span style=\"font-size:10pt;\"><font face=\"Tahoma\"><input type=\"text\" name=\"logo\" size=\"53\" value=\"$logo\">&nbsp;</font></span></p>
        </td>
    </tr>
    <tr>
        <td width=\"152\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
            <p align=\"right\"><span style=\"font-size:10pt;\"><font face=\"Tahoma\">&nbsp;Your Homepage:
            &nbsp;</font></span></p>
        </td>
        <td width=\"645\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
                <p><span style=\"font-size:10pt;\"><font face=\"Tahoma\"><input type=\"text\" name=\"to_hp\" size=\"53\" value=\"$to_hp\">&nbsp;</font></span></p>
        </td>
    </tr>
    <tr>
        <td width=\"152\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
            <p align=\"right\"><span style=\"font-size:10pt;\"><font face=\"Tahoma\">Webmaster-eMail:
            &nbsp;</font></span></p>
        </td>
        <td width=\"645\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
                <p><span style=\"font-size:10pt;\"><font face=\"Tahoma\"><input type=\"text\" name=\"webmasteremail\" size=\"53\" value=\"$webmasteremail\">&nbsp;</font></span></p>
        </td>
    </tr>
</table>
<table align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"797\" bordercolordark=\"white\" bordercolorlight=\"black\">
    <tr>
        <td width=\"152\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
            <p align=\"right\"><span style=\"font-size:10pt;\"><font face=\"Tahoma\">&nbsp;Adminpassword:
            &nbsp;</font></span></p>
        </td>
        <td width=\"645\" valign=\"top\" height=\"10\" style=\"font-size:10;\">
                <p><span style=\"font-size:10pt;\"><font face=\"Tahoma\"><input type=\"text\" name=\"adminpw\" maxlength=\"15\" size=\"53\" value=\"$adminpw\">&nbsp;</font></span></p>
        </td>
    </tr>
</table>
    <p align=\"center\"><span style=\"font-size:10pt;\"><font face=\"Tahoma\"><br><input type=\"submit\" name=\"absenden\" value=\"Save\">&nbsp;<input type=\"reset\" name=\"kill\" value=\"Reset\"></font></span></p>
</form>
<br><br><br><p align=\"center\"><span style=\"font-size:20pt;\"><font face=\"Tahoma\"><a href=\"../kick.php\">Kick User!!!</a></font></span></p>
</body>

</html>";}
else{
echo"<html>

<head>
<title>!! ERROR !!</title>
</head>

<body bgcolor=\"white\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p>&nbsp;</p>
<table width=\"982\" cellpadding=\"0\" cellspacing=\"0\">
    <tr>
        <td width=\"246\" bgcolor=\"red\" height=\"100\">
            <p>&nbsp;</p>
        </td>
        <td width=\"487\" height=\"100\">
            <p>&nbsp;</p>
        </td>
        <td width=\"246\" bgcolor=\"red\" height=\"100\">
            <p>&nbsp;</p>
        </td>
    </tr>
    <tr>
        <td width=\"246\" height=\"100\">
            <p>&nbsp;</p>
        </td>
        <td width=\"487\" height=\"100\">
            <p align=\"center\"><b><u><font color=\"red\"><span style=\"font-size:24pt;\"><blink>Wrong password!!!</blink></span></font></u></b></p>
        </td>
        <td width=\"246\" height=\"100\">
            <p>&nbsp;</p>
        </td>
    </tr>
    <tr>
        <td width=\"246\" height=\"100\" bgcolor=\"red\">
            <p>&nbsp;</p>
        </td>
        <td width=\"487\" height=\"100\">
            <p>&nbsp;</p>
        </td>
        <td width=\"246\" height=\"100\" bgcolor=\"red\">
            <p>&nbsp;</p>
        </td>
    </tr>
</table>
<p>&nbsp;</p>
</body>

</html>";
}
}
if($action == "speichern"){
$datei = fopen("../einstellungen.php", "w");
fwrite($datei, "<?");
fwrite($datei, "$"."chatname = \"$chatname\";");
fwrite($datei, "$"."teamname = \"$teamname\";");
fwrite($datei, "$"."weburl = \"$weburl\";");
fwrite($datei, "$"."logo = \"$logo\";");
fwrite($datei, "$"."webmasteremail = \"$webmasteremail\";");
fwrite($datei, "$"."adminpw = \"$adminpw\";");
fwrite($datei, "$"."to_hp = \"$to_hp\";");
fwrite($datei, "?>");
fclose($datei);
echo "All changes saved!<br><a href=\"../login.php\">Chat-Login</a><br><a href=\"admin.php?action=go\">Administration</a>";
}
?>