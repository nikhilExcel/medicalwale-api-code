<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class spin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
         $this->load->model('LoginModel');
         $this->load->model('Spin_Model');
     
    }
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    public function get_all_spin_data()
    {
        $this->load->model('Spin_Model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Spin_Model->check_auth_client();
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
                        $coupon_id     = $params['coupon_id'];
                      
                        
                        $resp        = $this->Spin_Model->get_all_spin_data($user_id,$coupon_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function spin_action()
    {
        $this->load->model('Spin_Model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Spin_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['coupon_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $coupon_id     = $params['coupon_id'];
                        $type     = $params['type'];
                       
                        
                        $resp        = $this->Spin_Model->spin_action($user_id,$coupon_id,$type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
 
    
}