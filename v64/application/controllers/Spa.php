<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Spa extends CI_Controller {

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
    
    public function spa_search() {
        $this->load->model('SpaModel');
        $this->load->library('ElasticSearch');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $keyword = $params['keyword'];
                          $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $type=$params['type'];
                        $resp = $this->SpaModel->spa_center_list_v2_search($user_id,$keyword,$latitude,$longitude,$type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function spa_search_all_list() {
        $this->load->model('SpaModel');
        $this->load->library('ElasticSearch');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $keyword = $params['keyword'];
                          $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        
                        $resp = $this->SpaModel->spa_search_all_list($user_id,$keyword,$latitude,$longitude);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function spa_package_list_categorywise() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $tab_id = $params['tab_id'];         // 0-center O 1-home
                        $sub_category = $params['sub_category'];  //category_id
                        $main_category = $params['main_category'];
                        $sort = $params['sort'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $resp = $this->SpaModel->spa_package_list_categorywise($user_id,$main_category,$sub_category,$tab_id,$sort,$latitude,$longitude);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function spa_therapies_list() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $resp = $this->SpaModel->spa_therapies_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function spa_common_therapies_list() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $resp = $this->SpaModel->spa_common_therapies_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function spa_center_list_v2() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        if(array_key_exists("page",$params)){
                            $page = $params['page'];
                        } else {
                            $page = "";
                        }
                        
                        
                        $tab_id = $params['tab_id'];         // 0-center O 1-home
                        $cat_type = ""; 
                        $main_category = "";   //foot spa
                        $sub_category = "";   //category_id
                        
                        $sort = $params['sort'];
                        
                        $resp = $this->SpaModel->spa_center_list_v2($user_id,$latitude, $longitude, $tab_id, $cat_type, $main_category, $sub_category, $sort, $page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function spa_center_select_order() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        
                        $tab_id = $params['tab_id'];         // 0-center O 1-home
                        $cat_type = ""; 
                        $main_category = "";   //foot spa
                        $sub_category = "";   //category_id
                        
                        $sort = $params['sort'];
                        
                         if(array_key_exists("page",$params)){
                            $page = $params['page'];
                        } else {
                            $page = "";
                        }
                        $resp = $this->SpaModel->spa_center_select_order($user_id,$latitude, $longitude, $tab_id, $cat_type, $main_category, $sub_category, $sort, $page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function spa_center_list_recommended_v2() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "" || $params['api_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $api_type = $params['api_type'];     // 1-recommended 2-best deals
                        $tab_id = $params['tab_id'];         // 0-center O 1-home
                        $sub_category = $params['sub_category'];;   //category_id
                        $main_category = $params['main_category'];
                        $sort = $params['sort'];
                        if(array_key_exists("page",$params)){
                            $page = $params['page'];
                        } else {
                            $page = "";
                        }
                        
                        if(array_key_exists("category_type",$params)){
                            $category_type = $params['category_type'];
                        } else {
                            $category_type = "";
                        }
                        
                      
                        if(array_key_exists("price",$params)){
                            $price = $params['price'];
                        } else {
                            $price = "";
                        }
                        $resp = $this->SpaModel->spa_center_list_recommended_v2($user_id, $latitude, $longitude,$api_type, $tab_id,$main_category, $sub_category, $page, $sort,$category_type,$price);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function spa_center_list_category() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "" || $params['api_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $api_type=$params['api_type'];
                        if(array_key_exists("page",$params)){
                            $page = $params['page'];
                        } else {
                            $page = "";
                        }
                        $resp = $this->SpaModel->spa_center_list_category($user_id, $latitude, $longitude,$page,$api_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function spa_center_details_v2() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $branch_id = $params['branch_id'];
                        $resp = $this->SpaModel->spa_center_details_v2($user_id, $listing_id,$branch_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function spa_category_v2() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                   
                        $resp = $this->SpaModel->spa_category_v2();
                  
                    json_outputs($resp);
                }
            }
        }
    }
    public function view_all_list_typewise() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        if(array_key_exists("page",$params)){
                            $page = $params['page'];
                        } else {
                            $page = "";
                        }
                        $tab_id = $params['tab_id'];         // 0-center O 1-home
                        $cat_type = $params['cat_type']; 
                        $main_category = $params['main_category'];   //foot spa
                        $sub_category = $params['sub_category'];   //category_id
                        
                        $sort = $params['sort'];
                        
                        $resp = $this->SpaModel->view_all_list_typewise($user_id, $latitude, $longitude, $tab_id, $cat_type, $main_category, $sub_category, $sort, $page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function spa_trainer_list() {
        $this->load->model('SpaModel');
       
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
           
            $check_auth_client = $this->SpaModel->check_auth_client();
          
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
                        $listing_id = $params['listing_id'] ;
                        $gender = $params['gender'] ; //male,female
                        //$category = $params['category'];
                        $resp = $this->SpaModel->spa_trainer_list($user_id,$listing_id,$gender);
                    }
                  json_outputs($resp);
                }
            }
        }
    }
    public function add_bookings() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    //$params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($this->input->post('user_id') == "" || $this->input->post('listing_id') == "" || $this->input->post('package_id') == "" || $this->input->post('branch_id') == "" || $this->input->post('category_id') == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $this->input->post('user_id');
                        $listing_id = $this->input->post('listing_id');
                        $package_id = $this->input->post('package_id');
                        $branch_id = $this->input->post('branch_id');
                        $category_id = $this->input->post('category_id'); // at home/center
                        $patient_id = $this->input->post('patient_id');
                        $trainer_id = $this->input->post('trainer_id');
                        
                        $trail_booking_date = $this->input->post('booking_date');
                        $trail_booking_time = $this->input->post('booking_time');
                        
                        $user_name = $this->input->post('user_name');
                        $user_mobile = $this->input->post('user_mobile');
                        $user_email = $this->input->post('user_email');
                        $user_gender = $this->input->post('user_gender');
                        $user_age = $this->input->post('age');
                        $address_id = $this->input->post('address_id');
                        
                        $question = $this->input->post('question');
                        
                       /* $user_diet_preference= $params['diet_preference'];
                        $user_exercise_level= $params['exercise_level'];
                        $user_medical_condition= $params['medical_condition'];
                        $user_ever_went_gym= $params['ever_went_gym'];*/
                        
                        $resp = $this->SpaModel->add_bookings($user_id,$patient_id, $listing_id, $package_id, $branch_id, $category_id, $trail_booking_date, $trail_booking_time, $user_name, $user_mobile, $user_email, $user_gender,$user_age,$question,$trainer_id,$address_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function spaform() {
        $this->load->model('SpaModel');
       
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
           // $user_id = $this->input->post('user_id');
            
            $question = $this->input->post('question');

            $questions = json_decode($question);
            $pre_final_que = $questions->quastion;
            $final_que = $pre_final_que[0];
            $q_user_id = $final_que->user_id;
            $final_q = $final_que->qas;
            $data2 = array();
            $resp = $this->SpaModel->delete_question($q_user_id);
            for ($i = 0; $i < sizeof($final_q); $i++) {

                $data2['user_id'] = $q_user_id;
                $data2['question_id'] = $final_q[$i]->qid;
                $data2['answer'] = $final_q[$i]->qans;

                $resp = $this->SpaModel->update_userprofile_question($data2);
            }


            if ($resp == "") {
                $respStatus = 400;
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter all field'
                );
            }else{
                $respStatus = 400;
                $resp = array(
                    'status' => 400,
                    'message' => 'Sucess'
                );
                
            }
            json_output($respStatus, $resp);
        }
    }

    public function spa_question_list() {
         $this->load->model('SpaModel');
       
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
              $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $resp = $this->SpaModel->spa_question_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function spa_time_slot() {
         $this->load->model('SpaModel');
       
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
              $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $branch_id = $params['branch_id'];
                        $resp = $this->SpaModel->spa_time_slot($user_id,$branch_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    
    
    
    //---------------------------old----------------------------------
    public function spa_center_related_list() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == '' || $params['listing_id'] == '') {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $latitude    = $params['latitude'];
                        $longitude   = $params['longitude'];
                        $listing_id   = $params['listing_id'];
                      //$category_id = $params['category_id'];
                        $resp        = $this->SpaModel->spa_center_related_list($user_id,$latitude,$longitude,$listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function spa_center_other_branch() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['category_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $category_id_new = $params['category_id'];
                        $branch_id = $params['branch_id'];
                        $category_id=rtrim($category_id_new,',');
                        $resp = $this->SpaModel->spa_center_other_branch($user_id, $listing_id, $category_id, $branch_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function spa_center_list() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $category = $params['category'];
                        $resp = $this->SpaModel->spa_center_list($user_id, $latitude, $longitude, $category);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function spa_center_bachat_list() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['latitude'] == "" || $params['longitude'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $category = $params['category'];
                        $resp = $this->SpaModel->spa_center_bachat_list($user_id, $latitude, $longitude, $category);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  
    public function spa_center_details() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $resp = $this->SpaModel->spa_center_details($user_id, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function spa_category() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                   
                        $resp = $this->SpaModel->spa_category();
                  
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function add_review() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $branch_id = $params['branch_id'];
                        $resp = $this->SpaModel->add_review($user_id, $listing_id, $rating, $review, $service, $branch_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function edit_review() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['review_id'] == "" ||$params['user_id'] == "" || $params['listing_id'] == "" || $params['rating'] == "" || $params['review'] == "" || $params['service'] == "") {
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
                     
                        $resp = $this->SpaModel->edit_review($user_id, $listing_id, $rating, $review, $service,$review_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function review_list() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $resp = $this->SpaModel->review_list($user_id, $listing_id, $branch_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function review_with_comment() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $resp = $this->SpaModel->review_with_comment($user_id, $listing_id, $branch_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function review_like() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $resp = $this->SpaModel->review_like($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function review_comment() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $resp = $this->SpaModel->review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function review_comment_like() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $resp = $this->SpaModel->review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function review_comment_list() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $resp = $this->SpaModel->review_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function spa_views() {
        $this->load->model('SpaModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SpaModel->check_auth_client();
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
                        $resp = $this->SpaModel->spa_views($user_id, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
   
    public function update_price() {
        $this->load->model('SpaModel');
       
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
           
            $check_auth_client = $this->SpaModel->check_auth_client();
          
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                 
                        $resp = $this->SpaModel->update_price();
                    
                  json_outputs($resp);
                }
            }
        }
    }
    
   
    
}