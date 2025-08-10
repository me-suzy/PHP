<?php
/**
 * If you have problems with your memory or timeout limits we suggest that you
 * convert you site in three different stages.  They are labeled below.
 *
 * READ CONVERSION.TXT in the docs directory before going any further
 */

/* Set these to FALSE if you have already converted the module, the module
   is not installed, or if you just don't want to convert the module. */

/* STAGE 1: PAGEMASTER, MENUMAN, BLOCKMAKER */
// Note: PageMaster and MenuMan should be converted together. MenuMan
// receives its setup information from PageMaster
define("CONVERT_PAGEMASTER", TRUE);
define("CONVERT_MENUMAN", TRUE);

define("CONVERT_BLOCKMAKER", TRUE);

/* STAGE 2: TOPICS, ANNOUNCE, COMMENTS */
// Note: Topics, Announcements and Comments should be converted together. Announcements
// receives its setup information from Topics and comments receives ids from announcements.
define("CONVERT_TOPICS", TRUE);
define("CONVERT_ANNOUNCE", TRUE);
define("CONVERT_COMMENTS", TRUE);

/* STAGE 3: USERS, CALENDAR, LINKMAN */
define("CONVERT_USERS", TRUE);
define("CONVERT_CALENDAR", TRUE);
define("CONVERT_LINKMAN", TRUE);

define("CONVERT_PHOTOALBUM", TRUE);


/* Important Settings! */
// this directory should always be set to the phpwebsite root
define("PHPWS_SOURCE_DIR", "../");

// this directory will either be set to the root OR to the branch directory
//define("PHPWS_HOME_DIR", "../");
define("PHPWS_HOME_DIR", "/www/var/html/branch/stardog/");

// Make sure you set this your old site directory
// Make sure there is a forward slash at the end
define("OLD_SITE_DIR", "/var/www/html/");

// Set this to the branch site's name to properly convert data to a branch site
$IDhash = "e3a9c9445f2e57e985a61bce2574d3ff";

// Set this to the name of the branch you wish to convert to. Or leave as is for
// main site conversion.
$branchName = "stardog";


include("../core/Core.php");
$core = new PHPWS_Core($branchName, PHPWS_SOURCE_DIR);

if(file_exists("../conf/config.php")) {
  include("../conf/config.php");
} else {
  header("Location: ../setup/set_config.php");
  exit();
}

$core->source_dir = $core->home_dir = $source_dir;

define("NEW_DBNAME", $dbname);
define("NEW_PREFIX", $core->tbl_prefix);

if(isset($_POST['pass']) && ($_POST['pass'] == $install_pw)) {
  echo "<html><head><title>PhpWebSite Conversion</title></head>";
  echo "<body><img src=\"../setup/poweredby.jpg\" /><br />";
  echo "<h1>Beginning Module Conversion Proccess</h1>";
  include("../mod/layout/class/Layout.php");
  include("../mod/fatcat/class/Fatcat.php");

  /* Include database functions and settings */
  include("./db.php");

  /* Grabbing currently installed modules */
  $currentModules = $core->listModules();

  if(CONVERT_PAGEMASTER && in_array("pagemaster", $currentModules))
    include("./mod/pagemaster.php");

  if(CONVERT_MENUMAN && in_array("menuman", $currentModules))
    include("./mod/menuman.php");

  if(CONVERT_BLOCKMAKER && in_array("blockmaker", $currentModules))
    include("./mod/blockmaker.php");

  if(CONVERT_TOPICS && in_array("fatcat", $currentModules))
    include("./mod/topics.php");

  if(CONVERT_ANNOUNCE && in_array("announce", $currentModules))
    include("./mod/announce.php");

  if(CONVERT_COMMENTS && in_array("comments", $currentModules))
    include("./mod/comment.php");

  if(CONVERT_USERS && in_array("users", $currentModules))
    include("./mod/user.php");

  if(CONVERT_CALENDAR && in_array("calendar", $currentModules))
    include("./mod/calendar.php");
  
  if(CONVERT_LINKMAN && in_array("linkman", $currentModules))
    include("./mod/linkman.php");
  
  if(CONVERT_PHOTOALBUM && in_array("photoalbum", $currentModules))
    include("./mod/photoalbum.php");
  
  echo "<h2>PhpWebSite Conversion Complete!</h2>";
  echo "</body></html>";
} else {

  echo "<html><head><title>PhpWebSite Conversion</title></head>";
  echo "<body><img src=\"../setup/poweredby.jpg\" /><br />";
  echo "<h1>phpWebSite 0.8.3 to 0.9.x Conversion</h1>";
  echo "Please enter your 0.9.x install password to continue.<br /><br />";
  echo "<form action=\"./index.php\" method=\"post\">";
  echo PHPWS_Core::formPassword("pass") . "&#160;";
  echo PHPWS_Core::formSubmit("Continue");
  echo "</form></body></html>";
}

?>
