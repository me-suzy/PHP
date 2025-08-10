<?php

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

$status = 1;

if ($currentVersion < 1.02) {
  $content .= "Comment Updates (Version 1.02)<br />";
  $content .= "+ fixed a bug when viewing comments threaded as anonymous user comments would crash<br />";
}

?>