<?php
###########################################################################
#
#               FLOODBOX v1.01 By Jorge Beltran
#	    				31 / MAR / 2003
#
#    			  http://www.floodboy.net
#	      george@floodboy.net / digitalfila@msn.com
#
#
############################################################################

//--------------------------------------------------------------------------------------------
//                   DON'T EDIT THIS FILE
//--------------------------------------------------------------------------------------------
//                  NO EDITES ESTE ARCHIVO
//--------------------------------------------------------------------------------------------

require("floodconfig.php");
function form(){
	global $sitename, $maxchar, $message, $posted, $pemail, $url, $pname, $wname;
	$file = "dataflood.db";
	$fp = fopen($file, "r") or die("<b>error:</b> can't open file");
	$val = file($file);
	print "<html>
	<head>
		<title>$sitename shoutbox</title>
		<link rel=\"stylesheet\" href=\"floodstyle.css\" type=\"text/css\">
		<script type=\"text/javascript\" src=\"floodjs.js\"></script>
	</head>
	<body onload=\"window.scrollTo(0,99999);\">
		<center>
	<font>Welcome <b>$wname</b></font><br><br>
	<TABLE width=\"99%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
		<TR>
			<TD id=\"tdbg2\">$val[9]</TD>
		</TR>
		<TR>
			<TD id=\"tdbg1\">$val[8]</TD>
		</TR>
		<TR>
			<TD id=\"tdbg2\">$val[7]</TD>
		</TR>
		<TR>
			<TD id=\"tdbg1\">$val[6]</TD>
		</TR>
		<TR>
			<TD id=\"tdbg2\">$val[5]</TD>
		</TR>
		<TR>
			<TD id=\"tdbg1\">$val[4]</TD>
		</TR>
		<TR>
			<TD id=\"tdbg2\">$val[3]</TD>
		</TR>
		<TR>
			<TD id=\"tdbg1\">$val[2]</TD>
		</TR>
		<TR>
			<TD id=\"tdbg2\">$val[1]</TD>
		</TR>
		<TR>
			<TD id=\"tdl\">$val[0]</TD>
		</TR>
	</TABLE>
<FORM METHOD=POST name=\"shoutthis\" ACTION=\"floodbox.php?action=add\" onSubmit=\"submitonce(this)\">
	<INPUT TYPE=\"text\" NAME=\"name\" value=\"$pname\" onfocus=\"this.value=''\"><br>
	<INPUT TYPE=\"text\" NAME=\"site\" value=\"$url\"><br>
	<INPUT TYPE=\"text\" NAME=\"email\" value=\"$pemail\" onfocus=\"this.value=''\"><br>
	<INPUT TYPE=\"text\" NAME=\"mensaje\" value=\"$posted\" onfocus=\"this.value=''\"><br>
	<table width=\"40\"><tr><td id=\"tdc\" align=\"center\">
	<script>displaylimit('document.shoutthis.mensaje',$maxchar-1)</script></tr></td></table>
	<INPUT TYPE=\"button\" value=\" flood \" onClick=\"this.disabled=true; this.form.submit()\"/><INPUT TYPE=\"reset\" value=\" reset \"><br>
	<a href=\"javascript:openpopupall()\" onfocus=\"this.blur();\"><font><b>View all</b></font></a> - <a href=\"javascript:openpopuphelp()\" onfocus=\"this.blur();\"><font><b>Help</b></font></a><br>
	<font color=\"red\">$message</font><br>
</FORM>
		</center>
	</body>
	</html>";
fclose($fp);
}
function asteriscos($input){
	return $output = str_repeat("*", (strlen($input)));
}
function filtro($input){
	global $badwords;
	foreach ($badwords as $correct){
		$input = str_replace($correct, asteriscos($correct), strtolower($input));
		$inputf = ucfirst($input);
	}
	return $inputf;
}
function emoticon($input){
	global $smilies;
	foreach($smilies as $value=>$icon){
		$input = str_replace($value, $icon, $input);
	}
	return $input;
}
function makeurl($site, $mail, $name){
	if (($site != "http://") && (!empty($site))){
		if (($mail != "your@mail.com") && (!empty($mail))){
			$namelink = "<a href=\"$site\" target=\"_blank\">$name</a>";
			$namemailto = "<a href=\"mailto:$mail\">@</a>";
			$nametopost = "$namemailto$namelink";
		}else{
			$namelink = "<a href=\"$site\" target=\"_blank\">$name</a>";
			$namemailto = "@";
			$nametopost = "$namemailto$namelink";
		}
	}else{
		if (($mail != "your@mail.com") && (!empty($mail))){
			$namemailto = "<a href=\"mailto:$mail\">@</a>";
			$namelink = "$name";
			$nametopost = "$namemailto$namelink";
		}else{
			$namelink = "$name";
			$namemailto = "@";
			$nametopost = "$namemailto$namelink";
		}
	}
	return $nametopost;
}
if ($_GET['action'] == "add"){
	$validatem = "/[a-zA-Z0-9]+[a-zA-Z-_0-9.]*@[a-zA-Z0-9-]+.[a-zA-Z]{2,3}[.a-zA-Z]*/";
	$validateu = "/^((.*?):\/\/)([^\/:]*)(:([^\/]*))?([^\?]*\/?)?(\?(.*))?$/";
	$validatea = "/[a-zA-Z0-9]+@{1}([a-zA-Z0-9]+)?/";
	$smilies = array(":)" => "<img src='smilies/smile.gif' border='0' alt=':)'>",
		":(" => "<img src='smilies/sad.gif' border='0' alt=':('>",
		":p" => "<img src='smilies/tongue.gif' border='0' alt=':P'>",
		":d" => "<img src='smilies/biggrin.gif' border='0' alt=':D'>",
		":@" => "<img src='smilies/mad.gif' border='0' alt=':@'>",
		":o" => "<img src='smilies/eek.gif' border='0' alt=':o'>",
		":s" => "<img src='smilies/confused.gif' border='0' alt=':S'>",
		";)" => "<img src='smilies/wink.gif' border='0' alt=';)'>",
		"=blush" => "<img src='smilies/blush.gif' border='0' alt='=blush'>",
		"=boggle" => "<img src='smilies/boggle.gif' border='0' alt='=boggle'>",
		"=cool" => "<img src='smilies/cool.gif' border='0' alt='=cool'>",
		"=huh" => "<img src='smilies/huh.gif' border='0' alt='=huh'>",
		"=notsure" => "<img src='smilies/notsure.gif' border='0' alt='=notsure'>",
		"=ooh" => "<img src='smilies/ooh.gif' border='0' alt='=ooh'>",
		"=rolleyes" => "<img src='smilies/rolleyes.gif' border='0' alt='=rolleyes'>",
		"=sleep" => "<img src='smilies/sleep.gif' border='0' alt='=sleep'>",
		"=stress" => "<img src='smilies/stress.gif' border='0' alt='=stress'>",
		"=tired" => "<img src='smilies/tired.gif' border='0' alt='=tired'>",
		"=urgh" => "<img src='smilies/urgh.gif' border='0' alt='=urgh'>",
		"=worry" => "<img src='smilies/worry.gif' border='0' alt='=worry'>");
	$_POST['name'] = strip_tags($_POST['name'], "");
	$_POST['name'] = ucfirst (strtolower($_POST['name']));
	$_POST['mensaje'] = strip_tags($_POST['mensaje'], "");
	$_POST['mensaje'] = filtro($_POST['mensaje']);
	$_POST['site'] = strip_tags($_POST['site'], "");
	$_POST['site'] = strtolower($_POST['site']);
	$_POST['email'] = strip_tags($_POST['email'], "");
	$_POST['email'] = strtolower($_POST['email']);

	if (($_POST['name'] == "name") || ($_POST['name'] == "Name") || (empty($_POST['name']))){
		$message = "Enter name";
		$url = $_POST['site'];
		$pemail = $_POST['email'];
		$posted = $_POST['mensaje'];
		form();
	}
	elseif (in_array($_POST['name'], $admin)){
		$message = "Only Admin can post under $_POST[name] nick";
		$url = $_POST['site'];
		$pemail = $_POST['email'];
		$posted = $_POST['mensaje'];
		form();
	}
	elseif (preg_match($validatea, $_POST['name'])){
		$veradmin = explode("@", $_POST['name']);
		if (in_array($veradmin[0], $admin)){
			if (($veradmin[1] != $password) || (empty($veradmin[1]))){
				$message= "Password incorrect";
				$url = $_POST['site'];
				$pemail = $_POST['email'];
				$posted = $_POST['mensaje'];
				form();
			}else{
				$_POST['name'] = "$veradmin[0]";
					if ($_POST['mensaje'] == "Message"){
						$message = "Enter message";
						$url = $_POST['site'];
						$pemail = $_POST['email'];
						$pname = $_POST['name'];
						form();
					}
					elseif (empty($_POST['mensaje'])){
						$message = "Enter message";
						$url = $_POST['site'];
						$pemail = $_POST['email'];
						$pname = $_POST['name'];
						form();
					}
					elseif ($maxchar <= strlen($_POST['mensaje'])){
						$message = "Message must be less than $maxchar chars";
						$url = $_POST['site'];
						$pemail = $_POST['email'];
						$pname = $_POST['name'];
						form();
					}
					elseif ((!empty($_POST['email'])) && (!preg_match($validatem, $_POST['email']))){
						$message = "Enter valid email or left the field empty";
						$url = $_POST['site'];
						$pname = $_POST['name'];
						$posted = $_POST['mensaje'];
						form();
					}
					elseif ((!empty($_POST['site'])) && (!preg_match($validateu, $_POST['site']))){
						$message = "Url must start with http:// or<br>left the field empty";
						$url = "http://";
						$pemail = $_POST['email'];
						$pname = $_POST['name'];
						$posted = $_POST['mensaje'];
						form();
					}else{
					$file = "dataflood.db";
						if (file_exists($file)){
							$fp = fopen($file, "r+") or die("<b>error:</b> can't open file");
							$read = fread($fp, filesize($file));
							rewind($fp);
							$cookiename=$_POST['name'];
							$cookieurl=$_POST['site'];
							$cookiemail=$_POST['email'];
							setcookie("flood_name", "$cookiename", time()+7776000);
							setcookie("flood_site", "$cookieurl", time()+7776000);
							setcookie("flood_email", "$cookiemail", time()+7776000);
							$nametopost = makeurl($_POST['site'], $_POST['email'], $_POST['name']);
							$_POST['mensaje'] = wordwrap($_POST['mensaje'], $maxlenword, " ", 1);
							$_POST['mensaje'] = emoticon($_POST['mensaje']);
							$_POST['mensaje'] = stripslashes($_POST['mensaje']);
							$write = fwrite($fp, "<font color=red><b>::</b></font><b>$nametopost</b>: $_POST[mensaje]\n".$read);
							fclose($fp);
							print "<meta http-equiv=\"refresh\" content=\"0; URL=floodbox.php\">";
						}else{
							print "<b>error:</b> dataflood.db must be in the same dir than this script";
						}
					}
				}
		}else{
			$message = "Bad admin name";
			$url = $_POST['site'];
			$pemail = $_POST['email'];
			$posted = $_POST['mensaje'];
			form();
		}
	}
	elseif ($namemaxchar <= strlen($_POST['name'])){
		$message = "Username must be less than $namemaxchar chars";
		$url = $_POST['site'];
		$pemail = $_POST['email'];
		$posted = $_POST['mensaje'];
		form();
	}
	elseif (($_POST['mensaje'] == "Message") || (empty($_POST['mensaje'])) || ($_POST['mensaje'] == "message")){
		$message = "Enter message";
		$url = $_POST['site'];
		$pemail = $_POST['email'];
		$pname = $_POST['name'];
		form();
	}
	elseif ($maxchar+1 <= strlen($_POST['mensaje'])){
		$message = "Message must be less than $maxchar chars";
		$url = $_POST['site'];
		$pemail = $_POST['email'];
		$pname = $_POST['name'];
		form();
	}
	elseif ((!empty($_POST['email'])) && (!preg_match($validatem, $_POST['email']))){
		$message = "Enter valid email or left the field empty";
		$url = $_POST['site'];
		$pname = $_POST['name'];
		$posted = $_POST['mensaje'];
		form();
	}

	elseif ((!empty($_POST['site'])) && (!preg_match($validateu, $_POST['site']))){
		$message = "Url must start with http:// or<br>left the field empty";
		$url = "http://";
		$pemail = $_POST['email'];
		$pname = $_POST['name'];
		$posted = $_POST['mensaje'];
		form();
	}else{
		if ((!empty($_COOKIE['flood_anti'])) && (time() - $_COOKIE['flood_anti'] <= $floodtime)){
			$message = "The time allowed between posts is: <b>$floodtime</b> seconds";
			$pname = $_POST['name'];
			$wname = $_POST['name'];
			$url = $_POST['site'];
			$pemail = $_POST['email'];
			$posted = $_POST['mensaje'];
			form();
		}else{
			$file = "dataflood.db";
			if (file_exists($file)){
				$fp = fopen($file, "r+") or die("<b>error:</b> can't open file");
				$read = fread($fp, filesize($file));
				rewind($fp);
				$cookiename=$_POST['name'];
				$cookieurl=$_POST['site'];
				$cookiemail=$_POST['email'];
				setcookie("flood_name", "$cookiename", time()+7776000);
				setcookie("flood_site", "$cookieurl", time()+7776000);
				setcookie("flood_email", "$cookiemail", time()+7776000);
				setcookie("flood_anti", time(), time()+3600);
				$nametopost = makeurl($_POST['site'], $_POST['email'], $_POST['name']);
				$_POST['mensaje'] = wordwrap($_POST['mensaje'], $maxlenword, " ", 1);
				$_POST['mensaje'] = emoticon($_POST['mensaje']);
				$_POST['mensaje'] = stripslashes($_POST['mensaje']);
				$write = fwrite($fp, "::<b>$nametopost</b>: $_POST[mensaje]\n".$read);
				fclose($fp);
				print "<meta http-equiv=\"refresh\" content=\"0; URL=floodbox.php\">";
			}else{
			print "<b>error:</b> dataflood.db must be in the same dir than this script";
			}
		}
	}
}elseif ($_GET['action'] == "viewall"){
	$file = "dataflood.db";
	$fp = fopen($file, "r") or die("<b>error:</b> can't open file");
	$val = file($file);
	$valr = array_reverse($val);
	$todos = sizeof($valr);
	if ((isset($_COOKIE['flood_name'])) && ($_COOKIE['flood_name'] != "")){
		$waname = $_COOKIE['flood_name'];
	}else{
		$waname = "Guest!";
	}
	print "<html>
		<head>
			<title>Flood-all</title>
		<link rel=\"stylesheet\" href=\"floodstyle.css\" type=\"text/css\">
		</head>
		<body onload=\"window.scrollTo(0,99999);\">
			<center>
		<font>Welcome <b>$waname</b></font><br><br>
		<TABLE width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
		$i=-1;
		while($i < $todos){
		$i++;
		print "<TR> 
		<TD id=\"tdbg4\">$valr[$i]</TD>
		</TR>";
		$i++;
		print"<TR>
		<TD id=\"tdbg3\">$valr[$i]</TD>
		</TR>";
		}

	print "</TABLE>
		<br><br><font>[Floodbox v1.0]</font><br><br>
		</center>

	</body>
	</html>";

fclose($fp);
}elseif ($_GET['action'] == "help"){
print "<html>
	<head>
		<title>floodbox v1.0 help</title>
		<link rel=\"stylesheet\" href=\"floodstyle.css\" type=\"text/css\">
		<script type=\"text/javascript\" src=\"floodjs.js\"></script>
	</head>
	<body>
		<center>
	<font><b>FLOODHELP</b></font><br><br>
	<TABLE width=\"300\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
		<TR>
			<TD id=\"tdc\" align=\"center\"><b>Where can I download this shoutbox ?</b><br><br>
		In the website <a href=\"http://www.floodboy.net\" target=\"_blank\"><b>floodboy.net</b></a><br><br>
	</TD>
		</TR>
		<tr>
			<td id=\"tdc\" align=\"center\"><b>How can I post smilies in my messages ?</b><br><br>
		Very easy, when you write:<br>
		<b>:)</b> will be replaced with <img src=\"smilies/smile.gif\" border=0 alt=:)><br><br>
		When your write:<br>
		<b>=ooh</b> will be replaced with <img src=\"smilies/ooh.gif\" border=0 alt==ooh>
		<br><br>
		Here is a list with all the smilies available:<br>

			<table width=\"140\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><br><br>
		<tr><td width=\"50%\" align=\"center\"> :) </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/smile.gif\" border=0 alt=:)> </td>
		<tr><td width=\"50%\" align=\"center\"> :( </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/sad.gif\" border=0 alt=:(> </td>				
		<tr><td width=\"50%\" align=\"center\"> :p </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/tongue.gif\" border=0 alt=:p> </td>	
		<tr><td width=\"50%\" align=\"center\"> :D </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/biggrin.gif\" border=0 alt=:D> </td>	
		<tr><td width=\"50%\" align=\"center\"> :@ </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/mad.gif\" border=0 alt=:@> </td>	
		<tr><td width=\"50%\" align=\"center\"> :o </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/eek.gif\" border=0 alt=:o> </td>	
		<tr><td width=\"50%\" align=\"center\"> :S </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/confused.gif\" border=0 alt=:S> </td>	
		<tr><td width=\"50%\" align=\"center\"> ;) </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/wink.gif\" border=0 alt=;)> </td>	
		<tr><td width=\"50%\" align=\"center\"> =blush </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/blush.gif\" border=0 alt==blush> </td>
		<tr><td width=\"50%\" align=\"center\"> =boggle </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/boggle.gif\" border=0 alt==boggle> </td>
		<tr><td width=\"50%\" align=\"center\"> =cool </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/cool.gif\" border=0 alt==cool> </td>
		<tr><td width=\"50%\" align=\"center\"> =huh </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/huh.gif\" border=0 alt==huh> </td>
		<tr><td width=\"50%\" align=\"center\"> =notsure </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/notsure.gif\" border=0 alt==notsure> </td>
		<tr><td width=\"50%\" align=\"center\"> =ooh </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/ooh.gif\" border=0 alt==ooh> </td>
		<tr><td width=\"50%\" align=\"center\"> =rolleyes </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/rolleyes.gif\" border=0 alt==rolleyes> </td>
		<tr><td width=\"50%\" align=\"center\"> =sleep </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/sleep.gif\" border=0 alt==sleep> </td>
		<tr><td width=\"50%\" align=\"center\"> =stress </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/stress.gif\" border=0 alt==stress> </td>
		<tr><td width=\"50%\" align=\"center\"> =tired </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/tired.gif\" border=0 alt==tired> </td>
		<tr><td width=\"50%\" align=\"center\"> =urgh </td><td width=\"50%\" align=\"center\"> <img src=\"smilies/urgh.gif\" border=0 alt==urgh> </td>
		<tr><td width=\"50%\" id=\"tdhelp\" align=\"center\"> =worry </td><td width=\"50%\" id=\"tdhelp\" align=\"center\"> <img src=\"smilies/worry.gif\" border=0 alt==worry> </td>
		</tr></table><br><br>

	</td>
		</tr>
				<TR>
			<TD id=\"tdc\" align=\"center\"><b>Need more help ?</b><br><br>
		Questions, hacks, bugs, requests, comments go to the <b>floodbox</b> board at <a href=\"http://www.floodboy.net/board/\" target=\"_blank\"><b>floodboy.net/board/</b></a><br><br>
	</TD>
		</TR>
	</TABLE>
		</center>
	</body>
	</html>";
}else{
	if (isset($_COOKIE['flood_name'])){
		if ($_COOKIE['flood_name'] == ""){
			$wname = "Guest!";
			$pname = "name";
			$pemail="your@mail.com";
			$url="http://";
		}else{
			if ((empty($_COOKIE['flood_site'])) || ($_COOKIE['flood_site'] == "")){
				if ((empty($_COOKIE['flood_email'])) || ($_COOKIE['flood_email'] == "")){
					$wname = $_COOKIE['flood_name'];
					$pname = $_COOKIE['flood_name'];
					$url = "http://";
					$pemail = "your@mail.com";
				}else{
					$wname = $_COOKIE['flood_name'];
					$pname = $_COOKIE['flood_name'];
					$url = "http://";
					$pemail = $_COOKIE['flood_email'];
				}
			}else{
				if ((empty($_COOKIE['flood_email'])) || ($_COOKIE['flood_email'] == "")){
					$wname = $_COOKIE['flood_name'];
					$pname = $_COOKIE['flood_name'];
					$url = $_COOKIE['flood_site'];
					$pemail = "your@mail.com";
				}else{
					$wname = $_COOKIE['flood_name'];
					$pname = $_COOKIE['flood_name'];
					$url = $_COOKIE['flood_site'];
					$pemail = $_COOKIE['flood_email'];
				}
			}
		}
	}else{
		$wname = "Guest!";
		$pname = "name";
		$pemail="your@mail.com";
		$url="http://";
	}
	$message="";
	$posted="message";
	form();
}
?>