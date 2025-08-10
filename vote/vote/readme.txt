Hi,
Um Vote 1.2 auf ihren Server zu Laden, müssen Sie einfach alle Hochladen und den Ordner Account auf CHMOD 777 setzen. 
Dann unter data die data.php öffnen und das $pw (Passwort) und $php (PHP Version mit Punkt! Also, .php4). Dann edit.php & vote.php auf die
PHP Version anpassen (Dateiname ändern).

Um eine neue Umfrage zu machen öffnen Sie die edit.php und gehen auf "Neue Umfrage". Passwort ist das Passwort was Sie zu editieren der 
Umfrage brauchen. E-Pass ist das Passwort was Sie unter der data.php eingegeben haben (So kann nicht jeder Umfragen erstellen).  Um es
möglich zu machen das jeder eine Umfrage machen kann, einfach echo "E-Pass:<input type=\"Password\" name=\"epass\" value=\"\" size=\"20\" maxlength=\"\"><br>";
durch echo "<input type=\"hidden\" name=\"epass\" value=\"$pw\">"; in der edit.php ersetzen.

Die Farben der Umfrage werden in der URL bestimmt, so ist es möglich das eine Umfrage auf mehren Seiten erscheinen kann. Die Farben müssen ohne
# in die URL eingegeben werden. Werden keine Farben eingegeben werden die Standartfarben genutzt.

Beispiel URL
http://www.xyz.de/vote/vote.php?wahl=1&umfrage=(Name der Umfrage)&show=0&vote=0&hg=(Hintergrundfarbe)&tab=(Tabellenfarbe)&link=(Linkfarbe)&text=(Textfarbe)

Viel Spaß wünscht das PHP-is-easy.de! Team

Bei Fragen E-Mail an: support@php-is-easy.de