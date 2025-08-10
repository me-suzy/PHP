<html>
 <head>
  <title>phpNewsManager Installation Script</title> 
  <link rel="stylesheet" type="text/css" href="MojStil.css" />
  <meta http-equiv="Content-Type" content="text/html; charset=<?=_CHARSET;?>" />
 </head>
<body>

<table width="796" cellspacing="0" cellpadding="0" border="0" class="MojText">
 <tr bgcolor="#9ec5e4">
  <td>
   <a href="index.php"><img src="./gfx/logos.jpg" border="0" width="280" alt="" /></a><br />
  </td>
  <td valign="bottom" align="right">
   <font size="4" color="#8eb5d4" face="tahoma">INSTALLATION</font> 
  </td>
 </tr>
 <tr><td colspan="2" height="1" bgcolor="#000000"></td></tr>
</table>

<?

if(empty($action))
{
?>
 <br/>
 <table class="MojText" width="630" align="center">
  <tr>
   <td>
    Please fill out this form with required mySQL data.
    <form action="<?= $PHP_SELF;?>" method="post">
     Server:<br/>
     <input type="text" name="db_server" value="localhost" size="40"/><br/>
     Username:<br/>
     <input type="text" name="db_uname" value="" size="40"/><br/>
     Password:<br/>
     <input type="password" name="db_pass" value="" size="40"/><br/>
     Database Name:<br/>
     <input type="text" name="db_name" value="newman" size="40"/><br/>
   <input type="hidden" name="action" value="step2" size="40"/><br/>
    <input type="submit" style="width:114px;height:35px;background-image: url(./gfx/button.gif);"/>
    </form>
     FILE <b>db.inc.php</b> MUST HAVE PERMISSION SET TO MODE 666 !!!<br/>
   </td>
  </tr>
 </table>
<?
}
?>



<?
if($action == "step2")
{
 $link = mysql_connect($db_server, $db_uname, $db_pass) or die("Could not connect");
 if(!mysql_select_db($db_name)) 
 {
  mysql_create_db("$db_name");
  mysql_select_db($db_name);
 }
?>
<br/>
<table class="MojText" border="0" width="700" >
 <tr>
  <td width="50"></td>
  <td width="200">
   <form action="<?=$PHP_SELF;?>" method="post">
   <b>Table names</b><br/><br/>
   Administrators:<br/>
   <input type="text" name="db_admin" value="admin" size="30"/><br/>
   Groups:<br/>
   <input type="text" name="db_groups" value="groups" size="30"/><br/>
   News:<br/>
   <input type="text" name="db_news" value="news" size="30"/><br/>
   News Pictures:<br/>
   <input type="text" name="db_news_pics" value="news_pics" size="30"/><br/>
   Story:<br/>
   <input type="text" name="db_story" value="story" size="30"/><br/>
   Comments for news:<br/>
   <input type="text" name="db_news_comments" value="news_comments" size="30"/><br/>
   Log for news:<br/>
   <input type="text" name="db_news_logged" value="news_logged" size="30"/><br/>
   Partners,Links:<br/>
   <input type="text" name="db_partners" value="partners" size="30"/><br/>
   Public News:<br/>
   <input type="text" name="db_pnews" value="pnews" size="30"/><br/>
   Smileys:<br/>
   <input type="text" name="db_smileys" value="smileys" size="30"/><br/>
   Topic:<br/>
   <input type="text" name="db_topic" value="topic" size="30"/><br/>
   Users:<br/>
   <input type="text" name="db_users" value="users" size="30"/><br/>
   Weekly Poll - Questions:<br/>
   <input type="text" name="db_weekQ" value="weekQ" size="30"/><br/>
   Weekly Poll - Answers:<br/>
   <input type="text" name="db_weekA" value="WeekA" size="30"/><br/>
   RSS:<br/>
   <input type="text" name="db_rss" value="rss" size="30"/><br/>
   Gallery:<br/>
   <input type="text" name="db_gallery_groups" value="gallery" size="30"/><br/>
   Gallery pictures:<br/>
   <input type="text" name="db_gallery" value="pictures" size="30"/><br/>
   Weather:<br/>
   <input type="text" name="db_weather" value="weather" size="30"/><br/>

   <br/><br/>
  </td>
  <td width="200" valign="top">
   <b>Paths & Folders</b><br/><br/>
   <?
    $cwd = getcwd();
    $smiley_path  =  "$cwd/gfx/smileys";
    $partner_path =  "$cwd/gfx/partners";
    $rss_path     =  "$cwd/rss";
    $topic_path   =  "$cwd/topic";
    $news_path    =  "$cwd/gfx/news";
    $gallery_path =  "$cwd/gfx/gallery";
    $weather_path =  "$cwd/gfx/weather";

   ?>
   News Pictures Path:<br/>
   <input type="text" name="news_path" value="<?=$news_path;?>" size="30"><br/>
   News Pictures URL:<br/>
   <input type="text" name="news_url" value="./gfx/news/" size="30"><br/>
   Smileys Path:<br/>
   <input type="text" name="smileys_path" value="<?=$smiley_path;?>" size="30"><br/>
   Smileys URL:<br/>
   <input type="text" name="smileys_url" value="./gfx/smileys/" size="30"><br/>
   Partners Path:<br/>
   <input type="text" name="partners_path" value="<?=$partner_path;?>" size="30"><br/>
   Partners URL:<br/>
   <input type="text" name="partners_url" value="./gfx/partners/" size="30"><br/>
   Topic Path:<br/>
   <input type="text" name="topic_path" value="<?=$topic_path;?>" size="30"><br/>
   Topic URL:<br/>
   <input type="text" name="topic_url" value="./topic" size="30"><br/>
   RSS Path:<br/>
   <input type="text" name="rss_path" value="<?=$rss_path;?>" size="30"><br/>
   Gallery Path:<br/>
   <input type="text" name="gallery_path" value="<?=$gallery_path;?>" size="30"><br/>
   Gallery URL:<br/>
   <input type="text" name="gallery_url" value="./gfx/gallery/" size="30"><br/>

   Weather Path:<br/>
   <input type="text" name="weather_path" value="<?=$weather_path;?>" size="30"><br/>
   Weather URL:<br/>
   <input type="text" name="weather_url" value="./gfx/weather/" size="30"><br/>

   <?
    if(!file_exists($smiley_path))  echo "<font color=\"red\"><b>Smiley path doesn't exists!</b></font><br/>";
    if(!file_exists($partner_path)) echo "<font color=\"red\"><b>Partner path doesn't exists!</b></font><br/>";
    if(!file_exists($rss_path))     echo "<font color=\"red\"><b>RSS path doesn't exists!</b></font><br/>";
    if(!file_exists($topic_path))   echo "<font color=\"red\"><b>Topic path doesn't exists!</b></font><br/>";
    if(!file_exists($news_path))    echo "<font color=\"red\"><b>News path doesn't exists!</b></font><br/>";
    if(!file_exists($gallery_path)) echo "<font color=\"red\"><b>Gallery path doesn't exists!</b></font><br/>";
    if(!file_exists($weather_path)) echo "<font color=\"red\"><b>Weather path doesn't exists!</b></font><br/>";
   ?>
  </td>
  <td width="200" valign="top">
   <b>Personal Data</b><br/><br/>
   Admin username:<br/>
   <input type="text" name="admin_uname" size="30"><br/>
   Admin password:<br/>
   <input type="text" name="admin_pass" size="30"><br/>
   Admin Real Name:<br/>
   <input type="text" name="admin_name" size="30"><br/>
   Admin Email:<br/>
   <input type="text" name="admin_email" size="30"><br/>
   <br/>
   <input type="hidden" name="action" value="step3">
   <input type="hidden" name="db_server" value="<?=$db_server;?>">
   <input type="hidden" name="db_uname" value="<?=$db_uname;?>">
   <input type="hidden" name="db_pass" value="<?=$db_pass;?>" >
   <input type="hidden" name="db_name" value="<?=$db_name;?>">
   <input type="submit" style="width:114px;height:35px;background-image: url(./gfx/button.gif);"/>
   </form>
  </td>
 </tr>
</table>
<?
mysql_close($link);
}
?>
 <br/>
 <table class="MojText" width="630">
  <tr>
   <td width="50"></td>
   <td>
<?
if($action == "step3")
{
 $link = mysql_connect($db_server, $db_uname, $db_pass) or die("Could not connect");
 mysql_select_db($db_name);
 echo "Creating Tables:<br/>";

 echo "$db_admin ...";
 mysql_query ("CREATE TABLE $db_admin (id int(11) NOT NULL auto_increment,uname varchar(30) default NULL,passwd varchar(30) default NULL,name varchar(120) default NULL,email varchar(80) default NULL,priv int(11) default NULL,info tinytext NOT NULL,PRIMARY KEY  (id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_groups...";
 mysql_query ("CREATE TABLE $db_groups (id int(11) NOT NULL auto_increment,name tinytext NOT NULL,description text NOT NULL,news_add tinyint(4) NOT NULL default '0',news_edit tinyint(4) NOT NULL default '0',news_del tinyint(4) NOT NULL default '0',news_ul tinyint(4) NOT NULL default '0',news_mod tinyint(4) NOT NULL default '0',admin_add tinyint(4) NOT NULL default '0',admin_edit tinyint(4) NOT NULL default '0',admin_del tinyint(4) NOT NULL default '0',pnews_add tinyint(4) NOT NULL default '0',pnews_edit tinyint(4) NOT NULL default '0',pnews_del tinyint(4) NOT NULL default '0',pnews_submit tinyint(4) NOT NULL default '0',cat_add tinyint(4) NOT NULL default '0',cat_edit tinyint(4) NOT NULL default '0',cat_del tinyint(4) NOT NULL default '0',cat_ul tinyint(4) NOT NULL default '0',users_add tinyint(4) NOT NULL default '0',users_edit tinyint(4) NOT NULL default '0',users_del tinyint(4) NOT NULL default '0',partner_add tinyint(4) NOT NULL default '0',partner_edit tinyint(4) NOT NULL default '0',partner_del tinyint(4) NOT NULL default '0',partner_ul tinyint(4) NOT NULL default '0',wp_add tinyint(4) NOT NULL default '0',wp_edit tinyint(4) NOT NULL default '0',wp_del tinyint(4) NOT NULL default '0',group_add tinyint(4) NOT NULL default '0',group_edit tinyint(4) NOT NULL default '0',group_del tinyint(4) NOT NULL default '0',smiley_add tinyint(4) NOT NULL default '0',smiley_edit tinyint(4) NOT NULL default '0',smiley_del tinyint(4) NOT NULL default '0',smiley_ul tinyint(4) NOT NULL default '0',rss_edit tinyint(4) NOT NULL default '0',gallery_add tinyint(4) NOT NULL default '0',gallery_edit tinyint(4) NOT NULL default '0',gallery_del tinyint(4) NOT NULL default '0',gallery_ul tinyint(4) NOT NULL default '0',story_add tinyint(4) NOT NULL default '1',story_edit tinyint(4) NOT NULL default '1',story_del tinyint(4) NOT NULL default '1',story_mod tinyint(4) NOT NULL default '1',weather_add tinyint(4) NOT NULL default '0',weather_edit tinyint(4) NOT NULL default '0',weather_del tinyint(4) NOT NULL default '0',weather_ul tinyint(4) NOT NULL default '0',PRIMARY KEY  (id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_news_comments...";
 mysql_query ("CREATE TABLE $db_news_comments(id int(11) NOT NULL auto_increment,nid int(11) NOT NULL default '0',author varchar(30) NOT NULL default '',datum timestamp(14) NOT NULL,tekst text NOT NULL,UNIQUE KEY id (id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_news_logged...";
 mysql_query("CREATE TABLE $db_news_logged (id int(11) NOT NULL auto_increment,nid int(11) NOT NULL default '0',ip varchar(250) NOT NULL default '',PRIMARY KEY  (id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_partners...";
 mysql_query("CREATE TABLE $db_partners (id int(11) NOT NULL auto_increment,name tinytext,link tinytext,picture tinytext,description text,clicks int(11) default NULL,out int(11) default NULL,main int(1) default NULL,gfx int(1) default NULL,PRIMARY KEY  (id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_pnews...";
 mysql_query("CREATE TABLE $db_pnews (id int(11) NOT NULL auto_increment,headline varchar(80) default NULL,author varchar(80) default NULL,category varchar(80) default NULL,picture varchar(120) default NULL,datum timestamp(14) NOT NULL,preview text,tekst text,lang varchar(10) default NULL,PRIMARY KEY  (id)) TYPE=MyISAM;");
 
 echo mysql_error()."<br/>$db_story...";
 mysql_query("CREATE TABLE $db_story (id int(11) NOT NULL auto_increment,headline varchar(80) default NULL,author varchar(80) default NULL,category varchar(80) default NULL,picture varchar(120) default NULL,datum2 timestamp(14) NOT NULL,datum timestamp(14) NOT NULL,preview text,views int(11) default NULL,PRIMARY KEY  (id)) TYPE=MyISAM;");
 
 echo mysql_error()."<br/>$db_smileys...";
 mysql_query("CREATE TABLE $db_smileys (id int(11) NOT NULL auto_increment,code varchar(50) default NULL,smile varchar(100) default NULL,emotion varchar(75) default NULL,PRIMARY KEY  (id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_news...";
 mysql_query("CREATE TABLE $db_news (id int(11) NOT NULL auto_increment,headline varchar(80) default NULL,author varchar(80) default NULL,category varchar(80) default NULL,picture varchar(120) default NULL,datum2 timestamp(14) NOT NULL,datum timestamp(14) NOT NULL,preview text,tekst text,story int(11) NOT NULL default '0',views int(11) default NULL,type tinyint(4) NOT NULL default '1',lang tinytext NOT NULL,PRIMARY KEY  (id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_news_pics...";
 mysql_query("CREATE TABLE $db_news_pics (id int(11) NOT NULL auto_increment,author tinytext NOT NULL,name tinytext NOT NULL,picture tinytext NOT NULL,description tinytext NOT NULL,datum date NOT NULL default '0000-00-00',PRIMARY KEY  (id,id,id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_topic...";
 mysql_query("CREATE TABLE $db_topic (id int(11) NOT NULL auto_increment,topicimage varchar(80) default NULL,topictext varchar(255) default NULL,counter int(11) default NULL,PRIMARY KEY  (id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_users...";
 mysql_query("CREATE TABLE $db_users (id int(11) NOT NULL auto_increment,uname varchar(30) default NULL,passwd varchar(30) default NULL,email varchar(80) default NULL,info tinytext,name tinytext,PRIMARY KEY  (id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_weekA...";
 mysql_query("CREATE TABLE $db_weekA (id int(11) NOT NULL auto_increment,wid int(11) default NULL,answer int(11) default NULL,PRIMARY KEY  (id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_weekQ...";
 mysql_query("CREATE TABLE $db_weekQ (id int(11) NOT NULL auto_increment,question text,answers varchar(255) default NULL,author varchar(120) default NULL,PRIMARY KEY  (id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_rss...";
 mysql_query("CREATE TABLE $db_rss (filename tinytext NOT NULL,number int(11) NOT NULL default '10',title tinytext NOT NULL,link tinytext NOT NULL,description tinytext NOT NULL,auto tinyint(4) NOT NULL default '0') TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_gallery...";
 mysql_query("CREATE TABLE $db_gallery (id int(11) NOT NULL auto_increment,author tinytext NOT NULL,name tinytext NOT NULL,description tinytext NOT NULL,datum date NOT NULL default '0000-00-00',root tinyint(4) NOT NULL default '0',picture tinytext NOT NULL,PRIMARY KEY  (id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_gallery_groups...";
 mysql_query("CREATE TABLE $db_gallery_groups (id int(11) NOT NULL auto_increment,author tinytext NOT NULL,name tinytext NOT NULL,description tinytext NOT NULL,datum date NOT NULL default '0000-00-00',root tinyint(4) NOT NULL default '0',picture tinytext NOT NULL,PRIMARY KEY  (id)) TYPE=MyISAM;");

 echo mysql_error()."<br/>$db_weather...";
 mysql_query("CREATE TABLE $db_weather (id int(11) NOT NULL auto_increment,datum date NOT NULL default '0000-00-00',morning tinyint(4) NOT NULL default '0',daily tinyint(4) NOT NULL default '0',picture tinytext NOT NULL,preview tinytext NOT NULL,description text NOT NULL,PRIMARY KEY  (id)) TYPE=MyISAM;");

 echo "Filling Data into Tables:<br/>";
 mysql_query ("INSERT INTO rss VALUES ('news.rss', 10, 'Name', 'http://www.mydomain.com', 'My Description', 0);");
 echo ".";
 mysql_query ("INSERT INTO $db_admin VALUES (0, '$admin_uname', '$admin_pass', '$admin_name', '$admin_email', 1, '');");
 echo ".";
mysql_query("INSERT INTO $db_groups VALUES (0, 'Full Admin', 'All function can be accessed', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);");
 echo mysql_error().".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':x', 's026.gif', 'Sick');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':(', 's024.gif', 'Sad');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':-(', 's024.gif', 'Sad');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, '(o)', 's020.gif', 'Moon');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, '}:|', 's021.gif', 'Ninja');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':sad:', 's024.gif', 'Sad');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':brb', 's023.gif', 'Running');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':,', 's022.gif', 'OhWell');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, 'LOL', 's019.gif', 'LOL');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, 'lol', 's019.gif', 'LOL');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':moon', 's020.gif', 'Moon');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':|', 's017.gif', 'Impartial');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':-)', 's016.gif', 'Happy');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':smile:', 's016.gif', 'Happy');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':grin:', 's015.gif', 'Grin');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':)', 's016.gif', 'Happy');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':-D', 's015.gif', 'Grin');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':D', 's015.gif', 'Grin');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, '{:p', 's011.gif', 'Fool');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':evil', 's010.gif', 'Evil');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':grave', 's013.gif', 'gRAVE');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, '}:D', 's010.gif', 'Evil');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':O', 's009.gif', 'Embarassed');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':devil', 's007.gif', 'Devil');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':cry', 's006.gif', 'Cry');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, 'B)', 's005.gif', 'Cool');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, '8)', 's005.gif', 'Cool');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':9', 's003.gif', 'Cheesy');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, '}:(', 's002.gif', 'Angry');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':o', 's001.gif', 'Amazed');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, '8O', 's001.gif', 'Amazed');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':zzz', 's027.gif', 'Sleepy');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, '==~', 's028.gif', 'Smoker');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':smoke', 's028.gif', 'Smoker');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':p', 's029.gif', 'Tongue');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ':P', 's029.gif', 'Tongue');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, ';)', 's030.gif', 'Wink');");
 echo ".";
 mysql_query ("INSERT INTO $db_smileys VALUES (0, '}:?', 's031.gif', 'What');");
 echo ".";
 mysql_query ("INSERT INTO $db_topic VALUES (0, 'linux.gif', 'Linux', 0);");
 echo ".";
 mysql_query ("INSERT INTO $db_topic VALUES (0, 'mac.gif', 'Apple / Mac', 0);");
 echo ".";
 mysql_query ("INSERT INTO $db_topic VALUES (0, 'beos.gif', 'BeOS', 0);");
 echo ".";
 mysql_query ("INSERT INTO $db_topic VALUES (0, 'gnome.gif', 'GNOME', 0);");
 echo ".";
 mysql_query ("INSERT INTO $db_topic VALUES (0, 'gnu.jpg', 'GNU / GPL', 0);");
 echo ".";
 mysql_query ("INSERT INTO $db_topic VALUES (0, 'java.gif', 'Java', 0);");
 echo ".";
 mysql_query ("INSERT INTO $db_topic VALUES (0, 'perl.gif', 'Perl', 0);");
 echo ".";
 mysql_query ("INSERT INTO $db_topic VALUES (0, 'windows.jpg', 'Windows 95/98/NT', 0);");
 echo ".";
 mysql_query ("INSERT INTO $db_topic VALUES (0, 'other.jpg', 'Other', 0);");
 echo ".<br/><b/>Done.</b><br/>";

 $fp = fopen("db.inc.php","w");
 fwrite($fp,"<?\n");
 fwrite($fp,"\$db_server = \"$db_server\";\n");
 fwrite($fp,"\$db_uname  = \"$db_uname\";\n");
 fwrite($fp,"\$db_pass   = \"$db_pass\";\n");
 fwrite($fp,"\$db_name   = \"$db_name\";\n");
 fwrite($fp,"\$db_admin  = \"$db_admin\";\n");
 fwrite($fp,"\$db_groups = \"$db_groups\";\n");
 fwrite($fp,"\$db_news   = \"$db_news\";\n");
 fwrite($fp,"\$db_news_pics   = \"$db_news_pics\";\n");
 fwrite($fp,"\$db_news_comments = \"$db_news_comments\";\n");
 fwrite($fp,"\$db_news_logged = \"$db_news_logged\";\n");
 fwrite($fp,"\$db_partners = \"$db_partners\";\n");
 fwrite($fp,"\$db_pnews  = \"$db_pnews\";\n");
 fwrite($fp,"\$db_story  = \"$db_story\";\n");
 fwrite($fp,"\$db_smileys = \"$db_smileys\";\n");
 fwrite($fp,"\$db_topic  = \"$db_topic\";\n");
 fwrite($fp,"\$db_users  = \"$db_users\";\n");
 fwrite($fp,"\$db_weekQ  = \"$db_weekQ\";\n");
 fwrite($fp,"\$db_weekA  = \"$db_weekA\";\n");
 fwrite($fp,"\$db_weather  = \"$db_weather\";\n");
 fwrite($fp,"\$db_rss  = \"$db_rss\";\n");
 fwrite($fp,"\$db_gallery_groups  = \"$db_gallery_groups\";\n");
 fwrite($fp,"\$db_gallery  = \"$db_gallery\";\n");

 fwrite($fp,"\$smileys_path  = \"$smileys_path\";\n");
 fwrite($fp,"\$smiley_url = \"$smileys_url\";\n");

 fwrite($fp,"\$partners_path = \"$partners_path\";\n");
 fwrite($fp,"\$partners_url = \"$partners_url\";\n");

 fwrite($fp,"\$topic_path = \"$topic_path\";\n");
 fwrite($fp,"\$topic_url = \"$topic_url\";\n");

 fwrite($fp,"\$rss_path = \"$rss_path\";\n");

 fwrite($fp,"\$news_path = \"$news_path\";\n");
 fwrite($fp,"\$news_url = \"$news_url\";\n");

 fwrite($fp,"\$gallery_path = \"$gallery_path\";\n");
 fwrite($fp,"\$gallery_url = \"$gallery_url\";\n");

 fwrite($fp,"\$weather_path = \"$weather_path\";\n");
 fwrite($fp,"\$weather_url = \"$weather_url\";\n");

 fwrite($fp,"?>");
 fclose($fp);
 mysql_close($link);
 ?>
 <br/>
 <h3>
 <p align="center">phpNewsManager is succssesfuly installed.<br/>
 Now, for your safety, change permission back to <font color="red">644</font> for file <b>db.inc.php</b> and <b>delete install.php</b> file !!!
 </p></h3>
 </td></tr></table><br/><br/>
 <?
}
?>
</body>
</html>