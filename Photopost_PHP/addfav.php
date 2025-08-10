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
require "languages/$pplang/addfav.php";
require "login-inc.php";

list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();

if ( !isset($photo) || !isset($do) ) {
    diewell($Globals['pp_lang']['badcall']);
    exit;
}

authenticate();

if ( isset($Globals['ppboards']) && $adminedit != 1 ) {
    if ( $Globals['ppboards'] == "closed" ) {
        print $Globals['pp_lang']['closed'];
        exit;
    }
}

if ($userid == "") {
    diewell( $Globals['pp_lang']['noreg'] );
    exit;
}

if ( $do == "add" ) {
    $query = "REPLACE INTO favorites values(NULL,$userid,$photo)";
    $type = $Globals['pp_lang']['added'];
}
else {
    if ( !is_numeric($photo) || !is_numeric($userid) ) {
        diewell( $Globals['pp_lang']['malform'] );
        exit;
    }
    $type = $Globals['pp_lang']['removed'];
    $query = "DELETE FROM favorites WHERE photo=$photo AND userid=$userid";
}
$result = ppmysql_query($query, $link);

if ( isset($cat) ) {
    forward( "{$Globals['maindir']}/showgallery.php?cat=$cat&thumb=1", $type );
    exit;
}

forward( "{$Globals['maindir']}/showphoto.php?photo=$photo", $type );

?>
