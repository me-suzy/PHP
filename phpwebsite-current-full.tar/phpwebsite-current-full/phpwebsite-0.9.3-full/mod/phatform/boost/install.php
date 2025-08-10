<?php
/**
 * Install file for PhatForm v2
 *
 * @version $Id: install.php,v 1.5 2003/05/30 14:15:11 matt Exp $
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($status = $GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir . "mod/phatform/boost/install.sql", TRUE)){
  $content .= "All PhatForm tables successfully written.<br />";
  
  /* Create image directory */
  @mkdir($GLOBALS["core"]->home_dir . "images/phatform");
  if(is_dir($GLOBALS["core"]->home_dir . "images/phatform"))
    $content .= "PhatForm images directory successfully created!<br />" . $GLOBALS["core"]->home_dir . "images/phatform<br />";
  else
    $content .= "Boost could not create the PhatForm image directory:<br />" . $GLOBALS["core"]->home_dir . "images/phatform<br />You will have to do this manually!<br />";

  @mkdir($GLOBALS["core"]->home_dir . "files/phatform");
  if(is_dir($GLOBALS["core"]->home_dir . "files/phatform")) {
    @mkdir($GLOBALS["core"]->home_dir . "files/phatform/export");
    @mkdir($GLOBALS["core"]->home_dir . "files/phatform/archive");
    $content .= "PhatForm files directory successfully created!<br />" . $GLOBALS["core"]->home_dir . "files/phatform<br />";
  } else
    $content .= "Boost could not create the PhatForm files directory:<br />" . $GLOBALS["core"]->home_dir . "files/phatform<br />You will have to do this manually!<br />";

    /* Register with search module */
  if(isset($_SESSION["OBJ_search"])) {
    $search['module'] = "phatform";
    $search['search_class'] = "PHAT_FormManager";
    $search['search_function'] = "search";
    $search['search_cols'] = "none";
    $search['view_string'] = "none";
    $search['show_block'] = 0;

    if(!$GLOBALS['core']->sqlInsert($search, "mod_search_register"))
      $content .= "Database problem registering with Search module.<br />";
    else
      $content .= "Successfully registered with Search module!<br />";
  }
} else
    $content .= "There was a problem writing to the database.<br />";

?>