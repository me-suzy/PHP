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

require "adm-inc.php";
require "adm-cinc.php";

if ( empty($do) ) $do="";
if ( empty($catid) ) $catid="";

if ($ppaction == "albums") {
    //# Generate the edit categories HTML form
    printheader( 0, "PhotoPost Personal Album Admin" );
    
    $output = "<center>

        <p><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><b>PhotoPost Personal Album Editor</font>
        </font></td>
        </tr>
        <tr>
        <td bgcolor=\"{$Globals['headcolor']}\" align=\"left\"><b>
        <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\">$adminmenu</b></font></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">";

    $whosup = -1;
    $query = "SELECT id,albumname,parent FROM useralbums ORDER BY parent";
    $albums = ppmysql_query($query, $link);
    
    while ( list( $id, $albumname, $parent ) = mysql_fetch_row($albums) ) {
        if ( $parent != $whosup ) {
            list( $username, $email ) = get_username( $parent );
            $output .= "<br /><a href=\"mailto:$email\">$username</a> Personal Albums<br />";
                        
            albumli($parent);
            $output .= "<p>";
            $whosup = $parent;
        }
    }
    
    if ( $albums )
        ppmysql_free_result( $albums );

    $output.= "<p><center></td></tr></table></td></tr></table>";

    print "$output<p>{$Globals['cright']}<p>";
    printfooter();    
}

?>
