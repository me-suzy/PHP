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
function dieWell( $message )  {
    $output = "<title>PhotoPost Installation</title>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#C6DFF7\"  width=\"100%\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"2\" align=\"left\" bgcolor=\"#0059B5\"><font face=\"verdana\"
        color=\"#FFFFFF\" size=\"4\">Installation Error&nbsp;&nbsp;<font size=2>Nullified by CyKuH [WTN]</font>
        </font></td>
        </tr><Tr><Td bgcolor=\"#FFFFFF\" height=\"300\" valign=\"middle\"><center><table cellpadding=\"0\"><Tr><Td><font
        face=\"verdana\"
        size=\"3\" color=\"#000000\">$message</font></td></tr></table></center></td></tr></table></td></tr></table>";

    print "$output";
    exit;
}

function findenv ( $name ) {
    global $HTTP_SERVER_VARS;

    $this = "";
    if (empty($HTTP_SERVER_VARS["$name"]))
        $HTTP_SERVER_VARS["$name"]="";
    if (empty($_ENV["$name"]))
        $_ENV["$name"]="";

    if(getenv($name) != '') {
        $this = getenv("$name");
    }

    if(($this == '') && ($HTTP_SERVER_VARS["$name"] != '')) {
        $this = $HTTP_SERVER_VARS["$name"];
    }

    if(($this == '') && ($_ENV["$name"] != '')) {
        $this = $_ENV["$name"];
    }

    return $this;
}


function HTTPPost() {
 }

function wordchars ( $string ) {

    $stripstring = ereg_replace( "[^a-zA-Z0-9/\:]", "", $string );

    if ( strcmp($string, $stripstring) ) {
        dieWell( "The username you chose is not valid. Usernames may not contain anything but numbers and letters." );
        exit;
    }

    return ( $stripstring );
}

// Get magic quote setting
   $magic = get_magic_quotes_gpc();

// --------------------------------
// Register the necessary variables
   if (is_array($HTTP_GET_VARS)) {
     while(list($key,$value) = each($HTTP_GET_VARS)) {
        if ($magic) {
           $value = stripslashes($value);
        }
        ${$key} = $value;
     }
   }
   if (is_array($HTTP_POST_VARS)) {
     while(list($key,$value) = each($HTTP_POST_VARS)) {
        if ($magic) {
           $value = stripslashes($value);
        }
        ${$key} = $value;
     }
   }
   if (is_array($HTTP_COOKIE_VARS)) {
     while(list($key,$value) = each($HTTP_COOKIE_VARS)) {
        if ($magic) {
           $value = stripslashes($value);
        }
        ${$key} = $value;
     }
   }

// ---------------------------
// Turn off the magic quoting
   set_magic_quotes_runtime(0);

if (empty($step)) $step="";

//print "Content-type: text/html\n\n";
$output = "<title>PhotoPost Install Script</title>";

if ($step == "") {
    $output .= "<Br><Br><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#C6DFF7\" width=\"650\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"6\" align=\"left\" bgcolor=\"#0059B5\"><font face=\"\" color=\"#ffffff\"
        size=\"1\"><font
        size=\"4\" face=\"verdana\">Welcome to PhotoPost Install&nbsp;&nbsp;<font size=2>Nullified by CyKuH [WTN]</font>
        </font></td></tr>
        <Tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">
        This installation routine will test your server to ensure that it has the necessary modules installed to run
        PhotoPost, and it will also set up your database for use.<p>
        <Div align=\"right\">
        <A href=\"install.php?step=1\">Proceed to Step #1 &raquo;&raquo; </a></div></font></td></tr></table></td></tr></table>";

    print $output;
}

if ($step == "1") {
    $domain=$HTTP_SERVER_VARS["HTTP_HOST"];
    $output="<Br><Br><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#C6DFF7\" width=\"650\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"6\" align=\"left\" bgcolor=\"#0059B5\"><font face=\"\" color=\"#ffffff\"
        size=\"1\"><font
        size=\"4\" face=\"verdana\">Checking Your Server</font>
        </font></td></tr>
        <Tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">";

    include("config-inc.php");

    $stop = 0;
    $output .= "<br>Testing UNZIP at: $zip_command";

    exec( $zip_command, $retdata, $retval );
    if ( $retval != 0 ) {
        $output .= ".... WARNING!<br><b>Cannot find the UNZIP command listed as: $zip_command</b><p>";
    }
    else $output .= ".... OK!<p>";

    $output .= "Testing MOGRIFY at: $mogrify_command";

    exec( $mogrify_command, $retdata, $retval );
    if ( $retval != 0 ) {
        $output .= ".... WARNING!<br><b>Cannot find ImagicMagick's MOGRIFY command listed as: $mogrify_command</b><p>
        Be sure that the name of the executable is listed in this variable.";
        $stop=1;
    }
    else $output .= ".... OK!<p>";


    if ( !$stop ) $output.="If you get WARNING's be sure the commands are in your server's path; otherwise you need to edit the config-inc.php file..<p>
        The next step will configure your database settings.<p>

        <Div align=\"right\"><a href=\"install.php?step=2UPGRADE\">Click here to UPGRADE your v2.5X database --></a><br>
        <a href=\"install.php?step=2\">Click here for a FRESH install --></a></div></font></td></tr></table></td></tr></table>";
    else $output .= "<p>You will need to modify your config-inc.php file and then refresh this page to try again.";

    print $output;
}

if ($step == "2") {
    $output = "<Br><Br><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#C6DFF7\" width=\"650\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"6\" align=\"left\" bgcolor=\"#0059B5\"><font face=\"\" color=\"#ffffff\"
        size=\"1\"><font
        size=\"4\" face=\"verdana\">Setting up the PhotoPost database&nbsp;&nbsp;<font size=2>Nullified by CyKuH [WTN]</font>
        </font></td></tr>
        <Tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">";

    include("config-inc.php");
    //===========================================================

    // Connecting, selecting database
    $link = mysql_connect("$host", "$mysql_user", "$mysql_password") or die('I cannot connect to the PhotoPost database. [host:$host][mysql_user:$mysql_user][mysql_password:$mysql_password]'); //CyKuH [WTN]
    mysql_select_db ("$database", $link)or die("Could not connect to PhotoPost database");

    if ( file_exists( "photopost.sql")  ) {
        $filearray = file( "photopost.sql" );

        while ( list($num, $query) = each($filearray) ) {
            if ($query != "") {
                $query = str_replace( ";", "", $query);
                $setup = mysql_query($query,$link);
            }
        }

        for( $x=0; $x < 3000; $x++ ) {
            $query = "INSERT INTO useralbums values(NULL,'NULL',0,'NULL')";
            $resultb = mysql_query($query, $link);
            $thealbumid = mysql_insert_id( $link );

            $query = "DELETE FROM useralbums WHERE id=$thealbumid";
            $resultb = mysql_query($query, $link);
        }
    }
    else {
        dieWell("photopost.sql is missing.");
        exit;
    }

    $output .= "Database setup successful, <b>but you aren't finished yet</b>. The final installation step will help you
        begin configuring PhotoPost for your server.<p>

        Now you must choose whether you intend to use PhotoPost's internal registration system, vBulletin's,
        UBBThreads', phpBB's or phpBB2's to register yourself and your users to grant access to upload
        photos and post comments,<p>

        If you do not own vBulletin, UBBThreads, phpBB, or phpBB2, or you do not wish PhotoPost to work with those
        applications, then choose:<br>
        <Center><font size=\"+1\"><B><a href=\"install.php?step=3&regtype=internal\">PhotoPost Internal User
        Registration</a></b></font><p></center>

        If you want PhotoPost to use vBulletin's, UBBThreads's or phpBB's existing user database and registration system, choose:<Br>
        <Center><font size=\"+1\"><b><A href=\"install.php?step=3&regtype=external\">External User
        Registration<Br>(vBulletin, UBBT5, UBBT6, phpBB, or phpBB2)</a></b></font></center><p>
        </font></td></tr></table></td></tr></table>";

    print $output;
    exit;
}

if ($step == "2UPGRADE") {
    $output = "<Br><Br><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#C6DFF7\" width=\"650\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"6\" align=\"left\" bgcolor=\"#0059B5\"><font face=\"\" color=\"#ffffff\"
        size=\"1\"><font
        size=\"4\" face=\"verdana\">Setting up the PhotoPost database</font>
        </font></td></tr>
        <Tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">";

    include("config-inc.php");
    //===========================================================

    // Connecting, selecting database
    $link = mysql_connect("$host", "$mysql_user", "$mysql_password") or die('I cannot connect to the PhotoPost database. [host:$host][mysql_user:$mysql_user][mysql_password:$mysql_password]');
    mysql_select_db ("$database", $link)or die("Could not connect to PhotoPost database");

    if ( file_exists( "upgrade252.sql")  ) {
        $filearray = file( "upgrade252.sql" );

        while ( list($num, $query) = each($filearray) ) {
            if ($query != "") {
                $query = str_replace( ";", "", $query);
                $setup = mysql_query($query,$link);
            }
        }
    }
    else {
        dieWell("upgrade252.sql is missing.");
        exit;
    }

    $output .= "Database upgrade successful (unless you see errors, that is),<br> <b>but you aren't finished yet</b>. The final installation step will help you
        begin configuring PhotoPost for your server.<p>

        Now you must choose whether you intend to use PhotoPost's internal registration system, vBulletin's,
        UBBThreads', phpBB's, or phpBB2's to register yourself and your users to grant access to upload
        photos and post comments,<p>

        If you do not own vBulletin, UBBThreads, phpBB, or phpBB2, or you do not wish PhotoPost to work with those
        applications, then choose:<br>
        <Center><font size=\"+1\"><B><a href=\"install.php?step=3&regtype=internal\">PhotoPost Internal User
        Registration</a></b></font><p></center>

        If you want PhotoPost to use vBulletin's, UBBThreads's or phpBB's existing user database and registration system, choose:<Br>
        <Center><font size=\"+1\"><b><A href=\"install.php?step=3&regtype=external\">External User
        Registration<Br>(vBulletin, UBB5, UBB6, phpBB, or phpBB2)</a></b></font></center><p>
        </font></td></tr></table></td></tr></table>";

    print $output;
    exit;
}

if ($step == "3") {
    $output = "<Br><Br>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#C6DFF7\" width=\"650\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"6\" align=\"left\" bgcolor=\"#0059B5\"><font face=\"\" color=\"#ffffff\"
        size=\"1\"><font
        size=\"4\" face=\"verdana\">Configuring PhotoPost&nbsp;&nbsp;<font size=2>Nullified by CyKuH [WTN]</font>
        </font></td></tr>
        <Tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">

        This is the final step to install PhotoPost.  I have filled in the fields below based on my best guesses about your
        server configuration.  Please check to make sure that the fields are accurate, make any corrections, then submit.  If
        you don't know what the values should be, either leave them as they are or ask your web host.  You can change them later
        in PhotoPost's admin interface, but please note that the URL to your VBulletin installation MUST be correct at this
        point.<p>";

    $website = "http://".findenv("SERVER_NAME");
    $vb="$website/forum";

    $fullpath = findenv("PATH_TRANSLATED");

    if (empty($fullpath))
        $fullpath = findenv("SCRIPT_FILENAME");

    $datapath = str_replace( "install.php", "data", $fullpath);

    $docroot = findenv("DOCUMENT_ROOT");
    $photourl = findenv("SCRIPT_NAME");
    $photourl = str_replace( "install.php", "", $photourl);
    $photourl = "$website$photourl";
    $dataurl = $photourl."data";
    $imagesurl = $photourl."images";

    if ($regtype == "internal") {
        $regtext = "<tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">Admin Username:</font></td><Td bgcolor=\"#FFFFFF\"><input
            type=\"text\" size=\"50\" name=\"username\"></td></tr>
            <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">Admin Password:</font></td><Td bgcolor=\"#FFFFFF\"><input
            type=\"password\" size=\"50\" name=\"password\"></td></tr>
            <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">Enter Password Again:</font></td><Td bgcolor=\"#FFFFFF\"><input
            type=\"password\" size=\"50\" name=\"passwordconfirm\"></td></tr>
            <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">Admin Email Address:</font></td><Td bgcolor=\"#FFFFFF\"><input
            type=\"text\" size=\"50\"
            name=\"adminemail\"></td></tr>
            <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">Enter Email Again:</font></td><Td
            bgcolor=\"#FFFFFF\"><input type=\"text\" size=\"50\" name=\"emailconfirm\">
            <input type=\"hidden\" name=\"id41\" value=\"Internal\"></td></tr>";
    }
    if ($regtype == "external") {
        $regtext = "<tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">Your Forum</font><br>
            <font size=\"1\" face=\"verdana\">Only vBulletin 2.0.3, 2.2.0 (and 2.2.1), UBBThreads (5, 6) and phpBB 1.4.4, are supported at this
            time. If you have vBulletin 2.2.1 or newer, choose \"2.2.0\".</font></td><Td
            bgcolor=\"#FFFFFF\">
            <select name=\"id41\">
            <option selected value=\"2.2.0\">2.2.0</option>
            <option value=\"2.0.3\">2.0.3</option>
            <option value=\"phpBB\">phpBB</option>
            <option value=\"phpBB2\">phpBB2</option>
            <option value=\"w3t\">UBBThreads v5</option>
            <option value=\"w3t6\">UBBThreads v6</option>
            </select></td></tr>";
    }

    $output .= "<form method=\"POST\" action=\"install.php\">
        <input type=\"hidden\" name=\"step\" value=\"4\">
        <input type=\"hidden\" name=\"ppaction\" value=\"final\">
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#C6DFF7\" width=\"650\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        $regtext
        <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">Your website name (i.e.: TechIMO.com)</font></td><Td bgcolor=\"#FFFFFF\"><input type=\"text\" value=\"\" name=\"id2\"
        size=\"50\"></td></tr>
        <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">Website URL to homepage</font></td><Td bgcolor=\"#FFFFFF\"><input type=\"text\" value=\"$website\" name=\"id3\"
        size=\"50\"></td></tr>
        <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">URL to PhotoPost scripts directory</font>
        </td><Td
        bgcolor=\"#FFFFFF\"><input type=\"text\" value=\"$photourl\" name=\"id11\"
        size=\"50\"></td></tr> ";

    if ($regtype != "internal") {
        $output .= "<tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">URL to Forum directory</font><br>
            <font size=\"1\" face=\"verdana\" color=\"red\">Please make sure this is correct!</font></td><Td bgcolor=\"#FFFFFF\"><input
            type=\"text\" value=\"$vb\" name=\"id7\" size=\"50\"></td></tr>
            <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">Database table prefix for phpBB 2.0 (you may have chosen
            this initially when you installed your forum - leave blank if none):
            </font></td><Td bgcolor=\"#FFFFFF\"><input type=\"text\"
            value=\"\" name=\"id83\" size=\"50\"></td></tr>";
    }

    $output .= "<tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">URL to PhotoPost images directory</font></td><Td bgcolor=\"#FFFFFF\"><input type=\"text\" value=\"$imagesurl\" name=\"id49\" size=\"50\"></td></tr>
        <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">URL to PhotoPost data directory</font></td><Td bgcolor=\"#FFFFFF\"><input type=\"text\" value=\"$dataurl\" name=\"id5\"
        size=\"50\"></td></tr>
        <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">Full path to PhotoPost data directory</font></td><Td bgcolor=\"#FFFFFF\"><input type=\"text\" value=\"$datapath\"
        name=\"id6\" size=\"50\"></td></tr>
        </table></td></tr></table><Br>
        <Center>
        <input type=\"hidden\" name=\"regtype\" value=\"$regtype\">
        <input type=\"submit\" value=\"Complete PhotoPost Installation\"><Br>&nbsp;</center></td></tr></table></td></tr></table>";

    print $output;
    exit;
}

if ($step == "4") {
    if ($ppaction == "final") {
        $checkgroups = "";

        if ($id41 == "phpBB2") {
            $checkgroups = "<p>We imported your forum usergroups, but you must configure usergroup access
                permissions in PhotoPost's admin panel before you configure anything else.<p>";
        }

        $output = "<Br><Br>
            <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#C6DFF7\" width=\"650\" align=\"center\"><tr><td>
            <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr align=\"center\">
            <td colspan=\"6\" align=\"left\" bgcolor=\"#0059B5\"><font face=\"\" color=\"#ffffff\"
            size=\"1\"><font size=\"4\" face=\"verdana\">PhotoPost Installation is Complete&nbsp;&nbsp;<font size=2>Nullified by CyKuH [WTN]</font>
            </font></td></tr><Tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\">";

        if ($regtype == "external") {
            if ($id7 == "") {
                $dtext .= "The URL to your forum directory must be filled in.</td></tr></table></td></tr></table>";
                dieWell($dtext);
            }
        }
        require "config-inc.php";

        $link = mysql_connect("$host:3306", "$mysql_user", "$mysql_password") or die ('I cannot connect to the PhotoPost database.');
        mysql_select_db ("$database",$link)or die("Could not connect to PhotoPost database");

        $db_link = mysql_connect("$host_bb", "$user_bb", "$password_bb") or die ('I cannot connect to the Members database.');
        mysql_select_db ("$database_bb",$db_link)or die("Could not connect to User database");

        $reason="";
        if ($regtype == "internal") {
            wordchars($username); //check username for bad characters

            if ($adminemail != "") {
                if ( !strstr($adminemail, "@") ) {
                    $reason .= "<li>The email address you entered is not valid. It must contain an \"@\" symbol.";
                }
                if ( !strstr($adminemail, ".") ) {
                    $reason .= "<li>The email address you entered is not valid. It must contain a period.";
                }
            }
            if ($password == "") $reason .= "<li>The password is blank.";

            if ($password != "") {
                $pwdlength=strlen($password);
                if ($pwdlength < 4) $reason .= "<li>Your password must be at least 4 characters long.";
            }
            if ($username != "") {
                $userlength=strlen($username);
                if ($userlength < 2) $reason .= "<li>Your username must be at least 2 characters long.";
            }
            if ($passwordconfirm == "") $reason .= "<li>The password verification field is blank.";
            if ($password != $passwordconfirm) $reason .= "<li>The password does not match the password verification field.";
            if ($adminemail == "") $reason .= "<li>The email field is blank.";
            if ($emailconfirm == "") $reason .= "<li>The email verification field is blank.";
            if ($emailconfirm != $adminemail) $reason .= "<li>The email field does not match the email verification field.";
            if ($reason != "") {
                dieWell($reason);
            }

            list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
            $joindate = mktime($hour,$min,$sec,$mon,$mday,$year);

            $password=md5($password);
        }

        $query = "DELETE FROM usergroups";
        $resulta = mysql_query($query,$link);

        foreach($HTTP_POST_VARS as $id=>$setting) {
            //print "[$id]=[$setting]<br>";

            if ($id == "id41") {
                if ($setting != "2.0.3" && $setting != "2.2.0" && $setting != "Internal" && $setting !=
                "phpBB" && $setting != "w3t" && $setting != "w3t6" && $setting != "phpBB2") {
                    dieWell("Invalid user registration system type.  Must be 2.03, 2.2.0 (which is 2.2.1 compatible), Internal,
                    phpBB, phpBB2, w3t or w3t6");
                }
            }

            if ($id == "id11") {
                $len = strlen($setting);
                if ( $setting[$len-1] == "/" )
                    $setting[$len-1] = "";
            }

            if ($id == "id83") {
                $id83 = str_replace( "_", "", $id83 );
                $setting = str_replace( "_", "", $setting );
            }

            if ($id == "id6") {
                $len = strlen($setting);
                if ( strstr( $setting, "/" ) ) {
                    if( $setting[$len-1] != "/" ) $setting = "$setting/";
                }
                else {
                    if( $setting[$len-1] != "\\" ) $setting = "$setting\\\\";
                }
            }

            $id = str_replace( "id", "", $id );
            if ( is_numeric( $id ) ) {
                $setting = addslashes( $setting );

                $query = "UPDATE settings SET setting='$setting' WHERE id=$id";
                $resulta = mysql_query($query, $link)or die( "Failed MySQL Query: [$link]:$query<br>Error: ".mysql_error() );
                //print "$query<br>";
            }
        }

        if ($id41 == "phpBB") {
            if ( file_exists( "usergroups-phpbb.sql")  ) {
                $filearray = file( "usergroups-phpbb.sql" );

                while ( list($num, $query) = each($filearray) ) {
                    if ($query != "") {
                        $query = str_replace( ";", "", $query);
                        $setup = mysql_query($query, $link);
                    }
                }
            }
            else {
                dieWell("usergroups-phpbb.sql is missing.");
                exit;
            }
        }

        // For UBBThreads usergroups

        if ($id41 == "w3t" || $id41 == "w3t6") {
            if ( file_exists("usergroups-vb.sql")  ) {
                $filearray = file( "usergroups-vb.sql" );

                while ( list($num, $query) = each($filearray) ) {
                    if ($query != "") {
                        $query = str_replace( ";", "", $query);
                        $setup = mysql_query($query,$link);
                    }
                }
            }
            else {
                dieWell("usergroups-vb.sql is missing.");
                exit;
            }

            $query = "SELECT G_Id, G_Name FROM w3t_Groups";
            $readug = mysql_query($query,$db_link);

            while ( $row = mysql_fetch_row( $readug ) ) {
                list( $groupid, $title ) = $row;

                $title = addslashes( $title );

                if ( $title == "Administrators" ) {
                    $query="INSERT INTO usergroups VALUES('$groupid','$title','1','1','1','0','0','1','1')";
                }
                elseif ( $title == "Banned" || $title == "Unregistered" ) {
                    $query="INSERT INTO usergroups VALUES('$groupid','$title','0','0','0','0','0','0','0')";
                }
                else {
                    $query="INSERT INTO usergroups VALUES('$groupid','$title','0','1','1','0','0','1','1')";
                }
                $setug = mysql_query($query, $link);
            }
        }

        //# For phpBB2

        if ($id41 == "phpBB2") {
            $gprefix=$id83;
            if ( file_exists( "usergroups-phpbb2.sql")  ) {
                $filearray = file( "usergroups-phpbb2.sql" );

                while ( list($num, $query) = each($filearray) ) {
                    if ($query != "") {
                        $query = str_replace( ";", "", $query);
                        $setup = mysql_query($query,$link);
                    }
                }
            }
            else {
                dieWell("usergroups-phpbb2.sql is missing.");
                exit;
            }

            $grouptable=$gprefix ."_groups";
            $query = "SELECT group_id,group_name FROM $grouptable WHERE group_single_user=0 OR group_type=2";
            $readug = mysql_query($query,$db_link);

            if ( !$readug ) {
                dieWell("Error: Are you certain that your database prefix is set properly?<p>I cannot see your
                    phpBB2 \"<b>$grouptable</b>\" table.<p>The prefix setting adds characters to the front of phpBB2
                    table names - please doublecheck this.");
                exit;
            }

            while ( $row = mysql_fetch_row( $readug ) ) {
                list( $groupid, $title ) = $row;
                $title = addslashes( $title );

                $query = "INSERT INTO usergroups VALUES('$groupid','$title','0','0','0','0','0','1','1')";
                $setug = mysql_query($query,$link);
            }
        }

        //# For vBulletin

        if ($id41 == "2.0.3" || $id41 == "2.2.0") {
            if ( file_exists( "usergroups-vb.sql")  ) {
                $filearray = file( "usergroups-vb.sql" );

                while ( list($num, $query) = each($filearray) ) {
                    if ($query != "") {
                        $query = str_replace( ";", "", $query);
                        $setup = mysql_query($query,$link);
                    }
                }
            }
            else {
                dieWell("usergroups-vb.sql is missing.");
                exit;
            }

            $query = "SELECT usergroupid,title,cancontrolpanel,canpostnew FROM usergroup";
            $readug = mysql_query($query,$db_link);

            while ( $row = mysql_fetch_row( $readug ) ) {
                list( $groupid, $title, $cancontrol, $canpost ) = $row;
                $title = addslashes( $title );

                $query = "INSERT INTO usergroups VALUES('$groupid','$title','$cancontrol','$canpost','$canpost','0','0','$canpost','$canpost')";
                $setug = mysql_query($query,$link);
            }
        }

        //
        // Internal
        //

        if ($regtype == "internal") {
            if ( file_exists( "usergroups.sql")  ) {
                $filearray = file( "usergroups.sql" );

                while ( list($num, $query) = each($filearray) ) {
                    if ($query != "") {
                        $query = str_replace( ";", "", $query);
                        $setup = mysql_query($query,$link);
                    }
                }
            }
            else {
                dieWell("usergroups.sql is missing.");
                exit;
            }

            $username = addslashes( $username );
            $adminemail = addslashes( $adminemail );

            $query = "INSERT INTO users VALUES(NULL,'5','$username','$password','$adminemail',NULL,NULL,NULL,NULL,'$joindate','0','0000-00-00',NULL,NULL,NULL,NULL,NULL)";
            $lastug = mysql_query($query,$link);

            $query = "UPDATE settings SET setting='Internal' WHERE id='41'";
            $lastug = mysql_query($query,$link)or die( "Failed MySQL Query: $query / ".mysql_error() );

            $query = "UPDATE settings SET setting='$adminemail' WHERE id='4'";
            $lastug = mysql_query($query,$link)or die( "Failed MySQL Query: $query / ".mysql_error() );
        }

        $output .= "PhotoPost is installed.  <font color=\"red\">To prevent users from modifying your PhotoPost database settings, please be sure to delete the install.php file from
            your PhotoPost
            directory before continuing</font>. $checkgroups Open PhotoPost's <A
            href=\"adm-index.php\">adm-index.php</a> in your browser to configure options and create
            categories.</td></tr></table></td></tr></table>";

        print $output;
    }
}

?>

