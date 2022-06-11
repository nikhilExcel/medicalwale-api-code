<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class subscription extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
         $this->load->model('LoginModel');
        
     
    }
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
     public function get_subscription()
    {
        $this->load->model('Subscription_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Subscription_model->check_auth_client();
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
                        $vendor_type = $params['vendor_type'];
                        $resp        = $this->Subscription_model->get_subscription($user_id,$vendor_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
}
