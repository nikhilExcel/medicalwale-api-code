<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function request_inventory_access()
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
                $params   = json_decode(file_get_contents('php://input'), TRUE);
                $user_id  = $params['user_id'];
                $party_code  = $params['party_code'];
                $distributor_id  = $params['distributor_id'];
                if ($user_id != '') {
                    $response = $this->LoginModel->request_inventory_access($user_id,$party_code,$distributor_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array( 
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function validate_admin()
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
                $params   = json_decode(file_get_contents('php://input'), TRUE);
                $token    = $params['token'];
                $type    = $params['type'];
                if ($token != '') {
                    $response = $this->LoginModel->validate_admin($token,$type);
                    simple_json_output($response);
                } else {
                    json_output(400, array( 
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    
    public function web_token()
    {
        $this->load->model('LoginModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $token    = $_POST['token'];
            $user_id   = $_POST['user_id'];
            if ($token != '') {
                $response = $this->LoginModel->web_token($token,$user_id);
                simple_json_output($response);
            } else {
                json_output(400, array( 
                    'status' => 400,
                    'message' => 'Enter Username or Password'
                ));
            }
        }
    }
      
   
   
   
    public function login()
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
                $params   = json_decode(file_get_contents('php://input'), TRUE);
                $email    = $params['email'];
                $password = $params['password'];
                if ($email != '' && $password != '') {
                    $response = $this->LoginModel->login($email, $password);
                    simple_json_output($response);
                } else {
                    json_output(400, array( 
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
      
    public function register()
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
                $params   = json_decode(file_get_contents('php://input'), TRUE);
                $firm_name    = $params['firm_name'];
                $name    = $params['name'];
                $email    = $params['email']; 
                $phone = $params['phone'];
                $password = $params['password'];
                $confirm_password = $params['confirm_password'];
                
                
                
        if($name=='' && $email=='' && $phone=='' && $password=='' && $confirm_password==''){
          json_output(400, array( 'status' => 400, 'message' => 'Enter all fields!.'));
        }     
       elseif($password!=$confirm_password){
          json_output(400, array( 'status' => 400, 'message' => 'Error! Password Mismatch.'));
        }
        

        //is email unique
		elseif (!$this->LoginModel->is_unique_email($email)) {
		  json_output(400, array( 'status' => 400, 'message' => 'Error! Email Id Already Exist.'));
		}
			
        //is mobile unique
		elseif (!$this->LoginModel->is_unique_mobile($phone)) {
		  json_output(400, array( 'status' => 400, 'message' => 'Error! Mobile No. Already Exist.'));
		}
       else {
           $response = $this->LoginModel->register_vendor($name,$email,$phone, $password,$firm_name);
           simple_json_output($response);
        }
       }
     }
    }
   
   
     
   
    public function register_otp_verify()
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
                $params   = json_decode(file_get_contents('php://input'), TRUE);
                $user_id    = $params['user_id'];
                $otp    = $params['otp'];
                
                if ($user_id != '' && $otp != '') {
                    $response = $this->LoginModel->register_otp_verify($otp,$user_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter OTP!'
                    ));
                }
            }
        }
    }
    
    
     
   
    public function register_resend_otp()
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
                $params   = json_decode(file_get_contents('php://input'), TRUE);
                $user_id    = $params['user_id'];
               
                if ($user_id != '') {
                    $response = $this->LoginModel->register_resend_otp($user_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'error! Enter ocurred while sending OTP!'
                    ));
                }
            }
        }
    }
    
    
    
    public function module_list()
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
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id =3;  
                $school_id =1;
                $profile = 18;  
                
                if ($user_id != '' && $school_id != '') {
                    $response = $this->LoginModel->module_list($user_id,$school_id,$profile);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function get_uniform_size_chart()
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
                $params    = json_decode(file_get_contents('php://input'), TRUE);
                $user_id   = $params['user_id'];
                $category  = $params['category'];
                $per_page  = $params['per_page'];
                $offset  = $params['offset'];
                
                if ($user_id != '' && $category!='') {
                    $response = $this->auth_model->get_uniform_size_chart($user_id,$category,$per_page,$offset);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter required fields'
                    ));
                }
            }
        }
    } 
    
    public function size_chart_list()
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
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id   = $params['user_id'];
                $category_id  = $params['category_id']; 
                
                if ($user_id != '' && $category_id != '') {
                    $response = $this->auth_model->size_chart_list($user_id,$category_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function delete_uniform_size_chart()
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
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id   = $params['user_id'];
                $size_id  = $params['size_id']; 
                if ($user_id != '' && $size_id != '') {
                    $response = $this->auth_model->delete_uniform_size_chart($user_id,$size_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function add_size_chart()
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
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id   = $params['user_id'];
                $category_id  = $params['category_id']; 
                $size_id  = $params['size_id']; 
                $name  = $params['name']; 
                $value  = $params['value']; 
                $parent_id  = $params['parent_id']; 
                $count = $this->auth_model->count_size_chart($size_id);
                if($count > 0){
                    $delete = $this->auth_model->delete_size_chart($user_id,$size_id);
                }
                
                if ($user_id != '' && $size_id != '') {
                    $response = $this->auth_model->add_size_chart($user_id,$size_id,$name,$value,$category_id,$parent_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
  
    
    
}