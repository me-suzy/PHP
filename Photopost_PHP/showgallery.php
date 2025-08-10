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
if (empty($thumb)) $thumb=0;

if ( !isset($cat) ) {
    diewell( $Globals['pp_lang']['errorscript'] );
    exit;
}

authenticate();

if ( isset($Globals['ppboards']) && $adminedit != 1 ) {
    if ( $Globals['ppboards'] == "closed" ) {
        print $Globals['pp_lang']['closed'];
        exit;
    }
}

if ($ugview{$cat} == 1 ) {
    diewell( $Globals['pp_lang']['noview'] );
    exit;
}

if ( $userid > 0 ) {
    list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
    $mon = $mon + 1;
    $lasttimeon = mktime($hour,$min,$sec,$mon,$mday,$year);

    $laston = "REPLACE INTO laston VALUES($cat,$userid,$lasttimeon)";
    $resultb = ppmysql_query($laston, $link);
}

$perpage1x = $Globals['thumbcols'];
$perpage3x = ($perpage1x * 3);
$perpage4x = ($perpage1x * 4);
$perpage5x = ($perpage1x * 5);
$perpage6x = ($perpage1x * 6);

if ( isset($perpage) ) {
    if ($perpage > $perpage6x)
        $perpage = $perpage6x;
}
else
    $perpage = $perpage3x;

if ( isset($page) ) {
    $startnumb = ($page*$perpage)-$perpage+1;
}
else {
    $page=1;
    $startnumb=1;
}

if ( isset($cat) ) {
    if ( $thumb != 0 ) {
        $thecat = $cat;

        // do they have permission to be here?
        if ( $thecat > 3000 ) {
            $catthumbs = "yes";

            $query = "SELECT id,albumname,parent,isprivate,password FROM useralbums WHERE id=$thecat";
            $resultb = ppmysql_query($query,$link);

            if ( $resultb ) {
                list( $thecatid, $thecatname, $parent, $isprivate, $password ) = mysql_fetch_row($resultb);
                ppmysql_free_result( $resultb );

                if ( ($isprivate == "yes" && $userid != $parent) && $adminedit != 1 ) {
                    if ( empty($papass) ) $papass = "";
                    if ( $password != $papass ) {
                        diewell( $Globals['pp_lang']['noperm'] );
                        exit;
                    }
                }
            }
        }

        // do the sort box //
        $query = "SELECT * FROM sort";
        if ($thecat == "500") {
            if ( empty($ppuser) && empty($si) ) {
                $query = "SELECT * FROM sortmemb";
            }
        }
        $resultc = ppmysql_query($query,$link);

        if ( empty($sort) ) $sortparam = 1;
        else $sortparam = $sort;

        $sortoptions = ""; $sortdefault=""; $catrows="";

        while ( list($sortid, $sortname, $sortc) = mysql_fetch_row($resultc) ) {
            if ($sortparam != $sortid) {
                $sortoptions .= "<option value =\"$sortid\">$sortname</option>";
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

        $albums=""; $personal=0;
        $subcats=""; $albumrow="";
        $searchterms = $si;
        $incat = $cat;

        if ($ppuser != "") {
            if ( $cat == 500 ) {
                list( $tcat, $tmail ) = get_username($ppuser);
                $thecatname = "$tcat's Gallery";
                $titlereplace = "$tcat's {$Globals['pp_lang']['gallery']}";
            }
            elseif ( $cat > 3000 ) {
                list( $tcat, $tmail ) = get_username($ppuser);
                $thecatname = "$tcat's Personal Album";
                $titlereplace = "$tcat's {$Globals['pp_lang']['album']}";
            }
        }
        else {
            if ( $cat == "999" ) {
                list( $tcat, $tmail ) = get_username($userid);
                $thecatname = "$tcat's {$Globals['pp_lang']['favorites']}";
            }
            elseif ( $cat == "998" ) {
                $thecatname = $Globals['pp_lang']['callimages'];
            }
            elseif ( $cat == "997" ) {
                $thecatname = $Globals['pp_lang']['clastday'];
            }
            elseif ( $cat == "996" ) {
                $thecatname = $Globals['pp_lang']['clast7'];
            }
            elseif ( $cat == "995" ) {
                $thecatname = $Globals['pp_lang']['clast14'];
            }
            elseif ( $cat == "991" ) {
                $thecatname = $Globals['pp_lang']['searchres'];
            }
            else {
                $query = "SELECT id,catname FROM categories WHERE id='$cat'";
                $ctitleq = ppmysql_query($query, $link);
                if ( $ctitleq ) {
                    list( $catid, $thecatname ) = mysql_fetch_row($ctitleq);
                    ppmysql_free_result( $ctitleq );
                }
            }
        }

        printheader( $thecat, $thecatname );

        childsub($incat);
        $childnav = "<font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['catfontsize']}\"><a href=\"{$Globals['maindir']}/index.php\">{$Globals['pp_lang']['home']}</a> $childnav</font>";

        topmenu();

        $searchbox = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr>
            <td align=\"right\"><!--PhotoPost, Copyright All Enthusiast, Inc.-->
            <input type=\"hidden\" name=\"thumb\" value=\"1\" />
            <input type=\"hidden\" name=\"cat\" value=\"$cat\" />
            <input type=\"text\" name=\"si\" style=\"font-size: 8pt;\" size=\"15\" value=\"$si\" />
            <input type=\"submit\" value=\"{$Globals['pp_lang']['search']}\" style=\"font-size: 9pt;\" />
            </td></tr><tr><td colspan=\"6\" align=\"right\">
            <font color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['recent']}:
            <a href=\"{$Globals['maindir']}/showgallery.php?cat=997&amp;thumb=1\">{$Globals['pp_lang']['lastday']}</a>
            &nbsp;<a href=\"{$Globals['maindir']}/showgallery.php?cat=996&amp;thumb=1\">{$Globals['pp_lang']['last7']}</a>
            &nbsp;<a href=\"{$Globals['maindir']}/showgallery.php?cat=995&amp;thumb=1\">{$Globals['pp_lang']['last14']}</a>
            &nbsp;<a href=\"{$Globals['maindir']}/showgallery.php?cat=998&amp;thumb=1\">{$Globals['pp_lang']['allimages']}</a>
            </font></td></tr>
            </table>";

        if ($cat == "500") {
            if ($si == "") {
                if ( $ppuser == "" ) {
                    $thumb = 2;
                }
                else {
                    $query = "SELECT id,albumname,description,isprivate FROM useralbums WHERE parent=$ppuser";
                    $arows = ppmysql_query($query,$link);

                    if ( $arows > 0 ) {
                        while ( list( $subid, $subalbumname, $subalbumdesc, $isprivate ) = mysql_fetch_row($arows) ) {
                            if ( empty($subalbumdesc) ) $subalbumdesc = "&nbsp;";

                            if ( $isprivate == "no" || ($isprivate == "yes" && ($userid == $ppuser || $adminedit == 1)) ) {
                                $albumrow .= "<tr><td width=\"30%\" bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><a href=\"{$Globals['maindir']}/showgallery.php?cat=$subid&amp;ppuser=$ppuser&amp;thumb=1\">$subalbumname</a></font>
                                </td><td align=\"left\" width=\"70%\" bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$subalbumdesc</font>
                                </td></tr>\n";
                            }
                        }

                        if ( $arows )
                            ppmysql_free_result( $arows );

                        if ( $albumrow != "" ) {
                            $albums .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\" align=\"center\">
                                <tr><td>
                                <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\" width=\"100%\">
                                <tr align=\"center\">
                                <td align=\"left\" colspan=\"$cols\" bgcolor=\"{$Globals['headcolor']}\">
                                <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                                <tr><td>
                                <font size=\"{$Globals['fontlarge']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['headfontcolor']}\">$childnav</font>
                                </td><td align=\"right\">$searchbox</td>
                                </tr></table>
                                </td>
                                <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\" width=\"100%\">
                                <tr><td bgcolor=\"{$Globals['headcolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\" face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['albums']}</b></font>
                                </td><td align=\"left\" bgcolor=\"{$Globals['headcolor']}\" nowrap=\"nowrap\"><font color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['desc']}</b></font>
                                </td></tr>$albumrow</table></td></tr></table><br />";
                        }

                        $personal=1;
                    }
                }
            }
        }
        else {
            if ( empty($si) ) {
                catrow( $cat );

                if ( !empty($catrows) ) {
                    $subcats = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\" align=\"center\">
                        <tr><td>
                        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\" width=\"100%\">
                        <tr align=\"center\">
                        <td align=\"left\" colspan=\"$cols\" bgcolor=\"{$Globals['headcolor']}\">
                        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                        <tr><td>
                        <font size=\"{$Globals['fontlarge']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['headfontcolor']}\">$childnav</font>
                        </td><td align=\"right\">$searchbox</td>
                        </tr></table></td></tr></table>
                        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\" width=\"100%\">
                        <tr align=\"center\">
                        <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\"><b>{$Globals['pp_lang']['category']}</b></font>
                        </td><td bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\"><b>{$Globals['pp_lang']['photos']}</b></font>
                        </td><td bgcolor=\"{$Globals['headcolor']}\" align=\"center\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\"><b>{$Globals['pp_lang']['comments']}</b></font>
                        </td><td bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\"><b>{$Globals['pp_lang']['lastcomment']}</b></font>
                        </td><td bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\"><b>{$Globals['pp_lang']['lastphoto']}</b></font>
                        </td></tr>";

                    $subcats .= $catrows;
                    $subcats .= "</table></td></tr></table><br />";
                }
            }
        }

        if ( !empty($incat) ) {
            if ( $incat == "500" )
                $cols = 6;
            else
                $cols = $Globals['thumbcols'];
        }
        else {
            $cols = $Globals['thumbcols'];
        }

        $output .= "<form method=\"get\" action=\"{$Globals['maindir']}/showgallery.php\">
            <table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td>
            <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>";

        if ( ($incat < 990 || $incat > 2999) && empty($si) ) {
            if ( $Globals['memformat'] == "no" && ($incat == "500" && $ppuser != "") ) {
                if ( $Globals['mostrecent'] == "yes" && $Globals['recentdefault'] == "no" ) {
                    display_gallery("latest", $ppuser);
                }
                else {
                    $output .= "<br />";
                }

                if ( $si == "" ) {
                    list( $tname, $tmail ) = get_username($ppuser);
                    $output .= "<center><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"><b><a href=\"{$Globals['maindir']}/showgallery.php?thumb=1&amp;stype=2&amp;si=$tname&amp;cat=500&amp;sort=1&amp;ppuser=$ppuser\">{$Globals['pp_lang']['tosee']} $tname's {$Globals['pp_lang']['littlephotos']}</a></b></font></center><br />";
                }
            }
            elseif ( $Globals['memformat'] == "no" && ($Globals['mostrecent'] == "yes" && $Globals['recentdefault'] == "no") ) {
                display_gallery("latest", "", $incat);
            }
        }

        if ( !empty($albums) ) {
            $output .= "$albums";
            $galleryhead = "<tr><td><table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">";
        }
        elseif ( !empty($subcats) ) {
            $output .= "<form method=\"get\" action=\"{$Globals['maindir']}/showgallery.php\">$subcats";
            $galleryhead = "<tr><td><table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">";
        }
        else {
            $output .= "<form method=\"get\" action=\"{$Globals['maindir']}/showgallery.php\">";
            $galleryhead = "<tr><td><table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
                <tr align=\"center\">
                <td align=\"left\" colspan=\"$cols\" bgcolor=\"{$Globals['headcolor']}\">
                <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                <tr><td>
                <font size=\"{$Globals['fontlarge']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['headfontcolor']}\">$childnav</font>
                </td><td align=\"right\">$searchbox</td></tr>
                </table>
                </td></tr>";
        }

        $output .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\" align=\"center\">
            $galleryhead";

        if ($incat != "" && $thumb != 1) {
            if ($incat != "500" ) {
                $space = catrow($incat);
            }

            if ( isset($space) ) {
                $catrows .= "</table></td></tr></table><br /><br /><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"
                    bgcolor=\"{$Globals['bordercolor']}\" width=\"100%\" align=\"center\"><tr><td><table cellpadding=\"0\"
                    cellspacing=\"1\" border=\"0\" width=\"100%\"><tr align=\"center\"><td colspan=\"5\" align=\"left\">";
            }
            $output .= $catrows;
        }

        $output .= "<";
        $output .= "!--7575-->";
        $output .= "<tr><td bgcolor=\"{$Globals['navbarcolor']}\" colspan=\"$cols\">
            <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\">
            <tr align=\"center\">
            <td width=\"50%\" align=\"left\"><font size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\"
            color=\"{$Globals['searchtext']}\"><b>{$Globals['pp_lang']['perpage']}</b></font>
            <select onchange=\"submit();\" name=\"perpage\" style=\"font-size: 9pt; background: FFFFFF;\">
            <option selected=\"selected\">$perpage</option><option>$perpage3x</option><option>$perpage4x</option><option>$perpage5x</option><option>$perpage6x</option></select>
            </td>

            <td width=\"50%\" align=\"right\"><font size=\"{$Globals['fontsmall']}\"
            face=\"{$Globals['mainfonts']}\" color=\"{$Globals['searchtext']}\"><b>{$Globals['pp_lang']['sortby']}</b> $sort
            <input type=\"hidden\" name=\"stype\" value=\"$stype\" />
            <input type=\"hidden\" name=\"ppuser\" value=\"$ppuser\" />
            </font></td>
            </tr>
            </table></td></tr><tr>";

        // If we're not in the member gallery cat, then print thumbs..
        // Otherwise, print a list of users.
        $phrase="";

        if ( $thumb != 2 ) {
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
                    $phrase .= " OR (title LIKE \"$key%\" OR description LIKE \"$key%\" OR keywords LIKE \"$key%\" OR bigimage LIKE \"$key%\" OR user LIKE \"%$key%\")";
                }
            }

            if ( $personal == 1 ) {
                $exclude_cat .= " AND cat < 3000";
            }
            elseif ( $cat > 3000 ) {
                $exclude_cat .= " AND cat=$cat";
            }

            if ( $cat == 999 ) {
                // My Favorites
                if ( empty($si) ) {
                    $query = "SELECT f.userid,p.id,p.user,p.userid,p.cat,p.date,p.title,p.description,p.keywords,
                        p.bigimage,p.width,p.height,p.filesize,p.views,p.medwidth,p.medheight,p.medsize,p.approved,p.rating,p.allowprint
                        FROM favorites f, photos p
                        WHERE f.userid=$userid AND f.photo=p.id $sortcode";
                }
                else {
                    $query = "SELECT f.userid,p.id,p.user,p.userid,p.cat,p.date,p.title,p.description,p.keywords,
                        p.bigimage,p.width,p.height,p.filesize,p.views,p.medwidth,p.medheight,p.medsize,p.approved,p.rating,p.allowprint
                        FROM favorites f, photos p
                        WHERE ($phrase) AND f.userid=$userid AND f.photo=p.id $sortcode";                    
                }
                $queryv = ppmysql_query($query, $link);
            }
            elseif ( $cat == 998 ) {
                // All Images
                if ( empty($si) ) {
                    $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating,allowprint FROM photos
                        WHERE bigimage!='' $exclude_cat $sortcode";
                    $queryv = ppmysql_query($query, $link);
                }
                else {
                    $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating,allowprint FROM photos
                        WHERE ($phrase) AND bigimage!='' $exclude_cat $sortcode";
                    $queryv = ppmysql_query($query, $link);
                }
            }
            elseif ( $cat > 994 && $cat < 998 ) {
                // Last 1 days
                if ( $cat == 995 ) $days = 14;
                elseif ( $cat == 996 ) $days = 7;
                else $days = 1;

                list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
                $mon = $mon + 1;
                $hour = $hour - ($days * 24);
                $searchdate = mktime($hour,$min,$sec,$mon,$mday,$year);

                $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating,allowprint FROM photos
                        WHERE bigimage!='' AND date > $searchdate $exclude_cat $sortcode";
                $queryv = ppmysql_query($query, $link);
            }
            elseif ($si == "") {
                if ($ppuser == "") {
                    $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating,allowprint FROM photos
                        WHERE bigimage!='' AND cat=$thecat $exclude_cat $sortcode";
                }
                else {
                    if ( $Globals['memformat'] == "yes" ) {
                        $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating,allowprint FROM photos
                            WHERE bigimage!='' AND userid=$ppuser $exclude_cat $sortcode";
                    }
                    else {
                        $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating,allowprint FROM photos
                            WHERE bigimage!='' AND userid=$ppuser AND cat=$thecat $exclude_cat $sortcode";
                    }
                }
                $queryv = ppmysql_query($query,$link);
            }
            else {
                if ($stype == "") {
                    $stype=1;
                }

                if ($stype == "1") {
                    if ($thecat != 500) {
                        $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating,allowprint
                            FROM photos WHERE ($phrase) AND cat=$thecat $exclude_cat $sortcode";
                        $queryv = ppmysql_query($query,$link);
                    }
                    else {
                        if ($phrase != "") {
                            $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating,allowprint
                                FROM photos WHERE $phrase $exclude_cat $sortcode";
                            $queryv = ppmysql_query($query,$link);
                        }
                        else {
                            if ($exclude_cat) {
                                $exclude_cat = str_replace("AND", "WHERE", $exclude_cat);
                            }
                            $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating,allowprint
                                FROM photos $exclude_cat $sortcode";
                            $queryv = ppmysql_query($query,$link);
                        }
                    }
                }
                else {
                    if ($thecat != 500) {
                        $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating,allowprint
                            FROM photos WHERE user LIKE '$sterms' AND cat=$thecat $exclude_cat $sortcode";
                        $queryv = ppmysql_query($query,$link);
                    }
                    else {
                        $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating,allowprint
                            FROM photos WHERE user LIKE '$sterms' $exclude_cat $sortcode";
                        $queryv = ppmysql_query($query,$link);
                    }
                }
            }

            $rowcnt = mysql_num_rows($queryv);

            if ($rowcnt == "0") {
                if ( $catthumbs == "yes" ) {
                    if ($ugview{$thecat} != 1 ) {
                        $noresults = "<center><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['nophotos']}<br /><br /></font></center>";
                        }
                        else {
                            $noresults = "<center><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['nopermcat']}<br /><br /></font></center>";
                        }
                }
                else {
                    $noresults = "<center><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['nocatimg']}<br /><br /></font></center>";
                }
            }
            else {
                $noresults="";
            }

            pagesystem($rowcnt);

            $count=0; $cntresults=0;
            $numcols = $Globals['thumbcols']+1;
            $pwidth = intval(100/($numcols-1));

            while ( $row = mysql_fetch_row($queryv) ) {
                if ( $cat == 999 )
                    list( $favid, $id, $tuser, $tuserid, $pcat, $date, $title, $desc, $keywords, $bigimage, $width, $height, $filesize, $views, $medwidth, $medheight, $medsize, $approved, $imgrating, $allowprint ) = $row;
                else
                    list( $id, $tuser, $tuserid, $pcat, $date, $title, $desc, $keywords, $bigimage, $width, $height, $filesize, $views, $medwidth, $medheight, $medsize, $approved, $imgrating, $allowprint ) = $row;

                $is_private = "no";
                if ( $pcat != $thecat ) $is_private = is_image_private( $pcat );

                if ( $is_private == "no" ) {
                    if ( $width == 0 && $height == 0 )
                        $sizecode = "n/a";
                    else
                        $sizecode = "$width x $height";

                    $cntresults++;
                    $filesize = $filesize/1024;
                    $filesize = sprintf("%1.1f", $filesize);
                    $filesize = $filesize."k";

                    if ($cntresults >= $startnumb) {
                        if ($cntresults < ($startnumb+$perpage)) {
                            // Print out the thumbnail photo along with the title, username, etc
                            // PERL->PHP (had to +1 for some reason)

                            $querya = "SELECT username FROM comments WHERE photo=$id ORDER BY date DESC";
                            $queryz = ppmysql_query($querya,$link);
                            list( $lastposter ) = mysql_fetch_row($queryz);
                            $comcount = mysql_num_rows($queryz);
                            ppmysql_free_result($queryz);

                            $count++;
                            if ($count == $numcols) {
                                $output .= "</tr><tr>";
                                $count = 1;
                            }

                            $theext = get_ext($bigimage);
                            $filename = $bigimage;
                            $filename = str_replace( $theext, "", $filename);

                            $ppdate = formatppdate( $date );

                            if ($medsize > 0) {
                                $medsize = $medsize/1024;
                                $medsize = sprintf("%1.1f", $medsize);
                                $medsize = $medsize."k";
                                $ilink = "{$Globals['datadir']}/$pcat/$tuserid$filename-med$theext";
                                $biglink = "{$Globals['datadir']}/$pcat/$tuserid$filename$theext";
                                $fsizedisp = "<a href=\"{$Globals['maindir']}/showphoto.php?photo=$id&amp;papass=$papass&amp;sort=$sortparam&amp;thecat=$thecat\">$medsize</a>, <a
                                    href=\"{$Globals['maindir']}/showphoto.php?photo=$id&amp;size=big&amp;papass=$papass&amp;sort=$sortparam&amp;thecat=$thecat\">$filesize</a>";
                            }
                            else {
                                $ilink = "{$Globals['datadir']}/$pcat/$tuserid$filename$theext";
                                $fsizedisp = "<a href=\"{$Globals['maindir']}/showphoto.php?photo=$id&amp;papass=$papass&amp;sort=$sortparam&amp;thecat=$thecat\">$filesize</a>";
                            }

                            // Find out if a photo has comments

                            if ($comcount != "0") {
                                $comline = "<font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\"><a
                                    href=\"{$Globals['maindir']}/showphoto.php?photo=$id&amp;papass=$papass&amp;sort=$sortparam&amp;thecat=$thecat\">$comcount comments</a></font>";
                            }
                            else {
                                $comline = "<font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\"
                                    face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['nocomments']}</font>";
                            }

                            // get the rating
                            if ($imgrating && $Globals['allowrate'] == "yes" ) {
                                for ( $x = 1; $x <= $imgrating; $x++ ) {
                                    if ( $x == 1 ) $rating = "<img src=\"{$Globals['idir']}/star.gif\" alt=\"$imgrating {$Globals['pp_lang']['stars']}\" />";
                                    else $rating .= "<img src=\"{$Globals['idir']}/star.gif\" alt=\"$imgrating {$Globals['pp_lang']['stars']}\" />";
                                }
                            }
                            else {
                                $rating = $Globals['pp_lang']['none'];
                            }

                            $thumbrc = get_imagethumb( $bigimage, $pcat, $tuserid, $approved );
                            $profilelink = get_profilelink( $tuserid );

                            $output .= "<td bgcolor=\"{$Globals['maincolor']}\" valign=\"top\" align=\"left\" width=\"$pwidth%\">
                                <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"center\" height=\"125\">
                                <a href=\"{$Globals['maindir']}/showphoto.php?photo=$id&amp;papass=$papass&amp;sort=$sortparam&amp;thecat=$thecat\">$thumbrc</a></td></tr></table>
                                <table cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"{$Globals['detailbgcolor']}\" width=\"90%\" align=\"center\"><tr><td>
                                <table cellpadding=\"2\" cellspacing=\"1\" width=\"100%\"><tr>
                                <td colspan=\"2\" bgcolor=\"{$Globals['detailbgcolor']}\">
                                <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><!--PhotoPost, Copyright All Enthusiast, Inc.-->
                                <a href=\"{$Globals['maindir']}/showphoto.php?photo=$id&amp;papass=$papass&amp;sort=$sortparam&amp;thecat=$thecat\">$title</a>";
                                
                            if ( ($allowprint == "yes" || $tuserid == $userid) && $Globals['enablecal'] == "yes" ) {
                                $output .= "&nbsp;<img src=\"{$Globals['idir']}/print.gif\" alt=\"{$Globals['pp_lang']['canprint']}\" />";
                            }
                                
                            $output .= "</font></td></tr><tr>
                                <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['user']}:</font></td>
                                <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\"><a href=\"$profilelink\">$tuser</a></font></td>
                                </tr><tr>
                                <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['views']}:</font></td>
                                <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">$views</font></td>
                                </tr><tr>";

                            if ( $Globals['allowrate'] == "yes" ) {
                                $output .= "<td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['rating']}:</font></td>
                                    <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">$rating</font></td>
                                    </tr><tr>";
                            }

                            $output .= "<td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['date']}:</font></td>
                                <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">$ppdate</font></td>
                                </tr><tr>
                                <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['filesize']}:</font></td>
                                <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">$fsizedisp</font></td>
                                </tr><tr>
                                <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['dimensions']}:</font></td>
                                <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">$sizecode</font></td>
                                </tr>";

                            if ( $Globals['allowpost'] == "yes" ) {
                                $output .="<tr><td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['comments']}:</font></td>
                                    <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">$comline</font></td>
                                    </tr>";
                            }

                            $output .= "</table></td></tr></table>";

                            if ( $cat == 999 ) {
                                $output .= "<br /><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr><td align=\"center\" width=\"50%\">
                                    <font size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">
                                    <a href=\"{$Globals['maindir']}/addfav.php?photo=$id&do=del&cat=999\">{$Globals['pp_lang']['delfav']}</a></font></td>";
                                    
                                $output .= "<td align=\"center\" width=\"50%\">";
                                
                                if ( ($allowprint == "yes" || $tuserid == $userid) && $Globals['enablecal'] == "yes" ) {
                                    $output .= "<font size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['printable']}</font>";
                                }
                                else {
                                    $output .= "&nbsp;";
                                }
                                
                                $output .= "</td></tr></table>";
                            }                            
                            
                            $output .= "</td>";
                        }
                    }
                }
            }

            if ( $queryv )
                ppmysql_free_result( $queryv );

            $squares = $Globals['thumbcols']-$count;

            for ($v=1; $v <= $squares; $v++) {
                $output .= "<td bgcolor=\"{$Globals['maincolor']}\" width=\"$pwidth%\">&nbsp;</td>";
            }

            if ( $posternav != "" ) $posternav = "$posternav<p>";

            $output .= "</tr></table></td></tr></table></form>$posternav$noresults";

            if ( ($incat < 990 || $incat > 2999) && empty($si) ) {
                if ( $Globals['memformat'] == "no" ) {
                    if ( $incat == "500" && ($Globals['mostrecent'] == "yes" && $Globals['recentdefault'] == "yes") ) {
                        display_gallery("latest", $ppuser);
                    }
                    elseif ( $Globals['mostrecent'] == "yes" && $Globals['recentdefault'] == "yes" ) {
                        display_gallery("latest", "", $incat);
                    }

                    if ( $Globals['dispopular'] == "yes" && $incat == "500" && $ppuser != "" ) {
                        display_gallery("most_views", $ppuser);
                    }
                    elseif ( $Globals['dispopular'] == "yes" ) {
                        display_gallery("most_views", "", $incat);
                    }

                    if ( $Globals['disrandom'] == "yes" && $incat == "500" && $ppuser != "" ) {
                        display_gallery("random", $ppuser);
                    }
                    elseif ( $Globals['disrandom'] == "yes" ) {
                        display_gallery("random", "", $incat);
                    }
                }
            }

            print "$output{$Globals['cright']}";
            printfooter();

            exit;
        }
        else {
            $query = "SELECT user,userid,SUM(views) AS tviews,COUNT(*) AS pcount,MAX(lastpost) AS maxlast,MAX(date) AS
                maxdate,date,SUM(filesize) AS tfilesize,id FROM photos GROUP BY user $sortcode";
            $queryz = ppmysql_query($query,$link);
            $rowcnt = mysql_num_rows($queryz);

            pagesystem($rowcnt);

            if ($rowcnt == "0") {
                $noresults = "<font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\"><br />{$Globals['pp_lang']['nophotos']}<br /><br /></font>";
            }
            else {
                $noresults="";
            }

            $uout=""; $cc=0;
            $count=0; $cntresults=0;
            $numcols = $Globals['thumbcols']+1;
            //$numcols = 7;
            $pwidth = intval(100/($numcols-1));

            while ( list($theuser, $theuserid, $views, $uphotos, $ulast, $maxdate, $date, $tfilesize, $pid) = mysql_fetch_row($queryz)) {
                $cc++;
                if ($cc >= $startnumb) {
                    if ($cc < ($startnumb+$perpage)) {
                        $query = "select comments.id from comments,photos where photos.id=comments.photo AND photos.userid=$theuserid";
                        $comcountdb = ppmysql_query($query,$link);
                        $comcount = mysql_num_rows($comcountdb);

                        //$lastphotime = $ulast+$soffset;
                        $cclock = formatpptime( $ulast );
                        $ppdate = formatppdate( $ulast );

                        $lpprint = "$ppdate $cclock";
                        $mthumb = "";

                        if ($Globals['membthumb'] == "yes") {
                            $query = "SELECT bigimage,id,cat FROM photos WHERE userid=$theuserid AND approved=1 $exclude_cat ORDER BY date DESC"; // 14745
                            $resulta = ppmysql_query($query,$link);

                            while( list( $bigimage, $phoid, $pcat ) = mysql_fetch_row($resulta) ) {
                                $is_private = is_image_private( $pcat );
                                if ( $is_private == "no" ) break;
                            }
                            ppmysql_free_result( $resulta );

                            if ( $is_private == "yes" ) {
                                $bigimage="";
                                $mthumb = "<img border=\"0\" src=\"{$Globals['idir']}/nothumb.gif\" alt=\"\" />";
                            }

                            if ( !empty($bigimage) ) {
                                $imgthumb = get_imagethumb( $bigimage, $pcat, $theuserid, 1 );
                                $mthumb = "<a href=\"{$Globals['maindir']}/showgallery.php?cat=500&amp;ppuser=$theuserid&amp;thumb=1\">$imgthumb</a>";
                            }
                        }

                        if ( !empty($mthumb) || (empty($mthumb) && $Globals['membthumb'] == "no") ) {
                            $tfilesize = $tfilesize/1024;
                            $filesize=sprintf("%1.1f", $tfilesize);
                            $filesize = $filesize."k";

                            if ( $Globals['memblist'] == "no" ) {
                                $count++;

                                if ($count == $numcols) {
                                    $uout .= "</tr><tr>";
                                    $count = 1;
                                }
                            }

                            list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($ulast);
                            $mon++;
                            $year=1900+$year;

                            $profilelink = get_profilelink( $theuserid );

                            if ( $Globals['memblist'] == "no" ) {
                                $uout .= "<td bgcolor=\"{$Globals['maincolor']}\" valign=\"top\" align=\"center\" width=\"$pwidth%\">
                                    <table cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"{$Globals['detailbgcolor']}\" width=\"90%\"><tr><td>
                                    <table cellpadding=\"2\" cellspacing=\"1\" width=\"100%\"><tr>
                                    <td colspan=\"2\" bgcolor=\"{$Globals['detailbgcolor']}\" align=\"center\" valign=\"middle\" height=\"125\"><font size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\"><!--PhotoPost, Copyright All Enthusiast, Inc.-->$mthumb</font></td>
                                    </tr><tr>
                                    <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['user']}:</font></td>
                                    <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\"><a href=\"$profilelink\">$theuser</a></font></td>
                                    </tr><tr>
                                    <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['photos']}:</font></td>
                                    <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">$uphotos</font></td>
                                    </tr><tr>
                                    <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['space']}:</font></td>
                                    <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">$filesize</font></td>
                                    </tr><tr>
                                    <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['views']}:</font></td>
                                    <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">$views</font></td>
                                    </tr>";

                                    if ( $Globals['allowpost'] == "yes" ) {
                                        $uout .= "<tr><td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">Comments:</font></td>
                                            <td bgcolor=\"{$Globals['detailcolor']}\"><font color=\"{$Globals['detailfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">$comcount</font></td></tr>";
                                    }

                                    $uout .= "</table></td></tr></table></td>";
                            }
                            else {
                                $uout .= "<tr>
                                <td width=\"30%\" bgcolor=\"{$Globals['maincolor']}\">
                                <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><a href=\"{$Globals['maindir']}/showgallery.php?cat=500&ppuser=$theuserid&thumb=1\">$theuser</a></font>
                                </td><td width=\"11%\" bgcolor=\"{$Globals['maincolor']}\" align=\"center\">
                                <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$uphotos</font>
                                </td><td width=\"11%\" bgcolor=\"{$Globals['maincolor']}\" align=\"center\">
                                <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$views</font>
                                </td><td width=\"11%\" bgcolor=\"{$Globals['maincolor']}\" align=\"center\">
                                <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$comcount</font>
                                </td><td width=\"11%\" bgcolor=\"{$Globals['maincolor']}\" align=\"center\">
                                <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$filesize</font>
                                </td><td width=\"30%\" bgcolor=\"{$Globals['maincolor']}\" align=\"center\">
                                <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$mthumb<br />$lpprint</font>
                                </td></tr>\n";
                            }
                        }
                        else {
                            // usually means a person with no approved images, so we dont display them
                            $cc--;
                        }
                    }
                }
            }

            ppmysql_free_result( $queryz );

            if ( $Globals['memblist'] == "no" ) {
                $squares = $Globals['thumbcols']-$count;

                for ($v=1; $v <= $squares; $v++) {
                    $uout .= "<td bgcolor=\"{$Globals['maincolor']}\" width=\"$pwidth%\">&nbsp;</td>";
                }

                $output .= "$uout</tr></table></td></tr></table>";
            }
            else {
                $output .= "<tr align=\"center\"><td bgcolor=\"{$Globals['headcolor']}\">
                <font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['headfontcolor']}\" face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['user']}</b></font>
                </td><td bgcolor=\"{$Globals['headcolor']}\">
                <font color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\"><b>Photos</b></font><!--1019940618-->
                </td><td bgcolor=\"{$Globals['headcolor']}\" nowrap=\"nowrap\">
                <font color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['views']}</b></font>
                </td><td bgcolor=\"{$Globals['headcolor']}\">
                <font color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['comments']}</b></font>
                </td><td bgcolor=\"{$Globals['headcolor']}\">
                <font color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['space']}</b></font>
                </td><td bgcolor=\"{$Globals['headcolor']}\">
                <font color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['lastphoto']}</b></font>
                </td></tr>$uout</table></td></tr></table>";
            }

            print "$output</form>$posternav$noresults<br />{$Globals['cright']}";
            printfooter();

            exit;
        }
    }
}

?>
