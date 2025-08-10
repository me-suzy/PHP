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
////////////////// MySQL Database Configuration ////////////////////////

// PhotoPost database host address, or leave as localhost
$host="localhost";

// PhotoPost's database name
$database="photopost";

// MySQL username and password to access PhotoPost's database
//
// These two variables are for the userid and password needed to access
// the PhotoPost database named above.
$mysql_user="root";
$mysql_password="mypass";

// User database host address, or leave as localhost
$host_bb="localhost";

// User database MySQL database name
//
// This is the variable for the User Database; if you are using Internal
// as your registration system, then these variables are the same as the
// ones above.  If you are linking to a message board system,
// thse variables should be set to the database, user and password for that
// database.
$database_bb="photopost";

// MySQL username and password to access user database
//
// These two variables are for the userid and password needed to access
// the PhotoPost or BB database.
//
$user_bb="root";
$password_bb="";

////////////////// Application Configuration ////////////////////////
// These variables set the path to the UNZIP and MOGRIFY commands on your system
// This only needs to be set if you are allowing ZIP uploads. These are full paths,
// including the name of the executable (.exe extensions for windows)
$zip_command = "/usr/bin/unzip";

// windows example:
// $mogrify_command = "c:\ImageMagick\mogrify.exe";
$mogrify_command = "/usr/lib/X11/mogrify";

// Debug variable.
// 0 = No debug notifications
// 1 = Program should generate an email and send it to the site administrator
// 2 = Program should terminate with a formatted screen with error message
// When set to 0 or 1, the program will not end on non-fatal errors.
$debug=2;

// Cookie variable
// This should be set to match the path for your cookies, / sets the cookie
// to be usable throughout the site. If your BB system has a different setting,
// then you need to put that path here as well.
$cookie_path="/";

// BotBuster integration
// http://www.botbuster.com
// Set to "yes" if you have BotBuster on your system (this includes the necessary tag)
$botbuster="no";

// ZLIB compression
// Set to "1" if you want to enable Zlib compression
$compression="0";

// vBPortal Integration
// If you want to include the vBPortal header, footer, and left menu, remove
// the "//" slashes from the beginning of the 8 lines of code below, and change
// "/home/public_html/vbportal" to your actual path to vbportal's main directory,
// and change "/home/public_html/photopost" to your actual path to PhotoPost's
// directory. This will override the default header and footer variables set in the
// PhotoPost admin panel.

// $vbportal="/home/public_html/vbportal"; // No ending slash
// $pppath ="/home/public_html/photopost"; // No ending slash
// chdir($vbportal . "/");
// require ("mainfile.php");
// $index = 0;
// global $Pmenu,$Pheader;
// $Pheader="P_themeheader";
// $Pmenu="P_thememenu_photopost";
// require("header.php");
// $vbportal=ob_get_contents();
// ob_end_clean();
// ob_start();
// require("footer.php");
// $vbfooter=ob_get_contents();
// ob_end_clean();
// chdir($pppath . "/");

// vBulletin Integration
// Instead of using the static header/footer file specified in the Admin options
// panel, you can use your existing default vBulletin header/footer.  Just change
// $vbpath and $pppath below to the proper full paths and remove the "//" slashes
// from the beginning of the 16 lines of code below.  If PhotoPost has an odd
// background color or squished width, you will need to edit vbulletin's default
// "header" style input box / template and change "{pagebgcolor}" and "{tablewidth}"
// (near the bottom) to your preferred background color and table width, respectively.
//$vbpath ="/www/forum"; // changeme
//$pppath ="/www/photopost"; // changeme
//chdir($vbpath . "/");
//require($vbpath . "/global.php");
//ob_start();
//eval("dooutput(\"".gettemplate('headinclude')."\",0);");
//$bodytag="<body>";
//echo dovars($bodytag,0);
//eval("dooutput(\"".gettemplate('header')."\",0);");
//$vbheader=ob_get_contents();
//ob_end_clean();
//ob_start();
//eval("dooutput(\"".gettemplate('footer')."\",0);");
//$vbfooter=ob_get_contents();
//ob_end_clean();
//chdir($pppath . "/");


// Don't change the line below - we only support mysql at this time.
$driver="mysql";
?>
