<?

// Affiche le code source dans le navigateur web
function code()
{
  Header("Content-type: text/plain");
}



function get_translation_admin($dir) 
{

  $handle=opendir($dir);
  $i = 0;
  while ($file = readdir($handle)) {
    
    // Damien Seguy - marche
   preg_match_all("/^([a-zA-Z0-9]*).php/", $file, $regs);
   echo $regs[1][0];

  }
  
  closedir($handle);
  
  
}

$dir= "lang" ;

get_translation_admin($dir) ;

?>
