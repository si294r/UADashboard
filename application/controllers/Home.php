<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION['signin'])) {
            redirect('signin');
        }
    }

    public function index() {
        $this->load->view('home_view');
    }

    public function manage_notes($id = "") {
        $this->load->model('manage_note_model', 'manage_note');

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $data = $this->manage_note->get();
                echo json_encode(
                        array('data' => $data, 'message' => 'Data Loaded', 'success' => true, 'method' => 'GET')
                );
                break;
            case 'POST':
                $data = $this->manage_note->post();
                echo json_encode(
                        array('data' => $data, 'message' => 'Data Created', 'success' => true, 'method' => 'POST')
                );
                break;
            case 'PUT':
                $data = $this->manage_note->put($id);
                echo json_encode(
                        array('data' => $data, 'message' => 'Data Updated', 'success' => true, 'method' => 'PUT')
                );
                break;
            case 'DELETE':
                $data = $this->manage_note->delete($id);
                echo json_encode(
                        array('data' => $data, 'message' => 'Data Deleted', 'success' => true, 'method' => 'DELETE')
                );
                break;
            default :
                echo json_encode(
                        array('data' => null, 'message' => 'Unsupported Request Method', 'success' => FALSE)
                );
        }
    }

    public function grid($tipe = "", $start_date = "", $end_date = "") {
        $this->load->model('grid_model', 'grid');

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
                $index = -1;
                $tree = array();
                foreach ($data as $k => $v) {
                    if ($v['node'] == 0) {
                        $index++;
                        $tree[$index] = $v;
                    } elseif ($v['node'] == 1) {
                        $v['leaf'] = true;
                        $tree[$index]['children'][] = $v;
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

    public function chart($data_referrer = "") {
        $this->load->model('chart_model', 'chart');

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if ($data_referrer != "") {
                    $this->chart->set_data_referrer(json_decode(urldecode($data_referrer)));
                }
                $data = $this->chart->get();                
                $xAxis = array();
                $note = array();
                $yAxis = array();
                foreach ($data as $k => $v) {
                    if (!in_array($v['dates'], $xAxis)) {
                        $xAxis[] = $v['dates'];
                        $note[] = $v['event_note'];
                        $yAxis['Non_Organic_Install'][] = (int) $v['non_organic_install'];
                    }
                    $yAxis[$v['series']][] = (int) $v['install'];
                }
                echo json_encode(
                        array('data' => $data,
                            'start_date' => $this->chart->get_start_date(),
                            'end_date' => $this->chart->get_end_date(),
                            'xAxis' => $xAxis,
                            'note' => $note,
                            'yAxis' => $yAxis,
                            'text' => '.',
                            'success' => true,
                            'method' => 'GET')
                );
                break;
            default :
                echo json_encode(
                        array('data' => null, 'text' => 'Unsupported Request Method', 'success' => FALSE)
                );
        }
    }

}
