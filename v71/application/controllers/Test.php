<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Test extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
         $this->load->model('LoginModel');
         $this->load->model('Testmodel');
     
    }
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
    
    
    
    
    public function doctor_list()
    {
        $this->load->model('TestModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Testmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "" || $params['category_id'] == " "   ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $latitude    = $params['latitude'];
                        $longitude   = $params['longitude'];
                        $category_id = $params['category_id'];
                        if(array_key_exists("page",$params)){
                            $page = $params['page'];
                        } else {
                            $page = '';
                        }
                        if(array_key_exists("keyword",$params)){
                            $keyword= $params['keyword'];
                        } else {
                            $keyword= '';
                        }
                        
                        
                        
                        $resp  = $this->Testmodel->doctor_list($latitude, $longitude, $user_id, $category_id,$page,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function mcrypt()
    {
        $this->load->model('TestModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Testmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                  
                        $Key    = $params['key'];
                       
                        
                        $resp  = $this->Testmodel->mcrypt($Key);
                   // print_r($resp);
                    json_outputs($resp);
                }
            }
        }
    }
}