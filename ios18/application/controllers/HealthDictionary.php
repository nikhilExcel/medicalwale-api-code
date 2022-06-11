<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class HealthDictionary extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('LoginModel');
        /*
        $check_auth_client = $this->SexeducationModel->check_auth_client();
        if($check_auth_client != true){
        die($this->output->get_output());
        }
        */
    }
    
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
    
    public function WordOfTheDay()
    {
        $this->load->model('HealthDictionaryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            
            $params = json_decode(file_get_contents('php://input'), TRUE);
            if ($params['user_id'] == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                $user_id = $params['user_id'];
                $resp    = $this->HealthDictionaryModel->WordOfTheDayModel($user_id);
            }
            simple_json_output($resp);
        }
    }
    
    
    public function WordOfTheDay_By_date()
    {
        $this->load->model('HealthDictionaryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            
            $params = json_decode(file_get_contents('php://input'), TRUE);
            if ( $params['date'] == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                if(array_key_exists("user_id",$params)){
                           $user_id = $params['user_id'];
                        } else {
                             $user_id= '';
                        }
                
                $date    = $params['date'];
                $resp    = $this->HealthDictionaryModel->WordOfTheDay_By_date($user_id, $date);
            }
            simple_json_output($resp);
        }
    }
    
    
    public function Searchword()
    {
        $this->load->model('HealthDictionaryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            
            $params = json_decode(file_get_contents('php://input'), TRUE);
            if ($params['keyword'] == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                //  $user_id = $params['user_id'];
                $keyword = $params['keyword'];
                
                $resp = $this->HealthDictionaryModel->SearchwordModel($keyword);
            }
            simple_json_output($resp);
        }
    }
    
    
    
    public function Get_health_quates()
    {
        $this->load->model('HealthDictionaryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            
            $params = json_decode(file_get_contents('php://input'), TRUE);
            if ($params['user_id'] == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                if(array_key_exists("user_id",$params)){
                           echo $user_id = $params['user_id'];
                        } else {
                             $user_id= '';
                        }
                $resp    = $this->HealthDictionaryModel->Get_health_quates($user_id);
            }
            simple_json_output($resp);
        }
    }
    
}