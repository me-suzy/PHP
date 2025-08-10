<?php

/**
 * Conversion Script for the menu
 *
 * Converts a menu from 0.8.x to the 0.9.x version
 * This DOES NOT include any modded menus 
 */

/* Security Check **********************************/
$correctPath = $core->source_dir . "convert/index.php";
if($_SERVER['SCRIPT_FILENAME'] != $correctPath) {
  header("Location: ../index.php");
  exit();
}
/***************************************************/

$count = $core->db->nextId($core->tbl_prefix . "mod_menuman_items");
$menuOrder0 = 1;
$menuOrder1 = 1;
$menuOrder2 = 1;

$db = old_connect();
$result = $db->query("SELECT menu_id, menu_text, menu_url, menu_order, page_id, menu_active FROM " . OLD_PREFIX . "menu WHERE menu_level = 1 ORDER BY menu_order");
while($row = $result->fetchrow(DB_FETCHMODE_ASSOC)) {
  $dispKey = checkUrl($row['menu_url'], $row['page_id']);
  $saveArray[$count] = array("menu_item_id"=>"$count",
			     "menu_item_pid"=>"$count",
			     "menu_item_title"=>$row['menu_text'],
			     "menu_item_url"=>$row['menu_url'],
			     "menu_item_order"=>$menuOrder0,
			     "menu_item_active"=>$row['menu_active'],
			     "display_key"=>$dispKey);
  $parentId0 = $count;
  $count++;
  $menuOrder0++;
  $low_sub = $row['menu_id'] * 100;
  $high_sub = $low_sub + 99;
  $result_sub = $db->query("SELECT menu_id, menu_text, menu_url, menu_order, page_id, menu_active FROM " . OLD_PREFIX . "menu WHERE menu_level = 2 && menu_id >= $low_sub && menu_id <= $high_sub ORDER BY menu_order");
  while($row = $result_sub->fetchrow(DB_FETCHMODE_ASSOC)) {
    $dispKey = checkUrl($row['menu_url'], $row['page_id']);
    $saveArray[] = array("menu_item_id"=>"$count",
			 "menu_item_pid"=>"$parentId0",
			 "menu_item_title"=>$row['menu_text'],
			 "menu_item_url"=>$row['menu_url'],
			 "menu_item_order"=>$menuOrder1,
			 "menu_item_active"=>$row['menu_active'],
			 "display_key"=>$dispKey);
    $parentId1 = $count;
    $count++;
    $menuOrder1++;
    $low_sub = $row['menu_id'] * 100;
    $high_sub = $low_sub + 99;
    $result_sub2 = $db->query("SELECT menu_id, menu_text, menu_url, menu_order, page_id, menu_active FROM " . OLD_PREFIX . "menu WHERE menu_level = 3 && menu_id >= $low_sub && menu_id <= $high_sub ORDER BY menu_order");
    while($row = $result_sub2->fetchrow(DB_FETCHMODE_ASSOC)) {
      $dispKey = checkUrl($row['menu_url'], $row['page_id']);
      $saveArray[] = array("menu_item_id"=>"$count",
			   "menu_item_pid"=>"$parentId1",
			   "menu_item_title"=>$row['menu_text'],
			   "menu_item_url"=>$row['menu_url'],
			   "menu_item_order"=>$menuOrder2,
			   "menu_item_active"=>$row['menu_active'],
			   "display_key"=>$dispKey);
      $count++;
      $menuOrder2++;
    }
    $menuOrder2 = 1;
  }
  $menuOrder1 = 1;
}

function checkUrl(&$menu_url, $page_id) {
  if(preg_match("/:\/\//i", $menu_url)) {
    return 3;
  } else {
    if(preg_match("/mod\.php\?mod=userpage/", $menu_url)) {
      $id = $GLOBALS['page_ids'][$page_id];
      $menu_url = "./index.php?module=pagemaster&amp;PAGE_user_op=view_page&amp;PAGE_id=" . $id;
      return 1;
    } else if($menu_url == "site_map.php") {
      $menu_url = "./index.php?module=menuman&amp;MMN_menuman_op=siteMap";
      return 1;
    } else if($menu_url == "submit.php") {
      $menu_url = "./index.php?module=announce&amp;ANN_user_op=submit_announcement";
      return 1;
    } else {
      return 1;
    }
  } 
}

$layout = new PHPWS_Layout;

$saveAllow = $core->listModules();
array_push($saveAllow, "home");
$saveAllow = addslashes(serialize($saveAllow));

//echo PHPWS_Debug::testArray($saveArray);
$time = time();

$menuArray = array("menu_title"=>"Main Menu",
		   "menu_spacer"=>3,
		   "menu_active"=>1,
		   "menu_indent"=>"none",
		   "indent_key"=>"",
		   "color_key"=>"",
		   "menu_image"=>"",
		   "active_image"=>"",
		   "open_image"=>"",
		   "horizontal"=>"FALSE",
		   "image_map"=>"",
		   "template"=>"default",
		   "allow_view"=>"$saveAllow",
		   "updated"=>"$time"
		   );

//echo "MENU<br />" . PHPWS_Debug::testArray($menuArray);

$maxId = $core->sqlInsert($menuArray, "mod_menuman_menus", FALSE, TRUE, FALSE);
$contentVar = "CNT_menuman_" . $maxId;
$extraArray = array("content_var"=>"$contentVar");
$core->sqlUpdate($extraArray, "mod_menuman_menus", "menu_id", $maxId);

$layout->create_temp("menuman", $contentVar, "left_col_top");

foreach($saveArray as $key => $value) {
  $saveArray[$key]['menu_id'] = $maxId;
}

//echo "MENU ITEMS<br />" . PHPWS_Debug::testArray($saveArray);

foreach($saveArray as $key => $value) {
  $core->sqlInsert($value, "mod_menuman_items", FALSE, FALSE, FALSE, FALSE);
}

$core->db->nextId($core->tbl_prefix . "mod_menuman_items");
$core->sqlLock(array("mod_menuman_items"=>"WRITE", "mod_menuman_items_seq"=>"WRITE"));
$maxId = $core->sqlMaxValue("mod_menuman_items", "menu_item_id");
$core->query("UPDATE " . $core->tbl_prefix . "mod_menuman_items_seq SET id='$maxId'");
$core->sqlUnlock();

echo "<h3>Menu Conversion Complete!</h3>";

?>
