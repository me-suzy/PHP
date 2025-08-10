<?php
# config.inc.php
#
# The Tunez php config file


# Mysql details
$mysql_dbhost = 'localhost';
$mysql_dbuser = 'tunez';
$mysql_dbpass = '';
$mysql_dbname = 'tunez';

# Choose mode.  Refer to the INSTALL file for directions.

#$mode = "local-perl";  # tunezd.pl
#$mode = "local-php";  # tunezd.php
#$mode = "shout-perl"; # tunezd.pl
#$mode = "shout-php";  # tunezd.php
$mode = "ices";       

# The path to your PERL binary for calling tunezd.pl
# (shout-perl), (local-perl)
$perl_binary = "/usr/bin/perl";

# The paths to your binaries for playing back in local mode
# (local-perl), (local-php)
$mpg123_binary = "/usr/bin/mpg123";
$ogg123_binary = "/usr/bin/ogg123";
#----------------------------------

# To display the Icecast URL on the index.php page
$icecast_URL = "http://ADD YOUR URL HERE:8000/ices";

# for Icecast Stream mode (v1.x)
$shoutcast_binary = "/usr/bin/shout";
$icecast_v1_host = "localhost";
$icecast_v1_password = "hackme";
#-------------------------------

# for ices mode
$ices_binary = "/usr/local/bin/ices";
$ices_working_directory = "/SOMEWHERE";
# The ices_working_directory should be a location where your ices.conf
# and ices.pm files are located.  Tunez will change to this directory
# prior to running the ices binary when you are using admin_daemon
$detach_binary = "/SOMEWHERE/detach-1.2/detach";
# The detach_binary is whereever you have put the detach binary.
# This must be set because we do a chdir($ices_working_directory) so
#-------------------------------------------------------------------

# for (local-php) and (shout-php) modes
#       Location of the php executable with mysql support compiled in
$php_executable="/usr/local/bin/php";
#------------------------------------



# --------------------------
# General settings

# typical_song_select [text]
#       These are the SQL rows which will be displayed on the main page when
#       doing a browse.  Power users may customize this with additional fields
#       if desired (refer to SQL specs)
$typical_song_select = "song_id, status, update_id3, songtitle, artist_name, album_name";

# TIMEOUT_SECONDS [int]
#   How long a user can remain idle in seconds before they have to reauthenticate
define("TIMEOUT_SECONDS", 60*10);  // 10 minutes

# ----------------------------------------------------------------------
# Choose voting mode

$voting_mode = "classic";
#$voting_mode = "complex";
# complex is still in beta.. you're welcome to try it out if you want but you'll
# have to play around with the voting_rights table on your own (see vote.inc.php
# for guidance)

# ---------------------------------
# Choose government type
#
# Democracy is the Tunez default and is suggested
# If you want to play with socialism or define your own modes, you are welcome to

#democracy:
#       METHOD: Everyone gets unlimited votes but can only vote once per song.
#               Votes are sorted by total number per song, then by how new the
#               song is.
$government="SELECT songs.song_id, songtitle, length, artists.artist_name,
    albums.album_name, count(*) AS votes, MIN(timestamp) as timestamp FROM
    queue LEFT JOIN songs ON queue.song_id=songs.song_id LEFT JOIN artists on
    songs.artist_id=artists.artist_id LEFT JOIN albums on
    songs.album_id=albums.album_id GROUP BY filename ORDER BY votes DESC,
    timestamp";

#socialism:
#       METHOD: Songs that are in the queue for 10 minutes get extra
#               Pseudo-Votes.  This is supposed to help make every artist the
#               same.  Ayn Rand proved this wouldn't work in Atlas Shrugged
/*
$government = "select songs.song_id, songtitle, length, artists.artist_name,
    albums.album_name, count(*) AS votes, count(*) + (UNIX_TIMESTAMP() -
            UNIX_TIMESTAMP(timestamp))/(60*10) AS score from queue LEFT JOIN
            songs using(song_id) LEFT JOIN artists on
            songs.artist_id=artists.artist_id LEFT JOIN albums on
            songs.album_id=albums.album_id GROUP BY filename ORDER BY score
            DESC, timestamp";
*/
# ----------------------------------------------------------------------

# The types of file extensions you are looking to be added to the database
# (case doesn't matter).  Feel free to hack on support for whatever filetypes 
# you like/use but you may run into problems when trying to update tags.
$valid_extensions = Array("mp3","ogg");

# Array for which directories to search through for mp3's (it will recurse
# down from this directory)
#$mp3dir[0] = "/path/to/mp3s";
#$mp3dir[1] = "/another/mp3/folder";

# If you want to ignore processing of a file when running admin_updateDb.php
# just add it to this array
#$ignore_list[0] = "/files/you/want/to/ignore";

# default_group_id
#       This is the default group id someone is assigned to when they signup
#       for an account
$default_group_id = 1;

# trim_songs [boolean]
#       FALSE, 0:
#               Songs are marked as being offline and their records are saved
#               if the file can't be found on the file system.  Later if the
#               file is rediscovered (ala NFS share comes back online or a
#               CDROM is reinserted the file is shown again during searches)
#       TRUE, 1:
#               Songs are deleted from the database when they dissappear off
#               of the file system (old tunez behavior).
$trim_songs=0;

# Authenticate the user w/ e-mail + confirmation code
$authenticate = 0;

# Options for HTTP uploading 

# enable_uploads
#       0 means no uploads are allowed
#       1 means uploads are allowed
$enable_uploads = 0;
# copying_root_directory is the root path where the mp3's are copied to
# THIS IS IMPORTANT.  You should set this to match an entry in the $mp3dir
# array!!!!
$copying_root_dir = "";
# distribute_with_full_paths means that if set, directories will be created to
# house the newly uploaded mp3's in the fashion of distribute_style below.
$distribute_with_full_paths = 1;
// distribute_style allows you to sort uploaded mp3's into a tree based
// on their id3 tags onto your filesystem.
//
// %a = Artist Name
// %n = Album Name
// %s = Song Title
// %t = Track Number
// %g = Genre
$distribute_style = "/%a/%n/%s";
# temp_dir = the place where temporarily uploaded mp3's are stored
# ex. $temp_location = "/tmp/"
$temp_dir = "/tmp";

?>
