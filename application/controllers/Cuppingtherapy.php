<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cuppingtherapy extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
    }

	public function index()
	{
	    json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}

	
	
		
	public function cuppingtherapy_list()
	{
	    $this->load->model('CuppingtherapyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->CuppingtherapyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->CuppingtherapyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['latitude'] == "" || $params['longitude'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$latitude = $params['latitude'];
						$longitude = $params['longitude'];
		        		$resp = $this->CuppingtherapyModel->cuppingtherapy_list($user_id,$latitude,$longitude);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	
	
	public function cuppingtherapy_packages()
	{
	    $this->load->model('CuppingtherapyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->CuppingtherapyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->CuppingtherapyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['cuppingtherapy_id'] == "" ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $cuppingtherapy_id = $params['cuppingtherapy_id'];
		        		$resp = $this->CuppingtherapyModel->cuppingtherapy_packages($cuppingtherapy_id); 
					}
				
				 json_outputs($resp);
		        }
			}
		}
	}


public function add_review()
	{
	    $this->load->model('CuppingtherapyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->CuppingtherapyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->CuppingtherapyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['rating'] == "" || $params['review'] == "" || $params['service'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$listing_id = $params['listing_id'];
						$rating = $params['rating'];
						$review = $params['review'];
						$service = $params['service']; 
		        		$resp = $this->CuppingtherapyModel->add_review($user_id,$listing_id,$rating,$review,$service);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function review_list()
	{
	    $this->load->model('CuppingtherapyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->CuppingtherapyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->CuppingtherapyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['listing_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$listing_id = $params['listing_id'];
		        		$resp = $this->CuppingtherapyModel->review_list($user_id,$listing_id); 
					}
					
					if($resp!='') { json_outputs($resp); }
					else { json_outputs_not_found($resp); }
		        }
			}
		}
	}
	
	public function review_like()
	{
	    $this->load->model('CuppingtherapyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->CuppingtherapyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->CuppingtherapyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->CuppingtherapyModel->review_like($user_id,$post_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
    public function review_comment()
	{
	    $this->load->model('CuppingtherapyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->CuppingtherapyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->CuppingtherapyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
						$comment = $params['comment'];
		        		$resp = $this->CuppingtherapyModel->review_comment($user_id,$post_id,$comment);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function review_comment_like()
	{
	    $this->load->model('CuppingtherapyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->CuppingtherapyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->CuppingtherapyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['comment_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$comment_id = $params['comment_id'];
		        		$resp = $this->CuppingtherapyModel->review_comment_like($user_id,$comment_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function review_comment_list()
	{
	    $this->load->model('CuppingtherapyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->CuppingtherapyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->CuppingtherapyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
					    $post_id = $params['post_id'];
		        		$resp = $this->CuppingtherapyModel->review_comment_list($user_id,$post_id);
					}
										if($resp!='') { json_outputs($resp); }
					else { json_outputs_not_found($resp); }
		        }
			}
		}
	}
		
	
	
	
}