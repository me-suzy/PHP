<?php
/**
 * @version $Id: install.php,v 1.2 2003/04/23 17:32:40 matt Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

  if($GLOBALS["core"]->sqlImport($GLOBALS["core"]->source_dir . "mod/notes/boost/install.sql", TRUE)) {
    $content .= "All Note tables successfully written.<br />";
    CLS_help::setup_help("notes");

    $status = 1;
  } else
    $content .= "There was a problem writing to the database.<br />";

?>