<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Healthwall extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }

    public function hash_tag() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->HealthwallModel->hash_tag();
                    json_outputs($resp);
                }
            }
        }
    }

    public function healthwall_category() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->HealthwallModel->healthwall_category();
                    json_outputs($resp);
                }
            }
        }
    }

    public function healthwall_doctor_category() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->HealthwallModel->healthwall_doctor_category();
                    json_outputs($resp);
                }
            }
        }
    }

    public function add_post() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $image = '0';
            $article_title = '';
            $article_image = '';
            $article_domain_name = '';
            $user_id = $this->input->post('user_id');
            $tag = $this->input->post('tag');
            $category = $this->input->post('category');
            $post = $this->input->post('post');
            $type = $this->input->post('type');
            $is_anonymous = $this->input->post('is_anonymous');
            $caption = $this->input->post('caption');
            $article_title = $this->input->post('article_title');
            $article_image = $this->input->post('article_image');
            $article_domain_name = $this->input->post('article_domain_name');
            if ($user_id == "" && $is_anonymous == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                $post_id = $this->HealthwallModel->add_post($user_id, $tag, $category, $post, $type, $is_anonymous, $caption, $article_title, $article_image, $article_domain_name);
                if ($post_id != '') {
                    if (!empty($_FILES["image"]["name"])) {
                        $image = count($_FILES['image']['name']);
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        $video_format = array("mp4", "avi", "flv", "wmv", "mov", "MP4", "AVI", "FLV", "WMV", "MOV");
                        include('s3_config.php');
                        date_default_timezone_set('Asia/Calcutta');
                        $date = date('Y-m-d H:i:s');
                    }
                    if ($image > 0) {
                        $flag = '1';
                        $video_flag = '1';
                        $i = 0;
                        foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                            $img_name = $key . $_FILES['image']['name'][$key];
                            $img_size = $_FILES['image']['size'][$key];
                            $img_tmp = $_FILES['image']['tmp_name'][$key];
                            $ext = getExtension($img_name);
                            if (strlen($img_name) > 0) {
                                if ($img_size < (50000 * 50000)) {
                                    if (in_array($ext, $img_format)) {
                                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                        $actual_image_path = 'images/healthwall_media/image/' . $actual_image_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                            if ($flag > 0) {
                                                $img_url = 'https://medicalwale.s3.amazonaws.com/images/healthwall_media/image/' . $actual_image_name;
                                                $imagedetails = getimagesize($img_url);
                                                $widths = $imagedetails[0];
                                                $heights = $imagedetails[1];
                                                $flag = '0';
                                            }
                                            $this->db->query("INSERT INTO `media`(`caption`,`title`, `type`, `source`, `created_at`, `updated_at`) VALUES ('$caption[$i]','$actual_image_name', 'image', '$actual_image_name', '$date','$date')");
                                            $media_id = $this->db->insert_id();
                                            $this->db->query("INSERT INTO `post_media`(`post_id`, `media_id`, `created_at`, `updated_at`, `img_width`, `img_height`) VALUES ('$post_id', '$media_id', '$date', '$date', '$widths', '$heights')");
                                        }
                                    }
                                    if (in_array($ext, $video_format)) {
                                        $uniqid = uniqid() . date("YmdHis");
                                        $actual_video_name = $uniqid . "." . $ext;
                                        $actual_video_path = 'images/healthwall_media/video/' . $actual_video_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_video_path, S3::ACL_PUBLIC_READ)) {
                                            $video_url = 'https://medicalwale.s3.amazonaws.com/images/healthwall_media/video/' . $actual_video_name;
                                            $thumb_size = $_FILES['thumbnail']['size'][$key];
                                            $thumb_tmp = $_FILES['thumbnail']['tmp_name'][$key];
                                            if ($thumb_size > 0) {
                                                $actual_thumbnail_name = $uniqid . ".jpg";
                                                $actual_thumbnail_path = 'images/healthwall_media/thumb/' . $actual_thumbnail_name;
                                                $s3->putObjectFile($thumb_tmp, $bucket, $actual_thumbnail_path, S3::ACL_PUBLIC_READ);
                                            }
                                            $thumb_url = 'https://medicalwale.s3.amazonaws.com/images/healthwall_media/thumb/' . $actual_thumbnail_name;
                                            $video_width = '0';
                                            $video_height = '0';
                                            if ($video_flag > 0) {
                                                $videodetails = getimagesize($thumb_url);
                                                $video_width = $videodetails[0];
                                                $video_height = $videodetails[1];
                                                $video_flag = '0';
                                            }
                                            $this->db->query("INSERT INTO `media`(`caption`,`title`, `type`, `source`, `created_at`, `updated_at`) VALUES ('$caption[$i]','$actual_video_name', 'video', '$actual_video_name', '$date','$date')");
                                            $media_id = $this->db->insert_id();
                                            $this->db->query("INSERT INTO `post_media`(`post_id`, `media_id`, `created_at`, `updated_at`, `video_width`, `video_height`) VALUES ('$post_id', '$media_id', '$date', '$date', '$video_width', '$video_height')");
                                        }
                                    }
                                }
                            }
                            $i++;
                        }
                    }
                    simple_json_output(array("status" => 200, "message" => "success"));
                } else {
                    simple_json_output(array("status" => 201, "message" => "fail"));
                }
            }
        }
    }

    public function post_list() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['activity_user_id'] == "" || $params['healthwall_category'] == "" || $params['page'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'User Id can\'t empty'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $page = $params['page'];
                        $activity_user_id = $params['activity_user_id'];
                        $healthwall_category = $params['healthwall_category'];
                        $resp = $this->HealthwallModel->post_list($user_id, $activity_user_id, $healthwall_category, $page);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }
    
    public function post_list1() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['activity_user_id'] == "" || $params['healthwall_category'] == "" || $params['page'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'User Id can\'t empty'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $page = $params['page'];
                        $activity_user_id = $params['activity_user_id'];
                        $healthwall_category = $params['healthwall_category'];
                        $resp = $this->HealthwallModel->post_list1($user_id, $activity_user_id, $healthwall_category, $page);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }
    
    
    public function single_post_list() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'User Id can\'t empty'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->HealthwallModel->single_post_list($user_id, $post_id);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }

    public function post_like() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
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
                        $post_user_id = $params['post_user_id'];
                        $resp = $this->HealthwallModel->post_like($user_id, $post_id, $post_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function follow_post() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
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
                        $resp = $this->HealthwallModel->follow_post($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function post_video_views() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
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
                        $resp = $this->HealthwallModel->post_video_views($user_id, $media_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function post_views() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
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
                        $resp = $this->HealthwallModel->post_views($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function post_comment() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $comment = $params['comment'];
                        $post_user_id = $params['post_user_id'];
                        $resp = $this->HealthwallModel->post_comment($user_id, $post_id, $comment, $post_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function post_comment_reply() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "" || $params['comment'] == "") {
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
                        $resp = $this->HealthwallModel->post_comment_reply($user_id, $post_id, $comment_id, $comment, $post_user_id, $comment_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function comment_like() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
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
                        $post_id = $params['post_id'];
                        $comment_user_id = $params['comment_user_id'];
                        $resp = $this->HealthwallModel->comment_like($user_id, $comment_id, $post_id, $comment_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function comment_reply_like() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_reply_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $comment_reply_id = $params['comment_reply_id'];
                        $post_id = $params['post_id'];
                        $comment_reply_user_id = $params['comment_reply_user_id'];
                        $resp = $this->HealthwallModel->comment_reply_like($user_id, $post_id, $comment_reply_id, $comment_reply_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function comment_list() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
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
                        $resp = $this->HealthwallModel->comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function comment_reply_list() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->HealthwallModel->comment_list($user_id, $post_id, $comment_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function post_hide() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
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
                        $resp = $this->HealthwallModel->post_hide($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function post_delete() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
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
                        $repost_user_id = $params['repost_user_id'];
                        $resp = $this->HealthwallModel->post_delete($user_id, $post_id, $repost_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function repost() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
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
                        $repost_user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $repost_location = $params['repost_location'];
                        $resp = $this->HealthwallModel->repost($repost_user_id, $post_id, $repost_location);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    

    
       public function healthwall_post_comment_all_reply_list() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['comment_id'] == "" || $params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $comment_id = $params['comment_id'];
                        $user_id = $params['user_id'];
                        $resp = $this->HealthwallModel->healthwall_post_comment_all_reply_list($comment_id, $user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function vendor_list() {
        $this->load->model('HealthwallModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->HealthwallModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['lat'] == "" || $params['lng'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $vendor_id = $params['vendor_id'];
                        $lat = $params['lat'];
                        $lng = $params['lng'];
                        $resp = $this->HealthwallModel->vendor_list($vendor_id,$lat, $lng);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

}
