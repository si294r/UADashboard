<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subhome4 extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION['signin'])) {
            redirect('signin');
        }
    }

    public function index($referrer_name, $campaign_name) {
        $_SESSION['referrer_name4'] = $referrer_name;
        $_SESSION['campaign_name4'] = $campaign_name;
        $this->load->view('subhome4_view', array('referrer_name' => $referrer_name, 'campaign_name' => $campaign_name));
    }

    public function grid($tipe = "", $start_date = "", $end_date = "") {
        $this->load->model('subgrid4_model', 'grid');

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
                echo json_encode(
                        array('children' => $data, 'text' => '.', 'success' => true, 'method' => 'GET')
                );
                break;
            default :
                echo json_encode(
                        array('children' => null, 'text' => 'Unsupported Request Method', 'success' => FALSE)
                );
        }
    }

    public function chart($data_AFSiteID = "") {
        $this->load->model('subchart4_model', 'chart');

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if ($data_AFSiteID != "") {
                    $this->chart->set_data_AFSiteID(json_decode(urldecode($data_AFSiteID)));
                }
                $data = $this->chart->get();
                $xAxis = array();
                $note = array();
                $yAxis = array();
                foreach ($data as $k => $v) {
                    if (!in_array($v['dates'], $xAxis)) {
                        $xAxis[] = $v['dates'];
                        $note[] = $v['event_note'];
                    }
//                    $yAxis[$v['series']][] = (int) $v['install'];
                    $yAxis[$v['afsiteid']][] = (int) $v['install'];
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

    public function export_csv() {
        $this->load->model('subgrid4_model', 'grid');
        $data = $this->grid->get();

        $csv = session_id() . '.csv';
        $fp = fopen($csv, 'w');

        $header = array_keys($data[0]);
        fputcsv($fp, $header);
        
        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }

        fclose($fp);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="export.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($csv));
        readfile($csv);

        unlink($csv);
    }

}
