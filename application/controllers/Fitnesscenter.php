<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fitnesscenter extends CI_Controller
{    
    public function __construct()
    {
        parent::__construct();
    }    
	
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }  
	
    public function fitness_center_list()
    {
        $this->load->model('FitnesscenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->FitnesscenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->FitnesscenterModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['latitude'] == "" || $params['longitude'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $latitude  = $params['latitude'];
                        $longitude = $params['longitude'];
                        $category  = $params['category'];
                        $resp      = $this->FitnesscenterModel->fitness_center_list($user_id, $latitude, $longitude, $category);
                    }
                    json_outputs($resp);
                }
            }
        }
    }    
    
    
        public function fitness_center_other_branch()
    {
        $this->load->model('FitnesscenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->FitnesscenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->FitnesscenterModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['category_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $listing_id = $params['listing_id']; 
                        $category_id = $params['category_id'];
                        $branch_id = $params['branch_id'];
                        $resp       = $this->FitnesscenterModel->fitness_center_other_branch($user_id,$listing_id,$category_id,$branch_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function fitness_center_details()
    {
        $this->load->model('FitnesscenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->FitnesscenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->FitnesscenterModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $listing_id = $params['listing_id']; 
                        $resp       = $this->FitnesscenterModel->fitness_center_details($user_id,$listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
 
    public function add_bookings()
    {
        $this->load->model('FitnesscenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->FitnesscenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->FitnesscenterModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['package_id'] == "" || $params['branch_id'] == "" || $params['category_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id        = $params['user_id'];
                        $listing_id     = $params['listing_id'];
                        $package_id     = $params['package_id'];
                        $branch_id      = $params['branch_id'];
                        $category_id    = $params['category_id']; 
                        $trail_booking_date = $params['trail_booking_date'];   
                        $trail_booking_time = $params['trail_booking_time']; 
                        $joining_date   = $params['joining_date'];   
                        $user_name      = $params['user_name'];  
                        $user_mobile    = $params['user_mobile'];    
                        $user_email     = $params['user_email'];    
                        $user_gender    = $params['user_gender'];  
                        $resp = $this->FitnesscenterModel->add_bookings($user_id, $listing_id, $package_id, $branch_id, $category_id,$trail_booking_date,$trail_booking_time,$joining_date,$user_name,$user_mobile,$user_email,$user_gender);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    
    
    public function add_review()
    {
        $this->load->model('FitnesscenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->FitnesscenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->FitnesscenterModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['rating'] == "" || $params['review'] == "" || $params['service'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $listing_id = $params['listing_id']; 
                        $rating     = $params['rating'];
                        $review     = $params['review'];
                        $service    = $params['service'];  
                        $branch_id  = $params['branch_id'];
                        $resp       = $this->FitnesscenterModel->add_review($user_id, $listing_id, $rating, $review, $service,$branch_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function review_list()
    {
        $this->load->model('FitnesscenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->FitnesscenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->FitnesscenterModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['branch_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $branch_id = $params['branch_id'];
                        $resp       = $this->FitnesscenterModel->review_list($user_id, $listing_id, $branch_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function review_like()
    {
        $this->load->model('FitnesscenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->FitnesscenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->FitnesscenterModel->auth();
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
                        $resp    = $this->FitnesscenterModel->review_like($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function review_comment()
    {
        $this->load->model('FitnesscenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->FitnesscenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->FitnesscenterModel->auth();
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
                        $resp    = $this->FitnesscenterModel->review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function review_comment_like()
    {
        $this->load->model('FitnesscenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->FitnesscenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->FitnesscenterModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp       = $this->FitnesscenterModel->review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function review_comment_list()
    {
        $this->load->model('FitnesscenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->FitnesscenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->FitnesscenterModel->auth();
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
                        $resp    = $this->FitnesscenterModel->review_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
               
                        
    public function fitness_views()
    {
        $this->load->model('FitnesscenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->FitnesscenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->FitnesscenterModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp    = $this->FitnesscenterModel->fitness_views($user_id, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    
}