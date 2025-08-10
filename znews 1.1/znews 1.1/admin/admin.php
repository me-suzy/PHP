<?

require("./pass.php"); 

if($passw=="") { ?>

<?

   echo "<center>Hmm, da kennt wohl jemand die Daten nicht!?</center>";

?>

<? }

elseif($user=="") { ?>

<?

   echo "<center>Hmm, da kennt wohl jemand die Daten nicht!?</center>";

?>

<? }

elseif ($passw==$pass)

   { ?>

<html>

<head>

<FRAMESET border="0"  rows="65,*">

<FRAME name="top" src="menu.php" scrolling="no" marginheight="0" marginwidth="0" noresize>

<FRAME name="main" src="admin2.php" scrolling="no" marginheight="0" marginwidth="0" noresize>

</head>

<body>

</body>

</html>

<? } 

else { } ?>