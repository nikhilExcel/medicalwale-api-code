<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Substitute extends CI_Controller {

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
    
    public function substitute_list() {
        $this->load->model('Substitute_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Substitute_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['mname'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $mname = $params['mname'];
                       
                        $resp = $this->Substitute_model->substitute_list($mname);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     
     
    public function substitute_search() {
        $this->load->model('Substitute_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Substitute_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['mname'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $mname = $params['mname'];
                       
                        $resp = $this->Substitute_model->substitute_search($mname);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
}