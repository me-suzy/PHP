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

list($sec,$min,$hour,$mday,$mon,$year,$wday,$yday,$isdst) = localtime();

if ( !IsSet($report) ) {
    dieWell("Script not called correctly.  Navigate to a specific photo, then click on the edit link.");
    exit;
}

authenticate();

if (empty($final)) {
    topmenu();

    $output = "<title>".$Globals{'galleryname'}." Photo Reporting Tool</title>$header
        <p><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"".$Globals{'tablewidth'}."\"><Tr>
        <Td valign=\"bottom\" width=\"50%\">&nbsp;</td>
        <td width=\"50%\" align=\"right\" valign=\"center\"><font face=\"verdana, arial\" size=\"2\">$menu</font></td></tr></table>
        <p><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"".$Globals{'bordercolor'}."\"  width=\"".$Globals{'tablewidth'}."\"
        align=\"center\"><tr><td>
        <table cellpadding=\"4\" cellspacing=\"1\" border=\"0\"  width=\"100%\">
        <tr align=\"center\">
        <td colspan=\"4\" align=\"left\" bgcolor=\"".$Globals{'headcolor'}."\"><font face=\"".$Globals{'headfont'}."\" color=\"".$Globals{'headfontcolor'}."\" size=\"4\">".$Globals{'galleryname'}." Reporting
        Tool</font>
        </font></td>
        </tr>
        <form method=\"post\" action=\"".$Globals{'maindir'}."/reportphoto.php\">";

    $output .= "<tr><Td bgcolor=\"".$Globals{'maincolor'}."\" width=\"50%\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">
        Reason for reporting?</font></td><Td bgcolor=\"".$Globals{'maincolor'}."\"><select name=\"reason\">";

    $output .= "<option value=\"Inappropriate material\">Inappropriate material</option>";
    $output .= "<option selected value=\"Copyright Infringement\">Copyright Infringement</option>";
    $output .= "<option value=\"Image in wrong Category\">Image in wrong Category</option>";
    $output .= "<option value=\"Other\">Other</option>";

    $output .= "</select></td></tr>
        <tr><Td bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" color=\"".$Globals{'maintext'}."\" face=\"verdana,arial\">
        More information (if needed)?</td><td bgcolor=\"".$Globals{'maincolor'}."\"><textarea
        name=\"desc\" cols=\"40\" rows=\"5\"></textarea></td></tr>
        <Center>

        <Tr><Td colspan=\"2\" bgcolor=\"".$Globals{'maincolor'}."\"><font size=\"2\" face=\"verdana,arial\"><center>
        <input type=\"hidden\" name=\"report\" value=\"$report\">
        <input type=\"hidden\" name=\"final\" value=\"yes\">
        <input type=\"submit\" value=\"Report Photo\"></td></tr></table></td></tr></table><p>".$Globals{'cright'}."$footer";

    print $output;
}
else {
    $letter = "$username has complained ($reason) about one of the photos in the database:\n\n";
    $letter .= $Globals{'maindir'}."/showphoto.php?photo=$report\n\n";
    $letter .="with the following comments: \n\n$desc";

    $email = $Globals{'adminemail'};
    $email_from = "From: ".$Globals{'adminemail'};

    $subject="Subject: ".$Globals{'webname'}." User Reported Photo Complaint";
    $subject=trim($subject);

    mail( $email, $subject, $letter, $email_from );

    forward("", "The photo you reported has generated a notice to the webmaster.");
}

?>

