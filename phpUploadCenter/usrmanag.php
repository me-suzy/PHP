<?
// File Upload Center
//
// Copyright (C) 2001, 2002 by Sergey Korostel    skorostel@mail.ru
//

function print_user_profile()
{
  global $mess, $font, $normalfontcolor, $selectedfontcolor, $homeurl, $languages;
  global $uploadcentercaption,$logged_user_name,$mailfunctionsenabled;
  global $tablecolor,$bordercolor,$headercolor,$headerfontcolor, $order, $letter, $accpage;
  global $user_wish_receive_digest, $user_email, $user_account_creation_time, $user_status, $activationcode;

  $accountsperpage = 20;

  $users = userslist($order);
  $usercount = count($users);

echo "<p>&nbsp;</p>
  <table cellSpacing=\"1\" cellPadding=\"4\" width=\"85%\" bgColor=\"$bordercolor\" border=\"0\">
    <tr>
      <td align=\"center\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\"><a href=\"usrmanag.php\">$mess[57]</a></font></td>
      <td align=\"center\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\"><a href=\"usrmanag.php?order=date\">$mess[154]</a></font></td>
      <td align=\"center\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\"><a href=\"usrmanag.php?order=access\">$mess[155]</a></font></td>
      <td align=\"center\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\"><a href=\"usrmanag.php?order=status\">$mess[156]</a></font></td>
      <td align=\"center\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\"><a href=\"usrmanag.php?order=activestatus\">$mess[157]</a></font></td>
      <td rowspan=\"2\" align=\"right\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[160] $usercount $mess[161]</font></td>
    </tr>
    <tr>
      <td align=\"center\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\"><a href=\"usrmanag.php?order=receivedigest\">$mess[158]</a></font></td>
      <td align=\"center\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\"><a href=\"usrmanag.php?order=email\">$mess[159]</a></font></td>

      <td align=\"center\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\"><a href=\"usrmanag.php?order=uploaded\">$mess[151]</a></font></td>
      <td align=\"center\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\"><a href=\"usrmanag.php?order=downloaded\">$mess[152]</a></font></td>
      <td align=\"center\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\"><a href=\"usrmanag.php?order=emailed\">$mess[153]</a></font></td>
    </tr>
  </table>";

if ($usercount > $accountsperpage)
{
echo "<br>
  <table cellSpacing=\"1\" cellPadding=\"4\" width=\"85%\" bgColor=\"$bordercolor\" border=\"0\">
    <tr>
      <td align=\"center\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">";

      if (($order == "name") || ($order == "email"))
      {
        echo "<a href=\"usrmanag.php?order=$order&letter=*\">*</a> ";
        for ($i = 65; $i <= 90; $i++)
        {
          $lett = chr($i);
          echo "<a href=\"usrmanag.php?order=$order&letter=$lett\">$lett</a> ";
        }
        echo "<a href=\"usrmanag.php?order=$order&letter=All\">$mess[142]</a> ";
      }
      else
      {
        for ($i = 1; $i < $usercount/$accountsperpage + 1; $i++)
        {
          echo "<a href=\"usrmanag.php?order=$order&accpage=$i\">$i</a> ";
        }
        echo "<a href=\"usrmanag.php?order=$order&accpage=0\">$mess[142]</a> ";
      }
echo " </font></td>
    </tr>
  </table>";
}

  $shownaccounts = 0;
  $currentaccount = 0;
  while (list($filename,$filed) = each($users))
  {
    $currentaccount++;
    if ($usercount > $accountsperpage)
      if (($order == "name") || ($order == "email"))
      {
        if ($letter != "All")
        {
          $let = ucfirst(substr($filed,0,1));
          if ($letter == '*')
          {
            if (eregi("[A-Z]", $let))
              continue;
          }
          else
            if ($let != $letter)
              continue;
        }
      }
      else
        if ($accpage != 0)
          if ($accpage-1 != (int)($currentaccount/$accountsperpage))
            continue;

    $shownaccounts++;
    load_user_profile($filename);

    $account_date = getdate($user_account_creation_time);
    $month = $account_date['mon'];
    $mday = $account_date['mday'];
    $year = $account_date['year'];
    list($files_uploaded, $files_downloaded, $files_emailed, $last_acess_time) = load_userstat($filename);
    if ($last_acess_time == 0)
      $last_acess_time = $user_account_creation_time;
    $access_date = getdate($last_acess_time);
    $accessmonth = $access_date['mon'];
    $accessmday = $access_date['mday'];
    $accessyear = $access_date['year'];

echo "<br>
   <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
     <tr>
       <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\"><font size=\"2\" face=\"$font\" color=\"$headerfontcolor\">$currentaccount. $filename</font></th>
     </tr>
     <tr>
         <td align=\"left\" bgcolor=\"$tablecolor\" valign=\"middle\">
         <form name=\"useraccount\" action=\"usrmanag.php\" enctype=\"multipart/form-data\" method=\"post\" style=\"margin: 0\">
           <input type=\"hidden\" name=\"action\" value=\"customizeaccount\">
           <input type=\"hidden\" name=\"order\" value=\"$order\">
           <input type=\"hidden\" name=\"letter\" value=\"$letter\">
           <input type=\"hidden\" name=\"accpage\" value=\"$accpage\">
           <input type=\"hidden\" name=\"username\" value=\"$filename\">
           <table border=\"0\" width=\"100%\" cellpadding=\"4\">
             <tr>
               <td align=\"left\" width=\"30%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[154]/$mess[155]</font></td>
               <td align=\"left\" width=\"70%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">
               $mess[$month] $mday, $year / $mess[$accessmonth] $accessmday, $accessyear
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


     echo "  <tr>
               <td align=\"left\" width=\"30%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">E-mail</font></td>
               <td align=\"left\" width=\"70%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">
                 <input type=\"text\" name=\"typed_email\" value=\"$user_email\">
               </font></td>
             </tr>
             <tr>
               <td align=\"left\" width=\"30%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[156]</font></td>
               <td align=\"left\" width=\"70%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">
                 <select name=\"typed_status\" size=\"1\">";

                 if ($user_status == 0)
                   echo "<option value=\"0\" selected>$mess[77]</option>";
                 else
                   echo "<option value=\"0\">$mess[77]</option>";
                 if ($user_status == 1)
                   echo "<option value=\"1\" selected>$mess[76]</option>";
                 else
                   echo "<option value=\"1\">$mess[76]</option>";
                 if ($user_status == 2)
                   echo "<option value=\"2\" selected>$mess[75]</option>";
                 else
                   echo "<option value=\"2\">$mess[75]</option>";
                 if ($user_status == 3)
                   echo "<option value=\"3\" selected>$mess[138]</option>";
                 else
                   echo "<option value=\"3\">$mess[138]</option>";
                 if ($user_status == 4)
                   echo "<option value=\"4\" selected>$mess[139]</option>";
                 else
                   echo "<option value=\"4\">$mess[139]</option>";

         echo "  </select>
               </font></td>
             </tr>
             <tr>
               <td align=\"left\" width=\"30%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[157]</font></td>
               <td align=\"left\" width=\"70%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">
                 <select name=\"typed_activestatus\" size=\"1\">";

                 if ($activationcode == 0)
                   echo "<option value=\"0\" selected>$mess[175]</option>";
                 else
                   echo "<option value=\"0\">$mess[175]</option>";
                 if ($activationcode == 1)
                   echo "<option value=\"1\" selected>$mess[174]</option>";
                 else
                   echo "<option value=\"1\">$mess[174]</option>";
                 if ($activationcode > 1)
                   echo "<option value=\"2\" selected>$mess[176]</option>";
                 else
                   echo "<option value=\"2\">$mess[176]</option>";

         echo "  </select>
               </font></td>
             </tr>
             <tr>
               <td align=\"left\" width=\"30%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[158]</font></td>
               <td align=\"left\" width=\"70%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">
                 <select name=\"typed_digest\" size=\"1\">";

                 if ($user_wish_receive_digest == 0)
                   echo "<option value=\"0\" selected>$mess[173]</option>";
                 else
                   echo "<option value=\"0\">$mess[173]</option>";
                 if ($user_wish_receive_digest == 1)
                   echo "<option value=\"1\" selected>$mess[172]</option>";
                 else
                   echo "<option value=\"1\">$mess[172]</option>";

         echo "  </select>
               </font></td>
             </tr>
             <tr>
               <td align=\"left\" width=\"30%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[162]</font></td>
               <td align=\"left\" width=\"70%\">
                 <input type=\"checkbox\" name=\"deleteaccountcheckbox\">
               </td>
             </tr>
             <tr>
               <td align=\"left\" width=\"100%\" colspan=\"2\">
                 <input type=\"submit\" name=\"Submit\" value=\"$mess[127]\" />
               </td>
             </tr>
           </table>
         </form>
         </td>
     </tr>
     </table>";
  }

echo "<br>
  <table cellSpacing=\"1\" cellPadding=\"4\" width=\"85%\" bgColor=\"$bordercolor\" border=\"0\">
    <tr>
      <td align=\"right\" bgColor=\"$tablecolor\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">";
  echo sprintf($mess[163], $shownaccounts, $usercount);
echo "</font></td>
    </tr>
  </table>";
}

function change_account_data()
{
  global $username, $typed_email, $typed_status, $typed_activestatus, $typed_digest, $deleteaccountcheckbox;
  global $accounts_path, $enc_user_pass, $enc_logged_user_id, $user_email;
  global $user_status, $activationcode, $user_temp_info, $user_wish_receive_digest;
  global $user_account_creation_time;

  $userfilename = "$accounts_path/$username";
  if (!file_exists($userfilename))
    return;
  if (isset($deleteaccountcheckbox))
  {
    if ($deleteaccountcheckbox == "on")
    {
      unlink("$accounts_path/$username");                // Delete account file
      if (file_exists("$accounts_stat_path/$username.stat"))
        unlink("$accounts_stat_path/$username.stat");      // Delete account statistics file
      return;
    }
  }
  load_user_profile($username);
  $user_email = $typed_email;
  $user_status = $typed_status;
  if ($typed_activestatus < 2)
    $activationcode = $typed_activestatus;
  $user_wish_receive_digest = $typed_digest;
  save_user_profile($username);
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
        $message = $mess[137];
      place_message($mess[137], $logged_user_name.": ".$message, "<a href=\"login.php?action=logout\">".$mess[72]."</a> <a href=\"index.php\">".$mess[133]."</a>");
      show_menu(1, 'usrmanag.php');
      print_user_profile();
      return;
    }
  }
  // Show default window
  if ($message == "")
    $message = $mess[42];
  place_message($mess[137], $message, "<a href=\"login.php\">".$mess[71]."</a> <a href=\"index.php\">".$mess[133]."</a>");
  show_menu(0, 'usrmanag.php');
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
if (!isset($order))
  $order = "name";
if (!isset($letter))
  $letter = "A";
if (!isset($accpage))
  $accpage = 1;
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


case "customizeaccount";
  if ($logged_user_name != "")
  {
    if (check_is_user_session_active($logged_user_name))
    {
      if (isset($username))
      {
        require("include/${language}.php");
        page_header($page_title);
        change_account_data();
        show_default(sprintf($mess[140], $username));
      }
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
