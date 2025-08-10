<?php
//******************************************************************************************
//**                                                                                      **
//** phpNewsManager v1.40                                                                 **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 17th.March,2003                                                         **
//******************************************************************************************

$title    = "phpNewsManager $newman_ver";
include "functions.php";
include ("header.php");
if($psw == TRUE)
 if ($action == "edit") EditUser();
 else if ($action == "delete") DeleteUser();
 else if ($action == "multidel") MultiDelete($db_users,"id","users_del");
 else if ($action == "add") AddUser();
 else ShowMain();

include ("footer.php");

function ShowMain()
{
 if(!check_version("4.1.0")) global $_GET;

 ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"><img src="gfx/users_big.jpg" width="32" height="32" border="0" alt="<?=_ADDUSER;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"><?=_ADDUSER;?></a></td>
   <td align="center">
   <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_users']) or die("<B>Error 23:</B>".mysql_error());
    $num = mysql_num_rows($res);
    $myopt = ShowPages($num,$GLOBALS['page'],$GLOBALS['hits'],$GLOBALS['show']);
   ?>
   </td>
   <td align="right"><? echo _REGISTEREDUSERS.": ".$num;?></td>
  </tr>
 </table>
 <form action="<?=$GLOBALS['PHP_SELF'];?>" name="myform" method="post">
 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td><font color="#<?echo _COLOR05;?>"><?=_OPTIONS;?></font></td>
   <td><font color="#<?echo _COLOR05;?>"><a href="<?=$GLOBALS['PHP_SELF'];?>?sort=id&amp;page=<?=$GLOBALS['page'];?>"><?=_ID;?></a></font></td>
   <td><font color="#<?echo _COLOR05;?>"><a href="<?=$GLOBALS['PHP_SELF'];?>?sort=username&amp;page=<?=$GLOBALS['page'];?>"><?=_USERNAME;?></a></font></td>
   <td><font color="#<?echo _COLOR05;?>"><a href="<?=$GLOBALS['PHP_SELF'];?>?sort=name&amp;page=<?=$GLOBALS['page'];?>"><?=_NAME;?></a></font></td>
   <td><font color="#<?echo _COLOR05;?>"><a href="<?=$GLOBALS['PHP_SELF'];?>?sort=email&amp;page=<?=$GLOBALS['page'];?>"><?=_EMAIL;?></a></font></td>
   <td><font color="#<?echo _COLOR05;?>"><?=_CHECK;?></font></td>
  </tr>  
  <?
  if(empty($GLOBALS['sort']))
   $query = "SELECT * FROM ".$GLOBALS['db_users']." ORDER BY id DESC LIMIT $myopt[0],$myopt[1]";
  else
  {
   if($GLOBALS['sort'] == "username") $sort = "uname";
   else if($GLOBALS['sort'] == "name") $sort = "name";
   else if($GLOBALS['sort'] == "email") $sort = "email";
   else 
   {
    $sort = "id";
    $desc = "DESC";
   }

   $query = "SELECT * FROM ".$GLOBALS['db_users']." ORDER BY ".$sort." ".$desc." LIMIT $myopt[0],$myopt[1]";
  }
  $res = mysql_query($query) or die("<B>Error 23:</B>".mysql_error());  
  while ($ar = mysql_fetch_array($res))
  {
   ?>
   <tr>
    <td width="66">
     <a href="<?=$GLOBALS['PHP_SELF'];?>?action=edit&amp;sort=<?=$GLOBALS['sort'];?>&amp;order=<?=$GLOBALS['order'];?>&amp;page=<?=$GLOBALS['page'];?>&amp;id=<?=$ar[id];?>"><img src="gfx/edit.gif" width="20" height="20" border="0" alt="" /></a> 
     <a href="javascript:Confirm('<?=$GLOBALS['PHP_SELF'];?>?action=delete&amp;sort=<?=$GLOBALS['sort'];?>&amp;order=<?=$GLOBALS['order'];?>&amp;page=<?=$GLOBALS['page'];?>&amp;id=<?=$ar[id];?>','<?=_DELETE.": ".$ar[uname]."?";?>');"><img src="gfx/trash.gif" width="20" height="20" border="0" alt="" /></a>
    </td> 
    <td><?=$ar[id];?></td>
    <td <?if($GLOBALS['sort'] == "username") echo 'bgcolor="#fafafa"';?>><?=$ar[uname];?></td>
    <td <?if($GLOBALS['sort'] == "name") echo 'bgcolor="#fafafa"';?>><?=$ar[name];?></td>
    <td <?if($GLOBALS['sort'] == "email") echo 'bgcolor="#fafafa"';?>><a href="mailto:<?=$ar[email];?>"><?=$ar[email];?></a></td>
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

function AddUser()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;

 // CHECK PRIVILEGIES
 if(CheckPriv("users_add") <> 1)
 {
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }
 if($_POST['confirm'] == "true") 
 {
  if(!is_email_valid($_POST['a_email']))
  {
   echo "<script type=\"text/javascript\">alert('"._EMAIL_ERROR."');</script>";
   unset($_POST['confirm']);
   $_GET['id'] = $_POST['id'];
  }
  else if(empty($_POST['a_username']))
  {
   echo "<script type=\"text/javascript\">alert('"._USERNAME_ERROR."');</script>";
   unset($_POST['confirm']);
   $_GET['id'] = $_POST['id'];
  }
  else if(empty($_POST['a_password']))
  {
   echo "<script type=\"text/javascript\">alert('"._PASSWORD_ERROR."');</script>";
   unset($_POST['confirm']);
   $_GET['id'] = $_POST['id'];
  }
  else
  {
   $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_users']." WHERE uname='".$_POST['a_username']."'");
   if(mysql_num_rows($res2)<1) {$res = mysql_query("INSERT INTO ".$GLOBALS['db_users']." VALUES(0,'".$_POST['a_username']."','".$_POST['a_password']."','".$_POST['a_email']."','".$GLOBALS['a_infek']."','".$_POST['a_name']."')") or die ("<B>Error:</B>".mysql_error());}
   ShowMain();
  }
 }
 
 if ($_POST['confirm'] <> "true")
 { 
  $res = mysql_query("SELECT * from ".$GLOBALS['db_users']." where id='".$_GET['id']."'");
  $ar = mysql_fetch_array($res);
  ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/users_big.jpg" width="32" height="32" border="0" alt="<?=_ADDUSER;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_ADDUSER;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_users']) or die("<b>Line 90:</b>".mysql_error());
    echo _REGISTEREDUSERS.": ".mysql_num_rows($res);
    ?>
   </td>
  </tr>
 </table>

 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td>&nbsp;</td>
  </tr>
 </table>

  <br/>
    <table width="630" cellspacing="2" cellpadding="0" class="MojText">
     <tr>
      <td>
       <form action="<?=$GLOBALS['PHP_SELF'];?>" method="post">
        <?=_USERNAME;?><br/>
        <input type="text" class="news" value="<?=$GLOBALS['a_username'];?>" name="a_username" size="40"/><br/>
        <?=_PASSWORD;?><br/>
        <input type="password" class="news"  value="<?=$GLOBALS['a_password'];?>" name="a_password" size="40"/><br/>
        <?=_NAME;?><br/>
        <input type="text" class="news"  value="<?=$GLOBALS['a_name'];?>" name="a_name" size="40"/><br/>
        <?=_EMAIL;?><br/>
        <input type="text" class="news" value="<?=$GLOBALS['a_email'];?>" name="a_email" size="40"/><br/>
        <?=_INFO;?><br/>
        <input type="text" class="news"  value="<?=$GLOBALS['a_infek'];?>" name="a_infek" size="40"/><br/>
        <input type="hidden" name="action" value="add"/>
        <input type="hidden" name="confirm" value="true"/><br/>
        <input type="submit" value="<?echo _SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);"/>
        </form>
       </td>
      </tr>
    </table> 
 <?
 }
}

function EditUser()
{
 if(!check_version("4.1.0")) global $_POST,$_GET;

 // CHECK PRIVILEGIES
 if(CheckPriv("users_edit") <> 1)
 {
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if ($_POST['confirm'] == "true") 
 {
  if(!is_email_valid($_POST['email']))
  {
   echo "<script type=\"text/javascript\">alert('"._EMAIL_ERROR."');</script>";
   unset($_POST['confirm']);
   $_GET['id'] = $_POST['id'];
  }
  else if(empty($_POST['username']))
  {
   echo "<script type=\"text/javascript\">alert('"._USERNAME_ERROR."');</script>";
   unset($_POST['confirm']);
   $_GET['id'] = $_POST['id'];
  }
  else if(empty($_POST['password']))
  {
   echo "<script type=\"text/javascript\">alert('"._PASSWORD_ERROR."');</script>";
   unset($_POST['confirm']);
   $_GET['id'] = $_POST['id'];
  }
  else
  {
   $res = mysql_query("UPDATE ".$GLOBALS['db_users']." SET uname='".$_POST['username']."', passwd='".$_POST['password']."', name='".$_POST['name']."', email='".$_POST['email']."', info='".$_POST['infek']."' WHERE id='".$_POST['id']."'") or die ("<b>error:</b>".mysql_error()); 
   ShowMain();
  }
 } 

 if ($_POST['confirm'] <> "true") 
   {
    $res = mysql_query("SELECT * from ".$GLOBALS['db_users']." where id='".$_GET['id']."'");
    $ar = mysql_fetch_array($res);
    ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/users_big.jpg" width="32" height="32" border="0" alt="<?=_ADDUSER;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_EDITUSER;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_users']) or die("<b>Line 90:</b>".mysql_error());
    echo _REGISTEREDUSERS.": ".mysql_num_rows($res);
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
       <form action="<?=$GLOBALS['PHP_SELF'];?>" method="post">
        <?=_USERNAME;?><br/>
        <input type="text" name="username" size="40" class="news" value="<?echo $ar[uname];?>"/><br/>
        <?=_PASSWORD;?>:<br/>
        <input type="password" name="password" size="40" class="news" value="<?echo $ar[passwd];?>"/><br/>
        <?=_NAME;?><br/>
        <input type="text" name="name" size="40" class="news" value="<?echo $ar[name];?>"/><br/>
        <?=_EMAIL;?><br/>
        <input type="text" name="email" size="40" class="news" value="<?echo $ar[email];?>"/><br/>
        <?=_INFO;?><br/>
        <input type="text" name="infek" size="40" class="news" value="<?echo $ar[info];?>"/><br/>
        <input type="hidden" name="action" value="edit"/>
        <input type="hidden" name="confirm" value="true"/>
        <input type="hidden" name="id" value="<?=$_GET['id'];?>"/><br/>
        <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);"/>
        </form>
       </td>
      </tr>
    </table> 
   <?
  }
}

function DeleteUser()
{
 if(!check_version("4.1.0")) global $_GET; // only need if you're running 4.06 or lower version of PHP
 // CHECK PRIVILEGIES
 if(CheckPriv("wp_del") == 1) 
   $res = mysql_query("DELETE FROM ".$GLOBALS['db_users']." where id='".$_GET['id']."'"); 
 else
   echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
 unset($GLOBALS['id']);
 ShowMain();
 return;
}
?>
