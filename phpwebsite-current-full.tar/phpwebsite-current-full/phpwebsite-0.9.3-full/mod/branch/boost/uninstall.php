<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}


if ($GLOBALS['core']->sqlTableExists("branch_sites", TRUE)){
  $status = $GLOBALS['core']->sqlDropTable("branch_sites");
}
     
if (is_dir(PHPWS_SOURCE_DIR . "conf/branch/")){
  $status = $GLOBALS['core']->rmdir(PHPWS_SOURCE_DIR . "conf/branch/");
  $content .= "Removing branch configuration directory.<br />";
}


?>