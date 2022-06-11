<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class NotificationcontrolModel extends CI_Model {

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
    
    public function update_token_firebase($token)   {
        $user_id = $this->session->userdata('id'); 
     
        $chk_otp=$this->db->select('id')->get_where('users', array('id' => $user_id));
        if($chk_otp->num_rows()>0){
           $querys = $this->db->query("UPDATE `users` SET `web_token`='$token' WHERE id='$user_id'");
        
          $response =  array(
                'status' => 200,
                'message' => 'success'
            );
            
          return $response;   
        }
        else{
          $response =  array(
                'status' => 400,
                'message' => 'failure',
            );
            
            return $response;   
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
    
    //encrypt string to md5 base64
    public function encrypt($str) {
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

    //decrypt string from md5 base64
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
    
    public function Notification_list($user_id)
    {
            $query_main_category = $this->db->query("SELECT category FROM `notification_category`");
            
            
             $query = $this->db->query("SELECT * FROM `notification_switch_control` WHERE user_id = '$user_id'");
     
        $count = $query->num_rows();
        if ($count > 0) {
            foreach($query_main_category->result_array() as $row_main)
        {
            $main_category = $row_main['category'];
             $query_inner = $this->db->query("SELECT * FROM `notification_switch_control` WHERE user_id = '$user_id' and category = '$main_category'");
             $inner_count = $query_inner->num_rows();
             if($inner_count>0)
             {
            foreach ($query_inner->result_array() as $row) {
                $category = $row['category'];
                $status   = $row['status'];
                $resultpost[] = array(
                    'name' => $category,
                    'value'=> $status
                );
            }
             }
             else
             {
                 $resultpost[] = array(
                    'name' => $main_category,
                    'value'=> 'on'
                );
             }
        }
        } else {
            
                         foreach($query_main_category->result_array() as $row_m)
                         {
                            $resultpost[] = array(
                                      'name' => $row_m['category'],
                                      'value' => 'on'
                                );
                         }
            
        }
        return array(
                    'status' => 200,
                    'message' => 'success',
                    'data' => $resultpost
                    );
    }
    
    public function Notification_update($user_id,$category,$status)
    {
         $updated_at = date('Y-m-d H:i:s');
         $query = $this->db->query("SELECT * FROM `notification_switch_control` WHERE user_id = '$user_id' and category = '$category'");
             $count = $query->num_rows();
        if($count>0)
        {
           $this->db->where('user_id', $user_id)->where('category', $category)->update('notification_switch_control', array(
                    'status' => $status,
                    'update_at' => $updated_at
                ));
                
                 return array(
                    'status' => 200,
                    'message' => 'success'
                );
        }
        else
        {
        $insert_notfication = array(
                'user_id'  => $user_id,
                'category' => $category,
                'status'   => $status,
                'update_at' => $updated_at
            );
            
           $this->db->insert('notification_switch_control',$insert_notfication);
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
    
     public function Notification_all_read($user_id,$id,$type)
    {
           
                 $exp_noti = explode(',',$id);
                
                $type=explode(',',$type);
                if(count($exp_noti)>0)
                {
                     
                    for($ip=0;$ip<count($exp_noti);$ip++)
                    {
                        $ids=$exp_noti[$ip];
                       $updated_at = date('Y-m-d H:i:s');
                         if($type[$ip]=="promo"){
                
                $query = $this->db->query("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$ids' ");
                $count = $query->num_rows();
             
                     if($count>0){
                         
                          $this->db->set('is_read',1);
                            $this->db->where('user_id',$user_id);
                             $this->db->where('noti_id',$ids);
                            $this->db->update('other_notifications_status');
                         
                           return array(
                                        'status' => 201,
                                        'message' => 'Already Updated'
                                    );
                            }else{
                                
                                
                                  $insert_notfication = array(
                                    'user_id'  => $user_id,
                                    'noti_id' => $ids,
                                    'is_read'   => 1,
                                );
                                
                               $this->db->insert('other_notifications_status',$insert_notfication);
                            }
                      
          }
                             if($type[$ip]=="trans"){
                         $query = $this->db->query("UPDATE All_notification_Mobile SET is_read='1' WHERE (user_id='$user_id' || listing_id = '$user_id') &&  id = '$ids'");
                          }
                             if($type[$ip]=="myactivity"){
                         $query = $this->db->query("UPDATE myactivity_notification SET is_read='1' WHERE (user_id='$user_id' || listing_id = '$user_id') &&  id = '$ids'");
                          }
                    }
                }
                
                   return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            
      
    }
    
    public function Notification_read_update($user_id,$noti_id,$type)
    {
         $updated_at = date('Y-m-d H:i:s');
         
         $data=$this->Notification_unread_count($user_id);
         $query = $this->db->query("UPDATE All_notification_Mobile SET is_read='1' WHERE id = '$noti_id'");
         
        if($this->db->affected_rows()>0)
        {
                 return array(
                    'status' => 200,
                    'message' => 'Success',
                    'data'=>$data
                );
        }
        else
        {
       
                return array(
                    'status' => 201,
                    'message' => 'Already Updated'
                );
            
        }
    }
    
    public function Notification_read_update_v1($user_id,$noti_id,$type)
    {
         $updated_at = date('Y-m-d H:i:s');
             $data=$this->Notification_unread_count($user_id);
          if($type=="promo"){
                
                $query = $this->db->query("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$noti_id'");
                $count = $query->num_rows();
             
                     if($count>0){
                           return array(
                                        'status' => 201,
                                        'message' => 'Already Updated'
                                    );
                            }else{
                                
                                
                                  $insert_notfication = array(
                                    'user_id'  => $user_id,
                                    'noti_id' => $noti_id,
                                    'is_read'   => 1,
                                );
                                
                               $this->db->insert('other_notifications_status',$insert_notfication);
                            }
                      
          }
        if($type=="trans"){
         $query = $this->db->query("UPDATE All_notification_Mobile SET is_read='1' WHERE (user_id='$user_id' || listing_id = '$user_id') &&  id = '$noti_id'");
          }
             if($type=="myactivity"){
         $query = $this->db->query("UPDATE myactivity_notification SET is_read='1' WHERE (user_id='$user_id' || listing_id = '$user_id') &&  id = '$noti_id'");
          }
            
            if($this->db->affected_rows()>0)
            {
                     return array(
                        'status' => 200,
                        'message' => 'Success',
                        'data'=>$data
                    );
            }
            else
            {
           
                    return array(
                        'status' => 201,
                        'message' => 'Already Updated'
                    );
                
            }
    }
    
    public function Notification_unread_count($user_id)
    {
         $querycreated = $this->db->query("SELECT created_at FROM `users` WHERE id = '$user_id'");
                $rowcreated_at = $querycreated->row_array() ;
                $user_created_at=$rowcreated_at['created_at'];
        
        
         $query = $this->db->query("SELECT id FROM All_notification_Mobile WHERE (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  AND is_read = '0' OR (listing_id = '$user_id' AND notification_type != 'view_notifications')
         UNION ALL
         SELECT id FROM `myactivity_notification` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications') AND is_read = '0'  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order')
         UNION ALL
        SELECT id FROM `other_notifications` where (user_id = '0' || user_id = '$user_id' ) AND is_read = '0' and created_at >='$user_created_at' ");
        $count = $query->num_rows(); 
        
        $promo = $this->db->query("SELECT id FROM `other_notifications` where (user_id = '0' || user_id = '$user_id' ) and   notification_type!='article' and notification_type!='HealthDictionary' AND is_read = '0' and created_at >='$user_created_at' order by notification_date desc ");
        $promo_countt = $promo->num_rows();
        if($promo_countt > 0){
        foreach($promo->result_array() as $row_main)
             {
                $id  = $row_main['id'];
                  $query11 = $this->db->query("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$id' and is_read = '1'  ");
                  $promo_count_status = $query11->num_rows();
                   
             }
        $promo_count=$promo_countt-$promo_count_status;
        }else{
         $promo_count='0';
        }
        
        $learn = $this->db->query("SELECT id FROM `other_notifications` where (user_id = '0' || user_id = '$user_id' ) and  is_read = '0' and (notification_type='article' || notification_type='HealthDictionary') and created_at >='$user_created_at' order by notification_date desc ");
        $learncountt = $learn->num_rows();
        
         foreach($learn->result_array() as $row_main1)
             {
                $id  = $row_main1['id'];
                  $query111 = $this->db->query("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$id' and is_read = '1' ");
                  $learncount_status = $query111->num_rows();
                   
             }
        $learncount=$learncountt-$learncount_status;
        
        $trans = $this->db->query("SELECT id FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications') AND is_read = '0'  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') order by id desc ");
        $transcount = $trans->num_rows();
        
        $myactivity = $this->db->query("SELECT id FROM `myactivity_notification` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications') AND is_read = '0'  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') order by id desc ");
        $myactivitycount = $myactivity->num_rows();
        
        return array(
                    'status' => 200,
                    'count' => $count,
                    'promo'=> $promo_count,
                    'learn'=> $learncount,
                    'trans'=> $transcount,
                    'myactivity'=> $myactivitycount
                );
    }
    
    public function Notification_All_list($user_id,$page)
    {
        if($page==""){
         $page=1;   
        }
        $count1 = 0;
      //  $radius = $page*5;
        $limit = 10;
        $limitk = 20;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        $startk = ($page - 1) * $limitk;
        
                $querycreated = $this->db->query("SELECT created_at FROM `users` WHERE id = '$user_id'");
                $rowcreated_at = $querycreated->row_array() ;
                $user_created_at=$rowcreated_at['created_at'];
        
        //$query = $this->db->query("SELECT * FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') order by id desc LIMIT $start, $limit");
       $query =$this->db->query("SELECT * FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order')
                                UNION ALL
                                SELECT * FROM `other_notifications` where (user_id = '0' || user_id = '$user_id' ) and (noti_type='andriod' || noti_type='android') and created_at >='$user_created_at' order by created_at  desc  LIMIT $start, $limit");
         $count = $query->num_rows();
        if ($count > 0) {
            
            $query1 = $this->db->query("SELECT * FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') and created_at >='$user_created_at' ");
            $count1 = $query1->num_rows();
        
        
            foreach($query->result_array() as $row_main)
             {
                $id  = $row_main['id'];
                  $query11 = $this->db->query("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$id' order by noti_id DESC limit 1");
              //  echo "SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$id' order by noti_id DESC limit 1";  
                $count = $query11->num_rows();
                   
                if($count>0){
                     
                         $row = $query11->row_array() ;
                       $is_read  = $row['is_read'];
                      }else{
                           $is_read  = $row_main['is_read'];
                      }
                  $user_id1 = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                    $noti_type  = $row_main['trans_type'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id1,
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                      'noti_type'=>$noti_type,
                      'med_video_id' => $med_video_id,
                      'is_read' => $read
                     
                      );
                  
             }
        } else {
            $resultpost = array();
        }
      
        return array(
                    'status' => 200,
                    'message' => 'success',
                    'count' => $count1,
                    'data' => $resultpost
                    );
    }
    
     public function Notification_All_list_v1($user_id,$page,$type)
    {
        if($page==""){
         $page=1;   
        }
        $count=0;
        $count1 = 0;
      //$radius = $page*5;
        $limit = 10;
        $limitk = 20;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        $startk = ($page - 1) * $limitk;
                 $querycreated = $this->db->query("SELECT created_at FROM `users` WHERE id = '$user_id'");
                $rowcreated_at = $querycreated->row_array() ;
                $user_created_at=$rowcreated_at['created_at'];

        if($type=="promo"){
        $query = $this->db->query("SELECT * FROM `other_notifications` where (user_id = '0' || user_id = '$user_id' ) and   notification_type!='article' and notification_type!='HealthDictionary' and  notification_type!='quoteweek' and (noti_type='andriod' || noti_type='android') and created_at >='$user_created_at' order by created_at desc LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            
        
            foreach($query->result_array() as $row_main)
               {
                  $id  = $row_main['id'];
                   $query11 = $this->db->query("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$id'");
                  $count = $query11->num_rows();
                    if($count>0){
                     $row = $query11->row_array() ;
                        $is_read  = $row['is_read'];
                      }else{
                           $is_read  = $row_main['is_read'];
                      }
                  $user_id1 = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $noti_type  = $row_main['trans_type'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id1,
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                      'noti_type'=>$noti_type,
                      'med_video_id' => $med_video_id,
                      'is_read' => $read
                     
                      );
                  
                         }
            } else {
                $resultpost = array();
            }
        }
        
        if($type=="learn"){
        $query = $this->db->query("SELECT * FROM `other_notifications` where (user_id = '0' || user_id = '$user_id' ) and (notification_type='article' || notification_type='HealthDictionary' || notification_type='quoteweek') and (noti_type='andriod' || noti_type='android') and created_at >='$user_created_at' order by created_at desc LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            
        
            foreach($query->result_array() as $row_main)
               {
                  $id  = $row_main['id'];
                  $query11 = $this->db->query("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$id'");
                  $count = $query11->num_rows();
                 //   print_r("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$id'");
                    if($count>0){
                     $row = $query11->row_array();
                        $is_read  = $row['is_read'];
                      }else{
                        $is_read  = $row_main['is_read'];
                      }
                  $user_id1 = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $noti_type  = $row_main['trans_type'];
                 // $is_read  = $row_main['is_read'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                    if($notification_type=='article'){ 
                      $share_url= 'https://medicalwale.com/blogs/'.$article_id;
                     }else{
                         $share_url='';
                     }
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id1, 
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                        'noti_type'=>$noti_type,
                      'med_video_id' => $med_video_id,
                      'share_url' => $share_url,
                      'is_read' => $read
                     
                      );
                  
                         }
            } else {
                $resultpost = array();
            }
        }   
            
        if($type=="trans"){
                
                $query = $this->db->query("SELECT * FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') order by created_at desc LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            
            $query1 = $this->db->query("SELECT * FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') ");
            $count1 = $query1->num_rows();
        
        
            foreach($query->result_array() as $row_main)
             {
                  $id  = $row_main['id'];
                  $user_id = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $noti_type  = $row_main['trans_type'];
                  $is_read  = $row_main['is_read'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id,
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                      'noti_type'=>$noti_type,                      
                      'med_video_id' => $med_video_id,
                      'is_read' => $read
                     
                      );
                  
                         }
                    } else {
                        $resultpost = array();
                    }
            }
            
        if($type=="myactivity"){
                
                $query = $this->db->query("SELECT * FROM `myactivity_notification` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') order by created_at desc LIMIT $start, $limit");
        $count = $query->num_rows();
        if ($count > 0) {
            
            $query1 = $this->db->query("SELECT * FROM `myactivity_notification` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') ");
            $count1 = $query1->num_rows();
        
        
            foreach($query->result_array() as $row_main)
             {
                  $id  = $row_main['id'];
                  $user_id = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $noti_type  = $row_main['trans_type'];
                  $is_read  = $row_main['is_read'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  $datetime = $row_main['created_at'];
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id,
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $datetime,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                      'noti_type'=>$noti_type,
                      'med_video_id' => $med_video_id,
                      'is_read' => $read
                     
                      );
                  
                         }
                    } else {
                        $resultpost = array();
                    }
            }
            
      
        return array(
                    'status' => 200,
                    'message' => 'success',
                    'count' => $count,
                    'data' => $resultpost
                    );
    }
    
    public function Web_Notification_All_list($user_id)
    {
        //     if($page==""){
        //      $page=1;   
        //     }
        //     $count1 = 0;
        //   //  $radius = $page*5;
        //     $limit = 10;
        //     $limitk = 20;
        //     $start = 0;
        //     if ($page > 0) {
        //         if (!is_numeric($page)) {
        //             $page = 1;
        //         }
        //     }
        //     $start = ($page - 1) * $limit;
        //     $startk = ($page - 1) * $limitk;

        $query = $this->db->query("SELECT * FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            
            $query1 = $this->db->query("SELECT * FROM `All_notification_Mobile` where (user_id = '$user_id' AND notification_type != 'user_feedback' AND notification_type != 'healthwall_notifications')  OR (listing_id = '$user_id' AND notification_type != 'view_notifications' AND notification_type != 'order') ");
            $count1 = $query1->num_rows();
        
        
            foreach($query->result_array() as $row_main)
             {
                  $id  = $row_main['id'];
                  $user_id = $row_main['user_id'];
                  $listing_name = $row_main['listing_name'];
                  $listing_id = $row_main['listing_id'];
                  $post_id = $row_main['post_id'];
                  $order_id = $row_main['order_id'];
                  $order_date = date('Y-m-d',strtotime($row_main['order_date']));
                  $order_status = $row_main['order_status'];
                  $booking_id = $row_main['booking_id'];
                  $invoice_no = $row_main['invoice_no'];
                  $notification_type= $row_main['notification_type'];
                  $notification_date = date('Y-m-d',strtotime($row_main['notification_date']));
                  $package_id  = $row_main['package_id'];
                  $package_name  = $row_main['package_name'];
                  $title= $row_main['title'];
                  $list_type = $row_main['list_type'];
                  $message = $row_main['msg'];
                  $img_url = $row_main['img_url'];
                  $tag  = $row_main['tag'];
                  $healthmall_id  = $row_main['healthmall_id'];
                  $pdf_link  = $row_main['pdf_link'];
                  $booking_time  = $row_main['booking_time'];
                  $booking_from  = $row_main['booking_from'];
                  $booking_to  = $row_main['booking_to'];
                  $is_read  = $row_main['is_read'];
                  $presription_id= $row_main['presription_id'];
                  $article_id = $row_main['article_id'];
                  $med_video_id = $row_main['med_video_id'];
                  if($is_read==0)
                  {
                      $read = "false";
                  }
                  else
                  { 
                      $read = "true";
                  } 
                  $resultpost[] = array(
                      'id'  => $id,
                      'user_id' => $user_id,
                      'listing_name' => $listing_name,
                      'listing_id' => $listing_id,
                      'post_id'   => $post_id,
                      'order_id'  => $order_id,
                      'order_date' => $order_date,
                      'order_status' => $order_status,
                      'booking_id'  => $booking_id,
                      'presription_id' => $presription_id,
                      'article_id' => $article_id,
                      'invoice_no' => $invoice_no,
                      'notification_type' => $notification_type,
                      'notification_date' => $notification_date,
                      'title'  => $title,
                      'package_id'  => $package_id,
                      'package_name'  => $package_name,
                      'message' => $message,
                      'img_url'  => $img_url,
                      'tag'  => $tag,
                      'list_type' => $list_type,
                      'healthmall_id' => $healthmall_id,
                      'pdf_link' => $pdf_link,
                      'booking_time' => $booking_time,
                      'booking_from' => $booking_from,
                      'booking_to' => $booking_to,
                      'med_video_id' => $med_video_id,
                      'is_read' => $read
                      );
                  
             }
        } else {
            $resultpost = array();
        }
      
        return array(
                    'status' => 200,
                    'message' => 'success',
                    'count' => $count1,
                    'data' => $resultpost
                    );
    }
    
    public function Notification_Delete_All_list($user_id,$id)
    {
            
        $count_query = $this->db->query("SELECT id FROM `users` WHERE id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            if($id == '')
            {
            $count_query1 = $this->db->query("SELECT * FROM `All_notification_Mobile` where user_id = '$user_id'");
        $count1 = $count_query1->num_rows();
            }
            else
            {
                  $count_query1 = $this->db->query("SELECT * FROM `All_notification_Mobile` where user_id = '$user_id' AND id = '$id'");
                   $count1 = $count_query1->num_rows();
            }
        if($count1>0)
        {
             if($id == '')
            {
            $query = $this->db->query("DELETE FROM `All_notification_Mobile` where user_id = '$user_id'");
            $data = "All Notification Deleted Successfully.";
            }
            else
            {
                $query = $this->db->query("DELETE FROM `All_notification_Mobile` where user_id = '$user_id' AND id = '$id'");
                $data = "Notification Deleted Successfully.";
            }
            
            
            return array(
                    'status' => 200,
                    'message' => 'success',
                    'data' => $data
                    );
        }
        else
        {
           return array(
                    'status' => 200,
                    'message' => 'success',
                    'data' => "No Notification To Delete."
                    );
        }
        }
        else
        {
        return array(
                    'status' => 200,
                    'message' => 'success',
                    'data' => "User Not Exist."
                    );
        }
    }
    
    public function Notification_Delete_single_list($user_id,$id)
    {
            
        $count_query = $this->db->query("SELECT id FROM `users` WHERE id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $count_query1 = $this->db->query("SELECT * FROM `All_notification_Mobile` where user_id = '$user_id' AND id = '$id'");
        $count1 = $count_query1->num_rows();
        
        if($count1>0)
        {
            $query = $this->db->query("DELETE * FROM `All_notification_Mobile` where user_id = '$user_id' AND id = '$id'");
            
            return array(
                    'status' => 200,
                    'message' => 'success',
                    'data' => "Notification Deleted Successfully."
                    );
        }
        else
        {
           return array(
                    'status' => 200,
                    'message' => 'success',
                    'data' => "No Notification To Delete."
                    );
        }
        }
        else
        {
        return array(
                    'status' => 200,
                    'message' => 'success',
                    'data' => "User Not Exist."
                    );
        }
    }
    
    public function Notification_Delete($user_id,$id,$type)
    {
        
            if($id == '')
            {
               
                $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where user_id = '$user_id' OR post_id = '$user_id'");
                return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            }
            else
            {
                $exp_noti = explode(',',$id);
                if(count($exp_noti)>0)
                {
                     
                    for($ip=0;$ip<count($exp_noti);$ip++)
                    {
                         
                        $ids=$exp_noti[$ip];
                        $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where id = '$ids'");
                    }
                }
                else
                {
                     
                  $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where id = '$id'");
                }
                   return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            }
      
    }
    
    public function Notification_Delete_v1($user_id,$id,$type)
    {
        
            if($id == '')
            {
               
                $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where user_id = '$user_id' OR post_id = '$user_id'");
                return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            }
            else
            {
                
                $exp_noti = explode(',',$id);
           
                 $type = explode(',',$type);
                if(count($exp_noti)>0)
                {
                 
                    
                    for($ip=0;$ip<count($exp_noti);$ip++)
                    {
                        $ids=$exp_noti[$ip];
                 
                        if($type[$ip]=="promo"){
                    // $count_query1 = $this->db->query("DELETE FROM other_notifications WHERE id = '$ids'");
               // echo "SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$ids' and is_delete='1'";
                     $query = $this->db->query("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$ids'");
                $count = $query->num_rows();
             
                     if($count>0){
                         
                         $this->db->set('is_delete',1);
                            $this->db->where('user_id',$user_id);
                             $this->db->where('noti_id',$ids);
                            $this->db->update('other_notifications_status');
                           return array(
                                        'status' => 201,
                                        'message' => 'Already Updated'
                                    );
                            }else{
                                
                                
                                  $insert_notfication = array(
                                    'user_id'  => $user_id,
                                    'noti_id' => $ids,
                                    'is_delete'   => 1,
                                );
                                
                               $this->db->insert('other_notifications_status',$insert_notfication);
                            }
                     
                     
                      }
                        if($type[$ip]=="trans"){
                     $count_query1 = $this->db->query("DELETE FROM All_notification_Mobile WHERE id = '$ids'");
                      }
                        if($type[$ip]=="myactivity"){
                     $count_query1 = $this->db->query("DELETE FROM myactivity_notification  WHERE  id = '$ids'");
                      } 
                    }
                }
                else
                {
                    echo "dinesh";
                        if($type=="promo"){
                    // $count_query1 = $this->db->query("DELETE other_notifications WHERE id = '$id'");
                     
                     $query = $this->db->query("SELECT * FROM `other_notifications_status` WHERE user_id = '$user_id' and noti_id = '$id'");
                $count = $query->num_rows();
             
                     if($count>0){
                         
                         $this->db->set('is_delete',1);
                            $this->db->where('user_id',$user_id);
                             $this->db->where('noti_id',$ids);
                            $this->db->update('other_notifications_status');
                         
                           return array(
                                        'status' => 201,
                                        'message' => 'Already Updated'
                                    );
                            }else{
                                
                                
                                  $insert_notfication = array(
                                    'user_id'  => $user_id,
                                    'noti_id' => $id,
                                    'is_delete'   => 1,
                                );
                                
                               $this->db->insert('other_notifications_status',$insert_notfication);
                            }
                     
                      }
                        if($type=="trans"){
                     $count_query1 = $this->db->query("DELETE All_notification_Mobile WHERE id = '$id'");
                      }
                        if($type=="myactivity"){
                     $count_query1 = $this->db->query("DELETE myactivity_notification  WHERE  id = '$id'");
                      } 
                 // $count_query1 = $this->db->query("DELETE FROM `All_notification_Mobile` where id = '$id'");
                }
                   return array(
                    'status' => 200,
                    'message' => 'success',
                    );
            }
      
    }
    public function video_notification($user_id,$type,$type_id)
    {
        if($type == "med_spread")
        {
            $med_spread_query = $this->db->query("SELECT * FROM med_spread where id = '$type_id'");
            $med_spread_count = $med_spread_query->num_rows();
            if ($med_spread_count > 0) {
                $row = $med_spread_query->row_array() ;
                    $id              = $row['id'];
                    $video           = $row['video'];
                    $details         = $row['description'];
                    $video_thumbnail = $row['thumbnail'];
                    $title           = $row['title'];
                    if ($video_thumbnail != '') {
                        $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/med_spread/' . $video;
                        $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/med_spread/thumbs/' . $video_thumbnail;
                    } else {
                        $video_thumbnail = '';
                    }
                    $views_query = $this->db->query("SELECT id from med_spread_views WHERE video_id='$id'");
                    $total_views          = $views_query->num_rows();
                    
                    $like_query  = $this->db->query("SELECT id from med_spread_likes WHERE video_id='$id'");
                    $total_likes = $like_query->num_rows();
                    
                    $like_yes_no = $this->db->select('id')->from('med_spread_likes')->where('video_id', $id)->where('user_id', $user_id)->get()->num_rows();
                    
                    $result[] = array(
                        'video_id' => $id,
                        'video' => $video,
                        'video_thumbnail' => $video_thumbnail,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appmedspread/' . $id
                    );
                
            } else {
                $result = array();
            }
        }
        else if($type == "med_tube")
        {
            $other_query         = $this->db->query("SELECT * FROM med_tube where id = '$type_id'");
            $other_count         = $other_query->num_rows();
            if ($other_count > 0) 
            { 
                
                $social_events = array();
                $special_days  = array();
                $row = $other_query->row_array();
                $id              = $row['id'];
                $type1            = $row['type'];
                $video_thumbnail = $row['thumbnail'];
                $title           = $row['title'];
                $video           = $row['video'];
                $details         = $row['description'];
                
                $video_views_query = $this->db->query("SELECT id from med_tube_views WHERE video_id='$id'");
                $total_views       = $video_views_query->num_rows();
                
                $like_query  = $this->db->query("SELECT id from med_tube_likes WHERE video_id='$id'");
                $total_likes = $like_query->num_rows();
                
                $like_yes_no = $this->db->select('id')->from('med_tube_likes')->where('video_id', $id)->where('user_id', $user_id)->get()->num_rows();
                
                
                if ($type1 == 'social_events') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/social_events/' . $video;
                        $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/social_events/thumb/' . $video_thumbnail;
                    } else {
                        $video_thumbnail = '';
                    }
                    $result[] = array(
                        'video_id' => $id,
                        'video_thumbnail' => $video_thumbnail,
                        'video' => $video,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appsocialevents/' . $id
                    );
                }
                if ($type1 == 'special_days') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/special_days/' . $video;
                        $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/special_days/thumb/' . $video_thumbnail;
                    } else {
                        $video_thumbnail = '';
                    }
                    $result[] = array(
                        'video_id' => $id,
                        'video' => $video,
                        'video_thumbnail' => $video_thumbnail,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appspecialdays/' . $id
                    );
                }
                if ($type1 == 'advertising_campaign') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/advertising_campaign/' . $video;
                        $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/advertising_campaign/thumb/' . $video_thumbnail;
                    } else {
                        $video_thumbnail = '';
                    }
                    $result[] = array(
                        'video_id' => $id,
                        'video' => $video,
                        'video_thumbnail' => $video_thumbnail,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appadvertisingcampaign/' . $id
                    );
                }
                 if ($type1 == 'animation') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://s3.amazonaws.com/medicalwale/images/fitness_center/video/' . $video;
                        $video_thumbnail = 'https://s3.amazonaws.com/medicalwale/images/fitness_center/video/Thumbnails/' . $video_thumbnail;
                    } else {
                        $video           = 'https://s3.amazonaws.com/medicalwale/images/fitness_center/video/' . $video;
                        $video_thumbnail = '';
                    }
                    $result[] = array(
                        'video_id' => $id,
                        'video' => $video,
                        'video_thumbnail' => $video_thumbnail,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appadvertisingcampaign/' . $id
                    );
                }
            
            } else {
                $result = array();
            }
      
        }
            return array(
                        'status' => 200,
                        'message' => 'success',
                        'data' => $result
                        );
        
    }
    
}
