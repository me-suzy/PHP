<?php
if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

$status = 1;

if ($currentVersion < 3.2){
     $status = $GLOBALS['core']->query("ALTER TABLE mod_calendar_repeats ADD `active` SMALLINT DEFAULT '1' NOT NULL", TRUE);
}

if ($currentVersion < 3.3){
  $status = $GLOBALS['core']->query("ALTER TABLE mod_calendar_repeats DROP PRIMARY KEY", TRUE);
  $status = $GLOBALS['core']->query("ALTER TABLE mod_calendar_repeats ADD INDEX ( id )", TRUE); 
  $content .= "+ Fixed primary key error that was causing problems with repeats.<br />";
}


if ($currentVersion < 3.4){
  $content .= "+ Fixed bugs with Deadlines and Starts At. <br />";
  $content .= "+ Reduced memory overhead. <br />";
  $content .= "+ Increased speed on side panel calendar. <br />";
  $content .= "+ Fixed where Calendar was grabbing start day correctly.<br />";
  $content .= "+ Refreshes cache after an event is created, editted or deleted.<br />";
  $content .= "+ Removed class extentions.<br />";
  $content .= "+ Many notices and warnings squashed.<br />";
  $content .= "+ Removed Form class from normal operations.<br />";
}

if ($currentVersion < "3.4.2")
  $content .= "+ Fixed the week display.<br />";


?>