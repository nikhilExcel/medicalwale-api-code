<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Partnerdoctor extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }


     public function patient_mobile() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" || $params['mobile'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $mobile    = $params['mobile'];
                        $resp = $this->PartnerdoctorModel->patient_mobile($doctor_id,$mobile);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
      public function payment_process() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['booking_id'] == "" || $params['amount'] == "" || $params['user_id']=="" || $params['doctor_id']=="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $booking_id      = $params['booking_id'];
                        $amount          = $params['amount'];
                        $discount        = $params['discount'];
                        $card_type       = $params['card_type'];
                        $card_sub_type   = $params['card_sub_type'];
                        $carditdetails   = $params['carditdetails'];
                        $debitdetails    = $params['debitdetails'];
                        $walletdetails   = $params['walletdetails'];
                        $user_id         = $params['user_id'];
                        $doctor_id       = $params['doctor_id'];
                        $finalamount     = $params['finalamount'];
                        $discount_type   = $params['discount_type'];
                        $savedamount     =$params['savedamount'];
                        $resp = $this->PartnerdoctorModel->payment_process($booking_id,$amount,$discount,$card_type,$card_sub_type,$carditdetails,$debitdetails,$walletdetails,$user_id,$doctor_id,$discount_type,$finalamount,$savedamount);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
     public function patient_appointment() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                          if(array_key_exists('agent', $params))
                            {
                                $agent   = $params['agent'];
                                 
                            } else {
                                $agent = "";
                            }
                            
                        if(array_key_exists('clinic_id', $params))
                            {
                                $clinic_id   = $params['clinic_id'];
                                 
                            } else {
                                $clinic_id = "";
                            }    
                            
                        $resp = $this->PartnerdoctorModel->patient_appointment($doctor_id,$agent,$clinic_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function add_bookings() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } 
        else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['patient_id'] == "" || $params['doctor_id'] == "" || $params['clinic_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $patient_id         = $params['patient_id'];
                        $doctor_id         = $params['doctor_id'];
                        $clinic_id          = $params['clinic_id'];
                        $booking_date       = $params['booking_date'];
                        $booking_time       = $params['booking_time'];
                        $from_time          = $params['from_time'];
                        $to_time            = $params['to_time'];
                        $user_name          = $params['user_name'];
                        $user_mobile        = $params['user_mobile'];
                        $user_email         = $params['user_email'];
                        $user_gender        = $params['user_gender'];
                        $description        = $params['description'];
                        $date_of_birth      = $params['date_of_birth'];
                        $connect_type       = "visit";
                        $status      = $params['booking_status']; 
                         $resp               = $this->PartnerdoctorModel->add_bookings($patient_id, $doctor_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $description, $date_of_birth, $connect_type,$status);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
   
    public function add_re_bookings()
    {
      $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } 
        else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['patient_id'] == "" || $params['doctor_id'] == "" || $params['clinic_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $patient_id         = $params['patient_id'];
                        $doctor_id         = $params['doctor_id'];
                        $clinic_id          = $params['clinic_id'];
                        $booking_date       = $params['booking_date'];
                        $booking_time       = $params['booking_time'];
                        $from_time          = $params['from_time'];
                        $to_time            = $params['to_time'];
                        $appointment_id      = $params['appointment_id'];
                        $description        =$params['description'];
                        $connect_type       = "visit";
                        $status      = $params['booking_status']; 
                         $resp               = $this->PartnerdoctorModel->add_re_bookings($patient_id, $doctor_id, $clinic_id, $booking_date, $booking_time, $from_time, $to_time, $connect_type,$status,$appointment_id,$description);
                    }
                    json_outputs($resp);
                }
            }
        }  
    }
    public function add_patient() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $responce = $this->PartnerdoctorModel->auth();
                if ($responce['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" || $params['patient_name'] == "" || $params['contact_no'] == "" || $params['gender'] == "") {
                        $resp = array('status' => 400, 'message' => 'Please enter all fields');
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $patient_name = $params['patient_name'];
                        $address = $params['address'];
                        $state = $params['state'];
                        $city = $params['city'];
                        $pincode = $params['pincode'];
                        $contact_no = $params['contact_no'];
                        $gender = $params['gender'];
                        $date_of_birth = $params['date_of_birth'];
                        $blood_group = $params['blood_group'];
                        $medical_profile = $params['medical_profile'];
                        $email = $params['email'];
                        $resp = $this->PartnerdoctorModel->add_patient($doctor_id, $patient_name, $address, $state, $city, $pincode, $contact_no, $gender, $date_of_birth, $blood_group, $medical_profile, $email);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function add_prescription() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" || $params['clinic_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $clinic_id = $params['clinic_id'];
                        $patient_id = $params['patient_id'];
                        $prescription_note = $params['prescription_note'];
                        $medicine_name = $params['medicine_name'];
                        $dosage = $params['dosage'];
                        $dosage_unit = $params['dosage_unit'];
                        $frequency_first = $params['frequency_first'];
                        $frequency_second = $params['frequency_second'];
                        $frequency_third = $params['frequency_third'];
                        $instruction = $params['instruction'];
                        $category = $params['category'];
                        $test = $params['test'];
						$test_instruction = $params['test_instruction'];
						$days = $params['days'];
						$booking_id = $params['booking_id'];
						$booking_type = $params['booking_type'];
                        $resp = $this->PartnerdoctorModel->add_prescription($doctor_id,$clinic_id,$patient_id,$prescription_note,$medicine_name,$dosage,$dosage_unit,$frequency_first,$frequency_second,$frequency_third,$instruction,$category,$test,$test_instruction,$booking_id,$booking_type, $days);
                        //$status = $this->create_pdf($doctor_id, $clinic_id, $patient_id, $prescription_id, $prescription_note, $medicine_name, $dosage, $dosage_unit, $frequency_first, $frequency_second, $frequency_third, $instruction, $category, $test, $test_instruction);
                        if($resp['message']==='success'){
                            $this->PartnerdoctorModel->send_message_prescription($resp['prescription_id']);
                            //$this->PartnerdoctorModel->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
                            
                        }
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
   


    public function patient_search() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" || $params['keyword'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $keyword   = $params['keyword'];
                        $resp = $this->PartnerdoctorModel->patient_search($doctor_id,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function council_list() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $type = $params['type'];
                        $resp = $this->PartnerdoctorModel->council_list($type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }


    public function card_type() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" || $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $type   = $params['type'];
                        $resp = $this->PartnerdoctorModel->card_type($doctor_id,$type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function search_test() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'GET') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->PartnerdoctorModel->search_test();
                    json_outputs($resp);
                }
            }
        }
    }

    public function council_list_search() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $type = $params['type'];
                        $keyword = $params['keyword'];
                        $resp = $this->PartnerdoctorModel->council_list_search($type, $keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function partner_area_expertise() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $list = json_decode(file_get_contents('php://input'), TRUE);
                    $category = $list['category'];
                    $keyword = $list['keyword'];
                    $type = $list['type'];
                    if ($category == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        if ($type == "expertise") {
                            $resp = $this->PartnerdoctorModel->partner_area_expertise_model($category, $keyword);
                        } elseif ($type == "specialization") {
                            $resp = $this->PartnerdoctorModel->partner_doctor_specialization_model($keyword);
                        } elseif ($type == "services") {
                            $resp = $this->PartnerdoctorModel->partner_doctor_service_model($keyword);
                        }
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function partner_doctor_prescription() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $list = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id = $list['doctor_id'];
                    $keyword = $list['keyword'];
                    $type = $list['type'];
                    if ($doctor_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        if ($type == "patient") {
                            $resp = $this->PartnerdoctorModel->partner_patient_list_model($doctor_id, $keyword,$type);
                        } elseif ($type == "clinic") {
                            $resp = $this->PartnerdoctorModel->partner_clinic_list_model($doctor_id, $keyword);
                        } elseif ($type == "medicines") {
                            $resp = $this->PartnerdoctorModel->partner_medicines_list_model($keyword);
                        }
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function signup() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['area_expertise'] == "" || $params['type'] == "" || $params['doctor_name'] == "" || $params['email'] == "" || $params['phone'] == "" || $params['qualification'] == "" || $params['experience'] == "" || $params['gender'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $category = $params['area_expertise'];
                        $type = $params['type'];
                        $doctor_name = $params['doctor_name'];
                        $email = $params['email'];
                        $phone = $params['phone'];
                        $qualification = $params['qualification'];
                        $experience = $params['experience'];
                        $gender = $params['gender'];
                        $dob = $params['dob'];
                        $reg_council = $params['reg_council'];
                        $reg_number = $params['reg_number'];
                        $token = $params['token'];
                        $agent = $params['agent'];
                        $resp = $this->PartnerdoctorModel->signup($category, $type, $doctor_name, $email, $phone, $qualification, $experience, $gender, $dob, $reg_council, $reg_number, $token, $agent);
                    }
                }
                simple_json_output($resp);
            }
        }
    }

    public function doctor_profile_pic() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $listing_id = $this->input->post('listing_id');
            if ($listing_id == "" || empty($_FILES["profile_pic"]["name"])) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                //unlink images
                $file_query = $this->db->query("SELECT image FROM `doctor_list` WHERE  user_id='$listing_id'");
                $get_file = $file_query->row();
                if ($get_file) {
                    $profile_pic = $get_file->image;
                    $file = "images/healthwall_avatar/" . $profile_pic;
                    @unlink(trim($file));
                    DeleteFromToS3($file);
                }
                //unlink images ends

                $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                include('s3_config.php');

                $img_name = $_FILES['profile_pic']['name'];
                $img_size = $_FILES['profile_pic']['size'];
                $img_tmp = $_FILES['profile_pic']['tmp_name'];
                $ext = getExtension($img_name);

                if (strlen($img_name) > 0) {
                    if ($img_size < (50000 * 50000)) {
                        if (in_array($ext, $img_format)) {
                            $profile_pic_file = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path = 'images/healthwall_avatar/' . $profile_pic_file;
                            $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                        }
                    }
                }
                $resp = $this->PartnerdoctorModel->doctor_profile_pic($listing_id, $profile_pic_file);
            }

            simple_json_output($resp);
        }
    }




    public function doctor_pic() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
             $groupname = $this->input->post('groupname');
             $group_user_id = $this->input->post('group_user_id');
            
            if ($groupname == "" || $group_user_id =="") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                     //unlink images
                $file_query = $this->db->query("SELECT img_url FROM `doctor_group_img` WHERE  group_user_id='$group_user_id'");
                $get_file = $file_query->row();
                if ($get_file) {
                    $profile_pic = $get_file->img_url;
                    $file = "images/healthwall_avatar/" . $profile_pic;
                    @unlink(trim($file));
                    DeleteFromToS3($file);
                }
                   //unlink images ends
                $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                include('s3_config.php');

                $img_name = $_FILES['img_url']['name'];
                $img_size = $_FILES['img_url']['size'];
                $img_tmp = $_FILES['img_url']['tmp_name'];
                $ext = getExtension($img_name);

                if (strlen($img_name) > 0) {
                    if ($img_size < (50000 * 50000)) {
                        if (in_array($ext, $img_format)) {
                            $profile_pic_file = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path = 'images/healthwall_avatar/' . $profile_pic_file;
                            $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                        }
                    }
                }
                
                $resp = $this->PartnerdoctorModel->doctor_pic($groupname,$group_user_id,$profile_pic_file);
            }

            simple_json_output($resp);
        }
    }
   
   
   
       public function doctor_pic_list() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
           
              $params = json_decode(file_get_contents('php://input'), TRUE);
              $uid = $params['group_user_id'];
                $resp = $this->PartnerdoctorModel->doctor_pic_list($uid);
            }

                simple_json_output($resp);
        
    }
    public function doctor_my_profile_details() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $type = $params['type'];
                        $resp = $this->PartnerdoctorModel->doctor_my_profile_details($listing_id, $type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function doctor_specialization_update() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['speciality'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $speciality = $params['speciality'];
                        $resp = $this->PartnerdoctorModel->doctor_specialization_update($listing_id, $speciality);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function doctor_specialization() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->PartnerdoctorModel->doctor_specialization();
                    json_outputs($resp);
                }
            }
        }
    }

    public function doctor_services() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->PartnerdoctorModel->doctor_services();
                    json_outputs($resp);
                }
            }
        }
    }

    public function doctor_details() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $type = $params['type'];
                        $resp = $this->PartnerdoctorModel->doctor_details($listing_id, $type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function doctor_documents_upload() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $listing_id = $this->input->post('listing_id');
            if ($listing_id == "" || empty($_FILES["medical_registration_pic"]["name"]) || empty($_FILES["medical_degree_pic"]["name"]) || empty($_FILES["government_id_pic"]["name"]) || empty($_FILES["prescription_pad_pic"]["name"]) || empty($_FILES["business_card_pic"]["name"])) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                include('s3_config.php');
                $img_name = $_FILES['medical_registration_pic']['name'];
                $img_size = $_FILES['medical_registration_pic']['size'];
                $img_tmp = $_FILES['medical_registration_pic']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if ($img_size < (50000 * 50000)) {
                        if (in_array($ext, $img_format)) {
                            $medical_registration_pic = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path = 'images/doctor_images/' . $medical_registration_pic;
                            $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                        }
                    }
                }
                $img_name2 = $_FILES['medical_degree_pic']['name'];
                $img_size2 = $_FILES['medical_degree_pic']['size'];
                $img_tmp2 = $_FILES['medical_degree_pic']['tmp_name'];
                $ext2 = getExtension($img_name);
                if (strlen($img_name2) > 0) {
                    if ($img_size2 < (50000 * 50000)) {
                        if (in_array($ext2, $img_format)) {
                            $medical_degree_pic = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path2 = 'images/doctor_images/' . $medical_degree_pic;
                            $s3->putObjectFile($img_tmp2, $bucket, $actual_image_path2, S3::ACL_PUBLIC_READ);
                        }
                    }
                }
                $img_name3 = $_FILES['government_id_pic']['name'];
                $img_size3 = $_FILES['government_id_pic']['size'];
                $img_tmp3 = $_FILES['government_id_pic']['tmp_name'];
                $ext3 = getExtension($img_name);
                if (strlen($img_name3) > 0) {
                    if ($img_size3 < (50000 * 50000)) {
                        if (in_array($ext3, $img_format)) {
                            $government_id_pic = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path3 = 'images/doctor_images/' . $government_id_pic;
                            $s3->putObjectFile($img_tmp3, $bucket, $actual_image_path3, S3::ACL_PUBLIC_READ);
                        }
                    }
                }
                $img_name4 = $_FILES['prescription_pad_pic']['name'];
                $img_size4 = $_FILES['prescription_pad_pic']['size'];
                $img_tmp4 = $_FILES['prescription_pad_pic']['tmp_name'];
                $ext4 = getExtension($img_name);
                if (strlen($img_name4) > 0) {
                    if ($img_size4 < (50000 * 50000)) {
                        if (in_array($ext4, $img_format)) {
                            $prescription_pad_pic = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path4 = 'images/doctor_images/' . $prescription_pad_pic;
                            $s3->putObjectFile($img_tmp4, $bucket, $actual_image_path4, S3::ACL_PUBLIC_READ);
                        }
                    }
                }
                $img_name5 = $_FILES['business_card_pic']['name'];
                $img_size5 = $_FILES['business_card_pic']['size'];
                $img_tmp5 = $_FILES['business_card_pic']['tmp_name'];
                $ext5 = getExtension($img_name);
                if (strlen($img_name5) > 0) {
                    if ($img_size5 < (50000 * 50000)) {
                        if (in_array($ext5, $img_format)) {
                            $business_card_pic = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path5 = 'images/doctor_images/' . $business_card_pic;
                            $s3->putObjectFile($img_tmp5, $bucket, $actual_image_path5, S3::ACL_PUBLIC_READ);
                        }
                    }
                }
                $resp = $this->PartnerdoctorModel->doctor_documents_upload($listing_id, $medical_registration_pic, $medical_degree_pic, $government_id_pic, $prescription_pad_pic, $business_card_pic);
            }
            simple_json_output($resp);
        }
    }

    public function doctor_clinic_list() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $responce = $this->PartnerdoctorModel->auth();
                if ($responce['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'Please enter all fields');
                    } else {
                        $user_id = $params['user_id'];

                        $resp = $this->PartnerdoctorModel->doctor_clinic_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function prescription_list() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $responce = $this->PartnerdoctorModel->auth();
                if ($responce['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'Please enter all fields');
                    } else {
                        $doctor_id = $params['doctor_id'];

                        $resp = $this->PartnerdoctorModel->prescription_list($doctor_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function list_patient() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $responce = $this->PartnerdoctorModel->auth();
                if ($responce['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'Please enter all fields');
                    } else {
                        $resp = $this->PartnerdoctorModel->list_patient($params['user_id']);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function list_prescription() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $responce = $this->PartnerdoctorModel->auth();
                if ($responce['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['patient_id'] == "" || $params['user_id'] =="" || $params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'Please enter all fields');
                    } else {
                        $user_id=$params['user_id'];
                        $patient_id=$params['patient_id'];
                        $page = $params['page'];
                        $resp = $this->PartnerdoctorModel->list_prescription($user_id,$patient_id,$page);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function edit_patient() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $responce = $this->PartnerdoctorModel->auth();
                if ($responce['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['patient_name'] == "" || $params['contact_no'] == "" || $params['gender'] == "") {
                        $resp = array('status' => 400, 'message' => 'Please enter all fields');
                    } else {
                        
                        $user_id = $params['user_id'];
                        $patient_id = $params['patient_id'];
                        $patient_name = $params['patient_name'];
                        $address = $params['address'];
                        $state = $params['state'];
                        $city = $params['city'];
                        $pincode = $params['pincode'];
                        $contact_no = $params['contact_no'];
                        $gender = $params['gender'];
                        $date_of_birth = $params['date_of_birth'];
                        $blood_group = $params['blood_group'];
                        $medical_profile = $params['medical_profile'];
                        $email = $params['email'];
                        $data = array(
                            'doctor_id' => $user_id,
                            'patient_name' => $patient_name,
                            'address' => $address,
                            'state' => $state,
                            'city' => $city,
                            'pincode' => $pincode,
                            'contact_no' => $contact_no,
                            'gender' => $gender,
                            'date_of_birth' => $date_of_birth,
                            'blood_group' => $blood_group,
                            'medical_profile' => $medical_profile,
                            'email' => $email
                        );

                        $resp = $this->PartnerdoctorModel->edit_patient($patient_id, $data);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function delete_patient() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $responce = $this->PartnerdoctorModel->auth();
                if ($responce['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'Please enter all fields');
                    } else {


                        $resp = $this->PartnerdoctorModel->delete_patient($params['user_id'], $params['patient_id']);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function partner_doctor_details() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $resp = $this->PartnerdoctorModel->partner_doctor_details($listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function doctor_add_clinic() {
       
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
            
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            
            
            if ($check_auth_client == true) {
                $responce = $this->PartnerdoctorModel->auth();
                if ($responce['status'] == 200) {
                    
                        date_default_timezone_set("Asia/Kolkata"); 
                    //  simple_json_output(array('status' => 200));
                     
                    // $params = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id = $this->input->post('doctor_id');
                    
                    // die();
                    $doctor_id = (int)$doctor_id;
                   
                    /* if ($doctor_id == "") {
                        $resp = array('status' => 400, 'message' => 'Please enter all fields');
                         simple_json_output(array('status' => 400, 'message' => 'Please enter doctor id')); 
                     } else {*/
                           date_default_timezone_set("Asia/Kolkata"); 
                        
                        // $manage = json_decode($data);
                        
                        // $doctor_id = $params['doctor_id'];
                        // $clinic_name = $params['clinic_name'];
                        // $address = $params['address'];
                        // $state = $params['state'];
                        // $city = $params['city'];
                        // $pincode = $params['pincode'];
                        // $map_location = $params['map_location'];
                        // $lat = $params['lat'];
                        // $lng = $params['lng'];
                        // $consultation_charges = $params['consultation_charges'];
                        // $contact_no = $params['contact_no'];
                        // $appointment_time = $params['appointment_time'];
                        // $open_hours = $params['open_hours'];
                        // $timings = $params['timings'];
                        
                        $doctor_id = $this->input->post('doctor_id');
                        $clinic_name = $this->input->post('clinic_name');
                        $address = $this->input->post('address');
                        $state = $this->input->post('state');
                        $city = $this->input->post('city');
                        $pincode = $this->input->post('pincode');
                        $map_location = $this->input->post('map_location');
                        $lat = $this->input->post('lat');
                        $lng = $this->input->post('lng');
                        $consultation_charges = $this->input->post('consultation_charges'); if($consultation_charges == ""){$consultation_charges = "null";};
                        $contact_no = $this->input->post('contact_no');
                        $appointment_time = $this->input->post('appointment_time'); if($appointment_time == ""){$appointment_time = "null";};
                        $open_hours = $this->input->post('open_hours'); if($open_hours == ""){$open_hours = "null";};
                        //$to_time = $params['to_time'];
                        $time =  $this->input->post('timings');
                        
                        
                        $discount_amount_min = $this->input->post('discount_amount_min');
                        $discount_amount_max = $this->input->post('discount_amount_max');
                        $discount_type = $this->input->post('discount_type');
                        $discount_limit = $this->input->post('discount_limit');
                        $discount_category = $this->input->post('discount_category');
                        $discount_by = $this->input->post('discount_by');
                        $consultation_type = 'visit';
                        
                        
                        $discount_data = array(
                            'vendor_id' => $this->input->post('doctor_id'),
                            'discount_min' => $discount_amount_min,
                            'discount_max' => $discount_amount_max,
                            'discount_type' => $discount_type,
                            'discount_limit' => $discount_limit,
                            'discount_category' => $discount_category,
                            'discount_by' => $discount_by,
                            'discount_exp'=> date("YmdHis")
                            );
                            
                        $discount_data_add = $this->PartnerdoctorModel->doctor_add_discount_clinic($discount_data);
                        // $consultation_charges_call =  $this->input->post('consultation_charges_call'); if($consultation_charges_call == ""){$consultation_charges_call = "null";};
                        // $consultation_charges_video =  $this->input->post('consultation_charges_video'); if($consultation_charges_video == ""){$consultation_charges_video = "null";};
                        // $consultation_charges_text =  $this->input->post('consultation_charges_text'); if($consultation_charges_text == ""){$consultation_charges_text = "null";};
                        // $appointment_time_call =  $this->input->post('appointment_time_call'); if($appointment_time_call == ""){$appointment_time_call = "null";};
                        // $appointment_time_video =  $this->input->post('appointment_time_video'); if($appointment_time_video == ""){$appointment_time_video = "null";};
                        // $appointment_time_text =  $this->input->post('appointment_time_text'); if($appointment_time_text == ""){$appointment_time_text = "null";};
                         
                        $timings = json_decode($time);
                        
                        
                        
                        $data = array(
                            'doctor_id' => $doctor_id,
                            'clinic_name' => $clinic_name,
                            'address' => $address,
                            'state' => $state,
                            'city' => $city,
                            'pincode' => $pincode,
                            'map_location' => $map_location,
                            'lat' => $lat,
                            'lng' => $lng,
                            'consultation_charges' => $consultation_charges,
                            'contact_no' => $contact_no,
                            'appointment_time' => $appointment_time,
                            'open_hours' => $open_hours
                            // 'consultation_charges_call' => $consultation_charges_call,
                            // 'consultation_charges_video' => $consultation_charges_video,
                            // 'consultation_charges_text' => $consultation_charges_text,
                            // 'appointment_time_call' => $appointment_time_call,
                            // 'appointment_time_video' => $appointment_time_video,
                            // 'appointment_time_text' => $appointment_time_text
                        );
                        $respData = $data;
                        //$doctor_id == "" || $clinic_name == "" || $address == "" || $state == "" || $city == "" || $pincode == "" || $map_location == "" || $lat == "" || $lng == "" || $consultation_charges == "" || $contact_no == "" || $appointment_time == "" || $open_hours == ""
                        if ($doctor_id == "") {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter required fields'
                            );
                            
                            simple_json_output(array( 'status' => 400,  'message' => 'please enter required fields'));
                            
                        } else {
                            $clinic_id = $this->PartnerdoctorModel->doctor_add_clinic($data);
                            
                            if ($clinic_id != '') {
                                if (!empty($_FILES)) {
                                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                                    include('s3_config.php');
            
                                    $img_name = $_FILES['clinic_image']['name'];
                                    $img_size = $_FILES['clinic_image']['size'];
                                    $img_tmp = $_FILES['clinic_image']['tmp_name'];
                                    $ext = getExtension($img_name);
            
                                    if (strlen($img_name) > 0) {
                                        if ($img_size < (50000 * 50000)) {
                                            if (in_array($ext, $img_format)) {
                                                $clinic_image = uniqid() . date("YmdHis") . "." . $ext;
                                                $actual_image_path = 'images/doctor_images/' . $clinic_image;
                                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                                    $this->db->query("update doctor_clinic set image = '$clinic_image' where id = '$clinic_id'");
                                                }
                                            }
                                        }
                                    }
                                }
                    $time = array();    
                    $slots = array();
                    $timingsMain = array();
                    //  $timeConsult = array();
                    
                    // $timeCall = $appointment_time_call;
                    // $timeVideo = $appointment_time_video;
                    // $timeText = $appointment_time_text;
                    $timeConsult = $appointment_time;
                    
                    for($i=0;$i<sizeof($timings->timings);$i++){
                        // day monday
                           //  slots 
                             //   time
                              // FromTime -> from to
                            $day = $timings->timings[$i]->day;
                             
                            for($j=0;$j<sizeof($timings->timings[$i]->slots);$j++){
                                // print_r($timings->timings[$i]->slots[$j]);
                                
                                $timeSlot = $timings->timings[$i]->slots[$j]->timeSlot;
                                for($k=0;$k<sizeof($timings->timings[$i]->slots[$j]->time);$k++){
                                            // time
                                            
                                            $fromDate =  $timings->timings[$i]->slots[$j]->time[$k]->FromTime;
                                            $toDate =  $timings->timings[$i]->slots[$j]->time[$k]->ToTime;
                                            
                                            // echo $fromDate ."fromDate<br>";
                                            // echo $toDate ."toDate<br>";
                                            
                                         
                                            $timestampFrom = date('Y-m-d H:i:s', strtotime($fromDate));
                                            $timestampTo = date('Y-m-d H:i:s', strtotime($toDate));
                                            
                                            $epoToDateFrom =  date("Y-m-d H:i:s", substr($fromDate, 0, 10));
                                            $epoToDateTo =  date("Y-m-d H:i:s", substr($toDate, 0, 10));
                                            
                                            
                                            
                                            
                                              
                                            $timeSlotFrom = date('H:i:s', strtotime($fromDate));
                                            $timeSlotTo = date('H:i:s', strtotime($toDate));
                                            // doctor_slot_details

                                            // echo $timestampFrom."\n\n";
                                            // echo $timestampTo."\n\n";
                                            // echo $epoToDateFrom."\n\n";
                                            // echo $epoToDateTo."\n\n";
                                            // echo $timeSlotFrom."\n\n";
                                            // echo $timeSlotTo."\n\n";
                                            // die();                                            
                                            

                                            $data_time_slots = array(
                                                    'doctor_id' => $doctor_id,
                                                    'clinic_id' => $clinic_id,
                                                    'from_time' => $epoToDateFrom,
                                                    'to_time' => $epoToDateTo,
                                                    'day' => $day,
                                                    'time_slot' => $timeSlot,
                                                    'consultation_type' => $consultation_type,
                                                    'open_hours' => $appointment_time
                                                );
                                                
                                               
                                            // die();
                                        $this->PartnerdoctorModel->doctor_slot_details($data_time_slots);
                                        
                                        
                                        
                                            
                                            
                                            
                                            // $timestampFromDate = new DateTime(date('Y-m-d H:i:s', strtotime($fromDate)));
                                            // $timestampToDate = new DateTime(date('Y-m-d H:i:s', strtotime($toDate)));
                                            
                                            $timestampFromDate = new DateTime(date('Y-m-d H:i:s', strtotime($epoToDateFrom)));
                                            $timestampToDate = new DateTime(date('Y-m-d H:i:s', strtotime($epoToDateTo)));
                                            
                                            $totalHts = $toDate - $fromDate;
                                            // $timestampDiff = date('H:i', strtotime($totalHts));
                                            // $interval = $timestampFromDate->diff($timestampToDate);
                                            $interval = $timestampFromDate->diff($timestampToDate);
                                            
                                            $remHr = $interval->format('%h');
                                            $remMin = $interval->format('%i');
                                            
                                            $totalRemMin = $remHr * 60 + $remMin;
                                            
                                            
                                            // $timeForCall = $totalRemMin / $timeCall;
                                            // $timeForVideo = $totalRemMin / $timeVideo;
                                            // $timeForText = $totalRemMin / $timeText;
                                            
                                             $timeForConsult = $totalRemMin / $timeConsult;
                                            
                                           
                                            // $timeForCallFloor = floor($timeForCall);
                                            // $decimalForCall = $timeForCall - $timeForCallFloor;
                                            // if($decimalForCall > 0.5){ $finalSlotCall = ceil($timeForCall); } else { $finalSlotCall = floor($timeForCall); };
                                            
                                            // $timeForVideoFloor = floor($timeForVideo);
                                            // $decimalForVideo = $timeForVideo - $timeForVideoFloor;
                                            //  if($decimalForVideo > 0.5){ $finalSlotVideo = ceil($timeForVideo); } else { $finalSlotVideo = floor($timeForVideo); };
                                            
                                            // $timeForTextFloor = floor($timeForText);
                                            // $decimalForText = $timeForText - $timeForTextFloor;
                                            //  if($decimalForText > 0.5){ $finalSlotText = ceil($timeForText); } else { $finalSlotText = floor($timeForText); };
                                            
                                            
                                             $timeForConsultFloor = floor($timeForConsult);
                                            $decimalForConsult = $timeForConsult - $timeForConsultFloor;
                                            if($decimalForConsult > 0.5){ $finalSlotConsult = ceil($timeForConsult); } else { $finalSlotConsult = floor($timeForConsult); };
                                            
                                            
                                            
                                         
                                           
                                        //   $newCallTime = $timestampFrom;
                                        
                                        
                                            // $newVideoTime = $timestampFrom;
                                            // echo "newCallTime".$newCallTime;
                                            //  $newTextTime = $timestampFrom;
                                            
                                            $newCallTime = $epoToDateFrom;
                                             $newVideoTime = $epoToDateFrom;
                                            $newTextTime = $epoToDateFrom;
                                            
                                             $epoToDateFrom =  date("H:i:s", substr($fromDate, 0, 10));
                                            
                                            $newConsultTime = $epoToDateFrom;
                                            
                                            
                                            // echo $newConsultTime;
                                            // die();
                                             
                                        //   echo $timestampFrom."<br>";
                                        //   echo $timestampTo."<br>";
                                        //      echo "call:<br>";
                                        
                                        
                                        // consult
                                        
                                        for($consultSlot=0;$consultSlot<$finalSlotConsult;$consultSlot++){
                                                
                                            // echo $timeConsult;
                                            // die();
                                                
                                                // $CallTime = $newCallTime + $timeCallSec;
                                                
                                                // $selectedTime = $newCallTime;
                                               
                                                $ConsultTimeAdded = strtotime("+".$timeConsult." minutes", strtotime($newConsultTime));
                                                $ConsultTime = date('H:i:s', $ConsultTimeAdded);
                                                
                                            //     echo "ConsultTimeAdded".$ConsultTimeAdded."<br>";
                                            //       echo "epoToDateFrom".$epoToDateFrom."<br>";
                                            //   echo "newConsultTime".$newConsultTime."<br>";
                                            //                     echo "ConsultTime".$ConsultTime."<br>";
                                               
                                                
                                                        
                                            // $timestampFrom = date('H:i:s', strtotime($fromDate));
                                            // $timestampTo = date('H:i:s', strtotime($toDate));
                                                $data_time = array(
                                                    'doctor_id' => $doctor_id,
                                                    'clinic_id' => $clinic_id,
                                                    'from_time' => $newConsultTime,
                                                    'to_time' => $ConsultTime,
                                                    'day' => $day,
                                                    'timeSlot' => $timeSlot,
                                                    'status' => "0",
                                                    'consultation_type' => $consultation_type
                                                );
                                                $timing_id = $this->PartnerdoctorModel->doctor_add_clinic_timing($data_time);
                                          
                                           
                                           $time['FromTime']=$newConsultTime;
                                            $time['to_time']=$ConsultTime;
                                            $timeAll[] = $time;
                                             $newConsultTime = $ConsultTime;
                                        
                                            }
                                        
                                    
                                }
                               $slots['time'] = $timeAll;
                                $slots['timeSlot'] = $timeSlot;
                                
                                $slotsAll[]=$slots;
                                // print_r($slots);die();
                            }
                            $timingsNew['day'] = $day;
                            $timingsNew['slots'] = $slotsAll;
                            
                            $timing[] = $timingsNew;
                            
                            // print_r($timingsMain);die();
                    }
                   
                    //  array_push($respData, $timingsMain);
                    //  $repArray = array_merge($respData, array("timings" => ""));
                    // $repArray['timings'][]= $timingsMain;
                     
                    //  print_r($repArray);die();
                //   $respData['timings'] = $timing;
            
                    simple_json_output(array('status' => 200, 'message' => 'success', 'data' => $respData));
                } else {
                    simple_json_output(array("status" => 201, "message" => "fail"));
                }
            }
        // }
      //}
      
     } 
    }  else { simple_json_output(array('message' => 'Unauthorised')); }
    }
        
    }

    public function doctor_edit_clinic() {
      
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            //  echo "inelseAuth";
            if ($check_auth_client == true) {
                $responce = $this->PartnerdoctorModel->auth();
                //  echo "inIfRespAuth";
                if ($responce['status'] == 200) {
                    
             $doctor_id = $this->input->post('doctor_id');
                     if ($doctor_id == "") {
                        $resp = array('status' => 400, 'message' => 'Please enter all fields');
                     } else {
                        $doctor_id = $this->input->post('doctor_id');
                        $clinic_id = $this->input->post('clinic_id');
                        $clinic_name = $this->input->post('clinic_name');
                        $address = $this->input->post('address');
                        $state = $this->input->post('state');
                        $city = $this->input->post('city');
                        $pincode = $this->input->post('pincode');
                        $map_location = $this->input->post('map_location');
                        $lat = $this->input->post('lat');
                        $lng = $this->input->post('lng');
                        $consultation_charges = $this->input->post('consultation_charges'); if($consultation_charges == ""){$consultation_charges = "null";};
                        $contact_no = $this->input->post('contact_no');
                        $appointment_time = $this->input->post('appointment_time'); if($appointment_time == ""){$appointment_time = "null";};
                        $open_hours = $this->input->post('open_hours'); if($open_hours == ""){$open_hours = "null";};
                        //$to_time = $params['to_time'];
                        $time =  $this->input->post('timings');
                        // $consultation_charges_call =  $this->input->post('consultation_charges_call'); if($consultation_charges_call == ""){$consultation_charges_call = "null";};
                        // $consultation_charges_video =  $this->input->post('consultation_charges_video'); if($consultation_charges_video == ""){$consultation_charges_video = "null";};
                        // $consultation_charges_text =  $this->input->post('consultation_charges_text'); if($consultation_charges_text == ""){$consultation_charges_text = "null";};
                        // $appointment_time_call =  $this->input->post('appointment_time_call'); if($appointment_time_call == ""){$appointment_time_call = "null";};
                        // $appointment_time_video =  $this->input->post('appointment_time_video'); if($appointment_time_video == ""){$appointment_time_video = "null";};
                        // $appointment_time_text =  $this->input->post('appointment_time_text'); if($appointment_time_text == ""){$appointment_time_text = "null";};
                        //start of me
                        $discount_amount_min = $this->input->post('discount_amount_min');
                        $discount_amount_max = $this->input->post('discount_amount_max');
                        $discount_type = $this->input->post('discount_type');
                        $discount_limit = $this->input->post('discount_limit');
                        $discount_category = $this->input->post('discount_category');
                        $discount_by = $this->input->post('discount_by');
                        $consultation_type = 'visit';
                        
                        
                        $discount_data = array(
                            'vendor_id' => $this->input->post('doctor_id'),
                            'discount_min' => $discount_amount_min,
                            'discount_max' => $discount_amount_max,
                            'discount_type' => $discount_type,
                            'discount_limit' => $discount_limit,
                            'discount_category' => $discount_category,
                            'discount_by' => $discount_by,
                            'discount_exp'=> date("YmdHis")
                            );
                            
                        $discount_data_add = $this->PartnerdoctorModel->doctor_edit_discount_clinic($discount_data);
                        //end of me
                        $timings = json_decode($time);
                        
            $data = array(
                'doctor_id' => $doctor_id,
                //  'clinic_id' => $clinic_id,
                'clinic_name' => $clinic_name,
                'address' => $address,
                'state' => $state,
                'city' => $city,
                'pincode' => $pincode,
                'map_location' => $map_location,
                'lat' => $lat,
                'lng' => $lng,
                'consultation_charges' => $consultation_charges,
                'contact_no' => $contact_no,
                'appointment_time' => $appointment_time,
                'open_hours' => $open_hours
                // 'consultation_charges_call' => $consultation_charges_call,
                // 'consultation_charges_video' => $consultation_charges_video,
                // 'consultation_charges_text' => $consultation_charges_text,
                // 'appointment_time_call' => $appointment_time_call,
                // 'appointment_time_video' => $appointment_time_video,
                // 'appointment_time_text' => $appointment_time_text
            );
            // print_r($data);
            // die();
            $respData = $data;
            $response = $this->PartnerdoctorModel->doctor_edit_clinic($clinic_id,$data);
            // $this->PartnerdoctorModel->doctor_edit_clinic($clinic_id,$data);        
      
       
                      if (!empty($_FILES)) {
                                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                                    include('s3_config.php');
            
                                    $img_name = $_FILES['clinic_image']['name'];
                                    $img_size = $_FILES['clinic_image']['size'];
                                    $img_tmp = $_FILES['clinic_image']['tmp_name'];
                                    $ext = getExtension($img_name);
            
                                    if (strlen($img_name) > 0) {
                                        if ($img_size < (50000 * 50000)) {
                                            if (in_array($ext, $img_format)) {
                                                $clinic_image = uniqid() . date("YmdHis") . "." . $ext;
                                                $actual_image_path = 'images/doctor_images/' . $clinic_image;
                                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                                    $this->db->query("update doctor_clinic set image = '$clinic_image' where id = '$clinic_id'");
                                                }
                                            }
                                        }
                                    }
                                }
                    $time = array();    
                    $slots = array();
                    $timingsMain = array();
                    
                    // $timeCall = $appointment_time_call;
                    // $timeVideo = $appointment_time_video;
                    // $timeText = $appointment_time_text;
                    $timeConsult = $appointment_time;
                    $allRows = $this->PartnerdoctorModel->doctor_delete_clinic_timing($clinic_id, $doctor_id, $consultation_type); 
                    // print_r($allRows);
                    // die();
                    // if($allRows == 1){
                        
                    $deleted = $this->PartnerdoctorModel->doctor_delete_slot_details($doctor_id, $clinic_id, $consultation_type);
                                            
                    // echo "deleted : ". $deleted;
                    
                    $timeAll = array();
                    
                    for($i=0;$i<sizeof($timings->timings);$i++){
                      
                             $day = $timings->timings[$i]->day;
                             
                            for($j=0;$j<sizeof($timings->timings[$i]->slots);$j++){
                                
                                $timeSlot = $timings->timings[$i]->slots[$j]->timeSlot;
                                
                                for($k=0;$k<sizeof($timings->timings[$i]->slots[$j]->time);$k++){
                                            
                                            $fromDate =  $timings->timings[$i]->slots[$j]->time[$k]->FromTime;
                                            $toDate =  $timings->timings[$i]->slots[$j]->time[$k]->ToTime;
                                            
                                            $timestampFrom = date('Y-m-d H:i:s', strtotime($fromDate));
                                            $timestampTo = date('Y-m-d H:i:s', strtotime($toDate));
                                            
                                            $epoToDateFrom =  date("Y-m-d H:i:s", substr($fromDate, 0, 10));
                                            $epoToDateTo =  date("Y-m-d H:i:s", substr($toDate, 0, 10));
                                            
                                            $timestampFromDate = new DateTime(date('Y-m-d H:i:s', strtotime($epoToDateFrom)));
                                            $timestampToDate = new DateTime(date('Y-m-d H:i:s', strtotime($epoToDateTo)));
                                            
                                            
                                            
                                              $timeSlotFrom = date('H:i:s', strtotime($fromDate));
                                            $timeSlotTo = date('H:i:s', strtotime($toDate));
                                            // doctor_slot_details



                                            $data_time_slots = array(
                                                    'doctor_id' => $doctor_id,
                                                    'clinic_id' => $clinic_id,
                                                    'from_time' => $epoToDateFrom,
                                                    'to_time' => $epoToDateTo,
                                                    'day' => $day,
                                                    'time_slot' => $timeSlot,
                                                    'consultation_type' => $consultation_type,
                                                    'open_hours' => $appointment_time
                                                );
                                                
                                               
                                            // die();
                                        $inserted = $this->PartnerdoctorModel->doctor_edit_slot_details($doctor_id, $clinic_id, $data_time_slots);
                                        
                                        // echo "inserted : ".$inserted;
                                        // die();
                                            
                                            $totalHts = $toDate - $fromDate;
                                            $interval = $timestampFromDate->diff($timestampToDate);
                                            
                                            $remHr = $interval->format('%h');
                                            $remMin = $interval->format('%i');
                                            
                                            $totalRemMin = $remHr * 60 + $remMin;
                                            
                                            
                                            // $timeForCall = $totalRemMin / $timeCall;
                                            // $timeForVideo = $totalRemMin / $timeVideo;
                                            // $timeForText = $totalRemMin / $timeText;
                                            
                                             $timeForConsult = $totalRemMin / $timeConsult;
                                           
                                            // $timeForCallFloor = floor($timeForCall);
                                            // $decimalForCall = $timeForCall - $timeForCallFloor;
                                            // if($decimalForCall > 0.5){ $finalSlotCall = ceil($timeForCall); } else { $finalSlotCall = floor($timeForCall); };
                                            
                                            // $timeForVideoFloor = floor($timeForVideo);
                                            // $decimalForVideo = $timeForVideo - $timeForVideoFloor;
                                            //  if($decimalForVideo > 0.5){ $finalSlotVideo = ceil($timeForVideo); } else { $finalSlotVideo = floor($timeForVideo); };
                                            
                                            // $timeForTextFloor = floor($timeForText);
                                            // $decimalForText = $timeForText - $timeForTextFloor;
                                            //  if($decimalForText > 0.5){ $finalSlotText = ceil($timeForText); } else { $finalSlotText = floor($timeForText); };
                                            
                                            $timeForConsultFloor = floor($timeForConsult);
                                            $decimalForConsult = $timeForConsult - $timeForConsultFloor;
                                            if($decimalForConsult > 0.5){ $finalSlotConsult = ceil($timeForConsult); } else { $finalSlotConsult = floor($timeForConsult); };
                                            
                                            
                                            $newCallTime = $epoToDateFrom;
                                             $newVideoTime = $epoToDateFrom;
                                            $newTextTime = $epoToDateFrom;
                                            
                                              $epoToDateFrom =  date("H:i:s", substr($fromDate, 0, 10));
                                            $newConsultTime = $epoToDateFrom;
                                            
                                           
                                             
                                            for($consultSlot=0;$consultSlot<$finalSlotConsult;$consultSlot++){
                                                
                                             
                                                
                                                // $CallTime = $newCallTime + $timeCallSec;
                                                
                                                // $selectedTime = $newCallTime;
                                               
                                                $ConsultTimeAdded = strtotime("+".$timeConsult." minutes", strtotime($newConsultTime));
                                                $ConsultTime = date('H:i:s', $ConsultTimeAdded);
                                                
                                            //     echo "ConsultTimeAdded".$ConsultTimeAdded."<br>";
                                            //       echo "epoToDateFrom".$epoToDateFrom."<br>";
                                            //   echo "newConsultTime".$newConsultTime."<br>";
                                            //                     echo "ConsultTime".$ConsultTime."<br>";
                                               
                                                
                                                        
                                            // $timestampFrom = date('H:i:s', strtotime($fromDate));
                                            // $timestampTo = date('H:i:s', strtotime($toDate));
                                                $data_time = array(
                                                    'doctor_id' => $doctor_id,
                                                    'clinic_id' => $clinic_id,
                                                    'from_time' => $newConsultTime,
                                                    'to_time' => $ConsultTime,
                                                    'day' => $day,
                                                    'timeSlot' => $timeSlot,
                                                    'status' => "0",
                                                    'consultation_type' => $consultation_type
                                                );
                                                
                                               
                                            // die();
                                            $timing_id = $this->PartnerdoctorModel->doctor_add_clinic_timing($data_time);
                                          
                                           
                                           
                                             
                                
                                            // $timeNew = array();
                                            // $timeNew = array("timing_id" => "$timing_id","from_time" => "$fromDate","to_time" => "$toDate");
                                                
                                            // array_push($time, $timeNew);
                                            
                                            $time['FromTime']=$newConsultTime;
                                            $time['to_time']=$ConsultTime;
                                            $timeAll[] = $time;
                                             $newConsultTime = $ConsultTime;
                                        
                                            }
                                            
                                            
                                            // for($callSlot=0;$callSlot<$finalSlotCall;$callSlot++){
                                               
                                            //     $CallTimeAdded = strtotime("+".$timeCall." minutes", strtotime($newCallTime));
                                            //     $CallTime = date('h:i:s', $CallTimeAdded);
                                                
                                            //     $data_time = array(
                                            //         'doctor_id' => $doctor_id,
                                            //         'clinic_id' => $clinic_id,
                                            //         'from_time' => $newCallTime,
                                            //         'to_time' => $CallTime,
                                            //         'day' => $day,
                                            //         'timeSlot' => $timeSlot,
                                            //         'status' => "0",
                                            //         'consultation_type' => 'call'
                                            //     );
                                                
                                            // $timing_id = $this->PartnerdoctorModel->doctor_add_clinic_timing($data_time);
                                           
                                            // $newCallTime = $CallTime;
                                            
                                            // $timeNew = array();
                                            // $timeNew = array("timing_id" => "$timing_id","from_time" => "$fromDate","to_time" => "$toDate");
                                                
                                            // array_push($time, $timeNew);
                                        
                                            // }
                                            // for($videoSlot=0;$videoSlot<$finalSlotVideo;$videoSlot++){
                                          
                                            //     $VideoTimeAdded = strtotime("+".$timeVideo." minutes", strtotime($newVideoTime));
                                            //     $VideoTime = date('h:i:s', $VideoTimeAdded);
                                               
                                            //      $data_time = array(
                                            //         'doctor_id' => $doctor_id,
                                            //         'clinic_id' => $clinic_id,
                                            //         'from_time' => $newVideoTime,
                                            //         'to_time' => $VideoTime,
                                            //         'day' => $day,
                                            //         'timeSlot' => $timeSlot,
                                            //         'status' => "0",
                                            //         'consultation_type' => 'video'
                                            //     );
                                                
                                            // $timing_id = $this->PartnerdoctorModel->doctor_add_clinic_timing($data_time);
                                            // $newVideoTime = $VideoTime;
                                            // $timeNew = array();
                                            // $timeNew = array("timing_id" => "$timing_id","from_time" => "$fromDate","to_time" => "$toDate");
                                                
                                            // array_push($time, $timeNew);
                                             
                                            // }
                                            // for($textSlot=0;$textSlot<$finalSlotText;$textSlot++){
                                                
                                            //     $TextTimeAdded = strtotime("+".$timeText." minutes", strtotime($newTextTime));
                                            //     $TextTime = date('h:i:s', $TextTimeAdded);
                                               
                                            //      $data_time = array(
                                            //         'doctor_id' => $doctor_id,
                                            //         'clinic_id' => $clinic_id,
                                            //         'from_time' => $newTextTime,
                                            //         'to_time' => $TextTime,
                                            //         'day' => $day,
                                            //         'timeSlot' => $timeSlot,
                                            //         'status' => "0",
                                            //         'consultation_type' => 'text'
                                            //     );
                                                
                                                
                                            
                                            // $timing_id = $this->PartnerdoctorModel->doctor_add_clinic_timing($data_time);
                                            
                                            // $newTextTime = $TextTime;
                                            // $timeNew = array();
                                            // $timeNew = array("timing_id" => "$timing_id","from_time" => "$fromDate","to_time" => "$toDate");
                                                
                                            // array_push($time, $timeNew);
                                                
                                            // }
                                            
                                            
                                           
                                    
                                }
                                // $slotsNew = array();
                                // $slotsNew = array("timeSlot" => "$timeSlot", "time" => "");
                                // $slotsNew['time'][] =  $time;
                                // array_push($slots, $slotsNew);  
                                
                                $slots['time'] = $timeAll;
                                $slots['timeSlot'] = $timeSlot;
                                
                                $slotsAll[]=$slots;
                               
                            }
                            // $timingsNew = array();
                            // $timingsNew = array("day" => "$day", "slots" => "");
                            // $timingsNew['slots'][] =  $slots;
                            // array_push($timingsMain, $timingsNew);
                            $timingsNew['day'] = $day;
                            $timingsNew['slots'] = $slotsAll;
                            
                            $timing[] = $timingsNew;
                            
                    } 
                    // } else { 
                    //       $resp = array('message' => 'Error in adding new slots');
                    //   simple_json_output($resp);
                
                    // }
                    // $respData['timings'] = $timing;
                     $resp = array('status' => 200, 'message' => 'success', 'data' => $respData); 
                //   simple_json_output(array('status' => 200, 'message' => 'success', 'data' => $respData));
                
              simple_json_output($resp);
              
            }
        }
        // else {  $resp = array('status' => 400, 'message' => 'error'); simple_json_output($resp); }
        } 
        // else {  $resp = array('status' => 400, 'message' => 'errorAuth'); simple_json_output($resp); }
        }
        
    }

    public function doctor_delete_clinic() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $responce = $this->PartnerdoctorModel->auth();
                if ($responce['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['clinic_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'Please enter all fields');
                    } else {


                        $resp = $this->PartnerdoctorModel->doctor_delete_clinic($params['clinic_id']);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function partner_doctor_update() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $responce = $this->PartnerdoctorModel->auth();
                if ($responce['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'Please enter all fields');
                    } else {
                        $listing_id = $params['listing_id'];
                        $doctor_name = $params['doctor_name'];
                        $email = $params['email'];
                        $experience = $params['experience'];
                        $reg_council = $params['reg_council'];
                        $reg_number = $params['reg_number'];
                        $gender = $params['gender'];
                        $dob = $params['dob'];
                        $area_expertise = $params['area_expertise'];
                        $speciality = $params['speciality'];
                        $service = $params['service'];
                        $degree = $params['degree'];
                        $resp = $this->PartnerdoctorModel->partner_doctor_update($listing_id, $doctor_name, $email, $experience, $reg_council, $reg_number, $gender, $dob, $area_expertise, $speciality, $service, $degree);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function add_doctor_medicine() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id = $params['doctor_id'];
                    $patient_id = $params['patient_id'];

                    if ($params['doctor_id'] == "" || $params['patient_id'] == "" || $params['medicine_name'] == "" || $params['dose'] == "" || $params['dose_unit'] == "" || $params['duration'] == "" || $params['duration_circle'] == "" || $params['dose_time'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $medicine_array = array(
                            'doctor_id' => $params['doctor_id'],
                            'patient_id' => $params['patient_id'],
                            'medicine_name' => $params['medicine_name'],
                            'dose' => $params['dose'],
                            'dose_unit' => $params['dose_unit'],
                            'frequency' => $params['frequency'],
                            'hours' => $params['hours'],
                            'duration' => $params['duration'],
                            'duration_circle' => $params['duration_circle'],
                            'dose_time' => $params['dose_time'],
                            'instruction' => $params['instruction']
                        );
                        $resp = $this->PartnerdoctorModel->add_doctor_medicine($doctor_id, $patient_id, $medicine_array);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function edit_medicine_details() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['medicine_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields',
                        );
                    } else {
                        $medicine_id = $params['medicine_id'];
                        $resp = $this->PartnerdoctorModel->edit_medicine_details($medicine_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function delete_medicine_details() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['medicine_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields',
                        );
                    } else {
                        $medicine_id = $params['medicine_id'];
                        $resp = $this->PartnerdoctorModel->delete_medicine_details($medicine_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function update_doctor_medicine() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['medicine_id'] == "" || $params['medicine_name'] == "" || $params['dose'] == "" || $params['dose_unit'] == "" || $params['duration'] == "" || $params['duration_circle'] == "" || $params['dose_time'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $medicine_id = $params['medicine_id'];
                        $medicine_name = $params['medicine_name'];
                        $dose = $params['dose'];
                        $dose_unit = $params['dose_unit'];
                        $frequency = $params['frequency'];
                        $hours = $params['hours'];
                        $duration = $params['duration'];
                        $duration_circle = $params['duration_circle'];
                        $dose_time = $params['dose_time'];
                        $instruction = $params['instruction'];

                        $resp = $this->PartnerdoctorModel->update_doctor_medicine($medicine_id, $medicine_name, $dose, $dose_unit, $frequency, $hours, $duration, $duration_circle, $dose_time, $instruction);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function patient_all_medicine() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id = $params['doctor_id'];
                    $patient_id = $params['patient_id'];
                    $resp = $this->PartnerdoctorModel->patient_all_medicine($doctor_id, $patient_id);
                }
                simple_json_output($resp);
            }
        }
    }

    public function add_doctor_prescription() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id = $params['doctor_id'];
                    $patient_id = $params['patient_id'];
                    $clinic_id = $params['clinic_id'];
                    $medicine_id = $params['medicine_id'];
                    $prescription = $params['prescription'];

                    if ($params['doctor_id'] == "" || $params['patient_id'] == "" || $params['clinic_id'] == "" || $params['prescription'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        date_default_timezone_set('Asia/Kolkata');
                        $updated_at = date('Y-m-d H:i:s');
                        $prescription_array = array(
                            'patient_id' => $params['patient_id'],
                            'medicine_id' => $params['medicine_id'],
                            'clinic_id' => $params['clinic_id'],
                            'prescription' => $params['prescription'],
                            'doctor_id' => $params['doctor_id'],
                            'created_date' => $updated_at
                        );
                        $resp = $this->PartnerdoctorModel->add_doctor_prescription($prescription_array);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function doctors_prescription_list() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id = $params['doctor_id'];

                    $resp = $this->PartnerdoctorModel->doctors_prescription_list($doctor_id);
                }
                simple_json_output($resp);
            }
        }
    }

    public function doctors_prescription_list_edit() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $prescription_id = $params['prescription_id'];

                    $resp = $this->PartnerdoctorModel->doctors_prescription_list_edit($prescription_id);
                }
                simple_json_output($resp);
            }
        }
    }

    public function delete_prescription() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['prescription_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields',
                        );
                    } else {
                        $prescription_id = $params['prescription_id'];
                        $resp = $this->PartnerdoctorModel->delete_prescription($prescription_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function doctors_appointment_list() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id = $params['doctor_id'];

                    $resp = $this->PartnerdoctorModel->doctors_appointment_list($doctor_id);
                }
                json_outputs($resp);
            }
        }
    }
    
    public function doctor_appointment_approval() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id = $params['doctor_id'];
                    $confirm_reschedule = $params['confirm_reschedule'];
                $booking_id = $params['booking_id'];
                     if ($params['doctor_id'] == "" || $params['confirm_reschedule'] == "" || $params['booking_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $resp = $this->PartnerdoctorModel->doctor_appointment_approval($doctor_id, $confirm_reschedule, $booking_id);
                    }
                }
      
               simple_json_output($resp);
            }
        }
    }



     public function doctor_approval() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id = $params['doctor_id'];
                    $booking_status = $params['booking_status'];
                    $booking_id = $params['booking_id'];
                     if ($params['doctor_id'] == "" || $params['booking_status'] == "" || $params['booking_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $resp = $this->PartnerdoctorModel->doctor_approval($doctor_id, $booking_status, $booking_id);
                    }
                }
      
               simple_json_output($resp);
            }
        }
    }

    public function update_doctor_prescription() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['patient_id'] == "" || $params['clinic_id'] == "" || $params['doctor_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $prescription_id = $params['prescription_id'];
                        $medicine_id = $params['medicine_id'];
                        $patient_id = $params['patient_id'];
                        $clinic_id = $params['clinic_id'];
                        $doctor_id = $params['doctor_id'];
                        $prescription = $params['prescription'];
                        date_default_timezone_set('Asia/Kolkata');
                        $updated_at = date('Y-m-d H:i:s');

                        $resp = $this->PartnerdoctorModel->update_doctor_prescription($medicine_id, $prescription_id, $patient_id, $clinic_id, $doctor_id, $prescription, $updated_at);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function doctor_consultation_add_service() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "") {

                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $id = $params['id'];
                        $doctor_id = $params['doctor_id'];
                        $is_active = $params['is_active'];
                        $consultation_name = $params['consultation_name'];
                        $open_hours = $params['open_hours'];
                        $charges = $params['charges'];
                        date_default_timezone_set('Asia/Kolkata');
                        $created_at = date('Y-m-d');
                        $resp = $this->PartnerdoctorModel->doctor_consultation_add_service($id,$doctor_id, $is_active, $consultation_name, $open_hours, $charges, $created_at);
                    }
                    json_outputS($resp);
                }
            }
        }
    }

    public function doctor_consultation_list_service() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "") {

                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $consultation_name = $params['consultation_name'];
                        $resp = $this->PartnerdoctorModel->doctor_consultation_list_service($doctor_id, $consultation_name);
                    }
                    json_outputS($resp);
                }
            }
        }
    }
    
    
        public function medicine_search() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" || $params['keyword'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $keyword   = $params['keyword'];
                        $resp = $this->PartnerdoctorModel->medicine_search($doctor_id,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function clinic_search() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                    if ($params['doctor_id'] == "" && $params['keyword'] == "") {
                       
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                       
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $keyword   = $params['keyword'];
                        $resp = $this->PartnerdoctorModel->clinic_search($doctor_id,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
      public function all_test_search() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                    if ($params['keyword'] == "" && $params['test_id'] == "") {
                       
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                       
                    } else {
                        $test_id = $params['test_id'];
                        $keyword   = $params['keyword'];
                        $resp = $this->PartnerdoctorModel->all_test_search($test_id,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function view_appointments() {
        
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if (  $params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                      
                        
                        $resp = $this->PartnerdoctorModel->view_appointments_module($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function insert_doctor_users_feedback()
	{

	    $this->load->model('PartnerdoctorModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PartnerdoctorModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PartnerdoctorModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['doctor_id'] == "" || $params['type'] == "" || $params['feedback'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $doctor_id = $params['doctor_id'];
					    $user_id = $params['user_id'];
					    $type = $params['type'];
					    $feedback = $params['feedback'];
					    $ratings = $params['ratings'];
					    $recommend = $params['recommend'];
					    $booking_id = $params['booking_id'];
					    $booking_type =$params['booking_type'];
					    
		        		$resp = $this->PartnerdoctorModel->insert_doctor_users_feedback($doctor_id,$user_id,$type,$feedback,$ratings,$recommend,$booking_id,$booking_type);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	 public function clinic_delete() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['clinic_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $clinic_id = $params['clinic_id'];
                        $resp = $this->PartnerdoctorModel->clinic_delete($clinic_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function doctor_view_timings()
    {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" || $params['consultation_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $consultation_type = $params['consultation_type'];
                        $resp = $this->PartnerdoctorModel->doctor_view_timings($doctor_id,$consultation_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function get_vendor_ledger_details()
     {
          $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                       
                        $resp = $this->PartnerdoctorModel->get_vendor_ledger_details($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
     }
    
public function view_patient_details(){
     $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['patient_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['patient_id'];
                      
                        $resp = $this->PartnerdoctorModel->view_patient_details($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
}

public function doctor_edit_timings() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" || $params['consultation_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $consultation_type = $params['consultation_type'];
                        
                        $resp = $this->PartnerdoctorModel->doctor_edit_timings($data,$doctor_id,$consultation_type);
                    }
                    json_outputs($resp);
                }
            }
        }
}


public function doctor_edit_consultation() {
      
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            //  echo "inelseAuth";
            if ($check_auth_client == true) {
                $responce = $this->PartnerdoctorModel->auth();
                //  echo "inIfRespAuth";
                if ($responce['status'] == 200) {
                    
             $doctor_id = $this->input->post('doctor_id');
                     if ($doctor_id == "") {
                        $resp = array('status' => 400, 'message' => 'Please enter all fields');
                     } else {
                        $doctor_id = $this->input->post('doctor_id');
                        $clinic_id = $this->input->post('clinic_id');
                        $clinic_name = $this->input->post('clinic_name');
                        $address = $this->input->post('address');
                        $state = $this->input->post('state');
                        $city = $this->input->post('city');
                        $pincode = $this->input->post('pincode');
                        $map_location = $this->input->post('map_location');
                        $lat = $this->input->post('lat');
                        $lng = $this->input->post('lng');
                        $consultation_charges = $this->input->post('consultation_charges'); if($consultation_charges == ""){$consultation_charges = "null";};
                        $contact_no = $this->input->post('contact_no');
                        $appointment_time = $this->input->post('appointment_time'); if($appointment_time == ""){$appointment_time = "null";};
                        $open_hours = $this->input->post('open_hours'); if($open_hours == ""){$open_hours = "null";};
                        //$to_time = $params['to_time'];
                        $time =  $this->input->post('timings');
                        // $consultation_charges_call =  $this->input->post('consultation_charges_call'); if($consultation_charges_call == ""){$consultation_charges_call = "null";};
                        // $consultation_charges_video =  $this->input->post('consultation_charges_video'); if($consultation_charges_video == ""){$consultation_charges_video = "null";};
                        // $consultation_charges_text =  $this->input->post('consultation_charges_text'); if($consultation_charges_text == ""){$consultation_charges_text = "null";};
                        // $appointment_time_call =  $this->input->post('appointment_time_call'); if($appointment_time_call == ""){$appointment_time_call = "null";};
                        // $appointment_time_video =  $this->input->post('appointment_time_video'); if($appointment_time_video == ""){$appointment_time_video = "null";};
                        // $appointment_time_text =  $this->input->post('appointment_time_text'); if($appointment_time_text == ""){$appointment_time_text = "null";};
                        //start of me
                        $discount_amount_min = $this->input->post('discount_amount_min');
                        $discount_amount_max = $this->input->post('discount_amount_max');
                        $discount_type = $this->input->post('discount_type');
                        $discount_limit = $this->input->post('discount_limit');
                        $discount_category = $this->input->post('discount_category');
                        $discount_by = $this->input->post('discount_by');
                        $consultation_type =$this->input->post('consultation_type'); 
                        
                        $consultation_entry = array(
                            'consultation_type' => $consultation_type
                            );
                       $consultation_entryadd = $this->PartnerdoctorModel->doctor_consultation_entry($doctor_id,$consultation_entry);
                       
                        $clinic_id = '0';
                        $is_active = $this->input->post('is_active');
                        
                        $discount_data = array(
                            'vendor_id' => $this->input->post('doctor_id'),
                            'discount_min' => $discount_amount_min,
                            'discount_max' => $discount_amount_max,
                            'discount_type' => $discount_type,
                            'discount_limit' => $discount_limit,
                            'discount_category' => $discount_category,
                            'discount_by' => $discount_by,
                            'discount_exp'=> date("YmdHis")
                            );
                            
                        $discount_data_add = $this->PartnerdoctorModel->doctor_edit_discount_clinic($discount_data);
                        
                        $discount_data = array(
                            'doctor_user_id' => $this->input->post('doctor_id'),
                            'charges'=> $consultation_charges,
                            'duration'=>$appointment_time,
                           // 'consultation_name'=>$consultation_type,
                           'consultation_name'=>'online',
                            'discount_min' => $discount_amount_min,
                            'discount_max' => $discount_amount_max,
                            'discount_type' => $discount_type,
                            'discount_limit' => $discount_limit,
                            'is_active'=>$is_active
                            );
                            
                        $discount_data_add = $this->PartnerdoctorModel->doctor_edit_discount_clinic2($doctor_id, $consultation_type, $discount_data);
                        
                        
                        
                        
                        
                        
                        //end of me
                        $timings = json_decode($time);
                        
            $data = array(
                'doctor_id' => $doctor_id,
                //  'clinic_id' => $clinic_id,
                'clinic_name' => $clinic_name,
                'address' => $address,
                'state' => $state,
                'city' => $city,
                'pincode' => $pincode,
                'map_location' => $map_location,
                'lat' => $lat,
                'lng' => $lng,
                'consultation_charges' => $consultation_charges,
                'contact_no' => $contact_no,
                'appointment_time' => $appointment_time,
                'open_hours' => $open_hours
                // 'consultation_charges_call' => $consultation_charges_call,
                // 'consultation_charges_video' => $consultation_charges_video,
                // 'consultation_charges_text' => $consultation_charges_text,
                // 'appointment_time_call' => $appointment_time_call,
                // 'appointment_time_video' => $appointment_time_video,
                // 'appointment_time_text' => $appointment_time_text
            );
            // print_r($data);
            // die();
            $respData = $data;
            $response = $this->PartnerdoctorModel->doctor_edit_clinic($clinic_id,$data);
            // $this->PartnerdoctorModel->doctor_edit_clinic($clinic_id,$data);        
      
       
                      if (!empty($_FILES)) {
                                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                                    include('s3_config.php');
            
                                    $img_name = $_FILES['clinic_image']['name'];
                                    $img_size = $_FILES['clinic_image']['size'];
                                    $img_tmp = $_FILES['clinic_image']['tmp_name'];
                                    $ext = getExtension($img_name);
            
                                    if (strlen($img_name) > 0) {
                                        if ($img_size < (50000 * 50000)) {
                                            if (in_array($ext, $img_format)) {
                                                $clinic_image = uniqid() . date("YmdHis") . "." . $ext;
                                                $actual_image_path = 'images/doctor_images/' . $clinic_image;
                                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                                    $this->db->query("update doctor_clinic set image = '$clinic_image' where id = '$clinic_id'");
                                                }
                                            }
                                        }
                                    }
                                }
                    $time = array();    
                    $slots = array();
                    $timingsMain = array();
                    
                    // $timeCall = $appointment_time_call;
                    // $timeVideo = $appointment_time_video;
                    // $timeText = $appointment_time_text;
                    $timeConsult = $appointment_time;
                   // $allRows = $this->PartnerdoctorModel->doctor_delete_clinic_timing($clinic_id, $doctor_id, $consultation_type); 
                    $allRows = $this->PartnerdoctorModel->doctor_delete_clinic_timing($clinic_id, $doctor_id, 'online');
                    // print_r($allRows);
                    // die();
                    // if($allRows == 1){
                        
                   // $deleted = $this->PartnerdoctorModel->doctor_delete_slot_details($doctor_id, $clinic_id, $consultation_type);
                    $deleted = $this->PartnerdoctorModel->doctor_delete_slot_details($doctor_id, $clinic_id, 'online');                        
                    // echo "deleted : ". $deleted;
                    
                    $timeAll = array();
                    
                    for($i=0;$i<sizeof($timings->timings);$i++){
                      
                             $day = $timings->timings[$i]->day;
                             
                            for($j=0;$j<sizeof($timings->timings[$i]->slots);$j++){
                                
                                $timeSlot = $timings->timings[$i]->slots[$j]->timeSlot;
                                
                                for($k=0;$k<sizeof($timings->timings[$i]->slots[$j]->time);$k++){
                                            
                                            $fromDate =  $timings->timings[$i]->slots[$j]->time[$k]->FromTime;
                                            $toDate =  $timings->timings[$i]->slots[$j]->time[$k]->ToTime;
                                            
                                            $timestampFrom = date('Y-m-d H:i:s', strtotime($fromDate));
                                            $timestampTo = date('Y-m-d H:i:s', strtotime($toDate));
                                            
                                            $epoToDateFrom =  date("Y-m-d H:i:s", substr($fromDate, 0, 10));
                                            $epoToDateTo =  date("Y-m-d H:i:s", substr($toDate, 0, 10));
                                            
                                            $timestampFromDate = new DateTime(date('Y-m-d H:i:s', strtotime($epoToDateFrom)));
                                            $timestampToDate = new DateTime(date('Y-m-d H:i:s', strtotime($epoToDateTo)));
                                            
                                            
                                            
                                              $timeSlotFrom = date('H:i:s', strtotime($fromDate));
                                            $timeSlotTo = date('H:i:s', strtotime($toDate));
                                            // doctor_slot_details



                                            $data_time_slots = array(
                                                    'doctor_id' => $doctor_id,
                                                    'from_time' => $epoToDateFrom,
                                                    'to_time' => $epoToDateTo,
                                                    'day' => $day,
                                                    'time_slot' => $timeSlot,
                                                  //  'consultation_type' => $consultation_type,
                                                    'consultation_type' => 'online',
                                                    'open_hours' => $appointment_time
                                                );
                                                
                                               
                                            // die();
                                        $inserted = $this->PartnerdoctorModel->doctor_edit_slot_details2($doctor_id, $data_time_slots);
                                        
                                        // echo "inserted : ".$inserted;
                                        // die();
                                            
                                            $totalHts = $toDate - $fromDate;
                                            $interval = $timestampFromDate->diff($timestampToDate);
                                            
                                            $remHr = $interval->format('%h');
                                            $remMin = $interval->format('%i');
                                            
                                            $totalRemMin = $remHr * 60 + $remMin;
                                            
                                            
                                            // $timeForCall = $totalRemMin / $timeCall;
                                            // $timeForVideo = $totalRemMin / $timeVideo;
                                            // $timeForText = $totalRemMin / $timeText;
                                            
                                             $timeForConsult = $totalRemMin / $timeConsult;
                                           
                                            // $timeForCallFloor = floor($timeForCall);
                                            // $decimalForCall = $timeForCall - $timeForCallFloor;
                                            // if($decimalForCall > 0.5){ $finalSlotCall = ceil($timeForCall); } else { $finalSlotCall = floor($timeForCall); };
                                            
                                            // $timeForVideoFloor = floor($timeForVideo);
                                            // $decimalForVideo = $timeForVideo - $timeForVideoFloor;
                                            //  if($decimalForVideo > 0.5){ $finalSlotVideo = ceil($timeForVideo); } else { $finalSlotVideo = floor($timeForVideo); };
                                            
                                            // $timeForTextFloor = floor($timeForText);
                                            // $decimalForText = $timeForText - $timeForTextFloor;
                                            //  if($decimalForText > 0.5){ $finalSlotText = ceil($timeForText); } else { $finalSlotText = floor($timeForText); };
                                            
                                            $timeForConsultFloor = floor($timeForConsult);
                                            $decimalForConsult = $timeForConsult - $timeForConsultFloor;
                                            if($decimalForConsult > 0.5){ $finalSlotConsult = ceil($timeForConsult); } else { $finalSlotConsult = floor($timeForConsult); };
                                            
                                            
                                            $newCallTime = $epoToDateFrom;
                                             $newVideoTime = $epoToDateFrom;
                                            $newTextTime = $epoToDateFrom;
                                            
                                              $epoToDateFrom =  date("H:i:s", substr($fromDate, 0, 10));
                                            $newConsultTime = $epoToDateFrom;
                                            
                                           
                                             
                                            for($consultSlot=0;$consultSlot<$finalSlotConsult;$consultSlot++){
                                                
                                             
                                                
                                                // $CallTime = $newCallTime + $timeCallSec;
                                                
                                                // $selectedTime = $newCallTime;
                                               
                                                $ConsultTimeAdded = strtotime("+".$timeConsult." minutes", strtotime($newConsultTime));
                                                $ConsultTime = date('H:i:s', $ConsultTimeAdded);
                                                
                                              /*  echo "ConsultTimeAdded".$ConsultTimeAdded."<br>";
                                                  echo "epoToDateFrom".$epoToDateFrom."<br>";
                                              echo "newConsultTime".$newConsultTime."<br>";
                                                                echo "ConsultTime".$ConsultTime."<br>";*/
                                               
                                                
                                                        
                                            // $timestampFrom = date('H:i:s', strtotime($fromDate));
                                            // $timestampTo = date('H:i:s', strtotime($toDate));
                                                $data_time = array(
                                                    'doctor_id' => $doctor_id,
                                                    'clinic_id' => '0',
                                                    'from_time' => $newConsultTime,
                                                    'to_time' => $ConsultTime,
                                                    'day' => $day,
                                                    'timeSlot' => $timeSlot,
                                                    'status' => "0",
                                                  //  'consultation_type' => $consultation_type
                                                  'consultation_type' => 'online'
                                                );
                                                
                                               
                                            // die();
                                            $timing_id = $this->PartnerdoctorModel->doctor_add_clinic_timing($data_time);
                                          
                                           
                                           
                                             
                                
                                            // $timeNew = array();
                                            // $timeNew = array("timing_id" => "$timing_id","from_time" => "$fromDate","to_time" => "$toDate");
                                                
                                            // array_push($time, $timeNew);
                                            
                                            $time['FromTime']=$newConsultTime;
                                            $time['to_time']=$ConsultTime;
                                            $timeAll[] = $time;
                                             $newConsultTime = $ConsultTime;
                                        
                                            }
                                            
                                            
                                            // for($callSlot=0;$callSlot<$finalSlotCall;$callSlot++){
                                               
                                            //     $CallTimeAdded = strtotime("+".$timeCall." minutes", strtotime($newCallTime));
                                            //     $CallTime = date('h:i:s', $CallTimeAdded);
                                                
                                            //     $data_time = array(
                                            //         'doctor_id' => $doctor_id,
                                            //         'clinic_id' => $clinic_id,
                                            //         'from_time' => $newCallTime,
                                            //         'to_time' => $CallTime,
                                            //         'day' => $day,
                                            //         'timeSlot' => $timeSlot,
                                            //         'status' => "0",
                                            //         'consultation_type' => 'call'
                                            //     );
                                                
                                            // $timing_id = $this->PartnerdoctorModel->doctor_add_clinic_timing($data_time);
                                           
                                            // $newCallTime = $CallTime;
                                            
                                            // $timeNew = array();
                                            // $timeNew = array("timing_id" => "$timing_id","from_time" => "$fromDate","to_time" => "$toDate");
                                                
                                            // array_push($time, $timeNew);
                                        
                                            // }
                                            // for($videoSlot=0;$videoSlot<$finalSlotVideo;$videoSlot++){
                                          
                                            //     $VideoTimeAdded = strtotime("+".$timeVideo." minutes", strtotime($newVideoTime));
                                            //     $VideoTime = date('h:i:s', $VideoTimeAdded);
                                               
                                            //      $data_time = array(
                                            //         'doctor_id' => $doctor_id,
                                            //         'clinic_id' => $clinic_id,
                                            //         'from_time' => $newVideoTime,
                                            //         'to_time' => $VideoTime,
                                            //         'day' => $day,
                                            //         'timeSlot' => $timeSlot,
                                            //         'status' => "0",
                                            //         'consultation_type' => 'video'
                                            //     );
                                                
                                            // $timing_id = $this->PartnerdoctorModel->doctor_add_clinic_timing($data_time);
                                            // $newVideoTime = $VideoTime;
                                            // $timeNew = array();
                                            // $timeNew = array("timing_id" => "$timing_id","from_time" => "$fromDate","to_time" => "$toDate");
                                                
                                            // array_push($time, $timeNew);
                                             
                                            // }
                                            // for($textSlot=0;$textSlot<$finalSlotText;$textSlot++){
                                                
                                            //     $TextTimeAdded = strtotime("+".$timeText." minutes", strtotime($newTextTime));
                                            //     $TextTime = date('h:i:s', $TextTimeAdded);
                                               
                                            //      $data_time = array(
                                            //         'doctor_id' => $doctor_id,
                                            //         'clinic_id' => $clinic_id,
                                            //         'from_time' => $newTextTime,
                                            //         'to_time' => $TextTime,
                                            //         'day' => $day,
                                            //         'timeSlot' => $timeSlot,
                                            //         'status' => "0",
                                            //         'consultation_type' => 'text'
                                            //     );
                                                
                                                
                                            
                                            // $timing_id = $this->PartnerdoctorModel->doctor_add_clinic_timing($data_time);
                                            
                                            // $newTextTime = $TextTime;
                                            // $timeNew = array();
                                            // $timeNew = array("timing_id" => "$timing_id","from_time" => "$fromDate","to_time" => "$toDate");
                                                
                                            // array_push($time, $timeNew);
                                                
                                            // }
                                            
                                            
                                           
                                    
                                }
                                // $slotsNew = array();
                                // $slotsNew = array("timeSlot" => "$timeSlot", "time" => "");
                                // $slotsNew['time'][] =  $time;
                                // array_push($slots, $slotsNew);  
                                
                                $slots['time'] = $timeAll;
                                $slots['timeSlot'] = $timeSlot;
                                
                                $slotsAll[]=$slots;
                               
                            }
                            // $timingsNew = array();
                            // $timingsNew = array("day" => "$day", "slots" => "");
                            // $timingsNew['slots'][] =  $slots;
                            // array_push($timingsMain, $timingsNew);
                            $timingsNew['day'] = $day;
                            $timingsNew['slots'] = $slotsAll;
                            
                            $timing[] = $timingsNew;
                            
                    } 
                    // } else { 
                    //       $resp = array('message' => 'Error in adding new slots');
                    //   simple_json_output($resp);
                
                    // }
                    // $respData['timings'] = $timing;
                     $resp = array('status' => 200, 'message' => 'success', 'data' => $respData); 
                //   simple_json_output(array('status' => 200, 'message' => 'success', 'data' => $respData));
                
              simple_json_output($resp);
              
            }
        }
        // else {  $resp = array('status' => 400, 'message' => 'error'); simple_json_output($resp); }
        } 
        // else {  $resp = array('status' => 400, 'message' => 'errorAuth'); simple_json_output($resp); }
        }
        
    }
    
    
    //service for appointment invitation to users
    	 public function appointment_invitation() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" || $params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $user_id = $params['user_id'];
                        $resp = $this->PartnerdoctorModel->appointment_invitation($doctor_id,$user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
     	 public function QR_code_images() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                    
                        $resp = $this->PartnerdoctorModel->QR_code_images($doctor_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function Add_story()
    {
        $this->load->model('PartnerdoctorModel');
         	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
	      	 $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PartnerdoctorModel->auth();
		        if($response['status'] == 200){
					if ($_POST['doctor_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $story_file = "";
					    $doctor_id  = $_POST['doctor_id'];
					    $stroy_text = $_POST['stroy_text'];
					    $cny = count($_FILES);
					    if($cny > 0){
					     $story_file = $_FILES['stroy']['name'];
					    }
					    
					    $resp = $this->PartnerdoctorModel->Add_story($doctor_id, $stroy_text, $story_file);
					
					}
					    simple_json_output($resp);
				
		        }
			}
		}
    }
    
  
    //added by zak for story_list_details 
     public function Add_story_details() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $resp = $this->PartnerdoctorModel->Add_story_details($doctor_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //end
    
    //added by zak for story_list
    
    public function get_story_list() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $resp = $this->PartnerdoctorModel->get_story_list($doctor_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    //end
    
    //added by zak for badge count in doctor application
    
     public function get_Badge_count() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $resp = $this->PartnerdoctorModel->get_Badge_count($doctor_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //added by dinesh for gender switch  in doctor application
    
    public function doctor_list_gender()
    {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields1'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $latitude    = $params['latitude'];
                        $longitude   = $params['longitude'];
                        $gender   = $params['gender'];
                        $page = $params['page'];
                       
                        $resp        = $this->PartnerdoctorModel->doctor_list_gender($latitude, $longitude, $user_id,$gender,$page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function doctor_friends_list()
    {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            
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
                        $user_id     = $params['user_id'];
                      
                        $resp        = $this->PartnerdoctorModel->doctor_friends_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
   
    
    
   /* public function doctor_like() {
        $this->load->model('PartnerdoctorModel');
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
                    if ($params['user_id'] == "" || $params['partners_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $following_id = $params['partners_id'];
                        $resp = $this->PartnerdoctorModel->doctor_like($user_id, $following_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }*/
    
    public function doctor_dating_status() {
        $this->load->model('PartnerdoctorModel');
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
                    if ($params['user_id'] == "" || $params['dating_id'] == "" || $params['status'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $following_id = $params['dating_id'];
                        $status = $params['status'];
                        $resp = $this->PartnerdoctorModel->doctor_dating_status($user_id, $following_id, $status);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    /*******************doctor secret list**********************/
   
      public function doctor_secret_friends_list()
      {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
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
                         $user_id   = $params['user_id'];
                       
                        $resp        = $this->PartnerdoctorModel->doctor_secret_friends_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
   
   public function doctor_request_list()
      {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
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
                         $user_id   = $params['user_id'];
                       
                        $resp        = $this->PartnerdoctorModel->doctor_request_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
   
    
    public function doctor_secret_status() {
        $this->load->model('PartnerdoctorModel');
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
                    if ($params['user_id'] == "" || $params['dating_id'] == "" || $params['status'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $following_id = $params['dating_id'];
                        $status = $params['status'];
                        $resp = $this->PartnerdoctorModel->doctor_secret_status($user_id, $following_id, $status);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    /******************************************************/
    public function view_doctor_profile()
    {
        $this->load->model('PartnerdoctorModel');
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
                        $view_yourself = $params['view_yourself'];
                        if(isset($params['self_user_id']))
                        {
                            $self_user_id = $params['self_user_id'];
                        }
                        else
                        {
                            $self_user_id = "";
                        }
                        $resp        = $this->PartnerdoctorModel->view_doctor_profile($user_id,$view_yourself,$self_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function edit_doctor_profile()
    {
        $this->load->model('PartnerdoctorModel');
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
                    
                   // $params = json_decode(file_get_contents('php://input'), TRUE);
                   
                        $user_id        =  $this->input->post('user_id');
                        $doctor_name    =  $this->input->post('doctor_name');
                        $dob            =  $this->input->post('dob');
                        $qualification  =  $this->input->post('qualification');
                        $experience     =  $this->input->post('experience');
                        $reg_council    =  $this->input->post('reg_council');
                        $reg_number     =  $this->input->post('reg_number');
                        $address        =  $this->input->post('address');
                        $city           =  $this->input->post('city');
                        $state          =  $this->input->post('state');
                        $pincode        =  $this->input->post('pincode');
                        $other_name        =  $this->input->post('other_name');
                        $other_name_type        =  $this->input->post('other_name_type');
                        $placeses        =  $this->input->post('placeses');
                        $relation_ship_status       =  $this->input->post('relation_ship_status');
                        $language_selection        =  $this->input->post('language_selection');
                        $bio  =  $this->input->post('bio');
                        $email        =  $this->input->post('email');
                     
                        
                    if ($user_id=="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                     } 
                    else 
                     {
               
                        $resp = $this->PartnerdoctorModel->edit_doctor_profile($user_id,$doctor_name,$dob,$qualification,$experience,$reg_council,$reg_number,$address,$city,$state,$pincode,$other_name,$other_name_type,$placeses,$relation_ship_status,$language_selection,$bio,$email);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
  public function add_doctor_pic_movies()
    {
        $this->load->model('PartnerdoctorModel');
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
                    
                   // $params = json_decode(file_get_contents('php://input'), TRUE);
                  
                        $user_id     =  $this->input->post('user_id');
                        $fav_details     =  $this->input->post('fav_details');
                        $type     =  $this->input->post('type');
                        
                        
                       if ($user_id == "" ||count($fav_details) <=0) {
                            $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                     } 
                    else 
                     {
               
                        $resp  = $this->PartnerdoctorModel->add_doctor_pic_movies($user_id,$fav_details,$type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function doctor_privacy_profile() {
        $this->load->model('PartnerdoctorModel');
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
                    if ($params['user_id'] == "" || $params['field'] == "" || $params['privacy'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $field = $params['field'];
                        $privacy = $params['privacy'];
                        $resp = $this->PartnerdoctorModel->doctor_privacy_profile($user_id, $field, $privacy);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
      public function doctor_privacy_profile_list() {
        $this->load->model('PartnerdoctorModel');
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
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                       
                        $resp = $this->PartnerdoctorModel->doctor_privacy_profile_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function edit_doctor_album() {
        $this->load->model('PartnerdoctorModel');
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
                    if ($params['user_id'] == "" ||  $params['album_name']  == ""  ||  $params['album_name']  == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $album_name = $params['album_name'];
                        $description = $params['description'];
                        $privacy = $params['privacy'];
                        $album_id = $params['album_id'];
                        $resp = $this->PartnerdoctorModel->edit_doctor_album($user_id,$album_id,$album_name,$description,$privacy);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function create_doctor_album() {
        $this->load->model('PartnerdoctorModel');
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
                    if ($params['user_id'] == "" || $params['album_name']  == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $album_name = $params['album_name'];
                        $description = $params['description'];
                        $privacy = $params['privacy'];
                        $resp = $this->PartnerdoctorModel->create_doctor_album($user_id, $album_name,$description,$privacy);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function add_doctor_album_photos() {
       
        
         $this->load->model('PartnerdoctorModel');
         	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
	      	if ($this->input->post('user_id') == "" ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
			} else {
					     $user_id = $this->input->post('user_id');
                        $album_id = $this->input->post('album_id');
					    $story_file = "";
					    $cny = count($_FILES);
					    if($cny > 0){
					    $story_file = $_FILES['doctor_images']['name'];
					    $resp = $this->PartnerdoctorModel->add_doctor_album_photos($user_id, $album_id, $story_file);
					
					    }else
					    {
					        	$resp = array('status' => 400,'message' =>  'Please Select Atleast One Photo');
					    }
					    
					    
					}
			simple_json_output($resp);
		}
        
        
        
    }
    public function add_doctor_photos() {
        $this->load->model('PartnerdoctorModel');
         	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
	      	if ($this->input->post('user_id') == "" ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
			} else {
					    $user_id = $this->input->post('user_id');
					    $story_file = "";
					    $cny = count($_FILES);
					    if($cny > 0){
					    $story_file = $_FILES['doctor_images']['name'];
					    $resp = $this->PartnerdoctorModel->add_doctor_photos($user_id,$story_file);
					
					    }else
					    {
					        	$resp = array('status' => 400,'message' =>  'Please Select Atleast One Photo');
					    }
					    
					    
					}
			simple_json_output($resp);
		}
    }
    public function doctor_photos_list() {
        $this->load->model('PartnerdoctorModel');
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
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                       
                        $resp = $this->PartnerdoctorModel->doctor_photos_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function doctor_album_list() {
        $this->load->model('PartnerdoctorModel');
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
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                       
                        $resp = $this->PartnerdoctorModel->doctor_album_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function doctor_album_photos_list() {
        $this->load->model('PartnerdoctorModel');
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
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $album_id = $params['album_id'];
                       
                        $resp = $this->PartnerdoctorModel->doctor_album_photos_list($user_id,$album_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function delete_doctor_photos() {
        $this->load->model('PartnerdoctorModel');
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
                    if ($params['user_id'] == "" || $params['photo_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $photo_id = $params['photo_id'];
                       
                        $resp = $this->PartnerdoctorModel->delete_doctor_photos($user_id,$photo_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function delete_doctor_album() {
        $this->load->model('PartnerdoctorModel');
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
                    if ($params['user_id'] == "" || $params['album_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $album_id = $params['album_id'];
                       
                        $resp = $this->PartnerdoctorModel->delete_doctor_album($user_id,$album_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    /*******************Doctor works API*******************************/
      public function add_doctor_works_details()
      {
        $this->load->model('PartnerdoctorModel');
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
                    
              $params = json_decode(file_get_contents('php://input'),  TRUE);
                  
if($params['user_id']=="" ||$params['work_place']=="" || $params['position']=="" || $params['city']=="") 
                   {
                            $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                     } 
                    else 
                     {
                            $user_id = $params['user_id'];
                            $work_place = $params['work_place'];
                            $position = $params['position'];
                            $city = $params['city'];
                            $from_date = $params['from_date'];
                            $from_to = $params['from_to'];
                            $current_status = $params['current_status'];
                            
                           
$resp  = $this->PartnerdoctorModel->add_doctor_works_details($user_id,$work_place,$position,$city,$from_date,$from_to,$current_status);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    
     public function add_doctor_education_details()
      {
        $this->load->model('PartnerdoctorModel');
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
                    
              $params = json_decode(file_get_contents('php://input'),  TRUE);
                  
if($params['user_id']=="" ||$params['college_name']=="" || $params['qualification']=="" || $params['from_date']=="") 
                   {
                            $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                     } 
                    else 
                     {
                            $user_id = $params['user_id'];
                            $college_name = $params['college_name'];
                            $qualification = $params['qualification'];
                            $from_date = $params['from_date'];
                            $to_date = $params['to_date'];
                           
$resp  = $this->PartnerdoctorModel->add_doctor_education_details($user_id,$college_name,$qualification,$from_date,$to_date);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    /**********************************************/
   
    public function all_event_list()
    {
        $this->load->model('PartnerdoctorModel');
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
                        $user_id     = $params['user_id'];
                        $find_date   = $params['date'];
                        $resp        = $this->PartnerdoctorModel->all_event_list($user_id,$find_date);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function update_event_list()
    {
          $this->load->model('PartnerdoctorModel');
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
                        $user_id     = $params['user_id'];
                       // $find_date   = $params['date'];
                        $event_id    = $params['evnet_id'];
                        $intrested_status = $params['intrested_id'];
                        $resp        = $this->PartnerdoctorModel->update_event_list($user_id,$event_id,$intrested_status);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
   
    public function add_event_details()
    {
        $this->load->model('PartnerdoctorModel');
         	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
	      	if ($this->input->post('user_id') == "" || $this->input->post('start_date') == "" || $this->input->post('end_date') == "" || $this->input->post('start_time') == "" || $this->input->post('title') == "" || $this->input->post('venue') == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
			} else {
					    
					    $user_id = $this->input->post('user_id');
                        $start_date = $this->input->post('start_date');
                        $end_date = $this->input->post('end_date');
                        $start_time = $this->input->post('start_time');
                        $end_time = $this->input->post('end_time');
                        $title = $this->input->post('title');
                        $description = $this->input->post('description');
                        $venue = $this->input->post('venue');
                        $lat = $this->input->post('lat');
                        $lang = $this->input->post('lang');
                        
					    $story_file = "";
					    $cny = count($_FILES);
					    if($cny > 0){
					    $story_file = $_FILES['event_images']['name'];
					    }
					    
					    $resp = $this->PartnerdoctorModel->add_event_details($user_id,$start_date,$end_date,$start_time,$end_time,$title,$description,$venue,$lat,$lang,$story_file);
					
					}
			simple_json_output($resp);
		}
    }
    
    public function edit_event_details()
    {
        $this->load->model('PartnerdoctorModel');
         	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
	        	if ($this->input->post('user_id') == "" || $this->input->post('event_id') == "" || $this->input->post('start_date') == "" || $this->input->post('end_date') == "" || $this->input->post('start_time') == "" || $this->input->post('title') == "" || $this->input->post('venue') == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
			} else {
					    
					    $user_id = $this->input->post('user_id');
					    $event_id = $this->input->post('event_id');
                        $start_date = $this->input->post('start_date');
                        $end_date = $this->input->post('end_date');
                        $start_time = $this->input->post('start_time');
                        $end_time = $this->input->post('end_time');
                        $title = $this->input->post('title');
                        $description = $this->input->post('description');
                        $venue = $this->input->post('venue');
                        $lat = $this->input->post('lat');
                        $lang = $this->input->post('lang');
                        
					    $story_file = "";
					    $cny = count($_FILES);
					    if($cny > 0){
					    $story_file = $_FILES['event_images']['name'];
					    }
					    
					    $resp = $this->PartnerdoctorModel->edit_event_details($user_id,$event_id,$start_date,$end_date,$start_time,$end_time,$title,$description,$venue,$lat,$lang,$story_file);
					
					}
			simple_json_output($resp);
		}
    }
    
    public function delete_event_image()
    {
          $this->load->model('PartnerdoctorModel');
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
                    if ($params['event_id'] == "" || $params['image_name'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $event_id     = $params['event_id'];
                        $image_name    = $params['image_name'];
                      
                        $resp        = $this->PartnerdoctorModel->delete_event_image($event_id,$image_name);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function delete_event()
    {
          $this->load->model('PartnerdoctorModel');
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
                    if ($params['event_id'] == "" || $params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $event_id     = $params['event_id'];
                        $user_id    = $params['user_id'];
                      
                        $resp        = $this->PartnerdoctorModel->delete_event($event_id,$user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function all_event_list_new()
    {
        $this->load->model('PartnerdoctorModel');
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
                        $user_id     = $params['user_id'];
                        $status      = $params['status'];   // all/upcoming/my
                       
                        $resp        = $this->PartnerdoctorModel->all_event_list_new($user_id,$status);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
    public function add_highlights()
    {
        $this->load->model('PartnerdoctorModel');
         	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
	      	if ($this->input->post('user_id') == "" || $this->input->post('event_id') == "" ) {
	      	   
						$resp = array('status' => 400,'message' =>  'please enter fields');
			} else {
				//   
					    $user_id = $this->input->post('user_id');
                        $event_id = $this->input->post('event_id');
                        $description = $this->input->post('description');
                        
                         // multiple image/video
    	        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                $video_format = array("mp4", "avi", "flv", "wmv", "mov", "3gp", "MP4", "AVI", "FLV", "WMV", "MOV", "3GP");
                include('s3_config.php');
                   $img=array();
                     $img_v=array();
                   
    	        //$image = count($_FILES['image']['name']); 
                if (! empty($_FILES["image"]["name"])) {
                    $flag = '1';
                    $video_flag = '1';
                    foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                        $img_name = $key . $_FILES['image']['name'][$key];
                        $img_size = $_FILES['image']['size'][$key];
                        $img_tmp = $_FILES['image']['tmp_name'][$key];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) 
                           {
                                if (in_array($ext, $img_format)) 
                                  {
                                    $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/Event_images/' . $actual_image_name;
                                    if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                        
                                       $img[]=$actual_image_name;
                                    }
                                }
                                if (in_array($ext, $video_format)) {
                                    $uniqid = uniqid() . date("YmdHis");
                                    $actual_video_name = $uniqid . "." . $ext;
                                    $actual_video_path = 'images/Event_images/' . $actual_video_name;
                                    if ($s3->putObjectFile($img_tmp, $bucket, $actual_video_path, S3::ACL_PUBLIC_READ)) {
                                       
                                      $img_v[]=$actual_video_name;
        
                                    }
                                }
                        }
                    }
                }    
                          
                 
                  
        		        if(!empty($img)) {
        		        $img1=implode(",",$img);
                    }
                    else{
                        $img1="";
                    }
        		        
        		        
        		   
        		        if(!empty($img_v)) {
        		        $img1_v=implode(",",$img_v);
                    }
                    else{
                        $img1_v="";
                    }
                 
                
              $resp = $this->PartnerdoctorModel->add_highlights($user_id,$event_id,$description,$img1,$img1_v);
					
					}
			simple_json_output($resp);
		}
    }
    
    public function Jayesh($file_post)
    {
        $file_ary   = array();
        $file_count = count($file_post['name']);
        $file_keys  = array_keys($file_post);
        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }
        
        return $file_ary;
    }
    public function all_share_follow_list()
    {
        $this->load->model('PartnerdoctorModel');
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
                        $user_id     = $params['user_id'];
                       
                        $resp        = $this->PartnerdoctorModel->all_share_follow_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function event_highlights()
    {
          $this->load->model('PartnerdoctorModel');
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
                    if ($params['user_id'] == "" || $params['event_id'] == "" || $params['status'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $event_id    = $params['event_id'];
                        $status    = $params['status'];
                        $resp        = $this->PartnerdoctorModel->event_highlights($user_id,$event_id,$status);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
   
   
    public function story_highlight()
    {
          $this->load->model('PartnerdoctorModel');
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
                    if ($params['user_id'] == "" || $params['story_id'] == "" || $params['status'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $story_id    = $params['story_id'];
                        $status    = $params['status'];
                        $resp        = $this->PartnerdoctorModel->story_highlight($user_id,$story_id,$status);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
     public function story_highlight_list()
    {
          $this->load->model('PartnerdoctorModel');
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
                        $user_id    = $params['user_id'];
                       
                        $resp        = $this->PartnerdoctorModel->story_highlight_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
     public function add_live_url()
    {
          $this->load->model('PartnerdoctorModel');
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
                    if ($params['doctor_id'] == "" || $params['url'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['doctor_id'];
                        $url    = $params['url'];
                        $type    = $params['type'];
                        $id      = $params['id'];
                        $resp        = $this->PartnerdoctorModel->add_live_url($user_id,$url,$type,$id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function dashboard_counter() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $resp = $this->PartnerdoctorModel->dashboard_counter($doctor_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function block_list() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $resp = $this->PartnerdoctorModel->block_list($doctor_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function doctor_update_block() {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" || $params['status'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $user_id = $params['user_id'];
                        $status=$params['status'];
                        $resp = $this->PartnerdoctorModel->doctor_update_block($user_id,$doctor_id,$status);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function doctor_feedback()
    {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $feedback        = $list['feedback'];
                    $email          = $list['email'];
                    $user_id        = $list['user_id'];
                 
                    if ($user_id == "" || $feedback=="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerdoctorModel->doctor_feedback($user_id,$feedback,$email); 
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function doctor_status_change()
    {
        $this->load->model('PartnerdoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerdoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerdoctorModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $from_date        = $list['from_date'];
                    $clinic_id   = $list['clinic_id'];
                    $user_id     = $list['user_id'];
                   // $to_date          = $list['to_date'];
                    $from_time   = $list['from_time'];
                    $to_time    = $list['to_time'];
                    $reason    = $list['reason'];
                    if ($user_id == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerdoctorModel->doctor_status_change($user_id,$clinic_id,$from_date,$from_time,$to_time,$reason); 
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
}
