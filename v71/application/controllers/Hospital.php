<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hospital extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
          $this->load->model('LedgerModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function hospital_list() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                       
                        if(array_key_exists("category_name",$params)){
                            $category_name = $params['category_name'];
                        } else {
                            $category_name = '';
                        }
                        //$page=$params['page'];
                       if(array_key_exists("page",$params)){
                            $page = $params['page'];
                        } else {
                            $page = '';
                        }
                        if(array_key_exists("keyword",$params)){
                            $keyword= $params['keyword'];
                        } else {
                            $keyword= '';
                        }
                        
                        
                        $resp = $this->HospitalModel->hospital_list($latitude, $longitude, $user_id, $category_name,$page,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }


    
     


   // new api for hospital list added by dhaval 
   public function list_category() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $resp = $this->HospitalModel->list_category($latitude, $longitude, $user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function hospital_list_v2() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['lat'] == "" || $params['long'] == "" || $params['cat_id'] == "" || $params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['lat'];
                        $longitude = $params['long'];
                        $category_id = $params['cat_id'];
                        $page=$params['page'];
                        $type=$params['type'];
                       // $sort=$params['sort'];
                        if(array_key_exists("keyword",$params)){
                            $keyword= $params['keyword'];
                        } else {
                            $keyword= '';
                        }
                         if(array_key_exists("sort",$params)){
                            $sort= $params['sort'];
                        } else {
                            $sort= '';
                        }
                        
                        $resp = $this->HospitalModel->hospital_list_v2($user_id, $latitude, $longitude, $category_id,$page,$type,$keyword,$sort);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function add_favourite_hospital() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id']; 
                        $listing_id = $params['listing_id']; 
                        $resp = $this->HospitalModel->add_favourite_hospital($user_id, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function hospital_list_surgery() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['lat'] == "" || $params['long'] == "" || $params['cat_id'] == "" || $params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['lat'];
                        $longitude = $params['long'];
                        $category_id = $params['cat_id'];
                        $page=$params['page'];
                        $type=$params['type'];
                        if(array_key_exists("keyword",$params)){
                            $keyword= $params['keyword'];
                        } else {
                            $keyword= '';
                        }
                        if(array_key_exists("sort",$params)){
                            $sort= $params['sort'];
                        } else {
                            $sort= '';
                        }
                        if(array_key_exists("filter_id",$params)){
                            $filter_id= $params['filter_id'];
                        } else {
                            $filter_id= 0;
                        }
                        $resp = $this->HospitalModel->hospital_list_surgery($user_id, $latitude, $longitude, $category_id,$page,$type,$keyword,$sort,$filter_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
   // end here
   // new api for wellness list added by dhaval 
  
    public function wellness_list_v2() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['lat'] == "" || $params['long'] == "" || $params['cat_id'] == "" || $params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['lat'];
                        $longitude = $params['long'];
                        $category_id = $params['cat_id'];
                        $page=$params['page'];
                        $type=$params['type'];
                        if(array_key_exists("keyword",$params)){
                            $keyword= $params['keyword'];
                        } else {
                            $keyword= '';
                        }
                         if(array_key_exists("sort",$params)){
                            $sort= $params['sort'];
                        } else {
                            $sort= '';
                        }
                        
                        $resp = $this->HospitalModel->wellness_list_v2($user_id, $latitude, $longitude, $category_id,$page,$type,$keyword,$sort);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    public function wellness_list_surgery() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['lat'] == "" || $params['long'] == "" || $params['cat_id'] == "" || $params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['lat'];
                        $longitude = $params['long'];
                        $category_id = $params['cat_id'];
                        $page=$params['page'];
                        $type=$params['type'];
                        if(array_key_exists("keyword",$params)){
                            $keyword= $params['keyword'];
                        } else {
                            $keyword= '';
                        }
                        if(array_key_exists("sort",$params)){
                            $sort= $params['sort'];
                        } else {
                            $sort= '';
                        }
                        if(array_key_exists("filter_id",$params)){
                            $filter_id= $params['filter_id'];
                        } else {
                            $filter_id= '';
                        }
                        $resp = $this->HospitalModel->wellness_list_surgery($user_id, $latitude, $longitude, $category_id,$page,$type,$keyword,$sort,$filter_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    // old 
    
     public function list_surgey_category() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                      
                         if(array_key_exists("type",$params)){
                            $type= $params['type'];
                        } else {
                            $type= '';
                        }
                        $resp = $this->HospitalModel->list_surgey_category($latitude, $longitude, $user_id,$type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function hospital_detail_surgery() {
        $this->load->model('HospitalModel');
         $this->load->library('ElasticSearch');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['lat'] == "" || $params['long'] == "" || $params['cat_id'] == "" ||  $params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['lat'];
                        $longitude = $params['long'];
                        $page=$params['page'];
                        $type=$params['type'];
                        $hospital_id = $params['hospital_id'];
                            $category_id= $params['cat_id'];
                        if(array_key_exists("keyword",$params)){
                            $keyword= $params['keyword'];
                        } else {
                            $keyword= '';
                        }
                         if(array_key_exists("sort",$params)){
                            $sort= $params['sort'];
                        } else {
                            $sort= '';
                        }
                        
                        $resp = $this->HospitalModel->hospital_detail_surgery($user_id, $latitude, $longitude, $category_id,$page,$type,$keyword,$sort,$hospital_id,$sort);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function hospital_detail_surgery_search() {
        $this->load->model('HospitalModel');
         $this->load->library('ElasticSearch');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['cat_id'] == "" ||  $params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $type=$params['type'];
                        $hospital_id = $params['hospital_id'];
                            $category_id= $params['cat_id'];
                     
                        
                        
                        $resp = $this->HospitalModel->hospital_detail_surgery_search($user_id,$category_id,$type,$hospital_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
   // end here
   

    public function hospital_details() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->HospitalModel->hospital_details($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function doctor_list() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['hospital_id'] == "" || $params['category_name'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
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
    
    public function hospital_doctor_list() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['hospital_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $hospital_id = $params['hospital_id'];
                        $resp = $this->HospitalModel->hospital_doctor_list($hospital_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function hospital_doctor_list_new() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['hospital_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $hospital_id = $params['hospital_id'];
                        $latitude = $params['lat'];
                        $longitude = $params['lng'];
                        $resp = $this->HospitalModel->hospital_doctor_list_new($latitude, $longitude,$hospital_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

 public function hospital_doctor_list_new_v1() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['hospital_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $hospital_id = $params['hospital_id'];
                        $latitude = $params['lat'];
                        $longitude = $params['lng'];
                        $category_id = $params['category_id'];
                        $page = $params['page'];
                        $user_id=$params['user_id'];
                        $resp = $this->HospitalModel->hospital_doctor_list_new_v1($latitude, $longitude,$hospital_id,$category_id,$page,$user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

public function hospital_doctor_list_new_v2() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['hospital_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $hospital_id = $params['hospital_id'];
                        $latitude = $params['lat'];
                        $longitude = $params['lng'];
                        $category_id = $params['category_id'];
                        $page = $params['page'];
                        $user_id=$params['user_id'];
                        $resp = $this->HospitalModel->hospital_doctor_list_new_v2($latitude, $longitude,$hospital_id,$category_id,$page,$user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function hospitals_appointment() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['hospital_id'] == "" || $params['surgery_id'] == "" || $params['patient_name'] == "" || $params['gender'] == "" || $params['age'] == "" || $params['mobile'] == "" || $params['ts1_date'] == "" || $params['ts1_time'] == "" || $params['ts2_date'] == "" || $params['ts2_time'] == "" || $params['medical_condition'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $hospital_id = $params['hospital_id'];
                        $surgery_id = $params['surgery_id'];
                        $patient_name = $params['patient_name'];
                        $gender = $params['gender'];
                        $age = $params['age'];
                        $mobile = $params['mobile'];
                        $ts1_date = $params['ts1_date'];
                        $ts1_time = $params['ts1_time'];
                        $ts2_date = $params['ts2_date'];
                        $ts2_time = $params['ts2_time'];
                        $medical_condition = $params['medical_condition'];
                        $resp = $this->HospitalModel->hospitals_appointment($user_id, $hospital_id, $surgery_id, $patient_name, $gender, $age, $mobile, $ts1_date, $ts1_time, $ts2_date, $ts2_time, $medical_condition);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function add_review() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['rating'] == "" || $params['review'] == "" || $params['service'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $rating = $params['rating'];
                        $review = $params['review'];
                        $service = $params['service'];
                        $resp = $this->HospitalModel->add_review($user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

 public function edit_review() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['rating'] == "" || $params['review'] == "" || $params['service'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $review_id = $params['review_id'];
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $rating = $params['rating'];
                        $review = $params['review'];
                        $service = $params['service'];
                        $resp = $this->HospitalModel->edit_review($review_id,$user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }


    public function review_list() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->HospitalModel->review_list($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
      public function review_with_comment() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->HospitalModel->review_with_comment($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    

    public function review_like() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->HospitalModel->review_like($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $comment = $params['comment'];
                        $resp = $this->HospitalModel->review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_like() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->HospitalModel->review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_list() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->HospitalModel->review_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function get_hospital_package(){
        
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->HospitalModel->get_hospital_package($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    } 
    
    public function get_hospital_surgery(){
        
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->HospitalModel->get_hospital_surgery($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    } 
    
    public function get_hospital_wards(){
        
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" && $params['package_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $package_id = $params['package_id'];
                        $resp = $this->HospitalModel->get_hospital_wards($user_id,$package_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    } 
    
    public function hospital_surgery_package() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->HospitalModel->hospital_surgery_package($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function hospital_booking() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    ///print_r($params);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['package_id'] == "" ||  $params['name'] == "" || $params['gender'] == "" || $params['patient_name'] == "" || $params['patient_relation'] == "" || $params['patient_gender'] == "" || $params['amount'] == "" || $params['patient_dob'] == "" || $params['patient_preferred_date'] == "" ) {
                       // $resp = array('status' => 400, 'message' => 'please enter fields');
                        json_output(400, array('status' => 400, 'message' => 'please enter fields'));
                      //  echo json_output($resp);
                         //echo "undashfdsgklnhfsdgdfghgdlkhnzfxfdf";
                    } else {
                       // echo "dashfdsgklnhfsdgdfghgdlkhnzfxfdf";
                        $dob=$params['patient_dob'];
                        $age = (date('Y') - date('Y',strtotime($dob)));
                        
                        $user_id            = $params['user_id'];
                        $listing_id         = $params['listing_id'];
                        $package_id         = $params['package_id'];
                        $ward_id            = $params['ward_id'];
                        $name               = $params['name'];
                        $gender             = $params['gender'];
                        $patiente_name      = $params['patient_name'];
                        $patient_relation   = $params['patient_relation'];
                        $patient_dob        = $params['patient_dob'];
                        $patient_gender     = $params['patient_gender'];
                        $amount             = $params['amount'];
                        $patient_age        = $age;
                       
                        $emergency          = $params['emergency'];   
                        $patient_preferred_date = $params['patient_preferred_date'];
                         $patient_allergies  = $params['patient_allergies'];
                        $patient_addiction  = $params['patient_addiction'];
                        $ambulance_pickup_address = $params['ambulance_pickup_address'];
                        $ambulance_drop_address = $params['ambulance_drop_address'];
                        //addded for patient details 
                        if($params['patient_id'] != '' || isset($params['patient_id']))
                        {$patient_id = $params['patient_id'];}
                        else{
                         $patient_id = 0;   
                        }
                        $resp1               = $this->HospitalModel->hospital_booking($user_id,$listing_id,$package_id,$ward_id,$name,$gender, $patiente_name, $patient_relation, $patient_dob, $patient_gender, $amount, $patient_age, $patient_allergies, $emergency, $patient_preferred_date, $patient_addiction, $ambulance_pickup_address,$patient_id, $ambulance_drop_address);
                        if($resp1 != "")
                        {
                             json_output(200, array('status' => 200, 'message' => 'Success', 'booking_id' => $resp1));
                        }
                        else
                        {
                             //$resp = array('status' => 400, 'message' => 'Unable to Book Package');
                             json_output(200, array('status' => 400, 'message' => 'Unable to Book Package'));
                        }
                        
                    }
                    
                   
                }
            }
        }
    }
    
     public function hospital_booking_v2() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    ///print_r($params);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['package_id'] == "" ||  $params['amount'] == ""  || $params['patient_preferred_date'] == "" ) {
                       // $resp = array('status' => 400, 'message' => 'please enter fields');
                        json_output(400, array('status' => 400, 'message' => 'please enter fields'));
                      //  echo json_output($resp);
                         //echo "undashfdsgklnhfsdgdfghgdlkhnzfxfdf";
                    } 
                    else {
                       // echo "dashfdsgklnhfsdgdfghgdlkhnzfxfdf";
                        $user_id            = $params['user_id'];
                        $listing_id         = $params['listing_id'];
                        $package_id         = $params['package_id'];
                        $ward_id            = $params['ward_id'];
                        $amount             = $params['amount'];
                        $emergency          = $params['emergency'];   
                        $patient_preferred_date = $params['patient_preferred_date'];
                        $ambulance_pickup_address = $params['ambulance_pickup_address'];
                        $ambulance_drop_address = $params['ambulance_drop_address'];
                        $patient_allergies  = $params['patient_allergies'];
                        $patient_addiction  = $params['patient_addiction'];
                        //addded for patient details 
                        if($params['patient_id'] != '' || isset($params['patient_id']))
                        {$patient_id = $params['patient_id'];}
                        else{
                         $patient_id = 0;   
                        }
                        $resp1               = $this->HospitalModel->hospital_booking_v2($user_id,$listing_id,$package_id,$ward_id,$amount, $emergency, $patient_preferred_date, $ambulance_pickup_address,$patient_id, $ambulance_drop_address,$patient_allergies,$patient_addiction);
                        if($resp1 != "")
                        {
                             json_output(200, array('status' => 200, 'message' => 'Success', 'booking_id' => $resp1));
                        }
                        else
                        {
                             //$resp = array('status' => 400, 'message' => 'Unable to Book Package');
                             json_output(200, array('status' => 400, 'message' => 'Unable to Book Package'));
                        }
                        
                    }
                    
                   
                }
            }
        }
    }
    public function hospital_appointment_list()
    {
         $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->HospitalModel->hospital_appointment_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    //********************** added by zak for wellness clinic ***********************
    public function wellness_clinic_list() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['latitude'] == "" || $params['longitude'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                       
                         if(array_key_exists("category_name",$params)){
                            $category_name = $params['category_name'];
                        } else {
                            $category_name = '';
                        }
                        //$page=$params['page'];
                        if(array_key_exists("page",$params)){
                            $page = $params['page'];
                        } else {
                            $page = '';
                        }
                        if(array_key_exists("keyword",$params)){
                            $keyword= $params['keyword'];
                        } else {
                            $keyword= '';
                        }
                        
                        
                        $resp = $this->HospitalModel->wellness_clinic_list($latitude, $longitude, $user_id, $category_name,$page,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    //***********************added by zak for wellness clinic details**************************
      public function wellness_clinic_details() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->HospitalModel->wellness_clinic_details($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
        //********************** added by zak for nursing metarinity list  (nursing home list ) ***********************
       public function nursing_home_list() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['latitude'] == "" || $params['longitude'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                         if(array_key_exists("category_name",$params)){
                            $category_name = $params['category_name'];
                        } else {
                            $category_name = '';
                        }
                        //$page=$params['page'];
                        if(array_key_exists("page",$params)){
                            $page = $params['page'];
                        } else {
                            $page = '';
                        }
                        if(array_key_exists("keyword",$params)){
                            $keyword= $params['keyword'];
                        } else {
                            $keyword= '';
                        }
                        
                        
                        $resp = $this->HospitalModel->nursing_home_list($latitude, $longitude, $user_id, $category_name,$page,$keyword);
                        
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    
    //*********************************added by zak for nursing metarinity list (nursing home list) **********************
    
       public function nursing_home_details() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->HospitalModel->nursing_home_details($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
     //*********************************added by Dhaval for Hospital Timing Slot **********************
       public function user_read_slot()
    {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" || $params['consultation_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $hospital_id       = $params['hospital_id'];
                        $doctor_id         = $params['doctor_id'];
                        $consultation_type = $params['consultation_type'];
                        $resp              = $this->HospitalModel->user_read_slot($hospital_id, $doctor_id, $consultation_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
      public function add_bookings()
    {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    //echo $params['user_id']; die;
                    if (!is_numeric($params['user_id']) || $params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields1'
                        );
                    } else if ($params['is_user'] == 0 && ($params['relationship'] == "" || $params['date_of_birth'] == "")) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields2'
                        );
                    } else if ($params['is_patient'] == 1 && $params['patient_id'] == '' && !is_numeric($params['patient_id'])) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields3'
                        );
                    } else {
                        $user_id            = $params['user_id'];
                        $listing_id         = $params['listing_id'];
                        $clinic_id          = $params['doctor_id'];
                        $booking_date       = $params['booking_date'];
                        $booking_time       = $params['booking_time'];
                        $from_time          = $params['from_time'];
                        $to_time            = $params['to_time'];
                        $user_name          = $params['user_name'];
                        $user_mobile        = $params['user_mobile'];
                        $user_email         = $params['user_email'];
                        $user_gender        = $params['user_gender'];
                        $is_user            = $params['is_user'];
                        $is_patient         = $params['is_patient'];
                        $patient_id         = $params['patient_id'];
                        $health_condition   = $params['health_condition'];
                        $allergies          = $params['allergies'];
                        $heradiatry_problem = $params['heradiatry_problem'];
                        $description        = $params['description'];
                        $relationship       = $params['relationship'];
                        $date_of_birth      = $params['date_of_birth'];
                        $connect_type       = $params['connect_type'];
                        $resp               = $this->HospitalModel->add_bookings($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $is_user, $is_patient, $patient_id, $health_condition, $allergies, $heradiatry_problem, $description, $relationship, $date_of_birth, $connect_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
      public function user_payment_approval()
    {
        $this->load->model('HospitalModel');
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
                    $params             = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id          = $params['doctor_id'];
                    $confirm_reschedule = $params['confirm_reschedule'];
                    $booking_id         = $params['booking_id'];
                    $user_id            = $params['user_id'];
                    if ($params['doctor_id'] == "" || $params['user_id'] == "" || $params['confirm_reschedule'] == "" || $params['booking_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->HospitalModel->user_payment_approval($doctor_id, $confirm_reschedule, $booking_id, $user_id);
                    }
                }
                simple_json_output($resp);
            }
        }
    }
    
    
    
      public function booking_details()
    {
        $this->load->model('HospitalModel');
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
                    if ($params['booking_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'User Id can\'t empty'
                        );
                    } else {
                        $booking_id = $params['booking_id'];
                        $resp       = $this->HospitalModel->booking_details($booking_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    // New Flow For Surgries start 03-01-2020
    public function surgey_category() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $type = $params['type'];
                        $resp = $this->HospitalModel->surgey_category($latitude,$longitude,$user_id,$type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function view_all_category() {
        $this->load->model('HospitalModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HospitalModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $page=$params['page'];
                        $resp = $this->HospitalModel->view_all_category($latitude,$longitude,$user_id,$page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
}
