<?php

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}


if ($GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir."mod/blockmaker/boost/uninstall.sql", 1, 1)){
  $_SESSION['translate']->unregisterModule("mod_blockmaker_data");
  $GLOBALS['core']->killSession("OBJ_blockmaker");
  $content .= "All Blockmaker tables successfully removed.<br />";
  $status = 1;
} else {
  $content .= "There was a problem accessing the database.<br />";
  $status = 0;
}

?>