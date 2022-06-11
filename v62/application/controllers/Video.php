<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Video extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function med_tube() {
        $this->load->model('VideoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->VideoModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->VideoModel->med_tube($user_id);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }
    
    
    public function med_tube_by_type() {
        $this->load->model('VideoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->VideoModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                         $id = $params['id'];
                          $type= $params['type'];
                        $resp = $this->VideoModel->med_tube_by_type($user_id,$id,$type);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }
    
    
     public function med_tube_pagination() {
        $this->load->model('VideoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->VideoModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $page = $params['page'];
                        $category = $params['category'];
                        $keyword  = $params['keyword'];
                        $resp = $this->VideoModel->med_tube_pagination($user_id,$page,$category,$keyword);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }

    public function med_tube_likes() {
        $this->load->model('VideoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->VideoModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['video_id'] == "" || $params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $video_id = $params['video_id'];
                        $type = $params['type'];
                        $resp = $this->VideoModel->med_tube_likes($user_id, $video_id,$type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function med_tube_views() {
        $this->load->model('VideoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->VideoModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['video_id'] == "" || $params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $video_id = $params['video_id'];
                        $type = $params['type'];
                        $resp = $this->VideoModel->med_tube_views($user_id, $video_id,$type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function med_spread() {
        $this->load->model('VideoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->VideoModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $page    = $params['page'];
                        $keyword  = $params['keyword'];
                        $resp = $this->VideoModel->med_spread($user_id,$page,$keyword);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }
    
    
      public function med_spread_view_more() {
        $this->load->model('VideoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->VideoModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['id']=="") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                       
                        $id    = $params['id'];
                        $resp = $this->VideoModel->med_spread_view_more($user_id,$id);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }
    

    public function med_spread_likes() {
        $this->load->model('VideoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->VideoModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['video_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $video_id = $params['video_id'];
                        $resp = $this->VideoModel->med_spread_likes($user_id, $video_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function med_spread_views() {
        $this->load->model('VideoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->VideoModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['video_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $video_id = $params['video_id'];
                        $resp = $this->VideoModel->med_spread_views($user_id, $video_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

}
