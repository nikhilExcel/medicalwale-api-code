<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Oldagehome extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function oldagehome_details() {
        $this->load->model('OldagehomeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->OldagehomeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->OldagehomeModel->oldagehome_details($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function oldagehome_list() {
        $this->load->model('OldagehomeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->OldagehomeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['latitude'] == "" || $params['longitude'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $resp = $this->OldagehomeModel->oldagehome_list($user_id, $latitude, $longitude);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function add_review() {
        $this->load->model('OldagehomeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->OldagehomeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['rating'] == "" || $params['review'] == "" || $params['service'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $rating = $params['rating'];
                        $review = $params['review'];
                        $service = $params['service'];
                        $resp = $this->OldagehomeModel->add_review($user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_list() {
        $this->load->model('OldagehomeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->OldagehomeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->OldagehomeModel->review_list($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

 public function review_with_comment() {
        $this->load->model('OldagehomeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->OldagehomeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->OldagehomeModel->review_with_comment($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }


    public function review_like() {
        $this->load->model('OldagehomeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->OldagehomeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->OldagehomeModel->review_like($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment() {
        $this->load->model('OldagehomeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->OldagehomeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $comment = $params['comment'];
                        $resp = $this->OldagehomeModel->review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_like() {
        $this->load->model('OldagehomeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->OldagehomeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->OldagehomeModel->review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_list() {
        $this->load->model('OldagehomeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->OldagehomeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->OldagehomeModel->review_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

}
