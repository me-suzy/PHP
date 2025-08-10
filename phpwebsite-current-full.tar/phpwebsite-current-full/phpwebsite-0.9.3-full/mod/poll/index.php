<?php
if (!isset($GLOBALS['core'])){
  header("location:../../");
  exit();
}

if(!isset($_SESSION["SES_POLL"])) {
    $_SESSION["SES_POLL"] = new PollManager;
}

if(isset($_REQUEST["module"]) && ($_REQUEST["module"] == "poll") && isset($_REQUEST["PHPWS_MAN_OP"])) {
  $_SESSION["SES_POLL"]->managerAction();
}
	
if (isset($_REQUEST["poll_op"])) {
    $_SESSION["SES_POLL"]->action();
}

$_SESSION["SES_POLL"]->showUserBox();

?>
