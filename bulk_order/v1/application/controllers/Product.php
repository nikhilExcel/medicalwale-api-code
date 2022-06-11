<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller
{
    function __construct() 
    { 
        parent::__construct();
        $this->load->model('Product_model');
    }
    
    public function covid_msg(){
        json_output(200, array(
            'status' => 200,
            'message' => 'Alert:- Due to increase cases of COVID-19 across India and lockdown in few states, deliveries will be delayed and for few schools orders may not be accepted.'
        ));
    }

    public function search_unmap_distributor_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $keyword = $params['keyword'];
                if ($user_id !='') {
                    $response = $this->Product_model->search_unmap_distributor_list($user_id,$keyword);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function search_product_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $keyword = $params['keyword'];
                if ($user_id !='') {
                    $response = $this->Product_model->search_product_list($user_id,$keyword);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function search_distributor_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $keyword = $params['keyword'];
                if ($user_id !='') {
                    $response = $this->Product_model->search_distributor_list($user_id,$keyword);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }

    public function distributor_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                if ($user_id !='') {
                    $response = $this->Product_model->distributor_list($user_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }

    public function unmap_distributor_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                if ($user_id !='') {
                    $response = $this->Product_model->unmap_distributor_list($user_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }

    public function distributor_product_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $distributors = $params['distributors'];
                if ($user_id !='') {
                    $response = $this->Product_model->distributor_product_list($user_id,$distributors);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }

    public function hot_deals(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                if (array_key_exists("page",$params)){
                    $page = $params['page'];
                }
                else{
                    $page = '1';
                }
                if ($user_id !='') {
                    $response = $this->Product_model->hot_deals($user_id,$page);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }

    public function frequently_purchased_products(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                if (array_key_exists("page",$params)){
                    $page = $params['page'];
                }
                else{
                    $page = '1';
                }
                if ($user_id !='') {
                    $response = $this->Product_model->frequently_purchased_products($user_id,$page);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }

    public function top_selling_products_in_your_area(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                if ($user_id !='') {
                    $response = $this->Product_model->top_selling_products_in_your_area($user_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }

    public function new_apprivals(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                if ($user_id !='') {
                    $response = $this->Product_model->new_apprivals($user_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }

    public function deal_product_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                if (array_key_exists("page",$params)){
                    $page = $params['page'];
                }
                else{
                    $page = '1';
                }
                if ($user_id !='') {
                    $response = $this->Product_model->deal_product_list($user_id,$page);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function my_product_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $offset = $params['offset'];
                $perPage = $params['per_page'];
                
                if ($user_id !='' ) {
                    $response = $this->Product_model->my_product_list($user_id,$offset,$perPage);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    } 
    
    public function my_hire_product_details(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $product_id = $params['product_id'];
                
                if ($user_id !='' && $product_id!='') {
                    $response = $this->Product_model->my_hire_product_details($user_id,$product_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function my_product_details(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $product_id = $params['product_id'];
                
                if ($user_id !='' && $product_id!='') {
                    $response = $this->Product_model->my_product_details($user_id,$product_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function hire_product_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $pincode = $params['pincode'];
                $offset = $params['offset'];
                $perPage = $params['per_page'];
                
                if (array_key_exists("filter_category_id",$params)){
                    $filter_category_id = $params['filter_category_id'];
                }
                else{
                    $filter_category_id = '';
                }
                
                if (array_key_exists("filter_brand_id",$params)){
                    $filter_brand_id = $params['filter_brand_id'];
                }
                else{
                    $filter_brand_id = '';
                }
                
                if ($user_id!='' ) {
                    $response = $this->Product_model->hire_product_list($pincode,$offset,$perPage,$filter_category_id,$filter_brand_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function used_product_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $pincode = $params['pincode'];
                $offset = $params['offset'];
                $perPage = $params['per_page'];
                
                if (array_key_exists("filter_category_id",$params)){
                    $filter_category_id = $params['filter_category_id'];
                }
                else{
                    $filter_category_id = '';
                }
                
                if (array_key_exists("filter_brand_id",$params)){
                    $filter_brand_id = $params['filter_brand_id'];
                }
                else{
                    $filter_brand_id = '';
                }
                
                if ($user_id!='' ) {
                    $response = $this->Product_model->used_product_list($pincode,$offset,$perPage,$filter_category_id,$filter_brand_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    } 
    
    public function product_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                $category_slug = $params['category_slug'];
                $pincode = $params['pincode'];
                $offset = $params['offset'];
                $perPage = $params['per_page'];
                
                if (array_key_exists("filter_category_id",$params)){
                    $filter_category_id = $params['filter_category_id'];
                }
                else{
                    $filter_category_id = array();
                }
                
                if ($pincode !='' ) {
                    $response = $this->Product_model->product_list($category_slug,$pincode,$offset,$perPage,$filter_category_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    } 
    
    public function product_shoes_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                $category_slug = $params['category_slug'];
                $category_id = $params['category_id'];
                $pincode = $params['pincode'];
                $offset = $params['offset'];
                $perPage = $params['per_page'];
                
                if (array_key_exists("filter_category_id",$params)){
                    $filter_category_id = $params['filter_category_id'];
                }
                else{
                    $filter_category_id = array();
                }
                
                if ($category_slug !='' ) {
                    //$response = $this->Product_model->product_shoes_list($category_slug,$category_id,$pincode,$offset,$perPage,$filter_category_id);
                    //simple_json_output($response);
                    json_output(200, array(
                        'status' => 200,
                        'message' => 'success'
                    ));
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function product_list_by_pincode(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                $category_slug = $params['category_slug'];
                $pincode = $params['pincode'];
                
                if ($category_slug !='' ) {
                    //$response = $this->Product_model->product_list_by_pincode($category_slug,$pincode);
                    //simple_json_output($response);
                    json_output(200, array(
                        'status' => 200,
                        'message' => 'success'
                    ));
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    } 
    
    public function product_uniform_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                $category_slug = $params['category_slug'];
                $pincode = $params['pincode'];
                $school_id = $params['school_id'];
                $offset = $params['offset'];
                $perPage = $params['per_page'];
                
                if (array_key_exists("filter_category_id",$params)){
                    $filter_category_id = $params['filter_category_id'];
                }
                else{
                    $filter_category_id = array();
                }
                
                if ($category_slug !='' ) {
                    //$response = $this->Product_model->product_uniform_list($category_slug,$pincode,$school_id,$offset,$perPage,$filter_category_id);
                    //simple_json_output($response);
                    json_output(200, array(
                        'status' => 200,
                        'message' => 'success'
                    ));
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    } 
    
    public function product_dress_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                $category_slug = $params['category_slug'];
                $pincode = $params['pincode'];
                $offset = $params['offset'];
                $perPage = $params['per_page'];
                
                if ($category_slug !='' ) {
                    //$response = $this->Product_model->product_dress_list($category_slug,$pincode,$offset,$perPage);
                    //simple_json_output($response);
                    json_output(200, array(
                        'status' => 200,
                        'message' => 'success'
                    ));
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    } 
    
    public function home_book_list(){ 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                //$response = $this->Product_model->home_book_list(); 
                //simple_json_output($response);
                    json_output(200, array(
                        'status' => 200,
                        'message' => 'success'
                    ));
            }
        }
    }
     
    public function home_stationary_list(){ 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                //$response = $this->Product_model->home_stationary_list(); 
                //simple_json_output($response);
                    json_output(200, array(
                        'status' => 200,
                        'message' => 'success'
                    ));
            }
        }
    }
    
    public function home_fancy_list(){ 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                //$response = $this->Product_model->home_fancy_list(); 
                //simple_json_output($response);
                    json_output(200, array(
                        'status' => 200,
                        'message' => 'success'
                    ));
            }
        }
    }
    
    public function product_book_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                // $category_id = $params['category_id'];
                $category_slug = $params['category_slug'];
                $pincode = $params['pincode'];
                $offset = $params['offset'];
                $perPage = $params['per_page'];
                
                if (array_key_exists("category_id",$params)){
                    $category_id = $params['category_id'];
                }
                else{
                    $category_id = array();
                }
                
                if ($category_slug !='' ) {
                    //$response = $this->Product_model->product_book_list($category_slug,$pincode,$offset,$perPage,$category_id);
                    //simple_json_output($response);
                    json_output(200, array(
                        'status' => 200,
                        'message' => 'success'
                    ));
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }  
    
    public function hire_product_details(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $slug = $params['slug'];
                $product_id = $params['product_id'];
                if ($product_id !='' ) {
                    $response = $this->Product_model->hire_product_details($slug,$product_id);
                    simple_json_output($response);
                    
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    } 
    
    public function used_product_details(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                $slug = $params['slug'];
                $product_id = $params['product_id'];
                if ($product_id !='' ) {
                    $response = $this->Product_model->used_product_details($slug,$product_id);
                    simple_json_output($response);
                    
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    } 
    
    public function product_details(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                $slug = $params['slug'];
                $category_slug = $params['category_slug'];
                $product_id = $params['product_id'];
                $pincode = $params['pincode'];
                $other_id = $params['other_id'];
                if (array_key_exists("address_id",$params)){
                    $address_id = $params['address_id'];
                }
                else{
                    $address_id = '';
                }
                
                if ($product_id !='' ) {
                    $response = $this->Product_model->product_details($slug,$category_slug,$product_id,$pincode,$address_id,$other_id);
                    simple_json_output($response);
                    
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    } 
    
    public function cart_product_details(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                $slug = $params['slug'];
                $category_slug = $params['category_slug'];
                $product_id = $params['product_id'];
                $pincode = $params['pincode'];
                $quantity = $params['quantity'];
                if (array_key_exists("address_id",$params)){
                    $address_id = $params['address_id'];
                }
                else{
                    $address_id = '';
                }
                if ($slug !='' ) {
                    $response = $this->Product_model->cart_product_details($slug,$category_slug,$product_id,$pincode,$quantity,$address_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    } 
    
    public function cart_product_details_by_lat_lng(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                $slug = $params['slug'];
                $category_slug = $params['category_slug'];
                $product_id = $params['product_id'];
                $pincode = $params['pincode'];
                $quantity = $params['quantity'];
                if (array_key_exists("address_id",$params)){
                    $address_id = $params['address_id'];
                }
                else{
                    $address_id = '';
                }
                
                if ($slug !='' ) {
                    $response = $this->Product_model->cart_product_details_by_lat_lng($slug,$category_slug,$product_id,$pincode,$quantity,$address_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
  
  
    public function product_other_seller(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                $slug = $params['slug'];
                $category_slug = $params['category_slug'];
                $product_id = $params['product_id'];
                $pincode = $params['pincode'];
                $seller_id = $params['seller_id'];
                
                if ($slug !='' ) {
                    //$response = $this->Product_model->product_other_seller($slug,$category_slug,$product_id,$pincode,$seller_id);
                    //simple_json_output($response);
                    json_output(200, array(
                        'status' => 200,
                        'message' => 'success',
                        'data'=>array()
                    ));
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    } 
  
       
    public function product_warehouse_checker(){
      $this->Product_model->product_warehouse_checker();
    } 
    
    public function vendor_details(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                $slug = $params['slug'];
                $vendor_id = $params['vendor_id'];
                
                
                if ($slug !='' && $vendor_id !='') {
                    $response = $this->Product_model->vendor_details($slug,$vendor_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function vendor_product_catrgory(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                $slug = $params['slug'];
                $vendor_id = $params['vendor_id'];
                
                
                if ($slug !='' && $vendor_id !='') {
                    $response = $this->Product_model->vendor_product_catrgory($slug,$vendor_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function vendor_product(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                $category_id = $params['category_id'];
                $vendor_id = $params['vendor_id'];
                $offset = $params['offset'];
                $perPage = $params['per_page'];
                $filter['keyword'] = $params['keyword'];
                
                
                if ($category_id !='' && $vendor_id !='') {
                    //$response = $this->Product_model->vendor_product($category_id,$vendor_id,$offset,$perPage,$filter);
                   // simple_json_output($response);
                    json_output(200, array(
                        'status' => 200,
                        'message' => 'success',
                        'data'=>array()
                    ));
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    public function educational_product_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                
                $category_slug = $params['category_slug'];
                $pincode = $params['pincode'];
                $offset = $params['offset'];
                $perPage = $params['per_page'];
                
                if (array_key_exists("category_id",$params)){
                    $category_id = $params['category_id'];
                }
                else{
                    $category_id = array();
                }
                
                if ($category_slug !='' ) {
                    //$response = $this->Product_model->educational_product_list($category_slug,$pincode,$offset,$perPage,$category_id);
                    //simple_json_output($response);
                    json_output(200, array(
                        'status' => 200,
                        'message' => 'success'
                    ));
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    } 
    
     public function product_search_list(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params  = json_decode(file_get_contents('php://input'), TRUE);
                
                $category_slug = $params['category_slug'];
                $pincode = $params['pincode'];
                
                if (array_key_exists("filter_category_id",$params)){
                    $filter_category_id = $params['filter_category_id'];
                }
                else{
                    $filter_category_id = array();
                }
                
                if ($category_slug !='' ) {
                    $response = $this->Product_model->product_search_list($pincode,$category_slug);
                    simple_json_output($response);
                    /*json_output(200, array(
                        'status' => 200,
                        'message' => 'success'
                    ));*/
                } else {
                    json_output(400, array(
                        'status' => 400,
                        'message' => 'Enter Username or Password'
                    ));
                }
            }
        }
    }
    
    
    
     
}
