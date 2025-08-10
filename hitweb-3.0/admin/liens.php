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
// $Id: liens.php,v 1.5 2001/06/19 22:54:26 hitweb Exp $

//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
//  Changer le liens pour que cette informations soit plus sécurisée
include "../conf/hitweb.conf" ;


//########################################################################################
//# CLASS FastTemplate en PHP
//########################################################################################
include "../$REP_CLASS/class.FastTemplate".$EXT_PHP ;
include "../$REP_CLASS/class.db_$BASE".$EXT_PHP ;
include "../$REP_CLASS/class.hitweb".$EXT_PHP ;


//########################################################################################
//# Internationalisation de la partie administration
//########################################################################################

include "$REP_LANG_ADMIN/$LANG_ADMIN".$EXT_PHP ;


/*
function getCategorie($LIENS_CATEGORIES_ID)
{
  global $tpl, $EXT_PHP;
  $hitweb = new Hitweb ;
  
  // Affichage de la categories
  $hitweb->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP);
  $liste_categorie = $hitweb->$liste;
  $tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ;
}
*/

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


function getCommentaire($LIENS_COMMENTAIRES_ID)
{
  global $class_db ;
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $tpl;
  
  // Affichage des protoles
  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
  
  $sql = "SELECT COMMENTAIRES_ID, COMMENTAIRES_TEXTE FROM COMMENTAIRES  ";
  
  $base->query("$sql");
  
  $num = $base->num_rows();

  
  if ($num > 0)
    {
      while (list ( $COMMENTAIRES_ID,
		    $COMMENTAIRES_TEXTE ) = $base->fetch_row())
	{
	  $tpl->assign (COMMENTAIRES_ID, "$COMMENTAIRES_ID") ;
	  $tpl->assign (COMMENTAIRES_TEXTE, "$COMMENTAIRES_TEXTE") ;
	  
	  if($LIENS_COMMENTAIRES_ID == $COMMENTAIRES_ID)
	    {
	      $tpl->assign (CHOIXCOM, "SELECTED") ;
	    } else {
	      $tpl->assign (CHOIXCOM, "") ;
	    }
	  
	  $tpl->parse (BLOCK_COMMENTAIRE, ".commentaire" );
	}
      
    } else {
      $tpl->assign (COMMENTAIRES_ID, "") ;
      $tpl->assign (COMMENTAIRES_TEXTE, "") ;
    }
}








function affiche($liens_id, $message) {

  global $tpl ;
  global $EXT_PHP, $EXT_TPL ;
  global $class_db, $Hitweb ;
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $title_admin, $title_som_admin, $link_conf_db, $link_conf_file, $link_application, $link_valid_url, $link_polls, $link_check_url, $link_international_admin, $licence;
  global $lib_name, $lib_lastname, $lib_mail, $lib_address, $lib_keyword, $lib_subject, $lib_description, $lib_id, $mes_update_link, $bt_enre, $bt_reset;


  $tpl = new FastTemplate( "tpl/") ;

  $start = $tpl->utime();

  $tpl->define( array ( 
		       header => "header".$EXT_TPL,
		       modiflien => "modiflien".$EXT_TPL,
		       footer => "footer".$EXT_TPL
		       )) ;


  $tpl->define_dynamic ( "protocol", "modiflien" );
  $tpl->define_dynamic ( "commentaire", "modiflien" );
   

  if (isset($message))
    {
      $tpl->assign(MESSAGE,"$mes_update_link");
    } else {
      $tpl->assign(MESSAGE,"");
    }

  $tpl->assign(
	       array(
		     LIENS_ID => "$liens_id",
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
		     ALIGN => "center"
		     )
	       );


 $tpl->assign(
	      array(
		    LIB_ID => "$lib_id",
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
 
 
 //
 //  Recuperation du webaster qui a enregistrer son liens dans HITWEB
 // 
 $base = new class_db ;
 //$base->debug = 1; 
 $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
 
 $sql = "SELECT WEBMASTER_NOM, WEBMASTER_PRENOM, WEBMASTER_EMAIL FROM WEBMASTER WHERE WEBMASTER_LIENS_ID = $liens_id ";
 
 $base->query("$sql");

 $num = $base->num_rows();

 if ($num > 0)
   {
     while (list ( $WEBMASTER_NOM,
		   $WEBMASTER_PRENOM,
		   $WEBMASTER_EMAIL ) = $base->fetch_row())
       {
	 
	 $tpl->assign (
		       array(
			     WEBMASTER_NOM => "$WEBMASTER_NOM",
			     WEBMASTER_PRENOM => "$WEBMASTER_PRENOM",
			     WEBMASTER_EMAIL => "$WEBMASTER_EMAIL"
			     )
		       );
       }
     
   } else {
     
     $tpl->assign(
		  array(
			WEBMASTER_NOM => "",
			WEBMASTER_PRENOM => "",
			WEBMASTER_EMAIL => ""
			)
		  );
   }

 
 //
 // Recuperation sur le lien
 //

 $sql = "SELECT LIENS_CATEGORIES_ID, LIENS_ADRESSE, LIENS_DESCRIPTION, LIENS_COMMENTAIRES_ID, LIENS_RECHERCHE, LIENS_PROTOCOL_ID ";
 $sql .= "FROM LIENS WHERE LIENS_ID = $liens_id ";
 
 $base->query("$sql");

 $num = $base->num_rows();

 if ($num > 0)
   {
     while (list ( $LIENS_CATEGORIES_ID,
		   $LIENS_ADRESSE,
		   $LIENS_DESCRIPTION,
		   $LIENS_COMMENTAIRES_ID,
		   $LIENS_RECHERCHE,
		   $LIENS_PROTOCOL_ID ) = $base->fetch_row())
       {

    $hitweb = new Hitweb ;
  
    // Affichage de la categories
    $hitweb->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP,"");
    $liste_categorie = $hitweb->$liste;
    $tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ;


	 getProtocol($LIENS_PROTOCOL_ID);

	 $tpl->clear_tpl("BLOCK");
	 
	 getCommentaire($LIENS_COMMENTAIRES_ID);



	 $LIENS_DESCRIPTION = stripslashes($LIENS_DESCRIPTION) ;
	 
	 $tpl->assign(
		      array(
			    LIENS_CATEGORIES_ID => "$LIENS_CATEGORIES_ID",
			    LIENS_ADRESSE => "$LIENS_ADRESSE",
			    LIENS_DESCRIPTION => "$LIENS_DESCRIPTION",
			    LIENS_RECHERCHE => "$LIENS_RECHERCHE"
			    )
		      );
       }
     
   } else {

     $tpl->assign(
		  array(
			LIENS_CATEGORIES_ID => "$LIENS_CATEGORIES_ID",
			LIENS_ADRESSE => "",
			LIENS_DESCRIPTION => "",
			LIENS_RECHERCHE => ""
			)
		  );
   }
 
 
 
 

 
 $tpl->parse(HEADER, header) ; 
 $tpl->FastPrint("HEADER");
 
 $tpl->parse(MODIFLIEN, modiflien) ; 
 $tpl->FastPrint("MODIFLIEN");
 
 $tpl->parse(FOOTER, footer) ; 
 $tpl->FastPrint("FOOTER");
 
 // Permet d'arrêter le cacul du temps et affichage du résultat en commentaire HTML
 // dans le code généré.
 $end = $tpl->utime();
 $run = $end - $start;
 echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
 exit;
}



function modif($WEBMASTER_NOM, $WEBMASTER_PRENOM, $WEBMASTER_EMAIL, $LIENS_PROTOCOL_ID, $LIENS_ADRESSE, $LIENS_RECHERCHE, $LIENS_CATEGORIES_ID, $LIENS_DESCRIPTION, $LIENS_COMMENTAIRES_ID, $LIENS_ID)
{

  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;
  global $tpl ;
  global $EXT_PHP, $EXT_TPL ;
  
  
  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");


  // Mise a jour des infos concernant le webmaster ou la personne qui a enregistré le lien
  $sqlwebmaster = "UPDATE WEBMASTER ";
  $sqlwebmaster .= "SET WEBMASTER_NOM = '$WEBMASTER_NOM', ";
  $sqlwebmaster .= "WEBMASTER_PRENOM = '$WEBMASTER_PRENOM', ";
  $sqlwebmaster .= "WEBMASTER_EMAIL = '$WEBMASTER_EMAIL' ";
  $sqlwebmaster .= "WHERE WEBMASTER_LIENS_ID = '$LIENS_ID' ";
  
  $base->query("$sqlwebmaster");



  // Mise a jour des infos concernant le lien enregistré dans hitweb
  $LIENS_DESCRIPTION = addslashes($LIENS_DESCRIPTION) ;
  
  $sqllien = "UPDATE LIENS ";
  $sqllien .= "SET LIENS_CATEGORIES_ID = '$LIENS_CATEGORIES_ID', ";
  $sqllien .= "LIENS_PROTOCOL_ID = '$LIENS_PROTOCOL_ID', ";
  $sqllien .= "LIENS_ADRESSE = '$LIENS_ADRESSE', "; 
  $sqllien .= "LIENS_DESCRIPTION = '$LIENS_DESCRIPTION', "; 
  $sqllien .= "LIENS_COMMENTAIRES_ID = '$LIENS_COMMENTAIRES_ID', "; 
  $sqllien .= "LIENS_RECHERCHE = '$LIENS_RECHERCHE' "; 
  $sqllien .= "WHERE LIENS_ID = '$LIENS_ID' " ;
  
  $base->query("$sqllien");
  

  
  // Retour vers le formulaire + message
  //echo "tout marche super";
  $message = "Ok";
  affiche($LIENS_ID, $message);
  
}




if ($action == "") $action="main" ;

switch ($action) {  
 
 case "main" : {
   affiche($liens_id, $message);
   break ;
 }

 case "modif" : {
   modif($WEBMASTER_NOM, $WEBMASTER_PRENOM, $WEBMASTER_EMAIL, $LIENS_PROTOCOL_ID, $LIENS_ADRESSE, $LIENS_RECHERCHE, $LIENS_CATEGORIES_ID, $LIENS_DESCRIPTION, $LIENS_COMMENTAIRES_ID, $LIENS_ID); 
   break ;
 }  


}
?>
