<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Skin_hair_expert extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

 
    public function skin_hair_add_question() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['user_name'] == "" || $params['question'] == "" || $params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $type = $params['type'];
                        $for_user_id = $params['for_user_id'];
                        $user_name = $params['user_name'];
                        $user_image = $params['user_image'];
                        $question = $params['question'];
                        $age = $params['age'];
                        $height = $params['height'];
                        $weight = $params['weight'];
                        $diet_preference = $params['diet_preference'];
                        $post_location = $params['post_location'];
                        $skin_type     = $params['skin_type'];
                        $skin_color    = $params['skin_color'];
                        $skin_concern  = $params['skin_concern'];
                        $skin_concern_other = $params['skin_concern_other'];
                         $is_anonymous = $params['is_anonymous'];
                        //$tag = addslashes($_POST['tag']);
                        //$category = addslashes($_POST['category']);
                        //$post = trim(addslashes($_POST['post']));
                        //$type = addslashes($_POST['type']);
                        //$user_id = addslashes($_POST['user_id']);
                       
                        //$caption = $_POST['caption'];
                        //$post_location = $_POST['post_location'];
                       // $healthwall_category = addslashes($_POST['healthwall_category']);
                       // $article_title = $_POST['article_title'];
                        //$article_title = str_replace("'", "\'", $article_title);
                        //$article_image = addslashes($_POST['article_image']);
                        //$article_domain_name = addslashes($_POST['article_domain_name']);
                        //$article_url = addslashes($_POST['article_url']);
    
                        $resp = $this->Skin_hair_expert_model->skin_hair_add_question($type,$user_id, $for_user_id, $user_name, $user_image, $question, $age, $height, $weight, $diet_preference, $post_location, $skin_type, $skin_color, $skin_concern, $skin_concern_other, $is_anonymous);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function skin_hair_question_list() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $page = $params['page'];
                        $type = $params['type'];
                        $resp = $this->Skin_hair_expert_model->skin_hair_question_list($user_id, $type, $page);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function skin_hair_character() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $type = $params['type'];
                        $resp = $this->Skin_hair_expert_model->skin_hair_character($type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function skin_hair_fields() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $type = $params['type'];
                        $resp = $this->Skin_hair_expert_model->skin_hair_fields($type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function skin_hair_question_details() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->Skin_hair_expert_model->skin_hair_question_details($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function skin_hair_like() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['user_image'] == "" || $params['user_name'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $user_image = $params['user_image'];
                        $user_name = $params['user_name'];
                        $post_user_id = $params['post_user_id'];
                        $resp = $this->Skin_hair_expert_model->skin_hair_like($user_id, $post_id, $user_image, $user_name, $post_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
        public function skin_concern_list() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                       
                        $resp = $this->Skin_hair_expert_model->skin_concern_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function asktheexpert_list() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                       
                        $resp = $this->Skin_hair_expert_model->asktheexpert_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    public function add_comment() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['pstid'] == "" || $params['comment1'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $pstid = $params['pstid'];
                        $comment1 = $params['comment1'];
                       // $user_id = $params['user_id'];
                        
                        function encrypt($str) {
                            $key = hash('MD5', '8655328655mdwale', true);
                            $iv = hash('MD5', 'mdwale8655328655', true);
                            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
                            mcrypt_generic_init($module, $key, $iv);
                            $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
                            $pad = $block - (strlen($str) % $block);
                            $str .= str_repeat(chr($pad), $pad);
                            $encrypted = mcrypt_generic($module, $str);
                            mcrypt_generic_deinit($module);
                            mcrypt_module_close($module);
                            return base64_encode($encrypted);
                        }
    
                        $data   = array(
                            "post_id" => $pstid,
                            "answer" => encrypt($comment1),
                            //"uid" => $user_id,
                            "type"=> "Miss Belly",
                            "date" => curr_date(),
                        );
                        $resp = $this->Skin_hair_expert_model->add_comment($data,$comment1,$user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function miss_belly_add_reply() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['doctor_id'] == "" || $params['post_id'] == "" || $params['type'] == "" || $params['answer'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $doctor_id = $params['doctor_id'];
                        $post_id = $params['post_id'];
                        $type = $params['type'];
                        $answer = $params['answer'];

                        $resp = $this->Skin_hair_expert_model->miss_belly_add_reply($user_id, $doctor_id, $post_id, $type, $answer);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function miss_belly_like() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['user_image'] == "" || $params['user_name'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $user_image = $params['user_image'];
                        $user_name = $params['user_name'];
                        $post_user_id = $params['post_user_id'];
                        $resp = $this->Skin_hair_expert_model->miss_belly_like($user_id, $post_id, $user_image, $user_name, $post_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function miss_belly_user_like_list() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {

                        $post_id = $params['post_id'];

                        $resp = $this->Skin_hair_expert_model->miss_belly_user_like_list($post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function miss_belly_is_notify() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->Skin_hair_expert_model->miss_belly_is_notify($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function miss_belly_hide() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->Skin_hair_expert_model->miss_belly_hide($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function miss_belly_user_update() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['user_name'] == "" || $params['user_image'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $user_name = $params['user_name'];
                        $user_image = $params['user_image'];

                        $resp = $this->Skin_hair_expert_model->miss_belly_user_update($user_id, $user_name, $user_image);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function miss_belly_user_check() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];

                        $resp = $this->Skin_hair_expert_model->miss_belly_user_check($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function miss_belly_delete() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->Skin_hair_expert_model->miss_belly_delete($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function miss_belly_reply_delete() {
        $this->load->model('Skin_hair_expert_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Skin_hair_expert_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['answer_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $answer_id = $params['answer_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->Skin_hair_expert_model->miss_belly_reply_delete($post_id, $answer_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
}
