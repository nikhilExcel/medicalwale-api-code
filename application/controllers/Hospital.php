<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hospital extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
    }

	public function index()
	{
	    json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}
	
	
	
	public function hospital_list()
	{
	    $this->load->model('HospitalModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->HospitalModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->HospitalModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['latitude'] == "" || $params['longitude'] == "" || $params['category_name'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$latitude = $params['latitude'];
						$longitude = $params['longitude'];
						$category_name = $params['category_name'];
		        		$resp = $this->HospitalModel->hospital_list($latitude,$longitude,$user_id, $category_name);
					}
				  json_outputs($resp);
		        }
			}
		}
	}
	
	
	public function hospital_details()
	{
	    $this->load->model('HospitalModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->HospitalModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->HospitalModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['listing_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$listing_id = $params['listing_id'];
		        		$resp = $this->HospitalModel->hospital_details($user_id,$listing_id);
					}
				  json_outputs($resp);
		        }
			}
		}
	}
		
	public function doctor_list()
	{
	    $this->load->model('HospitalModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->HospitalModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->HospitalModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['hospital_id'] == "" || $params['category_name'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$hospital_id = $params['hospital_id'];
						$category_name = $params['category_name'];
		        		$resp = $this->HospitalModel->doctor_list($hospital_id, $category_name);
					}
				  json_outputs($resp);
		        }
			}
		}
	}
	
public function hospitals_appointment()
	{
	    $this->load->model('HospitalModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->HospitalModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->HospitalModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['hospital_id'] == "" || $params['surgery_id'] == "" || $params['patient_name'] == "" || $params['gender'] == "" || $params['age'] == "" || $params['mobile'] == "" || $params['ts1_date'] == "" || $params['ts1_time'] == "" || $params['ts2_date'] == "" || $params['ts2_time'] == "" || $params['medical_condition'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
                      	$user_id= $params['user_id'];
                      	$hospital_id= $params['hospital_id'];
                      	$surgery_id= $params['surgery_id'];
                      	$patient_name= $params['patient_name'];
                      	$gender= $params['gender'];
                      	$age= $params['age'];
                      	$mobile= $params['mobile'];
                      	$ts1_date= $params['ts1_date'];
                      	$ts1_time= $params['ts1_time'];
                      	$ts2_date= $params['ts2_date'];
                      	$ts2_time= $params['ts2_time'];
                      	$medical_condition= $params['medical_condition'];
		        		$resp = $this->HospitalModel->hospitals_appointment($user_id,$hospital_id,$surgery_id,$patient_name,$gender,$age,$mobile,$ts1_date,$ts1_time,$ts2_date,$ts2_time,$medical_condition);
					}
					simple_json_output($resp);
		        }
			}
		}
	}		


public function add_review()
	{
	    $this->load->model('HospitalModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->HospitalModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->HospitalModel->auth();
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
		        		$resp = $this->HospitalModel->add_review($user_id,$listing_id,$rating,$review,$service);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function review_list()
	{
	    $this->load->model('HospitalModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->HospitalModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->HospitalModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['listing_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$listing_id = $params['listing_id'];
		        		$resp = $this->HospitalModel->review_list($user_id,$listing_id); 
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	public function review_like()
	{
	    $this->load->model('HospitalModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->HospitalModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->HospitalModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->HospitalModel->review_like($user_id,$post_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
    public function review_comment()
	{
	    $this->load->model('HospitalModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->HospitalModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->HospitalModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
						$comment = $params['comment'];
		        		$resp = $this->HospitalModel->review_comment($user_id,$post_id,$comment);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function review_comment_like()
	{
	    $this->load->model('HospitalModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->HospitalModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->HospitalModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['comment_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$comment_id = $params['comment_id'];
		        		$resp = $this->HospitalModel->review_comment_like($user_id,$comment_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function review_comment_list()
	{
	    $this->load->model('HospitalModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->HospitalModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->HospitalModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
					    $post_id = $params['post_id'];
		        		$resp = $this->HospitalModel->review_comment_list($user_id,$post_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	
	
	
	
	
	
}	