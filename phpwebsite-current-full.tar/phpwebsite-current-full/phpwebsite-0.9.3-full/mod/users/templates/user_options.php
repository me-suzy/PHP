<?php

//Creates a small user admin block
/*
if ($_SESSION["OBJ_user"]->allow_access("users")){

  if ($_SESSION["OBJ_user"]->allow_access("users", "add_user"))
    $user_opts["ADD_USER"] = $GLOBALS["core"]->link("index.php", $_SESSION["translate"]->it("Add User"), "index", array("module"=>"users", "user_op"=>"user_direct", "user_dir_com[add_user]"=>1));

  if ($_SESSION["OBJ_user"]->allow_access("users", "manage_users"))
    $user_opts["MANAGE_USERS"] = $GLOBALS["core"]->link("index.php", $_SESSION["translate"]->it("Manage Users"), "index", array("module"=>"users", "user_op"=>"user_direct", "user_dir_com[manage_users]"=>1));

  if ($_SESSION["OBJ_user"]->allow_access("users", "manage_groups"))
    $user_opts["MANAGE_GROUPS"] = $GLOBALS["core"]->link("index.php", $_SESSION["translate"]->it("Manage Groups"), "index", array("module"=>"users", "user_op"=>"user_direct", "user_dir_com[manage_groupss]"=>1));

  $template["USER_OPTIONS"] = $GLOBALS["core"]->processTemplate($user_opts, "users", "user_options.tpl");
}
*/
?>