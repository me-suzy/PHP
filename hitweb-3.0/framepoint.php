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
// $Id: framepoint.php,v 1.4 2001/09/22 13:44:28 hitweb Exp $

//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
//  Changer le liens pour que cette informations soit plus sécurisée
include "conf/hitweb.conf" ;

//########################################################################################
//# Utilisation de la CLASS Base de données
//########################################################################################
include "$REP_CLASS/class.db_$BASE".$EXT_PHP ;
include "$REP_CLASS/class.hitweb".$EXT_PHP ;




function pointsem($liens_categories_id, $liens_id, $liens_protocol_id, $adresse) {

  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;
  global $EXT_PHP, $EXT_TPL ;
  global $SITE;


  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");

  
  $sql  = "SELECT PROTOCOL_NOM FROM PROTOCOL WHERE PROTOCOL_ID = $liens_protocol_id ";
  $base->query("$sql");
  $PROTOCOL_NOM = $base->result($row, 0);


  // Définition du jour, du mois et de l'année....
  $Jour = date("d");
  $Mois = date("m");
  $Annee = date("Y");

  // Requete pour savoir s'il y a déjà l'enregistrement DATE DU JOUR
  // dans la TABLE DATE...
  $SqlDate = "SELECT DATE_ID FROM DATE ";
  //$SqlDate .= "WHERE DATE_JOUR = $Jour ";
  $SqlDate .= "WHERE DATE_MOIS = $Mois ";
  $SqlDate .= "AND DATE_ANNEE = $Annee ";

  $base->query("$SqlDate");

  $TotalDate  = $base->num_rows();

  $DateID = $base->result($row, 0);

  if (empty($TotalDate))
  {
    //########################################################################################
    //# Si la date n'existe pas dans la table DATE...
    //# Il y a une date Par MOIS pour tous les sites
    //########################################################################################

    // Enregistrement de la date du jour dans la table DATE
    $SqlEnrDate = "INSERT INTO DATE (DATE_JOUR, DATE_MOIS, DATE_ANNEE) ";
    $SqlEnrDate .= "VALUES ('$Jour', '$Mois', '$Annee') ";
  
    $base->query("$SqlEnrDate");
    
    // Enregistrement des premiers points du jour
    $SqlEnrLien = "INSERT INTO POINT (POINT_LIENS_ID, POINT_DATE_ID, POINT_NB) ";
    $SqlEnrLien .= "VALUES ('$liens_id', LAST_INSERT_ID(), '1') ";

    $base->query("$SqlEnrLien");

  }
  else
  {
    //########################################################################################
    //# Si la date existe MOIS ET ANNEE
    //# Alors soit le site en question à déjà des point à cette date
    //# soit il n'en a pas encore
    //########################################################################################

    // Recherche si num LIENS_ID Avec MOIS et ANNEE
    $SqlLienID = "SELECT POINT_LIENS_ID, POINT_DATE_ID, DATE_ID, DATE_JOUR ";
    $SqlLienID .= "FROM POINT, DATE ";
    $SqlLienID .= "WHERE POINT_LIENS_ID = $liens_id ";
    $SqlLienID .= "AND POINT_DATE_ID = DATE_ID ";
    //$SqlLienID .= "AND DATE_JOUR = $Jour ";
    $SqlLienID .= "AND DATE_MOIS = $Mois ";
    $SqlLienID .= "AND DATE_ANNEE = $Annee ";

    $base->query("$SqlLienID");

    $TotalLienID  = $base->num_rows();

    if (empty($TotalLienID))
    {
      //########################################################################################
      //# Le site n'a pas de point ce mois ci 
      //# Donc je fais un INSERT
      //########################################################################################
      // Enregistrement des premiers points du jour
      $SqlEnrLien2 = "INSERT INTO POINT (POINT_LIENS_ID, POINT_DATE_ID, POINT_NB) ";
      $SqlEnrLien2 .= "VALUES ('$liens_id', '$DateID', '1') ";

      $base->query("$SqlEnrLien2");

    }
    else
    {
      //########################################################################################
      //# Le site a des points ce mois ci 
      //# Donc je fais un UPDATE
      //########################################################################################
      $SqlPointNb = "UPDATE POINT ";
      $SqlPointNb .= "SET POINT_NB = POINT_NB + 1 ";
      $SqlPointNb .= "WHERE POINT_LIENS_ID = $liens_id ";
      $SqlPointNb .= "AND POINT_DATE_ID = $DateID ";
      
      $base->query("$SqlPointNb");

    }
  }

  
  $adresse = ereg_replace( "http://", "", $adresse );

  // Construction de la categorie en static
  $fp2 = @fopen("http://$SITE/genpage2.php?CATEGORIES_ID=$liens_categories_id", "r");

  if(!$fp2)
    {
      echo "Erreur !!!";
      exit;
    }
  
  header("Location: $PROTOCOL_NOM://$adresse") ;
}

pointsem($liens_categories_id, $liens_id, $liens_protocol_id, $adresse) ;

?>
