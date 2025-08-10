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

if (empty($do)) $do="";

authenticate();

if ( $gologin==1 ) {
    $furl=$Globals{'maindir'};
    $furl= str_replace( $Globals{'domain'}, "", $furl );
    $furl="$furl/adm-index.php";
    login( $furl );
    exit;
}

if ( $adminedit != 1 ) {
    dieWell( "You are not a valid administrator!" );
    exit;
}

adminmenu();

function adminmenu() {
    global $Globals, $adminmenu;

    $logout = "<A href=\"".$Globals{'maindir'}."/logout.php?logout\">Logout</a>";

    if ($Globals{'vbversion'} == "Internal") {
        $userhtml= " | <A href=\"".$Globals{'maindir'}."/adm-users.php?ppaction=users\">Users</a>";
    }
    else
        $userhtml= "";    

    $adminmenu = "[ <A href=\"".$Globals{'maindir'}."/adm-index.php\">Approval</a> | <a
        href=\"".$Globals{'maindir'}."/adm-options.php?ppaction=options\">Options</a> | <a
        href=\"".$Globals{'maindir'}."/adm-cats.php?ppaction=cats\">Categories</a>$userhtml | <A
        href=\"".$Globals{'maindir'}."/adm-userg.php?ppaction=usergroups\">Usergroups</a> | <A href=\"".$Globals{'maindir'}."/index.php\">User
        Interface</a> | $logout ]";
}


?>

