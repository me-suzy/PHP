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
function create_thumb( $realname, $filepath, $thecat ) {
    global $Globals, $userid, $imagewidth, $imageheight, $thumbnail, $resizeorig;

    //
    // NEW RESIZE CODE
    //
    $basedir = $Globals{'datafull'};    
    $previewwidth = $Globals{'previewwidth'};

    $image_stats = getimagesize( $filepath );
    $imagewidth = $image_stats[0];
    $imageheight = $image_stats[1];
    $img_type = $image_stats[2];

    // Create thumbnails
    $filenoext = get_filename( $realname );
    $theext    = get_ext( $realname );
    $thumbnail = "$userid".$filenoext."-thumb.$theext";
    $outthumb = "$basedir$thecat/$thumbnail";

    if ( !file_exists( $outthumb ) )  {
        copy ( $filepath, $outthumb );
        // if image is taller than wider, then portrait
        if ( $imageheight < $imagewidth ) {
            $scaleFactor = $imagewidth / $previewwidth;
            $newheight = round( $imageheight / $scaleFactor );

            //$previewwidth = $previewwidth
            $syscmd = $Globals{'mogrify_command'}." -format $theext -border 2x2 -bordercolor black -geometry ".$previewwidth."x".$newheight." $outthumb";
        }
        else {
            $scaleFactor = $imageheight / $previewwidth;
            $newwidth = round( $imagewidth / $scaleFactor );
            //$previewheight = $previewwidth
            $syscmd = $Globals{'mogrify_command'}." -format $theext -border 2x2 -bordercolor black -geometry ".$newwidth."x".$previewwidth." $outthumb";
        }

        // call ImageMagick mogrify to create the thumbnail
        system( $syscmd, $retval );
        if ( $retval != 0 ) {
            dieWell("Error creating thumbnail! Error code: $retval<p>Command: $syscmd");
            unlink( $outthumb );
            unlink( $filepath );
            exit;
        }
    }

    $imagesize = filesize( $outthumb );

    return( $imagesize );
}

function process_image( $realname, $filepath, $thecat ) {
    global $Globals, $userid, $link, $db_link;
    global $username, $usergroup, $title, $desc, $keywords;
    global $adminexclude, $keywords, $notify, $resizeorig;

    $uganno = array(0);

    list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();
    $mon = $mon + 1;
    $julian = mktime($hour,$min,$sec,$mon,$mday,$year);

    $query = "SELECT id,ugnoanno FROM categories";
    $resultb = mysql_query_eval($query,$link);

    while ( $row = mysql_fetch_array($resultb, MYSQL_ASSOC) ) {
        $catugid = $row['id']; $ugnoanno = $row['ugnoanno'];

        $allnoanno = explode( ",", $ugnoanno );
        foreach ($allnoanno as $key) {
            if ($usergroup == $key) {
                $uganno{$catugid}=1;
            }
            else
                $uganno{$catugid}=0;
        }
    }
    mysql_free_result($resultb);

    $filenoext = get_filename( $realname );
    $theext    = get_ext( $realname );
    $outfilename = "$userid$realname";
    $basedir = $Globals{'datafull'};

    $image_stats = getimagesize( $filepath );
    $imagewidth = $image_stats[0];
    $imageheight = $image_stats[1];
    $imagesize = filesize( $filepath );

    $resizeorig = 0;
    $maxwidth = $Globals{'maxwidth'};
    $maxheight = $Globals{'maxheight'};
    
    if ( $imagewidth > $maxwidth ) {
        if ( $Globals{'resizeorig'} == "yes" ) {
            $resizeorig = 1;
        }
        else {
            dieWell("Your graphic is too wide!  Please upload a smaller one.");
            unlink($realname);
            exit;
        }
    }

    if ( $imageheight > $maxheight ) {
        if ( $Globals{'resizeorig'} == "yes") {
            $resizeorig = 1;
        }
        else {
            dieWell("Your graphic is too tall!  Please upload a smaller one.");
            unlink($realname);
            exit;
        }
    }
        
    //##// resize original and/or annotate original ###
    if ( $uganno{$thecat} != 1 && $Globals{'annotate'} == "yes") {
         // stamp the image
         $agravity = $Globals{'gravity'};
         $aopacity = $Globals{'opacity'};
         $water_image = $Globals{'watermark'};

         // need to execute this command after images:
         // composite -compose multiply -gravity southeast eblogo.jpg jess.jpg jesslogo.jpg

         $composite_cmd = str_replace( "mogrify", "composite", $Globals{'mogrify_command'} );
         $stampcmd = $composite_cmd." -compose multiply -gravity $agravity $water_image $filepath $filepath";
         system( $stampcmd, $retval );

         if ( $retval != 0 ) {
             dieWell("Error creating watermarked original! Error code: $retval<br><br>Command: $stampcmd");
             unlink( $filepath );
             exit;
         }
    }

    if ( $resizeorig == 1 ) {
        // if image is taller than wider, then portrait
        if ( $imagewidth > $maxwidth ) {
            $scaleFactor = $imagewidth / $maxwidth;
            $newheight = round( $imageheight / $scaleFactor );
            $syscmd = $Globals{'mogrify_command'}." -format $theext -border 2x2 -bordercolor black -geometry ".$maxwidth."x".$newheight." $filepath";
        }
        else {
            $scaleFactor = $imageheight / $maxheight;
            $newwidth = round( $imagewidth / $scaleFactor );
            $syscmd = $Globals{'mogrify_command'}." -format $theext -border 2x2 -bordercolor black -geometry ".$newwidth."x".$maxheight." $filepath";
        }

        // call ImageMagick mogrify to resize the original down
        system( $syscmd, $retval );
        if ( $retval != 0 ) {
            dieWell("Error creating resized original! Error code: $retval");
            unlink( $outthumb );
            unlink( $filepath );
            exit;
        }
        
        $image_stats = getimagesize( $filepath );
        $imagewidth = $image_stats[0];
        $imageheight = $image_stats[1];
        $imagesize = filesize( $filepath );        
    }
    //##// end resize original and/or annotate original ###

    //##// create a medium sized graphic if the graphic is too big ###
    $createmed = 0;
    $biggraphic = $Globals{'biggraphic'};

    if ( $Globals{'bigsave'} == "yes" ) {
        if ( $imagewidth > $biggraphic || $imageheight > $biggraphic ) $createmed = 1;
    }

    if ( $createmed == 1 ) {
        $medium = $filenoext."-med.$theext";
        $medfile="$basedir$thecat/$userid$medium";
        copy ( $filepath, $medfile );

        if ( $imageheight > $imagewidth ) {
            $scaleFactor = $imagewidth / $biggraphic;
            $medwidth = round( $imagewidth / $scaleFactor );
            $medheight = $biggraphic;
            $syscmd = $Globals{'mogrify_command'}." -format $theext -border 2x2 -bordercolor black -geometry ".$medwidth."x".$medheight." $medfile";
        }
        else {
            $scaleFactor = $imageheight / $biggraphic;
            $medheight = round( $imageheight / $scaleFactor );
            $medwidth = $biggraphic;
            $syscmd = $Globals{'mogrify_command'}." -format $theext -border 2x2 -bordercolor black -geometry ".$medwidth."x".$medheight." $medfile";
        }
        
        // call ImageMagick mogrify to create the medium image
        system( $syscmd, $retval );
        if ( $retval != 0 ) {
            dieWell("Error creating resized medium image! Error code: $retval<br>Command attempted: $syscmd");
            unlink( $outthumb );
            unlink( $filepath );
            unlink( $medium );
            exit;
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

    if ( $Globals{'moderation'} == "yes" && $adminexclude != 1 ) $moderate = "0";
    else $moderate = "1";

    $username = addslashes( $username );
    $title = addslashes( $title );
    $desc = addslashes( $desc );
    $keywords = addslashes( $keywords );

    $query = "INSERT INTO photos values(NULL,'$username', $userid, $thecat, $julian, '$title', '$desc', '$keywords', '$realname', $imagewidth, $imageheight, $imagesize, '0', $medwidth, $medheight, $medsize, $moderate, $julian, '0')";
    $resulta = mysql_query_eval($query, $link);
    
    if ( !$resulta ) {
        dieWell( "Database error! Please report to System Administrator." );
        exit;
    }

    if ( $Globals{'uploadnotify'} == "yes" ) {
        $letter="
            $username has uploaded one or more photos to your gallery.
            If approval is required, visit the admin panel:

            Image name: $realname
            Title: $title
            Size: $imagesize
            Keywords: $keywords
            Description: $desc

            Link to image: ".$Globals{'datadir'}."/$thecat/$outfilename

            ".$Globals{'maindir'}."/adm-index.php";

        $subject = $Globals{'webname'}." photo upload(s)";
        $send_to = $Globals{'adminemail'};
        $from_email = "From: ".$Globals{'adminemail'};
        mail( $send_to, $subject, $letter, $from_email );
    }

    if ($Globals{'ppostcount'} == "yes") 
        inc_user_posts();

    if ($Globals{'usenotify'} == "yes") {
        if ($notify == "yes") {
            $query = "SELECT id FROM photos WHERE userid=$userid AND bigimage='$realname'";
            $resulta = mysql_query_eval($query,$link);
            $row = mysql_fetch_array($resulta);
            $photoid = $row['id'];
            mysql_free_result($resulta);

            $query = "INSERT INTO notify values(NULL,$userid,$photoid)";
            $resulta = mysql_query_eval($query,$link);
        }
    }
    // end write ##
}

?>
