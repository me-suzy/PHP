<?php
  //******************************************************************************************
  //**                                                                                      **
  //** phpNewsManager v1.30                                                                 **
  //** contact: gregor@klevze.si                                                            **
  //** Last edited: 27th.May,2002                                                           **
  //******************************************************************************************

$title    = "phpNewsManager $newman_ver";
include "functions.php";
include("header.php");

if ($action == "category") DisplayGroup();
else if ($action == "shownews") ShowNews();
else ShowGroups();

include("footer.php");


Function ShowGroups()
{
?>
<br/>
<table width="630" cellspacing="0" cellpadding="2" bgcolor="#<?=_COLOR02;?>" align="center" class="MojHead">
 <tr> 
 <td><?=$GLOBALS['site_title'];?></td>
</tr>
</table>


<table width="630" border="0" cellspacing="0" cellpadding="2" align="center" class="MojText">
 <tr> 
  <td>
    <?php
     $res = mysql_query("SELECT * FROM ".$GLOBALS['db_topic']." ORDER BY topictext"); 
     echo '<table width="630"><tr>';
     while ($ar = mysql_fetch_array($res))
      {
       $x++;
       print "<td align=\"center\" height=\"100\"><a href=\"?action=category&amp;id=$ar[id]\"><img src=\"topic/$ar[topicimage]\" alt=\"$ar[topictext]\" border=\"0\"/></a></td>";
       if ($x == 5) { print "</tr><tr>";$x=0;} 
      }
    ?>
   </tr></table>
  </td>
 </tr>
</table>
<?
}

Function DisplayGroup()
{
 if(!check_version("4.1.0")) global $_GET;

 $res = mysql_query("SELECT * FROM ".$GLOBALS['db_topic']." where id='".$_GET['id']."'"); 
 $tp = mysql_fetch_array($res)
?>
<br/>
<table width="630" border="0" cellspacing="0" cellpadding="2" bgcolor="#<?=_COLOR02;?>" align="center" class="MojHead">
 <tr> 
  <td><a href="<?=$GLOBALS['PHP_SELF'];?>"> <?=$GLOBALS['site_title'];?></a> :: <?=$tp[topictext];?></td>
</tr>
</table>

<table width="630" border="0" cellspacing="0" cellpadding="2" class="MojText">
 <tr>
  <td>
   <img src="topic/<?=$tp[topicimage];?>" alt="<?=$tp[topictext];?>" border="0" align="right" />
   <?

    $res = mysql_query("SELECT * FROM ".$GLOBALS['db_news']." WHERE category='".$_GET['id']."' ORDER BY datum DESC"); 
    while ($ar = mysql_fetch_array($res))
     { 
      ereg ("([0-9]{4})([0-9]{1,2})([0-9]{1,2})([0-9]{1,2})([0-9]{1,2})([0-9]{1,2})", $ar[datum], $datetime);
      $datum = date("M jS ", mktime($datetime[4],$datetime[5],$datetime[6],$datetime[2],$datetime[3],$datetime[1]));
      $x++;
      print "$x.<a href=\"?action=shownews&amp;category=".$_GET['category']."&amp;id=".$_GET['id']."&amp;topicid=$ar[id]\">$ar[headline] / $datum</a><br/>";
     }
   ?>
  </td>
 </tr>
</table>
<?
}

Function ShowNews()
{
 if(!check_version("4.1.0")) global $_GET;
 $res = mysql_query("SELECT * FROM ".$GLOBALS['db_topic']." WHERE id='".$_GET['id']."'"); 
 $tp = mysql_fetch_array($res)
?>

<br/>
<table width="630" border="0" cellspacing="0" cellpadding="2" bgcolor="#<?=_COLOR02;?>" align="center" class="MojHead">
 <tr> 
 <td width="100%" bgcolor="#<?=_COLOR02;?>">
  <a href="<?=$GLOBALS['PHP_SELF'];?>"><?=$GLOBALS['site_title'];?></a> :: <a href="?action=category&amp;id=<?=$_GET['id'];?>"><?=$tp[topictext];?></a>
 </td>
</tr>
</table>
<br/>
<table width="630" border="0" cellspacing="0" cellpadding="2" align="center" class="MojHead">
 <tr> 
  <td>
   <div align="right"><img src="topic/<?=$tp[topicimage];?>" alt="<?=$tp[topictext];?>" border="0"/></div>
   <?
  $res = mysql_query("SELECT * FROM ".$GLOBALS['db_news']." where id='".$_GET['topicid']."'"); 
  while ($ar = mysql_fetch_array($res))
  { 
   $res2 = mysql_query("SELECT email FROM ".$GLOBALS['db_admin']." WHERE uname='$ar[author]'");
   $ar2 =  mysql_fetch_array($res2);
   $res3 = mysql_query("SELECT topicimage FROM ".$GLOBALS['db_topic']." where id='$ar[category]'"); 
   $ar3 = mysql_fetch_array($res3);

   $datum = formatTimestamp($ar[datum]);
 echo "</td></tr></table>";
   ?>
   <table width="630" cellspacing="1" cellpadding="1" class="MojText">
    <tr><td class="MojHead"><?=$ar[headline];?></td></tr>
    <tr><td><?=$datum;?> / <a href="mailto:<?=$ar2[email];?>"><?=$ar[author];?></a></td></tr>
    <tr>
     <td>
      <p style="width:630; text-align:justify;" class="MojText">
       <?
        if(!empty($ar[picture]) && file_exists($GLOBALS['news_url'].$ar[picture]))
          echo '<img src="'.$GLOBALS['news_url'].$ar[picture].'" align="left" style="padding-right:4pt;">';
        echo $ar[preview];
       ?>
      </p>
      <p style="width:630; text-align:justify;" class="MojText"><?=$ar[tekst];?></p>
     </td>
    </tr>
   </table>
   <br/><br/>
  <?
 }
 
}
?>