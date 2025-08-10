<?php

/**
 * @version $Id: uninstall.php,v 1.6 2003/06/25 19:18:46 steven Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlImport(PHPWS_SOURCE_DIR . "mod/photoalbum/boost/uninstall.sql", 1, 1)) {
  $content .= "All photoalbum tables successfully removed.<br />";

  $ok = PHPWS_File::rmdir(PHPWS_HOME_DIR . "images/photoalbum/");
  if($ok) {
    $content .= "The photoalbum images directory was fully removed.<br />";
  } else {
    $content .= "The photoalbum images directory could not be removed.<br />";
  }

  $status = 1;
} else {
  $content .= "There was a problem accessing the database.<br />";
  $status = 0;
}

?>