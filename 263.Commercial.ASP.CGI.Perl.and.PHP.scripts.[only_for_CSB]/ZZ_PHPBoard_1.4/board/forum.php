<?php
include("./inc/inc.page_top.php");

function gettime($time) {
global $weekdays, $datesub, $dateorder;
$time = explode(' ',$time);
	if($dateorder!='en'){
		$date = explode('/',$time[0]);
		$time[0] = $date[1].'/'.$date[0].'/'.$date[2];
	} 
$date = sprintf("%08s %02s %08s", $time[1], substr($weekdays[$time[2]],0,$datesub), $time[0]);
return $date;
}

function printentry($entry) {
		global $weekdays, $datesub, $dateorder, $cfg_msgdir;
		
		$sub = explode (".", $entry);
		$len = (count($sub)-1)*6;
		for ($i = 1; $i <= $len; $i++) {
		$pusher = $pusher . "&nbsp;";
		}
		
		$file = "./".$cfg_msgdir."/" . $entry . ".txt";
		if (file_exists($file)) {
		$tfile = fopen("$file","r");
		$name = chop(fgets($tfile, 1000));
		$email = chop(fgets($tfile, 1000));
		$subject = chop(fgets($tfile, 1000));
		$time = chop(fgets($tfile, 1000));
		
		$date= gettime($time);
		
		fclose($tfile);
		$entry = "entry.php?show=".$entry;
		echo "$pusher<font face=\"$cfg_type\"><b>&#8226; &nbsp;<a href=\"$entry\" class=forum>$subject</a></b> - ";
if (preg_match("/[a-zA-Z0-9]/",$email)){
	echo "<a href=\"mailto:$email\">";
	$tag = "a";
} else {
	echo "<b>";
	$tag = "b";
}
echo $name;
echo "</$tag> - <i>$date</i></font><br>";
	}
}

////////////////////////////////////// SORTIERFUNKTION BEGIN ////////////
include("./inc/func.sort.php");
////////////////////////////////////// SORTIERFUNKTION ENDE ////////////

foreach($all as $val) {			
		if (strstr ($val, ".") == "") {
			$main[($val)] = array();
		} else {
			$sub = explode (".", $val);
			$main[$sub[0]][] = $val;
		}
}

krsort($main);
$msg_num = key($main) + 1;

////////////////////////////////////// MESSAGE BEGIN ////////////
include("./inc/inc.form_top.php"); 

while (list ($m_key, $m_val) = each ($main)) {
    printentry($m_key);
	if (is_array($m_val)) {
	sort($m_val);
		while (list ($s_key, $s_val) = each ($m_val)) {		
			printentry($s_val);
		}
	}
	//echo "</p>"; oben ist <p> in printmain auch entfernt worden
}

if (count($all) == 0) { // Falls noch gar keine NAchricht im Forum steht
echo "<center><b>- $cfg_m_no_message -</b></center>";
} else {
	echo "<br>";
}

echo "</td></tr></table>"; // table-bottom zu -- include("./inc/mform_top.php"); --
////////////////////////////////////// MESSAGE END ////////////

echo <<<PostForm
<a name="post"><center>
  <table width="$cfg_table_width" border="0" cellspacing="0" cellpadding="10" class=ta-tblr>
    <tr>
      <td>
        <div align="center">
          <center>
            <font face="$cfg_type" size="5"><b>$cfg_m_write_m</b></font>
          </center>
        </div>
      </td>
    </tr>
  </table>
  <br>
  <br>
</center>
</a>
<form method=POST action="send.php?post=$msg_num">
  <font face="$cfg_type">
  <table border="0" align="center">
    <tr> 
      <td width="100"><b><font size="2">$cfg_m_f_name</font></b></td>
      <td> <input type=text name="name" value="" size=50> </td>
    </tr>
    <tr> 
      <td width="100"><b><font size="2">$cfg_m_f_email</font></b></td>
      <td> <input type=text name="email" value="" size=50> </td>
    </tr>
    <tr> 
      <td width="100"><b><font size="2">$cfg_m_f_subject</font></b></td>
      <td> <input type=text name="subject" value="" size=50> </td>
    </tr>
    <tr> 
      <td width="100" valign="top"><b><font size="2">$cfg_m_f_message</font></b></td>
      <td><textarea cols=43 rows=10 name="message" value=""></textarea> <p> </td>
    </tr>
    <tr> 
      <td width="100"><b><font size="2">$cfg_m_f_linkurl</font></b></td>
      <td> <input type=text name="url" value="" size=50> </td>
    </tr>
    <tr> 
      <td width="100"><b><font size="2">$cfg_m_f_linktitel</font></b></td>
      <td> <input type=text name="url_title" value="" size=50> </td>
    </tr>
    <tr> 
      <td width="100"></td>
      <td> <input type=submit value="$cfg_m_submit" name="submit" class=submit> <input type=reset value="$cfg_m_reset" name="reset" class=submit> 
      </td>
    </tr>
  </table>
  <br>
  </font> <br>
  <table width="$cfg_table_width" border="0" cellspacing="0" cellpadding="15" class=ta-tblr align="center">
    <tr>
      <td>
        <div align="center"><b>[ <a href="$cfg_m_boturl">$cfg_m_botlink</a> ]</b></div>
      </td>
    </tr>
  </table>
  </form>
PostForm;

include("./inc/inc.page_bot.php");
?>