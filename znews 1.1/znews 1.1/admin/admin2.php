<head>

<style type="text/css">

table {

font-family : verdana; 

font-size : 12px; 

font-weight : bold;

text-align : left;

border : 1px solid #000000;}



input {

border : 1px solid #000000; 

background-color : #3399FF;

color : #3399FF; 

font-size : 12px; 

color : #3399FF;}

</style>

<body  bgcolor="#4682B4" link="#000000" alink="#000000" vlink="#000000"><font face="Verdana" size="2">

<p align="center"><br>

<?

$verz = dir("../eint/"); 

while($entry=$verz->read()) {

if ($entry==".") {} 

elseif ($entry==".."){} 

else {

require("../eint/$entry");

?>

<table width="600" bgcolor="#3399FF" border="1" bordercolor="#000000" cellspacing="0" cellpadding="0">

<font face="Verdana" size="2"><tr><td width="100%" cellspacing="0" cellpadding="3"><a href="mailto:<? echo $mail; ?>"><? echo $name; ?></a></td></tr><tr><td width="100%" cellspacing="0" cellpadding="3"><? echo $betr; ?>&nbsp|&nbsp <? echo $date; ?></td></tr><tr><td width="100%" cellspacing="0" cellpadding="3"><? echo $text; ?><br><br><? echo $text2; ?></td></tr><tr><td width="100%" cellspacing="0" cellpadding="3"><a href="loeschen.php?id=<? echo $mname; ?>&user=<? echo('$user'); ?>&passw=$passw">Artikel löschen</a></td></tr></table><br>

<? } } ?>

</table>

</center>

</body>

</html>

