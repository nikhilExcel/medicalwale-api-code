<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Trackhealthrecord extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
        /*
          $check_auth_client = $this->SexeducationModel->check_auth_client();
          if($check_auth_client != true){
          die($this->output->get_output());
          }
         */
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function record_list() {
        $this->load->model('TrackhealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->TrackhealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $type = $params['type'];
                        $user_id = $params['user_id'];
                        $resp = $this->TrackhealthrecordModel->record_list($user_id, $type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function add_record() {
        $this->load->model('TrackhealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->TrackhealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['type'] == "" || $params['value'] == "" || $params['date'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $type = $params['type'];
                        $user_id = $params['user_id'];
                        $value = $params['value'];
                        $date = $params['date'];
                        $resp = $this->TrackhealthrecordModel->add_record($user_id, $type, $value, $date);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function update_record() {
        $this->load->model('TrackhealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->TrackhealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['type'] == "" || $params['value'] == "" || $params['date'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $type = $params['type'];
                        $user_id = $params['user_id'];
                        $value = $params['value'];
                        $date = $params['date'];
                        $resp = $this->TrackhealthrecordModel->update_record($user_id, $type, $value, $date);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function delete_record() {
        $this->load->model('TrackhealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->TrackhealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['type'] == "" || $params['date'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $type = $params['type'];
                        $user_id = $params['user_id'];
                        $date = $params['date'];
                        $resp = $this->TrackhealthrecordModel->delete_record($user_id, $type, $date);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function update_profile() {
        $this->load->model('TrackhealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->TrackhealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['gender'] == "" || $params['height'] == "" || $params['weight'] == "" || $params['weight_date'] == "" || $params['height_date'] == "" || $params['age'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $gender = $params['gender'];
                        $height = $params['height'];
                        $weight = $params['weight'];
                        $weight_date = $params['weight_date'];
                        $height_date = $params['height_date'];
                        $age = $params['age'];
                        $activity_level = $params['activity_level'];
                        $resp = $this->TrackhealthrecordModel->update_profile($user_id, $gender, $height, $weight, $weight_date, $height_date, $age, $activity_level);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

}
