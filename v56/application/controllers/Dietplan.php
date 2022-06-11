<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dietplan extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function leads_followup_list()
    {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['booking_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $book_id     = $params['booking_id'];
                        $resp = $this->dietplan_model->leads_followup_list($user_id,$book_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function leads_followup_update()
    {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['id'] == "" || $params['next_followup_date'] == "" || $params['next_followup_time'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $id     = $params['id'];
                        $next_followup_date = $params['next_followup_date'];
                        $next_followup_time = $params['next_followup_time'] ;
                        $resp = $this->dietplan_model->leads_followup_update($user_id,$id,$next_followup_date,$next_followup_time);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function missbelly_packages_list()
    {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
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
                        $resp = $this->dietplan_model->missbelly_packages_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function missbelly_diet_list_school()
    {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
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
                        $resp = $this->dietplan_model->missbelly_diet_list_school($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function renew_package()
    {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['package_id'] == "" || $params['from_date'] == "" ||  $params['gst'] == "" ||  $params['amount'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $package_id     = $params['package_id'];
                        $from_date   = $params['from_date'] ;
                        $gst  = $params['gst'];
                        $amount     = $params['amount'];
                        $resp = $this->dietplan_model->renew_package($user_id,$package_id,$from_date,$gst,$amount);
                    }
                       simple_json_output($resp);
                }
            }
        }
    }
    
       public function dietplan_booking() {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    //print_r($params);
                    if ($params['user_id'] == "" || $params['package_id'] == "" ||  $params['gst'] == "" ||  $params['amount'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id    = $params['user_id'];
                        $gst  = $params['gst'];
                        $amount     = $params['amount'];
                        $package_id = $params['package_id'];
                      
                        $booking_id = date('YmdHis');
                      
                        $resp = $this->dietplan_model->dietplan_booking($user_id, $package_id, $gst, $amount, $booking_id);
                    }

                    simple_json_output($resp);
                }
            }
        }
    }
  /* public function up()
    {
        $this->load->model('dietplan_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->dietplan_model->check_auth_client();
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
                      
                        $resp = $this->dietplan_model->up($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }*/
}
