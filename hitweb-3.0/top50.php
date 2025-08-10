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
// $Id: top50.php,v 1.10 2001/06/19 22:44:14 hitweb Exp $



//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
//  Changer le liens pour que cette informations soit plus sécurisée
include "conf/hitweb.conf" ;


//########################################################################################
//# Fonction site du mois. Attention, c'est cette fonction qui fais marcher les templates
//########################################################################################
include "sitedumois".$EXT_PHP ;


//########################################################################################
//# Utilisation des CLASS FastTemplates et Base de données
//########################################################################################
include "$REP_CLASS/class.FastTemplate".$EXT_PHP ;
include "$REP_CLASS/class.db_$BASE".$EXT_PHP ;
include "$REP_CLASS/class.hitweb".$EXT_PHP ;


//########################################################################################
//# Fichier Meta avec DATE de dernière révision du document (automatique)
//########################################################################################
$date = date(  "Ymd", filemtime( $PATH_TRANSLATED ) );



function affiche($themes_id, $themes_nom, $sujets_id, $sujets_nom) {

  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db, $Hitweb ;
  global $tpl, $date ;
  global $EXT_PHP, $EXT_TPL, $REP_TPL ;
  


  $tpl = new FastTemplate("tpl/$REP_TPL/") ;
  
  $start = $tpl->utime();

  $tpl->define( array ( 
  		       header => "header".$EXT_TPL,
		       page => "top50".$EXT_TPL,
		       sitedumois => "sitedumois".$EXT_TPL,
		       footer => "footer".$EXT_TPL
			   )) ;
 
  $tpl->define_dynamic ( "top", "page" );
  
  // Insertion des informations sur les balises meta.
  include "meta".$EXT_PHP ;

  $tpl->assign (REP_TPL,"$REP_TPL");
  $tpl->assign(EXT_PHP,"$EXT_PHP");
  $tpl->assign (LIENS_CATEGORIES_ID,"");

  // Affichage de la barre de navigation dans les categories 
  $hitweb = new Hitweb ;
  $hitweb->navigBarCategorie($categories_parents_id, "index".$EXT_PHP, "html");
  $liste_categorie = $hitweb->$liste;
  $tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ; 
  

  $tpl->assign ( THEMES_ID, "$themes_id" ) ;
  $tpl->assign ( THEMES_NOM, "$themes_nom" ) ;
  $tpl->assign ( SUJETS_NOM, "$sujets_nom" ) ;


  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");

  //########################################################################################
  //# Cette requete affiche le top50 du site HITWEB
  //########################################################################################
  $sql = "SELECT LIENS_ID, LIENS_CATEGORIES_ID, LIENS_ADRESSE, LIENS_DESCRIPTION, LIENS_COMMENTAIRES_ID, LIENS_PROTOCOL_ID, ";
  $sql .= "sum(POINT_NB) AS nb ";
  $sql .= "FROM LIENS, POINT ";
  $sql .= "WHERE LIENS_ID = POINT_LIENS_ID ";
  $sql .= "AND LIENS_COMMENTAIRES_ID > '2' ";
  $sql .= "GROUP BY LIENS_ID ";
  $sql .= "ORDER BY nb DESC ";
  $sql .= "LIMIT 0,50 "; 

  $base->query("$sql");

  $num = $base->num_rows();
  
  if ($num>0)
  {
    
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
	  }
        else
	  {
	    $tpl->assign ( LIENS_NBCLICK, "$LIENS_NBCLICK" ) ;
	  }
      
      $LIENS_DESCRIPTION = stripslashes($LIENS_DESCRIPTION);
      
      $tpl->assign ( array ( LIENS_ID => $LIENS_ID,
	            		     LIENS_ADRESSE => $LIENS_ADRESSE,
	                         LIENS_DESCRIPTION => $LIENS_DESCRIPTION,
							 LIENS_PROTOCOL_ID => $LIENS_PROTOCOL_ID ));	
      
      $tpl->parse ( BLOCK, ".top" );
    }

  } else {
    $tpl->assign ( array ( LIENS_ID => "",
	                       LIENS_ADRESSE => "",
						   LIENS_NBCLICK => "",
	                       LIENS_DESCRIPTION => "",
						   LIENS_PROTOCOL_ID => "" ));	
      
  }
  
  //########################################################################################
  //# Affichage d'un nombre aléatoire pour l'affichage de la bannière de PUB
  //########################################################################################
  srand(time());
  
//prendre 10 num aléatoire de 1 à 12
  for ($index = 0; $index < 1; $index++)
    {
      $number = (rand()%12)+1;
      $tpl->assign ( NBANPUB, $number) ;
    }
  
  //#######################################################################################
  //# Affichage site du mois
  //########################################################################################
  
  sitedumois() ;

  //########################################################################################
  //# Configurations spécifique pour les différents Template
  //########################################################################################

  // TEMPLATE LITE 
  $tpl->assign ( MOT, "") ;

  
  $tpl->parse(HEADER, header) ; 
  $tpl->FastPrint("HEADER");

  $tpl->parse(PAGE, page) ; 
  $tpl->FastPrint("PAGE");

  $tpl->parse(SITEDUMOIS, sitedumois) ; 
  $tpl->FastPrint("SITEDUMOIS");
  

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
   affiche($themes_id, $themes_nom, $sujets_id, $sujets_nom) ;
   break ;
 }  
 
}

?>
