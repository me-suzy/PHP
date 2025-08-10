<?php
  //******************************************************************************************
  //** phpNewsManager                                                                       **
  //** contact: gregor@klevze.si                                                            **
  //** Last edited: 20th.March,2003                                                         **
  //******************************************************************************************

 ini_set('register_globals', 0);

 // YOU ONLY NEED THIS IF YOU'RE RUNNING 4.06 OR EARLIER VERSION OF PHP !!!
 if(!check_version("4.1.0"))
 {
  $_COOKIE = &$GLOBALS['HTTP_COOKIE_VARS'];
  $_SERVER = &$GLOBALS['HTTP_SERVER_VARS'];
  $_SESSION = &$GLOBALS['HTTP_SESSION_VARS'];
  $_GET = &$GLOBALS['HTTP_GET_VARS'];
  $_POST = &$GLOBALS['HTTP_POST_VARS'];
 }
 $newman_ver = "v1.45";
 $site_title = "phpNewsManager $newman_ver";
 require_once("colors.php");
  
 include ("db.inc.php");  
 if(empty($clang)) {$clang = "lang-english.php";}
 include ("languages/$clang");
 mysql_connect("$db_server","$db_uname","$db_pass");
 mysql_select_db($db_name); 

function check_version($vercheck) 
{
 $minver = explode(".", $vercheck);
 $curver = explode(".", phpversion());
 if(($curver[0] < $minver[0]) || (($curver[0] = $minver[0]) && ($curver[1] < $minver[1])) || (($curver[0] = $minver[0]) && ($curver[1] = $minver[1]) && ($curver[2][0] < $minver[2][0])))
   return false;
 else
   return true;
}

function pic($string,$pic=1)
{
 while(preg_match("/\[PIC=(\d+)\:(.+)\]/isU", $string, $data))
 {
  list($null,$id,$align) = $data;
  $res = mysql_query("SELECT * from ".$GLOBALS['db_news_pics']." WHERE id=".$id) or die("LINE 71:".mysql_error());
  $ar = mysql_fetch_array($res);
  $size = getimagesize($GLOBALS['news_path']."/".$ar[picture]);
  if($pic == 0) $pix="";
  else $pix = '<table align="'.$align.'" border="0" cellpadding="1" cellspacing="1" width="'.$size[0].'" class="newsPic"><tr><td><img src="'.$GLOBALS['news_url'].$ar[picture].'" title="'.$ar[name].'" style="padding:4px;"><br/><span style="width:'.$size[0].';text-align:center;" class="txtSub">'.$ar[description].'</span></td></tr></table>';
  $string = preg_replace("/(\[PIC=".$id.":".$align."\])/isU", $pix, $string);
 }
 return $string;
}

function CheckLogin()
{
 $info = base64_decode($GLOBALS['nm_user']);
 $info = explode(":", $info);
 if ($GLOBALS['action'] == "Login") 
 {
  $info[0] = $GLOBALS['login']; 
  $info[1] = $GLOBALS['pass']; 
 }
 $result = mysql_query("select * from ".$GLOBALS['db_admin']." where uname='".$info[0]."' and passwd='".$info[1]."'") or die ("<b>Error 36:</b>".mysql_error());
 $num = mysql_num_rows($result);
 if($num == 1) $passek=1; else $passek=0;
 return $passek;
}  



function unhtmlentities ($string)
{
 $trans_tbl = get_html_translation_table (HTML_ENTITIES);
 $trans_tbl = array_flip ($trans_tbl);
 return strtr ($string, $trans_tbl);
}

function ShowLogin()
{ 
 ?>
  <form action="login.php" method="post">
  <?=_USERNAME;?><br />
  <input type="text" name="login" class="text"/><br />
  <?=_PASSWORD;?><br />
  <input type="password" name="pass" class="text" /><br />
  <input type="hidden" name="action"  value="login" />
  <div align="center"><input type="submit" value="<?=_LOGIN;?>" /></div>
  </form> 
  <!--For demo use this:<br />
  // FOR ONLINE DEMO
  USERNAME: demo<br />
  PASSWORD: demo<br />
  <br />
  Front end demo available at:<br />
  <a href="http://skintech.skinbase.org/newman/demo/myPage/" target="_new">Layout 1</A><br />
  <a href="http://skintech.skinbase.org/newman/demo/myPage2/" target="_new">Layout 2</A><br />
  -->
 <?
}

function Logout()
{
 echo '<p align="center" class="MojText"><?=_YOULOGOUT;?></p>';
}

function formatTimestamp($time)
 {
  global $datetime;
  ereg ("([0-9]{4})([0-9]{1,2})([0-9]{1,2})([0-9]{1,2})([0-9]{1,2})([0-9]{1,2})", $time, $datetime);
  $datetime = date("d M Y", mktime($datetime[4],$datetime[5],$datetime[6],$datetime[2],$datetime[3],$datetime[1]));
  return($datetime);
}

function CheckPriv($myTable)
{
 $chu = mysql_query("SELECT priv FROM ".$GLOBALS['db_admin']." WHERE uname='".$GLOBALS['login']."'") or die("<b>LINE 98:</b>:".mysql_error());
 $usr = mysql_fetch_array($chu);
 $chk = mysql_query("SELECT ".$myTable." FROM ".$GLOBALS['db_groups']." WHERE id='$usr[priv]'") or die("<b>LINE 100:</b>:".mysql_error());
 $prv = mysql_fetch_array($chk);
 if(mysql_num_rows($chk) == 0) 
   return 0;
 return $prv[$myTable];
}

function is_email_valid($email) 
{ 
 if(eregi("^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,4}$", $email)) 
   return TRUE; 
 else 
   return FALSE; 
}

function makeRSS()
{
 $resn = mysql_query("SELECT * FROM ".$GLOBALS['db_rss']);
 $arn = mysql_fetch_array($resn);
 if($arn[auto]==1)
 {
  $query = "SELECT * from ".$GLOBALS['db_news']." order by datum desc, id desc LIMIT $arn[number]";

  if(!$fps = fopen($GLOBALS['rss_path']."/$arn[filename]","w") && file_exists($GLOBALS['rss_path']."/$arn[filename]"))
  {
   flock($fps,LOCK_EX);
   fwrite($fps,"<?xml version=\"1.0\"?>\n");
   fwrite($fps,"<rdf:RDF xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\" xmlns=\"http://purl.org/rss/1.0/\">\n");
   fwrite($fps,"<channel rdf:about=\"http://www.skinbase.org/news.rss\">\n");
   fwrite($fps,"<title>$arn[title]</title>\n");
   fwrite($fps,"<link>$arn[link]</link>\n");
   fwrite($fps,"<description>$arn[description]</description>\n");
   fwrite($fps,"<items>\n");
   fwrite($fps," <rdf:Seq>\n");
   $res = mysql_query($query) or die("<b>Error:</B>".mysql_error());
   while($ar = mysql_fetch_array($res))
     fwrite($fps,"<rdf:li resource=\"$arn[link]/news.php?id=$ar[id]\" />\n");
   fwrite($fps,"</rdf:Seq>\n");
   fwrite($fps,"</items>\n");
   fwrite($fps,"</channel>\n");
   $res = mysql_query($query) or die("<b>Error:</B>".mysql_error());
   while ($ar = mysql_fetch_array($res))
   {
    fwrite($fps,"<item rdf:about=\"http://www.skinbase.org/news.php?id=$ar[id]\">\n");
    fwrite($fps,"<title>$ar[headline]</title>\n");
    fwrite($fps,"<link>http://www.skinbase.org/news.php?id=$ar[id]</link>\n");
    fwrite($fps,"<description>$ar[preview]</description>\n");
    fwrite($fps,"</item>\n");
   }
   fwrite($fps,"</rdf:RDF>\n");
   //flock($fps,4);
   fclose($fps);
  }
 }
}

function MakeJS($myPath,$myURL)
{
 if(file_exists($myPath))
 {
  ?>
  <script type="text/javascript">
  <!-- Beginning of JavaScript -
  <?
  $d = dir($myPath);
  $x=0;$y=0;
  while($entry=$d->read()) 
  {
   $x++;
   if ($x > 2)
   {
    $y++;
    echo "Image".$y." = new Image(100,100)\n";
    echo "Image".$y.".src = \"$myURL/$entry\"\n";
   }
  }
  $d->close();
  ?>
  function Swap()
  {
   x = forma.picture.selectedIndex;
   <?
   $y++;
   for($z=0;$z<$y;$z++)
   {
    $f = $z+1;
    echo "if (x == ".$z.") {document.button.src = Image".$f.".src; return true;}\n";
   }
   ?>
  }
  // - End of JavaScript - -->
  </script>
 <?
 }
}

function UploadPicture($path,$name,$priv)
{
 global $HTTP_POST_FILES;

 // CHECK PRIVILEGIES
 if(CheckPriv($priv) <> 1) 
  { 
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }
 ?>
 <table width="630" cellspacing="2" cellpadding="0" class="MojText">
  <tr>
   <td width="20"><a href="<?=$GLOBALS['PHP_SELF'];?>"><img src="gfx/upload_picture_big.jpg" width="32" height="32" border="0" alt=""/></a></td>
   <td width="100"><a href="<?=$GLOBALS['PHP_SELF'];?>"><?=_MAINMENU;?></a></td>
   <td align="center" width="510"><font size="4" face="Arial"> <b><?=$name;?></b></font></td>
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
   <?if ($GLOBALS['check'] <> "ul_picture")
    {?>
     <form enctype="multipart/form-data" method="post" action="<?=$GLOBALS['PHP_SELF'];?>">
      <?=_IMAGE;?>:<br />
      <input type="file"   class="news" name="filename" size="50"/><br />
      <input type="hidden" name="action" value="upload"/>
      <input type="hidden" name="check"  value="ul_picture"/><br/>
      <input type="submit" value="<?=_SUBMIT;?>" style="width:114px;height:35px;background-image: url(./gfx/button.gif);"/>
     </form>
  <?}
 if ($GLOBALS['check'] == "ul_picture")
 {
  $HTTP_POST_FILES['filename']['name'] = eregi_replace(" ","",$HTTP_POST_FILES['filename']['name']);
  $HTTP_POST_FILES['filename']['name'] = eregi_replace("/","",$HTTP_POST_FILES['filename']['name']);
  $HTTP_POST_FILES['filename']['name'] = eregi_replace("@","",$HTTP_POST_FILES['filename']['name']);
  $HTTP_POST_FILES['filename']['name'] = eregi_replace("%","",$HTTP_POST_FILES['filename']['name']);
  $HTTP_POST_FILES['filename']['name'] = eregi_replace("\"","",$HTTP_POST_FILES['filename']['name']);
  $HTTP_POST_FILES['filename']['name'] = eregi_replace("'","",$HTTP_POST_FILES['filename']['name']);

  if($HTTP_POST_FILES['filename']['type'] == "image/jpeg" | $HTTP_POST_FILES['filename']['type'] == "image/pjpeg" | $HTTP_POST_FILES['filename']['type'] == "image/gif" | $HTTP_POST_FILES['filename']['type'] == "image/png") {
   if(file_exists($path."/".$HTTP_POST_FILES['filename']['name'])) 
    echo _FILEALREADYEXIST."<br />";
   else
   if(!move_uploaded_file($HTTP_POST_FILES['filename']['tmp_name'],$path."/".$HTTP_POST_FILES['filename']['name']))
     echo _ERROR."<br />";
   else 
   {
    chmod($path."/".$HTTP_POST_FILES['filename']['name'],0644);
    echo _SUCCESS."<br />";
   }
  }
  else 
   echo _WRONGDATATYPE.": ".$HTTP_POST_FILES['filename']['type']."<br />";
 } 
 ?>
  </td>
 </tr>
</table>
<?
}

function ShowPages($xnum)
{
 global $sort,$order,$id,$page,$hits,$show;
 
 if ($page == 0)   {$page=1;}
 if (empty($page)) {$page=1;}
 if (empty($hits)) {$hits=20;}
 if (empty($show)) {$show = 7;}

 $start = $hits*($page-1);
 $stw2 = ($xnum/$hits);
 $stw2 = (int) $stw2;
 if ($xnum%$hits > 0) {$stw2++;}
 
 $np = $page+1;
 $pp = $page-1;
 if ($page == 1) { $pp=1; }

 $l1 = $page - $show;
 $d1 = $page + $show;

 $n1 = $page - $show;
 if($n1<1) {$n1=1;}
 $n2 = $page + $show;
 if($n2>$stw2) {$n2=$stw2;}

 $d1 = 2 * $show + $l1;
 if($d1>$stw2) {$d1=$stw2;$l1=$stw2-$show*2;}
 if($l1<1) {$l1 = 1;}

 if ($np>$stw2) { $np=$stw2; }

 echo "<a href=\"".$GLOBALS['PHP_SELF']."?page=$pp&amp;sort=$sort&amp;id=$id\">&#171;</a> ";
 echo "<a href=\"".$GLOBALS['PHP_SELF']."?page=$n1&amp;sort=$sort&amp;id=$id\">&lt;</a> ";
 for($i=$l1;$i<=$d1;$i++)
 {
  if($page==$i) {echo "<b>$i</b>";} 
  else{echo " <a href=\"".$GLOBALS['PHP_SELF']."?page=$i&amp;sort=$sort&amp;id=$id\">$i</a> ";}
 }
 echo " <a href=\"".$GLOBALS['PHP_SELF']."?page=$np&amp;sort=$sort&amp;id=$id\">&gt;</a> ";
 echo " <a href=\"".$GLOBALS['PHP_SELF']."?page=$n2&amp;sort=$sort&amp;id=$id\">&#187;</a>";
 return array($start,$hits);
}

function ConvertHTML($txt)
{
 $txt = eregi_replace( "\n", "<br />", $txt );
 $txt = eregi_replace( "\[b\]", "<b>", $txt );
 $txt = eregi_replace( "\[i\]", "<i>", $txt );
 $txt = eregi_replace( "\[u\]", "<u>", $txt );
 $txt = eregi_replace( "\[/b\]", "</b>", $txt );
 $txt = eregi_replace( "\[/i\]", "</i>", $txt );
 $txt = eregi_replace( "\[/u\]", "</u>", $txt );

 return $txt;
}

function UnConvertHTML($txt)
{
 /*
 $txt = preg_replace( "#<I>(.+?)</I>#"  , "[I]\\1[/I]"  , $txt );
 $txt = preg_replace( "#<B>(.+?)</B>#"  , "[B]\\1[/B]"  , $txt );
 $txt = preg_replace( "#<U>(.+?)</U>#"  , "[U]\\1[/U]"  , $txt );
 */
 $txt = eregi_replace( "<br />", "\n", $txt );
 $txt = eregi_replace( "<br>", "\n", $txt );
 $txt = eregi_replace( "<i>"  , "[i]"  , $txt );
 $txt = eregi_replace( "<u>"  , "[u]"  , $txt );
 $txt = eregi_replace( "<b>"  , "[b]"  , $txt );
 $txt = eregi_replace( "</i>"  , "[/i]"  , $txt );
 $txt = eregi_replace( "</u>"  , "[/u]"  , $txt );
 $txt = eregi_replace( "</b>"  , "[/b]"  , $txt );

 $txt = eregi_replace( "<br />", "\n", $txt );

 return $txt;
}

function MultiDelete($table,$id,$priv)
{
 if(!check_version("4.1.0")) global $_POST; // only need if you're running 4.06 or lower version of PHP

 if(CheckPriv($priv) <> 1) 
 {
  ShowMain();
  echo "<script type=\"text/javascript\">alert('"._NOTENOUGHPRIV."');</script>";
  return;
 }

 if(is_array($_POST['list']))
   while(list($key, $value) = each ($_POST['list'])) 
     mysql_query("DELETE FROM ".$table." WHERE ".$id."='".$value."'");

 ShowMain();
 return;
}
?>
