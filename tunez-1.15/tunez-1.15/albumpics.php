<?php
# albumpics.php
# 
# Uses amazon.inc.php to get and assign album data
# 
# Disclaimer:
# I bear no responsibility whatsoever for your use of this code and the accompaning
# code in amazon.inc.php.  I provide this only for educational purposes only.

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

include("tunez.inc.php");
include("song.class.php");
include("amazon.inc.php");

if (empty($_GET[song_id])) {
    die("you must provide a songid");
}

if (!empty($_GET[store])) {
    include("album.class.php");
    $album = new album($_GET[album_id]);
    if(!$album->load()) {
        die("invalid album_id");
    }
    $album->small_album_cover = $_GET[small];
    $album->large_album_cover = $_GET[large];
    $album->amazon_url = $_GET[amazon_url];
    $album->save();
    header("Location: $_GET[referer]");
    return;
}
else {
    $song = new song($_GET[song_id], NULL);
    $song->read_data_from_db();

    print "Attempting to lookup:<br>";
    print "Artist: $song->artist_name<br>";
    print "Album: $song->album_name<br>";
    
    if ($song->album_name == "Unknown") {
        print htmlentities("Using \"\" instead of album name Unknown<br>");
        $album_name = "";
    }
    elseif (empty($song->artist_name) OR empty($song->album_name)) {
        die("I'm not even going to bother with a blank album or artist name");
    }
    else {
        $album_name = $song->album_name;
    }

    $amazon_info = get_album_covers(array(artist => $song->artist_name, album => $album_name));
    if (empty($amazon_info)) {
        print "Nothing found for this album, please click here to continue";
        return;
    }
    
    foreach ($amazon_info as $album) {
        displayalbum($album, $song);
    }

}

function displayalbum($album, $song) {
    ?>
        <p>
        Artist Name: <?= $album[artist_name] ?><br>
        Album Name: <?= $album[album_name] ?><br>
        <img src=" <?= $album[large_cover_img] ?>"></img><br>
        <img src=" <?= $album[small_cover_img] ?>"></img><br>
        <?
        print "Click <a href=\"albumpics.php?store=1&song_id=$song->song_id&album_id=$song->album_id&small=" . htmlentities($album[small_cover_img]) .
        "&large=" . htmlentities($album[large_cover_img]) . "&amazon_url=" . htmlentities($album[amazon_url]) . 
        "&referer=" . htmlentities($_SERVER[HTTP_REFERER]) . "\"> here </a> to store these into the database.";
        print "</p>";
}

?>

