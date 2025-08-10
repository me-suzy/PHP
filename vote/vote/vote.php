<?
if(!isset($hg)) {
$hg = "FFFFFF"; }
if(!isset($text)) {
$text = "000000"; }
if(!isset($link)) {
$link = "0000FF"; }
if(!isset($tab)) {
$tab = "4199FA"; }

echo "<body text=\"#$text\" bgcolor=\"#$hg\" link=\"#$link\" alink=\"#$link\" vlink=\"#$link\">";

if(!isset($umfrage)) {
echo "Keine Umfrage!";
exit;}

if (!file_exists("account/$umfrage.php")) {
echo "Umfrage wurde nicht gefunden!";
exit;
}

$loadumfrage = "account/$umfrage.php";

require $loadumfrage;

if(!isset($vote)) {
$vote = "0"; }
if(!isset($show)) {
$show = "0"; }
if(!isset($wahl)) {
$wahl = "0"; }

echo "<h1><center>Vote it!</center></h1>";
if($vote == "1") {
$ip = $REMOTE_ADDR;
$zeit = time();

if (!file_exists("account/$umfrage.ip.log")) {
$datei = fopen("$account/$umfrage.ip.log", "w+");
fputs($datei, "");
fclose($datei);
}

$ipdata = file("account/$umfrage.ip.log");

$i = "0";
$merke = "1";
$eintragen = "0";

while($i < count($ipdata)) {

$dat = explode("|", $ipdata[$i]);

if($dat[0] == $ip and $zeit <= $dat[1] + 7200) {
$zählen = "0";
$ipa[$i] = "$ip|";
$zeita[$i] = "$dat[1]|\n";
$merke = "0";
}
if($zeit > $dat[1] + 7200) {
$ipa[$i] = "";
$zeita[$i] = "";
}
$i++;
}

$zeitan = "";
$ipan = "";
$counteran = "";

if($merke == "1") {
$ipan = "$ip|";
$zeitan = "$zeit|\n";
$eintragen = "1";
}

$t = "0";

$datei = fopen("account/$umfrage.ip.log", "w+");
while($t < $i) {
fputs($datei, "$ipa[$t]$zeita[$t]");
$t++;
}
if($zeitan != "" and $ipan != "") {
fputs($datei, "$ipan$zeitan");
}
fclose($datei);

if($eintragen == "1") {
$i = "1";
while ($i <= $nr) {

if($votef == $name[$i]) {
$wert[$i]++;
}
$i++;}
$i = "1";


$zahl = fopen("account/$umfrage.php","w+");
while ($i <= $nr) {
if($i == "1") {
$vorz = "<? \n";
}
else {
$vorz = "";
}
fputs($zahl,"$vorz\$wert[$i] = \"$wert[$i]\";\n\$name[$i] = \"$name[$i]\";\n");
$i++;
}
fclose($zahl);
$zahl = fopen("account/$umfrage.php","a+");
fputs($zahl,"\$nr = \"$nr\";\n\$frage = \"$frage\";\n\$pw = \"$pw\";\n?>");
fclose($zahl);
echo "<center>Danke für deine Stimme";
echo "<br><br>";
echo "<a href=\"$PHP_SELF?show=1&umfrage=$umfrage&vote=0&wahl=0&hg=$hg&tab=$tab&link=$link&text=$text\">Zum Ergebnis</a></center>"; }
else {
echo "<center>Ihre Stimme wurde nicht gezählt";
echo "<br><br>";
echo "<a href=\"$PHP_SELF?show=1&umfrage=$umfrage&vote=0&wahl=0&hg=$hg&tab=$tab&link=$link&text=$text\">Zum Ergebnis</a></center>";
}
exit;}

if($show == "1") {
echo "<div align=\"center\">";
echo "<table border=\"0\" width=\"50%\"><tr>";
echo "<td width=\"100%\" bgcolor=\"#$tab\"><small><font face=\"Arial\">";
echo "<center>$frage</center></font></small></td>";
echo "</tr><tr>";
echo "<td width=\"100%\">";
echo "<font face=\"Arial\"><small>";
$ges = "0";
$i = "1";
while ($i <= $nr) {
$ges = $ges + $wert[$i];
$i++;
}
$i = "1";
while ($i <= $nr) {
if($wert[$i] > 0) {
$pro = $wert[$i] * 100 / $ges;
$pro = round ($pro,2); }
else {
$pro = "0";
}
echo "<table border=\"0\" width=\"100%\"><tr>";
echo "<td width=\"40%\"><small><font face=\"Arial\">$name[$i]</td>";
echo "<td width=\"10%\"><small><font face=\"Arial\">$wert[$i]</td>";
echo "<td width=\"50%\"><center><small><font face=\"Arial\">$pro%</center></td>";
echo "</tr></table>";
$i++;
}
echo "<table border=\"0\" width=\"100%\" bgcolor=\"#$tab\"><tr>";
echo "<td width=\"40%\"><small><font face=\"Arial\">Gesamt</td>";
echo "<td width=\"10%\"><small><font face=\"Arial\">$ges</td>";
echo "<td width=\"50%\"><center></center></td>";
echo "</tr></table>";

echo "</font></small></td></tr></table>";}

if($wahl == "1") {
echo "<div align=\"center\">";
echo "<table border=\"0\" width=\"50%\"><tr>";
echo "<td width=\"100%\" bgcolor=\"#$tab\"><small><font face=\"Arial\">";
echo "<center>$frage</center></font></small></td>";
echo "</tr><tr>";
echo "<td width=\"100%\">";
echo "<font face=\"Arial\"><small>";
$ges = "0";
$i = "1";
while ($i <= $nr) {
echo "<table border=\"0\" width=\"100%\"><tr>";
echo "<td width=\"50%\"><center><small><font face=\"Arial\"><a href=\"$PHP_SELF?vote=1&umfrage=$umfrage&hg=$hg&votef=$name[$i]&hg=$hg&tab=$tab&link=$link&text=$text\">$name[$i]</a></center></td>";
echo "</tr></table>";
$i++;
}
echo "<table border=\"0\" width=\"100%\" bgcolor=\"#$tab\"><tr>";
echo "<td width=\"50%\"><center><small><font face=\"Arial\"><a href=\"$PHP_SELF?show=1&umfrage=$umfrage&vote=0&wahl=0&hg=$hg&tab=$tab&link=$link&text=$text\">Ergebnis</a></center></td>";
echo "</tr></table>";
echo "</font></small></td></tr></table>";exit;
}

echo "<br><br><a href=\"$PHP_SELF?wahl=1&umfrage=$umfrage&show=0&vote=0&hg=$hg&tab=$tab&link=$link&text=$text\">Vote</a></div>";
echo "</body>";
?>