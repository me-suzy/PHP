<?php
//******************************************************************************************
//**                                                                                      **
//**  phpNewsManager v1.40                                                                **
//**  contact: info@skintech.org                                                          **
//**  Last edited: 14th.March,2003                                                        **
//**                                                                                      **
//******************************************************************************************
  
$title = "phpNewsManager $newman_ver";
include "functions.php";
include "header.php";
if ($psw == TRUE)
 if ($action == "edit") EditPoll();
 else if ($action == "add") AddPoll();
 else if ($action == "delete") DeletePoll();
 else if ($action == "multidel") MultiDelete($db_weekQ,"id","wp_del");
 else ShowMain();
include ("footer.php");

function ShowMain()
{
 if(!check_version("4.1.0")) global $_GET; // only need if you're running 4.06 or lower version of PHP
 ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"><img src="gfx/poll_big.jpg" width="32" height="32" border="0" alt="<?=_ADDWEEKLYPOLL;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"> &nbsp;<?=_ADDWEEKLYPOLL;?></a></td>
   <td align="center">
   <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_weekQ']) or die("<b>LINE 36:</b>".mysql_error());
    $num = mysql_num_rows($res);
    $myopt = ShowPages($num,$GLOBALS['page'],$GLOBALS['hits'],$GLOBALS['show']);
   ?>
   </td>
   <td align="right"><?=_SUBMITEDPOLLS.": ".$num;?></td>
  </tr>
 </table>
 
 <form action="<?=$GLOBALS['PHP_SELF'];?>" name="myform" method="post">
 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td><font color="#<?=_COLOR05;?>"><?=_OPTIONS;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_QUESTION;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_AUTHOR;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?=_CHECK;?></font></td>
  </tr>  
  <?
   $res = mysql_query("SELECT * from ".$GLOBALS['db_weekQ']." order by id desc LIMIT $myopt[0],$myopt[1]");
   while ($ar = mysql_fetch_array($res))
   {
    ?>
    <tr>
     <td width="66">
      <?if($_GET['action'] == "open" && $_GET['id'] == $ar[id]) $mact = "close"; else $mact = "open";?>
      <a href="<?=$GLOBALS['PHP_SELF'];?>?action=<?=$mact;?>&amp;id=<?=$ar[id];?>"><img src="gfx/open.gif" width="20" height="20" border="0" alt="" /></a> 
      <a href="<?=$GLOBALS['PHP_SELF'];?>?action=edit&amp;id=<?=$ar[id];?>"><img src="gfx/edit.gif" width="20" height="20" border="0" alt="" /></a> 
      <a href="javascript:Confirm('<?=$GLOBALS['PHP_SELF'];?>?action=delete&amp;id=<?=$ar[id];?>','<?=_DELETE.": ".eregi_replace("'","\'",$ar[question])."?";?>');"><img src="gfx/trash.gif" width="20" height="20" border="0" alt="" /></a>
     </td> 
     <td><?=$ar[question];?></td>
     <td align="right" valign="top"><?=$ar[author];?></td>
     <td valign="top" align="center" width="40"><input type="checkbox" name="list[]" value="<?=$ar[id];?>"/></td>
    </tr>
     <?
      if ($_GET['action'] == "open" && $_GET["id"] == $ar[id])
      {
       echo '<tr><td colspan="4" style="padding-left:90px; background:#f9f9f9;">';
	 $myvote = mysql_query("SELECT count(*) AS suma FROM ".$GLOBALS['db_weekA']." WHERE wid=".$_GET['id']) or die ("LINE 77:".mysql_error());
         $myvres = mysql_fetch_array($myvote);
	 echo _ANSWERS.":<br />";
         $gres = mysql_query("SELECT * FROM ".$GLOBALS['db_weekQ']." where id=".$_GET['id']) or die("LINE 80".mysql_error()); 
         $gar = mysql_fetch_array($gres);
         $odg = explode (":", $gar[2]);
         while ( list ($key,$values)= each($odg))
         {
          $myres = mysql_query("SELECT COUNT(*) AS vote FROM ".$GLOBALS['db_weekA']." where wid=".$_GET['id']." AND answer=$key;") or die ("LINE 85:".mysql_error());
          $myar  = mysql_fetch_array($myres);
          echo "$values: $myar[vote] ";
          if($myar[vote] <> 0 and $myvres[suma] <> 0) {$procent = $myar[vote]/$myvres[suma]*100;} else {$procent=0;}
          printf (" [%.2f",$procent);
          echo "%]<br />";
         }
        echo "</td></tr>";
     }
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

function AddPoll()
{
 if(!check_version("4.1.0")) global $_POST; // only need if you're running 4.06 or lower version of PHP

 // CHECK PRIVILEGIES
 if(CheckPriv("wp_add") == 0) 
 { 
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }
 if($_POST['confirm'] !== "true") 
 {
  ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/poll_big.jpg" width="32" height="32" border="0" alt="<?=_ADDWEEKLYPOLL;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_ADDWEEKLYPOLL;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_weekQ']) or die("<b>Line 246:</b>".mysql_error());
    echo _SUBMITEDPOLLS.": ".mysql_num_rows($res);
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
      <?=_QUESTION;?><br />
      <input type="text" name="question" class="news" style="width:400px;" /><br />
      <?=_ANSWERS;?> [<small><?=_SEPERATEANSWERSWITH;?> :</small>]<br />
      <textarea name="answers" cols="" rows="" class="news" style="width:400px; height:200px;" class=""></textarea>
      <input type="hidden" name="action" class="news" value="add" /><br /><br />
      <input type="hidden" name="confirm" class="news" value="true" />
      <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);" />
     </form>
    </td>
   </tr>
  </table> 
  <?
 }
 if($_POST['confirm'] == "true") 
 {
  $_POST['question'] = eregi_replace( "\"","&quot;",$_POST['question']);
  $_POST['question'] = eregi_replace( "'","&acute",$_POST['question']);

  $_POST['answers'] = eregi_replace( "\"","&quot;",$_POST['answers']);
  $_POST['answers'] = eregi_replace( "'","&acute",$_POST['answers']);
  $_POST['answers'] = eregi_replace( "\n",":",$_POST['answers']);

  $res = mysql_query("INSERT INTO ".$GLOBALS['db_weekQ']." VALUES (0,'".$_POST['question']."','".$_POST['answers']."','".$GLOBALS['login']."')") or die("mysql error"); 
  ShowMain();
 } 
}

function EditPoll()
{
 if(!check_version("4.1.0")) global $_GET,$_POST; // only need if you're running 4.06 or lower version of PHP

 // CHECK PRIVILEGIES
 if(CheckPriv("wp_edit") == 0) 
 { 
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if ($_POST['confirm'] !== "true") 
 {
  $res = mysql_query("SELECT * from ".$GLOBALS['db_weekQ']." where id='".$_GET['id']."'");
  $ar = mysql_fetch_array($res);

  $ar[question] = eregi_replace( "&quot;","\"",$ar[question]);
  $ar[question] = eregi_replace( "&acute","'",$ar[question]);

  $ar[answers] = eregi_replace( "&quot;","\"",$ar[answers]);
  $ar[answers] = eregi_replace( "&acute","'",$ar[answers]);
  $ar[answers] = eregi_replace( ":","\n",$ar[answers]);
  ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/poll_big.jpg" width="32" height="32" border="0" alt="<?=_ADDWEEKLYPOLL;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_EDITWEEKLYPOLL;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_weekQ']) or die("<b>Line 246:</b>".mysql_error());
    echo _SUBMITEDPOLLS.": ".mysql_num_rows($res);
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
    <td >
     <form action="<?=$GLOBALS['PHP_SELF'];?>" method="post">
      <?=_QUESTION;?><br />
      <input type="text" name="question" class="news" style="width:400px;" value="<?echo $ar[question];?>" /><br />
      <?=_ANSWERS;?> [<small><?=_SEPERATEANSWERSWITH;?>:</small>]<br />
      <textarea name="answers" rows="" class="news" cols="" style="width:400px; height:200px;" class="textarea"><?=$ar[answers];?></textarea><br />
      <input type="hidden" name="action" value="edit"/>
      <input type="hidden" name="confirm" value="true"/>
      <input type="hidden" name="id" value="<?=$_GET['id'];?>"/>
      <input type="submit" value="<?=_SUBMIT;?>"/>
     </form>
    </td>
   </tr>
  </table> 
  <?
 }
 if ($_POST['confirm'] == "true") 
 {
  $_POST['question'] = eregi_replace( "\"","&quot;",$_POST['question']);
  $_POST['question'] = eregi_replace( "'","&acute",$_POST['question']);
  $_POST['answers'] = eregi_replace( "\n",":",$_POST['answers']);
  $_POST['answers'] = eregi_replace( "\"","&quot;",$_POST['answers']);
  $_POST['answers'] = eregi_replace( "'","&acute;",$_POST['answers']);
  $res = mysql_query("UPDATE ".$GLOBALS['db_weekQ']." SET question='".$_POST['question']."', answers='".$_POST['answers']."' WHERE id=".$_POST['id']) or die("LINE 217:".mysql_error()); 
  unset($_GET['id']);
  ShowMain();
 } 
}

function DeletePoll()
{
 if(!check_version("4.1.0")) global $_GET; // only need if you're running 4.06 or lower version of PHP
 // CHECK PRIVILEGIES
 if(CheckPriv("wp_del") == 1) 
   $res = mysql_query("DELETE FROM ".$GLOBALS['db_weekQ']." where id='".$_GET['id']."'"); 
 else
   echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
 unset($GLOBALS['id']);
 ShowMain();
 return;
}
?>