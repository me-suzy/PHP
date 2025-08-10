<?php
//******************************************************************************************
//** phpNewsManager v1.40                                                                 **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 18th.March,2003                                                         **
//******************************************************************************************

$title  = "phpNewsManager $newman_ver";
$makejs = "partners";
include "functions.php";
include "header.php";
if($psw == TRUE)
 if ($action == "edit") EditPartners();   
 else if ($action == "add") AddPartners();    
 else if ($action == "delete") DeletePartners(); 
 else if ($action == "multidel") MultiDelete($db_partners,"id","parnter_del");
 else if ($action == "upload") UploadPicture($partners_path,_ADDPARTNERS,"partner_ul"); 
 else ShowMain();
include ("footer.php");


function ShowMain()
{
 if(!check_version("4.1.0")) global $_GET;
 ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"><img src="gfx/partners_big.jpg" width="32" height="32" border="0" alt="<?=_ADDPARTNERS;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add">&nbsp;<?=_ADDPARTNERS;?></a></td>
   <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><img src="gfx/upload_picture_big.jpg" width="32" height="32" border="0" alt="<?=_UPLOADLOGO;?>"/></a></td>
   <td><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><?=_UPLOADLOGO;?></a></td>
   <td align="center">
   <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_partners']) or die("<b>LINE 31:</b>".mysql_error());
    $num = mysql_num_rows($res);
    $myopt = ShowPages($num,$GLOBALS['page'],$GLOBALS['hits'],$GLOBALS['show']);
   ?>
   </td>
   <td align="right"><?=_SUBMITEDPARTNERS.": ".$num;?></td>
  </tr>
 </table>
   
 <form action="<?=$GLOBALS['PHP_SELF'];?>" name="myform" method="post">
 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td><font color="#<?=_COLOR05;?>"><?= _OPTIONS;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?= _NAME;?></font></td>
   <td><font color="#<?=_COLOR05;?>"><?= _LINK;?></font></td>
   <td width="20"><font color="#<?=_COLOR05;?>"><?= _IN;?></font></td>
   <td width="20"><font color="#<?=_COLOR05;?>"><?= _OUT;?></font></td>
   <td width="20"><font color="#<?=_COLOR05;?>"><?= _AFFILIATES;?></font></td>
   <td width="20"><font color="#<?=_COLOR05;?>"><?= _GFX;?></font></td>
   <td width="20"><font color="#<?=_COLOR05;?>"><?= _CHECK;?></font></td>
  </tr>  
  <?
   $res = mysql_query("SELECT * from ".$GLOBALS['db_partners']." ORDER BY name LIMIT $myopt[0],$myopt[1]");
   while ($ar = mysql_fetch_array($res))
   {
    if ($ar[main]==0) $main = "No"; else $main = "Yes";
    if ($ar[gfx]==0) $gfx = "No"; else $gfx = "Yes";         
    ?>
    <tr>
     <td width="44">
      <a href="<?=$GLOBALS['PHP_SELF'];?>?action=edit&amp;id=<?=$ar[id];?>"><img src="gfx/edit.gif" width="20" height="20" border="0" alt="" /></a> 
      <a href="javascript:Confirm('<?=$GLOBALS['PHP_SELF'];?>?action=delete&amp;id=<?=$ar[id];?>','<?=_DELETE.": ".eregi_replace("'","\'",$ar[name])."?";?>');"><img src="gfx/trash.gif" width="20" height="20" border="0" alt="" /></a>
     </td> 
     <td><?=$ar[name];?></td>
     <td align="right" valign="top"><?=$ar[link];?></td>
     <td align="right" valign="top"><?=$ar[clicks];?></td>
     <td align="right" valign="top"><?=$ar[out];?></td>
     <td align="right" valign="top"><?=$main;?></td>
     <td align="right" valign="top"><?=$gfx;?></td>
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

function AddPartners()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;
 // CHECK PRIVILEGIES
 if(CheckPriv("partner_add") <> 1) 
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
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/category_big.jpg" width="32" height="32" border="0" alt="<?=_ADDCATEGORY;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><img src="gfx/upload_picture_big.jpg" width="32" height="32" border="0" alt="<?=_UPLOADLOGO;?>"/></a></td>
   <td><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><?=_UPLOADLOGO;?></a></td>
   <td align="center"><font size="4" face="Arial"> <b><?=_ADDPARTNERS;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_partners']) or die("<b>Line 246:</b>".mysql_error());
    echo _SUBMITEDPARTNERS.": ".mysql_num_rows($res);
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
        <form method="post" action="<?=$GLOBALS['PHP_SELF'];?>" name="forma">
        <?=_NAME;?><br/>
        <input type="text" name="name" class="news" size="60"/><br/>
        <?=_LINK;?><br/>
        <input type="text" name="link" class="news" size="60"/><br/>
        <?=_IMAGE;?><br/>
       <select name="picture" size="8" class="news" onclick="Swap();" onchange="Swap();">
       <?
        $d = dir($GLOBALS['partners_path']);
        $x=0;
	while($entry=$d->read()) {$x++;if ($x > 2) {echo "<option value=\"$entry\""; if ($entry == $ar[topicimage]){echo " selected ";};echo ">$entry</option>";}}
        $d->close();
       ?>
       </select>
       <p><img name="button" src="http://www.skinbase.org/gfx/partners/linkus.jpg" width="88" height="31" border="0" alt=""/></p>
      <br/>
      <?=_DESCRIPTION;?><br/>
      <textarea name="desc" rows="" cols="" class="news" ></textarea>
      <br/>
      <br/>

	<?=_AFFILIATES;?><br/>
	<select name="afil" class="news" >
	 <option value="0"> no</option>
	 <option value="1"> yes</option>
	</select><br/>

	<?=_GFX;?><br/>
	<select name="gfx" class="news" >
	 <option value="0"> no</option>
	 <option value="1"> yes</option>
	</select><br/>
	
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
    $_POST['desc'] = ereg_replace( "\"","&quot;",$_POST['desc']);
    $_POST['desc'] = ereg_replace( "'","&acute",$_POST['desc']);

    $res = mysql_query("INSERT INTO ".$GLOBALS['db_partners']." VALUES (0,'".$_POST['name']."','".$_POST['link']."','".$_POST['picture']."','".$_POST['desc']."',0,0,'".$_POST['afil']."','".$_POST['gfx']."')") or die("mysql error"); 
    ShowMain();
   } 
}


function EditPartners()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;
 // CHECK PRIVILEGIES
 if(CheckPriv("partner_edit") <> 1) 
 {
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if($_POST['confirm'] <> "true") 
 {
  $res = mysql_query("SELECT * FROM ".$GLOBALS['db_partners']." WHERE id='".$_GET['id']."'");
  $ar = mysql_fetch_array($res);
  $_POST['description'] = ereg_replace( "&quot;","\"",$ar[description]);
  $_POST['description'] = ereg_replace( "&acute","'",$_POST['description']);
  ?>
  <table width="630" cellspacing="2" cellpadding="0" class="MojText">
   <tr>
    <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/category_big.jpg" width="32" height="32" border="0" alt="<?=_ADDCATEGORY;?>"/></a></td>
    <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
    <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><img src="gfx/upload_picture_big.jpg" width="32" height="32" border="0" alt="<?=_UPLOADLOGO;?>"/></a></td>
    <td><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><?=_UPLOADLOGO;?></a></td>
    <td align="center"><font size="4" face="Arial"> <b><?=_EDITPARTNER;?></b></font></td>
    <td align="right">
    <?
     $res = mysql_query("SELECT * FROM ".$GLOBALS['db_partners']) or die("<b>Line 246:</b>".mysql_error());
     echo _SUBMITEDPARTNERS.": ".mysql_num_rows($res);
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
      <?= _NAME;?><br/>
      <input type="text" name="name" class="news" size="60" value="<?= $ar[name];?>"/><br/>
      <?= _LINK;?><br/>
      <input type="text" name="link" class="news" size="60" value="<?= $ar[link];?>"/><br/>
      <?= _IMAGE;?><br/>
      <select name="picture" size="8" class="news" onclick="Swap();" onchange="Swap();">
      <?
       $d = dir($GLOBALS['partners_path']);
       $x=0;
       while($entry=$d->read()) 
       {
       	$x++;
       	if ($x > 2) 
       	{
	 echo "<option value=\"$entry\""; if ($entry == $ar[picture]){echo " selected=\"selected\" ";};echo ">$entry</option>";}
        }
       $d->close();
      ?>
      </select>
      <p><img name="button" src="http://www.skinbase.org/gfx/partners/<?= $ar[picture];?>" width="88" height="31" border="0" alt="" /></p>
      <br/>
      <?= _DESCRIPTION;?><br/>
      <textarea name="description" class="news" cols="" rows=""><?= $ar[description];?></textarea>
      <br/><br/>
      <?= _AFFILIATES;?><br/>
      <select name="afil" class="news" >
       <option value="0" <?if($ar[main]==0) {echo " selected=\"selected\" ";};?>> no</option>
       <option value="1" <?if($ar[main]==1) {echo " selected=\"selected\" ";};?>> yes</option>
      </select><br/>
      <?= _GFX;?><br/>
      <select name="gfx" class="news" >
       <option value="0" <?if($ar[gfx]==0) {echo " selected=\"selected\" ";};?>> no</option>
       <option value="1" <?if($ar[gfx]==1) {echo " selected=\"selected\" ";};?>> yes</option>
      </select><br/>
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
  $_POST['description'] = ereg_replace( "\"","&quot;",$_POST['description']);
  $_POST['description'] = ereg_replace( "'","&acute",$_POST['description']);
  $res = mysql_query("UPDATE ".$GLOBALS['db_partners']." SET name='".$_POST['name']."',picture='".$_POST['picture']."', link='".$_POST['link']."',description='".$_POST['description']."', main='".$GLOBALS['afil']."', gfx='".$_POST['gfx']."' WHERE id='".$_POST['id']."'") or die("<b>LINE 262:</b>".mysql_error());
  ShowMain();
 } 
}

function DeletePartners()
{
 if(!check_version("4.1.0")) global $_GET; // only need if you're running 4.06 or lower version of PHP
 if(CheckPriv("partner_del") == 1) 
   mysql_query("DELETE FROM ".$GLOBALS['db_partners']." WHERE id='".$_GET['id']."'"); 
 else
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
 ShowMain();
}
?>