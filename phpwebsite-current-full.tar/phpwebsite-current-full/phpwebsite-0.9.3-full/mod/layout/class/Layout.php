<?php

require_once($GLOBALS["core"]->source_dir . "mod/layout/class/Box.php");
require_once($GLOBALS["core"]->source_dir . "mod/layout/class/Forms.php");

/**
 * Controls the layout and themes
 *
 * @version $Id: Layout.php,v 1.55 2003/06/30 12:24:45 matt Exp $
 * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
 * @package phpWebSite
 */
class PHPWS_Layout extends PHPWS_Layout_Forms{

  /**
   * Directory of current theme
   *
   * @var string
   * @access private
   */
  var $theme_dir;

  /**
   * Directory of box files for current theme
   *
   * @var string
   * @access private
   */
  var $box_dir;

  /**
   * Web address of current theme
   *
   * @var string
   * @access private
   */
  var $theme_address;

  /** Web address of box files for current theme
   *
   * @var string
   * @access private
   */
  var $box_address;

  /**
   * Current theme used by session
   *
   * @var string
   * @access private
   */

  var $current_theme;

  /**
   * Determines if user can change theme
   *
   * @var boolean
   * @access private
   */
  var $userAllow;

  /**
   * Page title added to theme
   *
   * @var string
   * @access private
   */
  var $page_title;

  /**
   * Keywords for meta tag
   *
   * @var string
   * @access private
   */
  var $meta_keywords;


  /**
   * Content type for meta tag
   *
   * @var string
   * @access private
   */
  var $meta_content;

  /**
   * Robot for meta tag
   *
   * @var string
   * @access private
   */
  var $meta_robot;

  /**
   * Description for meta tag
   *
   * @var string
   * @access private
   */
  var $meta_description;

  /**
   * Author for meta tag
   *
   * @var string
   * @access private
   */
  var $meta_author;

  /**
   * Owner for meta tag
   *
   * @var string
   * @access private
   */
  var $meta_owner;

  /**
   * Array list of theme variables
   *
   * @var array
   * @access private
   */
  var $themeVarList;

  /**
   * Array of theme variables and the elements
   * associated with that variable
   * 
   * @var array
   * @access private
   */
  var $ordered_list;

  /**
   * Indicates whether in Box Move mode
   *
   * @var boolean
   * @access private
   */
  var $_move;

  /**
   * Indicates whether in Box Change mode
   *
   * @var boolean
   * @access private
   */
  var $_change;

  /**
   * List of content variables and the information
   * associated with them
   *
   * @var array
   * @access private
   */
  var $content_array;

  /**
   * Layout of current theme
   *
   * @var array
   * @access private
   */
  var $row_col;

  /**
   * Temporarily stores javascript to be inserted during execution.
   *
   * @var    string
   * @access private
   */
  var $_js;

  /**
   * Temporarily stores styles to be inserted during execution.
   *
   * @var    string
   * @access private
   */
  var $_style;


  /**
   * If true, layout panel appears
   *
   * @var boolean
   * @access private
   */
  var $_panel;

  /**
   * If true, user layout panel appears
   *
   * @var boolean
   * @access private
   */
  var $_userPanel;

  /**
   * Name of default theme
   *
   * @var string
   * @access private
   */
  var $_default;

  function PHPWS_Layout($loadTheme=TRUE){
    $this->initializeLayout();

    if ($loadTheme)
      $this->loadBoxInfo();
  }


  /**
   * Initializes layout for user session
   */
  function initializeLayout($changeTheme=NULL){
    list($result) = $GLOBALS["core"]->sqlSelect("mod_layout_config");
    PHPWS_Template::refreshTemplate("layout");

    if ($result){
      extract ($result);

      if (!is_null($changeTheme))
	$theme = $changeTheme;
      elseif(isset($_SESSION['OBJ_user']) && $_SESSION["OBJ_user"]->getUserVar("theme", NULL, "layout"))
	$theme = $_SESSION["OBJ_user"]->getUserVar("theme", NULL, "layout");
      elseif(isset($default_theme))
	$theme = $default_theme;
      else
	$theme = "Default";

      if (!$this->themeExists($theme)){
	$theme = "Default";
	$this->_changeDefault($theme);
      }

      $this->ordered_list     = $this->themeVarList = $this->row_col = $this->content_array = NULL;
      $this->current_theme    = $theme;
      $this->userAllow        = $userAllow;

      $this->theme_dir        = $GLOBALS["core"]->home_dir . "themes/$theme/";
      $this->box_dir          = $this->theme_dir . "boxstyles/";
      $this->theme_address    = "themes/$theme/";
      $this->box_address      = $this->theme_address . "boxstyles/";

      $this->page_title       = $page_title;
      $this->meta_keywords    = $meta_keywords;
      $this->meta_content     = $meta_content;
      $this->meta_description = $meta_description;
      $this->meta_robots      = $meta_robots;
      $this->meta_author      = $meta_author;
      $this->meta_owner       = $meta_owner;

      $this->_default         = $default_theme;
      $this->_style = NULL;
      $this->_js = NULL;
    }
  }

  function themeExists($theme){
    return file_exists($GLOBALS["core"]->home_dir . "themes/$theme/theme.tpl");
  }


  /**
   * Prints the theme and boxes
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   */

  function displayTheme(){
    include_once(PHPWS_SOURCE_DIR . "mod/layout/conf/config.php");
    if ($_SESSION["OBJ_user"]->isUser()){
      $newTheme = $_SESSION["OBJ_user"]->getUserVar("theme");
      if($newTheme != $this->current_theme)
	$this->initializeLayout($newTheme);
    }

    if (isset($_REQUEST["lay_quiet"]) && $_REQUEST["lay_quiet"] == 1)
      return;

    $THEME["THEME_DIRECTORY"] = $this->theme_address;

    $extraInfoFile = PHPWS_HOME_DIR . "themes/" . $this->current_theme . "/theme.php";
    if ((defined("ALLOW_THEME_PHP_INSERTION") && ALLOW_THEME_PHP_INSERTION == TRUE) &&  is_file($extraInfoFile))
      include($extraInfoFile);
    
    if (!is_array($this->ordered_list))
      $this->loadBoxInfo();

    foreach ($this->ordered_list as $theme_var=>$themeInfo){
      if (!isset($themeInfo["content"]))
	continue;

      $THEME[strtoupper($theme_var)] = NULL;

      foreach ($themeInfo["content"] as $content_var){
	unset($boxTemplate);
	$complete = 0;
	
	if (!isset($GLOBALS[$content_var]) || !is_array($GLOBALS[$content_var]))
	  continue;

	$templateDir = $this->box_dir . $this->content_array[$content_var]["box_file"];

	foreach ($GLOBALS[$content_var] as $tag=>$tagLabel)
	  if (!empty($tagLabel)){
	    $boxTemplate[strtoupper($tag)] = $tagLabel;
	    $complete = 1;
	  }

	if ($complete == 1){
	  $boxTemplate["HOME_ADDRESS"] = $GLOBALS["core"]->home_http;
	  $boxTemplate["THEME_DIRECTORY"] = $this->theme_address;
	  $boxTemplate["BOX_DIRECTORY"] = $this->box_address;
	} else
	  continue;

	if ($this->_move)
	  $THEME[strtoupper($theme_var)] .= "<div align=\"left\">" . $this->moveTop($content_var) . "</div>\n";

	$themeData = $GLOBALS["core"]->processTemplate($boxTemplate, "layout", $templateDir, FALSE);

	$THEME[strtoupper($theme_var)] = (isset($THEME[strtoupper($theme_var)])) ? $THEME[strtoupper($theme_var)] . $themeData : $themeData;

	if ($this->_move)
	  $THEME[strtoupper($theme_var)] .= "<div align=\"right\">" . $this->moveBottom($content_var) . "</div>\n";
	
	if ($this->_change)
	  $THEME[strtoupper($theme_var)] .= $this->changeBox($content_var);

	//$THEME[strtoupper($theme_var)] .= "<br />";
      }
    }

    if (isset($GLOBALS["Layout_title"]))
      $THEME["TITLE"] = $GLOBALS["Layout_title"];
    else
      $THEME["TITLE"] = $this->page_title;

    $THEME["METATAGS"] = $this->getMetaTags();
    $THEME["JAVASCRIPT"] = $GLOBALS["core"]->load_js_funcs();

    if($this->_js !== NULL)      $THEME["DYN_JAVASCRIPT"] = $this->_js;

    $THEME["STYLE"] = $this->pickCSS();

    if($this->_style !== NULL)
      $THEME["DYN_STYLE"] = $this->_style;


    if (is_file($this->theme_dir . "theme.tpl")){
      return $GLOBALS["core"]->processTemplate($THEME, "layout", $this->theme_dir . "theme.tpl", FALSE);
    } else {
      $this->_changeDefault("Default");
      header("location:.");
      exit();
    }
  }

  /**
   * Chooses style sheet file name to echo
   *
   * A theme developer can create a browsers.txt file to indicate alternate
   * style sheets to pull instead of the standard style.css if the user browser
   * matches one of the searches
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   */
  function pickCSS(){
    $pick_css = $this->theme_dir."browsers.txt";

    if (file_exists($pick_css)){
      $allBrowsers = file($pick_css);
      foreach ($allBrowsers as $browser){
	$temp = explode("::", $browser);
	if (preg_match("/".$temp[0]."/", $_SERVER["HTTP_USER_AGENT"]) && file_exists($this->theme_dir.trim($temp[1]))){
	  $css = "<link rel=\"stylesheet\" href=\"" . $this->theme_address . trim($temp[1]) . "\" type=\"text/css\" />";
	  $browser_css = 1;
	  break;
	}
      }
    }

    if (!isset($browser_css) && file_exists($this->theme_dir."style.css"))
      $css = "<link rel=\"stylesheet\" href=\"" . $this->theme_address . "style.css\" type=\"text/css\" />";
    return $css;
  }


  function _toggleMove(){
    if ($this->_move)
      $this->_move = FALSE;
    else
      $this->_move = TRUE;
  }

  function _toggleChange(){
    if ($this->_change)
      $this->_change = FALSE;
    else
      $this->_change = TRUE;
  }


  function _changeUserTheme($theme){
    $_SESSION["OBJ_user"]->setUserVar("theme", $theme);

    $this->initializeLayout($theme);
    $this->loadBoxInfo();
  }

  function _changeDefault($theme){
    $update["default_theme"] = $theme;
    $GLOBALS["core"]->sqlUpdate($update, "mod_layout_config");
    $oldTheme = $this->_default;
    $this->_default = $theme;

    if ($oldTheme == $this->current_theme){
      $this->initializeLayout($theme);
      $this->loadBoxInfo();
    }
  }

  function getMetaTags(){
    $metatags = "<meta name=\"generator\" content=\"phpWebSite\" />\n";

    if ($this->meta_keywords)
      $metatags .= "<meta name=\"keywords\" content=\"".$this->meta_keywords."\" />\n";

    if ($this->meta_description)
      $metatags .= "<meta name=\"description\" content=\"".$this->meta_description."\" />\n";

    if ($this->meta_robots){
      switch ($this->meta_robots){
      case "00":
	$metatags .= "<meta name=\"robots\" content=\"noindex, nofollow\" />\n";
	break;

      case "01":
	$metatags .= "<meta name=\"robots\" content=\"noindex, follow\" />\n";
	break;

      case "10":
	$metatags .= "<meta name=\"robots\" content=\"index, nofollow\" />\n";
	break;

      case "11":
	$metatags .= "<meta name=\"robots\" content=\"index, follow\" />\n";
	break;
      }
    }

    if ($this->meta_author)
      $metatags .= "<meta name=\"author\" content=\"".$this->meta_author."\" />\n";

    if ($this->meta_owner)
      $metatags .= "<meta name=\"owner\" content=\"".$this->meta_owner."\" />\n";

    if ($this->meta_content)
      $metatags .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=" . $this->meta_content."\" />\n";

    return $metatags;
  }



  /**
   * Adds to or replaces the page title
   *
   * @author                    Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   * @param  string  title      Page title
   * @param  boolean overWrite  If TRUE, the title will overwrite the default
   *                            page title
   */  
  function addPageTitle($title, $overWrite=FALSE){
    $title = strip_tags($title);
    if ($overWrite)
      $GLOBALS["Layout_title"] = $title;
    else
      $GLOBALS["Layout_title"] = $title . " - " . $_SESSION["OBJ_layout"]->page_title;
  }

  function _changeDefaultTitle($title){
    $title = strip_tags($title);
    $GLOBALS["core"]->sqlUpdate(array("page_title"=>$title), "mod_layout_config");
    $_SESSION["OBJ_layout"]->page_title = $title;
  }


  function _dropTheme($theme){
    $GLOBALS["core"]->sqlDelete("mod_layout_box", "theme", $theme);
  }


  function reorder_boxes($ord_trans_var=NULL){
    $_SESSION["OBJ_layout"]->initializeLayout();
    $_SESSION["OBJ_layout"]->loadBoxInfo();
  }


  function getBoxstyles(){
    if(!($boxes = $GLOBALS["core"]->readDirectory($this->box_dir)))
      return NULL;
    
    $i = NULL;
    foreach ($boxes as $filename){
      if (preg_match("/\.tpl\$/i", $filename))
	  $templates[] = $filename;
    }
    return $templates;
  }



  function set_box_order($theme_var, $rows){
    if (count($rows)){
      foreach($rows as $update["box_order"]=>$content_var){
	if ($content_var){
	  $where["content_var"] = $content_var;
	  $where["theme"]       = $this->current_theme;
	  $update["theme_var"]  = $theme_var;
	  $GLOBALS["core"]->sqlUpdate($update, "mod_layout_box", $where);
	}
      }
    }
    $this->loadBoxInfo();
  }


  function drop_layout($drop_id){
    $GLOBALS["core"]->sqlDelete("mod_layout_box", "id", $drop_id);
  }


  function update_layout(){
    extract($_POST);

    list($lay_update_id) = each($lay_update_temp);

    $sql = $GLOBALS["core"]->sqlSelect("mod_layout_box", "id", $lay_update_id);
    list(,$sql_result) = each($sql);
    extract($sql_result);

    if ($lay_trans_var[$id] != $theme_var){
      $high_rank = $GLOBALS["core"]->sqlMaxValue("mod_layout_box", "box_order", "theme_var", $lay_trans_var[$id]);
    }

    $upd_array = array ('theme_var'=>$lay_trans_var[$lay_update_id], 'box_file'=>$lay_box_file[$lay_update_id], 'popbox'=>$lay_popbox[$lay_update_id]);

    if ($high_rank)
      $upd_array["box_order"] = $high_rank+1;

    if ($lay_home_only)
      $upd_array["home_only"] = 1;
    else
      $upd_array["home_only"] = 0;

    $GLOBALS["core"]->sqlUpdate($upd_array, "mod_layout_box", "id", $lay_update_id);
  }

  function get_themes(){
    $dir = $GLOBALS["core"]->readDirectory($GLOBALS["core"]->home_dir . "themes/", 1);
    if (is_array($dir)){
      foreach($dir as $file){
	if (file_exists($GLOBALS["core"]->home_dir . "themes/$file/theme.tpl"))
	  $theme_names[$file] = $file;
      }
      natcasesort($theme_names);
      return $theme_names;
    } else
      exit("Error - no themes found.");
  }

  function order_boxes(){
    extract($_POST);
    foreach ($lay_move as $content_var=>$key);
    foreach ($key as $direction=>$null_it);
    $this->shift_vert($content_var, $direction);
  }


  function check_new_box(){
    extract($_POST);
    $error_found =  FALSE;
  
    if (!$GLOBALS["core"]->isValidInput($new_content_var) && $legacy=="not"){
      $GLOBALS["CNT_layout"]["content"] .= "<span class=\"errortext\">You must enter an alphanumeric Content Variable</span><br />";
      $error_found = TRUE;
    }
    
    if (!$GLOBALS["core"]->isValidInput($new_theme_var)){
      $GLOBALS["CNT_layout"]["content"] .= "<span class=\"errortext\">You need to enter an alphanumeric Theme Variable.</span><br />";
      $error_found = TRUE;
    }
    
    if (!$box_file || $box_file=="default")
      $box_file = $this->default_box;
    
    if (!$popbox_file || $popbox_file=="default")
      $popbox_file = $this->default_pop;
    
    $lgcy_blk = $lgcy_cnt = 0;
    
    if ($legacy != "not"){
      if ($legacy == "lgcy_blk")
	$lgcy_blk = 1;
      else
	$lgcy_cnt = 1;
    }
    
    if (!$home_only)
      $home_only = 0;
    
    if ($error_found){
      $template_array = NULL;
      $this->format_module($lay_mod_title);
    }
    else {
      $this->create_temp($lay_mod_title, $new_content_var, $new_theme_var, $home_only, $box_file, $popbox_file, $lgcy_blk, $lgcy_cnt, $lay_blk_file);
      header("location:index.php?module=layout&lay_adm_op=admin_layout&lay_update_mod=1&lay_mod_title=$lay_mod_title");
      exit();
    }
    $GLOBALS["CNT_layout"] = $GLOBALS["CNT_layout"];
  }


  function popbox($box_title=NULL, $box_content=NULL, $box_footer=NULL, $mod_var=NULL){
    if (!($result = $GLOBALS["core"]->sqlSelect("mod_layout_box", array("content_var"=>$mod_var, "theme"=>$this->current_theme))))
      $box_file = "default_pop.tpl";
    else
      $box_file = $result[0]["popbox"];
    
    $home_address = "http://".$GLOBALS["core"]->home_http;

    $BOX["HOME_ADDRESS"] = $home_address;
    $BOX["BOX_DIRECTORY"] = "http://" . $this->box_address;
    $BOX["THEME_DIRECTORY"] = "http://" . $this->theme_address;
    $BOX["TITLE"] = $box_title;
    $BOX["CONTENT"] = $box_content;
    $BOX["FOOTER"] = $box_footer;

    $box_dir = $this->box_dir;

    if (is_file($box_dir . $box_file))
      $use_file = $box_dir . $box_file;
    else
      $use_file = $box_dir . "default_box.tpl";

    $hold = $GLOBALS["core"]->processTemplate($BOX, "layout", $use_file, FALSE);

    if ($this->_change)
      $hold .= $this->changePop($mod_var);

    if (!is_null($mod_var)){
      $GLOBALS[$mod_var]["content"] = (isset($GLOBALS[$mod_var]["content"])) ? $GLOBALS[$mod_var]["content"] . $hold : $hold;
      return TRUE;
    }
    return $hold;
  }



  function uninstallBoxStyle($moduleDir){
    $modFile   = $GLOBALS["core"]->source_dir . "mod/" . $moduleDir . "/conf/boost.php";

    if (!file_exists($modFile))
      return $_SESSION["translate"]->it("Module Information file missing in") . " $modFile<br />";

    include ($modFile);

    $success = $GLOBALS["core"]->sqlDelete("mod_layout_box", "mod_title", $mod_title);

    if ($success)
      return "<b>" . $_SESSION["translate"]->it("Layout for [var1] uninstalled successfully", $mod_pname) . "</b><br />";
    else
      return "<b>" . $_SESSION["translate"]->it("Layout for [var1] NOT uninstalled successfully", $mod_pname) . "</b><br />";

  }


  function getThemeVars(){
    return $_SESSION["OBJ_layout"]->themeVarList;
  }

  /**
   * Used by programmers to add styles on the fly.
   *
   * @author Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @param  string  $style The style you wish to add in a string format.
   * @return boolean TRUE on success or FALSE on failure.
   * @access public
   */
  function addStyle($style) {
    $this->_style .= $style;
    return TRUE;
  }

  /**
   * Used by programmers to add javascript on the fly.
   *
   * @author Adam Morton <adam@NOSPAM.tux.appstate.edu>
   * @param string $script The java script in string format.
   * @return boolean TRUE on success or FALSE on failure.
   * @access public
   */
  function addJavaScript($script) {
    $this->_js .= $script;
    return TRUE;
  }

  function getBoxByContent($content_var){
    return $this->content_array[$content_var]["box_file"];
  }

  function getPopByContent($content_var){
    return $this->content_array[$content_var]["popbox"];
  }

  function dropBox($content_var){
    $themes = PHPWS_Layout::get_themes();
    foreach ($themes as $theme){
      if ($boxes = PHPWS_Layout_Box::getThemeVar($theme, $content_var)){
	foreach ($boxes as $boxInfo){
	  if($GLOBALS["core"]->sqlDelete("mod_layout_box", "id", $boxInfo["id"]))
	    PHPWS_Layout_Box::rebuildThemeBoxes($theme, $boxInfo["theme_var"]);
	  else
	    return FALSE;
	}
      }
    }
    $_SESSION["OBJ_layout"]->loadBoxInfo();
    return TRUE;
  }

}

?>