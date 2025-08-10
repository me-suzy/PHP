<?php

/**
 * @version $Id: install.php,v 1.3 2003/06/25 19:18:46 steven Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if($status = $GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/photoalbum/boost/install.sql", TRUE)) {
  $content .= "All PhotoAlbum tables successfully written.<br />";
  
  /* Create image directory */
  @mkdir(PHPWS_HOME_DIR . "images/photoalbum");
  if(is_dir(PHPWS_HOME_DIR . "images/photoalbum")) {
    $content .= "PhotoAlbum images directory successfully created!<br />" . PHPWS_HOME_DIR . "images/photoalbum<br />";
  } else {
    $content .= "Boost could not create the PhotoAlbum image directory:<br />" . PHPWS_HOME_DIR . "images/photoalbum<br />You will have to do this manually!<br />";
  }

} else {
    $content .= "There was a problem writing to the database.<br />";
}

?>