<?
//////////////////////////// COPYRIGHT NOTICE //////////////////////////////
// This script is part of PhotoPost PHP, a software application by        //
// All Enthusiast, Inc.  Use of any kind of part or all of this           //
// script or modification of this script requires a license from All      //
// Enthusiast, Inc.  Use or modification of this script without a license //
// constitutes Software Piracy and will result in legal action from All   //
// Enthusiast, Inc.  All rights reserved.                                 //
// http://www.photopost.com      legal@photopost.com                      //
// Contributing Developer: Michael Pierce (danasoft.com)                  //
//                                                                        //
//            PhotoPost Copyright 2002, All Enthusiast, Inc.              //
////////////////////////////////////////////////////////////////////////////

require "pp-inc.php";
require "languages/$pplang/slideshow.php";

if ( isset($Globals['ppboards']) && $adminedit != 1 ) {
    if ( $Globals['ppboards'] == "closed" ) {
        print $Globals['pp_lang']['closed'];
        exit;
    }
}

if (empty($size)) $size="medium";
if (empty($cat)) $cat=0;

topmenu();

printheader( $cat, "{$Globals['galleryname']} {$Globals['pp_lang']['slideshow']}" );

$output = "<p><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\"><tr>
    <td valign=\"bottom\" width=\"50%\">&nbsp;</td>
    <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>
    <p><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
    align=\"center\"><tr><td>
    <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
    <tr align=\"center\">
    <td colspan=\"4\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontlarge']}\">{$Globals['galleryname']} {$Globals['pp_lang']['slideshow']}
    </font></td>
    </tr>
    <form method=\"post\" action=\"{$Globals['maindir']}/showphoto.php\">";

$output .= "<tr><td bgcolor=\"{$Globals['maincolor']}\" width=\"50%\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">
    {$Globals['pp_lang']['delay']}</font></td><td bgcolor=\"{$Globals['maincolor']}\"><select name=\"slidedelay\">";

$output .= "<option value=\"2\">{$Globals['pp_lang']['twosec']}</option>";
$output .= "<option selected value=\"4\">{$Globals['pp_lang']['foursec']}</option>";
$output .= "<option value=\"8\">{$Globals['pp_lang']['eightsec']}</option>";
$output .= "<option value=\"10\">{$Globals['pp_lang']['tensec']}</option>";

$output .= "</select></td></tr>
    <center>
    <tr><td colspan=\"2\" bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><center>
    <input type=\"hidden\" name=\"photo\" value=\"$photo\">
    <input type=\"hidden\" name=\"sort\" value=\"$sort\">
    <input type=\"hidden\" name=\"thecat\" value=\"$thecat\">        
    <input type=\"hidden\" name=\"size\" value=\"$size\">
    <input type=\"hidden\" name=\"slideshow\" value=\"1\">    
    <input type=\"submit\" value=\"{$Globals['pp_lang']['startslide']}\"></td></tr></table></td></tr></table><p>{$Globals['cright']}";

print $output;
printfooter();

?>
