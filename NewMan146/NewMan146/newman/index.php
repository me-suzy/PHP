<?php
  //******************************************************************************************
  //** phpNewsManager                                                                       **
  //** contact: gregor@klevze.si                                                            **
  //** Last edited: 7th.June,2002                                                           **
  //******************************************************************************************

// LOGOUT FUNCTION
if ($action == "Logout")
{
 $info = "0:0";
 setcookie("nm_user","$info",time()+15552000); // 6 mo is 15552000
 $nm_user = "";
 header ("Location: index.php"); 
}

  $title    = "phpNewsManager $newman_ver";
  include ("colors.php");
  include "functions.php";
  include("header.php");

if ($psw == TRUE)
{
 ?>
   <table width="630" cellspacing="2" cellpadding="0" class="MojText">
    <tr>
     <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/news_big.jpg" width="32" height="32" border="0" alt=""/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center" width="510"><font size="4" face="Arial"> <b><?=_STATISTICS;?></b></font></td>
  </tr>
 </table>
  <table width="630" cellspacing="2" cellpadding="1" class="MojText">
  <tr bgcolor="#<?=_COLOR02;?>">
   <td>&nbsp;</td>
  </tr>
 </table>

 <table width="630" cellspacing="0" cellpadding="0" border="0" class="MojText">
  <tr>
   <td valign="top">

     <table width="99%" cellspacing="1" cellpadding="5" class="MojText">
      <tr>
       <td valign="top">
        <?
         $res1 = mysql_query("SELECT count(*) from $db_news") or die("error1");
         $ar1 = mysql_fetch_array($res1);
         $res2 = mysql_query("SELECT count(*) from $db_news_comments") or die("error2");
         $ar2 = mysql_fetch_array($res2);
         $res3 = mysql_query("SELECT count(*) from $db_weekQ") or die("error3");
         $ar3 = mysql_fetch_array($res3);
         $res4 = mysql_query("SELECT count(*) from $db_weekA") or die("error4");
         $ar4 = mysql_fetch_array($res4);
         $res5 = mysql_query("SELECT count(*) from $db_pnews") or die(myError());
         $ar5 = mysql_fetch_array($res5);
         $res6 = mysql_query("SELECT count(*) from $db_users") or die(myError());
         $ar6 = mysql_fetch_array($res6);

         if(empty($ar1[0])) $ar1[0]=0;
         if(empty($ar2[0])) $ar2[0]=0;
         if(empty($ar3[0])) $ar3[0]=0;
         if(empty($ar4[0])) $ar4[0]=0;
         if(empty($ar5[0])) $ar5[0]=0;
         if(empty($ar6[0])) $ar6[0]=0;

         $nfo = mysql_query("SELECT info FROM $db_admin where uname='$login'") or die(myError());;
         $inf=mysql_fetch_array($nfo);
         $lst = explode("#", $inf[0]);
        ?>
        <br />
        
        <table cellspacing="1" cellpadding="5" bgcolor="#dddddd" align="center" class="MojText2">
         <tr bgcolor="#EEEEEE"><td><?=_STATISTICS;?></td><td><?=_ACTUAL;?></td><td><?=_LASTVISIT;?></td><td><?=_NEW;?></td></tr>
         <tr bgcolor="#FFFFFF"><td><?=_ONLINENEWS;?>:</td><td align="right"> <b><?=$ar1[0];?></b></td><td align="right"><b><?=$lst[0];?></b></td><td align="right"><b><?=$ar1[0]-$lst[0];?></b></td></tr>
         <tr bgcolor="#FFFFFF"><td><?=_NEWSCOMMENTS;?>:</td><td align="right"> <b><?=$ar2[0];?></b></td><td align="right"><b><?=$lst[1];?></b></td><td align="right"><b><?=$ar2[0]-$lst[1];?></b></td></tr>
         <tr bgcolor="#FFFFFF"><td><?=_WEEKLYPOLLQ;?>:</td><td align="right"> <b><?=$ar3[0];?></b></td><td align="right"><b><?=$lst[2];?></b></td><td align="right"><b><?=$ar3[0]-$lst[2];?></b></td></tr>
         <tr bgcolor="#FFFFFF"><td><?=_WEEKLYPOLLA;?>:</td><td align="right"> <b><?=$ar4[0];?></b></td><td align="right"><b><?=$lst[3];?></b></td><td align="right"><b><?=$ar4[0]-$lst[3];?></b></td></tr>
         <tr bgcolor="#FFFFFF"><td><?=_PUBLICNEWS;?>:</td><td align="right"> <b><?=$ar5[0];?></b></td><td align="right"><b><?=$lst[4];?></b></td><td align="right"><b><?=$ar5[0]-$lst[4];?></b></td></tr>
         <tr bgcolor="#ffFFFF"><td><?=_REGISTEREDUSERS;?>:</td><td align="right"> <b><?=$ar5[0];?></b></td><td align="right"><b><?=$lst[5];?></b></td><td align="right"><b><?=$ar6[0]-$lst[5];?></b></td></tr>
        </table>
        <br />

       </td>
      </tr>
     </table>

     <?mysql_query("UPDATE $db_admin SET info='$ar1[0]#$ar2[0]#$ar3[0]#$ar4[0]#$ar5[0]#$ar6[0]' where uname='$login'");?>

   </td>
  </tr>
 </table>
 <?
  }
  include ("footer.php");
 ?>