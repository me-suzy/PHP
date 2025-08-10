<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if($status = $GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir."mod/approval/boost/install.sql", 1, 1))
     CLS_Help::setup_help("approval");

?>