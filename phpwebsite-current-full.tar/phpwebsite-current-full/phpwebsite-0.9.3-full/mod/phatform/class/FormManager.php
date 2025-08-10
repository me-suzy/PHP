<?php

/**
 * Form Manager Class
 *
 * @version $Id: FormManager.php,v 1.47 2003/06/27 15:23:32 adam Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package Phat Form
 */
class PHAT_FormManager extends PHPWS_Manager {

  /**
   * The PHAT_Form object of the current form the user is insterested in.
   *
   * @var     object
   * @example $this->form = new PHAT_Form(6);
   * @access  public
   */
  var $form = NULL;

  /**
   * The constructor for the PHAT_FormManager
   *
   * @access public
   */
  function PHAT_FormManager() {
    $this->setModule("phatform");
    $this->setRequest("PHAT_MAN_OP");
    $this->setTable("mod_phatform_forms");
  }// END FUNC PHAT_FormManager

  /**
   * Returns the templated menu for this form manager.
   *
   * @return string The templated menu ready for display.
   * @access private
   */
  function menu() {
    $menuTags = array();
    $menuTags["NEW_FORM_HREF"] = "index.php?module=phatform&PHAT_MAN_OP=NewForm";
    $menuTags["NEW_FORM_LABEL"] = $_SESSION["translate"]->it("New Form");

    $menuTags["LIST_FORMS_HREF"] = "index.php?module=phatform&PHAT_MAN_OP=List";
    $menuTags["LIST_FORMS_LABEL"] = $_SESSION["translate"]->it("List Forms");

    if(isset($this->form)) {
      if($this->form->isSaved() && $_SESSION["OBJ_user"]->allow_access("phatform", "report_view")) {
	$menuTags["REPORT_HREF"] = "index.php?module=phatform&PHAT_FORM_OP=report";
	$menuTags["REPORT_LABEL"] = $_SESSION["translate"]->it("Report");
      }

      if($this->form->isSaved() && $_SESSION["OBJ_user"]->allow_access("phatform", "archive_form")) {
	$menuTags["ARCHIVE_HREF"] = "index.php?module=phatform&PHAT_FORM_OP=archive";
	$menuTags["ARCHIVE_LABEL"] = $_SESSION["translate"]->it("Archive");
      }


      $id = $this->form->getId();
      if(isset($id)) {
	if($_SESSION["OBJ_user"]->allow_access("phatform", "edit_forms")) {
	  $menuTags["SETTINGS_HREF"] = "index.php?module=phatform&PHAT_FORM_OP=editSettings";
	  $menuTags["SETTINGS_LABEL"] = $_SESSION["translate"]->it("Settings");
	}
	
	if(!$this->form->isSaved() && $_SESSION["OBJ_user"]->allow_access("phatform", "edit_forms")) {
	  $menuTags["ELEMENTS_HREF"] = "index.php?module=phatform&PHAT_FORM_OP=editElements";
	  $menuTags["ELEMENTS_LABEL"] = $_SESSION["translate"]->it("Elements");
	}
      }
    }

    return $GLOBALS["core"]->processTemplate($menuTags, "phatform", "manager/menu.tpl");
  }// END FUNC _menu

  /**
   * Required function for PHPWS_Manager
   *
   * This function is called via the PHPWS_Manager list.  It loads the form
   * $ids[0] from the database and enters edit mode for that form.
   *
   * @param  array $ids The ids of the items that were selected from the PHPWS_Manager list.
   * @access private
   */
  function _edit($ids) {
    $this->form = new PHAT_Form($ids[0]);

    /* Make sure this user has access to edit the current form */
    if($_SESSION["OBJ_user"]->isDeity() || $_SESSION["OBJ_user"]->allow_access("phatform", "edit_forms")) {

      /* Make sure this user owns and can edit the selected form */
      if(!$_SESSION["OBJ_user"]->isDeity() && 
	 $_SESSION["OBJ_user"]->allow_access("phatform", "user_forms_only") &&
	 $this->form->getOwner() != $_SESSION["OBJ_user"]->username) {
	$this->_accessDenied();
      }

      $content = $this->menu();
      if($this->form->isSaved())
	$content .= $this->form->editSettings();
      else
	$content .= $this->form->view(TRUE);
    } else {
      $this->_accessDenied();
    }
    $GLOBALS["CNT_phatform"]["content"] .= $content;
  }// END FUNC _edit

  /**
   * Required function for PHPWS_Manager
   *
   * This function is called via the PHPWS_Manager list.  It loads the form
   * $ids[0] from the database and enters view mode for that form.
   *
   * @param  array $ids The ids of the items that were selected from the PHPWS_Manager list.
   * @access private
   */
  function _view($ids) {
    $this->form = new PHAT_Form($ids[0]);
    if($_SESSION["OBJ_user"]->allow_access("phatform")) {
      $content = $_SESSION["PHAT_FormManager"]->menu() . $this->form->view();
    } else {
      $content = $this->form->view();
    }
    $GLOBALS["CNT_phatform"]["content"] .= $content;
  }// END FUNC _view

  /**
   * Required function for PHPWS_Manager
   *
   * This function is called via the PHPWS_Manager. It is used to allow the Manager
   * to send itself to it's list.
   *
   * @access private
   */
  function _list() {
    $this->init();
    /* Check if user has admin access to phatform */
    if($_SESSION["OBJ_user"]->allow_access("phatform")) {
      unset($this->form);

      /* Show menu to admin user */
      $content = $this->menu();

      /* Always show the saved/approved forms to the admin user */
      /* Make sure user isn't a deity and then see if they can only edit their forms */
      $content .= $this->getList("saved", $_SESSION["translate"]->it("Saved Forms"));

      /* If a user can edit forms, show the unsaved ones */
      if($_SESSION["OBJ_user"]->allow_access("phatform", "edit_forms")) {
	$content .= "<hr />" . $this->getList("unsaved", $_SESSION["translate"]->it("Unsaved Forms"));
      }

      /* Grab unapproved forms */
      if($_SESSION["OBJ_user"]->allow_access("phatform", "approve_forms")) {
      	$content .= "<hr />" . $this->getList("unapproved", $_SESSION["translate"]->it("Unapproved Forms"));
      }
    } else {
      /* Not an admin user so only show them a list of available forms */
      $content .= $this->getList("user", $_SESSION["translate"]->it("Forms"));
    }

    $GLOBALS["CNT_phatform"]["content"] .= $content;
  }// END FUNC _list

  /**
   * Displays a confirmation for deletion of forms and carries out the delete.
   *
   * @param  array $ids An array of form ids of forms to be deleted.
   * @access private
   */
  function _delete($ids) {
    if(isset($_REQUEST["yes"])) {
      foreach($ids as $id) {
	$this->form = new PHAT_Form($id);
	$this->form->delete();
	unset($this->form);
      }
    } elseif(isset($_REQUEST["no"])) {
      $this->_list();
      return;
    } else {
      $content = $this->_confirmDelete($ids);
      $GLOBALS["CNT_phatform"]["content"] .= $content;
    }
  }

  /**
   * Returns the templated confirmation for deleting multiple items from a list.
   *
   * @param array   $ids     An array of item ids to be deleted.
   * @return string The templated confirmation message.
   * @access private
   */
  function _confirmDelete($ids) {
    /* Make sure an array of ids was recived before asking to confirm */
    if(!is_array($ids)) {
      $this->_list();
      return;
    }

    $elements = array();
    $elements[0] = "";
    $confirmTags = array();
    $confirmTags['ITEMS'] = ""; 
    
    $confirmTags["MESSAGE"] = $_SESSION["translate"]->it("Are you sure you wish to delete the following forms?  All data associated with these forms will be lost!");
    $confirmTags["YES_BUTTON"] = PHPWS_Form::formSubmit("Yes", "yes");
    $confirmTags["NO_BUTTON"] = PHPWS_Form::formSubmit("No", "no");

    /* Step through ids and grab the names of each form */
    foreach($ids as $key=>$id) {
      $temp = new PHAT_Form($id);
      $confirmTags["ITEMS"] .= $key+1 . ". " . $temp->getLabel() . "<br />";
      $elements[0] .= PHPWS_Form::formHidden("PHPWS_MAN_ITEMS[]", $id);
    }

    /* Finish creating elements array for form */
    $elements[0] .= PHPWS_Form::formHidden("module", $this->_module);
    $elements[0] .= PHPWS_Form::formHidden("PHAT_MAN_OP", "delete");
    $elements[0] .= $GLOBALS["core"]->processTemplate($confirmTags, "phatform", "manager/confirm.tpl");

    return PHPWS_Form::makeForm("PHPWS_MAN_Deletion", "index.php", $elements);
  } // END FUNC _confirmDelete()

  /**
   * Prints an access denied message and exits the script.
   *
   * @access private
   */
  function _accessDenied() {
    $error = new PHPWS_Error("phatform",
			     "PHAT_FormManager::action",
			     $_SESSION["translate"]->it("ACCESS DENIED!"),
			     "exit",
			     PHAT_DEBUG_MODE);
    $error->errorMessage();
  }

  /**
   * The action function for PHAT_FormManager
   *
   * This function checks the PHAT_MAN_OP...hehe I said phat man :)  Based on that
   * variable, action calls the required functions to complete the operation.
   *
   * @access private
   */
  function action() {
    $action = FALSE;
    switch($_REQUEST['PHAT_MAN_OP']) {
      case "View":
      if(isset($_REQUEST["PHAT_FORM_ID"])) {
	$this->form = new PHAT_Form($_REQUEST["PHAT_FORM_ID"]);
	if($this->form->isSaved() && !$this->form->isHidden() && $this->form->isApproved())
	  $content = $this->form->view();
	else
	  $content = $_SESSION["translate"]->it("This form is not available for viewing at this time.");
      } 
      $action = TRUE;
      break;

      case "List":
      $this->_list();
      return;
      break;

      case "NewForm":
      if($_SESSION["OBJ_user"]->allow_access("phatform", "edit_forms")) {
	$this->form = new PHAT_Form;
	$content = $this->menu();
	$content .= $this->form->editSettings();
      } else {
	$this->_accessDenied();
      }
      $action = TRUE;
      break;
      
    }// END PHAT_MAN_OP SWITCH
    

    if($action) {
      $GLOBALS["CNT_phatform"]["content"] .= $content;
    }
  }// END FUNC action

}// END CLASS PHAT_FormManager

?>