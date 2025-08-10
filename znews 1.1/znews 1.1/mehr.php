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

background-color : #FFFFFF;

color : #3399FF; 

font-size : 12px; 

color : #3399FF;}

</style>

<body  bgcolor="#4682B4" link="#000000" alink="#000000" vlink="#000000"><font face="Verdana" size="2">

<?php include("./eint/$id.php"); ?>

<p align="center"><br>

<table width="600" bgcolor="#3399FF" bordercolor="#000000" cellspacing="0" cellpadding="0">

<tr><td width="100%" bgcolor="#3399FF" border="1" cellspacing="0" cellpadding="3"><font face="Verdana" size="2"><b>News</b></td><td width="100%" border="1" cellspacing="0" cellpadding="3"><font face="Verdana" size="2"><a href="index.php"><b>Zurück</b></a></td></tr></table><br>

<table width="600"  bgcolor="#3399FF" border="1" bordercolor="#000000" cellspacing="0" cellpadding="1">

<font face="Verdana" size="2"><tr><td width="100%" cellspacing="0" cellpadding="3"><font face="Verdana" size="2"><a href="mailto:<? echo $mail; ?>"><? echo $name; ?></a></td></tr><tr><td width="100%" cellspacing="0" cellpadding="3"><font face="Verdana" size="2"><? echo $betr; ?>&nbsp|&nbsp <? echo $date; ?></td></tr><tr><td width="100%" cellspacing="0" cellpadding="3"><font face="Verdana" size="2"><? echo $text; ?><br><br><? echo $text2; ?></td></tr></table><br>

</table><br><br><br>
<? include("./copy.php"); ?>

</center>

</body>

</html>

