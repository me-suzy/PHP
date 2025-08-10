<html>
<head>
<title> Cyberkit.co.uk </title>
<link rel="stylesheet" href="cyber.css" type="text/css">
<SCRIPT LANGUAGE="JavaScript">

<!-- Begin
 function putFocus(formInst, elementInst) {
  if (document.forms.length > 0) {
   document.forms[formInst].elements[elementInst].focus();
  }
 }
//  End -->
</script>
</head>
<body onLoad="putFocus(0,1);">


<center>
<table width="80%" cellpadding="0" cellspacing="0" bordercolor="gray" rules="none" border="0">
</center>
<form method="post" action="cyberact.php?query=<?echo $PHP_SELF."&".$convert?>" name="shoutbox">
	  <tr>
<center>	    
<td class="small">Name:</td><td class="small"><input type="text" name="name" size="25" <?if(!empty($ck_name)): echo"value=\"$ck_name\""; endif;?>><b>   <?php 
$message = "You Are 1 Of $online Visitors";
$alonemess = "You Are Alone.";
$file = "online.dat";
$timeoutseconds = 30;
$timestamp = time();
$timeout = ($timestamp-$timeoutseconds);
$fp = fopen("$file", "a+");
$write = $REMOTE_ADDR."||".$timestamp."\n";
fwrite($fp, $write);
fclose($fp);
$online_array = array();
$file_array = file($file);
foreach($file_array as $newdata){
	list($ip, $time) = explode("||", $newdata);
	if($time >= $timeout){
		array_push($online_array, $ip);
	}
}
$online_array = array_unique($online_array);
$online = count($online_array);
if($online == "1"){
	echo "$alonemess";
}else{
	echo "$message";
}
?></b></td>
	  </tr>
	  	  <tr>
	    <td class="small">Say:</td><td class="small"><input type="text" name="yousay" size="100%"></td>
	  </tr>
	  <tr>
	    <td class="small" colspan="20" align="center"><input type="submit" value="say"> <?if($shoutbox=="open"):echo"<input type=\"checkbox\" name=\"func\" value=\"close\"> <img src=\"../imgs/misc/alwayclose.gif\" width=\"10\" height=\"9\" border=0 alt=\"alway close\">"; elseif($shoutbox=="close"):echo"<input type=\"checkbox\" name=\"func\" value=\"open\"> <img src=\"../imgs/misc/alwayopen.gif\" width=\"10\" height=\"9\" border=0 alt=\"alway open\">";endif;?></td>
	  </tr>
	  </form>	 
</table>
  </div>
      </td>
	</tr>
<table>
</body>
</html>