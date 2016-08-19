<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Setting2 extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION['signin'])) {
            redirect('signin');
        }
    }

    public function index($channel = "") {
        $this->load->model('setting_model', 'setting');
        $this->setting->set_project('jellypop');
        if (isset($_POST['channel'])) {
            $this->setting->save();
        }
        $data['arr_channel'] = $this->setting->list_channel();
        $data['row_setting'] = $this->setting->get($channel);
        $data['selected_channel'] = $channel;
        $this->load->view('setting2_view', $data);
    }

}
