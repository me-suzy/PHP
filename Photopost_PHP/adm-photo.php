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
require "languages/$pplang/adm-photo.php";
require "login-inc.php";
require "image-inc.php";

authenticate();

if (empty($pdelete)) $pdelete="";

if ($ppaction == "manipulate") {
    if ( !is_numeric($pid) ) {
        diewell( $Globals['pp_lang']['malform'] );
        exit;
    }
    
    $query = "SELECT id,user,userid,cat,bigimage,medsize,watermarked FROM photos WHERE id=$pid";
    $queryv = ppmysql_query($query,$link);
    list( $id, $user, $tuserid, $cat, $bigimage, $medsize, $watermarked ) = mysql_fetch_row($queryv);

    $datadir = $Globals['datafull'];    
    if ( strstr($datadir, "/") )
        $newdir = $datadir."$cat/";
    else
        $newdir = $datadir."$cat\\";
    
    $theext = substr($bigimage, strlen($bigimage) - 4,4);
    $filename = $bigimage;
    $filename = str_replace( $theext, "", $filename);
    $thumblink = $newdir."$tuserid$filename-thumb$theext";
    $medimage = $newdir."$tuserid$filename-med$theext";    
    $imagelink = $newdir."$tuserid$bigimage";
    $bigimage = "$tuserid$bigimage";
    
    $imagewidth = 0; $imageheight = 0; $imagesize = 0;
    $medwidth = 0; $medheight = 0; $medimgsize = 0;
    
    unlink( $thumblink );
    
    if ( $Globals['usegd'] != 0 ) {
        $resize_worked = rotate_jpeg($filepath, $filepath);
    }
    else {
        if ( $dowhat == "rotateccw" ) $syscmd = $Globals['mogrify_command']." -rotate 270 $imagelink";
        elseif ( $dowhat == "rotatecw" ) $syscmd = $Globals['mogrify_command']." -rotate 90 $imagelink";
        elseif ( $dowhat == "flip" ) $syscmd = $Globals['mogrify_command']." -flip $imagelink";
        elseif ( $dowhat == "flop" ) $syscmd = $Globals['mogrify_command']." -flop $imagelink";        
        else {
            diewell( $Globals['pp_lang']['mogrify'] );
            exit;
        }

        // call ImageMagick mogrify to perform action
        system( $syscmd, $retval );
        
        if ( $retval != 0 ) {
            diewell("{$Globals['pp_lang']['erotate']}!<p>{$Globals['pp_lang']['errorcode']}: $retval<br />$syscmd");
            exit;
        }
        
        $image_stats = getimagesize( $imagelink );
        $imagewidth = $image_stats[0];
        $imageheight = $image_stats[1];
        $imagesize = filesize( $imagelink );
        
        if ( $medsize > 0 ) {
            if ( $dowhat == "rotateccw" ) $syscmd = $Globals['mogrify_command']." -rotate 270 $medimage";
            elseif ( $dowhat == "rotatecw" ) $syscmd = $Globals['mogrify_command']." -rotate 90 $medimage";
            elseif ( $dowhat == "flip" ) $syscmd = $Globals['mogrify_command']." -flip $medimage";
            elseif ( $dowhat == "flop" ) $syscmd = $Globals['mogrify_command']." -flop $medimage";                    
            else {
                diewell( $Globals['pp_lang']['mogrify'] );
                exit;
            }
    
            // call ImageMagick mogrify to perform action
            system( $syscmd, $retval );
            
            if ( $retval != 0 ) {
                diewell( "{$Globals['pp_lang']['erotatemed']}<p>{$Globals['pp_lang']['errorcode']}: $retval<br />$syscmd");
                exit;
            }
            
            $image_stats = getimagesize( $medimage );
            $medwidth = $image_stats[0];
            $medheight = $image_stats[1]; 
            $medimgsize = filesize( $medimage );
        }
    }

    $holduserid = $userid;
    $userid = $tuserid;    
    $thumbsize = create_thumb( $bigimage, $imagelink, $cat, "rebuildthumbnail" );
    $userid = $holduserid;
    
    $query = "UPDATE photos SET width=$imagewidth, height=$imageheight, filesize=$imagesize, medwidth=$medwidth, medheight=$medheight, medsize=$medimgsize WHERE id=$pid";
    $resulta = ppmysql_query($query, $link);
    
    $furl = "{$Globals['maindir']}/editphoto.php?phoedit=$pid";
    
    if ( $dowhat == "rotateccw" ) $msg = $Globals['pp_lang']['rotateccw'];
    elseif ( $dowhat == "rotatecw" ) $msg = $Globals['pp_lang']['rotatecw'];
    elseif ( $dowhat == "flip" ) $msg = $Globals['pp_lang']['flipped'];
    elseif ( $dowhat == "flop" ) $msg = $Globals['pp_lang']['flopped'];    
    
    forward($furl, $msg);
    exit;
}

if ($ppaction == "movedel") {
    if ($pdelete == "yes") {
        if ( $pid == "") {
            diewell( $Globals['pp_lang']['nopic'] );
            exit;
        }
        
        if ( !is_numeric($pid) ) {
            diewell( $Globals['pp_lang']['malform'] );
            exit;
        }

        $query = "SELECT userid,cat,bigimage,medsize,title FROM photos WHERE id=$pid";
        $resulta = ppmysql_query($query,$link);

        if ( !$resulta ) {
            diewell( $Globals['pp_lang']['nophoto'] );
            exit;
        }

        list( $puserid, $thecat, $filename, $medsize, $ptitle ) = mysql_fetch_row($resulta);
        ppmysql_free_result( $resulta );

        if ( ($userid == $puserid && $Globals['userdel'] == "yes") || $adminedit == 1 ) {
            if ( $filename != "" ) remove_all_files( $filename, $medsize, $puserid, $thecat );

            $query = "DELETE FROM photos WHERE id=$pid";
            $resulta = ppmysql_query($query,$link);
            
            $query = "DELETE FROM exif WHERE photoid=$pid";
            $resulta = ppmysql_query($query,$link);            

            $query = "DELETE FROM comments WHERE photo=$pid";
            $resulta = ppmysql_query($query,$link);

            $query = "DELETE FROM notify WHERE photo=$pid";
            $resulta = ppmysql_query($query,$link);

            if ($Globals['ppostcount'] == "yes") {
                inc_user_posts( "minus", $puserid );
            }
            
            upgradecategories(0);

            $adesc = $Globals['pp_lang']['delete'];
            $furl = "{$Globals['maindir']}/showgallery.php?cat=$thecat&thumb=1";

            if ( $Globals['useemail'] == "yes" && ($adminedit == 1 && $userid != $puserid) ) admin_email( 'delete', $pid, $puserid, $ptitle );

            forward($furl, $adesc);
            exit;
        }
        else {
            diewell( $Globals['pp_lang']['noperm'] );
            exit;
        }
    }

    if ($catmove != "") {
        if ( $catmove == "notcat" ) {
            diewell( $Globals['pp_lang']['badmove'] );
            exit;
        }

        if ( $origcat != $catmove && !empty($catmove) ) {
            move_image_cat( $pid, $catmove );
        }
        else {
            $furl = "{$Globals['maindir']}/showphoto.php?photo=$pid";
            forward($furl, $Globals['pp_lang']['noneed']);
            exit;
        }
    }
}

diewell( $Globals['pp_lang']['noaction'] );
exit;

?>

