<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }    

    public function signin($username, $password) {
        $query = $this->db->query(
                    "select * from tbl_ua_admin where username = ? and password = ? limit 1", 
                    array($username, md5($password))
                );
        return $query->row_array();
    }

}
