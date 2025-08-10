<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir."mod/comments/boost/install.sql", 1, 1)){
  $search['module'] = "comments";
  $search['search_class'] = "PHPWS_CommentActions";
  $search['search_function'] = "search";
  $search['search_cols'] = "subject, comment, author, postDate";
  $search['view_string'] = "&amp;CM_op=viewComment&amp;CM_cid=";
  $search['show_block'] = 1;

  if(!$GLOBALS['core']->sqlInsert($search, "mod_search_register"))
    $content .= "Problem registering search<br />";

  $content .= "All Comment Manager tables successfully written.<br />";

  CLS_help::setup_help("comments");
  $status = 1;
} else {
  $content .= "There was a problem writing to the database.<br />";
  $status = 0;
}

?>
