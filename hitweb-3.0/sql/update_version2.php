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
// $Id: update_version2.php,v 1.2 2001/07/19 15:43:23 hitweb Exp $

//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
include "../conf/hitweb.conf" ;


$res = mysql_connect("$DBHOST", "$DBUSER", "$DBPASS") or die ("<p><b>ERREUR DE CONNECTION  A LA BASE</b><p>");
mysql_select_db("$DBNAME") or die ("<p><b>PROBLEME SELECTION DE LA BASE</b><p>");

//########################################################################################
//# AJOUT de la table protocol 
//########################################################################################

$sqlprotocol = "CREATE TABLE PROTOCOL ( ";
$sqlprotocol .= "PROTOCOL_ID int(11) DEFAULT '0' NOT NULL auto_increment, ";
$sqlprotocol .= "PROTOCOL_NOM text, ";
$sqlprotocol .= "PRIMARY KEY (PROTOCOL_ID), ";
$sqlprotocol .= "KEY PROTOCOL_ID (PROTOCOL_ID) ) ";
$result = mysql_query($sqlprotocol);

$sqlprotocol1 .= "INSERT INTO PROTOCOL VALUES('', 'http') ";
$result1 = mysql_query($sqlprotocol1);

$sqlprotocol2 .= "INSERT INTO PROTOCOL VALUES('', 'ftp') ";
$result2 = mysql_query($sqlprotocol2);


//########################################################################################
//# TABLE THEMES RENAME CATEGORIES + CHANGE FIELDS
//########################################################################################
$sqlcategories .= "ALTER TABLE THEMES RENAME CATEGORIES ";
$resultcat = mysql_query($sqlcategories);

$sqlcategories1 .= "ALTER TABLE CATEGORIES CHANGE THEMES_ID CATEGORIES_ID INT (11) not null AUTO_INCREMENT ";
$resultcat1 = mysql_query($sqlcategories1);

$sqlcategories2 .= "ALTER TABLE CATEGORIES CHANGE THEMES_NOM CATEGORIES_NOM TEXT ";
$resultcat2 = mysql_query($sqlcategories2);

$sqlcategories3 .= "ALTER TABLE CATEGORIES ADD CATEGORIES_PARENTS INT (11) DEFAULT '0' not null ";
$resultcat3 = mysql_query($sqlcategories3);


//########################################################################################
//# CHANGE AND ADD FIELDS IN TABLE LIENS
//########################################################################################

$sqlliens = "ALTER TABLE LIENS ADD LIENS_PROTOCOL_ID TINYINT DEFAULT '1' not null ";
$resultlien = mysql_query($sqlliens);

$sqlliens1 = "ALTER TABLE LIENS CHANGE LIENS_SUJETS_ID LIENS_CATEGORIES_ID INT (11) ";
$result = mysql_query($sqlliens1);


//########################################################################################
//# Récupération des sujets pour les ajouter dans la table CATEGORIES
//########################################################################################
$sqlsujets = "SELECT SUJETS_ID, SUJETS_NOM, SUJETS_THEMES_ID FROM SUJETS ";
//$sqlsujets .= "FROM SUJETS ORDER BY SUJETS_ID DESC ";

$result1 = mysql_query($sqlsujets);

$num = mysql_num_rows($result1) ;

if ($num > 0)
  {
    while (list ($SUJETS_ID,
		 $SUJETS_NOM,
		 $SUJETS_THEMES_ID ) = mysql_fetch_row($result1))
      {
	
	$sqlcat = "INSERT INTO CATEGORIES (CATEGORIES_ID, CATEGORIES_NOM, CATEGORIES_PARENTS) VALUES ( ";
	$sqlcat .= "'', '$SUJETS_NOM', '$SUJETS_THEMES_ID') ";
	$result = mysql_query($sqlcat);
	
	// Récupéartion du dernier ID de l'INSERT dans la table LIENS
	$NEW_ID = mysql_insert_id() ;



/* Marche pas A REVOIR POUR LES LIENS	
	//########################################################################################
	//# UPDATE FIELDS LIENS_CATEGORIES_ID WITH NEW INSERT
	//########################################################################################
	$sqluplien = "UPDATE LIENS SET LIENS_CATEGORIES_ID = '$NEW_ID' ";
	$sqluplien .= "WHERE LIENS_CATEGORIES_ID = '$SUJETS_ID' ";
	
	echo $sqluplien ."<br>";
	
	$resultotal = mysql_query($sqluplien);
*/




	}
  
} else {
  echo "PAS D'ENREGISTREMENT";
}

echo "Ok tout est terminé";

?>
