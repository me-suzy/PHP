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
// $Id: ajoutsite.php,v 1.10 2001/07/20 10:01:56 hitweb Exp $



//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
//  Changer le liens pour que cette informations soit plus sécurisée
include "conf/hitweb.conf" ;


if ($USE_MAIL == "oui")
{
  include "mail".$EXT_PHP ;
}

function  ajoutsite($WEBMASTER_NOM, $WEBMASTER_PRENOM, $WEBMASTER_EMAIL, $LIENS_PROTOCOL_ID, $LIENS_ADRESSE, $LIENS_RECHERCHE, $LIENS_DESCRIPTION, $LIENS_CATEGORIES_ID)
{

  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;
  global $tpl ;
  global $EXT_PHP, $SITE, $USE_MAIL;

  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");


  //########################################################################################
  //# Enregistrement d'un nouveau site web directement par un formulaire
  //# Pour l'instant il va servir pour moi, pour enregistrer les sites web
  //########################################################################################

  $LIENS_DESCRIPTION = addslashes($LIENS_DESCRIPTION);
  
  // LIENS A VALIDER 
  $LIENS_COMMENTAIRES_ID = '2';
  $LIENS_RECHERCHE = addslashes($LIENS_RECHERCHE);
  
  $sqlliens  = "INSERT INTO LIENS (LIENS_CATEGORIES_ID, LIENS_ADRESSE, LIENS_DESCRIPTION, LIENS_COMMENTAIRES_ID, LIENS_RECHERCHE, LIENS_PROTOCOL_ID ) VALUES ( ";
  $sqlliens .= "'$LIENS_CATEGORIES_ID', '$LIENS_ADRESSE', '$LIENS_DESCRIPTION', '$LIENS_COMMENTAIRES_ID', '$LIENS_RECHERCHE', '$LIENS_PROTOCOL_ID' ) ";
  
  $base->query("$sqlliens");

 //########################################################################################
 //# Enregistrement de la personne qui presente le site
 //########################################################################################
 
 // Récupéartion du dernier ID de l'INSERT dans la table LIENS
 $WEBMASTER_LIENS_ID = $base->insert_id() ;
 
 $sqlwebmaster  = "INSERT INTO WEBMASTER ";
 $sqlwebmaster  .= "(WEBMASTER_LIENS_ID, WEBMASTER_NOM, WEBMASTER_PRENOM, WEBMASTER_EMAIL, WEBMASTER_MAILING ) VALUES ( ";
 $sqlwebmaster .= "'$WEBMASTER_LIENS_ID', '$WEBMASTER_NOM', '$WEBMASTER_PRENOM', '$WEBMASTER_EMAIL', '1' ) ";
 
 $base->query("$sqlwebmaster");
 

 //########################################################################################
 //# Enregistrement du NB de point et de la date d'enregistrement dans HITWEB
 //########################################################################################
 
 // Définition du jour, du mois et de l'année....
 $Jour = date("d");
 $Mois = date("m");
 $Annee = date("Y");

 // Requete pour savoir s'il y a déjà l'enregistrement dans la TABLE DATE...
 $sqldate = "SELECT DATE_ID FROM DATE ";
 // Pourra servir dans un proche avenir pour avoir le liste des points par jours
 //$sqldate .= "WHERE DATE_JOUR = $Jour ";
 $sqldate .= "WHERE DATE_MOIS = $Mois ";
 $sqldate .= "AND DATE_ANNEE = $Annee ";
 
 $base->query("$sqldate");

 $DateID = $base->result($row, 0);

 $totaldate = $base->num_rows();
 
 if (empty($totaldate))
   {
     // Enregistrement de la date du jour dans la table DATE
     $SqlEnrDate = "INSERT INTO DATE (DATE_JOUR, DATE_MOIS, DATE_ANNEE) ";
     $SqlEnrDate .= "VALUES ('$Jour', '$Mois', '$Annee') ";
     
     $base->query("$SqlEnrDate");
     
     // Récupéartion du dernier ID de l'INSERT dans la table DATE
     $DateID = $base->insert_id() ;
     
     // Enregistrement des premiers points du jour
     $SqlEnrLien = "INSERT INTO POINT (POINT_LIENS_ID, POINT_DATE_ID, POINT_NB) ";
     $SqlEnrLien .= "VALUES ('$WEBMASTER_LIENS_ID', '$DateID', '1') ";
     
     $ResultEnrLien = $base->query("$SqlEnrLien");
   }
 else
   {
     
     $sqlpoint = "INSERT INTO POINT (POINT_LIENS_ID, POINT_DATE_ID, POINT_NB ) VALUES ( ";
     $sqlpoint .= "'$WEBMASTER_LIENS_ID', '$DateID', '1') ";
     
     $resultpoint = $base->query("$sqlpoint");
   }
 
 if ($USE_MAIL == "oui")
   {
     mail_newsite($WEBMASTER_EMAIL, $WEBMASTER_LIENS_ID) ;
   }
}





function analyse_url($WEBMASTER_NOM, $WEBMASTER_PRENOM, $WEBMASTER_EMAIL, $LIENS_PROTOCOL_ID, $LIENS_ADRESSE, $LIENS_RECHERCHE, $LIENS_DESCRIPTION, $LIENS_CATEGORIES_ID) {
  
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;
  global $tpl, $date ;
  global $EXT_TPL, $EXT_PHP, $MAIL, $REP_TPL;
  global $lib_name, $lib_lastname, $lib_mail, $lib_address, $lib_keyword, $lib_subject, $lib_description, $bt_enre, $bt_reset;
  global $mes_link_not_valid, $mes_enre_hitweb, $mes_pb_meta;
  

  $tpl->assign(
	       array(
		     REP_TPL => "$REP_TPL",
		     EXT_PHP => "$EXT_PHP",
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
  
  
  // getProtocol
  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS"); 
  
  $sql  = "SELECT PROTOCOL_NOM FROM PROTOCOL WHERE PROTOCOL_ID = $LIENS_PROTOCOL_ID ";
  
  $base->query("$sql");
  
  $PROTOCOL_NOM = $base->result($row, 0);
 
  $tpl->assign(PROTOCOL_NOM ,"$PROTOCOL_NOM");
  



  if($PROTOCOL_NOM == "http")
    {
      // Delete protocol http:// in URL
      $LIENS_ADRESSE = ereg_replace( "http://", "", $LIENS_ADRESSE );
      
      //checkURL
      $hitweb3 = new Hitweb ;
      
      $LIENS_ADRESSE = "http://".$LIENS_ADRESSE;
      
      $result = $hitweb3->checkURL("$LIENS_ADRESSE");
      
      // Delete protocol http:// in URL
      $LIENS_ADRESSE = ereg_replace( "http://", "", $LIENS_ADRESSE );

      /*
	Read this RFC 2616
	ftp://ftp.isi.edu/in-notes/rfc2616.txt

	10.2  Successful 2xx ..............................................58
	10.3  Redirection 3xx .............................................61
	10.4  Client Error 4xx ............................................65
	10.5  Server Error 5xx ............................................70
      */
      
      


      if(($result > 399) or ($result=="0"))
	{
	  // This url isn't add to hitweb
	  	  
	  $tpl->assign ( MESSAGE, "$mes_link_not_valid <b><a href='mailto:$MAIL'>$MAIL</a><b>");
	  
	  $LIENS_RECHERCHE = stripslashes($LIENS_RECHERCHE) ;
	  $LIENS_DESCRIPTION = stripslashes($LIENS_DESCRIPTION) ;
	  
	  $tpl->assign ( array ( LIENS_DESCRIPTION => $LIENS_DESCRIPTION,
				 LIENS_RECHERCHE => $LIENS_RECHERCHE ));
	  
	  
	  $hitweb = new Hitweb ;
	  
	  $hitweb->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "html");
	  $liste_categorie = $hitweb->$liste;
	  $tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ;
	  
	  $hitweb2 = new Hitweb ;
	  
	  $hitweb2->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "");
	  $liste_categorie = $hitweb2->$liste;
	  $tpl->assign ( LISTE_CATEGORIE_NOHTML, $liste_categorie) ;
	  
	  getProtocol($LIENS_PROTOCOL_ID);
	  
	  $WEBMASTER_NOM = stripslashes($WEBMASTER_NOM) ;
	  $WEBMASTER_PRENOM = stripslashes($WEBMASTER_PRENOM) ;
	  $WEBMASTER_EMAIL = stripslashes($WEBMASTER_EMAIL) ;
	  $LIENS_ADRESSE = stripslashes($LIENS_ADRESSE) ;
	  
	  $tpl->assign ( array ( WEBMASTER_NOM => $WEBMASTER_NOM,
				 WEBMASTER_PRENOM => $WEBMASTER_PRENOM,
				 WEBMASTER_EMAIL => $WEBMASTER_EMAIL,
				 LIENS_ADRESSE => $LIENS_ADRESSE,
				 LIENS_CATEGORIES_ID => $LIENS_CATEGORIES_ID
				 ));
	  
	  
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
	// This url is good for me
	
	$tpl = new FastTemplate( "tpl/".$REP_TPL."/") ;
	
        $start = $tpl->utime();

        $tpl->define( array ( 
			     header => "header".$EXT_TPL,
			     infouser => "infouser".$EXT_TPL,
			     footer => "footer".$EXT_TPL
			     )) ;
	
	// Insertion des informations sur les balises meta.
        include "meta".$EXT_PHP ;
	
        $tpl->assign( LIENS_CATEGORIES_ID, "");
	$tpl->assign( REP_TPL, "$REP_TPL") ;
        $tpl->assign( EXT_PHP, "$EXT_PHP") ;
	
	
	$hitweb = new Hitweb ;
	    
        // Affichage de la categories
        $hitweb->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "html");
        $liste_categorie = $hitweb->$liste;
        $tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ;
	
	$hitweb2 = new Hitweb ;
	
	$hitweb2->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "");
        $liste_categorie = $hitweb2->$liste;
        $tpl->assign ( LISTE_CATEGORIE_NOHTML, $liste_categorie) ;
	

	$base = new class_db ;
	//$base->debug = 1; 
	$base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
	
	$sql2 = "SELECT CATEGORIES_NOM ";
	$sql2 .= "FROM CATEGORIES ";
	$sql2 .= "WHERE CATEGORIES_ID='$LIENS_CATEGORIES_ID' ";
	
	$base->query("$sql2");
	
	$CATEGORIES_NOM = $base->result($row, 0);
	
	$tpl->assign ( CATEGORIES_NOM, "$CATEGORIES_NOM") ;
	
	$tpl->assign ( MESSAGE, "$mes_enre_hitweb");
	$tpl->assign (PROTOCOL_NOM ,"$PROTOCOL_NOM");
	
	// rechercher les dans les balises META (pour les mots clés. 
	$MetaTags = @get_meta_tags("http://$LIENS_ADRESSE");
	
	if (!$MetaTags)
	  {
	    
	    // Un petit cours sur l'utilisation des balise META
	    $tpl->assign ( META, "$mes_pb_meta") ;
	    
	  } else {
	    $tpl->assign ( META, "<p>&nbsp;");
	  }
	
	// Seulement les mots clés, car sinon il risque d'y avoir une répétition
	// dans la description du site
	$keywords = $MetaTags["keywords"] ;
	
	$LIENS_RECHERCHE = stripslashes($LIENS_RECHERCHE) ;
	$LIENS_DESCRIPTION = stripslashes($LIENS_DESCRIPTION) ;
	
	$LIENS_RECHERCHE = "$LIENS_RECHERCHE, $WEBMASTER_NOM, $WEBMASTER_PRENOM, $CATEGORIES_NOM, $keywords" ;
	
	
	// PROTECTION pour ne pas afficher du HTML, PHP et autre
        $WEBMASTER_NOM = ereg_replace("<[^>]*>", "", $WEBMASTER_NOM);
	$WEBMASTER_PRENOM = ereg_replace("<[^>]*>", "", $WEBMASTER_PRENOM);
	$WEBMASTER_EMAIL = ereg_replace("<[^>]*>", "", $WEBMASTER_EMAIL);
	$LIENS_PROTOCOL_ID = ereg_replace("<[^>]*>", "", $LIENS_PROTOCOL_ID);
	$LIENS_ADRESSE = ereg_replace("<[^>]*>", "", $LIENS_ADRESSE);
	$LIENS_RECHERCHE = ereg_replace("<[^>]*>", "", $LIENS_RECHERCHE);
	$LIENS_DESCRIPTION = ereg_replace("<[^>]*>", "", $LIENS_DESCRIPTION);
	$LIENS_CATEGORIS_ID = ereg_replace("<[^>]*>", "", $LIENS_CATEGORIS_ID);
	
	
	$tpl->assign ( array ( WEBMASTER_NOM => $WEBMASTER_NOM,
			       WEBMASTER_PRENOM => $WEBMASTER_PRENOM, 
			       WEBMASTER_EMAIL => $WEBMASTER_EMAIL, 
			       LIENS_ADRESSE => $LIENS_ADRESSE, 
			       LIENS_DESCRIPTION => $LIENS_DESCRIPTION,
			       LIENS_RECHERCHE => $LIENS_RECHERCHE ));

	ajoutsite($WEBMASTER_NOM, $WEBMASTER_PRENOM, $WEBMASTER_EMAIL, $LIENS_PROTOCOL_ID, $LIENS_ADRESSE, $LIENS_RECHERCHE, $LIENS_DESCRIPTION, $LIENS_CATEGORIES_ID);
	
	
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
	
	
	$tpl->parse(HEADER, header) ; 
	$tpl->FastPrint("HEADER");
      
	$tpl->parse(INFOUSER, infouser) ; 
	$tpl->FastPrint("INFOUSER");
	
	$tpl->parse(FOOTER, footer) ; 
	$tpl->FastPrint("FOOTER");
	
	// Permet d'arrêter le cacul du temps et affichage du résultat en commentaire HTML
	// dans le code généré.
	$end = $tpl->utime();
	$run = $end - $start;
	echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
	exit;
	
      } 
      
    } else { 
      //Si pas le protocol http
        
      $tpl = new FastTemplate( "tpl/".$REP_TPL."/") ;
      
      $start = $tpl->utime();
      
      $tpl->define( array ( 
			   header => "header".$EXT_TPL,
			   infouser => "infouser".$EXT_TPL,
			   footer => "footer".$EXT_TPL
			   )) ;
      
      // Insertion des informations sur les balises meta.
      include "meta".$EXT_PHP ;
      
      $tpl->assign( LIENS_CATEGORIES_ID, "");
      $tpl->assign( EXT_PHP, "$EXT_PHP") ;
      $tpl->assign ( REP_TPL, $REP_TPL) ;
      
      $hitweb = new Hitweb ;
      
      // Affichage de la categories
      $hitweb->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "html");
      $liste_categorie = $hitweb->$liste;
      $tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ;
      
      $hitweb2 = new Hitweb ;
      
      $hitweb2->navigBarCategorie($LIENS_CATEGORIES_ID, "index".$EXT_PHP, "");
      $liste_categorie = $hitweb2->$liste;
      $tpl->assign ( LISTE_CATEGORIE_NOHTML, $liste_categorie) ;
      
  
      $base = new class_db ;
      //$base->debug = 1; 
      $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
      
      $sql2 = "SELECT CATEGORIES_NOM ";
      $sql2 .= "FROM CATEGORIES ";
      $sql2 .= "WHERE CATEGORIES_ID='$LIENS_CATEGORIES_ID' ";
       
      $base->query("$sql2");
      
      $CATEGORIES_NOM = $base->result($row, 0);
      
      $tpl->assign ( CATEGORIES_NOM, "$CATEGORIES_NOM") ;
      
      $tpl->assign ( EXT_PHP, $EXT_PHP) ;
      $tpl->assign (PROTOCOL_NOM ,"$PROTOCOL_NOM");
  
      $tpl->assign ( MESSAGE, "$mes_enre_hitweb");
      
      $tpl->assign ( META, "<p>&nbsp;");
      
      
      // Seulement les mots clés, car sinon il risque d'y avoir une répétition
      // dans la description du site
      $keywords = $MetaTags["keywords"] ;
      
      $LIENS_RECHERCHE = stripslashes($LIENS_RECHERCHE) ;
      $LIENS_DESCRIPTION = stripslashes($LIENS_DESCRIPTION) ;
      
      $LIENS_RECHERCHE = "$LIENS_RECHERCHE, $WEBMASTER_NOM, $WEBMASTER_PRENOM, $CATEGORIES_NOM, $keywords" ;
      
  
      // PROTECTION pour ne pas afficher du HTML, PHP et autre
      $WEBMASTER_NOM = ereg_replace("<[^>]*>", "", $WEBMASTER_NOM);
      $WEBMASTER_PRENOM = ereg_replace("<[^>]*>", "", $WEBMASTER_PRENOM);
      $WEBMASTER_EMAIL = ereg_replace("<[^>]*>", "", $WEBMASTER_EMAIL);
      $LIENS_PROTOCOL_ID = ereg_replace("<[^>]*>", "", $LIENS_PROTOCOL_ID);
      $LIENS_ADRESSE = ereg_replace("<[^>]*>", "", $LIENS_ADRESSE);
      $LIENS_RECHERCHE = ereg_replace("<[^>]*>", "", $LIENS_RECHERCHE);
      $LIENS_DESCRIPTION = ereg_replace("<[^>]*>", "", $LIENS_DESCRIPTION);
      $LIENS_CATEGORIS_ID = ereg_replace("<[^>]*>", "", $LIENS_CATEGORIS_ID);
      
      
      $tpl->assign ( array ( WEBMASTER_NOM => $WEBMASTER_NOM,
			     WEBMASTER_PRENOM => $WEBMASTER_PRENOM, 
			     WEBMASTER_EMAIL => $WEBMASTER_EMAIL, 
			     LIENS_ADRESSE => $LIENS_ADRESSE, 
			     LIENS_DESCRIPTION => $LIENS_DESCRIPTION,
			     LIENS_RECHERCHE => $LIENS_RECHERCHE ));
      
      
      ajoutsite($WEBMASTER_NOM, $WEBMASTER_PRENOM, $WEBMASTER_EMAIL, $LIENS_PROTOCOL_ID, $LIENS_ADRESSE, $LIENS_RECHERCHE, $LIENS_DESCRIPTION, $LIENS_CATEGORIES_ID);
      
      
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
      
      
      $tpl->parse(HEADER, header) ; 
      $tpl->FastPrint("HEADER");
      
      $tpl->parse(INFOUSER, infouser) ; 
      $tpl->FastPrint("INFOUSER");
      
      $tpl->parse(FOOTER, footer) ; 
      $tpl->FastPrint("FOOTER");
      
      // Permet d'arrêter le cacul du temps et affichage du résultat en commentaire HTML
      // dans le code généré.
      $end = $tpl->utime();
      $run = $end - $start;
      echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
      exit;
      
    } // End test protocol http or not 
} // End function analyse_url
?>
