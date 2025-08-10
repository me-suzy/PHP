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
// $Id: genpage.php,v 1.2 2001/09/22 13:39:05 hitweb Exp $


/*
Il reste à implémenter la génération d'une page static quand un visiteur
click sur un liens...

Et la gestion du template après la génération des pages statiques de l'annuaire
*/

//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
include "conf/hitweb.conf";

//########################################################################################
//# Utilisation des CLASS FastTemplates et Base de données
//########################################################################################
include "$REP_CLASS/class.db_$BASE".$EXT_PHP ;

$ficname = "index"; 

//########################################################################################
//# Utilisation des CLASS FastTemplates et Base de données
//########################################################################################
$base = new class_db ;
//$base->debug = 1; 
$base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");

$sql = "SELECT CATEGORIES_ID ";
$sql .= "FROM CATEGORIES ";
$sql .= "ORDER BY CATEGORIES_ID";

$base->query("$sql");

// Contruction de la page d'index en static
$fp = @fopen("http://$SITE/genpage2.php?CATEGORIES_ID=", "r");

if(!$fp)
{
  echo "Erreur !!!";
  exit;
} 


while (list ( $CATEGORIES_ID) = $base->fetch_row())
{
  // Construction des categories en static
  $fp2 = @fopen("http://$SITE/genpage2.php?CATEGORIES_ID=$CATEGORIES_ID", "r");

  if(!$fp2)
    {
      echo "Erreur !!!";
      exit;
    }
}

// Faire ici une gestion de template.
echo "<P><CENTER>";
echo "<FONT SIZE=-1 FACE=Arial>";
echo "<a href=http://$SITE/index.html>Voir les pages statiques</a><br>";
echo "</FONT>";
echo "</CENTER></P>";


?>
