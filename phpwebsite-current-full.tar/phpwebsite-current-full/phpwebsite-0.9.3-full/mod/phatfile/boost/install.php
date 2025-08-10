<?php
/*
 * @version $Id: install.php,v 1.4 2003/04/14 14:18:55 matt Exp $
 */

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if($status = $GLOBALS["core"]->sqlImport(PHPWS_SOURCE_DIR . "mod/phatfile/boost/install.sql", TRUE)) {
  $content .= "All File Manager tables successfully written.<br />";
  
  if (!is_dir(PHPWS_HOME_DIR . "files/phatfile") && is_writable(PHPWS_HOME_DIR . "files/"))
    @mkdir(PHPWS_HOME_DIR . "files/phatfile");

  if(is_dir(PHPWS_HOME_DIR . "files/phatfile")) {
    $content .= "File Manager files directory successfully created in:<br />" . PHPWS_HOME_DIR .
      "files/phatfile<br />";
  } else {
    $content .= "Boost could not create the File Manager files directory in:<br />" . PHPWS_HOME_DIR .
      "files/phatfile<br />You will have to do this manually!<br />";
  }

  if(isset($_SESSION["OBJ_search"])) {
    $search['module'] = "phatfile";
    $search['search_class'] = "PHAT_FileManager";
    $search['search_function'] = "searchFiles";
    $search['search_cols'] = "none";
    $search['view_string'] = "none";
    $search['show_block'] = 0;

    if(!$GLOBALS["core"]->sqlInsert($search, "mod_search_register")) {
      $content .= "Database problem when attempting to register with Search module.<br />";
    } else {
      $content .= "Successfully registered with Search module!<br />";
    }
  }
} else {
  $content .= "There was a problem writing to the database!<br />";
}

?>
