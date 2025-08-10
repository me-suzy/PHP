<?php
/**
 * @version $Id: Announcement.php,v 1.47 2003/07/08 15:47:03 adam Exp $
 */

class PHPWS_Announcement {

  var $_id = NULL;
  var $_subject = NULL;
  var $_summary = NULL;
  var $_body = NULL;
  var $_image = array();
  var $_hits = 0;
  var $_approved = 0;
  var $_active = 1;
  var $_comments = 0;
  var $_anonymous = 0;
  var $_new = TRUE;
  var $_user = FALSE;
  var $_poston;
  var $_expiration;
  var $_userCreated;
  var $_userUpdated;
  var $_dateCreated;
  var $_dateUpdated;

  function PHPWS_Announcement($ANN_id = NULL) {
    if($ANN_id === NULL) {
      $this->_new = TRUE;

      if(isset($_REQUEST["ANN_user_op"]))
	$this->_user = TRUE;
      elseif (isset($_REQUEST["ANN_op"]))
	$this->_user = FALSE;

      $this->_poston = date("Y-m-d H:i:s");
      $year = date("Y");
      $year = $year + 5;
      $this->_expiration = $year . "-01-01 12:00:00";
    } else {
      $result = $GLOBALS["core"]->sqlSelect("mod_announce", "id", $ANN_id);

      $this->_new = FALSE;
      $this->_id = $result[0]["id"];
      $this->_subject = $result[0]["subject"];
      $this->_summary = $result[0]["summary"];
      $this->_body = $result[0]["body"];

      if (isset($result[0]["image"]))
	$this->_image = unserialize($result[0]["image"]);
      else
	$this->_image = NULL;

      $this->_hits = $result[0]["hits"];
      $this->_active = $result[0]["active"];
      $this->_approved = $result[0]["approved"];
      $this->_comments = $result[0]["comments"];
      $this->_anonymous = $result[0]["anonymous"];
      $this->_poston = $result[0]["poston"];
      $this->_expiration = $result[0]["expiration"];
      $this->_userCreated = $result[0]["userCreated"];
      $this->_userUpdated = $result[0]["userUpdated"];
      $this->_dateCreated = $result[0]["dateCreated"];
      $this->_dateUpdated = $result[0]["dateUpdated"];
    }
  }

  function view($type, $approveView=FALSE) {
    if(!$this->_active && !$_SESSION["OBJ_user"]->allow_access("announce"))
      return;

    $tags["SUBJECT"] = $this->_subject;
    $tags["SUMMARY"] = PHPWS_Core::parseOutput($this->_summary);
    $tags["BODY"] = PHPWS_Core::parseOutput($this->_body);
    $tags["EXPIRATION"] = $this->_expiration;

    if (!empty($this->_userCreated))
      $tags["POSTED_USER"] = $this->_userCreated;      
    else
      $tags["POSTED_USER"] = $_SESSION["translate"]->it("Anonymous");

    $tags["POSTED_DATE"] = $this->_dateCreated;
    $tags["UPDATED_USER"] = $this->_userUpdated;
    $tags["UPDATED_DATE"] = $this->_dateUpdated;

    if($_SESSION["OBJ_user"]->allow_access("announce", "edit_announcement")) {
      $edit = $_SESSION["translate"]->it("Edit");
      $tags["EDIT"] = "<a href=\"index.php?module=announce&amp;ANN_op=$edit&amp;ANN_id=" .
	$this->_id . "\">$edit</a>";
    }

    if($_SESSION["OBJ_fatcat"]) {
      $tags["CATEGORY_ICON"] = $_SESSION["OBJ_fatcat"]->getIcon($this->_id);
      $tags["CATEGORY_IMAGE"] = $_SESSION["OBJ_fatcat"]->getImage($this->_id);
    }

    if(isset($this->_image["name"]))
      $tags["IMAGE"] = "<img src=\"images/announce/" . $this->_image["name"] . "\" border=\"0\" width=\"" . $this->_image["width"] . "\" height=\"" . $this->_image["height"] . "\" alt=\"" . $this->_image["alt"] . "\" title=\"" . $this->_image["alt"] . "\" />";


    /* Full view of the announcement */
    if($type == "full") {
      if($GLOBALS["core"]->moduleExists("comments") && $this->_comments) {
	$tags["COMMENTS"] = $_SESSION["PHPWS_CommentManager"]->listCurrentComments("announce", $this->_id, $this->_anonymous);
      }

      $content = $GLOBALS["core"]->processTemplate($tags, "announce", "view_full.tpl");

      if($_SESSION["OBJ_fatcat"])
	$_SESSION["OBJ_fatcat"]->whatsRelated($this->_id);

    } elseif ($type = "small") {

      if(!$approveView && (!$this->_active || !$this->_approved))
	return;

      /* Summarized view of announcement, user on home page */
      if($GLOBALS["core"]->moduleExists("comments") && $this->_comments) {
	$numComments = $_SESSION["PHPWS_CommentManager"]->numComments("announce", $this->_id);
	$tags["NUM_COMMENTS"] = "<a href=\"index.php?module=announce&amp;ANN_user_op=view&amp;ANN_id=" . $this->_id .  "\">$numComments " . $_SESSION["translate"]->it("Comments") . "</a>";
      }

      if (strlen($this->_body) > 0){
	/* if hits = 0 set it to string "None" or it won't show up in the template */
	if($this->_hits == 0)
	  $tags["HITS"] = $_SESSION["translate"]->it("None");
	else
	  $tags["HITS"] = $this->_hits;

	$tags["READ_MORE"] = "<a href=\"index.php?module=announce&amp;ANN_user_op=view&amp;ANN_id=" . $this->_id .  "\">" . $_SESSION["translate"]->it("Read More") . "</a>";
      }

      $content = $GLOBALS["core"]->processTemplate($tags, "announce", "view_small.tpl");
    }

    if($approveView)
      return $content;
    else {
      if(!isset($GLOBALS["CNT_announce"])) {
	$GLOBALS["CNT_announce"] = array("title"=>NULL, "content"=>NULL);
      }

      $GLOBALS["CNT_announce"]["content"] .= $content;
    }
  }

  function formatDate($date) {
    return substr(preg_replace("/[^0-9]/", "", $date), 0, 8);
  }

  function edit() {
    $tags = array();
    $tags["SUBJECT_LABEL"] = $_SESSION["translate"]->it("Subject");
    $tags["SUBJECT"] = PHPWS_Core::formTextField("ANN_subject", $this->_subject, 60, 255);
    $tags["SUMMARY_LABEL"] = $_SESSION["translate"]->it("Summary");
    $tags["SUMMARY"] = $GLOBALS["core"]->js_insert("wysiwyg", "announce_edit", "ANN_summary") . PHPWS_Core::formTextArea("ANN_summary", $this->_summary, 10, 70);
    $tags["BODY_LABEL"] = $_SESSION["translate"]->it("Body");
    $tags["BODY"] = $GLOBALS["core"]->js_insert("wysiwyg", "announce_edit", "ANN_body") . PHPWS_Core::formTextArea("ANN_body", $this->_body, 10, 70);
    $tags["IMAGE_LABEL"] = $_SESSION["translate"]->it("Image");
    $tags["IMAGE"] = PHPWS_Core::formFile("ANN_image");

    if(!empty($this->_image["name"])) {
      $tags["IMAGE"] .= "<br /><img src=\"images/announce/" . $this->_image["name"] . "\" border=\"0\" width=\"" .
	 $this->_image["width"] . "\" height=\"" . $this->_image["height"] . "\" alt=\"" . $this->_image["alt"] .
	 "\" title=\"" . $this->_image["alt"] . "\" />";
      $tags["REMOVE_LABEL"] = $_SESSION["translate"]->it("Remove Image");
      $tags["REMOVE_CHECK"] = PHPWS_Core::formCheckBox("ANN_remove_image");
    }

    if (isset($this->_image["alt"]))
      $altTag = $this->_image["alt"];
    else
      $altTag = NULL;

    $tags["IMAGE_ALT_LABEL"] = $_SESSION["translate"]->it("Short Description");
    $tags["IMAGE_ALT"] = PHPWS_Core::formTextField("ANN_alt", $altTag, 60, 100);
    $tags["POSTON_LABEL"] = $_SESSION["translate"]->it("Post On");
    $tags["POSTON"] = $GLOBALS["core"]->formDate("ANN_poston", $this->formatDate($this->_poston), substr($this->formatDate($this->_poston), 0, 4));
    $tags["EXPIRATION_LABEL"] = $_SESSION["translate"]->it("Expiration");
    $tags["EXPIRATION"] = $GLOBALS["core"]->formDate("ANN_expiration", $this->formatDate($this->_expiration), substr($this->formatDate($this->_expiration), 0, 4));
    $tags["COMMENTS_LABEL"] = $_SESSION["translate"]->it("Allow Comments?");
    $tags["YES_COMMENTS"] = PHPWS_Core::formRadio("ANN_comments", 1, $this->_comments, NULL, "Yes");
    $tags["NO_COMMENTS"] = PHPWS_Core::formRadio("ANN_comments", 0, $this->_comments, NULL, "No");
    $tags["ANON_LABEL"] = $_SESSION["translate"]->it("Anonymous Posts?");
    $tags["ANON_YES"] = PHPWS_Core::formRadio("ANN_anonymous", 1, $this->_anonymous, NULL, "Yes");
    $tags["ANON_NO"] = PHPWS_Core::formRadio("ANN_anonymous", 0, $this->_anonymous, NULL, "No");

    if($_SESSION["OBJ_fatcat"]) {
      $tags["CATEGORIES_LABEL"] = $_SESSION["translate"]->it("Category");
      $tags["CATEGORIES"] = $_SESSION["OBJ_fatcat"]->showSelect($this->_id);
    }

    if($this->_user)
      $tags["SUBMIT_BUTTON"] = PHPWS_Core::formSubmit($_SESSION["translate"]->it("Save"), "ANN_user_op");
    else
      $tags["SUBMIT_BUTTON"] = PHPWS_Core::formSubmit($_SESSION["translate"]->it("Save"), "ANN_op");

    if($this->_new) $tags["TITLE"] = $_SESSION["translate"]->it("New Announcement");
    else $tags["TITLE"] = $_SESSION["translate"]->it("Edit Announcement");

    $elements[0] = PHPWS_Core::formHidden("module", "announce");
    $elements[0] .= $GLOBALS["core"]->processTemplate($tags, "announce", "edit.tpl");

    $content = PHPWS_Core::makeForm("announce_edit", "index.php", $elements, "post", FALSE, TRUE);
    $GLOBALS["CNT_announce"]["content"] .= $content;
  }

  function save() {
    if(isset($_REQUEST["ANN_op"]) && $_REQUEST["ANN_op"] == $_SESSION["translate"]->it("Save") && !$_SESSION["OBJ_user"]->allow_access("announce", "edit_announcement") && !$_SESSION["OBJ_user"]->allow_access("announce", "edit_announcement")) {
      $this->_error("access_denied");
      return;
    }

    $this->_body = PHPWS_Core::parseInput($_POST["ANN_body"]);
    $data["body"] = $this->_body;
    $this->_poston = $_POST["ANN_poston_year"] . "-" . $_POST["ANN_poston_month"] . "-" . $_POST["ANN_poston_day"] . " 00:00:00";
    $data["poston"] = $this->_poston;
    $this->_expiration = $_POST["ANN_expiration_year"] . "-" . $_POST["ANN_expiration_month"] . "-" . $_POST["ANN_expiration_day"] . " 00:00:00";
    $data["expiration"] = $this->_expiration;
    $this->_comments = $_POST["ANN_comments"];
    $data["comments"] = $this->_comments;
    $this->_anonymous = $_POST["ANN_anonymous"];
    $data["anonymous"] = $this->_anonymous;

    if(isset($_REQUEST["ANN_user_op"]) && $_REQUEST["ANN_user_op"] == $_SESSION["translate"]->it("Save")) {
      $this->_approved = 0;
    } elseif($_REQUEST["ANN_op"] == $_SESSION["translate"]->it("Save")) {
      $this->_approved = 1;
    }

    $data["approved"] = $this->_approved;

    if($_POST["ANN_subject"]) {
      $this->_subject = PHPWS_Core::parseInput($_POST["ANN_subject"]);
      $data["subject"] = $this->_subject;
    } else {
      $this->_error("no_subject");
      $this->edit();
      return;
    }

    if($_POST["ANN_summary"]) {
      $this->_summary = PHPWS_Core::parseInput($_POST["ANN_summary"]);
      $data["summary"] = $this->_summary;
    } else {
      $this->_error("no_summary");
      $this->edit();
      return;
    }

    if($_FILES["ANN_image"]["name"]) {
      include($GLOBALS["core"]->source_dir . "mod/announce/conf/config.php");

      $image = EZform::saveImage("ANN_image", $GLOBALS["core"]->home_dir . "images/announce/", $max_image_width, $max_image_height, NULL, (isset($allowedImageTypes)) ? $allowedImageTypes : NULL);

      if (PHPWS_Error::isError($image)){
	$image->message("CNT_announce");
	$this->edit();
	return;
      }
      
      $this->_image = $image;

    } elseif (isset($this->_image["name"]) && isset($_POST["ANN_remove_image"])) {
      unlink($GLOBALS["core"]->home_dir . "images/announce/" . $this->_image["name"]);
      $this->_image = array();
      $data["image"] = $this->_image;
    }

    if(isset($this->_image["name"]) && isset($_POST["ANN_alt"])) {
      $this->_image["alt"] = $_POST["ANN_alt"];
      $data["image"] = serialize($this->_image);
    } elseif(isset($this->_image["name"])) {
      $this->_error("no_alt");
      $this->edit();
      return;
    }

    if($this->_new) {
      if($this->add($data)) {
	if($this->_user) {
	  $short = "<b>" . $this->_subject . "</b><br />" . $this->_summary;
	  $info["id"] = $this->_id;
	  PHPWS_Approval::add($this->_id, $short, "announce");
	  PHPWS_Fatcat::deactivate($this->_id, "announce");
	  $content = $_SESSION["translate"]->it("Your announcement was submitted for approval.");
	} else {
	  $content = $_SESSION["translate"]->it("Your announcement was successfully saved.");
	}
      } else {
	$this->_error("save_failed");
	$this->edit();
	return;
      }
    } elseif ($this->_id) {
      if($this->update($data)) {
	$content = $_SESSION["translate"]->it("Your announcement was successfully updated.");
      } else {
	$this->_error("update_failed");
	$this->edit();
	return;
      }
    }

    unset($_SESSION["SES_ANN_MANAGER"]->_pager);
    $GLOBALS["CNT_announce"]["content"] .= $content;
    if($this->_user) {
      return;
    } else {
      $_SESSION["SES_ANN_MANAGER"]->listAnnouncements();
    }
  }

  function delete() {
    if(!$_SESSION["OBJ_user"]->allow_access("announce", "delete_announcement")) {
      $this->_error("access_denied");
      return;
    }

    if(isset($_POST["yes"])){
      $GLOBALS["core"]->sqlDelete("mod_announce", "id", $this->_id);
      if (class_exists("PHPWS_Fatcat"))
	PHPWS_Fatcat::purge($this->_id, "announce");

      if (class_exists("PHPWS_Comment")){
	$where['module'] = "announce";
	$where['itemId'] = $this->_id;
	$GLOBALS['core']->sqlDelete("mod_comments_data", $where);
      }

      unset($_SESSION["SES_ANN_MANAGER"]->_pager);
      $content = $_SESSION["translate"]->it("The announcement was successfully <b>deleted</b>.") . "<br />";
    } elseif (isset($_POST["no"])) {
      $content = $_SESSION["translate"]->it("You have chosen <b>not</b> to delete the announcement.") . "<br />";
    } else {
      $elements[0] = PHPWS_Core::formHidden("module", "announce");
      $elements[0] .= PHPWS_Core::formHidden("ANN_op", "Delete");
      $elements[0] .= PHPWS_Core::formHidden("ANN_id", $this->_id);
      $elements[0] .= PHPWS_Core::formSubmit($_SESSION["translate"]->it("Yes"), "yes");
      $elements[0] .= PHPWS_Core::formSubmit($_SESSION["translate"]->it("No"), "no");

      $content = $_SESSION["translate"]->it("Are you sure you wish to delete the announcement with subject") . " <b>" . $this->_subject . "</b>?<br /><br />";
      $content .= PHPWS_Core::makeForm("announce_delete", "index.php", $elements);
    }

    $GLOBALS["CNT_announce"]["content"] .= $content;
  }

  function add($data) {
    if((isset($_REQUEST["ANN_op"]) && $_REQUEST["ANN_op"] == $_SESSION["translate"]->it("Save")) && !$_SESSION["OBJ_user"]->allow_access("announce", "edit_announcement")) {
      $this->_error("access_denied");
      return;
    }

    if (!empty($_SESSION["OBJ_user"]->username)){
      $data["userCreated"] = $_SESSION["OBJ_user"]->username;
      $data["userUpdated"] = $_SESSION["OBJ_user"]->username;
    }

    $data["dateCreated"] = date("Y-m-d H:i:s");
    $data["dateUpdated"] = date("Y-m-d H:i:s");

    $this->_id = $GLOBALS["core"]->sqlInsert($data, "mod_announce", FALSE, TRUE);

    if($_SESSION["OBJ_fatcat"])
      $_SESSION["OBJ_fatcat"]->saveSelect($this->_subject, "index.php?module=announce&amp;ANN_user_op=view&amp;ANN_id=" . $this->_id, $this->_id);

    if($this->_id)
      return TRUE;
    else
      return FALSE;
  }

  function update($data) {
    if(!$_SESSION["OBJ_user"]->allow_access("announce", "edit_announcement") && empty($data["hits"])) {
      $this->_error("access_denied");
      return;
    }

    if(empty($data["hits"])) {
      $data["userUpdated"] = $_SESSION["OBJ_user"]->username;
      $data["dateUpdated"] = date("Y-m-d H:i:s");
    }

    if($GLOBALS["core"]->sqlUpdate($data, "mod_announce", "id", $this->_id)){
      if($_SESSION["OBJ_fatcat"])
	$_SESSION["OBJ_fatcat"]->saveSelect($this->_subject, "index.php?module=announce&amp;ANN_user_op=view&amp;ANN_id=" . $this->_id, $this->_id);
      return TRUE;
    } else
      return FALSE;
  }

  function hit() {
    $this->_hits++;
    $data["hits"] = $this->_hits;
    $this->update($data);
  }

  function showHide() {
    if(!$_SESSION["OBJ_user"]->allow_access("announce", "activate_announcement")) {
      $this->_error("access_denied");
      return;
    }

    PHPWS_Core::toggle($this->_active);
    $data["active"] = $this->_active;
    $this->update($data);
  }

  /**
   * Returns an indexed array of all the current groups in the database
   *
   * @return array $users An array of all groups
   * @access private
   * @see    edit()
   */
  function _getGroups() {
    /* Grab all groups from database */
    $result = $GLOBALS["core"]->sqlSelect("mod_user_groups", NULL, NULL, "group_name");

    /* Add blank group */
    $groups[] = " ";

    /* Create groups array */
    if($result)
    foreach($result as $resultRow)
      $groups[] = $resultRow["group_name"];

    return $groups;
  }// END FUNC _getGroups()

  function approve($id) {
    $data["approved"] = 1;
    $data["dateCreated"] = date("Y-m-d H:i:s");
    $data["dateUpdated"] = date("Y-m-d H:i:s");

    $GLOBALS["core"]->sqlUpdate($data, "mod_announce", "id", $id);
    PHPWS_Fatcat::activate($id, "announce");
  }

  function refuse($id) {
    $GLOBALS["core"]->sqlDelete("mod_announce", "id", $id);
    PHPWS_Fatcat::purge($id, "announce");
  }

  function fatView($id) {
    $this = new PHPWS_Announcement($id);
    return $this->view("small", TRUE);
  }

  function _error($type) {
    $content = "<b><font class=\"errortext\">" . $_SESSION["translate"]->it("ERROR!") . "</font></b><br /><br />";
    switch($type) {
      case "no_summary":
      $content .= $_SESSION["translate"]->it("You did not provide a summary for your announcement.");
      break;

      case "no_subject":
      $content .= $_SESSION["translate"]->it("You did not provide a subject for your announcement.");
      break;

      case "no_alt":
      $content .= $_SESSION["translate"]->it("You must provide a short description for the image you supplied.");
      break;

      case "image_upload":
      $content .= $_SESSION["translate"]->it("There was a problem uploading the image you specified.  Check your permissions.");
      break;

      case "not_allowed_type":
      include($GLOBALS["core"]->source_dir . "mod/announce/conf/config.php");
      $content .= $_SESSION["translate"]->it("The file you uploaded is not an allowed type on this server") . ": <b>" . $_FILES["ANN_image"]["type"] . "</b><br />" .
      $_SESSION["translate"]->it("The allowed types are") . ": <b>$allowedImageTypes</b>";
      break;

      case "save_failed":
      $content .= $_SESSION["translate"]->it("There was a problem saving your announcement.") . " " . $_SESSION["translate"]->it("If the problem persists contact your system administrator.");
      break;

      case "update_failed":
      $content .= $_SESSION["translate"]->it("There was a problem updating your announcement.") . " " . $_SESSION["translate"]->it("If the problem persists contact your system administrator.");
      break;

      case "access_denied":
      $content .= "<b>" . $_SESSION["translate"]->it("ACCESS DENIED!") . "</b>";
      break;
    }

    $GLOBALS["CNT_announce"]["content"] .= $content;
  }

}// END CLASS PHPWS_Announcement

?>