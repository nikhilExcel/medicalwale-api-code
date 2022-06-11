<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function keyword_list() {
        $this->load->model('SearchModel');
        $this->load->library('ElasticSearch');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SearchModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['keyword'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                         if (array_key_exists("type",$params))
                            {                           
                        $type = $params['type'];
                        }else{
                        $type="";    
                            
                        }

                        $keyword = $params['keyword'];
                        $resp = $this->SearchModel->keyword_list($user_id,$type, $keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function search_list() {
        $this->load->model('SearchModel');
         $this->load->library('ElasticSearch');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SearchModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['keyword'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $keyword = $params['keyword'];
                        $resp = $this->SearchModel->search_list($user_id, $keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function search_doctor() {
        $this->load->model('SearchModel');
         $this->load->library('ElasticSearch');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SearchModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['keyword'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $keyword = $params['keyword'];
                        $resp = $this->SearchModel->search_doctor($user_id, $keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //added by zak on   18-09-2018
    public function search_list_by_category() {
        $this->load->model('SearchModel');
        $this->load->library('ElasticSearch');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SearchModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['keyword'] == "" || $params['type'] =="") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $keyword = $params['keyword'];
                        $type = $params['type'];
                        $resp = $this->SearchModel->search_list_by_category($user_id, $keyword,$type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }


    public function page_list() {
        $this->load->model('SearchModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SearchModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['keyword'] == "" || $params['listing_type'] == "" || $params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_type = $params['listing_type'];
                        $page = $params['page'];
                        $keyword = $params['keyword'];
                        $resp = $this->SearchModel->page_list($user_id, $keyword, $listing_type, $page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function profile_details() {
        $this->load->model('SearchModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SearchModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['post_user_id'] == "" || $params['listing_type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $post_user_id = $params['post_user_id'];
                        $user_id = $params['user_id'];
                        $listing_type = $params['listing_type'];
                        if(array_key_exists("branch_id",$params)){
                            $branch_id = $params['branch_id'];
                        } else {
                            $branch_id = 0;
                            
                        }
                        $resp = $this->SearchModel->profile_details($user_id, $post_user_id, $listing_type,$branch_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function image_list() {
        $this->load->model('SearchModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SearchModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['keyword'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $keyword = $params['keyword'];
                        $resp = $this->SearchModel->image_list($user_id, $keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
}
