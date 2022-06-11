<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Article extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }
  //require(APPPATH.'/libraries/REST_Controller.php');  

}

class Api extends REST_Controller{
    
    public function __construct()
    {
        parent::__construct();

        $this->load->model('book_model');
    }

	function get_cat_get(){
	
		$result = $this->book_model->getallCategories();
		$categories = array(
                    "status" => "true",
                    "statuspic_root_code" => "200",
                    "data" => $result
                );
        if($categories){

           
						// $this->response(json_encode($categories), 200); 
						 $this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($categories));

        } 

        else{

           $result = array(
				"status" => "false",
				"message" => "Result not found"
				);
			$this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($result));

        }


	}
	function getFind_get(){
	
	
	
		$result = $this->book_model->getallfinds();
		$categories = array(
                    "status" => "true",
                    "statuspic_root_code" => "200",
                    "data" => $result
                );
        if($categories){

           
						// $this->response(json_encode($categories), 200); 
						 $this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($categories));

        } 

        else{

           $result = array(
				"status" => "false",
				"message" => "Result not found"
				);
			$this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($result));

        }


	}
	function getLanguages_get(){
	
	
	
		$result = $this->book_model->getalllanguages();
		$categories = array(
                    "status" => "true",
                    "statuspic_root_code" => "200",
                    "data" => $result
                );
        if($categories){

           
						// $this->response(json_encode($categories), 200); 
						 $this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($categories));

        } 

        else{

           $result = array(
				"status" => "false",
				"message" => "Result not found"
				);
			$this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($result));

        }


	}
	function job_title_get(){
	
	
	
		$result = $this->book_model->getalljobtitle();
		$categories = array(
                    "status" => "true",
                    "statuspic_root_code" => "200",
                    "data" => $result
                );
        if($categories){

           
						// $this->response(json_encode($categories), 200); 
						 $this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($categories));

        } 

        else{

           $result = array(
				"status" => "false",
				"message" => "Result not found"
				);
			$this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($result));

        }


	}
	function job_hire_get(){
	
	
		$result = $this->book_model->getalljobhire();
		$categories = array(
                    "status" => "true",
                    "statuspic_root_code" => "200",
                    "data" => $result
                );
        if($categories){

           
						// $this->response(json_encode($categories), 200); 
						 $this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($categories));

        } 

        else{

           $result = array(
				"status" => "false",
				"message" => "Result not found"
				);
			$this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($result));

        }


		
	}
	function job_by_cat_get(){
		$data  = $this->get('category_id');
		$result = $this->book_model->getJobCategorys($data);
		$categories = array(
                    "status" => "true",
                    "statuspic_root_code" => "200",
                    "data" => $result
                );
        if($categories){

           
						// $this->response(json_encode($categories), 200); 
						 $this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($categories));

        } 

        else{

           $result = array(
				"status" => "false",
				"message" => "Result not found"
				);
			$this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($result));

        }
		
	}
	function baner_api_get(){
		$result = $this->book_model->getBanerAPI();
		$categories = array(
                    "status" => "true",
                    "statuspic_root_code" => "200",
                    "data" => $result
                );
        if($categories){

           
						// $this->response(json_encode($categories), 200); 
						 $this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($categories));

        } 

        else{

           $result = array(
				"status" => "false",
				"message" => "Result not found"
				);
			$this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($result));

        }
		
	
	}
	function job_approve_by_title_post(){
	
		$job_by_approval  = $this->get('category_id');
	
		$result = $this->book_model->getJobApproveByTitle(job_by_approval);
		$categories = array(
                    "status" => "true",
                    "statuspic_root_code" => "200",
                    "data" => $result
                );
        if($categories){

           
						// $this->response(json_encode($categories), 200); 
						 $this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($categories));

        } 

        else{

           $result = array(
				"status" => "false",
				"message" => "Result not found"
				);
			$this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($result));

        }
		
		
	}
	function job_by_type_get(){
	
	$job_by_type  = $this->get('job_by_type');
	
	$result = $this->book_model->getJobByType($job_by_type);
	
		$categories = array(
                    "status" => "true",
                    "statuspic_root_code" => "200",
                    "data" => $result);
        if($categories){

           
						// $this->response(json_encode($categories), 200); 
						 $this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($categories));

        } 

        else{

           $result = array(
				"status" => "false",
				"message" => "Result not found"
				);
			$this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($result));

        }
		
	
			
	}
	function upload_post(){
		 $user_name      = $this->post('user_name');

         $user_mobile     = $this->post('user_mobile');

         $user_email    = $this->post('user_email');

         $user_dob  = $this->post('user_dob');

         $user_gender  = $this->post('user_gender');

         $user_job_title      = $this->post('user_job_title');
		 
		 $user_min_salary  = $this->post('user_min_salary');

         $user_max_salary  = $this->post('user_max_salary');

         $user_exp_year      = $this->post('user_exp_year');
		 
		 $user_exp_month  = $this->post('user_exp_month');

         $user_city  = $this->post('user_city');

		 $path      = $this->post('path');
		 
		 $stmt = "INSERT INTO `job_user_profile`(`name`, `phone`, `email`, `dob`, `gender`, `job_role`, `min_salary`, `max_salary`, `year_exp`, `month_exp`, `city`, `resume`) VALUES ('".$user_name."','".$user_mobile."','".$user_email."',".$user_dob.",'".$user_gender."','".$user_job_title."',".$user_min_salary.",".$user_max_salary.",".$user_exp_year.",'".$user_exp_month."','".$user_city."','".$path."')";
	
		$query = $this->db->query($stmt);

		$categories = array(
                    "status" => "true",
                    "statuspic_root_code" => "200",
                    "data" => $query
                );
        if($categories){

           
						// $this->response(json_encode($categories), 200); 
						 $this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($categories));

        } 

        else{

           $result = array(
				"status" => "false",
				"message" => "Result not found"
				);
			$this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($result));

        }
		
	}
	function post_job_post(){
		 $job_title      = $this->post('job_title');

         $job_description     = $this->post('job_description');

         $category_id    = $this->post('category_id');

         $job_type  = $this->post('job_type');

         $min_salary  = $this->post('min_salary');

         $max_salary      = $this->post('max_salary');
		 
		 $company_name  = $this->post('company_name');

         $job_location  = $this->post('job_location');

         $mobile      = $this->post('mobile');
		 
		 $email  = $this->post('email');
		 
		 $stmt = "INSERT INTO `job_list`(`job_title`, `job_description`, `category_id`, `job_type`, `min_salary`, `max_salary`, `company_name`, `job_location`, `mobile`, `email`) VALUES ('".$job_title."','".$job_desc."','".$job_category."',".$job_type.",'".$job_min_salary."','".$job_max_salary."','".$job_companey_name."','".$job_city."','".$job_phone_no."','".$job_email."')";
	
		$query = $this->db->query($stmt);

		$categories = array(
                    "status" => "true",
                    "statuspic_root_code" => "200",
                    "data" => $query
                );
        if($categories){

           
						// $this->response(json_encode($categories), 200); 
						 $this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($categories));

        } 

        else{

           $result = array(
				"status" => "false",
				"message" => "Result not found"
				);
			$this->output->set_content_type('Content-Type: application/json');

						return $this->output
						->set_content_type('Content-Type: application/json')
						->set_output(json_encode($result));

        }
		
	}
	}
