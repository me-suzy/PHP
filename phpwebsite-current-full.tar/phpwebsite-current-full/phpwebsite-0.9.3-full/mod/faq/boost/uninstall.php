<?php

/**
 * Uninstall file for module faq.(used by Boost)
 *
 * @version $Id: uninstall.php,v 1.6 2003/04/16 17:37:57 darren Exp $
 * @author Darren Greene <dg49379@NOSPAM.appstate.edu>
 *
 */

/* Check to see if administrator */
if(!$_SESSION["OBJ_user"]->isDeity()) {
  header("location:index.php");
  exit();
}

if($GLOBALS["core"]->sqlImport($GLOBALS["core"]->source_dir. "mod/faq/boost/uninstall.sql", 1, 1)) {
  CLS_help::uninstall_help("faq");
  $content = "Unregistered FAQ from help module.<br />";

  if(isset($_SESSION["OBJ_fatcat"])) {
    if(PHPWS_Fatcat::purge(NULL, "faq"))
      $content .= "Removed any FAQs in fatcat categories.<br />";
  }

  PHPWS_Approval::remove(NULL, "faq");

  if(isset($_SESSION["OBJ_menuman"])) {
    if($GLOBALS["core"]->sqlDelete("mod_menuman_items", "menu_item_url", "%module=faq%", "LIKE"))
      $content .= "Removed link to FAQ from menu.<br />";
  }

  if(isset($_SESSION['OBJ_search'])) {
    if($GLOBALS['core']->sqlDelete("mod_search_register", "module", "faq")) 
      $content .= "Unregistered FAQ from search module.<br />";
  }

  $content .= "<br />All FAQ tables were successfully removed from the database.<br /><br />";
  $GLOBALS['core']->killSession("SES_FAQ_STATS");

  $status = 1;
} else {
  $content = "There was a problem accessing the database.<br />";
}

?>