<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Healthrecord extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
         $this->load->model('LoginModel');
        /*
          $check_auth_client = $this->SexeducationModel->check_auth_client();
          if($check_auth_client != true){
          die($this->output->get_output());
          }
         */
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }
    
    // Family Tree user list function by ghanshyam parihar starts
    public function healthrecord_familytree_user_list() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->HealthrecordModel->healthrecord_familytree_user_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    // Family Tree user list function by ghanshyam parihar ends
    
    // Family Tree list function by ghanshyam parihar starts
    public function healthrecord_familytree_list() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->HealthrecordModel->healthrecord_familytree_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    // Family Tree list function by ghanshyam parihar ends

// Add folder function by Dinesh Suthar starts
 // Add folder function by Dinesh Suthar starts
 public function add_folder() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['patient_id'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                        $patient_id = $params['patient_id'];
                        $folder_name = $params['folder_name'];
                        $date = $params['date'];
                        $resp = $this->HealthrecordModel->add_folder($user_id,$patient_id,$folder_name,$date);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function edit_folder() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['patient_id'] == "" ||$params['folder_id'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $folder_id= $params['folder_id'];
                        $user_id = $params['user_id'];
                        $patient_id = $params['patient_id'];
                        $folder_name = $params['folder_name'];
                        $date = $params['date'];
                        $resp = $this->HealthrecordModel->edit_folder($folder_id,$user_id,$patient_id,$folder_name,$date);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function health_folder_delete(){
        
         $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['folder_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $id = $params['folder_id'];
                        
                        $resp = $this->HealthrecordModel->health_folder_delete($id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
     public function folder_list() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['patient_id'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                       
                        $patient_id = $params['patient_id'];
                       
                        $resp = $this->HealthrecordModel->folder_list($patient_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function healthrecord_list_web() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == ""  ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                      
                        $resp = $this->HealthrecordModel->healthrecord_list_web($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
      public function healthrecord_document_delete() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['media_id'] == ""  ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $media_id = $params['media_id'];
                      
                        $resp = $this->HealthrecordModel->healthrecord_document_delete($media_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
// Add folder function by Dinesh Suthar ends

    public function add_record() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {

            $params = json_decode(file_get_contents('php://input'), TRUE);
            if ($params['user_id'] == "" || $params['patient_name'] == "" || $params['relationship'] == "" || $params['date_of_birth'] == "" || $params['gender'] == "") {
                $resp = array('status' => 400, 'message' => 'please enter fields');
            } else {
                $user_id = $params['user_id'];
                $patient_name = $params['patient_name'];
                $relationship = $params['relationship'];
                $date_of_birth = $params['date_of_birth'];
                $gender = $params['gender'];
                $resp = $this->HealthrecordModel->add_record($user_id, $patient_name, $relationship, $date_of_birth, $gender);
            }
            simple_json_output($resp);
        }
    }

    public function healthrecord_list() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                        $resp = $this->HealthrecordModel->healthrecord_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function healthrecord_list_group() {

        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                        $resp = $this->HealthrecordModel->healthrecord_list_group($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function healthrecord_list_group_v2() {

        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                        $resp = $this->HealthrecordModel->healthrecord_list_group_v2($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    public function healthrecord_details() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['patient_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                        $patient_id = $params['patient_id'];
                        $resp = $this->HealthrecordModel->healthrecord_details($user_id,$patient_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function health_list_by_date() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['patient_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $patient_id = $params['patient_id'];
                        $resp = $this->HealthrecordModel->health_list_by_date($patient_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function health_record_delete() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['patient_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $id = $params['patient_id'];
                        $resp = $this->HealthrecordModel->health_record_delete($user_id,$id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    public function health_document_delete(){
        
         $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['patient_id'] == "" || $params['document_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $id = $params['patient_id'];
                        $docmunt_id =$params['document_id'];
                        $resp = $this->HealthrecordModel->health_document_delete($user_id,$id,$docmunt_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    
    public function health_document_add_web(){
       
         $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
            
                    
                        $folder_id = $this->input->post('folder_id');
                         $patient_id =$this->input->post('patient_id');
                          $caption = $this->input->post('caption');
                          $document_name = $this->input->post('document_name');
                            $user_id = $this->input->post('user_id');
                        $document_file =$this->input->post('document_file');
                          $date_added = $this->input->post('date_added');
                       
                       
                 if ($user_id == "" || $patient_id == "" || $folder_id == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                       
                        $resp = $this->HealthrecordModel->health_document_add_web($user_id, $folder_id, $patient_id, $document_name, $caption, $document_file, $date_added);
                    }
                    simple_json_output($resp);
                }
            }
        }
    } 
    
    // Added by swapnali
    
      public function healthrecord_list_web_v2() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == ""  ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                      
                        $resp = $this->HealthrecordModel->healthrecord_list_web_v2($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function healthrecord_document_delete_v2() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['media_id'] == ""  ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $media_id = $params['media_id'];
                      
                        $resp = $this->HealthrecordModel->healthrecord_document_delete_v2($media_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function health_document_add_web_v2(){
       
         $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
            
                    
                        $folder_id = $this->input->post('folder_id');
                         $patient_id =$this->input->post('patient_id');
                          $caption = $this->input->post('caption');
                          $document_name = $this->input->post('document_name');
                            $user_id = $this->input->post('user_id');
                        $document_file =$this->input->post('document_file');
                          $date_added = $this->input->post('date_added');
                       
                       
                 if ($user_id == "" || $patient_id == "" || $folder_id == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                       
                        $resp = $this->HealthrecordModel->health_document_add_web_v2($user_id, $folder_id, $patient_id, $document_name, $caption, $document_file, $date_added);
                    }
                    simple_json_output($resp);
                }
            }
        }
    } 
    
    public function folder_list_v2() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['patient_id'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                       
                        $patient_id = $params['patient_id'];
                       
                        $resp = $this->HealthrecordModel->folder_list_v2($patient_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function add_folder_v2() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['patient_id'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                        $patient_id = $params['patient_id'];
                        $folder_name = $params['folder_name'];
                        $date = $params['date'];
                        $resp = $this->HealthrecordModel->add_folder_v2($user_id,$patient_id,$folder_name,$date);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function edit_folder_v2() {
        $this->load->model('HealthrecordModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthrecordModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['patient_id'] == "" ||$params['folder_id'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $folder_id= $params['folder_id'];
                        $user_id = $params['user_id'];
                        $patient_id = $params['patient_id'];
                        $folder_name = $params['folder_name'];
                        $date = $params['date'];
                        $resp = $this->HealthrecordModel->edit_folder_v2($folder_id,$user_id,$patient_id,$folder_name,$date);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

}
