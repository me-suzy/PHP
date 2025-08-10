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
require "languages/$pplang/bulkupload.php";
require "login-inc.php";
require "image-inc.php";

$gologin=0; $nopost=0;

authenticate();

if ( isset($Globals['ppboards']) && $adminedit != 1 ) {
    if ( $Globals['ppboards'] == "closed" ) {
        print $Globals['pp_lang']['closed'];
        exit;
    }
}

if ( $Globals['adminnolimit'] == "yes" ) {
    if ( $adminedit  == 1) $nolimit = 1;
    else $nolimit = 0;
}
else
    $nolimit = 0;

if ( $Globals['adminexclude'] == "yes" ) {
    if ( $adminedit == 1 ) $adminexclude = 1;
    else $adminexclude = 0;
}
else
    $adminexclude = 0;

if ( $adminedit == 0 ) {
    if ( $Globals['allowup'] == "no" )
        diewell( $Globals['pp_lang']['noupload'] );
}

$qenv = findenv( "QUERY_STRING" );
if ( ($useruploads == 0 && $gologin==1) || $querystring == "gologin" ) {
    $furl=$Globals['maindir'];
    $furl= str_replace( $Globals['domain'], "", $furl );
    $furl="$furl/uploadform.php";
    login($furl);
    exit;
}

if ( $gologin != 1 ) {
    if ( $nopost == 1 || $useruploads == 0 ) {
        diewell($Globals['pp_lang']['noperm']);
        exit;
    }
    
    if ( $useruploads == 2 ) {
        diewell($Globals['pp_lang']['noverify']);
    }
}

topmenu();

//
// skip previews entirely and just process all the images
//
if ( $ppaction == "addphotos" && $processall == "processall" ) {
    if ( !isset($numprocess) ) $numprocess = 20;
    
    $photocount = 0;
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
    $inpath = "{$Globals['zipuploaddir']}/$photopath";
    $openpath = $inpath;
    $imgurl = $furl;        

    if ( $handle = opendir( $openpath ) ) {
        while ( $realname = readdir( $handle ) ) {
            if (( $realname != ".") && ( $realname != ".." ) ) {
                $title = $deftitle;
                $desc = $defdesc;
                
                $filein = $inpath."/$realname";
                $chkrealname  = fixfilenames($realname);

                if ( strcmp($chkrealname, $realname) != 0 ) {
                    $newfile = $inpath."/$chkrealname";
                    @rename($filein, $newfile);
                    $realname = $chkrealname;
                    $filein = $newfile;
                }
                
                $theext = get_ext($realname);

                $querya="SELECT id FROM photos WHERE userid=$userid AND bigimage='$realname'";
                $catq = ppmysql_query($querya,$link);
                $imgchk = mysql_num_rows($catq);

                if ( $imgchk != 0 ) {
                    // Image is a duplicate
                    $filenoext = get_filename( $realname );

                    $x = 0;                        
                    while ( $imgchk != 0 ) {
                        $x++;
                        $newfile = "$filenoext$x$theext";
                        $newfilepath = $inpath."/$newfile";                            
                        
                        $querya="SELECT id FROM photos WHERE userid=$userid AND bigimage='$newfile'";
                        $catq = ppmysql_query($querya,$link);
                        $imgchk = mysql_num_rows($catq);
                        
                        if ( $imgchk == 0 ) {
                            @rename($filepath, $newfilepath);
                            $realname = $newfile;
                            $filein = $newfilepath;
                        }
                    }
                }

                $filepath = "{$Globals['datafull']}$defcat/$userid$realname";                   
                copy( $filein, $filepath );
                
                echo "Processing $realname... ($filepath) ($defcat)<br />";
                @flush();
                
                if ( is_multimedia( $filepath ) ) {
                    process_image( $realname, $filepath, $defcat, 1 );
                }
                elseif ( is_image( $filepath ) ) {
                    // Open image, write out thumb, fullsize, and medium as needed
                    create_thumb( $realname, $filepath, $defcat );
                    process_image( $realname, $filepath, $defcat );
                }
                else {
                    echo "ERROR! unknown file type $filepath<br />";
                    unlink( $filepath );
                }

                // Delete thumb and image from temp dir
                if ( file_exists( $filein ) )
                    @unlink ($filein);
                    
                $photocount++;
                if ( $photocount == $numprocess ) {
                    $fwdlink = "bulkupload.php?ppaction=addphotos&do=preview&photopath=$photopath&upuser=$userid&deftitle=$deftitle&defdesc=$defdesc&defcat=$defcat&keywords=$keywords&numprocess=$numprocess&dthumbs=$dthumbs&processall=$processall&allowprint=$allowprint&furl=$furl";
                    echo "<br />{$Globals['pp_lang']['preppro']} $numprocess {$Globals['pp_lang']['images']}; <a href=\"{$Globals['maindir']}/$fwdlink\">{$Globals['pp_lang']['click']}</a> {$Globals['pp_lang']['notrefresh']}";
                    echo "<script language=\"javascript\">window.location=\"$fwdlink\";</script>";
                    exit;                    
                }
            }
        }
    }
    
    $forward_url = "{$Globals['maindir']}/index.php";
    forward( $forward_url, $Globals['pp_lang']['processing'] );
    exit;
}

if ( $ppaction == "addphotos" ) {
    if ($do == "process") {
        $totalphotos=$thecount;
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

        for ( $i = 1; $i <= $totalphotos; $i++ ) {
            $addk = "add$i";
            $catkey = "cat$i";
            $titlekey = "title$i";
            $desckey = "desc$i";
            $imgkey = "imgname$i";

            $imgname = ${$imgkey};
            $category = ${$catkey};
            $title = ${$titlekey};
            $desc = ${$desckey};
            $addkey = ${$addk};

            $filein = $inpath."/$imgname";

            if ( $defcat != $category ) {
                move_image( $defcat, $category, $userid, $imgname );
            }           

            if ($addkey == 1) {
                $imgname = fixfilenames($imgname);
                $filepath = "{$Globals['datafull']}$category/$userid$imgname";
                @copy( $filein, $filepath );

                if ( is_multimedia( $filepath ) ) {
                    process_image( $imgname, $filepath, $category, 1 );
                }
                elseif ( is_image( $filepath ) ) {
                    // Open image, write out thumb, fullsize, and medium as needed
                    create_thumb( $imgname, $filepath, $category );
                    process_image( $imgname, $filepath, $category );
                }
                else {
                    @unlink( $filepath );
                }

                // Delete thumb and image from temp dir
                if ( file_exists( $filein ) )
                    @unlink ($filein);
            }
            else {
                // Delete the image and thumb from temp dir
                if ( file_exists( $filein ) )
                    @unlink ($filein);

                $filenoext = get_filename( $imgname );
                $theext    = get_ext( $imgname );
                $thumbnail = "$userid{$filenoext}-thumb$theext";
                $tfile = "{$Globals['datafull']}$category/$thumbnail";

                if ( file_exists( $tfile ) )
                    @unlink ($tfile);
            }
        }
        
        $forward_url = "{$Globals['maindir']}/bulkupload.php?ppaction=addphotos&do=preview&thecount=$totalphotos&photopath=$photopath&upuser=$upuser&deftitle=$deftitle&defdesc=$defdesc&defcat=$defcat&keywords=$keywords&numprocess=$numprocess&dthumbs=$dthumbs&allowprint=$allowprint&furl=$furl";
        forward( $forward_url, $Globals['pp_lang']['processing'] );
        exit;
    }

    printheader( 0, $Globals['pp_lang']['bulkuploads'] );

    $output = "<center><hr>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"{$Globals['headcolor']}\">
        <font size=\"{$Globals['fontlarge']}\" color=\"{$Globals['headfontcolor']}\"
        face=\"{$Globals['mainfonts']}\">{$Globals['pp_lang']['photoadd']}</font>
        </td></tr><tr>
        <td bgcolor=\"#f7f7f7\">$menu</td></tr>
        <form action=\"{$Globals['maindir']}/bulkupload.php\" method=\"POST\">";

    if ( $do == "preview" ) {  // Get dir listing, thumbs, w/checkboxes
        if ( $numprocess == 10 ) $numopts = "<option selected>10</option>";
        else $numopts = "<option>10</option>";
        if ( $numprocess == 25 ) $numopts .= "<option selected>25</option>";
        else $numopts .= "<option>25</option>";
        if ( $numprocess == 50 ) $numopts .= "<option selected>50</option>";
        else $numopts .= "<option>50</option>";
        if ( $numprocess == 100 ) $numopts .= "<option selected>100</option>";
        else $numopts .= "<option>100</option>";

        if ( $dthumbs == "yes" ) $thumopts = "<option selected>yes</option><option>no</option>";
        else $thumopts = "<option selected>no</option><option>yes</option>";
        
        $photocount = 0;
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
        $inpath = "{$Globals['zipuploaddir']}/$photopath";        

        $middle = "<tr><td bgcolor=\"{$Globals['maincolor']}\"><center><br />
            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"{$Globals['bordercolor']}\"><tr>
            <td><table border=\"0\" cellpadding=\"5\" cellspacing=\"1\"><tr>
            <td colspan=\"4\" bgcolor=\"#FFFFFF\">
            <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['addselect']}
            </td>
            </tr><tr>
            <td colspan=\"4\" width=\"100%\" height=\"2\" bgcolor=\"#000000\"></td></tr><tr>
            <td bgcolor=\"#FFFFFF\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['image']}</font></td>
            <td bgcolor=\"#FFFFFF\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['add']}?</font></td>
            <td bgcolor=\"#FFFFFF\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['category']}</font></td>
            <td bgcolor=\"#FFFFFF\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['optional']}</font></td></tr>";

        $defcatname = "";
        $querya = "SELECT catname FROM categories WHERE id=$defcat";
        $catq = ppmysql_query($querya, $link);
        
        if ( $catq ) {
            list ( $defcatname ) = mysql_fetch_row($catq);
            ppmysql_free_result($catq);
        }

        catmoveopt(0);

        if ( $handle = opendir( $inpath ) ) {
            while ( $realname = readdir( $handle ) ) {
                if (( $realname != ".") && ( $realname != ".." ) ) {
                    $filepath = $inpath."/$realname";
                    $chkrealname  = fixfilenames($realname);

                    if ( strcmp($chkrealname, $realname) != 0 ) {
                        $newfile = $inpath."/$chkrealname";
                        @rename($filepath, $newfile);
                        $realname = $chkrealname;
                        $filepath = $newfile;
                    }
                    
                    $theext = get_ext($realname);

                    $querya="SELECT id FROM photos WHERE userid=$userid AND bigimage='$realname'";
                    $catq = ppmysql_query($querya,$link);
                    $imgchk = mysql_num_rows($catq);
                    
                    if ( $imgchk != 0 ) {
                        // Image is a duplicate
                        $filenoext = get_filename( $realname );

                        $x = 0;                        
                        while ( $imgchk != 0 ) {
                            $x++;
                            $newfile = "$filenoext$x$theext";
                            $newfilepath = $inpath."/$newfile";                            
                            
                            $querya="SELECT id FROM photos WHERE userid=$userid AND bigimage='$newfile'";
                            $catq = ppmysql_query($querya,$link);
                            $imgchk = mysql_num_rows($catq);
                            
                            if ( $imgchk == 0 ) {
                                @rename($filepath, $newfilepath);
                                $realname = $newfile;
                                $filepath = $newfilepath;
                            }
                        }
                    }

                    $thumb = "";
                    $size = filesize( $filepath );
                        
                    if ( is_image($realname) && $photocount < $numprocess && $size > 0 ) {
                        $photocount++;
                        if ( $dthumbs == "yes") {
                            create_thumb( $realname, $filepath, $defcat );
                            $thumb = "<a target=\"_blank\" href=\"".$Globals['zipuploadurl']."/$userid/$thumbnail\"><img border=\"0\" src=\"".$Globals['datadir']."/$defcat/$thumbnail\"></a><br />";                                
                        }

                        $middle .= "<tr><td bgcolor=\"#FFFFFF\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" color=\"{$Globals['maintext']}\"><b><center>$thumb$realname</center></td><td
                            bgcolor=\"#FFFFFF\"><center>
                            <input type=\"hidden\" name=\"imgname".$photocount."\" value=\"$realname\">                            
                            <input type=\"checkbox\" CHECKED value=\"1\" name=\"add$photocount\"></center></td>
                            <td bgcolor=\"#FFFFFF\"><select name=\"cat$photocount\" style=\"font-size: 9pt; background: FFFFFF;\">
                            <option value=\"$defcat\" selected=\"selected\">$defcatname</option>$catoptions</select></td>
                            <td bgcolor=\"#FFFFFF\">
                            <table width=\"95%\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><font face=\"{$Globals['mainfonts']}\"
                            size=\"{$Globals['fontsmall']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['title']}:
                            </td><td>
                            <input type=\"text\" size=\"30\" name=\"title$photocount\" value=\"$deftitle\"
                            style=\"font-size: 9pt; background: FFFFFF;\"></td></tr>
                            <tr><td><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\" color=\"{$Globals['maintext']}\">{$Globals['pp_lang']['desc']}:</td><td>
                            <input type=\"text\" size=\"30\" name=\"desc$photocount\" value=\"$defdesc\" 
                            style=\"font-size: 9pt; background: FFFFFF;\"></td></tr></table></td></tr>";
                    }
                    elseif ( is_multimedia($realname) && $photocount < $numprocess && $size > 0) {
                        $photocount++;

                        if ( $dthumbs == "yes") {
                            //create_thumb( $realname, $filepath, $defcat );
                            $thumb = "<a target=\"_blank\" href=\"".$Globals['zipuploadurl']."/$userid/$realname\"><img border=\"0\" src=\"".$Globals['idir']."/video.jpg\"></a><br />";
                        }

                        $middle .= "<tr><td bgcolor=\"#FFFFFF\"><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\"><b><center>$thumb$realname</center></td>
                            <td bgcolor=\"#FFFFFF\"><center><input type=\"hidden\" name=\"imgname".$photocount."\" value=\"$realname\">
                            <input type=\"checkbox\" CHECKED value=\"1\" name=\"add$photocount\"></center></td>
                            <td bgcolor=\"#FFFFFF\"><select name=\"cat$photocount\" style=\"font-size: 9pt; background: FFFFFF;\">
                            <option value=\"$defcat\" selected=\"selected\">$defcatname</option>$catoptions</select></td>
                            <td bgcolor=\"#FFFFFF\">
                            <table width=\"95%\" cellpadding=\"0\" cellspacing=\"0\"><tr><td><font face=\"{$Globals['mainfonts']}\"
                            size=\"{$Globals['fontsmall']}\">{$Globals['pp_lang']['title']}:
                            </td><td>
                            <input type=\"text\" size=\"30\" name=\"title$photocount\" value=\"$deftitle\"
                            style=\"font-size: 9pt; background: FFFFFF;\"></td></tr>
                            <tr><td><font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontsmall']}\">{$Globals['pp_lang']['desc']}:</td><td>
                            <input type=\"text\" size=\"30\" name=\"desc$photocount\" value=\"$defdesc\" style=\"font-size:
                            9pt; background: FFFFFF;\"></td></tr></table></td></tr>";
                    }
                }
            }
        }

        if ($photocount == 0) {
            $middle .= "<tr><td colspan=\"4\" bgcolor=\"#FFFFFF\">
                <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\">
                <center>{$Globals['pp_lang']['nomore']}<br /><b>
                <a href=\"{$Globals['maindir']}/index.php\">{$Globals['pp_lang']['clickhere']}</a></b></center></td></tr>";
        }

        $middle .= "<tr><td bgcolor=\"#FFFFFF\" colspan=\"4\"><center>
            <input type=\"hidden\" name=\"furl\" value=\"$furl\">
            <input type=\"hidden\" name=\"deftitle\" value=\"$deftitle\">            
            <input type=\"hidden\" name=\"allowprint\" value=\"$allowprint\">
            <input type=\"hidden\" name=\"defdesc\" value=\"$defdesc\">
            <input type=\"hidden\" name=\"defcat\" value=\"$defcat\">
            <input type=\"hidden\" name=\"keywords\" value=\"$keywords\">
            <input type=\"hidden\" name=\"upuser\" value=\"$upuser\">            
            <input type=\"hidden\" name=\"photopath\" value=\"$photopath\">
            <input type=\"hidden\" name=\"ppaction\" value=\"addphotos\">
            <input type=\"hidden\" name=\"do\" value=\"process\">
            <input name=\"thecount\" value=\"$photocount\" type=\"hidden\">
            <input name=\"inpath\" value=\"$inpath\" type=\"hidden\">";

        if ( $photocount != 0 ) {
            $middle .= "<font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\">{$Globals['pp_lang']['show']} <select name=\"dthumbs\">$thumopts</select></font><br />
                <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\">{$Globals['pp_lang']['thumbs']} <select name=\"numprocess\">$numopts</select><p>";
        }

        $middle .= "<input type=\"submit\" value=\"{$Globals['pp_lang']['process']}\"></form>
            </td></tr></table></td></tr></table>";
    }

    $output .= "$middle</td></tr></table></td></tr></table>{$Globals['cright']}";    

    print $output;
    printfooter();
    
    exit;
}

?>
