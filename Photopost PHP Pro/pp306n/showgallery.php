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

if (empty($stype)) $stype="";
if (empty($si)) $si="";
if (empty($user)) $user="";
if (empty($thumb)) $thumb=0;

authenticate();

$output = "$header";

if ( IsSet($perpage) ) {
    if ($perpage > 28)
        $perpage=28;
}
else
    $perpage=12;

if ( IsSet($page) ) {
    $startnumb = ($page*$perpage)-$perpage+1;
}
else {
    $page=1;
    $startnumb=1;
}

if ( IsSet($cat) ) {
    if ( $thumb != 0 ) {
        $thecat = $cat;

        // do the sort box //
        $query = "SELECT * FROM sort";
        if ($thecat == "500") {
            if ( empty($user) && empty($si) ) {
                $query = "SELECT * FROM sortmemb";
                $resultc = mysql_query_eval($query,$link);
            }
        }
        $resultc = mysql_query_eval($query,$link);

        if ( empty($sort) ) $sortparam = 1;
        else $sortparam = $sort;

        $sortoptions = ""; $sortdefault=""; $catrows="";

        while ( $row = mysql_fetch_row($resultc) ) {
            list($sortid, $sortname, $sortc) = $row;

            if ($sortparam != $sortid) {
                $sortoptions .= "<OPTION value = $sortid>$sortname</OPTION>";
            }
            else {
                $sortdefault = "<option selected value=\"$sortid\">$sortname</option>";
                $sortcode = "$sortc";
            }

            if ($sortdefault == "") {
                $sortdefault = "<option selected>Date (newest first)</option>";
            }
        }

        $sort = "<select onChange=\"submit();\" name=\"sort\" style=\"font-size: 9pt; background: FFFFFF;\">$sortdefault$sortoptions</select>";

        // end sort box //

        if ( $thecat < 3000 ) {
            $query = "SELECT id,header,footer,headtags,catname FROM categories WHERE id=$thecat";
            $resultb = mysql_query_eval($query,$link);

            if ( $resultb ) {
                $row = mysql_fetch_row($resultb);
                list( $thecatid, $newheader, $newfooter, $newheadtags, $thecatname ) = $row;

                if ( $newheadtags != "" && file_exists($newheadtags) ) {
                    $filearray = file($newheadtags);
                    $headtags = implode( " ", $filearray );
                }

                if ( $newheader != "" && file_exists($newheader) ) {
                    $filearray = file($newheader);
                    $header = implode( " ", $filearray );
                }

                if ( $newfooter != "" && file_exists($newfooter) ) {
                    $filearray = file($newfooter);
                    $footer = implode( " ", $filearray );
                }
                $output = "$headtags$header";
            }
        }
        else {
            $query = "SELECT id,albumname FROM useralbums WHERE id=$thecat";
            $resultb = mysql_query_eval($query,$link);

            if ( $resultb ) {
                $row = mysql_fetch_row($resultb);
                list( $thecatid, $thecatname ) = $row;
            }
        }

        $keycheck=""; $ucheck="";
        $albums=""; $personal=0;
        $subcats=""; $albumrow="";

        if ( $stype == "1" ) $keycheck="CHECKED";
        if ( $stype == "2" ) $ucheck="CHECKED";
        if ( $stype == "" ) $keycheck="CHECKED";

        if ($cat == "500") {
            if ($si == "") {
                if ( $user == "" ) {
                    $thumb = 2;
                }
                else {
                    $query = "SELECT id,albumname,parent,description FROM useralbums WHERE parent=$user";
                    $arows = mysql_query_eval($query,$link);

                    if ( $arows > 0 ) {
                        while ( $aresult = mysql_fetch_row($arows) ) {
                            list( $subid, $subalbumname, $aparent, $subalbumdesc ) = $aresult;

                            $albumrow .= "<tr><Td width=\"30%\" bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana\"><A
                                href=\"".$Globals{'maindir'}."/showgallery.php?cat=$subid&user=$aparent&thumb=1\">$subalbumname</a></td><Td align=\"left\" width=\"70%\"
                                bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana\"
                                color=\"".$Globals{'maintext'}."\">$subalbumdesc</font></td>
                                </tr>\n";
                        }

                        if ( $albumrow != "" ) {
                            $albums .= "<Center>
                                <table cellpadding=\"3\" cellspacing=\"0\" border=\"1\" height=\"40\" width=\"".$Globals{'tablewidth'}."\"><Tr>
                                <Td valign=\"center\" width=\"30%\">
                                <Tr align=\"left\"><Td bgcolor=\"".$Globals{'headcolor'}."\"><font size=\"1\" color=\"".$Globals{'headfontcolor'}."\"
                                face=\"verdana,arial\"><b>Personal Albums</b></font></td>
                                <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\" nowrap><font color=\"".$Globals{'headfontcolor'}."\" size=\"1\"
                                face=\"verdana,arial\"><B>Description</b></font></td>
                                </tr>$albumrow</table><p>";
                        }

                        $personal=1;
                    }
                }
            }
        }
        else {
            catrow( $cat );

            if ( !empty($catrows) ) {
                $subcats = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"
                    bgcolor=\"".$Globals{'bordercolor'}."\" width=\"".$Globals{'tablewidth'}."\" align=\"center\"><tr><td><table cellpadding=\"4\"
                    cellspacing=\"1\" border=\"0\" width=\"100%\">
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

                $subcats .= $catrows;
                $subcats .= "</table></td></tr></table><p>";
            }
        }

        if ($user != "") {
            if ( $cat == "500" ) {
                list( $tcat, $tmail ) = get_username($user);
                $thecatname = "$tcat's Gallery";
                $output = str_replace( $Globals{'galleryname'}, $Globals{'galleryname'}." - $tcat's Gallery", $output );
            }
            elseif ( $cat > 3000 ) {
                list( $tcat, $tmail ) = get_username($user);
                $output = str_replace( $Globals{'galleryname'}, $Globals{'galleryname'}." - $tcat's Personal Album", $output );
            }
        }
        else {
            $output = str_replace( $Globals{'galleryname'}, $Globals{'galleryname'}." - $thecatname", $output );
        }

        $searchterms = $si;
        $inputuser = $user;
        $incat = $cat;

        if ( !empty($incat) ) {
            if ( $incat == "500" )
                $cols = "6";
            else
                $cols = $Globals{'thumbcols'};
        }
        else {
            $cols = $Globals{'thumbcols'};
        }

        childsub($incat);
        $childnav = "<b><font face=\"verdana, arial, helvetica\" size=\"2\"><a
            href=\"".$Globals{'maindir'}."/index.php\">Home</a> $childnav";

        $uploadquery = "?cat=$incat";

        topmenu();

        $output .= "<Center>
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" height=\"40\" width=\"".$Globals{'tablewidth'}."\"><Tr>
            <Td valign=\"center\" width=\"50%\">&nbsp;$childnav</td>
            <td width=\"50%\" align=\"right\" valign=\"center\"><font face=\"verdana, arial\" size=\"2\">$menu&nbsp;</font></td></tr></table>";
            
        if ( $incat == "500" && $user != "" ) {
            if ( $Globals{'mostrecent'} == "yes" )
                display_gallery("latest", $user);
            
            if ( $si == "" ) {
                list( $tname, $tmail ) = get_username($user);
                $output .= "<p><font face=\"verdana, arial, helvetica\" size=\"2\"><A href=\"".$Globals{'maindir'}."/showgallery.php?thumb=1&stype=2&si=$tname&cat=500&perpage=12&sort=1&user=$user\">Click here to see all of $tname's photos</a></font></p>";
            }
            else {
                $output .= "<p>";
            }
        }
        elseif ( $Globals{'mostrecent'} == "yes" ) {
            display_gallery("latest", "", $incat);
            $output .= "<p>";            
        }

            
        $output .= "$albums
            $subcats
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\" align=\"center\"><tr><td>
            <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr align=\"center\">
            <td align=\"left\" colspan=\"$cols\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\"
            color=\"".$Globals{'headfontcolor'}."\"
            size=\"1\"><font size=\"3\" face=\"verdana\"><b>$thecatname</font></font></td></tr>";

        if ($incat != "" && $thumb != 1) {
            if ($incat != "500" ) {
                $space = catrow($incat);
            }

            if ( IsSet($space) ) {
                $catrows .= "</table></td></tr></table><p> <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"
                    bgcolor=\"".$Globals{'bordercolor'}."\" width=\"100%\" align=\"center\"><tr><td><table cellpadding=\"0\"
                    cellspacing=\"1\" border=\"0\" width=\"100%\"><tr align=\"center\"><td colspan=\"5\" align=\"left\">";
            }
            $output .= $catrows;
        }

        $output .= "<";
        $output .= "!--14579-->";
        $output .= "<form method=\"get\" action=\"".$Globals{'maindir'}."/showgallery.php\">
            <tr id=\"cat\"><td bgcolor=\"".$Globals{'navbarcolor'}."\" colspan=\"$cols\">
            <Table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
            <tr>
            <center>
            <td width=\"5%\" align=\"right\" valign=\"middle\"><font color=\"".$Globals{'searchtext'}."\" size=\"2\"
            face=\"verdana,arial\"><b>Search:&nbsp;&nbsp;</b>
            </td>
            <td valign=\"middle\" width=\"1%\"><img src=\"".$Globals{'idir'}."/yellowline.gif\" width=\"1\"
            height=\"28\">
            </td>
            <Td valign=\"middle\" width=\"20%\"><input type=\"radio\" $keycheck name=\"stype\" value=\"1\"><input
            type=\"hidden\"
            name=\"thumb\"
            value=\"1\">
            <font color=\"".$Globals{'searchtext'}."\" size=\"1\"
            face=\"verdana,arial\" color=\"".$Globals{'searchtext'}."\"><b>Keywords</font>
            &nbsp;&nbsp;<input type=\"radio\" $ucheck name=\"stype\" value=\"2\"><font
            color=\"".$Globals{'searchtext'}."\" size=\"1\"
            color=\"".$Globals{'searchtext'}."\" face=\"verdana,arial\"><b>Username</b>&nbsp;</font>
            </td><Td width=\"1%\">
            <img src=\"".$Globals{'idir'}."/yellowline.gif\" width=\"1\" height=\"28\">
            </td>
            <td width=\"20%\"><!--PhotoPost, Copyright All Enthusiast, Inc.-->&nbsp;<input value=\"$searchterms\"
            type=\"text\"
            name=\"si\" style=\"font-size: 8pt;\" size=\"15\">
            <input type=\"submit\" value=\"Search\" style=\"font-size: 8pt;\">
            </td><!-- CyKuH [WTN] -->
            <td width=\"20%\" align=\"right\"><font size=\"1\" face=\"verdana,arial\"
            color=\"".$Globals{'searchtext'}."\"><b>Per
            Page:</b></font>
            <select onChange=\"submit();\" name=\"perpage\" style=\"font-size: 9pt; background: FFFFFF;\"><option
            selected>$perpage</option><option>4</option><option>12</option><option>20</option><option>28</option></select>
            </td>

            <td width=\"26%\" align=\"right\"><font size=\"1\"
            face=\"verdana,arial\" color=\"".$Globals{'searchtext'}."\"><b>Sort by:</b> $sort
            <input type=\"hidden\" name=\"cat\" value=\"$thecat\">
            <input type=\"hidden\" name=\"user\" value=\"$inputuser\">
            </td>
            </tr></form>
            </table></td></tr><tr>";

        // If we're not in the member gallery cat, then print thumbs..
        // Otherwise, print a list of users.

        if ( $thumb != 2 ) {
            if ($si != "") {
                $sterms = trim($si);
                $searchterms = explode(" ", $sterms);
                $scount=0; $phrase="";
                $totalterms = count($searchterms);
                $totalterms++;

                foreach ($searchterms as $key) {
                    $scount++;
                    if ($scount > 1) {
                            $phrase .= " AND ";
                    }
                    $phrase .= "(title LIKE '% $key%' OR description LIKE '% $key%' OR keywords LIKE '% $key%')";
                    $phrase .= " OR (title LIKE '$key%' OR description LIKE '$key%' OR keywords LIKE '$key%')";
                }
            }

            if ( $personal == 1 ) {
                $exclude_cat .= " AND cat < 3000";
            }
            elseif ( $cat > 3000 ) {
                $exclude_cat .= " AND cat=$cat";
            }

            if ($si == "") {
                if ($user == "") {
                    $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating FROM photos
                        WHERE bigimage!='' AND cat=$thecat $exclude_cat $sortcode";
                }
                else {
                    $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating FROM photos
                        WHERE bigimage!='' AND userid=$user AND cat=$thecat $exclude_cat $sortcode";
                }
                $queryv = mysql_query_eval($query,$link);
            }
            else {
                if ($stype == "") {
                    $stype=1;
                }

                $sword = $si;
                if ($stype == "1") {
                    if ($thecat != 500) {
                        $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating
                            FROM photos WHERE $phrase AND cat=$thecat $exclude_cat $sortcode";
                        $queryv = mysql_query_eval($query,$link);
                    }
                    else {
                        if ($phrase != "") {
                            $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating
                                FROM photos WHERE $phrase $exclude_cat $sortcode";
                            $queryv = mysql_query_eval($query,$link);
                        }
                        else {
                            if ($exclude_cat) {
                                $exclude_cat= str_replace("AND", "WHERE", $exclude_cat);
                            }
                            $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating FROM photos $exclude_cat $sortcode";
                            $queryv = mysql_query_eval($query,$link);
                        }

                    }
                }
                else {
                    $sword="$sword";
                    if ($thecat != 500) {
                        $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating
                            FROM photos WHERE user LIKE '$sword' AND cat=$thecat $exclude_cat $sortcode";
                        $queryv = mysql_query_eval($query,$link);
                    }
                    else {
                        $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved,rating
                            FROM photos WHERE user LIKE '$sword' $exclude_cat $sortcode";
                        $queryv = mysql_query_eval($query,$link);
                    }
                }
            }

            $rowcnt = mysql_num_rows($queryv);

            if ($rowcnt == "0") {
                $noresults = "<font size=\"2\" face=\"verdana\" color=\"".$Globals{'maintext'}."\">No photos found.  If you searched, try fewer or less specific
                    keywords.<p></font></b>";
            }
            else {
                $noresults="";
            }

            pagesystem($rowcnt);

            $count=0; $cntresults=0;
            $numcols = $Globals{'thumbcols'}+1;
            $pwidth = intval(100/($numcols-1));

            while ( $row = mysql_fetch_row($queryv) ) {
                list( $id, $user, $tuserid, $pcat, $date, $title, $desc, $keywords, $bigimage, $width, $height, $filesize, $views, $medwidth, $medheight, $medsize, $approved, $imgrating ) = $row;

                $cntresults++;
                $filesize = $filesize/1024;
                $filesize = sprintf("%1.1f", $filesize);
                $filesize = $filesize."k";

                if ($cntresults >= $startnumb) {
                    if ($cntresults < ($startnumb+$perpage)) {
                        // Print out the thumbnail photo along with the title, username, etc
                        // PERL->PHP (had to +1 for some reason)

                        $querya = "SELECT username FROM comments WHERE photo=$id ORDER BY date DESC";
                        $queryz = mysql_query_eval($querya,$link);
                        $lastposterdb = mysql_fetch_array($queryz);
                        $comcount = mysql_num_rows($queryz);

                        $lastposter = $lastposterdb['username'];
                        mysql_free_result($queryz);

                        $count++;
                        if ($count == $numcols) {
                            $output .= "</tr><Tr>";
                            $count = 1;
                        }

                        $theext = substr($bigimage, strlen($bigimage) - 4,4);
                        $filename = $bigimage;
                        $filename = str_replace( $theext, "", $filename);

                        list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($date);
                        $mon++;
                        $year=1900+$year;

                        if ($medsize > 0) {
                            $medsize = $medsize/1024;
                            $medsize = sprintf("%1.1f", $medsize);
                            $medsize = $medsize."k";
                            $ilink = $Globals{'datadir'}."/$pcat/$tuserid$filename-med$theext";
                            $biglink = $Globals{'datadir'}."/$pcat/$tuserid$filename$theext";
                            $fsizedisp = "<A href=\"".$Globals{'maindir'}."/showphoto.php?photo=$id\">$medsize</a>, <A
                                href=\"".$Globals{'maindir'}."/showphoto.php?photo=$id&size=big\">$filesize</a>";
                        }
                        else {
                            $ilink = $Globals{'datadir'}."/$pcat/$tuserid$filename$theext";
                            $fsizedisp = "<A href=\"".$Globals{'maindir'}."/showphoto.php?photo=$id\">$filesize</a>";
                        }

                        // Find out if a photo has comments

                        if ($comcount != "0") {
                            $comline = "<font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\" face=\"verdana,arial\"><a
                                href=\"".$Globals{'maindir'}."/showphoto.php?photo=$id\">$comcount comments</a></font>";
                        }
                        else {
                            $comline = "<font size=\"1\" color=\"".$Globals{'detailfontcolor'}."\"
                                face=\"verdana,arial\">No comments</font>";
                        }

                        // get the rating

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
                            if ( file_exists($Globals{'datafull'}."/$pcat/$tuserid$filename-thumb$theext") ) {
                                $thumbrc = "<img src=\"".$Globals{'datadir'}."/$pcat/$tuserid$filename-thumb$theext\" border=\"0\">";
                            }
                            else {
                                if ( file_exists($Globals{'datadir'}."/$pcat/$tuserid$filename-thumb.jpg") )
                                    $thumbrc = "<img src=\"".$Globals{'datadir'}."/$pcat/$tuserid$filename-thumb.jpg\" border=\"0\">";
                                else
                                    $thumbrc = "<img border=\"0\" src='".$Globals{'idir'}."/nothumb.gif'>";
                            }
                        }
                        else {
                            $thumbrc = "<img width=\"100\" height=\"75\" src=\"".$Globals{'idir'}."/ipending.gif\" border=\"0\">";
                        }
                        // end comments info //

                        $profilelink = get_profilelink( $tuserid );

                        $output .= "<Td bgcolor=\"".$Globals{'maincolor'}."\" valign=\"top\" align=\"center\" width=\"$pwidth%\">
                            <Table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" height=\"125\"><tr><td>
                            <center><A href=\"".$Globals{'maindir'}."/showphoto.php?photo=$id\">$thumbrc</a></center></tr></td></table>
                            <Table cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"".$Globals{'detailbgcolor'}."\" width=\"90%\"><tr><Td>
                            <Table cellpadding=\"2\" cellspacing=\"1\" width=\"100%\"><tr>
                            <td colspan=\"2\" bgcolor=\"".$Globals{'detailbgcolor'}."\"><font size=\"2\"
                            face=\"verdana\"><!--PhotoPost, Copyright All Enthusiast, Inc.--><A
                            href=\"".$Globals{'maindir'}."/showphoto.php?photo=$id\">$title</a>&nbsp;</font></td></tr><Tr>
                            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\">User:</font></td>
                            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\"><A href=\"$profilelink\">$user</a></font></td>
                            </tr><Tr>
                            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\">Views:</font></td>
                            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\">$views</font></td>
                            </tr><Tr>
                            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\">Rating:</font></td>
                            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\">$rating</font></td>
                            </tr><Tr>
                            <td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\">Date:</font></td>
                            <td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\">$mon/$mday/$year</font></td>
                            </tr><tr>
                            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\">Filesize:</font></td>
                            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\">$fsizedisp</font></td>
                            </tr><tr>
                            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\">Dimensions:</font></td>
                            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\">$width X $height</font></td>
                            </tr><tr>
                            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\">Comments:</font></td>
                            <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font color=\"".$Globals{'detailfontcolor'}."\" size=\"1\" face=\"verdana\">$comline</font></td>
                            </tr>
                            </table></td></tr></table></td>";
                    }
                }
            }
            $squares = $Globals{'thumbcols'}-$count;

            for ($v=1; $v <= $squares; $v++) {
                $output .= "<td bgcolor=\"".$Globals{'maincolor'}."\" width=\"$pwidth%\">&nbsp;</td>";
            }

            if ( $posternav != "" ) $posternav = "$posternav<p>";
            
            $output .= "</tr></table></td></tr></table><p>$posternav$noresults";
            
            if ( $incat == "500" && $user != "" ) {
                
                display_gallery("most_views", $inputuser);
                $output .= "<p>";
            }
            elseif ( $Globals{'dispopular'} == "yes" ) {
                display_gallery("most_views", "", $incat);
                $output .= "<p>";            
            }
        
            if ( $incat == "500" && $user != "" ) {
                display_gallery("random", $inputuser);
                $output .= "<p>";
            }
            elseif ( $Globals{'disrandom'} == "yes" ) {
                display_gallery("random", "", $incat);
                $output .= "<p>";            
            }
            
            print "$output<p>".$Globals{'cright'}."$footer";
            exit;
        }
        else {
            $query = "SELECT user,userid,SUM(views) AS tviews,COUNT(*) AS pcount,MAX(lastpost) AS maxlast,MAX(date) AS
                maxdate,date,SUM(filesize) AS tfilesize,id FROM photos GROUP BY user $sortcode";
            $queryz = mysql_query_eval($query,$link);
            $rowcnt = mysql_num_rows($queryz);

            pagesystem($rowcnt);

            if ($rowcnt == "0") {
                $noresults = "<font size=\"2\" face=\"verdana\" color=\"".$Globals{'maintext'}."\"><Br>No photos found.  If you searched, try fewer or less specific
                    keywords.<p></font></b>";
            }
            else {
                $noresults="";
            }

            $uout=""; $cc=0;

            while ( $theimages = mysql_fetch_row($queryz)) {
                list( $theuser, $theuserid, $views, $uphotos, $ulast, $maxdate, $date, $tfilesize, $pid) = $theimages;

                $cc++;
                if ($cc >= $startnumb) {
                    if ($cc < ($startnumb+$perpage)) {
                        $query = "select comments.id from comments,photos where photos.id=comments.photo AND photos.userid=$theuserid";
                        $comcountdb = mysql_query_eval($query,$link);
                        $comcount = mysql_num_rows($comcountdb);

                        //$lastphotime = $ulast+$soffset;
                        list($lpsec,$lpmin,$lphour,$lpmday,$lpmon,$lpyear,$lpwday,$lpyday,$lpisdst) = localtime($ulast);
                        $lpmon++;
                        $lpyear = 1900+$lpyear;
                        $lpclock = thetime($lphour,$lpmin);

                        $lpprint = "$lpmon-$lpmday-$lpyear $lpclock";

                        $mthumb = "";
                        if ($Globals{'membthumb'} == "yes") {
                            $query = "SELECT bigimage,id,cat FROM photos WHERE userid=$theuserid AND approved=1 $exclude_cat ORDER BY date DESC"; // CyKuH [WTN]
                            $resulta = mysql_query_eval($query,$link);
                            $row = mysql_fetch_array($resulta);
                            $bigimage = $row['bigimage']; $phoid = $row['id']; $pcat = $row['cat'];

                            //$mthumb = "[$bigimage][$phoid][$comcount]<br>";
                            if ( $bigimage != "" ) {
                                $theext = substr( $bigimage, strlen($bigimage) - 4,4 );
                                $filename = $bigimage;
                                $filename= str_replace( $theext, "", $filename );

                                if ( file_exists($Globals{'datafull'}."/$pcat/$theuserid$filename-thumb$theext") ) {
                                    $mthumb = "<a href=\"".$Globals{'maindir'}."/showphoto.php?photo=$phoid\"><img
                                        border=\"0\" src=\"".$Globals{'datadir'}."/$pcat/$theuserid$filename-thumb$theext\"></a><Br>";
                                }
                                else {
                                    $mthumb = "<a href=\"".$Globals{'maindir'}."/showphoto.php?photo=$phoid\"><img
                                        border=\"0\" src=\"".$Globals{'datadir'}."/$pcat/$theuserid$filename-thumb.jpg\"></a><Br>";
                                }
                            }
                        }
                        $tfilesize = $tfilesize/1024;
                        $filesize=sprintf("%1.1f", $tfilesize);
                        $filesize = $filesize."k";
                        if ($theuser != "") {
                            list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($ulast);
                            $mon++;
                            $year=1900+$year;
                            $uout .= "<tr><Td width=\"30%\" bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana\"><A
                                href=\"".$Globals{'maindir'}."/showgallery.php?cat=500&user=$theuserid&thumb=1\">$theuser</a></td><Td width=\"11%\"
                                bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana\"
                                color=\"".$Globals{'maintext'}."\"><Center>$uphotos</center></font></td>
                                <Td width=\"11%\" bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\"
                                face=\"verdana\" color=\"".$Globals{'maintext'}."\"><Center>$views</center></font></td>
                                <Td width=\"11%\" bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\"
                                face=\"verdana\" color=\"".$Globals{'maintext'}."\"><Center>$comcount</center></font></td>
                                <Td width=\"11%\" bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\"
                                face=\"verdana\" color=\"".$Globals{'maintext'}."\"><Center>$filesize</center></font></td>
                                <Td width=\"30%\" bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'maintext'}."\">
                                <Center>$mthumb$lpprint</center></font></td></tr>\n";
                        }
                    }
                }
            }

            $output .= "<Tr align=\"center\"><Td bgcolor=\"".$Globals{'headcolor'}."\"><font size=\"1\" color=\"".$Globals{'headfontcolor'}."\"
                face=\"verdana,arial\"><b>Member</b></font></td><Td
                bgcolor=\"".$Globals{'headcolor'}."\"><font color=\"".$Globals{'headfontcolor'}."\" size=\"1\" face=\"verdana,arial\"><b>Photos</b></font><!--CyKuH [WTN]--></td>
                <td bgcolor=\"".$Globals{'headcolor'}."\" nowrap><font color=\"".$Globals{'headfontcolor'}."\" size=\"1\"
                face=\"verdana,arial\"><B>Photo Views</b></font></td>
                <td bgcolor=\"".$Globals{'headcolor'}."\"><font color=\"".$Globals{'headfontcolor'}."\" size=\"1\"
                face=\"verdana,arial\"><B>Comments</b></font></td>
                <td bgcolor=\"".$Globals{'headcolor'}."\"><font color=\"".$Globals{'headfontcolor'}."\" size=\"1\"
                face=\"verdana,arial\"><B>Disk Space</b></font></td>
                <td bgcolor=\"".$Globals{'headcolor'}."\"><font color=\"".$Globals{'headfontcolor'}."\" size=\"1\"
                face=\"verdana,arial\"><B>Last Photo Added</td></tr>$uout</table></td></tr></table>";

            print "$output$posternav$noresults<p>".$Globals{'cright'}."$footer";
            exit;
        }
    }
}

?>
