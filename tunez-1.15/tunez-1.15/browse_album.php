<?php
# browse_album.php
#
# Lists albums and lets user vote.

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

include ("tunez.inc.php");

if(!empty($_GET[selected_album])) {
    include("album.class.php");
    $album = new album($_GET[selected_album]);
    $album->load();
    
    $kweerie="SELECT $typical_song_select,track from songs
        LEFT JOIN albums on songs.album_id=albums.album_id 
        LEFT JOIN artists on songs.artist_id=artists.artist_id 
        WHERE songs.album_id=albums.album_id AND
        songs.album_id=$album->album_id AND $show_active ";
    $kweerie .= sql_order($_GET[order_by], $_GET[sort_dir]);

    $title = "Browse by Album: $album->album_name";
    include("header.inc.php");

    if (!empty($album->large_album_cover)) {
        if (!empty($album->amazon_url)) {
            echo "<a href=\"" . htmlentities($album->amazon_url) . "\">";
            echo "<img src=\"$album->large_album_cover\"></img>";
            echo "</a>";
        }
        else {
            echo "<img src=\"$album->large_album_cover\"></img>";
        }
    }

    listSongs($kweerie);
}
else {
    $title = "Browse by Album";
    include ("header.inc.php");
    $kweerie = "select distinct  
        songs.album_id,album_name,songs.artist_id,artist_name,small_album_cover from songs LEFT
        JOIN albums on songs.album_id=albums.album_id LEFT JOIN artists on
        songs.artist_id=artists.artist_id order by artist_name";
    $result = tunez_query($kweerie);

    while($album_row = mysql_fetch_object($result))
    {

        if (!empty($album_row->small_album_cover)) {
            print "<img src=\"$album_row->small_album_cover\"></img>&nbsp;";
        }
        print $album_row->artist_name . " - <a
            href=\"browse_album.php?artist_id=" . $album_row->artist_id .
            "&amp;order_by=track&amp;sort_dir=ASC&amp;selected_album=$album_row->album_id&artist_name=$album_row->artist_name&album_name=$album_row->album_name\"
            class=\"nav\">" . $album_row->album_name . "</a><BR>";
    
    }
    //$content = twoColumnsDisplay($data);

    //showBox ("Browse the songs by Album",'');
}
include ("footer.inc.php");
?>
