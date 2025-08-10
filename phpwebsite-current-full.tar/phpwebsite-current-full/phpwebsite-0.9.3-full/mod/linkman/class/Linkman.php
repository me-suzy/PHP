<?php

/**
 * Link Manager module master class
 *
 * @version $Id: Linkman.php,v 1.21 2003/06/27 15:25:24 steven Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Link Manager
 */
class PHPWS_Linkman {

  /**
   * Holder variable for link content
   *
   * @var    array
   * @access private
   */
  var $_linkListContent;

  /**
   * Current link being edited
   *
   * @var    object
   * @access public
   */
  var $currentLink;

  /**
   * Constructor for the linkman class
   *
   * Builds array of links in the database and grabs settings
   */
  function PHPWS_Linkman() {
    $this->_linkListContent = array();
    $this->currentLink = NULL;
  }
 
  /**
   * admin menu
   */
  function adminMenu() {
    //$title = $_SESSION['translate']->it("Link Manager Admin Menu");

    $template['LIST_LINKS'] = "<a href=\"./index.php?module=linkman&amp;LMN_op=adminMenuAction&amp;LMN_listLinks=1\">" . $_SESSION['translate']->it("List Links") . "</a>";
    $template['ADD_LINK'] = "<a href=\"./index.php?module=linkman&amp;LMN_op=adminMenuAction&amp;LMN_addLink=1\">" . $_SESSION['translate']->it("Add Link") . "</a>";

    $content = $GLOBALS['core']->processTemplate($template, "linkman", "adminMenu.tpl");

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_linkman");
    $GLOBALS['CNT_linkman']['content'] = $content;
  }

  /**
   * admin menu action
   */
  function adminMenuAction() {
    if($_SESSION['OBJ_user']->allow_access("linkman")) {
      $this->adminMenu();
      
      if(isset($_REQUEST['LMN_addLink'])) {
	$title = $_SESSION['translate']->it("Add A Link");
	$this->currentLink = new PHPWS_Link;
	$content = $this->currentLink->link("add");
      } else {
	$title = $_SESSION['translate']->it("Links Database") . "&#160;&#160;" . CLS_help::show_link("linkman", "main");
	$content = $this->linkList();
      }

      //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_linkman")
      $GLOBALS['CNT_linkman']['content'] .= "<h3>$title</h3>$content";
    } else {
      $message = $_SESSION['translate']->it("You do not have access to administrate links.");
      $error = new PHPWS_Error("linkman", "PHPWS_Linkman::linkListAction()", $message, "continue", 0);
      $error->message("CNT_linkman", $_SESSION['translate']->it("Access Denied"));
    }
  }

  /**
   * link list
   *
   * Flat out lists all the links in the database in the order provided in settings
   */
  function linkList($paging = FALSE) {
    if(!$paging) {
      $this->_linkListContent = array();
      $sql = "SELECT id FROM " . $GLOBALS['core']->tbl_prefix . "mod_linkman_links WHERE new='0'";
      $this->_linkListContent = $GLOBALS['core']->getCol($sql);
    }

    return $this->listLinks();
  }

  function listLinks() {
    $linkInfo = PHPWS_Array::paginateDataArray($this->_linkListContent, "./index.php?module=linkman&#38;LMN_op=linkListAction", 10, 1, array("<b> [", "] </b>"), NULL, 20, TRUE);
  
    $content = "<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"1\">";
    $content .= "<tr class=\"bg_dark\"><td width=\"5%\">" . $_SESSION['translate']->it("ID") . "</td>";
    $content .= "<td width=\"15%\">" . $_SESSION['translate']->it("Title") . "</td>";
    $content .= "<td>" . $_SESSION['translate']->it("URL") . "</td>";
    $content .= "<td width=\"35%\" align=\"center\">" . $_SESSION['translate']->it("Action") . "</td></tr>";

    if(is_array($linkInfo[0]) && sizeof($linkInfo[0]) > 0) {
      $sql = "SELECT id, title, url, active FROM " . $GLOBALS['core']->tbl_prefix . "mod_linkman_links WHERE ";
      foreach($linkInfo[0] as $id) {
	$sql .= "id='$id' OR ";
      }
      $sql = substr($sql, 0, strlen($sql)-4);
      $linkResult = $GLOBALS['core']->getAll($sql);

      $hiddens = array("module"=>"linkman",
		       "LMN_op"=>"linkListAction");
      $highlight = NULL;
      foreach($linkResult as $key => $link) {
	$content .= "<tr $highlight>";
	
	$content .= "<td width=\"5%\">" . $link['id'] . "</td>";
	$content .= "<td width=\"15%\">" . $link['title'] . "</td>";
	$content .= "<td><a href=\"" . $link['url'] . "\" target=\"_blank\">" . $link['url'] . "</a></td>";
	$content .= "<td align=\"center\">";
	
	$hiddens['LMN_id'] = $link['id'];
	$elements[0] = PHPWS_Form::formHidden($hiddens);
	$elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Edit Link"), "LMN_editLink") . "&#160;&#160";
	$elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Delete Link"), "LMN_deleteLink") . "&#160;&#160";
	
	if($link['active']) {
	  $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Deactivate"), "LMN_setActivity") . "&#160;&#160";
	} else {
	  $elements[0] .= PHPWS_Form::formSubmit($_SESSION['translate']->it("Activate"), "LMN_setActivity") . "&#160;&#160";
	}
	
	$content .= PHPWS_Form::makeForm("LMN_linkListAction" . $link['id'], "index.php", $elements, "post", NULL, NULL);
	$content .= "</td></tr>";
	
	$GLOBALS['core']->toggle($highlight, "class=\"bg_medium\"");
      }

      $content .= "</table><br /><br />";
      $content .= $linkInfo[1] . "<br />";
      $content .= $linkInfo[2] . "&#160;" . $_SESSION['translate']->it("Links") . "<br /><br />";
    } else {
      $content .= "<tr><td colspan=\"4\">" . $_SESSION['translate']->it("There are no links in the database.") . "</td></tr></table>";
    }

    return $content;
  }

  function linkListAction() {
    if($_SESSION['OBJ_user']->allow_access("linkman")) {
      $this->adminMenu();
      
      if(isset($_REQUEST['LMN_id'])) {
	$this->currentLink = new PHPWS_Link($_REQUEST['LMN_id']);
      }

      $paging = TRUE;
      if(isset($_REQUEST['LMN_editLink'])) {
	$title = $_SESSION['translate']->it("Edit A Link");
	$content = $this->currentLink->link("edit");
	//$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_linkman");
	$GLOBALS['CNT_linkman']['content'] .= "<h3>$title</h3>$content";
      } else if(isset($_REQUEST['LMN_deleteLink'])) {
	$this->currentLink->deleteLink();
	if(!isset($_REQUEST['LMN_yes']) && !isset($_REQUEST['LMN_no'])) {
	  return;
	}
      } else if(isset($_REQUEST['LMN_setActivity'])) {
	$this->currentLink->setActivity();
	$paging = FALSE;
      }
      
      $title = $_SESSION['translate']->it("Links Database");
      $content = $this->linkList($paging);
      
      //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_linkman");
      $GLOBALS['CNT_linkman']['content'] .= "<h3>$title</h3>$content";    
    } else {
      $message = $_SESSION['translate']->it("You do not have access to administrate links.");
      $error = new PHPWS_Error("linkman", "PHPWS_Linkman::linkListAction()", $message, "continue", 0);
      $error->message("CNT_linkman", $_SESSION['translate']->it("Access Denied"));
    }
  }

  function visitLink() {
    if(isset($_REQUEST['LMN_id'])) {
      $this->currentLink = new PHPWS_Link($_REQUEST['LMN_id']); 
      $id = $this->currentLink->id;
      $sql = "UPDATE " . $GLOBALS['core']->tbl_prefix . "mod_linkman_links SET hits=hits+1 WHERE id='$id'";
      $GLOBALS['core']->query($sql);
      $header = $this->currentLink->url;
      header("Location: $header");
      exit();
    } else {
      exit("No ID passed to PHPWS_Linkman::visitLink");
    }
  }

  /**
   * user menu
   */
  function userMenu() {
    //$title = $_SESSION['translate']->it("Link Manager User Menu");

    $hiddens = array("module"=>"linkman", "LMN_op"=>"userMenuAction");
    $elements[0] = PHPWS_Form::formHidden($hiddens);

    $categories = $_SESSION['OBJ_fatcat']->showSelect(NULL, "single", NULL, 'linkman', TRUE, FALSE);
    if(isset($categories)){
      $template['CATEGORY_LIST'] = $categories;
      $template['CATEGORY_SUBMIT'] = PHPWS_Form::formSubmit($_SESSION['translate']->it("View by Category"), "LMN_category");
    }

    $template['TOP_LINKS'] = "<a href=\"./index.php?module=linkman&amp;LMN_op=userMenuAction&amp;LMN_topLinks=1\">" . $_SESSION['translate']->it("Top Ten") . "</a>";
    $template['RECENT'] = "<a href=\"./index.php?module=linkman&amp;LMN_op=userMenuAction&amp;LMN_recent=1\">" . $_SESSION['translate']->it("Most Recent") . "</a>";
    $template['ADD_LINK'] = "<a href=\"./index.php?module=linkman&amp;LMN_op=userMenuAction\">" . $_SESSION['translate']->it("Submit Link") . "</a>";

    $elements[0] .= $GLOBALS['core']->processTemplate($template, "linkman", "userMenu.tpl");

    $content = PHPWS_Form::makeForm("LMN_userMenu", "index.php", $elements, "post", NULL, NULL);

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_linkman");
    $GLOBALS['CNT_linkman']['content'] = $content;
  }

  /**
   * user menu action
   */
  function userMenuAction() {
    $this->userMenu();

    if(isset($_REQUEST['LMN_topLinks'])) {
      $title = $_SESSION['translate']->it("Top Ten Links");
      $content = $this->userList("top");
    } else if(isset($_REQUEST['LMN_recent'])) {
      $title = $_SESSION['translate']->it("Recent Links");
      $content = $this->userList("recent");
    } elseif (isset($_REQUEST['LMN_category'])){
      if (isset($_REQUEST['fatSelect']['linkman'])){
	$cat_id = $_REQUEST['fatSelect']['linkman'];
	$cat_title = PHPWS_Fatcat_Category::getTitle($cat_id);
      } else
	$cat_title = $_SESSION["translate"]->it("No Categories");

      $title = $_SESSION['translate']->it("Category Links") . ": $cat_title";
      $content = $this->userList("category");
    } else {
      $title = $_SESSION['translate']->it("Submit A Link");
      $this->currentLink = new PHPWS_Link;
      $content = $this->currentLink->link("user");
    }

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_linkman");
    $GLOBALS['CNT_linkman']['content'] .= "<h3>$title</h3>$content";
  }

  function userList($mode) {
    $i = 1;
    $highlight = NULL;
    $paging = FALSE;

    $content = "<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"1\">";
    $content .= "<tr class=\"bg_dark\">";
    if ($mode != 'category')
      $content .= "<td width=\"5%\">&#35;</td>";
    $content .= "<td width=\"30%\"><b>" . $_SESSION['translate']->it("Title") . "</b></td>";
    $content .= "<td><b>" . $_SESSION['translate']->it("URL") . "</b></td>";

    if ($mode != 'category'){

      unset($_SESSION['LinkPage']);
      if($mode == "top") {
	$orderBy = "hits DESC ";
	$content .= "<td width=\"10%\" align=\"center\"><b>" . $_SESSION['translate']->it("Hits") . "</b></td></tr>";
      } elseif($mode == "recent") {
	$orderBy = "datePosted DESC ";
	$content .= "<td width=\"10%\" align=\"center\"><b>" . $_SESSION['translate']->it("Date") . "</b></td></tr>";
      }

      $linkResult = $GLOBALS['core']->sqlSelect("mod_linkman_links", array('new'=>'0', 'active'=>'1'), NULL, $orderBy, NULL, NULL, 10);
    } else {
      if (isset($_REQUEST['fatSelect']['linkman'])){
	$cat_id = $_REQUEST['fatSelect']['linkman'];
	$cat_title = PHPWS_Fatcat_Category::getTitle($cat_id);
      } else {
	$cat_title = $_SESSION["translate"]->it("No Categories");
	$cat_id = 0;
      }

      if (!isset($_SESSION['LinkPage']) || $_SESSION['LinkPage']->cat_id != $cat_id){
	$elements = PHPWS_Fatcat::getModuleElements('linkman', NULL, $cat_id);
	foreach ($elements as $catID=>$catInfo)
	  $result[] = $GLOBALS['core']->getRow("SELECT * FROM " . $GLOBALS['core']->tbl_prefix . "mod_linkman_links WHERE id='" . $catInfo['module_id'] . "' AND active='1' AND new='0' ORDER BY hits DESC");

	$_SESSION['LinkPage'] = new PHPWS_Pager;
	$_SESSION['LinkPage']->cat_id = $catID;
	$_SESSION['LinkPage']->setLinkBack("./index.php?module=linkman&amp;LMN_op=userMenuAction&amp;LMN_category=1&amp;fatSelect[linkman]=$cat_id");
	$_SESSION['LinkPage']->makeArray(TRUE);
	$_SESSION['LinkPage']->setData($result);
      }

      $_SESSION['LinkPage']->pageData();
      $linkResult = $_SESSION['LinkPage']->getData();
      $paging = TRUE;
      $content .= "<td width=\"10%\" align=\"center\"><b>" . $_SESSION['translate']->it("Hits") . "</b></td></tr>";
    }

    
    if(count($linkResult)) {
      foreach ($linkResult as $link) {
	$content .= "<tr $highlight>";

	if ($mode != 'category')
	  $content .= "<td width=\"5%\">" . $i . "</td>";

	$content .= "<td width=\"15%\">" . $link['title'] . "</td>";
	$content .= "<td><a href=\"./index.php?module=linkman&amp;LMN_op=visitLink&amp;LMN_id=" . $link['id'] . "\" target=\"_blank\">" . $link['url'] . "</a></td>";
	
	if($mode == "top" || $mode =="category") {	
	  $content .= "<td align=\"center\">" . $link['hits'] . "</td></tr>";  
	} else if($mode == "recent") {
	  $content .= "<td align=\"center\">" . $link['datePosted'] . "</td></tr>";  
	}
	
	$i++;
      }
    } else {
      $content .= "<tr><td colspan=\"4\">" . $_SESSION['translate']->it("There are no links in the database.") . "</td></tr>";
    }

    $content .= "</table>";
    if ($paging){
      $content .= "<hr /><div align=\"center\">\n";
      $content .= $_SESSION['LinkPage']->getBackLink();
      $content .= $_SESSION['LinkPage']->getSectionLinks();
      $content .= $_SESSION['LinkPage']->getForwardLink() . "<br />";
      $content .= $_SESSION['LinkPage']->getSectionInfo() ."<br /><br />";
      $content .= $_SESSION['LinkPage']->getLimitLinks() . " ";
      $content .= $_SESSION["translate"]->it("Limit");
      $content .= "</div>\n";
    }
    return $content;
  }

  function search($where) {
    $sql = "SELECT id, title FROM " . $GLOBALS['core']->tbl_prefix . "mod_linkman_links " . $where . " AND new='0'";
    $linkResult = $GLOBALS['core']->query($sql);
    if($linkResult->numrows()) {
      while($link = $linkResult->fetchrow(DB_FETCHMODE_ASSOC)) {
	$results[$link['id']] = $link['title'];
      }
      return $results;
    } else {
      return FALSE;
    }
  }
}

?>