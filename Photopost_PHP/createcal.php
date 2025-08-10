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
require "languages/$pplang/showgallery.php";
require "login-inc.php";

if (empty($stype)) $stype="";
if (empty($si)) $si="";
if (empty($ppuser)) $ppuser="";

authenticate();

if ( isset($Globals['ppboards']) && $adminedit != 1 ) {
    if ( $Globals['ppboards'] == "closed" ) {
        print $Globals['pp_lang']['closed'];
        exit;
    }
}

$querystring = findenv("QUERY_STRING");
if ( $gologin == 1 || $querystring == "gologin" ) {
    $furl = $Globals['maindir'];
    $furl = str_replace( $Globals['domain'], "", $furl );
    $furl = "$furl/createcal.php";

    login( $furl );
    exit;
}

// do the sort box //
$query = "SELECT * FROM sort";
$resultc = ppmysql_query($query,$link);

if ( empty($sort) ) $sortparam = 1;
else $sortparam = $sort;

$sortoptions = ""; $sortdefault="";

while ( list($sortid, $sortname, $sortc) = mysql_fetch_row($resultc) ) {
    if ($sortparam != $sortid) {
        $sortoptions .= "<option value=\"$sortid\">$sortname</option>";
    }
    else {
        $sortdefault = "<option selected=\"selected\" value=\"$sortid\">$sortname</option>";
        $sortcode = "$sortc";
    }

    if ($sortdefault == "") {
        $sortdefault = "<option selected=\"selected\">{$Globals['pp_lang']['newest']}</option>";
    }
}
ppmysql_free_result( $resultc );

$sort = "<select onchange=\"submit();\" name=\"sort\" style=\"font-size: 9pt; background: FFFFFF;\">$sortdefault$sortoptions</select>";
// end sort box //

$searchterms = $si;
$cols = $Globals['thumbcols'];

list( $tcat, $tmail ) = get_username($userid);
$thecatname = "$tcat's {$Globals['pp_lang']['calendar']}";
printheader( 999, $thecatname );

childsub(999);
$childnav = "<font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['catfontsize']}\"><a href=\"{$Globals['maindir']}/index.php\">{$Globals['pp_lang']['home']}</a> $childnav</font>";
topmenu();

$searchbox = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr>
    <td align=\"right\"><!--PhotoPost, Copyright All Enthusiast, Inc.-->
    <input type=\"hidden\" name=\"thumb\" value=\"1\" />
    <input type=\"hidden\" name=\"cat\" value=\"999\" />
    <input type=\"text\" name=\"si\" style=\"font-size: 8pt;\" size=\"15\" value=\"$si\" />
    <input type=\"submit\" value=\"{$Globals['pp_lang']['search']}\" style=\"font-size: 9pt;\" />
    </td></tr></table>";
    
$sortbox = "<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\">
    <tr><td align=\"right\">
    <font size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['searchtext']}\"><b>{$Globals['pp_lang']['sortby']}</b>&nbsp;$sort
    <input type=\"hidden\" name=\"stype\" value=\"$stype\" />
    <input type=\"hidden\" name=\"ppuser\" value=\"$ppuser\" />
    </font></td></tr></table>";
    
$galleryhead = "<tr><td><table cellpadding=\"2\" cellspacing=\"1\" border=\"0\" width=\"100%\">
    <tr align=\"center\">
    <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\">
    <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
    <tr>
    <td align=\"left\" width=\"30%\"><font size=\"{$Globals['fontlarge']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['headfontcolor']}\">$childnav</font></td>
    <td align=\"right\" width=\"40%\">$sortbox</td>
    <td align=\"right\" width=\"30%\">$searchbox</td>
    </tr></table></td></tr>";    

$output = "<form method=\"get\" action=\"{$Globals['maindir']}/createcal.php\">    
    <table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td>
    <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>
    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\" align=\"center\">
    $galleryhead
    <!--7575-->
    </table></td></tr></table></form>";
    
$output .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\" align=\"center\">
    <tr><td><table cellpadding=\"5\" cellspacing=\"1\" border=\"0\" width=\"100%\">
    <tr align=\"center\"><td bgcolor=\"{$Globals['maincolor']}\">
    <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\">
    {$Globals['pp_lang']['calintro1']}
    </font></td></tr></table></td></tr></table>
    <form method=\"post\" action=\"http://www.photopost.com/print.pl\">
    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\" align=\"center\">
    <tr><td><table cellpadding=\"2\" cellspacing=\"1\" border=\"0\" width=\"100%\">
    <tr align=\"center\">";

$phrase="";
if ($si != "") {
    $sterms = trim($si);
    $searchterms = explode(" ", $sterms);
    $scount=0;
    $totalterms = count($searchterms);
    $totalterms++;

    foreach ($searchterms as $key) {
        $scount++;
        if ($scount > 1) {
            $phrase .= " AND ";
        }

        $key = addslashes( $key );
        $phrase .= "(title LIKE \"% $key%\" OR description LIKE \"% $key%\" OR keywords LIKE \"% $key%\" OR bigimage LIKE \"% $key%\" OR user LIKE \"% $key%\")";
        $phrase .= " OR (title LIKE \"$key%\" OR description LIKE \"$key%\" OR keywords LIKE \"$key%\" OR bigimage LIKE \"$key%\" OR user LIKE \"$key%\")";
    }
}

// My Favorites is used for calenders (check for search terms)
if ( empty($si) ) {
    $query = "SELECT f.userid,p.id,p.user,p.userid,p.cat,p.date,p.title,p.description,p.keywords,
        p.bigimage,p.width,p.height,p.filesize,p.views,p.medwidth,p.medheight,p.medsize,p.approved,p.rating,p.allowprint
        FROM favorites f, photos p
        WHERE f.userid=$userid AND f.photo=p.id AND (p.allowprint='yes' OR p.userid=$userid) $sortcode";
}
else {
    $query = "SELECT f.userid,p.id,p.user,p.userid,p.cat,p.date,p.title,p.description,p.keywords,
        p.bigimage,p.width,p.height,p.filesize,p.views,p.medwidth,p.medheight,p.medsize,p.approved,p.rating,p.allowprint
        FROM favorites f, photos p
        WHERE ($phrase) AND f.userid=$userid AND f.photo=p.id AND (p.allowprint='yes' OR p.userid=$userid) $sortcode";                    
}

$queryv = ppmysql_query($query, $link);
$rowcnt = mysql_num_rows($queryv);

$noresults="";
if ($rowcnt == "0") {
    $noresults = "<center><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['nophotos']}<br /><br /></font></center>";
}

$count=0; $imgcount=0;
$numcols = $Globals['thumbcols']+1;
$pwidth = intval(100/($numcols-1));

while ( $row = mysql_fetch_row($queryv) ) {
    list( $favid, $id, $tuser, $tuserid, $pcat, $date, $title, $desc, $keywords, $bigimage, $width, $height, $filesize, $views, $medwidth, $medheight, $medsize, $approved, $imgrating, $allowprint ) = $row;

    if ( is_image($bigimage) ) {
        $imgcount++;
        $count++;
        if ($count == $numcols) {
            $output .= "</tr><tr>";
            $count = 1;
        }
    
        $thumbrc = get_imagethumb( $bigimage, $pcat, $tuserid, $approved );
    
        $output .= "<td bgcolor=\"{$Globals['maincolor']}\" valign=\"top\" align=\"left\" width=\"$pwidth%\">
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"center\" height=\"125\">
            <a href=\"{$Globals['maindir']}/showphoto.php?photo=$id&amp;papass=$papass&amp;sort=$sortparam&amp;thecat=999\">$thumbrc</a></td></tr></table>";
    
        // Here is where we add the selection code
        $output .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"center\">
            <font size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">
            <input type=\"checkbox\" name=\"calimg-$imgcount\" value=\"$thumbtag\" />
            </font></td>";
            
        $output .= "</td></tr></table>";
        $output .= "</td>";
    }
}

ppmysql_free_result( $queryv );

$squares = $Globals['thumbcols']-$count;
for ($v=1; $v <= $squares; $v++) {
    $output .= "<td bgcolor=\"{$Globals['maincolor']}\" width=\"$pwidth%\">&nbsp;</td>";
}

$output .= "</tr></table></td></tr></table>$noresults";

print "$output";
print "<br /><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\" align=\"center\">
    <tr><td><table cellpadding=\"5\" cellspacing=\"1\" border=\"0\" width=\"100%\">
    <tr align=\"center\"><td bgcolor=\"{$Globals['maincolor']}\">
    <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\">
    {$Globals['pp_lang']['calintro2']}
    <input type=\"hidden\" name=\"calid\" value=\"{$Globals['calid']}\" />
    <br /><br /><input type=\"submit\" value=\"{$Globals['pp_lang']['submit']}\" />
    </font></td></tr></table></td></tr></table>";

print "</form>{$Globals['cright']}";

printfooter();

?>
