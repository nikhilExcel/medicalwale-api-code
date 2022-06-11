<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class DoctoronlineModel extends CI_Model
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
    public function encrypt($str)
    {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv  = hash('MD5', 'mdwale8655328655', true);
        $module    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad   = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $encrypted = mcrypt_generic($module, $str);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        return base64_encode($encrypted);
    }
    public function decrypt($str)
    {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv  = hash('MD5', 'mdwale8655328655', true);
        $module    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $str = mdecrypt_generic($module, base64_decode($str));
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        $slast = ord(substr($str, -1));
        $str   = substr($str, 0, strlen($str) - $slast);
        return $str;
    }
  
    public function doctor_online_cat_v1($user_id,$mlat,$mlng,$page)
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
      
        
        // genral Doctor Start Here
         date_default_timezone_set('Asia/Kolkata');
                    $current_date = date('H:i:s');
        $day=date('l');
          $this->un_block_doctor();
        $sql   =  sprintf("SELECT DISTINCT doctor_list.user_id, doctor_list.*,doctor_online.chat,doctor_online.video,doctor_online.chat_fee,doctor_online.video_fee  FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id WHERE doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' AND FIND_IN_SET('" . $day . "',doctor_online.day) and doctor_online.status='0' and doctor_online.block='0' LIMIT $start, $limit  ");
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) 
        {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
                 $chat=$row['chat'];
                 $video=$row['video'];
                 $chat_fee=$row['chat_fee'];
                 $video_fee=$row['video_fee'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
                $category            = explode(",",$row['category']);
                $cat_name="";
                $cat_id="";
                for($i=0;$i < count($category);$i++)
                {
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  id='$category[$i]' ");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                if(count($category)-1==$i)
                  {
                      $cat_name               .= $get_sp['area_expertise'];
                      $cat_id                 .= $get_sp['id'];
                  }
                else
                  {
                    $cat_name               .= $get_sp['area_expertise'].",";
                    $cat_id                 .= $get_sp['id'].",";
                   }
                }
                $resultpost[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$cat_id,
                            'category_name'=>$cat_name,
                            'chat'=>(int)$chat,
                            'video'=>(int)$video,
                            'chat_fee'=>$chat_fee,
                            'video_fee'=>$video_fee,
                            'online_status'=>1
                            );
            }
            
        } 
        else 
        {
            $resultpost=array();
        }
        
        return $resultpost;
    }
    
    public function doctor_user_block($doctor_id,$user_id,$member_id,$type,$fee)
    {
        // genral Doctor Start Here
        date_default_timezone_set('Asia/Kolkata');
        $current_date = date('Y-m-d H:i:s');
        $start_date = date('Y-m-d  H:i:s');
        $end_date = date('Y-m-d H:i:s',strtotime("+10 minutes"));
          $this->un_block_doctor();
        $query2 = $this->db->query("select * from doctor_online_block where doctor_id = '$doctor_id' and status='0'");
        $count2 = $query2->num_rows();
        if ($count2 =="0") 
           {
                $booking_master_array = array(
                                            'doctor_id' => $doctor_id,
                                            'user_id' => $user_id,
                                            'member_id' => $member_id,
                                            'start_time' => $start_date,
                                            'end_time' => $end_date,
                                            'consulation' =>$type,
                                            'fee' => $fee,
                                            'status' => 0,
                                            'created_date' => $current_date,
                                           );
                $insert = $this->db->insert('doctor_online_block', $booking_master_array);
                $appointment_id       = $this->db->insert_id();
                if($appointment_id > 0)
                  {
                   $booking_array = array(
                        'block' => 1,
                        );
                    $updateStatus  = $this->db->where('doctor_id', $doctor_id)->update('doctor_online', $booking_array);
                    $resultpost=array('status'=>200,
                                     'message'=>'success');  
        }
                else
                  {
                   $resultpost=array('status'=>201,
                                     'message'=>'failed'); 
        }
           }
           else
            {
                $resultpost=array('status'=>201,
                                  'message'=>'Already Block'); 
            }
      
         
         
        return $resultpost;
    }
    
    public function doctor_online_chat($doctor_id,$user_id,$booking_id)
    {
          $this->un_block_doctor();
        $query2 = $this->db->query("select name,token,agent,token_status,web_token  from users where id = '$doctor_id'");

            $count2 = $query2->num_rows();
            if ($count2 > 0) 
            {
               $row=$query2->row_array();
               $usr_name = $row['name'];
               $agent = $row['agent'];
               $reg_id = $row['token'];
               $web_token = $row['web_token'];
               
               $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/doctor.png';
               $tag = 'text';
               $key_count = '1';
               $title       = "Go Live - It`s appointment time!";
               $msg         = "You have an appointment in few seconds, tap here to go live. Please be in a good network area for seamless appointment.";
               
               $type="go_live";
               $click_action = 'https://doctor.medicalwale.com/booking_controller/booking_appointment/'.$doctor_id;
               
               $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $doctor_id,
                      'booking_id'  => $booking_id,
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => '$type',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
                    );
                    $this->db->insert('All_notification_Mobile', $notification_array);
               
              
               $this->send_gcm_notify_doctor_online($title, $reg_id, $msg, $img_url, $tag, $agent,$type,$booking_id);
               $this->send_gcm_web_notify_doctor_online($title, $msg, $web_token, $img_url, $tag, $agent, $type,$click_action);
               $return=array(
                            'status' => 200,
                            'message' => 'success'
                        );
              return $return;
            }
    }
    
    
     public function send_gcm_notify_doctor_online($title, $reg_id, $msg, $img_url, $tag, $agent,$type,$booking_id)
    {
        
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        if (!defined("GOOGLE_GCM_URL")) {
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields  = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array(
                    "title" => $title,
                    "message" => $msg,
                    "notification_image" => $img_url,
                    "tag" => $tag,
                    'sound' => 'default',
                    "notification_type" => $type,
                    "notification_date" => $date,
                    "booking_id" => $booking_id
                    
                )
            );
            /*IOS registration is left */
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
            );
            $ch      = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
             //print_r($result);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
        }
    }
    
    // Web notification FCM 
    function send_gcm_web_notify_doctor_online($title, $msg, $reg_id, $img_url, $tag, $agent, $type,$click_action) {
            date_default_timezone_set('Asia/Kolkata');
            $date = date('j M Y h:i A');
            if (!defined("GOOGLE_GCM_URL"))
                define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'notification' : 'notification' => array(
                     "title"=> $title,
                     "body" => $msg,
                     "click_action"=> $click_action,
                     "icon"=> $img_url
                )
            );
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            //print_r($result);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
    }
    
    
    
    
    
    
    
    
    
    
    
    public function add_bookings($doctor_id,$user_id,$member_id,$type,$fee,$status,$coupon_id,$trans_id,$payment_method)
    {
          $this->un_block_doctor();
        date_default_timezone_set('Asia/Kolkata');
        $response=array();
        $booking_id           = date("YmdHis");
        $date                 = date('Y-m-d H:i:s');
        $booking_date=date('Y-m-d');
        $from_time = date("H:i:s" , strtotime("+1 minutes"));
        $to_time =  date("H:i:s", strtotime("+10 minutes",strtotime($from_time)));
        $booking_time=$from_time."-".$to_time;
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $user_id,
            'listing_id' => $doctor_id,
            'clinic_id' => 0,
            'booking_date' => $booking_date,
            'booking_time' => $from_time."-".$to_time,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'status' => $status,
            'description' => "",
            'date' => $date,
            'patient_id' => $member_id,
            'consultation_type' => $type,
            'vendor_type'=>5,
            'consultation_charges'=>$fee
        );
        $con_type="";
        if($type=="chat")
        {
            $con_type="Chat";
        }
        else
        {
            $con_type="Video";
        }
        $insert = $this->db->insert('doctor_booking_master', $booking_master_array);
        $appointment_id       = $this->db->insert_id();
        if($insert)
            {
                
              
                
             $booking_array = array(
                        'appoint_done' => 1,
                        'booking_id'=>$booking_id,
                        'status'=>1
                        );
                     $updateStatus  = $this->db->where('doctor_id', $doctor_id)->update('doctor_online_block', $booking_array);
                     
               $query_dr2           = $this->db->query("SELECT * FROM users  WHERE id='$user_id' ");
                $data22               = $query_dr2->row_array();
                
                $patient_master_array = array(
                    'user_id' => $user_id,
                    'clinic_id' =>0, 
                    'booking_id' => $appointment_id,
                    'patient_name' => $data22['name'],   
                    'contact_no' => $data22['phone'],
                    'email' => $data22['email'],
                    'gender' => $data22['gender'],
                    'allergies' => $data22['allergies'],
                    'heradiatry_problem' => $data22['heradiatry_problem'],
                    'created_date' => $date,
                    'type' => $con_type,
                    'date_of_birth' => $data22['dob'],
                    'doctor_id' => $doctor_id
                   );
                $this->db->insert('doctor_patient', $patient_master_array);
                $doctor_patient_id = $this->db->insert_id();  
                
                $query_dr1           = $this->db->query("SELECT name FROM users  WHERE id='$user_id'");
                $data1               = $query_dr1->row_array();
                $user_name        = $data1['name'];
                $description="";
                $this->notifyMethod_doctor($doctor_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $con_type, $user_name,$from_time);
                
                $query_dr           = $this->db->query("SELECT doctor_list.doctor_name,doctor_list.telephone FROM doctor_list  WHERE user_id='$doctor_id'");
                $data               = $query_dr->row_array();
                $doctor_name        = $data['doctor_name'];
                $doctor_phone       = $data['telephone'];
                $final_booking_date = date('M-d', strtotime($booking_date));
              /*  if ($insert) 
                    {
            $message      = 'Your appointment with ' . $doctor_name . '  for ' . $final_booking_date . ', ' . $booking_time . ' is confirmed and your booking id is '.$booking_id.'. Thanks Medicalwale.com';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_mobile,
                'Body' => $message
            );
            $exotel_sid   = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);
            $type_of_order = 'booking';
            $login_url     = 'https://doctor.medicalwale.com';
            $message2      = 'You have received ' . $type_of_order . ', kindly login to ' . $login_url . '. Thank you.';
            $post_data2    = array(
                'From' => '02233721563',
                'To' => $doctor_phone,
                'Body' => $message2
            );
            $exotel_sid2   = "aegishealthsolutions";
            $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
            $url2          = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
            $ch2           = curl_init();
            curl_setopt($ch2, CURLOPT_VERBOSE, 1);
            curl_setopt($ch2, CURLOPT_URL, $url2);
            curl_setopt($ch2, CURLOPT_POST, 1);
            curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
            $http_result2 = curl_exec($ch2);
            curl_close($ch2);
        }*/
      
               $this->notifyMethod_user($doctor_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $con_type, $doctor_name);
                
                if($payment_method != '3' || $payment_method != '14') 
                               {
                                $this->load->model('LedgerModel'); 
                                $ledger_owner_type=0;
                    			$listing_id_type=5;
                    			$credit=0;
                    			$debit=$fee;
                    			$user_comments='';
                    			$mw_comments='';
                    			$vendor_comments='';
                    			$order_type=3;
                    			$transaction_of=3;
                    			$transaction_id=$trans_id;
                    			$trans_status=1;
                    			$transaction_date=date('Y-m-d H:i:s');
                    			$vendor_id = 5;
                    			$listing_id=$doctor_id;
                    			$array_data = array();
                                $ledger_data = $this->LedgerModel->create_ledger($user_id,  $booking_id,  $ledger_owner_type,  $listing_id,  $listing_id_type,  $credit,  $debit,  $payment_method,  $user_comments,  $mw_comments, $vendor_comments, $order_type, $transaction_of, $transaction_id, $trans_status, $transaction_date, $vendor_id, $array_data);
                               //print_r($ledger_data);
                                 $ledger_owner_type1=0;
                    			$listing_id_type1=5;
                    			$credit1=$fee;
                    			$debit1=0;
                    			$user_comments1='';
                    			$mw_comments1='';
                    			$vendor_comments1='';
                    			$order_type1=3;
                    			$transaction_of1=3;
                    			$transaction_id1=$trans_id;
                    			$trans_status1=1;
                    			$transaction_date1=date('Y-m-d H:i:s');
                    			$vendor_id1 = 5;
                    			$listing_id1=$doctor_id;
                    			$array_data1 = array();
                                $ledger_data1 = $this->LedgerModel->create_ledger($user_id,  $booking_id,  $ledger_owner_type1,  $listing_id1,  $listing_id_type1,  $credit1,  $debit1,  $payment_method,  $user_comments1,  $mw_comments1, $vendor_comments1, $order_type1, $transaction_of1, $transaction_id1, $trans_status1, $transaction_date1, $vendor_id1, $array_data1); 
                                 //print_r($ledger_data1);
                               }
                     
                
                $con_from=date('H:i:s A',strtotime($from_time));
                $con_to=date('H:i:s A',strtotime($to_time));
                $resultpost=array('status'=>200,
                                  'message'=>'success',
                                  'booking_id'=>$booking_id,
                                  'booking_date'=>date('d-m-Y',strtotime($booking_date)),
                                  'consulation_time'=>$con_from." - ".$con_to,
                                  'consulation_type'=>$con_type
                              );  
            }
            else
            {
              $resultpost=array('status'=>201,
                                  'message'=>'failed');   
            }
            return $resultpost;
    }
     public function notifyMethod_doctor($listing_id, $user_id, $description, $booking_date, $booking_time, $appointment_id, $connect_type, $user_name,$from)
    {
        
        $customer_token       = $this->db->query("SELECT name,token,agent,token_status,web_token FROM users WHERE id='$listing_id'");
        $customer_token_count = $customer_token->num_rows();
        if ($customer_token_count > 0) {
            $timestamp = strtotime($booking_date);
            $newdate=date("d-M-Y", $timestamp);
            
            $newtime=date("h:i A", strtotime($from));
            $token_status = $customer_token->row_array();
            $usr_name     = $token_status['name']; 
            $agent        = $token_status['agent'];
            $reg_id       = $token_status['token'];
            $web_token       = $token_status['web_token'];
            $img_url      = 'https://s3.amazonaws.com/medicalwale/images/img/doctor.png';
            $tag          = 'text';
            $key_count    = '1';
            $title        = "You have a new $connect_type appointment For $newdate at $newtime ."; 
            $msg          = 'Tap for the patient`s details';
            $type = 'Booking';
            $click_action = 'https://doctor.medicalwale.com/booking_controller/booking_appointment/'.$listing_id;
            $this->send_gcm_notify_doctor($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_time, $appointment_id, $connect_type,$from);
            $this->send_gcm_web_notify_doctor($title, $web_token, $msg, $img_url,$agent,$click_action);    
           
            
            
            
        }
    }
    
     public function send_gcm_notify_doctor($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_time, $appointment_id, $connect_type,$from)
    {
          date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        
         //added by zak for all notification therough services
                $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $listing_id,
                      'booking_id'  => $appointment_id,
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'doctor_appointment_notifications',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
        //end
        
        
       
        if (!defined("GOOGLE_GCM_URL")) {
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields  = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array(
                    "title" => $title,
                    "message" => $msg,
                    "notification_image" => $img_url, 
                    "tag" => $tag,
                    'sound' => 'default',
                    "notification_type" => 'doctor_appointment_notifications',
                    "notification_date" => $date,
                    "appointment_date" => $date,
                    "appointment_time" => $booking_time,
                    "appointment_id" => $appointment_id,
                    "type_of_connect" => $connect_type
                )
            );
            /*IOS registration is left */
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
            );
            $ch      = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
           // print_r($result);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
        }
    }
    
     public function send_gcm_web_notify_doctor($title, $reg_id, $msg, $img_url,$agent,$click_action)
    {
          date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        
        
        if (!defined("GOOGLE_GCM_URL")) {
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields  = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array(
                    "title"=> $title,
                     "body" => $msg,
                     "click_action"=> $click_action,
                     "icon"=> $img_url
                )
            );
             
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
            );
           
            $ch      = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
           // print_r($result);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
        }
    }
    
    
    // user notifiaction
     public function notifyMethod_user($listing_id, $user_id, $description, $booking_date, $booking_time, $appointment_id, $connect_type, $user_name)
    {
        $customer_token       = $this->db->query("SELECT name,token,agent,token_status,web_token FROM users WHERE id='$user_id'");
        $customer_token_count = $customer_token->num_rows();
        if ($customer_token_count > 0) {
            $token_status = $customer_token->row_array();
            $usr_name     = $token_status['name'];
            $agent        = $token_status['agent'];
            $reg_id       = $token_status['token'];
            $web_token       = $token_status['web_token'];
            $img_url      = 'https://s3.amazonaws.com/medicalwale/images/img/doctor.png';
            $tag          = 'text';
            $key_count    = '1';
            $title        = "Your appointment with $user_name has been booked."; //$user_name . ' has booked an appointment'; 
            $msg          = 'Date : ' . $booking_date . '  Time : ' . $booking_time;
            
            $type = 'Booking';
            
            $this->send_gcm_notify_user($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_time, $appointment_id, $connect_type);
            
           
            
            
        }
    }
    
     public function send_gcm_notify_user($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent, $date, $slot, $appointment_id, $consultation_type)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        
         //added by zak for all notification therough services
                $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $listing_id,
                      'booking_id'  => $appointment_id,
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'appointment_notifications',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
        //end
        
        
        
        if (!defined("GOOGLE_GCM_URL")) {
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields  = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array(
                    "title" => $title,
                    "message" => $msg,
                    "notification_image" => $img_url,
                    "tag" => $tag,
                    'sound' => 'default',
                    "notification_type" => 'appointment_notifications',
                    "notification_date" => $date,
                    "appointment_date" => $date,
                    "appointment_time" => $slot,
                    "appointment_id" => $appointment_id,
                    "type_of_connect" => $consultation_type
                )
            );
            /*IOS registration is left */
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
               $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
               
            );
            $ch      = curl_init();
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
    // End Notifiaction
    
    
    public function doctor_online_cat($user_id,$mlat,$mlng)
    {
         $radius = '5';
         $ty ="LIMIT 5";
        
        
        // genral Doctor Start Here
         date_default_timezone_set('Asia/Kolkata');
                    $current_date = date('H:i:s');
        $day=date('l');
        
        $sql   =  sprintf("SELECT DISTINCT doctor_list.user_id, doctor_list.*  FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id WHERE FIND_IN_SET('41', doctor_list.category) and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' AND FIND_IN_SET('" . $day . "',doctor_online.day) and doctor_online.status='0' ORDER BY RAND () $ty  ");
        /*echo "SELECT doctor_list.*  FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id WHERE FIND_IN_SET('41', doctor_list.category) and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' AND FIND_IN_SET('" . $day . "',doctor_online.day) ORDER BY RAND () $ty  ";   */         
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) 
        {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
                $category            = explode(",",$row['category']);
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  id='41' ");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                $cat_name               = $get_sp['area_expertise'];
               
                if(in_array("41", $category))
                {
                   
                    
                        $genral[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>"41",
                            'category_name'=>$cat_name,
                            'online_status'=>1
                            
                        );
                    
                }
            }} 
        else 
        {
            $genral = array();
        }
         // genral Doctor End Here
        
        
        
         // Gynecologist Doctor Start Here
        $Where = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(46|47),"';
        
        $sql   =  sprintf("SELECT  DISTINCT doctor_list.user_id,doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id WHERE $Where and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date' AND doctor_online.endtime >= '$current_date' AND FIND_IN_SET('" . $day . "',doctor_online.day) and doctor_online.status='0' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            } 
                $category       = $row['category'];
                $cat=explode(",",$category);
               if(in_array('46', $cat))
               {
                   
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'46')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $gy[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                   
               }
               else if(in_array('47', $cat))
               {
                    
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'47')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $gy[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
               }
              
               
            }
        } else {
            $gy = array();
        }
        
        // Gynecologist Doctor End  Here
        
        // Dermatologist Start Here
        $sql   =  sprintf("SELECT  DISTINCT doctor_list.user_id,doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id WHERE FIND_IN_SET('27', doctor_list.category) and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' AND FIND_IN_SET('" . $day . "',doctor_online.day) and doctor_online.status='0' ORDER BY RAND () $ty  ");
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
                    
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  id='27' $ty");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $dermatologist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>"27",
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                     
            }
        } else {
            $dermatologist = array();
        }
        //Dermatologist End Here
        
        
         //Ayurvedic End Here
         $Where1 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(9|10|11|12|13|14|15|40),"';
         $doctor_data=array();
        $sql   =  sprintf("SELECT  DISTINCT doctor_list.user_id,doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id  WHERE $Where1 and  doctor_list.is_active = 1 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' AND FIND_IN_SET('" . $day . "',doctor_online.day) and doctor_online.status='0' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
           
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                $doctor_image        = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
             $category       = $row['category'];
                $cat=explode(",",$category);
            
                if(in_array($doctor_user_id,$doctor_data))
                {
                }
                else
                {
                    
               
               
                //print_r($cat);
               if(in_array('9', $cat))
               {
                   
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'9')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
               }
               else if(in_array('10', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'10')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
               }
               
               
               
               else if(in_array('11', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'11')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
               }
               else if(in_array('12', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'12')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
               }
               
               else if(in_array('13', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'13')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
               }
               
                else if(in_array('14', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'14')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
               }
               
               else if(in_array('15', $cat))
               {
                   
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'15')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
               }
               
               
               else if(in_array('40', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'40')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $ayur[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
               }
                }
                $doctor_data[]=$doctor_user_id;
            }
           
        } else {
            $ayur = array();
        }
         //Ayurvedic End Here
        
        //ENT Start Here
         $sql   =  sprintf("SELECT  DISTINCT doctor_list.user_id,doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id  WHERE FIND_IN_SET('31', doctor_list.category) and  doctor_list.is_active = 1 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' AND FIND_IN_SET('" . $day . "',doctor_online.day) and doctor_online.status='0' ORDER BY RAND () $ty  ");
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
              
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  id='31' $ty");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $ent[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>"31",
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                       
            }
        } else {
            $ent = array();
        }
        //ENT End Here
        
        //Dentist Start Here
         $Where2 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(23|26|239|240),"';
        $doctor_data_1=array();
        $sql   =  sprintf("SELECT  DISTINCT doctor_list.user_id,doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id   WHERE $Where2 and  doctor_list.is_active = 1 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' AND FIND_IN_SET('" . $day . "',doctor_online.day) and doctor_online.status='0' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
            $category       = $row['category'];
                $cat=explode(",",$category);
            
                if(in_array($doctor_user_id,$doctor_data_1))
                {
                }
                else
                {
                    
               
               
                //print_r($cat);
               if(in_array('23', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'23')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $dentist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
               }
               else if(in_array('26', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'26')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $dentist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
               }
               
               
               
               else if(in_array('239', $cat))
               {
                
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'239')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $dentist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
               }
               else if(in_array('240', $cat))
               {
                 
                            $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'240')");
                            $total_category = $query_sp->num_rows();
                            $get_sp=$query_sp->row_array();
                            $cat_name               = $get_sp['area_expertise'];
                            $cat_id  = $get_sp['id'];
                            
                          
                              $final_cat_id=$cat_id;
                          
                            $dentist[] = array(
                                'doctor_user_id' => $doctor_user_id,
                                'doctor_name' => $doctor_name,
                                'experience' => $experience,
                                'image'=>$doctor_image,
                                'category_id'=>$final_cat_id,
                                'category_name'=>$cat_name,
                            'online_status'=>1
                            );
                        
               }
             
                }
                $doctor_data_1[]=$doctor_user_id;
             
            
            }
        } else {
            $dentist = array();
        }
         //Dentist End Here
         
         //Cardiologist Start Here
         $Where2 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(9|19|91),"';
        $doctor_data_2=array();
        $sql   =  sprintf("SELECT  DISTINCT doctor_list.user_id,doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id    WHERE $Where2 and  doctor_list.is_active = 1 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' AND FIND_IN_SET('" . $day . "',doctor_online.day) and doctor_online.status='0' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
            $category       = $row['category'];
                $cat=explode(",",$category);
            
                if(in_array($doctor_user_id,$doctor_data_2))
                {
                }
                else
                {
                    
               
               
                //print_r($cat);
               if(in_array('9', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'9')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $cardiologist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
                
               }
               else if(in_array('19', $cat))
               {
                 
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'19')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $cardiologist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
                
               }
               
               
               
               else if(in_array('91', $cat))
               {
                 
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'91')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $cardiologist[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
                
               }
              
             
                }
                $doctor_data_2[]=$doctor_user_id;
             
            
            }
        } else {
            $cardiologist = array();
        }
         //Cardiologist End Here
         
         
         
         //Obstetrician Start Here
         $Where2 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(12|73),"';
        $doctor_data_3=array();
        $sql   =  sprintf("SELECT  DISTINCT doctor_list.user_id,doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id  WHERE $Where2 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' AND FIND_IN_SET('" . $day . "',doctor_online.day) and doctor_online.status='0' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
            $category       = $row['category'];
                $cat=explode(",",$category);
            
                if(in_array($doctor_user_id,$doctor_data_3))
                {
                }
                else
                {
                    
               
               
                //print_r($cat);
               if(in_array('12', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'12')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $obstetrician[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
                
               }
               else if(in_array('73', $cat))
               {
                  
                        $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'73')");
                        $total_category = $query_sp->num_rows();
                        $get_sp=$query_sp->row_array();
                        $cat_name               = $get_sp['area_expertise'];
                        $cat_id  = $get_sp['id'];
                        
                      
                          $final_cat_id=$cat_id;
                      
                        $obstetrician[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'doctor_name' => $doctor_name,
                            'experience' => $experience,
                            'image'=>$doctor_image,
                            'category_id'=>$final_cat_id,
                            'category_name'=>$cat_name,
                            'online_status'=>1
                        );
                    
               }
             
             
                }
                $doctor_data_3[]=$doctor_user_id;
             
            
            }
        } else {
            $obstetrician = array();
        }
         //Obstetrician End Here
         
         
         
         //Neurologist Start Here
         
          $Where2 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(67|68|94),"';
        $doctor_data_3=array();
        $sql   =  sprintf("SELECT  DISTINCT doctor_list.user_id,doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id WHERE $Where2 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' AND FIND_IN_SET('" . $day . "',doctor_online.day) and doctor_online.status='0' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
            $category       = $row['category'];
                $cat=explode(",",$category);
            
                if(in_array($doctor_user_id,$doctor_data_2))
                {
                }
                else
                {
                    
               
               
                //print_r($cat);
               if(in_array('67', $cat))
               {
                  
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'67')");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                $cat_name               = $get_sp['area_expertise'];
                $cat_id  = $get_sp['id'];
                
              
                  $final_cat_id=$cat_id;
              
                $neurologist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>1
                );
                    
                
               }
               else if(in_array('68', $cat))
               {
                  
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'68')");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                $cat_name               = $get_sp['area_expertise'];
                $cat_id  = $get_sp['id'];
                
              
                  $final_cat_id=$cat_id;
              
                $neurologist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>1
                );
                    
                
               }
               
               
               
               else if(in_array('94', $cat))
               {
                 
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'94')");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                $cat_name               = $get_sp['area_expertise'];
                $cat_id  = $get_sp['id'];
                
              
                  $final_cat_id=$cat_id;
              
                $neurologist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>1
                );
                    
                
               }
              
             
                }
                $doctor_data_3[]=$doctor_user_id;
             
            
            }
        } else {
            $neurologist = array();
        }
         //Neurologist End Here
         
         
          //Homeopathic Start Here
         $Where2 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(52|53),"';
        $doctor_data_4=array();
        $sql   =  sprintf("SELECT  DISTINCT doctor_list.user_id,doctor_list.* FROM doctor_list INNER JOIN doctor_online ON doctor_list.user_id=doctor_online.doctor_id WHERE $Where2 and  doctor_list.is_active = 1 and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' AND FIND_IN_SET('" . $day . "',doctor_online.day) and doctor_online.status='0' ORDER BY RAND () $ty  ");
        
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_name         = $row['doctor_name'];
                $experience          = $row['experience'];
                $doctor_user_id      = $row['user_id'];
                 $doctor_image         = $row['image'];
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
            $category       = $row['category'];
                $cat=explode(",",$category);
            
                if(in_array($doctor_user_id,$doctor_data_4))
                {
                }
                else
                {
                    
               
               
                //print_r($cat);
               if(in_array('52', $cat))
               {
                  
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'52')");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                $cat_name               = $get_sp['area_expertise'];
                $cat_id  = $get_sp['id'];
                
              
                  $final_cat_id=$cat_id;
              
                $homeopathic[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>1
                );
                    
                
               }
               else if(in_array('53', $cat))
               {
                
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'53')");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                $cat_name               = $get_sp['area_expertise'];
                $cat_id  = $get_sp['id'];
                
              
                  $final_cat_id=$cat_id;
              
                $homeopathic[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>1
                );
                    
                
               }
             
             
                }
                $doctor_data_4[]=$doctor_user_id;
             
            
            }
        } else {
            $homeopathic = array();
        }
         //Homeopathic End Here
         
        
          if(!empty($genral)) 
         {
           $resultpost[] = $genral;  
         }
         if(!empty($gy)) 
         {
           $resultpost[] = $gy;  
         }
         if(!empty($dermatologist)) 
         {
           $resultpost[] = $dermatologist;  
         }
         if(!empty($ayur)) 
         {
           $resultpost[] = $ayur;  
         }
         if(!empty($ent)) 
         {
           $resultpost[] = $ent;  
         }
         if(!empty($dentist)) 
         {
           $resultpost[] = $dentist;  
         }
         if(!empty($cardiologist)) 
         {
           $resultpost[] = $cardiologist;  
         }
        if(!empty($obstetrician)) 
         {
           $resultpost[] = $obstetrician;  
         }
         if(!empty($neurologist)) 
         {
           $resultpost[] = $neurologist;  
         }
         if(!empty($homeopathic)) 
         {
           $resultpost[] = $homeopathic;  
         }
        
         if(empty($resultpost))
         {
             $resultpost=array();
         }
        return $resultpost;
    }
    
     public function doctor_online_detail($user_id,$doctor_user_id)
    {
        // genral Doctor Start Here
          date_default_timezone_set('Asia/Kolkata');
                    $current_date = date('H:i:s');
         $sqldi   =  "SELECT * from doctor_online  WHERE doctor_online.doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
        if ($countdi > 0) {
            $row=$querydi->row_array();
            
                $id           = $row['id']; 
                $chat         = $row['chat'];
                $video        = $row['video'];
                 $chat_fee         = $row['chat_fee'];
                $video_fee        = $row['video_fee'];
                 
               if($chat!=0)
               {
                $resultpost[] = array(
                    'id' => $id,
                    'chat' => $chat,
                    'chat_fee' => $chat_fee,
                    'image'=>""
                   
                );
               }
               if($video!=0)
               {
                   $resultpost[] = array(
                    'id' => $id,
                    'chat' => $video,
                    'chat_fee' => $video_fee,
                    'image'=>""
                   
                );
               }
            
        } else {
            $resultpost = array();
        }
         // genral Doctor End Here
      
         
         
        return $resultpost;
    }
    
     
    
    public function doctor_booking_check($user_id,$listing_id,$date,$time)
    {
          $this->un_block_doctor();
        $query2 = $this->db->query("select booking_id from doctor_booking_master where user_id = '$user_id' and listing_id='$listing_id' and booking_date='$date' and  from_time <= '$time' AND to_time >= '$time'  ");

            $count2 = $query2->num_rows();
            if ($count2 > 0) {
                $row2=$query2->row_array(); 
                 
                    $booking_id = $row2['booking_id'];
                    
                    $resultpost = array(
                        'status' => 200,
                        'message' => "success",
                        'booking_id' => $booking_id
                    );
                
            } else {
                 $resultpost = array(
                        'status' => 201,
                        'message' => "No Booking Available",
                        'booking_id' => ""
                    );
            }
            return $resultpost;
    }
    
    function un_block_doctor()
    {
        date_default_timezone_set('Asia/Kolkata');
        $current_date = date('H:i:s');
        $day=date('l');
        $sql   =  sprintf("SELECT doctor_id  FROM doctor_online_block  WHERE status='0' and end_time >='$current_date'");
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) 
        {
            foreach ($query->result_array() as $row) 
            {
                 
                $doctor_id = $row['doctor_id'];
                $data = array('block' => '0');
                $this->db->where('doctor_id', $doctor_id);
                $insert = $this->db->update('doctor_online', $data);
                          
                $booking_array = array('status' => 1);
                $updateStatus  = $this->db->where('doctor_id', $doctor_id)->update('doctor_online_block', $booking_array);     
                   
            }
            
        } 
      
        
        
    }
    
    
}
