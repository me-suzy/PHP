<?php
/* Security Check **********************************/
$correctPath = $core->source_dir . "convert/index.php";
if($_SERVER['SCRIPT_FILENAME'] != $correctPath) {
  header("Location: ../index.php");
  exit();
}
/***************************************************/

/* Connect to old database */
$db = old_connect();

/* Get all old users */
$admin_result = $db->getAll("SELECT * FROM " . OLD_PREFIX . "authors");
$user_result = $db->getAll("SELECT * FROM " . OLD_PREFIX . "users");

if(DB::isError($admin_result)) {
  echo PHPWS_Debug::testObject($admin_result);
  exit("Error selecting from old author table in database " . OLD_DBNAME . "!");
}

if(DB::isError($user_result)) {
  echo PHPWS_Debug::testObject($user_result);
  exit("Error selecting from old user table in database " . OLD_DBNAME . "!");
}

/* Connect to new database */
$db = new_connect();
$user_insert = $db->prepare("INSERT INTO " . NEW_PREFIX . "mod_users VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");


/* Check to see if any admins were found */
if(count($admin_result)) {
  /* Loop through author and insert them into the new database */
  foreach($admin_result as $row) {
    $sql = "select * from " . NEW_PREFIX . "mod_users where username='" . $row["aid"] . "' or email='" . $row["email"] . "'";
    $found = $db->getAll($sql, DB_FETCHMODE_ASSOC);
    if(DB::isError($found))
      exit("ERROR: Users: There was a problem accessing your v0.9.x database! " . NEW_DBNAME);

    if(is_array($found) && count($found)>0) {
      $duplicates[] = $row["uname"] . " -- " . $row["email"];
      continue;
    }

    /* Get next available user id */
    $user_id = $db->nextId(NEW_PREFIX . "mod_users");

    /* Prepare data array for insertion into database */
    $admindata = array();
    $admindata[] = $user_id;
    $admindata[] = $row["aid"];     //username
    $admindata[] = $row["pwd"];     //password
    $admindata[] = $row["email"];   //email
    $admindata[] = 1;               //admin_switch
    $admindata[] = NULL;            //groups
    $admindata[] = 0;               //deity
    $admindata[] = $row["counter"]; //log_sess
    $admindata[] = 0;               //last_on
    

    $result = $db->execute($user_insert, $admindata);

    /* Make sure everything got inserted correctly */
    if(DB::isError($result)) {
      echo PHPWS_Debug::testObject($result);
      exit("There was a problem inserting user data into the database " . NEW_DBNAME . "!");
    }
  }// END USER LOOP

} else {
  echo "No admins found for conversion!<br />";
}


/* Check to see if any users were found */
if(count($user_result)) {
  /* Loop through user and insert them into the new database */
  foreach($user_result as $row) {
    $sql = "select * from " . NEW_PREFIX . "mod_users where username='" . $row["uname"] . "' or email='" . $row["email"] . "'";
    $found = $db->getAll($sql, DB_FETCHMODE_ASSOC);
    if(DB::isError($found))
      exit("ERROR: Users: There was a problem accessing your v0.9.x database! " . NEW_DBNAME);

    if(is_array($found) && count($found)>0) {
      $duplicates[] = $row["uname"] . " -- " . $row["email"];
      continue;
    }

    /* Get next available user id */
    $user_id = $db->nextId(NEW_PREFIX . "mod_users");
    
    /* Prepare data array for insertion into database */
    $userdata = array();
    $userdata[] = $user_id;
    $userdata[] = $row["uname"];
    $userdata[] = $row["pass"];
    $userdata[] = $row["email"];
    $userdata[] = 0;
    $userdata[] = NULL;
    $userdata[] = 0;
    $userdata[] = $row["counter"];
    $userdata[] = 0;
    

    $result = $db->execute($user_insert, $userdata);
    /* Make sure everything got inserted correctly */
    if(DB::isError($result)) {
      echo PHPWS_Debug::testObject($result);
      exit("There was a problem inserting user data into the database " . NEW_DBNAME . "!");
    }
  }// END USER LOOP

} else {
  echo "No users found for conversion!<br />";
}

echo "<h3>Users Conversion Complete!</h3>";
if ($duplicates){
  echo "<b>The following users were not entered as they were identical to an admin account:</b><br />";
  foreach ($duplicates as $user)
    echo $user . "<br />";

}
?>
