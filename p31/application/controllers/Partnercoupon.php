<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Partnercoupon extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('PartnercouponModel');
    }

    public function index() {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
      public function vendor_get_users() {
        $this->load->model('PartnercouponModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnercouponModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnercouponModel->auth();
                if ($response['status'] == 200) {
                  
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                    $vendor_id = $params['vendor_id'];
                    // $user_id = $params['user_id'];
                    // $coupon = $params['coupon'];
                    // if ($vendor_id == "" || $user_id == "" || $coupon == "") {
                    if ($vendor_id == "") {
                        $resp = array('status' => 400, 'message' => 'please enter vendor id');
                    } else {
                       
                        $resp = $this->PartnercouponModel->vendor_get_users($vendor_id);
                    }
                    simple_json_output($resp);
                }
            }
            
        }
    }
    
    
       public function get_card_detail() {
        $this->load->model('PartnercouponModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnercouponModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnercouponModel->auth();
                if ($response['status'] == 200) {
                  
                 
                  
                  
                    
                        $resp = $this->PartnercouponModel->get_carddetail();
                   
                    simple_json_output($resp);
                }
            }
            
        }
       }
    
    
    
    
    
    
    
     public function addcustomercoupon()
     {
        $this->load->model('PartnercouponModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PartnercouponModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PartnercouponModel->auth();
                 if ($response['status'] == 200) {
                  
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $vendor_id = $params['vendor_id'];
                    //$coupon_code= $params['couponcode'];
                    $userid=$params['userid'];
                    $card_type = $params['card_type'];
                    $card_sub_type = $params['card_sub_type']; 
                    $carditdetails = $params['carddetails']; 
                    $vendor_comment = $params['v_comment']; 
                    $total_amount = $params['totalbill'];
                    $discount = $params ['discount'];
                    $is_coupon= $params ['is_coupon']; 
                    
                    if($is_coupon==1)
                    {
                    $coupon_code= $params['couponcode'];
                   
                    
                    
                    $resp= $this->PartnercouponModel->Vendor_initiate_coupon($vendor_id,$userid,$card_type,$card_sub_type,$carditdetails,$vendor_comment,$total_amount,$coupon_code,$discount);
                    
                    
                    }
                    else
                    {
                    
                     $RGBNumber = $params['rgbnumber'];
                    
                    
                    $code= $this->PartnercouponModel->customer_Coupon_varification($RGBNumber,$vendor_id);
                    
                    
                    if($code == "0") 
                      {
                       
                       $resp = array('status' => 400, 'message' => 'Invalid card number');
                         }
                    else
                    {
                        
                        $generated_code = $code;
                        $resp= $this->PartnercouponModel->Vendor_initiate($vendor_id,$userid,$card_type,$card_sub_type,$carditdetails,$vendor_comment,$total_amount,$RGBNumber,$discount,$generated_code);
                     
                        
                    }
                    }
                    simple_json_output($resp);
                
                
                 }
            }
        }
       
       
       
       
         
     }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent,$discount,$trans_id,$getdiccounttype) {

        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        if (!defined("GOOGLE_GCM_URL"))
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields = array(
            'to' => $reg_id,
            'priority' => "high",
            $agent === 'android' ? 'data' : 'notification' => array(
                "title" => $title,
                "message" => $msg,
                "notification_image" => $img_url,
                "tag" => $tag,
                'sound' => 'default',
                "notification_type" => 'send_coupon',
                "notification_date" => $date,
                "transaction_id" => $trans_id,
                "coupon_discount" => $discount,
                "discount_type" => $getdiccounttype->discount_type
                
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    
    
    
}