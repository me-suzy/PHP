<?php

class PHPWS_Security {

  var $sec_codes;

  function PHPWS_Security(){
    $this->sec_codes = NULL;
  }

  function show_log(){

    $title = "Security Log";
    $sec_log = $GLOBALS["core"]->sqlSelect("mod_security_log",NULL,NULL,"timestamp");
    if ($sec_log){
      $content .= "<center>".$this->admin_link()."</center><br />
<table border=\"0\">
  <tr class=\"bg_medium\">
    <td><b>Time</b></td><td><b>Offense</b></td><td><b>Module</b></td></tr>";
      $highlight = NULL;
      foreach ($sec_log as $info){
       if($highlight)
	 $content .= "<tr class=\"bg_medium\">";
       else
	 $content .= "<tr class=\"bg_light\">"; 
	extract($info);
	$date = $GLOBALS["core"]->date($timestamp, 1);
	$format_date = $date["full"]." - ".$date["time"];
	$content .= "
    <td>$format_date</td><td>Code $offense: ".$this->sec_codes[$offense]."</td><td>$sec_mod_name</td></tr>";
	$GLOBALS["core"]->toggle($highlight);
      }
      $content .= "</table>";
      $_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_security");
    }
    else {
      $title = "Security Log";
      $content = "No Logs avalable";
      $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_security");
    } 
  }

  function admin_menu(){

    $array1[] = "Default";
    $all_mods = array_merge($array1, $GLOBALS["core"]->listModules());

    $title = $_SESSION["translate"]->it("Apache Settings");

    $content .= "
<form action=\"index.php\" method=\"post\">
".$GLOBALS["core"]->formHidden("module", "security")."
".$GLOBALS["core"]->formHidden("secure_op", "admin_ops")."
".$GLOBALS["core"]->formSubmit("Manage Logs", "sec_admop[view_log]").$_SESSION["OBJ_help"]->show_link("security", "manage_logs")."&nbsp;&nbsp;
".$GLOBALS["core"]->formSubmit("Manage Error Pages", "sec_admop[error_pages]").$_SESSION["OBJ_help"]->show_link("security", "manage_errorpage")."&nbsp;&nbsp;
".$GLOBALS["core"]->formSubmit("Manage Access", "sec_admop[manage_access_menu]").$_SESSION["OBJ_help"]->show_link("security", "manage_access")."<br />
</form>";
    $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_security");
  }

  function admin_link(){
    return "<table border=\"0\" width=\"100%\"><tr><td align=\"right\"><a href=\"index.php?module=security&amp;secure_op=admin_menu\">".$_SESSION["translate"]->it("Apache Settings")."</a></td></tr></table>";
  }

  function force_admin(){
    header("location:index.php?module=security&secure_op=admin_menu");
    exit();
  }

  function display_error_page($error) {
    $results = $GLOBALS["core"]->sqlSelect("mod_security_errorpage");
    if ($results){
      foreach($results as $errorpage){
	if($errorpage["error"] == $error) {
	  $title = $errorpage["label"];
	  $content = $errorpage["content"];
	  break;
	}
      }
    } 
    $_SESSION['OBJ_layout']->popbox($title, $content, NULL, "CNT_security");
  }

  function manage_error_page() {
    $results = $GLOBALS["core"]->sqlSelect("mod_security_errorpage");
    $content = $this->admin_link();
    if ($results){
    $content .= "<table border=\"0\"><tr class=\"bg_medium\"><td>".$_SESSION["translate"]->it("Error Number")."</td><td>".$_SESSION["translate"]->it("Label")."</td><td>&#160</td><td>&#160</td></tr>";
      $highlight = NULL;
      foreach($results as $errorpage){
	if($highlight)
	  $content .= "<tr class=\"bg_medium\">";
	else
	  $content .= "<tr class=\"bg_light\">";
	$content .= "<td>".$errorpage["error"]."</td><td>".$errorpage["label"]."</td><td>";
	$myelements[0] = $GLOBALS["core"]->formHidden("module", "security");
	$myelements[0] .= $GLOBALS["core"]->formHidden("secure_op", "admin_ops");
	$myelements[0] .= $GLOBALS["core"]->formHidden("sec_admop[edit_error_page]",$errorpage["error"]);
 	$myelements[0] .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Edit"), "bla");
	$content .= $GLOBALS["core"]->makeForm("security_error_page", "index.php", $myelements, "post", 0, 1);
	$content .= "</td><td>";
	$myelements[0] = $GLOBALS["core"]->formHidden("module", "security");
	$myelements[0] .= $GLOBALS["core"]->formHidden("secure_op", "admin_ops");
	$myelements[0] .= $GLOBALS["core"]->formHidden("sec_admop[delete_error_page]",$errorpage["error"]);
 	$myelements[0] .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Delete"), "bla");
	$content .= $GLOBALS["core"]->makeForm("security_error_page", "index.php", $myelements, "post", 0, 1);
	$content .= "</td></tr>";
	$GLOBALS["core"]->toggle($highlight);	
      }
      $content .= "</table><br />";
    }
    $title = $_SESSION["translate"]->it("Manage Error Pages");
    $myelements[0] = $GLOBALS["core"]->formHidden("module", "security");
    $myelements[0] .= $GLOBALS["core"]->formHidden("secure_op", "admin_ops");
    $myelements[0] .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Add"), "sec_admop[add_error_page]");
    $content .= $GLOBALS["core"]->makeForm("security_error_page", "index.php", $myelements, "post", 0, 1);
    $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_security"); 
  }

  function edit_error_page($error_number) {
    $results = $GLOBALS["core"]->sqlSelect("mod_security_errorpage","error", $error_number);
    if ($results){
      foreach($results as $errorpage)
	$myelements[0] = $GLOBALS["core"]->formHidden("module", "security")
	. $GLOBALS["core"]->formHidden("secure_op", "admin_ops")
	. $GLOBALS["core"]->formHidden("sec_admop[save_error_page]",$error_number)
	. "<table border=\"0\"><tr><td>".$_SESSION["translate"]->it("Error Lable")."</td><td>"
	. $GLOBALS["core"]->formTextField("label",$errorpage["label"])."</td></tr><tr><td>"
	. $_SESSION["translate"]->it("Content")."</td><td>".$GLOBALS["core"]->formTextArea("content",$errorpage["content"],6)
	. "</td></tr></table>".$GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Save"), "bla");
      $content = "<center>".$this->admin_link()."</center><br />"; 
      $content .= $GLOBALS["core"]->makeForm("security_error_page", "index.php", $myelements, "post", 0, 1);
      $title = "Edit Error Page";
      $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_security"); 
    }
  }

  function save_error_page($error_number) {
    $GLOBALS["core"]->sqlUpdate(array("label"=>$_REQUEST["label"], "content"=>$_REQUEST["content"]), "mod_security_errorpage", "error", $error_number);
    $this->manage_error_page();
  }

  function add_error_page() {

    $dropbox = array(""=>"",401=>"401",402=>"402",403=>"403",404=>"404",500=>"500",501=>"501");
    $myelements[0] = $GLOBALS["core"]->formHidden("module", "security")
      . $GLOBALS["core"]->formHidden("secure_op", "admin_ops")."<table border=\"0\"><tr><td>"
      . $_SESSION["translate"]->it("Error Number")."</td><td>".$GLOBALS["core"]->formSelect("error2", $dropbox)
      . "&nbsp;".$_SESSION["translate"]->it("Or")."&nbsp;".$GLOBALS["core"]->formTextField("error","",3)
      . "</td></tr><tr><td>".$_SESSION["translate"]->it("Error Label")."</td><td>".$GLOBALS["core"]->formTextField("label","")
      . "</td></tr><tr><td>".$_SESSION["translate"]->it("Content")."</td><td>".$GLOBALS["core"]->formTextArea("content","",6)
      . "</td></tr></table>".$GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Save"), "sec_admop[add_save_error_page]");
      $content = $this->admin_link(); 
      $content .= $GLOBALS["core"]->makeForm("security_error_page", "index.php", $myelements, "post", 0, 1);
      $title = $_SESSION["translate"]->it("Add Error Page");
      $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_security");   
  }

  function add_save_error_page() {
    if($_REQUEST["error"] || $_REQUEST["error2"]) {
      if(!$_REQUEST["error"])
	$error_page["error"] = $_REQUEST["error2"];
      else
	$error_page["error"] = $_REQUEST["error"];
      $sql = "select error from ".$GLOBALS["core"]->tbl_prefix."mod_security_errorpage where error=".$error_page["error"];    
      if(!$GLOBALS["core"]->quickFetch($sql)) {
	$error_page["label"] = $_REQUEST["label"];
	$error_page["content"] = $_REQUEST["content"];
	$GLOBALS["core"]->sqlInsert($error_page, "mod_security_errorpage");
	$this->make_htaccess();
      }
    }
    $this->manage_error_page();
  }

  function delete_error_page_conf() {
    $GLOBALS["core"]->sqlDelete("mod_security_errorpage","error", $_REQUEST["sec_admop"]["delete_error_page_conf"]);
    $this->make_htaccess();
    $this->manage_error_page();
  }

  function delete_error_page() {
 
    $title = $_SESSION["translate"]->it("Delete Error Page");
    $content = $_SESSION["translate"]->it("Are you sure you want to delete the")." ".$_REQUEST["sec_admop"]["delete_error_page"]." ".$_SESSION["translate"]->it("error page")."?"." <a href=\"".$source_http."index.php?module=security&amp;secure_op=admin_ops&amp;sec_admop[delete_error_page_conf]=".$_REQUEST["sec_admop"]["delete_error_page"]."\">".$_SESSION["translate"]->it("Yes")."</a> | <a href=\"".$source_http."index.php?module=security&amp;secure_op=admin_menu\">".$_SESSION["translate"]->it("No")."</a>";

    $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_security") . "<br />";
  }

  function manage_access_menu() {
    $title = $_SESSION["translate"]->it("Manage Access");
    $content = $this->admin_link(); 
    $content .= "<form action=\"index.php\" method=\"post\"><table border=\"0\" width=\"100%\" cellpading=\"7\"><tr><td>".$GLOBALS["core"]->formHidden("module", "security").$GLOBALS["core"]->formHidden("secure_op", "admin_ops").$GLOBALS["core"]->formRadio('access_set_add', '1',$row_reg["data"],NULL,$_SESSION["translate"]->it("Allow")).$GLOBALS["core"]->formRadio('access_set_add', '0',$row_reg["data"],NULL,$_SESSION["translate"]->it("Deny"))."<br />".$GLOBALS["core"]->formTextField("ipaddress",NULL,15,15)."<br />".$GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Add"), "sec_admop[add_this_ipban]").$_SESSION["OBJ_help"]->show_link("security", "ip_add_ex")."</td><td align=\"center\">";
    
    $sql = "select data from ".$GLOBALS["core"]->tbl_prefix."mod_security_settings where name='access_default'";    
    $row_reg = $GLOBALS["core"]->quickFetch($sql);

    $content .= $GLOBALS["core"]->formRadio('access_default', '1',$row_reg["data"],NULL,$_SESSION["translate"]->it("Allow")).$GLOBALS["core"]->formRadio('access_default', '0',$row_reg["data"],NULL,$_SESSION["translate"]->it("Deny"))."<br />".$_SESSION["translate"]->it("The public by default.")."<br />".$GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Save"), "sec_admop[set_access]")."</td><td>".$GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("htaccess extra"),"sec_admop[edit_htaccess_extra]")."</td></tr><tr><td colspan=\"3\">";

    if($row_reg["data"])
      $allow = "0";
    else
      $allow = "1";

    $results = $GLOBALS["core"]->sqlSelect("mod_security_ipinfo","allow",$allow,"timestamp");
    if ($results){
	 if($row_reg["data"])
	   $content .= "<center><h3>".$_SESSION["translate"]->it("Deny List");
	 else
	   $content .= "<center><h3>".$_SESSION["translate"]->it("Allow List");
      $content .= "</h3></center><table border=\"0\" width=\"100%\"><tr class=\"bg_medium\"><td>".$_SESSION["translate"]->it("IP Address")."</td><td>".$_SESSION["translate"]->it("Time")."</td><td>&#160</td></tr>";
    $highlight = NULL;
     foreach($results as $ipban){
       if($highlight)
	 $content .= "<tr class=\"bg_medium\">";
       else
	 $content .= "<tr class=\"bg_light\">";
       $date = $GLOBALS["core"]->date($ipban["timestamp"], 1);
       $content .= "<td>".$ipban["ipaddress"]."</td><td>".$date["full"]." - ".$date["time"]."</td><td>".$GLOBALS["core"]->formCheckBox("ban_allow_id[]", $ipban["ban_allow_id"])."</td></tr>";
       $GLOBALS["core"]->toggle($highlight);
     }
     $content .= "</table>";
    $content .= $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Delete"), "sec_admop[delete_this_ipban]");
    }
    $content .= "</td></tr></table></form>";
    $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_security") . "<br />";
  }

  function set_allow_deny($set) {
    $GLOBALS["core"]->sqlUpdate(array("data"=>$set), "mod_security_settings" , "name", "access_default");
    $this->make_htaccess();
    $this->manage_access_menu();
  }

  function delete_this_ipban() {
    foreach($_REQUEST["ban_allow_id"] as $key=>$value)
      $GLOBALS["core"]->sqlDelete("mod_security_ipinfo","ban_allow_id", $value);
    $this->make_htaccess();
    $this->manage_access_menu();
  }

  function add_this_ipban() {
    if($_REQUEST["ipaddress"]){
      $insert["ipaddress"] = $_REQUEST["ipaddress"];
      $insert["timestamp"] = NULL;
      if($_REQUEST["access_set_add"])
	$insert["allow"] = 1;
      else
	$insert["allow"] = 0;
      $GLOBALS["core"]->sqlInsert($insert,"mod_security_ipinfo");
      $this->make_htaccess();
      $this->manage_access_menu();
    } else
      $this->manage_access_menu();
 }

  function edit_htaccess_extra() {
    $sql = "select data from ".$GLOBALS["core"]->tbl_prefix."mod_security_settings where name='htaccess_extra'";    
    $row_reg = $GLOBALS["core"]->quickFetch($sql);
    $myelements[0] = $GLOBALS["core"]->formHidden("module", "security").$GLOBALS["core"]->formHidden("secure_op", "admin_ops").$_SESSION["translate"]->it(".htaccess extra info")."<br />".$GLOBALS["core"]->formTextArea("extrainfo",$row_reg["data"],6)."<br />".$GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Save"), "sec_admop[save_htaccess_extra]");
    $content = $this->admin_link(); 
    $content .= $GLOBALS["core"]->makeForm("security_htaccess_edit", "index.php", $myelements, "post", 0, 1);
    $title = $_SESSION["translate"]->it("Edit htacces info");
    $_SESSION["OBJ_layout"]->popbox($title, $content, NULL, "CNT_security"); 
  }

  function save_htaccess_extra() {
   $GLOBALS["core"]->sqlUpdate(array("data"=>$_REQUEST["extrainfo"]), "mod_security_settings" , "name", "htaccess_extra");
    $this->make_htaccess();
    $this->manage_access_menu();
  }

  function custom_htaccess_error_page() {
    $results = $GLOBALS["core"]->sqlSelect("mod_security_errorpage");
    if ($results){
      foreach($results as $errorpage){
	$error_page .= $_SESSION["translate"]->it("ErrorDocument")." ".$errorpage["error"]." http://".$GLOBALS["core"]->source_http."index.php?module=security&page=".$errorpage["error"]."\n";
      }
    }
    return $error_page;
  }

  function ban_ip() {
    $sql = "select data from ".$GLOBALS["core"]->tbl_prefix."mod_security_settings where name='access_default'";    
    $row_reg = $GLOBALS["core"]->quickFetch($sql);

    if($row_reg["data"])
      $allow = "0";
    else
      $allow = "1";

    $results = $GLOBALS["core"]->sqlSelect("mod_security_ipinfo",allow,$allow);
    if ($results){
      $write_data .= "<Limit GET PUT POST>\n";
      if($row_reg["data"]) {
	$write_data .= "order allow,deny\nAllow from all\n";
	$access = "deny";
      }
      else {
	$write_data .= "order deny,allow\nDeny from all\n";
	$access = "allow";
      }
      foreach($results as $baninfo){
	$write_data .= "$access from ".$baninfo["ipaddress"]."\n";
      }
      $write_data .= "</Limit>";
    }
    return $write_data;
  }

  function make_htaccess($path = NULL) {
    $sql = "select data from ".$GLOBALS["core"]->tbl_prefix."mod_security_settings where name='htaccess_extra'";    
    $row_reg = $GLOBALS["core"]->quickFetch($sql);
    $write_data = $row_reg["data"]."\n\n";
    $write_data .= $this->custom_htaccess_error_page();
    $write_data .= $this->ban_ip();
    if($path)
      $this->write_htaccess($path, $write_data);
    else
      $this->write_htaccess($GLOBALS["core"]->source_dir, $write_data);
  }

  function write_htaccess($save_path, $write_data) {
    $fp = @fopen($save_path.".htaccess", "w");
    @fputs($fp, $write_data);
    @fclose($fp);
  }

}

?>
