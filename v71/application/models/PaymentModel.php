<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('razorpay-php/Razorpay.php');
use Razorpay\Api\Api as RazorpayApi;

class PaymentModel extends CI_Model {

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
    
    public function insert_paypal_token_payment_id($payment_id,$access_token,$uni_id,$invoice_id)
    {
        $insert_array = array(
            'payment_id' => $payment_id,
            'access_token' => $access_token,
            'tracking_id' => $uni_id
        );
        $this->db->insert('paypal_payment_details', $insert_array);
        return  array(
            "status" => 200,
            "message" => "success",
            "payment_id" => $payment_id,
            "invoice_id" => $invoice_id,
            "payment_url" => "https://www.sandbox.paypal.com/checkoutnow?token=".$payment_id."&country.x=IN&locale.x=en_IN"
        );
    }
    
    public function generate_razorpay_payment_id($user_id,$amount){
        $tid          = time();
        $api = new RazorpayApi('rzp_live_UmKdgCLkfZ5ut8', 'RoEl2iNvznpTfrPGzF0IJDuM');
        //$api = new RazorpayApi('rzp_test_snt63i7ZRb8Mtc', 'wUfqfeQG5ukMO5jrTyEIAXMc');
		$order  = $api->order->create(array(
			'receipt' => 'rcptid_'.$tid.'_'.$user_id,
			'amount' => $amount*100,
			'currency' => 'INR'
		));
		$orderId = $order['id'];
		$response[] = array(
            'amount' => $amount,
            'orderId' => $orderId
        );
        return $response;
    }
    
    public function paypal_capture($payment_id)
    {
        $get_token    = $this->db->query("SELECT access_token,tracking_id from paypal_payment_details WHERE payment_id='$payment_id' limit 1")->row_array();
        $access_token = $get_token['access_token'];
        $tracking_id = $get_token['tracking_id'];
        date_default_timezone_set('Asia/Kolkata');
        $request_id = date("YmdHis");
        $headers      = array(
           'Authorization:Bearer '.$access_token,
           'Content-Type:application/json',
           'Prefer:return=representation',
           'PayPal-Client-Metadata-Id:'.$tracking_id,
           'PayPal-Request-Id:'.$request_id
        );
        $url = "https://api.sandbox.paypal.com/v2/checkout/orders/".$payment_id."/capture";
        $ch  = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        
        $headers      = array(
           'Authorization:Bearer '.$access_token
        );
        $url = "https://api.sandbox.paypal.com/v2/checkout/orders/".$payment_id;
        $ch  = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $res=json_decode($result,true);
        $payment_status=$res['status'];
        return  array(
            "status" => 200,
            "message" => "success",
            "payment_id" => $payment_id,
            "payment_status" => $payment_status
        );
    }

    public function insert_payment_status($user_id, $listing_id, $trans_id, $status, $type, $order_id, $order_type,$amount, $status_mesg, $discount, $discount_rupee, $payment_type,$opd,$card_type) {
        
      
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        
        
        $Expire_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date)) . " + 1 year"));
        if($status == "1"){
            $creadit_debit = 0;
        }else{
            $creadit_debit = 2;
        }    
        
         
        $upadte_ledger_array = array(
            'user_id'       => $user_id,
            'listing_id'    => $listing_id,
            'trans_id'      => $trans_id,
            'trans_type'    => $creadit_debit,
            'order_id'      => $order_id,
            'amount'        => $amount,
            'order_status'  => $status=='1'?'success':'failed',
            'order_type'    => $order_type,
            'status_message'=> $status_mesg,
            'discount'      => $discount,
            'discount_rupee'=> $discount_rupee,
            'trans_time'    => $date,
            'trans_mode'=> $payment_type,
            //'vendor_category'=> $vendor_category
            'transaction_sub_type'=>$card_type,
            'vendor_category'=>  $type
        );
        
        
    //   echo 'dsfnhdsfh'.$payment_type;
       
       
        if($payment_type != '3'){
            
            
            
            $this->db->insert('user_ledger',$upadte_ledger_array);
          //echo 'bahger'; 
            
            
              if($payment_type == '6')
        {
            //echo 'under';
        
            $query = $this->db->query("select * from user_ledger_balance where user_id='$user_id'");
            $row_bal = $query->row();
            $row_count =$query ->num_rows();
            if($row_count>0)
            {
                   
        
        
                
                $existing_balance = $row_bal->ledger_balance;
                $existing_lock_balance =$row_bal->lock_amount;
                $total_balance = $existing_balance - $amount; 
              // $total_lock_amount = $existing_lock_balance + $convert_rs;
                $user_ledger_balance = array(
                    'ledger_balance' =>$total_balance
                    );
                     $this->db->where('user_id', $user_id);  
                $this->db->update('user_ledger_balance', $user_ledger_balance); 
            }
            else
            {
              
            }
            
        
            
            
        }
        }
       
        
        if($status == 1){
            
            //12=Nursing, 10=Lab, 8=hospital
            if($type==='12' || $type==='10' || $type==='8'){
                if($opd=="opd")
                {
                    $sql = "UPDATE hospital_booking_master SET status = 5 WHERE order_id = '$order_id'"; 
                }
                else
                {
                $data = array('status' => 'confirmed');
                //$sql = "UPDATE booking_master SET status = 'payment' WHERE booking_id = '$order_id'";  
                 $this->db->where('booking_id', $order_id);  
                $this->db->update('booking_master', $data); 
                }
            }
            else if($type == '5'){
                //$sql = "UPDATE doctor_booking_master SET status = 5 WHERE order_id = '$order_id'";   
                
                $status_change = array(
                    'status' => 5                                                                  
                    );
                $this->db->where('booking_id', $order_id); 
                $this->db->update('doctor_booking_master',$status_change);
               
            }else if($type == '6'){
                $status_change = array(
                    'status' => 5                                                                  
                    );
                $this->db->where('booking_id', $order_id); 
                $this->db->update('booking_master',$status_change);
            }
            else if($type == '36'){
                $status_change = array(
                    'status' => 5                                                                  
                    );
                $this->db->where('booking_id', $order_id); 
                $this->db->update('booking_master',$status_change);
            }
            else if($type == '37'){
                $status_change = array(
                    'status' => 5                                                                  
                    );
                $this->db->where('booking_id', $order_id); 
                $this->db->update('booking_master',$status_change);
                
                $status_change1 = array(
                    'payment_status' => 5                                                                  
                    );
                $this->db->where('booking_id', $order_id); 
                $this->db->update('diet_user_package_history',$status_change1);
            }
            else if($type == '39'){
               
                $status_change = array(
                    'status' => 5                                                                  
                    );
                $this->db->where('booking_id', $order_id); 
                $this->db->update('booking_master',$status_change);
            }
        }
        return array(
            'status' => 201,
            'message' => 'success'
        );
        
    }
    
    
     public function insert_payment_status_v1($user_id, $listing_id, $trans_id, $status, $type, $order_id, $order_type,$amount, $status_mesg, $discount, $discount_rupee, $payment_type,$opd,$card_type) {
        
      
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        
        
        $Expire_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date)) . " + 1 year"));
        if($status == "1"){
            $creadit_debit = 0;
        }else{
            $creadit_debit = 2;
        }    
        
         $debit='0';
        $upadte_ledger_array = array(
            'user_id'       => $user_id,
            'listing_id'    => $listing_id,
            'trans_id'      => $trans_id,
            'trans_type'    => $creadit_debit,
            'order_id'      => $order_id,
            'amount'        => $amount,
            'order_status'  => $status=='1'?'success':'failed',
            'order_type'    => $order_type,
            'status_message'=> $status_mesg,
            'discount'      => $discount,
            'discount_rupee'=> $discount_rupee,
            'trans_time'    => $date,
            'trans_mode'=> $payment_type,
            //'vendor_category'=> $vendor_category
            'transaction_sub_type'=>$card_type,
            'vendor_category'=>  $type
        );
        if(empty($discount))
        {
           $debit=$amount; 
        }
        else
        {
        $debit=$amount-$discount;
        }
        
    //   echo 'dsfnhdsfh'.$payment_type;
       
       
        if($payment_type != '3'){
             $this->db->insert('user_ledger',$upadte_ledger_array);
             
            if($type=='5'){
                            $ledger_owner_type=0;
            				$listing_id_type=$type;
            				$credit=0;
            				$booking_id=$order_id;
            				$debit=$debit;
            				$user_comments='';
            				$mw_comments='';
            				$vendor_comments='';
            				$order_type=3;
            				$transaction_of=3;
            				$transaction_id=$trans_id;
            				$trans_status=1;
            				$transaction_date=date('Y-m-d H:i:s');
            				$vendor_id = $type;
            				$array_data = array();
                            $res=$this->LedgerModel->create_ledger($user_id, $booking_id, $ledger_owner_type, $listing_id, $listing_id_type, $credit, $debit, $payment_type, $user_comments, $mw_comments,$vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_id,$array_data);
            				
            				$ledger_id=$res['ledger_id'];
            				$ledger_type=$res['type'];
                            $ledger_type1='user';
                        
                        $this->LedgerModel->update_ledger($user_id,$ledger_id,$booking_id,$trans_id,$status,$ledger_type1);
                        
                             $response[] = array(
                        'booking_id' => $booking_id,
                        'ledger_id' => $ledger_id,
                        'ledger_type' => $ledger_type
                        );
            }
           
          //echo 'bahger'; 
            
            
              if($payment_type == '6')
        {
            //echo 'under';
        
            $query = $this->db->query("select * from user_ledger_balance where user_id='$user_id'");
            $row_bal = $query->row();
            $row_count =$query ->num_rows();
            if($row_count>0)
            {
          
                $existing_balance = $row_bal->ledger_balance;
                $existing_lock_balance =$row_bal->lock_amount;
                $total_balance = $existing_balance - $amount; 
              // $total_lock_amount = $existing_lock_balance + $convert_rs;
                $user_ledger_balance = array(
                    'ledger_balance' =>$total_balance
                    );
                     $this->db->where('user_id', $user_id);  
                $this->db->update('user_ledger_balance', $user_ledger_balance); 
            }
            else
            {
              
            }
            
        
            
            
        }
        }
       
        
        if($status == 1){
            
            //12=Nursing, 10=Lab, 8=hospital
            if($type==='12' || $type==='10' || $type==='8'){
                if($opd=="opd")
                {
                    $sql = "UPDATE hospital_booking_master SET status = 5 WHERE order_id = '$order_id'"; 
                }
                else
                {
                $data = array('status' => 'confirmed');
                //$sql = "UPDATE booking_master SET status = 'payment' WHERE booking_id = '$order_id'";  
                 $this->db->where('booking_id', $order_id);  
                $this->db->update('booking_master', $data); 
                }
            }
            else if($type == '5'){
                //$sql = "UPDATE doctor_booking_master SET status = 5 WHERE order_id = '$order_id'";   
                
                $status_change = array(
                    'status' => 5                                                                  
                    );
                $this->db->where('booking_id', $order_id); 
                $this->db->update('doctor_booking_master',$status_change);
               
            }else if($type == '6'){
                $status_change = array(
                    'status' => 5                                                                  
                    );
                $this->db->where('booking_id', $order_id); 
                $this->db->update('booking_master',$status_change);
            }
            else if($type == '36'){
                $status_change = array(
                    'status' => 5                                                                  
                    );
                $this->db->where('booking_id', $order_id); 
                $this->db->update('booking_master',$status_change);
            }
            else if($type == '37'){
                $status_change = array(
                    'status' => 5                                                                  
                    );
                $this->db->where('booking_id', $order_id); 
                $this->db->update('booking_master',$status_change);
                
                $status_change1 = array(
                    'payment_status' => 5                                                                  
                    );
                $this->db->where('booking_id', $order_id); 
                $this->db->update('diet_user_package_history',$status_change1);
            }
            else if($type == '39'){
               
                $status_change = array(
                    'status' => 5                                                                  
                    );
                $this->db->where('booking_id', $order_id); 
                $this->db->update('booking_master',$status_change);
            }
        }
        return array(
            'status' => 201,
            'message' => 'success'
        );
        
    }
    
           
    
     public function insert_payment_status_amb($user_id, $listing_id, $trans_id, $status, $type, $order_id, $order_type,$amount, $status_mesg, $discount, $discount_rupee, $payment_type,$opd) {
        
      
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        
        
        $Expire_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date)) . " + 1 year"));
        if($status == "1"){
            $creadit_debit = 0;
        }else{
            $creadit_debit = 2;
        }    
        
         
        $upadte_ledger_array = array(
            'user_id'       => $user_id,
            'listing_id'    => $listing_id,
            'trans_id'      => $trans_id,
            'trans_type'    => $creadit_debit,
            'order_id'      => $order_id,
            'amount'        => $amount,
            'order_status'  => $status=='1'?'success':'failed',
            'order_type'    => $order_type,
            'status_message'=> $status_mesg,
            'discount'      => $discount,
            'discount_rupee'=> $discount_rupee,
            'trans_time'    => $date,
            'trans_mode'=> $payment_type,
            //'vendor_category'=> $vendor_category
            'vendor_category'=>  $type
        );
        $this->db->insert('user_ledger',$upadte_ledger_array);
        $query = $this->db->query("select * from user_ledger_balance where user_id='$user_id'");
        $row_bal = $query->row();
        $row_count =$query ->num_rows();
         if($row_count>0)
            {
                   
        
        
                
                $existing_balance = $row_bal->ledger_balance;
                $existing_lock_balance =$row_bal->lock_amount;
                $total_balance = $existing_balance - $amount; 
              // $total_lock_amount = $existing_lock_balance + $convert_rs;
                $user_ledger_balance = array(
                    'ledger_balance' =>$total_balance
                    );
                     $this->db->where('user_id', $user_id);  
                $this->db->update('user_ledger_balance', $user_ledger_balance); 
            }
         else
            {
              
            }
        return array(
            'status' => 201,
            'message' => 'success'
        );
        
    }
    
    
    
    // public function insert_payment_status($user_id, $listing_id, $trans_id, $status, $type, $order_id, $order_type,$amount, $status_mesg, $discount, $discount_rupee, $payment_type,$opd) {
        
      
    //     date_default_timezone_set('Asia/Kolkata');
    //     $date = date('Y-m-d H:i:s');
        
    //     $Expire_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date)) . " + 1 year"));
    //     if($status == "1"){
    //         $creadit_debit = 0;
    //     }else{
    //         $creadit_debit = 2;
    //     }    
        
    //      //for health mall 
    //     if($type == '34')
    //      {
             
    //          if($payment_type != '3'){
    //                 $exploded_order_id   = explode(',',$order_id);
    //                 print_r($exploded_order_id);
                    
    //                 for($i = 0; $i < $exploded_order_id; $i++)
    //                 {
    //          $upadte_ledger_array = array(
    //                  'user_id'       => $user_id,
    //                  'listing_id'    => $listing_id,
    //                  'trans_id'      => $trans_id,
    //                  'trans_type'    => $creadit_debit,
    //                  'order_id'      => $exploded_order_id[$i],
    //                  'amount'        => $amount,
    //                  'order_status'  => $status=='1'?'success':'failed',
    //                  'order_type'    => $order_type,
    //                  'status_message'=> $status_mesg,
    //                  'discount'      => $discount,
    //                  'discount_rupee'=> $discount_rupee,
    //                  'trans_time'    => $date,
    //                  'trans_mode'=> $payment_type,
    //                   //'vendor_category'=> $vendor_category
    //                  'vendor_category'=>  $type
    //     );
    //                       $this->db->insert('user_ledger',$upadte_ledger_array);
                          
                          
                          
                          
                          
    //   if($payment_type == '6')
    //     {
    //         //echo 'under';
        
    //         $query = $this->db->query("select * from user_ledger_balance where user_id='$user_id'");
    //         $row_bal = $query->row();
    //         $row_count =$query ->num_rows();
    //         if($row_count>0)
    //         {
    //             $existing_balance = $row_bal->ledger_balance;
    //             $existing_lock_balance =$row_bal->lock_amount;
    //             $total_balance = $existing_balance - $amount; 
    //           // $total_lock_amount = $existing_lock_balance + $convert_rs;
    //             $user_ledger_balance = array(
    //                 'ledger_balance' =>$total_balance
    //                 );
    //                  $this->db->where('user_id', $user_id);  
    //             $this->db->update('user_ledger_balance', $user_ledger_balance); 
    //         }
    //         else
    //         {
              
    //         }
            
    //     }
    //                 }
    //          }
    //       }
    //      else
    //      {
    //     $upadte_ledger_array = array(
    //         'user_id'       => $user_id,
    //         'listing_id'    => $listing_id,
    //         'trans_id'      => $trans_id,
    //         'trans_type'    => $creadit_debit,
    //         'order_id'      => $order_id,
    //         'amount'        => $amount,
    //         'order_status'  => $status=='1'?'success':'failed',
    //         'order_type'    => $order_type,
    //         'status_message'=> $status_mesg,
    //         'discount'      => $discount,
    //         'discount_rupee'=> $discount_rupee,
    //         'trans_time'    => $date,
    //         'trans_mode'=> $payment_type,
    //         //'vendor_category'=> $vendor_category
    //         'vendor_category'=>  $type
    //     );
        
    //      }
        
    // //   echo 'dsfnhdsfh'.$payment_type;
    //   //for health mall 
    //      if($type != '34')
    //      {
       
    //     if($payment_type != '3'){
            
            
            
    //         $this->db->insert('user_ledger',$upadte_ledger_array);
    //       //echo 'bahger'; 
            
            
    //           if($payment_type == '6')
    //     {
    //         //echo 'under';
        
    //         $query = $this->db->query("select * from user_ledger_balance where user_id='$user_id'");
    //         $row_bal = $query->row();
    //         $row_count =$query ->num_rows();
    //         if($row_count>0)
    //         {
                   
        
        
                
    //             $existing_balance = $row_bal->ledger_balance;
    //             $existing_lock_balance =$row_bal->lock_amount;
    //             $total_balance = $existing_balance - $amount; 
    //           // $total_lock_amount = $existing_lock_balance + $convert_rs;
    //             $user_ledger_balance = array(
    //                 'ledger_balance' =>$total_balance
    //                 );
    //                  $this->db->where('user_id', $user_id);  
    //             $this->db->update('user_ledger_balance', $user_ledger_balance); 
    //         }
    //         else
    //         {
              
    //         }
            
        
            
            
    //     }
    //     }
    //      }
        
    //     if($status == 1){
    //         //12=Nursing, 10=Lab, 8=hospital
    //         if($type==='12' || $type==='10' || $type==='8'){
    //             if($opd=="opd")
    //             {
    //                 $sql = "UPDATE hospital_booking_master SET status = 5 WHERE order_id = '$order_id'"; 
    //             }
    //             else
    //             {
    //             $data = array('status' => 'confirmed');
    //             //$sql = "UPDATE booking_master SET status = 'payment' WHERE booking_id = '$order_id'";  
    //              $this->db->where('booking_id', $order_id);  
    //             $this->db->update('booking_master', $data); 
    //             }
    //         }
    //         elseif($type==='5'){
    //             //$sql = "UPDATE doctor_booking_master SET status = 5 WHERE order_id = '$order_id'";   
                
    //             $status_change = array(
    //                 'status' => 5                                                                  
    //                 );
    //             $this->db->where('booking_id', $order_id); 
    //             $this->db->update('doctor_booking_master',$status_change);
               
    //         }elseif($type==='6' ){
    //             $status_change = array(
    //                 'status' => 5                                                                  
    //                 );
    //             $this->db->where('booking_id', $order_id); 
    //             $this->db->update('booking_master',$status_change);
    //         }
    //         elseif($type==='37' ){
    //             $status_change = array(
    //                 'status' => 5                                                                  
    //                 );
    //             $this->db->where('booking_id', $order_id); 
    //             $this->db->update('booking_master',$status_change);
                
    //             $status_change1 = array(
    //                 'payment_status' => 5                                                                  
    //                 );
    //             $this->db->where('booking_id', $order_id); 
    //             $this->db->update('diet_user_package_history',$status_change1);
    //         }
    //     }
    //     return array(
    //         'status' => 201,
    //         'message' => 'success'
    //     );
        
    // }
    
    
    
    public function get_payment_status($order_id,$mode){
        
        
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        $Expire_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date)) . " + 1 year"));
        
        $query = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and trans_type='0'");
        $row = $query->row();
        
        
        $trans_id = mt_rand(1000000000000, 9999999999999);
        
        if(isset($row)){
    
        //------UPDATE LEDGER APPROVED--------
        
        $data = array(  
            'authenticate' => '1'
        );  
        
        $this->db->where('order_id', $order_id); 
        $this->db->where('trans_type', '0'); 
        $this->db->update('user_ledger', $data); 
        
        //------UPDATE LEDGER APPROVED END----
            
            
            
        //-------LEDGER ENTRY------
        
        $upadte_ledger_array = array(
            'user_id'       => $row->user_id,
            'listing_id'    => $row->listing_id,
            'trans_id'      => $trans_id,
            'trans_type'    => '1',
            'order_id'      => $row->order_id,
            'amount'        => $row->amount,
            'order_status'  => 'success',
            'order_type'    => $row->order_type,
            'status_message'=> $row->status_message,
            'discount'      => $row->discount,
            'discount_rupee'=> $row->discount_rupee,
            'trans_time'    => $date,
            'trans_mode'=> $row->trans_mode,
            //'vendor_category'=> $vendor_category
            'vendor_category'=>  $row->vendor_category,
            'authenticate' => '1'
        );
         $this->db->insert('user_ledger',$upadte_ledger_array);
        //-------LEDGER ENTRY END------
        
        
        //-------LEDGER POINT ENTRY-------
         $upadte_ledger_array_point = array(
            'user_id'       => $row->user_id,
            'listing_id'    => $row->listing_id,
            'trans_id'      => $trans_id,
            'trans_type'    => 4,
            'order_id'      => $row->order_id,
            'amount'        => $row->amount,
            'order_status'  => 'success',
            'order_type'    => $row->order_type,
            'status_message'=> $row->status_message,
            'discount'      => $row->discount,
            'discount_rupee'=> $row->discount_rupee,
            'trans_time'    => $date,
            'trans_mode'=> $row->trans_mode,
             //   'vendor_category'=> $vendor_category
             'vendor_category'=>  $row->vendor_category,
             'authenticate' => '1'
        );
        
        $this->db->insert('user_ledger',$upadte_ledger_array_point);
        //-------LEDGER POINT ENTRY END---
        
        //--------POINT ENTRY-----
        $upadte_user_points = array(
            'user_id'        => $row->user_id,
            'order_id'       => $row->order_id,
            'trans_id'       => $trans_id,
            'points'         => $row->amount,
            'created_at'     => $date,
            'expire_at'      => $Expire_date,    
            'status'         => 'active'
        );
        $this->db->insert('user_points', $upadte_user_points);
        //--------POINT ENTRY END-----    
        
        //--------LEDGER and LOCK BAL MINUS---------
        if($row->trans_mode != '3'){
        
            $query = $this->db->query("select * from user_ledger_balance where user_id='$row->user_id'");
            $row_bal = $query->row();
            $row_count =$query ->num_rows();
            if($row_count>0)
            {
                  
                //update ledger balance 
                    $pnts_rate = $this->db->select('rate')->where('id', 1)->get('points_rate')->row();
            if (!empty($pnts_rate)) {
                $rate = $pnts_rate->rate;
            } else {
                $rate = "";
            }
            
            //$convert_rs = round($tot_points/$rate);
            $amt = $row->amount; 
            $convert_rs = $amt/$rate;
        // converted rupees for not to direct insert amount
        
        
                
                $existing_balance = $row_bal->ledger_balance;
                $existing_lock_balance =$row_bal->lock_amount;
                $total_balance = $existing_balance + $convert_rs; 
                $total_lock_amount = $existing_lock_balance + $convert_rs;
                $user_ledger_balance = array(
                    'ledger_balance' =>$total_balance,
                    'lock_amount' => $total_lock_amount
                    );
                     $this->db->where('user_id', $row->user_id);  
                $this->db->update('user_ledger_balance', $user_ledger_balance); 
            }
            else
            {
              // insert into ledger balance 
                //update ledger balance 
                    $pnts_rate = $this->db->select('rate')->where('id', 1)->get('points_rate')->row();
            if (!empty($pnts_rate)) {
                $rate = $pnts_rate->rate;
            } else {
                $rate = "";
            }
            
            //$convert_rs = round($tot_points/$rate);
            $amt = $row->amount; 
            $convert_rs = $amt/$rate;
        // converted rupees for not to direct insert amount
              
               $user_ledger_balance = array(
                    'user_id' => $row->user_id,
                    'ledger_balance' =>$convert_rs,
                    'lock_amount' => $convert_rs                                                                  
                    );
              $this->db->insert('user_ledger_balance',$user_ledger_balance);
            }
            
        }
        //--------LEDGER and LOCK BAL MINUS END-----
        
        
         return array(
                'status' => 201,
                'message' => 'success',
         );
        
        }else{
          
           return array(
                'status' => 201,
                'message' => 'somthing went wrong',
         );  
        }
    }
    
    //added by zak for add payment status for medicalwale admin
    
      public function add_both_ledger_for_admin($id,$mode){
        
        //echo "SELECT * FROM `payment_status_master` WHERE id= '$id'"; 
        $query = $this->db->query("SELECT * FROM `payment_status_master` WHERE id= '$id'");
        $raw = $query->row();
       
        
        if($mode == 'add'){
            
            //User Ledger
            $upadte_user_ledger_array = array(
                'order_id'       => $raw->order_id,
                'trans_id'       => $raw->trans_id,
                'trans_type'     => 1,
                'amount'         => $raw->amount
            );
            
            
            //Vendor Ledger
            $upadte_vendor_ledger_array = array(
                'order_id'       => $raw->order_id,
                'trans_id'       => $raw->trans_id,
                'trans_type'     => 0,
                'amount'         => $raw->amount
            );
            $this->db->insert('user_ledger', $upadte_user_ledger_array);
            $this->db->insert('vendor_ledger', $upadte_vendor_ledger_array);
        }else{
            
            //User Ledger
            $upadte_user_ledger_array = array(
                'order_id'       => $raw->order_id,
                'trans_id'       => $raw->trans_id,
                'trans_type'     => 0,
                'amount'         => $raw->amount
            );
            $this->db->insert('user_ledger', $upadte_user_ledger_array);
            
            //Vendor Ledger
            $upadte_vendor_ledger_array = array(
                'order_id'       => $raw->order_id,
                'trans_id'       => $raw->trans_id,
                'trans_type'     => 1,
                'amount'         => $raw->amount
            );
            $this->db->insert('vendor_ledger', $upadte_vendor_ledger_array);
        }
        
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
    public function get_user_ledger_details($user_id){
        $debit_list =array();
        $point_list =array();
        $credit_list =array();
        $failure_list = array();
        $bachat_list = array();
        $credit_list_trans = array();
        //echo "SELECT * FROM user_ledger_balance WHERE user_id='$user_id'";
        
          
        $query = $this->db->query("SELECT * FROM user_ledger_balance WHERE user_id='$user_id'");

        $row = $query->row();
  
        $num_row = $query->num_rows();
     
        if ($num_row>0)
        {
                $ledger_balance =  $row->ledger_balance;
                $lock_amount =  $row->lock_amount;
        }else{
                $ledger_balance = 0;
                $lock_amount =  0;
        }
       
        
        $pnts_rate = $this->db->select('rate')->where('id', 1)->get('points_rate')->row();
        if (!empty($pnts_rate)) {
            $rate = $pnts_rate->rate;
        } else {
            $rate = "";
        }    
        
        $query_point = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' AND (trans_type='4' or trans_type='5') GROUP BY order_id order by id DESC");

        $count_point = $query_point->num_rows();
        // print_r($count_point); die();
        //$ttl_pointds = array();
        if ($count_point > 0) {

            foreach ($query_point->result_array() as $row) {
               $ttl_pointds = array();
                $listing_id = $row['listing_id'];
                
                //added for vendor details 
                $listing_id = $row['listing_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end
            
                
                $order_id      = $row['order_id'];
                $trans_type      = $row['trans_type'];
                 
                //echo "SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC";
                // $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC");
                $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') and user_id='$user_id' order by id DESC");
                $count_query_point_trans = $query_point_trans->num_rows();
                
                $total_amount = $total_amount_saved = 0;
                //CHECK INVOICE IS ADDED OR NOT
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                if ($count_query_point_trans > 0) {
                    $points_list_trans = array();
                    foreach ($query_point_trans->result_array() as $row_nest) {
                            
                            if($row_nest['trans_type']  == '5'){   
                                $trans_id       = $row_nest['trans_id'];
                                $trans_type     = $row_nest['trans_type'];
                                $trans_mode     = $row_nest['trans_mode'];
                                $amount        = $row_nest['amount'];
                                $amount_saved        = $row_nest['amount_saved'];
                                $trans_time     = $row_nest['trans_time'];
                                
                                
                                $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                            
                                array_push($ttl_pointds,$row_nest['amount']);
                                $points_list_trans[] = array(
                                    'order_id' => $order_id,
                                    'trans_id' => $trans_id,
                                    'amount' => $amount-$amount_saved,
                                    'trans_mode' => $trans_mode,
                                    'trans_time'=>$trans_time
                                ); 
                            }else if($row_nest['trans_type']  == '4'){
                                $trans_id       = $row_nest['trans_id'];
                                $trans_type     = $row_nest['trans_type'];
                                $trans_mode     = $row_nest['trans_mode'];
                                $amount        = $row_nest['amount'];
                                $amount_saved        = $row_nest['amount_saved'];
                                $trans_time     = $row_nest['trans_time'];
                                $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                                
                                
                                $total_amount = $total_amount + $amount;
                                $total_amount_saved = $total_amount_saved + $amount_saved;


                                array_push($ttl_pointds,$row_nest['amount']);
                                $points_list_trans[] = array(
                                    'order_id' => $order_id,
                                    'trans_id' => $trans_id,
                                    'amount' => $amount-$amount_saved,
                                    'trans_mode' => $trans_mode,
                                    'trans_time'=>$trans_time,
                                    'invoice' =>$invoice
                                );
                            }
                        
                    }
                      // print_r($ttl_pointds); 
                    
                }
                if($doctor_name == ""){
                    $doctor_name = 'Medicalwale';
                }
                if($row_nest['trans_type']  == '5'){        
                     $convert_rs = array_sum($ttl_pointds);
                     $point_list[] = array(
                         'vendor_image' => $vendor_image,
                         'vendor_name' =>$doctor_name,
                        'order_id' => $order_id,
                        'trans_type'=>$trans_type,
                        //'total_points'=>$amount-$amount_saved,
                        'total_points'=>$convert_rs*$rate,
                        'point_rupee'=>array_sum($ttl_pointds),
                        'trans_details'=>$points_list_trans,
                         'invoice' =>$invoice
                    ); 
                }else if($row_nest['trans_type']  == '4'){
                    $convert_rs = round(array_sum($ttl_pointds)/$rate,2);
                     $point_list[] = array(
                         'vendor_image' => $vendor_image,
                         'vendor_name' =>$doctor_name,
                        'order_id' => $order_id,
                        'trans_type'=>$trans_type,
                        'total_points'=>$total_amount-$total_amount_saved,
                        'point_rupee'=>$convert_rs,
                        'trans_details'=>$points_list_trans,
                         'invoice' =>$invoice
                        
                    ); 
                }
               
            }
           
           
        }      
        
        
        $query_credit = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' and (trans_type='0' or trans_type='1' or trans_type='2') GROUP BY order_id order by id DESC");
        
        $count_credit = $query_credit->num_rows();
        if ($count_credit > 0) {

            foreach ($query_credit->result_array() as $row) {
                
                $order_id      = $row['order_id'];
                $listing_id = $row['listing_id'];
                
               //added for vendor details 
                $listing_id = $row['listing_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end

                $query_debit_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='0' or trans_type='1' or trans_type = '2') order by id DESC");
                $count_query_debit_trans = $query_debit_trans->num_rows();
                
                //CHECK INVOICE IS ADDED OR NOT
                //echo "SELECT * FROM tbl_invoice WHERE order_id='$order_id'";
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                if ($count_query_debit_trans > 0) {
                    $credit_list_trans = array();
                    foreach ($query_debit_trans->result_array() as $row_nest) {
                        
                            $trans_id       = $row_nest['trans_id'];
                            $trans_type     = $row_nest['trans_type'];
                            $trans_mode     = $row_nest['trans_mode'];
                             $amount         = $row_nest['amount'];
                            $amount_saved        = $row_nest['amount_saved'];
                            
                          
                           
                            $trans_time     = $row_nest['trans_time'];
                            $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                            $credit_list_trans[] = array(
                                'order_id' => $order_id,
                                'trans_id' => $trans_id,
                                'trans_type' => $trans_type,
                                'trans_mode' => $trans_mode,
                                'amount' => $amount-$amount_saved,
                                'point_earned' => $amount-$amount_saved,
                                'trans_time'=>$trans_time
                            ); 
                         
                    }
                        
                    
                }
                 
                $credit_list[] = array(
                    'vendor_image' => $vendor_image,
                     'vendor_name' =>$doctor_name,
                    'order_id' => $order_id,
                    'trans_details'=>$credit_list_trans,
                    'invoice' =>$invoice
                ); 
                
                
            }
        }  
        
        
        
       
        $query_failure = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' AND trans_type = '2' order by id DESC");
        $count_failure = $query_failure->num_rows();
        if ($count_failure > 0) {

            foreach ($query_failure->result_array() as $row) {
                //added for vendor details 
                $listing_id = $row['listing_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end
                $order_id      = $row['order_id'];
                $trans_id      = $row['trans_id'];
                $trans_type    = $row['trans_type'];
                $amount        = $row['amount'];
                
                //CHECK INVOICE IS ADDED OR NOT
                
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                
                
                
                $failure_list[] = array(
                    'vendor_image' => $vendor_image,
                    'vendor_name' =>$doctor_name,
                    'order_id' => $order_id,
                    'trans_id' => $trans_id,
                    'trans_type' => $trans_type,
                    'amount' => $amount,
                     'invoice' =>$invoice
                );
            }
           
           
        }
     
     
     //added by zak for to show  invoice list in ledger details 
     
     $query_invoice_details = $this->db->query("SELECT * FROM tbl_invoice WHERE user_id='$user_id' ");
     $count_invoice_details = $query_invoice_details->num_rows();
     if($count_invoice_details > 0){
          foreach ($query_invoice_details->result_array() as $row) {
              $id =$row['id'];
              $order_id = $row['order_id'];
              $invoice_no = $row['invoice_no'];
              $comment =$row['comment'];
              $invoice_date = $row['invoice_date'];
              $query_attachment = $this->db->query("select * from tbl_invoice_attachment WHERE invoice_id='$id'");
              
              $attachment_file = array();
              foreach ($query_attachment->result_array() as $row) {
                  $file_name = $row['file_name'];
              
              $attachment_file[] = array(
                        'https://medicalwale.s3.amazonaws.com/images/invoice_images/'.$file_name
                  );
              }
              $invoice_details[] = array(
                   'order_id' => $order_id,
                   'invoice_no' => $invoice_no,
                   'comment' => $comment,
                   'invoice_date' => $invoice_date,
                   'attachment' =>$attachment_file
                  );
          }
     }
     else
     {
         $invoice_details = array();
     }
        
        $query_bachat = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' AND (trans_type='1') AND amount_saved!='0' GROUP BY order_id order by id DESC");

        $count_bachat = $query_bachat->num_rows();
        // print_r($count_point); die();
        //$ttl_pointds = array();
        if ($count_bachat > 0) {

            foreach ($query_bachat->result_array() as $row) {
                $ttl_bachat = array();
                $order_id      = $row['order_id'];
                $trans_type      = $row['trans_type'];
                 $discount = "";
                 
                 
                 $listing_id = $row['listing_id'];
                
               //added for vendor details 
                $listing_id = $row['listing_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end
                // $query_bachat_trans = $this->db->query();
                
                //CHECK INVOICE IS ADDED OR NOT
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                //echo "SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC";
                // $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC");
                $query_bachat_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='1') order by id DESC");
                
                $count_query_bachat_trans = $query_bachat_trans->num_rows();
                 //print_r($query_bachat_trans->result_array());
                if ($count_query_bachat_trans > 0) {
                    $bachat_list_trans = array();
                    foreach ($query_bachat_trans->result_array() as $row_nest) {
                            
                        if(!empty($row_nest['amount_saved'])){
                            $trans_id       = $row_nest['trans_id'];
                            $trans_type     = $row_nest['trans_type'];
                            $trans_mode     = $row_nest['trans_mode'];
                            $amount         = $row_nest['amount'];
                            $amount_saved   = $row_nest['amount_saved'];
                            $discount       = $row_nest['discount'];
                            $trans_time     = $row_nest['trans_time'];
                               $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                                
                            array_push($ttl_bachat,$row_nest['amount']);
                            $bachat_list_trans[] = array(
                                'order_id' => $order_id,
                                'trans_id' => $trans_id,
                                'trans_mode' => $trans_mode,
                                'trans_time'=>$trans_time,
                                'invoice' =>$invoice
                            ); 
                        }
                        
                    }
                    // print_r($ttl_pointds); 
                    
                }
                
                $convert_rs = round(array_sum($ttl_bachat)/$rate);
                 $bachat_list[] = array(
                     'vendor_image' => $vendor_image,
                     'vendor_name' =>$doctor_name,
                    'order_id' => $order_id,
                    'trans_type'=>$trans_type,
                    // 'total_points'=>array_sum($ttl_bachat),
                    // 'point_rupee'=>$convert_rs,
                    'amount' => $amount,
                    'amount_saved' => $amount_saved,
                    'discount' => $discount,
                    'trans_details'=>$bachat_list_trans,
                    'invoice' =>$invoice
                ); 
               
            }
           
           
        }
       // echo "SELECT SUM(points) as total_points FROM user_points WHERE user_id='$user_id' and status='active' order by id DESC";
                $query = $this->db->query("SELECT SUM(points) as total_points FROM user_points WHERE user_id='$user_id' and status='active' order by id DESC");

                $rows = $query->row();
                
                if (isset($rows))
                {
                        $total_points =  $rows->total_points;
                }else{
                        $total_points = 0;
                }
     
        $ledger_details = array();
        $ledger_details['balance_sheet'] =  $credit_list;
        $ledger_details['points']   =  $point_list;
        $ledger_details['failure'] =  $failure_list;
        $ledger_details['bachat_card'] =  $bachat_list;
      //  if($ledger_balance == $lock_amount)
        $ledger_details['balance'] = $ledger_balance;
        $ledger_details['total_points']   =  $total_points;
        $ledger_details['invoice_details'] = $invoice_details;
        return $ledger_details;
        
    }
    
    
    
     public function get_vendor_ledger_detail($user_id){
        $debit_list =array();
        $point_list =array();
        $credit_list =array();
        $failure_list = array();
        $bachat_list = array();
        $credit_list_trans = array();
        //echo "SELECT * FROM user_ledger_balance WHERE user_id='$user_id'";
        
          
        $query = $this->db->query("SELECT * FROM user_ledger_balance WHERE user_id='$user_id'");

        $row = $query->row();
  
        $num_row = $query->num_rows();
     
        if ($num_row>0)
        {
                $ledger_balance =  $row->ledger_balance;
                $lock_amount =  $row->lock_amount;
        }else{
                $ledger_balance = 0;
                $lock_amount =  0;
        }
       
        
        $pnts_rate = $this->db->select('rate')->where('id', 1)->get('points_rate')->row();
        if (!empty($pnts_rate)) {
            $rate = $pnts_rate->rate;
        } else {
            $rate = "";
        }    
        
        $query_point = $this->db->query("SELECT * FROM user_ledger WHERE listing_id='$user_id' AND (trans_type='4' or trans_type='5') GROUP BY order_id order by id DESC");

        $count_point = $query_point->num_rows();
        // print_r($count_point); die();
        //$ttl_pointds = array();
        if ($count_point > 0) {

            foreach ($query_point->result_array() as $row) {
               $ttl_pointds = array();
                $listing_id = $row['user_id'];
                
                //added for vendor details 
                $listing_id = $row['user_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end
            
                
                $order_id      = $row['order_id'];
                $trans_type      = $row['trans_type'];
                 
                //echo "SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC";
                // $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC");
                $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') and listing_id='$user_id' order by id DESC");
                $count_query_point_trans = $query_point_trans->num_rows();
                
                
                //CHECK INVOICE IS ADDED OR NOT
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                if ($count_query_point_trans > 0) {
                    $points_list_trans = array();
                    foreach ($query_point_trans->result_array() as $row_nest) {
                            
                            if($row_nest['trans_type']  == '5'){   
                                $trans_id       = $row_nest['trans_id'];
                                $trans_type     = $row_nest['trans_type'];
                                $trans_mode     = $row_nest['trans_mode'];
                                $amount        = $row_nest['amount'];
                                $amount_saved        = $row_nest['amount_saved'];
                                $trans_time     = $row_nest['trans_time'];
                                
                                
                                $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                            
                                array_push($ttl_pointds,$row_nest['amount']);
                                $points_list_trans[] = array(
                                    'order_id' => $order_id,
                                    'trans_id' => $trans_id,
                                    'amount' => $amount-$amount_saved,
                                    'trans_mode' => $trans_mode,
                                    'trans_time'=>$trans_time
                                ); 
                            }else if($row_nest['trans_type']  == '4'){
                                $trans_id       = $row_nest['trans_id'];
                                $trans_type     = $row_nest['trans_type'];
                                $trans_mode     = $row_nest['trans_mode'];
                                $amount        = $row_nest['amount'];
                                $amount_saved        = $row_nest['amount_saved'];
                                $trans_time     = $row_nest['trans_time'];
                                $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                                
                                array_push($ttl_pointds,$row_nest['amount']);
                                $points_list_trans[] = array(
                                    'order_id' => $order_id,
                                    'trans_id' => $trans_id,
                                    'amount' => $amount-$amount_saved,
                                    'trans_mode' => $trans_mode,
                                    'trans_time'=>$trans_time,
                                    'invoice' =>$invoice
                                );
                            }
                        
                    }
                      // print_r($ttl_pointds); 
                    
                }
                if($row_nest['trans_type']  == '5'){        
                     $convert_rs = array_sum($ttl_pointds);
                     $point_list[] = array(
                         'vendor_image' => $vendor_image,
                         'vendor_name' =>$doctor_name,
                        'order_id' => $order_id,
                        'trans_type'=>$trans_type,
                        //'total_points'=>$amount-$amount_saved,
                        'total_points'=>$convert_rs*$rate,
                        'point_rupee'=>array_sum($ttl_pointds),
                        'trans_details'=>$points_list_trans,
                         'invoice' =>$invoice
                    ); 
                }else if($row_nest['trans_type']  == '4'){
                    $convert_rs = round(array_sum($ttl_pointds)/$rate,2);
                     $point_list[] = array(
                         'vendor_image' => $vendor_image,
                         'vendor_name' =>$doctor_name,
                        'order_id' => $order_id,
                        'trans_type'=>$trans_type,
                        'total_points'=>$amount-$amount_saved,
                        'point_rupee'=>$convert_rs,
                        'trans_details'=>$points_list_trans,
                         'invoice' =>$invoice
                        
                    ); 
                }
               
            }
           
           
        }      
        
        
        $query_credit = $this->db->query("SELECT * FROM user_ledger WHERE listing_id='$user_id' and (trans_type='0' or trans_type='1' or trans_type='2') GROUP BY order_id order by id DESC");
        
        $count_credit = $query_credit->num_rows();
        if ($count_credit > 0) {

            foreach ($query_credit->result_array() as $row) {
                
                $order_id      = $row['order_id'];
                $listing_id = $row['user_id'];
                
               //added for vendor details 
                $listing_id = $row['user_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end

                $query_debit_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='0' or trans_type='1' or trans_type = '2') order by id DESC");
                $count_query_debit_trans = $query_debit_trans->num_rows();
                
                //CHECK INVOICE IS ADDED OR NOT
                //echo "SELECT * FROM tbl_invoice WHERE order_id='$order_id'";
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                if ($count_query_debit_trans > 0) {
                    $credit_list_trans = array();
                    foreach ($query_debit_trans->result_array() as $row_nest) {
                        
                            $trans_id       = $row_nest['trans_id'];
                            $trans_type     = $row_nest['trans_type'];
                            $trans_mode     = $row_nest['trans_mode'];
                             $amount         = $row_nest['amount'];
                            $amount_saved        = $row_nest['amount_saved'];
                            
                          
                           
                            $trans_time     = $row_nest['trans_time'];
                            $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                            $credit_list_trans[] = array(
                                'order_id' => $order_id,
                                'trans_id' => $trans_id,
                                'trans_type' => $trans_type,
                                'trans_mode' => $trans_mode,
                                'amount' => $amount-$amount_saved,
                                'point_earned' => $amount-$amount_saved,
                                'trans_time'=>$trans_time
                            ); 
                         
                    }
                        
                    
                }
                 
                $credit_list[] = array(
                    'user_image' => $vendor_image,
                     'user_name' =>$doctor_name,
                    'order_id' => $order_id,
                    'trans_details'=>$credit_list_trans,
                    'invoice' =>$invoice
                ); 
                
                
            }
        }  
        
        
        
       
        $query_failure = $this->db->query("SELECT * FROM user_ledger WHERE listing_id='$user_id' AND trans_type = '2' order by id DESC");
        $count_failure = $query_failure->num_rows();
        if ($count_failure > 0) {

            foreach ($query_failure->result_array() as $row) {
                //added for vendor details 
                $listing_id = $row['user_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end
                $order_id      = $row['order_id'];
                $trans_id      = $row['trans_id'];
                $trans_type    = $row['trans_type'];
                $amount        = $row['amount'];
                
                //CHECK INVOICE IS ADDED OR NOT
                
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                
                
                
                $failure_list[] = array(
                    'user_image' => $vendor_image,
                    'user_name' =>$doctor_name,
                    'order_id' => $order_id,
                    'trans_id' => $trans_id,
                    'trans_type' => $trans_type,
                    'amount' => $amount,
                     'invoice' =>$invoice
                );
            }
           
           
        }
     
     
     //added by zak for to show  invoice list in ledger details 
     
     $query_invoice_details = $this->db->query("SELECT * FROM tbl_invoice WHERE user_id='$user_id' ");
     $count_invoice_details = $query_invoice_details->num_rows();
     if($count_invoice_details > 0){
          foreach ($query_invoice_details->result_array() as $row) {
              $id =$row['id'];
              $order_id = $row['order_id'];
              $invoice_no = $row['invoice_no'];
              $comment =$row['comment'];
              $invoice_date = $row['invoice_date'];
              $query_attachment = $this->db->query("select * from tbl_invoice_attachment WHERE invoice_id='$id'");
              
              $attachment_file = array();
              foreach ($query_attachment->result_array() as $row) {
                  $file_name = $row['file_name'];
              
              $attachment_file[] = array(
                        'https://medicalwale.s3.amazonaws.com/images/invoice_images/'.$file_name
                  );
              }
              $invoice_details[] = array(
                   'order_id' => $order_id,
                   'invoice_no' => $invoice_no,
                   'comment' => $comment,
                   'invoice_date' => $invoice_date,
                   'attachment' =>$attachment_file
                  );
          }
     }
     else
     {
         $invoice_details = array();
     }
        
        $query_bachat = $this->db->query("SELECT * FROM user_ledger WHERE listing_id='$user_id' AND (trans_type='1') AND amount_saved!='0' GROUP BY order_id order by id DESC");

        $count_bachat = $query_bachat->num_rows();
        // print_r($count_point); die();
        //$ttl_pointds = array();
        if ($count_bachat > 0) {

            foreach ($query_bachat->result_array() as $row) {
                $ttl_bachat = array();
                $order_id      = $row['order_id'];
                $trans_type      = $row['trans_type'];
                 $discount = "";
                 
                 
                 $listing_id = $row['user_id'];
                
               //added for vendor details 
                $listing_id = $row['user_id'];
                
                $doctor_details = $this->db->query("SELECT * FROM users WHERE id='$listing_id'");
                $details = $doctor_details->row();
                $doctor_count = $doctor_details->num_rows();
                if($doctor_count>0)
                {
                    $doctor_name = $details->name;
                }
                else
                {
                    $doctor_name = "";
                }
                
                 $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $vendor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                //end
                // $query_bachat_trans = $this->db->query();
                
                //CHECK INVOICE IS ADDED OR NOT
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                //echo "SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC";
                // $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC");
                $query_bachat_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='1') order by id DESC");
                
                $count_query_bachat_trans = $query_bachat_trans->num_rows();
                 //print_r($query_bachat_trans->result_array());
                if ($count_query_bachat_trans > 0) {
                    $bachat_list_trans = array();
                    foreach ($query_bachat_trans->result_array() as $row_nest) {
                            
                        if(!empty($row_nest['amount_saved'])){
                            $trans_id       = $row_nest['trans_id'];
                            $trans_type     = $row_nest['trans_type'];
                            $trans_mode     = $row_nest['trans_mode'];
                            $amount         = $row_nest['amount'];
                            $amount_saved   = $row_nest['amount_saved'];
                            $discount       = $row_nest['discount'];
                            $trans_time     = $row_nest['trans_time'];
                               $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                                
                            array_push($ttl_bachat,$row_nest['amount']);
                            $bachat_list_trans[] = array(
                                'order_id' => $order_id,
                                'trans_id' => $trans_id,
                                'trans_mode' => $trans_mode,
                                'trans_time'=>$trans_time,
                                'invoice' =>$invoice
                            ); 
                        }
                        
                    }
                    // print_r($ttl_pointds); 
                    
                }
                
                $convert_rs = round(array_sum($ttl_bachat)/$rate);
                 $bachat_list[] = array(
                     'user_image' => $vendor_image,
                     'user_name' =>$doctor_name,
                    'order_id' => $order_id,
                    'trans_type'=>$trans_type,
                    // 'total_points'=>array_sum($ttl_bachat),
                    // 'point_rupee'=>$convert_rs,
                    'amount' => $amount,
                    'amount_saved' => $amount_saved,
                    'discount' => $discount,
                    'trans_details'=>$bachat_list_trans,
                    'invoice' =>$invoice
                ); 
               
            }
           
           
        }
       // echo "SELECT SUM(points) as total_points FROM user_points WHERE user_id='$user_id' and status='active' order by id DESC";
                $query = $this->db->query("SELECT SUM(points) as total_points FROM user_points WHERE user_id='$user_id' and status='active' order by id DESC");

                $rows = $query->row();
                
                if (isset($rows))
                {
                        $total_points =  $rows->total_points;
                }else{
                        $total_points = 0;
                }
     
        $ledger_details = array();
        $ledger_details['balance_sheet'] =  $credit_list;
        $ledger_details['points']   =  $point_list;
        $ledger_details['failure'] =  $failure_list;
        $ledger_details['bachat_card'] =  $bachat_list;
      //  if($ledger_balance == $lock_amount)
        $ledger_details['balance'] = $ledger_balance;
        $ledger_details['total_points']   =  $total_points;
        $ledger_details['invoice_details'] = $invoice_details;
        return $ledger_details;
        
    }
    
    ///ADDED FOR PAGINATION BY ZAK 
      public function get_user_ledger_details_pagination($user_id,$page){
        $debit_list =array();
        $point_list =array();
        $credit_list =array();
        $failure_list = array();
        $bachat_list = array();
        $credit_list_trans = array();
        //echo "SELECT * FROM user_ledger_balance WHERE user_id='$user_id'";
        //ADDED BY ZAK FOR PAGINATION 
         $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
          
        $query = $this->db->query("SELECT * FROM user_ledger_balance WHERE user_id='$user_id' order by id DESC limit $start, $limit");

        $row = $query->row();
  
        $num_row = $query->num_rows();
     
        if ($num_row>0)
        {
                $ledger_balance =  $row->ledger_balance;
                $lock_amount =  $row->lock_amount;
        }else{
                $ledger_balance = 0;
                $lock_amount =  0;
        }
       
        
        $pnts_rate = $this->db->select('rate')->where('id', 1)->get('points_rate')->row();
        if (!empty($pnts_rate)) {
            $rate = $pnts_rate->rate;
        } else {
            $rate = "";
        }    
        
        $query_point = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' AND (trans_type='4' or trans_type='5') GROUP BY order_id order by id DESC limit $start, $limit");

        $count_point = $query_point->num_rows();
        // print_r($count_point); die();
        //$ttl_pointds = array();
        if ($count_point > 0) {

            foreach ($query_point->result_array() as $row) {
               $ttl_pointds = array();
                $order_id      = $row['order_id'];
                $trans_type      = $row['trans_type'];
                 
                //echo "SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC";
                // $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC");
                $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') and user_id='$user_id' order by id DESC");
                $count_query_point_trans = $query_point_trans->num_rows();
                
                
                //CHECK INVOICE IS ADDED OR NOT
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                if ($count_query_point_trans > 0) {
                    $points_list_trans = array();
                    foreach ($query_point_trans->result_array() as $row_nest) {
                            
                            if($row_nest['trans_type']  == '5'){   
                                $trans_id       = $row_nest['trans_id'];
                                $trans_type     = $row_nest['trans_type'];
                                $trans_mode     = $row_nest['trans_mode'];
                                $amount        = $row_nest['amount'];
                                $amount_saved        = $row_nest['amount_saved'];
                                $trans_time     = $row_nest['trans_time'];
                                
                                
                                $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                            
                                array_push($ttl_pointds,$row_nest['amount']);
                                $points_list_trans[] = array(
                                    'order_id' => $order_id,
                                    'trans_id' => $trans_id,
                                    'amount' => $amount-$amount_saved,
                                    'trans_mode' => $trans_mode,
                                    'trans_time'=>$trans_time
                                ); 
                            }else if($row_nest['trans_type']  == '4'){
                                $trans_id       = $row_nest['trans_id'];
                                $trans_type     = $row_nest['trans_type'];
                                $trans_mode     = $row_nest['trans_mode'];
                                $amount        = $row_nest['amount'];
                                $amount_saved        = $row_nest['amount_saved'];
                                $trans_time     = $row_nest['trans_time'];
                                $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                                
                                array_push($ttl_pointds,$row_nest['amount']);
                                $points_list_trans[] = array(
                                    'order_id' => $order_id,
                                    'trans_id' => $trans_id,
                                    'amount' => $amount-$amount_saved,
                                    'trans_mode' => $trans_mode,
                                    'trans_time'=>$trans_time,
                                    'invoice' =>$invoice
                                );
                            }
                        
                    }
                      // print_r($ttl_pointds); 
                    
                }
                if($row_nest['trans_type']  == '5'){        
                     $convert_rs = array_sum($ttl_pointds);
                     $point_list[] = array(
                        'order_id' => $order_id,
                        'trans_type'=>$trans_type,
                        //'total_points'=>$amount-$amount_saved,
                        'total_points'=>$convert_rs*$rate,
                        'point_rupee'=>array_sum($ttl_pointds),
                        'trans_details'=>$points_list_trans,
                         'invoice' =>$invoice
                    ); 
                }else if($row_nest['trans_type']  == '4'){
                    $convert_rs = round(array_sum($ttl_pointds)/$rate,2);
                     $point_list[] = array(
                        'order_id' => $order_id,
                        'trans_type'=>$trans_type,
                        'total_points'=>$amount-$amount_saved,
                        'point_rupee'=>$convert_rs,
                        'trans_details'=>$points_list_trans,
                         'invoice' =>$invoice
                        
                    ); 
                }
               
            }
           
           
        }      
        
        
        $query_credit = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' and (trans_type='0' or trans_type='1' or trans_type='2') GROUP BY order_id order by id DESC limit $start, $limit");
        
        $count_credit = $query_credit->num_rows();
        if ($count_credit > 0) {

            foreach ($query_credit->result_array() as $row) {
                
                $order_id      = $row['order_id'];
                

                $query_debit_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='0' or trans_type='1' or trans_type = '2') order by id DESC limit $start, $limit");
                $count_query_debit_trans = $query_debit_trans->num_rows();
                
                //CHECK INVOICE IS ADDED OR NOT
                //echo "SELECT * FROM tbl_invoice WHERE order_id='$order_id'";
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                if ($count_query_debit_trans > 0) {
                    $credit_list_trans = array();
                    foreach ($query_debit_trans->result_array() as $row_nest) {
                        
                            $trans_id       = $row_nest['trans_id'];
                            $trans_type     = $row_nest['trans_type'];
                            $trans_mode     = $row_nest['trans_mode'];
                             $amount         = $row_nest['amount'];
                            $amount_saved        = $row_nest['amount_saved'];
                            
                          
                           
                            $trans_time     = $row_nest['trans_time'];
                            $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                            $credit_list_trans[] = array(
                                'order_id' => $order_id,
                                'trans_id' => $trans_id,
                                'trans_type' => $trans_type,
                                'trans_mode' => $trans_mode,
                                'amount' => $amount-$amount_saved,
                                'trans_time'=>$trans_time
                            ); 
                         
                    }
                        
                    
                }
                 
                $credit_list[] = array(
                    'order_id' => $order_id,
                    'trans_details'=>$credit_list_trans,
                    'invoice' =>$invoice
                ); 
                
                
            }
        }  
        
        
        
       
        $query_failure = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' AND trans_type = '2' order by id DESC limit $start, $limit");
        $count_failure = $query_failure->num_rows();
        if ($count_failure > 0) {

            foreach ($query_failure->result_array() as $row) {
                
                $order_id      = $row['order_id'];
                $trans_id      = $row['trans_id'];
                $trans_type    = $row['trans_type'];
                $amount        = $row['amount'];
                
                //CHECK INVOICE IS ADDED OR NOT
                
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                
                
                
                $failure_list[] = array(
                    'order_id' => $order_id,
                    'trans_id' => $trans_id,
                    'trans_type' => $trans_type,
                    'amount' => $amount,
                     'invoice' =>$invoice
                );
            }
           
           
        }
     
     
     //added by zak for to show  invoice list in ledger details 
     
     $query_invoice_details = $this->db->query("SELECT * FROM tbl_invoice WHERE user_id='$user_id' order by id DESC limit $start, $limit");
     $count_invoice_details = $query_invoice_details->num_rows();
     if($count_invoice_details > 0){
          foreach ($query_invoice_details->result_array() as $row) {
              $id =$row['id'];
              $order_id = $row['order_id'];
              $invoice_no = $row['invoice_no'];
              $comment =$row['comment'];
              $invoice_date = $row['invoice_date'];
              $query_attachment = $this->db->query("select * from tbl_invoice_attachment WHERE invoice_id='$id'");
              
              foreach ($query_attachment->result_array() as $row) {
                  $file_name = $row['file_name'];
              }
              $attachment_file[] = array(
                        'https://medicalwale.s3.amazonaws.com/images/invoice_images/'.$file_name
                  );
              
              $invoice_details[] = array(
                   'order_id' => $order_id,
                   'invoice_no' => $invoice_no,
                   'comment' => $comment,
                   'invoice_date' => $invoice_date,
                   'attachment' =>$attachment_file
                  );
          }
     }
     else
     {
         $invoice_details =array();
     }
        
        $query_bachat = $this->db->query("SELECT * FROM user_ledger WHERE user_id='$user_id' AND (trans_type='1') AND amount_saved!='0' GROUP BY order_id order by id DESC limit $start, $limit");

        $count_bachat = $query_bachat->num_rows();
        // print_r($count_point); die();
        //$ttl_pointds = array();
        if ($count_bachat > 0) {

            foreach ($query_bachat->result_array() as $row) {
                $ttl_bachat = array();
                $order_id      = $row['order_id'];
                $trans_type      = $row['trans_type'];
                 $discount = "";
                // $query_bachat_trans = $this->db->query();
                
                //CHECK INVOICE IS ADDED OR NOT
                $query_inovce = $this->db->query("SELECT * FROM tbl_invoice WHERE order_id='$order_id'");
                $count_inovce = $query_inovce->num_rows();
                if($count_inovce > 0){
                    $invoice = '1';
                }else{
                    $invoice = '0';
                }
                 
                //echo "SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC";
                // $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC");
                $query_bachat_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='1') order by id DESC limit $start, $limit");
                
                $count_query_bachat_trans = $query_bachat_trans->num_rows();
                 //print_r($query_bachat_trans->result_array());
                if ($count_query_bachat_trans > 0) {
                    $bachat_list_trans = array();
                    foreach ($query_bachat_trans->result_array() as $row_nest) {
                            
                        if(!empty($row_nest['amount_saved'])){
                            $trans_id       = $row_nest['trans_id'];
                            $trans_type     = $row_nest['trans_type'];
                            $trans_mode     = $row_nest['trans_mode'];
                            $amount         = $row_nest['amount'];
                            $amount_saved   = $row_nest['amount_saved'];
                            $discount       = $row_nest['discount'];
                            $trans_time     = $row_nest['trans_time'];
                               $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                                
                            array_push($ttl_bachat,$row_nest['amount']);
                            $bachat_list_trans[] = array(
                                'order_id' => $order_id,
                                'trans_id' => $trans_id,
                                'trans_mode' => $trans_mode,
                                'trans_time'=>$trans_time,
                                'invoice' =>$invoice
                            ); 
                        }
                        
                    }
                    // print_r($ttl_pointds); 
                    
                }
                
                $convert_rs = round(array_sum($ttl_bachat)/$rate);
                 $bachat_list[] = array(
                    'order_id' => $order_id,
                    'trans_type'=>$trans_type,
                    // 'total_points'=>array_sum($ttl_bachat),
                    // 'point_rupee'=>$convert_rs,
                    'amount' => $amount,
                    'amount_saved' => $amount_saved,
                    'discount' => $discount,
                    'trans_details'=>$bachat_list_trans,
                    'invoice' =>$invoice
                ); 
               
            }
           
           
        }
       // echo "SELECT SUM(points) as total_points FROM user_points WHERE user_id='$user_id' and status='active' order by id DESC";
                $query = $this->db->query("SELECT SUM(points) as total_points FROM user_points WHERE user_id='$user_id' and status='active' order by id DESC");

                $rows = $query->row();
                
                if (isset($rows))
                {
                        $total_points =  $rows->total_points;
                }else{
                        $total_points = 0;
                }
     
        $ledger_details = array();
        $ledger_details['balance_sheet'] =  $credit_list;
        $ledger_details['points']   =  $point_list;
        $ledger_details['failure'] =  $failure_list;
        $ledger_details['bachat_card'] =  $bachat_list;
      //  if($ledger_balance == $lock_amount)
        $ledger_details['balance'] = $ledger_balance;
        $ledger_details['total_points']   =  $total_points;
        $ledger_details['invoice_details'] = $invoice_details;
        return $ledger_details;
        
    }
    
    
    public function get_vendor_ledger_details($user_id){
        $debit_list =array();
        $credit_list =array();
        $failure_list = array();
        $bachat_cards_list = array();
        
        $query_debit = $this->db->query("SELECT * FROM vendor_ledger WHERE user_id='$user_id' AND trans_type = '1' order by id DESC");

        $count_debit = $query_debit->num_rows();
        if ($count_debit > 0) {

            foreach ($query_debit->result_array() as $row) {
                
                $order_id      = $row['order_id'];
                $trans_id       = $row['trans_id'];
                $trans_type     = $row['trans_type'];
                $amount        = $row['amount'];
                
                 $debit_list[] = array(
                    'order_id' => $order_id,
                    'trans_id' => $trans_id,
                    'trans_type' => $trans_type,
                    'amount' => $amount
                );
            }
            
        }        
        $query_credit = $this->db->query("SELECT * FROM vendor_ledger WHERE user_id='$user_id' AND trans_type = '0' order by id DESC");

        $count_credit = $query_credit->num_rows();
        if ($count_credit > 0) {

            foreach ($query_credit->result_array() as $row) {
                
                $order_id      = $row['order_id'];
                $trans_id      = $row['trans_id'];
                $trans_type    = $row['trans_type'];
                $amount        = $row['amount'];
                
                 $credit_list[] = array(
                    'order_id' => $order_id,
                    'trans_id' => $trans_id,
                    'trans_type' => $trans_type,
                    'amount' => $amount
                   
                );
            }
           
           
        }
        
        $query_failure = $this->db->query("SELECT * FROM vendor_ledger WHERE user_id='$user_id' AND trans_type = '2' order by id DESC");

        $count_failure = $query_failure->num_rows();
        if ($count_failure > 0) {

            foreach ($query_failure->result_array() as $row) {
                
                $order_id      = $row['order_id'];
                $trans_id       = $row['trans_id'];
                $trans_type     = $row['trans_type'];
                $amount        = $row['amount'];
                
                $failure_list[] = array(
                    'order_id' => $order_id,
                    'trans_id' => $trans_id,
                    'trans_type' => $trans_type,
                    'amount' => $amount
                );
            }
           
           
        }
     
     
     //nawaz vendor vedger //
     
     $query_bachat_card = $this->db->query("SELECT * FROM vendor_ledger WHERE user_id='$user_id' AND trans_type = '3' order by id DESC");

        $count_bachat_card = $query_bachat_card->num_rows();
        if ($count_bachat_card > 0) {

            foreach ($query_bachat_card->result_array() as $row) {
                
                $order_id      = $row['order_id'];
                $trans_id       = $row['trans_id'];
                $trans_type     = $row['trans_type'];
                $amount        = $row['amount'];
                
                $bachat_cards_list[] = array(
                    'order_id' => $order_id,
                    'trans_id' => $trans_id,
                    'trans_type' => $trans_type,
                    'amount' => $amount
                );
            }
           
           
        }
     
     
        $ledger_details = array("credit"=>"","debit"=>"","failure"=>"","bachat_card"=>"");
        $ledger_details['credit'] =  $credit_list;
        $ledger_details['debit'] =  $debit_list;
        $ledger_details['failure'] =  $failure_list;
        $ledger_details['bachat_card'] =  $bachat_cards_list;
        
        return $ledger_details;
        
    }
    
    public function get_ledgerBal_Points_old($user_id){
        
        //ledger_balance
        $query = $this->db->query("SELECT * FROM user_ledger_balance WHERE user_id='$user_id'");

        $row = $query->row();
        $count = $query->num_rows();
        if ($count>0)
        {
                $ledger_balance =  $row->ledger_balance;
                $lock_amount =  $row->lock_amount;
        }else{
                $ledger_balance = 0;
        }
        
        //points
        $query1 = $this->db->query("SELECT SUM(points) as total_points FROM user_points WHERE user_id='$user_id' and (status='active' or status='Active') order by id DESC");

        $rows = $query1->row();
        $count1 = $query1->num_rows();
        if ($count1>0)
        {
                $total_points =  $rows->total_points;
        }else{
                $total_points = 0;
        }
        // $pnts_rate = $this->db->select('rate')->where('id', 1)->get('points_rate')->row();
        //     if (!empty($pnts_rate)) {
        //         $rate = $pnts_rate->rate;
        //     } else {
        //         $rate = "";
        //     }
        $ledger_details['balance'] = $ledger_balance;
        $ledger_details['total_points']   = $total_points;
        return $data = array(
                'status' => 200,
                'message' => 'success',
                'data' =>$ledger_details
            );
        
        //$ledger_details['data']= $data;
        
        
        
        
        return $ledger_details;
    }
    
    // public function convert_user_points_to_amount($user_id){
        
    //     date_default_timezone_set('Asia/Kolkata');
    //     $date = date('Y-m-d H:i:s');
        
    //     $querys = $this->db->query("SELECT sum(points) as total_points FROM user_points WHERE user_id='$user_id' and status='active'");
    //     $count_failure = $querys->num_rows();
    //     if ($count_failure > 0) {
            
    //         foreach ($querys->result_array() as $row) {
    //           $tot_points =  $row['total_points'];
    //         }
    //      if($tot_points > 0) {
               
    //         $pnts_rate = $this->db->select('rate')->where('id', 1)->get('points_rate')->row();
    //         $pnts_rate_count = $this->db->select('rate')->where('id', 1)->get('points_rate')->num_rows();
    //         if ($pnts_rate_count > 0) {
    //             $rate = $pnts_rate->rate;
    //         } else {
    //             $rate = "";
    //         }
            
    //         //$convert_rs = round($tot_points/$rate);
    //         $convert_rs = $tot_points/$rate;
            
    //         $exist_user_or_not = $this->db->query("SELECT user_id FROM user_ledger_balance WHERE user_id='$user_id'");
    //         $count_ = $exist_user_or_not->num_rows();
    //         if ($count_ > 0) {  
                
    //             $exist_user_or_not = $this->db->query("SELECT ledger_balance FROM user_ledger_balance WHERE user_id='$user_id'");
    //             $ledger_balance = $exist_user_or_not->row()->ledger_balance;
                
    //           $ttl_ledger_bal = $ledger_balance+$convert_rs;
    //           // echo "UPDATE `user_ledger_balance` SET `ledger_balance`='$ttl_ledger_bal' WHERE user_id='$user_id'";
                
    //             //UPDATE CONVERTED AMOOUNT TO USER LEDGER BALANCE TABLE    
    //             $querys = $this->db->query("UPDATE `user_ledger_balance` SET `ledger_balance`='$ttl_ledger_bal' WHERE user_id='$user_id'");
    //         }else{
    //             //INSERT CONVERTED AMOOUNT TO USER LEDGER BALANCE TABLE    
    //             $upadte_user_ledger_amnt_array = array(
    //                 'user_id'        => $user_id,
    //                 'ledger_balance' => $convert_rs,
    //             );
    //             $querys = $this->db->insert('user_ledger_balance', $upadte_user_ledger_amnt_array);
    //         }
            
    //         if($querys){
    //             //UPDATE USER POINT STATUS
    //             $querys_update = $this->db->query("UPDATE `user_points` SET `status`='converted' WHERE user_id='$user_id' and status='active'");
    //                 if($querys_update){
                
    //                 //$new_trans = mt_rand(100000, 999999); 
    //                 $new_key = uniqid();
                        
    //                 $upadte_user_ledger_array = array(
    //                     'user_id'        => $user_id,
    //                     'order_id'       => '-',
    //                     'trans_id'       => 'BCH'.$new_key,
    //                     'trans_type'     => 5,
    //                     'amount'         => $convert_rs,
    //                     'user_id'        => $user_id,
    //                     'trans_time'     => $date
    //                 );
    //                 $inserted = $this->db->insert('user_ledger', $upadte_user_ledger_array);        
    //                 if($inserted){    
    //                     return array(
    //                         'status' => 201,
    //                         'message' => 'success',
    //                         'converted_amount' => $ttl_ledger_bal
    //                     );
    //                 }
    //             }
    //         }
            
            
    //     }else{
    //         return array(
    //             'status' => 201,
    //             'message' => 'success',
    //             'converted_amount' => 'no points found'
    //         );
    //     }
            
            
            
    //     }
    //     else{
    //         return array(
    //             'status' => 201,
    //             'message' => 'success',
    //             'converted_amount' => 'no points found'
    //         );
    //     }
    // }
    
    //new added 
    
 public function convert_user_points_to_amount_old($user_id){
    
    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d H:i:s');
    
    $querys = $this->db->query("SELECT sum(points) as total_points FROM user_points WHERE user_id='$user_id' and status='active'");
    $count_failure = $querys->num_rows();
    if ($count_failure > 0) {
        
        foreach ($querys->result_array() as $row) {
           $tot_points =  $row['total_points'];
        }
     if($tot_points > 0) {
           
        $pnts_rate = $this->db->select('rate')->where('id', 1)->get('points_rate')->row();
        $pnts_rate_count = $this->db->select('rate')->where('id', 1)->get('points_rate')->num_rows();
        if ($pnts_rate_count > 0) {
            $rate = $pnts_rate->rate;
        } else {
            $rate = "";
        }
        
        //$convert_rs = round($tot_points/$rate);
        $convert_rs = $tot_points/$rate;
        
        $exist_user_or_not = $this->db->query("SELECT user_id FROM user_ledger_balance WHERE user_id='$user_id'");
        $count_ = $exist_user_or_not->num_rows();
        if ($count_ > 0) {  
            
            $exist_user_or_not = $this->db->query("SELECT ledger_balance FROM user_ledger_balance WHERE user_id='$user_id'");
            $ledger_balance = $exist_user_or_not->row()->ledger_balance;
         
            
           $ttl_ledger_bal = $ledger_balance;
          
            
            //UPDATE CONVERTED AMOOUNT TO USER LEDGER BALANCE TABLE    
            $querys = $this->db->query("UPDATE `user_ledger_balance` SET `ledger_balance`='$ttl_ledger_bal',`lock_amount`='0' WHERE user_id='$user_id'");
        }else{
            //INSERT CONVERTED AMOOUNT TO USER LEDGER BALANCE TABLE    
            $upadte_user_ledger_amnt_array = array(
                'user_id'        => $user_id,
                'ledger_balance' => $convert_rs,
                'lock_amount'    => '0'
                
            );
            $querys = $this->db->insert('user_ledger_balance', $upadte_user_ledger_amnt_array);
        }
        
        if($querys){
            //UPDATE USER POINT STATUS
            $querys_update = $this->db->query("UPDATE `user_points` SET `status`='converted' WHERE user_id='$user_id' and status='active'");
                if($querys_update){
            
                //$new_trans = mt_rand(100000, 999999); 
                $new_key = uniqid();
                    
                $upadte_user_ledger_array = array(
                    'user_id'        => $user_id,
                    'order_id'       => '-',
                    'trans_id'       => 'BCH'.$new_key,
                    'trans_type'     => 5,
                    'amount'         => $convert_rs,
                    'user_id'        => $user_id,
                    'trans_time'     => $date
                );
                $inserted = $this->db->insert('user_ledger', $upadte_user_ledger_array);        
                if($inserted){    
                    return array(
                        'status' => 201,
                        'message' => 'success',
                        'converted_amount' => $ttl_ledger_bal
                    );
                }
            }
        }
        
        
    }else{
        return array(
            'status' => 201,
            'message' => 'success',
            'converted_amount' => 'no points found'
        );
    }
        
        
        
    }
    else{
        return array(
            'status' => 201,
            'message' => 'success',
            'converted_amount' => 'no points found'
        );
    }
}
    
    public function insert_add_cart($user_id, $listing_id, $product_id, $quantity, $sub_category, $product_type, $status){
        
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        $add_cart = array(
            'user_id'        => $user_id,
            'listing_id'     => $listing_id,
            'product_id'     => $product_id,
            'quantity'       => $quantity,
            'sub_category'   => $sub_category,
            'product_type'   => $product_type,
            'status'         => $status,
            'created_at'     => $date,
            'updated_at'     => $date
        );
        $inserted = $this->db->insert('cart', $add_cart);        
        if($inserted){    
            return array(
                'status' => 201,
                'message' => 'success'
            );
        }
        
    }
    
    public function delete_cart($cart_id){
        $this->db->where('id', $cart_id);
        $this->db->delete('cart');
        if ($this->db->affected_rows()) {
            return array(
                'status' => 201,
                'message' => 'error'
            );
        } else {
            return array(
                'status' => 201,
                'message' => 'success'
            );
        }
    }
    
    public function update_cart($cart_id, $quantity){
        
        $data = array(  
            'quantity' => $quantity
        );  
        
        $this->db->where('id', $cart_id);  
        $this->db->update('cart', $data); 
        
        if ($this->db->affected_rows()) {
            return array(
                'status' => 201,
                'message' => 'error'
            );
        } else {
            return array(
                'status' => 201,
                'message' => 'success'
            );
        }
    }
    
    public function get_cart_details_list($user_id){
          $query = $this->db->query("SELECT * FROM `cart` WHERE `user_id` = '$user_id' ");
        $num_count = $query->num_rows();
        $qRows = $query->result_array();
       // print_r ($num_count);
      //  print_r ($qRows);
      //  die();
        if($num_count>0)
        {
        foreach($qRows as $qRow)
        {
            $id = $qRow['id'];
            $user_id = $qRow['user_id'];
            $listing_id =$qRow['listing_id'];
            $product_id =$qRow['product_id'];
            $sub_category = $qRow['sub_category'];
            $quantity = $qRow['quantity'];
            $product_type = $qRow['product_type'];
            $status = $qRow['status'];
            //  echo "SELECT * FROM `product` WHERE `id` = '$id' and `sub_category` = '$sub_category' ";
              $product_query = $this->db->query("SELECT * FROM `product` WHERE `id` = '$product_id' and `sub_category` = '$sub_category' ");
               $nume_count = $product_query->num_rows();
              $product_Rows = $product_query->result_array();
            //  print_r ($nume_count);
            //  print_r ($product_Rows);
            //  die();
            if($nume_count>0)
            {
                foreach($product_Rows as $product_Row)
                {
                    $product_name = $product_Row['product_name'];
                   $product_price = $product_Row['product_price'];
                   $product_description = $product_Row['product_description'];
                   $product_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/'. $product_Row['image'];
                   $in_stock = $product_Row['in_stock'];
                   $product_weight = $product_Row['pack'];
                }
            }
            else
            {
                $product_name = "";
                   $product_price = "";
                   $product_description = "";
                   $product_image = "";
                   $in_stock = "";
                   $product_weight = "";
            }
            
             $results_cart[] = array(
                        'id' => $id,
                        'user_id' => $user_id,
                        'listing_id' =>$listing_id,
                        'product_id' =>$product_id,
                        'sub_category' =>$sub_category,
                        'quantity' =>$quantity,
                        'product_type'=>$product_type,
                        'status'=>$status,
                        'product_name' => $product_name,
                        'product_price' => $product_price,
                        'product_image' => $product_image,
                        'product_description' => $product_description,
                        'in_stock' => $in_stock,
                        'product_weight' => $product_weight
                    ); 
        }
            $data = $results_cart;
            return $data;
        }
        else
        {
           return array(
                'status' => 200,
                'data' => array(),
                'message' => 'data not found'
            ); 
        }
    }
    
    public function add_invoice($user_id, $invoice_number, $order_id, $invoice_date, $comment, $invoice_file){
        
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        $getComp = $this->db->query("SELECT * FROM `tbl_invoice` WHERE order_id ='$order_id'");
        $num_count_data = $getComp->num_rows();
        $comp = $getComp->row();
        
        if($num_count_data > 0){
            
           /* $this->db->where('invoice_id', $comp->id);
            $Deleted = $this->db->delete('tbl_invoice_attachment'); */   
            
            $add_invoice = array(
                'user_id'       => $user_id,
                'invoice_no'    => $invoice_number,
                'order_id'      => $order_id,
                'comment'       => $comment,
                'invoice_date'  => $invoice_date,
                'created_at'    => $date,
                'updated_at'    => $date
            ); 
            
            $this->db->where('order_id', $order_id);  
            $this->db->update('tbl_invoice', $add_invoice); 
            
            //---------------------------INVOICE ATTACHMENT UPLOAD---------------------------
            //echo $invoice_file;;
           /* echo $_FILES['invoice_file']['name'];
            print_r($_FILES);*/
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF", "pdf");
            include('../s3_config.php'); 
            if($invoice_file > 0) {
                $flag = '1';
                $video_flag = '1';
                foreach ($_FILES['invoice_file']['tmp_name'] as $key => $tmp_name) {
                    $img_name = $key . $_FILES['invoice_file']['name'][$key];
                    $img_size = $_FILES['invoice_file']['size'][$key];
                    $img_tmp = $_FILES['invoice_file']['tmp_name'][$key];
                    $ext = getExtension($img_name);
                    if (strlen($img_name) > 0) {
                            if (in_array($ext, $img_format)) {
                                $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/invoice_images/' . $actual_image_name;
                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                    if ($flag > 0) {
                                        $img_url = 'https://medicalwale.s3.amazonaws.com/images/invoice_images/' . $actual_image_name;
                                        $imagedetails = getimagesize($img_url);
                                        $add_invoice_attachment = array(
                                            'invoice_id'    => $comp->id,
                                            'file_name'     => $actual_image_name,
                                            'created_at'    => $date,
                                            'updated_at'    => $date
                                        );
                                        $inserted = $this->db->insert('tbl_invoice_attachment', $add_invoice_attachment);
                                        
                                    }
                                    
                                }
                            }
                        
                    }
                }
                
            }
               
                return array(
                    'status' => 201,
                    'message' => 'success'
                );
           
            
        }else{
        
           /* $this->db->where('invoice_id', $comp->id);
            $Deleted = $this->db->delete('tbl_invoice_attachment'); */
        
            $add_invoice = array(
                'user_id'       => $user_id,
                'invoice_no'    => $invoice_number,
                'order_id'      => $order_id,
                'comment'       => $comment,
                'invoice_date'  => $invoice_date,
                'created_at'    => $date,
                'updated_at'    => $date
            );
            $inserted = $this->db->insert('tbl_invoice', $add_invoice);        
            $invoice_id = $this->db->insert_id(); 
            
            
            
            
            //---------------------------INVOICE ATTACHMENT UPLOAD---------------------------
            $invoice_file = count($_FILES['invoice_file']['name']);
            /* echo $_FILES['invoice_file']['name'];
            print_r($_FILES);*/
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF", "pdf");
            include('../s3_config.php'); 
            if($invoice_file > 0) {
                $flag = '1';
                $video_flag = '1';
                foreach ($_FILES['invoice_file']['tmp_name'] as $key => $tmp_name) {
                    $img_name = $key . $_FILES['invoice_file']['name'][$key];
                    $img_size = $_FILES['invoice_file']['size'][$key];
                    $img_tmp = $_FILES['invoice_file']['tmp_name'][$key];
                    $ext = getExtension($img_name);
                    if (strlen($img_name) > 0) {
                            if (in_array($ext, $img_format)) {
                                $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/invoice_images/' . $actual_image_name;
                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                    if ($flag > 0) {
                                        $img_url = 'https://medicalwale.s3.amazonaws.com/images/invoice_images/' . $actual_image_name;
                                        $imagedetails = getimagesize($img_url);
                                        $add_invoice_attachment = array(
                                            'invoice_id'    => $invoice_id,
                                            'file_name'     => $actual_image_name,
                                            'created_at'    => $date,
                                            'updated_at'    => $date
                                        );
                                        $inserted = $this->db->insert('tbl_invoice_attachment', $add_invoice_attachment);
                                        
                                    }
                                    
                                }
                            }
                        
                    }
                }
               
            }
            if($inserted){    
                    return array(
                        'status' => 201,
                        'message' => 'success'
                    );
                }
           
        }
        //---------------------------IF END---------------------------
        
    }
    
     public function add_invoice_web($user_id, $invoice_number, $order_id, $invoice_date, $comment, $invoice_file){
        
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        $getComp = $this->db->query("SELECT * FROM `tbl_invoice` WHERE order_id ='$order_id'");
        $num_count_data = $getComp->num_rows();
        $comp = $getComp->row();
        
        if($num_count_data > 0){
            
           /* $this->db->where('invoice_id', $comp->id);
            $Deleted = $this->db->delete('tbl_invoice_attachment'); */   
            
            $add_invoice = array(
                'user_id'       => $user_id,
                'invoice_no'    => $invoice_number,
                'order_id'      => $order_id,
                'comment'       => $comment,
                'invoice_date'  => $invoice_date,
                'created_at'    => $date,
                'updated_at'    => $date
            ); 
            
            $this->db->where('order_id', $order_id);  
            $this->db->update('tbl_invoice', $add_invoice); 
            
            //---------------------------INVOICE ATTACHMENT UPLOAD---------------------------
            //echo $invoice_file;;
           /* echo $_FILES['invoice_file']['name'];
            print_r($_FILES);*/
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF", "pdf");
           
            if($invoice_file > 0) {
                $flag = '1';
                $video_flag = '1';
                  $imagearray=explode(',', $invoice_file);
                foreach ($imagearray as $image) {
                  
                    $ext = getExtension($image);
                    if ($image > 0) {
                            if (in_array($ext, $img_format)) {
                              
                                
                                
                                    if ($flag > 0) {
                                     
                                        $add_invoice_attachment = array(
                                            'invoice_id'    => $comp->id,
                                            'file_name'     => $image,
                                            'created_at'    => $date,
                                            'updated_at'    => $date
                                        );
                                        $inserted = $this->db->insert('tbl_invoice_attachment', $add_invoice_attachment);
                                        
                                    }
                    
                                }
                            
                           }
                      }
                    
                 }
                   
                return array(
                    'status' => 201,
                    'message' => 'success'
                );
           
            
        }else{
        
           /* $this->db->where('invoice_id', $comp->id);
            $Deleted = $this->db->delete('tbl_invoice_attachment'); */
        
            $add_invoice = array(
                'user_id'       => $user_id,
                'invoice_no'    => $invoice_number,
                'order_id'      => $order_id,
                'comment'       => $comment,
                'invoice_date'  => $invoice_date,
                'created_at'    => $date,
                'updated_at'    => $date
            );
            $inserted = $this->db->insert('tbl_invoice', $add_invoice);        
            $invoice_id = $this->db->insert_id(); 
            
            
            
            
            //---------------------------INVOICE ATTACHMENT UPLOAD---------------------------
            $imagearray=explode(',', $invoice_file);
            $invoice_file = count($imagearray);
            /* echo $_FILES['invoice_file']['name'];
            print_r($_FILES);*/
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF", "pdf");
            include('../s3_config.php'); 
            if($invoice_file > 0) {
                $flag = '1';
                $video_flag = '1';
                
                foreach ($imagearray as $image) {
                    $ext = getExtension($image);
                    if ($image > 0) {
                            if (in_array($ext, $img_format)) {
                                    if ($flag > 0) {
                                       
                                        $add_invoice_attachment = array(
                                            'invoice_id'    => $invoice_id,
                                            'file_name'     => $image,
                                            'created_at'    => $date,
                                            'updated_at'    => $date
                                        );
                                        $inserted = $this->db->insert('tbl_invoice_attachment', $add_invoice_attachment);
                                        
                                    }
                                    
                                
                            }
                        
                    }
                }
               
            }
            if($inserted){    
                    return array(
                        'status' => 201,
                        'message' => 'success'
                    );
                }
           
        }
        //---------------------------IF END---------------------------
        
    }
    
    
    
    
    
    public function view_invoice($user_id, $order_id){
        
        $query = $this->db->query("SELECT * FROM `tbl_invoice` WHERE `user_id` = '$user_id' and order_id ='$order_id'");
        $num_count = $query->num_rows();
        $qRows = $query->row();
        
        if($num_count > 0){
            
            //echo "SELECT * FROM `tbl_invoice_attachment` WHERE `invoice_id` = '$qRows->id'";
            $query = $this->db->query("SELECT * FROM `tbl_invoice_attachment` WHERE `invoice_id` = '$qRows->id' ");
            $num_count_ = $query->num_rows();
            $qRows_attach = $query->result();
            if($num_count_ > 0){    
                foreach($qRows_attach as $attach_row){
                    
                    $attachement_img_arra[] = array(
                                            'file_name' =>'https://medicalwale.s3.amazonaws.com/images/invoice_images/'.$attach_row->file_name,
                                            'image_id' =>$attach_row->id
                                              ); 
                }
            }else{
                $attachement_img_arra =array();
            }
            
            $add_invoice = array(
                'user_id'       => $qRows->user_id,
                'invoice_no'    => $qRows->invoice_no,
                'order_id'      => $qRows->order_id,
                'invoice_date'  => $qRows->invoice_date,
                'comment'       => $qRows->comment,
                'attachments'   => $attachement_img_arra
            );
            
            return array(
                'status'  => 201,
                'message' => 'success',
                'data'    => $add_invoice
            );
        }
        else{
            return array(
                'status'  => 201,
                'message' => 'No data found!'
            );
        }
        
    }
    
    public function delete_invoice_attachment($image_id){
        
      
            $this->db->where('id', $image_id);
            $this->db->delete('tbl_invoice_attachment');
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
    
    public function delete_invoice($user_id, $order_id){
        
           // echo "SELECT * FROM `tbl_invoice` WHERE `user_id` = '$user_id' and order_id ='$order_id'";
            $query = $this->db->query("SELECT * FROM `tbl_invoice` WHERE `user_id` = '$user_id' and order_id ='$order_id'");
          $num_count = $query->num_rows();
            
            if($num_count > 0){
                $id = $query->row()->id;
                $this->db->where('invoice_id', $id);
                $this->db->delete('tbl_invoice_attachment');
               
                $this->db->where('user_id', $user_id);
                    $this->db->where('order_id', $order_id);
                    $this->db->delete('tbl_invoice');
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
               
                
            }else{
                 return array(
                                'status' => 201,
                                'message' => 'No Record Found!'
                            );
            }
            
    }
    
    // add_delivery_charges : added by swapnali on 17th sept 2019
    
    public function add_delivery_charges($delivery_charges, $invoice_no, $delivery_charges_by_customer, $offer_id){
        $this->load->model('PartnermnoModel');
        $delivery_charges_by_vendor = 0;
        $delivery_charges_by_mw = 0;
        $delivery_charges_to_mno = 0;
        $mno_service_charge_rupees = 0;
        $mno_service_charge_percent = 0;
        $data = array();
        $order_details = $this->db->query("SELECT uo.* FROM user_order as uo WHERE uo.invoice_no = '$invoice_no' GROUP BY uo.invoice_no ")->row_array();
        
        if(sizeof($order_details) > 0){
            $listing_type = $order_details['listing_type'];
            $order_deliver_by = $order_details['order_deliver_by']; // if blanck then consider as deliver by pharmacy
            if($listing_type == 13 || $listing_type == 44){
                if($listing_type == 13){
                    // order to pharmacy
                    if($order_deliver_by == 'mno'){
                        // pharmacy order and delivery by mno
                        $delivery_charges = $delivery_charges;
                        $delivery_charges_by_customer = $delivery_charges_by_customer;
                        $delivery_charges_by_vendor = $delivery_charges - $delivery_charges_by_customer;
                        $delivery_charges_by_mw = 0;
                        $delivery_charges_to_mno = $delivery_charges;
                        $mno_service_charge_percent = 18; //standard
                        $mno_service_charge_rupees = (floatval($delivery_charges_to_mno))*((floatval($mno_service_charge_percent))/100);
                        
                    } else { 
                        // pharmacy order and delivery by pharmacy
                        $delivery_charges = $delivery_charges_by_customer;
                        $delivery_charges_by_customer = $delivery_charges_by_customer;
                        $delivery_charges_by_vendor = 0;
                        $delivery_charges_by_mw = 0;
                        $delivery_charges_to_mno = 0;
                    } 
                } else if($listing_type == 44){
                    // order to MNO
                    // consider default as $order_deliver_by == 'mno'
                        if($offer_id > 0){
                            $offer_on = 1; // as per vendor_offer table offer_on = 1 and listing_id = 44 means mno order where user will get off on delivery charges
                            // only if delivery by mno
                            $order_total = $order_details['order_total'];
                            $offerDetails = $this->PartnermnoModel->get_offer_details($offer_id, $offer_on,$delivery_charges,$order_total);
                            
                            if($offerDetails['status'] == true){
                                $del_charge_off_for_customer = $offerDetails['del_charge_off_for_customer'];
                                $delivery_charges_by_mw = $del_charge_off_for_customer;
                            } else {
                                $delivery_charges_by_mw = 0;
                            }
                           
                        } 
                        // MNO order and delivery by mno
                        
                        $delivery_charges = $delivery_charges;
                        $delivery_charges_by_customer = $delivery_charges - $delivery_charges_by_mw;
                        $delivery_charges_by_vendor = 0;
                        $delivery_charges_by_mw = $delivery_charges_by_mw;
                        $delivery_charges_to_mno = $delivery_charges;
                        
                    
                } 
                $added_delivery_charges['delivery_charges'] = $delivery_charges;
                $added_delivery_charges['delivery_charges_by_customer'] = $delivery_charges_by_customer;
                $added_delivery_charges['delivery_charges_by_vendor'] = $delivery_charges_by_vendor;
                $added_delivery_charges['delivery_charges_by_mw'] = $delivery_charges_by_mw;
                $added_delivery_charges['delivery_charges_to_mno'] = $delivery_charges_to_mno;
                $added_delivery_charges['mno_service_charge_rupees'] = $mno_service_charge_rupees;
                $added_delivery_charges['mno_service_charge_percent'] = $mno_service_charge_percent;
           
    
                // update user_order table with delivery charges
                
                $this->db->query("UPDATE user_order SET mno_service_charge_rupees = '$mno_service_charge_rupees', mno_service_charge_percent = '$mno_service_charge_percent' ,`delivery_charge` = '$delivery_charges' , `delivery_charges_by_customer` = '$delivery_charges_by_customer', `delivery_charges_by_vendor` = '$delivery_charges_by_vendor' , `delivery_charges_by_mw` = '$delivery_charges_by_mw' , `delivery_charges_to_mno` = '$delivery_charges_to_mno' WHERE invoice_no = '$invoice_no' ");
                
                $data['status'] = 1;
                $data['message'] = "success";
                $data['data'] = $added_delivery_charges;
            } else {
                // no mno or pharmacy order
                $data['status'] = 4;
                $data['message'] = "No pharmacy or night owl order";
            }
        } else {
            $data['status'] = 2;
            $data['message'] = "No order found";
        }
        return $data;
    }
    
    // added by swapnali on 21st sept 2k19
    /*used in Partnermnomodel*/
    public function get_invoice_costing($invoice_no){
        $cost  = $data = array();
        $order_found = 0;
        $user_order = $this->db->query("SELECT pm.icon,uo.* FROM user_order as uo left join payment_method as pm on (pm.payment_method = uo.payment_method) WHERE uo.invoice_no  = '$invoice_no' Group by uo.invoice_no ")->row_array();
        if(sizeof($user_order) > 0){
           $order_found = 1; 
           
            $order_id = $user_order['order_id'];
            $invoice_no = $user_order['invoice_no'];
            $listing_type = $user_order['listing_type'];
            $actual_cost = $user_order['actual_cost'];
            $order_total = $user_order['order_total']; // amount which customer will pay without discount
            $delivery_charge = $user_order['delivery_charge'];
            $delivery_charges_by_customer = $user_order['delivery_charges_by_customer'];
            $delivery_charges_by_vendor = $user_order['delivery_charges_by_vendor'];
            $delivery_charges_by_mw = $user_order['delivery_charges_by_mw'];
            $delivery_charges_to_mno = $user_order['delivery_charges_to_mno'];
            $discount = $user_order['discount']; // in rupees
            $chc = $user_order['chc'];
            $gst = $user_order['gst'];
            $payment_method = $user_order['payment_method'];
            $icon = $user_order['icon'];
            $order_date = $user_order['order_date'];
            $order_deliver_by = $user_order['order_deliver_by'];
            $listing_type = $user_order['listing_type']; 
            $bill_url = $user_order['bill_url']; 
            $amount_with_discount = $order_total - $discount; // amount with discount
            $gst_in_rupees = ($amount_with_discount * $gst) / 100; 
            
            /*aded by swapnali on 24th APR 2020 6:00*/
            $mno_service_charge_percent = $user_order['mno_service_charge_percent'];
            $mno_service_charge_rupees = $user_order['mno_service_charge_rupees'];
            
            // grand total
            // grand_total_for_customer
            // grand_total_for_vendor
            $grand_total = $amount_with_discount + $gst_in_rupees + $chc + $delivery_charge;
            $grand_total_for_customer = $amount_with_discount + $gst_in_rupees + $chc + $delivery_charges_by_customer;
            $grand_total_for_vendor = $amount_with_discount + $gst_in_rupees + $chc - $delivery_charges_by_vendor + $mno_service_charge_rupees;
            
            $cost['order_id'] = intval($order_id);
            $cost['invoice_no'] = $invoice_no;
            $cost['listing_type'] = $listing_type ;
            $cost['order_deliver_by'] = $order_deliver_by;
            $cost['delivery_charge'] = $delivery_charge;
            $cost['delivery_charges_by_customer'] = $delivery_charges_by_customer;
            $cost['delivery_charges_by_vendor'] = $delivery_charges_by_vendor;
            $cost['delivery_charges_by_mw'] = $delivery_charges_by_mw;
            $cost['delivery_charges_to_mno'] = $delivery_charges_to_mno;
            $cost['mno_service_charge_percent'] = $mno_service_charge_percent;
            $cost['mno_service_charge_rupees'] = $mno_service_charge_rupees;
            $cost['discount'] = $discount;
            $cost['chc'] = $chc;
            $cost['gst_in_percent'] = $gst;
            $cost['gst_in_rupees'] = strval($gst_in_rupees);
            $cost['payment_method'] = $payment_method;
            $cost['payment_icon'] = $icon;
            $cost['sub_total'] = $order_total;
            $cost['amount_without_delivery'] = strval($amount_with_discount + $gst_in_rupees + $chc);
            $cost['grand_total'] = strval($grand_total);
            $cost['grand_total_customer'] = strval($grand_total_for_customer);
            $cost['grand_total_vendor'] = strval($grand_total_for_vendor);  
            $cost['bill_url'] = $bill_url;
        }
        
        $data['order_found'] = $order_found;
        $data['data'] = $cost;
        return $data;
    }
    
    
    
    public function generate_bhc($user_id,$coupon_id, $amount, $payment_method, $transaction_id, $trans_status) {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id = date("YmdHis");
        $created_at = date('Y-m-d H:i:s');
        $response = array();
       $invoice_no = "";
        
        // get payment details used in create ledger - done
        // get payment status - done
        // if payment success
        //   -- create ledger user debit transaction success - done
        //   -- generate card - done
        //      success ---- create ledger user credit - done
        //      failed  ---- no any entry - done
        // if payment failed
        //   -- create ledger user debit transaction failed
        
        if($trans_status == 1){
            
            
            $response = $this->LoginModel->generate_privilage($user_id,$coupon_id);
            if(array_key_exists('id',$response)){
                $card_id = $response['id'];
            } else {
                $card_id = "";
            }
            if($amount > 0){
                // add for each transaction
                $mw_id = $mw_id_type = $user_id_type = 0;
                $invoice_no = $card_id;
                $user_comments = "";
                $mw_comments = "Paid for bachat helth card";
                $vendor_comments = "";
                //$payment_method = "";
                $credit = "";
                $debit = $amount;
                $transaction_of = 2; // entry for amount
                $order_type = 6; // BHC
                $transaction_id = "";
                //$trans_status = 1;
                $transaction_date = "";
                $vendor_id = 35;
                $array_data = array();
                // order picked up : package entry from pharmacy to mno
                
                // WORKING
                
                $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $mw_id, $mw_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_id,$array_data);
             //   print_r($res); die();
            }
          //  echo "out of ledger"; die();
            
            if($response['status'] ==  200 && $response['card_status'] == 1 && $amount > 0){
                
                // add for each transaction
                $invoice_no = $card_id;
                $mw_id = $mw_id_type = $user_id_type = 0;
                $user_comments = "";
                $mw_comments = "Bachat helth card activated";
                $vendor_comments = "";
                $payment_method = "";
                $credit = $amount;
                $debit = "";
                $transaction_of = 3; // entry for service
                $order_type = 6; // BHC
                $transaction_id = "";
                $trans_status = 1;
                $transaction_date = "";
                $vendor_id = 35;
                $array_data = array();
                // order picked up : package entry from pharmacy to mno
                
                // WORKING
                
                $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $mw_id, $mw_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_id,$array_data);
            
            }
            
        } else {
            // payment failed
            $response = array('status' => 204);
        }
        return $response;
        
        
    }
    
    public function convert_user_points_to_amount($user_id){
         $this->load->model('LedgerModel'); 
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $querys = 0;
        $ttl_ledger_bal = $ledger_balance = 0;
        $get_points = $this->db->query("SELECT u.id,u.vendor_id , sum(up.points) as total_points FROM users as u left join user_points as up on (u.id = up.user_id) WHERE up.user_id='$user_id' and (up.status='active' or up.status='Active') AND up.expire_at > '$date'")->row_array();
        if (sizeof($get_points) > 0) {
            $tot_points =  $get_points['total_points'];
            $vendor_id = $get_points['vendor_id'] ; 
           
            if($tot_points >= 100) {
                $get_rate = $this->db->query("SELECT * from points_rate WHERE vendor_type = '$vendor_id'")->row_array();
                if(sizeof($get_rate) > 0){
                    $rate = $get_rate['rate'];
                    $convert_rs = $tot_points/$rate;
                   
                    $exist_user_or_not = $this->db->query("SELECT * FROM user_ledger_balance WHERE user_id='$user_id'")->row_array();
                    if (sizeof($exist_user_or_not) > 0) {
                        $ledger_balance = $exist_user_or_not['ledger_balance'];
                        $ttl_ledger_bal = $ledger_balance + $convert_rs;
                        //UPDATE CONVERTED AMOOUNT TO USER LEDGER BALANCE TABLE    
                        $querys = $this->db->query("UPDATE `user_ledger_balance` SET `ledger_balance`='$ttl_ledger_bal'  WHERE user_id='$user_id'");
                    }else{
                        //INSERT CONVERTED AMOOUNT TO USER LEDGER BALANCE TABLE  
                        $ttl_ledger_bal = $convert_rs;
                        $upadte_user_ledger_amnt_array = array(
                            'user_id'        => $user_id,
                            'ledger_balance' => $convert_rs,
                            'lock_amount'    => '0'
                            
                        );
                        $querys = $this->db->insert('user_ledger_balance', $upadte_user_ledger_amnt_array);
                    }
                    
                    if($querys){
                        //UPDATE USER POINT STATUS
                        $querys_update = $this->db->query("UPDATE `user_points` SET `status`='converted' WHERE user_id='$user_id' and (status='active' or status='Active') AND expire_at > '$date'");
                        if($querys_update){ 
                            $new_key = date('ymdHsi');
                  
                            /*create ledger entry*/ 
                            $invoice_no = 'BCH'.$new_key;
                            $ledger_owner_type = '0';
                            $listing_id = '0';
                            $listing_id_type = '0';
                            $credit = $convert_rs;
                            $debit = '';
                            $payment_method = '';
                            $user_comments = '';
                            $mw_comments = 'Converted '.$tot_points.' points to '.$convert_rs.' ledger';
                            $vendor_comments = '';
                            $order_type = '9';
                            $transaction_of = '4'; //leger credited
                            $transaction_id = 'BCH'.$new_key;
                            $trans_status = '1';
                            $transaction_date = date('Y-m-d H:s:i');
                            $vendor_id = '';
                            $ledger_entry = $data = array();
                            $ledger_entry = $this->LedgerModel->create_ledger($user_id,  $invoice_no,  $ledger_owner_type,  $listing_id,  $listing_id_type,  $credit,  $debit,  $payment_method,  $user_comments,  $mw_comments, $vendor_comments, $order_type, $transaction_of, $transaction_id, $trans_status, $transaction_date, $vendor_id, $data); 
                            
                            return array(
                                'status' => 200,
                                'message' => 'success',
                                'converted_amount' => strval($ttl_ledger_bal)
                            );
                            
                            
                        }
                    }
                } else {
                    return array(
                        'status' => 400,
                        'message' => 'success',
                        'converted_amount' => 'Can not redeem points'
                    );   
                }
            } else {
                return array(
                    'status' => 400,
                    'message' => 'Total points should be greater than 100'
                );
            }
        } else {
            return array(
                'status' => 400,
                'message' => 'No points found'
            );
        }
    }
    
    public function get_ledgerBal_Points($user_id){
        
        //ledger_balance
        $row = $this->db->query("SELECT * FROM user_ledger_balance WHERE user_id='$user_id'")->row();
        if (sizeof($row)>0){
                $ledger_balance =  $row->ledger_balance == null ? 0 : $row->ledger_balance;
                $lock_amount =  $row->lock_amount == null ? 0 : $row->lock_amount ;
        }else{
                $ledger_balance = 0;
        }
        
        //points
        $currentDate = date('Y-m-d H:i:s');
        $rows = $this->db->query("SELECT SUM(points) as total_points FROM `user_points` WHERE `user_id` = '$user_id' AND `expire_at` > '$currentDate' AND (`status` LIKE 'Active' or `status` LIKE 'active')  ")->row();

        if (sizeof($rows)>0) {
                $total_points =  $rows->total_points == null ? 0 : $rows->total_points;
        } else {
                $total_points = 0;
        }
      
        $ledger_details['balance'] = floatval($ledger_balance);
        $ledger_details['total_points']   = strval($total_points);
        return $data = array(
                'status' => 200,
                'message' => 'success',
                'data' =>$ledger_details
            );
        return $ledger_details;
    }
         
}
