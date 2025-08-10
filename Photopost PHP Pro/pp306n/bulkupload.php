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

$gologin=0; $nopost=0;
authenticate();

if ( $Globals{'adminnolimit'} == "yes" ) {
    if ( $adminedit  == 1) $nolimit = 1;
    else $nolimit = 0;
}
else
    $nolimit = 0;

if ( $Globals{'adminexclude'} == "yes" ) {
    if ( $adminedit == 1 ) $adminexclude = 1;
    else $adminexclude = 0;
}
else
    $adminexclude = 0;

if ( $adminedit == 0 ) {
    if ( $Globals{'allowup'} == "no" ) 
        dieWell( "User uploads not allowed" );
}

$qenv = findenv( "QUERY_STRING" );
if ( ($Globals{'unregpho'} != "yes" && $gologin==1) || $qenv == "gologin" ) {
    $furl=$Globals{'maindir'};
    $furl= str_replace( $Globals{'domain'}, "", $furl );
    $furl="$furl/uploadform.php";
    login($furl);
    exit;
}

if ( $gologin != 1 ) {
    if ( $nopost == 1 ) {
        dieWell("Sorry, you don't have permission to post, or if you tried to edit, you might not be the post's author.");
        exit;
    }
    if ( $userup == 2 ) {
        dieWell("Sorry, you don't have permission to upload photos.");
    }
}

topmenu();

if ( $ppaction == "addphotos" ) {
    if ($do == "process") {

        $totalphotos=$thecount;
        
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
 
            if ( $origcat != $category ) {
                move_image( $origcat, $category, $tuserid, $imgname );
            }
                       
            if ($addkey == 1) {
                //print "Add: $inpath,$imgname<Br>";
                $imgname = ereg_replace( "[^a-zA-Z0-9/\:.]", "", $imgname );
                $filepath = $Globals{'datafull'}."$category/$photopath$imgname";
                copy( $filein, $filepath );
                
                // Open image, write out thumb, fullsize, and medium as needed
                
                process_image( $imgname, $filepath, $category );

                // Delete thumb and image from temp dir
                if ( file_exists( $filein ) ) 
                    unlink ($filein);
            }
            else {
                // Delete the image and thumb from temp dir
                //print "Del: $imgname<p>";
                if ( file_exists( $filein ) ) 
                    unlink ($filein);
                
                $filenoext = get_filename( $imgname );       
                $theext    = get_ext( $imgname );
                $thumbnail = "$userid".$filenoext."-thumb.$theext";
                $tfile = $Globals{'datafull'}."$category/$thumbnail";
                
                if ( file_exists( $tfile ) ) 
                    unlink ($tfile);
            }
        }
        $forward_url = $Globals{'maindir'}."/bulkupload.php?ppaction=addphotos&do=preview&thecount=$totalphotos&photopath=$userid&deftitle=$deftitle&defdesc=$defdesc&defcat=$defcat&keywords=$keywords&numprocess=$numprocess&dthumbs=$dthumbs&furl=$furl";
        forward( $forward_url, "Processing image list!" );
        exit;
    }

    catmoveopt(0);

    $output = "$header<center><hr>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"4\"
        face=\"verdana\">PhotoPost Add Photos</font>
        </font></td>
        </tr>
        <tr id=\"cat\">
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$menu</b></font></td></tr>
        <form action=\"".$Globals{'maindir'}."/bulkupload.php\" method=\"POST\">";

    if ( $do == "preview" ) {  // Get dir listing, thumbs, w/checkboxes
        $middle = "<tr><td bgcolor=\"".$Globals{'maincolor'}."\"><center><Br>
            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"><tr><Td>
            <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\"><tr><Th colspan=\"4\" bgcolor=\"#FFFFFF\"><font face=\"verdana\"
            size=\"2\">Add Photos - Select Photos / Configure Properties</th></tr>
            <Tr><Td colspan=\"4\" width=\"100%\" height=\"2\" bgcolor=\"#000000\"></td></tr>
            <Tr>
            <th bgcolor=\"#FFFFFF\"><font face=\"verdana\" size=\"2\">Image</font></th>
            <th bgcolor=\"#FFFFFF\"><font face=\"verdana\" size=\"2\">Add?</font></th>
            <th bgcolor=\"#FFFFFF\"><font face=\"verdana\" size=\"2\">Category</font></th>
            <th bgcolor=\"#FFFFFF\"><font face=\"verdana\" size=\"2\">Optional Text</font></th></tr>";

        $photocount=0;
        $userid = $photopath;
        $inpath = $Globals{'zipuploaddir'}."/$userid"; 
        $openpath = $inpath;
        $inpath = str_replace( "/+$", "", $inpath);
        $openpath = str_replace( "/+$", "", $openpath);
        //$openpath = "ls -l $openpath";
        $imgurl = str_replace( "/+$", "", $furl);
        $maxp = $numprocess;

        $querya="SELECT catname FROM categories WHERE id=$defcat";
        $catq = mysql_query_eval($querya,$link);
        $catr = mysql_fetch_array($catq);
        $defcatname = $catr['catname'];
        
        catmoveopt(0);
        
        if ( $handle = opendir( $openpath ) ) {
            while ( $realname = readdir( $handle ) ) {
                if (( $realname != ".") && ( $realname != ".." ) ) {

                    $filepath = $inpath."/$realname";
                    $chkrealname  = ereg_replace( "\\\\'", "_", $chkrealname );                    
                    $chkrealname = ereg_replace( "[^a-zA-Z0-9/\:.]", "", $realname );
                    $chkrealname = str_replace( "%20", "_", strtolower($chkrealname) );
                    
                    if ( $chkrealname != $realname ) {
                        $newfile = $inpath."/$chkrealname";
                        copy( $filepath, $newfile );
                        unlink ( $filepath );
                        $realname = $chkrealname;
                        $filepath = $newfile;
                    }
                    
                    $filepath = $inpath."/$realname";
                    $image_stats = getimagesize( $filepath );
                    $imagewidth = $image_stats[0];
                    $imageheight = $image_stats[1];
                    $type = $image_stats[2];
                    $size = filesize( $filepath );
                                
                    $theext = get_ext($realname);

                    $querya="SELECT id FROM photos WHERE userid=$userid AND bigimage='$realname'";
                    $catq = mysql_query_eval($querya,$link);
                    $imgchk = mysql_num_rows($catq);
    
                    if ( $imgchk != 0 ) {
                        if ( $dthumbs == "yes") {
                            create_thumb( $realname, $filepath, $defcat );
                            $thumb = "<A target=\"_blank\" href=\"".$Globals{'zipuploadurl'}."/$userid/$realname\"><img border=\"0\" src=\"".$Globals{'datadir'}."/$defcat/$thumbnail\"></a><Br>";
                        }
                        
                        $middle .= "<input type=\"hidden\" name=\"imgname".$photocount."\" value=\"$realname\">
                            <tr><Td bgcolor=\"#FFFFFF\"><font face=\"verdana\" size=\"1\"><b><center>$thumb$realname</center></td><Td
                            bgcolor=\"#FFFFFF\"><center><input type=\"hidden\" value=\"0\" name=\"add$photocount\"></center></td>
                            <Td bgcolor=\"#FFFFFF\"><select name=\"cat$photocount\" style=\"font-size: 9pt; background: FFFFFF;\">
                            <option value=\"$defcat\" selected>$defcatname</option>$catoptions</select>
                            <td bgcolor=\"#FFFFFF\"><Table width=\"95%\" cellpadding=\"0\" cellspacing=\"0\"><tr><Td><font face=\"verdana\"
                            size=\"1\">DUPLICATE FILE!</td><Td><input type=\"hidden\" size=\"30\" name=\"title$photocount\"
                            value=\"$deftitle\"
                            style=\"font-size: 9pt; background: FFFFFF;\"></td></tr><tr><Td>
                            <input type=\"hidden\" size=\"30\" name=\"desc$photocount\" value=\"$defdesc\" style=\"font-size:
                            9pt; background: FFFFFF;\"></td></tr></table></td></tr>";
                    }
                    elseif ( ($theext == "jpg" || $theext == "gif" || $theext == "png") && $photocount < $maxp && $size > 0) {
                        $photocount++;
                        //$fullname="$inpath/$name";
    
                        if ( $dthumbs == "yes") {
                            create_thumb( $realname, $filepath, $defcat );
                            $thumb = "<A target=\"_blank\" href=\"".$Globals{'zipuploadurl'}."/$userid/$realname\"><img border=\"0\" src=\"".$Globals{'datadir'}."/$defcat/$thumbnail\"></a><Br>";
                        }
                
                        $middle .= "<input type=\"hidden\" name=\"imgname".$photocount."\" value=\"$realname\">
                            <tr><Td bgcolor=\"#FFFFFF\"><font face=\"verdana\" size=\"1\"><b><center>$thumb$realname</center></td><Td
                            bgcolor=\"#FFFFFF\"><center><input type=\"checkbox\" CHECKED value=\"1\" name=\"add$photocount\"></center></td>
                            <Td bgcolor=\"#FFFFFF\"><select name=\"cat$photocount\" style=\"font-size: 9pt; background: FFFFFF;\">
                            <option value=\"$defcat\" selected>$defcatname</option>$catoptions</select>
                            <td bgcolor=\"#FFFFFF\"><Table width=\"95%\" cellpadding=\"0\" cellspacing=\"0\"><tr><Td><font face=\"verdana\"
                            size=\"1\">Title:</td><Td><input type=\"text\" size=\"30\" name=\"title$photocount\"
                            value=\"$deftitle\"
                            style=\"font-size: 9pt; background: FFFFFF;\"></td></tr><tr><Td><font face=\"verdana\" size=\"1\">Description:</td><Td>
                            <input type=\"text\" size=\"30\" name=\"desc$photocount\" value=\"$defdesc\" style=\"font-size:
                            9pt; background: FFFFFF;\"></td></tr></table></td></tr>";
                    }
                }
            }
        }
        
        if ($photocount == 0) {
            $middle .= "<Tr><Td colspan=\"4\" bgcolor=\"#FFFFFF\"><font face=\"verdana\" size=\"2\"><center>No more images found.<br><b><a href=\"".$Globals{'maindir'}."/index.php\">Click here</a> to return to main menu.</b></td></tr>";
        }
        
        $middle .= "<input type=\"hidden\" name=\"furl\" value=\"$imgurl\">
            <input type=\"hidden\" name=\"numprocess\" value=\"$maxp\">
            <input type=\"hidden\" name=\"dthumbs\" value=\"$dthumbs\">
            <input type=\"hidden\" name=\"deftitle\" value=\"$deftitle\">
            <input type=\"hidden\" name=\"defdesc\" value=\"$defdesc\">
            <input type=\"hidden\" name=\"defcat\" value=\"$defcat\">
            <input type=\"hidden\" name=\"keywords\" value=\"$keywords\">
            <input type=\"hidden\" name=\"photopath\" value=\"$userid\">
            <input type=\"hidden\" name=\"origcat\" value=\"$defcat\">
            <input type=\"hidden\" name=\"tuserid\" value=\"$userid\">                               
            <input type=\"hidden\" name=\"ppaction\" value=\"addphotos\">
            <input type=\"hidden\" name=\"do\" value=\"process\">
            <input name=\"thecount\" value=\"$photocount\" type=\"hidden\">
            <input name=\"inpath\" value=\"$inpath\" type=\"hidden\"><Tr><td bgcolor=\"#FFFFFF\" colspan=\"4\"><center><input
            type=\"submit\" value=\"Process\"></form>
            </td></tr></table></td></tr></table>";
    }
    else {
        $middle = "<input type=\"hidden\" name=\"ppaction\" value=\"addphotos\">
            <input type=\"hidden\" name=\"do\" value=\"preview\">
            <tr><td bgcolor=\"".$Globals{'maincolor'}."\"><center><Br>
            <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"><tr><Td>
            <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\"><tr><Th colspan=\"2\" bgcolor=\"#FFFFFF\"><font face=\"verdana\"
            size=\"2\">Upload ZIP file</th>
            <Tr><td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\"><b>Default Category for
            Photos</b></font><Br>
            <font size=\"1\" face=\"verdana\">(You can set each photo separately in the
            next screen)</font></td><td bgcolor=\"#FFFFFF\"><Select
            name=\"defcat\">$catoptions</select></td></tr>
            <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'maintext'}."\"><b>Default Title for
            Photos (can leave blank)</b></font><Br><font size=\"1\" face=\"verdana\" color=\"".$Globals{'maintext'}."\">(You can set each photo
            separately in the next screen)</font></td><Td bgcolor=\"#FFFFFF\"><input type=\"text\" name=\"deftitle\"
            size=\"40\"></td></tr>
            <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'maintext'}."\"><B>Default Description for
            Photos (can leave blank)</b></font><Br><font size=\"1\" face=\"verdana\" color=\"".$Globals{'maintext'}."\">(You can set each photo
            separately in the next screen)</font></td><Td bgcolor=\"#FFFFFF\"><input type=\"text\" name=\"defdesc\"
            size=\"40\"></td></tr>
            <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'maintext'}."\"><b>Display Thumbnail Images on Next
            Screen?</b></font></td><Td bgcolor=\"#FFFFFF\"><select name=\"dthumbs\"><option selected>yes</option><option>no</option></select>
            <input type=\"hidden\" name=\"photopath\" value=\"$userid\">
            <input type=\"hidden\" name=\"furl\" value=\"".$Globals{'zipuploadurl'}."/$userid\">
            </td></tr>

            <tr><Td bgcolor=\"#FFFFFF\"><font size=\"2\" face=\"verdana\" color=\"".$Globals{'maintext'}."\"><b>Max Number of Photos to Process at a
            Time</b></font><br> <font size=\"1\" face=\"verdana\" color=\"".$Globals{'maintext'}."\">ImageMagick must process each photo.  We
            recommended<Br>no more than 25 photos at a time, but you can experiment<Br>with what works best for you and your
            server.</font></td><Td bgcolor=\"#FFFFFF\"><select name=\"numprocess\"><option
            selected>10</option>
            <option>5</option>
            <option>20</option>
            <option>50</option><option>75</option><option>100</option></select></td></tr>

            <Tr><td bgcolor=\"#FFFFFF\" colspan=\"2\"><center><input type=\"submit\" value=\"Next\"></form>
            </td></tr>
            </table>
            </td></tr></table>";
    }

    $output .= "$middle</td></tr></table></td></tr></table>".$Globals{'cright'}."$footer";

    print $output;
    exit;
}

?>

