<?php
# admin_db_action.php
#
# This is a wrapper page for database requests on a given song_id

/*
 * tunez
 *
 * Copyright (C) 2003, Ivo van Doesburg <idoesburg@outdare.nl>
 *  
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 */

require("tunez.inc.php");
require("song.class.php");

if(!($_SESSION[perms][p_select_edit])) {
    header("Location: access_denied.php?page=$_SERVER[REQUEST_URI]");
    die;
}

if(!empty($_GET[song_id])) {
    $song_id = $_GET[song_id];
}
else die("no song id passed");

if($_GET[action]=="delete") {
    //    if (!empty($_GET[confirm])) {
    tunez_query("UPDATE songs set status=\"delete\" where song_id=$song_id");
    //    }
    //    else {
    //        echo "Are you absolutely sure you would like to delete that song?<br>";
    //        echo "<a href=\"$_SERVER[PHP_SELF]?action=delete&song_id=$_GET[song_id]&confirm=1\">YES</a><br>";
    //        die;
    //    }
    $_SESSION[messageTitle] = "Success";
    $_SESSION[messageBody] = "Song_id $song_id has been marked for deletion";
}
elseif($_GET[action]=="hide") {
   tunez_query("UPDATE songs set status=\"hide\" where song_id=$song_id");
   $_SESSION[messageTitle] = "Success";
   $_SESSION[messageBody] = "Song_id $song_id was successfully hidden";
}
elseif($_GET[action]=="normal") {
   tunez_query("UPDATE songs set status=\"normal\" where song_id=$song_id");
   $_SESSION[messageTitle] = "Success";
   $_SESSION[messageTitle] = "Song_id $song_id was returned to normal status";
}
elseif($_GET[action]=="readtag") {
    $song = new Song($song_id, NULL);
    $song->read_data_from_db("filename,type");
    $song->read_data_from_file(TRUE);
    $song->write_data_to_db($song->sql_song_fields_for_id3s);
    $_SESSION[messageTitle] = "Success";
    $_SESSION[messageBody] = "The tag was successfully read into the database";
}
elseif($_GET[action]=="writetag") {
    $song = new Song($song_id, NULL);
    $song->read_data_from_db();
    $song->write_data_to_file();
    $song->update_ID3=0;
    $song->write_data_to_db("update_ID3");
    $_SESSION[messageTitle] = "Success";
    $_SESSION[messageBody] = "The database information on the song was successfully written out to the audio file";
}
else {
    die("no valid action, you suck");
}


header("Location: $_SERVER[HTTP_REFERER]");
?>
