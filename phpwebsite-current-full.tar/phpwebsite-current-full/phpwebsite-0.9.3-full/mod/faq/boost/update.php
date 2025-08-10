<?php
if(!$_SESSION["OBJ_user"]->isDeity()) {
  header("location:index.php");
  exit();
}

$status = 1;

if ($currentVersion < ".92"){
  $content .= "- Fixed table prefix problems with approval.<br />";
  $content .= "- Default menu link now points to view.<br />";
  $content .= "- Fixed category indentions in view.<br />";
}


?>