<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Healthrecord extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
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

    public function add_record()
	{
	    $this->load->model('HealthrecordModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
	
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['patient_name'] == "" || $params['relationship'] == "" || $params['date_of_birth'] == "" || $params['gender'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id = $params['user_id'];
						$patient_name = $params['patient_name'];
						$relationship = $params['relationship'];
						$date_of_birth = $params['date_of_birth'];
						$gender = $params['gender'];
		        		$resp = $this->HealthrecordModel->add_record($user_id,$patient_name,$relationship,$date_of_birth,$gender);
					}
					simple_json_output($resp);
		 
		}
	}
	
	
		public function healthrecord_list()
	{
	    $this->load->model('HealthrecordModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->HealthrecordModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->HealthrecordModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					  
						$user_id = $params['user_id'];
		        		$resp = $this->HealthrecordModel->healthrecord_list($user_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	



	
		public function health_list_by_date()
	{
	    $this->load->model('HealthrecordModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->HealthrecordModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->HealthrecordModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['patient_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					  
						$patient_id = $params['patient_id'];
		        		$resp = $this->HealthrecordModel->health_list_by_date($patient_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	







}
