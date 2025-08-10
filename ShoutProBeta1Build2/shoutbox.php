<?php

/*
SHOUTPRO(TM) 1.0 BETA 1 - shoutbox.php
ShoutPro(TM) is released under the GNU General Public Liscense.  A full copy of this license is included with this distribution under the file LICENSE.TXT.  By using ShoutPro(TM) or the source code, you acknowledge you have read and agree to the license.

This file is shoutbox.php.  It is the main part of ShoutPro(TM).  There is no need to modify anything in this file.  All modifications should be done in the file config.php.
*/



if (!@include("config.php")){
	die ("<b>SHOUTPRO ERROR</b>: config.php cannot be found.");
}
?>

<html><head><title>ShoutBox</title>

<?php //echo("<META HTTP-EQUIV='refresh' content='".$refresh.";URL=shoutbox.php'>"); ?>

<style type="text/css">
<!--
<?php echo($textboxstyle); ?>
-->
</style>

<!-- Begin Scrollbar Styles -->
<style>  
<!-- 
<?php
if($scrollbar_styles_on != "off"){
echo("BODY{ scrollbar-face-color:".$scrollbar_face_color."; scrollbar-arrow-color:".$scrollbar_arrow_color."; scrollbar-track-color:".$scrollbar_track_color."; scrollbar-shadow-color:".$scrollbar_shadow_color."; scrollbar-highlight-color:".$scrollbar_highlight_color."; scrollbar-3dlight-color:".$scrollbar_3dlight_color."; scrollbar-darkshadow-Color:".$scrollbar_darkshadow_color."; }");
}
?>

-->   
</style>    
<!-- End Scrollbar Styles -->

</head>
<?php
echo("<body bgcolor=".$bgcolor." text=".$textcolor." bottommargin=".$bottommargin." topmargin=".$topmargin." leftmargin=".$leftmargin." rightmargin=".$rightmargin.">");
?>
<font style='font-size: <?php echo($textsize); ?>pt' face='<?php echo($fontface); ?>'>
<?php


function killhtml($shout){
	//This function searches the shout for HTML tags and replaces them with the actual symbol.
	$shout = str_replace("<","&lt;",$shout);
	$shout = str_replace(">","&gt;",$shout);
	return $shout;
}

function shoutcode($shout){
	//This function parses the ShoutCode.
	$FileName="lists/shoutcode.php";
	$list = file ($FileName);
	foreach ($list as $value) {
		list ($shoutcode,$html,) = explode ("|^|", $value);
		$shout = str_replace ($shoutcode, $html, $shout);
	}
	$shout = $shout."</b></u></i>";
	return $shout;
}

function smilies($shout){
	//This function searches the shout for the smilies and replaces it with the image code.
	$FileName="lists/smilies.php";
	$list = file ($FileName);
	foreach ($list as $value) {
		list ($code,$image,) = explode ("|^|", $value);
		$shout = str_replace ($code, "<img src='smilies/".$image."'>", $shout);
	}
	return $shout;
}

function profanityfilter($shout){
	//This function filters profanities from the shout.
	$FileName="lists/profanities.php";
	$list = file ($FileName);
	foreach ($list as $value) {
		list ($profanity,$filter,) = explode ("|^|", $value);
		$shout = str_replace ($profanity, $filter, $shout);
	}
	return $shout;
}

if($action=="post"){
	if(!$shout || $shout=="Shout!"){
		echo("<script>alert(\"".$inputshout."\");</script>");
	} else {
		//Get rid of HTML tags, parse shoutcode and smilies, filter curses
		$shout=killhtml($shout);
		$shout=shoutcode($shout);
		$shout=smilies($shout);
		$shout=profanityfilter($shout);
		//Read the current contents of shouts.php
		$FileName="shouts.php";
		if($FilePointer=fopen($FileName,"r")){
			$oldshouts=fread($FilePointer,filesize($FileName));
			fclose($FilePointer);
		}
		//Create newshouts.php and add the new shout along with the old ones
		$FileName="newshouts.php";
		if($FilePointer=fopen($FileName, "a+")){
			$width=$width-30;
			fwrite($FilePointer,"<table cellpadding=0 cellspacing=0 width=".$width."><tr><td width=".$width."><font style='font-size: <?php echo($textsize); ?>pt' face='<?php echo($fontface); ?>'><font color=".$namecolor.">$name:</font> $shout</td></tr></table>\n$oldshouts");
			fclose($FilePointer);
			//Delete the current shouts.php and replace it with newshouts.php
			unlink("shouts.php");
			rename("newshouts.php","shouts.php");
		}
	}
}

if ($action=="post"){echo("<script>location.href='shoutbox.php';</script>");}


//The form
echo("<form name='postshout' method='post' action='shoutbox.php?action=post'>");
echo("<input class='textbox' name='name' type='text' size='6' value='Name' onFocus=\"this.value=''\"><br>\n"); 
echo("<textarea class=textbox name='shout' rows='5' cols='15'  onFocus=\"this.value=''\">Shout!</textarea><br>\n");
echo("<input class=textbox type='submit' value='Post'>\n");
echo("<input class=textbox type='button' onClick=\"window.open('help.php','help_window','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=no,width=270,height=400')\"  value='Help'>");
echo("<br>");

include("shouts.php");

?>