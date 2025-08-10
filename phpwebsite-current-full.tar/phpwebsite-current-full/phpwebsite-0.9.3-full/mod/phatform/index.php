<?php

/**
 * Index file for phatform module
 *
 * @version $Id: index.php,v 1.21 2003/07/10 13:40:03 steven Exp $
 */

if(!isset($GLOBALS['core'])) {
  header("Location: ../..");
  exit();
}

/* Include the phatform config file */
include(PHPWS_SOURCE_DIR . "mod/phatform/conf/phatform.php");

if($GLOBALS["module"] == "phatform") {
  $GLOBALS['CNT_phatform'] = array("title"=>PHAT_TITLE,
				   "content"=>NULL);
}

/* Look for the PHAT MAN :) */
if(isset($_REQUEST["PHAT_MAN_OP"])) {
  $_SESSION["PHAT_FormManager"]->managerAction();
  $_SESSION["PHAT_FormManager"]->action();
}

/* Check for PHAT_Form operation */
if(isset($_REQUEST["PHAT_FORM_OP"])) {
  $_SESSION["PHAT_FormManager"]->form->action();
}

/* Where's the PHAT EL? */
if(isset($_REQUEST["PHAT_EL_OP"])) {
  $_SESSION["PHAT_FormManager"]->form->element->action();
}

/* Check to see if there is a reprt operation */
if(isset($_REQUEST["PHAT_REPORT_OP"])) {
  $_SESSION["PHAT_FormManager"]->form->report->action();
}

?>