<?php

class PHAT_FileManager extends PHPWS_Manager {

  var $file;

  function PHAT_FileManager() {
    $this->setModule("phatfile");
    $this->setRequest("FILE_MAN_OP");
    $this->setTable("mod_phatfile_files");
    $this->init();
  }// END FUNC PHAT_FileManager

  function _menu() {
    $addFile = $_SESSION["translate"]->it("Add New File");
    $tags["ADD_NEW_FILE"] = "<a href=\"index.php?module=phatfile&amp;FILE_MAN_OP=Add\">$addFile</a>";

    return $GLOBALS["core"]->processTemplate($tags, "phatfile", "menu.tpl");
  }// END FUNC _menu

  function _list() {
    $content = $this->getList("files");
    return $content;
  }// END FUNC _list

  function action() {
    switch($_REQUEST["FILE_MAN_OP"]) {
      case "Main":
      $content = $this->_menu();
      $content .= $this->_list();
      break;

      case "Add":
      $this->file = new PHAT_File;
      $content = $this->file->edit();
      break;

      case "Edit":
      if(sizeof($_REQUEST["PHPWS_MAN_ITEMS"]) > 0) {
	$this->file = new PHAT_File($_REQUEST["PHPWS_MAN_ITEMS"][0]);
	$content = $this->file->edit();
      } else {
	$content = $this->_menu();
	$content .= $this->_list();
      }
      break;

      case "Download":
      if(isset($_REQUEST["PHPWS_MAN_ITEMS"]) && sizeof($_REQUEST["PHPWS_MAN_ITEMS"]) > 0) {
	$this->file = new PHAT_File($_REQUEST["PHPWS_MAN_ITEMS"][0]);
	$this->file->download();
      } else {
	$content = $this->_menu();
	$content .= $this->_list();
      }
      break;

      case "Delete":
      if(sizeof($_REQUEST["PHPWS_MAN_ITEMS"]) > 0) {
	$this->file = new PHAT_File($_REQUEST["PHPWS_MAN_ITEMS"][0]);
	$content = $this->file->delete();
      } else {
	$content = $this->_menu();
	$content .= $this->_list();
      }
      break;

      default:
      $content = $this->_menu();
      $content .= $this->_list();
      break;
    }

    $GLOBALS["CNT_phatfile"]["content"] .= $content;
  }// END FUNC _action

}// END CLASS PHAT_FileManager

?>