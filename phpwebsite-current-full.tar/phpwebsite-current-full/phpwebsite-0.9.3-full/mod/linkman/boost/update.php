<?php

if(!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

$status = 1;

if($currentVersion < 1.02) {
  $sql = "ALTER TABLE " . $GLOBALS['core']->tbl_prefix . "mod_linkman_links CHANGE user username TEXT NOT NULL";
  $result = $GLOBALS['core']->query($sql);

  if($result) {
    $content .= "Link Manager Updates (Version 1.02)<br />";
    $content .= "+ changed user column in table to username to be postgre compatible<br />";
  } else {
    $status = 0;
  }
}

if($currentVersion < 1.03) {
  $content .= "Link Manager Updates (Version 1.03)<br />";
  $content .= "+ Security update with permissions<br />";
}
?>