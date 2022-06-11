<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Fitnesscenter extends CI_Controller {

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

    public function fitness_center_list() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                     
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $category = $params['category'];
                        $resp = $this->FitnesscenterModel->fitness_center_list($user_id, $latitude, $longitude, $category);
                    }
                  json_outputs($resp);
                }
            }
        }
    }

    public function fitness_center_other_branch() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['category_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $category_id = $params['category_id'];
                        $branch_id = $params['branch_id'];
                        $resp = $this->FitnesscenterModel->fitness_center_other_branch($user_id, $listing_id, $category_id, $branch_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function fitness_center_details() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->FitnesscenterModel->fitness_center_details($user_id, $listing_id);
                    }
                     simple_json_output($resp);
                }
            }
        }
    }
    
     public function fitness_center_details_v2() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->FitnesscenterModel->fitness_center_details_v2($user_id, $listing_id);
                    }
                     simple_json_output($resp);
                }
            }
        }
    }
    
    //added by zak for branches details 
    public function fitness_center_branch_details() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['branch_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $branch_id = $params['branch_id'];
                        $resp = $this->FitnesscenterModel->fitness_center_branch_details($user_id, $listing_id, $branch_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //end

    public function add_bookings() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['package_id'] == "" || $params['branch_id'] == "" || $params['category_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $package_id = $params['package_id'];
                        $branch_id = $params['branch_id'];
                        $category_id = $params['category_id'];
                        $trail_booking_date = $params['trail_booking_date'];
                        $trail_booking_time = $params['trail_booking_time'];
                        $joining_date = $params['joining_date'];
                        $user_name = $params['user_name'];
                        $user_mobile = $params['user_mobile'];
                        $user_email = $params['user_email'];
                        $user_gender = $params['user_gender'];
                        $user_age= $params['age'];
                        $user_height= $params['height'];
                        $user_weight= $params['weight'];
                        $user_diet_preference= $params['diet_preference'];
                        $user_exercise_level= $params['exercise_level'];
                        $user_medical_condition= $params['medical_condition'];
                        $user_ever_went_gym= $params['ever_went_gym'];
                        
                        
                        $resp = $this->FitnesscenterModel->add_bookings($user_id, $listing_id, $package_id, $branch_id, $category_id, $trail_booking_date, $trail_booking_time, $joining_date, $user_name, $user_mobile, $user_email, $user_gender,$user_age,$user_height,$user_weight,$user_diet_preference,$user_exercise_level,$user_medical_condition,$user_ever_went_gym);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function add_review() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['rating'] == "" || $params['review'] == "" || $params['service'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $rating = $params['rating'];
                        $review = $params['review'];
                        $service = $params['service'];
                        $branch_id = $params['branch_id'];
                        $resp = $this->FitnesscenterModel->add_review($user_id, $listing_id, $rating, $review, $service, $branch_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_list() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['branch_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $branch_id = $params['branch_id'];
                        $resp = $this->FitnesscenterModel->review_list($user_id, $listing_id, $branch_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }


  public function review_with_comment() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['branch_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $branch_id = $params['branch_id'];
                        $resp = $this->FitnesscenterModel->review_with_comment($user_id, $listing_id, $branch_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function review_like() {
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
                        $resp = $this->FitnesscenterModel->review_like($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment() {
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
                        $resp = $this->FitnesscenterModel->review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_like() {
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
                        $resp = $this->FitnesscenterModel->review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_list() {
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
                        $resp = $this->FitnesscenterModel->review_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function fitness_views() {
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
                $response = $this->LoginModel->auth();
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
                        $resp = $this->FitnesscenterModel->fitness_views($user_id, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function fitness_trainer_list() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                     
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'] ;
                     
                        //$category = $params['category'];
                        $resp = $this->FitnesscenterModel->fitness_trainer_list($user_id,$listing_id);
                    }
                  json_outputs($resp);
                }
            }
        }
    }
   // scheduled for next release 
    public function fitness_form_questions() {
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
                        $resp = $this->FitnesscenterModel->fitness_form_questions($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function user_payment_approval_v30()
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params             = json_decode(file_get_contents('php://input'), TRUE);
                    
                    $status = $params['status'];
                    $booking_id         = $params['booking_id'];
                    $user_id            = $params['user_id'];
                    if ( $params['user_id'] == "" || $params['status'] == "" || $params['booking_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->FitnesscenterModel->user_payment_approval($status, $booking_id, $user_id);
                    }
                }
                simple_json_output($resp);
            }
        }
    }
    
      public function view_bookings_v30() {
        $this->load->model('FitnesscenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->FitnesscenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['package_id'] == "" || $params['branch_id'] == "" || $params['category_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields1');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $package_id = $params['package_id'];
                        $branch_id = $params['branch_id'];
                        $category_id = $params['category_id'];
                        $resp = $this->FitnesscenterModel->view_bookings_v30($user_id, $listing_id, $package_id, $branch_id, $category_id);
                    }
                     simple_json_output($resp);
                }
            }
        }
    }
    public function add_bookings_v30() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                  //  $params = json_decode(file_get_contents('php://input'), TRUE);
                 
                    if ($this->input->post('user_id') == "" || $this->input->post('listing_id') == "" || $this->input->post('package_id') == "" || $this->input->post('branch_id') == "" || $this->input->post('category_id') == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $this->input->post('user_id');
                        $listing_id = $this->input->post('listing_id');
                        $package_id = $this->input->post('package_id');
                        $branch_id = $this->input->post('branch_id');
                        $category_id = $this->input->post('category_id');
                        $trail_booking_date = $this->input->post('trail_booking_date');
                        $trail_booking_time = $this->input->post('trail_booking_time');
                        $joining_date = $this->input->post('joining_date');
                        $user_name = $this->input->post('user_name');
                        $user_mobile = $this->input->post('user_mobile');
                        $user_email = $this->input->post('user_email');
                        $user_gender = $this->input->post('user_gender');
                        $user_age= $this->input->post('age');
                        $user_height= $this->input->post('height');
                        $height_cm_ft= $this->input->post('height_cm_ft');
                        $user_weight= $this->input->post('weight');
                        $user_diet_preference= $this->input->post('diet_preference');
                        $user_exercise_level= $this->input->post('exercise_level');
                        $user_medical_condition= $this->input->post('medical_condition');
                        $user_ever_went_gym= $this->input->post('ever_went_gym');
                        $user_bmi= $this->input->post('bmi');
                        $user_dob= $this->input->post('dob');
                        $user_blood_group= $this->input->post('blood_group');
                        
                        $answers = $this->input->post('answers');
                        $timings = json_decode($answers);
                        
                        $del = $this->FitnesscenterModel->delete_fitness_form_answers($user_id,$listing_id,$branch_id,$package_id);
                          
                        for($i=0;$i<sizeof($timings->answers);$i++){
                           $resp = $this->FitnesscenterModel->Fill_fitness_form_answers($user_id,$listing_id,$branch_id,$package_id,$timings->answers[$i]->id,$timings->answers[$i]->answer1, $timings->answers[$i]->answer2);
                          
                        }
                        
                        
              
                        $resp = $this->FitnesscenterModel->add_bookings_v30($user_id, $listing_id, $package_id, $branch_id, $category_id, $trail_booking_date, $trail_booking_time, $joining_date, $user_name, $user_mobile, $user_email, $user_gender,$user_age,$user_height,$height_cm_ft,$user_weight,$user_diet_preference,$user_exercise_level,$user_medical_condition,$user_ever_went_gym,$user_bmi,$user_dob,$user_blood_group);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function fitness_center_list_v2() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                     
                    } else {
                        $user_id = $params['user_id'];
                        $latitude = $params['latitude'];
                        $longitude = $params['longitude'];
                        $category = $params['category'];
                        if(array_key_exists("term",$params)){
                            $term = $params['term'];
                        } else {
                            $term = "";
                            
                        }
                        $resp = $this->FitnesscenterModel->fitness_center_list_v2($user_id, $latitude, $longitude, $category,$term);
                    }
                  json_outputs($resp);
                }
            }
        }
    } 

     public function fitness_center_related_list()
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == '' || $params['listing_id'] == '') {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $latitude    = $params['latitude'];
                        $longitude   = $params['longitude'];
                        $listing_id   = $params['listing_id'];
                      //$category_id = $params['category_id'];
                        $resp        = $this->FitnesscenterModel->fitness_center_related_list($user_id,$latitude,$longitude,$listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    

}
