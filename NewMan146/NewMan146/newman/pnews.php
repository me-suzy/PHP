<?php
//******************************************************************************************
//**                                                                                      **
//** phpNewsManager v1.40                                                                 **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 18th.March,2003                                                         **
//******************************************************************************************
 
$title    = "phpNewsManager $newman_ver";
include "functions.php";
include ("header.php");
if ($psw == TRUE)
 if ($action == "edit") EditNews();
 else if ($action == "delete") DeleteNews();
 else if ($action == "multidel") MultiDelete($db_pnews,"id","pnews_del");
 else if ($action == "submit") SubmitNews();
 else ShowMain();

include ("footer.php");


function ShowMain()
{
 if(!check_version("4.1.0")) global $_GET;
 ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="news.php?action=add"><img src="gfx/news_big.jpg" width="32" height="32" border="0" alt="<?=_ADDNEWS;?>"/></a></td>
   <td width="100"><a href="news.php?action=add"> &nbsp;<?=_ADDNEWS;?></a></td>
   <td align="center">
   <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_pnews']) or die("<b>LINE 31:</b>".mysql_error());
    $num = mysql_num_rows($res);
    $myopt = ShowPages($num,$GLOBALS['page'],$GLOBALS['hits'],$GLOBALS['show']);
   ?>
   </td>
   <td align="right"><?=_SUBMITEDNEWS.": ".$num;?></td>
  </tr>
 </table>


 <form action="<?=$GLOBALS['PHP_SELF'];?>" name="myform" method="post">
 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td><font color="#<?=_COLOR05;?>"><?=_OPTIONS;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_NEWSHEADLINE;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_AUTHOR;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_DATE;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_SECTION;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_CHECK;?></font></td>
  </tr>  
  <?
   $res = mysql_query("SELECT * from ".$GLOBALS['db_pnews']." order by datum desc limit $myopt[0],$myopt[1]");
   while ($ar = mysql_fetch_array($res))
   {
    $datum = formatTimestamp($ar[datum]);
    $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_topic']." where id='$ar[category]'"); 
    $ar2 = mysql_fetch_array($res2);
    ?>
     <tr>
     <td width="100">
      <?if($_GET['action'] == "open" && $_GET['id'] == $ar[id]) $mact = "close"; else $mact = "open";?>
      <a href="<?=$GLOBALS['PHP_SELF'];?>?action=<?=$mact;?>&amp;id=<?=$ar[id];?>"><img src="gfx/open.gif" width="20" height="20" border="0" alt="" /></a> 
      <a href="<?=$GLOBALS['PHP_SELF'];?>?action=edit&amp;id=<?=$ar[id];?>"><img src="gfx/edit.gif" width="20" height="20" border="0" alt="" /></a> 
      <a href="javascript:Confirm('<?=$GLOBALS['PHP_SELF'];?>?action=delete&amp;page=<?=$GLOBALS['page'];?>&amp;id=<?=$ar[id];?>','<?=_DELETE.": ".eregi_replace("'","\'",$ar[headline])."?";?>');"><img src="gfx/trash.gif" width="20" height="20" border="0" alt="" /></a>
      <a href="<?=$GLOBALS['PHP_SELF'];?>?action=submit&amp;id=<?=$ar[id];?>"><img src="gfx/survey.gif" width="20" height="20" border="0" alt="" /></a> 
     </td> 
     <td><?=$ar[headline];?></td>
     <td align="right" valign="top"><?=$ar[author];?></td>
     <td align="right" width="100" valign="top"><?=$datum;?></td>
     <td align="right" valign="top"><img src="<?=$topic_url."/".$ar2[topicimage];?>" alt="<?=$ar2[topictext];?>" height="20" /></td>
     <td valign="top" align="center" width="40"><input type="checkbox" name="list[]" value="<?=$ar[id];?>"/></td>
    </tr>
    <?
     if($_GET['action'] == "open" && $_GET['id'] == $ar[id]) 
      echo "<tr bgcolor=\"#eeeeee\"><td style=\"padding-left:120px;\" colspan=\"6\" class=\"MojText\">$ar[preview]</td></tr>";
   }
   echo "</table>";
 ?>
 <div align="right">
 <input type="button" name="CheckAll" value="<?=_CHECK_ALL;?>" onclick="checkAll(document.myform)" class="news">
 <input type="button" name="UnCheckAll" value="<?=_UNCHECK_ALL;?>" onclick="uncheckAll(document.myform)" class="news">
 <input type="hidden" name="action" value="multidel">
 <input type="submit" value="<?=_DELETE;?>" class="news">
 </div>
 </form>
 <?
}

function SubmitNews()
{
 if(!check_version("4.1.0")) global $_GET;
 if(CheckPriv("pnews_submit") <> 1)
 { 
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }
 $res = mysql_query("SELECT * FROM ".$GLOBALS['db_pnews']." WHERE id='".$_GET['id']."'") or die("<b>LINE 127:</b>:".mysql_error());
 $ar = mysql_fetch_array($res);
 if(mysql_num_rows($res) == 1)
 {
  $ar[text] = eregi_replace("\'",'&acute;',$ar[text]);
  $ar[text] = eregi_replace("<",'&lt;',$ar[text]);
  $ar[text] = eregi_replace(">",'&gt;',$ar[text]);

  $ar[preview] = eregi_replace("\'",'&acute;',$ar[preview]);
  $ar[preview] = eregi_replace("<",'&lt;',$ar[preview]);
  $ar[preview] = eregi_replace(">",'&gt;',$ar[preview]);

  echo "INSERT INTO ".$GLOBALS['db_news']." VALUES(0,'$ar[headline]','$ar[author]','$ar[category]','$ar[picture]',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$ar[preview]','$ar[tekst]','0','0','1','')";
  mysql_query("INSERT INTO ".$GLOBALS['db_news']." VALUES(0,'$ar[headline]','$ar[author]','$ar[category]','$ar[picture]',CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,'$ar[preview]','$ar[tekst]','0','0','1','')") or die("Error 103:".mysql_error());
  mysql_query("DELETE FROM ".$GLOBALS['db_pnews']." WHERE id='".$_GET['id']."'");
  echo "<script type=\"text/javascript\">alert('"._PNEWS_SUBMITED."');</script>";
 }
 ShowMain();
}

function EditNews()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;

 // CHECK PRIVILEGIES
 if(CheckPriv("pnews_edit") <> 1)
 { 
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if ($_POST['confirm'] <> "true") 
 {
  $res = mysql_query("SELECT * from ".$GLOBALS['db_pnews']." where id='".$_GET['id']."'");
  $ar = mysql_fetch_array($res);

  $ar[headline] = ereg_replace( "&quot;","\"",$ar[headline]);
  $ar[headline] = ereg_replace( "&acute;","'",$ar[headline]);

    $ar[tekst] = ereg_replace( "<br/>","\n",$ar[tekst]);
    $ar[tekst] = ereg_replace( "&quot;","\"",$ar[tekst]);
    $ar[tekst] = ereg_replace( "&acute","'",$ar[tekst]);

    $ar[preview] = ereg_replace( "<br/>","\n",$ar[preview]);
    $ar[preview] = ereg_replace( "&quot;","\"",$ar[preview]);
    $ar[preview] = ereg_replace( "&acute;","'",$ar[preview]);

    ?>
   <table width="630" cellspacing="2" cellpadding="0" class="MojText">
    <tr>
     <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/news_big.jpg" width="32" height="32" border="0" alt="<?=_ADDNEWS;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_EDITNEWS;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_pnews']) or die("<b>Line 246:</b>".mysql_error());
    echo _SUBMITEDNEWS.": ".mysql_num_rows($res);
    ?>
   </td>
  </tr>
 </table>
 
 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td>&nbsp;</td>
  </tr>
 </table>

  <form action="<?=$GLOBALS['PHP_SELF'];?>" method="post">
        <?=_NEWSHEADLINE;?><br/>
        <input type="text" name="headline" size="60" class="news" value="<?=$ar[headline];?>"/><br/>
        <?=_SECTION;?>:<br/>
        <select name="section" class="news" >
        <?php
         $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_topic']); 
         while ($ar2 = mysql_fetch_array($res2))
          {
           print "<option name=section value=\"$ar2[topicname]\" ";
           if ($ar2[topicname] == $ar[category]) { print "selected=\"selected\" ";}
           print ">$ar2[topictext]</option>";
          }
        ?>
        </select><br/>
        <?=_PREVIEW;?><br/>
        <textarea name=preview class="news" cols="60" rows="6"><?="$ar[preview]";?></textarea><br/>
        <?=_MESSAGE;?><br/>
        <textarea name="message" cols="60" class="news" rows="10"><?="$ar[tekst]";?></textarea>
        <input type="hidden" name="action" value="edit"><br/><br/>
        <input type="hidden" name="confirm" value="true"/>
        <input type="hidden" name="id" value="<?=$_GET['id'];?>"/>
        <input type="hidden" name="time" values="<?=$ar[datum];?>"/>
        <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);" />
        </form>
       </td>
      </tr>
    </table> 
   <?
  }
 if ($_POST['confirm'] == "true") 
   {
    $_POST['message'] = ereg_replace( "\n", "<br/>",$_POST['message']);
    /*$_POST['message'] = ereg_replace( "\"", "&quot;",$_POST['message']);*/
    $_POST['message'] = ereg_replace( "'", "&acute",$_POST['message']);

    /*$_POST['headline'] = ereg_replace( "\"", "&quot;",$_POST['headline']);*/
    $_POST['headline'] = ereg_replace( "\n", " ",$_POST['headline']);
    $_POST['headline'] = ereg_replace( "'", "&acute;",$_POST['headline']);

    $_POST['preview'] = ereg_replace( "\n", "<br/>",$_POST['preview']);
    /*$_POST['preview'] = ereg_replace( "\"", "&quot;",$_POST['preview']);*/
    $_POST['preview'] = ereg_replace( "'", "&acute;",$_POST['preview']);
    $res2 = mysql_query("SELECT datum FROM ".$GLOBALS['db_pnews']." WHERE id='".$_POST['id']."'");
    $mr= mysql_fetch_array($res2);
    $res = mysql_query("UPDATE ".$GLOBALS['db_pnews']." SET headline='".$_POST['headline']."', category='".$_POST['section']."', preview='".$_POST['preview']."', tekst='".$_POST['message']."', datum='$mr[datum]' WHERE id='".$_POST['id']."'"); 
    ShowMain();
   } 
 }

function DeleteNews()
{
 if(!check_version("4.1.0")) global $_GET; // only need if you're running 4.06 or lower version of PHP
 // CHECK PRIVILEGIES
 if(CheckPriv("pnews_del") == 1) 
  mysql_query("DELETE FROM ".$GLOBALS['db_pnews']." where id='".$_GET['id']."'"); 
 else
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
 unset($GLOBALS['id']);
 ShowMain();
 return;
}
?>