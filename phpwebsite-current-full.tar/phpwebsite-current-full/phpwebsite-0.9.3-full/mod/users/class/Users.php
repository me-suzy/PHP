<?php

/**
 * The user class handles the authorization and manipulation of the user
 * accounts within the system.
 *
 * The user class holds on to the data of the person who logged in. It 
 * carries with it their personal information and their rights within the
 * system, what modules they have access to and what parts of each module
 * are defined. This will described in more detail per function.
 *
 * @version $Id: Users.php,v 1.81 2003/06/10 17:23:12 matt Exp $
 * @author Matthew McNaney matt@NOSPAM.tux.appstate.edu
 * @module users
 * @modulegroup administration
 * @package phpWebSite
 */
define("USER_COOKIE", md5($GLOBALS["core"]->site_hash."_user"));
require(PHPWS_SOURCE_DIR . "mod/users/init.php");


class PHPWS_User extends PHPWS_User_Groups{
  var $user_id;
  var $username;
  var $password;
  var $email;
  var $admin_switch;
  var $deity;
  var $groups;
  var $modSettings;
  var $permissions;
  var $groupPermissions;
  var $groupModSettings;
  var $error;

  var $temp_var;
  var $last_on;
  var $js_on;
  var $user_settings;

  /**
   * Constructor for PHPWS_User class
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param integer id If greater than 0, loads the appropiate user
   */
  function PHPWS_User($id = 0){
    $this->username         = NULL;
    $this->password         = NULL;
    $this->email            = NULL;
    $this->admin_switch     = 0;
    $this->deity            = 0;
    $this->groups           = array();
    $this->modSettings      = NULL;
    $this->permissions      = NULL;
    $this->groupPermissions = NULL;
    $this->groupModSettings = NULL;
    $this->last_on          = NULL;
    $this->js_on            = NULL;
    $this->user_settings    = NULL;
    $this->error            = array();

    if (is_numeric($id) && $id > 0){
      if (!$this->loadUser($id)){
	$error = new PHPWS_Error("users", "PHPWS_User", "Unable to load user id $id", "exit", 1);
	$error->message();
      }
    }
    else
      $this->user_id = 0;
  }

  /**
   * Loads the user information into the class
   * 
   * Permissions are handled separately in the load_permissions function
   *
   * @author            Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param integer id  User's id
   * @param boolean log If TRUE, update the user's log time
   */
  function loadUser ($id, $log=FALSE){
    if (list($row) = $GLOBALS["core"]->sqlSelect("mod_users", "user_id", $id)){
      extract($row);

      $this->user_id      = $id;
      $this->username     = $username;
      $this->password     = $password;
      $this->email        = $email;

      if ($admin_switch) $this->admin_switch = TRUE;
      else $this->admin_switch = FALSE;

      if ($deity) $this->deity = TRUE;
      else $this->deity = FALSE;

      $this->last_on      = $last_on;

      $this->loadModSettings("user");
      $this->setPermissions();

      if ($groups) $this->groups = $this->listGroups();

      $this->groupPermissions = $this->getMemberRights();
      $this->groupModSettings = $this->loadUserGroupVars();

      if ($log){
	$log_sess++;
	$GLOBALS["core"]->sqlUpdate(array("log_sess"=>$log_sess, "last_on"=>mktime()), "mod_users", "user_id", $id);
      }

      return TRUE;
    } else 
      return FALSE;
  }


  /**
   * Fetches a user id by searching by the username
   *
   * @author                   Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param   string  username Username to search by
   * @returns integer          Returns the id if found, zero otherwise
   */
  function getUserId($username=NULL){
    if (isset($username) && $user = $GLOBALS["core"]->sqlSelect("mod_users", "username", $username))
      return $user[0]["user_id"];
    elseif (isset($this))
      return $this->user_id;
    else return FALSE;
  }

  /**
   * Sets the user's username
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param string username Username to apply to user
   */
  function setUsername($username, $checkDuplicate=TRUE){
    if (!$this->allowUsername($username, $checkDuplicate))
      return FALSE;

    $this->username = $username;
    return TRUE;
  }


  /**
   * Returns the username of an user
   *
   * @author             Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param   integer id If set, function will pull username from the database
   * @returns string     Returns username of user
   */
  function getUsername($id=NULL){
    if ($id){
      $user = new PHPWS_User($id);
      return $user->username;
    } else
      return $this->username;
  }

  /**
   * Sets the user's password
   *
   * If the password does not fit certain standards, it will be rejected
   *
   * @author                   Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param   string  password String to be hashed as password
   * @returns boolean          TRUE is the password can be used, FALSE otherwise
   */
  function setPassword($password){
    $passcheck = $this->checkPassword($password, $password);

    if (is_string($passcheck)){
      $this->error[] = $passcheck;
      return FALSE;
    }

    $this->password = md5($password);
    return TRUE;
  }

  /**
   * Returns the user's hashed password
   *
   * @author         Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @returns string User's hashed password
   */
  function getPassword(){
    return $this->password;
  }


  /**
   * Sets a user's email address
   *
   * Will fail if the address is formatted incorrectly. Will also
   * fail if checkDuplicate is TRUE and there is another user with the
   * same email address in the system.
   *
   * @author                          Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param   string   email          Email address for the user
   * @param   boolean  checkDuplicate If TRUE, function will see if the email is used by another user.
   * @returns boolean                 TRUE is successful, FALSE otherwise.
   */
  function setEmail($email, $checkDuplicate=TRUE){
    if (!$this->allowEmail($email, $checkDuplicate))
      return FALSE;

    $this->email = $email;
    return TRUE;
  }


  /**
   * Returns a user's email address
   * 
   * The id can be passed if you don't wish to create the object
   * 
   * @author              Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param   integer id  Gets email address of the user specified by the id
   * @returns string      Email address of user
   */
  function getEmail($id=NULL){
    if ($id){
      $user = new PHPWS_User($id);
      return $user->email;
    } else
      return $this->email;
  }


  /**
   * Sets the admin status of a user
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param boolean admin Admin status to set to user
   */
  function setAdmin($admin){
    if ($admin) $this->admin_switch = TRUE;
    else $this->admin_switch = FALSE;
  }

  /**
   * Returns whether an user is an admin or not
   *
   * @author              Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param   integer  id Id number of user (not need if used in object)
   * @returns boolean     Returns TRUE if user is an admin, FALSE otherwise.
   */
  function isAdmin($id=NULL){
    if ($id){
      $user = new PHPWS_User($id);
      if ($user->admin_switch)	return TRUE;
      else return FALSE;
    } else {
      if ($this->admin_switch)	return TRUE;
      else return FALSE;
    }
  }

  /**
   * Sets the deity status of a user
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param boolean admin Admin status to set to user
   */
  function setDeity($deity){
    if ($deity) $this->deity = TRUE;
    else $this->deity = FALSE;
  }

  /**
   * Returns whether an user is an deity or not
   *
   * @author              Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param   integer  id Id number of user (not need if used in object)
   * @returns boolean     Returns TRUE if user is an admin, FALSE otherwise.
   */
  function isDeity($id=NULL){
    if ($id){
      $user = new PHPWS_User($id);
      if ($user->deity)	return TRUE;
      else return FALSE;
    } else {
      if ($this->deity)	return TRUE;
      else return FALSE;
    }
  }


  /**
   * Checks the validity of an email address
   *
   * @author                         Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param   string  email          Email address to test
   * @param   boolean checkDuplicate If TRUE, function returns FALSE if there is a duplicate
   *                                 email address in the system.
   * @returns boolean
   */
  function allowEmail($email, $checkDuplicate=TRUE){
    if (!PHPWS_Text::isValidInput($email, "email")){
      $this->error[] = $_SESSION["translate"]->it("Improperly formatted email address").": <b>$email</b>";
      return FALSE;
    }

    if($checkDuplicate && $GLOBALS['core']->sqlSelect("mod_users", "email", $email)){
      $this->error[] = $_SESSION["translate"]->it("Email address already in use") . ": <b>$email</b>";
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Checks the validity of a username address
   *
   * @author                         Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param   string  email          username to test
   * @param   boolean checkDuplicate If TRUE, function returns FALSE if there is a duplicate
   *                                 username in the system.
   * @returns boolean
   */
  function allowUsername($username, $checkDuplicate=TRUE){
    if (!$GLOBALS["core"]->isValidInput($username)){
      $this->error[] = $_SESSION["translate"]->it("Improperly formatted username").": <b>$username</b>";
      return FALSE;
    }

    if($checkDuplicate && PHPWS_User::getUserId($username)){
      $this->error[] = $_SESSION["translate"]->it("Username already in use").": <b>$username</b>";
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Returns a non terminating error message
   *
   * @author          Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @returns string  Error message from last executed command
   */
  function getError(){
    if (empty($this->error) || !is_array($this->error))
      return FALSE;

    return implode("<br />", $this->error);
  }


  /**
   * Creates or updates a user
   *
   * Make sure you have the various user variables set before calling this
   * function
   *
   * @author          Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @returns boolean TRUE if successful, FALSE otherwise
   */
  function save(){
    if (!isset($this->username) || !isset($this->password)){
      $this->error[] = $_SESSION["translate"]->it("Username and password must be set before user can be saved") . ".";
      return FALSE;
    }

    $sql['username'] = $this->username;
    $sql['password'] = $this->password;
    $sql['email'] = $this->email;

    if ($this->admin_switch)
      $sql['admin_switch'] = 1;
    else
      $sql['admin_switch'] = 0;

    if ($this->deity)
      $sql['deity'] = 1;
    else
      $sql['deity'] = 0;

    if (isset($this->groups)){
      $groups = $this->groups;
      $GLOBALS["core"]->dropNulls($groups);
      $sql['groups'] = implode(":", array_keys($groups));
    }
    
    if ($this->user_id < 1)
      $this->user_id = $GLOBALS['core']->sqlInsert($sql, "mod_users", FALSE, TRUE);
    else
      $GLOBALS['core']->sqlUpdate($sql, "mod_users", "user_id", $this->user_id);

    $this->updateUserGroups();
    return TRUE;
  }


  function listUserErrors(){
    $content = NULL;
    if (isset($GLOBALS["userError"])){
      foreach ($GLOBALS["userError"] as $error)
	$content .= "<span class=\"errortext\">$error</span><br />\n";
    }
    
    unset($GLOBALS["userError"]);

    return $content;
  }

  function setUserPermissions(){
    $permissions = $this->permissions;

    foreach ($permissions as $mod_title=>$rights){
      if (is_null($rights))
	$this->dropUserVar($mod_title);
      else {
	if (is_array($rights))
	  $this->setUserVar($mod_title, implode(":", $rights));
	else
	  $this->setUserVar($mod_title, 1);
      }
    }
  }

  function addUser($username, $password){
    if (!PHPWS_User::allowUsername($username))
      return FALSE;

    $user = new PHPWS_User();
    $user->username = $username;
    $user->password = md5($password);
    $user->writeUser();
    return TRUE;
  }

  function updateUser($username){

  }


  function userAction($mode){
    extract($_POST);
    $this->setFormPermissions();

    if (empty($edit_username))
      $error[] = $_SESSION["translate"]->it("Missing username") . ".";
    elseif (!$GLOBALS["core"]->isValidInput($edit_username))
      $error[] = $_SESSION["translate"]->it("Username may not contain spaces or non-alphanumeric characters") . ".";
    elseif ($mode == "add" && $this->getUserId($edit_username))
      $error[] = $_SESSION["translate"]->it("Username already in use") . ".";

    $this->username = $edit_username;

    if ($pass1 || $pass2){
      if ($passError = $this->checkPassword($pass1, $pass2))
	$error[] = $passError;
      else
	$this->password = md5($pass1);
    } elseif (!$this->password)
	$error[] = $_SESSION["translate"]->it("Missing password");

    if ($email){
      if (!$GLOBALS["core"]->isValidInput($email, "email"))
	$error[] = $_SESSION["translate"]->it("Malformed email address") . ".";
      elseif ($mode == "add" && !$this->check_email($email))
	$error[] = $_SESSION["translate"]->it("Email address already in use") . ".";
    }
    $this->email = $email;
    
    if (isset($admin_switch))
      $this->admin_switch = $admin_switch;
    else
      $this->admin_switch = 0;

    if (isset($deity) && $_SESSION["OBJ_user"]->isDeity())
      $this->deity = 1;
    elseif (isset($deity))
      $this->deity = 0;
    
    if (isset($dropGroup)){
      if ($currentGroups)
	foreach ($currentGroups as $dropId)
	  $this->groups[$dropId] = NULL;

      return NULL;
    } elseif (isset($addGroup)) {
      if ($availableGroups)
	foreach ($availableGroups as $addId)
	  $this->groups[$addId] = PHPWS_USER_GROUPS::getGroupName($addId);
      return NULL;
    }

    if (isset($error)){
      $GLOBALS["userError"] = $error;

      return FALSE;
    } else {

      if ($mode == "add"){
	$this->writeUser();
	$this->setUserPermissions();
	$this->updateUserGroups();
	return TRUE;
      } elseif ($mode == "edit"){
	$groups = $this->groups;
	$GLOBALS["core"]->dropNulls($groups);
	
	if ($groups)
	  $writeGroups = implode(":", array_keys($groups));
	else
	  $writeGroups = NULL;

	if ($_SESSION["OBJ_user"]->isDeity() && $this->deity)
	  $this->deity = 1;
	else
	  $this->deity = 0;

	$sql_array = array ("username"=>$this->username,
			    "password"=>$this->password,
			    "email"=>$this->email,
			    "admin_switch"=>$this->admin_switch,
			    "groups"=>$writeGroups,
			    "deity"=>$this->deity);
	$GLOBALS["core"]->sqlUpdate($sql_array, "mod_users", "user_id", $this->user_id);
	$this->updateUserGroups();
	$this->setUserPermissions();
	return TRUE;
      } else
	exit("Error userAction: Incorrect mode requested.");
    }
  }


  function writeUser(){
    $sql['username'] = $this->username;
    $sql['password'] = $this->password;

    if (isset($this->email))
      $sql['email'] = $this->email;

    if (isset($this->admin_switch))
      $sql['admin_switch'] = $this->admin_switch;
    else
      $sql['admin_switch'] = 0;

    if (isset($this->deity))
      $sql['deity'] = $this->deity;
    else
      $sql['deity'] = 0;

    if (isset($this->groups)){
      $groups = $this->groups;
      $GLOBALS["core"]->dropNulls($groups);
	$sql['groups'] = implode(":", array_keys($groups));
    }

    $user_id = $GLOBALS["core"]->sqlInsert($sql, "mod_users", 1, TRUE);
    if ($user_id){
      $this->user_id = $user_id;
      $this->updateUserGroups();
      return TRUE;
    } else 
      return FALSE;
  }

  function setAdminSwitch($user_id, $state){
    if ($state != 1 && $state != 0)
      return;
    return $GLOBALS["core"]->sqlUpdate(array("admin_switch"=>$state), "mod_users", "user_id", $user_id);
  }

  function accepted_new_submit($data_array){
    extract($data_array);
    if (!($this->send_invitation($username, $email)))
      $GLOBALS["CNT_user"]["content"] .= $_SESSION["translate"]->it("Sorry but there is a problem with email");
  }


  function listUserGroups($user_id=NULL){
    if ($user_id)
      $user = new PHPWS_User((int)$user_id);
    else
      $user = $this;

    return $user->groups;
  }

  function userInGroup($group_id, $user_id=NULL){
    if ($user_id)
      $user = new PHPWS_User((int)$user_id);
    else
      $user = $this;

    return isset($user->groups[(int)$group_id]);
    
  }

  function checkUserPermission($mod_title, $subright=NULL, $checkGroup=NULL){
    if(isset($this->permissions["MOD_".$mod_title]))
      $rights = $this->permissions["MOD_".$mod_title];

    if ($checkGroup && isset($this->groupPermissions["MOD_".$mod_title]))
      if (isset($rights))
	$rights = $rights + $this->groupPermissions["MOD_".$mod_title];
      else
	$rights = $this->groupPermissions["MOD_".$mod_title];

    if (!$subright){
      if (isset($rights))
	return TRUE;
    } elseif (isset($rights) && is_array($rights))
	return in_array($subright, $rights);
    else
      return FALSE;
  }

  function checkPassword($pass1, $pass2="blank"){
    if(preg_match("/[^a-zA-Z0-9!@_]/", $pass1))
      $error=  $_SESSION["translate"]->it("Some characters in your password were refused") . ".";
    elseif ($pass2 != "blank" && $pass1 != $pass2)
      $error = $_SESSION["translate"]->it("Passwords did not match"). ". " .$_SESSION["translate"]->it("Try again") . ".";
    elseif(strlen($pass1) < 5)
      $error = $_SESSION["translate"]->it("Password must be five characters or more in length") . ".";
    elseif(preg_match("/(pass|password|phpwebsite|blank|qwerty|passwd|admin|fallout)/i", $pass1))
      $error = $_SESSION["translate"]->it("Password too familiar") . ".";
    else
      $error = NULL;

    return $error;
  }


  function getSettings(){    
    list(,$settings) = each($GLOBALS["core"]->sqlSelect("mod_user_settings"));
    return $settings;
  }


  function viewUser($id){
    $user = new PHPWS_User($id);

    $template["USERNAME"] = $user->username;
    $template["USER_ID"] = $user->user_id;
    $template["EMAIL"]   = $user->email;
    return $GLOBALS["core"]->processTemplate($template, "users", "viewuser.tpl");
  }




  function submit_new_user($username, $email){
    extract($this->getSettings());

    $GLOBALS["CNT_user"]["title"] = $_SESSION["translate"]->it("Thank you for applying for an account").".";
    if ($user_signup=="hold"){
      $this->RSVP($username, $email);
	$GLOBALS["CNT_user"]["content"] = $_SESSION["translate"]->it("Your request is being reviewed").".";
    } elseif ($user_signup == "send") {
      if ($this->send_invitation($username, $email)){
	$GLOBALS["CNT_user"]["content"] = $_SESSION["translate"]->it("Your account verification is sent").".";
      } else {
	$GLOBALS["CNT_user"]["title"] = $_SESSION["translate"]->it("A Problem Occurred");
	$GLOBALS["CNT_user"]["content"] = $_SESSION["translate"]->it("Sorry but there is a problem with our email").". ".$_SESSION["translate"]->it("Please try again later").".";
      }
    }
  }


  function view_user($user_id){
    $temp_user = new CLS_user;
    $temp_user->loadUser($user_id);

    $table[] = array ("<b>".$_SESSION["translate"]->it("Username")."</b>", $temp_user->username);
    $table[] = array ("<b>".$_SESSION["translate"]->it("Email")."</b>", $GLOBALS["core"]->link("mailto:".$temp_user->email, $temp_user->email, "index"));
    $temp_user->admin_switch ? $answer = $_SESSION["translate"]->it("Yes") : $answer = $_SESSION["translate"]->it("No");
    $table[] = array ("<b>".$_SESSION["translate"]->it("Admin")."?</b>", $answer);
    $content = $GLOBALS["core"]->ezTable($table, 4, 4, 0, "100%");

    return $content;
  }

  function RSVP($username, $email){
    $information = "<b>".$_SESSION["translate"]->it("Username").":</b> $username<br /><b>".$_SESSION["translate"]->it("Email").":</b> $email";
    $newUser = new PHPWS_User;
    $newUser->username = $username;
    $newUser->password = md5($newUser->createPassword());
    $newUser->email = $email;
    $newUser->writeUser();
    PHPWS_Approval::add($newUser->user_id, $information);
  }

  function createPassword(){
    $password = NULL;
    $upper = PHPWS_User::alphabet();
    $lower = PHPWS_User::alphabet("lower");
    $alphabet = array_merge($upper, $lower);
    
    for ($i=0;$i < 11; $i++){
      $key = rand(0,51);
      $password .= $alphabet[$key];
    }

    return $password;
  }

  function welcomeUser($user_id){
    $user = new PHPWS_User($user_id);

    extract(PHPWS_User::getSettings());

    if (!$user_contact){
      echo $_SESSION["translate"]->it("WARNING")."! :". $_SESSION["translate"]->it("A contact email address has not been setup for this website").".";
      return FALSE;
    }

    $password = PHPWS_User::createPassword();
    $update["password"] = md5($password);

    $message = $greeting."\n\n".$_SESSION["translate"]->it("Username")." = " . $user->username . "\n".$_SESSION["translate"]->it("Password")." = $password";

    if(PHPWS_User::mailInvitation($user->email, $message))
      return $GLOBALS["core"]->sqlUpdate($update, "mod_users", "user_id", $user->user_id);
    else
      return FALSE;
  }


  function send_invitation($username, $email){
    if (!$GLOBALS["core"]->isValidInput($username) || !$GLOBALS["core"]->isValidInput($email, "email"))
      exit("send_invitation error: <b>Username: '$username'</b> and/or <b>Email: '$email'</b> are malformed.<br />"); 

    extract($this->getSettings());
    if (!$user_contact){
      echo $_SESSION["translate"]->it("WARNING")."! :". $_SESSION["translate"]->it("A contact email address has not been setup for this website").".";
      return;
    }
    $password = $this->createPassword();

    $message = $greeting."\n\n".$_SESSION["translate"]->it("Username")." = $username\n".$_SESSION["translate"]->it("Password")." = $password";

    if(PHPWS_User::mailInvitation($email, $message)){
      $insert = array ("username"=>$username, "password"=>md5($password), "email"=>$email);
      return $GLOBALS["core"]->sqlInsert($insert, "mod_users", 1);
    } else
      return FALSE;

  }

  function mailInvitation($email, $message){
    extract(PHPWS_User::getSettings());
    return mail($email, $nu_subj, $message, "From: $user_contact");
  }

  function check_email($address){
    if ($GLOBALS["core"]->isValidInput($address, "email")){
      if ($GLOBALS["core"]->sqlSelect("mod_users", "email", $address))
	return FALSE;
      else
	return TRUE;
    } else
      return FALSE;
  }




  function deify($user_id){
    if (list($row) = $GLOBALS["core"]->sqlSelect ("mod_users", "user_id", $user_id))
      extract ($row);

    $GLOBALS["CNT_user"]["title"] = $_SESSION["translate"]->it("Make [var1] a Deity", $username);
    if ($deity){
      $GLOBALS["CNT_user"]["content"] .= "<br />".$_SESSION["translate"]->it("This user is currently a deity").". ".$_SESSION["translate"]->it("Do you wish to remove their status")."?<br /><br />
<a href=\"index.php?module=users&amp;user_op=user_deify&amp;deification=cast_down&amp;user_id=$user_id\">".$_SESSION["translate"]->it("Yes, make them mortal")."</a> &nbsp;&nbsp; <a href=\"index.php?module=users&amp;user_op=user_deify&amp;deification=bestow&amp;user_id=$user_id\">".$_SESSION["translate"]->it("No, let them remain a deity")."</a>";
    } else {
      $GLOBALS["CNT_user"]["content"] .= "<br />".$_SESSION["translate"]->it("This is a mortal").". ".$_SESSION["translate"]->it("Shall we deify them")."?<br /><br />
<a href=\"index.php?module=users&amp;user_op=user_deify&amp;deification=bestow&amp;user_id=$user_id\">".$_SESSION["translate"]->it("Yes, make them a deity")."</a> &nbsp;&nbsp; <a href=\"index.php?module=users&amp;user_op=user_deify&amp;deification=cast_down&amp;user_id=$user_id\">".$_SESSION["translate"]->it("No, do leave them mortal")."</a>";
    }
  }
  
  function switch_admin(){
    if ($_REQUEST["admin"] && $_REQUEST["user_id"]){
      if ($_REQUEST["admin"] == "off")
	$sql = array("admin_switch"=>0);
      elseif ($_REQUEST["admin"]=='on')
	$sql = array("admin_switch"=>1);
      
      $GLOBALS["core"]->sqlUpdate($sql, "mod_users", "user_id", $_REQUEST["user_id"]);
    }
  }


  function force_to_user(){
    header("location:index.php?module=users&user_op=admin");
    exit();
  }

  function getModIcon($icon_name, $mod_directory, $mod_pname){
    $mod_address = "mod/$mod_directory/img/$icon_name";

    if (!$icon_name)
      return NULL;
    return $GLOBALS["core"]->imageTag($GLOBALS["core"]->source_http . $mod_address);
  }


  function forgotPassword($username){
    $settings = $this->getSettings();
    extract($settings);

    if(!$user_id = $this->getUserId($username))
      return FALSE;

    $user = new PHPWS_User;
    $user->loadUser($user_id);

    if (!isset($user->email))
      return FALSE;

    $subject = "Password Change for $username";

    $password = md5($this->createPassword());
    $link = "http://".$GLOBALS["core"]->home_http."index.php?module=users&id=".$user->user_id."&hash=$password&norm_user_op=forgotPasswordForm";
    $message = "A request has been made to change your password.\nClick on the link below to change your password or copy and paste it into your browser.\n\nPassword change address:\n$link";

    if (!empty($user_contact)){
      if (mail($user->email, $subject, $message, "From: $user_contact")){
	$user->setUserVar("forgotHash", $password);
	$user->setUserVar("forgotDateTime", mktime());
	return TRUE;
      }
    }
    
  }

  function update_personal(){
    extract($_POST);
    if (!empty($pass1)){
      if ($errorMessage = $this->checkPassword($pass1, $pass2)){
	$error = new PHPWS_Error("users", "update_personal", $errorMessage);
	$error->errorMessage("CNT_user");
	return FALSE;
      }else
	$personal_upd["password"] = md5($pass1);
    }

    if (!$GLOBALS["core"]->isValidInput($usr_email, "email")){
	$error = new PHPWS_Error("users", "update_personal", "<span class=\"errortext\">".$error."</span><br />");
	$error->errorMessage("CNT_user");

      $GLOBALS["CNT_user"]["content"] .= "<span class=\"errortext\">".$_SESSION["translate"]->it("Malformed email address").".</span><br />";
      return FALSE;
    } else
      $personal_upd["email"] = $usr_email;

    if (isset($loginToList))
      $this->setUserVar("loginToList", 1);
    else
      $this->setUserVar("loginToList", 0);

    return $GLOBALS["core"]->sqlUpdate($personal_upd, "mod_users", "user_id", $this->user_id);
    
  }

  function jsToggle(){
    if ($_POST["js_on"])
      $this->js_on = 1;
    else
      $this->js_on = 0;
  }


  function validate_login($username, $password){
    $GLOBALS['core']->killSession("PHPWS_ControlPanel");
    $settings = $this->getSettings();
    extract($settings);
    $authed = FALSE;
    $usr_log_atmpt = 0;

    if ($GLOBALS["core"]->isValidInput($username)) {
      $hash_pass = md5($password);
      if ($user_authentication=="external") {
	//attempt authorization via "ldap"
	if (file_exists($GLOBALS["core"]->source_dir."mod/users/".$external_auth_file)) {
	  require_once($GLOBALS["core"]->source_dir."mod/users/".$external_auth_file);
	  if (($auth_group=authorize($username,$password))!==FALSE) {
	    $insert = array ("username"=>$username, "password"=>md5($password), "email"=>$username."@appstate.edu");
	    $id = $GLOBALS["core"]->getOne("select user_id from ".$GLOBALS["core"]->tbl_prefix."mod_users where username='$username'", TRUE);
	    if ($id)
	      $GLOBALS["core"]->sqlUpdate($insert, "mod_users", "user_id", $id);
	    else
	      $GLOBALS["core"]->sqlInsert($insert, "mod_users");

	    $usr_log_atmpt = NULL;
	    $id = $GLOBALS["core"]->getOne("select user_id from ".$GLOBALS["core"]->tbl_prefix."mod_users where username='$username' and password='$hash_pass'", TRUE);
	    $this->loadUser($id, 1);
	    
	    if ($auth_group!==TRUE) {
	      $result=$GLOBALS["core"]->getOne("SELECT group_id FROM ".$GLOBALS["core"]->tbl_prefix."mod_user_groups WHERE group_name='$auth_group'", TRUE);
	      $this->temp_var[$result]=TRUE;
	    }
	    $authed=true;
	  }
	} else {
	  $GLOBALS["CNT_user"]["content"] .= $_SESSION["translate"]->it("External Authorization is set on site, but authorization file doesn't exist. Please notify system administrator.")."<br />";
	  $no_redirect=true;
	}
      }
      
      if ($user_authentication=="local" || $authed!==true) {
	$id = $GLOBALS["core"]->getOne("select user_id from mod_users where username='$username' and password='$hash_pass'", TRUE);
	if($id){
	  $this->loadUser($id, 1);

	  if (strstr($_SERVER["HTTP_REFERER"], "norm_user_op=signup"))
	    $location = ".";
	  elseif ($this->getUserVar("loginToList") == 1 && !strstr($_SERVER["HTTP_REFERER"], 'module='))
	    $location = "index.php?module=controlpanel";
	  else {
	    $location = str_replace("http://" . PHPWS_HOME_HTTP, "", $_SERVER["HTTP_REFERER"]);
	    if (empty($location))
	      $location = ".";
	  }

	  header("location:" . $location );
	  exit();
	}
      }
      
      if ($authed != TRUE || !$no_redirect) {
	if (isset($usr_log_atmpt) && $usr_log_atmpt >= $max_log_attempts){
	  $GLOBALS["core"]->unauthorized(5, "users");
	}
	$usr_log_atmpt++;

	header("location:index.php?module=users&norm_user_op=signup&username=$username");
	exit();
      }
    }
    else {
      if ($settings["user_signup"] != "none")
	header("location:index.php?module=users&norm_user_op=signup&username=$username");
      else 
	header("location:index.php");

      exit();
    }
  }


  function update_user(){
    $sql_array = array ("username"=>$this->username, "password"=>$this->password, "email"=>$this->email, "admin_switch"=>$this->admin_switch);
    $GLOBALS["core"]->sqlUpdate($sql_array, "mod_users", "user_id", $this->user_id);

  }
  
  function deleteUser($user_id, $confirm=NULL){
    if ($user_id)
      $user = new PHPWS_User($user_id);
    else
      return;

    if (is_null($confirm)){
      if ($user->username == $_SESSION["OBJ_user"]->username)
	$GLOBALS["CNT_user"]["content"] = $_SESSION["translate"]->it("You sure you want to delete your OWN account")."?!<br />".$_SESSION["translate"]->it("You won't be able to log in afterwards").".&nbsp;&nbsp;";
      else
	$GLOBALS["CNT_user"]["content"] = $_SESSION["translate"]->it("Are you sure you want to delete this user")."?&nbsp;&nbsp;";
      
      $GLOBALS["CNT_user"]["content"] .= $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Yes"), "users", array("user_op"=>"deleteUser", "confirm"=>"yes", "user_id"=>$user->user_id))
	 . "&nbsp;&nbsp;". $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("No"), "users", array("user_op"=>"panelCommand", "usrCommand[user]"=>"edit")). "<br /><br />";
      $GLOBALS["CNT_user"]["content"] .= "<b>".$_SESSION["translate"]->it("Username").":</b> $user->username<br />";
      return;
    } elseif ($confirm == "yes"){
      PHPWS_User::dropUser($user_id);
      PHPWS_User::removeUserFromGroups($user_id);
      $GLOBALS["core"]->sqlDelete("mod_users", "user_id", $user->user_id);
      $GLOBALS["CNT_user"]["content"] .= $_SESSION["translate"]->it("User deleted");
    }
    $this->manageUsers();
  }


  function allow_access($mod_title, $subright=NULL){
    if ($this->isDeity())
      return TRUE;

    if ($this->admin_switch)
      if ($this->checkUserPermission($mod_title, $subright, TRUE))
	return TRUE;

    return FALSE;
  }

  function userMenu(){
    $template["MODULES"] = $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Control Panel"), "controlpanel");
    $template["LOGOUT"] = $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Log Out"), "users", array("norm_user_op"=>"logout"));
    $template["HOME"] = $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Home"));

    return $GLOBALS["core"]->processTemplate($template, "users", "usermenus/Default.tpl");

  }

  function isUser(){
    if ($this->user_id > 0)
      return TRUE;
    else
      return FALSE;
  }

  function logout(){
    $GLOBALS["core"]->killAllSessions();
    header("location:index.php");
    exit();
  }

  function signupAction(){
    if (isset($_POST["usr_login"])){
      if (isset($_POST["login_username"]) && isset($_POST["password"]))
	$_SESSION["OBJ_user"]->validate_login($_POST["login_username"], $_POST["password"]);
      elseif (isset($_POST["login_username"]))
	$_SESSION["OBJ_user"]->signup_user();
    } elseif (isset($_POST["signup_request"])){
      $settings = $_SESSION["OBJ_user"]->getSettings();
      if ($settings["user_signup"] != "none"){

	if ($_SESSION["OBJ_user"]->allowUsername($_POST["signup_username"])){
	  if ($_SESSION["OBJ_user"]->check_email($_POST["usr_new_email"])){
	    $_SESSION["OBJ_user"]->submit_new_user($_POST["signup_username"], $_POST["usr_new_email"]);
	    $_POST = NULL;
	  } else {
	    $GLOBALS["CNT_user"]["content"] .= "<span class=\"errortext\">".$_SESSION["translate"]->it("Your username and email combination was refused").".<br /> "
	       . $_SESSION["translate"]->it("Please try another") . ".<br />"
	       . $_SESSION["translate"]->it("If you think you might already have an account, try the lost password option") . ".</span>";
	    $_SESSION["OBJ_user"]->signup_user();
	  }
	} else {
	  $GLOBALS["CNT_user"]["content"] .= "<span class=\"errortext\">".$_SESSION["translate"]->it("Your username and email combination was refused").".<br />"
	     . $_SESSION["translate"]->it("Please try another") . ".<br />"
	     . $_SESSION["translate"]->it("If you think you might already have an account, try the lost password option") . ".</span>";
	  $_SESSION["OBJ_user"]->signup_user();
	}
      }
    } elseif (isset($_POST["forgotPW"])){
      if (!$GLOBALS["core"]->isValidInput($_POST["forgot_username"])){
	$GLOBALS["CNT_user"]["content"] .= "<span class=\"errortext\">".$_SESSION["translate"]->it("The username you have entered in invalid").".</span>";
	$_SESSION["OBJ_user"]->signup_user();
      } elseif (!($user_id = $_SESSION["OBJ_user"]->getUserId($_POST["forgot_username"]))){
	$GLOBALS["CNT_user"]["content"] .= "<span class=\"errortext\">".$_SESSION["translate"]->it("The username you have entered in invalid").".</span>";
	$_SESSION["OBJ_user"]->signup_user();
      } elseif ($_SESSION["OBJ_user"]->isDeity($user_id)) {
	$GLOBALS["CNT_user"]["content"] .= "<span class=\"errortext\">".$_SESSION["translate"]->it("The username you have entered is not permitted to change their password").".</span>";
	$_SESSION["OBJ_user"]->signup_user();
      } elseif (!$_SESSION["OBJ_user"]->forgotPassword($_POST["forgot_username"])){
	$GLOBALS["CNT_user"]["content"] .= "<span class=\"errortext\">".$_SESSION["translate"]->it("Unable to email change form").". ".$_SESSION["translate"]->it("Please contact the systems administrator").".</span>";
	$_SESSION["OBJ_user"]->signup_user();
      } else {
	$GLOBALS["CNT_user"]["title"]   = $_SESSION["translate"]->it("Request Successful")."!";
	$GLOBALS["CNT_user"]["content"] .= $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Home")) . "<br /><br />" . $_SESSION["translate"]->it("Please check your email for the change of password form").".";
      }
    }

  }


  /**
   * Creates an array of the English alphabet
   *
   * If '$letter_case' is lower then the character set
   * will be lowercase. If it is NULL, then uppercase.
   * Needs internationalization
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string $letter_case Indicates to return an uppercase or lowercase array
   * @return array  $ret_array   Numerically indexed array of alphabet
   * @access public
   */
  function alphabet($letter_case=NULL) {
    if ($letter_case == "lower") {
      $start = ord("a");
      $end = ord("z");
    } else {
      $start = ord("A");
      $end = ord("Z");
    }
    
    for ($i=$start;$i<=$end;$i++)
      $ret_array[] = chr($i);

    return $ret_array;
  }// END FUNC alphabet()


  function setJS($JS){
    if ($JS === NULL)
      return FALSE;
    elseif ($JS)
      $this->js_on = 1;
    else
      $this->js_on = 0;

    return TRUE;
  }

}

?>