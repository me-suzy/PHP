<?php

require_once(PHPWS_SOURCE_DIR . "mod/pagemaster/class/Page.php");

/**
 * This is the PHPWS_PageMaster class.  It controls interaction and
 * organization with PHPWS_Page objects.
 *
 * @version $Id: PageMaster.php,v 1.20 2003/07/01 15:18:55 adam Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package PageMaster
 */
class PHPWS_PageMaster {
  /**
   * Array of pages from the database (key=PAGE_id, value=page title)
   * @var    array
   * @access private
   */
  var $pages;

  /**
   * The current homepage stored as a PHPWS_Page object.
   * @var    object PHPWS_PAGE
   * @access private
   */
  var $homepage;
  
  /**
   * Pagemaster constructor.
   *
   * @access public
   */
  function PHPWS_PageMaster () {
    $result = $GLOBALS["core"]->sqlSelect("mod_pagemaster_pages");
    
    if($result) {
      foreach($result as $value) {
	$this->pages[$value["id"]] = $value["title"];
	if($value["mainpage"]) {
	  $this->homepage = new PHPWS_Page($value["id"]);
	}
      }
    }
  }// END FUNC PHPWS_PageMaster()

  /**
   * Displays the main menu with main functions like "New Page" and "List Pages"
   *
   * @access public
   */
  function main_menu () {
    $bg = NULL;
	 
    $myelements[0] = $GLOBALS["core"]->formHidden("module", "pagemaster");

    if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages")) {
      $myelements[0] .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("New Page"),
        "MASTER_op[new_page]") . "&nbsp;&nbsp;&nbsp;&nbsp;";
    }

    $myelements[0] .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("List Pages"),
	"MASTER_op[list_pages]") . "&nbsp;&nbsp;&nbsp;&nbsp;";

    if($_SESSION["OBJ_user"]->allow_access("pagemaster", "set_mainpage")) {
      $myelements[0] .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Set Main Page"),
        "MASTER_op[set_main]");
    }

    $content = "<center>" . $GLOBALS["core"]->makeForm("PAGE_main_menu", "index.php", $myelements, "post", 0, 0) . "</center>";

    $GLOBALS["CNT_pagemaster"]["content"] .= $content;

    if(isset($_REQUEST["MASTER_op"]) && ($_REQUEST["MASTER_op"] == "main_menu" || isset($_REQUEST["MASTER_op"]["list_pages"]))) {
      $sql = "SELECT * FROM " . $GLOBALS["core"]->tbl_prefix . "mod_pagemaster_pages WHERE new_page='1'";
      if(!$_SESSION["OBJ_user"]->allow_access("needs_approval")) {
	$sql .= " AND created_username='" . $_SESSION["OBJ_user"]->username . "'";
      }
      $result = $GLOBALS["core"]->sqlSelect("mod_pagemaster_pages", "new_page", 1);
    }

    if(isset($result)) {
      $content = "<b>" . $_SESSION["translate"]->it("Unsaved Pages") . "</b><br />";
      $content .= "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"5\">
        <tr><td width=\"50%\" class=\"bg_dark\">" . $_SESSION["translate"]->it("Title") .
	$_SESSION["OBJ_help"]->show_link("pagemaster", "unsaved_title") .
	"</td><td width=\"30%\" align=\"center\" class=\"bg_dark\">" .
	$_SESSION["translate"]->it("Created By") .
	$_SESSION["OBJ_help"]->show_link("pagemaster", "unsaved_created") .
	"</td><td width=\"20%\" align=\"center\" class=\"bg_dark\">" .
	$_SESSION["translate"]->it("Action") .
	$_SESSION["OBJ_help"]->show_link("pagemaster", "unsaved_action") .
	"</td></tr>";

      foreach($result as $value) {
	$content .= "<td width=\"50%\"$bg>" . $value["title"] .
	  "</td><td width=\"30%\" align=\"center\"$bg>" .
	$value["created_username"] . " - " . $value["created_date"] .
	  "</td><td width=\"20%\" align=\"center\"$bg>" .
	$myelements[0] = $GLOBALS["core"]->formHidden("module", "pagemaster");
	$myelements[0] .= $GLOBALS["core"]->formHidden("PAGE_id", $value["id"]);

	if($_SESSION["OBJ_user"]->allow_access("pagemaster", "create_pages")) {
	  $myelements[0] .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Finish"),
							 "MASTER_op[finish]");
	}

	if($_SESSION["OBJ_user"]->allow_access("pagemaster", "delete_pages")) {
	  $myelements[0] .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Delete"),
							 "MASTER_op[delete_page]");
	}

	$content .=
	  $GLOBALS["core"]->makeForm("PAGE_unsaved_pages", "index.php", $myelements, "post", 0, 0) .
	  "</td></tr>";

	$GLOBALS["core"]->toggle($bg, " class=\"bg_medium\"");
      }

      $content .= "</table><br />";

      $GLOBALS["CNT_pagemaster"]["content"] .= $content;
    }
  }// END FUNC main_menu()

  /**
   * Lists the current saved pages stored in the database
   *
   * @access public
   */
  function list_pages () {
    if(isset($_REQUEST['orderby'])) {
      if($_REQUEST['orderby'] == 'title_asc') {
	$_SESSION['PM_orderby'] = "title asc";
      } elseif($_REQUEST['orderby'] == 'title_desc') {
	$_SESSION['PM_orderby'] = "title desc";
      }

      if($_REQUEST['orderby'] == 'udate_asc') {
	$_SESSION['PM_orderby'] = "updated_date asc";
      } elseif($_REQUEST['orderby'] == 'udate_desc') {
	$_SESSION['PM_orderby'] = "updated_date desc";
      }

      $GLOBALS["core"]->killSession('PM_Pager');
    }

    if(!isset($_SESSION['PM_orderby'])) {
      $_SESSION['PM_orderby'] = "updated_date desc";
    }

    if ($_SESSION['PM_orderby'] == 'title asc') {
      $titleLink = array('MASTER_op'=>'list_pages', 'orderby'=>'title_desc');
    } else {
      $titleLink = array('MASTER_op'=>'list_pages', 'orderby'=>'title_asc');
    }

    if ($_SESSION['PM_orderby'] == 'updated_date asc') {
      $dateLink = array('MASTER_op'=>'list_pages', 'orderby'=>'udate_desc');
    } else {
      $dateLink = array('MASTER_op'=>'list_pages', 'orderby'=>'udate_asc');
    }

    $bg = NULL;
    $spacer = "&nbsp;";

    if (!isset($_SESSION['PM_Pager'])) {
      $sql = "SELECT * FROM " . $GLOBALS["core"]->tbl_prefix . "mod_pagemaster_pages WHERE new_page='0'";
      if(!$_SESSION["OBJ_user"]->allow_access("needs_approval")) {
	$sql .= " AND created_username='" . $_SESSION["OBJ_user"]->username . "'";
      }
      $sql .= " ORDER BY " . $_SESSION["PM_orderby"];
      $pageData = $GLOBALS["core"]->getAll($sql);

      if(!empty($pageData)) {
	$_SESSION['PM_Pager'] = new PHPWS_Pager;
	$_SESSION['PM_Pager']->setData($pageData);
	$_SESSION['PM_Pager']->setLinkBack("index.php?module=pagemaster&amp;MASTER_op=list_pages");
      } else {
	$title = $_SESSION["translate"]->it("No Pages Found!");
	$content = $_SESSION["translate"]->it("No pages were found in the database!");
	return;
      }
    }

    $_SESSION['PM_Pager']->makeArray(TRUE);
    $_SESSION['PM_Pager']->pageData();

    $content = "<b>" . $_SESSION["translate"]->it("Current Pages") . "</b>";
    $content .= "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"5\"><tr>
      <td width=\"5%\" align=\"center\" class=\"bg_dark\">" . $_SESSION["translate"]->it("ID") .
      "</td><td width=\"40%\" class=\"bg_dark\">" .
      $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Title"), "pagemaster", $titleLink) .
      $_SESSION["OBJ_help"]->show_link("pagemaster", "current_title") .
      "</td><td width=\"10%\" align=\"center\" class=\"bg_dark\"><b>" .
      $_SESSION["translate"]->it("Mainpage") . "</b>" . 
      $_SESSION["OBJ_help"]->show_link("pagemaster", "current_mainpage") .
      "</td><td width=\"20%\" align=\"center\" class=\"bg_dark\">" .
      $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Updated"), "pagemaster", $dateLink) .
      $_SESSION["OBJ_help"]->show_link("pagemaster", "current_updated") .
      "</td><td width=\"25%\" align=\"center\" class=\"bg_dark\"><b>" .
      $_SESSION["translate"]->it("Action") . "</b>" .
      $_SESSION["OBJ_help"]->show_link("pagemaster", "current_action") .
      "</td></tr>";

    $pages = $_SESSION['PM_Pager']->getData();

    foreach($pages as $value) {
      if($this->homepage->id == $value["id"]) {
	$mainpage = "<font color=\"lime\"><b><i>" . $_SESSION["translate"]->it("CURRENT") .
	  "</i></b></font>";
      } else {
	$mainpage = " ";
      }

      $content .= "<tr><td width=\"5%\" align=\"center\"$bg>" . $value["id"] . "<td width=\"40%\"$bg>
        <a href=\"index.php?module=pagemaster&amp;PAGE_user_op=view_page&amp;PAGE_id=" .
	$value["id"] . "\">" . $value["title"] . "</a></td><td width=\"15%\" align=\"center\"$bg>" .
	$mainpage . "<td width=\"20%\" align=\"center\"$bg>" . $value["updated_date"] .
	"<td width=\"20%\" align=\"center\"$bg>";

      $myelements[0] = $GLOBALS["core"]->formHidden("module", "pagemaster");
      $myelements[0] .= $GLOBALS["core"]->formHidden("PAGE_id", $value["id"]);

      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "activate_pages")) {
	if($value["active"]) {
	  $myelements[0] .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Hide"),
							 "MASTER_op[hide_page]") . $spacer;
	} else {
	  $myelements[0] .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Show"),
							 "MASTER_op[show_page]") . $spacer;
	}
      }

      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "edit_pages")) {
	$myelements[0] .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Edit"),
						       "MASTER_op[edit_page]") . $spacer;
      }

      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "delete_pages")) {
	$myelements[0] .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Delete"),
						       "MASTER_op[delete_page]");
      }

      $content .=
	$GLOBALS["core"]->makeForm("PAGE_current_pages", "index.php", $myelements, "post", 0, 0) .
	"</td></tr>";

      $GLOBALS["core"]->toggle($bg, " class=\"bg_medium\"");
    }

    $content .= "</table>";
    $forward = $_SESSION['PM_Pager']->getForwardLink();
    $back = $_SESSION['PM_Pager']->getBackLink();
    $content .= "<div align=\"center\">" . $back . $_SESSION['PM_Pager']->getSectionInfo() .$forward .
      "<br />" .  $_SESSION['PM_Pager']->getSectionLinks() . "<br />" .
      $_SESSION['PM_Pager']->getLimitLinks() . "</div>";

    $GLOBALS["CNT_pagemaster"]["content"] .= $content;
  }// END FUNC list_pages()

  /**
   * 2 functions: Displays interface for choosing which page you want to set as the home
   * or main page.  Actually sets the main page in the database and in this PHPWS_PageMaster class.
   *
   * @access public
   */
  function set_main_page () {
    $bg = NULL;

    if(isset($_POST["PAGE_id"])) {
      if(isset($this->homepage)) {
	$this->homepage->toggle_mainpage();
      }

      $this->homepage = new PHPWS_Page($_POST["PAGE_id"]);
      $this->homepage->toggle_mainpage();
    }

    $content = "<h3>" . $_SESSION["translate"]->it("Set Main Page") . "</h3>"; 
    $result = $GLOBALS["core"]->sqlSelect("mod_pagemaster_pages", "new_page", 0);

    if($result) {
      $content .= "<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"5\"><tr>
        <td width=\"80%\" class=\"bg_medium\"><b><i>" . $_SESSION["translate"]->it("Title") .
	"</i></b></td><td align=\"center\" width=\"20%\" class=\"bg_medium\"><b><i>" .
	$_SESSION["translate"]->it("Mainpage") . "</i></b></td></tr>";

      $myelements[0] = $GLOBALS["core"]->formHidden("MASTER_op", "set_main");
      $myelements[0] .= $GLOBALS["core"]->formHidden("module", "pagemaster");

      if($_SESSION["OBJ_user"]->allow_access("pagemaster", "set_mainpage")) {
	$myelements[0] .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Select"), "select");
      }

      foreach($result as $value) {
	$content .= "<tr><td width=\"80%\"$bg>" . $value["title"] . "</td>";
	$myelements[1] = $GLOBALS["core"]->formHidden("PAGE_id", $value["id"]);
	$content .= "<td align=\"center\" width=\"20%\"$bg>";

	if($value["mainpage"]) {
	  $content .= "<font color=\"lime\"><b><i>" . $_SESSION["translate"]->it("CURRENT") .
	    "</i></b></font>";
	} else {
	  $content .= $GLOBALS["core"]->makeForm("MASTER_set_main_page", "index.php", $myelements, "post", 0, 0);
	}

	$content .= "</td></tr>";
	$GLOBALS["core"]->toggle($bg, " class=\"bg_medium\"");
      }
      $content .= "</table>";
    } else {
      $content .= $_SESSION["translate"]->it("No pages found!");
    }

    $GLOBALS["CNT_pagemaster"]["content"] .= $content;
  }// END FUNC set_main_page()

  /**
   * Simply displays the homepage.
   *
   * @access public
   */
  function show_mainpage () {
    if(isset($this->homepage)) {
      $_SESSION["SES_PM_page"] = $this->homepage;
      $_SESSION["SES_PM_page"]->view_page();
    }
  }// END FUNC show_mainpage()

  /**
   * Function used by search module to search pages
   *
   * @access public
   */
  function search($where) {
    $resultArray = array();

    $sql = "SELECT * FROM " . $GLOBALS["core"]->tbl_prefix . "mod_pagemaster_sections $where";
    $result = $GLOBALS["core"]->getAll($sql);

    if(!DB::isError($result) && is_array($result) && sizeof($result) > 0) {
      $pages = array();
      $text = array();
      foreach($result as $row) {
	$pages[] = "id='" . $row["page_id"] . "'";
	$text[$row["page_id"]] = $row["text"];
      }
      $pages = array_unique($pages);
      $pages = implode(" OR ", $pages);

      $sql = "SELECT id, title FROM " . $GLOBALS["core"]->tbl_prefix . "mod_pagemaster_pages WHERE $pages";
      $result = $GLOBALS["core"]->getAll($sql);
      if(!DB::isError($result) && is_array($result) && sizeof($result) > 0) {
	foreach($result as $row) {
	  if(isset($text[$row["id"]])) {
	    $resultArray[$row["id"]] = "<b>" . $row["title"] . "</b><br />" . $text[$row["id"]];
	  }
	}
      }
    }

    return $resultArray;
  }// END FUNC search

}// END CLASS PHPWS_PageMaster

?>