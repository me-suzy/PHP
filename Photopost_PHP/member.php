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
require "languages/$pplang/member.php";
require "login-inc.php";

list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
authenticate();

// Display a user's profile

if ($ppaction == "rpwd") {
    $query = "SELECT joindate,email,username FROM users WHERE userid=$uid";
    $resulta = ppmysql_query($query,$link);
    list( $dbkey, $email, $username ) = mysql_fetch_row($resulta);
    ppmysql_free_result($resulta);

    $redir = "{$Globals['maindir']}/index.php";
    
    if ($verifykey == $dbkey) {
        
        $newpass = gen_password();
        $npass = md5($newpass);

        $query = "UPDATE users SET password='$npass' WHERE userid=$uid";
        $resulta = ppmysql_query($query,$link);

        $mail_from = "From: {$Globals['adminemail']}";
        $letter="You just requested that your password be reset at {$Globals['webname']}.
        
We have issued a you a new password.

Your username is: $username
Your new password is: $newpass

If you would like to change that password, you may do so here:

{$Globals['maindir']}/member.php?ppaction=chgpass

Or to edit your profle:

{$Globals['maindir']}/member.php?ppaction=edit

Thanks!

The {$Globals['webname']} Team
".$Globals['domain'];

        $subject="New temporary {$Globals['webname']} password";

        mail( $email, $subject, $letter, $email_from );
        
        if ( isset($adminreset) ) {
            diewell( $Globals['pp_lang']['ureset'] );
            exit;
        }
        
        forward( $redir, $Globals['pp_lang']['preset'] );
        exit;
    }
    else {
        forward( $redir, $Globals['pp_lang']['nomatch'] );
        exit;
    }
}

if ($ppaction == "profile") {
    $query = "SELECT username,usergroupid,homepage,icq,aim,yahoo,joindate,posts,birthday,location,interests,occupation,bio,offset FROM users WHERE userid=$uid LIMIT 1";
    $resulta = ppmysql_query($query,$link);
    list( $username,$usergroupid,$homepage,$icq,$aim,$yahoo,$joindate,$posts,$birthday,$location,$interests,$occupation,$bio,$offset ) = mysql_fetch_row($resulta);
    ppmysql_free_result($resulta);

    $query = "SELECT id,title FROM photos WHERE userid=$uid ORDER BY date DESC LIMIT 1";
    $resulta = ppmysql_query($query,$link);
    list( $phoid, $photitle ) = mysql_fetch_row($resulta);
    ppmysql_free_result($resulta);

    $query = "SELECT photo FROM comments WHERE userid=$uid ORDER BY date DESC LIMIT 1";
    $resulta = ppmysql_query($query,$link);
    list( $comid ) = mysql_fetch_row($resulta);
    ppmysql_free_result($resulta);

    if ($comid != "" ) {
        $query = "SELECT id,title FROM photos WHERE id=$comid ORDER BY date DESC LIMIT 1";
        $resulta = ppmysql_query($query,$link);
        list($comphoid,$comphotitle) = mysql_fetch_row($resulta);
        ppmysql_free_result($resulta);
    }
    else {
        $comphotitle = "";
        $comphoid = -1;
    }

    list($jsec,$jmin,$jhour,$jmday,$jmon,$jyear,$jwday,$jyday,$jisdst) = localtime($joindate);

    $jmon++;
    $jyear=1900+$jyear;

    $query = "SELECT groupname from usergroups WHERE groupid=$usergroupid";
    $resulta = ppmysql_query($query,$link);
    list( $usergroup ) = mysql_fetch_row($resulta);
    ppmysql_free_result($resulta);

    topmenu();
    
    printheader( 0, $Globals['pp_lang']['memprofile'] );

    $output = "<br /><table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td>
        <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals['bordercolor']."\"
        width=\"{$Globals['tablewidth']}\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontlarge']}\"
        face=\"{$Globals['mainfonts']}\">".$Globals['galleryname']."</font>
        </font></td></tr><!--PhotoPost, Copyright All Enthusiast,Inc.-->

        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['profilefor']} $username</font>
        </font></td></tr><!--PhotoPost, Copyright All Enthusiast,Inc.-->

        <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['datereg']}</font></td>
        <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$jmon-$jmday-$jyear</font></td></tr>
        <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['posts']}</font></td>
        <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$posts</font></td></tr>
        <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['status']}</font></td>
        <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$usergroup</font></td></tr>
        <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['lastphotoup']}</font></td>
        <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\"><a href=\"{$Globals['maindir']}/showphoto.php?photo=$phoid\">$photitle</a></font></td></tr>
        <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['lastcommentpo']}</font></td>
        <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\"><a href=\"{$Globals['maindir']}/showphoto.php?photo=$comphoid\">$comphotitle</a></td></tr>";
        
    if ( $Globals['getoptional'] == "yes" ) {        
        $output .= "<tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['bday']}</font></td>
            <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$birthday</font></td></tr>        
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['homepage']}</font></td>
            <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\"><a href=\"$homepage\">$homepage</a></font></td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['icq']}</font></td>
            <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$icq</font></td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['aim']}</font></td>
            <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$aim</font></td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['yahoo']}</font></td>
            <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$yahoo</font></td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['location']}</font></td>
            <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$location</font></td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['interests']}</font></td>
            <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$interests</font></td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['occupation']}</font></td>
            <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$occupation</font></td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['aboutme']}</font></td>
            <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$bio</font></td></tr>";
    }
        
    $output .= "</table></td></tr></table>";

    print "$output<p>{$Globals['cright']}";
    printfooter();    
}

if ( $ppaction == "forgot" ) {
    if ( $do == "process" ) {
        $query = "SELECT username,userid,joindate FROM users WHERE email='$inemail'";
        $resultb = ppmysql_query($query,$link);
        $checkrows = mysql_num_rows($resultb);

        while( list( $dbuser, $dbuid, $joindate ) = mysql_fetch_row($resultb) ) {
            $email_from = "From: {$Globals['adminemail']}";
            $letter="You just requested that your password be reset at {$Globals['webname']}.
            
In order to do so, you must click on the link below or copy it into your web browser:

{$Globals['maindir']}/member.php?ppaction=rpwd&uid=$dbuid&verifykey=$joindate

Thanks!

The {$Globals['webname']} Team
".$Globals['domain'];

            $subject="How to reset your {$Globals['webname']} password";
            mail( $inemail, $subject, $letter, $email_from );
        }
        if ( $resultb ) ppmysql_free_result( $resultb );

        if ($checkrows > 0) {
            $redir = "{$Globals['maindir']}/index.php";
            forward( $redir, $Globals['pp_lang']['checke'] );
            exit;
        }
        else {
            $message = "<form size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['noaddr']}</font>";
        }
    }

    printheader( 0, $Globals['pp_lang']['memberpassadmin'] );

    $output = "<p><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals['bordercolor']."\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">

        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['mainfonts']}\"
        color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font
        size=\"{$Globals['fontlarge']}\"
        face=\"{$Globals['mainfonts']}\">".$Globals['galleryname']." {$Globals['pp_lang']['forgot']}</font>
        </font></td></tr></table></td></tr></table><p><center>$message<p>";

    $login = "<p><FORM ACTION=\"{$Globals['maindir']}/member.php\" METHOD=\"POST\">
        <TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\">
        <TR><TD BGCOLOR=\"".$Globals['bordercolor']."\">
        <TABLE BORDER=\"0\" CELLPADDING=\"10\" CELLSPACING=\"1\" WIDTH=\"100%\">
        <TR BGCOLOR=\"{$Globals['headcolor']}\">
        <TD COLSPAN=\"3\">
        <FONT FACE=\"sans-serif\" size=\"{$Globals['fontmedium']}\" COLOR=\"{$Globals['headfontcolor']}\">
        <b>{$Globals['pp_lang']['resetform']}</b>
        </FONT>
        <br />
        </TD>
        </TR><TR BGCOLOR=\"{$Globals['maincolor']}\">
        <TD>
        <FONT FACE=\"sans-serif\" size=\"{$Globals['fontmedium']}\" COLOR=\"{$Globals['maintext']}\">
        <b>{$Globals['pp_lang']['youremail']}: &nbsp;</b></font>
        </FONT>
        </TD>
        <TD>
        <INPUT TYPE=\"TEXT\" NAME=\"inemail\" SIZE=\"25\" MAXstrlen=\"40\" VALUE=\"\"></td>
        </TR>
        <TR BGCOLOR=\"{$Globals['maincolor']}\">
        <TD COLSPAN=\"3\" ALIGN=\"CENTER\">
        <input type=\"hidden\" name=\"ppaction\" value=\"forgot\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <INPUT TYPE=\"SUBMIT\" NAME=\"submit\" VALUE=\"{$Globals['pp_lang']['submit']}\">
        </TD>
        </TR>
        </TABLE>
        </TD></TR>
        </TABLE>
        </FORM>

        </td></tr></table></td></tr></table>";

    print "$output$login{$Globals['cright']}";
    printfooter();
}


if ( $ppaction == "chgpass" ) {
    if ( $gologin == 1 ) {
        $furl = $Globals['maindir'];
        $furl = str_replace( $Globals['domain'], "", $furl );
        $furl = "$furl/member.php?ppaction=chgpass";
        login($furl);
        exit;
    }

    if ( $do == "process" ) {
        $reason = "";

        if ( empty($oldpassword) ) {
            $reason .= "<li>{$Globals['pp_lang']['blankpass']}";
            $stop = 1;
        }
        if ( empty($newpassword) ) {
            $reason .= "<li>{$Globals['pp_lang']['blankpass2']}";
            $stop = 1;
        }
        if ( empty($cnewpassword) ) {
            $reason .= "<li>{$Globals['pp_lang']['confirmblank']}";
            $stop = 1;
        }
        if ($newpassword != $cnewpassword) {
            $reason .= "<li>{$Globals['pp_lang']['nomatch']}";
            $stop = 1;
        }

        if ($newpassword != "") {
            $pwdstrlen = strlen($newpassword);
            if ($pwdstrlen < 4) {
                $reason .= "<li>{$Globals['pp_lang']['fourchars']}";
                $stop = 1;
            }
        }

        $query = "SELECT password FROM users WHERE userid=$userid LIMIT 1";
        $resulta = ppmysql_query($query, $link);
        list( $dbpwd ) = mysql_fetch_row($resulta);
        ppmysql_free_result($resulta);        

        $oldpassword = md5($oldpassword);
        if ($oldpassword != $dbpwd) {
            $reason .= "<li>{$Globals['pp_lang']['oldwrong']}";
            $stop = 1;
        }

        if ($stop == 1) {
            diewell($reason);
        }

        $newpassword = md5($newpassword);
        $query = "UPDATE users SET password='$newpassword' WHERE userid=$userid";
        $resulta = ppmysql_query($query,$link);

        $redirc = "{$Globals['maindir']}/index.php";
        forward( $redirc, $Globals['pp_lang']['passchanged'] );
        exit;
    }

    topmenu();

    printheader( 0, $Globals['pp_lang']['memberoassadmin'] );

    $output = "<br /><table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td>
        <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"{$Globals['headcolor']}\">
        <font size=\"{$Globals['fontlarge']}\" face=\"{$Globals['mainfonts']}\">".$Globals['galleryname']."</font>
        </td></tr><!--PhotoPost, Copyright All Enthusiast,Inc.-->

        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"{$Globals['headcolor']}\">
        <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['changeform']}</b></font>
        </td></tr><!--PhotoPost, Copyright All Enthusiast,Inc.-->
        <form method=\"POST\" action=\"{$Globals['maindir']}/member.php\">
        <tr>
        <td bgcolor=\"{$Globals['altcolor1']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\"><b>{$Globals['pp_lang']['oldpass']}:</b></font></td>
        <td bgcolor=\"{$Globals['altcolor1']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">
        <input type=\"password\" name=\"oldpassword\" maxstrlen=\"100\"></font>
        </td></tr>
        <tr><td bgcolor=\"{$Globals['altcolor2']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\"><b>{$Globals['pp_lang']['newpass']}:</b></font></td>
        <td bgcolor=\"{$Globals['altcolor2']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">
        <input type=\"password\" name=\"newpassword\" maxstrlen=\"100\"></font></td></tr>
        <tr><td bgcolor=\"{$Globals['altcolor1']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\"><b>{$Globals['pp_lang']['confirmpass']}:</b></font></td>
        <td bgcolor=\"{$Globals['altcolor1']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">
        <input type=\"password\" name=\"cnewpassword\" maxstrlen=\"100\"></font></td></tr>
        </table></td></tr></table><p>
        <input type=\"hidden\" name=\"ppaction\" value=\"chgpass\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <center><input type=\"submit\" name=\"submit\" value=\"{$Globals['pp_lang']['changepass']}\"></center>

        </td></tr></table></td></tr></table><p>";

    print "$output{$Globals['cright']}";
    printfooter();
}

// Edit a user's profile (form)

if ($ppaction == "edit") {
    if ( $gologin == 1 ){
        $furl = $Globals['maindir'];
        $furl = str_replace( $Globals['domain'], "", $furl );
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

    $query = "SELECT username,usergroupid,homepage,icq,aim,yahoo,joindate,posts,birthday,location,interests,occupation,bio,email,offset FROM users WHERE userid=$uid LIMIT 1";
    $resulta = ppmysql_query($query,$link);
    list($username,$usergroupid,$homepage,$icq,$aim,$yahoo,$joindate,$posts,$birthday,$location,$interests,$occupation,$bio,$email,$offset) = mysql_fetch_row($resulta);
    ppmysql_free_result($resulta);

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
    $resulta = ppmysql_query($query,$link);
    list( $usergroup ) = mysql_fetch_row($resulta);
    ppmysql_free_result($resulta);    

    topmenu();
    
    list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
    $mon = $mon + 1;
    $julian = mktime($hour,$min,$sec,$mon,$mday,$year);
    $cclock = formatpptime( $julian );
    $ppdate = formatppdate( $julian );
    $ttime = "$ppdate $cclock";

    printheader( 0, $Globals['pp_lang']['memberprofile'] );

    $output = "<br /><table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td>
        <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals['bordercolor']."\"
        width=\"{$Globals['tablewidth']}\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontlarge']}\"
        face=\"{$Globals['mainfonts']}\">".$Globals['galleryname']."</font>
        </font></td></tr><!--PhotoPost, Copyright All Enthusiast,Inc.-->

        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['editprofile']} $username</font>
        </font></td></tr><!--PhotoPost, Copyright All Enthusiast,Inc.-->
        <form method=\"post\" action=\"{$Globals['maindir']}/member.php\">
        <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['datereg']}</font>
        </td><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$jmon-$jmday-$jyear</font></td></tr>
        <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['status']}</font></td>
        <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">$usergroup</font></td></tr>
        <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['password']}</font></td>
        <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">[ <a href=\"{$Globals['maindir']}/member.php?ppaction=chgpass\">{$Globals['pp_lang']['changepass']}</a> ]</td></tr>
        <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['email']}</font></td>
        <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\"><input type=\"text\" name=\"editemail\" size=\"25\" maxstrlen=\"100\" value=\"$email\"></td></tr>
        <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['emailconf']}</font></td>
        <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\"><input type=\"text\" name=\"editemailconfirm\" size=\"25\" maxstrlen=\"100\" value=\"$email\"></td></tr>";
        
    if ( $Globals['getoptional'] == "yes" ) {
        $output .= "<tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['bday']}</font></td>
            <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">
            <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
            <tr>
            <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" >{$Globals['pp_lang']['month']}</font></td>
            <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" >Day</font></td>
            <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" >Year</font></td>
            </tr>
            <tr>
            <td><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" ><select name=\"editmonth\">
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
            <td><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\">
            <input type=\"text\" name=\"edityear\" value=\"$byear\" size=\"{$Globals['fontlarge']}\" maxstrlen=\"4\"></font>
            </td></tr>
            </table>
            </font></td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['homepage']}</font></td>
            <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">
            <input type=\"text\" name=\"edithomepage\" size=\"25\" maxstrlen=\"100\" value=\"$homepage\"></font>
            </td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['icq']}</font></td>
            <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">
            <input type=\"text\" name=\"editicq\" size=\"25\" maxstrlen=\"20\" value=\"$icq\"></font>
            </td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['aim']}</font></td>
            <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">
            <input type=\"text\" name=\"editaim\" size=\"25\" maxstrlen=\"20\" value=\"$aim\"></font>
            </td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['yahoo']}</font></td>
            <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">
            <input type=\"text\" name=\"edityahoo\" size=\"25\" maxstrlen=\"20\" value=\"$yahoo\"></font>
            </td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['location']}</font></td>
            <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">
            <input type=\"text\" name=\"editlocation\" size=\"25\" maxstrlen=\"250\" value=\"$location\"></font>
            </td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['interests']}</font></td>
            <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">
            <input type=\"text\" name=\"editinterests\" size=\"25\" maxstrlen=\"250\" value=\"$interests\"></font>
            </td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['occupation']}</font></td>
            <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">
            <input type=\"text\" name=\"editoccupation\" size=\"25\" maxstrlen=\"250\" value=\"$occupation\"></font>
            </td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['aboutme']}</font></td>
            <td bgcolor=\"{$Globals['altcolor1']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">
            <input type=\"text\" name=\"editbio\" size=\"25\" maxstrlen=\"250\" value=\"$bio\"></font>
            </td></tr>";
        }
        
        $output .= "<tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">{$Globals['pp_lang']['timezone']}: $ttime</font></td>
            <td bgcolor=\"{$Globals['altcolor2']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"".$Globals['commentstext']."\">
            <input type=\"text\" name=\"offset\" size=\"25\" maxstrlen=\"3\" value=\"$offset\"></font>
            </td></tr>
            </table></td></tr></table><br />
            <center>
            <input type=\"hidden\" name=\"uid\" value=\"$uid\">
            <input type=\"hidden\" name=\"ppaction\" value=\"processedit\">
            <input type=\"submit\" value=\"{$Globals['pp_lang']['savechanges']}\"></form>";

    print "$output{$Globals['cright']}";
    printfooter();
}

// Process a user's edit, forward to profile display

if ($ppaction == "processedit") {
    if ($gologin==1){
        $furl=$Globals['maindir'];
        $furl= str_replace( $Globals['domain'], "", $furl );
        $furl="$furl/member.php?ppaction=edit";
        login($furl);
        exit;
    }

    $redir = "{$Globals['maindir']}/member.php?ppaction=profile&amp;uid=$uid";
    if ($adminedit == 0) {
        if ($uid != $userid) {
            diewell( $Globals['pp_lang']['noeditperm'] );
            exit;
        }
    }

    $email=$editemail;
    $emailconfirm=$editemailconfirm;
    $bio=$editbio;
    $birthday="$edityear-$editmonth-$editday";
    $homepage = fixstring($edithomepage);
    $icq = fixstring( $editicq );
    $aim = fixstring( $editaim );
    $yahoo = fixstring( $edityahoo );
    $year = fixstring( $edityear );
    $hobbies = fixstring( $editinterests );
    $occupation = fixstring( $editoccupation );
    $location = fixstring( $editlocation );
    
    if ($year == "") $year="0000";
    if ($month == "") $month="0";
    if ($day == "") $day="0";    

    if ($email != $emailconfirm) {
        diewell($Globals['pp_lang']['noemailmatch']);
        exit;
    }

    if ($Globals['emailverify'] == "yes") {
        // Check to see if user changed email. Verify it if needed.
        $query = "SELECT email,username FROM users WHERE userid=$uid LIMIT 1";
        $resulta = ppmysql_query($query,$link);
        list( $emaildb, $username ) = mysql_fetch_row($resulta);
        ppmysql_free_result($resulta);        

        if ($email != $emaildb) {
            list($dsec,$dmin,$dhour,$dmday,$dmon,$dyear,$dwday,$dyday,$disdst) = localtime();
            
            $nowtime = mktime($dhour,$dmin,$dsec,$dmon,$dmday,$dyear);
            $nowpass = md5($nowtime);
            
            $query = "UPDATE users SET password='$nowpass' WHERE userid=$uid";
            $resulta = ppmysql_query($query,$link);

            $email_from = "From: {$Globals['adminemail']}";
            $letter="You just changed your email address at {$Globals['webname']}.
We have issued a you a new temporary password in order to confirm your new email address.

Your New Temporary Password is: $nowtime

If you would like to change that password, you may do so here:

{$Globals['maindir']}/member.php?ppaction=chgpass

Thanks!

The {$Globals['webname']} Team
".$Globals['domain'];

            $subject="New temporary {$Globals['webname']} password";
            mail( $email, $subject, $letter, $email_from );
        }
    }

    // Write input data to db
    $query = "UPDATE users SET email='$email',homepage='$homepage',icq='$icq',aim='$aim',yahoo='$yahoo',birthday='$birthday',interests='$hobbies',occupation='$occupation',bio='$bio',location='$location',offset='$offset' WHERE userid=$uid";
    $resulta = ppmysql_query($query,$link);

    forward( $redir, $Globals['pp_lang']['profileupdated'] );
}

?>
