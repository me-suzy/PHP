<?php

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

$status = 1;

if ($currentVersion < 0.41) {
  $content .= "Photoalbum Updates (Version 0.41)<br />";
  $content .= "+ ability to hide and show albums and photos<br />";
  $content .= "+ next and previous links in the photo view<br />";
  $content .= "+ printable view of a photo<br />";
  $content .= "+ bug fixes<br />";
}

if ($currentVersion < 0.50) {
  $content .= "Photoalbum Updates (Version 0.50)<br />";
  $content .= "+ Converted all forms to EZforms<br />";
}

if ($currentVersion < 0.55) {
  $content .= "Photoalbum Updates (Version 0.55)<br />";
  $content .= "+ Made access denied error messages more specific<br />";
  $content .= "+ Updated all messages to use PHPWS_Message<br />";
  $content .= "+ Print view now opens in a new window<br />";
  $content .= "+ Other various bug fixes<br />";
}

if ($currentVersion < 0.58) {
  $content .= "Photoalbum Updates (Version 0.58)<br />";
  $content .= "+ added the ability to delete entire albums<br />";
}

if ($currentVersion < 0.60) {
  $content .= "Photoalbum Updates (Version 0.60)<br />";
  $content .= "+ bug fixes<br />";
}

if ($currentVersion < 0.62) {
  $content .= "Photoalbum Updates (Version 0.62)<br />";
  $content .= "+ fixed bug related to paging when photos were hidden<br />";
  $content .= "&#160;&#160;&#160;- improper amount of photos showing up on each page<br />";
  $content .= "+ session cleanup<br />";
}
?>