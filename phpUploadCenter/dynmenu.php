<?
function build_menu($isuserloggedin, $scriptname)
{
global $languages, $mess;
global $allow_choose_language,$allow_choose_skin,$skins;
global $headercolor,$tablecolor;

echo "
  <STYLE><!--
  A.ssmItems:link         {color:black;text-decoration:none;}
  A.ssmItems:hover        {color:black;text-decoration:none;}
  A.ssmItems:active       {color:black;text-decoration:none;}
  A.ssmItems:visited      {color:black;text-decoration:none;}
  //--></STYLE>
  <SCRIPT SRC=\"ssm.js\" language=\"JavaScript1.2\"></SCRIPT>

  <SCRIPT language=\"JavaScript1.2\">
  <!--
  /*
  Configure menu styles below
  NOTE: To edit the link colors, go to the STYLE tags and edit the ssmItems colors
  */
  YOffset=60; // no quotes!!
  staticYOffset=20; // no quotes!!
  XOffset=0; // no quotes!!
  slideSpeed=20 // no quotes!!
  waitTime=100; // no quotes!! this sets the time the menu stays out for after the mouse goes off it.
  menuBGColor=\"#898979\";
  menuIsStatic=\"yes\";
  menuWidth=300; // Must be a multiple of 10! no quotes!!
  menuCols=2;
  hdrFontFamily=\"verdana\";
  hdrFontSize=\"2\";
  hdrFontColor=\"white\";
  hdrBGColor=\"${headercolor}\";
  hdrAlign=\"left\";
  hdrVAlign=\"center\";
  hdrHeight=\"25\";
  linkFontFamily=\"Verdana\";
  linkFontSize=\"2\";
  linkBGColor=\"white\";
  linkOverBGColor=\"#FFFF99\";
  linkTarget=\"_top\";
  linkAlign=\"left\";
  barBGColor=\"${tablecolor}\";
  barFontFamily=\"Verdana\";
  barFontSize=\"2\";
  barFontColor=\"black\";
  barVAlign=\"center\";
  barWidth=20; // no quotes!!
  barText='$mess[132]'; // <IMG> tag supported, Ex: '<img src=\"some.gif\" border=0>'
";

  //       name, link, target, colspan, endrow
  if ($allow_choose_language)
  {
    echo "addHdr(\"$mess[134]\");";

/*
  addItem('<img src=\"images/flag_en.gif\" border=1> English (GMT)', 'index.php?action=savelanguage&language=en', '', 1, 'no');
  addItem('<img src=\"images/flag_ru.gif\" border=1> Russian (MCK)', 'index.php?action=savelanguage&language=ru', '', 1);
*/

    while (list($langid, $langdata) = each($languages))
    {

     echo "addItem('<img src=\"";
     echo $langdata["LangFlag"];
     echo "\" border=1> ";
     echo $langdata["LangName"];
     echo "', '$scriptname?action=savelanguage&language=";
     echo $langid;
     echo "', '');\n";
    }
  }
  if ($allow_choose_skin)
  {
    echo "addHdr(\"$mess[52]\");";
    $j = 1;
    for ($i = 0; $i < count($skins); $i++)
    {
      echo "addItem('$mess[53] ";
      echo $i+1;
      echo "&nbsp;&nbsp;<b><font size=\"4\" color=\"";
      echo $skins[$i]["headercolor"];
      echo "\">*</font>";
      echo "<font size=\"4\" color=\"";
      echo $skins[$i]["lightcolor"];
      echo "\">*</font>";
      echo "<font size=\"4\" color=\"";
      echo $skins[$i]["tablecolor"];
      echo "\">*</font></b>', '$scriptname?action=selectskin&skinindex=";
      echo $i;
      if ($j)
      {
        echo "', '', 1, 'no');";
        $j = 0;
      }
      else
      {
        echo "', '', 1);";
        $j = 1;
      }
    }
  }

if ($isuserloggedin)
{
echo "
  addHdr(\"$mess[81]\");
//  addItem(\"$mess[82]\", \"login.php\", \"_blank\");
  addItem(\"$mess[82]\", \"login.php\", \"\");
  addItem(\"$mess[72]\", \"login.php?action=logout\", \"\");";
}
else
{
echo "
  addHdr(\"$mess[81]\");
  addItem(\"$mess[71]\", \"login.php\", \"\");";
}

echo "
  buildMenu();
  //-->
  </SCRIPT>
";

}

?>
