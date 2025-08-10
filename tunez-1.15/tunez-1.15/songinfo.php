<?php
# songinfo.php
# 
# Displays information on a song

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
$title="Detail on that song...";
include("header.inc.php");
include("song.class.php");

if(!empty($_GET)) {
        if(isset($_GET[song_id])) {
                $song = new song($_GET[song_id], NULL);
                $song->read_data_from_db();
                $song->print_info();
        }
        else echo "No Song Id Specified<br>";
}
else echo "No Song Id Specified<br>";
                

include("footer.inc.php");

?>



