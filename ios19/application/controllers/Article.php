<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Article extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function article_list() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $category_id = $params['category_id'];
                        $user_id = $params['user_id'];
                        $page = $params['page'];
                        $resp = $this->ArticleModel->article_list($category_id, $user_id, $page);
                    }
                     //print_r($resp);
                   // json_outputs($resp);
                    json_output(200, array('status' => 200, 'data' => $resp));
                }
            }
        }
    }
    
       public function article_list_new() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                       
                        $user_id = $params['user_id'];
                        $page = $params['page'];
                        $type = $params['type'];
                        $sort = $params['sort'];
                        if(array_key_exists("cat",$params)){
                            $sub_type = $params['cat'];
                        } else {
                            $sub_type = '';
                        }
                        
                        $resp = $this->ArticleModel->article_list_new($user_id, $page,$type,$sub_type,$sort);
                    }
                     //print_r($resp);
                   // json_outputs($resp);
                    json_output(200, array('status' => 200, 'data' => $resp));
                }
            }
        }
    }
    
    public function article_sub_cat() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                       
                        $user_id = $params['user_id'];
                      
                        $type = $params['type'];
                        $resp = $this->ArticleModel->article_sub_cat($user_id,$type);
                    }
                     //print_r($resp);
                   // json_outputs($resp);
                    json_output(200, array('status' => 200, 'data' => $resp));
                }
            }
        }
    }
    
    //added by zak for article list by category
      public function article_list_by_keyword() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $category_id = $params['category_id'];
                        $user_id = $params['user_id'];
                        $page = $params['page'];
                        $keyword = $params['keyword'];
                        $resp = $this->ArticleModel->article_list_by_keyword($category_id, $user_id, $page, $keyword);
                    }
                     //print_r($resp);
                   // json_outputs($resp);
                    json_output(200, array('status' => 200, 'data' => $resp));
                }
            }
        }
    }
    
    public function blog_article_list() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->ArticleModel->blog_article_list($user_id);
                    }
                   
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function article_detail() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->ArticleModel->article_detail($user_id, $post_id);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }

    public function article_details() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->ArticleModel->article_details($user_id, $post_id);
                    }
                  
                    json_outputs($resp);
                }
            }
        }
    }

    public function article_like() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['article_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $article_id = $params['article_id'];
                        $resp = $this->ArticleModel->article_like($user_id, $article_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function add_review() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
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
                        $resp = $this->ArticleModel->add_review($user_id, $article_id, $review, $service);
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
                $response = $this->LoginModel->auth();
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

    public function article_review_likes() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
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

    public function article_views() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['article_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $article_id = $params['article_id'];
                        $resp = $this->ArticleModel->article_views($user_id, $article_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function article_bookmark() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['article_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $article_id = $params['article_id'];
                        $resp = $this->ArticleModel->article_bookmark($user_id, $article_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function related_article_list() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['article_id'] == "" || $params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $article_id = $params['article_id'];
                        $category_id = $params['category_id'];
                     //   echo $category_id;
                       // echo "fgfdgfdgfd";
                        $user_id = $params['user_id'];
                        $resp = $this->ArticleModel->related_article_list($article_id, $category_id, $user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function article_follow() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->ArticleModel->article_follow($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function your_story() {
        $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            $title = $this->input->post('title');
            $description = $this->input->post('description');

            if ($user_id == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields1'
                );
            } else {
                $post_id = $this->ArticleModel->your_story($user_id, $title, $description);
                if ($post_id != '') {
                    $image = ""; //Added by swapnali on 20th nov
                    if (!empty($_FILES["image"]["name"])) {
                        $image = count($_FILES['image']['name']);
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        $video_format = array("mp4", "avi", "flv", "wmv", "mov", "3gp", "MP4", "AVI", "FLV", "WMV", "MOV", "3GP");
                        include('s3_config.php');
                        $date = date('Y-m-d H:i:s');
                        date_default_timezone_set('Asia/Calcutta');
                    }
                    if ($image > 0) {
                        $flag = '1';
                        $video_flag = '1';
                        foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                            $img_name = $key . $_FILES['image']['name'][$key];
                            $img_size = $_FILES['image']['size'][$key];
                            $img_tmp = $_FILES['image']['tmp_name'][$key];
                            $ext = getExtension($img_name);
                            if (strlen($img_name) > 0) {
                                if ($img_size < (50000 * 50000)) {
                                    if (in_array($ext, $img_format)) {
                                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                        $actual_image_path = 'images/article_images/image/' . $actual_image_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                            $this->db->query("INSERT INTO `your_story_media`(`post_id`, `type`,`source`, `created_at`) VALUES ('$post_id', 'image','$actual_image_name', '$date')");
                                        }
                                    }
                                    if (in_array($ext, $video_format)) {
                                        $uniqid = uniqid() . date("YmdHis");
                                        $actual_video_name = $uniqid . "." . $ext;
                                        $actual_video_path = 'images/article_images/video/' . $actual_video_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_video_path, S3::ACL_PUBLIC_READ)) {
                                            $this->db->query("INSERT INTO `your_story_media`(`post_id`, `type`,`source`, `created_at`) VALUES ('$post_id', 'video','$actual_video_name', '$date')");
                                        }
                                    }
                                }
                            }
                        }
                    }
                    simple_json_output(array('status' => 200, 'message' => 'success'));
                } else {
                    simple_json_output(array("status" => 201, "message" => "fail"));
                }
            }
        }
    }
    
   
    public function article_select() 
      { $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $params['user_id'];
                        
                        $resp = $this->ArticleModel->article_select($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function surgery_details() 
      { $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $params['user_id'];
                        $surgey_id = $params['surgey_id'];
                        
                        $resp = $this->ArticleModel->surgery_details($user_id,$surgey_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
     public function update_surgery() 
      { $this->load->model('ArticleModel');
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ArticleModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $params['user_id'];
                      
                        
                        $resp = $this->ArticleModel->update_surgery($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    

}
