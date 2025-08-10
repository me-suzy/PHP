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

include("adm-inc.php");
include("email-inc.php");

if ( empty($ppaction) ) $ppaction="";

if ( $ppaction == "" ) {
    //# This is the thumbnail page display section, show photos that need approval
    //# do the sort box //#
    $query="SELECT * FROM sort";
    $resultc = mysql_query_eval($query,$link);

    if ( !IsSet($sort) ) {
        $inputsort=1;
    }
    else {
        $inputsort = $sort;
    }

    if (empty($sortparam)) $sortparam="";
    $sortoptions = ""; $sortcode=""; $posternav="";

    while ( $row = mysql_fetch_array($resultc, MYSQL_ASSOC) ) {
        //list($sortid, $sortname, $sortc) = $row;
        $sortid = $row['sortid']; $sortname=$row['name']; $sortc=$row['code'];

        if ($sortparam != $sortid) {
            $sortoptions .= "<OPTION value = $sortid>$sortname</OPTION>";
        }
        else {
            $sortdefault = "<option selected value=\"$sortid\">$sortname</option>";
            $sortcode = "$sortc";
        }

        if ( empty($sortdefault) ) {
            $sortdefault = "<option selected>Date (newest first)</option>";
        }
    }

    $sort = "<select onChange=\"submit();\" name=\"sort\" style=\"font-size: 9pt; background: FFFFFF;\">$sortdefault$sortoptions</select>";
    //# end sort box //#

    //$querya="SELECT id,catname FROM categories WHERE id=$thecat";
    //$catq = mysql_query_eval($querya,$link);
    //$catr = mysql_fetch_array($catq);
    //$thecatname = $catr['catname'];
    //mysql_free_result($catq);

    $query="SELECT id FROM photos WHERE approved=0";
    $nump = mysql_query_eval($query,$link);
    $numpho = mysql_fetch_array($nump);
    $rows = mysql_num_rows($nump);

    // begin pages/nav system //
    if ( empty($perpage) ) $perpage = 8;

    if ( !empty($page) ) {
        $startnumb=($page*$perpage)-$perpage+1;
    }
    else {
        $page=1;
        $startnumb=1;
    }
    $pages = ($rows/$perpage);
    $pages = intval($pages)+1;

    if ($pages > 1) {
        $posternav .= "<table width=\"560\"><Tr><Td></td><Td>";  //# Create bottom page nav bar for pagination
        $thestart = "";

        if ($page < 11) {
            $thestart=1;
        }
        if ($page > 10) {
            $thestart=$page/10;
            $thestart=intval($thestart);
            $thestart=$thestart*10;
        }
        $theend=$thestart+9;

        for ($p=$thestart; $p <= $pages; $p++) {
            if ($p != $thestart) {
                $posternav .= " | ";
            }

            if ($page != $p) {
                if ($p == ($theend+1)) {
                    $thispage = "$p>";
                }
                else {
                    $thispage = "$p";
                }
                $posternav .= "<a href=\"".$Globals{'maindir'}."/adm-index.php?page=$p&sort=$inputsort&perpage=$perpage\">$thispage</a>";
            }

            if ($p > $theend) {
                last;
            }
            if ($page == $p) {
                $posternav .= "<b>$p</b>";
            }
        }

        if ($page < $pages) {
            $nextpage=$page+1;
            $more = "<a href=\"".$Globals{'maindir'}."/adm-index.php?page=$nextpage&sort=$inputsort&perpage=$perpage\"><img
                height=\"16\" width=\"63\" alt=\"More Items\"
                border=\"0\" src=\"".$Globals{'idir'}."/more.gif\"></a>";
        }
        else {
            $more = "&nbsp";
        }
        $posternav .= "</td><td width=\"20%\"><center>$more</center></td></tr></table>";
    }
    // end pages/nav //#

    if ($perpage == "") {
        $perpage = 10;
    }

    if (empty($thecat)) $thecat="";
    if (empty($s)) $s="";

    $output = "$header
        <p><Center>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\"
        color=\"".$Globals{'headfontcolor'}."\" size=\"1\"><font size=\"2\"
        face=\"verdana\"><B>PhotoPost Admin Photo Approval Interface</font>
        </font></td>
        </tr>
        <form method=\"post\" action=\"".$Globals{'maindir'}."/adm-index.php\">
        <tr id=\"cat\">
        <td bgcolor=\"#f7f7f7\" colspan=\"4\"><Table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><Td>
        <b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$adminmenu</b></font></td><td>
        <center></td>
        <td align=\"right\"><font size=\"2\"
        face=\"verdana,arial\" size=\"2\" color=\"#000000\"><b>Sort by:</b> $sort<input type=\"hidden\" name=\"cat\"
        value=\"$thecat\"><br>
        <font size=\"2\" face=\"verdana,arial\" color=\"#000000\"><b>Per Page:</b></font>
        <select onChange=\"submit();\" name=\"perpage\" style=\"font-size: 9pt; background: FFFFFF;\"><option
        selected>$perpage</option><option>4</option><option>12</option><option>20</option><option>28</option></select></td></tr></form></table></td></tr>
        <form method=\"post\" action=\"".$Globals{'maindir'}."/adm-index.php\"><input type=\"hidden\" value=\"dochanges\" name=\"ppaction\">";

    $count = 0;

    $photocount = 0;
    $cntresults = 0;

    $query = "SELECT * FROM photos WHERE approved='0' $sortcode";
    $queryz = mysql_query_eval($query,$link);
    $rowcnt = mysql_num_rows($queryz);

    while ( $theusers = mysql_fetch_row($queryz)) {
        list($id,$user,$userid,$cat,$date,$title,$desc,$keywords,$bigimage,$width,$height,$filesize,$views,$medwidth,$medheight,$medsize,$comments) = $theusers;

        $cntresults++;
        $filesize=$filesize/1024;
        $filesize=sprintf("%1.1f", $filesize);
        $filesize = $filesize."k";

        if ($cntresults >= $startnumb) {
            if ($cntresults < ($startnumb+$perpage)) {
                $photocount++;
                $count++;
                if ($count == 5) {
                        $output .= "</tr><Tr>";
                        $count=1;
                }

                $theext = substr( $bigimage, strlen($bigimage) - 4,4 );
                $filename = $bigimage;
                $filename= str_replace( $theext, "", $filename );

                list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime($date);
                $mon++;
                $year = 1900+$year;

                if ($Globals{'bigsave'} == "yes") {
                    if ($filesize != "0") {
                        if ($medsize > 0) {
                            $medsize=$medsize/1024;
                            $medsize=sprintf("%1.1f", $medsize);
                            $medsize=$medsize."k";
                            $ilink = $Globals{'datadir'}."/$cat/$userid$filename-med$theext";
                            $biglink = $Globals{'datadir'}."/$cat/$userid$filename$theext";
                            $fsizedisp = "<A href=\"".$Globals{'datadir'}."/$cat/$userid$filename-med$theext\">$medsize</a>, <A
                                href=\"".$Globals{'datadir'}."/$cat/$userid$filename$theext\">$filesize</a>";
                        }
                        else {
                            $ilink = $Globals{'datadir'}."/$cat/$userid$filename$theext";
                            $fsizedisp = "<A href=\"".$Globals{'datadir'}."/$cat/$userid$filename$theext\">$filesize</a>";
                        }
                    }
                }
                else {
                    $fsizedisp = "n/a";
                }

                $output .= "<Td bgcolor=\"".$Globals{'maincolor'}."\" valign=\"bottom\" width=\"25%\"><center>";

                if ($Globals{'bigsave'} == "yes") {
                    if ($filesize != "0") {
                        if ( file_exists( $Globals{'datafull'}."$cat/$userid$filename-thumb$theext") ) {
                            $output .= "<A href=\"".$Globals{'datadir'}."/$cat/$userid$filename$theext\"><img
                                src=\"".$Globals{'datadir'}."/$cat/$userid$filename-thumb$theext\" border=\"0\"></a>";
                        }
                        else {
                            $output .= "<A href=\"".$Globals{'datadir'}."/$cat/$userid$filename$theext\"><img src=\"".$Globals{'datadir'}."/$userid$filename-thumb.jpg\"
                                border=\"0\"></a>";
                        }
                    }
                    else {
                        if ( file_exists($Globals{'datafull'}."$cat/$userid$filename-thumb$theext") ) {
                            $output .= "<img src=\"".$Globals{'datadir'}."/$cat/$userid$filename-thumb$theext\" border=\"0\">";
                        }
                        else {
                            $output .= "<img src=\"".$Globals{'datadir'}."/$cat/$userid$filename-thumb.jpg\" border=\"0\">";
                        }
                    }
                }
                else {
                    if (file_exists($Globals{'datafull'}."$cat/$userid$filename-thumb$theext") ) {
                        $output = "<img src=\"".$Globals{'datadir'}."/$cat/$userid$filename-thumb$theext\" border=\"0\">";
                    }
                    else {
                        $output .= "<img src=\"".$Globals{'datadir'}."/$cat/$userid$filename-thumb.jpg\" border=\"0\">";
                    }
                }

                if ( $cat < 3000 ) {
                    $query = "SELECT id,catname,thumbs FROM categories WHERE id=$cat LIMIT 1";
                    $result = mysql_query_eval($query,$link);
                    $row = mysql_fetch_row($result);
                    list( $subid, $subcatname, $subthumbs ) = $row;
                }
                else {
                    $query = "SELECT id,albumname FROM useralbums WHERE id=$cat LIMIT 1";
                    $result = mysql_query_eval($query,$link);
                    $row = mysql_fetch_row($result);
                    list( $subid, $subcatname ) = $row;
                    $subthumbs = "yes";
                }                   

                $catdefhide = "<input type=\"hidden\" name=\"catdef$photocount\" value=\"$subid\">";
                $selected = $subid;

                catmoveopt(0);

                $theimage = $Globals{'datadir'}."/$cat/$userid$filename.$theext";

                $output .= "<Br>#$photocount
                    <br>
                    <Table cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"".$Globals{'detailbgcolor'}."\" width=\"200\"><tr><Td>
                    <Table cellpadding=\"2\" cellspacing=\"1\" width=\"200\"><tr>
                    <td colspan=\"2\" bgcolor=\"".$Globals{'detailbgcolor'}."\"><font size=\"2\"
                    face=\"verdana\"><A href=\"$theimage\">$title</a></font></td></tr><Tr>
                    <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">User:</font></td>
                    <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">$user</font></td>
                    </tr><Tr>
                    <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Views:</font></td>
                    <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">$views</font></td>
                    </tr><Tr>
                    <td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Date:</font></td>
                    <td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">$mon/$mday/$year</font></td>
                    </tr><tr>
                    <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Filesize:</font></td>
                    <Td bgcolor=\"".$Globals{'detailcolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">$fsizedisp</font></td>
                    </tr>
                    <Tr>
                    <td bgcolor=\"".$Globals{'detailcolor'}."\" colspan=\"2\"><center><Select name=\"category$photocount\"
                    style=\"font-size: 8pt; background: FFFFFF;\">$catoptions</select></center></td>
                    </tr>

                    <tr><td bgcolor=\"".$Globals{'detailcolor'}."\" colspan=\"2\">
                    <font size=\"2\" face=\"verdana\" color=\"".$Globals{'maintext'}."\">
                    <input type=\"hidden\" name=\"theimage$photocount\" value=\"$bigimage\">
                    <input type=\"hidden\" name=\"origcat$photocount\" value=\"$subid\">
                    <input type=\"hidden\" name=\"tuserid$photocount\" value=\"$userid\">
                    <input name=\"delete$photocount\" value=\"$id\" type=\"checkbox\"> Delete&nbsp;&nbsp;
                    <input name=\"approve$photocount\" value=\"$id\" CHECKED type=\"checkbox\"> Approve&nbsp;&nbsp;
                    $catdefhide
                    </td></tr>
                    </table></td></tr></table></td>";
            }
        }
    }
    $squares = 4-$count;
    for ($v=1; $v<=$squares; $v++) {
        $output .= "<td bgcolor=\"".$Globals{'maincolor'}."\">&nbsp</td>";
    }

    if ($cntresults == 0)
        $noresults = "No photos need approval at this time.<p>";
    else
        $noresults="";

    $output .= "</tr><tr><td bgcolor=\"".$Globals{'maincolor'}."\" colspan=\"4\"><center><input type=\"hidden\" name=\"thecount\" value=\"$photocount\">
        <input type=\"hidden\" name=\"s\" value=\"$s\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">
        $noresults</font>
        <input type=\"submit\" value=\"Submit Changes\"></form></td></tr></table></td></tr></table><p>$posternav".$Globals{'cright'}."$footer";

    print $output;
//// end thumbnail page section ////
}

if ($ppaction == "dochanges") {
    $totalphotos=$thecount;

    list($seconds,$minutes,$hours,$mdays,$mons,$years,$wday,$yday,$isdst) = localtime();
    $mons++;

    for ($i=1; $i<=$totalphotos; $i++) {
        $tappkey = "approve$i";
        $tdelkey = "delete$i";
        $catkey = "category$i";
        $catdefkey = "catdef$i";
        $origcat = "origcat$i";
        $tuserid = "tuserid$i";
        $theimage = "theimage$i";

        if (!empty(${$tappkey})) $appkey = ${$tappkey};
        if (!empty(${$tdelkey})) $delkey = ${$tdelkey};
        else $delkey="";
        $alist="";

        if ($appkey != "") {
            if ($delkey == "") {
                $pid=$appkey;
                $newcatid=${$catkey};
                $catdef=${$catdefkey};
                $origcat=${$origcat};
                $tuserid=${$tuserid};
                $theimage=${$theimage};

                if ($newcatid == "notcat") {
                    dieWell("The category you chose for photo #$i can't contain photos.");
                    exit;
                }

                if ( $origcat != $newcatid ) {
                    move_image( $origcat, $newcatid, $tuserid, $theimage );
                }

                //print "Approve photo \#$i: PID\#$pid, \n";
                $query = "UPDATE photos SET approved='1' WHERE id=$pid";
                $resulta = mysql_query_eval($query,$link);

                $query = "UPDATE photos SET cat=$newcatid WHERE id=$pid";
                $resulta = mysql_query_eval($query,$link);

                $query = "UPDATE comments SET cat=$newcatid WHERE photo=$pid";
                $resulta = mysql_query_eval($query,$link);

                $alist .= ",$pid";
                if ( $Globals{'useemail'} == "yes" ) {
                    admin_email('approve',$pid);
                    if ( $catdef != $newcatid ) {
                        admin_email('moved',$pid);
                    }
                }
            }
            else {
                print "You checked both approve and delete on photo number $i.";
                exit;
            }
        }

        if ($delkey != "") {
            if ($appkey == "") {
                $pid=$delkey;

                $query = "SELECT userid,cat,title,bigimage,medsize FROM photos WHERE id=$pid";
                $resulta = mysql_query_eval($query,$link);
                $row = mysql_fetch_row($resulta);
                list( $uid, $cat, $title, $filename, $medsize ) = $row;

                inc_user_posts( "minus", $uid );

                if ($uid != "") {
                    remove_all_files( $filename, $medsize, $uid, $cat );
                }
                else {
                    print "No pic to delete: error.";
                    exit;
                }

                //# end delete the files //#

                if ($Globals{'useemail'} == "yes") {
                    admin_email( 'delete', $pid, $uid, $title );
                    //&email('delete',$pid);
                }

                $query = "DELETE FROM photos WHERE id=$pid";
                $resulta = mysql_query_eval($query, $link);

                $query = "DELETE FROM comments WHERE photo=$pid";
                $resulta = mysql_query_eval($query, $link);
            }
            else {
                print "You checked both approve and delete on photo number $i.";
                exit;
            }
        }
    }
    $furl = $Globals{'maindir'}."/adm-index.php?s=$s";

    forward( $furl, "Processing completed! Returning to index." );
    exit;
}



?>
