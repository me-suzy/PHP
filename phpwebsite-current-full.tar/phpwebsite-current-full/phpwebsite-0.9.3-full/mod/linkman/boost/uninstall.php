<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir."mod/linkman/boost/uninstall.sql", 1, 1)){
  $content .= "All Link Manager tables successfully removed.<br />";
  $status = 1;

  if(isset($_SESSION['OBJ_search'])) {
    $GLOBALS['core']->sqlDelete("mod_search_register", "module", "linkman");
  }

} else
  $content .= "There was a problem accessing the database.<br />";

?>