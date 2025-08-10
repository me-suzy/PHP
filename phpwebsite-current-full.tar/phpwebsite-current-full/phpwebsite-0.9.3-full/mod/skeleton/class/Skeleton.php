<?php

class PHPWS_Skeleton extends PHPWS_Item {

  var $_muscle = NULL;

  function PHPWS_Skeleton($SKEL_ID = NULL) {
    $this->setTable("mod_skeleton_items");

    if(isset($SKEL_ID)) {
      $this->setId($SKEL_ID);
      $this->init();
    }
  }// END FUNC PHPWS_Skeleton

  function _view() {
    $tags = array();
    $tags["LABEL"] = $this->getLabel();
    $tags["MUSCLE"] = $this->getMuscle();

    return $GLOBALS["core"]->processTemplate($tags, "skeleton", "view.tpl");
  }// END FUNC _view

  function _edit() {
    $form = new EZForm("skeleton_edit");
    $form->add("SKEL_LABEL", "text", $this->getLabel());
    $form->add("SKEL_MUSCLE", "textarea", $this->_muscle);
    $form->add("SKEL_SAVE", "submit", $_SESSION["translate"]->it("Save Skeleton"));
    $form->add("module", "hidden", "skeleton");
    $form->add("SKEL_OP", "hidden", "save");

    $tags = $form->getTemplate();

    $id = $this->getId();
    if(isset($id)) {
      $tags["TITLE"] = $_SESSION["translate"]->it("Edit Skeleton");
    } else {
      $tags["TITLE"] = $_SESSION["translate"]->it("Add Skeleton");
    }

    $tags["BACK"] = "<a href=\"index.php?module=skeleton&amp;SKEL_MAN_OP=main\">" .
       $_SESSION["translate"]->it("Back to Skeleton List") . "</a>";

    $tags["LABEL_LABEL"] = $_SESSION["translate"]->it("Label");
    $tags["MUSCLE_LABEL"] = $_SESSION["translate"]->it("The Meat");

    return $GLOBALS["core"]->processTemplate($tags, "skeleton", "edit.tpl");
  }// END FUNC _edit

  function _save() {
    $error = $this->setLabel($_REQUEST["SKEL_LABEL"]);
    if(PHPWS_Error::isError($error)) {
      $error->message("CNT_skeleton");
      return $this->_edit();
    }

    $error = $this->setMuscle($_REQUEST["SKEL_MUSCLE"]);
    if(PHPWS_Error::isError($error)) {
      $error->message("CNT_skeleton");
      return $this->_edit();
    }

    $this->commit();
    $_SESSION["PHPWS_Skeleton_Message"] = $_SESSION["translate"]->it("Skeleton Saved!");
    $_REQUEST["SKEL_MAN_OP"] = "main";
    $_SESSION["PHPWS_SkeletonManager"]->action();
  }// END FUNC _save

  function _delete() {
    if(isset($_REQUEST["SKEL_ANSWER"]) && $_REQUEST["SKEL_ANSWER"] == "yes") {
      $this->kill();
      $_SESSION["PHPWS_Skeleton_Message"] = $_SESSION["translate"]->it("Skeleton deleted!");
      $_REQUEST["SKEL_MAN_OP"] = "main";
      $_SESSION["PHPWS_SkeletonManager"]->action();
    } elseif(isset($_REQUEST["SKEL_ANSWER"]) && $_REQUEST["SKEL_ANSWER"] == "no") {
      $_SESSION["PHPWS_Skeleton_Message"] = $_SESSION["translate"]->it("Skeleton was not deleted!");
      $_REQUEST["SKEL_MAN_OP"] = "main";
      $_SESSION["PHPWS_SkeletonManager"]->action();
    } else {
      $tags = array();
      $tags["MESSAGE"] = $_SESSION["translate"]->it("Are you sure you want to delete this skeleton?");
      $tags["YES"] = "<a href=\"index.php?module=skeleton&amp;SKEL_OP=delete&amp;SKEL_ANSWER=yes\">" .
	 $_SESSION["translate"]->it("Yes") . "</a>";
      $tags["NO"] = "<a href=\"index.php?module=skeleton&amp;SKEL_OP=delete&amp;SKEL_ANSWER=no\">" .
	 $_SESSION["translate"]->it("No") ."</a>";
      $tags["SKELETON"] = $this->_view();

      return $GLOBALS["core"]->processTemplate($tags, "skeleton", "confirm.tpl");
    }
  }// END FUNC _delete

  function setMuscle($muscle) {
    if(is_string($muscle)) {
      if(strlen($muscle) > 0) {
	$this->_muscle = PHPWS_Text::parseOutput($muscle);
	return TRUE;
      } else {
	$message = $_SESSION["translate"]->it("You must provide some meat!");
	return new PHPWS_Error("skeleton", "PHPWS_Skeleton::save", $message);
      }
    } else {
      $message = $_SESSION["translate"]->it("Muscle must be a string!");
      return new PHPWS_Error("skeleton", "PHPWS_Skeleton::save", $message);
    }
  }// END FUNC setMuscle

  function getMuscle() {
    if(isset($this->_muscle) && strlen($this->_muscle) > 0) {
      return PHPWS_Text::parseOutput($this->_muscle);
    } else {
      return NULL;
    }
  }// END FUNC getMuscle

  function action() {
    $content = NULL;
    switch($_REQUEST["SKEL_OP"]) {
      case "edit":
      $content .= $this->_edit();
      break;

      case "save":
      $content .= $this->_save();
      break;

      case "delete":
      $content .= $this->_delete();
      break;
    }

    if(isset($content)) {
      $GLOBALS["CNT_skeleton"]["content"] .= $content;
    }
  }// END FUNC action

}// END CLASS PHPWS_Skeleton

?>