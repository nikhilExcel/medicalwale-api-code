<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Partnerpersonaltrainers extends CI_Controller
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
    public function signup()
    {
        $this->load->model('PartnerpersonaltrainersModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerpersonaltrainersModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerpersonaltrainersModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['area_expertise'] == "" || $params['name'] == "" || $params['email'] == "" || $params['phone'] == "" || $params['gender'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $category = $params['area_expertise'];
                        $type     = $params['type'];
                        $name     = $params['name'];
                        $email    = $params['email'];
                        $phone    = $params['phone'];
                        $gender   = $params['gender'];
                        $dob      = $params['dob'];
                        $token    = $params['token'];
                        $agent    = $params['agent'];
                        $resp     = $this->PartnerpersonaltrainersModel->signup($category, $type, $name, $email, $phone, $gender, $dob, $token, $agent);
                    }
                }
                simple_json_output($resp);
            }
        }
    }
    
    public function personaltrainers_profile_pic()
    {
        $this->load->model('PartnerpersonaltrainersModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {            
            if ($_POST['listing_id'] == "" || empty($_FILES["profile_pic"]["name"])) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                $listing_id = $_POST['listing_id'];
                if (isset($_FILES["profile_pic"]) && !empty($_FILES["profile_pic"]["name"])) {                    
                    //unlink images
                    $file_query = $this->db->query("SELECT image FROM `personal_trainers` WHERE user_id='$listing_id'");
                    $get_file   = $file_query->row_array();                    
                    if ($get_file) {
                        $profile_pic = $get_file['image'];
                        $file = "images/healthwall_avatar/".$profile_pic;
						@unlink(trim($file));
						DeleteFromToS3($file);	
                    } 
					include('s3_config.php');
					$img_name = $_FILES['profile_pic']['name'];
					$img_size = $_FILES['profile_pic']['size'];
					$img_tmp  = $_FILES['profile_pic']['tmp_name'];
					$ext      = getExtension($img_name);
                
					if(strlen($img_name) > 0) {
						if ($img_size < (50000 * 50000)) {
							if (in_array($ext, $img_format)) {
								$profile_pic_file = uniqid() . date("YmdHis") . "." . $ext;
								$actual_image_path        = 'images/healthwall_avatar/' . $profile_pic_file;
								$s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
								
							}
						}
					}
                }
                //unlink images ends                
                $resp = $this->PartnerpersonaltrainersModel->personaltrainers_profile_pic($listing_id, $profile_pic_file);
            }            
            simple_json_output($resp);
        }
    }    
    
    public function partner_statistics()
    {
        $this->load->model('PartnerpersonaltrainersModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerpersonaltrainersModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerpersonaltrainersModel->auth();
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
                        $resp       = $this->PartnerpersonaltrainersModel->partner_statistics($listing_id, $type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function personaltrainers_details()
    {
        $this->load->model('PartnerpersonaltrainersModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerpersonaltrainersModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerpersonaltrainersModel->auth();
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
                        $resp       = $this->PartnerpersonaltrainersModel->personaltrainers_details($listing_id, $type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function personaltrainers_lat_log()
    {
        $this->load->model('PartnerpersonaltrainersModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerpersonaltrainersModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerpersonaltrainersModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                    if ($params['latitude'] == "" || $params['longitude'] == "" || $params['radius'] == "" || $params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $latitude   = $params['latitude'];
                        $longitude  = $params['longitude'];
                        $radius     = $params['radius'];
                        $listing_id = $params['listing_id'];
                        
                        $resp = $this->PartnerpersonaltrainersModel->personaltrainers_lat_log($latitude, $longitude, $radius, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
        
    public function personaltrainers_is_free_trial_session()
    {
        $this->load->model('PartnerpersonaltrainersModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerpersonaltrainersModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerpersonaltrainersModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                    if ($params['is_free_trial_session'] == "" || $params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        $listing_id            = $params['listing_id'];
                        $is_free_trial_session = $params['is_free_trial_session'];
                        
                        $resp = $this->PartnerpersonaltrainersModel->personaltrainers_is_free_trial_session($listing_id, $is_free_trial_session);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function personaltrainers_kyc_pic()
    {
        $this->load->model('PartnerpersonaltrainersModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            
            if ($_POST['listing_id'] == "" || empty($_FILES["kyc_pic"]["name"])) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                $listing_id = $_POST['listing_id'];
                if (isset($_FILES["kyc_pic"]) && !empty($_FILES["kyc_pic"]["name"])) {                    
                    //unlink images
                    $file_query = $this->db->query("SELECT kyc_pic FROM `personal_trainers` WHERE user_id='$listing_id'");
                    $get_file   = $file_query->row_array();
                    if ($get_file) {
                        $kyc_pic = $get_file['kyc_pic'];
						$file = "images/personal_trainers_images/".$kyc_pic;
						 @unlink(trim($file));
						 DeleteFromToS3($file);	
                    }
					include('s3_config.php');
					$img_name = $_FILES['kyc_pic']['name'];
					$img_size = $_FILES['kyc_pic']['size'];
					$img_tmp  = $_FILES['kyc_pic']['tmp_name'];
					$ext      = getExtension($img_name);
                
					if(strlen($img_name) > 0) {
						if ($img_size < (50000 * 50000)) {
							if (in_array($ext, $img_format)) {
								$kyc_pic_file = uniqid() . date("YmdHis") . "." . $ext;
							$actual_image_path= 'images/personal_trainers_images/' . $kyc_pic_file;
							$s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
								
							}
						}
					}
                }
                        
                $resp = $this->PartnerpersonaltrainersModel->personaltrainers_kyc_pic($listing_id, $kyc_pic_file);
            }            
            simple_json_output($resp);
        }
    }    
    
    public function personaltrainers_is_approval()
    {
        $this->load->model('PartnerpersonaltrainersModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerpersonaltrainersModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerpersonaltrainersModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);                    
                    if ($params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        $listing_id = $params['listing_id'];
                        
                        $resp = $this->PartnerpersonaltrainersModel->personaltrainers_is_approval($listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }    
    
    public function personaltrainers_my_profile_details()
    {
        $this->load->model('PartnerpersonaltrainersModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerpersonaltrainersModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerpersonaltrainersModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);                    
                    if ($params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {                        
                        $listing_id = $params['listing_id'];                        
                        $resp = $this->PartnerpersonaltrainersModel->personaltrainers_my_profile_details($listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function personaltrainers_update_profile()
    {
        $this->load->model('PartnerpersonaltrainersModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerpersonaltrainersModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerpersonaltrainersModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['name'] == "" || $params['email'] == "" || $params['phone'] == "" || $params['gender'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {                        
                        $listing_id = $params['listing_id'];
                        $name       = $params['name'];
                        $email      = $params['email'];
                        $phone      = $params['phone'];
                        $gender     = $params['gender'];
                        $dob        = $params['dob'];
                        $resp       = $this->PartnerpersonaltrainersModel->personaltrainers_update_profile($listing_id, $name, $email, $phone, $gender, $dob);
                    }
                }
                simple_json_output($resp);
            }
        }
    }
    
}