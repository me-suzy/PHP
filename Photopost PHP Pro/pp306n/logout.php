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

authenticate();

if ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
    setcookie( "bbuserid", "", time()-3600, $Globals{'cookie_path'} );
    setcookie( "bbpassword", "", time()-3600, $Globals{'cookie_path'} );
    $query = "DELETE FROM session WHERE host='$REMOTE_ADDR'";
    $resulta = mysql_query_eval($query,$db_link);
}

if ($Globals{'vbversion'} == "w3t" ) {
    setcookie( "w3t_myname", "", time()-3600, $Globals{'cookie_path'} );
    setcookie( "w3t_mypass", "", time()-3600, $Globals{'cookie_path'} );
}
if ($Globals{'vbversion'} == "w3t6") {
    setcookie( "w3t_myid", "", time()-3600, $Globals{'cookie_path'} );
    setcookie( "w3t_mypass", "", time()-3600, $Globals{'cookie_path'} );
    setcookie( "w3t_key", "", time()-3600, $Globals{'cookie_path'} );
}
if ($Globals{'vbversion'} == "phpBB2") {
    setcookie( "phpbb2uid", "", time()-3600, $Globals{'cookie_path'} );
    setcookie( "phpbb2pw", "", time()-3600, $Globals{'cookie_path'} );
}

if ($Globals{'vbversion'} == "phpBB" || $Globals{'vbversion'} == "Internal") {
    setcookie( "phpbbuid", "", time()-3600, $Globals{'cookie_path'} );
    setcookie( "phpbbpass", "", time()-3600, $Globals{'cookie_path'} );
}

$url = $Globals{'maindir'}."/index.php";
forward( $url, "Logging out! Please wait." );

?>
