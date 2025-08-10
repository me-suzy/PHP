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

if ($Globals{'enablecard'} == "no") {
    dieWell("e-Cards are disabled");
    exit;
}

authenticate();

if ( IsSet($ecard) ) {
    $inputphoto=$ecard;

    if ($Globals{'cardreg'} == "yes") {
        if ( $gologin == 1 ) {
            $furl=$Globals{'maindir'};
            $furl= str_replace( $Globals{'domain'}, "", $furl);
            $furl="$furl/ecard.php?ecard=$phoedit";
            login($furl);
            exit;
        }
    }
}

if ( IsSet( $view ) ) {
    $cnum = explode( "-", $view );
    $carddate = $cnum[1];

    $query = "SELECT fromname,toname,subject,message,date,photoid FROM ecards WHERE date=$carddate LIMIT 1";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_row($resulta);

    list( $fromnname, $toname, $subject, $message, $date, $photoid) = $row;
    $inputphoto = $photoid;
}

if ( $send != "yes" ) {
    $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved FROM photos WHERE id=$inputphoto";
    $rows = mysql_query_eval($query,$link);

    while ( $result = mysql_fetch_row($rows) ) {
        list( $id, $user, $userid, $cat, $date, $title, $desc, $keywords, $bigimage, $width, $height, $filesize, $views, $medwidth, $medheight, $medsize, $approved ) = $result;

        $theext = substr( $bigimage, strlen($bigimage) - 4,4 );
        $filename = get_filename( $bigimage );

        if ( $approved == "1" ) {
            if ( $medsize > 0 ) {
                if ( $size != "big" ) {
                    $dispmed = "1";
                    $altlink = "<center><font size=\"2\" face=\"verdana\"><B>
                        <a href=\"".$Globals{'maindir'}."/showphoto.php?photo=$inputphoto&size=big\">View Larger
                        Image</a></b></font></center><Br>";
                }
                else {
                    $altlink = "<center><font size=\"2\" face=\"verdana\"><B><a
                        href=\"".$Globals{'maindir'}."/showphoto.php?photo=$inputphoto\">View Smaller Image</a></b></font></center><Br>";
                }
            }
            if ($Globals{'bigsave'} == "yes") {
                if ($dispmed > 0) {
                    $medsize = $medsize/1024;
                    $medsize = sprintf("%1.1f", $medsize);
                    $medsize = "$medsize\k";
                    $filesize = $filesize/1024;
                    $filesize = sprintf("%1.1f", $filesize);
                    $filesize = "<A href=\"".$Globals{'maindir'}."/showphoto.php?photo=$id\">$medsize</a>, <A
                        href=\"".$Globals{'maindir'}."/showphoto.php?photo=$id&size=big\">$filesize\k</a>, $width X $height";
                    $imgdisp = "<img width=\"$medwidth\" height=\"$medheight\" src=\"".$Globals{'datadir'}."/$cat/$userid$filename-med$theext\" border=\"0\">";
                }
                else {
                    if ($filesize != "") {
                        $filesize=$filesize/1024;
                        $filesize=sprintf("%1.1f", $filesize);
                        $filesize="$filesize\k, $width X $height";
                        $imgdisp = "<img width=\"$width\" height=\"$height\" src=\"".$Globals{'datadir'}."/$cat/$userid$filename$theext\" border=\"0\">";
                    }
                    else {
                        $imgdisp = "<img src=\"".$Globals{'datadir'}."/$cat/$userid$filename-thumb$theext\" border=\"0\">";
                    }
                }
            }
            else {
                $imgdisp = "<img src=\"".$Globals{'datadir'}."/$cat/$userid$filename-thumb$theext\" border=\"0\">";
            }
        }
        else {
            $imgdisp = "<img width=\"100\" height=\"75\" src=\"".$Globals{'idir'}."/ipending.gif\" border=\"0\">";
        }

        if ( !IsSet( $view ) ) {
            $navtext = "Send photo as e-Card";
        }
        else {
            $navtext = "View your e-Card";
        }

        topmenu();
        $output = "<head><title>".$Globals{'galleryname'}." eCards</title>$script</head>$header<table
            cellpadding=\"0\" cellspacing=\"0\" border=\"0\" height=\"40\" width=\"".$Globals{'tablewidth'}."\"><Tr>
            <Td valign=\"center\" width=\"50%\">&nbsp;$childnav</td><td width=\"50%\" align=\"right\" valign=\"center\">
            <font face=\"verdana, arial\" size=\"2\">$menu&nbsp;</font>
            </td></tr></table>

            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"
            width=\"".$Globals{'tablewidth'}."\" align=\"center\"><tr><td>
            <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr align=\"center\">
            <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
            size=\"1\"><font size=\"2\"
            face=\"verdana\"><B>".$Globals{'galleryname'}."</font>
            </font></td></tr><tr id=\"cat\"><td bgcolor=\"".$Globals{'navbarcolor'}."\"><b><a
            href=\"".$Globals{'maindir'}."/index.php\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'catnavcolor'}."\">Home</font></a>
            <font face=\"verdana, arial,
            helvetica\" size=\"2\" color=\"".$Globals{'catnavcolor'}."\"> > $navtext</font> <a
            href=\"".$Globals{'maindir'}."/index.php?cat=$catid\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'catnavcolor'}."\">$thecatname</font></a></td></tr><!-- CyKuH [WTN] -->

            <Tr><td bgcolor=\"".$Globals{'maincolor'}."\">

            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#000000\" width=\"80%\" align=\"center\"><tr><td>
            <table cellpadding=\"0\" cellspacing=\"1\" border=\"0\" width=\"100%\"><tr><td bgcolor=\"".$Globals{'maincolor'}."\">
            <Center><Br>$imgdisp</center><p>";

        if ( !IsSet($view) ) {
            $output .= "<table cellpadding=\"15\" cellspacing=\"0\" border=\"0\" width=\"100%\" align=\"center\"><tr><td>
                <table cellpadding=\"3\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
                <form method=\"post\" action=\"".$Globals{'maindir'}."/ecard.php\"><Tr>
                <Th bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"verdana\" size=\"2\" color=\"".$Globals{'headfontcolor'}."\">Recipient Info</th></tr>
                <tr>
                <Td>
                <font size=\"2\" face=\"verdana\">
                Name: <input type=\"text\" name=\"rname\" size=\"40\"  style=\"font-size: 9pt;\"><br>
                Email: <input type=\"text\" name=\"remail\" size=\"40\"  style=\"font-size: 9pt;\"><p>

                If you don't know the recipient's email address, enter their member name instead:<p>
                Member Name: <input type=\"text\" name=\"touser\" size=\"40\"  style=\"font-size: 9pt;\">
                </td>
                </tr></table>
                </td><Td valign=\"top\">
                <table cellpadding=\"3\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
                <Tr>
                <Th bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"verdana\" size=\"2\" color=\"".$Globals{'headfontcolor'}."\">e-Card Subject/Greeting</th></tr>
                <tr><Td><font face=\"verdana\" size=\"2\" color=\"".$Globals{'headfontcolor'}."\">Subject: <input type=\"text\" name=\"subject\" size=\"40\" style=\"font-size: 9pt;\"></font></td></tr>
                </table>
                </td></tr>
                <Tr><Td valign=\"top\">
                <table cellpadding=\"3\" cellspacing=\"1\" border=\"0\"  width=\"100%\"><tr>
                <Th bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"verdana\" size=\"2\" color=\"".$Globals{'headfontcolor'}."\">Your e-card message</th></tr>
                <Td>
                <center><textarea name=\"message\" cols=\"40\" rows=\"5\"></textarea></center></td></tr></table>
                </td></tr></table>
                <input type=\"hidden\" value=\"yes\" name=\"send\">
                <input type=\"hidden\" value=\"$userid\" name=\"euserid\">
                <input type=\"hidden\" value=\"$inputphoto\" name=\"photoid\">
                <center><input type=\"submit\" value=\"Send the e-Card\"></center>
                </form>

                </td></tr></table>
                </td></tr></table><center><p>".$Globals{'cright'}."</td></tr></table>
                </td></tr></table>$footer";
        }
        else {
            $message = ConvertReturns($message);

            list( $fromname, $eemail ) = get_username( $userid );

            $output .= "<center>
                <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"50%\">
                <Tr><Th valign=\"top\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\"
                color=\"".$Globals{'headfontcolor'}."\" size=\"2\">Your e-Card</font></th></tr><Tr><Td>

                <B>$subject</b><p>

                $toname,<p>

                $message
                <p>
                $yname<p>&nbsp;
                </td></tr></table></td></tr></table></td></tr></table></td></tr></table>";
        }
    }
    print $output;
}

if ( $send == "yes" ) {
    // send the e-card, store the data in the db, forward the user ###

    $rname = preg_replace( "/<(?:[^>'\"]*|(['\"]).*?\1)*>/e", "", $rname);
    $remail = preg_replace( "/<(?:[^>'\"]*|(['\"]).*?\1)*>/e", "", $remail);
    $subject = preg_replace( "/<(?:[^>'\"]*|(['\"]).*?\1)*>/e", "", $subject);
    $message = preg_replace( "/<(?:[^>'\"]*|(['\"]).*?\1)*>/e", "", $message);

    list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
    $mon = $mon + 1;
    $julian = mktime($hour,$min,$sec,$mon,$mday,$year);

    if ($remail == "") {
        if ($touser == "") {
            $error .= "<li>You didn't enter a recipient email.  Please go back and enter either a recipient email
                or a member name.";
        }
    }

    if ($message == "") {
        $error .= "<li>Your message was blank.";
    }

    list( $yname, $yemail ) = get_username( $userid );

    if ($error != "") {
        dieWell( $error );
        exit;
    }

    if ($touser != "") {
        if ( $rname == "" ) {
            $rname = $touser;
        }

        if ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
            $query = "SELECT email FROM user WHERE username='$touser'";
        }
        if ($Globals{'vbversion'} == "phpBB") {
            $query = "SELECT user_email FROM users WHERE username='$touser'";
        }
        if ($Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
            $query = "SELECT U_Email FROM w3t_Users WHERE U_Username='$touser' LIMIT 1";
        }
        if ($Globals{'vbversion'} == "phpBB2") {
            if ( !empty($Globals{'dprefix'}) ) {
                $utable=$Globals{'dprefix'} ."_users";
            }
            else {
                $utable="users";
            }

            $query = "SELECT user_email FROM $utable WHERE username='$touser'";
        }
        if ($Globals{'vbversion'} == "Internal") {
            $query = "SELECT email FROM users WHERE username='$touser'";
        }

        $queryv = mysql_query_eval($query,$db_link);
        $results = mysql_fetch_row($queryv);
        $useremail = $results[0];

        if ($useremail == "") {
            diedWell("Sorry, member name not recognized.");
            exit;
        }
        else {
            $remail=$useremail;
        }
    }

    $subject = addslashes( $subject );
    $message = addslashes( $message );
    $yname = addslashes( $yname );
    $rname = addslashes( $rname );

    $query = "INSERT INTO ecards VALUES(NULL,'$yname','$rname','$subject','$message',$photoid,$julian)";
    $resulta = mysql_query_eval($query,$link);

    $cardid="$photoid-$julian";

    if ($rname == "") {
        $rname="Hello";
    }

    if ($yemail == "") {
        $yemail=$Globals{'adminemail'};
    }

    $letter="$rname,

        $yname has sent you an e-Card from ".$Globals{'webname'}.".  You can view your card here:

        ".$Globals{'maindir'}."/ecard.php?view=$cardid

        And you can email $yname directly at $yemail.

        Thanks!

        The ".$Globals{'webname'}." Team
        ".$Globals{'domain'};

    $subject="$yname sent you an e-Card";

    $from_email = "From: $yemail";
    mail( $remail, $subject, $letter, $from_email );

    $redirect = $Globals{'maindir'}."/showphoto.php?photo=$photoid";
    forward($redirect, "Your card has been sent!");
}

?>
