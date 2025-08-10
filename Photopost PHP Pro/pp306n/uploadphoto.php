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
include("image-inc.php");

if ( is_array($HTTP_POST_FILES) ) {
    while(list($key,$value) = each($HTTP_POST_FILES)) {
        //if ($magic) {
        //    $value = stripslashes($value);
        //}
        ${$key} = $value;
    }
}

function handleupload( $location = "data" ) {
    global $HTTP_POST_FILES, $userid, $Globals, $category;

    if (is_uploaded_file($HTTP_POST_FILES['theimage']['tmp_name'])) {
        $tmpname = $HTTP_POST_FILES['theimage']['tmp_name'];
        $realname = $HTTP_POST_FILES['theimage']['name'];
        
        $realname  = str_replace( "%20", "_", $realname );
        $realname  = ereg_replace( "\\\\'", "_", strtolower($realname) );
        $stripname = get_filename( $realname );
        $theext    = get_ext( $realname );
        $stripname = ereg_replace( "[^a-zA-Z0-9/\:_]", "_", $stripname );
        $realname  = "{$stripname}.{$theext}";

        if ( $location != "data" ) {
            $dst_file = $location;
        }
        else {
            $dst_file = $Globals{'datafull'}."$category/$userid$realname";
        }

        copy($tmpname, $dst_file);
    } else {
        dieWell("Possible file upload attack or File Not Found for upload: $realname (".$realname.")");
        exit;
    }

    return;
}

list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();

$uploadsize = $Globals{'uploadsize'};
$gologin=0;
$nopost=0;

authenticate();

if ( $Globals{'adminnolimit'} == "yes" ) {
    if ( $adminedit  == 1) $nolimit = 1;
    else $nolimit = 0;
}
else {
    $nolimit = 0;
}

if ( $Globals{'adminexclude'} == "yes" ) {
    if ( $adminedit == 1 ) $adminexclude = 1;
    else $adminexclude = 0;
}
else {
    $adminexclude = 0;
}

if ( $adminedit == 0 ) {
    if ( $Globals{'allowup'} == "no" ) {
        dieWell( "User uploads not allowed" );
    }
}

$querystring = findenv("QUERY_STRING");
if ( ($Globals{'unregpho'} != "yes" && $gologin==1) || $querystring == "gologin" ) {
    $furl=$Globals{'maindir'};
    $furl= str_replace( $Globals{'domain'}, "", $furl );
    $furl="$furl/uploadphoto.php";

    login( $furl );
    exit;
}

if ( $gologin != 1 ) {
    if ( $nopost == 1 ) {
        dieWell("Sorry, you don't have permission to upload photos.");
        exit;
    }
    if ( $userup == 2 ) {
        dieWell("Sorry, but you have not verified your account yet.<p>You must do so before being able to upload.");
    }
}

topmenu();
$loginhtml = "( <a href=\"".$Globals{'maindir'}."/uploadphoto.php?gologin\">login</a> | ";
$logout = "<a href=\"".$Globals{'maindir'}."/logout.php?logout\">logout</a> )";

if ( $Globals{'unregpho'} == "yes" && $gologin == 1 ) {
    $username = "Unregistered";
    $userid = "0";
    $up_k = $Globals{'uploadsize'};
    $nolimit = 0;
}

if ( !IsSet($theimage) ) {
    $catdefault = "";
    
    if ( !empty($cat) ) {
        if ($adminedit == 1) {
            $query = "SELECT id,catname,thumbs FROM categories WHERE id=$cat LIMIT 1";
        }
        else {
            $query = "SELECT id,catname,thumbs FROM categories WHERE id=$cat AND private='no' LIMIT 1";
        }

        $resultb = mysql_query_eval($query,$link);
        while ( $row = mysql_fetch_row($resultb) ) {
            list( $subid, $subcatname, $subthumbs ) = $row;
            if ( $subid < 3000 ) {
                if ( $ugcat{$subid} != 1 ) {
                    $catdefault = "<option selected value=\"$subid\">$subcatname</option>";
                }
            }
        }
    }
    else {
        if ( $ugcat{500} != 1 ) {
            $query = "SELECT id,catname,thumbs FROM categories WHERE id=500 LIMIT 1";
            $resultb = mysql_query_eval($query,$link);
            $row = mysql_fetch_row($resultb);

            list( $subid, $subcatname, $subthumbs ) = $row;
            $catdefault = "<option selected value=\"$subid\">$subcatname</option>";
        }
    }

    $output = "<title>".$Globals{'galleryname'}." Image Upload</title>$header

        <center><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" height=\"40\" width=\"".$Globals{'tablewidth'}."\"><Tr>
        <Td valign=\"center\" width=\"50%\">&nbsp;</td>
        <td width=\"50%\" align=\"right\" valign=\"center\"><font face=\"verdana, arial\"
        size=\"2\">$menu&nbsp;</font></td></tr></table>
        <table cellpadding=\"0\" cellspacing=\"0\"
        border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"2\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\"
        color=\"".$Globals{'headfontcolor'}."\" size=\"2\"><B>".$Globals{'galleryname'}." Image
        Upload</font>
        </font></td>
        </tr>
        <form method=\"post\" action=\"".$Globals{'maindir'}."/uploadphoto.php\" enctype=\"multipart/form-data\">
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana,arial\" color=\"".$Globals{'maintext'}."\">Username</font></td><td
        bgcolor=\"".$Globals{'maincolor'}."\">
        <font size=\"2\" face=\"verdana\" color=\"".$Globals{'maintext'}."\">$username $loginhtml $logout</td></tr>
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\" width=\"50%\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Choose a
        category</font></td><Td bgcolor=\"".$Globals{'maincolor'}."\"><select
        name=\"category\">$catdefault";

    if (empty($subid)) $subid="";
    $selected = $subid;
    catmoveopt(0);
    $output .= $catoptions;

    $query = "SELECT SUM(filesize) AS fsize FROM photos WHERE userid=$userid";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_row($resulta);
    $diskuse = $row[0];

    if ( $nolimit == 0 ) {
        $disk_k = $disk_k*1024;
        $diskbytes = $disk_k-$diskuse;
        $diskspace = $diskbytes;
        $diskspace = $diskbytes/1024;
        $diskspace = sprintf("%1.1f", $diskspace);
        $diskbytes = number_format( $diskbytes );
        $diskspace = $diskspace."kb ($diskbytes bytes)";
    }
    else {
        $diskspace = "Unlimited";
    }

    if ( $Globals{'usenotify'} == "yes" && $userid != 0 ) {
        $notifyhtml = "<tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Want to be notified by email when users post replies?</td><td
            bgcolor=\"".$Globals{'maincolor'}."\"><select name=\"notify\"><option selected>No</option><option>Yes</option></select></td></tr>";
    }
    else
        $notifyhtml="";

    if ( $adminedit == 1 ) {
        $imgdir = $Globals{'zipuploaddir'}."/$userid";
        $skiphtml = "<tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Admin: Skip upload and process files in your upload directory<br>[Directory: $imgdir]</td><td
            bgcolor=\"".$Globals{'maincolor'}."\"><input type=\"checkbox\" name=\"skipupload\" value=\"skipupload\"></td></tr>";
    }
    else
        $skiphtml="";

    if ( $nolimit == 0 || $userid == 0 ) {
        $maxfilesize = $up_k."k file size limit.";
    }
    else {
        $maxfilesize = "No file size limit.";
    }

    if ( $Globals{'allowzip'} == "yes" ) {
        $maxfilesize .= " ZIP file uploads allowed (2MB limit).";
    }

    $output .= "</select></td></tr>
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana,arial\" color=\"".$Globals{'maintext'}."\">Photo to upload:</font><br><b><font size=\"1\" face=\"verdana,arial\"
        color=\"red\">$maxfilesize</font></b></td><td bgcolor=\"".$Globals{'maincolor'}."\"><input type=\"file\" name=\"theimage\"></td></tr>
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Disk space remaining for your account:</td><td
        bgcolor=\"".$Globals{'maincolor'}."\"><font face=\"verdana\" size=\"2\" color=\"".$Globals{'maintext'}."\">$diskspace</td></tr>
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Enter a title for the photo</td><td bgcolor=\"".$Globals{'maincolor'}."\"><input
        type=\"text\" name=\"title\"></td></tr>
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">To help users find your photo, please enter a few (up to 10) descriptive
        keywords (separated by spaces):</td><td bgcolor=\"".$Globals{'maincolor'}."\"><input type=\"text\" name=\"keywords\"></td></tr>
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">Photo Description</td><td bgcolor=\"".$Globals{'maincolor'}."\"><textarea
        name=\"desc\" cols=\"30\" rows=\"5\"></textarea></td></tr>
        $notifyhtml
        $skiphtml
        <Center>
        <Tr><Td colspan=\"2\" bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana,arial\"><center>
        <input type=\"hidden\" name=\"password\" value=\"$password\">
        <input type=\"hidden\" name=\"userid\" value=\"$userid\">
        <input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"2000000\">
        <input type=\"submit\" value=\"Upload/Submit\">
        <p><b>When you hit SUBMIT, the file you selected will be uploaded.</b><br></font><font size=\"1\" face=\"verdana,arial\">
        (Depending on the size of the file and your connection, this may take some time. <b>Please be patient.</b>)</p></font></td></tr></table></td></tr></table><p>".$Globals{'cright'}."$footer";

    print $output;
}
else {
    if (empty($skipupload)) $skipupload="";
    if ( $category == "" ) {
        $category = 500;
    }

    if ( $skipupload == "skipupload" ) {
        $deftitle = $title;
        $defdesc = $desc;
        $defcat = $category;
        $maxp = 10;
        $dthumbs = "yes";
        $furl = $Globals{'zipuploadurl'}."/$userid";

        forward( $Globals{'maindir'}."/bulkupload.php?ppaction=addphotos&do=preview&photopath=$userid&deftitle=$deftitle&defdesc=$defdesc&defcat=$defcat&keywords=$keywords&numprocess=$maxp&dthumbs=$dthumbs&furl=$furl", "Preparing to process image list!" );
        exit;
    }

    $realname = $HTTP_POST_FILES['theimage']['name'];

    if ( $realname == "" ) {
        dieWell( "You need to enter the name of a file to upload!" );
        exit;
    }

    $realname  = str_replace( "%20", "_", $realname );
    $realname  = ereg_replace( "\\\\'", "_", strtolower($realname) );
    $stripname = get_filename( $realname );
    $theext    = get_ext( $realname );
    $stripname = ereg_replace( "[^a-zA-Z0-9/\:_]", "_", $stripname );
    $realname  = "{$stripname}.{$theext}";

    $filepath = $Globals{'datafull'}."$category/$userid$realname";
    $outfilename = "$userid$realname";

    $mon = $mon + 1;
    $julian = mktime($hour,$min,$sec,$mon,$mday,$year);

    $query = "SELECT userid,bigimage FROM photos where userid=$userid";
    $resulta = mysql_query_eval($query,$link);

    while( $row = mysql_fetch_array($resulta, MYSQL_ASSOC) ) {
        $uid = $row['userid']; $bgimage = $row['bigimage'];
        if ($uid == $userid && $uid != 0) {
            if ( $bgimage == $realname ) {
                dieWell("Sorry, you already uploaded an image called $realname.  Try a different name.");
                exit;
            }
        }
    }
    mysql_free_result($resulta);

    $query = "SELECT SUM(filesize) AS fsize FROM photos WHERE userid=$userid";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_row($resulta);
    $diskuse = $row[0];
    $disk_k=$disk_k*1024;
    $diskbytes=$disk_k-$diskuse;

    if ( $nolimit == 0 ) {
        if ( $diskbytes < 0 ) {
            dieWell( "You are allowed a maximum of $disk_k bytes of diskspace for your images.  If you would like to
                upload more images, please delete some of your older images and/or optimize your images using lower quality jpg settings before
                uploading." );
            exit;
        }
    }

    $title = fixmessage( $title );
    $keywords = fixmessage( $keywords );
    $desc = fixmessage( $desc );

    if ( $category == "notcat" ) {
        $emessage = "The category you chose is a top level category.  Please go back<Br> and choose one of its subcategories to upload your image.";
        dieWell($emessage);
    }

    //####// Write the file to a directory #####

    //#// Do you wish to allow all file types?  yes/no (no capital letters)
    $allowall = "no";

    //#// If the above = "no"; then which is the only extention to allow?
    //#// Remember to have the LAST 4 characters i.e. .ext
    $theexta = "gif";
    $theextb = "jpg";
    $theextc = "png";

    if ($realname != "") {
        $isfilegood = "yes";
        if ( $allowall != "yes" ) {
            if ( $theext != $theexta && $theext != $theextb && $theext != $theextc ) {
                $isfilegood = "no";
            }
        }

        if ($isfilegood == "yes") {
            handleupload();
        }

        //
        // ZIP Uploads for Users
        //
        if ( $Globals{'allowzip'} ) {
            $theextz = ".zip";
            if (strtolower(substr($outfilename,strlen($outfilename) - 4,4)) == $theextz) {
                if ( $Globals{'unregpho'} == "yes" && $gologin == 1 ) {
                    dieWell("You must be a registered user to upload a ZIP file!");
                }

                $filepath = $Globals{'zipuploaddir'}."/$userid";
                $filedir = "$filepath/$outfilename";

                if ( !file_exists( $filepath ) ) {
                    mkdir( $filepath, 0755 );
                    chmod( $filepath, 0777 );
                }

                chdir( $filepath );

                handleupload( $filedir );

                $sys_cmd = $Globals{'zip_command'}." -qq $filedir";
                system( $sys_cmd );
                unlink( $filedir );

                $deftitle = $title;
                $defdesc = $desc;
                $defcat = $category;
                $maxp = 10;
                $dthumbs = "yes";
                $furl = $Globals{'zipuploadurl'}."/$userid";

                forward( $Globals{'maindir'}."/bulkupload.php?ppaction=addphotos&do=preview&photopath=$userid&deftitle=$deftitle&defdesc=$defdesc&defcat=$defcat&keywords=$keywords&numprocess=$maxp&dthumbs=$dthumbs&furl=$furl", "Preparing to process image list!" );
                exit;
            }
        }
    }

    if ( file_exists($filepath) ) {
        $insize = filesize( $filepath );
    }
    else {
        dieWell("File upload error. Cannot find uploaded file.<br>Path: [$filepath]");
        exit;
    }

    $uploadsize = $Globals{'uploadsize'};
    if ( $theext != "zip" ) {
        if ( $nolimit == 0 && ($insize > ($uploadsize*1024)) ) {
            unlink($filepath);
            dieWell("Your file exceeded our limit of ".$uploadsize."kb.  Please go back and try again.");
        }
    }

    if ( $isfilegood != "yes" ) {
        dieWell( "Image must be a .jpg, .gif, or .png file." );
        exit;
    }

    $thumbsize = create_thumb( $realname, $filepath, $category );
    process_image( $realname, $filepath, $category );

    $query = "SELECT id FROM photos WHERE userid=$userid AND bigimage='$realname'";
    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_array($resulta);
    $forwardid = $row['id'];
    mysql_free_result($resulta);

    if ( empty($forwardid) ) {
        dieWell( "There was a problem processing your image: $realname.<p>Please notify the System Administrator." );
        exit;
    }
    forward( $Globals{'maindir'}."/showphoto.php?photo=$forwardid", "Your image was uploaded successfully!" );
}

?>
