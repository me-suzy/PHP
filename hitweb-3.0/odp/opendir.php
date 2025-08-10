<?

/************************************************************************/
/* PHP-NUKE ADDON: ODP Link Importer                                    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2001 by Jim Ribar (jimmacr@optonline.net)              */
/* http://www.aeolus.wox.org                                            */
/*                                                                      */    
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

/************************************************************************/
/* Configuration:                                                       */
/************************************************************************/

	$name  = "DMOZ IMPORT";
		// The Name To Use To Submit Links Under
	$email = "webmaster@hitweb.org";
		// The Email To Use To Submit Links Under
       //if(!$DMOZPATH) { $DMOZPATH = "/Computers/"; }
       if(!$DMOZPATH) { $DMOZPATH = "/World/Français/Informatique/Programmation/Langages/PHP/"; }
	
        	// Set $DMOZPATH to the path of the default directory. 
			// NOTE:  / does not work for unknown reasons.  You must use a caterogry off of /,
			// such as /Computers/, /Arts/, and /Business/.  Also, you must start and end the 
			// line with a /
		

/************************************************************************/
/* Do Not Edit Below This Line                                          */
/************************************************************************/

// Include The Required Files
//include("mainfile.php");
//include("config.php");
include("header.php");
include("phpodp.php");


// Set Some Other Vars
$scriptpath = $SCRIPT_NAME;

// Open The Table
//OpenTable();

/* Suppression pour hitweb car ceci sert pour phpnuke
if ($Action == "Process") {

	// Get The Info From Dmoz
	$odp = new phpODP;
	$odp->_init();
	$links = $odp->links;
	
	// Seperate The Nuke Category ID
	$cat = explode("-", $cat); if ($cat[1]=="") { $cat[1] = 0; }
	
	// Remove The <UL> Tags
	$links = ereg_replace ("<ul>", "", $links);
	$links = ereg_replace ("</ul>", "", $links);
	
	// Split Up The Links Into An Array
	$links = explode ("<li>", $links);

	// Print The Header
	echo "<center>The Following Links Have Been Added To ";
    $result=mysql_query("select cid, title from links_categories where cid=$cat[0] order by title");
    list($cid, $title) = mysql_fetch_row($result);
    echo "$title ";
	$result2=mysql_query("select sid, title from links_subcategories where sid=$cat[1] order by title");
	list($sid, $stitle) = mysql_fetch_row($result2);
    if ($sid) { echo "/ $stitle"; }
    echo "</center>";
	
	// Start The Table
	echo "<table><tr><td><b>Title</b></td><td><b>Description</b></td></tr>";
	
	// Loop Through All The Links 
	for($index=0; $index < count($links); $index++) {

		// If the line is empty, don't proccess it
		if ($links[$index] == "") {} else {
		
		// Set The Line For Easier Processing
		$linkstring = $links[$index];

		// Remove Extra Unneeded Tags
		$linkstring = preg_replace ("/<img.*.> &nbsp; \n /", "", $linkstring);
		$linkstring = ereg_replace ("<b>", "", $linkstring);
		$linkstring = ereg_replace ("<\/b>", "", $linkstring);
		$linkstring = ereg_replace ("\<li\>\<a href\=\"", "", $linkstring);
		$linkstring = ereg_replace ("\"\>", "\t", $linkstring);
		$linkstring = ereg_replace ("\<\/a\> - ", "\t", $linkstring);
		$linkstring = ereg_replace ("\<\/a\>", "\t", $linkstring);
		$linkstring = ereg_replace ("\<p\>", "", $linkstring);

		// Split Up The Link By The Tabs
		$linkarray = explode ("\t", $linkstring);
	
		// Set The Array Contents To Individual Vars
		list($url, $title, $description) = $linkarray;
		





		//
		// A REVOIR L'ENREGISTREMENT DANS LA BASE....
		//
		//





		// Another If statement to prevent the addition of blank links
		//if ($title != "") {

		    // Clean up the links for inserting into the database
		    $title = stripslashes(FixQuotes($title));
		    $url = stripslashes(FixQuotes($url));
		    $url = ereg_replace ("<a href=\"", "", $url);
		    $description = stripslashes(FixQuotes($description));

			// Insert into the database
		    //mysql_query("insert into links_links values (NULL, '$cat[0]', '$cat[1]', '$title', '$url', '$description', now(), '$name', '$email', '0','0.0.0.0',0,0,0)");

			// Print Out The Link
		       echo "<tr><td><font size=2><a href=\"$url\">$title</a></font></td><td><font size=2>$description</font></td></tr>";
		    //echo $title." ".$url." ".$url." ".$description;
		    //echo "<tr><td><font size=2>$url</td></tr>";
		    //}


		
		}
		}
	
	// Close the table
	echo "</table><br><center>Click <a href=\"".$scriptpath."\">Here</a> To Add Additional Links</center>";

} else {
*/

	// Get The Info From Dmoz
	$odp = new phpODP;
	$odp->_init();
	$links = $odp->links;

	// Get The Links Categories and Split Up Each Line For Processing.
	$dmozcats = $odp->catagories;
	$dmozcats = explode ("\n", $dmozcats);

	// Get the clean page title and remove the open directory from the title
	$pagetitle = $odp->page_title;
	$pagetitle = ereg_replace("Open Directory - ", "", $pagetitle);

	// Print The Header, And Start The Form
	//echo "<center>The links will be added from \"$pagetitle\" to the database, or select another category you wish to gather the links from:</center>";
echo "<center><font size=5><u>$pagetitle</u></font></center>";	
        
	echo "<form action=$scriptpath method=get>";
	echo "<input type=\"hidden\" name=\"DMOZPATH\" value=\"$DMOZPATH\"><font size=\"2\">";
	
	// Loop Through Each Line
	for($index=0; $index < count($dmozcats); $index++) {
	
		//  Set The Line To A Var, For Easier Processing
		$line = $dmozcats[$index];
		
		// Use regular expressions to clean up the links
		

//  J'ai personellement supprimer ceci pour ne pas avoir le liens
//  vers 
//  $line = preg_replace("/<a href=\"\/(.*)\">/", "<a href=\"\\1\">", $line);


		$line = ereg_replace("<td>", "<td><font size=2>", $line);
		$line = ereg_replace("</td>", "</td><font>", $line);
		$line = ereg_replace("<td valign=top>", "<td valign=top><font size=2>", $line);
		$line = ereg_replace("<b>", "", $line);
		$line = ereg_replace("</b>", "", $line);

		// Print out the line
		echo "$line";
		
	}
	
	// Print The Next Section Header
	//echo "</font><hr><center>The following links exist in this category, and will be added to the category of your choice below:</center>";
	echo "<hr>";
	
	// Get A Copy Of All The Links In The Section
	$links = $odp->links;	
	
	// Print Out All The Links
	echo "<font size=2>$links</font>";
	
	// Print Out The Next Section Header
	//echo "<hr><center>Please select the category you wish to add the links to:</center><br>";



/*	
	// Build A List Of All The Categories In The Nuke Database
    $result=mysql_query("select cid, title from links_categories order by title");
    echo "<center><select name=cat>";	
    while(list($cid, $title) = mysql_fetch_row($result)) {
		echo "<option value=$cid>$title</option>";
		$result2=mysql_query("select sid, title from links_subcategories where cid=$cid order by title");
		
		while(list($sid, $stitle) = mysql_fetch_row($result2)) {
    		echo "<option value=$cid-$sid>$title / $stitle</option>";
		}
    }
    echo "</select></center><br><br>";
    
    
    // Finish The Form
    echo "<center><input type=\"submit\" name=\"Action\" value=\"Process\"><input type=\"reset\" name=\"Action\" value=\"Reset\"></form></center>";
    
}

    // Close The Table, and Print Out The Footer
    CloseTable();
	include("footer.php");

*/

include "footer.php";
?>
