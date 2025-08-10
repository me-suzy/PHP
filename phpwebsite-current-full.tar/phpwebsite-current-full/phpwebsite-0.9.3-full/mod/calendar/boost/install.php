<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($status = $GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir."mod/calendar/boost/install.sql", 1, 1)){
  if (!is_dir($GLOBALS['core']->home_dir . "images/calendar"))
    @mkdir($GLOBALS['core']->home_dir . "images/calendar");

  CLS_help::setup_help("calendar");  
  if(is_dir($GLOBALS['core']->home_dir . "images/calendar"))
    $content .= "Calendar image directories successfully created!<br />";
  else
    $content .= "Calendar could not create the image directory:<br /> "
      . $GLOBALS['core']->home_dir . "images/calendar/<br />";

  /* Register with search module */
  $search['module'] = "calendar";
  $search['search_class'] = "PHPWS_Calendar";
  $search['search_function'] = "search";
  $search['search_cols'] = "title, description";
  $search['view_string'] = "&amp;calendar[view]=event&amp;id=";
  $search['show_block'] = 1;
  
  if(!$GLOBALS['core']->sqlInsert($search, "mod_search_register"))
    $content .= "Problem registering search<br />";
  else
    $content .= "Registered with Search module!<br />";

}
?>
