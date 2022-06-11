<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sexeducation extends CI_Controller {

    public function __construct() {
        
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

    public function kamasutra_category_list() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->kamasutra_category_list();
                    json_outputs($resp);
                }
            }
        }
    }

    public function kamasutra_naughty_talks() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->kamasutra_naughty_talks();
                    simple_json_output($resp);
                }
            }
        }
    }
    
   /* public function kamasutra_naughty_talks_hindi() {                                          
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->kamasutra_naughty_talks_hindi();
                    simple_json_output($resp);
                }
            }
        }
    }*/
    
    public function kamasutra_love_talks() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->kamasutra_love_talks();
                    simple_json_output($resp);
                }
            }
        }
    }

 /*   public function kamasutra_love_talks_hindi() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->kamasutra_love_talks_hindi();
                    simple_json_output($resp);
                }
            }
        }
    }*/
    public function kamasutra_love_quotes() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->kamasutra_love_quotes();
                    json_outputs($resp);
                }
            }
        }
    }

    public function kamasutra_love_quotes_hindi() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->kamasutra_love_quotes_hindi();
                    
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function kamasutra_pickup_lines() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->kamasutra_pickup_lines();
                    simple_json_output($resp);
                }
            }
        }
    }

    public function kamasutra_dirty_talks() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->kamasutra_dirty_talks();
                    simple_json_output($resp);
                }
            }
        }
    }

  /*  public function kamasutra_dirty_talks_hindi() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->kamasutra_dirty_talks_hindi();
                    simple_json_output($resp);
                }
            }
        }
    }*/
    
    public function kamasutra_position_gif() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['position_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'position id not blank');
                    } else {
                        $position_id = $params['position_id'];
                        $resp = $this->SexeducationModel->kamasutra_position_gif($position_id);
                    }
                    kama_webview_output($resp);
                }
            }
        }
    }

    public function kamasutra_all_positions() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'User Id cant empty');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->SexeducationModel->kamasutra_all_positions($user_id);
                    }
                    kama_json_output($resp);
                }
            }
        }
    }

    public function kamasutra_position_list() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['category_id'] == "" || $params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'Category Id can\'t empty');
                    } else {
                        $category_id = $params['category_id'];
                        $user_id = $params['user_id'];
                        $resp = $this->SexeducationModel->kamasutra_position_list($category_id, $user_id);
                    }
                    kama_json_output($resp);
                }
            }
        }
    }

    public function kamasutra_sex_tips() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->kamasutra_sex_tips();
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function kamasutra_sex_tips_hindi() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->kamasutra_sex_tips_hindi();
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function kamasutra_to_do_create() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                $respStatus = $response['status'];
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                    $position_id = $params['position_id'];
                    if ($params['user_id'] == "" || $params['position_id'] == "") {
                        $respStatus = 400;
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $resp = $this->SexeducationModel->kamasutra_to_do_create($user_id, $position_id);
                    }
                    json_output($respStatus, $resp);
                }
            }
        }
    }

    public function kamasutra_to_do_list() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'User Id cant empty');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->SexeducationModel->kamasutra_to_do_list($user_id);
                    }
                    kama_json_output($resp);
                }
            }
        }
    }

    public function kamasutra_favourite_create() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                $respStatus = $response['status'];
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                    $position_id = $params['position_id'];
                    if ($params['user_id'] == "" || $params['position_id'] == "") {
                        $respStatus = 400;
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $resp = $this->SexeducationModel->kamasutra_favourite_create($user_id, $position_id);
                    }
                    json_output($respStatus, $resp);
                }
            }
        }
    }

    public function kamasutra_favourite_list() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'User Id cant empty');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->SexeducationModel->kamasutra_favourite_list($user_id);
                    }
                    kama_json_output($resp);
                }
            }
        }
    }

    public function kamasutra_tried_create() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                $respStatus = $response['status'];
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                    $position_id = $params['position_id'];
                    if ($params['user_id'] == "" || $params['position_id'] == "") {
                        $respStatus = 400;
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $resp = $this->SexeducationModel->kamasutra_tried_create($user_id, $position_id);
                    }
                    json_output($respStatus, $resp);
                }
            }
        }
    }

    public function kamasutra_tried_list() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'User Id cant empty');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->SexeducationModel->kamasutra_tried_list($user_id);
                    }
                    kama_json_output($resp);
                }
            }
        }
    }

    public function kamasutra_flag() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->kamasutra_flag();
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_home() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->sex_store_home();
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_category() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->sex_store_category();
                }
                json_outputs($resp);
            }
        }
    }

    public function sex_store_subcategory() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['category_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $category_id = $params['category_id'];
                        $resp = $this->SexeducationModel->sex_store_subcategory($category_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_store_products() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['sub_category_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $sub_category_id = $params['sub_category_id'];
                        $resp = $this->SexeducationModel->sex_store_products($sub_category_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_store_related_prod() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['product_id'] == "" || $params['sub_category_id'] == "" || $params['category_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $product_id = $params['product_id'];
                        $category_id = $params['category_id'];
                        $sub_category_id = $params['sub_category_id'];
                        $resp = $this->SexeducationModel->sex_store_related_prod($product_id, $category_id, $sub_category_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_store_about_us() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->sex_store_about_us();
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_contact_us() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['name'] == "" || $params['message'] == "" || $params['mobile'] == "") {
                        $resp = array('status' => 400, 'message' => 'User Id cant empty');
                    } else {
                        $user_id = $params['user_id'];
                        $name = $params['name'];
                        $message = $params['message'];
                        $mobile = $params['mobile'];

                        $resp = $this->SexeducationModel->sex_store_contact_us($user_id, $name, $message, $mobile);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_country() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $resp = $this->SexeducationModel->sex_store_country();

                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_store_state() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['country'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $country = $params['country'];
                        $resp = $this->SexeducationModel->sex_store_state($country);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_store_get_quotes() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['pincode'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $pincode = $params['pincode'];
                        $resp = $this->SexeducationModel->sex_store_get_quotes($pincode);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_pincode_check() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['pincode'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $pincode = $params['pincode'];
                        $resp = $this->SexeducationModel->sex_store_pincode_check($pincode);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_cart_order() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
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
                        $resp = $this->SexeducationModel->sex_store_cart_order($user_id, $address_id, $product_id, $product_quantity, $product_price);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_store_cart_order_list() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->SexeducationModel->sex_store_cart_order_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_store_cart_order_details() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['order_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $order_id = $params['order_id'];
                        $resp = $this->SexeducationModel->sex_store_cart_order_details($user_id, $order_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_store_product_review() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
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
                        $resp = $this->SexeducationModel->sex_store_product_review($user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function edit_sex_store_product_review() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
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
                        $resp = $this->SexeducationModel->edit_sex_store_product_review($review_id,$user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_product_review_list() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->SexeducationModel->sex_store_product_review_list($user_id, $listing_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_store_product_review_likes() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->SexeducationModel->sex_store_product_review_likes($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_product_review_comment() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
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
                        $resp = $this->SexeducationModel->sex_store_product_review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_product_review_comment_like() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->SexeducationModel->sex_store_product_review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_product_review_comment_list() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->SexeducationModel->sex_store_product_review_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_store_review() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
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
                        $resp = $this->SexeducationModel->sex_store_review($user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function edit_sex_store_review() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
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
                        $resp = $this->SexeducationModel->edit_sex_store_review($review_id,$user_id, $listing_id, $rating, $review, $service);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_review_list() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $resp = $this->SexeducationModel->sex_store_review_list($user_id, $listing_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_store_review_likes() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->SexeducationModel->sex_store_review_likes($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_review_comment() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
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
                        $resp = $this->SexeducationModel->sex_store_review_comment($user_id, $post_id, $comment);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_review_comment_like() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['comment_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $comment_id = $params['comment_id'];
                        $resp = $this->SexeducationModel->sex_store_review_comment_like($user_id, $comment_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_store_review_comment_list() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->SexeducationModel->sex_store_review_comment_list($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_expert_character() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['type'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $type = $params['type'];
                        $resp = $this->SexeducationModel->sex_expert_character($type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_expert_add_question() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['user_name'] == "" || $params['question'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $user_name = $params['user_name'];
                        $user_image = $params['user_image'];
                        $question = $params['question'];
                        $age = $params['age'];
                        $post_location = $params['post_location'];
                        $resp = $this->SexeducationModel->sex_expert_add_question($user_id, $user_name, $user_image, $question, $age, $post_location);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_expert_add_reply() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['doctor_id'] == "" || $params['post_id'] == "" || $params['type'] == "" || $params['answer'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $doctor_id = $params['doctor_id'];
                        $post_id = $params['post_id'];
                        $type = $params['type'];
                        $answer = $params['answer'];

                        $resp = $this->SexeducationModel->sex_expert_add_reply($user_id, $doctor_id, $post_id, $type, $answer);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_expert_question_list() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $page = $params['page'];
                        $resp = $this->SexeducationModel->sex_expert_question_list($user_id, $page);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }

  public function sex_expert_question_list_web() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['page'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $page = $params['page'];
                        $resp = $this->SexeducationModel->sex_expert_question_list_web($user_id, $page);
                    }
                    json_healthwall($resp);
                }
            }
        }
    }


    public function sex_expert_question_details() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->SexeducationModel->sex_expert_question_details($user_id, $post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_expert_like() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "" || $params['user_image'] == "" || $params['user_name'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $user_image = $params['user_image'];
                        $user_name = $params['user_name'];
                        $post_user_id = $params['post_user_id'];


                        $resp = $this->SexeducationModel->sex_expert_like($user_id, $post_id, $user_image, $user_name, $post_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_expert_user_like_list() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {

                        $post_id = $params['post_id'];

                        $resp = $this->SexeducationModel->sex_expert_user_like_list($post_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_education_is_notify() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->SexeducationModel->sex_education_is_notify($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_education_hide() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->SexeducationModel->sex_education_hide($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_education_user_update() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['user_name'] == "" || $params['user_image'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $user_name = $params['user_name'];
                        $user_image = $params['user_image'];

                        $resp = $this->SexeducationModel->sex_education_user_update($user_id, $user_name, $user_image);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_education_user_check() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];

                        $resp = $this->SexeducationModel->sex_education_user_check($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function sex_education_delete() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->SexeducationModel->sex_education_delete($user_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function sex_education_reply_delete() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['answer_id'] == "" || $params['post_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $answer_id = $params['answer_id'];
                        $post_id = $params['post_id'];
                        $resp = $this->SexeducationModel->sex_education_reply_delete($answer_id, $post_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    ///added by zak for audio services 
    
     
     public function kamashastra_audio_list() {
        $this->load->model('SexeducationModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->SexeducationModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter all fields');
                    } else {
                        $user_id = $params['user_id'];
                       
                        $resp = $this->SexeducationModel->kamashastra_audio_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    

}
