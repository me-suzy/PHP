<?php
/*
SHOUTPRO(TM) 1.0 BETA 1 - config.php
ShoutPro(TM) is released under the GNU General Public Liscense.  A full copy of this license is included with this distribution under the file LICENSE.TXT.  By using ShoutPro(TM) or the source code, you acknowledge you have read and agree to the license.

This file is config.php.  Edit all the variables to customize ShoutPro(TM).
*/


$textboxstyle = ".textbox {font-family: tahoma; font-size: 10pt; border: 1px solid #555555; background-color: #333333; color:#DDDDCC}"; //CSS code for the textbox style.  If you want a default style textbox then make this variable empty.


//Scrollbar styles - All of these variables have the same name as the CSS scrollbar setting except that the dashes have been replaced with underscores.
$scrollbar_face_color = "#666666";
$scrollbar_arrow_color = "#444444";
$scrollbar_track_color = "#333333";
$scrollbar_shadow_color = "#333333";
$scrollbar_highlight_color = "#333333";
$scrollbar_3dlight_color = "#333333";
$scrollbar_darkshadow_color = "#333333";

$scrollbar_styles_on = "on"; //This variable sets the scrollbar CSS styles on or off.  Everything other than "off" (case sensitive) will put the styles on.




$refresh = "30"; //The number of seconds between each automatic refresh of the shoutbox.  Default is 30.
$fontface = "Tahoma"; //The font used in the shoutbox.  Default is Tahoma.
$textcolor = "#DDDDCC"; //The main text color.  Default is #DDDDCC.
$bgcolor = "#333333"; //The background color of the shoutbox.  Default is #333333.
$textsize = "10"; //The text size in points.  Default is 10.

//Textmargins -- Make sure you leave $leftmargin and $rightmargin at 0 or there may be a horizontal scrollbar in your shoutbox.
$topmargin = "2";
$bottommargin = "2";
$leftmargin = "0";
$rightmargin = "0";

$inputshout = "Sorry, you have to input a shout."; //This message is shown if the user does not input a shout.  Default is "Sorry, you have to input a shout."

$namecolor = "#FF0000"; //The color users' names appear in.  Default is #FF0000.

$width = "150"; //The width of the shoutbox.  This needs to be the same as the width of the IFRAME.
?>