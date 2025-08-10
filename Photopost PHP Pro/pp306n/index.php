<?
//////////////////////////// COPYRIGHT NOTICE //////////////////////////////
// Program Name  	 : PhotoPost PHP                                  //
// Program Version 	 : 3.0.6                                          //
// Contributing Developer: Michael Pierce                                 //
// Supplied By           : Goshik [WTN]                                   //
// Nullified By          : CyKuH [WTN]                                    //
//  This script is part of PhotoPost PHP, a software application by       //
// All Enthusiast, Inc.  Use of any kind of part or all of this           //
// script or modification of this script requires a license from All      //
// Enthusiast, Inc.  Use or modification of this script without a license //
// constitutes Software Piracy and will result in legal action from All   //
//                                                                        //
//           PhotoPost Copyright 2002, All Enthusiast, Inc.               //
//                       Copyright WTN Team`2002                          //
////////////////////////////////////////////////////////////////////////////
include("pp-inc.php");
include("login-inc.php");

authenticate();
topmenu();

if ( IsSet($cat) ) {
    childsub($cat);
    $childnav = "<b><font face=\"verdana, arial, helvetica\" size=\"2\"><a href=\"".$Globals{'maindir'}."/index.php\">Home</a> $childnav";
}
else {
    if ( $username != "" && $username != "Unregistered" ) $childnav = "<b>Welcome, $username!</b>";
    else $childnav = "<b>Welcome to the ".$Globals{'galleryname'}."!</b>";
}

$output = "$header<Center><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" height=\"40\"
width=\"".$Globals{'tablewidth'}."\"><Tr>
 <Td valign=\"center\" width=\"50%\"><font face=\"verdana, arial\" size=\"2\">$childnav</font></td><td width=\"50%\" align=\"right\" valign=\"center\">
 <font face=\"verdana, arial\" size=\"2\">$menu&nbsp;</font>
 </td></tr></table>";

if ( $Globals{'mostrecent'} == "yes" && $Globals{'recentdefault'} == "no" ) {
    display_gallery("latest");
    $output .= "<p>";
}

if ( !empty($cat) ) {
    $query = "SELECT id,catname FROM categories WHERE id='$cat'";
    $ctitleq = mysql_query_eval($query, $link);
    $row = mysql_fetch_row($ctitleq);
    if ( $row ) {
        list( $catid, $cattitle ) = $row;
        $tablehead = "$cattitle";
    }
    else
        $tablehead = "";
}
else {
    $tablehead = $Globals{'galleryname'};
}


$output .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"
    bgcolor=\"".$Globals{'bordercolor'}."\" width=\"".$Globals{'tablewidth'}."\" align=\"center\"><tr><td><table cellpadding=\"4\"
    cellspacing=\"1\" border=\"0\" width=\"100%\"><tr align=\"center\"><td colspan=\"5\" align=\"left\"
    bgcolor=\"".$Globals{'headcolor'}."\">
    <Table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
    <form method=\"get\" action=\"".$Globals{'maindir'}."/showgallery.php\">
    <Tr><Td colspan=5><font face=\"".$Globals{'headfont'}."\"
    color=\"".$Globals{'headfontcolor'}."\"
    size=\"1\"><font size=\"2\" face=\"verdana\"><B>$tablehead</font></font></td><td
    align=\"right\">
    <input value=\"500\" type=\"hidden\" name=\"cat\">
    <Table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><Tr>
    <td valign=\"middle\"><font color=\"".$Globals{'headfontcolor'}."\" size=\"2\" face=\"verdana,arial\"><b>Search:&nbsp;</b></td>
    <td valign=\"middle\"><img src=\"".$Globals{'idir'}."/yellowline.gif\" width=\"1\" height=\"20\"></td>
    <Td><input CHECKED type=\"radio\" name=\"stype\" value=\"1\"><input type=\"hidden\" name=\"thumb\" value=\"1\">
    <font color=\"".$Globals{'headfontcolor'}."\" size=\"1\"
    face=\"verdana,arial\" color=\"".$Globals{'headfontcolor'}."\"><b>Keywords</font></td>
    <Td>&nbsp;&nbsp;<input type=\"radio\" name=\"stype\" value=\"2\"><font color=\"".$Globals{'headfontcolor'}."\" size=\"1\"
    color=\"".$Globals{'searchtext'}."\" face=\"verdana,arial\"><b>Username</b>&nbsp;</font></td>
    <td valign=\"middle\"><img src=\"".$Globals{'idir'}."/yellowline.gif\" width=\"1\" height=\"20\"></td>
    <td><!-- CyKuH [WTN] -->&nbsp;<input value=\"\" type=\"text\"
    name=\"si\" style=\"font-size: 8pt;\" size=\"15\"> <input type=\"submit\" value=\"Search\" style=\"font-size: 9pt;\"></td>
    </tr></table>

    </td></tr></form></table></td></tr>
    <tr align=\"center\">
    <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\"
    color=\"".$Globals{'headfontcolor'}."\" size=\"1\"><font size=\"1\" face=\"verdana,arial\"><b>Category</b>
    </font></td><Td bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\"
    color=\"".$Globals{'headfontcolor'}."\" size=\"1\"><b>Comments</b></font></td><Td
    bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
    size=\"1\"><b>Photos</center></b></font></td><Td bgcolor=\"".$Globals{'headcolor'}."\">
    <font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\" size=\"1\"><b>Last Comment</b></font></td>
    <Td bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
    size=\"1\"><B>Last Photo Upload</b></font></td></tr>";

$count = 0; $catdepth = 0;
$catrows = ""; $cptotal = 0; $posttotal = 0; $totalviews = 0; $diskspace = 0;

if ( !(IsSet($cat)) ) {
    catrow(0);
}
else {
    catrow($cat);
}

$output .= $catrows;

if ($Globals{'stats'} == "yes") {
    if ( !IsSet($cat) ) {
        $output .= "<tr><Td colspan=\"5\" bgcolor=\"".$Globals{'maincolor'}."\"><font color=\"".$Globals{'maintext'}."\" size=\"2\"
            face=\"verdana\"><center><b>$cptotal</b> photos with <b>$posttotal</b> comments and <b>$totalviews</b> views, requiring <b>$diskspace</b> of
            server disk space.</td></tr>";
    }
}

$output .= "</table></td></tr></table><p>";

if ( $Globals{'mostrecent'} == "yes" && $Globals{'recentdefault'} == "yes") {
    display_gallery("latest");
    $output .= "<p>";
}
if ( $Globals{'dispopular'} == "yes" ) {
    display_gallery("most_views");
}
if ( $Globals{'disrandom'} == "yes" ) {
    $output .= "<p>";
    display_gallery("random");
}

print $output."<p>".$Globals{'cright'}."<font size=\"1\" face=\"verdana,arial\">$zlibdebug</font>$footer";

// Closing connection
mysql_close($link);
mysql_close($db_link);

?>

