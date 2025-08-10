<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

CLS_Help::uninstall_help("modmaker");
$status = 1;

?>