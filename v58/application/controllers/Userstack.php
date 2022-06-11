<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Userstack extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('LoginModel');
        $this->load->model('UserstackModel');
        $this->load->model('LabcenterModel_v2');
    
        
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }
    
    public function add_to_stack1(){ 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_type'] == "" || $params['stack'] == "") {
                        json_output(400, array('status' => 400, 'message' => 'please enter user_id, listing_type and stack'));
                    } else {
                        $user_id = $params['user_id'];
                        $listing_type = $params['listing_type'];
                        $stack = $params['stack'];
                        
                        $resp = $this->UserstackModel->add_to_stack1($user_id, $listing_type, $stack);
                        simple_json_output(array('status' => 200, 'message' => 'success', 'description' => 'Stack updated successfully'));
                    }
                    
                }
            }
        }
    }
    public function add_to_stack(){ 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    //$params = json_decode(file_get_contents('php://input'), TRUE);
                    
                     $data['user_id']  = $this->input->post('user_id');
                     $data['listing_type']  = $this->input->post('listing_type');
                     $data['stack']  = $this->input->post('stack');
                     
                     
                    if ($data['user_id'] == "" || $data['listing_type'] == "" || $data['stack'] == "") {
                        json_output(400, array('status' => 400, 'message' => 'please enter user_id, listing_type and stack'));
                    } else {
                        
                        $resp = $this->UserstackModel->add_to_stack($data);
                        if($resp['status'] == 200){
                            simple_json_output(array('status' => 200, 'message' => 'success', 'description' => 'Stack updated successfully'));    
                        } else if($resp['status'] == 201){
                            $name = $resp['vendor_name'];
                            $resp = $this->UserstackModel->add_to_stack($data);
                            $msg = $resp['message'];
                            simple_json_output(array('status' => 201, 'message' => 'failed', 'description' => $msg));    
                        } else {
                            simple_json_output(array('status' => 400, 'message' => 'failed', 'description' => 'Something went wrong'));    
                        }
                        
                    }
                    
                }
            }
        }
    }
    
    public function stack_list() {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
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
                        $user_id = $params['user_id'];
                        $resp = $this->UserstackModel->stack_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function delete_stack() {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $stack_id = $params['stack_id'];
                    $user_id = $params['user_id'];
                    if ($stack_id == "" || $user_id == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter stack_id and user_id'
                        );
                    } else {
                        
                        
                        if(array_key_exists('remove_all',$params)){
                            $remove_all = $params['remove_all'];
                        } else {
                            $remove_all = 0;
                        }
                        $resp = $this->UserstackModel->delete_stack($user_id,$stack_id,$remove_all);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function delete_test_packages() {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                    $service_type = $params['service_type'];
                    $service_id = $params['service_id'];
                    if ($service_type == "" || $user_id == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter user_id and service_type'
                        );
                    } else {
                        
                        
                        if(array_key_exists('remove_all',$params)){
                            $remove_all = $params['remove_all'];
                        } else {
                            $remove_all = 0;
                        }
                        $resp = $this->UserstackModel->delete_test_packages($user_id,$service_type,$service_id,$remove_all);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
}