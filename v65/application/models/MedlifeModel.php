<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MedlifeModel extends CI_Model {

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
                 if ($q->expired_at < date("Y-m-d H:i:s", strtotime("-1 days"))) {
                return json_output(401, array(
                    'status' => 401,
                    'message' => 'Your session has been expired.'
                ));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = date("Y-m-d H:i:s", strtotime("+1 days"));//'2020-12-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array(
                    'expired_at' => $expired_at,
                    'updated_at' => $updated_at
                ));
               // echo $this->db->last_query();
                return array(
                    'status' => 200,
                    'message' => 'Authorized.'
                );
            }
                
            }
        }
    }

    public function encrypt($str) {
        //echo $str;
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv = hash('MD5', 'mdwale8655328655', true);
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $encrypted = mcrypt_generic($module, $str);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        return base64_encode($encrypted);
    }

    public function decrypt($str) {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv = hash('MD5', 'mdwale8655328655', true);
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $str = mdecrypt_generic($module, base64_decode($str));
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        $slast = ord(substr($str, -1));
        $str = substr($str, 0, strlen($str) - $slast);
        return $str;
    }
    
    public function medlife_order_callback1($orderId, $orderState, $estimatedDeliveryDate, $totalAmount, $payableAmount, $discountAmount, $createdTime, $prepayments, $settlements, $orderItems, $deliveryCharge)
    {
        date_default_timezone_set('Asia/Kolkata');
        $updated_at = date('Y-m-d H:i:s');
        $medlife_array = array(
            'orderId' => $orderId,
            'orderState' => $orderState,
            'estimatedDeliveryDate' =>$estimatedDeliveryDate,
            'totalAmount' => $totalAmount,
            'payableAmount' => $payableAmount,
            'discountAmount' => $discountAmount,
            'createdTime' => $createdTime,
            'prepayments' => $prepayments,
            'settlements' => $settlements,
            'orderItems'  => $orderItems,
            'deliveryCharge' => $deliveryCharge
            );
            
           $this->db->insert('medlife_order',$medlife_array);
            if ($this->db->affected_rows()) {
                return array(
                    'status' => 200,
                    'message' => 'success'
                );
            } else {
                return array(
                    'status' => 201,
                    'message' => 'error'
                );
            }
    }
    
    
      public function medlife_order_callback($orderId, $orderState, $estimatedDeliveryDate, $totalAmount, $payableAmount, $discountAmount, $createdTime, $prepayments, $settlements, $orderItems, $deliveryCharge,$rxId,$deliveryTime)
    {
        
        date_default_timezone_set('Asia/Kolkata');
        $updated_at = date('Y-m-d H:i:s');
        //added for notification send on status change
        
         function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $orderState, $agent) {
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
                "tag" => $tag,
                'sound' => 'default',
                "notification_image" => $img_url,
                "notification_type" => 'NOTIFICATION_MEDLIFE_ORDER_STATUS',
                "notification_date" => $date,
                "notification_status" => $orderState
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
        
        
        
        
        ////added code for settelment table entry 
        if($settlements == array() || $settlements == '')
        {
            
        }
        else
        {
            foreach ($settlements as $row)
            {
               $paymentId = $row['paymentId'];
                $mode      = $row['mode'];
                $amount   = $row['amount'];
                $createdTime = $row['createdTime'];
            }
           $settlement_array = array(
                'orderId' => $orderId,
                'paymentId' => $paymentId,
                'mode'      => $mode,
                'amount'    => $amount,
                'createdTime' => $createdTime,
                );
               // print_r($settlement_array);
             $this->db->insert('medlife_settlement',$settlement_array);
            
        }
        //end
        
        //added code for prepayments for medlife 
        if($prepayments == array() || $prepayments == '')
        {
             
        }
        else
        {
             foreach ($prepayments as $row)
            {
                
               $paymentId = $row['paymentId'];
                $mode      = $row['mode'];
                $paymentMode   = $row['paymentMode'];
                $amount = $row['amount'];
                 $uniqueTransactionId = $row['uniqueTransactionId'];
                $transferId      = $row['transferId'];
                $cardNo   = $row['cardNo'];
                $terminalId = $row['terminalId'];
                 $paymentSource   = $row['paymentSource'];
                $createdTime = $row['createdTime'];
            }
           $prepayment_array = array(
                'orderId' => $orderId,
                'paymentId' => $paymentId, 
                'mode'      => $mode,
                'amount'    => $amount,
                 'uniqueTransactionId' => $uniqueTransactionId,
                'transferId' => $transferId,
                'cardNo'      => $cardNo,
                'terminalId'    => $terminalId,
                'paymentSource'    => $paymentSource,
                'createdTime' => $createdTime,
                );
              //  print_r($prepayment_array);
             $this->db->insert('medlife_prepayment',$prepayment_array);
        }
        //end 
        
        
        //added code for medlife product info 
        
          if($orderItems == array() || $orderItems == '')
        {
             
        }
        else
        {
             foreach ($orderItems as $row)
            {
                
                $quantity = $row['quantity'];
                $amount      = $row['amount'];
                 $total_mrp   = $row['mrp'];
                 $product    = $row['product'];
            }
            
           
            
            foreach($product as $row1)
            {
               // print_r($row1);
             $brand   = $product['brand'];
                $product_id = $product['id'];
                 $manufacturer = $product['manufacturer'];
                $rxDrug      = $product['rxDrug'];
                $medicineType   = $product['medicineType'];
                $packageType = $product['packageType'];
                 $packageQuantity   = $product['packageQuantity'];
                $mrp = $product['mrp'];
                $strength   = $product['strength'];
                if (array_key_exists("packageUnit",$product))
                   {
                     $packageUnit = $product['packageUnit'];
                   }
               else
                   {
                   $packageUnit = "";
                    }
              
            }
            
            
           $orderItems_array = array(
                'orderId' => $orderId,
                'quantity' => $quantity,
                'amount'      => $amount,
                'brand'    => $brand,
                 'product_id' => $product_id,
                'manufacturer' => $manufacturer,
                'rxDrug'      => $rxDrug,
                'medicineType'    => $medicineType,
                'packageType'    => $packageType,
                'packageQuantity' => $packageQuantity,
                'mrp'    => $mrp,
                'strength'    => $strength,
                'packageUnit' => $packageUnit,
                'total_mrp' => $total_mrp,
                );
              //  print_r($prepayment_array);
             $this->db->insert('medlife_product_details',$orderItems_array);
        }
        
        //end
        
        $medlife_array = array(
            'orderId' => $orderId,
            'orderState' => $orderState,
            'estimatedDeliveryDate' =>$estimatedDeliveryDate,
            'totalAmount' => $totalAmount,
            'payableAmount' => $payableAmount,
            'discountAmount' => $discountAmount,
            'createdTime' => $createdTime,
            // 'prepayments' => $prepayments,
            // 'settlements' => $settlements,
            // 'orderItems'  => $orderItems,
            'deliveryCharge' => $deliveryCharge,
            'rxID'      => $rxId,
            'deliveryTime' => $deliveryTime
            );
            
            //added by zak for medlife notification 
            
             $userdata = $this->db->query("SELECT user_id from user_order where rxID = $rxId");
              $userdata_count = $userdata->num_rows();
               $userdataa = $userdata->row();
              if($userdata_count>0)
              {
               
                $user_order_id = $userdataa->user_id;
            
              $usr_info = $this->db->query("SELECT token, agent, token_status,phone from users where id = $user_order_id");
              
           // echo 'SELECT web_token, agent, token_status,phone from users where id= '.$listing_id[$i];
            $usr_info_status = $usr_info->num_rows();
            $usr_info = $usr_info->row();
            if ($usr_info_status > 0) {
                $usr_info_status = $usr_info->token_status;
                $usr_info_phone = $usr_info->phone;
                $reg_id = $usr_info->token;
                $agent = $usr_info->agent;
                $msg = 'Your order status changed to '.$orderState;
                $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
                $tag = 'text';
                $key_count = '2';
                $title = 'Your, Medlife Order status Changed.';
            }
            
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $orderState, $agent);
            
              }
            
            //end
            
           $this->db->insert('medlife_order',$medlife_array);
            if ($this->db->affected_rows()) {
                return array(
                    'status' => 200,
                    'message' => 'success'
                );
            } else {
                return array(
                    'status' => 201,
                    'message' => 'error'
                );
            }
    }
    
}