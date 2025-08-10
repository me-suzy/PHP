<?php
/**
 * This is a skeleton manager configuration file.  Edit it to be used
 * with your module.
 *
 * $Id: manager.php,v 1.2 2003/07/02 18:18:05 adam Exp $
 */

/* Column labels being translated */
$id = $_SESSION['translate']->it("ID");
$label = $_SESSION["translate"]->it("Label");
$created = $_SESSION["translate"]->it("Created");
$updated = $_SESSION["translate"]->it("Updated");
$owner = $_SESSION["translate"]->it("Owner");
$editor = $_SESSION["translate"]->it("Editor");
$hidden = $_SESSION["translate"]->it("Hidden");
$approved = $_SESSION["translate"]->it("Approved");

/* Actions being translated */
$edit = $_SESSION["translate"]->it("Edit");
$hide = $_SESSION["translate"]->it("Hide");
$show = $_SESSION["translate"]->it("Show");
$delete = $_SESSION["translate"]->it("Delete");

/* The lists you will be using */
$lists = array("skeleton"=>NULL);

/* The location of the templates to use for each list */
$templates = array("skeleton"=>"manager");

/* The tables to use for each list */
$tables = array("skeleton"=>"mod_skeleton_items");

/* The values to show in lists when an item is hidden or visable */
$hiddenValues = array("Visable", "Hidden");

/* The values to show in lists when an item is approved or unapproved */
$approvedValues = array("Unapproved", "Approved");

/* The columns to display for the skeleton list */
$skeletonColumns = array("id"=>$id,
			 "label"=>$label,
			 "created"=>$created,
			 "updated"=>$updated,
			 "owner"=>$owner,
			 "editor"=>$editor,
			 "hidden"=>$hidden,
			 "approved"=>$approved);

/* The actions that can be performed on items in the skeleton list */
$skeletonActions = array("edit"=>$edit,
			 "hide"=>$hide,
			 "show"=>$show,
			 "delete"=>$delete);

/* The permissions to check when showing actions to a user */
$skeletonPermissions = array("edit"=>"edit_skeletons",
			     "hide"=>"hideshow_skeletons",
			     "show"=>"hideshow_skeletons",
			     "delete"=>"delete_skeletons");

/* The paging information to use for the skeleton list */
$skeletonPaging = array("op"=>"SKEL_MAN_OP=main",
			"limit"=>10,
			"section"=>1,
			"limits"=>array(5,10,25,50),
			"back"=>"&#60;&#60;",
			"forward"=>"&#62;&#62;");

?>