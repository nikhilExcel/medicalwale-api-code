<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function login()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params   = json_decode(file_get_contents('php://input'), TRUE);
                $username = $params['username'];
                $password = $params['password'];                
                $response = $this->LoginModel->login($username, $password);
                json_output($response['status'], $response);
            }
        }
    }
    
    public function logout()
    {
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
    
    public function sendotp()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params   = json_decode(file_get_contents('php://input'), TRUE);
                $phone    = $params['phone'];
                $response = $this->LoginModel->sendotp($phone);
                otp_json_output($response);
            }
        }
    }
    
    public function sales_login()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params   = json_decode(file_get_contents('php://input'), TRUE);
                $username = $params['username'];
                $password = $params['password'];                
                $response = $this->LoginModel->sales_login($username, $password);
                simple_json_output($response);
            }
        }
    }
    
    public function userdetails()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params     = json_decode(file_get_contents('php://input'), TRUE);
                $listing_id = $params['listing_id'];
                $user_id    = $params['user_id'];
                $response   = $this->LoginModel->userdetails($listing_id, $user_id);
                userlogin_json_output($response);
            }
        }
    }
    
    public function userlogin()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_user = $this->LoginModel->check_auth_client();
			if($check_auth_user == true){
				$params = json_decode(file_get_contents('php://input'), TRUE);
		        $phone = $params['phone'];
				$token = $params['token'];
				$agent = $params['agent'];
		        $response = $this->LoginModel->userlogin($phone,$token,$agent);
				userlogin_json_output($response); 
			}
		}
	}
    
    public function usersignup()
    {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $name     = $this->input->post('name');
            $phone    = $this->input->post('phone');
            $email    = $this->input->post('email');
            $gender   = $this->input->post('gender');
            $dob      = $this->input->post('dob');
            $password = $this->input->post('password');
            $token    = $this->input->post('token');
            $agent    = $this->input->post('agent');
            
            if ($name == "" || $phone == "" || $email == "" || $gender == "" || $password == "") {
                $resp       = array(
                    'status' => 400,
                    'message' => 'failure'
                );
            } else {
                if (isset($_FILES["image"]) AND !empty($_FILES["image"]["name"])) {                    
                    $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                    include('s3_config.php');                    
                    $img_name = $_FILES['image']['name'];
                    $img_size = $_FILES['image']['size'];
                    $img_tmp  = $_FILES['image']['tmp_name'];
                    $ext      = getExtension($img_name);
                    if (strlen($img_name) > 0) {
                        if ($img_size < (50000 * 50000)) {
                            if (in_array($ext, $img_format)) {
                                $image             = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/healthwall_avatar/' . $image;
                                $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);                                
                            }
                        }
                    }
                } else {
                    
                    $image = '';
                }     
                $resp = $this->LoginModel->usersignup($name, $phone, $email, $gender, $dob, $password, $image, $token, $agent);
            }
            simple_json_output($resp);
        }
    }
    
    public function userprofile()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response   = $this->LoginModel->auth();
                $respStatus = $response['status'];
                if ($response['status'] == 200) {
                    $params           = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id          = $params['user_id'];
                    $bmi              = $params['bmi'];
                    $activity_level   = $params['activity_level'];
                    $marital_status   = $params['marital_status'];
                    $diet_fitness     = $params['diet_fitness'];
                    $height           = $params['height'];
                    $weight           = $params['weight'];
                    $organ_donor      = $params['organ_donor'];
                    $blood_group      = $params['blood_group'];
                    $exercise_level   = $params['exercise_level'];
                    $health_condition = $params['health_condition'];
                    $allergies        = $params['allergies'];
                    $marital_status   = $params['marital_status'];
                    $ask_saheli       = $params['ask_saheli'];  
                    $addiction       = $params['addiction'];
                    $health_insurance       = $params['health_insurance']; 
                    $blood_is_active       = $params['blood_is_active'];
                    $heradiatry_problem       = $params['heradiatry_problem'];
                    
                    $lat       = $params['lat'];
                    $lng       = $params['lng'];
                    $map_location       = $params['map_location'];
                    
                    if ($params['user_id'] == "") {
                        $respStatus = 400;
                        $resp       = array(
                            'status' => 400,
                            'message' => 'please enter all field'
                        );
                    } else {
                        $respStatus = 200;
                        $resp = $this->LoginModel->userprofile($user_id,$marital_status, $blood_group, $height, $weight, $diet_fitness, $exercise_level, $health_condition, $allergies, $ask_saheli, $organ_donor, $bmi, $activity_level, $addiction, $health_insurance,$blood_is_active,$heradiatry_problem,$lat,$lng,$map_location);
                    }
                    json_output($respStatus, $resp);
                }
            }
        }
    }
    
    
     public function blood_group_update()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response   = $this->LoginModel->auth();
                $respStatus = $response['status'];
                if ($response['status'] == 200) {
                    $params           = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id          = $params['user_id'];
                   
                    $blood_group      = $params['blood_group'];
                    $blood_is_active       = $params['blood_is_active'];
                    if ($params['user_id'] == "") {
                        $respStatus = 400;
                        $resp       = array(
                            'status' => 400,
                            'message' => 'please enter all field'
                        );
                    } else {
                        $respStatus = 200;
                        $resp = $this->LoginModel->blood_group_update($user_id, $blood_group,$blood_is_active);
                    }
                    json_output($respStatus, $resp);
                }
            }
        }
    }
    
    
    
    public function follow()
    {
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
                        $user_id      = $params['user_id'];
                        $following_id = $params['following_id'];
                        $resp         = $this->LoginModel->follow($user_id, $following_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function following_list()
    {
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
    
    public function follower_list()
    {
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
    
    public function address_list()
    {
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
                        $resp    = $this->LoginModel->address_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function address_add()
    {
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
                    if ($params['user_id'] == "" || $params['name'] == "" || $params['mobile'] == "" || $params['pincode'] == "" || $params['address1'] == "" || $params['city'] == "" || $params['state'] == "" || $params['address_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id  = $params['user_id'];
                        $name     = $params['name'];
                        $mobile   = $params['mobile'];
                        $pincode  = $params['pincode'];
                        $address1 = $params['address1'];
                        $address2 = $params['address2'];
                        $landmark = $params['landmark'];
                        $city     = $params['city'];
                        $state    = $params['state'];
                        $address_type= $params['address_type'];
                        
                        
                        $resp     = $this->LoginModel->address_add($user_id, $name, $mobile, $pincode, $address1, $address2, $landmark, $city, $state,$address_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function address_update()
    {
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
                    if ($params['user_id'] == "" || $params['address_id'] == "" || $params['name'] == "" || $params['address1'] == "" || $params['address2'] == "" || $params['landmark'] == "" || $params['mobile'] == "" || $params['city'] == "" || $params['state'] == "" || $params['pincode'] == "" || $params['address_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $address_id = $params['address_id'];
                        $name       = $params['name'];
                        $address1   = $params['address1'];
                        $address2   = $params['address2'];
                        $landmark   = $params['landmark'];
                        $mobile     = $params['mobile'];
                        $city       = $params['city'];
                        $state      = $params['state'];
                        $pincode    = $params['pincode']; 
                        $address_type= $params['address_type'];
                        $resp       = $this->LoginModel->address_update($user_id, $address_id, $name, $address1, $address2, $landmark, $mobile, $city, $state, $pincode,$address_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function address_delete()
    {
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
                        
                        $user_id    = $params['user_id'];
                        $address_id = $params['address_id'];
                        $resp       = $this->LoginModel->address_delete($user_id, $address_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function country()
    {
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
    
    public function state()
    {
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
                        $resp       = $this->LoginModel->state($country_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function city()
    {
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
                        $resp     = $this->LoginModel->city($state_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function state_city()
    {
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
    
    
    public function country_state_city()
    {
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
    
    
    public function userupdate()
    {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            
            $user_id = $_POST['user_id'];
            $gender  = $_POST['gender'];
            $dob     = $_POST['dob'];
            $name    = $_POST['name'];
            $phone   = $_POST['phone'];
            $email   = $_POST['email'];
            
            
            if ($user_id == "" || $dob == "" || $gender == "" || $name == "" || $phone == "" || $email == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {                
                if (isset($_FILES["profile_pic"]) AND !empty($_FILES["profile_pic"]["name"])) {                    
                    //unlink images
                    $file_query = $this->db->query("SELECT users.id as user_id , users.avatar_id, media.id as media_id, media.source FROM `users` LEFT JOIN media ON users.avatar_id = media.id WHERE  users.id='$user_id'");
                    $get_file   = $file_query->row();
                    
                    if ($get_file) {
                        $profile_pic  = $get_file->source;
                        $profile_pic_ = 'images/healthwall_avatar/' . $profile_pic;
                        @unlink(trim($profile_pic_));
                        $delete_from_s3 = DeleteFromToS3($profile_pic_);
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
                                $profile_pic       = uniqid() . date("YmdHis") . "." . $ext;
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
    
    public function all_review_list()
    {
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
                        $resp    = $this->LoginModel->all_review_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function user_lat_lng()
    {
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
                        $user_id   = $params['user_id'];
                        $latitude  = $params['latitude'];
                        $longitude = $params['longitude'];
                        $map_location  = $params['map_location'];
                        $resp      = $this->LoginModel->user_lat_lng($user_id, $latitude, $longitude, $map_location);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function review_delete()
    {
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
                        $type    = $params['type'];
                        $resp    = $this->LoginModel->review_delete($user_id, $post_id, $type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function app_banner()
    {
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
}
