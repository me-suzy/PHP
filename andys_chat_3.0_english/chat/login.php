<html>

<head>
<title><?php require("einstellungen.php"); echo "Willkommen im $chatname" ?></title>
</head>
<body bgcolor="white" text="black" link="blue" vlink="purple" alink="red">
<p align="center"><img src="<? echo"$logo"; ?>" border="0"></p>
<p align="center"><font face="Verdana"><span style="font-size:10pt;">Welcome to the <? echo"$chatname"; ?> !!!!</span></font></p>
<p align="center"><font face="Verdana"><span style="font-size:10pt;">&nbsp;</span></font></p>
<form name="form1" action="index.php" method="post">
<table align="center" cellpadding="0" cellspacing="0" width="262">
    <tr>
        <td width="73" valign="top">
            <p><font face="Verdana"><span style="font-size:10pt;">Username:</span></font></p>
        </td>
        <td width="189" valign="top">
                <p><font face="Verdana"><span style="font-size:10pt;"><input type="text" name="chatuser" maxlength="15"></span></font></p>

        </td>
    </tr>
    <tr>
        <td width="73" valign="top">
            <p><font face="Verdana"><span style="font-size:10pt;">Password:</span></font></p>
        </td>
        <td width="189" valign="top">

                <p><font face="Verdana"><span style="font-size:10pt;"><input type="password" name="passwortlogin" maxlength="10"></span></font></p>


        </td>
    </tr>
</table>
    <p align="center"><font face="Verdana"><span style="font-size:10pt;"><input type="submit" name="login" value="LOGIN"></span></font></form><ul>
    <font face="Verdana"><span style="font-size:10pt;">&nbsp;</span></font>
    <table align="center" cellpadding="0" cellspacing="0" width="928">
        <tr>
            <td width="441"><ul>
<table align="center" cellspacing="0" width="304" border="4" bordercolordark="black" bordercolorlight="black">
        <tr>
            <td width="292" height="18">
                <p><font face="Verdana"><span style="font-size:10pt;">&nbsp;&nbsp;New here??? &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Registrieren <a href="register.htm">&gt;&gt;</a></span></font></p>
            </td>
        </tr>
        <tr>
            <td width="292">
                <p><font face="Verdana"><span style="font-size:10pt;">&nbsp;&nbsp;Forget your password? &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GO
                                <a href="forget.php?go=1">&gt;&gt;</a></span></font></p>
            </td>
        </tr>
        <tr>
            <td width="292">
                <p><font face="Verdana"><span style="font-size:10pt;">&nbsp;&nbsp;Delete Account &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GO
                                <a href="delete.php?schritt=1">&gt;&gt;</a></span></font></p>
            </td>
        </tr>
        <tr>
            <td width="292">
                <p><font face="Verdana"><span style="font-size:10pt;">&nbsp;&nbsp;Administration
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;GO
                                <a href="admin/admin.php?action=go">&gt;&gt;</a></span></font></p>
            </td>
        </tr>
    </table>
</ul>
            </td>
            <td width="487">
               <P align=center><font face="Verdana"><span style="font-size:10pt;">Who is online?<BR><IFRAME
style="BORDER-RIGHT: #cecece 1px dashed; BORDER-TOP: #cecece 1px dashed; BORDER-LEFT: #cecece 1px dashed; BORDER-BOTTOM: #cecece 1px dashed"
name=xbox_iframe marginWidth=3 marginHeight=3
src="aktuell.php" frameBorder=5
width=300
height=250>
Sorry, your Browser doesn't support iFrames ...
</IFRAME></span></font></P>
            </td>
        </tr>
    </table>
</ul>
<p align="center"><font face="Verdana"><span style="font-size:10pt;">&nbsp;</span></font></p>
</body>

</html>