<?php
/**
 * @package Core
 */
class PHPWS_WizardBag extends PHPWS_Debug{

  function js_insert($file, $form_name=NULL, $section_name=NULL, $check=0, $js_var_array=NULL){
    if ($check && !$_SESSION["OBJ_user"]->js_on)
      return NULL;

    if (is_array($js_var_array))
      extract($js_var_array);

    if (file_exists($this->source_dir."js/".$file.".php")){
      include($this->source_dir."js/".$file.".php");
      return $js;
    }
  }

  /**
   * loads the required javascript functions to be echoed in a theme's head
   *
   * @author Matthew McNaney
   */
  function load_js_funcs(){
    if (count($this->js_func)){
      $info = "<script language=\"JavaScript\" type=\"text/javascript\">
<!--\n";
      foreach ($this->js_func as $js_functions){
        $info .= $js_functions."\n";
      }
      $info .= "//-->
</script>\n";

      return $info;
    }
  }

  function whereami($file_only=NULL){
    $loop = 0;
    $suffix = $prefix = NULL;
    if (count($_GET)){
      $return_array = $_GET;
    } elseif (count($_POST)){
      $return_array = $_POST;
    } else
      $return_array = NULL;

    if ($return_array){
      $suffix = "?";
      foreach($return_array as $var_name=>$value){
        if ($loop)
          $suffix .= "&";

        if (is_array($value)){
          list($key, $val2) = each($value);
          $suffix .= $var_name."[".$key."]=".$val2;
        } else {
          $suffix .= "$var_name=$value";
        }
        $loop = 1;
      }
    }

    if ($file_only){
      $address = explode("/", $_SERVER["PHP_SELF"]);
      $prefix = end($address);
    } else
      $prefix=$_SERVER["PHP_SELF"];

    return $prefix . $suffix;
  }

  /**
   * Sends the user back to the index page
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   */
  function home(){
    header("location:index.php");
    exit();
  }

  /**
   * Seeds the random generator
   *
   * @author Matthew McNaney <matt@NOSPAM.tux.appstate.edu>
   */
  function seed_rand() {
    $seed_set = (double)microtime() * 100000;
    srand($seed_set);
  }


  function toggle(&$tog, $ret_value=NULL){
    if(!$tog) {
      if($ret_value !== NULL)
	$tog = $ret_value;
      else
	$tog = 1;
    } else {
      $tog = NULL;
    }
  }

}

?>