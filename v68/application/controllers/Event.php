<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Event extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
         $this->load->model('LoginModel');
         $this->load->model('EventModel');
     
    }
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
     public function get_story_list() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->EventModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->EventModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->EventModel->get_story_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }   
    public function get_story_list_id() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->EventModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->EventModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->EventModel->get_story_list_id($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }   
     public function all_event_list_new()
    {
        
         $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->EventModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->EventModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $status      = $params['status'];   // all/upcoming/my
                       
                        $resp        = $this->EventModel->all_event_list_new($user_id,$status);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function event_tracker()
      {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->EventModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->EventModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $doctor_id   = $params['doctor_id'];   // all/upcoming/my
                        $count       = $params['count'];
                        $seen        = $params['seen'];
                        $story_id    = $params['story_id'];
                         if(array_key_exists("track_id",$params)){
                            $track_id = $params['track_id'];
                        } else {
                            $track_id = 0;
                        }
                        $resp        = $this->EventModel->event_tracker($user_id,$doctor_id,$count,$seen,$story_id,$track_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
      public function update_event_list()
    {
          $this->load->model('PartnerdoctorModel');
         $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                       // $find_date   = $params['date'];
                        $event_id    = $params['event_id'];
                        $intrested_status = $params['intrested_id'];
                        $resp        = $this->EventModel->update_event_list($user_id,$event_id,$intrested_status);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
}
?>
