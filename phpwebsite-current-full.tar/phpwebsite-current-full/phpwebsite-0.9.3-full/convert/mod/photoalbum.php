<?php

/**
 * Conversion Script For Photoalbum
 *
 * Converts phpws_gallery to photoalbum
 */

$db = old_connect();

if (!$core->sqlTableExists(OLD_PREFIX . "mod_gallery_albums")){
  echo "<h3>0.8.x Gallery not installed.</h3>";
  return;
}

$result = $db->query("SELECT * FROM " . OLD_PREFIX . "mod_gallery_albums");
$albumArray = array();
$albumIds = array();

while($album = $result->fetchrow(DB_FETCHMODE_ASSOC)) {
  $albumIds[$album['id']] = NULL;
  $albumArray[$album['id']]['label'] = $album['name'];

  if($album['enabled'] == "yes") {
    $albumArray[$album['id']]['hidden'] = 0;
  } else {
    $albumArray[$album['id']]['hidden'] = 1;
  }

  $albumArray[$album['id']]['owner'] = "converted";
  $albumArray[$album['id']]['editor'] = "converted";
  $albumArray[$album['id']]['ip'] = $_SERVER['REMOTE_ADDR'];
  $albumArray[$album['id']]['groups'] = array();
  $albumArray[$album['id']]['created'] = time();
  $albumArray[$album['id']]['updated'] = time();
  $albumArray[$album['id']]['approved'] = 1;
  $albumArray[$album['id']]['blurb1'] = "Please enter a description for this album.";
  sleep(1);
}

$count = 1;
foreach($albumIds as $albumId => $nullVar) {
  $albumArray[$albumId]['id'] = $count;
  $core->sqlInsert($albumArray[$albumId], "mod_photoalbum_albums", FALSE, FALSE, FALSE, FALSE);
  $albumIds[$albumId] = $count;
  $count ++;
  $dir = "../images/photoalbum/" . $albumIds[$albumId];
  mkdir($dir);
  if(!is_dir($dir)) {
    echo "Unable to create directory: $dir<br />";
  }
}

$core->db->nextId($core->tbl_prefix . "mod_photoalbum_albums");
$core->sqlLock(array("mod_photoalbum_albums"=>"WRITE", "mod_photoalbum_albums_seq"=>"WRITE"));
$maxId = $core->sqlMaxValue("mod_photoalbum_albums", "id");
$core->query("UPDATE " . $core->tbl_prefix . "mod_photoalbum_albums_seq SET id='$maxId'");
$core->sqlUnlock();


$result = $db->query("SELECT * FROM " . OLD_PREFIX . "mod_gallery_media");
$photoArray = array();
$oldDir = OLD_SITE_DIR . "mod/gallery/media/";

$count = 0;
while($photo = $result->fetchrow(DB_FETCHMODE_ASSOC)) {
  $photoArray[$photo['id']]['album'] = $albumIds[$photo['album_id']];

  if(isset($photo['title']) && (strlen($photo['title']) > 0)) {
    $photoArray[$photo['id']]['label'] = $photo['title'];
  } else {
    $photoArray[$photo['id']]['label'] = "Description not available.";
  }

  $newDir = PHPWS_HOME_DIR . "images/photoalbum/" . $albumIds[$photo['album_id']] . "/";

  $time = time() + $count;
  $oldName = $photo['id'] . "." . $photo['extension'];
  $oldThumbnail = $photo['id'] . "." . $photo['thumbnail_extension'];
  $name = $time . "_" . $photo['title'] . ".jpg";
  $thumbnail = $time . "_" . $photo['title'] . "_tn.jpg";

  $file0 = $oldDir . $oldName;
  $file1 = $oldDir . "thumbnails/" . $oldThumbnail;

  if(!copy($file0, $newDir . $name)) {
    echo "Could not copy the file $file0<br />";
  }

  if(!copy($file1, $newDir . $thumbnail)) {
    echo "Could not copy the file $file1<br />";
  }

  $photoArray[$photo['id']]['name'] = $name;

  if($photo['extension'] == "jpg" || $photo['extension'] == "JPG" || $photo['extension'] == "jpeg" || $photo['extension'] == "JPEG" || $photo['extension'] == "pjpeg") {
    $photoArray[$photo['id']]['type'] = "image/jpeg";
  } else if($photo['extension'] == "png" || $photo['extension'] == "PNG") {
    $photoArray[$photo['id']]['type'] = "image/png";
  } else if($photo['extension'] == "gif" || $photo['extension'] == "GIF") {
    $photoArray[$photo['id']]['type'] = "image/gif";
  }

  $photoArray[$photo['id']]['width'] = $photo['width'];
  $photoArray[$photo['id']]['height'] = $photo['height'];

  $photoArray[$photo['id']]['tnname'] = $thumbnail;
  $img = $newDir . $thumbnail;
  $info = getimagesize($img);

  $photoArray[$photo['id']]['tnwidth'] = $info[0];
  $photoArray[$photo['id']]['tnheight'] = $info[1];

  $images[$albumIds[$photo['album_id']]] = "<img src=\"images/photoalbum/" . $albumIds[$photo['album_id']] . "/" . $thumbnail . "\" width=\"" . $info[0] . "\" height=\"" . $info[1] . "\" border=\"0\" alt=\"" . $photo['title'] . "\" />";

  if(isset($photo['short_description']) && (strlen($photo['short_description']) > 0)) {
    $photoArray[$photo['id']]['blurb'] = $photo['short_description'];
  } else {
    $photoArray[$photo['id']]['blurb'] = $photoArray[$photo['id']]['label'];
  }

  $photoArray[$photo['id']]['owner'] = $photo['submitter_name'];
  $photoArray[$photo['id']]['editor'] = "converted";
  $photoArray[$photo['id']]['ip'] = $_SERVER['REMOTE_ADDR'];
  $photoArray[$photo['id']]['groups'] = array();
  $photoArray[$photo['id']]['created'] = $photo['timestamp'];
  $photoArray[$photo['id']]['updated'] = time();
  $photoArray[$photo['id']]['hidden'] = 0;
  $photoArray[$photo['id']]['approved'] = 1;

  $count ++;
}

foreach($photoArray as $photo) {
  $core->sqlInsert($photo, "mod_photoalbum_photos");
}

foreach($images as $id => $image) {
  $save = array("image"=>$image);
  $core->sqlUpdate($save, "mod_photoalbum_albums", "id", $id);
}

echo "<h3>Photoalbum Conversion Complete!</h3>";

?>