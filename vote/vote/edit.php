<?
$data = "data/data.php";

require $data;

echo "<body text=\"#000000\" bgcolor=\"#FFFFFF\" link=\"#0000FF\" alink=\"#0000FF\" vlink=\"#0000FF\">";
if(!isset($wahl)) {
$wahl = "";
}

if($wahl == "") {
echo "<center><a href=\"$PHP_SELF?wahl=nf\">Neue Umfrage</a><br>\n";
echo "<a href=\"$PHP_SELF?wahl=ef\">Edit Umfrage</a></center><br><br><br>\n";
echo "<center><a href=\"http://www.multi-counter.de\">";
echo "<img src=\"http://www.multi-counter.de/banner.gif\" border=\"0\"><br>Der Counter für alle!</a></center>";
exit;}

if($wahl == "nf") {
echo "<form action=\"$PHP_SELF?wahl=nf1\" method=\"post\" target=\"\">";
echo "Name der Umfrage:<input type=\"Text\" name=\"umfname\" value=\"\" size=\"20\" maxlength=\"\"><br>";
echo "Frage:<input type=\"Text\" name=\"umffrage\" value=\"\" size=\"20\" maxlength=\"\"><br>";
echo "Antworten:<input type=\"Text\" name=\"umfaw\" value=\"2\" size=\"20\" maxlength=\"\"><br>";
echo "Passwort:<input type=\"Password\" name=\"umflog\" value=\"\" size=\"20\" maxlength=\"\"><br>";
echo "E-Pass:<input type=\"Password\" name=\"epass\" value=\"\" size=\"20\" maxlength=\"\"><br>";
echo "<input type=\"Submit\" name=\"senden\" value=\"OK\"></form>";
}

if($wahl == "nf1") {
if($umfname == "" or $umffrage == "" or $umflog == "" or $epass == "") {
echo "Es wurden nicht alle Felder ausgefüllt!";
exit;}
if($epass != $pw) {
echo "Falsches Passwort!";
exit;}
if (file_exists("account/$umfname.php")) {
echo "Der Name ist schon weg :)";
exit;
}

echo "<b>Name:</b> $umfname<br>";
echo "<b>Frage:</b> $umffrage<br><br>";
$i = "1";
echo "<form action=\"$PHP_SELF?wahl=nf2\" method=\"post\" target=\"\">";
while ($i <= $umfaw) {
echo "<b>Antwort $i: <input type=\"Text\" name=\"aw[$i]\" value=\"\" size=\"30\" maxlength=\"\"></b><br>";
$i++;
}
echo "<input type=\"hidden\" name=\"umfname\" value=\"$umfname\">";
echo "<input type=\"hidden\" name=\"umffrage\" value=\"$umffrage\">";
echo "<input type=\"hidden\" name=\"umflog\" value=\"$umflog\">";
echo "<input type=\"hidden\" name=\"umfaw\" value=\"$umfaw\">";
echo "<br><input type=\"Submit\" name=\"senden\" value=\"OK\"></form>";
}

if($wahl == "nf2") {
$i = "1";


$zahl = fopen("account/$umfname.php","w+");
while ($i <= $umfaw) {
if($i == "1") {
$vorz = "<? \n";
}
else {
$vorz = "";
}
fputs($zahl,"$vorz\$wert[$i] = \"0\";\n\$name[$i] = \"$aw[$i]\";\n");
$i++;
}
fclose($zahl);
$zahl = fopen("account/$umfname.php","a+");
fputs($zahl,"\$nr = \"$umfaw\";\n\$frage = \"$umffrage\";\n\$pw = \"$umflog\";\n?>");
fclose($zahl);
echo "Fertig<br>";
echo "<a href=\"vote$php?wahl=1&umfrage=$umfname&show=0&vote=0\">Zur Umfrage</a>";}

if($wahl == "ef") {
echo "<form action=\"$PHP_SELF?wahl=ef1\" method=\"post\" target=\"\">";
echo "Name der Umfrage:<input type=\"Text\" name=\"umfname\" value=\"\" size=\"20\" maxlength=\"\"><br>";
echo "Passwort:<input type=\"Password\" name=\"umflog\" value=\"\" size=\"20\" maxlength=\"\"><br>";
echo "<input type=\"Submit\" name=\"senden\" value=\"OK\"></form>";
}

if($wahl == "ef1") {

if(!isset($umfname)) {
echo "Kein Umfrage Name!";
exit;
}
if (!file_exists("account/$umfname.php")) {
echo "Umfrage wurde nicht gefunden!";
exit;
}

$loadumfrage = "account/$umfname.php";

require $loadumfrage;

if($umflog != $pw) {
echo "Falsches Passwort!";
exit;
}

echo "<form action=\"$PHP_SELF?wahl=ef2\" method=\"post\" target=\"\">";
echo "<b>Frage: </b><input type=\"Text\" name=\"umffrage\" value=\"$frage\" size=\"20\" maxlength=\"\"><br><br>";
$i = "1";
while ($i <= $nr) {
echo "<b>Antwort $i: <input type=\"Text\" name=\"aw[$i]\" value=\"$name[$i]\" size=\"30\" maxlength=\"\"><input type=\"Text\" name=\"wert[$i]\" value=\"$wert[$i]\" size=\"3\" maxlength=\"\"></b><br>";
$i++;
}
echo "<br><b>Passwort:</b> <input type=\"Password\" name=\"umflog\" value=\"$pw\" size=\"20\" maxlength=\"\"><br>";
echo "<input type=\"hidden\" name=\"umfname\" value=\"$umfname\">";
echo "<input type=\"hidden\" name=\"nr\" value=\"$nr\">";
echo "<br><input type=\"Submit\" name=\"senden\" value=\"OK\"></form>";
echo "<form action=\"$PHP_SELF?wahl=ef3\" method=\"post\" target=\"\">";
echo "<br><input type=\"Submit\" name=\"senden\" value=\"Umfrage Löschen\">";
echo "<input type=\"hidden\" name=\"umfname\" value=\"$umfname\"></form>";
}

if($wahl == "ef3") {
unlink("account/$umfname.php");
echo "... und weg ist die Umfrage :)";
}

if($wahl == "ef2") {
$i = "1";


$zahl = fopen("account/$umfname.php","w+");
while ($i <= $nr) {
if($i == "1") {
$vorz = "<? \n";
}
else {
$vorz = "";
}
fputs($zahl,"$vorz\$wert[$i] = \"$wert[$i]\";\n\$name[$i] = \"$aw[$i]\";\n");
$i++;
}
fclose($zahl);
$zahl = fopen("account/$umfname.php","a+");
fputs($zahl,"\$nr = \"$nr\";\n\$frage = \"$umffrage\";\n\$pw = \"$umflog\";\n?>");
fclose($zahl);
echo "Fertig<br>";
echo "<a href=\"vote$php?wahl=1&umfrage=$umfname&show=0&vote=0\">Zur Umfrage</a>";
}
?>