<?php
/**
 * This is a skeleton version of an installation file for boost.  Edit it to be
 * used with your module.
 *
 * $Id: install.php,v 1.4 2003/07/10 13:08:22 matt Exp $
 */

/* Make sure the user is a deity before running this script */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if($GLOBALS["core"]->version < "0.9.2-1") {
  $content .= "This module requires a phpWebSite core version of 0.9.2-1 or greater to install.<br />";
  $content .= "<br />You are currently using phpWebSite core version " . $GLOBALS["core"]->version . ".<br />";
  return;
} 

/* Import installation database and dump result into status variable */
if($status = $GLOBALS["core"]->sqlImport(PHPWS_SOURCE_DIR . "mod/skeleton/boost/install.sql", TRUE)) {
  $content .= "All skeleton tables successfully written.<br /><br />";

  /* Check for permissions and create files directory if possible */
  if (is_writable(PHPWS_HOME_DIR . "files/")) {
    if(!is_dir(PHPWS_HOME_DIR . "files/skeleton")) {
      @mkdir(PHPWS_HOME_DIR . "files/skeleton");
      if(is_dir(PHPWS_HOME_DIR . "files/skeleton")) {
	$content .= "Skeleton files directory successfully created in:<br />" . PHPWS_HOME_DIR .
	   "files/skeleton<br /><br />";
      } else {
	$content .= "Boost could not create the skeleton files directory in:<br />" . PHPWS_HOME_DIR .
	   "files/skeleton<br />You will have to do this manually!<br /><br />";
      }
    }
  } else {
    $content .= "Files directory is not writable.  Please check the permissions and re-install.<br /><br />";
    $status = 0;
  }

  /* Check for permissions and create images directory if possible */
  if (is_writable(PHPWS_HOME_DIR . "images/")) {
    if(!is_dir(PHPWS_HOME_DIR . "images/skeleton")) {
      @mkdir(PHPWS_HOME_DIR . "images/skeleton");
      if(is_dir(PHPWS_HOME_DIR . "images/skeleton")) {
	$content .= "Skeleton images directory successfully created in:<br />" . PHPWS_HOME_DIR .
	   "images/skeleton<br /><br />";
      } else {
	$content .= "Boost could not create the skeleton images directory in:<br />" . PHPWS_HOME_DIR .
	   "images/skeleton<br />You will have to do this manually!<br /><br />";
      }
    }
  } else {
    $content .= "Images directory is not writable.  Please check the permissions and re-install.<br /><br />";
    $status = 0;
  }

  /* Register with core search module */
  if(isset($_SESSION["OBJ_search"])) {
    $search['module'] = "skeleton";
    $search['search_class'] = "PHPWS_SkeletonManager";
    $search['search_function'] = "search";
    $search['search_cols'] = "none";
    $search['view_string'] = "none";
    $search['show_block'] = 0;

    if(!$GLOBALS["core"]->sqlInsert($search, "mod_search_register")) {
      $content .= "Database problem when attempting to register with Search module.<br /><br />";
    } else {
      $content .= "Successfully registered with Search module!<br /><br />";
    }
  }

} else {
  $content .= "There was a problem writing to the database!<br /><br />";
}

?>
