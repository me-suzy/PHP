<?
//******************************************************************************************
//** phpNewsManager v1.40                                                                 **
//** contact: gregor@klevze.si                                                            **
//** Last edited: 20th.March,2002                                                         **
//******************************************************************************************

//$info = base64_encode("$language");
setcookie("clang","$language",time()+15552000); // 6 mo is 15552000
header ("Location: index.php"); 
?>
