<?php
include_once ("tunez.inc.php");
include_once ("vote.inc.php");
require_once("PQueue.class.php");

#if (isset($_GET[song_id])) 
#    $song_id = $_GET[song_id];
if (isset($_GET[action])) {
    $action = $_GET[action];
}
else die("error");

if (isset($_GET[song_id])) {
    $songs = preg_split("/,/", $_GET[song_id]);   
}
else die("error");

if ($action=="unvote") {
    foreach ($songs as $song_id) {
        unvote($song_id);
    }
}
elseif($action=="vote") {
    if ($voting_mode == "classic") {
        dovote($songs);
    }
    elseif($voting_mode == "complex") {
        newvote($songs);
    }
    else die("error in vote.php");
}
header("Location: $_SERVER[HTTP_REFERER]");
?>
