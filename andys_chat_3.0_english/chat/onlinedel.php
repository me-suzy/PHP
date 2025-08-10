<html>
<head><title>Ausgeloggt!</title></head>
<body onload="setTimeout('javascript:window.close()',10000);">
<?
unlink("online/$username.txt");
echo"$username logged out!!";
?>
</body></html>