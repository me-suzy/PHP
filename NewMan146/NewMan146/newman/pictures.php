<?php
//******************************************************************************************
//** phpNewsManager v1.40                                                                 **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 18th.March,2003                                                         **
//******************************************************************************************

$title  = "phpNewsManager $newman_ver";
$makejs = "gallery";
include "functions.php";
include "header.php";
if($psw == TRUE)
 if ($action == "edit") EditGallery();   
 else if ($action == "add") AddGallery();    
 else if ($action == "delete") DeleteGallery(); 
 else if ($action == "multidel") MultiDelete($db_gallery,"id","gallery_del");
 else ShowMain();
include ("footer.php");

function ShowMain()
{
 if(!check_version("4.1.0")) global $_GET;
 ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"><img src="gfx/partners_big.jpg" width="32" height="32" border="0" alt="<?=_ADDPICTURE;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add">&nbsp;<?=_ADDPICTURE;?></a></td>
   <td align="center">
   <?
    $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_gallery']) or die("<b>LINE 31:</b>".mysql_error());
    $num2 = mysql_num_rows($res2);
    
    if($GLOBALS['filter'] == "") unset($GLOBALS['filter']);
    $query  = "SELECT * FROM ".$GLOBALS['db_gallery']." ";
    if(!empty($GLOBALS['filter']) || !empty($GLOBALS['txtfilter'])) $query .= "WHERE ";
    if(!empty($GLOBALS['filter'])) $query .= " gid='".$GLOBALS['filter']."' ";
    if(!empty($GLOBALS['filter']) && !empty($GLOBALS['txtfilter'])) $query .= "AND ";
    if(!empty($GLOBALS['txtfilter'])) $query .= " name LIKE '%".$GLOBALS['txtfilter']."%' ";
 
    $res = mysql_query($query) or die("<b>LINE 42:</b>".mysql_error());
    $num = mysql_num_rows($res);
   
    $myopt = ShowPages($num,$GLOBALS['page'],$GLOBALS['hits'],$GLOBALS['show']);
    $query .= "ORDER BY id DESC LIMIT $myopt[0],$myopt[1]";
   ?>
   </td>
   <td align="right"><?=_SUBMITEDPICTURES.": ".$num2;?></td>
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
      $rest = mysql_query("SELECT * FROM ".$GLOBALS['db_gallery_groups']." ORDER BY name");
      while($art = mysql_fetch_array($rest))
      {
       echo '<option value="'.$art[id].'" ';
       if($GLOBALS['filter'] == $art[id]) echo 'selected="selected"';
       echo '>'.$art[name].'</option>';
      }
     ?>
    </select>
   <input type="submit" value="show" class="news">
  </form>
 </div>
   
 <form action="<?=$GLOBALS['PHP_SELF'];?>" name="myform" method="post">
 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td><font color="#<?=_COLOR05;?>"><?= _OPTIONS;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?= _NAME;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?= _AUTHOR;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?= _DATE;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?= _ROOT;?></font></td>
   <td width="20"><font color="#<?=_COLOR05;?>"><?= _CHECK;?></font></td>
  </tr>  
  <?
   $res = mysql_query($query) or die("<b>Line 83:</b>".mysql_error());
   while ($ar = mysql_fetch_array($res))
   {
    if($ar[gid] == 0) $root = "/";
    else
    {
     $res2 = mysql_query("SELECT name from ".$GLOBALS['db_gallery_groups']." WHERE id='".$ar[gid]."'");
     $ar2 = mysql_fetch_array($res2);
     $root = $ar2[name];
    }
    ?>
    <tr>
     <td width="44">
      <a href="<?=$GLOBALS['PHP_SELF'];?>?action=edit&amp;id=<?=$ar[id];?>"><img src="gfx/edit.gif" width="20" height="20" border="0" alt="" /></a> 
      <a href="javascript:Confirm('<?=$GLOBALS['PHP_SELF'];?>?action=delete&amp;id=<?=$ar[id];?>','<?=_DELETE.": ".eregi_replace("'","\'",$ar[name])."?";?>');"><img src="gfx/trash.gif" width="20" height="20" border="0" alt="" /></a>
     </td> 
     <td><?=$ar[name];?></td>
     <td valign="top"><?=$ar[author];?></td>
     <td align="right" valign="top"><?=$ar[datum];?></td>
     <td valign="top"><?=$root;?></td>
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

function AddGallery()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;
 global $HTTP_POST_FILES;

 // CHECK PRIVILEGIES
 if(CheckPriv("gallery_add") <> 1) 
 {
   ShowMain();
   echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
   return;
 }

 if ($_POST['confirm'] <> "true") 
 {
 ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/category_big.jpg" width="32" height="32" border="0" alt="<?=_ADDPICTURE;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_ADDPICTURE;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_gallery']) or die("<b>Line 246:</b>".mysql_error());
    echo _SUBMITEDPICTURES.": ".mysql_num_rows($res);
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
     <form action="<?=$GLOBALS['PHP_SELF'];?>" enctype="multipart/form-data" method="post" name="forma"> 
     <?=_NAME;?><br/>
     <input type="text" name="name" class="news" size="60"/><br/>
     <?=_DESCRIPTION;?><br/>
     <textarea name="description" class="news" rows="" cols="" style="width:98%;height:150px;"><?=$GLOBALS['description'];?></textarea><br />
     <?=_CATEGORY;?><br/>
     <select name="root" class="news" >
       <?
	$res2 = mysql_query("SELECT id,name FROM ".$GLOBALS['db_gallery_groups']." ORDER BY name");
	while($ar2 = mysql_fetch_array($res2))
	  echo '<option value="'.$ar2[id].'">'.$ar2[name].'</option>';
       ?>
       </select><br/>
       <br/>
       <?=_UPLOADPICTURE;?>:<br />
       <input type="file" name="filename" class="news" size="50" /><br />
       <br/>
       <input type="hidden" name="action" value="add"/>
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
    if(file_exists($GLOBALS['gallery_path']."/".$HTTP_POST_FILES['filename']['name'])) 
    {
       //echo "<script type=\"text/javascript\">alert('"._FILEALREADYEXIST."');</script>";
       
       $newfile = $GLOBALS['gallery_path']."/".$HTTP_POST_FILES['filename']['name'];
       while (file_exists($newfile) == true)
       {
        $xv++;
        $new_name = $xv."-".$HTTP_POST_FILES['filename']['name'];
        $newfile = $GLOBALS['gallery_path']."/".$new_name;
        $nf = $new_name;
       }
       $HTTP_POST_FILES['filename']['name'] = $nf;
       if(!move_uploaded_file($HTTP_POST_FILES['filename']['tmp_name'],$GLOBALS['gallery_path']."/".$HTTP_POST_FILES['filename']['name']))
         echo "<script type=\"text/javascript\">alert('"._ERROR."');</script>";
    }
    else
    if(!move_uploaded_file($HTTP_POST_FILES['filename']['tmp_name'],$GLOBALS['gallery_path']."/".$HTTP_POST_FILES['filename']['name']))
       echo "<script type=\"text/javascript\">alert('"._ERROR."');</script>";
    else 
    {
     chmod($GLOBALS['gallery_path']."/".$HTTP_POST_FILES['filename']['name'],0644);
    }
  }
   else 
    echo _WRONGDATATYPE.": ".$HTTP_POST_FILES['filename']['type']."<br />";
  }

  $_POST['description'] = ereg_replace( "\"","&quot;",$_POST['description']);
  $_POST['description'] = ereg_replace( "'","&acute",$_POST['description']);

  //exec(EscapeShellCmd($app_mogrify." -geometry 300x".$nx." /opt/www/virtual/skinbase/files/shots/$ns"));

  $res = mysql_query("INSERT INTO ".$GLOBALS['db_gallery']." VALUES (0,'".$GLOBALS['login']."','".$_POST['name']."','".$_POST['description']."',NOW(),'".$_POST['root']."','".$HTTP_POST_FILES['filename']['name']."')") or die("<b>LINE 231:</b>".mysql_error()); 
  ShowMain();
 } 
}


function EditGallery()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;
 global $HTTP_POST_FILES;
 // CHECK PRIVILEGIES
 if(CheckPriv("gallery_edit") <> 1) 
 {
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if(empty($_POST['confirm'])) 
 {
  $res = mysql_query("SELECT * FROM ".$GLOBALS['db_gallery']." WHERE id='".$_GET['id']."'");
  $ar = mysql_fetch_array($res);
  $ar[description] = ereg_replace( "&quot;","\"",$ar[description]);
  $ar[description] = ereg_replace( "&acute","'",$ar[description]);
  $ar[description] = ereg_replace( "<br/>","\n",$ar[description]);
  $ar[description] = ereg_replace( "<br>","\n",$ar[description]);
  ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/category_big.jpg" width="32" height="32" border="0" alt="<?=_ADDPICTURE;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_ADDPICTURE;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_gallery']) or die("<b>Line 246:</b>".mysql_error());
    echo _SUBMITEDPICTURES.": ".mysql_num_rows($res);
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
     <?=_NAME;?><br/>
     <input type="text" name="name" class="news" value="<?=$ar[name];?>" size="60"/><br/>
     <?=_DESCRIPTION;?><br/>
     <textarea name="description" class="news" rows="" cols="" style="width:98%;height:150px;"><?=$GLOBALS['description'];?><?=$ar[description];?></textarea><br />
     <?=_CATEGORY;?><br/>
     <select name="root" class="news" >
       <?
	$res2 = mysql_query("SELECT id,name FROM ".$GLOBALS['db_gallery_groups']." ORDER BY name");
	while($ar2 = mysql_fetch_array($res2))
	{
	   echo '<option value="'.$ar2[id].'"';
	   if($ar2[id] == $ar[gid]) echo ' selected="selected"';
	   echo '>'.$ar2[name].'</option>';
	  
	}
       ?>
       </select><br/>
       <br/>
       <input type="hidden" name="id" value="<?=$_GET['id'];?>"/>
       <input type="hidden" name="action" value="edit"/>
       <input type="hidden" name="confirm" value="true"/>
       <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);"/>
      </form>

        <br />
        <?
         if(file_exists($GLOBALS['gallery_path']."/".$ar[picture]) && !empty($ar[picture])) 
         {
          $size = getimagesize($GLOBALS['gallery_path']."/".$ar[picture]);
          echo '<img src="'.$GLOBALS['gallery_url']."/".$ar[picture].'" alt="" ';
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
  <?
  }
 if ($_POST['confirm'] == "true") 
 {
  $_POST['description'] = ereg_replace( "\"","&quot;",$_POST['description']);
  $_POST['description'] = ereg_replace( "'","&acute",$_POST['description']);

  $res = mysql_query("UPDATE ".$GLOBALS['db_gallery']." SET name='".$_POST['name']."', description='".$_POST['description']."', gid='".$_POST['root']."' WHERE id='".$_POST['id']."'") or die("<b>LINE 262:</b>".mysql_error());
  ShowMain();
 } 

 if ($_POST['confirm'] == "picture") 
 {
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
    if(file_exists($GLOBALS['gallery_path']."/".$HTTP_POST_FILES['filename']['name'])) 
    {
     $newfile = $GLOBALS['gallery_path']."/".$HTTP_POST_FILES['filename']['name'];
     while (file_exists($newfile) == true)
     {
      $xv++;
      $new_name = $xv."-".$HTTP_POST_FILES['filename']['name'];
      $newfile = $GLOBALS['gallery_path']."/".$new_name;
      $nf = $new_name;
     }
     $HTTP_POST_FILES['filename']['name'] = $nf;
     if(!move_uploaded_file($HTTP_POST_FILES['filename']['tmp_name'],$GLOBALS['gallery_path']."/".$HTTP_POST_FILES['filename']['name']))
       echo "<script type=\"text/javascript\">alert('"._ERROR."');</script>";
    }
    else
     if(!move_uploaded_file($HTTP_POST_FILES['filename']['tmp_name'],$GLOBALS['gallery_path']."/".$HTTP_POST_FILES['filename']['name']))
      echo "<script type=\"text/javascript\">alert('"._ERROR."');</script>";
     else 
      chmod($GLOBALS['gallery_path']."/".$HTTP_POST_FILES['filename']['name'],0644);
   }
   else 
    echo _WRONGDATATYPE.": ".$HTTP_POST_FILES['filename']['type']."<br />";
  }
  mysql_query("UPDATE ".$GLOBALS['db_gallery']." SET picture='".$HTTP_POST_FILES['filename']['name']."',thumbnail='".$HTTP_POST_FILES['filename']['name']."' WHERE id='".$_POST['id']."'") or die("<b>LINE 262:</b>".mysql_error());
  ShowMain();
 } 

}

function DeleteGallery()
{
 if(!check_version("4.1.0")) global $_GET; // only need if you're running 4.06 or lower version of PHP
 if(CheckPriv("gallery_edit") == 1) 
   mysql_query("DELETE FROM ".$GLOBALS['db_gallery']." WHERE id='".$_GET['id']."'"); 
 else
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
 ShowMain();
}

?>