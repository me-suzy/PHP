<?php
/**
 * This is the control class for the Announce module.
 *
 * @version $Id: AnnouncementManager.php,v 1.25 2003/07/08 15:47:03 adam Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */
class PHPWS_AnnouncementManager {

  var $_numHome;
  var $_numPast;
  var $_showCurrent;
  var $_showPast;
  var $_allowedImageTypes;
  var $_pager;

  function PHPWS_AnnouncementManager() {
    $allowedImageTypes = NULL;

    include($GLOBALS["core"]->source_dir . "mod/announce/conf/config.php");
    $this->_allowedImageTypes = explode(",", $allowedImageTypes);
    foreach($this->_allowedImageTypes as $key=>$type)
      $this->_allowedImageTypes[$key] = trim($type);

    $result = $GLOBALS["core"]->sqlSelect("mod_announce_settings");

    $this->_numHome = $result[0]["numHome"];
    $this->_numPast = $result[0]["numPast"];
    $this->_showCurrent = $result[0]["showCurrent"];
    $this->_showPast = $result[0]["showPast"];
  }

  function menu() {
    if($_SESSION["OBJ_user"]->allow_access("announce", "edit_announcement")) {
      $tags["NEW_ANNOUNCEMENT"] = "<a href=\"index.php?module=announce&amp;ANN_op=new\">" .
	 $_SESSION["translate"]->it("New Announcement") . "</a>";
    }

    if($_SESSION["OBJ_user"]->allow_access("announce", "modify_settings")) {
      $tags["SETTINGS"] = "<a href=\"index.php?module=announce&amp;ANN_op=settings\">" .
	 $_SESSION["translate"]->it("Settings") . "</a>";
    }

    $tags["LIST_ANNOUNCEMENTS"] = "<a href=\"index.php?module=announce&amp;ANN_op=list\">" .
       $_SESSION["translate"]->it("List Announcements") . "</a>";

    $tags["HELP"] = $_SESSION["OBJ_help"]->show_link("announce", "admin_menu");

    $content = $GLOBALS["core"]->processTemplate($tags, "announce", "menu.tpl");

    if(!isset($GLOBALS["CNT_announce"]["content"])) {
      $GLOBALS["CNT_announce"]["content"] = NULL;
    }

    $GLOBALS["CNT_announce"]["content"] .= $content;
  }

  function showAnnouncements() {
    if(!$this->_showCurrent)
      return;

    $sql = "SELECT * FROM " . $GLOBALS["core"]->tbl_prefix .
       "mod_announce WHERE expiration>'" . date("Y-m-d H:i:s") .
       "' AND poston<='" . date("Y-m-d H:i:s") .
       "' AND approved='1' ORDER BY dateCreated DESC LIMIT " . $this->_numHome;
    $result = $GLOBALS["core"]->getAll($sql);

    if($result) {
      foreach($result as $row) {
	$current_announcement = new PHPWS_Announcement($row["id"]);
	$current_announcement->view("small");
      }
    }
  }// END FUNC showAnnouncements()

  function listAnnouncements() {
    if(!isset($this->_pager)) {
      $this->_pager = new PHPWS_Pager;
      $this->_pager->setLinkBack("./index.php?module=announce&amp;ANN_op=menu");
      $this->_pager->makeArray(TRUE);

      $sql = "SELECT id FROM " . $GLOBALS["core"]->tbl_prefix . "mod_announce ORDER BY dateCreated DESC";

      $result = NULL;
      $result = $GLOBALS["core"]->getCol($sql);
      $this->_pager->setData($result);
      $result = NULL;
    }

    $this->_pager->pageData();

    $data = NULL;
    $data = $this->_pager->getData();

    if(isset($data) && sizeof($data) > 0) {

      $sql = "SELECT * FROM " . $GLOBALS["core"]->tbl_prefix . "mod_announce WHERE ";
      $flag = FALSE;
      foreach($data as $id) {
	if($flag) {
	  $sql .= " OR ";
	}
	$sql .= "id='$id'";
	$flag = TRUE;
      }
      $sql .= " ORDER BY dateCreated DESC";
      $result = $GLOBALS["core"]->getAll($sql);

      if(isset($result) && sizeof($result) > 0) {
	$tags = array();
	$tags2 = array();

	$tags["SUBJECT"] = "<b>" . $_SESSION["translate"]->it("Subject") . "</b>";
	$tags["POSTON_DATE"] = "<b>" . $_SESSION["translate"]->it("Post on Date") . "</b>";
	$tags["POSTED_USER"] = "<b>" . $_SESSION["translate"]->it("Posted by") . "</b>";
	$tags["SHOW_HIDE"] = "<b>" . $_SESSION["translate"]->it("Action") . "</b>";

	$tags2["TITLE"] = $_SESSION["translate"]->it("Current Announcements");
	$tags2["LIST_ITEMS"] = $GLOBALS["core"]->processTemplate($tags, "announce", "list_item.tpl");

	foreach($result as $row) {
	  $tags = array();

	  $tags["SUBJECT"] = "<a href=\"index.php?module=announce&amp;ANN_id=" .
	     $row["id"] . "&amp;ANN_op=view\">" . $row["subject"] . "</a>";

	  $tags["POSTON_DATE"] = $row["poston"];

	  if (empty($row["userCreated"]))
	    $tags["POSTED_USER"] = $_SESSION["translate"]->it("Anonymous");
	  else
	    $tags["POSTED_USER"] = $row["userCreated"];

	  if($_SESSION["OBJ_user"]->allow_access("announce", "activate_announcement")) {
	    if($row["active"]) {
	      $tags["SHOW_HIDE"] = PHPWS_Core::formSubmit($_SESSION["translate"]->it("Hide"), "ANN_op");
	    } else {
	      $tags["SHOW_HIDE"] = PHPWS_Core::formSubmit($_SESSION["translate"]->it("Show"), "ANN_op");
	    }
	  }

	  if($_SESSION["OBJ_user"]->allow_access("announce", "edit_announcement")) {
	    $tags["EDIT"] = PHPWS_Core::formSubmit($_SESSION["translate"]->it("Edit"), "ANN_op");
	  }

	  if($_SESSION["OBJ_user"]->allow_access("announce", "delete_announcement")) {
	    $tags["DELETE"] = PHPWS_Core::formSubmit($_SESSION["translate"]->it("Delete"), "ANN_op");
	  }

	  $elements = array();
	  $elements[0] = PHPWS_Core::formHidden("module", "announce");
	  $elements[0] .= PHPWS_Core::formHidden("ANN_id", $row["id"]);
	  $elements[0] .= $GLOBALS["core"]->processTemplate($tags, "announce", "list_item.tpl");
	  
	  $tags2["LIST_ITEMS"] .=  PHPWS_Core::makeForm("announce_menu", "index.php", $elements);
	}

	$tags2["PAGE_BACKWARD_LINK"] = $this->_pager->getBackLink();
	$tags2["PAGE_FORWARD_LINK"] = $this->_pager->getForwardLink();
	$tags2["SECTION_LINKS"] = $this->_pager->getSectionLinks();
	$tags2["SECTION_INFO"] = $this->_pager->getSectionInfo();
	$tags2["LIMIT_LINKS"] = $this->_pager->getLimitLinks();
	$tags2["LIMIT_LINKS_LABEL"] = $_SESSION["translate"]->it("Limit");

	$content = $GLOBALS["core"]->processTemplate($tags2, "announce", "list.tpl");
      }
    } else {
      $content = $_SESSION["translate"]->it("No announcements found!");
    }

    $GLOBALS["CNT_announce"]["content"] .= $content;
  }// END FUNC listAnnouncements()

  function getSettings() {
    $tags = array();
    $tags["TITLE"] = $_SESSION["translate"]->it("Announcement Settings");
    $tags["SHOW_ANN_LABEL"] = $_SESSION["translate"]->it("Show Announcements");
    $tags["SHOW_ANN"] = PHPWS_Core::formCheckBox("ANN_showCurrent", 1, $this->_showCurrent);
    $tags["SHOW_PAST_ANN_LABEL"] = $_SESSION["translate"]->it("Show Past Announcements");
    $tags["SHOW_PAST_ANN"] = PHPWS_Core::formCheckBox("ANN_showPast", 1, $this->_showPast);
    $tags["NUM_HOME_ANN_LABEL"] = $_SESSION["translate"]->it("Number of announcements shown on home page");
    $tags["NUM_HOME_ANN"] = PHPWS_Core::formTextField("ANN_numHome", $this->_numHome, 3);
    $tags["NUM_PAST_ANN_LABEL"] = $_SESSION["translate"]->it("Number of past announcements shown");
    $tags["NUM_PAST_ANN"] = PHPWS_Core::formTextField("ANN_numPast", $this->_numPast, 3);
    $tags["SUBMIT"] = PHPWS_Core::formSubmit($_SESSION["translate"]->it("Save Settings"), "ANN_op");

    $elements[0] = PHPWS_Core::formHidden("module", "announce");
    $elements[0] .= $GLOBALS["core"]->processTemplate($tags, "announce", "settings.tpl");

    $content = PHPWS_Core::makeForm("announce_settings", "index.php", $elements);

    $GLOBALS["CNT_announce"]["content"] .= $content;
  }

  function setSettings() {
    $this->_showCurrent = $_POST["ANN_showCurrent"];
    $this->_showPast = $_POST["ANN_showPast"];
    $this->_numHome = $_POST["ANN_numHome"];
    $this->_numPast = $_POST["ANN_numPast"];

    $data["showCurrent"] = $this->_showCurrent;
    $data["showPast"] = $this->_showPast;
    $data["numHome"] = $this->_numHome;
    $data["numPast"] = $this->_numPast;

    $GLOBALS["core"]->sqlUpdate($data, "mod_announce_settings");

    $content = $_SESSION["translate"]->it("Your settings have successfully been saved!");

    $GLOBALS["CNT_announce"]["content"] .= $content;
  }

  function pastBlock() {
    if(!$this->_showPast)
      return;

    $limit = $this->_numHome + $this->_numPast;
    $sql = "SELECT * FROM " . $GLOBALS["core"]->tbl_prefix . "mod_announce WHERE expiration>'" . date("Y-m-d H:i:s") .
       "' AND approved='1' ORDER BY dateCreated DESC LIMIT " . $limit;
    $result = $GLOBALS["core"]->getAll($sql);

    if($result) {
      $content = NULL;
      $num = 1;
      for($i=0; $i < $this->_numHome+$this->_numPast; $i++) {
	if($i < $this->_numHome) {
	  continue;
	} elseif(isset($result[$i]["subject"])) {
	  $content .= $num . ". <a href=\"index.php?module=announce&amp;ANN_user_op=view&amp;ANN_id=". $result[$i]["id"] ."\">" . $result[$i]["subject"] . "</a><br />";
	  $num++;
	}
      }

      if(isset($content)) {
	$tags = array();
	$tags["TITLE"] = $_SESSION["translate"]->it("Past") . " $this->_numPast " . $_SESSION["translate"]->it("Announcements");
	$tags["CONTENT"] = $content;

	$GLOBALS["CNT_announce_past"] = array("content"=>NULL);
	$GLOBALS["CNT_announce_past"]["content"] .=
	   $GLOBALS["core"]->processTemplate($tags, "announce", "past.tpl");
      }
    }
  }

  function isAllowedImageType($type) {
    return in_array($type, $this->_allowedImageTypes);
  }

  function search($where) {
    $sql = "SELECT id, subject FROM " . $GLOBALS["core"]->tbl_prefix . "mod_announce $where";
    $result = $GLOBALS["core"]->query($sql);

    if($result) {
      while($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
	$array[$row["id"]] = $row["subject"];
      }
    }
    return $array;
  }

}// END CLASS PHPWS_AnnouncementManager

?>