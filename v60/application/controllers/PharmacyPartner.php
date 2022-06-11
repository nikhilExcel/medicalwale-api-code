<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PharmacyPartner extends CI_Controller
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
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
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
                        $resp     = $this->PharmacyPartnerModel->signup($category, $name, $email, $city, $phone, $token, $agent);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function sendotp()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter fields'
                    );
                } else {
                    $phone = $params['phone'];
                    $resp  = $this->PharmacyPartnerModel->sendotp($phone);
                }
                otp_json_output($resp);
            }
        }
    }
    
    public function login()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
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
                   
                    $res   = $this->PharmacyPartnerModel->login($phone, $token, $agent);
                }
                simple_json_output($res);
            }
        }
    }   
    
    public function update_registration_token()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
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
                    $res        = $this->PharmacyPartnerModel->update_registration_token($listing_id, $token);
                }
                simple_json_output($res);
            }
        }
    }
    
    public function order_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
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
                       /// echo 'check';
                        $resp         = $this->PharmacyPartnerModel->order_list($listing_id, $listing_type, $order_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
  /*  public function order_status()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $order_   = json_decode(file_get_contents('php://input'), TRUE);
                    
                 
               
                     if(count($order_['order']) > 0 &&  count($order_['prescription']) > 0)
                    {
                           $orders[] = $order_['order'];
                           foreach ($orders as $order_array) 
                           {
                            $order_id      = $order_array['order_id'];
                           }
                           
                           $orders1[] = $order_['prescription'];
                           foreach ($orders1 as $order_array1) 
                           {
                            $order_id1= $order_array1['order_id'];
                           }
                          if($order_id == "" && $order_id1== "") 
                          {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                            simple_json_output($resp);
                          } 
                         else 
                          {
                            $resp = $this->PharmacyPartnerModel->order_status_common($orders, $orders1,$order_id,$order_id1);
                            simple_json_output($resp);
                          } 
                    
                    }
                    else if(count($order_['order']) > 0 &&  count($order_['prescription']) == 0)
                    {
                       $orders[] = $order_['order'];
                       foreach ($orders as $order_array) 
                       {
                            $order_id      = $order_array['order_id'];
                            $delivery_time = $order_array['delivery_time'];
                            $order_status  = $order_array['order_status'];
                            $listing_id    = $order_array['listing_id'];
                            $listing_type  = $order_array['listing_type'];
                            $order_data    = $order_array['product_order'];
                        }
                      if ($order_id == "") 
                       {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                            simple_json_output($resp);
                       } 
                     else 
                      {
                        $resp = $this->PharmacyPartnerModel->order_status($order_id, $delivery_time, $order_status, $listing_id, $listing_type, $order_data);
                        simple_json_output($resp);
                          
                      } 
                    }
                    else if (count($order_['order']) == 0 &&  count($order_['prescription']) > 0)
                    {
                        $orders[] = $order_['prescription'];
                        foreach ($orders as $order_array) 
                        {
                            $order_id           = $order_array['order_id'];
                            $order_status       = $order_array['order_status'];
                            $delivery_time      = $order_array['delivery_time'];
                            $prescription_order = $order_array['prescription_order'];
                        }
                        if ($order_id == "" && $order_status == "") 
                        {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                            simple_json_output($resp);
                        } 
                        else 
                       {
                        $resp = $this->PharmacyPartnerModel->prescription_status($order_id, $order_status, $prescription_order, $delivery_time);
                        simple_json_output($resp);
                       } 
                    }
                  
                    
                    
                }
            }
        }
    }
    */
    
   public function order_status()
     {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                  //  $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($this->input->post('user_id') == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $this->input->post('user_id');
                        $order_details = $this->input->post('order');
                       
                        $prescription_details = $this->input->post('prescription');
                       
                        
                          if( $order_details !="" &&  $prescription_details!="")
                             {
                                 $product_details_new = json_decode($order_details,TRUE);
                                 $order_id_data = $product_details_new['order_id']; 
                                 
                                 $prescription_details_new = json_decode($prescription_details,TRUE);
                                 $prescription_id_data = $prescription_details_new['order_id'];  
                          
                          if($order_id_data == "" && $prescription_id_data== "") 
                          {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter Order ID' 
                            );
                            simple_json_output($resp);
                          } 
                         else 
                          {
                            $resp = $this->PharmacyPartnerModel->order_status_common($order_id_data, $prescription_id_data,$order_details,$prescription_details,$user_id);
                            simple_json_output($resp);
                          } 
                    
                    }
                          else if($order_details !="" && $prescription_details=="")
                             {
                                 $product_details_new = json_decode($order_details,TRUE);
                                 $order_id      = $product_details_new['order_id'];
                                 $delivery_time = $product_details_new['delivery_time'];
                                 $order_status  = $product_details_new['order_status'];
                                 $listing_id    = $product_details_new['listing_id'];
                                 $listing_type  = $product_details_new['listing_type'];
                                 $order_data    = $product_details_new['product_order'];
                       
                      if ($order_id == "") 
                       {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                            simple_json_output($resp);
                       } 
                     else 
                      {
                        $resp = $this->PharmacyPartnerModel->order_status($order_id, $delivery_time, $order_status, $listing_id, $listing_type, $order_data,$user_id);
                        simple_json_output($resp);
                          
                      } 
                    }
                    else if ($order_details =="" && $prescription_details !="")
                    {
                        
                        $prescription_details_new = json_decode($prescription_details,TRUE);
                        $order_id           = $prescription_details_new['order_id'];
                        $order_status       = $prescription_details_new['order_status'];
                        $delivery_time      = $prescription_details_new['delivery_time'];
                        $prescription_order = $prescription_details_new['prescription_order'];
                       
                        if ($order_id == "") 
                        {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                            simple_json_output($resp);
                        } 
                        else 
                       {
                        $resp = $this->PharmacyPartnerModel->prescription_status($order_id, $order_status, $prescription_order, $delivery_time,$user_id);
                        simple_json_output($resp);
                       } 
                    } 
                    }
                   
                  
                }
            }
        }
    }  
    
    
    
    public function prescription_status()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
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
                        $resp = $this->PharmacyPartnerModel->prescription_status($order_id, $order_status, $prescription_order, $delivery_time);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
  /* 
  old file 
  public function order_deliver_cancel()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
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
                        $resp = $this->PharmacyPartnerModel->order_deliver_cancel($order_id, $cancel_reason, $type, $notification_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }*/
    
    
    //new file
     public function order_deliver_cancel()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $cancel_order      = json_decode(file_get_contents('php://input'), TRUE);
                    $invoice_id          = $cancel_order['invoice_id'];
                    $type              = $cancel_order['type'];
                    $notification_type = $cancel_order['notification_type'];
                    $cancel_reason     = $cancel_order['cancel_reason'];
                    if ($invoice_id == "" && $cancel_reason == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->order_deliver_cancel($invoice_id, $cancel_reason, $type, $notification_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function add_pharmacy()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
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
                        $resp = $this->PharmacyPartnerModel->add_pharmacy($store_name, $store_manager_name, $store_since, $address_line1, $address_line2, $state, $city, $pincode, $latitude, $longitude, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_details()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $resp       = $this->PharmacyPartnerModel->pharmacy_details($listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function partner_statistics()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
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
                        $resp       = $this->PharmacyPartnerModel->partner_statistics($listing_id, $type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function pharmacy_license_no()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
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
                        $resp = $this->PharmacyPartnerModel->pharmacy_license_no($license_no, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_delivery_details()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
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
                        $resp = $this->PharmacyPartnerModel->pharmacy_delivery_details($reach_area, $day_night_delivery, $free_start_time, $free_end_time, $days_closed, $store_open, $store_close, $is_24hrs_available, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_delivery_charges()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
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
                        $resp = $this->PharmacyPartnerModel->pharmacy_delivery_charges($min_order,$is_min_order_delivery, $min_order_delivery_charge, $night_delivery_charge, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_payment_details()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
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
                        $resp = $this->PharmacyPartnerModel->pharmacy_payment_details($payment_type, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_partner_profile_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list       = json_decode(file_get_contents('php://input'), TRUE);
                    $listing_id = $list['listing_id'];
                    if ($listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->pharmacy_partner_profile_list($listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_licence_pic()
    {
        $this->load->model('PharmacyPartnerModel');
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
                $resp = $this->PharmacyPartnerModel->pharmacy_licence_pic($listing_id, $licence_pic_file);
            }            
            simple_json_output($resp);
        }
    }
    
     public function pharmacy_logo()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {            
            $listing_id = $this->input->post('listing_id');            
            if ($listing_id == "" || empty($_FILES["logo"]["name"])) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {                
                //unlink images
                $file_query = $this->db->query("SELECT logo FROM `medical_stores` WHERE  user_id='$listing_id'");
                $get_file   = $file_query->row();
                
                if ($get_file) {
                    $licence_pic = $get_file->logo;
                        $file = "images/pharmacy_images/" . $licence_pic;
						@unlink(trim($file));
						DeleteFromToS3($file);
                }
                //unlink images ends

                $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                include('s3_config.php');
                
                $img_name = $_FILES['logo']['name'];
                $img_size = $_FILES['logo']['size'];
                $img_tmp  = $_FILES['logo']['tmp_name'];
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
                $resp = $this->PharmacyPartnerModel->pharmacy_logo($listing_id, $licence_pic_file);
            }            
            simple_json_output($resp);
        }
    }
    public function pharmacy_shop_establish_pic()
    {
        $this->load->model('PharmacyPartnerModel');
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
                $resp = $this->PharmacyPartnerModel->pharmacy_shop_establish_pic($listing_id, $shop_establish_pic_file);
            }
            
            simple_json_output($resp);
        }
    }
    
    public function update_pharmacy()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
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
                        $resp = $this->PharmacyPartnerModel->update_pharmacy($mobile, $store_name, $store_manager_name, $store_since, $address_line1, $address_line2, $state, $city, $pincode, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    
    public function pharmacy_profile_pic()
    {
        $this->load->model('PharmacyPartnerModel');
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
                $resp = $this->PharmacyPartnerModel->pharmacy_profile_pic($listing_id, $profile_pic_file);
            }
            
            simple_json_output($resp);
        }
    }
    
    public function pharmacy_is_approval()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list = json_decode(file_get_contents('php://input'), TRUE);
                    
                    $listing_id = $list['listing_id'];
                    if ($listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->pharmacy_is_approval($listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_lat_log()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
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
                        $resp = $this->PharmacyPartnerModel->pharmacy_lat_log($latitude, $longitude, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function partner_subcategory()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $category = $list['category'];
                    if ($category == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->partner_subcategory($category);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //**************************************************Login Staff System***************************************************
    public function add_staff_member()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list               = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id            = $list['user_id'];
                    $mobile             = $list['mobile'];
                    $staff_name         = $list['staff_name'];
                    $staff_email        = $list['staff_email'];
                   
                    if ($mobile == "" && $staff_name == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->add_staff_member($user_id,$mobile, $staff_name, $staff_email);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function staff_member_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->staff_member_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function delete_staff_member()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list               = json_decode(file_get_contents('php://input'), TRUE);
                    $staff_user_id            = $list['staff_user_id'];
                  
                    if ($staff_user_id == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->delete_staff_member($staff_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    //end Login Flow
    
     //**************************************************Login Staff System***************************************************
    public function inventory_product_type_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->inventory_product_type_list($user_id,$staff_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function inventory_category_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->inventory_category_list($user_id,$staff_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function inventory_product_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->inventory_product_list($user_id,$staff_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function add_inventory_product()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   // $list                   = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id                = $this->input->post('user_id');
                    $staff_user_id          = $this->input->post('staff_user_id');
                    $product_name           = $this->input->post('product_name');
                    $cost_price             = $this->input->post('cost_price');
                    $mrp                    = $this->input->post('mrp');
                    $selling_price          = $this->input->post('selling_price');
                    $manufacture_date       = $this->input->post('manufacture_date');
                    $expiry_date            = $this->input->post('expiry_date');
                    $distributor_name       = $this->input->post('distributor_name');
                    $category               = $this->input->post('pd_pc_id');
                    $sub_category           = $this->input->post('pd_psc_id');
                    $product_type           = $this->input->post('product_type');
                    $barcode                = $this->input->post('barcode');
                    $ingredients            = $this->input->post('ingredients');
                    $size                   = $this->input->post('size');
                   
                    if ($user_id == "" && $staff_user_id == "" && $product_name == "" && $cost_price =="" && $mrp == "" && $selling_price == "" && $barcode == "" && $category == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->add_inventory_product($user_id,$staff_user_id, $product_name, $cost_price,$mrp, $selling_price, $manufacture_date, $expiry_date, $distributor_name, $category, $sub_category, $product_type, $barcode, $ingredients, $size);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function edit_inventory_product()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   // $list                   = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id                = $this->input->post('user_id');
                    $staff_user_id          = $this->input->post('staff_user_id');
                    $product_id             = $this->input->post('product_id');
                    $product_name           = $this->input->post('product_name');
                    $cost_price             = $this->input->post('cost_price');
                    $mrp                    = $this->input->post('mrp');
                    $selling_price          = $this->input->post('selling_price');
                    $manufacture_date       = $this->input->post('manufacture_date');
                    $expiry_date            = $this->input->post('expiry_date');
                    $distributor_name       = $this->input->post('distributor_name');
                    $category               = $this->input->post('pd_pc_id');
                    $sub_category           = $this->input->post('pd_psc_id');
                    $product_type           = $this->input->post('product_type');
                    $barcode                = $this->input->post('barcode');
                    $ingredients            = $this->input->post('ingredients');
                    $size                   = $this->input->post('size');
                   
                    if ($user_id == "" && $staff_user_id == "" && $product_name == "" && $cost_price =="" && $mrp == "" && $selling_price == "" && $barcode == "" && $category == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->edit_inventory_product($user_id,$staff_user_id, $product_id, $product_name, $cost_price,$mrp, $selling_price, $manufacture_date, $expiry_date, $distributor_name, $category, $sub_category, $product_type, $barcode, $ingredients, $size);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function inventory_distributor_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->inventory_distributor_list($user_id,$staff_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function add_inventory_distributor()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list                         = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id                      = $list['user_id'];
                    $staff_user_id                = $list['staff_user_id'];
                    $distributor_name             =   $list['distributor_name'];
        	        $manufacturer_name            =   $list['manufacturer_name'];
        	        $distributor_phone            =   $list['distributor_phone'];
        	        $manufacturer_phone           =   $list['manufacturer_phone'];
        	        $map_location                 =   $list['map_location'];
        	        $lat                          =   $list['lat'];
        	        $lng                          =   $list['lng'];
                    if ($user_id == "" && $staff_user_id == "" && $distributor_name == "" && $distributor_phone =="" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->add_inventory_distributor($user_id,$staff_user_id, $distributor_name, $manufacturer_name , $distributor_phone, $manufacturer_phone, $map_location, $lat, $lng);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function edit_inventory_distributor()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list                         = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id                      =   $list['user_id'];
                    $staff_user_id                =   $list['staff_user_id'];
                    $distributor_id               =   $list['distributor_id'];
                    $distributor_name             =   $list['distributor_name'];
        	        $manufacturer_name            =   $list['manufacturer_name'];
        	        $distributor_phone            =   $list['distributor_phone'];
        	        $manufacturer_phone           =   $list['manufacturer_phone'];
        	        $map_location                 =   $list['map_location'];
        	        $lat                          =   $list['lat'];
        	        $lng                          =   $list['lng'];
                    if ($user_id == "" && $staff_user_id == "" && $distributor_name == "" && $distributor_phone =="" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->edit_inventory_distributor($user_id,$staff_user_id,$distributor_id, $distributor_name, $manufacturer_name , $distributor_phone, $manufacturer_phone, $map_location, $lat, $lng);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function inventory_warehouse_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->inventory_warehouse_list($user_id,$staff_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function add_inventory_warehouse()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list                         = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id                      = $list['user_id'];
                    $staff_user_id                = $list['staff_user_id'];
                    $wname                        =   $list['wname'];
        	        $map_location                 =   $list['map_location'];
        	        $lat                          =   $list['lat'];
        	        $lng                          =   $list['lng'];
                    if ($user_id == "" && $staff_user_id == "" && $wname == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->add_inventory_warehouse($user_id,$staff_user_id, $wname, $map_location, $lat, $lng);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function edit_inventory_warehouse()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list                         = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id                      =   $list['user_id'];
                    $staff_user_id                =   $list['staff_user_id'];
                    $warehouse_id                 =   $list['warehouse_id'];
                    $wname                        =   $list['wname'];
        	        $map_location                 =   $list['map_location'];
        	        $lat                          =   $list['lat'];
        	        $lng                          =   $list['lng'];
                    if ($user_id == "" && $staff_user_id == "" && $wname == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->edit_inventory_warehouse($user_id,$staff_user_id, $warehouse_id, $wname, $map_location, $lat, $lng);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function inventory_po_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->inventory_po_list($user_id,$staff_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function add_inventory_po()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id        = $this->input->post('user_id');
                    $staff_user_id  = $this->input->post('staff_user_id');
                    $po_number      = $this->input->post('po_number');
                    $po_status      = $this->input->post('po_status');
                    $po_date        = $this->input->post('po_date');
                    $warehouse_id   = $this->input->post('warehouse_id');
                    $distributor_id = $this->input->post('distributor_id');
                    $product_details = $this->input->post('product_details');
                    
                if ($user_id == "" || $staff_user_id == "" || $po_number == "" || $po_date == "" || count($product_details) <=0 || $warehouse_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->add_inventory_po($user_id,$staff_user_id,$po_number,$po_status,$product_details,$po_date,$warehouse_id,$distributor_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function edit_inventory_po()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id        = $this->input->post('user_id');
                    $staff_user_id  = $this->input->post('staff_user_id');
                    $po_id          = $this->input->post('po_id');
                    $po_number      = $this->input->post('po_number');
                    $po_status      = $this->input->post('po_status');
                    $po_date        = $this->input->post('po_date');
                    $warehouse_id   = $this->input->post('warehouse_id');
                    $distributor_id = $this->input->post('distributor_id');
                    $product_details = $this->input->post('product_details');
                    
                if ($user_id == "" || $staff_user_id == "" || $po_number == "" || $po_date == "" || count($product_details) <=0 || $warehouse_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->edit_inventory_po($user_id,$staff_user_id,$po_id,$po_number,$po_status,$product_details,$po_date,$warehouse_id,$distributor_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    //**************************************************Billing System***************************************************
    public function product_barcode_scanner()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $barcode        = $list['barcode'];
                    $user_id        = $list['user_id'];
                    $hub_user_id    = $list['staff_user_id'];
                    if ($barcode == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->product_barcode_scanner($user_id,$hub_user_id,$barcode);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function product_inventory_bill()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   // $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id        = $this->input->post('user_id');
                    $hub_user_id    = $this->input->post('staff_user_id');
                    $date           = $this->input->post('date');
                    $invoice_no     = $this->input->post('invoice_no');
                    $product_details= $this->input->post('product_details');
                    $total_quantity = $this->input->post('total_quantity');
                    $total_price    = $this->input->post('total_price');
                    $discount       = $this->input->post('discount');
                    $tax            = $this->input->post('tax');
                    $net_amount     = $this->input->post('net_amount');
                    $payment_method = $this->input->post('payment_method');
                    $customer_name  = $this->input->post('customer_name');
                    $customer_phone = $this->input->post('customer_phone');
                    $customer_email = $this->input->post('customer_email');
                    $customer_address = $this->input->post('customer_address');
                    $doctor_name     = $this->input->post('doctor_name');
                    $bhc_no         = $this->input->post('bhc_no');
                    //count($product_details) <=0
                    
                if ($user_id == "" || $hub_user_id == "" || $date == "" || $invoice_no == ""   || $total_quantity == "" || $total_price == "" || $net_amount == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->product_inventory_bill($user_id,$hub_user_id,$date,$invoice_no,$product_details,$total_quantity,$total_price,$discount,$tax,$net_amount,$payment_method,$customer_name,$customer_phone,$customer_email,$customer_address,$doctor_name,$bhc_no);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
     public function monthly_report()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   // $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id        = $list['user_id'];
                    $hub_user_id    = $list['staff_user_id'];
                    if ($user_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->monthly_report($user_id,$hub_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function get_customer_detail_mobile()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   // $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $mobile        = $list['mobile'];
                    //$hub_user_id    = $list['staff_user_id'];
                    if ($mobile == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->get_customer_detail_mobile($mobile);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function inventory_dashboard()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $from_date        = $list['from_date'];
                    $to_date          = $list['to_date'];
                    $user_id        = $list['user_id'];
                    $hub_user_id    = $list['staff_user_id'];
                    if ($user_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->inventory_dashboard($user_id,$hub_user_id,$from_date,$to_date);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function stock_inventory_dashboard()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id        = $list['user_id'];
                    $hub_user_id    = $list['staff_user_id'];
                    $date_from        = $list['date_from'];
                    $date_to        = $list['date_to'];
                    $page = $list['page'];
                    if ($user_id == "" || $date_from =="" || $date_to == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->stock_inventory_dashboard($user_id,$hub_user_id,$page,$date_from,$date_to);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function check_json()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                  //  $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $product_details        = $this->input->post('product_details');
                  
                   
                        $resp = $this->PharmacyPartnerModel->check_json($product_details);
                    
                   // json_outputs($resp);
                }
            }
        }
    }
     public function check_notification()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id        = $list['user_id'];
                    $title          = $list['title'];
                    $msg            = $list['msg'];
                
                    if ($user_id == "" || $title =="" || $msg == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->check_notification($user_id,$title,$msg);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
      public function booking_details()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['invoice_no'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $invoice_no = $params['invoice_no'];
                          
                        $resp       = $this->PharmacyPartnerModel->booking_details($user_id,$invoice_no);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
}
