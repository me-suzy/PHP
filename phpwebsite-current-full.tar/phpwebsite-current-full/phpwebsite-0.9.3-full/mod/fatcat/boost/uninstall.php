<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

$content .= "Removing images directory " . $GLOBALS["core"]->home_dir . "images/fatcat<br />";

$_SESSION["OBJ_help"]->uninstall_help("fatcat");
$GLOBALS['core']->sqlDropTable("mod_fatcat_categories");
$GLOBALS['core']->sqlDropTable("mod_fatcat_elements");
$GLOBALS['core']->sqlDropTable("mod_fatcat_settings");
$status = 1;
?>