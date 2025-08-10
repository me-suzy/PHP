<html>

<head>

<title>ZNews 1.1</title>

</head>

<body  bgcolor="#4682B4" alink="#000000" link="#000000" vlink="#00000">

<font face="Verdana" size="2"><p align="center">

<?php

$max=19;

$mname="";

for($i=0;$i<= $max; $i++)

{ $mname .=chr(rand(48,57));}

$text=wordwrap($text,200);

$text=str_replace("\n","<br>\n",$text);

$fp = fopen("../eint/$mname.php", "w");

fputs($fp,"<? $");

fputs($fp,"betr=\"$betr\";\n");

fputs($fp,"?>");

fputs($fp,"<? $");

fputs($fp,"date=\"$date\";\n");

fputs($fp,"?>");

fputs($fp,"<? $");

fputs($fp,"name=\"$name\";\n");

fputs($fp,"?>");

fputs($fp,"<? $");

fputs($fp,"mail=\"$mail\";\n");

fputs($fp,"?>");

fputs($fp,"<? $");

fputs($fp,"text=\"$text\";\n");

fputs($fp,"?>");

fputs($fp,"<? $");

fputs($fp,"text2=\"$text2\";\n");

fputs($fp,"?>");

fputs($fp,"<? $");

fputs($fp,"mname=\"$mname\";\n");

fputs($fp,"?>");

fclose($fp);

?><br><br>

News eingetragen!<br><br>

</body>

</html>

