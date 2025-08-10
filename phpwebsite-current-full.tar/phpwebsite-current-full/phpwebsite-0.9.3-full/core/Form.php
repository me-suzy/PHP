<?php
/**
 * Controls form generation for phpWebSite
 * 
 * @version $Id: Form.php,v 1.39 2003/07/10 14:18:28 matt Exp $
 * @author  Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Core
 */
class PHPWS_Form extends PHPWS_File
{

  /**
   * Creates a simple form.
   *
   * Creates a form object using the elements provided in the $elements array.
   * It is recommended that you create the $elements array using the core functions
   * provided to create the form elements.
   *
   * This function is for simple forms only.  If you require a complex or custom form
   * DO NOT use this function.
   *
   * @author Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @param  string  $name     Name to assign to the form object.
   * @param  string  $action   File to send form data to (e.g.: index.php).
   * @param  array   $elements Array of strings containing the form elements in order of display.
   * @param  string  $method   Form method to use (default: post).
   * @param  boolean $breaks   Put a break after each element?
   * @param  boolean $file     If this form comtains a file input type
   * @return string  $string   The complete form in a string format
   * @access public
   */
  function makeForm($name, $action, $elements, $method="post", $breaks=FALSE, $file=FALSE) {
    if($file) $form[0] = "<form name=\"$name\" action=\"$action\" method=\"$method\" enctype=\"multipart/form-data\">\n";
    else $form[0] = "<form name=\"$name\" action=\"$action\" method=\"$method\">\n";
    
    if($breaks) {
      $form[1] = implode("<br />\n", $elements);
    } else {
      $form[1] = implode("\n", $elements);
    }

    $form[2] = "</form>\n";
    return implode("", $form);
  }// END FUNC makeForm()

  
  /**
   * Creates a radio button
   *
   * Creates a form radio button using the $name and $value submitted. If $match
   * exists, it checks it against the $value. A positive checks the button. If
   * $match_diff exists, $match is checked against it instead of the $value.
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string $name       Name of the radio button (make sure it matches its compliments)
   * @param  string $value      Value that is based if radio button is selected
   * @param  string $match      Value that is compared to the value or to match_diff to activate default checking
   * @param  string $match_diff Value compared to match, but only if it exists
   * @param  string $label      Label that is appended to the radio button
   * @return string $radio      Completed radio button form element
   * @access public
   */
  function formRadio($name, $value, $match=NULL, $match_diff=NULL, $label=NULL) {
    $radio = "<input type=\"radio\" name=\"$name\" value=\"$value\" ";

    if ($match !== NULL) {
      if ($match_diff !== NULL) {
	if($match == $match_diff)
	  $radio .= "checked=\"checked\" ";
      } else if ($match == $value)
	$radio .= "checked=\"checked\" ";
    }
    $radio .= "/>\n";
    
    if($label)
      $radio .= $label;
    
    return $radio;
  }// END FUNC formRadio()
  

  /**
   * Creates a hidden variable or variables for a form post
   *
   * If the name variable is an array, this function will assume
   * that an associative array of hidden variables is sent.
   * Example $array["name_of_variable"] = "value_of_variable"
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string $name    Name of the hidden variable
   * @param  string $value   Value of the hidden variable
   * @return string $content Input form string
   * @access public
   */
  function formHidden($name, $value=NULL) {
    $content = NULL;
    if (is_array($name)){
      foreach($name as $new_name=>$new_value)
	$content .= PHPWS_Core::formHidden($new_name, $new_value)."\n";
      
      return $content;
    } else
      return "<input type=\"hidden\" name=\"$name\" value=\"$value\" />\n";
  }// END FUNC formHidden()


  /**
   * Creates a file input form element.
   *
   * Creates a file input for element using the name and size parameters
   * passed in.  If no size is specified, the default size is used.
   *
   * @author Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @param  string  $name     Name of this form element.
   * @param  integer $size     Size of this form element.
   * @param  integer $maxsize  Maximum size of this form element.
   * @param  string  $label    Text label to be applied to this element.
   * @return string  $fileform The completed file input element is returned.
   * @access public
   */
  function formFile($name, $size=33, $maxsize=255, $label=NULL) {
    if($label) $fileform = $label;
    else $fileform = NULL;
    $fileform .= "<input type=\"file\" name=\"$name\" ";
    
    if ($size) $fileform .= " size=\"$size\"";
    if ($maxsize) $fileform .= " maxlength=\"$maxsize\"";
    
    $fileform .= " />\n";
    return $fileform;
  }// END FUNC formFile()

  
  /**
   * Creates a form text box
   *
   * If the size is not designated, the browser default will be used.
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string  $name      Name of form element
   * @param  string  $value     String to insert by default into the text box
   * @param  integer $size      Width of the text box
   * @param  integer $maxsize   Maximum characters allowed in the text box
   * @param  string  $label     Text to echo before the text box
   * @return string  $textfield The text box form element
   * @access public
   */
  function formTextField ($name, $value=NULL, $size=20, $maxsize=255, $label=NULL) {
    $textfield = NULL;
    if($label) $textfield = $label;
    $textfield .= "<input type=\"text\" name=\"$name\"";

    $value = str_replace("\"", "&#x0022;", $value);

    if ($value!==NULL) $textfield .= " value=\"". $value ."\"";
    if ($size) $textfield .= " size=\"$size\"";
    if ($maxsize) $textfield .= " maxlength=\"$maxsize\"";
    
    $textfield .= " />\n";
    return $textfield;
  }// END FUNC formTextField()
  
  
  /**
   * Creates a password text box
   * 
   * Unlike the textbox, a default value is not available
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string  $name     Name of the password form element
   * @param  integer $size     Size of the password box
   * @param  integer $maxsize  Maximum allowed characters
   * @return string  $password The form password box
   * @access public
   */
  function formPassword ($name, $size=20, $maxsize=255) {
    $password = "<input type=\"password\" name=\"$name\"";
    
    if ($size) $password .= " size=\"$size\"";
    if ($maxsize) $password .= " maxlength=\"$maxsize\"";
    
    $password .= " />\n";
    return $password;
  }// END FUNC formPassword()

  
  /**
   * Creates a form textarea
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string  $name     Name value of the textarea
   * @param  string  $value    Data to appear in the textarea
   * @param  integer $rows     Number of rows (lines) down to make the textarea
   * @param  integer $cols     Number of columns (characters) wide to make the textarea
   * @param  string  $label    The text label for this form element
   * @return string  $textarea Textarea form string
   * @access public
   */
  function formTextArea ($name, $value=NULL, $rows=5, $cols=40, $label=NULL) {
    $value = htmlspecialchars($value);
    $value = str_replace("&#x0024;", "&", $value);

    if (ord(substr($value, 0, 1)) == 13)
      $value = "\n" . $value;

    $textarea = NULL;
    if (!$rows)
      $rows = 6;
    
    if (!$cols)
      $cols = 60;
    
    if($label)
      $textarea = $label;

    $textarea = $label;

    $textarea .= "<textarea name=\"$name\" rows=\"$rows\" cols=\"$cols\" wrap=\"virtual\">" . $value . "</textarea>\n";
      return $textarea;
  }// END FUNC formTextArea()

  
  /**
   * Creates a form checkbox
   *
   * If '$match' is submitted, the function will compare it to the
   * current value. If there is a match, the box will default to checked.
   * If '$match_diff' is submitted, '$match' will be compared to it instead.
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string $name       Name of the checkbox
   * @param  string $value      Value reported if the checkbox is checked
   * @param  string $match      Value compared to the value/match_diff
   * @param  string $match_diff Unknown
   * @param  string $label      String echoed after the checkbox
   * @return string $checkbox   Returns the form entry
   * @access public
   */
  function formCheckBox ($name, $value="1", $match=NULL, $match_diff=NULL, $label=NULL) {
    $checkbox = "<input type=\"checkbox\" name=\"$name\" value=\"$value\" ";
    if ($match !== NULL) {
      if ($match_diff !== NULL) {
	if ($match == $match_diff) $checkbox .= "checked=\"checked\" ";
      }
      else if ($match == $value) $checkbox .= "checked=\"checked\" ";
    }
    $checkbox .= "/>\n";
    if($label) $checkbox .= $label;
    return $checkbox;
  }// END FUNC formCheckBox()


  /**
   * Creates a form selection box from an array.
   *
   * This function is sent an array. If you send an associative array, the index of the array will
   * be used as the option tag's value. The value of the array will echo in the selection box.
   * If ignore_index is used, then the value will not echo in the option (best used with non-associative
   * array). "match" will compare itself to the option tag itself unless "match_to_value" is true. In this
   * case, "match" will  instead compare itself to the option tag's value.
   * Example:
   * <option value="1">Yes</option>
   * If "match" is set, it will be compared to "Yes".
   * If "match_to_value" is set, "match" will be compared to "1"
   *
   * "onchange" can be used for javascript.
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string  $name           Name given to the select tag
   * @param  array   $opt_array      Array of options for the select tag
   * @param  string  $match          Value compared to either the value or the option tag itself
   * @param  boolean $ignore_index   If TRUE, then the index of the opt_array is not set to the value of the option
   * @param  boolean $match_to_value If TRUE, then match is compared to the option value instead of what is displayed
   * @param  string  $onchange       Allows for script to be inserted
   * @param  string  $label          The text label to give this for element
   * @return string  $option_string  The completed option selection element
   * @access public
   */  
  function formSelect($name, $opt_array, $match=NULL, $ignore_index=FALSE, $match_to_value=FALSE, $onchange=NULL, $label=NULL) {
    $option_string = $change = NULL;

    if($label) $option_string = $label;
    if (is_array($opt_array)) {
      if ($onchange) $change = " onchange=\"$onchange\"";
      $option_string .= "\n<select name=\"$name\"".$change.">\n";
      foreach($opt_array as $value=>$option) {
	if ($ignore_index) $option_string .= "<option";
	else $option_string .= "<option value=\"$value\"";
	
	if ($match_to_value) {
	  if ($value == $match) $option_string .= " selected=\"selected\"";
	} else if ($option == $match) $option_string .= " selected=\"selected\"";
	
	$option_string .= ">".$option."</option>\n";
      }
      $option_string .= "</select>\n";
      return $option_string;
    }
  }// END FUNC formSelect()


  /**
   * Creates a multiple dropdown list
   *
   * This is just an edited version of input options to do multiple select lists. The options
   * are sent as an array.
   *
   * @author   Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @modified Steven Levin <steven@NOSPAM.tux.appstate.edu>
   * @param    string   $name           The name for the select tag
   * @param    resource $opt_array      The options for the select list
   * @param    resource $match          Values to match against
   * @param    boolean  $ignore_index   Whether or not to use the key of $opt_array for the value
   * @param    boolean  $match_to_value Whether or not to match the $match to value
   * @param    integer  $size           The size of the multiple select list
   * @param    string   $label          Text to describe the form element
   * @return   string   $option_string  The select element
   * @access public
   */
  function formMultipleSelect($name, $opt_array, $match=NULL, $ignore_index=FALSE, $match_to_value=FALSE, $size=4, $label=NULL) {
    $option_string = NULL;
    if($label) $option_string = $label;
    
    if (is_array($opt_array)) {
      $name = $name . "[]";
      $option_string .= "\n<select name=\"$name\" multiple=\"multiple\" size=\"$size\">\n";
      
      foreach($opt_array as $value=>$option) {
	if($ignore_index) $option_string .= "<option";
	else $option_string .= "<option value=\"$value\"";
	
	if ($match_to_value) {
	  if (is_array($match) && in_array($value, $match)) $option_string .= " selected=\"selected\"";
	} else if (is_array($match) && in_array($option, $match)) $option_string .= " selected=\"selected\"";
	$option_string .= ">".$option."</option>\n";
      }
      $option_string .= "</select>\n";
      return $option_string;
    }
  }// END FUNC formMultipleSelect()
  

  /**
   * formSubmit
   *
   * Creates a form submit button
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string $value  The value attribute printed on the submit button
   * @param  string $name   The name associated to the submit tag
   * @param  string $class  The style sheet class attribute of the input tag
   * @return string $button The generated button tag
   * @access public
   */      
  function formSubmit($value, $name=NULL, $class=NULL) {
    $button = "<input type=\"submit\"";
    if ($name) $button .= " name=\"$name\"";
    $button .= " value=\"$value\"";
    if ($class) $button .= " class=\"$class\"";
    $button .= " />\n";
    return $button;
  }// END FUNC formSubmit()


  /**
   * Creates a form button. NOTE: This is not a submit button.
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string $value     The value attribute printed on the button
   * @param  string $name      The name attribute of the button tag
   * @param  string $onclick   The script, if any, to start when the button is clicked
   * @param  string $class     The style sheet class attribute of the input tag
   * @param  string $accesskey The keyword combination that applies to the button
   * @param  string $mouseover The script to run when the mouse is hovered over the button
   * @return string $button    The generated input tag
   * @access public
   */      
  function formButton($value, $name=NULL, $onclick=NULL, $class=NULL, $accesskey=NULL, $mouseover=NULL) {
    $button = "<input type=\"button\"";
    if ($name!==NULL) $button .= " name=\"$name\"";
    if ($class) $button .= " class=\"$mod_label\"";
    if ($accesskey) $button .= " accesskey=\"$accesskey\"";
    if ($mouseover) $button .= " onMouseOver=\"$mouseover\"";
    if ($onclick) $button .= " onClick=\"$onclick\"";
    $button .= " value=\"$value\" />\n";
    return $button;
  }// END FUNC formButton()


  /**
   * Creates an option form list based on the SQL command string $sql.
   *
   * The $option variable should contain the name of database column. The
   * data in that column appears as a selection. If the user wishes another
   * column from the database be the value, they can enter that as well. If
   * a value is entered for $selected it will be compared to the $value or
   * the $option if $value is missing. A match will select the option as default.
   *
   * Note: this is an early function. Its use is minimized in favor of formSelect.
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string $sql           SQL select query 
   * @param  string $option        Column name echoed in the option tag
   * @param  string $value         Column name echoed in the value of the option tag
   * @param  string $selected      Text compared to value to determine default selection
   * @return string $option_string The completed options
   * @access public
   */
  function formSqlSelect($sql, $option, $value=NULL, $selected=NULL) {
    $sql_result = $this->query($sql);
    while ($row = $sql_result->fetchrow(DB_FETCHMODE_ASSOC)) {
      extract($row);
      $option_string .= "<option";
      if ($value!==NULL) $option_string .= " value=\"".$$value."\"";
      if ($selected && $selected == $$value) $option_string .= " selected=\"selected\"";
      $option_string .= ">".$$option."</option>\n";
    }
    return $option_string;
  }// END FUNC formSqlSelect()

  /**
   * Returns a drop down select box based on the core settings
   *
   * Send the function a name for the date select. That name will be suffixed with its function.
   * For example : 
   * $core->formDate("widget");
   * // returns drop down boxes with widget_month, widget_day, widget_year.
   *
   * The date order is set by $date_order the dateSettings.xx.php language file, where xx is the language
   * abbreviation. The separators are ignored.
   *
   * If a date is sent, it will become the default selection for the boxes. Send the date
   * in Ymd format (eg 20020901 for September 1, 2002). If no date is sent, it defaults
   * to today's date.
   *
   * You can set the year select box by entering a start and end date into yearStart
   * and yearEnd respectively.
   *
   * When catching the date MAKE SURE you are checking to make sure it is an actual date.
   * (ie 20030229, 20021131, etc.) 
   *
   * @author                       Matthew McNaney<matt@NOSPAM.tux.appstate.edu>
   * @param   string   date_name   Name to label each form select box.
   * @param   integer  date_match  Eight digit number to default the selection box
   * @param   integer  yearStart   Date to start the year list
   * @param   integer  yearEnd     Date to end the year list
   * @return  string   date_form   Completed drop down form selection box
   * @access  public
   */
  function formDate($date_name, $date_match=NULL, $yearStart=NULL, $yearEnd=NULL, $useBlanks=FALSE){
    if (!$date_match && !$useBlanks)
      $date_match = date("Ymd");
    elseif(!$date_match && $useBlanks)
        $date_match = "        ";
    
    $y_match = substr($date_match, 0, 4);
    $m_match = substr($date_match, 4, 2);
    $d_match = substr($date_match, 6, 2);

    $month = $this->monthArray();
    $day   = $this->dayArray();

    if (is_numeric($yearStart) && is_numeric($yearEnd))
      $length = $yearEnd - $yearStart;
    elseif (is_numeric($yearStart) && $yearStart < date('Y'))
      $length = 10;
    elseif (($yearStart - (int)date('Y')) > 10){
      $length = $yearStart - (int)date('Y') + 3;
      $yearStart = (int)date('Y');
    }

    if (isset($length) && $length > 0 && $length < 1000)
      $year  = $this->yearArray($yearStart, $length);
    else
      $year  = $this->yearArray();

    if($useBlanks) {
        $day[""] = "";
        asort($day);
        reset($day);
        $month[""] = "";
        asort($month);
        reset($month);
        $year[""] = "";
        asort($year);
        reset($year);
    }

    if ($this->date_order){
      $dateOrder = $this->date_order;
      $dateOrder = preg_replace("/[^mdy]/", "", $dateOrder);
      $dateOrder = preg_replace("/(m|d|y)/", "[\\1]", $dateOrder);
      $date_form = $dateOrder;
      $date_form = str_replace("[m]", PHPWS_Core::formSelect($date_name."_month", $month, $m_match, NULL, 1), $date_form);
      $date_form = str_replace("[d]", PHPWS_Core::formSelect($date_name."_day", $day, $d_match, NULL, 1), $date_form);
      $date_form = str_replace("[y]", PHPWS_Core::formSelect($date_name."_year", $year, $y_match, NULL, 1), $date_form);
    } else {
      $date_form .= PHPWS_Core::formSelect($date_name."_month", $month, $m_match, NULL, 1);
      $date_form .= PHPWS_Core::formSelect($date_name."_day", $day, $d_match, NULL, 1);
      $date_form .= PHPWS_Core::formSelect($date_name."_year", $year, $y_match, NULL, 1);
    }
    return $date_form;
  }// END FUNC formDate

  /**
   * Creates a clock drop down selection box
   *
   * The drop down box will be displayed in the format specified by the core
   * settings. Send the function the "time" and the drop down boxes
   * will have that time selected by default.
   *
   * Make sure to sent the increment as a realistic one (try 5, 10, 15 not 7, 9)
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string  $var_name  Name to be applied to the select tag
   * @param  string  $time      Four digit time to used as a default
   * @param  integer $increment How many minutes to increment
   * @return string  $clock     The completed drop down clock box
   * @access public
   */
  function clock ($var_name, $time=NULL, $increment=15){
    if (preg_match("/g/", $this->time_format))
      $hour = $this->interval(12, 1);
    elseif (preg_match("/G/", $this->time_format)){
      $hour = $this->interval(23, 0);
      $military = 1;
    }
    elseif (preg_match("/h/", $this->time_format)){
      $hour = $this->interval(12, 1);
      foreach ($hour as $key=>$old_hour){
	if ((int)$old_hour < 10)
	  $hour[$key] = "0".(string)$old_hour;
      }
    } elseif (preg_match("/H/", $this->time_format)){
      $hour = $this->interval(23, 0);
      $military = 1;
      foreach ($hour as $key=>$old_hour){
	if ((int)$old_hour < 10)
	  $hour[$key] = "0".(string)$old_hour;
      }
    }
    
    $minute = $this->interval(59, 0, $increment);
    $current_hour = floor($time/100);
    
    if ($current_hour == "12"){
      $clock_hour = "12";
      $clock_ampm = 1;
    } elseif ($current_hour == "00") {
      $clock_hour = "12";
      $clock_ampm = 0;
    } else {
      $clock_hour = $current_hour % 12;
      $clock_ampm = floor($current_hour/12);
    }
    
    $clock_minute = $time%100;
    
    foreach ($minute as $key=>$old_min){
      if ((int)$old_min < 10)
	$minute[$key] = "0".(string)$old_min;
    }
    
    $clock = $this->formSelect($var_name."_hour", $hour, $clock_hour, 1);
    $clock .= $this->formSelect($var_name."_minute", $minute, $clock_minute, 1);
    
    if (!isset($military) || $military != 1){
      if (preg_match("/a/", $this->time_format))
	$clock .= $this->formSelect($var_name."_ampm", array(0=>"am", 1=>"pm"), $clock_ampm, NULL, 1);
      else
	$clock .= $this->formSelect($var_name."_ampm", array(0=>"AM", 1=>"PM"), $clock_ampm, NULL, 1);
    }
    
    return $clock;
  }// END FUNC clock()

}//END CLASS CLS_form

?>
