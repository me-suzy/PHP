<?php

require_once (PHPWS_SOURCE_DIR . "/mod/calendar/class/Repeat.php");
class PHPWS_Calendar_Event extends PHPWS_Calendar_Repeat{

  var $id;
  var $title;
  var $description;
  var $startTime;
  var $endTime;
  var $startDate;
  var $endDate;
  var $template;
  var $eventType;
  var $groups;
  var $pmChoice;
  var $pmID;
  var $image;
  var $active;
  var $dayNumber;
  var $error;

  function PHPWS_Calendar_Event ($id=NULL){
    if (!$id)
      return FALSE;

    if (!($event = $GLOBALS["core"]->sqlSelect("mod_calendar_events", "id", $id)))
      return FALSE;

    $GLOBALS["core"]->arrayToObject($event[0], $this);

    if ($this->image){
      $file = explode(":", $this->image);
      $this->image = array();
      $this->image["name"] = $file[0];
      $this->image["width"] = $file[1];
      $this->image["height"] = $file[2];
    }

    if ($this->every)
      $this->every = explode(":", $this->every);

    $this->repeatWeekdays = explode(":", $this->repeatWeekdays);
  }


  function setRepeatVars($endRepeat, $repeatMode, $monthMode=NULL, $repeatWeekdays=NULL, $every=NULL){
    $update["endRepeat"] = $endRepeat;
    $update["repeatMode"] = $repeatMode;

    if ($repeatMode == "monthly" && !is_null($monthMode))
          $update["monthMode"] = $monthMode;

    if ($repeatMode == "weekly" && !is_null($repeatWeekdays))
      $update["repeatWeekdays"] = implode(":", $repeatWeekdays);

    if ($repeatMode == "every" && !is_null($every))
      $update["every"] = implode(":", $every);

    return $GLOBALS["core"]->sqlUpdate($update, "mod_calendar_events", "id", $this->id);
  }


  function eventDuration($event=NULL){
    if (is_null($event))
      $event = $this;

    $start = PHPWS_Calendar::splitDate($event->startDate);
    $end   = PHPWS_Calendar::splitDate($event->endDate);

    return Date_Calc::dateDiff($start["day"], $start["month"], $start["year"],
			       $end["day"], $end["month"], $end["year"]);
  }


  function getFormattedDateTime(){
    if ($this->eventType == "allday"){
      $startDate = PHPWS_Calendar::formatDateTime(12, $this->startDate);
      $endDate   = PHPWS_Calendar::formatDateTime(12, $this->endDate);
    } else {
      $startDate = PHPWS_Calendar::formatDateTime($this->startTime, $this->startDate);
      $endDate   = PHPWS_Calendar::formatDateTime($this->endTime, $this->endDate);
    }
    switch ($this->eventType){
    case "allday":
      $template["START_DATE"] = $startDate["full"];
      $template["START_TIME"] = $template["TIME"] = $_SESSION["translate"]->it("All Day");
    if ($startDate["n_full"] == $endDate["n_full"]){
      $template["DATE_TIME"] = $startDate["full"] . "<br />"; 
    }
    else {
      $template["END_DATE"] = $endDate["full"];
	$template["DATE_TIME"] = $startDate["full"] . " to " .$endDate["full"] . "<br />";
    }
    
    $template["DATE_TIME"] .= $template["ALL_DAY"] = $_SESSION["translate"]->it("All Day Event");
    break;
    
    case "start":
      $template["DATE_TIME"] = $startDate["full"] . "<br />";
    $template["TIME"] = $startDate["time"];
    $template["START_DATE"] = $startDate["full"];
    $template["START_TIME"] = $startDate["time"];
    $template["DATE_TIME"] .= $_SESSION["translate"]->it("Starts at") . " " . $startDate["time"];
    break;
    
    case "deadline":
      $template["TIME"] = $endDate["time"];
      $template["END_DATE"] = $endDate["full"];
    $template["DATE_TIME"] = $endDate["full"] . "<br />";
    $template["DATE_TIME"] .= $_SESSION["translate"]->it("Deadline at") . " " . $endDate["time"];
    break;
    
    case "interval":
      $template["TIME"] = $startDate["time"] . " - " . $endDate["time"];
      $template["START_TIME"] = $startDate["time"];
      $template["END_TIME"] = $endDate["time"];
    $template["START_DATE"] = $startDate["full"];
    if ($startDate["n_full"] == $endDate["n_full"]){
      $template["DATE_TIME"] = $startDate["time"] . " - " . $endDate["time"] . "<br />";
      $template["DATE_TIME"] .= $startDate["full"]; 

    } else {
	$template["END_DATE"] = $endDate["full"];
	$template["DATE_TIME"] = "<b>" . $_SESSION["translate"]->it("From") . ":</b> " . $startDate["time"] . ", " .$startDate["full"];
	$template["DATE_TIME"] .= "<br /><b>" . $_SESSION["translate"]->it("To") . ":</b> " . $endDate["time"] . ", " . $endDate["full"];
    }
    break;
    }

    return $template;
  }


  function processEvent(){
    $image_directory = "images/calendar/";
    extract($_POST);

    if (checkdate($cal_startDate_month, $cal_startDate_day, $cal_startDate_year))
      $startDate = PHPWS_Calendar::buildDate($cal_startDate_month, $cal_startDate_day, $cal_startDate_year);
    else
      $this->error[] = $_SESSION["translate"]->it("Invalid start date") . ".";

    if (checkdate($cal_endDate_month, $cal_endDate_day, $cal_endDate_year))
      $endDate   = PHPWS_Calendar::buildDate($cal_endDate_month, $cal_endDate_day, $cal_endDate_year);
    else
      $this->error[] = $_SESSION["translate"]->it("Invalid end date") . ".";

    if ($cal_title)
      $this->title = $GLOBALS["core"]->parseInput($cal_title);
    else
      $this->error[] = $_SESSION["translate"]->it("Missing Title");

    if (isset($cal_active))
      $this->active = $cal_active;
    else
      $this->active = 0;
    

    $this->description = $GLOBALS["core"]->parseInput($cal_description);
    $this->template    = $cal_template;
    $this->eventType   = $cal_eventType;

    if (isset($_FILES["NEW_IMAGE"]["name"]) && !empty($_FILES["NEW_IMAGE"]["name"])){
      $image = EZform::saveImage("NEW_IMAGE", $image_directory, 1024, 1000);

      if (PHPWS_Error::isError($image)){
	$image->message("CNT_Calendar_Main");
	$this->error[] = $_SESSION["translate"]->it("Image not saved") . ".";
      } else
	$this->image = $image;
    }
    elseif (isset($CURRENT_IMAGE) && $CURRENT_IMAGE != "none"){
      if (isset($REMOVE_IMAGE)){
	@unlink($image_directory . $CURRENT_IMAGE);
	$this->error[] = $_SESSION["translate"]->it("Image deleted") . ".";
      } else {
	$oldImage["name"] = $CURRENT_IMAGE;
	$size = getimagesize($image_directory . $CURRENT_IMAGE);
	$oldImage["width"] = $size[0];
	$oldImage["height"] = $size[1];
	$this->image = $oldImage;
      }
    } else
      $this->image = NULL;

    if ($this->eventType == "allday"){
      $this->startTime = -1;
      $this->endTime   = 9999;
    } else {
      $this->startTime   = PHPWS_Calendar::formatTime($cal_startTime_hour, $cal_startTime_minute, $cal_startTime_ampm);
      $this->endTime     = PHPWS_Calendar::formatTime($cal_endTime_hour, $cal_endTime_minute, $cal_endTime_ampm);
    }

    if (isset($startDate))
      $this->startDate   = $startDate->format("%Y%m%d");

    if (isset($endDate))
      $this->endDate     = $endDate->format("%Y%m%d");


    $this->template    = $cal_template;
    $this->eventType   = $cal_eventType;

    if (isset($viewGroups))
      $this->groups    = $viewGroups;

    if (isset($cal_pmChoice))
      $this->pmChoice  = $cal_pmChoice;

    if (isset($cal_pmID))
      $this->pmID      = $cal_pmID;
   
    if (isset($startDate) && isset($endDate)){
      if ($this->eventType == "deadline"){
	$this->startTime = $this->endTime;
	$this->startDate = $this->endDate;
      }
      
      if ($this->eventType == "start"){
	$this->endTime = $this->startTime;
	$this->endDate = $this->startDate;
      }

      if ($this->endDate < $this->startDate)
	$this->error[] = $_SESSION["translate"]->it("The Start Date must be less than the End Date") . ".";
    
      if ($this->eventType == "interval" && ($this->endTime <= $this->startTime) && ($this->endDate == $this->startDate))
	$this->error[] = $_SESSION["translate"]->it("The End Time must be greater than the Start Time on a single day, interval event") . ".";
    }

    $this->processRepeats();

    if ($this->error)
      return FALSE;
    else
      return TRUE;
  }


  function writeEvent(){
    if ($this->groups)
      $insert["groups"]    = implode(":", $this->groups);

    $insert["active"]      = $this->active;
    $insert["title"]       = $this->title;
    $insert["description"] = $this->description;
    if ($this->image)
      $insert["image"]       = implode(":", $this->image);
    $insert["template"]    = $this->template;
    $insert["eventType"]   = $this->eventType;

    if ($this->eventType=="deadline")
      $insert["startTime"] = $this->endTime;
    else
      $insert["startTime"] = $this->startTime;

    $insert["startTime"]   = $this->startTime;
    $insert["endTime"]     = $this->endTime;
    $insert["startDate"]   = $this->startDate;
    $insert["endDate"]     = $this->endDate;

    if ($this->pmChoice)
      $insert["pmChoice"]    = $this->pmChoice;
    if ($this->pmID)
      $insert["pmID"]        = $this->pmID;

    if ($this->id = $GLOBALS["core"]->sqlInsert($insert, "mod_calendar_events", NULL, TRUE)){
      $this->removeRepeats($this->id);
      if ($this->repeatMode)
	$this->repeatEvent($this->active);

      $link = "index.php?module=calendar&calendar[view]=event&id=$this->id";
      if ($this->active)
	$_SESSION["OBJ_fatcat"]->saveSelect($this->title, $link, $this->id, $this->groups, "calendar");
      else
	$_SESSION["OBJ_fatcat"]->saveSelect($this->title, $link, $this->id, $this->groups, "calendar", NULL, NULL, FALSE);

      return TRUE;
    } else 
      return FALSE;
  }

  function updateEvent(){
    if ($this->groups)
      $update["groups"]    = implode(":", $this->groups);

    $update["active"]      = $this->active;
    $update["title"]       = $this->title;
    $update["description"] = $this->description;

    if ($this->image)
      $update["image"]       = implode(":", $this->image);
    else
      $update["image"]       = NULL;

    $update["template"]    = $this->template;
    $update["eventType"]   = $this->eventType;

    if ($this->eventType=="deadline")
      $update["startTime"] = $this->endTime;
    else
      $update["startTime"] = $this->startTime;

    $update["endTime"]     = $this->endTime;
    $update["startDate"]   = $this->startDate;
    $update["endDate"]     = $this->endDate;

    if ($this->pmChoice)
      $update["pmChoice"]    = $this->pmChoice;
    if ($this->pmID)
      $update["pmID"]        = $this->pmID;

    if ($GLOBALS["core"]->sqlUpdate($update, "mod_calendar_events", "id", $this->id)){
      $this->removeRepeats($this->id);

      if ($this->repeatMode)
	$this->repeatEvent($this->active);
      else
	$this->clearRepeatsFromEvent();

      $link = "index.php?module=calendar&calendar[view]=event&id=$this->id";
      if ($this->active)
	$_SESSION["OBJ_fatcat"]->saveSelect($this->title, $link, $this->id, $this->groups, "calendar");
      else
	$_SESSION["OBJ_fatcat"]->saveSelect($this->title, $link, $this->id, $this->groups, "calendar", NULL, NULL, FALSE);

      return TRUE;
    } else 
      return FALSE;
  }

  function printErrors(){
    $content = NULL;
    if ($this->error){
      foreach ($this->error as $error)
	$content .= "<span class=\"errortext\">$error</span><br />\n";

      unset($this->error);

      return $content;
    }
  }

  function deleteEvent($id){
    $GLOBALS["core"]->sqlDelete("mod_calendar_events", "id", $id);
    $GLOBALS["core"]->sqlDelete("mod_calendar_repeats", "id", $id);
    PHPWS_Fatcat::purge($id, "calendar");
  }


  function setTitle($title){
    if (empty($title))
      return FALSE;

    $this->title = $GLOBALS["core"]->parseInput($title);
    return TRUE;
  }

  function setDesc($description){
    $this->description = $GLOBALS["core"]->parseInput($description);
    return TRUE;
  }

  function setStartTime($hour, $minute, $ampm=NULL){
    $this->startTime = PHPWS_Calendar::formatTime($hour, $minute, $ampm);
    return TRUE;
  }

  function setEndTime($hour, $minute, $ampm=NULL){
    $this->endTime = PHPWS_Calendar::formatTime($hour, $minute, $ampm);
    return TRUE;
  }
}

?>