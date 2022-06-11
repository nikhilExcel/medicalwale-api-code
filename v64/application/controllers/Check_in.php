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
    
     public function vendor_list()
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
                        $resp       = $this->Check_in_model->vendor_list($user_id, $lat, $lng,$page,$vendor_id,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
     public function all_list()
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
                        $keyword  = $params['keyword'];
                        $resp       = $this->Check_in_model->all_list($user_id, $lat, $lng,$page,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function card_wallet_list()
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
                    if ($params['user_id'] == "" || $params['type'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $card  = $params['type'];
                      
                        $resp       = $this->Check_in_model->card_wallet_list($user_id, $card);
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
            
                    
                    if ($this->input->post('user_id') == "" || $this->input->post('vendor_id') == "" || $this->input->post('listing_id') == "" || $this->input->post('amount') == "")
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $this->input->post('user_id');
                        $vendor_id = $this->input->post('vendor_id');
                        $listing_id = $this->input->post('listing_id');
                        $amount = $this->input->post('amount');
                        $tags = $this->input->post('tags');
                        $show_image = $this->input->post('showpost'); 
                        $resp = $this->Check_in_model->addCheckInDetails($user_id,$vendor_id,$amount,$tags,$show_image,$listing_id);
                    }
                    simple_json_output($resp);
                
        }
    }
    
    
    public function all_edit_review() {
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
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['rating'] == "" || $params['review'] == "" || $params['service'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $type=$params['type'];
                        $review_id = $params['review_id'];
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $rating = $params['rating'];
                        $review = $params['review'];
                        $service = $params['service'];
                        $resp = $this->Check_in_model->all_edit_review($type,$review_id,$user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
      public function feature_list()
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
                    if ($params['user_id'] == "" || $params['type'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $type  = $params['type'];
                      
                        $resp       = $this->Check_in_model->feature_list($user_id, $type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
}
