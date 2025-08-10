<?php
/**
 * @version $Id: install.php,v 1.2 2003/05/30 14:15:12 matt Exp $
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($status = $GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir . "mod/announce/boost/install.sql", TRUE)){
  $content .= "All Announcement tables successfully written.<br />";
  
  /* Create image directory */
  system("mkdir " . $GLOBALS["core"]->home_dir . "images/announce");
  if(is_dir($GLOBALS["core"]->home_dir . "images/announce"))
    $content .= "Announcements image directory " . $GLOBALS["core"]->home_dir . "images/announce successfully created!<br />";
  else
    $content .= "Announcements could not create the image directory: " . $GLOBALS["core"]->home_dir . "images/announce<br />You will have to do this manually!<br />";

  if(isset($_SESSION["OBJ_search"])) {
    /* Register with search module */
    $search['module'] = "announce";
    $search['search_class'] = "PHPWS_AnnouncementManager";
    $search['search_function'] = "search";
    $search['search_cols'] = "subject, summary, body";
    $search['view_string'] = "&amp;ANN_user_op=view&amp;ANN_id=";
    $search['show_block'] = 1;

    if(!$GLOBALS['core']->sqlInsert($search, "mod_search_register"))
      $content .= "Problem registering search<br />";
    else
      $content .= "Registered with Search module!<br />";
  }
} else
    $content .= "There was a problem writing to the database.<br />";

?>