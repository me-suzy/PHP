<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($currentVersion < "1.5.1")
     $status = $core->sqlModifyColumn("mod_boost_version", "version", "varchar(20) NOT NULL default ''");

?>