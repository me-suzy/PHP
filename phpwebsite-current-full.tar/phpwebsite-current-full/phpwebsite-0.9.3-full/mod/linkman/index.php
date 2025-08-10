<?php

/**
 * Link Manager module main switch
 *
 * @version $Id: index.php,v 1.10 2003/07/10 13:39:49 steven Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 * @package Link Manager
 */

if(!isset($GLOBALS['core'])) {
  header("Location: ../..");
  exit();
}

/* begin switch */
if (!isset($_REQUEST['LMN_op']))
     return;

if ($_REQUEST['LMN_op'] != "userMenuAction")
     unset($_SESSION['LinkPage']);

switch($_REQUEST['LMN_op']) { 
 case "adminMenuAction":
   $_SESSION['PHPWS_Linkman']->adminMenuAction();
   break;

 case "userMenuAction":
   $_SESSION['PHPWS_Linkman']->userMenuAction();
   break;

 case "linkListAction":
   $_SESSION['PHPWS_Linkman']->linkListAction();
   break;

 case "linkAction":
   $_SESSION['PHPWS_Linkman']->currentLink->linkAction();
   break;

 case "visitLink":
   $_SESSION['PHPWS_Linkman']->visitLink();
   break;

}
/* end switch */

?>