<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function login() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $username = $params['username'];
                $password = $params['password'];
                $response = $this->LoginModel->login($username, $password);
                json_output($response['status'], $response);
            }
        }
    }
    

    
    public function check_recent_login() {
        $this->load->model('LoginModel');
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
                        $resp = $this->LoginModel->check_recent_login($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    
    
    
    public function applogin() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $username = $params['username'];
                $token = $params['token'];
                $agent = $params['agent'];
                $password = $params['password'];
                $password=md5($password);
                $response = $this->LoginModel->applogin($username,$password,$token,$agent);
                simple_json_output($response);
            }
        }
    }
    
    public function app_set_password() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $password = $params['password'];
                $password=md5($password);
                $response = $this->LoginModel->app_set_password($user_id,$password);
                simple_json_output($response);
            }
        }
    }

    public function logout() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->logout();
                json_output($response['status'], $response);
            }
        }
    }

    public function appversion() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'GET') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $response = $this->LoginModel->appversion();
                simple_json_output($response);
            }
        }
    }

    public function sendotp() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $phone = $params['phone'];
                $response = $this->LoginModel->sendotp($phone);
                otp_json_output($response);
            }
        }
    }

    public function userdetails() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $listing_id = $params['listing_id'];
                $user_id = $params['user_id'];
                $response = $this->LoginModel->userdetails($listing_id, $user_id);
                userlogin_json_output($response);
            }
        }
    }
    
    public function sociallogin() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $oauth_provider = $params['oauth_provider'];
                $oauth_uid = $params['oauth_uid'];
                $email = $params['email']; 
                $phone = $params['phone'];
                $agent = $params['agent'];
                $token = $params['token'];
                $name = $params['name'];
                $gender = $params['gender'];
                $image = $params['image'];

                $response = $this->LoginModel->sociallogin($email,$phone,$oauth_provider,$oauth_uid,$agent,$name,$gender,$image,$token);
                userlogin_json_output($response);
            }
        }
    }

    public function userlogin() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $phone = $params['phone'];
                $token = $params['token'];
                $agent = $params['agent'];

                if (array_key_exists('hash_key', $params) && $params['hash_key'] != null) {
                    $hash_key = $params['hash_key'];
                } else {
                    $hash_key = 'pBrUetoPZhH';
                }

                $response = $this->LoginModel->userlogin($phone, $token, $agent, $hash_key);
                // $response = $this->LoginModel->userlogin($phone, $token, $agent);
                simple_json_output($response);
            }
        }
    }

    public function checkUserDetails() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $response = $this->LoginModel->checkUserDetails($user_id);
                userlogin_json_output($response);
            }
        }
    }

    // sample to check mail
    public function userloginformail() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $phone = $params['phone'];
                $token = $params['token'];
                $agent = $params['agent'];
                $mail = $params['mail'];
                $is_mail = $params['is_mail'];
                $response = $this->LoginModel->userloginformail($phone, $mail, $token, $agent, $is_mail);
                simple_json_output($response);
            }
        }
    }
    
        public function forgotpassword_app() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $username = $params['username'];
                $type = $params['type'];
                $hash_key = $params['hash_key'];
                $agent = $params['agent'];
                $response = $this->LoginModel->forgotpassword_app($username,$type,$hash_key,$agent);
                simple_json_output($response);
            }
        }
    }

    public function user_login() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $phone = $params['phone'];
                $token = $params['token'];
                $agent = $params['agent'];
                $country_code = $params['country_code'];
                $response = $this->LoginModel->user_login($country_code, $phone, $token, $agent);
                simple_json_output($response);
            }
        }
    }

    // verify OTP
    public function otp_verify() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $phone = $params['phone'];
                $otp = $params['otp'];
                $response = $this->LoginModel->otp_verify($phone, $otp);
                simple_json_output($response);
            }
        }
    }

    public function activate_privilage() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $card_no = $params['card_no'];
                $response = $this->LoginModel->activate_privilage($user_id, $card_no);
                simple_json_output($response);
            }
        }
    }

    public function generate_privilage() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                if(array_key_exists('coupon_id', $params)){
                            $coupon_id = $params['coupon_id'];
                             
                        } else {
                            $coupon_id = 0;
                        }
                $response = $this->LoginModel->generate_privilage($user_id,$coupon_id);
                simple_json_output($response);
            }
        }
    }

    //for activate previlage card with coupon code

    public function activate_privilage_with_Coupon() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $card_no = $params['card_no'];
                $coupon_code = $params['coupon_code'];
                $response = $this->LoginModel->activate_privilage_with_Coupon($user_id, $card_no, $coupon_code);
                simple_json_output($response);
            }
        }
    }

    //new service for check privilage card is exist with coupon code or not


    public function check_privilage_card_with_Coupon() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $card_no = $params['card_no'];
                $response = $this->LoginModel->check_privilage_card_with_Coupon($user_id, $card_no);
                simple_json_output($response);
            }
        }
    }

    //new service for genrate coupon with otp validation

    public function generate_coupon_code_for_card() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $card_no = $params['card_no'];
                $response = $this->LoginModel->generate_coupon_code_for_card($user_id, $card_no);
                simple_json_output($response);
            }
        }
    }

    // new service for validate code with privilage card

    public function verify_generated_coupon($user_id, $card_no, $coupon_code) {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $card_no = $params['card_no'];
                $coupon_code = $params['coupon_code'];
                $response = $this->LoginModel->verify_generated_coupon($user_id, $card_no, $coupon_code);
                simple_json_output($response);
            }
        }
    }

    public function usersignup() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $name = $this->input->post('name');
            $phone = $this->input->post('phone');
            $gender = $this->input->post('gender');
            $dob = $this->input->post('dob');
            $token = $this->input->post('token');
            $agent = $this->input->post('agent');
            $mail = $this->input->post('email');
            $is_mail = $this->input->post('is_mail');
            if(!empty($this->input->post('deviceId')))
            {
                $deviceId = $this->input->post('deviceId');
            }
            else
            {
                $deviceId = "";
            }
            
            //new code for email
            if ($is_mail == 'android_mail' || $is_mail == 'ios_mail') {
                if ($name == "" || $mail == "" || $gender == "") {
                   // echo " android mail";
                    $resp = array(
                        'status' => 400,
                        'message' => 'failure'
                    );
                } else {
                    if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['image']['name'];
                        $img_size = $_FILES['image']['size'];
                        $img_tmp = $_FILES['image']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $image = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/healthwall_avatar/' . $image;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }
                            }
                        }
                    } else {

                        $image = '';
                    }
                    $resp = $this->LoginModel->usersignup($name, $phone, $mail, $gender, $dob, $image, $token, $agent, $is_mail, $deviceId);
                }
            } else {

                if ($name == "" || $phone == "" || $gender == "") {

                    $resp = array(
                        'status' => 400,
                        'message' => 'failure'
                    );
                } else {
                    if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['image']['name'];
                        $img_size = $_FILES['image']['size'];
                        $img_tmp = $_FILES['image']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $image = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/healthwall_avatar/' . $image;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }
                            }
                        }
                    } else {

                        $image = '';
                    }
                    $resp = $this->LoginModel->usersignup($name, $phone, $mail, $gender, $dob, $image, $token, $agent, $is_mail, $deviceId);
                }
            }




            simple_json_output($resp);
        }
    }

    public function add_family() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            $name = $this->input->post('name');
            $phone = $this->input->post('phone');
            $gender = $this->input->post('gender');
            $dob = $this->input->post('dob');
            $token = $this->input->post('token');
            $agent = $this->input->post('agent');
            $email = $this->input->post('email');
            $relationship = $this->input->post('relationship');
			
			if ($name == "" || $relationship == "" || $gender == "") {
                    $resp = array(
                        'status' => 208,
                        'message' => 'Please enter all fields'
                    );
                } else {
                    if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['image']['name'];
                        $img_size = $_FILES['image']['size'];
                        $img_tmp = $_FILES['image']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $image = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/healthwall_avatar/' . $image;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }
                            }
                        }
                    } else {

                        $image = '';
                    }
                    $resp = $this->LoginModel->add_family($user_id,$name, $phone, $email, $gender, $dob, $image, $token, $agent, $relationship);
            }


            simple_json_output($resp);
        }
    }
	
	public function update_family() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
           
            $id = $this->input->post('profile_id');
            $name = $this->input->post('name');
            $phone = $this->input->post('phone');
            $gender = $this->input->post('gender');
            $dob = $this->input->post('dob');
            $email = $this->input->post('email');
            $relationship = $this->input->post('relationship');
			
			if ($name == "" || $relationship == "" || $gender == "") {
                    $resp = array(
                        'status' => 208,
                        'message' => 'Please enter all fields'
                    );
                } else {
					$file_query = $this->db->query("SELECT  health_record.avatar_id, media.id as media_id, media.source FROM `health_record` LEFT JOIN media ON health_record.avatar_id = media.id WHERE  health_record.id='$id'");
					$get_file = $file_query->row();
					$profile_pic = $get_file->source;
                    if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['image']['name'];
                        $img_size = $_FILES['image']['size'];
                        $img_tmp = $_FILES['image']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $image = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/healthwall_avatar/' . $image;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);								

									$profile_pic_ = 'images/healthwall_avatar/' . $profile_pic;
								    @unlink(trim($profile_pic_));
								    $delete_from_s3 = DeleteFromToS3($profile_pic_);
                                }
                            }
                        }
                    } else {
                        $image = $profile_pic;
                    }
                    $resp = $this->LoginModel->update_family($id, $name, $phone, $email, $gender, $dob, $image, $relationship);
            }


            simple_json_output($resp);
        }
    }
	
	public function delete_family() {
        $this->load->model('LoginModel');
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

                    if ($params['user_id'] == "" || $params['family_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $family_id = $params['family_id'];
                        $resp = $this->LoginModel->delete_family($user_id, $family_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
	
	public function family_tree() {
        $this->load->model('LoginModel');
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
                        $resp = $this->LoginModel->family_tree($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function family_relation_tree() {
        $this->load->model('LoginModel');
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
                        $resp = $this->LoginModel->family_relation_tree($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    //added by zak for new user through create miss belly  
    //start
    public function usersignup_for_app() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $name = $this->input->post('name');
            $phone = $this->input->post('phone');
            $gender = $this->input->post('gender');

            $dob = $this->input->post('dob');
            $token = $this->input->post('token');
            $agent = $this->input->post('agent');
            $mail = $this->input->post('email');
            $is_mail = $this->input->post('is_mail');
            $is_user = $this->input->post('is_user');
            //new code for email
            if ($is_mail == 'android_mail' || $is_mail == 'ios_mail') {
                if ($name == "" || $mail == "" || $gender == "") {
                    //echo " android mail";
                    $resp = array(
                        'status' => 400,
                        'message' => 'failure'
                    );
                } else {
                    if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['image']['name'];
                        $img_size = $_FILES['image']['size'];
                        $img_tmp = $_FILES['image']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $image = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/healthwall_avatar/' . $image;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }
                            }
                        }
                    } else {

                        $image = '';
                    }
                    $resp = $this->LoginModel->usersignup_for_app($name, $phone, $mail, $gender, $dob, $image, $token, $agent, $is_mail, $is_user);
                }
            } else {

                if ($name == "" || $phone == "" || $gender == "") {

                    $resp = array(
                        'status' => 400,
                        'message' => 'failure'
                    );
                } else {
                    if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['image']['name'];
                        $img_size = $_FILES['image']['size'];
                        $img_tmp = $_FILES['image']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $image = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/healthwall_avatar/' . $image;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }
                            }
                        }
                    } else {

                        $image = '';
                    }
                    $resp = $this->LoginModel->usersignup_for_app($name, $phone, $mail, $gender, $dob, $image, $token, $agent, $is_mail, $is_user);
                }
            }




            simple_json_output($resp);
        }
    }

    //end
    //added by zak on for email signup 
    //start
    public function usersignupformail() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $name = $this->input->post('name');
            $phone = $this->input->post('phone');
            $gender = $this->input->post('gender');
            $dob = $this->input->post('dob');
            $token = $this->input->post('token');
            $agent = $this->input->post('agent');
            $mail = $this->input->post('mail');
            if ($name == "" || $gender == "" || $mail == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'failure'
                );
            } else {
                if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {
                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                    include('s3_config.php');
                    $img_name = $_FILES['image']['name'];
                    $img_size = $_FILES['image']['size'];
                    $img_tmp = $_FILES['image']['tmp_name'];
                    $ext = getExtension($img_name);
                    if (strlen($img_name) > 0) {
                        if ($img_size < (50000 * 50000)) {
                            if (in_array($ext, $img_format)) {
                                $image = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/healthwall_avatar/' . $image;
                                $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                            }
                        }
                    }
                } else {

                    $image = '';
                }
                $resp = $this->LoginModel->usersignupformail($name, $phone, $gender, $dob, $image, $token, $agent, $mail);
            }
            simple_json_output($resp);
        }
    }

    //end

    public function userprofile() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            $data['bmi'] = $this->input->post('bmi');
            $data['activity_level'] = $this->input->post('activity_level');
            $data['marital_status'] = $this->input->post('marital_status');
            $data['diet_fitness'] = $this->input->post('diet_fitness');
            $data['height'] = $this->input->post('height');
            $data['weight'] = $this->input->post('weight');
            $data['organ_donor'] = $this->input->post('organ_donor');
            $data['blood_group'] = $this->input->post('blood_group');
            $data['exercise_level'] = $this->input->post('exercise_level');
            $data['health_condition'] = $this->input->post('health_condition');
            $data['allergies'] = $this->input->post('allergies');
            $data['marital_status'] = $this->input->post('marital_status');
            $data['ask_saheli'] = $this->input->post('ask_saheli');
            $data['health_insurance'] = $this->input->post('health_insurance');
            $data['blood_is_active'] = $this->input->post('blood_is_active');
            $data['heradiatry_problem'] = $this->input->post('heradiatry_problem');
            $data['addiction'] = $this->input->post('addiction');
            //$data['addiction_value'] = $this->input->post('addiction_value');
            $data['lat'] = $this->input->post('lat');
            $data['lng'] = $this->input->post('lng');
            $data['map_location'] = $this->input->post('map_location');
            $data['height_cm_ft'] = $this->input->post('height_cm_ft');
            $genetic_disorder = $this->input->post('medical_genetic_disorder');
            $question = $this->input->post('question');


            $questions = json_decode($question);
            /* print_r ($questions);
              die(); */
            // foreach($questions['quastion'] as $qa){
            //   // echo $qa['user_id'];
            //     echo $qa['qid'];
            //     echo $qa['qans'];
            // }
            $pre_final_que = $questions->quastion;
            $final_que = $pre_final_que[0];
            $q_user_id = $final_que->user_id;
            $final_q = $final_que->qas;
            $data2 = array();
            $resp = $this->LoginModel->delete_question($q_user_id);
            for ($i = 0; $i < sizeof($final_q); $i++) {

                $data2['user_id'] = $q_user_id;
                $data2['question_id'] = $final_q[$i]->qid;
                $data2['answer'] = $final_q[$i]->qans;

                $resp = $this->LoginModel->update_userprofile_question($data2);
            }


            if ($user_id == "") {
                $respStatus = 400;
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter all field'
                );
            } else {
                $respStatus = 200;
                $resp = $this->LoginModel->userprofile($user_id, $data, $genetic_disorder);
            }
            json_output($respStatus, $resp);
        }
    }

    public function blood_group_update() {
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
                $respStatus = $response['status'];
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];

                    $blood_group = $params['blood_group'];
                    $blood_is_active = $params['blood_is_active'];
                    if ($params['user_id'] == "") {
                        $respStatus = 400;
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all field'
                        );
                    } else {
                        $respStatus = 200;
                        $resp = $this->LoginModel->blood_group_update($user_id, $blood_group, $blood_is_active);
                    }
                    json_output($respStatus, $resp);
                }
            }
        }
    }

    public function follow() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['following_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $following_id = $params['following_id'];
                        $resp = $this->LoginModel->follow($user_id, $following_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function following_list() {
        $this->load->model('LoginModel');
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
                        $resp = $this->LoginModel->following_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function follower_list() {
        $this->load->model('LoginModel');
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
                        $resp = $this->LoginModel->follower_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function address_list() {
        $this->load->model('LoginModel');
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
                        $resp = $this->LoginModel->address_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function address_add() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['name'] == "" || $params['mobile'] == "" || $params['pincode'] == "" || $params['address1'] == "" || $params['city'] == "" || $params['state'] == "" || $params['address_type'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                         $user_id = $params['user_id'];
                        $name = $params['name'];
                        $mobile = $params['mobile'];
                        $pincode = $params['pincode'];
                        $address1 = $params['address1'];
                        $city = $params['city'];
                        $state = $params['state'];
                        if (array_key_exists("address2",$params)){
                            $address2 = $params['address2'];
                        } else  {
                            $address2 = "";
                        }
                        
                        if (array_key_exists("landmark",$params)){
                            $landmark = $params['landmark'];
                        } else  {
                            $landmark = "";
                        }
                        
                        if (array_key_exists("address_type",$params)){
                            $address_type = $params['address_type'];
                        } else  {
                            $address_type = "";
                        }
                        
                        if (array_key_exists("lat",$params)){
                            $lat = $params['lat'];
                        } else  {
                            $lat = "";
                        }
                        
                        if (array_key_exists("lng",$params)){
                            $lng = $params['lng'];
                        } else  {
                            $lng = "";
                        }
                        
                        if (array_key_exists("full_address",$params)){
                            $full_address = $params['full_address'];
                        } else  {
                            $full_address = "";
                        }
                        
                        if (array_key_exists("member_id",$params)){
                            $member_id = $params['member_id'];
                        } else  {
                            $member_id = "";
                        }
                        
                        if (array_key_exists("relation",$params)){
                            $relation_ship = $params['relation'];
                        } else  {
                            $relation_ship = "";
                        }
                        
                        
                        $resp = $this->LoginModel->address_add($user_id, $name, $mobile, $pincode, $address1, $address2, $landmark, $city, $state, $address_type,$lat,$lng,$full_address,$relation_ship,$member_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function address_update() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['address_id'] == "" || $params['name'] == "" || $params['address1'] == "" || $params['address2'] == "" || $params['mobile'] == "" || $params['city'] == "" || $params['state'] == "" || $params['pincode'] == "" || $params['address_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        /*$user_id = $params['user_id'];
                        $address_id = $params['address_id'];
                        $name = $params['name'];
                        $address1 = $params['address1'];
                        $address2 = $params['address2'];
                        $landmark = $params['landmark'];
                        $mobile = $params['mobile'];
                        $city = $params['city'];
                        $state = $params['state'];
                        $pincode = $params['pincode'];
                        $address_type = $params['address_type'];
                        $lat = $params['lat'];
                        $lng = $params['lng'];
                        $full_address = $params['full_address'];*/
                         $user_id = $params['user_id'];
                         $address_id = $params['address_id'];
                         
                        $name = $params['name'];
                        $mobile = $params['mobile'];
                        $pincode = $params['pincode'];
                        $address1 = $params['address1'];
                        
                        
                        $city = $params['city'];
                        $state = $params['state'];
                        
                        
                        
                        
                        
                        if (array_key_exists("address2",$params)){
                            $address2 = $params['address2'];
                        } else  {
                            $address2 = "";
                        }
                        
                        if (array_key_exists("landmark",$params)){
                            $landmark = $params['landmark'];
                        } else  {
                            $landmark = "";
                        }
                        
                        if (array_key_exists("address_type",$params)){
                            $address_type = $params['address_type'];
                        } else  {
                            $address_type = "";
                        }
                        
                        if (array_key_exists("lat",$params)){
                            $lat = $params['lat'];
                        } else  {
                            $lat = "";
                        }
                        
                        if (array_key_exists("lng",$params)){
                            $lng = $params['lng'];
                        } else  {
                            $lng = "";
                        }
                        
                        if (array_key_exists("full_address",$params)){
                            $full_address = $params['full_address'];
                        } else  {
                            $full_address = "";
                        }
                         if (array_key_exists("member_id",$params)){
                            $member_id = $params['member_id'];
                        } else  {
                            $member_id = "";
                        }
                        
                        if (array_key_exists("relation",$params)){
                            $relation_ship = $params['relation'];
                        } else  {
                            $relation_ship = "";
                        }
                        
                        $resp = $this->LoginModel->address_update($user_id, $address_id, $name, $address1, $address2, $landmark, $mobile, $city, $state, $pincode, $address_type,$lat,$lng,$full_address,$relation_ship,$member_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }



/***************************simple api**********************************/


    public function add_data() {
        $this->load->model('LoginModel');
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
                    if ($params['name'] == "" || $params['email'] == "" || $params['phone'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                         $name  =$params['name'];
                         $email =$params['email'];
                         $phone= $params['phone'];
                        
                        if (array_key_exists("name",$params)){
                            $name = $params['name'];
                        } else  {
                            $name = "";
                        }
                        
                        if (array_key_exists("email",$params)){
                            $email = $params['email'];
                        } else  {
                            $email = "";
                        }
                        
                        if (array_key_exists("phone",$params)){
                            $phone = $params['phone'];
                        } else  {
                            $phone = "";
                        }
                        
    $resp = $this->LoginModel->addr($name,$email,$phone);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }



    public function update_data() {
        $this->load->model('LoginModel');
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
                    if ($params['id'] == "" || $params['name'] == "" || $params['email'] == "" || $params['phone'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                         $id  =$params['id'];
                         $name  =$params['name'];
                         $email =$params['email'];
                         $phone= $params['phone'];
                         
                         
                         if (array_key_exists("id",$id)){
                            $id = $params['id'];
                        } else  {
                            $id = "";
                        }
                        
                        if (array_key_exists("name",$params)){
                            $name = $params['name'];
                        } else  {
                            $name = "";
                        }
                        
                        if (array_key_exists("email",$params)){
                            $email = $params['email'];
                        } else  {
                            $email = "";
                        }
                        
                        if (array_key_exists("phone",$params)){
                            $phone = $params['phone'];
                        } else  {
                            $phone = "";
                        }
                        
    $resp = $this->LoginModel->adddetails_update($id,$name,$email,$phone);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    

    public function demo_delete() {
        $this->load->model('LoginModel');
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
                    
                    if ($params['id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $id = $params['id'];
                        $resp = $this->LoginModel->add_delete($id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }








    public function data_list() {
        $this->load->model('LoginModel');
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
                /*    if ($params['id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {*/
                       // $id = $params['id'];
                        $resp = $this->LoginModel->simple_list();
                    //}
                    json_outputs($resp);
                }
            }
        }
    }



public function data_list_get_id() {
        $this->load->model('LoginModel');
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
                   if ($params['id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $id = $params['id'];
                        $resp = $this->LoginModel->simple_list_where_condition($id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

/************************************************************/




    public function address_delete() {
        $this->load->model('LoginModel');
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

                    if ($params['user_id'] == "" || $params['address_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $address_id = $params['address_id'];
                        $resp = $this->LoginModel->address_delete($user_id, $address_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function country() {
        $this->load->model('LoginModel');
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
                    $resp = $this->LoginModel->country();
                    json_outputs($resp);
                }
            }
        }
    }

    public function state() {
        $this->load->model('LoginModel');
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
                    if ($params['country_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $country_id = $params['country_id'];
                        $resp = $this->LoginModel->state($country_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function city() {
        $this->load->model('LoginModel');
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
                    if ($params['state_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $state_id = $params['state_id'];
                        $resp = $this->LoginModel->city($state_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function state_city() {
        $this->load->model('LoginModel');
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
                    $resp = $this->LoginModel->state_city();
                    json_outputs($resp);
                }
            }
        }
    }

    public function country_state_city() {
        $this->load->model('LoginModel');
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
                    $resp = $this->LoginModel->country_state_city();
                    json_outputs($resp);
                }
            }
        }
    }

    public function userupdate() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {

            $user_id = $_POST['user_id'];
            $gender = $_POST['gender'];
            $dob = $_POST['dob'];
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $email = $_POST['email'];
            if ($user_id == "" || $dob == "" || $gender == "" || $name == "" || $phone == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                if (isset($_FILES["profile_pic"]) AND ! empty($_FILES["profile_pic"]["name"])) {
                    //unlink images
                    $file_query = $this->db->query("SELECT users.id as user_id , users.avatar_id, media.id as media_id, media.source FROM `users` LEFT JOIN media ON users.avatar_id = media.id WHERE  users.id='$user_id'");
                    $get_file = $file_query->row();

                    if ($get_file) {
                        $profile_pic = $get_file->source;
                        $profile_pic_ = 'images/healthwall_avatar/' . $profile_pic;
                        @unlink(trim($profile_pic_));
                        $delete_from_s3 = DeleteFromToS3($profile_pic_);
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
                                $profile_pic = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/healthwall_avatar/' . $profile_pic;
                                $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                            }
                        }
                    }
                } else {
                    $profile_pic = '';
                }
                $resp = $this->LoginModel->userupdate($user_id, $gender, $dob, $name, $phone, $email, $profile_pic);
            }

            simple_json_output($resp);
        }
    }

    public function userupdate_web() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {

            $user_id = $_POST['user_id'];
            $gender = $_POST['gender'];
            $dob = $_POST['dob'];
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $email = $_POST['email'];
            $image = $_POST['profile_pic'];


            if ($user_id == "" || $dob == "" || $gender == "" || $name == "" || $phone == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {

                $resp = $this->LoginModel->userupdate($user_id, $gender, $dob, $name, $phone, $email, $image);
            }

            simple_json_output($resp);
        }
    }

    public function all_review_list() {
        $this->load->model('LoginModel');
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
                        $resp = $this->LoginModel->all_review_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function user_lat_lng() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['latitude'] == "" || $params['longitude'] == "" || $params['map_location'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $map_location = $params['map_location'];
                        $resp = $this->LoginModel->user_lat_lng($user_id, $latitude, $longitude, $map_location);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function delete_post_comment() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['type'] == "" || $params['comment_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $comment_id = $params['comment_id'];
                        $type = $params['type'];
                        $resp = $this->LoginModel->delete_post_comment($user_id, $post_id, $type, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_delete() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" && $params['post_id'] == "" && $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $type = $params['type'];
                        $resp = $this->LoginModel->review_delete($user_id, $post_id, $type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function app_banner() {
        $this->load->model('LoginModel');
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
                    $resp = $this->LoginModel->app_banner();
                    simple_json_output($resp);
                }
            }
        }
    }

    public function save_post() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['post_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $post_type = $params['post_type'];
                        $resp = $this->LoginModel->save_post($user_id, $post_id, $post_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function save_post_list() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['type'] == "" || $params['page'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $type = $params['type'];
                        $page = $params['page'];
                        $resp = $this->LoginModel->save_post_list($user_id, $type, $page);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function remove_save_post() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->LoginModel->remove_save_post($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function report_post() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['post_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $post_type = $params['post_type'];
                        $resp = $this->LoginModel->report_post($user_id, $post_id, $post_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function user_character() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $type = $params['type'];
                        $resp = $this->LoginModel->user_character($type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function common_user_update() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['user_name'] == "" || $params['user_image'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $user_name = $params['user_name'];
                        $user_image = $params['user_image'];

                        $resp = $this->LoginModel->common_user_update($user_id, $user_name, $user_image);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function common_user_check() {
        $this->load->model('LoginModel');
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
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];

                        $resp = $this->LoginModel->common_user_check($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function user_booking_details() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['listing_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_type = $params['listing_type'];
                      
                        if (array_key_exists('opd',$params)) {
                            $opd = $params['opd'];
                        } else {
                            $opd="";
                        }
                        
                        $resp = $this->LoginModel->user_booking_details($user_id, $listing_type, $opd);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function user_feedback() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['email'] == "" || $params['feed_back'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {

                        $user_id = $params['user_id'];
                        $email = $params['email'];
                        $feedback = $params['feed_back'];

                        $resp = $this->LoginModel->user_feedback($user_id, $email, $feedback);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function fetch_comment_reply_delete() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['comment_type'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $comment_type = $params['comment_type'];
                        $comment_id = $params['comment_id'];

                        $resp = $this->LoginModel->fetch_comment_reply_delete($comment_id, $comment_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function fetch_comment_reply_edit() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['comment'] == "" || $params['comment_type'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $comment = $params['comment'];
                        $comment_type = $params['comment_type'];
                        $comment_id = $params['comment_id'];

                        $resp = $this->LoginModel->fetch_comment_reply_edit($comment_id, $comment_type, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function fetch_comment_edit() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['comment'] == "" || $params['comment_type'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $comment = $params['comment'];
                        $comment_type = $params['comment_type'];
                        $comment_id = $params['comment_id'];

                        $resp = $this->LoginModel->fetch_comment_edit($comment_id, $comment_type, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function vendor_add_discount() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['vendor_id'] == "" || $params['discount_amount'] == "" || $params['discount_type'] == "" || $params['discount_limit'] == "" || $params['discount_category'] == "" || $params['discount_by'] == "" || $params['discount_exp'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $vendor_id = $params['vendor_id'];
                        $discount_amount = $params['discount_amount'];
                        $discount_type = $params['discount_type'];

                        $discount_limit = $params['discount_limit'];
                        $discount_category = $params['discount_category'];
                        $discount_by = $params['discount_by'];

                        $discount_exp = $params['discount_exp'];

                        $resp = $this->LoginModel->vendor_add_discount($vendor_id, $discount_amount, $discount_type, $discount_limit, $discount_category, $discount_by, $discount_exp);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function App_logout() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $response = $this->LoginModel->App_logout($user_id);
                json_output($response['status'], $response);
            }
        }
    }

    public function master_profile_views() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['vendor_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $vendor_type = $params['vendor_type'];
                        $resp = $this->LoginModel->master_profile_views($user_id, $listing_id, $vendor_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function all_booking_cancel() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['booking_id'] == "" || $params['status'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $booking_id = $params['booking_id'];
                        $status = $params['status'];
                        $resp = $this->LoginModel->all_booking_cancel($user_id, $listing_id, $booking_id, $status);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    //added by Jayesh for share pdf on Saturday
    public function share_profile() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['type'] == "" || $params['reason'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {


                        /* $listing_id = $params['listing_id'];
                          $vendor_type = $params['vendor_type'];
                          $mobile_num = $params['mobile'];
                          $name = $params['name'];
                          $email = $params['email'];
                          $dateofbirth = $params['dateofbirth'];
                          $gender = $params['gender'];
                          $blood_group = $params['blood_group'];
                          $merital_status = $params['merital_status'];
                          $height = $params['height'];
                          $weight = $params['weight'];
                          $bmi = $params['bmi'];
                          $medical_condition = $params['medical_condition'];
                          $allergies = $params['allergies'];
                          $hereditary_problems = $params['hereditary_problems'];
                          $addiction = $params['addiction'];
                          $diet_prefrence = $params['diet_prefrence'];
                          $Exercise_level = $params['Exercise_level'];
                          $health_insurance = $params['health_insurance'];
                          $organ_donor = $params['organ_donor']; */

                        $array_save = array();
                        $user_id = $params['user_id'];
                        $type = $params['type'];
                        $reason = $params['reason'];
                        array_push($array_save, array('user_id' => $params['user_id']));
                        $file_name = uniqid();
                        $listing_id = '';
                        $vendor_type = '';
                        if ($params['listing_id'] != '') {
                            $listing_id = $params['listing_id'];
                        }
                        if ($params['vendor_type'] != '') {
                            $vendor_type = $params['vendor_type'];
                        }
                        if ($params['mobile_num'] != '') {
                            $mobile_num = $params['mobile_num'];
                            array_push($array_save, array('Mobile Number ' => $mobile_num));
                        }
                        if ($params['name'] != '') {
                            $name = $params['name'];
                            array_push($array_save, array('Name' => $name));
                        }
                        if ($params['email'] != '') {
                            $email = $params['email'];
                            array_push($array_save, array('Email' => $email));
                        }
                        if ($params['dateofbirth'] != '') {
                            $dateofbirth = $params['dateofbirth'];
                            array_push($array_save, array('Date Of birth' => $dateofbirth));
                        }
                        if ($params['gender'] != '') {
                            $gender = $params['gender'];
                            array_push($array_save, array('Gender' => $gender));
                        }
                        if ($params['blood_group'] != '' && isset($params['blood_group'])) {
                            $blood_group = $params['blood_group'];
                            array_push($array_save, array('Blood Group' => $blood_group));
                        }
                        if ($params['merital_status'] != '') {
                            $merital_status = $params['merital_status'];
                            array_push($array_save, array('Merital Status' => $merital_status));
                        }
                        if ($params['height'] != '') {
                            $height = $params['height'];
                            array_push($array_save, array('Height' => $height));
                        }
                        if ($params['weight'] != '') {
                            $weight = $params['weight'];
                            array_push($array_save, array('Weight' => $weight));
                        }
                        if ($params['bmi'] != '') {
                            $bmi = $params['bmi'];
                            array_push($array_save, array('BMI' => $bmi));
                        }
                        if ($params['medical_condition'] != '') {
                            $medical_condition = $params['medical_condition'];
                            array_push($array_save, array('Medical Condition' => $medical_condition));
                        }
                        if ($params['allergies'] != '') {
                            $allergies = $params['allergies'];
                            array_push($array_save, array('Allergies' => $allergies));
                        }
                        if ($params['hereditary_problems'] != '') {
                            $hereditary_problems = $params['hereditary_problems'];
                            array_push($array_save, array('Hereditary Problems' => $hereditary_problems));
                        }
                        if ($params['addiction'] != '') {
                            $addiction = $params['addiction'];
                            array_push($array_save, array('Addiction' => $addiction));
                        }
                        if ($params['diet_prefrence'] != '') {
                            $diet_prefrence = $params['diet_prefrence'];
                            array_push($array_save, array('Diet Prefrence' => $diet_prefrence));
                        }
                        if ($params['Exercise_level'] != '') {
                            $Exercise_level = $params['Exercise_level'];
                            array_push($array_save, array('Exercise Level' => $Exercise_level));
                        }
                        if ($params['health_insurance'] != '') {
                            $health_insurance = $params['health_insurance'];
                            array_push($array_save, array('Health Insurance' => $health_insurance));
                        }
                        if ($params['organ_donor'] != '') {
                            $organ_donor = $params['organ_donor'];
                            array_push($array_save, array('Organ Donor' => $organ_donor));
                        }

                        /* $user_data['users'] = array(
                          'user_id' => $user_id,
                          'listing_id' => $listing_id,
                          'vendor_type' => $vendor_type,
                          'mobile_num' => $mobile_num,
                          'name' => $params['name'],
                          'email' => $params['email'],
                          'dateofbirth' => $params['dateofbirth'],
                          'gender' => $params['gender'],
                          'blood_group' => $params['blood_group'],
                          'merital_status' => $params['merital_status'],
                          'height' => $params['height'],
                          'weight' => $params['weight'],
                          'bmi' => $params['bmi'],
                          'medical_condition' => $params['medical_condition'],
                          'allergies' => $params['allergies'],
                          'hereditary_problems' => $params['hereditary_problems'],
                          'addiction' => $params['addiction'],
                          'diet_prefrence' => $params['diet_prefrence'],
                          'Exercise_level' => $params['Exercise_level'],
                          'health_insurance' => $params['health_insurance'],
                          'organ_donor' => $params['organ_donor'] ); */

                        $user_data['users'] = $array_save;
                        $user_data['pdf_file_name'] = $file_name;

                        $user_data['pdfpass'] = mt_rand(100000, 999999); 

                        $this->load->library('Pdf');
                        $this->load->view('mpdf', $user_data);

                        $resp = $this->LoginModel->save_pdf_file($file_name, $user_id, $listing_id, $vendor_type, $type, $reason,$user_data['pdfpass']);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function recent_vendor_list() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['vendor_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $vendor_type = $params['vendor_type'];
                        $resp = $this->LoginModel->recent_vendor_list($user_id, $vendor_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function question_list() {
        $this->load->model('LoginModel');
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
                        $resp = $this->LoginModel->question_list($user_id);
                    }
                    json_outputs($resp); 
                }
            }
        }
    }
    
    
        public function faq_question_list() {
        $this->load->model('LoginModel');
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
                        $resp = $this->LoginModel->faq_question_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    

    //added by zak for shared document list for profile , prescription , healthrecords, reports 

    public function Shared_documentlist() {
        $this->load->model('LoginModel');
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
                        $resp = $this->LoginModel->Shared_documentlist($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function share_prescription() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['vendor_type'] == "" || $params['file_name'] == "" || $params['type'] == "" || $params['reason'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $vendor_type = $params['vendor_type'];
                        $file_name = $params['file_name'];
                        $type = $params['type'];
                        $reason = $params['reason'];

                        $resp = $this->LoginModel->share_prescription($user_id, $listing_id, $vendor_type, $file_name, $type, $reason);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function support_feedback() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['feedback'] == "" || $params['ratings'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {

                        $user_id = $params['user_id'];
                        $listing = $params['listing_id'];
                        $feedback = $params['feedback'];
                        $ratings = $params['ratings'];
                        $service = $params['service'];
                        $resp = $this->LoginModel->support_feedback($user_id, $listing, $feedback, $ratings, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    //added by zak for card offer 

    public function priviladge_offer_list() {
        $this->load->model('PharmacyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    //if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "" || $params['page'] == "") 
                    if ($params['lat'] == "" || $params['lng'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $lat = $params['lat'];
                        $lng = $params['lng'];
                        $category_id = $params['category_id'];
                        $type = $params['type'];
                        $page = $params['page'];
                        $resp = $this->LoginModel->priviladge_offer_list($user_id, $lat, $lng, $category_id, $type, $page);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    // added by vishal & Dhaval card Offer for ios
    public function priviladge_offer_list_ios() {
        $this->load->model('PharmacyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    //if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "" || $params['page'] == "") 
                    if ($params['lat'] == "" || $params['lng'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $lat = $params['lat'];
                        $lng = $params['lng'];
                        $category_id = $params['category_id'];
                        $type = $params['type'];
                        $page = $params['page'];
                        $resp = $this->LoginModel->priviladge_offer_list_ios($user_id, $lat, $lng, $category_id, $type, $page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    //added by zak for video how to use
    public function How_to_use() {
        $this->load->model('LoginModel');
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
                    // if ($params['user_id'] == "") {
                    //     $resp = array(
                    //         'status' => 400,
                    //         'message' => 'please enter fields'
                    //     );
                    // } else {
                    $user_id = $params['user_id'];
                    $resp = $this->LoginModel->How_to_use($user_id);
                    //  }
                    json_outputs($resp);
                }
            }
        }
    }

    //added by dinesh for change password 

    public function change_password_web() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    //if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "" || $params['page'] == "") 
                    if ($params['user_id'] == "" || $params['pervious_pwd'] == "" || $params['new_pwd'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $pervious_pwd = $params['pervious_pwd'];
                        $new_pwd = $params['new_pwd'];

                        $resp = $this->LoginModel->change_password_web($user_id, $pervious_pwd, $new_pwd);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function sendotp_web() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "") {
                    $resp = array('status' => 400, 'message' => 'please enter fields');
                } else {
                    $phone = $params['phone'];
                    $response = $this->LoginModel->sendotp_web($phone);
                }
                otp_json_output($response);
            }
        }
    }

    public function confirm_otp() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "" || $params['otp'] == "") {
                    $resp = array('status' => 400, 'message' => 'please enter fields');
                } else {
                    $phone = $params['phone'];
                    $otp = $params['otp'];
                    $response = $this->LoginModel->confirm_otp($phone, $otp);
                }
                otp_json_output($response);
            }
        }
    }

    public function forgot_password() {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    //if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "" || $params['page'] == "") 
                    if ($params['phone'] == "" || $params['confirm_pwd'] == "" || $params['new_pwd'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $phone = $params['phone'];
                        $confirm_pwd = $params['confirm_pwd'];
                        $new_pwd = $params['new_pwd'];

                        $resp = $this->LoginModel->forgot_password($phone, $new_pwd, $confirm_pwd);
                    }
                    otp_json_output($resp);
                }
            }
        }
    }
    
    
    
    public function ivr_details() {
        $this->load->model('LoginModel');
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
                    if ($params['number'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $number = $params['number'];
                        $resp = $this->LoginModel->ivr_details($number);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
     public function coupon_code() {
        $this->load->model('LoginModel');
       
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
                    if ($params['user_id'] == "" || $params['coupon'] == "" || $params['listing_id'] == "" || $params['listing_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                     
                    } else {
                        $user_id = $params['user_id'];
                        $coupon = $params['coupon'];
                        $listing_id = $params['listing_id'];
                        $listing_type = $params['listing_type'];
                        
                        if(array_key_exists('amount', $params)){
                            $amount = $params['amount'];
                             
                        } else {
                            $amount = 0;
                        }
                         if(array_key_exists('card_type', $params)){
                            $card_type = $params['card_type'];
                             
                        } else {
                            $card_type = 0;
                        }
                        
                        $resp = $this->LoginModel->coupon_code($user_id,$coupon,$listing_id,$listing_type,$amount,$card_type);
                    }
                  simple_json_output($resp);
                }
            }
        }
    }
    
    
    public function privilage_card_detail() {
        $this->load->model('LoginModel');
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
                        $resp = $this->LoginModel->privilage_card_detail($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
   public function Profile_menu() {
        $this->load->model('LoginModel');
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
                    if ($params['user_id'] == "" ||  $params['listing_type']=="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_type = $params['listing_type'];
                      
                        $resp = $this->LoginModel->Profile_menu($user_id,$listing_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function get_coupon() {
        $this->load->model('LoginModel');
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
                    if ($params['listing_id'] == "" ||  $params['listing_type']=="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['listing_id'];
                        $listing_type = $params['listing_type'];
                      
                        $resp = $this->LoginModel->get_coupon($user_id,$listing_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
	
	 public function remove_user_profile() {
        $this->load->model('LoginModel');
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
                        $patient_id = $params['patient_id'];
                        $resp = $this->LoginModel->remove_user_profile($user_id, $patient_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    

}
