<?php

if($_SESSION["OBJ_user"]->allow_access("announce")){
  if ($approvalChoice == "yes"){
    PHPWS_FaqManager::approvalApprove($id);
  } else if ($approvalChoice == "no") {
    PHPWS_FaqManager::approvalRefuse($id);
  } else if ($approvalChoice == "view") {
    $ids[0] = $id;
    $_SESSION["SES_FAQ_MANAGER"]->_view($ids, 1);
  }
}

?> 