<?php

/**
 * @version $Id: manager.php,v 1.2 2003/03/28 18:00:09 steven Exp $
 * @author  Steven Levin <steven@NOSPAM.tux.appstate.edu>
 */

/* Labels */
$label = $_SESSION['translate']->it("Name");
$editor = $_SESSION['translate']->it("Editor");
$updated = $_SESSION['translate']->it("Updated");
$desc = $_SESSION['translate']->it("Description");

$lists = array("albums"=>"approved='1'");

$templates = array("albums"=>"albums");

$albumsColumns = array("label"=>$label,
		       "blurb1"=>$desc,
		       "updated"=>$updated,
		       "id"=>NULL,
		       "blurb0"=>NULL,
		       "image"=>NULL);

$albumsActions = array();

$albumsPermissions = array();

$albumsPaging = array("op"=>"PHPWS_AlbumManager_op=list",
		      "limit"=>10,
		      "section"=>1,
		      "limits"=>array(5,10,20,50),
		      "back"=>"&#60;&#60;",
		      "forward"=>"&#62;&#62;");

?>