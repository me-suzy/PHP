<?php
/**
 * Configuration file for phatform module
 *
 * @version $Id: phatform.php,v 1.27 2003/06/19 13:50:13 steven Exp $
 */

/* DO NOT CHANGE THIS UNLESS YOU ARE ADAM OR STEVEN! */
define("PHAT_VERSION", "2.21");

/* Main title to use throughout phatform */
define("PHAT_TITLE", "Form Generator v" . PHAT_VERSION);

/* Set the hex to use for alternating section colors when viewing forms */
define("PHAT_SECTION_HEX", "#EEEEEE");

/* Set default rows and columns for textareas */
define("PHAT_DEFAULT_ROWS", 5);
define("PHAT_DEFAULT_COLS", 40);

/* Set default size and maxsize for textfields */
define("PHAT_DEFAULT_SIZE", 33);
define("PHAT_DEFAULT_MAXSIZE", 255);

/* Whether or not the blurb and value are required fields when making a form */
define("PHAT_BLURB_REQUIRED", 0);
define("PHAT_VALUE_REQUIRED", 0);

/* Default size for a multiselect list */
define("PHAT_MULTISELECT_SIZE", 4);

/* Turn on and off debugging */
define("PHAT_DEBUG_MODE", 0);

/* Default page limit for form elements */
define("PHAT_PAGE_LIMIT", 10);

/* Turn on and off instructions */
define("PHAT_SHOW_INSTRUCTIONS", 1);

/* How many entries to show per page when viewing data */
define("PHAT_ENTRY_LIST_LIMIT", 20);

/* Time to live for the cache of the entry list */
define("PHAT_ENTRY_LIST_TTL", 300);
?>