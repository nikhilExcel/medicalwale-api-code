<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends CI_Controller
{
    public function state_list()
    {
        $this->load->model('common_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                
                if ($user_id != '') {
                    $response = $this->common_model->state_list($user_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    
    
    public function city_list()
    {
        $this->load->model('common_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params   = json_decode(file_get_contents('php://input'), TRUE);
                $user_id  = $params['user_id'];
                $state_id = $params['state_id'];
                
                if ($user_id != '' && $state_id != '') {
                    $response = $this->common_model->city_list($user_id, $state_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
}