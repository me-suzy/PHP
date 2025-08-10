<?php
//******************************************************************************************
//**                                                                                      **
//** phpNewsManager v1.40                                                                 **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 17th.March,2003                                                         **
//******************************************************************************************

$title = "phpNewsManager $newman_ver";
$makejs = "category";
include "functions.php";
include "header.php";
if ($psw == 1)
{
 if ($action == "edit")        EditCategory();
 else if ($action == "delete") DeleteCategory();
 else if ($action == "multidel") MultiDelete($db_topic,"id","cat_del");
 else if ($action == "add")    AddCategory();
 else if ($action == "upload") UploadPicture($GLOBALS['topic_path'],_UPLOADLOGO,"cat_ul");
 else ShowMain();
}
include ("footer.php"); 



function ShowMain()
{
 if(!check_version("4.1.0")) global $_GET;
 ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"><img src="gfx/category_big.jpg" width="32" height="32" border="0" alt="<?=_ADDCATEGORY;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"> &nbsp;<?=_ADDCATEGORY;?></a></td>
   <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><img src="gfx/upload_picture_big.jpg" width="32" height="32" border="0" alt="<?=_UPLOADLOGO;?>"/></a></td>
   <td><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><?=_UPLOADLOGO;?></a></td>
  <td align="center">
   <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_topic']) or die("<b>LINE 31:</b>".mysql_error());
    $num = mysql_num_rows($res);
    $myopt = ShowPages($num,$GLOBALS['page'],$GLOBALS['hits'],$GLOBALS['show']);
   ?>
   </td>
   <td align="right"><?=_SUBMITEDCATEGORY.": ".$num;?></td>
  </tr>
 </table>
 
 <form action="<?=$GLOBALS['PHP_SELF'];?>" name="myform" method="post">
 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td><font color="#<?=_COLOR05;?>"><?=_OPTIONS;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_NAME;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_NUMBER;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_IMAGE;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_CHECK;?></font></td>
  </tr>
    <?php
    $res = mysql_query("SELECT * from ".$GLOBALS['db_topic']." LIMIT $myopt[0],$myopt[1]");
    while ($ar = mysql_fetch_array($res))
     {
      $res2 = mysql_query("SELECT * from ".$GLOBALS['db_news']." where category = $ar[id]");
      $num = mysql_num_rows($res2);
      ?>
      <tr>
       <td width="44">
        <a href="<?=$GLOBALS['PHP_SELF'];?>?action=edit&amp;id=<?=$ar[id];?>"><img src="gfx/edit.gif" alt="" width="20" height="20" border="0"/></a> 
        <a href="javascript:Confirm('<?=$GLOBALS['PHP_SELF'];?>?action=delete&amp;id=<?=$ar[id];?>','<?=_DELETE.": ".eregi_replace("'","\'",$ar[topictext])."?";?>');"><img src="gfx/trash.gif" width="20" height="20" border="0" alt="" /></a>
       </td> 
       <td valign="top"><?=$ar[topictext];?></td>
       <td valign="top"><?=$num;?></td>
       <td valign="top" width="100"><img height="20" src="<?=$GLOBALS['topic_url']."/".$ar[topicimage];?>" alt="" /></td>
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
}

// ************ ADD CATEGORY **************
function AddCategory()
{
 if(!check_version("4.1.0")) global $_POST;
 
 // CHECK PRIVILEGIES
 if(CheckPriv("cat_add") <> 1)
 { 
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if (!empty($_POST['topictext']))
 {
  $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_topic']." WHERE topictext = '".$_POST['topictext']."'");
  if(mysql_num_rows($res2)==0) {$res = mysql_query("INSERT INTO ".$GLOBALS['db_topic']." VALUES(0,'".$_POST['picture']."','".$_POST['topictext']."','0')") or die ("<b>Error5:</B> Invalid Query");}
  ShowMain();
  return;
 }
 if(empty($_POST['topictext']))
 { 
  ?>
  <table width="630" cellspacing="2" cellpadding="0" class="MojText">
   <tr>
    <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/category_big.jpg" width="32" height="32" border="0" alt="<?=_ADDCATEGORY;?>"/></a></td>
    <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
    <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><img src="gfx/upload_picture_big.jpg" width="32" height="32" border="0" alt="<?=_UPLOADLOGO;?>"/></a></td>
    <td><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><?=_UPLOADLOGO;?></a></td>
    <td align="center"><font size="4" face="Arial"> <b><?=_ADDCATEGORY;?></b></font></td>
    <td align="right">
    <?
     $res = mysql_query("SELECT * FROM ".$GLOBALS['db_topic']) or die("<b>Line 246:</b>".mysql_error());
     echo _SUBMITEDCATEGORY.": ".mysql_num_rows($res);
    ?>
    </td>
   </tr>
  </table>
  <table width="630" cellspacing="2" cellpadding="1" class="MojText"><tr bgcolor="#<?=_COLOR02;?>"><td>&nbsp;</td></tr></table>

  <table width="630" cellspacing="2" cellpadding="0" class="MojText">
   <tr>
    <td>
     <form action="<?=$GLOBALS['PHP_SELF'];?>" method="post" name="forma">
      <?=_CATEGORYNAME;?>:<br/>
      <input type="text" name="topictext" class="news" value="<?=$GLOBALS['topictext'];?>" size="60" /><br/>
      <?=_IMAGE;?><br/>
      <select name="picture" size="8" class="news" onchange="Swap();" onclick="Swap();">
      <?
       $d = dir($GLOBALS['topic_path']);
       $x=0;
       while($entry=$d->read()) 
       {
       	$x++;
       	if($x > 2) 
       	{
       	 echo '<option value="'.$entry.'"'; 
       	 if($entry == $ar[topicimage])
            echo ' selected="selected" ';
       	 echo '>'.$entry.'</option>';
       	}
       }
       $d->close();
      ?>
      </select>
      <p><img name="button" src="http://www.skinbase.org/gfx/partners/linkus.jpg" alt="" border="0"/></p>
      <input type="hidden" name="action" value="add" />
      <input type="submit" value="<?echo _SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);" />
     </form>
    </td>
   </tr>
  </table> 
  <?
 } 
}

function EditCategory()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;

 // CHECK PRIVILEGIES
 if(CheckPriv("cat_edit") <> 1)
 { 
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if ($_POST['confirm'] <> "true") 
 {
  $res = mysql_query("SELECT * from ".$GLOBALS['db_topic']." WHERE id='".$_GET['id']."'");
  $ar = mysql_fetch_array($res);
  $ar[topictext] = ereg_replace( "&quot;","\"",$ar[topictext]);
  $ar[topictext] = ereg_replace( "&acute;","'",$ar[topictext]);
  ?>
  <table width="630" cellspacing="2" cellpadding="0" class="MojText">
   <tr>
    <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/category_big.jpg" width="32" height="32" border="0" alt="<?=_ADDCATEGORY;?>"/></a></td>
    <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
    <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><img src="gfx/upload_picture_big.jpg" width="32" height="32" border="0" alt="<?=_UPLOADLOGO;?>"/></a></td>
    <td><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><?=_UPLOADLOGO;?></a></td>
    <td align="center"><font size="4" face="Arial"> <b><?=_EDITCATEGORY;?></b></font></td>
    <td align="right">
    <?
     $res = mysql_query("SELECT * FROM ".$GLOBALS['db_topic']) or die("<b>Line 246:</b>".mysql_error());
     echo _SUBMITEDCATEGORY.": ".mysql_num_rows($res);
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
      <?=_CATEGORYNAME;?>:<br/>
      <input type="text" name="topictext" class="news" value="<?=$ar[topictext];?>" size="60"/><br/>
      <?=_IMAGE;?><br/>
      <select name="picture" size="8" class="news" onchange="Swap();" onclick="Swap();">
       <?
        $d = dir($GLOBALS['topic_path']);
        $x=0;
	while($entry=$d->read()) {$x++;if ($x > 2) 
	{
	 echo '<option value="'.$entry.'"'; 
	 if($entry == $ar[topicimage])
	   echo ' selected="selected" ';
	 echo ">$entry</option>";}
	}
        $d->close();
       ?>
      </select>
      <p><img name="button" src="<?=$GLOBALS['topic_url'];?>/<?=$ar[topicimage];?>" border="0" alt="" /></p>
      <input type="hidden" name="id" value="<?=$_GET['id'];?>"/>
      <input type="hidden" name="action" value="edit"/>
      <input type="hidden" name="confirm" value="true"/>
      <input type="submit" value="<?echo _SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);" />
     </form>
    </td>
   </tr>
  </table> 
  <?
 }
 if ($_POST['confirm'] == "true") 
 {
  $_POST['topictext'] = ereg_replace( "'", "&acute;",$_POST['topictext']);
  $_POST['topictext'] = ereg_replace( "\"", "&quot;",$_POST['topictext']);
  $res = mysql_query("UPDATE ".$GLOBALS['db_topic']." SET topicimage='".$_POST['picture']."', topictext='".$_POST['topictext']."' WHERE id='".$_POST['id']."'") or die ("<B>Error4:</B> Invalid query"); 
  ShowMain();
 } 
}

function MultiDeleteCategory()
{
 if(!check_version("4.1.0")) global $_POST; // only need if you're running 4.06 or lower version of PHP
 
 if(is_array($_POST['list']))
   while(list($key, $value) = each ($_POST['list'])) 
     mysql_query("DELETE FROM ".$GLOBALS['db_topic']." WHERE id='".$value."'");

 ShowMain();
 return;
}

function DeleteCategory()
{
 if(!check_version("4.1.0")) global $_GET; // only need if you're running 4.06 or lower version of PHP
 // CHECK PRIVILEGIES
 if(CheckPriv("cat_del") == 1) 
   mysql_query("DELETE FROM ".$GLOBALS['db_topic']." WHERE id='".$_GET['id']."'");
 else
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
 
 unset($GLOBALS['id']);
 ShowMain();
 return;
}

?>
