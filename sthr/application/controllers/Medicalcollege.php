<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Medicalcollege extends CI_Controller {

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

    public function category_list() {
        $this->load->model('MedicalcollegeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->MedicalcollegeModel->college_cat_list();
                    json_outputs($resp);
                }
            }
        }
    }

    // public function college_list() {
    //     $this->load->model('MedicalcollegeModel');
    //     $method = $_SERVER['REQUEST_METHOD'];
    //     if ($method != 'POST') {
    //         json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    //     } else {
    //         $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
    //         if ($check_auth_client == true) {
    //             $response = $this->LoginModel->auth();
    //             if ($response['status'] == 200) {
    //                 $params = json_decode(file_get_contents('php://input'), TRUE);
    //                 if ($params['category_id'] == "") {
    //                     $resp = array('status' => 400, 'message' => 'please select Category');
    //                 } else {
    //                     $category_id = $params['category_id'];

    //                     $resp = $this->MedicalcollegeModel->college_list($category_id);
    //                 }
    //                 json_outputs($resp);
    //             }
    //         }
    //     }
    // }


 public function college_list() {
        $this->load->model('MedicalcollegeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['category_id'] == ""  || $params['latitude'] == "" || $params['longitude']== "") {
                        $resp = array('status' => 400, 'message' => 'please select Category');
                    } else {
                        $category_id = $params['category_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $resp = $this->MedicalcollegeModel->college_list($category_id,$latitude,$longitude);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

//added by zak for college searching by lat long ,name , college type / cources 

  public function college_list_filter() {
        $this->load->model('MedicalcollegeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if($params['search_type'] == 'location')
                     {    
                    if ($params['category_id'] == ""  || $params['latitude'] == "" || $params['longitude']== "") {
                         $resp = array('status' => 400, 'message' => 'please select Category');
                     }
                     else if($params['search_type'] == 'name')
                     {
                         if ($params['category_id'] == "" || $params['keyword'] == "") 
                         {
                         $resp = array('status' => 400, 'message' => 'please select Category');
                          }
                     }
                     else if($params['search_type'] == 'streams')
                     {
                         if ($params['category_id'] == "" || $params['keyword'] == "") 
                         {
                         $resp = array('status' => 400, 'message' => 'please select Category');
                          }
                     }
                     else
                     {
                         if ($params['category_id'] == ""  || $params['latitude'] == "" || $params['longitude']== "") 
                         {
                         $resp = array('status' => 400, 'message' => 'please select Category');
                          }
                     }
                    } else {
                        $category_id = $params['category_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $search_type = $params['search_type'];
                        $keyword = $params['keyword'];
                        $resp = $this->MedicalcollegeModel->college_list_filter($category_id,$latitude,$longitude,$search_type,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }


    public function college_details() {
        $this->load->model('MedicalcollegeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['college_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please select Category');
                    } else {
                        $college_id = $params['college_id'];
                        $user_id = $params['user_id'];
                       
                        $resp = $this->MedicalcollegeModel->college_details($college_id, $user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function add_review() {
        $this->load->model('MedicalcollegeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
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
                        $resp = $this->MedicalcollegeModel->add_review($user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function edit_review() {
        $this->load->model('MedicalcollegeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['rating'] == "" || $params['review'] == "" || $params['service'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $review_id = $params['review_id'];
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $rating = $params['rating'];
                        $review = $params['review'];
                        $service = $params['service'];
                        $resp = $this->MedicalcollegeModel->edit_review($review_id,$user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_list() {
        $this->load->model('MedicalcollegeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->MedicalcollegeModel->review_list($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function review_with_comment() {
        $this->load->model('MedicalcollegeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->MedicalcollegeModel->review_with_comment($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    

    public function review_like() {
        $this->load->model('MedicalcollegeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->MedicalcollegeModel->review_like($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment() {
        $this->load->model('MedicalcollegeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
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
                        $resp = $this->MedicalcollegeModel->review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_like() {
        $this->load->model('MedicalcollegeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->MedicalcollegeModel->review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_list() {
        $this->load->model('MedicalcollegeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->MedicalcollegeModel->review_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function Request_broucher() {
        $this->load->model('MedicalcollegeModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->MedicalcollegeModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['medicalcol_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $college_id = $params['medicalcol_id'];
                        $resp = $this->MedicalcollegeModel->Request_broucher($user_id, $college_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
  

}
