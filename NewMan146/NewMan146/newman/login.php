<?php
//******************************************************************************************
//**                                                                                      **
//** phpNewsManager v1.30                                                                 **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 11th.June,2002                                                           **
//******************************************************************************************

$info = base64_encode("$login:$pass");
setcookie("nm_user","$info",time()+15552000); // 6 mo is 15552000
header ("Location: index.php"); 
?>
