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
// $Id: index.php,v 1.13 2001/09/18 21:43:17 hitweb Exp $


//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
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


function affiche($categories_parents_id) {
  
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db, $Hitweb, $date ;	
  global $tpl ;
  global $EXT_PHP, $EXT_TPL, $REP_TPL ;


  $tpl = new FastTemplate("tpl/$REP_TPL/") ;

  $start = $tpl->utime();

  $tpl->define( array ( 
		       header => "header".$EXT_TPL,
		       categories => "categories".$EXT_TPL,
		       liens => "liens".$EXT_TPL,
		       sitedumois => "sitedumois".$EXT_TPL,
		       footer => "footer".$EXT_TPL
		       )) ;
 
  $tpl->define_dynamic ( "cat", "categories" );
  $tpl->define_dynamic ( "lien", "liens" );


  // Insertion des informations sur les balises meta.
  include "meta".$EXT_PHP ;

  $tpl->assign(REP_TPL,"$REP_TPL");
  $tpl->assign(EXT_PHP,"$EXT_PHP"); 
  $tpl->assign(LIENS_CATEGORIES_ID, $categories_parents_id);
  

  // Affichage de la barre de navigation dans les categories 
  $hitweb = new Hitweb ;
  $hitweb->navigBarCategorie($categories_parents_id, "index".$EXT_PHP, "html");
  $liste_categorie = $hitweb->$liste;
  $tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ; 
 


  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");

  if (!$categories_parents_id)
    {
      $categories_parents_id = 0;
      $tpl->assign ( LIENS_CATEGORIES_ID, "") ; 
    } 
  
  $sql = "SELECT CATEGORIES_ID, CATEGORIES_NOM ";
  $sql .= "FROM CATEGORIES ";
  $sql .= "WHERE CATEGORIES_PARENTS = '$categories_parents_id' "; 
  $sql .= "ORDER BY CATEGORIES_NOM";
  
  $base->query("$sql");
  
  $num = $base->num_rows();
  
  if ($num == 0)
    {
      $tpl->assign ( DEBUT_TABLE, "") ;
      $tpl->assign ( CATEGORIES_ID, "0") ;
      $tpl->assign ( CATEGORIES_NOM, "") ;
      $tpl->assign ( NUM_LIENS, "") ;
	  
      $tpl->parse ( BLOCK, ".cat" );
    } else {
      
      $i=0;
      while (list ( $CATEGORIES_ID,
		    $CATEGORIES_NOM ) = $base->fetch_row())
	{

	  
	  // Récupération du nb de liens dans les categories
	  $db = new class_db ;
	  //$db->debug = 1; 
	  $db->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
	  
	  $sql2 = "SELECT count(LIENS_ID) as numcat ";
	  $sql2 .= "FROM LIENS ";
	  $sql2 .= "WHERE LIENS_CATEGORIES_ID = '$CATEGORIES_ID' ";
	  $sql2 .= "AND LIENS_COMMENTAIRES_ID = '3' ";
	  
	  $db->query("$sql2");
	  
	  $NUM_LIENS =  $db->result($row, 0);
	  


	  if($i == 4)
	    { 
	      $CATEGORIES_NOM2 = urlencode($CATEGORIES_NOM);
	      
	      $tpl->assign ( DEBUT_TABLE, "</tr><tr>") ;
	      $tpl->assign ( CATEGORIES_ID, "$CATEGORIES_ID") ;
	      $tpl->assign ( CATEGORIES_NOM, "$CATEGORIES_NOM") ;
	      $tpl->assign ( NUM_LIENS, "$NUM_LIENS") ;
	      
	      $tpl->parse ( BLOCK, ".cat" );
	      $i=1;
	      
	    } else {
	      $CATEGORIES_NOM2 = urlencode($CATEGORIES_NOM);
	      
	      $tpl->assign ( DEBUT_TABLE, "") ;
      
	      $tpl->assign ( CATEGORIES_ID, "$CATEGORIES_ID") ;
	      $tpl->assign ( CATEGORIES_NOM, "$CATEGORIES_NOM") ;
	      $tpl->assign ( NUM_LIENS, "$NUM_LIENS") ;
	      
	      $i++;
	      $tpl->parse ( BLOCK, ".cat" );
	    }
	}
  }





  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
  
  //########################################################################################
  //# Cette requete permet d'afficher tous les sites qui sont 
  //# dans la table liens, il y a donc le site de la semaine + les liens de la base
  //# Plus le calcul du nb de click par site...
  //########################################################################################
  
  if (!$categories_parents_id)
    {
      $categories_parents_id = 0;
    }

  $sql = "SELECT LIENS_ID, LIENS_CATEGORIES_ID, LIENS_ADRESSE, LIENS_DESCRIPTION, LIENS_COMMENTAIRES_ID, LIENS_PROTOCOL_ID, ";
  $sql .= "sum(POINT_NB) AS nb ";
  $sql .= "FROM LIENS, POINT ";
  $sql .= "WHERE LIENS_ID = POINT_LIENS_ID ";
  $sql .= "AND LIENS_CATEGORIES_ID=$categories_parents_id "; 
  $sql .= "AND LIENS_COMMENTAIRES_ID > '2' ";
  $sql .= "GROUP BY LIENS_ID ";
  $sql .= "ORDER BY nb DESC ";
  
  $base->query("$sql");
  
  $num = $base->num_rows();  

  if ($num > 0)
    {
      $tpl->assign(BARE, "<p><hr width=50%><p>");
      
      while (list ( $LIENS_ID,
		    $LIENS_CATEGORIES_ID,
		    $LIENS_ADRESSE,
		    $LIENS_DESCRIPTION,
		    $LIENS_COMMENTAIRES_ID,
		    $LIENS_PROTOCOL_ID,
		    $LIENS_NBCLICK ) = $base->fetch_row())
	
	{
	  
	  if ($LIENS_NBCLICK == 0)
	    {
	      $tpl->assign ( LIENS_NBCLICK, "New" ) ;
	    } else  {
	      $tpl->assign ( LIENS_NBCLICK, "$LIENS_NBCLICK" ) ;
	    }
	  
	  
	  $LIENS_DESCRIPTION = stripslashes($LIENS_DESCRIPTION);
	  
	  $tpl->assign ( array ( LIENS_ID => $LIENS_ID,
				 LIENS_CATEGORIES_ID => $LIENS_CATEGORIES_ID,
				 LIENS_ADRESSE => $LIENS_ADRESSE,
				 LIENS_DESCRIPTION => $LIENS_DESCRIPTION,
				 LIENS_PROTOCOL_ID => $LIENS_PROTOCOL_ID ));	
	  
	  $tpl->parse ( BLOCK_LIENS, ".lien" );
	}
      
    } else {
      $tpl->assign ( array ( LIENS_ID => "",
			     LIENS_ADRESSE => "",
			     LIENS_DESCRIPTION => "",
			     LIENS_PROTOCOL_ID => "",
			     LIENS_NBCLICK => "",
			     BARE => "" ));	
      
      $tpl->parse ( BLOCK_LIENS, ".lien" );
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
  
  
  //########################################################################################
  //# Affichage site du mois - revoir comment mettre cette fonction dans un autre fichier
  //########################################################################################
  sitedumois() ;


  //########################################################################################
  //# Configurations spécifique pour les différents Template
  //########################################################################################

  // TEMPLATE LITE 
  $tpl->assign ( MOT, "") ;



  $tpl->parse(HEADER, header) ; 
  $tpl->FastPrint("HEADER");

  $tpl->parse(CATEGORIES, categories) ; 
  $tpl->FastPrint("CATEGORIES");

  $tpl->parse(LIENS, liens) ; 
  $tpl->FastPrint("LIENS");

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

// Test pour savoir s'il y a des pages statiques (redirect de index.php -> index.html)
// Cette redirection n'est pas faites pendant la génération des pages

if(!$genpage)
{
  $fp = @fopen("http://$SITE/index.html","r");
  
  if($fp)
    {
      header("Location:http://$SITE/index.html");
    } 
}



if ($action == "") $action="main" ;

switch ($action) {
  
 case "main" : {
   affiche($categories_parents_id) ;
   break ;
 }  
 
}

?>
