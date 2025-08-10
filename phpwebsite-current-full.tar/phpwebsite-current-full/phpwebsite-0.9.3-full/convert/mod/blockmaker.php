<?php

/**
 * Conversion Script For Blocks
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


class block {
  var $title;
  var $content;
}

$db = old_connect();
$count = 0;
$result = $db->query("SELECT * FROM " . OLD_PREFIX . "core_blocks ORDER BY block_lorder, block_rorder");

while($row = $result->fetchrow(DB_FETCHMODE_ASSOC)) {
  $block = unserialize($row['data']);
  
  $blockArray[$count]['data'] = array("block_title"=>$block->title,
				     "block_content"=>$block->content,
				     "block_footer"=>"",
				     "block_active"=>$row['block_status']);
  $blockArray[$count]['location'] = $row['block_location'];
  $count++;
}

$layout = new PHPWS_Layout;

$saveAllow = $core->listModules();
array_push($saveAllow, "home");
$saveAllow = addslashes(serialize($saveAllow));

foreach($blockArray as $key => $value) {
  $blockArray[$key]['data']['allow_view'] = $saveAllow;
}

foreach($blockArray as $key => $value) {
  $maxId = $core->sqlInsert($blockArray[$key]['data'], "mod_blockmaker_data", FALSE, TRUE, FALSE);

  $contentVar = "CNT_blockmaker_" . $maxId;
  $extraArray = array("content_var"=>"$contentVar");
  $core->sqlUpdate($extraArray, "mod_blockmaker_data", "block_id", $maxId);

  switch($blockArray[$key]['location']) {
  case "L":
    $location = "left_col_bottom";
    break;
  case "R":
    $location = "right_col_bottom";
    break;
  } 

  $layout->create_temp("blockmaker", $contentVar, $location);
}

echo "<h3>Blockmaker Conversion Complete!</h3>";

?>