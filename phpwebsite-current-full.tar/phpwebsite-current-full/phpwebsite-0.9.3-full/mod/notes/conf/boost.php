<?php

$mod_title = "notes";
$mod_pname = "Notes";
$mod_directory = "notes";
$mod_filename = "index.php";
$active = "on";
$priority = 50;
$version = 1.2;
$allow_view = "all";

$mod_class_files = array("NoteManager.php",
			 "Note.php");

$mod_sessions = array("SES_NOTE_MANAGER",
		      "SES_NOTE");

?>