<?php

$image["name"] = "pagemaster.png";
$image["alt"] = "Author: Adam Morton";

$link[] = array ("label"=>"PageMaster",
		 "module"=>"pagemaster",
		 "url"=>"index.php?module=pagemaster&MASTER_op=main_menu",
		 "image"=>$image,
		 "admin"=>TRUE,
		 "description"=>"Go here to create and edit your site's web pages.",
		 "tab"=>"content");

?>