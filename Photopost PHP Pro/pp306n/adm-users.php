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
        $nusers = mysql_query_eval($query,$link);
        $rcount = mysql_num_rows($nusers);

        pagesystem( $rcount );

        $output = "$header<center><hr>
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\" width=\"".$Globals{'tablewidth'}."\"
            align=\"center\"><tr><td>
            <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr align=\"center\">
            <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
            size=\"1\"><font size=\"2\"
            face=\"verdana\"><B>PhotoPost Select Users</font>
            </font></td>
            </tr>
            <tr id=\"cat\">
            <td bgcolor=\"#f7f7f7\"><b>
            <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$adminmenu</b></font></td></tr>
            <tr><td bgcolor=\"".$Globals{'maincolor'}."\"><center><Br>

            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"><tr><Td>
            <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\"><Tr>
            <Th bgcolor=\"".$Globals{'headcolor'}."\"><font
            size=\"2\" color=\"".$Globals{'headfontcolor'}."\" face=\"verdana\">Username</th>
            <Th bgcolor=\"".$Globals{'headcolor'}."\"><font
            size=\"2\" color=\"".$Globals{'headfontcolor'}."\" face=\"verdana\">Actions</th>
            <Th bgcolor=\"".$Globals{'headcolor'}."\"><font
            size=\"2\" color=\"".$Globals{'headfontcolor'}."\" face=\"verdana\">Email</th>
            <Th bgcolor=\"".$Globals{'headcolor'}."\"><font
            size=\"2\" color=\"".$Globals{'headfontcolor'}."\" face=\"verdana\">Posts</th>
            </tr>";

        $query = "SELECT userid,username,joindate,posts,email FROM users $srch LIMIT $startnumb,$perpage";
        $fusers = mysql_query_eval($query,$link);
        $posts = mysql_num_rows($fusers);

        while ( $row = mysql_fetch_row($fusers) ) {
            list( $euserid,$eusername,$joindate,$posts,$email ) = $row;

            $output .= "<tr>
                <Td bgcolor=\"#FFFFFF\"<font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">$eusername</font></td>
                <Td bgcolor=\"#FFFFFF\"<font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\"><center>[ <a
                href=\"".$Globals{'maindir'}."/adm-users.php?ppaction=edituser&uid=$euserid\">Edit User</a> ] [ <a
                href=\"".$Globals{'maindir'}."/adm-users.php?ppaction=deluser&uid=$euserid&inusername=$eusername\">Delete User</a> ] [ <a
                target=\"_blank\"
                href=\"".$Globals{'maindir'}."/member.php?ppaction=rpwd&uid=$euserid&key=$joindate\">Reset Password</a> ]</font></td>
                <Td bgcolor=\"#FFFFFF\"<font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">$email</font></td>
                <Td bgcolor=\"#FFFFFF\"<font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\"><center>$posts</center></font></td></tr>";
        }

        $output .= "<Tr><Td
            colspan=\"4\" bgcolor=\"#FFFFFF\">$posternav</td></tr></table></td></tr></table></td></tr></table></td></tr></table>";

        if ($rcount > 0) {
            print "$output<p>".$Globals{'cright'}."<p>$footer";
            exit;
        }
        else {
            $message = "<font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">No users found. Please try an alternate search, or list
                all users.</font><p>";
        }
    }

    if ( $susergroupid != "" ) {
        $query="SELECT groupname from usergroups WHERE groupid=$susergroupid";
        $resultb = mysql_query_eval($query,$link);
        $gname = mysql_fetch_array($resultb);
        $usergroup = $gname['groupname'];
    }

    if ($do == "findusers") {
        $groupopt = "<option value=\"$susergroupid\">$usergroup</option><option></option>";
    }
    else {
        $groupopt = "<option></option>";
        $eusername="";
    }

    $query = "SELECT userid from users";
    $nusers = mysql_query_eval($query,$link);
    $numusers = mysql_num_rows($nusers);

    $query = "SELECT groupid,groupname from usergroups";
    $groups = mysql_query_eval($query,$link);
    while ( $row = mysql_fetch_row( $groups ) ) {
        list( $groupid, $ugusergroup ) = $row;
        $groupopt .= "<option value=\"$groupid\">$ugusergroup</option>";
    }

    $output = "$header<center><hr>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\"
        face=\"verdana\"><B>PhotoPost Select Users</font>
        </font></td>
        </tr>
        <tr id=\"cat\">
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$adminmenu</b></font></td></tr>
        <tr><td bgcolor=\"".$Globals{'maincolor'}."\"><center><Br>
        $message<p><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">
        <A href=\"".$Globals{'maindir'}."/adm-users.php?ppaction=users&do=findusers\">Click to list all $numusers users</a> or use the
        advanced search
        box below.<p>

        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"><tr><Td>
        <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\">
        <form method=\"post\" action=\"".$Globals{'maindir'}."/adm-users.php\"><Tr><Th bgcolor=\"".$Globals{'headcolor'}."\" colspan=\"2\"><font
        size=\"2\"
        color=\"".$Globals{'headfontcolor'}."\" face=\"verdana\">Select users where: (leave a field blank to ignore
        it)</td></tr>
        <Tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Username contains:</td><td bgcolor=\"#FFFFFF\"><input type=\"text\"
        value=\"$eusername\" name=\"susername\"></td></tr>
        <Tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">and email contains:</td><td bgcolor=\"#FFFFFF\"><input type=\"text\"
        value=\"$email\" name=\"email\"></td></tr>
        <Tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">and usergroup is:</td><td bgcolor=\"#FFFFFF\"><select
        name=\"susergroupid\">$groupopt
        </select></td></tr>
        </table></td></tr></table><p>
        <input type=\"hidden\" name=\"ppaction\" value=\"users\">
        <input type=\"hidden\" name=\"do\" value=\"findusers\">
        <input type=\"submit\" value=\"Find users\">
        </td></tr></table></td></tr></table>";

    print "$output<p>".$Globals{'cright'}."<p>$footer";
}


if ($ppaction == "edituser") {
    if ($do == "process") {
        if ($year == "") {
            $year="0000";
        }

        if ($month == "") {
            $month="0";
        }

        if ($day == "") {
            $day="0";
        }

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
        $resulta = mysql_query_eval($query,$link);

        $redir = $Globals{'maindir'}."/adm-users.php?ppaction=edituser&uid=$uid";
        forward( $redir, "Processing complete!" );
        exit;
    }

    if ($uid != "") {
        $months = array('January','February','March','April','May','June','July','August','September','October','November','December');

        $query = "SELECT username,usergroupid,homepage,icq,aim,yahoo,joindate,posts,birthday,location,interests,occupation,bio,email FROM users WHERE userid=$uid LIMIT 1";
        $resulta = mysql_query_eval($query,$link);
        $row = mysql_fetch_row($resulta);
        list($eusername,$usergroupid,$homepage,$icq,$aim,$yahoo,$joindate,$posts,$birthday,$location,$interests,$occupation,$bio,$email) = $row;
        mysql_free_result($resulta);

        $birth = explode( "-", $birthday );
        $bmon = intval($birth[1]); $bday = intval($birth[2]); $byear = $birth[0];

        if ($bmon != "") $bmonsel = "<option value=\"$bmon\">".$months[$bmon-1]."</option>";
        else $bmonsel = "<option value=\"-1\"></option>";

        if ($bday != "") $bdaysel = "<option value=\"$bday\">$bday</option>";
        else $bdaysel = "<option value=\"-1\"></option>";

        if ($byear == "") $byear = "";
        if ($byear == "0000") $byear = "";

        list($jsec,$jmin,$jhour,$jmday,$jmon,$jyear,$jwday,$jyday,$jisdst) = localtime($joindate);
        $jmon++;
        $jyear=1900+$jyear;

        $query = "SELECT groupid,groupname from usergroups WHERE groupid=$usergroupid";
        $resulta = mysql_query_eval($query,$link);
        $row = mysql_fetch_row($resulta);
        list( $usergroupid, $groupname ) = $row;
        $groupopt = "<option selected value=\"$usergroupid\">$groupname</option>";

        $query = "SELECT groupid,groupname from usergroups WHERE groupid !='$usergroupid'";
        $groups = mysql_query_eval($query,$link);
        while ( $row = mysql_fetch_row( $groups ) ) {
            list( $groupid, $groupname ) = $row;
            $groupopt .= "<option value=\"$groupid\">$groupname</option>";
        }

        $output = "$header<center><hr>
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
            align=\"center\"><tr><td>
            <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr align=\"center\">
            <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
            size=\"1\"><font size=\"2\"
            face=\"verdana\"><B>PhotoPost Options</font>
            </font></td>
            </tr>
            <tr id=\"cat\">
            <td bgcolor=\"#f7f7f7\"><b>
            <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$adminmenu</b></font></td></tr>
            <tr><td bgcolor=\"".$Globals{'maincolor'}."\"><center><Br>

            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"><tr><Td>
            <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\">

            <tr align=\"center\">
            <td align=\"left\" colspan=\"2\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\"
            color=\"".$Globals{'headfontcolor'}."\"
            size=\"1\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\"><b>Edit Profile for $eusername</font>
            </font></td></tr><!-- CyKuH [WTN] -->
            <form method=\"post\" action=\"".$Globals{'maindir'}."/adm-users.php\">
            <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Date
            Registered</font></td><td
            bgcolor=\"".$Globals{'altcolor1'}."\">$jmon-$jmday-$jyear</font></td></tr>
            <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\"
            color=\"".$Globals{'commentstext'}."\">Username:</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\"
            color=\"".$Globals{'commentstext'}."\"><input type=\"text\" name=\"eusername\" size=\"25\" maxlength=\"100\" value=\"$eusername\"></td></tr>
            <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\"
            face=\"verdana\"
            color=\"".$Globals{'commentstext'}."\">Status</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\"
            color=\"".$Globals{'commentstext'}."\"><select name=\"usergroupid\">$groupopt
            </select></td></tr>
            <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\"
            color=\"".$Globals{'commentstext'}."\">Email</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\"
            color=\"".$Globals{'commentstext'}."\"><input type=\"text\" name=\"email\" size=\"25\" maxlength=\"100\" value=\"$email\"></td></tr>
            <tr><td
            bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Posts</font></td><td
            bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><input type=\"text\"
            name=\"posts\" size=\"10\" maxlength=\"25\" value=\"$posts\"></td></tr> <tr><td
            bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\"
            face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Birthday</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\"
            face=\"verdana\"
            color=\"".$Globals{'altcolor1'}."\">

            <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
            <tr>
            <td align=\"center\"><font face=\"verdana,arial,helvetica\" size=\"1\" >Month</font></td>
            <td align=\"center\"><font face=\"verdana,arial,helvetica\" size=\"1\" >Day</font></td>
            <td align=\"center\"><font face=\"verdana,arial,helvetica\" size=\"1\" >Year</font></td>
            </tr>
            <tr>
            <td><font face=\"verdana,arial,helvetica\" size=\"1\" ><select name=\"month\">
            $bmonsel
            <option value=\"1\" >January</option>
                        <option value=\"2\" >February</option>
                        <option value=\"3\" >March</option>
                        <option value=\"4\" >April</option>
                        <option value=\"5\" >May</option>
                        <option value=\"6\" >June</option>
                        <option value=\"7\" >July</option>
                        <option value=\"8\" >August</option>
                        <option value=\"9\" >September</option>
                        <option value=\"10\" >October</option>
                        <option value=\"11\" >November</option>
                        <option value=\"12\" >December</option>
            </select></font></td>
            <td><font face=\"verdana,arial,helvetica\" size=\"1\"><select name=\"day\">
            $bdaysel
                        <option value=\"1\" >1</option>
                        <option value=\"2\" >2</option>
                        <option value=\"3\" >3</option>
                        <option value=\"4\" >4</option>
                        <option value=\"5\" >5</option>
                        <option value=\"6\" >6</option>
                        <option value=\"7\" >7</option>
                        <option value=\"8\" >8</option>
                        <option value=\"9\" >9</option>
                        <option value=\"10\" >10</option>
                        <option value=\"11\" >11</option>
                        <option value=\"12\" >12</option>
                        <option value=\"13\" >13</option>
                        <option value=\"14\" >14</option>
                        <option value=\"15\" >15</option>
                        <option value=\"16\" >16</option>
                        <option value=\"17\" >17</option>
                        <option value=\"18\" >18</option>
                        <option value=\"19\" >19</option>
                        <option value=\"20\" >20</option>
                        <option value=\"21\" >21</option>
                        <option value=\"22\" >22</option>
                        <option value=\"23\" >23</option>
                        <option value=\"24\" >24</option>
                        <option value=\"25\" >25</option>
                        <option value=\"26\" >26</option>
                        <option value=\"27\" >27</option>
                        <option value=\"28\" >28</option>
                        <option value=\"29\" >29</option>
                        <option value=\"30\" >30</option>
                        <option value=\"31\" >31</option>
                </select></font></td>
                <td><font face=\"verdana,arial,helvetica\" size=\"1\"><input type=\"text\" name=\"year\" value=\"$byear\" size=\"4\"
                maxlength=\"4\"></font></td>
                </tr>
                </table>

                </td>
                </tr>
                <tr>
                <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\">Homepage:</font><br>
                </td>
                <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\"><input
                type=\"text\" name=\"homepage\" value=\"$homepage\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\">Biography:</font><br>
                </td>
                <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\"><input
                type=\"text\" name=\"bio\" value=\"$bio\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\">Location:</font><br>
                </td>
                <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\"><input
                type=\"text\" name=\"location\" value=\"$location\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\">Interests:</font><br>
                </td>
                <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\"><input
                type=\"text\" name=\"interests\" value=\"$interests\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\">ICQ:</font><br>
                </td>
                <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\"><input
                type=\"text\" name=\"icq\" value=\"$icq\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\">AIM:</font><br>
                </td>
                <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\"><input
                type=\"text\" name=\"aim\" value=\"$aim\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\">Yahoo:</font><br>
                </td>
                <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\"><input
                type=\"text\" name=\"yahoo\" value=\"$yahoo\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\">Occupation:</font><br>
                </td>
                <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'commentstext'}."\"><input
                type=\"text\" name=\"occupation\" value=\"$occupation\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                </table>
                </td></tr></table><p>
                <center>
                <input type=\"hidden\" name=\"ppaction\" value=\"edituser\">
                <input type=\"hidden\" name=\"do\" value=\"process\">
                <input type=\"hidden\" name=\"uid\" value=\"$uid\">
                <input type=\"submit\" value=\"Save Changes\">

                </form>";

        print "$output<p>".$Globals{'cright'}."<p>$footer";
    }
}

if ($ppaction == "deluser") {  //# Delete a user and users' posts/comments
    if ($do == "process") { //# Process delete user
        $query = "DELETE FROM comments WHERE userid=$uid";
        $resulta = mysql_query_eval($query,$db_link);

        $query = "SELECT bigimage,medsize,cat FROM photos WHERE userid=$uid";
        $resulta = mysql_query_eval($query,$link);

        while ( $row = mysql_fetch_row($resulta) ) {
            list( $filename, $medsize, $thecat ) =$row;
            remove_all_files( $filename, $medsize, $uid, $thecat );
        }

        //# end delete the files //#

        $query = "DELETE FROM photos WHERE userid=$uid";
        $resulta = mysql_query_eval($query,$link);

        $query = "DELETE FROM users WHERE userid=$uid";
        $resulta = mysql_query_eval($query,$db_link);

        $forwardid = $Globals{'maindir'}."/adm-users.php?ppaction=users";
        forward( $forwardid, "Finished processing user request!" );
        exit;
    }

    //# Generate an 'are you sure' you want to delete? form...
    $output = "$header<center><hr>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".".$Globals{'bordercolor'}."."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\"
        face=\"verdana\"><B>PhotoPost Remove User</font>
        </font></td>
        </tr>
        <tr id=\"cat\">
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$adminmenu</b></font></td></tr>
        <tr><td bgcolor=\"#f7f7f7\"><center><Br>
        You're about to delete user \"$inusername\", and <b>ALL PHOTOS AND COMMENTS THAT HE/SHE HAS SUBMITTED</B>.<p>
        Are you sure you want to do that?
        <form action=\"".$Globals{'maindir'}."/adm-users.php\" method=\"post\">
        <input type=\"hidden\" name=\"uid\" value=\"$uid\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <input type=\"hidden\" name=\"ppaction\" value=\"deluser\">
        <input type=\"submit\"
        value=\"I'm sure, delete the user.\"></form></td></tr></table></td></tr></table>";

    print "$output<p>".$Globals{'cright'}."<p>$footer";
}

?>

