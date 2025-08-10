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

require "languages/$pplang/adm-cinc.php";

function catopt( $subcatid ) {
    global $Globals, $link, $paropts;

    $query = "SELECT id,catname,description,thumbs FROM categories WHERE parent=$subcatid ORDER BY catorder";
    $rows = ppmysql_query($query,$link);

    while ( list( $subid, $subcatname, $subcatdesc, $subthumbs ) = mysql_fetch_row($rows) ) {
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
    ppmysql_free_result($rows);
}

function catli( $subcatid ) {
    global $Globals, $link, $output;

    $catcnt=0;
    $query = "SELECT id,catname,description FROM categories WHERE parent=$subcatid ORDER BY catorder";
    $rows = ppmysql_query($query,$link);

    while ( list( $subid, $subcatname, $subcatdesc ) = mysql_fetch_row($rows) ) {
        $catcnt++;

        if ($subid != "500") {
            $delete = "<a href=\"{$Globals['maindir']}/adm-cats.php?ppaction=delcat&catid=$subid\">{$Globals['pp_lang']['delete']}</a>";
            $addcat = "<a href=\"{$Globals['maindir']}/adm-cats.php?ppaction=addcat&catid=$subid\">{$Globals['pp_lang']['addsubcat']}</a>";
        }
        else {
            $delete = "{$Globals['pp_lang']['delete']}";
            $addcat = "{$Globals['pp_lang']['addsubcat']}";
        }
        $edit = "<a href=\"{$Globals['maindir']}/adm-cats.php?ppaction=editcat&catid=$subid\">{$Globals['pp_lang']['edit']}</a>";
        $empty = "<a href=\"{$Globals['maindir']}/adm-cats.php?ppaction=emptycat&catid=$subid\">{$Globals['pp_lang']['empty']}</a>";        

        $subcatname = str_replace( "\"", "&quot", $subcatname);
        $subcatdesc = str_replace( "\"", "&quot", $subcatdesc);
        $output .= "<font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\"><ul><li>$subcatname ({$Globals['pp_lang']['order']}: <input type=\"text\" size=\"{$Globals['fontlarge']}\"
            value=\"$catcnt\" name=\"catorder-$subid\"
            style=\"font-size: 8pt; background: FFFFFF;\">) [$delete] [$addcat] [$edit] [$empty]";

        catli($subid);
    }
    ppmysql_free_result( $rows );
    
    $output .= "</ul>";

    return;
}

function childcheck( $parid, $nowpar ) {
    global $link;

    $query = "SELECT id,parent FROM categories WHERE id=$parid";
    $rows = ppmysql_query($query,$link);

    while ( list( $tid, $tparent ) = mysql_fetch_row($rows) ) {
        if ($tparent == $nowpar) {
            diewell("{$Globals['pp_lang']['parenter1']}");
            exit;
        }
        if ($tid == $nowpar) {
            diewell("{$Globals['pp_lang']['parenter2']}");
            exit;
        }
        childcheck( $tparent, $nowpar );
    }
    ppmysql_free_result( $rows );

    return;
}

function albumopt( $subalbumid ) {
    global $Globals, $link, $paropts;

    $query = "SELECT id,albumname FROM useralbums WHERE parent=$subalbumid";
    $rows = ppmysql_query($query,$link);

    while ( list( $subid, $subalbumname ) = mysql_fetch_row($rows) ) {
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
    ppmysql_free_result( $rows );
}

function albumli( $userid ) {
    global $Globals, $link, $output;

    $albumcnt=0;
    $query = "SELECT id,albumname,description,isprivate,password FROM useralbums WHERE parent=$userid";
    $rows = ppmysql_query($query,$link);

    if ( !$rows ) return;

    while ( list( $subid, $subalbumname, $subalbumdesc, $isprivate, $password ) = mysql_fetch_row($rows) ) {
        $albumcnt++;
        
        if ( $isprivate == "yes" ) {
            $private = " (private) ";
            $privatelink = "Private link to access album: <a href=\"{$Globals['maindir']}/showgallery.php?cat=$subid&amp;papass=$password&amp;thumb=1\">{$Globals['maindir']}/showgallery.php?cat=$subid&amp;papass=$password&amp;thumb=1</a>";
        }
        else {
            $private = "";
            $privatelink = "";
        }

        $delete = "<a href=\"{$Globals['maindir']}/useralbums.php?ppaction=delalbum&albumid=$subid\">{$Globals['pp_lang']['delete']}</a>";
        $edit = "<a href=\"{$Globals['maindir']}/useralbums.php?ppaction=editalbum&albumid=$subid\">{$Globals['pp_lang']['edit']}</a>";

        $subalbumname = str_replace( "\"", "&quot", $subalbumname);
        $subalbumdesc = str_replace( "\"", "&quot", $subalbumdesc);

        $output .= "<ul><li><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">$subalbumname $private [$delete] [$edit]<br />$subalbumdesc<br />$privatelink";

        albumli($subid);
    }
    ppmysql_free_result( $rows );
    
    $output .= "</ul>";

    return;
}


function updateparents( $parent ) {
    global $Globals, $link;
    
    do {
        catlist( $parent );
        
        $queryp = "SELECT parent FROM categories WHERE id=$parent";
        $catp = ppmysql_query($queryp, $link);
        list( $parent ) = mysql_fetch_row($catp);
        ppmysql_free_result( $catp );
    } while( $parent != 0 );
 
    return;   
}


function delete_cat( $catid ) {
    global $Globals, $link;

    // Delete category from categories table
    if ( !is_numeric($catid) ) {
        diewell( "Malformed call to delete_cat ($catid)" );
        exit;
    }

    if ( $catid < 3000 ) {
        // first, delete children
        $query = "SELECT id FROM categories WHERE parent=$catid";
        $cats = ppmysql_query($query,$link);

        while ( list( $subcatid ) = mysql_fetch_row($cats) ) {
            delete_cat( $subcatid );
        }
        ppmysql_free_result( $cats );

        // get the parent, so we can update the children after we remove it
        $query = "SELECT parent FROM categories WHERE id=$catid";
        $cats = ppmysql_query($query,$link);
        list( $subparent ) = mysql_fetch_row($cats);
        ppmysql_free_result( $cats );

        $query = "DELETE FROM categories WHERE id=$catid";
        $cats = ppmysql_query($query,$link);
        
        // now we update the children
        updateparents( $subparent );        
    }
    else {
        $query = "DELETE FROM useralbums WHERE id=$catid";
        $cats = ppmysql_query($query,$link);
    }

    $query = "SELECT userid,bigimage,medsize,title FROM photos WHERE cat=$catid";
    $resulta = ppmysql_query($query,$link);

    while ( list( $uid, $bigimage, $medsize, $title ) = mysql_fetch_row($resulta) ) {
        remove_all_files( $bigimage, $medsize, $uid, $catid );
    }
    ppmysql_free_result( $resulta );

    //# end delete the files //#
    $query = "DELETE FROM photos WHERE cat=$catid";
    $resulta = ppmysql_query($query, $link);

    $query = "DELETE FROM comments WHERE cat=$catid";
    $resulta = ppmysql_query($query, $link);

    $photodir = $Globals['datafull']."$catid";
    if ( file_exists( $photodir ) ) {
        delete_dir( $photodir );
    }
}

?>

