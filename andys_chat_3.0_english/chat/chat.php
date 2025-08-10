<html>

<head>
<title>No title</title>
</head>

<body bgcolor="white" text="black" link="blue" vlink="purple" alink="red" onLoad="setTimeout('moving()',1000);">
<font face="Veranda"><?php
          require("einstellungen.php");
          $message_array = file("messages.htm");
          $counter=1;
          $old_messages="";
            for ($counter=1; $counter<17; $counter++){
                      $old_messages .= $message_array[$counter];
             }
$message=htmlspecialchars(trim($message));
if (!empty($message)) {
$message=ereg_replace("\'","´",$message);
$message=ereg_replace(":-\)","<img src=smiles/1.gif width=16 height=16 vspace=0 border=0>",$message);
$message=ereg_replace(":-\(","<img src=smiles/2.gif width=15 height=15 vspace=0 border=0>",$message);
$message=ereg_replace(":-\|","<img src=smiles/3.gif width=15 height=15 vspace=0 border=0>",$message);
$message=ereg_replace(";-\)","<img src=smiles/4.gif width=15 height=15 vspace=0 border=0>",$message);
$message=ereg_replace(":-x","<img src=smiles/5.gif width=15 height=18 vspace=0 border=0>",$message);
$message=ereg_replace(":-O","<img src=smiles/6.gif width=15 height=15 vspace=0 border=0>",$message);
}
$jetzt = date("H:i:s");
         $new_message = "<font face=\"Verdana\" color=\"$farbe\">&nbsp;$jetzt&nbsp;&nbsp;$chatuser : $message<br>\n</font>";
          $header = "<html><head><meta http-equiv=\"refresh\" content=\"8\">".
                  "<meta name=\"robots\" content=\"noindex\">
</head>".
                      "<body bgcolor=\"white\" text=\"#000000\" onLoad=\"setTimeout('moving()',500);\">\n";
          $open_file = fopen("messages.htm", "w");
          fputs($open_file, $header);
          fputs($open_file, stripslashes($new_message));
          fputs($open_file, $old_messages);
          fclose($open_file);
header ("Location: eingabe.php?chatuser=$chatuser");
?></font><script language="JavaScript"><!--
ie=(navigator.appName=='Microsoft Internet Explorer');
newpage="eingabe.php?chatuser=<?php echo "$chatuser&farbe=$farbe";?>";
function moving(){ location=newpage; }
// --></script>

<script language="JavaScript1.1"><!--
function moving(){
  // JavaScript1.1 È~ÅÍ replace()AÈOÍ location() Å
  if(ie && parseInt(navigator.appVersion)<4)
    location=newpage;
  else
    location.replace(newpage);
}
// --></script>


</body>

</html>