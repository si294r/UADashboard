<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Signin extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data['message'] = "";
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $this->load->model('admin_model', 'admin');
            $data = $this->admin->signin($_POST['username'], $_POST['password']);
            if (is_object($data) || is_array($data)) {
                $_SESSION['signin'] = $data;
                redirect('home');
            } else {
                $data['message'] = "Sign In Failed.";
            }
        }
        $this->load->view('signin_view', $data);
    }

    public function out() {
        session_destroy();
        redirect('signin');
    }

}
