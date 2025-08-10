<?php
# admin_daemon.php
#
# This handles starting and stopping of the Tunez perl or php daemons

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

if(!($_SESSION[perms][p_daemon])) {
	header("Location: access_denied.php?page=$_SERVER[REQUEST_URI]");
	die;
}

if (!(file_exists("./detach-1.2/detach"))) {
    $_SESSION[messageTitle] = "ERROR starting Daemon!";
    $_SESSION[messageBody] = "Did you compile the detach program in the detach-1.2 directory?";
    $location = $_SERVER['HTTP_REFERER'];
    header("Location: $location");
    return;
}
    
$action = $_GET[action];
if ($action == "start")
{
    if ($mode == "local-php" || $mode == "shout-php")
    {
        exec("./detach-1.2/detach $php_executable -d \"max_execution_time=0\" ./tunezd.php & >/dev/null 2>/dev/null");
        $_SESSION['messageTitle'] = "Starting Tunez PHP Daemon";
        $_SESSION['messageBody'] = "";
    }
    elseif ($mode == "local-perl" || $mode == "shout-perl") {
        exec("./detach-1.2/detach $perl_binary ./tunezd.pl & >/dev/null 2>/dev/null");
        $_SESSION['messageTitle'] = "Starting Tunez Perl Daemon";
        $_SESSION['messageBody'] = "";
    }
    elseif ($mode == "ices") {
        chdir($ices_working_directory);
        exec("$detach_binary $ices_binary & >/tmp/ices.log 2>/tmp/ices2.log");
        $_SESSION[messageTitle] = "Starting ices script";
        $_SESSION[messageBody] = "";
    }
    sleep(2); // give mysql and mpg123 some time before refreshing page with new info
}

if ($action == "stop")
{
    if ($mode == "local-php" || $mode == "shout-php") {
        system("killall php");
        sleep(1);
        //system("kill -9 `ps -aef |grep tunezd.php |grep www-data |cut -c 11-15`");
        //system("kill -9 `ps -aef |grep mpg123 |grep www-data |grep -v sh |cut -c 11-15`");
        system("killall mpg123");
        system("killall ogg123");
        system("killall shout");
    }
    elseif ($mode == "local-perl" || $mode == "shout-perl") {
        system("killall tunezd.pl");
        sleep(1);
        system("killall mpg123");
        system("killall ogg123");
        system("killall shout");
    }
    elseif ($mode == "ices") {
        system("killall ices");
        sleep(1);
    }
    else {
        die("bad mode");
    }
    $kweerie = "DELETE FROM np";
    mysql_query($kweerie);
 
	$_SESSION['messageTitle'] = "Stopping Tunez Daemon";
	$_SESSION['messageBody'] = "Hope it worked ;)";
}

$location = $_SERVER['HTTP_REFERER'];
header("Location: $location");
?>
