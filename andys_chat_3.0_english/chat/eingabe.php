<html><head>
<script>
function sf(){document.eingabe.message.focus();}
function smile1(){
absatz=document.eingabe.message.value=document.eingabe.message.value+(" :-) ");
}
function smile2(){
absatz=document.eingabe.message.value=document.eingabe.message.value+(" :-( ");
}
function smile3(){
absatz=document.eingabe.message.value=document.eingabe.message.value+(" :-| ");
}
function smile4(){
absatz=document.eingabe.message.value=document.eingabe.message.value+(" ;-) ");
}
function smile5(){
absatz=document.eingabe.message.value=document.eingabe.message.value+(" :-x ");
}
function smile6(){
absatz=document.eingabe.message.value=document.eingabe.message.value+(" :-O ");
}
function blau(){
document.eingabe.farbe.value='#0000FF';
}
function rot(){
document.eingabe.farbe.value='#FF0000';
}
function gruen(){
document.eingabe.farbe.value='#008000';
}
function gelb(){
document.eingabe.farbe.value='#FFFF00';
}
function schwarz(){
document.eingabe.farbe.value='#000000';
}
function seticon(Image) {

  document.ticon.src=Image;
    }
</script>
<SCRIPT LANGUAGE="JavaScript">
function blockError(){return true;}
window.onerror = blockError;
</script>
</head>

<body bgcolor=red text=#000000>
<?
require("einstellungen.php");
if (file_exists("online/$chatuser.txt")){
echo "<font face=\"Tahoma\">&nbsp;&nbsp;&nbsp;Your Nickname: <b>&quot;$chatuser&quot;</b>
<br>
</font><form name=eingabe method=post action=\"chat.php\">
<font face=\"Tahoma\"><input type=hidden name=chatuser value=\"$chatuser\">
 &nbsp;&nbsp;&nbsp;<font face=\"Tahoma\">Message: <input type=text name=message size=100 maxlength=\"100\"><script>
document.eingabe.message.focus();
</script></font>
<input type=submit value=Send> <input type=reset value=Reset><span style=\"font-size:10pt;\"><font face=\"Tahoma\"><b>&nbsp;&gt; <a href=\"bye.php?username=$chatuser\" target=\"_parent\">LOG-OUT</a> &lt;</b></font></span>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Smilies: <a href=\"javascript:smile1();\"><img src=\"smiles/1.gif\" width=\"16\" height=\"16\" border=\"0\"></a>
    &nbsp;<a href=\"javascript:smile2();\"><img src=\"smiles/2.gif\" width=\"15\" height=\"15\" border=\"0\"></a> &nbsp;<a href=\"javascript:smile3();\"><img src=\"smiles/3.gif\" width=\"15\" height=\"15\" border=\"0\"></a>
    &nbsp;<a href=\"javascript:smile4();\"><img src=\"smiles/4.gif\" width=\"15\" height=\"15\" border=\"0\"></a> &nbsp;<a href=\"javascript:smile5();\"><img src=\"smiles/5.gif\" width=\"15\" height=\"18\" border=\"0\"></a>
    &nbsp;<a href=\"javascript:smile6();\"><img src=\"smiles/6.gif\" width=\"15\" height=\"15\" border=\"0\"></font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href=\"javascript:schwarz();javascript:seticon('farben/schwarz.bmp')\"><i><font face=\"Tahoma\" color=\"black\"><b>Black</b></font></i></a><i><font face=\"Tahoma\" color=\"black\"><b> </b></font></i><a href=\"javascript:blau();javascript:seticon('farben/blau.bmp')\"><i><font face=\"Tahoma\" color=\"blue\"><b>Blue</b></font></i></a><i><font face=\"Tahoma\" color=\"#CC0000\"><b> </b></font></i><a href=\"javascript:rot();javascript:seticon('farben/rot.bmp')\"><i><font face=\"Tahoma\" color=\"red\"><b>
    <span style=\"background-color:yellow;\">Red</span></b></font></i></a><i><font face=\"Tahoma\" color=\"red\"><b> </b></font></i><a href=\"javascript:gruen();javascript:seticon('farben/gruen.bmp')\"><i><font face=\"Tahoma\" color=\"green\"><b>Green</b></font></i></a><i><font face=\"Tahoma\" color=\"#CC0000\"><b>
    </b></font></i><a href=\"javascript:gelb();javascript:seticon('farben/gelb.bmp')\"><i><font face=\"Tahoma\" color=\"yellow\"><b>Yellow</b></font></i></a><i><font face=\"Tahoma\" color=\"yellow\"><b>
    &nbsp;</b></font><font face=\"Tahoma\" color=\"#3399CC\"><b>Selected:</b></font><font face=\"Tahoma\" color=\"#CC0000\"><b> <img name=\"ticon\" src=\"farben/schwarz.bmp\" border=0 width=\"14\" height=\"14\"></b></font></i><i><font face=\"Tahoma\" color=\"#CC0000\"><input type=\"hidden\" name=\"farbe\" size=\"9\" maxlength=\"7\" value=\"$farbe\"> &nbsp;</font></i></form>
<font face=\"Tahoma\"><b>&nbsp;&nbsp;&nbsp;<span style=\"font-size:10pt;\"><font face=\"Tahoma\">Chat powered by:
</font></span></b><a href=\"http://www.php-scripts.ch.vu\"

   target=\"neuesfenster\"

   onclick=\"window.open('','neuesfenster','top=50,screenX=50,left=100,screenY=100,height=600,width=800')\"><b><span style=\"font-size:10pt;\"><font face=\"Tahoma\">http://www.php-scripts.ch.vu</font></span></b></a><b><span style=\"font-size:10pt;\"><font face=\"Tahoma\">
| &nbsp;Andys Chat 3.0 &nbsp;| &nbsp;Andreas B.</font></font></span></b></font></p></body>
</html>";}
else{
echo"<html>

<head>
<title>Kicked!!</title>
</head>
<body bgcolor=\"#CC0000\" text=\"black\" link=\"blue\" vlink=\"purple\" alink=\"red\">
<table align=\"center\" border=\"5\" width=\"895\" cellspacing=\"0\" bgcolor=\"white\" bordercolordark=\"black\" bordercolorlight=\"black\">
    <tr>
        <td width=\"885\">
            <p align=\"center\"><font face=\"Tahoma\" color=\"#CC0000\"><span style=\"font-size:48pt;\">Kicked! Bye!</span></font></p>
        </td>
    </tr>
</table>
</body>

</html>";}
?>