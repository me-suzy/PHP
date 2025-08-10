<?
// File Upload Center
//
// Copyright (C) 2001, 2002 by Sergey Korostel    skorostel@mail.ru
//

function print_configure_page()
{
  global $mess, $font, $normalfontcolor, $selectedfontcolor, $homeurl, $languages;
  global $uploadcentercaption,$logged_user_name,$mailfunctionsenabled;
  global $tablecolor,$bordercolor,$headercolor,$headerfontcolor;
  global $mailinfopage;


echo "<p>&nbsp;</p>
   <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
     <tr>
       <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\"><font size=\"2\" face=\"$font\" color=\"$headerfontcolor\">$mess[165]:</font></th>
     </tr>";


  $handle=opendir("include");
  while ($filename = readdir($handle))
  {
//    if(eregi("\.txt$|\.htm$|\.html$|\.php$",$filename))
    if(eregi("\.txt$|\.htm$|\.html$",$filename))
    {
      if (!is_dir("include/$filename"))
      {
echo"
    <tr>
      <td align=\"left\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\"><a href=\"configure.php?action=editfile&filename=$filename\">$filename</a></font></td>
    </tr>";
      }
    }
  }
  closedir($handle);


echo"</table>";

}

function show_file_editor($filename)
{
  global $mess, $font, $normalfontcolor, $selectedfontcolor, $languages;
  global $tablecolor,$bordercolor,$headercolor,$headerfontcolor;

  if (!file_exists("include/$filename"))
    return;

  $max_caracters = filesize("include/$filename");
  $fp=@fopen("include/$filename","r");
  $filebody = fread($fp, $max_caracters);
  fclose($fp);

echo "<p>&nbsp;</p>
  <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
    <tr>
      <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\"><font size=\"2\" face=\"$font\" color=\"$headerfontcolor\">$mess[166]:</font></th>
    </tr>
    <tr>
      <td align=\"left\" bgColor=\"$tablecolor\">
        <form name=\"editfile\" action=\"configure.php\" enctype=\"multipart/form-data\" method=\"post\" style=\"margin: 0\">
          <input type=\"hidden\" name=\"action\" value=\"savefile\">
          <input type=\"hidden\" name=\"filename\" value=\"$filename\">
          <textarea name=\"filebody\" cols=\"82\" rows=\"20\">$filebody</textarea>
          <input type=\"submit\" value=\"$mess[168]\">
        </form>
      </td>
    </tr>
  </table>";
}

function show_default($message)
{
  global $logged_user_name, $mess;

  if ($logged_user_name != "")
  {
    if (check_is_user_session_active($logged_user_name))
    {
      // If user already entered, show logout screen
      if ($message == "")
        $message = $mess[164];
      place_message($mess[164], $logged_user_name.": ".$message, "<a href=\"login.php?action=logout\">".$mess[72]."</a> <a href=\"index.php\">".$mess[133]."</a>");
      show_menu(1, 'configure.php');
      print_configure_page();
      return;
    }
  }
  // Show default window
  if ($message == "")
    $message = $mess[42];
  place_message($mess[164], $message, "<a href=\"login.php\">".$mess[71]."</a> <a href=\"index.php\">".$mess[133]."</a>");
  show_menu(0, 'configure.php');
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
if (!isset($action))
  $action = "";
if (!isset($filename))
  $filename = "";
if (!isset($language))
  $language=$dft_language;
if ($language=="")
  $language=$dft_language;
require("include/${language}.php");
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

if (!isset($logged_user_name))
  $logged_user_name = "";

switch($action)
{

case "savelanguage";
  change_language();
  require("include/${language}.php");
  page_header($page_title);
  show_default($mess[41]);
break;


case "selectskin";
  change_skin();
  require("include/${language}.php");
  page_header($page_title);
  show_default($mess[96]);
break;


case "editfile";
  if ($logged_user_name != "")
  {
    if (check_is_user_session_active($logged_user_name))
    {
      require("include/${language}.php");
      page_header($page_title);
      place_message($mess[164], $logged_user_name.": ".$mess[166], "<a href=\"login.php?action=logout\">".$mess[72]."</a> <a href=\"index.php\">".$mess[133]."</a>");
      show_menu(1, 'configure.php');
      show_file_editor($filename);
    }
  }
break;

case "savefile";
  if ($logged_user_name != "")
  {
    if (check_is_user_session_active($logged_user_name))
    {
      require("include/${language}.php");
      page_header($page_title);
      if (!isset($filebody))
        break;
      $filebody = stripslashes($filebody);
      $fp=@fopen("include/$filename","w+");
      fwrite($fp, $filebody);
      fclose($fp);
      show_default(sprintf($mess[167], $filename));
    }
  }
break;

//----------------------------------------------------------------------------
//      DEFAULT
//----------------------------------------------------------------------------


default;
  require("include/${language}.php");
  page_header($page_title);
  show_default("");
  break;
}


include($footerpage);
?>
