<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Referral extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('LoginModel');
        $this->load->model('referral_model');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

   
    public function referral_details() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->referral_model->get_referral_details($user_id);
                    }
                    simple_json_output($resp);
                
            }
        }
    }
    
 
    
   public function referral_url($referral_code) { 
     $user=$this->referral_model->get_user_by_refferal($referral_code);
     if(!empty($user)){
      $page_data['page_name'] = 'referral_url';
      $page_data['page_title'] = "Referral URL";
      $page_data['referral_code'] = $referral_code;
      $this->load->view('referral_url.php', $page_data);
     }
  }  
  

 

  
  
}


