<?php
class PHPWS_Poll extends PHPWS_Item {

  /**
   * Holds the question of the poll.
   * @var		string
   * @access	private
   */
  var $_body = NULL;

  /**
   * List of Ips that have voted, for non-restricted polls only.
   * @var		array
   * @access	private
   */
  var $_votedIps = array();

  /**
   * List of options for the poll.
   * @var		array
   * @access	private
   */
  var $_options = NULL;

  /**
   * Number of votes for each of the options.
   * @var		array
   * @access	private
   */
  var $_counts = NULL;

  /**
   * When set to Yes, only registered users can vote.
   * When set to No, all users can vote.
   * @var		string
   * @access	private
   */
  var $_restricted = "Yes";

  /**
   * When set to Yes, this poll will be displayed.
   * Only one poll can be active at any time.
   * @var		string
   * @access	private
   */
  var $_active = "No";
	
  /**
   * When set to Yes, comments are allowed.
   * @var		string
   * @access	private
   */
  var $_allowComments = "Yes";

  /**
   * Contructor of the PHPWS_Poll class.
   * 
   * @author Feng Pan <fp38660@tux.appstate.edu>
   * @param	int		$my_id	ID of the poll
   * @access public
   */
  function PHPWS_Poll($my_id=NULL) {
    $this->setTable("mod_poll");
    if(isset($my_id)) {
      $this->setId($my_id);
      $this->init();
      $this->_options = explode(";", $this->_options);
      $this->_counts = explode(";", $this->_counts);

     if (count($this->_votedIps))
       $this->_votedIps = explode(";", $this->_votedIps);
    }
  }
	
  /**
  * Displays editing menu for both creating new poll and editing existing poll.
  * This function does not commit any changes to the poll.
  *
  * @author Feng Pan <fp38660@tux.appstate.edu>
  * @access public
  */
  function edit() {
   $content = NULL;

   if($this->_id) {
     $title = "Edit Poll";
   } else {
     $title = "New Poll";
   }

   if (isset($_REQUEST["title"])) {
     $requestTitle = PHPWS_Text::parseInput($_REQUEST["title"]);
   } else {
     $requestTitle = NULL;
   }


   if (isset($_REQUEST["restricted"])) {
     $requestRestricted = $_REQUEST["restricted"];
   } else {
     $requestRestricted = NULL;
   }

   if (isset($_REQUEST["active"])) {
      $requestActive = $_REQUEST["active"];
   } else {
      $requestActive = NULL;
   }

   if (isset($_REQUEST["allowComments"])) {
      $requestComments = $_REQUEST["allowComments"];
   } else {
      $requestComments = NULL;
   }

   if (isset($_REQUEST["question"])) {
      $requestQuestion = PHPWS_Text::parseInput($_REQUEST["question"]);
   } else {
      $requestQuestion = NULL;
   }
		
   $tags["OLD_OPTIONS"] = $tags["NEW_OPTION"] = $tags["TITLE"] = $tags["RESTRICTED"] = 
        $tags["ACTIVE"] = $tags["COMMENTS"]= NULL;

   $opt_array = array("Yes"=>"Yes", "No"=>"No");

   $tags["TITLE"] = PHPWS_Core::formTextField("title", $this->_label);
   $tags["RESTRICTED"] = PHPWS_Core::formSelect("restricted", $opt_array, $this->_restricted).
      CLS_help::show_link("poll", "registered_users");
   $tags["ACTIVE"] = PHPWS_Core::formSelect("active", $opt_array, $this->_active).
      CLS_help::show_link("poll", "active");
   $tags["COMMENTS"] = PHPWS_Core::formSelect("allowComments", $opt_array, $this->_allowComments).
      CLS_help::show_link("poll", "comments");
   $tags["QUESTION_FIELD"] = PHPWS_Core::formTextField("question", $this->_body, 100);
   $op_cnt = 0;

   if(isset($_REQUEST["poll_op"]) && 
      ($_REQUEST["poll_op"] == "Add Option" || $_REQUEST["poll_op"] == "newpoll" 
       || $_REQUEST["poll_op"] == "Submit")) {

     while(isset($_REQUEST["OPTION$op_cnt"]) && ($_REQUEST["OPTION$op_cnt"] != "")) {
        $tags["OLD_OPTIONS"] .= "<br />";
	$tags["OLD_OPTIONS"] .= PHPWS_Core::formTextField("OPTION$op_cnt", PHPWS_Text::parseInput($_REQUEST["OPTION$op_cnt"]));
	$op_cnt++;
     }

     $tags["TITLE"] = PHPWS_Core::formTextField("title", $requestTitle);
     $tags["RESTRICTED"] = PHPWS_Core::formSelect("restricted", $opt_array, $requestRestricted).
       CLS_help::show_link("poll", "registered_users");
     $tags["ACTIVE"] = PHPWS_Core::formSelect("active", $opt_array, $requestActive).
      CLS_help::show_link("poll", "active");
     $tags["COMMENTS"] = PHPWS_Core::formSelect("allowComments", $opt_array, $requestComments).
      CLS_help::show_link("poll", "comments");
     $tags["QUESTION_FIELD"] = PHPWS_Core::formTextField("question", $requestQuestion, 100);

     if($op_cnt < 1) {
       $tags["NEW_OPTION"] .= "<br />";
     }
     $tags["NEW_OPTION"] .= PHPWS_Core::formTextField("OPTION$op_cnt", NULL);

  } elseif (count($this->_options) > 0) {
      foreach($this->_options as $value) {
	$tags["OLD_OPTIONS"] .= "<br />";
	$tags["OLD_OPTIONS"] .= PHPWS_Core::formTextField("OPTION$op_cnt", $value);
	$op_cnt++;
      }
  }
     $tags["ADD"] = PHPWS_Core::formSubmit("Add Option", "poll_op");
     $tags["SUBMIT_BUTTON"] = PHPWS_Core::formSubmit("Submit", "poll_op");

     $elements[0] = PHPWS_Core::formHidden("module", "poll");
     $elements[0] .= $GLOBALS["core"]->processTemplate($tags, "poll", "edit.tpl");
     $content .= PHPWS_Core::makeForm("edit_poll", "index.php", $elements);

     $_SESSION["OBJ_layout"]->popbox($title, $content, NULL,"CNT_POLL");
  }

  /**
   * Commit changes made to the poll
   * 
   * @author Feng Pan <fp38660@tux.appstate.edu>
   * @access public
   */
   function send() {
     if($_POST["question"]) {
	$this->_body = $GLOBALS['core']->parseInput($_POST["question"]);
     } else {
	$title = $_SESSION["translate"]->it("Error");
	$content = $_SESSION["translate"]->it("Missing Question!");
	$_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");
	$this->edit();
	return;
     }

     $op_cnt = 0;
     if($_POST["OPTION0"]) {
	while(isset($_POST["OPTION$op_cnt"]) && (strlen($_POST["OPTION$op_cnt"]) >= 1)) {
	  $this->_options[$op_cnt] = $_POST["OPTION$op_cnt"];

	  if(!isset($this->_counts[$op_cnt])) {
	    $this->_counts[$op_cnt] = 0;
	  }

	 $op_cnt++;
	} 
     } else {
       $title = $_SESSION["translate"]->it("Error");
       $content = $_SESSION["translate"]->it("No option set!");
       $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");
       $this->edit();
       return;
     }

     if($_POST["title"]) {
	$this->_label = $GLOBALS['core']->parseInput($_POST["title"]);
     } else {
        $title = $_SESSION["translate"]->it("Error");
        $content = $_SESSION["translate"]->it("No option set!");
        $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");
	$this->edit();
	return;
     }
		
     $this->_restricted = $_POST["restricted"];
     $this->_active = $_POST["active"];
     $this->_allowComments = $_POST["allowComments"];

     if($this->_active == "Yes") {
	$db_array = array("active"=>"No");
	$result = $GLOBALS["core"]->sqlUpdate($db_array, "mod_poll", "active", 'Yes');
     }
			
     
     if($this->_id) {
       $title = $_SESSION["translate"]->it("Poll Updated");
       $content = $_SESSION["translate"]->it("Your poll was successfully updated!");
     } else {
       $title = $_SESSION["translate"]->it("Poll Created");
       $content = $_SESSION["translate"]->it("Your poll was successfully created!");
     }

     $this->commit_changes();

     $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");
					
   }

   /**
    * Display voting box on screen.
    *
    * @author Feng Pan <fp38660@tux.appstate.edu>
    * @access public
    */
    function showUserBox() {
      $title = $this->_label;
      $tags['OPTIONS'] = NULL;
      $tags["QUESTION"] = $this->_body;
      $op_cnt = 0;

      foreach($this->_options as $value) {
	$tags["OPTIONS"] .= PHPWS_Core::formRadio("OPTIONS", $op_cnt, NULL, NULL, $value)."<br />";
	$op_cnt++;
      }

      $tags["VIEW_RESULT"] = "<a href=\"index.php?module=poll&amp;poll_op=result&amp;poll_id=".$this->_id."\">[View Result]</a>";
      $tags["VOTE"] = PHPWS_Core::formSubmit("Vote", "poll_op");

      $total_count = 0;
      foreach($this->_counts as $value) {
	$total_count = $total_count + $value;
      }

      $tags["TOTAL_COUNT"] = $total_count;
      if($GLOBALS['core']->moduleExists("comments") && ($this->_allowComments == "Yes")) {
	$tags["COMMENT_COUNT"] = $_SESSION['PHPWS_CommentManager']->numComments("poll", $this->_id);
      }
		
      $elements[0] = PHPWS_Core::formHidden("module", "poll");
      $elements[0] .= $GLOBALS["core"]->processTemplate($tags, "poll", "userbox.tpl");

      $content = PHPWS_Core::makeForm("vote_poll", "index.php", $elements);
      $GLOBALS['CNT_POLL_BOX']['title'] = $title;
      $GLOBALS['CNT_POLL_BOX']['content'] = $content;
    }
	
    /**
     * Commit changes to the poll after user votes, also display results
     *
     * @author Feng Pan <fp38660@tux.appstate.edu>
     * @access public
     */
     function vote() {
	if(!isset($_REQUEST["OPTIONS"])) {
  	  $title = $_SESSION["translate"]->it("Error");
	  $content = $_SESSION["translate"]->it("No option selected.");
	  $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");
	  return;
	} else {
	  $this->_counts[$_REQUEST["OPTIONS"]]++;
	}		

	$uservar = "voted".$this->_id;
	if($this->_restricted == "Yes") {
	  if($_SESSION["OBJ_user"]->isUser() == false) {
	     $content = $_SESSION["translate"]->it("This vote is for registered user only, please log in to vote.");
	  } elseif ($_SESSION["OBJ_user"]->getUserVar($uservar) == "true") {
	     $content = $_SESSION["translate"]->it("You have already voted!");
	  } else {
	     $_SESSION["OBJ_user"]->setUserVar($uservar, "true");
	     $_SESSION["OBJ_user"]->modSettings["poll"][$uservar] = "true";
	  }
	} else {
	  $current_ip = $_SERVER['REMOTE_ADDR'];
	  foreach($this->_votedIps as $ip) {
	    if ($current_ip == $ip) {
	       $content = $_SESSION["translate"]->it("You have already voted!");
	    }
	}
	  if(!isset($content)) {
	    array_push($this->_votedIps, $current_ip);
	  }
	}
		
	if(isset($content)) {
	  $title = "Error!";
	  $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");
	  return;
	}

	$this->commit_changes();
		
	$title = $_SESSION["translate"]->it("Vote Submitted");
	$content = $_SESSION["translate"]->it("Your vote was submitted sucessfully!");
	$_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");

	$this->showResult();
     }

     /**
      * Display results and comments (if possible) of the poll.
      *
      * @author Feng Pan <fp38660@tux.appstate.edu>
      * @access public
      */
      function showResult() {
	$tags["OPTIONS"] = NULL;
	$title = "Poll Results: &nbsp;".$this->_body;
		
	$op_cnt = 0;
	$total_votes = 0;
	foreach($this->_counts as $cnt) {
	  $total_votes = $total_votes + $cnt;
	}

	foreach($this->_options as $value) {
	  $tags["OPTIONS"] .= "<tr><td align=\"right\">".$value."&nbsp;&nbsp;</td>";
	  if($total_votes == 0) {
	     $percent = 0;
	  } else {
	     $percent = $this->_counts[$op_cnt]/$total_votes*100;
	     $percent = sprintf("%.1f", $percent);
	  }

	  $tags["OPTIONS"] .= "<td align=\"left\"> "
			   . $GLOBALS['core']->imageTag(PHPWS_SOURCE_HTTP . "mod/poll/img/mainbar-blue.gif", $percent . "%", $percent, 16)
			    . " ".$percent. " % (".$this->_counts[$op_cnt].")</td></tr>";
	  $op_cnt++;
	}
		
        $total_count = 0;
	foreach($this->_counts as $value) {
	  $total_count = $total_count + $value;
	}

	$tags["TOTAL_COUNT"] = $total_count;
		
        if($GLOBALS['core']->moduleExists("comments") && ($this->_allowComments == "Yes")) {
	  $_SESSION['PHPWS_CommentManager']->listCurrentComments("poll", $this->_id, TRUE);
	}

	$elements[0] = PHPWS_Core::formHidden("module", "poll");
	$elements[0] .= $GLOBALS["core"]->processTemplate($tags, "poll","result.tpl");
        $content = PHPWS_Core::makeForm("poll_result", "index.php", $elements);
        $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");
      }

      /**
       * Make string to array conversions and commit changes to the poll.
       *
       * @author Feng Pan <fp38660@tux.appstate.edu>
       * @access private
       */
      function commit_changes() {
	if(count($this->_options) != 0) {
	  $this->_options = implode(";", $this->_options);
	}

	if(count($this->_counts) != 0) {
 	  $this->_counts = implode(";", $this->_counts);
	}

	if(count($this->_votedIps) != 0) {
	  $this->_votedIps = implode(";", $this->_votedIps);
	}

	$this->commit();

	$this->_options = explode(";", $this->_options);
	$this->_counts = explode(";", $this->_counts);
	if (count($this->_votedIps))
	  $this->_votedIps = explode(";", $this->_votedIps);
      }	
	
}
?>
