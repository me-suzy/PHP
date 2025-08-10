<?php
include ("./config.inc.php");
if ($COUNT_CFG['use_db']) {
    include ("./mysql.class.php");
}
include ("./counter.class.php");       
$counter = new dcounter();
$visits = $counter->show_counter();  /* Returns an associative array */

?>
<html>
<head>
<title>Daily Counter</title>
<style type="text/css">
<!--
.links { color: #000000}
-->
</style>
</head>
<body bgcolor="#FFFFFF">
<center>
  <br>
  <br>
  <u><font face="Arial, Helvetica, sans-serif" size="4">Daily Counter - PHP Version</font></u><br>
  <br>
  <font size="2" face="Arial, Helvetica, sans-serif">- Hit Reload - <br>
  <br>
  </font> 

  <table border="0" cellspacing="1" cellpadding="2" align="center">
  <tr>
    <td>
    <font face="Verdana, Arial, Helvetica, sans-serif" size="1">
      <?php echo date("D, j F Y"); ?>
      <br><br>
      Total Visits: <?php echo $visits['total']; ?>
      <br>
      <b><a href="javascript:void(window.open('visitors.php','Visitors','scrollbars=yes,width=420,height=210'))" class="links">Visitors</a> today: <font color="#CC0066">
       <?php echo $visits['visits_today']; ?></font></b>
     </font>
    </td>
  </tr>
</table>
</center>
</body>
</html>

