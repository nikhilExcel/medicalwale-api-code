<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BachatHealthCard extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('BachatHCModel');
        $this->load->model('LedgerModel');
    }
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    public function get_bachat_details()
    {
        $this->load->model('BachatHCModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->BachatHCModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->BachatHCModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter user id'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                       
                        $resp      = $this->BachatHCModel->get_bachat_details($user_id);
        
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function get_bachat_service_details()
    {
        $this->load->model('BachatHCModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->BachatHCModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->BachatHCModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter user id'
                        );
                    }
                    elseif ($params['card_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter Card id'
                        );
                    }
                    else {
                        $user_id   = $params['user_id'];
                        $card_id   = $params['card_id'];
                        $resp      = $this->BachatHCModel->get_bachat_service_details($user_id,$card_id);
        
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function create_bachat()
    {
        $this->load->model('BachatHCModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->BachatHCModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->BachatHCModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter user id'
                        );
                    }
                    elseif ($params['card_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter Card id'
                        );
                    }
               
               elseif ($params['amount'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter Amount'
                        );
                    }
                    else {
                        
                             if(array_key_exists('coupon_id', $params))
                             {
                                 $coupon_id = $params['coupon_id'];
                             } 
                             else 
                              {
                                    $coupon_id = 0;
                              }
                              // $amount
                            if(array_key_exists('amount', $params))
                            {
                                $amount = $params['amount'];
                            } else 
                            {
                                $amount = 0;
                            }
                            // $payment_method
                            if(array_key_exists('payment_method', $params))
                            {
                                $payment_method = $params['payment_method'];
                            } else 
                            {
                                $payment_method = "";
                            }
                           // $transaction_id
                            if(array_key_exists('transaction_id', $params))
                            {
                                $transaction_id = $params['transaction_id'];
                            } else 
                            {
                                $transaction_id = "";
                            }
                
                // $transaction_status
                if(array_key_exists('transaction_status', $params)){
                    $transaction_status = $params['transaction_status'];
                } else {
                    $transaction_status = "";
                }
                        
                        $user_id   = $params['user_id'];
                        $card_type   = $params['card_id'];
                        $amount=$params['amount'];
                        
                        $resp      = $this->BachatHCModel->create_bachat($user_id,$coupon_id, $amount, $payment_method, $transaction_id, $transaction_status,$card_type);
        
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function get_user_bachat_list()
    {
        $this->load->model('BachatHCModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->BachatHCModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->BachatHCModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter user id'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                       
                        $resp      = $this->BachatHCModel->get_user_bachat_list($user_id);
        
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function get_user_bachat_list_detail()
    {
        $this->load->model('BachatHCModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->BachatHCModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->BachatHCModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter user id'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $card_id   = $params['card_id'];
                        $resp      = $this->BachatHCModel->get_user_bachat_list_detail($user_id,$card_id);
        
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function subscription_order_booking() {
        $this->load->model('LabcenterModel');
        $this->load->model('Dental_clinic_model');
        $this->load->model('BachatHCModel');
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->BachatHCModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->BachatHCModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                    if ($params['user_id'] == "" || $params['vendor_id'] == "" || $params['address_id'] == "" || $params['vendor_type'] == "" || $params['booking_date'] == "" || $params['booking_time'] == "" || $params['package_id'] == "" || $params['test_id'] == "" || $params['member_id'] == "" || $params['payment_type'] == "" || $params['user_email'] =="") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id      = $params['user_id'];
                        $vendor_id    = $params['vendor_id'];
                        $address_id   = $params['address_id'];
                        $vendor_type  = $params['vendor_type'];
                        $booking_date = $params['booking_date'];
                        $booking_time = $params['booking_time'];
                        $package_id   = $params['package_id'];
                        $test_id      = $params['test_id'];
                        $member_id    = $params['member_id'];
                        $payment_type = $params['payment_type'];
                        $user_email =$params['user_email'];
                        if(!empty($params['coupon_id'])){
                            $coupon_id = $params['coupon_id'];
                        } else {
                            $coupon_id = 0;
                        }
                        $bachat_service_id = $params['bachat_service_id'];
                        $month = $params['month'];
                        if($vendor_type == 10) {
                            if ($params['user_id'] == "" || $params['vendor_id'] == "" || $params['address_id'] == "" || $params['vendor_type'] == "" || $params['booking_date'] == "" || $params['booking_time'] == "" || $params['package_id'] == "" || $params['test_id'] == "" || $params['member_id'] == "" || $params['payment_type'] == "" || $params['bachat_service_id'] == "" || $params['user_email']=="") {
                                 $resp = array('status' => 400, 'message' => 'please enter fields');
                            } 
                            else {
                                //added beacuse of bachat card booking api
                                
                                $resp = $this->LabcenterModel->add_order($user_id,$vendor_id,$address_id,$vendor_type,$booking_date,$booking_time,$package_id,$test_id,$member_id,$payment_type,$coupon_id,$bachat_service_id,$user_email);
                            
                                
                            }
                        }
                        if($vendor_type == 39) {
                            if ($params['user_id'] == "" || $params['vendor_id'] == "" || $params['address_id'] == "" || $params['vendor_type'] == "" || $params['booking_date'] == "" || $params['booking_time'] == "" || $params['package_id'] == "" || $params['test_id'] == "" || $params['member_id'] == "" || $params['payment_type'] == "" || $params['bachat_service_id'] == "" || $params['user_email']=="") {
                                 $resp = array('status' => 400, 'message' => 'please enter fields');
                            } 
                            else {
                                //added beacuse of bachat card booking api
                                
                                $resp = $this->Dental_clinic_model->quick_dental_booking($user_id,$vendor_id,$address_id,$vendor_type,$booking_date,$booking_time,$package_id,$test_id,$member_id,$payment_type,$coupon_id,$bachat_service_id,$user_email);
                            
                                
                            }
                        }
                        if($vendor_type == 37) {
                            if ($params['user_id'] == "" || $params['month'] == "" || $params['bachat_service_id'] == "" || $params['package_id'] == "") {
                                 $resp = array('status' => 400, 'message' => 'please enter fields');
                            } 
                            else {
                                //added beacuse of bachat card booking api
                                
                                $resp = $this->BachatHCModel->missbelly_booking($user_id,$month,$package_id,$bachat_service_id);
                            }
                        }
                     
                        
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function dentist_branch_list()
    {
        $this->load->model('BachatHCModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->BachatHCModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->BachatHCModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['lat'] == "" || $params['lng'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                        $page    = $params['page'];
                        
                        $resp = $this->BachatHCModel->dentist_branch_list($user_id,$listing_id,$lat,$lng,$page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
}
