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
require "languages/$pplang/reportphoto.php";
require "login-inc.php";

list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();

if ( !isset($report) ) {
    diewell($Globals['pp_lang']['badcall']);
    exit;
}

authenticate();

if ( isset($Globals['ppboards']) && $adminedit != 1 ) {
    if ( $Globals['ppboards'] == "closed" ) {
        print $Globals['pp_lang']['closed'];
        exit;
    }
}

if (empty($final)) {
    topmenu();

    printheader( 0, $Globals['pp_lang']['reportphoto'] );

    $output = "<br /><table cellpadding=\"10\" cellspacing=\"0\" border=\"0\" width=\"{$Globals['tablewidth']}\" align=\"center\"><tr>    <td valign=\"middle\" width=\"50%\" nowrap>$menu2</td>
        <td width=\"50%\" align=\"right\" valign=\"middle\" nowrap>$menu&nbsp;</td></tr></table>
        <table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"{$Globals['bordercolor']}\"  width=\"{$Globals['tablewidth']}\"
        align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"{$Globals['headcolor']}\"><font face=\"{$Globals['headfont']}\" color=\"{$Globals['headfontcolor']}\" size=\"{$Globals['fontlarge']}\">{$Globals['galleryname']} {$Globals['pp_lang']['tool']}</font>
        </font></td>
        </tr>
        <form method=\"post\" action=\"{$Globals['maindir']}/reportphoto.php\">";

    $output .= "<tr><td bgcolor=\"{$Globals['maincolor']}\" width=\"50%\" align=\"center\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">
        {$Globals['pp_lang']['reportreason']}</font></td><td bgcolor=\"{$Globals['maincolor']}\" align=\"left\"><select name=\"reason\">";

    $output .= "<option value=\"Inappropriate material\">{$Globals['pp_lang']['inapprop']}</option>";
    $output .= "<option selected value=\"Copyright Infringement\">{$Globals['pp_lang']['copyright']}</option>";
    $output .= "<option value=\"Image in wrong Category\">{$Globals['pp_lang']['wrongcat']}</option>";
    $output .= "<option value=\"Other\">{$Globals['pp_lang']['other']}</option>";

    $output .= "</select></td></tr>
        <tr><td bgcolor=\"{$Globals['maincolor']}\" align=\"center\"><font size=\"{$Globals['fontmedium']}\" color=\"{$Globals['maintext']}\" face=\"{$Globals['mainfonts']}\">
        {$Globals['pp_lang']['moreinfo']}</td><td bgcolor=\"{$Globals['maincolor']}\" align=\"left\"><textarea
        name=\"desc\" cols=\"40\" rows=\"5\"></textarea></td></tr>
        <center>

        <tr><td colspan=\"2\" bgcolor=\"{$Globals['maincolor']}\"><font size=\"{$Globals['fontmedium']}\" face=\"{$Globals['mainfonts']}\"><center>
        <input type=\"hidden\" name=\"report\" value=\"$report\">
        <input type=\"hidden\" name=\"final\" value=\"yes\">
        <input type=\"submit\" value=\"{$Globals['pp_lang']['reportphoto']}\"></td></tr></table></td></tr></table><p>{$Globals['cright']}";

    print $output;
    printfooter();    
}
else {
    $letter = "$username has complained ($reason) about one of the photos in the database:\n\n";
    $letter .= "{$Globals['maindir']}/showphoto.php?photo=$report\n\n";
    $letter .= "with the following comments: \n\n$desc";

    $email = $Globals['adminemail'];
    $email_from = "From: {$Globals['adminemail']}";

    $subject = "Subject: {$Globals['webname']} User Reported Photo Complaint";
    $subject = trim($subject);

    mail( $email, $subject, $letter, $email_from );

    forward("", $Globals['pp_lang']['webnotice']);
}

?>
