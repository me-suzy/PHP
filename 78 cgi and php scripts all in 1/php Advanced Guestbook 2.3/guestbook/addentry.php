<?php
$include_path = dirname(__FILE__);
include_once $include_path."/admin/config.inc.php";
include_once $include_path."/lib/$DB_CLASS";
include_once $include_path."/lib/image.class.php";
include_once $include_path."/lib/template.class.php";

include_once $include_path."/lib/vars.class.php";
include_once $include_path."/lib/add.class.php";

$gb_post = new addentry($include_path);

if (isset($HTTP_POST_VARS["gb_action"])) {
    $gb_post->name = (isset($HTTP_POST_VARS["gb_name"])) ? $HTTP_POST_VARS["gb_name"] : '';
    $gb_post->email = (isset($HTTP_POST_VARS["gb_email"])) ? $HTTP_POST_VARS["gb_email"] : '';
    $gb_post->url = (isset($HTTP_POST_VARS["gb_url"])) ? $HTTP_POST_VARS["gb_url"] : '';
    $gb_post->comment = (isset($HTTP_POST_VARS["gb_comment"])) ? $HTTP_POST_VARS["gb_comment"] : '';
    $gb_post->location = (isset($HTTP_POST_VARS["gb_location"])) ? $HTTP_POST_VARS["gb_location"] : '';
    $gb_post->icq = (isset($HTTP_POST_VARS["gb_icq"])) ? $HTTP_POST_VARS["gb_icq"] : '';
    $gb_post->aim = (isset($HTTP_POST_VARS["gb_aim"])) ? $HTTP_POST_VARS["gb_aim"] : '';
    $gb_post->gender = (isset($HTTP_POST_VARS["gb_gender"])) ? $HTTP_POST_VARS["gb_gender"] : '';
    $gb_post->userfile = (isset($HTTP_POST_FILES["userfile"]["tmp_name"]) && $HTTP_POST_FILES["userfile"]["tmp_name"] != "") ? $HTTP_POST_FILES : '';
    $gb_post->user_img = (isset($HTTP_POST_VARS["gb_user_img"])) ? $HTTP_POST_VARS["gb_user_img"] : '';
    $gb_post->preview = (isset($HTTP_POST_VARS["gb_preview"])) ? 1 : 0;
    $gb_post->private = (isset($HTTP_POST_VARS["gb_private"])) ? 1 : 0;
    echo $gb_post->process($HTTP_POST_VARS["gb_action"]);
} else {
    echo $gb_post->process();
}

?>