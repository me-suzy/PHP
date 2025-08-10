<?php

include "conf/hitweb.conf";

$ficname = "index";

function genPage($CATEGORIES_ID)
{
  global $SITE, $ficname;

  $url = "http://".$SITE."/index.php?categories_parents_id=$CATEGORIES_ID&genpage=genpage";

  $page = fopen($url, "r");

  while(!feof($page)) {
    $line = fgets($page, 1024);
    $totline = $totline.$line;
  }
  
  //fclose($page);

  $totline2 = $totline;

  // le numéro des catégorie doivent être dans cette regex
  $totline2 = ereg_replace( "index.php\?categories_parents_id=([0-9]+)", $ficname."\\1.html", $totline );
  $totline2 = ereg_replace($ficname.".php", "index.html", $totline2);


  // cette variable contient la page semi-statique.
  // Son nom doit être index+$CATEGORIE_ID+".html"
  // echo $totline2;

  //Construction du nom du fichier
  $filename = $ficname.$CATEGORIES_ID.".html";
  
  $file = fopen($filename, "w");
  if (!$file) {
    echo "<p>Unable to open remote file for writing.\n";
    exit;
  }

  fputs($file, $totline2);
  fclose($file);


}


genPage($CATEGORIES_ID);


?>
