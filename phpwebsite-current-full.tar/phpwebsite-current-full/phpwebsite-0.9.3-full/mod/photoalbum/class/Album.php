<?php

require_once(PHPWS_SOURCE_DIR . "mod/photoalbum/class/Photo.php");

/**
 * @version $Id: Album.php,v 1.24 2003/07/03 15:47:59 steven Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 */

class PHPWS_Album extends PHPWS_Item {

  /**
   * The short description of the photo album
   *
   * @var    string
   * @access private
   */
  var $_blurb0 = NULL;

  /**
   * The extended description of the photo album
   *
   * @var    string
   * @access private
   */
  var $_blurb1 = NULL;

  /**
   * The image to be shown in the album list
   *
   * @var    string
   * @access private
   */
  var $image = NULL;

  /**
   * The ids of all the photos for the current album
   *
   * @var    array
   * @access public
   */
  var $photos = array();

  /**
   * The current photo being edited or viewed
   *
   * @var    PHPWS_Photo
   * @access public
   */
  var $photo = NULL;

  var $pager = NULL;

  function PHPWS_Album($id=NULL) {
    $this->setTable("mod_photoalbum_albums");
    $this->addExclude(array("photos", "photo", "pager"));

    if(isset($id)) {
      $error = $this->setId($id);
      if(PHPWS_Error::isError($error)) {
	$error->message();
      }
      $this->init();
      $sql = "SELECT id FROM " . $GLOBALS['core']->tbl_prefix . "mod_photoalbum_photos WHERE album='" . $this->getId() . "'";

      if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "edit_photo")) {
	$sql .= " AND hidden='0'";
      }

      $this->photos = $GLOBALS['core']->getCol($sql);
    }
  }

  function _edit() {
    $id = $this->getId();
    $authorize = TRUE;
    if(isset($id)) {
      if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "edit_album")) {
	$message = $_SESSION['translate']->it("You do not have permission to edit photoalbums.");
	$authorize = FALSE;
      }
    } else {
      if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "add_album")) {
	$message = $_SESSION['translate']->it("You do not have permission to add photoalbums.");
	$authorize = FALSE;
      }
    }

    if(!$authorize) {
      $_SESSION['PHPWS_AlbumManager']->error = new PHPWS_Error("photoalbum", "PHPWS_Album::_edit()", $message, "continue", PHPWS_PHOTOALBUM_DEBUG);
      $_REQUEST['PHPWS_AlbumManager_op'] = "accessDenied";
      $_SESSION['PHPWS_AlbumManager']->action();
      return;
    }
    
    $options = array(0=>$_SESSION['translate']->it("Visible"),
		     1=>$_SESSION['translate']->it("Hidden"));

    $hidden = 0;
    if($this->isHidden()) {
      $hidden = 1;
    }

    $form = new EZform("PHPWS_Album_edit");
    $form->add("Album_name", "text", $this->getLabel());
    $form->setSize("Album_name", 33);
    $form->setMaxSize("Album_name", 255);
    $form->setTab("Album_name", 1);

    $form->add("Album_short", "text", $this->_blurb0);
    $form->setSize("Album_short", 40);
    $form->setMaxSize("Album_short", 255);
    $form->setTab("Album_short", 2);

    $form->add("Album_hidden", "select", $options);
    $form->setMatch("Album_hidden", $hidden);
    $form->setTab("Album_hidden", 3);

    $form->add("Album_ext", "textarea", $this->_blurb1);
    $form->setTab("Album_ext", 4);

    $form->add("Album_save", "submit", $_SESSION['translate']->it("Save"));
    $form->setTab("Album_save", 5);
 
    $form->add("module", "hidden", "photoalbum");
    $form->add("PHPWS_Album_op", "hidden", "save");
     
    $tags = array();
    $tags = $form->getTemplate();

    $tags['NAME_TEXT'] = $_SESSION['translate']->it("Name");
    $tags['SHORT_TEXT'] = $_SESSION['translate']->it("Short");
    $tags['HIDDEN_TEXT'] = $_SESSION['translate']->it("Activity");
    $tags['EXT_TEXT'] = $_SESSION['translate']->it("Extended");

    if(isset($_SESSION['OBJ_fatcat'])) {
      $tags['CATEGORY_TEXT'] = $_SESSION['translate']->it("Category");
      $tags['CATEGORY'] = $_SESSION['OBJ_fatcat']->showSelect($this->getId());
    }

    if($_SESSION['OBJ_user']->js_on){
      $tags['ALBUM_EXT'] = $GLOBALS['core']->js_insert("wysiwyg", "PHPWS_Album_edit", "Album_ext") . $tags['ALBUM_EXT'];
    }

    $id = $this->getId();
    if(isset($id)) {
      $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=view\">" . $_SESSION['translate']->it("Back") . "</a>";

      if($_SESSION['OBJ_user']->allow_access("photoalbum", "delete_album")) {
	$links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=delete\">" . $_SESSION['translate']->it("Delete Album") . "</a>";
      }

      if($GLOBALS['core']->moduleExists("menuman")) {
	$_SESSION['OBJ_menuman']->add_module_item("photoalbum", "&amp;PHPWS_AlbumManager_op=view&amp;PHPWS_MAN_ITEMS[]=" . $this->getId(), "./index.php?module=photoalbum&amp;PHPWS_AlbumManager_op=view&amp;PHPWS_MAN_ITEMS[]=" . $this->getId(), 1);
      }
    } else {
      $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_AlbumManager_op=list\">" . $_SESSION['translate']->it("Back") . "</a>";
    }

    $tags['LINKS'] = implode("&#160;|&#160;", $links);

    return $GLOBALS['core']->processTemplate($tags, "photoalbum", "editAlbum.tpl");
  }

  function _save() {
    $id = $this->getId();
    $authorize = TRUE;
    if(isset($id)) {
      if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "edit_album")) {
	$message = $_SESSION['translate']->it("You do not have permission to edit photoalbums.");
	$authorize = FALSE;
      }
    } else {
      if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "add_album")) {
	$message = $_SESSION['translate']->it("You do not have permission to add photoalbums.");
	$authorize = FALSE;
      }
    }

    if(!$authorize) {
      $_SESSION['PHPWS_AlbumManager']->error = new PHPWS_Error("photoalbum", "PHPWS_Album::_save()", $message, "continue", PHPWS_PHOTOALBUM_DEBUG);
      $_REQUEST['PHPWS_AlbumManager_op'] = "accessDenied";
      $_SESSION['PHPWS_AlbumManager']->action();
      return;
    }
    
    if(isset($_REQUEST['Album_ext'])) {
      $this->_blurb1 = $GLOBALS['core']->parseInput($_REQUEST['Album_ext']);
    }

    if(isset($_REQUEST['Album_short'])) {
      $this->_blurb0 = $GLOBALS['core']->parseInput($_REQUEST['Album_short']);
    }

    if(isset($_REQUEST['Album_name'])) {
      $error = $this->setLabel($_REQUEST['Album_name']);
    }

    if(isset($_REQUEST['Album_hidden']) && ($_REQUEST['Album_hidden'] == 1)) {
      $this->setHidden();
    } else {
      $this->setHidden(FALSE);
    }

    if(PHPWS_Error::isError($error)) {
      $message =  $_SESSION['translate']->it("You must enter a name for the Photo Album.");
      $error = new PHPWS_Error("photoalbum", "PHPWS_Album::_save()", $message, "continue", PHPWS_PHOTOALBUM_DEBUG);
      $error->message("CNT_photoalbum");

      $_REQUEST['PHPWS_Album_op'] = "edit";
      $this->action();
      return;
    }

    $error = $this->commit();
    if(PHPWS_Error::isError($error)) {
      $message = $_SESSION['translate']->it("The Photo Album could not be updated to the database.");
      $error = new PHPWS_Error("photoalbum", $message, "continue", PHPWS_PHOTOALBUM_DEBUG);
      $error->message("CNT_photoalbum");
      
      $_REQUEST['PHPWS_Album_op'] = "edit";
      $this->action();
      return;
    } else {

      if ($this->isHidden())
	$fatActive = FALSE;
      else
	$fatActive = TRUE;

      $_SESSION['OBJ_fatcat']->saveSelect($this->getLabel(), "index.php?module=photoalbum&amp;PHPWS_AlbumManager_op=view&amp;PHPWS_MAN_ITEMS[]=" . $this->getId(), $this->getId(), NULL, "photoalbum", NULL, NULL, $fatActive);

      if(!is_dir(PHOTOALBUM_DIR . $this->getId() . "/")) {
	mkdir(PHOTOALBUM_DIR . $this->getId() . "/");
	if(!is_dir(PHOTOALBUM_DIR . $this->getId() . "/")) {
	  $message = $_SESSION['translate']->it("The photo album image directory could not be created.");
	  $error = new PHPWS_Error("photoalbum", "PHPWS_Photo::_save()", $message, "continue", PHPWS_PHOTOALBUM_DEBUG);
	  $error->message("CNT_photoalbum");
	  
	  $_REQUEST['PHPWS_Album_op'] = "edit";
	  $this->action();
	  return;
	}
      }

      $message = $_SESSION['translate']->it("The Photo Album [var1] was successfully saved.", "<b><i>" . $this->getLabel() . "</i></b>") . "<br />\n";
      $_SESSION['PHPWS_AlbumManager']->message = new PHPWS_Message($message, "CNT_photoalbum");

      $_REQUEST['PHPWS_Album_op'] = "view";
      $this->action();
    }
  }

  function _delete() {
    if(!$_SESSION['OBJ_user']->allow_access("photoalbum", "delete_album")) {
      $message = $_SESSION['translate']->it("You do not have permission to delete entire photo albums.");
      $_SESSION['PHPWS_AlbumManager']->error = new PHPWS_Error("photoalbum", "PHPWS_Album::_delete()", $message, "continue", PHPWS_PHOTOALBUM_DEBUG);
      $_REQUEST['PHPWS_AlbumManager_op'] = "accessDenied";
      $_SESSION['PHPWS_AlbumManager']->action();
      return;
    }

    if(isset($_REQUEST['Album_yes'])) {
      $sql = "DELETE FROM " . $GLOBALS['core']->tbl_prefix . "mod_photoalbum_albums WHERE id='" . $this->getId() . "'";
      $GLOBALS['core']->query($sql);

      $sql = "DELETE FROM " . $GLOBALS['core']->tbl_prefix . "mod_photoalbum_photos WHERE album='" . $this->getId() . "'";
      $GLOBALS['core']->query($sql);

      PHPWS_File::rmdir("images/photoalbum/" . $this->getId() . "/");
      PHPWS_Fatcat::purge($this->getId(), "photoalbum");

      $message = $_SESSION['translate']->it("The album [var1] and all its photos were successfully deleted from the database.", "<b><i>" . $this->getLabel() . "</i></b>");
      $_SESSION['PHPWS_AlbumManager']->message = new PHPWS_Message($message, "CNT_photoalbum");
      $_SESSION['PHPWS_AlbumManager']->message->display();
      
      $_REQUEST['PHPWS_AlbumManager_op'] = "list";
      $_SESSION['PHPWS_AlbumManager']->managerAction();
      $this = NULL;

    } else if(isset($_REQUEST['Album_no'])) {
      $message = $_SESSION['translate']->it("No album was deleted from the database.");
      $_SESSION['PHPWS_AlbumManager']->message = new PHPWS_Message($message, "CNT_photoalbum");

      $_REQUEST['PHPWS_Album_op'] = "view";
      $_SESSION['PHPWS_AlbumManager']->album->action();

    } else {
      $title = $_SESSION['translate']->it("Delete Album Confirmation");

      $form = new EZform("PHPWS_Album_delete");
      $form->add("module", "hidden", "photoalbum");
      $form->add("PHPWS_Album_op", "hidden", "delete");

      $form->add("Album_yes", "submit", $_SESSION['translate']->it("Yes"));
      $form->add("Album_no", "submit", $_SESSION['translate']->it("No"));
      
      $tags = array();
      $tags = $form->getTemplate();
      $tags['MESSAGE'] = $_SESSION['translate']->it("Are you sure you want to delete this album and all the photos associated with it?");
      
      $content = $GLOBALS['core']->processTemplate($tags, "photoalbum", "deleteAlbum.tpl");
      $GLOBALS['CNT_photoalbum']['content'] = "<h3>$title</h3>$content";
     
      $GLOBALS['CNT_photoalbum']['title'] = $_SESSION['translate']->it("Photo Album") . ":&#160;" . $_SESSION['PHPWS_AlbumManager']->album->getLabel();
      $GLOBALS['CNT_photoalbum']['content'] .= $this->_view();
    }
  }
    
  function _view() {
    $columns = NULL;
    if(!isset($this->pager)) {
      $this->pager = new PHPWS_pager;
      $this->pager->setLimits(array(1,4,9,16));
      $this->pager->makeArray(TRUE);
      $this->pager->limit = 9;
    }

    $this->pager->setLinkBack("./index.php?module=photoalbum&amp;PHPWS_Album_op=view");
    $this->pager->setData($this->photos);
    $this->pager->pageData();
    $photoIds = $this->pager->getData();

    $links = array();
    $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_AlbumManager_op=list\">" . $_SESSION['translate']->it("List Albums") . "</a>";

    if($_SESSION['OBJ_user']->allow_access("photoalbum", "add_photo")) {
      $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=new\">" . $_SESSION['translate']->it("New Photo") . "</a>";
    }

    if($_SESSION['OBJ_user']->allow_access("photoalbum", "edit_album")) {
      $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=edit\">" . $_SESSION['translate']->it("Settings") . "</a>";
    }

    $listTags = array();

    if($this->isHidden()) {
      $listTags['HIDDEN_INFO'] = $_SESSION['translate']->it("This album is currently hidden from the public.");
    }

    $listTags['ALBUM_LINKS'] = implode("&#160;|&#160;", $links);
    
    if(is_array($photoIds) && (sizeof($photoIds) > 0)) {
      $sql = "SELECT * FROM " . $GLOBALS['core']->tbl_prefix . "mod_photoalbum_photos WHERE (";
      
      foreach($photoIds as $photoId) {
	$sql .= " id='$photoId' OR ";
      }
      $sql = substr($sql, 0, strlen($sql)-4) . ")";

      $photos = $GLOBALS['core']->query($sql);

      /* Initialize column information */
      for($i = 1; $i != ($this->pager->limit / $i); $i++) $columns = $i + 1;
      $column = 0;

      $navTags = array();
      $navTags['BACK_LINK'] = $this->pager->getBackLink();
      $navTags['SECTION_LINKS'] = $this->pager->getSectionLinks();
      $navTags['FORWARD_LINK'] = $this->pager->getForwardLink();
      $navTags['SECTION_INFO'] = $this->pager->getSectionInfo();
      $navTags['LIMIT_LINKS'] = $this->pager->getLimitLinks(TRUE);
      $navTags['LIMIT_TEXT'] = $_SESSION['translate']->it("Limits");

      $listTags['NAVIGATION'] = $GLOBALS['core']->processTemplate($navTags, "photoalbum", "photos/navigation.tpl");
      $listTags['LIST_ITEMS'] = NULL;

      while($photo = $photos->fetchrow(DB_FETCHMODE_ASSOC)) {
	$column ++;
	$cellTags = array();
	$cellTags['ID'] = $photo['id'];
	$cellTags['ALBUM'] = $photo['album'];
	$cellTags['TNNAME'] = $photo['tnname'];
	$cellTags['TNWIDTH'] = $photo['tnwidth'];
	$cellTags['TNHEIGHT'] = $photo['tnheight'];
	$cellTags['SHORT'] = $photo['label'];

	$links = array();

	if($_SESSION['OBJ_user']->allow_access("photoalbum", "edit_photo")) {
	  $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=editPhoto&amp;PHPWS_Photo_id=" . $photo['id'] . "\">" . $_SESSION['translate']->it("Edit") . "</a>";
	}

	if($_SESSION['OBJ_user']->allow_access("photoalbum", "delete_photo")) {
	  $links[] = "<a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=deletePhoto&amp;PHPWS_Photo_id=" . $photo['id'] . "\">" . $_SESSION['translate']->it("Delete") . "</a>";	
	}

	$cellTags['EDIT_DELETE_LINKS'] = implode("&#160;|&#160;", $links);

	if($column == 1) {
	  $listTags['LIST_ITEMS'] .= "<tr>" . $GLOBALS['core']->processTemplate($cellTags, "photoalbum", "photos/cell.tpl");
	} else if($column == $columns) {
	  $listTags['LIST_ITEMS'] .= $GLOBALS['core']->processTemplate($cellTags, "photoalbum", "photos/cell.tpl") . "</tr>\n";
	  $column = 0;
	} else {
	  $listTags['LIST_ITEMS'] .= $GLOBALS['core']->processTemplate($cellTags, "photoalbum", "photos/cell.tpl");
	}
      }

      if($column != 0) {
	$listTags['LIST_ITEMS'] .= "<td colspan=\"" . ($columns - $column) . "\">&#160;</td></tr>";
      }
    } else {
      $listTags['LIST_ITEMS'] = "<tr><td colspan=\"$columns\">" . $_SESSION['translate']->it("No photos were found for this album.") . "</td></tr>";

      if($_SESSION['OBJ_user']->allow_access("photoalbum", "add_photo")) {
	$listTags['LIST_ITEMS'] .= "<tr><td colspan=\"$columns\"><a href=\"./index.php?module=photoalbum&amp;PHPWS_Album_op=new\">" . $_SESSION['translate']->it("Add a photo") . "</a></td></tr>";
      }
    }

    $this->pager->cleanUp();
    return $GLOBALS['core']->processTemplate($listTags, "photoalbum", "photos/list.tpl");
  }

  function _photo($op) {
    if(isset($_REQUEST['PHPWS_Photo_id'])) {
      $this->photo = new PHPWS_Photo($_REQUEST['PHPWS_Photo_id']);
      $_REQUEST['PHPWS_Photo_op'] = $op;
    }
  }

  function _new() {
    $this->photo = new PHPWS_Photo;
    $_REQUEST['PHPWS_Photo_op'] = "edit";
  }

  function action() {
    if(PHPWS_Message::isMessage($_SESSION['PHPWS_AlbumManager']->message)) {
      $_SESSION['PHPWS_AlbumManager']->message->display();
    }

    switch($_REQUEST['PHPWS_Album_op']) {
    case "new":
      $this->_new();
      break;

    case "edit":
      $title = $_SESSION['translate']->it("Edit Album");
      $content = $this->_edit();
      break;

    case "save":
      $this->_save();
      break;

    case "delete":
      $this->_delete();
      break;

    case "view":
      $title = $_SESSION['translate']->it("Photo Album") . ": " . $this->getLabel();
      $content = $this->_view();
      break;

    case "viewPhoto":
      $this->_photo("view");
      break;

    case "editPhoto":
      $this->_photo("edit");
      break;

    case "deletePhoto":
      $this->_photo("delete");
      break;
    }

    if(isset($content)) {
      $GLOBALS['CNT_photoalbum']['title'] = $title;
      $GLOBALS['CNT_photoalbum']['content'] .= $content;
    }
  }
}

?>