<?php

$tab[] = array ("label"=>"my_modules",
		 "title"=>"My Modules",
		 "grid"=>3
		);

$image["name"] = "notes.png";
$image["alt"] = "Author: Adam Morton";

$link[] = array ("label"=>"Notes",
		 "module"=>"notes",
		 "url"=>"index.php?module=notes&NOTE_op=menu",
		 "image"=>$image,
		 "admin"=>FALSE,
		 "description"=>"Go here to send notes to other users on this site.",
		 "tab"=>"my_modules");

?>