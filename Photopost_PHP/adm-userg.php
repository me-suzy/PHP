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

if ($ppaction == "usergroups") {
    if ($do == "add") {
        $query = "INSERT INTO usergroups (groupid,groupname,uploads,comments,diskspace,uploadsize) values(NULL,'Default','0','0','0','0')";
        $resulta = ppmysql_query($query,$link);

        forward( "{$Globals['maindir']}/adm-userg.php?ppaction=usergroups");
        exit;
    }

    if ($do == "delete") {
        $usergroupid=$groupid;

        if ($Globals['vbversion'] == "Internal") {
            if ($usergroupid < 6) {
                diewell("You can't delete this usergroup.");
            }
        }
        else {
            if ($usergroupid < 5) {
                diewell("You can't delete this usergroup.");
            }
        }

        if (empty($okay)) $okay="no";

        if ($okay != "yes") {
            printheader( 0, "PhotoPost User Groups" );
            
            $output = "<center>

                <hr><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
                align=\"center\"><tr><td>
                <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
                <tr align=\"center\">
                <td colspan=\"4\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
                size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\"><b>PhotoPost Add a Category</font>
                </font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['headcolor']}\"><b>
                <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\">$adminmenu</b></font></td></tr>
                <tr><td bgcolor=\"{$Globals['maincolor']}\"><center><br />
                <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\">You are about to delete the <b>\"$usergroup\"</b> usergroup.  Please note that if you have
                any users that are set to this
                usergroup, you should change those users to a different group BEFORE you delete this one or they will be unable to
                login to upload photos or post comments.<p>

                <form action=\"{$Globals['maindir']}/adm-userg.php\" method=\"POST\">
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
            $resulta = ppmysql_query($query,$link);

            forward( "{$Globals['maindir']}/adm-userg.php?ppaction=usergroups", "Processing complete!" );
            exit;
        }
    }

    if ($do == "refresh-vb") {
        if ( empty($okay) ) $okay="no";
        if ($okay != "yes") {
            printheader( 0, "PhotoPost USer Groups" );
            
            $output = "<center>
                <hr><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
                align=\"center\"><tr><td>
                <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
                <tr align=\"center\">
                <td colspan=\"4\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
                size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><b>PhotoPost Refresh Usergroups</font>
                </font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['headcolor']}\"><b>
                <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\">$adminmenu</b></font></td></tr>
                <tr><td bgcolor=\"{$Globals['maincolor']}\"><center><br />
                <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\">You are about to refresh your forum usergroups.  This is necessary whenever you
                add or delete usergroups within your forum software.  After you refresh, <b>please double-check your PhotoPost usergroup
                settings</b> to ensure that they are correct!

                <form action=\"{$Globals['maindir']}/adm-userg.php\" method=\"POST\">
                <input type=\"hidden\" name=\"do\" value=\"refresh-vb\">
                <input type=\"hidden\" name=\"okay\" value=\"yes\">
                <input type=\"hidden\" name=\"ppaction\" value=\"usergroups\">
                <input type=\"submit\" value=\"Go ahead and refresh usergroups.\"></form></td></tr></table></td></tr></table>";

            print "$output<p>{$Globals['cright']}<p>";
            printfooter();
            
            exit;
        }
        else {
            //phpBB2
            if ($Globals['vbversion'] == "phpBB2") {
                if ( !empty($Globals['dprefix']) ) {
                    $grouptable="{$Globals['dprefix']}groups";
                }
                else
                    $grouptable="groups";

                $query = "SELECT group_id,group_name FROM $grouptable WHERE group_type=2 OR group_single_user=0";
                $readug = ppmysql_query($query, $db_link);
                $rcount = mysql_num_rows($readug);

                if ($rcount < 1) {
                    diewell("Error: Are you certain that your database prefix is set properly?<p>I cannot see your
                        phpBB2 \"<b>groups</b>\" table.<p>The prefix setting adds characters to the front of phpBB2
                        table names - please doublecheck this.");
                    exit;
                }

                $deleteq = "DELETE FROM usergroups";
                $resultd = ppmysql_query($deleteq, $link);

                if ( file_exists( "usergroups-phpbb2.sql")  ) {
                    $filearray = file( "usergroups-phpbb2.sql" );

                while ( list($num, $query) = each($filearray) ) {
                        if ($query != "") {
                            $query = str_replace( ";", "", $query);
                            $setup = ppmysql_query($query, $link);
                        }
                    }
                }
                else {
                    diewell("usergroups-phpbb2.sql is missing.");
                    exit;
                }

                while ( list( $groupid, $title ) = mysql_fetch_row( $readug ) ) {
                    $title = addslashes( $title );
                    $query = "INSERT INTO usergroups (groupid,groupname,cpaccess,uploads,comments,diskspace,uploadsize,editpho,editposts) VALUES($groupid,'$title','0','0','0','0','0','1','1')";
                    $setug = ppmysql_query($query, $link);
                }
                ppmysql_free_result( $readug );
            }

            //# ubb threads
            if ($Globals['vbversion'] == "w3t" || $Globals['vbversion'] == "w3t6") {
                $query = "DELETE FROM usergroups";
                $resulta = ppmysql_query($query, $link);

                if ( !empty( $Globals['dprefix'] ) ) {
                    $gtable = "{$Globals['dprefix']}Groups";
                }
                else {
                    $gtable = "w3t_Groups";
                }

                $query = "SELECT G_Id, G_Name FROM $gtable";
                $readug = ppmysql_query($query, $db_link);

                while ( list( $groupid, $title ) = mysql_fetch_row( $readug ) ) {
                    $title = addslashes( $title );

                    if ( $title == "Administrators" ) {
                        $query="INSERT INTO usergroups (groupid,groupname,cpaccess,uploads,comments,diskspace,uploadsize,editpho,editposts) VALUES($groupid,'$title','1','1','1','0','0','1','1')";
                    }
                    elseif ( $title == "Banned" || $title == "Unregistered" ) {
                        $query="INSERT INTO usergroups (groupid,groupname,cpaccess,uploads,comments,diskspace,uploadsize,editpho,editposts) VALUES($groupid,'$title','0','0','0','0','0','0','0')";
                    }
                    else {
                        $query="INSERT INTO usergroups (groupid,groupname,cpaccess,uploads,comments,diskspace,uploadsize,editpho,editposts) VALUES($groupid,'$title','0','1','1','0','0','1','1')";
                    }
                    $setug = ppmysql_query($query,$link);
                }
                ppmysql_free_result( $readug );
            }

            //# vbulletin
            if ($Globals['vbversion'] == "2.0.3" || $Globals['vbversion'] == "2.2.0") {
                $query = "DELETE FROM usergroups";
                $resulta = ppmysql_query($query,$link);

                $query = "SELECT usergroupid,title,cancontrolpanel,canpostnew FROM usergroup";
                $readug = ppmysql_query($query,$db_link);

                while ( list( $groupid, $title, $cancontrol, $canpost ) = mysql_fetch_row( $readug ) ) {
                    $title = addslashes( $title );

                    $query = "INSERT INTO usergroups (groupid,groupname,cpaccess,uploads,comments,diskspace,uploadsize,editpho,editposts) VALUES($groupid,'$title',$cancontrol,$canpost,$canpost,'0','0',$canpost,$canpost)";
                    $setug = ppmysql_query($query,$link);
                }
                ppmysql_free_result( $readug );
            }

            forward( "{$Globals['maindir']}/adm-userg.php?ppaction=usergroups", "Processing complete!");
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
            diewell("At least one usergroup must have Admin Access.");
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
                //print "$query<br />";
                $resulta = ppmysql_query($query,$link);
            }
        }

        forward( "{$Globals['maindir']}/adm-userg.php?ppaction=usergroups", "Processing complete!" );
        exit;
    }

    printheader( 0, "PhotoPost User Groups" );
    
    $output = "<center>
        <hr><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\"
        face=\"{$Globals['mainfonts']}\"><b>PhotoPost UserGroup editor</font>
        </font></td>
        </tr>
        <form method=\"POST\" action=\"{$Globals['maindir']}/adm-userg.php\">
        <tr>

        <td bgcolor=\"{$Globals['headcolor']}\"><b>
        <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\">$adminmenu</b></font></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\"><center><br />

        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"{$Globals['bordercolor']}\"><tr><td>
        <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\"><tr><Th bgcolor=\"{$Globals['headcolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">UserGroup
        name</th>
        <Th bgcolor=\"{$Globals['headcolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Admin Access?</font></th>
        <Th bgcolor=\"{$Globals['headcolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Allow Uploads?</font></th>
        <Th bgcolor=\"{$Globals['headcolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Allow comments?</font></th>
        <Th bgcolor=\"{$Globals['headcolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Disk Space Limit (in kB)</font></th>
        <Th bgcolor=\"{$Globals['headcolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Upload File Size Limit (in KB)</font></th>
        <Th bgcolor=\"{$Globals['headcolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Allow edit own photos?</font></th>
        <Th bgcolor=\"{$Globals['headcolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Allow edit own posts?</font></th></tr>";

    $query = "SELECT groupid,groupname,cpaccess,uploads,comments,diskspace,uploadsize,editpho,editposts FROM usergroups ORDER BY groupid";
    $resulta = ppmysql_query($query,$link);

    while ( list($uggroupid,$uggroupname,$ugcpaccess,$uguploads,$ugcomments,$ugdiskspace,$uguploadsize,$editpho,$editposts) = mysql_fetch_row($resulta) ) {
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

        if ($Globals['vbversion'] == "Internal" || $Globals['vbversion'] == "phpBB" || $Globals['vbversion'] == "w3t" || $Globals['vbversion'] == "w3t6") {
            $addhtml = "<font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\"><b>(<a href=\"{$Globals['maindir']}/adm-userg.php?ppaction=usergroups&do=add\">Add a New
                Usergroup</a>)</b></font>";
        }

        if ($Globals['vbversion'] == "2.0.3" || $Globals['vbversion'] == "2.2.0" || $Globals['vbversion'] == "w3t" || $Globals['vbversion'] == "w3t6" || $Globals['vbversion'] == "phpBB2") {
            $addhtml = "<font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><b>(<a
                href=\"{$Globals['maindir']}/adm-userg.php?ppaction=usergroups&do=refresh-vb\">Refresh usergroups from your forum software?</a>)</b></font>";
        }

        $output .= "<tr><td bgcolor=\"{$Globals['maincolor']}\"><center><input type=\"text\" size=\"25\" maxlength=\"25\" value=\"$uggroupname\"
            name=\"groupname-$uggroupid\" class=\"bginput\">";

        if ($Globals['vbversion'] == "Internal" || $Globals['vbversion'] == "phpBB" || $Globals['vbversion'] == "w3t" || $Globals['vbversion'] == "w3t6") {
            if ($uggroupid > 5) {
                $output .= "<br />(<font size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\"><a
                    href=\"{$Globals['maindir']}/adm-userg.php?ppaction=usergroups&groupid=$uggroupid&do=delete&usergroup=$uggroupname\">delete</a>)";
            }
        }

        $output .= "</td>
            <td bgcolor=\"{$Globals['maincolor']}\"><center><select name=\"cpaccess-$uggroupid\">$cpaccess_opts</select></td>
            <td bgcolor=\"{$Globals['maincolor']}\"><center><select name=\"uploads-$uggroupid\">$uploads_opts</select></td>
            <td bgcolor=\"{$Globals['maincolor']}\"><center><select name=\"comments-$uggroupid\">$comments_opts</select></td>
            <td bgcolor=\"{$Globals['maincolor']}\"><center><input type=\"text\" size=\"8\" value=\"$ugdiskspace\" name=\"diskspace-$uggroupid\" class=\"bginput\"></td>
            <td bgcolor=\"{$Globals['maincolor']}\"><center><input type=\"text\" size=\"8\" value=\"$uguploadsize\" name=\"uploadsize-$uggroupid\" class=\"bginput\"></td>
            <td bgcolor=\"{$Globals['maincolor']}\"><center><select name=\"editpho-$uggroupid\">$editpho_opts</select></td>
            <td bgcolor=\"{$Globals['maincolor']}\"><center><select name=\"editposts-$uggroupid\">$editposts_opts</select></td>
            </tr>";
    }
    ppmysql_free_result( $resulta );    

    $output .= "</table></td></tr></table><p><center>
        <input type=\"hidden\" value=\"usergroups\" name=\"ppaction\">
        <input type=\"hidden\" value=\"process\" name=\"do\">
        $addhtml<p>
        <input value=\"Save Changes\" type=\"submit\">
        </td></tr></table></td></tr></table>";

    print "$output<p>{$Globals['cright']}<p>";
    printfooter();
    
    exit;
}

diewell("Usergroups called improperly!");

?>
