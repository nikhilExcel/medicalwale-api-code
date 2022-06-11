<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dietplan extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function leads_followup_list()
    {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['booking_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $book_id     = $params['booking_id'];
                        $resp = $this->dietplan_model->leads_followup_list($user_id,$book_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function leads_followup_update()
    {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['id'] == "" || $params['next_followup_date'] == "" || $params['next_followup_time'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $id     = $params['id'];
                        $next_followup_date = $params['next_followup_date'];
                        $next_followup_time = $params['next_followup_time'] ;
                        $resp = $this->dietplan_model->leads_followup_update($user_id,$id,$next_followup_date,$next_followup_time);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function missbelly_packages_list()
    {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $resp = $this->dietplan_model->missbelly_packages_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function missbelly_diet_list_school()
    {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $resp = $this->dietplan_model->missbelly_diet_list_school($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function renew_package()
    {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['package_id'] == "" || $params['from_date'] == "" ||  $params['gst'] == "" ||  $params['amount'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $package_id     = $params['package_id'];
                        $from_date   = $params['from_date'] ;
                        $gst  = $params['gst'];
                        $amount     = $params['amount'];
                        if(array_key_exists('coupon_id', $params)){
                           $coupon_id = $params['coupon_id'];                        
                            
                        } else {
                           $coupon_id = 0;
                       }
                        $resp = $this->dietplan_model->renew_package($user_id,$package_id,$from_date,$gst,$amount,$coupon_id);
                    }
                       simple_json_output($resp);
                }
            }
        }
    }
    
       public function dietplan_booking() {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    //print_r($params);
                    if ($params['user_id'] == "" || $params['package_id'] == "" ||  $params['gst'] == "" ||  $params['amount'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id    = $params['user_id'];
                        $gst  = $params['gst'];
                        $amount     = $params['amount'];
                        $package_id = $params['package_id'];
                        if(array_key_exists('coupon_id', $params)){
                           $coupon_id = $params['coupon_id'];                        
                            
                        } else {
                               
                           $coupon_id = 0;
                       }
                       
                        if(array_key_exists('commission_id', $params)){
                           $commission_id = $params['commission_id'];                        
                            
                        } else {
                               
                           $commission_id = "";
                       }
                       
                        if(array_key_exists('refer_code', $params)){
                           $refer_code = $params['refer_code'];                        
                            
                        } else {
                               
                           $refer_code = "";
                       }
                       
                        $booking_id = date('YmdHis');
                      
                        $resp = $this->dietplan_model->dietplan_booking($user_id, $package_id, $gst, $amount, $booking_id,$coupon_id,$commission_id,$refer_code);
                    }

                    simple_json_output($resp);
                }
            }
        }
    }
  /* public function up()
    {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                      
                        $resp = $this->dietplan_model->up($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }*/
    
    public function buy_diet_plan() {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                    if(array_key_exists('user_id',$params)){
                        $user_id = $params['user_id'];
                    } else {
                        $user_id = "";
                    }
                    
                    if(array_key_exists('package_id',$params)){
                        $package_id = $params['package_id'];
                    } else {
                        $package_id = "";
                    }
                    
                    if(array_key_exists('gst',$params)){
                        $gst = $params['gst'];
                    } else {
                        $gst = 0;
                    }
                    
                    if(array_key_exists('amount',$params)){
                        $amount = $params['amount'];
                    } else {    
                        $amount = 0;
                    }
                    
                    if(array_key_exists('coupon_id', $params)){
                       $coupon_id = $params['coupon_id']; 
                    } else {
                       $coupon_id = 0;
                    }
                    
                    // transaction_status : 1 - success
                    // transaction_status : 2 - failed
                    // transaction_status : 3 - cancel by user
                    
                    if(array_key_exists('transaction_status', $params)){
                       $transaction_status = $params['transaction_status']; 
                    } else {
                       $transaction_status = 0;
                    }
                    
                    // $transaction_id
                    if(array_key_exists('transaction_id', $params)){
                       $transaction_id = $params['transaction_id']; 
                    } else {
                       $transaction_id = 0;
                    }
                    
                    // $payment_method
                    if(array_key_exists('payment_method', $params)){
                       $payment_method = $params['payment_method']; 
                    } else {
                       $payment_method = 0;
                    }
                    
                    // commission_id
                    if(array_key_exists('commission_id', $params)){
                       $commission_idd = $params['commission_id']; 
                    } else {
                       $commission_id = 0;
                    }
                    // refer_code
                    if(array_key_exists('refer_code', $params)){
                       $refer_code = $params['refer_code']; 
                    } else {
                       $refer_code = 0;
                    }
                    
                    if ($user_id == "" || $package_id == "" ||  $amount == "" || $transaction_status == "" || $transaction_id == "" || $payment_method == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields : user_id, package_id, amount, transaction_status, transaction_id, payment_method');
                    } else {
                        $response = $this->dietplan_model->buy_diet_plan($user_id, $package_id, $gst, $amount,$coupon_id,$transaction_id,$transaction_status,$payment_method,$commission_id,$refer_code);
                    
                        if($response['status'] == 1 ){
                            $resp = array('status' => 200, 'message' => 'success','booking_id' => $response['booking_id']);
                        } else {
                            $resp = array('status' => 400, 'message' => 'Something went wrong');
                        }
                    }

                    simple_json_output($resp);
                }
            }
        }
    }
    public function check_refer_code()
    {
         $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                    if(array_key_exists('user_id',$params)){
                        $user_id = $params['user_id'];
                    } else {
                        $user_id = "";
                    }
                    
                    if(array_key_exists('refer_code',$params)){
                        $refer_code = $params['refer_code'];
                    } else {
                        $refer_code = "";
                    }
                    
                    
                    if ($user_id == "" || $refer_code == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields : user_id, refer_code');
                    } else {
                        $resp = $this->dietplan_model->check_refer_code($user_id, $refer_code);
                    
                    }

                    simple_json_output($resp);
                }
            }
        }
    }
}
?>