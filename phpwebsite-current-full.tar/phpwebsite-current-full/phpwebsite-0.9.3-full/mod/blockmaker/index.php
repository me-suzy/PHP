<?php

/**
 * Main switch for Block Maker module, all operations 
 * pass though this switch.
 *
 * @version $Id: index.php,v 1.11 2003/07/10 13:39:36 steven Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu, steven@NOSPAM.tux.appstate.edu>
 * @package Block Maker
 */

if(!isset($GLOBALS['core'])) {
  header("Location: ../..");
  exit();
}

/* Default block activity */
define("BLK_DEF_BLOCK_ACT", 0);

/* start switch */
if (isset($_REQUEST['BLK_block_op'])){
  switch($_REQUEST['BLK_block_op']){
  case "block_menu":
    PHPWS_BlockActions::blockMenu();
    break;
    
  case "menu_select": 
    PHPWS_BlockActions::menuSelect();
    break;
    
  case "insert_block":
    PHPWS_BlockActions::insertBlock();
    break;
    
  case "update_block":
    PHPWS_BlockActions::updateBlock();
    break;
    
  case "blockAction":
    PHPWS_BlockActions::blockAction();
    break;
  }
  /* end switch */
}

/* displays blocks if they are active and allowed to be viewed */
PHPWS_BlockActions::showBlocks();

?>