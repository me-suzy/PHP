<?php

/**
 * File:  index.php
 *
 * Main switch for the menu manager module.
 *
 * @version $Id: index.php,v 1.28 2003/07/10 13:39:54 steven Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Menu Manager
 */

if(!isset($GLOBALS['core'])) {
  header("Location: ../..");
  exit();
}

/* keep menu position expanded */
define("PHPWS_MENUMAN_STAY_EXPANDED", FALSE);

if(isset($_REQUEST['MMN_position'])) {
  $_SESSION['SES_parentlevel_id'] = explode(":", $_REQUEST['MMN_position']);
} else if(!isset($_SESSION['SES_parentlevel_id']) || !PHPWS_MENUMAN_STAY_EXPANDED) {
  $_SESSION['SES_parentlevel_id'] = array();
}

if (isset($_REQUEST['MMN_menuman_op'])) {
  /* begin switch */
  switch($_REQUEST['MMN_menuman_op']) {
  case "adminMenu":
    PHPWS_MenuActions::adminMenu();
    break;
    
  case "menuAction":
    PHPWS_MenuActions::menuAction();
    break;
    
  case "insert_menu":
    PHPWS_MenuActions::insertMenu();
    break;
    
  case "update_menu":
    PHPWS_MenuActions::updateMenu();
    break;
    
  case "menu_items":
    PHPWS_MenuActions::menuItemAction();
    break;
    
  case "add_menu_item":
    PHPWS_MenuActions::addMenuItem();
    break;
    
  case "move_item_up":
    PHPWS_MenuActions::moveItemUp();
    break;
    
  case "move_item_down":
    PHPWS_MenuActions::moveItemDown();
    break;
    
  case "delete_image":
    PHPWS_MenuActions::deleteImage();
   break;
   
  case "upload_image":
    PHPWS_MenuActions::uploadImage();
    break;

  case "addModuleDefault":
    PHPWS_MenuActions::addModuleDefault();
    break;

  case "siteMap":
    PHPWS_MenuActions::siteMap();
    break;
  }
  /* end switch */
}

/* build each menu and display if active */
PHPWS_MenuActions::displayMenus();
  
?>
