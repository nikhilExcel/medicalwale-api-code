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
   /* public function add_jobs_user_profile() {
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
    } */
    
    // user_profile_with_image
    
    public function user_profile() {
        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            $first_name    = $this->input->post('first_name');
            $last_name    = $this->input->post('last_name');
            $mobile = $this->input->post('mobile');
            $email = $this->input->post('email');
            $dob = $this->input->post('dob');
            $gender     = $this->input->post('gender');
            $marital_status = $this->input->post('marital_status');
            $languages_known     = $this->input->post('languages_known');
            $nationality     = $this->input->post('nationality');
            $address_line1 = $this->input->post('address_line1');
            $address_line2 = $this->input->post('address_line2');
            $city         = $this->input->post('city');
            $city_id         = $this->input->post('city_id');
            $state        = $this->input->post('state');
            $state_id        = $this->input->post('state_id');
            $country      =$this->input->post('country');
            $pincode      = $this->input->post('pincode');
            $c_address_line_1 = $this->input->post('c_address_line_1');
            $c_address_line_2 = $this->input->post('c_address_line_2');
            $c_city         = $this->input->post('c_city');
            $c_state = $this->input->post('c_state');
            $c_city_id         = $this->input->post('c_city_id');
            $c_state_id        = $this->input->post('c_state_id');
            $c_country      =$this->input->post('c_country');
            $c_pincode      = $this->input->post('c_pincode');
            $pincode      = $this->input->post('pincode');
            $mother_tongue  = $this->input->post('mother_tongue');
            
           
                       
            if ($user_id == "" || $first_name == "" || $mobile == "" || $email == "")  {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields1'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields1"));
            } else {
                $result = $this->JobsprofileModel->user_profile($user_id, $first_name, $last_name, $mobile,$email,$dob,$gender,$marital_status,$languages_known,$nationality, $address_line1, $address_line2, $city,$city_id, $state,$state_id,$country,$pincode,$c_address_line_1, $c_address_line_2, $c_city, $c_state,$c_city_id, $c_state_id,$c_country,$c_pincode, $mother_tongue);
                
                $date = date('Y-m-d'); 
                if ($result != '') {
                    // $order_id = $result['order_id'];
                   $image ="";
                    include('s3_config.php');
                    
                    if(empty($_FILES["image"]["name"])){
                        $previous_image = $this->db->get_where('users',array('id' => $user_id))->row()->avatar_id;
                        $previous_image1 = $this->db->get_where('media',array('id' => $previous_image))->row()->title;
                        $this->db->set('image', $previous_image1); //value that used to update column  
                        $this->db->where('user_id', $user_id); //which row want to upgrade  
                        $this->db->update('jobs_user_profile_master'); 
                        
                    }
                    
                    if (!empty($_FILES["image"]["name"])) {
                        $image = count($_FILES['image']['name']);
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        date_default_timezone_set('Asia/Calcutta');
                        //$invoice_no = date("YmdHis");
                      //  $order_status = 'Awaiting Confirmation';
                    }
                    if ($image > 0) {
                        foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                            $img_name = $key . $_FILES['image']['name'][$key];
                            $img_size = $_FILES['image']['size'][$key];
                            $img_tmp = $_FILES['image']['tmp_name'][$key];
                            $ext = getExtension($img_name);
                            if (strlen($img_name) > 0) {
                                if ($img_size < (50000 * 50000)) {
                                    if (in_array($ext, $img_format)) {
                                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                        $actual_image_path = 'images/job_image/' . $actual_image_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                        $this->db->set('image', $actual_image_name); //value that used to update column  
                                        $this->db->where('user_id', $user_id); //which row want to upgrade  
                                        $this->db->update('jobs_user_profile_master'); 
                                        }
                                    }
                                }
                            }
                        }
                    }
                    simple_json_output(array('status' => 200, 'message' => 'success','data'=> $result));
                } else {
                    simple_json_output(array("status" => 201, "message" => "fail" ,'data'=> array()));
                }
            }
        }
    }
    
    public function user_profile_resume(){
        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            $resume_head = $this->input->post('resume_head');
            if ($user_id == ""  ||  empty($_FILES["resume"]["name"])) {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields1'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields1"));
            } else {
                $result = $this->JobsprofileModel->user_profile_resume($user_id, $resume_head);
                
                $date = date('Y-m-d'); 
                if ($result != '') {
                    // $order_id = $result['order_id'];
                   
                    include('s3_config.php');
                    if (!empty($_FILES["resume"]["name"])) {
                        $image = count($_FILES['resume']['name']);
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP","pdf");
                        include('s3_config.php');
                        date_default_timezone_set('Asia/Calcutta');
                        //$invoice_no = date("YmdHis");
                      //  $order_status = 'Awaiting Confirmation';
                    }
                    if ($image > 0) {
                        foreach ($_FILES['resume']['tmp_name'] as $key => $tmp_name) {
                            $img_name = $key . $_FILES['resume']['name'][$key];
                            $img_size = $_FILES['resume']['size'][$key];
                            $img_tmp = $_FILES['resume']['tmp_name'][$key];
                            $ext = getExtension($img_name);
                            if (strlen($img_name) > 0) {
                                if ($img_size < (50000 * 50000)) {
                                    if (in_array($ext, $img_format)) {
                                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                        $actual_image_path = 'images/job_image/' . $actual_image_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                        $this->db->set('resume', $actual_image_name); //value that used to update column  
                                        $this->db->where('user_id', $user_id); //which row want to upgrade  
                                        $this->db->update('jobs_user_profile_master'); 
                                        }
                                    }
                                }
                            }
                        }
                    }
                    simple_json_output(array('status' => 200, 'message' => 'success','data'=> $result));
                } else {
                    simple_json_output(array("status" => 201, "message" => "fail" ,'data'=> array()));
                }
            }
        }
    }
    
    public function user_profile_video(){
        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            $resume_head = 1;
            if ($user_id == ""  ||  empty($_FILES["video"]["name"])) {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields1'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields1"));
            } else {
                $result = $this->JobsprofileModel->user_profile_video($user_id, $resume_head);
                
                $date = date('Y-m-d'); 
                if ($result != '') {
                    // $order_id = $result['order_id'];
                   
                    include('s3_config.php');
                    if (!empty($_FILES["video"]["name"])) {
                        $image = count($_FILES['video']['name']);
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP","pdf","mp4","MPG","AVI");
                        include('s3_config.php');
                        date_default_timezone_set('Asia/Calcutta');
                        //$invoice_no = date("YmdHis");
                      //  $order_status = 'Awaiting Confirmation';
                    }
                    if ($image > 0) {
                        foreach ($_FILES['video']['tmp_name'] as $key => $tmp_name) {
                            $img_name = $key . $_FILES['video']['name'][$key];
                            $img_size = $_FILES['video']['size'][$key];
                            $img_tmp = $_FILES['video']['tmp_name'][$key];
                            $ext = getExtension($img_name);
                            if (strlen($img_name) > 0) {
                                if ($img_size < (50000 * 50000)) {
                                    if (in_array($ext, $img_format)) {
                                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                        $actual_image_path = 'images/job_image/' . $actual_image_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                        $this->db->set('video', $actual_image_name); //value that used to update column  
                                        $this->db->where('user_id', $user_id); //which row want to upgrade  
                                        $this->db->update('jobs_user_profile_master'); 
                                        }
                                    }
                                }
                            }
                        }
                    }
                    simple_json_output(array('status' => 200, 'message' => 'success','data'=> $result));
                } else {
                    simple_json_output(array("status" => 201, "message" => "fail" ,'data'=> array()));
                }
            }
        }
    }
    
    
    public function user_profile_background(){
        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            $resume_head = $this->input->post('resume_head');
            if ($user_id == ""  ||  empty($_FILES["background"]["name"])) {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields1'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields1"));
            } else {
                $result = $this->JobsprofileModel->user_profile_resume($user_id, $resume_head);
                
                $date = date('Y-m-d'); 
                if ($result != '') {
                    // $order_id = $result['order_id'];
                   
                    include('s3_config.php');
                    if (!empty($_FILES["background"]["name"])) {
                        $image = count($_FILES['background']['name']);
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP","pdf");
                        include('s3_config.php');
                        date_default_timezone_set('Asia/Calcutta');
                        //$invoice_no = date("YmdHis");
                      //  $order_status = 'Awaiting Confirmation';
                    }
                    if ($image > 0) {
                        foreach ($_FILES['background']['tmp_name'] as $key => $tmp_name) {
                            $img_name = $key . $_FILES['background']['name'][$key];
                            $img_size = $_FILES['background']['size'][$key];
                            $img_tmp = $_FILES['background']['tmp_name'][$key];
                            $ext = getExtension($img_name);
                            if (strlen($img_name) > 0) {
                                if ($img_size < (50000 * 50000)) {
                                    if (in_array($ext, $img_format)) {
                                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                        $actual_image_path = 'images/job_image/' . $actual_image_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                        $this->db->set('background', $actual_image_name); //value that used to update column  
                                        $this->db->where('user_id', $user_id); //which row want to upgrade  
                                        $this->db->update('jobs_user_profile_master'); 
                                        }
                                    }
                                }
                            }
                        }
                    }
                    simple_json_output(array('status' => 200, 'message' => 'success','data'=> $result));
                } else {
                    simple_json_output(array("status" => 201, "message" => "fail" ,'data'=> array()));
                }
            }
        }
    }
            //JOBS-EDUCATION
     public function user_profile_education() {
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
                    if ($params['user_id'] == "" || $params['job_education_id'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $job_education_id = $params['job_education_id'];
                       // $highest_qualification = $params['highest_qualification'];
                        $school_name = $params['school_name'];
                        $board_name = $params['board_name'];
                        $medium = $params['medium']; 
                        $percentage = $params['percentage'];
                        $passing_year = $params['passing_year'];
                        $grade = $params['grade'];
                        $specifications = $params['specifications'];
                        $courses = $params['courses'];
                        $id = $params['id'];
                       
                        $resp = $this->JobsprofileModel->user_profile_education($user_id,$job_education_id,$school_name,$board_name, $medium, $percentage, $passing_year,$grade,$specifications,$courses,$id );
                    }
                    simple_json_output($resp);
               }
            }
        }
    }
    
    //JOBS-EDUCATION-XII
  /*  public function add_jobs_user_profile_education_college() {
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
    }  */
            
            //JOBS-EDUCATION-GRADUATE
   /*  public function add_jobs_user_profile_education_graduate() {
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
    } */
                //JOBS-EDUCATION-POSTGRADUATE
 /*    public function add_jobs_user_profile_education_postgraduate() {
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
    }   */
    
            //JOBS-USER-KEYSKILLS
   public function user_profile_key_skills() {
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
                    if ($params['user_id'] == "" || $params['technical_skills'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $technical_skills = $params['technical_skills'];
                        $technical_skills_desc = $params['technical_skills_desc'];

                        $resp = $this->JobsprofileModel->user_profile_key_skills($user_id,$technical_skills,$technical_skills_desc );
                    }
                    simple_json_output($resp);
               }
            }
        }
    }

    
    
           //JOBS-CERTIFICATE
    public function user_profile_certificate() {
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
                    if ($params['user_id'] == "" || $params['certificate_name'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $id = $params['id'];
                        $user_id = $params['user_id'];
                        $certificate_name = $params['certificate_name'];
                        $ce_issued_date = $params['ce_issued_date'];
                        //$ce_year = $params['ce_year'];
                        $ce_description = $params['ce_description']; 
                        $ce_issued_by = $params['ce_issued_by'];
                        $achievement = $params['achievement'];
                       
                        $resp = $this->JobsprofileModel->user_profile_certificate($id,$user_id,$certificate_name,$ce_issued_date, $ce_description, $ce_issued_by,$achievement );
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
            //JOBS-previous-job-details
    public function user_profile_previous_job() {
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
                    if ($params['user_id'] == "" || $params['company_name'] == "" || $params['company_type'] == "" || $params['employment_type'] == "" || $params['designation'] == "" || $params['location'] == ""  || $params['work_experience'] == "" || $params['work_start'] == "" || $params['work_end'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $id = $params['id'];
                        $user_id = $params['user_id'];
                        $company_name = $params['company_name'];
                        $company_type = $params['company_type'];
                        $designation = $params['designation'];
                        $employment_type = $params['employment_type'];
                        $location = $params['location'];
                        $work_experience = $params['work_experience']; 
                        $work_start = $params['work_start'];
                        $work_end = $params['work_end'];
                        $desc_profile = $params['desc_profile'];
                       
                        $resp = $this->JobsprofileModel->user_profile_previous_job($id,$user_id,$company_name,$company_type,$designation,$employment_type,$location, $work_experience, $work_start,$work_end,$desc_profile );
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    
            //JOBS-preferred-job-details
    public function user_profile_preferred_job() {
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
                    if ($params['user_id'] == "" || $params['job_type'] == "" || $params['job_location'] == "" || $params['job_position'] == ""  || $params['min_salary'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $job_type = $params['job_type'];
                        $job_location = $params['job_location'];
                        $job_position = $params['job_position'];
                        $min_salary = $params['min_salary'];

                        $resp = $this->JobsprofileModel->user_profile_preferred_job($user_id,$job_type,$job_location,$job_position,$min_salary );
                    }
                    simple_json_output($resp);
                }
            }
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
  
  
  // jobs-listing
    public function jobs_listing() {
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
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $type = $params['job_type'];
                        $page = $params['page'];
                        $sort_salary = $params['sort_salary']; //sort_by_salary
                        /* filters*/
                        $location = $params['location'];
                        $salary = $params['salary'];
                        $work_experience = $params['work_experience'];
                        //$education = $params['education'];

                        $resp = $this->JobsprofileModel->jobs_listing($user_id,$type,$page,$sort_salary,$location,$salary,$work_experience);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
  
  public function jobs_detail_listing() {
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
                    if ($params['user_id'] == "" || $params['job_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $job_id = $params['job_id'];

                        $resp = $this->JobsprofileModel->jobs_detail_listing($user_id,$job_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

public function jobs_ques() {
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
                    if ( $params['job_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        
                        $job_id = $params['job_id'];

                        $resp = $this->JobsprofileModel->jobs_ques($job_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

public function jobs_ques_ans() {
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
                    $user_id = $this->input->post('user_id');
		            $job_id = $this->input->post('job_id');
		            //$question_id = $this->input->post('question_id');
		            $answers = $this->input->post('answer');
		          //  print_r($answers); die();
                    if ( $user_id == "" ||  $job_id == "" || $answers == ""  ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $resp = $this->JobsprofileModel->jobs_ques_ans($user_id,$job_id,$answers);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

  public function favourite_job(){
      
        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['job_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $job_id = $params['job_id'];
                        $user_id = $params['user_id'];
                       
                        $resp = $this->JobsprofileModel->favourite_job($user_id,$job_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    
  }
  
  public function similar_job(){
      
        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['job_id'] == "" || $params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $job_id = $params['job_id'];
                        $user_id = $params['user_id'];

                        $resp = $this->JobsprofileModel->similar_job($job_id,$user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    
  }
  
  public function jobs_company_profile(){
      
        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['company_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $company_id = $params['company_id'];

                        $resp = $this->JobsprofileModel->jobs_company_profile($company_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    
  }
  
  public function jobs_main_cat(){
      
        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];

                        $resp = $this->JobsprofileModel->jobs_main_cat($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
  }
  
    public function cat_job_listing(){

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['category_id'] == "" || $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $category_id = $params['category_id'];
                        $user_id = $params['user_id'];
                        $page = $params['page'];
                        $sort_salary = $params['sort_salary']; //sort_by_salary
                        /* filters*/
                        $location = $params['location'];
                        $salary = $params['salary'];
                        $work_experience = $params['work_experience'];
                        

                        $resp = $this->JobsprofileModel->cat_job_listing($category_id,$user_id,$page,$location,$salary,$work_experience,$sort_salary);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //VIEW-USER-PROFILE
    public function view_user_profile(){

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];

                        $resp = $this->JobsprofileModel->view_user_profile($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function view_user_profile_edu()
     {

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];

                        $resp = $this->JobsprofileModel->view_user_profile_edu($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
   //VIEW-USER-PROFILE
    public function languages_known(){

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];

                        $resp = $this->JobsprofileModel->languages_known($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
    public function available(){

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];

                        $resp = $this->JobsprofileModel->available($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
  public function user_dashboard(){

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];

                        $resp = $this->JobsprofileModel->user_dashboard($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
  
  public function delete_details(){
        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="" || $params['type'] =="" || $params['id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $type = $params['type'];
                        $id = $params['id'];
                       // $job_education_id = $params['job_education_id'];

                        $resp = $this->JobsprofileModel->delete_details($user_id,$type,$id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
  public function application_status(){

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="" || $params['job_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $job_id = $params['job_id'];

                        $resp = $this->JobsprofileModel->application_status($user_id,$job_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function company_job_listing()
    {

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="" || $params['company_id'] =="" || $params['vendor_type'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $company_id = $params['company_id'];
                        $vendor_type = $params['vendor_type'];
                        $page = $params['page'];

                        $resp = $this->JobsprofileModel->company_job_listing($user_id,$company_id,$vendor_type,$page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function job_alert()
    {
        
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
                    if ($params['user_id'] == "" || $params['study'] == "" || $params['work_exp'] == "" || $params['location'] == "" || $params['alert_name'] == ""  ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $id = $params['id'];
                        $user_id = $params['user_id'];
                        $study = $params['study'];
                        $department = $params['department'];
                       // $highest_qualification = $params['highest_qualification'];
                        $work_exp = $params['work_exp'];
                        $location = $params['location'];
                        $alert_name = $params['alert_name'];
                        $salary = $params['salary'];
                        
                       
                        $resp = $this->JobsprofileModel->job_alert($id,$user_id,$study,$department,$work_exp,$location, $alert_name,$salary );
                    }
                    simple_json_output($resp);
               }
            }
        }
    
    }
    
    public function delete_job_alert()
    {

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="" || $params['id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $id = $params['id'];
                       // $job_education_id = $params['job_education_id'];

                        $resp = $this->JobsprofileModel->delete_job_alert($user_id,$id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
  public function view_alert_listing()
  {

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];

                        $resp = $this->JobsprofileModel->view_alert_listing($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
    public function view_alert_jobs()
    {

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="" || $params['page'] =="" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $page = $params['page'];

                        $resp = $this->JobsprofileModel->view_alert_jobs($user_id,$page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function view_recommended_jobs()
    {

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="" || $params['page'] =="" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $page = $params['page'];

                        $resp = $this->JobsprofileModel->view_recommended_jobs($user_id,$page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
  public function job_title()
  {
        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                    

                        $resp = $this->JobsprofileModel->job_title($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
  }
  
  
   public function industry_type()
  {
        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                    

                        $resp = $this->JobsprofileModel->industry_type($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
  }
   public function company_review() {
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
                    if ($params['user_id'] == "" || $params['company_id'] == "" ||$params['current_employ'] == "" || $params['employment_status'] == "" ||$params['review_title'] == "" || $params['company_pros'] == "" || $params['company_cons'] == "" || $params['comment_or_advise'] == "" ||$params['rating'] == "" || $params['designation'] == "" ||$params['base_salary'] == "" || $params['anonymously'] == ""   ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $company_id = $params['company_id'];
                       // $highest_qualification = $params['highest_qualification'];
                        $current_employ = $params['current_employ'];
                        $employment_status = $params['employment_status'];
                        $review_title = $params['review_title']; 
                        $company_pros = $params['company_pros'];
                        $company_cons = $params['company_cons'];
                        $comment_or_advise = $params['comment_or_advise'];
                        $rating = $params['rating'];
                        $designation = $params['designation'];
                        $base_salary = $params['base_salary'];
                        $anonymously = $params['anonymously'];
                       
                        $resp = $this->JobsprofileModel->company_review($user_id,$company_id,$current_employ,$employment_status, $review_title, $company_pros, $company_cons,$comment_or_advise,$rating,$designation,$base_salary,$anonymously );
                    }
                    simple_json_output($resp);
               }
            }
        }
    }
  
  public function view_company_reviews()
  {

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="" || $params['page'] =="" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $company_id = $params['company_id'];
                        $page = $params['page'];

                        $resp = $this->JobsprofileModel->view_company_reviews($user_id,$company_id,$page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
  public function delete_review()
  {

    $this->load->model('JobsprofileModel');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method != 'POST') {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
            ));
        } else
            {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="" || $params['company_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $company_id = $params['company_id'];
                       // $job_education_id = $params['job_education_id'];

                        $resp = $this->JobsprofileModel->delete_review($user_id,$company_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
  public function delete_profile_image()
  {

    $this->load->model('JobsprofileModel');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method != 'POST') {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
            ));
        } else
            {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->JobsprofileModel->delete_profile_image($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
  public function delete_profile_resume()
  {

    $this->load->model('JobsprofileModel');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method != 'POST') {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
            ));
        } else
            {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->JobsprofileModel->delete_profile_resume($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function delete_background()
  {

    $this->load->model('JobsprofileModel');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method != 'POST') {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
            ));
        } else
            {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->JobsprofileModel->delete_background($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function delete_profile_video()
  {

    $this->load->model('JobsprofileModel');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method != 'POST') {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
            ));
        } else
            {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->JobsprofileModel->delete_profile_video($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    
    public function university_list()
    {

    $this->load->model('JobsprofileModel');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method != 'POST') {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
            ));
        } else
            {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        $user_id = $params['user_id'];
                        $name = $params['name'];
                        $resp = $this->JobsprofileModel->university_list($user_id,$name);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
     public function board_list()
    {

    $this->load->model('JobsprofileModel');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method != 'POST') {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
            ));
        } else
            {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        $user_id = $params['user_id'];
                        $name = $params['name'];
                        $resp = $this->JobsprofileModel->board_list($user_id,$name);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function state_list()
    {

    $this->load->model('JobsprofileModel');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method != 'POST') {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
            ));
        } else
            {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        $user_id = $params['user_id'];
                        $name = $params['name'];
                        $resp = $this->JobsprofileModel->state_list($user_id,$name);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function city_list()
    {

    $this->load->model('JobsprofileModel');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method != 'POST') {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
            ));
        } else
            {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="" || $params['state_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        $user_id = $params['user_id'];
                        $state_id = $params['state_id'];
                        $name = $params['name'];
                        $resp = $this->JobsprofileModel->city_list($user_id,$state_id,$name);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function religion_list()
    {

    $this->load->model('JobsprofileModel');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method != 'POST') {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
            ));
        } else
            {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        $user_id = $params['user_id'];
                         $page = $params['page'];
                        $name = $params['name'];
                        $resp = $this->JobsprofileModel->religion_list($user_id,$page,$name);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function job_share(){

    $this->load->model('JobsprofileModel');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method != 'POST') {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
            ));
        } else
            {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        $user_id = $params['user_id'];
                        $name = $params['name'];
                        $resp = $this->JobsprofileModel->job_share($user_id,$name);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
 
  public function languages_add()
  {

        $this->load->model('JobsprofileModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] ==""|| $params['languages_known'] ==""|| $params['proficiency'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $languages_known = $params['languages_known'];
                        $proficiency = $params['proficiency'];
                        $language_effi = $params['language_effi'];

                        $resp = $this->JobsprofileModel->languages_add($user_id,$languages_known,$proficiency,$language_effi);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function jobs_filter_cri()
    {

    $this->load->model('JobsprofileModel');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method != 'POST') {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
            ));
        } else
            {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        $user_id = $params['user_id'];
                        $resp = $this->JobsprofileModel->jobs_filter_cri($user_id);
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