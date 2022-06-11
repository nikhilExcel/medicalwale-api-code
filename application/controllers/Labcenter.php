<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Labcenter extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
    }

	public function index()
	{
	    json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}
	


	public function labcenter_list()
	{
	    $this->load->model('LabcenterModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LabcenterModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LabcenterModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "" || $params['category_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id = $params['user_id'];
						$lat = $params['lat'];
						$lng = $params['lng'];
						$category_id = $params['category_id'];
		        		$resp = $this->LabcenterModel->labcenter_list($lat,$lng,$user_id,$category_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	
		public function labcenter_packages()
	{
	    $this->load->model('LabcenterModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LabcenterModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LabcenterModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['labcenter_id'] == "" ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $labcenter_id = $params['labcenter_id'];
		        		$resp = $this->LabcenterModel->labcenter_packages($labcenter_id); 
					}
					
				 json_outputs($resp); 
				 
		        }
			}
		}
	}
	
	
	
	public function lab_test_search()
	{
	    $this->load->model('LabcenterModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LabcenterModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LabcenterModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['keyword'] == "" || $params['category_id'] == "" || $params['lab_user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter Test');
					} else {
						$keyword= $params['keyword'];
						$category_id= $params['category_id'];
						$lab_user_id= $params['lab_user_id'];
		        		$resp = $this->LabcenterModel->lab_test_search($keyword,$category_id,$lab_user_id);
					}
					
					if($resp!='') { json_outputs($resp); }
					else { json_outputs_not_found($resp); }
		        }
			}
		}
	}
	
	


public function add_review()
	{
	    $this->load->model('LabcenterModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LabcenterModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LabcenterModel->auth();
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
		        		$resp = $this->LabcenterModel->add_review($user_id,$listing_id,$rating,$review,$service);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function review_list()
	{
	    $this->load->model('LabcenterModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LabcenterModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LabcenterModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['listing_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$listing_id = $params['listing_id'];
		        		$resp = $this->LabcenterModel->review_list($user_id,$listing_id); 
					}
					
				json_outputs($resp);
		        }
			}
		}
	}
	
	public function review_like()
	{
	    $this->load->model('LabcenterModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LabcenterModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LabcenterModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->LabcenterModel->review_like($user_id,$post_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
    public function review_comment()
	{
	    $this->load->model('LabcenterModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LabcenterModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LabcenterModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
						$comment = $params['comment'];
		        		$resp = $this->LabcenterModel->review_comment($user_id,$post_id,$comment);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function review_comment_like()
	{
	    $this->load->model('LabcenterModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LabcenterModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LabcenterModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['comment_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$comment_id = $params['comment_id'];
		        		$resp = $this->LabcenterModel->review_comment_like($user_id,$comment_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function review_comment_list()
	{
	    $this->load->model('LabcenterModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LabcenterModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LabcenterModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
					    $post_id = $params['post_id'];
		        		$resp = $this->LabcenterModel->review_comment_list($user_id,$post_id);
					}
					
					json_outputs($resp);  }
		        }
			}
		}



	}
		
	
	
	
	
	
	

	