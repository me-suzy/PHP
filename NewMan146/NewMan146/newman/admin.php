<?php
//******************************************************************************************
//** phpNewsManager                                                                       **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 14th.March,2003                                                         **
//******************************************************************************************

$title    = "phpNewsManager v1.30";
include "functions.php";
include "header.php";

if($psw == TRUE)
 if($action == "edit") EditAdmin();   
 else if($action == "delete") DeleteAdmin(); 
 else if ($action == "multidel") MultiDelete($db_admin,"id","admin_del");
 else if($action == "add") AddAdmin();    
 else ShowMain();   

include "footer.php";


function ShowMain()
{
 if(!check_version("4.1.0")) global $_GET;
 ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"><img src="gfx/admin_big.jpg" width="32" height="32" border="0" alt="<?=_ADDUSER;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"> &nbsp;<?=_ADDADMIN;?></a></td>
   <td align="center">
   <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_admin']) or die("<B>Error 23:</B>".mysql_error());
    $num = mysql_num_rows($res);
    $myopt = ShowPages($num,$GLOBALS['page'],$GLOBALS['hits'],$GLOBALS['show']);
   ?>
   </td>
   <td align="right"><? echo _REGISTEREDADMINS.": ".$num;?></td>
  </tr>
 </table>

 <form action="<?=$GLOBALS['PHP_SELF'];?>" name="myform" method="post">
 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td><font color="#<?=_COLOR05;?>"><?=_OPTIONS;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_USERNAME;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_NAME;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_EMAIL;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_PRIVILEGES;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_CHECK;?></font></td>
  </tr>  
  <?
   $res = mysql_query("SELECT * from ".$GLOBALS['db_admin']);
   while ($ar = mysql_fetch_array($res))
   {
    $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_groups']." WHERE id=".$ar[priv]);
    $ar2 = mysql_fetch_array($res2);
    ?>
    <tr>
     <td width="44">
      <a href="<?=$GLOBALS['PHP_SELF'];?>?action=edit&amp;id=<?=$ar[id];?>"><img src="gfx/edit.gif" width="20" height="20" border="0" alt="" /></a> 
      <a href="javascript:Confirm('<?=$GLOBALS['PHP_SELF'];?>?action=delete&amp;id=<?=$ar[id];?>','<?=_DELETE.": ".eregi_replace("'","\'",$ar[uname])."?";?>');"><img src="gfx/trash.gif" width="20" height="20" border="0" alt="" /></a>
     </td> 
     <td><?=$ar[uname];?></td>
     <td valign="top"><?=$ar[name];?></td>
     <td valign="top"><?=$ar[email];?></td>
     <td valign="top" align="right"><?=$ar2[name];?></td>
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

function AddAdmin()
{
 if(!check_version("4.1.0")) global $_POST; // only need if you're running 4.06 or lower version of PHP

 if(CheckPriv("admin_add") <> 1) 
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
   $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_admin']." WHERE uname='".$_POST['a_username']."'");
   if(mysql_num_rows($res2)<1) 
    $res = mysql_query("INSERT INTO ".$GLOBALS['db_admin']." VALUES(0,'".$_POST['a_username']."','".$_POST['a_password']."','".$_POST['a_name']."','".$_POST['a_email']."','".$_POST['priv']."','')") or die("LINE 83".mysql_error());
   ShowMain();
   return;
  }
 }

 if ($_POST['confirm'] <> "true")
 { 
  $res = mysql_query("SELECT * from ".$GLOBALS['db_admin']." where id='".$_GET['id']."'");
  $ar = mysql_fetch_array($res);
  ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/admin_big.jpg" width="32" height="32" border="0" alt="<?=_ADDUSER;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_ADDADMIN;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_admin']) or die("<b>Line 90:</b>".mysql_error());
    echo _REGISTEREDADMINS.": ".mysql_num_rows($res);
    ?>
   </td>
  </tr>
 </table>

 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td>&nbsp;</td>
  </tr>
 </table>


  <table width="630" cellspacing="2" cellpadding="0">
   <tr>
    <td class="MojText">
     <form action="<?=$GLOBALS['PHP_SELF'];?>" method="post">
      <?=_USERNAME;?><br />
      <input type="text" class="news" value="<?=$GLOBALS['a_username'];?>" name="a_username" size="40" /><br />
      <?=_PASSWORD;?><br />
      <input type="password" class="news" value="<?=$GLOBALS['a_password'];?>" name="a_password" size="40" /><br />
      <?=_NAME;?><br />
      <input type="text" class="news" value="<?=$GLOBALS['a_name'];?>" name="a_name" size="40" /><br />
      <?=_EMAIL;?><br />
      <input type="text" class="news" value="<?=$GLOBALS['a_email'];?>" name="a_email" size="40" /><br />
      <?=_PRIVILEGES;?><br />
      <select name="priv"  class="news">
        <?
         $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_groups']." ORDER BY name"); 
         while($ar2 = mysql_fetch_array($res2))
           echo '<option value="'.$ar2[id].'">'.$ar2[name].'</option>';
        ?>
       </select>
       <input type="hidden" name="action" value="add" /><br /><br />
       <input type="hidden" name="confirm" value="true"/>
       <input type="submit" value="<?= _SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);"/>
       </form>
       </td>
      </tr>
     </table> 
 <?
 }
}

function EditAdmin()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;	// only need if you're running 4.06 or lower version of PHP

 // CHECK PRIVILEGIES
 if(CheckPriv("admin_edit") <> 1) 
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
  $res = mysql_query("UPDATE ".$GLOBALS['db_admin']." SET uname='".$_POST['username']."', passwd='".$_POST['password']."', name='".$_POST['name']."', email='".$_POST['email']."', priv='".$_POST['priv']."' WHERE id='".$_POST['id']."'") or die ("MyError"); 
  ShowMain();
  }
 }


 if($_POST['confirm'] <> "true") 
 {
  $res = mysql_query("SELECT * from ".$GLOBALS['db_admin']." where id='".$_GET['id']."'");
  $ar = mysql_fetch_array($res);
  ?>

 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/admin_big.jpg" width="32" height="32" border="0" alt="<?=_ADDUSER;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_EDITADMIN;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_admin']) or die("<b>Line 90:</b>".mysql_error());
    echo _REGISTEREDADMINS.": ".mysql_num_rows($res);
    ?>
   </td>
  </tr>
 </table>

 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td>&nbsp;</td>
  </tr>
 </table>

  <br />

    <table width="630" cellspacing="2" cellpadding="0" class="MojText">
     <tr>
      <td>
       <form action="<?=$GLOBALS['PHP_SELF'];?>" method="post">
        <?=_USERNAME;?><br />
        <input type="text" name="username" class="news" size="40" value="<?=$ar[uname];?>"/><br />
        <?=_PASSWORD;?><br />
        <input type="password" name="password" class="news" size="40" value="<?=$ar[passwd];?>"/><br />
        <?=_NAME;?><br />
        <input type="text" name="name" size="40" class="news" value="<?=$ar[name];?>"/><br />
        <?=_EMAIL;?><br />
        <input type="text" name="email" size="40" class="news" value="<?=$ar[email];?>"/><br />
        <?=_PRIVILEGES;?><br />
        <select name="priv"  class="news">
        <?
         $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_groups']." order by name"); 
         while ($ar2 = mysql_fetch_array($res2))
         {
          echo '<option value="'.$ar2[id].'"';
          if($ar2[id] == $ar[priv]) echo " selected=\"selected\" ";
          echo ">".$ar2[name]."</option>";
         }
        ?>
       </select><br/><br/>
        <input type="hidden" name="action" value="edit"/>
        <input type="hidden" name="confirm" value="true"/>
        <input type="hidden" name="id" value="<?=$_GET['id'];?>"/>
        <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);"/>
        </form>
       </td>
      </tr>
    </table> 
   <?
  }
}

function DeleteAdmin()
{
 if(!check_version("4.1.0")) global $_GET; // only need if you're running 4.06 or lower version of PHP
 // CHECK PRIVILEGIES
 if(CheckPriv("admin_del") == 1) 
 { 
  mysql_query("DELETE FROM ".$GLOBALS['db_admin']." WHERE id='".$_GET['id']."'"); 
 }
 else
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
 
 unset($GLOBALS['id']);
 ShowMain();
 return;
}
?>
