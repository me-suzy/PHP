<?php
/**
 * @version $Id: boost.php,v 1.6 2003/06/25 15:36:34 adam Exp $
 */
$mod_title = "announce";
$mod_pname = "Announcements";
$mod_directory = "announce";
$mod_filename = "index.php";
$allow_view = "all";
$active = "on";
$version = 1.2;
$admin_mod = 1;

$mod_class_files = array("AnnouncementManager.php",
			 "Announcement.php");

$mod_sessions = array("SES_ANN_MANAGER",
		      "SES_ANN");
?>