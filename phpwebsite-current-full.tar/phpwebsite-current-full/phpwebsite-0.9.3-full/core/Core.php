<?php

if (!defined("PHPWS_SOURCE_DIR"))
     define("PHPWS_SOURCE_DIR", "./");

/* Comment out the ini_set line if you want to use the system pear libs. This is always
 * commented out in cvs copy's to make it easer on developers. Make sure you chose the 
 * correct OS and only use one of the lines NEVER use both! */

/*******************************************************************************************
 *                         Do NOT edit anything above this point!                          *
 ********************************** BEGIN - USER EDIT **************************************/

/* This line is for *nix/linux environments */
ini_set("include_path", ".:" . PHPWS_SOURCE_DIR . "lib/pear/");

/* This line is for windows environments */
//ini_set("include_path", ".;".PHPWS_SOURCE_DIR."lib\\pear\\");

/* Uncomment this line to attempt to use a higher memory limit */
ini_set("memory_limit", "12M");

/********************************* END - USER EDIT ****************************************
 *                         Do NOT edit anything below this point!                         *
 ******************************************************************************************/

/* Require PEAR packages. Note: These packages are required for phpWebSite to run correctly. */

/* Require PEAR Base Class */
require_once("PEAR.php");
/* Require DB Database Abstraction Class */
require_once("DB.php");
/* Require HTML_Template_IT package */
require_once("HTML/Template/IT.php");
/* Require Table package */
require_once("HTML/Table.php");


/* Require the phpWebSite core classes. Note: Keep these in the order they appear. */

/* Require phpwebsite base item class */
require_once(PHPWS_SOURCE_DIR . "core/Item.php");
/* Require phpwebsite base manager class */
require_once(PHPWS_SOURCE_DIR . "core/Manager.php");
/* Require Error Handling class (not available yet)*/
require_once(PHPWS_SOURCE_DIR . "core/Error.php");

require_once(PHPWS_SOURCE_DIR . "core/Message.php");

/* Debug functions */
require_once(PHPWS_SOURCE_DIR . "core/Debug.php");
/* A compilation of small, usuful functions */
require_once(PHPWS_SOURCE_DIR . "core/WizardBag.php");
/* Contains the templating functions */
require_once(PHPWS_SOURCE_DIR . "core/Template.php");
/* Contains date and time functions and variables */
require_once(PHPWS_SOURCE_DIR . "core/DateTime.php");
/* Contains file manip functions */
require_once(PHPWS_SOURCE_DIR . "core/File.php");
/* Require form generation */
require_once(PHPWS_SOURCE_DIR . "core/Form.php");
/* Require text parsing */
require_once(PHPWS_SOURCE_DIR . "core/Text.php");
/* Require array tools */
require_once(PHPWS_SOURCE_DIR . "core/Array.php");
/* Require database */
require_once(PHPWS_SOURCE_DIR . "core/Database.php");
/* Require cache */
require_once(PHPWS_SOURCE_DIR . "core/Cache.php");
/* Require EZform */
require_once(PHPWS_SOURCE_DIR . "core/EZform.php");
/* Require Pager */
require_once(PHPWS_SOURCE_DIR . "core/Pager.php");
/**
 * Controls the core functions and info of phpWebSite
 *
 * PHPWS_Core extends other core classes to obtain access
 * to their functions and variables.
 * 
 * @version $Id: Core.php,v 1.117 2003/07/02 15:14:31 matt Exp $
 * @author  Matt McNaney <matt@NOSPAM.tux.appstate.edu>
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Core
 */
class PHPWS_Core extends PHPWS_Database {

  /**
   * TRUE if this instance of PHPWS_Core is the hub of FALSE if this instance
   * of PHPWS_Core is a spoke.
   * @var    boolean
   * @access private
   */
  var $isHub;

  /**
   * The configuration file for this instance of PHPWS_Core.  This variable
   * will be removed in the future in favor of the configFile variable.
   * @var    string
   * @access private
   */
  var $configFile;

  /**
   * The directory where the phpWebSite source files reside.  Used to access
   * any source or configuration file that is not specific to a spoke site.
   * @var    string
   * @access private
   */
  var $source_dir;

  /**
   * The home directory of this instance of PHPWS_Core. Used to access
   * images, themes, and templates. This should be used to ensure your
   * module works with spoke sites.
   * @var    string
   * @access private
   */
  var $home_dir;

  /**
   * The source http path to the phpWebSite source files.
   * @var    string
   * @access private
   */
  var $source_http;

  /**
   * Stores module information
   * @var array
   * @access private
   */
  var $modules;

  /**
   * The http path used when accessing images, themes, or templates.  This
   * should be used to insure your module works with spoke sites.
   * @var    string
   * @access private
   */
  var $home_http;

  /**
   * Information on the current branch
   * @var    array
   * @access private
   */
  var $branch;

  /**
   * The version of phpWebSite running.
   * @var    string
   * @access private
   */
  var $version;

  /**
   * The current module that is currently at the attention of the core.
   * @var    string
   * @access private
   */
  var $current_mod;

  var $requestModule;

  /**
   * A list of modules that are currently loaded in the core.
   * @var    array
   * @access private
   */
  var $mods_loaded;

  /**
   * The site hash used with this instance of PHPWS_Core.  This will change
   * if you are coming from a spoke site.
   *
   * This should be moved into the security module soon.
   *
   * @var    string
   * @access private
   */
  var $site_hash;

  /**
   * array of primary ids for auto incrementing
   *
   * @var    array
   * @access private
   */
  var $js_func;

  /**
   * Constructor for the PHPWS_Core class
   *
   * Since there can be multiple spokes to the hub site,
   * the variable branchName determines which site is getting initialized.
   * If there is not one supplied, it is assumed the hub is getting initialized.
   * Normally the core is designed to start the database. However, if you just want to 
   * to use the core without the database, send "noDB" as the branchName.
   *
   * The hubDirectory is sent by the spoke. This keeps which directory is handling what separate.
   *
   * @author   Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @modified Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @modified Steven Levin <steven@NOSPAM.tux.appstate.edu>
   * @param    string  $branchName   Name of the web site getting initialized.
   * @param    string  $hubDirectory Directory containing the core files.
   * @param    boolean $install      If set to TRUE, the core doesn't fully initialize the database
   * @access   public
   */
  function PHPWS_Core($branchName=NULL, $hubDirectory=NULL, $install=FALSE) {
    if (!$branchName) {
      $this->isHub = TRUE;

      if (file_exists($hubDirectory . "conf/config.php"))
	$this->configFile = "conf/config.php";
      else
	exit("PHPWS_Core initialize ERROR! Config file not found!");
    }
    elseif ($branchName == "noDB")
      return;
    elseif (!$this->loadBranch($branchName, $hubDirectory))
      exit("Sorry, but this is an invalid branch");

    $version = NULL;
    if (file_exists($hubDirectory . "conf/config.php")) {
      require($hubDirectory . "conf/config.php");
      $this->source_dir = PHPWS_SOURCE_DIR;
      $this->source_http = $source_http;
      if (!defined("PHPWS_SOURCE_HTTP"))
	define("PHPWS_SOURCE_HTTP", $source_http);
    }

    if ($this->isHub) {
      if (!defined("PHPWS_HOME_DIR"))
	define("PHPWS_HOME_DIR", $this->source_dir);
      $this->home_dir  = $this->source_dir;
      if (!defined("PHPWS_HOME_HTTP"))
	define("PHPWS_HOME_HTTP", $this->source_http);
      $this->home_http = $this->source_http;
      $this->site_hash = $hub_hash;
    } else {
      if (!defined("PHPWS_HOME_DIR"))
	define("PHPWS_HOME_DIR", $this->branch["branchDir"]);
      $this->home_dir  = $this->branch["branchDir"];
      if (!defined("PHPWS_HOME_HTTP"))
	define("PHPWS_HOME_HTTP", $this->branch["branchHttp"]);
      $this->home_http = $this->branch["branchHttp"];
      $this->site_hash = $this->branch["IDhash"];
    }

    include($hubDirectory . "conf/core_info.php");
    $this->version = $version;

    if (!$this->loadDatabase(NULL, TRUE)){
      if ($install) return;
      else exit("Unable to load database indicated by configuration file.");
    }
    
    $this->loadSettings();

    if ($this->isHub && !$install && !count($this->listTables())){
      header("location:setup/setup.php");
      exit();
    }
  }

  /**
   * Loads core settings once database connection is made
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   */
  function loadSettings(){
    $this->loadCacheSettings();
    $this->loadTextSettings();
    $this->loadDateTimeSettings();
  }

  /**
   * Loads the cache settings from the cache.php file
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   */
  function loadCacheSettings(){
    $cacheFile = $this->source_dir . "conf/cache.php";
    if (!file_exists($cacheFile))
      define("CACHE", FALSE);

    include($cacheFile);
  }

  /**
   * Loads class files and includes
   *
   * We need ALL class files called before any system objects are sessioned.
   * If sessions happen inbetween class file requires, PHP dies.
   *
   * @author   Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @modified Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @access   public
   */
  function initModules() {
    if (DEBUG_MODE)
      $GLOBALS['PHPWS_Timer']->setMarker("Begin Module Initialization");

    if (empty($_REQUEST["module"])) $this->setRequestModule("home");
    else $this->setRequestModule($_REQUEST["module"]);

    $initMods = $this->loadModules();

    $sessionName = md5($this->site_hash . PHPWS_HOME_HTTP);
    session_name($sessionName);
    session_start($sessionName);

    if (!isset($initMods))
      exit("Fatal Error: phpWebSite made its database connection but failed to load any modules. <br />Please check your installation.");
    
    if (DEBUG_MODE)
      $GLOBALS['PHPWS_Timer']->setMarker("End Module Initialization");

    foreach($initMods as $mod_title=>$mod_include){
      if (DEBUG_MODE)
	$GLOBALS['PHPWS_Timer']->setMarker("Begin Load $mod_title Objects");
      if (is_array($mod_include["init_object"])){
	foreach ($mod_include["init_object"] as $object_name=>$class_name){
	  if (!isset($_SESSION[$object_name]) && class_exists($class_name)){
	    if (is_array($mod_include["mod_sessions"]) && in_array($object_name, $mod_include["mod_sessions"]))
	      $_SESSION[$object_name] = new $class_name;
	    else
	      $GLOBALS[$object_name] = new $class_name;
	  }
	}
      }
      if (DEBUG_MODE)
	$GLOBALS['PHPWS_Timer']->setMarker("End Load $mod_title Objects");

      $includeFiles[$mod_title] = $this->source_dir . "mod/" . $mod_include["mod_directory"] . "/" . $mod_include["mod_filename"];
    }

    // Load module index file
    $current_mod_file = NULL;

    return $includeFiles;
  }// END FUNC initModules()


  /**
   * Returns listing of currently active modules
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @access public
   */
  function listLoadedMods(){
    return $this->mods_loaded;
  }// END FUNC listLoadedMods()

  /**
   * Assures the hub that the request branch site is legitimate and loads it into the core object
   *
   * If the hash supplied along with the branchName is not correct, the branch site will not initialize.
   * The branch site information is stored in the branch_sites table. If all is correct, the branch will
   * be loaded and it will return TRUE. Otherwise it will return FALSE;
   *
   * @author   Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @modified Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @param    string  $branchName   Name of the branch site
   * @param    string  $hubDirectory Location of the hub files
   * @return   string  $configFile    Name of the branch site config file.
   * @access   private
   */
  function loadBranch($branchName, $hubDirectory) {
    if ($branchName){
      if(!$this->loadDatabase($hubDirectory."conf/config.php", 1))
	exit("Unable to load database using ".$hubDirectory."conf/config.php");
      
      if (!$sql_result = $this->sqlSelect("branch_sites", "branchName", $branchName))
	return FALSE;
      
      list(,$this->branch) = each($sql_result);
      
      $configDir = $hubDirectory . "conf/branch/" . $this->branch["configFile"];
      
      if (!file_exists($configDir)){
	echo "Configuration directory does not exist<br />";
	return FALSE;
      }

      $this->configFile = "conf/branch/" . $this->branch["configFile"];
      
      if (getcwd() == $this->branch["branchDir"]){
	echo "Registered branch directory does not match current directory.<br />";
	return FALSE;
      }
      
      if ($this->branch["IDhash"] != $GLOBALS["IDhash"]){
	echo "Branch ID does not match registration.<br />";
	return FALSE;
      }
      
      $this->isHub = FALSE;
      return TRUE;
      
    }
  }// END FUNC loadBranch()
  

  /**
   * Sets the module name to the core's requestModule variable
   *
   * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
   * @param  string $module The name of the module to set in the GLOBALS array and in the core requestModule variable.
   * @access private
   */
  function setRequestModule($module) {
    $GLOBALS["module"] = $this->requestModule = $module;
  }// END FUNC setRequestModule()


  /**
   * Updates or creates the module information from an imported file.
   *
   * If the process = "insert", a new module is registered.
   * If the process = "update", a current module is updated.
   * If the process is not sent, then the settings array is returned.
   *
   * @author   Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @modified Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @param    string $info_dir File address of the module information file
   * @param    string $process  Determines whether to update or insert the database
   * @return   array  $sql      Array of the module registration settings
   * @access   public
   */
  function setModuleInfo($info_dir, $process=NULL){
    if (file_exists($info_dir))
      include($info_dir);
    else {
      echo "Warning: unable to find file $info_dir<br />";
      return NULL;
    }

    if ($process == "remove")
      return $this->sqlDelete("modules", "mod_title", $mod_title);

    $sql["mod_title"] = $mod_title;
    $sql["mod_pname"] = $mod_pname;

    if (!isset($mod_directory))
      $mod_directory = $mod_title;

    if (!isset($mod_filename))
      $mod_filename = "index.php";

    $sql["mod_directory"] = $mod_directory;
    $sql["mod_filename"] = $mod_filename;

    if (!$allow_view)
      $allow_view = array($mod_title=>1);

    $sql["allow_view"] = $allow_view;

    if (isset($priority) && is_numeric($priority))
      $sql["priority"] = $priority;
    else
      $sql["priority"] = 50;

    if (isset($user_mod))
      $sql["user_mod"] = $user_mod;

    if (isset($admin_mod))
      $sql["admin_mod"] = $admin_mod;

    if (isset($deity_mod))
      $sql["deity_mod"] = $deity_mod;

    if (isset($mod_class_files))
      $sql["mod_class_files"] = $mod_class_files;

    if (isset($mod_sessions))
      $sql["mod_sessions"] = $mod_sessions;

    if (isset($init_object))
      $sql["init_object"] = $init_object;

    $sql["active"] = $active;
    
    PHPWS_Core::dropNulls($sql);
    
    if ($process == "insert")
      return $this->sqlInsert($sql, "modules", 1);
    elseif ($process == "update")
      return $this->sqlUpdate($sql, "modules", "mod_title", $mod_title);
    else 
      return $sql;
  }// END FUNC setModuleInfo()


  /**
   * Gets all the information on one module and returns it in an associative array.
   *
   * @author   Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @modified Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @param    string $mod_title Name of module to fetch from database
   * @return   array  $row[0]    Information on module
   * @access   public
   */
  function getModuleInfo($mod_title){
    if (!phpws_text::isValidInput($mod_title))
      return NULL;

    if (isset($this->modules[$mod_title]))
      return $this->modules[$mod_title];

    $row=$this->sqlSelect("modules", "mod_title", $mod_title);
    if ($row){
      $this->modules[$mod_title] = $row[0];
      return $row[0];
    }
    else
      return NULL;
  }// END FUNC getModuleInfo()


  /**
   * Loads modules currently required into the core
   *
   * Which modules to load is decided by whether 1) the module
   * is set to load everytime (set to 'all') 2) the module is
   * set to load with the current '$module'. The index.php
   * file will send 'home' as the module if one is not currently required.
   * This will cause only the 'all' modules to be loaded.
   *
   * @author   Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @modified Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @return   boolean Returns TRUE is there aren't any problems, FALSE otherwise
   * @access   public
   */
  function loadModules(){
    if(!$result = $this->sqlSelect("modules", "active", "on", "priority"))
      return FALSE;

    foreach($result as $row){
      extract($row);
      $this->modules[$mod_title] = $row;
      if ($allow_view != 'all')
	$allow_array = unserialize($allow_view);
    
      if ($mod_class_files)
	$mod_class_files = unserialize($mod_class_files);
    
      if ($mod_sessions)
	$mod_sessions = unserialize($mod_sessions);
    
      if ($init_object)
	$init_object = unserialize($init_object);
    
      if ($allow_view == "all" || isset($allow_array[$this->requestModule])){
	$modules[$mod_title] = array ("mod_pname"=>$mod_pname,
					   "mod_directory"=>$mod_directory,
					   "mod_filename"=>$mod_filename,
					   "mod_class_files"=>$mod_class_files,
					   "mod_sessions"=>$mod_sessions,
					   "init_object"=>$init_object
					   );
	$this->mods_loaded[] = $mod_title;
	if (is_array($mod_class_files)){
	  foreach($mod_class_files as $class_file){
	    $classInit = $this->source_dir."mod/".$mod_directory."/class/".$class_file;
	    if(file_exists($classInit))
	      require_once($classInit);
	    else
	      echo "Unable to open class file from location $classInit<br />";
	  }
	}
      }
    }
    return $modules;
  }// END FUNC loadModules()


  /**
   * Returns an array of all the modules in the module table.
   *
   * @author   Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @modified Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @return   array $mods Listing of all the currently installed modules
   * @access   public
   */
  function listModules($activeOnly=FALSE){
    if ($activeOnly)
      $grabActive = "where active='on'";
    else
      $grabActive = NULL;

    $mods = $this->getCol("select mod_title from ".$this->tbl_prefix."modules ".$grabActive." order by mod_pname");
    return $mods;
  }// END FUNC listModules()


  /**
   * Returns the directory where a particular module is installed
   *
   * @author   Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @modified Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @param    string $module Name of the module
   * @return   string         Directory of the module
   * @access   public
   */
  function getModuleDir($module){
    if (!$this->isValidInput($module))
      return FALSE;

    $sql = $this->sqlSelect("modules", "mod_title", $module);
    return $sql[0]["mod_directory"];
  }// END FUNC getModuleDir()


  /**
   * Checks to see if a)module is installed and b)mod_title is formed
   * correctly (no spaces, punctuation, etc.)
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string  $mod_title Name of module to check for.
   * @return boolean TRUE if the module exists, FALSE if it does not.
   * @access public
   */
  function moduleExists($mod_title){
    if (!(PHPWS_Core::isValidInput($mod_title)))
      return FALSE;

    if (!($this->getModuleInfo($mod_title)))
      return FALSE;

    return TRUE;
  }//END FUNC moduleExists


  /**
   * Attempts to destroy all the currently active sessions
   *
   * @author   Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @modified Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @access   public
   */
  function killAllSessions(){
    $_SESSION = array();
    unset($_SESSION);
    session_destroy();
  }// END FUNC killAllSessions()


  /**
   * Attempts to destroy a specific session
   *
   * @author   Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @modified Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @param    string $session_name Name of the session to be destroyed
   * @access   public
   */
  function killSession($session_name){
    $_SESSION[$session_name]=NULL;
    unset($_SESSION[$session_name]);
  }// END FUNC killSession()


  /* ATTENTION!! */
  /* The following functions will be moved to more appropriate classes in the future. */

  /**
   * Logs a transgression or attempted security breach
   *
   * The core merely records. A security module is needed
   * to parse this data.
   *
   * @author Matt McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  integer $offence      Number of the offense
   * @param  string  $sec_mod_name Name of module
   * @access public
   */
  function unauthorized($offense=0, $sec_mod_name=NULL){
    global $REMOTE_ADDR, $OBJ_user;

    if (is_int($offense))
      $sql_array["offense"] = $offense;

    if ($REMOTE_ADDR)
      $sql_array["ip_address"] = $REMOTE_ADDR;
    else
      $sql_array["ip_address"] = NULL;

    if ($OBJ_user->user_id)
      $sql_array["sec_user_id"] = $OBJ_user->user_id;

    if ($sec_mod_name)
      $sql_array["sec_mod_name"] = $sec_mod_name;

    $this->sqlInsert($sql_array, "mod_security_log");
    header("location:index.php");
    exit();
  }// END FUNC unauthorized()
  
} //END CLASS PHPWS_Core

?>
