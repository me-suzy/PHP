<?php
if (!isset($GLOBALS['core'])){
  header("location:../../");
  exit();
}

$CNT_Calendar_Main["title"] = $CNT_Calendar_Main["content"] = NULL;
$calendarSettings = PHPWS_Calendar::getSettings();

/**
 * Command Section
 */
if (isset($_REQUEST["module"]) && $_REQUEST["module"] == "calendar"){
  PHPWS_Calendar::panel();
  if ($calCommand = $_REQUEST["calendar"])
    foreach ($calCommand as $section=>$command);

  // Administrative functions
  if ($section == "admin" && $_SESSION["OBJ_user"]->allow_access("calendar")){

    switch ($command){
    case "admin_menu":
      $_SESSION["CreateEvent"] = new PHPWS_Calendar_Event;
      PHPWS_Calendar_Forms::createEventForm($_SESSION["CreateEvent"]);
      break;

    case "createEventForm":
      $_SESSION["CreateEvent"] = new PHPWS_Calendar_Event;
      PHPWS_Calendar_Forms::createEventForm($_SESSION["CreateEvent"]);
    break;

    case "createEventAction":
      if (isset($_SESSION["CreateEvent"])){
	if ($_SESSION["CreateEvent"]->processEvent()){
	  $_SESSION["CreateEvent"]->writeEvent();
	  $CNT_Calendar_Main["title"] = $_SESSION["translate"]->it("Event created successfully")."!";
	  $CNT_Calendar_Main["content"] .=  PHPWS_Calendar_Display::viewEvent($_SESSION['CreateEvent']->id);
	  $core->killSession("CreateEvent");
	  PHPWS_Calendar::resetCache();
	} else {
	  $CNT_Calendar_Main["content"] .= $_SESSION["CreateEvent"]->printErrors();
	  PHPWS_Calendar_Forms::createEventForm($_SESSION["CreateEvent"]);
	}
      }
      break;

    case "editEvent":
      $_SESSION["EditEvent"] = new PHPWS_Calendar_Event($_REQUEST["id"]);
      PHPWS_Calendar_Forms::editEventForm($_SESSION["EditEvent"]);
      break;

    case "deleteEvent":
      PHPWS_Calendar_Forms::deleteEventForm($_REQUEST["id"], isset($_REQUEST["confirm"]) ? $_REQUEST["confirm"] : NULL);
      PHPWS_Calendar::resetCache();
      break;

    case "editEventAction":
      if ($_SESSION["EditEvent"]){
	if ($_SESSION["EditEvent"]->processEvent()){
	  $_SESSION["EditEvent"]->updateEvent();
	  PHPWS_Calendar::resetCache();
	  $CNT_Calendar_Main["title"] = $_SESSION["translate"]->it("Event updated successfully")."!";
	  $CNT_Calendar_Main["content"] =  PHPWS_Calendar_Display::viewEvent($_SESSION['EditEvent']->id);
	  $core->killSession("EditEvent");
	} else {
	  $CNT_Calendar_Main["content"] .= $_SESSION["EditEvent"]->printErrors();
	  PHPWS_Calendar_Forms::editEventForm($_SESSION["EditEvent"]);
	}
      }
      break;

    case "settings":
    $CNT_Calendar_Main["title"]   = $_SESSION["translate"]->it("Calendar Settings");
    $CNT_Calendar_Main["content"] = PHPWS_Calendar_Forms::settings();
      break;

    case "updateSettings":
      PHPWS_Calendar::updateSettings();
      PHPWS_Calendar::resetCache();
      $CNT_Calendar_Main["title"] = $_SESSION["translate"]->it("Calendar Settings");
      $CNT_Calendar_Main["content"] = "<b>" . $_SESSION["translate"]->it("Settings Updated") . "!</b>";
      $CNT_Calendar_Main["content"] .= PHPWS_Calendar_Forms::settings();
      break;

    default:
      
      break;
    } // End admin command switch
  }// End admin section
  elseif ($section == "user"){
    switch ($command){
    case "changeBoxMonth":
      PHPWS_Calendar_Display::setBoxMonth($_GET["month"], $_GET["year"]);
    header("location:./" .  str_replace("http://" . PHPWS_HOME_HTTP, "", $_SERVER["HTTP_REFERER"]));
    exit();
      break;

    case "userEvent":
      if ($calendarSettings["userSubmit"]){
	$_SESSION["CreateUserEvent"] = new PHPWS_Calendar_Event;
	PHPWS_Calendar_Forms::createUserEventForm($_SESSION["CreateUserEvent"]);
      }
      break;

    case "createUserEventAction":
      if ($_SESSION["CreateUserEvent"]){
	if ($_SESSION["CreateUserEvent"]->processEvent()){
	  $_SESSION["CreateUserEvent"]->writeEvent();
	  PHPWS_Approval::add($_SESSION["CreateUserEvent"]->id, $_SESSION["CreateUserEvent"]->title);
	  $GLOBALS['core']->killSession("CreateUserEvent");
	  PHPWS_Calendar::resetCache();
	  $CNT_Calendar_Main["content"] .= $_SESSION["translate"]->it("Event submitted for approval").".";
	} else {
	  $CNT_Calendar_Main["content"] .= $_SESSION["CreateUserEvent"]->printErrors();
	  $_SESSION["CreateUserEvent"]->createUserEventForm();
	}
      }
      break;

    case "reset":
      PHPWS_Calendar::resetCache();
    header("location:./" . str_replace("http://" . PHPWS_HOME_HTTP, "", $_SERVER["HTTP_REFERER"]));
    exit();
      break;
    } // End user command switch
  }// End user section
  elseif ($section == "view"){
    $yearSet  = (isset($_REQUEST['year'])) ? $_REQUEST['year'] : NULL;
    $monthSet = (isset($_REQUEST['month'])) ? $_REQUEST['month'] : NULL;
    $daySet   = (isset($_REQUEST['day'])) ? $_REQUEST['day'] : NULL;
    $weekSet   = (isset($_REQUEST['week'])) ? $_REQUEST['week'] : NULL;

    switch ($command){
    case "year":
      $CNT_Calendar_Main["content"] = PHPWS_Calendar_Display::viewYear($yearSet);
    break;

    case "month":
      $CNT_Calendar_Main["content"] = PHPWS_Calendar_Display::viewMonth($yearSet, $monthSet);
    break;

    case "day":
      $CNT_Calendar_Main["content"] = PHPWS_Calendar_Display::viewDay($yearSet, $monthSet, $daySet);
    break;

    case "week":
      $CNT_Calendar_Main["content"] = PHPWS_Calendar_Display::viewWeek($yearSet, $monthSet, $weekSet);
    break;

    case "event":
      // $CNT_Calendar_Main["title"] = $_SESSION["translate"]->it("View Event");
      $eventcontent = PHPWS_Calendar_Display::viewEvent($_REQUEST["id"], (isset($_REQUEST["date"])) ? $_REQUEST["date"] : NULL);
  if (!$eventcontent)
    $CNT_Calendar_Main["content"] = $_SESSION["translate"]->it("This event is no longer listed") . ".";
  else
    $CNT_Calendar_Main["content"] = isset($CNT_Calendar_Main["content"]) ? $CNT_Calendar_Main["content"] . $eventcontent : $eventcontent;
      break;

    case "minievent":
      echo PHPWS_Calendar_Display::viewMiniEvent($_REQUEST["id"]);
      break;

    }// End view command switch
  }// End view section
}

/***************************************************/
/* Create Calendar box                             */
/***************************************************/ 

if ($calContent = PHPWS_Calendar_Display::showUserBox()){
  $CNT_Calendar_Box["title"] = $_SESSION["translate"]->it("Calendar");
  $CNT_Calendar_Box["content"] = $calContent;
}

?>