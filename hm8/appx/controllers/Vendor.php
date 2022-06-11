<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Vendor extends CI_Controller{
    
    public function __construct($config = 'rest')
    {
        //  header('Access-Control-Allow-Origin: *'); 
        //  header("Access-Control-Allow-Credentials: true"); 
        //  header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
         
        parent::__construct($config);

        $this->load->model('Vendor_model');
        $this->load->model('Login_Model');
    }
	
	public function get_vendor_offers(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => 400,
                "message" => "Bad request",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            
		            $v_id = $this->input->post('v_id');
		            if(!empty($v_id)){
		                	$get_vendor_offers = $this->Vendor_model->get_vendor_offers($v_id); 
    		                $resp = array(
    		                "status" => 200,
                            "message" => "success",
                            "data" => $get_vendor_offers
                           
    		                );
				
		            } else {
		                $resp = array(
		                "status" => 201,
                        "message" => "PLease add v_id",
                       
		                );
				
		            }
		                
		            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}
	
	
	public function get_vendor_by_categories(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => 400,
                "message" => "Bad request",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            
		            $cat_id = $this->input->post('cat_id');
		            if(!empty($cat_id)){
		                	$get_vendor_by_categories = $this->Vendor_model->get_vendor_by_categories($cat_id); 
    		                $resp = array(
    		                "status" => 200,
                            "message" => "success",
                            "data" => $get_vendor_by_categories
                           
    		                );
				
		            } else {
		                $resp = array(
		                "status" => 201,
                        "message" => "PLease add v_id",
                       
		                );
				
		            }
		                
		            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}
	
	
}
?>