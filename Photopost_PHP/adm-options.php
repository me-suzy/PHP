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

if ($ppaction == "options") {
    if ($do == "process") {

        foreach($HTTP_POST_VARS as $id=>$setting) {
            //$id=stripslashes($id);
            $setting=stripslashes($setting);
            //$id=urlencode($id);
            //$setting=urlencode($setting);
            //print "[$id]=[$setting]<br />";
            $setting = str_replace( "\\", "/", $setting );

            if ($id == "41") {
                if ($setting != "2.0.3" && $setting != "2.2.0" && $setting != "Internal" && $setting !=
                "phpBB" && $setting != "w3t" && $setting != "w3t6" && $setting != "phpBB2") {
                    diewell("Invalid user registration system type.  Must be 2.03, 2.2.0 (which is 2.2.1 compatible), Internal,
                    phpBB, phpBB2, w3t or w3t6");
                }
            }

//            if ($id == "83") {
//                $len = strlen($setting)-1;
//                if( $setting[$len] == "_" ) $setting[$len] = "";
//            }

            if ($id == "6") {
                $len = strlen($setting)-1;
                if( $setting[$len] != "/" ) $setting = "$setting/";
            }

            if ( is_numeric( $id ) ) {
                $setting = addslashes( $setting );
                $query = "UPDATE settings SET setting='$setting' WHERE id=$id";
                $resulta = ppmysql_query($query, $link);
                //print "$query<br />";
            }
        }

        forward( "{$Globals['maindir']}/adm-options.php?ppaction=options", "Processed changes!");
        exit;
    }

    printheader( 0, "PhotoPost Options" );    

    $output = "<center><hr>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\"
        size=\"{$Globals['fontsmall']}\"><font size=\"{$Globals['fontmedium']}\"
        face=\"{$Globals['mainfonts']}\"><b>PhotoPost Options</font>
        </font></td>
        </tr>
        <tr>
        <td bgcolor=\"{$Globals['headcolor']}\"><b>
        <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\">$adminmenu</b></font></td></tr>
        <form method=\"post\" action=\"{$Globals['maindir']}/adm-options.php\">
        <tr><td bgcolor=\"{$Globals['maincolor']}\"><center><br />";

    $optprnt=1;
    $newsection=1;
    $ckcolor=0;

    $query = "SELECT settings.id,settings.title,settings.varname,settings.description,settings.setting,
        settings.optionorder,a.id,a.name from settings LEFT JOIN admingroups a ON settings.section=a.id
        ORDER BY settings.section";

    $sets = ppmysql_query($query,$link);
    $fillcolor=0;

    while ( list($id,$title,$varname,$optdesc,$setting,$optorder,$section,$secname) = mysql_fetch_row($sets) ) {
        $setting=stripslashes($setting);
        $setting= str_replace( "\"", "&quot", $setting);

        $proceed = 1;
        if ($Globals['vbversion'] != "Internal") {
            if ($varname == "cright" || $varname == "allowregs" || $varname == "coppa" || 
            $varname == "rules" || $varname == "emailverify" || $varname == "address" || 
            $varname == "copparules" || $varname == "pversion") {
                $proceed=0;
            }
        }
        else {
            if ($varname == "cright" || $varname == "pversion" || $varname == "dprefix") {
                $proceed=0;
            }
        }

        if ($proceed == 1) {
            if ( $fillcolor == 1) {
                $ckcolor=$Globals['altcolor1'];
                $fillcolor=0;
            }
            else {
                $ckcolor=$Globals['altcolor2'];
                $fillcolor=1;
            }

            if ($newsection == 1) {
                $newsection=0;
                $gnum=$section;
                $output .= "<table width=\"90%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"
                    bgcolor=\"{$Globals['bordercolor']}\"><tr><td>
                    <table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\"><tr>
                    <td bgcolor=\"{$Globals['headcolor']}\" colspan=\"2\" align=\"left\">
                    <font color=\"{$Globals['headfontcolor']}\" face=\"{$Globals['headfont']}\"
                    size=\"{$Globals['fontmedium']}\"><b>$secname</td></tr>";
            }

            if ($gnum != $section && $newsection != 1) {
                $gnum=$section;
                $gname=$secname;
                $newsection=0;
                $output .= "</table></td></tr></table><p><table width=\"90%\" border=\"0\" cellpadding=\"0\"
                    cellspacing=\"0\" bgcolor=\"{$Globals['bordercolor']}\"><tr><td width=\"100%\">
                    <table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\"><tr><td
                    bgcolor=\"{$Globals['headcolor']}\" colspan=\"2\" align=\"left\">
                    <font color=\"{$Globals['headfontcolor']}\" face=\"{$Globals['headfont']}\" size=\"{$Globals['fontmedium']}\"><b>$secname</td></tr>";
            }

            $output .=" <tr><td
                width=\"65%\" bgcolor=\"$ckcolor\" align=\"left\">
                <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$title</font></td>";

            if ($setting == "yes") {
                $output .= "<td width=\"35%\" bgcolor=\"$ckcolor\" align=\"left\"><select name=\"$id\"><option
                    selected=\"selected\">$setting</option>
                    <option>no</option></select></td></tr>";
                $optprnt=0;
            }

            if ($setting == "no") {
                $output .= "<td width=\"35%\" bgcolor=\"$ckcolor\" align=\"left\"><select name=\"$id\">
                    <option selected=\"selected\">$setting</option><option>yes</option></select></td></tr>";
                $optprnt=0;
            }

            if ($id == "41") {
                $output .= "<td width=\"35%\" bgcolor=\"$ckcolor\" align=\"left\">
                    <select name=\"$id\">
                    <option selected=\"selected\">$setting</option>
                    </select></td></tr>";
                $optprnt=0;
            }

             if ($id == "87") {
                $output .= "<td width=\"35%\" bgcolor=\"$ckcolor\" align=\"left\"><select name=\"$id\">
                    <option selected=\"selected\">$setting</option>
                    <option>NorthWest</option>
                    <option>North</option>
                    <option>NorthEast</option>
                    <option>West</option>
                    <option>Center</option>
                    <option>East</option>
                    <option>SouthWest</option>
                    <option>South</option>
                    <option>SouthEast</option>
                    </select></td></tr>";
                $optprnt=0;
            }

            if ($id == "88") {
                $output .= "<td width=\"35%\" bgcolor=\"$ckcolor\" align=\"left\"><select name=\"$id\">
                    <option selected=\"selected\">$setting</option>
                    <option>10</option>
                    <option>20</option>
                    <option>30</option>
                    <option>40</option>
                    <option>50</option>
                    <option>60</option>
                    <option>70</option>
                    <option>80</option>
                    <option>90</option>
                    <option>100</option>
                    </select></td></tr>";
                $optprnt=0;
            }

            if ($optprnt == 1) {
                if ( preg_match( "/\#/", $setting ) ) {
                    $output .= "<td width=\"35%\" bgcolor=\"$ckcolor\" align=\"left\"><center>
                        <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr><td align=\"left\">
                        <input type=\"text\" size=\"15\" value=\"$setting\" name=\"$id\" class=\"bginput\"></td><td><center><table width=\"75\"
                        border=\"2\" height=\"17\" cellpadding=\"0\" cellspacing=\"0\"><tr><td
                        bgcolor=\"$setting\" align=\"left\"></td></tr></table></td></tr></table></td></tr>";
                }
                else {
                    $output .= "<td width=\"35%\" bgcolor=\"$ckcolor\" align=\"left\"><input type=\"text\" size=\"30\"
                        value=\"$setting\" name=\"$id\" class=\"bginput\"></td></tr>";
                }
            }
            $optprnt=1;
        }
    }
    ppmysql_free_result( $sets );

    $output .= "</table></td></tr></table><input type=\"hidden\" name=\"ppaction\" value=\"options\">
        <br /><input type=\"hidden\" name=\"do\" value=\"process\">
        <input type=\"submit\" value=\"Save Changes\"></form>
        </td></tr></table></td></tr></table>";

    print "$output<p>{$Globals['cright']}<p>";
    printfooter();
}

?>
