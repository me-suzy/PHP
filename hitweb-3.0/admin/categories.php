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
// $Id: categories.php,v 1.8 2001/06/19 22:54:26 hitweb Exp $

//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
include "../conf/hitweb.conf" ;


//########################################################################################
//# Utilisation des CLASS FastTemplates et Base de données
//########################################################################################
include "../$REP_CLASS/class.FastTemplate".$EXT_PHP ;
include "../$REP_CLASS/class.db_$BASE".$EXT_PHP ;
include "../$REP_CLASS/class.hitweb".$EXT_PHP ;

//########################################################################################
//# Internationalisation de la partie administration
//########################################################################################

include "$REP_LANG_ADMIN/$LANG_ADMIN".$EXT_PHP ;



function affiche($categories_parents_id, $categories_nom) {

  global $tpl ;
  global $EXT_PHP, $EXT_TPL ;
  global $class_db ;
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $title_admin, $title_som_admin, $link_conf_db, $link_conf_file, $link_application, $link_valid_url, $link_polls, $link_check_url, $link_international_admin, $licence;
  global $title_categories, $title_categories_liens, $title_links, $link_add_categories, $link_add_links, $link_update, $link_delete, $link_categories;




  $tpl = new FastTemplate( "tpl/") ;

  $start = $tpl->utime();


  $tpl->define( array ( 
		       header => "header".$EXT_TPL,
		       categories => "categories".$EXT_TPL,
		       liens => "liens".$EXT_TPL,
		       footer => "footer".$EXT_TPL
		       )
		) ;


  $tpl->define_dynamic ( "cat", "categories" );
  $tpl->define_dynamic ( "lien", "liens" );
  

  // Affichage de la barre de navigation dans les categories 
  $hitweb = new Hitweb ;
  $hitweb->navigBarCategorie($categories_parents_id, "categories".$EXT_PHP, "html");
  $liste_categorie = $hitweb->$liste;
  $tpl->assign ( LISTE_CATEGORIE, $liste_categorie) ;


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
		     EXT_PHP => "$EXT_PHP"
		     )
	       );


  if (!$categories_parents_id)
  {

    $tpl->assign( array(
			TITRECAT => "$title_categories",
			CATEGORIES_CAT_NOM => "" 
			)
		);

  } else {

    $categories_nom = stripslashes($categories_nom) ;
    
    $tpl->assign( array(
			TITRECAT => "$title_categories_liens : <b> $categories_nom</b>",
			CATEGORIES_CAT_NOM => "$categories_nom"
			)
		  );

  }


  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");


  if (!$categories_parents_id)
  {
    $sql = "SELECT CATEGORIES_ID, CATEGORIES_NOM FROM CATEGORIES  ";
    $sql .= "WHERE CATEGORIES_PARENTS = '0' ";
    $sql .= "ORDER BY CATEGORIES_NOM ";
  } else {
    $sql = "SELECT CATEGORIES_ID, CATEGORIES_NOM FROM CATEGORIES  ";
    $sql .= "WHERE CATEGORIES_PARENTS = '$categories_parents_id' ";
    $sql .= "ORDER BY CATEGORIES_NOM ";
  }


  $base->query("$sql");


  $num = $base->num_rows();

  if ($num > 0)
  {
  
    while (list ( $CATEGORIES_ID,
                  $CATEGORIES_NOM ) = $base->fetch_row())
      {
        $tpl->assign (CATEGORIES_ID, "$CATEGORIES_ID") ;
        $tpl->assign ( CATEGORIES_NOM, "$CATEGORIES_NOM") ;
	
        $CATEGORIES_NOM_URL = urlencode($CATEGORIES_NOM);
        $tpl->assign (CATEGORIES_NOM_URL, "$CATEGORIES_NOM_URL") ;
	
        $tpl->parse (BLOCK_CATEGORIES, ".cat" );
      }
    
    
    $tpl->assign( array(
			LINK_UPDATE => "$link_update",
			LINK_DELETE => "$link_delete",
			LINK_CATEGORIES => "$link_categories"
			)
		  );


  } else {

    $tpl->assign( array(
			CATEGORIES_ID => "",
			CATEGORIES_NOM => "",
			LINK_UPDATE => "",
			LINK_DELETE => "",
			LINK_CATEGORIES => ""
			)
		  );

    $tpl->parse (BLOCK_CATEGORIES, ".cat" );
  }
  


  if (!$categories_parents_id)
    {
      $tpl->assign (CATEGORIES_PARENTS_ID, "0");
      $tpl->assign (TITRE_LIENS, ""); 
      
      $tpl->assign (MODIFIER_LIENS,"");
      $tpl->assign (SUPPRIMER_LIENS,"");
 
      $tpl->parse(HEADER, header) ; 
      $tpl->FastPrint("HEADER");
      
      $tpl->parse(CATEGORIES, categories) ; 
      $tpl->FastPrint("CATEGORIES");
  
      $tpl->parse(FOOTER, footer) ; 
      $tpl->FastPrint("FOOTER");  

      $end = $tpl->utime();
      $run = $end - $start;
      echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
      exit;
      
    } else {
      $tpl->assign (CATEGORIES_PARENTS_ID, "$categories_parents_id");
      
      $tpl->assign(TITRE_LIENS, "
<p>
<hr width='50%'>
<p>
$title_links<p>

<a href='proposite$EXT_PHP?LIENS_CATEGORIES_ID=$categories_parents_id'>$link_add_links</a><p>
");  
      
      $sql = "SELECT LIENS_ID, LIENS_CATEGORIES_ID, LIENS_PROTOCOL_ID, LIENS_ADRESSE, LIENS_DESCRIPTION, LIENS_COMMENTAIRES_ID, LIENS_RECHERCHE ";
      $sql .= "FROM LIENS  ";
      $sql .= "WHERE LIENS_CATEGORIES_ID = '$categories_parents_id' ";
      $sql .= "ORDER BY LIENS_ADRESSE ";
      
      $base->query("$sql");
      
      $num = $base->num_rows();


      if ($num > 0)
	{

	  $tpl->assign (MODIFIER_LIENS,"Modifier");
	  $tpl->assign (SUPPRIMER_LIENS,"Supprimer");

	  while (list ( $LIENS_ID,
			$LIENS_CATEGORIES_ID,
			$LIENS_PROTOCOL_ID,
			$LIENS_ADRESSE,
			$LIENS_DESCRIPTION,
			$LIENS_COMMENTAIRES_ID,
			$LIENS_RECHERCHE ) = $base->fetch_row())
	    {
	      $tpl->assign ( LIENS_ID, "$LIENS_ID") ;
	      $tpl->assign ( LIENS_CATEGORIES_ID, "$LIENS_CATEGORIES_ID") ;
	      $tpl->assign ( LIENS_PROTOCOL_ID, "$LIENS_PROTOCOL_ID") ;
	      $tpl->assign ( LIENS_ADRESSE, "$LIENS_ADRESSE") ;
	      $tpl->assign ( LIENS_DESCRIPTION, "$LIENS_DESCRIPTION") ;
	      $tpl->assign ( LIENS_COMMENTAIRES_ID, "$LIENS_COMMENTAIRES_ID") ;
	      $tpl->assign ( LIENS_RECHERCHE, "$LIENS_RECHERCHE") ;
	      
	      
	      // Gestion du protocol (A REVOIR)
	      $db = new class_db ;
	      //$base->debug = 1; 
	      $db->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
	      
	       // Récupération du protocol
	      $sqlprotocol = "SELECT PROTOCOL_NOM FROM PROTOCOL WHERE PROTOCOL_ID = '$LIENS_PROTOCOL_ID' ";
	      $db->query("$sqlprotocol");
	      $PROTOCOL_NOM = $db->result($row, 0);
 
	      $tpl->assign (PROTOCOL_NOM,"$PROTOCOL_NOM://");
	       
	      $tpl->parse ( BLOCK_LIENS, ".lien" );
	      
	    }

	} else {

	  $tpl->assign (MODIFIER_LIENS,"");
	  $tpl->assign (SUPPRIMER_LIENS,"");
	
	  $tpl->assign ( LIENS_ID, "") ;
	  $tpl->assign ( LIENS_CATEGORIES_ID, "") ;
	  $tpl->assign ( LIENS_ADRESSE, "") ;
	  $tpl->assign ( LIENS_DESCRIPTION, "") ;
	  $tpl->assign ( LIENS_COMMENTAIRES_ID, "") ;
	  $tpl->assign ( LIENS_RECHERCHE, "") ;
	  $tpl->assign ( PROTOCOL_NOM, "") ;

	  $tpl->parse ( BLOCK_LIENS, ".lien" );

	}
      
      $tpl->parse(HEADER, header) ; 
      $tpl->FastPrint("HEADER");
      
      $tpl->parse(CATEGORIES, categories) ; 
      $tpl->FastPrint("CATEGORIES");
      
      $tpl->parse(LIENS, liens) ; 
      $tpl->FastPrint("LIENS");
      
      $tpl->parse(FOOTER, footer) ; 
      $tpl->FastPrint("FOOTER");  
      
      $end = $tpl->utime();
      $run = $end - $start;
      echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
      exit;
      
    }


    
}



function msg()
{
  global $tpl, $EXT_TPL, $EXT_PHP ;
  global $bt_enre, $bt_reset, $title_update_cat, $lib_id, $lib_name ;
  global $title_admin, $title_som_admin, $link_conf_db, $link_conf_file, $link_application, $link_valid_url, $link_polls, $link_check_url, $link_international_admin, $licence;
  
  
  $tpl = new FastTemplate( "tpl/") ;
  
  $start = $tpl->utime();
  
  $tpl->define( array ( 
		       header => "header".$EXT_TPL,
		       msg => "msg".$EXT_TPL,
		       footer => "footer".$EXT_TPL
		       )
		) ;
  
  
  $tpl->assign(
	       array(
		     TITLE => "$title_admin",
		     TITLE_SOM => "$title_som_admin",
		     TITLE_UPDATE_CAT => "$title_update_cat",
		     LINK_CONF_DB => "$link_conf_db",
		     LINK_CONF_FILE => "$link_conf_file",
		     LINK_APPLICATION => "$link_application",
		     LINK_VALID_URL => "$link_valid_url",
		     LINK_POLLS => "$link_polls",
		     LINK_CHECK_URL => "$link_check_url",
		     LINK_INTERNATIONAL_ADMIN => "$link_international_admin",
		     LIB_ID => "$lib_id",
		     LIB_NAME => "$lib_name",
		     BT_ENRE => "$bt_enre",
		     BT_RESET => "$bt_reset",
		     LICENCE => "$licence",
		     ALIGN => "center",
		     EXT_PHP => "$EXT_PHP"
		     )
	       );
  
  
  
   
  $tpl->parse(HEADER, header) ; 
  $tpl->FastPrint("HEADER");
  
  $tpl->parse(MESSAGE, msg) ; 
  $tpl->FastPrint("MESSAGE");
  
  $tpl->parse(FOOTER, footer) ; 
  $tpl->FastPrint("FOOTER");
  
  $end = $tpl->utime();
  $run = $end - $start;
  echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
  exit;
}



function ajout($categories_new_nom, $categories_nom, $categories_parents_id)
{

  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;

  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");

  $sql = "INSERT INTO CATEGORIES (CATEGORIES_NOM, CATEGORIES_PARENTS) VALUES ('$categories_new_nom', '$categories_parents_id')";
  
  $base->query("$sql");
  
  // Pour afficher de nouveau la liste des themes
  affiche($categories_parents_id, $categories_nom) ;
}





function supp($categories_id, $categories_parents_id, $categories_nom )
{  

  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;

  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");

  $sql = "DELETE FROM CATEGORIES WHERE CATEGORIES_ID = '$categories_id' ";
  
  $base->query("$sql");
    
  affiche($categories_parents_id, $categories_nom) ;

}



function modif($categories_id, $categories_parents_id, $categories_cat_nom, $categories_nom)
{
  global $tpl, $EXT_TPL, $EXT_PHP ;
  global $bt_enre, $bt_reset, $title_update_cat, $lib_id, $lib_name ;
  global $title_admin, $title_som_admin, $link_conf_db, $link_conf_file, $link_application, $link_valid_url, $link_polls, $link_check_url, $link_international_admin, $licence;
  
  $tpl = new FastTemplate( "tpl/") ;
    
  $start = $tpl->utime();

  $tpl->define( array ( 
		       header => "header".$EXT_TPL,
		       modifcategories => "modifcategories".$EXT_TPL,
		       footer => "footer".$EXT_TPL
		       )
		) ;
  

    $tpl->assign(
	       array(
		     TITLE => "$title_admin",
		     TITLE_SOM => "$title_som_admin",
		     TITLE_UPDATE_CAT => "$title_update_cat",
		     LINK_CONF_DB => "$link_conf_db",
		     LINK_CONF_FILE => "$link_conf_file",
		     LINK_APPLICATION => "$link_application",
		     LINK_VALID_URL => "$link_valid_url",
		     LINK_POLLS => "$link_polls",
		     LINK_CHECK_URL => "$link_check_url",
		     LINK_INTERNATIONAL_ADMIN => "$link_international_admin",
		     LIB_ID => "$lib_id",
		     LIB_NAME => "$lib_name",
		     BT_ENRE => "$bt_enre",
		     BT_RESET => "$bt_reset",
		     LICENCE => "$licence",
		     ALIGN => "center",
		     EXT_PHP => "$EXT_PHP"
		     )
	       );
  


  $tpl->assign ( CATEGORIESID, "$categories_id") ;

  $categories_nom = stripslashes($categories_nom);

  $tpl->assign ( CATEGORIESNOM, "$categories_nom") ;
  $tpl->assign ( CATEGORIESCATNOM, "$categories_cat_nom" ) ;

  $tpl->assign ( CATEGORIES_PARENTS_ID, "$categories_parents_id");

  $tpl->parse(HEADER, header) ; 
  $tpl->FastPrint("HEADER");

  $tpl->parse(MODIFCAT, modifcategories) ; 
  $tpl->FastPrint("MODIFCAT");

  $tpl->parse(FOOTER, footer) ; 
  $tpl->FastPrint("FOOTER");

  $end = $tpl->utime();
  $run = $end - $start;
  echo "\n<!-- Runtime [$run] seconds<BR> -->\n";
  exit;
}


function enregistrer($categories_id, $categories_parents_id, $categories_cat_nom, $categories_nom)
{
  
  global $tpl ;
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;
  global $EXT_TPL ;

  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
 
  $sql = "UPDATE CATEGORIES SET CATEGORIES_NOM = '$categories_nom' ";
  $sql .= "WHERE CATEGORIES_ID = '$categories_id' " ;

  $base->query("$sql");

  //########################################################################################
  //# Permet d'afficher correctement le nom de la catégorie, ou rien si par default
  //########################################################################################
  if ($categories_parents_id == "0")
  {  
    affiche($categories_parents_id, "") ;
  } else {
    affiche($categories_parents_id, $categories_cat_nom) ;
  }
}



function suppliens($categories_parents_id, $liens_id)
{
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;
  
  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
  
  $sql = "DELETE FROM LIENS WHERE LIENS_ID = '$liens_id' ";
  
  $base->query("$sql");
  
  affiche($categories_parents_id, $categories_nom) ;
}




if ($action == "") $action="main" ;

switch ($action) {  
  
 case "main" : {
   affiche($categories_parents_id, $categories_nom) ;
   break ;
 }

 case "ajouter" : {
   ajout($categories_new_nom, $categories_nom, $categories_parents_id) ; 
   break ;
 }  

 case "supp" : {
   supp($categories_id, $categories_parents_id, $categories_nom) ; 
   break ;
 }  

case "modif" : {
   modif($categories_id, $categories_parents_id, $categories_cat_nom, $categories_nom) ; 
   break ;
 }  

case "enre" : {
   enregistrer($categories_id, $categories_parents_id, $categories_cat_nom, $categories_nom ) ; 
   break ;
 }  


 
// Pour les liens hypertextes

 case "modifliens" :{
 
 } 

 
 case "suppliens" : {
   suppliens($categories_parents_id, $liens_id);
   break;
 }
 



}
?>
