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
include("adm-cinc.php");

if ( empty($do) ) $do="";
if ( empty($catid) ) $catid="";

if ($ppaction == "delcat") {  //# Delete a category
    if ($do == "process") { //# Process delete cat
        if ($catid != 500 && $catid != "0" && $catid != "") {
            delete_cat($catid);
        }
        else {
            dieWell("Invalid category ID.");
        }

        $forwardid = $Globals{'maindir'}."/adm-cats.php?ppaction=cats";
        forward( $forwardid, "Processing complete!" );
        exit;
    }

    //# Generate an 'are you sure' you want to delete? form...

    $querya="SELECT catname FROM categories where id=$catid";
    $catq = mysql_query_eval($querya, $link);
    $catr = mysql_fetch_array($catq, MYSQL_ASSOC);
    $catname = $catr['catname'];
    mysql_free_result($catq);

    $output="$header<center><hr>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\"
        face=\"verdana\"><B>PhotoPost Add a Category</font>
        </font></td>
        </tr>
        <tr id=\"cat\">
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$adminmenu</b></font></td></tr>
        <tr><td bgcolor=\"#f7f7f7\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\"><center><Br>
        You're about to delete the category called \"$catname\", and <b>ALL PHOTOS AND COMMENTS WITHIN THE CATEGORY</B>.<p>
        Are you sure you want to do that?
        <form action=\"".$Globals{'maindir'}."/adm-cats.php\" method=\"post\">
        <input type=\"hidden\" name=\"catid\" value=\"$catid\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <input type=\"hidden\" name=\"ppaction\" value=\"delcat\">
        <input type=\"submit\"
        value=\"I'm sure, delete the category.\"></form></font></td></tr></table></td></tr></table>";

    print "$output<p>".$Globals{'cright'}."<p>$footer";
}

if ($ppaction == "addcat") { //# Add a category
    $parent=$catid;

    if ($do == "process") {
        // Process a user's category submission

        if ($parent == "") {
            $parent=0;
        }

        $querya="SELECT catorder FROM categories ORDER BY catorder DESC";
        $catq = mysql_query_eval($querya, $link);
        $catr = mysql_fetch_array($catq, MYSQL_ASSOC);
        $maxcatorder = $catr['catorder'];
        $maxcatorder++;

        $catname = addslashes( $catname );
        $catdesc = addslashes( $catdesc );

        $query = "INSERT INTO categories values(NULL,'$catname','$catdesc','$maxcatorder','yes',$parent,NULL,NULL,NULL,'no',NULL,NULL,NULL,NULL)";
        $setug = mysql_query_eval($query,$link);

        $thecatid = mysql_insert_id( $link );
        $newdir = $Globals{'datafull'}."$thecatid";

        if ( !mkdir( $newdir, 0755 ) ) {
            dieWell( "Error creating directory $newdir. Please check your system." );
            exit;
        }
        chmod( $newdir, 0777 );

        forward( $Globals{'maindir'}."/adm-cats.php?ppaction=cats", "Processing complete!" );
        exit;
    }

    //# Print out the Add a category HTML form

    if ($parent != "") {
        $querya="SELECT catname FROM categories where id=$parent";
        $catq = mysql_query_eval($querya,$link);
        $catr = mysql_fetch_array($catq, MYSQL_ASSOC);
        $parname = $catr['catname'];

        $partext = "<font size=\"3\" face=\"verdana\" color=\"".$Globals{'headfontcolor'}."\">&nbsp;Create a subcategory for: \"<b>$parname</b>\"<Br>";
    }
    else
        $partext="";

    $output = "$header<center><hr>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\"
        face=\"verdana\"><b>PhotoPost Add a Category</font>
        </font></td>
        </tr>
        <tr id=\"cat\">
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$adminmenu</b></font></td></tr>
        <tr><td bgcolor=\"".$Globals{'maincolor'}."\"><center><Br>

        <table border=\"0\" cellpadding=\"1\" cellspacing=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"><form method=\"post\"
        action=\"".$Globals{'maindir'}."/adm-cats.php\">
        <tr><Td bgcolor=\"".$Globals{'headcolor'}."\">
        $partext
        <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\">
        <tr><Th bgcolor=\"#F7f7f7\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Category Name</font></th>
        <Th bgcolor=\"#F7f7f7\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Category Description</font></th></tr><Tr>
        <Td
        bgcolor=\"#f7f7f7\"><input type=\"text\" size=\"50\"
        value=\"\" name=\"catname\"></td><Td bgcolor=\"#f7f7f7\"><input type=\"text\" size=\"50\" value=\"\" name=\"catdesc\"></td>
        </tr>
        </table></td></tr></table><p>
        <input type=\"hidden\" name=\"catid\" value=\"$parent\">
        <input type=\"hidden\" name=\"ppaction\" value=\"addcat\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <input type=\"submit\" value=\"Add Category\"></form></td></tr></table></td></tr></table>";

    print "$output<p>".$Globals{'cright'}."<p>$footer";
}


//# Edit categories

if ($ppaction == "editcat") {
    $output = "$header<center><hr>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\"
        face=\"verdana\"><B>PhotoPost Category Editor</font>
        </font></td>
        </tr>
        <tr id=\"cat\">
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$adminmenu</b></font></td></tr>
        <tr><td bgcolor=\"".$Globals{'maincolor'}."\"><center><Br>
        <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"><form method=\"post\"
        action=\"".$Globals{'maindir'}."/adm-cats.php\"><tr><Td>
        <table border=\"0\" cellpadding=\"5\" cellspacing=\"1\"><tr><Th bgcolor=\"#F7f7f7\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Display Order</th>
        <Th bgcolor=\"#F7f7f7\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Category Name</font></th>
        <Th bgcolor=\"#F7f7f7\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\">Category Description</font></th></tr>";

    $viewoptions = "";
    $upoptions = "";
    $postoptions = "";
    $annooptions = "";
    $thumbpar = "";
    $adminup = "";

    $query = "SELECT
        id,catname,description,catorder,parent,thumbs,header,footer,headtags,private,ugnoview,ugnoupload,ugnopost,ugnoanno
        FROM categories WHERE id=$catid";
    $boards = mysql_query_eval($query,$link);
    $posts = mysql_num_rows($boards);

    while ( $row = mysql_fetch_row($boards) ) {
        list($id,$catname,$catdesc,$catorder,$parent,$thumbs,$cheader,$cfooter,$cheadtags,$cprivate,$ugnoview,$ugnoupload,$ugnopost,$ugnoanno) = $row;

        //# Do the HTML for the usergroup access
        $ugviewblock = explode( ",", $ugnoview );
        $ugupblock = explode( ",", $ugnoupload );
        $ugpostblock = explode( ",", $ugnopost );
        $ugannoblock = explode( ",", $ugnoanno );

        $query = "SELECT groupid,groupname FROM usergroups";
        $resultug = mysql_query_eval($query,$link);

        while ( $ugrow = mysql_fetch_row($resultug) ) {
            list($groupid,$groupname) = $ugrow;

            $view_checked="CHECKED";
            $up_checked="CHECKED";
            $post_checked="CHECKED";
            $anno_checked="CHECKED";

            reset( $ugviewblock );
            while ( list($ignore,$vgid) = each($ugviewblock) ) {
                if ($groupid == $vgid) $view_checked="";
            }

            reset( $ugupblock );
            while ( list($ignore,$ugid) = each($ugupblock) ) {
                if ($groupid == $ugid) $up_checked="";
            }

            reset( $ugpostblock );
            while ( list($ignore,$pgid) = each($ugpostblock) ) {
                if ($groupid == $pgid) $post_checked="";
            }

            reset( $ugannoblock );
            while ( list($ignore,$agid) = each($ugannoblock) ) {
                if ($groupid == $agid) $anno_checked="";
            }

            $viewoptions .= "<input type=\"checkbox\" name=\"view-$groupid\" value=\"1\" $view_checked> $groupname<Br>";
            $upoptions .= "<input type=\"checkbox\" name=\"up-$groupid\" value=\"1\" $up_checked> $groupname<br>";
            $postoptions .= "<input type=\"checkbox\" name=\"post-$groupid\" value=\"1\" $post_checked> $groupname<br>";
            $annooptions .= "<input type=\"checkbox\" name=\"anno-$groupid\" value=\"1\" $anno_checked> $groupname<br>";
        }
        //# end usergroup access html

        if ($id != "500") {
            if ($thumbs == "yes") {
                $thumbopt = "<option selected>yes</option><option>no</option>";
            }
            else {
                $thumbopt = "<option selected>no</option><option>yes</option>";
            }

            if ($cprivate == "yes") {
                $privateopt = "<option selected>yes</option><option>no</option>";
            }
            else {
                $privateopt = "<option selected>no</option><option>yes</option>";
            }

            $adminup = "<tr><Td colspan=\"2\" bgcolor=\"#f7f7f7\"><font size=\"2\" face=\"verdana\">Only allow admin uploads?
                <br></font>
                <font size=\"1\" face=\"verdana\">(Set to yes, users won't be able to upload to this category.)</font></td><Td bgcolor=\"#f7f7f7\">
                <select name=\"private-$id\">$privateopt</select></td></tr>";

            $query = "SELECT catname FROM categories WHERE id=$parent";
            $catquery = mysql_query_eval($query,$link);
            $resultcat = mysql_fetch_array($catquery, MYSQL_ASSOC);
            $parname = $resultcat['catname'];
            mysql_free_result($catquery);

            if ($parent != "0") {
                $defaultopt = "<option selected value=\"$parent\">$parname</option>";
                catopt(0);
                $defaultopt .= "<option value=\"0\">None</option>";
            }
            else {
                $defaultopt = "<option selected value=\"0\">None</option>";
                catopt(0);
            }

            $thumbpar = "<tr><Td colspan=\"2\" bgcolor=\"#f7f7f7\"><font size=\"2\" face=\"verdana\"><Center>Contains Thumbnails? <select
                name=\"thumbs-$id\">$thumbopt</select></center></font></td>
                <Td bgcolor=\"#f7f7f7\"><font size=\"2\" face=\"verdana\"><Center>Parent: <select
                name=\"parent-$id\">$defaultopt$paropts</select></td></tr>";
        }
        else {
            $delete="";
            $addcat="";
        }

        $catname = str_replace( "\"", "&quot", $catname);
        $catdesc = str_replace( "\"", "&quot", $catdesc);

        $output .= "<Tr><Td bgcolor=\"#f7f7f7\"><center><input type=\"text\" size=\"4\" value=\"$catorder\" name=\"catorder-$id\"></td><Td
            bgcolor=\"#f7f7f7\"><input type=\"text\" size=\"50\"
            value=\"$catname\" name=\"catname-$id\"></td><Td bgcolor=\"#f7f7f7\"><input type=\"text\" size=\"50\" value=\"$catdesc\" name=\"description-$id\"></td></tr>";

        $output.= "
            $thumbpar
            <tr><Td colspan=\"2\" bgcolor=\"#f7f7f7\"><font size=\"2\" face=\"verdana\">Header Include File Path:
            <br></font>
            <font size=\"1\" face=\"verdana\">(leave blank to use admin setting)</font></td><Td bgcolor=\"#f7f7f7\">
            <input type=\"text\" name=\"header-$id\" value=\"$cheader\" size=\"50\"></td></tr>
            <tr><Td colspan=\"2\" bgcolor=\"#f7f7f7\"><font size=\"2\" face=\"verdana\">Headtags Include File Path:
            <br></font>
            <font size=\"1\" face=\"verdana\">(leave blank to use admin setting)</font></td><Td bgcolor=\"#f7f7f7\">
            <input type=\"text\" name=\"headtags-$id\" value=\"$cheadtags\" size=\"50\"></td></tr>
            <tr><Td colspan=\"2\" bgcolor=\"#f7f7f7\"><font size=\"2\" face=\"verdana\">Footer Include File Path:
            <br></font>
            <font size=\"1\" face=\"verdana\">(leave blank to use admin setting)</font></td><Td bgcolor=\"#f7f7f7\">
            <input type=\"text\" name=\"footer-$id\" value=\"$cfooter\" size=\"50\"></td></tr>
            $adminup
            <Tr><Td bgcolor=\"#FFFFFF\" colspan=\"3\"><br><font size=\"2\" face=\"verdana\"><B>Usergroup Access
            Permissions</b></font><br><font size=\"1\"
            face=\"verdana\">Note: If you disable a
            usergroup's uploads or posts in the
            Usergroups panel, the category specific settings below won't have an effect for that usergroup.</td></tr>
            <Tr><Td valign=\"top\" colspan=\"2\" bgcolor=\"#f7f7f7\"><font size=\"2\" face=\"verdana\">These checked usergroups can view
            images in this category.</td><Td bgcolor=\"#f7f7f7\">$viewoptions</td></tr>
            <Tr><Td valign=\"top\" colspan=\"2\" bgcolor=\"#f7f7f7\"><font size=\"2\" face=\"verdana\">These checked usergroups can upload
            to this category,<Br>unless a usergroup's overall upload access is disabled.</td><Td
            bgcolor=\"#f7f7f7\">$upoptions</td></tr>

            <Tr><Td valign=\"top\" colspan=\"2\" bgcolor=\"#f7f7f7\"><font size=\"2\" face=\"verdana\">These checked
            usergroups can post to this category,<Br>unless a usergroup's overall posting access is disabled.</td><Td
            bgcolor=\"#f7f7f7\">$postoptions</td></tr>";

        if ($Globals{'annotate'} == "yes") {
            $output .= "<Tr><Td valign=\"top\" colspan=\"2\" bgcolor=\"#f7f7f7\"><font size=\"2\" face=\"verdana\">These checked
                usergroups  will have their photos stamped with your overlay photo.</td><Td bgcolor=\"#f7f7f7\">$annooptions</td></tr>";
        }

        $output .= "

            </table></td></tr></table><p>

            <input type=\"hidden\" name=\"catid\" value=\"$id\">
            <input type=\"hidden\" name=\"ppaction\" value=\"cats\">
            <input type=\"hidden\" name=\"do\" value=\"process\">
            <input type=\"submit\" value=\"Save Changes\"></form></td></tr></table></td></tr></table>";
    }

    print "$output<p>".$Globals{'cright'}."<p>$footer";
    exit;
}


if ($ppaction == "cats") {
    if ($do == "process") {
        $doview = "";
        $doupload = "";
        $dopost = "";
        $doanno = "";

        foreach($HTTP_POST_VARS as $vid=>$setting) {
            $ugorder = explode("-", $vid);
            $ugtype=$ugorder[0];
            if ( count($ugorder) > 1 ) $ugid=$ugorder[1];

            //print "==[$ugtype][$ugid]==<br>";
            if ($ugtype == "view") {
                if ($setting==1) {
                    if ( $doview != "" ) $doview .= ",";
                    $doview .= "$ugid";
                }
            }
            elseif ($ugtype == "up") {
                if ($setting==1) {
                    if ( $doupload != "" ) $doupload .= ",";
                    $doupload .= "$ugid";
                }
            }
            elseif ($ugtype == "post") {
                if ($setting==1) {
                    if ( $dopost != "" ) $dopost .= ",";
                    $dopost .= "$ugid";
                }
            }
            elseif ($ugtype == "anno") {
                if ($setting==1) {
                    if ( $doanno != "" ) $doanno .= ",";
                    $doanno .= "$ugid";
                }
            }
            else {
                $query = "";
                //print "[$id][$vid][$ugid/$catid][$ugtype][$setting]<br>";
                if ( $ugid != "" ) {
                    if ( $ugtype != "private" && $ugtype != "thumbs" && $ugtype != "parent" )
                        $query = "UPDATE categories SET ".$ugtype."='$setting' WHERE id=$ugid";

                    if ($ugid != 500) {
                        if ($ugtype == "private" )
                            $query = "UPDATE categories SET private='$setting' WHERE id=$ugid";

                        if ($ugtype == "thumbs" )
                            $query = "UPDATE categories SET thumbs='$setting' WHERE id=$ugid";

                        if ($ugtype == "parent" ) {
                            childcheck($setting, $ugid);
                            $query = "UPDATE categories SET parent='$setting' WHERE id=$ugid";
                        }
                    }

                    if ($query != "") {
                        //print "[$query]<br>";
                        $resulta = mysql_query_eval($query,$link);
                    }
                }
            }
        }

        if ( $catid != "" ) {
            //print "[$doview][$doupload][$dopost][$doanno]<br>";
            $ugviewblock = explode( ",", $doview);
            $ugupblock = explode( ",", $doupload);
            $ugpostblock = explode( ",", $dopost);
            $ugannoblock = explode( ",", $doanno);

            $blockview = "";
            $blockup = "";
            $blockpost = "";
            $blockanno = "";

            $query = "SELECT groupid,groupname FROM usergroups";
            $resultg = mysql_query_eval($query,$link);

            while ( $grow = mysql_fetch_row($resultg) ) {
                list($groupid, $groupname) = $grow;
                $vthisid=0; $uthisid=0; $pthisid=0; $athisid=0;

                reset($ugviewblock);
                while ( list($ignore,$vgid) = each($ugviewblock) ) {
                    if ($groupid == $vgid) $vthisid="1";
                }

                reset($ugupblock);
                while ( list($ignore,$ugid) = each($ugupblock) ) {
                    if ($groupid == $ugid) $uthisid="1";
                }

                reset($ugpostblock);
                while ( list($ignore,$pgid) = each($ugpostblock) ) {
                    if ($groupid == $pgid) $pthisid="1";
                }

                reset($ugannoblock);
                while ( list($ignore,$agid) = each($ugannoblock) ) {
                    if ($groupid == $agid) $athisid="1";
                }

                if ($vthisid==0) {
                    if ( $blockview != "" ) $blockview .= ",";
                    $blockview .= "$groupid";
                }

                if ($uthisid==0) {
                    if ( $blockup != "" ) $blockup .= ",";
                    $blockup .= "$groupid";
                }

                if ($pthisid==0) {
                    if ( $blockpost != "" ) $blockpost .= ",";
                    $blockpost .= "$groupid";
                }

                if ($athisid==0) {
                    if ( $blockanno  != "" ) $blockanno .= ",";
                    $blockanno .= "$groupid";
                }
                //print "[$groupid][$groupname][$vthisid/$blockview][$uthisid/$blockup][$pthisid/$blockpost][$athisid/$blockanno]<br>";
            }

            $sql = "UPDATE categories SET ugnoview='$blockview',ugnoupload='$blockup',ugnopost='$blockpost',ugnoanno='$blockanno' WHERE id=$catid";
            $resultc = mysql_query_eval($sql,$link);
        }

        //print "[$sql]<br>";
        forward( $Globals{'maindir'}."/adm-cats.php?ppaction=cats", "Processing complete!" );
        exit;
    }

    //# Generate the edit categories HTML form

    $output = "$header<center><hr>

        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"2\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\"
        size=\"1\"><font size=\"2\" face=\"verdana\"><b>PhotoPost Category Editor</font>
        </font></td>
        </tr>
        <form method=\"post\" action=\"".$Globals{'maindir'}."/adm-cats.php\">
        <tr id=\"cat\">
        <td bgcolor=\"#f7f7f7\"><b>
        <font face=\"verdana, arial, helvetica\" size=\"2\" color=\"#000000\">$adminmenu</b></font></td></tr>
        <tr><td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana\"><Br><ul>(
        <a href=\"".$Globals{'maindir'}."/adm-cats.php?ppaction=addcat\">Add Top Level Category</a> )</font></ul>";

    catli(0);

    $output.= "<p><center>
        <input type=\"hidden\" name=\"ppaction\" value=\"cats\">
        <input type=\"hidden\" name=\"do\" value=\"process\">
        <input type=\"submit\" value=\"Save Order Changes\"></form></td></tr></table></td></tr></table>";

    print "$output<p>".$Globals{'cright'}."<p>$footer";
}

?>

