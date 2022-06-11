<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class OpticController extends CI_Controller {

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

    public function optic_list() {
        $this->load->model('OpticModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->OpticModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    // if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "" || $params['radius'] == "") {
                    //     $resp = array('status' => 400, 'message' => 'please enter fields');
                    // } else {
                        $user_id = $params['user_id'];
                        $lat ='';
                        $lng = '';
                        $radius = '';
                        $resp = $this->OpticModel->optic_list($user_id, $lat, $lng, $radius);
                    // }
                    json_outputs($resp);
                }
            }
        }
    }

    public function pharmacy_details() {
        $this->load->model('OpticModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->OpticModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];

                        $resp = $this->OpticModel->pharmacy_details($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
}
