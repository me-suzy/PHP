<?
//******************************************************************************************
//** phpNewsManager: NEWS v1.40                                                           **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 19th.March,2003                                                         **
//******************************************************************************************
  
$title = "phpNewsManager $newman_ver";
include "functions.php";
include ("header.php");

if ($psw == TRUE)
 if ($action == "edit")        EditStory();   
 else if ($action == "delete") DeleteStory(); 
 else if ($action == "multidel") MultiDelete($db_story,"id","story_del");
 else if ($action == "add")    AddStory();    
 else if ($action == "upload") UploadPicture($GLOBALS['story_path'],_ADDSTORY,"story_add");
 else if ($action == "logout") Logout();
 else ShowMain();
include ("footer.php");
 
function ShowMain()
{
 if(!check_version("4.1.0")) global $_GET;
 ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"><img src="gfx/news_big.jpg" width="32" height="32" border="0" alt="<?=_ADDSTORY;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"> &nbsp;<?=_ADDSTORY;?></a></td>
   <td align="center">
   <?
    if($GLOBALS['filter'] == "") unset($GLOBALS['filter']);
    $query  = "SELECT * FROM ".$GLOBALS['db_story']." ";
    if(!empty($GLOBALS['filter']) || !empty($GLOBALS['txtfilter'])) $query .= "WHERE ";
    if(!empty($GLOBALS['filter'])) $query .= " category='".$GLOBALS['filter']."' ";
    if(!empty($GLOBALS['filter']) && !empty($GLOBALS['txtfilter'])) $query .= "AND ";
    if(!empty($GLOBALS['txtfilter'])) $query .= " headline LIKE '%".$GLOBALS['txtfilter']."%' ";

    $res = mysql_query($query) or die("<b>LINE 42:</b>".mysql_error());
    $num = mysql_num_rows($res);
    $myopt = ShowPages($num,$GLOBALS['page'],$GLOBALS['hits'],$GLOBALS['show']);

    $query .= "ORDER BY datum DESC LIMIT $myopt[0],$myopt[1]";

    $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_story']) or die("<b>LINE 31:</b>".mysql_error());
    $num2 = mysql_num_rows($res2);
   ?>
   </td>
   <td align="right"><?=_SUBMITEDSTORIES.": ".$num2;?></td>
  </tr>
 </table>

 <div align="right" style="padding-right:5px;">
  <form action="<?=$GLOBALS['PHP_SELF'];?>" method="post" style="padding: 0;">
   <?=_FILTER;?>:&nbsp;
   <input type="text" name="txtfilter" value="<?=$GLOBALS['txtfilter'];?>" class="news">
   &nbsp;
   <select name="filter" class="news">
    <option value="">/</option>
     <?
      $rest = mysql_query("SELECT * FROM ".$GLOBALS['db_topic']." ORDER BY topictext");
      while($art = mysql_fetch_array($rest))
      {
       echo '<option value="'.$art[id].'" ';
       if($GLOBALS['filter'] == $art[id]) echo 'selected="selected"';
       echo '>'.$art[topictext].'</option>';
      }
     ?>
    </select>
   <input type="submit" value="show" class="news">
  </form>
 </div>
 
 <form action="<?=$GLOBALS['PHP_SELF'];?>" name="myform" method="post" style="padding: 0;">
 <table width="630" cellspacing="1" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td><?=_OPTIONS;?></td>
   <td><?=_STORYHEADLINE;?></td>
   <td><?=_AUTHOR;?></td>
   <td><?=_UPDATED;?></td>
   <td><?=_DATE;?></td>
   <td><?=_SECTION;?></td>
   <td><?=_CHECK;?></td>
  </tr>  
  <?
   $res = mysql_query($query) or die("<b>Line 84:</b>".mysql_error());
   while ($ar = mysql_fetch_array($res))
   {
    $datum = formatTimestamp($ar[datum]);
    $datum2 = formatTimestamp($ar[datum2]);
    $res1 = mysql_query("SELECT * FROM ".$GLOBALS['db_topic']." WHERE id='".$ar[category]."'");
    $ar1 = mysql_fetch_array($res1);
    ?>
    <tr>
     <td width="66">
      <?if($_GET['action'] == "open" && $_GET['id'] == $ar[id]) $mact = "close"; else $mact = "open";?>
      <a href="<?=$GLOBALS['PHP_SELF'];?>?action=<?=$mact;?>&amp;id=<?=$ar[id];?>"><img src="gfx/open.gif" width="20" height="20" border="0" alt="" /></a> 
      <a href="<?=$GLOBALS['PHP_SELF'];?>?action=edit&amp;id=<?=$ar[id];?>"><img src="gfx/edit.gif" width="20" height="20" border="0" alt="" /></a> 
      <a href="javascript:Confirm('<?=$GLOBALS['PHP_SELF'];?>?action=delete&amp;page=<?=$GLOBALS['page'];?>&amp;id=<?=$ar[id];?>','<?=_DELETE.": ".eregi_replace("'","\'",$ar[headline])."?";?>');"><img src="gfx/trash.gif" width="20" height="20" border="0" alt="" /></a>
     </td> 
     <td><?=$ar[headline];?></td>
     <td align="right" valign="top"><?=$ar[author];?></td>
     <td align="right" width="70" valign="top"><?=$datum2;?></td>
     <td align="right" width="70" valign="top"><?=$datum;?></td>
     <td align="right" width="30" valign="top"><?if(!empty($ar1[topicimage])) {?><img src="<?=$GLOBALS['topic_url'];?>/<?=$ar1[topicimage];?>" alt="<?=$ar1[topictext];?>" width="20" /><?}?></td>
     <td valign="top" align="center" width="40"><input type="checkbox" name="list[]" value="<?=$ar[id];?>"/></td>
    </tr>
    <?
    if($_GET['action'] == "open" && $_GET['id'] == $ar[id]) 
      echo "<tr bgcolor=\"#eeeeee\"><td style=\"padding-left:120px;\" colspan=\"5\" class=\"MojText\">$ar[preview]</td></tr>";
   }
  ?>
  </table>
  <div align="right">
   <input type="button" name="CheckAll" value="<?=_CHECK_ALL;?>" onclick="checkAll(document.myform)" class="news">
   <input type="button" name="UnCheckAll" value="<?=_UNCHECK_ALL;?>" onclick="uncheckAll(document.myform)" class="news">
   <input type="hidden" name="action" value="multidel">
   <input type="submit" value="<?=_DELETE;?>" class="news">
  </div>
 </form>
 <?
}

//******************************************************************************************
//** Function: AddNews()                                                                  **
//** Last edited: 24th.June,2002                                                          **
//******************************************************************************************

function AddStory()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;
 global $HTTP_POST_FILES;

 // CHECK PRIVILEGIES
 if(CheckPriv("story_add") <> 1) 
 {
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if($_POST['confirm'] == "true")
 {
//  $_POST['headline'] = ConvertHTML($_POST['headline']);
  $_POST['preview'] = ConvertHTML($_POST['preview']);

  $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_story']." WHERE preview='".$_POST['preview']."'") or die("<b>Error 234:</b>".mysql_error());
  if(mysql_num_rows($res2)<1)
    mysql_query("INSERT INTO ".$GLOBALS['db_story']." VALUES(0,'".$_POST['headline']."','".$GLOBALS['login']."','".$_POST['section']."','',NOW(),NOW(),'".$_POST['preview']."',0)") or die("<b>Error 150:</b>".mysql_error());
  ShowMain();
 }
 
 if(empty($_POST['confirm']))
 { 
  ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/news_big.jpg" width="32" height="32" border="0" alt="<?=_ADDSTORY;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_ADDSTORY;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_story']) or die("<b>Line 246:</b>".mysql_error());
    echo _SUBMITEDSTORIES.": ".mysql_num_rows($res);
    ?>
   </td>
  </tr>
 </table>
 
 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td>&nbsp;</td>
  </tr>
 </table>

  <a name="addit"></a>
 <form action="<?=$GLOBALS['PHP_SELF'];?>" method="post" name="novica"> 
  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="MojText">
   <tr> 
    <td valign="top" width="70%">
    <?=_STORYHEADLINE;?><br />
     <input type="text" name="headline" value="<?=$GLOBALS['headline'];?>" class="news" size="60" /><br />
     <br/>
     <?=_SECTION;?>:<br />
     <select name="section" class="news">
      <?
       $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_topic']) or die("<b>Error 271:</b>".mysql_error());
       while($ar2 = mysql_fetch_array($res2))
         echo '<option value="'.$ar2[id].'">'.$ar2[topictext].'</option>';
      ?>
     </select><br />
 
     <table width="440"><tr><td><?=_PREVIEW;?></td><td align="right">
      <input type="button" accesskey="B" value="B" class="news" onclick="insert_tag('B','preview')" title="BOLD: [Control / Alt] + b" style="width:25px;font-weight:bold" />
      <input type="button" accesskey="B" value="/B" class="news" onclick="insert_tag('/B','preview');" title="BOLD: [Control / Alt] + SHIFT + B" style="width:25px;font-weight:bold" />
      <input type="button" accesskey="i" value="I" class="news" onclick="insert_tag('I','preview');" title="ITALIC: [Control / Alt] + i" style="width:25px;font-style:italic" />
      <input type="button" accesskey="I" value="/I" class="news" onclick="insert_tag('/I','preview');" title="ITALIC: [Control / Alt] + SHIFT + I" style="width:25px;font-style:italic" />
      <input type="button" accesskey="u" value="U" class="news" onclick="insert_tag('U','preview');" title="UNDERLINE: [Control / Alt] + u" style="width:25px;text-decoration:underline" />
      <input type="button" accesskey="U" value="/U" class="news" onclick="insert_tag('/U','preview');" title="UNDERLINE: [Control / Alt] + SHIFT + U" style="width:25px;text-decoration:underline" />
     </td></tr></table>
     <textarea onchange="MakePreview" onkeydown="MakePreview()" onblur="MakePreview" onkeyup="MakePreview()" name="preview" class="news" rows="" cols="" style="width:98%;height:100px;"><?=$GLOBALS['preview'];?></textarea><br/>
 
    </td>
    <td valign="top">
    <div style="height:80px;"></div>
     <div style="overflow:auto; width=100%; height:119px;">
      <?
       echo '<br/><table width="60%" cellspacing="1" cellpadding="2" bgcolor="'._COLOR02.'" class="MojText">';
       
       $rsm = mysql_query("SELECT * FROM ".$GLOBALS['db_smileys']);
       while($arm = mysql_fetch_array($rsm))
         echo '<tr bgcolor="'._COLOR06.'"><td align="center"><img style="cursor:pointer;" onclick="put_smiley(\''.$arm[code].'\',\'preview\');" title="'.$arm[emotion].'" alt="" border="0" src="'.$GLOBALS['smiley_url'].'/'.$arm[smile].'" /></td><td>'.$arm[code].'</td></tr>';
      echo '</table>';
      ?>
     </div>
    </td>
   </tr>
   <tr>
    <td colspan="2" width="30%">
     <br/>
     <input type="hidden" name="action" value="add" />
     <input type="hidden" name="confirm" value="true" />
     <br/>
     <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);" />
     <br/>
     <input type="checkbox" name="makreprev" onclick="ResetPreview();MakePreview();" value="1"><?=_PREVIEW_STORY;?><br/>
     <input type="checkbox" name="newwindow" value="1"><?=_OPEN_NEW_WINDOW;?><br/>
      
     </td>
   </tr>
  </table>
  </form>
  <hr/>
  <p id="tiph" tyle="width:620px; text-align:justify;"></p>
  <p id="tipp" tyle="width:620px; text-align:justify;"></p>
  <p id="tipm" tyle="width:620px; text-align:justify; overflow:auto; height:400px;"></p>
  <br/><br/>
 <?
 }
}

function EditStory()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;
 global $HTTP_POST_FILES;

 if(CheckPriv("story_edit") == 0)
 {
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 $res = mysql_query("SELECT * from ".$GLOBALS['db_story']." WHERE id='".$_GET['id']."'") or die("<b>Line 292:</b>".mysql_error());
 $ar = mysql_fetch_array($res);
 if(CheckPriv("story_mod") == 0 && $ar[author] <> $GLOBALS['login'])
 {
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTYOURSTORY."');</script>";
  return;
 }

 if ($_POST['confirm'] == "picture") 
 {
  $_GET['id'] = $_POST['id'];
  if(!empty($HTTP_POST_FILES['filename']['name']))
  {
   $HTTP_POST_FILES['filename']['name'] = eregi_replace(" ","",$HTTP_POST_FILES['filename']['name']);
   $HTTP_POST_FILES['filename']['name'] = eregi_replace("/","",$HTTP_POST_FILES['filename']['name']);
   $HTTP_POST_FILES['filename']['name'] = eregi_replace("@","",$HTTP_POST_FILES['filename']['name']);
   $HTTP_POST_FILES['filename']['name'] = eregi_replace("%","",$HTTP_POST_FILES['filename']['name']);
   $HTTP_POST_FILES['filename']['name'] = eregi_replace("\"","",$HTTP_POST_FILES['filename']['name']);
   $HTTP_POST_FILES['filename']['name'] = eregi_replace("'","",$HTTP_POST_FILES['filename']['name']);

   if($HTTP_POST_FILES['filename']['type'] == "image/jpeg" || $HTTP_POST_FILES['filename']['type'] == "image/pjpeg" | $HTTP_POST_FILES['filename']['type'] == "image/gif" || $HTTP_POST_FILES['filename']['type'] == "image/x-png") 
   {
    if(file_exists($GLOBALS['story_path']."/".$HTTP_POST_FILES['filename']['name'])) 
    {
       //echo "<script type=\"text/javascript\">alert('"._FILEALREADYEXIST."');</script>";
       
       $newfile = $GLOBALS['story_path']."/".$HTTP_POST_FILES['filename']['name'];
       while (file_exists($newfile) == true)
       {
        $xv++;
        $new_name = $xv."-".$HTTP_POST_FILES['filename']['name'];
        $newfile = $GLOBALS['story_path']."/".$new_name;
        $nf = $new_name;
       }
       $HTTP_POST_FILES['filename']['name'] = $nf;
       if(!move_uploaded_file($HTTP_POST_FILES['filename']['tmp_name'],$GLOBALS['story_path']."/".$HTTP_POST_FILES['filename']['name']))
         echo "<script type=\"text/javascript\">alert('"._ERROR."');</script>";
    }
    else
    if(!move_uploaded_file($HTTP_POST_FILES['filename']['tmp_name'],$GLOBALS['story_path']."/".$HTTP_POST_FILES['filename']['name']))
       echo "<script type=\"text/javascript\">alert('"._ERROR."');</script>";
    else 
    {
     chmod($GLOBALS['story_path']."/".$HTTP_POST_FILES['filename']['name'],0644);
    }
  }
   else 
    echo _WRONGDATATYPE.": ".$HTTP_POST_FILES['filename']['type']."<br />";

    $resx = mysql_query("SELECT picture FROM ".$GLOBALS['db_story']." WHERE id='".$_POST['id']."'") or die("<b>Error 228:</b>".mysql_error());
    $arx  = mysql_fetch_array($resx);

    mysql_query("UPDATE ".$GLOBALS['db_story']." SET picture='".$HTTP_POST_FILES['filename']['name']."' WHERE id='".$_POST['id']."'") or die("<b>Error 307:</b>".mysql_error());
   }
 } 
 if($_POST['confirm'] == "true") 
 {
  //echo ConvertHTML($_POST['message']);
   mysql_query("UPDATE ".$GLOBALS['db_story']." SET headline='".$_POST['headline']."', category='".$_POST['section']."', preview='".ConvertHTML($_POST['preview'])."' WHERE id='".$_POST['id']."'") or die("<b>Error 81:</b>".mysql_error());
   ShowMain();
  } 
  else
  {
   $res = mysql_query("SELECT * from ".$GLOBALS['db_story']." WHERE id='".$_GET['id']."'") or die("<b>Line 292:</b>".mysql_error());
   $ar = mysql_fetch_array($res);

   $ar[headline] = ereg_replace( "&quot;","'",$ar[headline]);
   $ar[headline] = ereg_replace( "&acute;","'",$ar[headline]);
   ?>
   <table width="630" cellspacing="2" cellpadding="0" class="MojText">
    <tr>
     <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/news_big.jpg" width="32" height="32" border="0" alt="<?=_ADDSTORY;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_EDIT_STORY;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_story']) or die("<b>Line 246:</b>".mysql_error());
    echo _SUBMITEDSTORIES.": ".mysql_num_rows($res);
    ?>
   </td>
  </tr>
 </table>
 
 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td>&nbsp;</td>
  </tr>
 </table>

  <a name="addit"></a>
  <form action="<?=$GLOBALS['PHP_SELF'];?>" method="post" name="novica"> 
  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="Mojtext">
   <tr> 
    <td valign="top" width="70%">
     <?=_STORYHEADLINE;?><br />
     <input type="text" onchange="MakePreview" onblur="MakePreview" onkeydown="MakePreview()" onkeyup="MakePreview()" name="headline" size="60"  value="<?echo $ar[headline];?>" class="news" /><br />
     <?=_SECTION;?>:<br />
     <select name="section" class="news">
     <?
      $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_topic']) or die("<b>line 329:</b>".mysql_error());
      while ($ar2 = mysql_fetch_array($res2))
      {
       echo "<option value=\"$ar2[0]\" ";
       if($ar2[id] == $ar[category]) 
         echo " selected=\"selected\" ";
       echo ">$ar2[topictext]</option>";
      }
      ?>
      </select><br />

      <table width="440"><tr><td><?=_PREVIEW;?></td><td align="right">
      <input type="button" accesskey="b" value="B" class="news" onclick="insert_tag('B','preview')" title="BOLD: [Control / Alt] + b" style="width:25px;font-weight:bold"/>
      <input type="button" accesskey="B" value="/B" class="news" onclick="insert_tag('/B','preview');" title="BOLD: [Control / Alt] + SHIFT + B" style="width:25px;font-weight:bold"/>
      <input type="button" accesskey="i" value="I" class="news" onclick="insert_tag('I','preview');" title="ITALIC: [Control / Alt] + i" style="width:25px;font-style:italic"/>
     <input type="button" accesskey="I" value="/I" class="news" onclick="insert_tag('/I','preview');" title="ITALIC: [Control / Alt] + SHIFT + I" style="width:25px;font-style:italic"/>
    <input type="button" accesskey="u" value="U" class="news" onclick="insert_tag('U','preview');" title="UNDERLINE: [Control / Alt] + u" style="width:25px;text-decoration:underline"/>
    <input type="button" accesskey="U" value="/U" class="news" onclick="insert_tag('/U','preview');" title="UNDERLINE: [Control / Alt] + SHIFT + U" style="width:25px;text-decoration:underline"/>
    </td></tr></table>
     <textarea name="preview" onchange="MakePreview" onblur="MakePreview" onkeydown="MakePreview()" onkeyup="MakePreview()" rows="" cols="" class="news" style="width:98%;height:100px;"><?=UnConvertHTML($ar['preview']);?></textarea><br />
 
    </td>
    <td valign="top">
    <div style="height:76px;"></div>
     <div style="overflow:auto; width=100%; height:116px;">
      <?
       echo '<br/><table width="60%" cellspacing="1" cellpadding="2" bgcolor="'._COLOR02.'" class="MojText">';
       
       $rsm = mysql_query("SELECT * FROM ".$GLOBALS['db_smileys']);
       while($arm = mysql_fetch_array($rsm))
         echo '<tr bgcolor="'._COLOR06.'"><td align="center"><img style="cursor:pointer;" onclick="put_smiley(\''.$arm[code].'\',\'preview\');" title="'.$arm[emotion].'" border="0" src="'.$GLOBALS['smiley_url'].'/'.$arm[smile].'" alt="" /></td><td>'.$arm[code].'</td></tr>';
      echo '</table>';
      ?>
     </div>

    </td>
   </tr>
   <tr>
    <td colspan="2" width="30%">
     <input type="hidden" name="id" value="<?=$_GET['id'];?>" />
     <input type="hidden" name="confirm" value="true" />
     <input type="hidden" name="action" value="edit" />
     <br/>
     <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);" />
     <br/>
     <input type="checkbox" name="makreprev" onclick="ResetPreview();MakePreview();" value="1"><?=_PREVIEW_STORY;?><br/>
     <input type="checkbox" name="newwindow" value="1"><?=_OPEN_NEW_WINDOW;?><br/>
    </td>
   </tr>
  </table>
  </form>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="Mojtext">
   <tr>
   <td>
    <br />
    <?
     if(file_exists($GLOBALS['story_path']."/".$ar[picture]) && !empty($ar[picture])) 
     {
      $size = getimagesize($GLOBALS['story_path']."/".$ar[picture]);
      echo '<img src="'.$GLOBALS['story_url']."/".$ar[picture].'" alt="" ';
      if($size[0]>600) echo 'width="600"';
      echo '/>';
     }
    ?>
    <br />

    <form action="<?=$GLOBALS['PHP_SELF'];?>" enctype="multipart/form-data" method="post"> 
     <?=_UPLOADPICTURE;?>:<br />
     <input type="file" class="news" name="filename" size="50" /><br />
     <input type="hidden" name="action" value="edit" /><br /><br />
     <input type="hidden" name="confirm" value="picture" />
     <input type="hidden" name="id" value="<?=$_GET['id'];?>" />
     <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);" />
    </form>
   </td>
  </tr>
 </table>
 <hr/>
 <p id="tiph" tyle="width:620px; text-align:justify; overflow:auto; height:400px;"></p>
 <p id="tipp" tyle="width:620px; text-align:justify; overflow:auto; height:400px;"></p>
 <p id="tipm" tyle="width:620px; text-align:justify; overflow:auto; height:400px;"></p>
 <?
 }
}

function DeleteStory()
{
 if(!check_version("4.1.0")) global $_GET; // only need if you're running 4.06 or lower version of PHP

 $res = mysql_query("SELECT * from ".$GLOBALS['db_story']." WHERE id='".$_GET['id']."'") or die("<b>Line 292:</b>".mysql_error());
 $ar = mysql_fetch_array($res);
 if(CheckPriv("story_mod") == 0 && $ar[author] <> $GLOBALS['login'])
 {
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTYOURSTORY."');</script>";
  return;
 }

 if(CheckPriv("story_del") == 1) 
 {
  mysql_query("DELETE FROM ".$GLOBALS['db_story']." where id='".$GLOBALS['id']."'") or die("<b>Error 361:</b>".mysql_error()); 
 }
 else
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";

 ShowMain();
}
?>
