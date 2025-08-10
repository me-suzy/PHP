<?php

function export($formId = NULL) {
  if(!isset($formId)) {
    $message = $_SESSION['translate']->it("No form ID was passed");
    return new PHPWS_Error("phatform", "export()", $message, "continue", PHAT_DEBUG_MODE);
  }

  $exportDir = $GLOBALS['core']->home_dir . "files/phatform/export/";
  $path = $exportDir;

  clearstatcache();
  if(!is_dir($path)) {
    if(is_writeable($exportDir)) {
      mkdir($path, 0777);
    } else {
      $message = $_SESSION['translate']->it("The export path is not webserver writable.");
      return new PHPWS_Error("phatform", "export()", $message, "continue", PHAT_DEBUG_MODE);
    }
  } elseif(!is_writeable($path)) {
    $message = $_SESSION['translate']->it("The export path is not webserver writable.");
    return new PHPWS_Error("phatform", "export()", $message, "continue", PHAT_DEBUG_MODE);    
  }

  $sql = "SELECT * FROM mod_phatform_form_" . $formId;
  $GLOBALS["core"]->setFetchMode(DB_FETCHMODE_ASSOC);
  $result = $GLOBALS["core"]->getAll($sql, TRUE);

  if(sizeof($result) > 0) {
    foreach($result[0] as $key=>$value) {
      if($key != "position")
	$data .= $key . "\t";
    }

    foreach($result as $entry) {
      $data .= "\n";
      foreach($entry as $key=>$value) {
	if($key != "position") {
	  if($key == "updated") {
	    $value = date(PHPWS_DATE_FORMAT . " " . PHPWS_TIME_FORMAT, $value);
	  } else {
	    $value = str_replace("\t", " ", $value);
	    $value = str_replace("\r\n", "", $value);
	    $value = str_replace("\n", "", $value);

	    $temp = unserialize($value);
	    if(is_array($temp)) {
	      $value = implode(",", $temp);
	    }
	  }
	  $data .= "$value\t";
	}
      }
    }
  }

  $filename = "form_" . $formId . "_export." . time() . ".csv";
  $file = fopen($path . $filename, "w");
  fwrite($file, $data);
  fclose($file);

  $goCode = "zip -qmj " . $path . $filename . ".zip " . $path . $filename;
  system($goCode);

  header("Location: files/phatform/export/" . $filename . ".zip");
}

?>