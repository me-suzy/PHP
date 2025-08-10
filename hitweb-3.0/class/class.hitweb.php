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
// $Id: class.hitweb.php,v 1.4 2001/07/19 15:22:15 hitweb Exp $

//########################################################################################
//# Class principale de l'application hitweb
//########################################################################################

class Hitweb {
 
    
  /*
   *  Function navigBarCategorie  : Affichage de la barre de navigation des categories 
   *  @author	                Brian FRAVAL 
   *  @contributor              Herimamy RATEFIARIVONY
   *
   *  @categories_parents_id	integer 0 = 1ere categories, > 0 = categories_parents
   *  @page	                string nom de la page pour le liens
   *  @html                     string information sur l'affichage txt ou HTML
   */
  

  function navigBarCategorie($categories_parents_id="0", $page, $html)
    {
      global $class_db ;
      global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
      global $categoriesnom, $categoriesid;
       
      if(!$categories_parents_id)
      {
        $categories_parents_id = "0";
      }

      $base = new class_db ;
      //$base->debug = 1; 
      $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
      
      $sql = "SELECT CATEGORIES_ID, CATEGORIES_NOM, CATEGORIES_PARENTS FROM CATEGORIES ";
      $sql .= "WHERE CATEGORIES_ID = '$categories_parents_id' ";
      
      $base->query("$sql");
      
      while (list ( $CATEGORIES_ID,
		    $CATEGORIES_NOM,
		    $CATEGORIES_PARENTS ) = $base->fetch_row())
	{
	  $categories_parents_id = $CATEGORIES_PARENTS;
	  $categoriesid[$compt] = $CATEGORIES_ID;
	  $categoriesnom[$compt] = $CATEGORIES_NOM; 
	}
      
      

      if ($html == "html")
	{
	  
	  if (isset($categoriesnom[$compt]))
	    {
	      $this->$liste = "<a href='$page?categories_parents_id=$categoriesid[$compt]'>".$categoriesnom[$compt]."</a> / ".$this->$liste;
	      
	    }
	  
	  if ($categories_parents_id=="0")
	    {
	      $this->$liste = "<a href='$page'>Hitweb</a> / ".$this->$liste;
	    } else {
	      $this->navigBarCategorie($categories_parents_id, $page, "html");
	    } 
	  
	} else {
	  
	  if (isset($categoriesnom[$compt]))
	    {
	      $this->$liste = $categoriesnom[$compt]." / ".$this->$liste;
	      
	    }
	  
	  if ($categories_parents_id=="0")
	    {
	      $this->$liste = "Hitweb / ".$this->$liste;
	    } else {
	      $this->navigBarCategorie($categories_parents_id, $page, "");
	    }
	}
    } // End function navigBarCategorie

   

  /*
   *  Function getProtocol  : Récupère les protocols pour les URL
   *  @author		      Brian FRAVAL 
   *
   *  @LIENS_PROTOCOL_ID      integer 
   */

  function getProtocol()
    {
       
      global $class_db ;
      global $DBNAME, $DBHOST, $DBUSER, $DBPASS ;
      global $tpl;
      
      // Affichage des protoles
      $base = new class_db ;
      //$base->debug = 1; 
      $base->connect("$DBNAME", "$DBHOST", "$DBUSER", "$DBPASS");
      

      $sql = "SELECT PROTOCOL_ID, PROTOCOL_NOM FROM PROTOCOL  ";
      

      $base->query("$sql");

      $num = $base->num_rows();
      
      if ($num > 0)
	{
	  while (list ( $PROTOCOL_ID,
			$PROTOCOL_NOM ) = $base->fetch_row())
	    {
	      $tpl->assign (PROTOCOL_ID, "$PROTOCOL_ID") ;
	      $tpl->assign (PROTOCOL_NOM, "$PROTOCOL_NOM") ;
	      
	      $tpl->parse (BLOCK, ".protocol" );
	    }
	  
	} else {
	  $tpl->assign (PROTOCOL_ID, "") ;
	  $tpl->assign (PROTOCOL_NOM, "") ;
	}

      
    } //End function getProtocol




  /*
   *  Function checkURL  : Analyse le code retour d'un lien 
   *  @author Michael BASCOU    
   *
   *  @url        string
   */
  function checkURL($url)
    {

      $struct_url = parse_url($url);
      /*
	echo "<br>scheme   : $struct_url[scheme]";
	echo "<br>host     : $struct_url[host]";
	echo "<br>port     : $struct_url[port]";	
	echo "<br>user     : $struct_url[user]";
	echo "<br>pass     : $struct_url[pass]";
	echo "<br>path     : $struct_url[path]";
	echo "<br>query    : $struct_url[query]";
	echo "<br>fragment : $struct_url[fragment]";
      */

      $fp = fsockopen($struct_url[host], 80, &$errno, &$errstr, 30);
                  
      if(!$fp) { 
	
	return 0;
      
      } else { 
	
	if(!$struct_url[path])
	  { 
	    $request = "GET / HTTP/1.0 /"; 
	  } else { 
	    $request = "GET $struct_url[path] HTTP/1.0 /"; 
	  }
	
	fputs($fp,"$request \n\n");
	$buffer = fgets($fp, 128);                
	fclose($fp);
	
	$back_list = split( " ", $buffer, 10 );
	return $back_list[1];
      }  
    }





} // End class hitweb

?>
