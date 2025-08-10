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
function admin_email ( $ppactionvar, $photonum, $getuserid="", $phototitle="" ) {
    global $Globals, $link;

    if ( $getuserid == "" ) {
        $query = "SELECT userid, title FROM photos WHERE id=$photonum";
        $results = mysql_query_eval($query, $link);

        $rows = mysql_fetch_row($results);
        list( $getuserid, $phototitle ) = $rows;
    }

    list( $usernm, $useremail ) = get_username( $getuserid );

    if ( empty($useremail) ) return;

    $email_from = "From: ".$Globals{'adminemail'};

    if ($ppactionvar == "approve") {
        $letter="$usernm,

            We wanted to let you know that your photo, titled \"$phototitle\", has
            been approved and is now visible.  Here is the link to the photo:
            ".$Globals{'maindir'}."/showphoto.php?photo=$photonum

            And if you would like to view your personal photo album, containing all
            of the images that you have uploaded to ".$Globals{'webname'}.", you can do so here:
            ".$Globals{'maindir'}."/showgallery.php?user=$getuserid&thumb=1&cat=500

            Thanks!

            The ".$Globals{'webname'}." Team
            ".$Globals{'domain'};

        $subject = $Globals{'webname'}." photo upload approved";
    }

    if ($ppactionvar == "moved") {
        $letter="$usernm,

            We felt that your photo, titled \"$phototitle\", was more appropriate
            in a different category.  To view it, and to find out where
            we moved it (look in the upper left for the category name), visit
            this link:
            
            ".$Globals{'maindir'}."/showphoto.php?photo=$photonum

            Thanks!

            The ".$Globals{'webname'}." Team
            ".$Globals{'domain'};

        $subject = $Globals{'webname'}." photo category change";
    }

    if ($ppactionvar == "delete") {
        $letter="$usernm,

            I'm sorry, but the photo you submitted to ".$Globals{'webname'}.", titled
            \"$phototitle\", has been deleted.  Some reasons for photo deletions include:

            -Images that were partially uploaded/incomplete
            -Broken images
            -Extremely poor quality/images (impossible to make out the image itself)
            -Images that did not conform to our published site contribution and usage guidelines such as offensive images

            If you would like to submit another photo, please return to our photo upload form:
            ".$Globals{'maindir'}."/uploadphoto.php

            Thanks,

            The ".$Globals{'webname'}." Team
            ".$Globals{'domain'};

        $subject="Regarding your ".$Globals{'webname'}." photo upload";
    }

    mail( $useremail, $subject, $letter, $email_from );
} // end sub email

?>

