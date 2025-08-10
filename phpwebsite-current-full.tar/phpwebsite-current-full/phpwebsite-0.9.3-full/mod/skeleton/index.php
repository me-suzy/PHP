<?php
/**
 * This is a skeleton index file.  Edit it to be used with your module.
 *
 * $Id: index.php,v 1.4 2003/07/10 13:08:27 matt Exp $
 */
if (!isset($GLOBALS['core'])){
  header("location:../../");
  exit();
}

if(!isset($_SESSION["PHPWS_SkeletonManager"])) {
  $_SESSION["PHPWS_SkeletonManager"] = new PHPWS_SkeletonManager;
}

$GLOBALS["CNT_skeleton"] = array("title"=>"Skeleton Module",
				 "content"=>NULL);

if(isset($_REQUEST["SKEL_MAN_OP"])) {
  $_SESSION["PHPWS_SkeletonManager"]->managerAction();
  $_SESSION["PHPWS_SkeletonManager"]->action();
}

if(isset($_REQUEST["SKEL_OP"]) && isset($_SESSION["PHPWS_SkeletonManager"]->skeleton)) {
  $_SESSION["PHPWS_SkeletonManager"]->skeleton->action();
}

?>