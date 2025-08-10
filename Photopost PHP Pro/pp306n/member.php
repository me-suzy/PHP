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

list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
authenticate();

// Display a user's profile

if ($ppaction == "rpwd") {
    $query = "SELECT joindate,email,username FROM users WHERE userid=$uid";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_row($resulta);
    list( $dbkey, $email, $username ) = $row;
    mysql_free_result($resulta);

    $redir = $Globals{'maindir'}."/index.php";
    
    if ($verifykey == $dbkey) {
        list($dsec,$dmin,$dhour,$dmday,$dmon,$dyear,$dwday,$dyday,$disdst) = localtime();
        $timekey = mktime($dhour,$dmin,$dsec,$dmon,$dmday,$dyear);
        $npass = md5($timekey);

        $query = "UPDATE users SET password=$npass WHERE userid=$uid";
        $resulta = mysql_query_eval($query,$link);
        mysql_free_result($resulta);

        $mail_from = "From: ".$Globals{'adminemail'};
        $letter="You just requested that your password be reset at ".$Globals{'webname'}.".
        
            We have issued a you a new password.

            Your username is: $username
            Your new password is: $timekey

            If you would like to change that password, you may do so here:
            
            ".$Globals{'maindir'}."/member.php?ppaction=chgpass

            Or to edit your profle:
            
            ".$Globals{'maindir'}."/member.php?ppaction=edit

            Thanks!

            The ".$Globals{'webname'}." Team
            ".$Globals{'domain'};

        $subject="New temporary ".$Globals{'webname'}." password";

        mail( $email, $subject, $letter, $email_from );
        forward( $redir, "Your password has been reset and emailed to you." );
        exit;
    }
    else {
        forward( $redir, "No match. Check the URL and try again. [$verifykey][$dbkey][$uid]" );
        exit;
    }
}

if ($ppaction == "profile") {
    $query = "SELECT username,usergroupid,homepage,icq,aim,yahoo,joindate,posts,birthday,location,interests,occupation,bio FROM users WHERE userid=$uid LIMIT 1";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_row($resulta);
    list($username,$usergroupid,$homepage,$icq,$aim,$yahoo,$joindate,$posts,$birthday,$location,$interests,$occupation,$bio) = $row;

    $query = "SELECT id,title FROM photos WHERE userid=$uid ORDER BY date DESC LIMIT 1";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_row($resulta);
    list($phoid,$photitle) = $row;

    $query = "SELECT photo FROM comments WHERE userid=$uid ORDER BY date DESC LIMIT 1";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_array($resulta);
    $comid = $row['photo'];
    mysql_free_result($resulta);

    if ($comid != "" ) {
        $query = "SELECT id,title FROM photos WHERE id=$comid ORDER BY date DESC LIMIT 1";
        $resulta = mysql_query_eval($query,$link);
        $row = mysql_fetch_row($resulta);
        list($comphoid,$comphotitle) = $row;
    }
    else {
        $comphotitle = "";
        $comphoid = -1;
    }

    list($jsec,$jmin,$jhour,$jmday,$jmon,$jyear,$jwday,$jyday,$jisdst) = localtime($joindate);

    $jmon++;
    $jyear=1900+$jyear;

    $query = "SELECT groupname from usergroups WHERE groupid=$usergroupid";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_array($resulta);
    $usergroup = $row['groupname'];
    mysql_free_result($resulta);

    topmenu();

    $output = "$header$toplinks
        <Center><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" height=\"40\" width=\"".$Globals{'tablewidth'}."\"><Tr>
        <Td valign=\"center\" width=\"25%\">&nbsp;$childnav</td>
        <td width=\"75%\" align=\"right\" valign=\"center\"><font face=\"verdana, arial\" size=\"2\">$menu&nbsp;</font></td></tr></table>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"
        width=\"".$Globals{'tablewidth'}."\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"4\"
        face=\"verdana\">".$Globals{'galleryname'}."</font>
        </font></td></tr><!--PhotoPost, Copyright All Enthusiast,Inc.-->

        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\" face=\"verdana\"><b>Profile for $username</font>
        </font></td></tr><!-- CyKuH [WTN] -->

        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Date Registered</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\">$jmon-$jmday-$jyear</font></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Posts</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">$posts</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Status</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">$usergroup</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Last Photo Uploaded</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><A href=\"".$Globals{'maindir'}."/showphoto.php?photo=$phoid\">$photitle</a></font></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Last Comment Posted</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><A href=\"".$Globals{'maindir'}."/showphoto.php?photo=$comphoid\">$comphotitle</a></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Birthday</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">$birthday</font></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Homepage</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><a href=\"$homepage\">$homepage</a></font></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">ICQ</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">$icq</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">AIM</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">$aim</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Yahoo</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">$yahoo</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Location</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">$location</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Interests</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">$interests</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Occupation</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">$occupation</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">About Me</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">$bio</td></tr>

        </table></td></tr></table>";

    print "$output<p>".$Globals{'cright'}."$footer";
}

if ( $ppaction == "forgot" ) {
    if ( $do == "process" ) {
        $query = "SELECT username,userid,joindate FROM users WHERE email='$inemail'";
        $resultb = mysql_query_eval($query,$link);
        $checkrows = mysql_num_rows($resultb);

        while( $row = mysql_fetch_row($resultb) ) {
            list( $dbuser, $dbuid, $joindate ) = $row;

            $email_from = "From: ".$Globals{'adminemail'};
            $letter="You just requested that your password be reset at ".$Globals{'webname'}.".
            
                In order to do so, you must click on the link below or copy it into your web browser:
                
                ".$Globals{'maindir'}."/member.php?ppaction=rpwd&uid=$dbuid&key=$joindate

                Thanks!

                The ".$Globals{'webname'}." Team
                ".$Globals{'domain'};

            $subject="How to reset your ".$Globals{'webname'}." password";
            mail( $inemail, $subject, $letter, $email_from );
        }

        if ($checkrows > 0) {
            $redir = $Globals{'maindir'}."/index.php";
            forward( $redir, "Please check your email for instructions." );
            exit;
        }
        else {
            $message = "<form size=\"2\" face=\"verdana\" color=\"".$Globals{'maintext'}."\">That address was not found in our records.</font>";
        }
    }

    $output = "<head><title>".$Globals{'galleryname'}." Login</title></head>$header<p>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">

        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"verdana,arial,helvetica\"
        color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font
        size=\"4\"
        face=\"verdana\">".$Globals{'galleryname'}." Login</font>
        </font></td></tr></table></td></tr></table><p><Center>$message<p>";

    $login = "<p><FORM ACTION=\"".$Globals{'maindir'}."/member.php\" METHOD=\"POST\">
        <TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\">
        <TR><TD BGCOLOR=\"".$Globals{'bordercolor'}."\">
        <TABLE BORDER=\"0\" CELLPADDING=\"10\" CELLSPACING=\"1\" WIDTH=\"100%\">
        <TR BGCOLOR=\"".$Globals{'headcolor'}."\">
        <TD COLSPAN=\"3\">
        <FONT FACE=\"sans-serif\" SIZE=\"2\" COLOR=\"".$Globals{'headfontcolor'}."\">
        <b>Reset Password Form</b>
        </FONT>
        <br>
        </TD>
        </TR><TR BGCOLOR=\"".$Globals{'maincolor'}."\">
        <TD>
        <FONT FACE=\"sans-serif\" SIZE=\"2\" COLOR=\"".$Globals{'maintext'}."\">
        <b>Your Email Address: &nbsp;</b></font>
        </FONT>
        </TD>
        <TD>
        <INPUT TYPE=\"TEXT\" NAME=\"inemail\" SIZE=\"25\" MAXstrlen=\"40\" VALUE=\"\"></td>
        </TR>
        <TR BGCOLOR=\"".$Globals{'maincolor'}."\">
        <TD COLSPAN=\"3\" ALIGN=\"CENTER\">
        <input type=\"hidden\" name=\"ppaction\" value=\"forgot\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <INPUT TYPE=\"SUBMIT\" NAME=\"submit\"
        VALUE=\"Submit\">
        </TD>
        </TR>
        </TABLE>
        </TD></TR>
        </TABLE>
        </FORM>

        </td></tr></table></td></tr></table>";

    print "$output$login".$Globals{'cright'}."$footer";
}


if ( $ppaction == "chgpass" ) {
    if ( $gologin == 1 ) {
        $furl = $Globals{'maindir'};
        $furl = str_replace( $Globals{'domain'}, "", $furl );
        $furl = "$furl/member.php?ppaction=chgpass";
        login($furl);
        exit;
    }

    if ( $do == "process" ) {
        $reason = "";

        if ( empty($oldpassword) ) {
            $reason .= "<li>The old password is blank.";
            $stop = 1;
        }
        if ( empty($newpassword) ) {
            $reason .= "<li>The new password is blank.";
            $stop = 1;
        }
        if ( empty($cnewpassword) ) {
            $reason .= "<li>The confirm new password field is blank.";
            $stop = 1;
        }
        if ($newpassword != $cnewpassword) {
            $reason .= "<li>The new password field does not match the confirmation password field.";
            $stop = 1;
        }

        if ($newpassword != "") {
            $pwdstrlen = strlen($newpassword);
            if ($pwdstrlen < 4) {
                $reason .= "<li>Your password must be at least 4 characters long.";
                $stop = 1;
            }
        }

        $query = "SELECT password FROM users WHERE userid=$cookuser LIMIT 1";
        $resulta = mysql_query_eval($query, $link);
        $row = mysql_fetch_array($resulta);
        $dbpwd = $row['password'];
        mysql_free_result($resulta);

        $oldpassword = md5($oldpassword);
        if ($oldpassword != $dbpwd) {
            $reason .= "<li>Your old password is not correct.";
            $stop = 1;
        }

        if ($stop == 1) {
            dieWell($reason);
        }

        $newpassword = md5_hex($newpassword);
        $query = "UPDATE users SET password='$newpassword' WHERE userid=$cookuser";
        $resulta = mysql_query_eval($query,$link);
        mysql_free_result($resulta);

        $redirc = $Globals{'maindir'}."/index.php";
        forward( $redirc, "Password changed" );
    }

    topmenu();

    $output = "$header
        <Center><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" height=\"40\" width=\"".$Globals{'tablewidth'}."\"><Tr>
        <Td valign=\"center\" width=\"25%\">&nbsp;</td>
        <td width=\"75%\" align=\"right\" valign=\"center\"><font face=\"verdana, arial\" size=\"2\">$menu&nbsp;</font></td></tr></table>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"100%\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"4\"
        face=\"verdana\">".$Globals{'galleryname'}."</font>
        </font></td></tr><!-- CyKuH [WTN] -->

        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\"
        color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\" face=\"verdana\"><b>Password Change Form</font>
        </font></td></tr><!-- CyKuH [WTN] -->
        <form method=\"POST\" action=\"".$Globals{'maindir'}."/member.php\">
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><b>Old
        Password:</font></td><td
        bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">
        <input type=\"password\" name=\"oldpassword\" maxstrlen=\"100\"></font></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><b>New
        Password:</font></td><td
        bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><input type=\"password\"
        name=\"newpassword\" maxstrlen=\"100\">
        </font></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\"><b>Confirm New Password:</font></td><td
        bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><input type=\"password\"
        name=\"cnewpassword\" maxstrlen=\"100\">
        </font></td></tr>
        </table></td></tr></table><p>
        <input type=\"hidden\" name=\"ppaction\" value=\"chgpass\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <Center><input type=\"submit\" name=\"submit\" value=\"Change Password\"></center>

        </td></tr></table></td></tr></table><p>";

    print "$output".$Globals{'cright'}."$footer";
}

// Edit a user's profile (form)

if ($ppaction == "edit") {
    if ( $gologin == 1 ){
        $furl = $Globals{'maindir'};
        $furl = str_replace( $Globals{'domain'}, "", $furl );
        $furl = "$furl/member.php?ppaction=edit";
        login($furl);
        exit;
    }

    $uid = $userid;
    if ($adminedit == 1) {
        if ($uid == "") {
            $uid = $cookuser;
        }
    }

    $months = array('January','February','March','April','May','June','July','August','September','October','November','December');

    $query = "SELECT username,usergroupid,homepage,icq,aim,yahoo,joindate,posts,birthday,location,interests,occupation,bio,email FROM users WHERE userid=$uid LIMIT 1";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_row($resulta);
    list($username,$usergroupid,$homepage,$icq,$aim,$yahoo,$joindate,$posts,$birthday,$location,$interests,$occupation,$bio,$email) = $row;

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

    $query = "SELECT groupname from usergroups WHERE groupid=$usergroup";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_array($resulta);
    $usergroup = $row['groupname'];

    topmenu();

    $output = "$header
        <Center><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" height=\"40\" width=\"".$Globals{'tablewidth'}."\"><Tr>
        <Td valign=\"center\" width=\"25%\">&nbsp;</td>
        <td width=\"75%\" align=\"right\" valign=\"center\"><font face=\"verdana, arial\" size=\"2\">$menu&nbsp;</font></td></tr></table>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"
        width=\"".$Globals{'tablewidth'}."\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"4\"
        face=\"verdana\">".$Globals{'galleryname'}."</font>
        </font></td></tr><!-- CyKuH [WTN] -->

        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\" face=\"verdana\"><b>Edit Profile for $username</font>
        </font></td></tr><!-- CyKuH [WTN] -->
        <form method=\"post\" action=\"".$Globals{'maindir'}."/member.php\">
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Date Registered</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\">$jmon-$jmday-$jyear</font></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Status</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">$usergroup</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\">Password</font></td><td
        bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">[ <a
        href=\"".$Globals{'maindir'}."/member.php?ppaction=chgpass\">Change
        Password</a> ]</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Email</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><input type=\"text\" name=\"editemail\" size=\"25\" maxstrlen=\"100\" value=\"$email\"></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Email Confirmation</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><input type=\"text\" name=\"editemailconfirm\" size=\"25\" maxstrlen=\"100\" value=\"$email\"></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Birthday</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">
        <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
        <tr>
        <td align=\"center\"><font face=\"verdana,arial,helvetica\" size=\"1\" >Month</font></td>
        <td align=\"center\"><font face=\"verdana,arial,helvetica\" size=\"1\" >Day</font></td>
        <td align=\"center\"><font face=\"verdana,arial,helvetica\" size=\"1\" >Year</font></td>
        </tr>
        <tr>
        <td><font face=\"verdana,arial,helvetica\" size=\"1\" ><select name=\"editmonth\">
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
        <td><font face=\"verdana,arial,helvetica\" size=\"1\"><select name=\"editday\">
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
        <td><font face=\"verdana,arial,helvetica\" size=\"1\"><input type=\"text\" name=\"edityear\" value=\"$byear\"
        size=\"4\" maxstrlen=\"4\"></font></td>
        </tr>
        </table>

        </font></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Homepage</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><input type=\"text\" name=\"edithomepage\" size=\"25\" maxstrlen=\"100\" value=\"$homepage\"></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">ICQ</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><input type=\"text\" name=\"editicq\" size=\"25\" maxstrlen=\"20\" value=\"$icq\"></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">AIM</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><input type=\"text\" name=\"editaim\" size=\"25\" maxstrlen=\"20\" value=\"$aim\"></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Yahoo</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><input type=\"text\" name=\"edityahoo\" size=\"25\" maxstrlen=\"20\" value=\"$yahoo\"></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Location</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><input type=\"text\" name=\"editlocation\" size=\"25\" maxstrlen=\"250\" value=\"$location\"></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Interests</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><input type=\"text\" name=\"editinterests\" size=\"25\" maxstrlen=\"250\" value=\"$interests\"></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Occupation</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><input type=\"text\" name=\"editoccupation\" size=\"25\" maxstrlen=\"250\" value=\"$occupation\"></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">About Me</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\"><input type=\"text\" name=\"editbio\" size=\"25\" maxstrlen=\"250\" value=\"$bio\"></td></tr>

        </table></td></tr></table><Br>
        <center>
        <input type=\"hidden\" name=\"uid\" value=\"$uid\">
        <input type=\"hidden\" name=\"ppaction\" value=\"processedit\">
        <input type=\"submit\" value=\"Save Changes\"></form>";

    print "$output".$Globals{'cright'}."$footer";
}

// Process a user's edit, forward to profile display

if ($ppaction == "processedit") {
    if ($gologin==1){
        $furl=$Globals{'maindir'};
        $furl= str_replace( $Globals{'domain'}, "", $furl );
        $furl="$furl/member.php?ppaction=edit";
        login($furl);
        exit;
    }

    $redir = $Globals{'maindir'}."/member.php?ppaction=profile&uid=$uid";
    if ($adminedit == 0) {
        if ($uid != $userid) {
            dieWell( "You don't have permission to edit this profile." );
            exit;
        }
    }

    $email=$editemail;
    $emailconfirm=$editemailconfirm;
    $homepage=$edithomepage;
    $icq=$editicq;
    $aim=$editaim;
    $yahoo=$edityahoo;
    $bio=$editbio;
    $hobbies=$editinterests;
    $occupation=$editoccupation;
    $location=$editlocation;
    $day=$editday;
    $month=$editmonth;
    $year=$edityear;

    if ($year == "") $year="0000";
    if ($month == "") $month="0";
    if ($day == "") $day="0";

    $birthday="$year-$month-$day";
    $homepage = fixstring($homepage);
    $icq = fixstring( $icq );
    $aim = fixstring( $aim );
    $yahoo = fixstring( $yahoo );
    $year = fixstring( $year );
    $hobbies = fixstring( $hobbies );
    $occupation = fixstring( $occupation );
    $location = fixstring( $location );

    if ($email != $emailconfirm) {
        dieWell("The email field must match the email confirmation field.");
        exit;
    }

    if ($Globals{'emailverify'} == "yes") {
        // Check to see if user changed email. Verify it if needed.
        $query = "SELECT email,username FROM users WHERE userid=$uid LIMIT 1";
        $resulta = mysql_query_eval($query,$link);
        $row = mysql_fetch_array($resulta);
        $emaildb = $row['email']; $username = $row['username'];

        if ($email != $emaildb) {
            list($dsec,$dmin,$dhour,$dmday,$dmon,$dyear,$dwday,$dyday,$disdst) = localtime();
            $nowtime=localtime($dsec,$dmin,$dhour,$dmday,$dmon,$dyear);

            $nowpass=md5_hex($nowtime);
            $query = "UPDATE users SET password='$nowpass' WHERE userid=$uid";
            $resulta = mysql_query_eval($query,$link);
            mysql_free_result($resulta);

            $email_from = "From: ".$Globals{'adminemail'};
            $letter="You just changed your email address at ".$Globals{'webname'}.".
                We have issued a you a new temporary password in order to confirm
                your new email address.

                Your New Temporary Password is: $nowtime

                If you would like to change that password, you may do so here:
                ".$Globals{'maindir'}."/member.php?ppaction=chgpass

                Thanks!

                The ".$Globals{'webname'}." Team
                ".$Globals{'domain'};

            $subject="New temporary ".$Globals{'webname'}." password";
            mail( $email, $subject, $letter, $email_from );
        }
    }

    // Write input data to db
    $query = "UPDATE users SET email='$email',homepage='$homepage',icq='$icq',aim='$aim',yahoo='$yahoo',birthday='$birthday',interests='$hobbies',occupation='$occupation',bio='$bio',location='$location' WHERE userid=$uid";
    $resulta = mysql_query_eval($query,$link);

    forward( $redir, "Profile updated." );
}

?>

