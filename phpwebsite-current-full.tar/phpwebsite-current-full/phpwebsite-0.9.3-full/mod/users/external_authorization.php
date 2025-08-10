<?php
/*
 * Function to use the VMS authorization technique in
 * lue of an honest LDAP connection
 *
 * @author Mike Wilson <mike@NOSPAM.tux.appstate.edu>
 * @param username : string with username
 * @param password : string with password
 * @return String with authorized user type FACULTY/STAFF as example
 *         OR returns false if error or unauthorized.
 */

function authorize($username,$password) {
  if (!($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)))
    return false;
  if (!socket_connect($socket,"myadress",myport))
    return false;
  if (!socket_write($socket,$username."/".$password."\r\n"))
    return false;
  while(($buf = socket_read($socket, 512)) !== false && ($buf!=""))
    $data .= $buf;
  
  if ($data=="INVALID") {
    echo "INVALID<br />";
    return false;
  } else if (substr($data,0,2)=="OK") {
    echo "valid::".substr($data,3)."<br />";
    return substr($data,3);
  }
  return false;
}
?>