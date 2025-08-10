<?php

/* Edit these to reflect your v0.8.x database information */

define("OLD_DBUSER", "old_username");
define("OLD_DBPASS", "old_password");
define("OLD_DBHOST", "old_host");
define("OLD_DBNAME", "old_database_name");
define("OLD_PREFIX", "old_table_prefix");

function old_connect() {
  $dsn = "mysql://" . OLD_DBUSER . ":" . OLD_DBPASS . "@" . OLD_DBHOST . "/" . OLD_DBNAME;
  $db = DB::connect($dsn, TRUE);

  if(DB::isError($db)) {
    exit("There was a problem connecting to your v0.8.x database!<br />DSN: $dsn");
  } else {
    $db->setFetchMode(DB_FETCHMODE_ASSOC);
    return $db;
  }
}

function new_connect(){
  return $GLOBALS['core']->db;
}

function duplicateFatcat($title){
  $core = $GLOBALS["core"];

  $title = addslashes($title);
  return $core->getOne("select cat_id from " . NEW_PREFIX . "mod_fatcat_categories where title='$title'");

}

function writeReroute($mod_title, $idArray){
  $core = $GLOBALS["core"];

  $directory = PHPWS_HOME_DIR . "convert/reroute/";
  if (!is_dir($directory) || !is_writable($directory)){
    echo "Unable to create reroute file in convert/reroute directory.";
    return;
  }

  $file = "<?php \n";
  foreach ($idArray as $oldID=>$newID){
    $file .= "\$convert['". $oldID . "'] = ". $newID . ";\n";
  }
  
  $file .= "\n?>";
  
  $core->writeFile($directory."/" . $mod_title . ".php", $file, TRUE, TRUE);
}

?>