<?php

include(PHPWS_SOURCE_DIR . "mod/photoalbum/conf/config.php");

require_once(PHPWS_SOURCE_DIR . "mod/photoalbum/class/Album.php");

/**
 * @version $Id: AlbumManager.php,v 1.8 2003/05/21 18:44:53 steven Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 */

class PHPWS_AlbumManager extends PHPWS_Manager {

  /**
   * Stores the ids of the photos for the current album being viewed
   *
   * @var    PHPWS_Album
   * @access public
   */
  var $album = NULL;

  /**
   * Stores the current error that has occured in the photoalbum
   *
   * @var    PHPWS_Error
   * @access public
   */
  var $error = NULL;

  /**
   * Stores the current message to display for the photoalbum
   *
   * @var    PHPWS_Message
   * @access public
   */
  var $message = NULL;

  function PHPWS_AlbumManager() {
    $this->setModule("photoalbum");
    $this->setTable("mod_photoalbum_albums");
    $this->setRequest("PHPWS_AlbumManager_op");
    $this->init();
  }

  function _list() {
    $GLOBALS['CNT_photoalbum']['title'] = $_SESSION['translate']->it("Photo Albums"); 

    $links = array();
    $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_AlbumManager_op=list\">" . $_SESSION['translate']->it("List Albums") . "</a>";

    if($_SESSION['OBJ_user']->allow_access("photoalbum", "add_album")) {
      $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_AlbumManager_op=new\">" . $_SESSION['translate']->it("New Album") . "</a>";
    }
    
    $GLOBALS['CNT_photoalbum']['content'] .= "<div align=\"right\">" . implode("&#160;|&#160;", $links) . "</div><br />";

    if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "edit_album")) {
      $this->setSort("hidden='0'");
    }

    $GLOBALS['CNT_photoalbum']['content'] .= $this->getList("albums", $_SESSION['translate']->it("Photo Albums"), FALSE);
    $this->setSort(NULL);
  }

  function _view($id) {
    $this->album = new PHPWS_Album($id[0]);
    $_REQUEST['PHPWS_Album_op'] = "view";
  }

  function _new() {
    $this->album = new PHPWS_Album;
    $_REQUEST['PHPWS_Album_op'] = "edit";
  }

  function _accessDenied() {
    if(PHPWS_Error::isError($this->error)) {
      $this->error->message("CNT_photoalbum", $_SESSION['translate']->it("Access Denied!"));
      $this->error = NULL;
    } else {
      $message = $_SESSION['translate']->it("Access denied function was called without a proper error initialized.");
      $error = new PHPWS_Error("photoalbum", "PHPWS_AlbumManager::_accessDenied()", $message, "exit", 1);
      $error->message();
    }
  }

  function action() {
    if(PHPWS_Message::isMessage($this->message)) {
      $this->message->display();
    }

    switch($_REQUEST['PHPWS_AlbumManager_op']) {
    case "new":
      $this->_new();
      break;

    case "accessDenied":
      $this->_accessDenied();
      break;
    }
  }
}

?>