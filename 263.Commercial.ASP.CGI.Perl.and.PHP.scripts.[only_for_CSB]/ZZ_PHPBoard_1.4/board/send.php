<?php
include("./inc/inc.config.php");
function refresh() {
global $cfg_sendmessage;
echo <<<REFRESH
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="refresh" content="2; URL=forum.php">
<link href="./inc/styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<table width="100%" height="80%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td><div align="center"><font face="Arial, Helvetica, sans-serif"><strong>$cfg_sendmessage</strong></font></div></td>
  </tr>
</table>
</body>
</html>
REFRESH;
}

if ($post !== "") {
	if ($name !== "") {
		if ($subject !== "") {
			if (preg_match("/[a-zA-Z0-9]/", $message)) {
				$preg = "^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,3})$";
				if (preg_match("/$preg/", $email) or !preg_match("/[a-zA-Z0-9]/", $email)) {
					$time = strftime("%m/%d/%y %T %w"); # Format: "Month/Day/Year Hour:Min:Sec Weekday" Weekday as number beginning with 0 as Sunday	
					if ($follow_up) {
						$handle = opendir ('./'.$cfg_msgdir);
						$all = array();	
						while (false !== ($file = readdir ($handle))) { // Alle Datein im Verzeichnis einlesesn bis false
							if (substr ($file, -4) == ".txt") { // Weiter nur wenn Datei mit .txt endet	
								$file = preg_replace("/.txt/","",$file); // .txt entfernen	
								$all[] = $file;
							} // end IF 
						} // end While
						sort($all);
						$amount = count($all);
						for ($i=1; $i <= $amount; $i++) {
								$f_up = $post . "." . $i;
								$entry = "./".$cfg_msgdir."/" . $f_up . ".txt";
								if (!file_exists($entry)) {
									$data = fopen($entry,"w+");
									$send = $name. "\n" . $email . "\n" . $subject . "\n" . $time . "\n" . $url . "\n" . $url_title . "\n" . $message;
									$send = strip_tags($send, "&nbsp;");
									$send = stripcslashes ($send);
									fputs($data, $send);
									fclose($data);
									break;
								}
							}					
						} else { // Main Nachricht
							$data = fopen("./".$cfg_msgdir."/". $post .".txt","w+");
							$send = $name. "\n" . $email . "\n" . $subject . "\n" . $time . "\n" . $url . "\n" . $url_title . "\n" . $message;
							$send = strip_tags($send, "&nbsp;");
							$send = stripcslashes ($send);
							fputs($data, $send);
							fclose($data);
						} // END Follow_up
						refresh();
					} else { // else email
						include("./inc/inc.page_top.php");
						$col02 = "class=highlight";	
						$error_msg01 = "No valid email";
						$error_msg02 = $cfg_error_wrongemail;
						include("inc/inc.error.php");
						include("./inc/inc.page_bot.php");
					}
			} else { // else message
					include("./inc/inc.page_top.php");
					$col04 = "class=highlight";
					$error_msg01 = "No message";
					$error_msg02 = $cfg_error_nomessage;
					include("inc/inc.error.php");
					include("./inc/inc.page_bot.php");
			} // ende message
		} else { // else subject
				include("./inc/inc.page_top.php");
				$col03 = "class=highlight";
				$error_msg01 = "No subject";
				$error_msg02 = $cfg_error_nosubject;
				include("./inc/inc.error.php");
				include("./inc/inc.page_bot.php");
		} // ende subject
	} else { // else name
			include("./inc/inc.page_top.php");
			$col01 = "class=highlight";
			$error_msg01 = "No name";
			$error_msg02 = $cfg_error_noname;
			include("./inc/inc.error.php");
			include("./inc/inc.page_bot.php");
	} // ende name#
}
?>