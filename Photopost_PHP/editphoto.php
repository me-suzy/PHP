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
require "languages/$pplang/editphoto.php";
require "login-inc.php";

$output = "";
if ( !isset($edit) ) $edit="no";
if ( !isset($delete) ) $delete="";

list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();

authenticate();

if ( isset($Globals['ppboards']) && $adminedit != 1 ) {
    if ( $Globals['ppboards'] == "closed" ) {
        print $Globals['pp_lang']['closed'];
        exit;
    }
}

// if we're editing a photo, check to see that the user is either an admin/mod or the post owner ###

if ( $gologin == 1 ) {
    $furl=$Globals['maindir'];
    $furl= str_replace( $Globals['domain'], "", $furl);
    $furl="$furl/editphoto.php?phoedit=$phoedit";
    login($furl);
    exit;
}

if ( ($nopost == 1 || $ueditpho == 0) && $adminedit != 1 ) {
    forward( "{$Globals['maindir']}/showphoto.php?photo=$phoedit", $Globals['pp_lang']['noperm'] );
    exit;
}

if ( $phoedit == "" ) {
    diewell( $Globals['pp_lang']['badcall'] );
    exit;
}

if ( $edit != "yes" ) {
    topmenu();

    $query = "SELECT cat,title,description,keywords,bigimage,approved,userid,allowprint FROM photos WHERE id=$phoedit LIMIT 1";
    $resulta = ppmysql_query($query,$link);
    list( $pcat, $ptitle, $pdesc, $pkeywords, $bigimage, $approved, $theuser, $allowprint ) = mysql_fetch_row($resulta);
    ppmysql_free_result( $resulta );

    $ptitle = str_replace( "\"", "&quot;", $ptitle);
    $pkeywords = str_replace( "\"", "&quot;", $pkeywords);

    printheader( $pcat, $Globals['pp_lang']['editphoto'] );

    $output = "<br /><table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td>
        <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\">
        <font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontlarge']}\">".$Globals['galleryname']." {$Globals['pp_lang']['editor']}</font>
        </font></td>
        </tr>
        <form method=\"post\" action=\"{$Globals['maindir']}/editphoto.php\">
        <tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['username']}</font></td>
        <td bgcolor=\"{$Globals['maincolor']}\">
        <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$username</font>
        </td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['thumbnail']}</font></td>
        <td bgcolor=\"{$Globals['maincolor']}\" align=\"center\">";
        
    $imgtag = get_imagethumb( $bigimage, $pcat, $theuser, $approved );
    
    $output .= "$imgtag<br />";
    $output .= "<font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$ptitle";
    
    if ( $Globals['usegd'] == 0 ) {
        $output .= "<br />
            <a href=\"{$Globals['maindir']}/adm-photo.php?ppaction=manipulate&pid=$phoedit&dowhat=rotateccw\">{$Globals['pp_lang']['rotateccw']}</a>&nbsp;
            <a href=\"{$Globals['maindir']}/adm-photo.php?ppaction=manipulate&pid=$phoedit&dowhat=rotatecw\">{$Globals['pp_lang']['rotatecw']}</a><br />       
            <a href=\"{$Globals['maindir']}/adm-photo.php?ppaction=manipulate&pid=$phoedit&dowhat=flip\">{$Globals['pp_lang']['flip']}</a>&nbsp;
            <a href=\"{$Globals['maindir']}/adm-photo.php?ppaction=manipulate&pid=$phoedit&dowhat=flop\">{$Globals['pp_lang']['flop']}</a><br />               
            </font></td>";
    }
        
    $output .= "</td></tr><tr><td bgcolor=\"{$Globals['maincolor']}\" width=\"50%\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">
        {$Globals['pp_lang']['catchange']}</font></td><td bgcolor=\"{$Globals['maincolor']}\"><select name=\"category\">";

    $selected = $pcat;
    catmoveopt(0);
    
    if ( $allowprint == "yes" ) {
        $allowselect = "<option selected=\"selected\">yes</option><option>no</option>";
    }
    else {
        $allowselect = "<option>yes</option><option selected=\"selected\">no</option>";        
    }
    
    $output .= "$catoptions</select></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['edittitle']}:</td><td bgcolor=\"{$Globals['maincolor']}\"><input
        type=\"text\" value=\"$ptitle\" name=\"title\" size=\"40\"></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">
        {$Globals['pp_lang']['editkey']}:</td><td bgcolor=\"{$Globals['maincolor']}\"><input type=\"text\" value=\"$pkeywords\" size=\"40\" name=\"keywords\"></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['editdesc']}:</td><td bgcolor=\"{$Globals['maincolor']}\"><textarea
        name=\"desc\" cols=\"30\" rows=\"5\">$pdesc</textarea></td></tr>";
        
    if ( $Globals['enablecal'] == "yes" ) {
        $output .= "<tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['allowprint']}</font></td>
        <td bgcolor=\"{$Globals['maincolor']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\"><select name=\"allowprint\">$allowselect</select></font></td></tr>";
    }
    
    $output .= "<center>
        <tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">
        {$Globals['pp_lang']['deletechk']}</font>
        </td><td
        bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['careful']}</font>
        <input type=\"checkbox\" name=\"delete\" value=\"delete\">
        </td></tr>

        <tr><td colspan=\"2\" bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><center>
        <input type=\"hidden\" name=\"password\" value=\"$password\">
        <input type=\"hidden\" name=\"userid\" value=\"$userid\">
        <input type=\"hidden\" name=\"origcat\" value=\"$pcat\">
        <input type=\"hidden\" name=\"phoedit\" value=\"$phoedit\">
        <input type=\"hidden\" name=\"edit\" value=\"yes\">
        <input type=\"submit\" value=\"{$Globals['pp_lang']['submitchg']}\"></td></tr></table></td></tr></table><p>{$Globals['cright']}";

    print $output;
    printfooter();    
}
else {
    // do the edit, make sure the user has permission to edit

    if ($delete == "delete") {
        $query = "SELECT userid,cat,bigimage,medsize,title FROM photos WHERE id=$phoedit";
        $resulta = ppmysql_query($query,$link);

        if ( !$resulta ) {
            diewell( "$photo: {$Globals['pp_lang']['nophoto']}" );
            exit;
        }

        list( $puserid, $thecat, $filename, $medsize, $ptitle ) = mysql_fetch_row($resulta);
        ppmysql_free_result( $resulta );

        if ( ($userid == $puserid && $Globals['userdel'] == "yes") || $adminedit == 1 ) {
            if ( !is_numeric($phoedit) ) {
                diewell( $Globals['pp_lang']['malformed'] );
                exit;
            }
            
            if ( $filename != "" ) remove_all_files( $filename, $medsize, $puserid, $thecat );

            $query = "DELETE FROM photos WHERE id=$phoedit";
            $resulta = ppmysql_query($query, $link);

            $query = "DELETE FROM comments WHERE photo=$phoedit";
            $resulta = ppmysql_query($query, $link);

            if ($Globals['ppostcount'] == "yes") {
                inc_user_posts( "minus", $puserid );
            }

            $adesc = $Globals['pp_lang']['deleted'];
            $furl = "{$Globals['maindir']}/index.php";

            if ( $Globals['useemail'] == "yes" && ($adminedit == 1 && $userid != $puserid) ) admin_email( 'delete', $phoedit, $puserid, $ptitle );

            forward($furl, $adesc);
            exit;
        }
        else {
            diewell( $Globals['pp_lang']['noaction'] );
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

        $query = "UPDATE photos SET title='$title',keywords='$keywords',description='$desc',allowprint='$allowprint' WHERE id=$phoedit";
        $resulta = ppmysql_query($query,$link);

        if ( $origcat != $category && !empty($category) ) {
            move_image_cat( $phoedit, $category );
        }

        forward( "{$Globals['maindir']}/showphoto.php?photo=$phoedit", $Globals['pp_lang']['success'] );
        exit;
    }
} // end do edit

?>
