<?php
# skip_random.php
#
# Page for allowing users to skip random songs

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

if(!($_SESSION[perms][p_random_skip])) {
    header("Location: access_denied.php?page=$_SERVER[REQUEST_URI]");
    die;
}

$query="select wasrandom from np";
$result=mysql_query($query) or die(mysql_error());
if(mysql_num_rows($result) < 1)
        die("no song playing!");
        
$row=mysql_fetch_object($result);
if($row->wasrandom != 1) {
    die("the song you're listening to wasn't random!");
}

if (preg_match("/local/", $mode)) {
    system ("killall mpg123");
    system ("killall ogg123");
}
elseif ($mode == "ices")
{
    system ("killall -USR1 ices");
}
elseif (preg_match("/shout/", $mode)) {
    system ("killall shout");
}

sleep(1); // give mysql and mpg123 some time before refreshing page with new info
header("Location:$_SERVER[HTTP_REFERER]");
?>
