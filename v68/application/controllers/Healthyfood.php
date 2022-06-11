<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Healthyfood extends CI_Controller {

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

    public function yogapulp_home() {
        $this->load->model('Healthyfoodmodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Healthyfoodmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];

                        $resp = $this->Healthyfoodmodel->yogapulp_home($user_id, $listing_id);
                    }



                    simple_json_output($resp);
                }
            }
        }
    }

    public function yogapulp_get_quotes() {
        $this->load->model('Healthyfoodmodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Healthyfoodmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth()
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['pincode'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $pincode = $params['pincode'];
                        $resp = $this->Healthyfoodmodel->yogapulp_get_quotes($pincode);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function yogapulp_pincode_check() {
        $this->load->model('Healthyfoodmodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Healthyfoodmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth()
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['pincode'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $pincode = $params['pincode'];
                        $resp = $this->Healthyfoodmodel->yogapulp_pincode_check($pincode);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function yogapulp_cart_order() {
        $this->load->model('Healthyfoodmodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Healthyfoodmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth()
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['address_id'] == "" || $params['product_id'] == "" || $params['product_quantity'] == "" || $params['product_price'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $address_id = $params['address_id'];
                        $product_id = $params['product_id'];
                        $product_quantity = $params['product_quantity'];
                        $product_price = $params['product_price'];
                        $resp = $this->Healthyfoodmodel->yogapulp_cart_order($user_id, $address_id, $product_id, $product_quantity, $product_price);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function yogapulp_cart_order_list() {
        $this->load->model('Healthyfoodmodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Healthyfoodmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth()
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->Healthyfoodmodel->yogapulp_cart_order_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function yogapulp_cart_order_details() {
        $this->load->model('Healthyfoodmodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Healthyfoodmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth()
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['order_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $order_id = $params['order_id'];
                        $resp = $this->Healthyfoodmodel->yogapulp_cart_order_details($user_id, $order_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }

    public function yogapulp_product_review() {
        $this->load->model('Healthyfoodmodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Healthyfoodmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth()
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
                        $resp = $this->Healthyfoodmodel->yogapulp_product_review($user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function edit_yogapulp_product_review() {
        $this->load->model('Healthyfoodmodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->Healthyfoodmodel->check_auth_client();
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
                        $resp = $this->Healthyfoodmodel->edit_yogapulp_product_review($review_id,$user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function yogapulp_product_review_list() {
        $this->load->model('Healthyfoodmodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Healthyfoodmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth()
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->Healthyfoodmodel->yogapulp_product_review_list($user_id, $listing_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }

    public function yogapulp_product_review_likes() {
        $this->load->model('Healthyfoodmodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Healthyfoodmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth()
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->Healthyfoodmodel->yogapulp_product_review_likes($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function yogapulp_product_review_comment() {
        $this->load->model('Healthyfoodmodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Healthyfoodmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth()
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $comment = $params['comment'];
                        $resp = $this->Healthyfoodmodel->yogapulp_product_review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function yogapulp_product_review_comment_like() {
        $this->load->model('Healthyfoodmodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Healthyfoodmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth()
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->Healthyfoodmodel->yogapulp_product_review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function yogapulp_product_review_comment_list() {
        $this->load->model('Healthyfoodmodel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->Healthyfoodmodel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth()
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->Healthyfoodmodel->yogapulp_product_review_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

}
