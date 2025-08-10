<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}


if ($GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir."mod/fatcat/boost/install.sql", 1, 1)){
  CLS_help::setup_help("fatcat");

  if (!is_dir($GLOBALS['core']->home_dir . "images/fatcat"))
    mkdir($GLOBALS['core']->home_dir . "images/fatcat");

  if (!is_dir($GLOBALS['core']->home_dir . "images/fatcat/images"))
    mkdir($GLOBALS['core']->home_dir . "images/fatcat/images");

  if (!is_dir($GLOBALS['core']->home_dir . "images/fatcat/icons"))
    mkdir($GLOBALS['core']->home_dir . "images/fatcat/icons");
  
  if(is_dir($GLOBALS['core']->home_dir . "images/fatcat"))
    $content .= "FatCat image directories successfully created!<br />";
  else
    $content .= "FatCat could not create the image directories:<br /> "
      . $GLOBALS['core']->home_dir . "images/fatcat/images/<br />" 
      . $GLOBALS['core']->home_dir . "images/fatcat/icons/<br />You will have to do this manually!<br />";
  $status = 1;
} else
  $content .= "There was a problem writing to the database.<br />";

?>