<?php

/**
 * PHPWS_Stats
 *
 * Class to generate stats for a phpwebsite table given certain table information.  This is 
 * just a helper class for other modules.  PHPWS_Stats expects a PHPWS_Core instantiated in
 * $GLOBALS['core'] in order to do its database interaction.
 *
 * @version $Id: Stats.php,v 1.1 2003/01/27 20:00:08 steven Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 */
class PHPWS_Stats {

  /**
   * Table to retrieve stats from.
   *
   * @var     string
   * @example $this->_tableName = "mod_phatform_form_1";
   * @access  private
   */
  var $_tableName;

  /**
   * Table information needed to retrieve stats.
   *
   * This array is keyed by the column name.  At each key is another array containing
   * all of the possible values for that column.
   *
   * @var     array
   * @example $this->_tableInfo = array("Element1"=>array(1,2,3),
   *                                    "Element2"=>array("Dog","Cat","Bird"),
   *                                   );
   * @access  private
   */
  var $_tableInfo;

  /**
   * The number of rows for the table thayt was set.
   *
   * @var     integer
   * @example $this->_rows = 712;
   * @access  private
   */
  var $_rows;

  /**
   * The raw data returned for the table information set by the developer
   *
   * @var     array
   * @example $this->_data = array("Element1"=>array(1=>204,2=>500,3=>8),
   *                               "Element2"=>array("Dog"=>154,"Cat"=>450,"Bird"=>108),
   *                              );
   * @access  private
   */
  var $_data;

  /**
   * The totals for each column
   *
   * @var     array
   * @example $this->_totals = array("Element1"=>712,
   *                                 "Element2"=>712
   *                                );
   * @access  private
   */
  var $_totals;

  /**
   * setTableName
   *
   * Provides developer ability to set table to report on.
   *
   * @param  string $table The name of the table to report on.
   * @access public
   */
  function setTableName($table) {
    if(is_string($table)) {
      $this->_tableName = $table;
    }
  }

  /**
   * setTableInfo
   *
   * Provides developer ability to set the table information to report on.
   *
   * @param  array  $info The table information to report on.
   * @access public
   */
  function setTableInfo($info) {
    if(is_array($info)) {
      $this->_tableInfo = $info;
    }
  }

  /**
   * generateData
   *
   * Call this when you are ready to generate your data
   *
   * @access public
   */
  function generateData() {
    if(!isset($this->_tableName) || !isset($this->_tableInfo)) {
      $message = $_SESSION['translate']->it("The table name and table info must be properly set.");
      return new PHPWS_Error("stats", "PHPWS_Stats::gimmeData()", $message, "continue", 0);
    }

    $this->_data = array();
    $sql[0] = "SELECT id FROM " . $GLOBALS['core']->tbl_prefix . $this->_tableName;
    $result = $GLOBALS['core']->query($sql[0]);
    $this->_rows = $result->numrows();
    $sql[0] .= " WHERE";
    foreach($this->_tableInfo as $column => $values) {
      $sql[2] = $sql[0];
      $this->_data[$column] = array();

      foreach($values as $value) {
	$sql[1] = $sql[0] . " " . $column . "='" . $value . "'";
	$result = $GLOBALS['core']->query($sql[1]);
	$this->_data[$column][$value] = $result->numrows();

	$sql[2] .= " " . $column . "='" . $value . "' OR";
      }

      $sql[2] = substr($sql[2], 0, -3);
      $result = $GLOBALS['core']->query($sql[2]);
      $this->_totals[$column] = $result->numrows();
    }
  } // END FUNC generateData()

  /**
   * getRows
   *
   * Returns the number of rows for this table (generateData must be called first!)
   *
   * @access public
   */
  function getRows() {
    return $this->_rows;
  }

  /**
   * getData
   *
   * Returns the data for this table (generateData must be called first!)
   *
   * @access public
   */
  function getData() {
    return $this->_data;
  }

  /**
   * getTotals
   *
   * Returns the totals for each column for this table (generateData must be called first!)
   *
   * @access public
   */
  function getTotals() {
    return $this->_totals;
  }

} // END CLASS PHPWS_Stats

?>