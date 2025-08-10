<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir."mod/blockmaker/boost/install.sql", 1, 1)){
  CLS_help::setup_help("blockmaker");
  $_SESSION['translate']->registerModule("blockmaker", "mod_blockmaker_data", "block_id", "block_title:block_content:block_footer");
  $content .= "All Blockmaker tables successfully written.<br />";
  $status = 1;
} else {
  $content .= "There was a problem writing to the database.<br />";
  $status = 0;
}
  

?>
