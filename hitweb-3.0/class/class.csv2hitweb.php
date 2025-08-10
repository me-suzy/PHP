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
// $Id: class.csv2hitweb.php,v 1.3 2001/06/20 19:19:40 hitweb Exp $

//########################################################################################
//# Cette class est un utilitaire pour importer des fichiers CSV dans la base de hitweb
//########################################################################################

class csv_import {

  var $file = "" ;
  
  var $table = "" ;





  //########################################################################################
  //# Ouverture du fichier
  //########################################################################################
  function open_file($fichier) {

 /* Etablir la connection, */
   if ( 0 == $this->file ) {
     // ouverture du fichier en lecture    
     $this->file = fopen("$fichier",   "r");
     if (!$this->file)
       {
	 echo "Fichier introuvable !<br>Importation stoppée.";
	 return 0;
       }
   }
   
   return $this->file;
    
  }




  //########################################################################################
  //# Importation des données dans la base
  //# Attention !! cette partie de la class est spécialisée à l'architecture de la 
  //# base HITWEB.
  //########################################################################################
  function import($table) {
    global $base;
    
    // importation    
    while (!feof($this->file)){
      $ligne = fgets($this->file,4096);  
      

      $liste = explode(";", $ligne); // on crée un tableau des élements séparés par des points virgule
      //syslog(LOG_INFO,"ligne : ".$ligne." /// liste : ".$liste[2]);
      
      if ($table == "") $table="main" ;
      
      switch ($table) {
	
      case "main" : {
	echo "Cette fonction n'est pas implémentée pour les fichiers csv";
	break ;
      }
      
      case "CATEGORIES" : {
	$categories_id = $liste[0];              
	$categories_nom  = $liste[1];          
	$categories_parents = $liste[2];
	
	$query =   "INSERT INTO CATEGORIES VALUES('$categories_id','$categories_nom','$categories_parents')"; 
	
	break ;
      }   


      case "COMMENTAIRES" : {
	$commentaires_id = $liste[0];
	$commentaires_texte = $liste[1];
	
	$query =   "INSERT INTO COMMENTAIRES VALUES('$commentaires_id','$commentaires_texte')"; 
	
	break ;
      }

      case "DATE" : {
	$date_id = $liste[0];
	$date_jour = $liste[1];
	$date_mois = $liste[2];
	$date_annee = $liste[3];

	$query =   "INSERT INTO DATE VALUES('$date_id','$date_jour','$date_mois','$date_annee')"; 
	
	break ;
      }


      case "LIENS" : {
	$liens_id = $liste[0];
	$liens_categories_id = $liste[1];
	$liens_adresse = $liste[2];
	$liens_description =  addslashes($liste[3]);
	$liens_commentaires_id = $liste[4];
	$liens_recherche = $liste[5];
	$liens_protocol_id = $liste[6];

	$query =   "INSERT INTO LIENS VALUES('$liens_id','$liens_categories_id','$liens_adresse','$liens_description','$liens_commentaires_id','$liens_recherche', '$liens_protocol_id')"; 
	
	//syslog(LOG_INFO,"liens_adresse : ".$liens_description);

	break ;
      }
      

      case "POINT" : {
	$point_id = $liste[0];
	$point_liens_id = $liste[1];
	$point_date_id = $liste[2];
	$point_nb = $liste[3];
	
	$query =   "INSERT INTO POINT VALUES('$point_id','$point_liens_id','$point_date_id','$point_nb')"; 
	
	break ;
      }

      
      case "PROTOCOL" : {
	$protocol_id = $liste[0];
	$protocol_nom = $liste[1];
	
	$query =   "INSERT INTO PROTOCOL VALUES('$protocol_id','$protocol_nom')"; 
	
	break ;
      }
      


      case "VOTE" : {
	$vote_id = $liste[0];
	$vote_text = $liste[1];
	$vote_nb = $liste[2];

	$query =   "INSERT INTO VOTE VALUES('$vote_id','$vote_text','$vote_nb')"; 
	
	break ;
      }
      

      case "WEBMASTER" : {
	$webmaster_id = $liste[0];
	$webmaster_liens_id = $liste[1];
	$webmaster_nom = $liste[2];
	$webmaster_prenom = $liste[3];
	$webmaster_email = $liste[4];
	$webmaster_mailing = $liste[5];

	$query =   "INSERT INTO WEBMASTER VALUES('$webmaster_id','$webmaster_liens_id','$webmaster_nom','$webmaster_prenom','$webmaster_email','$webmaster_mailing')"; 
	
	break ;
      }

     
      }

      $base->query("$query");
      
    }
    
  }




  //########################################################################################
  //# Fermeture du fichier 
  //########################################################################################
  function close_file() {

    fclose($this->file);
    echo "<br>Importation terminée,avec succés.";
    
  }
  
  
  
  

}
