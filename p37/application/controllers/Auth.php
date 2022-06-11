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
    
    
    public function all_doctorprofile_details() {
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
                    if ($params['user_id'] == "" || $params['listing_id'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                      

                        $resp = $this->LoginModel->all_doctorprofile_details($user_id,$listing_id);
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
                       
                        $resp = $this->LoginModel->remove_user_profile($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    
    
}
