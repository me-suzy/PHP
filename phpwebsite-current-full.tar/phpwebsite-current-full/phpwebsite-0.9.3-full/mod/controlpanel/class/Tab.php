<?php

require (PHPWS_SOURCE_DIR . "mod/controlpanel/class/Link.php");

define("CP_DEFAULT_GRID_SIZE", 3);

class PHPWS_ControlPanel_Tab extends PHPWS_Item {

  var $_links = NULL;
  var $_grid = CP_DEFAULT_GRID_SIZE;
  var $_taborder = NULL;
  var $_title = NULL;

  function PHPWS_ControlPanel_Tab($id=NULL) {
    $this->setTable("mod_controlpanel_tab");

    if(isset($id)) {
      $this->setId($id);
      $this->init();
    }

  }

  function setTitle($title){
    $this->_title = $GLOBALS['core']->parseInput($title);
  }

  function getTitle(){
    return $this->_title;
  }

  function setOrder($order){
    $this->_taborder = $order;
  }

  function addLink($id){
    $this->_links[] = $id;
  }

  function dropLink($id){
    if (!is_array($this->_links))
      return TRUE;

    $link = array_search($id, $this->_links);
    if (is_numeric($link)){
      unset($this->_links[$link]);
      return TRUE;
    } else
      return FALSE;
  }

  function setLinks($links){
    $this->_links = NULL;
    if (!is_array($links))
      return;

    foreach ($links as $id)
      $this->_links[$id] = new PHPWS_ControlPanel_Link($id);
  }

  function getLinks(){
    return $this->_links;
  }

  function setGrid($size){
    if (!is_numeric($size)){
      $message = $_SESSION["translate"]->it("The size <b>$size</b> was not a number") . ".";
      return new PHPWS_Error("controlpanel", "PHPWS_ControlPanel_Tab::setGrid", $message);
    }

    $this->_grid = $size;
  }

  function getTab(){
    if ($this->isHidden() || !isset($this->_links))
      return NULL;
    $colspan = NULL;
    $allRows = array();
    $cell = $count = 1;    
    $grid = $this->_grid;
    $imageStatus = $_SESSION['OBJ_user']->getUserVar("image_status");
    $descStatus =  $_SESSION['OBJ_user']->getUserVar("desc_status");

    if (!isset($imageStatus[$this->getId()]))
      $imageStatus[$this->getId()] = 1;

    if (!isset($descStatus[$this->getId()]))
      $descStatus[$this->getId()] = 1;

    if (count($this->_links)){
      foreach ($this->_links as $id) {
	$newlink = new PHPWS_ControlPanel_Link($id);
	if ($newlink->isViewable()) {
	  $newlink->showImage((bool)$imageStatus[$this->getId()]);
	  $newlink->showDescription((bool)$descStatus[$this->getId()]);

	  $links[$count] = $newlink->get();
	  $count++;
	} else {
	  continue;
	}
      }

      if (!isset($links)) {
	return NULL;
      }

      $total = count($links);
    } else {
      return NULL;
    }

    $rows = floor($total / $grid);

    if ($remainder = $total % $grid) {
      $rows++;
    }

    for($i = 0; $i < $rows; $i++) {
      $allCells = array();
      $cellTpl = array();
      $cellTpl['WIDTH'] = "width=\"" . floor( 100 / $grid) . "%\"";

      for ($j=0; $j < $grid; $j++, $cell++) {
	if ($cell <= $total) {
	  $cellTpl["CONTENT"] = $links[$cell];
	} else {
	  $cellTpl['CONTENT'] = "&nbsp;";
	}

	$allCells[] = $GLOBALS['core']->processTemplate($cellTpl, "controlpanel", "grid/cell.tpl");
      }

      $allCellTpl = array('CELLS'=>implode("", $allCells));

      $allRows[] = $GLOBALS['core']->processTemplate($allCellTpl, "controlpanel", "grid/row.tpl");
    }
    $allRowTpl = array('ROWS'=>implode("", $allRows));

    if ($imageStatus[$this->getId()]) {
      $allRowTpl['IMAGE_SWITCH'] = $GLOBALS['core']->moduleLink($_SESSION["translate"]->it("Image Off"), "controlpanel", array("cp_image_toggle"=>1, "tab"=>$this->getId()));
    } else {
      $allRowTpl['IMAGE_SWITCH'] = $GLOBALS['core']->moduleLink($_SESSION["translate"]->it("Image On"), "controlpanel", array("cp_image_toggle"=>1, "tab"=>$this->getId()));
    }

    if ($descStatus[$this->getId()]) {
      $allRowTpl['DESC_SWITCH'] = $GLOBALS['core']->moduleLink($_SESSION["translate"]->it("Desc Off"), "controlpanel", array("cp_desc_toggle"=>1, "tab"=>$this->getId()));
    } else {
      $allRowTpl['DESC_SWITCH'] = $GLOBALS['core']->moduleLink($_SESSION["translate"]->it("Desc On"), "controlpanel", array("cp_desc_toggle"=>1, "tab"=>$this->getId()));
    }

    return $GLOBALS['core']->processTemplate($allRowTpl, "controlpanel", "grid/grid.tpl");
  }

  function tabExists($label){
     return $GLOBALS['core']->getOne("select id from mod_controlpanel_tab where label='$label'", TRUE);
  }


  function save(){
    if (isset($this->_label) && isset($this->_title)){
      
      $id = $this->getId();

      if (!isset($id)){
	if(!($order = $GLOBALS['core']->sqlMaxValue($this->getTable(), "taborder")))
	  $order = 1;
	else
	  $order++;

	$this->setOrder($order);
      }
      $this->commit();
    }
    else {
      $message = $_SESSION["translate"]->it("Label and Title are not set") . ".";
      return new PHPWS_Error("controlpanel", "PHPWS_ControlPanel_Tab::save", $message);
    }
  }


  function load($label){
    $id = $GLOBALS['core']->getOne("select id from mod_controlpanel_tab where label='$label'", TRUE);
    if ($id){
      $this->setId($id);
      $this->init();
    } else {
      $message = $_SESSION["translate"]->it("Tab <b>$label</b> does not exist") . ".";
      return new PHPWS_Error("controlpanel", "PHPWS_ControlPanel_Tab::load", $message);
    }
  }


  function toggleImage($tab){
    $image = $_SESSION['OBJ_user']->getUserVar("image_status");
    if (!isset($image[$tab]) || $image[$tab])
      $_SESSION['OBJ_user']->setUserVar("image_status", array($tab=>0));
    else
      $_SESSION['OBJ_user']->setUserVar("image_status", array($tab=>1));
  }

  function toggleDesc($tab){
    $desc = $_SESSION['OBJ_user']->getUserVar("desc_status");
    if (!isset($desc[$tab]) || $desc[$tab])
      $_SESSION['OBJ_user']->setUserVar("desc_status", array($tab=>0));
    else
      $_SESSION['OBJ_user']->setUserVar("desc_status", array($tab=>1));
  }

  function isEmpty(){
    if (!isset($this->_links))
      return TRUE;

    foreach($this->_links as $id){
      $link = new PHPWS_ControlPanel_Link($id);
      if ($link->isViewable())
	return FALSE;
    }

    return TRUE;
  }
}

?>