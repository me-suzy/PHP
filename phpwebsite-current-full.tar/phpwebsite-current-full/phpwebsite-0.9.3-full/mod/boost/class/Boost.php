<?php

class PHPWS_Boost{
  function modFileExists($moduleDir){
    $modFile = $GLOBALS["core"]->source_dir."mod/" . $moduleDir . "/conf/boost.php";

    if (file_exists($modFile))
      return $modFile;
    else
      return NULL;
  }

  function checkForBoostUpdate(){
    $info = $this->getVersionInfo("boost");
    include($GLOBALS["core"]->source_dir . "mod/boost/conf/boost.php");

    if ($version > $info["version"])
      return TRUE;
    else
      return FALSE;
  }


  function adminMenu(){
    $coreMods = array ("users", "approval", "help", "language", "layout", "search", "security", "fatcat", "controlpanel");
    $current_mods = $GLOBALS["core"]->listModules();
    $core_count = $non_count = 0;

    $template['CORE_VERSION_TEXT'] = $_SESSION['translate']->it("Core Version");
    $template['CORE_VERSION'] = $GLOBALS['core']->version;
    $template['ROWS'] = NULL;

    $content = 
      "<form action=\"index.php\" method=\"post\">"
      . $GLOBALS["core"]->formHidden(array("module"=>"boost", "boost_op"=>"update"));


    if ($this->checkForBoostUpdate()){
      $template["UPDATE_WARNING"] = $_SESSION["translate"]->it("You must update Boost before updating any other modules.");
      $template["UPDATE_BOOST"] = $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Update"), "bst_update[boost]");
    } else {
      if (!($dir = $GLOBALS["core"]->readDirectory($GLOBALS["core"]->source_dir."mod/", 1)))
	exit("Error in Boost.php - adminMenu was unable to locate your modules directory.");

      foreach ($dir as $moduleDir){
	$branch_allow = NULL;
	$version = NULL;
	$uninstall_allow = NULL;
	$mod_directory = $mod_filename = NULL;

	if ($moduleDir == "boost")
	  continue;

	$branch_block = 0;
	if ($modFile = $this->modFileExists($moduleDir)){
	  $rowTemplate = NULL;

	  include($modFile);

	  if (!isset($mod_directory))
	    $mod_directory = $mod_title;

	  if (!isset($mod_filename))
	    $mod_filename = "index.php";


	  if (isset($branch_allow) && $branch_allow === 0 && !$GLOBALS["core"]->isHub)
	    continue;

	  $rowTemplate["MOD_NAME"] = $mod_pname;
	  if (!$GLOBALS["core"]->moduleExists($mod_title)){
	    $rowTemplate["COMMAND"] = $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Install"), "bst_install[$mod_directory]");
	    $rowTemplate["VERSION"] = $version;
	  }
	  else {
	    $moduleInfo = $this->getVersionInfo($mod_title);
	    $rowTemplate["VERSION"] = $moduleInfo["version"];
	    if ($version && $version > $moduleInfo["version"]){
	      $rowTemplate["COMMAND"] = $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Update"), "bst_update[$mod_title]");
	      $allUpdates[] = $mod_title;
	    }
	    else
	      $rowTemplate["COMMAND"] = "<i>" .$_SESSION["translate"]->it("Up to Date") . "</i>";
	  
	    if(!isset($uninstall_allow) || $uninstall_allow == 1)
	      $rowTemplate["UNINSTALL"] = $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Uninstall"), "bst_uninstall[$mod_title]");
	  }

	  if (in_array($mod_title, $coreMods)){
	    $core_count++;
	    if ($core_count%2)
	      $rowTemplate["TOG1"] = " ";
	    else
	      $rowTemplate["TOG2"] = " ";

	    $coreRows[] = $GLOBALS["core"]->processTemplate($rowTemplate, "boost", "coreRows.tpl");
	  }
	  else {
	    $non_count++;
	    if ($non_count%2)
	      $rowTemplate["TOG1"] = " ";
	    else
	      $rowTemplate["TOG2"] = " ";

	    $noncoreRows[] = $GLOBALS["core"]->processTemplate($rowTemplate, "boost", "moduleRows.tpl");
	  }
	}
      }

      $template["CORE_ROWS"] = implode("", $coreRows);
      $template["NONCORE_ROWS"] = implode("", $noncoreRows);

      $template["CORE_MODS"] = $_SESSION["translate"]->it("Core Modules");
      $template["NONCORE_MODS"] = $_SESSION["translate"]->it("Other Modules");
      $template["MOD_NAME"] = $_SESSION["translate"]->it("Module Name");
      $template["VERSION"] = $_SESSION["translate"]->it("Version");
      $template["COMMAND"]  = $_SESSION["translate"]->it("Install") . " / " . $_SESSION["translate"]->it("Update");
      $template["UNINSTALL"] = $_SESSION["translate"]->it("Uninstall");

      if (isset($allUpdates) && is_array($allUpdates))
	$template["UPDATE_ALL"] = $GLOBALS["core"]->formSubmit($_SESSION["translate"]->it("Update All Modules"), "bst_updateAll");
    }

    $content .= $GLOBALS["core"]->processTemplate($template, "boost", "adminMenu.tpl");
    if (isset($allUpdates) && is_array($allUpdates))
      $content .= $GLOBALS["core"]->formHidden("allUpdates", implode(":", $allUpdates ));
    $content .= "</form>";
    $GLOBALS["core"]->refreshTemplate("boost");
    return $content;
  }

  function checkUpdate($mod_title){
    $GLOBALS["CNT_boost"]["content"] .= $this->boostLink()."<br /><br />";

    if ($mod_title == "core")
      include($GLOBALS["core"]->source_dir . "conf/core_info.php");
    else {
      if (!($info = $GLOBALS["core"]->getModuleInfo($mod_title))){
	$GLOBALS["CNT_boost"]["content"] .= $_SESSION["translate"]->it("Unable to get information on [var1]", $mod_title);
	return;
      }
      extract($info);
    }
    extract($this->getVersionInfo($mod_title));

    $GLOBALS["CNT_boost"]["title"]   .= $_SESSION["translate"]->it("Check Update for ". $mod_pname);
    $file = $GLOBALS["core"]->checkLink($update_link) . "update.txt";

    if (!($versionFile = @file($file))){
      $GLOBALS["CNT_boost"]["content"] .= $_SESSION["translate"]->it("Unable to read update.txt file from [var1]", $GLOBALS["core"]->checkLink($update_link));
      return;
    }

    foreach ($versionFile as $data){
      $process = explode("::", $data);

      if ($process[0] != "download")
	$upgradeData[$process[0]] = $process[1];
      else
	$upgradeData["download"][] = $process[1];
    }

    if (!isset($upgradeData["version"])){
     $GLOBALS["CNT_boost"]["content"] .= $_SESSION["translate"]->it("The upgrade file has a syntax error").".";
     return;
    }

    $content .= "<b>" . $_SESSION["translate"]->it("Your Version") . ":</b> " . $version . "<br />";
    $content .= "<b>" . $_SESSION["translate"]->it("Current Version") . ":</b> " . $upgradeData["version"] . "<br /><br />";


    if ($upgradeData["version"] > $version){
      $content .= "<b>" . $_SESSION["translate"]->it("There is an upgrade available for this module")."!</b><br />";
      if ($upgradeData["download"])
	$content .= "<b>" . $_SESSION["translate"]->it("Click on a download link to get the most recent version")."!</b><br /><br />";
    }
    else
      $content .= "<b>" . $_SESSION["translate"]->it("This module's version appears to be current").".</b><br /><br />";

    if ($infoLink = $upgradeData["information"])
      $content .= "<a href=\"". $GLOBALS["core"]->checkLink($infoLink)."\" target=\"_blank\">" .  $_SESSION["translate"]->it("Module Information") . "</a><br />";
    else
      $content .= $_SESSION["translate"]->it("Module Information link not provided").".<br />";

    if ($download = $upgradeData["download"]){
      $content .= "<hr /><b>" .$_SESSION["translate"]->it("Download the latest version from the following link(s)") . ":</b><br />";
      foreach ($download as $dlLinks)
	$content .= "<a href=\"". $GLOBALS["core"]->checkLink($dlLinks)."\">" . $GLOBALS["core"]->checkLink($dlLinks) . "</a><br />";
    } else {
      $content .= $_SESSION["translate"]->it("Download links were not provided"). "<br />";
      if ($infoLink)
	$content .= $_SESSION["translate"]->it("Please go to the Module Information link for more information"). "<br />";
    }

    $GLOBALS["CNT_boost"]["content"] .= $content;
  }


  function direct(){
    extract($_POST);
    $op_array["boost_op"] = "adminMenu";

    if (isset($bst_check)){
      list($updateMod) = each($bst_check);
      $this->checkUpdate($updateMod);
    }
    elseif (isset($bst_install)) {
      list($mod_dir) = each($bst_install);
      $modFile = $this->modFileExists($mod_dir);
      include($modFile);
      $GLOBALS["CNT_boost"]["title"] = $_SESSION["translate"]->it("Install Module") . " $mod_pname";
      $GLOBALS["CNT_boost"]["content"] .= $GLOBALS["core"]->modulelink("Go Back", "boost", $op_array)."<br /><br />";
      $GLOBALS["CNT_boost"]["content"] .= $this->installModule($mod_dir, TRUE, TRUE, TRUE, TRUE);
    } elseif (isset($bst_uninstall)){
      list($mod_title) = each($bst_uninstall);
      $mod_dir = $GLOBALS["core"]->getModuleDir($mod_title);
      $modFile = $this->modFileExists($mod_dir);
      include($modFile);

      $GLOBALS["CNT_boost"]["title"] = $_SESSION["translate"]->it("Uninstall Module") . " $mod_pname";
      $GLOBALS["CNT_boost"]["content"] .= $GLOBALS["core"]->modulelink("Go Back", "boost", $op_array)."<br /><br />";

      $GLOBALS["CNT_boost"]["content"] .= $_SESSION["translate"]->it("Are you sure you want to uninstall this module") . "?<br /><br />";
      $GLOBALS["CNT_boost"]["content"] .= $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("Yes"), "boost", array("boost_op"=>"uninstallModule", "killMod"=>$mod_title)) . " ";
      $GLOBALS["CNT_boost"]["content"] .= $GLOBALS["core"]->moduleLink($_SESSION["translate"]->it("No"), "boost", array("boost_op"=>"adminMenu"));

    } elseif (isset($bst_update)){
      list($mod_title) = each($bst_update);
      $GLOBALS["CNT_boost"]["content"] .= $this->boostLink()."<br /><br />";
      $this->updateModule($mod_title, TRUE, TRUE);
    } elseif ($bst_updateAll){
      $this->updateAll(TRUE);
    }
  }


  function updateAll($branchUpdate=FALSE){
    if (!$_POST["allUpdates"])
      $this->adminMenu();

    $updateList = explode(":", $_POST["allUpdates"]);

    foreach ($updateList as $mod_title){
      $this->updateModule($mod_title, $branchUpdate);
      $GLOBALS["CNT_boost"]["content"] .= "<hr />\n";

    }
  }

  function updateModule($mod_title, $branchUpdate=NULL, $directLink=FALSE){
    require_once(PHPWS_SOURCE_DIR . "mod/controlpanel/class/ControlPanel.php");
    if ($GLOBALS['core']->moduleExists("branch"))
      require_once(PHPWS_SOURCE_DIR . "mod/branch/class/Branch.php");
    if (!isset($GLOBALS["CNT_boost"]["content"]))
      $GLOBALS["CNT_boost"]["content"] = NULL;
    $content = NULL;
    if ($mod_title == "core")
      $modFile = $GLOBALS["core"]->source_dir . "conf/core_info.php";
    else {
      if (!($module = $GLOBALS["core"]->getModuleInfo($mod_title))){
	$GLOBALS["CNT_boost"]["content"] .= $_SESSION["translate"]->it("Unable to get module information on [var1]", $mod_title);
	return;
      }
      extract($module);
      $modFile = $GLOBALS["core"]->source_dir . "mod/$mod_directory/conf/boost.php";

    }

    if (file_exists($modFile))
      include($modFile);
    else
      exit("Error: Missing module information file for $mod_pname.");

    if (!isset($mod_directory))
      $mod_directory = $mod_title;

    if (!isset($mod_filename))
      $mod_filename = "index.php";

    $versionInfo = PHPWS_Boost::getVersionInfo($mod_title);

    if (!$versionInfo)
      return phpws_boost::installModule($mod_directory, TRUE, TRUE, TRUE, TRUE);

    extract ($versionInfo);

    $GLOBALS["CNT_boost"]["title"] = $_SESSION["translate"]->it("Update Module");
    $GLOBALS["CNT_boost"]["content"] .= "<b>" . $_SESSION["translate"]->it("Updating") . " $mod_pname</b><br />";

    if ($mod_title == "core")
      $updateFile = $GLOBALS["core"]->source_dir . "boost/update.php";
    else {
      $updateFile = $GLOBALS["core"]->source_dir . "mod/" . $mod_directory . "/boost/update.php";
    }

    $currentVersion = $version;

    if (!file_exists($updateFile)){
      $GLOBALS["CNT_boost"]["content"] .= $_SESSION["translate"]->it("Unable to locate update file for [var1]", $mod_title) . ".<br />";
      $GLOBALS["CNT_boost"]["content"] .= $_SESSION["translate"]->it("Assuming update needed registering only") . ".";
      $status = 1;
    } else 
      include($updateFile);

    if (!$status){
      $GLOBALS["CNT_boost"]["content"] .= "<span class=\"errortext\"><b>" . $_SESSION["translate"]->it("Some errors occurred while trying to update [var1]", $mod_pname).".</b></span>";
      return FALSE;
    } else {
      $GLOBALS["CNT_boost"]["content"] .= $content . "<br />";
      $GLOBALS["CNT_boost"]["content"] .= "<b>" . $_SESSION["translate"]->it("[var1] updated successfully", $mod_pname)."!</b><br />";

      if ($directLink) {
	$linkFile = PHPWS_SOURCE_DIR . "mod/$mod_directory/conf/controlpanel.php";
	if (is_file($linkFile)){
	  include ($linkFile);
	  if (!isset($link))
	    break;
	  
	  foreach ($link as $modLink){
	    PHPWS_ControlPanel::drop($mod_title);
	    PHPWS_ControlPanel::import($mod_title);
	    if (isset($modLink['admin']) && (bool)$modLink['admin'] == TRUE){
	      $url = $modLink['url'];
	    }
	  }
	  if(isset($url)) {
	    $GLOBALS["CNT_boost"]["content"] .= "<br /><a href=\"" . $url . "\">Go to Module</a>";
	  }
	}

      }

      $GLOBALS["core"]->setModuleInfo($modFile, "update");
      PHPWS_Boost::setVersionInfo($modFile, "update");

      if (class_exists('PHPWS_Branch') && $branchUpdate){
	PHPWS_Branch::updateBranches($mod_title);
      }

      return TRUE;
    }


  }

  function boostLink(){
    return $GLOBALS["core"]->moduleLink("Back", "boost", array("boost_op"=>"adminMenu"));
  }


  function installModule($moduleDir, $regLayout=FALSE, $regLang=FALSE, $regMenu=FALSE, $directLink=FALSE){
    require_once(PHPWS_SOURCE_DIR . "mod/controlpanel/class/ControlPanel.php");
    $dependList = $content = NULL;
    if (get_class($GLOBALS["core"]) != "phpws_core")
      exit("Error: Boost.php - installModules : Invalid DB connect object received");

    $installFile = $GLOBALS["core"]->source_dir . "mod/" . $moduleDir . "/boost/install.php";

    if (!($modFile = PHPWS_Boost::modFileExists($moduleDir)))
      return $_SESSION["translate"]->it("Module Information file missing in") . " $moduleDir<br />";

    include($modFile);

    if (!isset($mod_directory))
      $mod_directory = $mod_title;

    if (!isset($mod_filename))
      $mod_filename = "index.php";


    if ($GLOBALS["core"]->moduleExists($modFile))
      return $_SESSION["translate"]->it("Module is already registered") . ".<br />";


    if (isset($depend) && is_array($depend)){
      foreach ($depend as $dependMod){
	if (!$GLOBALS["core"]->moduleExists($dependMod)){
	  $dependList .= "<li>$dependMod</li>";
	  $dependError = 1;
	}
      }
    }

    if (isset($dependError))
      return $_SESSION["translate"]->it("This module cannot install until the following modules are installed") . ":<ul>$dependList</ul>";

    if (!file_exists($installFile)){
      $content .= "<b>".$_SESSION["translate"]->it("Installation file missing for [var1]", $mod_pname) . ".</b> ";
      $content .= "<b>".$_SESSION["translate"]->it("Assuming it is not needed") . ".</b><br /><br />";
      $status = 1;
    }
    else
      include($installFile);

    if ($status){
      if ($regLang){
	if ($langContent = PHPWS_Language::installLanguages($moduleDir))
	  $content .= $langContent . "<br />";
	$langContent = NULL;
      }

      $content .= "<b>***** " . $_SESSION["translate"]->it("[var1] installation successful", $mod_pname) . "! *****</b><br /><br />";
      $GLOBALS["core"]->setModuleInfo($modFile, "insert");

      if($regMenu == TRUE)
	PHPWS_ControlPanel::import($mod_title);

      if ($regLayout)
	$_SESSION["OBJ_layout"]->installModule($mod_title);

      PHPWS_Boost::setVersionInfo($modFile);
      if ($directLink) {
	$linkFile = PHPWS_SOURCE_DIR . "mod/$mod_directory/conf/controlpanel.php";
	if (is_file($linkFile)){
	  include ($linkFile);
	  if (!isset($link))
	    break;
	  
	  foreach ($link as $modLink){
	    if (isset($modLink['admin']) && (bool)$modLink['admin'] == TRUE){
	      $url = $modLink['url'];
	      break;
	    }
	  }
	  if(isset($url)) {
	    $content .= "<a href=\"" . $url . "\">Go to Module</a>";
	  }
	}
      }
    }
    else
      $content .= "<font color=\"red\"><b>" . $_SESSION["translate"]->it("[var1] installation NOT successful", $mod_pname) . "!</b></font><br />";

    return $content;
   
  }

  function setVersionInfo($modFile, $process="insert"){
    if (file_exists($modFile))
      include($modFile);
    else
      return;

    if ($process == "remove")
      return $GLOBALS["core"]->sqlDelete("mod_boost_version", "mod_title", $mod_title);
    
    $sql["mod_title"] = $mod_title;
    $sql["version"] = $version;

    //    $sql["update_link"] = $update_link;
    if (isset($branch_allow))
      $sql["branch_allow"] = $branch_allow;
    else
      $sql["branch_allow"] = 1;

    $GLOBALS["core"]->dropNulls($sql);

    if ($process == "insert")
      return $GLOBALS["core"]->sqlInsert($sql, "mod_boost_version", 1);
    elseif ($process == "update")
      return $GLOBALS["core"]->sqlUpdate($sql, "mod_boost_version", "mod_title", $mod_title);
    else 
      return $sql;

  }


  function uninstallModule($moduleDir, $regLayout=FALSE, $regLang=FALSE, $regMenu=TRUE){
    require_once(PHPWS_SOURCE_DIR . "mod/controlpanel/class/ControlPanel.php");
    $content = NULL;
    if (get_class($GLOBALS["core"]) != "phpws_core")
      exit("Error: Boost.php - installModules : Invalid DB connect object received");

    $uninstallFile = $GLOBALS["core"]->source_dir . "mod/" . $moduleDir . "/boost/uninstall.php";

    if (!($modFile = PHPWS_Boost::modFileExists($moduleDir))){
      $content .= $_SESSION["translate"]->it("Module Information file missing in") . " $moduleDir<br />";
      return $content;
    }

    include($modFile);

    if (!file_exists($uninstallFile)){
      $content .= "<b>".$_SESSION["translate"]->it("Uninstallation file missing for [var1]", $mod_pname) . ".</b> ";
      $content .= "<b>".$_SESSION["translate"]->it("Assuming it is not needed") . ".</b> <br /><br />";
      $status = 1;
    }
    else
      include($uninstallFile);

    if ($status){
      if ($regLayout)
	$content .= PHPWS_Layout::uninstallBoxStyle($moduleDir) . "<br />";

      if ($regLang)
	$content .= PHPWS_Language::uninstallLanguages($moduleDir) . "<br />";

      if ($regMenu)
	PHPWS_ControlPanel::drop($mod_title);

      PHPWS_Approval::remove(NULL, $mod_title);
      PHPWS_Boost::setVersionInfo($modFile, "remove");
      PHPWS_Fatcat::purge(NULL, $mod_title);

      $content .= "<b>" . $_SESSION["translate"]->it("[var1] uninstallation successful", $mod_pname) . "!</b><br />";
      $GLOBALS["core"]->setModuleInfo($modFile, "remove");
    }
    else
      $content .= "<font color=\"red\"><b>" . $_SESSION["translate"]->it("[var1] uninstallation NOT successful", $mod_pname) . "!</b></font><br />";

    return $content;
   
  }


  function postInstall($defaultMods){
    $content = NULL;

    if (!is_array($defaultMods))
      exit("Error in Boost.php - postInstall requires an array of default modules.");

    foreach ($defaultMods as $moduleDir){
      $postDir = $GLOBALS["core"]->source_dir . "mod/" . $moduleDir . "/boost/postinstall.php";

      if (!($modFile = PHPWS_Boost::modFileExists($moduleDir))){
	$content .= $_SESSION["translate"]->it("Module Information file missing in") . " $moduleDir<br />";
	return;
      }

      include($modFile);

      if (file_exists($postDir)){
	include($postDir);

	if ($status){
	  $content .= "<b>" . $_SESSION["translate"]->it("[var1] post-installation successful", $mod_pname) . "!</b><br /><br />";
	  $GLOBALS["core"]->setModuleInfo($modFile, "insert");

	}
	else
	  $content .= "<font color=\"red\"><b>" . $_SESSION["translate"]->it("[var1] post-installation NOT successful", $mod_pname) . "!</b></font><br />";
      }
      PHPWS_ControlPanel::import($mod_title);
    }
    
    return $content;
  }

  function installModuleList($defaultMods, $regLayout=FALSE, $regLang=FALSE, $regMenu=FALSE){
    $content = NULL;
    if (!is_array($defaultMods))
      exit("Error in Boost.php - installModuleList requires an array of default modules.");

    if (get_class($GLOBALS["core"]) != "phpws_core")
      exit("Error: Boost.php - installModuleList : Invalid DB connect object received");

    foreach ($defaultMods as $mod_dir){
      $content .= PHPWS_Boost::installModule($mod_dir, $regLayout, $regLang, $regMenu);
    }

    return $content;
  }

  function getVersionInfo($mod_title){
    if (!($row = $GLOBALS["core"]->sqlSelect("mod_boost_version", "mod_title", $mod_title)))
      return FALSE;

    return $row[0];
  }


  function needsUpdate($mod_title){
    $modDirectory = $GLOBALS['core']->getModuleDir($mod_title);

    if ($modDirectory && is_file(PHPWS_SOURCE_DIR . "mod/$modDirectory/conf/boost.php"))
      include (PHPWS_SOURCE_DIR . "mod/$modDirectory/conf/boost.php");
    if (!isset($version))
      return;

    $versionInfo = phpws_boost::getVersionInfo($mod_title);

    if ($versionInfo){
      if ($version > $versionInfo['version'])
	return TRUE;
      else
	return FALSE;
    } else
      return TRUE;
  }

}

?>