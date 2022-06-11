<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dental_clinic extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
    //-----------------------------------new------------------------------------
    public function featured_dental_clinic_list()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                       // $type    = $params['type']; //1-featured,
                         
                        $resp = $this->Dental_clinic_model->featured_dental_clinic_list($user_id,$lat,$lng);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function special_packages_list()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                        $page    = $params['page'];
                        $sort    = $params['sort'];
                         
                        $resp = $this->Dental_clinic_model->special_packages_list($user_id,$lat,$lng,$page,$sort);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function recommended_treatments_list()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                        $page    = $params['page'];
                        $sort    = $params['sort'];
                         
                        $resp = $this->Dental_clinic_model->recommended_treatments_list($user_id,$lat,$lng,$page,$sort);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function view_package()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['package_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id     = $params['listing_id'];
                        $package_id     = $params['package_id'];
                        $type           = $params['type'];   //1-package 2-treatment
                        
                        $resp = $this->Dental_clinic_model->view_package($user_id,$listing_id,$package_id,$type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function dentist_branch_list()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['lat'] == "" || $params['lng'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                        $page    = $params['page'];
                        
                        $resp = $this->Dental_clinic_model->dentist_branch_list($user_id,$listing_id,$lat,$lng,$page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function nearby_dental_clinic_list()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                        $page    = $params['page'];
                        $listing_id1    = $params['listing_id']; //if type==5 ie. branch list
                        $type    = $params['type']; //1-nearby, 2-favourite, 3-free-consultancy, 4-mba , 5- branch list
                             if (array_key_exists("keyword",$params))
                                   {
                                      $keyword    = $params['keyword'];
                                   }else
                                   {
                                   $keyword    = "";
                                    }
                       
                        $resp = $this->Dental_clinic_model->nearby_dental_clinic_list($user_id,$lat,$lng,$page,$listing_id1,$type,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function dental_clinic_profile()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $user_id = $params['user_id'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                      //  $type    = $params['type'];
                         
                        $resp = $this->Dental_clinic_model->dental_clinic_profile($user_id,$listing_id,$lat,$lng);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function favourite_dental_clinic()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $user_id = $params['user_id'];
                       
                        $resp = $this->Dental_clinic_model->favourite_dental_clinic($user_id,$listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function doctor_category()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctors_type_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $doctors_type_id = $params['doctors_type_id'];
                           if (array_key_exists("doctors_type_name",$params))
                   {
                     $doctors_type_name = $params['doctors_type_name'];
                   }
               else
                   {
                   $doctors_type_name = "";
                    }
              
                        $resp            = $this->Dental_clinic_model->doctor_category($doctors_type_id,$doctors_type_name);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function doctor_list()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "" || $params['category_id'] == " "   ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $latitude    = $params['latitude'];
                        $longitude   = $params['longitude'];
                        $category_id = $params['category_id'];
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
                        
                        
                        
                        $resp        = $this->Dental_clinic_model->doctor_list($latitude, $longitude, $user_id, $category_id,$page,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function dental_prescription_booking() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            $lat    = $this->input->post('lat');
            $lng    = $this->input->post('lng');
            $listing_id = $this->input->post('listing_id');
            $address_line1 = $this->input->post('address_line1');
            $address_line2 = $this->input->post('address_line2');
            $city         = $this->input->post('city');
            $state        = $this->input->post('state');
            $pincode      = $this->input->post('pincode');
            $user_name  = $this->input->post('user_name');
            $mobile     = $this->input->post('user_mobile');
            $email      = $this->input->post('user_email');
            $gender     = $this->input->post('user_gender');
            //$branch_id  = $this->input->post('branch_id');
            //$branch_name= $this->input->post('branch_name');
            //$vendor_id  = $this->input->post('vendor_id');
            $status     = $this->input->post('status');
            $payment_mode       = $this->input->post('payment_mode');
            $trail_booking_date = $this->input->post('trail_booking_date');
            $trail_booking_time = $this->input->post('trail_booking_time');
            $booking_date       = $this->input->post('booking_date');
            
            $booking_location   = $this->input->post('booking_location');
            $booking_address    = $this->input->post('booking_address');
            $booking_mobile     = $this->input->post('booking_mobile');
            $prescription_remark     = $this->input->post('prescription_remark');
            //$package_id = $this->input->post('package_id');
            $patient_id = $this->input->post('patient_id'); //family tree user_id
            $booking_id = date('YmdHis');
                       
            if ($user_id == "" || $listing_id == "" || $lat == "" || $lat == "" || empty($_FILES["image"]["name"])) {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields1'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields1"));
            } else {
                $result = $this->Dental_clinic_model->dental_prescription_booking($user_id, $lat, $lng, $listing_id, $address_line1, $address_line2, $city, $state, $pincode, $user_name, $mobile, $email, $gender, $status, $payment_mode, $booking_date, $booking_location, $booking_address, $booking_mobile, $patient_id, $booking_id, $trail_booking_date, $trail_booking_time, $prescription_remark);
                
                $date = date('Y-m-d'); 
                if ($result != '') {
                    $order_id = $result['order_id'];
                   
                    include('s3_config.php');
                    if (!empty($_FILES["image"]["name"])) {
                        $image = count($_FILES['image']['name']);
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        date_default_timezone_set('Asia/Calcutta');
                        //$invoice_no = date("YmdHis");
                        $order_status = 'Awaiting Confirmation';
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
                                        $actual_image_path = 'images/Dental_prescription/' . $actual_image_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                            $this->db->query("INSERT INTO `Dental_clinic_prescription`(`order_id`,`user_id`,`listing_id`, `image`,`status`, `date`, `description` ) VALUES ('$order_id','$user_id','$listing_id', '$actual_image_name','$order_status', '$date', '$prescription_remark')");
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
    public function dental_freeconsultancy_booking() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            $lat    = $this->input->post('lat');
            $lng    = $this->input->post('lng');
            $listing_id = $this->input->post('listing_id');
            $address_line1 = $this->input->post('address_line1');
            $address_line2 = $this->input->post('address_line2');
            $city         = $this->input->post('city');
            $state        = $this->input->post('state');
            $pincode      = $this->input->post('pincode');
            $user_name  = $this->input->post('user_name');
            $mobile     = $this->input->post('user_mobile');
            $email      = $this->input->post('user_email');
            $gender     = $this->input->post('user_gender');
            //$branch_id  = $this->input->post('branch_id');
            //$branch_name= $this->input->post('branch_name');
            //$vendor_id  = $this->input->post('vendor_id');
            $status     = $this->input->post('status');
            $payment_mode       = $this->input->post('payment_mode');
            $trail_booking_date = $this->input->post('trail_booking_date');
            $trail_booking_time = $this->input->post('trail_booking_time');
            $booking_date       = $this->input->post('booking_date');
            
            $booking_location   = $this->input->post('booking_location');
            $booking_address    = $this->input->post('booking_address');
            $booking_mobile     = $this->input->post('booking_mobile');
            
            $package_id = $this->input->post('package_id');
            $patient_id = $this->input->post('patient_id'); //family tree user_id
            $type = $this->input->post('type');
            $sub_type = $this->input->post('sub_type');
            $booking_id = date('YmdHis');
                       
            if ($user_id == "" || $listing_id == "" || $lat == "" || $lat == ""  ) {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields1'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields1"));
            } else {
                $result = $this->Dental_clinic_model->dental_freeconsultancy_booking($user_id, $lat, $lng, $listing_id, $address_line1, $address_line2, $city, $state, $pincode, $user_name, $mobile, $email, $gender, $status, $payment_mode, $booking_date, $booking_location, $booking_address, $booking_mobile, $patient_id, $booking_id, $trail_booking_date, $trail_booking_time,$package_id,$type,$sub_type);
               
                simple_json_output(array('status' => 200, 'message' => 'success','data'=> $result));
               
            }
        }
    }
    public function dentist_time_slot() {
         $this->load->model('Dental_clinic_model');
       
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
              $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['listing_id'];
                        $resp = $this->Dental_clinic_model->dentist_time_slot_v1($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    //-----------------------------------new-------------------------------------
    public function dental_clinic_services()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
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
                       
                        $resp = $this->Dental_clinic_model->dental_clinic_services($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function dental_clinic_list()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                        $type    = $params['type'];
                         
                        $resp = $this->Dental_clinic_model->dental_clinic_list($user_id,$lat,$lng,$type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function dental_booking() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    //print_r($params);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['address_line1']=="" || $params['user_name'] == "" ||  $params['user_mobile'] == "" || $params['user_gender'] == "" || $params['branch_id'] == "" || $params['branch_name'] == "" || $params['vendor_id'] == "" || $params['payment_mode'] == "" || $params['joining_date'] == "" || $params['status'] == "" ||$params['trail_booking_date'] == "" || $params['trail_booking_time'] == "" || $params['booking_location'] == "" || $params['booking_address'] == "" || $params['booking_mobile'] == "" || $params['city'] == "" || $params['state'] == "" || $params['pincode'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id    = $params['user_id'];
                        $listing_id = $params['listing_id'];
                       
                        $address_line1 = $params['address_line1'];
                        $address_line2 = $params['address_line2'];
                        $user_name  = $params['user_name'];
                        $mobile     = $params['user_mobile'];
                        $email      = $params['user_email'];
                        $gender     = $params['user_gender'];
                        $branch_id  = $params['branch_id'];
                        $branch_name= $params['branch_name'];
                        $vendor_id  = $params['vendor_id'];
                        $status     = $params['status'];
                        $payment_mode       = $params['payment_mode'];
                        $trail_booking_date = $params['trail_booking_date'];
                        $trail_booking_time = $params['trail_booking_time'];
                        $joining_date       = $params['joining_date'];
                        $booking_location   = $params['booking_location'];
                        $booking_address    = $params['booking_address'];
                        $booking_mobile     = $params['booking_mobile'];
                        $city         = $params['city'];
                        $state        = $params['state'];
                        $pincode      = $params['pincode'];
                        $package_id = $params['package_id'];
                        $patient_id = $params['patient_id'];
                        /*if($params['booking_id'] != ''){
                            $booking_id   = $params['booking_id'];
                        }
                        else{*/
                            $booking_id = date('YmdHis');
                            //echo 'kadak'.$booking_id;
                        /*}*/
                        
                        
                        $resp = $this->Dental_clinic_model->dental_booking($user_id, $listing_id, $address_line1, $address_line2, $package_id,$user_name, $mobile, $email, $gender, $branch_id, $branch_name, $vendor_id, $status, $payment_mode, $trail_booking_date, $trail_booking_time, $joining_date, $booking_location, $booking_address, $booking_mobile, $city, $state, $pincode, $booking_id, $patient_id);
                    }

                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function dental_prescription_add() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            $lat    = $this->input->post('lat');
            $lng    = $this->input->post('lng');
            
           
         
            if ($lat == "" || $lat == ""  || empty($_FILES["image"]["name"])) {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields1'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields1"));
            } else {
                $listing_id = $this->Dental_clinic_model->dental_prescription_add($user_id, $lat, $lng);
                
                $date = date('Y-m-d'); 
                if ($listing_id != '') {
                    if (!empty($_FILES["image"]["name"])) {
                        $image = count($_FILES['image']['name']);
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        date_default_timezone_set('Asia/Calcutta');
                        $invoice_no = date("YmdHis");
                        $order_status = 'Awaiting Confirmation';
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
                                        $actual_image_path = 'images/Dental_prescription/' . $actual_image_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                            $this->db->query("INSERT INTO `Dental_clinic_prescription`(`user_id`,`listing_id`, `image`,`status`, `date`) VALUES ('$user_id','$listing_id', '$actual_image_name','$order_status', '$date')");
                                        }
                                    }
                                }
                            }
                        }
                    }
                    simple_json_output(array('status' => 200, 'message' => 'success'));
                } else {
                    simple_json_output(array("status" => 201, "message" => "fail"));
                }
            }
        }
    }
    
    public function dental_prescription_add_web() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
             $user_id = $this->input->post('user_id');
             $lat    = $this->input->post('lat');
             $lng    = $this->input->post('lng');
             $image    = $this->input->post('image');
            
           
         
            if ($lat == "" || $lat == ""  || empty($image) ) {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields1'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields1"));
            } else {
                $listing_id = $this->Dental_clinic_model->dental_prescription_add($user_id, $lat, $lng);
                
              
                
                $date = date('Y-m-d'); 
                if ($listing_id != '') {
                    $imagearray=explode(',', $image);
                    if (!empty($imagearray)) {
                        date_default_timezone_set('Asia/Calcutta');
                        $invoice_no = date("YmdHis");
                        $order_status = 'Awaiting Confirmation';
                    }
                   
                   if ($imagearray > 0) {
                        foreach ($imagearray as $key => $img_name) {
        
                            $this->db->query("INSERT INTO `Dental_clinic_prescription`(`user_id`,`listing_id`, `image`,`status`, `date`) VALUES ('$user_id','$listing_id', '$img_name','$order_status', '$date')");

                        }
                    }
                    simple_json_output(array('status' => 200, 'message' => 'success'));
                } else {
                    simple_json_output(array("status" => 201, "message" => "fail"));
                }
            }
        }
    }

    public function Dental_clinic_views() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->Dental_clinic_model->Dental_clinic_views($user_id, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function review_list() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        
                        $resp = $this->Dental_clinic_model->review_list($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function add_review() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
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
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $rating = $params['rating'];
                        $review = $params['review'];
                        $service = $params['service'];
                       // $branch_id = $params['branch_id'];
                        $resp = $this->Dental_clinic_model->add_review($user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function review_with_comment() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['branch_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $branch_id = $params['branch_id'];
                        $resp = $this->Dental_clinic_model->review_with_comment($user_id, $listing_id, $branch_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function review_like() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->Dental_clinic_model->review_like($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $comment = $params['comment'];
                        $resp = $this->Dental_clinic_model->review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_like() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->Dental_clinic_model->review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_list() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->Dental_clinic_model->review_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function edit_review() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
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
                        $resp = $this->Dental_clinic_model->edit_review($review_id,$user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
      public function user_payment_approval_v30()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params             = json_decode(file_get_contents('php://input'), TRUE);
                    
                    $status = $params['status'];
                    $booking_id         = $params['booking_id'];
                    $user_id            = $params['user_id'];
                    if ( $params['user_id'] == "" || $params['status'] == "" || $params['booking_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->Dental_clinic_model->user_payment_approval($status, $booking_id, $user_id);
                    }
                }
                simple_json_output($resp);
            }
        }
    }
    
    public function packages_list()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        // $user_id = $params['user_id'];
                        if(array_key_exists('user_id', $params ) && $params['user_id'] != "" ){
                            $user_id = $params['user_id'];
                        } else {
                            $user_id = "";
                        }
                        // $listing_id = $params['listing_id'];
                        if(array_key_exists('listing_id', $params ) && $params['listing_id'] != "" ){
                            $listing_id = $params['listing_id'];
                        } else {
                            $listing_id = 0;
                        }
                        // $lat     = $params['lat'];
                        if(array_key_exists('lat', $params ) && $params['lat'] != "" ){
                            $lat     = $params['lat'];
                        } else {
                            $lat = "";
                        }
                        // $lng     = $params['lng'];
                        if(array_key_exists('lng', $params ) && $params['lng'] != "" ){
                            $lng     = $params['lng'];
                        } else {
                            $lng = "";
                        }
                        // $page    = $params['page'];
                        if(array_key_exists('page_no', $params ) && $params['page_no'] != "" ){
                            $page_no    = $params['page_no'];
                        } else {
                            $page_no = 0;
                        }
                         // $page    = $params['page'];
                        if(array_key_exists('per_page', $params ) && $params['per_page'] != "" ){
                            $per_page    = $params['per_page'];
                        } else {
                            $per_page = 0;
                        }
                        // $sort    = $params['sort'];
                        if(array_key_exists('sort_price', $params ) && $params['sort_price'] != "" ){
                            $sort_price    = $params['sort_price'];
                        } else {
                            $sort_price = "";
                        }
                        
                        // $sort    = $params['search'];
                        if(array_key_exists('search', $params ) && $params['search'] != "" ){
                            $search    = $params['search'];
                        } else {
                            $search = "";
                        }
                        
                        // special_package
                        if(array_key_exists('special_package', $params ) && $params['special_package'] != "" ){
                            $special_package    = $params['special_package'];
                        } else {
                            $special_package = 0;
                        }
                        if( $user_id == "" ||  $lat  == "" ||  $lng == "" ||  $page_no < 1 || $per_page < 1){
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter all fields : user_id, lat, lng, page_no should be greater than 0, per_page should be greater than 0'
                            );
                        } else {
                            $response = $this->Dental_clinic_model->packages_list($user_id ,  $listing_id ,  $lat ,  $lng ,  $page_no ,  $per_page ,  $sort_price ,  $search, $special_package);
                             $resp = array(
                                'status' => 200,
                                'message' => 'success',
                                'data' => $response 
                            );
                            
                        }
                        
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function treatments_list()
    {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        // $user_id = $params['user_id'];
                        if(array_key_exists('user_id', $params ) && $params['user_id'] != "" ){
                            $user_id = $params['user_id'];
                        } else {
                            $user_id = "";
                        }
                        // $listing_id = $params['listing_id'];
                        if(array_key_exists('listing_id', $params ) && $params['listing_id'] != "" ){
                            $listing_id = $params['listing_id'];
                        } else {
                            $listing_id = 0;
                        }
                        // $lat     = $params['lat'];
                        if(array_key_exists('lat', $params ) && $params['lat'] != "" ){
                            $lat     = $params['lat'];
                        } else {
                            $lat = "";
                        }
                        // $lng     = $params['lng'];
                        if(array_key_exists('lng', $params ) && $params['lng'] != "" ){
                            $lng     = $params['lng'];
                        } else {
                            $lng = "";
                        }
                        // $page    = $params['page'];
                        if(array_key_exists('page_no', $params ) && $params['page_no'] != "" ){
                            $page_no    = $params['page_no'];
                        } else {
                            $page_no = 0;
                        }
                         // $page    = $params['page'];
                        if(array_key_exists('per_page', $params ) && $params['per_page'] != "" ){
                            $per_page    = $params['per_page'];
                        } else {
                            $per_page = 0;
                        }
                        // $sort    = $params['sort'];
                        if(array_key_exists('sort_price', $params ) && $params['sort_price'] != "" ){
                            $sort_price    = $params['sort_price'];
                        } else {
                            $sort_price = "";
                        }
                        
                        // $sort    = $params['search'];
                        if(array_key_exists('search', $params ) && $params['search'] != "" ){
                            $search    = $params['search'];
                        } else {
                            $search = "";
                        }
                        
                        // recommended_treatments
                        if(array_key_exists('recommended_treatments', $params ) && $params['recommended_treatments'] != "" ){
                            $recommended_treatments    = $params['recommended_treatments'];
                        } else {
                            $recommended_treatments = 0;
                        }
                         
                        $response = $this->Dental_clinic_model->treatments_list($user_id ,  $listing_id ,  $lat ,  $lng ,  $page_no ,  $per_page ,  $sort_price ,  $search, $recommended_treatments);
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' => $response 
                        );
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function all_treatment_clinic_list() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $page    = $params['page'];
                        $treatment_id = $params['treatment_id'];
                        $lat =  $params['lat'];
                        $lng =  $params['lng'];
                         if(array_key_exists('sort_price', $params ) && $params['sort_price'] != "" ){
                            $sort_price    = $params['sort_price'];
                        } else {
                            $sort_price = "";
                        }
                        
                        // $sort    = $params['search'];
                        if(array_key_exists('search', $params ) && $params['search'] != "" ){
                            $search    = $params['search'];
                        } else {
                            $search = "";
                        }
                        $resp = $this->Dental_clinic_model->all_treatment_clinic_list($user_id,$treatment_id,$page,$lat,$lng,$sort_price,$search);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function treatment_master_list() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $page    = $params['page'];
                        $resp = $this->Dental_clinic_model->treatment_master_list($user_id,$page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function treatment_master_dental_clinic() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $page    = $params['page'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                        $cat_id  = $params['cat_id'];
                        //$sub_cat_name = $params['sub_cat_name'];
                        $resp = $this->Dental_clinic_model->treatment_master_dental_clinic($user_id,$page,$cat_id,$lat,$lng);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function treatments_services_list() {
        $this->load->model('Dental_clinic_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Dental_clinic_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $page    = $params['page'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                        $listing_id = $params['listing_id'];
                        $cat_id  = $params['cat_id'];
                        //$sub_cat_name = $params['sub_cat_name'];
                        $resp = $this->Dental_clinic_model->treatments_services_list($user_id,$listing_id,$page,$cat_id,$lat,$lng);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
}
