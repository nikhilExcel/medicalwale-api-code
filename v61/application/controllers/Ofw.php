<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ofw extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('LoginModel');
        /*
        $check_auth_client = $this->SexeducationModel->check_auth_client();
        if($check_auth_client != true){
        die($this->output->get_output());
        }update_cycle_category_status
        */
    }
    
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
    public function home_remedies()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" && $params['user_id'] == "category_id") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'user id not blank'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $category_id = $params['category_id'];
                        $resp        = $this->OfwModel->home_remedies($user_id, $category_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function home_remedies_likes()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['home_remedies_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'user id not blank'
                        );
                    } else {
                        $user_id          = $params['user_id'];
                        $home_remedies_id = $params['home_remedies_id'];
                        $resp             = $this->OfwModel->home_remedies_likes($user_id, $home_remedies_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function home_remedies_bookmark()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['home_remedies_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'user id not blank'
                        );
                    } else {
                        $user_id          = $params['user_id'];
                        $home_remedies_id = $params['home_remedies_id'];
                        $resp             = $this->OfwModel->home_remedies_bookmark($user_id, $home_remedies_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function ask_saheli_character()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->OfwModel->ask_saheli_character();
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function ask_saheli_post_category()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    
                    $resp = $this->OfwModel->ask_saheli_post_category();
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function ask_saheli_post_list()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['saheli_category'] == "" || $params['page'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id         = $params['user_id'];
                        $saheli_category = $params['saheli_category'];
                        $page            = $params['page'];
                        $resp            = $this->OfwModel->ask_saheli_post_list($user_id, $saheli_category, $page);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }
    
    public function ask_saheli_post_details()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp    = $this->OfwModel->ask_saheli_post_details($user_id, $post_id);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }
    
    public function ask_saheli_post_like()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['user_name'] == "" || $params['user_image'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id      = $params['user_id'];
                        $post_id      = $params['post_id'];
                        $user_name    = $params['user_name'];
                        $user_image   = $params['user_image'];
                        $post_user_id = $params['post_user_id'];
                        
                        $resp = $this->OfwModel->ask_saheli_post_like($user_id, $post_id, $user_name, $user_image, $post_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function ask_saheli_post_comment()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "" || $params['user_name'] == "" || $params['user_image'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id      = $params['user_id'];
                        $post_id      = $params['post_id'];
                        $comment      = $params['comment'];
                        $user_name    = $params['user_name'];
                        $user_image   = $params['user_image'];
                        $post_user_id = $params['post_user_id'];
                        
                        
                        $resp = $this->OfwModel->ask_saheli_post_comment($user_id, $post_id, $comment, $user_name, $user_image, $post_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function ask_saheli_post_comment_list()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp    = $this->OfwModel->ask_saheli_post_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function ask_saheli_post_comment_like()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "" || $params['user_name'] == "" || $params['user_image'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id         = $params['user_id'];
                        $comment_id      = $params['comment_id'];
                        $user_name       = $params['user_name'];
                        $user_image      = $params['user_image'];
                        $post_id         = $params['post_id'];
                        $comment_user_id = $params['comment_user_id'];
                        
                        $resp = $this->OfwModel->ask_saheli_post_comment_like($user_id, $comment_id, $user_name, $user_image, $post_id, $comment_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function ask_saheli_add_question()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['user_name'] == "" || $params['question'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id         = $params['user_id'];
                        //$doctor_id = $params['doctor_id'];
                        $user_name       = $params['user_name'];
                        $user_image      = $params['user_image'];
                        $question        = $params['question'];
                        $category        = $params['category'];
                        $saheli_category = $params['saheli_category'];
                        $post_location   = $params['post_location'];
                        $resp            = $this->OfwModel->ask_saheli_add_question($user_id, $user_name, $user_image, $question, $category, $post_location, $saheli_category);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function ask_saheli_user_like_list()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['post_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $post_id = $params['post_id'];
                        $resp    = $this->OfwModel->ask_saheli_user_like_list($post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function ask_saheli_video_views()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
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
                        $user_id  = $params['user_id'];
                        $media_id = $params['media_id'];
                        $resp     = $this->OfwModel->ask_saheli_video_views($user_id, $media_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function ask_saheli_post_views()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp    = $this->OfwModel->ask_saheli_post_views($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function ask_saheli_follow_post()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp    = $this->OfwModel->ask_saheli_follow_post($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function ask_saheli_user_update()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['user_name'] == "" || $params['user_image'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $user_name  = $params['user_name'];
                        $user_image = $params['user_image'];
                        
                        $resp = $this->OfwModel->ask_saheli_user_update($user_id, $user_name, $user_image);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function ask_saheli_user_check()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        
                        $resp = $this->OfwModel->ask_saheli_user_check($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function ask_saheli_post_hide()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp    = $this->OfwModel->ask_saheli_post_hide($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function ask_saheli_post_delete()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp    = $this->OfwModel->ask_saheli_post_delete($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function ask_saheli_edit_question()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['post_id'] == "" || $params['user_id'] == "" || $params['question'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $post_id         = $params['post_id'];
                        $user_id         = $params['user_id'];
                        $user_name       = $params['user_name'];
                        $user_image      = $params['user_image'];
                        $question        = $params['question'];
                        $category        = $params['category'];
                        $saheli_category = $params['saheli_category'];
                        $resp            = $this->OfwModel->ask_saheli_edit_question($post_id, $user_id, $user_name, $user_image, $question, $category, $saheli_category);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function repost()
    {
        
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $repost_user_id  = $params['user_id'];
                        $post_id         = $params['post_id'];
                        $repost_location = $params['repost_location'];
                        $resp            = $this->OfwModel->repost($repost_user_id, $post_id, $repost_location);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function ask_saheli_post_comment_reply()
    {
        
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "" || $params['comment'] == "" || $params['post_id'] == "" || $params['post_user_id'] == "" || $params['comment_user_id'] == "" || $params['user_image'] == "" || $params['user_name'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id         = $params['user_id'];
                        $post_id         = $params['post_id'];
                        $comment_id      = $params['comment_id'];
                        $comment         = $params['comment'];
                        $post_user_id    = $params['post_user_id'];
                        $comment_user_id = $params['comment_user_id'];
                        
                        $user_name  = $params['user_name'];
                        $user_image = $params['user_image'];
                        $resp       = $this->OfwModel->ask_saheli_post_comment_reply($user_id, $post_id, $comment_id, $comment, $post_user_id, $comment_user_id, $user_name, $user_image);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function ask_saheli_post_comment_reply_like()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "" || $params['user_name'] == "" || $params['user_image'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id         = $params['user_id'];
                        $comment_id      = $params['comment_id'];
                        $user_name       = $params['user_name'];
                        $user_image      = $params['user_image'];
                        $post_id         = $params['post_id'];
                        $comment_user_id = $params['comment_user_id'];
                        /* $comment_reply_id = $params['comment_reply_id'];*/
                        
                        $resp = $this->OfwModel->ask_saheli_post_comment_reply_like($user_id, $comment_id, $user_name, $user_image, $post_id, $comment_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function ask_saheli_post_comment_all_reply_list()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['comment_id'] == "" || $params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $comment_id = $params['comment_id'];
                        $user_id    = $params['user_id'];
                        $resp       = $this->OfwModel->ask_saheli_post_comment_all_reply_list($comment_id, $user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //addded for all mestrual cycle services by zak
    
    public function add_menstrual_cycle_data()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
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
                        $history_id       = $params['history_id'];
                        $user_id          = $params['user_id'];
                        $name             = $params['name'];
                        $start_period_day = $params['start_period_day'];
                        $cycle_length     = $params['cycle_length'];
                        $total_period_day = $params['total_period_day'];
                        $born_of_year     = $params['born_of_year'];
                        $tutual_cycle     = $params['tutual_cycle'];
                        $profile_id       = $params['profile_id'];
                        //$purpose_tracking = $params['purpose_tracking'];
                        $tracking_resoan  = $params['resoan'];
                        $relationship     = $params['relationship'];
                         if (array_key_exists("pcod",$params)){
                            $pcod = $params['pcod'];
                        } else  {
                            $pcod = "0";
                        }
                         if (array_key_exists("history_id",$params)){
                            $history_id = $params['history_id'];
                        } else  {
                            $history_id = "0";
                        }
                        
                        $resp  = $this->OfwModel->add_menstrual_cycle_data($user_id, $name, $relationship, $start_period_day, $cycle_length, $total_period_day, $born_of_year, $tutual_cycle, $tracking_resoan, $profile_id, $history_id,$pcod);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function add_menstural_cycle_profile()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id       = $this->input->post('user_id');
            $relationship  = $this->input->post('relationship');
            $name          = $this->input->post('name');
            $age           = $this->input->post('age');
            $height        = $this->input->post('height');
            $weight        = $this->input->post('weight');
            $sleep_cycle   = $this->input->post('sleep_cycle');
            $birth_control = $this->input->post('birth_control');
            $profile_id    = $this->input->post('profile_id');
            if ($user_id == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                if (isset($_FILES["image"]) AND !empty($_FILES["image"]["name"])) {
                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                    include('s3_config.php');
                    $img_name = $_FILES['image']['name'];
                    $img_size = $_FILES['image']['size'];
                    $img_tmp  = $_FILES['image']['tmp_name'];
                    $ext      = getExtension($img_name);
                    if (strlen($img_name) > 0) {
                        if ($img_size < (50000 * 50000)) {
                            if (in_array($ext, $img_format)) {
                                $image             = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/menstural_cycle/' . $image;
                                $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                            }
                        }
                    }
                } else {
                    $image = '';
                }
                $resp = $this->OfwModel->add_menstural_cycle_profile($user_id, $relationship, $name, $age, $height, $weight, $sleep_cycle, $birth_control, $image, $profile_id);
            }
            simple_json_output($resp);
        }
    }
    
    public function update_menstural_cycle_profile()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id       = $this->input->post('user_id');
            $relationship  = $this->input->post('relationship');
            $name          = $this->input->post('name');
            $age           = $this->input->post('age');
            $height        = $this->input->post('height');
            $weight        = $this->input->post('weight');
            $sleep_cycle   = $this->input->post('sleep_cycle');
            $birth_control = $this->input->post('birth_control');
            $profile_id    = $this->input->post('profile_id');
            
            if ($user_id == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                
                $file_query = $this->db->query("SELECT image FROM menstural_cycle_profile WHERE user_id='$user_id' and id='$profile_id' limit 1");
                $get_file   = $file_query->row();
                $image      = $get_file->source;
                if (isset($_FILES["image"]) AND !empty($_FILES["image"]["name"])) {
                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                    include('s3_config.php');
                    $img_name = $_FILES['image']['name'];
                    $img_size = $_FILES['image']['size'];
                    $img_tmp  = $_FILES['image']['tmp_name'];
                    $ext      = getExtension($img_name);
                    if (strlen($img_name) > 0) {
                        if ($img_size < (50000 * 50000)) {
                            if (in_array($ext, $img_format)) {
                                if ($get_file) {
                                    $profile_pic_ = 'images/menstural_cycle/' . $image;
                                    @unlink(trim($profile_pic_));
                                    $delete_from_s3 = DeleteFromToS3($profile_pic_);
                                }
                                
                                $image             = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/menstural_cycle/' . $image;
                                $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                            }
                        }
                    }
                }
                
                
                
                $resp = $this->OfwModel->update_menstural_cycle_profile($user_id, $profile_id, $relationship, $name, $age, $height, $weight, $sleep_cycle, $birth_control, $image);
            }
            simple_json_output($resp);
        }
    }
    
    /*public function get_menstural_cycle_profile()
    {
    $this->load->model('OfwModel');
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method != 'POST') {
    json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    } else {
    $check_auth_client = $this->OfwModel->check_auth_client();
    if ($check_auth_client == true) {
    $response = $this->LoginModel->auth();
    if ($response['status'] == 200) {
    $params = json_decode(file_get_contents('php://input'), TRUE);
    if ($params['user_id'] == "") {
    $resp = array('status' => 400, 'message' => 'please enter fields');
    } else {
    $user_id = $params['user_id'];
    $resp = $this ->OfwModel->get_menstural_cycle_profile($user_id);
    }
    json_outputs($resp);
    }
    }
    }
    }*/
    
    public function get_menstural_cycle_history()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['profile_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $profile_id = $params['profile_id'];
                        $resp       = $this->OfwModel->get_menstural_cycle_history($user_id, $profile_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function get_menstural_cycle_profile()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['profile_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $profile_id = $params['profile_id'];
                        $resp       = $this->OfwModel->get_menstural_cycle_profile($user_id, $profile_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function get_menstural_cycle_reminder_list()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['profile_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $profile_id = $params['profile_id'];
                        $resp       = $this->OfwModel->get_menstural_cycle_reminder_list($user_id, $profile_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function delete_menstural_cycle_profile()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" && $params['profile_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $profile_id = $params['profile_id'];
                        $resp       = $this->OfwModel->delete_menstural_cycle_profile($user_id, $profile_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function get_menstrual_cycle_data()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
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
                        $user_id    = $params['user_id'];
                        $profile_id = $params['profile_id'];
                        $resp       = $this->OfwModel->get_menstrual_cycle_data($user_id, $profile_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function get_menstrual_cycle_data_calendar_wise1()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
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
                        $user_id    = $params['user_id'];
                        $profile_id = $params['profile_id'];
                        $resp       = $this->OfwModel->get_menstrual_cycle_data_calendar_wise1($user_id, $profile_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function get_menstrual_cycle_data_calendar_wise()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
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
                        $user_id    = $params['user_id'];
                        $profile_id = $params['profile_id'];
                        $resp       = $this->OfwModel->get_menstrual_cycle_data_calendar_wise($user_id, $profile_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function get_menstrual_cycle_data_calendar_list()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
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
                        $user_id    = $params['user_id'];
                        $profile_id = $params['profile_id'];
                        $resp       = $this->OfwModel->get_menstrual_cycle_data_calendar_list($user_id, $profile_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function delete_menstrual_cycle_data()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
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
                        $user_id    = $params['user_id'];
                        $profile_id = $params['profile_id'];
                        $resp       = $this->OfwModel->delete_menstrual_cycle_data($user_id, $profile_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function get_menstrual_cycle_category()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
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
                        $user_id    = $params['user_id'];
                        $profile_id = $params['profile_id'];
                        $resp       = $this->OfwModel->get_menstrual_cycle_category($user_id, $profile_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function update_menstural_cycle_reminder()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['profile_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id       = $params['user_id'];
                        $profile_id = $params['profile_id'];
                        $period_in  = $params['period_in'];
                        $ovulation        = $params['ovulation'];
                        $ending_of_period    = $params['ending_of_period'];
                        $starting_of_period    = $params['starting_of_period'];
                        $pms    = $params['pms'];
                        $add_pill    = $params['add_pill'];
                        $safe_period    = $params['safe_period'];
                        $resp          = $this->OfwModel->update_menstural_cycle_reminder($user_id, $profile_id, $period_in, $ovulation, $ending_of_period,$starting_of_period,$pms,$add_pill,$safe_period);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function update_cycle_category_status()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['main_category'] == "" || $params['sub_category'] == "" || $params['status'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id       = $params['user_id'];
                        $main_category = $params['main_category'];
                        $sub_category  = $params['sub_category'];
                        $status        = $params['status'];
                        $profile_id    = $params['profile_id'];
                        $resp          = $this->OfwModel->update_cycle_category_status($user_id, $main_category, $sub_category, $status, $profile_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function get_cycle_Child_category()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['sub_category'] == "" || $params['date'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id      = $params['user_id'];
                        $sub_category = $params['sub_category'];
                        $date         = $params['date'];
                        $profile_id   = $params['profile_id'];
                        $resp         = $this->OfwModel->get_cycle_Child_category($user_id, $sub_category, $date, $profile_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    public function get_cycle_Child_category_update()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['sub_category'] == "" || $params['date'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id        = $params['user_id'];
                        $sub_category   = $params['sub_category'];
                        $child_category = $params['child_category'];
                        $date           = $params['date'];
                        $status         = $params['status'];
                        $profile_id     = $params['profile_id'];
                        $resp           = $this->OfwModel->get_cycle_Child_category_update($user_id, $sub_category, $child_category, $date, $status, $profile_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    //added by zak 
    public function get_menstrual_cycle_category_date()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
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
                        $user_id    = $params['user_id'];
                        $date       = $params['date'];
                        $profile_id = $params['profile_id'];
                        $resp       = $this->OfwModel->get_menstrual_cycle_category_date($user_id, $date, $profile_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function menstrual_cycle_profile_list()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
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
                        $user_id = $params['user_id'];
                        //   $profile_id = $params['profile_id'];
                        $resp    = $this->OfwModel->menstrual_cycle_profile_list($user_id);
                    }
                    json_output(200, array(
                        'status' => 200,
                        'data' => $resp
                    ));
                }
            }
        }
    }
    
    public function menstrual_cycle_article_list()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['page'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $page    = $params['page'];
                        $resp    = $this->OfwModel->menstrual_cycle_article_list($user_id, $page);
                    }
                    json_output(200, array(
                        'status' => 200,
                        'data' => $resp
                    ));
                }
            }
        }
    }
    
    
    public function menstrual_cycle_terms()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['page'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $page    = $params['page'];
                        $resp    = $this->OfwModel->menstrual_cycle_terms($user_id, $page);
                    }
                    json_output(200, array(
                        'status' => 200,
                        'message' => 'success',
                        'data' => $resp
                    ));
                }
            }
        }
    }
    
    
    public function menstrual_cycle_tips()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['page'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $page    = $params['page'];
                        $resp    = $this->OfwModel->menstrual_cycle_tips($user_id, $page);
                    }
                    json_output(200, array(
                        'status' => 200,
                        'data' => $resp
                    ));
                }
            }
        }
    }
    
    
    
    public function menstrual_cycle_save_tips()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $tips_id = $params['id'];
                        $status  = $params['status'];
                        $resp    = $this->OfwModel->menstrual_cycle_save_tips($user_id, $tips_id, $status);
                    }
                    json_output(200, array(
                        'status' => 200,
                        'data' => $resp
                    ));
                }
            }
        }
    }
    
    
    //added for check the user has exist profile in menstrual cycle 
    
    public function menstrual_cycle_user_exist()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
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
                        $user_id = $params['user_id'];
                        $resp    = $this->OfwModel->menstrual_cycle_user_exist($user_id);
                    }
                    json_output(200, array(
                        'status' => 200,
                        'data' => $resp
                    ));
                }
            }
        }
    }
    
    public function get_cycle_all_child_category()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['profile_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $profile_id = $params['profile_id'];
                        $resp       = $this->OfwModel->get_cycle_all_child_category($user_id, $profile_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function menstural_cycle_data_yes_no()
    {
        $this->load->model('OfwModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['profile_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $profile_id = $params['profile_id'];
                        $yes_no     = $params['yes_no'];
                        $resp       = $this->OfwModel->menstural_cycle_data_yes_no($user_id, $profile_id, $yes_no);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    
/*     public function download_menstural_cycle() {
        // Load PHPMailer library 
        $this->load->model('OfwModel');
        $this->load->library('pdf_dom');
        $added_date = date('YmdHs');     
        $profile_id=$this->input->get('profile_id'); ;
        $user_id=$this->input->get('user_id'); ;
     
         	$html_content =  '<link rel="stylesheet" href="'.base_url().'assets/bootstrap.min.css">';
         	$html_content .=  '<link rel="stylesheet" href="'.base_url().'assets/style.css">';
         	$html_content .= $this->OfwModel->fetch_menstural_cycle_details($profile_id,$user_id);
       	    //$paper_size = array(0,0,660,850);
    		$this->pdf_dom->set_paper("A4", "portrait"); 
    		$this->pdf_dom->set_option('isHtml5ParserEnabled', TRUE);
    		$this->pdf_dom->load_html($html_content);
    		$this->pdf_dom->render();
        	$this->pdf_dom->stream("menstural_cycle_report_$added_date.pdf", array("Attachment"=>1));
    }
    */
    
        public function download_menstural_cycle()
    {
        $this->load->model('OfwModel');
        $this->load->library('pdf_dom');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OfwModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['profile_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $profile_id = $params['profile_id'];
          	            $html_content =  '<link rel="stylesheet" href="'.base_url().'assets/bootstrap.min.css">';
         	            $html_content .=  '<link rel="stylesheet" href="'.base_url().'assets/style.css">';
         	            $html_content .= $this->OfwModel->fetch_menstural_cycle_details($profile_id,$user_id);
       	                //$paper_size = array(0,0,660,850);
    	                $this->pdf_dom->set_paper("A4", "portrait"); 
    		            $this->pdf_dom->set_option('isHtml5ParserEnabled', TRUE);
    		            $this->pdf_dom->load_html($html_content);
    		            $this->pdf_dom->render();
        	            //$this->pdf_dom->stream("menstural_cycle_report_$added_date.pdf", array("Attachment"=>1));
                        $pdfname = 'menstrual_cycle_report_'.date('YmdHis').".pdf";
        	            $output = $this->pdf_dom->output();
                        file_put_contents('/home/h8so2sh3q97n/public_html/sandboxapi.medicalwale.com/user_pdf/'.$pdfname.'', $output);
                        $file_link ='http://sandboxapi.medicalwale.com/user_pdf/'.$pdfname;
                    }
                    simple_json_output(array('status' => 200, 'message' => 'success','report_file'=>$file_link));
                }
            }
        }
    }
    
    
    
    
    
}
