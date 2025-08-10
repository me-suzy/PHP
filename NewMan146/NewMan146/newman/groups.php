<?php
//******************************************************************************************
//** phpNewsManager v1.40                                                                 **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 4th.April,2003                                                          **
//******************************************************************************************

$title = "phpNewsManager $newman_ver";
include "functions.php";
include ("header.php");
if ($psw == TRUE)
{
 if ($action == "edit")        Edit();
 else if ($action == "delete") Delete();
 else if ($action == "multidel") MultiDelete($db_groups,"id");
 else if ($action == "add")    Add();
 else ShowMain();
}
include ("footer.php");

function ShowMain()
{
 if(!check_version("4.1.0")) global $_GET;
 ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"><img src="gfx/groups_big.jpg" width="32" height="32" border="0" alt="<?=_ADDUSER;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"> &nbsp;<?=_ADDGROUP;?></a></td>
   <td align="center">
   <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_groups']) or die("<b>LINE 31:</b>".mysql_error());
    $num = mysql_num_rows($res);
    $myopt = ShowPages($num,$GLOBALS['page'],$GLOBALS['hits'],$GLOBALS['show']);
   ?>
   </td>
   <td align="right"><?=_SUBMITEDGROUPS.": ".$num;?></td>
  </tr>
 </table>
 
 <form action="<?=$GLOBALS['PHP_SELF'];?>" name="myform" method="post">
 <table width="630" align="center" cellspacing="2" cellpadding="1">
  <tr bgcolor="#<?=_COLOR02;?>" class="MojText">
   <td width="60"><font color="#<?=_COLOR05;?>"><?=_OPTIONS;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_GROUPNAME;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_USERS;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_CHECK;?></font></td>
  </tr>  
  <?
   $res = mysql_query("SELECT * FROM ".$GLOBALS['db_groups']) or die("<b>LINE 46:</b>".mysql_error());
   while ($ar = mysql_fetch_array($res))
   {
    $res2 = mysql_query("SELECT count(*) as num from ".$GLOBALS['db_admin']." where priv='$ar[id]'") or die("<b>Error:</b>".mysql_error());
    $ar2 = mysql_fetch_array($res2);
    ?>
    <tr class="MojText">
     <td width="44">
      <a href="<?=$_GLOBALS['PHP_SELF'];?>?action=edit&amp;id=<?=$ar[id];?>"><img src="gfx/edit.gif" width="20" height="20" border="0" alt=""/></a> 
      <a href="javascript:Confirm('<?=$GLOBALS['PHP_SELF'];?>?action=delete&amp;id=<?=$ar[id];?>','<?=_DELETE.": ".eregi_replace("'","\'",$ar[name])."?";?>');"><img src="gfx/trash.gif" width="20" height="20" border="0" alt="" /></a>
     </td> 
     <td valign="top"><?=$ar[name];?></td>
     <td valign="top"><?=$ar2[num];?></td>
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

function Add()
{
 if(!check_version("4.1.0")) global $_POST;
 
 // CHECK PRIVILEGIES
 if(CheckPriv("group_add") <> 1)
 { 
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }
 if($_POST['confirm'] == "true" && empty($_POST['grp_name']))   
 {
  echo "<script type=\"text/javascript\">alert('"._NONAME."');</script>";
  unset($_POST['confirm']);
 }
   
 if ($_POST['confirm'] == "true") 
 {
  $res2 = mysql_query("SELECT * FROM ".$GLOBALS['db_groups']." WHERE name='".$_POST['grp_name']."'") or die("<b>LINE 83:</b>".mysql_error());

  /************************************************/
  $query = "SELECT * FROM ".$GLOBALS['db_groups'];
  $result = mysql_query($query);  

  $query = "INSERT INTO ".$GLOBALS['db_groups']." VALUES(0,'".$_POST['grp_name']."','".$_POST['desc']."',";
  for($x=3;$x<mysql_num_fields($result);$x++)
  {
   $fname =  mysql_field_name($result, $x);
   $query .= "'".$_POST[$fname]."'";
   if($x < mysql_num_fields($result)-1) $query .= ", ";
   }
   $query .= ");";
  /************************************************/
  if(mysql_num_rows($res2)<1) 
    mysql_query($query) or die ("<b>Line 109:</b>".mysql_error());
  ShowMain();
 }
 if ($_POST['confirm'] <> "true")
 { 
  $res = mysql_query("SELECT * from ".$GLOBALS['db_groups']." where id='".$_GET['id']."'");
  $ar = mysql_fetch_array($res);
  ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/groups_big.jpg" width="32" height="32" border="0" alt="<?=_ADDUSER;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_ADDGROUP;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_groups']) or die("<b>Line 90:</b>".mysql_error());
    echo _SUBMITEDGROUPS.": ".mysql_num_rows($res);
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
    <form action="<?=$GLOBALS['PHP_SELF'];?>" name="groups" method="post">
     <?=_GROUPNAME;?>:<br/>
        <input type="text" class="news" name="grp_name" size="40"/><br/>
        <?=_INFO;?><br/>
        <input type="text" name="desc" class="news" size="40"/><br/>
        <br/>
        <?=_SELECT;?>:<br/>
 
        <?
         /************************************************/
	 $m = 0;
	 $fnum = 5;
	
	 $query = "SELECT * FROM ".$GLOBALS['db_groups'];
	 $result = mysql_query($query);  

	 echo '<table cellspacing="1" cellpadding="4" class="MojText" bgcolor="#'._COLOR02.'">';
	 for($x=3;$x<mysql_num_fields($result);$x++)
	 {
	  $fname =  mysql_field_name($result, $x);
	  list($name,$task) = explode("_",$fname);
	  if(empty($last) || $last <> $name)
	  {
	   if($m<$fnum) 
	   {
	    $l = $fnum - $m;
	    echo '<td colspan="'.$l.'"></td>';
	   }
	   $ime = "echo _".strtoupper($name).";";
	   echo '</tr><tr bgcolor="#ffffff"><td>';
	   eval($ime);
	   echo '</td>';
	   $m=0;
	  }
	  echo '<td><input type="checkbox" name="'.$fname.'"  value="1"/> '.$task.'</td>';
	  $m++;
	  $last = $name;
	 }
	 if($m<$fnum) 
	 {
	  $l = $fnum - $m;
	  echo '<td colspan="'.$l.'"></td>';
	 }
	 echo '</tr></table>';
	 
	 /************************************************/
 ?>
         <br/>
        <input type="button" name="CheckAll" value="<?=_CHECK_ALL;?>" onclick="checkAll(document.groups)" class="news">
        <input type="button" name="UnCheckAll" value="<?=_UNCHECK_ALL;?>" onclick="uncheckAll(document.groups)" class="news">

        <br/>
        <input type="hidden" name="action" value="add"/><br/><br/>
        <input type="hidden" name="confirm" value="true"/>
        <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);"/>
        </form>
<?}?>
  </td></tr></table>
 <?
}

function Edit()
{
 if(!check_version("4.1.0")) global $_POST,$_GET;

 // CHECK PRIVILEGIES
 if(CheckPriv("group_edit") <> 1)
 { 
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if($_POST['confirm'] <> "true") 
 {
  $res = mysql_query("SELECT * from ".$GLOBALS['db_groups']." where id='".$_GET['id']."'");
  $ar = mysql_fetch_array($res);
  ?>
  <table width="630" cellspacing="2" cellpadding="0" class="MojText">
   <tr>
    <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/groups_big.jpg" width="32" height="32" border="0" alt="<?=_ADDUSER;?>"/></a></td>
    <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
    <td align="center"><font size="4" face="Arial"> <b><?=_EDITGROUP;?></b></font></td>
    <td align="right">
    <?
     $res = mysql_query("SELECT * FROM ".$GLOBALS['db_groups']) or die("<b>Line 246:</b>".mysql_error());
     echo _SUBMITEDGROUPS.": ".mysql_num_rows($res);
    ?>
    </td>
   </tr>
  </table>
  <table width="630" cellspacing="2" cellpadding="1" class="MojText"><tr bgcolor="#<?=_COLOR02;?>"><td>&nbsp;</td></tr></table>
  <table width="630" cellspacing="2" cellpadding="0" class="MojText">
   <tr>
    <td>
     <form action="<?=$GLBOALS['PHP_SELF'];?>" method="post" name="groups">
      <?=_GROUPNAME;?><br/>
      <input type="text" name="grp_name" class="news" value="<?=$ar[1];?>" size="40"/><br/>
      <?=_INFO;?><br/>
      <input type="text" name="desc" class="news" value="<?=$ar[2];?>" size="40"/><br/>
      <?=_SELECT;?>:<br/>

        <?
         /************************************************/
	 $m = 0;
	 $fnum = 5;
	
	 $query = "SELECT * FROM ".$GLOBALS['db_groups']." WHERE id='".$_GET['id']."'";
	 $result = mysql_query($query);  

	 echo '<table cellspacing="1" cellpadding="4" class="MojText" bgcolor="#'._COLOR02.'">';
	 for($x=3;$x<mysql_num_fields($result);$x++)
	 {
	  $fname =  mysql_field_name($result, $x);
	  list($name,$task) = explode("_",$fname);
	  if(empty($last) || $last <> $name)
	  {
	   if($m<$fnum) 
	   {
	    $l = $fnum - $m;
	    echo '<td colspan="'.$l.'"></td>';
	   }
	   $ime = "echo _".strtoupper($name).";";
	   echo '</tr><tr bgcolor="#ffffff"><td>';
	   eval($ime);
	   echo '</td>';
	   $m=0;
	  }
	  echo '<td><input type="checkbox" name="'.$fname.'" ';
	  if($ar[$fname] == 1)
	    echo 'checked="checked"';
	  echo ' value="1"/> '.$task.'</td>';
	  $m++;
	  $last = $name;
	 }
	 if($m<$fnum) 
	 {
	  $l = $fnum - $m;
	  echo '<td colspan="'.$l.'"></td>';
	 }
	 echo '</tr></table>';
	 
	 /************************************************/
        ?>
        <br/>
        <input type="button" name="CheckAll" value="<?=_CHECK_ALL;?>" onclick="checkAll(document.groups)" class="news">
        <input type="button" name="UnCheckAll" value="<?=_UNCHECK_ALL;?>" onclick="uncheckAll(document.groups)" class="news">

        <br/>
        <input type="hidden" name="action" value="edit"/><br/><br/>
        <input type="hidden" name="confirm" value="true"/>
        <input type="hidden" name="id" value="<?=$_GET['id'];?>"/>
        <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);"/>
        </form>
       </td>
      </tr>
    </table> 
   <?
  }
 if ($_POST['confirm'] == "true") 
 {
  /************************************************/
  $query = "SELECT * FROM ".$GLOBALS['db_groups'];
  $result = mysql_query($query);  

  $query = "UPDATE ".$GLOBALS['db_groups']." SET name='".$_POST['grp_name']."', description='".$_POST['desc']."', ";
  for($x=3;$x<mysql_num_fields($result);$x++)
  {
   $fname =  mysql_field_name($result, $x);
   $query .= $fname."='".$_POST[$fname]."'";
   if($x < mysql_num_fields($result)-1) $query .= ", ";
   }
   $query .= " WHERE id='".$_POST['id']."';";
  /************************************************/
  $res = mysql_query($query) or die ("<b>Line 321:</b>".mysql_error()); 
  ShowMain();
 } 
}

function Delete()
{
 if(!check_version("4.1.0")) global $_GET; 
 // CHECK PRIVILEGIES
 if(CheckPriv("group_del") == 1) 
   mysql_query("DELETE FROM ".$GLOBALS['db_groups']." WHERE id='".$_GET['id']."'"); 
 else
   echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
 unset($GLOBALS['id']);
 ShowMain();
 return;
}

?>
