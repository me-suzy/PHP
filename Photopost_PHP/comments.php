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
require "languages/$pplang/comments.php";
require "login-inc.php";

authenticate();

if ( isset($Globals['ppboards']) && $adminedit != 1 ) {
    if ( $Globals['ppboards'] == "closed" ) {
        print $Globals['pp_lang']['closed'];
        exit;
    }
}

$querystring = findenv("QUERY_STRING");
if ( ($gologin == 1 && $usercomment == 0) ||$querystring == "gologin" ) {
    $furl = $Globals['maindir'];
    $furl = str_replace( $Globals['domain'], "", $furl );
    $furl = "$furl/comments.php?photo=$photo&cedit=$cedit";
    login($furl);
    exit;
}

if ($Globals['allowpost'] == "no" && $adminedit != 1) {
    diewell($Globals['pp_lang']['nocom']);
    exit;
}

if ($gologin != 1) {
    if ($nopost == 1) {
        diewell( $Globals['pp_lang']['noperm'] );
        exit;
    }
}

list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
$mon = $mon + 1;

$query = "SELECT cat,userid FROM photos WHERE id=$photo";
$resulta = ppmysql_query($query,$link);
list( $thiscat, $puserid ) = mysql_fetch_row($resulta);
ppmysql_free_result($resulta);

if ( $ugpost{$thiscat} == 1 || $usercomment == 0 ) {
     diewell( "{$Globals['pp_lang']['nopost']}" );
     exit;
}

if (empty($notify)) $notify="";

if ($Globals['usenotify'] == "yes") {
    if ($notify == "on") {
        $query = "INSERT INTO notify (id,userid,photo) values(NULL,$userid,$photo)";
        $resulta = ppmysql_query($query,$link);

        forward( "{$Globals['maindir']}/showphoto.php?photo=$photo", $Globals['pp_lang']['enabled'] );
        exit;
    }

    if ($notify == "off") {
        if ( !is_numeric($notifyid) || !is_numeric($userid) ) {
            diewell( $Globals['pp_lang']['malform'] );
            exit;
        }

        $query = "DELETE FROM notify WHERE id=$notifyid AND userid=$userid";
        $resulta = ppmysql_query($query,$link);

        forward( "{$Globals['maindir']}/showphoto.php?photo=$photo", $Globals['pp_lang']['disabled'] );
        exit;
    }
}

if ( isset($photo) ) {
    if ( !isset($post) ) {
        $erating=""; $ecomments="";

        if ( isset($cedit) && $cedit != "" ) {
            $query = "SELECT userid,username,rating,comment FROM comments WHERE id=$cedit LIMIT 1";
            $resulta = ppmysql_query($query,$link);
            list( $cuserid, $cusername, $erating, $ecomments ) = mysql_fetch_row($resulta);
            ppmysql_free_result( $resulta );

            if ( ($userid != $cuserid || $userid < 1) && $adminedit != 1 ) {
                diewell( $Globals['pp_lang']['noedit'] );
                exit;
            }
        }
        else {
            $cusername = $username;
        }

        topmenu();

        printheader( $thiscat, $Globals['pp_lang']['addcomment'] );

        $output = "<form name=\"theform\" method=\"post\" action=\"{$Globals['maindir']}/comments.php\">
            <table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td>
            <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr><td>
            <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr><td width=\"175\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\" nowrap>
            <font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontmedium']}\">
            <b>{$Globals['pp_lang']['addyour']}</b></font></td>
            <td colspan=\"2\" width=\"100%\" align=\"right\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontmedium']}\">
            <a href=\"javascript:PopUpHelp('comments.php')\">{$Globals['pp_lang']['help']}</a></font>
            </td></tr>";

        $output .= "<tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">
            {$Globals['pp_lang']['username']}</font></td><td colspan=\"2\" bgcolor=\"{$Globals['maincolor']}\">
            <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$cusername
            </font></td></tr>";

        if ( $Globals['allowrate'] == "yes" && ($userid != $puserid || $adminedit == 1) ) {
            $output .= "<tr><td bgcolor=\"{$Globals['maincolor']}\" nowrap><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['rateoverall']}</font></td>
                <td colspan=\"2\" bgcolor=\"{$Globals['maincolor']}\"><select name=\"rating\">
                <option value=\"0\" selected=\"selected\">{$Globals['pp_lang']['ratethis']}</option>
                <option value=\"5\">5 - {$Globals['pp_lang']['excellent']}</option>
                <option value=\"4\">4 - {$Globals['pp_lang']['great']}</option>
                <option value=\"3\">3 - {$Globals['pp_lang']['good']}</option>
                <option value=\"2\">2 - {$Globals['pp_lang']['fair']}</option>
                <option value=\"1\">1 - {$Globals['pp_lang']['poor']}</option>
                </select></td></tr>";
        }

        $output .= "<tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Comments:<br /><br />
            <font size=\"{$Globals['fontsmall']}\"><a href=\"javascript:PopUpHelp('ubbcode.php')\">{$Globals['pp_lang']['ubb']}</a><br />
            <a href=\"javascript:PopUpHelp('smilies.php')\">{$Globals['pp_lang']['smilies']}</a></font></font>
            </td><td bgcolor=\"{$Globals['maincolor']}\">
            <textarea name=\"message\" cols=\"50\" rows=\"10\">$ecomments</textarea></td>
            <td bgcolor=\"{$Globals['maincolor']}\" align=\"center\">";

        $query = "SELECT bigimage,approved,cat,title,userid FROM photos WHERE id=$photo LIMIT 1";
        $resulta = ppmysql_query($query,$link);
        list( $bigimage, $approved, $cat, $title, $theuser ) = mysql_fetch_row($resulta);
        ppmysql_free_result( $resulta );

        $imgtag = get_imagethumb( $bigimage, $cat, $theuser, $approved );

        $output .= "$imgtag<br />";
        $output .= "<font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$title</font>";
        $output .= "</td></tr>";
        
        if ($cedit != "") {
            $output .= "<tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['delete']}?</font></td><td colspan=\"2\"
                bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\"
                color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['check']}: <input type=\"checkbox\" name=\"delete\" value=\"yes\" /></td></tr>";
        }

        $output .= "<tr><td colspan=\"3\" bgcolor=\"{$Globals['maincolor']}\" align=\"center\">
            <input type=\"hidden\" name=\"cat\" value=\"$cat\" />
            <input type=\"hidden\" name=\"password\" value=\"$password\" />
            <input type=\"hidden\" name=\"puserid\" value=\"$theuser\" />
            <input type=\"hidden\" name=\"photo\" value=\"$photo\" />";

        if ($cedit == "") {
            $output .= "<input type=\"hidden\" name=\"post\" value=\"new\" />
                        <input type=\"submit\" value=\"{$Globals['pp_lang']['submitpost']}\" />";
        }
        else {
            $output .= "<input type=\"hidden\" name=\"postid\" value=\"$cedit\" />
                        <input type=\"hidden\" name=\"post\" value=\"cedit\" />
                        <input type=\"submit\" value=\"{$Globals['pp_lang']['submitedit']}\" />";
        }

        $output .= "</td></tr></table></td></tr></table></form>{$Globals['cright']}";
        
        print $output;
        printfooter();
    }
    else {
        // Go ahead and post the comment to the database
        if ( $rating == "" || $rating > 5 || $rating < 1 ) $rating=0;
        if ( $username == "" ) $gologin=1;
        if ( !isset($message) ) $message="";
        if ( !isset($delete) ) $delete="no";
        $noinsert=0;

        if ( $Globals['ipcache'] != 0 ) {
            $ipaddress = findenv("REMOTE_ADDR");
            $query = "SELECT userid,date,photo FROM ipcache WHERE ipaddr='$ipaddress' AND type='vote' AND photo='$photo' LIMIT 1";
            $result = ppmysql_query($query, $link);
            $numfound = mysql_num_rows($result);

            // for voting we do a double-check; we want to see if the userid has voted before
            if ( $numfound == 0 && $userid != 0 ) {
                $query = "SELECT userid,date,photo FROM ipcache WHERE userid='$userid' AND type='vote' AND photo='$photo' LIMIT 1";
                $result = ppmysql_query($query, $link);
                $numfound = mysql_num_rows($result);
            }

            if ( $numfound > 0 ) {
                list( $userid, $lastdate, $photo ) = mysql_fetch_row($result);

                list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
                $mon = $mon + 1;
                $hour = $hour - $Globals['ipcache'];
                $timeout = mktime($hour,$min,$sec,$mon,$mday,$year);

                if ( $lastdate < $timeout ) {
                    $query = "DELETE FROM ipcache WHERE date < $timeout";
                    $result = ppmysql_query($query,$link);
                }
                else {
                    if ( $rating != 0 ) {
                        diewell( "{$Globals['pp_lang']['every1']} {$Globals['ipcache']} {$Globals['pp_lang']['every2']}");
                        exit;
                    }
                }
            }
            else {
                list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
                $mon = $mon + 1;
                $mytime = mktime($hour,$min,$sec,$mon,$mday,$year);

                $query = "INSERT INTO ipcache (userid,ipaddr,date,type,photo) VALUES ('$userid', '$ipaddress', '$mytime', 'vote', '$photo')";
                $result = ppmysql_query($query,$link);
            }
        }

        if ( ($message == "" && $rating == 0) && $delete=="no" ) {
            diewell( $Globals['pp_lang']['nofill'] );
            exit;
        }

        list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
        $mon = $mon + 1;
        $julian = mktime($hour,$min,$sec,$mon,$mday,$year);

        if ($post == "new") {
            $query = "SELECT userid,comment,rating FROM comments WHERE photo=$photo";
            $resultb = ppmysql_query($query,$link);

            if ( $userid > 0 ) {
                while( list( $checkuid, $checkdup, $checkrating ) = mysql_fetch_row($resultb) ) {
                    if ( $checkdup == $message && $checkuid == $userid ) $noinsert=1;
                }
                ppmysql_free_result($resultb);
            }

            $query = "SELECT cat,title,userid FROM photos WHERE id=$photo";
            $resulta = ppmysql_query($query,$link);
            list( $getcat, $gettitle, $getuserid ) = mysql_fetch_row($resulta);
            ppmysql_free_result($resulta);

            if ($noinsert != 1) {
                $message = fixmessage ( $message );

                $username = addslashes( $username );
                $message = addslashes( $message );

                $query = "INSERT INTO comments (id,username,userid,date,rating,comment,photo,cat) values(NULL,'$username',$userid,$julian,$rating,'$message',$photo,$getcat)";
                $resulta = ppmysql_query($query,$link);
                $lastpostid = mysql_insert_id( $link );

                if ($Globals['cpostcount'] == "yes" && $message != "") {
                    inc_user_posts();
                }

                $query2 = "UPDATE categories SET posts=(posts+1),lastpost='$lastpostid' WHERE id=$getcat";
                $resultb = ppmysql_query($query2, $link);

                if ($Globals['usenotify'] == "yes") {
                    $queryc = "SELECT userid FROM notify WHERE photo=$photo";
                    $resultc = ppmysql_query($queryc, $link);

                    if ( $resultc ) {
                        while( list( $notify_user ) = mysql_fetch_row($resultc) ) {
                            list( $usernm, $useremail ) = get_username( $notify_user );

                            $email_from = "From: {$Globals['adminemail']}";
                            $letter = "$username has posted a reply about the following photo:

\"$gettitle\"
{$Globals['maindir']}/showphoto.php?photo=$photo

If you no longer wish to be notified of replies about the above photo, you can disable notification for it here:

{$Globals['maindir']}/comments.php?notify=off&notifyid=$getuserid&photo=$photo

Thanks!

The {$Globals['webname']} Team
{$Globals['domain']}";

                            $subject = "New Reply to $gettitle at {$Globals['webname']}";
                            mail( $useremail, $subject, $letter, $email_from );
                        }
                        ppmysql_free_result($resultc);
                    }
                }

                if ( $Globals['notifyadmin'] == "yes" ) {                
                    $email_from = "From: {$Globals['adminemail']}";
                    $useremail = $Globals['adminemail'];
                    $letter = "$username has posted a reply about the following photo:

\"$gettitle\" - {$Globals['maindir']}/showphoto.php?photo=$photo

$message";

                    $subject = "New Reply to $gettitle at {$Globals['webname']}";
                    mail( $useremail, $subject, $letter, $email_from );
                }
            }
        }
        else {
            if ( $delete == "yes" ) {
                if ( !is_numeric($postid) ) {
                    diewell( $Globals['pp_lang']['malform'] );
                    exit;
                }

                $query = "DELETE FROM comments WHERE id=$postid";
                $resulta = ppmysql_query($query,$link);

                if ($Globals['cpostcount'] == "yes" && $message != "") {
                    inc_user_posts( "minus" );
                }

                $query2 = "UPDATE categories SET posts=(posts-1) WHERE cat=$getcat";
                $resultb = ppmysql_query($query2, $link);
            }
            else {
                $message = fixmessage( $message );
                $message = addslashes( $message );

                $query = "UPDATE comments SET rating=$rating, comment='$message' WHERE id=$postid";
                $resulta = ppmysql_query($query,$link);

                $query = "UPDATE photos SET lastpost=$julian WHERE id=$photo";
                $resulta = ppmysql_query($query,$link);
            }
        }

        // just to revalidate the rating, we need to recheck the rating for the post
        $query = "SELECT rating FROM comments WHERE photo=$photo AND rating != '0'";
        $resultb = ppmysql_query($query,$link);

        $numrating=0; $sumrating=0; $averagerate=0;
        while( list ( $checkrating ) = mysql_fetch_row($resultb) ) {
            $numrating++;
            $sumrating = ($sumrating+$checkrating);
        }
        if ( $resultb ) ppmysql_free_result( $resultb );

        if ( $numrating != 0 && $sumrating != 0 ) $averagerate = round( $sumrating / $numrating );

        $query = "UPDATE photos SET rating=$averagerate WHERE id=$photo";
        $resulta = ppmysql_query($query, $link);

        if ( $noinsert == 1 ) $text = $Globals['pp_lang']['dupe'];
        else $text = $Globals['pp_lang']['success'];

        forward( "{$Globals['maindir']}/showphoto.php?photo=$photo", $text );
        exit;
    }
}
else {
    print $Globals['pp_lang']['invalid'];
}

?>
