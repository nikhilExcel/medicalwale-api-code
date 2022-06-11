<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Nightowls extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('LoginModel');
        $this->load->model('PartnermnoModel');
        $this->load->model('NightowlsModel');
        $this->load->model('LedgerModel');
        
        date_default_timezone_set('Asia/Kolkata');
    } 
    
    public function mno_list(){
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
                
                    
                $mno_list = $this->NightowlsModel->mno_list();
                if(sizeof($mno_list) > 0){
                    $resp = array(
                        'status' => 200,
                        'message' => 'success',
                        'data' => $mno_list
                    );    
                } else {
                     $resp = array(
                        'status' => 400,
                        'message' => 'No night owl found'
                        );
                }
            }
            simple_json_output($resp);
        }
    }
} 
?>