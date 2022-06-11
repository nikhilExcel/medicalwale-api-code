<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Nursingattendant extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function nursingattendant_list() {
        $this->load->model('NursingattendantModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->NursingattendantModel->check_auth_client();
            if ($check_auth_client == true) {
               // $response = $this->load->model('LoginModel');
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    // if ($params['user_id'] == "") {
                    //     $resp = array('status' => 400, 'message' => 'please enter fields');
                    // } else {
                        $user_id = $params['user_id'];
                        $lat = $params['lat'];
                        $lng = $params['lng'];

                        $resp = $this->NursingattendantModel->nursingattendant_list($user_id,$lat,$lng);
                   // }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function nursingattendant_appointment() {
        $this->load->model('NursingattendantModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->NursingattendantModel->check_auth_client();
            if ($check_auth_client == true) {
                //$response = $this->load->model('LoginModel');
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['booking_id'] == "" || $params['type'] == "" || $params['payment_method'] == "" || $params['amount'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id            = $params['user_id'];
                        $listing_id         = $params['listing_id'];
                        $booking_id         = $params['booking_id'];
                        $type               = $params['type'];
                        $payment_method     = $params['payment_method'];
                        $amount             = $params['amount'];
                        $resp               = $this->NursingattendantModel->nursung_appoinment($user_id,$listing_id,$booking_id,$type,$payment_method,$amount);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function nursingattendant_book_package() {
        $this->load->model('NursingattendantModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->NursingattendantModel->check_auth_client();
            if ($check_auth_client == true) {
               // $response = $this->load->model('LoginModel');
               $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    ///print_r($params);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['package_id'] == "" ||  $params['name'] == "" || $params['gender'] == "" || $params['patient_name'] == "" || $params['patient_age'] == "" || $params['patient_gender'] == "" || $params['city'] == "" || $params['attendent_time'] == "" || $params['attendant_hour'] == "" || $params['tentative_intime'] == "" || $params['tentative_outtime'] == "" || $params['nursing_gender'] == "" || $params['attendant_needed'] == "" || $params['appoinment_booking_date'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id            = $params['user_id'];
                        $listing_id         = $params['listing_id'];
                        $package_id         = $params['package_id'];
                        $name               = $params['name'];
                        $gender             = $params['gender'];
                        
                        
                        $patiente_name      = $params['patient_name'];
                        $patiente_age       = $params['patient_age'];
                        $patiente_gender    = $params['patient_gender'];
                        $city               = $params['city'];
                        $patiente_condition = $params['patient_condition'];
                        $attendent_time     = $params['attendent_time'];
                        $attendant_hour     = $params['attendant_hour'];
                        $tentative_intime   = $params['tentative_intime'];
                        $tentative_outtime  = $params['tentative_outtime'];
                        $nursing_gender     = $params['nursing_gender'];
                        $attandant_needed   = $params['attendant_needed'];
                        $appoinment_booking_date = $params['appoinment_booking_date'];
                        
                        $resp1               = $this->NursingattendantModel->nursingattendant_book_package($user_id,$listing_id,$package_id,$name,$gender, $patiente_name, $patiente_age, $patiente_gender, $city, $patiente_condition, $attendent_time, $attendant_hour, $tentative_intime, $tentative_outtime, $nursing_gender, $attandant_needed, $appoinment_booking_date);
                        if($resp1 != "")
                        {
                             json_output(200, array('status' => 200, 'message' => 'Success'));
                        }
                        else
                        {
                             //$resp = array('status' => 400, 'message' => 'Unable to Book Package');
                             json_output(400, array('status' => 400, 'message' => 'Unable to Book Package'));
                        }
                        
                    }
                    
                   
                }
            }
        }
    }
    
    public function nursingattendant_details() {
        $this->load->model('NursingattendantModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->NursingattendantModel->check_auth_client();
            if ($check_auth_client == true) {
               // $response = $this->load->model('LoginModel');
               $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->NursingattendantModel->nursingattendant_details($user_id,$listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }




    public function add_review() {
        $this->load->model('NursingattendantModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->NursingattendantModel->check_auth_client();
            if ($check_auth_client == true) {
              //  $response = $this->load->model('LoginModel');
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
                        $resp = $this->NursingattendantModel->add_review($user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_list() {
        $this->load->model('NursingattendantModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->NursingattendantModel->check_auth_client();
            if ($check_auth_client == true) {
               // $response = $this->load->model('LoginModel');
               $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->NursingattendantModel->review_list($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function review_with_comment() {
        $this->load->model('NursingattendantModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->NursingattendantModel->check_auth_client();
            if ($check_auth_client == true) {
               // $response = $this->load->model('LoginModel');
               $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->NursingattendantModel->review_with_comment($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function review_like() {
        $this->load->model('NursingattendantModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->NursingattendantModel->check_auth_client();
            if ($check_auth_client == true) {
              //  $response = $this->load->model('LoginModel');
              $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->NursingattendantModel->review_like($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment() {
        $this->load->model('NursingattendantModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->NursingattendantModel->check_auth_client();
            if ($check_auth_client == true) {
               // $response = $this->load->model('LoginModel');
               $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $comment = $params['comment'];
                        $resp = $this->NursingattendantModel->review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_like() {
        $this->load->model('NursingattendantModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->NursingattendantModel->check_auth_client();
            if ($check_auth_client == true) {
              //  $response = $this->load->model('LoginModel');
              $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->NursingattendantModel->review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_list() {
        $this->load->model('NursingattendantModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->NursingattendantModel->check_auth_client();
            if ($check_auth_client == true) {
               // $response = $this->load->model('LoginModel');
               $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->NursingattendantModel->review_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function nursing_views() {
        $this->load->model('NursingattendantModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->NursingattendantModel->check_auth_client();
            if ($check_auth_client == true) {
                //$response = $this->load->model('LoginModel');
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
                        $resp = $this->NursingattendantModel->nursing_views($user_id, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
   /* public function nursing_booking_cancel() {
        $this->load->model('NursingattendantModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->NursingattendantModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->load->model('LoginModel');
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['booking_id'] == "" || $params['status'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $booking_id = $params['booking_id'];
                        $status     = $params['status'];
                        $resp = $this->NursingattendantModel->nursing_booking_cancel($user_id, $listing_id, $booking_id, $status);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }*/

}
