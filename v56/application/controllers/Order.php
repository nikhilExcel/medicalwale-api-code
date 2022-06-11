<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }

    public function address_list() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->OrderModel->address_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    public function address_add() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['name'] == "" || $params['mobile'] == "" || $params['pincode'] == "" || $params['address1'] == "" || $params['address2'] == "" || $params['landmark'] == "" || $params['city'] == "" || $params['state'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $name = $params['name'];
                        $mobile = $params['mobile'];
                        $pincode = $params['pincode'];
                        $address1 = $params['address1'];
                        $address2 = $params['address2'];
                        $landmark = $params['landmark'];
                        $city = $params['city'];
                        $state = $params['state'];
                        $resp = $this->OrderModel->address_add($user_id, $name, $mobile, $pincode, $address1, $address2, $landmark, $city, $state);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function address_update() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['address_id'] == "" || $params['name'] == "" || $params['address1'] == "" || $params['address2'] == "" || $params['landmark'] == "" || $params['mobile'] == "" || $params['city'] == "" || $params['state'] == "" || $params['pincode'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $address_id = $params['address_id'];
                        $name = $params['name'];
                        $address1 = $params['address1'];
                        $address2 = $params['address2'];
                        $landmark = $params['landmark'];
                        $mobile = $params['mobile'];
                        $city = $params['city'];
                        $state = $params['state'];
                        $pincode = $params['pincode'];
                        $resp = $this->OrderModel->address_update($user_id, $address_id, $name, $address1, $address2, $landmark, $mobile, $city, $state, $pincode);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function address_delete() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['address_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {

                        $user_id = $params['user_id'];
                        $address_id = $params['address_id'];
                        $resp = $this->OrderModel->address_delete($user_id, $address_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function order_add() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $listing_type = $params['listing_type'];
                    if ($listing_type == "13") {
                        if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['listing_name'] == "" || $params['listing_type'] == "" || $params['address_id'] == "" || $params['payment_method'] == "" || $params['product_name'] == "" || $params['product_img'] == "") {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                        } else {

                            $user_id = $params['user_id'];
                            $listing_name = $params['listing_name'];
                            $listing_id = $params['listing_id'];
                            $address_id = $params['address_id'];
                            $product_id = $params['product_id'];
                            $product_price = $params['product_price'];
                            $product_quantity = $params['product_quantity'];
                            $payment_method = $params['payment_method'];
                            $product_name = $params['product_name'];
                            $product_img = $params['product_img'];
                            $product_unit = $params['product_unit'];
                            $product_unit_value = $params['product_unit_value'];
                            $chat_id = $params['chat_id'];
                            $delivery_charge = $params['delivery_charge'];
                            $is_night_delivery = $params['is_night_delivery'];
                            $lat = $params['lat'];
                            $lng = $params['lng'];
                            $schedule_date = "";
                            $device_type = "";
                            $cancel_status = "";
                            $thyrocare_cancel_status = "";
                            $lead_id = "";
                            $reference_id = "";

                            $resp = $this->OrderModel->order_add($user_id, $listing_id, $listing_type, $listing_name, $address_id, $payment_method, $product_id, $product_price, $product_quantity, $product_name, $delivery_charge, $product_img, $chat_id, $product_unit, $product_unit_value, $is_night_delivery, $schedule_date, $device_type, $cancel_status, $thyrocare_cancel_status, $lead_id, $reference_id,$lat,$lng);
                        }
                    }
                    if ($listing_type == "31") {
                        if ($params['user_id'] == ""  || $params['listing_type'] == "" ) {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                        } else {
                            
                            $address_id = "";
                            $payment_method = "";
                            $product_id = "";
                            $product_price = "";
                            $product_quantity = "";
                            $product_name = "";
                            $delivery_charge = "";
                            $product_img = "" ;
                            $chat_id = "";
                            $product_unit = "";
                            $product_unit_value = "";
                            $is_night_delivery = "";
                            $lat = "";
                            $lng = "";
                            
                            $user_id = $params['user_id'];
                            $listing_name = 'thyrocare';
                            $listing_id = $params['listing_id'];
                            $schedule_date = $params['schedule_date'];
                            $device_type = $params['device_type'];
                            $cancel_status = $params['cancel_status'];
                            $thyrocare_cancel_status = $params['thyrocare_cancel_status'];
                            $lead_id = $params['lead_id'];
                            $reference_id = $params['reference_id'];
                            
                            $resp = $this->OrderModel->order_add($user_id, $listing_id, $listing_type, $listing_name, $address_id, $payment_method, $product_id, $product_price, $product_quantity, $product_name, $delivery_charge, $product_img, $chat_id, $product_unit, $product_unit_value, $is_night_delivery, $schedule_date, $device_type, $cancel_status, $thyrocare_cancel_status, $lead_id, $reference_id,$lat,$lng);
                        }
                    }
                    //added by jakir on 25-05-2018 for nursing attendent
                     if ($listing_type == "12") {
                        if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['listing_type'] == "" || $params['payment_method'] == "" || $params['product_name'] == "" || $params['product_price'] == "") {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                        } else {

                            
                            $address_id = "";
                            $payment_method = $params['payment_method'];
                            $product_id = $params['product_id'];
                            $product_price = $params['product_price'];
                            $product_quantity = $params['product_quantity'];
                            $product_name = $params['product_name'];
                            $delivery_charge = "";
                            $product_img = "" ;
                            $chat_id = "";
                            $product_unit = "";
                            $product_unit_value = "";
                            $is_night_delivery = "";
                            $lat = "";
                            $lng = "";
                            
                            $user_id = $params['user_id'];
                            $listing_name = 'nursing_attendant';
                            $listing_id = $params['listing_id'];
                            $schedule_date = "";
                            $device_type = $params['device_type'];
                            $cancel_status = "";
                            $thyrocare_cancel_status = "";
                            $lead_id = "";
                            $reference_id = "";

                            $resp = $this->OrderModel->order_add($user_id, $listing_id, $listing_type, $listing_name, $address_id, $payment_method, $product_id, $product_price, $product_quantity, $product_name, $delivery_charge, $product_img, $chat_id, $product_unit, $product_unit_value, $is_night_delivery, $schedule_date, $device_type, $cancel_status, $thyrocare_cancel_status, $lead_id, $reference_id,$lat,$lng);
                        }
                    }
                    
                    //added by zak for miss belly
                    if($listing_type == "37")
                    {
                        if ($params['user_id'] == ""  || $params['listing_type'] == "" ) {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                        } else {
                            
                            $address_id = "";
                            $payment_method = "";
                            $product_id = "";
                            $product_price = "";
                            $product_quantity = "";
                            $product_name = "";
                            $delivery_charge = "";
                            $product_img = "" ;
                            $chat_id = "";
                            $product_unit = "";
                            $product_unit_value = "";
                            $is_night_delivery = "";
                            $lat = "";
                            $lng = "";
                            
                            $user_id = $params['user_id'];
                            $listing_name = 'miss_belly';
                            $listing_id = $params['listing_id'];
                            $schedule_date = "";
                            $device_type = "";
                            $cancel_status = "no";
                            $thyrocare_cancel_status = "";
                            $lead_id = "";
                            $reference_id = "";
                            
                            $resp = $this->OrderModel->order_add($user_id, $listing_id, $listing_type, $listing_name, $address_id, $payment_method, $product_id, $product_price, $product_quantity, $product_name, $delivery_charge, $product_img, $chat_id, $product_unit, $product_unit_value, $is_night_delivery, $schedule_date, $device_type, $cancel_status, $thyrocare_cancel_status, $lead_id, $reference_id,$lat,$lng);
                        }
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function order_route() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $listing_type = $params['listing_type'];
                        if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['listing_name'] == "" || $params['listing_type'] == "" || $params['address_id'] == "" || $params['payment_method'] == "" || $params['product_name'] == "" || $params['product_img'] == "") {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                        } else {
                            $current_listing_id = $params['current_listing_id'];
                            $current_listing_name = $params['current_listing_name'];
                            $order_id = $params['order_id'];
                            $user_id = $params['user_id'];
                            $listing_name = $params['listing_name'];
                            $listing_id = $params['listing_id'];
                            $address_id = $params['address_id'];
                            $product_id = $params['product_id'];
                            $product_price = $params['product_price'];
                            $product_quantity = $params['product_quantity'];
                            $payment_method = $params['payment_method'];
                            $product_name = $params['product_name'];
                            $product_img = $params['product_img'];
                            $product_unit = $params['product_unit'];
                            $product_unit_value = $params['product_unit_value'];
                            $chat_id = $params['chat_id'];
                            $delivery_charge = $params['delivery_charge'];
                            $is_night_delivery = $params['is_night_delivery'];
                            $lat = $params['lat'];
                            $lng = $params['lng'];
                            $invoice_no = $params['invoice_no'];

                            $resp = $this->OrderModel->order_route($current_listing_id,$current_listing_name,$order_id,$user_id, $listing_id, $listing_type, $listing_name, $address_id, $payment_method, $product_id, $product_price, $product_quantity, $product_name, $delivery_charge, $product_img, $chat_id, $product_unit, $product_unit_value, $is_night_delivery,$lat,$lng,$invoice_no);
                        }
                    simple_json_output($resp);
                }
            }
        }
    }

    public function order_list() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_type = $params['listing_type'];
                        $resp = $this->OrderModel->order_list($user_id, $listing_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function order_list_v2() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $listing_type = $params['listing_type'];
                        $resp = $this->OrderModel->order_list_v2($user_id, $listing_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function prescription_add() {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            
            $listing_id = $this->input->post('listing_id');
            $address_id = $this->input->post('address_id');
            $listing_name = $this->input->post('listing_name');
            $listing_type = $this->input->post('listing_type');
            $chat_id = $this->input->post('chat_id');
            $payment_method = $this->input->post('payment_method');
            $delivery_charge = $this->input->post('delivery_charge');
            $is_night_delivery = $this->input->post('is_night_delivery');
           
         
            if ($listing_id == "" || empty($_FILES["image"]["name"])) {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields1'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields1"));
            } else {
                $order_id = $this->OrderModel->prescription_add($user_id, $listing_id, $address_id, $listing_name, $listing_type, $chat_id, $payment_method, $delivery_charge, $is_night_delivery);
                if ($order_id != '') {
                    if (!empty($_FILES["image"]["name"])) {
                        $image = count($_FILES['image']['name']);
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        date_default_timezone_set('Asia/Calcutta');
                        $invoice_no = date("YmdHis");
                        $order_status = 'Awaiting Confirmation';
                    }
                    if ($image > 0) {
                        foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                            $img_name = $key . $_FILES['image']['name'][$key];
                            $img_size = $_FILES['image']['size'][$key];
                            $img_tmp = $_FILES['image']['tmp_name'][$key];
                            $ext = getExtension($img_name);
                            if (strlen($img_name) > 0) {
                                if ($img_size < (50000 * 50000)) {
                                    if (in_array($ext, $img_format)) {
                                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                        $actual_image_path = 'images/prescription_images/' . $actual_image_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                            $this->db->query("INSERT INTO `prescription_order_details`(`order_id`, `prescription_image`,`order_status`, `uni_id`) VALUES ('$order_id', '$actual_image_name','$order_status', '$invoice_no')");
                                        }
                                    }
                                }
                            }
                        }
                    }
                    simple_json_output(array('status' => 200, 'message' => 'success', 'order_id' => $invoice_no,'medlife' => $order_id));
                } else {
                    simple_json_output(array("status" => 201, "message" => "fail"));
                }
            }
        }
    }
    
    public function prescription_add_web() {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            
            $listing_id = $this->input->post('listing_id');
            $address_id = $this->input->post('address_id');
            $listing_name = $this->input->post('listing_name');
            $listing_type = $this->input->post('listing_type');
            $chat_id = $this->input->post('chat_id');
            $payment_method = $this->input->post('payment_method');
            $delivery_charge = $this->input->post('delivery_charge');
            $is_night_delivery = $this->input->post('is_night_delivery');
            $image=$this->input->post('image');
         
            if ($listing_id == "" || empty($image)) {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields1'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields1"));
            } else {
                
                $order_id = $this->OrderModel->prescription_add($user_id, $listing_id, $address_id, $listing_name, $listing_type, $chat_id, $payment_method, $delivery_charge, $is_night_delivery);
                if ($order_id != '') {
                    $imagearray=explode(',', $image);
                    
                    if (!empty($imagearray)) {
                        $image = count($imagearray);
                       
                        date_default_timezone_set('Asia/Calcutta');
                        $invoice_no = date("YmdHis");
                        $order_status = 'Awaiting Confirmation';
                    }
                    if ($image > 0) {
                        foreach ($imagearray as $key => $tmp_name) {
                           
                           
                               
                          $this->db->query("INSERT INTO `prescription_order_details`(`order_id`, `prescription_image`,`order_status`, `uni_id`) VALUES ('$order_id', '$tmp_name','$order_status', '$invoice_no')");
      
                           
                        }
                    }
                    simple_json_output(array('status' => 200, 'message' => 'success', 'order_id' => $invoice_no,'medlife' => $order_id));
                } else {
                    simple_json_output(array("status" => 201, "message" => "fail"));
                }
            }
        }
    }
    
    //added for quick booking to upload prescription 
    //start 
     public function prescription_add_quickbook() {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            $address_id = $this->input->post('address_id');
            $product_id = $this->input->post('product_id');
            $chat_id = $this->input->post('chat_id');
            $is_profile  = $this->input->post('is_fav');
            $listing_id  = $this->input->post('listing_id');
            
            $payment_method = 'Cash On Delivery';
            $delivery_charge = '0';
            $is_night_delivery = '1';
            $lat  = $this->input->post('lat');
            $lng  = $this->input->post('lng');
            $description  = $this->input->post('description'); 
             $desc = array();
                if(!empty($description))
                {
                    $description = $description.'$';
                    $desc = explode("$",$description);
                }
                else
                {
                    $desc='';
                }
            if ($lat == "" || $lng == "") {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields2'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields2"));
            } 
            
            else {
                $order_id = $this->OrderModel->prescription_add_quickbook($user_id, $address_id, $chat_id, $payment_method, $delivery_charge, $is_night_delivery,$lat,$lng,$product_id,$is_profile,$listing_id);
                if ($order_id != '') {
                    //   if($prescription_type == 'gallery')
                    //   {
                      $image = 0;
                    if (!empty($_FILES["image"]["name"])) {
                        $image = count($_FILES['image']['name']);
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        date_default_timezone_set('Asia/Calcutta');
                        $invoice_no = date("YmdHis");
                        $order_status = 'Awaiting Confirmation';
                    }
                    if ($image > 0) {
                        $i=0;
                        foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                            $img_name = $key . $_FILES['image']['name'][$key];
                            $img_size = $_FILES['image']['size'][$key];
                            $img_tmp = $_FILES['image']['tmp_name'][$key];
                            $ext = getExtension($img_name);
                            if (strlen($img_name) > 0) {
                                if ($img_size < (50000 * 50000)) {
                                    if (in_array($ext, $img_format)) {
                                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                        $actual_image_path = 'images/prescription_images/' . $actual_image_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                              if(!empty($desc))
                                    {
                                        $d = $desc[$i];
                                    }
                                    else
                                    {
                                        $d = "";
                                    }
                                            $this->db->query("INSERT INTO `prescription_order_details`(`order_id`, `prescription_image`,`order_status`, `uni_id`,`description`) VALUES ('$order_id', '$actual_image_name','$order_status', '$invoice_no','$d')");
                                        }
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                  //  simple_json_output(array('status' => 200, 'message' => 'success', 'order_id' => $invoice_no,'medlife' => ""));
                  //  }
                  //    else
                  //     {
                        //  date_default_timezone_set('Asia/Calcutta');
                        // $invoice_no = date("YmdHis");
                        // $order_status = 'Awaiting Confirmation';
                        
                        if($product_id != '' )
                        {
                          $product_id  = explode(',',$product_id);
                        }
                        else
                        {
                            $product_id = " ";
                        }
                        
                        if(count($product_id) > 0) 
                        {
                          for($i=0 ; $i<count($product_id) ; $i++)
                           {
                        $query = $this->db->query("SELECT prescription_link from user_prescription where id = '$product_id[$i]'");
                        $query_status_count = $query->num_rows();
                        $query_status = $query->row();
                        if($query_status_count > 0)
                        {
                            date_default_timezone_set('Asia/Calcutta');
                        $invoice_no = date("YmdHis");
                        $order_status = 'Awaiting Confirmation';
                        $actual_image_name  = $query_status->prescription_link;
                        $this->db->query("INSERT INTO `prescription_order_details`(`order_id`, `prescription_image`,`order_status`, `uni_id`) VALUES ('$order_id', '$actual_image_name','$order_status', '$invoice_no')");
                        }
                        }
                        }
                    simple_json_output(array('status' => 200, 'message' => 'success', 'order_id' => $order_id,'medlife' => ""));
                      // }

                      } else {
                    simple_json_output(array("status" => 201, "message" => "fail"));
                }
            }
        }
    }
    
    //end
    
     //added by zak for quick book from stack to order medicines 
   //start
     public function order_from_stack()
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                  //  $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($this->input->post('user_id') == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $this->input->post('user_id');
                        $address_id = $this->input->post('address_id');
                        $payment_method = $this->input->post('payment_method');
                        $delivery_charges = $this->input->post('delivery_charges');
                        $is_profile  = $this->input->post('is_fav');
                        $listing_id  = $this->input->post('listing_id');
                        if($delivery_charges == "" || $delivery_charges != null)
                        {
                            $delivery_charges = 0;
                        }
                        
                        $is_night_delivery = $this->input->post('is_night_delivery');
                        $lat = $this->input->post('lat');
                        $lng = $this->input->post('lng');
                        $product_details = $this->input->post('product_details');
                        $resp1 = $this->OrderModel->order_from_stack($user_id, $address_id, $payment_method, $delivery_charges, $is_night_delivery, $lat, $lng, $product_details,$is_profile,$listing_id);
                         $resp = array(
                            'status' => 200,
                            'message' => 'Success',
                            'order_id'=>$resp1
                        );
                      
                    }
                   
                   simple_json_output($resp);
                }
            }
        }
    }
    
    //end
    
    //added for quick booking to upload prescription 
    //start 
     public function favourite_add_quickbook() {
        $this->load->model('OrderModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $user_id = $this->input->post('user_id');
            
         //   $listing_id = $this->input->post('listing_id');
            $address_id = $this->input->post('address_id');
        //    $listing_name = $this->input->post('listing_name');
        //    $listing_type = $this->input->post('listing_type');
            $chat_id = $this->input->post('chat_id');
            $payment_method = 'Cash On Delivery';
            $delivery_charge = '0';
            $is_night_delivery = '1';
            $lat  = $this->input->post('lat');
            $lng  = $this->input->post('lng');
            $fav_pharmacy  = $this->input->post('fav_pharmacy');
            
            if (empty($_FILES["image"]["name"])) {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields1'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields1"));
            } 
            else if ($lat == "" || $lng == "") {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields2'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields2"));
            } 
            else if ($fav_pharmacy == "" ) {
               
                $resp = array(
                    'status' => 400,
                    'message' => 'please Select Favourite Pharmacy'
                );
                
                simple_json_output(array("status" => 400, "message" => "please enter fields2"));
            } 
            else {
                $order_id = $this->OrderModel->favourite_add_quickbook($user_id, $address_id, $chat_id, $payment_method, $delivery_charge, $is_night_delivery,$lat,$lng,$fav_pharmacy);
                if ($order_id != '') {
                    if (!empty($_FILES["image"]["name"])) {
                        $image = count($_FILES['image']['name']);
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        date_default_timezone_set('Asia/Calcutta');
                        $invoice_no = date("YmdHis");
                        $order_status = 'Awaiting Confirmation';
                    }
                    if ($image > 0) {
                        foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                            $img_name = $key . $_FILES['image']['name'][$key];
                            $img_size = $_FILES['image']['size'][$key];
                            $img_tmp = $_FILES['image']['tmp_name'][$key];
                            $ext = getExtension($img_name);
                            if (strlen($img_name) > 0) {
                                if ($img_size < (50000 * 50000)) {
                                    if (in_array($ext, $img_format)) {
                                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                        $actual_image_path = 'images/prescription_images/' . $actual_image_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                            $this->db->query("INSERT INTO `prescription_order_details`(`order_id`, `prescription_image`,`order_status`, `uni_id`) VALUES ('$order_id', '$actual_image_name','$order_status', '$invoice_no')");
                                        }
                                    }
                                }
                            }
                        }
                    }
                    simple_json_output(array('status' => 200, 'message' => 'success', 'order_id' => $invoice_no,'medlife' => ""));
                } else {
                    simple_json_output(array("status" => 201, "message" => "fail"));
                }
            }
        }
    }
    
    
    
    //end

   /* public function order_confirm_cancel() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['order_id'] == "" || $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $order_id = $params['order_id'];
                        $type = $params['type'];
                        $order_status = $params['type'];
                        $cancel_reason = $params['cancel_reason'];
                        $resp = $this->OrderModel->order_confirm_cancel($order_id, $type, $order_status, $cancel_reason);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    */
    
     public function order_confirm_cancel() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['order_id'] == "" || $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $order_id = $params['order_id'];
                        $type = $params['type'];
                        $order_status = $params['type'];
                        $cancel_reason = $params['cancel_reason'];
                        $mode= $params['mode'];
                        $resp = $this->OrderModel->order_confirm_cancel($order_id, $type, $order_status, $cancel_reason,$mode);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function customer_call() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    $call_to = $params['call_to'];
                    $call_from = $params['call_from'];
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $post_data = array(
                        
                        // 'From' => "<First-phone-number-to-call (Your agent's number)>",
                        // 'To' => "<Second-phone-number-to-call (Your customer's number)>",
                        // 'CallerId' => "<Your-Exotel-virtual-number>",
                        // 'TimeLimit' => "<time-in-seconds> (optional)",
                        // 'TimeOut' => "<time-in-seconds (optional)>",
                        // 'CallType' => "promo" //Can be "trans" for transactional and "promo" for promotional content
                        'From' => $call_from,
                        'To' => $call_to,
                        'CallerId' => "02248931498",
                        // 'TimeLimit' => " ",
                        // 'TimeOut' => "",
                        'CallType' => "trans" //Can be "trans" for transactional and "promo" for promotional content
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
                
                $this->load->model('LoginModel');
                $type= "order/customer_call";
                $this->LoginModel->exotel_call($http_result,$type);
                
              
                }
            }
        }
     
    }
    
    
    public function customer_call_whitlisting() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id = $params['user_id'];
                     $order_info = $this->db->select('phone,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
                     $customer_phone = $order_info->phone;
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $post_data = array(
                        
                        'VirtualNumber' => '022-337-21563',
                        'Number' => $customer_phone,
                        'Language' => 'en',
                    );
                
                // print_r($post_data); die();
                 
                $exotel_sid = "aegishealthsolutions"; // Your Exotel SID - Get it from here: http://my.exotel.in/settings/site#api-settings
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5"; // Your exotel token - Get it from here: http://my.exotel.in/settings/site#api-settings
                 
                $url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/CustomerWhitelist";
                 
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
    
     public function order_details_by_id() {
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
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                       
                        $order_id   = $params['order_id'];
                        $resp = $this->OrderModel->order_details_by_id($user_id,  $order_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

}
