<?php
/**
 * Routing file for phpWebSite
 *
 * Index initializes the core and database
 *
 * @version $Id: index.php,v 1.64 2003/07/03 17:00:11 adam Exp $
 * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
 * @modified by Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @modified by Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package phpWebSite
 */

// Change to TRUE to allow DEBUG mode
define("DEBUG_MODE", FALSE);

/* Show all errors */
//error_reporting (E_ALL);

/* Security against those with register globals = on */
if (isset($_POST)){
  foreach ($_POST as $postVarName=>$nullIT) {
    unset($postVarName);
  }
}

if (isset($_GET)){
  foreach ($_GET as $getVarName=>$getValue) {
    unset($getVarName);
  }
}

if (!isset($hub_dir)) {
  $hub_dir = NULL;
}
   
loadConfig($hub_dir);

if (file_exists($hub_dir . "core/Core.php") && file_exists($hub_dir . "core/Debug.php") && !preg_match ("/:\/\//i", $hub_dir)) {
  require_once($hub_dir . "core/Core.php");

  if(DEBUG_MODE) {
    require_once("Benchmark/Timer.php");
    $PHPWS_Timer =& new Benchmark_Timer();
    $PHPWS_Timer->start();
    
    $PHPWS_Timer->setMarker("Begin Core Initialization");
  }

} else {
  exit("FATAL ERROR! Required file <b>Core.php</b> not found.");
}

if(!isset($branchName)) {
  $branchName = NULL;
}

$_SESSION["core"] = new PHPWS_Core($branchName, $hub_dir);
$GLOBALS["core"] = &$_SESSION["core"];

if(DEBUG_MODE) {
  $PHPWS_Timer->setMarker("End Core Initialization");
}

$includeList = $core->initModules();

if (isset($_SESSION["siteHash"]) && $_SESSION["siteHash"] != $core->site_hash){
     $core->killAllSessions();
     $_SESSION["siteHash"] = $core->site_hash;
}

if(!isset($_SESSION['PHPWS_Debug'])) {
  $_SESSION['PHPWS_Debug'] = new PHPWS_Debug();
}

if(DEBUG_MODE) {
  /* phpWebSite debugger */
  if($_SESSION['PHPWS_Debug']->isActive()) {
    $_SESSION['PHPWS_Debug']->displayDebugInfo(TRUE);
    
    if($_SESSION['PHPWS_Debug']->getBeforeExecution()) {
      $_SESSION['PHPWS_Debug']->displayDebugInfo(FALSE);
    }
  }
}
/***********************/

if (isset($_REQUEST["print_mode"])) {
  echo $_SESSION["print_info"][$print_queue];
  unset($_SESSION["print_info"]);
  exit();
}

// Load module index file
$current_mod_file = NULL;

foreach($includeList as $mod_title=>$current_mod_file) {
  if(DEBUG_MODE) {
    $PHPWS_Timer->setMarker("Begin $mod_title Execution");
  }

  $core->current_mod = $mod_title;

  if (is_file($current_mod_file)) {
    include_once($current_mod_file);
  }

  if(DEBUG_MODE) {
    $PHPWS_Timer->setMarker("End $mod_title Execution");
  }
}

// Preventing last mod loaded from being 'current_mod'
$core->current_mod = NULL;
$core->db->disconnect();

if(DEBUG_MODE) {
  $PHPWS_Timer->stop();

  /* phpWebSite debugger */
  if($_SESSION['PHPWS_Debug']->isActive()) {
    if($_SESSION['PHPWS_Debug']->getShowTimer()) {
      echo "<br /><font size=\"+1\">phpWebSite Timer</font><br />";
      $PHPWS_Timer->display();
      echo "<br />";
    }
    if($_SESSION['PHPWS_Debug']->getAfterExecution()) {
      $_SESSION['PHPWS_Debug']->displayDebugInfo(FALSE);
    }
    /***********************/
  }
}

$_SESSION["PHPWS_WhereWasI"] = PHPWS_WizardBag::whereami(TRUE);

function loadConfig($hub_dir){
  /* Check for config file and define source directory. */
  if(file_exists($hub_dir . "conf/config.php")){
    if(filesize($hub_dir . "conf/config.php") > 0) {
      include($hub_dir . "conf/config.php");
      define("PHPWS_SOURCE_DIR", $source_dir);
    } else {
      header("location:setup/set_config.php");
      exit();
    }
  }
  else {
    header("location:setup/set_config.php");
    exit();
  }
  
}
?>
