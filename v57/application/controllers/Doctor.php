<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Doctor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
         $this->load->model('LoginModel');
         $this->load->model('DoctorModel');
     
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
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "" || $params['category_id'] == " "   ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $latitude    = $params['latitude'];
                        $longitude   = $params['longitude'];
                        $category_id = $params['category_id'];
                        if(array_key_exists("page",$params)){
                            $page = $params['page'];
                        } else {
                            $page = '';
                        }
                        if(array_key_exists("keyword",$params)){
                            $keyword= $params['keyword'];
                        } else {
                            $keyword= '';
                        }
                        
                        
                        
                        $resp        = $this->DoctorModel->doctor_list($latitude, $longitude, $user_id, $category_id,$page,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
      public function doctor_list_search()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == "" ||  $params['page'] == "" ||$params['keyword'] == ""  ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $latitude    = $params['latitude'];
                        $longitude   = $params['longitude'];
                        $page = $params['page'];
                        $keyword= $params['keyword'];
                        $resp        = $this->DoctorModel->doctor_list_search($latitude, $longitude, $user_id,$page,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    //added by zak for related doctor 
    
    
    public function doctor_related_list()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == '' || $params['doctor_id'] == '') {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $latitude    = $params['latitude'];
                         $longitude   = $params['longitude'];
                         $doctor_id  = $params['doctor_id'];
                       // $category_id = $params['category_id'];
                        $resp        = $this->DoctorModel->doctor_related_list($user_id,$latitude,$longitude,$doctor_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function doctor_list_gender()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['latitude'] == "" || $params['longitude'] == ""|| $params['gender'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id     = $params['user_id'];
                        $latitude    = $params['latitude'];
                        $longitude   = $params['longitude'];
                        $gender   = $params['gender'];
                       
                        $resp        = $this->DoctorModel->doctor_list_gender($latitude, $longitude, $user_id,$gender);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function doctor_details()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp       = $this->DoctorModel->doctor_details($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //added by zak for doctor details for searching 
    
      public function doctor_search_details()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp       = $this->DoctorModel->doctor_search_details($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function add_review()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp       = $this->DoctorModel->add_review($user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function review_list()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp       = $this->DoctorModel->review_list($user_id, $listing_id);
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
    
     public function review_with_comment()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp       = $this->DoctorModel->review_with_comment($user_id, $listing_id);
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
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp    = $this->DoctorModel->review_like($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function review_comment()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp    = $this->DoctorModel->review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function review_comment_like()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp       = $this->DoctorModel->review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function review_comment_list()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp    = $this->DoctorModel->review_comment_list($user_id, $post_id);
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
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                           if (array_key_exists("doctors_type_name",$params))
                   {
                     $doctors_type_name = $params['doctors_type_name'];
                   }
               else
                   {
                   $doctors_type_name = "";
                    }
              
                        $resp            = $this->DoctorModel->doctor_category($doctors_type_id,$doctors_type_name);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function clinic_booking_slot()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp      = $this->DoctorModel->clinic_booking_slot($clinic_id, $doctor_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function doctor_views()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp       = $this->DoctorModel->doctor_views($user_id, $listing_id);
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
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp               = $this->DoctorModel->add_bookings($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $is_user, $is_patient, $patient_id, $health_condition, $allergies, $heradiatry_problem, $description, $relationship, $date_of_birth, $connect_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }*/
    
     public function add_bookings()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                   
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['clinic_id'] == "" || $params['is_user'] == "" || $params['is_patient'] == "") {
                       
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
                        $resp               = $this->DoctorModel->add_bookings($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $is_user, $is_patient, $patient_id, $health_condition, $allergies, $heradiatry_problem, $description, $relationship, $date_of_birth, $connect_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
     public function add_bookings_v2() {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
              if ($this->input->post('user_id') == "" || $this->input->post('listing_id') == "" || $this->input->post('clinic_id') == "") {
                       
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields1'
                        );
                    } else if (($this->input->post('relationship') == "" || $this->input->post('date_of_birth') == "")) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields2'
                        );
                    } else if ($this->input->post('patient_id') == '' && !is_numeric($this->input->post('patient_id'))) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields3'
                        );
                    } else 
                    {
            $question = $this->input->post('health_condition');
            $questions = json_decode($question);
            $pre_final_que = $questions->quastion;
            $final_que = $pre_final_que[0];
            $q_user_id = $final_que->user_id;
            $final_q = $final_que->qas;
            $data2 = array();
            $resp = $this->DoctorModel->delete_question($q_user_id);
            for ($i = 0; $i < sizeof($final_q); $i++) {

                $data2['user_id'] = $q_user_id;
                $data2['question_id'] = $final_q[$i]->qid;
                $data2['answer'] = $final_q[$i]->qans;

                $resp = $this->DoctorModel->update_userprofile_question($data2);
            }
            
            $user_id            = $this->input->post('user_id');
            $listing_id         = $this->input->post('listing_id');
            $clinic_id          = $this->input->post('clinic_id');
            $booking_date       = $this->input->post('booking_date');
            $booking_time       = $this->input->post('booking_time');
            $from_time          = $this->input->post('from_time');
            $to_time            = $this->input->post('to_time');
            $user_name          = $this->input->post('user_name');
            $user_mobile        = $this->input->post('user_mobile');
            $user_email         = $this->input->post('user_email');
            $user_gender        = $this->input->post('user_gender');
            $is_user            = $this->input->post('is_user');           
            $is_patient         = $this->input->post('is_patient');
            $patient_id         = $this->input->post('patient_id');
            $allergies          = $this->input->post('allergies');
            $heradiatry_problem = $this->input->post('heradiatry_problem');
            $description        = $this->input->post('description');
            $relationship       = $this->input->post('relationship');
            $date_of_birth      = $this->input->post('date_of_birth');
            $connect_type       = $this->input->post('connect_type');
            $resp               = $this->DoctorModel->add_bookings_v2($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $is_user, $is_patient, $patient_id,$allergies, $heradiatry_problem, $description, $relationship, $date_of_birth, $connect_type);
                
}
         


           
            json_outputs($resp);
        }
    }
    
    public function user_booking_profile()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp                  = $this->DoctorModel->user_booking_profile($user_id, $user_rel_name, $user_relation, $rel_dob, $user_rel_gender, $user_rel_mobile, $user_rel_email, $user_health_condition, $user_allergies, $hereditary_problems);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function user_booking_slot()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp              = $this->DoctorModel->user_booking_slot($user_id, $listing_id, $booking_id, $clinic_id, $consultation_type, $from_time, $to_time, $description, $status, $booking_date, $patient_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function user_read_slot()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp              = $this->DoctorModel->user_read_slot($clinic_id, $doctor_id, $consultation_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
     public function user_read_slot_v2()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                       
                        $doctor_id         = $params['doctor_id'];
                        $consultation_type = $params['consultation_type'];
                        $resp              = $this->DoctorModel->user_read_slot_v2($doctor_id, $consultation_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function insert_doctor_users_feedback()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
					    
                        $resp      = $this->DoctorModel->insert_doctor_users_feedback($doctor_id, $user_id, $type, $feedback, $ratings, $recommend,$booking_id,$booking_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function view_appointments()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp    = $this->DoctorModel->view_appointments_module($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    //   public function add_bookings() {
    //         $this->load->model('DoctorModel');
    //         $method = $_SERVER['REQUEST_METHOD'];
    //         if ($method != 'POST') {
    //             json_output(400, array(
    //                 'status' => 400,
    //                 'message' => 'Bad request.'
    //             ));
    //         } else {
    //             $check_auth_client = $this->DoctorModel->check_auth_client();
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
    //                         $resp = $this->DoctorModel->Add_booking($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, 
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
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $doctorName             = $this->DoctorModel->get_doctor_name($doctor_id);
                        $doctorprescriptionRows = $this->DoctorModel->get_doctor_prescription($doctor_id, $patient_id);
                        foreach ($doctorprescriptionRows->result_array() as $row) {
                            $prescription_id = $row['id'];
                            $prescription_id;
                            $clinic_id                          = $row['clinic_id'];
                            $clinicNameReturn                   = $this->DoctorModel->get_clinic_name($clinic_id);
                            $dateOfVisit                        = $row['created_date'];
                            $visitTime                          = date("H:i:s", strtotime($dateOfVisit));
                            $visitDate                          = date("Y-m-d", strtotime($dateOfVisit));
                            $patientDetails['clinic_name']      = $clinicNameReturn;
                            $patientDetails['visit_date']       = $visitDate;
                            $patientDetails['visit_time']       = $visitTime;
                            $patientDetails['prescriptionNote'] = $row['prescription_note'];
                            // doctor_prescription_medicine
                            $allMedicine                        = $this->DoctorModel->get_doctor_prescription_medicine($prescription_id);
                            $patientDetails['Medicines']        = $allMedicine;
                            // doctor_prescription_test
                            $testDetails                        = $this->DoctorModel->get_doctor_prescription_test($prescription_id);
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
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp       = $this->DoctorModel->booking_details($booking_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function vendor_discount()
    {
        $this->load->model('DoctorModel');
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
                        $resp = $this->DoctorModel->vendor_discount($vendor_id, $clinic_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function user_payment_approval()
    {
        $this->load->model('DoctorModel');
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
                        $resp = $this->DoctorModel->user_payment_approval($doctor_id, $confirm_reschedule, $booking_id, $user_id);
                    }
                }
                simple_json_output($resp);
            }
        }
    }
    
    public function get_doctor_consultation(){
        $this->load->model('DoctorModel');
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
                        $data = $this->DoctorModel->get_doctor_consultation($doctor_id);
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
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                     
                        $resp = $this->DoctorModel->edit_bookings($booking_id, $newdata);
                        
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //added for cart list for globally
    public function get_cart_details_list(){
        $this->load->model('DoctorModel');
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
                        $data = $this->DoctorModel->get_cart_details_list($user_id);
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
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp        = $this->DoctorModel->recent_doctor_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    //doctor prescription 
    
       public function doctor_prescription_list()
    {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
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
                        $resp        = $this->DoctorModel->doctor_prescription_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function customer_call() {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id     = $params['user_id'];
                    $listing_id     = $params['listing_id'];
                    $call_to = $params['call_to'];
                    $call_from = $params['call_from'];
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $post_data = array(
                        
                    
                        'From' => $call_from,
                        'To' => $call_to,
                        'CallerId' => "02248931498",
                    
                        'CallType' => "trans",
                         'StatusCallback' => "http://medicalwale.com/"
                    );
               
                // print_r($post_data); die();
                 
                $exotel_sid = "aegishealthsolutions"; // Your Exotel SID - Get it from here: http://my.exotel.in/settings/site#api-settings
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5"; // Your exotel token - Get it from here: http://my.exotel.in/settings/site#api-settings
                 
                $url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Calls/connect";
                 
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                 
                $http_result = curl_exec($ch);
                $error = curl_error($ch);
                $http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
                 
                curl_close($ch);
                 
                print "Response = ".print_r($http_result); 
                
                
                $type= "doctor/customer_call";
                $this->DoctorModel->exotel_call($http_result,$type,$user_id,$listing_id);
                
              
                }
            }
        }
     
    }
    public function customer_call_whitlisting() {
        $this->load->model('DoctorModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->DoctorModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                     $order_info = $this->db->select('phone,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
                     $customer_phone = $order_info->phone;
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $post_data = array(
                        
                        'VirtualNumber' => '022-489-33722',
                        'Number' => "7021327803",
                        'Language' => 'en',
                        'CallType' => "trans",
                    );
                
                // print_r($post_data); die();
                 
                $exotel_sid = "aegishealthsolutions"; // Your Exotel SID - Get it from here: http://my.exotel.in/settings/site#api-settings
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5"; // Your exotel token - Get it from here: http://my.exotel.in/settings/site#api-settings
                 
                $url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Calls/connect";
                 
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                 
                $http_result = curl_exec($ch);
                $error = curl_error($ch);
                $http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
                 
                curl_close($ch);
                 
                print "Response = ".print_r($http_result); 
            
                }
            }
        }
     
    } 
    
    
    public function coupon_code() {
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
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['coupon'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                     
                    } else {
                        $user_id = $params['user_id'];
                        $physical_code = $params['coupon'] ;
                        $vendor_id = $params['vendor_id'] ;
                        $listing_id = $params['listing_id'] ;
                        
                     
                        $resp = $this->DoctorModel->coupon_code($user_id, $physical_code, $vendor_id, $listing_id);
                    }
                  simple_json_output($resp);
                }
            }
        }
    }

    
}