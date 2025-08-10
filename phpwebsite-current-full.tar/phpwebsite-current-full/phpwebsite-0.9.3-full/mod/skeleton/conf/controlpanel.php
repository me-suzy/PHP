<?php
/**
 * This is a skeleton control panel configuration file. Edit it to be used
 * with you module.
 *
 * $Id: controlpanel.php,v 1.2 2003/07/02 18:18:05 adam Exp $
 */

$image["name"] = "skeleton.jpg";
$image["alt"] = "Module Author: Adam Morton";

/* Create a link to your module */
$link[] = array ("label"=>"Skeleton Module",
		 "module"=>"skeleton",
		 "url"=>"index.php?module=skeleton&SKEL_MAN_OP=main",
		 "image"=>$image,
		 "admin"=>TRUE,
		 "description"=>"This module is a skeleton module for use by developers when creating a new module.",
		 "tab"=>"developer");

?>