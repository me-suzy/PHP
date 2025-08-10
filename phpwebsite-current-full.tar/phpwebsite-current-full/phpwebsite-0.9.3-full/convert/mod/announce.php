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

/* Get all old announcements */
$ann_result = $db->getAll("SELECT * FROM " . OLD_PREFIX . "stories");

if(DB::isError($ann_result)) {
  echo PHPWS_Debug::testObject($ann_result);
  exit("Error selecting from old announcements table in database " . OLD_DBNAME . "!");
}

/* Connect to new database */
$db = new_connect();

$ann_insert = $db->prepare("INSERT INTO " . NEW_PREFIX . "mod_announce VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$element_insert = $db->prepare("INSERT INTO " . NEW_PREFIX . "mod_fatcat_elements VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

/* Check to see if any announcements were found */
if(count($ann_result)) {
  /* Loop through announcement and insert them into the new database */
  foreach($ann_result as $row) {
    /* Get next available announcement id */
    $ann_id = $db->nextId(NEW_PREFIX . "mod_announce");

    /* Update changed ids array for use by comments conversion */
    $GLOBALS["ann_ids"][$row["sid"]] = $ann_id;

    /* Prepare data array for insertion into database */
    $ann_data = array();
    $ann_data[] = $ann_id;
    $ann_data[] = $row["title"];
    $ann_data[] = $row["hometext"];
    $ann_data[] = $row["bodytext"];
    $ann_data[] = NULL;
    $ann_data[] = $row["counter"];
    $ann_data[] = 1;
    $ann_data[] = 1;
    $ann_data[] = 1;
    $ann_data[] = 1;
    $ann_data[] = $row["aid"];
    $ann_data[] = $row["aid"];
    $ann_data[] = $row["time"];
    $ann_data[] = $row["time"];
    $ann_data[] = date("Y-m-d H:i:s");
    $ann_data[] = $row["exp_date"];

    $result = $db->execute($ann_insert, $ann_data);

    /* Make sure everything got inserted correctly */
    if(DB::isError($result)) {
      echo PHPWS_Debug::testObject($result);
      exit("There was a problem inserting announcement data into the database " . NEW_DBNAME . "!");
    }



    if ($row["topic"] && in_array("fatcat", $currentModules)){
      $element_id = $db->nextId(NEW_PREFIX . "mod_fatcat_elements");

      $element_data    = array();
      $element_data[]  = $element_id;
      $element_data[]  = $GLOBALS["topicID"][$row["topic"]];
      $element_data[]  = $row["title"];
      $element_data[]  = "index.php?module=announce&ANN_user_op=view&ANN_id=$ann_id";
      $element_data[]  = $ann_id;
      $element_data[]  = "announce";
      $element_data[]  = "home";
      $element_data[]  = 50;
      $element_data[]  = 1;
      $element_data[]  = NULL;
      $element_data[]  = date("Ymd");

      $element = $db->execute($element_insert, $element_data);
    }
  }// END ANNOUNCE LOOP

} else {
  echo "No announcements found for conversion!<br />";
}

echo "<h3>Announcements Conversion Complete!</h3>";

writeReroute("announce", $GLOBALS["ann_ids"]);

?>
