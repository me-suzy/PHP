<?
//
// This file "upgrades" your existing v3.1.0 database
//

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

    require "config-inc.php";

    print "Preparing to update 3.1.0 database...<p>";

    // Connecting, selecting database
    $link = mysql_connect("$host", "$mysql_user", "$mysql_password") or die('I cannot connect to the PhotoPost database. [host:$host][mysql_user:$mysql_user][mysql_password:$mysql_password]');
    mysql_select_db ("$database", $link)or die("Could not connect to PhotoPost database");

    if ( file_exists( "upgrade310.sql")  ) {
        $filearray = file( "upgrade310.sql" );

        while ( list($num, $query) = each($filearray) ) {
            if ($query != "") {
                $query = str_replace( ";", "", $query);
                print " Performing MySQL command: $query ... ";
                $setup = mysql_query($query, $link);

                if ( !$setup ) {
                    print "<b>Error: ".mysql_error()."</b><br />";
                }
                else {
                    print "Successful!<br />";
                }
            }
        }
    }
    else {
        print "upgrade310.sql is missing.";
        exit;
    }

print "Finished upgrade!";

?>

