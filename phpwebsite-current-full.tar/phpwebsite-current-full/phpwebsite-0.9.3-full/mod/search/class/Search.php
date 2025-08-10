<?php

/**
 * Class file for search module
 *
 * @version $Id: Search.php,v 1.10 2003/03/28 18:43:46 matt Exp $
 * @author Steven Levin <steven@NOSPAM.tux.appstate.edu, steven@NOSPAM.tux.appstate.edu>
 * @module search
 * @modulegroup phpws_mods
 * @package phpWebSite
 */
class PHPWS_Search {

  var $search_query;
  var $search_results;

  /**
   * Constructor for the search class
   *
   * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
   * @param none
   * @return none
   */
  function PHPWS_Search(){
    $this->search_results = array();
  }


  /**
   * The search function
   *
   * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
   * @modified Matt McNaney <matt@NOSPAM.tux.appstate.edu>
   * @return none
   */
  function search(){
    $this->search_query = preg_replace("/[^\w\s]/", "", $_REQUEST['SEA_search_string']);
    $module = $_REQUEST['SEA_search_module'];
    $this->show_search_block($module);

    if($module == "All"){
      $sql = "SELECT module, search_class, search_function, search_cols, view_string FROM " . $GLOBALS['core']->tbl_prefix . "mod_search_register ORDER BY module ASC";
    } else{
      $sql = "SELECT module, search_class, search_function, search_cols, view_string FROM " . $GLOBALS['core']->tbl_prefix . "mod_search_register WHERE module='" . $module . "'";
    }

    $module_info = $GLOBALS['core']->query($sql);

    if(!$module_info->numrows()){
      if(!$module_info){
	$GLOBALS['CNT_search_results']['content'] = $_SESSION['translate']->it("The are no modules registered with the search database");
      } else{
	$GLOBALS['CNT_search_results']['content'] = $_SESSION['translate']->it("The module [var1] is not registered with the search database", $module);
      }
    }
    
    $x = 0;
    $highlight = NULL;
    $this->search_results = array();
    $search_array = explode(" ", $this->search_query);

    while($module_row = $module_info->fetchrow(DB_FETCHMODE_ASSOC)){
      extract($module_row);
      
      if($search_cols) {
	$cols_array = explode(", ", $search_cols);
	
	$sql = NULL;
	$where_clause = "WHERE ";
	
	for($i=0; $i<count($cols_array); $i++){
	  for($j=0; $j<count($search_array); $j++){
	    $sql .= "$cols_array[$i] LIKE '%$search_array[$j]%' ";
	    if($j<count($search_array)){
	      $sql .= "OR ";
	    }
	  }
	}
	$where_clause .= "(" . substr($sql, 0, -3) . ")";

	if (class_exists($search_class)){
	  $tempObj = new $search_class;
	  if (method_exists($tempObj, $search_function))
	    $results = $tempObj->$search_function($where_clause);
	}
      } else {
	if (class_exists($search_class)){
	  $tempObj = new $search_class;
	  if (method_exists($tempObj, $search_function))
	    $results = $tempObj->$search_function($search_array);
	}
      }

      if($results) {
	foreach($results as $id=>$summary){
	  $this->search_results[$x] = "<tr $highlight>";
	  $this->search_results[$x] .= "<td>" . ($x + 1) . "</td>";
	  $this->search_results[$x] .= "<td>" . $summary . "</td>";
	  $this->search_results[$x] .= "<td align=\"center\"><a href=\"index.php?module=" . $module . $view_string . $id . "\">" . $_SESSION["translate"]->it("View") . "</a></td></tr>";
	  $x++;
	  $GLOBALS['core']->toggle($highlight, " class=\"bg_light\"");
	}
      }
    }
    $this->show_results();
  }


  /**
   * Show results for current search
   *
   * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
   * @param  none
   * @return none
   */
  function show_results(){
    $title = $_SESSION['translate']->it("Search Results for") . "&#160;"  . $this->search_query;
    $content = "<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"1\">\n";
    $content .= "<tr class=\"bg_medium\"><td width=\"5%\">&#35;</td><td>Summary</td><td align=\"center\" width=\"20%\">Action</td></tr>";

    if(count($this->search_results) > 0) {
      $page_data = $GLOBALS['core']->paginateDataArray($this->search_results, "./index.php?module=search&#38;SEA_search_op=continue", SEA_DEF_RESULT_LIMIT, 1, array("<b>[ ", " ]</b>"), NULL, 20);
    }

    if($page_data[0]) {
      $content .= $page_data[0] . "</table><br /><br /><div align=\"center\">" . $page_data[1] . "<br />" . $page_data[2] . "&#160;" . $_SESSION['translate']->it("Results") . "</div>";
    } else {
      $content .= "<tr><td colspan=\"3\">" . $_SESSION['translate']->it("No results were returned for your search query.") . "</td></tr></table>";
    }

    $_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_search_results");
  }


  /**
   * Allows other modules to register with the search module
   *
   * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
   * @param  string  module name of module registering
   * @param  string  search_object object to call search for
   * @param  string  search_function search function for the module
   * @param  string  search_cols all of the searchable columns
   * @param  string  view_string operation string needed to view item
   * @param  boolean show_block flag whether or not search block is show for the module
   * @return none
   */
  function register($module, $search_object, $search_function, $search_cols, $view_string, $show_block){
    $save_array = array("module"=>"$module",
			"search_object"=>"$search_object",
			"search_function"=>"$search_function",
			"search_cols"=>"$search_cols",
			"view_string"=>"$view_string",
			"show_block"=>"$show_block"
			);

    $GLOBALS['core']->sqlInsert($save_array, "mod_search_register");
  }


  /**
   * Search block for search module
   *
   * @author Steven Levin <steven@NOSPAM.tux.appstate.edu>
   * @param  string the individual module to make the search block for
   * @return none
   */
  function show_search_block($module){
    if (!isset($GLOBALS['CNT_search_block']['content']))
      $GLOBALS['CNT_search_block']['content'] = NULL;

    $sql = "SELECT mod_pname FROM " . $GLOBALS['core']->tbl_prefix . "modules WHERE mod_title='$module'";
    $mod_result = $GLOBALS['core']->query($sql);
    $mod_info = $mod_result->fetchrow(DB_FETCHMODE_ASSOC);

    $GLOBALS['CNT_search_block']['title'] = $_SESSION['translate']->it("Search") . " " . $mod_info["mod_pname"];

    $hiddens = array("module"=>"search",
		     "SEA_search_op"=>"search",
		     "SEA_search_module"=>"$module"
		     );

    if(!isset($elements[0])) {
      $elements[0] = NULL;
    }

    $elements[0] = $GLOBALS['core']->formHidden($hiddens);
    $elements[0] .= $GLOBALS['core']->formTextField("SEA_search_string", NULL, 15, 100) . "<br />";
    $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Search"));

    $GLOBALS['CNT_search_block']['content'] .= $GLOBALS['core']->makeForm("SEA_search_block", "index.php", $elements, "get", NULL, NULL);
  }

  function search_form(){
    $GLOBALS['CNT_search_results']['title'] = $_SESSION['translate']->it("Search");

    $hiddens = array("module"=>"search",
		     "SEA_search_op"=>"search",
		     "SEA_search_module"=>"All"
		     );

    $elements[0] = $GLOBALS['core']->formHidden($hiddens);
    $elements[0] .= $GLOBALS['core']->formTextField("SEA_search_string", NULL, 30, 100) . "&#160;&#160;";
    $elements[0] .= $GLOBALS['core']->formSubmit($_SESSION['translate']->it("Search"));

    $GLOBALS['CNT_search_results']['content'] .= $GLOBALS['core']->makeForm("SEA_search_block", "index.php", $elements, "get", NULL, NULL);
  }

}
?>
