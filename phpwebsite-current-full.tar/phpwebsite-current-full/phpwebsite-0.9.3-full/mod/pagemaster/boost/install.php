<?php
/**
 * This is the Pagemaster install file for Boost
 *
 * @version $Id: install.php,v 1.5 2003/05/30 14:15:11 matt Exp $
 * @author Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if($GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir . "mod/pagemaster/boost/install.sql", TRUE)) {
  $content .= "All PageMaster tables successfully written.<br />";
  
  CLS_Help::setup_help("pagemaster");
  
  if (!is_dir($GLOBALS['core']->home_dir . "images/pagemaster"))
    @mkdir($GLOBALS['core']->home_dir . "images/pagemaster");

  if(is_dir($GLOBALS['core']->home_dir . "images/pagemaster"))
    $content .= "PageMaster image directory " . $GLOBALS['core']->home_dir . "images/pagemaster successfully created!<br />";
  else
    $content .= "PageMaster could not create the image directory: " . $GLOBALS['core']->home_dir . "images/pagemaster<br />You will have to do this manually!<br />";
  
  if(isset($_SESSION["OBJ_search"])) {
    /* Register with search module */
    $search['module'] = "pagemaster";
    $search['search_class'] = "PHPWS_PageMaster";
    $search['search_function'] = "search";
    $search['search_cols'] = "title, text"; 
    $search['view_string'] = "&amp;PAGE_user_op=view_page&amp;PAGE_id=";
    $search['show_block'] = 1;
      
    if(!$GLOBALS["core"]->sqlInsert($search, "mod_search_register"))
      $content .= "Problem registering search<br />";
    else
      $content .= "Registered with Search module!<br />";
  }
  $status = 1;
} else {
  $content .= "There was a problem writing to the database.<br />";
}

?>
