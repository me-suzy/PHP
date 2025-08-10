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

//$link = mysql_connect("$host", "$mysql_user", "$mysql_password") or die ('I cannot connect to the PhotoPost database.');
//mysql_select_db ("$database",$link)or die("Could not connect to PhotoPost database". mysql_error() );

// first thing, make all the directories we need
// then, for each category - get the photos and move them into new location

print "Starting to move image files... You can ignore any \"file exists errors\"...<br><br>";

$datadir = $Globals{'datafull'};

$query = "SELECT id,catname FROM categories";
$resultb = mysql_query_eval($query, $link);

while ( $row = mysql_fetch_row($resultb) ) {
    list( $thecatid, $thecatname ) = $row;

    if ( strstr($datadir, "/") )
        $newdir = $datadir."$thecatid/";
    else
        $newdir = $datadir."$thecatid\\";

    mkdir( $newdir, 0755 );
    chmod( $newdir, 0777 );

    $query = "SELECT id,user,userid,cat,bigimage,medsize FROM photos where cat=$thecatid";
    $queryv = mysql_query_eval($query,$link);

    while ( $row = mysql_fetch_row($queryv) ) {
        list( $id, $user, $tuserid, $cat, $bigimage, $medsize ) = $row;
        
        $theext = substr($bigimage, strlen($bigimage) - 4,4);
        $filename = $bigimage;
        $filename = str_replace( $theext, "", $filename);

        $biglink = $Globals{'datafull'}."$tuserid$filename$theext";
        $newbiglink = $newdir."$tuserid$filename$theext";

        $thumblink = $Globals{'datafull'}."$tuserid$filename-thumb$theext";
        $newthumblink = $newdir."$tuserid$filename-thumb$theext";

        $mediumlink = $Globals{'datafull'}."$tuserid$filename-med$theext";
        $newmediumlink = $newdir."$tuserid$filename-med$theext";

        //print "Moving file: [$biglink] to [$newbiglink]<br>";
        //print "Moving medium file: [$mediumlink] to [$newmediumlink]<br>";
        //print "Moving thumbnail: [$thumblink] to [$newthumblink]<br><br>";

        if ( file_exists( $biglink ) ) {
            if ( copy($biglink, $newbiglink) ) {
                unlink( $biglink );
            }
            else {
                print( "Copy of the file $biglink failed. Check your system when operation is complete.<br>" );
            }
        }
        else {
            if ( !file_exists( $newbiglink ) ) {
                print "Warning: The file $biglink does not exist; but has an entry in your database.<br>";
                // If you want to remove the links without filenames, uncomment the lines below
                //
                //print( "<b>The file $biglink does not exist. Entry removed from your database.</b><br>" );
                //$queryd = "DELETE FROM photos where id=$id";
                //$querydr = mysql_query_eval($queryd,$link);
            }
        }
            
        if ( $medsize > 0 ) {
            if ( file_exists( $mediumlink ) ) {
                if ( copy($mediumlink, $newmediumlink) ) {
                    unlink( $mediumlink );
                }
                else {
                    print( "Copy of the file $mediumlink failed. Check your system when operation is complete.<br>" );
                }
            }
            else {
                if ( !file_exists( $newmediumlink ) ) {
                    print "Warning: The file $mediumlink does not exist; but has an entry in your database.<br>";
                }   
            }
        }

        if ( file_exists( $thumblink ) ) {
            if ( copy($thumblink, $newthumblink) ) {
                unlink( $thumblink );
            }
            else {
                print( "Copy of the file $thumblink failed. Check your system when operation is complete.<br>" );               
            }            
        }
        else {
            $thethumbext = strtolower( $theext );
            $s_thumblink = $Globals{'datafull'}."$tuserid$filename-thumb$thethumbext";
            if ( file_exists( $s_thumblink ) ) {
                if ( copy($s_thumblink, $newthumblink) ) {
                    unlink( $s_thumblink );
                }
                else {
                    print( "Copy of the file $s_thumblink failed. Check your system when operation is complete.<br>" );
                }
            }
            else {
                $s_thumblink = $Globals{'datafull'}."$tuserid$filename-thumb.jpg";
                if ( file_exists( $s_thumblink ) ) {
                    if ( copy($s_thumblink, $newthumblink) ) {
                        unlink( $s_thumblink );
                    }
                    else {
                        print( "Copy of the file $s_thumblink failed. Check your system when operation is complete.<br>" );
                    }
                }
                else {
                    if ( !file_exists( $newthumblink ) ) {
                        print( "Warning: The thumbnail [$thumblink] does not exist.<br>" );
                    }
                }
            }
        }
    }
}

print "Finished!";

?>
