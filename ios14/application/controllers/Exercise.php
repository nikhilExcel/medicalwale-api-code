<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Exercise extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function exercise_subcategory() {
        $this->load->model('ExerciseModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ExerciseModel->check_auth_client();
            if ($check_auth_client == true) {
               $response =  $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['category_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $category_id = $params['category_id'];
                        $resp = $this->ExerciseModel->exercise_subcategory($category_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function exercise_list() {
        $this->load->model('ExerciseModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ExerciseModel->check_auth_client();
            if ($check_auth_client == true) {
                $response =  $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['cat_id'] == "" || $params['subcat_id'] == "" || $params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $cat_id = $params['cat_id'];
                        $subcat_id = $params['subcat_id'];
                        $keyword = $params['keyword'];
                        $page = $params['page'];
                        $resp = $this->ExerciseModel->exercise_list($user_id, $cat_id, $subcat_id, $keyword, $page);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }
    
    public function exercise_details() {
        $this->load->model('ExerciseModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ExerciseModel->check_auth_client();
            if ($check_auth_client == true) {
                $response =  $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $id = $params['id'];
                        $resp = $this->ExerciseModel->exercise_details($user_id, $id);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }

    public function exercise_likes() {
        $this->load->model('ExerciseModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ExerciseModel->check_auth_client();
            if ($check_auth_client == true) {
                $response =  $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->ExerciseModel->exercise_likes($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function exercise_comment() {
        $this->load->model('ExerciseModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ExerciseModel->check_auth_client();
            if ($check_auth_client == true) {
               $response =   $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $comment = $params['comment'];
                        $resp = $this->ExerciseModel->exercise_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function exercise_comment_like() {
        $this->load->model('ExerciseModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ExerciseModel->check_auth_client();
            if ($check_auth_client == true) {
                $response =  $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->ExerciseModel->review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function exercise_comment_list() {
        $this->load->model('ExerciseModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ExerciseModel->check_auth_client();
            if ($check_auth_client == true) {
                $response =  $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->ExerciseModel->exercise_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
 
    public function exercise_views() {
        $this->load->model('ExerciseModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ExerciseModel->check_auth_client();
            if ($check_auth_client == true) {
                $response =  $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {

                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];

                        $resp = $this->ExerciseModel->exercise_views($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function exercise_upnext_list() {
        $this->load->model('ExerciseModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ExerciseModel->check_auth_client();
            if ($check_auth_client == true) {
               $response =   $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['cat_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $cat_id = $params['cat_id'];
                        $resp = $this->ExerciseModel->exercise_upnext_list($user_id, $listing_id, $cat_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

}
