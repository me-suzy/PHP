<?
/*********************************************/
/* verifie l'existence d'une url */


function url_exists($url) {
  
  $fp = fsockopen ("localhost", 80, &$errno, &$errstr, 30);
  
  if (!$fp) {
    echo "Erreur de fp<p>";

  } else {
    
	fputs ($fp, "HEAD $url HTTP/1.0\r\nHost: www.chez.com\r\n\r\n");
    
	while (!feof($fp)) {
      $result .= fgets ($fp,128);
    }
    
	fclose ($fp);
  
  }

  if (stristr ($result, "200 OK")) {
    echo "ok<p>" ;
	return 1 ;
  }
  else {
  echo "pas ok<p>" ;
    return 0 ;
  }
}
/*********************************************/

$url = "/hitweb/index.php";
url_exists($url);
?>
