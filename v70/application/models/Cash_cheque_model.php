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
                    $branch_id=$row['branch_id'];
                    $discount_type=$row['save_type'];
                    $discount_amount=$row['amount'];
                    $max_usage_day=$row['max_usage_day']; 
                    $min_order=$row['min_order']; 
                    $max_order=$row['max_order']; 
                    $expiry_dayt=$row['expiry_day']; 
                    $creation_date=$row['creation_date']; 
                    $is_active=$row['active'];
                    
                    if($branch_id==null){
                        $branch_id="";
                        
                    }
                        if($discount_type=="percent"){
                         $offer_type="Upto"; 
                        }else{
                            $offer_type="Flat";
                        }
        
                    $resultpost[] = array
                        (   
                            "Brand_image"=>"",
                            "vendor_id"=>$vendor_id,
                            'verndor_branch_id'=>$branch_id,
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
    
    
     public function get_total_bachat($user_id,$page)
    {
         if($page==""){
         $page=1;   
        }
        $radius = $page*5;
        $limit = 10;
      
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
  
    	    $resultpost=array();
    	       // $query = $this->db->query("SELECT invoice_no,discount,reward_amount,cash_cheque_reward.status,used_amount FROM `user_order` join cash_cheque_reward on user_order.invoice_no=cash_cheque_reward.txn_reward where user_order.user_id='$user_id'");
                $query = $this->db->query("SELECT invoice_no,order_date,listing_name,discount,reward_amount,cash_cheque_reward.status,used_amount FROM `user_order` left join cash_cheque_reward on user_order.invoice_no=cash_cheque_reward.txn_reward where user_order.user_id='$user_id' LIMIT $start, $limit"); 
                if($query->num_rows()>0)
                {
               
                $adddis="";
                foreach ($query->result_array() as $row) {
                        
                    $invoice_no=$row['invoice_no'];
                    $discount=$row['discount'];
                    $reward_amount=$row['reward_amount'];
                    $status=$row['status'];
                    $date_time=$row['order_date'];
                    $listing_name=$row['listing_name'];
                    $used_amount=$row['used_amount'];
                    
                        if($status!="1"){
                             $adddis=$discount+$used_amount;
                        }else{
                         $adddis+=$discount;
                        }
                $resultpost[] =array('trans_id'=>$invoice_no,
                    'time'=>$date_time,
                    'vendor_name'=>$listing_name,
                    'bachat'=>$discount );
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
                       $type_id=$row['type_id'];
                      
                       if($type=='3'){    
                $ledger_balance_row = $this->db->query("SELECT * FROM `user_ledger_balance` WHERE `user_id` = '$user_id' order by id DESC")->row_array();       
                                      $info=$ledger_balance_row['ledger_balance'];
                                       if($info == null ){
                $info="";
                }
                }

                        
                         $resultpost[] = array('id'=> $id,
                                                 'image' => $image,
                                                 'name' => $name,
                                                 'type'=>$type,
                                                 'type_id'=>$type_id,
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