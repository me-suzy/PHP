<?php
/* database or textfile */

$COUNT_CFG["use_db"]     = false;

/* config */

$COUNT_CFG["block_time"] = 3600; /* sec */
$COUNT_CFG["offset"]     = 0;

/* textfile settings */

$COUNT_CFG["logfile"] = "./ip.txt";
$COUNT_CFG["counter"] = "./total_visits.txt";
$COUNT_CFG["daylog"]  = "./daily.txt";

/* database settings */

$COUNT_DB["dbName"] = "your_db";
$COUNT_DB["host"]   = "localhost";
$COUNT_DB["user"]   = "root";
$COUNT_DB["pass"]   = "";

$COUNT_TBL["visitors"] = "count_visitors";
$COUNT_TBL["daily"]    = "count_daily";
$COUNT_TBL["total"]    = "count_total";

?>