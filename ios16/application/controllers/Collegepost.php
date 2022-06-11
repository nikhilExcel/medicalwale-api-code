<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Collegepost extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('CollegepostModel');
         $this->load->model('LoginModel');
        /*
          $check_auth_client = $this->SexeducationModel->check_auth_client();
          if($check_auth_client != true){
          die($this->output->get_output());
          }
         */
    }

//Comment by sandeep Hello
    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function college_post_list() {
        $this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['page'] == "" || $params['college_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $college_id = $params['college_id'];
                        $page = $params['page'];
                        $resp = $this->CollegepostModel->college_post_list($user_id, $page, $college_id);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }

    public function college_post_question() {
        $this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post'] == "" || $params['type'] == "" || $params['college_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post = $params['post'];
                        $type = $params['type'];
                        $college_id = $params['college_id'];




                        $resp = $this->CollegepostModel->college_post_question($user_id, $post, $type, $college_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function college_post_details() {
        $this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->CollegepostModel->college_post_details($user_id, $post_id);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }

    public function college_post_like() {
        $this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];

                        $post_user_id = $params['post_user_id'];

                        $resp = $this->CollegepostModel->college_post_like($user_id, $post_id, $post_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function college_post_comment() {
        $this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "" || $params['post_user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $comment = $params['comment'];
                        $post_user_id = $params['post_user_id'];

                        $resp = $this->CollegepostModel->college_post_comment($user_id, $post_id, $comment, $post_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function college_post_comment_list() {
        $this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->CollegepostModel->college_post_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function college_post_comment_like() {
        $this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all  ');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                         $post_id = $params['post_id'];
                        $comment_user_id = $params['comment_user_id'];

                        $resp = $this->CollegepostModel->college_post_comment_like($user_id, $comment_id,$post_id,$comment_user_id);
                    }
                    simple_json_output($resp);
                } 
            }
        }
    }
    
    public function college_post_comment_reply_like() {
        $this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_reply_id'] == "" || $params['post_id'] == "" || $params['comment_reply_user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_reply_id = $params['comment_reply_id'];
                        $post_id = $params['post_id'];
                        $comment_reply_user_id = $params['comment_reply_user_id'];


                        $resp = $this->CollegepostModel->college_post_comment_reply_like($user_id, $post_id, $comment_reply_id, $comment_reply_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function college_post_hide() {
        $this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->CollegepostModel->college_post_hide($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function college_post_delete() {
        $this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->CollegepostModel->college_post_delete($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function college_post_comment_reply_delete() {
        $this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->CollegepostModel->college_post_comment_reply_delete($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function college_follow_post() {
        $this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $post_user_id = $params['post_user_id'];
                     
                        $resp = $this->CollegepostModel->college_follow_post($user_id, $post_id,$post_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function college_video_views() {
        $this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['media_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $media_id = $params['media_id'];
                        $resp = $this->CollegepostModel->college_video_views($user_id, $media_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function medicalCollegeQuestionEdit() {
       $this->load->model('LoginModel');
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                   
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    //print_r($params);
                    if ($params['user_id'] == "" || $params['post'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post = $params['post'];
                        $post_id = $params['post_id'];
                        $resp = $this->CollegepostModel->medical_college_question($user_id, $post, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        } 
    }
    
      public function college_post_comment_reply() {

        //$this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "" || $params['comment'] == "" || $params['post_id'] == "" || $params['post_user_id'] == "" || $params['comment_user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $comment_id = $params['comment_id'];
                        $comment = $params['comment'];
                        $post_user_id = $params['post_user_id'];
                        $comment_user_id = $params['comment_user_id'];
                       
                        $resp = $this->CollegepostModel->medical_college_comment_reply($user_id, $post_id, $comment_id, $comment, $post_user_id, $comment_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function post_comment_all_reply_list() {

        //$this->load->model('CollegepostModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->CollegepostModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                       
                       
                        $resp = $this->CollegepostModel->post_comment_all_reply_list($comment_id, $user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    

}
