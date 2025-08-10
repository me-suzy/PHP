-----------------------------------------------------------------------------------
phpWebSite Menu Manager README
-----------------------------------------------------------------------------------
Author: Steven Levin <steven@NOSPAM.tux.appstate.com>
Version: 0.7 12/19/2002

-------------------------------
REQUIREMENTS:
-------------------------------
phpWebSite v0.9.0

-------------------------------
DELEVOPER API
-------------------------------
How do I register with search?

Put in you install.php file this info (replace linkman info with yours).
$search['module'] = "linkman";
$search['search_class'] = "PHPWS_Linkman";
$search['search_function'] = "search";
$search['search_cols'] = "title, url, description, datePosted";
$search['view_string'] = "&amp;LMN_op=visitLink&amp;LMN_id=";
$search['show_block'] = 1;

if(!$core->sqlInsert($search, "mod_search_register"))
     $content .= "Problem registering search<br />";

How do I unregister with search?

Put this call in you uninstall.php file.
$GLOBALS['core']->sqlDelete("mod_search_register", "module", "linkman");

Then you must implement what you defined as your search_function.
Have it accept and use the where clause gnerated by search and 
return and array with summary for values keyed by their repective ids.

If search_cols are not registered then search will pass the user query
to the search_function and expect a returned array with summary for values 
keyed by their DB ids.
