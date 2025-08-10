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
include("config-inc.php");

$magic = get_magic_quotes_gpc();

// --------------------------------
// Register the global variables
   if (is_array($HTTP_GET_VARS)) {
     while(list($key,$value) = each($HTTP_GET_VARS)) {
        if ($magic) {
           $value = stripslashes($value);
        }
        ${$key} = $value;
     }
   }
   if (is_array($HTTP_POST_VARS)) {
     while(list($key,$value) = each($HTTP_POST_VARS)) {
        if ($magic) {
           $value = stripslashes($value);
        }
        ${$key} = $value;
     }
   }
   if (is_array($HTTP_COOKIE_VARS)) {
     while(list($key,$value) = each($HTTP_COOKIE_VARS)) {
        if ($magic) {
           $value = stripslashes($value);
        }
        ${$key} = $value;
     }
   }

// ---------------------------
// Turn off the magic quoting
   set_magic_quotes_runtime(0);


//===========================================================

// Connecting, selecting database
$link = mysql_connect("$host:3306", "$mysql_user", "$mysql_password") or die ('I cannot connect to the PhotoPost database. [$php_errormsg]');
mysql_select_db ("$database",$link)or die("Could not connect to PhotoPost database". mysql_error() );

$db_link = mysql_connect("$host_bb", "$user_bb", "$password_bb") or die ('I cannot connect to the Members database. [$php_errormsg]');
mysql_select_db ("$database_bb",$db_link)or die("Could not connect to User database". mysql_error() );

$query="SELECT varname,setting FROM settings";
$getsets = mysql_query($query,$link);
while ( $setrows = mysql_fetch_array($getsets, MYSQL_ASSOC)) {
    $var = $setrows['varname'];
    $set = $setrows['setting'];
    $Globals[$var] = stripslashes($set);
}
mysql_free_result($getsets);

// These variables come from the config-inc.php, placed here so global
$Globals{'zip_command'} = $zip_command;
$Globals{'mogrify_command'} = $mogrify_command;
$Globals{'cookie_path'} = $cookie_path;
$Globals{'debug'} = $debug;
$Globals{'botbuster'} = $botbuster;

// need to fix a couple variables to prevent problems
$Globals{'maindir'} = trim( $Globals{'maindir'} );

// overrides; this is mostly for testing purposes
if ( file_exists("globals-over.inc") ) {
    $filearray = file( "globals-over.inc" );

    while ( list($num, $line) = each($filearray) ) {
        if ($line != "") {
            $vars = explode( "=", $line);
            $var = $vars[0]; $set = $vars[1];
            $Globals[$var] = trim($set);
        }
    }
}

// If they want compression, enable it!
$zlibdebug = "Zlib compression disabled.";
if ( $compression == "1" ) {
    $phpa = phpversion();
    $phpv = $phpa[0] . "." . $phpa[2] . $phpa[4];
    if (($phpv > 4.0004) && extension_loaded("zlib") && !ini_get("zlib.output_compression") && !ini_get("output_handler")) {
        ob_start("ob_gzhandler");
        $zlibdebug = "Zlib compression enabled.";
    }
}

$headeropen=$Globals{'header'};
$footeropen=$Globals{'footer'};
$headtagsopen=$Globals{'headtags'};

// Read in the header tags file
if ( file_exists($headtagsopen) ) {
    $filearray = file($headtagsopen);
    $headtags = implode( " ", $filearray );
}
else
    $headtags = "";

// read in the header (or vbheader file)
if ( !empty($vbheader) ) {
        $theader = $vbheader;
        $theader = str_replace( "{tablewidth}", $Globals{'tablewidth'}, $theader);
        $theader = str_replace( "{pagebgcolor}", $Globals{'forwardbody'}, $theader);
}
elseif ( file_exists($headeropen) ) {
    $filearray = file($headeropen);
    $theader = implode( " ", $filearray );
}
else
    $theader="";

// read in the footer (or the vbfooter file)
if ( !empty($vbfooter) ) {
        $footer = $vbfooter;
}
elseif ( file_exists($footeropen) ) {
    $filearray = file($footeropen);
    $footer = implode( " ", $filearray );
}
else
    $footer="";

if ( $Globals{'cjurl'} != "" ) {
    if ( $Globals{'cjurl'} != "http://www.qksrv.net" ) {
        $Globals{'cright'} = str_replace( "$HTTP_SERVER_VARS[HTTP_HOST]", $Globals{'cjurl'}, $Globals{'cright'} );
    }
}

$Globals{'cright'} = str_replace( "--replaceme--", "PHP 3.0.6", $Globals{'cright'} );

$nocachetag = "<!-- no cache headers -->
        <meta http-equiv=\"Pragma\" content=\"no-cache\">
        <meta http-equiv=\"no-cache\">
        <meta http-equiv=\"Expires\" content=\"-1\">
        <meta http-equiv=\"Cache-Control\" content=\"no-cache\">
        <!-- end no cache headers -->";

$header="<head><title>".$Globals{'galleryname'}."</title>$nocachetag$headtags</head>$theader";

$username=""; $userid=""; $menu=""; $posternav="";

// handler to hand all mysql_queries
function mysql_query_eval( $query, $database ) {
    global $Globals;

    $mysql_eval_error="";
    $mysql_eval_result = mysql_query($query, $database) or $mysql_eval_error = mysql_error();
    if ($mysql_eval_error) {
        if ( $Globals{'debug'} == 1 ) {
            $letter = "An error was encountered during execution of the query:\n\n";
            $letter .= $query."\n\n";
            $letter .="The query returned with an errorcode of: \n\n$mysql_eval_error\n\n";
            $letter .= "To turn off these emails, set \$debug=0 in your config-inc.php file.";

            $email = $Globals{'adminemail'};
            $email_from = "From: ".$Globals{'adminemail'};

            $subject="Subject: ".$Globals{'webname'}." MySQL Error Report";
            $subject=trim($subject);

            mail( $email, $subject, $letter, $email_from );
        }
        elseif ( $Globals{'debug'} == 2 ) {
            dieWell( "MySQL error reported!<p>Query: $query<p>Result: $mysql_eval_error<p>Database handle: $database" );
            exit;
        }
        return FALSE;
    }
    else {
        return $mysql_eval_result;
    }
}

function get_ext( $filename ) {
    $photolen = strlen($filename);
    $RetVal = substr( $filename, $photolen-3, $photolen);

    return $RetVal;
}

function get_filename($filename) {
    // strip off the last 4
    $len = strlen( $filename )-4;
    $RetVal = substr( $filename, 0, $len);
    return $RetVal;
}

function display_gallery($q_switch, $showuser="", $showcat="") {
    // thanks to matfz for cleanup up empty cells
    global $Globals, $link, $output, $cat, $exclude_cat, $si;

    $rcols=$Globals['recentcols'];
    $rphotos=$Globals['recentnum'];
    $catquery="";

    if ($rcols < 0) $rcols=4;
    if ($rcols > 100) $rcols=4;
    if ($rphotos < 0) $rphotos=12;
    if ($rphotos > 500) $rphotos=12;

    $twidth = intval(100/$rcols);

    if ( $cat < 3000 ) 
        $querya="SELECT catname FROM categories WHERE id='$cat'";
    else
        $querya="SELECT albumname FROM useralbums WHERE id='$cat'";
            
    $catq = mysql_query_eval($querya,$link);
    $catr = mysql_fetch_row($catq);
    list( $catname ) = $catr;

    if ( $cat == "500" ) {
        if ( $si == "" ) {
            $catquery = "500";
        }
        else {
            $queryb = "SELECT id FROM categories ORDER BY catorder ASC";
            $boards = mysql_query_eval($queryb,$link);
            $cnt=0;
            while ( $row = mysql_fetch_array($boards, MYSQL_ASSOC)) {
                $catqid = $row['id'];
                if ( $cnt == 0 ) {
                    $cnt=1;
                    $catquery .= "$catqid";
                }
                else {
                    $catquery .= ",$catqid";
                }
            }
            mysql_free_result($boards);
        }
    }
    else {
        if ( $showcat > 3000 ) {
            $catquery="$showcat";
        }
        else {
            $catquery = "$cat";
            
            $queryb = "SELECT id FROM categories WHERE parent='$cat' ORDER BY catorder ASC";
            $boards = mysql_query_eval($queryb,$link);
            
            while ( $row = mysql_fetch_row($boards)) {
                list( $catqid ) = $row;
                $catquery .= ",$catqid";
            }
        }
    }

    $userq = "";
    if ( $showuser != "" ) {
        $userq = " AND userid=$showuser";
        
        list( $tcat, $tmail ) = get_username($showuser);
        $catname = "$tcat's $catname";
    }
    
    switch ($q_switch) {
        case "most_views":
            if ( !IsSet($cat) ) {
                $group_title = "Most Popular Images - All Categories";
                $query = "SELECT id,user,userid,cat,title,bigimage,views FROM photos WHERE bigimage!='' AND approved='1' $exclude_cat ORDER BY views DESC LIMIT $rphotos";
                break;
            }
            else {
                $group_title = "Most Popular Images - $catname";
                $query = "SELECT id,user,userid,cat,title,bigimage,views FROM photos WHERE bigimage!='' AND approved='1' $userq $exclude_cat AND cat in ($catquery) ORDER BY views DESC LIMIT $rphotos";
            }
            break;
            
        case "latest":
            if ( !IsSet($cat) ) {
                $group_title = "Most Recent Images - All Categories";
                $query = "SELECT id,user,userid,cat,title,bigimage,views FROM photos WHERE bigimage!='' AND approved='1' $exclude_cat ORDER BY date DESC LIMIT $rphotos";
                break;
            }
            else {
                $query = "SELECT id,user,userid,cat,title,bigimage,views FROM photos WHERE bigimage!='' AND approved='1' $userq $exclude_cat AND cat in ($catquery) ORDER BY date DESC LIMIT $rphotos";
                $group_title = "Most Recent Images - $catname";                
                break;
            }
            break;
            
        default:
            if ( !IsSet($cat) ) {
                $group_title = "Random Images - All Categories";
                $query = "SELECT id,user,userid,cat,title,bigimage,views FROM photos WHERE bigimage!='' AND approved='1' $exclude_cat ORDER BY RAND() DESC LIMIT $rphotos";
                break;
            }
            else {
                $group_title = "Random Images - $catname";
                $query = "SELECT id,user,userid,cat,title,bigimage,views FROM photos WHERE bigimage!='' AND approved='1' $userq $exclude_cat AND cat in ($catquery) ORDER BY RAND() DESC LIMIT $rphotos";
                break;
            }
            break;
    }

    $result = mysql_query_eval($query,$link);
    //$numimages = mysql_num_rows( $result );
    //if ( $numimages == 0 ) return;

    $output .= "<center>";
    $output .= "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\" width=\"".$Globals{'tablewidth'}."\" align=\"center\"><tr><td>";
    $output .= "<table width=\"100%\" cellpadding=\"4\" cellspacing=\"1\" border=\"0\">\n";
    $output .= "<tr align=\"center\">";
    $output .= "<td colspan=\"".$Globals{'recentcols'}."\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\">";
    $output .= "<font face=\"verdana,arial\" color=\"".$Globals{'headfontcolor'}."\" size=\"1\">";
    $output .= "<font size=\"2\" face=\"verdana\"><b>$group_title</font>\n";
    $output .= "</font>";
    $output .= "</td></tr>\n";
    $output .= "<Tr align=\"center\">";

    $count=1;
    $e = 2;
    $cell_output_success=1;

    $column = $Globals{'recentcols'};
    if ( $result ) while ($count<= $column and $count <= $rphotos) {
         unset($line);
         $line = mysql_fetch_array($result, MYSQL_ASSOC);

         if (!empty($line)) {
            $photo_name = get_filename( $line['bigimage'] );
            $theext = get_ext( $line['bigimage'] );
            $thecat = $line['cat'];

            $temp_user = ($line["userid"]);

            $output .= "<td width=\"$twidth%\" align=center valign=\"middle\" bgcolor=\"".$Globals{'maincolor'}."\">\n";
            $output .= "<Font size=\"1\"color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">\n";
            $output .=  "<A href=\"".$Globals{'maindir'}."/showphoto.php?photo=".$line['id']."\">\n";
            $filepath = $Globals{'datafull'}."/$thecat/$temp_user$photo_name-thumb.$theext";
            $fileurl = $Globals{'datadir'}."/$thecat/$temp_user$photo_name-thumb.$theext";

            if ( file_exists( $filepath ) ) {
                $output .= "<img border=\"0\" src='$fileurl'>";
            }
            else {
                $theext = strtolower( $theext );
                $filepath = $Globals{'datafull'}."/$thecat/$temp_user$photo_name-thumb.$theext";
                $fileurl = $Globals{'datadir'}."/$thecat/$temp_user$photo_name-thumb.$theext";

                if ( file_exists( $filepath ) )
                    $output .= "<img border=\"0\" src='$fileurl'>";
                else
                    $output .= "<img border=\"0\" src='".$Globals{'idir'}."/nothumb.gif' alt='$fileurl'>";
            }

            $output .= "<Br>".$line["title"]."</a>\n<Br>by ".$line["user"]."</font><!--$cell_output_success - $column -->\n";
            $cell_output_success++;

            if ( $column == $cell_output_success) {
                $pre_done = "1";
            }

            if ( $count == $column && $rphotos != $Globals{'recentcols'} ) {
                $output .= "</td></tr><tr>";

                $column = $Globals{'recentcols'}*$e;
                $e++;
            }
            else {
                $output .="</td>";
            }

        }
        $count++;
    }

    if ( $cell_output_success < $column ) {
        while ($cell_output_success <= $column) {
            $output .= "<td valign=\"middle\" bgcolor=\"".$Globals{'maincolor'}."\" align=\"center\" width=\"$twidth%\"><!-- $column - $cell_output_success - cell fills-->&nbsp;</td>";
            $cell_output_success++;
        }
    }

    if ( $result ) mysql_free_result($result);
    $output .= "</tr></table><!--CyKuH [WTN]-->";
    $output .= "</td></tr></table></center>";
}


function catlist( $parid ) {
    global $Globals, $link;

    $query = "SELECT id,catname,parent,thumbs FROM categories WHERE parent=$parid";
    $boards = mysql_query_eval($query,$link);

    $cathash = "cat='$parid'";

    while ( $row = mysql_fetch_row($boards) ) {
        list ( $tid, $tcatname, $tparent, $tthumb ) = $row;

        $cathash = $cathash." OR cat='$tid'";
    }

    return ( $cathash );
}


function catrow( $parent ) {
    global $Globals, $link, $catrows, $catdepth, $cptotal, $posttotal, $totalviews, $diskspace, $headdone, $output;
    global $ugview;

    if ( $Globals{'catdepth'} == 0 && $parent == 0 ) {
        // this one gets through
    }
    elseif ( $Globals{'catdepth'} == 0 && $parent == 0 ) {
        return;
    }
    elseif ( $catdepth > $Globals{'catdepth'} )
        return;

    $query = "SELECT id,catname,description,catorder,parent,thumbs FROM categories WHERE parent='$parent' ORDER BY catorder ASC";
    $boards = mysql_query_eval($query,$link);
    $posts = mysql_num_rows($boards);

    $indent = "";
    for( $x=0; $x < $catdepth; $x++ ) {
        $indent .="<img height=\"1\" width=\"15\" src='".$Globals{'idir'}."/spacer.gif'>";
    }

    while ( $row = mysql_fetch_row($boards) ) {
        list( $id, $catname, $catdesc, $order, $catparent, $catthumbs ) = $row;

        // If we're dealing with the member galleries, count all the photos and comments.  Otherwise,
        // just count the photos and comments for this particular category.

        if ( $catthumbs == "yes" ) {
            $dodetails = 1;
        }
        else {
            if ($Globals{'catdetails'} == "yes") $dodetails = 1;
            else $dodetails = 0;
        }

        $lastposttime=""; $lastpostby=""; $lastpostlink="";
        $lastphoid=""; $lastphoby=""; $lpholink="";

        if ($dodetails == "1") {
            $quid=""; $cphotos=0; $catposts=0;

            if ( $id != "500" ) {
                $quid = catlist( $id );

                if ( $quid != "" && $quid != "cat=" ) {
                    $query="SELECT id,user,userid,date FROM photos WHERE $quid ORDER BY date DESC";
                    $lastp = mysql_query_eval($query,$link);
                    $cphotos = mysql_num_rows($lastp);
                    $lastpost = mysql_fetch_row($lastp);
                    list( $lastphoid, $lastphoby, $lastphobyid, $lastphotime ) = $lastpost;

                    $query="SELECT username,id,date,photo FROM comments WHERE $quid ORDER BY date DESC";
                    $lastc = mysql_query_eval($query,$link);
                    $catposts = mysql_num_rows($lastc);
                    $lastcomment = mysql_fetch_row($lastc);
                    list( $lastpostby, $lastpostbyid, $lastposttime, $lastpostlink ) = $lastcomment;
                }
            }
            else {
                $query = "SELECT id,user,userid,date FROM photos ORDER BY date DESC";
                $lastp = mysql_query_eval($query,$link);
                $cphotos = mysql_num_rows($lastp);
                $lastpost = mysql_fetch_row($lastp);
                list( $lastphoid, $lastphoby, $lastphobyid, $lastphotime ) = $lastpost;

                $query = "SELECT username,id,date,photo FROM comments ORDER BY date DESC";
                $lastc = mysql_query_eval($query,$link);
                $catposts = mysql_num_rows($lastc);
                $lastcomment = mysql_fetch_row($lastc);
                list( $lastpostby, $lastpostbyid, $lastposttime, $lastpostlink ) = $lastcomment;

                $query = "SELECT SUM(views) as tviews, SUM(filesize) AS fsize FROM photos";
                $totalv = mysql_query_eval($query,$link);
                $totalrow = mysql_fetch_row($totalv);

                $totalviews = number_format( $totalrow[0] );
                $diskuse = $totalrow[1];

                $cptotal = number_format( $cphotos );
                $posttotal = number_format( $catposts );

                $diskspace = $diskuse/1048576;
                $diskspace = number_format( $diskspace, 1 );
                $diskspace = "$diskspace MB";
            }

            $lpholink="";
            $lpprint = "no photos";

            if ($lastphoid != "" && $lastphotime != "") {
                $lpholink = "<a href=\"".$Globals{'maindir'}."/showphoto.php?photo=$lastphoid\"><img border=\"0\" src=\"".$Globals{'idir'}."/lastpost.gif\" alt=\"View last photo posted\"></a>";
                //CyKuH [WTN]
                // set $soffset if there is a time difference
                //$lastphotime = $lastphotime + $soffset;
                list($lpsec,$lpmin,$lphour,$lpmday,$lpmon,$lpyear,$lpwday,$lpyday,$lpisdst) = localtime($lastphotime);
                $lpmon++;
                $lpyear = 1900+$lpyear;
                $lpclock = thetime($lphour,$lpmin);

                $lpprint = "$lpmon-$lpmday-$lpyear $lpclock";
            }

            if ($lastpostby != "") $lastposttext = "by $lastpostby";
            else $lastposttext = "";

            if ($lastphoby != "") $lastphotext = "by $lastphoby";
            else $lastphotext = "";

            if ( $lastpostlink != "" ) $lpostlink="<a href=\"".$Globals{'maindir'}."/showphoto.php?photo=$lastpostlink\"><img border=\"0\" src=\"".$Globals{'idir'}."/lastpost.gif\" alt=\"View last comment posted\"></a>";
            else $lpostlink="";

            if ($lastposttime != "") {
                list($lcsec,$lcmin,$lchour,$lcmday,$lcmon,$lcyear,$lcwday,$lcyday,$lcisdst) = localtime($lastposttime);
                $lcmon++;
                $lcyear = 1900+$lcyear;
                $lcclock = thetime($lchour,$lcmin);
                $lcprint = "$lcmon-$lcmday-$lcyear $lcclock";
            }
            else {
                $lcprint="no comments";
            }

            if ( IsSet($thumb) ) {
                if ($headdone != "1") {
                    $catrows .= "<tr align=\"center\"><td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\"
                        color=\"".$Globals{'headfontcolor'}."\" size=\"1\"><font size=\"1\" face=\"verdana,arial\"><b>Category</b>
                        </font></td><Td bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\"
                        color=\"".$Globals{'headfontcolor'}."\" size=\"1\"><b>Comments</b></font></td><Td
                        bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
                        size=\"1\"><b>Photos</center></b></font></td><Td bgcolor=\"".$Globals{'headcolor'}."\">
                        <font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\" size=\"1\"><b>Last Comment</b></font></td><!-- CyKuH [WTN] -->
                        <Td bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
                        size=\"1\"><B>Last Photo Upload</b></font></td></tr>";
                    $headdone=1;
                }
            }

            if ($catthumbs == "yes") {
                if ($ugview{$id} != 1 && !($id == "500" && $Globals{'showmem'} == "no") ) {
                    $catrows .= "<Tr><Td width=\"45%\" bgcolor=\"".$Globals{'maincolor'}."\"><font face=\"verdana\"
                        size=\"".$Globals{'catfontsize'}."\">$indent<b><A
                        href=\"".$Globals{'maindir'}."/showgallery.php?cat=$id&thumb=1\">$catname</a></b></font><br>
                        <font face=\"verdana,arial\" size=\"1\" color=\"".$Globals{'maintext'}."\">$indent$catdesc</font></td><Td
                        bgcolor=\"".$Globals{'maincolor'}."\"><font color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\"
                        size=\"2\"><Center>$catposts</center></td><Td bgcolor=\"".$Globals{'maincolor'}."\"><font
                        face=\"verdana,arial\" size=\"2\" color=\"".$Globals{'maintext'}."\"><Center>$cphotos</center></td>
                        <Td bgcolor=\"".$Globals{'maincolor'}."\"><Table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
                        <Tr><Td><font face=\"verdana,arial\" size=\"1\" color=\"".$Globals{'maintext'}."\">$lcprint<Br>
                        $lastposttext</td><Td>$lpostlink</td></tr></table></td>
                        <Td bgcolor=\"".$Globals{'maincolor'}."\"><Table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><Tr><Td><font
                        face=\"verdana,arial\"
                        size=\"1\" color=\"".$Globals{'maintext'}."\">$lpprint<Br>$lastphotext</td><Td>$lpholink</td>
                        </tr></table></td></tr>";

                    $catdepth++;
                    catrow($id);
                    $catdepth--;
                }
            }
            else {
                if ($ugview{$id} != 1 ) {
                    $catrows .= "<Tr><Td width=\"45%\" bgcolor=\"".$Globals{'topcatcolor'}."\"><font face=\"verdana\"
                        size=\"".$Globals{'catfontsize'}."\">$indent<b><A
                        href=\"".$Globals{'maindir'}."/index.php?cat=$id\"><font face=\"verdana, arial, helvetica\"
                        size=\"2\"
                        color=\"".$Globals{'catnavcolor'}."\">$catname</font></a></b></font><br>
                        <font face=\"verdana,arial\" size=\"1\" color=\"".$Globals{'topcatfcolor'}."\">$indent$catdesc</font></td><Td
                        bgcolor=\"".$Globals{'topcatcolor'}."\"><font color=\"".$Globals{'topcatfcolor'}."\" face=\"verdana,arial\"
                        size=\"2\"><Center>$catposts</center></td><Td bgcolor=\"".$Globals{'topcatcolor'}."\"><font
                        face=\"verdana,arial\" size=\"2\" color=\"".$Globals{'topcatfcolor'}."\"><Center>$cphotos</center></td>
                        <Td bgcolor=\"".$Globals{'topcatcolor'}."\"><Table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
                        <Tr><Td><font face=\"verdana,arial\" size=\"1\" color=\"".$Globals{'topcatfcolor'}."\">$lcprint<Br>
                        $lastposttext</td><Td>$lpostlink</td></tr></table></td>
                        <Td bgcolor=\"".$Globals{'topcatcolor'}."\"><Table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><Tr><Td><font
                        face=\"verdana,arial\"
                        size=\"1\" color=\"".$Globals{'topcatfcolor'}."\">$lpprint<Br>$lastphotext</td><Td>$lpholink</td>
                        </tr></table></td></tr>";

                    $catdepth++;
                    catrow($id);
                    $catdepth--;
                }
            }
        }
        else {
            if ($ugview{$id} != 1) {
                $catrows .= "<Tr><Td width=\"100%\" colspan=\"5\" bgcolor=\"".$Globals{'topcatcolor'}."\"><font
                    face=\"verdana\" size=\"".$Globals{'catfontsize'}."\">$indent<b><A
                    href=\"".$Globals{'maindir'}."/index.php?cat=$id\"><font face=\"verdana, arial, helvetica\" size=\"2\"
                    color=\"".$Globals{'catnavcolor'}."\">$catname</font></a></b></font><br>
                    <font face=\"verdana,arial\" size=\"1\" color=\"".$Globals{'searchtext'}."\">$indent$catdesc</font></td></tr>";

                $catdepth++;
                catrow($id);
                $catdepth--;
            }
        }
    }
    mysql_free_result($boards);

    return;
} // end sub catrow

function thetime($inhour,$inmin) {
    if ($inmin < 10) {
        $inmin="0$inmin";
    }
    if ($inhour == 0) {
        $inhour = str_replace("0", "12", $inhour);
        $outclock=$inhour.":".$inmin."am";
        return($outclock);
    }
    else {
        if ($inhour < 10) {
            $inhour = str_replace("0", "", $inhour);
        }
    }
    if ($inhour > 11) {
        if ($inhour != 12) {
            $inhour=$inhour-12;
        }
        $outclock=$inhour.":".$inmin."pm";
    }
    else {
        $outclock=$inhour.":".$inmin."am";
    }

    return($outclock);
}

function get_username( $user ) {
    global $Globals, $db_link;

    if ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
        $query = "SELECT userid,username,email FROM user WHERE userid='$user'";
    }
    if ($Globals{'vbversion'} == "phpBB") {
        $query = "SELECT user_id,username,user_email FROM users WHERE user_id='$user'";
    }
    if ($Globals{'vbversion'} == "phpBB2") {
        if ( !empty($Globals{'dprefix'}) ) {
            $utable=$Globals{'dprefix'} ."_users";
        }
        else {
            $utable="users";
        }

        $query = "SELECT user_id,username,user_email FROM $utable WHERE user_id='$user'";
    }
    if ($Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
        $query = "SELECT U_Number,U_Username,U_Email FROM w3t_Users WHERE U_Number='$user'";
    }
    if ($Globals{'vbversion'} == "Internal") {
        $query = "SELECT userid,username,email FROM users WHERE userid='$user'";
    }

    $queryv = mysql_query_eval($query,$db_link);
    $results = mysql_fetch_array($queryv);

    //need to modify to return both username and email
    $username[0] = $results[1];
    $username[1] = $results[2];
    //list( $username, $email ) = get_username( $user );

    return( $username );
}

function get_profilelink( $tuserid ) {
    global $Globals, $db_link, $postreply, $privatelink;

    if ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
        $profilelink = $Globals{'vbulletin'}."/member.php?action=getinfo&userid=$tuserid";
        $privatelink = $Globals{'vbulletin'}."/private.php?action=newmessage&userid=$tuserid";
        $postreply = "<img src=\"".$Globals{'vbulletin'}."/images/reply.gif\" border=\"0\" alt=\"Post A Reply\">";
    }
    if ($Globals{'vbversion'} == "Internal") {
        $profilelink = $Globals{'maindir'}."/member.php?ppaction=profile&uid=$tuserid";
        $postreply = "<font color=\"".$Globals{'headfontcolor'}."\" face=\"verdana\" size=\"2\"><b>Post a Reply</b>";
    }
    if ($Globals{'vbversion'} == "phpBB") {
        $profilelink = $Globals{'vbulletin'}."/bb_profile.php?mode=view&user=$tuserid";
        $postreply = "<font color=\"".$Globals{'headfontcolor'}."\" face=\"verdana\" size=\"2\"><b>Post a Reply</b>";
    }
    if ($Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
        $query = "SELECT U_Username FROM w3t_Users WHERE U_Number=$tuserid LIMIT 1";
        $queryv = mysql_query_eval($query,$db_link);
        $results = mysql_fetch_array($queryv);
        $tusername = $results[0];
        $profilelink = $Globals{'vbulletin'}."/showprofile.php?Cat=&User=$tusername";
        $postreply = "<font color=\"".$Globals{'headfontcolor'}."\" face=\"verdana\" size=\"2\"><b>Post a Reply</b>";
    }
    if ($Globals{'vbversion'} == "phpBB2") {
        $profilelink = $Globals{'vbulletin'}."/profile.php?mode=viewprofile&u=$tuserid";
        $postreply = "<font color=\"".$Globals{'headfontcolor'}."\" face=\"verdana\" size=\"2\"><b>Post a Reply</b>";
    }

    return( $profilelink );
}

function pagesystem( $rowcnt ) {
    global $Globals, $perpage, $pages, $page, $posternav, $thecat, $sword, $p, $sortparam, $user, $stype;

    // begin pages/nav system //
    // $rowcnt = number of images to display
    // $perpage = configurable by menu

    $pages = ($rowcnt/$perpage);

    if ((intval($pages)) < $pages) {
        $pages = intval($pages)+1;
    }
    else {
        $pages = intval($pages);
    }

    if ($page != "") {
        $startnumb = ($page*$perpage)-$perpage+1;
    }
    else {
        $page = 1;
        $startnumb = 1;
    }

    if ($pages > 1) {
        $posternav .= "<table width=\"".$Globals{'tablewidth'}."\"><Tr><Td></td><Td align=\"right\"><font size=\"1\" face=\"verdana,sans-serif\">";
        $thestart = 0;

        if ($page < 11) {
            $thestart = 1;
        }
        elseif ($page > 10) {
            $thestart = $page-5;
        }
        $theend = $thestart+9;

        for ($p=$thestart; $p<=$pages; $p++) {
            if ($p != $thestart) {
                $posternav .= " | ";
            }
            else
                $posternav .= "Page ";

                if ( $p == $thestart )
                    $posternav .= "<a href=\"".$Globals{'maindir'}."/showgallery.php?cat=$thecat&si=$sword&thumb=1&page=1&sort=$sortparam&perpage=$perpage&user=$user&stype=$stype\">1</a> ... ";

            if ($page != $p) {
                $thispage = "$p";
                $posternav .= "<a href=\"".$Globals{'maindir'}."/showgallery.php?cat=$thecat&si=$sword&thumb=1&page=$p&sort=$sortparam&perpage=$perpage&user=$user&stype=$stype\">$thispage</a>";
            }

            if ($p > $theend) {
                $posternav .= " ... <a href=\"".$Globals{'maindir'}."/showgallery.php?cat=$thecat&si=$sword&thumb=1&page=$pages&sort=$sortparam&perpage=$perpage&user=$user&stype=$stype\">$pages</a>";
                break;
            }

            if ($page == $p) {
                $posternav .= "<b>$p</b>";
            }
        }

        $posternav .= "</td></tr></table>";
    }
// end pages/nav //#
}

function ConvertReturns( $ConvertReturns ) {
    $ConvertReturns = str_replace( "\n\r\n", "<p>", $ConvertReturns);
    $ConvertReturns = str_replace( "\r", "<br>", $ConvertReturns);
    //$ConvertReturns = eregi_replace( "(\r\n|\r|\n)","<br />", $ConvertReturns );

    return ($ConvertReturns);
}

function childsub( $parid ) {
    global $Globals, $childnav, $link, $tcat, $user;

    if ( $parid > 3000 ) {
        $aquery = "SELECT id,albumname FROM useralbums WHERE id=$parid";
        $arows = mysql_query_eval($aquery,$link);

        while ( $aresult = mysql_fetch_row($arows) ) {
            list( $subid, $subalbumname ) = $aresult;

            $childnav = "<font face=\"verdana, arial, helvetica\" size=\"2\">
            > <a href=\"".$Globals{'maindir'}."/showgallery.php?cat=500&thumb=1\">Member</a> >
            <a href=\"".$Globals{'maindir'}."/showgallery.php?cat=500&user=$user&thumb=1\">$tcat</a> > <b>
            <a href=\"".$Globals{'maindir'}."/showgallery.php?cat=$subid&thumb=1\">
            $subalbumname</a> $childnav";
        }
    }
    else {
        $query = "SELECT id,catname,parent,thumbs FROM categories WHERE id=$parid";
        $resulta = mysql_query_eval($query, $link);

        while ( $row = mysql_fetch_row($resulta) ) {
            list( $tid, $tcatname, $tparent, $tthumb ) = $row;

            if ($tthumb == "no") {
                $childnav = "> <font face=\"verdana, arial, helvetica\" size=\"2\"><b><a href=\"".$Globals{'maindir'}."/index.php?cat=$tid\">
                $tcatname</a> $childnav";
            }
            else {
                $childnav = "> <font face=\"verdana, arial, helvetica\" size=\"2\"><b><a href=\"".$Globals{'maindir'}."/showgallery.php?cat=$tid&thumb=1\">
                $tcatname</a> $childnav";
            }

            childsub($tparent);
        }
    }
}

//
//
function catmoveopt( $subcatid ) {
    global $Globals, $catoptions, $link, $dashes, $selected, $ugcat, $adminedit, $userid, $personal;

    if (empty($dashes)) $dashes=array("");

    if (empty($personal)) {
        $aquery = "SELECT id,albumname,description FROM useralbums WHERE parent=$userid";
        $arows = mysql_query_eval($aquery,$link);

        if ( $arows ) {
            while ( $aresult = mysql_fetch_row($arows) ) {
                list( $subid, $subalbumname, $subalbumdesc ) = $aresult;

                if ($selected == $subid) {
                    $selecttext = "SELECTED";
                }
                else {
                    $selecttext = "";
                }

                $catoptions .= "<option $selecttext value=\"$subid\">*$subalbumname</option>";
            }
        }
        $personal = 1;
    }

    $query = "SELECT id,ugnoupload FROM categories";
    $resultb = mysql_query_eval($query,$link);

    if ($adminedit == "1") {
        $query = "SELECT id,catname,thumbs FROM categories WHERE parent=$subcatid ORDER BY catorder";
    }
    else {
        $query = "SELECT id,catname,thumbs FROM categories WHERE parent=$subcatid AND private='no' ORDER BY catorder";
    }

    $rows = mysql_query_eval($query,$link);
    while ( $result = mysql_fetch_row($rows) ) {
        list( $subid, $subcatname, $subthumbs ) = $result;

        $dashdisp = "";
        for ($i = 0; $i < $dashes[$subcatid]; $i++) {
            $dashdisp .= "-";
        }
        $dashes[$subid] = $dashes[$subcatid]+1;

        if ($selected == $subid) {
            $selecttext = "SELECTED";
        }
        else {
            $selecttext = "";
        }

        if ( $subthumbs == "no" ) {
            if ( $ugcat{$subid} != 1 ) {
                $catoptions .= "<option $selecttext value=\"notcat\">$dashdisp$subcatname</option>";
            }
        }
        else {
            if ( $ugcat{$subid} != 1 ) {
                $catoptions .= "<option $selecttext value=\"$subid\">$dashdisp$subcatname</option>";
            }
        }
        catmoveopt($subid);
    }

    mysql_free_result($rows);

}


function topmenu() {
    global $Globals, $menu, $userid, $username, $cat, $adminedit, $userup, $catquery;

    if ( IsSet($cat) ) {
        $catquery="?cat=$cat";
    }

    if ( $adminedit == 1 )
        $menu = "[ <A href=\"".$Globals{'maindir'}."/adm-index.php\">Admin</a> | <A href=\"".$Globals{'maindir'}."/index.php\">Home</a>";
    else
        $menu = "[ <A href=\"".$Globals{'maindir'}."/index.php\">Home</a>";

    if ($userid != "") {
        $log = " | <a href=\"".$Globals{'maindir'}."/logout.php\">Logout</a>";
        $menu .= " | <a href=\"".$Globals{'maindir'}."/useralbums.php\">Albums</a>";
    }
    else {
        $log = "| <a href=\"".$Globals{'maindir'}."/login.php?login=yes\">Login</a>";
    }

    if ( $adminedit == 0 ) {
        if ($Globals{'allowregs'} == "yes" && $userid == "") {
            if ($Globals{'vbversion'} == "phpBB") {
                $regtext = " | <A href=\"".$Globals{'vbulletin'}."/bb_register.php?mode=agreement\">Register</a>";
                $menu .= $regtext;
            }
            elseif ($Globals{'vbversion'} == "phpBB2") {
                $regtext = " | <A href=\"".$Globals{'vbulletin'}."/profile.php?mode=register\">Register</a>";
                $menu .= $regtext;
            }
            elseif ($Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
                $regtext = " | <A href=\"".$Globals{'vbulletin'}."/newuser.php?Cat=\">Register</a>";
                $menu .= $regtext;
            }
            elseif ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
                $regtext = " | <A href=\"".$Globals{'vbulletin'}."/register.php?action=signup\">Register</a>";
                $menu .= $regtext;
            }
            else {
                $regtext = " | <A href=\"".$Globals{'maindir'}."/register.php\">Register</a>";
                $menu .= $regtext;
            }
        }
    }

    if ($Globals{'vbversion'} == "phpBB") {
        $menu .= " | <A href=\"".$Globals{'vbulletin'}."/prefs.php\">Profile</a>";
    }
    elseif ($Globals{'vbversion'} == "phpBB2") {
        $menu .= " | <A href=\"".$Globals{'vbulletin'}."/profile.php?mode=editprofile\">Profile</a>";
    }
    elseif ($Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
        $menu .= " | <A href=\"".$Globals{'vbulletin'}."/login.php\">Profile</a>";
    }
    elseif ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
        $menu .= " | <A href=\"".$Globals{'vbulletin'}."/usercp.php\">Profile</a>";
    }
    else {
        $menu .= " | <A href=\"".$Globals{'maindir'}."/member.php?ppaction=edit\">Profile</a>";
    }

    if ( $adminedit == 0 ) {
        if ( $Globals{'allowup'} == "yes" ) {
            if ( $userup != 2 ) {
                $menu .= " | <A href=\"".$Globals{'maindir'}."/uploadphoto.php$catquery\">Upload Photos</a>";
            }
        }
    }
    else {
        $menu .= " | <A href=\"".$Globals{'maindir'}."/uploadphoto.php$catquery\">Upload Photos</a>";
    }

    $menu .= " $log ]";

    return;
}

function forward( $redirect, $msg="Action Complete." ) {
    global $Globals, $link, $db_link;

    if ( !IsSet($msg) ) {
        $msg = "Now forwarding you to $redirect";
    }
    if ( $redirect == "" ) {
        $redirect = $Globals{'maindir'}."/index.php";
    }

    $success = "<head><title>".$Globals{'galleryname'}."</title>
    <script language=\"JavaScript\"><!--
    t=1; function dorefresh() { u=new String(\"$redirect\");
    ti=setTimeout(\"dorefresh();\",2000); if (t>0) { t-=1; }
    else { clearTimeout(ti); window.location=u.replace(\"#\",\"&t=\"+parseInt(10000*Math.random())+\"#\"); }
    } window.onLoad=dorefresh();
    --></script><meta http-equiv=\"Refresh\" content=\"2; URL=$redirect\">
    </head>
    <body bgcolor=\"".$Globals{'forwardbody'}."\" leftmargin=\"10\" topmargin=\"10\" marginwidth=\"10\" marginheight=\"10\"
    link=\"".$Globals{'maintext'}."\" vlink=\"".$Globals{'maintext'}."\" alink=\"".$Globals{'maintext'}."\"><!-- CyKuH [WTN] -->
    <br><br>

    <table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" height=\"50%\" align=\"center\">
    <tr align=\"center\" valign=\"middle\">
    <td>
    <table border=\"0\" cellspacing=\"1\" cellpadding=\"10\" bgcolor=\"".$Globals{'bordercolor'}."\" width=\"70%\">
    <tr>
    <td bgcolor=\"".$Globals{'maincolor'}."\" align=\"center\">
    <p><font face=\"verdana, arial, helvetica\" size=\"2\" color=\"".$Globals{'maintext'}."\"><b>
    $msg</b><br><br></font>

    <font face=\"verdana,arial,helvetica\" size=\"1\"> <a
    href=\"$redirect\">Click here if you do not want to wait any longer<br>
    (or if your browser does not automatically forward you)</a></font></p></td>
    </tr>
    </table>
    </td>
    </tr>
    </table>";

    print $success;
}

function dieWell( $message )  {
    global $Globals, $childnav, $menu, $link, $db_link, $header, $footer;

    topmenu();

    $output = "<title>".$Globals{'galleryname'}." Message</title>$header<table
        cellpadding=\"0\" cellspacing=\"0\" border=\"0\" height=\"40\" width=\"".$Globals{'tablewidth'}."\"><Tr>
        <Td valign=\"center\" width=\"50%\">&nbsp;$childnav</td><td width=\"50%\" align=\"right\" valign=\"center\">
        <font face=\"verdana, arial\" size=\"2\">$menu&nbsp;</font>
        </td></tr></table><!--CyKuH [WTN]-->

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"100%\" align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"2\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\"
        color=\"".$Globals{'headfontcolor'}."\" size=\"2\"><B>".$Globals{'galleryname'}." Message
        </font>
        </font></td>
        </tr><Tr><Td bgcolor=\"".$Globals{'maincolor'}."\" height=\"300\" valign=\"middle\"><center><font face=\"verdana\"
        size=\"3\" color=\"".$Globals{'maintext'}."\"><b>$message</b></font></centeR></td></tr></table></td></tr></table>";

    print "$output\n$footer";

    // Closing connection
    mysql_close($link);
    mysql_close($db_link);

    exit;
}

function fixmessage ( $message ) {
    $newmessage = str_replace( "%22","\"", $message );


    $newmessage = str_replace( "&", "&amp;", $newmessage );
    $newmessage = str_replace( "<", "&lt;", $newmessage );
    $newmessage = str_replace( ">", "&gt;", $newmessage );

    return ( $newmessage );
}

function fixstring ( $string ) {
    $string = fixmessage ( $string );
    $string = preg_replace( "/<(?:[^>']*|([']).*?\1)*>/e", "", $string );

    return( $string );
}

function findenv ( $name ) {
    global $HTTP_SERVER_VARS;

    $this = "";
    if (empty($HTTP_SERVER_VARS["$name"]))
        $HTTP_SERVER_VARS["$name"]="";
    if (empty($_ENV["$name"]))
        $_ENV["$name"]="";

    if(getenv($name) != '') {
        $this = getenv("$name");
    }

    if(($this == '') && ($HTTP_SERVER_VARS["$name"] != '')) {
        $this = $HTTP_SERVER_VARS["$name"];
    }

    if(($this == '') && ($_ENV["$name"] != '')) {
        $this = $_ENV["$name"];
    }

    return $this;
}

function wordchars ( $string ) {

    $stripstring = ereg_replace( "[^a-zA-Z0-9/\:]", "", $string );

    if ( strcmp($string, $stripstring) ) {
        dieWell( "The username you chose is not valid. Usernames may not contain anything but numbers and letters." );
        exit;
    }

    return ( $stripstring );
}


function inc_user_posts( $type = "plus", $user = 0 ) {
    global $Globals, $db_link, $userid;

    if ( $user == 0 ) $user = $userid;

    if ( $type == "plus" ) {
        $do_inc = "+1";
    }
    else {
        $do_inc = "-1";
    }

    if ($Globals{'vbversion'} == "2.0.3" || $Globals{'vbversion'} == "2.2.0") {
        $query = "UPDATE user SET posts=posts$do_inc WHERE userid=$user";
        $resulta = mysql_query_eval($query,$db_link);
    }
    if ($Globals{'vbversion'} == "phpBB") {
        $query = "UPDATE users SET user_posts=user_posts$do_inc WHERE user_id=$user";
        $resulta = mysql_query_eval($query,$db_link);
    }
    if ($Globals{'vbversion'} == "w3t" || $Globals{'vbversion'} == "w3t6") {
        $query = "UPDATE w3t_Users SET U_Totalposts=U_Totalposts$do_inc WHERE U_Number=$user";
        $resulta = mysql_query_eval($query,$db_link);
    }
    if ($Globals{'vbversion'} == "Internal") {
        $query = "UPDATE users SET posts=posts$do_inc WHERE userid=$user";
        $resulta = mysql_query_eval($query,$db_link);
    }

    return;
}

function remove_all_files( $filename, $medsize, $uid, $thecat ) {
    global $Globals;

    $theext = substr($filename,strlen($filename) - 4,4);
    $filenoext = $filename;
    $filenoext= str_replace( $theext, "", $filenoext);

    if ( file_exists($Globals{'datafull'}."$thecat/$uid$filenoext-thumb$theext") ) {
        $thumbnail = $Globals{'datafull'}."$thecat/$uid$filenoext-thumb$theext";
    }
    else {
        $thumbnail = $Globals{'datafull'}."$thecat/$uid$filenoext-thumb.jpg";
    }

    $bigpic = $Globals{'datafull'}."$thecat/$uid$filenoext$theext";
    $medpic = $Globals{'datafull'}."$thecat/$uid$filenoext-med$theext";

    if ( file_exists($thumbnail) ) unlink($thumbnail);
    if ( file_exists($bigpic) ) unlink($bigpic);

    if ($medsize != "0") {
         if ( file_exists($medpic) ) unlink($medpic);
    }
}

function move_image( $pcat, $catmove, $tuserid, $bigimage ) {
    global $Globals;

    $datadir = $Globals{'datafull'};
    $olddir = $datadir."$pcat";
    $newdir = $datadir."$catmove";

    $theext = substr($bigimage, strlen($bigimage) - 4,4);
    $filename = $bigimage;
    $filename = str_replace( $theext, "", $filename);

    $biglink = $olddir."/$tuserid$filename$theext";
    $newbiglink = $newdir."/$tuserid$filename$theext";

    $thumblink = $olddir."/$tuserid$filename-thumb$theext";
    $newthumblink = $newdir."/$tuserid$filename-thumb$theext";

    $mediumlink = "";
    $newmediumlink = "";
    $mediumlink = $olddir."/$tuserid$filename-med$theext";
    $newmediumlink = $newdir."/$tuserid$filename-med$theext";

    //print "Moving file: [$biglink] to [$newbiglink]<br>";
    //print "Moving medium file: [$mediumlink] to [$newmediumlink]<br>";
    //print "Moving thumbnail: [$thumblink] to [$newthumblink]<br><br>";

    if ( file_exists($biglink) ) {
        if ( copy($biglink, $newbiglink) ) {
            unlink( $biglink );
        }
        else {
            dieWell( "Copy of the file $biglink failed. Operation cancelled." );
            exit;
        }
    }

    if ( file_exists($mediumlink) ) {
        if ( copy($mediumlink, $newmediumlink) ) {
            unlink( $mediumlink );
        }
        else {
            unlink( $newbiglink );
            dieWell( "Copy of the file $mediumlink failed. Operation cancelled." );
            exit;
        }
    }

    if ( file_exists($thumblink) ) {
        if ( copy($thumblink, $newthumblink) ) {
            unlink( $thumblink );
        }
        else {
            unlink( $newbiglink );
            unlink( $newmediumlink );
            dieWell( "Copy of the file $thumblink failed. Operation cancelled." );
            exit;
        }
    }
}

function move_image_cat( $pid, $catmove ) {
    global $Globals, $link, $userid, $adminedit;

    $query = "SELECT userid,cat,bigimage,medsize,title FROM photos WHERE id=$pid";

    $resulta = mysql_query_eval($query,$link);
    $row = mysql_fetch_row($resulta);
    list( $puserid, $pcat, $bigimage, $medsize, $ptitle ) = $row;

    if ( ($userid == $puserid && $Globals{'userdel'} == "yes") || $adminedit == 1 ) {
        move_image( $pcat, $catmove, $puserid, $bigimage );

        if ( $Globals{'moderation'} == "yes" && $adminedit != 1 ) $approved="0";
        else $approved="1";

        $query = "UPDATE photos SET cat=$catmove, approved='$approved' WHERE id=$pid";
        $resulta = mysql_query_eval($query,$link);

        $query = "UPDATE comments SET cat=$catmove WHERE photo=$pid";
        $resulta = mysql_query_eval($query,$link);

        if ( $Globals{'useemail'} == "yes" && $userid != $puserid ) admin_email( 'moved', $pid, $puserid, $ptitle ) ;

        $adesc="Moved image #$pid";
        $furl = $Globals{'maindir'}."/showphoto.php?photo=$pid";

        forward( $furl, $adesc );
        exit;
    }
    else {
        dieWell( "You do not have permission for this action!" );
        exit;
    }
}

function fixcolor( $string="" ) {
    $string = str_replace(" ","",$string);
    $string = str_replace("\"","",$string);
    $string = "<font color=\"$string\">";
    return $string;
}


function convert_markups( $ecomments ) {
    global $Globals;

    // Convert near-URL tags to HTML
    $ecomments = " " . $ecomments;
    $ecomments = preg_replace("#([\n ])([a-z]+?)://([^, \n\r]+)#i", "\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>", $ecomments);
    $ecomments = preg_replace("#([\n ])www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[^, \n\r]*)?)#i", "\\1<a href=\"http://www.\\2.\\3\\4\" target=\"_blank\">www.\\2.\\3\\4</a>", $ecomments);
    $ecomments = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([^, \n\r]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ecomments);
    $ecomments = substr($ecomments, 1);

    // Convert URL tags to HTML
    $ecomments = preg_replace("/\[url\]ftp:\/\/([^\[]*?)\[\/url\]/i","<a href=\"ftp://\\1\" target=\"_blank\">ftp://\\1</a>",$ecomments);
    $ecomments = preg_replace("/\[url\]http:\/\/([^\[]*?)\[\/url\]/i","<a href=\"http://\\1\" target=\"_blank\">http://\\1</a>",$ecomments);
    $ecomments = preg_replace("/\[url\]https:\/\/([^\[]*?)\[\/url\]/i","<a href=\"https://\\1\" target=\"_blank\">https://\\1</a>",$ecomments);
    $ecomments = preg_replace("/\[url\]([^\[]*?)\[\/url\]/i","<a href=\"http://\\1\" target=\"_blank\">\\1</a>",$ecomments);
    $ecomments = preg_replace("/\[url=http:\/\/(.*?)\](.*?)\[\/url\]/i","<a href=\"http://\\1\" target=\"_blank\">\\2</a>",$ecomments);
    $ecomments = preg_replace("/\[url=https:\/\/(.*?)\](.*?)\[\/url\]/i","<a href=\"https://\\1\" target=\"_blank\">\\2</a>",$ecomments);
    $ecomments = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/i","<a href=\"http://\\1\" target=\"_blank\">\\2</a>",$ecomments);

    // Convert bolds and italics
    $ecomments = str_replace("[b]","<b>",$ecomments);
    $ecomments = str_replace("[i]","<i>",$ecomments);
    $ecomments = str_replace("[/i]","</i>",$ecomments);
    $ecomments = str_replace("[/b]","</b>",$ecomments);

    // Convert the color codes
    $ecomments = preg_replace("/\[color:(.+?)\]/e","fixcolor(\"$1\")",$ecomments);
    $ecomments = preg_replace("/\[\/color\]/","</font color>",$ecomments);

    // Do list elements
    $ecomments = preg_replace("/(\[list\])\n?\r?(.+?)(\[\/list\])/is","<ul type=\"square\">\\2</ul>",$ecomments);
    $ecomments = preg_replace("/(\[list=)(A|1)(\])\n?\r?(.+?)(\[\/list\])/is","<ol type=\"\\2\">\\4</ol>",$ecomments);
    $ecomments = preg_replace("/\n?\r?(\[\*\])/is","<li>",$ecomments);

    // Quote markup
    $ecomments = str_replace("[quote]","<blockquote>Quote:<hr /><br />",$ecomments);
    $ecomments = str_replace("[/quote]","<br /><br /><hr /></blockquote>",$ecomments);

    // Convert email markup to html
    $ecomments = eregi_replace("\[email\]([^\[]*)\[/email\]","<a href=\"mailto:\\1\">\\1</a>",$ecomments);

    // Smilies
    //$Body = preg_replace("/(( |\n|^):\)|(\[|:){$ubbt_lang['ICON_SMILE']}(\]|:))/","\\2<img src=\"{$config['images']}/icons/smile.gif\" alt=\"\" />",$Body);

    $ecomments = preg_replace("/(( |\n|^):\)|(\[|:)smile(\]|:))/","\\2<img src=\"{$Globals['idir']}/smile.gif\" alt=\"Smile\" />", $ecomments);
    $ecomments = preg_replace("/(( |\n|^);\)|(\[|:)wink(\]|:))/","\\2<img src=\"{$Globals['idir']}/wink.gif\" alt=\"Wink\" />", $ecomments);
    $ecomments = preg_replace("/(( |\n|^):\(|(\[|:)sad(\]|:))/","\\2<img src=\"{$Globals['idir']}/sad.gif\" alt=\"Sad\" />", $ecomments);

    $ecomments = preg_replace("/(( |\n|^):d|(\[|:)teeth(\]|:))/","\\2<img src=\"{$Globals['idir']}/teeth_smile.gif\" alt=\"Smile!\" />", $ecomments);
    $ecomments = preg_replace("/(( |\n|^):p|(\[|:)tounge(\]|:))/","\\2<img src=\"{$Globals['idir']}/tounge_smile.gif\" alt=\"Nah!\" />", $ecomments);

    $ecomments = preg_replace("/(( |\n|^)\(y\)|(\[|:)up(\]|:))/","\\2<img src=\"{$Globals['idir']}/thumbs_up.gif\" alt=\"Thumbs Up!\" />", $ecomments);
    $ecomments = preg_replace("/(( |\n|^)\(n\)|(\[|:)no(\]|:))/","\\2<img src=\"{$Globals['idir']}/thumbs_down.gif\" alt=\"Thumbs Down!\" />", $ecomments);

    return ($ecomments);
}

?>
