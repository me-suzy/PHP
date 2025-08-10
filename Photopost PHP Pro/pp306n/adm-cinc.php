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
function catopt( $subcatid ) {
    global $Globals, $link, $paropts;

    $query = "SELECT id,catname,description,thumbs FROM categories WHERE parent=$subcatid ORDER BY catorder";
    $rows = mysql_query_eval($query,$link);

    while ( $row = mysql_fetch_row($rows) ) {
        list( $subid, $subcatname, $subcatdesc, $subthumbs ) = $row;

        if ($subid != "500") {
            if ($subthumbs == "no") {
                $paropts .= "<option value=\"$subid\">- -$subcatname</option>";
            }
            else {
                $paropts .= "<option value=\"$subid\">$subcatname</option>";
            }
        }
        catopt( $subid );
    }
}

function catli( $subcatid ) {
    global $Globals, $link, $output;

    $catcnt=0;
    $query = "SELECT id,catname,description FROM categories WHERE parent=$subcatid ORDER BY catorder";
    $rows = mysql_query_eval($query,$link);

    while ( $result = mysql_fetch_row($rows) ) {
        list( $subid, $subcatname, $subcatdesc ) = $result;
        $catcnt++;

        if ($subid != "500") {
            $delete = "<a href=\"".$Globals{'maindir'}."/adm-cats.php?ppaction=delcat&catid=$subid\">Delete</a>";
            $addcat = "<a href=\"".$Globals{'maindir'}."/adm-cats.php?ppaction=addcat&catid=$subid\">Add Subcat</a>";
        }
        else {
            $delete = "Delete";
            $addcat = "Add Subcat";
        }
        $edit = "<a href=\"".$Globals{'maindir'}."/adm-cats.php?ppaction=editcat&catid=$subid\">Edit</a>";

        $subcatname = str_replace( "\"", "&quot", $subcatname);
        $subcatdesc = str_replace( "\"", "&quot", $subcatdesc);
        $output .= "<Font face=\"verdana\" size=\"2\" color=\"".$Globals{'maintext'}."\"><ul><li>$subcatname (Order: <input type=\"text\" size=\"4\"
            value=\"$catcnt\" name=\"catorder-$subid\"
            style=\"font-size: 8pt; background: FFFFFF;\">) [$delete] [$addcat] [$edit]";

        catli($subid);
    }
    $output .= "</ul>";

    return;
}

function childcheck( $parid, $nowpar ) {
    global $link;

    $query = "SELECT id,parent FROM categories WHERE id=$parid";
    $rows = mysql_query_eval($query,$link);

    while ( $result = mysql_fetch_row($rows) ) {
        list( $tid, $tparent ) = $result;

        if ($tparent == $nowpar) {
            dieWell("You tried to parent a forum to one of its children.");
            exit;
        }
        if ($tid == $nowpar) {
            dieWell("You tried to parent a forum to itself.");
            exit;
        }
        childcheck( $tparent, $nowpar );
    }

    return;
}

function albumopt( $subalbumid ) {
    global $Globals, $link, $paropts;

    $query = "SELECT id,albumname FROM useralbums WHERE parent=$subalbumid";
    $rows = mysql_query_eval($query,$link);

    while ( $row = mysql_fetch_row($rows) ) {
        list( $subid, $subalbumname ) = $row;

        if ($subid != "500") {
            if ($subthumbs == "no") {
                $paropts .= "<option value=\"$subid\">- -$subalbumname</option>";
            }
            else {
                $paropts .= "<option value=\"$subid\">$subalbumname</option>";
            }
        }
        albumopt( $subid );
    }
}

function albumli( $userid ) {
    global $Globals, $link, $output;

    $albumcnt=0;
    $query = "SELECT id,albumname,description FROM useralbums WHERE parent=$userid";
    $rows = mysql_query_eval($query,$link);

    if ( !$rows ) return;

    while ( $result = mysql_fetch_row($rows) ) {
        list( $subid, $subalbumname, $subalbumdesc ) = $result;
        $albumcnt++;

        $delete = "<a href=\"".$Globals{'maindir'}."/useralbums.php?ppaction=delalbum&albumid=$subid\">Delete</a>";
        $edit = "<a href=\"".$Globals{'maindir'}."/useralbums.php?ppaction=editalbum&albumid=$subid\">Edit</a>";

        $subalbumname = str_replace( "\"", "&quot", $subalbumname);
        $subalbumdesc = str_replace( "\"", "&quot", $subalbumdesc);

        $output .= "<ul><li><Font face=\"verdana\" size=\"2\" color=\"".$Globals{'maintext'}."\">$subalbumname [$delete] [$edit]<br>$subalbumdesc";

        albumli($subid);
    }
    $output .= "</ul>";

    return;
}

function delete_cat( $catid ) {
    global $Globals, $link;

    // Delete category from categories table

    if ( $catid < 3000 ) {
        $query = "SELECT id FROM categories WHERE parent=$catid";
        $cats = mysql_query_eval($query,$link);

        while ( $row = mysql_fetch_row($cats) ) {
            $subcatid = $row[0];
            delete_cat( $subcatid );
        }

        $query = "DELETE FROM categories WHERE id=$catid";
        $cats = mysql_query_eval($query,$link);
    }
    else {
        $query = "SELECT id FROM useralbums WHERE parent=$catid";
        $cats = mysql_query_eval($query,$link);

        while ( $row = mysql_fetch_row($cats) ) {
            $subcatid = $row[0];
            delete_cat( $subcatid );
        }

        $query = "DELETE FROM useralbums WHERE id=$catid";
        $cats = mysql_query_eval($query,$link);
    }

    $query = "SELECT userid,bigimage,medsize,title FROM photos WHERE cat=$catid";
    $resulta = mysql_query_eval($query,$link);

    while ( $row = mysql_fetch_row($resulta) ) {
        list( $uid, $bigimage, $medsize, $title ) = $row;
        remove_all_files( $bigimage, $medsize, $uid, $catid );
    }

    //# end delete the files //#
    $query = "DELETE FROM photos WHERE cat=$catid";
    $resulta = mysql_query_eval($query, $link);

    $query = "DELETE FROM comments WHERE cat=$catid";
    $resulta = mysql_query_eval($query, $link);

    $photodir = $Globals{'datafull'}."$catid";
    if ( file_exists( $photodir ) ) {
        rmdir( $photodir );
    }
}

?>

