<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Healthmall extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('LoginModel');
        $this->load->model('HealthmallModel');
        $this->load->model('LedgerModel');
        
        date_default_timezone_set('Asia/Kolkata');
    } 
 
    public function index() { 
        json_output(400, array(
            'message' => 'Bad request.'
        ));
    }
    
    public function top_menu() { 
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if(array_key_exists('user_id',$params)){
                    $user_id = $params['user_id'];
                } else {
                    $user_id = "";
                }
                if ($user_id  == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter user_id'
                    );
                } else {
                    $response = $this->HealthmallModel->top_menu($user_id);
                    $resp = array(
                        'status' => 200,
                        'message' => 'success',
                        'description' => $response['description'],
                        'data' => $response['top_menu']
                    );
                }
                simple_json_output($resp);
            } else {
                $resp = array(
                    'status' => 400,
                    'message' => 'Authentication failed'
                );
                simple_json_output($resp);
            }
        }
    }
    
    public function get_categories() { 
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if(array_key_exists('user_id',$params)){
                    $user_id = $params['user_id'];
                } else {
                    $user_id = "";
                }
                if ($user_id  == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter user_id'
                    );
                } else {
                    if(array_key_exists('v_id',$params)){
                    $brand_id = $params['v_id'];
                } else {
                    $brand_id = "";
                }
                    $response = $this->HealthmallModel->get_categories($user_id,$brand_id);
                    $resp = array(
                        'status' => 200,
                        'message' => 'success',
                        // 'description' => $response['description'],
                        'data' => $response['data']
                    );
                }
                simple_json_output($resp);
            } else {
                $resp = array(
                    'status' => 400,
                    'message' => 'Authentication failed'
                );
                simple_json_output($resp);
            }
        }
    }
    
    public function get_brands() { 
        
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if(array_key_exists('user_id',$params)){
                    $user_id = $params['user_id'];
                } else {
                    $user_id = "";
                }
                if(array_key_exists('page_no',$params)){
                    $page_no = $params['page_no'];
                } else {
                    $page_no = "";
                }
                if(array_key_exists('per_page',$params)){
                    $per_page = $params['per_page'];
                } else {
                    $per_page = "";
                }
                if(array_key_exists('search',$params)){
                    $search = $params['search'];
                } else {
                    $search = "";
                }
                
                if ($user_id  == "" || $page_no  == "" || $per_page == "" ) {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter user_id, page_no and _per page'
                    );
                } else {
                    $response = $this->HealthmallModel->get_brands($user_id, $page_no, $per_page,$search);
                    if(sizeof($response['data'] > 0)){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' => $response
                        );
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'No brands available'
                        );
                    }
                    
                }
                simple_json_output($resp);
            } else {
                $resp = array(
                    'status' => 400,
                    'message' => 'Authentication failed'
                );
                simple_json_output($resp);
            }
        }
    }
    
    public function get_offers()
    {
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
                    if ($params['user_id'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else{
                    
                      $user_id = $params['user_id'];
                    
                    $result = $this->HealthmallModel->get_offers($user_id);
                      if(sizeof($result['data'] > 0)){
                        $resp =  $result;
                        
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'No brands available'
                        );
                    }
                    
                    }
                    simple_json_output($resp);
                }
                else
                {
                   $resp = array(
                    'status' => 400,
                    'message' => 'Authentication failed'
                );
                simple_json_output($resp); 
                }
            }
        }
    }
    public function get_brand_images()
    {
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
                     if ($params['user_id'] == "" || $params['v_id'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else{
                    $user_id = $params['user_id'];
                    $vid = $params['v_id'];
                    $result = $this->HealthmallModel->get_brand_images($user_id,$vid);
                    $response = $this->HealthmallModel->top_menu_brand($user_id);
                   
                    $resp = array(
                        "status" => 200,
                        "message" => "Success",
                        "data" => array_merge($result,$response)
                    );
                    
                    
                    }
                    simple_json_output($resp); 
                }
            }
        }
    }
    
   
     public function get_vendor_detail()
    {
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
                     if ($params['user_id'] == "" || $params['v_id'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else{
                    $user_id = $params['user_id'];
                    $vid = $params['v_id'];
                  
                    $response = $this->HealthmallModel->get_vendor_detail($user_id,$vid);
                   if(count($response) > 0)
                   {
                    $resp = array(
                        "status" => 200,
                        "message" => "Success",
                        "data" => $response
                    );
                   }
                   else
                   {
                     $resp = array(
                            'status' => 400,
                            'message' => 'No brands Details'
                        ); 
                   }
                    
                    }
                    simple_json_output($resp); 
                }
            }
        }
    }
    
    
    public function get_product_list()
     {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                         if ($params['page_no'] == "" || $params['per_page'] == "" )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter page number(page_no) and per page(per_page) fields');
                        }
                        elseif ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                        else
                        {
                            $page_no = $params['page_no'];
                            $per_page = $params['per_page'];
                            if(array_key_exists('brands',$params))
                                {
                                   $brands = $params['brands'];
                                } 
                            else 
                                {
                                  $brands = "";
                                }
                            if(array_key_exists('cat_id',$params))
                                {
                                   $category_ids = $params['cat_id'];
                                } 
                            else 
                                {
                                  $category_ids = "";
                                }
                            if(array_key_exists('sort',$params))
                                {
                                   $sort = $params['sort'];
                                } 
                            else 
                                {
                                  $sort= "";
                                }  
                            if(array_key_exists('price_min',$params))
                                {
                                   $price_min = $params['price_min'];
                                } 
                            else 
                                {
                                  $price_min= "";
                                }  
                            if(array_key_exists('price_max',$params))
                                {
                                    $price_max = $params['price_max'];
                                } 
                            else 
                                {
                                  $price_max = "";
                                }      
                                
                                if(array_key_exists('offer',$params))
                                {
                                    $offer = $params['offer'];
                                } 
                            else 
                                {
                                  $offer = "";
                                }  
                                if(array_key_exists('best_seller',$params))
                                {
                                    $best_seller = $params['best_seller'];
                                } 
                            else 
                                {
                                  $best_seller = "";
                                }  
                                 if(array_key_exists('gender',$params))
                                {
                                    $gender = $params['gender'];
                                } 
                            else 
                                {
                                  $gender = "";
                                }  
                                
                            $user_id = $params['user_id'];
                            
                            $result = $this->HealthmallModel->get_product_list($brands,$page_no,$per_page,$category_ids,$sort,$user_id,$price_min,$price_max,$offer,$best_seller,$gender);
                            $resp = $result;
                            
                            
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
    }
     public function get_product_details()
     {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                         if ($params['pd_id'] == "" )
                        {
                            $resp = array('status' => 400, 'message' => 'Please Enter Product ID');
                        }
                        elseif ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'Please Enter User ID');
                        }
                        else
                        {
                            $pd_id = $params['pd_id'];
                            $user_id = $params['user_id'];
                            if(array_key_exists('try_on',$params))
                                {
                                    $try_on = $params['try_on'];
                                } 
                            else 
                                {
                                  $try_on = "0";
                                }  
                            $result = $this->HealthmallModel->get_product_details($pd_id,$user_id,$try_on);
                            if(count($result) > 0)
                               {
                                $resp = array(
                                    "status" => 200,
                                    "message" => "Success",
                                    "data" => $result
                                );
                               }
                               else
                               {
                                 $resp = array(
                                        'status' => 400,
                                        'message' => 'No Product Details'
                                    ); 
                               }
                    
                            
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
    }
    
      public function get_brand_details()
     {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'Please Enter User ID');
                        }
                        else
                        {
                            $user_id = $params['user_id'];
                            $cat_id = $params['cat_id'];
                            $result = $this->HealthmallModel->get_brand_details($cat_id,$user_id);
                            if(count($result) > 0)
                               {
                                $resp = array(
                                    "status" => 200,
                                    "message" => "Success",
                                    "data" => $result
                                );
                               }
                               else
                               {
                                 $resp = array(
                                        'status' => 400,
                                        'message' => 'No Brand Details'
                                    ); 
                               }
                    
                            
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
    }
    
      public function add_to_wishlist()
     {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['user_id'] == "" || $params['product_id'] == "" || $params['user_id'] == 0 || $params['product_id'] == 0)
                        {
                            $resp = array('status' => 400, 'message' => 'please enter user_id and product_id');
                        }
                        else
                        {
                            $user_id = $params['user_id'];
                            $product_id = $params['product_id'];
                            $type = $params['type'];
                            if(array_key_exists('try_on',$params))
                                {
                                    $try_on = $params['try_on'];
                                } 
                            else 
                                {
                                  $try_on = "0";
                                }   
                            $resp = $this->HealthmallModel->add_to_wishlist($product_id,$type,$user_id,$try_on);
                           
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
    }
    
     public function get_user_wishlist()
     {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['page_no'] == "" || $params['per_page'] == "" )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter page number(page_no) and per page(per_page) fields');
                        }
                        elseif ($params['user_id'] == "" || $params['user_id'] == 0 )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter user_id');
                        }
                        else
                        {
                            $user_id = $params['user_id'];
                            $page_no = $params['page_no'];
                            $per_page = $params['per_page'];
                            $resp = $this->HealthmallModel->get_user_wishlist($user_id,$per_page,$page_no);
                           
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
    }
    
     public function add_to_cart()
     {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        
                       
                    
                    if ($params['user_id'] == 0 || $params['product_id'] == 0 || $params['user_id'] == "" || $params['product_id'] == "" || $params['quantity'] == "" || $params['quantity'] == 0 || $params['offer_id'] == "") {
                        $resp = array(
                            "status" => 400,
                            "message" => "please enter user_id, product_id, quantity and offer_id"
                        );
                    } else if ($params['quantity'] < 1) {
                        $resp = array(
                            "status" => 400,
                            "message" => "Quantity must be greater than 0 "
                        );
                    } 
                   
                        else
                        {
                            
                        $user_id    = $params['user_id'];
                        $product_id = $params['product_id'];
                        $quantity   = $params['quantity'];
                        $offer_id   = $params['offer_id'];
                        $referal_code   = $params['referal_code'];
                        $variable_pd_id   = $params['variable_pd_id'];
                        $deal_id   = $params['deal_id'];
                        $deal_type   = $params['deal_type'];
                        $sku    = $params['sku'];
                        $type   = $params['type'];
                        if(empty($referal_code)){
                            $referal_code = 0;
                        }
                        
                        if(empty($variable_pd_id)){
                            $variable_pd_id = 0;
                        }
                        
                        if(empty($sku)){
                            $sku = 0;
                        }
                            if(array_key_exists('try_on',$params))
                                {
                                    $try_on = $params['try_on'];
                                } 
                            else 
                                {
                                  $try_on = "0";
                                }   
                            
                            $resp = $this->HealthmallModel->add_to_cart($user_id, $product_id, $quantity, $offer_id,$referal_code,$variable_pd_id,$sku,$type,$deal_id,$deal_type,$try_on);
                           
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
    }
     
     public function get_user_cart()
     {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['user_id'] == "" || $params['user_id'] == 0 )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter user_id');
                        }
                        else
                        {
                            $user_id = $params['user_id'];
                            
                            $resp = $this->HealthmallModel->get_user_cart($user_id);
                           
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
    }
     
     
     
         public function get_user_cart_wishlist_count()
     {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['user_id'] == "" || $params['user_id'] == 0 )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter user_id');
                        }
                        else
                        {
                            $user_id = $params['user_id'];
                            
                            $resp = $this->HealthmallModel->get_user_cart_wishlist_count($user_id);
                           
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
    }
     public function remove_all_cart()
     {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['user_id'] == "" || $params['user_id'] == 0 )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter user_id');
                        }
                        else
                        {
                            $user_id = $params['user_id'];
                            
                            $resp = $this->HealthmallModel->remove_all_cart($user_id);
                           
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
    } 
      
      
      public function get_similar_items()
     {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                         if ($params['page_no'] == "" || $params['per_page'] == "" )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter page number(page_no) and per page(per_page) fields');
                        }
                        elseif ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                        elseif ($params['pd_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                        else
                        {
                            $page_no = $params['page_no'];
                            $per_page = $params['per_page'];
                            $user_id = $params['user_id'];
                            $pd_id = $params['pd_id'];
                            
                            $resp = $this->HealthmallModel->get_similar_items($user_id,$pd_id,$page_no,$per_page);
                           
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
    } 
    
      public function get_similar_brand()
     {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                         if ($params['page_no'] == "" || $params['per_page'] == "" )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter page number(page_no) and per page(per_page) fields');
                        }
                        elseif ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                        elseif ($params['v_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                        else
                        {
                            $page_no = $params['page_no'];
                            $per_page = $params['per_page'];
                            $user_id = $params['user_id'];
                            $pd_id = $params['v_id'];
                            
                            $resp = $this->HealthmallModel->get_similar_brand($user_id,$pd_id,$page_no,$per_page);
                           
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
    } 
    
     function check_product_availability_post()
    {
       $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
               $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                         if ($params['user_id'] == "")
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                        elseif ($params['pd_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter Product ID');
                        }
                        elseif ($params['pincode'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter Pincode');
                        }
                        else
                        {
                           
                            $pincode = $params['pincode'];
                            $user_id = $params['user_id'];
                            $pd_id = $params['pd_id'];
                            
                            $resp1 = $this->HealthmallModel->checkProductAvailibility($user_id,$pd_id,$pincode);
                            $count        = $resp1;
                            if($count > 0)
                            {
                                 $resp = array(
                                                "status" => 200,
                                                "message" => "Product Available",
                                                "p_id"=> $params['pd_id'],
                                                "pincode"=> $params['pincode']
                                             );
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "Result not found",
                                                "p_id"=> $params['pd_id'],
                                                "pincode"=> $params['pincode']
                                             );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
            
        }
    }
    
    function get_offer_products()
    {
       $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
              
                $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                         if ($params['user_id'] == "")
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                        elseif ($params['offer_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter Offer ID');
                        }
                       
                        else
                        {
                           
                            $offer_id = $params['offer_id'];
                            $user_id = $params['user_id'];
                           $resp1=array();
                            $resp1[] = $this->HealthmallModel->get_offer_products($offer_id);
                            $count        = count($resp1);
                            if($count > 0)
                            {
                                 $resp = array(
                                                "status" => 200,
                                                "message" => "Success",
                                                "data"=>$resp1
                                             );
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "No Record Found",
                                                "data"=> array()
                                             );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
              
           
        }
    }
    
     function get_num_ratings()
    {
        
         $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
              
                $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['pd_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter Product ID');
                        }
                       
                        else
                        {
                           
                            $pd_id = $params['pd_id'];
                           
                            $resp1  = $this->HealthmallModel->get_num_ratings($pd_id);
                            $count        = count($resp1);
                            if($count > 0)
                            {
                                 $resp = array(
                                                "status" => 200,
                                                "message" => "Success",
                                                "data"=>$resp1
                                             );
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "No Record Found",
                                                "data"=> array()
                                             );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
              
           
        }
        
        
        
        
        
      
    }

    
     public function place_order()
     {
           
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
                    $data['orders'] = $this->input->post('products');
                        $orders = json_decode($data['orders'],TRUE);
                      if ($this->input->post('user_id') == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter user_id'
                        );
                        } 
                        elseif ($this->input->post('address_id') == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'Please Enter Address ID');
                        }
                        elseif ($this->input->post('member_id') == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'Please Enter Member ID');
                        }
                         elseif ($data['orders'] == "" )
                        {
                            $resp = array('status' => 400, 'message' => 'Please Enter Order Details');
                        }
                     else {
                        $data['cust_id'] = $this->input->post('user_id');
                        $data['address_id'] = $this->input->post('address_id');
                        $data['member_id'] = $this->input->post('member_id');
                        $data['payment_method'] = $this->input->post('payment_method');
                        $referral_codes = $resp = $allOrders = array();
                        $allowed = $this->HealthmallModel->placing_order_allowed($data);
                        
                        $referral_code['allowedArray'] = $allowed['allowedArray'];
                        $referral_code['notAllowedArray'] = $allowed['notAllowedArray'];
                        
                        if($allowed['status'] == 200)
                        {
                            
                        $address_details=$this->HealthmallModel->address_detail($data['cust_id'],$data['address_id']);
                                  if(!empty($address_details))
                                  {
                                  
                                  $data['name']  = $address_details['name'];
                                  $data['mobileno']   = $address_details['mobile'];
                                  $data['pincode']  = $address_details['pincode'];
                                  $data['address']   = $address_details['address1'];
                                  $data['address1']   = $address_details['address2'];
                                  $data['address_id']   = $data['address_id'];
                                  $data['lat']   = $address_details['lat'];
                                  $data['lng']   = $address_details['lng'];
                                  $data['landmark']  = $address_details['landmark'];
                                  $data['city']   = $address_details['city'];
                                  $data['state'] = $address_details['state'];
                                  $data['is_website'] = 0;
                                  for($o=0;$o<sizeof($orders['orders']);$o++)
                                     {
                                        $pro = $orders['orders'][$o];
                                        $data['products'] = $orders['orders'][$o];
                                        if(!empty($orders['orders'][$o]['chc']))
                                          {
                                            $chc = $orders['orders'][$o]['chc'];
                                          } 
                                        else 
                                          {
                                            $chc = 0;
                                          }
                            
                            
                    //          print_r($chc);
                    //      die();
                            $data['total_products'] = count($pro['products']);
                    //      print_r($data['total_products']);die;
                    
                    
                    // place orde
                            for($i=0;$i<$data['total_products'];$i++){
                                $data['product_id'.$i]   = $pro['products'][$i]['id'];
                                $data['product_name'.$i]   = $pro['products'][$i]['name'];
                                $data['product_qty'.$i]  = $pro['products'][$i]['quantity'];
                                $data['product_price'.$i]   = $pro['products'][$i]['price'];
                                $data['total_products_price'.$i]   = $pro['products'][$i]['price'] * $pro['products'][$i]['quantity'];
                                $data['vendor_id'.$i]   = $pro['products'][$i]['v_id'];
                                $data['chc'.$i]   = $chc;
                                $data['vendor_name'.$i]   = $pro['products'][$i]['v_name'];
                                $data['is_offer'.$i]   = $pro['products'][$i]['is_offer'];
                                $data['is_deal'.$i]   = $pro['products'][$i]['is_deal'];
                                if($pro['products'][$i]['is_offer'] == 1){
                                    $data['offer_id'.$i]   = $pro['products'][$i]['offer_id'];
                                    $data['offer_price'.$i]   = $pro['products'][$i]['offer_price'];
                                    $data['offer_total'.$i] = $pro['products'][$i]['offer_price'] * $pro['products'][$i]['quantity'];
                                }else{
                                    $data['offer_id'.$i]   = 0;
                                  //  $data['offer_price'.$i]   = $pro['products'][$i]['price'];
                                  //  $data['offer_total'.$i] = $data['total_products_price'.$i];
                                  $data['offer_price'.$i]   = 0;
                                    $data['offer_total'.$i] = 0;

                                }
                                if($pro['products'][$i]['is_deal'] == 1){
                                    $data['deal_id'.$i]   = $pro['products'][$i]['deal_id'];
                                    $data['deal_price'.$i]   = $pro['products'][$i]['deal_price'];
                                    $data['deal_total'.$i] = $pro['products'][$i]['deal_price'] * $pro['products'][$i]['quantity'];
                                }else{
                                    $data['deal_id'.$i]   = 0;
                                  //  $data['offer_price'.$i]   = $pro['products'][$i]['price'];
                                  //  $data['offer_total'.$i] = $data['total_products_price'.$i];
                                  $data['deal_price'.$i]   = 0;
                                    $data['deal_total'.$i] = 0;

                                }
                                //  referral_code
                                if(!empty($pro['products'][$i]['referral_code']) && $pro['products'][$i]['referral_code'] != 0){
                                    //echo "referral code not empty its".$pro['products'][$i]['referral_code'];
                                    $data['referral_code'.$i] = $pro['products'][$i]['referral_code'];
                                } else {
                                   // echo "referral code empty";
                                    $data['referral_code'.$i] = 0;
                                }
                                
                                //  variable_pd_id
                                if(!empty($pro['products'][$i]['variable_pd_id'])){
                                    //echo "referral code not empty its".$pro['products'][$i]['referral_code'];
                                    $data['variable_pd_id'.$i] = $pro['products'][$i]['variable_pd_id'];
                                } else {
                                   // echo "referral code empty";
                                    $data['variable_pd_id'.$i] = 0;
                                }
                                
                              //  sku
                                if(!empty($pro['products'][$i]['sku'])){
                                    //echo "referral code not empty its".$pro['products'][$i]['referral_code'];
                                    $data['sku'.$i] = $pro['products'][$i]['sku'];
                                } else {
                                   // echo "referral code empty";
                                    $data['sku'.$i] = 0;
                                }
                                
                            }
                            $amt=0;
                            $offer_amt=0;
                            $deal_amt=0;
                            for($i=0;$i<$data['total_products'];$i++){
                                $amt += $data['total_products_price'.$i];
                                $offer_amt += $data['offer_total'.$i];
                                $deal_amt += $data['deal_total'.$i];
                            }
                            $data['amount']   = $amt;
                            $data['offer_amount'] = $offer_amt;
                            $data['deal_amount'] = $deal_amt;
                            $data['savings'] = $amt - ($offer_amt+$deal_amt);
                            $data['number'] = $o;
                            // print_r($pro);die;
                            $products = $pro;
                            $result[] = $this->HealthmallModel->place_order($data, $allowed,$products);
                                
                            
                        }
                                  $original_total_price = 0;
                                  $offer_total_price = 0;
                                   $deal_total_price = 0;
                                  $saved_amount = 0;
                                  $ledger = $response = $orders = $order = $productDetails = $orderDetails = array();
                                  
                                   $original_total_price = 0;
                        $offer_total_price = 0;
                         $deal_total_price = 0;
                        $saved_amount = 0;
                        $ledger = $response = $orders = $order = $productDetails = $orderDetails = array();
                        for($j=0;$j<sizeof($result);$j++){
                            if($result[$j]['status'] == 'false'){
                                $resp = array(
                                "status" => "400",
                                "message" => "something went wrong"
                            );
                               simple_json_output($resp);
                            } else {
                              //  print_r($result[$j]); die();
                                 $original_total_price = $result[$j]['original_total_price'] + $original_total_price;
                                 $offer_total_price = $result[$j]['offer_total_price'] + $offer_total_price;
                                 $deal_total_price = $result[$j]['deal_total_price'] + $deal_total_price;
                                 $saved_amount = $result[$j]['saved_amount'] + $saved_amount;
                                 $orderDetails = $result[$j]['order_details']; 
                                 
                                 if(sizeof($result[$j]['ledger']) > 0){
                                     $ledger[] = $result[$j]['ledger']; 
                                 }
                                 unset($result[$j]['ledger']); 
                               $p = 2;
                            //   print_r($result[$j]['products'][$p]); die();
                                 $productDetails = $result[$j]['products']['products'];
                                //  print_r($productDetails); die();
                                for($i=0;$i<sizeof($productDetails);$i++){
                                    
                                  
                                    $res = $this->HealthmallModel->get_product_image($productDetails[$i]['id']);
                                   // print_r($productDetails); die();
                                    if(sizeof($res) > 0){
                                        $productDetails[$i]['pd_photo_1'] = $res['pd_photo_1'];
                                    }else{
                                        $productDetails[$i]['pd_photo_1'] = "";
                                    }
                                    
                                    
                                    if(array_key_exists('variable_pd_id', $productDetails[$i])  && $productDetails[$i]['variable_pd_id'] > 0){
                                    //   print_r($productDetails); die();
                                      $res = $this->HealthmallModel->get_variable_product_image($productDetails[$i]['variable_pd_id']);
                                      $productDetails[$i]['pd_photo_1'] = $res['image'];
                                      $reduce_product_quantity = $this->HealthmallModel->variable_quantity_bestseller($productDetails[$i]['id'],$productDetails[$i]['variable_pd_id'],$productDetails[$i]['quantity']);
                                   } else {
                                    
                                    $reduce_product_quantity = $this->HealthmallModel->quantity_bestseller($productDetails[$i]['id'],$productDetails[$i]['quantity']);
                                      
                                  }
                                }
                                
                                if(sizeof($ledger) > 0){
                                  
                                    $final_ledger[] = $ledger;
                                } else {
                                    $final_ledger = (object)[];
                                }
                                //  die();
                                 $order = array('order_details' => $orderDetails, 'products' => $productDetails, 'ledger' => $final_ledger);
                                 $orders[] = $order;
                           
                             
                            }
                             
                        }
                        $resp['status'] = "200";
                    
                        $resp['message'] = "Order Created Succesfully";
                        $resp['original_total_price'] = $original_total_price;
                        $resp['offer_total_price'] = $offer_total_price;
                        $resp['deal_total_price'] = $deal_total_price;
                        $resp['saved_amount'] = $saved_amount;
                        $resp['referral_codes'] = $referral_code;
                        $resp['ledger'] = $ledger;
                        $resp['orders'] = $orders;
                        
                                  
                                  
                                  
                                  }
                                  else
                                  {
                                      $resp = array('status'=>"400", 
                                                 'message' => "Please Select Proper Address",
                                                );
                                  }
                       
                    
            
            
                      
                    }
                   
                   
                }
                simple_json_output($resp);
            }
        }
        }
    }
    
      public function call_post_order()
      {
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
                    $user_id  = $this->input->post('user_id');
                    $amount = $this->input->post('amount');
                    $transaction_id = $this->input->post('transaction_id');
                    $transaction_status = $this->input->post('transaction_status');
                    $payment_method = $this->input->post('payment_method');
                    $ledger = json_decode($this->input->post('ledger'),TRUE);
                    if($user_id == "" || $amount == "" || ($transaction_id == "" && $transaction_status != 3 ) || ( $transaction_status != 3 && sizeof($ledger) < 1) || $transaction_status == "")
                    {
                        $resp = array(
                            "status" => 400,
                            "message" => "please enter user_id,  amount,  transaction_id,  transaction_status,  ledger"
                        );
                     }
                     else
                     {
                         $call_post_order = $this->HealthmallModel->call_post_order($user_id, $amount, $transaction_id, $transaction_status, $ledger,$payment_method);
                         if($call_post_order['status'] == 1 || $call_post_order['status'] == 2){
                          //  DECLINED
                            // FAILURE
                            // SUCCESS 
                            $description = "";
                            if($transaction_status == 1){
                                $description = "SUCCESS";
                            }  else if($transaction_status == 2){
                                $description = "FAILURE";
                            } else if($transaction_status == 3){
                                $description = "DECLINED";
                            }
                            $resp = array('status'=> 200 , 'message' => 'success', 'description' => $description);
                        }  else {
                            $resp = array('status'=> 400 , 'message' => 'SOmething went wrong');
                        }
                     }
                simple_json_output($resp);
            }
        }
        }
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
    }
    
    
      public function hm_menu_list1()
    {
        
         $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
              
                $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                       
                        else
                        {
                           
                            $user_id = $params['user_id'];
                           
                            $resp1  = $this->HealthmallModel->hm_menu_list1($user_id);
                            $count        = count($resp1);
                            if($count > 0)
                            {
                                 $resp = array(
                                                "status" => 200,
                                                "message" => "Success",
                                                "data"=>$resp1
                                             );
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "No Record Found",
                                                "data"=> array()
                                             );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
              
           
        }
        
        
        
        
        
      
    }
    
    public function hm_menu_list2()
    {
        
         $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
              
                $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                       
                        else
                        {
                           
                            $user_id = $params['user_id'];
                            $page_no = $params['page_no'];
                            $page_size = $params['page_size'];
                            
                            $resp1  = $this->HealthmallModel->hm_menu_list2($user_id,$page_no,$page_size);
                            $count        = count($resp1);
                            if($count > 0)
                            {
                                 $resp = array(
                                                "status" => 200,
                                                "message" => "Success",
                                                "data"=>$resp1
                                             );
                                           
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "No Record Found",
                                                "data"=> array()
                                             );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
              
           
        }
        
        
        
        
        
      
    }
     public function hm_menu_list3()
    {
        
         $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
              
                $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                       
                        else
                        {
                           
                            $user_id = $params['user_id'];
                           
                            $resp1  = $this->HealthmallModel->hm_menu_list3($user_id);
                            $count        = count($resp1);
                            if($count > 0)
                            {
                                 $resp = array(
                                                "status" => 200,
                                                "message" => "Success",
                                                "data"=>$resp1
                                             );
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "No Record Found",
                                                "data"=> array()
                                             );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
              
           
        }
        

    }
     public function hm_menu_list4()
    {
        
         $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
              
                $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                       
                        else
                        {
                           
                            $user_id = $params['user_id'];
                            $page_no = $params['page_no'];
                            $page_size = $params['page_size'];
                            $resp1  = $this->HealthmallModel->hm_menu_list4($user_id,$page_no,$page_size);
                            $count        = count($resp1);
                            if($count > 0)
                            {
                                 $resp = array(
                                                "status" => 200,
                                                "message" => "Success",
                            "image" => "https://d2c8oti4is0ms3.cloudfront.net/images/hm_deal/Noticebanner.jpeg",
                                                "data"=>$resp1
                                             );
                                           
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "No Record Found",
                            "image" => "https://d2c8oti4is0ms3.cloudfront.net/images/hm_deal/Noticebanner.jpeg",
                                                "data"=> array()
                                             );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
              
           
        }
        
        
        
        
        
      
    }
    
     public function hm_menu_sponor()
    {
        
         $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
              
                $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                         if ($params['page_no'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter page number');
                        }
                       
                        else
                        {
                           
                            $user_id = $params['user_id'];
                            $page_no = $params['page_no']; 
                            $resp1  = $this->HealthmallModel->hm_menu_sponor($user_id,$page_no);
                            $count        = count($resp1);
                            if($count > 0)
                            {
                                 $resp = $resp1;
                                           
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "No Record Found",
                                                "data"=> array()
                                             );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
              
           
        }
        
        
        
        
        
      
    }
      public function get_product_list_deal()
     {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                         if ($params['page_no'] == "" || $params['per_page'] == "" )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter page number(page_no) and per page(per_page) fields');
                        }
                        elseif ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                        else
                        {
                            $page_no = $params['page_no'];
                            $per_page = $params['per_page'];
                            $user_id = $params['user_id'];
                            $deal_id = $params['deal_id'];
                            $type=$params['type'];
                            
                            $result = $this->HealthmallModel->get_product_list_deal($page_no,$per_page,$user_id,$deal_id,$type);
                            $resp = $result;
                            
                            
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
    }
    
    
    public function hm_menu_list_page()
    {
        
         $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
              
                $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                       
                        else
                        {
                           
                            if(array_key_exists('brand_id',$params)){
                                $brand_id = $params['brand_id'];
                            } else {
                                $brand_id = "";
                            }
                            
                            $user_id = $params['user_id'];
                           
                            $resp1  = $this->HealthmallModel->hm_menu_list_page($user_id,$brand_id);
                            $count        = count($resp1);
                            if($count > 0)
                            {
                                 $resp = array(
                                                "status" => 200,
                                                "message" => "Success",
                                                "data"=>$resp1
                                             );
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "No Record Found",
                                                "data"=> array()
                                             );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
              
           
        }
        
        
        
        
        
      
    }
     public function hm_menu_list_category()
    {
        
         $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
              
                $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                        if ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                       
                        else
                        {
                           
                            if(array_key_exists('brand_id',$params)){
                                $brand_id = $params['brand_id'];
                            } else {
                                $brand_id = "";
                            }
                            
                            $user_id = $params['user_id'];
                           
                            $resp1  = $this->HealthmallModel->hm_menu_list_category($user_id,$brand_id);
                            $count        = count($resp1);
                            if($count > 0)
                            {
                                 $resp = array(
                                                "status" => 200,
                                                "message" => "Success",
                                                "data"=>$resp1
                                             );
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "No Record Found",
                                                "data"=> array()
                                             );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
              
           
        }
        
        
        
        
        
      
    }
   public function check_product()
    {
       $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
               $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                         if ($params['user_id'] == "")
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                        elseif ($params['pd_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter Product ID');
                        }
                        elseif ($params['pincode'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter Pincode');
                        }
                        else
                        {
                           
                            $pincode = $params['pincode'];
                            $user_id = $params['user_id'];
                            $pd_id = $params['pd_id'];
                            
                            $resp1 = $this->HealthmallModel->check_product($user_id,$pd_id,$pincode);
                            if(count($resp1) > 0)
                            {
                                 $resp = array(
                                                "status" => 200,
                                                "message" => "Success",
                                                "data"=> $resp1
                                               
                                             );
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "Result not found"
                                               );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
            
        }
    }
    
    public function generate_user_code(){
       
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
               $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                         if ($params['user_id'] == "")
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                        elseif ($params['pd_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter Product ID');
                        }
                      
                        else
                        {
                           
                           
                            $user_id = $params['user_id'];
                            $pd_id = $params['pd_id'];
                            
                            $resp1 = $this->HealthmallModel->generate_user_code($user_id,$pd_id);
                            if(count($resp1) > 0)
                            {
                                 $resp = $resp1;
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "Result not found"
                                               );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
            
        }
    
    }
    
    
        public function add_physical_code(){
            
            $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
               $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                         if ($params['user_id'] == "")
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                        elseif ($params['pd_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter Product ID');
                        }
                         elseif ($params['physical_code'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter Physical Code');
                        }
                        else
                        {
                            $physical_code = $params['physical_code'];
                            $user_id = $params['user_id'];
                            $pd_id = $params['pd_id'];
                            
                            $resp1 = $this->HealthmallModel->add_physical_code($physical_code, $pd_id, $user_id);
                            if(count($resp1) > 0)
                            {
                                 $resp = $resp1;
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "Result not found"
                                               );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
          
        }
        
    }
   
 public function get_offer_products_offerid()
    {
       $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
              
                $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                         if ($params['user_id'] == "")
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                        elseif ($params['offer_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter Offer ID');
                        }
                       
                        else
                        {
                           
                            $offer_id = $params['offer_id'];
                            $user_id = $params['user_id'];
                            $resp1=array();
                            $resp1[] = $this->HealthmallModel->get_offer_products_offerid($user_id,$offer_id);
                            $count        = count($resp1);
                            if($count > 0)
                            {
                                 $resp = array(
                                                "status" => 200,
                                                "message" => "Success",
                                                "data"=>$resp1
                                             );
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "No Record Found",
                                                "data"=> array()
                                             );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
              
           
        }
    }
    
   public function get_filter_list()
    {
       $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
              
                $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        
                         if ($params['user_id'] == "")
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                       
                       
                        else
                        {
                           
            		        
            		        $user_id = $params['user_id'];
            		        
            		        
            		        
            		        if(array_key_exists('brand_id',$params)){
                                $brand_id = $params['brand_id'];
                            } else {
                                $brand_id = 0;
                            }
                            if(array_key_exists('offer_id',$params)){
                                $offer_id = $params['offer_id'];
                            } else {
                                $offer_id = 0;
                            }
                             if(array_key_exists('cat_id',$params)){
                                $cat_id = $params['cat_id'];
                            } else {
                                $cat_id = 0;
                            }
                            $resp1=array();
                            $resp1 = $this->HealthmallModel->get_filter_list($user_id,$brand_id,$offer_id,$cat_id);
                            $count        = count($resp1);
                            if($count > 0)
                            {
                                 $resp = array(
                                                "status" => 200,
                                                "message" => "Success",
                                                "data"=>$resp1
                                             );
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "No Record Found",
                                                "data"=> array()
                                             );
                            }
                        }
                        simple_json_output($resp); 
                    }
                }  
              
              
              
              
           
        }
    }
    
     public function get_product_details_try_on()
     {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                         if ($params['pd_id'] == "" )
                        {
                            $resp = array('status' => 400, 'message' => 'Please Enter Product ID');
                        }
                        elseif ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'Please Enter User ID');
                        }
                        else
                        {
                            $pd_id = $params['pd_id'];
                            $user_id = $params['user_id'];
                           
                            $result = $this->HealthmallModel->get_product_details_try_on($pd_id,$user_id);
                            if(count($result) > 0)
                               {
                                $resp = array(
                                    "status" => 200,
                                    "message" => "Success",
                                    "data" => $result
                                );
                               }
                               else
                               {
                                 $resp = array(
                                        'status' => 400,
                                        'message' => 'No Product Details'
                                    ); 
                               }
                    
                            
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
    }

   	public function hm_menu_list5()
     {
	    $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                         if ($params['page_no'] == "" || $params['per_page'] == "" )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter page number(page_no) and per page(per_page) fields');
                        }
                        elseif ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                        else
                        {
                            $page_no = $params['page_no'];
            		        $per_page = $params['per_page'];
            		        $user_id = $params['user_id'];
                            $result = $this->HealthmallModel->hm_menu_list5($user_id,$page_no,$per_page);
                            $count        = count($result);
                            if($count > 0)
                            {
                                 $resp = array(
                                                "status" => 200,
                                                "message" => "Success",
                                               
                                                "data"=>$result
                                             );
                                           
                            }
                            else
                            {
                                $resp = array(
                                                "status" => 201,
                                                "message" => "No Record Found",
                                                
                                                "data"=> array()
                                             );
                            }
                            
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
	}
	
	public function get_product_list_hm5()
     {
	    $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') 
           {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
           } 
        else 
          {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) 
                {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) 
                    {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                         if ($params['page_no'] == "" || $params['per_page'] == "" )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter page number(page_no) and per page(per_page) fields');
                        }
                        elseif ($params['user_id'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter User ID');
                        }
                         elseif ($params['type'] == ""  )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter Type');
                        }
                        else
                        {
                            $page_no = $params['page_no'];
            		        $per_page = $params['per_page'];
            		        $user_id = $params['user_id'];
                            $type=$params['type'];
                             if(array_key_exists('brands',$params))
            		            {
                                   $brands = $params['brands'];
                                } 
                            else 
                                {
                                  $brands = "";
                                }
                            if(array_key_exists('cat_id',$params))
            		            {
                                   $category_ids = $params['cat_id'];
                                } 
                            else 
                                {
                                  $category_ids = "";
                                }
                            if(array_key_exists('sort',$params))
            		            {
                                   $sort = $params['sort'];
                                } 
                            else 
                                {
                                  $sort= "";
                                }  
                            if(array_key_exists('price_min',$params))
            		            {
                                   $price_min = $params['price_min'];
                                } 
                            else 
                                {
                                  $price_min= "";
                                }  
                            if(array_key_exists('price_max',$params))
            		            {
                                    $price_max = $params['price_max'];
                                } 
                            else 
                                {
                                  $price_max = "";
                                }      
                                
                                if(array_key_exists('offer',$params))
            		            {
                                    $offer = $params['offer'];
                                } 
                            else 
                                {
                                  $offer = "";
                                }  
                                if(array_key_exists('best_seller',$params))
            		            {
                                    $best_seller = $params['best_seller'];
                                } 
                            else 
                                {
                                  $best_seller = "";
                                }  
                                 if(array_key_exists('gender',$params))
            		            {
                                    $gender = $params['gender'];
                                } 
                            else 
                                {
                                  $gender = "";
                                }  

                            $response=0;
                            $result = $this->HealthmallModel->get_product_list_hm5($user_id,$page_no,$per_page,$type,$response,$brands,$category_ids,$sort,$price_min,$price_max,$offer,$best_seller,$gender);
                            $resp = $result;
                            
                            
                        }
                        simple_json_output($resp); 
                    }
                }  
         }
	}
	


    
}
?>
