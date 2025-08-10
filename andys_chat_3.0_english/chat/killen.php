<?
require("einstellungen.php");
if ("$adminpasswort" == "$adminpw"){
if (file_exists("online/$username.txt")){
$loeschen=unlink("online/$username.txt");
if($loeschen){
echo"<html>

<head>
<title>$username kicked!</title>
</head>

<body bgcolor=\"#CC0000\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p align=\"center\">&nbsp;</p>
<p align=\"center\"><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:28pt;\">&nbsp;</span></font></p>
<table border=\"10\" width=\"718\" bgcolor=\"white\" align=\"center\" cellspacing=\"0\" bordercolordark=\"black\" bordercolorlight=\"black\">
    <tr>
        <td width=\"694\">
            <p align=\"center\"><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:28pt;\">$username
            kicked! </span></font></p>
            <p align=\"center\"><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:20pt;\">Bann $username </span></font><a href=\"$PHP_SELF?killen=2&username=$username&adminpasswort=$adminpasswort&adminpw=$adminpw\"><span style=\"font-size:20pt;\"><font color=\"#CC0000\" face=\"Verdana\">&gt;&gt;</font></span></a></p>
            <p align=\"center\"><a href=\"javascript:history.back();\"><span style=\"font-size:10pt;\"><font color=\"#CC0000\" face=\"Verdana\">&lt;&lt;</font></span></a><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:10pt;\">
            Kick once more!<br></span></font><a href=\"admin.php?action=go\"><span style=\"font-size:10pt;\"><font color=\"#CC0000\" face=\"Verdana\">&lt;&lt;</font></span></a><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:10pt;\">
            Administration<br></span></font><a href=\"login.php\"><span style=\"font-size:10pt;\"><font color=\"#CC0000\" face=\"Verdana\">&lt;&lt;</font></span></a><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:10pt;\">
            Login</span></font></p>
        </td>
    </tr>
</table>
</body>

</html>";
}else{
echo"<html>

<head>
<title>Error!</title>
</head>

<body bgcolor=\"#CC0000\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p align=\"center\">&nbsp;</p>
<p align=\"center\"><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:28pt;\">&nbsp;</span></font></p>
<table border=\"10\" width=\"718\" bgcolor=\"white\" align=\"center\" cellspacing=\"0\" bordercolordark=\"black\" bordercolorlight=\"black\">
    <tr>
        <td width=\"694\">
            <p align=\"center\"><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:28pt;\">$username
            not kicked!</span></font></p>
            <p align=\"center\"><a href=\"javascript:history.back();\"><span style=\"font-size:10pt;\"><font color=\"#CC0000\" face=\"Verdana\">&lt;&lt;</font></span></a><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:10pt;\">
            Next try<br></span></font><a href=\"admin.php?action=go\"><span style=\"font-size:10pt;\"><font color=\"#CC0000\" face=\"Verdana\">&lt;&lt;</font></span></a><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:10pt;\">
            Administration<br></span></font><a href=\"login.php\"><span style=\"font-size:10pt;\"><font color=\"#CC0000\" face=\"Verdana\">&lt;&lt;</font></span></a><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:10pt;\">
            Login</span></font></p>
        </td>
    </tr>
</table>
</body>

</html>";}}else{
echo"<html>

<head>
<title>$username isn't logged in!</title>
</head>

<body bgcolor=\"#CC0000\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p align=\"center\">&nbsp;</p>
<p align=\"center\"><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:28pt;\">&nbsp;</span></font></p>
<table border=\"10\" width=\"718\" bgcolor=\"white\" align=\"center\" cellspacing=\"0\" bordercolordark=\"black\" bordercolorlight=\"black\">
    <tr>
        <td width=\"694\">
            <p align=\"center\"><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:28pt;\">$username isn't in the Chat at the moment!!</span></font></p>
            <p align=\"center\"><a href=\"javascript:history.back();\"><span style=\"font-size:10pt;\"><font color=\"#CC0000\" face=\"Verdana\">&lt;&lt;</font></span></a><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:10pt;\">
            Next try<br></span></font><a href=\"admin.php?action=go\"><span style=\"font-size:10pt;\"><font color=\"#CC0000\" face=\"Verdana\">&lt;&lt;</font></span></a><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:10pt;\">
            Administration<br></span></font><a href=\"login.php\"><span style=\"font-size:10pt;\"><font color=\"#CC0000\" face=\"Verdana\">&lt;&lt;</font></span></a><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:10pt;\">
            Login</span></font></p>
        </td>
    </tr>
</table>
</body>

</html>";}
if($killen == "2"){
$datei = fopen("verbannt/$username.txt", "w");
fputs($datei, "0");
fclose($datei);
echo"<html>

<head>
<title>$username banned</title>
</head>

<body bgcolor=\"#CC0000\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p align=\"center\">&nbsp;</p>
<p align=\"center\"><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:28pt;\">&nbsp;</span></font></p>
<table border=\"10\" width=\"718\" bgcolor=\"white\" align=\"center\" cellspacing=\"0\" bordercolordark=\"black\" bordercolorlight=\"black\">
    <tr>
        <td width=\"694\">
            <p align=\"center\"><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:28pt;\">$username is banned now!</span></font></p>
            <p align=\"center\"><a href=\"javascript:history.back();\"><span style=\"font-size:10pt;\"><font color=\"#CC0000\" face=\"Verdana\">&lt;&lt;</font></span></a><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:10pt;\">
            Kick once more!<br></span></font><a href=\"admin/admin.php?action=go\"><span style=\"font-size:10pt;\"><font color=\"#CC0000\" face=\"Verdana\">&lt;&lt;</font></span></a><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:10pt;\">
            Administration<br></span></font><a href=\"login.php\"><span style=\"font-size:10pt;\"><font color=\"#CC0000\" face=\"Verdana\">&lt;&lt;</font></span></a><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:10pt;\">
            Login</span></font></p>
        </td>
    </tr>
</table>
</body>

</html>";}}
else{
echo"<html>

<head>
<title>ERROR</title>
</head>

<body bgcolor=\"#CC0000\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p align=\"center\">&nbsp;</p>
<p align=\"center\"><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:28pt;\">&nbsp;</span></font></p>
<table border=\"10\" width=\"718\" bgcolor=\"white\" align=\"center\" cellspacing=\"0\" bordercolordark=\"black\" bordercolorlight=\"black\">
    <tr>
        <td width=\"694\">
            <p align=\"center\"><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:28pt;\">Wrong Admin-Password!</span></font></p>
            <p align=\"center\"><a href=\"javascript:history.back();\"><span style=\"font-size:10pt;\"><font color=\"#CC0000\" face=\"Verdana\">&lt;&lt;</font></span></a><font color=\"#CC0000\" face=\"Verdana\"><span style=\"font-size:10pt;\">
            Try once more!<br>&nbsp;</span></font></p>
        </td>
    </tr>
</table>
</body>

</html>";}
?>