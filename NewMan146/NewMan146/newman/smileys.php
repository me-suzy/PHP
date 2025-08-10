<?php
//******************************************************************************************
//**                                                                                      **
//** phpNewsManager v1.30                                                                 **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 27th.May,2002                                                           **
//******************************************************************************************

$title    = "phpNewsManager $newman_ver";
$makejs = "smileys";
include "functions.php";
include "header.php";
// MAIN CODE STARTS HERE
if ($psw == 1)
{
 if ($action == "edit") EditSmiley();
 else if ($action == "delete") DeleteSmiley();
 else if ($action == "multidel") MultiDelete($db_smileys,"id","smiley_del");
 else if ($action == "add") AddSmiley();
 else if ($action == "upload") { UploadPicture($GLOBALS['smileys_path'],_UPLOADSMILEY,"smiley_ul"); }
 else ShowMain();
}
include "footer.php";
 
function ShowMain()
{
 if(!check_version("4.1.0")) global $_GET;
 ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"><img src="gfx/smiley_big.jpg" width="32" height="32" border="0" alt="<?=_ADDSMILEY;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"> &nbsp;<?=_ADDSMILEY;?></a></td>
   <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><img src="gfx/upload_picture_big.jpg" width="32" height="32" border="0" alt="<?=_UPLOADLOGO;?>"/></a></td>
   <td><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><?=_UPLOADLOGO;?></a></td>
   <td align="center">
   <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_smileys']) or die("<b>LINE 31:</b>".mysql_error());
    $num = mysql_num_rows($res);
    $myopt = ShowPages($num,$GLOBALS['page'],$GLOBALS['hits'],$GLOBALS['show']);
   ?>
   </td>
   <td align="right"><?=_SUBMITEDSMILEYS.": ".$num;?></td>
  </tr>
 </table>

 <form action="<?=$GLOBALS['PHP_SELF'];?>" name="myform" method="post">
  <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td><font color="#<?=_COLOR05;?>"><?=_OPTIONS;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_IMAGE;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_NAME;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_SMILEYEMOTION;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_CHECK;?></font></td>
  </tr>  
  <?
   $res = mysql_query("SELECT * FROM ".$GLOBALS['db_smileys']." ORDER BY id DESC LIMIT $myopt[0],$myopt[1]");
   while ($ar = mysql_fetch_array($res))
   {
    ?>
    <tr>
     <td width="44">
      <a href="<?=$GLOBALS['PHP_SELF'];?>?action=edit&amp;id=<?=$ar[id];?>"><img src="gfx/edit.gif" width="20" height="20" border="0" alt=""/></a> 
      <a href="javascript:Confirm('<?=$GLOBALS['PHP_SELF'];?>?action=delete&amp;id=<?=$ar[id];?>','<?=_DELETE.": ".eregi_replace("'","\'",$ar[code])." - ".eregi_replace("'","\'",$ar[emotion])."?";?>');"><img src="gfx/trash.gif" width="20" height="20" border="0" alt="" /></a>
     </td> 
     <td valign="top" width="50" align="center"><img src="<?=$GLOBALS['smiley_url']."/".$ar[smile];?>" alt=""/></td>
     <td valign="top"><?=$ar[code];?></td>
     <td valign="top"><?=$ar[emotion];?></td>
     <td valign="top" align="center" width="40"><input type="checkbox" name="list[]" value="<?=$ar[id];?>"/></td>
    </tr>
    <?
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
   if(!file_exists($GLOBALS['smileys_path']))
     echo "<br/><br/><center><font size=3 face=arial><b>ERROR: CHECK YOUR \$smileys_path VARIABLE IN db.inc.php FILE !!!</b></font></center><br/><br/>";
}

function AddSmiley()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;
 
 // CHECK PRIVILEGIES
 if(CheckPriv("smiley_add") <> 1)
 { 
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if (!empty($_POST['code']))
 {
  $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_smileys']." WHERE code = '".$_POST['code']."'");
  if(mysql_num_rows($res2)==0) {$res = mysql_query("INSERT INTO ".$GLOBALS['db_smileys']." VALUES(0,'".$_POST['code']."','".$_POST['picture']."','".$_POST['emotion']."')") or die ("<B>Error5:</B> Invalid Query");}
  ShowMain();
  return 0;
 }
 if (empty($_POST['code']))
  { 
   ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/smiley_big.jpg" width="32" height="32" border="0"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><img src="gfx/upload_picture_big.jpg" width="32" height="32" border="0" alt="<?=_UPLOADLOGO;?>"/></a></td>
   <td><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><?=_UPLOADLOGO;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_ADDSMILEY;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_smileys']) or die("<b>Line 246:</b>".mysql_error());
    echo _SUBMITEDSMILEYS.": ".mysql_num_rows($res);
    ?>
   </td>
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
      <form action="<?=$GLOBALS['PHP_SELF'];?>" method="post" name="forma">
       <?=_SMILEYCODE;?>:<br/>
       <input type="text" name="code" class="news" size="5"/><br/>
       <?=_SMILEYEMOTION;?>:<br/>
       <input type="text" name="emotion" class="news" size="40"/><br/>
        <?=_IMAGE;?><br/>
       <select name="picture" size="8" class="news" onclick="Swap();" onchange="Swap();">
       <?
        $d = dir($GLOBALS['smileys_path']);
        $x=0;
	while($entry=$d->read()) {$x++;if ($x > 2) {echo "<option value=\"$entry\""; if ($entry == $ar[topicimage]){echo " selected=\"selected\" ";};echo ">$entry</option>";}}
        $d->close();
       ?>
       </select>
       <p><img name="button" src="http://www.skinbase.org/gfx/partners/linkus.jpg" border="0" alt="" /></p>
       <br/>
       <input type="hidden" name="action" value="add"/>
       <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);"/>
      </form>
     </td>
    </tr>
   </table> 
  <?
 } 
}

function EditSmiley()
{
 if(!check_version("4.1.0")) global $_POST,$_GET;

 // CHECK PRIVILEGIES
 if(CheckPriv("smiley_edit") <> 1)
 { 
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if ($_POST['confirm'] <> "true") 
  {
   $res = mysql_query("SELECT * from ".$GLOBALS['db_smileys']." where id='".$_GET['id']."'");
   $ar = mysql_fetch_array($res);
   ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/smiley_big.jpg" width="32" height="32" border="0"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><img src="gfx/upload_picture_big.jpg" width="32" height="32" border="0" alt="<?=_UPLOADLOGO;?>"/></a></td>
   <td><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><?=_UPLOADLOGO;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_EDITSMILEY;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_smileys']) or die("<b>Line 246:</b>".mysql_error());
    echo _SUBMITEDSMILEYS.": ".mysql_num_rows($res);
    ?>
   </td>
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
      <form action="<?=$GLOBALS['PHP_SELF'];?>" method="post" name="forma">
       <?=_SMILEYCODE;?>:<br/>
       <input type="text" name="code" class="news" value="<?=$ar[code];?>" size="5"/><br/>
       <?=_SMILEYEMOTION;?>:<br/>
       <input type="text" name="emotion" class="news" value="<?=$ar[emotion];?>" size="40"/><br/>
       <?=_IMAGE;?><br/>
       <select name="picture" size="8" class="news" onclick="Swap();" onchange="Swap();">
       <?
        $d = dir($GLOBALS['smileys_path']);
        $x=0;
	while($entry=$d->read()) {$x++;if ($x > 2) 
	{
	 echo "<option value=\"$entry\""; if ($entry == $ar[smile]){echo " selected=\"selected\" ";};echo ">$entry</option>";}
	}
        $d->close();
       ?>
       </select>
       <p><img name="button" src="<?=$GLOBALS['smileys_url']."/$ar[smile]";?>" border="0" alt="" /></p>

       <input type="hidden" name="id" value="<?=$_GET['id'];?>"/>
       <input type="hidden" name="action" value="edit"/>
       <input type="hidden" name="confirm" value="true"/>
     <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);"/>
      </form>
     </td>
    </tr>
   </table> 
   <?
  }
 if ($_POST['confirm'] == "true") 
  {
   $res = mysql_query("UPDATE ".$GLOBALS['db_smileys']." SET smile='".$_POST['picture']."', code='".$_POST['code']."', emotion='".$_POST['emotion']."' WHERE id='".$_POST['id']."'") or die ("<B>Error4:</B> Invalid query"); 
   ShowMain();
  } 
}

function DeleteSmiley()
{
 if(!check_version("4.1.0")) global $_GET; // only need if you're running 4.06 or lower version of PHP
 // CHECK PRIVILEGIES
 if(CheckPriv("smiley_del") == 1) 
   $res = mysql_query("DELETE FROM ".$GLOBALS['db_smileys']." where id='".$_GET['id']."'"); 
 else
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
 unset($GLOBALS['id']);
 ShowMain();
 return;
} 
?>
