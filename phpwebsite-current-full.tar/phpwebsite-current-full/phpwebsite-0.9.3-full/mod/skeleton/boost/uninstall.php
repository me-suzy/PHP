<?php
/**
 * This is a skeleton version of an uninstall file for boost. Edit it to
 * be used with your module.
 *
 * $Id: uninstall.php,v 1.3 2003/07/02 18:18:01 adam Exp $
 */

/* Make sure the user is a deity before running this script */
if(!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

/* Import the uninstall database file and dump the result into the status variable */
if($status = $GLOBALS["core"]->sqlImport(PHPWS_SOURCE_DIR . "mod/skeleton/boost/uninstall.sql", 1, 1)) {
  $content .= "All skeleton tables successfully removed!<br /><br />";

  /* Check for files directory and remove if it exists */
  if(is_dir(PHPWS_HOME_DIR . "files/skeleton")) {
    $content .= "Removing skeleton files directory at:<br />" . PHPWS_HOME_DIR . "files/skeleton<br /><br />";
    PHPWS_File::rmdir(PHPWS_HOME_DIR . "files/skeleton/");
  } else {
    $content .= "No files directory found for removal.<br /><br />";
  }

  /* Check for images directory and remove if it exists */
  if(is_dir(PHPWS_HOME_DIR . "images/skeleton")) {
    $content .= "Removing skeleton images directory at:<br />" . PHPWS_HOME_DIR . "images/skeleton<br /><br />";
    PHPWS_File::rmdir(PHPWS_HOME_DIR . "images/skeleton/");
  } else {
    $content .= "No images directory found for removal.<br /><br />";
  }

  /* Unregister with core search module */
  if(isset($_SESSION["OBJ_search"])) {
    $GLOBALS["core"]->sqlDelete("mod_search_register", "module", "phatfile");
  }

} else {
  $content .= "There was a problem accessing the database.<br /><br />";
}

?>