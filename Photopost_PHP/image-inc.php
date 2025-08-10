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

function resize_jpeg( $image_file_path, $new_image_file_path, $max_width=480, $max_height=1600 )
{
    global $Globals;

    $return_val = 1;

    $image_stats = getimagesize( $image_file_path );
    $FullImage_width = $image_stats[0];
    $FullImage_height = $image_stats[1];
    $img_type = $image_stats[2];

    switch( $img_type ) {
        case 1: $src_img = ImageCreateFromGif($image_file_path); break;
        case 2: $src_img = ImageCreateFromJpeg($image_file_path); break;
        case 3: $src_img = ImageCreateFromPng($image_file_path); break;
        default: diewell("Sorry, this image type ($img_type) is not supported yet."); exit;
    }

    if ( !$src_img ) {
        diewell("Unable to create image [$image_file_path].<br />Please contact system administrator.");
        exit;
    }

    $ratio =  ( $FullImage_width > $max_width ) ? (real)($max_width / $FullImage_width) : 1 ;
    $new_width = ((int)($FullImage_width * $ratio));    //full-size width
    $new_height = ((int)($FullImage_height * $ratio));    //full-size height

    $ratio =  ( $new_height > $max_height ) ? (real)($max_height / $new_height) : 1 ;
    $new_width = ((int)($new_width * $ratio));    //mid-size width
    $new_height = ((int)($new_height * $ratio));    //mid-size height

    if ( $new_width == $FullImage_width && $new_height == $FullImage_height )
        copy ( $image_file_path, $new_image_file_path );

    $full_id = ImageCreateTrueColor( $new_width, $new_height );
    ImageCopyResampled( $full_id, $src_img, 0,0, 0,0, $new_width, $new_height,
                $FullImage_width, $FullImage_height );

    switch( $img_type ) {
        case 1: $return_val = ImageGIF( $full_id, $new_image_file_path ); break;
        case 2: $return_val = ImageJPEG( $full_id, $new_image_file_path, 90 ); break;
        case 3: $return_val = ImagePNG( $full_id, $new_image_file_path ); break;
        default: diewell("Sorry, this image type is not supported yet."); exit;
    }

    ImageDestroy( $full_id );

    return ($return_val) ? TRUE : FALSE ;
}

function gdwatermark($srcfilename, $watermark) {
    $imageInfo = getimagesize($srcfilename);
    $width = $imageInfo[0];
    $height = $imageInfo[1];

    $logoinfo = getimagesize($watermark);
    $logowidth = $logoinfo[0];
    $logoheight = $logoinfo[1];

    $horizextra =$width - $logowidth;
    $vertextra =$height - $logoheight;
    // middle
    //$horizmargin =  round($horizextra / 2);
    //$vertmargin =  round($vertextra / 2);
    // lower right corner
    $horizmargin =  $horizextra;
    $vertmargin =  $vertextra;

    $photoImage = ImageCreateFromJPEG($srcfilename);
    ImageAlphaBlending($photoImage, true);

    $logoImage = ImageCreateFromPNG($watermark);
    $logoW = ImageSX($logoImage);
    $logoH = ImageSY($logoImage);

    ImageCopy($photoImage, $logoImage, $horizmargin, $vertmargin, 0, 0, $logoW, $logoH);

    //ImageJPEG($photoImage); // output to browser
    ImageJPEG($photoImage, $srcfilename, 90);

    ImageDestroy($photoImage);
    ImageDestroy($logoImage);
}


function create_thumb( $realname, $filepath, $thecat, $thevideo="" ) {
    global $Globals, $userid, $imagewidth, $imageheight, $thumbnail, $resizeorig, $uganno;

    //
    // NEW RESIZE CODE
    //
    $basedir = $Globals['datafull'];
    $previewwidth = $Globals['previewwidth'];
    
    if ( $filepath == "default" ) {
        $filepath = "{$Globals['idir']}/video.jpg";
    }

    $image_stats = getimagesize( $filepath );
    $imagewidth = $image_stats[0];
    $imageheight = $image_stats[1];
    $img_type = $image_stats[2];
    
    // Create thumbnails
    if ( $thevideo == "" ) {
        $filenoext = get_filename( $realname );
        $theext    = get_ext( $realname );
        $thumbnail = "$userid".$filenoext."-thumb$theext";
        $thumbnail = strtolower( $thumbnail );
        $outthumb = "$basedir$thecat/$thumbnail";
    }
    elseif ( $thevideo == "rebuildthumbnail" ) {
        $filenoext = get_filename( $realname );
        $theext    = get_ext( $realname );
        $thumbnail = $filenoext."-thumb$theext";
        $thumbnail = strtolower( $thumbnail );        
        $outthumb = "$basedir$thecat/$thumbnail";
    }
    else {
        $filenoext = get_filename( $thevideo );
        $theext = "jpg";
        $thumbnail = "$userid".$filenoext."-thumb.jpg";
        $thumbnail = strtolower( $thumbnail );
        $outthumb = "$basedir$thecat/$thumbnail";
    }
    
    if ( !file_exists( $outthumb ) )  {
        if ( $Globals['usegd'] != 0 ) {
            if ( $imageheight < $imagewidth ) {
                $scaleFactor = $imagewidth / $previewwidth;
                $newheight = round( $imageheight / $scaleFactor );
                $newwidth = $previewwidth;
            }
            else {
                $scaleFactor = $imageheight / $previewwidth;
                $newwidth = round( $imagewidth / $scaleFactor );
                $newheight = $previewwidth;
            }

            $resize_worked = resize_jpeg($filepath, $outthumb, $newwidth, $newheight);
        }
        else {
            copy ( $filepath, $outthumb );

            // watermark thumbnail before resizing
            // uncomment if you want your thumbnails to have watermarks
            //if ( $uganno{$thecat} != 1 && $Globals['annotate'] == "yes") {
            //    // stamp the image
            //    watermark( $outthumb );
            //}
            
            // if image is taller than wider, then portrait
            if ( $imageheight < $imagewidth ) {
                $scaleFactor = $imagewidth / $previewwidth;
                $newheight = round( $imageheight / $scaleFactor );
                $newwidth = $previewwidth;
                //$previewwidth = $previewwidth
            }
            else {
                $scaleFactor = $imageheight / $previewwidth;
                $newwidth = round( $imagewidth / $scaleFactor );
                $newheight = $previewwidth;
                //$previewheight = $previewwidth
            }

            $theext = str_replace( ".", "", $theext );
            $syscmd = $Globals['mogrify_command']." -format $theext -geometry ".$newwidth."x".$newheight." $outthumb";

            // call ImageMagick mogrify to create the thumbnail
            system( $syscmd, $retval );
            if ( $retval != 0 ) {
                diewell("Error creating thumbnail! Error code: $retval<p>Command: $syscmd");
                unlink( $outthumb );
                unlink( $filepath );
                exit;
            }
        }
    }

    $imagesize = filesize( $outthumb );

    return( $imagesize );
}

function watermark( $filepath, $isadmin=0 ) {
    global $Globals;
    
    $agravity = $Globals['gravity'];
    $water_image = $Globals['watermark'];

    // need to execute this command after images:
    // composite -compose over -gravity southeast eblogo.jpg jess.jpg jesslogo.jpg

    if ( $Globals['usegd'] != 0 ) {
        $retval = gdwatermark( $filepath, $water_image );
    }
    else {
        $composite_cmd = str_replace( "mogrify", "composite", $Globals['mogrify_command'] );
        $stampcmd = $composite_cmd." -compose over -gravity $agravity $water_image $filepath $filepath";
        system( $stampcmd, $retval );

        if ( $retval != 0 ) {
            if ( $isadmin == 0 ) {
                diewell("Error creating watermarked original! Error code: $retval<br /><br />Command: $stampcmd");
                exit;
            }
            else {
                print "Error creating watermarked original on $filepath [$stampcmd]<br />";
            }
        }
    }
     
    return;
}

function readexifinfo($file, $origfile) {
    global $Globals;
    
    $return = array();
    $system_cmd = "{$Globals['jhead_command']} -exonly $file";
    exec($system_cmd, $return, $status);
        
	$exifinfo = array();
	if ($status == 0) {
        while (list($key,$value) = each ($return)) {
            $explodeReturn = explode(':', $value, 2);
            if ( trim($explodeReturn[0]) == "File name" ) $explodeReturn[1] = $origfile;
            
	        if ($exifinfo[trim($explodeReturn[0])]) { 
		    	$exifinfo[trim($explodeReturn[0])] .= "<br />" . trim($explodeReturn[1]);
	        } else {
		    	$exifinfo[trim($explodeReturn[0])] = trim($explodeReturn[1]);
	        }
	    }
	}

    return ($exifinfo);
}

function process_image( $realname, $filepath, $thecat, $thevideo=0 ) {
    global $Globals, $userid, $link, $db_link, $uganno;
    global $username, $usergroup, $title, $desc, $keywords;
    global $adminexclude, $keywords, $notify, $resizeorig, $allowprint;

    list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
    $mon = $mon + 1;
    $julian = mktime($hour,$min,$sec,$mon,$mday,$year);

    $filenoext = get_filename( $realname );
    $theext    = get_ext( $realname );
    $outfilename = "$userid$realname";
    $basedir = $Globals['datafull'];

    if ( $thevideo == 0 ) {
        $image_stats = getimagesize( $filepath );
        $imagewidth = $image_stats[0];
        $imageheight = $image_stats[1];
    
        $resizeorig = 0;
        $maxwidth = $Globals['maxwidth'];
        $maxheight = $Globals['maxheight'];
    
        if ( $imagewidth > $maxwidth ) {
            if ( $Globals['resizeorig'] == "yes" ) {
                $resizeorig = 1;
            }
            else {
                diewell("Your graphic is too wide!  Please upload a smaller one.");
                unlink($realname);
                exit;
            }
        }
    
        if ( $imageheight > $maxheight ) {
            if ( $Globals['resizeorig'] == "yes") {
                $resizeorig = 1;
            }
            else {
                diewell("Your graphic is too tall!  Please upload a smaller one.");
                unlink($realname);
                exit;
            }
        }
       
        // before we do any resizing, get the EXIF information (if any)
        $exifinfo = "";
        if ( $Globals['showexif'] == "yes" ) 
            $exifinfo = readexifinfo( $filepath, $realname );        
                
        // Watermark
        $watermarked = "no";
        if ( $uganno{$thecat} != 1 && $Globals['annotate'] == "yes") {
            // stamp the image
            watermark( $filepath );
            $watermarked = "yes";
        }
    
        // Resizing
        if ( $resizeorig == 1 ) {
            $newheight = $imageheight;
            $newwidth = $imagewidth;
            
            if ( $newwidth > $maxwidth ) {
                $scaleFactor = $newwidth / $maxwidth;
                $newheight = round( $newheight / $scaleFactor );
                $newwidth = $maxwidth;
            }
            
            if ( $newheight > $maxheight ) {
                $scaleFactor = $newheight / $maxheight;
                $newwidth = round( $newwidth / $scaleFactor );
                $newheight = $maxwidth;
            }
    
            if ( $Globals['usegd'] != 0 ) {
                $resize_worked = resize_jpeg($filepath, $filepath, $newwidth, $newheight);
            }
            else {
                $theext = str_replace( ".", "", $theext );
                $syscmd = $Globals['mogrify_command']." -format $theext -geometry ".$newwidth."x".$newheight." $filepath";
    
                // call ImageMagick mogrify to resize the original down
                system( $syscmd, $retval );
                if ( $retval != 0 ) {
                    diewell("Error creating resized original! Error code: $retval");
                    unlink( $outthumb );
                    unlink( $filepath );
                    exit;
                }
            }
    
            $image_stats = getimagesize( $filepath );
            $imagewidth = $image_stats[0];
            $imageheight = $image_stats[1];
        }
        
        $imagesize = filesize( $filepath );
        //##// end resize original and/or annotate original ###
    
        //##// create a medium sized graphic if the graphic is too big ###
        $createmed = 0;
        $biggraphic = $Globals['biggraphic'];
    
        if ( $Globals['bigsave'] == "yes" ) {
            if ( $imagewidth > $biggraphic || $imageheight > $biggraphic ) $createmed = 1;
        }
    
        if ( $createmed == 1 ) {
            $medium = $filenoext."-med$theext";
            $medfile="$basedir$thecat/$userid$medium";
    
            if ( $imageheight > $imagewidth ) {
                $scaleFactor = $imagewidth / $biggraphic;
                $medwidth = round( $imagewidth / $scaleFactor );
                $medheight = $biggraphic;
            }
            else {
                $scaleFactor = $imageheight / $biggraphic;
                $medheight = round( $imageheight / $scaleFactor );
                $medwidth = $biggraphic;
            }
    
            if ( $Globals['usegd'] != 0 ) {
                $resize_worked = resize_jpeg($filepath, $medfile, $medwidth, $medheight);
            }
            else {
                copy ( $filepath, $medfile );
                $theext = str_replace( ".", "", $theext );
                $syscmd = $Globals['mogrify_command']." -format $theext -geometry ".$medwidth."x".$medheight." $medfile";
    
                // call ImageMagick mogrify to create the medium image
                system( $syscmd, $retval );
                if ( $retval != 0 ) {
                    diewell("Error creating resized medium image! Error code: $retval<br />Command attempted: $syscmd");
                    unlink( $outthumb );
                    unlink( $filepath );
                    unlink( $medium );
                    exit;
                }
            }
    
            // get the proper stats
            $image_stats = getimagesize( $medfile );
            $medwidth = $image_stats[0];
            $medheight = $image_stats[1];
            $medsize = filesize( $medfile );
        }
        else {
            $medwidth = 0;
            $medheight = 0;
            $medsize = 0;
        }
        //##// end medium sized ###
    }
    else {
        $videofile = "$basedir$thecat/$userid$realname";
    
        $imagesize = filesize( $videofile );
        $medwidth = 0;
        $medheight = 0;
        $medsize = 0;
        $imageheight = 0;
        $imagewidth = 0;
    }

    if ( $Globals['moderation'] == "yes" && $adminexclude != 1 ) $moderate = "0";
    else $moderate = "1";

    list( $tmpuname, $tmail ) = get_username($userid);
    $username = addslashes( $tmpuname );
    
    if ( empty($title) ) $title = $filenoext;
    $ititle = urldecode( $title );
    $ititle = addslashes( $title );
    $idesc = urldecode( $desc );
    $idesc = addslashes( $desc );
    $ikeywords = addslashes( $keywords );

    $query = "INSERT INTO photos (id,user,userid,cat,date,title,description,keywords,bigimage,width,height,filesize,medwidth,medheight,medsize,approved,lastpost,watermarked,allowprint)
        values(NULL,'$username', $userid, $thecat, $julian, '$ititle', '$idesc', '$ikeywords', '$realname', $imagewidth, $imageheight, $imagesize, $medwidth, $medheight, $medsize, $moderate, $julian, '$watermarked','$allowprint')";
        
    $resulta = ppmysql_query($query, $link);

    if ( !$resulta ) {
        diewell( "Database error! Please report to System Administrator.<br /><br />$query" );
        exit;
    }
        
    $lastphotoid = mysql_insert_id( $link );    
    $query2 = "UPDATE categories SET photos=(photos+1),lastphoto='$lastphotoid' WHERE id=$thecat";
    $resultb = ppmysql_query($query2, $link);

    if ( $exifinfo != "" && count($exifinfo) > 6 ) {
        $storeexif = addslashes(serialize($exifinfo));            
        $query3 = "INSERT INTO exif (photoid,exifinfo) values( $lastphotoid, '$storeexif' )";
        $resultc = ppmysql_query($query3, $link);
    }
    
    if ( $Globals['uploadnotify'] == "yes" ) {
        $letter="$username has uploaded one or more photos to your gallery.
        
If approval is required, visit the admin panel:

Image name: $realname
Title: $title
Size: $imagesize
Keywords: $keywords
Description: $desc

Link to image: ".$Globals['datadir']."/$thecat/$outfilename

{$Globals['maindir']}/adm-index.php";

        $subject = $Globals['webname']." photo upload(s)";
        $send_to = $Globals['adminemail'];
        $from_email = "From: {$Globals['adminemail']}";
        mail( $send_to, $subject, $letter, $from_email );
    }

    if ($Globals['ppostcount'] == "yes")
        inc_user_posts();

    if ($Globals['usenotify'] == "yes") {
        if ($notify == "yes") {
            $query = "SELECT id FROM photos WHERE userid=$userid AND bigimage='$realname'";
            $resulta = ppmysql_query($query,$link);
            list( $photoid ) = mysql_fetch_row($resulta);
            ppmysql_free_result($resulta);

            $query = "INSERT INTO notify (id,userid,photo) values(NULL,$userid,$photoid)";
            $resulta = ppmysql_query($query,$link);
        }
    }
    // end write ##
}

?>
