<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Doctoronline extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
         $this->load->model('LoginModel');
         $this->load->model('DoctoronlineModel');
     
    }
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
    
    public function doctor_online_cat() 
    {
        $this->load->model('DoctoronlineModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->DoctoronlineModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->DoctoronlineModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                     if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == ""  ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                     
                        $resp = $this->DoctoronlineModel->doctor_online_cat($user_id,$lat,$lng);
                    }
                }
      
               json_outputs($resp);
            }
        }
    }
    
     public function doctor_online_detail() 
    {
        $this->load->model('DoctoronlineModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->DoctoronlineModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->DoctoronlineModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                     if ($params['user_id'] == "" || $params['doctor_user_id'] == ""   ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $doctor_user_id     = $params['doctor_user_id'];
                       
                     
                        $resp = $this->DoctoronlineModel->doctor_online_detail($user_id,$doctor_user_id);
                    }
                }
      
               json_outputs($resp);
            }
        }
    }
    
     public function doctor_online_user() {
        $this->load->model('DoctoronlineModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctoronlineModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->DoctoronlineModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" || $params['user_id'] == "" || $params['fee']=="" || $params['online_type']=="" || $params['transaction']=="" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $doctor_id=$params['doctor_id'];
                        $user_id=$params['user_id'];
                        $fee=$params['fee'];
                        $online_type=$params['online_type'];
                        $transaction=$params['transaction'];
                        $resp = $this->DoctoronlineModel->doctor_online_user($doctor_id,$user_id,$fee,$online_type,$transaction);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
}
