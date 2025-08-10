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
include("adm-inc.php");

if ($ppaction == "options") {
    if ($do == "process") {

        foreach($HTTP_POST_VARS as $id=>$setting) {
            //$id=stripslashes($id);
            //$setting=stripslashes($setting);
            //$id=urlencode($id);
            //$setting=urlencode($setting);
            //print "[$id]=[$setting]<br>";

            if ($id == "41") {
                if ($setting != "2.0.3" && $setting != "2.2.0" && $setting != "Internal" && $setting !=
                "phpBB" && $setting != "w3t" && $setting != "w3t6" && $setting != "phpBB2") {
                    dieWell("Invalid user registration system type.  Must be 2.03, 2.2.0 (which is 2.2.1 compatible), Internal,
                    phpBB, phpBB2, w3t or w3t6");
                }
            }

            if ($id == "83") {
                $setting = str_replace( "_", "", $setting );
            }

            if ($id == "6") {
                $len = strlen($setting);
                if ( strstr( $setting, "/" ) ) {
                    if( $setting[$len-1] != "/" ) $setting = "$setting/";
                }
                else {
                    if( $setting[$len-1] != "\\" )    $setting = "$setting\\\\";
                }
            }

            if ( is_numeric( $id ) ) {
                $setting = addslashes( $setting );
                $query = "UPDATE settings SET setting='$setting' WHERE id=$id";
                $resulta = mysql_query_eval($query, $link);
                //print "$query<br>";
            }
        }

        forward( $Globals{'maindir'}."/adm-options.php?ppaction=options", "Processed changes!");
        exit;
    }

    $output = "$header<center><hr>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\"
        face=\"verdana\"><b>PhotoPost Options</font>
        </font></td>
        </tr>
        <tr id=\"cat\">
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$adminmenu</b></font></td></tr>
        <form method=\"post\" action=\"".$Globals{'maindir'}."/adm-options.php\">
        <tr><td bgcolor=\"".$Globals{'maincolor'}."\"><center><Br>
        ";

    $optprnt=1;
    $newsection=1;
    $ckcolor=0;

    $query = "SELECT settings.id,settings.title,settings.varname,settings.description,settings.setting,
        settings.optionorder,a.id,a.name from settings LEFT JOIN admingroups a ON settings.section=a.id
        ORDER BY settings.section";

    $sets = mysql_query_eval($query,$link);
    $fillcolor=0;

    while ( $row = mysql_fetch_row($sets) ) {
        list($id,$title,$varname,$optdesc,$setting,$optorder,$section,$secname) = $row;

        //dieWell( "[$id][$title][$varname][$setting]" );
        //exit;

        $setting=stripslashes($setting);
        $setting= str_replace( "\"", "&quot", $setting);

        $proceed = 1;
        if ($Globals{'vbversion'} != "Internal") {
            if ($varname == "cright" || $varname == "allowregs" || $varname == "coppa" || $varname ==
            "rules" || $varname == "emailverify" || $varname == "address" || $varname == "copparules"
            || $varname == "pversion") {
                $proceed=0;
                if (($Globals{'vbversion'} != "phpBB2") && $varname == "dprefix") {
                    $proceed=0;
                }
            }
        }
        else {
            if ($varname == "cright" || $varname == "pversion" || $varname == "dprefix") {
                $proceed=0;
            }
        }

        if ($proceed == 1) {
            if ( $fillcolor == 1) {
                $ckcolor=$Globals{'altcolor1'};
                $fillcolor=0;
            }
            else {
                $ckcolor=$Globals{'altcolor2'};
                $fillcolor=1;
            }

            if ($newsection == 1) {
                $newsection=0;
                $gnum=$section;
                $output .= "<table width=\"90%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"
                    bgcolor=\"".$Globals{'bordercolor'}."\"><tr><Td>
                    <table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\"><tr><td
                    bgcolor=\"".$Globals{'headcolor'}."\"
                    colspan=\"2\"><font color=\"".$Globals{'headfontcolor'}."\" face=\"".$Globals{'headfont'}."\"
                    size=\"2\"><b>$secname</td></tr>";
            }

            if ($gnum != $section && $newsection != 1) {
                $gnum=$section;
                $gname=$secname;
                $newsection=0;
                $output .= "</table></td></tr></table><p><table width=\"90%\" border=\"0\" cellpadding=\"0\"
                    cellspacing=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"><tr><Td width=\"100%\">
                    <table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"1\"><tr><td
                    bgcolor=\"".$Globals{'headcolor'}."\" colspan=\"2\">
                    <font color=\"".$Globals{'headfontcolor'}."\" face=\"".$Globals{'headfont'}."\" size=\"2\"><b>$secname</td></tr>";
            }

            $output .=" <Tr><Td
                width=\"65%\" bgcolor=\"$ckcolor\">
                <font size=\"2\" face=\"verdana\" color=\"".$Globals{'maintext'}."\">$title</font></td>";

            if ($setting == "yes") {
                $output .= "<Td width=\"35%\" bgcolor=\"$ckcolor\"><select name=\"$id\"><option
                    selected>$setting</option>
                    <option>no</option></select></td></tr>";
                $optprnt=0;
            }

            if ($setting == "no") {
                $output .= "<Td width=\"35%\" bgcolor=\"$ckcolor\"><select name=\"$id\">
                    <option selected>$setting</option><option>yes</option></select></td></tr>";
                $optprnt=0;
            }

            if ($id == "41") {
                $output .= "<Td width=\"35%\" bgcolor=\"$ckcolor\">
                    <select name=\"$id\">
                    <option selected>$setting</option>
                    </select></td></tr>";
                $optprnt=0;
            }

             if ($id == "87") {
                $output .= "<Td width=\"35%\" bgcolor=\"$ckcolor\"><select name=\"$id\">
                    <option selected>$setting</option>
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
                $output .= "<Td width=\"35%\" bgcolor=\"$ckcolor\"><select name=\"$id\">
                    <option selected>$setting</option>
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
                    $output .= "<Td width=\"35%\" bgcolor=\"$ckcolor\"><center>
                        <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><Tr><Td>
                        <input type=\"text\" size=\"15\" value=\"$setting\" name=\"$id\"></td><Td><center><table width=\"75\"
                        border=\"2\" height=\"17\" cellpadding=\"0\" cellspacing=\"0\"><tr><Td
                        bgcolor=\"$setting\"></td></tr></table></td></tr></table> </td></tr>";
                }
                else {
                    $output .= "<Td width=\"35%\" bgcolor=\"$ckcolor\"><input type=\"text\" size=\"30\"
                        value=\"$setting\" name=\"$id\"></td></tr>";
                }
            }
            $optprnt=1;
        }
    }

    $output .= "</table></td></tr></table><input type=\"hidden\" name=\"ppaction\" value=\"options\">
        <Br><input type=\"hidden\" name=\"do\" value=\"process\">
        <input type=\"submit\" value=\"Save Changes\"></form>
        </td></tr></table></td></tr></table>";

    print "$output<p>".$Globals{'cright'}."<p>$footer";

}

?>

