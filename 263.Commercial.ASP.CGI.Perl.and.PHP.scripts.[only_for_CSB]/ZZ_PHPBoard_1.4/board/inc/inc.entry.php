<?php
echo <<<ENTRY01
<font face="$cfg_type">
<table width="$cfg_table_width" border="0" cellspacing="0" cellpadding="15" align="center">
<tr>
<td class=ta-tblr>
      <center>
        <font size="6" face="$cfg_type">$subject</font> 
      </center>
</td></tr>
<tr>
<td class=ta-blr> $cfg_p_mesi1 <font face="$cfg_type">
ENTRY01;
if (preg_match("/[a-zA-Z0-9]/",$email)){
	echo "<a href=\"mailto:$email\" title=\"Email:  $email \">";
} else {
	echo "<b>";
}
echo $name;
if (preg_match("/[a-zA-Z0-9]/",$email)){
	echo "</a>";
} else {
	echo "</b>";
}	
echo <<<ENTRY02
	</font> 
      $cfg_p_mesi2 <font face="$cfg_type">$time</font>:
	  $answer
      <p>
<font face="$cfg_type"><b>$message</b></font> 
      <p><br>
ENTRY02;
if (preg_match("/[a-zA-Z0-9]/",$url)){
	if (!preg_match("/[a-zA-Z0-9]/",$url_title)){
		$url_title = $url;
	}
echo "<b>&#8226; &nbsp;</b><a href=\"$url\">$url_title</a><br><br>";
}

echo <<<ENTRY03
</td>
</tr>
<tr><a name="followups">
<td class=ta-blr>

$follow_up

</td></tr></table>
<br>
<table width="$cfg_table_width" border="0" cellspacing="0" cellpadding="15" align="center">
<tr>
<td>
<b><a name="postfp">$cfg_p_write_fu</a></b>
</td></tr></table>
<form method=POST action="send.php?post=$show">
<input type=hidden name="follow_up" value="1">
<input type=hidden name="msg_num" value="$show">
  <table border="0" align="center">
    <tr> 
      <td width="80"><b><font size="2">$cfg_m_f_name</font></b></td>
      <td> <input type=text name="name" size=50> </td>
    </tr>
    <tr> 
      <td width="80"><b><font size="2">$cfg_m_f_email</font></b></td>
      <td> <input type=text name="email" size=50> </td>
    </tr>
    <tr> 
      <td width="80"><b><font size="2">$cfg_m_f_subject</font></b></td>
      <td> <input type=text name="subject" size=50> </td>
    </tr>
    <tr> 
      <td width="80"  valign="top"><b><font size="2">$cfg_m_f_message</font></b></td>
      <td> <textarea name="message" COLS=43 ROWS=10></textarea> <p> </td>
    </tr>
    <tr> 
      <td width="80"><b><font size="2">$cfg_m_f_linkurl</font></b></td>
      <td> <input type=text name="url" size=50> </td>
    </tr>
    <tr> 
      <td width="80"><b><font size="2">$cfg_m_f_urltitel</font></b></td>
      <td> <input type=text name="url_title" size=50> </td>
    </tr>
    <tr> 
      <td width="80"></td>
      <td> <br>
        <input type=submit value="$cfg_p_write_fu" class=submit> <input type=reset value="$cfg_m_reset" class=submit> 
      </td>
    </tr>
  </table>
</form>
<br>
<table width="$cfg_table_width" border="0" cellspacing="0" cellpadding="15" align="center" class=ta-tblr>
<tr><td>
<center>[ <a href="#followups" class=navoff onmouseover="this.className='navover'" onmouseout="this.className='navoff'">$cfg_p_m_f_ups</a> ] [ <a href="#postfp" class=navoff onmouseover="this.className='navover'" onmouseout="this.className='navoff'">$cfg_p_m_post_f_ups</a> ] [ <a href="forum.php" class=navoff onmouseover="this.className='navover'" onmouseout="this.className='navoff'">$cfg_p_m_forum</a> ] [ <a href="faq.php" class=navoff onmouseover="this.className='navover'" onmouseout="this.className='navoff'">$cfg_p_m_faq</a> ]</center>
</tr></td>
</table>
</font>
ENTRY03;
?>