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
                            $user_id = $params['user_id'];
                            
                            $result = $this->HealthmallModel->get_product_list($brands,$page_no,$per_page,$category_ids,$sort,$user_id,$price_min,$price_max,$offer,$best_seller);
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
            		       
                            $result = $this->HealthmallModel->get_product_details($pd_id,$user_id);
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
                            $resp = $this->HealthmallModel->add_to_wishlist($product_id,$type,$user_id);
                           
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
                        
                        if ($params['user_id'] == "" || $params['user_id'] == 0 )
                        {
                            $resp = array('status' => 400, 'message' => 'please enter user_id');
                        }
                        else
                        {
                            $user_id = $params['user_id'];
            		        
                            $resp = $this->HealthmallModel->get_user_wishlist($user_id);
                           
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
                            
                            
                            $resp = $this->HealthmallModel->add_to_cart($user_id, $product_id, $quantity, $offer_id,$referal_code,$variable_pd_id,$sku,$type);
                           
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
	
	
    
}
?>