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
// $Id: appreciation.php,v 1.9 2001/06/19 22:44:14 hitweb Exp $


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


function affiche($data) {

  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db, $Hitweb ;
  global $tpl, $date ;
  global $EXT_PHP, $EXT_TPL, $REP_TPL ;
  $tpl = new FastTemplate("tpl/$REP_TPL/") ;
  
  $start = $tpl->utime();

  $tpl->define( array ( 
  		       header => "header".$EXT_TPL,
		       page => "appreciation".$EXT_TPL,
		       sitedumois => "sitedumois".$EXT_TPL,
		       footer => "footer".$EXT_TPL
			   )) ;
 
  $tpl->define_dynamic ( "top", "page" );
  
  // Insertion des informations sur les balises meta.
  include "meta".$EXT_PHP ;

  $tpl->assign(REP_TPL,"$REP_TPL");
  $tpl->assign (EXT_PHP,"$EXT_PHP");
  $tpl->assign (LIENS_CATEGORIES_ID,"");

  // Affichage de la barre de navigation dans les categories 
  $hitweb = new Hitweb ;
  $hitweb->navigBarCategorie($categories_parents_id, "index".$EXT_PHP, "html");
  $liste_categorie = $hitweb->$liste;
  $tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ; 


  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");


  //########################################################################################
  //# Résultat du vote pour l'appreciation du site HITWEB
  //########################################################################################

  $sql3 = "SELECT SUM(VOTE_NB) FROM VOTE";

  $base->query("$sql3");

  $VOTE_TOTAL = $base->result($row, 0);

  $tpl->assign (VOTE_TOTAL,"$VOTE_TOTAL");


  $sql = "SELECT VOTE_ID, VOTE_TEXT, VOTE_NB FROM VOTE ";
  $sql .= "ORDER BY VOTE_NB DESC ";

  $base->query("$sql");

	while (list ( $VOTE_ID,
				  $VOTE_TEXT,
				  $VOTE_NB ) = $base->fetch_row())
	{

    $VOTE_NB1 = $VOTE_NB;
    $VOTE_NB = round(($VOTE_NB * 100) / $VOTE_TOTAL) ;
     
    $tpl->assign ( array ( VOTE_TEXT => $VOTE_TEXT,
                           VOTE_NB1 => $VOTE_NB1,
			   VOTE_NB => $VOTE_NB ));
     
     $tpl->parse ( BLOCK, ".top" );
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

function vote($MAILIST_VOTE) {

  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;
  global $tpl, $date ;


  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");

  //########################################################################################
  //# Enregistrement des informations de mes visiteurs dans la base 
  //# MAILIST...
  //########################################################################################

  $sql = "UPDATE VOTE ";
  $sql .= "SET VOTE_NB = VOTE_NB + 1 ";
  $sql .= "WHERE VOTE_TEXT='$MAILIST_VOTE' ";

  $base->query("$sql");

  // Afficher de nouveau cette page, mais maintenant avec le nouveau % du vote
  affiche($date) ;
}


if ($action == "") $action="main" ;

switch ($action) {
  
 case "main" : {
   affiche($date) ;
   break ;
 }  
 
 case "Voter" : {
   vote($MAILIST_VOTE) ;
   break ;
 }  
 
}

?>
