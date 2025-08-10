<?

// Header and footer files

$headerpage="include/header.htm";
$footerpage="include/footer.htm";
$infopage="include/info.htm";

$charsetencoding="windows-1250";
$uploadcentercaption="SatTV - Upload Center";
$uploadcentermessage="Pamiêtaj, ¿e ogl¹danie pay-TV bez op³acania abonamentu jest nielegalne!<br>Wszystkie zamieszczone tutaj pliki mo¿esz wykorzystywaæ wy³¹cznie dla celów edukacyjnych!<br>Nikt nie odpowiada za szkody, które mog¹ wyrz¹dziæ pliki tutaj zgromadzone,<br>mog¹ one uszkodziæ twój system lub odbiornik!";

$mess=array(
"0" => "",
"1" => "Styczeñ",
"2" => "Luty",
"3" => "Marzec",
"4" => "Kwiecieñ",
"5" => "Maj",
"6" => "Czerwiec",
"7" => "Lipiec",
"8" => "Sierpieñ",
"9" => "Wrzesieñ",
"10" => "Padziernik",
"11" => "Listopad",
"12" => "Grudzieñ",
"13" => "Dzisiaj",
"14" => "Wczoraj",
"15" => "Nazwa pliku",
"16" => "Ocena",
"17" => "D³ugoæ",
"18" => "Dostarczony",
"19" => "Opis",
"20" => "Zapis pliku",
"21" => "Lokalizacja pliku",
"22" => "Opis pliku",
"23" => "Download",
"24" => "Order",
"25" => "Strona g³ówna",
"26" => "Plik",
"27" => "Drukuj",
"28" => "Zamknij",
"29" => "Powrót",
"30" => "Ten plik zosta³ usuniêty",
"31" => "Nie mo¿na otworzyæ pliku",
"32" => "Cofnij",
"33" => "B³³d ³adowania pliku",
"34" => "Musisz wybraæ plik",
"35" => "Cofnij",
"36" => "Plik",
"37" => "za³adowany pomylnie !!!",
"38" => "Plik o nazwie",
"39" => "ju¿ istnieje",
"40" => "Plik za³adowany pomylnie",
"41" => "Jêzyk wybrany pomylnie [Przek³ad na polski: Stefan Goryl]",
"42" => "Witamy w Sat-TV Upload Center!",
"43" => "Wykorzystana przestrzeñ dyskowa:",
"44" => "Show files for all days",
"45" => "Uszkodzone archiwum ZIP!",
"46" => "Zawartoæ archiwum:",
"47" => "Data/Czas",
"48" => "Katalog",
"49" => "Ograniczono prawo do ³adowania pliku o nazwie %s",
"50" => "przekracza dopuszczalny rozmiar",

"51" => "Informacja",
"52" => "Wybierz skórkê",
"53" => "Skórka",
"54" => "Witamy",
"55" => "Aktualny czas",
"56" => "U¿ytkownik",
"57" => "Nazwa u¿ytkownika",
"58" => "Zarejestruj",
"59" => "Rejestracja",
"60" => "Niedziela",
"61" => "Poniedzia³ek",
"62" => "Wtorek",
"63" => "roda",
"64" => "Czwartek",
"65" => "Pi¹tek",
"66" => "Sobota",
"67" => "Wylij",
"68" => "Mail file",
"69" => "File has been mailed to %s address.",
"70" => "File uploaded by user: %s",
"71" => "Zaloguj siê",
"72" => "Wyloguj siê",
"73" => "Wejd",
"74" => "Anonimowy",
"75" => "U¿ytkownik zwyczajny",
"76" => "Power user",
"77" => "Administrator",
"78" => "Strefa prywatna",
"79" => "Strefa publiczna",
"80" => "Wprowadzi³e z³¹ nazwê u¿ytkownika lub has³o.",
"81" => "Mój profil",
"82" => "Przegl¹daj/edytuj profil",
"83" => "Has³o",
"84" => "Wybierz jêzyk",
"85" => "Wybierz strefê czasow¹",
"86" => "Twój aktualny czas",
"88" => "Proszê wprowadziæ prawid³owy adres e-mail.",
"89" => "Twój kod aktywacyjny zostanie przes³any na adres e-mail.",
"90" => "File uploaded: ",
"91" => "Proszê wprowadziæ adres e-mail, który poda³e podczas rejestracji.",
"92" => "File size: ",
"93" => "Proszê wpisaæ swoj¹ nazwê u¿ytkownika i has³o",
"94" => "Wymagana rejestracja. Proszê siê zarejestrowaæ.",
"95" => "Rejestracja nie jest wymagana. Mo¿esz siê zarejestrowaæ, jeli chcesz, aby twój nick zosta³ dodany do za³adowanych prez Ciebie plików. Nikt nie bêdzie mó³ u¿yc twojego nicka aby podpisaæ ³adowane przez siebie pliki.",

"96" => "Skin selected.",
"97" => "Refresh",
"98" => "Please, enter your login name and password",
"99" => "Still not registered? - Register here!",
"100" => "Forgotten your password?",
"101" => "Please, go %s back %s and try again.",
"102" => "You succesfully logged out.",
"103" => "User name is invalid. The name must be not longer than 12 symbols and can consists of latin symbols and digits only. Name can also contain '-', '_', and space symbols inside.",
"104" => "The '%s' you picked has been taken.",
"105" => "Confirm password",
"106" => "Passwords do not match.",
"107" => "The format of entered e-mail address is invalid.",
"108" => "Thank you for registering. Please do not forget your password as it has been encrypted in our database and we cannot retrieve it for you. However, should you forget your password we provide an easy to use script to generate and email a new, random, password.",
"109" => "You can %s enter to Upload Center here. %s",
"110" => "Your activation code has been e-mailed to you. You must activate your account within 2 days or account will be automatically removed.",
"111" => "
Dear %s,

Thank You for registration at our Upload Center.

Because your security is importance to us, your account will need to be activated upon receipt.

Your personal activation code is: %s
(please note: this is not your password)

Activating Your account is simple:
1. Visit us at $installurl/activate.php and we will guide you through the process
2. Enter your account name and activation code.
3. Click on the 'Activate account' button.

Regards,
$admin_name
Email: mailto:$admin_email
Web Page: $installurl",
"112" => "Activate account",
"113" => "Please, activate your account",
"114" => "Activation code",
"115" => "Your account now is active.",
"116" => "Please %s enter here %s.",
"117" => "The entered account name or activation code is invalid.",
"118" => "Account already active.",
"119" => "I wish to receive to my e-mail everyday digest of uploaded files.",
"120" => "Change your password.",
"121" => "Your old password",
"122" => "The entered account name is not exists.",
"123" => "The entered e-mail address is not valid.",
"124" => "Your new password has been sent to your e-mail.",
"125" => "
Dear Upload Center user,

Your new Upload Center password is %s

Regards,
$admin_name
Email: mailto:$admin_email
Web Page: $installurl",
"126" => "Customize your account settings",
"127" => "Apply",
"128" => "Your profile saved.",
"129" => "Your password changed.",
"130" => "You typed invalid previous password.",
"131" => "You must specify your new password.",
"132" => "CONFIGURATION",
"133" => "Upload",
"134" => "Language & timezone",
"135" => "Account statistics",
"136" => "Your account has been created:",
"137" => "User management",
"138" => "Viewer (view only)",
"139" => "Uploader (upload only)",
"140" => "Account '%s' changed successfully",
"141" => "Latest",
"142" => "All",
"143" => "New e-mail address takes effect after confirmation. Confirmation code has been e-mailed to your new mail address. See instructions inside the letter.",
"144" => "
Dear %s,

Because your security is importance to us, your new e-mail address will need to be confirmed upon receipt.

Your personal confirmation code is: %s

Activating e-mail address is simple:
1. Visit us at $installurl/confirm.php and we will guide you through the process
2. Enter your account name and confirmation code.
3. Click on the 'Confirm' button.

Regards,
$admin_name
Email: mailto:$admin_email
Web Page: $installurl",
"145" => "Please, confirm your new e-mail address.",
"146" => "Confirmation code",
"147" => "Confirm",
"148" => "Nothing to confirm.",
"149" => "The entered account name or confirmation code is invalid.",
"150" => "Your new e-mail address '%s' confirmed.",
"151" => "Files uploaded",
"152" => "Files downloaded",
"153" => "Files e-mailed",
"154" => "Account created",
"155" => "Last access time",
"156" => "Status",
"157" => "Active status",
"158" => "Receive digest",
"159" => "e-mail",
"160" => "Total:",
"161" => "account(s)",
"162" => "Delete account",
"163" => "Shown %s account(s) of %s",
"164" => "Configure Upload Center",
"165" => "Edit files",
"166" => "Edit file",
"167" => "File %s has been changed succesfully",
"168" => "Save",
"169" => "Delete",
"170" => "Delete files",
"171" => "Mirror",
"172" => "Yes",
"173" => "No",
"174" => "Active",
"175" => "Inactive",
"176" => "Unautorized",
"177" => "Sorry, but server could not execute the mail program.",
"178" => "Your registration failed. Please, try again later.",
"179" => "Please, try again later."
);
?>
