<?
//
// Copyright (C) 2001, 2002 by Sergey Korostel    skorostel@mail.ru
//-----------------------------------------------------------------------------------------------------------------------------------------
//    VARS TO MODIFY / ÏÅÐÅÌÅÍÍÛÅ ÄËß ÍÀÑÒÐÎÉÊÈ ÑÊÐÈÏÒÀ
//    See comments in English & Russian.
//-----------------------------------------------------------------------------------------------------------------------------------------


// Next three is the only settings you should need to change in this file.
// All other settions are optional

// URL link to homepage (the link under "home" icon). These value not take influence to script working
// Ññûëêà íà äîìàøíþþ ñòðàíèöó (ññûëêà ñêðûâàåòñÿ ïîä èêîíêîé äîìèêà). Ýòî çíà÷åíèå íå âëèÿåò íà ðàáîòîñïîñîáíîñòü ñêðèïòà
$homeurl="http://www.mysite.com/mypage/";

// The name of administrator (usually your name)
// Èìÿ àäìèíèñòðàòîðà (îáû÷íî âàøå èìÿ)
$admin_name="James Bond";

// Administrator e-mail address
// Àäðåñ ýëåêòðîííîé ïî÷òû àäìèíèñòðàòîðà
$admin_email="bond@mail.net";


// Next settings you should need to change if you wish use non-default folders
// or your script works incorrectly

$domain_name = $HTTP_SERVER_VARS["SERVER_NAME"];
$root_path = $DOCUMENT_ROOT;
$script_folder_name = dirname($PHP_SELF);
$users_folder_name=$script_folder_name."/users";
$userstat_folder_name=$script_folder_name."/userstat";
$uploads_folder_name = $script_folder_name."/files";


/*
// If you wish use non-default folders, you can assign settings like below:
$domain_name = "www.mysite.com";
$root_path = "/www/htdocs";
$script_folder_name = "/myfolder/upload";             // The name of folder, where script files will be installed
$uploads_folder_name = "/myfolder/uploads";           // The name of folder, where users will be upload files
$users_folder_name="/myfolder/upload/users";          // The name of folder, where script will store the user accounts
$userstat_folder_name="/myfolder/upload/userstat";
*/

// Paths settings. Don't change this settings!
$installurl="http://" . $domain_name . $script_folder_name;
$users_path=$root_path . $users_folder_name;
$uploads_path=$root_path . $uploads_folder_name;
$url_path="http://" . $domain_name . $uploads_folder_name;
$accounts_path=$root_path . $users_folder_name;
$accounts_stat_path=$root_path . $userstat_folder_name;

// Cookie settings. Don't change this settings!
$cookiedomain = $domain_name;
$cookiepath = $script_folder_name;
$cookiesecure = false;

$second_cookiedomain = "";
$second_cookiepath = $script_folder_name;

// The default status of new registered user
// 0 - Administrator, 1 - Power User, 2 - Normal User, 3 - Viewer (view only), 4 - Uploader (upload only)
// Ñòàòóñ âíîâü çàðåãèñòðèðîâàííîãî ïîëüçîâàòåëÿ ïî äåôîëòó
// 0 - Àäìèíèñòðàòîð, 1 - Ïðîäâèíóòûé, 2 - Íîðìàëüíûé, 3 - Òîëüêî ïðîñìîòð, 4 - Òîëüêî çàãðóçêà
$default_user_status=2;

// Next two variables allow unregistered users view & upload files (1 - on, 0 - off)
// When these settings is on, the user statuses 3 and 4 is equal to 2.
// Ñëåäóþùèå äâå ïåðåìåííûå ïîçâîëÿþò íåçàðåãèñòðèðîâàííûì ïîëüçîâàòåëÿì ïðîñìàòðèâàòü è çàãðóæàòü ôàéëû (1 - âêëþ÷èòü, 0 - âûêëþ÷èòü)
// Êîãäà ýòè çíà÷åíèÿ âêëþ÷åíû, ñòàòóñû ïîëüçîâàòåëÿ 3 è 4 ðàâíû 2.

$allow_anonymous_upload=1;
$allow_anonymous_view=1;

// The name of page header
// Çàãîëîâîê ñòðàíèöû
$page_title="File Upload Center";

// Server timezone offset, relative to GMT (Greenwich Mean Time), in hours
// Change this setting only if time works incorrect
// Ñìåùåíèå âðåìåííîé çîíû ñåðâåðà, îòíîñèòåëüíî GMT, â ÷àñàõ
// Ìåíÿéòå ýòî çíà÷åíèå, òîëüêî åñëè âðåìÿ îòîáðàæàåòñÿ íåêîððåêòíî
$GMToffset = date("Z")/3600;

// The time when script have to do maintenance functions (delete old files, send digest, ...)
// Values is GMT hour, i.e. if you want to do maintenance functions after 03:00, set the value to 3.
// Âðåìÿ, êîãäà ñêðèïò äîëæåí ïðîâîäèòü ñåðâèñíûå îïåðàöèè (óäàëÿòü ñòàðûå ôàéëû, ðàññûëàòü ðàññûëêó)
// Âðåìÿ óêàçûâàåòñÿ â ÷àñàõ ïî GMT, íàïðèìåð çíà÷åíèå 3 îçíà÷àåò, ÷òî ôóíêöèè áóäóò ïðîâîäèòñÿ ïîñëå 03:00 ïî GMT
$maintenancetime=2;

// Enable (1) or disable (0) mail functions. If disabled, the digest, mail confirmation & file mailing function is off
// Disable, if you haven't rights to send mail from server of you don't need these functions
// Âêëþ÷èòü (1) èëè âûêëþ÷èòü (0) ïî÷òîâûå ôóíêöèè.
// Âûêëþ÷èòå, åñëè ó âàñ íåò ïðàâ ðàáîòû ñ ïî÷òîé èëè âûì íå íóæíû ýòè ôóíêöèè
$mailfunctionsenabled=1;


// The maximum size of file, which normal users can send to their mail (in kilobytes)
// To disable this function (for normal users only), set size below zero
// Ìàåñèìàëüíûé ðàçìåð ôàéëà, êîòîðûé ïîëüçîâàòåëü ìîæåò çàïðàøèâàòü ïî e-mail.
// ×òî áû âûêëþ÷èòü ýòó âîçìîæíîñòü (äëÿ îáû÷íûõ ïîëüçîâàòåëåé), óñòàíîâèòå ðàçìåð ìåíüøå íóëÿ.
$maxfilesizetomail=20;

// Enable (1) or disable (0) account activation via e-mail.
// These feature helps you check the e-mail address of registered user
// Òðåáóåòñÿ ëè àêòèâèçàöèÿ àêêàóíòà ïîëüçîâàòåëÿ ïóòåì îòñûëêè ïîëüçîâàòåëþ ïèñüìà
// Ýòà ôóíêöèÿ ïîìîæåò âàì ïðîâåðèòü ñóùåñòâîâàíèå e-mail àäðåñà, óêàçàííîãî ïðè ðåãèñòðàöèè
$requiremailconfirmation=1;

// Show dynamic java menu or not
// Ïîêàçûâàòü äèíàìè÷åñêîå ìåíþ êîíôèãóðàöèè (1) èëè íåò (0)
// â ìåíþ êîíôèãóðàöèè ìîæíî âûáðàòü òåêóùèé ÿçûê, ñêèí è äð. îïöèè
// Åñëè ìíåþ íå ïîêàçûâàåòñÿ, òî âûáîð ÿçûêà ïðîèñõîäèò â "øàïêå" ñòðàíèöû,
// à ñêèí âûáðàòü íåâîçìîæíî.
$show_configuration_menu = 1;

// Enable (1) or disable (0) language switching ability
// Ïîçâîëÿåò (1) èëè çàïðåùàåò (0) ïîëüçîâàòåëþ èçìåíÿòü òåêóùèé ÿçûê è âðåìåííóþ çîíó
// Åñëè ðàçðåøåíî, îòîáðàæàåò ôëàãè ñòðàí, êëèêíóâ íà êîòîðûå ìîæíî ñìåíèòü ÿçûê è âðåìåííóþ çîíó
$allow_choose_language = 1;

// Enable or disable change user skins
// Ïîçâîëÿåò (1) èëè çàïðåùàåò (0) ïîëüçîâàòåëþ èçìåíÿòü òåêóùèé ñêèí
$allow_choose_skin = 1;

// The mail header for digest (html code allowed)
// Çàãîëîâîê ïèñüìà ðàññûëêè (äîïóñêàþòñÿ html òåãè)
$mailinfopage="include/mailinfo.htm";

// Default language
// ßçûê ïî-óìîë÷àíèþ
// English : en
// Russian : ru
// Poland : pl
// and so on....

$dft_language="en";

$languages=array(
  "en" => array(                        // Language code. You have to place php language file with this name into include folder
                                        // Êîä ÿçûêà. Âû äîëæíû ïîìåñòèòü ÿçûêîâîé php ôàéë ñ ýòèì èìåíåì â êàòàëîã include
    "TimeZone" => 0,                    // Timezone offset (relative to GMT, in hours)
                                        // Ñìåùåíèå äî âðåìåííîé çîíû (îòíîñèòåëüíî GMT, â ÷àñàõ)
    "LangName" => "English (GMT)",      // Language name
                                        // Íàçâàíèå ÿçûêà
    "LangFlag" => "images/flag_en.gif"  // Language flag picture name
                                        // Èìÿ èçîáðàæåíèÿ ôëàãà äëÿ ÿçûêà
  ),
  "ru" => array(
    "TimeZone" => 3,                        // Moscow time = GMT + 3
    "LangName" => "Russian (Moscow time)",
    "LangFlag" => "images/flag_ru.gif"
  ),
  "by" => array(
    "TimeZone" => 2,                        // Byelorussian time = EET (East European Time) = GMT + 2
    "LangName" => "Byelorussian (East European Time)",
    "LangFlag" => "images/flag_by.gif"
  ),
  "lt" => array(
    "TimeZone" => 2,                        // EET (East European Time) = GMT + 2
    "LangName" => "Lithuanian (EET)",
    "LangFlag" => "images/flag_lt.gif"
  ),
  "pl" => array(
    "TimeZone" => 1,                        // CET (Central European Time) = GMT + 1
    "LangName" => "Polish (CET)",
    "LangFlag" => "images/flag_pl.gif"
  ),
  "bg" => array(
    "TimeZone" => 2,                        // EET (Central European Time) = GMT + 2
    "LangName" => "Bulgarian (EET)",
    "LangFlag" => "images/flag_bg.gif"
  ),
  "de" => array(
    "TimeZone" => 1,                        // CET (Central European Time) = GMT + 1
    "LangName" => "German (CET)",
    "LangFlag" => "images/flag_de.gif"
  ),
  "nl" => array(
    "TimeZone" => 1,                        // CET (Central European Time) = GMT + 1
    "LangName" => "Dutch (CET)",
    "LangFlag" => "images/flag_nl.gif"
  ),
  "it" => array(
    "TimeZone" => 1,                        // CET (Central European Time) = GMT + 1
    "LangName" => "Italian (CET)",
    "LangFlag" => "images/flag_it.gif"
  ),
  "gr" => array(
    "TimeZone" => 2,                        // EET (East European Time) = GMT + 2
    "LangName" => "Greek (EET)",
    "LangFlag" => "images/flag_gr.gif"
  ),
  "cz" => array(
    "TimeZone" => 1,                        // CET (Central European Time) = GMT + 1
    "LangName" => "Czech (CET)",
    "LangFlag" => "images/flag_cz.gif"
  )
);

// Maximum allowed filesize to upload (Kilobytes)
// Note: server also has upload size limit
// Ìàêñèìàëüíî äîïóñòèìûé ðàçìåð ôàéëà äëÿ çàêà÷êè (Êèëîáàéò)
// Çàìåòêà: ñåðâåð òîæå èìååò îãðàíè÷åíèå íà ïðèíèìàåìûé ðàçìåð ôàéëà
$maxalowedfilesize=4096;  // 4Mb

// Format of date & time
// Ôîðìàò âûâîäà äàòû è âðåìåíè
$datetimeformat="d.m.Y H:i";

// Max number chars for file and directory names
// Ìàêñèìàëüíîå ÷èñëî ñèìâîëîâ äëÿ ôàéëîâ è êàòàëîãîâ
$file_name_max_caracters=150;

// Max number chars for filename in tables
// Ìàêñèìàëüíî âèäèìîå ÷èñëî ñèìâîëîâ äëÿ ôàéëîâ
$file_out_max_caracters=40;

// Max number chars for file comment (description)
// Ìàêñèìàëüíîå ÷èñëî ñèìâîëîâ â êîììåíòàðèè ê ôàéëó
$comment_max_caracters=300;

// Regular expression defines which files can't be uploaded
// Ðåãóëÿðíîå âûðàæåíèå, îïðåäåëÿþùåå, êàêèå ôàéëû íåëüçÿ çàãðóçèòü ñ ïîìîùüþ ýòîãî ñêðèïòà
$rejectedfiles="^index.|.desc$|.dlcnt$|.php$|.php3$|.cgi$|.pl$";

// Show hidden files: Yes=1, No=0
// Ïîêàçûâàòü ñêðûòûå ôàéëû: Äà=1, Íåò=0
$showhidden=0;

// Delete files older then $daysinhistory days
// Ñêîëüêî äíåé äîëæíî ïðîéòè, ïðåæäå, ÷åì ñêðèïò íà÷íåò óäàëÿòü ôàéëû
$daysinhistory = 7;


// Header & Background colors of table, Font colors
// Öâåòà òàáëèö, øðèôòà

$skins=array(
  array(
    "bordercolor" => "#000000",    // The table border color       /Öâåò ðàìêè òàáëèöû/
    "headercolor" => "#4682B4",    // The table header color      /Öâåò çàãîëîâêà òàáëèöû/
    "tablecolor" => "#F5F5F5",     // The table background color /Öâåò ôîíà òàáëèöû/
    "lightcolor" => "#FFFFFF",     // Table date field color     /Öâåò ôîíà äàòû â òàáëèöå/
    "headerfontcolor" => "#FFFFFF",
    "normalfontcolor" => "#000000",
    "selectedfontcolor" => "#4682B4",
    "bodytag" => "bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#333333\" alink=\"#000000\""
  ),
  array(
    "bordercolor" => "#000000",
    "headercolor" => "#8b9c8b",
    "tablecolor" => "#F5F5F5",
    "lightcolor" => "#bfccbf",
    "headerfontcolor" => "#FFFFFF",
    "normalfontcolor" => "#000000",
    "selectedfontcolor" => "#ABCDAB",
    "bodytag" => "bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#333333\" alink=\"#000000\""
  ),
  array(
    "bordercolor" => "#000000",
    "headercolor" => "#006666",
    "tablecolor" => "#F5F5F5",
    "lightcolor" => "#CCFFFF",
    "headerfontcolor" => "#FFFFFF",
    "normalfontcolor" => "#000000",
    "selectedfontcolor" => "#ABCDAB",
    "bodytag" => "bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#333333\" alink=\"#000000\""
  ),
  array(
    "bordercolor" => "#000000",
    "headercolor" => "#990000",
    "tablecolor" => "#F5F5F5",
    "lightcolor" => "#FFFF99",
    "headerfontcolor" => "#FFFFFF",
    "normalfontcolor" => "#000000",
    "selectedfontcolor" => "#ABCDAB",
    "bodytag" => "bgcolor=\"#FFFFFF\" text=\"#000000\" link=\"#000000\" vlink=\"#333333\" alink=\"#000000\""
  )
);


// Letters font
// Ãàðíèòóðà øðèôòà

$font="Verdana";


?>