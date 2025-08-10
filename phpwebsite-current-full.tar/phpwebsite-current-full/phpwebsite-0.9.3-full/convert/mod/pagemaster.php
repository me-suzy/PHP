<?php

/* Security Check **********************************/
$correctPath = $core->source_dir . "convert/index.php";
if($_SERVER['SCRIPT_FILENAME'] != $correctPath) {
  header("Location: ../index.php");
  exit();
}
/***************************************************/

/* Edit this to point to your old images directory for userpages */
define("OLD_USERPAGE_IMAGES", OLD_SITE_DIR . "mod/userpage/images/");
define("OLD_MAINPAGE_IMAGES", OLD_SITE_DIR . "mod/mainpage/images/");

/* New directory for pagemaster images. Don't edit */
define("NEW_IMAGE_DIR", PHPWS_HOME_DIR . "images/pagemaster/");

/* Include new pagemaster classes */
require_once("../mod/pagemaster/class/Page.php");
require_once("../mod/pagemaster/class/Section.php");

/* Create a small data structure to mimick the old userpages */
class userpage {
  var $new_page;
  var $editing_page;
  var $text_mode;
  var $page_active;
  var $sub_active;
  var $text_active;
  var $image_active;
  var $main_title;
  var $subtitle;
  var $text;
  var $imagename;
  var $alt;
  var $layout;
  var $sections;
  var $viewblocks;
}

/* Create a small data structure to mimick old mainpages */
class mainpage {
  var $title;
  var $text;
  var $image;
  var $alt;
  var $image_active;
}

/* Connect to old v0.8.x database */
$db = old_connect();

/* Get all the pages from old database */
$page_result = $db->getAll("SELECT * FROM " . OLD_PREFIX . "mod_userpage_data");

if(DB::isError($page_result)) {
  echo PHPWS_Debug::testObject($page_result);
  exit("Error selecting from " . OLD_PREFIX . "mod_userpage_data in database " . OLD_DBNAME . "!");
}

/* Select the mainpage for coversion to a pagemaster page */
$mainpage_result = $db->getAll("SELECT * FROM " . OLD_PREFIX . "mod_mainpage_data");

if(DB::isError($mainpage_result)) {
  echo PHPWS_Debug::testObject($mainpage_result);
  exit("Error selecting from " . OLD_PREFIX . "mod_mainpage_data in database " . OLD_DBNAME . "!");
}

/* Connect to the new v0.9.x database */
$db = new_connect();

/* Prepare the insert statements for pages and sections */
$page_insert = $db->prepare("INSERT INTO " . NEW_PREFIX . "mod_pagemaster_pages VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$section_insert = $db->prepare("INSERT INTO " . NEW_PREFIX . "mod_pagemaster_sections VALUES (?, ?, ?, ?, ?, ?)");

/* Check to see if any pages were found */
if(count($page_result)) {

  foreach($page_result as $row) {
  
    /* Create an old page object to store data temporarily */
    $old_page = new userpage;
    $old_page = unserialize($row["data"]);

    /* Create a new page object to transfer old data into */
    $new_page = new PHPWS_Page;

    foreach($old_page->subtitle as $key=>$subtitle) {

      /* Check to see if this section was empty or not */
      if(!$subtitle && !$old_page->text[$key] && !$old_page->image_name[$key]) {
	continue;
      } else {

	/* Create a new section to transfer old data into */
	$current_section = new PHPWS_Section;

	if($subtitle)
	  $current_section->title = $subtitle;

	if($old_page->text[$key])
	  $current_section->text = $old_page->text[$key];

	/* Get the id for the current section */
	$current_section->id = $db->nextId(NEW_PREFIX . "mod_pagemaster_sections");
	$current_section->template = newLayout($old_page->layout, count($new_page->order)+1);

	/* Initialize and setup data array for insertion into database */
	$section_data = array();
	$section_data[] = $current_section->id;
	$section_data[] = $new_page->id;
	$section_data[] = $current_section->title;
	$section_data[] = $core->stripSlashQuotes($current_section->text);

	$image = array();

	if($old_page->imagename[$key] && is_file(OLD_USERPAGE_IMAGES . $old_page->imagename[$key])) {
	  $imageSize = getimagesize(OLD_USERPAGE_IMAGES . $old_page->imagename[$key]);

	  $image["width"] = $imageSize[0];
	  $image["height"] = $imageSize[1];
	  $image["name"] = $old_page->imagename[$key];
	  $image["alt"] = $old_page->alt[$key];

	  $core->fileCopy(OLD_USERPAGE_IMAGES . $old_page->imagename[$key], NEW_IMAGE_DIR, $old_page->imagename[$key], 1, 1);
	}

	$section_data[] = serialize($image);
	$section_data[] = $current_section->template;
	$section_result = $db->execute($section_insert, $section_data);

	if(DB::isError($section_result)) {
	  echo PHPWS_Debug::testObject($section_result);
	  exit("There was an error when inserting section data into the database " . NEW_DBNAME . "!");
	} else {
	  $new_page->order[] = $current_section->id;
	}
      }
    }// END SECTION LOOP

    /* Get new id for the current page */
    $new_page->id = $db->nextId(NEW_PREFIX . "mod_pagemaster_pages");

    /* Setup page id array for menu conversion use */
    $GLOBALS["page_ids"][$row["id"]] = $new_page->id;

    /* Initialize and setup data array for insertion into database */
    $date = $row["last_update"];
    $year = substr($date, 0, 4);
    $month = substr($date, 4, 2);
    $day = substr($date, 6, 2);

    $page_data = array();
    $page_data[] = $new_page->id;
    $page_data[] = $row["title"];
    $page_data[] = "default.tpl";
    $page_data[] = serialize($new_page->order);
    $page_data[] = 0;
    $page_data[] = 0;
    $page_data[] = 1;
    $page_data[] = 0;
    $page_data[] = 1;
    $page_data[] = "converted";
    $page_data[] = "converted";
    $page_data[] = date("Y-m-d H:i:s", mktime(12,0,0, $month, $day, $year));
    $page_data[] = date("Y-m-d H:i:s", mktime(12,0,0, $month, $day, $year));
    $page_data[] = 0;
    $page_data[] = 0;

    $page_result = $db->execute($page_insert, $page_data);

    if(DB::isError($page_result)) {
      echo PHPWS_Debug::testObject($page_result);
      exit("There was an error when inserting page data into the database " . NEW_DBNAME . "!");
    }

  }// END PAGE LOOP

} else {
  echo "No userpages were found for conversion in database " . OLD_DBNAME . "!<br />";
}

/* Check to see if a mainpage was found */
if(count($mainpage_result)) {
  /* Create an old userpage object to store data */
  $old_mainpage = new mainpage;
  $old_mainpage = unserialize($mainpage_result[0]["data"]);

  //echo PHPWS_Debug::testObject($old_mainpage);

  /* Check to see if this section was empty or not */
  if(!$old_mainpage->title && !$old_mainpage->text && !$old_mainpage->image) {
    echo "Found empty mainpage! Continuing...<br />";
  } else {
    /* Unset old mainpage */
    $result = $db->query("UPDATE " . NEW_PREFIX . "mod_pagemaster_pages SET mainpage='0'");

    /* Get next available section id */
    $sect_id = $db->nextId(NEW_PREFIX . "mod_pagemaster_sections");

    /* Initialize and setup data array for insertion into database */
    $section_data = array();
    $section_data[] = $sect_id;
    $section_data[] = $new_page->id;
    $section_data[] = $old_mainpage->title;
    $section_data[] = $core->stripSlashQuotes($old_mainpage->text);

    $image = array();

    if($old_mainpage->image && file_exists(OLD_MAINPAGE_IMAGES . $old_mainpage->image)) {
      $imageSize = getimagesize(OLD_MAINPAGE_IMAGES . $old_mainpage->image);

      $image["width"] = $imageSize[0];
      $image["height"] = $imageSize[1];
      $image["name"] = $old_mainpage->image;
      $image["alt"] = $old_mainpage->alt;
    }

    $section_data[] = serialize($image);
    $section_data[] = newLayout($old_page->layout, count($new_page->order)+1);

    $section_result = $db->execute($section_insert, $section_data);

    if(DB::isError($section_result)) {
      echo PHPWS_Debug::testObject($section_result);
      exit("There was an error when inserting section data into the database " . NEW_DBNAME . "!");
    } else {
      $order[] = $sect_id;
    }
  }

  /* Get next available page id */
  $page_id = $db->nextId(NEW_PREFIX . "mod_pagemaster_pages");

  /* Setup page id array for menu conversion use */
  $GLOBALS["page_ids"]["mainpage"] = $page_id;

  /* Initialize and setup data array for insertion into database */
  $page_data = array();
  $page_data[] = $page_id;
  $page_data[] = $old_mainpage->title;
  $page_data[] = "default.tpl";
  $page_data[] = serialize($order);
  $page_data[] = 0;
  $page_data[] = 0;
  $page_data[] = 1;
  $page_data[] = 1;
  $page_data[] = 1;
  $page_data[] = "converted";
  $page_data[] = "converted";
  $page_data[] = date("Y-m-d H:i:s", $row["last_update"]);
  $page_data[] = date("Y-m-d H:i:s", $row["last_update"]);
  $page_data[] = 0;
  $page_data[] = 0;
    
  $page_result = $db->execute($page_insert, $page_data);

  if(DB::isError($page_result)) {
    echo PHPWS_Debug::testObject($page_result);
    exit("There was an error when inserting page data into the database " . NEW_DBNAME . "!");
  }

} else {
  echo "No mainpage found for conversion in database " . OLD_DBNAME . "!<br />";
}

echo "<h3>PageMaster Conversion Complete!</h3>";
//echo PHPWS_Debug::testArray($GLOBALS["page_ids"]);

writeReroute("pagemaster", $GLOBALS['page_ids']);

/**
 * This function converts the old template styles to the new.
 *
 * We need to set section templates depending on what layout the userpage
 * was using and which section number in order we're on.  The Layouts
 * go as follows:
 *
 * 1,4) Transfer to image_right.tpl.  All sections use the same template.
 *
 * 2,3) Transfer to image_left.tpl.  All sections use the same template.
 *
 * 5) Odd Sections are image_right.  Even Sections are image_left.
 *
 * 6) Odd Sections are image_left.  Even Sections are image_right.
 *
 * @author Eloi George <eloi@NOSPAM.bygeorgeware.com>
 * @module userpages->pagemaster conversion script
 * @param int layout : Layout id of this page.
 * @param int position : Section's position in the section_order array.
 * @return string : Name of equivalent layout
 **/
 function newLayout (&$layout, $position)
 {
    /* Determine is this section's position is even or odd-numbered */
    $odd = fmod($position, 2);

    /* Layouts 1 or 4 */
    if ($layout==1 || $layout==4)
        return "image_right.tpl";
    /* Layouts 2 or 3 */
    if ($layout==2 || $layout==3)
        return "image_left.tpl";
    /* Layout 5 */
    if ($layout==5)
        if ($odd)
            return "image_right.tpl";
        else
            return "image_left.tpl";
    /* Layout 6 */
    if ($layout==6)
        if ($odd)
            return "image_left.tpl";
        else
            return "image_right.tpl";
    /* If all else fails, give it the default template */
    return "default.tpl";
 }

?>
