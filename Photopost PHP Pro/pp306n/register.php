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

list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();

authenticate();

if ($Globals{'allowregs'} == "no") {
  dieWell("New user registrations not allowed.");
}


// Check to see if the user is already logged in.

if ($Globals{'vbversion'} != "Internal") {
    dieWell("This script is only used for PhotoPost's internal registration system.");
    exit;
}

if ($gologin != "1") {
    if ($ppaction != "vfy") {
        $dietext = "You're already registered and logged in!  Return to the <a
            href=\"".$Globals{'maindir'}."/index.php\">".$Globals{'galleryname'}."</font></a>.";
        dieWell( $dietext );
    }
}

// If using Coppa, spit out the Coppa form

$gocoppa=0;

if ( !isset($ppaction) ) $ppaction = "register";
if ( !isset($agree) ) $agree="";

if ($ppaction == "register") {
    if ($age == "") {
        if ($Globals{'coppa'} == "yes") {
            $gocoppa=1;
        }
        if ($Globals{'coppa'} == "no") {
            $age="adult";
        }
    }

    if ($age != "") {
        if ($Globals{'coppa'} == "yes") {
            $gocoppa=2;
        }
    }

    if ($gocoppa == 1) {
        $output .= "$header<Br><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"
            bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
            align=\"center\"><tr><td>
            <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"".$Globals{'tablewidth'}."\">
            <tr>
            <td bgcolor=\"".$Globals{'maincolor'}."\" width=\"100%\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'maintext'}."\"><b>Register for ".$Globals{'webname'}."</b></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'headcolor'}."\" width=\"100%\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'headfontcolor'}."\"><b>COPPA Information</b></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'maincolor'}."\" width=\"100%\">
            <p><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'maintext'}."\">Please choose your age:</font></p>
            <p align=\"center\"><font face=\"verdana, arial, helvetica\" size=\"2\"><b> [ <a
            href=\"".$Globals{'maindir'}."/register.php?ppaction=register&age=adult\">Over
            13 years of age</a> | <a
            href=\"".$Globals{'maindir'}."/register.php?ppaction=register&age=coppa\">Under
            13 years of age</a> ]</b></font></p>";

        if ($Globals{'privacylink'} != "") {
            $output .= "<p><font face=\"verdana, arial, helvetica\" size=\"2\" >For information about how this site uses personal information,
                please read the <a href=\"".$Globals{'privacylink'}."\">".$Globals{'galleryname'}." Privacy Statement</a></font></p>";
        }

        $output .= "</td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'headcolor'}."\" width=\"100%\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'headfontcolor'}."\"><b>Permission Form</b></font></td>
            </tr>

            <tr>
            <td width=\"100%\" bgcolor=\"".$Globals{'maincolor'}."\">
            <p><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'maintext'}."\">A parent or guardian must mail or fax a signed
            <a href=\"".$Globals{'maindir'}."/register.php?ppaction=cform\">permission form</a>
            to the administrator of ".$Globals{'webname'}." before anyone under the age of 13 can complete the registration.</font></p>

            <p><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'maintext'}."\">For further information contact <a
            href=\"mailto:".$Globals{'adminemail'}."\">".$Globals{'adminemail'}."</a>.</font></p>
            </td>
            </tr>
            </table>
            </td></tr></table>";

        print "$output".$Globals{'cright'}."$footer";
        exit;
    }

    $coppavar=$coppa;

    // First see if they agree to the rules
    $output .= $header;

    if ($agree == "") {
        if ($age == "adult") {

            $output .= "<Br><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"
                width=\"".$Globals{'tablewidth'}."\"
                align=\"center\"><tr><td>
                <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
                <tr>
                <td bgcolor=\"".$Globals{'maincolor'}."\" width=\"100%\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'maintext'}."\"><b>Register for ".$Globals{'webname'}."</b></font></td>
                </tr>
                <tr>
                <td bgcolor=\"".$Globals{'headcolor'}."\" align=\"center\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'headfontcolor'}."\"
                class=\"thtcolor\"><b>".$Globals{'webname'}." Rules & Policies</b></font></td>
                </tr>
                <tr>
                <td bgcolor=\"".$Globals{'maincolor'}."\">
                <p><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'maintext'}."\">";

            if ($rulestext == "") {
                $output .= "Registration to this forum is free!

                    We do insist that you abide by the rules and policies detailed below.
                    If you agree to the terms, please press the Agree button at the end of the page.

                    Although the administrators and moderators of ".$Globals{'galleryname'}." will attempt to keep all objectionable messages and images out
                    of our gallery, it is impossible for us to review all messages.  All messages express the views of the author, and neither the
                    owners of ".$Globals{'galleryname'}." or All Enthusiast, Inc. (developers of PhotoPost) will be held responsible for the content of any
                    message or any image in our gallery.</font></p>

                    <p><font face=\"verdana, arial, helvetica\" size=\"2\" >By clicking the Agree button, you warrant that you will not post any
                    messages or upload any images that are obscene,
                    vulgar, sexually-orientated, hateful, threatening, or otherwise violative of any laws.</font></p>

                    <p><font face=\"verdana, arial, helvetica\" size=\"2\" >The owners of ".$Globals{'galleryname'}." have the right to remove, edit, or move
                    any image or post for any reason.</font></p>";
            }
            else {
                $output .= $rulestext;
            }
        }

        if ($age == "coppa") {
            $output .= "<br><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"
                bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
                align=\"center\"><tr><td>
                <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
                <tr>
                <td bgcolor=\"".$Globals{'maincolor'}."\" width=\"100%\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'maintext'}."\"><b>Register for ".$Globals{'webname'}."</b></font></td>
                </tr>
                <tr>
                <td bgcolor=\"".$Globals{'headcolor'}."\" align=\"center\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                color=\"".$Globals{'headfontcolor'}."\"
                class=\"thtcolor\"><b>TechIMO Forums Rules</b></font></td>
                </tr>
                <tr>
                <td bgcolor=\"".$Globals{'maincolor'}."\">
                <p><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'maintext'}."\">";

            if ($coppatext == "") {
                $output .= "All users under the age of 13 must seek permission from their parent or guardian in order to gain membership to ".$Globals{'webname'}.".
                    While we welcome participation from members under 13, we require that a parent or guardian fax or mail back a signed permission
                    form before we grant membership.<p>

                    You can begin the registration process even before we receive a permission form by pressing the Agree button.  Or you
                    can cancel registration by pressing the Cancel button.<p>

                    You can download the permission form here: <A href=\"".$Globals{'maindir'}."/register.php?ppaction=cform\">permissions form</a>.
                    For more information about the
                    registration process, or ".$Globals{'webname'}." in general, please send email to ".$Globals{'adminemail'}.".";
            }
            else {
                $output .= $coppatext;
            }
        }

        $output .= "</td>
            </tr>
            </table>
            </td></tr></table>

            <p align=\"center\"><font face=\"verdana, arial, helvetica\" size=\"2\" >
            <form action=\"".$Globals{'maindir'}."/register.php\" method=\"get\">
            <input type=\"hidden\" name=\"agree\" value=\"yes\">
            <input type=\"hidden\" name=\"age\" value=\"$age\">
            <input type=\"hidden\" name=\"ppaction\" value=\"register\">
            <input type=\"submit\" value=\"Agree\">
            </font></p>
            </form>

            <form action=\"".$Globals{'maindir'}."/index.php\" method=\"get\">
            <p align=\"center\"><font face=\"verdana, arial, helvetica\" size=\"2\" >
            <input type=\"submit\" value=\"Cancel\">
            </font></p>
            </form>";
    }

    // If they agreed to the rules, spit out the reg form
    if ($agree == "yes") {
        if ($age == "coppa") {
            $output .= "<Br><b>&nbsp;Please Note:</b> Until we receive a signed <a
                href=\"".$Globals{'maindir'}."/register.php?ppaction=cform\">permission form</a> from your parent or guardian you
                will be unable to upload photos or post comments.<p>";
        }

        $output .= "<form action=\"".$Globals{'maindir'}."/register.php\" method=\"post\">

            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\" width=\"".$Globals{'tablewidth'}."\"
            align=\"center\"><tr><td>
            <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr>
            <td colspan=\"2\"bgcolor=\"".$Globals{'maincolor'}."\" width=\"100%\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'maintext'}."\"><b>Register for ".$Globals{'webname'}."</b></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'headcolor'}."\" colspan=\"2\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'headfontcolor'}."\"><b>Required Info</b></font>
            <font face=\"verdana,arial,helvetica\" size=\"1\"  color=\"".$Globals{'headfontcolor'}."\">(The info in this section is required. Please
            note that passwords are case sensitive.)</font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'commentstext'}."\"><b>Username:</b></font></td>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><input
            type=\"text\" name=\"pick_username\" size=\"25\" maxlength=\"16\"></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'commentstext'}."\"><b>Password:</b></font></td>
            <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><input
            type=\"password\" name=\"password\" size=\"25\" maxlength=\"15\"></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'commentstext'}."\"><b>Enter
            Password Again:</b></font></td>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><input type=\"password\"  name=\"passwordconfirm\" size=\"25\" maxlength=\"15\"></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'commentstext'}."\"><b>Email:</b></font><br>
            <font face=\"verdana,arial,helvetica\" size=\"1\" >Please enter a valid email address. It will not be visible to the
            public.</font></td>
            <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'commentstext'}."\"><input
            type=\"text\" name=\"email\" size=\"25\" maxlength=\"50\"></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><b>Enter
            Email Again:</b></font></td>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><input
            type=\"text\" name=\"emailconfirm\" size=\"25\" maxlength=\"50\"></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'headcolor'}."\" colspan=\"2\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'headfontcolor'}."\"><b>Optional Info</b></font>
            <font face=\"verdana,arial,helvetica\" size=\"1\" color=\"".$Globals{'headfontcolor'}."\">The info below will be visible to
            ".$Globals{'webname'}." visitors.</font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'commentstext'}."\"><b>Homepage:</b></font></td>
            <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><input
            type=\"text\" name=\"homepage\" value=\"http://\" size=\"25\" maxlength=\"100\"></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><b>ICQ
            Number:</b></font></td>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><input
            type=\"text\" name=\"icq\" size=\"25\" maxlength=\"20\"></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><b>AOL
            Instant Messenger Handle:</b></font></td>
            <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><input
            type=\"text\" name=\"aim\" size=\"25\" maxlength=\"20\"></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><b>Yahoo
            Messenger Handle:</b></font></td>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><input
            type=\"text\" name=\"yahoo\" size=\"25\" maxlength=\"20\"></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'commentstext'}."\"><b>Birthdate:</b></font><br>
            <font face=\"verdana,arial,helvetica\" size=\"1\">If you select your birthday then other forum members will be able to see
            your birthday in your profile.</font></td>
            <td bgcolor=\"".$Globals{'altcolor1'}."\" valign=\"top\">

            <table border=\"0\" cellspacing=\"0\" cellpadding=\"2\">
            <tr>
            <td align=\"center\"><font face=\"verdana,arial,helvetica\" size=\"1\" >Month</font></td>
            <td align=\"center\"><font face=\"verdana,arial,helvetica\" size=\"1\" >Day</font></td>
            <td align=\"center\"><font face=\"verdana,arial,helvetica\" size=\"1\" >Year</font></td>
            </tr>
            <tr>
            <td><font face=\"verdana,arial,helvetica\" size=\"1\" ><select name=\"month\">
            <option value=\"-1\" ></option>
            <option value=\"1\" >January</option>
            <option value=\"2\" >February</option>
            <option value=\"3\" >March</option>
            <option value=\"4\" >April</option>
            <option value=\"5\" >May</option>
            <option value=\"6\" >June</option>
            <option value=\"7\" >July</option>
            <option value=\"8\" >August</option>
            <option value=\"9\" >September</option>
            <option value=\"10\" >October</option>
            <option value=\"11\" >November</option>
            <option value=\"12\" >December</option>
            </select></font></td>
            <td><font face=\"verdana,arial,helvetica\" size=\"1\"><select name=\"day\">
            <option value=\"-1\" ></option>
            <option value=\"1\" >1</option>
            <option value=\"2\" >2</option>
            <option value=\"3\" >3</option>
            <option value=\"4\" >4</option>
            <option value=\"5\" >5</option>
            <option value=\"6\" >6</option>
            <option value=\"7\" >7</option>
            <option value=\"8\" >8</option>
            <option value=\"9\" >9</option>
            <option value=\"10\" >10</option>
            <option value=\"11\" >11</option>
            <option value=\"12\" >12</option>
            <option value=\"13\" >13</option>
            <option value=\"14\" >14</option>
            <option value=\"15\" >15</option>
            <option value=\"16\" >16</option>
            <option value=\"17\" >17</option>
            <option value=\"18\" >18</option>
            <option value=\"19\" >19</option>
            <option value=\"20\" >20</option>
            <option value=\"21\" >21</option>
            <option value=\"22\" >22</option>
            <option value=\"23\" >23</option>
            <option value=\"24\" >24</option>
            <option value=\"25\" >25</option>
            <option value=\"26\" >26</option>
            <option value=\"27\" >27</option>
            <option value=\"28\" >28</option>
            <option value=\"29\" >29</option>
            <option value=\"30\" >30</option>
            <option value=\"31\" >31</option>
            </select></font></td>
            <td><font face=\"verdana,arial,helvetica\" size=\"1\"><input type=\"text\" name=\"year\" value=\"\" size=\"4\"
            maxlength=\"4\"></font></td>
            </tr>
            </table>

            </td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'commentstext'}."\"><b>Biography:</b></font><br>
            <font face=\"verdana,arial,helvetica\" size=\"1\" color=\"".$Globals{'commentstext'}."\">Some info about you</font></td>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><input
            type=\"text\" name=\"bio\" value=\"\" size=\"25\" maxlength=\"250\"></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'commentstext'}."\"><b>Location:</b></font><br>
            <font face=\"verdana,arial,helvetica\" size=\"1\" color=\"".$Globals{'commentstext'}."\">Your Geographic Location</font></td>
            <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><input
            type=\"text\" name=\"location\" value=\"\" size=\"25\" maxlength=\"250\"></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'commentstext'}."\"><b>Interests:</b></font><br>
            <font face=\"verdana,arial,helvetica\" size=\"1\" color=\"".$Globals{'commentstext'}."\">Your hobbies</font></td>
            <td bgcolor=\"".$Globals{'altcolor2'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><input
            type=\"text\" name=\"hobbies\" value=\"\" size=\"25\" maxlength=\"250\"></font></td>
            </tr>
            <tr>
            <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\"
            color=\"".$Globals{'commentstext'}."\"><b>Occupation:</b></font><br>
            <font face=\"verdana,arial,helvetica\" size=\"1\" color=\"".$Globals{'commentstext'}."\">What you do for a living.</font></td>
            <td bgcolor=\"".$Globals{'altcolor1'}."\"><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'commentstext'}."\"><input
            type=\"text\" name=\"occupation\" value=\"\" size=\"25\" maxlength=\"250\"></font></td>
            </tr>
            </table>
            </td></tr></table><p>
            <center>
            <input type=\"hidden\" name=\"ppaction\" value=\"submit\">
            <input type=\"hidden\" name=\"age\" value=\"$age\">
            <input type=\"submit\" value=\"Submit Registration Form\"></form>
            <form action=\"".$Globals{'maindir'}."/index.php\" method=\"get\">
            <input type=\"submit\" value=\"Cancel\">
            </form>";
    }
}

// Process registration input, send verify email or enable acct

if ($ppaction == "submit") {
    $reason = "";

    if ($pick_username == "") {
        $reason .= "<li>The username is blank.";
        $stop = 1;
    }

    wordchars( $pick_username ); // check username for bad characters

    $query = "SELECT userid FROM users WHERE username='$pick_username' LIMIT 1";
    $resulta = mysql_query_eval($query, $link);
    $matchu = mysql_num_rows( $resulta );

    if ( $matchu > 0 ) {
        $reason .= "<li>The username you chose already exists in our database.  Please choose another.";
        $stop = 1;
    }

    if ($Globals{'emailunique'} == "yes") {
        $query = "SELECT email FROM users WHERE email='$email' LIMIT 1";
        $resulta = mysql_query_eval($query,$link);
        $row = mysql_fetch_array($resulta);
        $dbemail = $row['email'];
        mysql_free_result($resulta);

        if ( !strcasecmp($dbemail, $email) ) {
            $reason .= "<li>The email address you entered already exists in our database.";
            $stop = 1;
        }
    }

    if ($email != "") {
        if ( !strstr($email, "@") ) {
            $reason .= "<li>The email address you entered is not valid. It must contain an \"@\" symbol.";
            $stop = 1;
        }

        if ( !strstr($email, ".") ) {
            $reason .= "<li>The email address you entered is not valid. It must contain a period.";
            $stop = 1;
        }
    }

    if ($password == "") {
        $reason .= "<li>The password is blank.";
        $stop = 1;
    }

    if ($password != "") {
        $pwdlength = strlen($password);
        if ($pwdlength < 4) {
            $reason .= "<li>Your password must be at least 4 characters long.";
            $stop = 1;
        }
    }

    if ($pick_username != "") {
        $userlength = strlen($pick_username);
        if ($userlength < 2) {
            $reason .= "<li>Your username must be at least 2 characters long.";
            $stop = 1;
        }
    }

    if ($passwordconfirm == "") {
        $reason .= "<li>The password verification field is blank.";
        $stop = 1;
    }

    if ($password != $passwordconfirm) {
        $reason .= "<li>The password does not match the password verification field.";
        $stop = 1;
    }

    if ($email == "") {
        $reason .= "<li>The email field is blank.";
        $stop = 1;
    }

    if ($emailconfirm == "") {
        $reason .= "<li>The email verification field is blank.";
        $stop = 1;
    }

    if ($emailconfirm != $email) {
        $reason .= "<li>The email field does not match the email verification field.";
        $stop = 1;
    }

    if ($stop == 1) {
        dieWell($reason);
    }

    $homepage = fixstring( $homepage );
    $icq = fixstring( $icq );
    $aim = fixstring( $aim );
    $yahoo = fixstring( $yahoo );
    $year = fixstring( $year );
    $hobbies = fixstring( $hobbies );
    $occupation = fixstring( $occupation );
    $location = fixstring( $location );

    $ipaddress = findenv("REMOTE_ADDR");

    list($dsec,$dmin,$dhour,$dmday,$dmon,$dyear,$dwday,$dyday,$disdst) = localtime();
    $mon = $mon + 1;
    $joindate = mktime($hour,$min,$sec,$mon,$mday,$year);

    if ($age == "coppa") {
        $userlevel=2;
    }

    if ($age == "adult") {
        if ($Globals{'emailverify'} == "yes") {
            $userlevel=3;
        }
        else {
            $userlevel=4;
        }
    }

    $passwordmd5 = md5($password);
    $birthday = "";
    if ( $year != "" && $month != "-1" && $day != "-1" ) {
        $birthday = "$year-$month-$day";
    }

    $pick_username = addslashes( $pick_username );
    $email = addslashes( $email );
    $homepage = addslashes( $homepage );
    $location = addslashes( $location );
    $hobbies = addslashes( $hobbies );
    $occupation = addslashes( $occupation );
    $bio = addslashes( $bio );

    $query = "INSERT INTO users values(NULL,'$userlevel','$pick_username','$passwordmd5','$email','$homepage','$icq','$aim','$yahoo','$joindate','0','$birthday','$ipaddress','$location','$hobbies','$occupation','$bio')";
    $resulta = mysql_query_eval($query,$link);
    $newuserid = mysql_insert_id( $link );

    if ( !$newuserid ) {
        dieWell( "We experienced a problem adding your account. Please contact the System Administrator!" );
        exit;
    }

    if ($age == "adult") {
        if ($Globals{'emailverify'} == "yes") {
            $query = "SELECT userid FROM users WHERE username='$pick_username' AND joindate='$joindate' LIMIT 1";
            $resulta = mysql_query_eval($query,$link);
            $row = mysql_fetch_array($resulta);
            $theuid = $row['userid'];
            mysql_free_result($resulta);

            $email_from = "From: ".$Globals{'adminemail'};

            $letter = "Thanks for registering at ".$Globals{'webname'}.".

                In order to activate your account, which will enable you to upload photos and post comments (if
                the site allows comments), you must click on the link below or copy and paste it into your browser:

                ".$Globals{'maindir'}."/register.php?ppaction=vfy&uid=$theuid&knum=$joindate

                Thanks!

                The ".$Globals{'webname'}." Team
                ".$Globals{'domain'};

            $subject = "Confirm ".$Globals{'webname'}." Registration (action needed)";
            mail( $email, $subject, $letter, $email_from );

            $done = "Thanks for registering. Please check your email for instructions on activating your new account.<p>

                <A href=\"".$Globals{'maindir'}."/index.php\"><font color=\"".$Globals{'maintext'}."\">Return to
                the front page of ".$Globals{'galleryname'}."</font></a>.";

            dieWell( $done );
        }
    }

    $done = "Thanks for registering, your account is active!

        <A href=\"".$Globals{'maindir'}."/index.php\"><font color=\"".$Globals{'maintext'}."\">Return to the front page of ".$Globals{'galleryname'}."</font></a>.";

    dieWell($done);
}

// Verify a user's email, change status from unregistered to registered

if ($ppaction == "vfy") {
    $query = "SELECT joindate,usergroupid FROM users WHERE userid=$uid LIMIT 1";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_array($resulta);
    $joindate = $row['joindate']; $ugid = $row['usergroupid'];
    mysql_free_result($resulta);

    if ($joindate == $knum && ($ugid == 3 || $ugid == 4)) {
        $query = "UPDATE users SET usergroupid=4 WHERE userid=$uid";
        $resulta = mysql_query_eval($query,$link);

        $done = "Thanks for confirming your account.  It is now active!<Br><A href=\"".$Globals{'maindir'}."/index.php\"><font
            color=\"".$Globals{'maintext'}."\">Return to
            the front page of ".$Globals{'galleryname'}."</font></a>.";
        dieWell($done);
    }
    else {
        $done = "Sorry, no match.  Check the link and try again.";
        dieWell($done);
    }
}

if ($ppaction == "cform") {
    $Globals{'commentstext'}="#000000";
    $Globals{'altcolor1'}="#FFFFFF";
    $Globals{'altcolor2'}="#FFFFFF";

    topmenu();

    $output = "$header$toplinks
        <Center><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" height=\"40\" width=\"".$Globals{'tablewidth'}."\"><Tr>
        <Td valign=\"center\" width=\"50%\">&nbsp;$childnav</td>
        <td width=\"50%\" align=\"right\" valign=\"center\"><font face=\"verdana, arial\" size=\"2\">$menu&nbsp;</font></td></tr></table>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"100%\"
        align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\"
        color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"4\"
        face=\"verdana\">".$Globals{'galleryname'}."</font>
        </font></td></tr><!-- CyKuH [WTN] -->

        <tr align=\"center\">
        <td align=\"left\" colspan=\"2\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\"
        color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\" face=\"verdana\"><b>COPPA Permission Form</font>
        </font></td></tr><!-- CyKuH [WTN] -->
        <tr><td colspan=\"2\" bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'maintext'}."\">Please have a
        parent/guardian print out this page, fill in the blanks below, and
        mail this permissions
        form to us at the following address:<br>
        ".$Globals{'address'}."<Br></td></tr>
        <tr><td bgcolor=\"#FFFFFF\" width=\"20%\" align=\"right\"><font size=\"2\" face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\">Username:</font></td><td
        bgcolor=\"#FFFFFF\"></font></td></tr>
        <tr><td bgcolor=\"#FFFFFF\" align=\"right\"><font size=\"2\" face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\">Password:</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\" face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\">$usergroup</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\" align=\"right\"><font size=\"2\"
        face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\">Email Address:</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\">$posts</td></tr>
        <tr><Td colspan=\"2\" bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\"><B>The fields below are
        optional</b></font></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor1'}."\" align=\"right\"><font
        size=\"2\"
        face=\"verdana\" color=\"".$Globals{'commentstext'}."\">Birthday:</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"><font size=\"2\"
        face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\"></font></td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\" align=\"right\"><font size=\"2\" face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\">Homepage:</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\"><a href=\"$homepage\">$homepage</a></font></td></tr>
        <tr><td
        bgcolor=\"".$Globals{'altcolor1'}."\" align=\"right\"><font size=\"2\"
        face=\"verdana\" color=\"".$Globals{'commentstext'}."\">ICQ Number:</font></td><td bgcolor=\"".$Globals{'altcolor1'}."\"
        align=\"right\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'commentstext'}."\">$icq</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\" align=\"right\"><font size=\"2\"
        face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\">AIM Number:</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\">$aim</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\" align=\"right\"><font size=\"2\"
        face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\">Interests:</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\" face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\">$aim</td></tr>
        <tr><td bgcolor=\"".$Globals{'altcolor2'}."\" align=\"right\"><font size=\"2\"
        face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\">Bio / About You:</font></td><td bgcolor=\"".$Globals{'altcolor2'}."\"><font size=\"2\"
        face=\"verdana\"
        color=\"".$Globals{'commentstext'}."\">$aim</td></tr>
        </table></td></tr></table>";
}

print "$output.".$Globals{'cright'}."$footer";

?>

