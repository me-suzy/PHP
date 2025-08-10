<?php

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

$status = 1;

if ($currentVersion < 1.1){
  $_SESSION["OBJ_security"] = NULL;
}

?>
