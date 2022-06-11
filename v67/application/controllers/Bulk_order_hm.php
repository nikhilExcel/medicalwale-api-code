<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bulk_order_hm extends CI_Controller {
    
    
    public function __construct() {
        parent::__construct();
        $this->load->model('LoginModel');
        $this->load->model('Bulk_order_hm_Model');
     
        date_default_timezone_set('Asia/Kolkata');
    } 
 
    public function index() { 
        json_output(400, array(
            'message' => 'Bad request.'
        ));
    }
    
    public function get_brands(){
	    $method = $_SERVER['REQUEST_METHOD'];
	   
		if($method != 'POST'){
		    $resp = array(
                'status' => 400,
                'message' => 'Bad request'
            );
			simple_json_output($resp);
		} else {
			$check_auth_client = $this->LoginModel->check_auth_client();
		
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
		            
    		        $params = json_decode(file_get_contents('php://input'), TRUE);
                
                    if(array_key_exists('user_id',$params)){
                        $user_id = $params['user_id'];
                    } else {
                        $user_id = "";
                    }
                     if(array_key_exists('vendor_type',$params)){
                        $vendor_type = $params['vendor_type'];
                    } else {
                        $vendor_type = 0;
                    }
                    
                    if(array_key_exists('page_no',$params)){
                        $page_no = $params['page_no'];
                    } else {
                        $page_no = 1;
                    }
                    if(array_key_exists('per_page',$params)){
                        $per_page = $params['per_page'];
                    } else {
                        $per_page = 100;
                    }
                    
                    
                    if($user_id == "" || $vendor_type < 0){
                        $result = array(
            	            "status" => 400,
            	            "message" => "Please send user_id and vendor_type"
                        );
                        simple_json_output($result);
                    } else {
                        
                    
		             $finalData = $this->Bulk_order_hm_Model->get_brands($user_id, $vendor_type,$page_no , $per_page);
		             if(sizeof($finalData['brands']) > 0){
		                 $result = array(
            	            "status" => 200,
            	            "message" => "success",
            	            "data" => $finalData['brands']
                        );
    		            
		             } else {
		                 $result = array(
            	            "status" => 400,
            	            "message" => "No vendors found"
                        );
		             }
		             simple_json_output($result);
                    }
		             
		            
		            
		        }  else {
    			     $resp = array(
                        'status' => 400,
                        'message' => 'Authentication failed'
                    );
        			simple_json_output($resp);
    			}
			} else {
			     $resp = array(
                    'status' => 400,
                    'message' => 'Authorization failed'
                );
    			simple_json_output($resp);
			}
		}
	}
	
    public function get_all_products(){
	    $method = $_SERVER['REQUEST_METHOD'];
	   
		if($method != 'POST'){
		    $resp = array(
                'status' => 400,
                'message' => 'Bad request'
            );
			simple_json_output($resp);
		} else {
			$check_auth_client = $this->LoginModel->check_auth_client();
		
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
		            
    		        $params = json_decode(file_get_contents('php://input'), TRUE);
                
                    if(array_key_exists('user_id',$params)){
                        $user_id = $params['user_id'];
                    } else {
                        $user_id = "";
                    }
                    if(array_key_exists('brand_id',$params)){
                        $brand_id = $params['brand_id'];
                    } else {
                        $brand_id = "";
                    }
                    
                    
                    if(array_key_exists('page_no',$params)){
                        $page_no = $params['page_no'];
                    } else {
                        $page_no = 1;
                    }
                    if(array_key_exists('per_page',$params)){
                        $per_page = $params['per_page'];
                    } else {
                        $per_page = 100;
                    }
                    
                    if($user_id == ""){
                        $result = array(
            	            "status" => 400,
            	            "message" => "Please send user_id "
                        );
                        simple_json_output($result);
                    } else {
                        
                    
		             $finalData = $this->Bulk_order_hm_Model->get_all_products($user_id, $brand_id, $page_no, $per_page);
		             if(sizeof($finalData['brands']) > 0){
		                 $result = array(
            	            "status" => 200,
            	            "message" => "success",
            	            "data" => $finalData['brands']
                        );
    		            
		             } else {
		                 $result = array(
            	            "status" => 400,
            	            "message" => "No vendors found"
                        );
		             }
		             simple_json_output($result);
                    }
		             
		            
		            
		        }  else {
    			     $resp = array(
                        'status' => 400,
                        'message' => 'Authentication failed'
                    );
        			simple_json_output($resp);
    			}
			} else {
			     $resp = array(
                    'status' => 400,
                    'message' => 'Authorization failed'
                );
    			simple_json_output($resp);
			}
		}
	}
}

?>