<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payment extends CI_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('Payment_model');
        $this->load->model('Login_Model');
    }
    
    
     public function add_payment_status()
	{
// 	  echo "dasd"; die();
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			$resp = array('status' => 400,'message' => 'Bad request');
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
				
				
					if ($this->input->post('user_id') == null || $this->input->post('listing_id') == "" || $this->input->post('trans_id') == "" || $this->input->post('orderid_bookingid')  == ""|| $this->input->post('order_type') == "" || $this->input->post('status') == "" || $this->input->post('type') == "" || $this->input->post('amount') == "" || $this->input->post('status_mesg') == "" || $this->input->post('payment_type') == "" ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id        = $this->input->post('user_id');
					    $listing_id     = $this->input->post('listing_id');
					    $trans_id       = $this->input->post('trans_id');
					    $status         = $this->input->post('status');
					    $type           = $this->input->post('type');
					    $order_id       = $this->input->post('orderid_bookingid');
					    $order_type     = $this->input->post('order_type');
					    $amount         = $this->input->post('amount');
					    $status_mesg    = $this->input->post('status_mesg');
					    $discount       = $this->input->post('discount');
					    $discount_rupee = $this->input->post('cashback');
					    $payment_type   = $this->input->post('payment_type');
				
					
                        // $resp = array('status' => 200,'message' =>  'All  fields');
                        $resp           = $this->Payment_model->insert_payment_status($user_id, $listing_id, $trans_id, $status, $type, $order_id, $order_type, $amount, $status_mesg, $discount, $discount_rupee, $payment_type);
					}
					    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
				
		        } else {
		             $resp = array('status' => 400,'message' =>  'Auth failed');
		             return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}
	
	
    
    function payment_success(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
        	    $pro_id = $this->input->post('pd_id');
        	    if($pro_id == "" || $pro_id == null){
        	     $res = array(
		                    "status" => 400,
                            "message" => "please enter fields"
		                );
        	    }else{
        	         $res = array(
            	        "status" => 200,
                        "message" => "success",
                        "data" => $result
                    );
        	  
        	    }
        	   
                    
        	    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($res));
	        }
			    
			}
		    
		}
	}

}

?>