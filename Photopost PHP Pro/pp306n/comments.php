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

$output = "";

$gologin = authenticate();

list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
$mon = $mon + 1;

$query = "SELECT cat FROM photos WHERE id=$photo";
$resulta = mysql_query_eval($query,$link);
$row = mysql_fetch_array($resulta);
$thiscat = $row['cat'];
mysql_free_result($resulta);

if ( $ugpost{$thiscat} == 1 ) {
     dieWell("You don't have permission to post comments for images in this category.");
     exit;
}

if ($adminedit == 0) {
    if ($Globals{'allowpost'} == "no") {
        dieWell("User comments not allowed.");
    }
}

$querystring = findenv("QUERY_STRING");
if ( ($Globals{'unregcom'} != "yes" && $gologin==1) || $querystring == "gologin" ) {
    $furl=$Globals{'maindir'};
    $furl= str_replace( $Globals{'domain'}, "", $furl );
    $furl="$furl/comments.php?photo=$photo&cedit=$cedit";
    login($furl);
    exit;
}

if ($gologin != 1) {
    if ($nopost == 1) {
        dieWell("Sorry, you don't have permission to post/edit.<p>If you tried to edit, you might not be the post's
            author or editing may<Br> be disabled for your usergroup.");
        exit;
    }
    if ($usercomment == 2) {
        dieWell("Sorry, you don't have permission to post comments.");
    }
}

if (empty($notify)) $notify="";

if ($Globals{'usenotify'} == "yes") {
    if ($notify == "on") {
        $query = "INSERT INTO notify values(NULL,$userid,$photo)";
        $resulta = mysql_query_eval($query,$link);

        forward( $Globals{'maindir'}."/showphoto.php?photo=$photo", "Email notification enabled." );
        exit;
    }

    if ($notify == "off") {
        $query = "DELETE FROM notify WHERE id=$notifyid AND userid=$userid";
        $resulta = mysql_query_eval($query,$link);

        forward( $Globals{'maindir'}."/showphoto.php?photo=$photo", "Email notification disabled." );
        exit;
    }
}

if ( IsSet($photo) ) {
    if ( !IsSet($post) ) {
        $erating=""; $ecomments="";

        if ( IsSet($cedit) && $cedit != "" ) {
            $query = "SELECT userid,rating,comment FROM comments WHERE id=$cedit LIMIT 1";
            $resulta = mysql_query_eval($query,$link);
            $row = mysql_fetch_row($resulta);
            list( $cuserid, $erating, $ecomments ) = $row;

            if ( $userid != $cuserid || $userid < 1 ) {
                dieWell( "You do not have permission to edit this comment.");
                exit;
            }
        }

        topmenu();

        $output = "<head><title>".$Globals{'galleryname'}." Comments</title>$headtags</head>$header<table
            cellpadding=\"0\" cellspacing=\"0\" border=\"0\" height=\"40\" width=\"".$Globals{'tablewidth'}."\"><Tr>
            <Td valign=\"center\" width=\"50%\">&nbsp;</td><td width=\"50%\" align=\"right\" valign=\"center\">
            <font face=\"verdana, arial\" size=\"2\">$menu&nbsp;</font>
            </td></tr></table>

            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"
            width=\"".$Globals{'tablewidth'}."\" align=\"center\"><tr><td>
            <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr align=\"center\">
            <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font
            face=\"verdana,arial,helvetica\" color=\"".$Globals{'headfontcolor'}."\" size=\"2\"><b>
            ".$Globals{'galleryname'}." Comments</font>
            </font></td></tr>
            <form name=\"theform\" method=\"post\" action=\"".$Globals{'maindir'}."/comments.php\">";

        $output .= "<tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">
            Username</font></td><td colspan=\"2\" bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana,arial\" color=\"".$Globals{'maintext'}."\">$username
            </font></td></tr>
            <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Rate this photo overall</font></td>
            <td colspan=\"2\" bgcolor=\"".$Globals{'maincolor'}."\"><select name=\"rating\"><option
            selected>$erating</option>
            <option value=\"5\">5 - Excellent</option>
            <option value=\"4\">4 - Great</option>
            <option value=\"3\">3 - Good</option>
            <option value=\"2\">2 - Fair</option>
            <option value=\"1\">1 - Poor</option>
            </select></td></tr>
            <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Comments:</td><td
            bgcolor=\"".$Globals{'maincolor'}."\"><textarea
            name=\"message\" cols=\"50\" rows=\"10\">$ecomments</textarea></td>";

        $query = "SELECT bigimage,approved,cat,title,userid FROM photos WHERE id=$photo LIMIT 1";
        $resulta = mysql_query_eval($query,$link);
        $row = mysql_fetch_row($resulta);
        list( $bigimage, $approved, $cat, $title, $theuser ) = $row;

        if ($approved == 1) {
            if ($bigimage != "") {
                $theext = substr($bigimage,strlen($bigimage) - 4,4);
                $filename = $bigimage;
                $filename = str_replace( $theext, "", $filename);
                $output .= "<Td bgcolor=\"".$Globals{'maincolor'}."\"><center>";

                if ( file_exists($Globals{'datafull'}."$cat/$theuser$filename-thumb$theext") ) {
                    $output .= "<img width=\"100\" src=\"".$Globals{'datadir'}."/$cat/$theuser$filename-thumb$theext\"><br>";
                }
                else {
                    $output .= "<img width=\"100\" src=\"".$Globals{'datadir'}."/$cat/$theuser$filename-thumb.jpg\"><br>";
                }

                $output .= "<font size=\"2\" face=\"verdana,arial\" color=\"".$Globals{'maintext'}."\">$title</font></td>";
            }
            else {
                print "An error occured.  You tried to submit comments for a picture that doesn't exist in our database.";
                exit;
            }
        }

        $output .= "</tr>";
        if ($cedit != "") {
            $output .= "<Tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maincolor'}."\" face=\"verdana,arial\">Delete post?</font></td><Td colspan=\"2\"
                bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\"
                color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Only check this box if you want to completely delete this post: <input type=\"checkbox\" name=\"delete\" value=\"yes\"></td></tr>";
        }

        $inputcat=$cat;
        $inputedit=$cedit;

        $output .= "<Center>
            <Tr><Td colspan=\"3\" bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana,arial\"><center>
            <input type=\"hidden\" name=\"cat\" value=\"$inputcat\">
            <input type=\"hidden\" name=\"password\" value=\"$password\">
            <input type=\"hidden\" name=\"puserid\" value=\"$theuser\">
            <input type=\"hidden\" name=\"photo\" value=\"$photo\">";

        if ($cedit == "") {
            $output .= "<input type=\"hidden\" name=\"post\" value=\"new\"><input type=\"submit\" value=\"Submit Post\">";
        }
        else {
            $output .= "<input type=\"hidden\" name=\"postid\" value=\"$inputedit\">
                <Br><input type=\"hidden\" name=\"post\" value=\"cedit\"><input type=\"submit\" value=\"Submit Edit\">";
        }

        $output .= "</td></tr></table></td></tr></table><p>".$Globals{'cright'}."$footer";
        print $output;
    }
    else {
        // Go ahead and post the comment to the database. ##
        if ( $rating == "" ) $rating=0;
        if ( $username == "" ) $gologin=1;
        if ( !isset($message) ) $message="";
        if ( !isset($delete) ) $delete="no";
        $noinsert=0;

        if ( $message == "" && $rating == 0 ) {
            dieWell( "You did not fill in the comments or rating field." );
            exit;
        }

        $julian = mktime($hour,$min,$sec,$mon,$mday,$year);

        if ($post == "new") {
            $query = "SELECT userid,comment,rating FROM comments WHERE photo=$photo";
            $resultb = mysql_query_eval($query,$link);

            if ( $userid > 0 ) {
                while( $row = mysql_fetch_row($resultb) ) {
                    list ( $checkuid, $checkdup, $checkrating ) = $row;
                    if ( $checkdup == $message && $checkuid == $userid ) $noinsert=1;
                }
            }

            $query = "SELECT cat,title,userid FROM photos WHERE id=$photo";
            $resulta = mysql_query_eval($query,$link);
            $row = mysql_fetch_row($resulta);
            list ( $getcat, $gettitle, $getuserid ) = $row;

            if ($noinsert != 1) {
                $message = fixmessage ( $message );

                $username = addslashes( $username );
                $message = addslashes( $message );

                $query = "INSERT INTO comments values(NULL,'$username',$userid,$julian,$rating,'$message',$photo,$getcat)";
                $resulta = mysql_query_eval($query,$link);

                if ($Globals{'cpostcount'} == "yes") {
                    inc_user_posts();
                }

                if ($Globals{'usenotify'} == "yes") {
                    $queryc = "SELECT userid FROM notify WHERE photo=$photo";
                    $resultc = mysql_query_eval($queryc, $link);

                    if ( $resultc ) {
                        while( $row = mysql_fetch_row($resultc) ) {
                            $notify_user = $row[0];

                            list( $usernm, $useremail ) = get_username( $notify_user );

                            $email_from = "From: ".$Globals{'adminemail'};
                            $letter="$username has posted a reply about the following photo:

                                \"$gettitle\"
                                ".$Globals{'maindir'}."/showphoto.php?photo=$photo

                                If you no longer wish to be notified of replies about
                                the above photo, you can disable notification for it here:
                                ".$Globals{'maindir'}."/comments.php?notify=off&notifyid=$getuserid&photo=$photo

                                Thanks!

                                The ".$Globals{'webname'}." Team
                                ".$Globals{'domain'};

                            $subject="New Reply to $gettitle at ".$Globals{'webname'};
                            mail( $useremail, $subject, $letter, $email_from );
                        }
                    }
                }
            }
        }
        else {
            if ( $delete == "yes" ) {
                $query = "DELETE FROM comments WHERE id=$postid";
                $resulta = mysql_query_eval($query,$link);

                if ($Globals{'cpostcount'} == "yes") {
                    inc_user_posts( "minus" );
                }
            }
            else {
                $message = fixmessage( $message );
                $message = addslashes( $message );

                $query = "UPDATE comments SET rating=$rating, comment='$message' WHERE id=$postid";
                $resulta = mysql_query_eval($query,$link);

                $query = "UPDATE photos SET lastpost=$julian WHERE id=$photo";
                $resulta = mysql_query_eval($query,$link);
            }
        }

        // just to revalidate the rating, we need to recheck the rating for the post
        $query = "SELECT rating FROM comments WHERE photo=$photo AND rating != '0'";
        $resultb = mysql_query_eval($query,$link);

        $numrating=0; $sumrating=0; $averagerate=0;
        while( $row = mysql_fetch_row($resultb) ) {
            $numrating++;
            list ( $checkrating ) = $row;
            $sumrating = ($sumrating+$checkrating);
        }
        if ( $numrating != 0 && $sumrating != 0 ) $averagerate = round( $sumrating / $numrating );

        $query = "UPDATE photos SET rating=$averagerate WHERE id=$photo";
        $resulta = mysql_query_eval($query,$link);

        if ( $noinsert == 1 ) $text="You have a duplicate post, so it was not added.";
        else $text="Your post or edit was successful.";

        forward( $Globals{'maindir'}."/showphoto.php?photo=$photo", $text );
        exit;
    }

}
else {
    print "Invalid call to script.";
}

?>

