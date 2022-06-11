<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Spin_Model extends CI_Model {

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

    public function get_all_spin_data($user_id,$coupon_id) {

      date_default_timezone_set('Asia/Kolkata');
        $resultpost =array();
            	
                $query = $this->db->query("SELECT * FROM `spinner_master` where active=1");
                $count=$query->num_rows();
                if($query->num_rows()>0)
                {
                foreach ($query->result_array() as $row) {
        
                    $type=$row['type'];
                    $value=$row['value'];
                    $color_code=$row['color_code'];
                    $color_code1=$row['color_code1'];
                    $text_msg=$row['text_msg'];
                     $status='1';  
                     $lives="5";
                    if($type=='1'){
                     $query1 = $this->db->query("SELECT * FROM `spin_value` where user_id='$user_id' and coupon_id='$coupon_id' and type='$type'");
                      if($query1->num_rows()>0)
                            {
                             $status='0';   
                            }
                    }
                    
                    $query1 = $this->db->query("SELECT * FROM `spin_value` where user_id='$user_id' and coupon_id='$coupon_id'");
                      if($query1->num_rows() == 5)
                            {
                             $status='0';   
                            }
                    
                    if($type=='1'){
                        $query = $this->db->query("SELECT v_company_logo FROM `vendor_details_hm` where v_id='$coupon_id' ")->row_array();
                         $image=$query['v_company_logo'];
                         if($image==null){
                             $image="";
                         }
                    }else{
                        $image=$row['icons'];
                    }
                    
                      $query12 = $this->db->query("SELECT * FROM `spin_value` where user_id='$user_id' and coupon_id='$coupon_id'");
                        $result1=$query12->result_array();
                         if($query12->num_rows()>0)
                            {
                               foreach($result1 as $result){
    
                                    $lives=$result['lives'];
                               }    
                           }
                        $resultpost1[] = array
                            (
                               "type" => $type,
                               "value" => $value,
                               "color_code" => $color_code,
                               "color_code1" => $color_code1,
                               "text_msg"=>$text_msg,
                               "image"=>$image,
                               "status"=>$status
                            );
                            
                            $resultpost = array
                            (
                                "status"=> 200,
                                "message"=> "success",
                               "lives" => $lives,
                               "count"=>$count,
                               "data" => $resultpost1
                               
                            );
                            
                            
                }
        
                return $resultpost;
 
    	    } 
    	    else
    	    {
    	      return  $resultpost; 
    	    }
    }
    
       public function spin_action($user_id,$coupon_id,$type) {
            $this->load->model('LedgerModel');

            $query = $this->db->query("SELECT * FROM `spin_value` where user_id='$user_id' and coupon_id='$coupon_id'");
                
                if($query->num_rows()>0)
                {
                   
                        $result1=$query->result_array();
                           foreach($result1 as $result){

                                $lives[]=$result['lives']-1;
                              $remain_leave=min($lives);
                           }
                           
                
                           
                            $data=array(
                                        "user_id"=>$user_id,
                                        "coupon_id"=>$coupon_id,
                                        "lives"=>"$remain_leave",
                                        "type"=>$type,
                                        'created_at'=>date('Y-m-d H:i:s'),
                                        );
                                
                          
                           if($remain_leave >= 0){
                              
                               /* Call points entry model here*/
                               $newINV = $user_id . date("mdHis");
                               $invoice_no="SPIN_".$newINV;
                                if($type == 3){
                                    
                                    $transaction_date = "";
                                    $transaction_id = "";
                                    $points = 50;
                                    $comments = "Got ".$points." from spinner";
                                    $status = "Active";
                                    $listing_type = "";
                                    $expire_at = date("Y-m-d H:i:s", strtotime("+30 days"));
                                    $res = $this->LedgerModel->add_points($user_id,  $invoice_no, $transaction_date, $transaction_id,  $points,  $comments,  $status,  $listing_type,  $expire_at);
                                    $points_msg = $res['message'];
                                
                                
                                /*Delete this after new ledger is live*/
                                
                                $upadte_ledger_array = array(
                                    'user_id'       => $user_id,
                                    'listing_id'    => 0,
                                    'trans_id'      => "",
                                    'trans_type'    => 4, // points credit
                                    'order_id'      => $invoice_no,
                                    'amount'        => $points,
                                    'order_type'    => "", 
                                    'status_message'=> $comments,
                                    'trans_time'    => date("Y-m-d H:i:s")
                                    
                                );
                                $this->db->insert('user_ledger',$upadte_ledger_array);
                                /*Delete this after new ledger is live : end*/
                                }
                                    $this->db->insert('spin_value',$data);   
                                    $response=$data;
                                   
                                        return $response;
                           }else{
                                    $response=$data;
                                    
                                        return $response;
                               
                           }
                        }else{
                             $data=array(
                                "user_id"=>$user_id,
                                "coupon_id"=>$coupon_id,
                                "lives"=>'4',
                                "type"=>$type,
                                'created_at'=>date('Y-m-d H:i:s'),
                                );
                            
                            $this->db->insert('spin_value',$data);    
                            
                            $response=$data;
                                
                                return $response;
                    }
        }
}
