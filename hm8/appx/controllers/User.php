<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User extends CI_Controller{
    
    public function __construct($config = 'rest'){
        //  header('Access-Control-Allow-Origin: *'); 
        //  header("Access-Control-Allow-Credentials: true"); 
        //  header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
         
        parent::__construct($config);

        $this->load->model('User_model');
        $this->load->model('Login_Model');
        $this->load->model('MedicalMall_model_2');
        $this->load->model('MedicalMall_model');
        
    }
	
	public function generate_user_code(){
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
		            
		            $user_id = $this->input->post('user_id');
		            $pd_id = $this->input->post('pd_id');
		            if(!empty($user_id) && !empty($pd_id)){
		                	$resp = $this->User_model->generate_user_code($user_id, $pd_id); 
    		               
		            } else {
		                $resp = array(
		                "status" => 400,
                        "message" => "Please enter user_id and pd_id",
                       
		                );
				
		            }
		                
		            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}
	
	public function add_referral_code(){
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
		            
		            $referral_code = $this->input->post('referral_code');
		            $pd_id = $this->input->post('pd_id');
		            if(!empty($referral_code) && !empty($pd_id)){
		                	$resp = $this->User_model->add_referral_code($referral_code, $pd_id); 
    		               
		            } else {
		                $resp = array(
		                "status" => 400,
                        "message" => "Please enter referral_code and pd_id",
                       
		                );
				
		            }
		                
		            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}
	
		public function add_physical_code(){
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
		            
		            $physical_code = $this->input->post('physical_code');
		            $pd_id = $this->input->post('pd_id');
		            $user_id = $this->input->post('user_id');
		            if(!empty($physical_code) && !empty($pd_id)){
		                	$resp = $this->User_model->add_physical_code($physical_code, $pd_id, $user_id); 
    		               
		            } else {
		                $resp = array(
		                "status" => 400,
                        "message" => "Please enter physical_code, user_id and pd_id",
                       
		                );
				
		            }
		                
		            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}


    public function all_promotional_ads(){
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
		            
		            $user_id = $this->input->post('user_id');
		            if(!empty($user_id)){
		                	$res = $this->User_model->all_promotional_ads($user_id);
		                	 $resp = array(
        		                "status" => 200,
                                "message" => "success",
                                "data" => $res
                               
        		                );
		            } else {
		                $resp = array(
		                "status" => 400,
                        "message" => "Please enter  user_id",
                       
		                );
				
		            }
		                
		            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}
	
	 public function view_promotional_ads(){
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
		            
		            $user_id = $this->input->post('user_id');
		            $offer_id = $this->input->post('offer_id');
		            if(!empty($user_id) && !empty($offer_id)){
		                	$res = $this->User_model->view_promotional_ads($user_id,$offer_id);
		                	 $resp = array(
        		                "status" => 200,
                                "message" => "success",
                                "data" => $res
                               
        		                );
		            } else {
		                $resp = array(
		                "status" => 400,
                        "message" => "Please enter  user_id and offer_id",
                       
		                );
				
		            }
		                
		            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}
	
	
	 public function view_promotional_ads_products(){
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
		            
		            $user_id = $this->input->post('user_id');
		            $offer_id = $this->input->post('offer_id');
		            $vendor_id = $this->input->post('vendor_id');
		            
		            if(!empty($user_id) && !empty($offer_id)){
		                	$res = $this->User_model->view_promotional_ads($user_id,$offer_id);
		                	 $resp = array(
        		                "status" => 200,
                                "message" => "success",
                                "data" => $res
                               
        		                );
		            } else {
		                $resp = array(
		                "status" => 400,
                        "message" => "Please enter  user_id and offer_id",
                       
		                );
				
		            }
		                
		            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}
	
	
}
?>