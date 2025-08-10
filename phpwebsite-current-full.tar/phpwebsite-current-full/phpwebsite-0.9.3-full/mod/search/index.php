<?php

/**
 * index.php
 *
 * Main Control switch for the search module
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu, steven@NOSPAM.tux.appstate.edu>
 * @version $Id: index.php,v 1.4 2003/07/10 13:40:11 steven Exp $
 */

if(!isset($GLOBALS['core'])) {
  header("Location: ../..");
  exit();
}

define("SEA_DEF_RESULT_LIMIT", 10);
$CNT_search_results['content'] = NULL;

if (isset($_REQUEST['SEA_search_op'])){
  /* begin switch */

  switch($_REQUEST['SEA_search_op']){
  case "search":
    $_SESSION['OBJ_search']->search();
    break;

  case "continue":
    $_SESSION['OBJ_search']->show_results();
    break;
   
  case "search_form":
    $_SESSION['OBJ_search']->search_form();
    break;
  }
  /* end switch */
}
$sql = "SELECT show_block FROM " . $GLOBALS['core']->tbl_prefix . "mod_search_register WHERE module='" . $GLOBALS['module'] . "' AND show_block='1'";
$module_result = $GLOBALS['core']->query($sql);

if($module_result->numrows()){
  $_SESSION['OBJ_search']->show_search_block($GLOBALS['module']);
}

?>