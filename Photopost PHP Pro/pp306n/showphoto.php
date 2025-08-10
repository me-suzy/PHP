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

// variable setup

if (empty($slideshow)) $slideshow=0;
if (empty($pperpage)) $pperpage="";
if (empty($size)) $size="";

$output = "";

authenticate();

if ( !empty($photo) ) {
    if ( ($username == "" || $username == "Unregistered") && $Globals{'reqregister'} == "yes" ) {
        dieWell( "You must be a registered user to view images!<p><b>To register click on the REGISTER button in the menu above.</b>");
        exit;
    }

    if ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
        $query = "SELECT maxposts FROM user WHERE userid=$userid";
        $pperpagedb = mysql_query_eval($query,$db_link);
        $row = mysql_fetch_array($pperpagedb);
        $pperpage = $row['maxposts'];
        mysql_free_result($pperpagedb);
    }

    if ($pperpage == "-1") {
        $pperpage=$Globals{'defaultposts'};
    }
    if ($pperpage == "") {
        $pperpage=$Globals{'defaultposts'};
    }

    $query = "SELECT cat,user,userid FROM photos WHERE id=$photo";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_row($resulta);

    if ( !$row ) {
        dieWell( "Photo $photo not found in the database!" );
        exit;
    }
    list( $catid, $tcat, $user ) = $row;

    if ( $catid > 3000 ) {
        $query = "SELECT albumname FROM useralbums where id=$catid";
        $resulta = mysql_query_eval($query,$link);
        $row = mysql_fetch_array($resulta);
        $thecatname = $row['albumname'];
        mysql_free_result($resulta);
    }
    else {
        $query = "SELECT catname FROM categories where id=$catid";
        $resulta = mysql_query_eval($query,$link);
        $row = mysql_fetch_array($resulta);
        $thecatname = $row['catname'];
        mysql_free_result($resulta);
    }

    //
    // Next and Previous images for display
    //
    $query = "SELECT id FROM photos WHERE cat=$catid";
    $rows = mysql_query_eval($query,$link);

    $ref=0; $first_image=0; $last_image=0; $ids = array(0); $curr=0;

    while ( $resultp = mysql_fetch_array($rows, MYSQL_ASSOC) ) {
        $ref++;
        $ids[$ref]=$resultp['id'];
        if ( $ids[$ref] == $photo ) {
            $curr = $ref;
        }
    }
    mysql_free_result($rows);

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
    if ( $Globals{'botbuster'} == "yes" )
        $botbuster = "<a href=\"".$Globals{'domain'}."/".mt_srand ((double) microtime() * 1000000).mt_rand(10,99)."images".mt_rand(1000,9999)."/".mt_rand(1000000,9999999).".jpg\"></a>";

    $prevlink = "<font size=\"2\" face=\"verdana\"><A href=\"".$Globals{'maindir'}."/showphoto.php?photo=$previous_image\"><img border=\"0\" src=\"".$Globals{'idir'}."/previmg.gif\" alt=\"Previous image in category\"></a></font>$botbuster";
    $nextlink = "<font size=\"2\" face=\"verdana\"><A href=\"".$Globals{'maindir'}."/showphoto.php?photo=$next_image\"><img border=\"0\" src=\"".$Globals{'idir'}."/nextimg.gif\" alt=\"Next image in category\"></a></font>";

    // End to get Next and Previous images for display

    if ( $slideshow == 1 ) {
        $slideurl = $Globals{'maindir'}."/showphoto.php?photo=$next_image&slideshow=1";
        $slidestop = $Globals{'maindir'}."/showphoto.php?photo=$photo";
        $slidecode = "<font size=\"2\" face=\"verdana\"><A href=\"$slidestop\"><img border=\"0\" src=\"".$Globals{'idir'}."/stopshow.gif\" alt=\"Stop the slideshow\"></a></font>";

        if ( $slideshow == 1 ) {
            $headslide="<head><script language=\"JavaScript\"><!--
                t=1; function dorefresh() { u=new String(\"$slideurl\");
                ti=setTimeout(\"dorefresh();\",5000); if (t>0) { t-=1; }
                else { clearTimeout(ti); window.location=u.replace(\"#\",\"&t=\"+parseInt(10000*Math.random())+\"#\"); }
                } window.onLoad=dorefresh();
                --></script><noscript><meta http-equiv=\"Refresh\" content=\"5; URL=$slideurl\"></noscript>";

            $prevlink=""; $nextlink="";
        }
    }
    else {
        $headslide="";
        $slidecode="";
        if ( $next_image != 0 ) {
            $slideurl = $Globals{'maindir'}."/showphoto.php?photo=$next_image&slideshow=1";
            $slidecode = "<font size=\"2\" face=\"verdana\"><A href=\"$slideurl\"><img border=\"0\" src=\"".$Globals{'idir'}."/slideshow.gif\" alt=\"Start a slideshow of images\"></a></font>";
        }
    }

    if ( $slideshow != 1 ) {
        childsub($catid);
        $childnav = "<b><font face=\"verdana, arial, helvetica\" size=\"2\"><a href=\"".$Globals{'maindir'}."/index.php\">Home</a> $childnav";
    }
    else
        $childnav="";

    $uploadquery = "?cat=$catid";

    if ( $slideshow != 1 ) topmenu();

    $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating FROM photos WHERE id=$photo";
    $rows = mysql_query_eval($query,$link);
    $result = mysql_fetch_row($rows);
    list( $id, $user, $iuserid, $cat, $date, $title, $desc, $keywords, $bigimage, $width, $height, $filesize, $views, $medwidth, $medheight, $medsize, $approved, $imgrating) = $result;

    if ( !empty($title) )
        $header = str_replace( $Globals{'galleryname'}, $Globals{'galleryname'}." - $title", $header );
    else
        $header = str_replace( $Globals{'galleryname'}, $Globals{'galleryname'}." - $bigimage", $header );

    $output = "$headslide$header
        <Center>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" height=\"40\" width=\"".$Globals{'tablewidth'}."\"><Tr>
        <Td valign=\"center\" width=\"50%\">&nbsp;$childnav</td>
        <td width=\"50%\" align=\"right\" valign=\"center\"><font face=\"verdana, arial\" size=\"2\">$menu&nbsp;</font></td></tr></table>
        <table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\" align=\"center\">
        <tr><td>
        <table cellpadding=\"0\" cellspacing=\"1\" border=\"0\"  width=\"100%\" bgcolor=\"".$Globals{'headcolor'}."\">
        <tr><td>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"  width=\"98%\" align=\"center\"><tr>
        <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\">
        <font size=\"2\" face=\"verdana\"><B>$title - $bigimage</font></td>
        <Td bgcolor=\"".$Globals{'headcolor'}."\" valign=\"bottom\" align=\"right\">
        <font size=\"2\" face=\"verdana\">$prevlink&nbsp;&nbsp;$slidecode&nbsp;&nbsp;$nextlink</font>
        </td></tr></table></td></tr><!-- CyKuH [WTN] -->";

    $count=0;
    $theext = substr($bigimage,strlen($bigimage) - 4,4);
    $filename = $bigimage;
    $filename= str_replace( $theext, "", $filename);
    $dispmed = 0; $altlink="";

    $profilelink = get_profilelink( $iuserid );

    if ($imgrating) {
        for ( $x = 1; $x <= $imgrating; $x++ ) {
            if ( $x == 1 ) $rating = "<img src=\"".$Globals{'idir'}."/star.gif\" alt=\"$imgrating stars\">";
            else $rating .= "<img src=\"".$Globals{'idir'}."/star.gif\" alt=\"$imgrating stars\">";
        }
    }
    else {
        $rating = "None";
    }

    if ($approved == "1") {
        if ($medsize > 0) {
            if ($size != "big") {
                $dispmed=1;
                $altlink = "<center><font size=\"1\" face=\"verdana\">Click on image to view larger image</font></center><Br>";
            }
            else {
                $altlink = "<center><font size=\"1\" face=\"verdana\"><B><a href=\"".$Globals{'maindir'}."/showphoto.php?photo=$photo\">View Smaller Image</a></b></font></center><Br>";
            }
        }

        if ($Globals{'bigsave'} == "yes") {
            if ($dispmed > 0) {
                $medsize=$medsize/1024;
                $medsize=sprintf("%1.1f", $medsize);
                $medsize=$medsize."kb";
                $filesize=$filesize/1024;
                $filesize=sprintf("%1.1f", $filesize);
                $filesize = "<A href=\"".$Globals{'maindir'}."/showphoto.php?photo=$id\">$medsize</a>, <A
                    href=\"".$Globals{'maindir'}."/showphoto.php?photo=$id&size=big\">".$filesize."kb</a>";
                $imgdisp = "<a href=\"".$Globals{'maindir'}."/showphoto.php?photo=$photo&size=big\"><img
                    width=\"$medwidth\" height=\"$medheight\" src=\"".$Globals{'datadir'}."/$cat/$iuserid$filename-med$theext\" border=\"0\"></a>";
            }
            else {
                if ($filesize != "") {
                    $filesize=$filesize/1024;
                    $filesize=sprintf("%1.1f", $filesize);
                    $filesize=$filesize."kb";
                    $imgdisp = "<img width=\"$width\" height=\"$height\"
                        src=\"".$Globals{'datadir'}."/$cat/$iuserid$filename$theext\" border=\"0\">";
                }
                else {
                    $imgdisp = "<img src=\"".$Globals{'datadir'}."/$cat/$iuserid$filename-thumb$theext\" border=\"0\">";
                }
            }
        }
        else {
            $imgdisp = "<img src=\"".$Globals{'datadir'}."/$cat/$iuserid$filename-thumb$theext\" border=\"0\">";
        }
    }
    else {
        $imgdisp = "<img width=\"100\" height=\"75\" src=\"".$Globals{'idir'}."/ipending.gif\" border=\"0\">";
    }

    $output .= "<Tr><Td bgcolor=\"".$Globals{'maincolor'}."\" valign=\"top\" align=\"center\"><br>$imgdisp";

    $admindisplay=""; $adminopts="";

    if ( $slideshow != 1 ) {
        if ( $adminedit == 1 || ($userid == $iuserid && $Globals{'userdel'} == "yes") ) {
            catmoveopt(0);
            $adminopts = "<form method=\"post\" action=\"".$Globals{'maindir'}."/adm-photo.php\"><tr><Td>
                <font size=\"2\" face=\"verdana, arial\" color=\"".$Globals{'maintext'}."\">
                Move photo to: <select name=\"catmove\" style=\"font-size: 9pt; background: FFFFFF;\"><option
                selected></option>$catoptions</select></font></td><td><font size=\"2\" face=\"verdana, arial\" color=\"".$Globals{'maintext'}."\">&nbsp;&nbsp;<input
                type=\"checkbox\" name=\"pdelete\" value=\"yes\"> Delete
                Photo?&nbsp;&nbsp;<input type=\"hidden\" name=\"ppaction\" value=\"movedel\"><input type=\"hidden\" name=\"pid\" value=\"$id\">
                <input type=\"hidden\" name=\"origcat\" value=\"$cat\"></font></td><td>
                <input type=\"submit\" value=\"Submit Change\" style=\"font-size: 8pt;\"></td></tr></form>";
        }

        $ratedisplay = "<Table width=\"75%\"><form name=\"theform\" method=\"post\" action=\"".$Globals{'maindir'}."/comments.php\"><tr><td align=\"center\">
            <font size=\"1\" face=\"verdana, arial\" color=\"".$Globals{'maintext'}."\">
            <select name=\"rating\"><option selected>Rate this photo</option>
            <option value=\"5\">5 - Excellent</option>
            <option value=\"4\">4 - Great</option>
            <option value=\"3\">3 - Good</option>
            <option value=\"2\">2 - Fair</option>
            <option value=\"1\">1 - Poor</option>
            </select>
            <input type=\"hidden\" name=\"cat\" value=\"$cat\">
            <input type=\"hidden\" name=\"password\" value=\"$password\">
            <input type=\"hidden\" name=\"puserid\" value=\"$userid\">
            <input type=\"hidden\" name=\"photo\" value=\"$photo\">
            <input type=\"hidden\" name=\"message\" value=\" \">
            <input type=\"hidden\" name=\"post\" value=\"new\">
            <input type=\"submit\" name=\"submit\" value=\"Rate It!\" style=\"font-size: 8pt;\">
            </font></td></tr></form></table>";

       $admindisplay = "<Table width=\"75%\">$adminopts$ratedisplay</table>";

        list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($date);
        $mon++;
        $year=1900+$year;

        $desc = convert_markups( $desc );
        $desc = ConvertReturns( $desc );

        $output .= "<br>
            $altlink
            <p>
            <Table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tr><td bgcolor=\"".$Globals{'detailbgcolor'}."\">

            <Table cellpadding=\"0\" cellspacing=\"1\" border=\"0\" width=\"100%\"><tr>
            <td bgcolor=\"#E6E6E6\">
            <Table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><Tr>
            <Td bgcolor=\"".$Globals{'detailbgcolor'}."\" align=\"right\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'detailfontcolor'}."\">";

        $pmenu="";
        if ($nopost != 1 || $Globals{'unregcom'} == "yes") {
            $pmenu .= "<A href=\"".$Globals{'maindir'}."/comments.php?photo=$id\">Post a Comment</a>";
        }

        if ($userid != "") {
            if ( !empty($pmenu) )
                $pmenu .= " | ";

            $pmenu .= "<A href=\"".$Globals{'maindir'}."/reportphoto.php?report=$id\">Report Photo</a>";
        }

        if ( $Globals{'enablecard'} == "yes" && $userid != "" ) {
            if ( !empty($pmenu) )
                $pmenu .= " | ";

            $pmenu .= "<a href=\"".$Globals{'maindir'}."/ecard.php?ecard=$id\">Send as e-Card</a>";
        }

        if ($Globals{'usenotify'} == "yes" && $userid > 0) {
            $query = "SELECT id FROM notify WHERE userid=$userid AND photo=$photo LIMIT 1";
            $results = mysql_query_eval($query,$link);
            $row = mysql_fetch_array($results);
            $notifyid = $row['id'];

            if ( !empty($pmenu) )
                $pmenu .= " | ";

            if ($notifyid != "") {
                $pmenu .= "<A href=\"".$Globals{'maindir'}."/comments.php?notify=off&notifyid=$notifyid&photo=$photo\">Disable Email Updates</a>";
            }
            else {
                $pmenu .= "<A href=\"".$Globals{'maindir'}."/comments.php?notify=on&photo=$photo\">Receive Email Updates</a>";
            }
        }

        if ( $adminedit == 1 || ($userid == $iuserid) ) {
            if ( !empty($pmenu) )
                $pmenu .= " | ";

            $pmenu .= "<A href=\"".$Globals{'maindir'}."/editphoto.php?phoedit=$id\">Edit Photo</a>";
        }

        // find similiar posts
        $keylinks = "";
        $keys = explode( " ", $keywords );
        foreach($keys as $eachkey) {
            if ( !empty($eachkey) )
                $keylinks .= "<A href=\"".$Globals{'maindir'}."/showgallery.php?cat=500&stype=1&thumb=1&si=$eachkey\">$eachkey</a> ";
        }

        $output .= "$pmenu</font></td></tr></table></td>
            </tr><Tr><Td><Table width=\"100%\" cellpadding=\"2\" cellspacing=\"1\"><Tr>
            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">User:</font></td>
            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\"><A
            href=\"$profilelink\">$user</a> (<A
            href=\"".$Globals{'maindir'}."/showgallery.php?thumb=1&stype=2&si=$user&cat=500&perpage=12&sort=1&user=$iuserid\">see all of $user's photos</a>)</font></td>
            </tr><Tr>
            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">Views:</font></td>
            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">$views</font></td>
            </tr><Tr>
            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">Rating:</font></td>
            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">$rating</font></td>
            </tr><Tr>
            <td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">Date:</font></td>
            <td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">$mon/$mday/$year</font></td>
            </tr><tr>
            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">Filesize:</font></td>
            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">$filesize</font></td>
            </tr><tr>
            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">Dimensions:</font></td>
            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">$width X $height</font></td>
            </tr><tr>
            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">Keywords:</font></td>
            <td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"2\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">$keylinks</font></td>
            </tr>
            <Tr><Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">Description:</font></td><td
            bgcolor=\"".$Globals{'detailcolor'}."\"><font
            size=\"2\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana\">$desc</font></td></tr></table>
            </tr></table>";
    }

    $output .= "</td></tr></table>$admindisplay</td></tr></table>";

if ( $slideshow != 1 ) {
    $query = "SELECT id FROM comments WHERE photo=$photo";
    $results = mysql_query_eval($query,$link);
    $comcount = mysql_num_rows($results);

    if ( $comcount == 0 ) {
        $compages = 0;
    }
    else {
        if ($pperpage > 0) {
            $compages=($comcount/$pperpage);
        }
        else {
            $pperpage=$Globals{'defaultposts'};
            $compages=($comcount/$pperpage);
        }
    }

    if (intval($compages) < $compages) {
        $compages=intval($compages)+1;
    }
    else {
        $compages=intval($compages);
    }

    if ( IsSet($cpage) ) {
        $cstartnumb=($cpage*$pperpage)-$pperpage+1;
    }
    else {
        $cpage=1;
        $cstartnumb=1;
    }

    $cc=0; $ckcolor=0; $posts="";

    $query = "SELECT id,username,userid,date,rating,comment FROM comments WHERE photo=$photo ORDER BY date ASC";
    $rows = mysql_query_eval($query,$link);

    while ( $comments = mysql_fetch_row($rows) ) {
        list( $id, $user, $cuserid, $date, $rating, $comment ) = $comments;

        $yescomments="yes";
        if ($rating > 0) {
            $ratingdisp = "Rating: <b>$rating/5</b>&nbsp;";
        }
        else {
            $ratingdisp="";
        }
        $cc++;

        if ($cc >= $cstartnumb) {
            if ($cc < ($cstartnumb+$pperpage)) {
                $profilelink = get_profilelink( $cuserid );

                list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($date);
                $mon++;
                $year=1900+$year;
                $cclock = thetime($hour,$min);

                $query = "SELECT id FROM photos WHERE userid=$cuserid LIMIT 1";
                $results = mysql_query_eval($query,$link);
                $uphotos = mysql_fetch_array($results);
                $phoid = $uphotos['id'];

                $cuser="Unregistered";
                $clocation="";
                $cdesc="";
                $ctitle="";
                $cposts="";
                $regdate="";
                $ugallery="";
                $isonline="";
                $hpage="";

                if ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
                    if ($cuserid != 0) {
                        $query = "SELECT username,homepage,usertitle,posts,joindate FROM user WHERE userid=$cuserid LIMIT 1";
                        $results = mysql_query_eval($query,$db_link);
                        $udatac = mysql_fetch_row($results);
                        list( $cuser, $chomepage, $ctitle, $cposts, $regdate ) = $udatac;

                        list($rsec,$rmin,$rhour,$rmday,$rmon,$ryear,$rwday,$ryday,$risdst) = localtime($regdate);
                        $ryear=$ryear+1900;
                        $rmon++;
                        $regdate="$rmon/$ryear";

                        $query = "SELECT field1,field2 FROM userfield WHERE userid=$cuserid LIMIT 1";
                        $results = mysql_query_eval($query,$db_link);
                        if ( $results ) {
                            $ufields = mysql_fetch_row($results);
                            list( $cdesc, $clocation ) = $ufields;
                        }

                        $query = "SELECT host FROM session WHERE userid=$cuserid LIMIT 1";
                        $results = mysql_query_eval($query,$db_link);
                        $usession = mysql_fetch_array($results);
                        $conline = $usession['host'];
                    }
                    if ($phoid != "") {
                        $ugallery = "<a href=\"".$Globals{'maindir'}."/showgallery.php?user=$cuserid&cat=500&thumb=1\"><img alt=\"Visit this user's gallery.\" border=\"0\"
                            src=\"".$Globals{'idir'}."/gallery4.gif\">";
                    }

                    if ( $cuserid != 0 ) {
                        if ( $chomepage != "" ) {
                            $hpage = "<a
                                href=\"$chomepage\" target=\"_blank\"><img src=\"".$Globals{'vbulletin'}."/images/home.gif\" alt=\"Visit ".$cuser."'s homepage!\"
                                border=\"0\"></a>";
                        }
                        if ($conline == "") {
                            $isonline = "<img src=\"".$Globals{'vbulletin'}."/images/off.gif\" border=\"0\" alt=\"$cuser is offline\" align=\"absmiddle\"> ";
                        }
                        else {
                            $isonline = "<img src=\"".$Globals{'vbulletin'}."/images/on.gif\" border=\"0\" alt=\"$cuser is online\" align=\"absmiddle\"> ";
                        }
                    }
                }

                if ($Globals{'vbversion'} == "Internal") {
                    if ($cuserid != 0) {
                        $query = "SELECT username,homepage,posts,joindate,bio,location FROM users WHERE userid=$cuserid LIMIT 1";
                        $results = mysql_query_eval($query,$db_link);
                        $udatac = mysql_fetch_array($results);
                        $cuser = $udatac['username']; $chomepage = $udatac['homepage']; $cposts = $udatac['posts']; $regdate = $udatac['joindate'];
                        $cdesc = $udatac['bio']; $clocation = $udatac['location'];

                        list($rsec,$rmin,$rhour,$rmday,$rmon,$ryear,$rwday,$ryday,$risdst) = localtime($regdate);
                        $ryear=$ryear+1900;
                        $regdate="$rmon/$ryear";
                    }
                }

                if ($Globals{'vbversion'} == "phpBB") {
                    if ($cuserid != 0) {
                        $query = "SELECT username,user_website,user_posts,user_rank,user_regdate FROM users WHERE user_id=$cuserid LIMIT 1";
                        $results = mysql_query_eval($query,$db_link);
                        $udatac = mysql_fetch_array($results);
                        $cuser = $udatac['username']; $chomepage = $udatac['user_website']; $cposts = $udatac['user_posts']; $regdate = $udatac['user_regdate'];
                        $ctitlenum = $udatac['user_rank'];

                        $query = "SELECT rank_title FROM ranks WHERE rank_id=$ctitlenum LIMIT 1";
                        $results = mysql_query_eval($query,$db_link);
                        $udatac = mysql_fetch_array($results);
                        $ctitle = $udatac['rank_title'];

                        $query = "SELECT sess_id FROM sessions WHERE user_id=$cuserid LIMIT 1";
                        $results = mysql_query_eval($query,$db_link);
                        $udatac = mysql_fetch_array($results);
                        $conline = $udatac['sess_id'];

                        if ( $chomepage != "" ) {
                            $hpage = "<a
                                href=\"$chomepage\" target=\"_blank\"><img src=\"".$Globals{'vbulletin'}."/images/www_icon.gif\" alt=\"Visit ".$cuser."'s homepage!\"
                                border=\"0\"></a>)";
                        }
                    }
                    if ($phoid != "") {
                        $ugallery = "<a href=\"".$Globals{'maindir'}."/showgallery.php?user=$cuserid&cat=500&thumb=1\"><img alt=\"Visit this user's gallery.\" border=\"0\"
                            src=\"".$Globals{'idir'}."/gallery/phbb.gif\">";
                    }
                }

                if ($Globals{'vbversion'} == "phpBB2") {
                    if ($cuserid != 0) {
                        if ( !empty( $Globals{'dprefix'} ) ) {
                            $utable=$Globals{'dprefix'} ."_users";
                            $rtable=$Globals{'dprefix'} ."_ranks";
                        }
                        else {
                            $utable="users";
                            $rtable="ranks";
                        }
                        $query = "SELECT $utable.username,$utable.user_website,$utable.user_posts,$rtable.rank_title,$utable.user_regdate FROM ";
                        $query .= "$utable LEFT JOIN $rtable ON $utable.user_rank = $rtable.rank_id WHERE $utable.user_id=$cuserid LIMIT 1";
                        $results = mysql_query_eval($query, $db_link);

                        if ( $results ) {
                            $row = mysql_fetch_row($results);
                            list( $cuser, $chomepage, $cposts, $ctitle, $regdate) = $row;

                            list($rsec,$rmin,$rhour,$rmday,$rmon,$ryear,$rwday,$ryday,$risdst) = localtime($regdate);
                            $rmon++;
                            $ryear=1900+$ryear;
                            $regdate = "$rmon/$rmday/$ryear";
                        }
                    }
                }

                if ($Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
                    if ($cuserid != 0) {
                        $query = "SELECT U_Username,U_Homepage,U_Totalposts,U_Title,U_Registered FROM w3t_Users WHERE U_Number=$cuserid LIMIT 1";
                        $results = mysql_query_eval($query,$db_link);
                        $udatac = mysql_fetch_array($results);
                        $cuser = $udatac['U_Username']; $chomepage = $udatac['U_Homepage']; $cposts = $udatac['U_Totalposts'];
                        $tdate = $udatac['U_Registered'];
                        list($rsec,$rmin,$rhour,$rmday,$rmon,$ryear,$rwday,$ryday,$risdst) = localtime($tdate);
                        $rmon++;
                        $ryear=1900+$ryear;
                        $regdate = "$rmon/$rmday/$ryear";
                        $ctitle = $udatac['U_Title'];
                    }
                }

                if ($Globals{'vbversion'} == "Internal" || $Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6" ) {
                    if ($phoid != "") {
                        $ugallery = "<a href=\"".$Globals{'maindir'}."/showgallery.php?user=$cuserid&cat=500&thumb=1\"><img alt=\"Visit this user's gallery.\"  border=\"0\" src=\"".$Globals{'idir'}."/gallery.gif\">";
                    }
                    if ($chomepage != "" ) {
                        $chomepage = str_replace("http://", "", $chomepage);
                        $hpage = "<a href=\"http://$chomepage\" target=\"_blank\"><img src=\"".$Globals{'idir'}."/www.gif\" alt=\"Visit ".$cuser."'s homepage!\"
                            border=\"0\"></a>";
                    }
                }

                if ($regdate != "") $regdate = "Registered: $regdate<br>";
                if ($cposts != "") $cposts = "Posts: $cposts<br>";
                if ($clocation != "") $clocation = "Location: $clocation<Br>";
                if ($cdesc != "") $cdesc = "<p>$cdesc";

                if ($ckcolor == 1) {
                    $fillcolor = $Globals{'altcolor1'};
                    $ckcolor = 0;
                }
                else {
                    $fillcolor = $Globals{'altcolor2'};
                    $ckcolor = 1;
                }

                $comment = convert_markups($comment);
                $comment = ConvertReturns($comment);

                $posts .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
                    align=\"center\"><tr><td>
                    <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\" width=\"100%\">
                    <tr><td bgcolor=\"$fillcolor\" width=\"175\" valign=\"top\" nowrap>
                    <font face=\"verdana, arial, helvetica\" color=\"".$Globals{'commentstext'}."\" size=\"2\"><b>$cuser</b></font><br>
                    <font face=\"verdana,arial,helvetica\" color=\"".$Globals{'commentstext'}."\" size=\"1\">$ctitle</font><br>
                    <p><font face=\"verdana,arial,helvetica\" color=\"".$Globals{'commentstext'}."\" size=\"1\">$regdate$clocation$cposts$cdesc</font></td>

                    <td bgcolor=\"$fillcolor\" width=\"100%\" valign=\"top\">
                    <p><table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><Td><font face=\"verdana, arial, helvetica\" color=\"".$Globals{'commentstext'}."\"
                    size=\"2\">$comment</font></td><Td align=\"right\" valign=\"top\">
                    <font face=\"verdana, arial, helvetica\" color=\"".$Globals{'commentstext'}."\" size=\"2\">$ratingdisp</font></td></tr></table></p>

                    <p></p><p></p>
                    </td></tr><tr>
                    <td bgcolor=\"$fillcolor\" width=\"175\" height=\"16\" nowrap><font face=\"verdana,arial,helvetica\" size=\"1\">$isonline
                    $mon-$mday-$year <font color=\"".$Globals{'commentstext'}."\">$cclock</font></font></td>

                    <td bgcolor=\"$fillcolor\" width=\"100%\" valign=\"middle\" height=\"16\">
                    <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
                    <tr valign=\"bottom\"><td><font face=\"verdana,arial,helvetica\" size=\"1\">
                    <a href=\"$profilelink\" target=\"_blank\">";

                if ( $cuserid > 0 ) {
                    if ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
                        $posts .= "<img src=\"".$Globals{'vbulletin'}."/images/profile.gif\"
                            border=\"0\" alt=\"Click Here to See the Profile for $cuser\"></a> <a
                            href=\"$privatelink\"><img
                            src=\"".$Globals{'vbulletin'}."/images/sendpm.gif\" border=\"0\" alt=\"Click here to Send $username a Private Message\"></a>  $hpage
                            <a href=\"".$Globals{'vbulletin'}."/search.php?s=&action=finduser&userid=$cuserid\"><img src=\"".$Globals{'vbulletin'}."/images/find.gif\"
                            border=\"0\"
                            alt=\"Find more posts by $cuser\"></a><!--PhotoPost, copyright All, Enthusiast, Inc.--> <a
                            href=\"".$Globals{'vbulletin'}."/member2.php?s=&action=addlist&userlist=buddy&userid=$cuserid\"><img
                            src=\"".$Globals{'vbulletin'}."/images/buddy.gif\" border=\"0\"
                            alt=\"Add $cuser to your buddy list\"></a> $ugallery
                            </font></td>
                            <td align=\"right\" nowrap>
                            <a href=\"comments.php?photo=$photo&cedit=$id\"><img src=\"".$Globals{'vbulletin'}."/images/edit.gif\" border=\"0\"
                            alt=\"Edit/Delete Message\"></a></td>";
                    }
                    if ($Globals{'vbversion'} == "Internal" || $Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
                        $posts .= "<img src=\"".$Globals{'idir'}."/profile.gif\"
                            border=\"0\" alt=\"Click Here to See the Profile for $cuser\"></a>
                            $hpage<!--PhotoPost, copyright All, Enthusiast, Inc.-->
                            $ugallery
                            </font></td>
                            <td align=\"right\" nowrap><font face=\"verdana,arial,helvetica\" size=\"1\" >
                            <a href=\"comments.php?photo=$photo&cedit=$id\"><img src=\"".$Globals{'idir'}."/edit.gif\" border=\"0\"
                            alt=\"Edit/Delete Message\"></a>
                            </font></td>";
                    }
                    if ( $Globals{'vbversion'} == "phpBB" || $Globals{'vbversion'} == "phpBB2" ) {
                        $posts .= "<img src=\"".$Globals{'idir'}."/profile.gif\"
                            border=\"0\" alt=\"Click Here to See the Profile for $cuser\"></a>
                            $hpage<!--PhotoPost, copyright All, Enthusiast, Inc.-->
                            $ugallery
                            </font></td>
                            <td align=\"right\" nowrap><font face=\"verdana,arial,helvetica\" size=\"1\" >
                            <a href=\"comments.php?photo=$photo&cedit=$id\"><img src=\"".$Globals{'idir'}."/edit.gif\" border=\"0\"
                            alt=\"Edit/Delete Message\"></a>
                            </font></td>";
                    }
                }

                $posts .= "</tr></table></td></tr></table></td></tr></table>";
            }
        }
    }
}

    $cheader = "<p><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\" width=\"".$Globals{'tablewidth'}."\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr>
        <td bgcolor=\"".$Globals{'headcolor'}."\" width=\"175\" nowrap><font face=\"verdana,arial,helvetica\" size=\"1\" color=\"".$Globals{'headfontcolor'}."\"><b>Author</b></font></td>
        <td bgcolor=\"".$Globals{'headcolor'}."\" width=\"100%\">
        <table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
        <tr>
        <td width=\"100%\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"verdana,arial,helvetica\" size=\"1\" color=\"".$Globals{'headfontcolor'}."\"><b>Thread</b></font></td>
        <td nowrap><a href=\"comments.php?photo=$photo\">$postreply</a>&nbsp;</td>
        </tr>
        </table>
        </td>
        </tr>
        </table>
        </td></tr></table>";

    // begin pages/nav system ##
    $comnav="";

    if ($compages > 1) {
        $comnav .= "<table cellpadding=\"5\" cellspacing=\"0\" border=\"0\" width=\"100%\"><Tr bgcolor=\"".$Globals{'maincolor'}."\"><Td width=\"40%\"></td>
            <Td><font size=\"2\" face=\"verdana, arial\" color=\"".$Globals{'maintext'}."\"><B>Page:&nbsp;</b> ";
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
                $comnav .= "<a href=\"".$Globals{'maindir'}."/showphoto.php?photo=$photo&cpage=$p&pperpage=$pperpage#poststart\">$thispage</a>";
            }
            if ($p >$theend) {
                last;
            }
            if ($cpage == $p) {
                $comnav .= "<b>$p</b>";
            }
        }
        if ($cpage < $compages) {
            $nextpage=$cpage+1;
            $more = "<a href=\"".$Globals{'maindir'}."/showphoto.php?photo=$photo&cpage=$nextpage&sort=$sortparam&perpage=$pperpage\"><img
                height=\"16\" width=\"63\" alt=\"More Items\"
                border=\"0\" src=\"".$Globals{'idir'}."/more.gif\"></a>";
        }
        else {
            $more = "&nbsp";
        }

        $comnav .= "</td><td width=\"20%\"><center>$more</center></td></tr></table>";
    }
    // end pages/nav ###

    $query = "UPDATE photos SET views=views+1 WHERE id=$photo";
    $result = mysql_query_eval($query,$link);

    $comq = "<p><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"
            width=\"".$Globals{'tablewidth'}."\" align=\"center\"><tr><td>
            <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr align=\"center\">
            <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font
            face=\"verdana,arial,helvetica\" color=\"".$Globals{'headfontcolor'}."\" size=\"2\"><b>
            Add your comments</font>
            </font></td></tr>
            <form name=\"theform\" method=\"post\" action=\"".$Globals{'maindir'}."/comments.php\">";

    $comq .= "<tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">
            Username</font></td><td colspan=\"2\" bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana,arial\" color=\"".$Globals{'maintext'}."\">$username
            </font></td></tr>
            <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Rate this photo overall</font></td>
            <td colspan=\"2\" bgcolor=\"".$Globals{'maincolor'}."\"><select name=\"rating\"><option
            selected>$erating</option>
            <option value=\"5\">5 - Excellent</option>
            <option value=\"4\">4 - Great</option>
            <option value=\"3\">3 - Good</option>
            <option value=\"2\">2 - Fair</option>
            <option value=\"1\">1 - Poor</option>
            </select></td></tr>
            <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Comments:</td><td
            bgcolor=\"".$Globals{'maincolor'}."\"><textarea
            name=\"message\" cols=\"40\" rows=\"5\">$ecomments</textarea></td>";

    $comq .= "<Center>
            <Tr><Td colspan=\"3\" bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana,arial\"><center>
            <input type=\"hidden\" name=\"cat\" value=\"$cat\">
            <input type=\"hidden\" name=\"password\" value=\"$password\">
            <input type=\"hidden\" name=\"puserid\" value=\"$userid\">
            <input type=\"hidden\" name=\"photo\" value=\"$photo\">";

    $comq .= "<input type=\"hidden\" name=\"post\" value=\"new\"><input type=\"submit\" value=\"Submit Post\">";
    $comq .= "</td></tr></table>";


    if ( $posts != ""  ) {
        print "$output$cheader$posts$comnav$comq</td></tr></table><p>".$Globals{'cright'}."$footer";
    }
    else {
        print "$output$comq</td></tr></table><p>".$Globals{'cright'}."$footer";
    }
} // end individual photo display ###

//print $output;

// Closing connection
mysql_close($link);
mysql_close($db_link);

?>

