<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Survivorstory extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function story_list() {
        $this->load->model('SurvivorstoryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SurvivorstoryModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $page = $params['page'];
                        $resp = $this->SurvivorstoryModel->story_list($user_id, $page);
                    }
                   // print_r($resp);
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function article_story_list() {
        $this->load->model('SurvivorstoryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SurvivorstoryModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->SurvivorstoryModel->article_story_list($user_id);
                    }
                   // print_r($resp);
                    json_outputs($resp);
                }
            }
        }
    }

    public function story_details() {
        $this->load->model('SurvivorstoryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SurvivorstoryModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->SurvivorstoryModel->story_details($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function story_like() {
        $this->load->model('SurvivorstoryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SurvivorstoryModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['article_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $article_id = $params['article_id'];
                        $resp = $this->SurvivorstoryModel->story_like($user_id, $article_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function add_review() {
        $this->load->model('SurvivorstoryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SurvivorstoryModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['article_id'] == "" || $params['review'] == "" || $params['service'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $article_id = $params['article_id'];
                        $review = $params['review'];
                        $service = $params['service'];
                        $resp = $this->SurvivorstoryModel->add_review($user_id, $article_id, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_list() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->ArticleModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->ArticleModel->review_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function story_review_likes() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->ArticleModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->ArticleModel->article_review_likes($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function story_views() {
        $this->load->model('SurvivorstoryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SurvivorstoryModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['article_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $article_id = $params['article_id'];
                        $resp = $this->SurvivorstoryModel->story_views($user_id, $article_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function story_bookmark() {
        $this->load->model('SurvivorstoryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SurvivorstoryModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['article_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $article_id = $params['article_id'];
                        $resp = $this->SurvivorstoryModel->story_bookmark($user_id, $article_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function related_story_list() {
        $this->load->model('SurvivorstoryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SurvivorstoryModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['article_id'] == "" || $params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $article_id = $params['article_id'];
                        $user_id = $params['user_id'];
                        $resp = $this->SurvivorstoryModel->related_story_list($article_id, $user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function story_follow() {
        $this->load->model('SurvivorstoryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SurvivorstoryModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->SurvivorstoryModel->story_follow($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function bookmark() {
        $this->load->model('SurvivorstoryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SurvivorstoryModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['survival_stories_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $survival_stories_id = $params['survival_stories_id'];
                        $resp = $this->SurvivorstoryModel->bookmark($user_id, $survival_stories_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function story_bookmark_list() {
        $this->load->model('SurvivorstoryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SurvivorstoryModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->SurvivorstoryModel->story_bookmark_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function view_appointments() {
        
        $this->load->model('SurvivorstoryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SurvivorstoryModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if (  $params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                      
                        
                        $resp = $this->SurvivorstoryModel->view_appointments_module($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
     public function random_number_generation() {
        $this->load->model('SurvivorstoryModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SurvivorstoryModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->SurvivorstoryModel->random_number_generation($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

}
