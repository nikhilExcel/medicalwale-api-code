<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Bloodbank extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
    }

	public function index()
	{
	    json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}
	
	public function bank_list()
	{
	    $this->load->model('BloodbankModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->BloodbankModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->BloodbankModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['latitude'] == "" || $params['longitude'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$latitude = $params['latitude'];
						$longitude = $params['longitude'];
		        		$resp = $this->BloodbankModel->bank_list($user_id,$latitude,$longitude);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	public function bank_details()
	{
	    $this->load->model('BloodbankModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->BloodbankModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->BloodbankModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['listing_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$listing_id = $params['listing_id'];
		        		$resp = $this->BloodbankModel->bank_details($user_id,$listing_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
    public function blood_group_stock()
	{
	    $this->load->model('BloodbankModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->BloodbankModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->BloodbankModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['blood_bank_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$blood_bank_id = $params['blood_bank_id'];
		        		$resp = $this->BloodbankModel->blood_group_stock($blood_bank_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	public function add_review()
	{
	    $this->load->model('BloodbankModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->BloodbankModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->BloodbankModel->auth();
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
		        		$resp = $this->BloodbankModel->add_review($user_id,$listing_id,$rating,$review,$service);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function review_list()
	{
	    $this->load->model('BloodbankModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->BloodbankModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->BloodbankModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['listing_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$listing_id = $params['listing_id'];
		        		$resp = $this->BloodbankModel->review_list($user_id,$listing_id); 
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	public function review_comment_list()
	{
	    $this->load->model('BloodbankModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->BloodbankModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->BloodbankModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->BloodbankModel->review_comment_list($user_id,$post_id); 
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	public function review_like()
	{
	    $this->load->model('BloodbankModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->BloodbankModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->BloodbankModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->BloodbankModel->review_like($user_id,$post_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
    public function review_comment()
	{
	    $this->load->model('BloodbankModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->BloodbankModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->BloodbankModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
						$comment = $params['comment'];
		        		$resp = $this->BloodbankModel->review_comment($user_id,$post_id,$comment);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function review_comment_like()
	{
	    $this->load->model('BloodbankModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->BloodbankModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->BloodbankModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['comment_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$comment_id = $params['comment_id'];
		        		$resp = $this->BloodbankModel->review_comment_like($user_id,$comment_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function blood_donor_list()
	{
	    $this->load->model('BloodbankModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->BloodbankModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->BloodbankModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['blood_group'] == "" || $params['lat'] == "" || $params['lng'] == "" || $params['radius'] == "" || $params['page'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
					    $blood_group = $params['blood_group'];	
					    $radius = $params['radius'];
					    $lat = $params['lat'];
					    $lng = $params['lng'];
					    $page = $params['page'];
		        		$resp = $this->BloodbankModel->blood_donor_list($user_id,$lat,$lng,$radius,$blood_group,$page);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
   
   
   	public function blood_request()
	{
	    $this->load->model('BloodbankModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->BloodbankModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->BloodbankModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['donor_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
					    $donor_id = $params['donor_id'];	
		        		$resp = $this->BloodbankModel->blood_request($user_id,$donor_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
   
   
}
