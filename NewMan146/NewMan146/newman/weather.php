<?php
//******************************************************************************************
//** phpNewsManager v1.45                                                                 **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 6th.April, 2003                                                         **
//******************************************************************************************

$title  = "phpNewsManager $newman_ver";
$makejs = "weather";
require "functions.php";
require "header.php";

if($psw == TRUE)
 if ($action == "edit") Edit();   
 else if ($action == "add") Add();    
 else if ($action == "delete") Delete(); 
 else if ($action == "multidel") MultiDelete($db_weather,"id","weatherr_del");
 else if ($action == "upload") UploadPicture($weather_path,_ADDWEATHER,"weather_ul"); 
 else ShowMain();
include ("footer.php");


function ShowMain()
{
 if(!check_version("4.1.0")) global $_GET;
 ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add"><img src="gfx/sonce.gif" width="32" height="32" border="0" alt="<?=_ADDWEATHER;?>"/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=add">&nbsp;<?=_ADDWEATHER;?></a></td>
   <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><img src="gfx/upload_picture_big.jpg" width="32" height="32" border="0" alt="<?=_UPLOADLOGO;?>"/></a></td>
   <td><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><?=_UPLOADLOGO;?></a></td>
   <td align="center">
   <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_weather']) or die("<b>LINE 35:</b>".mysql_error());
    $num = mysql_num_rows($res);
    $myopt = ShowPages($num,$GLOBALS['page'],$GLOBALS['hits'],$GLOBALS['show']);
   ?>
   </td>
   <td align="right"><?=_SUBMITEDWEATHER.": ".$num;?></td>
  </tr>
 </table>
   
 <form action="<?=$GLOBALS['PHP_SELF'];?>" name="myform" method="post">
 <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td width="60"><?= _OPTIONS;?></td>
   <td><?= _DATE;?></td>
   <td><?= _MORNING;?></td>
   <td width="20"><?= _DAILY;?></td>
   <td width="30"><?=_CHECK;?></td>
  </tr>  
  <?
   $res = mysql_query("SELECT * from ".$GLOBALS['db_weather']." ORDER BY datum DESC LIMIT $myopt[0],$myopt[1]") or die("LINE 57:".mysql_error());
   while ($ar = mysql_fetch_array($res))
   {
    ?>
    <tr>
     <td width="44">
      <a href="<?=$GLOBALS['PHP_SELF'];?>?action=edit&amp;id=<?=$ar[id];?>"><img src="gfx/edit.gif" width="20" height="20" border="0" alt="" /></a> 
      <a href="javascript:Confirm('<?=$GLOBALS['PHP_SELF'];?>?action=delete&amp;id=<?=$ar[id];?>','<?=_DELETE.": ".eregi_replace("'","\'",$ar[name])."?";?>');"><img src="gfx/trash.gif" width="20" height="20" border="0" alt="" /></a>
     </td> 
     <td valign="top"><?=$ar[datum];?></td>
     <td width="30" align="right" valign="top"><?=$ar[morning];?></td>
     <td width="30" align="right" valign="top"><?=$ar[daily];?></td>
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
 if(!check_version("4.1.0")) global $_GET,$_POST;
 // CHECK PRIVILEGIES
 if(CheckPriv("weather_add") <> 1) 
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
   <td align="center"><font size="4" face="Arial"> <b><?=_ADDWEATHER;?></b></font></td>
   <td align="right">
    <?
    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_weather']) or die("<b>Line 246:</b>".mysql_error());
    echo _SUBMITEDWEATHER.": ".mysql_num_rows($res);
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
    <form method="post" action="<?=$GLOBALS['PHP_SELF'];?>" name="forma">
      <?=_DATE;?><br/>
      <select name="datum" class="news">
       <?
        for($x=-14;$x<14;$x++)
        {
          $now = getdate();
  	  $datum =  date ("Y-m-d", mktime (0,0,0,$now[mon],$now[mday]+$x,$now[year]));
  	  $datum2 =  date ("l, d.m.y", mktime (0,0,0,$now[mon],$now[mday]+$x,$now[year]));
  	  echo '<option ';
  	  if($x == 0) echo 'style="color:white;background:#4444aa;" ';
  	  echo 'value="'.$datum.'"';
  	  if($x == 0) echo ' selected="selected" ';
  	  echo '>'.$datum2.'</option>';
        }
       ?>
      </select>
      
      <table width="300" class="MojText">
      <tr>
      <td width="150">
      <?=_IMAGE;?><br/>
      <select name="picture" size="8" class="news" onclick="Swap();" onchange="Swap();">
       <?
        $d = dir($GLOBALS['weather_path']);
        $x=0;
	while($entry=$d->read()) {$x++;if ($x > 2) echo "<option value=\"$entry\">$entry</option>";}
        $d->close();
       ?>
       </select>
       </td><td>
       <p><img name="button" src="./gfx/blank.gif" border="0" alt=""/></p>
       </td></tr>
       <tr>
        <td>
         <?=_MORNING;?>
         <input type="text" name="morning" class="news"/>
        </td>       
        <td>
         <?=_DAILY;?>
         <input type="text" name="daily" class="news"/>
        </td>       
       </tr>
       </table>
      <br/>
      
      <?=_PREVIEW;?><br/>
      <textarea name="preview" rows="6" cols="90" class="news" ></textarea>
      <br/>
      <?=_FULL_TEXT;?><br/>
      <textarea name="description" rows="6" cols="90" class="news" ></textarea>
	
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

    $res = mysql_query("INSERT INTO ".$GLOBALS['db_weather']." VALUES (0,'".$_POST['datum']."','".$_POST['morning']."','".$_POST['daily']."','".$_POST['picture']."','".$_POST['preview']."','".$_POST['description']."')") or die("mysql error"); 
    ShowMain();
   } 
}


function Edit()
{
 if(!check_version("4.1.0")) global $_GET,$_POST;
 // CHECK PRIVILEGIES
 if(CheckPriv("weather_edit") <> 1) 
 {
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if($_POST['confirm'] <> "true") 
 {
  $res = mysql_query("SELECT * FROM ".$GLOBALS['db_weather']." WHERE id='".$_GET['id']."'");
  $ar = mysql_fetch_array($res);
  $_POST['description'] = ereg_replace( "&quot;","\"",$ar[description]);
  $_POST['description'] = ereg_replace( "&acute","'",$_POST['description']);
  $_POST['preview'] = ereg_replace( "&quot;","\"",$ar[preview]);
  $_POST['preview'] = ereg_replace( "&acute","'",$_POST['preview']);
  ?>
  <table width="630" cellspacing="2" cellpadding="0" class="MojText">
   <tr>
    <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/category_big.jpg" width="32" height="32" border="0" alt="<?=_ADDCATEGORY;?>"/></a></td>
    <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
    <td width="35"><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><img src="gfx/upload_picture_big.jpg" width="32" height="32" border="0" alt="<?=_UPLOADLOGO;?>"/></a></td>
    <td><a href="<?=$GLOBALS['PHP_SELF'];?>?action=upload"><?=_UPLOADLOGO;?></a></td>
    <td align="center"><font size="4" face="Arial"> <b><?=_EDITWEATHER;?></b></font></td>
    <td align="right">
    <?
     $res = mysql_query("SELECT * FROM ".$GLOBALS['db_weather']) or die("<b>Line 246:</b>".mysql_error());
     echo _SUBMITEDWEATHER.": ".mysql_num_rows($res);
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
    <form method="post" action="<?=$GLOBALS['PHP_SELF'];?>" name="forma">
      <?=_DATE;?><br/>
      <select name="datum" class="news">
       <?
        for($x=-14;$x<14;$x++)
        {
          $now = getdate();
  	  $datum =  date ("Y-m-d", mktime (0,0,0,$now[mon],$now[mday]+$x,$now[year]));
  	  $datum2 =  date ("l, d.m.y", mktime (0,0,0,$now[mon],$now[mday]+$x,$now[year]));
  	  echo '<option ';
  	  if($x == 0) echo 'style="color:white;background:#4444aa;" ';
  	  echo 'value="'.$datum.'"';
  	  if($datum == $ar[datum]) echo ' selected="selected" ';
  	  echo '>'.$datum2.'</option>'."\n";
        }
       ?>
      </select>
      
      <table width="300" class="MojText">
      <tr>
      <td width="150">
      <?=_IMAGE;?><br/>
      <select name="picture" size="8" class="news" onclick="Swap();" onchange="Swap();">
       <?
        $d = dir($GLOBALS['weather_path']);
        $x=0;
	while($entry=$d->read()) 
	{
	 $x++;
	 if($x > 2) 
	 {
	  echo '<option value="'.$entry.'" ';
	  if($entry == $ar[picture]) echo 'selected="selected"';
	  echo '>'.$entry.'</option>';
	 }
	}
        $d->close();
       ?>
       </select>
       </td><td>
       <p><img name="button" src="<?=$GLOBALS['weather_url']."/".$ar[picture];?>" border="0" alt=""/></p>
       </td></tr>
       <tr>
        <td>
         <?=_MORNING;?>
         <input type="text" name="morning" value="<?=$ar[morning]?>" class="news"/>
        </td>       
        <td>
         <?=_DAILY;?>
         <input type="text" name="daily" value="<?=$ar[daily]?>" class="news"/>
        </td>       
       </tr>
       </table>
      <br/>
      
      <?=_PREVIEW;?><br/>
      <textarea name="preview" rows="6" cols="90" class="news" ><?=$ar[preview];?></textarea>
      <br/>
      <?=_FULL_TEXT;?><br/>
      <textarea name="description" rows="6" cols="90" class="news"><?=$ar[description];?></textarea>
	
      <br/>
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
 if ($_POST['confirm'] == "true") 
 {
  $_POST['description'] = ereg_replace( "\"","&quot;",$_POST['description']);
  $_POST['description'] = ereg_replace( "'","&acute",$_POST['description']);
  $_POST['preview'] = ereg_replace( "\"","&quot;",$_POST['preview']);
  $_POST['preview'] = ereg_replace( "'","&acute",$_POST['preview']);

  $res = mysql_query("UPDATE ".$GLOBALS['db_weather']." SET datum='".$_POST['datum']."',morning='".$_POST['morning']."', daily='".$_POST['daily']."',description='".$_POST['description']."', preview='".$GLOBALS['preview']."', picture='".$GLOBALS['picture']."' WHERE id='".$_POST['id']."'") or die("<b>LINE 262:</b>".mysql_error());
  ShowMain();
 } 
}

function Delete()
{
 if(!check_version("4.1.0")) global $_GET; // only need if you're running 4.06 or lower version of PHP
 if(CheckPriv("weather_del") == 1) 
   mysql_query("DELETE FROM ".$GLOBALS['db_weather']." WHERE id='".$_GET['id']."'"); 
 else
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
 ShowMain();
}
?>