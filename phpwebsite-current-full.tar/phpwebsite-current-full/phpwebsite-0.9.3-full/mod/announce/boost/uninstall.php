<?php
/**
 * @version $Id: uninstall.php,v 1.2 2003/03/25 21:09:59 matt Exp $
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir . "mod/announce/boost/uninstall.sql", 1, 1)) {
  $content .= "All Announcement tables successfully removed.<br />";
  $content .= "Removing images directory " . $GLOBALS['core']->source_dir . "images/announce<br />";
  system("rm -rf " . $GLOBALS['core']->home_dir . "images/announce", $temp);
  $status =1;

  //$_SESSION["OBJ_help"]->uninstall_help("announce");

  if(isset($_SESSION["OBJ_search"]))
    $GLOBALS["core"]->sqlDelete("mod_search_register", "module", "announce");

} else {
  $content .= "There was a problem accessing the database.<br />";
  $status = 0;
}

?>