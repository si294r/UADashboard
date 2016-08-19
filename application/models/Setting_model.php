<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends CI_Model {

    private $project;
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }    
    
    public function set_project($value) {
        $this->project = $value;
    }
    
    public function list_channel() {
        $query = $this->db->query("select channel from tbl_ua_setting where project = ? order by channel", array($this->project));
        return $query->result_array();
    }

    public function get($channel) {
        if ($channel == "") {
            $query = $this->db->query("select * from tbl_ua_setting where project = ? order by channel limit 1", array($this->project));
        } else {
            $query = $this->db->query("select * from tbl_ua_setting where project = ? and channel = ? order by channel limit 1", array($this->project, $channel));
        }
        return $query->row_array();
    }

    public function save() {
        $this->db->query(
                    "update tbl_ua_setting set arpu_limit=?, cpi_limit=?, ppu_limit=?, d1_limit=?, d3_limit=?, d7_limit=? where project = ? and channel = ?",
                    array($_POST['arpu_limit'], $_POST['cpi_limit'], $_POST['ppu_limit'], $_POST['d1_limit'], $_POST['d3_limit'], $_POST['d7_limit'], $this->project, $_POST['channel'])
                );
    }
}
