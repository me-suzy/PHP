<html>
<head>
<style>
scrollbar-shadow-color: white; scrollbar-arrow-color: black; scrollbar-track-color: #797D86; scrollbar-base-color: #AEB0B5
</style>
<title> Cyberkit.co.uk </title>
<link rel="stylesheet" href="cyber.css" type="text/css">
<script language="JavaScript">
<!--
function chooseact(change) {
    document.shoutbox.yousay.value += change;
}
//-->
</script>
<meta http-equiv="refresh" content="5"> 
</head>
<body>

<table width="100%" cellpadding="2" cellspacing="2" bordercolor="gray" rules="none" border="1">
<?
include"func.inc.php";
$openfile = file("cyber.db.php");
$total = count($openfile);

for ($i=0; $i<$total; $i++):
list($UNEMPOWERED,$NAME,$EMAIL,$YOUSAY,$DATE) = explode('|',chop($openfile[$i]));

	$YOUSAY=output_content($YOUSAY);
	
	echo "<tr><td colspan=\"2\"><a href=mailto:$EMAIL><font class=\"alert\">$NAME</font></a></td></tr>\n";	
	echo "<tr><td class=\"small\" colspan=\"2\">$YOUSAY</td></tr>\n";
	echo "<tr><td class=\"alert\" colspan=\"2\">$DATE</td></tr>\n";
	echo "<tr><td colspan=\"2\">&nbsp;</td></tr>\n";
endfor;

	$convert = ereg_replace("&","andmark",$QUERY_STRING);
?>
	  <tr>
	    <td class="small" colspan="2" align="center"><a href="http://www.cyberkit.co.uk"> CyberChat - 1.3 (free)</a></td>
	  </tr>
	</table>
  </div>
      </td>
	</tr>
<table>
</table>
</body>
</html>