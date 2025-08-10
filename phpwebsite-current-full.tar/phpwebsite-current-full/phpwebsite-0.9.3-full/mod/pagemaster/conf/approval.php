<?php
if($_SESSION["OBJ_user"]->allow_access("pagemaster")){
  if ($approvalChoice == "yes"){
    PHPWS_Page::approve($id);
  } else if ($approvalChoice == "no") {
    PHPWS_Page::refuse($id);
  } else if ($approvalChoice == "view") {
    $page = new PHPWS_Page($id);
    $page->view_page();
    echo $_SESSION['OBJ_layout']->popbox($GLOBALS['CNT_pagemaster']['title'], $GLOBALS['CNT_pagemaster']['content']);
  }
}

?>