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

error_reporting (E_ALL ^ E_NOTICE);

// Get magic quote setting
$magic = get_magic_quotes_gpc();

// --------------------------------
// Register the necessary variables
if (is_array($HTTP_GET_VARS)) {
    while(list($key,$value) = each($HTTP_GET_VARS)) {
        if ($magic) {
            $value = stripslashes($value);
        }
        ${$key} = $value;
    }
}

if (is_array($HTTP_POST_VARS)) {
    while(list($key,$value) = each($HTTP_POST_VARS)) {
        if ($magic) {
            $value = stripslashes($value);
        }
        ${$key} = $value;
    }
}

if (is_array($HTTP_COOKIE_VARS)) {
    while(list($key,$value) = each($HTTP_COOKIE_VARS)) {
        if ($magic) {
            $value = stripslashes($value);
        }   
        ${$key} = $value;
    }
}
   
function catlist( $parid, $reiter=0 ) {
    global $Globals, $link, $cathash;

    $query = "SELECT id FROM categories WHERE parent=$parid ORDER BY catorder ASC";
    $boards = mysql_query($query,$link);

    if ( $reiter == 0 ) $cathash = "";

    while ( list ( $tid ) = mysql_fetch_row($boards) ) {
        if ( $cathash != "" ) $cathash .= ",";
        $cathash .= "$tid";
        catlist( $tid, 1 );
    }
    mysql_free_result( $boards );
    
    if ( $reiter == 0 ) {
        $catret = "cat IN ($cathash)";
        $query = "UPDATE categories SET children='$cathash' WHERE id=$parid";
        $resulta = mysql_query($query,$link);
    }

    return ( $catret );
}

function upgradecategories( $parent=0 ) {
    global $Globals, $link;
    
    // update categories
    //
    $query = "SELECT id,catname,description,catorder,parent,thumbs,children FROM categories ORDER BY catorder ASC";
    $boards = mysql_query($query, $link);
    //print "$query ... $boards<br />";    

    while ( list( $id, $catname, $catdesc, $order, $catparent, $catthumbs, $children ) = mysql_fetch_row($boards) ) {
        print "Processing $catname ... ";
        catlist( $id );
        
        $lastposttime=""; $lastpostby=""; $lastpostlink="";
        $lastphoid=""; $lastphoby=""; $lpholink="";
        $newphotos=""; $newcomments=""; $laston=0;
                        
        if ( $children != "" ) $children = "$id,$children";
        else $children = "$id";
                       
        $cphotos=0; $catposts=0;

        if ( $id != "500" ) {
            $quid = "cat IN ($children)";

            if ( $quid != "" && $quid != "cat=" ) {
                // first we need to get last photo/post info
                $query = "SELECT id,user,userid,date FROM photos WHERE $quid ORDER BY date DESC";
                print "$query<br />";
                $lastp = mysql_query($query, $link);
                list( $lastphoid, $lastphoby, $lastphobyid, $lastphotime ) = mysql_fetch_row($lastp);
                mysql_free_result($lastp);
                
                // now we get the number of photo, just in this category
                $query = "SELECT id FROM photos WHERE cat=$id";
                $lastp2 = mysql_query($query, $link);
                $cphotos = mysql_num_rows($lastp2);
                mysql_free_result($lastp2);
                
                $query = "SELECT username,id,date,photo FROM comments WHERE $quid ORDER BY date DESC";
                $lastc = mysql_query($query, $link);
                $catposts = mysql_num_rows($lastc);
                list( $lastpostby, $lastpostbyid, $lastposttime, $lastpostlink ) = mysql_fetch_row($lastc);
                mysql_free_result($lastc);
                
                $query = "SELECT id FROM comments WHERE cat=$id";
                $lastc2 = mysql_query($query, $link);
                $catposts = mysql_num_rows($lastc2);
                mysql_free_result($lastc2);
                
                // 3.1.2 - How to update children!
                $query = "UPDATE categories SET lastpost='$lastpostbyid', lastphoto='$lastphoid', posts='$catposts', photos='$cphotos' WHERE id=$id";
                $resulta = mysql_query($query,$link);
           }
        }
        else {
            if ( $Globals['memformat'] == "no" ) {
                $query1 = "SELECT id,user,userid,date FROM photos WHERE cat=500 ORDER BY date DESC";  
                $query2 = "SELECT username,id,date,photo FROM comments WHERE cat=500 ORDER BY date DESC";
            }
            else {
                $query1 = "SELECT id,user,userid,date FROM photos ORDER BY date DESC";  
                $query2 = "SELECT username,id,date,photo FROM comments ORDER BY date DESC";                    
            }
                                
            $lastp = mysql_query($query1,$link);
            $cphotos = mysql_num_rows($lastp);         
            list( $lastphoid, $lastphoby, $lastphobyid, $lastphotime ) = mysql_fetch_row($lastp);
            mysql_free_result($lastp);

            $lastc = mysql_query($query2,$link);
            $catposts = mysql_num_rows($lastc);
            list( $lastpostby, $lastpostbyid, $lastposttime, $lastpostlink ) = mysql_fetch_row($lastc);
            mysql_free_result($lastc);
            
            // 3.1.2 - How to update children!
            $query = "UPDATE categories SET lastpost='$lastpostbyid', lastphoto='$lastphoid', posts='$catposts', photos='$cphotos' WHERE id=500";
            $resulta = mysql_query($query,$link);                
        }
    }
    
    return;
} 


function upgradealbums( $parent=0 ) {
    global $Globals, $link;
    
    // update personal albums
    //
    $query = "SELECT id,albumname FROM useralbums";
    $boards = mysql_query($query, $link);

    while ( list( $id, $albumname ) = mysql_fetch_row($boards) ) {
        print "Processing Personal Album $albumname ...<br />";
        $cphotos=0; $catposts=0;

        // now we get the number of photo, just in this category
        $query = "SELECT id FROM photos WHERE cat=$id";
        $lastp2 = mysql_query($query, $link);
        $cphotos = mysql_num_rows($lastp2);
        mysql_free_result($lastp2);
        
        $query = "SELECT id FROM comments WHERE cat=$id";
        $lastc2 = mysql_query($query, $link);
        $catposts = mysql_num_rows($lastc2);
        mysql_free_result($lastc2);
        
        $query = "UPDATE useralbums SET posts='$catposts', photos='$cphotos' WHERE id=$id";
        $resulta = mysql_query($query,$link);
    }
    
    return;
} 


print "Preparing to update 3.1.1 database...<p>";

// Connecting, selecting database
$link = mysql_connect("$host", "$mysql_user", "$mysql_password") or die('I cannot connect to the PhotoPost database. [host:$host][mysql_user:$mysql_user][mysql_password:$mysql_password]');
mysql_select_db ("$database", $link)or die("Could not connect to PhotoPost database");

//
// check to see if this is the photo database update
//
if ( isset($photoset) ) {
    if ( $photoset == "yes" )
        $query = "ALTER TABLE photos ADD allowprint varchar(5) NOT NULL default 'yes'";
    else
        $query = "ALTER TABLE photos ADD allowprint varchar(5) NOT NULL default 'no'";
    
    $setphoto = mysql_query($query,$link);
    if ( !$setphoto ) {
        print "Photos database not updated properly. Please ensure that the database has:<br /><br />$query";
        exit;
    }
    
    print "Photos database upgraded. Please remove the upgrade and install scripts.";
    exit;
}


$query = "SELECT varname,setting FROM settings";
$getsets = mysql_query($query,$link);

if ( !$getsets ) {
    print "Database not properly setup. Contact administrator.";
    exit;
}

while ( list($var, $set) = mysql_fetch_row($getsets)) {
    $Globals[$var] = stripslashes($set);
}
mysql_free_result($getsets);

// Need to update this variable if no underscore at end
if ( !empty($Globals['dprefix']) ) {
    $oldprefix = $Globals['dprefix'];
    
    if ( !strstr($oldprefix, "_") ) {
        $newprefix = $oldprefix."_";

        $query = "UPDATE settings SET setting='$newprefix' WHERE id=83";
        $resulta = mysql_query($query, $link)or die( "Failed MySQL Query: [$link]:$query<br />Error: ".mysql_error() );
        
        print "Changed database prefix from '$oldprefix' to '$newprefix'<br /><b>Please make sure this setting is correct and manually fix if incorrect!</b><br /><br />";
    }
}

if ( file_exists( "upgrade311.sql")  ) {
    $filearray = file( "upgrade311.sql" );

    while ( list($num, $query) = each($filearray) ) {
        if ($query != "") {
            $query = str_replace( ";", "", $query);
            print "Performing MySQL command: $query ... ";
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
    print "upgrade311.sql is missing.";
    exit;
}

print "<p>Preparing to update categories with photo and posts information...<br />";
upgradecategories(0);

print "<p>Preparing to update personal albums with photo and posts information...<br />";
upgradealbums(0);

print "<br /><br />One final step. You need to decide if you photos will default to being available for printing or not.<br />";
print "<br /><a href=\"upgrade311.php?photoset=yes\">Click here to set your default to allow photos to be printed</a><br />";
print "<br /><a href=\"upgrade311.php?photoset=no\">Click here to set your default to NOT allow photos to be printed by default</a>";

?>
