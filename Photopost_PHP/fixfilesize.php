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

print "Fixing database: changing mediumint(9) to int(10) for photos.filesize...";

$query = "ALTER TABLE photos CHANGE filesize filesize int(10) default '0'";
$resultb = mysql_query($query, $link)or $mysql_eval_error = mysql_error();

if ( $resultb )
    print "was fixed!<br /><br />";
else
    print "was <b>not</b> fixed - Error returned was [$mysql_eval_error]<br /><br />";

print "Starting to find image files and update file sizes in database...<br /><br />";

$datadir = $Globals['datafull'];
$query = "SELECT id,catname FROM categories";
$resultb = ppmysql_query($query, $link);

while ( $row = mysql_fetch_row($resultb) ) {
    list( $thecatid, $thecatname ) = $row;

    if ( strstr($datadir, "/") )
        $newdir = $datadir."$thecatid/";
    else
        $newdir = $datadir."$thecatid\\";

    $query = "SELECT id,user,userid,cat,bigimage,filesize FROM photos where cat=$thecatid";
    $queryv = ppmysql_query($query,$link);

    while ( list( $id, $user, $tuserid, $cat, $bigimage, $filesize ) = mysql_fetch_row($queryv) ) {        
        $filelink = $newdir."$tuserid$bigimage";

        if ( $filesize > 8388606 ) {
            if ( file_exists( $filelink ) ) {
                $newsize = filesize( $filelink );
                $query = "UPDATE photos SET filesize=$newsize WHERE id=$id";
                $resulta = mysql_query($query,$link);
                
                print "$filelink: old size: $filesize  -  new size: $newsize<br />";
            }
        }
    }
}

print "<br>Finished!";

?>
