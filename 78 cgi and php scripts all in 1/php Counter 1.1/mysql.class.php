<?php
/**
 * ----------------------------------------------
 * Daily Counter 1.1
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

class dcounter_sql {

    var $conn_id;
    var $result;
    var $record;
    var $db = array();

    function dcounter_sql() {
        global $COUNT_DB;
        $this->db = $COUNT_DB;
    }

    function connect() {
        $this->conn_id = mysql_connect($this->db['host'],$this->db['user'],$this->db['pass']);
        if ($this->conn_id == 0) {
            return false;
        }
        if (!mysql_select_db($this->db['dbName'], $this->conn_id)) {
            return false;
        }
        return $this->conn_id;
    }

    function query($query_string) {
        $this->result = mysql_query($query_string,$this->conn_id);
        if (!$this->result) {
            return false;
        }
        return $this->result;
    }

    function fetch_array($query_id) {
        $this->record = mysql_fetch_array($query_id,MYSQL_ASSOC);
        return $this->record;
    }

    function num_rows($query_id) {
        return ($query_id) ? mysql_num_rows($query_id) : 0;
    }


    function num_fields($query_id) {
        return ($query_id) ? mysql_num_fields($query_id) : 0;
    }

    function insert_id() {
        return mysql_insert_id($link_id);
    }

    function free_result($query_id) {
        return mysql_free_result($query_id);
    }

    function affected_rows() {
        return mysql_affected_rows($this->conn_id);
    }

    function close_db() {
        if($this->conn_id) {
            return mysql_close($this->conn_id);
        } else {
            return false;
        }
    }

}

?>