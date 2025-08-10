<?
//******************************************************************************************
//**                                                                                      **
//** phpNewsManager v1.30                                                                 **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 10th.June,2002                                                          **
//******************************************************************************************

$title    = "phpNewsManager $newman_ver";
include "functions.php";
include ("header.php");
?>

<table width="630" cellspacing="2" cellpadding="0" class="MojText">
 <tr>
  <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/optimize32.jpg" width="32" height="32" border="0" alt="<?=_ADDNEWS;?>"/></a></td>
  <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
  <td align="center"><font size="4" face="Arial"> <b><?=_OPTIMIZEDATABASE;?></b></font></td>
  <td align="right"></td>
 </tr>
</table>
<table width="630" cellspacing="2" cellpadding="1" class="MojText"><tr bgcolor="#<?=_COLOR02;?>"><td>&nbsp;</td></tr></table>

<table width="630" cellspacing="2" cellpadding="0" class="MojText">
 <tr>
  <td>
   <table cellspacing="1" cellpadding="2" class="MojText">
    <?
     $result = mysql_list_tables($db_name);
     while ($row = mysql_fetch_row($result)) 
     {
      $result1 = mysql_query("OPTIMIZE TABLE $row[0];");
      echo ("<tr><td width=\"25\"><img src=\"gfx/optimize.gif\" width=\"20\" height=\"20\" alt=\"\"/></td><td><b>$row[0]</b> "._OPTIMIZED."</td></tr>");
     }
     mysql_free_result($result);
     ?>
     </table>
   </td>
  </tr>
 </table>
   
 <?include ("footer.php");?>
