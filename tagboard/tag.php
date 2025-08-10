<?php

include("config.php");

// No HTML Part

$protag = str_replace("&lt;","<", $protag);
$protag = str_replace("&gt;",">", $protag);
$protag = strip_tags($protag, '<i><u><b>');			// strips HTML tags except <i><u> and <b> from tag
$name = strip_tags($name);						// strips all HTML tags from name

// End

// Bad Word Filter function

foreach($badword_array as $insult=>$ok){
        $protag = eregi_replace("$insult", "$ok", "$protag");
        }

// End

// Main Program can (finally) start! ------------------//

if (!$name || !$protag){ 
	header("location: $display");
}
else $name .= ":";

$person = $name;

if($stats == 1){
	if (getenv(HTTP_X_FORWARDED_FOR)) {							
    		$ip = getenv(HTTP_X_FORWARDED_FOR); 
	}
	else{ 
    	$ip = getenv(REMOTE_ADDR);
	}

	$date = date("l d F - Y");
	$time = date("g:i:s a");

   $name = "<img src=\"ip.gif\" alt=\"$person&#10;&#13;IP: $ip&#10;&#13;Date: $date&#10;&#13;Time: $time\"> $person";
}

$tagomfile = file($datfile);

if ($protag != "") {
	if (strlen($protag) < $MAX_LENGTH) {
		$fd = fopen ($datfile, "w");
		$protag = stripslashes($protag);
		
		fwrite ($fd, "<small><b>$name</b><br>$protag<br></small>\r\n");
			for ($count = 0; $count < $NUM_COMMENTS-1; $count++) {
				fwrite ($fd, $tagomfile[$count]);
			}
	}

fclose($fd);

// Write the counter....

require("tagcount.txt");

$countfilename = "tagcount.txt";
$increment = $tagcount + 1;
$incrementoutput = "<? $" . "tagcount = " . $increment . "; ?>";
$write = fopen($countfilename, "w");
fwrite ($write, $incrementoutput);
fclose($write);

}

// else dont do anything to the data file

header("location: $display");
?>
