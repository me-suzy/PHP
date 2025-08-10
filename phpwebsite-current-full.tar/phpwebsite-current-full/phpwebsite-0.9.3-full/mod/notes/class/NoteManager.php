<?php
/**
 * This class controls interactions with the Notes module and it's PHPWS_Note objects.
 *
 * @version $Id: NoteManager.php,v 1.9 2003/06/27 14:54:05 adam Exp $
 * @author  Adam Morton <adam@NOSPAM.tux.appstate.edu>
 * @package Notes
 */
class PHPWS_NoteManager {

  /**
   * Displays the main menu for Notes
   *
   * @access public
   */
  function menu() {
    $tags = array();
    $tags["NEW_NOTE"] = PHPWS_Core::formSubmit($_SESSION["translate"]->it("New Note"), "NOTE_op");
    $tags["MY_NOTES"] = PHPWS_Core::formSubmit($_SESSION["translate"]->it("My Notes"), "NOTE_op");
    $tags["SENT_NOTES"] = PHPWS_Core::formSubmit($_SESSION["translate"]->it("Sent Notes"), "NOTE_op");

    $elements[0] = PHPWS_Core::formHidden("module", "notes");
    $elements[0] .= $GLOBALS["core"]->processTemplate($tags, "notes", "menu.tpl");

    $content = PHPWS_Core::makeForm("notes_menu", "index.php", $elements);
    $GLOBALS["CNT_notes"]["content"] .= $content;
  }// END FUNC menu()

  /**
   * Displays the current user's notes in a list format.
   *
   * @access public
   * @see    _showNotes()
   */
  function myNotes() {
    /* Get all notes that were sent to this user */
    $result = $GLOBALS["core"]->sqlSelect("mod_notes", "toUser", $_SESSION["OBJ_user"]->username);

    /* Create a list of notes depending on result */
    $content = "<h3>" . $_SESSION["translate"]->it("My Notes") . "</h3>";
    $content .= $this->_showNotes($result, TRUE);

    /* Display notes */
    $GLOBALS["CNT_notes"]["content"] .= $content;
  }// END FUNC myNotes()

  /**
   * Displays the current user's sent messages in a list format
   *
   * @access public
   * @see    _showNotes()
   */
  function sentNotes() {
    /* Get all notes sent by the current user */
    $result = $GLOBALS["core"]->sqlSelect("mod_notes", "fromUser", $_SESSION["OBJ_user"]->username);

    /* Create a list of notes depending on result */
    $content = "<h3>" . $_SESSION["translate"]->it("Sent Notes") . "</h3>";
    $content .= $this->_showNotes($result);

    /* Display notes */
    $GLOBALS["CNT_notes"]["content"] .= $content;
  }//END FUNC sentNotes()

  /**
   * Using the templates provided with Notes and a database result passed in from the
   * module writer, this function builds a list of messages into a string variable to
   * be used for display by other functions.
   *
   * @access private
   * @see    myNotes(), sentNotes()
   */
  function _showNotes($result, $showDelete=FALSE) {
    $itemTags = array();
    $listTags = array();

    /* Check for result and create table containing sent messages or warn that no messages were found */
    if($result) {
      $listTags["LIST_ITEMS"] = NULL;

      /* Build headers for table containing messages */
      $itemTags["TITLE"] = "<b>" . $_SESSION["translate"]->it("Message") . "</b>";
      $itemTags["FROM"] = "<b>" . $_SESSION["translate"]->it("From") . "</b>";
      $itemTags["DATE_SENT"] = "<b>" . $_SESSION["translate"]->it("Date Sent") . "</b>";
      $itemTags["NEW"] = "<b>" . $_SESSION["translate"]->it("Status") . "</b>";
      $showDelete ? $itemTags["DELETE"] = "<b>" . $_SESSION["translate"]->it("Delete?") . "</b>" : $itemTags["DELETE"] = NULL;

      $listTags["LIST_ITEMS"] .= $GLOBALS["core"]->processTemplate($itemTags, "notes", "list_item.tpl");

      /* Build table of actual messages */
      foreach($result as $resultRow) {
	$itemTags["TITLE"] = "<a href=\"index.php?module=notes&amp;NOTE_op=read&amp;NOTE_id=" . $resultRow["id"] . "\">" . substr($resultRow["message"], 0, 20) . "</a>";
	$itemTags["FROM"] = $resultRow["fromUser"];
	$itemTags["DATE_SENT"] = $resultRow["dateSent"];
	$resultRow["dateRead"] != "0000-00-00 00:00:00" ? $itemTags["NEW"] = "<b><i>" . $_SESSION["translate"]->it("READ") . "</i></b>" : $itemTags["NEW"] = "<font color=\"green\"><b><i>" . $_SESSION["translate"]->it("NEW") . "</i></b></font>";
	$showDelete ? $itemTags["DELETE"] = "<a href=\"index.php?module=notes&amp;NOTE_op=delete&amp;NOTE_id=" . $resultRow["id"] . "\">" . $_SESSION["translate"]->it("Delete") . "</a>" : $itemTags["DELETE"] = NULL;

	$listTags["LIST_ITEMS"] .= $GLOBALS["core"]->processTemplate($itemTags, "notes", "list_item.tpl");
      }
      $content = $GLOBALS["core"]->processTemplate($listTags, "notes", "list.tpl");
    } else {
      /* Oops, no messages sent out by this user */
      $content = $_SESSION["translate"]->it("No messages found!");
    }
    return $content;
  }

  /**
   * Displays the user block that contains new note information
   *
   * @access public
   */
  function showBlock() {
    /* Grab the current user's notes */
    $userResult = $GLOBALS["core"]->sqlSelect("mod_notes", "toUser", $_SESSION["OBJ_user"]->username);

    /* If the userResult exists, count number of new notes */
    if($userResult) {
      $userNotes = 0;

      foreach($userResult as $resultRow)
	if($resultRow["dateRead"] == "0000-00-00 00:00:00")
	  $userNotes++;

      if($userNotes > 0) {
	$tags["USER_NOTES"] = "<a href=\"index.php?module=notes&amp;NOTE_op=" . $_SESSION["translate"]->it("My Notes") . "\">" . $userNotes . "</a>";

	/* Display block */
	$title = $_SESSION["translate"]->it("My Notes");
	$content = $GLOBALS["core"]->processTemplate($tags, "notes", "block.tpl");
	$GLOBALS["CNT_notes_block"]["title"] = $title;
	$GLOBALS["CNT_notes_block"]["content"] = $content;
      }
    }
  }// END FUNC showBlock()

}// END CLASS PHPWS_NoteManager

?>