<?php

// The default rating applied to new elements
define("DEFAULT_RATING" , 50);


class PHPWS_Fatcat_Elements extends PHPWS_Fatcat_Category{
  function updateDate($element_id, $date=NULL){
    if (is_null($date))
      $date = date("Ymd");

    if ($date < 0 || $date > 30000101)
      return FALSE;

    return $GLOBALS['core']->sqlUpdate(array('created'=>$date), 'mod_fatcat_elements', 'element_id', (int)$element_id);

  }

  function activate($module_id, $module_title=NULL){
    if (!$GLOBALS["core"]->isValidInput($module_title))
      $module_title = $GLOBALS["core"]->current_mod;

    return $GLOBALS["core"]->sqlUpdate(array("active"=>1), "mod_fatcat_elements", array("module_title"=>$module_title, "module_id"=>$module_id));

  }

  function deactivate($module_id, $module_title=NULL){
    if (!$GLOBALS["core"]->isValidInput($module_title))
      $module_title = $GLOBALS["core"]->current_mod;

    return $GLOBALS["core"]->sqlUpdate(array("active"=>0), "mod_fatcat_elements", array("module_title"=>$module_title, "module_id"=>$module_id));

  }

  function getElement($id, $cat_id, $module_title){
    $cat_id = (int)$cat_id;
    if (!PHPWS_Text::isValidInput($module_title))
      return NULL;
    $sql  = "select * from mod_fatcat_elements where module_id=$id and cat_id=$cat_id and module_title='$module_title'";

    $element = $GLOBALS["core"]->getAllAssoc($sql, TRUE);

    if (empty($element) || !is_array($element))
      return NULL;

    extract($element[0]);

    if ($href == "away")
      $final_link = $GLOBALS["core"]->checkLink($link);
    else
      $final_link = $link;

    return $GLOBALS["core"]->link($final_link, $title, "index");
  }

  /**
   * Categorizes an element from a module
   *
   * After using the showSelect function, you would call this function
   * to catch the data. If you are inserting data make sure you send the id
   * of the new element.
   *
   * @author                     Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param string  title        Title of the element from your module
   * @param string  link         Link that will send a user to your module's element
   * @param int     module_id    ID number of your module's element
   * @param array   groups       Groups that an element may be viewed by
   * @param string  module_title Name of the module sending the information
   * @param string  href         If set to 'away', FatCat will assume the link leads offsite
   * @param int     rating       Number 1 - 100 that indicates the importance of an element
   * @param boolean active       If TRUE, enable the category element
   */
  function saveSelect($title, $link, $module_id, $groups=NULL, $module_title=NULL, $href=NULL, $rating=NULL, $active=TRUE){
    if (!is_numeric($module_id) || $module_id < 1)
      exit("saveSelect error: module id is not a number or is zero");

    if (!$GLOBALS["core"]->moduleExists($module_title))
      if (!($module_title = $GLOBALS["core"]->current_mod))
	exit("saveSelect error: Unable to pull module information for <b>".$GLOBALS["core"]->current_mod."</b>");


    if (!isset($_POST["fatSelect"][$module_title]) || !($categories = $_POST["fatSelect"][$module_title])){
      if (isset($_POST["fatcatProcess"]))
	$this->deleteModuleElements($module_title, $module_id);
      return;
    }

    $this->deleteModuleElements($module_title, $module_id);

    if (isset($_POST["fatSticky"][$module_title]))
      $rating = 999;
    else
      $rating = NULL;

    if (is_array($categories)){
      foreach ($categories as $cat_id)
	$this->saveElement($cat_id, strip_tags($title), strip_tags($link), $module_id, $module_title, $groups, $href, $rating, $active); 
    } elseif (is_numeric($categories))
	$this->saveElement($categories, strip_tags($title), strip_tags($link), $module_id, $module_title, $groups, $href, $rating, $active); 
  }


  function saveElement($cat_id, $title, $link, $module_id, $module_title, $groups=NULL, $href=NULL, $rating=NULL, $active=TRUE){
    $insert["cat_id"]   = (int)$cat_id;

    $insert["title"] = $GLOBALS["core"]->parseInput($title, "none");

    $insert["module_id"] = (int)$module_id;

    if ($groups) {
      if (is_array($groups))
	$insert["groups"] = implode(":", $groups);
      else
	$insert["groups"] = $groups;
    }

    if ($GLOBALS["core"]->isValidInput($module_title))
      $insert["module_title"] = $module_title;
    else
      exit("saveElement error: $module_title is not a valid module name");

    if (is_null($rating))
      $rating = DEFAULT_RATING;

    $rating = (int)$rating;
    if ($rating != 999 && ($rating < 1 || $rating > 100))
      exit("saveElement error: rating must be between 1 and 100");
    else
      $insert["rating"] = $rating;

    // href defaults to 'home' in the database. Anything other than "away" is ignored
    if ($href == "away" || $href == "AWAY")
      $insert["href"] = "away";

    $insert["link"] = strip_tags($link);

    if ($active)
      $insert["active"] = 1;
    else
      $insert["active"] = 0;

    $insert["created"] = (int)date("Ymd");
    $GLOBALS["core"]->sqlInsert($insert, "mod_fatcat_elements", 1);
  }

  function removeElement($element_id){
    return $GLOBALS["core"]->sqlDelete("mod_fatcat_elements", "element_id", (int)$element_id);
  }


  function getAllElements($cat_id = NULL){
    $limit =  $this->settings["relatedLimit"];
    $modList = array();

    if ($cat_id)
      $where["cat_id"] = (int)$cat_id;

    $orderby = array("created desc");
    
    $sql = "select * from mod_fatcat_elements where cat_id=".(int)$cat_id." and active=1 order by rating desc, created desc";

    if (!($row = $GLOBALS["core"]->getAllAssoc($sql, TRUE)))
      return NULL;

    foreach ($row as $setElements){
      $mod_title = $setElements['module_title'];
      if (!isset($modList[$mod_title]) || $setElements['rating'] == '999' || $modList[$mod_title] < $limit)
	$finalRow[$setElements["element_id"]] = $setElements;
      else
	continue;

      if (!isset($modList[$mod_title]))
	$modList[$mod_title] = 1;
      else
	$modList[$mod_title]++;
    }

    return $finalRow;
  }


  function getElementCatId($element_id){
    if(!($row = $GLOBALS["core"]->sqlSelect("mod_fatcat_elements", "element_id", $element_id)))
      return NULL;

    return $row[0]["cat_id"];
  }

  function boostRating($elementArray){
    $maximum = 100;

    if (!is_array($elementArray) || !$elementArray)
      return NULL;

    foreach ($elementArray as $elements){
      extract($elements);
      $count[$module_title][$module_id] = (isset($count[$module_title][$module_id])) ? $count[$module_title][$module_id] + 1 : 1;
      if ($count[$module_title][$module_id] > 1)
	$repeats[] = $element_id;
    }

    if (isset($repeats))
      foreach ($repeats as $repeated_id)
	unset($elementArray[$repeated_id]);

    foreach ($elementArray as $elements){
      extract($elements);
      $rating = $elementArray[$element_id]["rating"];

      if ($count[$module_title][$module_id]){
	$newRating = $rating + ($this->settings["multipleGroup"] * ($count[$module_title][$module_id] - 1));
	if ($newRating <= 100 || $rating > 100) 
	  $elementArray[$element_id]["rating"] = $newRating;
	else
	  $elementArray[$element_id]["rating"] = 100;
      }
    }

    return $elementArray;
  }

  function orderRating($elementArray){
    foreach ($elementArray as $element_id=>$info){
      extract($info);
      $newArray[$rating][$element_id] = $info;
    }
    krsort($newArray);

    return $newArray;
  }
 
  function getModulesCategories($module_title, $module_id){
    if (!($row = PHPWS_Fatcat_Elements::getModuleElements($module_title, $module_id)))
      return NULL;

    foreach ($row as $info)
      $categories[] = $info["cat_id"];

    return $categories;

  }

  function getModuleElements($module_title, $module_id=NULL, $cat_id=NULL){
    if (!PHPWS_Text::isValidInput($module_title))
      exit("getElementCategory error: <b>$module_title</b> is not a valid module_title.");

    $where["module_title"] = $module_title;
    if (!is_null($module_id))
      $where["module_id"] = (int)$module_id;

    if (!is_null($cat_id))
      $where["cat_id"] = (int)$cat_id;

    $row = $GLOBALS["core"]->sqlSelect("mod_fatcat_elements", $where);
    return $row;
  }

  function deleteModuleElements($module_title, $module_id=NULL){
    if (!$GLOBALS["core"]->isValidInput($module_title))
      exit("deleteModuleElements error: <b>$module_title</b> is not a valid module_title.");

    $where["module_title"] = $module_title;
    if (!is_null($module_id))
      $where["module_id"] = (int)$module_id;

    return $GLOBALS["core"]->sqlDelete("mod_fatcat_elements", $where);
  }

  function showWhatsRelated($element_id){
    $usedElements = array();

    $catalystRating = 2;

    if (empty($element_id))
      return NULL;
    
    if (!is_array($element_id))
      $elements[] = $element_id;
    else
      $elements = $element_id;

    foreach ($elements as $id)
      $category_list[] = $this->getElementCatId($id);

    foreach ($category_list as $cat_id){
      $elementSec = $this->getAllElements($cat_id);

      if (isset($allElements))
	$allElements = $elementSec + $allElements;
      else
	$allElements = $elementSec;
      $indexedCats[$cat_id] = new PHPWS_Fatcat_Category($cat_id);
    }

    foreach ($elements as $id)
      unset($allElements[$id]);

    if (!$allElements || !count($allElements))
      return NULL;

    ksort($allElements);

    $allElements = $this->boostRating($allElements);
    $allElements = $this->orderRating($allElements);
    $order = NULL;

    foreach ($allElements as $rating=>$elements){
      foreach ($elements as $elementInfo){
	$nextSpace = 0;
	extract($elementInfo);
	if ($href == "away")
	  $final_link = $GLOBALS["core"]->checkLink($link);
	else
	  $final_link = $link;

	$titleSize = strlen($title);
	if ($titleSize > FATCAT_LINK_CUTOFF + FATCAT_LINK_BUFFER){
	  $checkBuffer = substr($title, -($titleSize - FATCAT_LINK_CUTOFF));
	  $nextSpace = strpos($checkBuffer, " ");
	  
	  if ($nextSpace !== FALSE && $nextSpace <= FATCAT_LINK_BUFFER)
	    $title = substr($title, 0, FATCAT_LINK_CUTOFF + $nextSpace) . "...";
	  else
	    $title = substr($title, 0, FATCAT_LINK_CUTOFF) . "...";

	}
	$entry = $GLOBALS["core"]->link($final_link, $title, "index");
	$order[$module_title][$cat_id][] = $entry;  
      }
    }

    if (!$order)
      return NULL;
    $related_tpl["CONTENT"] = NULL;
    foreach ($order as $module_title=>$catlinks){

      $modLinks_tpl['CATLINKS'] = NULL;
      if (!$GLOBALS["core"]->moduleExists($module_title))
	continue;

      $mod_info = $GLOBALS["core"]->getModuleInfo($module_title);

      $modLinks_tpl["TITLE"] = $mod_info["mod_pname"];
      foreach ($catlinks as $catId=>$links){
	$catLinks_tpl['LINKS'] = $catLinks_tpl['CATNAME'] = NULL;

	$catLinks_tpl["CATNAME"] = $this->displayCategoryLink($indexedCats[$catId]->title, $catId, $module_title);
	foreach ($links as $link) {
	  $link_tpl["LINK"] = $link;
	  $catLinks_tpl["LINKS"] .= $GLOBALS["core"]->processTemplate($link_tpl, "fatcat", "related/link.tpl");
	}
	$modLinks_tpl["CATLINKS"] .= $GLOBALS["core"]->processTemplate($catLinks_tpl, "fatcat", "related/catLinks.tpl");
      }
      $related_tpl["CONTENT"] .= $GLOBALS["core"]->processTemplate($modLinks_tpl, "fatcat", "related/modLinks.tpl");
    }
    $related_tpl["INTRO"] = $this->settings["relatedtext"];

    /*
     // No longer used
    $allCats = $this->createCategoryLinks($category_list[0], $module_title);

    $related_tpl["CATLIST"] = implode("", $allCats);
    */
    return $GLOBALS["core"]->processTemplate($related_tpl, "fatcat", "related/related.tpl");
  }


}
?>