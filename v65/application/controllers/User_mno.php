<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_mno extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('LoginModel');
        $this->load->model('user_mnoModel');
    }
 
    public function index() {
        json_output(400, array(
            'message' => 'Bad request.'
        ));
    }
    
    // User MNO
    public function user_mno1(){
        $method = $_SERVER['REQUEST_METHOD'];
        if($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
                ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                $receiver_id = 27919;
                $invoice_no = 20190705142153;
                $notification_type = 1;
                $img = "";
                $mno_orders_id = 89;
                $title = "Order received";
                $msg = "Please accept the order";
                
                    $res = $this->user_mnoModel->user_mno1($receiver_id,$invoice_no,$mno_orders_id, $notification_type, $img, $title, $msg);
                    // print_r($res); die();
                 
                simple_json_output($res);
            }
        }   
    }
    
    
    // User MNO
    public function mno_order_notification_for_user(){
        $method = $_SERVER['REQUEST_METHOD'];
        if($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
                ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                $receiver_id = 38334;
                $invoice_no = 20190705142153;
                $notification_type = 16;
                $img = "";
                $mno_orders_id = 89;
                $title = "finding night owl";
                $msg = "finding night owl to accept the order...";
                
                    $res = $this->user_mnoModel->mno_order_notification_for_user($receiver_id,$invoice_no,$mno_orders_id, $notification_type, $img, $title, $msg);
                    // print_r($res); die();
                
                simple_json_output($res);
            }
        }   
    }
    
    // order_tracking_mno
      public function order_tracking_mno() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['invoice_no'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter user_id and invoice_no');
                    } else {
                        $user_id = $params['user_id']; 
                        $invoice_no = $params['invoice_no']; 
                        
                        $data =  array();
                        $res = $this->user_mnoModel->order_tracking_mno($user_id, $invoice_no);
                        $data = $res['data'];
                        if($res['status'] == 1){
                            $totalMsgs = sizeof($data['tracker']);
                            $latestMsg = $data['tracker'][$totalMsgs - 1];
                            $msg = $latestMsg['message'];
                            $resp = array('status' => 200, 'message' => $msg,  'data' => $data);
                        } else if($res['status'] == 2){
                            $resp = array('status' => 400, 'message' => 'This order is not given cutomer');
                        } else if($res['status'] == 3){
                            $resp = array('status' => 200, 'message' => 'Waiting for night owl to accept the order', 'data' => $data);
                        } else {
                            $resp = array('status' => 400, 'message' => 'something went wrong');
                        }
                    }
                   simple_json_output($resp);
                }
            }
        }
    }
}