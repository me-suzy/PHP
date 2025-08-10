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

// because this script can take a long time, we need to disable compression
$disableforadmin=1;

require "adm-inc.php";
require "image-inc.php"; 

if ( !isset($okay) ) $okay = "no";
if ( !isset($ppaction) ) $ppaction = "";

if ( isset($watermark) ) $watermark = "yes";
else $watermark = "no";

if ( isset($counts) ) $counts = "yes";
else $counts = "no";

if ( isset($thumbs) ) $thumbs = "yes";
else $thumbs = "no";

if ( isset($scanexif) ) $scanexif = "yes";
else $scanexif = "no";

if ( isset($allthumbs) ) $allthumbs = "yes";
else $allthumbs = "no";

if ($okay != "yes") {
    printheader( 0, "PhotoPost Database Scan" );
    
    $output = "<center>
        <hr><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\">
        <font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><b>PhotoPost Refresh Usergroups</font>
        </td>
        </tr><tr>
        <td bgcolor=\"{$Globals['headcolor']}\"><b>
        <font face=\"{$Globals['mainfonts']}\" size=\"{$Globals['fontmedium']}\" color=\"{$Globals['headfontcolor']}\">$adminmenu</b></font></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\"><center><br />
        <font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">Any work on your database and directory should involve a backup.<br />
        Many actions performed here cannot be undone (watermarking especially).

        <form action=\"{$Globals['maindir']}/adm-db.php\" method=\"POST\">
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"1\">        
        <tr><td align=\"center\" width=\"50\">
        <font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\" size=\"{$Globals['fontmedium']}\">Options</font></td>
        <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\" size=\"{$Globals['fontmedium']}\">Description<br />
        </font></td></tr>
        <tr>
        <td align=\"center\" width=\"50\">
        <input type=\"checkbox\" value=\"0\" name=\"thumbs\"></td>
        <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\" size=\"{$Globals['fontmedium']}\">Check for and create missing thumbnails.<br /></font></td>
        </tr>
        <tr>
        <td align=\"center\" width=\"50\">
        <input type=\"checkbox\" value=\"0\" name=\"allthumbs\"></td>
        <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\" size=\"{$Globals['fontmedium']}\">Rebuild all thumbnails.<br /></font></td>
        </tr>                
        <tr>
        <td align=\"center\" width=\"50\">
        <input type=\"checkbox\" value=\"0\" name=\"watermark\"></td>
        <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\" size=\"{$Globals['fontmedium']}\">Check here to watermark unwatermarked files.<br />
        <b>This action cannot be undone - backup your data directory and photos database!</b></font></td>
        </tr>
        <tr>
        <td align=\"center\" width=\"50\">
        <input type=\"checkbox\" value=\"0\" name=\"counts\"></td>
        <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\" size=\"{$Globals['fontmedium']}\">Check here to recalculate photos/posts numbers and check children.<br /></font></td>
        </tr>
        <tr>
        <td align=\"center\" width=\"50\">
        <input type=\"checkbox\" value=\"0\" name=\"scanexif\"></td>
        <td align=\"center\"><font face=\"{$Globals['mainfonts']}\" color=\"{$Globals['maintext']}\" size=\"{$Globals['fontmedium']}\">Check here to rescan photos and save EXIF information.<br /></font></td>
        </tr>        
        
        </table><p>
        <input type=\"hidden\" name=\"okay\" value=\"yes\">
        <input type=\"hidden\" name=\"ppaction\" value=\"scandb\">
        <input type=\"submit\" value=\"Perform Tasks\"></form></td></tr></table></td></tr></table>";

    print "$output<p>{$Globals['cright']}<p>";
    printfooter();
    
    exit;
}

if ( $ppaction == "scandb" ) {        
    print "$adminmenu<br /><br />Processing may take a while... please be patient and wait the for FINISHED message...<br /><br />";
    
    $datadir = $Globals['datafull'];
    
    $query = "SELECT id,user,userid,cat,bigimage,medsize,watermarked FROM photos";
    $queryv = ppmysql_query($query, $link);
        
    while ( list( $id, $user, $tuserid, $cat, $bigimage, $medsize, $watermarked ) = mysql_fetch_row($queryv) ) {
        if ( strstr($datadir, "/") )
            $newdir = $datadir."$cat/";
        else
            $newdir = $datadir."$cat\\";
        
        $theext = substr($bigimage, strlen($bigimage) - 4,4);
        $filename = $bigimage;
        $filename = str_replace( $theext, "", $filename);
        $newthumblink = $newdir."$tuserid$filename-thumb$theext";
        $imagelink = $newdir."$tuserid$bigimage";
        $bigimage = "$tuserid$bigimage";
        
        $fullsize = @filesize( $imagelink );
        $thumbsize = @filesize( $newthumblink );
        
        $redo = 0;
        if ( $fullsize == $thumbsize ) $redo = 1;
        
        if ( $thumbs == "yes" || $allthumbs == "yes" ) {
            // if rebuilding all thumbs, remove them to force a new one
            if ( $allthumbs == "yes" )
                unlink( $newthumblink );
            
            if ( !file_exists( $newthumblink ) || $redo == 1 ) {
                if ( !is_multimedia($newthumblink) ) {
                    if ( file_exists( $imagelink ) ) {
                        if ( $redo == 1 ) {
                            print "Thumbnail not sized: $newthumblink<br />Creating from $imagelink ... ";
                            @unlink( $newthumblink );
                        }
                        else
                            print "Rebuilding thumbnail: $newthumblink<br />Creating from $imagelink ... ";
                        
                        // create thumbnail
                        $holduserid = $userid;
                        $userid = $tuserid;
                        $thumbsize = create_thumb( $bigimage, $imagelink, $cat, "rebuildthumbnail" );
                        
                        if ( !file_exists( $newthumblink ) ) {
                            print "<b>failed!</b>";
                        }
                        else {
                            if ( $redo == 1 ) {
                                $fullsize = @filesize( $imagelink );
                                $thumbsize = @filesize( $newthumblink );
                                
                                if ( $fullsize == $thumbsize ) print "failed resize!";
                                else print "completed!";
                            }
                            else {
                                print "completed!";
                            }
                        }
                        print "<br /><br />";
                        $userid = $holduserid;
                    }
                    else {
                        print "Removing database entry with no image: $imagelink<br />";
                        $queryd = "DELETE FROM photos where id=$id";
                        $querydr = ppmysql_query($queryd, $link);                
                    }
                }
            }
        }
        
        // watermarks?
        if ( $watermark == "yes" && $watermarked == "no" ) {
            if ( is_image($imagelink) ) {
                print "watermarking: $imagelink<br />";
                watermark( $imagelink, 1 );
                $medwater = $newdir."$tuserid$filename-med$theext";
                if ( file_exists( $medwater ) ) {
                    print "watermarking ($id): $medwater<br />";
                    watermark( $medwater, 1 );
                }
                $watermarked = "yes";
            }
        }
        
        if ( $scanexif == "yes" ) {
            $exifinfo = "";
            $exifinfo = readexifinfo( $imagelink, $filename );
            if ( $exifinfo ) {
                if ( count($exifinfo) > 6 ) {
                    $storeexif = addslashes(serialize($exifinfo));
                    print "exif info added for image $id<br />";                    
                    $query3 = "REPLACE INTO exif values( $id, '$storeexif' )";
                    $resultc = ppmysql_query($query3, $link);
                }
            }            
        }
        
        // now lets make sure we have the right username for the photo
        list( $tname, $tmail ) = get_username($tuserid);
        $queryi = "UPDATE photos SET user='$tname', watermarked='$watermarked' where id=$id";
        $queryid = ppmysql_query($queryi, $link);
        
        @flush();
    }
    ppmysql_free_result( $queryv );
    
    if ( $counts == "yes" ) {
        print "Preparing to update categories with photo and posts information...<br />";
        upgradecategories(0);
        
        print "<p>Preparing to update personal albums with photo and posts information...<br />";
        upgradealbums(0);
        @flush();        
    }

    print "<br /><b>Finished!<br /><br /></b></center></td></tr></table></td></tr></table><p>";
    print $Globals['cright'];
}

?>
