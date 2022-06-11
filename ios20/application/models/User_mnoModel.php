<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_mnoModel extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";

    public function check_auth_client() {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            )); 
        }
    }
    
    public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            ));
        } else {
            if ($q->expired_at < date('Y-m-d H:i:s')) {
                return json_output(401, array(
                    'status' => 401,
                    'message' => 'Your session has been expired.'
                ));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = '2030-11-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array(
                    'expired_at' => $expired_at,
                    'updated_at' => $updated_at
                ));
                return array(
                    'status' => 200,
                    'message' => 'Authorized.'
                );
            }
        }
    }
    
     public function user_mno1($receiver_id,$invoice_no,$mno_orders_id, $notification_type, $img, $title, $msg){
         
         date_default_timezone_set('Asia/Kolkata');
         $date = date('j M Y h:i A');
         
         $result = array();
         $getUserInfo = $this->db->query("SELECT `token`,`name`, `phone`, `email`, `vendor_id`,`agent` FROM `users` where id='$receiver_id'")->row_array();
         
         $token = $getUserInfo['token'];
         $agent = $getUserInfo['agent'];
    if($token != ""){
        if (!defined("GOOGLE_GCM_URL")){
           define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        }
        
   
        $notificationCheck1 = $this->db->query("SELECT * FROM `mno_notifications` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_orders_id` = '$mno_orders_id' AND `notification_type` = '$notification_type' ORDER BY `id` ASC")->row_array();
        
        // print_r($notificationCheck1); die();
        
        if(sizeof($notificationCheck1) == 0){
            $this->db->query("INSERT INTO `mno_notifications`( `receiver_id`, `invoice_no`,`mno_orders_id`,`notification_type`, `img`, `title`, `msg`) VALUES ('$receiver_id','$invoice_no','$mno_orders_id','$notification_type','$img','$title','$msg')");
        }
      
        
        $tracker = $this->user_mnoModel->tracker_notifications_user_mno($invoice_no, $mno_orders_id);
        
        $fields = array(
           'to' => $token,
           'priority' => "high",
            $agent === 'android' ? 'data' : 'notification' => array(
               "title" => $title,
               "msg" => $msg,
               "img" => $img,
               "sound" => 'default',
               "notification_type" => $notification_type,
               "invoice_no" => $invoice_no,
               "step" => $tracker
            )
       );
     
        $headers = array(
            GOOGLE_GCM_URL,
           'Content-Type: application/json',
           'Authorization: key=AAAAbiy1Vsk:APA91bF7ueNzxt9Z13XZHCzJEEuUxczgpvY0IRxTTTSUvejgOIwgc9Sdq9kEQTyzqoDLKzniD4a94g8M99eDxiiQ9JW0xQeBVSLQkfSPpyPZOdXs4DTmz3ln-Ri3Ruyrj_KYtKOg6IXS'
       );
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
      $result = curl_exec($ch);
       
      curl_close($ch);
      $result = $fields['data'];
   
    }   
       
   return $result;
     }
    
    
    public function tracker_notifications_user_mno($invoice_no, $mno_orders_id){
        $allStatuses1 = $statusdata = array();
        $getNotificationTypes1 = $this->db->query("SELECT * FROM mno_notification_types WHERE `type_for` = 2 ORDER BY `id` ASC")->result_array();
        // $getStatuses1 = $this->db_>query("SELECT * FROM `mno_notifications` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_orders_id` = '$mno_order_id' ORDER BY `id` ASC")->result_array();
        $getStatuses1 = $this->db->query("SELECT * FROM `mno_notifications` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_orders_id`= '$mno_orders_id' ORDER BY `id` ASC")->result_array();
        $getOrderInfo1 = $this->db->query("SELECT uo.suggested_pharmacy_id, uo.listing_id FROM `user_order` as uo WHERE `invoice_no` LIKE '$invoice_no' ")->row_array();
       
        date_default_timezone_set('Asia/Kolkata');
         $date = date('j M Y h:i A');
        
        foreach($getNotificationTypes1 as $g1){
            
            $s['message'] = $g1['notification_type_description'];
            $s['step'] = $g1['notification_type'];
            $s['type'] = $g1['orders'];
             $s['time'] = "";
            $s['is_completed'] = 0;
            $statuses[] = $s;
        }
        
        $pharmacy_id = $getOrderInfo1['listing_id'];
        $suggested_pharmacy_id = $getOrderInfo1['suggested_pharmacy_id'];
        
        
        if($pharmacy_id > 0 && $pharmacy_id != null && $pharmacy_id != "" ){
            
        } else if($suggested_pharmacy_id > 0 && $suggested_pharmacy_id != null && $suggested_pharmacy_id != "" ){
            
        } else  {
            foreach($statuses as $st){
                $find = -1;
                $type = $st['type'];
                
                // print_r($type); die();
                  $find = array_search($type,array_column($getStatuses1, 'notification_type'));
              
                if($type == 1 || $type == 2){
                    $st['type'] = $st['step'];
                    $st['step'] = "MNO";
                   
                }
               
                if($type == 3 || $type == 4 || $type == 5 || $type == 6){
                    $st['type'] = $st['step'];
                    $st['step'] = "FINDING_PHARMACY";
                   
                }
               
                if($type == 7 || $type == 8 || $type == 9 || $type == 10|| $type == 11){
                   $st['type'] = $st['step'];
                   $st['step'] = "OUT_FOR_DELIVERY";
                    
                }
           
                if($find > -1){
                    $st['is_completed'] = 1;
                    $st['time'] = $getStatuses1[$find]['created_at'];
                    $allStatuses[] = $st;
                } 
                    
                 $allStatuses1[] = $st;
            
            }
            //   print_r($allStatuses1);  die();
             
        }
        return $allStatuses1;
    }
   
   
    public function mno_order_notification_for_user($receiver_id,$invoice_no,$mno_orders_id, $notification_type, $img, $title, $msg){
         
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
         
        $result = array();
        $getUserInfo = $this->db->query("SELECT `token`,`name`, `phone`, `email`, `vendor_id`,`agent` FROM `users` where id='$receiver_id' AND `vendor_id` = 0")->row_array();
        $token = $getUserInfo['token'];
        $agent = $getUserInfo['agent'];
        if($token != ""){
            if (!defined("GOOGLE_GCM_URL")){
               define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            }
            $notificationCheck1 = $this->db->query("SELECT * FROM `mno_notifications` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_orders_id` = '$mno_orders_id' AND `notification_type` = '$notification_type' AND `receiver_id` = '$receiver_id' ORDER BY `id` ASC")->row_array();
            
         //   print_r($notificationCheck1); die();
            
            if(sizeof($notificationCheck1) == 0){
                $this->db->query("INSERT INTO `mno_notifications`( `receiver_id`, `invoice_no`,`mno_orders_id`,`notification_type`, `img`, `title`, `msg`) VALUES ('$receiver_id','$invoice_no','$mno_orders_id','$notification_type','$img','$title','$msg')");
            }
          
            
            $tracker = $this->user_mnoModel->tracker_notifications_for_users_mno($receiver_id,$invoice_no, $mno_orders_id);
            
        //    print_r($tracker); die();
            
            $fields = array(
               'to' => $token,
               'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array(
                   "title" => $title,
                   "msg" => $msg,
                   "img" => $img,
                   "sound" => 'default',
                   "notification_type" => $notification_type,
                   "invoice_no" => $invoice_no,
                   "step" => $tracker
                )
           );
         
            $headers = array(
                GOOGLE_GCM_URL,
               'Content-Type: application/json',
               'Authorization: key=AAAAbiy1Vsk:APA91bF7ueNzxt9Z13XZHCzJEEuUxczgpvY0IRxTTTSUvejgOIwgc9Sdq9kEQTyzqoDLKzniD4a94g8M99eDxiiQ9JW0xQeBVSLQkfSPpyPZOdXs4DTmz3ln-Ri3Ruyrj_KYtKOg6IXS'
           );
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
          $result = curl_exec($ch);
           
          curl_close($ch);
          $result = $fields['data'];
       
        }    
        return $result;
    }
     
     
    public function tracker_notifications_for_users_mno($receiver_id,$invoice_no, $mno_orders_id){
        $userTillCurrentStatus = $allStatuses1 = $statusdata = array();
        // get user_notifications
        $getNotificationTypes1 = $this->db->query("SELECT * FROM mno_notification_types WHERE `type_for` = 2 ORDER BY `step_id` ASC")->result_array();
          $getStatuses1 = $this->db->query("SELECT * FROM `mno_notifications` WHERE `invoice_no` LIKE '$invoice_no' AND `mno_orders_id`= '$mno_orders_id' AND `receiver_id` = '$receiver_id'  ORDER BY `id` ASC")->result_array();
        $getOrderInfo1 = $this->db->query("SELECT uo.suggested_pharmacy_id, uo.listing_id FROM `user_order` as uo WHERE `invoice_no` LIKE '$invoice_no' ")->row_array();
       
        date_default_timezone_set('Asia/Kolkata');
         $date = date('j M Y h:i A');
        
        foreach($getNotificationTypes1 as $g1){
            $s['message'] = $g1['notification_type_description'];
            $s['step'] = intval($g1['step_id']);
            $s['type'] = intval($g1['id']);
            $s['time'] = "";
            $s['is_completed'] = 0;
            $s['processing'] = 0;
            $statuses[] = $s;
        }
       
        $pharmacy_id = $getOrderInfo1['listing_id'];
        $suggested_pharmacy_id = $getOrderInfo1['suggested_pharmacy_id'];
        
    /*    
        if($suggested_pharmacy_id > 0 && $suggested_pharmacy_id != null && $suggested_pharmacy_id != "" ){
            
        } else  {*/
         //  print_r($getStatuses1); die();
            foreach($getStatuses1 as $gs){
                $type = $gs['notification_type'];
                $find = array_search($type,array_column($statuses, 'type'));
                $statusData = $statuses[$find];
               $statusData['time'] = $gs['created_at'];
               $statusData['is_completed'] = 1;
               
               $userTillCurrentStatus[] = $statusData;
            }
          
               foreach($statuses as $st){
                $type = $st['type'];
                $find = -1;
                $find = array_search($type,array_column($userTillCurrentStatus, 'type'));
               
              if(sizeof($userTillCurrentStatus) > 0){
                  $alreadyExists = intval($userTillCurrentStatus[$find]['type']);
              } else {
                  $alreadyExists = "";
              }
                
                    if($type != $alreadyExists && $type != 27){
                        $userTillCurrentStatus[] = $st;
                    }
                   
            }
           
       // }
        
        $pressesingSet = $pos = 0;
        foreach($userTillCurrentStatus as $track){
            if($track['is_completed'] == 0  && $pos > 0 && $pressesingSet == 0){
                $lastPos = $pos - 1;
                $userTillCurrentStatus[$pos]['processing'] = 1;
                $pressesingSet = 1;
            } else if($track['is_completed'] == 0  && $pos == 0 && $pressesingSet == 0){
                $userTillCurrentStatus[$pos]['processing'] = 1;
                $pressesingSet = 1;
            }
            $pos++;
        }
      //  print_r($userTillCurrentStatus); die();
      return $userTillCurrentStatus;
    } 
    
    
    
    
    
    public function order_tracking_mno($user_id, $invoice_no){
     
       $delivery_time = $drivingTime = $mno_orders_id = 0;
         $completedKms = $drivingDistance = $drivingTime = $estimated_delivery_time = $drivingDistance = $total_price = $tax = $chc = $discountInRupees = $DeliveryCharges = $net_amount =  0;
        $delivery_time_in_hrs_mins = "";
        $estimated_time = "";
        $this->load->model('All_booking_model');
        $this->load->model('PartnermnoModel');
        $this->load->model('user_mnoModel');
        $customer_lat = $customer_lng = $store_lat = $store_lng = $mno_lat = $mno_lng = $mno_name = $mno_contact = "";
         
        $tracker = array();
       
        $data = $details = array();
        $order_details = $this->db->query("SELECT uo.listing_id ,ms.user_id,ms.pharmacy_branch_user_id, uo.invoice_no,uo.user_id,uo.listing_id,uo.lat as customer_lat,uo.lng as customer_lng,ms.lat as store_lat,ms.lng as store_lng, msp.lat as suggested_pharmacy_lat, msp.lng as suggested_pharmacy_lng FROM `user_order` as uo left join medical_stores as ms on ((ms.user_id = uo.listing_id && ms.pharmacy_branch_user_id = 0) || (ms.pharmacy_branch_user_id = uo.listing_id  && ms.pharmacy_branch_user_id != 0)) left join mno_suggested_pharmacies as msp on (msp.id = uo.suggested_pharmacy_id)    WHERE uo.`invoice_no` LIKE '$invoice_no' AND uo.`user_id` = '$user_id'  GROUP by uo.invoice_no")->row_array();
        if(sizeof($order_details) > 0){
            
            $get_mno_details = $this->db->query("SELECT uo.order_id , mo.*, ml.phone, ml.mno_name FROM `mno_orders` as mo left join user_order as uo on (mo.invoice_no = uo.invoice_no) left join mno_list as ml on (ml.mno_id = mo.mno_id) WHERE mo.`invoice_no` LIKE '$invoice_no' AND  (mo.status = 'accepted' OR mo.status = '')  GROUP BY uo.order_id order by mo.id desc")->row_array();
            $mno_orders_id = $get_mno_details['id'];
            $status =  $get_mno_details['status'];
            $mno_id = $get_mno_details['mno_id'];
            $order_id = $get_mno_details['order_id'];
            
            $customer_lat = $order_details['customer_lat'];
            $customer_lng = $order_details['customer_lng'];
            $mno_contact = $get_mno_details['phone'];
            $mno_name = $get_mno_details['mno_name'];
            //   print_r($get_mno_details); die();
            if($status == "accepted"){
       
              $data['status'] = 1; // data found 
              
                $deliveryTimeAndCostMno = $this->PartnermnoModel->get_delivery_time_cost($order_id);
                // print_r($deliveryTimeAndCostMno); die();
                $delivery_time_in_hrs_mins = $deliveryTimeAndCostMno['delivery_time_in_hrs_mins'];
                $delivery_charges = $deliveryTimeAndCostMno['delivery_charges'];
                $delivery_time = $deliveryTimeAndCostMno['delivery_time'];
                $store_lat = $deliveryTimeAndCostMno['store_lat'];
                $store_lng = $deliveryTimeAndCostMno['store_lng'];
                $mno_lat = $deliveryTimeAndCostMno['mno_lat'];
                $mno_lng = $deliveryTimeAndCostMno['mno_lng'];
                
            } else {
                $data['status'] = 3;
            }
            
        } else {
            $data['status'] = 2;  // no data found
        }
        
       
        // $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $invoice_no);
                
        // $tracker = $this->user_mnoModel->tracker_notifications_for_users_mno($user_id,$invoice_no, $mno_orders_id);
    
        $tracker = $this->user_mnoModel->order_tracker_for_users($user_id,$invoice_no);
        
        
        $totalMsgs = sizeof($tracker);
        $latestTracker = $tracker[$totalMsgs - 1];
        // print_r($latestTracker); die();
        // $msg = $latestTracker['message'];
        if(array_key_exists('type',$latestTracker)){
            $msg = "";
            $type = $latestTracker['type'];
            if($type < 12){
                $msg = $mno_name." is pickingup your order";
            } 
            if($type == 9){
                $msg = "Order cancel";
            } 
            if($type == 12){
                $msg = $mno_name." pickedup your order";
            }
            if($type > 12){
                $msg = $mno_name." reached at doorstep";
            }
            if($type == 15){
                $msg = $mno_name." delivered your order";
            }
        } else {
            $msg = 'Waiting for night owl to accept the order';
        }
        
        $details['message'] = $msg;
        $details['customer_lat'] = floatval($customer_lat);
        $details['customer_lng'] = floatval($customer_lng);
        $details['store_lat'] = floatval($store_lat);
        $details['store_lng'] = floatval($store_lng);
        $details['mno_lat'] = floatval($mno_lat);
        $details['mno_lng'] = floatval($mno_lng);
        $details['mno_name'] = $mno_name;
        $details['mno_contact'] = $mno_contact;
        $details['estimated_time'] = intval($delivery_time);
        $details['estimated_time_mins'] = $delivery_time_in_hrs_mins;
        $details['tracker'] = $tracker;     
        
       
     
        $data['data'] = $details;
        // print_r($data); die();
              
        return $data;
    }
    
    
       public function order_tracker_for_users($receiver_id,$invoice_no){
       $statuses = $userTillCurrentStatus = $allStatuses1 = $statusdata = array();
        // get user_notifications
        // $getNotificationTypes1 = $this->db->query("SELECT * FROM mno_notification_types WHERE `type_for` = 2 ORDER BY `step_id` ASC")->result_array();
        
          $getStatuses1 = $this->db->query("SELECT * FROM `mno_notifications` WHERE `invoice_no` LIKE '$invoice_no'   ORDER BY `id` ASC")->result_array();
          
        // $getOrderInfo1 = $this->db->query("SELECT uo.suggested_pharmacy_id, uo.listing_id FROM `user_order` as uo WHERE `invoice_no` LIKE '$invoice_no' ")->row_array();
       
        date_default_timezone_set('Asia/Kolkata');
         $date = date('j M Y h:i A');
        
        // print_r($getStatuses1); die();
        
        
        foreach($getStatuses1 as $st){
            if($st['notification_type'] != 1 && $st['notification_type'] != 4 && $st['notification_type'] != 5 && $st['notification_type'] != 6 && $st['notification_type'] != 29 ){
                    $s['message'] = $st['msg'];
                    $s['time'] = strtotime($st['created_at']);
                    $s['type'] = intval($st['notification_type']);
                    $s['is_completed'] = 1;
                    $s['processing'] = 0;
                    $statuses[] = $s;
            }
            
        }
        
        if(sizeof($statuses) == 0){
            $getOrderDetails = $this->db->query("SELECT * FROM `user_order` WHERE `invoice_no` LIKE '$invoice_no' group by invoice_no")->row_array();
        
            $s['message'] = "Finding night owl";
            $s['time'] = strtotime($getOrderDetails['order_date']);
            $s['is_completed'] = 0;
            $s['processing'] = 1;
            $statuses[] = $s;
        }
       
   
      return $statuses;
    } 
    
    
    
    public function get_delivery_details($mno_lat,$mno_lng,$pharma_lat, $pharma_lng,$customer_lat,$customer_lng){
                // $customer_lat $customer_lng
                $drivingDistancePharmaToUser = $this->PartnermnoModel->GetDrivingDistance($pharma_lat, $customer_lat, $pharma_lng, $customer_lng );
             
                $drivingDistance = $drivingDistance + $drivingDistancePharmaToUser['distance'];
                $drivingTime = $drivingTime + $drivingDistancePharmaToUser['duration'];
                
                // get costing
                if($drivingDistance > 0){
                    $distanceInKm = ceil($drivingDistance / 1000);
                    $chargesTable = $this->db->query("SELECT * FROM `mno_delivery_charges`" )->result_array();
                    
                    $first = 1;
                    foreach($chargesTable as $ct){
                        $charge = $ct['charge'];
                        $max_distance = $ct['max_distance'];
                        $min_distance = $ct['min_distance'];
                        
                        if($first == 1 && $max_distance != -1){
                            $DeliveryCharges = $DeliveryCharges + $charge;
                            $remainingDist = $distanceInKm - $max_distance;
                            $completedKms = $completedKms + $max_distance;
                        }  else if($max_distance >= $distanceInKm && $min_distance < $distanceInKm){
                            $valueFoeTotalKms = $max_distance - $min_distance;
                            $currentCharges = $valueFoeTotalKms * $charge;
                            $DeliveryCharges = $DeliveryCharges + $currentCharges;
                            $remainingDist = $remainingDist - $valueFoeTotalKms;
                            $completedKms = $completedKms + $valueFoeTotalKms;
                        } else if($max_distance == -1 && $min_distance <= $distanceInKm  ){
                             $valueFoeTotalKms = $remainingDist;
                            $currentCharges = $valueFoeTotalKms * $charge;
                            $DeliveryCharges = $DeliveryCharges + $currentCharges;
                            $remainingDist = $remainingDist - $valueFoeTotalKms;
                            $completedKms = $completedKms + $valueFoeTotalKms;
                            
                        } else if($first == 1 && $max_distance == -1){
                            $valueFoeTotalKms = $distanceInKm ;
                            $currentCharges = $valueFoeTotalKms * $charge;
                            $DeliveryCharges = $DeliveryCharges + $currentCharges;
                            $remainingDist = $remainingDist - $valueFoeTotalKms;
                            $completedKms = $completedKms + $valueFoeTotalKms;
                        } else if($completedKms >= $min_distance && $completedKms < $max_distance) {
                            $valueFoeTotalKms =  $max_distance - $min_distance;
                           
                            $currentCharges = $valueFoeTotalKms * $charge;
                            $DeliveryCharges = $DeliveryCharges + $currentCharges;
                             $remainingDist = $remainingDist - $valueFoeTotalKms;
                              $completedKms = $completedKms + $valueFoeTotalKms;
                            
                        }
                        
                        $first++; 
                    }
                }
                
                
                if($drivingTime > 0){
                    $delivery_time = $drivingTime + 900; //extra 15 mins
                    
                } else {
                    $delivery_time = $drivingTime;
                }
                
                $delivery_time_hr =   gmdate("H", $delivery_time);
                $delivery_time_min =   gmdate("i", $delivery_time);
                
                
                $delivery_time_hrs = $delivery_time_hr > 0 ? $delivery_time_hr .' hours' : "";
                $delivery_time_mins = $delivery_time_min > 0 ? $delivery_time_min .' mins' : "";
                 
                $delivery_time_in_hrs_mins = $delivery_time_hrs .' '.$delivery_time_mins;
                
            
    }
    
    
}    