<?

// File Upload Center   ver. 2.0
//
// Copyright (C) 2001, 2002 by Sergey Korostel    skorostel@mail.ru
//


//----------------------------------------------------------------------------
//      FUNCTIONS
//----------------------------------------------------------------------------

function MsDosTimeToUNIX($DOSdate, $DOStime)
{
  $year = (($DOSdate & 65024) >> 9) + 1980;
  $month = ($DOSdate & 480) >> 5;
  $day = ($DOSdate & 31);
  $hours = ($DOStime & 63488) >> 11;
  $minutes = ($DOStime & 2016) >> 5;
  $seconds = ($DOStime & 31) * 2;
  return mktime($hours, $minutes, $seconds, $month, $day, $year);
}

function list_zip($filename)
{
  global $bordercolor, $headercolor, $tablecolor, $font, $headerfontcolor;
  global $normalfontcolor, $datetimeformat, $mess;
  $fp=@fopen($filename,"rb");
  if (!$fp) {return;}
  fseek($fp, -22, SEEK_END);
  // Get central directory field values
  $headersignature = 0;
  do { // Search header
    $data = fread($fp, 22);
    list($headersignature,$numberentries, $centraldirsize, $centraldiroffset) =
      array_values(unpack("Vheadersignature/x6/vnumberentries/Vcentraldirsize/Vcentraldiroffset", $data));

    fseek($fp, -23, SEEK_CUR);
  } while (($headersignature != 0x06054b50) && (ftell($fp) > 0));
  if ($headersignature != 0x06054b50)
  {
    echo "<p><font face=\"$font\" size=\"3\" color=\"$normalfontcolor\">$mess[45]</font></p>";
    fclose($fp);
    return;
  }
  // Go to start of central directory
  fseek($fp, $centraldiroffset, SEEK_SET);
  // Read central dir entries
  echo "<p><font face=\"$font\" size=\"3\" color=\"$normalfontcolor\">$mess[46]</font></p>";
  echo "<p><table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">";
  echo "<tr bgcolor=\"$headercolor\">
    <td>
      <b><font face=\"$font\" size=\"2\" color=\"$headerfontcolor\">$mess[15]</font></b>
    </td>
    <td>
      <b><font face=\"$font\" size=\"2\" color=\"$headerfontcolor\">$mess[17]</font></b>
    </td>
    <td>
      <b><font face=\"$font\" size=\"2\" color=\"$headerfontcolor\">$mess[47]</font></b>
    </td>
   </tr>";
  for ($i = 1; $i <= $numberentries; $i++)
  {
    // Read central dir entry
    $data = fread($fp, 46);
    list($arcfiletime,$arcfiledate,$arcfilesize,$arcfilenamelen,$arcfileattr) =
      array_values(unpack("x12/varcfiletime/varcfiledate/x8/Varcfilesize/Varcfilenamelen/x6/varcfileattr", $data));
    $filenamelen = fread($fp, $arcfilenamelen);

    $arcfiledatetime = MsDosTimeToUNIX($arcfiledate, $arcfiletime);

    echo "<tr bgcolor=\"$tablecolor\">";
    // Print FileName
    echo "<td>";
    echo "<font face=\"$font\" size=\"1\" color=\"$normalfontcolor\">";
    if ($arcfileattr == 16)
    {
      echo "<b>$filenamelen</b>";
    }
    else
    {
      echo $filenamelen;
    }
    echo "</font>";
    echo "</td>";
    // Print FileSize column
    echo "<td><font face=\"$font\" size=\"1\" color=\"$normalfontcolor\">";
    if ($arcfileattr == 16) { echo "$mess[48]";} else echo $arcfilesize;
    echo "</td></font>";
    // Print FileDate column
    echo "<td><font face=\"$font\" size=\"1\" color=\"$normalfontcolor\">";
    echo date($datetimeformat, $arcfiledatetime);
    echo "</td></font>";
    echo "</tr>";
  }
  echo "</table></p>";
  fclose($fp);
  return;
}

function place_header($message)
{
global $mess, $infopage, $font, $normalfontcolor, $selectedfontcolor, $homeurl;
global $languages,$show_configuration_menu,$allow_choose_language;
global $page_title,$uploadcentercaption,$uploadcentermessage;
global $tablecolor,$bordercolor,$headercolor,$headerfontcolor;
global $logged_user_name,$user_status;

page_header($page_title);

if ($show_configuration_menu)
{
  include("dynmenu.php");
  if ($user_status < 0)
    build_menu(0, "index.php");
  else
    build_menu(1, "index.php");
}

echo "
<div align=\"center\">
  <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
    <tr>
      <td align=\"left\" bgcolor=\"$headercolor\" colspan=\"4\">
        <table border=\"0\" width=\"100%\">
          <tr>
            <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\" width=\"50%\">
              <h1><font color=\"$headerfontcolor\" size=\"5\"><b>$uploadcentercaption</b></font></h1>
            </th>
            <td align=\"right\" bgcolor=\"$headercolor\" valign=\"middle\" width=\"50%\" nowrap>
              <p><font face=\"$font\" size=\"1\" color=\"$headerfontcolor\">
                ${uploadcentermessage}</font></p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td align=\"left\" bgcolor=\"$tablecolor\" valign=\"middle\">
        <table border=\"0\" width=\"100%\">
          <tr>
            <th align=\"left\" valign=\"middle\" width=\"60%\">
              <font size=\"1\" face=\"$font\" color=\"$selectedfontcolor\">&raquo;</font>

              <font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">";

if ($user_status < 0)
  echo $message;
else
  echo $logged_user_name.": ".$message;

echo "      </font></th>
            <td align=\"right\" valign=\"middle\" width=\"40%\">
              <a href=\"$homeurl\">
                <img src=\"images/home.gif\" alt=\"$mess[25]\" border=\"0\" /></a>
";
  echo "&nbsp;<a href=\"index.php\"><img src=\"images/refresh.gif\" alt=\"$mess[97]\" border=\"0\" /></a>";

if ((!$show_configuration_menu) && ($allow_choose_language))
{
  echo "&nbsp;&nbsp;&nbsp;";
  while (list($langid, $langdata) = each($languages))
  {
   echo "<a href=\"index.php?action=savelanguage&language=";
   echo $langid;
   echo "\"><img src=\"";
   echo $langdata["LangFlag"];
   echo "\" border=\"1\" alt=\"";
   echo $langdata["LangName"];
   echo "\"></a>\n";
  }
}

// Show login/logout message
echo "&nbsp;&nbsp;";
if ($user_status < 0)
  echo "<a href=\"login.php\">".$mess[71]."</a>";
else
{
  if ($user_status == 0)
  {
    echo "<a href=\"configure.php\"><img src=\"images/config.gif\" alt=\"$mess[132]\" border=\"0\" /></a>&nbsp;";
    echo "<a href=\"usrmanag.php\"><img src=\"images/users.gif\" alt=\"$mess[137]\" border=\"0\" /></a>&nbsp;";
  }
  echo "<a href=\"login.php\"><img src=\"images/user.gif\" alt=\"$mess[81]\" border=\"0\" /></a>";
  echo "&nbsp;";
  echo "<a href=\"login.php?action=logout\">".$mess[72]."</a>";
}

echo "
            </td>
          </tr>
        </table>
      </td>
   </tr>
</table>
<br>
";
 if ($user_status == 0)  // If administrator
 {
echo "
   <script language=\"JavaScript\">
   <!--
   function x () {
   return;
   }
   function AddString(addSmilie) {
     var revisedFileList;
     var currentFileList = document.DeleteFile.FileList.value;
     revisedFileList = currentFileList + addSmilie + \"\\n\";
     document.DeleteFile.FileList.value=revisedFileList;
     document.DeleteFile.FileList.focus();
     return;
   }
   //-->
   </script>
";
 }
// Place info table
 if ((file_exists($infopage)) && (filesize($infopage) != 0))
 {
  echo "<table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
    <tr>
      <td align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\">
        <font size=\"2\" face=\"$font\">
        <img src=\"images/info.gif\">
        <font color=\"$headerfontcolor\"><b>$mess[51]</b> </font></font></td>
    </tr>
    <tr>
      <td align=\"left\" bgcolor=\"$tablecolor\" valign=\"middle\">
        <font size=\"1\" color=\"$normalfontcolor\" face=\"$font\">";

   include($infopage);

   echo "</font></td></tr></table>";
   echo "<br>";
 }

}

function get_file_description($filename, $max_caracters = 0, $replacecharacters = 1)
{
  if (!file_exists("$filename.desc"))
    return array("","","");

  if ($max_caracters == 0)
    $max_caracters = filesize("$filename.desc");

  $fp=@fopen("$filename.desc","r");

  $upl_user=rtrim(fgets($fp, 255));
  $upl_ip=rtrim(fgets($fp, 255));

  $contents = fread ($fp, $max_caracters);
  fclose($fp);
  if ($replacecharacters)
  {
    $contents=str_replace("&","&amp;",$contents);
    $contents=str_replace("<","&lt;",$contents);
    $contents=str_replace(">","&gt;",$contents);
    $contents=str_replace("\"","&quot;",$contents);
    $contents=str_replace("\x0D","",$contents);
    $contents=str_replace("\x0A"," ",$contents);
    $contents=str_replace("_"," ",$contents);
  }
  return array($upl_user, $upl_ip, $contents);
}

function is_viewable($filename)
        {
        $retour=0;
        if(eregi("\.txt$|\.sql$|\.php$|\.php3$|\.phtml$|\.htm$|\.html$|\.cgi$|\.pl$|\.js$|\.css$|\.inc$",$filename)) {$retour=1;}
        return $retour;
        }

function is_image($filename)
        {
        $retour=0;
        if(eregi("\.png$|\.bmp$|\.jpg$|\.jpeg$|\.gif$",$filename)) {$retour=1;}
        return $retour;
        }

function is_browsable($filename)
        {
        $retour=0;
        if(eregi("\.zip$",$filename)) {$retour=1;}
        return $retour;
        }

function getfilesize($filename)
        {
        $taille=filesize($filename);
        if ($taille >= 1073741824) {$taille = round($taille / 1073741824 * 100) / 100 . " Gb";}
        elseif ($taille >= 1048576) {$taille = round($taille / 1048576 * 100) / 100 . " Mb";}
        elseif ($taille >= 1024) {$taille = round($taille / 1024 * 100) / 100 . " Kb";}
        else {$taille = $taille . " b";}
        if($taille==0) {$taille="-";}
        return $taille;
        }

function unix_time()
{
  global $timeoffset;
  $tmp = time() + 3600*$timeoffset;
  return $tmp;
}

function file_time($filename)
{
  global $timeoffset;
  $tmp = filemtime($filename) + 3600*$timeoffset;
  return $tmp;
}

function DeleteFile($filename)
{
  if (file_exists("$filename"))
    unlink("$filename");                // Delete file
  if (file_exists("$filename.desc"))
    {unlink("$filename.desc");}       // Delete description
  if (file_exists("$filename.dlcnt"))
    {unlink("$filename.dlcnt");}      // Delete download counter
}

function send_digest_and_maintenance_accounts()
{
  global $uploads_path,$accounts_path,$user_wish_receive_digest,$admin_email;
  global $datetimeformat, $mess, $comment_max_caracters,$GMToffset,$installurl,$mailinfopage;
  global $mailfunctionsenabled,$user_email,$activationcode,$mess,$user_account_creation_time;

  if ($mailfunctionsenabled)
  {
    $message = "";
    if (file_exists($mailinfopage))
    {
      $max_caracters = filesize($mailinfopage);
      $fp=@fopen($mailinfopage,"r");
      $message = fread($fp, $max_caracters);
      fclose($fp);
    }
    $message.="<html><body bgcolor=\"#FFFFFF\" text=\"#000000\">\n<table border=\"1\">\n";
    $time = time() - $GMToffset * 3600;
    $currentdate = getdate($time);
    // Get previous day   $time1 - day start, $time2 - day end
    $time1 = mktime(0, 0, 0, $currentdate['mon'], $currentdate['mday']-1, $currentdate['year']);
    $time2 = $time1 + 86400;

    // Read files info
    $current_dir = $uploads_path;
    list($liste,$totalsize)=listing($current_dir);

    $filecount = 0;
    if(is_array($liste))
    {
      while (list($filename,$mime) = each($liste))
      {
        if(is_dir("$current_dir/$filename"))
          continue;
      $file_modif_time = filemtime("$current_dir/$filename") - $GMToffset * 3600;
      // check if file uploaded in previous date
      if (($file_modif_time < $time1) || ($file_modif_time >= $time2))
        continue;

     $filecount++;
     $message.="
      <tr valign=\"top\">
        <td align=\"left\" width=\"95%\">
          $filename
        </td>
        <td align=\"left\" width=\"95%\" nowrap>\n";
      $message.= getfilesize("$current_dir/$filename");
      $message.= "</td>
        <td align=\"left\" width=\"95%\" nowrap>\n";
      $message.=date($datetimeformat, $file_modif_time);
      $message.= "</td>
        <td align=\"left\" width=\"95%\">\n";

      // Load description
      list($upl_user, $upl_ip, $contents) = get_file_description("$current_dir/$filename", $comment_max_caracters);
      if ($upl_user != "")
        $message.= "<b>$upl_user</b><br>";
      $message.= $contents;

     $message.= "
        </td>
      </tr>\n";
      }
    }
    $message.= "</table><br>\n";
    $message.= "<br>All file times are GMT<br>\n<br>\nWeb Page: $installurl\n";
    $message.= "</body></html>";
  }

  $time = time();

  // Browse each user
  $handle=opendir($accounts_path);
  while ($filename = readdir($handle))
  {
    if (substr($filename,0,1) != ".")
    {
      if (!is_dir("$accounts_path/$filename"))
      {
        load_user_profile($filename);
        if (($activationcode > 1) && (floor($time - $user_account_creation_time)/86400 >= 2)) // 2 days
          DeleteFile("$accounts_path/$filename");  // Delete unactivated account

        if ($mailfunctionsenabled && $user_wish_receive_digest && ($activationcode = 1) && ($filecount > 0))
        {
          $body=$message;
          $from="Administrator <$admin_email>";
          $headers="Content-Type: text/html; charset=iso-8859-1\n";
          $headers.="From: $from\nX-Mailer: System33r";
          mail($user_email,"Upload Center everyday digest",$body,$headers);
        }
      }
    }
  }
  closedir($handle);
}

function remove_old_files()
{
  global $daysinhistory, $uploads_path;
  $time = time();
  // Remove all old files
  $handle=opendir($uploads_path);
  while ($filename = readdir($handle))
  {
    if($filename!="." && $filename!="..")
    {
      if(!is_dir("$uploads_path/$filename"))
      {
        $file_modif_time=filemtime("$uploads_path/$filename");
        if (floor(($time - $file_modif_time)/86400) >= $daysinhistory)
        {
          DeleteFile("$uploads_path/$filename");  // Delete file & all auxiliary files
        }
      }
    }
  }
  closedir($handle);
}

function DeleteFilesByList($list)
{
  global $uploads_path;
  $list=str_replace("\x0D","",$list);
  $list=str_replace("\x0A",";",$list);
  $filenames=explode(";",$list);
  $i = 0;
  while ($i < count($filenames))
  {
    if ($filenames[$i] != "")
    {
      DeleteFile("$uploads_path/" . $filenames[$i]);
    }
    $i++;
  }
}

function do_maintenance()
{
  global $uploads_path,$GMToffset,$maintenancetime;
  $time = 0;
  // Read timestamp (when system last time do maintenance)
  if (file_exists("$uploads_path/$$$.dlcnt"))
  {
    $fp=fopen("$uploads_path/$$$.dlcnt","r");
    $time = fread($fp, 100); // read prev maintenance date
    fclose($fp);
  }
  if (floor((time() - $time)/86400) >= 1) // If 1 day passed, then do maintenence
  {
    // Calculate new timestamp (current mainantance time)
    $time = time();
    $currentdate = getdate($time);
    $time = mktime($maintenancetime + $GMToffset, 0, 0, $currentdate['mon'], $currentdate['mday'], $currentdate['year']);

    // Write new timestamp
    $fp = fopen("$uploads_path/$$$.dlcnt","w+"); // write counter file
    fwrite($fp, $time, 100); //  write back
    fclose($fp);

    remove_old_files();
    send_digest_and_maintenance_accounts();
  }
}

function filedownloadcount($filename)
{
  if (file_exists("$filename.dlcnt"))
  {
    $fp=fopen("$filename.dlcnt","r");
    $count = fread($fp, 15); // read counter file
    fclose($fp);
    return $count;
  }
  else
  {
    return 0;
  }
}

function increasefiledownloadcount($filename)
{
  if ($filename!="." && $filename!="..")
  {
  $count = filedownloadcount($filename);
  $count += 1;      //  number of downloads + 1
  $fp = fopen("$filename.dlcnt","w+"); // write counter file
  @flock($fp, LOCK_EX);    // Lock file in exclusive mode
  fwrite($fp, $count, 15); //  write back
  @flock($fp, LOCK_UN);    // Reset locking
  fclose($fp);
  }
}

function mimetype($filename)
        {
        global $mess,$HTTP_USER_AGENT;
        if(!eregi("MSIE",$HTTP_USER_AGENT)) {$client="netscape.gif";} else {$client="html.gif";}
        if(is_dir($filename)){$image="dossier.gif";}
        else if(eregi("\.txt$",$filename)){$image="txt.gif";}
        else if(eregi("\.html$",$filename)){$image=$client;}
        else if(eregi("\.htm$",$filename)){$image=$client;}
        else if(eregi("\.doc$",$filename)){$image="doc.gif";}
        else if(eregi("\.pdf$",$filename)){$image="pdf.gif";}
        else if(eregi("\.xls$",$filename)){$image="xls.gif";}
        else if(eregi("\.gif$",$filename)){$image="gif.gif";}
        else if(eregi("\.jpg$",$filename)){$image="jpg.gif";}
        else if(eregi("\.bmp$",$filename)){$image="bmp.gif";}
        else if(eregi("\.png$",$filename)){$image="gif.gif";}
        else if(eregi("\.zip$",$filename)){$image="zip.gif";}
        else if(eregi("\.rar$",$filename)){$image="rar.gif";}
        else if(eregi("\.gz$",$filename)){$image="zip.gif";}
        else if(eregi("\.tgz$",$filename)){$image="zip.gif";}
        else if(eregi("\.z$",$filename)){$image="zip.gif";}
        else if(eregi("\.exe$",$filename)){$image="exe.gif";}
        else if(eregi("\.mid$",$filename)){$image="mid.gif";}
        else if(eregi("\.wav$",$filename)){$image="wav.gif";}
        else if(eregi("\.mp3$",$filename)){$image="mp3.gif";}
        else if(eregi("\.avi$",$filename)){$image="avi.gif";}
        else if(eregi("\.mpg$",$filename)){$image="mpg.gif";}
        else if(eregi("\.mpeg$",$filename)){$image="mpg.gif";}
        else if(eregi("\.mov$",$filename)){$image="mov.gif";}
        else if(eregi("\.swf$",$filename)){$image="flash.gif";}
        else {$image="defaut.gif";}
        return $image;
        }

function init($directory)
        {
        global $uploads_path,$direction,$mess,$font;
        if($directory==""){$current_dir=$uploads_path;}
        if($direction==""){$direction=1;}
        else
                {
                if($direction==1){$direction=0;}else{$direction=1;}
                }
        if($directory!=""){$current_dir="$uploads_path/$directory";}
        if(!file_exists($uploads_path))
          {echo "<font face=\"$font\" size=\"2\">The root path is not correct. Check the settings<br><br><a href=\"index.php\">$mess[29]</a></font>\n";exit;}
        if(!is_dir($current_dir)) {echo "<font face=\"$font\" color=\"$normalfontcolor\" size=\"2\">$mess[30]<br><br><a href=\"javascript:window.history.back()\">$mess[29]</a></font>\n";exit;}
        return $current_dir;
        }

function assemble_tableaux($t1,$t2)
        {
        global $direction;
        $liste="";
        if($direction==0) {$tab1=$t1; $tab2=$t2;} else {$tab1=$t2; $tab2=$t1;}
        if(is_array($tab1)) {while (list($cle,$val) = each($tab1)) {$liste[$cle]=$val;}}
        if(is_array($tab2)) {while (list($cle,$val) = each($tab2)) {$liste[$cle]=$val;}}
        return $liste;
        }

function txt_vers_html($chaine)
        {
        $chaine=str_replace("&","&amp;",$chaine);
        $chaine=str_replace("<","&lt;",$chaine);
        $chaine=str_replace(">","&gt;",$chaine);
        $chaine=str_replace("\"","&quot;",$chaine);
        return $chaine;
        }

function show_hidden_files($filename)
        {
        global $showhidden;
        $retour=1;
        if(substr($filename,0,1)=="." && $showhidden==0) {$retour=0;}
        return $retour;
        }

function listing($current_dir)
        {
        global $direction,$order;
        $totalsize=0;
        $handle=opendir($current_dir);
        $list_dir = "";
        $list_file = "";
        while ($filename = readdir($handle))
                {
                if($filename!="." && $filename!=".."
                && !eregi(".desc$", $filename)        // Test for description
                && !eregi(".dlcnt$", $filename)        // Test for download counter
                && show_hidden_files($filename)==1)
                        {
                        $filesize=filesize("$current_dir/$filename");
                        $totalsize+=$filesize;
                        if(is_dir("$current_dir/$filename"))
                                {
                        //      if($order=="mod") {$list_dir[$filename]=filemtime("$current_dir/$filename");}
                        //      else {$list_dir[$filename]=$filename;}
                                }
                        else
                                {
                                if($order=="nom") {$list_file[$filename]=mimetype("$current_dir/$filename");}
                                else if($order=="taille") {$list_file[$filename]=$filesize;}
                                else if($order=="mod") {$list_file[$filename]=filemtime("$current_dir/$filename");}
                                else if($order=="rating") {$list_file[$filename]=filedownloadcount("$current_dir/$filename");}
                                else {$list_file[$filename]=mimetype("$current_dir/$filename","image");}
                                }
                        }
                }
        closedir($handle);

        if(is_array($list_file))
                {
                if($order=="nom") {if($direction==0){ksort($list_file);}else{krsort($list_file);}}
                else if($order=="mod") {if($direction==0){arsort($list_file);}else{asort($list_file);}}
                else if($order=="rating"||$order=="type") {if($direction==0){asort($list_file);}else{arsort($list_file);}}
                else {if($direction==0){ksort($list_file);}else{krsort($list_file);}}
                }
//      if(is_array($list_dir))
//              {
//              if($order=="mod") {if($direction==0){arsort($list_dir);}else{asort($list_dir);}}
//              else {if($direction==0){ksort($list_dir);}else{krsort($list_dir);}}
//              }

        $liste=assemble_tableaux($list_dir,$list_file);
        if ($totalsize >= 1073741824) {$totalsize = round($totalsize / 1073741824 * 100) / 100 . " Gb";}
        elseif ($totalsize >= 1048576) {$totalsize = round($totalsize / 1048576 * 100) / 100 . " Mb";}
        elseif ($totalsize >= 1024) {$totalsize = round($totalsize / 1024 * 100) / 100 . " Kb";}
        else {$totalsize = $totalsize . " b";}

        return array($liste,$totalsize);
        }

function printshowalldaysmessage()
{
  global $lightcolor,$normalfontcolor,$font,$mess;
  echo "
    <tr bgcolor=\"$lightcolor\" valign=\"top\">
      <td align=\"right\" colspan=\"5\">
        <div align=\"left\"><font face=\"$font\" color=\"$normalfontcolor\" size=\"2\"><img src=\"images/calendar.gif\">
          <a href=\"index.php?showfilesfordate=All\">$mess[44]</a></font></div>
      </td>
    </tr>\n";
}

function contents_dir($current_dir)
{
  global $font,$direction,$order,$directory,$totalsize,$mess,$tablecolor,$lightcolor;
  global $file_out_max_caracters,$showfilesfordate,$normalfontcolor;
  global $comment_max_caracters,$datetimeformat;
  global $user_status,$activationcode,$maxfilesizetomail,$mailfunctionsenabled;

  $prev_currentdate = getdate(0);
  $day_passed = 0;


  if (($showfilesfordate != "Last") && ($showfilesfordate != "All"))
  {
    if (count(explode("-", $showfilesfordate)) != 3)  // Check the right date format
      $showfilesfordate = "Last";
    else
      list($swyear, $swmonth, $swday) = explode("-", $showfilesfordate);
  }

  // Read directory
  list($liste,$totalsize)=listing($current_dir);

  if(is_array($liste))
  {
    while (list($filename,$mime) = each($liste))
    {
      if(is_dir("$current_dir/$filename"))
      {
        $filenameandpath="index.php?direction=$direction&order=$order&directory=";
        if($directory!=""){$filenameandpath.="$directory/";}
        $filenameandpath.=$filename;
        $affiche_copier="non";
      }
      else
      {
        $filenameandpath="";
        if($directory!=""){$filenameandpath.="$directory/";}
        $filenameandpath.=$filename;
        $affiche_copier="oui";
      }
    $file_modif_time = file_time("$current_dir/$filename");

   if($order=="mod")
   {
    $currentdate = getdate($file_modif_time);
    if (($currentdate['year'] != $prev_currentdate['year']) ||
        ($currentdate['mon'] != $prev_currentdate['mon']) ||
        ($currentdate['mday'] != $prev_currentdate['mday']))
    {

      if (($day_passed == 1) && ($showfilesfordate == "Last"))
        Break;

      if (($showfilesfordate != "Last") && ($showfilesfordate != "All"))
        if (($currentdate['year'] != $swyear) || ($currentdate['mon'] != $swmonth) || ($currentdate['mday'] != $swday))
          Continue;

      // Print day stamp
      $prev_currentdate = $currentdate;
echo "
      <tr bgcolor=\"$lightcolor\" valign=\"top\">
        <td align=\"right\" colspan=\"5\">
          <div align=\"left\"><font face=\"$font\" color=\"$normalfontcolor\" size=\"2\"><img src=\"images/calendar.gif\">\n";
        $month = $currentdate['mon'];
        $mday = $currentdate['mday'];
        $year = $currentdate['year'];
  echo "$mess[$month] $mday, $year";
echo "      </font></div>
        </td>
      </tr>\n";
      $day_passed+=1;
    }
  }
echo "
    <tr bgcolor=\"$tablecolor\" valign=\"top\">
      <td align=\"right\" width=\"95%\">
        <div align=\"left\">
        <font face=\"$font\" size=\"1\" color=\"$normalfontcolor\">
          <img src=\"images/".mimetype("$current_dir/$filename")."\"align=\"ABSMIDDLE\" border=\"0\">\n";
          if(is_viewable($filename) || is_image($filename) || is_browsable($filename) || is_dir("$current_dir/$filename"))
            {echo "<a href=\"javascript:popup('$filenameandpath')\">";}
echo      substr($filename,0,$file_out_max_caracters);
          if(is_viewable($filename) || is_image($filename) || is_browsable($filename) || is_dir("$current_dir/$filename"))
            {echo "</a>\n";}
echo "   </font></div>
      </td>
      <td align=\"right\" width=\"95%\" nowrap>
        <div align=\"left\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">";


if ($user_status == 0) // If logged as admin, add delete file link
{
echo "&nbsp;
      <a href=\"javascript: x()\" onClick=\"AddString('$filename');\">
        <img src=\"images/delete.gif\" border=\"0\"></a>";
}

// Show mail file link

if (($user_status >= 0) && ($activationcode == 1) && $mailfunctionsenabled)
{
  if (($user_status == 0) || ($user_status == 1) || (filesize("$current_dir/$filename") < $maxfilesizetomail * 1024))
  {
echo "        <a href=\"javascript:popupmail('$filenameandpath')\">
             <img src=\"images/mail.gif\"
             alt=\"$mess[68]\" width=\"20\" height=\"20\" border=\"0\"></a>";
  }
  else
echo "<img src=\"images/empty.gif\" width=\"20\" height=\"20\" border=\"0\">";
}
echo "        <a href=\"index.php?action=downloadfile&filename=$filename\">
             <img src=\"images/download.gif\"
             alt=\"$mess[23]\" width=\"20\" height=\"20\" border=\"0\"></a>";
echo       filedownloadcount("$current_dir/$filename");
echo "    </font></div>
      </td>
      <td align=\"right\" width=\"95%\" nowrap>
        <div align=\"left\"><font size=\"1\" color=\"$normalfontcolor\" face=\"$font\">\n";
echo      getfilesize("$current_dir/$filename");
echo "    </font></div>
      </td>
      <td align=\"right\" width=\"95%\" nowrap>
        <div align=\"left\"><font size=\"1\" color=\"$normalfontcolor\" face=\"$font\">\n";
echo      date($datetimeformat, $file_modif_time);
echo "  </font></div>
      </td>
      <td align=\"right\" width=\"95%\">
        <div align=\"left\">
          <p><font size=\"1\" color=\"$normalfontcolor\" face=\"$font\">\n";

        // Load description
        list($upl_user, $upl_ip, $contents) = get_file_description("$current_dir/$filename", $comment_max_caracters);
        if ($user_status == 0) // If admin, show IP
          if ($upl_user != "")
            echo    "<b>$upl_user</b> - <b>$upl_ip</b><br>";
          else
            echo    "<b>$upl_ip</b><br>";
        else
        {
          if ($upl_user != "")
            echo    "<b>$upl_user</b><br>";
        }
        echo    $contents;
echo "    </font></p>
        </div>
      </td>
    </tr>\n";
    }
    if ($showfilesfordate != "All") // Print "Show all days message"
      printshowalldaysmessage();
  }
}

function list_dir($current_dir)
{
  global $directory,$uploads_path,$mess,$direction;
  global $font,$order,$totalsize,$tablecolor,$headercolor,$bordercolor;
  global $headerfontcolor, $normalfontcolor;

        if(eregi("\.\.",$directory)) {$directory="";}
        $current_dir=init($directory);
        //$base_nom_rep=str_replace($uploads_path,"",$current_dir);
        //if($base_nom_rep==""){$base_nom_rep="/";}

        if($direction==1){$direction=0;}else{$direction=1;}
        if($direction==1){$direction=0;}else{$direction=1;}

        echo "<script language=\"javascript\">\n";
        echo "function popup(lien) {\n";
        echo "var fen=window.open('index.php?action=view&filename='+lien,'filemanager','status=yes,scrollbars=yes,resizable=yes,width=500,height=400');\n";
        echo "}\n";
        echo "function popupmail(lien) {\n";
        echo "var fen=window.open('index.php?action=mailfile&filename='+lien,'filemanager','status=yes,scrollbars=yes,resizable=yes,width=500,height=400');\n";
        echo "}\n";
        echo "</script>\n";
        $filenameandpath = "";
        if($directory!=""){$filenameandpath="&directory=".$directory;}

echo "
  <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
    <tr bgcolor=\"$headercolor\">
      <td align=\"right\" valign=\"middle\" width=\"95%\">
        <div align=\"left\"><font color=\"$headerfontcolor\"><b><font face=\"$font\" size=\"2\">$mess[15]</font>
          <a href=\"index.php?order=nom&direction=$direction".$filenameandpath."\">\n";
          if ($order=="nom"||$order=="")
            {echo "<img src=\"images/fleche${direction}.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";}
          else
            {echo "<img src=\"images/fleche.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";}
echo "    </b></font></div>
      </td>
      <td align=\"right\" valign=\"middle\" width=\"95%\" nowrap>
        <div align=\"left\"><font color=\"$headerfontcolor\"><b><font face=\"$font\" size=\"2\">$mess[16]</font><font color=\"$headerfontcolor\"><b>
          <a href=\"index.php?order=rating&direction=$direction\">\n";
          if ($order=="rating")
            {echo "<img src=\"images/fleche${direction}.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";}
          else
            {echo "<img src=\"images/fleche.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";}
echo "    </b></font></b></font></div>
      </td>
      <td align=\"right\" valign=\"middle\" width=\"95%\" nowrap>
        <div align=\"left\"><font color=\"$headerfontcolor\"><b><font face=\"$font\" size=\"2\">$mess[17]</font>
          <a href=\"index.php?order=taille&direction=$direction\">\n";
          if ($order=="taille")
            {echo "<img src=\"images/fleche${direction}.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";}
          else
            {echo "<img src=\"images/fleche.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";}
echo "    </b></font></div>
      </td>
      <td align=\"right\" valign=\"middle\" width=\"95%\" nowrap>
        <div align=\"left\"><font color=\"$headerfontcolor\"><b><font face=\"$font\" size=\"2\">$mess[18]</font>
          <a href=\"index.php?order=mod&direction=$direction\">\n";
          if ($order=="mod")
            {echo "<img src=\"images/fleche${direction}.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";}
          else
            {echo "<img src=\"images/fleche.gif\" alt=\"$mess[24]\" width=\"10\" height=\"10\" border=\"0\"></a>\n";}
echo "    </b></font></div>
      </td>
      <td align=\"right\" valign=\"middle\" width=\"95%\">
        <div align=\"left\"><font color=\"$headerfontcolor\"><b><font face=\"$font,\" size=\"2\">$mess[19]</font></b></font></div>
      </td>
    </tr>\n";

        if($direction==1){$direction=0;}else{$direction=1;}

        if($directory!="")
                {
                $nom=dirname($directory);
                echo "<tr><td><a href=\"index.php?direction=$direction&order=$order";
                if($directory!=$nom && $nom!="."){echo "&directory=$nom";}
                echo "\"><img src=\"images/parent.gif\" width=\"20\" height=\"20\" align=\"ABSMIDDLE\" border=\"0\"><font face=\"$font\" color=\"$normalfontcolor\" size=\"2\">$mess[24]</font></a></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
                }

        contents_dir($current_dir);
echo "
    <tr bgcolor=\"$tablecolor\" valign=\"top\">
      <td align=\"right\" colspan=\"5\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">
        <b>$mess[43]</b>: $totalsize</font></td>
    </tr>
  </table>
<br>
<!-- File upload script is writen by Sergey Korostel   mailto:skorostel@mail.ru  //-->
<br>
\n";


}

function deldir($location)
        {
        if(is_dir($location))
                {
                $all=opendir($location);
                while ($file=readdir($all))
                        {
                        if (is_dir("$location/$file") && $file !=".." && $file!=".")
                                {
                                deldir("$location/$file");
                                if(file_exists("$location/$file")){rmdir("$location/$file"); }
                                unset($file);
                                }
                        elseif (!is_dir("$location/$file"))
                                {
                                if(file_exists("$location/$file")){unlink("$location/$file"); }
                                unset($file);
                                }
                        }
                closedir($all);
                rmdir($location);
                }
        else
                {
                if(file_exists("$location")) {unlink("$location");}
                }
        }

function normalize_filename($nom)
        {
        global $file_name_max_caracters;
        $nom=stripslashes($nom);
        $nom=str_replace("'","",$nom);
        $nom=str_replace("\"","",$nom);
        $nom=str_replace("\"","",$nom);
        $nom=str_replace("&","",$nom);
        $nom=str_replace(",","",$nom);
        $nom=str_replace(";","",$nom);
        $nom=str_replace("/","",$nom);
        $nom=str_replace("\\","",$nom);
        $nom=str_replace("`","",$nom);
        $nom=str_replace("<","",$nom);
        $nom=str_replace(">","",$nom);
        $nom=str_replace(":","",$nom);
        $nom=str_replace("*","",$nom);
        $nom=str_replace("|","",$nom);
        $nom=str_replace("?","",$nom);
        $nom=str_replace("§","",$nom);
        $nom=str_replace("+","",$nom);
        $nom=str_replace("^","",$nom);
        $nom=str_replace("(","",$nom);
        $nom=str_replace(")","",$nom);
        $nom=str_replace("=","",$nom);
        $nom=str_replace("$","",$nom);
        $nom=str_replace("%","",$nom);
        $nom = substr ($nom,0,$file_name_max_caracters);
        return $nom;
        }

//----------------------------------------------------------------------------
//      Shows complete page
//----------------------------------------------------------------------------

function show_contents()
{
global $current_dir,$directory,$uploads_path,$mess,$direction,$timeoffset,$daysinhistory;
global $order,$totalsize,$font,$tablecolor,$bordercolor,$headercolor,$showfilesfordate;
global $headerfontcolor,$normalfontcolor,$user_status,$allow_anonymous_upload,$allow_anonymous_view;

echo "<center>\n";

if ($allow_anonymous_view || (($user_status != 4) && ($user_status > 0)))
{
 // Print date selector
 echo "
   <table cellSpacing=\"1\" cellPadding=\"4\" width=\"85%\" bgColor=\"$bordercolor\" border=\"0\">
     <tr>
       <td align=\"center\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">";

         $today = time() + 3600*$timeoffset;
         for ($i = $daysinhistory-1; $i >= 0; $i--)
         {
           $today_date = getdate($today - $i*86400);
           $month = $today_date['mon'];
           $mday = $today_date['mday'];
           $year = $today_date['year'];
           $dayofweek = $mess[60+$today_date['wday']];
           if ($i > 6)
             echo "<a href=\"index.php?showfilesfordate=$year-$month-$mday\">$mday</a> ";
           else
             echo "<a href=\"index.php?showfilesfordate=$year-$month-$mday\">$dayofweek</a> &nbsp;";
         }
         echo "&nbsp;&nbsp;<a href=\"index.php?showfilesfordate=Last\">$mess[141]</a> &nbsp;";
         echo "<a href=\"index.php?showfilesfordate=All\">$mess[142]</a> ";
 echo " </font></td>
     </tr>
   </table>
 <br>";
  list_dir($current_dir);
}

if ($allow_anonymous_upload || (($user_status != 3) && ($user_status > 0)))
{
  echo "  <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">\n";
  echo "    <tr>\n";
  if ($user_status != 0)
  {
  echo "      <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\"><font size=\"2\" face=\"$font\" color=\"$headerfontcolor\">$mess[20]</font></th>\n";
  }
  else
  {
  echo "      <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\"><font size=\"2\" face=\"$font\" color=\"$headerfontcolor\">$mess[20]/$mess[170]:</font></th>\n";
  }
  echo "    </tr>\n";
  echo "    <tr>\n";
  echo "        <td align=\"left\" bgcolor=\"$tablecolor\" valign=\"middle\">\n";
  if ($user_status == 0)
  {
  echo "        <form name=\"DeleteFile\" action=\"index.php\" enctype=\"multipart/form-data\" method=\"post\" style=\"margin: 0\">\n";
  echo "          <input type=\"hidden\" name=\"action\" value=\"deletefiles\">\n";
  echo "          <input type=\"hidden\" name=\"directory\" value=\"$directory\">\n";
  echo "          <input type=\"hidden\" name=\"order\" value=\"$order\">\n";
  echo "          <input type=\"hidden\" name=\"direction\" value=\"$direction\">\n";
  echo "          <input type=\"hidden\" name=\"showfilesfordate\" value=\"$showfilesfordate\">\n";
  echo "          <textarea name=\"FileList\" cols=\"70\" rows=\"10\"></textarea>\n";
  echo "          <input type=\"submit\" value=\"$mess[169]\" />\n";
  echo "        </form><br>\n";
  }
  echo "        <form name=\"upload\" action=\"index.php\" enctype=\"multipart/form-data\" method=\"post\" style=\"margin: 0\">\n";
  echo "          <input type=\"hidden\" name=\"action\" value=\"upload\">\n";
  echo "          <input type=\"hidden\" name=\"directory\" value=\"$directory\">\n";
  echo "          <input type=\"hidden\" name=\"order\" value=\"$order\">\n";
  echo "          <input type=\"hidden\" name=\"direction\" value=\"$direction\">\n";
  echo "          <input type=\"hidden\" name=\"showfilesfordate\" value=\"$showfilesfordate\">\n";
  echo "          <table border=\"0\" width=\"100%\" cellpadding=\"4\">\n";
  echo "            <tr>\n";
  echo "              <td align=\"left\" width=\"15%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[21]</font></td>\n";
  echo "              <td align=\"left\" width=\"85%\">\n";
  echo "                <input type=\"file\" name=\"userfile\" size=\"50\" />\n";
  echo "              </td>\n";
  echo "            </tr>\n";
  echo "            <tr> \n";
  echo "              <td align=\"left\" width=\"15%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[22]</font></td>\n";
  echo "              <td align=\"left\" width=\"85%\">\n";
  echo "                <textarea name=\"description\" cols=\"50\" rows=\"3\"></textarea>\n";
  echo "              </td>\n";
  echo "            </tr>\n";
  echo "            <tr>\n";
  echo "              <td align=\"left\" width=\"100%\" colspan=\"2\">\n";
  echo "                <input type=\"submit\" value=\"$mess[20]\" />\n";
  echo "              </td>\n";
  echo "            </tr>\n";
  echo "          </table>\n";
  echo "        </form>\n";

  echo "        </td>\n";
  echo "    </tr>\n";
  echo "    </table>\n";
}
echo "</center>\n";
}

//----------------------------------------------------------------------------
//      MAIN
//----------------------------------------------------------------------------

header("Expires: Mon, 03 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
include("include/conf.php");
include("functions.php");

if (!isset($loginname))
  $loginname = "";
if (!isset($password))
  $password = "";

if (!isset($language))
  $language = "";
if (!isset($order))
  $order = "";
if (!isset($action))
  $action = "";
if (!isset($showfilesfordate))
  $showfilesfordate = "Last";  // Show only files for last day

if (!isset($current_dir))
  $current_dir = "";

if($language=="")
  {$language=$dft_language;}
if ($order=="")
  {$order="mod";}

$timeoffset = -$GMToffset + $languages[$language]["TimeZone"];

if (!isset($skinindex))
  $skinindex = 0;
if ($skinindex > count($skins))
  $skinindex = 0;

$bordercolor=$skins[$skinindex]["bordercolor"];
$headercolor = $skins[$skinindex]["headercolor"];
$tablecolor=$skins[$skinindex]["tablecolor"];
$lightcolor=$skins[$skinindex]["lightcolor"];
$headerfontcolor=$skins[$skinindex]["headerfontcolor"];
$normalfontcolor=$skins[$skinindex]["normalfontcolor"];
$selectedfontcolor=$skins[$skinindex]["selectedfontcolor"];

// Do maintenance opeartions (remove old files, send digest)
do_maintenance();

// Check active user
$activationcode = 0;
if (!isset($logged_user_name))
  $logged_user_name = "";
$user_status = -1;  // Anonymous user
if ($logged_user_name != "")
  if (!check_is_user_session_active($logged_user_name))
  {
    $user_status = -1;
    $logged_user_name = "";
  }
if ($user_status < 0)
  $logged_user_name = "";
if ($activationcode != 1)  // If account not activated or disabled
{
  $user_status = -1;  // Anonymous user
  $logged_user_name = "";
}

switch($action) {


//----------------------------------------------------------------------------
//      Delete files
//----------------------------------------------------------------------------

case "deletefiles";
require("include/${language}.php");
if ($user_status == 0)
{
  DeleteFilesByList($FileList);
  place_header("$loginname: files succesfully deleted.");
}
else
{
  place_header("$loginname: can't delete files - account invalid.");
}
show_contents();
break;


//----------------------------------------------------------------------------
//      Change Language
//----------------------------------------------------------------------------

case "savelanguage";
$language=$HTTP_GET_VARS["language"];
setcookie("language",$language,time()+31536000,$cookiepath,$cookiedomain, $cookiesecure);  // 1 year
if ($second_cookiedomain != "")
  setcookie("language",$language,time()+31536000, $second_cookiepath, $second_cookiedomain, $cookiesecure); // 1 year

require("include/${language}.php");
$timeoffset = -$GMToffset + $languages[$language]["TimeZone"];
place_header($mess[41]);
show_contents();
break;


//----------------------------------------------------------------------------
//      Change Skin
//----------------------------------------------------------------------------

case "selectskin";
$skinindex=$HTTP_GET_VARS["skinindex"];
if ($skinindex > count($skins))
  $skinindex = 0;
setcookie("skinindex",$skinindex,time()+31536000);  // 1 year
if ($second_cookiedomain != "")
  setcookie("skinindex",$skinindex,time()+31536000, $second_cookiepath, $second_cookiedomain, $cookiesecure); // 1 year
$bordercolor=$skins[$skinindex]["bordercolor"];
$headercolor = $skins[$skinindex]["headercolor"];
$tablecolor=$skins[$skinindex]["tablecolor"];
$lightcolor=$skins[$skinindex]["lightcolor"];
$headerfontcolor=$skins[$skinindex]["headerfontcolor"];
$normalfontcolor=$skins[$skinindex]["normalfontcolor"];
$selectedfontcolor=$skins[$skinindex]["selectedfontcolor"];
require("include/${language}.php");
place_header($mess[96]);
show_contents();
break;


//----------------------------------------------------------------------------
//      DOWNLOAD
//----------------------------------------------------------------------------

case "downloadfile";
require("include/${language}.php");
$Nomfilename = basename($filename);
$taille=filesize("$uploads_path/$filename");
increasefiledownloadcount("$uploads_path/$filename");

if (($user_status >= 0) && ($logged_user_name != ""))  // Update user statistics
{
  list($files_uploaded, $files_downloaded, $files_emailed) = load_userstat($logged_user_name);
  $files_downloaded++;
  save_userstat($logged_user_name, $files_uploaded, $files_downloaded, $files_emailed, time());
}

/*
header("Content-Type: application/force-download; name=\"$Nomfilename\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: $taille");
header("Content-Disposition: attachment; filename=\"$Nomfilename\"");
header("Expires: 0");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
readfile("$uploads_path/$filename");
*/
header("Location: $url_path/$filename");

exit();
break;


//----------------------------------------------------------------------------
//      VIEW & PRINT
//----------------------------------------------------------------------------

case "view";
require("include/${language}.php");
$filenametoview=basename($filename);
page_header($mess[26].": ".$filenametoview);

echo "<center><font face=\"$font\" color=\"$normalfontcolor\" size=\"2\">$mess[26] : ";
echo "<img src=\"images/".mimetype("$uploads_path/$filename")."\" align=\"ABSMIDDLE\">\n";
echo "<b>".$filenametoview."</b></font><br><br><hr>\n";
echo "<a href=\"javascript:window.print()\"><img src=\"images/imprimer.gif\" alt=\"$mess[27]\" border=\"0\"></a>\n";
echo "<a href=\"index.php?action=downloadfile&filename=$filename\"><img src=\"images/download.gif\" alt=\"$mess[23]\" width=\"20\" height=\"20\" border=\"0\"></a>";
echo "<a href=\"javascript:window.close()\"><img src=\"images/back.gif\" alt=\"$mess[28]\" border=\"0\"></a>\n";
echo "<br>\n";
echo "<hr><br>";
if(!is_image($filename))
{
   echo "</center>\n";
   if (is_browsable($filename))
   {
     list_zip("$uploads_path/$filename");
   }
   else
   {
     $fp=@fopen("$uploads_path/$filename","r");
     if($fp)
     {
       echo "<font face=\"$font\" color=\"$normalfontcolor\" size=\"1\">\n";
       while(!feof($fp))
       {
         $buffer=fgets($fp,4096);
         $buffer=txt_vers_html($buffer);
         $buffer=str_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$buffer);
         echo $buffer."<br>";
       }
       fclose($fp);
       echo "</font>\n";
     }
     else
     {
       echo "<font face=\"$font\" color=\"$normalfontcolor\" size=\"2\">$mess[31] : $uploads_path/$filename</font>";
     }
   }
   echo "<center>\n";
}
else
{
  echo "<img src=\"$url_path/$filename\">\n";
}
echo "<hr>\n";
echo "<a href=\"javascript:window.print()\"><img src=\"images/imprimer.gif\" alt=\"$mess[27]\" border=\"0\"></a>\n";
echo "<a href=\"index.php?action=downloadfile&filename=$filename\"><img src=\"images/download.gif\" alt=\"$mess[23]\" width=\"20\" height=\"20\" border=\"0\"></a>";
echo "<a href=\"javascript:window.close()\"><img src=\"images/back.gif\" alt=\"$mess[28]\" border=\"0\"></a>\n";
echo "<hr></center>\n";
echo "</body>\n";
echo "</html>\n";
exit;
break;


//----------------------------------------------------------------------------
//      SEND FILE BY E-MAIL
//----------------------------------------------------------------------------

case "mailfile";
require("include/${language}.php");
$filenametomail=basename($filename);
page_header($mess[26].": ".$filenametomail);

echo "<center><font face=\"$font\" color=\"$normalfontcolor\" size=\"2\">$mess[26] : ";
echo "<img src=\"images/".mimetype("$uploads_path/$filename")."\" align=\"ABSMIDDLE\">\n";
echo "<b>".$filenametomail."</b></font><br><br><hr>\n";
echo "<a href=\"javascript:window.close()\"><img src=\"images/back.gif\" alt=\"$mess[28]\" border=\"0\"></a>\n";
echo "<br>\n";
echo "<hr><br>";

if (($user_status >= 0) && ($activationcode == 1))
{
  if (($user_status == 0) || (filesize("$uploads_path/$filename") < $maxfilesizetomail * 1024))
  {
    $body = "Here is the file you ordered to your e-mail\n\n";
    // Load file description
    list($upl_user, $upl_ip, $contents) = get_file_description("$uploads_path/$filename", 0, 0);

    if ($upl_user != "")
      $body .= sprintf($mess[70], $upl_user);
    $body .= "\n";
    if ($user_status == 0) // If admin
      $body .= "IP: ".$upl_ip;
    $body .= "\n";
    $body .= $mess[92];
    $body .= getfilesize("$uploads_path/$filename");
    $body .= "\n";
    $body .= $mess[90];
    $file_modif_time = file_time("$uploads_path/$filename");
    $body .= date($datetimeformat, $file_modif_time);
    $body .= "\n\n";
    $body .= $mess[22].":\n";
    $body .= $contents;
    $body .= "\n
Regards,
$admin_name
Email: mailto:$admin_email
Web Page: $installurl";

    $mm = new MIME_MAIL("Administrator <$admin_email>", "Upload Center File", $body);
    $mm -> add_file("$uploads_path/$filename");
    if ($mm -> send($user_email))
    {
      // Update statistics
      increasefiledownloadcount("$uploads_path/$filename");
      if (($user_status >= 0) && ($logged_user_name != ""))  // Update user statistics
      {
        list($files_uploaded, $files_downloaded, $files_emailed) = load_userstat($logged_user_name);
        $files_emailed++;
        save_userstat($logged_user_name, $files_uploaded, $files_downloaded, $files_emailed, time());
      }
      echo "<p><font face=\"$font\" size=\"3\" color=\"$normalfontcolor\">";
      echo sprintf($mess[69], "<b>".$user_email."</b>");
      echo "</font></p>";
    }
    else
    {
      echo "<p><font face=\"$font\" size=\"3\" color=\"$normalfontcolor\">";
      echo $mess[177]." ".$mess[179];
      echo "</font></p>";
    }
  }
}

echo "<hr>\n";
echo "<a href=\"javascript:window.close()\"><img src=\"images/back.gif\" alt=\"$mess[28]\" border=\"0\"></a>\n";
echo "<hr></center>\n";
echo "</body>\n";
echo "</html>\n";
exit;
break;


//----------------------------------------------------------------------------
//      UPLOAD
//----------------------------------------------------------------------------

case "upload";
  require("include/${language}.php");
  $message = $mess[40];

  if (!($allow_anonymous_upload || (($user_status != 3) && ($user_status > 0))))
  {
    place_header(sprintf($mess[49], "<b>$userfile_name</b>"));
    show_contents();
    break;
  }

  $directory_source="/$directory";
  $destination=$uploads_path.$directory_source;
  if ($userfile_size!=0)
    $size_kb=$userfile_size/1024;
  else
    $size_kb=0;
  if ($userfile=="none")
    $message=$mess[34];
  if ($userfile!="none" && $userfile_size!=0)
  {
    $userfile_name=normalize_filename($userfile_name);

    // Try if file exists
    if (file_exists("$destination/$userfile_name") ||
    // Or file is script
      eregi($rejectedfiles, $userfile_name) || ($size_kb > $maxalowedfilesize))
    {
      if ($size_kb > $maxalowedfilesize)
        $message="$mess[38] <b>$userfile_name</b> $mess[50] ($maxalowedfilesize Kb)!";
      else
        if (eregi($rejectedfiles, $userfile_name))  // If file is script
          $message=sprintf($mess[49], "<b>$userfile_name</b>");
        else
          $message="$mess[38] <b>$userfile_name</b> $mess[39]";
    }
    else
    {
      if (($user_status >= 0) && ($logged_user_name != ""))  // Update user statistics
      {
        list($files_uploaded, $files_downloaded, $files_emailed) = load_userstat($logged_user_name);
        $files_uploaded++;
        save_userstat($logged_user_name, $files_uploaded, $files_downloaded, $files_emailed, time());
      }

      $description = stripslashes($description);

      // Save description
      $fp=fopen("$destination/$userfile_name.desc","w");

      $ip=getenv("REMOTE_ADDR");
      fputs($fp, $logged_user_name);
      fputs($fp, "\n");
      fputs($fp, $ip);
      fputs($fp, "\n");

      fputs($fp,$description);
      fclose($fp);

      if (!move_uploaded_file($userfile, "$destination/$userfile_name"))
//      if (!copy($userfile, "$destination/$userfile_name"))
        $message="$mess[33] $userfile_name";
      else
        $message="$mess[36] <b>$userfile_name</b> $mess[37]";
    }
  }
  place_header($message);
  show_contents();
break;


//----------------------------------------------------------------------------
//      DEFAULT
//----------------------------------------------------------------------------

default;
require("include/${language}.php");
place_header($mess[42]);
show_contents();
break;
}
include($footerpage);
?>
