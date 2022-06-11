<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
    public function address_list()
    {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OrderModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->OrderModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $resp    = $this->OrderModel->address_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function address_add()
    {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OrderModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->OrderModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['name'] == "" || $params['mobile'] == "" || $params['pincode'] == "" || $params['address1'] == "" || $params['address2'] == "" || $params['landmark'] == "" || $params['city'] == "" || $params['state'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id  = $params['user_id'];
                        $name     = $params['name'];
                        $mobile   = $params['mobile'];
                        $pincode  = $params['pincode'];
                        $address1 = $params['address1'];
                        $address2 = $params['address2'];
                        $landmark = $params['landmark'];
                        $city     = $params['city'];
                        $state    = $params['state'];
                        $resp     = $this->OrderModel->address_add($user_id, $name, $mobile, $pincode, $address1, $address2, $landmark, $city, $state);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function address_update()
    {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OrderModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->OrderModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['address_id'] == "" || $params['name'] == "" || $params['address1'] == "" || $params['address2'] == "" || $params['landmark'] == "" || $params['mobile'] == "" || $params['city'] == "" || $params['state'] == "" || $params['pincode'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $address_id = $params['address_id'];
                        $name       = $params['name'];
                        $address1   = $params['address1'];
                        $address2   = $params['address2'];
                        $landmark   = $params['landmark'];
                        $mobile     = $params['mobile'];
                        $city       = $params['city'];
                        $state      = $params['state'];
                        $pincode    = $params['pincode'];
                        $resp       = $this->OrderModel->address_update($user_id, $address_id, $name, $address1, $address2, $landmark, $mobile, $city, $state, $pincode);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function address_delete()
    {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OrderModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->OrderModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['address_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        $user_id    = $params['user_id'];
                        $address_id = $params['address_id'];
                        $resp       = $this->OrderModel->address_delete($user_id, $address_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function order_add()
    {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OrderModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->OrderModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['listing_name'] == "" || $params['listing_type'] == "" || $params['address_id'] == "" || $params['payment_method'] == "" || $params['product_name'] == "" || $params['product_img'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id          = $params['user_id'];
                        $listing_id       = $params['listing_id'];
                        $listing_type     = $params['listing_type'];
                        $listing_name     = $params['listing_name'];
                        $address_id       = $params['address_id'];
                        $product_id       = $params['product_id'];
                        $product_price    = $params['product_price'];
                        $product_quantity = $params['product_quantity'];
                        $payment_method   = $params['payment_method'];
                        $product_name     = $params['product_name'];
                        $product_img      = $params['product_img'];
                        $product_unit      = $params['product_unit'];
                        $product_unit_value= $params['product_unit_value'];
                        $chat_id          = $params['chat_id'];
                        $delivery_charge  = $params['delivery_charge'];
                        $is_night_delivery= $params['is_night_delivery'];
                        $resp             = $this->OrderModel->order_add($user_id, $listing_id, $listing_type, $listing_name, $address_id, $payment_method, $product_id, $product_price, $product_quantity, $product_name, $delivery_charge, $product_img, $chat_id,$product_unit,$product_unit_value,$is_night_delivery);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function order_list()
    {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OrderModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->OrderModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id      = $params['user_id'];
                        $listing_type = $params['listing_type'];
                        $resp         = $this->OrderModel->order_list($user_id, $listing_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    public function prescription_add()
    {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id      = $this->input->post('user_id');
            $listing_id   = $this->input->post('listing_id');
            $address_id   = $this->input->post('address_id');
            $listing_name = $this->input->post('listing_name');
            $listing_type = $this->input->post('listing_type');
            $chat_id      = $this->input->post('chat_id'); 
            $payment_method  = $this->input->post('payment_method');      
            $delivery_charge= $this->input->post('delivery_charge');    
            $is_night_delivery= $this->input->post('is_night_delivery');
            
            if($listing_id == "" || empty($_FILES["image"]["name"])) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields1'
                );
            }
			else 
			{       
                $order_id = $this->OrderModel->prescription_add($user_id,$listing_id,$address_id,$listing_name,$listing_type,$chat_id,$payment_method,$delivery_charge,$is_night_delivery);
				if($order_id!='')
				{
				if(!empty($_FILES["image"]["name"])) {
				$image = count($_FILES['image']['name']); 
				$img_format = array("jpg", "png", "gif", "bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
				include('s3_config.php');
				date_default_timezone_set('Asia/Calcutta');
				$invoice_no       = date("YmdHis");
				$order_status     = 'Awaiting Confirmation';
				}               
				if($image > 0) {
                    foreach($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                        $img_name = $key.$_FILES['image']['name'][$key];
                        $img_size = $_FILES['image']['size'][$key];
                        $img_tmp  = $_FILES['image']['tmp_name'][$key];
                        $ext      = getExtension($img_name);
                        if(strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if(in_array($ext, $img_format)) {
                                    $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/prescription_images/' . $actual_image_name;
                                    if($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
										$this->db->query("INSERT INTO `prescription_order_details`(`order_id`, `prescription_image`,`order_status`, `uni_id`) VALUES ('$order_id', '$actual_image_name','$order_status', '$invoice_no')");
                                    }
                                }
                            }
                        }
                    }
                }
				simple_json_output(array('status' => 200,'message' => 'success','order_id' => $invoice_no));
				}
				else{
				simple_json_output(array("status" => 201,"message" => "fail"));
				}
            }          
        }
    }
    
    
    
      public function order_confirm_cancel()
    {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->OrderModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->OrderModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['order_id'] == "" || $params['type'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $order_id         = $params['order_id'];
                        $type             = $params['type'];
                        $order_status     = $params['type'];
                        $cancel_reason     = $params['cancel_reason'];
                        $resp             = $this->OrderModel->order_confirm_cancel($order_id, $type, $order_status, $cancel_reason);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
      
    
    
    
    
}
