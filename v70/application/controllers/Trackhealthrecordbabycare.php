<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Trackhealthrecordbabycare extends CI_Controller {
    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
        
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }
    
      public function add_record() {
        $this->load->model('TrackbabyhealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->TrackbabyhealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['type'] == "" || $params['value'] == "" || $params['date'] == "" || $params['child_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $type = $params['type'];
                        $user_id = $params['user_id'];
                        $value = $params['value'];
                        $date = $params['date'];
                        $child_id = $params['child_id'];
                        $resp = $this->TrackbabyhealthrecordModel->add_record($user_id, $type, $value, $date, $child_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
      public function record_list() {
        $this->load->model('TrackbabyhealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->TrackbabyhealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['type'] == "" || $params['child_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $type = $params['type'];
                        $user_id = $params['user_id'];
                        $child_id = $params['child_id'];
                        $resp = $this->TrackbabyhealthrecordModel->record_list($user_id, $type, $child_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function update_record() {
        $this->load->model('TrackbabyhealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->TrackbabyhealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['type'] == "" || $params['value'] == "" || $params['date'] == "" || $params['child_id']=="") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $type = $params['type'];
                        $user_id = $params['user_id'];
                        $value = $params['value'];
                        $date = $params['date'];
                        $child_id = $params['child_id'];
                      //  $id = $params['id'];
                        $resp = $this->TrackbabyhealthrecordModel->update_record($user_id, $type, $value, $date, $child_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function delete_record() {
        $this->load->model('TrackbabyhealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->TrackbabyhealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                 
                    if ($params['user_id'] == "" || $params['type'] == "" || $params['child_id'] == "") {
                        
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $type = $params['type'];
                        $user_id = $params['user_id'];
                       $date = $params['date'];
                        $child_id = $params['child_id'];
                      //  $id = $params['id'];
                        $resp = $this->TrackbabyhealthrecordModel->delete_record($user_id,$type,$child_id,$date);
                    }
                    simple_json_output($resp);
                }
            }
        }
        
        
        
    }
    
     
    
}