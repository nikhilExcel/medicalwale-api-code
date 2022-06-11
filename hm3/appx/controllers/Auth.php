<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    public function __construct($config  = 'rest')
    {
        
      
         parent::__construct($config);
        
       $this->load->model('Login_Model');
    }
    public function login() {
         
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
           
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $username = $params['username'];
                $password = $params['password'];
                $response = $this->Login_Model->login($username, $password);
                // json_output($response['status'], $response);
                
                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
                
            }
        }
    }

    public function logout() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
           $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
             return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->logout();
                // json_output($response['status'], $response);
                 return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }

    public function appversion() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'GET') {
            $response =  array(
                'status' => 400,
                'message' => 'Bad request.'
            );
              return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_user = $this->Login_Model->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $response = $this->Login_Model->appversion();
                // simple_json_output($response);
                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }

    public function sendotp() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
             return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_user = $this->Login_Model->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $phone = $params['phone'];
                $response = $this->Login_Model->sendotp($phone);
                // otp_json_output($response);
                 return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }

    public function userdetails() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_user = $this->Login_Model->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $listing_id = $params['listing_id'];
                $user_id = $params['user_id'];
                $response = $this->Login_Model->userdetails($listing_id, $user_id);
                // userlogin_json_output($response);
                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }

    public function userlogin() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
             return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_user = $this->Login_Model->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $phone = $params['phone'];
                $token = $params['token'];
                $agent = $params['agent'];
                $response = $this->Login_Model->userlogin($phone, $token, $agent);
                // userlogin_json_output($response);
                 return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }
    
    public function checklogin() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request method should be POST.'
            );
             return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_user = $this->Login_Model->check_auth_client();
            if ($check_auth_user == true) {
                $response = $this->Login_Model->checklogin();
                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }

    public function user_login() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_user = $this->Login_Model->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $phone = $params['phone'];
                $token = $params['token'];
                $agent = $params['agent'];
                $country_code = $params['country_code'];
                $response = $this->Login_Model->user_login($country_code, $phone, $token, $agent);
                // simple_json_output($response);
            
                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }
    
    public function otp_verify() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
             return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_user = $this->Login_Model->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $phone = $params['phone'];
                $otp = $params['otp'];
                $response = $this->Login_Model->otp_verify($phone, $otp);
                // simple_json_output($response);
                 return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }

    public function activate_privilage() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
           $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_user = $this->Login_Model->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $card_no = $params['card_no'];
                $response = $this->Login_Model->activate_privilage($user_id, $card_no);
                // simple_json_output($response);
                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }

    public function generate_privilage() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_user = $this->Login_Model->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $response = $this->Login_Model->generate_privilage($user_id);
                // simple_json_output($response);
                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }

    public function activate_privilage_with_Coupon() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_user = $this->Login_Model->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $card_no = $params['card_no'];
                $coupon_code = $params['coupon_code'];
                $response = $this->Login_Model->activate_privilage_with_Coupon($user_id, $card_no, $coupon_code);
                // simple_json_output($response);
            
                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }

    public function check_privilage_card_with_Coupon() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_user = $this->Login_Model->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $card_no = $params['card_no'];
                $response = $this->Login_Model->check_privilage_card_with_Coupon($user_id, $card_no);
                // simple_json_output($response);
                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }

    public function generate_coupon_code_for_card() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_user = $this->Login_Model->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $card_no = $params['card_no'];
                $response = $this->Login_Model->generate_coupon_code_for_card($user_id, $card_no);
                // simple_json_output($response);
                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }

    public function verify_generated_coupon($user_id, $card_no, $coupon_code) {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_user = $this->Login_Model->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $card_no = $params['card_no'];
                $coupon_code = $params['coupon_code'];
                $response = $this->Login_Model->verify_generated_coupon($user_id, $card_no, $coupon_code);
                // simple_json_output($response);
                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
            }
        }
    }

    public function usersignup() {
        $this->load->model('Login_Model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $name = $this->input->post('name');
            $phone = $this->input->post('phone');
            $gender = $this->input->post('gender');
            $dob = $this->input->post('dob');
            $token = $this->input->post('token');
            $agent = $this->input->post('agent');

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
                $resp = $this->Login_Model->usersignup($name, $phone, $gender, $dob, $image, $token, $agent);
            }
            // simple_json_output($resp);
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        }
    }

    public function userprofile() {
        $this->load->model('Login_Model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
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
            $data['addiction_value'] = $this->input->post('addiction_value');
            $data['lat'] = $this->input->post('lat');
            $data['lng'] = $this->input->post('lng');
            $data['map_location'] = $this->input->post('map_location');
            $data['height_cm_ft'] = $this->input->post('height_cm_ft');
            $genetic_disorder = $this->input->post('medical_genetic_disorder');
            if ($user_id == "") {
                 $resp = array(
                    // 'status' => 400,
                    'message' => 'please enter all field'
                );
                $resp['status'] = 400;
               
            } else {
                
                $resp = $this->Login_Model->userprofile($user_id,$data,$genetic_disorder);
                $resp['status'] = 200;
            }
            // json_output($respStatus, $resp);
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        }
    }

    public function userupdate() {
        $this->load->model('Login_Model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
             return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {

            $user_id = $_POST['user_id'];
            $gender = $_POST['gender'];
            $dob = $_POST['dob'];
            $name = $_POST['name'];
            $phone = $_POST['phone'];
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
                $resp = $this->Login_Model->userupdate($user_id, $gender, $dob, $name, $phone, $email, $profile_pic);
            }

            // simple_json_output($resp);
             return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        }
    }

    public function user_lat_lng() {
        $this->load->model('Login_Model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $response = array(
                'status' => 400,
                'message' => 'Bad request.'
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
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
                        $resp = $this->Login_Model->user_lat_lng($user_id, $latitude, $longitude, $map_location);
                    }
                    // simple_json_output($resp);
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
                }
            }
        }
    }



}
