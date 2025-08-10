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
require "languages/$pplang/uploadphoto.php";
require "login-inc.php";
require "image-inc.php";

if ( is_array($HTTP_POST_FILES) ) {
    while(list($key,$value) = each($HTTP_POST_FILES)) {
        ${$key} = $value;
    }
}

function handleupload( $location = "data" ) {
    global $Globals, $HTTP_POST_FILES, $userid, $category, $wasuploaded;

    $tmpname = $HTTP_POST_FILES['theimage']['tmp_name'];
    $realname = $HTTP_POST_FILES['theimage']['name'];

    if (is_uploaded_file($tmpname) ) {
        $realname = fixfilenames( $realname );

        if ( $location != "data" ) {
            $dst_file = $location;
        }
        else {
            $dst_file = "{$Globals['datafull']}$category/$userid$realname";
        }

        move_uploaded_file($tmpname, $dst_file);
    }
    else {
        diewell( "$realname: {$Globals['pp_lang']['nofile']}" );
        exit;
    }

    $wasuploaded = "yes";    
    return;
}

authenticate();

if ( isset($Globals['ppboards']) && $adminedit != 1 ) {
    if ( $Globals['ppboards'] == "closed" ) {
        print $Globals['pp_lang']['closed'];
        exit;
    }
}

$nolimit = 0;
if ( $Globals['adminnolimit'] == "yes" && $adminedit == 1 ) {
    $nolimit = 1;
}

$adminexclude = 0;
if ( $Globals['adminexclude'] == "yes" && $adminedit == 1 ) {
    $adminexclude = 1;
}

if ( $adminedit == 0 ) {
    if ( $Globals['allowup'] == "no" ) {
        diewell( $Globals['pp_lang']['upnot'] );
    }
}

$querystring = findenv("QUERY_STRING");
if ( ($useruploads == 0 && $gologin==1) || $querystring == "gologin" ) {
    $furl = $Globals['maindir'];
    $furl = str_replace( $Globals['domain'], "", $furl );
    $furl = "$furl/uploadphoto.php";

    login( $furl );
    exit;
}

if ( $gologin != 0 ) {
    if ( $useruploads == 0 ) {
        diewell($Globals['pp_lang']['noperm']);
        exit;
    }

    if ( $useruploads == 2 ) {
        diewell($Globals['pp_lang']['noverify']);
    }
}

topmenu();

if ( !isset($theimage) ) {
    $catdefault = "";

    if ( !empty($cat) ) {
        $query = "SELECT id,catname,thumbs FROM categories WHERE id=$cat LIMIT 1";
        $resultb = ppmysql_query($query,$link);
        while ( list( $subid, $subcatname, $subthumbs ) = mysql_fetch_row($resultb) ) {
            if ( $subid < 3000 ) {
                if ( $ugcat{$subid} != 1 ) {
                    $catdefault = "<option selected=\"selected\" value=\"$subid\">$subcatname</option>";
                }
            }
        }
        ppmysql_free_result( $resultb );
    }
    else {
        if ( $ugcat{500} != 1 ) {
            $query = "SELECT id,catname,thumbs FROM categories WHERE id=500 LIMIT 1";
            $resultb = ppmysql_query($query,$link);
            list( $subid, $subcatname, $subthumbs ) = mysql_fetch_row($resultb);
            ppmysql_free_result( $resultb );

            $catdefault = "<option selected=\"selected\" value=\"$subid\">$subcatname</option>";
        }
    }

    printheader( $cat, $Globals['pp_lang']['uploadphoto'] );

    $output = "<form method=\"post\" action=\"{$Globals['maindir']}/uploadphoto.php\" enctype=\"multipart/form-data\">
        <table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td>
        <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\"><td colspan=\"1\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\">
        <font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontmedium']}\"><b>{$Globals['galleryname']} {$Globals['pp_lang']['imageupload']}</b></font></td>
        <td colspan=\"1\" align=\"right\" bgcolor=\"{$Globals['headcolor']}\">
        <font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontmedium']}\">
        <a href=\"javascript:PopUpHelp('uploadphoto.php')\">{$Globals['pp_lang']['help']}</a></font>
        </td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\">
        <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['username']}</font>
        </td><td bgcolor=\"{$Globals['maincolor']}\">
        <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">$username </font>
        </td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\" width=\"50%\">
        <font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['choosecat']}</font>
        </td><td bgcolor=\"{$Globals['maincolor']}\">
        <select name=\"category\">$catdefault";

    if (empty($subid)) $subid="";
    $selected = $subid;
    catmoveopt(0);
    $output .= $catoptions;

    $query = "SELECT SUM(filesize) AS fsize FROM photos WHERE userid=$userid";
    $resulta = ppmysql_query($query,$link);
    list( $diskuse ) = mysql_fetch_row($resulta);
    ppmysql_free_result( $resulta );

    $disk_b = $disk_k * 1024;

    if ( $nolimit == 0 ) {
        $diskbytes = $disk_b-$diskuse;
        $diskspace = $diskbytes;
        $diskspace = $diskbytes/1024;
        $diskspace = sprintf("%1.1f", $diskspace);
        $diskbytes = number_format( $diskbytes );
        $diskspace = $diskspace."kb ($diskbytes {$Globals['pp_lang']['bytes']})";
        $disk_k = number_format($disk_k);
        $disk_b = number_format($disk_b);
        $disk_k .= "kb ($disk_b {$Globals['pp_lang']['bytes']})";        
    }
    else {
        $diskspace = $Globals['pp_lang']['unlimit'];
        $disk_k = $Globals['pp_lang']['unlimit'];
    }

    $diskusekb = $diskuse/1024;
    $diskusekb = sprintf("%1.1f", $diskusekb );
    $diskusekb = number_format( $diskusekb );
    $diskuse = number_format( $diskuse );
    $diskuse = $diskusekb."kb ($diskuse {$Globals['pp_lang']['bytes']})";

    if ( $Globals['usenotify'] == "yes" && $userid != 0 ) {
        $notifyhtml = "<tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['notify']}</font></td>
            <td bgcolor=\"{$Globals['maincolor']}\"><select name=\"notify\"><option selected=\"selected\">no</option><option>yes</option></select></td></tr>";
    }
    else
        $notifyhtml="";

    if ( $adminedit == 1 ) {
        $imgdir = "{$Globals['zipuploaddir']}/$userid";
        
        //if you have a low number of users you can use this drop down list box, otherwise you have to input the name manually
        //$useroptions = "<select name=\"upuser\"><option selected=\"userid\" value=\"$userid\">$username</option>";
        //$useroptions .= useropts();
        //$useroptions .= "</select>";
        $useroptions = "<input type=\"text\" name=\"upuser\" value=\"$username\" />";
        
        $skiphtml = "</table><table cellpadding=\"4\" cellspacing=\"0\" border=\"0\" width=\"100%\">
            <tr><td bgcolor=\"{$Globals['headcolor']}\" align=\"center\">
            <font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\" face=\"{$Globals['mainfonts']}\">
            <b>{$Globals['pp_lang']['adminopt']}</b></font>
            </td>
            <td align=\"right\" bgcolor=\"{$Globals['headcolor']}\">
            <font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontmedium']}\">
            <a href=\"javascript:PopUpHelp('adminskip.php')\">{$Globals['pp_lang']['help']}</a></font>
            </td></tr></table>
            <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
            <tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['skipupl']}<br /><font size=\"{$Globals['fontsmall']}\">{$Globals['pp_lang']['filenotice']}: <b>$imgdir</b></font></font></td>
            <td bgcolor=\"{$Globals['maincolor']}\" align=\"center\"><input type=\"checkbox\" name=\"skipupload\" value=\"skipupload\" /></td></tr>
            <tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['processall']}<br /><font size=\"{$Globals['fontsmall']}\">{$Globals['pp_lang']['filenotice']}: <b>$imgdir</b></font></font></td>
            <td bgcolor=\"{$Globals['maincolor']}\" align=\"center\"><input type=\"checkbox\" name=\"processall\" value=\"processall\" /></td></tr>            
            <tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['showthumbs']}</font></td>
            <td bgcolor=\"{$Globals['maincolor']}\" align=\"center\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\"><select name=\"dthumbs\"><option selected=\"selected\">yes</option><option>no</option></select></font></td></tr>
            <tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['numthumbs']}</font></td>
            <td bgcolor=\"{$Globals['maincolor']}\" align=\"center\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\"><select name=\"numprocess\"><option selected=\"selected\">10</option><option>25</option><option>50</option><option>100</option></select></font></td></tr>
            <tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['upuser']}</font></td>
            <td bgcolor=\"{$Globals['maincolor']}\" align=\"center\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">$useroptions</font></td></tr>";            
    }
    else
        $skiphtml="";

    if ( $nolimit == 0 || $userid == 0 ) {
        $maxfilesize = $uploadsize."k {$Globals['pp_lang']['filelimit']}";
    }
    else {
        $maxfilesize = $Globals['pp_lang']['nolimit'];
    }

    if ( $Globals['allowzip'] == "yes" ) {
        $maxfilesize .= " {$Globals['pp_lang']['zips']}";
    }

    $output .= "</select></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\">
        <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['photoname']}:</font><br /><b>
        <font size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\" color=\"red\">$maxfilesize</font></b>
        </td><td bgcolor=\"{$Globals['maincolor']}\"><input type=\"file\" name=\"theimage\" /></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\">
        <font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['diskallowed']}:</font>
        </td><td bgcolor=\"{$Globals['maincolor']}\">
        <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">$disk_k </font></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\">
        <font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['diskused']}:</font>
        </td><td bgcolor=\"{$Globals['maincolor']}\">
        <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">$diskuse</font></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\">
        <font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['diskremain']}:</font>
        </td><td bgcolor=\"{$Globals['maincolor']}\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">$diskspace</font></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\">
        <font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['phototitle']}:</font>
        </td><td bgcolor=\"{$Globals['maincolor']}\"><input type=\"text\" name=\"title\" /></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\">
        <font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['pohotohelp']}:</font>
        </td><td bgcolor=\"{$Globals['maincolor']}\"><input type=\"text\" name=\"keywords\" /></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\">
        <font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['photodesc']}</font>
        </td><td bgcolor=\"{$Globals['maincolor']}\"><textarea name=\"desc\" cols=\"30\" rows=\"5\"></textarea></td></tr>";
        
    if ( $Globals['enablecal'] == "yes" ) {
        $output .= "<tr><td bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['allowprint']}</font></td>
            <td bgcolor=\"{$Globals['maincolor']}\" align=\"left\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\"><select name=\"allowprint\"><option selected=\"selected\">yes</option><option>no</option></select></font></td></tr>";
    }
        
    $output .= "$notifyhtml
        $skiphtml
        <tr><td colspan=\"2\" bgcolor=\"{$Globals['maincolor']}\" align=\"center\"><center><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\">
        <input type=\"hidden\" name=\"password\" value=\"$password\" />
        <input type=\"hidden\" name=\"userid\" value=\"$userid\" />
        <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"2000000\" />
        <input type=\"submit\" value=\"{$Globals['pp_lang']['uploadsubmit']}\" />
        <br /><br /><b>{$Globals['pp_lang']['subdesc']}</b><br /></font><font size=\"{$Globals['fontsmall']}\" face=\"{$Globals['mainfonts']}\"><b>
        {$Globals['pp_lang']['warning']}</b><br /></font></center></td></tr></table></td></tr></table></form>
        {$Globals['cright']}";

    print $output;
    printfooter();
}
else {
    $wasuploaded = "no";
    if (empty($skipupload)) $skipupload="";
    if ( $category == "" ) {
        $category = 500;
    }
    
    // If we are uploading as a different user, set us to that user
    $uploaduser = $userid;
    
    if ( isset($upuser) && !empty($upuser) ) {
        if ( is_numeric($upuser) ) {
            $userid = $upuser;
        }
        else {
            list( $userid, $tmail ) = get_username($upuser);
            if ( empty($userid) ) {
                diewell( "$upuser: Unable to find this user. Please check spelling." );
                exit;
            }
        }
    }

    if ( $skipupload == "skipupload" ) {
        $deftitle = urlencode($title);
        $defdesc = urlencode($desc);
        $keywords = urlencode($keywords);
        
        $furl = "{$Globals['zipuploadurl']}/$uploaduser";

        forward( "{$Globals['maindir']}/bulkupload.php?ppaction=addphotos&do=preview&photopath=$uploaduser&upuser=$upuser&deftitle=$deftitle&defdesc=$defdesc&defcat=$category&keywords=$keywords&numprocess=$numprocess&dthumbs=$dthumbs&processall=$processall&allowprint=$allowprint&furl=$furl", $Globals['pp_lang']['prepare'] );
        exit;
    }

    $realname = $HTTP_POST_FILES['theimage']['name'];

    if ( !empty($thevideo) && empty($realname) ) {
        process_image( $thevideo, $filepath, $category, 1 );

        $query = "SELECT id FROM photos WHERE userid=$userid AND bigimage='$thevideo'";
        $resulta = ppmysql_query($query,$link);
        list( $forwardid ) = mysql_fetch_row($resulta);
        ppmysql_free_result($resulta);

        if ( empty($forwardid) ) {
            diewell( "$realname: {$Globals['pp_lang']['videoprob']}" );
            exit;
        }
        forward( "{$Globals['maindir']}/showphoto.php?photo=$forwardid", $Globals['pp_lang']['upsuccess'] );
        exit;
    }

    if ( $realname == "" ) {
        diewell( $Globals['pp_lang']['errornoname'] );
        exit;
    }

    $realname = fixfilenames( $realname );
    $theext   = get_ext( $realname );

    $filepath = "{$Globals['datafull']}$category/$userid$realname";
    $outfilename = "$userid$realname";

    $query = "SELECT userid,bigimage FROM photos where userid=$userid";
    $resulta = ppmysql_query($query,$link);

    while( list( $uid, $bgimage ) = mysql_fetch_row($resulta) ) {
        if ($uid == $userid && $uid != 0) {
            if ( $bgimage == $realname ) {
                diewell("$realname: {$Globals['pp_lang']['dupe']}");
                exit;
            }
        }
    }
    ppmysql_free_result($resulta);

    $title = fixmessage( $title );
    $keywords = fixmessage( $keywords );
    $desc = fixmessage( $desc );

    if ( $category == "notcat" ) {
        diewell( $Globals['pp_lang']['topcat'] );
    }

    //####// Write the file to a directory #####

    //#// Do you wish to allow all file types?  yes/no (no capital letters)
    $allowall = "no";

    //#// If the above = "no"; then which is the only extention to allow?
    if ($realname != "") {
        $isfilegood = "yes";
        if ( $allowall != "yes" ) {
            if ( !is_image($outfilename) ) {
                $isfilegood = "no";
            }
        }

        if ($isfilegood == "yes") {
            handleupload();
        }

        //
        // ZIP Uploads for Users
        //
        if ( $Globals['allowzip'] == "yes" ) {
            if (strtolower(substr($outfilename,strlen($outfilename) - 4,4)) == ".zip" ) {
                if ( $Globals['unregpho'] == "yes" && $gologin == 1 ) {
                    diewell( $Globals['pp_lang']['zipreg'] );
                }

                $filepath = "{$Globals['zipuploaddir']}/$uploaduser";
                $filedir = "$filepath/$outfilename";

                if ( !file_exists( $filepath ) ) {
                    if ( !mkdir( $filepath, 0755 ) ) {
                        diewell( "$filepath: {$Globals['pp_lang']['errordir']}" );
                        exit;
                    }
                    chmod( $filepath, 0777 );
                }

                chdir( $filepath );
                handleupload( $filedir );

                $sys_cmd = "{$Globals['zip_command']} -qq $filedir";
                system( $sys_cmd );
                unlink( $filedir );

                $deftitle = urlencode($title);
                $defdesc = urlencode($desc);
                $furl = "{$Globals['zipuploadurl']}/$uploaduser";
                if ( empty($numprocess) ) $numprocess = 10;
                if ( empty($dthumbs) ) $dthumbs = "yes";

                forward( "{$Globals['maindir']}/bulkupload.php?ppaction=addphotos&do=preview&photopath=$uploaduser&upuser=$upuser&deftitle=$deftitle&defdesc=$defdesc&defcat=$category&keywords=$keywords&numprocess=$numprocess&dthumbs=$dthumbs&furl=$furl", $Globals['pp_lang']['process'] );
                exit;
            }
        }

        //
        // Multimedia uploads
        //
        if ( $Globals['allowmedia'] == "yes" ) {
            if ( is_multimedia( $outfilename ) ) {
                if ( $Globals['unregpho'] == "yes" && $gologin == 1 ) {
                    diewell( $Globals['pp_lang']['vidreg'] );
                }

                if ( !isset($thevideo) ) {
                    handleupload();
                    
                    if ( file_exists($filepath) ) {
                        $insize = filesize( $filepath );
                    }
                    else {
                        diewell("$filepath: {$Globals['pp_lang']['errorup']}");
                        exit;
                    }

                    $uploadsize = $Globals['mmuploadsize'];
                    if ( $nolimit == 0 && ($insize > ($uploadsize*1024)) ) {
                        unlink($filepath);
                        diewell( "{$Globals['pp_lang']['exceed']} {$uploadsize}kb.  {$Globals['pp_lang']['exceed2']}" );
                    }

                    printheader( $category, $Globals['pp_lang']['videoupload'] );

                    $output = "<br /><center>
                        <form method=\"post\" action=\"{$Globals['maindir']}/uploadphoto.php\" enctype=\"multipart/form-data\">
                        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\" align=\"center\">
                        <tr><td>
                        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
                        <tr align=\"center\">
                        <td colspan=\"2\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\">
                        <font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontmedium']}\"><b>{$Globals['galleryname']} {$Globals['pp_lang']['videoupload']}</b></font>
                        </td></tr>
                        <tr><td bgcolor=\"{$Globals['maincolor']}\" width=\"50%\">
                        <b><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\">
                        {$Globals['pp_lang']['nogen']}</font></b>
                        </td><td bgcolor=\"{$Globals['maincolor']}\"><input type=\"file\" name=\"theimage\" /></td></tr>
                        <tr><td colspan=\"2\" bgcolor=\"{$Globals['maincolor']}\">
                        <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><center>
                        <input type=\"hidden\" name=\"userid\" value=\"$uploaduser\" />
                        <input type=\"hidden\" name=\"category\" value=\"$category\" />
                        <input type=\"hidden\" name=\"upuser\" value=\"$userid\" />                        
                        <input type=\"hidden\" name=\"thevideo\" value=\"$realname\" />
                        <input type=\"hidden\" name=\"title\" value=\"$title\" />
                        <input type=\"hidden\" name=\"desc\" value=\"$desc\" />
                        <input type=\"hidden\" name=\"keywords\" value=\"$keywords\" />
                        <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"2000000\" />
                        <input type=\"submit\" value=\"{$Globals['pp_lang']['submit']}\" /></td></tr>
                        </table></td></tr></table></form><br />{$Globals['cright']}";

                    print $output;
                    printfooter();

                    exit;
                }
                else {
                    handleupload( $filedir );
                }
            }
        }
    }

    if ( $wasuploaded == "no" ) {
        diewell("{$Globals['pp_lang']['notype']}");
        exit;
    }
    
    if ( file_exists($filepath) ) {
        $insize = filesize( $filepath );
    }
    else {
        diewell("$filepath: {$Globals['pp_lang']['errorup']}");
        exit;
    }

    if ( $theext != ".zip" ) {
        if ( $nolimit == 0 && ($insize > ($up_k*1024)) ) {
            unlink($filepath);
            diewell( "{$Globals['pp_lang']['exceed']} ".$up_k."kb. {$Globals['pp_lang']['exceed2']}" );
            exit;
        }
    }

    $query = "SELECT SUM(filesize) AS fsize FROM photos WHERE userid=$userid";
    $resulta = ppmysql_query($query,$link);
    list( $diskuse ) = mysql_fetch_row($resulta);
    ppmysql_free_result( $resulta );

    $disk_k = ($disk_k * 1024);
    $diskbytes = $disk_k-($diskuse+$insize);

    if ( $nolimit == 0 ) {
        if ( $diskbytes < 0 ) {
            diewell( "{$Globals['pp_lang']['overmax1']} $disk_k {$Globals['pp_lang']['overmax2']}" );
            exit;
        }
    }

    if ( $isfilegood != "yes" ) {
        diewell( $Globals['pp_lang']['errortype'] );
        exit;
    }

    if ( isset($thevideo) ) {
        $thumbsize = create_thumb( $realname, $filepath, $category, $thevideo );
        unlink( $filepath );
        process_image( $thevideo, $filepath, $category, 1 );
        $realname = $thevideo;
    }
    else {
        $thumbsize = create_thumb( $realname, $filepath, $category );
        process_image( $realname, $filepath, $category );
    }

    $query = "SELECT id FROM photos WHERE userid=$userid AND bigimage='$realname'";
    $resulta = ppmysql_query($query,$link);
    list( $forwardid ) = mysql_fetch_row($resulta);
    ppmysql_free_result($resulta);

    if ( empty($forwardid) ) {
        diewell( "$realname: {$Globals['pp_lang']['errorprocess']}" );
        exit;
    }

    forward( "{$Globals['maindir']}/showphoto.php?photo=$forwardid", $Globals['pp_lang']['upsuccess'] );
}

?>
