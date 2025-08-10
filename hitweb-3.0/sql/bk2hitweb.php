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
// $Id: bk2hitweb.php,v 1.4 2001/06/20 19:20:46 hitweb Exp $


// A REVOIR LA GESTION DES PROTOCOLES AVANT L'AJOUT DANS LA BASE.


//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
include "../conf/hitweb.conf" ;

//########################################################################################
//# Utilisation de la CLASS Base de données
//########################################################################################
include "../$REP_CLASS/class.db_mysql".$EXT_PHP ;



// Passer le nom du bookmark en paramètre
$file = fopen("bookmarks.html", "r");
if (!$file)
{
  echo "Fichier introuvable !<br>Importation stoppée.";
  exit;
}





echo "<table border=1>";


function categories($file) {
    global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
    global $class_db ;

    $base = new class_db ;
    //$base->debug = 1; 
    $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");

    $i = -1;
    $count = 1;
    while (!feof($file)) {
      
      $ligne = fgets($file, 4096);
      
      
      if (eregi('(<DL>)', $ligne, $arbo)) {
	echo $arbo[0];
	$i++;
	unset($prev_lien);
	
	$liens_categories_prev[$i] = $liens_categories_id ;
      }
      
      if (eregi('(</DL>)', $ligne, $arbo2)) {
	echo $arbo2[0]."<br>\n\n";
	$i--;
      }
      
   
      //Récupération des catégories
      if (eregi('>([[:alnum:]].*)</H3>', $ligne, $regs )) {
	$categories_nom = ereg_replace(">([[:alnum:]].*)</H3>", "\\1", $regs[0]); 
	$categories_nom = addslashes($categories_nom);
	




	if ($categories_nom != "Bookmarks")
	  { 
	    
	    // Ici faire l'enregistrement de la catégories dans la première arbo
	    if ($i==0)
	      {
		
		echo $i." ".$categories_nom;
		
		/*
		// Affichage du tableau
		echo "<tr>";
		echo "<td>$count</td>";
		echo "<td>$categories_nom</td>";
		echo "<td>0</td>";
		echo "</tr>";
		*/


		$sql = "INSERT INTO CATEGORIES (CATEGORIES_NOM, CATEGORIES_PARENTS) VALUES ('$categories_nom', '0')";
		//echo $sql."\n<br>" ;
		$base->query("$sql");
		$liens_categories_id = $base->insert_id() ;
		
		$count++;
		$prev_id = $i;
		
	      } elseif($prev_id<$i) {

		    $next = $count - 1 ;
		  
		    echo $i." ".$categories_nom;

		    /*
		    // Affichage du tableau
		    echo "<tr>";
		    echo "<td>$count</td>";
		    echo "<td>$categories_nom</td>";
		    echo "<td>$next</td>";
		    echo "</tr>";
		    */

		    $sql = "INSERT INTO CATEGORIES (CATEGORIES_NOM, CATEGORIES_PARENTS) VALUES ('$categories_nom', '$next')";
		    //echo $sql."\n<br>" ;
		    $base->query("$sql");
		    $liens_categories_id = $base->insert_id() ;
		    
		    $prev_id = $i;
		    $prev_next = $next;
		    $count++;

	      } else {
		
		if ($prev_id > $i)
		  {

		    echo $i." ".$categories_nom;
		    
		    /*
		    // Affichage du tableau
		    echo "<tr>";
		    echo "<td>$count</td>";
		    echo "<td>$categories_nom</td>";
		    echo "<td>$i</td>";
		    echo "</tr>";
		    */

		    $sql = "INSERT INTO CATEGORIES (CATEGORIES_NOM, CATEGORIES_PARENTS) VALUES ('$categories_nom', '$i')";
		    //echo $sql."\n<br>" ;
		    $base->query("$sql");
		    $liens_categories_id = $base->insert_id() ;

		    $prev_id = $i;
		    $prev_next = $prev_id ;
		    
		  } else {
		    
		    $next = $next - 1;

		    echo $i." ".$categories_nom;
		    
		    /*
		    // Affichage du tableau
		    echo "<tr>";
		    echo "<td>$count</td>";
		    echo "<td>$categories_nom</td>";
		    echo "<td>$prev_next</td>";
		    echo "</tr>";
		    */


		    $sql = "INSERT INTO CATEGORIES (CATEGORIES_NOM, CATEGORIES_PARENTS) VALUES ('$categories_nom', '$prev_next')";
		    //echo $sql."\n<br>" ;
		    $base->query("$sql");
		    $liens_categories_id = $base->insert_id() ;

		  }
		
		$prev_id = $i;   
		$count++;
		
	    }
	    
	    
	  } // Fin de la récupération des ID et de leurs enregistrement en base.

      }
	




	// Récupération du nom pour les liens
	if (eregi('>([[:alnum:]].*)</A>', $ligne, $regs2 )) {
	  
	  //  $liens_description = ereg_replace(">([[:alnum:]].*)</A>", "\\1", $regs2[0]); 
	  $liens_description = ereg_replace(">([[:alnum:]].*)</A>", "\\1", $regs2[0]);
	  if ($liens_description != "Bookmarks")
	    { 
	      

	      if (($prev_lien > $i) and ($prev_lien))
		{
		  echo "<font color=00FF00>$i ".$liens_description."</font>\n<br>";
		} else { 
		  echo "<font color=FF0000>$i ".$liens_description."</font>\n<br>";
		}



	    }
	} 
	

	//Récupération des liens et enregistrement en BASE
	if (eregi("([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])",$ligne, $liens)) {
	  if($liens[0]!="http://checkget.udm.net")
	    {
	      $LIENS_DESCRIPTION = addslashes($liens_description);
	      
	      // LIENS VALIDE
	      $LIENS_COMMENTAIRES_ID = '3';

	      // Suppresion du protocol
	      $liens[0] = ereg_replace( "http://", "", $liens[0] ); 

	      if ($liens_categories_prev[$i] == 0)
		{
		
		  $sqlliens  = "INSERT INTO LIENS (LIENS_CATEGORIES_ID, LIENS_ADRESSE, LIENS_DESCRIPTION, LIENS_COMMENTAIRES_ID, LIENS_RECHERCHE ) VALUES ( ";
		  $sqlliens .= "'0', '$liens[0]', '$LIENS_DESCRIPTION', '$LIENS_COMMENTAIRES_ID', '$LIENS_DESCRIPTION' ) ";
		
		} elseif(($prev_lien > $i) and ($prev_lien)) {

		  
		  $sqlliens  = "INSERT INTO LIENS (LIENS_CATEGORIES_ID, LIENS_ADRESSE, LIENS_DESCRIPTION, LIENS_COMMENTAIRES_ID, LIENS_RECHERCHE ) VALUES ( ";
		  $sqlliens .= "'$liens_categories_prev[$i]', '$liens[0]', '$LIENS_DESCRIPTION', '$LIENS_COMMENTAIRES_ID', '$LIENS_DESCRIPTION' ) ";
		  
		  $liens_categories_id = $liens_categories_prev[$i];
		
	      } else {

		
		$sqlliens  = "INSERT INTO LIENS (LIENS_CATEGORIES_ID, LIENS_ADRESSE, LIENS_DESCRIPTION, LIENS_COMMENTAIRES_ID, LIENS_RECHERCHE ) VALUES ( ";
		$sqlliens .= "'$liens_categories_id', '$liens[0]', '$LIENS_DESCRIPTION', '$LIENS_COMMENTAIRES_ID', '$LIENS_DESCRIPTION' ) ";
		
	      }
	      
	      $base->query("$sqlliens");

	      $prev_lien = $i;
	      
	      
	      //récupération de l'id du liens enregistré
	      $LIENS_ID = $base->insert_id() ;
	      

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
		  $SqlEnrLien .= "VALUES ('$LIENS_ID', '$DateID', '1') ";
		  
		  $ResultEnrLien = $base->query("$SqlEnrLien");
		  
		} else {
		  
		  $sqlpoint = "INSERT INTO POINT (POINT_LIENS_ID, POINT_DATE_ID, POINT_NB ) VALUES ( ";
		  $sqlpoint .= "'$LIENS_ID', '$DateID', '1') ";
		  
		  $resultpoint = $base->query("$sqlpoint");
		}
	      
	      
	    }
	  
	}
	
	
    }	
    
}



categories($file);

echo "</table>";

fclose($file);



?>
