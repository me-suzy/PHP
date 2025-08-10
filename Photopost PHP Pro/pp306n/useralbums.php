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
include("adm-cinc.php");
include("login-inc.php");

authenticate();

if ( $gologin != 1 ) {
    if ( $nopost == 1 ) {
        dieWell("Sorry, you don't have permission to create albums.");
        exit;
    }
    if ( $userup == 2 ) {
        dieWell("Sorry, but you have not verified your account yet.<p>You must do so before being able to manage your albums.");
    }
}

topmenu();

if ( empty($ppaction) ) $ppaction="albums";
if ( empty($do) ) $do="";

if ( $ppaction == "albums" ) {
    //# Generate the edit useralbums HTML form

    $output = "$header<center><hr>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\"
        face=\"verdana\"><b>PhotoPost Album Editor</font>
        </font></td>
        </tr>
        <tr id=\"album\">
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$menu</b></font></td></tr>
        <tr><td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana\"><Br><ul>(<a
        href=\"".$Globals{'maindir'}."/useralbums.php?ppaction=addalbum\">Add Album</a>)</ul>";

    albumli( $userid );

    $output.= "<p><center></td></tr></table></td></tr></table><p>".$Globals{'cright'}."$footer";

    print $output;
}

if ($ppaction == "addalbum") { //# Add a album
    if ( !empty($do) ) {
        // Process a user's album submission
        $albumname = addslashes( $albumname );
        $albumdesc = addslashes( $albumdesc );

        $query = "INSERT INTO useralbums values(NULL,'$albumname',$userid,'$albumdesc')";
        $setug = mysql_query_eval($query,$link);
        $thealbumid = mysql_insert_id( $link );

        if ( $thealbumid < 3000 ) {
            $query = "UPDATE useralbums SET id='3000' WHERE id=$thealbumid";
            $setug = mysql_query_eval($query, $link);
            $thealbumid = 3000;
        }

        $newdir = $Globals{'datafull'}."$thealbumid";
        if ( !mkdir( $newdir, 0755 ) ) {
            dieWell( "Error creating directory $newdir. Please check your system." );
            exit;
        }
        chmod( $newdir, 0777 );

        forward( $Globals{'maindir'}."/useralbums.php?ppaction=albums", "Processing complete!" );
        exit;
    }

    //# Print out the Add a album HTML form
    $output = "$header<center><hr>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\"
        face=\"verdana\"><b>PhotoPost Add a album</font>
        </font></td>
        </tr>
        <tr id=\"album\">
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$menu</b></font></td></tr>
        <tr><td bgcolor=\"".$Globals{'maincolor'}."\"><center><Br>

        <table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"><form method=\"post\"
        action=\"".$Globals{'maindir'}."/useralbums.php\">
        <tr><Td bgcolor=\"".$Globals{'headcolor'}."\">
        <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\">
        <tr><Th bgcolor=\"#F7f7f7\"><font face=\"verdana\" size=\"2\">Album Name</font></th>
        <Th bgcolor=\"#F7f7f7\"><font face=\"verdana\" size=\"2\">Album Description</font></th></tr><Tr>
        <Td
        bgcolor=\"#f7f7f7\"><input type=\"text\" size=\"50\"
        value=\"\" name=\"albumname\"></td><Td bgcolor=\"#f7f7f7\"><input type=\"text\" size=\"50\" value=\"\" name=\"albumdesc\"></td>
        </tr>
        </table></td></tr></table><p>
        <input type=\"hidden\" name=\"albumid\" value=\"$userid\">
        <input type=\"hidden\" name=\"ppaction\" value=\"addalbum\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <input type=\"submit\" value=\"Add album\"></form></td></tr></table></td></tr></table><p>".$Globals{'cright'}."
        $footer";

    print $output;
}

if ($ppaction == "editalbum") {
    if ( $do == "process" ) {
        $albumname = addslashes( $albumname );
        $albumdesc = addslashes( $albumdesc );

        $query = "UPDATE useralbums SET albumname='$albumname', description='$albumdesc' WHERE id='$albumid'";
        $setug = mysql_query_eval($query,$link);

        forward( $Globals{'maindir'}."/useralbums.php?ppaction=albums", "Processing complete!" );
        exit;
    }

    $output = "$header<center><hr>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\"
        face=\"verdana\"><B>PhotoPost album Editor</font>
        </font></td>
        </tr>
        <tr id=\"album\">
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$menu</b></font></td></tr>
        <tr><td bgcolor=\"".$Globals{'maincolor'}."\"><center><Br>
        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"><form method=\"post\"
        action=\"".$Globals{'maindir'}."/useralbums.php\"><tr><Td>
        <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\"><tr>
        <Th bgcolor=\"#F7f7f7\"><font face=\"verdana\" size=\"2\">Album Name</font></th>
        <Th bgcolor=\"#F7f7f7\"><font face=\"verdana\" size=\"2\">Album Description</font></th></tr>";


    $query = "SELECT id,albumname,description FROM useralbums WHERE id=$albumid";
    $boards = mysql_query_eval($query,$link);
    $row = mysql_fetch_row($boards);

    list($id,$albumname,$albumdesc) = $row;

    $albumname = str_replace( "\"", "&quot", $albumname);
    $albumdesc = str_replace( "\"", "&quot", $albumdesc);

    $output .= "<Tr><Td
        bgcolor=\"#f7f7f7\"><input type=\"text\" size=\"50\"
        value=\"$albumname\" name=\"albumname\"></td><Td bgcolor=\"#f7f7f7\"><input type=\"text\" size=\"50\" value=\"$albumdesc\" name=\"albumdesc\"></td></tr>";

    $output .= "</table></td></tr></table><p>
        <input type=\"hidden\" name=\"albumid\" value=\"$id\">
        <input type=\"hidden\" name=\"ppaction\" value=\"editalbum\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <input type=\"submit\" value=\"Save Changes\"></form></td></tr></table></td></tr></table><p>".$Globals{'cright'}."$footer";

    print $output;
    exit;
}

if ($ppaction == "delalbum") {  //# Delete a album
    if ($do == "process") { //# Process delete album
        if ($albumid != 500 && $albumid != "0" && $albumid != "") {
            delete_cat($albumid);
        }
        else {
            dieWell("Invalid album ID.");
            exit;
        }

        $forwardid = $Globals{'maindir'}."/useralbums.php?ppaction=albums";
        forward( $forwardid, "Processing complete!" );
        exit;
    }

    //# Generate an 'are you sure' you want to delete? form...

    $querya="SELECT albumname FROM useralbums where id=$albumid";
    $albumq = mysql_query_eval($querya, $link);
    $albumr = mysql_fetch_array($albumq, MYSQL_ASSOC);
    $albumname = $albumr['albumname'];
    mysql_free_result($albumq);

    $output="$header<center><hr>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\"
        face=\"verdana\"><B>PhotoPost Add a album</font>
        </font></td>
        </tr>
        <tr id=\"album\">
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$menu</b></font></td></tr>
        <tr><td bgcolor=\"#f7f7f7\"><center><font face=\"verdana\" size=\"2\"><Br>
        You're about to delete the album called \"$albumname\", and <b>ALL PHOTOS AND COMMENTS</b> within the album.<p>
        Are you sure you want to do that?
        <form action=\"".$Globals{'maindir'}."/useralbums.php\" method=\"post\">
        <input type=\"hidden\" name=\"albumid\" value=\"$albumid\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <input type=\"hidden\" name=\"ppaction\" value=\"delalbum\">
        <input type=\"submit\" value=\"I'm sure, delete the album.\"></form></font></td></tr></table></td></tr></table>";

    print $output;
}

?>
