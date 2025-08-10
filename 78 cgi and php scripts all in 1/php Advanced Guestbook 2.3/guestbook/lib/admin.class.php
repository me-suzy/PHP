<?php
/**
 * ----------------------------------------------
 * Advanced Guestbook 2.3.1 (PHP/MySQL)
 * Copyright (c)2001 Chi Kien Uong
 * URL: http://www.proxy2.de
 * ----------------------------------------------
 */

class gb_admin {

    var $db;
    var $session;
    var $SELF;
    var $uid;
    var $VARS;
    var $table;

    function gb_admin($session,$uid) {
        global $HTTP_SERVER_VARS;
        $this->session = $session;
        $this->uid = $uid;
        $this->SELF = basename($HTTP_SERVER_VARS["PHP_SELF"]);
    }

    function get_updated_vars() {
        $this->db->query("SELECT * FROM ".$this->table['cfg']);
        $this->VARS = $this->db->fetch_array($this->db->result);
        $this->db->free_result($this->db->result);
    }

    function NoCacheHeader() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
    }

    function show_panel($panel) {
        global $smilie_list, $smilie_data;
        $this->NoCacheHeader();
        include_once "./admin/panel_$panel.php";
        include_once "./admin/footer.inc.php";
    }

    function scan_smilie_dir() {
        $smilies = '';
        chdir("./img/smilies");
        $hnd = opendir(".");
        while ($file = readdir($hnd)) {
            if(is_file($file)) {
                if ($file != "." && $file != "..") {
                    if (ereg(".gif|.jpg|.png|.jpeg",$file)) {
                        $smilie_list[] = $file;
                    }
                }
            }
        }
        closedir($hnd);
        if (isset($smilie_list)) {
            asort($smilie_list);
            for ($i=0;$i<sizeof($smilie_list);$i++) {
                $size = GetImageSize($smilie_list[$i]);
                if (is_array($size)) {
                    $smilies[$smilie_list[$i]] = "<img src=\"img/smilies/$smilie_list[$i]\" $size[3]>";
                }
            }
        }
        chdir("../../");
        return $smilies;
    }

    function show_entry($tbl="gb") {
        global $entry, $record, $GB_UPLOAD;
        if ($tbl=="priv") {
            $gb_tbl = $this->table['priv'];
            $book_id = 1;
        } else {
            $gb_tbl = $this->table['data'];
            $tbl="gb";
            $book_id = 2;
        }
        $entries_per_page = $this->VARS["entries_per_page"];
        if(!isset($entry)) {
            $entry = 0;
        }
        if(!isset($record)) {
            $record = 0;
        }
        $next_page = $entry+$entries_per_page;
        $prev_page = $entry-$entries_per_page;
        $this->db->query("select count(*) total from $gb_tbl");
        $this->db->fetch_array($this->db->result);
        $total = $this->db->record['total'];
        if ($record > 0 && $record <= $total) {
            $entry = $total-$record;
            $next_page = $entry+$entries_per_page;
            $prev_page = $entry-$entries_per_page;
        }
        $result = $this->db->query("select x.*, y.p_filename, y.width, y.height from $gb_tbl x left join ".$this->db->table['pics']." y on (x.id=y.msg_id and y.book_id=$book_id) order by id desc limit $entry, $entries_per_page");
        $img = new gb_image();
        $img->set_border_size($this->VARS["img_width"], $this->VARS["img_height"]);
        $this->NoCacheHeader();
        include_once "./admin/panel_easy.php";
        include_once "./admin/footer.inc.php";
    }

    function del_entry($entry_id,$tbl="gb") {
        global $GB_UPLOAD;
        switch ($tbl) {
            case "gb" :
                $this->db->query("select p_filename from ".$this->table['pics']." WHERE (msg_id = '$entry_id' and book_id=2)");
                $result = $this->db->fetch_array($this->db->result);
                if ($result["p_filename"]) {
                    if (file_exists("./$GB_UPLOAD/$result[p_filename]")) {
                        unlink ("./$GB_UPLOAD/$result[p_filename]");
                    }
                    if (file_exists("./$GB_UPLOAD/t_$result[p_filename]")) {
                        unlink ("./$GB_UPLOAD/t_$result[p_filename]");
                    }
                }
                $this->db->query("DELETE FROM ".$this->table['data']." WHERE (id = '$entry_id')");
                $this->db->query("DELETE FROM ".$this->table['com']." WHERE (id = '$entry_id')");
                $this->db->query("DELETE FROM ".$this->table['pics']." WHERE (msg_id = '$entry_id' and book_id=2)");
                break;

            case "priv" :
                $this->db->query("select p_filename from ".$this->table['pics']." WHERE (msg_id = '$entry_id' and book_id=1)");
                $result = $this->db->fetch_array($this->db->result);
                if ($result["p_filename"]) {
                    if (file_exists("./$GB_UPLOAD/$result[p_filename]")) {
                        unlink ("./$GB_UPLOAD/$result[p_filename]");
                    }
                    if (file_exists("./$GB_UPLOAD/t_$result[p_filename]")) {
                        unlink ("./$GB_UPLOAD/t_$result[p_filename]");
                    }
                }
                $this->db->query("DELETE FROM ".$this->table['priv']." WHERE (id = '$entry_id')");
                $this->db->query("DELETE FROM ".$this->table['pics']." WHERE (msg_id = '$entry_id' and book_id=1)");
                break;

            case "com" :
                $this->db->query("DELETE FROM ".$this->table['com']." WHERE (com_id = '$entry_id')");
                break;
        }
    }

    function update_record($entry_id,$tbl="gb") {
        global $HTTP_POST_VARS;
        $gb_tbl = ($tbl=="priv") ? $this->table['priv'] : $this->table['data'];
        if (!get_magic_quotes_gpc() ) {
            while (list($var, $value)=each($HTTP_POST_VARS)) {
                $HTTP_POST_VARS[$var]=addslashes($value);
            }
        }
        reset($HTTP_POST_VARS);
        while (list($var, $value)=each($HTTP_POST_VARS)) {
            $HTTP_POST_VARS[$var]=trim($value);
        }
        if (!eregi(".+@[-a-z0-9_]+", $HTTP_POST_VARS['email'])) {
            $HTTP_POST_VARS['email'] = '';
        }
        if (!eregi("^http://[-a-z0-9_]+", $HTTP_POST_VARS['url'])) {
            $HTTP_POST_VARS['url'] = '';
        }
        $sqlquery= "UPDATE $gb_tbl set name='$HTTP_POST_VARS[name]', email='$HTTP_POST_VARS[email]', gender='$HTTP_POST_VARS[gender]', url='$HTTP_POST_VARS[url]', location='$HTTP_POST_VARS[location]', ";
        $sqlquery.="host='$HTTP_POST_VARS[host]', browser='$HTTP_POST_VARS[browser]', comment='$HTTP_POST_VARS[comment]', icq='$HTTP_POST_VARS[icq]', aim='$HTTP_POST_VARS[aim]' WHERE (id = '$entry_id')";
        $this->db->query($sqlquery);
    }

    function show_form($entry_id,$tbl="gb") {
        global $record;
        $gb_tbl = ($tbl=="priv") ? $this->table['priv'] : $this->table['data'];
        $this->db->query("select * from $gb_tbl where (id = '$entry_id')");
        $row = $this->db->fetch_array($this->db->result);
        for(reset($row); $key=key($row); next($row)) {
            $row[$key] = htmlspecialchars($row[$key]);
        }
        $this->NoCacheHeader();
        include_once "./admin/panel_edit.php";
        include_once "./admin/footer.inc.php";
    }

    function edit_template($tpl_name,$tpl_save) {
        global $HTTP_POST_VARS, $GB_TPL;
        $this->NoCacheHeader();
        $filename = "./templates/$tpl_name";
        if (file_exists("$filename") && $tpl_name != '') {
            if ($tpl_save == "update") {
                if (get_magic_quotes_gpc()) {
                   $HTTP_POST_VARS['gb_template'] = stripslashes($HTTP_POST_VARS['gb_template']);
                }
                $fd = fopen ($filename, "w");
                fwrite($fd,$HTTP_POST_VARS['gb_template']);
                $gb_template = $HTTP_POST_VARS['gb_template'];
            } else {
                $fd = fopen ($filename, "r");
                $gb_template = fread ($fd, filesize ($filename));
            }
            fclose ($fd);
        } else {
            $gb_template ='';
        }
        include_once "./admin/panel_template.php";
        include_once "./admin/footer.inc.php";
    }

    function show_settings($cat) {
        $this->db->query("select * from ".$this->table['words']);
        while ($this->db->fetch_array($this->db->result)) {
            $badwords[] = $this->db->record["word"];
        }
        $this->db->free_result($this->db->result);
        $this->db->query("select * from ".$this->table['ban']);
        while ($this->db->fetch_array($this->db->result)) {
            $banned_ips[] = $this->db->record["ban_ip"];
        }
        $this->db->free_result($this->db->result);
        $this->db->query("select * from ".$this->table['auth']." where ID=$this->uid");
        $row = $this->db->fetch_array($this->db->result);
        $this->NoCacheHeader();
        if ($cat == "general") {
            include_once "./admin/panel_main.php";
        } elseif ($cat == "style") {
            include_once "./admin/panel_style.php";
        } elseif ($cat == "pwd") {
            include_once "./admin/panel_pwd.php";
        }
        include_once "./admin/footer.inc.php";
    }

}

?>