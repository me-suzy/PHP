<?php
/**
 * This is the uninstall file for Notes. It is used by Boost.
 *
 * @version $Id: uninstall.php,v 1.2 2003/03/25 21:09:58 matt Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

if ($GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir."mod/notes/boost/uninstall.sql", 1, 1)) {
  $content .= "All Note tables successfully removed.<br />";
  if($_SESSION["OBJ_help"])
    $_SESSION["OBJ_help"]->uninstall_help("notes");
  $status = 1;
} else
$content .= "There was a problem accessing the database.<br />";

?>