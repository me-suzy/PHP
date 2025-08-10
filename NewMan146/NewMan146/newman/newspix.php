<?php
//******************************************************************************************
//** phpNewsManager v1.40                                                                 **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 18th.March,2003                                                         **
//******************************************************************************************

$title  = "phpNewsManager $newman_ver";
$makejs = "news";
include "functions.php";
?>
<html>
 <head>
  <title><?echo $title;?></title>
  <link rel="stylesheet" type="text/css" href="MojStil.css" />
  <meta http-equiv="Content-Type" content="text/html; charset=<?=_CHARSET;?>" />

 <script language="JavaScript">
  <!--
   function TransferIt(id,align)
   {
     handle = opener.ret;
     if(handle == 'message')
     {
       opener.insertAtCaret(opener.novica.message,'[PIC='+id+':'+align+']');
       //opener.novica.message.value = opener.novica.message.value + '[PIC='+id+':'+align+']';
     }
     if(handle == 'preview')
        opener.insertAtCaret(opener.novica.preview,'[PIC='+id+':'+align+']');
    //  opener.novica.preview.value = opener.novica.preview.value + '[PIC='+id+':'+align+']';
   }

   function CloseIt()
   {
    window.close();
   }

  -->
 </script>

 </head>
 <body text="#<?=_COLOR_TEXT;?>" bgcolor="#<?=_COLOR_BACK;?>" link="#<?=_COLOR_LINK;?>" alink="#<?=_COLOR_ALINK;?>" vlink="#<?=_COLOR_VLINK;?>" style="background-image:url('./gfx/background.gif')">
<table width="100%" cellspacing="0" cellpadding="1" border="0"  style="background-image:url('./gfx/tile.gif')" class="MojText">
 <tr>
  <td align="center" valign="top">

<?
$psw = CheckLogin();
$info = base64_decode("$nm_user");
$info = explode(":", $info);
if ($action=="Login") { $info[0]=$login;}      
$login = $info[0];

if($psw == TRUE) 
{
 if($action == "add")  AddNewsPic();
 else if($action == "edit") EditNewsPic();
 else ShowMain();
}
?>

  </td>
  </tr>
  </table>
 </body>
</html>

<?

function ShowMain()
{
 if(!check_version("4.1.0")) global $_GET;
 ?>
 <table width="100%" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"><img src="gfx/partners_big.jpg" width="32" height="32" border="0" alt="<?=_ADDPICTURE;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add">&nbsp;<?=_ADDPICTURE;?></a></td>
   <td align="center">
   <?
    $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_news_pics']) or die("<b>LINE 31:</b>".mysql_error());
    $num2 = mysql_num_rows($res2);
    
    if($GLOBALS['filter'] == "") unset($GLOBALS['filter']);
    $query  = "SELECT * FROM ".$GLOBALS['db_news_pics']." ";
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
      $rest = mysql_query("SELECT * FROM ".$GLOBALS['db_news_pics']." ORDER BY name");
      while($art = mysql_fetch_array($rest))
      {
       echo '<option value="'.$art[id].'" ';
       if($GLOBALS['filter'] == $art[id]) echo 'selected="selected"';
       echo '>'.$art[name].'</option>';
      }
     ?>
    </select>
   <input type="submit" value="<?=_SHOW;?>" class="news">
  </form>
 </div>
   
 <form action="<?=$GLOBALS['PHP_SELF'];?>" name="myform" method="post">
 <table width="100%" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td><font color="#<?=_COLOR05;?>"><?= _OPTIONS;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?= _INSERT;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?= _NAME;?></font></td>
   <td width="20"><font color="#<?=_COLOR05;?>"><?= _CHECK;?></font></td>
  </tr>  
  <?
   $res = mysql_query($query) or die("<b>Line 83:</b>".mysql_error());
   while ($ar = mysql_fetch_array($res))
   {
    if($ar[gid] == 0) $root = "/";
    else
    {
     $res2 = mysql_query("SELECT name from ".$GLOBALS['db_news_pics']." WHERE id='".$ar[gid]."'");
     $ar2 = mysql_fetch_array($res2);
     $root = $ar2[name];
    }
    ?>
    <tr>
     <td width="44">
      <a href="<?=$GLOBALS['PHP_SELF'];?>?action=edit&amp;id=<?=$ar[id];?>"><img src="gfx/edit.gif" width="20" height="20" border="0" alt="" /></a> 
      <a href="javascript:Confirm('<?=$GLOBALS['PHP_SELF'];?>?action=delete&amp;id=<?=$ar[id];?>','<?=_DELETE.": ".eregi_replace("'","\'",$ar[name])."?";?>');"><img src="gfx/trash.gif" width="20" height="20" border="0" alt="" /></a>
     </td> 
     <td width="130">
      <a href="javascript:TransferIt(<?=$ar[id];?>,'left');"><?=_LEFT;?></a> .
      <a href="javascript:TransferIt(<?=$ar[id];?>,'center');"><?=_CENTER;?></a> .
      <a href="javascript:TransferIt(<?=$ar[id];?>,'right');"><?=_RIGHT;?></a>
     </td>
     <td><?=$ar[name];?></td>
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

function AddNewsPic()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;
 global $HTTP_POST_FILES;

 // CHECK PRIVILEGIES

 if(CheckPriv("news_add") <> 1) 
 {
   ShowMain();
   echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
   return;
 }
?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/category_big.jpg" width="32" height="32" border="0" alt="<?=_ADDPICTURE;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_ADDPICTURE;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_news_pics']) or die("<b>Line 246:</b>".mysql_error());
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
<?

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
    if(file_exists($GLOBALS['news_path']."/".$HTTP_POST_FILES['filename']['name'])) 
    {
       $newfile = $GLOBALS['news_path']."/".$HTTP_POST_FILES['filename']['name'];
       while (file_exists($newfile) == true)
       {
        $xv++;
        $new_name = $xv."-".$HTTP_POST_FILES['filename']['name'];
        $newfile = $GLOBALS['news_path']."/".$new_name;
        $nf = $new_name;
       }
       $HTTP_POST_FILES['filename']['name'] = $nf;
       if(!move_uploaded_file($HTTP_POST_FILES['filename']['tmp_name'],$GLOBALS['news_path']."/".$HTTP_POST_FILES['filename']['name']))
         echo "<script type=\"text/javascript\">alert('"._ERROR."');</script>";
    }
    else
    if(!move_uploaded_file($HTTP_POST_FILES['filename']['tmp_name'],$GLOBALS['news_path']."/".$HTTP_POST_FILES['filename']['name']))
       echo "<script type=\"text/javascript\">alert('"._ERROR."');</script>";
    else 
     chmod($GLOBALS['news_path']."/".$HTTP_POST_FILES['filename']['name'],0644);
  }
   else 
    echo _WRONGDATATYPE.": ".$HTTP_POST_FILES['filename']['type']."<br />";
  }

  $_POST['description'] = ereg_replace( "\"","&quot;",$_POST['description']);
  $_POST['description'] = ereg_replace( "'","&acute",$_POST['description']);

  //exec(EscapeShellCmd($app_mogrify." -geometry 300x".$nx." /opt/www/virtual/skinbase/files/shots/$ns"));

  //echo "INSERT INTO ".$GLOBALS['db_news_pics']." VALUES (0,'".$GLOBALS['login']."','".$_POST['name']."','".$HTTP_POST_FILES['filename']['name']."','".$_POST['description']."',NOW())";
  $res = mysql_query("INSERT INTO ".$GLOBALS['db_news_pics']." VALUES (0,'".$GLOBALS['login']."','".$_POST['name']."','".$HTTP_POST_FILES['filename']['name']."','".$_POST['description']."',NOW())") or die("<b>LINE 200:</b>".mysql_error()); 
  unset($_POST['confirm']);

  $res = mysql_query("SELECT * FROM ".$GLOBALS['db_news_pics']." WHERE name='".$_POST['name']."' AND picture='".$HTTP_POST_FILES['filename']['name']."'");
  $ar = mysql_fetch_array($res);
 } 

 if ($_POST['confirm'] <> "true") 
 {
 ?>

 <table width="99%" align="center" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td>
     <form action="<?=$GLOBALS['PHP_SELF'];?>" enctype="multipart/form-data" method="post" name="forma"> 
     <?=_NAME;?><br/>
     <input type="text" name="name" class="news" size="60"/><br/>
     <?=_DESCRIPTION;?><br/>
     <textarea name="description" class="news" rows="" cols="" style="width:98%;height:150px;"></textarea><br />

       <br/>
       <?=_UPLOADPICTURE;?>:<br />
       <input type="file" name="filename" class="news" size="50" /><br />
       <br/>
       <input type="hidden" name="action" value="add"/>
       <input type="hidden" name="confirm" value="true"/>
       <input type="submit" class="news" value="<?=_SUBMIT;?>"/> &nbsp; 
       <input type="button" class="news" onclick="CloseIt();" value="<?=_CLOSE;?>"/>
      </form>
     </td>
    </tr>
  </table> 
 <?
 }
}


function EditNewsPic()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;
 global $HTTP_POST_FILES;
 // CHECK PRIVILEGIES
 if(CheckPriv("news_edit") <> 1) 
 {
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if(empty($_POST['confirm'])) 
 {
  $res = mysql_query("SELECT * FROM ".$GLOBALS['db_news_pics']." WHERE id='".$_GET['id']."'");
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
   <td align="center"><font size="4" face="Arial"> <b><?=_EDITPICTURE;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_news_pics']) or die("<b>Line 246:</b>".mysql_error());
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
       <br/>
       <input type="hidden" name="id" value="<?=$_GET['id'];?>"/>
       <input type="hidden" name="action" value="edit"/>
       <input type="hidden" name="confirm" value="true"/>
       <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);"/>
      </form>

        <br />
        <?
         if(file_exists($GLOBALS['news_path']."/".$ar[picture]) && !empty($ar[picture])) 
         {
          $size = getimagesize($GLOBALS['news_path']."/".$ar[picture]);
          echo '<img src="'.$GLOBALS['news_url']."/".$ar[picture].'" alt="" ';
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

  $res = mysql_query("UPDATE ".$GLOBALS['db_news_pics']." SET name='".$_POST['name']."', description='".$_POST['description']."' WHERE id='".$_POST['id']."'") or die("<b>LINE 262:</b>".mysql_error());
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
    if(file_exists($GLOBALS['news_path']."/".$HTTP_POST_FILES['filename']['name'])) 
    {
     $newfile = $GLOBALS['news_path']."/".$HTTP_POST_FILES['filename']['name'];
     while (file_exists($newfile) == true)
     {
      $xv++;
      $new_name = $xv."-".$HTTP_POST_FILES['filename']['name'];
      $newfile = $GLOBALS['news_path']."/".$new_name;
      $nf = $new_name;
     }
     $HTTP_POST_FILES['filename']['name'] = $nf;
     if(!move_uploaded_file($HTTP_POST_FILES['filename']['tmp_name'],$GLOBALS['news_path']."/".$HTTP_POST_FILES['filename']['name']))
       echo "<script type=\"text/javascript\">alert('"._ERROR."');</script>";
    }
    else
     if(!move_uploaded_file($HTTP_POST_FILES['filename']['tmp_name'],$GLOBALS['news_path']."/".$HTTP_POST_FILES['filename']['name']))
      echo "<script type=\"text/javascript\">alert('"._ERROR."');</script>";
     else 
      chmod($GLOBALS['news_path']."/".$HTTP_POST_FILES['filename']['name'],0644);
   }
   else 
    echo _WRONGDATATYPE.": ".$HTTP_POST_FILES['filename']['type']."<br />";
  }
  mysql_query("UPDATE ".$GLOBALS['db_news_pics']." SET picture='".$HTTP_POST_FILES['filename']['name']."',thumbnail='".$HTTP_POST_FILES['filename']['name']."' WHERE id='".$_POST['id']."'") or die("<b>LINE 262:</b>".mysql_error());
  ShowMain();
 } 

}

function DeleteNewsPic()
{
 if(!check_version("4.1.0")) global $_GET; // only need if you're running 4.06 or lower version of PHP
 if(CheckPriv("news_edit") == 1) 
   mysql_query("DELETE FROM ".$GLOBALS['db_news_pics']." WHERE id='".$_GET['id']."'"); 
 else
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
 ShowMain();
}

?>