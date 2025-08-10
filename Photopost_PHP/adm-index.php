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

require "adm-inc.php";

if ( empty($ppaction) ) $ppaction="";

if ( $ppaction == "" ) {
    //# This is the thumbnail page display section, show photos that need approval
    //# do the sort box //#
    $query="SELECT * FROM sort";
    $resultc = ppmysql_query($query,$link);

    if ( !isset($sort) ) {
        $inputsort=1;
    }
    else {
        $inputsort = $sort;
    }

    $sortoptions = ""; $sortcode=""; $posternav="";

    while ( list($sortid, $sortname, $sortc) = mysql_fetch_row($resultc) ) {
        if ($inputsort != $sortid) {
            $sortoptions .= "<OPTION value = $sortid>$sortname</OPTION>";
        }
        else {
            $sortdefault = "<option selected value=\"$sortid\">$sortname</option>";
            $sortcode = "$sortc";
        }
    }
    ppmysql_free_result($resultc);    
    
    if ( empty($sortdefault) ) {
        $sortdefault = "<option selected>Date (newest first)</option>";
    }

    $sort = "<select onChange=\"submit();\" name=\"sort\" class=\"bginput\">$sortdefault$sortoptions</select>";
    //# end sort box //#

    $catdefault = "";
    if ( !empty($cat) ) {
        if ( $cat != "notcat" ) {
            $query = "SELECT id,catname,thumbs FROM categories WHERE id=$cat LIMIT 1";
            $resultb = ppmysql_query($query,$link);
            while ( list( $subid, $subcatname, $subthumbs ) = mysql_fetch_row($resultb) ) {
                if ( $subid < 3000 ) {
                    if ( $ugcat{$subid} != 1 ) {
                        $catdefault = "<option selected value=\"$subid\">$subcatname</option>";
                    }
                }
            }
            ppmysql_free_result( $resultb );
        }
    }
    else {
        if ( $ugcat{500} != 1 ) {
            $query = "SELECT id,catname,thumbs FROM categories WHERE id=500 LIMIT 1";
            $resultb = ppmysql_query($query,$link);
            list( $subid, $subcatname, $subthumbs ) = mysql_fetch_row($resultb);
            ppmysql_free_result( $resultb );

            $catdefault = "<option selected value=\"$subid\">$subcatname</option>";
        }
    }

    if ( empty($cat) ) $cat=0;
    $catdisp = "<select onChange=\"submit();\" name=\"cat\"><option selected value=\"0\">Approve Queue</option>";
    $selected = $cat;
    catmoveopt(0);
    $catdisp .= $catoptions;

    if ( $cat == 0 ) {    
        $query="SELECT id FROM photos WHERE approved=0";
    }
    else {
        $query="SELECT id FROM photos WHERE cat=$cat";
    }
    
    $nump = ppmysql_query($query,$link);
    $rows = mysql_num_rows($nump);
    ppmysql_free_result( $nump );

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
        $posternav .= "<table width=\"560\"><tr><td></td><td>";  //# Create bottom page nav bar for pagination
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
                $posternav .= "<a href=\"{$Globals['maindir']}/adm-index.php?page=$p&amp;sort=$inputsort&amp;perpage=$perpage&amp;cat=$cat\">$thispage</a>";
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
            $more = "<a href=\"{$Globals['maindir']}/adm-index.php?page=$nextpage&amp;sort=$inputsort&amp;perpage=$perpage&amp;cat=$cat\"><img
                height=\"16\" width=\"63\" alt=\"More Items\"
                border=\"0\" src=\"".$Globals['idir']."/more.gif\"></a>";
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
    
    printheader( 0, "PhotoPost Admin" );

    $output = "<hr><center>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font size=\"{$Globals['fontmedium']}\"
        face=\"{$Globals['mainfonts']}\"><b>PhotoPost Admin Photo Approval Interface</b></font>
        </td></tr>
        <form method=\"post\" action=\"{$Globals['maindir']}/adm-index.php\">
        <tr>
        <td bgcolor=\"{$Globals['headcolor']}\" colspan=\"4\"><table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td align=\"left\">
        <b>
        <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\">$adminmenu</b></font></td><td>
        <center></td>
        <td align=\"right\" nowrap=\"nowrap\"><font size=\"{$Globals['fontmedium']}\"
        face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\">
        <b>Category:</b> $catdisp</select><br />
        <b>Sort by:</b> $sort<br />        
        <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['headfontcolor']}\">
        <b>Per Page:</b></font>
        <select onChange=\"submit();\" name=\"perpage\" class=\"bginput\"><option
        selected=\"selected\">$perpage</option><option>4</option><option>12</option><option>20</option><option>28</option></select></td></tr></form></table></td></tr>
        <form method=\"post\" action=\"{$Globals['maindir']}/adm-index.php\"><input type=\"hidden\" value=\"dochanges\" name=\"ppaction\">";

    $count = 0;
    $photocount = 0;
    $cntresults = 0;

    if ( $cat == 0 ) {
        $query = "SELECT * FROM photos WHERE approved='0' $sortcode";
    }
    else {
        $query = "SELECT * FROM photos WHERE cat=$cat $sortcode";        
    }
    
    $queryz = ppmysql_query($query,$link);
    $rowcnt = mysql_num_rows($queryz);

    while ( list($id,$tuser,$tuserid,$cat,$date,$title,$desc,$keywords,$bigimage,$width,$height,$filesize,$views,$medwidth,$medheight,$medsize,$approved) = mysql_fetch_row($queryz)) {
        $cntresults++;
        $filesize=$filesize/1024;
        $filesize=sprintf("%1.1f", $filesize);
        $filesize = $filesize."k";

        if ($cntresults >= $startnumb) {
            if ($cntresults < ($startnumb+$perpage)) {
                $photocount++;
                $count++;
                if ($count == 5) {
                        $output .= "</tr><tr>";
                        $count=1;
                }

                $theext = substr( $bigimage, strlen($bigimage) - 4,4 );
                $filename = $bigimage;
                $filename= str_replace( $theext, "", $filename );

                $ppdate = formatppdate( $date );        

                if ($Globals['bigsave'] == "yes") {
                    if ($filesize != "0") {
                        if ($medsize > 0) {
                            $medsize=$medsize/1024;
                            $medsize=sprintf("%1.1f", $medsize);
                            $medsize=$medsize."k";
                            $ilink = $Globals['datadir']."/$cat/$tuserid$filename-med$theext";
                            $biglink = $Globals['datadir']."/$cat/$tuserid$filename$theext";
                            $fsizedisp = "<a href=\"".$Globals['datadir']."/$cat/$tuserid$filename-med$theext\">$medsize</a>, <a
                                href=\"".$Globals['datadir']."/$cat/$tuserid$filename$theext\">$filesize</a>";
                        }
                        else {
                            $ilink = $Globals['datadir']."/$cat/$tuserid$filename$theext";
                            $fsizedisp = "<a href=\"".$Globals['datadir']."/$cat/$tuserid$filename$theext\">$filesize</a>";
                        }
                    }
                }
                else {
                    $fsizedisp = "n/a";
                }

                $output .= "<td bgcolor=\"{$Globals['maincolor']}\" valign=\"bottom\" width=\"25%\"><center>";
                
                $imgtag = get_imagethumb( $bigimage, $cat, $tuserid, 1 );
                $output .= "$imgtag<br />";
                $output .= "<font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$title</font>";

                if ( $cat < 3000 ) {
                    $query = "SELECT id,catname,thumbs FROM categories WHERE id=$cat LIMIT 1";
                    $result = ppmysql_query($query,$link);
                    list( $subid, $subcatname, $subthumbs ) = mysql_fetch_row($result);
                    ppmysql_free_result( $result );
                }
                else {
                    $query = "SELECT id,albumname FROM useralbums WHERE id=$cat LIMIT 1";
                    $result = ppmysql_query($query,$link);
                    list( $subid, $subcatname ) = mysql_fetch_row($result);
                    ppmysql_free_result( $result );
                    $subthumbs = "yes";
                }                   

                $catdefhide = "<input type=\"hidden\" name=\"catdef$photocount\" value=\"$subid\">";
                $theimage = $Globals['datadir']."/$cat/$tuserid$filename$theext";
                
                $selected = $cat;
                $catdisp = "";
                catmoveopt(0);

                $output .= "<br />#$photocount
                    <br />
                    <table cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"".$Globals['detailbgcolor']."\" width=\"200\"><tr><td>
                    <table cellpadding=\"2\" cellspacing=\"1\" width=\"200\"><tr>
                    <td colspan=\"2\" bgcolor=\"".$Globals['detailbgcolor']."\"><font size=\"{$Globals['fontmedium']}\"
                    face=\"{$Globals['mainfonts']}\"><a href=\"$theimage\">$title</a></font></td></tr><tr>
                    <td bgcolor=\"".$Globals['detailcolor']."\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">User:</font></td>
                    <td bgcolor=\"".$Globals['detailcolor']."\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">$tuser</font></td>
                    </tr><tr>
                    <td bgcolor=\"".$Globals['detailcolor']."\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Views:</font></td>
                    <td bgcolor=\"".$Globals['detailcolor']."\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">$views</font></td>
                    </tr><tr>
                    <td bgcolor=\"".$Globals['detailcolor']."\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Date:</font></td>
                    <td bgcolor=\"".$Globals['detailcolor']."\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">$ppdate</font></td>
                    </tr><tr>
                    <td bgcolor=\"".$Globals['detailcolor']."\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Filesize:</font></td>
                    <td bgcolor=\"".$Globals['detailcolor']."\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">$fsizedisp</font></td>
                    </tr>
                    <tr>
                    <td bgcolor=\"".$Globals['detailcolor']."\" colspan=\"2\"><center><Select name=\"category$photocount\"
                    class=\"bginput\">$catoptions</select></center></td>
                    </tr>

                    <tr><td bgcolor=\"".$Globals['detailcolor']."\" colspan=\"2\">
                    <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">
                    <input type=\"hidden\" name=\"theimage$photocount\" value=\"$bigimage\">
                    <input type=\"hidden\" name=\"origcat$photocount\" value=\"$subid\">
                    <input type=\"hidden\" name=\"tuserid$photocount\" value=\"$tuserid\">
                    <input name=\"delete$photocount\" value=\"$id\" type=\"checkbox\"> Delete&nbsp;&nbsp;
                    <input name=\"approve$photocount\" value=\"$id\" CHECKED type=\"checkbox\"> Approve&nbsp;&nbsp;
                    $catdefhide
                    </td></tr>
                    </table></td></tr></table></td>";
            }
        }
    }
    
    if ( $queryz )
        ppmysql_free_result( $queryz );
    
    $squares = 4-$count;
    for ($v=1; $v<=$squares; $v++) {
        $output .= "<td bgcolor=\"{$Globals['maincolor']}\">&nbsp</td>";
    }

    if ($cntresults == 0)
        $noresults = "No photos need approval at this time.<p>";
    else
        $noresults="";

    $output .= "</tr><tr><td bgcolor=\"{$Globals['maincolor']}\" colspan=\"4\"><center>
        <input type=\"hidden\" name=\"thecount\" value=\"$photocount\">
        <input type=\"hidden\" name=\"cat\" value=\"$cat\">        
        <input type=\"hidden\" name=\"s\" value=\"$s\">
        <font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">
        $noresults</font>
        <input type=\"submit\" value=\"Submit Changes\"></form></td></tr></table></td></tr></table><p>$posternav{$Globals['cright']}";

    print $output;
    printfooter();    
//// end thumbnail page section ////
}

if ($ppaction == "dochanges") {
    $totalphotos=$thecount;

    list($seconds,$minutes,$hours,$mdays,$mons,$years,$wday,$yday,$isdst) = localtime();
    $mons++;

    for ($i=1; $i<=$totalphotos; $i++) {
        unset($appkey);
        unset($delkey);
        
        $tappkey = "approve$i";
        $tdelkey = "delete$i";
        $catkey = "category$i";
        $catdefkey = "catdef$i";
        $origcat = "origcat$i";
        $tuserid = "tuserid$i";
        $theimage = "theimage$i";

        if (!empty(${$tappkey})) $appkey = ${$tappkey};
        else $appkey="";
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
                    diewell("The category you chose for photo #$i can't contain photos.");
                    exit;
                }

                if ( $origcat != $newcatid ) {
                    move_image( $origcat, $newcatid, $tuserid, $theimage );
                }

                //print "Approve photo \#$i: PID\#$pid, \n";
                $query = "UPDATE photos SET approved='1' WHERE id=$pid";
                $resulta = ppmysql_query($query,$link);

                $query = "UPDATE photos SET cat=$newcatid WHERE id=$pid";
                $resulta = ppmysql_query($query,$link);

                $query = "UPDATE comments SET cat=$newcatid WHERE photo=$pid";
                $resulta = ppmysql_query($query,$link);

                $alist .= ",$pid";
                if ( $Globals['useemail'] == "yes" ) {
                    if ( $cat == 0 ) {
                        admin_email('approve',$pid);
                    }
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
                $pid = $delkey;

                $query = "SELECT userid,cat,title,bigimage,medsize FROM photos WHERE id=$pid";
                $resulta = ppmysql_query($query,$link);
                list( $uid, $cat, $title, $filename, $medsize ) = mysql_fetch_row($resulta);
                ppmysql_free_result( $resulta );

                inc_user_posts( "minus", $uid );

                if ($uid != "") {
                    remove_all_files( $filename, $medsize, $uid, $cat );
                }
                else {
                    print "No pic to delete: error.";
                    exit;
                }

                //# end delete the files //#

                if ($Globals['useemail'] == "yes") {
                    admin_email( 'delete', $pid, $uid, $title );
                    //&email('delete',$pid);
                }

                $query = "DELETE FROM photos WHERE id=$pid";
                $resulta = ppmysql_query($query, $link);

                $query = "DELETE FROM comments WHERE photo=$pid";
                $resulta = ppmysql_query($query, $link);
            }
            else {
                print "You checked both approve and delete on photo number $i.";
                exit;
            }
        }
    }
    
    $furl = "{$Globals['maindir']}/adm-index.php?s=$s&amp;cat=$cat";
    forward( $furl, "Processing completed! Returning to index." );
    exit;
}

?>
