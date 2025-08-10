<?php
/**
 * @version $Id: install.php,v 1.4 2003/04/03 21:50:08 darren Exp $
 * @author Darren Greene <dg49379@NOSPAM.appstate.edu>
 */

/* Make sure user is logged in as administrator */
if(!$_SESSION["OBJ_user"]->isDeity()) {
  header("location:index.php");
  exit();
}

if($GLOBALS["core"]->sqlImport($GLOBALS["core"]->source_dir . 
			       "mod/faq/boost/install.sql", TRUE)) { 

  /* get default legend from file and save to database */
  require_once("defaultScores.php");
  $queryData["score_text"] = serialize($ratings);

  if(!$GLOBALS["core"]->sqlInsert($queryData, "mod_faq_settings")) 
    $content .= "Error saving default scoring legend to database.<br />";

  /* Register with help module */
  if(!CLS_Help::setup_help("faq")) {
    $content .= "Problem registering FAQ with the help module.<br />";
  } else {
    $content .= "Added FAQ help options.<br />";
  }

  /* Register with search module */
  $search['module'] = "faq";
  $search['search_class'] = "PHPWS_FaqManager";
  $search['search_function'] = "search";
  $search['search_cols'] = "label, answer";
  $search['view_string'] = "&amp;FAQ_op=view&amp;FAQ_id=";
  $search['show_block'] = 1;

  if(!$GLOBALS["core"]->sqlInsert($search, "mod_search_register")) {
    $content .= "Problem registering with search module.<br />";
  } else {
    $content .= "Registered FAQ with search module.<br />";
  }

  $content .= "<br />All FAQ tables were successfully written to the database.<br /><br />";

  $status = 1;

} else {
  $status = 0;
  $content = "There was a problem with accessing the database.<br />";
}

?>