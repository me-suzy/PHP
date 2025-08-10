<?php
/**
 * This is the Pagemaster boost.php file for Boost
 *
 * @version $Id: boost.php,v 1.9 2003/06/12 16:31:57 adam Exp $
 * @author Adam Morton <adam@NOSPAM.tux.appstate.edu>
 */
$mod_title = "pagemaster";
$mod_pname = "PageMaster";
$mod_directory = "pagemaster";
$mod_filename = "index.php";
$allow_view = array("home"=>1, "pagemaster"=>1, "approval"=>1, "search"=>1);
$priority = 50;
$active = "on";
$version = "1.4";
$admin_mod = 1;

$mod_class_files = array("PageMaster.php",
			 "Page.php",
			 "Section.php");

$mod_sessions = array("SES_PM_master",
		      "SES_PM_page",
		      "SES_PM_section");


?>