<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class All_booking_controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('LoginModel');
    }
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    public function doctor_list()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "" || $params['category_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $latitude    = $params['latitude'];
                        $longitude   = $params['longitude'];
                        $category_id = $params['category_id'];
                        $resp        = $this->All_booking_model->doctor_list($latitude, $longitude, $user_id, $category_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function search_list() {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['keyword'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $keyword = $params['keyword'];
                        $resp = $this->All_booking_model->search_list($user_id, $keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function pharmacy_presciption_appointment_listing()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
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
                        /*$listing_id = $params['listing_id'];*/
                        $resp       = $this->All_booking_model->pharmacy_presciption_appointment_listing($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
      public function all_booking_details()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
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
                        /*$listing_id = $params['listing_id'];*/
                           if(array_key_exists("term",$params)){
                            $term = $params['term'];
                        } else {
                            $term = "";
                            
                        }
                        $resp       = $this->All_booking_model->all_booking_details($user_id,$term);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //added by zak for doctor details for searching 
    
      public function doctor_search_details()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
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
                        $user_id    = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp       = $this->All_booking_model->doctor_search_details($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function add_review()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
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
                        $user_id    = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $rating     = $params['rating'];
                        $review     = $params['review'];
                        $service    = $params['service'];
                        $resp       = $this->All_booking_model->add_review($user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function review_list()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
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
                        $user_id    = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp       = $this->All_booking_model->review_list($user_id, $listing_id);
                    }
                    if ($resp != '') {
                        json_outputs($resp);
                    } else {
                        json_outputs_not_found($resp);
                    }
                }
            }
        }
    }
    public function review_like()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
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
                        $resp    = $this->All_booking_model->review_like($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function review_comment()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
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
                        $resp    = $this->All_booking_model->review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function review_comment_like()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
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
                        $user_id    = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp       = $this->All_booking_model->review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function review_comment_list()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
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
                        $resp    = $this->All_booking_model->review_comment_list($user_id, $post_id);
                    }
                    if ($resp != '') {
                        json_outputs($resp);
                    } else {
                        json_outputs_not_found($resp);
                    }
                }
            }
        }
    }
    public function doctor_category()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctors_type_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $doctors_type_id = $params['doctors_type_id'];
                        $resp            = $this->All_booking_model->doctor_category($doctors_type_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function clinic_booking_slot()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['clinic_id'] == "" || $params['doctor_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $clinic_id = $params['clinic_id'];
                        $doctor_id = $params['doctor_id'];
                        $resp      = $this->All_booking_model->clinic_booking_slot($clinic_id, $doctor_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function doctor_views()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
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
                        $user_id    = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp       = $this->All_booking_model->doctor_views($user_id, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    /*
    Clinic id should not be empty
    */
   /* public function add_bookings()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if (!is_numeric($params['user_id']) || $params['user_id'] == "" || $params['listing_id'] == "" || !is_numeric($params['listing_id']) || $params['clinic_id'] == "" || !is_numeric($params['clinic_id']) || $params['is_user'] == "" || $params['is_patient'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields1'
                        );
                    } else if ($params['is_user'] == 0 && ($params['relationship'] == "" || $params['date_of_birth'] == "")) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields2'
                        );
                    } else if ($params['is_patient'] == 1 && $params['patient_id'] == '' && !is_numeric($params['patient_id'])) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields3'
                        );
                    } else {
                        $user_id            = $params['user_id'];
                        $listing_id         = $params['listing_id'];
                        $clinic_id          = $params['clinic_id'];
                        $booking_date       = $params['booking_date'];
                        $booking_time       = $params['booking_time'];
                        $from_time          = $params['from_time'];
                        $to_time            = $params['to_time'];
                        $user_name          = $params['user_name'];
                        $user_mobile        = $params['user_mobile'];
                        $user_email         = $params['user_email'];
                        $user_gender        = $params['user_gender'];
                        $is_user            = $params['is_user'];
                        $is_patient         = $params['is_patient'];
                        $patient_id         = $params['patient_id'];
                        $health_condition   = $params['health_condition'];
                        $allergies          = $params['allergies'];
                        $heradiatry_problem = $params['heradiatry_problem'];
                        $description        = $params['description'];
                        $relationship       = $params['relationship'];
                        $date_of_birth      = $params['date_of_birth'];
                        $connect_type       = $params['connect_type'];
                        $resp               = $this->All_booking_model->add_bookings($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $is_user, $is_patient, $patient_id, $health_condition, $allergies, $heradiatry_problem, $description, $relationship, $date_of_birth, $connect_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }*/
    
     public function add_bookings()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if (!is_numeric($params['user_id']) || $params['user_id'] == "" || $params['listing_id'] == "" || !is_numeric($params['listing_id']) || $params['clinic_id'] == "" || !is_numeric($params['clinic_id']) || $params['is_user'] == "" || $params['is_patient'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields1'
                        );
                    } else if ($params['is_user'] == 0 && ($params['relationship'] == "" || $params['date_of_birth'] == "")) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields2'
                        );
                    } else if ($params['is_patient'] == 1 && $params['patient_id'] == '' && !is_numeric($params['patient_id'])) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields3'
                        );
                    } else {
                        $user_id            = $params['user_id'];
                        $listing_id         = $params['listing_id'];
                        $clinic_id          = $params['clinic_id'];
                        $booking_date       = $params['booking_date'];
                        $booking_time       = $params['booking_time'];
                        $from_time          = $params['from_time'];
                        $to_time            = $params['to_time'];
                        $user_name          = $params['user_name'];
                        $user_mobile        = $params['user_mobile'];
                        $user_email         = $params['user_email'];
                        $user_gender        = $params['user_gender'];
                        $is_user            = $params['is_user'];
                        $is_patient         = $params['is_patient'];
                        $patient_id         = $params['patient_id'];
                        $health_condition   = $params['health_condition'];
                        $allergies          = $params['allergies'];
                        $heradiatry_problem = $params['heradiatry_problem'];
                        $description        = $params['description'];
                        $relationship       = $params['relationship'];
                        $date_of_birth      = $params['date_of_birth'];
                        $connect_type       = $params['connect_type'];
                        $resp               = $this->All_booking_model->add_bookings($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $is_user, $is_patient, $patient_id, $health_condition, $allergies, $heradiatry_problem, $description, $relationship, $date_of_birth, $connect_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function user_booking_profile()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" /*$params['user_relation'] == "" || $params['rel_dob'] == "" || $params['user_rel_gender'] == ""  || $params['user_rel_mobile'] == "" || $params['user_rel_email'] == "" || $params['user_medical_condition'] == "" || $params['user_allergies'] == "" || $params['hereditary_problems'] == ""*/ ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id               = $params['user_id'];
                        $listing_id            = $params['listing_id'];
                        $user_rel_name         = $params['user_rel_name'];
                        $user_relation         = $params['user_relation'];
                        $rel_dob               = $params['rel_dob'];
                        $user_rel_gender       = $params['user_rel_gender'];
                        $user_rel_mobile       = $params['user_rel_mobile'];
                        $user_rel_email        = $params['user_rel_email'];
                        $user_health_condition = $params['user_medical_condition'];
                        $user_allergies        = $params['user_allergies'];
                        $hereditary_problems   = $params['hereditary_problems'];
                        $resp                  = $this->All_booking_model->user_booking_profile($user_id, $user_rel_name, $user_relation, $rel_dob, $user_rel_gender, $user_rel_mobile, $user_rel_email, $user_health_condition, $user_allergies, $hereditary_problems);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function user_booking_slot()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['patient_id'] == "" || $params['clinic_id'] == "" || $params['listing_id'] == "" || $params['consultation_type'] == "" || $params['status'] == "" || $params['slot'] == "" || $params['booking_date'] == "" || $params['patient_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id           = $params['user_id'];
                        $listing_id        = $params['listing_id'];
                        $booking_id        = $params['booking_id'];
                        $clinic_id         = $params['clinic_id'];
                        $consultation_type = $params['consultation_type'];
                        $patient_id        = $params['patient_id'];
                        $booking_date      = $params['booking_date'];
                        $from_time         = $params['from_time'];
                        $to_time           = $params['to_time'];
                        $description       = $params['description'];
                        $status            = $params['status'];
                        $resp              = $this->All_booking_model->user_booking_slot($user_id, $listing_id, $booking_id, $clinic_id, $consultation_type, $from_time, $to_time, $description, $status, $booking_date, $patient_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function user_read_slot()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['doctor_id'] == "" || $params['consultation_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $clinic_id         = $params['clinic_id'];
                        $doctor_id         = $params['doctor_id'];
                        $consultation_type = $params['consultation_type'];
                        $resp              = $this->All_booking_model->user_read_slot($clinic_id, $doctor_id, $consultation_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function insert_doctor_users_feedback()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['doctor_id'] == "" || $params['type'] == "" || $params['feedback'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $doctor_id = $params['doctor_id'];
                        $user_id   = $params['user_id'];
                        $type      = $params['type'];
                        $feedback  = $params['feedback'];
                        $ratings   = $params['ratings'];
                        $recommend = $params['recommend'];
                         $booking_id = $params['booking_id'];
					    $booking_type =$params['booking_type'];
					    
                        $resp      = $this->All_booking_model->insert_doctor_users_feedback($doctor_id, $user_id, $type, $feedback, $ratings, $recommend,$booking_id,$booking_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function view_appointments()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
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
                        $resp    = $this->All_booking_model->view_appointments_module($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    //   public function add_bookings() {
    //         $this->load->model('All_booking_model');
    //         $method = $_SERVER['REQUEST_METHOD'];
    //         if ($method != 'POST') {
    //             json_output(400, array(
    //                 'status' => 400,
    //                 'message' => 'Bad request.'
    //             ));
    //         } else {
    //             $check_auth_client = $this->All_booking_model->check_auth_client();
    //             if ($check_auth_client == true) {
    //                 $response = $this->LoginModel->auth();
    //                 if ($response['status'] == 200) {
    //                     $params = json_decode(file_get_contents('php://input'), TRUE);
    //                     if (  $params['user_id'] == "") {
    //                         $resp = array(
    //                             'status' => 400,
    //                             'message' => 'please enter all fields'
    //                         );
    //                     } else {
    //                         $user_id = $params['user_id'];
    //                         $listing_id = $params['listing_id'];
    //                         $clinic_id = $params['clinic_id'];
    //                         $booking_date = $params['booking_date'];
    //                         $booking_time = $params['booking_time'];
    //                         $user_name = $params['user_name'];
    //                         $user_mobile = $params['user_mobile'];
    //                         $user_email = $params['user_email'];
    //                         $user_gender = $params['user_gender'];
    //                         $is_user = $params['is_user'];
    //                         $is_patient = $params['is_patient'];
    //                         $patient_id = $params['patient_id'];
    //                         $health_condition = $params['health_condition'];
    //                         $allergies = $params['allergies'];
    //                         $heradiatry_problem = $params['heradiatry_problem'];
    //                         $description = $params['description'];
    //                         $relationship = $params['relationship'];
    //                         $date_of_birth = $params['date_of_birth'];
    //                         $connect_type = $params['connect_type'];
    //                         $resp = $this->All_booking_model->Add_booking($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, 
    //                                                                  $user_email, $user_gender, $is_user, $is_patient, $patient_id, $health_condition, $allergies, $heradiatry_problem, $description,
    //                                                                  $relationship, $date_of_birth, $connect_type);
    //                     }
    //                     json_outputs($resp);
    //                 }
    //             }
    //         }
    //     }
    public function read_prescription()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params     = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id  = $params['doctor_id'];
                    $patient_id = $params['patient_id'];
                    if ($doctor_id == "" || $patient_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $doctorName             = $this->All_booking_model->get_doctor_name($doctor_id);
                        $doctorprescriptionRows = $this->All_booking_model->get_doctor_prescription($doctor_id, $patient_id);
                        foreach ($doctorprescriptionRows->result_array() as $row) {
                            $prescription_id = $row['id'];
                            $prescription_id;
                            $clinic_id                          = $row['clinic_id'];
                            $clinicNameReturn                   = $this->All_booking_model->get_clinic_name($clinic_id);
                            $dateOfVisit                        = $row['created_date'];
                            $visitTime                          = date("H:i:s", strtotime($dateOfVisit));
                            $visitDate                          = date("Y-m-d", strtotime($dateOfVisit));
                            $patientDetails['clinic_name']      = $clinicNameReturn;
                            $patientDetails['visit_date']       = $visitDate;
                            $patientDetails['visit_time']       = $visitTime;
                            $patientDetails['prescriptionNote'] = $row['prescription_note'];
                            // doctor_prescription_medicine
                            $allMedicine                        = $this->All_booking_model->get_doctor_prescription_medicine($prescription_id);
                            $patientDetails['Medicines']        = $allMedicine;
                            // doctor_prescription_test
                            $testDetails                        = $this->All_booking_model->get_doctor_prescription_test($prescription_id);
                            $patientDetails['Tests']            = $testDetails;
                            $details[]                          = $patientDetails;
                        }
                        $resp = array(
                            'doctor_name' => $doctorName,
                            'details' => $details
                        );
                        // $resp['doctor_name']=>$doctorName;
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function booking_details()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['booking_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'User Id can\'t empty'
                        );
                    } else {
                        $booking_id = $params['booking_id'];
                        $resp       = $this->All_booking_model->booking_details($booking_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function vendor_discount()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params    = json_decode(file_get_contents('php://input'), TRUE);
                    $vendor_id = $params['vendor_id'];
                    $clinic_id = $params['clinic_id'];
                    if ($vendor_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter vendor id'
                        );
                    } else {
                        $resp = $this->All_booking_model->vendor_discount($vendor_id, $clinic_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function user_payment_approval()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params             = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id          = $params['doctor_id'];
                    $confirm_reschedule = $params['confirm_reschedule'];
                    $booking_id         = $params['booking_id'];
                    $user_id            = $params['user_id'];
                    if ($params['doctor_id'] == "" || $params['user_id'] == "" || $params['confirm_reschedule'] == "" || $params['booking_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->All_booking_model->user_payment_approval($doctor_id, $confirm_reschedule, $booking_id, $user_id);
                    }
                }
                simple_json_output($resp);
            }
        }
    }
    
    public function get_doctor_consultation(){
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params             = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id          = $params['doctor_id'];
                   
                    if ($doctor_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter doctor id'
                        );
                    } else {
                        $data = $this->All_booking_model->get_doctor_consultation($doctor_id);
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' => $data
                        );
                    }
                }
                simple_json_output($resp);
            }
        }
    }
    
    public function edit_bookings(){
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['booking_id'] == "" || $params['from_time'] == "" || $params['to_time'] == "" || $params['booking_date'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    }  else {
                        
                        $booking_id  = $params['booking_id'];
                        $booking_time = $params['from_time'] . " - ". $params['to_time'];
                       
                        $newdata = array(
                                'booking_id' => $params['booking_id'],
                                'from_time' => $params['from_time'],
                                'to_time' => $params['to_time'],
                                'booking_date' => $params['booking_date'],
                                'booking_time' => $booking_time,
                                'status' => 1
                            );
                     
                        $resp = $this->All_booking_model->edit_bookings($booking_id, $newdata);
                        
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //added for cart list for globally
    public function get_cart_details_list(){
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params             = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id          = $params['user_id'];
                   
                    if ($user_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter user id'
                        );
                    } else {
                        $data = $this->All_booking_model->get_cart_details_list($user_id);
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' => $data
                        );
                    }
                }
                simple_json_output($resp);
            }
        }
    }
    
    public function recent_doctor_list()
    {
        $this->load->model('All_booking_model');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->All_booking_model->check_auth_client();
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
                        $user_id     = $params['user_id'];
                        $resp        = $this->All_booking_model->recent_doctor_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
}