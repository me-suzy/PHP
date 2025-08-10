<?php

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

$status = 1;

if($currentVersion < 1.10) {
  $content .= "Updating Announce Module to Version 1.10<br />";
  $content .= "Adding \"poston\" column to the \"mod_announce\" table.<br />";
  $sql = "ALTER TABLE " . $GLOBALS["core"]->tbl_prefix . "mod_announce ADD poston datetime NOT NULL";
  $GLOBALS["core"]->query($sql);
  $content .= "Column added successfully.<br />";
  $content .= "Setting \"post on\" dates for all announcements to their date created.<br />";

  $sql = "SELECT id FROM " . $GLOBALS["core"]->tbl_prefix . "mod_announce";
  $result = $GLOBALS["core"]->getAll($sql);
  if(sizeof($result) > 0) {
    $i = 0;
    foreach($result as $row) {
      $sql = "UPDATE " . $GLOBALS["core"]->tbl_prefix . "mod_announce SET poston=dateCreated WHERE id=" .
	 $row["id"];
      $GLOBALS["core"]->query($sql);
      $i++;
    }

    $content .= $i . " announcements were updated!<br />";
  } else {
    $content .= "No announcements were found. Skipping this step.<br />";
  }

  $content .= "Update successful!";
}

?>