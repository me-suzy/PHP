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
require "login-inc.php";

if (empty($do)) $do="";

authenticate();

if ( $gologin==1 ) {
    $furl=$Globals['maindir'];
    $furl= str_replace( $Globals['domain'], "", $furl );
    $furl="$furl/adm-index.php";
    login( $furl );
    exit;
}

if ( $adminedit != 1 ) {
    diewell( "You are not a valid administrator!" );
    exit;
}

adminmenu();

function adminmenu() {
    global $Globals, $adminmenu;

    $logout = "<a href=\"{$Globals['maindir']}/logout.php?logout\">Logout</a>";

    if ($Globals['vbversion'] == "Internal") {
        $userhtml= "| <a href=\"{$Globals['maindir']}/adm-users.php?ppaction=users\">Users</a>";
    }
    else
        $userhtml= "";    

    $adminmenu = "[ <a href=\"{$Globals['maindir']}/adm-index.php\">Approval</a> | <a
        href=\"{$Globals['maindir']}/adm-options.php?ppaction=options\">Options</a> | <a
        href=\"{$Globals['maindir']}/adm-db.php\">Scan Database</a> | <a 
        href=\"{$Globals['maindir']}/adm-move.php\">Bulk Move</a> | <a                       
        href=\"{$Globals['maindir']}/adm-cats.php?ppaction=cats\">Categories</a> | <a
        href=\"{$Globals['maindir']}/adm-pa.php?ppaction=albums\">Manage Albums</a> $userhtml | <a
        href=\"{$Globals['maindir']}/adm-userg.php?ppaction=usergroups\">Usergroups</a> | <a href=\"{$Globals['maindir']}/index.php\">User
        Interface</a> | $logout ]";
}

?>
