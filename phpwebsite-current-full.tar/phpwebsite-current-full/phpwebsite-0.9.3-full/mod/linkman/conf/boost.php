<?php

$mod_title = "linkman";
$mod_pname = "Link Manager";
$mod_directory = "linkman";
$mod_filename = "index.php";
$allow_view = "all";
$admin_mod = 1;
$priority = 50;
$mod_class_files = array("Linkman.php", "Link.php");
$mod_sessions = array("PHPWS_Linkman");
$init_object = array("PHPWS_Linkman"=>"PHPWS_Linkman");
$active = "on";
$version = 1.03;
//$update_link = "phpwebsite.appstate.edu/fallout/updates/linkman/VERSION";

$depend = array("fatcat");

?>