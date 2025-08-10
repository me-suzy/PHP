<html>
<head>
<title>b2 > LJUpdate Installation</title>
</head>
<body>

<?php
include ("b2config.php");

function mysql_doh($msg,$sql,$error) {
	echo "<p>$msg</p>";
	echo "<p>query:<br />$sql</p>";
	echo "<p>error:<br />$error</p>";
	die();
}

$connexion = mysql_connect($server,$loginsql,$passsql) or die("Can't connect to the database<br>".mysql_error());
$dbconnexion = mysql_select_db($base, $connexion);

if (!$dbconnexion) {
	echo mysql_error();
	die();
}

echo "Now creating the necessary tables in the database...<br /><br />";


# Note: if you want to start again with a clean b2 database,
#       just remove the // in this file

// $query = "DROP TABLE IF EXISTS $tableljusers";
// $q = mysql_query($query) or mysql_doh("doh, can't drop the table \"$tableljusers\" in the database.");

$query = "CREATE TABLE $tableljusers ( ID int(10) unsigned NOT NULL, user_login varchar(20) NOT NULL, user_pass varchar(20) NOT NULL, PRIMARY KEY (ID), UNIQUE ID (ID) )";
$q = mysql_query($query) or mysql_doh("doh, can't create the table \"$tableljusers\" in the database.", $query, mysql_error());

// $query = "DROP TABLE IF EXISTS $tableljposts";
// $q = mysql_query($query) or die ("doh, can't drop the table \"$tableljposts\" in the database.");

$query = "CREATE TABLE $tableljposts ( ID int(10) unsigned NOT NULL, LJID int(10) unsigned NOT NULL, post_author int(10) unsigned NOT NULL, PRIMARY KEY (ID), UNIQUE ID (ID) )";
$q = mysql_query($query) or mysql_doh("doh, can't create the table \"$tableposts\" in the database.", $query, mysql_error());

echo "livejournal integration: OK<br />";
?>

<br />
Installation successful !<br />
<br/ >

</body>
</html>