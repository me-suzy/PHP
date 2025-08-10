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
require "languages/$pplang/index.php";
require "login-inc.php";

if ( file_exists("install.php") || file_exists("{$Globals['maindir']}/install.php") ) {
    diewell( "For security reasons, please remove the install.php from the PhotoPost directory before proceeding." );
    exit;
}

authenticate();

if ( isset($Globals['ppboards']) && $adminedit != 1 ) {
    if ( $Globals['ppboards'] == "closed" ) {
        print $Globals['pp_lang']['board_closed'];
        exit;
    }
}

topmenu();

if ( isset($cat) ) {
    if ( $userid > 0 ) {
        list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
        $mon = $mon + 1;    
        $lasttimeon = mktime($hour,$min,$sec,$mon,$mday,$year);
        
        $laston = "REPLACE INTO laston VALUES($cat,$userid,$lasttimeon)";
        $resultb = ppmysql_query($laston, $link);    
    }
    
    childsub($cat);
    $childnav = "<font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['catfontsize']}\"><a href=\"{$Globals['maindir']}/index.php\">{$Globals['pp_lang']['home']}</a> $childnav</font>";
    $searchcat = $cat;
}
else {
    if ( $username != "" && $username != "Unregistered" ) $childnav = "{$Globals['pp_lang']['welcomeuser']} $username!";
    else $childnav = "{$Globals['pp_lang']['welcome']} {$Globals['galleryname']}!";
    $searchcat = 998;
}

if ( !empty($cat) ) {
    $query = "SELECT id,catname FROM categories WHERE id='$cat'";
    $ctitleq = ppmysql_query($query, $link);
    if ( $ctitleq ) {
        list( $catid, $cattitle ) = mysql_fetch_row($ctitleq);
        ppmysql_free_result( $ctitleq );
        $tablehead = "$cattitle";        
    }
    else
        $tablehead = "";
}
else {
    $tablehead = $Globals['pp_lang']['tablehead'];
}

printheader( $thecat, $tablehead );

$output = "<br /><table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>
    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td><td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>";

if ( $Globals['mostrecent'] == "yes" && $Globals['recentdefault'] == "no" ) {
    display_gallery("latest");
}

$output .= "<form method=\"get\" action=\"{$Globals['maindir']}/showgallery.php\">    
    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"
    bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr><td><table cellpadding=\"4\"
    cellspacing=\"1\" border=\"0\" width=\"100%\"><tr align=\"center\"><td colspan=\"5\" align=\"left\"
    bgcolor=\"{$Globals['headcolor']}\">
    <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
    <tr><td>
    <font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\" face=\"{$Globals['mainfonts']}\">&nbsp;$childnav</font>
    </td><td align=\"right\">
    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr>
    <td align=\"right\"><!--PhotoPost, Copyright All Enthusiast, Inc.-->
    <input type=\"hidden\" name=\"cat\" value=\"$searchcat\" />    
    <input type=\"hidden\" name=\"thumb\" value=\"1\" />
    <input type=\"text\" name=\"si\" style=\"font-size: 8pt;\" size=\"15\" value=\"\" />
    <input type=\"submit\" value=\"Search\" style=\"font-size: 9pt;\" />
    </td></tr><tr><td colspan=\"6\" align=\"right\">
    <font color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['recent']}
    <a href=\"{$Globals['maindir']}/showgallery.php?cat=997&amp;thumb=1\">{$Globals['pp_lang']['lastday']}</a>
    &nbsp;<a href=\"{$Globals['maindir']}/showgallery.php?cat=996&amp;thumb=1\">{$Globals['pp_lang']['last7']}</a>
    &nbsp;<a href=\"{$Globals['maindir']}/showgallery.php?cat=995&amp;thumb=1\">{$Globals['pp_lang']['last14']}</a>
    &nbsp;<a href=\"{$Globals['maindir']}/showgallery.php?cat=998&amp;thumb=1\">{$Globals['pp_lang']['allimages']}</a>
    </font></td></tr>
    </table>

    </td></tr></table></td></tr>
    <tr align=\"center\">
    <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\"><b>{$Globals['pp_lang']['category']}</b></font></td>
    <td align=\"center\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\"><b>{$Globals['pp_lang']['photos']}</b></font></td>";
    
if ( $Globals['allowpost'] == "yes" ) {
    $output .= "<td align=\"center\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\"><b>{$Globals['pp_lang']['comments']}</b></font></td>
    <td align=\"center\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\"><b>{$Globals['pp_lang']['lastcomment']}</b></font></td>";
}

$output .= "<!--REPLACEME-->
    <td align=\"center\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\"><b>{$Globals['pp_lang']['lastphoto']}</b></font></td>
    </tr>";

$count = 0; $catdepth = 0;
$catrows = ""; $cptotal = 0; $posttotal = 0; $totalviews = 0; $diskspace = 0;

if ( !(isset($cat)) ) {
    catrow(0);
}
else {
    catrow($cat);
}

$output .= $catrows;

$usertotal = get_totalusers();

$query = "SELECT SUM(views), SUM(filesize) AS fsize FROM photos";
$totalv = ppmysql_query($query,$link);
list( $totalviews, $diskuse ) = mysql_fetch_row($totalv);
ppmysql_free_result($totalv);

$totalviews = number_format( $totalviews );
$totalphotos = number_format( $totalphotos );
$usertotal = number_format( $usertotal );
$posttotal = number_format( $posttotal );

$diskspace = $diskuse/1048576;
$diskspace = number_format( $diskspace, 1 );
$diskspace = "$diskspace MB";

// Lets get the Top 5 Posters
$query = "SELECT user,userid,COUNT(*) AS pcount FROM photos GROUP BY user ORDER BY pcount DESC";
$queryz = ppmysql_query($query,$link);
$rowcnt = mysql_num_rows($queryz);
$numfound = 0;

while ( list($theuser, $theuserid, $uphotos) = mysql_fetch_row($queryz)) {
    $numfound++;
    $topposters[$numfound] = $theuser;
    $topid[$numfound] = $theuserid;
    $topposts[$numfound] = $uphotos;
    if ( $numfound == 5 ) break;
}

$toplist = "<br />{$Globals['pp_lang']['toposter']}";
for ( $x=1; $x < ($numfound+1); $x++ ) {
    $toplist .= "&nbsp;&nbsp;&nbsp;<a href=\"{$Globals['maindir']}/showgallery.php?cat=500&amp;ppuser=$topid[$x]&amp;thumb=1\">$topposters[$x] <font size=\"{$Globals['fontsmall']}\">($topposts[$x])</font></a>";
}

if ($Globals['stats'] == "yes") {
    if ( !isset($cat) ) {
        $output .= "<tr><td colspan=\"5\" bgcolor=\"{$Globals['maincolor']}\" align=\"center\"><font color=\"{$Globals['maintext']}\" size=\"{$Globals['fontmedium']}\"
            face=\"{$Globals['mainfonts']}\">$usertotal {$Globals['pp_lang']['regusers']} $totalphotos ";
            
        if ( $Globals['allowpost'] == "yes" ) {
            $output .= "{$Globals['pp_lang']['posted']} $posttotal {$Globals['pp_lang']['posts']}";
        }
        else {
            $output .= "{$Globals['pp_lang']['postednoc']}";
        }
        
        $output .= "<br />
            $totalviews {$Globals['pp_lang']['views']} $diskspace {$Globals['pp_lang']['diskspace']}$toplist</font></td></tr>";
    }
}

$output .= "</table></td></tr></table></form>";

if ( $Globals['mostrecent'] == "yes" && $Globals['recentdefault'] == "yes") {
    display_gallery("latest");
}
if ( $Globals['dispopular'] == "yes" ) {
    display_gallery("most_views");
}
if ( $Globals['disrandom'] == "yes" ) {
    display_gallery("random");
}

print "$output{$Globals['cright']}";
printfooter();

?>
