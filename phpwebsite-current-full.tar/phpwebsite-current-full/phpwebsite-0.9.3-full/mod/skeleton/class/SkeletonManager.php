<?php

class PHPWS_SkeletonManager extends PHPWS_Manager {

  var $skeleton = NULL;

  function PHPWS_SkeletonManager() {
    $this->setModule("skeleton");
    $this->setRequest("SKEL_MAN_OP");
    $this->setTable("mod_skeleton_items");
    $this->init();
  }// END FUNC PHPWS_SkeletonManager

  function _menu() {
    $addSkeleton = $_SESSION["translate"]->it("Add New Skeleton");
    $listSkeletons = $_SESSION["translate"]->it("List Skeletons");

    $tags = array();

    if($_SESSION["OBJ_user"]->allow_access("skeleton", "edit_skeletons")) {
      $tags["ADD_SKELETON"] = "<a href=\"index.php?module=skeleton&amp;SKEL_MAN_OP=add\">$addSkeleton</a>";
    }

    $tags["LIST_SKELETONS"] = "<a href=\"index.php?module=skeleton&amp;SKEL_MAN_OP=main\">$listSkeletons</a>";

    if(isset($_SESSION["PHPWS_Skeleton_Message"])) {
      $tags["SKELETON_MESSAGE"] = $_SESSION["PHPWS_Skeleton_Message"];
      $_SESSION["PHPWS_Skeleton_Message"] = NULL;
    }

    return $GLOBALS["core"]->processTemplate($tags, "skeleton", "menu.tpl");
  }// END FUNC _menu

  function _list() {
    $content = $this->_menu();
    $content .= $this->getList("skeleton");
    $GLOBALS["CNT_skeleton"]["content"] .= $content;
  }// END FUNC _list

  function _edit($ids) {
    $this->skeleton = new PHPWS_Skeleton($ids[0]);
    $_REQUEST["SKEL_OP"] = "edit";
  }// END FUNC _edit

  function _add() {
    $this->skeleton = new PHPWS_Skeleton;
    $_REQUEST["SKEL_OP"] = "edit";
  }// END FUNC _add

  function _delete($ids) {
    $this->skeleton = new PHPWS_Skeleton($ids[0]);
    $_REQUEST["SKEL_OP"] = "delete";
  }// END FUNC _edit

  function action() {
    switch($_REQUEST["SKEL_MAN_OP"]) {
      case "main":
      $this->_list();
      break;

      case "add":
      $this->_add();
      break;
    }
  }// END FUNC action

}// END CLASS PHPWS_SkeletonManager

?>