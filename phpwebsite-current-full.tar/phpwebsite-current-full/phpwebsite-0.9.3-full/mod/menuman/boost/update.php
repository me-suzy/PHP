<?php

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

$status = 1;

if ($currentVersion < 1.01) {
  $GLOBALS['core']->query("ALTER TABLE mod_menuman_menus ADD COLUMN `updated` INT(11) NOT NULL DEFAULT '0' AFTER `template`", TRUE);
  $content .= $_SESSION['translate']->it("You must logout and log back in before this update will take affect!");
}

if ($currentVersion < 1.12) {
  $_SESSION['OBJ_layout']->create_temp("menuman", "CNT_menuman_add", "bottom");

  $content .= "Menuman Updates (Version 1.12)<br />";
  $content .= "+ Added the site map functionality<br />";
  $content .= "+ Added content variable for adding menu links so it appears below other mods<br />";
  $content .= "+ Main administration menu is now made up of links for easier navigation<br />";
}

if ($currentVersion < 1.14) {
  $content .= "Menuman Updates (Version 1.14)<br />";
  $content .= "+ fixed a bug when adding pagemaster pages via the menu<br />";
  $content .= "+ fixed a bug causing newly saved menu settings to not take effect immediately<br />";
  $content .= "+ added new template variables THEME_DIRECTORY and MENU_ID<br />";
}

if ($currentVersion < 1.15) {
  $content .= "Menuman Updates (Version 1.15)<br />";
  $content .= "+ control over whether or not menu stays expanded<br />";
  $content .= "+ other various bug fixes<br />";
}

if(!is_dir(PHPWS_HOME_DIR . "images/menuman")) {
  $content .= "+ menuman directory did not exist attempting to create<br />";
  @mkdir(PHPWS_HOME_DIR . "images/menuman");
  
  if(is_dir(PHPWS_HOME_DIR . "images/menuman")) {
    $content .= "&#160;&#160;- creation successful<br />";
  } else {
    $content .= "&#160;&#160;- creation failed, please check file permissions<br />";
  }
}

?>