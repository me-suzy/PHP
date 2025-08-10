<?
// File Upload Center
//
// Copyright (C) 2001, 2002 by Sergey Korostel    skorostel@mail.ru
//


function print_default()
{
global $mess, $font, $normalfontcolor, $selectedfontcolor, $languages;
global $uploadcentercaption,$requiremailconfirmation;
global $tablecolor,$bordercolor,$headercolor,$headerfontcolor;

echo "
   <p>&nbsp;</p>

   <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
     <tr>
       <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\"><font size=\"2\" face=\"$font\" color=\"$headerfontcolor\">$mess[98]</font></th>
     </tr>
     <tr>
         <td align=\"left\" bgcolor=\"$tablecolor\" valign=\"middle\">
         <form name=\"userlogin\" action=\"login.php\" enctype=\"multipart/form-data\" method=\"post\" style=\"margin: 0\">
           <input type=\"hidden\" name=\"action\" value=\"userlogin\">
           <table border=\"0\" width=\"100%\" cellpadding=\"4\">
             <tr>
               <td align=\"left\" width=\"15%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[57]</font></td>
               <td align=\"left\" width=\"85%\">
                 <input type=\"text\" name=\"user_name\">
               </td>
             </tr>
             <tr>
               <td align=\"left\" width=\"15%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[83]</font></td>
               <td align=\"left\" width=\"85%\">
                 <input type=\"password\" name=\"user_pass\">
               </td>
             </tr>
             <tr>
               <td align=\"left\" width=\"100%\" colspan=\"2\">
                 <input type=\"submit\" name=\"Submit\" value=\"$mess[73]\" />
               </td>
             </tr>
           </table>
         </form>
         </td>
     </tr>
     </table>
   <br>
   <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
     <tr>
       <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\"><font size=\"2\" face=\"$font\" color=\"$headerfontcolor\">$mess[99]</font></th>
     </tr>
     <tr>
       <th align=\"left\" bgcolor=\"$tablecolor\" valign=\"middle\">
         <font size=\"1\" color=\"$normalfontcolor\" face=\"$font\">
         <a href=\"register.php\">$mess[58]</a>
       </font></th>
     </tr>
     </table>
   <br>
   <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
     <tr>
       <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\"><font size=\"2\" face=\"$font\" color=\"$headerfontcolor\">$mess[100]</font></th>
     </tr>
     <tr>
         <td align=\"left\" bgcolor=\"$tablecolor\" valign=\"middle\">
         <form name=\"logonsystem\" action=\"login.php\" enctype=\"multipart/form-data\" method=\"post\" style=\"margin: 0\">
           <input type=\"hidden\" name=\"action\" value=\"sendpassword\">
           <table border=\"0\" width=\"100%\" cellpadding=\"4\">
             <tr>
               <td align=\"left\" width=\"60%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[57]</font></td>
               <td align=\"left\" width=\"40%\">
                 <input type=\"text\" name=\"user_name\">
               </td>
             </tr>
             <tr>
               <td align=\"left\" width=\"60%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[91]</font></td>
               <td align=\"left\" width=\"40%\">
                 <input type=\"text\" name=\"typed_email\">
               </td>
             </tr>
             <tr>
               <td align=\"left\" width=\"100%\" colspan=\"2\">
                 <input type=\"submit\" value=\"$mess[67]\" />
               </td>
             </tr>
           </table>
         </form>
         </td>
     </tr>
     </table>
 </div>
<br>";
}


function print_user_profile()
{
global $mess, $font, $normalfontcolor, $selectedfontcolor, $languages;
global $uploadcentercaption,$logged_user_name,$mailfunctionsenabled;
global $tablecolor,$bordercolor,$headercolor,$headerfontcolor;
global $user_wish_receive_digest, $user_email, $user_account_creation_time;


  $account_date = getdate($user_account_creation_time);
  $month = $account_date['mon'];
  $mday = $account_date['mday'];
  $year = $account_date['year'];
  list($files_uploaded, $files_downloaded, $files_emailed) = load_userstat($logged_user_name);


echo "
   <p>&nbsp;</p>
   <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
     <tr>
       <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\"><font size=\"2\" face=\"$font\" color=\"$headerfontcolor\">$mess[135]</font></th>
     </tr>
     <tr>
         <td align=\"left\" bgcolor=\"$tablecolor\" valign=\"middle\">
           <table border=\"0\" width=\"100%\" cellpadding=\"4\">
             <tr>
               <td align=\"left\" width=\"30%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[136]</font></td>
               <td align=\"left\" width=\"70%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">
               $mess[$month] $mday, $year
               </font></td>
             </tr>";

       if ($files_uploaded)
         echo "
             <tr>
               <td align=\"left\" width=\"30%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[151]:</font></td>
               <td align=\"left\" width=\"70%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">
               $files_uploaded
               </font></td>
             </tr>";

       if ($files_downloaded)
         echo "
             <tr>
               <td align=\"left\" width=\"30%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[152]:</font></td>
               <td align=\"left\" width=\"70%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">
               $files_downloaded
               </font></td>
             </tr>";
       if ($files_emailed)
         echo "
             <tr>
               <td align=\"left\" width=\"30%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[153]:</font></td>
               <td align=\"left\" width=\"70%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">
               $files_emailed
               </font></td>
             </tr>";


    echo " </table>
         </td>
     </tr>
     </table>

   <br>

   <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
     <tr>
       <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\"><font size=\"2\" face=\"$font\" color=\"$headerfontcolor\">$mess[126]</font></th>
     </tr>
     <tr>
         <td align=\"left\" bgcolor=\"$tablecolor\" valign=\"middle\">
         <form name=\"userprofile\" action=\"login.php\" enctype=\"multipart/form-data\" method=\"post\" style=\"margin: 0\">
           <input type=\"hidden\" name=\"action\" value=\"customizeprofile\">
           <table border=\"0\" width=\"100%\" cellpadding=\"4\">
             <tr>
               <td align=\"left\" width=\"30%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[88]</font></td>
               <td align=\"left\" width=\"70%\">
                 <input type=\"text\" name=\"typed_email\" value=\"$user_email\">
               </td>
             </tr>";

if ($mailfunctionsenabled)
{
echo "
             <tr>
               <td align=\"left\" width=\"30%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[119]</font></td>
               <td align=\"left\" width=\"70%\">";
if ($user_wish_receive_digest)
  echo "         <input type=\"checkbox\" name=\"digestcheckbox\" checked>";
else
  echo "         <input type=\"checkbox\" name=\"digestcheckbox\">";
echo "         </td>
             </tr>";
}
echo "
             <tr>
               <td align=\"left\" width=\"100%\" colspan=\"2\">
                 <input type=\"submit\" name=\"Submit\" value=\"$mess[127]\" />
               </td>
             </tr>
           </table>
         </form>
         </td>
     </tr>
     </table>
   <br>
   <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
     <tr>
       <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\"><font size=\"2\" face=\"$font\" color=\"$headerfontcolor\">$mess[120]</font></th>
     </tr>
     <tr>
         <td align=\"left\" bgcolor=\"$tablecolor\" valign=\"middle\">
         <form name=\"changepass\" action=\"login.php\" enctype=\"multipart/form-data\" method=\"post\" style=\"margin: 0\">
           <input type=\"hidden\" name=\"action\" value=\"changepass\">
           <table border=\"0\" width=\"100%\" cellpadding=\"4\">
             <tr>
               <td align=\"left\" width=\"15%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[121]</font></td>
               <td align=\"left\" width=\"85%\">
                 <input type=\"password\" name=\"old_pass\">
               </td>
             </tr>
             <tr>
               <td align=\"left\" width=\"15%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[83]</font></td>
               <td align=\"left\" width=\"85%\">
                 <input type=\"password\" name=\"new_pass\">
               </td>
             </tr>
             <tr>
               <td align=\"left\" width=\"100%\" colspan=\"2\">
                 <input type=\"submit\" name=\"Submit\" value=\"$mess[73]\" />
               </td>
             </tr>
           </table>
         </form>
         </td>
     </tr>
     </table>
<br>";
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
        $message = $mess[82];
      place_message($mess[71], $logged_user_name.": ".$message, "<a href=\"login.php?action=logout\">".$mess[72]."</a> <a href=\"index.php\">".$mess[133]."</a>");
      show_menu(1, 'login.php');
      print_user_profile();
      return;
    }
  }
  // Show default window
  if ($message == "")
    $message = $mess[42];
  place_message($mess[71], $message, "");
  show_menu(0, 'login.php');
  print_default();
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


//----------------------------------------------------------------------------
//      User Login
//----------------------------------------------------------------------------

case "userlogin";
  require("include/${language}.php");

  // åñëè ïîëüçîâàòåëü ïðèø¸ë ñ ëþáîé ñòðàíèöû íàøåãî ñàéòà òî îí âðîäå íàø...
  // Ïåðåìåííàÿ $HTTP_REFERER âñåãäà äîñòóïíà ïî óìîë÷àíèþ
  // è ñîäåðæèò ïîëíûé àäðåñ ññûëàþùåéñÿ ñòðàíèöû...
  // ôóíêöèÿ eregi() ïðîâåðÿåò, íà÷èíàåòñÿ ëè àäðåñ ññûëàþùåéñÿ ñòðàíèöû
  // ñî çíà÷åíèÿ â ïåðåìåííîé $SERVER_ROOT
//  if(eregi("^$installurl",$HTTP_REFERER))
//  {
    // äàííûå áûëè îòïðàâëåíû ôîðìîé?
    if($Submit)
    {
      $userfilename = "$accounts_path/$user_name";
      // Check the user name
      if (file_exists($userfilename))
      {
        // Check the password
        if (check_user_password($user_name, $user_pass))
        {
          if ($activationcode == 1)
          {
            $logged_user_name = $user_name;
            // Generate & store new session id
            srand((double)microtime()*1000000);
            $logged_user_id = md5(rand().microtime());
            $enc_logged_user_id = md5($logged_user_id);

            // remember user name & session id
            setcookie("logged_user_name", $logged_user_name, time()+31536000, $cookiepath, $cookiedomain, $cookiesecure); // 1 year
            setcookie("logged_user_id", $logged_user_id, time()+31536000, $cookiepath, $cookiedomain, $cookiesecure); // 1 year
            if ($second_cookiedomain != "")
            {
              setcookie("logged_user_name", $logged_user_name, time()+31536000, $second_cookiepath, $second_cookiedomain, $cookiesecure); // 1 year
              setcookie("logged_user_id", $logged_user_id, time()+31536000, $second_cookiepath, $second_cookiedomain, $cookiesecure); // 1 year
            }
            // Write new session ID
            save_user_profile($logged_user_name);

            // and go to the propertly page...
            header("Location: index.php");
            exit;
          }
          else
          {
            header("Location: activate.php");
            exit;
          }
        }
      }
    }
//  }
  $logged_user_name = "";
  // Show invalid password message
  page_header($page_title.": ".$mess[71]);
  place_message($mess[80], $mess[80]." ".sprintf($mess[101], "<a href=\"login.php\">", "</a>"), "");
  show_menu(0, 'login.php');
break;


case "logout";
  require("include/${language}.php");
  // Delete cookie
  setcookie("logged_user_name", "", time()-86400, $cookiepath, $cookiedomain, $cookiesecure); // 1 day ago
  setcookie("logged_user_id", "", time()-86400, $cookiepath, $cookiedomain, $cookiesecure); // 1 day ago
  if ($second_cookiedomain != "")
  {
    setcookie("logged_user_name", "", time()-86400, $second_cookiepath, $second_cookiedomain, $cookiesecure); // 1 day ago
    setcookie("logged_user_id", "", time()-86400, $second_cookiepath, $second_cookiedomain, $cookiesecure); // 1 day ago
  }
  // Mark that user logged out
  if ($logged_user_name != "")
  {
    load_user_profile($logged_user_name);
    $enc_logged_user_id = 0;
    save_user_profile($logged_user_name);
  }
  // Show succesfully logout message
  page_header($page_title.": ".$mess[71]);
  place_message("", $mess[102], "<a href=\"login.php\">".$mess[71]."</a>");
  show_menu(0, 'login.php');
break;

case "sendpassword";
  require("include/${language}.php");
  page_header($page_title);

  $userfilename = "$accounts_path/$user_name";
  if (!file_exists($userfilename))
  {
    place_message($mess[58], sprintf($mess[122], $user_name)." ".sprintf($mess[101], "<a href=\"login.php\">", "</a>"), "");
    show_menu(0, 'login.php');
    break;
  }

  load_user_profile($user_name);
  if (!isset($typed_email))
    $typed_email = "";
  if ($user_email != $typed_email)
  {
    place_message($mess[58], $mess[123]." ".sprintf($mess[116], "<a href=\"login.php\">", "</a>"), "");
    break;
  }

  // Generate new password
  $user_pass = generate_password();
  $enc_user_pass = md5($user_pass);
  // Send e-mail
  $body=sprintf($mess[125], $user_pass);
  $from="Administrator <$admin_email>";
  if ($charsetencoding != "")
    $headers="Content-Type: text; charset=$charsetencoding\n";
  else
    $headers="Content-Type: text; charset=iso-8859-1\n";
  $headers.="From: $from\nX-Mailer: System33r";
  if (@mail($user_email,"Upload Center new password",$body,$headers))
  {
    // Save user profile
    save_user_profile($user_name);
    place_message($mess[58], $mess[124], "");
  }
  else
  {
    place_message($mess[58], $mess[177]." ".$mess[179], "");
  }
break;

case "customizeprofile";
  require("include/${language}.php");

  page_header($page_title.": ".$mess[71]);

  if ($logged_user_name != "")
  {
    if (check_is_user_session_active($logged_user_name))
    {
      $user_wish_receive_digest = 0;
      if (isset($digestcheckbox))
      {
        if ($digestcheckbox == "on")
          $user_wish_receive_digest = 1;
      }
      $user_temp_info = "";
      if ($typed_email != $user_email)
      {
        if (eregi( "^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,4}$", $user_email))
        {
          if (($mailfunctionsenabled) && ($requiremailconfirmation))
          {
            srand((double)microtime()*1000000);
            $activationcode = rand() + 100;
            $user_temp_info = "m:".$activationcode.":".$typed_email;
          }
          else
            $user_email = $typed_email;
        }
        else
        {
          show_default($mess[107]);
          break;
        }
      }
      save_user_profile($logged_user_name);
      if ($user_temp_info == "")
        show_default($mess[128]);
      else
      {
        // Send confirmation e-mail
        $body=sprintf($mess[144], $logged_user_name, $activationcode);
        $from="Administrator <$admin_email>";
        if ($charsetencoding != "")
          $headers="Content-Type: text; charset=$charsetencoding\n";
        else
          $headers="Content-Type: text; charset=iso-8859-1\n";
        $headers.="From: $from\nX-Mailer: System33r";
        if (@mail($typed_email,"Confirm your new E-Mail address - Upload Center",$body,$headers))
        {
          show_default($mess[128]." ".$mess[143]);
        }
        else
        {
          show_default($mess[177]." ".$mess[179]);
        }
      }
    }
  }
break;

case "changepass";
  require("include/${language}.php");

  page_header($page_title.": ".$mess[71]);

  if ($logged_user_name != "")
  {
    if (check_is_user_session_active($logged_user_name))
    {
      if (md5($old_pass) == $enc_user_pass)
      {
        if ($new_pass != "")
        {
          $enc_user_pass = md5($new_pass);
          save_user_profile($logged_user_name);
          show_default($mess[129]);
        }
        else
          show_default($mess[131]);
      }
      else
        show_default($mess[130]);
    }
  }
break;

//----------------------------------------------------------------------------
//      DEFAULT
//----------------------------------------------------------------------------


default;
  require("include/${language}.php");
  page_header($page_title.": ".$mess[71]);
  show_default("");
  break;
}


include($footerpage);
?>
