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
// $Id: proposite.php,v 1.11 2001/09/18 21:41:17 hitweb Exp $



//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
include "conf/hitweb.conf" ;


//########################################################################################
//# CLASS FastTemplate en PHP
//########################################################################################
include "$REP_CLASS/class.FastTemplate".$EXT_PHP ;
include "$REP_CLASS/class.db_$BASE".$EXT_PHP ;
include "$REP_CLASS/class.hitweb".$EXT_PHP ;



//########################################################################################
//# Fichier Meta avec DATE de dernière révision du document (automatique)
//########################################################################################
$date = date(  "Ymd", filemtime( $PATH_TRANSLATED ) );



//########################################################################################
//# Analyse URL + enregistrement dans la base
//########################################################################################
include "ajoutsite".$EXT_PHP ;


//########################################################################################
//# Internationalisation de la partie administration
//########################################################################################

include "admin/$REP_LANG_ADMIN/$LANG_ADMIN".$EXT_PHP ;




function getProtocol($LIENS_PROTOCOL_ID)
{
  global $class_db ;
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $tpl ;
  
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
	  $tpl->assign (PROTOCOL_ID, "$PROTOCOL_ID");
	  $tpl->assign (PROTOCOL_NOM, "$PROTOCOL_NOM");
	  
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

  global $tpl, $date ;
  global $EXT_TPL, $EXT_PHP, $REP_TPL ;
  global $class_db ;
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $bt_enre, $bt_reset;
  global $lib_name, $lib_lastname, $lib_mail, $lib_address, $lib_keyword, $lib_subject;
  global $mes_select_categorie;

  $tpl = new FastTemplate( "tpl/".$REP_TPL."/") ;
  
  $start = $tpl->utime();

  if (!$LIENS_CATEGORIES_ID)
    {
      
      $tpl->define( array ( 
			   header => "header".$EXT_TPL,
			   message => "message".$EXT_TPL,
			   footer => "footer".$EXT_TPL
			   )) ;

	  // Insertion des informations sur les balises meta.
      include "meta".$EXT_PHP ;

      $tpl->assign( MESSAGE, "$mes_select_categorie" );
      $tpl->assign( LIENS_CATEGORIES_ID, "");
      $tpl->assign(REP_TPL,"$REP_TPL");
      $tpl->assign( EXT_PHP, "$EXT_PHP") ;

      $hitweb = new Hitweb ;
  
      // Affichage de la categories
      $hitweb->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "html");
      $liste_categorie = $hitweb->$liste;
      $tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ;


      //########################################################################################
      //# Configurations spécifique pour les différents Template
      //########################################################################################
      
      // TEMPLATE LITE 
      $tpl->assign ( MOT, "") ;

      $tpl->parse(HEADER, header) ; 
      $tpl->FastPrint("HEADER");
      
      $tpl->parse(MESSAGE, message) ; 
      $tpl->FastPrint("MESSAGE");
      
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
			   )
		    );
      
      $tpl->define_dynamic ( "protocol", "proposite" );
      
      
      // Insertion des informations sur les balises meta.
      include "meta".$EXT_PHP ;

      $tpl->assign(
		   array(
			 REP_TPL => "$REP_TPL",
			 EXT_PHP => "$EXT_PHP",
			 MESSAGE => "",
			 WEBMASTER_NOM => "",
			 WEBMASTER_PRENOM => "",
			 WEBMASTER_EMAIL => "",
			 LIENS_ADRESSE => "",
			 LIENS_RECHERCHE => "",
			 LIENS_DESCRIPTION => "",
			 LIENS_CATEGORIES_ID => "$LIENS_CATEGORIES_ID"
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
      
      $hitweb = new Hitweb ;
  
      // Affichage de la categories
      $hitweb->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "html");
      $liste_categorie = $hitweb->$liste;
      $tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ;
      
      $hitweb2 = new Hitweb ;
      
      $hitweb2->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "html");
      $liste_categorie = $hitweb2->$liste;
      $tpl->assign ( LISTE_CATEGORIE_NOHTML, $liste_categorie) ;
      
      getProtocol("1");



      //########################################################################################
      //# Configurations spécifique pour les différents Template
      //########################################################################################
      
      // TEMPLATE LITE 
      $tpl->assign ( MOT, "") ;
      

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

 
}






function enregistrer($WEBMASTER_NOM, $WEBMASTER_PRENOM, $WEBMASTER_EMAIL, $LIENS_PROTOCOL_ID, $LIENS_ADRESSE, $LIENS_RECHERCHE, $LIENS_DESCRIPTION, $LIENS_CATEGORIES_ID) {
  
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;
  global $tpl, $date ;
  global $EXT_PHP, $EXT_TPL, $REP_TPL ;
  global $lib_name, $lib_lastname, $lib_mail, $lib_address, $lib_keyword, $lib_subject;
  global $bt_enre, $bt_reset, $mes_fields_empty, $mes_link_in_hitweb;
  

  $tpl = new FastTemplate( "tpl/".$REP_TPL."/") ;

  $start = $tpl->utime();
  

  if (($WEBMASTER_NOM == "") or ($WEBMASTER_PRENOM == "") or ($WEBMASTER_EMAIL == "") or ($LIENS_ADRESSE == "") or ($LIENS_RECHERCHE == "") or ($LIENS_DESCRIPTION == ""))
    {
      
      $tpl->define( array ( 
			   header => "header".$EXT_TPL,
			   proposite => "proposite".$EXT_TPL,
			   footer => "footer".$EXT_TPL
			   )) ;
      

	  // Insertion des informations sur les balises meta.
      include "meta".$EXT_PHP ;

      $tpl->define_dynamic ( "protocol", "proposite" );
      
      
      $tpl->assign ( MESSAGE, "
      <center><b><font color='#FF0000'>$mes_fields_empty</font></b></center>
      ") ;
      
      $tpl->assign(
		   array(
			 REP_TPL => "$REP_TPL",
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
      
      $hitweb = new Hitweb ;
  
      // insert category
      $hitweb->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "html");
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
      
      // include tag meta
      include "meta".$EXT_PHP ;

      // check URL in database 
      $base = new class_db ;
      //$base->debug = 1; 
      $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
      
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
			   REP_TPL => "$REP_TPL",
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
	
	$hitweb = new Hitweb ;
	
	$hitweb->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "html");
	$liste_categorie = $hitweb->$liste;
	$tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ;
	
	// insert category in plain/text
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
	
	$end = $tpl->utime();
	$run = $end - $start;
	echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
	exit;
	
	
      } else {


	// check url 
	analyse_url($WEBMASTER_NOM, $WEBMASTER_PRENOM, $WEBMASTER_EMAIL, $LIENS_PROTOCOL_ID, $LIENS_ADRESSE, $LIENS_RECHERCHE, $LIENS_DESCRIPTION, $LIENS_CATEGORIES_ID, $CHOIX_SUJETS_NOM) ;
	
	
      } // End check url + insert url 
      
    } // End if check input
  
} // End function add link
  


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
