<?php

/**
 * Controls all the operation need to be done to a single comment
 *
 * @version $Id: Comment.php,v 1.22 2003/06/27 14:44:10 steven Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Comment Manager
 */
class PHPWS_Comment {

  /**
   * primary id of this comment
   * @var int
   */
  var $cid = NULL;

  /**
   * subject of this comment
   * @var string
   */
  var $subject = NULL;

  /**
   * comment of this comment
   * @var string
   */
  var $comment = NULL;

  /**
   * score for this comment
   * @var int
   */
  var $score = NULL;

  /**
   * date posted for this comment
   * @var string
   */
  var $postDate = NULL;

  /**
   * author of this comment
   * @var string
   */
  var $author = NULL;

  /**
   * ip address for the author of this comment
   * @var string
   */
  var $authorIp = NULL;

  /**
   * editor of this comment
   * @var string
   */
  var $editor = NULL;

  /**
   * reason to edit this comment
   * @var string
   */
  var $editReason = NULL;

  /**
   * date this comment was edited
   * @var integer
   */
  var $editDate = NULL;

  /**
   * whether or not a known user posted the comment as anonymous
   * @var boolean
   */
  var $anonymous = 0;

  /**
   * PHPWS_Comment
   *
   * Constructor for the PHPWS_Comment class
   *
   * @param int cid primary key for this comment
   */
  function PHPWS_Comment($cid = NULL) {
    if($cid) {
      $this->cid = $cid;
      $commentResult = $GLOBALS['core']->sqlSelect("mod_comments_data", "cid", $this->cid);
      if($commentResult) {
	$this->subject = $commentResult[0]['subject'];
	$this->comment = $commentResult[0]['comment'];
	$this->score = $commentResult[0]['score'];
	$this->postDate = $commentResult[0]['postDate'];
	$this->author = $commentResult[0]['author'];
	$this->authorIp = $commentResult[0]['authorIp'];
	$this->editor = $commentResult[0]['editor'];
	$this->editReason = $commentResult[0]['editReason'];
	$this->editDate = $commentResult[0]['editDate'];
	$this->anonymous = $commentResult[0]['anonymous'];
      } else {
	exit("Invalid ID passed to PHPWS_Comment");
      }
    }
  } // END FUNC PHPWS_Comment

  /**
   * comment
   *
   * Provides functionality to add and edit comments
   *
   * @param string $action "add" or "edit" comment
   */
  function comment($action) {
    if (!isset($GLOBALS['CNT_comments']['content']))
      $GLOBALS['CNT_comments']['content'] = NULL;
    $ip = $_SERVER['REMOTE_ADDR'];
    $module = $_SESSION['PHPWS_CommentManager']->getCurrentModule();
    $itemId = $_SESSION['PHPWS_CommentManager']->getCurrentItemId();
    $sql = "SELECT cid FROM " . $GLOBALS['core']->tbl_prefix . "mod_comments_data WHERE authorIp='$ip' AND module='$module' AND itemId='$itemId'";
    $postsResult = $GLOBALS['core']->query($sql);

    $maxSub = $_SESSION['PHPWS_CommentManager']->getMaxSubmissions();
    if(($postsResult->numrows() >= $maxSub) && ($maxSub != 0)) {
      $hiddens = array("module"=>"comments",
		       "CM_op"=>"refreshView"
		       );

      $elements[0] = $_SESSION['translate']->it("You have exceeded the maximum number allowed comments for this forum.");
      $elements[0] .= $GLOBALS['core']->formHidden($hiddens) . "<br /><br />";
      $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Back"));

      $GLOBALS['CNT_comments']['title'] = $_SESSION['translate']->it("Comment Restriction");
      $GLOBALS['CNT_comments']['content'] = $GLOBALS['core']->makeForm("CM_maxerror", "index.php", $elements, "post", NULL, NULL);
      return;
    }
    if($action == "add") {
      $GLOBALS['CNT_comments']['title'] = $_SESSION['translate']->it("Post Comment");
      $hiddens = array("module"=>"comments",
		       "CM_op"=>"addAction"
		       );
    } else if($action == "edit") {
      $GLOBALS['CNT_comments']['title'] = $_SESSION['translate']->it("Edit Comment");
      $hiddens = array("module"=>"comments",
		       "CM_op"=>"editAction",
		       "CM_cid"=>"$this->cid"
		       );
    }

    /* check to see if this comment has a parent */
    if(isset($_REQUEST['CM_pid'])) {
      $pid = $_REQUEST['CM_pid'];
      $result = $GLOBALS['core']->quickFetch("SELECT subject FROM " . $GLOBALS['core']->tbl_prefix . "mod_comments_data WHERE cid='$pid'");

      $hiddens = array_merge($hiddens, array("CM_pid"=>"$pid"));

      $text = $_SESSION['translate']->it("Re") . ": ";
      if(!preg_match("/$text/", $result['subject'])) {
	$this->subject = $text . $result['subject'];
      } else {
	$this->subject = $result['subject'];
      }
    }

    if($_SESSION['OBJ_user']->username) {
      $CM_template['USER_TEXT'] = $_SESSION['translate']->it("You are currently logged in as");
      $CM_template['USER_INFO'] = $user = $_SESSION['OBJ_user']->username;
      $hiddens = array_merge($hiddens, array("CM_user"=>"$user"));
    } else {
      $CM_template['USER_TEXT'] = $_SESSION['translate']->it("You are not currently logged in. Posts will be Anonymous.");
    }

    $CM_template['HIDDENS'] = $GLOBALS['core']->formHidden($hiddens);
    $CM_template['SUBJECT_TEXT'] = $_SESSION['translate']->it("Subject");
    $CM_template['SUBJECT_FIELD'] = $GLOBALS['core']->formTextField("CM_subject", $this->subject, 35, 120);
    $CM_template['COMMENT_TEXT'] = $_SESSION['translate']->it("Comment");

    if($_SESSION['OBJ_user']->js_on){
      $CM_template['COMMENT_FIELD'] = $GLOBALS['core']->js_insert("wysiwyg", "CM_addedit", "CM_comment");
    } else {
      $CM_template['COMMENT_FIELD'] = "";
    }
    $CM_template['COMMENT_FIELD'] .= $GLOBALS['core']->formTextArea("CM_comment", $this->comment, 8, 60);

    if($_SESSION['PHPWS_CommentManager']->getAllowAnonymous() && $_SESSION['OBJ_user']->username) {
      $CM_template['ANONYMOUS_TEXT'] = $_SESSION['translate']->it("Post anonymously");
      $CM_template['ANONYMOUS_FIELD'] = $GLOBALS['core']->formCheckBox("CM_anonymous", 1, $this->anonymous);
    }

    $CM_template['SUBMIT_PREVIEW'] = $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Preview"), "CM_preview");
    $CM_template['SUBMIT_POST'] = $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Post"), "CM_post");

    if($action == "edit") {
      $CM_template['EDIT_TEXT'] = $_SESSION['translate']->it("Reason for edit");
      $CM_template['EDIT_FIELD'] = $GLOBALS['core']->formTextArea("CM_editReason", $this->editReason, 4, 40);
    }

    $content[0] = $GLOBALS['core']->processTemplate($CM_template, "comments", "addEditComment.tpl");

    $GLOBALS['CNT_comments']['content'] .= $GLOBALS['core']->makeForm("CM_addedit", "index.php", $content, "post", NULL, NULL);
  } // END FUNC comment

  /**
   * previewComment
   *
   * Preview a comment that is being added or edited
   *
   * @param string $action "add" or "edit" which called the preview, controls how comment is saved
   */
  function previewComment($action) {
    if (!isset($GLOBALS['CNT_comments']['content']))
      $GLOBALS['CNT_comments']['content'] = NULL;
    if(!$this->saveComment()) {
      $this->comment($action);
      return;
    }

    if($action == "add") {
      $GLOBALS['CNT_comments']['title'] = $_SESSION['translate']->it("Preview Comment");
      $hiddens = array("module"=>"comments",
		       "CM_op"=>"addPreviewAction"
		       );
    } else if($action == "edit") {
      $GLOBALS['CNT_comments']['title'] = $_SESSION['translate']->it("Preview Edited Comment");
      $hiddens = array("module"=>"comments",
		       "CM_op"=>"editPreviewAction",
		       "CM_cid"=>"$this->cid"
		       );
    }

    /* check to see if this comment has a parent */
    if(isset($_REQUEST['CM_pid'])) {
      $pid = $_REQUEST['CM_pid'];
      $hiddens = array_merge($hiddens, array("CM_pid"=>"$pid"));
    }

    $CM_template['HIDDENS'] = $GLOBALS['core']->formHidden($hiddens);
    $CM_template['SUBJECT'] = $this->subject;
    $CM_template['COMMENT'] = $GLOBALS['core']->parseOutput($this->comment);
    $CM_template['EDIT_REASON'] = $this->editReason;
    $CM_template['SUBMIT_EDIT'] = $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Edit"), "CM_edit");
    $CM_template['SUBMIT_POST'] = $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Post"), "CM_post");

    $content[0] = $GLOBALS['core']->processTemplate($CM_template, "comments", "previewComment.tpl");
    $GLOBALS['CNT_comments']['content'] .= $GLOBALS['core']->makeForm("CM_preview", "index.php", $content, "post", NULL, NULL);
  } // END FUNC previewComment

  /**
   * postComment
   *
   * Saves a comment to the database
   *
   * @param bool $preview whether or not save is coming after a preview, controls if class values are saved
   */
  function postComment($preview) {
    if(!$preview) {
      if(!$this->saveComment()) {
	if(stristr($_REQUEST['CM_op'], "add")) {
	  $action = "add";
	} else {
	  $action = "edit";
	}
	$this->comment($action);
	return FALSE;
      }
    }

    $subject = $GLOBALS['core']->addSlashes($this->subject);
    $comment = $GLOBALS['core']->addSlashes($this->comment);
    $editReason = $GLOBALS['core']->addSlashes($this->editReason);
    $module = $_SESSION['PHPWS_CommentManager']->getCurrentModule();
    $itemId = $_SESSION['PHPWS_CommentManager']->getCurrentItemId();

    $saveArray = array("subject"=>"$subject",
		       "comment"=>"$comment",
		       "postDate"=>"$this->postDate",
		       "author"=>"$this->author",
		       "authorIp"=>"$this->authorIp",
		       "editor"=>"$this->editor",
		       "editReason"=>"$editReason",
		       "editDate"=>"$this->editDate",
		       "module"=>"$module",
		       "itemId"=>"$itemId",
		       "anonymous"=>"$this->anonymous"
		       );

    $error = NULL;
    if($this->cid) {
      $GLOBALS['core']->sqlUpdate($saveArray, "mod_comments_data", "cid", $this->cid);
    } else if(isset($_POST['CM_pid'])) {
      $pid = $_POST['CM_pid'];
      $saveArray['pid'] = $pid;
      $GLOBALS['core']->sqlInsert($saveArray, "mod_comments_data");
    } else {
      $GLOBALS['core']->sqlInsert($saveArray, "mod_comments_data");
    }

    unset($_SESSION['comment']);
    return TRUE;
  } // END FUNC postComment

  /**
   * saveComment
   *
   * Saves the posted data to the class
   */
  function saveComment() {
    if(isset($_POST['CM_subject'])) {
      $subject = $GLOBALS['core']->parseInput($_POST['CM_subject']);
    }
    if(isset($_POST['CM_comment'])) {
      $comment = $GLOBALS['core']->parseInput($_POST['CM_comment']);
    }
    if(isset($_POST['CM_editReason'])) {
      $editReason = $GLOBALS['core']->parseInput($_POST['CM_editReason']);
    }

    if($this->subject && $this->comment) {
      if((strcmp($this->subject, $subject) != 0) || (strcmp($this->comment, $comment) != 0)  || (isset($editReason) && strcmp($this->editReason, $editReason) != 0)) {
	$edited = TRUE;
      }
    }

    $this->subject = $subject;
    $this->comment = $comment;
    if (isset($editReason))
      $this->editReason = $editReason;

    if(!$this->postDate && !$this->author && !$this->authorIp) {
      $this->postDate = date("Y-m-d H:i:s");
      $this->authorIp = $_SERVER['REMOTE_ADDR'];

      if(isset($_REQUEST['CM_user']) && isset($_REQUEST['CM_anonymous'])) {
	$this->author = $_REQUEST['CM_user'];
	$this->anonymous = 1;
      } else if(isset($_REQUEST['CM_user'])) {
	$this->author = $_REQUEST['CM_user'];
	$this->anonymous = 0;
      } else {
	$this->author = "Anonymous";
	$this->anonymous = 0;
      }
    }

    if(isset($edited) && $edited == TRUE) {
      $this->editor = $_SESSION['OBJ_user']->username;
      $this->editDate = date("Y-m-d H:i:s");
    }

    $maxChars = $_SESSION['PHPWS_CommentManager']->getMaxCommentSize();
    if((strlen($this->comment) > $maxChars) && ($maxChars != 0)) {
      $this->error($_SESSION['translate']->it("Your comment exceeds the max size allowed."));
      return FALSE;
    }
    
    return TRUE;
  }// END FUNC saveComment

  /**
   * viewComment
   *
   * Handles the viewing of a comment this whole display is templated in the viewComment.tpl
   *
   * @return string the content
   */
  function viewComment() {
    $CM_template['SUBJECT'] = $this->subject;
    $CM_template['COMMENT'] = $GLOBALS["core"]->parseOutput($this->comment);
    $CM_template['POST_TEXT'] = $_SESSION['translate']->it("Posted on");
    $CM_template['POST_DATE'] = $this->postDate;
    $CM_template['AUTHOR_TEXT'] = $_SESSION['translate']->it("By");

    if(!$this->anonymous) {
      $CM_template['AUTHOR'] = $this->author;
    } else {
      $CM_template['AUTHOR'] = "Anonymous";
    }

    if($_SESSION['OBJ_user']->allow_access("comments")) {
      $CM_template['AUTHOR_IP_TEXT'] = $_SESSION['translate']->it("Location");
      $CM_template['AUTHOR_IP'] = $this->authorIp;
    }

    if($this->editor && $this->editDate) {
      $CM_template['EDITOR_TEXT'] = $_SESSION['translate']->it("Edited By");
      $CM_template['EDITOR'] = $this->editor;
      $CM_template['EDIT_DATE_TEXT'] = $_SESSION['translate']->it("On");
      $CM_template['EDIT_DATE'] = $this->editDate;
      if($this->editReason) {
	$CM_template['EDIT_REASON_TEXT'] = $_SESSION['translate']->it("Edit Reason");
	$CM_template['EDIT_REASON'] = $this->editReason;
      }
    }

    if($_SESSION['OBJ_user']->username || $_SESSION['PHPWS_CommentManager']->getAllowAnonymous()) {
      $CM_template['REPLY_LINK'] = "<a href=\"./index.php?module=comments&amp;CM_op=replyToComment&amp;CM_pid=$this->cid\">". $_SESSION['translate']->it("Reply") . "</a>\n";
    }

    if($_SESSION['OBJ_user']->allow_access("comments", "edit") || $_SESSION['OBJ_user']->username == $this->author) {
      $CM_template['EDIT_LINK'] = "<a href=\"./index.php?module=comments&amp;CM_op=editComment&amp;CM_cid=$this->cid\">". $_SESSION['translate']->it("Edit") . "</a>\n";
    }
 
    if($_SESSION['OBJ_user']->allow_access("comments", "delete") || $_SESSION['OBJ_user']->username == $this->author) {
      $CM_template['DELETE_LINK'] = "<a href=\"./index.php?module=comments&amp;CM_op=deleteComment&amp;CM_cid=$this->cid\">". $_SESSION['translate']->it("Delete") . "</a>\n";
    }

    $content = $GLOBALS['core']->processTemplate($CM_template, "comments", "viewComment.tpl");
    return $content;
  } // END FUNC viewComment

  /**
   * delete
   *
   * Confirmation and delete actions for the comment module
   */
  function delete() {
    if(isset($_POST['CM_yes'])) {
      $this->deleteComments();
      $title = $_SESSION['translate']->it("Comment Deleted");
      $content = $_SESSION['translate']->it("The comment [var1] was successfully deleted from the database.", "<b><i>" . $this->subject . "</i></b>");
    } else if(isset($_POST['CM_no'])) {
      $title = $_SESSION['translate']->it("No Comment Deleted");
      $content = $_SESSION['translate']->it("No comment was deleted from the database.");
    } else {
      $title = $_SESSION['translate']->it("Delete Comment Confirmation");
      $content = $_SESSION['translate']->it("Are you sure you want to delete the comment [var1] and all its children?", "<b><i>" . $this->subject . "</i></b>");

      $hiddens = array("module"=>"comments",
		       "CM_op"=>"deleteComment",
		       "CM_cid"=>"$this->cid"
		       );

      $elements[0] = "<br />";
      $elements[0] .= $GLOBALS['core']->formHidden($hiddens);
      $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Yes"), "CM_yes");
      $elements[0] .= "&#160;&#160;";
      $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("No"), "CM_no");

      $content .= $GLOBALS['core']->makeForm("CM_delete", "index.php", $elements, "post", NULL, NULL);
    }

    //$GLOBALS['OBJ_layout']->popbox($title, $content, NULL, "CNT_comments");
    $GLOBALS['CNT_comments']['title'] = $title;
    $GLOBALS['CNT_comments']['content'] = $content;

    /* only display comments after an action has been taken */
    if(isset($_POST['CM_yes']) || isset($_POST['CM_no'])) {
      $_SESSION['PHPWS_CommentManager']->goBack();
    }
  } // END FUNC delete

  /**
   * deleteComments
   *
   * Actually handles the sql to delete the comments and call to delete children
   */
  function deleteComments() {
    $this->deleteCommentChildren($this->cid);
    $GLOBALS['core']->sqlDelete("mod_comments_data", "cid", $this->cid);
  } // END FUNC deleteComments

  /**
   * deleteCommentChildren
   *
   * Deletes all the children for the parent id which is passed in
   * @param int pid id of the parent comment
   */
  function deleteCommentChildren($pid) {
    $sql = "SELECT cid FROM " . $GLOBALS['core']->tbl_prefix . "mod_comments_data WHERE pid='$pid'";
    $deleteResult = $GLOBALS['core']->query($sql);

    while($row = $deleteResult->fetchrow(DB_FETCHMODE_ASSOC)) {
      $cid = $row['cid'];
      $this->deleteCommentChildren($cid);
      $GLOBALS['core']->sqlDelete("mod_comments_data", "cid", $cid);
    }
  } // END FUNC deleteCommentChildren

  /**
   * error
   *
   * handles printing error messages for this class
   */
  function error($text) {
    $title = "<br /><span class=\"error_text\">" . $_SESSION['translate']->it("Error!") . "</span><br />";

    //$GLOBALS['OBJ_layout']->popbox($title, $text . "<br /><br />", NULL, "CNT_comments");
    $GLOBALS['CNT_comments']['content'] .= $title . $content;
  }

} // END CLASS PHPWS_Comment

?>