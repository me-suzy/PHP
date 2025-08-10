<?
$go=mkdir("user", 0777);
$go.=mkdir("online", 0777);
$go.=mkdir("verbannt", 0777);
$go.=chmod("messages.htm", 0777);
if ($go){
echo "<html>

<head>
<title>OK</title>
</head>

<body bgcolor=\"black\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\" onLoad=\"setTimeout('wl()',500);\">
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\"><font face=\"Tahoma\" color=\"red\"><span style=\"font-size:28pt;\"><b>Done!!</b></span></font></p>
<p align=\"center\"><font face=\"Tahoma\" color=\"red\"><span style=\"font-size:24pt;\"><b>Before you can use the Chat, please go to the Admin! Please wait! . . .</b></span></font></p>
<script language=\"javascript\" type=\"text/javascript\">
function wl(){
var newpage = \"admin/admin.php?action=go\"
location = newpage}
</script>
<p align=\"center\"><font face=\"Tahoma\" color=\"red\"><span style=\"font-size:24pt;\"><b>ADMINISTRATION
<a href=\"admin/admin.php?action=go\">&gt;&gt;</a></b></span></font></p>
<p align=\"center\">&nbsp;</p>
</body>

</html>";}
else{
echo "<html>

<head>
<title>ERROR</title>
</head>

<body bgcolor=\"black\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\">&nbsp;&nbsp;</p>
<p align=\"center\"><font face=\"Tahoma\" color=\"red\"><span style=\"font-size:48pt;\"><b>ERROR!</b></span><span style=\"font-size:28pt;\"><b><br>Please make three folders by yourself with the names \"online\", \"verbannt\" and \"user\". Please set the CHMODS of all these folders and the \"messages.htm\" to \"777\".
</body>

</html>";}
?>