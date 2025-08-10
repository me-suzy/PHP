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

if ( IsSet( $login ) ) {
    $furl=$Globals{'maindir'};
    $furl= str_replace( $Globals{'domain'}, "", $furl );
    $furl="$furl/index.php";
    login( $furl );
    exit;
}

// Set to no vaifation, call authenticate and then check return
$gologin = 0;
authenticate( $user, $password );

if ( $gologin == 0 ) {
    if ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
        setcookie( "bbuserid", $userid, time()+2592000, $Globals{'cookie_path'} );
        setcookie( "bbpassword", md5($password), time()+2592000, $Globals{'cookie_path'} );
    }

    if ($Globals{'vbversion'} == "w3t" ) {
        setcookie( "w3t_myname", $user, time()+2592000, $Globals{'cookie_path'} );
        $dbpassword=md5( $password );
        setcookie( "w3t_mypass", $dbpassword, time()+2592000, $Globals{'cookie_path'} );
    }

    if ( $Globals{'vbversion'} == "w3t6" ) {        
        setcookie( "w3t_myid", $userid, time()+2592000, $Globals{'cookie_path'} );     
        $query = "SELECT U_Password from w3t_Users where U_Username = '$user' limit 1"; 
        $result = mysql_query_eval($query,$db_link);        
        $row = mysql_fetch_row($result);        
        list ( $dbpassword) = $row;        
        $db6password = md5("{$user}{$dbpassword}");        
        setcookie( "w3t_mypass", $dbpassword, time()+2592000, $Globals{'cookie_path'} );
        setcookie( "w3t_key", $db6password, time()+2592000, $Globals{'cookie_path'} );
    }    

    if ($Globals{'vbversion'} == "phpBB2") {
        setcookie( "phpbb2uid", $userid, time()+2592000, $Globals{'cookie_path'} );
        setcookie( "phpbb2pw", md5($password), time()+2592000, $Globals{'cookie_path'} );
    }

    if ($Globals{'vbversion'} == "phpBB" || $Globals{'vbversion'} == "Internal") {
        setcookie( "phpbbuid", $userid, time()+2592000, $Globals{'cookie_path'} );
        $dbpassword=md5($password);
        setcookie( "phpbbpass", $dbpassword, time()+2592000, $Globals{'cookie_path'} );
    }

    if ( $url == "" ) $url = $Globals{'maindir'}."/index.php";
    forward( $url, "Login Successful!" );
}
else {
    if ($Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
     $reglink = $Globals{'vbulletin'}."/newuser.php?Cat=";
    }
    if ($Globals{'vbversion'} == "phpBB") {
     $reglink = $Globals{'vbulletin'}."/bb_register.pl?mode=agreement";
    }
    if ($Globals{'vbversion'} == "phpBB2") {
     $reglink = $Globals{'vbulletin'}."/profile.php?mode=register";
    }
    if ($Globals{'vbversion'} == "Internal") {
     $reglink = $Globals{'maindir'}."/register.php";
    }

    $invalid = "Invalid login.  Please check your username and password, or <A
        href=\"$reglink\"><font
        color=\"".$Globals{'maintext'}."\">register</font></a>.";

    //$invalid .= "[$userid/$username/$user/$password/$dbpassword/$md5dbpass/$md5temppass]";

    dieWell( $invalid );
    exit;
}

?>

