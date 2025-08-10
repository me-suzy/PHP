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
function login( $redirect ) {
    global $Globals, $header, $footer;

    if ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
        $forgot_link = $Globals{'maindir'}."/member.php?ppaction=lostpw";
        $register_link = $Globals{'vbulletin'}."/register.php?action=signup";
    }
    if ($Globals{'vbversion'} == "phpBB") {
        $forgot_link = $Globals{'vbulletin'}."/sendpassword.pl";
        $register_link = $Globals{'vbulletin'}."/bb_register.php?mode=agreement";
    }
    if ($Globals{'vbversion'} == "phpBB2") {
        $forgot_link = $Globals{'vbulletin'}."/profile.php?mode=sendpassword";
        $register_link = $Globals{'vbulletin'}."/profile.php?mode=register";
    }
    if ($Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
        $forgot_link = $Globals{'vbulletin'}."/logout.php?Cat=";
        $register_link = $Globals{'vbulletin'}."/newuser.php?Cat=";
    }
    if ($Globals{'vbversion'} == "Internal") {
        $forgot_link = $Globals{'maindir'}."/member.php?ppaction=forgot";
        $register_link = $Globals{'maindir'}."/register.php";
    }

    $output = "<head><title>".$Globals{'galleryname'}." Login</title></head>$header<p>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"verdana,arial,helvetica\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"2\"><B>".$Globals{'galleryname'}." Login
        </font></td></tr></table></td></tr></table>";

    $login = "<p><FORM ACTION=\"".$Globals{'maindir'}."/login.php\" METHOD=\"POST\">
        <TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\">
        <TR><TD BGCOLOR=\"".$Globals{'bordercolor'}."\">
        <TABLE BORDER=\"0\" CELLPADDING=\"10\" CELLSPACING=\"1\" WIDTH=\"100%\">
        <TR BGCOLOR=\"".$Globals{'headcolor'}."\" ALIGN=\"CENTER\">
        <TD COLSPAN=\"3\"><FONT FACE=\"sans-serif\" SIZE=\"2\" COLOR=\"".$Globals{'headfontcolor'}."\">
        <b>Please enter your username and password.</b>
        </FONT><br></TD></TR><TR BGCOLOR=\"".$Globals{'maincolor'}."\">
        <TD><FONT FACE=\"sans-serif\" SIZE=\"2\" COLOR=\"".$Globals{'maintext'}."\">
        <b>Username: &nbsp;</b></font></FONT></TD><TD>
        <INPUT TABINDEX=\"1\" TYPE=\"TEXT\" NAME=\"user\" SIZE=\"25\" MAXLENGTH=\"40\" VALUE=\"\"></td><Td><center>
        <FONT FACE=\"sans-serif\" SIZE=\"2\">
        <a href=\"$register_link\"><font color=\"".$Globals{'maintext'}."\">Register</font></a></FONT>
        </td></TD></TR><TR BGCOLOR=\"".$Globals{'maincolor'}."\"><TD>
        <FONT FACE=\"sans-serif\" SIZE=\"2\" COLOR=\"".$Globals{'maintext'}."\">
        <b>Password: </b>
        </FONT></TD><TD><Center>
        <INPUT TABINDEX=\"2\" TYPE=\"PASSWORD\" NAME=\"password\" SIZE=\"25\" MAXLENGTH=\"25\"></td><Td><FONT FACE=\"sans-serif\" SIZE=\"2\">
        <a href=\"$forgot_link\"><font color=\"".$Globals{'maintext'}."\">Forgot password?</font></a></FONT>
        </td></TD></TR><TR BGCOLOR=\"".$Globals{'maincolor'}."\">
        <TD COLSPAN=\"3\" ALIGN=\"CENTER\">
        <input type=\"hidden\" name=\"url\" value=\"$redirect\">
        <INPUT TABINDEX=\"3\" TYPE=\"SUBMIT\" NAME=\"submit\" VALUE=\"Submit\">
        </TD></TR></TABLE></TD></TR></TABLE></FORM>";

    print "$output$login".$Globals{'cright'}."$footer";

    return(1);
}


function set_group_perms( $usergroup ) {
    global $Globals, $link, $db_link;
    global $ugcat, $uganno, $ugview, $ugpost, $exclude_cat;

    $query = "SELECT id,ugnoupload,ugnoanno,ugnoview,ugnopost FROM categories";
    $resultb = mysql_query_eval($query,$link);

    // need to do this loop for each group user is a member of
    while ( $row = mysql_fetch_row($resultb) ) {
        list( $catugid, $ugnoup, $ugnoanno, $ugnoview, $ugnopost ) = $row;

        $ugcat{$catugid}=0;
        $uganno{$catugid}=0;
        $ugview{$catugid}=0;
        $ugpost{$catugid}=0;
        
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
                $exclude_cat .= "AND cat != $catugid ";
            }
        }
    }
    return;
}


function authenticate( $user="", $chkpassword="" ) {
    global $Globals, $userid, $password;
    global $w3t_mypass, $w3t_myname, $w3t_myid, $w3t_key;
    global $postid, $echeck, $phoedit, $gologin,$bbuserid,$bbpassword;
    global $link, $db_link, $phpbbuid, $phpbbpass, $phpbb2uid, $phpbb2pw;
    global $adminedit, $nopost, $up_k, $usercomment, $userup, $disk_k, $cedit;
    global $comments, $uploads, $cpaccess, $diskspace, $uploadsize, $usergroup, $username;
    global $ugcat, $uganno, $ugview, $ugpost, $exclude_cat;

    // Init some variables
    $gologin = 1; $nopost = 1; $username = ""; $usergroup = 0; $adminedit = 0; $checkpass = 0;
    $ueditpho = 0; $ueditposts = 0; $comments = 0; $diskspace = 0; $uploadsize = 0; $cpaccess = 0;
    $adminedit = 0; $md5cookpass="";

    //
    // UBBThreads authentication
    //

    if ($Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
        // If we arent passing variables, set them to what was passed in the URL
        if ( $user == "" ) {
            $query = "SELECT U_Username, U_Number, U_Groups, U_Password, U_TempPass FROM w3t_Users WHERE U_Number='-1' LIMIT 1";

            if ( !empty($w3t_myid) ) {
                $userid = $w3t_myid;
                $query = "SELECT U_Username, U_Number, U_Groups, U_Password, U_TempPass FROM w3t_Users WHERE U_Number='$userid' LIMIT 1";
            }

            if ( !empty($w3t_myname) ) {
                $cookuser = $w3t_myname;
                $query = "SELECT U_Username, U_Number, U_Groups, U_Password, U_TempPass FROM w3t_Users WHERE U_Username='$cookuser' LIMIT 1";
            }

            if ( !empty($w3t_mypass) ) {
                $md5cookpass = $w3t_mypass;
            }
        }
        else {
            $w3t_myname = $user;
            $cookuser = $user;
            $md5cookpass = md5($chkpassword);

            $query = "SELECT U_Username, U_Number, U_Groups, U_Password,U_TempPass FROM w3t_Users WHERE U_Username='$cookuser' LIMIT 1";
        }

        // so at this point:
        // $cookuser should be the user we are checking
        // $md5cookpass should be the password we are checking
        // and either $userid (for v6) or $cookuser (for v5) is set

        $result = mysql_query_eval($query,$db_link);
        $row = mysql_fetch_row($result);
        list( $username, $userid, $usergroups, $dbpassword, $md5temppass ) = $row;

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

            //print("[$username:$md5cookpass]<br>password: $password<br>dbpassword: $dbpassword<br>md5cookpass: $md5cookpass<br>md5temppass: $md5temppass<br>checkpass: $checkpass<br>md5dblpass: $md5dblpass]<br>[$query]<br>");
            //exit;

            if ($checkpass == 1) {
                $adminedit=0; $gologin=5; $nopost=0;
                $ubbgroups = explode( "-", $usergroups);
            }
        }
        else {
            // This assumes that G_Id for "Guests" is 4
            $usergroup = 4;
            set_group_perms( $usergroup );
        }
    }


    //
    // Internal authentication
    //

    if ( $Globals{'vbversion'} == "Internal" ) {
        if ( $user == "" ) {
            if ( empty($cookuser) && IsSet($phpbbuid) ) {
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

            $query = "SELECT userid,username,password,usergroupid FROM users WHERE userid='$cookuser' LIMIT 1";
        }
        else {
            $cookuser = $user;
            $md5cookpass = md5($password);
            $query = "SELECT userid,username,password,usergroupid FROM users WHERE username='$cookuser' LIMIT 1";
        }

        $resulta = mysql_query_eval($query,$link);
        $row = mysql_fetch_row($resulta);
        list( $userid, $username, $md5dbpass, $usergroup ) = $row;

        //print("Internal: [$cookuser/$md5cookpass/$password/$md5dbpass/$md5temppass/$userid/$username/$usergroup]<br>");
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
            set_group_perms( $usergroup );
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

    if ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
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

        if ( IsSet($bbuserid) && ($userid == "0" || $userid == "") ) {
            $cookuser = $bbuserid;
            $md5cookpass=$bbpassword;
            $qtype = "userid='$cookuser'";
        }

        $query = "SELECT password,userid,usergroupid,username FROM user WHERE $qtype LIMIT 1";
        $result = mysql_query_eval($query,$db_link);
        $row = mysql_fetch_row($result);
        list( $dbpassword, $userid, $usergroup, $username ) = $row;

        if ($dbpassword != "") {
            if ($md5cookpass == "") $md5cookpass = $bbpassword;
            if ($Globals{'vbversion'} == "2.0.3") $md5dbpass = md5($dbpassword);
            else $md5dbpass = $dbpassword;
            if ($md5dbpass == $md5cookpass) {
                // user passed
                $adminedit=0; $gologin=0; $nopost=0;
            }
        }
    }


    //
    // phpBB
    //
    if ($Globals{'vbversion'} == "phpBB") {
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
        if ( IsSet($phpbbuid) && ($userid == "0" || $userid == "") ) {
            $cookuser = $phpbbuid;
            $md5cookpass = $phpbbpass;
            $qtype = "user_id='$cookuser'";
        }

        $query = "SELECT user_password,user_id,user_level,username FROM users WHERE $qtype LIMIT 1";
        $result = mysql_query_eval($query,$db_link);
        $row = mysql_fetch_row($result);
        list( $dbpassword, $userid, $usergroup, $username ) = $row;

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
            set_group_perms( $usergroup );
        }

        if ($usergroup < 1) {
            $nopost=1;
        }
    }


    //
    // phpBB2
    //
    if ($Globals{'vbversion'} == "phpBB2") {
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
        if ( IsSet($phpbb2uid) && ($userid == "0" || $userid == "") ) {
            $cookuser = $phpbb2uid;
            $md5cookpass = $phpbb2pw;
            $qtype = "user_id='$cookuser'";
        }

        if ( !empty($Globals{'dprefix'}) ) {
            $utable=$Globals{'dprefix'} ."_users";
        }
        else {
            $utable="users";
        }

        $query = "SELECT user_password,user_id,username,user_level FROM $utable WHERE $qtype LIMIT 1";
        $result = mysql_query_eval($query,$db_link);
        $row = mysql_fetch_row($result);
        list( $dbpassword, $userid, $username, $user_status ) = $row;

        if ($dbpassword != "") {
            if ($md5cookpass == "") $md5cookpass=$phpbb2pw;

            if ($dbpassword == $md5cookpass) {
                // user passed; 5 is for an array of groups
                $adminedit=0; $gologin=5; $nopost=0;

                if ( !empty($Globals{'dprefix'}) ) {
                    $ugtable = $Globals{'dprefix'} ."_user_group";
                    $uggroups = $Globals{'dprefix'} ."_groups";
                }
                else {
                    $ugtable = "user_group";
                    $uggroups = "groups";
                }

                $query = "SELECT $ugtable.group_id FROM $ugtable LEFT JOIN $uggroups ON $ugtable.group_id =
                    $uggroups.group_id WHERE $ugtable.user_id=$userid AND $ugtable.user_pending=0 AND $uggroups.group_single_user=0";
                $result = mysql_query_eval($query,$db_link);

                $ubbgroups = array();
                while ( $row = mysql_fetch_row($result) ) {
                    array_push( $ubbgroups, $row[0] );
                }

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
            set_group_perms( $usergroup );
        }
    }

    // gologin of 5 means that we already did this for multiple groups;
    // otherwise we check the users group for permissions

    if ( $gologin == 5 || $gologin == 0 ) {
        if ( $gologin == 5 ) {
            foreach ( $ubbgroups as $groupvalue ) {
                if ( $groupvalue != "" ) {
                    $query = "SELECT groupid,cpaccess,diskspace,uploadsize,uploads,comments,editpho,editposts FROM usergroups WHERE groupid='$groupvalue' LIMIT 1";

                    $result = mysql_query_eval($query,$link);
                    $row = mysql_fetch_row($result);
                    list( $usergroup,$cpaccess_a,$diskspace_a,$uploadsize_a,$uploads_a,$comments_a,$ueditpho_a,$ueditposts_a ) = $row;

                    //print "[$usergroup, $cpaccess_a, $diskspace_a, $uploadsize_a, $uploads_a, $comments_a]<br>";

                    if ( $cpaccess_a > $cpaccess || !$cpaccess ) $cpaccess=$cpaccess_a;
                    if ( $diskspace_a > $diskspace || !$diskspace ) $diskspace=$diskspace_a;
                    if ( $uploadsize_a > $uploadsize || !$uploadsize ) $uploadsize=$uploadsize_a;
                    if ( $uploads_a > $uploads || !$uploads ) $uploads=$uploads_a;
                    if ( $comments_a > $comments || !$comments ) $comments=$comments_a;
                    if ( $ueditpho_a > $ueditpho || !$ueditpho ) $ueditpho=$ueditpho_a;
                    if ( $ueditposts_a > $ueditposts || !$ueditposts ) $ueditposts=$ueditposts_a;

                    set_group_perms( $groupvalue );
                }
            }
        }

        //print "[$cpaccess][$diskspace][$uploadsize][$uploads][$comments]<br>";
        //exit;

        if ( $gologin == 0 ) {
            $query = "SELECT cpaccess,diskspace,uploadsize,uploads,comments,editpho,editposts FROM usergroups WHERE groupid=$usergroup LIMIT 1";
            $resulta = mysql_query_eval($query,$link);
            $row = mysql_fetch_row($resulta);
            list($cpaccess,$diskspace,$uploadsize,$uploads,$comments,$ueditpho,$ueditposts) = $row;

            set_group_perms( $usergroup );
        }

        if ($diskspace > 0) $disk_k = $diskspace;
        else $disk_k = $Globals{'maxspace'};

        if ($uploadsize > 0) $up_k = $uploadsize;
        else $up_k = $Globals{'uploadsize'};

        if ($uploads == "0") $userup = 2;
        if ($comments == "0") $usercomment = 2;
        if ($ueditpho == "0") $usereditpho = 2;
        if ($ueditposts == "0") $usereditposts = 2;
        if ($cpaccess == 1) $adminedit = 1;

        $gologin=0;
    }
    else {
        $username="Unregistered";
        $userid=0;
        $usergroup=0;
    }

    //
    //
    // We're done with Authorization, but jsut need to check a couple of variables
    // if we're editing a post, check to see that the user is either an admin/mod or the post owner ###
    //
    //
    if ($postid == "") {
        $postid = $cedit;
    }

    if ($postid != "") {
        $query = "SELECT userid FROM comments WHERE id='$postid' LIMIT 1";
        $result = mysql_query_eval($query,$link);
        $row = mysql_fetch_array($result);
        $echeck=$row['userid'];

        if ($adminedit == 0) {
            if ($userid != $echeck) $nopost=1;
        }
    }

    if ($phoedit != "") {
        $query = "SELECT userid FROM photos WHERE id='$phoedit' LIMIT 1";
        $result = mysql_query_eval($query,$link);
        $row = mysql_fetch_array($result);
        $echeck=$row['userid'];

        if ($adminedit == 0) {
            if ($userid != $echeck) $nopost=1;
        }
    }

    //dieWell("[$username][$usergroup]");
    //exit;
    return( $gologin );
}

?>
