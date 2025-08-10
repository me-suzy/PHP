<?php
include ("./config.inc.php");

if (isset($create)) {

$table[0] = "CREATE TABLE $COUNT_TBL[daily] (
  day date default NULL,
  visitors int(12) NOT NULL default '0'
)";

$table[1] = "CREATE TABLE $COUNT_TBL[total]  (
  total bigint(19) NOT NULL default '0',
  installtime int(11) NOT NULL
)";

$table[2] = "CREATE TABLE $COUNT_TBL[visitors] (
  time int(14) default NULL,
  ip varchar(15) NOT NULL default '',
  host varchar(90) NOT NULL default ''
)";


    $serverid  = mysql_connect($COUNT_DB["host"], $COUNT_DB["user"], $COUNT_DB["pass"]) or die("Cannot connect to database");
    @mysql_select_db($COUNT_DB['dbName'],$serverid) or die("Unable to select database: <b>$COUNT_DB[dbName]</b>");
    for ($i=0;$i<sizeof($table);$i++) {
        mysql_query($table[$i],$serverid) or die("Database Error");
    }

    echo "Done";
    exit();
}
?>
<html>
<head>
<title>Create Tables</title>
</head>
<body bgcolor="#FFFFFF" text="#000000">
  Database: <?php echo $COUNT_DB["dbName"];?><br>
  Hostname: <?php echo $COUNT_DB["host"];?><br>
  Username: <?php echo $COUNT_DB["user"];?><br>
  Password: *****<br>
<br><a href="<?php echo basename($PHP_SELF); ?>?create=1">Create tables</a>
</body>
</html>
