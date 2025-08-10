<?php
define("MAX_AHEAD", 14);

class PHPWS_Calendar_Forms{

  function deleteEventForm($id, $confirm=0){
    if (!$confirm){
      $GLOBALS["CNT_Calendar_Main"]["title"] = $_SESSION["translate"]->it("Delete Confirmation");
      $GLOBALS["CNT_Calendar_Main"]["content"] .= $_SESSION["translate"]->it("Are you certain you want to delete this event") . "?<br />\n";
      $GLOBALS["CNT_Calendar_Main"]["content"] .= $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Yes"), "calendar", array("calendar[admin]"=>"deleteEvent", "id"=>$id, "confirm"=>1))
	 . " <a href=\"".$_SERVER["HTTP_REFERER"]."\">".$_SESSION["translate"]->it("No")."</a>";
    } else {
      PHPWS_Calendar_Event::deleteEvent($id);
      $GLOBALS["CNT_Calendar_Main"]["title"] .= $_SESSION["translate"]->it("Event Deleted");
    }

  }

  function createEventForm($event){
    if (!isset($GLOBALS["CNT_Calendar_Main"]["content"]))
      $GLOBALS["CNT_Calendar_Main"]["content"] = NULL;

    $template["EVENT_DATA"] = PHPWS_Calendar_Forms::eventDataForm($event);
    $template["REPEAT"]     = PHPWS_Calendar_Forms::repeatForm($event);
    $template["EVENT_SUBMIT"] = $template["REPEAT_SUBMIT"] = $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Create Event"));

    $content = 
       "\n<form name=\"createEvent\" action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\">"
       . $GLOBALS["core"]->formHidden(array("module"=>"calendar", "calendar[admin]"=>"createEventAction"));
    $content .= $GLOBALS["core"]->processTemplate($template, "calendar", "admin/eventForm.tpl");
    $content .=
       "</form>";

    $GLOBALS["CNT_Calendar_Main"]["title"] = $_SESSION["translate"]->it("Create New Event");
    $GLOBALS["CNT_Calendar_Main"]["content"] .= $content;

  }

  function createUserEventForm($event){
    $template["EVENT_DATA"] = PHPWS_Calendar_Forms::eventDataForm($event, TRUE);
    $template["EVENT_SUBMIT"]     = $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Create Event"));

    $content = 
       "\n<form name=\"createEvent\" action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\">"
       . $GLOBALS["core"]->formHidden(array("module"=>"calendar", "calendar[user]"=>"createUserEventAction"));
    $content .= $GLOBALS["core"]->processTemplate($template, "calendar", "admin/eventForm.tpl");
    $content .=
       "</form>";

    $GLOBALS["CNT_Calendar_Main"]["title"] = $_SESSION["translate"]->it("Create New Event");
    $GLOBALS["CNT_Calendar_Main"]["content"] .= $content;

  }

  function editEventForm($event){
    if (!isset($GLOBALS["CNT_Calendar_Main"]["content"]))
      $GLOBALS["CNT_Calendar_Main"]["content"] = NULL; 

    if (!$event->id){
      $GLOBALS["CNT_Calendar_Main"]["title"] = $_SESSION["translate"]->it("Update Event");
      $GLOBALS["CNT_Calendar_Main"]["content"] .= $_SESSION["translate"]->it("This event is no longer listed") . ".";
      return;
    }

    $template["EVENT_DATA"] = PHPWS_Calendar_Forms::eventDataForm($event);
    $template["REPEAT"]     = PHPWS_Calendar_Forms::repeatForm($event);
    $template["REPEAT_SUBMIT"] = $template["EVENT_SUBMIT"] = $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Update Event"));

    $content = 
       "\n<form name=\"createEvent\" action=\"index.php\" method=\"post\" enctype=\"multipart/form-data\">"
       . $GLOBALS["core"]->formHidden(array("module"=>"calendar", "calendar[admin]"=>"editEventAction"));
    $content .= $GLOBALS["core"]->processTemplate($template, "calendar", "admin/eventForm.tpl");
    $content .=
       "</form>";

    $GLOBALS["CNT_Calendar_Main"]["title"] = $_SESSION["translate"]->it("Update Event");
    $GLOBALS["CNT_Calendar_Main"]["content"] .= $content;

  }

  function eventDataForm($event, $userForm=FALSE){
    $image_directory = $GLOBALS["core"]->home_dir."images/calendar";

    if ($userForm)
      $event->active = 0;
    else {
      if (!isset($event->active))
	$event->active = 1;
    }

      if (is_null($event->eventType))
	$event->eventType ="interval";

    if ($event->eventType == "allday" && $event->startTime == -1){
      $event->startTime = "1300";
      $event->endTime = "1300";
    }
    $form = new EZform;

    if (!($form->imageForm(NULL, NULL, $event->image["name"])))
      $template["IMAGE_LABEL"] = $_SESSION["translate"]->it("Image") . $_SESSION["OBJ_help"]->show_link("calendar", "noImage");
    else {
      $template = $form->getTemplate();
      $template["IMAGE_LABEL"] = $_SESSION["translate"]->it("Image");
    }
    if ($GLOBALS["core"]->moduleExists("fatcat")){
      $template["CAT_LIST"] = $_SESSION["OBJ_fatcat"]->showSelect($event->id, "multiple");
      $template["CAT_TITLE"] = $_SESSION["translate"]->it("Categories");
    }

    $template["TEMPLATE_TITLE"] = $_SESSION["translate"]->it("Template");
    $templateDir = $GLOBALS["core"]->source_dir . "mod/calendar/templates/event";
    if ($templateFiles = $GLOBALS["core"]->readDirectory($templateDir, FALSE, TRUE, FALSE, array("tpl")))
      $template["TEMPLATE_FORM"] = $GLOBALS["core"]->formSelect("cal_template", $templateFiles, $event->template, TRUE);
    else
      exit("Error in eventDataForm: Unable to find any event templates.");

    if (!$userForm){
      $template["ACTIVE"] = $_SESSION["translate"]->it("Active");
      $template["ACTIVE_RADIO_ON"] = $GLOBALS["core"]->formRadio("cal_active", 1, $event->active) . " " . $_SESSION["translate"]->it("On");
      $template["ACTIVE_RADIO_OFF"] = $GLOBALS["core"]->formRadio("cal_active", 0, $event->active) . " " . $_SESSION["translate"]->it("Off");
    }

    $template["TITLE"] = $_SESSION["translate"]->it("Title");
    $template["TITLE_FORM"] = $GLOBALS["core"]->formTextField("cal_title", $event->title, 40);

    $template["DESCRIPTION"] = $_SESSION["translate"]->it("Description");
    $template["DESCRIPTION_FORM"] = $GLOBALS["core"]->js_insert("wysiwyg", "createEvent", "cal_description", 1);
    $template["DESCRIPTION_FORM"] .= $GLOBALS["core"]->formTextArea("cal_description", $event->description, 8);

    /*******************************/
    $template["TYPE"] = $_SESSION["translate"]->it("Event Type") . CLS_help::show_link("calendar", "eventtype");
    $template["TYPE_FORM"] = $GLOBALS["core"]->formRadio("cal_eventType", "interval", $event->eventType) . " " . $_SESSION["translate"]->it("Interval") . "<br />\n";
    $template["TYPE_FORM"] .= $GLOBALS["core"]->formRadio("cal_eventType", "start", $event->eventType) . " " . $_SESSION["translate"]->it("Starts At") . "<br />\n";
    $template["TYPE_FORM"] .= $GLOBALS["core"]->formRadio("cal_eventType", "deadline", $event->eventType) . " " . $_SESSION["translate"]->it("Deadline") . "<br />\n";
    $template["TYPE_FORM"] .= $GLOBALS["core"]->formRadio("cal_eventType", "allday", $event->eventType) . " " . $_SESSION["translate"]->it("All Day") . "<br />\n";

    $template["START_TIME"] = $_SESSION["translate"]->it("Start Time");
    $template["START_TIME_FORM"] = $GLOBALS["core"]->clock("cal_startTime", $event->startTime, 5);

    $template["END_TIME"] = $_SESSION["translate"]->it("End Time");
    $template["END_TIME_FORM"] = $GLOBALS["core"]->clock("cal_endTime", $event->endTime, 5);

    $template["START_DATE"] = $_SESSION["translate"]->it("Start Date");
    $template["START_DATE_FORM"] = $GLOBALS["core"]->formDate("cal_startDate", $event->startDate);

    $template["END_DATE"] = $_SESSION["translate"]->it("End Date");
    $template["END_DATE_FORM"] = $GLOBALS["core"]->formDate("cal_endDate", $event->endDate);

    if ($_SESSION["OBJ_user"]->allow_access("calendar", "crossPost"))
      $groupList = PHPWS_User_Groups::listAllGroups();
    else
      $groupList = $_SESSION["OBJ_user"]->listUserGroups();

    return $GLOBALS["core"]->processTemplate($template, "calendar", "admin/eventDataForm.tpl");
  }


  function settings(){
    extract($GLOBALS["calendarSettings"]);
    $content = NULL;
    if (!$cacheView)
      $cacheView = 0;
    else
      $cacheView = 1;

    for ($i=0; $i <= MAX_AHEAD; $i++)
      $dayarray[$i] = $i;

    $form = new EZform;
    $form->add("minimonth", "checkbox", 1);
    $form->setMatch("minimonth", $minimonth);
    $form->add("cacheView", "checkbox", 1);
    $form->setMatch("cacheView", $cacheView);
    $form->add("daysAhead", "select", $dayarray);
    $form->setMatch("daysAhead", $daysAhead);
    $form->add("userSubmit", "checkbox", 1);
    $form->setMatch("userSubmit", $userSubmit);
    $form->add("reindexFatcat", "checkbox");

    $template = $form->getTemplate();
    $template["USERSUBMIT_LABEL"] = $_SESSION["translate"]->it("User Submitted Events");
    $template["DAYSAHEAD_LABEL"] = $_SESSION["translate"]->it("Days Ahead");
    $template["CACHEVIEW_LABEL"] = $_SESSION["translate"]->it("Cache Calendar Views");
    $template["MINIMONTH_LABEL"] = $_SESSION["translate"]->it("Mini Month");
    $template["REINDEX_LABEL"] = $_SESSION["translate"]->it("Reindex FatCat");
    //    $template["TODAY_LABEL"] = $_SESSION["translate"]->it("Today's Events");
    $template["VIEWS"] = $_SESSION["translate"]->it("Box Views");
    $template["OTHER_SETTINGS"] = $_SESSION["translate"]->it("Other Settings");


    $content .= "\n<form action=\"index.php\" method=\"post\">\n"
       . $GLOBALS["core"]->formHidden(array("module"=>"calendar", "calendar[admin]"=>"updateSettings")) . "\n";
    $content .= $GLOBALS["core"]->processTemplate($template, "calendar", "admin/settings.tpl");
    $content .= "</form>";
    return $content;
  }


  function repeatForm($event){
    $template["WEEKDAYS"] = NULL;

    if ($event->repeatMode)
      $repeat_switch = 1;
    else
      $repeat_switch = NULL;

    $template["REPEAT_SWITCH"] = $GLOBALS["core"]->formCheckBox("repeatEvent", 1, $repeat_switch);

    $template["REPEAT_UNTIL"] = $_SESSION["translate"]->it("Repeat Event until") . $GLOBALS["core"]->formDate("endRepeat", $event->endRepeat);
  
    $template["MODE_DAILY"] = $GLOBALS["core"]->formRadio("repeatMode", "daily", $event->repeatMode) . " " . $_SESSION["translate"]->it("Daily");

    $template["MODE_WEEKLY"] = $GLOBALS["core"]->formRadio("repeatMode", "weekly", $event->repeatMode) . " " . $_SESSION["translate"]->it("Weekly");

    $day0 = (isset($event->repeatWeekdays[0])) ? $event->repeatWeekdays[0] : NULL;
    $day1 = (isset($event->repeatWeekdays[1])) ? $event->repeatWeekdays[1] : NULL;
    $day2 = (isset($event->repeatWeekdays[2])) ? $event->repeatWeekdays[2] : NULL;
    $day3 = (isset($event->repeatWeekdays[3])) ? $event->repeatWeekdays[3] : NULL;
    $day4 = (isset($event->repeatWeekdays[4])) ? $event->repeatWeekdays[4] : NULL;
    $day5 = (isset($event->repeatWeekdays[5])) ? $event->repeatWeekdays[5] : NULL;
    $day6 = (isset($event->repeatWeekdays[6])) ? $event->repeatWeekdays[6] : NULL;

    if (!$GLOBALS["core"]->day_start)
      $template["WEEKDAYS"] .= $GLOBALS["core"]->formCheckBox("repeatWeekdays[0]", 1, $day0) . "&nbsp;" . $_SESSION["translate"]->it("Sunday") . " ";

    $template["WEEKDAYS"] .= $GLOBALS["core"]->formCheckBox("repeatWeekdays[1]", 1, $day1) . "&nbsp;" . $_SESSION["translate"]->it("Monday") . " ";
    $template["WEEKDAYS"] .= $GLOBALS["core"]->formCheckBox("repeatWeekdays[2]", 1, $day2) . "&nbsp;" . $_SESSION["translate"]->it("Tuesday") . " ";
    $template["WEEKDAYS"] .= $GLOBALS["core"]->formCheckBox("repeatWeekdays[3]", 1, $day3) . "&nbsp;" . $_SESSION["translate"]->it("Wednesday") . " ";
    $template["WEEKDAYS"] .= $GLOBALS["core"]->formCheckBox("repeatWeekdays[4]", 1, $day4) . "&nbsp;" . $_SESSION["translate"]->it("Thursday") . " ";
    $template["WEEKDAYS"] .= $GLOBALS["core"]->formCheckBox("repeatWeekdays[5]", 1, $day5) . "&nbsp;" . $_SESSION["translate"]->it("Friday") . " ";
    $template["WEEKDAYS"] .= $GLOBALS["core"]->formCheckBox("repeatWeekdays[6]", 1, $day6) . "&nbsp;" . $_SESSION["translate"]->it("Saturday") . " ";

    if ($GLOBALS["core"]->day_start)
      $template["WEEKDAYS"] .= $GLOBALS["core"]->formCheckBox("repeatWeekdays[0]", 1, $day0) . "&nbsp;" . $_SESSION["translate"]->it("Sunday") . " ";


    $template["MODE_MONTHLY"] = $GLOBALS["core"]->formRadio("repeatMode", "monthly", $event->repeatMode) . " " . $_SESSION["translate"]->it("Monthly");
    $month_repeat_day["begin"] = $_SESSION["translate"]->it("Beginning of each month");
    $month_repeat_day["end"]   = $_SESSION["translate"]->it("End of each month");
    $month_repeat_day["date"]  = $_SESSION["translate"]->it("Every month on Start Date");

    $template["MONTH_SELECT"] = $GLOBALS["core"]->formSelect("monthMode", $month_repeat_day, $event->monthMode, NULL, 1);

    $template["MODE_YEARLY"] = $GLOBALS["core"]->formRadio("repeatMode", "yearly", $event->repeatMode) . " " . $_SESSION["translate"]->it("Yearly");

    $input_every_num = array(1=>"1st", 2=>"2nd", 3=>"3rd", 4=>"4th");
    $input_every_day = array(
			     0=>$_SESSION["translate"]->it("Sunday"),
			     1=>$_SESSION["translate"]->it("Monday"),
			     2=>$_SESSION["translate"]->it("Tuesday"),
			     3=>$_SESSION["translate"]->it("Wednesday"),
			     4=>$_SESSION["translate"]->it("Thursday"),
			     5=>$_SESSION["translate"]->it("Friday"),
			     6=>$_SESSION["translate"]->it("Saturday")
			     );
    $input_every_month = array(
			       "a"=>$_SESSION["translate"]->it("Every Month"),
			       1=>$_SESSION["translate"]->it("January"),
			       2=>$_SESSION["translate"]->it("February"),
			       3=>$_SESSION["translate"]->it("March"),
			       4=>$_SESSION["translate"]->it("April"),
			       5=>$_SESSION["translate"]->it("May"),
			       6=>$_SESSION["translate"]->it("June"),
			       7=>$_SESSION["translate"]->it("July"),
			       8=>$_SESSION["translate"]->it("August"),
			       9=>$_SESSION["translate"]->it("September"),
			       10=>$_SESSION["translate"]->it("October"),
			       11=>$_SESSION["translate"]->it("November"),
			       12=>$_SESSION["translate"]->it("December")
			       ); 

    $template["EVERY_NUMBER"] = $GLOBALS["core"]->formSelect("everyNumber", $input_every_num, $event->every[0], NULL, 1);
    $template["EVERY_DAY"]    = $GLOBALS["core"]->formSelect("everyDay", $input_every_day, $event->every[1], NULL, 1);
    $template["EVERY_MONTH"]  = $GLOBALS["core"]->formSelect("everyMonth", $input_every_month, $event->every[2], NULL, 1);
    $template["MODE_EVERY"]   = $GLOBALS["core"]->formRadio("repeatMode", "every", $event->repeatMode) . " " . $_SESSION["translate"]->it("Every");
    $content = $GLOBALS["core"]->processTemplate($template, "calendar", "admin/repeatForm.tpl");
    return $content;
    
  }


}
?>