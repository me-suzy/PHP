<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

CLS_help::uninstall_help("calendar");

if ($GLOBALS['core']->sqlTableExists("mod_calendar_events", TRUE)) $GLOBALS['core']->sqlDropTable("mod_calendar_events");
if ($GLOBALS['core']->sqlTableExists("mod_calendar_repeats", TRUE)) $GLOBALS['core']->sqlDropTable("mod_calendar_repeats");
if ($GLOBALS['core']->sqlTableExists("mod_calendar_settings", TRUE)) $GLOBALS['core']->sqlDropTable("mod_calendar_settings");
if(isset($_SESSION["OBJ_search"]))
     $GLOBALS["core"]->sqlDelete("mod_search_register", "module", "calendar");

$status = 1;


?>