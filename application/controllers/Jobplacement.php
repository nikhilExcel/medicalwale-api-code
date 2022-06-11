<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jobplacement extends CI_Controller {

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
	
	public function record_list()
	{
	    $this->load->model('JobplacementModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->JobplacementModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->JobplacementModel->auth();
		        if($response['status'] == 200){
					$resp = $this->JobplacementModel->placement_list();
					json_outputs($resp);
		        }
			}
		}
	}
	
	public function job_list()
	{
	    $this->load->model('JobplacementModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->JobplacementModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->JobplacementModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['category'] == "" || $params['location'] == "" || $params['job_type'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$category = $params['category'];
						$location = $params['location'];
						$job_type = $params['job_type'];
		        		$resp = $this->JobplacementModel->job_list($category,$location,$job_type);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	public function add_job()
	{
	    $this->load->model('JobplacementModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->JobplacementModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->JobplacementModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['job_type'] == "" || $params['job_title'] == "" || $params['job_description'] == "" || $params['job_role'] == "" || $params['job_location'] == "" || $params['min_salary'] == "" || $params['max_salary'] == "" || $params['company_name'] == "" || $params['email'] == "" || $params['mobile'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$job_type = $params['job_type'];
						$job_title = $params['job_title'];
						$job_description = $params['job_description'];
						$job_role = $params['job_role'];
						$job_location = $params['job_location'];
						$min_salary = $params['min_salary'];
						$max_salary = $params['max_salary'];
						$company_name = $params['company_name'];
						$email = $params['email'];
						$mobile = $params['mobile'];
						$gender = $params['gender'];
		        		$resp = $this->JobplacementModel->add_job($job_type,$job_title,$job_description,$job_role,$job_location,$min_salary,$max_salary,$company_name,$email,$mobile,$gender);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
		public function add_job_user_profile()
	{
	    $this->load->model('JobplacementModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->JobplacementModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->JobplacementModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['name'] == "" || $params['phone'] == "" || $params['email'] == "" || $params['dob'] == "" || $params['gender'] == "" || $params['job_role'] == "" || $params['min_salary'] == "" || $params['max_salary'] == "" || $params['year_exp'] == "" || $params['month_exp'] == "" || $params['city'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id = $params['user_id'];
						$name = $params['name'];
						$phone = $params['phone'];
						$email = $params['email'];
						$dob = $params['dob'];
						$gender = $params['gender'];
						$job_role = $params['job_role'];
						$min_salary = $params['min_salary'];
						$max_salary = $params['max_salary'];
						$year_exp = $params['year_exp'];
						$month_exp = $params['month_exp'];
						$city = $params['city'];
						
		        		$resp = $this->JobplacementModel->add_job_user_profile($name,$phone,$email,$dob,$gender,$job_role,$min_salary,$max_salary,$year_exp,$month_exp,$city,$user_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	

	public function user_profile_doc()
		{
			$this->load->model('JobplacementModel');
			$method = $_SERVER['REQUEST_METHOD'];
			if ($method != 'POST') {
				json_output(400, array(
					'status' => 400,
					'message' => 'Bad request.'
				));
			} else {

			$user_id = $_POST['user_id'];		
			

			if ($user_id == "" || empty($_FILES["resume"]["name"]))  {
					$resp = array(
						'status' => 400,
						'message' => 'please enter fields'
					);
				}
				else
				{					
								
				//unlink images
					$file_query = $this->db->query("SELECT resume FROM `job_user_profile` WHERE user_id='$user_id'");
					$get_file   = $file_query->row();

					if ($get_file) {
					 $resume = $get_file->resume;
					 $file= 'images/job_image/' . $resume;
                     @unlink(trim($file));
                     DeleteFromToS3($file);	
					}
					//unlink images ends
		
					$img_format = array("jpg", "png", "gif", "bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
					include('s3_config.php');
				
			        $img_name = $_FILES['resume']['name'];
                    $img_size = $_FILES['resume']['size'];
                    $img_tmp  = $_FILES['resume']['tmp_name'];
                     $ext      = getExtension($img_name);	
				
				    if(strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if(in_array($ext, $img_format)) {
                                    $resume_file = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/job_image/' . $resume_file;
                                   $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
							
                                }
							  }
					}		
			
				
				$resp = $this->JobplacementModel->user_profile_doc($user_id, $resume_file);
				} 
			
				simple_json_output($resp);
			}
		}
		
		
			public function job_role()
	{
	    $this->load->model('JobplacementModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->JobplacementModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->JobplacementModel->auth();
		        if($response['status'] == 200){
					$resp = $this->JobplacementModel->job_role();
					json_outputs($resp);
		        }
			}
		}
	}
	
	
	
		public function user_profile_list()
	{
	    $this->load->model('JobplacementModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->JobplacementModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->JobplacementModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" ) 
					{
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
					
		        		$resp = $this->JobplacementModel->user_profile_list($user_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
    
}
