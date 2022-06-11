<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends CI_Controller 
 {

    public function __construct() 
      {
        parent::__construct();
         $this->load->model('LoginModel');
      }

     public function index() {

        json_output(400, array('status' => 400, 'message' => 'Bad request.'));
    }

     public function add_cart() 
        {
        $this->load->model('CartModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CartModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['product_id'] == "" || $params['ipaddress'] == "" || $params['product_name'] == "")
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $params['user_id'];
                        $listing_id = $params['listing_id'];
                        $product_id = $params['product_id'];
                        $ipaddress = $params['ipaddress'];
                        $product_name = $params['product_name'];
                        $product_image = $params['product_image'];
                        $product_price = $params['product_price'];
                        $product_type = $params['product_type'];
                        $quantity = $params['quantity'];
                        $medicalname = $params['medicalname'];
                        $product_unit = $params['product_unit'];
                        
                        $resp = $this->CartModel->cart_add($user_id,$listing_id,$product_id,$ipaddress,$product_name,$product_image,$product_price,$product_type,$quantity,$medicalname,$product_unit);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function cart_details() {
        $this->load->model('CartModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CartModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['ipaddress'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                     
                        $ipaddress = $params['ipaddress'];

                        $resp = $this->CartModel->cart_details($ipaddress);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function remove_cart() {
        $this->load->model('CartModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CartModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['ipaddress'] == "" || $params['product_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                     
                        $ipaddress = $params['ipaddress'];
                        $user_id = $params['user_id'];
                        $product_id = $params['product_id'];
                        $resp = $this->CartModel->remove_cart($ipaddress,$product_id,$user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function update_quantity() {
        $this->load->model('CartModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CartModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['ipaddress'] == "" || $params['product_id'] == "" || $params['quantity'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                     
                        $ipaddress = $params['ipaddress'];
                        $user_id = $params['user_id'];
                        $product_id = $params['product_id'];
                        $quantity = $params['quantity'];
                        $resp = $this->CartModel->update_quantity($ipaddress,$product_id,$quantity,$user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function remove_cart_all() {
        $this->load->model('CartModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CartModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['ipaddress'] == "" || $params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                     
                        $ipaddress = $params['ipaddress'];
                        $user_id = $params['user_id'];
                       
                        $resp = $this->CartModel->remove_cart_alls($ipaddress,$user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    //added by zak for add to cart new api implementation 
    public function order_add_cart() 
        {
        $this->load->model('CartModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CartModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                    
                    if ($this->input->post('user_id') == "" || $this->input->post('cart_details') == "")
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $this->input->post('user_id');
                        $cart_details = $this->input->post('cart_details');
                        $description = $this->input->post('description');
                         if(array_key_exists('pid',$_POST))
                           {
                                $pid = $this->input->post('pid');
                            } else {
                                $pid = 0;
                            }
                         
                        $resp = $this->CartModel->order_add_cart($user_id,$cart_details,$description,$pid);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
     public function order_add_cart_web() 
        {
        $this->load->model('CartModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CartModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                    
                    if ($this->input->post('user_id') == "" || $this->input->post('cart_details') == "")
                    {
                        
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $this->input->post('user_id');
                        $cart_details = $this->input->post('cart_details');
                        $description = $this->input->post('description');
                        $image = $this->input->post('image');
                        $resp = $this->CartModel->order_add_cart_web($user_id,$cart_details,$description,$image);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    public function remove_user_cart() 
        {
        $this->load->model('CartModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CartModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                
                 $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['product_id'] == "" || $params['listing_type'] == "")
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        
                        $user_id = $params['user_id'];
                        $product_id = $params['product_id'];
                        $listing_type = $params['listing_type'];
                        $resp = $this->CartModel->remove_user_cart($user_id,$product_id,$listing_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
        public function remove_user_cart_all() 
        {
        $this->load->model('CartModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CartModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                
                 $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_type'] == "")
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        
                        $user_id = $params['user_id'];
                      
                        $listing_type = $params['listing_type'];
                        $resp = $this->CartModel->remove_user_cart_all($user_id,$listing_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function all_user_cart_list() 
        {
        $this->load->model('CartModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CartModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "")
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $params['user_id'];
                        $resp = $this->CartModel->all_user_cart_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
       // added by Dhaval for re-oder to cart new api implementation
        public function reorder_add_cart() 
        {
        $this->load->model('CartModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CartModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                      $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['invoice_no'] == "")
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $params['user_id'];
                        $invoice_no = $params['invoice_no'];
                        
                        $resp = $this->CartModel->reorder_add_cart($user_id,$invoice_no);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
       
       
        public function reorder_edit_cart() 
        {
        $this->load->model('CartModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->CartModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                    
                    if ($this->input->post('user_id') == "" || $this->input->post('id') == "")
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $this->input->post('user_id');
                        $id = $this->input->post('id');
                        $desc=$this->input->post('description');
                         
                        $resp = $this->CartModel->reorder_edit_cart($user_id,$id,$desc);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
       
}
?>