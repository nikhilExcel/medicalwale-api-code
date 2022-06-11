<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Missbelly extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function miss_belly_character() {
        $this->load->model('MissBellyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MissBellyModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $type = $params['type'];
                        $resp = $this->MissBellyModel->miss_belly_character($type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function miss_belly_add_question() {
        $this->load->model('MissBellyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MissBellyModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['user_name'] == "" || $params['question'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $user_name = $params['user_name'];
                        $user_image = $params['user_image'];
                        $question = $params['question'];
                        $age = $params['age'];
                        $height = $params['height'];
                        $weight = $params['weight'];
                        $diet_preference = $params['diet_preference'];
                        $post_location = $params['post_location'];
                        $resp = $this->MissBellyModel->miss_belly_add_question($user_id, $user_name, $user_image, $question, $age, $height, $weight, $diet_preference, $post_location);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function miss_belly_add_reply() {
        $this->load->model('MissBellyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MissBellyModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['doctor_id'] == "" || $params['post_id'] == "" || $params['type'] == "" || $params['answer'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $doctor_id = $params['doctor_id'];
                        $post_id = $params['post_id'];
                        $type = $params['type'];
                        $answer = $params['answer'];

                        $resp = $this->MissBellyModel->miss_belly_add_reply($user_id, $doctor_id, $post_id, $type, $answer);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function miss_belly_question_list() {
        $this->load->model('MissBellyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MissBellyModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $page = $params['page'];
                        $resp = $this->MissBellyModel->miss_belly_question_list($user_id, $page);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }

    public function miss_belly_question_details() {
        $this->load->model('MissBellyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MissBellyModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->MissBellyModel->miss_belly_question_details($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function miss_belly_like() {
        $this->load->model('MissBellyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MissBellyModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['user_image'] == "" || $params['user_name'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $user_image = $params['user_image'];
                        $user_name = $params['user_name'];
                        $post_user_id = $params['post_user_id'];
                        $resp = $this->MissBellyModel->miss_belly_like($user_id, $post_id, $user_image, $user_name, $post_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function miss_belly_user_like_list() {
        $this->load->model('MissBellyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MissBellyModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {

                        $post_id = $params['post_id'];

                        $resp = $this->MissBellyModel->miss_belly_user_like_list($post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function miss_belly_is_notify() {
        $this->load->model('MissBellyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MissBellyModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->MissBellyModel->miss_belly_is_notify($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function miss_belly_hide() {
        $this->load->model('MissBellyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MissBellyModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->MissBellyModel->miss_belly_hide($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function miss_belly_user_update() {
        $this->load->model('MissBellyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MissBellyModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['user_name'] == "" || $params['user_image'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $user_name = $params['user_name'];
                        $user_image = $params['user_image'];

                        $resp = $this->MissBellyModel->miss_belly_user_update($user_id, $user_name, $user_image);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function miss_belly_user_check() {
        $this->load->model('MissBellyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MissBellyModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];

                        $resp = $this->MissBellyModel->miss_belly_user_check($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function miss_belly_delete() {
        $this->load->model('MissBellyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MissBellyModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->MissBellyModel->miss_belly_delete($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function miss_belly_reply_delete() {
        $this->load->model('MissBellyModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MissBellyModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['answer_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $answer_id = $params['answer_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->MissBellyModel->miss_belly_reply_delete($post_id, $answer_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

}
