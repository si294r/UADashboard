<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }    
    
    public function list_channel() {
        $query = $this->db->query("select channel from tbl_ua_setting order by channel");
        return $query->result_array();
    }

    public function get($channel) {
        if ($channel == "") {
            $query = $this->db->query("select * from tbl_ua_setting order by channel limit 1");
        } else {
            $query = $this->db->query("select * from tbl_ua_setting where channel = ? order by channel limit 1", array($channel));
        }
        return $query->row_array();
    }

    public function save() {
        $this->db->query(
                    "update tbl_ua_setting set arpu_limit=?, cpi_limit=?, ppu_limit=?, d1_limit=?, d3_limit=?, d7_limit=? where channel=?",
                    array($_POST['arpu_limit'], $_POST['cpi_limit'], $_POST['ppu_limit'], $_POST['d1_limit'], $_POST['d3_limit'], $_POST['d7_limit'], $_POST['channel'])
                );
    }
}
