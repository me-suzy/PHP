<?php

class PollManager extends PHPWS_Manager {

  var $poll;
  var $userbox_poll;

  
  function PollManager() {
    $this->setModule("poll");
    $this->setRequest("PHPWS_MAN_OP");
    $this->setTable("mod_poll");
    
    $this->init();
  }

  function menu() {
    $tags["SHOW_POLLS"] = "<a href=\"index.php?module=poll&amp;poll_op=showpolls\">Show Polls</a>";
    $tags["NEW_POLL"] = "<a href=\"index.php?module=poll&amp;poll_op=newpoll\">New Poll</a>";
    
    $elements[0] = PHPWS_Core::formHidden("module", "poll");
    $elements[0] .= $GLOBALS["core"]->processTemplate($tags, "poll","menu.tpl");
    $content = PHPWS_Core::makeForm("Poll", "index.php", $elements);
    return $content;														
  }
  
  function _list() {
    $this->init();
    $content = $this->menu();
    $content .= $this->getList("polls");
    $title = $_SESSION["translate"]->it("Poll");
    $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");
  }
  
  function _getUsers() {
    $result = $GLOBALS["core"]->sqlSelect("mod_users", NULL, NULL, "username");
    $users[] = " ";
    
    if($result)
      foreach($result as $resultRow)
	$users[] = $resultRow["username"];
    return $users;
  }
  
  function _delete($ids) {
    $content = $this->menu();
    $title = $_SESSION['translate']->it("Poll");
    foreach($ids as $value) {
      $this->poll = new PHPWS_Poll($value);
      $this->poll->kill();
    }
    $content .= $_SESSION['translate']->it("Deleted Poll Successfully");
    $_SESSION["OBJ_layout"]->popbox($title, $content, NULL,"CNT_POLL");
  }
  
  function _view($ids) {
    $content = $this->menu();
    $title = $_SESSION['translate']->it("Poll");
    $_SESSION["OBJ_layout"]->popbox($title, $content, NULL,"CNT_POLL");
    foreach($ids as $value) {
      $this->poll = new PHPWS_Poll($value);
      $title = $this->poll->_label;
      $content = $this->poll->_body."\n";
      foreach($this->poll->_options as $option) {
	$content .= $option."\n";
      }
      $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");
    }
  }
  
  function _edit($ids) {
    $content = $this->menu();
    $title = $_SESSION['translate']->it("Poll");
    $_SESSION["OBJ_layout"]->popbox($title, $content, NULL,"CNT_POLL");
    $this->poll = new PHPWS_Poll($ids[0]);
    $this->poll->edit();
  }
  
  function showUserBox() {
    $sql = "SELECT id FROM mod_poll WHERE active='Yes'";
    $result = $GLOBALS["core"]->getOne($sql, TRUE);
    if(isset($result)) {
      $this->userbox_poll = new PHPWS_Poll($result);
      $this->userbox_poll->showUserBox();
    }
  }
  
  function action() {
    switch($_REQUEST["poll_op"]) {
    case "list":
      if ($_SESSION["OBJ_user"]->allow_access("poll", "view")) {
	$this->_list();
      } else {
	$this->_error("access_denied");
      }
      break;
      
    case "newpoll":
      if ($_SESSION["OBJ_user"]->allow_access("poll", "create")) {
	$content = $this->menu();
	$title = "Poll";
	$_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");
	
	$this->poll = new PHPWS_Poll;
	$this->poll->edit();
      } else {
	$this->_error("access_denied");
      }
      break;
      
    case "showpolls":
      if ($_SESSION["OBJ_user"]->allow_access("poll", "view")) {
	$this->_list();
      } else {
	$this->_error("access_denied");
      }
      break;
      
    case "Add Option":
      $content = $this->menu();
      $title = "Poll";
      $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");
      $this->poll->edit();
      break;
      
    case "Submit":
      $content = $this->menu();
      $title = "Poll";
      $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");
      $this->poll->send();
      break;
      
    case "Vote":
      $this->userbox_poll->vote();
      break;
      
    case "result":
      $this->userbox_poll->showResult();
      break;
    }
  }
  
  function _error($type) {
    $title = "<font class=\"errortext\">" . $_SESSION["translate"]->it("ERROR!") . "</font>";
    switch($type) {
    case "access_denied":
      $content = $_SESSION["translate"]->it("ACCESS DENIED!");
      break;
    }
    $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_POLL");
  }
}
