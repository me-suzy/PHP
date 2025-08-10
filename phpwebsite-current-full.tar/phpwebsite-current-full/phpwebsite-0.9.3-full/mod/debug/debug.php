<?php

/**
 * TODO: add printout of sql information from tables hit to queries executed
 */

/**
 * debug.php
 *
 * Main control switch and debug display
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @version $Id: debug.php,v 1.12 2003/03/12 13:45:13 matt Exp $
 */
if (!DEBUG_MODE)
     return;

if(isset($DBUG_op)) {
  $GLOBALS['CNT_debug']['title'] = $_SESSION['translate']->it("phpWebSite Debugger");
  /* Begin Switch */
  switch($DBUG_op) {
  case "admin_settings":
    $_SESSION['PHPWS_Debug']->adminSettings();
    break;
    
  case "save_settings":
    $_SESSION['PHPWS_Debug']->saveSettings();
    $_SESSION['PHPWS_Debug']->adminSettings();
    break;

  case "setActivity":
    $_SESSION['PHPWS_Debug']->setActivity();
    $GLOBALS['core']->home();
    break;
  }
  /* End Switch */
}

if($_SESSION['PHPWS_Debug']->getShowBlock()) {
  $GLOBALS['CNT_debug_block']['title'] = $_SESSION['translate']->it("Debugger");

  $hiddens = array("module"=>"debug",
		   "DBUG_op"=>"admin_settings"
		   );

  $elements[0] = $GLOBALS['core']->formHidden($hiddens);
  $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Settings"));

  $content = $GLOBALS['core']->makeForm("DBUG_block0", "index.php", $elements, "post", NULL, NULL);


  $hiddens = array("module"=>"debug",
		   "DBUG_op"=>"setActivity"
		   );

  $elements[0] = $GLOBALS['core']->formHidden($hiddens);

  if($_SESSION['PHPWS_Debug']->isActive()) {
    $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Deactivate"));
  } else {
    $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Activate"));
  }

  $content .= $GLOBALS['core']->makeForm("DBUG_block1", "index.php", $elements, "post", NULL, NULL);

  $GLOBALS['CNT_debug_block']['content'] = $content;
}

?>
