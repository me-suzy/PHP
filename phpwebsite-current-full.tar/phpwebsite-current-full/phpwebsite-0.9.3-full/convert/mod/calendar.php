<?php

/**
 * Conversion Script For Calendar
 *
 * Converts 0.8.x Calendar to 0.9.x Calendar
 */

/***
 * Images note
 *
 * If you want to convert the images from calendar, you will need to
 * enter the EXACT directory where they are found below. If the conversion
 * cannot find the file, the category or event will be created without it.
 *
 */

$imageDir = OLD_SITE_DIR . "images/calendar/";

// Leave these directories as is
$fatcatDir = PHPWS_HOME_DIR . "images/fatcat/images/";
$calendarDir = PHPWS_HOME_DIR . "images/calendar/";


/* Security Check **********************************/
$correctPath = $core->source_dir . "convert/index.php";
if($_SERVER["SCRIPT_FILENAME"] != $correctPath) {
  header("Location: ../index.php");
  exit();
}
/***************************************************/
require (PHPWS_SOURCE_DIR . "mod/calendar/class/Calendar.php");

if (in_array("fatcat", $currentModules))
     $fatcat = TRUE;
     
     $db = old_connect();

     $sql_categories = $db->getAll("SELECT * FROM " . OLD_PREFIX . "mod_calendar_category ORDER BY cat_id");
     if(DB::isError($sql_categories)) {
       echo PHPWS_Debug::testObject($sql_categories);
       exit("Error selecting from old calendar categories table in database " . OLD_DBNAME . "!");
     }

$sql_subcat     = $db->getAll("SELECT * FROM " . OLD_PREFIX . "mod_calendar_subcat ORDER BY subcat_id");
if(DB::isError($sql_subcat)) {
  echo PHPWS_Debug::testObject($sql_subcat);
  exit("Error selecting from old calendar subcat table in database " . OLD_DBNAME . "!");
}

$sql_locations  = $db->getAll("SELECT * FROM " . OLD_PREFIX . "mod_calendar_location ORDER BY loc_id");
if(DB::isError($sql_locations)) {
  echo PHPWS_Debug::testObject($sql_locations);
  exit("Error selecting from old calendar locations table in database " . OLD_DBNAME . "!");
}

$sql_events     = $db->getAll("SELECT * FROM " . OLD_PREFIX . "mod_calendar_events ORDER BY event_id");
if(DB::isError($sql_events)) {
  echo PHPWS_Debug::testObject($sql_events);
  exit("Error selecting from old calendar events table in database " . OLD_DBNAME . "!");
}


$db = new_connect();
$category_insert = $db->prepare("INSERT INTO " . NEW_PREFIX . "mod_fatcat_categories VALUES (?, ?, ?, ?, ?, ?, ?, ?)");


if ($fatcat && $sql_locations){
  $loc_id = $db->nextId(NEW_PREFIX . "mod_fatcat_categories");
  
  $loc_data[] = $loc_id; // id
  $loc_data[] = "Locations"; //title
  $loc_data[] = NULL; //description
  $loc_data[] = "default.tpl"; //template
  $loc_data[] = NULL; //image
  $loc_data[] = NULL; //icon
  $loc_data[] = 0; //parent
  $loc_data[] = NULL; //children
  
  $result = $db->execute($category_insert, $loc_data);
  
  if(DB::isError($result)) {
    echo PHPWS_Debug::testObject($result);
    exit("There was a problem inserting category data into the database " . NEW_DBNAME . "!");
  }


  foreach ($sql_locations as $location){
    $imageSize = $fileDir = NULL;
    if (!($cat_id = duplicateFatcat($location['loc_name']))){
      $cat_id = $db->nextId(NEW_PREFIX . "mod_fatcat_categories");
      $loc_data = array();
  
      $loc_data[] = $cat_id; // id
      $loc_data[] = $location["loc_name"]; //title
      $loc_data[] = $location["loc_desc"]; //description
      $loc_data[] = "default.tpl"; //template

      if (!empty($location['image_name'])){
	$fileDir = $imageDir . $location['image_name'];
	if (file_exists($fileDir)){
	  if (isset($holdSize[$location["image_name"]]))
	    $imageSize = $holdSize[$location["image_name"]];
	  else {
	    $imageSize = getimagesize($fileDir);
	    $holdSize[$location["image_name"]] = array($imageSize[0], $imageSize[1]);
	  }
	  if (($imageSize[0] + $imageSize[1]) > 2){
	    $loc_data[] = $location["image_name"] . ":" . $imageSize[0]  . ":" . $imageSize[1]; //image
	    $core->fileCopy($fileDir, $fatcatDir, $location['image_name'], 1, 1);
	  } else
	    $loc_data[] = NULL;
	} else
	  $loc_data[] = NULL;
      } else
 	  $loc_data[] = NULL;

      $loc_data[] = NULL; //icon
      $loc_data[] = $loc_id; //parent
      $loc_data[] = NULL; //children

      $result = $db->execute($category_insert, $loc_data);
 
      if(DB::isError($result)) {
	echo PHPWS_Debug::testObject($result);
	exit("There was a problem inserting category data into the database " . NEW_DBNAME . "!");
      }
    }
    $locID[$location["loc_id"]] = $cat_id;
    $locKids[] = $cat_id;
  }
  $db->query("update " . NEW_PREFIX . "mod_fatcat_categories set children='" . implode(":", $locKids) . "' where cat_id=$loc_id");  
}

if ($fatcat && $sql_categories){
  foreach ($sql_categories as $category){
    $imageSize = $fileDir = NULL;
    if (!($cat_id = duplicateFatcat($category['category']))){
      $cat_id = $db->nextId(NEW_PREFIX . "mod_fatcat_categories");
      $cat_data = array();
      
      $cat_data[] = $cat_id; // id
      $cat_data[] = $category["category"]; //title
      $cat_data[] = $category["description"]; //description
      $cat_data[] = "default.tpl"; //template

      if (!empty($category['image_name'])){
	$fileDir = $imageDir . $category['image_name'];
	if (file_exists($fileDir)){
	  if (isset($holdSize[$category["image_name"]]))
	    $imageSize = $holdSize[$category["image_name"]];
	  else {
	    $imageSize = getimagesize($fileDir);
	    $holdSize[$category["image_name"]] = array($imageSize[0], $imageSize[1]);
	  }
	  if (($imageSize[0] + $imageSize[1]) > 2){
	    $cat_data[] = $category["image_name"] . ":" . $imageSize[0]  . ":" . $imageSize[1]; //image
	    $core->fileCopy($fileDir, $fatcatDir, $category['image_name'], 1, 1);
	  } else
	    $cat_data[] = NULL;
	} else
	  $cat_data[] = NULL;
      } else
 	  $cat_data[] = NULL;

      $cat_data[] = NULL; //icon
      $cat_data[] = 0; //parent
      $cat_data[] = NULL; //children
      
      $result = $db->execute($category_insert, $cat_data);
 
      if(DB::isError($result)) {
	echo PHPWS_Debug::testObject($result);
	exit("There was a problem inserting category data into the database " . NEW_DBNAME . "!");
      }
    }
    $catID[$category["cat_id"]] = $cat_id;
  }
}

if ($fatcat && $sql_subcat){
  foreach ($sql_subcat as $subcat){
    $imageSize = $fileDir = NULL;
    if (!($cat_id = duplicateFatcat($subcat['subcat']))){
      $cat_id = $db->nextId(NEW_PREFIX . "mod_fatcat_categories");
      $subcat_data = array();
      
      $subcat_data[] = $cat_id; // id
      $subcat_data[] = $subcat["subcat"]; //title
      $subcat_data[] = $subcat["description"]; //description
      $subcat_data[] = "default.tpl"; //template

      if (!empty($subcat['image_name'])){
	$fileDir = $imageDir . $subcat['image_name'];
	if (file_exists($fileDir)){
	  if (isset($holdSize[$subcat["image_name"]]))
	    $imageSize = $holdSize[$subcat["image_name"]];
	  else {
	    $imageSize = getimagesize($fileDir);
	    $holdSize[$subcat["image_name"]] = array($imageSize[0], $imageSize[1]);
	  }
	  if (($imageSize[0] + $imageSize[1]) > 2){
	    $subcat_data[] = $subcat["image_name"] . ":" . $imageSize[0]  . ":" . $imageSize[1]; //image
	    $core->fileCopy($fileDir, $fatcatDir, $subcat['image_name'], 1, 1);
	  } else
	    $subcat_data[] = NULL;
      	} else
	  $subcat_data[] = NULL;
      } else
 	  $subcat_data[] = NULL;

      $subcat_data[] = $subcat["image_name"]; //icon
      
      if ($subcat["cat_id"]){
	$parent = $catID[$subcat["cat_id"]];
	$subcat_data[] = $parent; //parent
	
	if (is_null($childrenList) || (is_array($childrenList[$parent]) && !in_array($cat_id, $childrenList[$parent])))
	$childrenList[$parent][] = $cat_id;
      } else
	$subcat_data[] = 0;
      
      $subcat_data[] = NULL;
      
      $result = $db->execute($category_insert, $subcat_data);
      
      if (is_array($childrenList)){
	foreach ($childrenList as $parent_id=>$childArray)
	  $db->query("update " . NEW_PREFIX . "mod_fatcat_categories set children='" . implode(":", $childArray) . "' where cat_id=$parent_id");
	
      }
      
      if(DB::isError($result)) {
	echo PHPWS_Debug::testObject($result);
	exit("There was a problem inserting subcat data into the database " . NEW_DBNAME . "!");
      }
    }
    $subID[$subcat["subcat_id"]] = $cat_id;
  }
}

$event_insert    = $db->prepare("INSERT INTO " . NEW_PREFIX . "mod_calendar_events VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$repeat_insert   = $db->prepare("INSERT INTO " . NEW_PREFIX . "mod_calendar_repeats VALUES (?, ?, ?)");
$element_insert = $db->prepare("INSERT INTO " . NEW_PREFIX . "mod_fatcat_elements VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if ($sql_events){
  foreach ($sql_events as $event){
    $event_id = $db->nextId(NEW_PREFIX . "mod_calendar_events");
    $imageSize = $fileDir = NULL;
    $event_data = array();

    $event_data[] = $event_id; // id
    $event_data[] = $event["title"]; // title
    $event_data[] = $event["description"]; // description

    if ($event["allday"] == 1)
      $event_data[] = -1; // startTime
    else
      $event_data[] = $event["starttime"]; // startTime


    $event_data[] = $event["endtime"]; // endTime
    $startDate = $event_data[] = $event["event_date"]; // startDate
    $event_data[] = $event["event_date"]; // endDate

    if ($event["allday"] == 1)
      $event_data[] = "allday"; // eventType
    elseif ($event["endtime"] == 9999)
      $event_data[] = "start";
    else
      $event_data[] = "interval";

    $event_data[] = NULL; // groups
    $event_data[] = NULL; // pmChoice
    $event_data[] = 0; // pmID
    $event_data[] = "default.tpl"; // template

    if (!empty($event['image_name'])){
      $fileDir = $imageDir . $event['image_name'];
      if (file_exists($fileDir)){
	if (isset($holdSize[$event["image_name"]]))
	  $imageSize = $holdSize[$event["image_name"]];
	else {
	  $imageSize = getimagesize($fileDir);
	  $holdSize[$event["image_name"]] = array($imageSize[0], $imageSize[1]);
	}

	if (($imageSize[0] + $imageSize[1]) > 2){
	  $event_data[] = $event['image_name'] . ":" . $imageSize[0]  . ":" . $imageSize[1]; //image
	  $core->fileCopy($fileDir, $calendarDir, $event['image_name'], 1, 1);
	} else
	  $event_data[] = NULL;
      } else
	$event_data[] = NULL;
    } else
      $event_data[] = NULL;

    $event_data[] = 1; // active


    if ($event["repeat_type"]){
      $endRepeat = $event_data[] = $event["end_repeat"]; // endRepeat
      switch ($event["repeat_type"]){
      case "day":
	$repeatType = $event_data[] = "daily";
	$event_data[] = NULL;
	$event_data[] = NULL;
	$event_data[] = NULL;
	break;
	
      case "week":
	$repeatType = $event_data[] = "weekly";
	$event_data[] = NULL;
	if ($event["repeat_week_day"])
	  $event_data[] = $event["repeat_week_day"];
	$repeatWeekdays = explode(":", $event["repeat_week_day"]);
	$event_data[] = NULL;
	break;
	
      case "month":
	$repeatType = $event_data[] = "monthly";
	if ($event["month_repeat_type"]== "day")
	  $monthMode = $event_data[] = "date";
	else
	  $monthMode = $event_data[] = "end";
	$event_data[] = NULL;
	$event_data[] = NULL;
	break;
	
      case "year":
	$repeatType = $event_data[] = "yearly";
	$event_data[] = NULL;
	$event_data[] = NULL;
	$event_data[] = NULL;
	break;
      }
    } else {
      $repeatType = NULL;
      $event_data[] = 0;    // endRepeat
      $event_data[] = NULL; // repeatMode
      $event_data[] = NULL; // monthMode
      $event_data[] = NULL; // repeatWeekdays
      $event_data[] = NULL; // every
    }

    $result = $db->execute($event_insert, $event_data);
 
    if(DB::isError($result)) {
      echo PHPWS_Debug::testObject($result);
      exit("There was a problem inserting category data into the database " . NEW_DBNAME . "!");
    }

    if ($repeatType){
      $countDate = new Date;
      $stopDate = new Date;

      PHPWS_Calendar::splitDateObject($countDate, $startDate);
      PHPWS_Calendar::splitDateObject($stopDate, $endRepeat);

      while ($stopDate->after($countDate) || $stopDate->equals($countDate)){
	$setDate = $countDate->format("%Y%m%d");
	switch ($repeatType){
	case "day":
	  $db->execute($repeat_insert, array($event_id, $setDate, $setDate));
	  break;
	
	case "week":
	  if ($repeatWeekdays[$countDate->getDayOfWeek()])
	    $db->execute($repeat_insert, array($event_id, $setDate, $setDate));
	  break;
	
	case "month":
	  if ($monthMode == "end" && $countDate->day == $countDate->getDaysInMonth()){
	    $db->execute($repeat_insert, array($event_id, $setDate, $setDate));
	    $countDate->addSeconds(86400 * 20);
	  }
	  elseif ($monthMode == "date" && $setDate == $startDate){
	    $db->execute($repeat_insert, array($event_id, $setDate, $setDate));
	    $countDate->addSeconds(86400 * 20);
	  }
	  break;
	
	case "year":
	  if ($countDate->format("%m%d") == substr($startDate, 4, 4)){
	    $db->execute($repeat_insert, array($event_id, $setDate, $setDate));
	    $countDate->addSeconds(86400 * 363);
	  }
	  break;
	}

	$countDate = $countDate->getNextDay();
      }

    }

    if ($fatcat){
      if ($event["cat_id"]){
	$element_id = $db->nextId(NEW_PREFIX . "mod_fatcat_elements");

	$element_data    = array();
	$element_data[]  = $element_id;
	$element_data[]  = $catID[$event["cat_id"]];
	$element_data[]  = $event["title"];
	$element_data[]  = "index.php?module=calendar&calendar[view]=event&id=$event_id";
	$element_data[]  = $event_id;
	$element_data[]  = "calendar";
	$element_data[]  = "home";
	$element_data[]  = 50;
	$element_data[]  = 1;
	$element_data[]  = NULL;
	$element_data[]  = $startDate;

	$element = $db->execute($element_insert, $element_data);
      }

      if ($event["subcat_id"]){
	$element_id = $db->nextId(NEW_PREFIX . "mod_fatcat_elements");

	$element_data    = array();
	$element_data[]  = $element_id;
	$element_data[]  = $subcatID[$event["subcat_id"]];
	$element_data[]  = $event["title"];
	$element_data[]  = "index.php?module=calendar&calendar[view]=event&id=$event_id";
	$element_data[]  = $event_id;
	$element_data[]  = "calendar";
	$element_data[]  = "home";
	$element_data[]  = 50;
	$element_data[]  = 1;
	$element_data[]  = NULL;
	$element_data[]  = $startDate;

	$element = $db->execute($element_insert, $element_data);
      }

      if ($event["loc_id"]){
	$element_id = $db->nextId(NEW_PREFIX . "mod_fatcat_elements");

	$element_data    = array();
	$element_data[]  = $element_id;
	$element_data[]  = $locID[$event["loc_id"]];
	$element_data[]  = $event["title"];
	$element_data[]  = "index.php?module=calendar&calendar[view]=event&id=$event_id";
	$element_data[]  = $event_id;
	$element_data[]  = "calendar";
	$element_data[]  = "home";
	$element_data[]  = 50;
	$element_data[]  = 1;
	$element_data[]  = NULL;
	$element_data[]  = $startDate;

	$element = $db->execute($element_insert, $element_data);
      }

    }
  }
}

echo "<h3>Calendar Conversion Complete!</h3>";

?>