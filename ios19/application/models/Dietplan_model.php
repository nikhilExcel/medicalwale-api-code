<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dietplan_model extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";

    public function check_auth_client() {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
        }
    }

    public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
        } else {
            if ($q->expired_at < date('Y-m-d H:i:s')) {
                return json_output(401, array('status' => 401, 'message' => 'Your session has been expired.'));
            } else {
                $updated_at = date('Y-m-d H:i:s');
               $expired_at = '2030-11-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array('expired_at' => $expired_at, 'updated_at' => $updated_at));
                return array('status' => 200, 'message' => 'Authorized.');
            }
        }
    }
    public function diet_likes_list($diet) {
        $query = $this->db->query("SELECT * FROM diet_dish WHERE veg_nonveg_egg='$diet' order by diet_name");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $dhlr_id = $row['id'];
                $diet_name = $row['diet_name'];
                $veg_nonveg_egg= $row['veg_nonveg_egg'];
              
                $resultpost[] = array(
                    'id' => $dhlr_id,
                    'diet_name' => $diet_name,
                    'veg_nonveg_egg'=>$veg_nonveg_egg
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    public function diet_dislikes_list($diet) {
        $query = $this->db->query("SELECT * FROM diet_dish WHERE veg_nonveg_egg='$diet' order by diet_name");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $dhlr_id = $row['id'];
                $diet_name = $row['diet_name'];
                $veg_nonveg_egg= $row['veg_nonveg_egg'];
              
                $resultpost[] = array(
                    'id' => $dhlr_id,
                    'diet_name' => $diet_name,
                    'veg_nonveg_egg'=>$veg_nonveg_egg
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    public function leads_followup_list($user_id,$book_id) {
        
        $query = $this->db->query("SELECT id FROM diet_leads WHERE user_id='$user_id'");
        $count = $query->num_rows();
      
        
        if ($count > 0) {
            
            $id = $query->row()->id;
           // echo "SELECT * FROM diet_leads_followup WHERE leads_id='$id' AND package_book_id='$book_id' order by id desc";
            $query1 = $this->db->query("SELECT * FROM diet_leads_followup WHERE leads_id='$id' AND package_book_id='$book_id' order by id desc");
            $count1 = $query1->num_rows();
              if ($count1 > 0) {
                    foreach ($query1->result_array() as $row) {
        
                        $resultpost[] = array(
                            'id' => $row['id'],
                            'leads_id' => $row['leads_id'],
                            'package_book_id' => $row['package_book_id'],
                            'followup_date'=>$row['followup_date'],
                            'next_followup_date'=>$row['next_followup_date'],
                            'next_followup_time'=>$row['next_followup_time'],
                            'comment'=>$row['comment']
                        );
                    }
              }
              else {
                $resultpost = array();
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function leads_followup_update($user_id,$id,$next_followup_date,$next_followup_time) {

         $result = array(
                            'next_followup_date'=>$next_followup_date,
                            'next_followup_time'=>$next_followup_time
                        );
            $this->db->where('id',$id);
            $this->db->update('diet_leads_followup',$result);
            $count1 = $this->db->affected_rows();
              if ($count1 > 0) {
                    $resultpost = array( 'status' => 201,
                                        'message' => 'success');
                                        
                    $notification = array('user_id' => $user_id,
                                'listing_id' => "",
                                'package_id' => $id,
                                'booking_id' => $id,
                               'order_id' => 0,
                               'title' => "MissBelly Followup",
                               'msg' => "User updated his followup.",
                               'notification_type' => "MissBelly_Followup",
                               'created_at' => date('Y-m-d H:i:s')
                               );
                               
                    $this->db->insert('diet_plan_notifications', $notification);  ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = 'Your diet package followup has been rescheduled successfully.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
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
                                            
                                        
              }
              else {
                $resultpost = array( 'status' => 200,
                                        'message' => 'Fail');
            }
         
        return $resultpost;
    }
    
    public function missbelly_packages_list($user_id) {
            $dietplan= "";
            $pdf_link= "";
            $curr_date = date('Y-m-d');
            $renew_show = "false";
            $resultpost1 = array();
            $resultpost2 = array();
            $resultpost = array();
            $query1 = $this->db->query("SELECT * FROM diet_user_package_history WHERE user_id='$user_id' order by id desc");
            $count1 = $query1->num_rows();
              if ($count1 > 0) {
                    foreach ($query1->result_array() as $row) {
                 
        /*
                        if($row['payment_status'] == 0)
                        {
                            $this->db->select('*');
                    		$this->db->from('diet_master_package');
                    		$this->db->where('id',$row['package_id']);
                    		$query = $this->db->get()->row(); 
                    		$package_month = $query->month;
                    		$package_followup = $query->follow_up;
                    		$package_price = $query->price;
                    		$is_it_week = $query->is_it_week;
                    		if($is_it_week == "1")
                    		{
                    		    $is_it_week = true;
                    		}
                    		else
                    		{
                    		    $is_it_week =false;
                    		}
                        }
                        else
                        {*/
                            $this->db->select('*');
                    		$this->db->from('medicalbot_missbellypackagedetails');
                    		$this->db->where('id',$row['package_id']);
                    		$query = $this->db->get()->row(); 
                    		$package_month = $query->month;
                    		$package_followup = $query->follow_up;
                    		$package_price = $query->price;
                    		$is_it_week = $query->is_it_week;
                    		if($is_it_week == "1")
                    		{
                    		    $is_it_week = true;
                    		}
                    		else
                    		{
                    		    $is_it_week =false;
                    		}
                       // }
                		
                		       
                        
                        $date_id =$row['id'];
                        if($row['complete_status']==0)
                        {
                            $dictionary_sql = $this->db->query("SELECT *,DATEDIFF(CURRENT_DATE(),booking_to) differ FROM diet_user_package_history WHERE id = $date_id ");
                            $differ = $dictionary_sql->row()->differ;
                            if($differ >=1 && $differ<=5)
                            {
                                $renew_show = "true";
                            }
                            
                            
                            $this->db->select('dietplan,pdf_link');
                    		$this->db->from('diet_user_pdf');
                            $this->db->where('leads_id',$row['leads_id']);
                            $this->db->where('user_id',$user_id);
                            $this->db->where('booking_id',$row['booking_id']);
                            $this->db->order_by('id','desc');
                            $this->db->limit('1');
                            $query = $this->db->get();
            
                            if($query->num_rows() > 0)
                            {
                                $dietplan= $query->row()->dietplan;
                                $pdf_link= "https://missbelly.medicalwale.com/diet_pdf/".$query->row()->pdf_link;
                            }
                        
                        
                        }
                        $resultpost1[] = array(
                            'id' => $row['id'],
                            'leads_id' => $row['leads_id'],
                            'package_id' => $row['package_id'],
                            'package_month' => $package_month,
                            'package_followup' => $package_followup,
                            'package_price' => $package_price,
                            'is_it_week' => $is_it_week,
                            'booking_from'=>$row['booking_from'],
                            'booking_to'=>$row['booking_to'],
                            'booking_id'=>$row['booking_id'],
                            'complete_status'=>$row['complete_status'],
                            'dietplan' => $dietplan,
                            'pdf_link' => $pdf_link
                           
                        );
                    }
              }
              $query2 = $this->db->query("SELECT * FROM diet_renew_packages WHERE user_id='$user_id' AND status=0 order by id desc LIMIT 1");
              $count2 = $query2->num_rows();
              if ($count2 > 0) {
                  
                
                    foreach ($query2->result_array() as $row) {
                        
                            $this->db->select('*');
                    		$this->db->from('medicalbot_missbellypackagedetails');
                    		$this->db->where('id',$row['package_id']);
                    		$query = $this->db->get()->row(); 
                    		$package_month = $query->month;
                    		$package_followup = $query->follow_up;
                    		$package_price = $query->price;
                    		$is_it_week = $query->is_it_week;
                    		if($is_it_week == "1")
                    		{
                    		    $is_it_week = true;
                    		}
                    		else
                    		{
                    		    $is_it_week =false;
                    		}
                    		
                        $resultpost2[] = array(
                            'id' => $row['id'],
                            'leads_id' => $row['leads_id'],
                            'package_id' => $row['package_id'],
                            'package_month' => $package_month,
                            'package_followup' => $package_followup,
                            'package_price' => $package_price,
                            'is_it_week' => $is_it_week,
                            'booking_from'=>$row['from_date'],
                            'booking_to'=>$row['to_date'],
                            'booking_id'=>$row['booking_id'],
                            'complete_status'=>"3",
                            'dietplan' => "",
                            'pdf_link' => ""
                            
                        );
                    }
              }
                 
              if(!empty($resultpost1) && !empty($resultpost2))
              {
                  //echo 'no branch';
                    $resultpost  =  array_merge($resultpost1,$resultpost2);
              }
              else if(!empty($resultpost1))
              {
                    $resultpost  =  $resultpost1;  
              }
              
         return     array( "status"=> 200,
                    "message"=> "success",
                    "count"=> count($resultpost),
                    "renewed" => $renew_show,
                    "data" => $resultpost);
         
    }
    
     public function missbelly_diet_list_school($user_id) {
            $curr_date = date('Y-m-d');
            $resultpost = array();
            $query1 = $this->db->query("SELECT * FROM individual_diet_plan_master_school WHERE user_id='$user_id'");
            $count1 = $query1->num_rows();
              if ($count1 > 0) {
                    foreach ($query1->result_array() as $row) {
                           if(!empty($row['pdf_link']))
                           {
                                $date_id = $row['id'];
                                $dietplan = $row['dietplan'];
                                $for_whome = $row['for_whome'];
                                $for_name = $row['for_name'];
                                $pdf_link= "https://missbelly.medicalwale.com/school_diet_pdf/".$row['pdf_link'];
                                
                                $resultpost[] = array(
                                    'id' => $row['id'],
                                    'leads_id' => $row['lead_id'],
                                    'dietplan' => $dietplan,
                                    'for_whom_relation' => $for_whome,
                                    'for_whom_name' => $for_name,
                                    'pdf_link' => $pdf_link
                                   
                                );
                            }
                    }
              }
             
            return     array( "status"=> 200,
                    "message"=> "success",
                    "count"=> count($resultpost),
                    "data" => $resultpost);
         
    }
    
    public function dietplan_booking_old($user_id, $package_id, $gst, $amount, $booking_id){
        
        date_default_timezone_set('Asia/Kolkata');
        $created_at   = date('Y-m-d H:i:s');
        $booking_date = date('Y-m-d');
        $booking_time = date('H:i:s');
        $status = 5;
        //$joining_date = date('Y-m-d');
        $query        = $this->db->query("SELECT * FROM users WHERE id = '$user_id'");
        $info        = $query->row();
        $user_name=$info->name;
        $mobile=$info->phone;
                    $lab_booking = array(
                'user_id' => $user_id,
                'booking_id' => $booking_id,
                'package_id' => $package_id,
                'user_name' => $info->name,
                'user_email' => $info->email,
                'user_mobile' => $info->phone,
                'user_gender' => $info->gender,
                'vendor_id' => 37,
                'booking_date' => $created_at,
                'status' => $status,
                'trail_booking_date' => $booking_date,
                'trail_booking_time' => $booking_time,
                'payment_mode' => 'online_payment',
            );
            $rst         = $this->db->insert('booking_master', $lab_booking);
            $appointment_id = $this->db->insert_id();
            
            $querys        = $this->db->query("SELECT id FROM diet_leads WHERE user_id = '$user_id'");
            if($querys->num_rows()==0)
            {
                 $new_deit_entry = array(
                        'user_id' => $user_id,
                        'enroll_from' => "Payment",
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $user_id
                        );
                        
                      $this->db->insert('diet_leads', $new_deit_entry); 
                     $lead_id =  $this->db->insert_id();
            }
            else
            {
                $infos  = $querys->row();
                $lead_id=$infos->id;
            
            }
                 
            //diet_packages_booked
                    $month = "";
                    $this->db->select('month');
            		$this->db->from('medicalbot_missbellypackagedetails');
            		$this->db->where('id',$package_id);
                    $query1 = $this->db->get();
                    $month = $query1->row()->month;
                    
                    $month1 ="+$month";
                    $effectiveDate1 = $booking_date; //date('Y-m-d');        
                    $effectiveDate = date('Y-m-d', strtotime("$month1 months", strtotime($effectiveDate1)));
                    
                    
                /*  $new_booking_lead = array(
                       'user_id' => $user_id,
                       'package_id' => '1',
                       'leads_id' => $lead_id,
                       'created_at' =>date('Y-m-d H:i:s'),
                       'booking_from_date' => date('Y-m-d'),
                        'booking_to_date' => $effectiveDate,
                       'created_by' =>$user_id,
                       'status' => '1'
                      );
                  
                   $this->db->insert('diet_packages_booked', $new_booking_lead);*/ 
                 
                    $notification = array('user_id' => $user_id,
                                'listing_id' => "",
                                'package_id' => $package_id,
                                'booking_id' => $booking_id,
                               'order_id' => 0,
                               'order_status' => 'paid',
                               'title' => "Paid Diet Package Booking",
                               'msg' => "Diet Package Booked Successfully.",
                               'notification_type' => "Paid Diet Booking",
                               'created_at' => date('Y-m-d H:i:s')
                               );
                    $this->db->insert('diet_plan_notifications', $notification);   
                     $note_id =  $this->db->insert_id(); 
                  
                  $img_url      = 'https://s3.amazonaws.com/medicalwale/images/img/missbelly.png';
                  $tag          = 'text';
            
                     $notification_array = array(
                      'title' => "Paid Diet Package Booking",
                      'msg'  => "Diet Package Booked Successfully.",
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => date('Y-m-d'),
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => "",
                      'booking_id'  => $booking_id,
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'package_name' => $package_id,
                       'notification_type'  => "Paid Diet Booking",
                       'notification_date'  => date('Y-m-d H:i:s')
                       
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
         
                    $booking_history = array('user_id' => $user_id,
                                'leads_id' => $lead_id,
                                'package_id' => $package_id,
                                'booking_id' => $booking_id,
                                'booking_from' => date('Y-m-d'),
                                'booking_to' => $effectiveDate,
                                'gst' => $gst,
                                'amount' => $amount,
                                'payment_status' => 1,
                                'created_at' => date('Y-m-d H:i:s')
                               );
                               
                    $this->db->insert('diet_user_package_history', $booking_history);
                     $diet_booking_id =  $this->db->insert_id(); 
                    
            //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file      = $profile_query->source;
                $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr               = $user_plike->row_array();
           // $usr_name             = $user_name;
            $msg                  = $user_name . ',your diet package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = 'https://s3.amazonaws.com/medicalwale/images/img/missbelly.png';
                $tag          = 'text';
                $key_count    = '1';
                $title_package = "Diet Package";
                $msg_package = $user_name . ', diet package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
                $this->send_gcm_notify_dietpackage($title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
            }
            //end
            ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ', diet package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
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
           if ($diet_booking_id) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id
                );
           }
        
    }
    
     public function dietplan_booking($user_id, $package_id, $gst, $amount, $booking_id,$coupon_id){
        
        date_default_timezone_set('Asia/Kolkata');
        $created_at   = date('Y-m-d H:i:s');
        $booking_date = date('Y-m-d');
        $booking_time = date('H:i:s');
        $status = 5;
        //$joining_date = date('Y-m-d');
        $query        = $this->db->query("SELECT * FROM users WHERE id = '$user_id'");
        $info        = $query->row();
        $user_name=$info->name;
        $mobile=$info->phone;
                    $lab_booking = array(
                'user_id' => $user_id,
                'booking_id' => $booking_id,
                'package_id' => $package_id,
                'user_name' => $info->name,
                'user_email' => $info->email,
                'user_mobile' => $info->phone,
                'user_gender' => $info->gender,
                'vendor_id' => 37,
                'booking_date' => $created_at,
                'status' => $status,
                'trail_booking_date' => $booking_date,
                'trail_booking_time' => $booking_time,
                'payment_mode' => 'online_payment',
            );
            $rst         = $this->db->insert('booking_master', $lab_booking);
            $appointment_id = $this->db->insert_id();
            
            $querys        = $this->db->query("SELECT id FROM diet_leads WHERE user_id = '$user_id'");
            if($querys->num_rows()==0)
            {
                 $new_deit_entry = array(
                        'user_id' => $user_id,
                        'enroll_from' => "Payment",
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $user_id
                        );
                        
                      $this->db->insert('diet_leads', $new_deit_entry); 
                     $lead_id =  $this->db->insert_id();
            }
            else
            {
                $infos  = $querys->row();
                $lead_id=$infos->id;
            
            }
                 
            //diet_packages_booked
                    $month = "";
                    $this->db->select('month');
            		$this->db->from('medicalbot_missbellypackagedetails');
            		$this->db->where('id',$package_id);
                    $query1 = $this->db->get();
                    $month = $query1->row()->month;
                    
                    $month1 ="+$month";
                    $effectiveDate1 = $booking_date; //date('Y-m-d');        
                    $effectiveDate = date('Y-m-d', strtotime("$month1 months", strtotime($effectiveDate1)));
                    
                    
                /*  $new_booking_lead = array(
                       'user_id' => $user_id,
                       'package_id' => '1',
                       'leads_id' => $lead_id,
                       'created_at' =>date('Y-m-d H:i:s'),
                       'booking_from_date' => date('Y-m-d'),
                        'booking_to_date' => $effectiveDate,
                       'created_by' =>$user_id,
                       'status' => '1'
                      );
                  
                   $this->db->insert('diet_packages_booked', $new_booking_lead);*/ 
                 
                    $notification = array('user_id' => $user_id,
                                'listing_id' => "",
                                'package_id' => $package_id,
                                'booking_id' => $booking_id,
                               'order_id' => 0,
                               'order_status' => 'paid',
                               'title' => "Paid Diet Package Booking",
                               'msg' => "Diet Package Booked Successfully.",
                               'notification_type' => "Paid Diet Booking",
                               'created_at' => date('Y-m-d H:i:s')
                               );
                    $this->db->insert('diet_plan_notifications', $notification);   
                     $note_id =  $this->db->insert_id(); 
                  
                  $img_url      = 'https://s3.amazonaws.com/medicalwale/images/img/missbelly.png';
                  $tag          = 'text';
            
                     $notification_array = array(
                      'title' => "Paid Diet Package Booking",
                      'msg'  => "Diet Package Booked Successfully.",
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => date('Y-m-d'),
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => "",
                      'booking_id'  => $booking_id,
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'package_name' => $package_id,
                       'notification_type'  => "Paid Diet Booking",
                       'notification_date'  => date('Y-m-d H:i:s')
                       
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
         
                    $booking_history = array('user_id' => $user_id,
                                'leads_id' => $lead_id,
                                'package_id' => $package_id,
                                'booking_id' => $booking_id,
                                'booking_from' => date('Y-m-d'),
                                'booking_to' => $effectiveDate,
                                'gst' => $gst,
                                'amount' => $amount,
                                'payment_status' => 1,
                                'created_at' => date('Y-m-d H:i:s')
                               );
                               
                    $this->db->insert('diet_user_package_history', $booking_history);
                     $diet_booking_id =  $this->db->insert_id(); 
                     
                     
                     
                     if($coupon_id > 0){
                         $user_data_dh    = array('use_status' => 1               );
                              $updateStatus = $this->db->where('coupon',$coupon_id)->where('user_id',$user_id)->update('use_coupon', $user_data_dh);
                      }
                    
            //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file      = $profile_query->source;
                $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr               = $user_plike->row_array();
           // $usr_name             = $user_name;
            $msg                  = $user_name . ',your diet package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = 'https://s3.amazonaws.com/medicalwale/images/img/missbelly.png';
                $tag          = 'text';
                $key_count    = '1';
                $title_package = "Diet Package";
                $msg_package = $user_name . ', diet package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
                $this->send_gcm_notify_dietpackage($title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
            }
            //end
            ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ', diet package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
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
           if ($diet_booking_id) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id
                );
           }
        
    }
    public function send_gcm_notify_dietpackage($title, $reg_id, $msg, $img_url, $tag, $agent, $diet_booking_id) {

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
                "notification_type" => 'paid_diet_plan',
                "notification_date" => $date,
                "booking_id" => $diet_booking_id
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
    public function renew_package($user_id,$package_id,$from_date,$gst,$amount,$coupon_id){
        date_default_timezone_set('Asia/Kolkata');
        $created_at   = date('Y-m-d H:i:s');
        $booking_date = date('Y-m-d');
        $booking_time = date('H:i:s');
        $booking_id = date('YmdHis');
        
        $querys        = $this->db->query("SELECT id FROM diet_leads WHERE user_id = '$user_id'");
        $lead_id =  $querys->row()->id;
        
        $month = "";
                    $this->db->select('month');
            		$this->db->from('medicalbot_missbellypackagedetails');
            		$this->db->where('id',$package_id);
                    $query1 = $this->db->get();
                    $month = $query1->row()->month;
                    
                    $month1 ="+$month";
                    $effectiveDate1 = $booking_date; //date('Y-m-d');        
                    $effectiveDate = date('Y-m-d', strtotime("$month1 months", strtotime($effectiveDate1)));
                    
                    $lab_booking = array(
                        'leads_id' => $lead_id,
                        'user_id' => $user_id,
                        'booking_id' => $booking_id,
                        'package_id' => $package_id,
                        'from_date' => $from_date,
                        'to_date' => $effectiveDate,
                        'gst' => $gst,
                        'amount' => $amount
                        
            );
            $rst         = $this->db->insert('diet_renew_packages', $lab_booking);
            $renew_id = $this->db->insert_id();
            
     /*
        $query        = $this->db->query("SELECT * FROM users WHERE id = '$user_id'");
        $info        = $query->row();
        $user_name=$info->name;
        $mobile=$info->phone;
                    $lab_booking = array(
                'user_id' => $user_id,
                'booking_id' => $booking_id,
                'package_id' => $package_id,
                'user_name' => $info->name,
                'user_email' => $info->email,
                'user_mobile' => $info->phone,
                'user_gender' => $info->gender,
                'vendor_id' => 37,
                'booking_date' => $created_at,
                'status' => $status,
                'trail_booking_date' => $booking_date,
                'trail_booking_time' => $booking_time,
                'payment_mode' => 'online_payment',
            );
            $rst         = $this->db->insert('booking_master', $lab_booking);
            $appointment_id = $this->db->insert_id();
            
            $querys        = $this->db->query("SELECT id FROM diet_leads WHERE user_id = '$user_id'");
            $lead_id =  $querys->row()->id;
                 
          
                    $month = "";
                    $this->db->select('month');
            		$this->db->from('medicalbot_missbellypackagedetails');
            		$this->db->where('id',$package_id);
                    $query1 = $this->db->get();
                    $month = $query1->row()->month;
                    
                    $month1 ="+$month";
                    $effectiveDate1 = $booking_date; //date('Y-m-d');        
                    $effectiveDate = date('Y-m-d', strtotime("$month1 months", strtotime($effectiveDate1)));
               
                    $notification = array('user_id' => $user_id,
                                'listing_id' => "",
                                'package_id' => $package_id,
                                'booking_id' => $booking_id,
                               'order_id' => 0,
                               'order_status' => 'paid',
                               'title' => "Paid Diet Package Booking",
                               'msg' => "Diet Package Booked Successfully.",
                               'notification_type' => "Paid Diet Booking",
                               'created_at' => date('Y-m-d H:i:s')
                               );
                    $this->db->insert('diet_plan_notifications', $notification);   
                     $note_id =  $this->db->insert_id(); 
                  
                              
                    $booking_history = array('user_id' => $user_id,
                                'leads_id' => $lead_id,
                                'package_id' => $package_id,
                                'booking_id' => $booking_id,
                                'booking_from' => date('Y-m-d'),
                                'booking_to' => $effectiveDate,
                                'gst' => $gst,
                                'amount' => $amount,
                                'payment_status' => 1,
                                'created_at' => date('Y-m-d H:i:s')
                               );
                               
                    $this->db->insert('diet_user_package_history', $booking_history);
                     $diet_booking_id =  $this->db->insert_id(); 
         */
         
          if($coupon_id > 0)
          {
             $user_data_dh    = array('use_status' => 1               );
                  $updateStatus = $this->db->where('coupon',$coupon_id)->where('user_id',$user_id)->update('use_coupon', $user_data_dh);
          }
          if ($renew_id) {
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file      = $profile_query->source;
                $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr               = $user_plike->row_array();
           // $usr_name             = $user_name;
            $msg                  = $getusr['name'] . ',your diet package has been Renewed successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $getusr['name'] . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $title_package = "Renewed Diet Package";
                $msg_package = $getusr['name'] . ', diet package has been Renewed successfully and Booking Id :' . $booking_id . ' for future reference.';
                $this->send_gcm_notify_dietpackage($title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $renew_id);
            }
            //end
            ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ', diet package has been Renewed successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
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
          
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id
                );
           }
           else
           {
                return array(
                    'status' => 200,
                    'message' => 'fail'
                  
                );
           }
        
    }
   /* public function up($user_id) {

        $query1 = $this->db->query("SELECT leads_id,id FROM diet_user_package_history");
        $count1 = $query1->num_rows();
        if ($count1 > 0) {
            foreach ($query1->result_array() as $row) {
                 $leads_id = $row['leads_id'];
                 $id = $row['id'];
                 $query2 = $this->db->query("Update diet_leads_followup set package_book_id='$id' where leads_id='$leads_id' ");
            }
        }
        
    }*/
    
    public function buy_diet_plan($user_id, $package_id, $gst, $amount,$coupon_id,$transaction_id,$transaction_status,$payment_method){
        $this->load->model('LedgerModel');
        $return = array();
        $status = 0;
        $booking_creted_id = "";
        $booking_id = date('YmdHis');
        
        // get all details to create ledger and dietplan_booking - done
        // create_ledger payment accepted - done
        // if payment success - done
        //   -- first call dietplan_booking model - done
        //      -- successfully inserted - done
        //          -- create_ledger diet plan given - done
        //      -- failed to insert - done
        //          -- Nothing - done
        // if payment failed - done
        //   -- Nothing - done
        
        
        if($transaction_status == 1){
            $transactionTstusWords = "successfully completed";
        } else if($transaction_status == 2){
            $transactionTstusWords = "failed";
        } else if($transaction_status == 3){
            $transactionTstusWords = "cancelled"; 
        } else {
            $transactionTstusWords = "";
        }
        
        // missbelly package : payment
        $user_id_type = $mw_id = $mw_id_type = 0;
        $invoice_no = $booking_id;
        $user_comments = "";
        $mw_comments = "Missbelly package payment ".$transactionTstusWords;
        $vendor_comments = "";
        $credit = "";
        $debit = $amount;
        $transaction_of = 2; // entry for amount
        $trans_status = $transaction_status;
        $transaction_date = "";
        $order_type = 8; // for missbelly
        $vendor_id = 37;
        $data = array();
        // WORKING
        $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $mw_id, $mw_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_id,$data);
    //   print_r($res); die();
        if($transaction_status == 1){
            
            $resp = $this->dietplan_model->dietplan_booking($user_id, $package_id, $gst, $amount, $booking_id,$coupon_id);
            
            if(array_key_exists('status',$resp) && $resp['status'] == 201){
               $booking_creted_id = $resp['booking_id'];
                // missbelly package : payment
                $user_id_type = $mw_id = $mw_id_type = 0;
                $invoice_no = $booking_id;
                $user_comments = "";
                $mw_comments = "Missbelly diet package";
                $vendor_comments = "";
                $credit = $amount;
                $debit = "";
                $transaction_of = 3; // entry for service
                $trans_status = 1;
                $transaction_date = "";
                $order_type = 8; // for missbelly
                $payment_method = "";
                $vendor_id = 37;
                $data = array();
                
                // WORKING
                $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $user_id_type, $mw_id, $mw_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_id,$data);
              
                $status = 1;
            } 
        } 
        
        $return = array('status' => $status,'booking_id' => $booking_creted_id);
        
        return $return;
        
    }
  
}
