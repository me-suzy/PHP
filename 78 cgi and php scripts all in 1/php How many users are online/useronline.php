<?                                                                                 
#################################################################################################             
#                                                                                                             
#  Project           	: phpUseronline
#  File name         	: useronline.php
#  Version           	: 1.10
#  Last Modified By  	: Erich Fuchs
#  e-mail            	: erich.fuchs@netone.at
#  Purpose           	: Main File
#  Last modified     	: 20 Apr 2001
#  Copyright		: 2001 by NETonE High Quality Networking (http://www.netone.at)
#                                                                                                             
#################################################################################################
                                                                                                              
#  Configuration
#################################################################################################                                                                                                              

$server         	= "localhost";  	// Your mySQL Server, most cases "localhost"                  
$db_user        	= "mysql_user"; 	// Your mySQL Username                                        
$db_pass        	= "mysql_pass";		// Your mySQL Password                                        
$database       	= "phpUseronline";	// Database Name                                              

$timeoutseconds 	= 300;			// Timeout value in seconds

#  End Configuration - DO NOT EDIT BEHIND THIS LINE !!!
#################################################################################################                                                                                                              

$timestamp=time();                                                                                            
$timeout=$timestamp-$timeoutseconds;  
mysql_connect($server, $db_user, $db_pass) or die ("Useronline Database CONNECT Error");                                                                   
mysql_db_query($database, "INSERT INTO useronline VALUES ('$timestamp','$REMOTE_ADDR','$PHP_SELF')") or die("Useronline Database INSERT Error"); 
mysql_db_query($database, "DELETE FROM useronline WHERE timestamp<$timeout") or die("Useronline Database DELETE Error");
$result=mysql_db_query($database, "SELECT DISTINCT ip FROM useronline WHERE file='$PHP_SELF'") or die("Useronline Database SELECT Error");
$user  =mysql_num_rows($result);                                                                              
mysql_close();                                                                                                
if ($user==1) {echo"$user User online";} else {echo"$user Users online";}
?>