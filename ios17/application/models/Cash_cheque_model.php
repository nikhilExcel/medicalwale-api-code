<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cash_cheque_model extends CI_Model
{
    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";
    public function check_auth_client()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key       = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            ));
        }
    }
   
    public function auth()
    {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token    = $this->input->get_request_header('Authorizations', TRUE);
        $q        = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            ));
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
    
    public function add_cash_cheque($user_id,$coupon_id,$vendor_id,$amount,$txn_id,$invoice_no)
    {
        date_default_timezone_set('Asia/Kolkata');
        $resultpost =array();
            	// echo "SELECT * FROM `cash_cheque_master` WHERE vendor_id='$vendor_id' and id='$coupon_id' and active=1"; 
            	// die;
                 $query = $this->db->query("SELECT * FROM `cash_cheque_master` WHERE vendor_id='$vendor_id' and id='$coupon_id' and active=1");
                $row=$query->row_array();
                
         
                if($query->num_rows()>0)
                {
           
                    $discount_type=$row['save_type'];
                    $discount_amount=$row['amount'];
                    $max_usage_day=$row['max_usage_day']; 
                    $min_order=$row['min_order']; 
                    $max_order=$row['max_order']; 
                    $expiry_dayt=$row['expiry_day']; 
                    $creation_date=$row['creation_date']; 
                    $is_active=$row['active']; 
                     $date_exp = date('Y-m-d H:i:s', strtotime('+'.$expiry_dayt.' days'));


                    if($discount_type=="percent"){
                        if($amount >= $min_order ){
                                    $percentage = ($discount_amount*$amount)/100;
                                        if ($percentage >= $max_order){
            
                                            $percentage=$max_order;
                                            
                                        }else{
                                            
                                            $percentage;
                                        }
                                 
                                   
                                     $insert_data=array(
                                        'user_id'=>$user_id,
                                      'cash_cheque_id'=>$coupon_id,
                                      'transaction_date'=>date('Y-m-d H:i:s'),
                                      'transaction_vendor_id'=>$vendor_id,
                                      'transaction_amount'=>$amount,
                                      'reward_amount'=>$percentage,
                                      'creation_date'=>date('Y-m-d H:i:s'),
                                      'expiry_date'=>$date_exp,
                                      'status'=>'1'
                                      );  
                              $this->db->insert('cash_cheque_reward',$insert_data);
                            }else{
                                
                                   $percentage="min amount is to low shop for more to get discount";
                            }    
                        
                    }else{
                            
                              if($amount >= $min_order ){
                                    $percentage = ($amount-$discount_amount);
                                     $insert_data=array(
                                                'user_id'=>$user_id,
                                      'cash_cheque_id'=>$coupon_id,
                                      'transaction_date'=>date('Y-m-d H:i:s'),
                                      'transaction_vendor_id'=>$vendor_id,
                                      'transaction_amount'=>$amount,
                                      'reward_amount'=>$discount_amount,
                                      'creation_date'=>date('Y-m-d H:i:s'),
                                      'expiry_date'=>$date_exp,
                                      'status'=>'1'
                                      );  
                            $this->db->insert('cash_cheque_reward',$insert_data);
                                     
                                }else{
                                    
                                       $percentage="min amount is to low shop for more to get discount";
                                } 
                        
                        }
        
                    $resultpost[] = array
                        (
                           "card_id" => $txn_id,
                           "percentage_amount" => $percentage,
                        );
                
        
                           return $resultpost;
 
    	    } 
    	    else
    	    {
    	      return  $resultpost; 
    	    }
    
    }
    
    public function get_coupon_by_vendor($user_id,$vendor_id,$listing_id,$category,$product_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $resultpost =array();
        $where1 = "";
            if($listing_id=='34'){
            
                       if(!empty($category))
                       {
                          $where1 .= "AND offer_on='$category'  ";
                       }
                       if(!empty($product_id)) 
                       {
                          $where1 .= "AND FIND_IN_SET('$product_id',offer_on_id)"; 
                       }
               
            } 
            	
                $query = $this->db->query("SELECT * FROM `cash_cheque_master` WHERE vendor_id='$vendor_id' and listing_id='$listing_id' $where1  and active=1");
                
                
                
                if($query->num_rows()>0)
                {
                foreach ($query->result_array() as $row) {
        
                    $discount_type=$row['save_type'];
                    $discount_amount=$row['amount'];
                    $max_usage_day=$row['max_usage_day']; 
                    $min_order=$row['min_order']; 
                    $max_order=$row['max_order']; 
                    $expiry_dayt=$row['expiry_day']; 
                    $creation_date=$row['creation_date']; 
                    $is_active=$row['active']; 

        
                    $resultpost[] = array
                        (
                           "discount_type" => $discount_type,
                           "discount_amount" => $discount_amount,
                           "max_usage_day" => $max_usage_day,
                           "min_order" => $min_order,
                           "max_discount"=>$max_order,
                           "discount_type" => $discount_type,
                          
                         
                        );
                }
        
                           return $resultpost;
 
    	    } 
    	    else
    	    {
    	      return  $resultpost; 
    	    }
    
    }
    
    public function get_all_cashcheck($user_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $resultpost =array();
       
        
                $query = $this->db->query("SELECT * FROM `cash_cheque_master` WHERE  active=1");
                
                if($query->num_rows()>0)
                {
                foreach ($query->result_array() as $row) {
        
                 $vendor_name=$row['vendor_name'];
                    $listing_id=$row['listing_id'];
                    $vendor_id=$row['vendor_id'];
                    $discount_type=$row['save_type'];
                    $discount_amount=$row['amount'];
                    $max_usage_day=$row['max_usage_day']; 
                    $min_order=$row['min_order']; 
                    $max_order=$row['max_order']; 
                    $expiry_dayt=$row['expiry_day']; 
                    $creation_date=$row['creation_date']; 
                    $is_active=$row['active']; 
                        if($discount_type=="percent"){
                         $offer_type="Upto"; 
                        }else{
                            $offer_type="Flat";
                        }
        
                    $resultpost[] = array
                        (   
                            "Brand_image"=>"",
                            "vendor_id"=>$vendor_id,
                            "vendor_name"=>$vendor_name,
                            "listing_id"=>$listing_id,
                           "offer_type"=>$offer_type,
                           "discount_type" => $discount_type,
                           "discount_amount" => $discount_amount,
                           "max_usage_day" => $max_usage_day,
                           "min_order" => $min_order,
                           "max_discount"=>$max_order,
                           "discount_type" => $discount_type,
                           
                          
                        );
                }
        
                           return $resultpost;
 
    	    } 
    	    else
    	    {
    	      return  $resultpost; 
    	    }
    
    }
    
      public function get_user_cashcheck($user_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $resultpost =array();
       $date=date('Y-m-d H:i:s');
        
                $query = $this->db->query("SELECT * FROM `cash_cheque_reward` where user_id='$user_id' ");
                
                if($query->num_rows()>0)
                {
                foreach ($query->result_array() as $row) {
                        
                    $cash_cheque_id=$row['cash_cheque_id'];
        
                    $query = $this->db->query("SELECT * FROM `cash_cheque_master` where id='$cash_cheque_id' ");
                    $row1=$query->row_array();
        
    
                    $vendor_name=$row1['vendor_name'];
                     $vendor_id=$row1['vendor_id'];
                     $txn_id=$row['txn_reward'];
                   $expiry_date=$row['expiry_date'];
                   $reward_amount=$row['reward_amount'];
                   $creation_date=$row['creation_date'];
                   $transaction_amount=$row['transaction_amount'];
                    if($date > $expiry_date){
                       
                       $status='Expired'; 
                        
                    }else{
                        
                       $status=$expiry_date;  
                    }
                   
                   
              

                 
        
                    $resultpost[] = array
                        (
                            "Brand_image"=>"",
                            "vendor_name"=>$vendor_name,
                            "expired_status"=>$status,
                            "vendor_id"=>$vendor_id,
                            "txn_id"=>$txn_id,
                            "creation_date"=>$creation_date,
                            "reward_amount"=>$reward_amount,
                            "paid_amount"=>$transaction_amount
                     
                          
                        );
                }
        
                           return $resultpost;
 
    	    } 
    	    else
    	    {
    	      return  $resultpost; 
    	    }
    
    }
    
    
     public function get_total_bachat($user_id)
    {
   /* $this->load->model('All_booking_model');
        $resultpost = array();
        
        date_default_timezone_set('Asia/Kolkata');
        $resultpost =array();
       $date=date('Y-m-d H:i:s');
        
      
                        $query = $this->db->query("SELECT user_order.*,medical_stores.medical_name,medical_stores.profile_pic FROM user_order LEFT JOIN medical_stores on user_order.listing_id=medical_stores.user_id WHERE (user_order.listing_type='13' or user_order.listing_type='38' ) AND user_order.user_id='$user_id' group by user_order.invoice_no order by user_order.order_date DESC   ");
                        $count = $query->num_rows();
                        if ($count > 0) 
                           { 
            foreach ($query->result_array() as $row) {
                
                $order_id = $row['order_id'];
                $order_type = $row['order_type'];
                $delivery_time = $row['delivery_time'];
                $listing_id = $row['listing_id'];
                $listing_name = $row['listing_name'];
                $listing_type = $row['listing_type'];
                $invoice_no = $row['invoice_no'];
                $chat_id = $row['chat_id'];
                $address_id = $row['address_id'];
                $name = $row['name'];
                $mobile = $row['mobile'];
                $pincode = $row['pincode'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $landmark = $row['landmark'];
                $city = $row['city'];
                $state = $row['state'];
                $action_by = $row['action_by'];
                $payment_method = $row['payment_method'];
                $order_date = $row['order_date'];
                $order_date = date('l j M Y h:i A', strtotime($order_date));
                $delivery_charge = $row['delivery_charge'];
                $order_status = $row['order_status'];
                $order_type = $row['order_type'];
                $action_by = $row['action_by'];
                $rxId = $row['rxId'];
               //added by zak for maintain medlife cancel order 
                  if ($row['profile_pic'] != '') {
                    $profile_pic = $row['profile_pic'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                }
                  $is_cancel = 'false';
                  $orderId="";
                if($rxId != 'NULL')
                {
                    $query = $this->db->query("select orderState,orderId from medlife_order where rxID='$rxId' order by id desc");
                $medlife_order =  $query->row();
                $medlife_order_num =  $query->num_rows();
                // = $medlife_order->orderState;
                 //    $medlife_order= $this->db->select('orderState')->from('medlife_order')->where('rxID', $rxId)->get()->row();
                 if($medlife_order_num>0)
                 {
                     $medlife_status = $medlife_order->orderState;
                     $order_status = $medlife_order->orderState;
                      $orderId = $medlife_order->orderId;
                     if($medlife_status == 'Received')
                     {
                         $is_cancel = 'true';
                     }
                     else
                     {
                         $is_cancel = 'false';
                     }
                 }
                 else
                 {
                     $is_cancel = 'false';
                 }
                }
                
                
               if ($action_by == 'vendor') {
                    $cancel_reason = $row['cancel_reason'];
                } else {
                    $cancel_reason = $row['cancel_reason'];
                }
                $user_info = $this->db->query("SELECT name,phone FROM users WHERE id='$user_id'");
                $getuser_info = $user_info->row_array();
                $user_name = $getuser_info['name'];
                $user_mobile = $getuser_info['phone'];
              
                $product_resultpost  = array();
               
                 $query1 = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where invoice_no='$invoice_no'");
                  $count1 = $query1->num_rows();
                  $order_total=0;
                  $sub_total_sum2=0;
                  $sub_total_sum1=0;
                  $sub_total_discount=0;
                  $sub_total_discount1=0;
                  if ($count1 > 0) {
            foreach ($query1->result_array() as $row1) {
                    $order_id1            = $row1['order_id'];
                    $product_query = $this->db->query("select id as product_order_id,product_id,product_discount,product_name,product_unit,product_unit_value,product_img,product_quantity,product_price,sub_total,product_status,product_status_type,product_status_value,order_status from user_order_product where order_id='$order_id1' order by product_order_id asc");
                    $product_count = $product_query->num_rows();
                    if ($product_count > 0) {
                        foreach ($product_query->result_array() as $product_row) {
                           
                            $product_order_id     = $product_row['product_order_id'];
                            $product_id           = $product_row['product_id'];
                            $product_name         = $product_row['product_name'];
                            $product_img          = $product_row['product_img'];
                            $product_price        = $product_row['product_price'];
                            $product_unit         = $product_row['product_unit'];
                            $product_unit_value   = $product_row['product_unit_value'];
                            $product_quantity     = $product_row['product_quantity'];
                            $product_discount     = $product_row['product_discount'];
                            $sub_total            = $product_row['sub_total'];
                            $product_status       = $product_row['product_status'];
                            $product_status_type  = $product_row['product_status_type'];
                            $product_status_value = $product_row['product_status_value'];
                            $product_order_status = $product_row['order_status'];
                            
                            $sub_total_sum1      += $product_price * $product_quantity;
                            $disc = (($product_price * $product_quantity)*$product_discount)/100;
                            $sub_total_discount +=$disc;
                            $product_resultpost[] = array(
                                "order_id" => $order_id1,
                                "product_order_id" => $product_order_id,
                                "product_id" => $product_id,
                                "product_name" => $product_name,
                                "product_img" => $product_img,
                                "product_quantity" => $product_quantity,
                                "product_price" => $product_price,
                                "product_unit" => $product_unit,
                                "product_unit_value" => $product_unit_value,
                                "product_discount" => $disc,
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status
                            );
                        }
                    }
              
                    
                        
                    }
                
                   
            }
      }
$order_total=$sub_total_sum1+$sub_total_sum2;
$order_total_discount=$sub_total_discount+$sub_total_discount1;
if($order_total_discount=="")
{
    $order_total_discount=0;
}
else
{
    $order_total_discount;
}

if($order_status!="Awaiting Confirmation")
{
    $user_info_user = $this->db->query("SELECT medical_name,payment_type FROM medical_stores WHERE user_id='$listing_id' or pharmacy_branch_user_id='$listing_id'");
    $getuser_info_user = $user_info_user->row_array();    
   if($listing_name=="Instant Order")
{
   $listing_name= "Instant Order";
}
elseif($listing_name=="Favourite Pharmacy")
{
   $listing_name= "Favourite Pharmacy"; 
}
else
{
  $listing_name=$getuser_info_user['medical_name'];
}
   if($getuser_info_user['payment_type']!=null || !empty($getuser_info_user['payment_type']))
   {
   $listing_paymode=$getuser_info_user['payment_type'];
   }
   else
   {
       $listing_paymode="Cash On Delivery";
   }
}
else
{
  
   $listing_paymode="Cash On Delivery";  
}
if($listing_type=="38")
{
   $listing_name="Medlife"; 
}
else
{
    $listing_name;
}
if($rxId == "")
{
    $rxId="";
}
else
{
    $rxId;
}
           
            
             if($listing_type=="38")
               {
                   if(!empty($rxId) )
                {
                $resultpost4[] = array(
                    "order_id" => $order_id,
                    "medlife_order_id" => $orderId,
                    "delivery_time" => str_replace('null', '', $delivery_time),
                    "order_type" => $order_type,
                    "listing_id" => $listing_id,
                    "listing_name" => $listing_name,
                    "listing_type" => $listing_type,
                    "listing_payment_mode" => $listing_paymode,
                    "invoice_no" => $invoice_no,
                    "chat_id" => $chat_id,
                    "address_id" => $address_id,
                    "name" => $name,
                    "mobile" => $mobile,
                    "pincode" => $pincode,
                    "address1" => $address1,
                    "address2" => $address2,
                    "landmark" => $landmark,
                    "city" => $city,
                    "state" => $state,
                    "user_name" => $user_name,
                    "user_mobile" => $user_mobile,
                    "user_email" => "",
                    "order_total" => $order_total,
                    "order_discount"=>$order_total_discount,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "image"=>$profile_pic,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge,
                    "product_order" => $product_resultpost,
                   
                
                    
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel,
                    "life_qty"=>"",
                    "urgent"=>"",
                    "image"=>$profile_pic
                );
                }
                
               }
               else
               {
                   $resultpost4[] = array(
                    "order_id" => $order_id,
                    "medlife_order_id" => $orderId,
                    "delivery_time" => str_replace('null', '', $delivery_time),
                    "order_type" => $order_type,
                    "listing_id" => $listing_id,
                    "listing_name" => $listing_name,
                    "listing_type" => $listing_type,
                    "listing_payment_mode" => $listing_paymode,
                    "invoice_no" => $invoice_no,
                    "chat_id" => $chat_id,
                    "address_id" => $address_id,
                    "name" => $name,
                    "mobile" => $mobile,
                    "pincode" => $pincode,
                    "address1" => $address1,
                    "address2" => $address2,
                    "landmark" => $landmark,
                    "city" => $city,
                    "state" => $state,
                    "user_name" => $user_name,
                    "user_mobile" => $user_mobile,
                    "user_email" => "",
                    "order_total" => $order_total,
                    "order_discount"=>$order_total_discount,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                    "image"=>$profile_pic,
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge,
                    "product_order" => $product_resultpost,
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel,
                    "life_qty"=>"",
                    "urgent"=>"",
                    "image"=>$profile_pic
                ); 
               }
            }
        
                        else 
                        {
                            
                            
                            $resultpost4 = array();
                        }
                        
                        $query1 = $this->db->query("SELECT * from life_saving_drugs where user_id='$user_id' order by created_at DESC ");
                         $count1 = $query1->num_rows();
                           if ($count1 > 0)
                              { 
                                foreach ($query1->result_array() as $row) 
                                        {
                                            $member_id = $row['member_id'];
                                            $qty = $row['qty'];
                                            $urgent = $row['urgent'];
                                            $mobile = $row['mobile'];
                                            $email = $row['email'];
                                            $image = $row['image'];
                                            $invoice_no = $row['invoice_no'];
                                            $order_status = $row['order_status'];
                                            $action_by = $row['action_by'];
                                            $updated_at = $row['created_at'];
                                            $cancel_reason = $row['cancel_reason'];
                                            if(empty($cancel_reason))
                                            {
                                                $cancel_reason='';
                                            }
                                            if(empty($updated_at))
                                            {
                                                $updated_at='';
                                            }
                
                                            $query1 = $this->db->query("SELECT * from users where id='$member_id'");
                                            $row1=$query1->row_array();
                                                if(empty($manufacturer))
                                                {
                                                    $manufacturer="";
                                                }
                                                if(empty($mrp))
                                                {
                                                    $mrp="";
                                                }
                                        
                                            $order_date = date('l j M Y h:i A', strtotime($updated_at));
                                            $name=$row1['name'];
                                             if(empty($name))
                                             {
                                                 $name1='';
                                             }
                                             else
                                             {
                                                 $name1=$name;
                                             }
                                            $resultpost5[] = array(
                                               "order_id" => $invoice_no,
                                                "medlife_order_id" => "",
                                                "delivery_time" => "",
                                                "order_type" => "Life Saving Drug",
                                                "listing_id" => "",
                                                "listing_name" => "",
                                                "listing_type" => "45",
                                                "listing_payment_mode" => "",
                                                "invoice_no" => $invoice_no,
                                                "chat_id" => "",
                                                "address_id" => "",
                                                "name" => $name1,
                                                "mobile" => $mobile,
                                                "pincode" => "",
                                                "address1" => "",
                                                "address2" => "",
                                                "landmark" => "",
                                                "city" => "",
                                                "state" => "",
                                                "user_name" => $name1,
                                                "user_mobile" => $mobile,
                                                "user_email" => $email,
                                                "order_total" => 0,
                                                "order_discount"=>0,
                                                "payment_method" => "",
                                                "order_date" => $order_date,
                                                "order_status" => $order_status,
                                                "cancel_reason" => $cancel_reason,
                                                "delivery_charge" => "",
                                                "product_order" => array(),
                                                "tracker" => array(),
                                                "prescription_create" => array(),
                                                "prescription_order" => array(),
                                                "action_by" => "",
                                                "rxid" => "",
                                                "is_cancel" => "",
                                                "profile_img"=>"https://d2c8oti4is0ms3.cloudfront.net/images/Check_In_Image/".$image,
                                                "life_qty" => $qty,
                                                "image" => "https://d2c8oti4is0ms3.cloudfront.net/images/Check_In_Image/".$image,
                                                "urgent"=>$urgent
                                            );
                                        }
                              }
                            else
                            {
                                $resultpost5 = array();
                            }
                            
                            
                           
                         $query22 = $this->db->query("SELECT * FROM user_order WHERE user_id='$user_id' and listing_type='44' group by invoice_no order by order_id DESC");
                         $count22 = $query22->num_rows(); 
                        
                        if ($count22 > 0) 
                           { 
            foreach ($query22->result_array() as $row22) {
                
                $order_id = $row22['order_id'];
                $order_type = $row22['order_type'];
                $delivery_time = $row22['delivery_time'];
               
                $listing_type = $row22['listing_type'];
                $invoice_no = $row22['invoice_no'];
               
                $address_id = $row22['address_id'];
                $name = $row22['name'];
                $mobile = $row22['mobile'];
                $pincode = $row22['pincode'];
                $address1 = $row22['address1'];
                $address2 = $row22['address2'];
                $landmark = $row22['landmark'];
                $city = $row22['city'];
                $state = $row22['state'];
                $action_by = $row22['action_by'];
                $payment_method = $row22['payment_method'];
                $order_date = $row22['order_date'];
                $order_date = date('l j M Y h:i A', strtotime($order_date));
               
                $delivery_charge = $row22['delivery_charge'];
                $order_status = $row22['order_status'];
                $order_type = $row22['order_type'];
                $action_by = $row22['action_by'];
                $rxId = $row22['rxId'];
                
              
               //added by zak for maintain medlife cancel order 
                  $is_cancel = 'false';
                  $orderId="";
                if($rxId != 'NULL')
                {
                    $query = $this->db->query("select orderState,orderId from medlife_order where rxID='$rxId' order by id desc");
                $medlife_order =  $query->row();
                $medlife_order_num =  $query->num_rows();
                // = $medlife_order->orderState;
                 //    $medlife_order= $this->db->select('orderState')->from('medlife_order')->where('rxID', $rxId)->get()->row();
                 if($medlife_order_num>0)
                 {
                     $medlife_status = $medlife_order->orderState;
                     $order_status = $medlife_order->orderState;
                      $orderId = $medlife_order->orderId;
                     if($medlife_status == 'Received')
                     {
                         $is_cancel = 'true';
                     }
                     else
                     {
                         $is_cancel = 'false';
                     }
                 }
                 else
                 {
                     $is_cancel = 'false';
                 }
                }
                
                
               if ($action_by == 'vendor') {
                    $cancel_reason = $row22['cancel_reason'];
                } else {
                    $cancel_reason = $row22['cancel_reason'];
                }
                $user_info = $this->db->query("SELECT name,phone FROM users WHERE id='$user_id'");
                $getuser_info = $user_info->row_array();
                $user_name = $getuser_info['name'];
                $user_mobile = $getuser_info['phone'];
               
                $product_resultpost  = array();
               
                 $query1 = $this->db->query("select user_id,order_id,order_type,action_by,delivery_time,cancel_reason,action_by,listing_id,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where invoice_no='$invoice_no'");
                  $count1 = $query1->num_rows();
                  $order_total=0;
                  $sub_total_sum2=0;
                  $sub_total_sum1=0;
                  $sub_total_discount=0;
                  $sub_total_discount1=0;
                  if ($count1 > 0) {
            foreach ($query1->result_array() as $row1) {
                    $order_id1            = $row1['order_id'];
                    $product_query = $this->db->query("select id as product_order_id,product_id,product_discount,product_name,product_unit,product_unit_value,product_img,product_quantity,product_price,sub_total,product_status,product_status_type,product_status_value,order_status from user_order_product where order_id='$order_id1' order by product_order_id asc");
                    $product_count = $product_query->num_rows();
                    if ($product_count > 0) {
                        foreach ($product_query->result_array() as $product_row) {
                           
                            $product_order_id     = $product_row['product_order_id'];
                            $product_id           = $product_row['product_id'];
                            $product_name         = $product_row['product_name'];
                            $product_img          = $product_row['product_img'];
                            $product_price        = $product_row['product_price'];
                            $product_unit         = $product_row['product_unit'];
                            $product_unit_value   = $product_row['product_unit_value'];
                            $product_quantity     = $product_row['product_quantity'];
                            $product_discount     = $product_row['product_discount'];
                            $sub_total            = $product_row['sub_total'];
                            $product_status       = $product_row['product_status'];
                            $product_status_type  = $product_row['product_status_type'];
                            $product_status_value = $product_row['product_status_value'];
                            $product_order_status = $product_row['order_status'];
                            
                            $sub_total_sum1      += $product_price * $product_quantity;
                          
                            $desc = (($product_price * $product_quantity)*$product_discount)/100;
                            $sub_total_discount +=$desc;
                           
                            $product_resultpost[] = array(
                                "order_id" => $order_id1,
                                "product_order_id" => $product_order_id,
                                "product_id" => $product_id,
                                "product_name" => $product_name,
                                "product_img" => $product_img,
                                "product_quantity" => $product_quantity,
                                "product_price" => $product_price,
                                "product_unit" => $product_unit,
                                "product_unit_value" => $product_unit_value,
                                "product_discount" => $desc,
                                "sub_total" => $sub_total,
                                "product_status" => $product_status,
                                "product_status_type" => $product_status_type,
                                "product_status_value" => $product_status_value,
                                "product_order_status" => $product_order_status
                            );
                        }
                    }
              
                   
                        
                        
                     
                        
                    }
                
                   
            }
      }
$order_total=$sub_total_sum1+$sub_total_sum2;
$order_total_discount=$sub_total_discount+$sub_total_discount1;
if($order_total_discount=="")
{
    $order_total_discount=0;
}
else
{
    $order_total_discount;
}

if($order_status!="Awaiting Confirmation")
{
    $listing_paymode="Cash On Delivery";  
}
else
{
  
   $listing_paymode="Cash On Delivery";  
}

if($rxId == "")
{
    $rxId="";
}
else
{
    $rxId;
}
            
            
           $resultpost99[] = array(
                    "order_id" => $order_id,
                    "medlife_order_id" => $orderId,
                    "delivery_time" => str_replace('null', '', $delivery_time), //- swap
                    "order_type" => $order_type,
                    "listing_id" => "",
                    "listing_name" => "Night Owl",
                    "listing_type" => "44",
                    "listing_payment_mode" => $listing_paymode,
                    "invoice_no" => $invoice_no,
                    "chat_id" => "",
                    "address_id" => $address_id,
                    "name" => $name,
                    "mobile" => $mobile,
                    "pincode" => $pincode,
                    "address1" => $address1,
                    "address2" => $address2,
                    "landmark" => $landmark,
                    "city" => $city,
                    "state" => $state,
                    "user_name" => $user_name,
                    "user_mobile" => $user_mobile,
                    "user_email" => "",
                    "order_total" => $order_total,
                    "order_discount"=>$order_total_discount,
                    "payment_method" => $payment_method,
                    "order_date" => $order_date,
                   
                    "order_status" => $order_status,
                    "cancel_reason" => $cancel_reason,
                    "delivery_charge" => $delivery_charge, //- swap
                    "product_order" => $product_resultpost,
                    
                    
                   
                    "action_by" => $action_by,
                    "rxid" => $rxId,
                    "is_cancel" => $is_cancel,
                    "profile_img"=>"",
                    "image"=>"",
                    "life_qty"=>"",
                    "urgent"=>"",
                ); 
            }
               
                                
                          else
                            {
                                $resultpost99 = array();
                            }      
                           
                             
                            
                            $resultpost=array_merge($resultpost4,$resultpost5,$resultpost99);
                            function cmp($a, $b)
{
    $aDateTime = new DateTime($a["order_date"]);
    $bDateTime = new DateTime($b["order_date"]);

    return $aDateTime < $bDateTime ? 1 : -1;
};

    usort($resultpost, "cmp");
return $resultpost;*/
                    
        
        
               $query = $this->db->query("SELECT invoice_no,discount,reward_amount,cash_cheque_reward.status,used_amount FROM `user_order` join cash_cheque_reward on user_order.invoice_no=cash_cheque_reward.txn_reward where user_order.user_id='$user_id'");
                
                if($query->num_rows()>0)
                {
                    $adddis="";
                foreach ($query->result_array() as $row) {
                        
                    $invoice_no=$row['invoice_no'];
                    $discount=$row['discount'];
                    $reward_amount=$row['reward_amount'];
                    $status=$row['status'];
                    $used_amount=$row['used_amount'];
                    
                        if($status!="1"){
                             $adddis=$discount+$used_amount;
                        }else{
                         $adddis+=$discount;
                        }
                            $resultpost =array(
                    'total_of_bachat'=>$adddis
                                        );
                }
                    
        
               return $resultpost;
 
    	    } 
    	    else
    	    {
    	      return  $resultpost; 
    	    }
    
    }
    
    
      public function coupon_select($user_id) 
   {
        
        
        $sql = "SELECT * FROM coupon_select";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count>0)
          {
            foreach ($query->result_array() as $row) 
                    {
                      $id=$row['id'];    
                      $image=$row['image'];
                      $name=$row['name'];
                      $info=$row['info'];
                      $type=$row['type'];

                        
                         $resultpost[] = array('id'=> $id,
                                                 'image' => $image,
                                                 'name' => $name,
                                                 'type'=>$type,
                                                 'Discount'=>$info,
                                                 'user_id'=>$user_id
                                     ); 
                        
                      
                     
                    }
                    
          }
          else
          {
             $resultpost=array(); 
            
          }
        
        
          return $resultpost;
       
   } 
    
    
    
    
    
}