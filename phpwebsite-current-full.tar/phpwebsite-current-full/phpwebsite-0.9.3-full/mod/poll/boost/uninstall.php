<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir."mod/poll/boost/uninstall.sql", 1, 1)) {
  $content .= "All Note tables successfully removed.<br />";
  CLS_Help::uninstall_help("poll");
  $status = 1;
} else
$content .= "There was a problem accessing the database.<br />";

?>
