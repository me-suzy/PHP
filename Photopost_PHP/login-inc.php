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

require "languages/$pplang/login-inc.php";

function login( $redirect ) {
    global $Globals, $link;

    if ($Globals['vbversion'] == "2.0.3" || $Globals['vbversion'] == "2.2.0") {
        $forgot_link ="{$Globals['vbulletin']}/member.php?ppaction=lostpw";
        $register_link ="{$Globals['vbulletin']}/register.php?action=signup";
    }
    if ($Globals['vbversion'] == "phpBB") {
        $forgot_link ="{$Globals['vbulletin']}/sendpassword.pl";
        $register_link ="{$Globals['vbulletin']}/bb_register.php?mode=agreement";
    }
    if ($Globals['vbversion'] == "phpBB2") {
        $forgot_link ="{$Globals['vbulletin']}/profile.php?mode=sendpassword";
        $register_link ="{$Globals['vbulletin']}/profile.php?mode=register";
    }
    if ($Globals['vbversion'] == "w3t" || $Globals['vbversion'] == "w3t6") {
        $forgot_link ="{$Globals['vbulletin']}/logout.php?Cat=";
        $register_link ="{$Globals['vbulletin']}/newuser.php?Cat=";
    }
    if ($Globals['vbversion'] == "Internal") {
        $forgot_link = "{$Globals['maindir']}/member.php?ppaction=forgot";
        $register_link = "{$Globals['maindir']}/register.php";
    }

    printheader( 0, "Login" );

    $output = "<center><p>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontmedium']}\"><b>{$Globals['galleryname']} {$Globals['pp_lang']['login']}
        </b></font></td></tr></table></td></tr></table>";

    $login = "<p><FORM ACTION=\"{$Globals['maindir']}/login.php\" METHOD=\"POST\">
        <TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACING=\"0\" align=\"CENTER\">
        <TR><TD BGCOLOR=\"{$Globals['bordercolor']}\">
        <TABLE BORDER=\"0\" CELLPADDING=\"10\" CELLSPACING=\"1\" WIDTH=\"100%\">
        <TR BGCOLOR=\"{$Globals['headcolor']}\" ALIGN=\"CENTER\">
        <TD COLSPAN=\"3\"><FONT FACE=\"sans-serif\" size=\"{$Globals['fontmedium']}\" COLOR=\"{$Globals['headfontcolor']}\">
        <b>{$Globals['pp_lang']['enterup']}</b>
        </FONT><br /></TD></TR><TR BGCOLOR=\"{$Globals['maincolor']}\">
        <TD><FONT FACE=\"sans-serif\" size=\"{$Globals['fontmedium']}\" COLOR=\"{$Globals['maintext']}\">
        <b>{$Globals['pp_lang']['username']}: &nbsp;</b></font></TD><TD align=\"center\">
        <INPUT TABINDEX=\"1\" TYPE=\"TEXT\" NAME=\"user\" SIZE=\"25\" MAXLENGTH=\"40\" VALUE=\"\"></td><td>
        <FONT FACE=\"sans-serif\" size=\"{$Globals['fontmedium']}\">
        <a href=\"$register_link\"><font color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['register']}</font></a></FONT>
        </td></TR><TR BGCOLOR=\"{$Globals['maincolor']}\"><TD>
        <FONT FACE=\"sans-serif\" size=\"{$Globals['fontmedium']}\" COLOR=\"{$Globals['maintext']}\">
        <b>{$Globals['pp_lang']['password']}: </b>
        </FONT></TD><TD align=\"center\">
        <INPUT TABINDEX=\"2\" TYPE=\"PASSWORD\" NAME=\"password\" SIZE=\"25\" MAXLENGTH=\"25\"></td><td><FONT FACE=\"sans-serif\" size=\"{$Globals['fontmedium']}\">
        <a href=\"$forgot_link\"><font color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['forgot']}</font></a></FONT>
        </td></TR><TR BGCOLOR=\"{$Globals['maincolor']}\">
        <TD COLSPAN=\"3\" ALIGN=\"CENTER\">
        <input type=\"hidden\" name=\"url\" value=\"$redirect\">
        <INPUT TABINDEX=\"3\" TYPE=\"SUBMIT\" NAME=\"submit\" VALUE=\"{$Globals['pp_lang']['submit']}\">
        </TD></TR></TABLE></TD></TR></TABLE></FORM>";

    print "$output$login{$Globals['cright']}";
    printfooter();    

    return(1);
}


function set_group_perms( $usergroup, $xloop = 0 ) {
    global $Globals, $link, $db_link;
    global $ugcat, $uganno, $ugview, $ugpost, $exclude_cat;

    $query = "SELECT id,ugnoupload,ugnoanno,ugnoview,ugnopost FROM categories";
    $resultb = ppmysql_query($query,$link);

    // need to do this loop for each group user is a member of
    while ( list( $catugid, $ugnoup, $ugnoanno, $ugnoview, $ugnopost ) = mysql_fetch_row($resultb) ) {
        if ( $xloop == 0 ) {
            $ugcat{$catugid}=0;
            $uganno{$catugid}=0;
            $ugview{$catugid}=0;
            $ugpost{$catugid}=0;
        }

        $allnoup = explode( ",", $ugnoup );
        foreach ($allnoup as $key) {
            if ($usergroup == $key) {
                $ugcat{$catugid}=1;
            }
        }

        $allnoanno = explode( ",", $ugnoanno );
        foreach ($allnoanno as $key) {
            if ($usergroup == $key) {
                $uganno{$catugid}=1;
            }
        }

        $allnoview = explode( ",", $ugnoview );
        foreach ($allnoview as $key) {
            if ($usergroup == $key) {
                $ugview{$catugid}=1;
                $exclude_cat .= "AND cat != $catugid ";
            }
        }

        $allnopost = explode( ",", $ugnopost );
        foreach ($allnopost as $key) {
            if ($usergroup == $key) {
                $ugpost{$catugid}=1;
            }
        }
    }
    ppmysql_free_result( $resultb );
    
    return;
}


function authenticate( $user="", $chkpassword="" ) {
    global $Globals, $userid, $password;
    global $w3t_mypass, $w3t_myname, $w3t_myid, $w3t_key, $w3t_mysess;
    global $postid, $echeck, $phoedit, $gologin, $bbuserid, $bbpassword;
    global $link, $db_link, $phpbbuid, $phpbbpass, $phpbb2uid, $phpbb2pw;
    global $adminedit, $nopost, $up_k, $usercomment, $useruploads, $disk_k, $cedit;
    global $cpaccess, $diskspace, $uploadsize, $usergroup, $username;
    global $ugcat, $uganno, $ugview, $ugpost, $ueditpho, $ueditposts, $exclude_cat;

    // Init some variables
    $gologin = 1; $nopost = 1; $username = ""; $usergroup = 0; $adminedit = 0; $checkpass = 0;
    $ueditpho = 0; $ueditposts = 0; $comments = 0; $diskspace = 0; $uploadsize = 0; $cpaccess = 0;
    $adminedit = 0; $md5cookpass="";

    //
    // UBBThreads authentication
    //

    if ($Globals['vbversion'] == "w3t" || $Globals['vbversion'] == "w3t6") {
        if ( !empty( $Globals['dprefix'] ) ) {
            $utable = "{$Globals['dprefix']}Users";
        }
        else {
            $utable = "w3t_Users";
        }
        
        // If we arent passing variables, set them to what was passed in the URL        
        if ( $user == "" ) {            
            $query = "SELECT U_Username, U_Number, U_Groups, U_SessionId, U_Password, U_TempPass, U_TimeOffset FROM $utable WHERE U_Number='-1' LIMIT 1";

            if ( !empty($w3t_myid) ) {
                $userid = $w3t_myid;
                $query = "SELECT U_Username, U_Number, U_Groups, U_SessionId, U_Password, U_TempPass, U_TimeOffset FROM $utable WHERE U_Number='$userid' LIMIT 1";
            }

            if ( !empty($w3t_myname) ) {
                $cookuser = $w3t_myname;
                $query = "SELECT U_Username, U_Number, U_Groups, U_SessionId, U_Password, U_TempPass, U_TimeOffset FROM $utable WHERE U_Username='$cookuser' LIMIT 1";
            }

            if ( !empty($w3t_mypass) ) {
                $md5cookpass = $w3t_mypass;
            }
        }
        else {
            $w3t_myname = $user;
            $cookuser = $user;
            $md5cookpass = md5($chkpassword);

            $query = "SELECT U_Username, U_Number, U_Groups, U_SessionId, U_Password, U_TempPass, U_TimeOffset FROM $utable WHERE U_Username='$cookuser' LIMIT 1";
        }

        // so at this point:
        // $cookuser should be the user we are checking
        // $md5cookpass should be the password we are checking
        // and either $userid (for v6) or $cookuser (for v5) is set

        $result = ppmysql_query($query,$db_link);
        list( $username, $userid, $usergroups, $sessionid, $dbpassword, $md5temppass, $offset ) = mysql_fetch_row($result);
        ppmysql_free_result($result);

        if ($dbpassword != "") {
            if ( !empty($w3t_key) ) {
                $testpass = md5("{$username}{$dbpassword}");
                if ( $testpass == $w3t_key ) $checkpass=1;
            }

            if ( $dbpassword == $md5cookpass ) $checkpass=1;

            // check DES for v5 and legecy v6 passwords
            if ( crypt($password,$dbpassword) == $dbpassword ) $checkpass=1;

            // just to be sure, lets see if they have a temp pass for verification and do the same checks
            if ( $md5temppass != "" && $checkpass != 1 ) {
                if ( $md5cookpass == $md5temppass ) $checkpass=1;
                if ( crypt($password,$dbpassword) == $dbpassword ) $checkpass=1;
            }

            //print("[$username:$md5cookpass]<br />password: $password<br />dbpassword: $dbpassword<br />md5cookpass: $md5cookpass<br />md5temppass: $md5temppass<br />checkpass: $checkpass<br />md5dblpass: $md5dblpass]<br />[$query]<br />sessionid: [$sessionid:$w3t_mysess]<br />");
            //exit;

            if ($checkpass == 1) {
                $adminedit=0; $gologin=5; $nopost=0;
                $ubbgroups = explode( "-", $usergroups);
            }
            else {
                // Maybe they didnt check Remember Me? and we need to verify their session
                if ( ($sessionid) && ($sessionid == $w3t_mysess) ) {
                    $adminedit=0; $gologin=5; $nopost=0;
                    $ubbgroups = explode( "-", $usergroups);
                }
                else {
                    // This assumes that G_Id for "Guests" is 4
                    $usergroup = 4;
                }
            }            
        }
        else {
            // This assumes that G_Id for "Guests" is 4
            $usergroup = 4;
        }
    }


    //
    // Internal authentication
    //

    if ( $Globals['vbversion'] == "Internal" ) {
        if ( $user == "" ) {
            if ( empty($cookuser) && isset($phpbbuid) ) {
                $cookuser = $phpbbuid;
                $md5cookpass = $phpbbpass;
            }
            else {
                if (!empty($forumuserid))
                    $cookuser = $formuserid;
                if (!empty($md5password))
                    $md5cookpass = $md5password;
            }
            if ( empty($cookuser) )
                $cookuser=-1;

            $query = "SELECT userid,username,password,usergroupid,offset FROM users WHERE userid='$cookuser' LIMIT 1";
        }
        else {
            $cookuser = $user;
            $md5cookpass = md5($password);
            $query = "SELECT userid,username,password,usergroupid,offset FROM users WHERE username='$cookuser' LIMIT 1";
        }

        $resulta = ppmysql_query($query,$link);
        list( $userid, $username, $md5dbpass, $usergroup, $offset ) = mysql_fetch_row($resulta);
        ppmysql_free_result($resulta);

        //print("Internal: [$cookuser/$md5cookpass/$password/$md5dbpass/$md5temppass/$userid/$username/$usergroup]<br />");
        //exit;
        if ($md5dbpass != "") {
            if ($md5dbpass == $md5cookpass) {
                // user passed
                $adminedit=0; $gologin=0; $nopost=0;
            }
        }
        else {
            // This assumes that usergroups has groupid of 3 for "Unregistered"
            $usergroup = 3;
        }
        
        // first check to see if the user has posting privledges..
        // groups of less than 3 do not have posting privledges
        if ($usergroup < 3) {
            $nopost = 1;
        }
    }

    //
    // vB authorization
    //

    if ($Globals['vbversion'] == "2.0.3" || $Globals['vbversion'] == "2.2.0") {
        if ( $user == "" ) {
            $cookuser = $bbuserid;
            $md5cookpass = $bbpassword;
            $qtype = "userid='$cookuser'";
        }
        else {
            $cookuser = $user;
            $md5cookpass = md5($password);
            $qtype = "username='$cookuser'";
        }

        if ( isset($bbuserid) && ($userid == "0" || $userid == "") ) {
            $cookuser = $bbuserid;
            $md5cookpass=$bbpassword;
            $qtype = "userid='$cookuser'";
        }

        $query = "SELECT password,userid,usergroupid,username,timezoneoffset FROM user WHERE $qtype LIMIT 1";
        $result = ppmysql_query($query,$db_link);
        list( $dbpassword, $userid, $usergroup, $username, $offset ) = mysql_fetch_row($result);
        ppmysql_free_result($result);

        if ($dbpassword != "") {
            if ($md5cookpass == "") $md5cookpass = $bbpassword;
            
            if ($Globals['vbversion'] == "2.0.3") $md5dbpass = md5($dbpassword);
            else $md5dbpass = $dbpassword;
            
            if ($md5dbpass == $md5cookpass) {
                // user passed
                $adminedit=0; $gologin=0; $nopost=0;
            }
        }
        else {
            // login for guests
            $usergroup = 1;
        }
        
        // groups less than 1 have no posting privledges..
        if ( $usergroup < 1 ) {
            $nopost = 1;
        }
    }


    //
    // phpBB
    //
    if ($Globals['vbversion'] == "phpBB") {
        if ( $user == "" ) {
            $cookuser = $phpbbuid;
            $md5cookpass = $phpbbpass;
            $qtype = "user_id='$cookuser'";
        }
        else {
            $cookuser = $user;
            $md5cookpass = md5($chkpassword);
            $qtype = "username='$cookuser'";
        }

        // cookies override what was passed, for security
        if ( isset($phpbbuid) && ($userid == "0" || $userid == "") ) {
            $cookuser = $phpbbuid;
            $md5cookpass = $phpbbpass;
            $qtype = "user_id='$cookuser'";
        }

        $query = "SELECT user_password,user_id,user_level,username,user_timezone FROM users WHERE $qtype LIMIT 1";
        $result = ppmysql_query($query,$db_link);
        list( $dbpassword, $userid, $usergroup, $username, $offset ) = mysql_fetch_row($result);
        ppmysql_free_result($result);

        if ($dbpassword != "") {
            if ($md5cookpass == "") $md5cookpass=$phpbbpass;
            $md5dbpass = $dbpassword;

            if ($md5dbpass == $md5cookpass) {
                // user passed
                $adminedit=0; $gologin=0; $nopost=0;
            }
        }
        else {
            // login for guests
            $usergroup = 1;
        }

        if ($usergroup < 1) {
            $nopost=1;
        }
    }


    //
    // phpBB2
    //
    if ($Globals['vbversion'] == "phpBB2") {
        if ( $user == "" ) {
            $cookuser = $phpbb2uid;
            $md5cookpass = $phpbb2pw;
            $qtype = "user_id='$cookuser'";
        }
        else {
            $cookuser = $user;
            $md5cookpass = md5($chkpassword);
            $qtype = "username='$cookuser'";
        }

        // cookies override what was passed, for security
        if ( isset($phpbb2uid) && ($userid == "0" || $userid == "") ) {
            $cookuser = $phpbb2uid;
            $md5cookpass = $phpbb2pw;
            $qtype = "user_id='$cookuser'";
        }

        if ( !empty($Globals['dprefix']) ) {
            $utable="{$Globals['dprefix']}users";
        }
        else {
            $utable="users";
        }

        $query = "SELECT user_password,user_id,username,user_level,user_timezone FROM $utable WHERE $qtype LIMIT 1";
        $result = ppmysql_query($query,$db_link);
        list( $dbpassword, $userid, $username, $user_status, $offset ) = mysql_fetch_row($result);
        ppmysql_free_result($result);

        if ($dbpassword != "") {
            if ($md5cookpass == "") $md5cookpass=$phpbb2pw;

            if ($dbpassword == $md5cookpass) {
                // user passed; 5 is for an array of groups
                $adminedit=0; $gologin=5; $nopost=0;

                if ( !empty($Globals['dprefix']) ) {
                    $ugtable = "{$Globals['dprefix']}user_group";
                    $uggroups = "{$Globals['dprefix']}groups";
                }
                else {
                    $ugtable = "user_group";
                    $uggroups = "groups";
                }

                $query = "SELECT $ugtable.group_id FROM $ugtable LEFT JOIN $uggroups ON $ugtable.group_id =
                    $uggroups.group_id WHERE $ugtable.user_id=$userid AND $ugtable.user_pending=0 AND $uggroups.group_single_user=0";
                $result = ppmysql_query($query,$db_link);

                $ubbgroups = array();
                while ( $row = mysql_fetch_row($result) ) {
                    array_push( $ubbgroups, $row[0] );
                }
                if ($result) ppmysql_free_result($result);

                if ( $user_status == 1 ) {
                    array_push( $ubbgroups, "2");
                }

                if ($ubbgroups[0] == "") {
                    $ubbgroups[0]=3;
                }
            }
        }
        else {
            // login for guests
            $usergroup = 1;
        }
    }

    // gologin of 5 means that we already did this for multiple groups;
    // otherwise we check the users group for permissions

    if ( $gologin == 5 ) {
        $xloop=0;
        foreach ( $ubbgroups as $groupvalue ) {
            if ( $groupvalue != "" ) {
                $query = "SELECT groupid,cpaccess,diskspace,uploadsize,uploads,comments,editpho,editposts FROM usergroups WHERE groupid='$groupvalue' LIMIT 1";
                $result = ppmysql_query($query,$link);
                list( $usergroup,$cpaccess_a,$diskspace_a,$uploadsize_a,$uploads_a,$comments_a,$ueditpho_a,$ueditposts_a ) = mysql_fetch_row($result);
                ppmysql_free_result($result);

                if ( $cpaccess_a   || !$adminedit   ) $adminedit   = $cpaccess_a;
                if ( $diskspace_a  >  $diskspace    ) $diskspace   = $diskspace_a;
                if ( $uploadsize_a >  $uploadsize   ) $uploadsize  = $uploadsize_a;
                if ( $uploads_a    || !$useruploads ) $useruploads = $uploads_a;
                if ( $comments_a   || !$usercomment ) $usercomment = $comments_a;
                if ( $ueditpho_a   || !$ueditpho    ) $ueditpho    = $ueditpho_a;
                if ( $ueditposts_a || !$ueditposts  ) $ueditposts  = $ueditposts_a;

                set_group_perms( $groupvalue, $xloop );
                $xloop++;
                //print "group: $groupvalue ... [$adminedit] [$diskspace] [$uploadsize] [$useruploads] [$usercomment] [$ueditpho] [$ueditposts]<br />";
            }
        }
        $gologin = 0;
        $Globals['offset'] = $offset;
        //print "group: $groupvalue ... [$adminedit] [$diskspace] [$uploadsize] [$useruploads] [$usercomment] [$ueditpho] [$ueditposts]<br />";        
    }
    else {
        $query = "SELECT cpaccess,diskspace,uploadsize,uploads,comments,editpho,editposts FROM usergroups WHERE groupid=$usergroup LIMIT 1";
        $resulta = ppmysql_query($query,$link);
        list( $adminedit,$diskspace,$uploadsize,$useruploads,$usercomment,$ueditpho,$ueditposts ) = mysql_fetch_row($resulta);
        ppmysql_free_result($resulta);
        
        set_group_perms( $usergroup );
        $Globals['offset'] = $offset;        
    }

    if ( $gologin == 1 ) {
        $username = $Globals['pp_lang']['unreg'];
        $userid = 0;
        $Globals['offset'] = $Globals['gmtoffset'];
    }

    if ( $diskspace > 0 ) $disk_k = $diskspace;
    else $disk_k = $Globals['maxspace'];

    if ( $uploadsize > 0 ) $up_k = $uploadsize;
    else $up_k = $Globals['uploadsize'];
    
    //
    //
    // We're done with Authorization, but just need to check a couple of variables
    // if we're editing a post, check to see that the user is either an admin/mod or the post owner ###
    //
    //
    if ($postid == "") {
        $postid = $cedit;
    }

    if ($postid != "") {
        $query = "SELECT userid FROM comments WHERE id='$postid' LIMIT 1";
        $result = ppmysql_query($query,$link);
        list( $echeck ) = mysql_fetch_row($result);
        ppmysql_free_result($result);        

        if ($adminedit == 0) {
            if ($userid != $echeck) $nopost=1;
        }
    }

    if ($phoedit != "") {
        $query = "SELECT userid FROM photos WHERE id='$phoedit' LIMIT 1";
        $result = ppmysql_query($query,$link);
        list( $echeck ) = mysql_fetch_row($result);
        ppmysql_free_result($row);        

        if ($adminedit == 0) {
            if ($userid != $echeck) $nopost=1;
        }
    }

    return( $gologin );
}

?>
