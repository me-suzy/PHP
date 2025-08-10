<?
// vBPortal Integration
// If you want to include the vBPortal header, footer, and left menu, remove
// the "//" slashes from the beginning of the 16 lines of code below, and change
// "/home/public_html/vbportal" to your actual path to vbportal's main directory,
// and change "/home/public_html/photopost" to your actual path to PhotoPost's
// directory. This will override the default header and footer variables set in the
// PhotoPost admin panel.

//$vbportal="/home/public_html/vbportal"; // No ending slash
//$pppath ="/home/public_html/photopost"; // No ending slash
//chdir($vbportal . "/");
//require ("mainfile.php");
//$index = 0;
//global $Pmenu,$Pheader;
//$Pheader="P_themeheader";
//$Pmenu="P_thememenu_photopost";
//require("header.php");
//$vbportal=ob_get_contents();
//ob_end_clean();
//ob_start();
//require("footer.php");
//$vbfooter=ob_get_contents();
//ob_end_clean();
//chdir($pppath . "/");

// vBulletin Integration
// Instead of using the static header/footer file specified in the Admin options
// panel, you can use your existing default vBulletin header/footer.  Just change
// $vbpath and $pppath below to the proper full paths and remove the "//" slashes
// from the beginning of the 17 lines of code below.  If PhotoPost has an odd
// background color or squished width, you will need to edit vbulletin's default
// "header" style input box / template and change "{pagebgcolor}" and "{tablewidth}"
// (near the bottom) to your preferred background color and table width, respectively.

//$vbpath ="/full/path/to/forum"; // changeme
//$pppath ="/full/path/to/photo"; // changeme
//chdir($vbpath);
//require("global.php");
//ob_start();
//eval("dooutput(\"".gettemplate('headinclude')."\",0);");
//$bodytag="<body>";
//echo dovars($bodytag,0);
//$vbheader="<head>";
//$vbheader.=ob_get_contents();
//$vbheader.="</head>";
//ob_end_clean();
//ob_start();
//eval("dooutput(\"".gettemplate('header')."\",0);");
//$vbheader.=ob_get_contents();
//ob_end_clean();
//ob_start();
//eval("dooutput(\"".gettemplate('footer')."\",0);");
//$vbfooter=ob_get_contents();
//ob_end_clean();
//chdir($pppath);

?>
