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
require "languages/$pplang/ecard.php";
require "login-inc.php";

if ($Globals['enablecard'] == "no") {
    diewell($Globals['pp_lang']['disabled']);
    exit;
}

authenticate();

if ( isset($ecard) ) {
    $inputphoto=$ecard;

    if ($Globals['cardreg'] == "yes") {
        if ( $gologin == 1 ) {
            $furl = $Globals['maindir'];
            $furl = str_replace( $Globals['domain'], "", $furl);
            $furl = "$furl/ecard.php?ecard=$phoedit";
            login( $furl );
            exit;
        }
    }
}

if ( isset( $view ) ) {
    $cnum = explode( "-", $view );
    $carddate = $cnum[1];

    $query = "SELECT fromname,toname,subject,message,date,photoid FROM ecards WHERE date=$carddate LIMIT 1";
    $resulta = ppmysql_query($query,$link);
    list( $fromnname, $toname, $subject, $message, $date, $photoid) = mysql_fetch_row($resulta);
    ppmysql_free_result( $resulta );
    
    $inputphoto = $photoid;
}

if ( $send != "yes" ) {
    $query = "SELECT id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,views,medwidth,medheight,medsize,approved FROM photos WHERE id=$inputphoto";
    $rows = ppmysql_query($query,$link);

    while ( list( $id, $user, $iuserid, $cat, $date, $title, $desc, $keywords, $bigimage, $width, $height, $filesize, $views, $medwidth, $medheight, $medsize, $approved ) = mysql_fetch_row($rows) ) {
        $theext = get_ext($bigimage);
        $filename = get_filename( $bigimage );

        if ( $approved == "1" ) {
            if ( $medsize > 0 ) {
                if ( $size != "big" ) {
                    $dispmed = "1";
                    $altlink = "<center><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><b>
                        <a href=\"{$Globals['maindir']}/showphoto.php?photo=$inputphoto&size=big\">{$Globals['pp_lang']['larger']}</a></b></font></center><br />";
                }
                else {
                    $altlink = "<center><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><b><a
                        href=\"{$Globals['maindir']}/showphoto.php?photo=$inputphoto\">{$Globals['pp_lang']['smaller']}</a></b></font></center><br />";
                }
            }
            
            if ($Globals['bigsave'] == "yes") {
                if ( $dispmed == 1 ) {
                    if ( $Globals['onthefly'] == 1 ) {
                        $imgdisp = "<a href=\"{$Globals['maindir']}/showphoto.php?photo=$photo&amp;size=big&amp;papass=$papass&amp;sort=$sort&amp;thecat=$thecat\"><img
                            width=\"$medwidth\" height=\"$medheight\" src=\"{$Globals['maindir']}/watermark.php?file=$cat/$iuserid$filename-med$theext&amp;sort=$sort\" border=\"0\" alt=\"\" /></a>";
                    }
                    else {
                        $imgdisp = "<a href=\"{$Globals['maindir']}/showphoto.php?photo=$photo&amp;size=big&amp;papass=$papass&amp;sort=$sort&amp;thecat=$thecat\"><img
                            width=\"$medwidth\" height=\"$medheight\" src=\"{$Globals['datadir']}/$cat/$iuserid$filename-med$theext\" border=\"0\" alt=\"\" /></a>";
                    }
                }
                else {
                    if ( is_multimedia($bigimage) == 1 ) {
                         $mmthumb = "{$Globals['datadir']}/$cat/$iuserid$filename-thumb.jpg";
                         $dirthumb = "{$Globals['datafull']}/$cat/$iuserid$filename-thumb.jpg";
    
                         if ( !file_exists($dirthumb) ) $mmthumb = "{$Globals['idir']}/video.jpg";
    
                         $imgdisp = "<a href=\"{$Globals['datadir']}/$cat/$iuserid$bigimage\"><img src=\"$mmthumb\" border=\"0\" alt=\"\" /></a>
                            <br /><font size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['video']}</font>";
                    }
                    else {
                        if ( $filesize != "" ) {
                            if ( $Globals['onthefly'] == 1 ) {
                                $imgdisp = "<a href=\"{$Globals['maindir']}/showphoto.php?photo=$photo&amp;papass=$papass&amp;sort=$sort&amp;thecat=$thecat\">
                                    <img width=\"$width\" height=\"$height\" src=\"{$Globals['maindir']}/watermark.php?file=$cat/$iuserid$filename$theext\" border=\"0\" alt=\"$filename\" /></a>";
                            }
                            else {
                                $imgdisp = "<a href=\"{$Globals['maindir']}/showphoto.php?photo=$photo&amp;papass=$papass&amp;sort=$sort&amp;thecat=$thecat\">
                                    <img width=\"$width\" height=\"$height\" src=\"{$Globals['datadir']}/$cat/$iuserid$filename$theext\" border=\"0\" alt=\"$filename\" /></a>";
                            }
                        }
                        else {
                            $imgdisp = "<img src=\"{$Globals['datadir']}/$cat/$iuserid$filename-thumb$theext\" border=\"0\" alt=\"\" />";
                        }
                    }
                }
            }
            else {
                $imgdisp = "<img src=\"{$Globals['datadir']}/$cat/$iuserid$filename-thumb$theext\" border=\"0\" alt=\"\" />";
            }
        }
        else {
            $imgdisp = "<img width=\"100\" height=\"75\" src=\"{$Globals['idir']}/ipending.gif\" border=\"0\" alt=\"Awaiting approval\">";
        }

        if ( !isset( $view ) ) {
            $navtext = $Globals['pp_lang']['send'];
        }
        else {
            $navtext = $Globals['pp_lang']['view'];
        }

        topmenu();
        
        childsub($cat);
        $childnav = "<font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['catfontsize']}\"><a href=\"{$Globals['maindir']}/index.php\">{$Globals['pp_lang']['home']}</a> $childnav</font>";

        printheader( $cat, $Globals['pp_lang']['ecard'] );
        
        $output = "<form method=\"post\" action=\"{$Globals['maindir']}/ecard.php\">
            <table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td>
            <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"
            width=\"{$Globals['tablewidth']}\" align=\"center\"><tr><td>
            <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr align=\"center\">
            <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
            size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\"
            face=\"{$Globals['mainfonts']}\"><b>$childnav</b></font>
            </font></td></tr><tr><td bgcolor=\"{$Globals['navbarcolor']}\">
            <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['catnavcolor']}\"><b>$navtext</b></font>
            </td></tr><!--PhotoPost, Copyright All Enthusiast,Inc.-->

            <tr><td bgcolor=\"{$Globals['maincolor']}\" align=\"center\">
            <br />$imgdisp<p>";

        if ( !isset($view) ) {
            $output .= "<table cellpadding=\"15\" cellspacing=\"0\" border=\"0\" width=\"100%\" align=\"center\">
                <tr>
                <Th bgcolor=\"{$Globals['headcolor']}\">
                <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\">{$Globals['pp_lang']['info']}</font>
                </th></tr>
                <tr><td align=\"left\">
                <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\">
                {$Globals['pp_lang']['name']}: <input type=\"text\" name=\"rname\" size=\"40\"  style=\"font-size: 9pt;\"><br /><br />
                {$Globals['pp_lang']['email']}: <input type=\"text\" name=\"remail\" size=\"40\"  style=\"font-size: 9pt;\"><br /><br />
                {$Globals['pp_lang']['alter']}: <input type=\"text\" name=\"touser\" size=\"40\"  style=\"font-size: 9pt;\">
                </font></td>
                </tr><tr>
                <Th bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\">{$Globals['pp_lang']['subjgreet']}</font>
                </th></tr>
                <tr><td align=\"left\">
                <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\">{$Globals['pp_lang']['subject']}: <input type=\"text\" name=\"subject\" size=\"40\" style=\"font-size: 9pt;\"></font></td></tr>
                <tr><Th bgcolor=\"{$Globals['headcolor']}\">
                <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\">{$Globals['pp_lang']['yourmess']}</font>
                </th></tr>
                <tr><td align=\"left\">
                <textarea name=\"message\" cols=\"50\" rows=\"8\"></textarea>
                </td></tr>
                <tr><td align=\"center\">
                <input type=\"hidden\" value=\"yes\" name=\"send\">
                <input type=\"hidden\" value=\"$inputphoto\" name=\"photoid\">
                <input type=\"submit\" value=\"{$Globals['pp_lang']['send']}\"><br />

                </td></tr></table>
                </td></tr></table>
                </td></tr></table></form>             
                
                <p><center>{$Globals['cright']}</center>";
        }
        else {
            $message = ConvertReturns($message);

            list( $fromname, $eemail ) = get_username( $userid );

            $output .= "<center>
                <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"80%\">
                <tr><th valign=\"top\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\"
                color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontmedium']}\"><b>$subject</b></font></th></tr><tr><td>

                <font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontmedium']}\">
                <br />
                $toname,<p>

                $message
                <p>
                $fromname<br />
                </font></td></tr></table></td></tr></table></td></tr></table></td></tr></table>
                <br />{$Globals['cright']}";
        }
    }
    ppmysql_free_result( $rows );
    
    print $output;
    
    printfooter();
    exit;
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
            $error .= "<li>{$Globals['pp_lang']['noemail']}";
        }
    }

    if ($message == "") {
        $error .= "<li>{$Globals['pp_lang']['blank']}";
    }

    list( $yname, $yemail ) = get_username( $userid );

    if ($error != "") {
        diewell( $error );
        exit;
    }

    if ($touser != "") {
        if ( $rname == "" ) {
            $rname = $touser;
        }

        if ($Globals['vbversion'] == "2.0.3" || $Globals['vbversion'] == "2.2.0") {
            $query = "SELECT email FROM user WHERE username='$touser'";
        }
        if ($Globals['vbversion'] == "phpBB") {
            $query = "SELECT user_email FROM users WHERE username='$touser'";
        }
        if ($Globals['vbversion'] == "w3t" || $Globals['vbversion'] == "w3t6") {
            if ( !empty( $Globals['dprefix'] ) ) {
                $utable = "{$Globals['dprefix']}Users";
            }
            else {
                $utable = "w3t_Users";
            }
            
            $query = "SELECT U_Email FROM $utable WHERE U_Username='$touser' LIMIT 1";
        }
        if ($Globals['vbversion'] == "phpBB2") {
            if ( !empty($Globals['dprefix']) ) {
                $utable = "{$Globals['dprefix']}users";
            }
            else {
                $utable = "users";
            }

            $query = "SELECT user_email FROM $utable WHERE username='$touser'";
        }
        if ($Globals['vbversion'] == "Internal") {
            $query = "SELECT email FROM users WHERE username='$touser'";
        }

        $queryv = ppmysql_query($query,$db_link);
        
        if ( $queryv ) {
            list( $useremail )= mysql_fetch_row($queryv);
            ppmysql_free_result( $queryv );
        }

        if ( !$queryv || empty($useremail) ) {
            diewell( $Globals['pp_lang']['badname'] );
            exit;
        }
        
        $remail = $useremail;
    }

    $subject = addslashes( $subject );
    $message = addslashes( $message );
    $yname = addslashes( $yname );
    $rname = addslashes( $rname );

    $query = "INSERT INTO ecards (id,fromname,toname,subject,message,photoid,date) VALUES(NULL,'$yname','$rname','$subject','$message',$photoid,$julian)";
    $resulta = ppmysql_query($query,$link);

    $cardid = "$photoid-$julian";

    if ($rname == "") {
        $rname = $Globals['pp_lang']['hello'];
    }

    if ($yemail == "") {
        $yemail = $Globals['adminemail'];
    }

    $letter="$rname,

$yname has sent you an e-Card from {$Globals['webname']}.  You can view your card here:

{$Globals['maindir']}/ecard.php?view=$cardid

And you can email $yname directly at $yemail.

Thanks!

The {$Globals['webname']} Team
{$Globals['domain']}";

    $subject = "$yname sent you an e-Card";

    $from_email = "From: $yemail";
    mail( $remail, $subject, $letter, $from_email );

    $redirect = "{$Globals['maindir']}/showphoto.php?photo=$photoid";
    forward($redirect, $Globals['pp_lang']['cardsent']);
}

?>
