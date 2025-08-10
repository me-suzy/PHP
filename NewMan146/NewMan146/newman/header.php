<?php
  //******************************************************************************************
  //**                                                                                      **
  //** phpNewsManager v1.30                                                                 **
  //** contact: gregor@klevze.si                                                            **
  //** Last edited: 27th.May,2002                                                           **
  //******************************************************************************************
?>
<!DOCTYPE html PUBLIC
"-//W3C//DTD XHTML 1.0 Transitional//EN" 
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
 <head>
  <title><?echo $title;?></title>
  <link rel="stylesheet" type="text/css" href="MojStil.css" />
  <meta http-equiv="Content-Type" content="text/html; charset=<?=_CHARSET;?>" />
  <?
   if($makejs == "category") {MakeJS($topic_path,$topic_url);}
   if($makejs == "partners") {MakeJS($partners_path,$partners_url);}
   if($makejs == "smileys")  {MakeJS($smileys_path,$smiley_url);}
   if($makejs == "weather")  {MakeJS($weather_path,$weather_url);}
  ?>

 <script type="text/javascript" src="./css/newman.js"></script>

 <script type="text/javascript">

function MakePreview()
{
 if(document.novica.makreprev.checked)
 {
  if(document.novica.newwindow.checked)
  {
   if(winnew.closed || winnew.opener == null)
      PreviewWindow();
  }
  else
   winnew = self;
 
  var previewTxt = new String();
  previewTxt = document.novica.preview.value;
  previewTxt = previewTxt.replace(/\[B\]/gi, '<b>');
  previewTxt = previewTxt.replace(/\[\/B\]/gi, '</b>');
  previewTxt = previewTxt.replace(/\[I\]/gi, '<i>');
  previewTxt = previewTxt.replace(/\[\/I\]/gi, '</i>');
  previewTxt = previewTxt.replace(/\[U\]/gi, '<u>');
  previewTxt = previewTxt.replace(/\[\/U\]/gi, '</u>');
  previewTxt = previewTxt.replace(/\n/gi, '<br/>');
  
  <?
/*   $res = mysql_query("SELECT * FROM ".$GLOBALS['db_smileys']." LIMIT 3") or die ("<b>LINE 123:</b>".mysql_error());
   while($ar = mysql_fetch_array($res))
   {
    $ar[code] = eregi_replace("\(","\\(",$ar[code]);
    $ar[code] = eregi_replace("\)","\\)",$ar[code]);
    //$ar[code] = eregi_replace("\\","\\\\",$ar[code]);
    $ar[code] = preg_replace("\\","\\\\",$ar[code]);
    echo "previewTxt = previewTxt.replace(/".$ar[code]."/gi, '<img src=\"".$GLOBALS['smiley_url'].$ar[smile]."\" alt=\"\"/>');\n";
   }
*/  ?>
  var messageTxt = new String(); 
  messageTxt = document.novica.message.value;
  messageTxt = messageTxt.replace(/\[B\]/gi, '<b>');
  messageTxt = messageTxt.replace(/\[\/B\]/gi, '</b>');
  messageTxt = messageTxt.replace(/\[I\]/gi, '<i>');
  messageTxt = messageTxt.replace(/\[\/I\]/gi, '</i>');
  messageTxt = messageTxt.replace(/\[U\]/gi, '<u>');
  messageTxt = messageTxt.replace(/\[\/U\]/gi, '</u >');
  messageTxt = messageTxt.replace(/\n/gi, '<br/>');

  <?
/*   $res = mysql_query("SELECT * FROM ".$GLOBALS['db_smileys']." LIMIT 1") or die ("<b>LINE 123:</b>".mysql_error());
   while($ar = mysql_fetch_array($res))
   {
    $ar[code] = eregi_replace("[","\[",$ar[code]);
    $ar[code] = eregi_replace("]","\]",$ar[code]);
    $ar[code] = eregi_replace("'","\'",$ar[code]);
    $ar[code] = eregi_replace("(","\(",$ar[code]);
    echo "messageTxt = messageTxt.replace(/".$ar[code]."/gi, '<img src=\"".$GLOBALS['smiley_url'].$ar[smile]."\" alt=\"\"/>');\n";
   }
*/  ?>
 
  winnew.tiph.innerHTML = '<font size="4" face="arial"><b>' + document.novica.headline.value  + '</b></font><br/><br/>';
  winnew.tipp.innerHTML = previewTxt;
  //winnew.tip.innerHTML += '<br/><br/>';
  winnew.tipm.innerHTML = messageTxt;
 }
}

</script>

 </head>
 <body text="#<?=_COLOR_TEXT;?>" bgcolor="#<?=_COLOR_BACK;?>" link="#<?=_COLOR_LINK;?>" alink="#<?=_COLOR_ALINK;?>" vlink="#<?=_COLOR_VLINK;?>" style="background-image:url('./gfx/background.gif')">
<table width="796" cellspacing="0" cellpadding="0" border="0" class="MojText">
 <tr bgcolor="#9ec5e4">
  <td>
   <a href="index.php"><img src="<?=$logo;?>" border="0" width="280" alt="" /></a><br />
  </td>
  <td valign="bottom" align="right">
   <font size="4" color="#8eb5d4" face="tahoma"><?echo _WEBCONTROLPANEL;?></font> 
  </td>
 </tr>
 <tr><td colspan="2" height="1" bgcolor="#000000"></td></tr>
</table>

<table width="795" cellspacing="0" cellpadding="1" border="0"  style="background-image:url('./gfx/tile.gif')" class="MojText">
 <tr>
  <td width="160" align="center" valign="top">

   <table width="141" align="center" cellspacing="0" cellpadding="0" class="MojText">
    <tr>
     <td  style="background-image:url('./gfx/menu1.jpg')" width="141" height="34">
      <span style="padding-left:5px; font-family:arial; font-size:12px; font-weight:bold;"><?=_USERINFO;?></span>
     </td>
    </tr>
    <tr>
     <td class="BoxText"  style="background-image:url('./gfx/menu2.jpg')" align="left">
      <?
      $psw = CheckLogin();
      $info = base64_decode("$nm_user");
      $info = explode(":", $info);
      if ($action=="Login") { $info[0]=$login;}      
      $login = $info[0];

      if ($psw == 0) { ShowLogin();}
      else
       {
        $res = mysql_query("SELECT * from $db_admin where uname='$login'") or die ("<B>Error 87:</B>".mysql_query());;
        $ar = mysql_fetch_array($res);
        echo _USERNAME.": <b>".$ar[uname]."</b><br />";
	?>
	<a href="index.php?action=Logout"><img src="./gfx/logout.jpg" border="0" alt="Logout" /></a><?
       }
      ?>
     </td>
    </tr>
    <tr>
     <td>
      <img src="gfx/menu3.jpg" alt="" /><br />
     </td>
    </tr>
   </table>
   <br />
   <?if ($psw <> 0) { 
   ?>
   <table width="141" align="center" cellspacing="0" cellpadding="0" class="MojText">
    <tr>
     <td  style="background-image:url('./gfx/menu1.jpg')" width="141" height="34">
      <span style="padding-left:5px; font-family:arial; font-size:12px; font-weight:bold;"><?echo _CONTENT;?></span>
     </td>
    </tr>
    <tr>
     <td class="BoxText"  style="background-image:url('./gfx/menu2.jpg')">
      <table width="98%" cellspacing="0" cellpadding="1" class="MojText">
       <tr><td width="25"><a href="news.php"><img src="gfx/book.gif" width="20" border="0" alt="" /></a></td><td align="left"><a href="news.php"><?=_NEWS;?></a></td></tr>
       <tr><td width="25"><a href="stories.php"><img src="gfx/book.gif" width="20" border="0" alt="" /></a></td><td align="left"><a href="stories.php"><?=_STORY;?></a></td></tr>
       <tr><td width="25"><a href="weather.php"><img src="gfx/book.gif" width="20" border="0" alt="" /></a></td><td align="left"><a href="weather.php"><?=_WEATHER;?></a></td></tr>
       <tr><td width="25"><a href="category.php"><img src="gfx/folders.gif" width="20" border="0" alt="" /></a></td><td align="left"><a href="category.php"><?=_CATEGORY;?></a></td></tr>
       <tr><td width="25"><a href="partners.php"><img src="gfx/book.gif" width="20" border="0" alt="" /></a></td><td align="left"><a href="partners.php"><?=_PARTNERS;?></a></td></tr>
       <tr><td width="25"><a href="gallery.php"><img src="gfx/graph.gif" width="20" border="0" alt="" /></a></td><td align="left"><a href="gallery.php"><?=_GALLERY;?></a></td></tr>
       <tr><td width="25"><a href="pictures.php"><img src="gfx/share.gif" width="20" border="0" alt="" /></a></td><td align="left"><a href="pictures.php"><?=_PICTURES;?></a></td></tr>

       <tr><td width="25"><a href="poll.php"><img src="gfx/question.gif" width="20" border="0" alt="" /></a></td><td align="left"><a href="poll.php"><?=_WEEKLYPOLL;?></a></td></tr>
       <tr><td width="25"><a href="pnews.php"><img src="gfx/book.gif" width="20" border="0" alt="" /></a></td><td align="left"><a href="pnews.php"><?=_PUBLICNEWS;?></a></td></tr>
       <tr><td width="25"><a href="smileys.php"><img src="gfx/smiley.gif" width="20" border="0" alt="" /></a></td><td align="left"><a href="smileys.php"><?=_SMILEYS;?></a></td></tr>
       <tr><td width="25"><a href="index.php"><img src="gfx/graph.gif" width="20" border="0" alt="" /></a></td><td align="left"><a href="index.php"><?=_STATISTICS;?></a></td></tr>
       <tr><td width="25"><a href="browse.php"><img src="gfx/browse.gif" width="20" border="0" alt="" /></a></td><td align="left"><a href="browse.php"><?=_BROWSENEWS;?></a></td></tr>
      </table>
     </td>
    </tr>
    <tr>
     <td><img src="gfx/menu3.jpg" alt="" /><br /></td>
    </tr>
   </table>
   <br />
   <table width="141" align="center" cellspacing="0" cellpadding="0" class="MojText">
    <tr>
     <td  style="background-image:url('./gfx/menu1.jpg')" width="141" height="34">
      <span style="padding-left:5px; font-family:arial; font-size:12px; font-weight:bold;"><?=_ADMINISTRATION;?></span>
     </td>
    </tr>
    <tr>
     <td class="BoxText" style="background-image:url('./gfx/menu2.jpg')">
      <table width="98%" cellspacing="0" cellpadding="1" class="MojText">
       <tr><td width="25"><a href="groups.php"><img src="gfx/keys.gif" width="20" border="0" alt="Modify News" /></a></td><td align="left"><a href="groups.php"><?=_GROUPS;?></a></td></tr>
       <tr><td width="25"><a href="admin.php"><img src="gfx/admins.gif" width="20" border="0" alt="Modify admins" /></a></td><td align="left"><a href="admin.php"><?=_ADMINS;?></a></td></tr>
       <tr><td width="25"><a href="user.php"><img src="gfx/users.gif" width="20" border="0" alt="Modify categories" /></a></td><td align="left"><a href="user.php"><?=_USERS;?></a></td></tr>
       <tr><td width="25"><a href="rssMan.php"><img src="gfx/settings.gif" width="20" border="0" alt="Modify categories" /></a></td><td align="left"><a href="rssMan.php"><?=_RSSSETTINGS;?></a></td></tr>
       <tr><td width="25" align="left"><a href="optimize.php"><img src="gfx/optimize.gif" width="20" border="0" alt="Modify categories" /></a></td><td align="left"><a href="optimize.php"><?=_OPTIMIZEDATABASE;?></a></td></tr>
      </table>
     </td>
    </tr>
    <tr>
     <td><img src="gfx/menu3.jpg" alt="" /><br /></td>
    </tr>
   </table>
   <br />
   <table width="141" align="center" cellspacing="0" cellpadding="0" class="MojText">
    <tr>
     <td style="background-image:url('./gfx/menu1.jpg')" width="141" height="34">
      <span style="padding-left:5px; font-family:arial; font-size:12px; font-weight:bold;"><?=_LANGUAGE;?></span>
     </td>
    </tr>
    <tr>
     <td class="BoxText" style="background-image:url('./gfx/menu2.jpg')" align="left">
      <form action="changelang.php" method="post">
       <select name="language" class="news">
        <?
        if ($handle = opendir('./languages')) 
        {
         while (false !== ($file = readdir($handle))) 
         { 
          if ($file != "." && $file != "..") 
          {         
           echo '<option value="'.$file.'"';;
           if ($clang == $file) echo ' selected="selected" ';
           $dfile = substr($file,5);
           $dfile = ereg_replace(".php","",$dfile);
           echo ">$dfile</option>\n";
          }
         }
         closedir($handle); 
         clearstatcache();
        }
        ?>
       </select>
       <input type="hidden" name="changelang" value="true" /><input type="submit" class="news" style="width:40px;" value="<?echo _SUBMIT;?>" />
      </form>
     </td>
    </tr>
    <tr>
     <td><img src="gfx/menu3.jpg" alt="" /><br /></td>
    </tr>
   </table>
   <?
   }?>
  </td>
  <td width="635" valign="top" bgcolor="#<?=_COLOR06;?>" height="550">