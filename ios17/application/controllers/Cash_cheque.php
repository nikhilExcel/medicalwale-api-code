<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cash_cheque extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
         $this->load->model('LoginModel');
         $this->load->model('Cash_cheque_model');
     
    }
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
      public function add_cash_cheque() 
      {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Cash_cheque_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['vendor_id'] == "" || $params['user_id'] == "" || $params['amount'] == "" || $params['txn_id'] == "" || $params['invoice_no'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        
                        $vendor_id = $params['vendor_id'];
                        $listing_id = $params['listing_id'];
                        $coupon_id = $params['coupon_id'];
                        $user_id = $params['user_id'];
                        $amount = $params['amount'];
                        $txn_id = $params['txn_id'];
                        $invoice_no = $params['invoice_no'];
                        
                        $resp = $this->Cash_cheque_model->add_cash_cheque($user_id,$coupon_id,$vendor_id,$amount,$txn_id,$invoice_no);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function get_coupon_by_vendor() 
      {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Cash_cheque_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['vendor_id'] == "" || $params['user_id'] == "" || $params['listing_id'] == ""  )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $params['user_id'];
                        $vendor_id = $params['vendor_id'];
                        $listing_id = $params['listing_id'];
                         if(array_key_exists("category",$params)){
                           $category = $params['category'];
                        } else {
                            $category = "";
                        }
                        
                         if(array_key_exists("product_id",$params)){
                             $product_id = $params['product_id'];
                        } else {
                            $product_id = "";
                        }
                        
                        
                        $resp = $this->Cash_cheque_model->get_coupon_by_vendor($user_id,$vendor_id,$listing_id,$category,$product_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function get_all_cashcheck() 
      {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Cash_cheque_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $params['user_id'];
                        
                        $resp = $this->Cash_cheque_model->get_all_cashcheck($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function get_user_cashcheck() 
      {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Cash_cheque_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $params['user_id'];
                        
                        $resp = $this->Cash_cheque_model->get_user_cashcheck($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function get_total_bachat() 
      {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Cash_cheque_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $params['user_id'];
                        
                        $resp = $this->Cash_cheque_model->get_total_bachat($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
       public function coupon_select() 
      {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Cash_cheque_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $params['user_id'];
                        
                        $resp = $this->Cash_cheque_model->coupon_select($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    
    
    
    
    
    
}