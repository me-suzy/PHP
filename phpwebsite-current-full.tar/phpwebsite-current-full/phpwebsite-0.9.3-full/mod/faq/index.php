<?php
if (!isset($GLOBALS['core'])){
  header("location:../../");
  exit();
}

/**
 * @version $Id: index.php,v 1.7 2003/07/10 13:07:36 matt Exp $
 * @author Darren Greene <dg49379@NOSPAM.appstate.edu>
 *
 */

if(isset($_REQUEST["module"]) && $_REQUEST["module"] == "faq") {
  require_once (PHPWS_SOURCE_DIR . "/mod/faq/conf/faq.php");

  if(!isset($GLOBALS["CNT_faq_body"])) {
   $GLOBALS["CNT_faq_body"] = array("content"=>NULL);
  }

  if(!isset($_SESSION["SES_FAQ_STATS"])) {
    $_SESSION["SES_FAQ_STATS"] = new PHPWS_FaqStats();
  }

  /* Check for PHPWS_FAQ operation */
  if(isset($_REQUEST["FAQ_op"]) || isset($_REQUEST["FAQ_adv"]) || isset($_REQUEST["FAQ_Stats_op"])) {
     $_SESSION["SES_FAQ_MANAGER"]->action(); 
  }

  /* Check for PHPWS_Manager operation */
  if(isset($_REQUEST["FAQ_MAN_OP"])) {
    $_SESSION["SES_FAQ_MANAGER"]->menu();
    $_SESSION["SES_FAQ_MANAGER"]->managerAction();
  } else if(isset($_REQUEST["FAQ_STATS_MAN_OP"])) {
      $_SESSION["SES_FAQ_MANAGER"]->menu();
      $_SESSION["SES_FAQ_STATS"]->managerAction();
  }
}
?>