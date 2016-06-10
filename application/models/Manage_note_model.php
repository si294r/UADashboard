<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_note_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }    
    
    private function get_json() {
        $input = file_get_contents("php://input");
        $json = json_decode($input);
        return $json;
    }
    
    public function get() {
        $start = isset($_GET['start']) && is_numeric($_GET['start']) ? $_GET['start'] : 0;
        $limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? $_GET['limit'] : 25;
        $query = $this->db->query("select * from tbl_ua_manage_note order by tanggal limit $limit offset $start");        
        return $query->result_array();
    }

    public function post() {
        $json = $this->get_json();
        return $json;
    }
    
    public function put($id) {
        $json = $this->get_json();
        $json->tanggal = substr($json->tanggal, 0, 10);
        if (!is_numeric($id)) {
            $id = 0;
        }
        $query = $this->db->query("select * from tbl_ua_manage_note where id = ?", array($id));
        $row = $query->row_array();
        if (!isset($row['id'])) {
            $this->db->query("insert into tbl_ua_manage_note "
                    . "select (coalesce(max(id), 0) + 1) as last_id, ?, ? from tbl_ua_manage_note", 
                    array($json->event_note, $json->tanggal));
        } else {
            $this->db->query("update tbl_ua_manage_note set event_note = ?, tanggal = ? where id = ?", 
                    array($json->event_note, $json->tanggal, $id));
        }        
        return $json;
    }
    
    public function delete($id) {
        $json = $this->get_json();
        $this->db->query("delete from tbl_ua_manage_note where id = ?", array($id));
        return $json;
    }
    
}
