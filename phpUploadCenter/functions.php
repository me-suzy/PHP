<?

function page_header($title)
{
  global $charsetencoding,$skins,$skinindex,$headerpage;

  $bodytag = $skins[$skinindex]["bodytag"];
  echo "<html><head><title>$title</title>";
  if ($charsetencoding != "")
    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=$charsetencoding\">";
  echo "<style type=\"text/css\"></style></head><body $bodytag>";
  if (file_exists($headerpage))
    include($headerpage);        // Include user portion of page
}

function place_message($welcomestring, $message, $link)
{
global $mess, $font, $normalfontcolor, $selectedfontcolor, $homeurl, $languages;
global $uploadcentercaption,$show_configuration_menu,$allow_choose_language;
global $tablecolor,$bordercolor,$headercolor,$headerfontcolor;

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
              <p><font face=\"$font\" size=\"1\" color=\"$headerfontcolor\">$welcomestring</font></p>
            </td>
          </tr>
        </table>
      </td>
    </tr>";

if ($message != "")
{
echo "<tr>
      <td align=\"left\" bgcolor=\"$tablecolor\" valign=\"middle\">
        <table border=\"0\" width=\"100%\">
          <tr>
            <th align=\"left\" valign=\"middle\" width=\"60%\">
              <font size=\"1\" face=\"$font\" color=\"$selectedfontcolor\">&raquo;</font>

              <font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">
              $message</font></th>
            <td align=\"right\" valign=\"middle\" width=\"40%\">
              <a href=\"$homeurl\">
                <img src=\"images/home.gif\" alt=\"$mess[25]\" border=\"0\" /></a>";

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

echo "         $link
            </td>
          </tr>
        </table>
      </td>
    </tr>";

}
echo "</table>";
}

function load_user_profile($username)
{
  global $accounts_path, $enc_user_pass, $enc_logged_user_id, $user_email;
  global $user_status, $activationcode, $user_temp_info, $user_wish_receive_digest;
  global $default_user_status,$user_account_creation_time;

  $enc_user_pass="";
  $enc_logged_user_id=0;
  $user_email="";
  $user_status=$default_user_status;
  $activationcode=1;
  $user_temp_info="";
  $user_wish_receive_digest=0;
  $user_account_creation_time=0;

  $userfilename = "$accounts_path/$username";
  if (!file_exists($userfilename))
    return;

  $fp=@fopen($userfilename,"r");
  if($fp)
  {
    if(!feof($fp))
      $enc_user_pass=rtrim(fgets($fp, 255));
    if(!feof($fp))
      $enc_logged_user_id=rtrim(fgets($fp, 255));
    if(!feof($fp))
      $user_email=rtrim(fgets($fp, 255));
    if(!feof($fp))
      $user_status=rtrim(fgets($fp, 255));
    if(!feof($fp))
      $activationcode=rtrim(fgets($fp, 255));
    if(!feof($fp))
      $user_temp_info=rtrim(fgets($fp, 255));
    if(!feof($fp))
      $user_wish_receive_digest=rtrim(fgets($fp, 255));
    if(!feof($fp))
      $user_account_creation_time=rtrim(fgets($fp, 255));
  }
  fclose($fp);
}


function save_user_profile($username)
{
  global $accounts_path, $enc_user_pass, $enc_logged_user_id, $user_email;
  global $user_status, $activationcode, $user_temp_info, $user_wish_receive_digest;
  global $user_account_creation_time;

  $userfilename = "$accounts_path/$username";
  $fp = fopen($userfilename, "w+"); // File named as User Name
  fwrite($fp, $enc_user_pass);      // 1st line: Encrypted user password
  fwrite($fp, "\n");
  fwrite($fp, $enc_logged_user_id); // 2nd line: Encrypted user session ID, 0 - if user logged out
  fwrite($fp, "\n");
  fwrite($fp, $user_email);         // 3rd line: User E-Mail address
  fwrite($fp, "\n");
  fwrite($fp, $user_status);    // 4 line: account status: 0 - Administrator, 1 - Power User, 2 - Normal User, 3 - Viewer (view only), 4 - Uploader (upload only)
  fwrite($fp, "\n");
  fwrite($fp, $activationcode); // 5 line: 1 - if account active, 0 - if disabled, other value - activation code
  fwrite($fp, "\n");
  fwrite($fp, $user_temp_info); // 6 line: any temporary information
  fwrite($fp, "\n");
  fwrite($fp, $user_wish_receive_digest);  // 7 line: User wish to receive files digest via e-mail
  fwrite($fp, "\n");
  fwrite($fp, $user_account_creation_time);  // 8 line: The time when user account created
  fwrite($fp, "\n");
  fclose($fp);
}

function check_is_user_session_active($username)
{
  global $accounts_path,$logged_user_id,$enc_logged_user_id;
  // Check user session id
  load_user_profile($username);
  return (md5($logged_user_id) == $enc_logged_user_id);
}

function check_user_password($username, $password)
{
  global $accounts_path,$enc_user_pass;
  // Check user session id
  $userfilename = "$accounts_path/$username";
  load_user_profile($username);
  return (md5($password) == $enc_user_pass);
}

function change_language()
{
  global $HTTP_GET_VARS, $language, $languages, $timeoffset, $GMToffset;
  global $cookiepath,$cookiedomain,$second_cookiepath, $second_cookiedomain;
  global $cookiesecure;

  $language=$HTTP_GET_VARS["language"];
  setcookie("language",$language,time()+31536000,$cookiepath,$cookiedomain, $cookiesecure);  // 1 year
  if ($second_cookiedomain != "")
    setcookie("language",$language,time()+31536000, $second_cookiepath, $second_cookiedomain, $cookiesecure); // 1 year
  $timeoffset = -$GMToffset + $languages[$language]["TimeZone"];
}

function change_skin()
{
  global $HTTP_GET_VARS, $skinindex, $skins, $bordercolor, $headercolor, $tablecolor;
  global $lightcolor, $headerfontcolor, $normalfontcolor, $selectedfontcolor;
  global $cookiepath,$cookiedomain,$second_cookiepath, $second_cookiedomain;
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
}

function show_menu($isuserloggedin, $scriptname)
{
  global $show_configuration_menu;
  if ($show_configuration_menu)
  {
    include("dynmenu.php");
    build_menu($isuserloggedin, $scriptname);
  }
}

function generate_password()
{
  // Generate new password
  $consonants="BCDFGHJKLMNPQRSTWXVZ";
  $vowels="AEIOUY";
  mt_srand((double)microtime()*1000000);
  $leng=mt_rand(3,5);
  $newpass="";
  for ($i = 0; $i < $leng; $i++)
  {
    mt_srand((double)microtime()*1000000);
    $newpass.=$consonants[mt_rand(0,19)].$vowels[mt_rand(0,5)];
  }
  return $newpass;
}

function userslist($order = "name")
{
  global $accounts_path, $user_account_creation_time, $user_status, $activationcode, $user_wish_receive_digest, $user_email;
  $userslist = "";
  // Browse each user
  $handle=opendir($accounts_path);
  while ($filename = readdir($handle))
  {
    if (substr($filename,0,1) != ".")
    {
      if (!is_dir("$accounts_path/$filename"))
      {
        if ($order == "name")
          $userslist[$filename] = $filename;
        else
        {
          if (($order == "uploaded") || ($order == "downloaded") || ($order == "emailed") || ($order == "access"))
          {
            list($files_uploaded, $files_downloaded, $files_emailed, $last_acess_time) = load_userstat($filename);
            if ($order == "uploaded")
              $userslist[$filename] = $files_uploaded;
            if ($order == "downloaded")
              $userslist[$filename] = $files_downloaded;
            if ($order == "emailed")
              $userslist[$filename] = $files_emailed;
            if ($order == "access")
              $userslist[$filename] = $last_acess_time;
          }
          else
          {
            load_user_profile($filename);
            if ($order == "date")
              $userslist[$filename] = $user_account_creation_time;
            if ($order == "status")
              $userslist[$filename] = $user_status;
            if ($order == "activestatus")
              $userslist[$filename] = $activationcode;
            if ($order == "receivedigest")
              $userslist[$filename] = $user_wish_receive_digest;
            if ($order == "email")
              $userslist[$filename] = $user_email;
          }
        }
      }
    }
  }
  closedir($handle);
  if (($order == "uploaded") || ($order == "downloaded") || ($order == "emailed"))
    arsort($userslist);
  else
    asort($userslist);
  return $userslist;
}

function load_userstat($username)
{
  global $accounts_stat_path;
  $files_uploaded = 0;
  $files_downloaded = 0;
  $files_emailed = 0;
  $last_acess_time = 0;

  $userfilename = "$accounts_stat_path/$username.stat";
  if (file_exists($userfilename))
  {
    $fp=@fopen($userfilename,"r");
    if($fp)
    {
      if(!feof($fp))
        $files_uploaded=rtrim(fgets($fp, 255));
      if(!feof($fp))
        $files_downloaded=rtrim(fgets($fp, 255));
      if(!feof($fp))
        $files_emailed=rtrim(fgets($fp, 255));
      if(!feof($fp))
        $last_acess_time=rtrim(fgets($fp, 255));
    }
    fclose($fp);
  }
  return array($files_uploaded, $files_downloaded, $files_emailed, $last_acess_time);
}

function save_userstat($username, $files_uploaded, $files_downloaded, $files_emailed, $last_acess_time)
{
  global $accounts_stat_path;

  $userfilename = "$accounts_stat_path/$username.stat";
  $fp=fopen($userfilename,"w+");
  if($fp)
  {
    fwrite($fp, $files_uploaded);
    fwrite($fp, "\n");
    fwrite($fp, $files_downloaded);
    fwrite($fp, "\n");
    fwrite($fp, $files_emailed);
    fwrite($fp, "\n");
    fwrite($fp, $last_acess_time);
    fwrite($fp, "\n");
  }
  fclose($fp);
}

class MIME_MAIL {
  var $attachments = array();
  var $from = "";
  var $subject = "";
  var $body = "";
  var $charset = "";

  function MIME_MAIL($from = "", $subject = "", $body = "", $charset = "")
  {
    $this->from = $from;
    $this->subject = $subject;
    $this->body = $body;
    $this->charset = $charset;
  }

  function attachment($name = "", $contents = "",
                      $type = "application/octet-stream", $encoding = "base64")
  {
    $this->attachments[] = array("filename" => $name,
                                 "type" => $type,
                                 "encoding" => $encoding,
                                 "data" => $contents);
  }

  function _build()
  {
    mt_srand((double)microtime()*1000000);
    $boundary = '--b'.md5(uniqid(mt_rand())) . getmypid();

    if ($this->from != "")
      $ret = "From: " . $this->from . "\n";
    else
      $ret = "";

    $ret .= "MIME-Version: 1.0\n";
    $ret .= "Content-Type: multipart/mixed; ";
    $ret .= "boundary=\"$boundary\"\n\n";


    $ret .= "This is a MIME encoded message. \n\n";
    $ret .= "--$boundary\n";

    $ret .= "Content-Type: text/plain";
    if ($this->charset != "")
      $ret .= "; charset=$this->charset";

    $ret .= "\n";
    $ret .= "Content-Transfer-Encoding: 8bit\n\n";
    $ret .= $this->body . "\n--$boundary";

    foreach($this->attachments as $attachment)
    {
      $attachment["data"] = base64_encode($attachment["data"]);
      $attachment["data"] = chunk_split($attachment["data"]);
      $data =
        "Content-Type: $attachment[type]";
        if ($attachment["filename"] != "")
          $data .= "; name = \"$attachment[filename]\"";
        else
          $data .= "";
        $data .= "\n" .
        "Content-Transfer-Encoding: $attachment[encoding]" .
        "\n\n$attachment[data]\n";
      $ret .= "\n$data\n--$boundary";
    }
    $ret .= "--\n";
    return($ret);
  }

  function send($to)
  {
    return @mail($to, $this->subject, "", $this->_build());
  }


  function add_html($html_message)
  {
    $this->attachment("", $html_message, "text/html");
  }

  function mime_type($filename)
  {
    if(eregi("\.zip$",$filename)) $mimet="application/zip";
    else if (eregi("\.gtar$",$filename)) $mimet="application/x-gtar";
    else if (eregi("\.tar$",$filename)) $mimet="application/x-tar";
    else if (eregi("\.gif$",$filename)) $mimet="image/gif";
    else if (eregi("\.jpeg$",$filename)) $mimet="image/jpeg";
    else if (eregi("\.jpg$",$filename)) $mimet="image/jpeg";
    else if (eregi("\.tiff$",$filename)) $mimet="image/tiff";
    else if (eregi("\.tif$",$filename)) $mimet="image/tiff";
    else if (eregi("\.rtf$",$filename)) $mimet="application/rtf";
    else if (eregi("\.wav$",$filename)) $mimet="audio/x-wav";
    else if (eregi("\.html$",$filename)) $mimet="text/html";
    else if (eregi("\.htm$",$filename)) $mimet="text/html";
    else if (eregi("\.txt$",$filename)) $mimet="text/plain";
    else if (eregi("\.pdf$",$filename)) $mimet="application/pdf";
    else if (eregi("\.eps$",$filename)) $mimet="application/postscript";
    else if (eregi("\.ps$",$filename)) $mimet="application/postscript";
    else if (eregi("\.avi$",$filename)) $mimet="video/x-msvideo";
    else if (eregi("\.mpeg$",$filename)) $mimet="video/mpeg";
    else if (eregi("\.mpg$",$filename)) $mimet="video/mpeg";
    else if (eregi("\.qt$",$filename)) $mimet="video/quicktime";
    else if (eregi("\.mov$",$filename)) $mimet="video/quicktime";
    else if (eregi("\.doc$",$filename)) $mimet="application/msword";
    else $mimet="application/octet-stream";
    return $mimet;
  }

  function add_file($filename)
  {
    if (!file_exists($filename))
      return;
    $fp=@fopen($filename,"rb");
    $contents = fread($fp, filesize($filename));
    fclose($fp);
    $this->attachment(basename($filename), $contents, $this->mime_type($filename));
  }

} // Enc of class MIME_MAIL

?>