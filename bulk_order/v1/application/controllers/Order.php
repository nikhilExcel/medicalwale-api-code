<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}

    public function order_add()
    {
        $this->load->model('LoginModel');
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params       = json_decode(file_get_contents('php://input'), TRUE);
                $user_id      = $params['user_id'];
                $cart_data    = $params['cart_data'];
                $address_id   = '';
                
                if ($user_id!='' && $cart_data!='') {
                    $response = $this->OrderModel->order_add($user_id,$cart_data,$address_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array( 
                        'status' => 400,
                        'message' => 'Error'
                    ));
                }
            }
        }
    }

    public function order_list()
    {
        $this->load->model('LoginModel');
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params       = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                if (array_key_exists("page",$params)){
                    $page = $params['page'];
                }
                else{
                    $page = '1';
                }
                
                if ($user_id!='' && $page!='') {
                    $response = $this->OrderModel->order_list($user_id,$page);
                    simple_json_output($response);
                } else {
                    json_output(400, array( 
                        'status' => 400,
                        'message' => 'Error'
                    ));
                }
            }
        }
    }

    public function order_details()
    {
        $this->load->model('LoginModel');
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params       = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $order_id = $params['order_id'];
                
                if ($user_id!='' && $order_id!='') {
                    $response = $this->OrderModel->order_details($user_id,$order_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array( 
                        'status' => 400,
                        'message' => 'Error'
                    ));
                }
            }
        }
    }





public function order_invoice_list()
    {
        $this->load->model('LoginModel');
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params       = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                if (array_key_exists("page",$params)){
                    $page = $params['page'];
                }
                else{
                    $page = '1';
                }
                
                if ($user_id!='' && $page!='') {
                    $response = $this->OrderModel->order_invoice_list($user_id,$page);
                    simple_json_output($response);
                } else {
                    json_output(400, array( 
                        'status' => 400,
                        'message' => 'Error'
                    ));
                }
            }
        }
    }

    public function order_invoice_details()
    {
        $this->load->model('LoginModel');
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params       = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                $order_id = $params['order_id'];
                
                if ($user_id!='' && $order_id!='') {
                    $response = $this->OrderModel->order_invoice_details($user_id,$order_id);
                    simple_json_output($response);
                } else {
                    json_output(400, array( 
                        'status' => 400,
                        'message' => 'Error'
                    ));
                }
            }
        }
    }
    
    
    
    
    public function order_invoice_return()
    {
        $this->load->model('LoginModel');
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params       = json_decode(file_get_contents('php://input'), TRUE);
                $user_id    = $params['user_id'];
                $id         = $params['id'];
                $order_id   = $params['order_id'];
                $product   = $params['product'];
                
                
                if ($user_id!='' && $order_id!='' && !empty($product)) {
                    $response = $this->OrderModel->order_invoice_return($user_id,$order_id,$id,$product);
                    simple_json_output($response);
                } else {
                    json_output(400, array( 
                        'status' => 400,
                        'message' => 'Error'
                    ));
                }
            }
        }
    }
    

}
