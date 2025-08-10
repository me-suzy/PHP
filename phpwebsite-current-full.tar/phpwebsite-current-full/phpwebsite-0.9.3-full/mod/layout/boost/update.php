<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

$status = 1;

if ($currentVersion < 1.7)
  $GLOBALS['core']->query("ALTER TABLE mod_layout_config ADD `userAllow` SMALLINT DEFAULT '1' NOT NULL AFTER `page_title`", TRUE);

?>