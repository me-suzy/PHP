<?php

if (!$_SESSION["OBJ_user"]->isDeity()){
  header("location:index.php");
  exit();
}

$status = 1;

if ($currentVersion < 1.2){
  if ($status = $GLOBALS['core']->query("ALTER TABLE mod_pagemaster_pages ADD `template` varchar(50) NOT NULL AFTER `title`", TRUE))
    $status = $GLOBALS['core']->query("UPDATE mod_pagemaster_pages set template='default.tpl'", TRUE);

  if (!$status)
    $content .= "There was an error trying to update the mod_pagemaster_pages table.";
}

if($currentVersion < 1.3) {
  $content .= "Updating PageMaster to version 1.3<br />";
  $content .= "Adding columns \"comments\" and \"anonymous\" to \"mod_pagemaster_pages\".";
  $sql = "ALTER TABLE " . $GLOBALS["core"]->tbl_prefix .
     "mod_pagemaster_pages ADD (comments tinyint(1) NOT NULL DEFAULT '0',
                                anonymous tinyint(1) NOT NULL DEFAULT '0')";
  $GLOBALS["core"]->query($sql);
  $content .= "Columns added successfully!";
}

if($currentVersion < 1.31) {
  $content .= "Attempting to register with search module.<br />";
  if(isset($_SESSION["OBJ_search"])) {
    /* Register with search module */
    $search['module'] = "pagemaster";
    $search['search_class'] = "PHPWS_PageMaster";
    $search['search_function'] = "search";
    $search['search_cols'] = "title, text"; 
    $search['view_string'] = "&amp;PAGE_user_op=view_page&amp;PAGE_id=";
    $search['show_block'] = 1;
      
    if(!$GLOBALS["core"]->sqlInsert($search, "mod_search_register"))
      $content .= "There was a database problem when registering with search.<br />";
    else
      $content .= "Successfully registered with search module!<br />";
  }
}

if($currentVersion < 1.4) {
  $content .= "Adding page_id column to mod_pagemaster_sections.<br />";
  $sql = "ALTER TABLE " . $GLOBALS["core"]->tbl_prefix . "mod_pagemaster_sections ADD `page_id` int";
  $result = $GLOBALS["core"]->query($sql);
  if(DB::isError($result)) {
    $content .= "There was an error when attempting to alter the mod_pagemaster_sections table:<br />" .
       $result->getMessage() . "<br />";
    $status = 0;
  } else {
    $content .= "Column page_id successfully added to the mod_pagemaster_sections table.<br />";
  }

  if($status == 1) {
    $content .= "Attempting to set page_ids for all existing sections.<br /><br />";
    $sql = "SELECT id, section_order FROM " . $GLOBALS["core"]->tbl_prefix . "mod_pagemaster_pages";
    $result = $GLOBALS["core"]->getAll($sql);

    if(DB::isError($result)) {
      $content .= "There was an error when attempting to select from mod_pagemaster_pages:<br />" .
	 $result->getMessage() . "<br />";
      $status = 0;
    } elseif(sizeof($result) > 0) {
      foreach($result as $row) {
	$sections = array();
	if(isset($row["section_order"])) {
	  $section_order = unserialize($row["section_order"]);
	  if(is_array($section_order) && sizeof($section_order) > 0) {
	    foreach($section_order as $id) {
	      $sections[] = "id='$id'";
	    }
	  } else {
	    $content .= "No sections found for page " . $row["id"] . "<br />";
	    continue;
	  }

	  if(sizeof($sections) > 0) {
	    $where = "WHERE " . implode(" OR ", $sections);
	    $sql = "UPDATE " . $GLOBALS["core"]->tbl_prefix . "mod_pagemaster_sections SET page_id='" . $row["id"] . "' $where";
	    $result = $GLOBALS["core"]->query($sql);

	    if(DB::isError($result)) {
	      $content .= "There was an error when attempting to update mod_pagemaster_sections:<br />" .
		 $result->getMessage() . "<br />";
	      $status = 0;
	    } else {
	      $content .= "Updated sections for page <b>" . $row["id"] . "</b> successfully.<br />";
	    }
	  } else {
	    continue;
	  }
	}
      }
    } else {
      $content .= "No pages found in mod_pagemaster_pages.  Assuming no updates to section data needed.<br />";
    }
  }
}

?>