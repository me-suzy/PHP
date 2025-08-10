<?php
echo <<<ERROR
<font face="$cfg_type">
<table width="$cfg_table_width" border="0" cellspacing="0" cellpadding="15" align="center">
<tr>
<td class=ta-tblr>
<div align="center">
<b><font size="6">ERROR: $error_msg01</font></b>
</div></td>
</tr>
<tr>
<td class=ta-blr>
<div align="center">$error_msg02</div>
</td></tr></table>
<br>
<form method=POST action="send.php?post=$post">
<table border="0" align="center">
<tr>
<td width="80"><b><font size="2" $col01>$cfg_m_f_name</font></b></td>
<td>
<input type=text name="name" value="$name" size=50>
</td>
</tr>
<tr>
<td width="80"><b><font size="2" $col02>$cfg_m_f_email</font></b></td>
<td>
<input type=text name="email" value="$email" size=50>
</td>
</tr>
<tr>
<td width="80"><b><font size="2" $col03>$cfg_m_f_subject</font></b></td>
<td>
<input type=text name="subject" value="$subject" size=50>
</td>
</tr>
<tr>
<td width="80" valign="top"><b><font size="2" $col04>$cfg_m_f_message</font></b></td>
<td>
<textarea COLS=43 ROWS=10 name="message">
$message
</textarea><p>
</td>
</tr>
<tr>
<td width="80"><b><font size="2">$cfg_m_f_linkurk</font></b></td>
<td>
<input type=text name="url" value="$url" size=50>
</td>
</tr>
<tr>
<td width="80"><b><font size="2">$cfg_m_f_linktitel</font></b></td>
<td>
<input type=text name="url_title" value="$url_title" size=50>
</td>
</tr>
<tr>
<td width="80"></td>
<td>
<br><input type=submit value="$cfg_m_submit" class=submit> <input type=reset value="$cfg_m_reset" class=submit>
</td>
</tr>
</table>
</form>
<br>
<table width="$cfg_table_width" border="0" cellspacing="0" cellpadding="15" align="center" class=ta-tblr>
<tr><td>
<center>[ <a href="forum.php">$cfg_p_m_forum</a> ] [ <a href="faq.php">$cfg_p_m_faq</a> ]</center>
</tr></td>
</table>
</font>
ERROR;
?>