<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PartnercouponModel extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key       = "medicalwalerestapi";

    public function check_auth_client(){
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key  = $this->input->get_request_header('Auth-Key', TRUE);
        if($client_service == $this->client_service && $auth_key == $this->auth_key){
            return true;
        } else {
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        }
    }


    public function auth(){
        date_default_timezone_set('Asia/Kolkata');
        $users_id  = $this->input->get_request_header('User-ID', TRUE);
        $token     = $this->input->get_request_header('Authorizations', TRUE);
        $q  = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id',$users_id)->where('token',$token)->get()->row();
        if($q == ""){
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        } else {
            if($q->expired_at < date('Y-m-d H:i:s')){
                return json_output(401,array('status' => 401,'message' => 'Your session has been expired.'));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = date("Y-m-d H:i:s", strtotime('+12 hours')); 
                $this->db->where('users_id',$users_id)->where('token',$token)->update('api_users_authentication',array('expired_at' => $expired_at,'updated_at' => $updated_at));
                return array('status' => 200,'message' => 'Authorized.');
            }
        }
    }
    
    public function vendor_get_users($vendor_id){
      
        // $get_value =  $this->db->query("SELECT * FROM `barcode_coupon` where vendor_id = '$vendor_id' AND user_id = '$user_id' AND coupon = '$coupon' AND status = 'active'");
        $get_value =  $this->db->query("SELECT * FROM `barcode_coupon` where vendor_id = '$vendor_id' AND status = 'active'");
        $data = array();
        $count = $get_value->conn_id->affected_rows;
    //   echo $count;
       if($count > 0 ){
           foreach($get_value->result_array() as $row){
               
            //   print_r($row); die();
               $user_id = $row['user_id'];
               $coupon = $row['coupon'];
            $userInfo =  $this->db->query("SELECT * FROM `users` where id = '$user_id'");
        
           $userDataCount = $userInfo->num_rows();
           foreach($userInfo->result_array() as $user){
               $userName = $user['name'];
           }
           
            $data_all = array(
                'vendor_id' => $vendor_id,
                'user_id' => $user_id,
                'coupon' => $coupon,
                'user_name' => $userName
            );
            
            $data[] = $data_all;
            
           }
           
            $resp = array(
                'status' => 200,
                'message' => 'success', 
                'data' => $data
            );
           
       } else {
            $resp = array(
                'status' => 400,
                'message' => 'No coupon code found' 
            );
       }
       return $resp;
    
    }
    
    
    
    
     public function get_carddetail(){
      
        // $get_value =  $this->db->query("SELECT * FROM `barcode_coupon` where vendor_id = '$vendor_id' AND user_id = '$user_id' AND coupon = '$coupon' AND status = 'active'");
        $get_value =  $this->db->query("SELECT * FROM `cardDetails` where p_cardid = '0' or card_id = '1'");
        
        $count = $get_value->conn_id->affected_rows;
       
       if($count > 0 ){
           foreach($get_value->result_array() as $row){
               
            //   print_r($row); die();
               $card_id = $row['card_id'];
               $card_name = $row['card_name'];
          
           
           
         
            $get_valuem =  $this->db->query("SELECT * FROM `cardDetails` where p_cardid = '$card_id'");
            $countm = $get_valuem->conn_id->affected_rows;
             if($countm > 0 ){
           foreach($get_valuem->result_array() as $rowm){
               $card_sub_name = $rowm['card_name'];
               $card_sub_id = $rowm['card_id'];
               $detail[]=array(
                'card_id' => $card_sub_id,
                'card_name' => $card_sub_name
                
            );
               
           }
             }
                $data_all = array(
                'card_id'=>$card_id,
                $card_name=>$detail
            );
            
              
            $data[]= $data_all;
            $detail=array();
            $data_all=array();
           }
           
            $resp = array(
                'status' => 200,
                'message' => 'success', 
                'data' => $data
            );
           
       } else {
            $resp = array(
                'status' => 400,
                'message' => 'No card found' 
            );
       }
       return $resp;
    
    }
    
    
    public function vender_discountlimit_row($vendor_id)
    {
        
       
         $sql       = "SELECT `discount_limit` FROM `vendor_discount` WHERE `vendor_id`=$vendor_id";
        $result    = $this->db->query($sql)->row();
        return $result;
        
    }
    
    
     public function vender_discount_row($vendor_id)
    {
        
        
        $sql       = "SELECT `discount_min`,`discount_max`, `discount_type`,`discount_limit` FROM `vendor_discount` WHERE `vendor_id`=$vendor_id";
        $result    = $this->db->query($sql)->row();
        return $result;
        
    }
    
    
      public function vender_discounttype_row($vendor_id)
    {
        
        
        $sql       = "SELECT `discount_type` FROM `vendor_discount` WHERE `vendor_id`=$vendor_id";
        $result    = $this->db->query($sql)->row();
        return $result;
        
    }
    
    
    public function  barcodecoupon($coupon)
    {
        $query=$this->db->select('*')
            ->from('barcode_coupon')
            ->where('coupon',$coupon)
            ->get();
        return $query->result();
    }
    
     public function coupon_code_validation ($userid,$id,$coupon_code)
    {
  
         $sql = "SELECT `status` FROM `barcode_coupon` WHERE user_id='$userid' AND vendor_id = '$id' AND coupon= '$coupon_code'";
         $result = $this->db->query($sql)->row()->status;
        return $result;
    }
     public function addcustomercoupon($data, $data_point, $data_credit)
    {
        
        $this->db->insert('user_ledger', $data_credit);
        $this->db->insert('user_ledger', $data);
        $this->db->insert('user_ledger', $data_point);
        
        
        
        if ($this->db->affected_rows() > 0) {
            return $this->db->affected_rows();
        } else {
            return null;
        }
    }
    
    
    
     public function customer_Coupon_varification($RGBNumber,$vendor_id)
    {
           
           
            $is_available=$this->get_availability_card($RGBNumber);
        
           if($is_available > 0)
           {
              $sql = "SELECT `user_id` FROM `user_privilage_card` WHERE card_no='$RGBNumber'";
              $result = $this->db->query($sql)->row();
              $card_holder_id = $result->user_id;
              
              $sql = "SELECT * FROM `barcode_coupon` WHERE user_id = '$card_holder_id' AND vendor_id='$vendor_id' and status='active'";
              $count = $this->db->query($sql)->num_rows();
              $result = $this->db->query($sql)->row(); 
              
              $getdiscount = $this->vender_discount_row($vendor_id);
              $getdiscountlimit = $this->vender_discountlimit_row($vendor_id);
              
              $getdiccounttype = $this->vender_discounttype_row($vendor_id);
              $getdiccounttype = $getdiccounttype->discount_type;
              
              if($getdiccounttype == "percent")
                   {
                     $getdiccounttype = " % ";
                   } 
              else if($getdiccounttype == "rupee")
                  {
                      $getdiccounttype = " Rs ";
                  }
                  
              $getmaxdiscount = $getdiscount->discount_max;
              $getmindiscount = $getdiscount->discount_min;
              $getlimitdiscount = $getdiscount->discount_limit;
              
                 if($count<=0)
                 {
                  
                    $couponcode = $this->generate_coupons();
                    $data = $couponcode;
                    $data1 = array(
                        'coupon'=>$couponcode,
                        'status' =>'active',
                        'vendor_id' =>$vendor_id,
                        'user_id' =>$card_holder_id,
                        );
                     
                    $this->db->insert('barcode_coupon',$data1);
                   
                 }
                 else
                 {
                 $old_coupon_code = $result->coupon;
                 $data = $old_coupon_code;
                 
                 } 
              
                 return $data;
              
              
              
              
           }
                
           else{
               
               return 0;
               
           }
     
    } 
    
    
     public function generate_coupons()
      {
        $coupon = "";
        for($counter = 0; $counter < 4; $counter++){
           
            $coupon .= chr(random_int(65,90));
        }
       
        $coupon .= time();
        return $coupon;
    }
    
    
    
    public function Vendor_initiate($vendor_id,$userid,$card_type,$card_sub_type,$carditdetails,$vendor_comment,$total_amount,$RGBNumber,$discount,$generated_code)
    {
         $finaldiscount ="";
        $trans_mode ="";
        $transaction_sub_type ="";
         $finaldiscount=0;
         
        if($card_type == "card")
            {
                if($card_sub_type == 2)
                   {
                        $trans_mode = $card_sub_type;
                        $transaction_sub_type = $carditdetails;
                    } 
            }
        else if($card_type == "wallet")
            {
                $trans_mode = 1;
                $transaction_sub_type = $carditdetails;
            } 
	   else if($card_type == "cash")
	        {
                $trans_mode = 3;
                $transaction_sub_type = null;
            } 
        
        $sql = "SELECT * FROM `user_privilage_card` WHERE card_no='$RGBNumber'";
        $result = $this->db->query($sql)->row();        
        if($result->is_active == '0')
        {
             $resp = array('status' => 400, 'message' => 'Please Activate Your Bachat Card');
                          
            return  $resp;             
                          
        }    
        else
        {
              $userid = $result->user_id;
              $getdiscountlimit = $this->vender_discountlimit_row($vendor_id);
              $dis=$getdiscountlimit->discount_limit;
              if(empty($dis) or $dis=='')
                {
                    $amount=$total_amount;
                    $totaldiscount= ($amount) * ($discount / 100); 
                    $finaldiscount=$totaldiscount;
                }
               else
                {
                    $data['getmaxdiscount'] = $this->vender_discount_row($vendor_id);
                    $getdiccounttype = $this->vender_discounttype_row($vendor_id);
                    if($getdiccounttype =='rupee')
                        {
                            $totaldiscount= ($total_amount) - ($discount);
                            $finaldiscount=$totaldiscount;
                            if($totaldiscount > $getdiscountlimit->discount_limit)
                               {
                                   $finaldiscount=$getdiscountlimit->discount_limit;
                               }
                            elseif($totaldiscount<= $getdiscountlimit->discount_limit)
                               {
                                  $finaldiscount=$totaldiscount;
                               }
                                   
                        }
                    else
                        { 
                           $totaldiscount= ($total_amount) * ($discount / 100); 
                           if($totaldiscount> $getdiscountlimit->discount_limit)
                                {
                                    $finaldiscount= $getdiscountlimit->discount_limit;
                                }
                            elseif($totaldiscount<= $getdiscountlimit->discount_limit)
                                     {
                                          $finaldiscount=$totaldiscount;
                                     }
                                     
                        }
                }
                $trans_type=1;
                $trans_time=date('Y-m-d H:i:s');
                $randnumdebit = rand(1111111111,9999999999);
                $booking_id           = date("YmdHis");
                $vendor_comment = $vendor_comment;
                $data = array(
                        'user_id'=>$userid,
                        'listing_id'=>$vendor_id,
                        'trans_id'=>$randnumdebit,
                        'order_id'=> $booking_id,
                        'trans_type'=>$trans_type,
                        'trans_time'=>$trans_time,
                        'amount'=>$total_amount,
                        'discount'=>$discount,
                        'amount_saved'=>$finaldiscount,
                        'discount_rupee'=>$finaldiscount,
                        'vendor_category'=>'13',
                        'authenticate' =>'1',
                        'trans_mode' => $trans_mode,
                        'transaction_sub_type' => $transaction_sub_type,
                        'authenticate' => 1,
                        'vendor_comment' => $vendor_comment
                        );
                        $trans_id = $randnumdebit;   
                
                $trans_point_type=4;
                $randnum = rand(1111111111,9999999999);
                $data_point = array(
                                    'user_id'=>$userid,
                                    'listing_id'=>$vendor_id,
                                    'order_id' => $booking_id,
                                    'trans_id'=>$randnum,
                                    'trans_type'=>$trans_point_type,
                                    'trans_time'=>$trans_time,
                                    'amount'=>$total_amount,
                                    'discount'=>$discount,
                                    'amount_saved'=>$finaldiscount,
                                    'discount_rupee'=>$finaldiscount,
                                    'vendor_category'=>'13',
                                    'authenticate' =>'1',
                                    'trans_mode' => $trans_mode,
                                    'transaction_sub_type'=> $transaction_sub_type   
                                   );
                $randnumdebit = rand(1111111111,9999999999);
                $trans_point_type=0;
                $data_credit = array(
                                     'user_id'=>$userid,
                                    'listing_id'=>$vendor_id,
                                    'order_id' => $booking_id,
                                    'trans_id'=>$randnumdebit,
                                    'trans_type'=>$trans_point_type,
                                    'trans_time'=>$trans_time,
                                    'amount'=>$total_amount,
                                    'discount'=>$discount,
                                    'amount_saved'=>$finaldiscount,
                                    'discount_rupee'=>$finaldiscount,
                                    'vendor_category'=>'13',
                                    'authenticate' =>'1',
                                    'trans_mode' => $trans_mode,
                                   'transaction_sub_type'=> $transaction_sub_type
                                    );
                $addcustomercoupon = $this->addcustomercoupon($data,$data_point,$data_credit);
                $upadte_user_points = array(
                            'user_id'        => $userid,
                            'order_id'       => $booking_id,
                            'trans_id'       => $randnum,
                            'points'         => $totaldiscount,
                            'created_at'     => $trans_time,
                            'expire_at'      => $trans_time,    
                            'status'         => 'active'
                        );
               $this->db->insert('user_points', $upadte_user_points);
               $user_detail = $this->db->query("SELECT phone,name FROM users WHERE id='$userid'");
               $getusrdetails = $user_detail->row_array();
               $phone = $getusrdetails['phone'];
               $user_name = $getusrdetails['name'];
               $message = $user_name . ', Congratulations you saved  :' . $finaldiscount . ' With Discount:' . $discount . 'And transaction id :'. $randnum;
               $post_data = array(
                                    'From' => '02233721563',
                                    'To' => $phone,
                                    'Body' => $message
                                  );
                if($discount > 0)
                    {
                        $exotel_sid = "aegishealthsolutions";
                        $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                        $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_VERBOSE, 1);
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                        $http_result = curl_exec($ch);
                        curl_close($ch);
                    }
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$userid'");
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id',$userid)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $userid)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                  
                $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', Congratulations you saved  :' . $finaldiscount . ' With Discount:' . $discount . 'And transaction id :'. $randnum;
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$userid'");
                $title = $usr_name . ', Congratulations you saved  :' . $finaldiscount . ' With Discount:' . $discount . 'And transaction id :'. $randnum;
                $customer_token_count = $customer_token->num_rows();
                
               
                if($discount > 0)
                {
                    if ($customer_token_count > 0) {
                        $token_status = $customer_token->row_array();
                        $agent = $token_status['agent'];
                        $reg_id = $token_status['token'];
                        $img_url = $userimage;
                        $tag = 'text';
                        $key_count = '1';
                        $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent,$discount,$trans_id,$getdiccounttype);
                    }
                }
                if($addcustomercoupon)
                        {
                         
                        $data1 = array(
                        'user_id'=>$vendor_id,
                        'trans_type'=>$trans_type,
                        'trans_time'=>$trans_time,
                        'trans_id'=>$randnum,
                        'listing_id'=>$userid,
                        'amount'=>$total_amount,
                        'discount'=>$discount,
                        'discount_rupee'=>$finaldiscount);
                     
                          $this->db->insert('vendor_ledger',$data1);
                           
                           $active='active';
                         
                           $data2=array('status'=>$active);
                          $this->db->where('user_id',$userid);
                          $this->db->where('vendor_id',$vendor_id);
                          $this->db->where('coupon',$generated_code);
                          $this->db->update('barcode_coupon',$data2);
                          
                           $resp = array('status' => 200, 'message' => 'Success');
                          
                           return  $resp; 
                         
                        }
                        else
                        {
                          $resp = array('status' => 400, 'message' => 'Data  failed');
                          
                           return  $resp;     
                            
                         
                        }
                
            
        }
    }
    
    public function Vendor_initiate_coupon($vendor_id,$userid,$card_type,$card_sub_type,$carditdetails,$vendor_comment,$total_amount,$coupon_code,$discount)
    {
        $getmaxdiscount = $this->vender_discounttype_row($vendor_id);   
        $is_available= $this->barcodecoupon($coupon_code); 
        $finaldiscount ="";
        $trans_mode ="";
        $transaction_sub_type ="";
         $finaldiscount=0;
         if($card_type == "card")
            {
                if($card_sub_type == 2)
                   {
                        $trans_mode = $card_sub_type;
                        $transaction_sub_type = $carditdetails;
                   } 
            }
            else if($card_type == "wallet")
            {
                $trans_mode = 1;
                $transaction_sub_type = $carditdetails;
            } 
	        else if($card_type == "cash")
	        {
                     $trans_mode = 3;
                     $transaction_sub_type = null;
            }
           if(count($is_available) > 0 )
                   { 
                     $getdiscountlimit = $this->vender_discountlimit_row($vendor_id);
                     $dis=$getdiscountlimit->discount_limit;  
                     if(empty($dis) or $dis=='')
                            {
                                     $amount=$total_amount;
                                     $discount=$discount;
                                     $totaldiscount= ($amount) * ($discount / 100); 
                                     $finaldiscount=$totaldiscount;
                            }
                       else
                          {  
                             $data['getmaxdiscount'] = $this->vender_discount_row($vendor_id);
                              $getdiccounttype = $this->vender_discounttype_row($vendor_id);
                               if($getdiccounttype=='rupee')
                              {
                                 
                                   $amount=$total_amount;
                                   $discount=$discount;
                                   $totaldiscount= ($amount) - ($discount);
                                   
                                   $finaldiscount=$totaldiscount;
                                   
                                   if($totaldiscount > $getdiscountlimit->discount_limit)
                                   {
                                   
                                   $finaldiscount=$getdiscountlimit->discount_limit;
                                   }
                                   elseif($totaldiscount<= $getdiscountlimit->discount_limit)
                                   {
                                      $finaldiscount=$totaldiscount;
                                   }
                                   
                             }
                              else
                             {
                                     $amount=$total_amount;
                                     $discount=$discount;
                                     $totaldiscount= ($amount) * ($discount / 100); 
                                    // $totaldiscount= ($percentage / 100) * $amount;
                                     
                                     if($totaldiscount> $getdiscountlimit->discount_limit)
                                     {
                                         $finaldiscount= $getdiscountlimit->discount_limit;
                                     }
                                     elseif($totaldiscount<= $getdiscountlimit->discount_limit)
                                     {
                                         $finaldiscount=$totaldiscount;
                                     }
                             }
                       
                       
                       
                     
                          }
                           
                          $trans_type=1;
                   $trans_time=date('Y-m-d H:i:s');
                   $randdebit = rand(1111111111,9999999999);
                   $booking_id           = date("YmdHis");
               
                    $data = array(
                    
                        'user_id'=>$userid,
                        'listing_id'=>$vendor_id,
                        'order_id' => $booking_id,
                        'trans_id'=>$randdebit,
                        'trans_type'=>$trans_type,
                        'trans_time'=>$trans_time,
                        'amount'=>$total_amount,
                        'discount'=>$discount,
                        'amount_saved'=>$finaldiscount,
                        'discount_rupee'=>$finaldiscount,
                        'vendor_category'=>13,
                        'authenticate' =>1,
                        'trans_mode' => $trans_mode,
                        'transaction_sub_type'=> $transaction_sub_type,
                        'vendor_comment' => $vendor_comment
                        );
                        
                    //   print_r($data); 
                    
                      //added for point entry 
                        $trans_point_type=4;
                        $randnum = rand(1111111111,9999999999);
                         $data_point = array(
                      //  'user_id'=>$id,
                     //   'listing_id'=>$userid,
                        'user_id'=>$userid,
                        'listing_id'=>$vendor_id,
                        'order_id' => $booking_id,
                        'trans_id'=>$randnum,
                        'trans_type'=>$trans_point_type,
                        'trans_time'=>$trans_time,
                        'amount'=>$total_amount,
                        'discount'=>$discount,
                        'amount_saved'=>$finaldiscount,
                        'discount_rupee'=>$finaldiscount,
                        'vendor_category'=>'13',
                        'authenticate' =>'1',
                        'trans_mode' => $trans_mode,
                        'transaction_sub_type'=> $transaction_sub_type
                        );
                        
                      //added for debit entry
                         $randnumcredit = rand(1111111111,9999999999);
                        
                        $trans_point_type=0;
                         $data_credit = array(
                      //  'user_id'=>$id,
                     //   'listing_id'=>$userid,
                        'user_id'=>$userid,
                        'listing_id'=>$vendor_id,
                        'order_id' => $booking_id,
                        'trans_id'=>$randnumcredit,
                        'trans_type'=>$trans_point_type,
                        'trans_time'=>$trans_time,
                        'amount'=>$total_amount,
                        'discount'=>$discount,
                        'amount_saved'=>$finaldiscount,
                        'discount_rupee'=>$finaldiscount,
                        'vendor_category'=>'13',
                        'authenticate' =>'1',
                        'trans_mode' => $trans_mode,
                        'transaction_sub_type'=> $transaction_sub_type
                        );
                        
                       // print_r($data);die();
                          
                          
                       $status = $this->coupon_code_validation($userid,$vendor_id,$coupon_code); 
                    
                         if($status == 'inactive')
                      {
                         $resp = array('status' => 400, 'message' => 'Coupon Code is Expired'); 
                      }
                       else if ($status== 'active')  //inactive to active for defualt active = ready to use , inactive = expired code and used = code is used
                      {
                        
                         
                        $addcustomercoupon = $this->addcustomercoupon($data,$data_point,$data_credit);
                        $amount=$total_amount;
                        $discount=$discount;
                        $totaldiscount= ($amount) * ($discount / 100); 
                         if($totaldiscount> $getdiscountlimit->discount_limit)
                                     {
                                         $finaldiscount= $getdiscountlimit->discount_limit;
                                         $pointsdis= ($amount) - ($finaldiscount);
                                         
                                     }
                                     elseif($totaldiscount<= $getdiscountlimit->discount_limit)
                                     {
                                         $finaldiscount=$totaldiscount;
                                      
                                          $pointsdis= ($amount) - ($finaldiscount) ;
                                    
                                     }
                                     
                        $upadte_user_points = array(
                            'user_id'        => $userid,
                            'order_id'       => $booking_id,
                            'trans_id'       => $randnum,
                            'points'         =>$pointsdis,
                            'created_at'     => $trans_time,
                            'expire_at'      => $trans_time,    
                            'status'         => 'active'
                        );
                        $this->db->insert('user_points', $upadte_user_points);
                           
                        
                         $trans_id = $randdebit;
                          $user_detail = $this->db->query("SELECT phone,name FROM users WHERE id='$userid'");
                 $getusrdetails = $user_detail->row_array();
                $phone = $getusrdetails['phone'];
                 $user_name = $getusrdetails['name'];
                 $message = $user_name . ', Congratulations you saved  :' . $finaldiscount . ' With Discount:' . $discount . 'And transaction id :'. $trans_id;
                    $post_data = array(
                        'From' => '02233721563',
                        'To' => $phone,
                        'Body' => $message
                    );
                    $exotel_sid = "aegishealthsolutions";
                    $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                    $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_VERBOSE, 1);
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                    $http_result = curl_exec($ch);
                    curl_close($ch);
                 
                 
                 //end
                   //added by jakir on 01-june-2018 for notification on activation 
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$userid'");

            $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id',$userid)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $userid)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                  
                 $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', Congratulations you saved  :' . $finaldiscount . ' With Discount:' . $discount . 'And transaction id :'. $trans_id;
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$userid'");
                $title = $usr_name . ', Congratulations you saved  :' . $finaldiscount . ' With Discount:' . $discount . 'And transaction id :'. $trans_id;
                $customer_token_count = $customer_token->num_rows();
                
               

                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = $token_status['token'];
                    $img_url = $userimage;
                    $tag = 'text';
                    $key_count = '1';
                    $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent,$discount,$trans_id,$getdiccounttype);
                }
              if($addcustomercoupon)
                        {
                            
                    //updated for vander ledger
                            
                        $data1 = array(
                        'user_id'=>$vendor_id,
                        'trans_type'=>$trans_type,
                        'trans_time'=>$trans_time,
                        'trans_id'=>$randnum,
                        'listing_id'=>$userid,
                         'amount'=>$total_amount,
                        'discount'=>$discount,
                        'discount_rupee'=>$finaldiscount);
                     
                        
                          // $this->db->where('user_id',$userid);
                          $this->db->insert('vendor_ledger',$data1);
                           
                           $active='used';
                         
                           $data2=array('status'=>$active);
                          $this->db->where('user_id',$userid);
                          $this->db->where('vendor_id',$vendor_id);
                          $this->db->where('coupon',$coupon_code);
                          $this->db->update('barcode_coupon',$data2);
                          
                         // $resultf = "UPDATE `barcode_coupon` SET `status` = 'active' [WHERE user_id='$id' AND vendor_id = '$vendor_id' AND coupon= '$coupon']";
                          
                           
                         $resp = array('status' => 200, 'message' => 'success');
                          return $resp;
                        }

                         else
                        {
                          $resp = array('status' => 400, 'message' => 'Failed');
                           return $resp;
                        }


                        
                        
                      }
                      
                       else
                      {
                        $resp = array('status' => 400, 'message' => 'Coupon Code is Already Used');
                         return $resp;
                      }
                      
                      
                      
                          
                       
                   }
                                
                    else
                     {
                        $resp = array('status' => 400, 'message' => 'Coupon Code is Invalid');
                        
                        return $resp;
                     }     
                        
                        
                        
                        
                       
                        
                    
                 
        
    }
    
    
   public function get_availability_card($card_no)
    {
        
        $query=$this->db->select('count(*) as counts')
            ->from('user_privilage_card')
            ->where('card_no',$card_no)
            ->where('is_active','1')
            ->get();
           return $query->row()->counts;
        
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
                "discount_type" => $getdiccounttype->discount_type,
                
                
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