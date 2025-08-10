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

if (empty($susergroupid)) $susergroupid="";
if (empty($susername)) $susername="";
if (empty($email)) $email="";
$message=""; $srch = "";

if ( $ppaction == "users" ) {
    if ( $do == "findusers" ) {
        if ( $susername != "" ) $srch .= "username LIKE '%$susername%'";

        if ( $susergroupid != "" ) {
            if ($srch != "") $srch .= " AND ";
            $srch .= "usergroupid=$susergroupid";
        }

        if ($email != "") {
            if ($srch != "") $srch .= " AND ";
            $srch .= "email LIKE '%$email%'";
        }

        if ($srch != "") $srch = "WHERE $srch";
        if ( empty($perpage) ) $perpage=50;

        if ( !empty($page) ) {
            $page = $page;
            $startnumb = ($page*$perpage)-$perpage+1;
        }
        else {
            $page = 1;
            $startnumb = 1;
        }

        $startnumb = $startnumb-1;

        $query = "SELECT userid from users";
        $nusers = ppmysql_query($query,$link);
        $rcount = mysql_num_rows($nusers);
        pagesystem( $rcount, "admusers" );
                
        printheader( 0, "PhotoPost Users" );

        $output = "<center><hr>
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\"
            align=\"center\"><tr><td>
            <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr align=\"center\">
            <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
            size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\"
            face=\"{$Globals['mainfonts']}\"><b>PhotoPost Select Users</font>
            </font></td>
            </tr>
            <tr>
            <td bgcolor=\"#f7f7f7\"><b>
            <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"#000000\">$adminmenu</b></font></td></tr>
            <tr><td bgcolor=\"{$Globals['maincolor']}\"><center><br />

            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"{$Globals['bordercolor']}\"><tr><td>
            <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\"><tr>
            <Th bgcolor=\"{$Globals['headcolor']}\"><font
            size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\" face=\"{$Globals['mainfonts']}\">Username</th>
            <Th bgcolor=\"{$Globals['headcolor']}\"><font
            size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\" face=\"{$Globals['mainfonts']}\">Actions</th>
            <Th bgcolor=\"{$Globals['headcolor']}\"><font
            size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\" face=\"{$Globals['mainfonts']}\">Email</th>
            <Th bgcolor=\"{$Globals['headcolor']}\"><font
            size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\" face=\"{$Globals['mainfonts']}\">Posts</th>
            <Th bgcolor=\"{$Globals['headcolor']}\"><font
            size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\" face=\"{$Globals['mainfonts']}\">Views</th>
            </tr>";

        $query = "SELECT userid,username,joindate,posts,email,views FROM users $srch ORDER BY username LIMIT $startnumb,$perpage";
        $fusers = ppmysql_query($query,$link);
        $posts = mysql_num_rows($fusers);

        while ( list( $euserid,$eusername,$joindate,$posts,$email,$views ) = mysql_fetch_row($fusers) ) {
            $output .= "<tr>
                <td bgcolor=\"#FFFFFF\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">$eusername</font></td>
                <td bgcolor=\"#FFFFFF\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\"><center>[ <a
                href=\"{$Globals['maindir']}/adm-users.php?ppaction=edituser&amp;uid=$euserid\">Edit User</a> ] [ <a
                href=\"{$Globals['maindir']}/adm-users.php?ppaction=deluser&amp;uid=$euserid&inusername=$eusername\">Delete User</a> ] [ <a
                target=\"_blank\"
                href=\"{$Globals['maindir']}/member.php?ppaction=rpwd&amp;uid=$euserid&verifykey=$joindate&adminreset=1\">Reset Password</a> ]</font></td>
                <td bgcolor=\"#FFFFFF\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">$email</font></td>
                <td bgcolor=\"#FFFFFF\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\"><center>$posts</center></font></td>
                <td bgcolor=\"#FFFFFF\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\"><center>$views</center></font></td>                
                </tr>";
        }
        ppmysql_free_result( $fusers );

        $output .= "<tr><td
            colspan=\"5\" bgcolor=\"#FFFFFF\">$posternav</td></tr></table></td></tr></table></td></tr></table></td></tr></table>";

        if ($rcount > 0) {
            print "$output<p>{$Globals['cright']}<p>";
            printfooter();
            
            exit;
        }
        else {
            $message = "<font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">No users found. Please try an alternate search, or list
                all users.</font><p>";
        }
    }

    if ( $susergroupid != "" ) {
        $query="SELECT groupname from usergroups WHERE groupid=$susergroupid";
        $resultb = ppmysql_query($query,$link);
        list( $usergroup ) = mysql_fetch_row($resultb);
        ppmysql_free_result( $resultb );
    }

    if ($do == "findusers") {
        $groupopt = "<option value=\"$susergroupid\">$usergroup</option><option></option>";
    }
    else {
        $groupopt = "<option></option>";
        $eusername="";
    }

    $query = "SELECT userid from users";
    $nusers = ppmysql_query($query,$link);
    $numusers = mysql_num_rows($nusers);

    $query = "SELECT groupid,groupname from usergroups";
    $groups = ppmysql_query($query,$link);
    while ( list( $groupid, $ugusergroup ) = mysql_fetch_row( $groups ) ) {
        $groupopt .= "<option value=\"$groupid\">$ugusergroup</option>";
    }
    ppmysql_free_result( $groups );
    
    printheader( 0, "PhotoPost Users" );

    $output = "<center><hr>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\"
        face=\"{$Globals['mainfonts']}\"><b>PhotoPost Select Users</font>
        </font></td>
        </tr>
        <tr>
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"#000000\">$adminmenu</b></font></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\"><center><br />
        $message<p><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">
        <a href=\"{$Globals['maindir']}/adm-users.php?ppaction=users&do=findusers\">Click to list all $numusers users</a> or use the
        advanced search box below.<p>

        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"{$Globals['bordercolor']}\"><tr><td>
        <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\">
        <form method=\"post\" action=\"{$Globals['maindir']}/adm-users.php\"><tr><Th bgcolor=\"{$Globals['headcolor']}\" colspan=\"2\"><font
        size=\"{$Globals['fontmedium']}\"
        color=\"{$Globals['headfontcolor']}\" face=\"{$Globals['mainfonts']}\">Select users where: (leave a field blank to ignore
        it)</td></tr>
        <tr><td bgcolor=\"#FFFFFF\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Username contains:</td><td bgcolor=\"#FFFFFF\"><input type=\"text\"
        value=\"$eusername\" name=\"susername\"></td></tr>
        <tr><td bgcolor=\"#FFFFFF\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">and email contains:</td><td bgcolor=\"#FFFFFF\"><input type=\"text\"
        value=\"$email\" name=\"email\"></td></tr>
        <tr><td bgcolor=\"#FFFFFF\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">and usergroup is:</td><td bgcolor=\"#FFFFFF\"><select
        name=\"susergroupid\">$groupopt
        </select></td></tr>
        </table></td></tr></table><p>
        <input type=\"hidden\" name=\"ppaction\" value=\"users\">
        <input type=\"hidden\" name=\"do\" value=\"findusers\">
        <input type=\"submit\" value=\"Find users\">
        </td></tr></table></td></tr></table>";

    print "$output<p>{$Globals['cright']}<p>";
    printfooter();    
}


if ($ppaction == "edituser") {
    if ($do == "process") {
        if ($year == "") $year="0000";
        if ($month == "") $month="0";
        if ($day == "") $day="0";

        $birthday="$year-$month-$day";
        $eusername = addslashes( $eusername );
        $email = addslashes( $email );
        $homepage = addslashes( $homepage );
        $location = addslashes( $location );
        $interests = addslashes( $interests );
        $occupation = addslashes( $occupation );
        $bio = addslashes( $bio );

        $query = "UPDATE users SET username='$eusername',posts=$posts,usergroupid=$usergroupid,email='$email',homepage='$homepage',icq='$icq',
            aim='$aim',yahoo='$yahoo',birthday='$birthday',interests='$interests',occupation='$occupation',bio='$bio',
            location='$location' WHERE userid=$uid";
        $resulta = ppmysql_query($query,$link);

        // we had a change of name, need to update some stuff
        if ( $ousername != $eusername ) {
            $query = "UPDATE comments SET username='$eusername' WHERE username='$ousername'";
            $result = ppmysql_query($query,$link);
            
            $query = "UPDATE photos SET user='$eusername' WHERE user='$ousername'";
            $result = ppmysql_query($query,$link);
        }
        
        $redir = "{$Globals['maindir']}/adm-users.php?ppaction=edituser&uid=$uid";
        forward( $redir, "Processing complete!" );
        exit;
    }

    if ($uid != "") {
        $months = array('January','February','March','April','May','June','July','August','September','October','November','December');

        $query = "SELECT username,usergroupid,homepage,icq,aim,yahoo,joindate,posts,birthday,location,interests,occupation,bio,email FROM users WHERE userid=$uid LIMIT 1";
        $resulta = ppmysql_query($query,$link);
        list($eusername,$usergroupid,$homepage,$icq,$aim,$yahoo,$joindate,$posts,$birthday,$location,$interests,$occupation,$bio,$email) = mysql_fetch_row($resulta);
        ppmysql_free_result($resulta);

        $birth = explode( "-", $birthday );
        $bmon = intval($birth[1]); $bday = intval($birth[2]); $byear = $birth[0];

        if ($bmon != "") $bmonsel = "<option value=\"$bmon\">".$months[$bmon-1]."</option>";
        else $bmonsel = "<option value=\"-1\"></option>";

        if ($bday != "") $bdaysel = "<option value=\"$bday\">$bday</option>";
        else $bdaysel = "<option value=\"-1\"></option>";

        if ($byear == "") $byear = "";
        if ($byear == "0000") $byear = "";

        $ppdate = formatppdate( $joindate );

        $query = "SELECT groupid,groupname from usergroups WHERE groupid=$usergroupid";
        $resulta = ppmysql_query($query,$link);
        list( $usergroupid, $groupname ) = mysql_fetch_row($resulta);
        ppmysql_free_result( $resulta );
        
        $groupopt = "<option selected value=\"$usergroupid\">$groupname</option>";

        $query = "SELECT groupid,groupname from usergroups WHERE groupid !='$usergroupid'";
        $groups = ppmysql_query($query,$link);
        while ( list( $groupid, $groupname ) = mysql_fetch_row( $groups ) ) {
            $groupopt .= "<option value=\"$groupid\">$groupname</option>";
        }
        if ( $groups ) ppmysql_free_result( $groups );
                
        $months = array('January','February','March','April','May','June','July','August','September','October','November','December');

        printheader( 0, "PhotoPost Users" );

        $output = "<center><hr>
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
            align=\"center\"><tr><td>
            <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr align=\"center\">
            <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
            size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\"
            face=\"{$Globals['mainfonts']}\"><b>PhotoPost Options</font>
            </font></td>
            </tr>
            <tr>
            <td bgcolor=\"#f7f7f7\"><b>
            <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"#000000\">$adminmenu</b></font></td></tr>
            <tr><td bgcolor=\"{$Globals['maincolor']}\"><center><br />

            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"{$Globals['bordercolor']}\"><tr><td>
            <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\">

            <tr align=\"center\">
            <td align=\"left\" colspan=\"2\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\"
            color=\"{$Globals['headfontcolor']}\"
            size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\"><b>Edit Profile for $eusername</font>
            </font></td></tr><!--PhotoPost, Copyright All Enthusiast,Inc.-->
            <form method=\"post\" action=\"{$Globals['maindir']}/adm-users.php\">
            <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">Date
            Registered</font></td><td
            bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$ppdate</font></td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"
            color=\"".$Globals['commentstext']."\">Username:</font></td><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"
            color=\"".$Globals['commentstext']."\"><input type=\"text\" name=\"eusername\" size=\"25\" maxlength=\"100\" value=\"$eusername\">
            <input type=\"hidden\" name=\"ousername\" value=\"$eusername\">
            </td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\"
            face=\"{$Globals['mainfonts']}\"
            color=\"".$Globals['commentstext']."\">Usergroup</font></td><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"
            color=\"".$Globals['commentstext']."\"><select name=\"usergroupid\">$groupopt
            </select></td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"
            color=\"".$Globals['commentstext']."\">Email</font></td><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"
            color=\"".$Globals['commentstext']."\"><input type=\"text\" name=\"email\" size=\"25\" maxlength=\"100\" value=\"$email\"></td></tr>
            <tr><td
            bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">Posts</font></td><td
            bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\"><input type=\"text\"
            name=\"posts\" size=\"10\" maxlength=\"25\" value=\"$posts\"></td></tr>";
            
        if ( $Globals['getoptional'] == "yes" ) {
            $output .= "<tr><td
                bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\"
                face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">Birthday</font></td><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\"
                face=\"{$Globals['mainfonts']}\"
                color=\"{$Globals['altcolor1']}\">
    
                <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
                <tr>
                <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\" size=\"{$Globals['fontsmall']}\" >Month</font></td>
                <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\" size=\"{$Globals['fontsmall']}\" >Day</font></td>
                <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\" size=\"{$Globals['fontsmall']}\" >Year</font></td>
                </tr>
                <tr>
                <td><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" ><select name=\"month\">
                $bmonsel";
            
            for ( $m=0; $m < 12; $m++ ) {
                $output .= "<option value=\"".($m+1)."\">".$months[$m]."</option>\n";
            }
        
            $output .= "</select></font></td>
                <td><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\"><select name=\"editday\">
                $bdaysel";
                
            for ( $x=1; $x < 32; $x++ ) {
                $output .= "<option value=\"$x\" >$x</option>\n";
            }
                    
            $output .= "</select></font></td>
                <td><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\"><input type=\"text\" name=\"year\" value=\"$byear\" size=\"{$Globals['fontlarge']}\"
                maxlength=\"4\"></font></td>
                </tr>
                </table>
    
                </td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\">Homepage:</font><br />
                </td>
                <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\"><input
                type=\"text\" name=\"homepage\" value=\"$homepage\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\">Biography:</font><br />
                </td>
                <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\"><input
                type=\"text\" name=\"bio\" value=\"$bio\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\">Location:</font><br />
                </td>
                <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\"><input
                type=\"text\" name=\"location\" value=\"$location\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\">Interests:</font><br />
                </td>
                <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\"><input
                type=\"text\" name=\"interests\" value=\"$interests\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\">ICQ:</font><br />
                </td>
                <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\"><input
                type=\"text\" name=\"icq\" value=\"$icq\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\">AIM:</font><br />
                </td>
                <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\"><input
                type=\"text\" name=\"aim\" value=\"$aim\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\">Yahoo:</font><br />
                </td>
                <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\"><input
                type=\"text\" name=\"yahoo\" value=\"$yahoo\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\">Occupation:</font><br />
                </td>
                <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"".$Globals['commentstext']."\"><input
                type=\"text\" name=\"occupation\" value=\"$occupation\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>";
        }
        
        $output .= "</table>
                </td></tr></table><p>
                <center>
                <input type=\"hidden\" name=\"ppaction\" value=\"edituser\">
                <input type=\"hidden\" name=\"do\" value=\"process\">
                <input type=\"hidden\" name=\"uid\" value=\"$uid\">
                <input type=\"submit\" value=\"Save Changes\">

                </form></td></tr></table></td></tr></table>";

        print "$output<p>{$Globals['cright']}<p>";
        printfooter();
    }
}

if ($ppaction == "deluser") {
    if ($do == "process") {
        $query = "DELETE FROM comments WHERE userid=$uid";
        $resulta = ppmysql_query($query,$db_link);

        $query = "SELECT bigimage,medsize,cat FROM photos WHERE userid=$uid";
        $resulta = ppmysql_query($query,$link);

        while ( list( $filename, $medsize, $thecat ) = mysql_fetch_row($resulta) ) {
            remove_all_files( $filename, $medsize, $uid, $thecat );
        }
        if ( $resulta ) ppmysql_free_result( $resulta );

        //# end delete the files //#

        $query = "DELETE FROM photos WHERE userid=$uid";
        $resulta = ppmysql_query($query,$link);

        $query = "DELETE FROM users WHERE userid=$uid";
        $resulta = ppmysql_query($query,$db_link);

        $forwardid = "{$Globals['maindir']}/adm-users.php?ppaction=users";
        forward( $forwardid, "Finished processing user request!" );
        exit;
    }

    //# Generate an 'are you sure' you want to delete? form...
    printheader( 0, "PhotoPost Users" );
    
    $output = "<center><hr>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\"
        face=\"{$Globals['mainfonts']}\"><b>PhotoPost Remove User</font>
        </font></td>
        </tr>
        <tr>
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"#000000\">$adminmenu</b></font></td></tr>
        <tr><td bgcolor=\"#f7f7f7\"><center><br />
        You're about to delete user \"$inusername\", and <b>ALL PHOTOS AND COMMENTS THAT HE/SHE HAS SUBMITTED</B>.<p>
        Are you sure you want to do that?
        <form action=\"{$Globals['maindir']}/adm-users.php\" method=\"post\">
        <input type=\"hidden\" name=\"uid\" value=\"$uid\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <input type=\"hidden\" name=\"ppaction\" value=\"deluser\">
        <input type=\"submit\" value=\"I'm sure, delete the user.\"></form></td></tr></table></td></tr></table>";

    print "$output<p>{$Globals['cright']}<p>";
    printfooter();    
}

?>
