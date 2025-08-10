<?php
$include_path = dirname(__FILE__);
include_once $include_path."/admin/config.inc.php";
include_once $include_path."/lib/$DB_CLASS";
include_once $include_path."/lib/image.class.php";
include_once $include_path."/lib/template.class.php";

include_once $include_path."/lib/vars.class.php";
include_once $include_path."/lib/comment.class.php";


$gb_com = new gb_comment($include_path);
$gb_com->id = (isset($HTTP_GET_VARS["gb_id"])) ? $HTTP_GET_VARS["gb_id"] : '';
$gb_com->id = (isset($HTTP_POST_VARS["gb_id"])) ? $HTTP_POST_VARS["gb_id"] : $gb_com->id;
$gb_com->comment = (isset($HTTP_POST_VARS["comment"])) ? $HTTP_POST_VARS["comment"] : '';
$gb_com->user = (isset($HTTP_POST_VARS["gb_user"])) ? $HTTP_POST_VARS["gb_user"] : '';
$gb_com->pass_comment = (isset($HTTP_POST_VARS["pass_comment"])) ? $HTTP_POST_VARS["pass_comment"] : '';
$gb_action = (isset($HTTP_POST_VARS["gb_comment"])) ? $HTTP_POST_VARS["gb_comment"] : '';
$gb_com->comment_action($gb_action);

?>