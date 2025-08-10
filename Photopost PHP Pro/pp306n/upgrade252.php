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
//
// This file "upgrades" your existing v2 database and then goes through all your images and
// averaging any rating and placing that rating in the new database field
//

    include("config-inc.php");

    print "Preparing to update 2.52 database...<p>";

    // Connecting, selecting database
    $link = mysql_connect("$host", "$mysql_user", "$mysql_password") or die('I cannot connect to the PhotoPost database. [host:$host][mysql_user:$mysql_user][mysql_password:$mysql_password]');
    mysql_select_db ("$database", $link)or die("Could not connect to PhotoPost database");

    if ( file_exists( "upgrade252.sql")  ) {
        $filearray = file( "upgrade252.sql" );

        while ( list($num, $query) = each($filearray) ) {
            if ($query != "") {
                $query = str_replace( ";", "", $query);
                $setup = mysql_query($query, $link);

                if ( !$setup ) {
                    print "Problem enountered with query: $query<br>Error: ".mysql_error();
                }
            }
        }
    }
    else {
        dieWell("upgrade252.sql is missing.");
        exit;
    }

    print "<p>Finished!<br><br>";

    //
    // Step 2 - here we run up the auto increment counter to 3000 so that any user groups
    // added will start at that high of a number (this way we know when we are dealing with user groups)
    //
    print "Adding/removing blank entries to useralbums...<br><br>";

    for( $x=0; $x < 3000; $x++ ) {
        $query = "INSERT INTO useralbums values(NULL,'NULL',0,'NULL')";
        $resultb = mysql_query($query, $link);

        if ( !$resultb ) {
            print "Error inserting blank line into UserAlbums.<br>Error: ".mysql_error();
            break;
        }

        $thealbumid = mysql_insert_id( $link );

        if ( $thealbumid > 3000 )
            break;

        $query = "DELETE FROM useralbums WHERE id=$thealbumid";
        $resultb = mysql_query($query, $link);
    }

    print "<p>Finished!<br><br>";

    //
    // Step 3 - here we query the photos and comments database to get the current "rating" of an image
    //
    print "Preparing to update database with new rating system...<br><br>";

    $query1 = "SELECT id FROM photos";
    $resulta = mysql_query($query1,$link);

    while( $photo = mysql_fetch_row($resulta) ) {
        $photoid = $photo[0];

        $query2 = "SELECT rating FROM comments WHERE photo=$photoid AND rating != '0'";
        $resultb = mysql_query($query2,$link);

        $numrating=0; $sumrating=0; $averagerate=0;
        while( $row = mysql_fetch_row($resultb) ) {
            $numrating++;
            list ( $checkrating ) = $row;
            $sumrating = ($sumrating+$checkrating);
        }
        if ( $numrating != 0 && $sumrating != 0 ) $averagerate = round( $sumrating / $numrating );

        $query3 = "UPDATE photos SET rating=$averagerate WHERE id=$photoid";
        $resultc = mysql_query($query3,$link);

        //print "Query: [$query3]<br>";
    }

print "Finished upgrade! If you havent already, be sure to execute <a href=\"upgradecats.php\">upgradecats.php</a> when you are finsihed!";

?>

