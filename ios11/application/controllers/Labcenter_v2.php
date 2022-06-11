<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Labcenter_v2 extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
         $this->load->model('LabcenterModel_v2');
         
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }
    
     //Added by Swapnali 
   public function lab_tests(){ 
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
                        $resp = array('status' => 400, 'message' => 'please enter user_id');
                    } else {
                        $user_id = $params['user_id'];
                        
                        if(empty($params['home_delivery'])){
                            $home_delivery = 0;
                        } else {
                            $home_delivery = $params['home_delivery'];
                        }
                        
                        if(empty($params['most_popular'])){
                            $most_popular = 0;
                        } else {
                            $most_popular = $params['most_popular'];
                        }
                        
                        if(empty($params['cat_id'])){
                            $cat_id = 0;
                        } else {
                            $cat_id = $params['cat_id'];
                        }
                        
                        if(array_key_exists("term",$params)){
                            $term = $params['term'];
                        } else {
                            $term = "";
                            
                        }
                        
                         if(array_key_exists("per_page",$params)){
                            $per_page = $params['per_page'];
                        } else {
                            $per_page = 0;
                        }
                        
                        if(array_key_exists("page_no",$params)){
                            $page_no = $params['page_no'];
                        } else {
                            $page_no = 0;
                        }
                        
                        $responseData = $this->LabcenterModel_v2->lab_tests($user_id,$home_delivery,$most_popular,$cat_id,$term,$per_page,$page_no);
                        $response  = $responseData['pagination'];
                        
                        $resp['status'] = 200;
                        $resp['data_count'] = $response['data_count'];
                        $resp['per_page'] = $response['per_page'];
                        $resp['current_page'] = $response['current_page'];
                        $resp['first_page'] = $response['first_page'];
                        $resp['last_page'] = $response['last_page'];
                        
                        $resp['data'] = $responseData['data'];
                        
                    }
                    // simple_json_output($resp);
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function lab_vendor_by_test(){ 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['test_id'] == "")  {
                        $resp = array('status' => 400, 'message' => 'please enter user_id and test_id');
                    } else {
                        $user_id = $params['user_id'];
                        $test_id = $params['test_id'];
                        
                        
                        if(array_key_exists("per_page",$params)){
                            $per_page = $params['per_page'];
                        } else {
                            $per_page = 0;
                        }
                        
                        if(array_key_exists("page_no",$params)){
                            $page_no = $params['page_no'];
                        } else {
                            $page_no = 0;
                        }
                        $resp = $this->LabcenterModel_v2->lab_vendor_by_test($user_id,$test_id,$per_page,$page_no);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function lab_test_by_vendor(){ 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['vendor_id'] == "")  {
                        $resp = array('status' => 400, 'message' => 'please enter user_id and vendor_id');
                    } else {
                        $user_id = $params['user_id'];
                        $vendor_id = $params['vendor_id'];
                        
                        if(array_key_exists("per_page",$params)){
                            $per_page = $params['per_page'];
                        } else {
                            $per_page = 0;
                        }
                        
                        if(array_key_exists("page_no",$params)){
                            $page_no = $params['page_no'];
                        } else {
                            $page_no = 0;
                        }
                        
                        if(array_key_exists("term",$params)){
                            $term = $params['term'];
                        } else {
                            $term = "";
                        }
                        
                       
                        $resp = $this->LabcenterModel_v2->lab_test_by_vendor($vendor_id,$per_page,$page_no,$term);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function lab_packages(){ 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" )  {
                        $resp = array('status' => 400, 'message' => 'please enter user_id');
                    } else {
                        $user_id = $params['user_id'];
                        
                        if(empty($params['vendor_id'])){
                            $vendor_id = 0;
                        }else{
                            $vendor_id = $params['vendor_id'];
                        }
                        
                        if(array_key_exists("per_page",$params)){
                            $per_page = $params['per_page'];
                        } else {
                            $per_page = 0;
                        }
                        
                        if(array_key_exists("page_no",$params)){
                            $page_no = $params['page_no'];
                        } else {
                            $page_no = 0;
                        }
                        
                        if(!array_key_exists("home_delivery",$params) || $params['home_delivery'] == 0){
                            $home_delivery = "'0','1'";
                        } else {
                            $home_delivery = $params['home_delivery'];
                            
                        }
                        
                        if(!array_key_exists("body_checkup",$params) || $params['body_checkup'] == 0){
                            $body_checkup = "'0','1'"; //default 0, 1  = all packages
                        } else {
                            $body_checkup = $params['body_checkup'];
                        }
                        
                        if(array_key_exists("lat",$params)){
                            $lat = $params['lat'];
                        } else {
                            $lat = 0;
                            
                        }
                        if(array_key_exists("lng",$params)){
                            $lng = $params['lng'];
                        } else {
                            $lng = 0;
                            
                        }
                        
                        if(array_key_exists("term",$params)){
                            $term = $params['term'];
                        } else {
                            $term = "";
                            
                        }
                        
                        
                        
                        $resp = $this->LabcenterModel_v2->lab_packages($vendor_id,$per_page,$page_no,$home_delivery,$body_checkup,$lat,$lng,$term);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function lab_search_test_packages(){ 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" )  {
                        $resp = array('status' => 400, 'message' => 'please enter user_id');
                    } else {
                        $user_id = $params['user_id'];
                        $term = $params['term'];
                        $search_for = $params['search_for'];
                        
                        if(array_key_exists("per_page",$params)){
                            $per_page = $params['per_page'];
                        } else {
                            $per_page = 10;
                        }
                        
                        if(array_key_exists("page_no",$params)){
                            $page_no = $params['page_no'];
                        } else {
                            $page_no = 1;
                        }
                           
                        
                        $resp = $this->LabcenterModel_v2->lab_search_test_packages($user_id,$term,$search_for,$per_page,$page_no);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function lab_test_by_tests(){ 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                    $test_id = $params['test_id'];
                    if ($user_id == "" || $test_id == "" )  {
                        $resp = array('status' => 400, 'message' => 'please enter user_id and test_id');
                        simple_json_output($resp);
                    } else {
                        if(array_key_exists("lat",$params)){
                            $lat = $params['lat'];
                        } else {
                            $lat = 0;
                            
                        }
                        
                        if(array_key_exists("lng",$params)){
                            $lng = $params['lng'];
                        } else {
                            $lng = 0;
                            
                        }
                        
                        if(array_key_exists("page_no",$params)){
                            $page_no = $params['page_no'];
                        } else {
                            $page_no = 0;
                            
                        }
                        
                        if(array_key_exists("per_page",$params)){
                            $per_page = $params['per_page'];
                        } else {
                            $per_page = 0;
                            
                        }
                        // $lat = $params['lat'];
                        // $lng = $params['lng'];
                        $resp = $this->LabcenterModel_v2->lab_test_by_tests($user_id,$test_id,$lat,$lng,$page_no,$per_page);
                        json_outputs($resp);
                    }
                    
                }
            }
        }
    }
    
    // from old labcenter
    
     public function labcenter_list() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['lat'] == "" || $params['lng'] == "" || $params['category_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $lat = $params['lat'];
                        $lng = $params['lng'];
                        $category_id = $params['category_id'];
                        if(!empty($params['hospital_type'])){
                            $hospital_type = $params['hospital_type'];
                        } else {
                            $hospital_type = 0;
                        }
                        $resp = $this->LabcenterModel_v2->labcenter_list($lat, $lng, $user_id, $category_id, $hospital_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function labcenter_details()
    {
        // $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->LabcenterModel_v2->labcenter_details($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
     public function lab_booking() {
       $this->load->model('UserstackModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                    if ($params['total_cost'] == "" || $params['discounted_price'] == "" || $params['user_id'] == "" || $params['listing_id'] == "" || $params['address_line1']=="" || $params['address_line2']=="" || $params['user_name'] == "" || $params['user_email'] == "" || $params['user_mobile'] == "" || $params['user_gender'] == ""  || $params['branch_name'] == "" || $params['vendor_id'] == "" || $params['payment_mode'] == "" ||  $params['status'] == "" ||$params['trail_booking_date'] == "" || $params['trail_booking_time'] == "" ||   $params['city'] == "" || $params['state'] == "" || $params['pincode'] == "" ) {
                        
                        $required = "required fields are :total_cost, discounted_price, user_id, listing_id, address_line1, address_line2, user_name, user_email, user_mobile, user_gender, branch_name, vendor_id, payment_mode, status, trail_booking_date, trail_booking_time,  city, state, pincode";
                        
                        $resp = array('status' => 400, 'message' => 'please enter all fields','required_fields' => $required);
                    } else {
                        //print_r($params); die();
                        $user_id            = $params['user_id'];
                        $listing_id         = $params['listing_id'];
                        $package_id         = $params['package_id'];
                        $address_line1      = $params['address_line1'];
                        $address_line2      = $params['address_line2'];
                        $user_name          = $params['user_name'];
                        $mobile             = $params['user_mobile'];
                        $email              = $params['user_email'];
                        $gender             = $params['user_gender'];
                        $branch_id          = $params['branch_id'];
                        $branch_name        = $params['branch_name'];
                        $vendor_id          = $params['vendor_id'];
                        $status             = $params['status'];
                        $payment_mode       = $params['payment_mode'];
                        $trail_booking_date = $params['trail_booking_date'];
                        $trail_booking_time = $params['trail_booking_time'];
                        $booking_location   = $params['booking_location'];
                        $booking_address    = $params['booking_address'];
                        $booking_mobile     = $params['booking_mobile'];
                        $test_ids           = $params['test_ids'];
                        $patient_id         = $params['patient_id'];
                        $at_home            = $params['at_home'];
                        $city               = $params['city'];
                        $state              = $params['state'];
                        $pincode            = $params['pincode'];
                        $address_id         = $params['address_id'];
                        $total_cost            = $params['total_cost'];
                        $discounted_price         = $params['discounted_price'];
                        
                        $amount = $total_cost - $discounted_price;
                        
                        if(array_key_exists('booking_id',$params) && $params['booking_id'] != ''){
                            $booking_id   = $params['booking_id'];
                        }
                        else{
                            $booking_id = date('YmdHis');
                        }
                        
                        
                        $resp = $this->LabcenterModel_v2->lab_booking($user_id, $listing_id, $address_line1, $address_line2, $package_id, $user_name, $mobile, $email, $gender, $branch_id, $branch_name, $vendor_id, $status, $payment_mode, $trail_booking_date, $trail_booking_time,  $booking_location, $booking_address, $booking_mobile,$test_ids, $patient_id, $at_home, $city, $state, $pincode, $address_id,$booking_id,$total_cost,$discounted_price,$amount);
                    }

                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    public function lab_bookings_history(){
       
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
                        // $listing_id = $params['listing_id'];
                        $resp = $this->LabcenterModel_v2->lab_bookings_history($user_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function lab_center_info(){
         $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['vendor_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['vendor_id'];
                        // $listing_id = $params['listing_id'];
                        $resp = $this->LabcenterModel_v2->lab_center_info($user_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }
    
    public function lab_center_search(){
         $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['term'] == "" || $params['user_id'] == "" || $params['per_page'] == "" || $params['page_no'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields term, user_id, per_page, page_no');
                    } else {
                        $term = $params['term'];
                        $user_id = $params['user_id'];
                        $per_page = $params['per_page'];
                        $page_no = $params['page_no'];
                        // $listing_id = $params['listing_id'];
                        $resp = $this->LabcenterModel_v2->lab_center_search($term, $user_id, $per_page, $page_no);
                    }

                    simple_json_output($resp);
                }
            }
        }
    }
  
}