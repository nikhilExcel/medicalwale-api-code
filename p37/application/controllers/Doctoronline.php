<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Doctoronline extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }

     public function doctor_online() {
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
                    if ($params['doctor_id'] == "" || $params['hours'] == "" ||  $params['days']=="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id=$params['doctor_id'];
                        $hours=$params['hours'];
                        $day=$params['days'];
                        $chat=$params['chat'];
                        $video=$params['video'];
                        $chat_fee=$params['chat_fee'];
                        $video_fee=$params['video_fee'];
                        $resp = $this->DoctoronlineModel->doctor_online($doctor_id,$hours,$chat,$video,$chat_fee,$video_fee,$day);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function doctor_online_user() {
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
                    if ($params['doctor_id'] == "" || $params['user_id'] == "" || $params['fee']=="" || $params['online_type']=="" || $params['transaction']=="" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id=$params['doctor_id'];
                        $user_id=$params['user_id'];
                        $fee=$params['fee'];
                        $online_type=$params['online_type'];
                        $transaction=$params['transaction'];
                        $resp = $this->DoctoronlineModel->doctor_online_user($doctor_id,$user_id,$fee,$online_type,$transaction);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
     public function doctor_online_list() {
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
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id=$params['user_id'];
                       
                        $resp = $this->DoctoronlineModel->doctor_online_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function doctor_offline() {
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
                    if ($params['doctor_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id=$params['doctor_id'];
                      
                        $resp = $this->DoctoronlineModel->doctor_offline($doctor_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function doctor_complete() {
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
                    if ($params['doctor_id'] == "" || $params['booking_id']=="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id=$params['doctor_id'];
                        $booking_id=$params['booking_id'];
                      
                        $resp = $this->DoctoronlineModel->doctor_complete($doctor_id,$booking_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
}
