/**
 * ----------------------------------------------
 * Daily Counter 1.1
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

- Chmod these files:

    visitor.php      - 755
    ip.txt           - 666
    daily.txt        - 666
    total_visits.txt - 666

- Usage:
    
    <?php
        
        include ("./config.inc.php");
        if ($COUNT_CFG['use_db']) {
            include ("./mysql.class.php");
        }
        include ("./counter.class.php");
        
        $counter = new dcounter();
        $visits = $counter->show_counter();  /* Returns an associative array */     

        echo $visits['total'];
       
        echo $visits['visits_today'];
        
    
    ?>


