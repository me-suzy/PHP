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
// $Id: lienavalider.php,v 1.7 2001/06/19 22:54:26 hitweb Exp $

//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
//  Changer le liens pour que cette informations soit plus sécurisée
include "../conf/hitweb.conf" ;


//########################################################################################
//# Utilisation des CLASS FastTemplates et Base de données
//########################################################################################
include "../$REP_CLASS/class.FastTemplate".$EXT_PHP ;
include "../$REP_CLASS/class.db_$BASE".$EXT_PHP ;


//########################################################################################
//# Internationalisation de la partie administration
//########################################################################################

include "$REP_LANG_ADMIN/$LANG_ADMIN".$EXT_PHP ;





function affichage() {

  global $tpl ;
  global $EXT_PHP, $EXT_TPL ;
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;
  global $title_admin, $title_som_admin, $link_conf_db, $link_conf_file, $link_application, $link_valid_url, $link_polls, $link_check_url, $link_international_admin, $licence, $link_update;
  
  
  $tpl = new FastTemplate("tpl/");

  $start = $tpl->utime();


  $tpl->define( array ( 
			header => "header".$EXT_TPL,
			liensavalider => "liensavalider".$EXT_TPL,
			footer => "footer".$EXT_TPL
			)) ;

  $tpl->define_dynamic ( "top", "liensavalider" );

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
		     LINK_UPDATE => "$link_update",
		     LICENCE => "$licence",
		     ALIGN => ""
		     )
	       );



  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");


  //########################################################################################
  //# Cette requete permet d'afficher tous les sites qui sont à valider
  //########################################################################################

  $sql = "SELECT LIENS_ID, LIENS_CATEGORIES_ID, LIENS_ADRESSE, LIENS_DESCRIPTION, LIENS_COMMENTAIRES_ID, LIENS_PROTOCOL_ID, ";
  $sql .= "sum(POINT_NB) AS nb ";
  $sql .= "FROM LIENS, POINT ";
  $sql .= "WHERE LIENS_ID = POINT_LIENS_ID ";
  $sql .= "AND LIENS_COMMENTAIRES_ID='2' ";
  $sql .= "GROUP BY LIENS_ID ";
  $sql .= "ORDER BY nb DESC ";


  $base->query("$sql");
  
  $num = $base->num_rows();
  
  
  if ($num > 0) {
  
    while (list ( $LIENS_ID,
		  $LIENS_SUJETS_ID,
		  $LIENS_ADRESSE,
		  $LIENS_DESCRIPTION,
		  $LIENS_COMMENTAIRES_ID, 
		  $LIENS_PROTOCOL_ID,
		  $LIENS_NBCLICK ) = $base->fetch_row())
    {
      if ($LIENS_NBCLICK == 0)
	  {
	    $tpl->assign ( LIENS_NBCLICK, "New" ) ;
	  } else {
	    $tpl->assign ( LIENS_NBCLICK, "$LIENS_NBCLICK" ) ;
	  }
      
      $LIENS_DESCRIPTION = stripslashes($LIENS_DESCRIPTION);
      
      $tpl->assign ( array ( LIENS_ID => $LIENS_ID,
			     LIENS_SUJETS_ID => $LIENS_SUJETS_ID,
			     LIENS_ADRESSE => $LIENS_ADRESSE,
			     LIENS_DESCRIPTION => $LIENS_DESCRIPTION ));	
      

      $db = new class_db ;
      //$db->debug = 1; 
      $db->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
      
      $sql  = "SELECT PROTOCOL_NOM FROM PROTOCOL WHERE PROTOCOL_ID = $LIENS_PROTOCOL_ID ";
      
      $db->query("$sql");
      
      $PROTOCOL_NOM = $db->result($row, 0);
      
      $tpl->assign(LIENS_PROTOCOL_ID, "$LIENS_PROTOCOL_ID");  
      $tpl->assign(PROTOCOL_NOM ,"$PROTOCOL_NOM");
      

      
      $tpl->parse ( BLOCK, ".top" );
    }

} else {

  $tpl->assign ( array ( LIENS_ID => "",
                         LIENS_ADRESSE => "",
			 LIENS_PROTOCOL_ID => "",
			 LIENS_DESCRIPTION => "",
			 LIENS_NBCLICK => "", 
			 PROTOCOL_NOM => "" ));
}



  $tpl->parse(HEADER, header) ; 
  $tpl->FastPrint("HEADER");

  $tpl->parse(LIENSAVALIDER, liensavalider) ; 
  $tpl->FastPrint("LIENSAVALIDER");

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
   affichage() ;
   break ;
 }  

}

?>
