<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

  if($GLOBALS["core"]->sqlImport($GLOBALS["core"]->source_dir . "mod/poll/boost/install.sql", TRUE)) {
    $content .= "All Poll tables successfully written.<br />";
    CLS_Help::setup_help("poll");  

    $status = 1;
  } else
    $content .= "There was a problem writing to the database.<br />";

?>
