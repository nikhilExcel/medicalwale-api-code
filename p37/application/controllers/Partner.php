<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Partner extends CI_Controller
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
    public function signup()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['category'] == "" || $params['name'] == "" || $params['email'] == "" || $params['phone'] == "" || $params['city'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $category = $params['category'];
                        $name     = $params['name'];
                        $email    = $params['email'];
                        $city     = $params['city'];
                        $phone    = $params['phone'];
                        $token    = $params['token'];
                        $agent    = $params['agent'];
                        $resp     = $this->PartnerModel->signup($category, $name, $email, $city, $phone, $token, $agent);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function sendotp()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter fields'
                    );
                } else {
                    $phone = $params['phone'];
                    $resp  = $this->PartnerModel->sendotp($phone);
                }
                otp_json_output($resp);
            }
        }
    }
    
    public function login()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter phone no'
                    );
                } else {
                    $phone = $params['phone'];
                    $token = $params['token'];
                    $agent = $params['agent'];
                    $res   = $this->PartnerModel->login($phone, $token, $agent);
                }
                simple_json_output($res);
            }
        }
    }   
    
    
    public function login_v1()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter phone no'
                    );
                } else {
                    $phone = $params['phone'];
                    $password = $params['password'];
                    $token = $params['token'];
                    $agent = $params['agent'];
                    $res   = $this->PartnerModel->login_v1($phone, $token, $agent,$password);
                }
                simple_json_output($res);
            }
        }
    } 
    
     public function set_password()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "" ) {
                    $res = array(
                        'status' => 400,
                        'message' => 'please enter phone no'
                    );
                }
                elseif ($params['password'] == "" ) {
                    $res = array(
                        'status' => 400,
                        'message' => 'please enter password'
                    );
                }else {
                    
                    $phone = $params['phone'];
                    $password = $params['password'];
                    $cpassword = $params['cpassword'];
                    if($password==$cpassword)
                    {
                    $res   = $this->PartnerModel->set_password($phone, $password,$cpassword);
                    }
                    else
                    {
                        $res = array(
                        'status' => 400,
                        'message' => 'Password Not Match'
                    );
                    }
                }
                simple_json_output($res);
            }
        }
    } 
    
      public function forget_sendotp()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter fields'
                    );
                } else {
                    $phone = $params['phone'];
                    $resp  = $this->PartnerModel->forget_sendotp($phone);
                }
                otp_json_output($resp);
            }
        }
    }
    
    public function update_registration_token()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['listing_id'] == "" || $params['token'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter phone no'
                    );
                } else {
                    $listing_id = $params['listing_id'];
                    $token      = $params['token'];
                    $res        = $this->PartnerModel->update_registration_token($listing_id, $token);
                }
                simple_json_output($res);
            }
        }
    }
    
    public function order_list()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['listing_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id   = $params['listing_id'];
                        $listing_type = $params['listing_type'];
                        $order_type   = $params['order_type'];
                        $resp         = $this->PartnerModel->order_list($listing_id, $listing_type, $order_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function order_status()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $order_   = json_decode(file_get_contents('php://input'), TRUE);
                    $orders[] = $order_['order'];
                    foreach ($orders as $order_array) {
                        $order_id      = $order_array['order_id'];
                        $delivery_time = $order_array['delivery_time'];
                        $order_status  = $order_array['order_status'];
                        $listing_id    = $order_array['listing_id'];
                        $listing_type  = $order_array['listing_type'];
                        $order_data    = $order_array['product_order'];
                    }
                    if ($order_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->order_status($order_id, $delivery_time, $order_status, $listing_id, $listing_type, $order_data);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function prescription_status()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $order_   = json_decode(file_get_contents('php://input'), TRUE);
                    $orders[] = $order_['prescription'];
                    foreach ($orders as $order_array) {
                        $order_id           = $order_array['order_id'];
                        $order_status       = $order_array['order_status'];
                        $delivery_time      = $order_array['delivery_time'];
                        $prescription_order = $order_array['prescription_order'];
                    }
                    if ($order_id == "" && $order_status == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->prescription_status($order_id, $order_status, $prescription_order, $delivery_time);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function order_deliver_cancel()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $cancel_order      = json_decode(file_get_contents('php://input'), TRUE);
                    $order_id          = $cancel_order['order_id'];
                    $type              = $cancel_order['type'];
                    $notification_type = $cancel_order['notification_type'];
                    $cancel_reason     = $cancel_order['cancel_reason'];
                    if ($order_id == "" && $cancel_reason == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->order_deliver_cancel($order_id, $cancel_reason, $type, $notification_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function add_pharmacy()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $list               = json_decode(file_get_contents('php://input'), TRUE);
                    $store_name         = $list['store_name'];
                    $store_manager_name = $list['store_manager_name'];
                    $store_since        = $list['store_since'];
                    $address_line1      = $list['address_line1'];
                    $address_line2      = $list['address_line2'];
                    $state              = $list['state'];
                    $city               = $list['city'];
                    $pincode            = $list['pincode'];
                    $latitude           = $list['latitude'];
                    $longitude          = $list['longitude'];
                    $listing_id         = $list['listing_id'];
                    
                    if ($store_name == "" && $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->add_pharmacy($store_name, $store_manager_name, $store_since, $address_line1, $address_line2, $state, $city, $pincode, $latitude, $longitude, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_details()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $resp       = $this->PartnerModel->pharmacy_details($listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function partner_statistics()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $type       = $params['type'];
                        $resp       = $this->PartnerModel->partner_statistics($listing_id, $type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function pharmacy_license_no()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $list       = json_decode(file_get_contents('php://input'), TRUE);
                    $license_no = $list['license_no'];
                    $listing_id = $list['listing_id'];
                    if ($license_no == "" || $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->pharmacy_license_no($license_no, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_delivery_details()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $list               = json_decode(file_get_contents('php://input'), TRUE);
                    $reach_area         = $list['reach_area'];
                    $day_night_delivery = $list['day_night_delivery'];
                    $free_start_time    = $list['free_start_time'];
                    $free_end_time      = $list['free_end_time'];
                    $days_closed        = $list['days_closed'];
                    $store_open         = $list['store_open'];
                    $store_close        = $list['store_close'];
                    $listing_id         = $list['listing_id'];
                    $is_24hrs_available = $list['is_24hrs_available'];
                    
                    if ($reach_area == "" || $free_start_time == "" || $free_end_time == "" ||  $is_24hrs_available == "" || $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->pharmacy_delivery_details($reach_area, $day_night_delivery, $free_start_time, $free_end_time, $days_closed, $store_open, $store_close, $is_24hrs_available, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_delivery_charges()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $list                      = json_decode(file_get_contents('php://input'), TRUE);
                    $min_order                 = $list['min_order'];  
                    $is_min_order_delivery     = $list['is_min_order_delivery'];
                    $min_order_delivery_charge = $list['min_order_delivery_charge'];
                    $night_delivery_charge     = $list['night_delivery_charge'];
                    $listing_id                = $list['listing_id'];
                    if ($min_order == "" || $min_order_delivery_charge == "" || $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->pharmacy_delivery_charges($min_order,$is_min_order_delivery, $min_order_delivery_charge, $night_delivery_charge, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_payment_details()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $list         = json_decode(file_get_contents('php://input'), TRUE);
                    $payment_type = $list['payment_type'];
                    $listing_id   = $list['listing_id'];
                    if ($payment_type == "" || $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->pharmacy_payment_details($payment_type, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_partner_profile_list()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $list       = json_decode(file_get_contents('php://input'), TRUE);
                    $listing_id = $list['listing_id'];
                    if ($listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->pharmacy_partner_profile_list($listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_licence_pic()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {            
            $listing_id = $this->input->post('listing_id');            
            if ($listing_id == "" || empty($_FILES["licence_pic"]["name"])) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {                
                //unlink images
                $file_query = $this->db->query("SELECT licence_pic FROM `medical_stores` WHERE  user_id='$listing_id'");
                $get_file   = $file_query->row();
                
                if ($get_file) {
                    $licence_pic = $get_file->licence_pic;
                        $file = "images/pharmacy_images/" . $licence_pic;
						@unlink(trim($file));
						DeleteFromToS3($file);
                }
                //unlink images ends

                $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                include('s3_config.php');
                
                $img_name = $_FILES['licence_pic']['name'];
                $img_size = $_FILES['licence_pic']['size'];
                $img_tmp  = $_FILES['licence_pic']['tmp_name'];
                $ext      = getExtension($img_name);
                
                if (strlen($img_name) > 0) {
                    if ($img_size < (50000 * 50000)) {
                        if (in_array($ext, $img_format)) {
                            $licence_pic_file  = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path = 'images/pharmacy_images/' . $licence_pic_file;
                            $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);                            
                        }
                    }
                }   
                $resp = $this->PartnerModel->pharmacy_licence_pic($listing_id, $licence_pic_file);
            }            
            simple_json_output($resp);
        }
    }
    
    
    public function pharmacy_shop_establish_pic()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            
            $listing_id = $this->input->post('listing_id');
            
            if ($listing_id == "" || empty($_FILES["shop_establish_pic"]["name"])) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {                
                //unlink images
                $file_query = $this->db->query("SELECT shop_establish_pic FROM `medical_stores` WHERE  user_id='$listing_id'");
                $get_file   = $file_query->row();                
                if ($get_file) {
                    $shop_establish_pic = $get_file->shop_establish_pic;
                        $file = "images/pharmacy_images/" . $shop_establish_pic;
						@unlink(trim($file));
						DeleteFromToS3($file);
                }
                //unlink images ends    
                 $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                include('s3_config.php');
                
                $img_name = $_FILES['shop_establish_pic']['name'];
                $img_size = $_FILES['shop_establish_pic']['size'];
                $img_tmp  = $_FILES['shop_establish_pic']['tmp_name'];
                $ext      = getExtension($img_name);
                
                if (strlen($img_name) > 0) {
                    if ($img_size < (50000 * 50000)) {
                        if (in_array($ext, $img_format)) {
                            $shop_establish_pic_file = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path = 'images/pharmacy_images/' . $shop_establish_pic_file;
                            $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                            
                        }
                    }
                } 
                $resp = $this->PartnerModel->pharmacy_shop_establish_pic($listing_id, $shop_establish_pic_file);
            }
            
            simple_json_output($resp);
        }
    }
    
    public function update_pharmacy()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $list               = json_decode(file_get_contents('php://input'), TRUE);
                    $mobile             = $list['mobile'];
                    $store_name         = $list['store_name'];
                    $store_manager_name = $list['store_manager_name'];
                    $store_since        = $list['store_since'];
                    $address_line1      = $list['address_line1'];
                    $address_line2      = $list['address_line2'];
                    $state              = $list['state'];
                    $city               = $list['city'];
                    $pincode            = $list['pincode'];
                    $listing_id         = $list['listing_id'];
                    if ($mobile == "" && $store_name == "" && $store_manager_name == "" && $store_since == "" && $address_line1 == "" && $address_line2 == "" && $state == "" && $city == "" && $pincode == "" && $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->update_pharmacy($mobile, $store_name, $store_manager_name, $store_since, $address_line1, $address_line2, $state, $city, $pincode, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    
    public function pharmacy_profile_pic()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {            
            $listing_id = $this->input->post('listing_id');            
            if ($listing_id == "" || empty($_FILES["profile_pic"]["name"])) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                //unlink images
                $file_query = $this->db->query("SELECT profile_pic FROM `medical_stores` WHERE  user_id='$listing_id'");
                $get_file   = $file_query->row();
                
                if ($get_file) {
                    $profile_pic = $get_file->profile_pic;                    
                        $file = "images/healthwall_avatar/".$profile_pic;
						@unlink(trim($file));
						DeleteFromToS3($file);
                    
                }                
                //unlink images ends  
       
                $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                include('s3_config.php');
                $img_name = $_FILES['profile_pic']['name'];
                $img_size = $_FILES['profile_pic']['size'];
                $img_tmp  = $_FILES['profile_pic']['tmp_name'];
                $ext      = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if ($img_size < (50000 * 50000)) {
                        if (in_array($ext, $img_format)) {
                            $profile_pic_file  = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path = 'images/healthwall_avatar/' . $profile_pic_file;
                            $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                            
                        }
                    }
                }    
                $resp = $this->PartnerModel->pharmacy_profile_pic($listing_id, $profile_pic_file);
            }
            
            simple_json_output($resp);
        }
    }
    
    public function pharmacy_is_approval()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $list = json_decode(file_get_contents('php://input'), TRUE);
                    
                    $listing_id = $list['listing_id'];
                    if ($listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->pharmacy_is_approval($listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_lat_log()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $list       = json_decode(file_get_contents('php://input'), TRUE);
                    $latitude   = $list['latitude'];
                    $longitude  = $list['longitude'];
                    $listing_id = $list['listing_id'];
                    if ($latitude == "" || $longitude == "" || $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->pharmacy_lat_log($latitude, $longitude, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function partner_subcategory()
    {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $category = $list['category'];
                    if ($category == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->partner_subcategory($category);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
     public function partner_doctor_insert()
       {
        $this->load->model('PartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnerModel->auth();
                if ($response['status'] == 200) {
                    $list               = json_decode(file_get_contents('php://input'), TRUE);
                    $doctor_id         = $list['doctor_id'];
                    $patient_name = $list['patient_name'];
                    $mobile_no    = $list['contact_no']; 
                    $email      = $list['email'];
                    $gender        = $list['gender'];
                    $dob         = $list['date_of_birth'];
                    $blood_group        = $list['blood_group'];
                    $address         = $list['address'];
                    $city = $list['city'];
                    $state = $list['state'];
                    $pincode = $list['pincode'];
                    $medical_profile = $list['medical_profile']; 
                    
                    if ($doctor_id == "" || $patient_name == "" || $mobile_no == "" ||  $email == "" || $gender == "" ||  $dob  == "" || $blood_group == "" || $address == "" || $city == ""
                    || $state == "" || $pincode == ""  || $medical_profile == "") {
                       
                       
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PartnerModel->add_doctor($doctor_id, $patient_name, $mobile_no, $email, $gender, $dob, $blood_group, $address, $city,$state,$pincode,$medical_profile);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
}
