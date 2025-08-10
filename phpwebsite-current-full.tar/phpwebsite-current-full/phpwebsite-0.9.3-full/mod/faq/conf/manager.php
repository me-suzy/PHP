<?php

$id        = $_SESSION["translate"]->it("ID");
$label     = $_SESSION["translate"]->it("Question");
$hits      = $_SESSION["translate"]->it("Hits");
$avgScore  = $_SESSION["translate"]->it("Average Score");
$numScored = $_SESSION["translate"]->it("Users Rated");
$compScore = $_SESSION["translate"]->it("Composite Score");
$rating    = $_SESSION["translate"]->it("Rating");
$approved  = $_SESSION["translate"]->it("Approved");
$updated   = $_SESSION["translate"]->it("Updated");
$hidden    = $_SESSION["translate"]->it("Hidden");
$delete    = $_SESSION["translate"]->it("Delete");

$lists = array("unapproved"=>"approved='0' OR hidden='1'",
	       "specialqueries"=>NULL,
	       "time_query"=>NULL);

$templates = array("unapproved"=>"manager/admin",
		   "specialqueries"=>"manager/stats/special_queries",
		   "time_query"=>"manager/stats/time_query");

$unapprovedColumns = array("id"=>$id,
			   "label"=>$label,
			   "approved"=>$approved,
                           "hidden"=>$hidden);

$unapprovedActions = array("view"=>"View",
			   "edit"=>"Edit",
			   "hide"=>"Hide",
			   "show"=>"Show",
                           "approve"=>"Approve",
			   "delete"=>"Delete");

$unapprovedPermissions = array("view"=>NULL,
			       "edit"=>"edit_faqs",
			       "hide"=>"hide_faqs",
			       "show"=>"show_faqs",
			       "approve"=>"approve_faqs",
			       "delete"=>"delete_faqs");

$unapprovedPaging = array("op"=>"FAQ_MAN_OP=list",
		          "limit"=>5,
		          "section"=>1,
		          "limits"=>array(5,10,25,50),
		          "back"=>"&#60;&#60;",
		          "forward"=>"&#62;&#62;");

$specialqueriesColumns = array("id"=>$id,
			       "label"=>$label,
			       "approved"=>$approved,
                               "hidden"=>$hidden,
			       "hits"=>$hits,
			       "avgScore"=>$avgScore,
			       "compScore"=>$compScore);

$specialqueriesActions = array("view"=>"View",
			       "edit"=>"Edit",
			       "hide"=>"Hide",
			       "show"=>"Show",
                               "approve"=>"Approve",
			       "delete"=>"Delete");

$specialqueriesPermissions = array("view"=>NULL,
			           "edit"=>"edit_faqs",
			           "hide"=>"hide_faqs",
			           "show"=>"show_faqs",
			           "approve"=>"approve_faqs",
			           "delete"=>"delete_faqs");

$specialqueriesPaging = array("op"=>"FAQ_STATS_MAN_OP=list",
		              "limit"=>5,
		              "section"=>1,
		              "limits"=>array(5,10,25,50),
		              "back"=>"&#60;&#60;",
		              "forward"=>"&#62;&#62;");

$time_queryColumns = array("id"        => $id,
			   "label"     => $label,
			   "updated"    => $updated,
			   "approved"  => $approved,
                           "hidden"    => $hidden,
			   "hits"      => $hits,
			   "avgScore"  => $avgScore,
			   "compScore" => $compScore);

$time_queryActions = array("view"    => "View",
			   "edit"    => "Edit",
			   "hide"    => "Hide",
			   "show"    => "Show",
                           "approve" => "Approve",
			   "delete"  => "Delete");

$time_queryPermissions = array("view"    => NULL,
			       "edit"    => "edit_faqs",
			       "hide"    => "hide_faqs",
		               "show"    => "show_faqs",
		               "approve" => "approve_faqs",
		               "delete"  => "delete_faqs");

$time_queryPaging = array("op"      => "FAQ_STATS_MAN_OP=list",
		          "limit"   => 5,
		          "section" => 1,
		          "limits"  => array(5,10,25,50),
	                  "back"    => "&#60;&#60;",
	                  "forward" => "&#62;&#62;");
?>