<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Partnermno extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('LoginModel');
        $this->load->model('PartnermnoModel');
    }

    public function index() {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }


    public function sendotp() {
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter fields'
                    );
                } else {
                    $phone = $params['phone'];
                    $resp = $this->PartnermnoModel->sendotp($phone);
                }
                otp_json_output($resp);
            }
        }
    }

    public function login() {
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if(array_key_exists('phone',$params)){
                    $phone = $params['phone'];
                } else {
                    $phone = "";
                }
                if(array_key_exists('token',$params)){
                    $token = $params['token'];
                } else {
                    $token = "";
                }
                if(array_key_exists('agent',$params)){
                    $agent = $params['agent'];
                } else {
                    $agent = "";
                }
                if(array_key_exists('password',$params)){
                   $password = $params['password']; 
                } else {
                    $password = "";
                }
                
                
                
                if ($phone == "" || $token == "" || $agent == "" || $password == "" ) {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter phone, token => fcm, agent => android / ios, password'
                    );
                } else {
                    
                    $phone = $params['phone'];
                    $token = $params['token'];
                    $agent = $params['agent'];
                    $password = $params['password'];
                    
                    $resp = $this->PartnermnoModel->login($phone, $token, $agent, $password);
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    public function update_token() {
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if(array_key_exists('mno_id',$params)){
                    $mno_id = $params['mno_id'];
                } else {
                    $mno_id = "";
                }
                if(array_key_exists('token',$params)){
                    $token = $params['token'];
                } else {
                    $token = "";
                }
                
                
                
                if ($mno_id == "" || $token == "" ) {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id, token => fcm'
                    );
                } else {
                    
                    
                    $resp = $this->PartnermnoModel->update_token($mno_id, $token);
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    public function forget_password() {
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if(array_key_exists('phone',$params)){
                    $phone = $params['phone'];
                } else {
                    $phone = "";
                }
                
                
                if ($phone == "") {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter phone'
                    );
                } else {
                    
                    
                    
                    $resp = $this->PartnermnoModel->forget_password($phone);
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    public function verify_otp() {
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if(array_key_exists('phone',$params)){
                    $phone = $params['phone'];
                } else {
                    $phone = "";
                }
                
                if(array_key_exists('otp',$params)){
                    $otp = $params['otp'];
                } else {
                    $otp = "";
                }
                
                
                if ($phone == "" || $otp == "") {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter phone and otp'
                    );
                } else {
                    
                    
                    
                    $resp = $this->PartnermnoModel->verify_otp($phone,$otp);
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    public function change_password() {
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if(array_key_exists('phone',$params)){
                    $phone = $params['phone'];
                } else {
                    $phone = "";
                }
                
                if(array_key_exists('password',$params)){
                    $password = $params['password'];
                } else {
                    $password = "";
                }
                
                if(array_key_exists('confirm_password',$params)){
                    $confirmPassword = $params['confirm_password'];
                } else {
                    $confirmPassword = "";
                }
                
                
                if ($phone == "" || $password == "" || $confirmPassword == "") {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter phone, password and confirm_password'
                    );
                } else {
                    
                    $resp = $this->PartnermnoModel->change_password($phone,$password,$confirmPassword);
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    public function status_online_offline() {
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if(array_key_exists('mno_id',$params)){
                    $mno_id = $params['mno_id'];
                } else {
                    $mno_id = "";
                }
                
                if(array_key_exists('status',$params)){
                    $status = $params['status'];
                } else {
                    $status = "";
                }
                
                
                if ($mno_id == "" || $status == "" || ($status != "online" && $status != "offline" )) {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id and status. Status should be online of offline'
                    );
                } else {
                    
                    $result = $this->PartnermnoModel->status_online_offline($mno_id, $status);
                    
                    if ($result == 1) {
            
                        $resp = array(
                            'status' => 200,
                            'message' => 'success'
                        );
                    } else if ($result == 2) {
            
                        $resp = array(
                            'status' => 400,
                            'message' => 'Something went wrong, please try again'
                        );
                    }else if ($result == 3) {
            
                        $resp = array(
                            'status' => 400,
                            'message' => 'User not found'
                        );
                    }else if ($result == 4) {
            
                        $resp = array(
                            'status' => 400,
                            'message' => 'No offline record of last logged in'
                        );
                    }else if ($result == 5) {
            
                        $resp = array(
                            'status' => 400,
                            'message' => 'No online record found'
                        );
                    } else {
                        $resp =  array(
                            'status' => '400',
                            'message' => 'Something went wrong'
                        );
                    }
                    
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    public function current_location() {
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if(array_key_exists('mno_id',$params)){
                    $mno_id = $params['mno_id'];
                } else {
                    $mno_id = "";
                }
                
                
                if(array_key_exists('lat',$params)){
                    $lat = $params['lat'];
                } else {
                    $lat = "";
                }
                
                if(array_key_exists('lng',$params)){
                    $lng = $params['lng'];
                } else {
                    $lng = "";
                }
                
                
                if ($mno_id == "" || $lat == "" || $lng == "") {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id, lat and lng'
                    );
                } else {
                    
                    $response = $this->PartnermnoModel->current_location($mno_id, $lat, $lng);
                    
                        if($response == true){
                            $resp = array(
                            'status' => 200,
                            'message' => 'success'
                        );
                    } else {
                        $resp = array(
                            'status' => 401,
                            'message' => 'Something went wrong, please try again'
                        );
                    }
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    public function order_list() {
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                if(array_key_exists('mno_id',$params)){
                    $mno_id = $params['mno_id'];
                } else {
                    $mno_id = "";
                }
                
                if ($mno_id == "") {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id'
                    );
                } else {
                    
                    $resp = $this->PartnermnoModel->order_list($mno_id);
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    
    
    
}
