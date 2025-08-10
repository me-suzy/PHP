<?php

/**
 * Conversion Script For Links
 *
 * Converts 0.8.x blocks to 0.9.x blocks
 */

/* Security Check **********************************/
$correctPath = $core->source_dir . "convert/index.php";
if($_SERVER['SCRIPT_FILENAME'] != $correctPath) {
  header("Location: ../index.php");
  exit();
}
/***************************************************/

$db = old_connect();
$count = 0;
$result = $db->query("SELECT * FROM " . OLD_PREFIX . "mod_weblink_data");

if (in_array("fatcat", $currentModules)){
  $oldCats = $db->getAll("SELECT * FROM " . OLD_PREFIX . "mod_weblink_cat");
  
  if ($oldCats){
    foreach ($oldCats as $oldInfo){
      if (!($cat_id = duplicateFatcat($oldInfo['cat']))){
	if ($oldInfo['cid'] != $oldInfo['pid'])
	  $cat_id =  PHPWS_Fatcat::createCategory($oldInfo['cat'], $oldInfo['description'], NULL, NULL, NULL, $updateCatId[$oldInfo['pid']]);
	else
	  $cat_id =  PHPWS_Fatcat::createCategory($oldInfo['cat'], $oldInfo['description']);
      }
      $updateCatId[$oldInfo['cid']] = $cat_id;
      $catTitles[$oldInfo['cat']] = $cat_id;
    }
  }
}

while($row = $result->fetchrow(DB_FETCHMODE_ASSOC)) {
  $linkArray = array("title"=>$row['title'],
		       "url"=>$row['url'],
		       "description"=>$row['description'],
		       "username"=>$row['name'],
		       "userEmail"=>$row['email'],
		       "datePosted"=>$row['date'],
		       "active"=>1,
		       "hits"=>$row['hits'],
		       "new"=>0
		       );
  if ($maxID = $core->sqlInsert($linkArray, "mod_linkman_links", FALSE, TRUE)){
    if (in_array("fatcat", $currentModules) && $row['category']){
      PHPWS_Fatcat_Elements::saveElement($catTitles[$row['category']], $row['title'], "index.php?module=linkman&LMN_op=visitLink&LMN_id=$maxID", $maxID, "linkman");
    }
  }
}


echo "<h3>Link Manager Conversion Complete!</h3>";

?>