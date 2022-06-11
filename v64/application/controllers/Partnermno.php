<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Partnermno extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('LoginModel');
        $this->load->model('PartnermnoModel');
        $this->load->model('LedgerModel');
        
        // date_default_timezone_set('GMT');
        date_default_timezone_set('Asia/Kolkata');
    } 
 
    public function index() { 
        json_output(400, array(
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
                
                if(array_key_exists('for_excecutor',$params)){
                   $for_excecutor = $params['for_excecutor']; 
                } else {
                    $for_excecutor = 0;
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
                    
                    $resp = $this->PartnermnoModel->login($phone, $token, $agent, $password, $for_excecutor);
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
    
    // pharmacy list
    
    public function pharmacy_list() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    //if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "" || $params['page'] == "") 
                     if ($params['lat'] == "" || $params['lng'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter following fields : mno_id, lat, lng, page_no, per_page');
                    } else {
                        $mno_id = $params['mno_id'];
                        $lat = $params['lat'];
                        $lng = $params['lng'];
                        if(array_key_exists('page_no',$params)){
                            $page_no = $params['page_no'];
                        } else {
                           $page_no = 1;
                        }
                        
                        
                        if(array_key_exists('per_page',$params)){
                            $per_page = $params['per_page'];
                        } else {
                           $per_page = 10;
                        }
                        
                        if(array_key_exists('pharmacy_id',$params)){
                            $pharmacy_id = $params['pharmacy_id'];
                        } else {
                           $pharmacy_id = "";
                        }
                        
                        if(array_key_exists('search',$params)){
                            $search = $params['search'];
                        } else {
                           $search = "";
                        }
                        
                        
                        
                        $res = $this->PartnermnoModel->pharmacy_list($mno_id, $lat, $lng, $page_no, $per_page,$pharmacy_id,$search);
                        if(!empty($res)){
                            $data_count = intval($res['data_count']);
                            $per_page = intval($res['per_page']);
                            $current_page = intval($res['current_page']);
                            $first_page = intval($res['first_page']);
                            $last_page = intval($res['last_page']);
                            $data = $res['stores'];
                            $resp = array('status' => 200, 'message' => 'success', 'data_count' => $data_count, 'per_page' => $per_page, 'current_page' => $current_page, 'first_page' => $first_page, 'last_page' => $last_page ,'data' => $data);
                        } else {
                            $resp = array('status' => 404, 'message' => 'No pharmacy found');
                        }
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
   /* public function customer_call_old() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    $call_to = $params['call_to'];
                    $call_from = $params['call_from'];
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $post_data = array(
                        'from' => $call_from,
                        'to' => $call_to,
                        'CallerId' => "02248931498",
                        'CallType' => "trans" //Can be "trans" for transactional and "promo" for promotional content
                    );
                
                $exotel_sid = "aegishealthsolutions"; // Your Exotel SID - Get it from here: http://my.exotel.in/settings/site#api-settings
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5"; // Your exotel token - Get it from here: http://my.exotel.in/settings/site#api-settings
                 
                $url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Calls/connect";
                 
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                 
                $http_result = curl_exec($ch);
                $error = curl_error($ch);
                $http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
                 
                curl_close($ch);
                 
                print "Response = ".print_r($http_result); 
                
                // $this->load->model('LoginModel');
                $type= "Partnermno/customer_call";
                $this->PartnermnoModel->exotel_call($http_result,$type);
                
                }
            }
        }
     
    }*/
    
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
                
                
                $last = end($this->uri->segments); 
                
                if($last == 1 || $last == 2 || $last == 3){
                    $type = $last;
                } else {
                    $type = null;
                }
                
                
                
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
                    
                    
                if(array_key_exists('page_no',$params)){
                    $page_no = $params['page_no'];
                } else {
                   $page_no = 1;
                }
                
                
                if(array_key_exists('per_page',$params)){
                    $per_page = $params['per_page'];
                } else {
                   $per_page = 10;
                }
                    $res = $this->PartnermnoModel->order_list($mno_id, $page_no, $per_page,$type);
                    
                    $data_count = intval($res['data_count']);
                    $per_page = intval($res['per_page']);
                    $current_page = intval($res['current_page']);
                    $first_page = intval($res['first_page']);
                    $last_page = intval($res['last_page']);
                    $orders = $res['orders'];
                
                
                    if(sizeof($orders) > 0){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data_count' => $data_count,
                            'per_page' => $per_page,
                            'current_page' => $current_page,
                            'first_page' => $first_page,
                            'last_page' => $last_page,
                            'data' => $orders
                        );
                    } else {
                        $resp = array(
                            'status' => 401,
                            'message' => 'No data found'
                        );
                    }
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    
     public function order_accept_reject() {
        
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
                
                if(array_key_exists('invoice_no',$params)){
                    $invoice_no = $params['invoice_no'];
                } else {
                    $invoice_no = "";
                }
                
                if(array_key_exists('accepted',$params)){
                    $accepted = $params['accepted'];
                } else {
                    $accepted = "";
                }
                
                if(array_key_exists('cancel_reason',$params)){
                    $cancel_reason = $params['cancel_reason'];
                    
                } else {
                    $cancel_reason = "";
                }
               
                if ($mno_id == "" ||  $invoice_no == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id and invoice_no '
                    );
                } else {
                    
                    $res = $this->PartnermnoModel->order_accept_reject($mno_id,$invoice_no, $accepted, $cancel_reason,$lat,$lng);
                    if($res ==  1){
                        $resp = array(
                            'status' => 400,
                            'message' => 'Night night owl found',
                        );
                    } else if($res ==  2){
                        $resp = array(
                            'status' => 400,
                            'message' => 'Please send either true or false in accepted',
                        );
                    } else if($res ==  3){
                        $resp = array(
                            'status' => 200,
                            'message' => 'Order rejected',
                        );
                    } else if($res ==  4){
                        $data = array();
                        $data = $this->PartnermnoModel->order_details($mno_id, $invoice_no);
                        if($data['accepted'] == true){
                            $resp = array(
                                'status' => 200,
                                'message' => 'Order accepted',
                                'data' => $data['data']
                            );
                        } else {
                            $resp = array(
                                'status' => 400,
                                'message' => $data['message']
                                
                            );
                        }
                        
                    } else if($res ==  5){
                        
                        $resp = array(
                            'status' => 400,
                            'message' => 'No such order exists, please check invoice_no again'
                            
                        );
                    }else if($res ==  6){
                        $data = $this->PartnermnoModel->order_details($mno_id, $invoice_no);
                        if($data['accepted'] == true){
                            $resp = array(
                                'status' => 200,
                                'message' => 'Order already accepted',
                                'data' => $data['data']
                            );
                        } else {
                            $resp = array(
                                'status' => 400,
                                'message' => $data['message']
                                
                            );
                        }
                       /* $resp = array(
                            'status' => 400,
                            'message' => 'Order is already accepted'
                            
                        );*/
                    }else if($res ==  7){
                        
                        $resp = array(
                            'status' => 400,
                            'message' => 'You have already reject this order'
                            
                        );
                    } else if($res ==  8){
                        $resp = array(
                            'status' => 400,
                            'message' => 'Order cancel reason not found',
                        );
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'Something went wrong, please try again'
                        );
                    }
                }
               
                simple_json_output($resp);
            }
        }
    }
    
     public function order_details() {
        
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
                
                if(array_key_exists('invoice_no',$params)){
                    $invoice_no = $params['invoice_no'];
                } else {
                    $invoice_no = "";
                }
                
                if ($invoice_no == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter invoice_no'
                    );
                } else {
                    
                     $data = $this->PartnermnoModel->order_details($mno_id , $invoice_no);
                     if($data['accepted'] == true){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' => $data['data']
                        ); 
                     } else {
                        $resp = array(
                            'status' => 400,
                          //  'message' => 'failed',
                            'message' => $data['message']
                        ); 
                     }
                        
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    public function add_new_pharmacy() {
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
                
                if(array_key_exists('medical_name',$params)){
                    $medical_name = $params['medical_name'];
                } else {
                    $medical_name = "";
                }
                
                
                if(array_key_exists('contact_person',$params)){
                    $contact_person = $params['contact_person'];
                } else {
                    $contact_person = "";
                }
                
                if(array_key_exists('contact_no',$params)){
                    $contact_no = $params['contact_no'];
                } else {
                    $contact_no = "";
                }
                
                
                if(array_key_exists('email_id',$params)){
                    $email_id = $params['email_id'];
                } else {
                    $email_id = "";
                }
                
                if(array_key_exists('lat',$params)){
                    $lat = $params['lat'];
                } else {
                    $lat = "";
                }
                
                if(array_key_exists('gst',$params)){
                    $gst = $params['gst'];
                } else {
                    $gst = "";
                }
                
                if(array_key_exists('lng',$params)){
                    $lng = $params['lng'];
                } else {
                    $lng = "";
                }
                
                if(array_key_exists('map_location',$params)){
                    $map_location = $params['map_location'];
                } else {
                    $map_location = "";
                }
                
                
                
                if(array_key_exists('city',$params)){
                    $city = $params['city'];
                } else {
                    $city = "";
                }
                
                if(array_key_exists('state',$params)){
                    $state = $params['state'];
                } else {
                    $state = "";
                }
                
                if(array_key_exists('pincode',$params)){
                    $pincode = $params['pincode'];
                } else {
                    $pincode = "";
                }
                
                if(array_key_exists('discount',$params) || $params['discount'] != null){
                    $discount = $params['discount'];
                } else {
                    $discount = 0;
                }
                
                if ($mno_id == "" || $medical_name == "" || $contact_person == "" || $contact_no == "" || $lat == "" || $lng == "" || $map_location == "" ||  $city == "" || $state == "" || $pincode == "" || $gst == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id , medical_name, contact_person, contact_no, lat, lng, map_location, city, state, pincode, discount, gst '
                    );
                } else {
                    
                    
                    if(array_key_exists('address1',$params) && !empty($params['address1'])){
                        $address1 = $params['address1'];
                    } else {
                        $address1 = $map_location;
                    }
                    
                    if(array_key_exists('address2',$params)){
                        $address2 = $params['address2'];
                    } else {
                        $address2 = "";
                    }
                    $getDetails = array(
                            'mno_id' => $mno_id,
                            'medical_name' => $medical_name,
                            'contact_person' => $contact_person,
                            'contact_no' => $contact_no,
                            'email_id' => $email_id,
                            'lat' => $lat,
                            'lng' => $lng,
                            'map_location' => $map_location,
                            'address1' => $address1,
                            'address2' => $address2,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'discount' => $discount,
                            'gst' => $gst
                        );
                    $res = $this->PartnermnoModel->add_new_pharmacy($getDetails);
                    // print_r($res); die();
                    if($res['exists'] == 1){
                       /* $resp = array(
                            'status' => 400,
                            'message' => 'Pharmacy already suggested'
                                                    );
                                                    */
                        $resp = array(
                            'status' => 200,
                            'message' => 'duplicate',
                            'data' => $res['phamacy']
                        );
                    } else if($res['insert_id'] > 0 && $res['exists'] == 0){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' => $res['phamacy']
                                                    );
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'something went wrong'
                        );
                    }
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    
    
    public function submit_price(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['mno_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter mno_id'
                        );
                    } else {
                        
                        
                        if(array_key_exists('mno_id',$params) && !empty($params['mno_id'])){
                            $mno_id = $params['mno_id'];
                        } else {
                            $mno_id = "";
                        }
                        
                        if(array_key_exists('order',$params) && !empty($params['order'])){
                             if(array_key_exists('product_order',$params['order']) && !empty($params['order']['product_order'])){
                                 $order_details = $params['order'];
                             } else {
                                  $order_details = "";
                             }
                            
                        } else {
                            $order_details = "";
                        }
                        
                        
                        
                        if(array_key_exists('prescription',$params) && !empty($params['prescription'])){
                            if(array_key_exists('prescription_order',$params['prescription']) && !empty($params['prescription']['prescription_order'])){
                                 $prescription_details = $params['prescription'];
                            } else {
                                $prescription_details = "";
                            }
                           
                        } else {
                            $prescription_details = "";
                        }
                   
                   
                        
                        if( $order_details !="" &&  $prescription_details!=""){
                            $order_id_data = $order_details['order_id']; 
                            $prescription_id_data = $prescription_details['order_id'];  
                            if($order_id_data == "" && $prescription_id_data== ""){
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'please enter Order ID'
                                );
                                simple_json_output($resp);
                            } else {
                                 
                      
                                $resp = $this->PartnermnoModel->order_status_common($mno_id,$order_id_data, $prescription_id_data,$order_details,$prescription_details);
                                simple_json_output($resp);
                            } 
                        } else if($order_details !="" && $prescription_details=="") {
                                //  $product_details_new = json_decode($order_details,TRUE);
                                 $product_details_new = $order_details;
                                 $order_id      = $product_details_new['order_id'];
                             //    $delivery_time = $product_details_new['delivery_time'];
                              //   $order_status  = $product_details_new['order_status'];
                             //    $listing_id    = $product_details_new['listing_id'];
                                 $listing_type  = 44;
                                 $order_data    = $product_details_new['product_order'];
                       
                      if ($order_id == "") {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter order_id in orders'
                            );
                            simple_json_output($resp);
                       } 
                     else 
                      {
                        $resp = $this->PartnermnoModel->order_price($mno_id,$order_id, $listing_type, $order_data);
                        simple_json_output($resp);
                          
                      } 
                    }
                    else if ($order_details =="" && $prescription_details !=""){
                        
                        // $prescription_details_new = json_decode($prescription_details,TRUE);
                        $prescription_details_new = $prescription_details;
                        
                        $order_id           = $prescription_details_new['order_id'];
                      //  $order_status       = "Awaiting Customer Confirmation";
                        //$delivery_time      = "30 mins";
                        $prescription_order = $prescription_details_new['prescription_order'];
                       
                        if ($order_id == "") 
                        {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter order_id in  prescription '
                            );
                            simple_json_output($resp);
                        } 
                        else 
                       {
                        $resp = $this->PartnermnoModel->prescription_price($mno_id,$order_id, $prescription_order);
                        simple_json_output($resp);
                       } 
                    }
                       
                    }
                   
                  
                }
            }
        }
    }  
    
    // suggested_pharmacy_list
    
    public function suggested_pharmacy_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['mno_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter mno_id'
                        );
                    } else {
                        $mno_id = $params['mno_id'];
                        
                        if(array_key_exists('page_no',$params) && $params['page_no'] > 0 ){
                            $page_no = $params['page_no'];
                        }else{
                             $page_no = 1;
                        }
                        
                        if(array_key_exists('per_page',$params) && $params['per_page'] > 0){
                            $per_page = $params['per_page'];
                        }else{
                             $per_page = 10;
                        }
                        
                        if(array_key_exists('gst',$params) && $params['gst'] > 0){
                            $gst = $params['gst'];
                        }else{
                            $gst = "";
                        }
                        
                        
                        if(array_key_exists('lat',$params)){
                            $lat = $params['lat'];
                        }else{
                             $lat = 0;
                        }
                        
                        if(array_key_exists('lng',$params)){
                            $lng = $params['lng'];
                        }else{
                            $lng = 0;
                        }
                        
                        if(array_key_exists('pharmacy_id',$params)){
                            $pharmacy_id = $params['pharmacy_id'];
                        }else{
                            $pharmacy_id = 0;
                        }
                        
                        
                         if(array_key_exists('search',$params)){
                            $search = $params['search'];
                        }else{
                            $search = 0;
                        }
                        
                        $res = $this->PartnermnoModel->suggested_pharmacy_list($mno_id,$page_no,$per_page,$gst,$lat,$lng,$pharmacy_id,$search);
                        
                        if($res['mno_exists'] == 0){
                             $resp = array(
                                'status' => 400,
                                'message' => 'Night night owl found',
                            );
                        } else if($res['mno_exists'] == 1){
                             $resp = array(
                                'status' => 200,
                                'message' => 'success',
                                'data_count' => $res['data_count'],
                                'per_page' => $res['per_page'],
                                'current_page' => $res['current_page'],
                                'first_page' => $res['first_page'],
                                'last_page' => $res['last_page'],
                                'data' => $res['stores']
                            );
                        } else {
                            $resp = array(
                                'status' => 400,
                                'message' => 'Something went wrong',
                            );
                        }
                    }
                    
                    simple_json_output($resp);
                }
            }
        }
        
    }
    
    
    // cancel reason
    
    public function cancel_reason(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if(sizeof($params) > 0){
                        if(array_key_exists('mno_id', $params)){
                            $mno_id = $params['mno_id'];
                        }else{
                             $mno_id = "";
                        }
                        
                        if(array_key_exists('before_accept',$params)){
                            $before_accept = $params['before_accept'];
                        }else{
                            $before_accept = '';
                        }
                        
                        if ( $mno_id == "" ) {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter mno_id and before_accept as true or false'
                            );
                        } else {
                            $mno_id = $params['mno_id'];
                            $before_accept = $params['before_accept'];
                            
                            
                            $res = $this->PartnermnoModel->cancel_reason($mno_id, $before_accept);
                            if($res['type'] ==  'before' || $res['type'] ==  'after'){
                                 $resp = array(
                                    'status' => 200,
                                    'message' => 'success',
                                    'data' => $res['reasons']
                                );
                            } else {
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'Value in before_accept should be either true or false',
                                );
                            }
                        }
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter mno_id and before_accept as true or false'
                        );
                    }
                        
                        
                        
                    
                    
                    simple_json_output($resp);
                }
            }
        }
        
    }
    
    // Order to pharmacy
    public function order_to_pharmacy() {
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
                
                if(array_key_exists('invoice_no',$params)){
                    $invoice_no = $params['invoice_no'];
                } else {
                    $invoice_no = "";
                }
                
                if(array_key_exists('pharmacy_id',$params)){
                    $pharmacy_id = $params['pharmacy_id'];
                } else {
                    $pharmacy_id = 0;
                }
                
                if(array_key_exists('suggested_pharmacy_id',$params)){
                    $suggested_pharmacy_id = $params['suggested_pharmacy_id'];
                } else {
                    $suggested_pharmacy_id = 0;
                }
                
                if ($mno_id == "" || $invoice_no == "" ) {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id and invoice_no'
                    );
                } else {
                    
                    $res = $this->PartnermnoModel->order_to_pharmacy($mno_id, $invoice_no, $pharmacy_id, $suggested_pharmacy_id);
                    if($res['store_exists'] > 0){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' => $res['order_details']
                        );
                    } else {
                        $resp = array(
                            'status' => 401,
                            'message' => 'No store found'
                        );
                    }
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    
   public function assign_mno() {
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
                
                if(array_key_exists('user_id',$params)){
                    $user_id = $params['user_id'];
                } else {
                    $user_id = "";
                }
                
                if(array_key_exists('invoice_no',$params)){
                    $invoice_no = $params['invoice_no'];
                } else {
                    $invoice_no = "";
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
                
                if ($user_id == "" || $invoice_no == ""  || $lat == "" || $lng == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id, pharmacy_id, lat and lng'
                    );
                } else {
                    
                    $res = $this->PartnermnoModel->assign_mno($user_id,$invoice_no,$lat,$lng);
                //     print_r($res); die();
                    if($res['res'] == 1 && $res['mno_id'] > 0 ){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'description' => 'Night owl assigned',
                            'data' => $res['data']
                           
                        );
                    } else if($res['res'] == 1 && ($res['mno_id'] == 0 || $res['mno_id'] == "")){
                        $resp = array(
                            'status' => 201,
                            'message' => 'success', // do not change msgs
                             'description' => 'Night owl not available, Once any MNO will get free we will redirect order.'
                           
                        );
                    }else if($res['res'] == 2){
                        // MNO delivery not available
                        $resp = array(
                            'status' => 201,
                            'message' => 'failed', // do not change msgs
                            'description' => $res['description']
                           
                        );
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'No data found'
                        );
                    }
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    public function dashboard1() {
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
                
               
                if(array_key_exists('mno_id',$params) && !empty($params['mno_id'])){
                    $mno_id = $params['mno_id'];
                } else {
                    $mno_id = "";
                }
            
                if(array_key_exists('from_date',$params)){
                    $from_date = $params['from_date'];
                } else {
                    $from_date = "";
                }
                
                if(array_key_exists('to_date',$params)){
                    $to_date = $params['to_date'];
                } else {
                    $to_date = "";
                }
                
                if ($mno_id == "" ) {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id'
                    );
                } else {
                     $reqData['mno_id'] = $mno_id;
                     $reqData['from_date'] = $from_date;
                     $reqData['to_date'] = $to_date;
                     
                     
                    $res = $this->PartnermnoModel->dashboard1($reqData);
                    //print_r($res); die();
                    if($res > 0){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' => $res
                           
                        );
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'No data found'
                        );
                    }
                }
               
                simple_json_output($resp);
            }
        }
    }
    
     public function dashboard() {
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
                
               
                if(array_key_exists('mno_id',$params) && !empty($params['mno_id'])){
                    $mno_id = $params['mno_id'];
                } else {
                    $mno_id = "";
                }
            
                if(array_key_exists('per_page',$params)){
                    $per_page = $params['per_page'];
                } else {
                    $per_page = 10;
                }
                
                if(array_key_exists('page_no',$params)){
                    $page_no = $params['page_no'];
                } else {
                    $page_no = 1;
                }
                
                if(array_key_exists('from_date',$params)){
                    $from_date = $params['from_date'];
                } else {
                    $from_date = "";
                }
                
                if(array_key_exists('to_date',$params)){
                    $to_date = $params['to_date'];
                } else {
                    $to_date = "";
                }
                
                if ($mno_id == "" ) {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id'
                    );
                } else {
                     $reqData['mno_id'] = $mno_id;
                     $reqData['per_page'] = $per_page;
                     $reqData['page_no'] = $page_no;
                     $reqData['from_date'] = $from_date;
                     $reqData['to_date'] = $to_date;
                     
                     if($from_date != ""  && $to_date != "" && $from_date > $to_date){
                        
                          $resp = array(
                                'status' => 400,
                                'message' => 'from date should be less than to date'
                            );
                     } else {
                           $resp = $this->PartnermnoModel->dashboard($reqData);
                           
                           $res = $resp['ordersFinal'];
                        //print_r($res); die();
                        if($res > 0){
                            $resp = array(
                                'status' => 200,
                                'message' => 'success',
                                'start_date' => $resp['mno_start_date'],
                                'total_pages' => $resp['total_pages'],
                                'data' => $res
                               
                            );
                        } else {
                            $resp = array(
                                'status' => 400,
                                'message' => 'No data found'
                            );
                        }
                     }

                     
                     
                   
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    
    public function order_reject() {
        
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
                
                if(array_key_exists('invoice_no',$params)){
                    $invoice_no = $params['invoice_no'];
                } else {
                    $invoice_no = "";
                }
                
                if(array_key_exists('reject',$params)){
                    $reject = $params['reject'];
                } else {
                    $reject = "";
                }
                
             
                
                if ($mno_id == "" ||  $invoice_no == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id and invoice_no '
                    );
                } else {
                    
                    $res = $this->PartnermnoModel->order_reject($mno_id,$invoice_no, $reject);
                    if($res ==  1){
                        $resp = array(
                            'status' => 400,
                            'message' => 'Night night owl found',
                        );
                    } else if($res ==  3){
                        $resp = array(
                            'status' => 400,
                            'message' => 'Order rejected',
                        );
                    }else if($res ==  5){
                        
                        $resp = array(
                            'status' => 400,
                            'message' => 'No such order exists, please check invoice_no again'
                            
                        );
                    }else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'Something went wrong, please try again'
                        );
                    }
                }
               
                simple_json_output($resp);
            }
        }
    }
    
    
    // order picked up i.e out for delivery
    // 11th october 2019 10:00am added $paid_to_pharmacy,$received_from_pharmacy. WRT this we will do entries in db 
    public function order_pickedup() {
        
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
                
                if(array_key_exists('invoice_no',$params)){
                    $invoice_no = $params['invoice_no'];
                } else {
                    $invoice_no = "";
                }
                
                
                // new fields wrt ledger 
                
                if(array_key_exists('paid_to_pharmacy',$params)){
                    $paid_to_pharmacy = $params['paid_to_pharmacy'];
                } else {
                    $paid_to_pharmacy = 0;
                }
                
                if(array_key_exists('paid_to_pharmacy_comment',$params)){
                    $paid_to_pharmacy_comment = $params['paid_to_pharmacy_comment'];
                } else {
                    $paid_to_pharmacy_comment = "";
                }
                
                if(array_key_exists('received_from_pharmacy',$params)){
                    $received_from_pharmacy = $params['received_from_pharmacy'];
                } else {
                    $received_from_pharmacy = 0;
                }
                
                if(array_key_exists('received_including_delivery_charges',$params)){
                    $received_including_delivery_charges = $params['received_including_delivery_charges'];
                } else {
                    $received_including_delivery_charges = false;
                }
                
                if(array_key_exists('received_from_pharmacy_comment',$params)){
                    $received_from_pharmacy_comment = $params['received_from_pharmacy_comment'];
                } else {
                    $received_from_pharmacy_comment = "";
                }
                
                if(array_key_exists('paid_payment_method',$params)){
                    $paid_payment_method = $params['paid_payment_method'];
                } else {
                    $paid_payment_method = "";
                }
                
                if(array_key_exists('received_payment_method',$params)){
                    $received_payment_method = $params['received_payment_method'];
                } else {
                    $received_payment_method = "";
                }
                
                
                
                
                 if ($mno_id == "" || $invoice_no == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id, order_pickedupad true or false and invoice_no '
                    );
                } else {
                     
                    $res = $this->PartnermnoModel->order_pickedup($mno_id,$invoice_no,$paid_to_pharmacy,$paid_to_pharmacy_comment,$received_from_pharmacy,$received_from_pharmacy_comment,$received_including_delivery_charges,$paid_payment_method, $received_payment_method);
                    
                    if($res == 1){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success'
                        );
                    } else if($res == 2) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'This order is not assign to given mno, Please check invoice_no and mno_id'
                        );
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'Something went wrong, please try again'
                        );
                    }
                    
                }
                
                simple_json_output($resp);
                
                
            }
        }
    }
    
    // order_delivered
    public function order_delivered() {
        
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
                /*if(array_key_exists('order_delivered',$params)){
                    $order_delivered = $params['order_delivered'];
                } else {
                    $order_delivered = 0;
                }*/
                
                if(array_key_exists('invoice_no',$params)){
                    $invoice_no = $params['invoice_no'];
                } else {
                    $invoice_no = "";
                }
                
                 if ($mno_id == "" || $invoice_no == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id and invoice_no '
                    );
                } else {
                    
                    $res = $this->PartnermnoModel->order_delivered($mno_id,$invoice_no);
                
                    if($res == 1){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success'
                        );
                    } else if($res == 2) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'This order is not assign to given mno or already delivered'
                        );
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'Something went wrong, please try again'
                        );
                    }
                    
                }
                
                simple_json_output($resp);
                
                
            }
        }
    }
    
    
    // out for pickup - on the way to pickup
    
    public function mno_out_for_pickup() {
        
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
                /*if(array_key_exists('out_for_pickup',$params)){
                    $out_for_pickup = $params['out_for_pickup'];
                } else {
                    $out_for_pickup = 0;
                }*/
                
                if(array_key_exists('invoice_no',$params)){
                    $invoice_no = $params['invoice_no'];
                } else {
                    $invoice_no = "";
                }
                
                 if ($mno_id == "" || $invoice_no == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id and invoice_no '
                    );
                } else {
                    
                    $res = $this->PartnermnoModel->mno_out_for_pickup($mno_id,$invoice_no);
                    
                    if($res == 1){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success'
                        );
                    } else if($res == 2) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'This order is not assign to given mno, Please check invoice_no and mno_id'
                        );
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'Something went wrong, please try again'
                        );
                    }
                    
                }
                
                simple_json_output($resp);
                
                
            }
        }
    }
   
    
    // at_store
    public function at_store() {
        
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
                /*if(array_key_exists('at_store',$params)){
                    $at_store = $params['at_store'];
                } else {
                    $at_store = 0;
                }*/
                
                if(array_key_exists('invoice_no',$params)){
                    $invoice_no = $params['invoice_no'];
                } else {
                    $invoice_no = "";
                }
                
                 if ($mno_id == "" || $invoice_no == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id  and invoice_no '
                    );
                } else {
                    
                    $res = $this->PartnermnoModel->at_store($mno_id,$invoice_no);
                    
                    if($res == 1){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success'
                        );
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'Something went wrong, please try again'
                        );
                    }
                    
                }
                
                simple_json_output($resp);
                
                
            }
        }
    }
    
    
    // at_doorstep
    public function at_doorstep() {
        
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
                /*if(array_key_exists('at_doorstep',$params)){
                    $at_doorstep = $params['at_doorstep'];
                } else {
                    $at_doorstep = 0;
                }*/
                
                if(array_key_exists('invoice_no',$params)){
                    $invoice_no = $params['invoice_no'];
                } else {
                    $invoice_no = "";
                }
                
                 if ($mno_id == "" || $invoice_no == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id, at_doorstep true or false and invoice_no '
                    );
                } else {
                    
                    $res = $this->PartnermnoModel->at_doorstep($mno_id,$invoice_no);
                    
                    if($res == 1){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success'
                        );
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'Something went wrong, please try again'
                        );
                    }
                    
                }
                
                simple_json_output($resp);
                
                
            }
        }
    }
    
    
    // payment_accepted
    public function payment_accepted() {
        
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
                
             //   print_r($params); die();
                if(array_key_exists('mno_id',$params)){
                    $mno_id = $params['mno_id'];
                } else {
                    $mno_id = "";
                }
                if(array_key_exists('amount',$params)){
                    $amount = $params['amount'];
                } else {
                    $amount = 0;
                }
                
                if(array_key_exists('invoice_no',$params)){
                    $invoice_no = $params['invoice_no'];
                } else {
                    $invoice_no = "";
                }
                
                if(array_key_exists('payment_id',$params)){
                    $payment_id = $params['payment_id'];
                } else {
                    $payment_id = "";
                }
                
                 if ($mno_id == "" || $invoice_no == "") {
                    $response = array(
                        'status' => 400,
                        'message' => 'please enter mno_id,  payment_id , amount and invoice_no '
                    );
                } else {
                    
                    $res = $this->PartnermnoModel->payment_accepted($mno_id,$invoice_no, $payment_id, $amount);
                    
                    if($res == 1){
                        $response = array(
                            'status' => 200,
                            'message' => 'success'
                        );
                    }else if($res == 2){
                        $response = array(
                            'status' => 400,
                            'message' => 'Order already completed'
                        );
                    } else if($res == 4){
                        $response = array(
                            'status' => 201,
                            'message' => 'please send sub_type id of payment method'
                        );
                    } else if($res == 5){
                        $response = array(
                            'status' => 400,
                            'message' => 'No payment method found'
                        );
                    } else {
                        $response = array(
                            'status' => 400,
                            'message' => 'Something went wrong, please try again'
                        );
                    }
                }
                simple_json_output($response);
            }
        }
    }
    
    
    
     public function check_pending_order() {
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
                
                if ($mno_id == ""   || $lat == "" || $lng == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter mno_id, lat and lng'
                    );
                } else {
                    
                    $res = $this->PartnermnoModel->check_pending_order($mno_id,$lat,$lng);
                    //print_r($res); die();
                    if($res ==  1){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success'
                           
                        );
                    } else if ($res == 3){
                        $resp = array(
                            'status' => 400,
                            'message' => 'No night owl found'
                           
                        );
                    }else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'No data found'
                        );
                    }
                }
               
                simple_json_output($resp);
            }
        }
    }
  
//   send_gcm_notification_to_mno
     public function send_gcm_notification_to_mno() {
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
                // for testing
                
                if(array_key_exists('receiver_id',$params)){
                    $receiver_id = $params['receiver_id'];
                } else {
                    $receiver_id = "";
                }
                
                if(array_key_exists('invoice_no',$params)){
                    $invoice_no = $params['invoice_no'];
                } else {
                    $invoice_no = "";
                }
                
                if(array_key_exists('notification_type',$params)){
                    $notification_type = $params['notification_type'];
                } else {
                    $notification_type = "";
                }
                
                if(array_key_exists('img',$params)){
                    $img = $params['img'];
                } else {
                    $img = "";
                }
                
                if(array_key_exists('mno_order_id',$params)){
                    $mno_order_id = $params['mno_order_id'];
                } else {
                    $mno_order_id = "";
                }
                
                
                if(array_key_exists('title',$params)){
                    $title = $params['title'];
                } else {
                    $title = "";
                }
                
                if(array_key_exists('msg',$params)){
                    $msg = $params['msg'];
                } else {
                    $msg = "";
                }
                
                
                
                // $receiver_id = 38247;
                // $invoice_no = 20190817160000;
                // $notification_type = 7;
                // $img = "";
                // $mno_order_id = 153;
                // $title = "ABC";
                // $msg = "XYZ";
                
                if($receiver_id == "" || $invoice_no  == "" || $notification_type  == "" ||  $mno_order_id  == "" || $title  == "" || $msg  == "" ){
                    $resp = array(
                            'status' => 400,
                            'message' => 'please enter receiver_id, invoice_no , notification_type ,mno_order_id , title, msg'
                        );
                } else {
                    $res = $this->PartnermnoModel->send_gcm_notification_to_mno($receiver_id,$invoice_no,$mno_order_id, $notification_type, $img, $title, $msg);
                    //print_r($res); die();
                    
                   $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' => $res
                        );
                }
                 simple_json_output($resp);
                
            }
        }
    }
    
    // payment_methods
    
    public function payment_methods(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if(sizeof($params) > 0){
                        if(array_key_exists('mno_id', $params)){
                            $mno_id = $params['mno_id'];
                        }else{
                             $mno_id = "";
                        }
                        
                        
                        
                        if ( $mno_id == "" ) {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter mno_id'
                            );
                        } else {
                            $mno_id = $params['mno_id'];
                            
                            
                            $res = $this->PartnermnoModel->payment_methods($mno_id);
                            
                            
                            if(sizeof($res['payment_method']) > 0){
                                 $resp = array(
                                    'status' => 200,
                                    'message' => 'success',
                                    'data' => $res['payment_method']
                                );
                            } else {
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'Something went wrong, please try again',
                                );
                            }
                        }
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter mno_id'
                        );
                    }
                        
                        
                        
                    
                    
                    simple_json_output($resp);
                }
            }
        }
        
    }
    
    
     // cancel_order
    
    public function cancel_order(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if(sizeof($params) > 0){
                        if(array_key_exists('mno_id', $params)){
                            $mno_id = $params['mno_id'];
                        }else{
                             $mno_id = "";
                        }
                        
                        if(array_key_exists('reason_id', $params)){
                            $reason_id = $params['reason_id'];
                        }else{
                             $reason_id = "";
                        }
                        
                        if(array_key_exists('invoice_no', $params)){
                            $invoice_no = $params['invoice_no'];
                        }else{
                             $invoice_no = "";
                        }
                        
                      
                        
                        if ( $mno_id == "" || $reason_id == "" || $invoice_no == "") {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter mno_id , invoice_no and  reason_id'
                            );
                        } else {
                            $mno_id = $params['mno_id'];
                            $res = $this->PartnermnoModel->cancel_order($mno_id, $reason_id,$invoice_no);
                            if($res == 1){
                                 $resp = array(
                                    'status' => 400,
                                    'message' => 'No mno found'
                                );
                            } else if($res == 2){
                                 $resp = array(
                                    'status' => 400,
                                    'message' => 'No reason found'
                                );
                            } else if($res == 3){
                                 $resp = array(
                                    'status' => 400,
                                    'message' => 'Order '.$invoice_no. ' is not assigned to you'
                                );
                            }  else if($res == 4){
                                 $resp = array(
                                    'status' => 200,
                                    'message' => 'Order '.$invoice_no. ' successfully cancelled'
                                );
                            }  else {
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'Something went wrong, please try again',
                                );
                            }
                        }
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter mno_id, invoice_no and  reason_id'
                        );
                    }
                        
                    simple_json_output($resp);
                }
            }
        }
        
    }
    
    // get ongoing orders
    
     public function get_ongoing_order(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if(sizeof($params) > 0){
                        if(array_key_exists('mno_id', $params)){
                            $mno_id = $params['mno_id'];
                        }else{
                             $mno_id = "";
                        }
                        
                       
                        
                        if ( $mno_id == "" ) {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter mno_id '
                            );
                        } else {
                            $mno_id = $params['mno_id'];
                            $res = $this->PartnermnoModel->get_ongoing_order($mno_id);
                            if(sizeof($res) > 0){
                                $resp = array(
                                    'status' => 200,
                                    'message' => 'success ',
                                    'data' => $res['data']
                                ); 
                            } else {
                                $resp = array(
                                    'status' => 404,
                                    'message' => 'No data found '
                                 
                                ); 
                            }
                        }  
                        simple_json_output($resp);
                    } 
                        
                    
                }
            }
        }
        
    }
    
    
    public function pharmacy_didnot_respond(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if(sizeof($params) > 0){
                        if(array_key_exists('mno_id', $params)){
                            $mno_id = $params['mno_id'];
                        }else{
                             $mno_id = "";
                        }
                        
                        if(array_key_exists('invoice_no', $params)){
                            $invoice_no = $params['invoice_no'];
                        }else{
                             $invoice_no = "";
                        }
                        
                        if ( $mno_id == "" || $invoice_no =="") {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter mno_id and $invoice_no '
                            );
                        } else {
                            $mno_id = $params['mno_id'];
                            $res = $this->PartnermnoModel->pharmacy_didnot_respond($mno_id,$invoice_no);
                            if($res == 1 ){
                                $resp = array(
                                    'status' => 200,
                                    'message' => 'success '
                                  
                                ); 
                            } else {
                                $resp = array(
                                    'status' => 404,
                                    'message' => 'Not assing to this mno or order is completed'
                                 
                                ); 
                            }
                        }  
                        simple_json_output($resp);
                    } 
                        
                    
                }
            }
        }
        
    }
    
      public function responce_behalf_of_user(){
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                   
                   if(array_key_exists('mno_id',$params)){
                         $mno_id = $params['mno_id'];
                    } else {
                        $mno_id = "";
                    }
                   
                   
                   if(array_key_exists('pharmacy_id',$params)){
                         $pharmacy_id = $params['pharmacy_id'];
                    } else {
                        $pharmacy_id = "";
                    }
                   
                    if(array_key_exists('invoice_no',$params)){
                         $invoice_no = $params['invoice_no'];
                    } else {
                        $invoice_no = "";
                    }
                   
                    if(array_key_exists('confirm',$params)){
                        $confirm = $type = $params['confirm'];
                    } else {
                        $confirm = $type = true;
                    }
                    
                    if(array_key_exists('cancel_reason',$params)){
                        $cancel_reason = $params['cancel_reason'];
                    } else {
                        $cancel_reason = "";
                    }
                    
                    if(array_key_exists('payment_method',$params)){
                         $payment_method = $params['payment_method'];
                    } else {
                        $payment_method = "";
                    }
                    
                    
                    if ( $invoice_no == "" || $mno_id == "" || $pharmacy_id == "" || ($confirm == true && $payment_method == "") || ($confirm == false && $cancel_reason == "")) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields',
                            'description' =>   'required fields are pharmacy_id, invoice_no and mno_id. if confirm is true then send payment_method and if confirm is false then send cancel_reason'
                        );
                    } else {
                        $resp = $this->PartnermnoModel->responce_behalf_of_user($mno_id, $invoice_no, $confirm, $cancel_reason, $payment_method);
                    }
                    simple_json_output($resp);
                }
            }
        }
    } 
    
    
    // vendor_payment_option
    public function vendor_payment_option(){
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                   
                   if(array_key_exists('pharmacy_id',$params)){
                         $pharmacy_id = $params['pharmacy_id'];
                    } else {
                        $pharmacy_id = "";
                    }
                   
                    
                    
                    
                    if ($pharmacy_id == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'required pharmacy_id'
                         
                        );
                    } else {
                        $res = $this->PartnermnoModel->vendor_payment_option($pharmacy_id);
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' =>  $res['data'] 
                        );
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function  assign_pending_order_to_mno(){
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
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
                    
                    if ($mno_id == "" || $lat  == "" || $lng == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'required mno_id, lat and lng'
                         
                        );
                    } else {
                        $res = $this->PartnermnoModel->assign_pending_order_to_mno($mno_id, $lat, $lng);
                        if($res['res'] == 1){
                            $resp = array(
                                'status' => 200,
                                'message' => 'MNO found',
                                 'data' =>  $res 
                            );
                        } else if($res['res'] == 2) {
                            $resp = array(
                                'status' => 400,
                                'message' => 'No MNO found'
                            );
                        } else if($res['res'] == 3) {
                            $resp = array(
                                'status' => 400,
                                'message' => 'MNO is offline'
                            );
                        }else if($res['res'] == 4) {
                            $resp = array(
                                'status' => 400,
                                'message' => 'MNO is busy'
                            );
                        }else if($res['res'] == 5) {
                            $resp = array(
                                'status' => 400,
                                'message' => 'No pending order'
                            );
                        }else {
                            $resp = array(
                                'status' => 400,
                                'message' => 'something went wrong'
                            );
                        }
                        
                        
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    // call 
    
     public function customer_call() {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OrderModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    $call_to = $params['call_to'];
                    $call_from = $params['call_from'];
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $post_data = array(
                        
                        // 'From' => "<First-phone-number-to-call (Your agent's number)>",
                        // 'To' => "<Second-phone-number-to-call (Your customer's number)>",
                        // 'CallerId' => "<Your-Exotel-virtual-number>",
                        // 'TimeLimit' => "<time-in-seconds> (optional)",
                        // 'TimeOut' => "<time-in-seconds (optional)>",
                        // 'CallType' => "promo" //Can be "trans" for transactional and "promo" for promotional content
                        'From' => $call_from,
                        'To' => $call_to,
                        'CallerId' => "02248931498",
                        // 'TimeLimit' => " ",
                        // 'TimeOut' => "",
                        'CallType' => "trans" //Can be "trans" for transactional and "promo" for promotional content
                    );
                
                // print_r($post_data); die();
                 
                $exotel_sid = "aegishealthsolutions"; // Your Exotel SID - Get it from here: http://my.exotel.in/settings/site#api-settings
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5"; // Your exotel token - Get it from here: http://my.exotel.in/settings/site#api-settings
                 
                $url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Calls/connect";
                 
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                 
                $http_result = curl_exec($ch);
                $error = curl_error($ch);
                $http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
                 
                curl_close($ch);
                 
                print "Response = ".print_r($http_result); 
                
                $this->load->model('LoginModel');
                $type= "order/customer_call";
                $this->LoginModel->exotel_call($http_result,$type);
                
              
                }
            }
        }
     
    }
    
    
    // send msg to executors
    
    public function send_msg_to_mno_executors(){
     //   $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                   
                   if(array_key_exists('invoice_no',$params)){
                        $invoice_no = $params['invoice_no'];
                    } else {
                        $invoice_no = "";
                    }
                    if ($invoice_no == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'required invoice_no'
                         
                        );
                    } else {
                        $res = $this->PartnermnoModel->send_msg_to_mno_executors($invoice_no);
                        if($res['sent_to'] > 0){
                            $resp = array(
                                'status' => 200,
                                'message' => 'success'
                            );
                        } else {
                            $resp = array(
                                'status' => 400,
                                'message' => 'No executors'
                            );
                        }
                        
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    // get_delivery_time_cost
    
    
    public function get_delivery_time_cost(){
     //   $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                   
                   if(array_key_exists('order_id',$params)){
                        $order_id = $params['order_id'];
                    } else {
                        $order_id = "";
                    }
                    if ($order_id == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'required order_id'
                         
                        );
                    } else {
                        $res = $this->PartnermnoModel->get_delivery_time_cost($order_id);
                        if(sizeof($res) > 0){
                            $resp = array(
                                'status' => 200,
                                'message' => 'success',
                                'data' => $res
                            );
                        } else {
                            $resp = array(
                                'status' => 400,
                                'message' => 'No executors'
                            );
                        }
                        
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    // user_address
    
    public function mno_available_delivery(){
     //   $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                   
                    if(array_key_exists('pincode',$params)){
                        $pincode = $params['pincode'];
                    } else {
                        $pincode = "";
                    }
                    
                    if(array_key_exists('address_id',$params)){
                        $address_id = $params['address_id'];
                    } else {
                        $address_id = "";
                    }
                    
                    // Either $pincode or $address_id are compulsory
                    if ($address_id == "" && $pincode == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'Enter either pincode or address_id'
                         
                        );
                    } else {
                        $res = $this->PartnermnoModel->mno_available_delivery($address_id, $pincode);
                        if($res['status'] == 1){
                            $resp = array(
                                'status' => 200,
                                'message' => $res['message'],
                                'delivery_available' => $res['delivery_available']
                            );
                        } else {
                            $resp = array(
                                'status' => 400,
                                'message' => $res['message'],
                                'delivery_available' => 0
                            );
                        }
                        
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    // get_mno_charges
    
    public function get_mno_charges(){
     //   $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    // $store_lat                   
                    if(array_key_exists('store_lat',$params)){
                        $store_lat = $params['store_lat'];
                    } else {
                        $store_lat = "";
                    }

                    // $store_lng

                    if(array_key_exists('store_lng',$params)){
                        $store_lng = $params['store_lng'];
                    } else {
                        $store_lng = "";
                    }
                    // $user_lat
                    if(array_key_exists('user_lat',$params)){
                        $user_lat = $params['user_lat'];
                    } else {
                        $user_lat = "";
                    }
                    // $user_lng
                    if(array_key_exists('user_lng',$params)){
                        $user_lng = $params['user_lng'];
                    } else {
                        $user_lng = "";
                    }
                    // $listing_id
                    if(array_key_exists('listing_id',$params)){
                        $listing_id = $params['listing_id'];
                    } else {
                        $listing_id = "";
                    }
                    // $mno_lat
                    if(array_key_exists('mno_lat',$params)){
                        $mno_lat = $params['mno_lat'];
                    } else {
                        $mno_lat = "";
                    }

                    // $mno_lng

                    if(array_key_exists('mno_lng',$params)){
                        $mno_lng = $params['mno_lng'];
                    } else {
                        $mno_lng = "";
                    }
                    
                    if(array_key_exists('pincode',$params)){
                        $pincode = $params['pincode'];
                    } else {
                        $pincode = 0;
                    }
                   
                    
                    // Either $pincode or $address_id are compulsory
                    if ($listing_id == "" || $pincode == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'Enter either pincode and listing_id'
                         
                        );
                    } else {
                        $get_mno_charges = $this->PartnermnoModel->get_mno_charges($store_lat,$store_lng,$user_lat,$user_lng,$mno_lat,$mno_lng, $listing_id,$pincode);
                       
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' => $get_mno_charges
                        );
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    public function get_mno_by_invoice(){
     //   $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    // invoice_no                   
                    if(array_key_exists('invoice_no',$params)){
                        $invoice_no = $params['invoice_no'];
                    } else {
                        $invoice_no = "";
                    }

                    // pharmacy_id

                    if(array_key_exists('pharmacy_id',$params)){
                        $pharmacy_id = $params['pharmacy_id'];
                    } else {
                        $pharmacy_id = "";
                    }
                   
                    
                    // Either $pincode or $address_id are compulsory
                    if ($invoice_no == "" || $pharmacy_id == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'Enter either invoice_no and pharmacy_id'
                         
                        );
                    } else {
                        $get_mno_by_invoice = $this->PartnermnoModel->get_mno_by_invoice($invoice_no, $pharmacy_id);
                       if($get_mno_by_invoice['status'] == 1){
                            $resp = array(
                                'status' => 200,
                                'message' => 'success',
                                'data' => $get_mno_by_invoice['data']
                            );   
                       } else if ($get_mno_by_invoice['status'] == 2){
                            $resp = array(
                                'status' => 400,
                                'message' => 'No data found'
                            );   
                       } else {
                           $resp = array(
                                'status' => 400,
                                'message' => 'Something went wrong'
                            );
                       }
                        
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    // login for executors
    public function order_list_executors() {
        
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
                
                
                // executors
                
                if(array_key_exists('excecutor_id',$params)){
                    $excecutor_id = $params['excecutor_id'];
                } else {
                    $excecutor_id = "";
                }
                
                $last = end($this->uri->segments); 
                
                if($last == 'accepted' || $last == 'rejected' || $last == 'pending'){
                    $type = $last;
                } else {
                    $type = "";
                }
                
                if(array_key_exists('mno_id',$params)){
                    $mno_id = $params['mno_id'];
                } else {
                    $mno_id = "";
                }
                
                if(array_key_exists('page_no',$params)){
                    $page_no = $params['page_no'];
                } else {
                   $page_no = 1;
                }
                
                
                if(array_key_exists('per_page',$params)){
                    $per_page = $params['per_page'];
                } else {
                   $per_page = 10;
                }
                
                if(array_key_exists('order_status',$params)){
                    $order_status = $params['order_status'];
                } else {
                   $order_status = "";
                }
                
                if(array_key_exists('ongoing',$params) && $params['ongoing'] == 1 ){
                    $is_ongoing = $params['ongoing'];
                } else {
                    $is_ongoing = 0;
                }
                
                if ($excecutor_id == "") {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter excecutor_id'
                    );
                } else {
                    
                    $res = $this->PartnermnoModel->order_list_executors($excecutor_id,$mno_id, $page_no, $per_page,$type,$order_status,$is_ongoing);
                    
                    if($res['status'] == 1){
                            $data = $res['data'];
                            $data_count = intval($data['data_count']);
                            $per_page = intval($data['per_page']);
                            $current_page = intval($data['current_page']);
                            $first_page = intval($data['first_page']);
                            $last_page = intval($data['last_page']);
                            $orders = $data['orders'];
                            
                        if(sizeof($orders) > 0){
                            $resp = array(
                                'status' => 200,
                                'message' => 'success',
                                'data_count' => $data_count,
                                'per_page' => $per_page,
                                'current_page' => $current_page,
                                'first_page' => $first_page,
                                'last_page' => $last_page,
                                'data' => $orders
                            );
                        } else {
                            $resp = array(
                                'status' => 401,
                                'message' => 'No data found'
                            );
                        }
                    } else if($res['status'] == 2){
                        $resp = array(
                            'status' => 400,
                            'message' => 'No executor found'
                        );
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'Something went wrong'
                        );
                    }
                   
                
                
                    
                }
               
                simple_json_output($resp);
            }
        }
    }
}

?>
