<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Doctoronline extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
         $this->load->model('LoginModel');
         $this->load->model('DoctoronlineModel');
     
    }
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
    
  
    
    public function doctor_online_cat_v1() 
    {
        $this->load->model('DoctoronlineModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->DoctoronlineModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->DoctoronlineModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                     if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == ""  ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                        if(array_key_exists("page",$params)){
                            $page = $params['page'];
                        } else {
                            $page = '';
                        }
                        $resp = $this->DoctoronlineModel->doctor_online_cat_v1($user_id,$lat,$lng,$page);
                    }
                }
      
               json_outputs($resp);
            }
        }
    }
    public function doctor_user_block() 
    {
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
                    if ($params['doctor_id'] == "" || $params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id=$params['doctor_id'];
                        $user_id=$params['user_id'];
                        $member_id=$params['member_id'];
                        $type=$params['type'];
                        $fee=$params['fee'];
                        $resp = $this->DoctoronlineModel->doctor_user_block($doctor_id,$user_id,$member_id,$type,$fee);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function add_bookings()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                   
                    if ($params['user_id'] == "") {
                       
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter user_id'
                        );
                    } 
                    else if ($params['doctor_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter doctor_id'
                        );
                    } 
                    else if ($params['transaction_id'] == "" || $params['transaction_id'] == "0") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter Transaction ID'
                        );
                    } 
                    else {
                            $doctor_id=$params['doctor_id'];
                            $user_id=$params['user_id'];
                            $member_id=$params['member_id'];
                            $type=$params['type'];
                            $fee=$params['fee'];
                            $status= $params['booking_status'];
                            $transaction_id=$params['transaction_id'];
                            $payment_method=$params['payment_method'];
                            if(array_key_exists('coupon_id', $params))
                            {
                              $coupon_id   = $params['coupon_id'];
                            }
                            else 
                            {
                              $coupon_id = 0;
                            }
                            $resp= $this->DoctoronlineModel->add_bookings($doctor_id,$user_id,$member_id,$type,$fee,$status,$coupon_id,$transaction_id,$payment_method);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    public function doctor_online_chat() {
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
                    if ($params['doctor_id'] == "" || $params['user_id'] == ""  ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } 
                    elseif($params['booking_id']=="")
                    {
                       $resp = array(
                            'status' => 400,
                            'message' => 'please enter booking_id'
                        ); 
                    }
                    else {
                        $doctor_id=$params['doctor_id'];
                        $user_id=$params['user_id'];
                        $booking_id=$params['booking_id'];
                        $resp = $this->DoctoronlineModel->doctor_online_chat($doctor_id,$user_id,$booking_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function doctor_online_cat() 
    {
        $this->load->model('DoctoronlineModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->DoctoronlineModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->DoctoronlineModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                     if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == ""  ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                        
                        $resp = $this->DoctoronlineModel->doctor_online_cat($user_id,$lat,$lng);
                    }
                }
      
               json_outputs($resp);
            }
        }
    }
    
     
    
     public function doctor_online_detail() 
    {
        $this->load->model('DoctoronlineModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->DoctoronlineModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->DoctoronlineModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                     if ($params['user_id'] == "" || $params['doctor_user_id'] == ""   ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $doctor_user_id     = $params['doctor_user_id'];
                       
                     
                        $resp = $this->DoctoronlineModel->doctor_online_detail($user_id,$doctor_user_id);
                    }
                }
      
               json_outputs($resp);
            }
        }
    }
    
    
     public function doctor_booking_check() 
    {
        $this->load->model('DoctoronlineModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->DoctoronlineModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->DoctoronlineModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                     if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['date']=="" || $params['time']==""  ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id    = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $date       = $params['date'];
                        $time       = $params['time'];
                       
                     
                        $resp = $this->DoctoronlineModel->doctor_booking_check($user_id,$listing_id,$date,$time);
                    }
                }
      
               simple_json_output($resp);
            }
        }
    }
    
}
