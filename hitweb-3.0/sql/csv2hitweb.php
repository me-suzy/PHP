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
// $Id: csv2hitweb.php,v 1.3 2001/07/19 15:43:23 hitweb Exp $

//########################################################################################
//# Fichier concernant le mtp et le login de connection à la base MySql
//########################################################################################
include "../conf/hitweb.conf" ;


//########################################################################################
//# Utilisation des CLASS FastTemplates et Base de données
//########################################################################################
include "../$REP_CLASS/class.db_mysql".$EXT_PHP ;


//########################################################################################
//# CLASS d'importation à partir de fichier CSV 
//########################################################################################
include "../$REP_CLASS/class.csv2hitweb".$EXT_PHP ;




$base = new class_db ;
$base->debug = 1; 
$base->connect("hitwebcsv", "$DBHOST", "$DBUSER", "$DBPASS");


$import = new csv_import ;


if ( !$file )
{

  echo "Vous devez donner le nom du fichier CSV (file=fichier.csv)<p>";
  exit;
  
} else {
  
  //fichier CSV à importer dans la base de données
  $import->open_file("../sql/$file");

}




if ( !$table )
{ 
  
  echo "Vous devez données le nom de la TABLE (table=categories)<p>";
  exit;
  
} else {
  
  // Nom de la table à mettre à jour
  $import->import("$table");
  $import->close_file();
  
} 


?>
