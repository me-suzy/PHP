<?
// File Upload Center
//
// Copyright (C) 2001, 2002 by Sergey Korostel    skorostel@mail.ru
//


function print_default()
{
global $mess, $font, $normalfontcolor, $selectedfontcolor, $homeurl, $languages;
global $uploadcentercaption,$requiremailconfirmation, $mailfunctionsenabled;
global $tablecolor,$bordercolor,$headercolor,$headerfontcolor;

echo "
   <p>&nbsp;</p>

   <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
     <tr>
       <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\"><font size=\"2\" face=\"$font\" color=\"$headerfontcolor\">$mess[99]</font></th>
     </tr>
     <tr>
         <td align=\"left\" bgcolor=\"$tablecolor\" valign=\"middle\">
         <form name=\"register\" action=\"register.php\" enctype=\"multipart/form-data\" method=\"post\" style=\"margin: 0\">
           <input type=\"hidden\" name=\"action\" value=\"register\">
           <table border=\"0\" width=\"100%\" cellpadding=\"4\">
             <tr>
               <td align=\"left\" width=\"40%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[57]</font></td>
               <td align=\"left\" width=\"60%\">
                 <input type=\"text\" name=\"user_name\">
               </td>
             </tr>
             <tr>
               <td align=\"left\" width=\"40%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[83]</font></td>
               <td align=\"left\" width=\"60%\">
                 <input type=\"password\" name=\"user_pass\">
               </td>
             </tr>
             <tr>
             <tr>
               <td align=\"left\" width=\"40%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[105]</font></td>
               <td align=\"left\" width=\"60%\">
                 <input type=\"password\" name=\"user_pass1\">
               </td>
             </tr>
             <tr>
               <td align=\"left\" width=\"40%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[88]
";
if ($requiremailconfirmation)
  echo " $mess[89]";
echo "
               </font></td>
               <td align=\"left\" width=\"60%\">
                 <input type=\"text\" name=\"user_email\">
               </td>
             </tr>";
if ($mailfunctionsenabled)
{
echo "
             <tr>
               <td align=\"left\" width=\"40%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[119]</font></td>
               <td align=\"left\" width=\"60%\">
                 <input type=\"checkbox\" name=\"digestcheckbox\">
               </td>
             </tr>";
}
echo "
             <tr>
               <td align=\"left\" width=\"100%\" colspan=\"2\">
                 <input type=\"submit\" value=\"$mess[58]\" />
               </td>
             </tr>
           </table>
         </form>
         </td>


     </tr>
     </table>

   <p>&nbsp;</p>

 </div>
<br>";
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
if (!isset($language))
  $language=$dft_language;
if ($language=="")
  $language=$dft_language;
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

if ($logged_user_name != "")
{
  if (check_is_user_session_active($logged_user_name))
  {
    // If user already entered, show user profile screen
    header("Location: login.php");
    exit;
    break;
  }
}


switch($action)
{

case "savelanguage";
  change_language();
  require("include/${language}.php");
  page_header($page_title);
  place_message($mess[58], $mess[41], "");
  show_menu(0, 'register.php');
  print_default();
break;


case "selectskin";
  change_skin();
  require("include/${language}.php");
  page_header($page_title);
  place_message($mess[58], $mess[96], "");
  show_menu(0, 'register.php');
  print_default();
break;


case "register";
  require("include/${language}.php");

  $userfilename = "$accounts_path/$user_name";
  // User name can contain only latin and number spases,
  // and space, "_", "-" symbols inside the name
  if (!eregi("^[a-z0-9][a-z0-9 _-]{0,10}[a-z0-9]$", $user_name))
  {
    page_header($page_title);
    place_message($mess[58], $mess[103]." ".sprintf($mess[101], "<a href=\"register.php\">", "</a>"), "");
    break;
  }
  if (file_exists($userfilename))
  {
    page_header($page_title);
    place_message($mess[58], sprintf($mess[104], $user_name)." ".sprintf($mess[101], "<a href=\"register.php\">", "</a>"), "");
    break;
  }
  if ($user_pass != $user_pass1)
  {
    page_header($page_title);
    place_message($mess[58], $mess[106]." ".sprintf($mess[101], "<a href=\"register.php\">", "</a>"), "");
    break;
  }
  if (!eregi( "^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}$", $user_email))
  {
    page_header($page_title);
    place_message($mess[58], $mess[107]." ".sprintf($mess[101], "<a href=\"register.php\">", "</a>"), "");
    break;
  }

  if ($requiremailconfirmation && $mailfunctionsenabled)
  {
    // Register user
    $enc_user_pass = md5($user_pass);
    srand((double)microtime()*1000000);
    $enc_logged_user_id = 0;
    $activationcode = rand() + 100;
  }
  else
  {
    // Register user & log on user
    $logged_user_name = $user_name;
    $enc_user_pass = md5($user_pass);
    srand((double)microtime()*1000000);
    $logged_user_id = md5(rand().microtime());
    $enc_logged_user_id = md5($logged_user_id);
    $user_status = $default_user_status;
    $user_temp_info = "";
    $user_wish_receive_digest = 0;
    $activationcode = 1;

    // Save user name & session ID
    setcookie("logged_user_name", $logged_user_name, time()+31536000, $cookiepath, $cookiedomain, $cookiesecure); // 1 year
    setcookie("logged_user_id", $logged_user_id, time()+31536000, $cookiepath, $cookiedomain, $cookiesecure); // 1 year
    if ($second_cookiedomain != "")
    {
      setcookie("logged_user_name", $logged_user_name, time()+31536000, $second_cookiepath, $second_cookiedomain, $cookiesecure); // 1 year
      setcookie("logged_user_id", $logged_user_id, time()+31536000, $second_cookiepath, $second_cookiedomain, $cookiesecure); // 1 year
    }

  }
  $user_status = $default_user_status;
  $user_temp_info = "";
  $user_wish_receive_digest = 0;
  $user_account_creation_time = time();
  if ($mailfunctionsenabled)
  {
    if (isset($digestcheckbox))
    {
      if ($digestcheckbox == "on")
        $user_wish_receive_digest = 1;
    }
  }
  // Send mail if needed
  if ($requiremailconfirmation && $mailfunctionsenabled)
  { // Send e-mail
    $body=sprintf($mess[111], $user_name, $activationcode);
    $from="Administrator <$admin_email>";
    if ($charsetencoding != "")
      $headers="Content-Type: text; charset=$charsetencoding\n";
    else
      $headers="Content-Type: text; charset=iso-8859-1\n";
    $headers.="From: $from\nX-Mailer: System33r";
    if (@mail($user_email,"Upload Center registration",$body,$headers))
    {
      // Create user account file
      save_user_profile($user_name);
      page_header($page_title);
      place_message($mess[58], $mess[108]." ".$mess[110], "");
    }
    else
    {
      page_header($page_title);
      place_message($mess[58], $mess[177]." ".$mess[178], "");
    }
  }
  else
  {
    // Create user account file
    save_user_profile($user_name);
    page_header($page_title);
    place_message($mess[58], $mess[108]." ".sprintf($mess[109], "<a href=\"index.php\">", "</a>"), "");
  }
break;

//----------------------------------------------------------------------------
//      DEFAULT
//----------------------------------------------------------------------------

default;
  require("include/${language}.php");
  page_header($page_title.": ".$mess[58]);
  place_message($mess[58], $mess[59], "");
  show_menu(0, 'register.php');
  print_default();
break;
}

include($footerpage);
?>
