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
require "languages/$pplang/showphoto.php";
require "login-inc.php";

// variable setup

if (empty($slideshow)) $slideshow=0;
if (empty($pperpage)) $pperpage="";
if (empty($size)) $size="medium";

authenticate();

if ( isset($Globals['ppboards']) && $adminedit != 1 ) {
    if ( $Globals['ppboards'] == "closed" ) {
        print $Globals['pp_lang']['closed'];
        exit;
    }
}

if ( !empty($photo) ) {
    if ( ($username == "" || $username == "Unregistered") && $Globals['reqregister'] == "yes" ) {
        diewell( $Globals['pp_lang']['noreg'] );
        exit;
    }

    if ($Globals['vbversion'] == "2.0.3" || $Globals['vbversion'] == "2.2.0") {
        $query = "SELECT maxposts FROM user WHERE userid=$userid";
        $pperpagedb = ppmysql_query($query,$db_link);
        list( $pperpage ) = mysql_fetch_row($pperpagedb);
        ppmysql_free_result($pperpagedb);
    }

    if ($pperpage == "-1" || $pperpage == "") {
        $pperpage=$Globals['defaultposts'];
    }

    $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating FROM photos WHERE id=$photo";
    $rows = ppmysql_query($query,$link);
    list( $id, $user, $iuserid, $cat, $date, $title, $desc, $keywords, $bigimage, $width, $height, $filesize, $views, $medwidth, $medheight, $medsize, $approved, $imgrating) = mysql_fetch_row($rows);
    ppmysql_free_result( $rows );

    if ( $id != $photo ) {
        diewell( $Globals['pp_lang']['nophoto'] );
        exit;
    }

    if ( $cat < 3000 ) {
        if ( $ugview{$cat} == 1 ) {
            diewell( $Globals['pp_lang']['noperm'] );
            exit;
        }
    }

    if ( $cat > 2999 ) {
        $query = "SELECT id,albumname,parent,isprivate,password FROM useralbums WHERE id=$cat";
        $resultb = ppmysql_query($query,$link);
        list( $thecatid, $thecatname, $aparent, $isprivate, $password ) = mysql_fetch_row($resultb);

        if ( ($isprivate == "yes" && $userid != $aparent) && $adminedit != 1 ) {
            if ( empty($papass) ) $papass = "";

            if ( $password != $papass ) {
                diewell( $Globals['pp_lang']['noperm'] );
                exit;
            }
        }
    }
    else {
        $query = "SELECT catname FROM categories where id=$cat";
        $resulta = ppmysql_query($query,$link);
        list( $thecatname ) = mysql_fetch_row($resulta);
        ppmysql_free_result($resulta);
    }

    //
    // Next and Previous images for display
    //
    if ( empty($sort) ) $sort = 1;
    $query = "SELECT * FROM sort WHERE sortid=$sort";
    $resultc = ppmysql_query($query,$link);
    list($sortid, $sortname, $sortc) = mysql_fetch_row($resultc);
    ppmysql_free_result( $resultc );
    $sortcode = "$sortc";

    if ( $thecat == 999 ) {
        $query = "SELECT p.id,p.user,p.userid,p.cat,p.date,p.title,p.description,p.keywords,
            p.bigimage,p.width,p.height,p.filesize,p.views,p.medwidth,p.medheight,p.medsize,p.approved,p.rating,f.userid
            FROM favorites f, photos p
            WHERE f.userid=$userid AND f.photo=p.id $sortcode";
    }
    elseif ( $cat == 500 ) {
        $query = "SELECT id FROM photos WHERE cat=500 AND userid=$iuserid $sortcode";
    }
    else
        $query = "SELECT id FROM photos WHERE cat=$cat $sortcode";

    $rows = ppmysql_query($query,$link);
    $ref=0; $first_image=0; $last_image=0; $ids = array(0); $curr=0;

    while ( $resultp = mysql_fetch_array($rows) ) {
        $ref++;
        $ids[$ref]=$resultp[0];
        if ( $ids[$ref] == $photo ) {
            $curr = $ref;
        }
    }
    ppmysql_free_result($rows);

    $previous_image = 0;
    $next_image = 0;

    if ( $curr > 1 ) {
         $previous_image = $ids[$curr-1];
    }
    if ( $curr != $ref ) {
        $next_image = $ids[$curr+1];
    }

    if ( $previous_image == 0 ) $previous_image=$ids[$ref];
    if ( $next_image == 0 ) $next_image=$ids[1];

    $botbuster="";
    if ( $Globals['botbuster'] == "yes" )
        $botbuster = "<a href=\"{$Globals['domain']}/".mt_srand ((double) microtime() * 1000000).mt_rand(10,99)."images".mt_rand(1000,9999)."/".mt_rand(1000000,9999999).".jpg\"></a>";

    $prevlink = "<font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><a href=\"{$Globals['maindir']}/showphoto.php?photo=$previous_image&amp;papass=$papass&amp;sort=$sort&amp;size=$size&amp;thecat=$thecat\"><img border=\"0\" src=\"{$Globals['idir']}/previmg.gif\" alt=\"{$Globals['pp_lang']['perv']}\" /></a></font>$botbuster";
    $nextlink = "<font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><a href=\"{$Globals['maindir']}/showphoto.php?photo=$next_image&amp;papass=$papass&amp;sort=$sort&amp;size=$size&amp;thecat=$thecat\"><img border=\"0\" src=\"{$Globals['idir']}/nextimg.gif\" alt=\"{$Globals['pp_lang']['next']}\" /></a></font>";

    // End to get Next and Previous images for display

    if ( $slideshow == 1 ) {
        if ( empty($slidedelay) ) $slidedelay = 4;

        $slideurl = "{$Globals['maindir']}/showphoto.php?photo=$next_image&amp;slideshow=1&amp;papass=$papass&amp;sort=$sort&amp;thecat=$thecat&amp;slidedelay=$slidedelay";
        $slidestop = "{$Globals['maindir']}/showphoto.php?photo=$photo&amp;papass=$papass&amp;sort=$sort&amp;thecat=$thecat";
        $slidecode = "<font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><a href=\"$slidestop\"><img border=\"0\" src=\"{$Globals['idir']}/stopshow.gif\" alt=\"{$Globals['pp_lang']['stop']}\" /></a></font>";
        $prevlink=""; $nextlink="";
    }
    else {
        $slidecode="";
        if ( $next_image != 0 ) {
            $slideurl = "{$Globals['maindir']}/slideshow.php?photo=$next_image&amp;sort=$sort&amp;thecat=$thecat";
            $slidecode = "<a href=\"$slideurl\"><img border=\"0\" src=\"{$Globals['idir']}/slideshow.gif\" alt=\"{$Globals['pp_lang']['start']}\" /></a>";
        }
    }

    if ( $slideshow != 1 ) {
        $exifinfo = "";
        if ( $Globals['showexif'] == "yes" ) {
            $query = "SELECT exifinfo FROM exif WHERE photoid=$photo";
            $row = ppmysql_query($query,$link);
            if ( $row ) {
                list( $exifinfo ) = mysql_fetch_row($row);
            }
            ppmysql_free_result( $row );
        }

        // for childsub, we need to set these globals
        $ppuser = $iuserid;
        $tcat = $user;
        childsub($cat);
        $childnav = "<font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['catfontsize']}\"><a href=\"{$Globals['maindir']}/index.php\">{$Globals['pp_lang']['home']}</a> $childnav</font>";
    }
    else
        $childnav="";

    $uploadquery = "?cat=$cat";

    if ( $width == 0 && $height == 0 )
        $sizecode = "n/a";
    else
        $sizecode = "$width x $height";

    if ( $slideshow != 1 ) topmenu();

    if ( !empty($title) )
        printheader( $cat, " $title", $slideshow );
    else
        printheader( $cat, " $bigimage", $slideshow );

    $output = "<br /><table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td>
        <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>
        <table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\" align=\"center\">
        <tr><td>
        <table cellpadding=\"0\" cellspacing=\"1\" border=\"0\"  width=\"100%\" bgcolor=\"{$Globals['headcolor']}\">
        <tr><td>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"  width=\"98%\" align=\"center\"><tr>
        <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\">
        <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\">$childnav</font></td>
        <td bgcolor=\"{$Globals['headcolor']}\" valign=\"middle\" align=\"right\">
        <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\">$prevlink&nbsp;$slidecode&nbsp;$nextlink</font>
        </td></tr></table></td></tr><!--PhotoPost, Copyright All Enthusiast,Inc.-->";

    $count=0;
    $theext = substr($bigimage,strlen($bigimage) - 4,4);
    $filename = $bigimage;
    $filename = str_replace( $theext, "", $filename);
    $altlink="<br />";

    $profilelink = get_profilelink( $iuserid );

    if ( $imgrating && $Globals['allowrate'] == "yes" ) {
        for ( $x = 1; $x <= $imgrating; $x++ ) {
            if ( $x == 1 ) $rating = "<img src=\"{$Globals['idir']}/star.gif\" alt=\"$imgrating {$Globals['pp_lang']['stars']}\" />";
            else $rating .= "<img src=\"{$Globals['idir']}/star.gif\" alt=\"$imgrating {$Globals['pp_lang']['stars']}\" />";
        }
    }
    else {
        $rating = $Globals['pp_lang']['none'];
    }

    if ($approved == "1") {
        $filesize = $filesize/1024;
        $filesize = sprintf("%1.1f", $filesize);
        $filesize = number_format($filesize)."kb";
        $dismed = 0;

        if ($medsize > 0) {
            $medsize = $medsize/1024;
            $medsize = sprintf("%1.1f", $medsize);
            $medsize = number_format($medsize)."kb";

            $filesize = "<a href=\"{$Globals['maindir']}/showphoto.php?photo=$id&amp;papass=$papass&amp;sort=$sort&amp;thecat=$thecat\">$medsize</a>, <a
                href=\"{$Globals['maindir']}/showphoto.php?photo=$id&amp;size=big&amp;papass=$papass&amp;sort=$sort&amp;thecat=$thecat\">$filesize</a>";

            if ($size != "big") {
                $dispmed = 1;
                $altlink = "<center><font size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['larger']}</font></center><br />";
            }
            else {
                $altlink = "<center><font size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['smaller']}</font></center><br />";
            }
        }

        if ($Globals['bigsave'] == "yes") {
            if ( $dispmed == 1 ) {
                if ( $Globals['onthefly'] == 1 ) {
                    $imgdisp = "<a href=\"{$Globals['maindir']}/showphoto.php?photo=$photo&amp;size=big&amp;papass=$papass&amp;sort=$sort&amp;thecat=$thecat\"><img
                        width=\"$medwidth\" height=\"$medheight\" src=\"{$Globals['maindir']}/watermark.php?file=$cat/$iuserid$filename-med$theext\" border=\"0\" alt=\"\" /></a>";
                }
                else {
                    $imgdisp = "<a href=\"{$Globals['maindir']}/showphoto.php?photo=$photo&amp;size=big&amp;papass=$papass&amp;sort=$sort&amp;thecat=$thecat\"><img
                        width=\"$medwidth\" height=\"$medheight\" src=\"{$Globals['datadir']}/$cat/$iuserid$filename-med$theext\" border=\"0\" alt=\"\" /></a>";
                }
            }
            else {
                if ( is_multimedia($bigimage) == 1 ) {
                     $mmthumb = "{$Globals['datadir']}/$cat/$iuserid$filename-thumb.jpg";
                     $dirthumb = "{$Globals['datafull']}/$cat/$iuserid$filename-thumb.jpg";

                     if ( !file_exists($dirthumb) ) $mmthumb = "{$Globals['idir']}/video.jpg";

                     $imgdisp = "<a href=\"{$Globals['datadir']}/$cat/$iuserid$filename$theext\"><img src=\"$mmthumb\" border=\"0\" alt=\"\" /></a>
                        <br /><font size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['video']}</font>";
                }
                else {
                    if ( $filesize != "" ) {
                        if ( $Globals['onthefly'] == 1 ) {
                            $imgdisp = "<a href=\"{$Globals['maindir']}/showphoto.php?photo=$photo&amp;papass=$papass&amp;sort=$sort&amp;thecat=$thecat\">
                                <img width=\"$width\" height=\"$height\" src=\"{$Globals['maindir']}/watermark.php?file=$cat/$iuserid$filename$theext\" border=\"0\" alt=\"$filename\" /></a>";
                        }
                        else {
                            $imgdisp = "<a href=\"{$Globals['maindir']}/showphoto.php?photo=$photo&amp;papass=$papass&amp;sort=$sort&amp;thecat=$thecat\">
                                <img width=\"$width\" height=\"$height\" src=\"{$Globals['datadir']}/$cat/$iuserid$filename$theext\" border=\"0\" alt=\"$filename\" /></a>";
                        }
                    }
                    else {
                        $imgdisp = "<img src=\"{$Globals['datadir']}/$cat/$iuserid$filename-thumb$theext\" border=\"0\" alt=\"\" />";
                    }
                }
            }
        }
        else {
            $imgdisp = "<img src=\"{$Globals['datadir']}/$cat/$iuserid$filename-thumb$theext\" border=\"0\" alt=\"\" />";
        }
    }
    else {
        $imgdisp = "<img width=\"100\" height=\"75\" src=\"{$Globals['idir']}/ipending.gif\" border=\"0\" alt=\"\" />";
    }

    $output .= "<tr><td bgcolor=\"{$Globals['maincolor']}\" valign=\"top\" align=\"center\"><br />$imgdisp<br />
        <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\"><b>$title</b></font><br />";

    $admindisplay=""; $adminopts="";

    if ( $slideshow != 1 ) {
        if ( $adminedit == 1 || ($userid == $iuserid && $Globals['userdel'] == "yes") ) {
            $selected = $cat;
            catmoveopt(0);
            $adminopts = "<tr align=\"center\" valign=\"top\"><td width=\"50%\" align=\"right\"><form method=\"post\" action=\"{$Globals['maindir']}/adm-photo.php\">
                <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">
                {$Globals['pp_lang']['move']}: <select name=\"catmove\" style=\"font-size: 9pt; background: FFFFFF;\"><option
                selected=\"selected\"></option>$catoptions</select></font></td><td width=\"25%\" align=\"center\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">&nbsp;&nbsp;<input
                type=\"checkbox\" name=\"pdelete\" value=\"yes\" /> {$Globals['pp_lang']['delete']}&nbsp;&nbsp;<input type=\"hidden\" name=\"ppaction\" value=\"movedel\"><input type=\"hidden\" name=\"pid\" value=\"$id\">
                <input type=\"hidden\" name=\"origcat\" value=\"$cat\" /></font></td><td align=\"left\" width=\"25%\">
                <input type=\"submit\" value=\"{$Globals['pp_lang']['submit']}\" style=\"font-size: 8pt;\" /></form></td></tr>";
        }

        if ( $usercomment == 1 && $Globals['allowrate'] == "yes" && $userid != $iuserid ) {
            $ratedisplay = "<tr align=\"center\" valign=\"top\"><td colspan=\"3\">
                <form name=\"theform\" method=\"post\" action=\"{$Globals['maindir']}/comments.php\">
                <select name=\"rating\" onchange=\"submit();\">
                <option selected=\"selected\">{$Globals['pp_lang']['ratethis']}</option>
                <option value=\"5\">5 - {$Globals['pp_lang']['excellent']}</option>
                <option value=\"4\">4 - {$Globals['pp_lang']['great']}</option>
                <option value=\"3\">3 - {$Globals['pp_lang']['good']}</option>
                <option value=\"2\">2 - {$Globals['pp_lang']['fair']}</option>
                <option value=\"1\">1 - {$Globals['pp_lang']['poor']}</option>
                </select>
                <input type=\"hidden\" name=\"cat\" value=\"$cat\" />
                <input type=\"hidden\" name=\"password\" value=\"$password\" />
                <input type=\"hidden\" name=\"puserid\" value=\"$userid\" />
                <input type=\"hidden\" name=\"photo\" value=\"$photo\" />
                <input type=\"hidden\" name=\"message\" value=\" \" />
                <input type=\"hidden\" name=\"post\" value=\"new\" />
                </form></td></tr>";
        }

        if ( !empty($adminopts) || !empty($ratedisplay) ) {
            $admindisplay = "<br /><table width=\"100%\">$adminopts$ratedisplay</table>";
        }

        $ppdate = formatppdate( $date );
        $desc = convert_markups( $desc );
        $desc = ConvertReturns( $desc );

        $output .= "$altlink
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"96%\">
            <tr><td bgcolor=\"{$Globals['detailbgcolor']}\">
            <table cellpadding=\"0\" cellspacing=\"1\" border=\"0\" width=\"100%\">";

        if ( $userid != "" ) {
            $query = "SELECT id FROM favorites WHERE photo=$id AND userid=$userid";
            $resultf = ppmysql_query($query, $link);
            $isfav = mysql_num_rows($resultf);

            if ( $isfav == 0 )
                $pmenu .= "<a href=\"{$Globals['maindir']}/addfav.php?photo=$id&do=add\">{$Globals['pp_lang']['addfav']}</a> | ";
            else
                $pmenu .= "<a href=\"{$Globals['maindir']}/addfav.php?photo=$id&do=del\">{$Globals['pp_lang']['delfav']}</a> | ";
        }

        if ( $usercomment == 1 && $Globals['allowpost'] == "yes" ) {
            $pmenu .= "<a href=\"{$Globals['maindir']}/comments.php?photo=$id\">{$Globals['pp_lang']['post']}</a>";
        }

        if ($userid != "") {
            if ( !empty($pmenu) )
                $pmenu .= " | ";

            $pmenu .= "<a href=\"{$Globals['maindir']}/reportphoto.php?report=$id\">{$Globals['pp_lang']['report']}</a>";
        }

        if ( $Globals['enablecard'] == "yes" && $userid != "" ) {
            if ( !empty($pmenu) )
                $pmenu .= " | ";

            $pmenu .= "<a href=\"{$Globals['maindir']}/ecard.php?ecard=$id\">{$Globals['pp_lang']['ecard']}</a>";
        }

        if ($Globals['usenotify'] == "yes" && $userid > 0) {
            $query = "SELECT id FROM notify WHERE userid=$userid AND photo=$photo LIMIT 1";
            $results = ppmysql_query($query,$link);
            list( $notifyid ) = mysql_fetch_row($results);
            ppmysql_free_result($results);

            if ( !empty($pmenu) )
                $pmenu .= " | ";

            if ($notifyid != "") {
                $pmenu .= "<a href=\"{$Globals['maindir']}/comments.php?notify=off&notifyid=$notifyid&photo=$photo\">{$Globals['pp_lang']['disable']}</a>";
            }
            else {
                $pmenu .= "<a href=\"{$Globals['maindir']}/comments.php?notify=on&photo=$photo\">{$Globals['pp_lang']['enable']}</a>";
            }
        }

        if ( $adminedit == 1 || ($userid == $iuserid && $ueditpho == 1) ) {
            if ( !empty($pmenu) )
                $pmenu .= " | ";

            $pmenu .= "<a href=\"{$Globals['maindir']}/editphoto.php?phoedit=$id\">{$Globals['pp_lang']['edit']}</a>";
        }

        if ( $pmenu != "" ) {
            $output .= "<tr><td bgcolor=\"#E6E6E6\"><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr>
                <td bgcolor=\"{$Globals['detailbgcolor']}\" align=\"right\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">
                $pmenu</font></td></tr></table></td></tr>";
        }

        // find similiar posts
        if ( empty($keywords) ) $keywords = $title;

        $keylinks = "";
        $keys = explode( " ", $keywords );
        $keys = array_unique ($keys );

        foreach($keys as $eachkey) {
            if ( !empty($eachkey) && $eachkey != "the" && $eachkey != "a" && $eachkey != "but" && $eachkey != "are" && $eachkey != "and" )
                $keylinks .= "<a href=\"{$Globals['maindir']}/showgallery.php?cat=500&amp;stype=1&amp;thumb=1&amp;si=$eachkey\">$eachkey</a> ";
        }

        $output .= "<tr><td><div align=\"left\"><table width=\"100%\" cellpadding=\"2\" cellspacing=\"1\" align=\"left\"><tr>
            <td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['poster']}:</font></td>
            <td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">
            <a href=\"$profilelink\"><b>$user</b></a>
            <font size=\"{$Globals['fontsmall']}\">(<a href=\"{$Globals['maindir']}/showgallery.php?thumb=1&amp;stype=2&amp;si=$user&amp;cat=500&amp;sort=1&amp;ppuser=$iuserid\">{$Globals['pp_lang']['seeall']}</a>)</font></font></td>
            </tr><tr>
            <td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['views']}:</font></td>
            <td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">$views</font></td>
            </tr><tr>";

        if ( $Globals['allowrate'] == "yes" ) {
            $output .= "<td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['rating']}:</font></td>
                <td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">$rating</font></td>
                </tr><tr>";
        }

        $output .= "<td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['date']}:</font></td>
            <td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">$ppdate</font></td>
            </tr><tr>
            <td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['filesize']}:</font></td>
            <td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">$filesize</font></td>
            </tr><tr>
            <td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['dims']}:</font></td>
            <td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">$sizecode</font></td>
            </tr><tr>
            <td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['keywords']}:</font></td>
            <td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">$keylinks</font></td>
            </tr>
            <tr><td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['desc']}:</font></td>
            <td bgcolor=\"{$Globals['detailcolor']}\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">$desc</font></td>
            </tr></table></div></td>";

        if ( $exifinfo != "" ) {
            $restexif = unserialize(stripslashes($exifinfo));

            $output .= "</tr></table><table width=\"100%\" cellpadding=\"2\" cellspacing=\"1\"><tr>
                <td bgcolor=\"{$Globals['detailcolor']}\" colspan=\"4\" align=\"center\">
                <font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">
                <b>{$Globals['pp_lang']['exif']}</b></font></td></tr>\n";

            $col = 1;
            while(list($k,$v)=each($restexif)) {
                if ( !empty($v) ) {
                    if ( is_numeric($v) ) {
                        if ( strlen($v) == 10 ) $v = formatppdate( $v );
                        else $v = number_format( $v );
                    }

                    if ( $k == "IsColor" ) {
                        if ( $v == 1 ) $v = "Yes";
                        else $v = "No";
                    }

                    if ( $col == 1 )
                        $output .= "<tr>";

                    $output .= "<td bgcolor=\"{$Globals['detailcolor']}\" width=\"25%\" align=\"left\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">$k:</font></td>
                        <td bgcolor=\"{$Globals['detailcolor']}\" width=\"25%\" align=\"left\"><font size=\"{$Globals['fontsmall']}\" color=\"{$Globals['detailfontcolor']}\" face=\"{$Globals['mainfonts']}\">$v</font></td>\n";

                    if ( $col == 1 )
                        $col = 2;
                    else {
                        $col = 1;
                        $output .= "</tr>";
                    }
                }
            }
            if ( $col == 2 ) $output .= "<td bgcolor=\"{$Globals['detailcolor']}\" width=\"25%\">&nbsp;</td><td bgcolor=\"{$Globals['detailcolor']}\" width=\"25%\">&nbsp;</td>";
        }

        $output .= "</tr></table>";
    }
    else {
        $output .= "<br />";
    }

    if ( $slideshow == 1 ){ 
        $output .= "</td></tr></table></td></tr></table><br />"; 
    } else { 
        $output .= "</td></tr></table>$admindisplay</td></tr></table></td></tr></table>"; 
    } 
    
    if ( $slideshow == 0 && $Globals['allowpost'] == "yes" ) {
        $query = "SELECT id FROM comments WHERE photo=$photo";
        $results = ppmysql_query($query,$link);
        $comcount = mysql_num_rows($results);

        if ( $comcount == 0 ) {
            $compages = 0;
        }
        else {
            if ($pperpage > 0) {
                $compages=($comcount/$pperpage);
            }
            else {
                $pperpage = $Globals['defaultposts'];
                $compages = ($comcount/$pperpage);
            }
        }

        if (intval($compages) < $compages) {
            $compages=intval($compages)+1;
        }
        else {
            $compages=intval($compages);
        }

        if ( isset($cpage) ) {
            $cstartnumb=($cpage*$pperpage)-$pperpage+1;
        }
        else {
            $cpage=1;
            $cstartnumb=1;
        }

        $cc=0; $ckcolor=0; $posts=""; $comq = "<br />";

        $query = "SELECT id,username,userid,date,rating,comment FROM comments WHERE photo=$photo ORDER BY date ASC";
        $rows = ppmysql_query($query,$link);

        while ( list( $id, $user, $cuserid, $date, $rating, $commenttext ) = mysql_fetch_row($rows) ) {
            if ($rating > 0 && $Globals['allowrate'] == "yes" ) {
                $ratingdisp = "{$Globals['pp_lang']['rating']}: <b>$rating/5</b>&nbsp;";
            }
            else {
                $ratingdisp="";
            }
            $cc++;

            if ($cc >= $cstartnumb) {
                if ($cc < ($cstartnumb+$pperpage)) {
                    $profilelink = get_profilelink( $cuserid );

                    $cclock = formatpptime( $date );
                    $ppdate = formatppdate( $date );

                    $query = "SELECT id FROM photos WHERE userid=$cuserid LIMIT 1";
                    $results = ppmysql_query($query,$link);
                    list( $phoid ) = mysql_fetch_row($results);
                    ppmysql_free_result($results);

                    $cuser=$Globals['pp_lang']['unreg'];
                    $clocation="";
                    $ctitle="";
                    $cposts="";
                    $regdate="";
                    $ugallery="";
                    $isonline="";
                    $hpage="";

                    if ($Globals['vbversion'] == "2.0.3" || $Globals['vbversion'] == "2.2.0") {
                        if ($cuserid != 0) {
                            $query = "SELECT username,homepage,usertitle,posts,joindate FROM user WHERE userid=$cuserid LIMIT 1";
                            $results = ppmysql_query($query,$db_link);
                            list( $cuser, $chomepage, $ctitle, $cposts, $regdate ) = mysql_fetch_row($results);
                            ppmysql_free_result( $results );

                            list($rsec,$rmin,$rhour,$rmday,$rmon,$ryear,$rwday,$ryday,$risdst) = localtime($regdate);
                            $ryear=$ryear+1900;
                            $rmon++;
                            $regdate="$rmon/$ryear";

                            $query = "SELECT field2 FROM userfield WHERE userid=$cuserid LIMIT 1";
                            $results = mysql_query($query, $db_link);

                            if ( $results ) {
                                $ufields = mysql_fetch_array($results);
                                $clocation = $ufields['field2'];
                                ppmysql_free_result($results);
                            }

                            $query = "SELECT host FROM session WHERE userid=$cuserid LIMIT 1";
                            $results = ppmysql_query($query,$db_link);
                            list( $conline ) = mysql_fetch_row($results);
                            ppmysql_free_result($results);
                        }

                        if ($phoid != "") {
                            $ugallery = "<a href=\"{$Globals['maindir']}/showgallery.php?ppuser=$cuserid&amp;cat=500&amp;thumb=1\"><img alt=\"{$Globals['pp_lang']['visitgallery']}\" border=\"0\" src=\"".$Globals['idir']."/gallery4.gif\" /></a>";
                        }

                        if ( $cuserid != 0 ) {
                            if ( $chomepage != "" ) {
                                $hpage = "<a href=\"$chomepage\" target=\"_blank\"><img src=\"{$Globals['vbulletin']}/images/home.gif\" alt=\"Visit ".$cuser."'s homepage!\"
                                    border=\"0\" /></a>";
                            }
                            if ($conline == "") {
                                $isonline = "<img src=\"{$Globals['vbulletin']}/images/off.gif\" border=\"0\" alt=\"{$Globals['pp_lang']['offline']}\" align=\"absmiddle\" /> ";
                            }
                            else {
                                $isonline = "<img src=\"{$Globals['vbulletin']}/images/on.gif\" border=\"0\" alt=\"{$Globals['pp_lang']['online']}\" align=\"absmiddle\" /> ";
                            }
                        }
                    }

                    if ($Globals['vbversion'] == "Internal") {
                        if ($cuserid != 0) {
                            $query = "SELECT username,homepage,posts,joindate,location FROM users WHERE userid=$cuserid LIMIT 1";
                            $results = ppmysql_query($query, $db_link);
                            list( $cuser, $chomepage, $cposts, $regdate, $clocation ) = mysql_fetch_row($results);
                            ppmysql_free_result( $results );

                            list($rsec,$rmin,$rhour,$rmday,$rmon,$ryear,$rwday,$ryday,$risdst) = localtime($regdate);
                            $ryear=$ryear+1900;
                            $regdate="$rmon/$ryear";
                        }
                    }

                    if ($Globals['vbversion'] == "phpBB") {
                        if ($cuserid != 0) {
                            $query = "SELECT username,user_website,user_posts,user_rank,user_regdate FROM users WHERE user_id=$cuserid LIMIT 1";
                            $results = ppmysql_query($query,$db_link);
                            list( $cuser, $chomepage, $cposts, $regdate, $ctitlenum ) = mysql_fetch_row($results);
                            ppmysql_free_result( $results );

                            $query = "SELECT rank_title FROM ranks WHERE rank_id=$ctitlenum LIMIT 1";
                            $results = ppmysql_query($query,$db_link);
                            list( $ctitle ) = mysql_fetch_row($results);
                            ppmysql_free_result( $results );

                            $query = "SELECT sess_id FROM sessions WHERE user_id=$cuserid LIMIT 1";
                            $results = ppmysql_query($query,$db_link);
                            list( $conline ) = mysql_fetch_row($results);
                            ppmysql_free_result( $results );

                            if ( $chomepage != "" ) {
                                $hpage = "<a href=\"$chomepage\" target=\"_blank\"><img src=\"{$Globals['vbulletin']}/images/www_icon.gif\" alt=\"{$Globals['pp_lang']['visithome']}\"
                                    border=\"0\" /></a>)";
                            }
                        }
                        if ($phoid != "") {
                            $ugallery = "<a href=\"{$Globals['maindir']}/showgallery.php?ppuser=$cuserid&amp;cat=500&amp;thumb=1\"><img alt=\"{$Globals['pp_lang']['visitgallery']}\" border=\"0\"
                                src=\"".$Globals['idir']."/gallery/phbb.gif\" /></a>";
                        }
                    }

                    if ($Globals['vbversion'] == "phpBB2") {
                        if ($cuserid != 0) {
                            if ( !empty( $Globals['dprefix'] ) ) {
                                $utable = "{$Globals['dprefix']}users";
                                $rtable = "{$Globals['dprefix']}ranks";
                            }
                            else {
                                $utable = "users";
                                $rtable = "ranks";
                            }
                            $query = "SELECT $utable.username,$utable.user_website,$utable.user_posts,$rtable.rank_title,$utable.user_regdate FROM ";
                            $query .= "$utable LEFT JOIN $rtable ON $utable.user_rank = $rtable.rank_id WHERE $utable.user_id=$cuserid LIMIT 1";
                            $results = ppmysql_query($query, $db_link);

                            if ( $results ) {
                                list( $cuser, $chomepage, $cposts, $ctitle, $regdate ) = mysql_fetch_row($results);
                                ppmysql_free_result( $results );

                                list($rsec,$rmin,$rhour,$rmday,$rmon,$ryear,$rwday,$ryday,$risdst) = localtime($regdate);
                                $rmon++;
                                $ryear=1900+$ryear;
                                $regdate = "$rmon/$rmday/$ryear";
                            }
                        }
                    }

                    if ($Globals['vbversion'] == "w3t" || $Globals['vbversion'] == "w3t6") {
                        if ($cuserid != 0) {
                            if ( !empty( $Globals['dprefix'] ) ) {
                                $utable = "{$Globals['dprefix']}Users";
                            }
                            else {
                                $utable = "w3t_Users";
                            }

                            $query = "SELECT U_Username,U_Homepage,U_Totalposts,U_Title,U_Registered FROM $utable WHERE U_Number=$cuserid LIMIT 1";
                            $results = ppmysql_query($query,$db_link);
                            list( $cuser, $chomepage, $cposts, $ctitle, $tdate ) = mysql_fetch_row($results);
                            ppmysql_free_result( $results );

                            list($rsec,$rmin,$rhour,$rmday,$rmon,$ryear,$rwday,$ryday,$risdst) = localtime($tdate);
                            $rmon++;
                            $ryear=1900+$ryear;
                            $regdate = "$rmon/$rmday/$ryear";
                        }
                    }

                    if ($Globals['vbversion'] == "Internal" || $Globals['vbversion'] == "w3t" || $Globals['vbversion'] == "w3t6" ) {
                        if ($phoid != "") {
                            $ugallery = "<a href=\"{$Globals['maindir']}/showgallery.php?ppuser=$cuserid&amp;cat=500&amp;thumb=1\"><img alt=\"{$Globals['pp_lang']['visitgallery']}\"  border=\"0\" src=\"".$Globals['idir']."/gallery.gif\" /></a>";
                        }
                        if ($chomepage != "" ) {
                            $chomepage = str_replace("http://", "", $chomepage);
                            $hpage = "<a href=\"http://$chomepage\" target=\"_blank\"><img src=\"".$Globals['idir']."/www.gif\" alt=\"{$Globals['pp_lang']['visithome']}\"
                                border=\"0\" /></a>";
                        }
                    }

                    if ($regdate != "") $regdate = "<br /><br />{$Globals['pp_lang']['registered']}: $regdate";
                    if ($cposts != "") $cposts = "<br />{$Globals['pp_lang']['posts']}: $cposts";
                    if ($clocation != "") $clocation = "<br />{$Globals['pp_lang']['location']}: $clocation";
                    if ($ctitle != "") $ctitle = "<br />$ctitle";

                    if ($ckcolor == 1) {
                        $fillcolor = $Globals['altcolor1'];
                        $ckcolor = 0;
                    }
                    else {
                        $fillcolor = $Globals['altcolor2'];
                        $ckcolor = 1;
                    }

                    $commenttext = convert_markups($commenttext);
                    $commenttext = ConvertReturns($commenttext);

                    $posts .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\" align=\"center\">
                        <tr><td>
                        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\" width=\"100%\">
                        <tr><td bgcolor=\"$fillcolor\" width=\"175\" valign=\"top\" nowrap=\"nowrap\">
                        <font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['commentstext']}\" size=\"{$Globals['fontmedium']}\">
                        <b>$cuser</b>
                        <font size=\"{$Globals['fontsmall']}\">$ctitle
                        $regdate$clocation$cposts</font></font></td>

                        <td bgcolor=\"$fillcolor\" width=\"100%\" valign=\"top\">
                        <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td><font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['commentstext']}\"
                        size=\"{$Globals['fontmedium']}\">$commenttext</font></td><td align=\"right\" valign=\"top\">
                        <font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['commentstext']}\" size=\"{$Globals['fontmedium']}\">$ratingdisp</font></td></tr></table>

                        </td></tr><tr>
                        <td bgcolor=\"$fillcolor\" width=\"175\" height=\"16\" nowrap=\"nowrap\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\">$isonline
                        $ppdate <font color=\"{$Globals['commentstext']}\">$cclock</font></font></td>

                        <td bgcolor=\"$fillcolor\" width=\"100%\" valign=\"middle\" height=\"16\">
                        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                        <tr valign=\"bottom\"><td>";

                    if ( $cuserid > 0 ) {
                        if ($Globals['vbversion'] == "2.0.3" || $Globals['vbversion'] == "2.2.0") {
                            $posts .= "<a href=\"$profilelink\" target=\"_blank\">
                            	<img src=\"{$Globals['vbulletin']}/images/profile.gif\" border=\"0\" alt=\"{$Globals['pp_lang']['seeprofile']}\" /></a> <a href=\"$privatelink\">
                                <img src=\"{$Globals['vbulletin']}/images/sendpm.gif\" border=\"0\" alt=\"{$Globals['pp_lang']['sendpm']}\" /></a>  $hpage
                                <a href=\"{$Globals['vbulletin']}/search.php?s=&action=finduser&amp;userid=$cuserid\">
                                <img src=\"{$Globals['vbulletin']}/images/find.gif\" border=\"0\" alt=\"{$Globals['pp_lang']['find']}\" /></a><!--PhotoPost, copyright All, Enthusiast, Inc.-->
                                <a href=\"{$Globals['vbulletin']}/member2.php?s=&action=addlist&amp;userlist=buddy&amp;userid=$cuserid\">
                                <img src=\"{$Globals['vbulletin']}/images/buddy.gif\" border=\"0\" alt=\"{$Globals['pp_lang']['addbuddy']}\" /></a> $ugallery
                                </td>";
                        }

                        if ($Globals['vbversion'] == "Internal" || $Globals['vbversion'] == "w3t" || $Globals['vbversion'] == "w3t6") {
                            $posts .= "<a href=\"$profilelink\" target=\"_blank\"><img src=\"{$Globals['idir']}/profile.gif\"
                                border=\"0\" alt=\"{$Globals['pp_lang']['seeprofile']}\" /></a>
                                $hpage<!--PhotoPost, copyright All, Enthusiast, Inc.-->
                                $ugallery
                                </td>";
                        }

                        if ( $Globals['vbversion'] == "phpBB" || $Globals['vbversion'] == "phpBB2" ) {
                            $posts .= "<a href=\"$profilelink\" target=\"_blank\"><img src=\"{$Globals['idir']}/profile.gif\"
                                border=\"0\" alt=\"{$Globals['pp_lang']['seeprofile']}\" /></a>
                                $hpage<!--PhotoPost, copyright All, Enthusiast, Inc.-->
                                $ugallery
                                </td>";
                        }
                    }

                    $posts .= "<td align=\"right\" nowrap=\"nowrap\">";

                    if ( $adminedit == 1 || ($userid == $cuserid && $ueditposts == 1) ) {
                        $posts .= "<a href=\"comments.php?photo=$photo&amp;cedit=$id\"><img src=\"{$Globals['idir']}/edit.gif\" border=\"0\" alt=\"{$Globals['pp_lang']['editdel']}\" /></a>";
                    }
                    else {
                        $posts .= "&nbsp;";
                    }

                    $posts .= "</td></tr></table></td></tr></table></td></tr></table>";
                }
            }
        }

        if ( $rows )
            ppmysql_free_result( $rows );

        if ( $usercomment == 1 && $Globals['allowpost'] == "yes" ) {
            $comq = "<br /><form name=\"theform\" method=\"post\" action=\"{$Globals['maindir']}/comments.php\">
                    <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"
                    width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>
                    <td>
                    <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\" width=\"100%\">
                    <tr align=\"center\">
                    <td colspan=\"1\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font
                    face=\"{$Globals['mainfonts']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontmedium']}\">
                    <b>{$Globals['pp_lang']['addcomments']}</b></font>
                    </td>
                    <td colspan=\"1\" align=\"right\" bgcolor=\"{$Globals['headcolor']}\"><font
                    face=\"{$Globals['mainfonts']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontmedium']}\">
                    <a href=\"javascript:PopUpHelp('comments.php')\">{$Globals['pp_lang']['help']}</a></font>
                    </td>
                    </tr>";

            $comq .= "<tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">
                    {$Globals['pp_lang']['username']}</font></td><td colspan=\"2\" bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$username
                    </font></td></tr>";

            if ( $Globals['allowrate'] == "yes" && $userid != $iuserid ) {
                $comq .= "<tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['rateover']}</font></td>
                    <td colspan=\"2\" bgcolor=\"{$Globals['maincolor']}\">
                    <select name=\"rating\">
                    <option value=\"0\" selected=\"selected\">{$Globals['pp_lang']['rate']}</option>
                    <option value=\"5\">5 - {$Globals['pp_lang']['excellent']}</option>
                    <option value=\"4\">4 - {$Globals['pp_lang']['great']}</option>
                    <option value=\"3\">3 - {$Globals['pp_lang']['good']}</option>
                    <option value=\"2\">2 - {$Globals['pp_lang']['fair']}</option>
                    <option value=\"1\">1 - {$Globals['pp_lang']['poor']}</option>
                    </select></td></tr>";
            }

            $comq .= "<tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Comments:<br /><br />
                    <font size=\"{$Globals['fontsmall']}\"><a href=\"javascript:PopUpHelp('ubbcode.php')\">{$Globals['pp_lang']['bbcode']}</a><br />
                    <a href=\"javascript:PopUpHelp('smilies.php')\">{$Globals['pp_lang']['smilies']}</a></font></font></td>
                    <td bgcolor=\"{$Globals['maincolor']}\">
                    <textarea name=\"message\" cols=\"50\" rows=\"5\"></textarea></td></tr>";

            $comq .= "<tr><td colspan=\"3\" bgcolor=\"{$Globals['maincolor']}\" align=\"center\">
                    <input type=\"hidden\" name=\"cat\" value=\"$cat\" />
                    <input type=\"hidden\" name=\"password\" value=\"$password\" />
                    <input type=\"hidden\" name=\"puserid\" value=\"$userid\" />
                    <input type=\"hidden\" name=\"photo\" value=\"$photo\" />";

            $comq .= "<input type=\"hidden\" name=\"post\" value=\"new\" /><input type=\"submit\" value=\"{$Globals['pp_lang']['submit']}\" />";
            $comq .= "</td></tr></table></td></tr></table></form>";
        }
    }

    $cheader = "<br /><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr>
        <td bgcolor=\"{$Globals['headcolor']}\" width=\"175\" nowrap=\"nowrap\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" color=\"{$Globals['headfontcolor']}\"><b>{$Globals['pp_lang']['author']}</b></font></td>
        <td bgcolor=\"{$Globals['headcolor']}\" width=\"100%\">
        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
        <tr>
        <td width=\"100%\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" color=\"{$Globals['headfontcolor']}\"><b>{$Globals['pp_lang']['thread']}</b></font></td>
        <td nowrap=\"nowrap\"><a href=\"comments.php?photo=$photo\">$postreply</a>&nbsp;</td>
        </tr>
        </table></td></tr></table></td></tr></table>";

    // begin pages/nav system ##
    $comnav="";

    if ($compages > 1) {
        $comnav .= "<table cellpadding=\"5\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr bgcolor=\"{$Globals['maincolor']}\"><td width=\"40%\"></td>
            <td><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\"><b>{$Globals['pp_lang']['page']}:&nbsp;</b> ";
        $thestart="";

        if ($cpage < 11) {
            $thestart=1;
        }
        if ($cpage > 10) {
            $thestart=$cpage/10;
            $thestart=intval($thestart);
            $thestart=$thestart*10;
        }
        $theend=$thestart+9;

        for ($p=$thestart;$p<=$compages;$p++) {
            if ($p != $thestart) {
                $comnav .= " | ";
            }

            if ($cpage != $p) {
                if ($p == ($theend+1)) {
                    $thispage="$p>";
                }
                else {
                    $thispage="$p";
                }
                $comnav .= "<a href=\"{$Globals['maindir']}/showphoto.php?photo=$photo&amp;cpage=$p&amp;pperpage=$pperpage&amp;thecat=$thecat&amp;papass=$papass#poststart\">$thispage</a>";
            }
            if ($p >$theend) {
                break;
            }
            if ($cpage == $p) {
                $comnav .= "<b>$p</b>";
            }
        }
        if ($cpage < $compages) {
            $nextpage=$cpage+1;
            $more = "<a href=\"{$Globals['maindir']}/showphoto.php?photo=$photo&amp;cpage=$nextpage&amp;sort=$sortparam&amp;perpage=$pperpage&amp;thecat=$thecat&amp;papass=$papass\"><img
                height=\"16\" width=\"63\" alt=\"{$Globals['pp_lang']['more']}\"
                border=\"0\" src=\"{$Globals['idir']}/more.gif\" /></a>";
        }
        else {
            $more = "&nbsp";
        }

        $comnav .= "</td><td width=\"20%\" align=\"center\">$more</td></tr></table>";
    }
    // end pages/nav ###

    if ( $Globals['ipcache'] != 0 ) {
        $ipaddress = findenv("REMOTE_ADDR");
        $query = "SELECT userid,date,photo FROM ipcache WHERE ipaddr='$ipaddress' AND type='view' AND photo='$photo' LIMIT 1";
        $result = ppmysql_query($query, $link);
        $numfound = mysql_num_rows($result);

        if ( $numfound > 0 ) {
            list( $tuserid, $lastdate, $photo ) = mysql_fetch_row($result);

            list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
            $mon = $mon + 1;
            $mytime = mktime($hour,$min,$sec,$mon,$mday,$year);

            $hour = $hour - $Globals['ipcache'];
            $timeout = mktime($hour,$min,$sec,$mon,$mday,$year);

            if ( $lastdate < $timeout ) {
                $query = "UPDATE photos SET views=views+1 WHERE id=$photo";
                $result = ppmysql_query($query,$link);

                if ( $userid > 0 && $Globals['vbversion'] == "Internal" ) {
                    $query = "UPDATE users SET views=views+1 WHERE userid=$userid";
                    $result = ppmysql_query($query,$db_link);
                }

                $query = "DELETE FROM ipcache WHERE date < $timeout";
                $result = ppmysql_query($query,$link);

                $query = "INSERT INTO ipcache (userid,ipaddr,date,type,photo) VALUES ('$tuserid', '$ipaddress', '$mytime', 'view', '$photo')";
                $result = ppmysql_query($query,$link);
            }
        }
        else {
            list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
            $mon = $mon + 1;
            $mytime = mktime($hour,$min,$sec,$mon,$mday,$year);

            $query = "INSERT INTO ipcache (userid,ipaddr,date,type,photo) VALUES ('$tuserid', '$ipaddress', '$mytime', 'view', '$photo')";
            $result = ppmysql_query($query,$link);

            $query = "UPDATE photos SET views=views+1 WHERE id=$photo";
            $result = ppmysql_query($query,$link);

            if ( $userid > 0 && $Globals['vbversion'] == "Internal" ) {
                $query = "UPDATE users SET views=views+1 WHERE userid=$userid";
                $result = ppmysql_query($query,$db_link);
            }
        }
    }
    else {
        $query = "UPDATE photos SET views=views+1 WHERE id=$photo";
        $result = ppmysql_query($query,$link);

        if ( $userid > 0 && $Globals['vbversion'] == "Internal" ) {
            $query = "UPDATE users SET views=views+1 WHERE userid=$userid";
            $result = ppmysql_query($query,$link);
        }
    }

    if ( $posts != ""  ) {
        print "$output$cheader$posts$comnav$comq{$Globals['cright']}";
    }
    else {
        print "$output$comq{$Globals['cright']}";
    }
    printfooter();

} // end individual photo display ###

?>

