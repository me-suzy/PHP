<?php
require_once (PHPWS_SOURCE_DIR . "/mod/faq/conf/faq.php");

/**
 * This class controls interactions with the Faq module and it's PHPWS_Faq
 * objects.
 *
 * @version $Id: FaqManager.php,v 1.30 2003/07/06 21:35:50 darren Exp $
 * @author Darren Greene <dg49379@appstate.edu>
 * @package Faq
 */
class PHPWS_FaqManager extends PHPWS_Manager {

  /**
   * Most recent faq that Manager has accessed
   *
   * @var object
   * @example $this->_currentFAQ = new PHPWS_Faq();
   * @access private
   */
  var $_currentFAQ;

  /**
   * Current layout view choosen by admin
   *
   * 0 => No Categories - Clickable Questions
   *   => PHPWS_FAQ_NOCAT_CLICKQUES_VIEW
   * 1 => No Categories - Question and Answer
   *   => PHPWS_FAQ_NOCAT_QA_VIEW
   * 2 => Fatcat Categories 
   *   => PHPWS_FAQ_CAT_VIEW
   *
   * @var integer
   * @example $this->_currentLayout = PHPWS_FAQ_CAT_VIEW;
   * @access private
   */
  var $_currentLayout = 0;

  /**
   * Flag to indicate the use of bookmarks for 'Question and Answer' view
   *
   * @ver integer
   * @example $this->_useBookmarks = 0;
   * @access private
   */
  var $_useBookmarks = 1;

  /**
   * Number of FAQs to display in the 'Question and Answer' view
   *
   * @var integer
   * @example $this->_pagingLimit = 6;
   * @access private
   */
  var $_pagingLimit = 5;

  /**
   * Flag to indicate if users not logged in can score a FAQs
   *
   * @var integer
   * @example $this->_allowScoring = 0;
   * @access private
   */
  var $_allowScoring = 0;

  /**
   * Flag to indicate if FAQs can have user comments
   *
   * @var integer
   * @example $this->_allowComments = 0;
   * @access private
   */
  var $_allowComments = 0;

  /**
   * Flag to indicate if users can suggest FAQs
   *
   * @var integer
   * @example $this->_allowSuggestions = 0;
   * @access private
   */
  var $_allowSuggestions = 0;

  /** 
   * Rating legend for scoring FAQs
   *
   * @var array
   * @example $this->_scoringLenged = array(1=>"High Score");
   * @access private
   */
  var $_scoringLegend = NULL;

  /**
   * Link back to FAQ listing
   *
   * @var text
   * @example $this->_faqLinkBack = "<a href='index.php?module=faq&amp;FAQ_op=view'>";
   * @access private
   */
  var $_faqLinkBack = NULL;

  /**
   * Constructor for PHPWS_FaqManager class
   *
   * @access public
   */
  function PHPWS_FaqManager() {
    $_SESSION["hasScored"] = "";

    /* initialize inherited manager class */
    $this->setModule("faq");
    $this->setRequest("FAQ_MAN_OP");
    $this->setTable("mod_faq_questions");
    $this->init();

    /* grab stored settings from database */
    $settingsRow = $GLOBALS["core"]->sqlSelect("mod_faq_settings");
    
    /* initialize this FAQ manager with values from database */
    if($settingsRow) {
      $this->_allowScoring     = $settingsRow[0]["anon"];
      $this->_allowComments    = $settingsRow[0]["comments"];
      $this->_allowSuggestions = $settingsRow[0]["suggestions"];
      $this->_scoringLegend    = unserialize($settingsRow[0]["score_text"]);
      $this->_currentLayout    = $settingsRow[0]["layout_view"];
      $this->_useBookmarks     = $settingsRow[0]["use_bookmarks"];
      $this->_pagingLimit      = $settingsRow[0]["paging_limit"];
    }
  } //END FUNC PHPWS_FaqManager()

  /**
   * displays menu to access and manage FAQs
   *
   * @access public
   */
  function menu() {
    /* Determine if user is allowed to configure setting for FAQ module */
    if($_SESSION["OBJ_user"]->admin_switch) {      
      /* ADMIN */

      $viewText = $_SESSION["translate"]->it("View");
      if(isset($_REQUEST["FAQ_op"]) && $_REQUEST["FAQ_op"] == "viewFAQs") {
	$tags["ACTIVE_VIEW_FAQ_LABEL"] = $viewText;
      } else {
        $tags["VIEW_FAQ_LABEL"] = $viewText;
        $tags["VIEW_FAQ_OP"] = "viewFAQs";
      }

      $newText = $_SESSION["translate"]->it("New");
      if(isset($_REQUEST["FAQ_op"]) && $_REQUEST["FAQ_op"] == "newFAQ") {
        $tags["ACTIVE_NEW_FAQ_LABEL"] = $newText;
      } else {
        $tags["NEW_FAQ_LABEL"] = $newText;
        $tags["NEW_FAQ_OP"] = "newFAQ";
      }

      $statsText = $_SESSION["translate"]->it("Stats");
      if(isset($_REQUEST["FAQ_op"]) && $_REQUEST["FAQ_op"] == "viewStats") {
	$tags["ACTIVE_STATS_FAQ_LABEL"] = $statsText;
      } else {
	$tags["STATS_FAQ_LABEL"] = $statsText;
        $tags["STATS_FAQ_OP"] = "viewStats";
      }

      $unapprovedText = $_SESSION["translate"]->it("Unapproved / Hidden");
      if(isset($_REQUEST["FAQ_op"]) && $_REQUEST["FAQ_op"] == "viewUnapprovedHidden") {
        $tags["ACTIVE_UNAPPROVED_FAQ_LABEL"] = $unapprovedText;
      } else {
	$tags["UNAPPROVED_FAQ_LABEL"] = $unapprovedText;
        $tags["UNAPPROVED_FAQ_OP"] = "viewUnapprovedHidden";
      }


      if($_SESSION["OBJ_user"]->allow_access("faq", "change_settings")) {
        $settingsText = $_SESSION["translate"]->it("Settings");
        if(isset($_REQUEST["FAQ_op"]) && $_REQUEST["FAQ_op"] == "viewSettings") {
  	  $tags["ACTIVE_SETTINGS_FAQ_LABEL"] = $settingsText;
	} else {
  	  $tags["SETTINGS_FAQ_LABEL"] = $settingsText;
  	  $tags["SETTINGS_FAQ_OP"] = "viewSettings";
	}

      }

      $tags["TITLE"] = $_SESSION["translate"]->it("FAQ (Frequently Asked Questions)");

      $elements[0] = PHPWS_Core::formHidden("module", "faq");
      $elements[0] .= $GLOBALS["core"]->processTemplate($tags, "faq", "menu.tpl"); 
      $content = PHPWS_Core::makeform("faq_admin_menu", "index.php", $elements);

      $GLOBALS["CNT_faq_body"]["content"] .= $content;
    }
    else {
      /* Normal User */
      if($this->_allowSuggestions) {
        $suggestText = $_SESSION["translate"]->it("Suggest a FAQ");
        if(isset($_REQUEST["FAQ_op"]) && $_REQUEST["FAQ_op"] == "suggestFAQ") {
   	  $tags["ACTIVE_SUGGEST_FAQ_LABEL"] = $suggestText;
	} else {
   	  $tags["SUGGEST_FAQ_LABEL"] = $suggestText;
	  $tags["SUGGEST_FAQ_OP"] = "suggestFAQ";
	}

        $viewText = $_SESSION["translate"]->it("View");
        if(isset($_REQUEST["FAQ_op"]) && $_REQUEST["FAQ_op"] == "viewFAQs") {
	  $tags["ACTIVE_VIEW_FAQ_LABEL"] = $viewText;
        } else {
          $tags["VIEW_FAQ_LABEL"] = $viewText;
          $tags["VIEW_FAQ_OP"] = "viewFAQs";
        }
	
	$tags["TITLE"] = $_SESSION["translate"]->it("FAQ (Frequently Asked Questions)");
        $elements[0]  = PHPWS_Core::formHidden("module", "faq");
        $elements[0] .= PHPWS_Core::formHidden("FAQ_user", "normal");
        $elements[0] .= $GLOBALS["core"]->processTemplate($tags, "faq", "menu.tpl"); 
        $content      = PHPWS_Core::makeform("faq_admin_menu", "index.php", $elements);

	$GLOBALS["CNT_faq_body"]["content"] .= $content;
      } 
    }
  } //END FUNC menu()

  /**
   * Show FAQs that have not been approved or are hidden
   *
   * @access private
   */
  function _viewUnapproved() {
    /* list obtained through inherited manager */
    $title   = $_SESSION["translate"]->it("Unapproved / Hidden FAQs");
    $content = $this->getList("unapproved", $_SESSION["translate"]->it("Unapproved / Hidden FAQs"));

    $GLOBALS["CNT_faq_body"]["content"] .= $content;

    $this->setListFunction("_viewUnapproved");
  } //END FUNC _viewUnapproved()

  /**
   * allows editing of faqs
   *
   * If multiple ids are passed then only the first id in the array
   * is choosen to be edited.
   *
   * @param array
   * @access private
   */
  function _edit($ids = NULL) {
    /* check to see if have access to edit FAQ */
    if($_SESSION["OBJ_user"]->admin_switch) {
      /* if more than one id then only choose first id */
      if($ids) {
        $this->_currentFAQ = new PHPWS_Faq($ids[0]);
        $this->_currentFAQ->edit();
      }
      else {
        /* no id passed so create new FAQ */
        $this->_currentFAQ = new PHPWS_Faq();
        $this->_currentFAQ->edit();
      }
    }
    else {
      $error = $_SESSION["translate"]->it("You are not authorized to perform this action.");
      $errorObj = new PHPWS_Error("darren_notes", "PHPWS_FAQ_MANAGER::_edit()", $error);
      $errorObj->errorMessage("CNT_faq_body");
    }
  } //END FUNC _edit()


  /**
   * allows deletion of FAQs from database
   *
   * @param array
   * @access private
   */
  function _delete($ids) {
    /* check to see if any FAQs to delete */
    if($ids) {

      /* check to see if more than one FAQ needs deletion */
      if(sizeOf($ids) >= 1 || $_REQUEST["ids"]) {

        /* check to see if ready to delete */
        if(isset($_REQUEST["MYES"])) { 
          $errorObj = NULL;

          /* delete FAQs */
          $idarr = explode(",", $_REQUEST["ids"]);

          foreach ($idarr as $id) {

            if($id && !PHPWS_Error::isError($errorObj)) {

	      $this->_currentFAQ = new PHPWS_Faq($id);
              $errorObj = $this->_currentFAQ->kill();

              /* if removed from database then remove entry from fatcat */
	      if($_SESSION["OBJ_fatcat"] && !PHPWS_Error::isError($errorObj)) {
		$_SESSION["OBJ_fatcat"]->purge($id, "faq");
	      }
	    }
	  }

          /* determine if error occurred */
          if(PHPWS_Error::isError($errorObj)) {

	    $errorObj->errorMessage;   //print error

	  } else {

            $_SESSION["SES_FAQ_STATS"]->init();  //reset stats

	    $tags["TITLE"] = $_SESSION["translate"]->it("Removed Entries");
	    $tags["MESSAGE_BODY"] = $_SESSION["translate"]->it("FAQ(s) have been removed from database.").
	                         "<br /><br />".$this->_faqLinkBack.$_SESSION["translate"]->it("Return to Listing")."</a>";
	    $content = $GLOBALS["core"]->processTemplate($tags, "faq", "general_msg.tpl");
	    $GLOBALS["CNT_faq_body"]["content"] .= $content;

	  }

        } else if(isset($_REQUEST["MNO"])) {

  	    /* user aborted deletion */
	    $tags["TITLE"] = $_SESSION["translate"]->it("Action Canceled");
	    $tags["MESSAGE_BODY"] = $_SESSION["translate"]->it("FAQ(s) have not been removed.").
                                    "<br /><br />".$this->_faqLinkBack.$_SESSION["translate"]->it("Return to Listing").
                                    "</a>";

            $content = $GLOBALS["core"]->processTemplate($tags, "faq", "general_msg.tpl");
   	    $GLOBALS["CNT_faq_body"]["content"] .= $content;

	} else {
	  /* first time through show confirmation */
         $title = $_SESSION["translate"]->it("Confirm");
         $content = $_SESSION["translate"]->it("Are you sure you want to delete the following FAQs?")."<br />";
    
	 /* show question so user can identify FAQ */
	 $arrids[0] = "";
         foreach ($ids as $id) {
	  $this->_currentFAQ = new PHPWS_Faq($id);
          $content .= "<br /><b>".$this->_currentFAQ->getQuestion()."</b>";
          $arrids[0] .= $id.",";
 	 }

         /* passed list of ids with form for later deletion */
         $associativeIds["ids"] = $arrids[0];

         $formElements[0]  = PHPWS_Core::formHidden($associativeIds);
         $formElements[0] .= PHPWS_Core::formHidden("PHPWS_MAN_ITEMS[]");
         $formElements[0] .= PHPWS_Core::formHidden("FAQ_MAN_OP", "delete");
         $formElements[0] .= "<br />";
         $formElements[0] .= PHPWS_Core::formSubmit("Yes", "MYES");
         $formElements[0] .= PHPWS_Core::formSubmit("No", "MNO");

	 $action = "index.php?module=faq";
         $content .= PHPWS_Core::makeForm("delete_multiple", $action, $formElements); 

	 $GLOBALS["CNT_faq_body"]["content"] .= $content;
	}
      }
    }
  } //END FUNC _delete()


  /** 
   * allows viewing a FAQ
   *
   * @param array
   * @access private
   */
  function _view($ids, $approvalView = NULL) {
    /* determine if any FAQs to view */
    if($ids) {

      $this->_currentFAQ = new PHPWS_Faq($ids[0]);

      if($approvalView === NULL) {

        $this->_currentFAQ->view($this->_allowComments, $this->_allowScoring, $this->_faqLinkBack);

      } else {

	$this->_currentFAQ->view(0, 0, NULL, 1);
      }
    }
  } //END FUNC _view()


  /**
   * administor choose to change the composite score of a FAQ
   *
   * @access private
   */
  function _scoreCurrentFAQ() {
    if($this->_currentFAQ) {
      $this->_currentFAQ->addScore($_REQUEST["score_faq"] + 1);
      $this->_currentFAQ->view($this->_allowComments, $this->_allowScoring, $this->_faqLinkBack);
    }
    else {
      //build error object
    }
  } //END FUNC _scoreCurrentFAQ()
  

  /**
   * used by layout view functions
   *
   * @access private
   */
  function _pageCurrentView(&$pagedIds, &$pagingInfo, &$tags) {
      $pagedIds   = $_SESSION["SES_FAQ_STATS"]->pageFAQs();
      $pagingInfo = $_SESSION["SES_FAQ_STATS"]->getPagingInfo();
 
      /* set tags for showing paging info */
      if($_SESSION["SES_FAQ_STATS"]->isPagerNeeded() ||
         $this->_currentLayout == PHPWS_FAQ_NOCAT_CLICKQUES_VIEW
	) {
        $tags["PAGING_BACKWARD_LINK"] = $pagingInfo["BackLink"];
        $tags["PAGING_SECTION_LINKS"] = $pagingInfo["SectionLinks"];
        $tags["PAGING_FORWARD_LINK"]  = $pagingInfo["ForwardLink"];
        $tags["PAGING_LIMIT_LINKS"]   = $pagingInfo["LimitLinks"];
        $tags["PAGING_SECTION_INFO"]  = $_SESSION["translate"]->it("Currently viewing FAQS ").$pagingInfo["SectionInfo"];
      }
  }


  /**
   * shows the setting for showing the layout option of having a list of clickable questions
   *
   * @access private
   */
  function _noCategoriesQuesClickView() {
    $rowContents = NULL;
    /* check to see if any FAQs to display */
    if($_SESSION["SES_FAQ_STATS"]->getViewable() > 0) {
      $this->_pageCurrentView($pagedIds, $pagingInfo, $tags);

      $rowClass = "";
      foreach ($pagedIds as $row) {
	$query     = "SELECT id, label, compScore FROM ".$GLOBALS["core"]->tbl_prefix."mod_faq_questions WHERE id = ".$row['id'];
	$rowResult = $GLOBALS["core"]->getAll($query);

        PHPWS_WizardBag::toggle($rowClass, " class=\"bg_light\"");
        $subTags["ROW_CLASS"] = $rowClass;

        $viewLink  = "<a ";
        $viewLink .= "href=\"index.php?module=faq&amp;FAQ_op=view&amp;FAQ_id=";
	$viewLink .= $rowResult[0]["id"]."\">".$rowResult[0]["label"]."</a>";

	$subTags["QUESTION_LABEL"] = $viewLink;
        $subTags["RATING_LABEL"]   = $rowResult[0]["compScore"];
        $rowContents .= $GLOBALS["core"]->processTemplate($subTags, "faq", "userLists/nocategories/row.tpl");
      }

      $tags["LIST_ITEMS"] = $rowContents;
    }
    else {
      $tags["NO_ITEMS"] = $_SESSION["translate"]->it("No FAQs to display");
    }

    $content = $GLOBALS["core"]->processTemplate($tags, "faq", "userLists/nocategories/list.tpl");

    $prefixTitle = $_SESSION["translate"]->it("FAQs &nbsp;");
    if(!isset($pagingInfo))
      $pagingInfo["SectionInfo"] = "";

    $GLOBALS["CNT_faq_body"]["content"] .= $content;
  } //END FUNC _noCategoriesQuesClickView()


  /**
   * shows the setting for showing the layout option of having questions followed by an answer
   *
   * @access private
   */
  function _noCategoriesQuesAnsView() {
    /* check to see if any FAQs to display */
    if($_SESSION["SES_FAQ_STATS"]->getViewable() > 0) {
      $this->_pageCurrentView($pagedIds, $pagingInfo, $tags);

      $rowContents = "";
      foreach ($pagedIds as $row) {
	$query     = "SELECT id, label, answer, compScore FROM ".$GLOBALS["core"]->tbl_prefix."mod_faq_questions WHERE id = ".$row['id'];
	$rowResult = $GLOBALS["core"]->getAll($query);

        $subTags["QUESTION_LABEL"]    = "<b><span style=\"color:red;\">".$_SESSION["translate"]->it("Q").":</span></b>";
	$subTags["QUESTION_CONTENTS"] = $rowResult[0]["label"];

	$subTags["ANSWER_LABEL"]      = "<b><span style=\"color:red;\">".$_SESSION["translate"]->it("A");
	$subTags["ANSWER_LABEL"]     .= ":</span></b>";
        $subTags["ANSWER_CONTENTS"]   = $rowResult[0]["answer"];

        if($this->_allowScoring) {
	  $address        = $GLOBALS["core"]->source_http."index.php";
	  $displayContent = $_SESSION["translate"]->it("feedback");
	  $get_var["module"] = "faq";
	  $get_var["FAQ_op"] = "view";
	  $get_var["FAQ_id"] = $row["id"];
	  $subTags["RATING_OPTION"] = PHPWS_Text::link($address, $displayContent, NULL, $get_var);
	  $subTags["RATING_OPTION"] = "[ ".$subTags["RATING_OPTION"]." ]";
        }

        $rowContents .= $GLOBALS["core"]->processTemplate($subTags, "faq", "userLists/quesAns/row.tpl");
      }

      $tags["LIST_ITEMS"] = $rowContents;
    }
    else {
      $tags["NO_ITEMS"] = $_SESSION["translate"]->it("No FAQs to display");
    }

    $content = $GLOBALS["core"]->processTemplate($tags, "faq", "userLists/quesAns/list.tpl");

    $GLOBALS["CNT_faq_body"]["content"] .= $content;
  } //END FUNC _noCategoriesQuesAnsView()


  /**
   * shows the setting for showing the layout option of having questions followed by an answer
   *
   * @access private
   */
  function _noCategoriesBookmarkesQuesAnsView() {
    /* check to see if any FAQs to display */
    if($_SESSION["SES_FAQ_STATS"]->getViewable() > 0) {
      $this->_pageCurrentView($pagedIds, $pagingInfo, $tags);

      $tags["TOP_BOOKMARK"] = "<a name=\"top\"></a>";

      $rowContents = "";
      foreach ($pagedIds as $row) {
	$query = "SELECT label FROM mod_faq_questions WHERE id = ".$row['id'];
	$rowResult = $GLOBALS["core"]->getAll($query, TRUE);

	$subTags["QUESTION_CONTENTS"] = "<a style=\"color:blue;:visited {background: blue}\" href=\"#".$row['id']."\">";
	$subTags["QUESTION_CONTENTS"] .= $rowResult[0]["label"];
	$subTags["QUESTION_CONTENTS"] .= "</a>";

        $rowContents .= $GLOBALS["core"]->processTemplate($subTags, "faq", "userLists/bookmarkedQuesAns/topRow.tpl");
      }

      $tags["ALL_QUESTIONS"] = $rowContents;

      $rowContents = "";
      foreach ($pagedIds as $row) {
	$query = "SELECT label, answer FROM ".$GLOBALS["core"]->tbl_prefix."mod_faq_questions WHERE id = ".$row['id'];
	$rowResult = $GLOBALS["core"]->getAll($query);

	$subTags["QUESTION_CONTENTS"] = "<a name=\"".$row['id']."\">";
	$subTags["QUESTION_CONTENTS"] .= $rowResult[0]["label"];
	$subTags["ANSWER_CONTENTS"] = $rowResult[0]["answer"];

        if($this->_allowScoring) {
	  $address        = $GLOBALS["core"]->source_http."index.php";
	  $displayContent = $_SESSION["translate"]->it("feedback");
	  $get_var["module"] = "faq";
	  $get_var["FAQ_op"] = "view";
	  $get_var["FAQ_id"] = $row["id"];
	  $subTags["RATING_OPTION"] = PHPWS_Text::link($address, $displayContent, NULL, $get_var);
	  $subTags["RATING_OPTION"] = "[ ".$subTags["RATING_OPTION"]." ]";
	}

        $subTags["TOP_LINK"]  = "<a style=\"text-decoration:none;color:blue;:visited {background: blue}\"";
	$subTags["TOP_LINK"] .= "href=\"#top\">[top]</a>";

        $rowContents .= $GLOBALS["core"]->processTemplate($subTags, "faq", "userLists/bookmarkedQuesAns/bottomRow.tpl");
      }

      $tags["QUESTION_ANSWER"] = $rowContents;
    }
    else {
      $tags["NO_ITEMS"] = $_SESSION["translate"]->it("No FAQs to display");
    }

    $content = $GLOBALS["core"]->processTemplate($tags, "faq", "userLists/bookmarkedQuesAns/list.tpl");
    $GLOBALS["CNT_faq_body"]["content"] .= $content;
  } //END FUNC _noCategoriesBookmarkesQuesAnsView()


  function _categoriesView() {
    if(isset($_SESSION["OBJ_fatcat"])) {
     if(!isset($_REQUEST["FAQ_cat"])) {
      $allCatObj = $GLOBALS["core"]->query("SELECT cat_id, title, description, parent, children FROM ".$GLOBALS['core']->tbl_prefix."mod_fatcat_categories WHERE parent=0");
      $atLeastOneFAQ = FALSE;
      
      $templatedCategories = "";
      $mainSubCatTags = "";
      // LOOP THROUGH ALL CATEGORIES
      while ($currentCategory = $allCatObj->fetchRow()) {

	//cleanup
        $mainSubCatTags["SUB_CATEGORIES"] = NULL;
        $mainSubNoTop["SUB_CATEGORIES"] = NULL;
        $subCatTags["SUB_CATEGORY_TITLE"] = NULL;
        $subCatTags["SUB_CATEGORY_DESCRIPTION"] = NULL;
        $catTags["CATEGORY_TITLE"] = NULL;
        $catTags["C_NUMBER_OF_FAQS"] = NULL;
        $catTags["CATEGORY_DESCRIPTION"] = NULL;	 
	$topItem = FALSE;
	$childFAQs = FALSE;

	$sql = "SELECT COUNT(module_id) FROM mod_fatcat_elements WHERE module_title='faq' AND active=1 AND cat_id=".$currentCategory["cat_id"];
	$countElementsObj = $GLOBALS["core"]->query($sql, TRUE);
	$countElementsRow = $countElementsObj->fetchRow();

	
	  // BUILD TAGS IF THIS CATEGORY HAS CHILDREN
  	  if($currentCategory["children"] !== NULL) {
	    $childArr = explode(":", $currentCategory["children"]);
	    $childTestArray = array();

	   //see if any of these children are parents
	   foreach ($childArr as $child ) {
	    $childTestArray[] = $child;
	    $sql = "SELECT children FROM mod_fatcat_categories WHERE cat_id=".$child;
	    $currChildCatObj = $GLOBALS["core"]->query($sql, TRUE);
	    $currChildCatRow = $currChildCatObj->fetchRow();

	    if($currChildCatRow["children"]) {
	      $addToArray = explode(":", $currChildCatRow["children"]);
	    }

	  if(isset($addToArray)) {
	    foreach ($addToArray as $subCat) {
	      $childArr[] = $subCat;
	      $childTestArray[] = $subCat;
	    }

   	    $addToArray = NULL;  //keep from adding to other categories
	  }


	   }

	  foreach ($childTestArray as $child) {	    
	    $subCatTags["SUB_CATEGORY_TITLE"] = NULL;
	    $subCatTags["SUB_CATEGORY_DESCRIPTION"] = NULL;

	    if(strstr($currentCategory["children"], $child))
  	      $subCatTags["SPACING"] = "&nbsp;&nbsp;&nbsp;&nbsp;";
	    else 
  	      $subCatTags["SPACING"] .= "&nbsp;&nbsp;&nbsp;&nbsp;";

	    /* get a count of how many in this child category */
    	    $sql = "SELECT COUNT(module_id) FROM mod_fatcat_elements WHERE module_title='faq' AND active=1 AND cat_id=".$child;
	    $countChildElementsObj = $GLOBALS["core"]->query($sql, TRUE);
	    $countChildElementsRow = $countChildElementsObj->fetchRow();

            if($countChildElementsRow["COUNT(module_id)"]) {
	      $sql = "SELECT cat_id, title, description FROM ".$GLOBALS["core"]->tbl_prefix."mod_fatcat_categories WHERE cat_id=".$child;
	      $currChildCatObj = $GLOBALS["core"]->query($sql);
	      $currChildCatRow = $currChildCatObj->fetchRow();

	      $childFAQs = TRUE;
 	      $atLeastOneFAQ = TRUE;
  	      $subCatTags["SC_NUMBER_OF_FAQS"] = $countChildElementsRow["COUNT(module_id)"];

     	      $address        = $GLOBALS["core"]->source_http."index.php";
	      $displayContent = $currChildCatRow["title"];
	      $get_var["module"] = "faq";
	      $get_var["FAQ_op"] = "viewFAQs";
	      $get_var["FAQ_cat"] = $child;
	      $subCatTags["SUB_CATEGORY_TITLE"] = PHPWS_Text::link($address, $displayContent, NULL, $get_var);

	      if($currChildCatRow["description"]) {
  	        $subCatTags["SUB_CATEGORY_DESCRIPTION"] = $currChildCatRow["description"];
	      }
	      $mainSubCatTags["SUB_CATEGORIES"] .= $GLOBALS["core"]->processTemplate($subCatTags, "faq", "userLists/categories/main_sub_category.tpl"); 
	      $mainSubNoTop["SUB_CATEGORIES"] .= $GLOBALS["core"]->processTemplate($subCatTags, "faq", "userLists/categories/main_sub_category_no_parent.tpl"); 
	    }
	    } // END IF check no FAQs
	  }




	  if($atLeastOneFAQ || $countElementsRow["COUNT(module_id)"]) {
  	   // BUILD TAGS IF THIS IS A TOP LEVEL CATEGORY
	    //	   if($countElementsRow["COUNT(module_id)"]) {
    	     $atLeastOneFAQ = TRUE;
	     $topItem = TRUE;
	     $process = FALSE;

             if($countElementsRow["COUNT(module_id)"]) {
    	       $address        = $GLOBALS["core"]->source_http."index.php";
	       $displayContent = $currentCategory["title"];
	       $get_var["module"] = "faq";
	       $get_var["FAQ_op"] = "viewFAQs";
	       $get_var["FAQ_cat"] = $currentCategory["cat_id"];
               $catTags["CATEGORY_TITLE"] = PHPWS_Text::link($address, $displayContent, NULL, $get_var);

               $catTags["C_NUMBER_OF_FAQS"] = "(".$countElementsRow["COUNT(module_id)"].")";
	       $process = TRUE;
	     } else if($childFAQs) {
	       $catTags["CATEGORY_TITLE"] = $currentCategory["title"];
	       $process = TRUE;
	     }

	     if($process === TRUE) {
  	       if($currentCategory["description"]) {
		 $catTags["CATEGORY_DESCRIPTION"] = $currentCategory["description"];
	       }

               $templatedCategories .= $GLOBALS["core"]->processTemplate($catTags, "faq", "userLists/categories/main_single_category.tpl");
	     }
	     //	   }

	  }
      

	 if($topItem == TRUE && is_array($mainSubCatTags)) {
	   $templatedCategories .= $mainSubCatTags["SUB_CATEGORIES"];
	 }

	 if($topItem == FALSE && is_array($mainSubNoTop)) {
	   $templatedCategories .= $mainSubNoTop["SUB_CATEGORIES"];
	 }

      }

	// Either show categories or say no FAQ to show 
	if($atLeastOneFAQ) {
          $listTags["CATEGORY_LISTINGS"] = $templatedCategories;
          $content = $GLOBALS["core"]->processTemplate($listTags, "faq", "userLists/categories/main_cat_listings.tpl");
	  $GLOBALS["CNT_faq_body"]["content"] .= $content;
	} else {
	  $content = $_SESSION["translate"]->it("There are currently no FAQs to display.");
	  $GLOBALS["CNT_faq_body"]["content"] .= $content;
        }

     } else {
	// SHOW FAQS IN CATEGORY CHOOSEN
        $faqsInCategory = $_SESSION["OBJ_fatcat"]->getModuleElements("faq", NULL, $_REQUEST["FAQ_cat"]);

        if(!is_null($faqsInCategory)) {
          $query = "SELECT id, label, compScore FROM ".$GLOBALS["core"]->tbl_prefix."mod_faq_questions WHERE hidden=0 AND approved=1 AND (";
	  foreach ($faqsInCategory as $faq) {
	    if($faq["active"]) {
  	      $query .= "id=".$faq["module_id"]." OR ";
	    }
	  }
	  $query = substr($query, 0, strlen($query) - 4);
	  $query .= ") ORDER BY compScore";

	  $rowResult = $GLOBALS["core"]->getAll($query);

	  if(!is_null($rowResult)) {
  	    $_SESSION["OBJ_fatcat"]->initCategory($_REQUEST["FAQ_cat"]);        
	    $faqTitleTags[] = "";
	    $faqTitles = "";

	    foreach ($rowResult as $element) {
	      $address = $GLOBALS["core"]->source_http."index.php";
	      $get_var["module"] = "faq";
	      $get_var["FAQ_op"] = "view";
	      $get_var["FAQ_id"] = $element["id"];
	  
	      $faqTitleTags["FAQ_QUESTION"] = PHPWS_Text::link($address, $element["label"], NULL, $get_var);
              $faqTitles .= $GLOBALS["core"]->processTemplate($faqTitleTags, "faq", "userLists/categories/single_faq.tpl");
	    }
	  }

	  /* build navigation links */
	  /* parents first */
          $showFAQTags["CATEGORIES_LINK"] = "";
          $faqsInCategory = $_SESSION["OBJ_fatcat"]->getModuleElements("faq", NULL, $_REQUEST["FAQ_cat"]);

	  if($_SESSION["OBJ_fatcat"]->parent) {
            $faqsInCategory = $_SESSION["OBJ_fatcat"]->getModuleElements("faq", NULL, $_SESSION["OBJ_fatcat"]->parent);
      
            $numFAQs = 0;
	    if($faqsInCategory) {
  	      foreach ($faqsInCategory as $faq) {
	        if($faq["active"] == 1) 
		  $numFAQs++;
	      }
	    }

            if($numFAQs) {
  	      $showFAQTags["CATEGORIES_LINK"] = "Top: &#160;";
  	      $_SESSION["OBJ_fatcat"]->initCategory($_SESSION["OBJ_fatcat"]->parent);
	      $address = $GLOBALS["core"]->source_http."index.php";
	      $linkText = $_SESSION["OBJ_fatcat"]->title;
	      $get_var["module"] = "faq";
	      $get_var["FAQ_op"] = "viewFAQs";
	      $get_var["FAQ_cat"] = $_SESSION["OBJ_fatcat"]->cat_id;
	  
	      $showFAQTags["CATEGORIES_LINK"] .= PHPWS_Text::link($address, $linkText, NULL, $get_var);
              $showFAQTags["CATEGORIES_LINK"] .= " &#160;&#160;";
	      $showFAQTags["CATEGORIES_LINK"] .= " | &#160;&#160;&#160;";
	    }
	  }

	  /* now any children */
	  $buildChildContent = "";
          if(count($_SESSION["OBJ_fatcat"]->getChildren($_REQUEST["FAQ_cat"]))) {
	    $childArr = array_keys($_SESSION["OBJ_fatcat"]->getChildren($_REQUEST["FAQ_cat"]));

	    foreach ($childArr as $child) {
	      $numFAQsObj = $GLOBALS["core"]->query("SELECT COUNT(element_id) FROM mod_fatcat_elements WHERE module_title='faq' AND active=1 AND cat_id=".$child, TRUE);
                 
	      if(!DB::isError($numFAQsObj)) {
		$numInChildRow = $numFAQsObj->fetchRow();
		$numFAQs = $numInChildRow['COUNT(element_id)'];
	      }

              if($numFAQs) {
      	        $_SESSION["OBJ_fatcat"]->initCategory($child);
	        $address = $GLOBALS["core"]->source_http."index.php";
	        $linkText = $_SESSION["OBJ_fatcat"]->title;
	        $get_var["module"] = "faq";
	        $get_var["FAQ_op"] = "viewFAQs";
	        $get_var["FAQ_cat"] = $_SESSION["OBJ_fatcat"]->cat_id;
	  
	        $buildChildContent .= PHPWS_Text::link($address, $linkText, NULL, $get_var);
	        $buildChildContent .= " &#160;| &#160;&#160;";
	      }
	    }

            if($buildChildContent != "") { 
  	      $showFAQTags["CATEGORIES_LINK"] .= "Subcategories: &#160;";
	      $showFAQTags["CATEGORIES_LINK"] .= $buildChildContent;
	    }
	  }

	  /* back to listing link */
	  $showFAQTags["CATEGORIES_LINK"] .= $this->_faqLinkBack."Categories</a>";      
          $this->_faqLinkBack  = "<a style=\"{color:blue;}:visited {color:blue}\" href='index.php?module=faq&amp;FAQ_op=";
          $this->_faqLinkBack .= "viewFAQs&amp;FAQ_cat=".$_REQUEST["FAQ_cat"]."'>";

	  $_SESSION["OBJ_fatcat"]->initCategory($_REQUEST["FAQ_cat"]);
  	  $showFAQTags["CATEGORY_NAME"] = $_SESSION["OBJ_fatcat"]->title;
	  $showFAQTags["FAQ_LISTINGS"] = $faqTitles;
	  $faqTitles = "";

          $content = $GLOBALS["core"]->processTemplate($showFAQTags, "faq", "userLists/categories/cat_faq_listings.tpl");
	  $GLOBALS["CNT_faq_body"]["content"] .= $content;
        }
      
      }
    } else {
      /* fatcat not installed - how did you get to this view? (sneaky :)) */
      $this->_currentLayout = PHPWS_FAQ_NOCAT_CLICKQUES_VIEW;
      $this->_list();
    }
}

  function categoryContent() {

  }


  /**
   * shows listing of current viewable FAQs
   *
   * @access private
   */ 
  function _list() {
    if($this->_currentLayout == PHPWS_FAQ_NOCAT_QA_VIEW) {
      $_SESSION["SES_FAQ_STATS"]->setPagerLimit($this->_pagingLimit);
    } 

    /* determine what type of query and layout the user has choosen */
    if(isset($_REQUEST["PHPWS_MAN_PAGE"]) && $_REQUEST["PHPWS_MAN_PAGE"] == "unapproved") {
      $this->_viewUnapproved();
    } else if($this->_currentLayout == PHPWS_FAQ_NOCAT_CLICKQUES_VIEW) {
      $this->_noCategoriesQuesClickView();
    } else if($this->_currentLayout == PHPWS_FAQ_NOCAT_QA_VIEW) {

      if($this->_useBookmarks) {
       $this->_noCategoriesBookmarkesQuesAnsView();
      } else {
       $this->_noCategoriesQuesAnsView();
      }

    } else if($this->_currentLayout == PHPWS_FAQ_CAT_VIEW) {
      $this->_categoriesView();
    }
  } //END FUNC _list()


  /**
   * show current legend for scoring FAQs
   *
   * This function returns the current legend in either a form to edit the legend or 
   * as simply a string of text.
   *
   * @param text $mode The type format the legend should be returned in i.e. form, text
   * @return array
   * @access private
   */
  function _showLegend($mode = "form") {
    $legendElements[0] = NULL;
    $_counterForScores = 6;

    if($mode == "form" ) {

      foreach ($this->_scoringLegend as $description) {
        $_counterForScores--;

	$legendTags["SCORE_NUMBER"]      = $_counterForScores;
	$legendTags["SCORE_DESCRIPTION"] = PHPWS_Core::formTextField("score_text[]" , $description, 30); 

        $legendElements[0] .= $GLOBALS["core"]->processTemplate($legendTags, "faq", "scoreItems.tpl");
      }

      return $legendElements[0];
    }
    else if($mode == "text") {

      foreach ($this->_scoringLegend as $description) {
        $_counterForScores--;

	$content .= $_SESSION["translate"]->it($_counterForScores).")&nbsp;&nbsp;";
	$content .= $description; 
        $content .= "<br />";
      }

      return $content;
    }
  } //END FUNC _showLegend()


  /**
   * Allows an admistrator to change the settings of FAQ.
   *
   * @access public
   */
  function changeSettings() {
    $elements[0] = NULL;
    if(!isset($_REQUEST["FAQ_menu"])) {
      if($_SESSION["OBJ_user"]->allow_access("faq")) {
        $tags["OPTIONS_HEADER_LABEL"] = $_SESSION["translate"]->it("Options");

	/* link to add FAQ to a menu */
        if($GLOBALS["core"]->moduleExists("menuman")) {
          $linkText = $_SESSION["translate"]->it("Add a menu link for the FAQ module.");
          $get_var["FAQ_op"] = "viewSettings";
          $get_var["FAQ_menu"] = "yes";	  
	  $tags["ADD_TO_MENU_LABEL"] = $GLOBALS["core"]->moduleLink($linkText, "faq", $get_var);
	}

        $tags["ALLOW_ANON_CHECKBOX"]  = PHPWS_Core::formCheckBox("allowAnon", "1", $this->_allowScoring);
        $tags["ALLOW_ANON_LABEL"]     = $_SESSION["translate"]->it("Allow users to rate FAQs.");

        if($GLOBALS["core"]->moduleExists("comments")) {
          $tags["ALLOW_COMMENTS_CHECKBOX"] = PHPWS_Core::formCheckBox("allowComments", "1", $this->_allowComments);
          $tags["ALLOW_COMMENTS_LABEL"]    = $_SESSION["translate"]->it("Allow users to post comments.");
        }

        $tags["ALLOW_SUGGESTIONS_CHECKBOX"] = PHPWS_Core::formCheckBox("allowSuggestions", "1", $this->_allowSuggestions);
        $tags["ALLOW_SUGGESTIONS_LABEL"]    = $_SESSION["translate"]->it("Allow users to suggest FAQs.");

        $tags["LAYOUT_VIEW_HEADER_LABEL"]   = $_SESSION["translate"]->it("Layout View");

        $tags["BASIC_QA_LAYOUT_RADIO"] = PHPWS_Core::formRadio("layout_option", PHPWS_FAQ_NOCAT_QA_VIEW, $this->_currentLayout);
        $tags["BASIC_QA_LAYOUT_TITLE"] = $_SESSION["translate"]->it("Basic Question and Answer View");

        $tags["BASIC_QA_LAYOUT_PAGINGLIMIT"]  = $_SESSION["translate"]->it("Limit results by showing ");
        $tags["BASIC_QA_LAYOUT_PAGINGLIMIT"] .= PHPWS_Core::formTextField("paging_limit", $this->_pagingLimit, 3);
        $tags["BASIC_QA_LAYOUT_PAGINGLIMIT"] .= $_SESSION["translate"]->it(" questions per page.");

        $tags["BASIC_QA_NOBOOKMARKS_RADIO"]  = PHPWS_Core::formRadio("bookmark_option", 0, $this->_useBookmarks);
        $tags["BASIC_QA_NOBOOKMARKS_TITLE"]  = "&#160;&#160;".$_SESSION["translate"]->it("Question and Answer");
        $tags["BASIC_QA_NOBOOKMARKS_HELP"]   = $_SESSION["OBJ_help"]->show_link("faq", "basic_no_bookmarks_view");      
        $tags["BASIC_QA_USEBOOKMARKS_RADIO"] = PHPWS_Core::formRadio("bookmark_option", 1, $this->_useBookmarks);
        $tags["BASIC_QA_USEBOOKMARKS_TITLE"] = "&#160;&#160;".$_SESSION["translate"]->it("Bookmarked Questions");
        $tags["BASIC_QA_USEBOOKMARKS_HELP"]  =  $_SESSION["OBJ_help"]->show_link("faq", "basic_bookmarks_view");

        $tags["LISTING_LAYOUT_RADIO"] = PHPWS_Core::formRadio("layout_option", PHPWS_FAQ_NOCAT_CLICKQUES_VIEW, $this->_currentLayout);
        $tags["LISTING_LAYOUT_TITLE"] = $_SESSION["translate"]->it("No Categories - Hyperlinked Questions");
        $tags["LISTING_LAYOUT_HELP"]  = $_SESSION["OBJ_help"]->show_link("faq", "nocat_clickable_view");

	if(isset($_SESSION["OBJ_fatcat"])) {
          $tags["CATEGORY_LAYOUT_RADIO"] = PHPWS_Core::formRadio("layout_option", PHPWS_FAQ_CAT_VIEW, $this->_currentLayout);
          $tags["CATEGORY_LAYOUT_TITLE"] = $_SESSION["translate"]->it("Category View");
          $tags["CATEGORY_LAYOUT_HELP"]  = $_SESSION["OBJ_help"]->show_link("faq", "category_view");
	}

        $tags["LEGEND_TITLE"] = $_SESSION["translate"]->it("Legend for Rating FAQs");

        $tags["SUBMIT_BUTTON"]    = PHPWS_Core::formSubmit($_SESSION["translate"]->it("Save Settings"));
        $tags["SCORE_LABEL_LIST"] = $this->_showLegend("form");
	$tags["TITLE"] = $_SESSION["translate"]->it("Settings");

	$elements[0] .= PHPWS_Core::formHidden("FAQ_op", "savesettings");
        $elements[0] .= PHPWS_Core::formHidden("module", "faq");
        $elements[0] .= $GLOBALS["core"]->processTemplate($tags, "faq", "settings.tpl");

        $content = PHPWS_Core::makeform("change_settings", "index.php", $elements);
	$GLOBALS["CNT_faq_body"]["content"] .= $content;
      }
      else {
        $content  = $_SESSION["translate"]->it("You are not authorized to perform this action.");
        $errorobj = new PHPWS_Error("darren_notes", "PHPWS_FAQ_MANAGER::_edit()", $content);
        $errorobj->errorMessage("CNT_faq_body");
      }
    } else {
      if($GLOBALS['core']->moduleExists("menuman")) {
        $op_string = "&amp;FAQ_op=viewFAQs";
        $call_back = "./index.php?module=faq&amp;FAQ_op=viewSettings";
        $item_active = 1;
        $_SESSION['OBJ_menuman']->add_module_item("faq", $op_string, $call_back, $item_active);
      }
    }
  } //END FUNC changeSettings()


  /**
   * saves any changes made to the settings for FAQ.
   *
   * @access public
   */
  function saveSettings() {
    if($_SESSION["OBJ_user"]->allow_access("faq")) {

      if(isset($_REQUEST["allowAnon"])) {
        $queryData["anon"] = 1;
      }
      else {
        $queryData["anon"] = 0;
      }

      if(isset($_REQUEST["allowComments"])) {
        $queryData["comments"] = 1;
      } else {
	$queryData["comments"] = 0;
      }

      if(isset($_REQUEST["allowSuggestions"])) {
        $queryData["suggestions"] = 1;
      } else {
	$queryData["suggestions"] = 0;
      }

      if(isset($_REQUEST["score_text"])) {
        $counter = 0;
        foreach ($_REQUEST["score_text"] as $legendDescription) {
	  $legendArr[$counter] = $legendDescription;
          $counter++;
        }
      }

      $queryData["use_bookmarks"] = $_REQUEST["bookmark_option"];
      $queryData["layout_view"] = $_REQUEST["layout_option"];
      $queryData["score_text"]  = serialize($legendArr);

      if(!is_numeric($_REQUEST["paging_limit"]) || $_REQUEST["paging_limit"] <= 0) {
         $content = $_SESSION["translate"]->it("The paging limit must be a number and greater than zero.");
         $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_saveSettings()", $content);
      } else {
        $queryData["paging_limit"] = $GLOBALS["core"]->parseInput($_REQUEST["paging_limit"]);
      }

      if(!isset($errorobj) || !PHPWS_Error::isError($errorobj)) {
        $result = $GLOBALS["core"]->sqlUpdate($queryData, "mod_faq_settings");

        if($result) { 
          $row = $GLOBALS["core"]->sqlSelect("mod_faq_settings");

          $this->_allowScoring = $row[0]["anon"];
	  $this->_allowComments = $row[0]["comments"];
	  $this->_allowSuggestions = $row[0]["suggestions"];
          $this->_scoringLegend = unserialize($row[0]["score_text"]);

	  $this->_currentLayout = $row[0]["layout_view"];
          if($_SESSION["SES_FAQ_STATS"]) {
            if($this->_currentLayout == 2) {
	      $_SESSION["SES_FAQ_STATS"]->turnPagingOff();
            } else {
	      $_SESSION["SES_FAQ_STATS"]->turnPagingOn();
	    }
          }

	  $this->_useBookmarks = $row[0]["use_bookmarks"];
	  $this->_pagingLimit = $row[0]["paging_limit"];

          $title = "Changed Settings";
          $content = "The changes you requested for the FAQ module have been successfully saved.";

	  $GLOBALS["CNT_faq_body"]["content"] .= $content;
          $this->changeSettings();

        } else {

          $content = $_SESSION["translate"]->it("There was a problem saving your settings.");
          $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_edit()", $content);

        } //END CHECK TO SEE IF SAVED TO DATABASE
      } 
     } else {
       $content = $_SESSION["translate"]->it("You are not authorized to perform this action.");
       $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_saveSettings()", $content);
     } 

    /* Show any errors */
    if(isset($errorobj) && PHPWS_Error::isError($errorobj)) {
       $errorobj->errorMessage("CNT_faq_body");
       $this->changeSettings();
    }
  } //END FUNC saveSettings()


  /**
   * diplays form to contact a user that has suggested a faq
   *
   * @access public
   */
  function contactUser($email, $recipient, $dontSend = FALSE) {
    if(!isset($_REQUEST["send_now"]) || $dontSend) {
      $tags["RECIPIENT_LABEL"] = "<b>".$_SESSION["translate"]->it("To:")."</b>";
      $tags["RECIPIENT_FIELD"] = "$recipient &#160;&#160; &lt;<a href=mailto::$email>$email</a>&gt;";

      $tags["SUBJECT_LABEL"] = "<b>".$_SESSION["translate"]->it("Subject:")."</b>";
      $defaultSubject = "Question Regarding:  ".$this->_currentFAQ->_label;
      $tags["SUBJECT_FIELD"] = PHPWS_Core::formTextField("subject", $defaultSubject, 60);
 
      $tags["BODY_LABEL"] = "<b>".$_SESSION["translate"]->it("Message:")."</b>";
      $tags["BODY_FIELD"] = PHPWS_Core::formTextArea("body", $_REQUEST["body"], 15, 50);
 
      $tags["CANCEL_BUTTON"] = PHPWS_Core::formSubmit("Cancel", "FAQ_op");

      $tags["SEND_EMAIL_BUTTON"] = PHPWS_Core::formSubmit("Send Email", "FAQ_op");
      $tags["TITLE"] = $_SESSION["translate"]->it("Contact User");

      $elements[0] = PHPWS_Core::formHidden("FAQ_id", $this->_currentFAQ->_id);
      $elements[0] .= PHPWS_Core::formHidden("FAQ_email", $email);
      $elements[0] .= PHPWS_Core::formHidden("send_now", "true");
      $elements[0] .= PHPWS_Core::formHidden("FAQ_name", $recipient);
      $elements[0] .= PHPWS_Core::formHidden("module", "faq");
      $elements[0] .= $GLOBALS["core"]->processTemplate($tags, "faq", "email.tpl");
      $content = PHPWS_Core::makeform("faq_email_user", "index.php", $elements);

      $GLOBALS["CNT_faq_body"]["content"] .= $content;
    } else {
       if($_REQUEST["body"] == "") {
        $content = $_SESSION["translate"]->it("The body of the message may not be left blank.");
        $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_contactUser()", $content);
        $errorobj->errorMessage("CNT_faq_body");

	$this->contactUser($_REQUEST["FAQ_email"], $_REQUEST["FAQ_name"], TRUE);
	return;
       }

       if($_REQUEST["FAQ_email"]) {
         $to = $GLOBALS["core"]->parseInput($_REQUEST["FAQ_email"]);
       }

       if($_REQUEST["subject"]) {
         $subject = $GLOBALS["core"]->parseInput($_REQUEST["subject"]);
       }

       if($_REQUEST["body"]) {
         $message = $GLOBALS["core"]->parseInput($_REQUEST["body"]);
       }

       $headers = "From:  Admin-".$_SESSION["OBJ_user"]->username." <".$_SESSION["OBJ_user"]->email.">\r\n";
       $headers .= "Reply-To: ".$_SESSION["OBJ_user"]->email;
       $result = mail($to, $subject, $message, $headers);
       if(!$result) {
         $content  = $_SESSION["translate"]->it("Error Sending Email. Try Again");
         $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_contactUser()", $content);
         $errorobj->errorMessage("CNT_faq_body");
	 $this->contactUser($_REQUEST["FAQ_email"], $_REQUEST["FAQ_name"], TRUE);
       } else {
	 $content  = $_SESSION["translate"]->it("The email to ");
	 $content .= $_REQUEST["FAQ_name"];
         $content .= $_SESSION["translate"]->it(" has been sent.")."<br />";
	 $content .= "<br /><a href=\"index.php?module=faq&amp;FAQ_op=view&amp;FAQ_id=".$this->_currentFAQ->_id."\">";
	 $content .= $_SESSION["translate"]->it("Return to suggested FAQ")."</a>";

         $GLOBALS["CNT_faq_body"]["content"] .= $content;
       }
    }
  }

  /*
   * overides function in Manager class to allow the ability to set fatcat category active state
   * 
   * @param text $column The name of the column to update
   * @param mixed $value The value to set the column to
   * @return boolean TRUE on success and FALSE on failure
   * @access private
   */
  function _doMassUpdate($column, $value) {
    $errorObj = NULL;

    if(is_array($_REQUEST["PHPWS_MAN_ITEMS"]) && sizeof($_REQUEST["PHPWS_MAN_ITEMS"]) > 0) {
      // Begin sql update statement 
      $sql = "UPDATE " . $GLOBALS["core"]->tbl_prefix . $this->_table ." SET $column='$value' ";
   
      $isApproved = 0;
      $isHidden = 0;

      foreach($_REQUEST["PHPWS_MAN_ITEMS"] as $itemId) {
 	$isApproved = $GLOBALS["core"]->getOne("SELECT approved FROM mod_faq_questions WHERE id=".$itemId, TRUE);
        $isHidden = $GLOBALS["core"]->getOne("SELECT hidden FROM mod_faq_questions WHERE id=".$itemId, TRUE);

        switch($column) {
         case "hidden":
           if($_SESSION["OBJ_fatcat"]) {
             if($value) { 
	       // FAQ is hidden 
  	      $_SESSION["OBJ_fatcat"]->deactivate($itemId);          
	     }
             else if($isApproved) {
               // FAQ is not visible and is approved 
	       $_SESSION["OBJ_fatcat"]->activate($itemId);
	     }
	   }
         break;
	case "approved":
          /* add a check here to make sure that an answer field has been provided */
   	  $answer = $GLOBALS["core"]->getOne("SELECT answer FROM mod_faq_questions WHERE id=".$itemId, TRUE);

	  if(strlen($answer) <= 1) {
            $error  = $_SESSION["translate"]->it("Every FAQ needs to have an answer before being approved.");
	    $error .= "<br />";
            $error .= $_SESSION["translate"]->it("Please choose the option to edit this FAQ and add an answer.");
	    $errorobj = new PHPWS_Error("faq", "PHPWS_FAQ_MANAGER::_edit()", $error);
	    $errorobj->errorMessage("CNT_faq_body");
            return FALSE;
	  }

          PHPWS_Approval::remove($itemId, "faq");

	  if($_SESSION["OBJ_fatcat"]) {
	    if(!$isHidden) {
              $_SESSION["OBJ_fatcat"]->activate($itemId);
	      $sql .= ", contact=NULL";
	    }
	  }
	}
      }

      // change ownership of FAQ
      $sql .= ", owner='".$_SESSION["OBJ_user"]->username."'";
      $sql .= ", editor='".$_SESSION["OBJ_user"]->username."'";

      $sql .= " WHERE id='";

      // Set flag to know when to add sql for checking against extra ids 
      $flag = FALSE;
      foreach($_REQUEST["PHPWS_MAN_ITEMS"] as $itemId) {
	if($flag)
	  $sql .= " OR id='";

	$sql .= $itemId . "'";
	$flag = TRUE;
      }

      // Execute query and test for failure 
      $result = $GLOBALS["core"]->query($sql);
      if($result) {
        $_SESSION["SES_FAQ_STATS"]->init();
	return TRUE;
      }
      else {
	return FALSE;
      }
    }

    $_SESSION["SES_FAQ_STATS"]->init();
  }// END FUNC _doMassUpdate()

  /**
   * returns flag to indicate if users can score FAQs
   *
   * @access public
   */
  function isAnonScoringAllowed() {
    return $this->_allowScoring;
  }

  /**
   * returns flag to indicate if users can post comments
   *
   * @access public
   */
  function isCommentsAllowed() {
    return $this->_allowComments;
  }

  /**
   * sets the link to get back to a listing
   *
   * format should be '<a href=\"index.php?module=faq&amp;FAQ_op=view>'
   *
   * @access public
   */
  function setFaqLinkBack($newLinkBack) {
    $this->_faqLinkBack = $newLinkBack;
  }

  /**
   * returns the link to get back to the last listing
   *
   * @access public
   */
  function getLinkBack() {
    return $this->_faqLinkBack;
  }

  /**
   * used by approval module to delete suggested FAQs
   *
   */
  function approvalRefuse($id) {
    $GLOBALS["core"]->sqlDelete("mod_faq_questions", "id", $id);
    PHPWS_Fatcat::purge($id, "faq");
    $_SESSION["SES_FAQ_STATS"]->init();
  }

  /**
   * used by approval module to approve suggested FAQs
   *
   */
  function approvalApprove($id) {
    $data["approved"] = 1;
    $GLOBALS["core"]->sqlUpdate($data, "mod_faq_questions", "id", $id);
    PHPWS_Fatcat::activate($id, "faq");
    $_SESSION["SES_FAQ_STATS"]->init();
  }

  /**
   * handles user interaction with FAQ Manager
   *
   * @access public
   */
  function action() {
    $this->menu();

    // an admin option was selected inside the view of a FAQ so returns
    // control to PHPWS_Faq
    if(isset($_REQUEST["FAQ_adv"]) && !isset($_REQUEST["FAQ_op"]) || isset($this->_currentFAQ)) {
      // checked added for specific layouts
      if(!is_object($this->_currentFAQ) && isset($_REQUEST["FAQ_id"])) {
	$this->_currentFAQ = new PHPWS_Faq($_REQUEST["FAQ_op"]);
      }
      $this->_currentFAQ->action();
      $_SESSION["SES_FAQ_STATS"]->init();
    }

    /* Show view user requested */
    if(isset($_REQUEST["FAQ_op"])) {
      /* if not administor or deity then default to show all FAQs */
      if(!$_SESSION["OBJ_user"]->admin_switch && !$_SESSION["OBJ_user"]->isDeity() && 
	 $_REQUEST["FAQ_op"] != "suggestFAQ" && 
	 !isset($_REQUEST["submitRatedFAQ"]) &&
	 $_REQUEST["FAQ_op"] != "viewFAQs" && 
	 $_REQUEST["FAQ_op"] != "view" && 
	 $_REQUEST["FAQ_op"] != "submitSuggestedFAQ") {
        $this->_faqLinkBack = "<a style=\"{color:blue;}:visited {color:blue}\" href='index.php?module=faq&amp;FAQ_op=";
	$this->_faqLinkBack .= $_SESSION["translate"]->it("View FAQs")."'>";
	$this->_list();
      } 

      /* Check for submit buttons */
      // User choose to score FAQ
      if(isset($_REQUEST["submitRatedFAQ"])) {
	$this->_scoreCurrentFAQ();
      } 
      
      switch ($_REQUEST["FAQ_op"]) { 
      /* User actions */
      case "view":
      case "Cancel":
        $id = array($_REQUEST["FAQ_id"]);
        $this->_view($id);
        break;
      case "suggestFAQ":
        $this->_currentFAQ = new PHPWS_Faq();
        $this->_currentFAQ->edit();
        break;
      case "submitSuggestedFAQ":
        $this->_currentFAQ->submitFAQ();
        $_SESSION["SES_FAQ_STATS"]->init();
        break;
      case "viewFAQs":
        $this->_faqLinkBack  = "<a style=\"{color:blue;}:visited {color:blue}\" href='index.php?module=faq&amp;FAQ_op=";
        $this->_faqLinkBack .= "viewFAQs'>";
	$this->_list();
        break;
       //END USER ACTIONS

      /* More Admin options */
      case "viewUnapprovedHidden":
        $this->_faqLinkBack  = "<a style=\"{color:blue;}:visited {color:blue}\" href='index.php?module=faq&amp;FAQ_op=";
        $this->_faqLinkBack .= "viewUnapprovedHidden'>";
	$this->_viewUnapproved();
      break;

      case "viewStats":
        $_SESSION["SES_FAQ_STATS"]->menuOptions();
      break;

      case "newFAQ":
	$this->_edit();
      break;

      case "submitNewFAQ":
      case "updateFAQ":
        $this->_currentFAQ->submitFAQ();
        $_SESSION["SES_FAQ_STATS"]->init();
      break;

      case "viewSettings":
	$this->changeSettings();
      break;

      case "savesettings":
	$this->saveSettings();
      break;

      case "email_user":
      case "Send Email":
	$this->contactUser($_REQUEST["FAQ_email"], $_REQUEST["FAQ_name"]);
      break;
      /* END ADMIN ACTIONS */
      }//END SWITCH STATEMENT

    } else {
       if(isset($_REQUEST["FAQ_Stats_op"])) {
         $_SESSION["SES_FAQ_STATS"]->action();
       }
    } //END FAQ_op
  }


  /** 
   * A operation on a FAQ listing was called in FAQ_Stats and since
   * all of manager's listing functions are centralized in this class
   * this function acts as a redirect back to the last query executed in
   * the FAQ_Stats class.
   */
  function bounceToStatsList() {
    $_SESSION["SES_FAQ_STATS"]->showLastQuery();
  }

  function search($where) {
    $sql = "SELECT id, label FROM " . $GLOBALS['core']->tbl_prefix . "mod_faq_questions ";
    $sql .= $where . " AND hidden='0' AND approved = '1'";

    $faqResult = $GLOBALS['core']->query($sql);

    if($faqResult->numrows()) {

      while($faq = $faqResult->fetchrow(DB_FETCHMODE_ASSOC)) {
	$results[$faq['id']] = $faq['label'];
      }

      return $results;
    } else {
      return FALSE;
    }
  }


}

?>