<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Daily_report extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION['signin'])) {
            redirect('signin');
        }
    }

    public function index($country = "ALL") {
        $this->load->model('daily_report_model', 'grid');
        $this->grid->set_country($country);
        
        $data['arr_country'] = $this->grid->get_arr_country();
        $this->load->view('daily_report_view', $data);        
    }

    public function get_session_report() {
        $this->load->model('daily_report_model', 'grid');
        echo json_encode(
            array(
                'country' => $this->grid->get_country(), 
                'start_date' => $this->grid->get_start_date(), 
                'end_date' => $this->grid->get_end_date()
                )
        );
    }

    public function grid($tipe = "", $start_date = "", $end_date = "") {
        $this->load->model('daily_report_model', 'grid');

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if (is_numeric($tipe)) {
                    switch ($tipe) {
                        case "0":
                            $this->grid->set_start_date($start_date);
                            $this->grid->set_end_date($end_date);
                            break;
                        case "1":
                            $this->grid->set_start_date(date('Y-m-d', strtotime("-7 days")));
                            $this->grid->set_end_date(date('Y-m-d', strtotime("-1 days")));
                            break;
                        case "2":
                            $this->grid->set_start_date(date('Y-m-d', strtotime("-14 days")));
                            $this->grid->set_end_date(date('Y-m-d', strtotime("-1 days")));
                            break;
                        case "3":
                            $this->grid->set_start_date(date('Y-m-d', strtotime("-1 months")));
                            $this->grid->set_end_date(date('Y-m-d', strtotime("-1 days")));
                            break;
                        case "4":
                            $this->grid->set_start_date(date('Y-m-d', strtotime("-3 months")));
                            $this->grid->set_end_date(date('Y-m-d', strtotime("-1 days")));
                            break;
                    }
                }
                $data = $this->grid->get();
                $index0 = -1;
                $arr_index1 = [];
                $tree = array();
                foreach ($data as $k => $v) {
                    if ($v['node'] == 0) {
                        $index0++;
                        $index1 = -1;
                        $tree[$index0] = $v;
                    } elseif ($v['node'] == 1) {
                        $index1++;
                        $arr_index1[$v['referrer_name']] = $index1;
                        $tree[$index0]['children'][$index1] = $v;
                    } elseif ($v['node'] == 2) {
                        $v['leaf'] = true;
                        $temp_index1 = $arr_index1[$v['referrer_name']];
                        $tree[$index0]['children'][$temp_index1]['children'][] = $v;
                    }
                }
                echo json_encode(
                        array('children' => $tree, 'text' => '.', 'success' => true, 'method' => 'GET')
                );
                break;
            default :
                echo json_encode(
                        array('children' => null, 'text' => 'Unsupported Request Method', 'success' => FALSE)
                );
        }
    }

}
