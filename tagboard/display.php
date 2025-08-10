<?
require("config.php");
?>

<html>
<head>
<?
if($meta_refresh == 1){
	print "<meta http-equiv=\"refresh\" content=\"$meta_refresh_rate; url=$PHP_SELF\">";
}
?>
<title>Tag Board</title>
<!-- CJ Tag Board V1.0 -->
<link rel="stylesheet" href="stylesheet.css" type="text/css">
</head>
<body topmargin="0" leftmargin="0">
<?
if($print_how_many == 1){
	include("stats.txt");
	print "<br>";
}
?>
<?php include("tag.txt"); ?>
</body>
</html>