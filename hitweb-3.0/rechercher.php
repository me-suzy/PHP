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
// $Id: rechercher.php,v 1.10 2001/07/19 08:31:03 hitweb Exp $



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






function affiche() {

  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db, $Hitweb ;
  global $tpl, $date ;
  global $EXT_PHP, $EXT_TPL, $REP_TPL ;


  $tpl = new FastTemplate("tpl/$REP_TPL/") ;
  
  $start = $tpl->utime();

  $tpl->define( array ( 
  		       header => "header".$EXT_TPL,
		       page => "rechercher".$EXT_TPL,
		       sitedumois => "sitedumois".$EXT_TPL,
		       footer => "footer".$EXT_TPL
			   )) ;
 
  $tpl->define_dynamic ( "top", "page" );
  
  // Insertion des informations sur les balises meta.
  include "meta".$EXT_PHP ;

  $tpl->assign (REP_TPL,"$REP_TPL");
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


  $tpl->assign ( MESSAGE, "") ;
  $tpl->assign ( RESULTAT, "") ;
  $tpl->assign ( MOT, "") ;
  $tpl->assign ( PAGE_G, "") ;
  $tpl->assign ( PAGE_M, "") ;
  $tpl->assign ( PAGE_D, "") ;

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



function rechercher($mot, $offset, $maxresult) {
  
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;
  global $tpl ;
  global $EXT_PHP, $EXT_TPL, $REP_TPL ;


  $tpl = new FastTemplate("tpl/$REP_TPL/") ;
  
  $start = $tpl->utime();

  $tpl->define( array ( 
  		       header => "header".$EXT_TPL,
		       page => "rechercher".$EXT_TPL,
		       sitedumois => "sitedumois".$EXT_TPL,
		       footer => "footer".$EXT_TPL
			   )) ;
 
  $tpl->define_dynamic ( "top", "page" );
  $tpl->define_dynamic ( "numpage", "page" ) ;
  
  // Insertion des informations sur les balises meta.
  include "meta".$EXT_PHP ;


  $tpl->assign (REP_TPL,"$REP_TPL");
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
  //# Transformation des mots recherchés en minuscules
  //########################################################################################
  $mot=strtolower($mot);

  //########################################################################################
  //# Séparation des mots saisi, création d'un tableau
  //########################################################################################
  $mots=split(" ",$mot);
  
  $nombre_mots=count($mots);
  
  $z=1;
  $texte="contenant <b>&quot;$mots[0]&quot;</b>";
  $phrase="'%$mots[0]%'";
  
  //########################################################################################
  //# Requete différente s'il y a plusieurs mots
  //########################################################################################
  while($z<$nombre_mots) {
        
    // IL RESTE A FAIRE LE MOTEUR L'UTILISATION DES CONCATENATION DE CHAINE
    // Pourquoi reconnait médecine et pas médecines
    
    // Et revoir pour rechercher aussi dans la description du site
    //$phrase.=" AND LIENS_DESCRIPTION LIKE '%$mots[$z]%' ";
    //$phrase.=" OR LIENS_DESCRIPTION LIKE '%$mots[$z]%' ";
    
    $texte.=" et <b>&quot;$mots[$z]&quot;</b>";
    $z++;
  }
  
  //########################################################################################
  //# Nombre total de résultat par requete
  //########################################################################################
  $sql2 = "SELECT LIENS_ID ";
  $sql2 .= "FROM LIENS, POINT ";
  $sql2 .= "WHERE LIENS_ID = POINT_LIENS_ID ";
  $sql2 .= "AND LIENS_COMMENTAIRES_ID > '1' ";
  $sql2 .= "AND (LIENS_RECHERCHE LIKE $phrase ";
  $sql2 .= "OR LIENS_DESCRIPTION LIKE $phrase) ";
  $sql2 .= "GROUP BY LIENS_ID ";
  
  $result2 = $base->query($sql2);
  
  $total = $base->num_rows($result2);
  
  //########################################################################################
  //# Définition du message par rapport au nb d'enregistrement trouvés
  //########################################################################################
  
  if (empty($total))
    {
      // PAS TROUVE
      $tpl->assign ( MESSAGE, "<center><font class='Texte'>Essayez encore !! car j'ai pas trouvé : <b>&quot;$mot&quot;</b>...</font></center><p>") ;
      $tpl->assign ( RESULTAT, "") ;
      $tpl->assign ( MOT, "$mot") ;
      $tpl->assign ( PAGE_G, "") ;
      $tpl->assign ( PAGE_M, "") ;
      $tpl->assign ( PAGE_D, "") ;
    }

  else if (empty($mot))
    {
      // PAS DE MOT RECHERCHER
      $tpl->assign ( MESSAGE, "") ;
      $tpl->assign ( RESULTAT, "") ;
      $tpl->assign ( MOT, "") ;
      $tpl->assign ( PAGE_G, "") ;
      $tpl->assign ( PAGE_M, "") ;
      $tpl->assign ( PAGE_D, "") ;
    }
  else if (strlen($mot)<3)
    {
      // MOINS DE TROIS CARACTERE....
      $tpl->assign ( MESSAGE, "<center><font class='Texte'>Veuillez saisir au moins 3 caractères.</font></center><p>") ;
      $tpl->assign ( RESULTAT, "") ;
      $tpl->assign ( MOT, "$mot") ;
      $tpl->assign ( PAGE_G, "") ;
      $tpl->assign ( PAGE_M, "") ;
      $tpl->assign ( PAGE_D, "") ;
    }
  
  //########################################################################################
  //# Affiche les résultats
  //########################################################################################
  else {
    //########################################################################################
    //# Une réponse donc pas de pluriel
    //########################################################################################
    if ($total==1) 
      {
	$tpl->assign ( MESSAGE, "<font class='Titre'><b>$total</b> r&eacute;ponse - $texte</font><p>" ) ;
      } else  {
	// Sinon pluriel
	$tpl->assign ( MESSAGE, "<font class='Titre'><b>$total</b> r&eacute;ponses - $texte</font><p>") ;
      }
    //########################################################################################
    //# Nombre de résultat par page
    //########################################################################################
    
    // Si c'est la première page alors offset est égale à 0
    if (empty($offset)) 
      {
	$offset=0;
      }
    
    $sql = "SELECT LIENS_ID, LIENS_ADRESSE, LIENS_DESCRIPTION, LIENS_COMMENTAIRES_ID, LIENS_PROTOCOL_ID, ";
    $sql .= "sum(POINT_NB) AS nb ";
    $sql .= "FROM LIENS, POINT ";
    $sql .= "WHERE LIENS_ID = POINT_LIENS_ID ";
    $sql .= "AND LIENS_COMMENTAIRES_ID > '1' ";
    $sql .= "AND (LIENS_RECHERCHE LIKE $phrase ";
    $sql .= "OR LIENS_DESCRIPTION LIKE $phrase) ";
    $sql .= "GROUP BY LIENS_ID ";
    $sql .= "ORDER BY nb DESC ";
    $sql .= "limit $offset,$maxresult";
    
    $result = $base->query($sql);
      
      $num = $base->num_rows($result);
      
      // calcul le nb de page par résultat + Convertion du resultat en entier
      
      $essai = ($total/$maxresult);
      $pages=intval($total/$maxresult);


      //########################################################################################
      //# Affichage les informations sur une pages si moins de MAXRESULT 
      //########################################################################################
      
      if (($pages==0) or ($pages==1))
	{
	  
	    while (list ( $LIENS_ID,
			  $LIENS_ADRESSE,
			  $LIENS_DESCRIPTION,
			  $LIENS_COMMENTAIRES_ID,
			  $LIENS_PROTOCOL_ID,
			  $LIENS_NBCLICK ) = $base->fetch_row())
	      {
		
		//Suppression des / \ pour l'affichage des cotes '' ''' ' ''
		$LIENS_DESCRIPTION = stripslashes($LIENS_DESCRIPTION);
		
		$tpl->assign ( RESULTAT, "
    
	<td valign='top' align='center' width='10%'><font class='Texte'><b>$LIENS_NBCLICK</b></font></td>
    <td valign='top' align='left' width='90%'><font class='Texte'>$LIENS_DESCRIPTION</font><br>
    <a href='framepoint$EXT_PHP?adresse=$LIENS_ADRESSE&liens_id=$LIENS_ID&liens_protocol_id=$LIENS_PROTOCOL_ID' target='hitweb'>$LIENS_ADRESSE</a><br>&nbsp;
	</td>

	  ") ;
		
		$tpl->assign ( MOT, "$mot") ;
		
		$tpl->assign ( PAGE_G, "") ;
		$tpl->assign ( PAGE_M, "") ;
		$tpl->assign ( PAGE_D, "") ;
		
		$tpl->parse ( BLOCK, ".top" );
		
		
	      }
	  
	} else {
	  //########################################################################################
	  //# Affichage des information sur Plusieurs pages
	  //########################################################################################
	  
	  while (list ( $LIENS_ID,
			$LIENS_ADRESSE,
	                $LIENS_DESCRIPTION,
			$LIENS_COMMENTAIRES_ID,
			$LIENS_PROTOCOL_ID,
			$LIENS_NBCLICK ) = $base->fetch_row())
	    {
	      
	      //Suppression des / \ pour l'affichage des cotes '' ''' ' ''
	      $LIENS_DESCRIPTION = stripslashes($LIENS_DESCRIPTION);
	      
	      $tpl->assign ( RESULTAT, "
	<td valign='top' align='center' width='10%'><font class='Texte'><b>$LIENS_NBCLICK</b></font></td>
    <td valign='top' align='left' width='90%'><font class='Texte'>$LIENS_DESCRIPTION</font><br>
    <a href='framepoint$EXT_PHP?adresse=$LIENS_ADRESSE&liens_id=$LIENS_ID&liens_protocol_id=$LIENS_PROTOCOL_ID' target='hitweb'>$LIENS_ADRESSE</a><br>&nbsp;
	</td>
	") ;
	      
	      $tpl->parse ( BLOCK_LIENS, ".top" );
	      
	    }
	  
	  $tpl->assign ( MOT, "$mot") ;
	  
	  if ($pages<$essai)
	    {
	      //Ajout un si ce n'est pas un compte rond
	      $pages = $pages + 1;
	    }
	  
	  for ($i=1;$i<=$pages;$i++) 
	    {  
	      if ($i==1)
		{
		  $offsetnew=$maxresult*($i-1);
		  
		  $tpl->assign ( PAGE_G, "

		    <td width='33%' align='right'>
		      <a href='rechercher$EXT_PHP?mot=$mot&offset=$offsetnew&maxresult=$maxresult&action=Rechercher'>Première page - </a>
			</td>
		") ;
		  
		  $compt = $compt + 1;
		  
		} elseif ($i==$pages) {
		  //$tpl->assign ( PAGE_M, "<td>&nbsp;</td>") ;
		} else {
		  $offsetnew=$maxresult*($i-1);
		  
		  $tpl->assign ( PAGE_M, "
			  <a href='rechercher$EXT_PHP?mot=$mot&offset=$offsetnew&maxresult=$maxresult&action=Rechercher'>$compt</a> 
		") ;

		  $tpl->parse ( BLOCK_NUM, ".numpage" );
		  
		  $compt = $compt + 1;
		}
	    }
	  
	  $offsetnew=$maxresult*($i-1);
	  $offsetnew = $offsetnew - $maxresult;
	  
	  $tpl->assign ( PAGE_D, "
	      <td width='33%'>
		    <a href='rechercher$EXT_PHP?mot=$mot&offset=$offsetnew&maxresult=$maxresult&action=Rechercher'> - Dernière page</a>
		  </td>
	") ;  
	  
	  $compt = $compt + 1;
	  
	  $tpl->clear_tpl("BLOCK_LIENS");
	}
      
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
   affiche() ;
   break ;
 }  
 
 case "Rechercher" : {
   rechercher($mot, $offset, $maxresult) ;
   break ;
 }  
 
}

?>
