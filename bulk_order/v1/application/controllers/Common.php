<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends CI_Controller
{
    public function send_notification_to_delivery()
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
                $title = $params['title'];
                $msg = $params['msg'];
                $token = $params['token'];
                $agent = $params['agent'];
                
                if ($title != '') {
                    $response = $this->common_model->send_notification_to_delivery($title, $token, $msg, $agent);
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
    
    public function state_list()
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
                $user_id = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->state_list($user_id);
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
    
    
    
    public function city_list()
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
                $user_id  = $params['user_id'];
                $state_id = $params['state_id'];
                
                if ($user_id != '' && $state_id != '') {
                    $response = $this->common_model->city_list($user_id, $state_id);
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
    
    public function banks_list()
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
                $user_id  = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->banks_list($user_id);
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
    
    public function all_school_list()
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
                $user_id = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->all_school_list($user_id);
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
    
    
    public function get_selected_school()
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
                $user_id = $params['user_id'];
                $id = $params['id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->get_selected_school($user_id,$id);
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
    
    
    
    public function publisher_list()
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
                $user_id  = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->publisher_list($user_id);
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
       
       
    
    public function board_list()
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
                $user_id = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->board_list($user_id);
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
    
    
    public function grades_list()
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
                $user_id = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->grades_list($user_id);
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
    
    
    public function brand_list()
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
                $user_id = $params['user_id'];
                $category_id = '6';
                
                if ($user_id != '') {
                    $response = $this->common_model->brand_list($category_id);
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
    
    public function notebook_brand_list()
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
                $user_id = $params['user_id'];
                $category_id ='10';
                
                if ($user_id != '') {
                    $response = $this->common_model->brand_list($category_id);
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
    
   
    public function stationary_list(){
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
                $user_id = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->stationary_list($user_id);
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
    
    public function stationary_type_list(){
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
                $user_id = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->stationary_type_list($user_id);
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
    
   
    public function subject_list(){
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
                $user_id = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->subject_list($user_id);
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
        
      
 
    public function category_by_parent(){
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
                $user_id = $params['user_id'];
                $parent_id = $params['parent_id'];
                
                if ($user_id != '' && $parent_id!='') {
                    $response = $this->common_model->category_by_parent($parent_id);
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
    
    
    
    public function vendor_shipping_list(){
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
                $user_id = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->vendor_shipping_list($user_id);
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
    
    
    
    //uniform
    public function size_list_by_category()
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
                $user_id = $params['user_id'];
                $category_id =$params['category_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->size_list($category_id);
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
    
       
    
       
    public function vendor_school_list()
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
                $user_id  = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->vendor_school_list($user_id);
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
    
    public function uniform_school_list()
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
                $user_id  = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->uniform_school_list($user_id);
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
    
    public function bookset_school_list()
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
                $user_id  = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->bookset_school_list($user_id);
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
    
      
    
     
    public function notebook_size_list()
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
                $user_id  = $params['user_id']; 
                $category_id ='10';
                
                if ($user_id != '') {
                    $response = $this->common_model->size_list($category_id);
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
    
       
    public function binding_type_list()
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
                $user_id  = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->binding_type_list($user_id);
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
    
    
     public function stationery_color_list()
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
                $user_id  = $params['user_id'];
                $category_id  = '6';
                
                if ($user_id != '') {
                    $response = $this->common_model->color_list($category_id);
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
      
     public function country_list()
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
                $user_id  = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->country_list($user_id);
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
    
    
     public function shoes_brand_list()
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
                $user_id = $params['user_id'];
                $category_id ='38';
                
                if ($user_id != '') {
                    $response = $this->common_model->brand_list($category_id);
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
    
    public function shoes_color_list()
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
                $user_id = $params['user_id'];
                $category_id ='38';
                
                if ($user_id != '') {
                    $response = $this->common_model->color_list($category_id);
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
      public function shoes_size_list()
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
                $user_id = $params['user_id'];
                $type = $params['type'];
                $category_id ='38';
                
                if ($user_id != '') {
                    $response = $this->common_model->uniform_size_list($category_id,$type);
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
    
    public function uniform_color_list()
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
                $user_id = $params['user_id'];
                $category_id ='22';
                
                if ($user_id != '') {
                    $response = $this->common_model->color_list($category_id);
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
     
     public function get_school_by_vendor()
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
                $user_id  = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->get_school_by_vendor($user_id);
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
    
        
     public function get_board_by_school()
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
                $user_id  = $params['user_id'];
                $school_id  = $params['school_id'];
                
                if ($school_id != '') {
                    $response = $this->common_model->get_board_by_school($school_id);
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
     
    public function get_categories()
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
                $user_id  = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->get_categories($user_id);
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
    
    public function dashboard()
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
                $user_id  = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->dashboard($user_id);
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