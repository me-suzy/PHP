<?php

/**
 * Class: PHPWS_BlockMaker
 *
 * Controls all of the individual blocks in the database.
 *
 * @version $Id: BlockMaker.php,v 1.7 2003/06/27 14:10:46 steven Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Block Maker
 */
class PHPWS_BlockMaker {
  
  /**
   * all block objects for corresponding blocks in the db
   * @var array
   */
  var $blocks;

  /**
   * list of html allowed to be entered in a block
   * @var array
   */
  var $allowed_html;

  /**
   * block object for creating new blocks
   * @var object
   */
  var $new_block;
  
  /**
   * PHPWS_Blockmaker
   *
   * Constructor for the blockmaker class
   */
  function PHPWS_Blockmaker() {
    $this->blocks = array();
    $this->new_block = new PHPWS_Block();
    $this->reload();
  }
  

  /**
   * get_allowed_html
   *
   * Returns the allowed html list
   *
   * @return array all of the allowed html tags
   */
  function get_allowed_html() {return $this->allowed_html;}


  /**
   * get_modules_allowed
   *
   * Returns the modules allowed
   *
   * @return array all of the modules allowed to have blocks active
   */
  function get_modules_allowed() {
    $modulesAllowed = $GLOBALS['core']->listModules();

    $text = $_SESSION['translate']->it("Select Modules Allowed");
    $options = array($text, 
		     "----------------------------------------------");
    array_push($options, "home");
    $options = array_merge($options, $modulesAllowed);

    return $options;
  }


  /**
   * reload
   *
   * Reinitializes the blocks array
   */
  function reload() {
    unset($this->blocks);
    $this->blocks = array();

    $sql = "SELECT block_id FROM " . $GLOBALS['core']->tbl_prefix . "mod_blockmaker_data ORDER BY block_title";
    $blocks_result = $GLOBALS['core']->getcol($sql);

    foreach($blocks_result as $id) {
      $this->blocks[$id] = new PHPWS_Block($id);
    }
  }


  /**
   * block_menu
   *
   * Administration options for blocks
   */
  function block_menu() {
    //$title = $_SESSION['translate']->it("Block Menu");

    if($_SESSION['OBJ_user']->allow_access("blockmaker")) {
      $template['LIST_LINK'] = "<a href=\"./index.php?module=blockmaker&amp;BLK_block_op=menu_select&amp;list_blocks=1\">" . $_SESSION['translate']->it("List Blocks") . "</a>";
      $template['LIST_HELP'] = CLS_help::show_link("blockmaker", "list_blocks");
    }

    if($_SESSION['OBJ_user']->allow_access("blockmaker", "create_block")) {
      $template['NEW_LINK'] = "<a href=\"./index.php?module=blockmaker&amp;BLK_block_op=menu_select&amp;create_block=1\">". $_SESSION['translate']->it("Create A New Block") . "</a>";    
      $template['NEW_HELP'] = CLS_help::show_link("blockmaker", "create_block");
    }

    $content = $GLOBALS['core']->processTemplate($template, "blockmaker", "adminMenu.tpl");

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_blockmaker_main");

    $GLOBALS['CNT_blockmaker_main']['content'] = $content;
 }


  /**
   * list_blocks
   *
   * Lists all of the blocks
   * Provides the edit, delete and activity submit buttons
   */
  function list_blocks() {
    $title = $_SESSION['translate']->it("Current blocks");
    $content = "<table border=\"0\" width=\"100%\" cellpadding=\"5\" cellspacing=\"1\">\n";
    $content .= "<tr class=\"bg_dark\">\n";
    $content .= "<td width=\"5%\">" . $_SESSION['translate']->it("ID") . "</td>\n";    
    $content .= "<td width=\"40%\">" . $_SESSION['translate']->it("Title") . "</td>\n";
    $content .= "<td width=\"20%\">" . $_SESSION['translate']->it("Updated") . "</td>\n";
    $content .= "<td width=\"35%\" align=\"center\">" . $_SESSION['translate']->it("Actions") . "</td></tr>\n";

    $highlight = NULL;
    if(is_array($this->blocks) && (count($this->blocks) > 0)) {
      $hiddens = array("module"=>"blockmaker", "BLK_block_op"=>"blockAction");

      foreach($this->blocks as $id => $block){
	$hiddens['BLK_block_id'] = $id;
	$elements[0] = $GLOBALS['core']->formHidden($hiddens);

	$content .= "<tr" . $highlight . ">";
	$content .= "<td>" . $id . "</td>\n";
	$content .= "<td>" . $this->blocks[$id]->block_title . "</td>\n";
	$content .= "<td>" . $this->blocks[$id]->block_updated . "</td>\n";
	$content .= "<td align=\"center\">";

	if($_SESSION['OBJ_user']->allow_access("blockmaker", "edit_block")) {
	  $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Edit"), "BLK_edit") . "&#160;";
	}

	if($_SESSION['OBJ_user']->allow_access("blockmaker", "delete_block")) {
	  $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Delete"), "BLK_delete") . "&#160;";
	}
	
	if($_SESSION['OBJ_user']->allow_access("blockmaker", "block_activity")) {
	  if($this->blocks[$id]->block_active) {
	    $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Deactivate"), "BLK_activity");
	  } else{
	    $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Activate"), "BLK_activity");
	  }
	}

	$content .= $GLOBALS['core']->makeForm("BLK_blockAction_" . $id, "index.php", $elements, "post", NULL, NULL);
	$content .= "</td></tr>\n";

	$GLOBALS['core']->toggle($highlight, " class=\"bg_light\"");
      }
    } else {
      $content .= "<tr><td colspan=\"4\">" . $_SESSION['translate']->it("There are no blocks in the database at this time.") . "</td></tr>\n";
    }

    $content .= "</table>";

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_blockmaker_main");

    $listTags = array();
    $listTags['TITLE'] = $title;
    $listTags['LIST'] = $content;

    $GLOBALS['CNT_blockmaker_main']['content'] .= $GLOBALS['core']->processTemplate($listTags, "blockmaker", "list.tpl");
  }


  /**
   * error
   *
   * Handles error messages
   *
   * @param string $error_type description of the error to be displayed
   */
  function error($error_type) {
    $title = "<br /><span class=\"errortext\">" . $_SESSION['translate']->it("Error") . "</span><br />";
    $content = $error_type;

    //$_SESSION['layout']->popbox($title, $content, NULL, "CNT_blockmaker_main");
    $GLOBALS['CNT_blockmaker_main']['content'] = $title . $content; 
  }
}

?>