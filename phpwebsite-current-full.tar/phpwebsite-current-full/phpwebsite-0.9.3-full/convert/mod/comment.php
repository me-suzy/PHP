<?php

/**
 * Conversion Script For Comments
 *
 * Converts comments fro the 0.8.x series to 0.9.x blocks
 */

/* Security Check **********************************/
$correctPath = $core->source_dir . "convert/index.php";
if($_SERVER['SCRIPT_FILENAME'] != $correctPath) {
  header("Location: ../index.php");
  exit();
}
/***************************************************/

$GLOBALS['db'] = $db = old_connect();
$count = 0;

foreach($GLOBALS['ann_ids'] as $oldId => $newId) {
  $key = "article" . $oldId;

  $result0 = $db->query("SELECT * FROM " . OLD_PREFIX . "mod_comments_data WHERE parent='0' AND mid='$key' ORDER BY mid");
  while($comment0 = $result0->fetchrow(DB_FETCHMODE_ASSOC)) {
    
    /* if(preg_match("/article/", $comment0['mid'])) {
      $itemId = str_replace("article", "", $comment0['mid']);
      $itemId = $GLOBALS['ann_ids'][$itemId];
      $module = "article";
    } else {
      $itemId = str_replace("poll", "", $comment0['mid']);
      $module = "poll";
    } */

    $count++; 
    $oldParent = $comment0['cid'];
    $mid = $comment0['mid'];
    $module = "announce";
    
    $sub = str_replace("\\", "", $comment0['subject']);
    $com = str_replace("\\", "", $comment0['comment']);
    $rea = str_replace("\\", "", $comment0['edit_reason']);
    
    $sub = addslashes($sub);
    $com = addslashes($com);
    $rea = addslashes($rea);

    if(!$comment0['name']) {
      $anon = 1;
      $author = "Anonymous";
    } else if($comment0['anonymous'] == "on" || $comment0['anonymous'] == 1){
      $anon = 1;
      $author = $comment0['name'];
    } else {
      $anon = 0;
      $author = $comment0['name'];
    }
    
    $commentArray[] = array("cid"=>$count,
			 "pid"=>$nullVar,
			 "module"=>$module,
			 "itemId"=>$newId,
			 "subject"=>$sub,
			 "comment"=>$com,
			 "author"=>$author,
			 "authorIp"=>$comment0['ip'],
			 "postDate"=>$comment0['date'],
			 "editor"=>$comment0['editor'],
			 "editReason"=>$rea,
			 "score"=>$comment0['score'],
			 "anonymous"=>$anon);
    
    getChildren($count, $oldParent, $count, $mid, $commentArray, $newId);
  }
}

function getChildren(&$count, $oldParent, $newParent, $mid, &$commentArray, $newId) {
  $result1 = $GLOBALS['db']->query("SELECT * FROM " . OLD_PREFIX . "mod_comments_data WHERE parent='$oldParent' AND mid='$mid'");
  while($comment1 = $result1->fetchrow(DB_FETCHMODE_ASSOC)) {
    
    /* if(preg_match("/article/", $comment1['mid'])) {
      $itemId = str_replace("article", "", $comment1['mid']);
      $itemId = $GLOBALS['ann_ids'][$itemId];
      $module = "article";
    } else {
      $itemId = str_replace("poll", "", $comment1['mid']);
      $module = "poll";
    } */

    $count++;
    $oldParent = $comment1['cid'];
    $mid = $comment1['mid'];
    $module = "announce";

    $sub = str_replace("\\", "", $comment1['subject']);
    $com = str_replace("\\", "", $comment1['comment']);
    $rea = str_replace("\\", "", $comment1['edit_reason']);
    
    $sub = addslashes($sub);
    $com = addslashes($com);
    $rea = addslashes($rea);
    
    if(!$comment1['name']) {
      $anon = 1;
      $author = "Anonymous";
    } else if($comment1['anonymous'] == "on" || $comment1['anonymous'] == 1){
      $anon = 1;
      $author = $comment1['name'];
    } else {
      $anon = 0;
      $author = $comment1['name'];
    }
    
    $commentArray[] = array("cid"=>$count,
			 "pid"=>$newParent,
			 "module"=>$module,
			 "itemId"=>$newId,
			 "subject"=>$sub,
			 "comment"=>$com,
			 "author"=>$author,
			 "authorIp"=>$comment1['ip'],
			 "postDate"=>$comment1['date'],
			 "editor"=>$comment1['editor'],
			 "editReason"=>$rea,
			 "score"=>$comment1['score'],
			 "anonymous"=>$anon);

    getChildren($count, $oldParent, $count, $mid, $commentArray, $newId);
  }
}

//echo PHPWS_Debug::testArray($commentArray);

if ($commentArray){
  foreach($commentArray as $key => $value)
    $core->sqlInsert($commentArray[$key], "mod_comments_data", FALSE, FALSE, FALSE, FALSE);
}

$core->db->nextId($core->tbl_prefix . "mod_comments_data");
$core->sqlLock(array("mod_comments_data"=>"WRITE", "mod_comments_data_seq"=>"WRITE"));
$maxId = $core->sqlMaxValue("mod_comments_data", "cid");
$core->query("UPDATE " . $core->tbl_prefix . "mod_comments_data_seq SET id='$maxId'");
$core->sqlUnlock();

echo "<h3>Comment Conversion Complete!</h3>";

?>