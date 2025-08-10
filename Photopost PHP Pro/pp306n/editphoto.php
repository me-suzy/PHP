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

$output = "";
if ( !isset($edit) ) $edit="no";
if ( !isset($delete) ) $delete="";

list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
authenticate();

// if we're editing a photo, check to see that the user is either an admin/mod or the post owner ###

if ( $gologin == 1 ) {
    $furl=$Globals{'maindir'};
    $furl= str_replace( $Globals{'domain'}, "", $furl);
    $furl="$furl/editphoto.php?phoedit=$phoedit";
    login($furl);
    exit;
}

if ( $nopost == 1 ) {
    forward( $Globals{'maindir'}."/showphoto.php?photo=$phoedit", "Sorry, you don't have permission to post, or if you tried to edit, you might not be the post's author." );
    exit;
}

if ( $phoedit == "" ) {
    dieWell( "Script not called correctly.  Navigate to a specific photo, then click on the edit link." );
    exit;
}

if ( $edit != "yes" ) {
    topmenu();

    $query = "SELECT cat,title,description,keywords FROM photos WHERE id=$phoedit LIMIT 1";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_row($resulta);
    list( $pcat, $ptitle, $pdesc, $pkeywords ) = $row;

    $ptitle = str_replace( "\"", "&quot;", $ptitle);
    $pkeywords = str_replace( "\"", "&quot;", $pkeywords);

    $output = "<title>".$Globals{'galleryname'}." Image Editor</title>$header
        <p><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"".$Globals{'tablewidth'}."\"><Tr>
        <Td valign=\"bottom\" width=\"50%\">&nbsp;</td>
        <td width=\"50%\" align=\"right\" valign=\"center\"><font face=\"verdana, arial\" size=\"2\">$menu</font></td></tr></table>
        <p><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\" size=\"4\">".$Globals{'galleryname'}." Image
        Editor</font>
        </font></td>
        </tr>
        <form method=\"post\" action=\"".$Globals{'maindir'}."/editphoto.php\">
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Username</font></td>
        <td bgcolor=\"".$Globals{'maincolor'}."\">
        <font size=\"2\" face=\"verdana,arial\" color=\"".$Globals{'maintext'}."\">$username</font></td></tr>
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\" width=\"50%\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Change
        category? (leave blank to leave alone)</font></td><Td bgcolor=\"".$Globals{'maincolor'}."\"><select
        name=\"category\"><option selected></option>";

    catmoveopt(0);
    $output .= "$catoptions</select></td></tr>
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Edit title for the photo</td><td bgcolor=\"".$Globals{'maincolor'}."\"><input
        type=\"text\" value=\"$ptitle\" name=\"title\" size=\"40\"></td></tr>
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">
        Edit keywords:</td><td bgcolor=\"".$Globals{'maincolor'}."\"><input type=\"text\" value=\"$pkeywords\" size=\"40\" name=\"keywords\"></td></tr>
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Edit Photo Description</td><td bgcolor=\"".$Globals{'maincolor'}."\"><textarea
        name=\"desc\" cols=\"30\" rows=\"5\">$pdesc</textarea></td></tr>
        <Center>
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">
        If you want to <b>delete</b> this image completely, check this box</font><br>
        <font size=\"1\" face=\"verdana\" color=\"".$Globals{'maintext'}."\">(CAREFUL - once it's gone it's gone):</td><td
        bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'maintext'}."\">(checking this checkbox will <b>delete</b> the image)</font>
        <input type=\"checkbox\" name=\"delete\" value=\"delete\">
        </td></tr>

        <Tr><Td colspan=\"2\" bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana,arial\"><center>
        <input type=\"hidden\" name=\"password\" value=\"$password\">
        <input type=\"hidden\" name=\"userid\" value=\"$userid\">
        <input type=\"hidden\" name=\"origcat\" value=\"$pcat\">
        <input type=\"hidden\" name=\"phoedit\" value=\"$phoedit\">
        <input type=\"hidden\" name=\"edit\" value=\"yes\">
        <input type=\"submit\" value=\"Upload/Submit\"></td></tr></table></td></tr></table><p>".$Globals{'cright'}."$footer";

    print $output;
}
else {
    // do the edit, make sure the user has permission to edit

    if ($delete == "delete") {
        $query = "SELECT userid,cat,bigimage,medsize,title FROM photos WHERE id=$phoedit";
        $resulta = mysql_query_eval($query,$link);
        $row = mysql_fetch_row($resulta);

        if ( !$row ) {
            dieWell( "Photo $photo not found in your database!" );
            exit;
        }

        list( $puserid, $thecat, $filename, $medsize, $ptitle ) = $row;

        if ( ($userid == $puserid && $Globals{'userdel'} == "yes") || $adminedit == 1 ) {
            if ( $filename != "" ) remove_all_files( $filename, $medsize, $puserid, $thecat );

            $query = "DELETE FROM photos WHERE id=$pid";
            $resulta = mysql_query_eval($query, $link);

            $query = "DELETE FROM comments WHERE photo=$pid";
            $resulta = mysql_query_eval($query, $link);

            if ($Globals{'ppostcount'} == "yes") {
                inc_user_posts( "minus", $puserid );
            }

            $adesc = "You have successfully deleted your image.";
            $furl = $Globals{'maindir'}."/index.php";

            if ( $Globals{'useemail'} == "yes" && ($adminedit == 1 && $userid != $puserid) ) admin_email( 'delete', $pid, $puserid, $ptitle );

            forward($furl, $adesc);
            exit;
        }
        else {
            dieWell( "You do not have permission for this action!");
            exit;
        }
    }
    else {
        if ( empty($rating) ) $rating=0;

        $title = fixmessage( $title );
        $keywords = fixmessage( $keywords );
        $desc = fixmessage( $desc );

        $title = addslashes( $title );
        $keywords = addslashes( $keywords );
        $desc = addslashes( $desc );

        $query = "UPDATE photos SET title='$title',keywords='$keywords',description='$desc' WHERE id=$phoedit";
        $resulta = mysql_query_eval($query,$link);

        if ( $origcat != $category && !empty($category) ) {
            move_image_cat( $phoedit, $category );
        }

        forward( $Globals{'maindir'}."/showphoto.php?photo=$phoedit", "You successfully edited your photo." );
        exit;
    }
} // end do edit

?>
