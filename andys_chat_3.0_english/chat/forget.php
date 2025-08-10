<?
if($go == "1"){
echo "<html>

<head>
<title>FORGET MY PASSWORD</title>
</head>

<body bgcolor=\"black\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p align=\"center\">&nbsp;</p>
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\"><b><u><span style=\"font-size:20pt;\"><font face=\"Tahoma\" color=\"white\"><i>FORGET
MY PASSWORD </i>-function</font></span></u></b></p>
<p align=\"center\"><font face=\"Tahoma\" color=\"white\">&nbsp;</font></p>
<p align=\"center\"><span style=\"font-size:16pt;\"><i><b><font face=\"Tahoma\" color=\"white\">Did you have forget your password?</font></b></i></span></p>
<p align=\"center\"><b><font face=\"Tahoma\" color=\"white\">No problem! Just enter your nickname and you're going to get an eMail with all the facts.<br></font></b></p>
<form name=\"form1\" action=\"$PHP_SELF?go=2\" method=\"POST\">
    <p align=\"center\"><b><font face=\"Tahoma\" color=\"white\">--&gt; <input type=\"text\" name=\"username\" value=\"Please enter your Nickname\" size=\"30\" style=\"color:white; text-align:center; background-color:black;\">
    &lt;--</font></b></p>
    <p align=\"center\"><b><font face=\"Tahoma\"><input type=\"submit\" name=\"formbutton1\" value=\"Send eMail!\" style=\"color:white; background-color:black; border-right-color:white; border-left-color:white;\"></font></b></p>
</form>
<p align=\"center\">&nbsp;</p>
</body>

</html>";}
if($go == "2"){
if (file_exists("user/$username.php")){
require("user/$username.daten.php");
require("einstellungen.php");
$email1 = "$email";
$b = "FORGET MY PASSWORD";
$text = "Your facts:\n\nUsername: $username\nPassword : $pw\n\nCome back: $weburl/login.php\nHave big fun!\n\nChat powered by:\nAndys Chat 3.0\nhttp://www.andys-chat.ch.vu\nAndrew B. (http://www.kunst-stoff.ch.vu)";
$x = "FROM: $webmasteremail";
mail($email1, $b, $text, $x);
echo "<html>

<head>
<title>Mail sended!</title>
</head>

<body bgcolor=\"black\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\"><font face=\"Tahoma\" color=\"white\"><span style=\"font-size:16pt;\">You are going to receive an eMail! PLease check your eMail account!</span></font></p>
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\"><font face=\"Tahoma\" color=\"white\"><span style=\"font-size:16pt;\"><a href=\"$weburl/login.php\"><span style=\"font-size:16pt;\"><font face=\"Tahoma\" color=\"white\">LOGIN</a></span></font></p>
</body>

</html>";
}}
else{
echo "<html>

<head>
<title>ERROR</title>
</head>

<body bgcolor=\"black\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\"><font color=\"white\">&nbsp;</font></p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\">&nbsp;</p>
<p align=\"center\"><font face=\"Tahoma\" color=\"red\"><span style=\"font-size:48pt;\">Username isn't registred!</span></font></p>
<p align=\"center\">&nbsp;</p>
</body>

</html>";
}
?>