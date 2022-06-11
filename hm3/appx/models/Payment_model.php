<?php
 class Payment_model extends CI_Model {
       
    public function __construct(){
          
        
        $this->load->database();
        
       
      }
      
    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";
    
    /*public function check_auth_client() {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            $resp = array(
		        "status" => "Unauthorized",
                "statuspic_root_code" => "401",
		        );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        }
    }*/
    
   /*public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            $resp = array(
		        "status" => "Unauthorized",
                "statuspic_root_code" => "401",
		        );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            
             if ($q->expired_at < date("Y-m-d H:i:s", strtotime("-1 days"))) {
                // return json_output(401, array(
                //     'status' => 401,
                //     'message' => 'Your session has been expired.'
                // ));
                return  array(
                    'status' => 401,
                    'message' => 'Your session has been expired.'
                );
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = date("Y-m-d H:i:s", strtotime("+1 days"));//'2020-12-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array(
                    'expired_at' => $expired_at,
                    'updated_at' => $updated_at
                ));
                //echo $this->db->last_query(); die();
                return 200;
            }
        }
    }*/
    
       public function insert_payment_status($user_id, $listing_id, $trans_id, $status, $type, $order_id, $order_type,$amount, $status_mesg, $discount, $discount_rupee, $payment_type) {
        
      
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
        
        
        $upadte_ledger_array_point = array(
            'user_id'       => $user_id,
            'listing_id'    => $listing_id,
            'trans_id'      => $trans_id,
            'trans_type'    => 4,
            'order_id'      => $order_id,
            'amount'        => $amount,
            'order_status'  => $status=='1'?'success':'failed',
            'order_type'    => $order_type,
            'status_message'=> $status_mesg,
            'discount'      => $discount,
            'discount_rupee'=> $discount_rupee,
            'trans_time'    => $date,
            'trans_mode'=> $payment_type,
             //   'vendor_category'=> $vendor_category
             'vendor_category'=>  $type
        );
        
        $upadte_user_points = array(
            'user_id'        => $user_id,
            'order_id'       => $order_id,
            'trans_id'       => $trans_id,
            'points'         => $amount,
            'created_at'     => $date,
            'expire_at'      => $Expire_date,    
            'status'         => 'active'
        );
       
       
        if($payment_type != '3'){
            
           
            $this->db->insert('user_ledger',$upadte_ledger_array);
         
         //update ledger balance but this balance is locked
            $query = $this->db->query("select * from user_ledger_balance where user_id='$user_id'");
            $row = $query->row();
            $row_count =$query ->num_rows();
            //die();
            
            if($row_count>0)
            {
                //update ledger balance 
                $existing_balance = $row->ledger_balance;
                $existing_lock_balance =$row->lock_amount;
                $total_balance = $existing_balance + $amount; 
                $total_lock_amount = $existing_lock_balance + $amount;
                $user_ledger_balance = array(
                    'ledger_balance' =>$total_balance,
                    'lock_amount' => $total_lock_amount
                    );
                     $this->db->where('user_id', $user_id);  
                $this->db->update('user_ledger_balance', $user_ledger_balance); 
            }
            else
            {
              // insert into ledger balance 
               $user_ledger_balance = array(
                    'user_id' => $user_id,
                    'ledger_balance' =>$amount,
                    'lock_amount' => $amount                                                                  
                    );
              $this->db->insert('user_ledger_balance',$user_ledger_balance);
            }
           //end
            
            // }
            
        }
      
        return array(
            'status' => 201,
            'message' => 'success'
        );
        
    }
 
 
 
 }
 
 ?>