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

if ($ppaction == "usergroups") {
    if ($do == "add") {
        $query = "INSERT INTO usergroups values(NULL,'Default','0','0','0','0','0','0','0')";
        $resulta = mysql_query_eval($query,$link);

        forward( $Globals{'maindir'}."/adm-userg.php?ppaction=usergroups");
        exit;
    }

    if ($do == "delete") {
        $usergroupid=$groupid;

        if ($Globals{'vbversion'} == "Internal") {
            if ($usergroupid < 6) {
                dieWell("You can't delete this usergroup.");
            }
        }
        else {
            if ($usergroupid < 5) {
                dieWell("You can't delete this usergroup.");
            }
        }

        if (empty($okay)) $okay="no";

        if ($okay != "yes") {
            $output = "$header<center><hr>

                <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
                align=\"center\"><tr><td>
                <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
                <tr align=\"center\">
                <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
                size=\"1\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\"><B>PhotoPost Add a Category</font>
                </font></td>
                </tr>
                <tr id=\"cat\">
                <td bgcolor=\"#f7f7f7\"><b>
                <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$adminmenu</b></font></td></tr>
                <tr><td bgcolor=\"#f7f7f7\"><center><Br>
                <font size=\"2\" face=\"verdana\">You are about to delete the <b>\"$usergroup\"</b> usergroup.  Please note that if you have
                any users that are set to this
                usergroup, you should change those users to a different group BEFORE you delete this one or they will be unable to
                login to upload photos or post comments.<p>

                <form action=\"".$Globals{'maindir'}."/adm-userg.php\" method=\"POST\">
                <input type=\"hidden\" name=\"groupid\" value=\"$usergroupid\">
                <input type=\"hidden\" name=\"do\" value=\"delete\">
                <input type=\"hidden\" name=\"okay\" value=\"yes\">
                <input type=\"hidden\" name=\"ppaction\" value=\"usergroups\">
                <input type=\"submit\"
                value=\"Go ahead and delete this usergroup.\"></form></td></tr></table></td></tr></table>";

            print $output;
            exit;
        }
        else {
            $query = "DELETE FROM usergroups WHERE groupid=$usergroupid";
            $resulta = mysql_query_eval($query,$link);

            forward( $Globals{'maindir'}."/adm-userg.php?ppaction=usergroups", "Processing complete!" );
            exit;
        }
    }

    if ($do == "refresh-vb") {
        if ( empty($okay) ) $okay="no";
        if ($okay != "yes") {
            $output = "$header<center><hr>
                <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
                align=\"center\"><tr><td>
                <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
                <tr align=\"center\">
                <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
                size=\"1\"><font size=\"2\" face=\"verdana\"><b>PhotoPost Refresh Usergroups</font>
                </font></td>
                </tr>
                <tr id=\"cat\">
                <td bgcolor=\"#f7f7f7\"><b>
                <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$adminmenu</b></font></td></tr>
                <tr><td bgcolor=\"#f7f7f7\"><center><Br>
                <font size=\"2\" face=\"verdana\">You are about to refresh your forum usergroups.  This is necessary whenever you
                add or delete usergroups within your forum software.  After you refresh, <b>please double-check your PhotoPost usergroup
                settings</b> to ensure that they are correct!

                <form action=\"".$Globals{'maindir'}."/adm-userg.php\" method=\"POST\">
                <input type=\"hidden\" name=\"do\" value=\"refresh-vb\">
                <input type=\"hidden\" name=\"okay\" value=\"yes\">
                <input type=\"hidden\" name=\"ppaction\" value=\"usergroups\">
                <input type=\"submit\" value=\"Go ahead and refresh usergroups.\"></form></td></tr></table></td></tr></table>";

            print "$output<p>".$Globals{'cright'}."<p>$footer";
            exit;
        }
        else {
            //phpBB2
            if ($Globals{'vbversion'} == "phpBB2") {
                if ( !empty($Globals{'dprefix'}) ) {
                    $grouptable=$Globals{'dprefix'} ."_groups";
                }
                else
                    $grouptable="groups";

                $query = "SELECT group_id,group_name FROM $grouptable WHERE group_type=2 OR group_single_user=0";
                $readug = mysql_query_eval($query, $db_link);
                $rcount = mysql_num_rows($readug);

                if ($rcount < 1) {
                    dieWell("Error: Are you certain that your database prefix is set properly?<p>I cannot see your
                        phpBB2 \"<b>groups</b>\" table.<p>The prefix setting adds characters to the front of phpBB2
                        table names - please doublecheck this.");
                    exit;
                }

                $deleteq = "DELETE FROM usergroups";
                $resultd = mysql_query_eval($deleteq, $link);

                if ( file_exists( "usergroups-phpbb2.sql")  ) {
                    $filearray = file( "usergroups-phpbb2.sql" );

                while ( list($num, $query) = each($filearray) ) {
                        if ($query != "") {
                            $query = str_replace( ";", "", $query);
                            $setup = mysql_query_eval($query, $link);
                        }
                    }
                }
                else {
                    dieWell("usergroups-phpbb2.sql is missing.");
                    exit;
                }

                while ( $row = mysql_fetch_row( $readug ) ) {
                    list( $groupid, $title ) = $row;

                    $title = addslashes( $title );
                    $query = "INSERT INTO usergroups VALUES($groupid,'$title','0','0','0','0','0','1','1')";
                    $setug = mysql_query_eval($query, $link);
                }
            }

            //# ubb threads
            if ($Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
                $query = "DELETE FROM usergroups";
                $resulta = mysql_query_eval($query, $link);

                $query = "SELECT G_Id, G_Name FROM w3t_Groups";
                $readug = mysql_query_eval($query, $db_link);

                while ( $row = mysql_fetch_row( $readug ) ) {
                    list( $groupid, $title ) = $row;

                    $title = addslashes( $title );

                    if ( $title == "Administrators" ) {
                        $query="INSERT INTO usergroups VALUES($groupid,'$title','1','1','1','0','0','1','1')";
                    }
                    elseif ( $title == "Banned" || $title == "Unregistered" ) {
                        $query="INSERT INTO usergroups VALUES($groupid,'$title','0','0','0','0','0','0','0')";
                    }
                    else {
                        $query="INSERT INTO usergroups VALUES($groupid,'$title','0','1','1','0','0','1','1')";
                    }
                    $setug = mysql_query_eval($query,$link);
                }
            }

            //# vbulletin
            if ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
                $query = "DELETE FROM usergroups";
                $resulta = mysql_query_eval($query,$link);

                $query = "SELECT usergroupid,title,cancontrolpanel,canpostnew FROM usergroup";
                $readug = mysql_query_eval($query,$db_link);

                while ( $row = mysql_fetch_row( $readug ) ) {
                    list( $groupid, $title, $cancontrol, $canpost ) = $row;
                    $title = addslashes( $title );

                    $query = "INSERT INTO usergroups VALUES($groupid,'$title',$cancontrol,$canpost,$canpost,'0','0',$canpost,$canpost)";
                    $setug = mysql_query_eval($query,$link);
                }
            }

            forward( $Globals{'maindir'}."/adm-userg.php?ppaction=usergroups", "Processing complete!");
            exit;
        }
    }

    if ($do == "process") {  //# Save input usergroups form to DB
        $admincheck=0;

        foreach($HTTP_POST_VARS as $id=>$setting) {
            //$setting=~ s/\\+$//g;
            //$setting=~ s/\/+$//g;
            $name = explode("-", $id);
            $dbid = $name[1];

            if ($name[0] == "cpaccess") {
                if ($setting == 1) {
                    $admincheck=1;
                }
            }
        }

        if ($admincheck == 0) {
            dieWell("At least one usergroup must have Admin Access.");
            exit;
        }

        foreach($HTTP_POST_VARS as $id=>$setting) {
            //$setting=~ s/\\+$//g;
            //$setting=~ s/\/+$//g;
            $name = explode("-", $id);
            $dbid = $name[1];

            if ($dbid != "") {
                $setting = addslashes( $setting );

                $query = "UPDATE usergroups SET ".$name[0]."='$setting' WHERE groupid=$dbid";
                //print "$query<br>";
                $resulta = mysql_query_eval($query,$link);
            }
        }

        forward( $Globals{'maindir'}."/adm-userg.php?ppaction=usergroups", "Processing complete!" );
        exit;
    }

    $output = "$header<center><hr>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\"
        face=\"verdana\"><b>PhotoPost UserGroup editor</font>
        </font></td>
        </tr>
        <form method=\"POST\" action=\"".$Globals{'maindir'}."/adm-userg.php\">
        <tr id=\"cat\">

        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$adminmenu</b></font></td></tr>
        <tr><td bgcolor=\"".$Globals{'maincolor'}."\"><center><Br>

        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"><tr><Td>
        <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\"><tr><Th bgcolor=\"#FFFFFF\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">UserGroup
        name</th>
        <Th bgcolor=\"#FFFFFF\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Admin Access?</font></th>
        <Th bgcolor=\"#FFFFFF\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Allow Uploads?</font></th>
        <Th bgcolor=\"#FFFFFF\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Allow comments?</font></th>
        <Th bgcolor=\"#FFFFFF\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Disk Space Limit (in kB)</font></th>
        <Th bgcolor=\"#FFFFFF\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Upload File Size Limit (in KB)</font></th>
        <Th bgcolor=\"#FFFFFF\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Allow edit own photos?</font></th>
        <Th bgcolor=\"#FFFFFF\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Allow edit own posts?</font></th></tr>";

    $query = "SELECT groupid,groupname,cpaccess,uploads,comments,diskspace,uploadsize,editpho,editposts FROM usergroups ORDER BY groupid";
    $resulta = mysql_query_eval($query,$link);

    while ( $row = mysql_fetch_row($resulta) ) {
        list($uggroupid,$uggroupname,$ugcpaccess,$uguploads,$ugcomments,$ugdiskspace,$uguploadsize,$editpho,$editposts) = $row;

        if ($ugcpaccess == "1")
            $cpaccess_opts= "<option selected value=\"1\">yes</option><option value=\"0\">no</option>";
        else
            $cpaccess_opts= "<option selected value=\"0\">no</option><option value=\"1\">yes</option>";

        if ($editpho == "1")
            $editpho_opts= "<option selected value=\"1\">yes</option><option value=\"0\">no</option>";
        else
            $editpho_opts= "<option selected value=\"0\">no</option><option value=\"1\">yes</option>";

        if ($editposts == "1")
            $editposts_opts= "<option selected value=\"1\">yes</option><option value=\"0\">no</option>";
        else
            $editposts_opts= "<option selected value=\"0\">no</option><option value=\"1\">yes</option>";

        if ($uguploads == "1")
            $uploads_opts= "<option selected value=\"1\">yes</option><option value=\"0\">no</option>";
        else
            $uploads_opts= "<option selected value=\"0\">no</option><option value=\"1\">yes</option>";

        if ($ugcomments == "1")
            $comments_opts= "<option selected value=\"1\">yes</option><option value=\"0\">no</option>";
        else
            $comments_opts= "<option selected value=\"0\">no</option><option value=\"1\">yes</option>";

        if ($Globals{'vbversion'} == "Internal" || $Globals{'vbversion'} == "phpBB" || $Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
            $addhtml = "<font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\"><B>(<a href=\"".$Globals{'maindir'}."/adm-userg.php?ppaction=usergroups&do=add\">Add a New
                Usergroup</a>)</b></font>";
        }

        if ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0" || $Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6" || $Globals{'vbversion'} == "phpBB2") {
            $addhtml = "<font size=\"2\" face=\"verdana\"><B>(<a
                href=\"".$Globals{'maindir'}."/adm-userg.php?ppaction=usergroups&do=refresh-vb\">Refresh usergroups from your forum software?</a>)</b></font>";
        }

        $output .= "<Tr><Td bgcolor=\"#FFFFFF\"><center><input type=\"text\" size=\"25\" maxlength=\"25\" value=\"$uggroupname\"
            name=\"groupname-$uggroupid\">";

        if ($Globals{'vbversion'} == "Internal" || $Globals{'vbversion'} == "phpBB" || $Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
            if ($uggroupid > 5) {
                $output .= "<Br>(<font size=\"1\" face=\"verdana\"><A
                    href=\"".$Globals{'maindir'}."/adm-userg.php?ppaction=usergroups&groupid=$uggroupid&do=delete&usergroup=$uggroupname\">delete</a>)";
            }
        }

        $output .= "</td>
            <Td bgcolor=\"#FFFFFF\"><center><select name=\"cpaccess-$uggroupid\">$cpaccess_opts</select></td>
            <Td bgcolor=\"#FFFFFF\"><center><select name=\"uploads-$uggroupid\">$uploads_opts</select></td>
            <Td bgcolor=\"#FFFFFF\"><center><select name=\"comments-$uggroupid\">$comments_opts</select></td>
            <Td bgcolor=\"#FFFFFF\"><center><input type=\"text\" size=\"8\" value=\"$ugdiskspace\" name=\"diskspace-$uggroupid\"></td>
            <Td bgcolor=\"#FFFFFF\"><center><input type=\"text\" size=\"8\" value=\"$uguploadsize\" name=\"uploadsize-$uggroupid\"></td>
            <Td bgcolor=\"#FFFFFF\"><center><select name=\"editpho-$uggroupid\">$editpho_opts</select></td>
            <Td bgcolor=\"#FFFFFF\"><center><select name=\"editposts-$uggroupid\">$editposts_opts</select></td>
            </tr>";
    }

    $output .= "</table></td></tr></table><p><Center>
        <input type=\"hidden\" value=\"usergroups\" name=\"ppaction\">
        <input type=\"hidden\" value=\"process\" name=\"do\">
        $addhtml<p>
        <input value=\"Save Changes\" type=\"submit\">
        </td></tr></table></td></tr></table>";

    print "$output<p>".$Globals{'cright'}."<p>$footer";
    exit;
}

dieWell("Usergroups called improperly!");

?>

