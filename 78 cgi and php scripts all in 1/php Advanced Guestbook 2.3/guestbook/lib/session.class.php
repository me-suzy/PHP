<?php
/**
 * ----------------------------------------------
 * Advanced Guestbook 2.3.1 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

class gb_session extends gbook_sql {

    var $expire = 7200;
    var $include_path;
    var $table;

    function gb_session($path='') {
        global $GB_TBL;
        $this->table =& $GB_TBL;
        $this->gbook_sql();
        $this->connect();
        $this->include_path = $path;
    }

    function isValidSession($session,$user_id) {
        $this->query("SELECT session, last_visit from ".$this->table['auth']." WHERE session='$session' and ID='$user_id'");
        $row = $this->fetch_array($this->result);
        if ($row) {            
            return ($this->expire + $row['last_visit'] > time()) ? $row["session"] : false;
        } else {
            return false;
        }
    }

    function isValidUser($user_id) {
        $this->query("SELECT username FROM ".$this->table['auth']." WHERE ID='$user_id'");
        $this->fetch_array($this->result);
        return ($this->record) ? true : false;
    }

    function changePass($user_id,$new_password) {
        $this->query("UPDATE ".$this->table['auth']." SET password=PASSWORD('$new_password') WHERE ID='$user_id'");
        return ($this->record) ? true : false;
    }

    function generateNewSessionID($user_id) {
        srand((double)microtime()*1000000);
        $session = md5 (uniqid (rand()));
        $timestamp = time();
        $this->query("UPDATE ".$this->table['auth']." SET session='$session', last_visit='$timestamp' WHERE ID='$user_id'");
        return $session;
    }

    function checkPass($username,$password) {
        $this->query("SELECT ID FROM ".$this->table['auth']." WHERE username='$username' and password=PASSWORD('$password')");
        $this->fetch_array($this->result);
        return ($this->record) ? $this->record["ID"] : false;
    }

    function checkSessionID() {
        global $username, $password, $session, $uid;
        if (isset($session) && isset($uid)) {
            return ($this->isValidSession($session,$uid)) ? array("session" => "$session", "uid" => "$uid") : false;
        } elseif (isset($username) && isset($password)) {
            if (!get_magic_quotes_gpc()) {
                $username = addslashes($username);
                $password = addslashes($password);
            }
            $ID = $this->checkPass($username,$password);
            if ($ID) {
                $session = $this->generateNewSessionID($ID);
                return array("session" => "$session", "uid" => "$ID");
            } else {
                return false;
            }

        } else {
            return false;
        }

    }

}

?>