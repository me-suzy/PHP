<?php
/**
 * Uninstall file for PhatForm v2
 *
 * @version $Id: uninstall.php,v 1.5 2003/03/25 21:09:58 matt Exp $
 */
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

/* Remove any dynamic tables */
$sql = "SELECT id FROM mod_phatform_forms WHERE saved='1'";
$result = $GLOBALS["core"]->getAll($sql, TRUE);
if(sizeof($result) > 0) {
  foreach($result as $form) {
    $result = $GLOBALS["core"]->getAll("SELECT * FROM mod_phatform_form_" . $form["id"], TRUE);
    $sql = "DROP TABLE mod_phatform_form_" . $form["id"];
    $GLOBALS["core"]->query($sql, TRUE);

    if(sizeof($result) > 0) {
      $sql = "DROP TABLE mod_phatform_form_" . $form["id"] . "_seq";
      $GLOBALS["core"]->query($sql, TRUE);
    }
  }
  $content .= "Removed all dynamic phatform tables successfully!";
}

if ($GLOBALS['core']->sqlImport($GLOBALS['core']->source_dir . "mod/phatform/boost/uninstall.sql", 1, 1)) {
  $content .= "All phatform static tables successfully removed.<br />";
  $content .= "Removing images directory " . $GLOBALS['core']->source_dir . "images/phatform<br />";
  system("rm -rf " . $GLOBALS['core']->home_dir . "images/phatform", $temp);
  $status =1;

  if(isset($_SESSION["OBJ_approval"]))
    $_SESSION["OBJ_approval"]->unregister_module("phatform");

  if(isset($_SESSION["OBJ_search"]))
    $GLOBALS["core"]->sqlDelete("mod_search_register", "module", "phatform");

} else {
  $content .= "There was a problem accessing the database.<br />";
  $status = 0;
}

?>