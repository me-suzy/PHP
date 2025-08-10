<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

$status = $GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir."mod/boost/boost/install.sql", 1, 1);

?>