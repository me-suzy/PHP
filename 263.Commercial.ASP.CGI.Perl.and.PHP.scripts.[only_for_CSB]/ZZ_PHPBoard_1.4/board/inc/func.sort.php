<?php
////////////////////////////////////// BEGIN Sortieren
// Alle Dateien im Verzeichnis werden eingelesen und nach Gliederungspunkten sortiert //

$handle= opendir ('./'.$cfg_msgdir);
$main = array();
$all = array();	
while (false !== ($file = readdir ($handle))) { // Alle Datein im Verzeichnis einlesesn bis false
	if (substr ($file, -4) == ".txt") { // Weiter nur wenn Datei mit .txt endet	
		$file = preg_replace("/.txt/","",$file); // .txt entfernen	
		$all[] = $file;
	} // end IF 
} // end While

function sorter($a,$b) {
	$a = explode(".",$a);
	$b = explode(".",$b);
	$i=0; 
	global $anz;
		while ($i <= $anz) {
			if(!$a[$i]) {
				$a[$i] = 0;
			}
			if(!$b[$i]) {
				$b[$i] = 0;
			}
			
			if ($a[$i] > $b[$i]) {
				return 1;
				break;
			} elseif ($a[$i] < $b[$i]) {
				return -1;
				break;
			} elseif ($a[$i] == $b[$i]) {
				$i++;
			}
		}
}

$anz = 1;
foreach($all as $num) { // Längsten Unterpunkt ermitteln (=höchste Anzahl an Trennpunkten)
	$num = explode(".",$num);
	$an = count($num);
	if ($an > $anz) {
		$anz = $an;
	}
}

usort($all, sorter);
////////////////////////////////////// ENDE Sortieren
?>