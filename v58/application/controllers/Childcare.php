<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Childcare extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
        /*
          $check_auth_client = $this->ChildCareModel->check_auth_client();
          if($check_auth_client != true){
          die($this->output->get_output());
          }
         */
    }

    public function index() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->ChildcareModel->baby_vaccination_tracker_all_data();
                    json_outputs($resp);
                }
            }
        }
    }

    public function baby_vaccination_tracker_all_data() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->ChildcareModel->baby_vaccination_tracker_all_data();
                    json_outputs($resp);
                }
            }
        }
    }

    public function childprofile_list() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->ChildcareModel->childprofile_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function child_mydiary() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['child_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $child_id = $params['child_id'];
                        $resp = $this->ChildcareModel->child_mydiary($child_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function childprofile() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {

            $name = $this->input->post('name');
            $gender = $this->input->post('gender');
            $dob = $this->input->post('dob');
            $birth_place = $this->input->post('birth_place');
            $parent = $this->input->post('parent');
            $user_id = $this->input->post('user_id');
            
            
            $blood_group = $this->input->post('blood_group');
            $current_height = $this->input->post('current_height');
            $current_weight = $this->input->post('current_weight');
            $height = $this->input->post('height');
            $weight = $this->input->post('weight');
            $medical_condition = $this->input->post('medical_condition');
            $allergy = $this->input->post('allergy');
            $hereditary_problem = $this->input->post('hereditary_problem');
            $diet = $this->input->post('diet');
            $active_level = $this->input->post('active_level');
            $question = $this->input->post('question');        
            
            if ($name == "" || $gender == "" || $dob == "" || $birth_place == "" || $parent == "" || $user_id == "") {
                
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {

                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                    include('s3_config.php');

                    $img_name = $_FILES['image']['name'];
                    $img_size = $_FILES['image']['size'];
                    $img_tmp = $_FILES['image']['tmp_name'];
                    $ext = getExtension($img_name);

                    if (strlen($img_name) > 0) {
                        if ($img_size < (50000 * 50000)) {
                            if (in_array($ext, $img_format)) {
                                $image = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/child_care_images/image/' . $image;
                                $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                            }
                        }
                    }
                } else {

                    $image = '';
                }


                $resp = $this->ChildcareModel->childprofile($name, $gender, $dob, $birth_place, $parent, $image, $user_id,$blood_group,$height,$current_height,$active_level,$diet,$weight,$current_weight,$medical_condition,$allergy,$hereditary_problem,$question);
            }

            simple_json_output($resp);
        }
    }
    
     public function childprofile_web() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {

            $name = $this->input->post('name');
            $gender = $this->input->post('gender');
            $dob = $this->input->post('dob');
            $birth_place = $this->input->post('birth_place');
            $parent = $this->input->post('parent');
            $user_id = $this->input->post('user_id');
            
            
            $blood_group = $this->input->post('blood_group');
            $current_height = $this->input->post('current_height');
            $current_weight = $this->input->post('current_weight');
            $height = $this->input->post('height');
            $weight = $this->input->post('weight');
            $medical_condition = $this->input->post('medical_condition');
            $allergy = $this->input->post('allergy');
            $hereditary_problem = $this->input->post('hereditary_problem');
            $diet = $this->input->post('diet');
            $active_level = $this->input->post('active_level');
            $question = $this->input->post('question');
            $question = $this->input->post('question'); 
            $image=$this->input->post('image'); 
            if ($name == "" || $gender == "" || $dob == "" || $birth_place == "" || $parent == "" || $user_id == "") {
                
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                if (empty($image)) {

                    $image = '';
                
                }


                $resp = $this->ChildcareModel->childprofile($name, $gender, $dob, $birth_place, $parent, $image, $user_id,$blood_group,$height,$current_height,$active_level,$diet,$weight,$current_weight,$medical_condition,$allergy,$hereditary_problem,$question);
            }

            simple_json_output($resp);
        }
    }


    public function childprofile_update() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {

            $user_id = $this->input->post('user_id');
            $gender = $this->input->post('gender');
            $dob = $this->input->post('dob');
            $name = $this->input->post('name');
            $birth_place = $this->input->post('birth_place');
            $parent = $this->input->post('parent');
            $childprofile_id = $this->input->post('childprofile_id');
            
            
              $blood_group = $this->input->post('blood_group');
               $current_height = $this->input->post('current_height');
            $current_weight = $this->input->post('current_weight');
            $height = $this->input->post('height');
            $weight = $this->input->post('weight');
            $medical_condition = $this->input->post('medical_condition');
            $allergy = $this->input->post('allergy');
            $hereditary_problem = $this->input->post('hereditary_problem');
            $diet = $this->input->post('diet');
            $active_level = $this->input->post('active_level');
            $question = $this->input->post('question');

            if ($user_id == "" || $dob == "" || $gender == "" || $name == "" || $birth_place == "" || $parent == "" || $childprofile_id == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {

                    //unlink images
                    $file_query = $this->db->query("SELECT * FROM `child_mydiary` WHERE `user_id`='$user_id' AND `id`='$childprofile_id' ");
                    $get_file = $file_query->row();

                    if ($get_file) {
                        $image = $get_file->source;
                        $media_name = "images/child_care_images/image/" . $image;
                        @unlink(trim($media_name));
                        $delete_from_s3 = DeleteFromToS3($media_name);
                    }
                    //unlink images ends



                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                    include('s3_config.php');

                    $img_name = $_FILES['image']['name'];
                    $img_size = $_FILES['image']['size'];
                    $img_tmp = $_FILES['image']['tmp_name'];
                    $ext = getExtension($img_name);

                    if (strlen($img_name) > 0) {
                        if ($img_size < (50000 * 50000)) {
                            if (in_array($ext, $img_format)) {
                                $image = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/child_care_images/image/' . $image;
                                $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                            }
                        }
                    }
                } else {

                    $image = '';
                }


                $resp = $this->ChildcareModel->childprofile_update($name, $gender, $dob, $birth_place, $parent, $image, $user_id, $childprofile_id,$blood_group,$height,$current_height,$active_level,$diet,$weight,$current_weight,$medical_condition,$allergy,$hereditary_problem,$question);
            }

            simple_json_output($resp);
        }
    }



public function childprofile_update_web() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {

            $user_id = $this->input->post('user_id');
            $gender = $this->input->post('gender');
            $dob = $this->input->post('dob');
            $name = $this->input->post('name');
            $birth_place = $this->input->post('birth_place');
            $parent = $this->input->post('parent');
            $childprofile_id = $this->input->post('childprofile_id');
            
            
            $blood_group = $this->input->post('blood_group');
            $current_height = $this->input->post('current_height');
            $current_weight = $this->input->post('current_weight');
            $height = $this->input->post('height');
            $weight = $this->input->post('weight');
            $medical_condition = $this->input->post('medical_condition');
            $allergy = $this->input->post('allergy');
            $hereditary_problem = $this->input->post('hereditary_problem');
            $diet = $this->input->post('diet');
            $active_level = $this->input->post('active_level');
            $question = $this->input->post('question');
            $image=$this->input->post('image'); 

            if ($user_id == "" || $dob == "" || $gender == "" || $name == "" || $birth_place == "" || $parent == "" || $childprofile_id == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                if (! empty($image)) {

                    //unlink images
                    $file_query = $this->db->query("SELECT * FROM `child_mydiary` WHERE `user_id`='$user_id' AND `id`='$childprofile_id' ");
                    $get_file = $file_query->row();

                    if ($get_file) {
                        $image = $get_file->source;
                        $media_name = "images/child_care_images/image/" . $image;
                        @unlink(trim($media_name));
                        $delete_from_s3 = DeleteFromToS3($media_name);
                    }
                    //unlink images ends
                        $image=$image;
                } else {

                    $image = '';
                }


                $resp = $this->ChildcareModel->childprofile_update($name, $gender, $dob, $birth_place, $parent, $image, $user_id, $childprofile_id,$blood_group,$height,$current_height,$active_level,$diet,$weight,$current_weight,$medical_condition,$allergy,$hereditary_problem,$question);
            }

            simple_json_output($resp);
        }
    }


    public function childprofile_delete() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);

                    if ($params['user_id'] == "" || $params['childprofile_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                        $childprofile_id = $params['childprofile_id'];
                        $resp = $this->ChildcareModel->childprofile_delete($user_id, $childprofile_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function child_mydiary_delete() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);

                    if ($params['user_id'] == "" || $params['childdiary_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {

                        $user_id = $params['user_id'];
                        $childdiary_id = $params['childdiary_id'];
                        $resp = $this->ChildcareModel->child_mydiary_delete($user_id, $childdiary_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function child_mydiary_album() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                $respStatus = $response['status'];
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                    $child_id = $params['child_id'];
                    if ($params['user_id'] == "" || $params['child_id'] == "") {
                        $respStatus = 400;
                        $resp = array('status' => 400, 'message' => 'failure');
                    } else {
                        $resp = $this->ChildcareModel->child_mydiary_album($child_id, $user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function childprofile_cover_list() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {

                    $resp = $this->ChildcareModel->childprofile_cover_list();
                    json_outputs($resp);
                }
            }
        }
    }

    public function childprofile_cover_update() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {

            $user_id = $this->input->post('user_id');
            $childprofile_id = $this->input->post('childprofile_id');
            $cover_id = $this->input->post('cover_id');



            if ($user_id == "" || $childprofile_id == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                if (isset($_FILES["cover_image"]) AND ! empty($_FILES["cover_image"]["name"])) {

                    //unlink images

                    $file_query = $this->db->query("SELECT * FROM `childprofile_cover` WHERE id='$cover_id' AND type!='system'");
                    $get_file = $file_query->row();
                    if ($get_file) {
                        $image = $get_file->image;
                        $media_name = "images/child_care_images/cover/" . $image;
                        @unlink(trim($media_name));
                        $delete_from_s3 = DeleteFromToS3($media_name);
                    }
                    //unlink images ends


                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                    include('s3_config.php');

                    $img_name = $_FILES['cover_image']['name'];
                    $img_size = $_FILES['cover_image']['size'];
                    $img_tmp = $_FILES['cover_image']['tmp_name'];
                    $ext = getExtension($img_name);

                    if (strlen($img_name) > 0) {
                        if ($img_size < (50000 * 50000)) {
                            if (in_array($ext, $img_format)) {
                                $cover_image = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/child_care_images/cover/' . $cover_image;
                                $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                            }
                        }
                    }
                } else {
                    $cover_image = '';
                }


                $resp = $this->ChildcareModel->childprofile_cover_update($user_id, $childprofile_id, $cover_id, $cover_image);
            }

            simple_json_output($resp);
        }
    }

    public function babysitter_list() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['latitude'] == "" || $params['longitude'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $resp = $this->ChildcareModel->babysitter_list($user_id, $latitude, $longitude);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function babysitter_add_review() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['rating'] == "" || $params['review'] == "" || $params['service'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $rating = $params['rating'];
                        $review = $params['review'];
                        $service = $params['service'];
                        $resp = $this->ChildcareModel->babysitter_add_review($user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function babysitter_review_list() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->ChildcareModel->babysitter_review_list($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function babysitter_review_with_comment() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->ChildcareModel->babysitter_review_with_comment($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function babysitter_review_like() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->ChildcareModel->babysitter_review_like($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function babysitter_review_comment() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $comment = $params['comment'];
                        $resp = $this->ChildcareModel->babysitter_review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function babysitter_review_comment_like() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->ChildcareModel->babysitter_review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function babysitter_review_comment_list() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->ChildcareModel->babysitter_review_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function dai_nanny_list() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['latitude'] == "" || $params['longitude'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $resp = $this->ChildcareModel->dai_nanny_list($user_id, $latitude, $longitude);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function dai_nanny_add_review() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['rating'] == "" || $params['review'] == "" || $params['service'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $rating = $params['rating'];
                        $review = $params['review'];
                        $service = $params['service'];
                        $resp = $this->ChildcareModel->dai_nanny_add_review($user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function dai_nanny_review_list() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->ChildcareModel->dai_nanny_review_list($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
        public function dai_nanny_review_with_comment() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->ChildcareModel->dai_nanny_review_with_comment($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    

    public function dai_nanny_review_like() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->ChildcareModel->dai_nanny_review_like($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function dai_nanny_review_comment() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $comment = $params['comment'];
                        $resp = $this->ChildcareModel->dai_nanny_review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function dai_nanny_review_comment_like() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->ChildcareModel->dai_nanny_review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function dai_nanny_review_comment_list() {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->ChildcareModel->dai_nanny_review_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function question_list()
    {
        $this->load->model('ChildcareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->ChildcareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->ChildcareModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $child_id     = $params['child_id'];
                        $resp        = $this->ChildcareModel->question_list($user_id,$child_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
}
