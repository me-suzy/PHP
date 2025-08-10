<?php
/*
 * @version $Id: manager.php,v 1.2 2003/03/24 19:32:08 adam Exp $
 */

$id = $_SESSION['translate']->it("ID");
$filename = $_SESSION['translate']->it("Filename");
$size = $_SESSION["translate"]->it("Size");
$type = $_SESSION["translate"]->it("Type");
$modified = $_SESSION["translate"]->it("Modified");
$owner = $_SESSION["translate"]->it("Owner");
$hidden = $_SESSION["translate"]->it("Hidden");
$description = $_SESSION["translate"]->it("Description");
$download = $_SESSION["translate"]->it("Download");
$edit = $_SESSION["translate"]->it("Edit");
$hide = $_SESSION["translate"]->it("Hide");
$show = $_SESSION["translate"]->it("Show");
$delete = $_SESSION["translate"]->it("Delete");

$lists = array("files"=>"approved='1'");
$templates = array("files"=>"manager");

$filesColumns = array("id"=>$id,
		      "label"=>$filename,
		      "size"=>$size,
		      "type"=>$type,
		      "updated"=>$modified,
		      "owner"=>$owner,
		      "hidden"=>$hidden,
		      "description"=>$description);

$filesActions = array("Download"=>$download,
		      "Edit"=>$edit,
		      "hide"=>$hide,
		      "show"=>$show,
		      "Delete"=>$delete);

$filesPermissions = array("Download"=>NULL,
			  "Edit"=>"edit_files",
			  "Hide"=>"hideshow_files",
			  "Show"=>"hideshow_files",
			  "Delete"=>"delete_files");

$filesPaging = array("op"=>"FILE_MAN_OP=Main",
		     "limit"=>10,
		     "section"=>1,
		     "limits"=>array(5,10,25,50),
		     "back"=>"&#60;&#60;",
		     "forward"=>"&#62;&#62;");

?>