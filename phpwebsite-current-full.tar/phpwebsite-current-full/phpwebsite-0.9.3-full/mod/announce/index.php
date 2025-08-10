<?php
/**
 * @version $Id: index.php,v 1.9 2003/07/10 13:07:06 matt Exp $
 */

if (!isset($GLOBALS['core'])){
  header("location:../../");
  exit();
}


/* Check to see if the manager exists and create it if it doesn't */
if(!isset($_SESSION["SES_ANN_MANAGER"])) {
  $_SESSION["SES_ANN_MANAGER"] = new PHPWS_AnnouncementManager;
}

if($GLOBALS["module"] == "announce") {
  $GLOBALS["CNT_announce"] = array("title"=>$_SESSION["translate"]->it("Announcements"),
				   "content"=>NULL);
}

if(isset($_REQUEST["ANN_op"]) && $_SESSION["OBJ_user"]->allow_access("announce"))
switch($_REQUEST["ANN_op"]) {
 case "menu":
 $_SESSION["SES_ANN_MANAGER"]->menu();
 $_SESSION["SES_ANN_MANAGER"]->listAnnouncements();
 break;

 case "new":
 $_SESSION["SES_ANN_MANAGER"]->menu();
 $_SESSION["SES_ANN"] = new PHPWS_Announcement;
 $_SESSION["SES_ANN"]->edit();
 break;

 case "list":
 $_SESSION["SES_ANN_MANAGER"]->menu();
 $_SESSION["SES_ANN_MANAGER"]->listAnnouncements();
 break;

 case "settings":
 $_SESSION["SES_ANN_MANAGER"]->menu();
 $_SESSION["SES_ANN_MANAGER"]->getSettings();
 break;

 case $_SESSION["translate"]->it("Save Settings"):
 $_SESSION["SES_ANN_MANAGER"]->menu();
 $_SESSION["SES_ANN_MANAGER"]->setSettings();
 $_SESSION["SES_ANN_MANAGER"]->listAnnouncements();
 break;

 case $_SESSION["translate"]->it("Save"):
 $_SESSION["SES_ANN_MANAGER"]->menu();
 $_SESSION["SES_ANN"]->save();
 //$_SESSION["SES_ANN_MANAGER"]->listAnnouncements();
 break;

 case $_SESSION["translate"]->it("Edit"):
 $_SESSION["SES_ANN_MANAGER"]->menu();
 $_SESSION["SES_ANN"] = new PHPWS_Announcement($_REQUEST["ANN_id"]);
 $_SESSION["SES_ANN"]->edit();
 break;

 case $_SESSION["translate"]->it("Delete"):
 $_SESSION["SES_ANN_MANAGER"]->menu();
 $_SESSION["SES_ANN"] = new PHPWS_Announcement($_REQUEST["ANN_id"]);
 $_SESSION["SES_ANN"]->delete();
 break;

 case $_SESSION["translate"]->it("Show"):
 $_SESSION["SES_ANN_MANAGER"]->menu();
 $_SESSION["SES_ANN"] = new PHPWS_Announcement($_REQUEST["ANN_id"]);
 $_SESSION["SES_ANN"]->showHide();
 $_SESSION["SES_ANN_MANAGER"]->listAnnouncements();
 break;

 case $_SESSION["translate"]->it("Hide"):
 $_SESSION["SES_ANN_MANAGER"]->menu();
 $_SESSION["SES_ANN"] = new PHPWS_Announcement($_REQUEST["ANN_id"]);
 $_SESSION["SES_ANN"]->showHide();
 $_SESSION["SES_ANN_MANAGER"]->listAnnouncements();
 break;

 case "view":
 $_SESSION["SES_ANN_MANAGER"]->menu();
 $_SESSION["SES_ANN"] = new PHPWS_Announcement($_REQUEST["ANN_id"]);
 $_SESSION["SES_ANN"]->view("full");
 break;
}

if(isset($_REQUEST["ANN_user_op"]))
switch($_REQUEST["ANN_user_op"]) {
 case "view":
 $_SESSION["SES_ANN"] = new PHPWS_Announcement($_REQUEST["ANN_id"]);
 $_SESSION["SES_ANN"]->view("full");
 $_SESSION["SES_ANN"]->hit();
 break;

 case "submit_announcement":
 $_SESSION["SES_ANN"] = new PHPWS_Announcement;
 $_SESSION["SES_ANN"]->edit();
 break;

 case $_SESSION["translate"]->it("Save"):
 $_SESSION["SES_ANN"]->save();
 break;
}

if($GLOBALS["module"] == "home") {
  $_SESSION["SES_ANN_MANAGER"]->showAnnouncements();
  $_SESSION["SES_ANN_MANAGER"]->pastBlock();
}

?>
