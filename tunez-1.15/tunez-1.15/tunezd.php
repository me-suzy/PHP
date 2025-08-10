<?php
# tunezd.php
#
# This is the Tunez PHP daemon

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

include ("config.inc.php");
include ("PQueue.class.php");
include ("song.class.php");
#$debug = true;
// Make mysql_pconnection
mysql_pconnect($mysql_dbhost, $mysql_dbuser, $mysql_dbpass);
mysql_select_db($mysql_dbname);

if ($mode != "shout-php" && $mode != "local-php") {
    die("You should not be using this php script unless you are in shout-php or local-php mode");
}

$queue = new PQueue;
$queue->generate_from_votes();

while(1) { 
    $queue->read();
    $songid = $queue->dequeue();
    $song = new song($songid, NULL);
    $song->read_data_from_db("filename,type");

    if ($mode == "local-php") {
        if($song->type == "mp3" || $song->type == "id3")
            $cmd = "$mpg123_binary -b 1024 \"$song->filename\" > /dev/null 2> /dev/null";
        elseif($type == "ogg")
            $cmd = "$ogg123_binary -b 1024 \"$song->filename\" > /dev/null 2> /dev/null";
        else //assuming it's an MP3
            $cmd = "$mpg123_binary -b 1024 \"$song->filename\" > /dev/null 2> /dev/null";
    }
    elseif ($mode=="shout-php")
        $cmd = "$shoutcast_binary -x -3 -t -P $icecast_v1_password $icecast_host \"$song->filename\" > /dev/null 2> /dev/null";
    else
        die("invalid mode");
    system($cmd);
}

mysql_close();
?>

