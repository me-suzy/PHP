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
// |                                                                      |
// | http://www.gnu.org/copyleft/gpl.html                                 |
// +----------------------------------------------------------------------+
// | Authors : Brian FRAVAL <brian@fraval.org>                            |
// +----------------------------------------------------------------------+
//
// $Id: index.php,v 1.8 2001/06/19 22:54:26 hitweb Exp $

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




function affiche()
{
  global $tpl ;
  global $EXT_PHP, $EXT_TPL ;
  global $title_admin, $title_som_admin, $link_conf_db, $link_conf_file, $link_application, $link_valid_url, $link_polls, $link_check_url, $link_international_admin, $licence;


  $tpl = new FastTemplate("tpl/");

  $start = $tpl->utime();


  $tpl->define( array ( 
		       header => "header".$EXT_TPL,
		       index => "index".$EXT_TPL,
		       footer => "footer".$EXT_TPL
			)) ;
  

  $tpl->assign(
	       array(
		     EXT_PHP => "$EXT_PHP",
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
		     ALIGN => ""
		     )
	       );

  $tpl->parse(HEADER, header) ; 
  $tpl->FastPrint("HEADER");

  $tpl->parse(INDEX, index) ; 
  $tpl->FastPrint("INDEX");

  $tpl->parse(FOOTER, footer) ; 
  $tpl->FastPrint("FOOTER");
  
  // Permet d'arrêter le cacul du temps et affichage du résultat en commentaire HTML
  // dans le code généré.
  $end = $tpl->utime();
  $run = $end - $start;
  echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
  exit;
  

}




if ($action == "") $action="main" ;

switch ($action) {  
 
 case "main" : {
   affiche() ;
   break ;
 }

}


?>
