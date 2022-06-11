<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }

    public function get_api_version() {
        /*$api_url = "http://sandboxapi.medicalwale.com/v52/";*/
      $api_url = "https://live.medicalwale.com/v69/";
        return $api_url;
    }
     public function call_api($data,$u) 
     {
        $get_api_version = $this->get_api_version();
        $url = $get_api_version.$u;
        $data = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        @curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER,['User-ID:1','Authorizations:25iwFyq/LSO1U','Client-Service:frontend-client','Auth-Key:medicalwalerestapi']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result ;
     }        

    public function ledger_page_options() {
        $this->load->model('DoctoronlineModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctoronlineModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->DoctoronlineModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id=$params['user_id'];
                        $vendor_type=$params['vendor_type'];
                        $apiData['user_id'] = $user_id;
                        $apiData['vendor_type'] = $vendor_type;
                        $url = "Ledger/ledger_page_options";
                        $resp1 = $this->call_api($apiData, $url);
                         $resp = json_decode($resp1);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function get_ledger() {
        $this->load->model('DoctoronlineModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctoronlineModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->DoctoronlineModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] =="" || $params['page_no'] =="" || $params['per_page'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                      
                        $apiData['user_id'] = $params['user_id'];
                        $apiData['listing_id'] = $params['listing_id'];
                        $apiData['page_no'] = $params['page_no'];
                        $apiData['per_page'] = $params['per_page'];
                        $apiData['search'] = $params['search'];
                        $url = "Ledger/get_ledger";
                        $resp1 = $this->call_api($apiData, $url);
                        $resp = json_decode($resp1);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function get_total_bachat() {
        $this->load->model('DoctoronlineModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctoronlineModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->DoctoronlineModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['page'] =="" || $params['per_page'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                      
                        $apiData['user_id'] = $params['user_id'];
                       
                        $apiData['page'] = $params['page'];
                        $apiData['per_page'] = $params['per_page'];
                       
                        $url = "Cash_cheque/get_total_bachat";
                        $resp1 = $this->call_api($apiData, $url);
                        //print_r($resp1);
                        $resp = json_decode($resp1);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function get_points() {
        $this->load->model('DoctoronlineModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctoronlineModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->DoctoronlineModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['vendor_type'] =="" || $params['page_no'] =="" || $params['per_page'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                      
                        $apiData['user_id'] = $params['user_id'];
                        $apiData['vendor_type'] = $params['vendor_type'];
                        $apiData['page_no'] = $params['page_no'];
                        $apiData['per_page'] = $params['per_page'];
                        $apiData['search'] = $params['search'];
                        $url = "Ledger/get_points";
                        $resp1 = $this->call_api($apiData, $url);
                        $resp = json_decode($resp1);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
}
