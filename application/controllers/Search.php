<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
    }

	public function index()
	{
	    json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}
	 
	public function keyword_list()
	{
	    $this->load->model('SearchModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->SearchModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->SearchModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['keyword'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$keyword = $params['keyword'];
		        		$resp = $this->SearchModel->keyword_list($user_id,$keyword);
					}
				  json_outputs($resp);
		        }
			}
		}
	}
	
	public function search_list()
	{
	    $this->load->model('SearchModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->SearchModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->SearchModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['keyword'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$keyword = $params['keyword'];
		        		$resp = $this->SearchModel->search_list($user_id,$keyword);
					}
				  json_outputs($resp);
		        }
			}
		}
	}
	
	public function page_list()
	{
	    $this->load->model('SearchModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->SearchModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->SearchModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['keyword'] == "" || $params['listing_type'] == "" || $params['page'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$listing_type = $params['listing_type'];
						$page = $params['page'];
						$keyword = $params['keyword'];
		        		$resp = $this->SearchModel->page_list($user_id,$keyword,$listing_type,$page);
					}
				  json_outputs($resp);
		        }
			}
		}
	}
	
	public function profile_details()
	{
	    $this->load->model('SearchModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->SearchModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->SearchModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['post_user_id'] == "" || $params['listing_type'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$post_user_id = $params['post_user_id'];
						$user_id = $params['user_id'];
						$listing_type = $params['listing_type'];
		        		$resp = $this->SearchModel->profile_details($user_id,$post_user_id,$listing_type);
					}
				  json_outputs($resp);
		        }
			}
		}
	}

}	