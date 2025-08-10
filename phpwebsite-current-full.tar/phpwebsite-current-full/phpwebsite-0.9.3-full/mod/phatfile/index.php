<?php

if(!isset($GLOBALS['core'])) {
  header("Location: ../..");
  exit();
}

include("conf/phatfile.php");

if(!isset($_SESSION["PHAT_FileManager"])) {
  $_SESSION["PHAT_FileManager"] = new PHAT_FileManager;
}

if(!isset($GLOBALS["CNT_phatfile"])) {
  $GLOBALS["CNT_phatfile"] = array("content"=>NULL);
}

if(isset($_REQUEST["FILE_MAN_OP"])) {
  $_SESSION["PHAT_FileManager"]->managerAction();
  $_SESSION["PHAT_FileManager"]->action();
}

if(isset($_REQUEST["FILE_OP"]) && isset($_SESSION["PHAT_FileManager"]->file)) {
    $_SESSION["PHAT_FileManager"]->file->action();
}

?>