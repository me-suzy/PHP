<?php
# upload.php 
#
# Handles HTTP uploads

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

$NoRefresh = true;

$title = "Upload a Song!";
require("tunez.inc.php");
require("database.inc.php");

if (!($_SESSION[perms][p_upload])) {
    header("Location: access_denied.php?page=" . $_SERVER['REQUEST_URI']);
    die;
}

if(!($enable_uploads)) {
    include("header.inc.php");
    showBox("Sorry!","Uploads are not allowed on this system.");
    include("footer.inc.php");
    die;
}
if(!($_SESSION[perms][p_upload])) {
    include("header.inc.php");
    showBox("Sorry!","You aren't allowed to upload files.");
    include("footer.inc.php");
    die;
}


if (empty($_FILES) && empty($_POST)) {
    include ("header.inc.php");
    ?>
        <body bgcolor="#FFFFFF">
        <h3 align="center">File Upload</h3>
        <form enctype="multipart/form-data" method="post" action="upload.php">
        <center>
        <h4>Please ensure that the ID3 data on the song is correct before you upload</h4><br>
        <input type="file" name="songfile">
        <br>
        <input type="submit" value="Upload the Song">
        </center>
        </form>
        </body>
        <?php
        include ("footer.inc.php");
}

else if (!empty($_FILES)) {
#else if (is_uploaded_file($songfile) && $_FILES['songfile']['type']=="audio/mpeg") {
# If they uploaded the file...
# TODO check for wrong MIME types.. etc.

    global $copying_root_dir;
    require("song.class.php");

    if ($_FILES[songfile][error]) {
        die("There was an error when uploading.  Check to make sure file upload size isn't too small in your PHP configuration file");
    }

    $song = new Song(NULL, $_FILES[songfile][tmp_name]);
    $song->read_data_from_file(TRUE, NULL, $_FILES[songfile][name]);
# duplicate song record will be created if file is uploaded twice (two song_ids, one file) FIXME
    $path = $song->guess_best_path($copying_root_dir);
    $song->move_file($path, FALSE, FALSE);
    $song->add_to_db(0);

    $_SESSION[messageTitle] = "Thanks for your upload";
    $_SESSION[messageBody] = "Thank you for your contribution";
    $redirect_to = "songinfo.php?song_id=$song->song_id";
    header("Location: $redirect_to");

}
 
?>
