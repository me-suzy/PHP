<?php

class PHAT_File extends PHPWS_Item {

  var $_description = NULL;
  var $_size = NULL;
  var $_type = NULL;

  function PHAT_File($FILE_ID = NULL) {
    $this->setTable("mod_phatfile_files");

    if(isset($FILE_ID)) {
      $this->setId($FILE_ID);
      $this->init();
    }
  }// END FUNC PHAT_File

  function edit() {
    if(isset($this->_id)) {
      $tags["TITLE"] = $_SESSION["translate"]->it("Edit File");
      $elements[] = PHPWS_Form::formHidden("FILE_ID", $this->_id);
    } else {
      $tags["TITLE"] = $_SESSION["translate"]->it("Add File");
    }

    if($this->getLabel()) {
      $tags["CURRENT_FILE_LABEL"] = $_SESSION["translate"]->it("Current File");
      $tags["CURRENT_FILE"] = $this->link();
    }

    $tags["BACK"] = "<a href=\"index.php?module=phatfile&amp;FILE_MAN_OP=Main\">" .
      $_SESSION["translate"]->it("Back") . "</a>";
    $tags["FILE_UPLOAD_LABEL"] = $_SESSION["translate"]->it("Upload File");
    $tags["DESCRIPTION_LABEL"] = $_SESSION["translate"]->it("Description");
    $tags["FILE_UPLOAD"] = PHPWS_Form::formFile("PHAT_FILE");
    $tags["DESCRIPTION"] = PHPWS_Form::formTextArea("PHAT_DESC", $this->_description);
    $tags["SUBMIT_BUTTON"] = PHPWS_Form::formSubmit($_SESSION["translate"]->it("Submit"), "submit");

    if(isset($_SESSION["OBJ_fatcat"])) {
      $tags["CATEGORY_SELECT"] = $_SESSION["OBJ_fatcat"]->showSelect($this->_id, "multiple", 4);
    }

    $elements[] = PHPWS_Form::formHidden("module", "phatfile");
    $elements[] = PHPWS_Form::formHidden("FILE_OP", "Save");
    $elements[] = $GLOBALS["core"]->processTemplate($tags, "phatfile", "edit.tpl");

    return PHPWS_Form::makeForm("PHAT_FILE_EDIT", "index.php", $elements, "post", FALSE, TRUE);
  }// END FUNC edit

  function _save() {
    if(is_uploaded_file($_FILES["PHAT_FILE"]["tmp_name"])) {
      if(is_file(PHAT_FILE_DIR . $_FILES["PHAT_FILE"]["name"])) {
	$message = $_SESSION["translate"]->it("Filename already exists! Please rename the file and try again.");
	$error = new PHPWS_Error("phatfile", "PHAT_File::_save()", $message, "continue");
      } elseif(move_uploaded_file($_FILES["PHAT_FILE"]["tmp_name"],
				  PHAT_FILE_DIR . $_FILES["PHAT_FILE"]["name"])) {
	if($this->getLabel()) {
	  unlink(PHAT_FILE_DIR . $this->getLabel());
	}

	$this->setLabel($_FILES["PHAT_FILE"]["name"]);
	$this->_size = $_FILES["PHAT_FILE"]["size"];
	$this->_type = $_FILES["PHAT_FILE"]["type"];
      } else {
	$message = $_SESSION["translate"]->it("Problem copying file to the filing system!");
	$error = new PHPWS_Error("phatfile", "PHAT_File::_save()", $message, "continue");
      }
    } elseif(!$this->getLabel()) {
      $message = $_SESSION["translate"]->it("You must provide a file for uploading!");
      $error = new PHPWS_Error("phatfile", "PHAT_File::_save()", $message, "continue");
    }

    if(isset($error) && PHPWS_Error::isError($error)) {
      $error->message("CNT_phatfile");
      return $this->edit();
    } else {
      $this->_description = PHPWS_Text::parseInput($_REQUEST["PHAT_DESC"]);
      $this->commit();

      if(isset($_SESSION["OBJ_fatcat"])) {
	$link = PHAT_FILE_HTTP . $this->getLabel();
	$_SESSION["OBJ_fatcat"]->saveSelect($this->getLabel(), $link, $this->getId());
      }

      $tags["FILENAME"] = $this->getLabel();
      $tags["SIZE"] = $this->_size;
      $tags["TYPE"] = $this->_type;
      $tags["DESCRIPTION"] = PHPWS_Text::parseOutput($this->_description);
      $tags["MESSAGE"] = $_SESSION["translate"]->it("File saved successfully!");
      $tags["BACK_TO_MAIN"] = "<a href=\"index.php?module=phatfile&amp;FILE_MAN_OP=Main\">" .
	$_SESSION["translate"]->it("Return to Main") . "</a>";

      $content = $GLOBALS["core"]->processTemplate($tags, "phatfile", "save.tpl");
    }

    return $content;
  }// END FUNC _save

  function delete() {
    if(isset($_REQUEST["yes"])) {
      if(isset($_SESSION["OBJ_fatcat"])) {
	PHPWS_Fatcat::purge($this->getId(), "phatfile");
      }

      unlink(PHAT_FILE_DIR . $this->getLabel());
      $this->kill();

      $tags["MESSAGE"] = $_SESSION["translate"]->it("Your file was successfully deleted!");
      $tags["BACK_TO_MAIN"] = "<a href=\"index.php?module=phatfile&amp;FILE_MAN_OP=Main\">" .
	$_SESSION["translate"]->it("Back to Main") . "</a>";

      $content = $GLOBALS["core"]->processTemplate($tags, "phatfile", "deleted.tpl");
    } elseif(isset($_REQUEST["no"])) {
      $tags["MESSAGE"] = $_SESSION["translate"]->it("Your file was NOT deleted!");
      $tags["BACK_TO_MAIN"] = "<a href=\"index.php?module=phatfile&amp;FILE_MAN_OP=Main\">" .
	$_SESSION["translate"]->it("Back to Main") . "</a>";

      $content = $GLOBALS["core"]->processTemplate($tags, "phatfile", "not_deleted.tpl");
    } else {
      $tags["MESSAGE"] = $_SESSION["translate"]->it("Are you sure you wish to delete this file?");
      $tags["FILENAME"] = $this->link();
      $tags["DESCRIPTION"] = PHPWS_Text::parseOutput($this->_description);
      $tags["YES_BUTTON"] =  PHPWS_Form::formSubmit($_SESSION["translate"]->it("Yes"), "yes");
      $tags["NO_BUTTON"] =  PHPWS_Form::formSubmit($_SESSION["translate"]->it("No"), "no");

      $elements[] = PHPWS_Form::formHidden("module", "phatfile");
      $elements[] = PHPWS_Form::formHidden("FILE_MAN_OP", "Delete");
      $elements[] = PHPWS_Form::formHidden("PHPWS_MAN_ITEMS[]", $this->getId());
      $elements[] = $GLOBALS["core"]->processTemplate($tags, "phatfile", "confirm.tpl");

      $content = PHPWS_Form::makeForm("PHAT_FILE_DELETE", "index.php", $elements, "post", FALSE, TRUE);
    }

    return $content;
  }// END FUNC delete

  function download() {
    header("Location: " . PHAT_FILE_HTTP . $this->getLabel());
    exit();
  }

  function link() {
    return "<a href=\"" . PHAT_FILE_HTTP . $this->getLabel() ."\">" . $this->getLabel() . "</a>";
  }

  function action() {
    switch($_REQUEST["FILE_OP"]) {
      case "Save":
      $content = $this->_save();
      break;
    }

    $GLOBALS["CNT_phatfile"]["content"] .= $content;
  }// END FUNC action
}

?>