<?
require("einstellungen.php");
?>
<HTML>
<HEAD>
<TITLE>Seite mit Buttons.</TITLE>
<script language="JavaScript">
<!--
function open_window(name, url, left, top, width, height, toolbar, menubar, statusbar, scrollbar, resizable)
{
  toolbar_str = toolbar ? 'yes' : 'no';
  menubar_str = menubar ? 'yes' : 'no';
  statusbar_str = statusbar ? 'yes' : 'no';
  scrollbar_str = scrollbar ? 'yes' : 'no';
  resizable_str = resizable ? 'yes' : 'no';
  window.open(url, name, 'left='+left+',top='+top+',width='+width+',height='+height+',toolbar='+toolbar_str+',menubar='+menubar_str+',status='+statusbar_str+',scrollbars='+scrollbar_str+',resizable='+resizable_str);
}

// -->
</script></HEAD>
<BODY TEXT="#FFFFFF" LINK="#00FF00" VLINK="#999999" ALINK="#77FF77" BGCOLOR="yellow" onunload="javascript:open_window('win', 'onlinedel.php?username=<? echo"$username";?>', 0, 0, 150, 150, 0, 0, 0, 0, 0);">
<?
echo "<CENTER>
    <img src=\"$logo\" border=\"0\"></CENTER>
</BODY>
</HTML>";
?>