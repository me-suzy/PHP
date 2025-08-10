<?php

$id = $_SESSION['translate']->it("ID");
$label = $_SESSION['translate']->it("Name");
$owner = $_SESSION['translate']->it("Owner");
$editor = $_SESSION['translate']->it("Editor");
$created = $_SESSION['translate']->it("Created");
$view = $_SESSION['translate']->it("View");
$edit = $_SESSION['translate']->it("Edit");
$delete = $_SESSION['translate']->it("Delete");
$show = $_SESSION['translate']->it("Show");
$body = $_SESSION['translate']->it("Description");
$restrict = $_SESSION['translate']->it("Restricted");
$active = $_SESSION['translate']->it("Active");
$comments = $_SESSION['translate']->it("Allow Comments");

$lists = array("polls"=>"id=id");

$templates = array("polls"=>"manager");

$pollsColumns = array("id"=>$id,
				"active"=>$active,
				"restricted"=>$restrict,
				"allowComments"=>$comments,
				"label"=>$label,
				"body"=>$body,
				"created"=>$created);

$pollsActions = array("edit"=>$edit,
				"delete"=>$delete);

$pollsPermissions = array("edit"=>"edit",
				"delete"=>"delete");

$pollsPaging = array("op"=>"PHPWS_MAN_OP=list",
             "limit"=>10,
             "section"=>1,
             "limits"=>array(5,10,25,50),
             "back"=>"&#60;&#60;",
             "forward"=>"&#62;&#62;");
?>
