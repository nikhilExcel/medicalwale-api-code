<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Language extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

 
    public function update_language() {
        $this->load->model('LanguageModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LanguageModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['language'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                        $language = $params['language'];
                        $resp = $this->LanguageModel->update_language($user_id,$language);
                         if($resp == 1)
                        {
                              $resp1 = array(
                            'status' => 200,
                            'message' => 'Success'
                            );
                             
                        }
                        else
                        {
                             $resp1 = array(
                            'status' => 400,
                            'message' => 'Unable to select Language.'
                                );
                            
                        }
                         json_outputs($resp1);
                    }
                    
                }
            }
        }
    }

}
