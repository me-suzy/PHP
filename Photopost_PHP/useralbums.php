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
require "languages/$pplang/useralbums.php";
require "adm-cinc.php";
require "login-inc.php";

authenticate();

if ( isset($Globals['ppboards']) && $adminedit != 1 ) {
    if ( $Globals['ppboards'] == "closed" ) {
        print $Globals['pp_lang']['closed'];
        exit;
    }
}

if ( $gologin != 1 ) {
    if ( $nopost == 1 ) {
        diewell($Globals['pp_lang']['noperm']);
        exit;
    }
    if ( $useruploads == 2 ) {
        diewell($Globals['pp_lang']['noverify']);
    }
}

if ( $isprivate == "yes" && $Globals['allowpa'] == "no" ) {
    diewell($Globals['pp_lang']['nopa']);
    exit;
}
    
topmenu();

if ( empty($ppaction) ) $ppaction="albums";
if ( empty($do) ) $do="";

if ( $ppaction == "albums" ) {
    //# Generate the edit useralbums HTML form

    printheader( 0, $Globals['pp_lang']['useralbums'] );
    
    $output = "<br /><table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td>
        <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>    
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\"
        face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['editor']}</font>
        </font></td>
        </tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><br /><ul>(<a
        href=\"{$Globals['maindir']}/useralbums.php?ppaction=addalbum\">{$Globals['pp_lang']['addalbum']}</a>)</ul>";

    albumli( $userid );

    $output.= "<p><center></td></tr></table></td></tr></table><p>{$Globals['cright']}";
    print $output;
    
    printfooter();
}

if ($ppaction == "addalbum") { //# Add a album
    if ( !empty($do) ) {
        if ( empty($isprivate) ) $isprivate="no";
        if ( empty($albumdesc) ) $albumdesc = "";
        if ( empty($albumname) ) $albumname = "New Album";
        
        $pa_password = "";        
        if ( $isprivate == "yes" ) $pa_password = gen_password();
        
        // Process a user's album submission
        $albumname = addslashes( $albumname );
        $albumdesc = addslashes( $albumdesc );

        $query = "INSERT INTO useralbums (id,albumname,parent,description,isprivate,password) values(NULL,'$albumname',$userid,'$albumdesc','$isprivate','$pa_password')";
        $setug = ppmysql_query($query,$link);
        $thealbumid = mysql_insert_id( $link );
        
        if ( $thealbumid == 0 ) {
            diewell( "{$Globals['pp_lang']['errordir']}" );
            exit;
        }
        elseif ( $thealbumid < 3000 ) {
            $query = "UPDATE useralbums SET id='3000' WHERE id=$thealbumid";
            $setug = ppmysql_query($query, $link);
            $thealbumid = 3000;
        }

        $newdir = "{$Globals['datafull']}$thealbumid";
        if ( !mkdir( $newdir, 0755 ) ) {
            diewell( "$newdir: {$Globals['pp_lang']['errordir']}" );
            exit;
        }
        chmod( $newdir, 0777 );

        forward( "{$Globals['maindir']}/useralbums.php?ppaction=albums", $Globals['pp_lang']['complete'] );
        exit;
    }

    //# Print out the Add a album HTML form
    printheader( 0, $Globals['pp_lang']['useralbums'] );
    
    $output = "<center><hr>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\"
        face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['addaalbum']}</font>
        </font></td>
        </tr>
        <tr>
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"#000000\">$menu</b></font></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\"><center><br />

        <table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" bgcolor=\"{$Globals['bordercolor']}\"><form method=\"post\"
        action=\"{$Globals['maindir']}/useralbums.php\">
        <tr><td bgcolor=\"{$Globals['headcolor']}\">
        <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\">
        <tr><Th bgcolor=\"#F7f7f7\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\">{$Globals['pp_lang']['albumname']}</font></th>
        <Th bgcolor=\"#F7f7f7\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\">{$Globals['pp_lang']['albumdesc']}</font></th>
        <Th bgcolor=\"#F7f7f7\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\">{$Globals['pp_lang']['isprivate']}?</font></th>        
        </tr><tr>
        <td bgcolor=\"#f7f7f7\">
        <input type=\"text\" size=\"50\" value=\"\" name=\"albumname\"></td>
        <td bgcolor=\"#f7f7f7\"><input type=\"text\" size=\"50\" value=\"\" name=\"albumdesc\"></td>
        <td bgcolor=\"#f7f7f7\"><select name=\"isprivate\">
        <option selected>no</option><option>yes</option></select></td>
        </tr></table></td></tr></table><p>
        <input type=\"hidden\" name=\"albumid\" value=\"$userid\">
        <input type=\"hidden\" name=\"ppaction\" value=\"addalbum\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <input type=\"submit\" value=\"{$Globals['pp_lang']['addalbum']}\"></form></td></tr></table></td></tr></table><p>{$Globals['cright']}";

    print $output;
    printfooter();    
}

if ($ppaction == "editalbum") {
    if ( $do == "process" ) {
        if ( empty($isprivate) ) $isprivate="no";
        if ( empty($albumdesc) ) $albumdesc = "";
        if ( empty($albumname) ) $albumname = "New Album";
        
        $pa_password = "";        
        if ( $isprivate == "yes" && $oldstat == "no" ) $pa_password = gen_password();        
        
        $albumname = addslashes( $albumname );
        $albumdesc = addslashes( $albumdesc );

        $query = "UPDATE useralbums SET albumname='$albumname', description='$albumdesc', isprivate='$isprivate', password='$pa_password' WHERE id='$albumid'";
        $setug = ppmysql_query($query,$link);

        forward( "{$Globals['maindir']}/useralbums.php?ppaction=albums", $Globals['pp_lang']['complete'] );
        exit;
    }

    $header = str_replace( "titlereplace", $Globals['pp_lang']['editalbum'], $header );    

    $output = "$header <center><hr>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\"
        face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['editor']}</font>
        </font></td>
        </tr>
        <tr>
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"#000000\">$menu</b></font></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\"><center><br />
        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"{$Globals['bordercolor']}\"><form method=\"post\"
        action=\"{$Globals['maindir']}/useralbums.php\"><tr><td>
        <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\"><tr>
        <Th bgcolor=\"#F7f7f7\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\">{$Globals['pp_lang']['albumname']}</font></th>
        <Th bgcolor=\"#F7f7f7\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\">{$Globals['pp_lang']['albumdesc']}</font></th>
        <Th bgcolor=\"#F7f7f7\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\">{$Globals['pp_lang']['isprivate']}?</font></th>        
        </tr>";


    $query = "SELECT id,albumname,description,isprivate,password FROM useralbums WHERE id=$albumid";
    $boards = ppmysql_query($query,$link);
    list($id,$albumname,$albumdesc,$isprivate,$password) = mysql_fetch_row($boards);
    ppmysql_free_result($boards);

    $albumname = str_replace( "\"", "&quot", $albumname);
    $albumdesc = str_replace( "\"", "&quot", $albumdesc);
    
    if ($isprivate == "yes") {
        $privatecode = "<td bgcolor=\"#f7f7f7\"><select name=\"isprivate\"><option
            selected>yes</option><option>no</option></select></td>";
    }
    else {
        $privatecode = "<td bgcolor=\"#f7f7f7\"><select name=\"isprivate\">
            <option selected>no</option><option>yes</option></select></td>";
    }

    $output .= "<tr><td bgcolor=\"#f7f7f7\"><input type=\"text\" size=\"50\"
        value=\"$albumname\" name=\"albumname\"></td><td bgcolor=\"#f7f7f7\"><input type=\"text\" size=\"50\" value=\"$albumdesc\" name=\"albumdesc\"></td>
        $privatecode</tr>";

    $output .= "</table></td></tr></table><p>
        <input type=\"hidden\" name=\"albumid\" value=\"$id\">
        <input type=\"hidden\" name=\"oldstat\" value=\"$isprivate\">
        <input type=\"hidden\" name=\"ppaction\" value=\"editalbum\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <input type=\"submit\" value=\"{$Globals['pp_lang']['savechanges']}\"></form></td></tr></table></td></tr></table><p>{$Globals['cright']}";

    print $output;
    printfooter();

    exit;
}

if ($ppaction == "delalbum") {  //# Delete a album
    if ($do == "process") { //# Process delete album
        if ( !is_numeric($albumid) ) {
            diewell( $Globals['pp_lang']['malformed'] );
            exit;
        }
        
        if ($albumid != 500 && $albumid != "0" && $albumid != "") {
            delete_cat($albumid);
        }
        else {
            diewell($Globals['pp_lang']['invalidid']);
            exit;
        }

        $forwardid = "{$Globals['maindir']}/useralbums.php?ppaction=albums";
        forward( $forwardid, $Globals['pp_lang']['complete'] );
        exit;
    }

    //# Generate an 'are you sure' you want to delete? form...

    $querya="SELECT albumname FROM useralbums where id=$albumid";
    $albumq = ppmysql_query($querya, $link);
    list( $albumname ) = mysql_fetch_row($albumq);
    ppmysql_free_result($albumq);

    $header = str_replace( "titlereplace", $Globals['pp_lang']['deleteuser'], $header );    

    $output="$header <center><hr>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\"
        face=\"{$Globals['mainfonts']}\"><b>{$Globals['pp_lang']['deleteuser']}</font>
        </font></td>
        </tr>
        <tr>
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"#000000\">$menu</b></font></td></tr>
        <tr><td bgcolor=\"#f7f7f7\"><center><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\"><br />
        {$Globals['pp_lang']['delete1']} \"$albumname\", {$Globals['pp_lang']['delete2']}<p>
        {$Globals['pp_lang']['delete3']}?
        <form action=\"{$Globals['maindir']}/useralbums.php\" method=\"post\">
        <input type=\"hidden\" name=\"albumid\" value=\"$albumid\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <input type=\"hidden\" name=\"ppaction\" value=\"delalbum\">
        <input type=\"submit\" value=\"{$Globals['pp_lang']['imsure']}\"></form></font></td></tr></table></td></tr></table>";

    print $output;
}

?>
