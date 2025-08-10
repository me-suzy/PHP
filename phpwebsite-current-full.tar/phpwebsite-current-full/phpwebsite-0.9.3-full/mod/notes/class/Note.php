<?php
/**
 * This class holds all information for a single instance of a Note.
 *
 * @version $Id: Note.php,v 1.7 2003/07/01 15:20:01 adam Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package Notes
 */
class PHPWS_Note {

  /**
   * The database id of this note.
   *
   * @var    integer
   * @access private
   */
  var $_id = NULL;

  /**
   * The username of the user to deliver this Note to.
   *
   * @var    string
   * @access private
   */
  var $_toUser = NULL;

  /**
   * The group name of the group to deliver this Note to.
   *
   * @var    string
   * @access private
   */
  var $_toGroup = NULL;

  /**
   * The username of the user who is sending this Note.
   *
   * @var    string
   * @access private
   */
  var $_fromUser = NULL;

  /**
   * The actual message body of the note.
   *
   * @var    string
   * @access private
   */
  var $_message = NULL;

  /**
   * The date this note was actually sent.
   *
   * @var    string
   * @access private
   */
  var $_dateSent = NULL;

  /**
   * The date this note was read by recipient. For a single user note,
   * this is the actual date read.  For a multiuser note, this is the
   * date this note was last read.
   *
   * @var    string
   * @access private
   */
  var $_dateRead = NULL;

  /**
   * The username of the user to last read this note. If this is a single
   * user note it will be the username of the user to recieve the note. In
   * the case of a multiuser note, this will be the username of the last
   * user to read this note.
   *
   * @var    string
   * @access private
   */
  var $_userRead = NULL;

  /**
   * Constructor for the PHPWS_Note class.
   *
   * @param  integer $NOTE_id The database id of the note to be constructed.
   *                          If NULL, a new note is constructed.
   * @access public
   */
  function PHPWS_Note($NOTE_id = NULL) {
    /* If this is a new note, simply set the from User */
    if($NOTE_id === NULL) {
      $this->_fromUser = $_SESSION["OBJ_user"]->username;
    } else {
      $result = $GLOBALS["core"]->sqlSelect("mod_notes", "id", $NOTE_id);
      if($result) {
	if($_SESSION["OBJ_user"]->username == $result[0]["toUser"]) {
	  $this->_id = $NOTE_id;
	  $this->_toUser = $result[0]["toUser"];
	  $this->_toGroup = $result[0]["toGroup"];
	  $this->_fromUser = $result[0]["fromUser"];
	  $this->_message = $result[0]["message"];
	  $this->_dateSent = $result[0]["dateSent"];
	  $this->_dateRead = $result[0]["dateRead"];
	  $this->_userRead = $result[0]["userRead"];
	}
      }
    }
  }// END FUNC PHPWS_Note()

  /**
   * Displays this note for reading
   *
   * @access public
   * @see    _mark()
   */
  function read() {
    /* Check to make sure this user has access to read this note */
    if($this->_toUser == $_SESSION["OBJ_user"]->username) {
      /* Mark this note as read by the current user on the current date/time */
      $this->_mark();

      /* Check whether this is a group note or a single user note */
      if($this->_toGroup) {
	$tags["TO_GROUP_LABEL"] = $_SESSION["translate"]->it("Group");
	$tags["TO_GROUP"] = $this->_toGroup;
      } elseif ($this->_toUser) {
	$tags["TO_USER_LABEL"] = $_SESSION["translate"]->it("To");
	$tags["TO_USER"] = $this->_toUser;
      } else {
	/* Message did not contain a recipient, so print error and do not display note */
	$this->_error("bad_message");
      }

      $tags["FROM_USER_LABEL"] = $_SESSION["translate"]->it("From");
      $tags["FROM_USER"] = $this->_fromUser;
      $tags["DATE_SENT_LABEL"] = $_SESSION["translate"]->it("Sent");
      $tags["DATE_SENT"] = $this->_dateSent;
      $tags["MESSAGE_BODY"] = $this->_message;

      $content = $GLOBALS["core"]->processTemplate($tags, "notes", "read.tpl");
    } else {
      /* The current user does not have access to read this note */
      $this->_error("unauthorized");
      return;
    }

    /* Display full note */
    $title = "<h3>" . $_SESSION["translate"]->it("Note from") . " " . $this->_fromUser . "</h3>";
    $GLOBALS["CNT_notes"]["content"] .= $content;
  }// END FUNC read()

  /**
   * Displays this note in a format to be edited.
   *
   * @access public
   * @see    _getUsers(), _getGroups()
   */
  function edit() {
    /* Get list of users and groups */
    $users = $this->_getUsers();
    $groups = $this->_getGroups();

    /* Prepare tags array for template */
    $tags = array();
    $tags["TITLE"] = $_SESSION["translate"]->it("Send Note");
    $tags["TO_USER"] = PHPWS_Form::formSelect("toUser", $users, $this->_toUser, TRUE);
    //$tags["TO_GROUP"] = PHPWS_Form::formSelect("toGroup", $groups, $this->_toGroup, TRUE);
    $tags["MESSAGE_FIELD"] = PHPWS_Form::formTextArea("message", $this->_message, 7, 50);
    $tags["SUBMIT_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Send Note"), "NOTE_op");

    /* Create edit form */
    $elements[0] = PHPWS_Form::formHidden("module", "notes");
    $elements[0] .= $GLOBALS["core"]->processTemplate($tags, "notes", "edit.tpl");
    $content = PHPWS_Form::makeForm("edit_note", "index.php", $elements);

    /* Display edit form */
    $GLOBALS["CNT_notes"]["content"] .= $content;
  }// END FUNC edit()

  /**
   * Sends this note to it's appropriate user/group.  This is done through some
   * database interaction.
   *
   * @access public
   */
  function send() {
    /* Check message text and save it first hand */
    if($_POST["message"]) {
      $this->_message = $_POST["message"];
      $queryData["message"] = $this->_message;
    } else {
      $this->_error("no_body");
      $this->edit();
      return;
    }

    /* Check recipients.  Group overrides User */
    if(isset($_POST["toGroup"])) {
      $this->_toGroup = $_POST["toGroup"];
      $queryData["toGroup"] = $this->_toGroup;
    } elseif($_POST["toUser"]) {
      $this->_toUser = $_POST["toUser"];
      $queryData["toUser"] = $this->_toUser;
    } else {
      $this->_error("no_recipient");
      $this->edit();
      return;
    }

    /* Set fromUser and dateSent as current date/time */
    $queryData["fromUser"] = $this->_fromUser;
    $this->_dateSent = date("Y-m-d H:i:s");
    $queryData["dateSent"] = $this->_dateSent;

    /* Save note in database */
    if(!$GLOBALS["core"]->sqlInsert($queryData, "mod_notes", "id")) {
      $this->_error("database");
      $this->edit();
      return;
    }

    /* Display menu and sent confirmation */
    $content = $_SESSION["translate"]->it("Your note was successfully sent!");
    $GLOBALS["CNT_notes"]["content"] .= $content;
  }// END FUNC send()

  /**
   * 2 functions:  Displays a delete confirmation to the user on first visit to this function.
   * Then, depending on the user's input, delete() will either keep the note (user_answer = "No")
   * or delete it from the database (user_answer = "Yes")
   *
   * @access public
   */
  function delete() {
    /* Make sure the current user is the owner of this note */
    if($this->_toUser == $_SESSION["OBJ_user"]->username) {
      if(isset($_POST["yes"])) {
	/* User submitted "yes" so delete the note and print the appropriate message */
	$GLOBALS["core"]->sqlDelete("mod_notes", "id", $_POST["NOTE_id"]);
	$content = $_SESSION["translate"]->it("Your note was successfully deleted.");
      } elseif(isset($_POST["no"])) {
	/* User submitted "no" so keep the note and print appropriate message */
	$content = $_SESSION["translate"]->it("Your note was <b>not</b> deleted.");
      } else {
	/* First time through this function ask for a confirmation from the user */
	$elements[0] = PHPWS_Form::formHidden("module", "notes");
	$elements[0] .= PHPWS_Form::formHidden("NOTE_op", "delete");
	$elements[0] .= PHPWS_Form::formHidden("NOTE_id", $_GET["NOTE_id"]);
	$elements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("Yes"), "yes");
	$elements[0] .= PHPWS_Form::formSubmit($_SESSION["translate"]->it("No"), "no");

	$content = "<br />" . $_SESSION["translate"]->it("Are you sure you wish to delete this note from") . " " . $this->_fromUser . "?<br /><br />";
	$content .= PHPWS_Form::makeForm("delete_note", "index.php", $elements);
	$this->read();
      }
    } else {
      /* Current user is not owner of this note, so print error and do not delete */
      $this->_error("unauthorized");
      return;
    }

    $GLOBALS["CNT_notes"]["content"] .= $content;
  }// END FUNC delete()

  /**
   * Returns an indexed array of all the current users in the database
   *
   * @return array $users An array of all users
   * @access private
   * @see    edit()
   */
  function _getUsers() {
    /* Grab all users from database */
    $result = $GLOBALS["core"]->sqlSelect("mod_users", NULL, NULL, "username");

    /* Add blank user */
    $users[] = " ";

    /* Create users array */
    if($result)
    foreach($result as $resultRow)
      $users[] = $resultRow["username"];
    natcasesort($users);
    return $users;
  }// END FUNC _getUsers()

  /**
   * Returns an indexed array of all the current groups in the database
   *
   * @return array $users An array of all groups
   * @access private
   * @see    edit()
   */
  function _getGroups() {
    /* Grab all groups from database */
    $result = $GLOBALS["core"]->sqlSelect("mod_user_groups", NULL, NULL, "group_name");

    /* Add blank group */
    $groups[] = " ";

    /* Create groups array */
    if($result)
    foreach($result as $resultRow)
      $groups[] = $resultRow["group_name"];

    return $groups;
  }// END FUNC _getGroups()

  /**
   * Marks this note as read by the current user on the current date/time
   *
   * @access private
   * @see    read()
   */
  function _mark() {
    $this->_userRead = $_SESSION["OBJ_user"]->username;
    $queryData["userRead"] = $this->_userRead;

    $this->_dateRead = date("Y-m-d H:i:s");
    $queryData["dateRead"] = $this->_dateRead;

    $GLOBALS["core"]->sqlUpdate($queryData, "mod_notes", "id", $this->_id);
  }// END FUNC _mark()

  /**
   * Displays an error depending on the $type variable sent in
   *
   * @param  string $type The type of error to display
   * @access private
   */
  function _error($type) {
    $content = "<div class=\"errortext\"><h3>" . $_SESSION["translate"]->it("ERROR!") . "</h3></div>";

    switch($type) {
      case "no_recipient":
      $content .= $_SESSION["translate"]->it("You must designate a recipient for this note.");
      break;

      case "no_body":
      $content .= $_SESSION["translate"]->it("You must provide a body to your note.");
      break;

      case "database":
      $content .= $_SESSION["translate"]->it("There was a database error when attempting to send your note.");
      break;

      case "bad_message":
      $content .= $_SESSION["translate"]->it("There was an error in the note you are attempting to read.  It will not be
      displayed for security reasons.  Contact your systems administrator for help.");
      break;

      case "unauthorized":
      $content .= $_SESSION["translate"]->it("You are not allowed to access the note you specified.");
      break;
    }

    $GLOBALS["CNT_notes"]["content"] .= $content;
  }// END FUNC _error()

}// END CLASS PHPWS_AtomNote

?>