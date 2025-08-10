<?
//******************************************************************************************
//**                                                                                      **
//** phpNewsManager v1.30                                                                 **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 10th.June,2002                                                          **
//******************************************************************************************

$title    = "phpNewsManager $newman_ver";
include ("colors.php");
include "functions.php";
include ("header.php");
 
if(CheckPriv("rss_edit") == 1)
{
 ?>
   <table width="630" cellspacing="2" cellpadding="0" class="MojText">
    <tr>
     <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/news_big.jpg" width="32" height="32" border="0" alt=""/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center" width="510"><font size="4" face="Arial"> <b><?=_RSSSETTINGS;?></b></font></td>
  </tr>
 </table>
  <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td>&nbsp;</td>
  </tr>
 </table>
  

 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td>
    <?
     if($action=="changeit")
       mysql_query("UPDATE $db_rss SET filename='$filename', number='$number', title='$title', link='$link', description='$description', auto='$auto'") or die("ERROR 18:".mysql_error());
     $res = mysql_query("SELECT * FROM $db_rss");
     $ar = mysql_fetch_array($res);
    ?>
    <form action="<?=$PHP_SELF;?>" method="post">
     <?=_NAMEOFRSSFILE;?>:<br/>
     <input type="text" name="filename" class="news" size="30" value="<?=$ar[filename];?>"/><br/>
     <?=_NUMBEROFNEWS;?>:<br/>
     <input type="text" name="number" size="30" class="news" value="<?=$ar[number];?>"/><br/>
     <?=_RSSTITLE;?>:<br/>
     <input type="text" name="title" size="30" class="news" value="<?=$ar[title];?>"/><br/>
     <?=_RSSLINK;?>:<br/>
     <input type="text" name="link" size="30" class="news" value="<?=$ar[link];?>"/><br/>
     <?=_RSSDESCRIPTION;?>:<br/>
     <input type="text" name="description" size="30" class="news" value="<?=$ar[description];?>"/><br/>
     <br/>
     <input type="checkbox" <?if($ar[auto]==1){echo " checked=\"checked\" ";}?> size="30" name="auto" value="1"/>&nbsp;<?=_AUTOCREATERSS;?><br/>
     <br/>
     <input type="hidden" name="action" value="changeit"/>
     <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);" />
    </form>
   </td>
  </tr>
 </table>
<?
}
else
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";

include ("footer.php");
?>
