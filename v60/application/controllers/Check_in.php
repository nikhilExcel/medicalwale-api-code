<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Check_in extends CI_Controller
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
  
    public function all_vendor_list()
    {
        $this->load->model('Check_in_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Check_in_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $lat        = $params['lat'];
                        $lng        = $params['lng'];
                        $page       = $params['page'];
                        $vendor_id  = $params['vendor_id'];
                        $keyword  = $params['keyword'];
                        $resp       = $this->Check_in_model->all_vendor_list($user_id, $lat, $lng,$page,$vendor_id,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function addCheckInDetails() 
        {
        $this->load->model('Check_in_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Check_in_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                    
                    if ($this->input->post('user_id') == "" || $this->input->post('vendor_id') == "" || $this->input->post('amount') == "")
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $this->input->post('user_id');
                        $vendor_id = $this->input->post('vendor_id');
                        $amount = $this->input->post('amount');
                        $tags = $this->input->post('tags');
                        $show_image = $this->input->post('showpost'); 
                        $resp = $this->Check_in_model->addCheckInDetails($user_id,$vendor_id,$amount,$tags,$show_image);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
}