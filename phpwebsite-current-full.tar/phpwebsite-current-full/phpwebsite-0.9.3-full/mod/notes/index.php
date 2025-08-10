<?php
/**
 * @version $Id: index.php,v 1.9 2003/07/10 13:07:57 matt Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */

if (!isset($GLOBALS['core'])){
  header("location:../../");
  exit();
}

/* Check to see if Note session is set and set it if it's not. */
if(!isset($_SESSION["SES_NOTE_MANAGER"])) {
  $_SESSION["SES_NOTE_MANAGER"] = new PHPWS_NoteManager;
}

if($GLOBALS["module"] == "notes") {
  $GLOBALS["CNT_notes"] = array("title"=>$_SESSION["translate"]->it("Notes"),
				"content"=>NULL);
}

/* Check to see if an operation was recieved */
if(isset($_REQUEST["NOTE_op"]))
switch($_REQUEST["NOTE_op"]) {
 case "menu":
 $_SESSION["SES_NOTE_MANAGER"]->menu();
 break;

 case $_SESSION["translate"]->it("New Note"):
 $_SESSION["SES_NOTE_MANAGER"]->menu();
 $_SESSION["SES_NOTE"] = new PHPWS_Note;
 $_SESSION["SES_NOTE"]->edit();
 break;

 case $_SESSION["translate"]->it("My Notes"):
 $_SESSION["SES_NOTE_MANAGER"]->menu();
 $_SESSION["SES_NOTE_MANAGER"]->myNotes();
 break;

 case $_SESSION["translate"]->it("Sent Notes"):
 $_SESSION["SES_NOTE_MANAGER"]->menu();
 $_SESSION["SES_NOTE_MANAGER"]->sentNotes();
 break;

 case $_SESSION["translate"]->it("Send Note"):
 $_SESSION["SES_NOTE_MANAGER"]->menu();
 $_SESSION["SES_NOTE"]->send();
 break;

 case "read":
 $_SESSION["SES_NOTE_MANAGER"]->menu();
 $_SESSION["SES_NOTE"] = new PHPWS_Note($_GET["NOTE_id"]);
 $_SESSION["SES_NOTE"]->read();
 break;

 case "delete":
 $_SESSION["SES_NOTE_MANAGER"]->menu();
 $_SESSION["SES_NOTE"] = new PHPWS_Note($_REQUEST["NOTE_id"]);
 $_SESSION["SES_NOTE"]->delete();
 break;
}// END INDEX SWITCH

if(isset($_SESSION["OBJ_user"]->username)) {
  $_SESSION["SES_NOTE_MANAGER"]->showBlock();
}

?>