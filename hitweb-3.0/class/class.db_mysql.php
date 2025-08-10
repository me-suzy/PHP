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
// $Id: class.db_mysql.php,v 1.7 2001/06/20 19:19:40 hitweb Exp $

/*
Cette class est un mix de plusieurs class sur les BD 
que j'ai trouvé sur le net, mais la plus complète est
certainement celle de phplib...


Pour utiliser l'option DEBUG pendant le developpement il suffit
de faire passer le paramètre Debug à 1

$base = new class_db ;
$base->debug = 1; 
*/

class class_db {
  
  /* PLUBIC : Paramètre de connection */
  var $dbname = "";
  var $dbhost = "";
  var $dbuser = "";
  var $dbpass = ""; 
  
  /* PUBLIC : Paramètre de configuration */
  var $debug  = 0;     ## Passer ce paramètre à 1 pour le débuger.
  

  /* PUBLIC : result array and current row number */
  var $Record = array();
  var $Row;

  /* PUBLIC : current error number and error text */
  var $Errno  = 0;
  var $Error  = "";



  /* Connection à la base */
  function connect($dbname = "", $dbhost = "localhost", $dbuser = "", $dbpass = "") {
      
   /* Etablir la connection, */
   if ( 0 == $this->Link_ID ) {
    
      $this->Link_ID=@mysql_pconnect($dbhost, $dbuser, $dbpass);
      if (!$this->Link_ID) {
        $this->halt("ERREUR DE CONNECTION  A LA BASE");
        return 0;
      }

   /* Selection de la base */
      if (!@mysql_select_db($dbname,$this->Link_ID)) {
        $this->halt("PROBLEME SELECTION DE LA BASE ".$this->dbname);
        return 0;
      }
    }
    
    return $this->Link_ID;
  }


  /*   */
  function free() {
      @mysql_free_result($this->Query_ID);
      $this->Query_ID = 0;
  }


  /* REQUETE SQL */
  function query($Query_String) {
    if ($Query_String == "")
      return 0;

    if (!$this->connect()) {
      return 0; /* S'il n'y a pas de connection stoper cette requete  */
    };

    # New query, discard previous result.
    if ($this->Query_ID) {
      $this->free();
    }

      if ($this->debug) {
        printf("<b>DEBUG : Query =</b> %s<p>\n", $Query_String);
        flush();
      }

    $this->Query_ID = @mysql_query($Query_String,$this->Link_ID);
    $this->Row   = 0;
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();
    if (!$this->Query_ID) {
      $this->halt("Invalid SQL : ".$Query_String);
    }

    # Will return nada if it fails. That's fine.
    return $this->Query_ID;
  }



  /* Passer à l'enregistrement suivant */
  function next_record() {
    if (!$this->Query_ID) {
      $this->halt("PAS DE REQUETE SQL");
      return 0;
    }

    $this->Record = @mysql_fetch_array($this->Query_ID);
    $this->Row   += 1;
    $this->Errno  = mysql_errno();
    $this->Error  = mysql_error();

    $stat = is_array($this->Record);
    if (!$stat && $this->Auto_Free) {
      $this->free();
    }
    return $stat;
  }

  function result($row, $fieldname) {
    return @mysql_result($this->Query_ID,$row, $fieldname);
  }

  function num_rows() {
    return @mysql_num_rows($this->Query_ID);
  }

  function num_fields() {
    return @mysql_num_fields($this->Query_ID);
  }

  function insert_id() {
    return @mysql_insert_id() ;
  }

  function fetch_row() {
    return @mysql_fetch_row($this->Query_ID);
  }

  
  function f($Name) {
	return $this->Record[$Name];
  }


  function p($Name) {
	print $this->Record[$Name];
  }

  
  /* Affichage des messages d'erreur */
  function halt($msg) {
    $this->Error = @mysql_error($this->Link_ID);
    $this->Errno = @mysql_errno($this->Link_ID);
    if ($this->Halt_On_Error == "no")
      return;

    $this->haltmsg($msg);

    if ($this->Halt_On_Error != "report")
      die("Session halted.</html>");
  }

  function haltmsg($msg) {
    printf("<html><b>Base de données :</b> %s<br>\n", $msg);
    printf("<b>MySQL</b>: %s (%s)<br>\n",
      $this->Errno,
      $this->Error);
  }

}

?>