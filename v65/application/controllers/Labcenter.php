<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Labcenter extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    } 

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function labcenter_list() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['lat'] == "" || $params['lng'] == "" || $params['category_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $lat = $params['lat'];
                        $lng = $params['lng'];
                        $category_id = $params['category_id'];
                        if(!empty($params['hospital_type'])){
                            $hospital_type = $params['hospital_type'];
                        } else {
                            $hospital_type = 0;
                        }
                        $resp = $this->LabcenterModel->labcenter_list($lat, $lng, $user_id, $category_id, $hospital_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function addtocart() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['test_id'] == "" || $params['vendor_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id   = $params['user_id'];
                        $test_id   = $params['test_id'];
                        $vendor_id = $params['vendor_id'];
                        $resp = $this->LabcenterModel->addtocart($user_id,$test_id,$vendor_id);
                    }
                    simple_json_output($resp);
                }
            }
        } 
    }
    
    public function add_package() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['package_id'] == "" || $params['vendor_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id   = $params['user_id'];
                        $package_id   = $params['package_id'];
                        $vendor_id = $params['vendor_id'];
                        $resp = $this->LabcenterModel->add_package($user_id,$package_id,$vendor_id);
                    }
                    simple_json_output($resp);
                }
            }
        } 
    }
    
    public function add_order() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['vendor_id'] == "" || $params['address_id'] == "" || $params['vendor_type'] == "" || $params['booking_date'] == "" || $params['booking_time'] == "" || $params['package_id'] == "" || $params['test_id'] == "" || $params['member_id'] == "" || $params['payment_type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id      = $params['user_id'];
                        $vendor_id    = $params['vendor_id'];
                        $address_id   = $params['address_id'];
                        $vendor_type  = $params['vendor_type'];
                        $booking_date = $params['booking_date'];
                        $booking_time = $params['booking_time'];
                        $package_id   = $params['package_id'];
                        $test_id      = $params['test_id'];
                        $member_id    = $params['member_id'];
                        $payment_type = $params['payment_type'];
                        $resp = $this->LabcenterModel->add_order($user_id,$vendor_id,$address_id,$vendor_type,$booking_date,$booking_time,$package_id,$test_id,$member_id,$payment_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function update_order() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['booking_id'] == "" || $params['ledger_id'] == "" || $params['transaction_id'] == "" || $params['payment_status'] == "" || $params['ledger_type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $booking_id     = $params['booking_id'];
                        $ledger_id      = $params['ledger_id'];
                        $transaction_id = $params['transaction_id'];
                        $payment_status = $params['payment_status'];
                        $ledger_type    = $params['ledger_type'];
                        $resp = $this->LabcenterModel->update_order($booking_id,$ledger_id,$transaction_id,$payment_status,$ledger_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function addtestcart() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['test_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id   = $params['user_id'];
                        $test_id   = $params['test_id'];
                        $vendor_id = $params['vendor_id'];
                        $clear_all = $params['clear_all'];
                        $resp = $this->LabcenterModel->addtestcart($user_id,$test_id,$vendor_id,$clear_all);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
      
    public function showtestcart() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->LabcenterModel->showtestcart($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function all_lab_package() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $page             = $params['page'];
                        $category         = $params['category'];
                        $most_common_risk = $params['most_common_risk'];
                        $sort_type        = $params['sort_type'];
                        $home_delivery    = $params['home_delivery'];
                        $vendor_id    = $params['vendor_id'];
                        $resp = $this->LabcenterModel->all_lab_package($page,$category,$most_common_risk,$sort_type,$home_delivery,$vendor_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function all_lab_test() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $page             = $params['page'];
                        $category         = $params['category'];
                        $most_common_risk = $params['most_common_risk'];
                        $sort_type        = $params['sort_type'];
                        $home_delivery    = $params['home_delivery'];
                        $vendor_id    = $params['vendor_id'];
                        $resp = $this->LabcenterModel->all_lab_test($page,$category,$most_common_risk,$sort_type,$home_delivery,$vendor_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function test_list_by_vendor() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->LabcenterModel->test_list_by_vendor($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function vendor_cart_list() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->LabcenterModel->vendor_cart_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function lab_home() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->LabcenterModel->lab_home();
                    simple_json_output($resp);
                }
            }
        }
    }
    
    //added by zak for lab details 
    public function labcenter_details()
    {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->LabcenterModel->labcenter_details($user_id, $listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //added by zak for lab branches details 
    public function labcenter_branches_details()
    { 
          $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $branch_id  = $params['branch_id'];
                        $resp = $this->LabcenterModel->labcenter_branches_details($user_id, $listing_id,$branch_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //end 

    public function labcenter_packages() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['labcenter_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $labcenter_id = $params['labcenter_id'];
                        $resp = $this->LabcenterModel->labcenter_packages($labcenter_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }

    public function lab_test_search() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['keyword'] == "" || $params['category_id'] == "" || $params['lab_user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter Test');
                    } else {
                        $keyword = $params['keyword'];
                        $category_id = $params['category_id'];
                        $lab_user_id = $params['lab_user_id'];
                        $resp = $this->LabcenterModel->lab_test_search($keyword, $category_id, $lab_user_id);
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

    public function add_review() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
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
                        $resp = $this->LabcenterModel->add_review($user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function edit_review() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
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
                        $resp = $this->LabcenterModel->edit_review($review_id,$user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_list() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->LabcenterModel->review_list($user_id, $listing_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }
       public function review_with_comment() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->LabcenterModel->review_with_comment($user_id, $listing_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }

    public function review_like() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->LabcenterModel->review_like($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
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
                        $resp = $this->LabcenterModel->review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_like() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->LabcenterModel->review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function review_comment_list() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->LabcenterModel->review_comment_list($user_id, $post_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }
    
    public function lab_booking() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    //print_r($params);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['address_line1']=="" || $params['address_line2']=="" || $params['user_name'] == "" || $params['user_email'] == "" || $params['user_mobile'] == "" || $params['user_gender'] == "" || $params['branch_id'] == "" || $params['branch_name'] == "" || $params['vendor_id'] == "" || $params['payment_mode'] == "" || $params['joining_date'] == "" || $params['status'] == "" ||$params['trail_booking_date'] == "" || $params['trail_booking_time'] == "" || $params['booking_location'] == "" || $params['booking_address'] == "" || $params['booking_mobile'] == "" || $params['patient_id'] == "" || $params['at_home'] == "" || $params['city'] == "" || $params['state'] == "" || $params['pincode'] == "" || $params['address_id'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id    = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $package_id = $params['package_id'];
                        $address_line1 = $params['address_line1'];
                        $address_line2 = $params['address_line2'];
                        $user_name  = $params['user_name'];
                        $mobile     = $params['user_mobile'];
                        $email      = $params['user_email'];
                        $gender     = $params['user_gender'];
                        $branch_id  = $params['branch_id'];
                        $branch_name= $params['branch_name'];
                        $vendor_id  = $params['vendor_id'];
                        $status     = $params['status'];
                        $payment_mode       = $params['payment_mode'];
                        $trail_booking_date = $params['trail_booking_date'];
                        $trail_booking_time = $params['trail_booking_time'];
                        $joining_date       = $params['joining_date'];
                        $booking_location   = $params['booking_location'];
                        $booking_address    = $params['booking_address'];
                        $booking_mobile     = $params['booking_mobile'];
                        $test_ids           = $params['test_ids'];
                        $patient_id         = $params['patient_id'];
                        $at_home      = $params['at_home'];
                        $city         = $params['city'];
                        $state        = $params['state'];
                        $pincode      = $params['pincode'];
                        $address_id   = $params['address_id'];
                        
                        /*if($params['booking_id'] != ''){
                            $booking_id   = $params['booking_id'];
                        }
                        else{*/
                            $booking_id = date('YmdHis');
                        /*}*/
                        
                        
                        $resp = $this->LabcenterModel->lab_booking($user_id, $listing_id, $address_line1, $address_line2, $package_id, $user_name, $mobile, $email, $gender, $branch_id, $branch_name, $vendor_id, $status, $payment_mode, $trail_booking_date, $trail_booking_time, $joining_date, $booking_location, $booking_address, $booking_mobile,$test_ids, $patient_id, $at_home, $city, $state, $pincode, $address_id,$booking_id);
                    }

                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    public function tyrocare_lab_booking() {
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    //print_r($params);
                    if ($params['user_id'] == "" || $params['address'] == "" || $params['rate']=="" || $params['report_code']=="" || $params['pincode'] == "" || $params['bencount'] == "" || $params['mobile'] == "" || $params['email'] == "" || $params['order_by'] == "" || $params['service_type'] == "" || $params['hc'] == "" || $params['ref_code'] == "" || $params['reports'] == "" || $params['bendataxml'] == "" ||$params['appt_date'] == "" || $params['appt_time'] == "" || $params['pay_type'] == "" || $params['product'] == "" || $params['orderid'] == "" || $params['vendor_type'] == "" || $params['status'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $vendor_type    = $params['vendor_type'];
                        $user_id    = $params['user_id'];
                        $address = $params['address'];
                        $amount = $params['rate'];
                        $report_code = $params['report_code'];
                        $pincode = $params['pincode'];
                        $bencount  = $params['bencount'];
                        $mobile     = $params['mobile'];
                        $email      = $params['email'];
                        $order_by     = $params['order_by'];
                        $service_type  = $params['service_type'];
                        $hc = $params['hc'];
                        $ref_code  = $params['ref_code'];
                        $reports     = $params['reports'];
                        $bendataxml       = $params['bendataxml'];
                        $booking_date = $params['appt_date'];
                        $booking_time = $params['appt_time'];
                        $payment_method       = $params['pay_type'];
                        $product   = $params['product'];
                        $booking_id    = $params['orderid'];
                        $status     = $params['status'];
                        $reference_id = $params['reference_id'];
                        $passon = $params['passOn']; 
                          if (array_key_exists("leadId",$params)){
                            $leadId = $params['leadId'];
                        } else {
                            $leadId = "";
                        }
                         
                       
                        $resp = $this->LabcenterModel->tyrocare_lab_booking($vendor_type, $user_id, $address, $amount, $report_code, $pincode, $bencount, $mobile, $email, $order_by, $service_type, $hc, $ref_code, $reports, $bendataxml, $booking_date, $booking_time, $payment_method, $product, $booking_id, $status,$reference_id,$leadId,$passon);
                    }

                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    public function lab_test_list(){
         $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $page    = $params['page'];
                        $resp = $this->LabcenterModel->lab_test_list($user_id, $page);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function lab_booked_list(){
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->LabcenterModel->lab_booked_list($user_id, $listing_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }
    
    public function tyrocare_booked_list(){
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['vendor_type'] == "" || $params['user_id'] == "" ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $vendor_type = $params['vendor_type'];
                        $userid=$params['user_id'];
                        $resp = $this->LabcenterModel->tyrocare_booked_list($vendor_type,$userid);
                    }

                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function lab_instruction_details(){
        $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['id'] == "" || $params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $id = $params['id'];
                        $resp = $this->LabcenterModel->lab_instruction_details($user_id,$id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    //Added by Swapnali 
    public function lab_tests(){ 
          $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter user_id');
                    } else {
                        $user_id = $params['user_id'];
                       
                        $resp = $this->LabcenterModel->lab_tests($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    // lab_vendor_by_test
    
    public function lab_vendor_by_test1(){ 
          $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['test_id'] == "")  {
                        $resp = array('status' => 400, 'message' => 'please enter user_id and test_id');
                    } else {
                        $user_id = $params['user_id'];
                        $test_id = $params['test_id'];
                       
                        $resp = $this->LabcenterModel->lab_vendor_by_test1($user_id,$test_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function lab_vendor_by_test(){ 
          $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['test_id'] == "")  {
                        $resp = array('status' => 400, 'message' => 'please enter user_id and test_id');
                    } else {
                        $user_id = $params['user_id'];
                        $test_id = $params['test_id'];
                        
                        
                        if(array_key_exists("per_page",$params)){
                            $per_page = $params['per_page'];
                        } else {
                            $per_page = 0;
                        }
                        
                        if(array_key_exists("page_no",$params)){
                            $page_no = $params['page_no'];
                        } else {
                            $page_no = 0;
                        }
                        

                       
                        $resp = $this->LabcenterModel->lab_vendor_by_test($user_id,$test_id,$per_page,$page_no);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function lab_test_by_vendor(){ 
          $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['vendor_id'] == "")  {
                        $resp = array('status' => 400, 'message' => 'please enter user_id and vendor_id');
                    } else {
                        $user_id = $params['user_id'];
                        $vendor_id = $params['vendor_id'];
                       
                        $resp = $this->LabcenterModel->lab_test_by_vendor($vendor_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function update_email(){ 
          $this->load->model('LabcenterModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LabcenterModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['email'] == "")  {
                        $resp = array('status' => 400, 'message' => 'please enter user_id and vendor_id');
                    } else {
                        $user_id = $params['user_id'];
                        $email= $params['email'];
                       
                        $resp = $this->LabcenterModel->update_email($user_id,$email);
                    }
                    json_output(400,$resp);
                }
            }
        }
    }
    
    
    //end 

}
