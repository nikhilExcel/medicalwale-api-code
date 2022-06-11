<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dhlr extends CI_Controller {

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
    
    
       public function dhlr_list() {
        $this->load->model('dhlr_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dhlr_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $resp = $this->dhlr_model->dhlr_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function dhlr_add() {
        $this->load->model('dhlr_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dhlr_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['time'] == "" || $params['title'] == "" || $params['status'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $time     = $params['time'];
                        $title    = $params['title'];
                        $status     = $params['status'];
                        $resp = $this->dhlr_model->dhlr_add($user_id,$status,$time,$title);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
     public function dhlr_delete() {
        $this->load->model('dhlr_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dhlr_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $id     = $params['id'];
                        $resp = $this->dhlr_model->dhlr_delete($user_id,$id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function dhlr_update() {
        $this->load->model('dhlr_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dhlr_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['id'] == "" || $params['time'] == "" || $params['title'] == "" || $params['status'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $id     = $params['id'];
                        $time     = $params['time'];
                        $title    = $params['title'];
                        $status     = $params['status'];
                        $resp = $this->dhlr_model->dhlr_update($user_id,$id,$status,$time,$title);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
     public function dhlr_update_status() {
        $this->load->model('dhlr_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dhlr_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['id'] == "" || $params['status'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $id     = $params['id'];
                        $status     = $params['status'];
                        $resp = $this->dhlr_model->dhlr_update_status($user_id,$id,$status);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
      public function dhlr_delete_all() {
        $this->load->model('dhlr_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dhlr_model->check_auth_client();
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
                        $user_id     = $params['user_id'];
                        $resp = $this->dhlr_model->dhlr_delete_all($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
  
}
