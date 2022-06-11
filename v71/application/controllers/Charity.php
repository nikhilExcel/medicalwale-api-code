<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Charity extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('LoginModel');
        $this->load->model('Charity_model');
    }
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
  
    public function charity_list()
    {
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Charity_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['type'] == "" || $params['page_no'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $page       = $params['page_no'];
                        $type       = $params['type'];
                        $search     = $params['search'];
                        $resp       = $this->Charity_model->charity_list($user_id,$type,$page,$search);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
  
    
}