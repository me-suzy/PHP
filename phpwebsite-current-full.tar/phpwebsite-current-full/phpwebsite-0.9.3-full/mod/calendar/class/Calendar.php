<?php

require_once (PHPWS_SOURCE_DIR . "/mod/calendar/class/Display.php");

if (isset($_REQUEST["module"])
    && $_REQUEST["module"] == "calendar"
    && (isset($_REQUEST['calendar']['admin'])
	|| (isset($_REQUEST['calendar']['user'])
	    && ($_REQUEST['calendar']['user'] == "userEvent"
		|| $_REQUEST['calendar']['user'] == "createUserEventAction")
	    )
	)
    )
{
  require_once (PHPWS_SOURCE_DIR . "/mod/calendar/class/Forms.php"); 
    require_once (PHPWS_SOURCE_DIR . "/mod/calendar/class/Event.php");
}

class PHPWS_Calendar {
  var $events;

  function getSettings(){
    $day_start = 0;
    require_once (PHPWS_SOURCE_DIR . "/conf/dateSettings.en.php");
    
    $settings['defaultWeekStart'] = $day_start;

    if ($row = $GLOBALS["core"]->sqlSelect("mod_calendar_settings"))
      $settings = $row[0] + $settings;

    return $settings;
  }

  function isLoaded($start, $end=NULL){
    return FALSE;
  }


  function getWeekNumber($month, $year, $day){
    $Sdate = new Date;
    $Edate = new Date;
    $count = 1;
    $settings = $GLOBALS["calendarSettings"];

    $Sdate->setMonth($month);
    $Sdate->setYear($year);
    $Sdate->setDay(1);

    $Edate->setMonth($month);
    $Edate->setYear($year);
    $Edate->setDay($day);

    while($Sdate->before($Edate) || $Sdate->equals($Edate)){
      $DOW = $Sdate->getDayOfWeek();
      if ($settings["defaultWeekStart"] == 1 && $DOW == 0)
	$count++;
      elseif ($settings["defaultWeekStart"] == 0 && $DOW == 6)
	$count++;

      $Sdate = $Sdate->getNextDay();
    }

    return $count;
  }

  function resetCache(){
    PHPWS_Cache::flush("calendar");
    PHPWS_Core::killSession("miniView");
  }


  function getWeekdayName($number){
    switch ($number){
    case 0:
      return $_SESSION["translate"]->it("Sunday");
      break;
    case 1:
      return $_SESSION["translate"]->it("Monday");
      break;
    case 2:
      return $_SESSION["translate"]->it("Tuesday");
      break;
    case 3:
      return $_SESSION["translate"]->it("Wednesday");
      break;
    case 4:
      return $_SESSION["translate"]->it("Thursday");
      break;
    case 5:
      return $_SESSION["translate"]->it("Friday");
      break;
    case 6:
      return $_SESSION["translate"]->it("Saturday");
      break;
    }

  }

  function reindexFatcat(){
    $elements = PHPWS_Fatcat::getModuleElements('calendar');
    if (!count($elements))
      return FALSE;

    foreach ($elements as $info){
      $event = new PHPWS_Calendar_Event($info['module_id']);
      PHPWS_Fatcat::updateDate($info['element_id'], $event->startDate);
    }
    
  }


  function updateSettings(){
    extract($_POST);

    if (isset($minimonth))
      $update["minimonth"] = 1;
    else
      $update["minimonth"] = 0;

    if (isset($today))
      $update["today"] = 1;
    else
      $update["today"] = 0;

    if (isset($cacheView))
      $update["cacheView"] = 1;
    else
      $update["cacheView"] = 0;

    if (isset($userSubmit))
      $update["userSubmit"] = 1;
    else
      $update["userSubmit"] = 0;

    if (isset($reindexFatcat) && $reindexFatcat == 1)
      PHPWS_Calendar::reindexFatcat();


    if (isset($daysAhead))
      $update["daysAhead"] = $daysAhead;

    $GLOBALS["core"]->sqlUpdate($update, "mod_calendar_settings");
    $GLOBALS["calendarSettings"] = $update;
  }

  function formatDateTime($time, $date){
    if ($time == 9999){
      $hour = 12;
      $minute = 0;
    } else {
      $hour   = floor($time / 100);
      $minute = $time % 100;
    }
    $year = substr($date, 0, 4);
    $month = substr($date, 4, 2);
    $day = substr($date, 6, 2);

    return $GLOBALS["core"]->date(mktime($hour, $minute, 30, $month, $day, $year));

  }


  function formatTime($hour, $minute, $ampm=NULL){
    if ($ampm == 1 && $hour >= 1 && $hour != 12)
      $hour = (string)((int)$hour + 12);
    elseif ($ampm == 0 && $hour == 12)
      $hour = "00";

    if ((int)$hour < 10 && (int)$hour >= 1)
      $hour = "0" . $hour;

    if ((int)$minute < 10 && (int)$minute >= 1)
      $minute = "0" . $minute;

    $time = $hour . $minute;

    return $time;
  }

  function loadEvents($start, $end=NULL){
    extract($GLOBALS["calendarSettings"]);

    if (is_null($end))
      $end = $start;

    $startDate   = $start->format("%Y%m%d");
    $endDate   = $end->format("%Y%m%d");

    if ($end->before($start))
      exit("Error in Calendar.php : loadEvents() - start date is greater than end date");
    
    $sql = "select * from mod_calendar_events where ((startDate >= $startDate and startDate <= $endDate) or (endDate >= $startDate and endDate <= $endDate))";

    if (!$_SESSION["OBJ_user"]->allow_access("calendar"))
      $sql .= " and active=1";

    $eventList = array();
    if($row = $GLOBALS["core"]->getAllAssoc($sql, TRUE)){
      foreach ($row as $event){
	$eventList = PHPWS_Calendar::setEvent($event, $eventList);
      }
    }

    if ($repeatList = PHPWS_Calendar::loadRepeats($start, $end))
      $eventList = PHPWS_Calendar::mergeLists($repeatList, $eventList);
    
    if ($eventList)
      PHPWS_Calendar::orderEvents($eventList);

    return $eventList;
  }
  
  function activateEvent($id){
    $GLOBALS["core"]->sqlUpdate(array("active"=>1), "mod_calendar_events", "id", $id);
    $link = "index.php?module=calendar&calendar[view]=event&id=$this->id";
    $event = new PHPWS_Calendar_Event($id);
    $_SESSION["OBJ_fatcat"]->saveSelect($event->title, $link, $event->id, $event->groups);
  }


  function orderEvents(&$eventList){
    if (!$eventList)
      return;

    foreach ($eventList as $year=>$monthIndex){
      foreach ($monthIndex as $month=>$dayIndex){
	foreach ($dayIndex as $day=>$timeIndex){
	  ksort($eventList[$year][$month][$day]);
	}
      }
    }
  }

  function setEvent($event, $list){
    if (!is_array($event))
      return;

    $startDate = new Date;
    $endDate   = new Date;

    PHPWS_Calendar::splitDateObject($startDate, $event["startDate"]);
    PHPWS_Calendar::splitDateObject($endDate, $event["endDate"]);

    $date = $startDate;
    $count = 0;
    while ($endDate->after($date) || $endDate->equals($date)){
      $count++;
      $event["dayNumber"] = $count;

      if ($event['eventType'] != "deadline")
	$list[$date->year][$date->month][$date->day][$event["startTime"]][$event["id"]] = $event;
      else
	$list[$date->year][$date->month][$date->day][$event["endTime"]][$event["id"]] = $event;

      $date = $date->getNextDay();
    }

    return $list;
  }

  function loadRepeats($start, $end=NULL){
    require_once (PHPWS_SOURCE_DIR . "/mod/calendar/class/Event.php");
    $repeatList = NULL;
    if (is_null($end))
      $end = $start;

    $startDate = $start->format("%Y%m%d");
    $endDate   = $end->format("%Y%m%d");

    $sql = "select * from mod_calendar_repeats where ((startDate >= $startDate and startDate <= $endDate) or (endDate >= $startDate and endDate <= $endDate))";

    if (!$_SESSION["OBJ_user"]->allow_access("calendar"))
      $sql .= " and active=1";

    if($row = $GLOBALS["core"]->getAllAssoc($sql, TRUE)){
      foreach ($row as $repeat)
	$repeatList = PHPWS_Calendar::setRepeat($repeat, $repeatList);
    } else
      return NULL;

    return $repeatList;
  }

  function setRepeat($repeat, $list){
    require_once (PHPWS_SOURCE_DIR . "/mod/calendar/class/Event.php");
    if (!is_array($repeat))
      return;

    $event = array();

    $temp = new PHPWS_Calendar_Event($repeat["id"]);
    $GLOBALS["core"]->objectToArray($temp, $event);

    $event["startDate"] = $repeat["startDate"];
    $event["endDate"] = $repeat["endDate"];
    $event["isRepeat"] = 1;
    return PHPWS_Calendar::setEvent($event, $list);
  }

  function mergeLists($list1, $list2){
    if (is_array($list1)){
      foreach ($list1 as $yearKey=>$month){
	foreach ($month as $monthKey=>$day){
	  foreach ($day as $dayKey=>$time){
	    foreach ($time as $timeKey=>$events){
	      foreach ($events as $event)
		$list2[$yearKey][$monthKey][$dayKey][$timeKey][$event["id"]] = $event;
	    }
	  }
	}
      }
    }
    return $list2;
  }

  function eventExists($date, $events){
    if (isset($events[$date->year][$date->month][$date->day])) return TRUE;
    else return FALSE;
  }

  function splitDateObject(&$dateOBJ, $date){
    if (get_class($dateOBJ) != "date")
      exit("Error: splitDateObject did not receive a date object.");
    $dateOBJ->setYear((int)substr($date, 0, 4));
    $dateOBJ->setMonth((int)substr($date, 4, 2));
    $dateOBJ->setDay((int)substr($date, 6, 2));
  }

  function buildDate($month, $day, $year){
    $date = new Date;
    $date->setYear($year);
    $date->setMonth($month);
    $date->setDay($day);

    return $date;
  }

  function splitDate($date){
    $array["year"] = (int)substr($date, 0, 4);
    $array["month"] = (int)substr($date, 4, 2);
    $array["day"] = (int)substr($date, 6, 2);

    return $array;
    
  }

  function emptyEventRange($start, $end){
    if (get_class($start) != "date" || get_class($end) != "date")
      exit("Error in Calendar.php : emptyEventRange did not receive Date objects.");
      
    for ($count = $start; $count->before($end); $count = $count->getNextDay())
      $this->events[$count->year][$count->month][$count->day] = 0;
  }

  function getEvents($date, $events){
    $year  = (int)$date->year;
    $month = (int)$date->month;
    $day   = (int)$date->day;

    if (isset($events[$year][$month][$day]))
      return $events[$year][$month][$day];
    else
      return NULL;
  }

  function addMonth($date){
    if ($date->month == 12){
      $date->setMonth(1);
      $date->setYear($date->year + 1);
    } else
      $date->setMonth($date->month + 1);

    return $date;
  }

  function addYear($date){
    $date->setYear($date->year +1);
    return $date;
  }

  function addWeek($date){
    $date->addSeconds(7 * 86400);
    return $date;
  }

  function viewFatCatEvent($id){
    require_once (PHPWS_SOURCE_DIR . "/mod/calendar/class/Event.php");
    $event = new PHPWS_Calendar_Event($id);
    $template = PHPWS_Calendar_Display::getEventTemplate($event);

    return $GLOBALS["core"]->processTemplate($template, "calendar", "fatcat/fatEvent.tpl");
  }
  
  function search($where) {
    $sql = "SELECT * FROM " . $GLOBALS["core"]->tbl_prefix . "mod_calendar_events $where";
    $result = $GLOBALS["core"]->query($sql);

    if($result) {
      while($row = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
	$array[$row["id"]] = "<b>" . $row["title"] . "</b><br />" . $row["description"];
      }
    }
    return $array;
  }

  function panel(){
    if ($_SESSION["OBJ_user"]->allow_access("calendar")){
      $template["CREATE_EVENT"] = $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Create New Event"), "calendar", array("calendar[admin]"=>"createEventForm"));
      $template["SETTINGS"] = $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Settings"), "calendar", array("calendar[admin]"=>"settings"));
      $template["RESET"]        = $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Refresh"), "calendar", array("calendar[user]"=>"reset"));
    }

    $template["YEAR_VIEW"]    = $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Year"), "calendar", array("calendar[view]"=>"year"));
    $template["MONTH_VIEW"]   = $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Month"), "calendar", array("calendar[view]"=>"month"));
    $template["WEEK_VIEW"]    = $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Week"), "calendar", array("calendar[view]"=>"week"));

    $GLOBALS["CNT_Calendar_Panel"]["content"] =  $GLOBALS["core"]->processTemplate($template, "calendar", "admin/panel.tpl");
  }// END FUNC panel

}

?>