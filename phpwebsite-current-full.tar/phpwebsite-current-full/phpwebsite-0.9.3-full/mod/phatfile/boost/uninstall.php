<?php
/*
 * @version $Id: uninstall.php,v 1.4 2003/03/24 20:19:10 adam Exp $
 */

if(!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if($GLOBALS["core"]->sqlImport(PHPWS_SOURCE_DIR . "mod/phatfile/boost/uninstall.sql", 1, 1)) {
  $content .= "All File Manager tables successfully removed!<br />";
  $content .= "Removing files directory at:<br />" . PHPWS_SOURCE_DIR . "files/phatfile<br />";

  PHPWS_File::rmdir(PHPWS_HOME_DIR . "files/phatfile/");

  if(isset($_SESSION["OBJ_fatcat"])) {
    $sql = "DELETE FROM " . $GLOBALS["core"]->tbl_prefix .
      "mod_fatcat_elements WHERE module_title='phatfile'";
    $GLOBALS["core"]->query($sql);
  }

  if(isset($_SESSION["OBJ_search"])) {
    $GLOBALS["core"]->sqlDelete("mod_search_register", "module", "phatfile");
  }

  $status = 1;
} else {
  $content .= "There was a problem accessing the database.<br />";
  $status = 0;
}

?>