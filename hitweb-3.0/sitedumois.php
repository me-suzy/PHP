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
// $Id: sitedumois.php,v 1.7 2001/06/18 09:25:15 hitweb Exp $

//########################################################################################
//# Fonction site du mois
//########################################################################################

function sitedumois()
{
  global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
  global $class_db ;
  global $tpl ;
  global $EXT_TPL, $EXT_PHP ;
 
  $base = new class_db ;
  //$base->debug = 1; 
  $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");

  $sql2 = "SELECT LIENS_ID, LIENS_ADRESSE, LIENS_DESCRIPTION, LIENS_COMMENTAIRES_ID, LIENS_PROTOCOL_ID ";
  $sql2 .= "FROM LIENS ";
  $sql2 .= "WHERE LIENS_COMMENTAIRES_ID = '4' "; 

  $base->query("$sql2");

  $num2 = $base->num_rows();

  $tpl->assign ( TITRE_AUTRE_RUBRIQUE, "Site du mois") ;

  if ($num2 > 0) {

    while ($base->next_record() ) {
      $LIENS_ID = $base->f("LIENS_ID");
      $LIENS_ADRESSE = $base->f("LIENS_ADRESSE");
      $LIENS_DESCRIPTION = $base->f("LIENS_DESCRIPTION");
      $LIENS_COMMENTAIRES_ID = $base->f("LIENS_COMMENTAIRES_ID");
      $LIENS_PROTOCOL_ID = $base->f("LIENS_PROTOCOL_ID");
    
      $tpl->assign ( array ( LIENS_ID_SEM => $LIENS_ID,
                             LIENS_ADRESSE_SEM => $LIENS_ADRESSE,
		                     LIENS_DESCRIPTION_SEM => stripslashes($LIENS_DESCRIPTION),
		 				     LIENS_COMMENTAIRES_ID_SEM => $LIENS_COMMENTAIRES_ID,
						     LIENS_PROTOCOL_ID_SEM => $LIENS_PROTOCOL_ID ));
    }

  } else {
    
    $tpl->assign ( array ( LIENS_ID_SEM => "&nbsp;",
			   LIENS_ADRESSE_SEM => "",
			   LIENS_DESCRIPTION_SEM => "<center>Pas de site du mois</center>",
			   LIENS_COMMENTAIRES_ID_SEM => "&nbsp;",
			   LIENS_PROTOCOL_ID_SEM => "&nbsp;" ));
  }
}

?>
