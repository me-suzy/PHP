<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | HITWEB version 3.0                                                   |
// +----------------------------------------------------------------------+
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 2 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful, but  |
// | WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU    |
// | General Public License for more details.                             |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to the Free Software          |
// | Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA            |
// | 02111-1307, USA.                                                     |
// +----------------------------------------------------------------------+
// | Authors : Brian FRAVAL <brian@fraval.org>                            |
// +----------------------------------------------------------------------+
//
// $Id: conf.php,v 1.8 2001/06/19 22:48:55 hitweb Exp $




//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
include "../conf/hitweb.conf" ;


//########################################################################################
//# CLASS FastTemplate en PHP
//########################################################################################
include "../$REP_CLASS/class.FastTemplate".$EXT_PHP ;


//########################################################################################
//# Internationalisation de la partie administration
//########################################################################################

include "$REP_LANG_ADMIN/$LANG_ADMIN".$EXT_PHP ;



function conf_file($msg) {
  
  global $tpl ;
  global $title_admin, $title_som_admin, $link_conf_db, $link_conf_file, $link_application, $link_valid_url, $link_polls, $link_check_url, $link_international_admin, $licence;
  global $bt_enre, $bt_reset;
  global $lib_repclass, $lib_replangadmin, $lib_langadmin, $lib_ext_php, $lib_ext_tpl, $lib_mail_moderator, $lib_address_site ;
  global $lib_func_mail, $lib_use_mail, $lib_rep_tpl;

  // Pour le deuxième TOUR
  include "../conf/hitweb.conf" ;


  $tpl = new FastTemplate("tpl/");

  // Calcul le temps d'exécution du script, pour regarder son temps
  // Il faut regarder le code HTML généré. Ceci est complètement optionnel.
  $start = $tpl->utime();


  $tpl->define(
	       array(
		     header => "header".$EXT_TPL,
		     conf_file => "conf_file".$EXT_TPL,
		     footer => "footer".$EXT_TPL   
		     )
	       );
  
  $tpl->define_dynamic ( "lang", "conf_file" );
  $tpl->define_dynamic ( "skin", "conf_file" );

  $tpl->assign(
	       array(
		     TITLE => "$title_admin",
		     TITLE_SOM => "$title_som_admin",
		     LINK_CONF_DB => "$link_conf_db",
		     LINK_CONF_FILE => "$link_conf_file",
		     LINK_APPLICATION => "$link_application",
		     LINK_VALID_URL => "$link_valid_url",
		     LINK_POLLS => "$link_polls",
		     LINK_CHECK_URL => "$link_check_url",
		     LINK_INTERNATIONAL_ADMIN => "$link_international_admin",
   		     LICENCE => "$licence",
		     ALIGN => "center",
		     WHAT => "file"
		     )
	       );

  
  $tpl->assign(
	       array(
		     BT_ENRE => $bt_enre,
		     BT_RESET => $bt_reset,
		     LIB_REP_CLASS => $lib_repclass,
		     LIB_REP_LANGADMIN => $lib_replangadmin,
		     LIB_LANG_ADMIN => $lib_langadmin,
		     LIB_EXT_PHP => $lib_ext_php,
		     LIB_EXT_TPL => $lib_ext_tpl,
			 LIB_REP_TPL => $lib_rep_tpl,
			 LIB_FUNC_MAIL => $lib_func_mail,
			 LIB_USE_MAIL => $lib_use_mail,
		     LIB_MAIL_MODERATOR => $lib_mail_moderator,
		     LIB_ADDRESS_SITE => $lib_address_site
		     )
	       );
  

  $tpl->assign(DBNAME, "$DBNAME");
  $tpl->assign(DBUSER, "$DBUSER");
  $tpl->assign(DBPASS, "$DBPASS");
  $tpl->assign(DBHOST, "$DBHOST");

  $tpl->assign(REP_CLASS, "$REP_CLASS");
  $tpl->assign(REP_LANG_ADMIN, "$REP_LANG_ADMIN");



// REGEX : Récupération des différentes langues disponible 
// pour l'interface d'administraion

  $dir = "$REP_LANG_ADMIN/";

  $handle=opendir($dir);
  $i = 0;
  while ($file = readdir($handle)) {
    
    if ( preg_match_all("/^([a-zA-Z0-9]*)$EXT_PHP/", $file, $regs))
      {
	$lang_admin = $regs[1][0];
	
	$tpl->assign(LANG_ADMIN, "$lang_admin");
	
	if ($LANG_ADMIN == $lang_admin)
	  {
	    $tpl->assign(SELECTED, "selected");
	  } else {
	    $tpl->assign(SELECTED, "");
	  }
	
	$tpl->parse (BLOCK_LANG, ".lang" );
	
      }
    
    $i++;
  }
  
  closedir($handle);
  
  
// REGEX : Récupération des interfaces graphiques disponibles 
// pour l'annuaire hitweb

//$tpl->clear("BLOCK");

$path = "../tpl";

if ($dir_tpl = opendir($path))
{
  while($file = readdir($dir_tpl))
  {
    if ($file !="." && $file != ".." && $file != "CVS")
      {
	if (is_dir($path."/".$file))
	  {
	    
	    $tpl->assign(DIR_TPL, $file);
	    
	    if ($REP_TPL == $file)
	      {
		$tpl->assign(SELECTEDTPL, "selected");
	      } else {
		$tpl->assign(SELECTEDTPL, "");
	      }
	    
	    $tpl->parse (BLOCK_SKIN, ".skin" );
	  }
      }
  }
}  
 
closedir($dir_tpl);

  $tpl->assign(EXT_PHP, "$EXT_PHP");
  $tpl->assign(EXT_TPL, "$EXT_TPL");






  if($USE_MAIL == "oui")
  {
    $tpl->assign(SELECTED_YES, "selected");  
	$tpl->assign(SELECTED_NO, "");
  } else {
    $tpl->assign(SELECTED_YES, "");
    $tpl->assign(SELECTED_NO, "selected");
  }

  $tpl->assign(FUNC_MAIL, "$FUNC_MAIL");

  $tpl->assign(MAIL, "$MAIL");
  $tpl->assign(SITE, "$SITE");

  $tpl->assign(BASE, "$BASE");






  
  if (!$msg)
  {
    $tpl->assign(MESSAGE, "");
  } else {
    $tpl->assign(MESSAGE, "$msg");
  }

  $tpl->parse(HEADER, header) ; 
  $tpl->FastPrint("HEADER");
  //$tpl->clear("HEADER");

  $tpl->parse(CONF_FILE, "conf_file") ; 
  $tpl->FastPrint("CONF_FILE");

  $tpl->parse(FOOTER, footer) ; 
  $tpl->FastPrint("FOOTER");  
  
  // Permet d'arrêter le cacul du temps et affichage du résultat en commentaire HTML
  // dans le code généré.
  $end = $tpl->utime();
  $run = $end - $start;
  echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
  exit;


}






function conf_db($msg)
{
  global $tpl ;
  global $DBNAME, $DBUSER, $DBPASS, $DBHOST, $BASE, $REP_CLASS, $EXT_PHP,  $EXT_TPL, $MAIL, $SITE, $REP_LANG_ADMIN, $LANG_ADMIN ;
  global $title_admin, $title_som_admin, $link_conf_db, $link_conf_file, $link_application, $link_valid_url, $link_polls, $link_check_url, $link_international_admin, $licence;
  global $bt_enre, $bt_reset;
  global $lib_dbhost, $lib_dbname, $lib_dbuser, $lib_dbpass, $lib_type_db ;

  // Pour le deuxième TOUR
  include "../conf/hitweb.conf" ;

  $tpl = new FastTemplate("tpl/");

  // Calcul le temps d'exécution du script, pour regarder son temps
  // Il faut regarder le code HTML généré. Ceci est complètement optionnel.
  $start = $tpl->utime();


  $tpl->define(
	       array(
			header => "header".$EXT_TPL,
			conf_db   => "conf_db".$EXT_TPL,
			footer => "footer".$EXT_TPL   
		     )
	       );
  
   $tpl->define_dynamic ( "base", "conf_db" );

  
  $tpl->assign(
	       array(
		     TITLE => "$title_admin",
		     TITLE_SOM => "$title_som_admin",
		     LINK_CONF_DB => "$link_conf_db",
		     LINK_CONF_FILE => "$link_conf_file",
		     LINK_APPLICATION => "$link_application",
		     LINK_VALID_URL => "$link_valid_url",
		     LINK_POLLS => "$link_polls",
		     LINK_CHECK_URL => "$link_check_url",
		     LINK_INTERNATIONAL_ADMIN => "$link_international_admin",
		     LICENCE => "$licence",
		     ALIGN => "center",
		     WHAT => "db"
		     )
	       );

  
  $tpl->assign(
	       array(
		     BT_ENRE => $bt_enre,
		     BT_RESET => $bt_reset,
		     LIB_DBHOST => $lib_dbhost,
		     LIB_DBNAME => $lib_dbname,
		     LIB_DBUSER => $lib_dbuser,
		     LIB_DBPASS => $lib_dbpass,
		     LIB_TYPE_DB => $lib_type_db
		     )
	       );
  

  $tpl->assign(DBNAME, "$DBNAME");
  $tpl->assign(DBUSER, "$DBUSER");
  $tpl->assign(DBPASS, "$DBPASS");
  $tpl->assign(DBHOST, "$DBHOST");

  $tpl->assign(REP_CLASS, "$REP_CLASS");
  
  $tpl->assign(EXT_PHP, "$EXT_PHP");
  $tpl->assign(EXT_TPL, "$EXT_TPL");
  $tpl->assign(MAIL, "$MAIL");
  $tpl->assign(SITE, "$SITE");






// REGEX : Récupération du nom de la base de données par rapport aux class
// disponible dans le répertoire CLASS

  
  $dir = "../$REP_CLASS/";

  $handle=opendir($dir);
  $i = 0;
  while ($file = readdir($handle)) {
    
    if ( preg_match_all("/^class\.db_([a-zA-Z0-9]*)$EXT_PHP/", $file, $regs))
      {
	$base = $regs[1][0];
	$tpl->assign(BASE, "$base");

	if ($BASE == $base)
	  {
	    $tpl->assign(SELECTED, "selected");
	  } else {
	    $tpl->assign(SELECTED, "");
	  }
		
	$tpl->parse (BLOCK, ".base" );
      }
    
    $i++;
  }
  
  closedir($handle);







  $tpl->assign(REP_LANG_ADMIN, "$REP_LANG_ADMIN");

  $tpl->assign(LANG_ADMIN, "$LANG_ADMIN");

if (!$msg)
  {
    $tpl->assign(MESSAGE, "");
  } else {
    $tpl->assign(MESSAGE, "$msg");
  }

  
   $tpl->parse(HEADER, header) ; 
  $tpl->FastPrint("HEADER");

  $tpl->parse(CONF_DB, conf_db) ; 
  $tpl->FastPrint("CONF_DB");

  $tpl->parse(FOOTER, footer) ; 
  $tpl->FastPrint("FOOTER");  
  
  // Permet d'arrêter le cacul du temps et affichage du résultat en commentaire HTML
  // dans le code généré.
  $end = $tpl->utime();
  $run = $end - $start;
  echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
  exit;

 
}










function writefile($dbhost, $dbname, $dbuser, $dbpass, $rep_class, $rep_tpl, $ext_php, $ext_tpl, $use_mail, $func_mail, $mail, $site, $base, $rep_lang_admin, $lang_admin, $action, $what ) {

  $inf_file="../conf/hitweb.conf";
  $inf_back="../conf/hitweb.bak.conf";

  if($action=='enregistrer'){
    if(@copy($inf_file, $inf_back)){
      $msg = "Changes Saved." ;
    }else{
	  //si le fichier n'existe il n'y a pas de backup
	  // mais le fichier est créé et ensuite il y a un backup
      $msg = "Changes saved but $inf_file could not be backed up." ;
    }
  }

  $data="<?php\n";
  $data.="// DO NOT EDIT THIS FILE.  USE THE ADMIN\n";
  $data.="// http://localhost/admin/ \n\n";

  $data.="// Variables pour la base de données\n";
  $data.="  \$DBNAME = '$dbname';\n";
  $data.="  \$DBUSER = '$dbuser';\n";
  $data.="  \$DBPASS = '$dbpass';\n";
  $data.="  \$DBHOST = '$dbhost';\n";
  $data.="  \$BASE = '$base';\n\n";

  $data.="// Repertoires \n";
  $data.="  \$REP_CLASS = '$rep_class'; \n";
  $data.="  \$REP_TPL = '$rep_tpl'; \n\n";

  $data.="// Extension de fichier \n";
  $data.="  \$EXT_PHP = '$ext_php'; \n";
  $data.="  \$EXT_TPL = '$ext_tpl'; \n\n";

  $data.="// Option hébergeur & PHP \n";
  $data.="  \$USE_MAIL = '$use_mail'; \n";
  $data.="  \$FUNC_MAIL = '$func_mail'; \n\n";
  
  $data.="// Information sur le site \n";
  $data.="  \$MAIL = '$mail'; \n";
  $data.="  \$SITE = '$site'; \n\n";

  $data.="// Internationalisation de l'application  \n";
  $data.="  \$REP_LANG_ADMIN = '$rep_lang_admin'; \n";
  $data.="  \$LANG_ADMIN = '$lang_admin'; \n\n";


  $data.="\n\n";
  $data.="?>" ;

  // il faut bien entendu avoir les droits en écriture dans ce rep
  $fp = fopen("$inf_file", "w");
  fputs($fp, $data);
  fclose($fp);


  if ($what == "file")
    {
      conf_file($msg);
    } else {
      conf_db($msg);
    }
}





if ($action == "") $action="main" ;

switch ($action) {  
 
 case "main" : {
   conf_file($msg);
   break ;
 }

 case "db" : {
   conf_db($msg);
   break;
 }

 case "enregistrer" : {
   writefile($dbhost, $dbname, $dbuser, $dbpass, $rep_class, $rep_tpl, $ext_php, $ext_tpl, $use_mail, $func_mail, $mail, $site, $base, $rep_lang_admin, $lang_admin, $action, $what) ; 
   break ;
 }  
}
?>
