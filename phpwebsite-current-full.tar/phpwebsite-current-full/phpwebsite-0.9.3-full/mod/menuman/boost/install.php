<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/menuman/boost/install.sql", 1, 1)){

  $time = time();
  $sql = "UPDATE " . $GLOBALS['core']->tbl_prefix . "mod_menuman_menus SET updated='$time'";
  $GLOBALS['core']->query($sql);

  CLS_help::setup_help("menuman");
  $content .= "All Menu Manager tables successfully written.<br />";
  $status = 1;
} else {
  $content .= "There was a problem writing to the database.<br />";
}

/* Create image directory */
@mkdir(PHPWS_HOME_DIR . "images/menuman");
if(is_dir(PHPWS_HOME_DIR . "images/menuman")) {
  $content .= "Menuman image directory " . PHPWS_HOME_DIR . "images/menuman successfully created!<br />";
} else {
  $content .= "Menuman could not create the image directory: " . PHPWS_HOME_DIR . "images/menuman<br />You will have to do this manually!<br />";
}

?>
