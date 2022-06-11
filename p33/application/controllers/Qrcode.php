<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Qrcode extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
         $this->load->model('Qrcodemodel');
        /*
        $check_auth_client = $this->SexeducationModel->check_auth_client();
		if($check_auth_client != true){
			die($this->output->get_output());
		}
		*/
    }

	public function index()
	{
	    json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}

	public function get_vendor_details(){
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Qrcodemodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Qrcodemodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['qrcode'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $qrcode     = $params['qrcode'];
					    $userid     = $params['user_id'];
		        		$resp       = $this->Qrcodemodel->get_vendor_details($qrcode,$userid);
					}
					    simple_json_output($resp);
		        }
			}
		}
	}
	
	public function sendCCToVendor1()
	{
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Qrcodemodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Qrcodemodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['coupon'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $coupon     = $params['coupon'];
					    $userid     = $params['user_id'];
					    $vendorid   = $params['vendor_id'];
					    $bcno       = $params['bachatno'];
					    $uname      = $params['user_name'];
		        		$resp       = $this->Qrcodemodel->sendCCToVendor1($coupon,$userid,$vendorid,$bcno,$uname);
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}
	
	public function sendCCToVendor()
	{
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Qrcodemodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Qrcodemodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['coupon'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $coupon     = $params['coupon'];
					    $userid     = $params['user_id'];
					    $vendorId     = $params['vendor_id'];
					   
		        		$resp  = $this->Qrcodemodel->sendCCToVendor($coupon,$userid,$vendorId);
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}
	
		public function generate_coupons()
	{
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Qrcodemodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Qrcodemodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['qrcode'] == "" || $params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $qrcode     = $params['qrcode'];
					    $userid     = $params['user_id'];
		        		$resp       = $this->Qrcodemodel->generate_coupons($qrcode,$userid);
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}
	
	public function vendor_txn_details(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Qrcodemodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Qrcodemodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['coupon'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $coupon     = $params['coupon'];
		        		$resp       = $this->Qrcodemodel->vendor_txn_details($coupon);
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}
	
	public function add_qrcode() {
        $this->load->model('Qrcodemodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
            
        } else {
            $check_auth_client = $this->Qrcodemodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Qrcodemodel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['vendor_id'] == "" || $params['vendor_qrcode'] == "" || $params['vendor_type'] == "" ) {
                        $resp = array('status' => 400, 'Description' => 'please enter fields');
                    } else {
                        $vendor_id     = $params['vendor_id'];
                        $vendor_qrcode = $params['vendor_qrcode'];
                        $vendor_type = $params['vendor_type'];
                        $resp          = $this->Qrcodemodel->add_qrcode($vendor_id, $vendor_qrcode, $vendor_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
//   user comments
    
		public function add_user_comment() {
		    $this->load->model('Qrcodemodel');
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method != 'POST') {
                json_output(400, array('status' => 400, 'message' => 'Bad request.'));
                
            } else {
            $check_auth_client = $this->Qrcodemodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Qrcodemodel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                    $trans_id = $params['trans_id'];
                    $user_comment = $params['user_comment'];


                    if ($trans_id == "" || $user_comment == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $resp          = $this->Qrcodemodel->add_user_comment($trans_id, $user_comment, $user_id);
                        // $resp = array('status'=>200, 'message'=>'success');
                    }
                   
                    simple_json_output($resp);
                }
            }
        }
		}
	
}