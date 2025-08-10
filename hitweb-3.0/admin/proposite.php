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
// $Id: proposite.php,v 1.9 2001/07/12 07:39:01 hitweb Exp $


//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
include "../conf/hitweb.conf" ;


//########################################################################################
//# CLASS FastTemplate en PHP
//########################################################################################
include "../$REP_CLASS/class.FastTemplate".$EXT_PHP ;
include "../$REP_CLASS/class.db_$BASE".$EXT_PHP ;
include "../$REP_CLASS/class.hitweb".$EXT_PHP ;


//########################################################################################
//# Analyse URL + enregistrement dans la base
//########################################################################################
include "ajoutsite".$EXT_PHP ;

//########################################################################################
//# Internationalisation de la partie administration
//########################################################################################

include "$REP_LANG_ADMIN/$LANG_ADMIN".$EXT_PHP ;



function getProtocol($LIENS_PROTOCOL_ID)
{
  global $class_db ;
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $tpl;

// Affichage des protoles
 $base = new class_db ;
 //$base->debug = 1; 
 $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");

 $sql = "SELECT PROTOCOL_ID, PROTOCOL_NOM FROM PROTOCOL  ";
 
 $base->query("$sql");


 $num = $base->num_rows();
 
 if ($num > 0)
   {
     while (list ( $PROTOCOL_ID,
		   $PROTOCOL_NOM ) = $base->fetch_row())
      {
        $tpl->assign (PROTOCOL_ID, "$PROTOCOL_ID") ;
        $tpl->assign (PROTOCOL_NOM, "$PROTOCOL_NOM") ;

	if($LIENS_PROTOCOL_ID == $PROTOCOL_ID)
	  {
	    $tpl->assign (SELECTED, "SELECTED") ;
	  } else {
	    $tpl->assign (SELECTED, "") ;
	  }
	
	$tpl->parse (BLOCK, ".protocol" );
      }

   } else {
     $tpl->assign (PROTOCOL_ID, "") ;
     $tpl->assign (PROTOCOL_NOM, "") ;
   }
    
}





function affiche($LIENS_CATEGORIES_ID) {
  
  global $tpl ;
  global $EXT_TPL, $EXT_PHP ;
  global $class_db ;
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $title_admin, $title_som_admin, $link_conf_db, $link_conf_file, $link_application, $link_valid_url, $link_polls, $link_check_url, $link_international_admin, $licence;
  global $lib_name, $lib_lastname, $lib_mail, $lib_address, $lib_keyword, $lib_subject, $lib_description, $bt_enre, $bt_reset;
  
  $tpl = new FastTemplate( "tpl/") ;
  
  $start = $tpl->utime();
  
  $tpl->define( array ( 
		       header => "header".$EXT_TPL,
		       proposite => "proposite".$EXT_TPL,
		       footer => "footer".$EXT_TPL
		       )) ;

  $tpl->define_dynamic ( "protocol", "proposite" );
  
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

 $tpl->assign(
	      array(
		    LIB_NAME => "$lib_name",
		    LIB_LASTNAME => "$lib_lastname",
		    LIB_MAIL => "$lib_mail",
		    LIB_ADDRESS => "$lib_address",
		    LIB_KEYWORD => "$lib_keyword",
		    LIB_SUBJECT => "$lib_subject",
		    LIB_DESCRIPTION => "$lib_description",
		    BT_ENRE => "$bt_enre",
		    BT_RESET => "$bt_reset"
		    )
	      );
 
 $tpl->assign(
	      array(
		    MESSAGE => "",
		    WEBMASTER_NOM => "",
		    WEBMASTER_PRENOM => "",
		    WEBMASTER_EMAIL => "",
		    LIENS_ADRESSE => "",
		    LIENS_RECHERCHE => "",
		    LIENS_DESCRIPTION => "",
		    LIENS_CATEGORIES_ID => "$LIENS_CATEGORIES_ID",
		    ACTION => "enregistrer"
		    )
	      );


 // Affichage de la barre de navigation dans les categories 
 $hitweb = new Hitweb ;
 $hitweb->navigBarCategorie($categories_parents_id, "index".$EXT_PHP, "html");
 $liste_categorie = $hitweb->$liste;
 $tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ; 

 // Categorie version texte, pas de HTML
 $hitweb2 = new Hitweb ;
 #$hitweb2->navigBarCategorie($categories_parents_id, "index".$EXT_PHP, ""); 
 $hitweb2->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "");
 $liste_categorie = $hitweb2->$liste;
 $tpl->assign ( LISTE_CATEGORIE_NOHTML, $liste_categorie) ; 


 getProtocol("1");


 $tpl->parse(HEADER, header) ; 
 $tpl->FastPrint("HEADER");
 
 $tpl->parse(PROPOSITE, proposite) ; 
 $tpl->FastPrint("PROPOSITE");
 
 $tpl->parse(FOOTER, footer) ; 
 $tpl->FastPrint("FOOTER");
 
 // Permet d'arrêter le cacul du temps et affichage du résultat en commentaire HTML
 // dans le code généré.
 $end = $tpl->utime();
 $run = $end - $start;
 echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
 exit;
}







function enregistrer($WEBMASTER_NOM, $WEBMASTER_PRENOM, $WEBMASTER_EMAIL, $LIENS_PROTOCOL_ID, $LIENS_ADRESSE, $LIENS_RECHERCHE, $LIENS_DESCRIPTION, $LIENS_CATEGORIES_ID) {
  
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;
  global $tpl ;
  global $EXT_PHP, $EXT_TPL ;
  global $title_admin, $title_som_admin, $link_conf_db, $link_conf_file, $link_application, $link_valid_url, $link_polls, $link_check_url, $link_international_admin, $licence;
  global $lib_name, $lib_lastname, $lib_mail, $lib_address, $lib_keyword, $lib_subject, $lib_description;
  global $bt_enre, $bt_reset, $mes_fields_empty, $mes_link_in_hitweb ;
  


  $tpl = new FastTemplate( "tpl/") ;
  
  $start = $tpl->utime();
  

  if (($WEBMASTER_NOM == "") or ($WEBMASTER_PRENOM == "") or ($WEBMASTER_EMAIL == "") or ($LIENS_ADRESSE == "") or ($LIENS_RECHERCHE == "") or ($LIENS_DESCRIPTION == ""))
    {
      
      $tpl->define( array ( 
			   header => "header".$EXT_TPL,
			   proposite => "proposite".$EXT_TPL,
			   footer => "footer".$EXT_TPL
			   )) ;
      
      $tpl->define_dynamic ( "protocol", "proposite" );
      
      
      $tpl->assign ( MESSAGE, "
      <center><b><font color='#FF0000'>$mes_fields_empty</font></b></center>
      ") ;
      
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
      
      $tpl->assign(
		   array(
			 LIB_NAME => "$lib_name",
			 LIB_LASTNAME => "$lib_lastname",
			 LIB_MAIL => "$lib_mail",
			 LIB_ADDRESS => "$lib_address",
			 LIB_KEYWORD => "$lib_keyword",
			 LIB_SUBJECT => "$lib_subject",
			 LIB_DESCRIPTION => "$lib_description",
			 ACTION => "enregistrer",
			 BT_ENRE => "$bt_enre",
			 BT_RESET => "$bt_reset"
			 )
		   );
      
      //Pour l'affichage dans le formulaire
      $WEBMASTER_NOM = stripslashes($WEBMASTER_NOM) ;
      $WEBMASTER_PRENOM = stripslashes($WEBMASTER_PRENOM) ;
      $WEBMASTER_EMAIL = stripslashes($WEBMASTER_EMAIL) ;
      $LIENS_ADRESSE = stripslashes($LIENS_ADRESSE) ;
      $LIENS_RECHERCHE = stripslashes($LIENS_RECHERCHE) ;
      $LIENS_DESCRIPTION = stripslashes($LIENS_DESCRIPTION) ;
      
      $tpl->assign ( array ( WEBMASTER_NOM => $WEBMASTER_NOM,
                             WEBMASTER_PRENOM => $WEBMASTER_PRENOM,
                             WEBMASTER_EMAIL => $WEBMASTER_EMAIL,		     
						     LIENS_ADRESSE => $LIENS_ADRESSE,
						     LIENS_RECHERCHE => $LIENS_RECHERCHE,
						     LIENS_DESCRIPTION => $LIENS_DESCRIPTION,
						     LIENS_CATEGORIES_ID => $LIENS_CATEGORIES_ID ));
      

      // Affichage de la barre de navigation dans les categories 
      $hitweb = new Hitweb ;
      $hitweb->navigBarCategorie($categories_parents_id, "index".$EXT_PHP, "html");
      $liste_categorie = $hitweb->$liste;
      $tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ; 
      
      $hitweb2 = new Hitweb ;
      $hitweb2->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "");
      $liste_categorie = $hitweb2->$liste;
      $tpl->assign ( LISTE_CATEGORIE_NOHTML, $liste_categorie) ; 
      
      
      getProtocol($LIENS_PROTOCOL_ID);
      
      
      $tpl->parse(HEADER, header) ; 
      $tpl->FastPrint("HEADER");
      
      $tpl->parse(PROPOSITE, proposite) ; 
      $tpl->FastPrint("PROPOSITE");
      
      $tpl->parse(FOOTER, footer) ; 
      $tpl->FastPrint("FOOTER");
      
      // Permet d'arrêter le cacul du temps et affichage du résultat en commentaire HTML
      // dans le code généré.
      $end = $tpl->utime();
      $run = $end - $start;
      echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
      exit;
      
      
      
    
    } else {
      
      $tpl->define( array ( 
			   header => "header".$EXT_TPL,
			   proposite => "proposite".$EXT_TPL,
			   footer => "footer".$EXT_TPL
			   )) ;
      
      $tpl->define_dynamic ( "protocol", "proposite" );
      
      
      //Analyser si l'URL exite deja dans la base HITWEB	
      $base = new class_db ;
      //$base->debug = 1; 
      $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
      
      
      // Le fait de rechercher toutes les adresse avec l'adresse + %
      // Permet d'annuler toutes les personnes qui rajoute un autre fichier
      // dans leurs adresses pour passer ce TEST.. arf !!! comme cela il n'y a
      // moins de pb pour gérer les infos de la base de données.
      
      // Par contre si une personne ajoute un rep la je ne peux pas
      // Regarder vraiment la validité... Mais ceci est aussi normal 
      // Car sinon si quelqu'un enregistre www.chez.com, je ne peux pas
      // Ajouter www.chez.com/brian/
      
      $sqlverif = "SELECT LIENS_ADRESSE ";
      $sqlverif .= "FROM LIENS ";
      $sqlverif .= "WHERE LIENS_ADRESSE LIKE '$LIENS_ADRESSE' ";
      
      $base->query("$sqlverif");
      
      $numverif = $base->num_rows() ;
      
      
      if ($numverif > 0) {
	
	$tpl->assign ( MESSAGE, "
      <center><b><font color='#FF0000'>$mes_link_in_hitweb</font></b></center>
      ") ;
	
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
	
	$tpl->assign(
		     array(
			   LIB_NAME => "$lib_name",
			   LIB_LASTNAME => "$lib_lastname",
			   LIB_MAIL => "$lib_mail",
			   LIB_ADDRESS => "$lib_address",
			   LIB_KEYWORD => "$lib_keyword",
			   LIB_SUBJECT => "$lib_subject",
			   LIB_DESCRIPTION => "$lib_description",
			   ACTION => "enregistrer",
			   BT_ENRE => "$bt_enre",
			   BT_RESET => "$bt_reset"
			   )
		     );
	
	
	
	//Pour l'affichage dans le formulaire
	$WEBMASTER_NOM = stripslashes($WEBMASTER_NOM) ;
	$WEBMASTER_PRENOM = stripslashes($WEBMASTER_PRENOM) ;
	$WEBMASTER_EMAIL = stripslashes($WEBMASTER_EMAIL) ;
	$LIENS_ADRESSE = stripslashes($LIENS_ADRESSE) ;
	$LIENS_RECHERCHE = stripslashes($LIENS_RECHERCHE) ;
	$LIENS_DESCRIPTION = stripslashes($LIENS_DESCRIPTION) ;
	
	
	$tpl->assign ( array ( WEBMASTER_NOM => $WEBMASTER_NOM,
			       WEBMASTER_PRENOM => $WEBMASTER_PRENOM,
			       WEBMASTER_EMAIL => $WEBMASTER_EMAIL,		     
			       LIENS_ADRESSE => $LIENS_ADRESSE,
			       LIENS_RECHERCHE => $LIENS_RECHERCHE,
			       LIENS_DESCRIPTION => $LIENS_DESCRIPTION,
			       LIENS_CATEGORIES_ID => $LIENS_CATEGORIES_ID ));
	
	
	// Affichage de la barre de navigation dans les categories 
	$hitweb = new Hitweb ;
	$hitweb->navigBarCategorie($categories_parents_id, "index".$EXT_PHP, "html");
	$liste_categorie = $hitweb->$liste;
	$tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ; 
	
	$hitweb2 = new Hitweb ;
	$hitweb2->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "");
	$liste_categorie = $hitweb2->$liste;
	$tpl->assign ( LISTE_CATEGORIE_NOHTML, $liste_categorie) ; 

	
	getProtocol($LIENS_PROTOCOL_ID);
	
	$tpl->parse(HEADER, header) ; 
	$tpl->FastPrint("HEADER");
	
	$tpl->parse(PROPOSITE, proposite) ; 
	$tpl->FastPrint("PROPOSITE");
	
	$tpl->parse(FOOTER, footer) ; 
	$tpl->FastPrint("FOOTER");
	
	// Permet d'arrêter le cacul du temps et affichage du résultat en commentaire HTML
	// dans le code généré.
	$end = $tpl->utime();
	$run = $end - $start;
	echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
	exit;
	
	
      } else {
	
	//Analyse l'existance de l'URL sur le net
	analyse_url($WEBMASTER_NOM, $WEBMASTER_PRENOM, $WEBMASTER_EMAIL, $LIENS_PROTOCOL_ID, $LIENS_ADRESSE, $LIENS_RECHERCHE, $LIENS_DESCRIPTION, $LIENS_CATEGORIES_ID, $CHOIX_SUJETS_NOM) ;
	
	
      } // End Check URL + insert URL
      
    } // End if check input
  
} // End function enregistre
  


if ($action == "") $action="main" ;

switch ($action) {
 case "main" : {
   affiche($LIENS_CATEGORIES_ID) ;
   break ;
 }  
 
 case "enregistrer" : {
   enregistrer($WEBMASTER_NOM, $WEBMASTER_PRENOM, $WEBMASTER_EMAIL, $LIENS_PROTOCOL_ID, $LIENS_ADRESSE, $LIENS_RECHERCHE, $LIENS_DESCRIPTION, $LIENS_CATEGORIES_ID) ;
   break ;
 }  
  
}

?>
