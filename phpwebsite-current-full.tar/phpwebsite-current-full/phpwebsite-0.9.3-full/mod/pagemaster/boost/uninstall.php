<?php
/**
 * This is the Pagemaster uninstall file for Boost
 *
 * @version $Id: uninstall.php,v 1.5 2003/06/12 16:30:25 adam Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS["core"]->sqlImport(PHPWS_SOURCE_DIR . "mod/pagemaster/boost/uninstall.sql", 1, 1)) {
  $content .= "All tables successfully removed.<br />";
  $content .= "Removing images directory " . PHPWS_HOME_DIR . "images/pagemaster<br />";

  PHPWS_File::rmdir(PHPWS_HOME_DIR . "images/pagemaster/");

  $_SESSION["OBJ_help"]->uninstall_help("pagemaster");

  if(isset($_SESSION["OBJ_approval"]))
    $_SESSION["OBJ_approval"]->unregister_module("pagemaster");

  if(isset($_SESSION["OBJ_search"]))
    $GLOBALS["core"]->sqlDelete("mod_search_register", "module", "pagemaster");

  $_SESSION["SES_PM_master"] = NULL;
  $_SESSION["SES_PM_page"] = NULL;
  $_SESSION["SES_PM_section"] = NULL;
  $_SESSION["SES_PM_error"] = NULL;

  $status = 1;
} else {
  $content .= "There was a problem accessing the database.<br />";
}

?>