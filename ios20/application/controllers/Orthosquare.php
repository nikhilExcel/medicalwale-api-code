<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orthosquare extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('PharmacyPartnerModel');
    }    
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }    
    
    
    public function get_user_data(){
        $this->load->model('Orthosquare_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        }else{
            $check_auth_client = $this->Orthosquare_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Orthosquare_model->auth();
                if ($response['status'] == 200) {
                    // $params = json_decode(file_get_contents('php://input'), TRUE);
                    // if ($params['user_id'] == "") {
                    //     $resp = array(
                    //         'status' => 400,
                    //         'message' => 'please enter fields'
                    //     );
                    // } else {
                    //     $user_id = $params['user_id'];
                        $resp = $this->Orthosquare_model->get_data();
                   // }
                    simple_json_output($resp);
                }
            }
        }
       //}
    }
    
     public function dental_clinic_list()
    {
        $this->load->model('Orthosquare_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Orthosquare_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Orthosquare_model->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $lat     = $params['lat'];
                        $lng     = $params['lng'];
                        $type    = $params['type'];
                         
                        $resp = $this->Orthosquare_model->dental_clinic_list($user_id,$lat,$lng,$type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
}
   
?>