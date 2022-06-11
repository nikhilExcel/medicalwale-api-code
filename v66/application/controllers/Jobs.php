<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Jobs extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('JobsprofileModel');
         $this->load->model('LoginModel');
         
        /*
          $check_auth_client = $this->SexeducationModel->check_auth_client();
          if($check_auth_client != true){
          die($this->output->get_output());
          }
         */
        //    $ch = curl_init();
         // $response = curl_exec($ch);
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }


    //user-profile-API
    public function add_jobs_user_profile() {
        $response = array();
        $status = array();
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
           // $check_auth_client = $this->JobplacementModel->check_auth_client();
           // if ($check_auth_client == true) {
             //   $response = $this->LoginModel->auth();
               // if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['first_name'] == "" || $params['last_name'] == "" || $params['mobile'] == ""  || $params['email'] == "" || $params['dob'] == "" || $params['gender'] == "" || $params['marital_status'] == "" || $params['address_line1'] == ""  || $params['address_line2'] == "" || $params['city'] == "" || $params['state'] == ""  ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $first_name = $params['first_name'];
                        $last_name = $params['last_name'];
                        $mobile = $params['mobile'];
                        $email = $params['email']; 
                        $dob = $params['dob'];
                        $gender = $params['gender'];
                        $marital_status = $params['marital_status'];
                        $address_line1 = $params['address_line1'];
                        $address_line2 = $params['address_line2'];
                        $state = $params['state'];
                        $city = $params['city'];

                        $resp = $this->JobsprofileModel->add_jobs_user_profile($user_id,$first_name,$last_name, $mobile, $email, $dob, $gender, $marital_status, $address_line1, $address_line2, $city, $state );
                    }
                    simple_json_output($resp);
               // }
            //}
        }
    }
    
            //JOBS-EDUCATION-X
     public function add_jobs_user_profile_education() {
        $response = array();
        $status = array();
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
           // $check_auth_client = $this->JobplacementModel->check_auth_client();
           // if ($check_auth_client == true) {
             //   $response = $this->LoginModel->auth();
               // if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['highest_qualification'] == "" || $params['class_x_school'] == "" || $params['x_board'] == ""  || $params['x_passing_year'] == "" || $params['x_medium'] == "" || $params['x_marks'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $highest_qualification = $params['highest_qualification'];
                        $class_x_school = $params['class_x_school'];
                        $x_board = $params['x_board'];
                        $x_passing_year = $params['x_passing_year']; 
                        $x_medium = $params['x_medium'];
                        $x_marks = $params['x_marks'];
                       
                        $resp = $this->JobsprofileModel->add_jobs_user_profile_education($user_id,$highest_qualification,$class_x_school,$x_board, $x_passing_year, $x_medium, $x_marks );
                    }
                    simple_json_output($resp);
               // }
            //}
        }
    }
    
    //JOBS-EDUCATION-XII
    public function add_jobs_user_profile_education_college() {
        $response = array();
        $status = array();
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
           // $check_auth_client = $this->JobplacementModel->check_auth_client();
           // if ($check_auth_client == true) {
             //   $response = $this->LoginModel->auth();
               // if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['class_xii_college'] == "" || $params['xii_board'] == "" || $params['xii_passing_year'] == ""  || $params['xii_medium'] == "" || $params['xii_marks'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $class_xii_college = $params['class_xii_college'];
                        $xii_board = $params['xii_board'];
                        $xii_passing_year = $params['xii_passing_year'];
                        $xii_medium = $params['xii_medium']; 
                        $xii_marks = $params['xii_marks'];
                       
                        $resp = $this->JobsprofileModel->add_jobs_user_profile_education_college($user_id,$class_xii_college,$xii_board,$xii_passing_year, $xii_medium, $xii_marks );
                    }
                    simple_json_output($resp);
               // }
            //}
        }
    }
            
            //JOBS-EDUCATION-GRADUATE
     public function add_jobs_user_profile_education_graduate() {
        $response = array();
        $status = array();
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
           // $check_auth_client = $this->JobplacementModel->check_auth_client();
           // if ($check_auth_client == true) {
             //   $response = $this->LoginModel->auth();
               // if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['graduate'] == "" || $params['g_course'] == "" || $params['g_specialisation'] == ""  || $params['g_university'] == "" || $params['g_pass_year'] == "" || $params['g_grade'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $graduate = $params['graduate'];
                        $g_course = $params['g_course'];
                        $g_specialisation = $params['g_specialisation'];
                        $g_university = $params['g_university']; 
                        $g_pass_year = $params['g_pass_year'];
                         $g_grade = $params['g_grade'];
                       
                        $resp = $this->JobsprofileModel->add_jobs_user_profile_education_graduate($user_id,$graduate,$g_course,$g_specialisation, $g_university, $g_pass_year,$g_grade );
                    }
                    simple_json_output($resp);
               // }
            //}
        }
    }
                //JOBS-EDUCATION-POSTGRADUATE
     public function add_jobs_user_profile_education_postgraduate() {
        $response = array();
        $status = array();
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
           // $check_auth_client = $this->JobplacementModel->check_auth_client();
           // if ($check_auth_client == true) {
             //   $response = $this->LoginModel->auth();
               // if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['postgraduate'] == "" || $params['pg_course'] == "" || $params['pg_specialisation'] == ""  || $params['pg_university'] == "" || $params['pg_pass_year'] == "" || $params['pg_grade'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $postgraduate = $params['postgraduate'];
                        $pg_course = $params['pg_course'];
                        $pg_specialisation = $params['pg_specialisation'];
                        $pg_university = $params['pg_university']; 
                        $pg_pass_year = $params['pg_pass_year'];
                        $pg_grade = $params['pg_grade'];
                       
                        $resp = $this->JobsprofileModel->add_jobs_user_profile_education_postgraduate($user_id,$postgraduate,$pg_course,$pg_specialisation, $pg_university, $pg_pass_year,$pg_grade );
                    }
                    simple_json_output($resp);
               // }
            //}
        }
    }
    
            //JOBS-EDUCATION-DOCTORATE
    public function add_jobs_user_profile_education_doctorate() {
        $response = array();
        $status = array();
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
           // $check_auth_client = $this->JobplacementModel->check_auth_client();
           // if ($check_auth_client == true) {
             //   $response = $this->LoginModel->auth();
               // if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['doctorate_phd'] == "" || $params['d_course'] == "" || $params['d_specialisation'] == ""  || $params['d_university'] == "" || $params['d_pass_year'] == "" || $params['d_grade'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $doctorate_phd = $params['doctorate_phd'];
                        $d_course = $params['d_course'];
                        $d_specialisation = $params['d_specialisation'];
                        $d_university = $params['d_university']; 
                        $d_pass_year = $params['d_pass_year'];
                        $d_grade = $params['d_grade'];
                       
                        $resp = $this->JobsprofileModel->add_jobs_user_profile_education_doctorate($user_id,$doctorate_phd,$d_course,$d_specialisation, $d_university, $d_pass_year,$d_grade );
                    }
                    simple_json_output($resp);
               // }
            //}
        }
    }
    
    
    
           //JOBS-CERTIFICATE
    public function add_jobs_user_profile_education_certificate() {
        $response = array();
        $status = array();
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
           // $check_auth_client = $this->JobplacementModel->check_auth_client();
           // if ($check_auth_client == true) {
             //   $response = $this->LoginModel->auth();
               // if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['certification'] == "" || $params['ce_month'] == "" || $params['ce_year'] == ""  || $params['ce_description'] == "" || $params['certificate_issued_by'] == "" || $params['achievements'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $certification = $params['certification'];
                        $ce_month = $params['ce_month'];
                        $ce_year = $params['ce_year'];
                        $ce_description = $params['ce_description']; 
                        $certificate_issued_by = $params['certificate_issued_by'];
                        $achievements = $params['achievements'];
                       
                        $resp = $this->JobsprofileModel->add_jobs_user_profile_education_certificate($user_id,$certification,$ce_month,$ce_year, $ce_description, $certificate_issued_by,$achievements );
                    }
                    simple_json_output($resp);
               // }
            //}
        }
    }
    
    
            //JOBS-previous-job-details
    public function add_jobs_user_profile_previous_job() {
        $response = array();
        $status = array();
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
           // $check_auth_client = $this->JobplacementModel->check_auth_client();
           // if ($check_auth_client == true) {
             //   $response = $this->LoginModel->auth();
               // if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['company_name'] == "" || $params['company_type'] == "" || $params['designation'] == "" || $params['location'] == ""  || $params['work_experience'] == "" || $params['work_start'] == "" || $params['work_end'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $company_name = $params['company_name'];
                        $company_type = $params['company_type'];
                        $designation = $params['designation'];
                        $location = $params['location'];
                        $work_experience = $params['work_experience']; 
                        $work_start = $params['work_start'];
                        $work_end = $params['work_end'];
                       
                        $resp = $this->JobsprofileModel->add_jobs_user_profile_previous_job($user_id,$company_name,$company_type,$designation,$location, $work_experience, $work_start,$work_end );
                    }
                    simple_json_output($resp);
               // }
            //}
        }
    }
    
    
    
            //JOBS-preferred-job-details
    public function add_jobs_user_profile_preferred_job() {
        $response = array();
        $status = array();
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
           // $check_auth_client = $this->JobplacementModel->check_auth_client();
           // if ($check_auth_client == true) {
             //   $response = $this->LoginModel->auth();
               // if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['job_type'] == "" || $params['job_location'] == "" || $params['job_position'] == ""  || $params['min_salary'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $job_type = $params['job_type'];
                        $job_location = $params['job_location'];
                        $job_position = $params['job_position'];
                        $min_salary = $params['min_salary'];
                        
                       
                        $resp = $this->JobsprofileModel->add_jobs_user_profile_preferred_job($user_id,$job_type,$job_location,$job_position,$min_salary );
                    }
                    simple_json_output($resp);
               // }
            //}
        }
    }
    
    
    
    
    
     public function user_profile_list() {
       $response = array();
       $status = array();
       
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $id = $params['id'];

                        $resp = $this->JobsprofileModel->user_profile_list($id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  /* public function get_data() {
         $this->load->model('JobsprofileModel');
 	$method = $_SERVER['REQUEST_METHOD'];
 	if($method != 'POST' ){
 		json_output(400, array('status' => 400, 'message' => 'Bad request.'));
 	}
   $this->data['posts'] = $this->JobsprofileModel->get_posts();
   $query = $this->data['posts']; 
    echo json_encode($query);
   // calling Post model method getPosts()
  // $this->load->view('posts_view', $this->data); // load the view file , we are passing $data array to view file
 }*/
    

}
?>