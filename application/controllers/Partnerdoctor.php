<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Partnerdoctor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
    public function council_list()
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
    
    public function signup()
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
                    if ($params['area_expertise'] == "" || $params['type'] == "" || $params['doctor_name'] == "" || $params['email'] == "" || $params['phone'] == "" || $params['qualification'] == "" || $params['experience'] == "" || $params['gender'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $category      = $params['area_expertise'];
                        $type          = $params['type'];
                        $doctor_name   = $params['doctor_name'];
                        $email         = $params['email'];
                        $phone         = $params['phone'];
                        $qualification = $params['qualification'];
                        $experience    = $params['experience'];
                        $gender        = $params['gender'];
                        $dob           = $params['dob'];
                        $reg_council   = $params['reg_council'];
                        $reg_number    = $params['reg_number'];
                        $token         = $params['token'];
                        $agent         = $params['agent'];
                        $resp          = $this->PartnerdoctorModel->signup($category, $type, $doctor_name, $email, $phone, $qualification, $experience, $gender, $dob, $reg_council, $reg_number, $token, $agent);
                    }
                }
                simple_json_output($resp);
            }
        }
    }    
    
    public function doctor_profile_pic()
    {
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
                $get_file   = $file_query->row();                
                if ($get_file) {
                    $profile_pic = $get_file->image;
                    $file = "images/healthwall_avatar/".$profile_pic;
					@unlink(trim($file));
					DeleteFromToS3($file);
                }
                //unlink images ends

                $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                include('s3_config.php');
                
                $img_name = $_FILES['profile_pic']['name'];
                $img_size = $_FILES['profile_pic']['size'];
                $img_tmp  = $_FILES['profile_pic']['tmp_name'];
                $ext      = getExtension($img_name);
                
                if (strlen($img_name) > 0) {
                    if ($img_size < (50000 * 50000)) {
                        if (in_array($ext, $img_format)) {
                            $profile_pic_file  = uniqid() . date("YmdHis") . "." . $ext;
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
    
    public function doctor_my_profile_details()
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
                    if ($params['listing_id'] == "" || $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $type       = $params['type'];
                        $resp       = $this->PartnerdoctorModel->doctor_my_profile_details($listing_id, $type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function doctor_specialization_update()
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
                    if ($params['listing_id'] == "" || $params['speciality'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $speciality = $params['speciality'];
                        $resp       = $this->PartnerdoctorModel->doctor_specialization_update($listing_id, $speciality);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function doctor_specialization()
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
                    $resp = $this->PartnerdoctorModel->doctor_specialization();
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function doctor_services()
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
                    $resp = $this->PartnerdoctorModel->doctor_services();
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function doctor_details()
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
                    if ($params['listing_id'] == "" || $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $type       = $params['type'];
                        $resp       = $this->PartnerdoctorModel->doctor_details($listing_id, $type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function doctor_documents_upload()
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
            $listing_id = $this->input->post('listing_id');            
            if ($listing_id == "" || empty($_FILES["medical_registration_pic"]["name"]) || empty($_FILES["medical_degree_pic"]["name"]) || empty($_FILES["government_id_pic"]["name"]) || empty($_FILES["prescription_pad_pic"]["name"]) || empty($_FILES["business_card_pic"]["name"])) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {   

                $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                include('s3_config.php');
                $img_name = $_FILES['medical_registration_pic']['name'];
                $img_size = $_FILES['medical_registration_pic']['size'];
                $img_tmp  = $_FILES['medical_registration_pic']['tmp_name'];
                $ext      = getExtension($img_name);
                
                if (strlen($img_name) > 0) {
                    if ($img_size < (50000 * 50000)) {
                        if (in_array($ext, $img_format)) {
                            $medical_registration_pic = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path        = 'images/doctor_images/' . $medical_registration_pic;
                            $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                            
                        }
                    }
                }                
                
                $img_name2 = $_FILES['medical_degree_pic']['name'];
                $img_size2 = $_FILES['medical_degree_pic']['size'];
                $img_tmp2  = $_FILES['medical_degree_pic']['tmp_name'];
                $ext2      = getExtension($img_name);
                
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
                $img_tmp3  = $_FILES['government_id_pic']['tmp_name'];
                $ext3      = getExtension($img_name);
                
                if (strlen($img_name3) > 0) {
                    if ($img_size3 < (50000 * 50000)) {
                        if (in_array($ext3, $img_format)) {
                            $government_id_pic  = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path3 = 'images/doctor_images/' . $government_id_pic;
                            $s3->putObjectFile($img_tmp3, $bucket, $actual_image_path3, S3::ACL_PUBLIC_READ);
                            
                        }
                    }
                }
                
                $img_name4 = $_FILES['prescription_pad_pic']['name'];
                $img_size4 = $_FILES['prescription_pad_pic']['size'];
                $img_tmp4  = $_FILES['prescription_pad_pic']['tmp_name'];
                $ext4      = getExtension($img_name);
                
                if (strlen($img_name4) > 0) {
                    if ($img_size4 < (50000 * 50000)) {
                        if (in_array($ext4, $img_format)) {
                            $prescription_pad_pic = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path4   = 'images/doctor_images/' . $prescription_pad_pic;
                            $s3->putObjectFile($img_tmp4, $bucket, $actual_image_path4, S3::ACL_PUBLIC_READ);
                            
                        }
                    }
                }
                
                $img_name5 = $_FILES['business_card_pic']['name'];
                $img_size5 = $_FILES['business_card_pic']['size'];
                $img_tmp5  = $_FILES['business_card_pic']['tmp_name'];
                $ext5      = getExtension($img_name);
                
                if (strlen($img_name5) > 0) {
                    if ($img_size5 < (50000 * 50000)) {
                        if (in_array($ext5, $img_format)) {
                            $business_card_pic  = uniqid() . date("YmdHis") . "." . $ext;
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
    
    
}