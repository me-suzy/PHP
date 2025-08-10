<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir."mod/linkman/boost/install.sql", 1, 1)){

  $search['module'] = "linkman";
  $search['search_class'] = "PHPWS_Linkman";
  $search['search_function'] = "search";
  $search['search_cols'] = "title, url, description, datePosted";
  $search['view_string'] = "&amp;LMN_op=visitLink&amp;LMN_id=";
  $search['show_block'] = 1;

  if(!$GLOBALS['core']->sqlInsert($search, "mod_search_register"))
    $content .= "Problem registering search<br />";

  $content .= "All Link Manager tables successfully written.<br />";

  CLS_help::setup_help("linkman");
  $status = 1;
} else {
  $content .= "There was a problem writing to the database.<br />";
  $status = 0;
}

?>