<?php
/* Security Check **********************************/
$correctPath = $core->source_dir . "convert/index.php";
if($_SERVER['SCRIPT_FILENAME'] != $correctPath) {
  header("Location: ../index.php");
  exit();
}
/***************************************************/

$imageDir = OLD_SITE_DIR . "images/topics/";

// Leave these directories as is
$fatcatDir = PHPWS_HOME_DIR . "images/fatcat/icons/";

/* Connect to old database */
$db = old_connect();

/* Get all old topics */
$topic_result = $db->getAll("SELECT * FROM " . OLD_PREFIX . "topics order by topicparent ASC");

if(DB::isError($topic_result)) {
  echo PHPWS_Debug::testObject($topic_result);
  exit("Error selecting from old topics table in database " . OLD_DBNAME . "!");
}

/* Connect to new database */
$db = new_connect();

$topic_insert = $db->prepare("INSERT INTO " . NEW_PREFIX . "mod_fatcat_categories VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

/* Check to see if any topics were found */
if(count($topic_result)) {
  /* Loop through topics and insert them into the new database */
  foreach($topic_result as $row) {
    $imageSize = $fileDir = NULL;
    if (!($topic_id = duplicateFatcat($row['topicname']))){
      /* Get next available topic id */
      $topic_id = $db->nextId(NEW_PREFIX . "mod_fatcat_categories");

      /* Prepare data array for insertion into database */
      $topic_data = array();
      $topic_data[] = $topic_id;

      $title = $row["topictext"];
      if (strlen($row["topicname"]))
	$title .= ": " . $row["topicname"];

      $topic_data[] = $title;

      $topic_data[] = $row["topiclongtext"];
      $topic_data[] = "default.tpl";
      $topic_data[] = NULL;

      if (!empty($row['topicimage'])){
	$fileDir = $imageDir . $row['topicimage'];
	if (file_exists($fileDir)){
	  if (isset($holdSize[$row["topicimage"]]))
	    $imageSize = $holdSize[$row["topicimage"]];
	  else {
	    $imageSize = getimagesize($fileDir);
	    $holdSize[$row["topicimage"]] = array($imageSize[0], $imageSize[1]);
	  }
	  if (($imageSize[0] + $imageSize[1]) > 2){
	    $topic_data[] = $row["topicimage"] . ":" . $imageSize[0]  . ":" . $imageSize[1]; //image
	    $core->fileCopy($fileDir, $fatcatDir, $row['topicimage'], 1, 1);
	  } else
	    $topic_data[] = NULL;
	} else
	  $topic_data[] = NULL;
      } else
 	  $topic_data[] = NULL;

      if ($row["topicparent"]){
	$parent = $GLOBALS["topicID"][$row["topicparent"]];
	$topic_data[] = $parent;
	if (is_null($childrenList) || !in_array($topic_id, $childrenList[$parent]))
	  $childrenList[$parent][] = $topic_id;
      } else
	$topic_data[] = 0;

      $topic_data[] = NULL;

      $result = $db->execute($topic_insert, $topic_data);

      if (is_array($childrenList))
	foreach ($childrenList as $parent_id=>$childArray)
	  $db->query("update " . NEW_PREFIX . "mod_fatcat_categories set children='" . implode(":", $childArray) . "' where cat_id=$parent_id");

      /* Make sure everything got inserted correctly */
      if(DB::isError($result)) {
	echo PHPWS_Debug::testObject($result);
	exit("There was a problem inserting topic data into the database " . NEW_DBNAME . "!");
      }
    }
    /* GLOBAL topics for announcements */
    $GLOBALS["topicID"][$row["topicid"]] = $topic_id;
  }// END TOPIC LOOP

} else {
  echo "No topics found for conversion!<br />";
}

echo "<h3>Topics Conversion Complete!</h3>";

?>
