<?php

/**
 * Class:  PHPWS_Menuman
 *
 * Controller class for the menu manager module.
 *
 * @version $Id: Menuman.php,v 1.23 2003/06/30 19:17:13 steven Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Menu Manager
 */
class PHPWS_Menuman {

  /**
   * all the current PHPWS_Menu(s) on the db
   * @var array
   */
  var $menus;

  /**
   * options available for indention scheme
   * @var array
   */
  var $indent_options;

  /**
   * options available for activity colors
   * @var array
   */
  var $color_options;

  /**
   * options available for diplaying
   * @var array
   */
  var $display_options;

  /**
   * current menuman image directory
   * @var string
   */
  var $upload_directory;

  /**
   * allowed image types for menuman
   * @var string
   */
  var $allowed_types;

  /**
   * flag for controlling advanced edit features
   * @var boolean
   */
  var $uber_edit;

  /**
   * available pagemaster pages
   * @var array
   */
  var $pageOptions;

  /**
   * Class constructor
   *
   * Initializes class variables then creates the menu array
   *
   * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
   * @param none
   * @return none
   */
  function PHPWS_Menuman() {
    /* set path to the configuration file for the menu manager module */
    include($GLOBALS['core']->source_dir . "mod/menuman/conf/config.php");

    /* set all configurable options */
    $this->indent_options = $indent_options;
    $this->color_options = $color_options;

    $disp_option1 = $_SESSION['translate']->it("Internal Page");
    $disp_option2 = $_SESSION['translate']->it("In A Box");
    $disp_option3 = $_SESSION['translate']->it("New Window");
    $disp_option4 = $_SESSION['translate']->it("External Page");
    $this->display_options = array(1=>"$disp_option1",3=>"$disp_option3",4=>"$disp_option4");

    $this->upload_directory = $upload_directory;
    $this->allowed_types = $allowed_types;
    $this->uber_edit = 0;

    $sql = "SELECT menu_id FROM " . $GLOBALS['core']->tbl_prefix . "mod_menuman_menus";
    $menu_result = $GLOBALS['core']->getCol($sql);

    /* initialize the menu manager array of menus */
    $this->menus[0] = new PHPWS_Menu;
    foreach($menu_result as $menu_id) {
      $this->menus[$menu_id] = new PHPWS_Menu($menu_id);
    }

    $this->refreshPageOptions();
  } // END FUNC PHPWS_Menuman


  /* get functions */
  function get_indent_options(){return $this->indent_options;}
  function get_color_options(){return $this->color_options;}
  function get_display_options(){return $this->display_options;}
  function get_upload_directory(){return $this->upload_directory;}
  function get_allowed_types(){return $this->allowed_types;}

  /**
   * refreshPageOptions
   *
   * refreshes the list of available pagemaster pages
   */
  function refreshPageOptions() {
    if($GLOBALS['core']->moduleExists("pagemaster")) {
      $sql = "SELECT id, title FROM " . $GLOBALS['core']->tbl_prefix . "mod_pagemaster_pages";
      $page_result = $GLOBALS['core']->query($sql);
      $this->pageOptions[0] = "";
      while($page = $page_result->fetchrow(DB_FETCHMODE_ASSOC)) $this->pageOptions[$page['id']] = $page['title'];
    }
  }

  /**
   * get_modules_allowed
   *
   * listing of allowed modules
   *
   * @return array listing
   */
  function get_modules_allowed() {
    $modulesAllowed = $GLOBALS['core']->listModules();

    $text = $_SESSION['translate']->it("Select Modules Allowed");
    $options = array($text, 
		     "----------------------------------------------");
    array_push($options, "home");
    $modulesAllowed = array_merge($options, $modulesAllowed);

    return $modulesAllowed;
  }


  /**
   * get_menu_list
   *
   * Builds a list of menus with id as the key
   * @return array list of the menus in the database
   */
  function get_menu_list() {
    if(is_array($this->menus)) {
      $menu_list = array();
      foreach($this->menus as $id => $menu) {
	$menu_list[$id] = $menu->menu_title;
      }
    } else {
      $menu_list[0] = $_SESSION['translate']->it("No Menus");
    }
    return $menu_list;
  } // END FUNC get_menu_list


  /**
   * error_message
   *
   * handles displaying of error messages
   *
   * @param string $message the message to be displayed
   */
  function error_message($message) {
    $title = "<font color=\"#ff0000\">" . $_SESSION['translate']->it("Error") . "!</font><br />";
    $content = $message;

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_options");
    //Phpws_Error::Phpws_Error($message); 
    $GLOBALS['CNT_menuman_options']['content'] .= $title . $content;
  } // END FUNC error_message


  /**
   * main_menu
   *
   * Creates and displays forms for the menu bar
   */
  function main_menu() {
    //$title = $_SESSION['translate']->it("Main Menu");

    $template['MENU_LIST'] = "<a href=\"./index.php?module=menuman&amp;MMN_menuman_op=adminMenu&amp;listMenus=1\">" . $_SESSION['translate']->it("List Menus") . "</a>";
    $template['MENU_IMAGE'] = "<a href=\"./index.php?module=menuman&amp;MMN_menuman_op=adminMenu&amp;imageManage=1\">" . $_SESSION['translate']->it("Image Manager") . "</a>";

    if($_SESSION['OBJ_user']->allow_access("menuman", "create_menu")) {
      $template['MENU_CREATE'] = "<a href=\"./index.php?module=menuman&amp;MMN_menuman_op=adminMenu&amp;createMenu=1\">" . $_SESSION['translate']->it("Create Menu") . "</a>";
    }

    $content = $GLOBALS['core']->processTemplate($template, "menuman", "adminMenu.tpl");

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_options");
    $GLOBALS['CNT_menuman_options']['content'] = $content;
  } // END FUNC main_menu

  /**
   * list_menus
   *
   * Action taken when the list menus button is hit from the main menu,
   * creates a list displaying all the menus and what actions can be taken
   */
  function list_menus() {
    $title = $_SESSION['translate']->it("Current Menus");
    $content = "<table border=\"0\" width=\"100%\" cellpadding=\"5\" cellspacing=\"1\">\n";
    $content .= "<tr class=\"bg_dark\">\n";
    $content .= "<td width=\"5%\"><b>" . $_SESSION['translate']->it("ID") . "</b></td>\n";
    $content .= "<td width=\"40%\"><b>" . $_SESSION['translate']->it("Title") . "</b></td>\n";
    $content .= "<td width=\"20%\"><b>" . $_SESSION['translate']->it("Updated") . "</b></td>\n";
    $content .= "<td width=\"35%\" align=\"center\"><b>" . $_SESSION['translate']->it("Actions") . "</b></td>\n";

    $hiddens = array("module"=>"menuman", "MMN_menuman_op"=>"menuAction");
  
    $highlight = NULL;
    reset($this->menus);
    foreach($this->menus as $id => $value) {
      if($id != 0){
	$content .= "<tr" . $highlight . ">";
	$content .= "<td>" . $id . "</td>\n";
	$content .= "<td>" . $this->menus[$id]->menu_title . "</td>\n";
	$content .= "<td>" . date(PHPWS_DATE_FORMAT . " " . PHPWS_TIME_FORMAT, $this->menus[$id]->updated) . "</td>\n";
	$content .= "<td align=\"center\">";
 
	$hiddens['MMN_menu_id'] = $id;
	$elements[0] = $GLOBALS['core']->formHidden($hiddens);

	if($_SESSION['OBJ_user']->allow_access("menuman", "edit_menu")) {
	  $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Edit"), "MMN_editMenu") . "&#160;";
	}
	
	if($_SESSION['OBJ_user']->allow_access("menuman", "delete_menu")) {
	  $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Delete"), "MMN_deleteMenu") . "&#160;";
	}
	
	if($_SESSION['OBJ_user']->allow_access("menuman", "set_activity")) {
	  if($this->menus[$id]->menu_active) {
	    $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Deactivate"), "MMN_setActivity");
	  } else {
	    $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Activate"), "MMN_setActivity");
	  }
	}

	$content .= $GLOBALS['core']->makeForm("MMN_menuAction_" . $id, "index.php", $elements, "post", NULL, NULL);	
	$content .= "</td></tr>\n";  
	$GLOBALS['core']->toggle($highlight, " class=\"bg_light\"");
      }
    }
      
    if(count($this->menus) < 2) {
      $content .= "<tr><td colspan=\"9\">" . $_SESSION['translate']->it("There are no menus in the database at this time.") . "</td></tr>\n";
    }

    $content .= "</table>";  

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_options");
    $GLOBALS['CNT_menuman_options']['content'] .= "<h3>$title</h3>$content";
  } // END FUNC list_menus


  /**
   * image_manager
   *
   * Action for when the image manager button is hit the main menu,
   * provides management functionality for menu images
   */
  function image_manager() {
    $title = $_SESSION['translate']->it("Menu Image Manager");
    $content = "<br /><a href=\"./index.php?module=menuman&MMN_menuman_op=adminMenu\">Main</a><br /><br />";
    $content .= $_SESSION['translate']->it("Upload an image to use as a menu marker.") . "<br />\n";
    
    $elements[0] = $GLOBALS['core']->formHidden("module", "menuman");
    $elements[0] .= $GLOBALS['core']->formHidden("MMN_menuman_op", "upload_image");
    $elements[0] .= $_SESSION['translate']->it("Image") . ": " . $GLOBALS['core']->formFile("MMN_file", 40, 200) . "&#160;&#160;";
    $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Upload"));
    
    $content = $GLOBALS['core']->makeForm("MMN_upload_image", "index.php", $elements, "post", NULL, 1);
    
    $elements[0] = $GLOBALS['core']->formHidden("module", "menuman");
    $elements[0] .= $GLOBALS['core']->formHidden("MMN_menuman_op", "delete_image");

    if(is_dir($GLOBALS['core']->home_dir.$this->get_upload_directory())) {
      $image_options = $GLOBALS['core']->readDirectory($GLOBALS['core']->home_dir.$this->get_upload_directory(), FALSE, TRUE);
    } else {
      $this->error_message($_SESSION['translate']->it("The images directory set in the menuman config file could not be found this will cause all image options to fail."));
    }

    $elements[0] .= "<br />";
    $elements[0] .= $_SESSION['translate']->it("Delete an image") . ":&#160;&#160;\n";    
    $elements[0] .= $GLOBALS['core']->formSelect("MMN_image", $image_options, NULL, 1);
    $elements[0] .= "&#160;&#160;" . $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Delete"));
    
    $content .= $GLOBALS['core']->makeForm("MMN_delete_image", "index.php", $elements, "post", NULL, 1);

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_options");
    $GLOBALS['CNT_menuman_options']['content'] .= "<h3>$title</h3>$content";
  } // END FUNC image_manager


  /**
   * upload_image
   *
   * Moves uploaded files to the proper directory
   *
   * @author Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @modified Steven Levin <steven@NOSPAM.tux.appstate.edu>
   */
  function upload_image() {
    if($_FILES['MMN_file']['error'] == 4) {
      $this->error_message($_SESSION['translate']->it("No file entered."));
      return;
    }

    if(!in_array($_FILES['MMN_file']['type'], $this->allowed_types)) {
      $this->error_message($_SESSION['translate']->it("File [var1] is not an allowed file for this option.", "<b><i>" . $_FILES['MMN_file']['name'] . "</i></b>"));
      return;
    } 

    if(!file_exists($GLOBALS['core']->home_dir.$this->upload_directory.$_FILES['MMN_file']['name'])) {
      if(!$GLOBALS["core"]->fileCopy($_FILES["MMN_file"]["tmp_name"], $GLOBALS["core"]->home_dir.$this->upload_directory, $_FILES["MMN_file"]["name"], 1, 0)) {
	$this->error_message($_SESSION['translate']->it("File [var1] upload failed. Contact your system administrator.", "<b><i>" . $_FILES['MMN_file']['name'] . "</i></b>"));
	return;
      }
    } else {
      $this->error_message($_SESSION['translate']->it("File [var1] already exists!", "<b><i>" . $_FILES['MMN_file']['name'] . "</i></b>"));
      return;
    }
  } // END FUNC upload_image


  /**
   * delete_image
   *
   * If POST yes then the selected message is deleted from the database,
   * otherwise a confirmation of delete is displayed
   */
  function delete_image() {
    if(isset($_POST['MMN_yes'])) {
      if(file_exists($GLOBALS['core']->home_dir.$this->upload_directory.$_POST['MMN_image'])) {
        unlink($GLOBALS['core']->home_dir.$this->upload_directory.$_POST['MMN_image']);
      }

      $title = $_SESSION['translate']->it("Image Deleted");
      $content = $_SESSION['translate']->it("The image [var1] was successfully deleted.", "<b><i>" . $_POST['MMN_image'] . "</i></b>");

      $this->main_menu();
      //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_options");
      $GLOBALS['CNT_menuman_options']['content'] .= "<h3>$title</h3>$content";
      $this->image_manager();

    } else if(isset($_POST["MMN_no"])) {

      $this->main_menu();
      $this->image_manager();
      return;

    } else {
      $title = $_SESSION['translate']->it("Delete Image Confirmation");
      $content = $_SESSION['translate']->it("Are you sure you want delete the image [var1]?", $_POST["MMN_image"]) . "<br /><br />";

      $elements[0] = $GLOBALS['core']->formHidden("module", "menuman");
      $elements[0] .= $GLOBALS['core']->formHidden("MMN_menuman_op", "delete_image");
      $elements[0] .= $GLOBALS['core']->formHidden("MMN_image", $_POST['MMN_image']);
      $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Yes"), "MMN_yes") . "&#160;&#160;";
      $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("No"), "MMN_no");

      $content .= $GLOBALS['core']->makeForm("MMN_delete_confirm", "index.php", $elements, "post", NULL, NULL);

      //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_options");
      $GLOBALS['CNT_menuman_options']['title'] = $title;
      $GLOBALS['CNT_menuman_options']['content'] = $content;
    }
  } // END FUNC delete_image

  /**
   * set_uber_edit
   *
   * turns on/off the advanced edit features
   */
  function set_uber_edit() {
    $GLOBALS['core']->toggle($this->uber_edit);
  } // END FUNC uber_edit

  /**
   * add_module_item
   *
   * provided for other modules to add menu items to menus located within the
   * menuman module
   *
   * @param string $module name of the module adding the link
   * @param string $op_string other get commands need for proper execution
   * @param string $call_back a link back to where the call to this func is coming from
   * @param boolean $item_activity default activity for menu item
   */
  function add_module_item($module, $op_string, $call_back=NULL, $item_active=MMN_DEF_ITEM_ACT) {
    $GLOBALS['CNT_menuman_add']['title'] = $_SESSION['translate']->it("Add A Menu Link");
    $title = $_SESSION['translate']->it("Select which menu you would like to add the link to.");

    $hiddens = array("module"=>"menuman",
		     "MMN_menuman_op"=>"add_menu_item",
		     "MMN_module"=>"$module",
		     "MMN_op_string"=>"$op_string",
		     "MMN_call_back"=>"$call_back",
		     "MMN_item_active"=>"$item_active"
		     );

    $elements[0] = PHPWS_Form::formHidden($hiddens);

    if(is_array($this->menus) && count($this->menus) > 1) {
      foreach($this->menus as $id=>$value) {
	$options[$id] = $value->menu_title;
      }
      $elements[0] .= $GLOBALS['core']->formSelect("MMN_menu_id", $options) . "<br /><br />";
      $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Continue"));
      
      $content = $GLOBALS['core']->makeForm("MMN_select_menu", "index.php", $elements, "post", NULL, NULL);
    } else {
      $content = $_SESSION['translate']->it("There are no menus to choose, this process cannot continue.");
    }

    //$_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_menuman_add");
    $GLOBALS['CNT_menuman_add']['content'] = "<b>$title</b><br /><br />$content";
  } // END FUNC add_module_item
} // END CLASS PHPWS_Menuman

?>
