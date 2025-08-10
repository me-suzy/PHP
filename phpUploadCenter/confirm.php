<?
// File Upload Center
//
// Copyright (C) 2001, 2002 by Sergey Korostel    skorostel@mail.ru
//


function print_default()
{
global $mess, $font, $normalfontcolor, $selectedfontcolor, $homeurl, $languages;
global $uploadcentercaption;
global $tablecolor,$bordercolor,$headercolor,$headerfontcolor;

echo "
   <p>&nbsp;</p>

   <table border=\"0\" width=\"85%\" bgcolor=\"$bordercolor\" cellpadding=\"4\" cellspacing=\"1\">
     <tr>
       <th align=\"left\" bgcolor=\"$headercolor\" valign=\"middle\"><font size=\"2\" face=\"$font\" color=\"$headerfontcolor\">$mess[145]</font></th>
     </tr>
     <tr>
         <td align=\"left\" bgcolor=\"$tablecolor\" valign=\"middle\">
         <form name=\"userlogin\" action=\"confirm.php\" enctype=\"multipart/form-data\" method=\"post\" style=\"margin: 0\">
           <input type=\"hidden\" name=\"action\" value=\"confirm\">
           <table border=\"0\" width=\"100%\" cellpadding=\"4\">
             <tr>
               <td align=\"left\" width=\"15%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[57]</font></td>
               <td align=\"left\" width=\"85%\">
                 <input type=\"text\" name=\"user_name\">
               </td>
             </tr>
             <tr>
               <td align=\"left\" width=\"15%\"><font size=\"1\" face=\"$font\" color=\"$normalfontcolor\">$mess[146]</font></td>
               <td align=\"left\" width=\"85%\">
                 <input type=\"text\" name=\"code\">
               </td>
             </tr>
             <tr>
               <td align=\"left\" width=\"100%\" colspan=\"2\">
                 <input type=\"submit\" name=\"Submit\" value=\"$mess[147]\" />
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
if (!isset($user_name))
  $user_name = "";
if (!isset($code))
  $code = "";


switch($action)
{

case "savelanguage";
  change_language();
  require("include/${language}.php");
  page_header($page_title);
  place_message($mess[147], $mess[41], "");
  show_menu(0, 'confirm.php');
  print_default();
break;


case "selectskin";
  change_skin();
  require("include/${language}.php");
  page_header($page_title);
  place_message($mess[147], $mess[96], "");
  show_menu(0, 'confirm.php');
  print_default();
break;


case "confirm";
  require("include/${language}.php");
  page_header($page_title.": ".$mess[147]);
  show_menu(0, 'confirm.php');
  if (($user_name != "") && ($code != ""))
  {
    // åñëè ïîëüçîâàòåëü ïðèø¸ë ñ ëþáîé ñòðàíèöû íàøåãî ñàéòà òî îí âðîäå íàø...
    // Ïåðåìåííàÿ $HTTP_REFERER âñåãäà äîñòóïíà ïî óìîë÷àíèþ
    // è ñîäåðæèò ïîëíûé àäðåñ ññûëàþùåéñÿ ñòðàíèöû...
    // ôóíêöèÿ eregi() ïðîâåðÿåò, íà÷èíàåòñÿ ëè àäðåñ ññûëàþùåéñÿ ñòðàíèöû
    // ñî çíà÷åíèÿ â ïåðåìåííîé $SERVER_ROOT
    if(eregi("^$installurl",$HTTP_REFERER))
    {
      $userfilename = "$accounts_path/$user_name";
      // Check the user name
      if (file_exists($userfilename))
      {
        load_user_profile($user_name);


        if (substr($user_temp_info,0,1) != "m")
        {
          place_message($mess[148], $mess[148]." ".sprintf($mess[116], "<a href=\"login.php\">", "</a>"), "");
          include($footerpage);
          exit;
        }
        list($m, $confcode, $newmail) = explode(":", $user_temp_info);
        if ($confcode == $code)
        {
          $user_email = $newmail;
          $user_temp_info = "";
          save_user_profile($user_name);
          place_message(sprintf($mess[150], $user_email), sprintf($mess[150], $user_email)." ".sprintf($mess[116], "<a href=\"login.php\">", "</a>"), "");
          include($footerpage);
          exit;
        }
      }
    }
    // Show invalid activation code message
    place_message($mess[149], $mess[149]." ".sprintf($mess[101], "<a href=\"confirm.php\">", "</a>"), "");
  }
  else
    place_message($mess[149], $mess[149]." ".sprintf($mess[101], "<a href=\"confirm.php\">", "</a>"), "");
break;

default;
  require("include/${language}.php");
  page_header($page_title.": ".$mess[147]);
  place_message($mess[147], $mess[147], "");
  show_menu(0, 'confirm.php');
  print_default();
break;
}

include($footerpage);
?>
