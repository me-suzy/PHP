<?php

/**
 * PHPWS Messaging class
 *
 * @version $Id: Message.php,v 1.2 2003/04/30 18:00:46 steven Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Core
 */
class PHPWS_Message {

  var $_title = NULL;
  var $_content = NULL;
  var $_contentVar = NULL;

  function PHPWS_Message($content, $contentVar, $title=NULL) {
    $this->_content = $content;
    $this->_contentVar = $contentVar;
    $this->_title = $title;
  }

  function display() {
    $messageTags = array();
    $messageTags['CONTENT'] = $this->_content;

    if(isset($this->_title)) {
      $messageTags['TITLE'] = $this->_title;
    }

    $GLOBALS[$this->_contentVar]['content'] .= $GLOBALS['core']->processTemplate($messageTags, "core", "message.tpl");
    $this = NULL;
  }
  
  function isMessage($value) {
    return (is_object($value) && (get_class($value) == 'phpws_message' || is_subclass_of($value, 'phpws_message')));
  }
} // END CLASS PHPWS_Message

?>
