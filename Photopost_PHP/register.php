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
require "languages/$pplang/register.php";
require "login-inc.php";

list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();

authenticate();

if ( isset($Globals['ppboards']) && $adminedit != 1 ) {
    if ( $Globals['ppboards'] == "closed" ) {
        print $Globals['pp_lang']['closed'];
        exit;
    }
}

if ($Globals['allowregs'] == "no") {
  diewell($Globals['pp_lang']['nonew']);
}


// Check to see if the user is already logged in.

if ($Globals['vbversion'] != "Internal") {
    diewell($Globals['pp_lang']['intonly']);
    exit;
}

if ($gologin != "1") {
    if ($ppaction != "vfy") {
        $dietext = "{$Globals['pp_lang']['loggedin']} <br /><br />{$Globals['pp_lang']['retfront']} <a href=\"{$Globals['maindir']}/index.php\">{$Globals['galleryname']}</a>.";
        diewell( $dietext );
    }
}

// If using Coppa, spit out the Coppa form

$gocoppa=0;

if ( !isset($ppaction) ) $ppaction = "register";
if ( !isset($agree) ) $agree="";

if ($ppaction == "register") {
    if ($age == "") {
        if ($Globals['coppa'] == "yes") {
            $gocoppa=1;
        }
        if ($Globals['coppa'] == "no") {
            $age="adult";
        }
    }

    if ($age != "") {
        if ($Globals['coppa'] == "yes") {
            $gocoppa=2;
        }
    }

    if ($gocoppa == 1) {
        printheader( 0, "COPPA Form" );
        
        $output .= "<br /><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"
            bgcolor=\"".$Globals['bordercolor']."\"  width=\"{$Globals['tablewidth']}\"
            align=\"center\"><tr><td>
            <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr>
            <td bgcolor=\"{$Globals['maincolor']}\" width=\"100%\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
            color=\"{$Globals['maintext']}\"><b>{$Globals['pp_lang']['regfor']} {$Globals['webname']}</b></font></td>
            </tr>
            <tr>
            <td bgcolor=\"{$Globals['headcolor']}\" width=\"100%\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
            color=\"{$Globals['headfontcolor']}\"><b>{$Globals['pp_lang']['coppainfo']}</b></font></td>
            </tr>
            <tr>
            <td bgcolor=\"{$Globals['maincolor']}\" width=\"100%\">
            <p><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">Please choose your age:</font></p>
            <p align=\"center\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"><b> [ <a
            href=\"{$Globals['maindir']}/register.php?ppaction=register&age=adult\">{$Globals['pp_lang']['over13']}</a> | <a
            href=\"{$Globals['maindir']}/register.php?ppaction=register&age=coppa\">{$Globals['pp_lang']['under13']}</a> ]</b></font></p>";

        if ($Globals['privacylink'] != "") {
            $output .= "<p><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" >
            {$Globals['pp_lang']['forinfo']} <a href=\"".$Globals['privacylink']."\">".$Globals['galleryname']." {$Globals['pp_lang']['privacy']}</a></font></p>";
        }

        $output .= "</td>
            </tr>
            <tr>
            <td bgcolor=\"{$Globals['headcolor']}\" width=\"100%\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
            color=\"{$Globals['headfontcolor']}\"><b>{$Globals['pp_lang']['permform']}</b></font></td>
            </tr>

            <tr>
            <td width=\"100%\" bgcolor=\"{$Globals['maincolor']}\">
            <p><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">
            <a href=\"{$Globals['maindir']}/register.php?ppaction=cform\">{$Globals['pp_lang']['permformlower']}</a>{$Globals['pp_lang']['parentreq']}
            {$Globals['pp_lang']['toadmin']} {$Globals['webname']} {$Globals['pp_lang']['beforeany']}</font></p>

            <p><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['forfurther']} <a
            href=\"mailto:".$Globals['adminemail']."\">".$Globals['adminemail']."</a>.</font></p>
            </td>
            </tr>
            </table>
            </td></tr></table>";

        print "$output{$Globals['cright']}";
        printfooter();
        
        exit;
    }

    $coppavar=$coppa;

    // First see if they agree to the rules
    printheader( 0, $Globals['pp_lang']['addcomment'] );
    
    if ($agree == "") {
        if ($age == "adult") {
            $output .= "<p><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals['bordercolor']."\"
                width=\"{$Globals['tablewidth']}\"
                align=\"center\"><tr><td>
                <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
                <tr>
                <td bgcolor=\"{$Globals['maincolor']}\" width=\"100%\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"{$Globals['maintext']}\"><b>{$Globals['pp_lang']['registerfor']} {$Globals['webname']}</b></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['headcolor']}\" align=\"center\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"{$Globals['headfontcolor']}\"
                class=\"thtcolor\"><b>{$Globals['webname']} {$Globals['pp_lang']['rulespol']}</b></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['maincolor']}\">
                <p><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">";

            $ruleshtml = $Globals['rules'];
            if ( empty($ruleshtml) || !file_exists($ruleshtml) ) {
                $output .= "Registration to this forum is free!

                    We do insist that you abide by the rules and policies detailed below.
                    If you agree to the terms, please press the Agree button at the end of the page.

                    Although the administrators and moderators of ".$Globals['galleryname']." will attempt to keep all objectionable messages and images out
                    of our gallery, it is impossible for us to review all messages.  All messages express the views of the author, and neither the
                    owners of ".$Globals['galleryname']." or All Enthusiast, Inc. (developers of PhotoPost) will be held responsible for the content of any
                    message or any image in our gallery.</font></p>

                    <p><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" >By clicking the Agree button, you warrant that you will not post any
                    messages or upload any images that are obscene,
                    vulgar, sexually-orientated, hateful, threatening, or otherwise violative of any laws.</font></p>

                    <p><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" >The owners of ".$Globals['galleryname']." have the right to remove, edit, or move
                    any image or post for any reason.</font></p>";
            }
            else {
                $filearray = file($ruleshtml);
                $rulestext = implode( " ", $filearray );
                
                $output .= $rulestext;
            }
        }

        if ($age == "coppa") {
            $output .= "<br /><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"
                bgcolor=\"".$Globals['bordercolor']."\"  width=\"{$Globals['tablewidth']}\"
                align=\"center\"><tr><td>
                <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
                <tr>
                <td bgcolor=\"{$Globals['maincolor']}\" width=\"100%\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"{$Globals['maintext']}\"><b>{$Globals['pp_lang']['registerfor']} {$Globals['webname']}</b></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['headcolor']}\" align=\"center\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"{$Globals['headfontcolor']}\"
                class=\"thtcolor\"><b>{$Globals['pp_lang']['forumrules']}</b></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['maincolor']}\">
                <p><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">";

            if ($coppatext == "") {
                $output .= "{$Globals['pp_lang']['coppaform']} 
                    <a href=\"{$Globals['maindir']}/register.php?ppaction=cform\">{$Globals['pp_lang']['permformlower']}</a>.
                    {$Globals['pp_lang']['formore']} ".$Globals['adminemail'].".";
            }
            else {
                $output .= $coppatext;
            }
        }

        $output .= "<p><center><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" >
            <form action=\"{$Globals['maindir']}/register.php\" method=\"get\">
            <input type=\"hidden\" name=\"agree\" value=\"yes\">
            <input type=\"hidden\" name=\"age\" value=\"$age\">
            <input type=\"hidden\" name=\"ppaction\" value=\"register\">
            <input type=\"submit\" value=\"{$Globals['pp_lang']['agree']}\">
            </font></form>
            <form action=\"{$Globals['maindir']}/index.php\" method=\"get\">
            <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" >
            <input type=\"submit\" value=\"{$Globals['pp_lang']['cancel']}\">
            </font></p>
            </form></center>       
            </td></tr></table>
            </td></tr></table>";
    }

    // If they agreed to the rules, spit out the reg form
    if ($agree == "yes") {
        if ($age == "coppa") {
            $output .= "<br /><b>{$Globals['pp_lang']['plznote']} <a
                href=\"{$Globals['maindir']}/register.php?ppaction=cform\">{$Globals['pp_lang']['permformlower']}</a> {$Globals['pp_lang']['plznote2']}
                <p>";
        }

        list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
        $mon = $mon + 1;
        $julian = mktime($hour,$min,$sec,$mon,$mday,$year);
        $cclock = formatpptime( $julian );
        $ppdate = formatppdate( $julian );        

        $thetime = "$ppdate $cclock";        

        $output .= "<form action=\"{$Globals['maindir']}/register.php\" method=\"post\">

            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals['bordercolor']."\" width=\"{$Globals['tablewidth']}\"
            align=\"center\"><tr><td>
            <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr>
            <td colspan=\"2\"bgcolor=\"{$Globals['maincolor']}\" width=\"100%\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
            color=\"{$Globals['maintext']}\"><b>{$Globals['pp_lang']['registerfor']} {$Globals['webname']}</b></font></td>
            </tr>
            <tr>
            <td bgcolor=\"{$Globals['headcolor']}\" colspan=\"2\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
            color=\"{$Globals['headfontcolor']}\"><b>{$Globals['pp_lang']['reqinfo']}</b></font>
            <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\"  color=\"{$Globals['headfontcolor']}\">
            {$Globals['pp_lang']['secreq']}</font></td>
            </tr>
            <tr>
            <td bgcolor=\"{$Globals['altcolor2']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
            color=\"{$Globals['commentstext']}\"><b>{$Globals['pp_lang']['username']}:</b></font></td>
            <td bgcolor=\"{$Globals['altcolor2']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><input
            type=\"text\" name=\"pick_username\" size=\"25\" maxlength=\"25\"></font></td>
            </tr>
            <tr>
            <td bgcolor=\"{$Globals['altcolor1']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
            color=\"{$Globals['commentstext']}\"><b>{$Globals['pp_lang']['password']}:</b></font></td>
            <td bgcolor=\"{$Globals['altcolor1']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><input
            type=\"password\" name=\"password\" size=\"25\" maxlength=\"25\"></font></td>
            </tr>
            <tr>
            <td bgcolor=\"{$Globals['altcolor2']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
            color=\"{$Globals['commentstext']}\"><b>{$Globals['pp_lang']['passagain']}:</b></font></td>
            <td bgcolor=\"{$Globals['altcolor2']}\"><input type=\"password\"  name=\"passwordconfirm\" size=\"25\" maxlength=\"15\"></td>
            </tr>
            <tr>
            <td bgcolor=\"{$Globals['altcolor1']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
            color=\"{$Globals['commentstext']}\"><b>{$Globals['pp_lang']['email']}:</b></font><br />
            <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" >{$Globals['pp_lang']['validemail']}</font></td>
            <td bgcolor=\"{$Globals['altcolor1']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
            color=\"{$Globals['commentstext']}\"><input type=\"text\" name=\"email\" size=\"25\" maxlength=\"50\"></font></td></tr>";
            
        if ( $Globals['getoptional'] == "yes" ) {                    
            $output .= "<tr>
                <td bgcolor=\"{$Globals['headcolor']}\" colspan=\"2\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"{$Globals['headfontcolor']}\"><b>{$Globals['pp_lang']['optional']}</b></font>
                <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" color=\"{$Globals['headfontcolor']}\">
                {$Globals['pp_lang']['visible']}</font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor1']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"{$Globals['commentstext']}\"><b>{$Globals['pp_lang']['homepage']}:</b></font></td>
                <td bgcolor=\"{$Globals['altcolor1']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><input
                type=\"text\" name=\"homepage\" value=\"http://\" size=\"25\" maxlength=\"100\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor2']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><b>
                {$Globals['pp_lang']['icq']}:</b></font></td>
                <td bgcolor=\"{$Globals['altcolor2']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><input
                type=\"text\" name=\"icq\" size=\"25\" maxlength=\"20\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor1']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><b>
                {$Globals['pp_lang']['aim']}:</b></font></td>
                <td bgcolor=\"{$Globals['altcolor1']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><input
                type=\"text\" name=\"aim\" size=\"25\" maxlength=\"20\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor2']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><b>
                {$Globals['pp_lang']['yahoo']}:</b></font></td>
                <td bgcolor=\"{$Globals['altcolor2']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><input
                type=\"text\" name=\"yahoo\" size=\"25\" maxlength=\"20\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor1']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"{$Globals['commentstext']}\"><b>{$Globals['pp_lang']['bday']}:</b></font><br />
                <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\">
                {$Globals['pp_lang']['seebday']}</font></td>
                <td bgcolor=\"{$Globals['altcolor1']}\" valign=\"top\">
    
                <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
                <tr>
                <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" >{$Globals['pp_lang']['month']}</font></td>
                <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" >{$Globals['pp_lang']['day']}</font></td>
                <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" >{$Globals['pp_lang']['year']}</font></td>
                </tr>
                <tr>
                <td><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" ><select name=\"month\">
                <option value=\"-1\" ></option>";
                
            $months = array('January','February','March','April','May','June','July','August','September','October','November','December');
            for ( $m=0; $m < 12; $m++ ) {
                $output .= "<option value=\"".($m+1)."\">".$months[$m]."</option>\n";
            }
                
            $output .= "</select></font></td>
                <td><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\"><select name=\"day\">
                <option value=\"-1\" ></option>";
                
            for ( $x=1; $x < 32; $x++ ) {
                $output .= "<option value=\"$x\" >$x</option>\n";
            }
            
            $output .= "</select></font></td>
                <td><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\"><input type=\"text\" name=\"year\" value=\"\" size=\"{$Globals['fontlarge']}\"
                maxlength=\"4\"></font></td>
                </tr>
                </table>
    
                </td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor2']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"{$Globals['commentstext']}\"><b>{$Globals['pp_lang']['bio']}:</b></font></td>
                <td bgcolor=\"{$Globals['altcolor2']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><input
                type=\"text\" name=\"bio\" value=\"\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor1']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"{$Globals['commentstext']}\"><b>{$Globals['pp_lang']['location']}:</b></font></td>
                <td bgcolor=\"{$Globals['altcolor1']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><input
                type=\"text\" name=\"location\" value=\"\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor2']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"{$Globals['commentstext']}\"><b>{$Globals['pp_lang']['interests']}:</b></font></td>
                <td bgcolor=\"{$Globals['altcolor2']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><input
                type=\"text\" name=\"hobbies\" value=\"\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>
                <tr>
                <td bgcolor=\"{$Globals['altcolor1']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
                color=\"{$Globals['commentstext']}\"><b>{$Globals['pp_lang']['occupation']}:</b></font></td>
                <td bgcolor=\"{$Globals['altcolor1']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><input
                type=\"text\" name=\"occupation\" value=\"\" size=\"25\" maxlength=\"250\"></font></td>
                </tr>";
        }
            
        $output .= "<tr>
            <td bgcolor=\"{$Globals['altcolor2']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"
            color=\"{$Globals['commentstext']}\"><b>{$Globals['pp_lang']['timezone']} $thetime</b></font></td>
            <td bgcolor=\"{$Globals['altcolor2']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['commentstext']}\"><input
            type=\"text\" name=\"offset\" value=\"0\" size=\"{$Globals['fontlarge']}\" maxlength=\"3\"></font></td>
            </tr>            
            </table>
            </td></tr></table><p>
            <center>
            <input type=\"hidden\" name=\"ppaction\" value=\"submit\">
            <input type=\"hidden\" name=\"age\" value=\"$age\">
            <input type=\"submit\" value=\"{$Globals['pp_lang']['submitreg']}\"></form>
            <form action=\"{$Globals['maindir']}/index.php\" method=\"get\">
            <input type=\"submit\" value=\"{$Globals['pp_lang']['cancel']}\">
            </form>";
    }
}

// Process registration input, send verify email or enable acct

if ($ppaction == "submit") {
    $reason = "";

    if ($pick_username == "") {
        $reason .= "<li>{$Globals['pp_lang']['userblank']}";
        $stop = 1;
    }

    wordchars( $pick_username ); // check username for bad characters

    $query = "SELECT userid FROM users WHERE username='$pick_username' LIMIT 1";
    $resulta = ppmysql_query($query, $link);
    $matchu = mysql_num_rows( $resulta );

    if ( $matchu > 0 ) {
        $reason .= "<li>{$Globals['pp_lang']['userexists']}";
        $stop = 1;
    }

    if ($Globals['emailunique'] == "yes") {
        $query = "SELECT email FROM users WHERE email='$email' LIMIT 1";
        $resulta = ppmysql_query($query,$link);
        list( $dbemail ) = mysql_fetch_row($resulta);
        ppmysql_free_result($resulta);

        if ( !strcasecmp($dbemail, $email) ) {
            $reason .= "<li>{$Globals['pp_lang']['emailexists']}";
            $stop = 1;
        }
    }

    if ($email != "") {
        if ( !strstr($email, "@") ) {
            $reason .= "<li>{$Globals['pp_lang']['emailat']}";
            $stop = 1;
        }

        if ( !strstr($email, ".") ) {
            $reason .= "<li>{$Globals['pp_lang']['emailperiod']}";
            $stop = 1;
        }
    }

    if ($password == "") {
        $reason .= "<li>{$Globals['pp_lang']['passblank']}";
        $stop = 1;
    }

    if ($password != "") {
        $pwdlength = strlen($password);
        if ($pwdlength < 4) {
            $reason .= "<li>{$Globals['pp_lang']['fourchars']}";
            $stop = 1;
        }
    }

    if ($pick_username != "") {
        $userlength = strlen($pick_username);
        if ($userlength < 2) {
            $reason .= "<li>{$Globals['pp_lang']['user2char']}";
            $stop = 1;
        }
    }

    if ($passwordconfirm == "") {
        $reason .= "<li>{$Globals['pp_lang']['passverblank']}";
        $stop = 1;
    }

    if ($password != $passwordconfirm) {
        $reason .= "<li>{$Globals['pp_lang']['passnomatch']}";
        $stop = 1;
    }

    if ($email == "") {
        $reason .= "<li>{$Globals['pp_lang']['emailblank']}";
        $stop = 1;
    }

    if ($stop == 1) {
        diewell($reason);
    }

    if ( $Globals['getoptional'] == "yes" ) {
        $homepage = fixstring( $homepage );
        $icq = fixstring( $icq );
        $aim = fixstring( $aim );
        $yahoo = fixstring( $yahoo );
        $year = fixstring( $year );
        $hobbies = fixstring( $hobbies );
        $occupation = fixstring( $occupation );
        $location = fixstring( $location );
        $birthday = "";
        if ( $year != "" && $month != "-1" && $day != "-1" ) {
            $birthday = "$year-$month-$day";
        }    
    }
    else {
        $homepage = "";
        $icq = "";
        $aim = "";
        $yahoo = "";
        $year = "";
        $hobbies = "";
        $occupation = "";
        $location = "";
        $birthday = "";        
    }

    $ipaddress = findenv("REMOTE_ADDR");

    list($dsec,$dmin,$dhour,$dmday,$dmon,$dyear,$dwday,$dyday,$disdst) = localtime();
    $mon = $mon + 1;
    $joindate = mktime($hour,$min,$sec,$mon,$mday,$year);

    if ($age == "coppa") {
        $userlevel=2;
    }

    if ($age == "adult") {
        if ($Globals['emailverify'] == "yes") {
            $userlevel=3;
        }
        else {
            $userlevel=4;
        }
    }

    $passwordmd5 = md5($password);

    $pick_username = addslashes( $pick_username );
    $email = addslashes( $email );
    $homepage = addslashes( $homepage );
    $location = addslashes( $location );
    $hobbies = addslashes( $hobbies );
    $occupation = addslashes( $occupation );
    $bio = addslashes( $bio );
    $thissite = $Globals['webname'];

    $query = "INSERT INTO users (userid,usergroupid,username,password,email,homepage,icq,aim,yahoo,joindate,birthday,ipaddress,location,interests,occupation,bio,site,offset)
        values(NULL,'$userlevel','$pick_username','$passwordmd5','$email','$homepage','$icq','$aim','$yahoo','$joindate','$birthday','$ipaddress','$location','$hobbies','$occupation','$bio','$thissite','$offset')";
    $resulta = ppmysql_query($query,$link);
    $newuserid = mysql_insert_id( $link );

    if ( !$newuserid ) {
        diewell( $Globals['pp_lang']['erroradd'] );
        exit;
    }

    if ($age == "adult") {
        if ($Globals['emailverify'] == "yes") {
            $query = "SELECT userid FROM users WHERE username='$pick_username' AND joindate='$joindate' LIMIT 1";
            $resulta = ppmysql_query($query,$link);
            list( $theuid ) = mysql_fetch_row($resulta);
            ppmysql_free_result($resulta);

            $email_from = "From: {$Globals['adminemail']}";

            $letter = "Thanks for registering at {$Globals['webname']}.

In order to activate your account, which will enable you to upload photos and post comments (if
the site allows comments), you must click on the link below or copy and paste it into your browser:

{$Globals['maindir']}/register.php?ppaction=vfy&uid=$theuid&knum=$joindate

Thanks!

The {$Globals['webname']} Team
".$Globals['domain'];

            $subject = "Confirm {$Globals['webname']} Registration (action needed)";
            mail( $email, $subject, $letter, $email_from );

            $done = "{$Globals['pp_lang']['thanksreg']}<p>

                <a href=\"{$Globals['maindir']}/index.php\"><font color=\"{$Globals['maintext']}\">
                {$Globals['pp_lang']['retfront']} ".$Globals['galleryname']."</font></a>.";

            diewell( $done );
        }
    }

    $done = "{$Globals['pp_lang']['thanksreg']}<br /><br />

        <a href=\"{$Globals['maindir']}/index.php\"><font color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['retfront']} ".$Globals['galleryname']."</font></a>.";

    diewell($done);
}

// Verify a user's email, change status from unregistered to registered

if ($ppaction == "vfy") {
    $query = "SELECT joindate,usergroupid FROM users WHERE userid=$uid LIMIT 1";
    $resulta = ppmysql_query($query,$link);
    
    if ( $resulta ) {
        list( $joindate, $ugid ) = mysql_fetch_row($resulta);
        ppmysql_free_result($resulta);

        if ($joindate == $knum && ($ugid == 3 || $ugid == 4)) {
            $query = "UPDATE users SET usergroupid=4 WHERE userid=$uid";
            $resulta = ppmysql_query($query,$link);
    
            $done = "{$Globals['pp_lang']['thanks']}!<br /><a href=\"{$Globals['maindir']}/index.php\"><font
                color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['retfront']} ".$Globals['galleryname']."</font></a>.";
                
            diewell($done);
        }
        else {
            diewell($Globals['pp_lang']['noverify']);
        }
    }
    else {
        diewell( $Globals['pp_lang']['nonum'] );
    }
}

if ($ppaction == "cform") {
    $Globals['commentstext']="#000000";
    $Globals['altcolor1']="#FFFFFF";
    $Globals['altcolor2']="#FFFFFF";

    topmenu();

    printheader( 0, "Register Form" );

    $output = "<br /><table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td>
        <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals['bordercolor']."\"  width=\"100%\"
        align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\"
        color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontlarge']}\"
        face=\"{$Globals['mainfonts']}\">".$Globals['galleryname']."</font>
        </font></td></tr><!--PhotoPost, Copyright All Enthusiast,Inc.-->

        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\"
        color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['coppainfo']}</font>
        </font></td></tr><!--PhotoPost, Copyright All Enthusiast,Inc.-->
        <tr><td colspan=\"2\" bgcolor=\"#FFFFFF\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">
        {$Globals['pp_lang']['parentprint']}:<br />".$Globals['address']."<br /></td></tr>
        <tr><td bgcolor=\"#FFFFFF\" width=\"20%\" align=\"right\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"
        color=\"{$Globals['commentstext']}\">{$Globals['pp_lang']['username']}:</font></td><td
        bgcolor=\"#FFFFFF\"></font></td></tr>
        <tr><td bgcolor=\"#FFFFFF\" align=\"right\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"
        color=\"{$Globals['commentstext']}\">{$Globals['pp_lang']['password']}:</font></td><td bgcolor=\"{$Globals['altcolor1']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"
        color=\"{$Globals['commentstext']}\">$usergroup</td></tr>
        <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"right\"><font size=\"{$Globals['fontmedium']}\"
        face=\"{$Globals['mainfonts']}\"
        color=\"{$Globals['commentstext']}\">{$Globals['pp_lang']['email']}:</font></td><td bgcolor=\"{$Globals['altcolor2']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"
        color=\"{$Globals['commentstext']}\">$posts</td></tr>";
        
    if ( $Globals['getoptional'] == "yes" ) {
        $output .= "<tr><td colspan=\"2\" bgcolor=\"#FFFFFF\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\">
            <b>{$Globals['pp_lang']['optional']}</b></font></td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor1']}\" align=\"right\"><font
            size=\"{$Globals['fontmedium']}\"
            face=\"{$Globals['mainfonts']}\" color=\"{$Globals['commentstext']}\">{$Globals['pp_lang']['bday']}:</font></td><td bgcolor=\"{$Globals['altcolor1']}\"><font size=\"{$Globals['fontmedium']}\"
            face=\"{$Globals['mainfonts']}\"
            color=\"{$Globals['commentstext']}\"></font></td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"right\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"
            color=\"{$Globals['commentstext']}\">{$Globals['pp_lang']['homepage']}:</font></td><td bgcolor=\"{$Globals['altcolor2']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"
            color=\"{$Globals['commentstext']}\"><a href=\"$homepage\">$homepage</a></font></td></tr>
            <tr><td
            bgcolor=\"{$Globals['altcolor1']}\" align=\"right\"><font size=\"{$Globals['fontmedium']}\"
            face=\"{$Globals['mainfonts']}\" color=\"{$Globals['commentstext']}\">{$Globals['pp_lang']['icq']}:</font></td><td bgcolor=\"{$Globals['altcolor1']}\"
            align=\"right\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['commentstext']}\">$icq</td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"right\"><font size=\"{$Globals['fontmedium']}\"
            face=\"{$Globals['mainfonts']}\"
            color=\"{$Globals['commentstext']}\">{$Globals['pp_lang']['aim']}:</font></td><td bgcolor=\"{$Globals['altcolor2']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"
            color=\"{$Globals['commentstext']}\">$aim</td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"right\"><font size=\"{$Globals['fontmedium']}\"
            face=\"{$Globals['mainfonts']}\"
            color=\"{$Globals['commentstext']}\">{$Globals['pp_lang']['interests']}:</font></td><td bgcolor=\"{$Globals['altcolor2']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"
            color=\"{$Globals['commentstext']}\">$hobbies</td></tr>
            <tr><td bgcolor=\"{$Globals['altcolor2']}\" align=\"right\"><font size=\"{$Globals['fontmedium']}\"
            face=\"{$Globals['mainfonts']}\"
            color=\"{$Globals['commentstext']}\">{$Globals['pp_lang']['aboutme']}:</font></td><td bgcolor=\"{$Globals['altcolor2']}\"><font size=\"{$Globals['fontmedium']}\"
            face=\"{$Globals['mainfonts']}\"
            color=\"{$Globals['commentstext']}\">$occupation</td></tr>";
    }
    $output .= "</table></td></tr></table>";
}

print "$output<br />{$Globals['cright']}";
printfooter();

?>
