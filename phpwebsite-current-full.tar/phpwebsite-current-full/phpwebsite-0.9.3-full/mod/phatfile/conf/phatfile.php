<?php
/*
 * @version $Id: phatfile.php,v 1.3 2003/05/15 16:33:50 adam Exp $
 */

/* The version of this module - DO NOT EDIT unless you are Adam :) */
define("PHAT_FILE_VERSION", "0.1");

/* The title to be used throughout the module */
define("PHAT_FILE_TITLE", "Document Manager v" . PHAT_FILE_VERSION);

/* The directory where phatfile should store it's files. Must have trailing slash */
define("PHAT_FILE_DIR", PHPWS_HOME_DIR . "files/phatfile/");

/* The http string to access files from. Must have trailing slash */
define("PHAT_FILE_HTTP", "http://" . PHPWS_HOME_HTTP . "files/phatfile/");

?>