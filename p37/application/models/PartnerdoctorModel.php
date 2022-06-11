<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PartnerdoctorModel extends CI_Model { 
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
    
     public function get_pdf_version() {
        $api_url = "https://medicalwale.s3.amazonaws.com/images/doctor_prescription_live/";
        //$api_url = "https://medicalwale.s3.amazonaws.com/images/doctor_prescription_sandbox/";
       
        return $api_url;
    }
     public function get_url_version() {
       
        $api_url = "https://doctor.medicalwale.com/doctor_prescription.php?id=";
        //$api_url = "http://vendorsandbox.medicalwale.com/doctor/doctor_prescription.php?id=";
        return $api_url;
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
                $expired_at = date("Y-m-d H:i:s", strtotime("+1 days"));//'2020-12-12 08:57:58';
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
    
     public function patient_mobile($doctor_id,$mobile) {
        $query = $this->db->query("SELECT id  FROM `users` WHERE phone='$mobile'");
        $count = $query->num_rows();
        if ($count > 0) 
        {
            $row=$query->row_array();
            $user_id = $row['id']; 
            $query1 = $this->db->query("SELECT *  FROM `doctor_patient` WHERE `doctor_id`='$doctor_id' and user_id='$user_id'");
            $count1 = $query1->num_rows();
            if ($count1 > 0) 
                {
                    $row1=$query1->row_array(); 
                    
                        $patient_name   = $row1['patient_name'];
                        $email          = $row1['email'];
                        $gender         = $row1['gender'];
                        $date_of_birth  = $row1['date_of_birth'];
                        $blood_group    = $row1['blood_group'];
                        $address        = $row1['address'];
                        $city           = $row1['city'];
                        $state          = $row1['state'];
                        $pincode        = $row1['pincode'];
                        $medical        = $row1['medical_profile'];
                        
                        if(empty($email))
                        {
                            $email="";
                        }
                        if(empty($gender))
                        {
                            $gender="";
                        }
                        if(empty($date_of_birth))
                        {
                            $date_of_birth="";
                        }
                        if(empty($blood_group))
                        {
                            $blood_group="";
                        }
                        if(empty($address))
                        {
                            $address="";
                        }
                        if(empty($city))
                        {
                            $city="";
                        }
                        if(empty($state))
                        {
                            $state="";
                        }
                        if(empty($pincode))
                        {
                            $pincode="";
                        }
                         if(empty($medical))
                        {
                            $medical="";
                        }
                        
                        $resultpost[] = array(
                            "patient_id"=>$user_id,
                            "patient_name" => $patient_name,
                            "email"=>$email,
                            "gender"=>$gender,
                            "date_of_birth" => $date_of_birth,
                            "blood_group"=>$blood_group,
                            "address"=>$address,
                            "city" => $city,
                            "state"=>$state,
                            "pincode"=>$pincode,
                            "medical_profile"=>$medical,
                            "contact_no" => $mobile,
                            
                        );
                    
                    
                }  
            else
              {
                $query1 = $this->db->query("SELECT *  FROM `doctor_patient` WHERE user_id='$user_id'");
                $count1 = $query1->num_rows();
                if($count1 > 0)
                {
                    $row1=$query1->row_array();  
                    date_default_timezone_set('Asia/Kolkata');
                    $created_date = date('Y-m-d H:i:s');
                    $patient_name   = $row1['patient_name'];
                    $email          = $row1['email'];
                    $gender         = $row1['gender'];
                    $date_of_birth  = $row1['date_of_birth'];
                    $blood_group    = $row1['blood_group'];
                    $address        = $row1['address'];
                    $city           = $row1['city'];
                    $state          = $row1['state'];
                    $pincode        = $row1['pincode'];
                    $medical        = $row1['medical_profile'];
                    if(empty($email))
                            {
                                $email="";
                            }
                    if(empty($gender))
                            {
                                $gender="";
                            }
                    if(empty($date_of_birth))
                            {
                                $date_of_birth="";
                            }
                    if(empty($blood_group))
                            {
                                $blood_group="";
                            }
                    if(empty($address))
                            {
                                $address="";
                            }
                    if(empty($city))
                            {
                                $city="";
                            }
                    if(empty($state))
                            {
                                $state="";
                            }
                    if(empty($pincode))
                            {
                                $pincode="";
                            }
                    if(empty($medical))
                            {
                                $medical="";
                            }
                            
                    $data = array(
                                    'user_id'=>$user_id,
                                    'doctor_id' => $doctor_id,
                                    'patient_name' => $patient_name,
                                    'address' => $address,
                                    'state' => $state,
                                    'city' => $city,
                                    'pincode' => $pincode,
                                    'contact_no' => $mobile,
                                    'gender' => $gender,
                                    'date_of_birth' => $date_of_birth,
                                    'blood_group' => $blood_group,
                                    'medical_profile' => $medical,
                                    'email' => $email,
                        			'created_date' => $created_date,
                	            );
                    $patient_insert = $this->db->insert('doctor_patient', $data);
                    $p_id = $this->db->insert_id();
                    if ($patient_insert) 
                       {
                          $resultpost[] = array(
                                "patient_id"=>$user_id,
                                "patient_name" => $patient_name,
                                "email"=>$email,
                                "gender"=>$gender,
                                "date_of_birth" => $date_of_birth,
                                "blood_group"=>$blood_group,
                                "address"=>$address,
                                "city" => $city,
                                "state"=>$state,
                                "pincode"=>$pincode,
                                "medical_profile"=>$medical,
                                "contact_no" => $mobile
                            );
                    } 
                }
                else
                  {
                       $query3 = $this->db->query("SELECT *  FROM `users` WHERE id='$user_id'");
                       $count3 = $query->num_rows();
                       if ($count3 > 0) 
                          {
                            $row3=$query3->row_array();
                            date_default_timezone_set('Asia/Kolkata');
                            $created_date = date('Y-m-d H:i:s');
                            $patient_name   = $row3['name'];
                            $email          = $row3['email'];
                            $gender         = $row3['gender'];
                            $date_of_birth  = $row3['dob'];
                            $blood_group    = $row3['blood_group'];
                            $address        = $row3['map_location'];
                            $city           = $row3['city'];
                            $state          = "";
                            $pincode        = "";
                            $medical        = "";
                            if(empty($email))
                                    {
                                        $email="";
                                    }
                            if(empty($gender))
                                    {
                                        $gender="";
                                    }
                            if(empty($date_of_birth))
                                    {
                                        $date_of_birth="";
                                    }
                            if(empty($blood_group))
                                    {
                                        $blood_group="";
                                    }
                            if(empty($address))
                                    {
                                        $address="";
                                    }
                            if(empty($city))
                                    {
                                        $city="";
                                    }
                        
                    $data = array(
                                    'user_id'=>$user_id, 
                                    'doctor_id' => $doctor_id,
                                    'patient_name' => $patient_name,
                                    'address' => $address,
                                    'state' => $state,
                                    'city' => $city,
                                    'pincode' => $pincode,
                                    'contact_no' => $mobile,
                                    'gender' => $gender,
                                    'date_of_birth' => $date_of_birth,
                                    'blood_group' => $blood_group,
                                    'medical_profile' => $medical,
                                    'email' => $email,
                        			'created_date' => $created_date,
                	            );
                    $patient_insert = $this->db->insert('doctor_patient', $data);
                    $p_id = $this->db->insert_id();
                    if ($patient_insert) 
                       {
                          $resultpost[] = array(
                                "patient_id"=>$user_id,
                                "patient_name" => $patient_name,
                                "email"=>$email,
                                "gender"=>$gender,
                                "date_of_birth" => $date_of_birth,
                                "blood_group"=>$blood_group,
                                "address"=>$address,
                                "city" => $city,
                                "state"=>$state,
                                "pincode"=>$pincode,
                                "medical_profile"=>$medical,
                                "contact_no" => $mobile
                            );
                    } 
                          }     
                  }
                 
              }
        } 
        else 
        {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function card_type($doctor_id,$type) 
    {
        if($type=="1")
        {
            //cardDetails
            $data=array();
            $sql2             = sprintf("SELECT card_id,card_name FROM cardDetails WHERE p_cardid='2'");
            $query_practices  = $this->db->query($sql2);
            foreach ($query_practices->result_array() as $get_pract) 
                   {
                        $card_id            = $get_pract['card_id'];
                        $card_name          = $get_pract['card_name'];
                        $data[] = array(
                                        'card_id' => $card_id,
                                        'card_name' => $card_name
                                        );
                   }    
            $resultpost[]=array(
                          'id'=>'2',
                          'cat_name'=>'CREDIT CARD',
                          'data'=> $data
                         );
                         
            $data1=array();
            $sql21             = sprintf("SELECT card_id,card_name FROM cardDetails WHERE p_cardid='5'");
            $query_practices1  = $this->db->query($sql21);
            foreach ($query_practices1->result_array() as $get_pract1) 
                   {
                        $card_id1            = $get_pract1['card_id'];
                        $card_name1          = $get_pract1['card_name'];
                        $data1[] = array(
                                        'card_id' => $card_id1,
                                        'card_name' => $card_name1
                                        );
                   }           
                         
             $resultpost[]=array(
                          'id'=>'5',
                          'cat_name'=>'DEBIT CARD',
                          'data'=> $data1
                         );             
        }
         if($type=="2")
        {
           
            $resultpost=array();
            $sql2             = sprintf("SELECT card_id,card_name FROM cardDetails WHERE p_cardid='1'");
            $query_practices  = $this->db->query($sql2);
            foreach ($query_practices->result_array() as $get_pract) 
                   {
                        $card_id            = $get_pract['card_id'];
                        $card_name          = $get_pract['card_name'];
                        $data2[] = array(
                                        'card_id' => $card_id,
                                        'card_name' => $card_name
                                        );
                   } 
                   
                    $resultpost[]=array(
                          'id'=>'101',
                          'cat_name'=>'Wallet',
                          'data'=> $data2
                         ); 
                    
        }
       
        return $resultpost;
    }
    
   public function payment_process($booking_id,$amount,$discount,$card_type,$card_sub_type,$carditdetails,$debitdetails,$walletdetails,$user_id,$doctor_id,$discount_type,$finalamount,$savedamount)
   {
         if ($card_type == "1") {
            
            
            if ($card_sub_type == 2) {
                
                $trans_mode = $card_sub_type;
                
                $transaction_sub_type = $carditdetails;
                
            } else {
                
                $trans_mode = $card_sub_type;
                
                $transaction_sub_type = $debitdetails;
                
            }
            
            } 
         else if ($card_type == "101") {
            
            $trans_mode = 1;
            
            $transaction_sub_type = $walletdetails;
            
            } 
         else if ($card_type == "3") {
            $trans_mode           = 3;
            $payment_status= 5;
            $transaction_sub_type = null;
            }
            
            
        $trans_type    = 1;
        $trans_time    = date('Y-m-d H:i:s');
        $randnumdebit  = rand(1111111111, 9999999999);
        $booking_id    = $booking_id;
        $data          = array(
            'user_id' => $user_id,
            'listing_id' => $doctor_id,
            'trans_id' => $randnumdebit,
            'order_id' => $booking_id,
            'trans_type' => $trans_type,
            'trans_time' => $trans_time,
            'amount' => $amount,
            'discount' => $discount,
            'amount_saved' => $savedamount,
            'discount_rupee' => $finalamount,
            'vendor_category' => '5',
            'authenticate' => '1',
            'trans_mode' => $trans_mode,
            'transaction_sub_type' => $transaction_sub_type,
            'authenticate' => 1
           
        );
        $trans_id      = $randnumdebit;
        
        //added for point entry 
        $trans_point_type = 4;
        $randnum          = rand(1111111111, 9999999999);
        $data_point       = array(
             'user_id' => $user_id,
            'listing_id' => $doctor_id,
            'trans_id' => $randnumdebit,
            'order_id' => $booking_id,
            'trans_type' => $trans_point_type,
            'trans_time' => $trans_time,
            'amount' => $amount,
            'discount' => $discount,
            'amount_saved' => $savedamount,
            'discount_rupee' => $finalamount,
            'vendor_category' => '5',
            'authenticate' => '1',
            'trans_mode' => $trans_mode,
            'transaction_sub_type' => $transaction_sub_type,
            'authenticate' => 1
        );
        
        //added for debit entry
        $randnumdebit     = rand(1111111111, 9999999999);
        $trans_point_type = 0;
        $data_credit      = array(
             'user_id' => $user_id,
            'listing_id' => $doctor_id,
            'trans_id' => $randnumdebit,
            'order_id' => $booking_id,
            'trans_type' => $trans_point_type,
            'trans_time' => $trans_time,
            'amount' => $amount,
            'discount' => $discount,
            'amount_saved' => $savedamount,
            'discount_rupee' => $finalamount,
            'vendor_category' => '5',
            'authenticate' => '1',
            'trans_mode' => $trans_mode,
            'transaction_sub_type' => $transaction_sub_type,
            'authenticate' => 1
        );
        
        $addcustomercoupon  = $this->addcustomercoupon($data, $data_point, $data_credit);
        
        $booking_master_array=array('status'=>5);
        
        $this->db->where('booking_id', $booking_id);
        $insert = $this->db->update('doctor_booking_master', $booking_master_array);
        
        $upadte_user_points = array(
            'user_id' => $user_id,
            'order_id' => $booking_id,
            'trans_id' => $randnum,
            'points' => $finalamount,
            'created_at' => $trans_time,
            'expire_at' => $trans_time,
            'status' => 'active'
        );
        $this->db->insert('user_points', $upadte_user_points);    
         return array(
                    'status' => 200,
                    'message' => 'success'
                );   
            
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
	   public function patient_appointment($doctor_id, $agent,$clinic_id)
    {
        if($agent=="ios")
        {
             $clinic1_slott=array();
             $wh="";
             if(empty($clinic_id))
             {
                 $wh .="";
             }
             else
             {
                 $wh .=" and id ='$clinic_id'";
             }
                $sql2             = sprintf("SELECT `id`, `clinic_name`,consultation_charges FROM doctor_clinic WHERE doctor_id='$doctor_id' $wh ");
              
                $query_practices  = $this->db->query($sql2);
                $total_practices  = $query_practices->num_rows();
                if ($total_practices > 0) {
                    foreach ($query_practices->result_array() as $get_pract) {
                        $clinic_id            = $get_pract['id'];
                        
                        $clinic_name          = $get_pract['clinic_name'];
                        
                        $charge=$get_pract['consultation_charges'];
                       
                      
                        
        
            $fees=$charge;
       if(empty($fees))
       {
           $fees=0;
       }
        $todayDay  = date('l');
        $dat_new=date('Y-m-d');
        $todayDate = date('Y-m-d H:i:s');
        $date1 =date('Y-m-d H:i:s', strtotime($todayDate));
        $time_slott=array();
        $time_slots=array();
         $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";
        for ($i = 0; $i < 7; $i++) {
            if($i==0)
            {
                $a=0;
            }
            else
            {
                $a=1;
            }
            $todayDate            =  date('Y-m-d', strtotime($todayDate . ' +'.$a .'day'));
            $todayDay             = date('l', strtotime($todayDate));
            $time_array           = array();
            $time_array1          = array();
            $time_array2          = array();
            $time_array3          = array();
            $time_slott           = array();
          
            
            // doctor_slot_details
            
           $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = 'visit' AND `day` = '$todayDay'");
             
            $count_day_time_slots = $day_time_slots->num_rows();
            foreach ($day_time_slots->result_array() as $row) {
               
                     $timeSlot              = $row['timeSlot'];
              $from_time             = $row['from_time'];
                $to_time               = $row['to_time'];
              if($dat_new== $todayDate)
              {
                
                // $status                = $row['status'];
                  $date2 =date('Y-m-d H:i:s', strtotime($from_time));
                   $date1;

            if ($date2 > $date1)
            {
              $day_time_status = $this->db->query("SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
              $count_day_time_status = $day_time_status->num_rows();
                if ($count_day_time_status) {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    }
                } else {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    }
                }
                //echo "SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$dhaval'"; echo "<br>";
            }
            else
            {
                
            }
   
   }
   else
   {
       $day_time_status       = $this->db->query("SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
              $count_day_time_status = $day_time_status->num_rows();
                if ($count_day_time_status) {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    }
                } else {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    }
                }
   }
   $queryTiming1 = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = 'visit' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
             $countTimingdm = $queryTiming1->num_rows();
              $countTimingdm;
             if($countTimingdm > 0)
             {
               
             $row11=$queryTiming1->row_array();
             $Day = $row11['day'];
              $working_type = $row11['time_slot'];
            
             if($working_type=='Morning' )
                {
                    $from_timem = $row11['from_time'];
                    $to_timem = $row11['to_time'];
                }     
             if($working_type=='Afternoon')
                {
                    $from_timea = $row11['from_time'];
                    $to_timea = $row11['to_time'];
                }
             if($working_type=='Evening')
                {
                    $from_timee = $row11['from_time'];
                    $to_timee = $row11['to_time'];
                }
             if($working_type=='Night')
                {
                    $from_timen = $row11['from_time'];
                    $to_timen = $row11['to_time'];
                }
             }
             else
             {
                
             $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";  
             }
            }
            
            $time_slott[] = array(
                'Morning' => $time_array,
                'time_slot' => 'Morning',
                'start_time'=>$from_timem,
                'end_time'=>$to_timem
            );
            $time_slott[] = array(
                'Afternoon' => $time_array1,
                'time_slot' => 'Afternoon',
                'start_time'=>$from_timea,
                'end_time'=>$to_timea
            );
            $time_slott[] = array(
                'Evening' => $time_array3,
                'time_slot' => 'Evening',
                'start_time'=>$from_timee,
                'end_time'=>$to_timee
            );
            $time_slott[] = array(
                'Night' => $time_array2,
                'time_slot' => 'Night',
                'start_time'=>$from_timen,
                'end_time'=>$to_timen
            );
          
             $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";  
            
           
          
                  $time_slots[] = array(
                'clinic_id' => $clinic_id,
                'clinic_name' => $clinic_name,
                'consultation_charge' => $fees,
                'timings' => $time_slott
                );
           
                  $clinic1_slott[] = array(
                    'day' => $todayDay,
                    'date' => $todayDate,
                    'clinic_array' => $time_slots
                );
           
            $time_slots=array();  
                
        }
      
        
      
        
        
        
                    }
                }
        }
        else
        {
                $clinic1_slott=array();
                
                 $wh="";
             if(empty($clinic_id))
             {
                 $wh .="";
             }
             else
             {
                 $wh .=" and id ='$clinic_id'";
             }
                    $sql2             = sprintf("SELECT `id`, `clinic_name`,consultation_charges FROM doctor_clinic WHERE doctor_id='$doctor_id' $wh");
                    $query_practices  = $this->db->query($sql2);
                    $total_practices  = $query_practices->num_rows();
                        
               
                
                if ($total_practices > 0) {
                    foreach ($query_practices->result_array() as $get_pract) {
                         $clinic_id            = $get_pract['id'];
                        
                        $clinic_name          = $get_pract['clinic_name'];
                        
                        $charge=$get_pract['consultation_charges'];
                       
                    
                        
        $fees=$charge;
        if(empty($fees))
        {
            $fees=0;
        }
        $todayDay  = date('l');
        $dat_new=date('Y-m-d');
        $todayDate = date('Y-m-d H:i:s');
        $date1 =date('Y-m-d H:i:s', strtotime($todayDate));
        $time_slott=array();
        $time_slots=array();
         $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";
        for ($i = 0; $i < 7; $i++) {
            if($i==0)
            {
                $a=0;
            }
            else
            {
                $a=1;
            }
            $todayDate            =  date('Y-m-d', strtotime($todayDate . ' +'.$a .'day'));
            $todayDay             = date('l', strtotime($todayDate));
            $time_array           = array();
            $time_array1          = array();
            $time_array2          = array();
            $time_array3          = array();
            $time_slott           = array();
          
            
            // doctor_slot_details
          
              $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = 'visit' AND `day` = '$todayDay'");
           
                //echo "SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'";
          // die();
            $count_day_time_slots = $day_time_slots->num_rows();
            foreach ($day_time_slots->result_array() as $row) {
             //  print_r($row); die();
                     $timeSlot              = $row['timeSlot'];
              
              
                $from_time             = $row['from_time'];
                $to_time               = $row['to_time'];
                // $status                = $row['status'];
   
   if($dat_new== $todayDate)
              {
                
                // $status                = $row['status'];
                  $date2 =date('Y-m-d H:i:s', strtotime($from_time));
                   $date1;

if ($date2 > $date1)
{
              $day_time_status       = $this->db->query("SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
            //   echo "SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3'"; die();
              $count_day_time_status = $day_time_status->num_rows();
                if ($count_day_time_status > 0) {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    }
                } else {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    }
                }
                //echo "SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$dhaval'"; echo "<br>";
}
else
{
    
}
   
   }
   else
   {
       $day_time_status       = $this->db->query("SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
              $count_day_time_status = $day_time_status->num_rows();
                if ($count_day_time_status) {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    }
                } else {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    }
                }
   }
              
                //echo "SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$dhaval'"; echo "<br>";
         //echo $timeSlot; die();
             $queryTiming1 = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = 'visit' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
             $countTimingdm = $queryTiming1->num_rows();
              $countTimingdm;
             if($countTimingdm > 0)
             {
               
             $row11=$queryTiming1->row_array();
             $Day = $row11['day'];
              $working_type = $row11['time_slot'];
            
             if($working_type=='Morning' )
                {
                    $from_timem = $row11['from_time'];
                    $to_timem = $row11['to_time'];
                }     
             if($working_type=='Afternoon')
                {
                    $from_timea = $row11['from_time'];
                    $to_timea = $row11['to_time'];
                }
             if($working_type=='Evening')
                {
                    $from_timee = $row11['from_time'];
                    $to_timee = $row11['to_time'];
                }
             if($working_type=='Night')
                {
                    $from_timen = $row11['from_time'];
                    $to_timen = $row11['to_time'];
                }
             }
             else
             {
                
             $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";  
             }
            }
            // print_r($time_array); die();
            $time_slott[] = array(
                'Morning' => $time_array,
                'time_slot' => 'Morning',
                'start_time'=>$from_timem,
                'end_time'=>$to_timem
            );
            $time_slott[] = array(
                'Afternoon' => $time_array1,
                'time_slot' => 'Afternoon',
                'start_time'=>$from_timea,
                'end_time'=>$to_timea
            );
            $time_slott[] = array(
                'Evening' => $time_array3,
                'time_slot' => 'Evening',
                'start_time'=>$from_timee,
                'end_time'=>$to_timee
            );
            $time_slott[] = array(
                'Night' => $time_array2,
                'time_slot' => 'Night',
                'start_time'=>$from_timen,
                'end_time'=>$to_timen
            );
          
             $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";  
            
           
        
             $time_slots[] = array(
                'day' => $todayDay,
                'date' => $todayDate,
                'timings' => $time_slott
            ); 
           
         
                
        }
      
        
          $clinic1_slott[] = array(
                'clinic_id' => $clinic_id,
                'clinic_name' => $clinic_name,
                'consultation_charge' => $fees,
                'time_slot' => $time_slots
            );
        
       
                    }
                }
                
                
                
             /*        $wh="";
             if(empty($clinic_id))
             {
                 $wh .="";
             }
             else
             {
                 $wh .=" and id ='$clinic_id'";
             }*/
                    $sql3             = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
                    $query_practices3  = $this->db->query($sql3);
                    $total_practices3  = $query_practices3->num_rows();
                    // print_r("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
                    //die;
               
                
                if ($total_practices3!="") {
                   $queryh=$query_practices3->row_array();
                  
                    $hospital_doctor_id=$queryh['hospital_doctor_id'];
                    
                     $sql4             = sprintf("SELECT name_of_hospital,consultation,user_id FROM hospital_doctor_list join hospitals on hospital_doctor_list.hospital_id=user_id WHERE hospital_doctor_list.id='$hospital_doctor_id'");
                    $query_practices4  = $this->db->query($sql4);
                    $total_practices4  = $query_practices4->num_rows();
                    if ($total_practices4 > 0) {
                    
                    foreach ($query_practices4->result_array() as $get_pract) {
                         $clinic_id            = $get_pract['user_id'];
                        
                        $clinic_name          = $get_pract['name_of_hospital'];
                        
                        $charge=$get_pract['consultation'];
                       
                    
                        
        $fees=$charge;
        if(empty($fees))
        {
            $fees=0;
        }
        $todayDay  = date('l');
        $dat_new=date('Y-m-d');
        $todayDate = date('Y-m-d H:i:s');
        $date1 =date('Y-m-d H:i:s', strtotime($todayDate));
        $time_slott=array();
        $time_slots=array();
         $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";
        for ($i = 0; $i < 7; $i++) {
            if($i==0)
            {
                $a=0;
            }
            else
            {
                $a=1;
            }
            $todayDate            =  date('Y-m-d', strtotime($todayDate . ' +'.$a .'day'));
            $todayDay             = date('l', strtotime($todayDate));
            $time_array           = array();
            $time_array1          = array();
            $time_array2          = array();
            $time_array3          = array();
            $time_slott           = array();
          
            
            // doctor_slot_details
          
              $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `hospital_doctor_timing` WHERE `doctor_id` = '$hospital_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = 'visit' AND `day` = '$todayDay'");
           
                //echo "SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'";
          // die();
            $count_day_time_slots = $day_time_slots->num_rows();
            foreach ($day_time_slots->result_array() as $row) {
             //  print_r($row); die();
                     $timeSlot              = $row['timeSlot'];
              
              
                $from_time             = $row['from_time'];
                $to_time               = $row['to_time'];
                // $status                = $row['status'];
   
   if($dat_new== $todayDate)
              {
                
                // $status                = $row['status'];
                  $date2 =date('Y-m-d H:i:s', strtotime($from_time));
                   $date1;

if ($date2 > $date1)
{
              $day_time_status       = $this->db->query("SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
            //   echo "SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3'"; die();
              $count_day_time_status = $day_time_status->num_rows();
                if ($count_day_time_status > 0) {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    }
                } else {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    }
                }
                //echo "SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$dhaval'"; echo "<br>";
}
else
{
    
}
   
   }
   else
   {
       $day_time_status       = $this->db->query("SELECT `id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `listing_id` = '$clinic_id' AND  `doctor_id`='$hospital_doctor_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
              $count_day_time_status = $day_time_status->num_rows();
                if ($count_day_time_status) {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    }
                } else {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    }
                }
   }
              
                //echo "SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$dhaval'"; echo "<br>";
         //echo $timeSlot; die();
             $queryTiming1 = $this->db->query("SELECT * FROM `hospital_doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = 'visit' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
             $countTimingdm = $queryTiming1->num_rows();
              $countTimingdm;
             if($countTimingdm > 0)
             {
               
             $row11=$queryTiming1->row_array();
             $Day = $row11['day'];
              $working_type = $row11['time_slot'];
            
             if($working_type=='Morning' )
                {
                    $from_timem = $row11['from_time'];
                    $to_timem = $row11['to_time'];
                }     
             if($working_type=='Afternoon')
                {
                    $from_timea = $row11['from_time'];
                    $to_timea = $row11['to_time'];
                }
             if($working_type=='Evening')
                {
                    $from_timee = $row11['from_time'];
                    $to_timee = $row11['to_time'];
                }
             if($working_type=='Night')
                {
                    $from_timen = $row11['from_time'];
                    $to_timen = $row11['to_time'];
                }
             }
             else
             {
                
             $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";  
             }
            }
            // print_r($time_array); die();
            $time_slott[] = array(
                'Morning' => $time_array,
                'time_slot' => 'Morning',
                'start_time'=>$from_timem,
                'end_time'=>$to_timem
            );
            $time_slott[] = array(
                'Afternoon' => $time_array1,
                'time_slot' => 'Afternoon',
                'start_time'=>$from_timea,
                'end_time'=>$to_timea
            );
            $time_slott[] = array(
                'Evening' => $time_array3,
                'time_slot' => 'Evening',
                'start_time'=>$from_timee,
                'end_time'=>$to_timee
            );
            $time_slott[] = array(
                'Night' => $time_array2,
                'time_slot' => 'Night',
                'start_time'=>$from_timen,
                'end_time'=>$to_timen
            );
          
             $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";  
            
           
        
             $time_slots[] = array(
                'day' => $todayDay,
                'date' => $todayDate,
                'timings' => $time_slott
            ); 
           
         
                
        }
      
        
          $clinic1_slott[] = array(
                'clinic_id' => $clinic_id,
                'clinic_name' => $clinic_name,
                'consultation_charge' => $fees,
                'time_slot' => $time_slots
            );
        
       
                    }
                    }
                }
        }
        return $clinic1_slott;
    }
    
      /* public function patient_appointment($doctor_id, $agent,$clinic_id)
    {
        if($agent=="ios")
        {
             $clinic1_slott=array();
             $wh="";
             if(empty($clinic_id))
             {
                 $wh .="";
             }
             else
             {
                 $wh .=" and id ='$clinic_id'";
             }
                $sql2             = sprintf("SELECT `id`, `clinic_name`,consultation_charges FROM doctor_clinic WHERE doctor_id='$doctor_id' $wh ");
              
                $query_practices  = $this->db->query($sql2);
                $total_practices  = $query_practices->num_rows();
                if ($total_practices > 0) {
                    foreach ($query_practices->result_array() as $get_pract) {
                        $clinic_id            = $get_pract['id'];
                        
                        $clinic_name          = $get_pract['clinic_name'];
                        
                        $charge=$get_pract['consultation_charges'];
                       
                      
                        
        
            $fees=$charge;
       if(empty($fees))
       {
           $fees=0;
       }
        $todayDay  = date('l');
        $dat_new=date('Y-m-d');
        $todayDate = date('Y-m-d H:i:s');
        $date1 =date('Y-m-d H:i:s', strtotime($todayDate));
        $time_slott=array();
        $time_slots=array();
         $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";
        for ($i = 0; $i < 7; $i++) {
            if($i==0)
            {
                $a=0;
            }
            else
            {
                $a=1;
            }
            $todayDate            =  date('Y-m-d', strtotime($todayDate . ' +'.$a .'day'));
            $todayDay             = date('l', strtotime($todayDate));
            $time_array           = array();
            $time_array1          = array();
            $time_array2          = array();
            $time_array3          = array();
            $time_slott           = array();
          
            
            // doctor_slot_details
            
           $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = 'visit' AND `day` = '$todayDay'");
             
            $count_day_time_slots = $day_time_slots->num_rows();
            foreach ($day_time_slots->result_array() as $row) {
               
                     $timeSlot              = $row['timeSlot'];
              $from_time             = $row['from_time'];
                $to_time               = $row['to_time'];
              if($dat_new== $todayDate)
              {
                
                // $status                = $row['status'];
                  $date2 =date('Y-m-d H:i:s', strtotime($from_time));
                   $date1;

            if ($date2 > $date1)
            {
              $day_time_status = $this->db->query("SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
              $count_day_time_status = $day_time_status->num_rows();
                if ($count_day_time_status) {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    }
                } else {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    }
                }
                //echo "SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$dhaval'"; echo "<br>";
            }
            else
            {
                
            }
   
   }
   else
   {
       $day_time_status       = $this->db->query("SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
              $count_day_time_status = $day_time_status->num_rows();
                if ($count_day_time_status) {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    }
                } else {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    }
                }
   }
   $queryTiming1 = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = 'visit' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
             $countTimingdm = $queryTiming1->num_rows();
              $countTimingdm;
             if($countTimingdm > 0)
             {
               
             $row11=$queryTiming1->row_array();
             $Day = $row11['day'];
              $working_type = $row11['time_slot'];
            
             if($working_type=='Morning' )
                {
                    $from_timem = $row11['from_time'];
                    $to_timem = $row11['to_time'];
                }     
             if($working_type=='Afternoon')
                {
                    $from_timea = $row11['from_time'];
                    $to_timea = $row11['to_time'];
                }
             if($working_type=='Evening')
                {
                    $from_timee = $row11['from_time'];
                    $to_timee = $row11['to_time'];
                }
             if($working_type=='Night')
                {
                    $from_timen = $row11['from_time'];
                    $to_timen = $row11['to_time'];
                }
             }
             else
             {
                
             $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";  
             }
            }
            
            $time_slott[] = array(
                'Morning' => $time_array,
                'time_slot' => 'Morning',
                'start_time'=>$from_timem,
                'end_time'=>$to_timem
            );
            $time_slott[] = array(
                'Afternoon' => $time_array1,
                'time_slot' => 'Afternoon',
                'start_time'=>$from_timea,
                'end_time'=>$to_timea
            );
            $time_slott[] = array(
                'Evening' => $time_array3,
                'time_slot' => 'Evening',
                'start_time'=>$from_timee,
                'end_time'=>$to_timee
            );
            $time_slott[] = array(
                'Night' => $time_array2,
                'time_slot' => 'Night',
                'start_time'=>$from_timen,
                'end_time'=>$to_timen
            );
          
             $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";  
            
           
          
                  $time_slots[] = array(
                'clinic_id' => $clinic_id,
                'clinic_name' => $clinic_name,
                'consultation_charge' => $fees,
                'timings' => $time_slott
                );
           
                  $clinic1_slott[] = array(
                    'day' => $todayDay,
                    'date' => $todayDate,
                    'clinic_array' => $time_slots
                );
           
            $time_slots=array();  
                
        }
      
        
      
        
        
        
                    }
                }
        }
        else
        {
                $clinic1_slott=array();
                
                 $wh="";
             if(empty($clinic_id))
             {
                 $wh .="";
             }
             else
             {
                 $wh .=" and id ='$clinic_id'";
             }
                    $sql2             = sprintf("SELECT `id`, `clinic_name`,consultation_charges FROM doctor_clinic WHERE doctor_id='$doctor_id' $wh");
                    $query_practices  = $this->db->query($sql2);
                    $total_practices  = $query_practices->num_rows();
                        
               
                
                if ($total_practices > 0) {
                    foreach ($query_practices->result_array() as $get_pract) {
                         $clinic_id            = $get_pract['id'];
                        
                        $clinic_name          = $get_pract['clinic_name'];
                        
                        $charge=$get_pract['consultation_charges'];
                       
                    
                        
        $fees=$charge;
        if(empty($fees))
        {
            $fees=0;
        }
        $todayDay  = date('l');
        $dat_new=date('Y-m-d');
        $todayDate = date('Y-m-d H:i:s');
        $date1 =date('Y-m-d H:i:s', strtotime($todayDate));
        $time_slott=array();
        $time_slots=array();
         $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";
        for ($i = 0; $i < 7; $i++) {
            if($i==0)
            {
                $a=0;
            }
            else
            {
                $a=1;
            }
            $todayDate            =  date('Y-m-d', strtotime($todayDate . ' +'.$a .'day'));
            $todayDay             = date('l', strtotime($todayDate));
            $time_array           = array();
            $time_array1          = array();
            $time_array2          = array();
            $time_array3          = array();
            $time_slott           = array();
          
            
            // doctor_slot_details
          
              $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = 'visit' AND `day` = '$todayDay'");
           
                //echo "SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'";
          // die();
            $count_day_time_slots = $day_time_slots->num_rows();
            foreach ($day_time_slots->result_array() as $row) {
             //  print_r($row); die();
                     $timeSlot              = $row['timeSlot'];
              
              
                $from_time             = $row['from_time'];
                $to_time               = $row['to_time'];
                // $status                = $row['status'];
   
   if($dat_new== $todayDate)
              {
                
                // $status                = $row['status'];
                  $date2 =date('Y-m-d H:i:s', strtotime($from_time));
                   $date1;

if ($date2 > $date1)
{
              $day_time_status       = $this->db->query("SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
            //   echo "SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3'"; die();
              $count_day_time_status = $day_time_status->num_rows();
                if ($count_day_time_status > 0) {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    }
                } else {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    }
                }
                //echo "SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$dhaval'"; echo "<br>";
}
else
{
    
}
   
   }
   else
   {
       $day_time_status       = $this->db->query("SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
              $count_day_time_status = $day_time_status->num_rows();
                if ($count_day_time_status) {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '1'
                        );
                    }
                } else {
                    if ($timeSlot == 'Morning') {
                        $time_array[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Afternoon') {
                        $time_array1[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Evening') {
                        $time_array3[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    } else if ($timeSlot == 'Night') {
                        $time_array2[] = array(
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                            'status' => '0'
                        );
                    }
                }
   }
              
                //echo "SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$dhaval'"; echo "<br>";
         //echo $timeSlot; die();
             $queryTiming1 = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = 'visit' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
             $countTimingdm = $queryTiming1->num_rows();
              $countTimingdm;
             if($countTimingdm > 0)
             {
               
             $row11=$queryTiming1->row_array();
             $Day = $row11['day'];
              $working_type = $row11['time_slot'];
            
             if($working_type=='Morning' )
                {
                    $from_timem = $row11['from_time'];
                    $to_timem = $row11['to_time'];
                }     
             if($working_type=='Afternoon')
                {
                    $from_timea = $row11['from_time'];
                    $to_timea = $row11['to_time'];
                }
             if($working_type=='Evening')
                {
                    $from_timee = $row11['from_time'];
                    $to_timee = $row11['to_time'];
                }
             if($working_type=='Night')
                {
                    $from_timen = $row11['from_time'];
                    $to_timen = $row11['to_time'];
                }
             }
             else
             {
                
             $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";  
             }
            }
            // print_r($time_array); die();
            $time_slott[] = array(
                'Morning' => $time_array,
                'time_slot' => 'Morning',
                'start_time'=>$from_timem,
                'end_time'=>$to_timem
            );
            $time_slott[] = array(
                'Afternoon' => $time_array1,
                'time_slot' => 'Afternoon',
                'start_time'=>$from_timea,
                'end_time'=>$to_timea
            );
            $time_slott[] = array(
                'Evening' => $time_array3,
                'time_slot' => 'Evening',
                'start_time'=>$from_timee,
                'end_time'=>$to_timee
            );
            $time_slott[] = array(
                'Night' => $time_array2,
                'time_slot' => 'Night',
                'start_time'=>$from_timen,
                'end_time'=>$to_timen
            );
          
             $from_timem="";
             $to_timem="";
             $from_timea="";
             $to_timea="";
             $from_timee="";
             $to_timee="";
             $from_timen="";
             $to_timen="";  
            
           
        
             $time_slots[] = array(
                'day' => $todayDay,
                'date' => $todayDate,
                'timings' => $time_slott
            ); 
           
         
                
        }
      
        
          $clinic1_slott[] = array(
                'clinic_id' => $clinic_id,
                'clinic_name' => $clinic_name,
                'consultation_charge' => $fees,
                'time_slot' => $time_slots
            );
        
       
                    }
                }
        }
        return $clinic1_slott;
    }*/
    public function add_bookings($patient_id, $doctor_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $description, $date_of_birth, $connect_type,$status)
     {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id           = date("YmdHis");
        $date                 = date('Y-m-d H:i:s');
       // $status               = '1'; //awaiting for confirmation  // pending
        $from_time = date("H:i:s", strtotime($from_time));
        $to_time =  date("H:i:s", strtotime($to_time));
        
        $sql2 ="SELECT `consultation_charges` FROM doctor_clinic WHERE doctor_id='$doctor_id' and id='$clinic_id'";
        $query_practices  = $this->db->query($sql2);
          $count_practices  = $query_practices->num_rows();
              
             if($count_practices > 0)
             {
        $get_pract=$query_practices->row_array();
        $consultation_charges = $get_pract['consultation_charges'];               
                        
        
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $patient_id,
            'listing_id' => $doctor_id,
            'clinic_id' => $clinic_id,
            'booking_date' => $booking_date,
            'booking_time' => $booking_time,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'status' => $status,
            'description' => $description,
            'created_date' => $date,
            'patient_id' => $patient_id,
            'consultation_type' => $connect_type,
            'consultation_charges'=>$consultation_charges
        );
        $insert = $this->db->insert('doctor_booking_master', $booking_master_array);
        $appointment_id       = $this->db->insert_id();
        if($insert)
            {
                $data= $this->PartnerdoctorModel->doctor_notification_confirm($booking_id);
                $booking_date=date('d-M-Y', strtotime($data->booking_date));
                $booking_time=date("h:i a", strtotime($data->from_time));
                
                $message = 'Your Appointment for '.$booking_date.' at '.$booking_time.' for '.$data->doctor_name.' is confirmed';
                $to = $data->email;
                $subject = 'medicalwale Appointment List';
                $msg = $message;
                $this->Send_Email_Notification($to,$subject,$msg);
                $query_dr1           = $this->db->query("SELECT name FROM users  WHERE id='$patient_id'");
                $data1               = $query_dr1->row_array();
                $user_name        = $data1['name'];
                $this->notifyMethod_doctor($doctor_id, $patient_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $user_name,$from_time);
            }
        if ($appointment_id > 0) 
            {
                date_default_timezone_set('Asia/Kolkata');
                $date= date('Y-m-d H:i:s');
                $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'");
                $count_user   = $table_record->num_rows();
                if ($count_user > 0) 
                   {
                     
                        if ($status == '8')
                            {
                                $row       = $table_record->row();
                                $user_id   = $row->user_id;
                                $doctor_id = $row->listing_id;
                                $this->confirm_cash_on_delivery_status($patient_id, $booking_id, $doctor_id);
                            }
                      
                    }
            } 
        else 
           {
                return array(
                    'status' => 208,
                    'message' => 'Database Insert Issue'
                );
           }
       
        $query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name,doctor_list.telephone FROM doctor_clinic INNER JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$doctor_id'");
        $data               = $query_dr->row_array();
        $doctor_name        = $data['doctor_name'];
        $clinic_name        = $data['clinic_name'];
        $doctor_phone       = $data['telephone'];
        $final_booking_date = date('M-d', strtotime($booking_date));
        
      
        $this->insert_notification_post_question($patient_id, $appointment_id,$doctor_id);
        $this->notifyMethod1($doctor_id, $patient_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
    
             }
             
             
               $sql3             = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
                    $query_practices3  = $this->db->query($sql3);
                    $total_practices3  = $query_practices3->num_rows();
                    // print_r("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
                    //die;
               
                
                if ($total_practices3!="") {
                   $queryh=$query_practices3->row_array();
                  
                    $hospital_doctor_id=$queryh['hospital_doctor_id'];
                    
                     $sql4             = sprintf("SELECT name_of_hospital,consultation,user_id FROM hospital_doctor_list join hospitals on hospital_doctor_list.hospital_id=user_id WHERE hospital_doctor_list.id='$hospital_doctor_id'");
                    $query_practices4  = $this->db->query($sql4);
                    $total_practices4  = $query_practices4->num_rows();
                   // if ($total_practices4 > 0) {
             
              
             if($total_practices4 > 0)
             {
        $get_pract=$query_practices4->row_array();
        $consultation_charges = $get_pract['consultation'];               
          
          
          
          
           $phoneno =$user_mobile;
       
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('phone',$phoneno);
        $this->db->where('vendor_id','0');
        $userdata= $this->db->get()->row_array();
        
        

       if($userdata==""){
         $date1 = $date_of_birth;
         $d1 = date("Y-m-d", strtotime($date1));
        $pram=array(
            'name' => $user_name,
            'phone'=> $phoneno,
            'email'    => $user_email,
            'vendor_id'=>'0',
            'chat_id'=>'anonymous',
            'city'        => "",
            'dob'  =>$d1,
            'gender'         => $user_gender,
            'agent'=>'vendor',
            'is_active'=>'1',
            'language'=>'0'
      
            );
        $this->db->insert('users',$pram);
        $insert_id=$this->db->insert_id();
        
        
        $data['name'] = $user_name;  
        $data['user_id']        = $insert_id;
        $data['listing_id']        = $clinic_id;
        $data['address']        = "";
        $data['adharcard']       = "";
        $data['weight']        = "";
        $data['mlc']        = "";
        $data['booking_type']  = "0";
        $data['mlc_no']        = "";
        $data['email']    = $user_email;
        $data['relativename']    = "";
        $data['remail']    = "";
        $data['rrelation']    = "";
        $data['phone']    = $phoneno;
        $data['relative_phoneno']    = "";
        $data['sex']          = $user_gender;
        $data['weight']  = "";
        $data['age']  = "0";
        $d1 = date("Y-m-d", strtotime($date1));
        $data['date_of_birth']  =$d1;
        $data['blood_group']  = "";
        $data['occupation']        = "";
        $data['city']        = "";
        $data['date']        = date("Y/m/d");
        $data['pincode']        = "";
        $data['staff_id']="";
        $data[' is_active']="1";
	    $listing_id=$clinic_id;
        $data['is_active']="1";
        $query2= $this->db->query("SELECT COUNT(*) AS `numrows` FROM `hospital_ipd_patient` WHERE `listing_id` = '$listing_id'");
        $queryopd = $query2->row_array();
        $pcount=$queryopd['numrows'];
        $pcount=$pcount+1;
        $ddyy=date("m/y");
        $query3= $this->db->query("SELECT name_of_hospital FROM `hospitals` WHERE `user_id` = '$listing_id'");
        $queryopd3 = $query3->row_array();
        $myStr=$queryopd3['name_of_hospital'];
        $result = substr($myStr, 0, 2);
        if($listing_id=="48524"){
         $data11="CH";
        }
        else {
         $data11=$result;
        }
        $final_data=$data11.$pcount."/".$ddyy;
        // echo $final_data;
        $data['uhidno'] = $final_data;    
        $this->db->insert('hospital_ipd_patient',$data);
        $patient_id=$this->db->insert_id();

       }else{
             $listing_id=$clinic_id;
           
        $insert_id=$userdata['id'];
        $query4= $this->db->query("SELECT id FROM `hospital_ipd_patient` WHERE `listing_id` = '$listing_id' and user_id='$insert_id'");
        $queryopd3 = $query4->row_array();
        $patient_id=$queryopd3['id'];
           
       }              
        
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $insert_id,
            'listing_id' => $clinic_id,
            'doctor_id' => $hospital_doctor_id,
            'booking_date' => $booking_date,
            'booking_time' => $booking_time,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'status' => $status,
            'description' => $description,
            'created_date' => $date,
            'patient_id' => $patient_id,
            'consultation_type' => $connect_type,
            'consultation_charges'=>$consultation_charges
        );
        $insert = $this->db->insert('hospital_booking_master', $booking_master_array);
        $appointment_id       = $this->db->insert_id();
        if($insert)
            {
                $data= $this->PartnerdoctorModel->hospital_notification_confirm($booking_id);
                $booking_date=date('d-M-Y', strtotime($data->booking_date));
                $booking_time=date("h:i a", strtotime($data->from_time));
                
                $message = 'Your Appointment for '.$booking_date.' at '.$booking_time.' for '.$data->doctor_name.' is confirmed';
                $to = $data->email;
                $subject = 'medicalwale Appointment List';
                $msg = $message;
                $this->Send_Email_Notification($to,$subject,$msg);
                $query_dr1           = $this->db->query("SELECT name FROM users  WHERE id='$patient_id'");
                $data1               = $query_dr1->row_array();
                $user_name        = $data1['name'];
                $this->notifyMethod_doctor($doctor_id, $patient_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $user_name,$from_time);
            }
        if ($appointment_id > 0) 
            {
                date_default_timezone_set('Asia/Kolkata');
                $date= date('Y-m-d H:i:s');
                $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM hospital_booking_master WHERE booking_id='$booking_id'");
                $count_user   = $table_record->num_rows();
                if ($count_user > 0) 
                   {
                     
                        if ($status == '8')
                            {
                                $row       = $table_record->row();
                                $user_id   = $row->user_id;
                                $doctor_id = $row->listing_id;
                                $this->confirm_cash_on_delivery_status($patient_id, $booking_id, $doctor_id);
                            }
                      
                    }
            } 
        else 
           {
                return array(
                    'status' => 208,
                    'message' => 'Database Insert Issue'
                );
           }
       
        /*$query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name,doctor_list.telephone FROM doctor_clinic INNER JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$doctor_id'");
        $data               = $query_dr->row_array();
        $doctor_name        = $data['doctor_name'];
        $clinic_name        = $data['clinic_name'];
        $doctor_phone       = $data['telephone'];
        $final_booking_date = date('M-d', strtotime($booking_date));
        
      
        $this->insert_notification_post_question($patient_id, $appointment_id,$doctor_id);
        $this->notifyMethod1($doctor_id, $patient_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
    */
             }
          } 
        return array(
            'booking_id' => $booking_id
        );
    }  
    
     /* public function add_bookings($patient_id, $doctor_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $description, $date_of_birth, $connect_type,$status)
     {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id           = date("YmdHis");
        $date                 = date('Y-m-d H:i:s');
       // $status               = '1'; //awaiting for confirmation  // pending
        $from_time = date("H:i:s", strtotime($from_time));
        $to_time =  date("H:i:s", strtotime($to_time));
	
	$sql2 ="SELECT `consultation_charges` FROM doctor_clinic WHERE doctor_id='$doctor_id' and id='$clinic_id'";
        $query_practices  = $this->db->query($sql2);
        $get_pract=$query_practices->row_array();
        $consultation_charges = $get_pract['consultation_charges'];  
	      
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $patient_id,
            'listing_id' => $doctor_id,
            'clinic_id' => $clinic_id,
            'booking_date' => $booking_date,
            'booking_time' => $booking_time,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'status' => $status,
            'description' => $description,
            'created_date' => $date,
            'patient_id' => $patient_id,
            'consultation_type' => $connect_type,
	    'consultation_charges'=>$consultation_charges	
        );
        $insert = $this->db->insert('doctor_booking_master', $booking_master_array);
        $appointment_id       = $this->db->insert_id();
        if($insert)
            {
                $data= $this->PartnerdoctorModel->doctor_notification_confirm($booking_id);
                $booking_date=date('d-M-Y', strtotime($data->booking_date));
                $booking_time=date("h:i a", strtotime($data->from_time));
                
                $message = 'Your Appointment for '.$booking_date.' at '.$booking_time.' for '.$data->doctor_name.' is confirmed';
                $to = $data->email;
                $subject = 'medicalwale Appointment List';
                $msg = $message;
                $this->Send_Email_Notification($to,$subject,$msg);
                $query_dr1           = $this->db->query("SELECT name FROM users  WHERE id='$patient_id'");
                $data1               = $query_dr1->row_array();
                $user_name        = $data1['name'];
                $this->notifyMethod_doctor($doctor_id, $patient_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $user_name,$from_time);
            }
        if ($appointment_id > 0) 
            {
                date_default_timezone_set('Asia/Kolkata');
                $date= date('Y-m-d H:i:s');
                $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'");
                $count_user   = $table_record->num_rows();
                if ($count_user > 0) 
                   {
                     /* if ($status == '5') //user confirm, payment pending
                            {
                                $row       = $table_record->row();
                                $user_id   = $row->user_id;
                                $doctor_id = $row->listing_id;
                                $this->confirm_status($user_id, $booking_id, $doctor_id);
                            }*
                        if ($status == '8')
                            {
                                $row       = $table_record->row();
                                $user_id   = $row->user_id;
                                $doctor_id = $row->listing_id;
                                $this->confirm_cash_on_delivery_status($patient_id, $booking_id, $doctor_id);
                            }
                        /* else if ($status == '3') //cancel appointment 
                                {
                                $row       = $table_record->row();
                                $user_id   = $row->user_id;
                                $doctor_id = $row->listing_id;
                                $this->cancel_status($user_id, $booking_id, $doctor_id);
                                }*
                    }
            } 
        else 
           {
                return array(
                    'status' => 208,
                    'message' => 'Database Insert Issue'
                );
           }
       
        $query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name,doctor_list.telephone FROM doctor_clinic INNER JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$doctor_id'");
        $data               = $query_dr->row_array();
        $doctor_name        = $data['doctor_name'];
        $clinic_name        = $data['clinic_name'];
        $doctor_phone       = $data['telephone'];
        $final_booking_date = date('M-d', strtotime($booking_date));
        
        /*if ($insert) {
            $message      = 'Your appointment with ' . $doctor_name . ' at ' . $clinic_name . ' for ' . $final_booking_date . ', ' . $booking_time . ' is confirmed. Thanks Medicalwale.com';
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
        }*
        $this->insert_notification_post_question($patient_id, $appointment_id,$doctor_id);
        $this->notifyMethod1($doctor_id, $patient_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
    
        return array(
            'booking_id' => $booking_id
        );
    } */ 
    
    
     public function add_re_bookings($patient_id, $doctor_id, $clinic_id, $booking_date, $booking_time, $from_time, $to_time, $connect_type,$status,$appointment_id,$description)
     {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id           = $appointment_id;
        $date                 = date('Y-m-d H:i:s');
       // $status               = '1'; //awaiting for confirmation  // pending
        $from_time = date("H:i:s", strtotime($from_time));
        $to_time =  date("H:i:s", strtotime($to_time));
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $patient_id,
            'listing_id' => $doctor_id,
            'clinic_id' => $clinic_id,
            'booking_date' => $booking_date,
            'booking_time' => $booking_time,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'status' => $status,
            'description' => $description,
            'created_date' => $date,
            'patient_id' => $patient_id,
            'consultation_type' => $connect_type
        );
        $this->db->where('booking_id', $appointment_id);
        $insert = $this->db->update('doctor_booking_master', $booking_master_array);
       /* $appointment_id       = $this->db->insert_id();
        if($insert)
            {
                $data= $this->PartnerdoctorModel->doctor_notification_confirm($booking_id);
                $message = 'Dear '.$data->name.', your booking has been confirm With  '.$data->doctor_name.'  Your '.'Booking ID is '.$data->booking_id.' '.'Thank you.';
                $to = $data->email;
                $subject = 'medicalwale Appointment List';
                $msg = $message;
                $this->Send_Email_Notification($to,$subject,$msg);
                $query_dr1           = $this->db->query("SELECT name FROM users  WHERE id='$patient_id'");
                $data1               = $query_dr1->row_array();
                $user_name        = $data1['name'];
                $this->notifyMethod_doctor($doctor_id, $patient_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $user_name,$from_time);
            }*/
        if ($appointment_id > 0) 
            {
                date_default_timezone_set('Asia/Kolkata');
                $date= date('Y-m-d H:i:s');
                $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'");
                $count_user   = $table_record->num_rows();
                if ($count_user > 0) 
                   {
                      if ($status == '9') //user confirm, payment pending
                            {
                                $row       = $table_record->row();
                                $user_id   = $row->user_id;
                                $doctor_id = $row->listing_id;
                                $this->reschedule_status($user_id, $booking_id, $doctor_id,$connect_type);
                            }
                        if ($status == '10')
                            {
                                $row       = $table_record->row();
                                $user_id   = $row->user_id;
                                $doctor_id = $row->listing_id;
                                $this->reschedule_status($patient_id, $booking_id, $doctor_id,$connect_type);
                            }
                        /* else if ($status == '3') //cancel appointment 
                                {
                                $row       = $table_record->row();
                                $user_id   = $row->user_id;
                                $doctor_id = $row->listing_id;
                                $this->cancel_status($user_id, $booking_id, $doctor_id);
                                }*/
                    }
            } 
        else 
           {
                return array(
                    'status' => 208,
                    'message' => 'Database Insert Issue'
                );
           }
       
        $query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name,doctor_list.telephone FROM doctor_clinic INNER JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$doctor_id'");
        $data               = $query_dr->row_array();
        $doctor_name        = $data['doctor_name'];
        $clinic_name        = $data['clinic_name'];
        $doctor_phone       = $data['telephone'];
        $final_booking_date = date('M-d', strtotime($booking_date));
        
        /*if ($insert) {
            $message      = 'Your appointment with ' . $doctor_name . ' at ' . $clinic_name . ' for ' . $final_booking_date . ', ' . $booking_time . ' is confirmed. Thanks Medicalwale.com';
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
        $this->insert_notification_post_requestion($patient_id, $appointment_id,$doctor_id);
        $this->notifyMethod1($doctor_id, $patient_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
    
        return array(
            'booking_id' => $booking_id
        );
    } 

      public function doctor_notification_confirm($booking_id)
       {
           $data="SELECT doctor_booking_master.*,users.name,users.email,users.phone,doctor_clinic.clinic_name,doctor_list.telephone,doctor_list.doctor_name FROM doctor_booking_master LEFT JOIN users ON (doctor_booking_master.user_id=users.id) LEFT JOIN doctor_clinic ON (doctor_booking_master.clinic_id=doctor_clinic.id) 
              LEFT JOIN doctor_list ON (doctor_booking_master.listing_id=doctor_list.user_id)WHERE doctor_booking_master.booking_id='".$booking_id."'";
             
             $result = $this->db->query($data)->row();
                return $result;
       }
         public function Send_Email_Notification($to,$subject,$msg)
           {
            
                $headers = 'From: info@medicalwale.com' . "\r\n" .
                   'Reply-To: donotreply@medicalwale.com' . "\r\n" .
                   'X-Mailer: PHP/' . phpversion();
                
                mail($to, $subject, $msg, $headers);
           }  
        public function notifyMethod_doctor($listing_id, $user_id, $description, $booking_date, $booking_time, $appointment_id, $connect_type, $user_name,$from)
    {
        
        $customer_token       = $this->db->query("SELECT name,token,agent,token_status,web_token FROM users WHERE id='$listing_id'");
        $customer_token_count = $customer_token->num_rows();
        if ($customer_token_count > 0) {
            $timestamp = strtotime($booking_date);
            $newdate=date("d-M-Y", $timestamp);
            
            $newtime=date("h:i A", strtotime($from));
            
          
                
                //$message = 'Your Appointment for '.$booking_date.' at '.$booking_time.' for '.$data->doctor_name.' is confirmed';
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
            
            $this->send_gcm_notify_doctor($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_time, $appointment_id, $connect_type,$from);
            
           
            
            
            
        }
    }
    
    
     public function confirm_cash_on_delivery_status($user_id, $booking_id, $listing_id)
    {
        $appointment_status = '8';
        $table_record   = $this->db->query("SELECT name as patient_name FROM users WHERE id='$user_id'");
        
        $bookingRecords =  $this->db->query("SELECT `booking_date`,`from_time`,`to_time`,`consultation_type` FROM `doctor_booking_master` WHERE `booking_id`='$booking_id'");
        $count_record  = $bookingRecords->num_rows();
        if($count_record > 0){
            $bookingRecord = $bookingRecords->row();
            
            $booking_date = $bookingRecord->booking_date;
            $from_time = $bookingRecord->from_time;
            $to_time = $bookingRecord->to_time;
            $booking_time = $from_time.'-'.$to_time;
            $consultation_type = $bookingRecord->consultation_type;
        }
         $timestamp = strtotime($booking_date);
            $newdate=date("d-M-Y", $timestamp);
            
            $newtime=date("h:i A", strtotime($from_time));
        $count_user         = $table_record->num_rows();
        if ($count_user > 0) {
            $row         = $table_record->row();
            $patient_name = $row->patient_name;
           
            $title       = $patient_name . '  has Confirmed an Payment on Cash At Point ';
            $msg         = $patient_name . '  has Confirmed and Cash At Point for'. $consultation_type .' appointment on '.$newdate .' at '.$newtime  ;
            
            //$this->notifyDoctorMethod($listing_id, $appointment_status, $booking_id, $patient_name, $title, $msg);
            $this->notifyDoctorMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name);
        }
    }
      public function notifyDoctorMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name)
    {

          $customer_token = $this->db->query("SELECT name,token,agent,token_status,web_token FROM users WHERE id='$listing_id'");
          $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                // $getusr = $user_plike->row_array();

                $usr_name = $token_status['name'];
                $agent = $token_status['agent'];
                $reg_id = $token_status['token'];
                $web_token = $token_status['web_token'];
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/doctor.png';
                $tag = 'text';
                $key_count = '1';
                $title = $title;
                $msg = $msg;
                
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
                      'booking_id'  => "",
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'appointment_notifications',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
                    );
                    $this->db->insert('All_notification_Mobile', $notification_array);
                    //end
                    
                    
                    $this->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent,$booking_date,$booking_time,$booking_id,$consultation_type);
                    $type="appointment_notifications";
                    // Web notification FCM function by ghanshyam parihar date:02 Feb 2019
                    $click_action = 'https://doctor.medicalwale.com/booking_controller/booking_appointment/'.$listing_id;
                    // $click_action = 'https://vendor.sandbox.medicalwale.com/doctor/booking_controller/booking_appointment/'.$listing_id;
                    $this->send_gcm_web_notify($title, $msg, $web_token, $img_url, $tag, $agent, $type,$click_action);
                    // Web notification FCM function by ghanshyam parihar date:02 Feb 2019 Ends
            }
    }
    
     // Web notification FCM function by ghanshyam parihar date:16 Jan 2019
    function send_gcm_web_notify($title, $msg, $reg_id, $img_url, $tag, $agent, $type,$click_action) {
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
    // Web notification FCM function by ghanshyam parihar date:16 Jan 2019 Ends
    
     public function insert_notification_post_question($user_id, $booking_id, $doctor_id){
      $data = array(
                           'user_id'        => $user_id,
                           'post_id'         => $booking_id,
                           'timeline_id'   => $user_id,
                           'type'          => 'Appointment',
                           'seen'          => '1',
                           'notified_by'   => $doctor_id,
                           'description'   => 'Appointment Booking',
                           'created_at'    => date('Y-m-d H:i:s'),
                           'updated_at'    => date('Y-m-d H:i:s')
                           
                   );
       //print_r($data);
       $this->db->insert("notifications", $data);        
       if($this->db->affected_rows() > 0)
       {
           
           return true; // to the controller
       }
       else{
           return false;
       }
   }
    public function insert_notification_post_requestion($user_id, $booking_id, $doctor_id){
      $data = array(
                           'user_id'        => $user_id,
                           'post_id'         => $booking_id,
                           'timeline_id'   => $user_id,
                           'type'          => 'Appointment',
                           'seen'          => '1',
                           'notified_by'   => $doctor_id,
                           'description'   => 'Appointment Reschedule',
                           'created_at'    => date('Y-m-d H:i:s'),
                           'updated_at'    => date('Y-m-d H:i:s')
                           
                   );
       //print_r($data);
       $this->db->insert("notifications", $data);        
       if($this->db->affected_rows() > 0)
       {
           
           return true; // to the controller
       }
       else{
           return false;
       }
   }
    
    public function notifyMethod1($listing_id, $user_id, $description, $booking_date, $booking_time, $appointment_id, $connect_type, $user_name)
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
            /*$title        = "Your appointment with $user_name has been booked."; //$user_name . ' has booked an appointment'; 
            $msg          = 'Date : ' . $booking_date . '  Time : ' . $booking_time;*/
            $title        = "Appointment Confirmation"; //$user_name . ' has booked an appointment'; 
            $msg          = "Your Appointment for ".$booking_date." at ".$booking_time." for ".$user_name." is confirmed";
            
            $type = 'Booking';
            
            $this->send_gcm_notify_booking_user($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_time, $appointment_id, $connect_type);
             
            
            
            
        }
    }
    
     public function send_gcm_notify_booking_user($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_id) {
    
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
                "notification_type" => 'doctor_confirmation',
                "notification_date" => $date,
                "booking_id" => $booking_id
                
            )
        );
       
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
          $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
          
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
    public function add_patient($doctor_id, $patient_name, $address, $state, $city, $pincode, $contact_no, $gender, $date_of_birth, $blood_group, $medical_profile, $email) {
		date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        $data = array(
            'doctor_id' => $doctor_id,
            'patient_name' => $patient_name,
            'address' => $address,
            'state' => $state,
            'city' => $city,
            'pincode' => $pincode,
            'contact_no' => $contact_no,
            'gender' => $gender,
            'date_of_birth' => $date_of_birth,
            'blood_group' => $blood_group,
            'medical_profile' => $medical_profile,
            'email' => $email,
			'created_date' => $created_date,
			
        );
        
        
        $query = $this->db->query("SELECT id  FROM `users` WHERE phone='$contact_no'");
        $count = $query->num_rows();
        if($count > 0){
            $row=$query->row_array();
            $user_id = $row['id'];
            
            $query1 = $this->db->query("SELECT id  FROM `doctor_patient` WHERE `doctor_id`='$doctor_id' and user_id='$user_id'");
            $count1 = $query1->num_rows();
            if ($count1 > 0) {
                
                $row1=$query1->row_array();  
                $p_id = $row1['id'];
                
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_patient', $data);
           
                if($this->db->affected_rows()>0){
                    return array(
                    'status' => 200,
                    'message' => 'success',
                    'patient_id' =>  $user_id,
                    'patient_name' => $patient_name
                    );
                } else {
                    return array(
                        'status' => 200,
                        'message' => 'failure',
                        'patient_id' => 0,
                        'patient_name' => ''
                    );
                }
            }
            else{
                $data2 = array(
                    'doctor_id' => $doctor_id,
                    'user_id' =>$user_id,
                    'patient_name' => $patient_name,
                    'address' => $address,
                    'state' => $state,
                    'city' => $city,
                    'pincode' => $pincode,
                    'contact_no' => $contact_no,
                    'gender' => $gender,
                    'date_of_birth' => $date_of_birth,
                    'blood_group' => $blood_group,
                    'medical_profile' => $medical_profile,
                    'email' => $email,
        			'created_date' => $created_date,
                );
                
                $patient_insert = $this->db->insert('doctor_patient', $data2);
                $p_id = $this->db->insert_id();
                if ($patient_insert) {
                    return array(
                        'status' => 200,
                        'message' => 'success',
                        'patient_id' => $user_id,
                        'patient_name' => $patient_name
                    );
                } else {
                    return array(
                        'status' => 200,
                        'message' => 'failure',
                        'patient_id' => 0,
                        'patient_name' => ''
                    );
                }
                
            }
        }else{
            
            
            
            $data_user = array(
                'name' => $patient_name,
                'phone' => $contact_no,
                'email' => $email,
                'vendor_id' => '0',
                'map_location' => $address,
                'city' => $city,
                'gender' => $gender,
                'dob' => $date_of_birth,
                'blood_group' => $blood_group,
                'last_login' => $created_date,
            );
            
            $patient_insert = $this->db->insert('users', $data_user);
            $u_id = $this->db->insert_id();
            $data2 = array(
                    'doctor_id' => $doctor_id,
                    'user_id' =>$u_id,
                    'patient_name' => $patient_name,
                    'address' => $address,
                    'state' => $state,
                    'city' => $city,
                    'pincode' => $pincode,
                    'contact_no' => $contact_no,
                    'gender' => $gender,
                    'date_of_birth' => $date_of_birth,
                    'blood_group' => $blood_group,
                    'medical_profile' => $medical_profile,
                    'email' => $email,
        			'created_date' => $created_date,
        			'user_detail_access'=>'1'
                );
            
            $patient_insert = $this->db->insert('doctor_patient', $data2);
           
            if ($patient_insert) {
                return array(
                    'status' => 200,
                    'message' => 'success',
                    'patient_id' => $u_id,
                    'patient_name' => $patient_name
                );
            } else {
                return array(
                    'status' => 200,
                    'message' => 'failure',
                    'patient_id' => 0,
                    'patient_name' => ''
                );
            }
        }
   
        
    }

    public function add_prescription1($doctor_id,$clinic_id,$patient_id,$prescription_note,$medicine_name,$dosage,$dosage_unit,$frequency_first,$frequency_second,$frequency_third,$instruction,$category,$test,$test_instruction,$booking_id,$booking_type, $days) {
        date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
 
        if($patient_id==0)
        {
             $query1 = $this->db->query("select user_id from doctor_booking_master where booking_id='$booking_id'");
             $fm_id = $query1->row_array(); 
             $patient_id=$fm_id['user_id'];
        }
        else
        {
            $patient_id;
        }
 
 
		$doctor_prescription = array(
            'doctor_id' => $doctor_id,
			'clinic_id' => $clinic_id,
			'patient_id' => $patient_id,
			'prescription_note' => $prescription_note,
			'created_date' => $created_date
        );
        $this->db->insert('doctor_prescription', $doctor_prescription);
        $prescription_id = $this->db->insert_id();

        $medicine_name_array = explode(",", $medicine_name);
		$dosage_array = explode(",", $dosage);
		$dosage_unit_array = explode(",", $dosage_unit);
		$frequency_first_array = explode(",", $frequency_first);
		$frequency_second_array = explode(",", $frequency_second);
		$frequency_third_array = explode(",", $frequency_third);
		$instruction_array = explode(",", $instruction);
        $cnt = count($medicine_name_array);
        $medicine_Name_dhlr_morning="";
                        $medicine_Name_dhlr_afternoon="";
                        $medicine_Name_dhlr_evening="";
        for ($i = 0; $i < $cnt; $i++) {
            $doctor_prescription_medicine = array(
				'prescription_id' => $prescription_id,
                'medicine_name' => $medicine_name_array[$i],
				'dosage' => $dosage_array[$i],
				'dosage_unit' => $dosage_unit_array[$i],
				'frequency_first' => $frequency_first_array[$i],
				'frequency_second' => $frequency_second_array[$i],
				'frequency_third' => $frequency_third_array[$i],
				'instruction' => $instruction_array[$i],
			    'day'=>$days
            );
          $insert_test = $this->db->insert('doctor_prescription_medicine', $doctor_prescription_medicine);
           if(!empty($morning[$i]))
                    {
                        $medicine_Name_dhlr_morning .= $medicineName[$i].",";
                    }
                     if(!empty($afternoon[$i]))
                    {
                        $medicine_Name_dhlr_afternoon .= $medicineName[$i].",";
                    }
                     if(!empty($evening[$i]))
                    {
                        $medicine_Name_dhlr_evening .= $medicineName[$i].",";
                    }
          
          
          
          
        }
        $medicine_Name_dhlr_morning; 
                $medicine_Name_dhlr_afternoon;
                 $medicine_Name_dhlr_evening;
               
                 date_default_timezone_set('Asia/Calcutta');
                  $created_at = date('Y-m-d H:i:s');
                  $date = date('Y-m-d');
                if(!empty($medicine_Name_dhlr_morning))
                {
                   $data_dhlr_morning = array(
                                           
                        'user_id' => $userid,
                        'time'=>'9.00 AM',
                        'title'=>$medicine_Name_dhlr_morning,
                        'status'=>0,
                        'created_at' => $created_at
                    );
                    
                    $dhlr_id   = $this->db->insert('dhlr', $data_dhlr_morning);
                   
                   
                    
                }
                if(!empty($medicine_Name_dhlr_afternoon))
                {
                    $data_dhlr_afternoon = array(
                                           
                        'user_id' => $userid,
                        'time'=>'2.00 PM',
                        'title'=>$medicine_Name_dhlr_afternoon,
                        'status'=>0,
                        'created_at' => $created_at
                    );
                    $dhlr_id   = $this->db->insert('dhlr', $data_dhlr_afternoon);
                }
                if(!empty($medicine_Name_dhlr_evening))
                {
                   $data_dhlr_evening = array(
                                           
                        'user_id' => $userid,
                        'time'=>'9.00 PM',
                        'title'=>$medicine_Name_dhlr_evening,
                        'status'=>0,
                        'created_at' => $created_at
                    );
                    $dhlr_id   = $this->db->insert('dhlr', $medicine_Name_dhlr_evening);
                }
                
        
        
        
		$test_array = explode(",", $test);
		$category_array = explode(",", $category);
        $testcnt = count($test_array);
        for ($j = 0; $j < $testcnt; $j++) {
            //added by 6 june 
           if($category_array[$j] == "")
           {
               
           }
           else
           {
        $query = $this->db->query("select `id` from doctor_test where test_name='$category_array[$j]'");
        $test_id = $query->row_array();
       
            $doctor_prescription_test = array(
				'prescription_id' => $prescription_id,
                'test' => $test_array[$j],
				'category' => $test_id['id']
            );
            
            $insert_test = $this->db->insert('doctor_prescription_test', $doctor_prescription_test);
           }
        }
        if ($insert_test) {
               //added by jakir on 17-july-2018 for notification on add prescription 
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$patient_id'");
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $patient_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $patient_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', Your prescription is added by doctor and prescription id is' . $prescription_id;
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$patient_id'");
                $title = $usr_name . ', Your prescription is added by doctor and prescription id is' . $prescription_id;
                $customer_token_count = $customer_token->num_rows();

                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = $token_status['token'];
                    $img_url = $userimage;
                    $tag = 'text';
                    $key_count = '1';
                    $this->send_gcm_notify($title, $reg_id, $msg, $img_url,$tag,$agent,$prescription_id);
                }
           // doctor_booking_master
           
           if($booking_type == 'inperson')
           {
            //$query = $this->db->query("UPDATE doctor_booking_master SET status='7' WHERE booking_id =$booking_id  AND user_id='$patient_id'");
           }
           else
           {
            $query = $this->db->query("UPDATE doctor_booking_master SET status='7' WHERE booking_id =$booking_id");

           }
            $this->print_item($prescription_id);
            
            
            return array(
                'status' => 200,
                'message' => 'success',
                'prescription_id' => "$prescription_id",/*
                'patient_name' => $patient_name*/
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure',/*
                'patient_id' => 0,
                'patient_name' => ''*/
            );
        }
		/*return array(
            'status' => 201,
            'message' => 'success'
        );*/
    }
    
    
    // added by swapnali on 29th may add_prescription were not working
    
     public function add_prescription($doctor_id,$clinic_id,$patient_id,$prescription_note,$medicine_name,$dosage,$dosage_unit,$frequency_first,$frequency_second,$frequency_third,$instruction,$category,$test,$test_instruction,$booking_id,$booking_type, $days) {
        date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
 
        if($patient_id==0)
        {
             $query1 = $this->db->query("select user_id from doctor_booking_master where booking_id='$booking_id'");
             $fm_id = $query1->row_array(); 
             $patient_id=$fm_id['user_id'];
        }
        else
        {
            $patient_id;
        }
 
 
		$doctor_prescription = array(
            'doctor_id' => $doctor_id,
			'clinic_id' => $clinic_id,
			'patient_id' => $patient_id,
			'prescription_note' => $prescription_note,
			'created_date' => $created_date,
			'booking_id'=>$booking_id
        );
        $this->db->insert('doctor_prescription', $doctor_prescription);
        $prescription_id = $this->db->insert_id();

        $medicine_name_array = explode(",", $medicine_name);
		$dosage_array = explode(",", $dosage);
		$dosage_unit_array = explode(",", $dosage_unit);
		$frequency_first_array = explode(",", $frequency_first);
		$frequency_second_array = explode(",", $frequency_second);
		$frequency_third_array = explode(",", $frequency_third);
		$instruction_array = explode(",", $instruction);
        $cnt = count($medicine_name_array);
        $medicine_Name_dhlr_morning="";
                        $medicine_Name_dhlr_afternoon="";
                        $medicine_Name_dhlr_evening="";
        for ($i = 0; $i < $cnt; $i++) {
            $doctor_prescription_medicine = array(
				'prescription_id' => $prescription_id,
                'medicine_name' => $medicine_name_array[$i],
				'dosage' => $dosage_array[$i],
				'dosage_unit' => $dosage_unit_array[$i],
				'frequency_first' => $frequency_first_array[$i],
				'frequency_second' => $frequency_second_array[$i],
				'frequency_third' => $frequency_third_array[$i],
				'instruction' => $instruction_array[$i],
			    'day'=>$days
            );
          $insert_test = $this->db->insert('doctor_prescription_medicine', $doctor_prescription_medicine);
        //   print_r($frequency_first_array); die();
        //   if(!empty($morning[$i]))
           
           if(!empty($frequency_first_array[$i]))
                    {
                        $medicine_Name_dhlr_morning .= $frequency_first_array[$i]." break fast ,";
                    }
                    //  if(!empty($afternoon[$i]))
                     
                        if(!empty($frequency_second_array[$i]))
                    {
                        $medicine_Name_dhlr_afternoon .= $frequency_second_array[$i]." lunch,";
                    }
                    //  if(!empty($evening[$i]))
                     
                         if(!empty($frequency_third_array[$i]))
                    {
                        $medicine_Name_dhlr_evening .= $frequency_third_array[$i]." dinner,";
                    }
          
          
          
          
        }
        $medicine_Name_dhlr_morning; 
                $medicine_Name_dhlr_afternoon;
                 $medicine_Name_dhlr_evening;
               
                 date_default_timezone_set('Asia/Calcutta');
                  $created_at = date('Y-m-d H:i:s');
                  $date = date('Y-m-d');
                if(!empty($medicine_Name_dhlr_morning))
                {
                   $data_dhlr_morning = array(
                                           
                        'user_id' => $patient_id,
                        'time'=>'9.00 AM',
                        'title'=>$medicine_Name_dhlr_morning,
                        'status'=>0,
                        'created_at' => $created_at
                    );
                    
                    $dhlr_id   = $this->db->insert('dhlr', $data_dhlr_morning);
                   
                   
                    
                }
                if(!empty($medicine_Name_dhlr_afternoon))
                {
                    $data_dhlr_afternoon = array(
                                           
                        'user_id' => $patient_id,
                        'time'=>'2.00 PM',
                        'title'=>$medicine_Name_dhlr_afternoon,
                        'status'=>0,
                        'created_at' => $created_at
                    );
                    $dhlr_id   = $this->db->insert('dhlr', $data_dhlr_afternoon);
                }
                if(!empty($medicine_Name_dhlr_evening))
                {
                   $data_dhlr_evening = array(
                                           
                        'user_id' => $patient_id,
                        'time'=>'9.00 PM',
                        'title'=>$medicine_Name_dhlr_evening,
                        'status'=>0,
                        'created_at' => $created_at
                    );
                    $dhlr_id   = $this->db->insert('dhlr', $data_dhlr_evening);
                }
                
        
        
        
		$test_array = explode(",", $test);
		$category_array = explode(",", $category);
        $testcnt = count($test_array);
        for ($j = 0; $j < $testcnt; $j++) {
            //added by 6 june 
           if($category_array[$j] == "")
           {
               
           }
           else
           {
        $query = $this->db->query("select `id` from doctor_test where test_name='$category_array[$j]'");
        $test_id = $query->row_array();
       
            $doctor_prescription_test = array(
				'prescription_id' => $prescription_id,
                'test' => $test_array[$j],
				'category' => $test_id['id']
            );
            
            $insert_test = $this->db->insert('doctor_prescription_test', $doctor_prescription_test);
           }
        }
        if ($insert_test) {
               //added by jakir on 17-july-2018 for notification on add prescription 
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$patient_id'");
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $patient_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $patient_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', Your prescription is added by doctor and prescription id is' . $prescription_id;
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$patient_id'");
                $title = $usr_name . ', Your prescription is added by doctor and prescription id is' . $prescription_id;
                $customer_token_count = $customer_token->num_rows();

                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = $token_status['token'];
                    $img_url = $userimage;
                    $tag = 'text';
                    $key_count = '1';
                    $this->send_gcm_notify($title, $reg_id, $msg, $img_url,$tag,$agent,$prescription_id);
                }
           // doctor_booking_master
           
           if(!empty($booking_type))
           {
            //$query = $this->db->query("UPDATE doctor_booking_master SET status='7' WHERE booking_id =$booking_id  AND user_id='$patient_id'");
            $query = $this->db->query("UPDATE doctor_booking_master SET status='7' WHERE booking_id =$booking_id");
           }
           else
           {
            

           }
            $this->print_item($prescription_id);
            
            
            return array(
                'status' => 200,
                'message' => 'success',
                'prescription_id' => (string)$prescription_id,/*
                'patient_name' => $patient_name*/
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure',/*
                'patient_id' => 0,
                'patient_name' => ''*/
            );
        }
		/*return array(
            'status' => 201,
            'message' => 'success'
        );*/
    }
   
    public function send_message_prescription($prescription_id){
            //$data= $this->booking_model->send_notification_to_user_about_confirmation_for_cancel($id);
               $sql = "select doctor_prescription.*,doctor_clinic.clinic_name,doctor_clinic.address,doctor_list.doctor_name,doctor_list.address,doctor_patient.patient_name,doctor_patient.address,doctor_patient.contact_no FROM doctor_prescription LEFT JOIN doctor_clinic ON (doctor_prescription.clinic_id=doctor_clinic.id) LEFT JOIN doctor_list ON (doctor_prescription.doctor_id=doctor_list.id) LEFT JOIN doctor_patient ON (doctor_prescription.patient_id=doctor_patient.id) LEFT JOIN doctor_prescription_medicine ON(doctor_prescription.id=doctor_prescription_medicine.prescription_id) WHERE doctor_prescription.id='$prescription_id'";
               $result = $this->db->query($sql)->row();
               //echo $result->contact_no;
            //$sendconfirmation_for_cancel = $this->booking_model->send_notification_to_user_about_confirmation_for_cancel($id);
        
            $message = 'Dear '.$result->patient_name.', your presciption has been recieved  by '.$result->doctor_name.'Thank you.';
            $post_data = array('From' => '02233721563', 'To' => $result->contact_no, 'Body' => $message);
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
            
            //echo 1;
            
            
            
    }
    
     //send notification through firebase
    public function send_gcm_notify($title, $reg_id, $msg, $img_url,$tag,$agent,$prescription_id) {
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
                'notification_prescripton_id' => $prescription_id,
                "notification_type" => 'doctor_prescription',
                "notification_date" => $date,
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch
        , CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }
    
    
    
    
      public function send_gcm_notify_1($title, $reg_id, $msg, $img_url,$tag,$agent,$doctor_id) {
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
                'notification_doctor_id' => $doctor_id,
                "notification_type" => 'doctor_invitation',
                "notification_date" => $date,
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch
        , CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }
    
    public function create_pdf($doctor_id, $clinic_id, $patient_id, $prescription_id, $prescription_note, $medicine_name, $dosage, $dosage_unit, $frequency_first, $frequency_second, $frequency_third, $instruction, $category, $test, $test_instruction){
        echo "SELECT doctor_prescription.*,doctor_clinic.clinic_name,doctor_clinic.address,doctor_list.doctor_name,doctor_list.address,doctor_patient.patient_name,doctor_patient.address FROM doctor_prescription LEFT JOIN doctor_clinic ON (doctor_prescription.clinic_id=doctor_clinic.id) LEFT JOIN doctor_list ON (doctor_prescription.doctor_id=doctor_list.id) LEFT JOIN doctor_patient ON (doctor_prescription.patient_id=doctor_patient.id) LEFT JOIN doctor_prescription_medicine ON(doctor_prescription.id=doctor_prescription_medicine.prescription_id) WHERE doctor_prescription.id='$prescription_id'";
      /*  $query = $this->db->query("SELECT doctor_prescription.*,doctor_clinic.clinic_name,doctor_clinic.address,doctor_list.doctor_name,doctor_list.address,doctor_patient.patient_name,doctor_patient.address FROM doctor_prescription LEFT JOIN doctor_clinic ON (doctor_prescription.clinic_id=doctor_clinic.id) LEFT JOIN doctor_list ON (doctor_prescription.doctor_id=doctor_list.id) LEFT JOIN doctor_patient ON (doctor_prescription.patient_id=doctor_patient.id) LEFT JOIN doctor_prescription_medicine ON(doctor_prescription.id=doctor_prescription_medicine.prescription_id) WHERE doctor_prescription.id='$prescription_id'");
        $count = $query->num_rows();*/
        
    }
    
    public function search_test() {
        $query = $this->db->query("SELECT id,test_name FROM `doctor_test` order by test_name asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $test_name = $row['test_name'];
                $resultpost[] = array(
                    "id" => $id,
                    "category" => $test_name
                );
            }
        } else {
            $resultpost = array();
        }
        $sub_query = $this->db->query("SELECT id,test as sub_test_name FROM `lab_all_test` order by test asc");
        $sub_count = $sub_query->num_rows();
        if ($sub_count > 0) {
            foreach ($sub_query->result_array() as $sub_row) {
                $id = $sub_row['id'];
                $sub_test_name = $sub_row['sub_test_name'];
                $sub_resultpost[] = array(
                    "id" => $id,
                    "category" => 'Test',
                    "test" => $sub_test_name
                );
            }
        } else {
            $resultpost = array();
        }
        $allresultpost = array('category' => $resultpost, 'test' => $sub_resultpost);
        return $allresultpost;
    }
    
    public function patient_search($doctor_id,$keyword) {
        date_default_timezone_set('Asia/Kolkata');
        $date=date('Y-m-d');
        $query = $this->db->query("SELECT DISTINCT user_id, user_id,patient_name  as keyword,gender,date_of_birth,email,contact_no FROM doctor_patient WHERE patient_name like '%$keyword%' and doctor_id='$doctor_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $booking="";
                $id = $row['user_id'];
                $keyword = $row['keyword'];
                $gender = $row['gender'];
                $dob = $row['date_of_birth'];
                $email = $row['email'];
                $contact_no = $row['contact_no'];
                if(empty($dob)){
                  $dob="";  
                }
                if(!empty($id))
                {
                $sel=$this->db->query("SELECT booking_id FROM `doctor_booking_master` where (user_id='$id' or patient_id='$id') and booking_date <= $date ");
                $count1 = $sel->num_rows();
                if ($count1 > 0) 
                   {
                       $row=$sel->row_array();
                       $booking=$row['booking_id'];
                       
                   }
                else
                {
                    $booking="";
                }
                 $resultpost[] = array(
                    "id" => $id,
                    "keyword" => $keyword,
                    "booking_id"=>$booking,
                    "gender"=>$gender,
                    "dob"=>"$dob",
                     "email"=>$email,
                    "contact_no"=>"$contact_no"
                );
                }
               
                
                
               
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    function sendmail($email, $password, $login_url) {
        $subject = "REGISTRATION INFORMATION";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Medicalwale.com <no-reply@medicalwale.com>' . "\r\n";
        $headers .= 'Cc: ' . "\r\n";
        $message = '<div style="max-width: 700px;float: none;margin: 0px auto;"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1"><div id="styles_holder"><style>.ReadMsgBody{width:100%;background-color:#fff}.ExternalClass{width:100%;background-color:#fff}.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div{line-height:100%}html{width:100%}body{-webkit-text-size-adjust:none;-ms-text-size-adjust:none;margin:0;padding:0}table{border-spacing:0;border-collapse:collapse;table-layout:fixed;margin:0 auto}table table table{table-layout:auto}img{display:block !important}table td{border-collapse:collapse}.yshortcuts a{border-bottom:none !important}a{color:#1abc9c;text-decoration:none}@media only screen and (max-width: 640px){body{width:auto !important}table[class="table-inner"]{width:90% !important}table[class="table-full"]{width:100% !important;text-align:center !important}img[class="img1"]{width:100% !important;height:auto !important}}@media only screen and (max-width: 479px){body{width:auto !important}table[class="table-inner"]{width:90% !important}table[class="table-full"]{width:100% !important;text-align:center !important}img[class="img1"]{width:100% !important}}</style></div><div id="frame" class="ui-sortable"><table data-thumb="" data-module="header-bar" data-bgcolor="Main BG" align="center" width="100%" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class=""><tbody><tr><td data-border-top-color="Top Border" style="border-top:4px solid #049341;"></td></tr><tr><td height="25"></td></tr><tr><td align="center"><table data-bgcolor="Container" width="600" class="table-inner" bgcolor="#ffffff" style="border-top-left-radius:5px;border-top-right-radius:5px;" border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" style="border-bottom: 5px solid #049341;"><table class="table-inner" width="520" border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td style="padding: 10px 0px;"><table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" style="line-height:0px;"> <img data-crop="false" mc:edit="quinn-box-1" style="display:block; line-height:0px; font-size:0px; border:0px;height: 70px;" src="https://d2c8oti4is0ms3.cloudfront.net/images/img/email-logo.png" alt="logo" ></td></tr></tbody></table><table border="0" align="right" cellpadding="0" cellspacing="0" class="table-full"><tbody><tr><td height="15"></td></tr><tr><td align="center"><table border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td align="center" style=""> <font style="font-size:11px;line-height:16px" face="font-family: arial,sans-serif;" color="#666666"> <b style="font-size: 12px;font-family: arial, sans-serif;">Congratulations On Registration</b><br> </font> <font style="font-size:14px" face="HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue, Helvetica, Arial, sans-serif" color="#666666"> Call Us: </font><font style="font-size:16px" face="HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica, Arial, sans-serif" color="#7db701"><strong>022-60123457</strong></font></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table data-thumb="" data-module="1-2-right" data-bgcolor="Main BG" width="100%" align="center" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class=""><tbody><tr><td align="center"><table data-bgcolor="Container" bgcolor="#FFFFFF" class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="height: 250px;"><tbody style="background: url(https://d2c8oti4is0ms3.cloudfront.net/images/img/mail_bg.jpg);background-size: cover;"><tr><td height="20"></td></tr><tr><td align="center"><table class="table-inner" width="570" border="0" align="center" cellpadding="0" cellspacing="0"><tbody ><tr><td><table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0" style="width: 320px;"><tbody><tr><td align="left" style="padding-bottom: 10px;"><p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >Welcome to </font><br> <font style="font-size:22px;color:#fff;" face="arial,sans-serif" ><span class="il">Medicalwale.com</span></font></p><p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >You are now part of 50,000+ Healthcare Service providers. We are delighted to have you on board with us. </font></p><p><font style="font-size:14px;color:#fff;" face="arial,sans-serif" >For any clarification <br> Mail us on: <a href="mailto:partner@medicalwale.com" style="color:#fff">partner@medicalwale.com</a> </font></p></td></tr></tbody></table><table border="0" align="right" cellpadding="0" cellspacing="0" class="table-full" style="width: 250px;"><tbody><tr><td><table style="background-color:rgb(249,246,246);border:0px solid rgb(213,210,210);width:100%;" cellpadding="0" cellspacing="0"><tbody><tr><td style="padding-left:20px;padding-top:10px;padding-bottom:10px;padding-right:10px; background: #a8abaf; text-align: center;" valign="top" align="left"><font style="font-size:16px" face="arial,sans-serif" color="#ffffff">Your Login Details</font></td></tr><tr><td style="padding:18px 15px 4px;background: #fff;" align="left"> <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Email:</font> <font style="font-size:14px;line-height:21px" face="arial,sans-serif" color="#333333"> <a href="mailto:' . $email . '" target="_blank" style="color: #656060;text-decoration: none;">' . $email . '</a></font></td></tr><tr><td style="padding-bottom:18px;padding-top:4px;padding-left:15px;background: #fff;" align="left"> <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Password :</font> <font style="font-size:14px;line-height:21px;color: #656060" face="arial,sans-serif" >' . $password . '</font></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td height="20"></td></tr></tbody></table></td></tr></tbody></table><table data-thumb="" data-module="quote" data-bgcolor="Main BG" width="100%" align="center" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class="" ><tbody><tr><td data-bgcolor="Feature BG" align="center" bgcolor="#e7e7e7"><table data-bgcolor="Feature inner" bgcolor="#fff" class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"><tbody><tr><td align="center"><table class="table-inner" width="520" border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td height="35"></td></tr><tr><td height="5"></td></tr><tr><td data-link-style="text-decoration:none; color:#4a4a4a;" data-link-color="Quote" data-size="Quote" data-color="Quote" mc:edit="quinn-box-56" align="center" style="font-family: century Gothic, arial, sans-serif; font-size:16px; color:#4a4a4a; font-weight:bold;">"Your partnership means a lot to us and your constant support is what keeps us going. We look forward to continuing our relation in years to come!"</td></tr><tr><td height="35"></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table><table data-thumb="" data-module="footer" data-bgcolor="Main BG" width="100%" bgcolor="#e7e7e7" border="0" align="center" cellpadding="0" cellspacing="0" class=""><tbody><tr><td data-bgcolor="Footer Bar" bgcolor="#191919" align="center" style="font-family: Open Sans, Arial, sans-serif; font-size:11px; color:#ffffff;background: #e7e7e7;"><table class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0"><tbody><tr><td><table class="table-full" bgcolor="#e7e7e7" border="0" align="left" cellpadding="0" cellspacing="0" style="width: 100%;"><tbody><tr><td data-link-style="text-decoration:none; color:#ffffff;" data-link-color="Footer Link" data-size="Content" data-color="Footer Text" mc:edit="quinn-box-81" height="30" style="font-family: Open Sans, Arial, sans-serif; font-size:12px; color: #6d6d6d;line-height: 19px;text-align:center; padding: 10px 0px;"> By using these service, you agree that you are bound by the Terms of Service.<br/>Copyright 2017 AEGIS HEALTH SOLUTIONS PVT. LTD. All rights reserved.</td></tr><tr><td height="15"></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></div></div>';

        $sentmail = mail($email, $subject, $message, $headers);
    }

    function randomPassword() {
        $pass = rand(100000, 999999);
        return $pass;
    }

    public function council_list($type) {
        $query = $this->db->query("SELECT `id`, `type`, `state`, `council`, `council_name` FROM `council` WHERE type='$type' order by council_name asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $council_name = $row['council_name'];
                $resultpost[] = array
                    (
                    "council_name" => $council_name
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function council_list_search($type, $keyword) {
        $query = $this->db->query("SELECT `id`, `type`, `state`, `council`, `council_name` FROM `council` WHERE type='$type' AND council_name LIKE '%$keyword%' order by council_name asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $council_name = $row['council_name'];
                $resultpost[] = array(
                    "council_name" => $council_name
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function partner_area_expertise_model($category, $keyword) {
        return $this->db->select('id,category')->from('business_category')->where('category_id', $category)->like('category', $keyword)->order_by('category', 'asc')->get()->result();
    }

    public function partner_doctor_specialization_model($keyword) {
        return $this->db->select('id,specialization as category')->from('doctor_specialization')->like('specialization', $keyword)->order_by('specialization', 'asc')->get()->result();
    }

    public function partner_doctor_service_model($keyword) {
        return $this->db->select('id,service as category')->from('doctor_services')->like('service', $keyword)->order_by('service', 'asc')->get()->result();
    }

    public function partner_patient_list_model($doctor_id, $keyword,$type) 
    {
        
        date_default_timezone_set('Asia/Kolkata');
        $date=date('Y-m-d');
        //echo "SELECT user_id,patient_name,contact_no,type FROM doctor_patient WHERE patient_name like '%$keyword%' and doctor_id='$doctor_id'";
        $query = $this->db->query("SELECT user_id,patient_name,contact_no,type FROM doctor_patient WHERE patient_name like '%$keyword%' and doctor_id='$doctor_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['user_id'];
                $patient_name = $row['patient_name'];
                $contact_no = $row['contact_no'];
                $type=$row['type'];
                $sel=$this->db->query("SELECT booking_id FROM `doctor_booking_master` where (user_id='$id' or patient_id='$id') and booking_date <= $date ");
                $count1 = $sel->num_rows();
                if ($count1 > 0) 
                   {
                       $row=$sel->row_array();
                       $booking=$row['booking_id'];
                    }
                else
                {
                    $booking="";
                }
                
                
                
                $resultpost[] = array(
                    "id" => $id,
                    "patient_name" => $keyword,
                    "contact_no" => $contact_no,
                    "type" => $type,
                    "booking_id"=>$booking
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
        
       
    }

    public function partner_clinic_list_model($doctor_id, $keyword) {
        return $this->db->select('id,clinic_name')->from('doctor_clinic')->where('doctor_id', $doctor_id)->like('clinic_name', $keyword)->order_by('clinic_name', 'asc')->get()->result();
    }

    public function partner_medicines_list_model($keyword) {
        return $this->db->select('id,product_name')->from('product')->like('product_name', $keyword)->get()->result();
    }

    public function signup($category, $type, $doctor_name, $email, $phone, $qualification, $experience, $gender, $dob, $reg_council, $reg_number, $token, $agent) {
        if ($doctor_name != '' && $email != '' && $phone != '') {
            $vendor_id = $type;
            $query = $this->db->query("SELECT id from users WHERE phone='$phone'");
            $count = $query->num_rows();
            $query2 = $this->db->query("SELECT id from users WHERE email='$email'");
            $count2 = $query2->num_rows();
            if ($count > 0) {
                return array(
                    'status' => 208,
                    'message' => 'Phone number already exist'
                );
            } else if ($count2 > 0) {
                return array(
                    'status' => 208,
                    'message' => 'Email id already exist'
                );
            } else {
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
                $pas = $this->randomPassword();
                $vendor_id = $vendor_id;
                $password = md5($pas);
                $user_data = array(
                    'name' => $doctor_name,
                    'email' => $email,
                    'phone' => $phone,
                    'gender' => $gender,
                    'dob' => $dob,
                    'vendor_id' => $vendor_id,
                    'password' => $password,
                    'token' => $token,
                    'agent' => $agent,
                    'token_status' => '1',
                    'created_at' => $updated_at
                );
                $success = $this->db->insert('users', $user_data);
                $id = $this->db->insert_id();
                $doctor_data = array(
                    'user_id' => $id,
                    'doctor_name' => $doctor_name,
                    'category' => $category,
                    'email' => $email,
                    'telephone' => $phone,
                    'gender' => $gender,
                    'dob' => $dob,
                    'reg_council' => $reg_council,
                    'reg_number' => $reg_number,
                    'reg_date' => $updated_at,
                    'qualification' => $qualification,
                    'experience' => $experience,
                    'image' => "",
                    'is_approval' => 1
                );
                $success = $this->db->insert('doctor_list', $doctor_data);
                if ($success) {
                    $date_array = array(
                        'listing_id' => $id,
                        'name' => $doctor_name,
                        'type' => $vendor_id,
                        'phone' => $phone,
                        'email' => $email
                    );
                    return array(
                        'status' => 201,
                        'message' => 'success',
                        'data' => $date_array
                    );
                } else {
                    return array(
                        'status' => 208,
                        'message' => 'failed'
                    );
                }
            }
        }
    }

    public function doctor_profile_pic($listing_id, $profile_pic_file) {
        $query = $this->db->query("UPDATE doctor_list SET image='$profile_pic_file' WHERE user_id='$listing_id'");
        $usr_query = $this->db->query("SELECT avatar_id FROM users WHERE id='$listing_id'");
        $get_usr = $usr_query->row_array();
        $avatar_id = $get_usr['avatar_id'];
        date_default_timezone_set('Asia/Kolkata');
        $updated_at = date('Y-m-d H:i:s');
        if ($avatar_id == '0') {
            $media_data = array(
                'title' => $profile_pic_file,
                'type' => 'image',
                'source' => $profile_pic_file,
                'created_at' => $updated_at,
                'deleted_at' => $updated_at
            );
            $media_insert = $this->db->insert('media', $media_data);
            $a_id = $this->db->insert_id();
            $query = $this->db->query("UPDATE users SET avatar_id='$a_id' WHERE id='$listing_id'");
        } else {
            $query = $this->db->query("UPDATE media SET title='$profile_pic_file',source='$profile_pic_file' WHERE id='$avatar_id'");
        }
        $actual_image_path = 'images/healthwall_avatar/' . $profile_pic_file;
        return array(
            'status' => 200,
            'message' => 'success',
            'image' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/$profile_pic_file",
        );
    }



public function doctor_pic($groupname,$group_user_id,$profile_pic_file) 
{
        /*$query = $this->db->query("UPDATE doctor_group_img SET img_url='$profile_pic_file' WHERE group_user_id='$group_user_id'");
        date_default_timezone_set('Asia/Kolkata');
        $updated_at = date('Y-m-d H:i:s');
        
         $query = $this->db->query("SELECT `img_url` FROM `doctor_group_img` WHERE `group_user_id`='$uid'");
        $count = $query->num_rows();
        if ($count > 0) { 
            
            echo "Same Image Not Add At the Time";
            
        }
        
       */
            $data = array(
                     'group_name' => $groupname,
                     'group_user_id' => $group_user_id,
                    'img_url' =>$profile_pic_file
                          );
            $doctor_group_img = $this->db->insert('doctor_group_img',$data);
     
         return array(
            'status' => 200,
            'message' => 'success',
            'img_url' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/$profile_pic_file",
        );
    }


public function doctor_pic_list111($group_user_id) 
{
           $file_query = $this->db->query("SELECT * FROM `doctor_group_img` WHERE  group_user_id='$group_user_id'");
            $row = $file_query->result_array();
               
            $data = array(
                     'group_name' => $row['group_name'],
                     'group_user_id' => $row['group_user_id'],
                     'img_url' =>'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$row['img_url']
                          );
            //$doctor_group_img = $this->db->insert('doctor_group_img',$data);
     
         return array(
            'status' => 200,
            'message' => 'success',
            'img_url' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/$profile_pic_file",
        );
    }
    
      public function doctor_pic_list($uid) 
      {
          
        $query = $this->db->query("SELECT * FROM `doctor_group_img` WHERE `group_user_id` ='$uid'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $group_name = $row['group_name'];
                $group_user_id = $row['group_user_id'];
                $profile_pic_file = $row['img_url'];

                $resultpost[] = array(
                    "id" => $id,
                    "group_name" => $group_name,
                    "group_user_id" => $group_user_id,
                    "img_url" => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/$profile_pic_file",
              
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }


    public function doctor_my_profile_details($listing_id, $type) {
        $query = $this->db->query("SELECT doctor_list.image,doctor_list.about_us,doctor_list.speciality,doctor_list.service,doctor_list.awards_recognition, doctor_list.awards_year,doctor_list.doctor_name, doctor_list.email, doctor_list.qualification, doctor_list.experience, doctor_list.gender, doctor_list.dob, doctor_list.reg_number, doctor_list.reg_council,doctor_list.is_approval,doctor_list.is_active, users.phone FROM doctor_list LEFT JOIN users ON doctor_list.user_id=users.id WHERE doctor_list.user_id='$listing_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            $row = $query->row_array();
            $doctor_name = $row['doctor_name'];
            $email = $row['email'];
            $phone = $row['phone'];
            $qualification = $row['qualification'];
            $experience = $row['experience'];
            $gender = $row['gender'];
            $dob = $row['dob'];
            $reg_number = $row['reg_number'];
            $reg_council = $row['reg_council'];
            $profile_pic = $row['image'];
            $is_approval = $row['is_approval'];
            $is_active = $row['is_active'];
            $about_us = $row['about_us'];
            $speciality = str_replace(", ", ",", $row['speciality']);
            $service = str_replace(", ", ",", $row['service']);
            $awards_recognition = str_replace(", ", ",", $row['awards_recognition']);
            $awards_year = str_replace(", ", ",", $row['awards_year']);
            $speciality = explode(',', $speciality);
            $service = explode(',', $service);
            if ($row['image'] != '') {
                $profile_pic = $row['image'];
                $profile_pic = str_replace(' ', '%20', $profile_pic);
                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
            } else {
                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
            }
            $doctor_profile = array(
                "doctor_name" => $doctor_name,
                "profile_pic" => $profile_pic,
                "email" => $email,
                "qualification" => $qualification,
                "experience" => $experience,
                "gender" => $gender,
                "dob" => $dob,
                "reg_number" => $reg_number,
                "reg_council" => $reg_council,
                "phone" => $phone,
                "is_approve" => (int) $is_approval,
                "is_active" => (int) $is_active
            );
            $specialization_query = $this->db->query("SELECT id,specialization FROM `doctor_specialization` order by specialization asc");
            foreach ($specialization_query->result_array() as $specialization_list) {
                $id = $specialization_list['id'];
                $speciality_value = $specialization_list['specialization'];
                if (in_array($speciality_value, $speciality)) {
                    $is_specialization_active = '1';
                } else {
                    $is_specialization_active = '0';
                }
                $specialization_result[] = array(
                    "id" => (int) $id,
                    "name" => $speciality_value,
                    "is_specialization_active" => (int) $is_specialization_active
                );
            }
            $service_query = $this->db->query("SELECT id,service FROM `doctor_services` order by service asc");
            foreach ($service_query->result_array() as $service_list) {
                $id = $service_list['id'];
                $servic_value = $service_list['service'];
                if (in_array($servic_value, $service)) {
                    $is_service_active = '1';
                } else {
                    $is_service_active = '0';
                }
                $service_result[] = array(
                    "id" => (int) $id,
                    "name" => $servic_value,
                    "is_service_active" => (int) $is_service_active
                );
            }
            $awards_recognition = explode(",", $awards_recognition);
            $awards_year = explode(",", $awards_year);
            foreach ($awards_recognition as $index => $awards) {
                $doctor_awards_recognition[] = array(
                    "awards_recognition" => $awards,
                    "awards_year" => (int) $awards_year[$index]
                );
            }
            $resultpost[] = array(
                "doctor_profile" => $doctor_profile,
                "about_us" => $about_us,
                "specialization" => $specialization_result,
                "service" => $service_result,
                "doctor_awards_recognition" => $doctor_awards_recognition
            );
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function doctor_specialization_update($listing_id, $speciality) {
        $query = $this->db->query("SELECT * FROM `doctor_list` WHERE user_id = '$listing_id'");
        $doctor_specialization = array(
            "speciality" => $speciality
        );
        $this->db->where('user_id', $listing_id);
        $this->db->UPDATE('doctor_list', $doctor_specialization);
        return array(
            'status' => 201,
            'message' => 'success',
            'speciality' => $speciality
        );
        return $resultpost;
    }

    public function doctor_specialization() {
        return $this->db->select('id,specialization')->from('doctor_specialization')->order_by('id', 'asc')->get()->result();
    }

    public function doctor_services() {
        return $this->db->select('id,service')->from('doctor_services')->order_by('id', 'asc')->get()->result();
    }

    public function doctor_details($listing_id, $type) {
        $query = $this->db->query("SELECT doctor_list.reg_date,doctor_list.image, doctor_list.doctor_name, doctor_list.lat, doctor_list.lng, doctor_list.email, doctor_list.qualification, doctor_list.experience, doctor_list.gender, doctor_list.dob, doctor_list.reg_number, doctor_list.reg_council, users.phone, doctor_list.is_approval, doctor_list.is_active FROM doctor_list LEFT JOIN users ON doctor_list.user_id=users.id WHERE doctor_list.user_id='$listing_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $reg_date = $row['reg_date'];
                $doctor_name = $row['doctor_name'];
                $latitude = $row['lat'];
                $longitude = $row['lng'];
                $email = $row['email'];
                $qualification = $row['qualification'];
                $experience = $row['experience'];
                $gender = $row['gender'];
                $dob = $row['dob'];
                $reg_number = $row['reg_number'];
                $reg_council = $row['reg_council'];
                $phone = $row['phone'];
                $is_approval = $row['is_approval'];
                $is_active = $row['is_active'];
                $profile_pic = $row['image'];
                if ($row['image'] != '') {
                    $profile_pic = $row['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }
                $resultpost = array(
                    "reg_date" => $reg_date,
                    "doctor_name" => $doctor_name,
                    "profile_pic" => $profile_pic,
                    "latitude" => $latitude,
                    "longitude" => $longitude,
                    "email" => $email,
                    "qualification" => $qualification,
                    "experience" => $experience,
                    "gender" => $gender,
                    "dob" => $dob,
                    "reg_number" => $reg_number,
                    "reg_council" => $reg_council,
                    "is_approval" => $is_approval,
                    "is_active" => $is_active,
                    "phone" => $phone
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function doctor_documents_upload($listing_id, $medical_registration_pic, $medical_degree_pic, $government_id_pic, $prescription_pad_pic, $business_card_pic) {
        $query = $this->db->query("UPDATE doctor_list SET `medical_registration_pic`='$medical_registration_pic',`medical_degree_pic`='$medical_degree_pic',`government_id_pic`='$government_id_pic',`prescription_pad_pic`='$prescription_pad_pic',`business_card_pic`='$business_card_pic'WHERE user_id='$listing_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function doctor_clinic_list($user_id) {
        // $querys = $this->db->query("SELECT dc.appointment_time,dc.consultation_charges,dc.id,dc.doctor_id,dc.image,dc.id as hospital_id,dc.clinic_name,dc.contact_no,dc.open_hours,dc.address,dc.state,dc.city,dc.pincode,dc.map_location,dc.lat,dc.lng ,vendor_discount.* FROM doctor_clinic as dc LEFT JOIN vendor_discount ON(dc.doctor_id = vendor_discount.vendor_id AND vendor_discount.discount_category = 'doctor_visit') LEFT JOIN doctor_list as dl ON(dl.user_id=dc.doctor_id) LEFT JOIN users as u ON(dl.user_id=u.id) WHERE dc.doctor_id='" . $user_id . "' ORDER BY dc.id desc");
        
        
      $querys = $this->db->query("SELECT * FROM `doctor_clinic` WHERE doctor_id = '$user_id' ORDER BY id desc ");
        
        

        $count = $querys->num_rows();
        // echo $count; die(); 
        if ($count > 0) {
            foreach ($querys->result_array() as $row) {
               
                $clinic_name = $row['clinic_name'];
                $address = $row['address'];
                $state = $row['state'];
                $city = $row['city'];
                $pincode = $row['pincode'];
                $map_location = $row['map_location'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $image = $row['image'];
                $consultation_charges = $row['consultation_charges'];
                $contact_no = $row['contact_no'];
                $appointment_time = $row['appointment_time'];
                $opening_hours = $row['open_hours'];
                
         $discountQuery = $this->db->query("SELECT * FROM `vendor_discount` WHERE vendor_id = '$user_id'");  
         $disCount = $discountQuery->num_rows();
      
            if ($disCount > 0) {
                foreach ($discountQuery->result_array() as $rowDis) {
                    // print_r($rowDis); die();
                    $discount_amount_min = $rowDis['discount_min'];
                    $discount_amount_max = $rowDis['discount_max'];
                    $discount_type = $rowDis['discount_type'];
                    $discount_limit = $rowDis['discount_limit'];
                    $discount_cat = $rowDis['discount_category'];
                    
                }
            } else {
                $discount_amount_min = 0;
                $discount_amount_max = 0;
                $discount_type = 0;
                $discount_limit = 0;
                $discount_cat = "null";
            }
                
                
                
                
                
                $open_days = '';
                $day_array_list = '';
                $day_list = '';
                $day_time_list = '';
                $time_list1 = '';
                $time_list2 = '';
                $time = '';
                $system_start_time = '';
                $system_end_time = '';
                $time_check = '';
                $current_time = '';
                $open_close = array();
                $time = array();
                date_default_timezone_set('Asia/Kolkata');
                $data = array();
                
                
                
                
                
                $doctor_id = $row['doctor_id'];
                $clinic_id = $row['id'];
                // echo "doctor_id".$doctor_id."<br>";
                // echo "clinic_id".$clinic_id."<br>";
                $final_Day = array();
                $time_slots = array();
                
                $weekday ='Sunday';
                
                for($i=0;$i<7;$i++){
                    
                     $weekday = date('l', strtotime($weekday.'+1 day'));
                
                $queryTiming = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `day` = '$weekday'" );
               
                $countTiming = $queryTiming->num_rows();
                
                // die();
             
                $time_array = array();
                $time_array1 = array();
                $time_array2 = array();
                $time_array3 = array();
                $time_slott = array();
                
                foreach($queryTiming->result_array() as $row1){
                // if($countTiming){
                   
                    $timeSlotDay = $row1['day'];
                    $timeSlot = $row1['time_slot'];
                    $from_time = $row1['from_time'];
                    $to_time = $row1['to_time'];
                    // echo $timeSlot;
                   
                     if ($timeSlot == 'Morning') {
        				$time_array[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        				} else  if ($timeSlot == 'Afternoon') {
        				$time_array1[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        				} else if ($timeSlot == 'Evening') {
        				$time_array3[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        				} else if ($timeSlot == 'Night') {
        				$time_array2[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        			}
        			
        			
                }
                
                $time_slott[] = array(
                    'time_slot'=> 'Morning',
        			'time' => $time_array
        		);
        		$time_slott[] = array(
        		    'time_slot'=> 'Afternoon',
        			'time' => $time_array1
        		);
        		
        		$time_slott[] = array(
        		    'time_slot'=> 'Evening',
        			'time' => $time_array3
        		);
        		$time_slott[] = array(
        		    'time_slot'=> 'Night',
        			'time' => $time_array2
        		);
        		
        		$time_slots[] = array(
        		    'day'=>$weekday,
                   'slots'=> $time_slott
                ); 
                
                }
                // return $time_slots;
                
                // die();
                // $day_array_list = explode('|', $opening_hours);
                // if (count($day_array_list) > 1) {
                //     for ($i = 0; $i < count($day_array_list); $i++) {
                //         $day_list = explode('>', $day_array_list[$i]);
                //         for ($j = 0; $j < count($day_list); $j++) {
                //             $day_time_list = explode('-', $day_list[$j]);
                //             for ($k = 1; $k < count($day_time_list); $k++) {
                //                 $time_list1 = explode(',', $day_time_list[0]);
                //                 $time_list2 = explode(',', $day_time_list[1]);
                //                 $time = array();
                //                 $open_close = array();
                //                 for ($l = 0; $l < count($time_list1); $l++) {
                //                     $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                //                     $time[] = str_replace('close-close', 'close', $time_check);
                //                     $system_start_time = date("H.i", strtotime($time_list1[$l]));
                //                     $system_end_time = date("H.i", strtotime($time_list2[$l]));
                //                     $current_time = date('H.i');
                //                     if ($current_time > $system_start_time && $current_time < $system_end_time) {
                //                         $open_close[] = 'open';
                //                     } else {
                //                         $open_close[] = 'close';
                //                     }
                //                 }
                //             }
                //         }
                //         $final_Day[] = array(
                //             'day' => $day_list[0],
                //             'time' => $time,
                //             'status' => $open_close
                //         );
                //     }
                // } else {
                //     $final_Day[] = array(
                //         'day' => 'close',
                //         'time' => array(),
                //         'status' => array()
                //     );
                // }

                // die();
                if ($row['image'] != '') {
                    $profile_pic = $row['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://medicalwale.s3.amazonaws.com/images/doctor_images/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }
                
                
                // $resultpost[] = array(
                //     'id' => $row['id'],
                //     'clinic_name' => $row['clinic_name'],
                //     'address' => $row['address'],
                //     'state' => $row['state'],
                //     'city' => $row['city'],
                //     'pincode' => $row['pincode'],
                //     'map_location' => $row['map_location'],
                //     'lat' => $row['lat'],
                //     'lng' => $row['lng'],
                //     'image' => $profile_pic,
                //     'consultation_charges' => $row['consultation_charges'],
                //     'contact_no' => $row['contact_no'],
                //     'time_slot' => $appointment_time,
                //     'timings' => $time_slots,
                //      'discount_amount' => $row['discount_amount'],
                // 'discount_type' => $row['discount_type'],
                // 'discount_limit' => $row['discount_limit'],
                // 'discount_category' => $row['discount_category']
                    
                // );
                
                
                    $resultpostDetails['id'] = $row['id'];
                    $resultpostDetails['clinic_name'] = $row['clinic_name'];
                    $resultpostDetails['address'] = $row['address'];
                    $resultpostDetails['state'] = $row['state'];
                    $resultpostDetails['city'] = $row['city'];
                    $resultpostDetails['pincode'] = $row['pincode'];
                    $resultpostDetails['map_location'] = $row['map_location'];
                    $resultpostDetails['lat'] = $row['lat'];
                    $resultpostDetails['lng'] = $row['lng'];
                    $resultpostDetails['image'] = $profile_pic;
                    $resultpostDetails['consultation_charges'] = $row['consultation_charges'];
                    $resultpostDetails['contact_no'] = $row['contact_no'];
                    $resultpostDetails['time_slot'] = $appointment_time;
                    $resultpostDetails['timings'] = $time_slots;
                     $resultpostDetails['discount_amount_min'] = $discount_amount_min;
                     $resultpostDetails['discount_amount_max'] = $discount_amount_max;
                $resultpostDetails['discount_type'] =  $discount_type;
                $resultpostDetails['discount_limit'] = $discount_limit;
                $resultpostDetails['discount_category'] = $discount_cat;
               
                    
               
                 $resultpost[] = $resultpostDetails;
            }
            
            
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function list_patient($doctor_id,$search) 
    {
       	    if($search == ""){
                $search = "";
            } else {
                $search =  "AND patient_name LIKE '%$search%'";
            }	
            $query1 = $this->db->query("SELECT *  FROM `doctor_patient` WHERE `doctor_id`='$doctor_id' and status = 0 $search group by contact_no order by created_date desc ");
            $count1 = $query1->num_rows();
            if ($count1 > 0) 
                {
                   
                      foreach ($query1->result_array() as $row1) 
                      {
                        $patient_name   = $row1['patient_name'];
                        $email          = $row1['email'];
                        $gender         = $row1['gender'];
                        $date_of_birth  = $row1['date_of_birth'];
                        $blood_group    = $row1['blood_group'];
                        $address        = $row1['address'];
                        $city           = $row1['city'];
                        $state          = $row1['state'];
                        $pincode        = $row1['pincode'];
                        $medical        = $row1['medical_profile'];
                        $contact_no     = $row1['contact_no'];
                        $user_id        = $row1['user_id'];
                        $user_detail_access        = $row1['user_detail_access'];
                         if(empty($patient_name))
                        {
                            $patient_name="";
                        }
                        if(empty($email))
                        {
                            $email="";
                        }
                        if(empty($gender))
                        {
                            $gender="";
                        }
                        if(empty($date_of_birth))
                        {
                            $date_of_birth="";
                        }
                        if(empty($blood_group))
                        {
                            $blood_group="";
                        }
                        if(empty($address))
                        {
                            $address="";
                        }
                        if(empty($city))
                        {
                            $city="";
                        }
                        if(empty($state))
                        {
                            $state="";
                        }
                        if(empty($pincode))
                        {
                            $pincode="";
                        }
                         if(empty($medical))
                        {
                            $medical="";
                        }
                         if(empty($user_id))
                        {
                            $user_id="";
                        }
                        
                        if($user_detail_access=='1'){
                            
                            $editble='1';
                            
                        }else{
                            $editble='0';
                            
                        }
                        
                        
                        $query_visit = $this->db->query("SELECT *  FROM `doctor_booking_master` WHERE listing_id='$doctor_id' and `user_id`='$user_id'");
                        $visit_count = $query_visit->num_rows();
                        
                        $query_visit1 = $this->db->query("SELECT *  FROM `doctor_prescription` WHERE doctor_id='$doctor_id' and `patient_id`='$user_id'");
                        $visit_count1 = $query_visit1->num_rows();
                        
                        $resultpost[] = array(
                            "patient_id"=>$user_id,
                            "patient_name" => $patient_name,
                            "email"=>$email,
                            "gender"=>$gender,
                            "date_of_birth" => $date_of_birth,
                            "blood_group"=>$blood_group,
                            "address"=>$address,
                            "city" => $city,
                            "state"=>$state,
                            "pincode"=>$pincode,
                            "medical_profile"=>$medical,
                            "contact_no" => $contact_no,
                            "visit_count"=>$visit_count,
                            "prescription_count"=>$visit_count1,
                            "Editble"=>$editble
                            
                        );
                      }
                    
                }
            else 
              {
                $resultpost = array();
              }
        return $resultpost;
    }

    public function list_prescription($user_id,$patient_id,$page) 
    {
            $limit = 10;
            $start = 0;
            if ($page > 0) 
            {
                if (!is_numeric($page)) 
                {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            
            
            $query1 = $this->db->query("SELECT *  FROM `doctor_prescription` WHERE `doctor_id`='$user_id' and `patient_id`='$patient_id' order by id desc LIMIT $start, $limit");
            $count1 = $query1->num_rows();
            
            if ($count1 > 0) 
                {
                    $query2 = $this->db->query("SELECT *  FROM `doctor_prescription` WHERE `doctor_id`='$user_id' and `patient_id`='$patient_id' ");
                    $total_page_old = $query2->num_rows();
                    
                       $qRows = $query1->result_array();
                       foreach($qRows as $qRow)
                            {
                                     $prescription_id = $qRow['id'];
                                     $doctor_id  = $qRow['doctor_id'];
                                     $patient_id   = $qRow['patient_id'];
                                     $prescription_note = $qRow['prescription_note'];
                                     $prescription_date = $qRow['created_date'];
                                     $booking_id=$qRow['booking_id'];
                                      $resultpost[] = array(
                                            'p_id'=>$prescription_id,
                                            'prescription_id' => $this->get_url_version().$prescription_id,
                                            'doctor_id' => $doctor_id,
                                            'patient_id' => $patient_id,
                                            'prescription_note' => $prescription_note,
                                            'prescription_date' => $prescription_date,
                                            'prescription_pdf'=> $this->get_pdf_version().$booking_id.".pdf"
                                        );
                                     
                            }
                            $total_page_dec=$total_page_old/$limit;
                            $total_page_1=str_replace(".",".5",$total_page_dec);
                            $total_page=round($total_page_1);
                    
                }
            else 
              {
                $total_page=0;  
                $resultpost = array();
              }
        return array(
            'status' => 200,
            'message' => 'success',
            'total_page'=>(string)$total_page,
            'data'=>$resultpost
        );
        
        
        
    }
    
    public function edit_patient($patient_id, $data) {
        $result = $this->db->where('user_id', $patient_id)->update('doctor_patient', $data);
        
        return $result;
    }

    public function delete_patient($doctor_id, $patient_id) {
       /* $this->db->where('id', $patient_id);
        $delete_patient = $this->db->delete('doctor_patient');*/
        
        $this->db->where('user_id',$patient_id);
        $this->db->where('doctor_id',$doctor_id);
        $this->db->set('status',1);
        $this->db->update('doctor_patient');
        if($this->db->affected_rows()>0){
             return true; 
            
        }
        else
        {
             return false;
        }
        
        
    }

    public function partner_doctor_details($listing_id) {
        $query = $this->db->query("SELECT * FROM doctor_list WHERE user_id='$listing_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $doctor_name = $row['doctor_name'];
                $phone = $row['telephone'];
                $email = $row['email'];
                $experience = $row['experience'];
                $reg_number = $row['reg_number'];
                $reg_council = $row['reg_council'];
                $gender = $row['gender'];
                $dob = $row['dob'];
                $speciality = $row['speciality'];
                $service = $row['service'];
                $degree = $row['qualification'];
                $category = $row['category'];
                $followers = 0;
                $following = 0;
                $total_review = 0;
                $profile_pic = $row['image'];
                if ($row['image'] != '') {
                    $profile_pic = $row['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }
                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                $total_review = $this->db->select('id')->from('doctors_review')->where('doctor_id', $listing_id)->get()->num_rows();
                $area_expertise = array();
                $query_sp = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $category . "')");
                $total_category = $query_sp->num_rows();
                if ($total_category > 0) {
                    foreach ($query_sp->result_array() as $get_sp) {
                        $id = $get_sp['id'];
                        $area_expertised = $get_sp['area_expertise'];
                        $area_expertise[] = array(
                            'id' => $id,
                            'category' => $area_expertised
                        );
                    }
                } else {
                    $area_expertise = array();
                }
                $speciality_array = array();
                $speciality_ = explode(',', $speciality);
                foreach ($speciality_ as $speciality_) {
                    $speciality_array[] = array(
                        'category' => $speciality_
                    );
                }
                $service_array = array();
                $service_ = explode(',', $service);
                foreach ($service_ as $service_) {
                    $service_array[] = array(
                        'category' => $service_
                    );
                }
                $degree_array = array();
                $degree_ = explode(',', $degree);
                foreach ($degree_ as $degree_) {
                    $degree_array[] = array(
                        'category' => $degree_
                    );
                }
                $resultpost = array(
                    "doctor_name" => $doctor_name,
                    "phone" => $phone,
                    "email" => $email,
                    "profile_pic" => $profile_pic,
                    "area_expertise" => $area_expertise,
                    "speciality" => $speciality_array,
                    "service" => $service_array,
                    "degree" => $degree_array,
                    "experience" => $experience,
                    "reg_council" => $reg_council,
                    "reg_number" => $reg_number,
                    "gender" => $gender,
                    "dob" => $dob,
                    "followers" => $followers,
                    "following" => $following,
                    "total_review" => $total_review
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function doctor_add_clinic($data) {
        $this->db->insert('doctor_clinic', $data);
        $clinic_id = $this->db->insert_id();
        return $clinic_id;
    }
    
    public function doctor_add_discount_clinic($discount_data) {
        $this->db->insert('vendor_discount', $discount_data);
        //$clinic_id = $this->db->insert_id();
        return 1;
    }
    //start of me
    public function doctor_edit_discount_clinic($discount_data) {
        $this->db->replace('vendor_discount', $discount_data);
        //$clinic_id = $this->db->insert_id();
        return 1;
    }
    //end of me
    public function doctor_edit_discount_clinic2($doctor_id, $consultation_type, $discount_data) {
        
        $q = $this->db->select('id')->from('doctor_consultation')->where('doctor_user_id', $doctor_id)->where('consultation_name', $consultation_type)->get()->row();
        if (!empty($q)) {
            $where =   "doctor_user_id='$doctor_id' AND consultation_name='$consultation_type'";
            $this->db->where($where)->update('doctor_consultation', $discount_data);
            
            return 1;
            
        }else{
            $query = $this->db->insert('doctor_consultation', $discount_data);
            if($query){
               return 1; 
            }
        }
    }
    
     public function update_consultation_amount($data,$id){
	    $this->db->where('user_id',$id);
		$result = $this->db->update('doctor_list',$data);
		
        return $result;
	}
    
    
    public function doctor_edit_discount($discount_data,$doctor_id,$consultation_type) 
    {
        //   $query = $this->db->query("UPDATE vendor_discount SET image='$profile_pic_file' WHERE user_id='$listing_id'");
        $where =   "vendor_id='$doctor_id' AND 	discount_category='$consultation_type'";
          $this->db->where($where)->update('vendor_discount', $discount_data);
       
        //$clinic_id = $this->db->insert_id();
        return 1;
    }
    
    public function doctor_add_clinic_timing($data_time) {
       
        $this->db->insert('doctor_clinic_timing', $data_time);
        //  print_r($data_time);die();
        $timing_id = $this->db->insert_id();
        return $timing_id;
    }

    public function doctor_slot_details($data_time) {
       
        $this->db->insert('doctor_slot_details', $data_time);
        //  print_r($data_time);die();
        // $timing_id = $this->db->insert_id();
        return 1;
    }

    public function doctor_edit_clinic($clinic_id, $data) {
       
        $q = $this->db->select('id')->from('doctor_clinic')->where('id', $clinic_id)->get()->row();
        // print_r($q);
        if (!empty($q)) {
            $this->db->where('id', $clinic_id)->update('doctor_clinic', $data);
            return array(
                'status' => 200,
                'result' => 1
                );
        }
    }
    
    // doctor_delete_slot_details
    public function doctor_delete_slot_details($doctor_id, $clinic_id, $consultation_type) {
         
          $allRows = $this->db->delete('doctor_slot_details', array('clinic_id' => $clinic_id, 'doctor_id'=> $doctor_id, 'consultation_type'=>$consultation_type));
         
       
        // $this->db->insert('doctor_slot_details', $data_time_slots);
        //  print_r($data_time);die();
        // $timing_id = $this->db->insert_id();
        return 1;
    }
    
    public function clinic_delete($clinic_id) {
        $this->db->where('id', $clinic_id);
        $delete_clinic = $this->db->delete('doctor_clinic');
        
        $this->db->where('clinic_id', $clinic_id);
        $delete_clinic_doctor_patient = $this->db->delete('doctor_patient');
        
        $this->db->where('clinic_id', $clinic_id);
        $delete_clinic_doctor_patient = $this->db->delete('doctor_prescription');
        
        $this->db->where('clinic_id', $clinic_id);
        $delete_clinic_doctor_patient = $this->db->delete('doctor_slot_details');
        
        
        
        return $delete_clinic;
    }
     public function doctor_edit_slot_details($doctor_id, $clinic_id, $data_time_slots) {
         
       
        $this->db->insert('doctor_slot_details', $data_time_slots);
        //  print_r($data_time);die();
        // $timing_id = $this->db->insert_id();
        return 1;
    }
    
     public function doctor_edit_slot_details2($doctor_id, $data_time_slots) {
         
       
        $this->db->insert('doctor_slot_details', $data_time_slots);
        //  print_r($data_time);die();
        // $timing_id = $this->db->insert_id();
        return 1;
    }
    
    // public function doctor_edit_clinic_timing($clinic_id, $data) {
    //     $q = $this->db->select('id')->from('doctor_clinic')->where('id', $clinic_id)->get()->row();
    //     if (!empty($q)) {
    //         $this->db->where('id', $clinic_id)->update('doctor_clinic_timing', $data);
    //         return '1';
    //     }
    // }
     public function doctor_edit_clinic_timing($data_time, $timing_id) {
        // $q = $this->db->select('id')->from('doctor_clinic_timing')->where('id', $data_time['timing_id'])->get()->row();
        // print_r($data_time['timing_id']);
        if ($timing_id != "0" ) {
            $this->db->where('id', $timing_id)->update('doctor_clinic_timing', $data_time); 
             $new_timing_id = $timing_id;
        } else {
            $this->db->insert('doctor_clinic_timing', $data_time);     
             $new_timing_id = $this->db->insert_id();
        }
        // $new_timing_id = $this->db->insert_id();
        return $new_timing_id;
    }
    
    public function doctor_delete_clinic_timing($clinic_id, $doctor_id, $consultation_type) {
       
        $allRows = $this->db->delete('doctor_clinic_timing', array('clinic_id' => $clinic_id, 'doctor_id'=> $doctor_id,'consultation_type'=>$consultation_type));
          
        return $allRows;
    }
    
    
    
    
    

    public function doctor_delete_clinic($clinic_id) {
        $this->db->where('id', $clinic_id);
        $delete_clinic = $this->db->delete('doctor_clinic');
        return $delete_clinic;
    }

    public function partner_doctor_update($listing_id, $doctor_name, $email, $experience, $reg_council, $reg_number, $gender, $dob, $area_expertise, $speciality, $service, $degree) {
        $query = $this->db->query("UPDATE doctor_list INNER JOIN users ON doctor_list.user_id=users.id
		SET
		users.name = '$doctor_name',
		doctor_list.doctor_name = '$doctor_name',
		doctor_list.email = '$email',
		doctor_list.experience = '$experience',
		doctor_list.reg_council = '$reg_council',
		doctor_list.reg_number = '$reg_number',
		doctor_list.gender = '$gender',
		doctor_list.dob = '$dob',
		doctor_list.category = '$area_expertise',
		doctor_list.speciality = '$speciality',
		doctor_list.service = '$service',
		doctor_list.qualification = '$degree'
		WHERE doctor_list.user_id ='$listing_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function add_doctor_medicine($doctor_id, $patient_id, $medicine_array) {
        $this->db->insert('doctor_medicine', $medicine_array);
        //$medicine_id = $this->db->insert_id();
        $patient_medicines = $this->db->select('id,doctor_id, patient_id, medicine_name, dose, dose_unit, frequency, hours, duration,duration_circle, dose_time, instruction')->from('doctor_medicine')->where('doctor_id', $doctor_id)->where('patient_id', $patient_id)->get()->result();
        return array(
            'status' => 200,
            'message' => 'success',
            'medicine_details' => $patient_medicines,
                //'medicine_id' => $medicine_id
        );
    }

    public function edit_medicine_details($medicine_id) {
        return $this->db->select('id,doctor_id, patient_id, medicine_name, dose, dose_unit, frequency, hours, duration,duration_circle, dose_time, instruction')->from('doctor_medicine')->where('id', $medicine_id)->get()->row();
    }

    public function delete_medicine_details($medicine_id) {
        $delete = $this->db->query("DELETE FROM `doctor_medicine` WHERE id='$medicine_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function update_doctor_medicine($medicine_id, $medicine_name, $dose, $dose_unit, $frequency, $hours, $duration, $duration_circle, $dose_time, $instruction) {
        $query = $this->db->query("UPDATE doctor_medicine SET medicine_name='$medicine_name',dose='$dose',dose_unit='$dose_unit',frequency='$frequency',hours='$hours',duration='$duration',duration_circle='$duration_circle',dose_time='$dose_time',instruction='$instruction' WHERE id='$medicine_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function patient_all_medicine($doctor_id, $patient_id) {
        $patient_medicines = $this->db->select('id,doctor_id, patient_id, medicine_name, dose, dose_unit, frequency,medicine_hours, duration,duration_circle, dose_time, instruction')->from('doctor_medicine')->where('doctor_id', $doctor_id)->where('patient_id', $patient_id)->get()->result();
        return array(
            'status' => 200,
            'message' => 'success',
            'medicine_details' => $patient_medicines
        );
    }

    public function add_doctor_prescription($data) {
        $this->db->insert('doctor_prescription', $data);
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function prescription_list($doctor_id) {
       $query = $this->db->query("SELECT * FROM `doctor_prescription` where doctor_id='$doctor_id' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                
                $prescription_id    = $row['id'];
                $clinic_id    = $row['clinic_id'];
                $patient_id    = $row['patient_id'];
                $prescription_note    = $row['prescription_note'];
                $created_date    = $row['created_date'];
                $order_date   = date('j M Y h:i A', strtotime($created_date));
                
               
                
                $user_info    = $this->db->query("SELECT * FROM `doctor_patient` WHERE id='$patient_id'");
                $getuser_info = $user_info->row_array();
                
                $patient_name    = $getuser_info['patient_name'];
                $contact_no  = $getuser_info['contact_no'];
                $gender  = $getuser_info['gender'];
                $address  = $getuser_info['address'];
                $state  = $getuser_info['state'];
                $pincode  = $getuser_info['pincode'];
                $city  = $getuser_info['city'];
                $email  = $getuser_info['email'];
                $medical_profile  = $getuser_info['medical_profile'];
                
                
                
                $clinic_info    = $this->db->query("SELECT * FROM `doctor_clinic` WHERE doctor_id='$doctor_id'");
                $getclinic_info = $clinic_info->row_array();
                $clinic_name    = $getclinic_info['clinic_name'];
                $address  = $getclinic_info['address'];
                
                $prescription_query = $this->db->query("SELECT * FROM `doctor_prescription_medicine` where prescription_id='$prescription_id' order by id asc");
                $prescription_count = $prescription_query->num_rows();
                $medicine_array = array();
                if ($prescription_count > 0) {
                    foreach ($prescription_query->result_array() as $prescription_row) {
                        $medicine_id = $prescription_row['id'];
                        $medicine_name = $prescription_row['medicine_name'];
                        $dosage = $prescription_row['dosage'];
                        $dosage_unit = $prescription_row['dosage_unit'];
                        $frequency_first = $prescription_row['frequency_first'];
                        $frequency_second = $prescription_row['frequency_second'];
                        $frequency_third = $prescription_row['frequency_third'];
                        $instruction = $prescription_row['instruction'];
                        
                        $medicine_array[] = array(
                            "medicine_id" => $medicine_id,
                            "medicine_name" => $medicine_name,
                            "dosage" => $dosage,
                            "dosage_unit" => $dosage_unit,
                            "frequency_first" => $frequency_first,
                            "frequency_second" => $frequency_second,
                            "frequency_third" => $frequency_third,
                            "instruction" => $instruction
                        );
                    }
                }
                
                $test_query = $this->db->query("SELECT * FROM `doctor_prescription_test` where prescription_id='$prescription_id' order by id asc");
                $test_count = $test_query->num_rows();
                 $test_array = array();
                if ($test_count > 0) {
                    foreach ($test_query->result_array() as $test_row) {
                        $test_id = $test_row['id'];
                        $category = $test_row['category'];
                        $test = $test_row['test'];
                        
                        $test_array[] = array(
                            "test_id" => $test_id,
                            "category" => $category,
                            "test" => $test
                        );
                    }
                }
                
                // $prescription[] = array(
                //   "prescription_id" => $prescription_id,
                //   "clinic_id" => $clinic_id,
                //   "patient_id" => $patient_id,
                //   "prescription_note" => $prescription_note,
                //   "order_date" => $order_date,
                //   "patient_name" => $patient_name,
                //   "contact_no" => $contact_no,
                //   "gender" => $gender,
                //   "address" => $address,
                //   "city" => $city,
                //   "state" => $state,
                //   "pincode" => $pincode,
                //   "medical_profile" => $medical_profile,
                //   "email" => $email,
                //   "clinic_name" => $clinic_name,
                //   "medicine_array" => $medicine_array,
                //   "test_array" => $test_array
                // );
                
                $prescriptioneach['prescription_id'] = $prescription_id;
                $prescriptioneach['clinic_id'] = $clinic_id;
                $prescriptioneach['patient_id'] = $patient_id;
                $prescriptioneach['prescription_note'] = $prescription_note;
                $prescriptioneach['order_date'] = $order_date;
                $prescriptioneach['patient_name'] = $patient_name;
                $prescriptioneach['contact_no'] = $contact_no;
                $prescriptioneach['gender'] = $gender;
                $prescriptioneach['address'] = $address;
                $prescriptioneach['city'] = $city;
                $prescriptioneach['state'] = $state;
                $prescriptioneach['pincode'] = $pincode;
                $prescriptioneach['medical_profile'] = $medical_profile;
                $prescriptioneach['email'] = $email;
                $prescriptioneach['clinic_name'] = $clinic_name;
                $prescriptioneach['medicine_array'] = $medicine_array;
                $prescriptioneach['test_array'] = $test_array;
                
                $prescription[]= $prescriptioneach;
                 
            }
           
            return $prescription;
        }
        else{
            $prescription=array();
            return $prescription;
        }
    }

    public function doctors_prescription_list_edit($prescription_id) {
        $query = $this->db->query("SELECT * from doctor_prescription where doctor_prescription.id = '$prescription_id'");
        $results = $query->row();
        $medicines = $results->medicine_id;
        $id = $results->id;
        $patient_id = $results->patient_id;
        $clinic_id = $results->clinic_id;
        $prescription = $results->prescription;
        $doctor_id = $results->doctor_id;
        $created_date = $results->created_date;
        $query1 = $this->db->query("SELECT * from doctor_medicine where id IN ($medicines) ");
        $results = $query1->result();
        for ($i = 0; $i < count($results); $i++) {
            $mediciness[] = array(
                "id" => $results[$i]->id,
                "doctor_id" => $results[$i]->doctor_id,
                "patient_id" => $results[$i]->patient_id,
                "medicine_name" => $results[$i]->medicine_name,
                "dose" => $results[$i]->dose,
                "dose_unit" => $results[$i]->dose_unit,
                "frequency" => $results[$i]->frequency,
                "hours" => $results[$i]->hours,
                "duration" => $results[$i]->duration,
                "duration_circle" => $results[$i]->duration_circle,
                "dose_time" => $results[$i]->dose_time,
                "instruction" => $results[$i]->instruction
            );
        }
        $query1 = $this->db->query("SELECT doctor_prescription.id,doctor_list.doctor_name, doctor_patient.patient_name, doctor_clinic.clinic_name FROM `doctor_prescription` LEFT JOIN doctor_patient on (doctor_patient.id = doctor_prescription.patient_id) LEFT JOIN  doctor_clinic ON (doctor_clinic.id = doctor_prescription.clinic_id) LEFT JOIN doctor_list ON (doctor_list.id = doctor_prescription.doctor_id) WHERE doctor_prescription.id = '$prescription_id'");
        $join_result = $query1->row();
        $resultpost[] = array(
            "id" => $id,
            "patient_id" => $patient_id,
            "clinic_id" => $clinic_id,
            "prescription" => $prescription,
            "doctor_id" => $doctor_id,
            "created_date" => $created_date,
            "medicines" => $mediciness,
            "doctor_name" => $join_result->doctor_name,
            "clinic_name" => $join_result->clinic_name,
            "patient_name" => $join_result->patient_name
        );
        if (count($resultpost) > 0) {
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $resultpost
            );
        } else {
            $resultpost = array();
            return $resultpost;
        }
    }

    public function delete_prescription($prescription_id) {
      /*  
        $this->db->where('doctor_prescription.id=doctor_prescription_test.prescription_id');
       $this->db->where('doctor_prescription.id=doctor_prescription_medicine.prescription_id');
      $this->db->where('doctor_prescription.id',$prescription_id);
      $this->db->delete(array('doctor_prescription','doctor_prescription_test','doctor_prescription_medicine'));*/
      
      $sql = "DELETE dp,dpm,dpt FROM doctor_prescription as dp LEFT JOIN `doctor_prescription_medicine` as dpm ON(dpm.prescription_id=dp.id) LEFT JOIN doctor_prescription_test as dpt ON(dpt.prescription_id=dp.id) WHERE dp.id='$prescription_id'";
      $this->db->query($sql);
    
      
    //   $this->db->where('pemohon.id_pemohon=user.id_user');
    //   $this->db->where('pemohon.id_pemohon=peserta.id_peserta');
    //   $this->db->where('pemohon.id_pemohon',$id);
    //   $this->db->delete(array('doctor_prescription','doctor_prescription_test','doctor_prescription_medicine'));
        
    //     $query = $this->db->query("SELECT * from doctor_prescription where doctor_prescription.id = '$prescription_id'");
    //     $results = $query->row();
    //     $doctor_prescription_test = $results->prescription_id;
    //     $doctor_prescription_medicine = $results->prescription_id;
    //     $delete = $this->db->query("DELETE FROM `doctor_prescription` WHERE id IN ($prescription_id)");
    //     $delete_prescription = $this->db->query("DELETE FROM `doctor_prescription_test` WHERE id='$doctor_prescription_test'");
    //   $delete_prescription2 = $this->db->query("DELETE FROM `doctor_prescription_medicine` WHERE id='$doctor_prescription_medicine'");

        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function update_doctor_prescription($medicine_id, $prescription_id, $patient_id, $clinic_id, $doctor_id, $prescription, $updated_at) {
        $query = $this->db->query("UPDATE doctor_prescription SET patient_id='$patient_id',clinic_id='$clinic_id',medicine_id='$medicine_id',prescription='$prescription',created_date='$updated_at' where doctor_prescription.id='$prescription_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    /*public function doctors_appointment_list($doctor_id) {
        $query = $this->db->query("select doctor_patient.id,doctor_patient.patient_name,doctor_patient.contact_no, doctor_clinic.clinic_name,doctor_clinic.image,doctor_clinic.consultation_charges,doctor_clinic.address,doctor_booking_master.booking_date,doctor_booking_master.booking_time  from doctor_patient INNER JOIN doctor_clinic ON(doctor_patient.doctor_id = doctor_clinic.doctor_id ) LEFT JOIN doctor_booking_master ON(doctor_booking_master.clinic_id = doctor_clinic.id) where doctor_clinic.doctor_id = '$doctor_id' ");
        foreach ($query->result_array() as $row) {
            $clinic_name = $row['clinic_name'];
            $clinic_image = "https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/" . $row['image'];
            $consultation_charges = $row['consultation_charges'];
            $patient_name = $row['patient_name'];
            $contact_no = $row['contact_no'];
            $address = $row['address'];
            $booking_date = $row['booking_date'];
            $booking_time = $row['booking_time'];
            if ($booking_date == null || $booking_time == null) {
                $booking_date = $booking_date . $booking_date;
            }
            $resultpost[] = array(
                'clinic_name' => $clinic_name,
                'clinic_image' => $clinic_image,
                'consultation_charges' => $consultation_charges,
                'patient_name' => $patient_name,
                'contact_no' => $contact_no,
                'address' => $address,
                'booking_date' => $booking_date,
            );
        }
        if (!empty($resultpost)) {
            return $resultpost;
        } else {
            $resultpost = array();
            return $resultpost;
        }
    }*/
    
    
    /*
    New Service for Doctor Appointment*/
    public function doctors_appointment_list($doctor_id) {
        $status='pending';
        $query = $this->db->query("select doctor_booking_master.booking_date, doctor_booking_master.booking_time, doctor_booking_master.description,
                                   doctor_booking_master.listing_id, doctor_patient.patient_name, doctor_patient.date_of_birth, doctor_patient.type,
                                   doctor_booking_master.id
                                   from doctor_booking_master INNER JOIN doctor_patient ON(doctor_booking_master.id = doctor_patient.booking_id )
                                   where doctor_booking_master.listing_id = '$doctor_id' AND doctor_booking_master.status='$status'");
        foreach ($query->result_array() as $row) {
            $patient_name = $row['patient_name'];
            $date_of_birth = $row['date_of_birth'];
            $description = $row['description'];
            $doctor_id = $row['listing_id'];
            $type_of_consultation = $row['type'];
            $booking_id = $row['id'];
            $booking_date = $row['booking_date'];
            $booking_time = $row['booking_time'];
            
            $resultpost[] = array(
                'patient_name' => $patient_name,
                'date_of_birth' => $date_of_birth,
                'description' => $description,
                'listing_id' => $doctor_id,
                'type' => $type_of_consultation,
                'id' => $booking_id,
                'booking_date' => $booking_date,
                'booking_time' => $booking_time
            );
        }
        if (!empty($resultpost)) {
            return $resultpost;
        } else {
            $resultpost = array();
            return $resultpost;
        }
    }
    
    
    
    
    public function doctor_consultation_add_service($id,$doctor_id, $is_active, $consultation_name, $open_hours, $charges, $created_at) {
        $WHERE = array('doctor_id' => $doctor_id, 'consultation_name' => $consultation_name);
        $success = '';
        $count = $this->db->select('id')->from('doctor_consultation')->where($WHERE)->get()->num_rows();
        if ($consultation_name == "phone") {
            if ($count >= 1) {
                $success = $this->db->query("UPDATE doctor_consultation SET consultation_name='$consultation_name',open_hours='$open_hours',is_active='$is_active',charges='$charges',created_at='$created_at' WHERE id = '$id' ");
            } else {
                $this->db->query("INSERT INTO doctor_consultation(doctor_id,is_active, consultation_name, open_hours, charges, created_at)
                VALUES ('$doctor_id', '$is_active', '$consultation_name', '$open_hours', '$charges', '$created_at')");
            }
        }
        if ($consultation_name == "chat") {
            if ($count >= 1) {
                $success = $this->db->query("UPDATE doctor_consultation SET consultation_name='$consultation_name',open_hours='$open_hours',is_active='$is_active',charges='$charges',created_at='$created_at' WHERE id = '$id' ");
            } else {
                $this->db->query("INSERT INTO doctor_consultation(doctor_id,is_active, consultation_name, open_hours, charges, created_at)
                VALUES ('$doctor_id', '$is_active', '$consultation_name', '$open_hours', '$charges', '$created_at')");
            }
        }
        if ($consultation_name == "video") {
            if ($count >= 1) {
                $success = $this->db->query("UPDATE doctor_consultation SET consultation_name='$consultation_name',open_hours='$open_hours',is_active='$is_active',charges='$charges',created_at='$created_at' WHERE id = '$id' ");
            } else {
                $this->db->query("INSERT INTO doctor_consultation(doctor_id,is_active, consultation_name, open_hours, charges, created_at)
                VALUES ('$doctor_id', '$is_active', '$consultation_name', '$open_hours', '$charges', '$created_at')");
            }
        }
        if ($success) {
            return "OK";
        } else {
            return "";
        }
    }

    public function doctor_consultation_list_service($doctor_id, $consultation_name) {
        $limit = 1;
        $success = $this->db->select('*')->from('doctor_consultation')->where('doctor_user_id', $doctor_id)->where('consultation_name', $consultation_name)->limit($limit)->get()->row();
        if ($success) {
            $is_active = $success->is_active;
            $id = $success->id;
            $doctor_id = $success->doctor_id;
            $consultation_name = $success->consultation_name;
            $charges = $success->charges;
            $created_at = $success->created_at;
            $opening_hours = $success->open_hours;
            $day_array_list = '';
            $day_list = '';
            $day_time_list = '';
            $time_list1 = '';
            $time_list2 = '';
            $time = '';
            $system_start_time = '';
            $system_end_time = '';
            $time_check = '';
            $current_time = '';
            $open_close = array();
            $time = array();
            date_default_timezone_set('Asia/Kolkata');
            $data = array();
            $final_Day = array();
            $day_array_list = explode('|', $opening_hours);
            if (count($day_array_list) > 1) {
                for ($i = 0; $i < count($day_array_list); $i++) {
                    $day_list = explode('>', $day_array_list[$i]);
                    for ($j = 0; $j < count($day_list); $j++) {
                        $day_time_list = explode('-', $day_list[$j]);
                        for ($k = 1; $k < count($day_time_list); $k++) {
                            $time_list1 = explode(',', $day_time_list[0]);
                            $time_list2 = explode(',', $day_time_list[1]);
                            $time = array();
                            $open_close = array();
                            for ($l = 0; $l < count($time_list1); $l++) {
                                $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                                $time[] = str_replace('close-close', 'close', $time_check);
                                $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                $system_end_time = date("H.i", strtotime($time_list2[$l]));
                                $current_time = date('H.i');
                                if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                    $open_close[] = 'open';
                                } else {
                                    $open_close[] = 'close';
                                }
                            }
                        }
                    }
                    $final_Day[] = array(
                        'day' => $day_list[0],
                        'time' => $time,
                        'status' => $open_close
                    );
                }
            } else {
                $final_Day[] = array(
                    'day' => 'close',
                    'time' => array(),
                    'status' => array()
                );
            }
            if (!empty($success)) {
                return array(
                    'id' => $id,
                    'doctor_id' => $doctor_id,
                    'consultation_name' => $consultation_name,
                    'open_hours' => $final_Day,
                    'is_active' => $is_active,
                    'charges' => $charges,
                    'created_at' => $created_at
                );
            } else {
                return array(
                    'id' => $id,
                    'doctor_id' => $doctor_id,
                    'consultation_name' => $consultation_name,
                    'open_hours' => $final_Day,
                    'is_active' => $is_active,
                    'charges' => $charges,
                    'created_at' => $created_at
                );
            }
        } else {
            $resultpost = (object) [] ;
            return $resultpost;
        }
    }
    
     public function medicine_search($doctor_id,$keyword,$page_no) {
	if($page_no == 0){
		 $query = $this->db->query("SELECT id,product_name  as keyword FROM product WHERE product_name  like '%$keyword%'");
	} else {
	  $per_page = 10;	
	  $offset = $per_page*($page_no - 1);
          $limit = "LIMIT $per_page OFFSET $offset";	
	  $query = $this->db->query("SELECT id,product_name  as keyword FROM product WHERE product_name  like '%$keyword%' $limit");	
	}
        
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $keyword = $row['keyword'];
                $resultpost[] = array(
                    "id" => $id,
                    "keyword" => $keyword
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
      public function clinic_search($doctor_id,$keyword) {
        $query = $this->db->query("SELECT id,clinic_name FROM doctor_clinic WHERE clinic_name like '%$keyword%' and doctor_id='$doctor_id'");
        $count = $query->num_rows();
        if ($count > 0) {
           
            foreach ($query->result_array() as $row) {
                $doctor_id = $row['id'];
                $keyword = $row['clinic_name'];
                $resp[] = array(
                    "id" => $doctor_id,
                    "keyword" => $keyword
                );
            }
        } else {
            $resp = array();
        }
        return $resp;
    }
    
     public function all_test_search($test_id,$keyword) {
        $query = $this->db->query("SELECT id,test_id,sub_test_name FROM doctor_subtest WHERE sub_test_name like '%$keyword%' AND test_id ='$test_id'");
        $count = $query->num_rows();
        if ($count > 0) {
           
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $keyword = $row['sub_test_name'];
                $resp[] = array(
                    "id" => $id,
                    "keyword" => $keyword
                );
            }
        } else {
            $resp = array();
        }
        return $resp;
    }
    
    /*
    Doctor Approval Status
    1=Confirm 
    2=Reschedule
    2 task in this method
    1.Doctor Appointment Status Change
    2.Notification trigger
    
    
    1 = booked by user / awaiting confirmation from doctor
    2 = doctor confirm ( payment pending from user side)
    3 = doctor cancel (doctor cancel or user canceled)
    4 = rescheduled by doctor (awaiting confirmation from user)
    5 = confirm (after payment done meeting is schedule for perticular date ) 
    6 = awaiting feedback 
    7 = completed (all process done completed all meetings)
    */
    public function doctor_appointment_approval($doctor_id, $status, $booking_id)
    {
       date_default_timezone_set('Asia/Kolkata');
       $date = date('Y-m-d H:i:s');
       //$status = strtolower($status);
       $status = $status;  // 2 - if doctor confirm timing ,4 doctor cancelled timing ,6 reschedule 

       //echo "SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'";
       $table_record = $this->db->query("SELECT user_id,booking_id,listing_id,consultation_type,status FROM doctor_booking_master WHERE booking_id='$booking_id' ");
       $count_user = $table_record->num_rows();
       if($count_user>0)
       {
           if($status=="3")
           {
               $row = $table_record->row();
               $status1 = $row->status;
              if($status1=="5")
              {
                  $status="9";
              }
              elseif($status1=="8")
              {
                  $status="10";
              }
              
            
           }
           $booking_array = array(
                           'status' => $status,
                           'created_date' => $date
                       );
           $updateStatus=$this->db->where('booking_id', $booking_id)->update('doctor_booking_master', $booking_array);
                        if(!$updateStatus)
                         {
                            return array(
                                 'status' => 204,
                                 'message' => 'Update failed'
                            );
                         } 
            if($status == '2') //doctor confirm, payment pending
            {
                $row = $table_record->row();
                $user_id = $row->user_id;
                $doctor_id = $row->listing_id;
                 $consultation_type = $row->consultation_type;
        $this->confirm_status($user_id, $booking_id, $doctor_id,$consultation_type);
            }
            else if($status == '4')  //reschedule and awaiting confirmation from user sid
            {
                $row = $table_record->row();
                $user_id = $row->user_id;
                $doctor_id = $row->listing_id;
              $consultation_type = $row->consultation_type;
                $this->reschedule_status($user_id,$booking_id,$doctor_id,$consultation_type);
            }
            else if ($status == '3' || $status == '9' || $status == '10' )  //cancel appointment 
            {
                $row = $table_record->row();
                $user_id = $row->user_id;
                $doctor_id = $row->listing_id;
                $consultation_type = $row->consultation_type;
                $this->cancel_status($user_id,$booking_id,$doctor_id,$consultation_type);
            }
       }else{
           return array(
                     'status' => 208,
                      'message' => 'Booking data not found'
                   );
       }
        return array(
            'status' => 200,
            'message' => 'success'
        );

   }  
   
   
    public function doctor_approval($doctor_id, $status, $booking_id)
    {
       date_default_timezone_set('Asia/Kolkata');
       $date = date('Y-m-d H:i:s');
       $table_record = $this->db->query("SELECT user_id,booking_id,listing_id,consultation_type FROM doctor_booking_master WHERE booking_id='$booking_id' ");
       $count_user = $table_record->num_rows();
       if($count_user>0)
          {
           $final="";   
           if($status=="9")
           {
             $final='5';  
           }
           elseif($status=="10")
           {
               $final='8';
           }
           else
           {
               $final=$status;
           }
           $booking_array = array(
                           'status' => $final,
                           'created_date' => $date
                       );
           $updateStatus=$this->db->where('booking_id', $booking_id)->update('doctor_booking_master', $booking_array);
                        if(!$updateStatus)
                         {
                            return array(
                                 'status' => 204,
                                 'message' => 'Update failed'
                            );
                         } 
            if($status == '9') //doctor confirm, payment pending
            {
                $row = $table_record->row();
                $user_id = $row->user_id;
                $doctor_id = $row->listing_id;
                 $consultation_type = $row->consultation_type;
        $this->confirm_status($user_id, $booking_id, $doctor_id,$consultation_type);
            }
             if ($status == '10')
            {
                                $row       = $table_record->row();
                                $user_id   = $row->user_id;
                                $doctor_id = $row->listing_id;
                                $this->confirm_cash_on_delivery_status($user_id, $booking_id, $doctor_id);
                            }
            else if ($status == '3')  //cancel appointment 
            {
                $row = $table_record->row();
                $user_id = $row->user_id;
                $doctor_id = $row->listing_id;
                $consultation_type = $row->consultation_type;
                $this->cancel_status($user_id,$booking_id,$doctor_id,$consultation_type);
            }
               if ($status == '6')
                   {
                        $row = $table_record->row();
                          $user_id = $row->user_id;
                         
                        $query2 = $this->db->query("select id  from doctor_online_block where user_id='$user_id' and status='0' and doctor_id='$doctor_id'");
                        $count2 = $query2->num_rows();
                        if ($count2 > 0) 
                        {
                          
                          $data = array('block' => '0');
                          $this->db->where('doctor_id', $doctor_id);
                          $insert = $this->db->update('doctor_online', $data);
                          
				
                          $booking_array = array('status' => 1);
                          $updateStatus  = $this->db->where('doctor_id', $doctor_id)->where('user_id', $user_id)->update('doctor_online_block', $booking_array);     
                         
                          $this->feedback_status($user_id, $booking_id, $doctor_id);    
                        }
                   } 
          }
        else{
           return array(
                     'status' => 208,
                      'message' => 'Booking data not found'
                   );
       }
        return array(
            'status' => 200,
            'message' => 'success'
        );

   } 
   
   /*
   Confirm Status is used to confirm the 
   status of the appointment.
   Doubt in query call doctor name can be called  from parent method using join
   which is better way.
   */
   
   
    public function feedback_status($user_id, $booking_id, $listing_id)
    {
        $appointment_status = '8';
        $table_record   = $this->db->query("SELECT name as patient_name FROM users WHERE id='$user_id'");
        
        $bookingRecords =  $this->db->query("SELECT `booking_date`,`from_time`,`to_time`,`consultation_type` FROM `doctor_booking_master` WHERE `booking_id`='$booking_id'");
        $count_record  = $bookingRecords->num_rows();
        if($count_record > 0){
            $bookingRecord = $bookingRecords->row();
            
            $booking_date = $bookingRecord->booking_date;
            $from_time = $bookingRecord->from_time;
            $to_time = $bookingRecord->to_time;
            $booking_time = $from_time.'-'.$to_time;
            $consultation_type = $bookingRecord->consultation_type;
        }
         $timestamp = strtotime($booking_date);
            $newdate=date("d-M-Y", $timestamp);
            
            $newtime=date("h:i A", strtotime($from_time));
        $count_user         = $table_record->num_rows();
        if ($count_user > 0) {
            $row         = $table_record->row();
            $patient_name = $row->patient_name;
           
            $title       = 'Appointment Feedback';
            $msg         = "Did you receive friendly, professional service today, $patient_name ? If so, please take a few seconds to leave a review.";
            
            //$this->notifyDoctorMethod($listing_id, $appointment_status, $booking_id, $patient_name, $title, $msg);
            $this->notifyuserMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name);
        }
    }
   
   
    public function notifyuserMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name)
    {

          $customer_token = $this->db->query("SELECT name,token,agent,token_status,web_token FROM users WHERE id='$user_id'");
          $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                // $getusr = $user_plike->row_array();

                $usr_name = $token_status['name'];
                $agent = $token_status['agent'];
                $reg_id = $token_status['token'];
               
                $web_token = $token_status['web_token'];
                $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/doctor.png';
                $tag = 'text';
                $key_count = '1';
                $title = $title;
                $msg = $msg;
                
                $listing_name="";
                $customer_token1 = $this->db->query("SELECT name,token,agent,token_status,web_token FROM users WHERE id='$listing_id'");
                $customer_token_count1 = $customer_token1->num_rows();
                if ($customer_token_count1 > 0) 
                   {
                     $token_status1 = $customer_token1->row_array();
                     $listing_name =$token_status1['name'];
                   }
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
                      'listing_name'=> $listing_name,
                      'booking_id'  => "",
                      'invoice_no' => "",
                      'user_id'  => $user_id,
                      'notification_type'  => 'feedback_to_doctor',
                      'notification_date'  => date('Y-m-d H:i:s')
                       
                    );
                    $this->db->insert('All_notification_Mobile', $notification_array);
                    //end
                    
                  
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
                "notification_type" => 'feedback_to_doctor',
                "notification_date" => $date,
                "DoctorUserId" => $listing_id,
                "DoctorName"=>$listing_name
                
            )
        );
       
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
           //$agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
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
   
   
   
   
   public function confirm_status($user_id, $booking_id, $listing_id,$consultation_type)
   {
       $appointment_status = '2';
       $table_record = $this->db->query("SELECT doctor_name FROM doctor_list WHERE user_id='$listing_id' ");
       $count_user = $table_record->num_rows();
       if($count_user>0)
       {
            $row = $table_record->row();
            $doctor_name = $row->doctor_name; 
            $title = $doctor_name . ' has confirmed an appointment';
            $msg = $doctor_name . '  has confirmed an appointment for'.$consultation_type;
            $this->notifyMethod($user_id, $appointment_status, $booking_id, $doctor_name,$title,$msg,$listing_id);
       }
      
   }
   
     /*
   Reschedule Status 
   Doctor has not confirmed the appointment.
   */
   public function reschedule_status($user_id, $booking_id, $listing_id,$consultation_type)
   {
       $appointment_status = '4';
       $table_record = $this->db->query("SELECT doctor_name FROM doctor_list WHERE user_id='$listing_id' ");
       $count_user = $table_record->num_rows();
       if($count_user>0)
       {
            $row = $table_record->row();
            $doctor_name = $row->doctor_name; 
             $title = $doctor_name . ' has Reschedule an appointment';
             $msg = $doctor_name . '  has Reschedule an appointment for '.$consultation_type;
            $this->notifyMethod($user_id, $appointment_status, $booking_id, $doctor_name,$title,$msg,$listing_id);
       }
      
   }
   
   
     /*
   Cancel Status 
   Doctor has canceled the appointment.
   */
   
   
  public function cancel_status($user_id, $booking_id, $listing_id)
   {
       $appointment_status = '3';
       $table_record = $this->db->query("SELECT doctor_name,consultation_type FROM doctor_list WHERE user_id='$listing_id' ");
       $count_user = $table_record->num_rows();
       if($count_user>0)
       {
           //echo "test2";
            $row = $table_record->row();
            $doctor_name = $row->doctor_name;
            $consultation_type=$row->consultation_type;
             $title = $doctor_name . ' has Cancel an appointment';
              $msg = $doctor_name . '  has Cancel an appointment for'.$consultation_type; 
            $this->notifyMethod($user_id, $appointment_status, $booking_id, $doctor_name,$title,$msg,$listing_id);
       }
      
   }
   
    /*
    This method is used for notification.
    Left doctor service.
    */
    public function notifyMethod($user_id, $appointment_status, $booking_id, $doctor_name,$title,$msg,$listing_id)
    {

          $customer_token = $this->db->query("SELECT name,token,agent,token_status FROM users WHERE id='$user_id'");
          $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                
                $token_status = $customer_token->row_array();
            //    $getusr = $user_plike->row_array();

                $usr_name = $token_status['name'];
                $agent = $token_status['agent'];
                $reg_id = $token_status['token'];
                $img_url = 'https://medicalwale.com/img/medical_logo.png';
                $tag = 'text';
                $key_count = '1';
               // $title = $doctor_name . ' has confirmed an appointment';
               // $msg = $doctor_name . '  has confirmed an appointment.\n';
                $title = $title;
                $msg = $msg;
                
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
                      'booking_id'  => "",
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'appointment_notifications',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
                    );
                    $this->db->insert('All_notification_Mobile', $notification_array);
                
                
                $this->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_id);
            }
    }


        //send notification through firebase
        /* notification to send in the doctor app for appointment confirmation*/
    public function send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_id) {
    
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
                "notification_type" => 'doctor_confirmation',
                "notification_date" => $date,
                "booking_id" => $booking_id
                
            )
        );
       
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
             $agent === 'android' ? 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
         //   $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
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
    
   
    
    
     /*  public function view_appointments_module($user_id)
    {
        $resultpost=array();
        // Doctor Panel
        $Appointment_dataList = $this->db->query("SELECT * FROM `doctor_booking_master` where `listing_id`= '$user_id' order by id desc");
        $count_appointment_slot = $Appointment_dataList->num_rows();
        if ($count_appointment_slot > 0) {
            foreach ($Appointment_dataList->result_array() as $row) 
          {
             $id =$row['id'];
            $booking_id = $row['booking_id'];
            $user_id  = $row['user_id'];
            $patient_id = $row['patient_id'];
            $listing_id = $row['listing_id'];
            $clinic_id  = $row['clinic_id'];
            $booking_date = $row['booking_date'];
            $consultation_type = $row['consultation_type'];
            $from_time = $row['from_time'];
            $to_time = $row['to_time'];
            $description = $row['description'];
            $status = $row['status'];
            $charge=$row['consultation_charges'];
            $query12 =   $this->db->query("SELECT `name` FROM `users` where `id`= '$user_id' ");
            $doctor = $query12->row_array();
            $user_name = $doctor['name'];
           
            if($clinic_id!=0)
             {
             $query12 =   $this->db->query("SELECT `clinic_name` FROM `doctor_clinic` where  `id` = '$clinic_id'");
             $count_query12 = $query12->num_rows();
             if($count_query12 > 0)
             {
            $clinic = $query12->row_array();
           
            $clinic_name = $clinic['clinic_name'];
             }
             else
             {
                 $query12 =   $this->db->query("SELECT `name_of_hospital` FROM `hospitals` where  `user_id` = '$clinic_id'");
            
            $clinic = $query12->row_array();
           $clinic_name = $clinic['name_of_hospital'];
             }
	    }
		    else
		    {
			 $clinic_name ="";    
		    }
            //echo "SELECT `id`,`relationship` FROM `health_record` where `user_id`= '$user_id' and `id`='$id'";
            
            $query12 =   $this->db->query("SELECT `id`,`relationship` FROM `health_record` where `relation_id`= '$patient_id'");
            $count_appointment_slot1 = $query12->num_rows();
            if($count_appointment_slot1 > 0 )
            {
            $Patient = $query12->row_array();
            $patient_relation = $Patient['relationship'];
            } 
          
           else
           {
                $patient_relation = "Myself";
           }
            if($patient_id=="0")
            {
                $patient_id=$user_id;
            }
           $is_user='1';
           
           $query12 =   $this->db->query("SELECT `id`,booking_id FROM `doctor_prescription` where booking_id='$booking_id'");
            $data = $query12->row_array();
            
            $pr_id = $data['id'];
            
            $url="";
                $pdf="";
            if($pr_id == null)
            { 
                $pr_id = "";
                $url="";
                $pdf="";
            }
            
            else
            {
                $url=$this->get_url_version().$pr_id;
                $pdf=$this->get_pdf_version().$booking_id.".pdf";                           
                                          
            }
          
            
            
            //$patient_relation = $Patient['relationship'];
          // echo $user_id;
          // die;
            if(!empty($user_id))
            {
           $query1 = $this->db->query("SELECT *  FROM `doctor_patient` WHERE `user_id`='$user_id'");
            $count1 = $query1->num_rows();
            if ($count1 > 0) 
                {
                   
                     $row1=$query1->row_array();  
                      
                        $patient_name   = $row1['patient_name'];
                        $email          = $row1['email'];
                        $gender         = $row1['gender'];
                        $date_of_birth  = $row1['date_of_birth'];
                        $blood_group    = $row1['blood_group'];
                        $address        = $row1['address'];
                        $city           = $row1['city'];
                        $state          = $row1['state'];
                        $pincode        = $row1['pincode'];
                        $medical        = $row1['medical_profile'];
                        $contact_no     = $row1['contact_no'];
                        $user_id        = $row1['user_id'];
                }
            }
                 else
                 {$patient_name="";
                       
                            $email="";
                       
                            $gender="";
                         $date_of_birth="";
                        
                            $blood_group="";
                       
                            $address="";
                        
                            $city="";
                        
                            $state="";
                        
                            $pincode="";
                        
                            $medical="";
                       
                            $user_id="";
                            $contact_no="";
           
                     
                 }
                           
                    if(empty($patient_name))
                        {
                            $patient_name="";
                        }
                        if(empty($email))
                        {
                            $email="";
                        }
                        if(empty($gender))
                        {
                            $gender="";
                        }
                        if(empty($date_of_birth))
                        {
                            $date_of_birth="";
                        }
                        if(empty($blood_group))
                        {
                            $blood_group="";
                        }
                        if(empty($address))
                        {
                            $address="";
                        }
                        if(empty($city))
                        {
                            $city="";
                        }
                        if(empty($state))
                        {
                            $state="";
                        }
                        if(empty($pincode))
                        {
                            $pincode="";
                        }
                         if(empty($medical))
                        {
                            $medical="";
                        }
                         if(empty($user_id))
                        {
                            $user_id="";
                        }
                         if(empty($contact_no))
                        {
                            $contact_no="";
                        }
                             
              
             $bcht="";
             $resultpost[] = array(
                 'patient_id' => $user_id,
                 'booking_id' => $booking_id,
                 //'user_name' => $user_name,
                 //'patient_id' => $patient_id,
                 'patient_relation'=> $patient_relation,
                 'is_user' => $is_user,
                 'prescription_id' => $url,
                 'prescription_pdf' => $pdf,
                // 'doctor_name' => $doctor_name,
                "patient_name" => $patient_name,
                "email"=>$email,
                "gender"=>$gender,
                "date_of_birth" => $date_of_birth,
                "blood_group"=>$blood_group,
                "address"=>$address,
                "city" => $city,
                "state"=>$state,
                "pincode"=>$pincode,
                "medical_profile"=>$medical,
                "contact_no" => $contact_no,
                 'clinic_id' => $clinic_id,
                 'clinic_name'=> $clinic_name,
                 'booking_date' => $booking_date,
                 'consultation_type' => $consultation_type,
                 'consultation_charge' => $charge,
                 'from_time' => $from_time,
                 'to_time' => $to_time,
                 'description' => $description,
                 'status' => $status,
                 //'bachatcard'=>$bcht
                 );
         }
         
          
    
        }
        
        // HBIOMS
        $doct=$this->db->query("SELECT * FROM `doctor_list` WHERE `user_id` = $user_id"); 
        $doct_id=$doct->row_array();
        $list=$doct_id['hospital_doctor_id'];
        if(!empty($list))
        {
         $blist=explode(",",$list);
         for($i=0;$i < count($blist); $i++)
         {
            $Appointment_dataList = $this->db->query("SELECT * FROM `hospital_booking_master` where `doctor_id`= '$blist[$i]' order by id desc");
            $count_appointment_slot = $Appointment_dataList->num_rows();
            if ($count_appointment_slot > 0) {
                foreach ($Appointment_dataList->result_array() as $row) 
              {
                 $id =$row['id'];
                $booking_id = $row['booking_id'];
                $user_id  = $row['user_id'];
                $patient_id = $row['patient_id'];
                $listing_id = $row['doctor_id'];
                $clinic_id  = $row['listing_id'];
                $booking_date = $row['booking_date'];
                $consultation_type = $row['consultation_type'];
                $from_time = $row['from_time'];
                $to_time = $row['to_time'];
                $description = $row['description'];
                $status = $row['status'];
                
                $query12 =   $this->db->query("SELECT `name` FROM `users` where `id`= '$user_id' ");
                $doctor = $query12->row_array();
                $user_name = $doctor['name'];
               
               
                 $query12 =   $this->db->query("SELECT `clinic_name` FROM `doctor_clinic` where  `id` = '$clinic_id'");
                 $count_query12 = $query12->num_rows();
                 if($count_query12 > 0)
                 {
                $clinic = $query12->row_array();
               
                $clinic_name = $clinic['clinic_name'];
                 }
                 else
                 {
                     $query12 =   $this->db->query("SELECT `name_of_hospital` FROM `hospitals` where  `user_id` = '$clinic_id'");
                
                $clinic = $query12->row_array();
               $clinic_name = $clinic['name_of_hospital'];
                 }
               
                
                //echo "SELECT `id`,`relationship` FROM `health_record` where `user_id`= '$user_id' and `id`='$id'";
                
                $query12 =   $this->db->query("SELECT `id`,`relationship` FROM `health_record` where `relation_id`= '$patient_id'");
                $count_appointment_slot1 = $query12->num_rows();
                if($count_appointment_slot1 > 0 )
                {
                $Patient = $query12->row_array();
                $patient_relation = $Patient['relationship'];
                } 
              
               else
               {
                    $patient_relation = "Myself";
               }
                if($patient_id=="0")
                {
                    $patient_id=$user_id;
                }
               $is_user='1';
               
                $query12 =   $this->db->query("SELECT `id` FROM `doctor_prescription` where booking_id='$booking_id'");
            $data = $query12->row_array();
            
            $pr_id = $data['id'];
            
            $url="";
                $pdf="";
            if($pr_id == null)
            { 
                $pr_id = "";
                $url="";
                $pdf="";
            }
            
            else
            {
                $url=$this->get_url_version().$pr_id;
                $pdf=$this->get_pdf_version().$booking_id.".pdf";                           
                                          
            }
              
                
                
                //$patient_relation = $Patient['relationship'];
              // echo $user_id;
              // die;
                if(!empty($user_id))
                {
               $query1 = $this->db->query("SELECT *  FROM `doctor_patient` WHERE `user_id`='$user_id'");
                $count1 = $query1->num_rows();
                if ($count1 > 0) 
                    {
                       
                         $row1=$query1->row_array();  
                          
                            $patient_name   = $row1['patient_name'];
                            $email          = $row1['email'];
                            $gender         = $row1['gender'];
                            $date_of_birth  = $row1['date_of_birth'];
                            $blood_group    = $row1['blood_group'];
                            $address        = $row1['address'];
                            $city           = $row1['city'];
                            $state          = $row1['state'];
                            $pincode        = $row1['pincode'];
                            $medical        = $row1['medical_profile'];
                            $contact_no     = $row1['contact_no'];
                            $user_id        = $row1['user_id'];
                    }
                }
                     else
                     {$patient_name="";
                           
                                $email="";
                           
                                $gender="";
                             $date_of_birth="";
                            
                                $blood_group="";
                           
                                $address="";
                            
                                $city="";
                            
                                $state="";
                            
                                $pincode="";
                            
                                $medical="";
                           
                                $user_id="";
                                $contact_no="";
               
                         
                     }
                               
                        if(empty($patient_name))
                            {
                                $patient_name="";
                            }
                            if(empty($email))
                            {
                                $email="";
                            }
                            if(empty($gender))
                            {
                                $gender="";
                            }
                            if(empty($date_of_birth))
                            {
                                $date_of_birth="";
                            }
                            if(empty($blood_group))
                            {
                                $blood_group="";
                            }
                            if(empty($address))
                            {
                                $address="";
                            }
                            if(empty($city))
                            {
                                $city="";
                            }
                            if(empty($state))
                            {
                                $state="";
                            }
                            if(empty($pincode))
                            {
                                $pincode="";
                            }
                             if(empty($medical))
                            {
                                $medical="";
                            }
                             if(empty($user_id))
                            {
                                $user_id="";
                            }
                             if(empty($contact_no))
                            {
                                $contact_no="";
                            }
                                 
                    $sql2             = "SELECT id, clinic_name,consultation_charges FROM doctor_clinic WHERE `doctor_id`= '$listing_id' AND `id` = '$clinic_id'";
                   $query_practices  = $this->db->query($sql2);
                   $total_practices  = $query_practices->num_rows();
                   if(count($total_practices) > 0)
                   {
                   $get_pract=$query_practices->row_array();
                   $charge=$get_pract['consultation_charges'];
                   }
                   else
                   {
                     $charge="0";  
                   }
                           if(empty($charge))
                           {
                              $charge="0"; 
                           }
                           
                  $sql2             = "SELECT id, clinic_name,consultation_charges FROM doctor_clinic WHERE `doctor_id`= '$listing_id' AND `id` = '$clinic_id'";
                   $query_practices  = $this->db->query($sql2);
                   $total_practices  = $query_practices->num_rows();
                   if(count($total_practices) > 0)
                   {
                   $get_pract=$query_practices->row_array();
                   $charge=$get_pract['consultation_charges'];
                   }
                   else
                   {
                     $charge="0";  
                   }           
                      if(empty($charge))
                      {
                          $charge="";
                      }
                 $bcht="";
                 $resultpost[] = array(
                     'patient_id' => $user_id,
                     'booking_id' => $booking_id,
                     //'user_name' => $user_name,
                     //'patient_id' => $patient_id,
                     'patient_relation'=> $patient_relation,
                     'is_user' => $is_user,
                     'prescription_id' => $url,
                     'prescription_pdf' => $pdf,
                    // 'doctor_name' => $doctor_name,
                    "patient_name" => $patient_name,
                    "email"=>$email,
                    "gender"=>$gender,
                    "date_of_birth" => $date_of_birth,
                    "blood_group"=>$blood_group,
                    "address"=>$address,
                    "city" => $city,
                    "state"=>$state,
                    "pincode"=>$pincode,
                    "medical_profile"=>$medical,
                    "contact_no" => $contact_no,
                     'clinic_id' => $clinic_id,
                     'clinic_name'=> $clinic_name,
                     'booking_date' => $booking_date,
                     'consultation_type' => $consultation_type,
                     'consultation_charge' => $charge,
                     'from_time' => $from_time,
                     'to_time' => $to_time,
                     'description' => $description,
                     'status' => $status,
                     //'bachatcard'=>$bcht
                     );
             }
             
              
        
        }
         }
        }
        
        
        
        
         return $resultpost;
    }*/
	
	  public function view_appointments_module($user_id)
    {
        $resultpost=array();
        // Doctor Panel
        $Appointment_dataList = $this->db->query("SELECT * FROM `doctor_booking_master` where `listing_id`= '$user_id' order by id desc");
        $count_appointment_slot = $Appointment_dataList->num_rows();
        if ($count_appointment_slot > 0) {
            foreach ($Appointment_dataList->result_array() as $row) 
          {
             $id =$row['id'];
            $booking_id = $row['booking_id'];
            $user_id  = $row['user_id'];
            $patient_id = $row['patient_id'];
            $listing_id = $row['listing_id'];
            $clinic_id  = $row['clinic_id'];
            $booking_date = $row['booking_date'];
            $consultation_type = $row['consultation_type'];
            $from_time = $row['from_time'];
            $to_time = $row['to_time'];
            $description = $row['description'];
            $status = $row['status'];
            $charge=$row['consultation_charges'];
            $query12 =   $this->db->query("SELECT `name` FROM `users` where `id`= '$user_id' ");
            $doctor = $query12->row_array();
            $user_name = $doctor['name'];
           
             if($clinic_id!=0)
             {
             $query12 =   $this->db->query("SELECT `clinic_name` FROM `doctor_clinic` where  `id` = '$clinic_id'");
             $count_query12 = $query12->num_rows();
             if($count_query12 > 0)
             {
            $clinic = $query12->row_array();
           
            $clinic_name = $clinic['clinic_name'];
             }
             else
             {
                 $query12 =   $this->db->query("SELECT `name_of_hospital` FROM `hospitals` where  `user_id` = '$clinic_id'");
            
            $clinic = $query12->row_array();
           $clinic_name = $clinic['name_of_hospital'];
             }
             }
             else
             {
                 $clinic_name="";
             }
            
            //echo "SELECT `id`,`relationship` FROM `health_record` where `user_id`= '$user_id' and `id`='$id'";
            
            $query12 =   $this->db->query("SELECT `id`,`relationship` FROM `health_record` where `relation_id`= '$patient_id'");
            $count_appointment_slot1 = $query12->num_rows();
            if($count_appointment_slot1 > 0 )
            {
            $Patient = $query12->row_array();
            $patient_relation = $Patient['relationship'];
            } 
          
           else
           {
                $patient_relation = "Myself";
           }
            if($patient_id=="0")
            {
                $patient_id=$user_id;
            }
           $is_user='1';
           
            $query12 =   $this->db->query("SELECT `id` FROM `doctor_prescription` where booking_id='$booking_id'");
            $data = $query12->row_array();
            
            $pr_id = $data['id'];
          
            $url="";
                $pdf="";
            if($pr_id == null)
            { 
                $pr_id = "";
                $url="";
                $pdf="";
            }
            
            else
            {
                $url=$this->get_url_version().$pr_id;
                $pdf=$this->get_pdf_version().$booking_id.".pdf";                           
                                          
            }
          
            
            
            //$patient_relation = $Patient['relationship'];
          // echo $user_id;
          // die;
            if(!empty($user_id))
            {
           $query1 = $this->db->query("SELECT *  FROM `doctor_patient` WHERE `user_id`='$user_id'");
            $count1 = $query1->num_rows();
            if ($count1 > 0) 
                {
                   
                     $row1=$query1->row_array();  
                      
                        $patient_name   = $row1['patient_name'];
                        $email          = $row1['email'];
                        $gender         = $row1['gender'];
                        $date_of_birth  = $row1['date_of_birth'];
                        $blood_group    = $row1['blood_group'];
                        $address        = $row1['address'];
                        $city           = $row1['city'];
                        $state          = $row1['state'];
                        $pincode        = $row1['pincode'];
                        $medical        = $row1['medical_profile'];
                        $contact_no     = $row1['contact_no'];
                        $user_id        = $row1['user_id'];
                }
            }
                 else
                 {$patient_name="";
                       
                            $email="";
                       
                            $gender="";
                         $date_of_birth="";
                        
                            $blood_group="";
                       
                            $address="";
                        
                            $city="";
                        
                            $state="";
                        
                            $pincode="";
                        
                            $medical="";
                       
                            $user_id="";
                            $contact_no="";
           
                     
                 }
                           
                    if(empty($patient_name))
                        {
                            $patient_name="";
                        }
                        if(empty($email))
                        {
                            $email="";
                        }
                        if(empty($gender))
                        {
                            $gender="";
                        }
                        if(empty($date_of_birth))
                        {
                            $date_of_birth="";
                        }
                        if(empty($blood_group))
                        {
                            $blood_group="";
                        }
                        if(empty($address))
                        {
                            $address="";
                        }
                        if(empty($city))
                        {
                            $city="";
                        }
                        if(empty($state))
                        {
                            $state="";
                        }
                        if(empty($pincode))
                        {
                            $pincode="";
                        }
                         if(empty($medical))
                        {
                            $medical="";
                        }
                         if(empty($user_id))
                        {
                            $user_id="";
                        }
                         if(empty($contact_no))
                        {
                            $contact_no="";
                        }
                             
               
                     
                  
             $bcht="";
             $resultpost[] = array(
                 'patient_id' => $user_id,
                 'booking_id' => $booking_id,
                 //'user_name' => $user_name,
                 //'patient_id' => $patient_id,
                 'patient_relation'=> $patient_relation,
                 'is_user' => $is_user,
                 'prescription_id' => $url,
                 'prescription_pdf' => $pdf,
                // 'doctor_name' => $doctor_name,
                "patient_name" => $patient_name,
                "email"=>$email,
                "gender"=>$gender,
                "date_of_birth" => $date_of_birth,
                "blood_group"=>$blood_group,
                "address"=>$address,
                "city" => $city,
                "state"=>$state,
                "pincode"=>$pincode,
                "medical_profile"=>$medical,
                "contact_no" => $contact_no,
                 'clinic_id' => $clinic_id,
                 'clinic_name'=> $clinic_name,
                 'booking_date' => $booking_date,
                 'consultation_type' => $consultation_type,
                 'consultation_charge' => $charge,
                 'from_time' => $from_time,
                 'to_time' => $to_time,
                 'description' => $description,
                 'status' => $status,
                 //'bachatcard'=>$bcht
                 );
         }
         
          
    
        }
        
        // HBIOMS
        $doct=$this->db->query("SELECT * FROM `doctor_list` WHERE `user_id` = $user_id"); 
        $doct_id=$doct->row_array();
        $list=$doct_id['hospital_doctor_id'];
        if(!empty($list))
        {
         $blist=explode(",",$list);
         for($i=0;$i < count($blist); $i++)
         {
            $Appointment_dataList = $this->db->query("SELECT * FROM `hospital_booking_master` where `doctor_id`= '$blist[$i]' order by id desc");
            $count_appointment_slot = $Appointment_dataList->num_rows();
            if ($count_appointment_slot > 0) {
                foreach ($Appointment_dataList->result_array() as $row) 
              {
                 $id =$row['id'];
                $booking_id = $row['booking_id'];
                $user_id  = $row['user_id'];
                $patient_id = $row['patient_id'];
                $listing_id = $row['doctor_id'];
                $clinic_id  = $row['listing_id'];
                $booking_date = $row['booking_date'];
                $consultation_type = $row['consultation_type'];
                $from_time = $row['from_time'];
                $to_time = $row['to_time'];
                $description = $row['description'];
                $status = $row['status'];
                
                $query12 =   $this->db->query("SELECT `name` FROM `users` where `id`= '$user_id' ");
                $doctor = $query12->row_array();
                $user_name = $doctor['name'];
               
               
                 $query12 =   $this->db->query("SELECT `clinic_name` FROM `doctor_clinic` where  `id` = '$clinic_id'");
                 $count_query12 = $query12->num_rows();
                 if($count_query12 > 0)
                 {
                $clinic = $query12->row_array();
               
                $clinic_name = $clinic['clinic_name'];
                 }
                 else
                 {
                     $query12 =   $this->db->query("SELECT `name_of_hospital` FROM `hospitals` where  `user_id` = '$clinic_id'");
                
                $clinic = $query12->row_array();
               $clinic_name = $clinic['name_of_hospital'];
                 }
               
                
                //echo "SELECT `id`,`relationship` FROM `health_record` where `user_id`= '$user_id' and `id`='$id'";
                
                $query12 =   $this->db->query("SELECT `id`,`relationship` FROM `health_record` where `relation_id`= '$patient_id'");
                $count_appointment_slot1 = $query12->num_rows();
                if($count_appointment_slot1 > 0 )
                {
                $Patient = $query12->row_array();
                $patient_relation = $Patient['relationship'];
                } 
              
               else
               {
                    $patient_relation = "Myself";
               }
                if($patient_id=="0")
                {
                    $patient_id=$user_id;
                }
               $is_user='1';
               
                $query12 =   $this->db->query("SELECT `id` FROM `doctor_prescription` where booking_id='$booking_id'");
                $data = $query12->row_array();
                
                $pr_id = $data['id'];
                
                $url="";
                    $pdf="";
                if($pr_id == null)
                { 
                    $pr_id = "";
                    $url="";
                    $pdf="";
                }
                
                else
                {
                    $url=$this->get_url_version().$pr_id;
                    $pdf=$this->get_pdf_version().$booking_id.".pdf";                           
                                              
                }
              
                
                
                //$patient_relation = $Patient['relationship'];
              // echo $user_id;
              // die;
                if(!empty($user_id))
                {
               $query1 = $this->db->query("SELECT *  FROM `hospital_ipd_patient` WHERE `user_id`='$user_id'");
                $count1 = $query1->num_rows();
                if ($count1 > 0) 
                    {
                       
                         $row1=$query1->row_array();  
                          
                            $patient_name   = $row1['name'];
                            $email          = $row1['email'];
                            $gender         = $row1['sex'];
                            $date_of_birth  = $row1['date_of_birth'];
                            $blood_group    = $row1['blood_group'];
                            $address        = $row1['address'];
                            $city           = $row1['city'];
                            $state          = $row1['state'];
                            $pincode        = $row1['pincode'];
                            //$medical        = $row1['medical_profile'];
                            $medical        = "";
                            $contact_no     = $row1['phone'];
                            $user_id        = $row1['user_id'];
                    }
                }
                     else
                     {$patient_name="";
                           
                                $email="";
                           
                                $gender="";
                             $date_of_birth="";
                            
                                $blood_group="";
                           
                                $address="";
                            
                                $city="";
                            
                                $state="";
                            
                                $pincode="";
                            
                                $medical="";
                           
                                $user_id="";
                                $contact_no="";
               
                         
                     }
                               
                        if(empty($patient_name))
                            {
                                $patient_name="";
                            }
                            if(empty($email))
                            {
                                $email="";
                            }
                            if(empty($gender))
                            {
                                $gender="";
                            }
                            if(empty($date_of_birth))
                            {
                                $date_of_birth="";
                            }
                            if(empty($blood_group))
                            {
                                $blood_group="";
                            }
                            if(empty($address))
                            {
                                $address="";
                            }
                            if(empty($city))
                            {
                                $city="";
                            }
                            if(empty($state))
                            {
                                $state="";
                            }
                            if(empty($pincode))
                            {
                                $pincode="";
                            }
                             if(empty($medical))
                            {
                                $medical="";
                            }
                             if(empty($user_id))
                            {
                                $user_id="";
                            }
                             if(empty($contact_no))
                            {
                                $contact_no="";
                            }
                                 
                    $sql2             = "SELECT id, clinic_name,consultation_charges FROM doctor_clinic WHERE `doctor_id`= '$listing_id' AND `id` = '$clinic_id'";
                   $query_practices  = $this->db->query($sql2);
                   $total_practices  = $query_practices->num_rows();
                   if(count($total_practices) > 0)
                   {
                   $get_pract=$query_practices->row_array();
                   $charge=$get_pract['consultation_charges'];
                   }
                   else
                   {
                     $charge="0";  
                   }
                           if(empty($charge))
                           {
                              $charge="0"; 
                           }
                           
                  $sql2             = "SELECT id, clinic_name,consultation_charges FROM doctor_clinic WHERE `doctor_id`= '$listing_id' AND `id` = '$clinic_id'";
                   $query_practices  = $this->db->query($sql2);
                   $total_practices  = $query_practices->num_rows();
                   if(count($total_practices) > 0)
                   {
                   $get_pract=$query_practices->row_array();
                   $charge=$get_pract['consultation_charges'];
                   }
                   else
                   {
                     $charge="0";  
                   }           
                      if(empty($charge))
                      {
                          $charge="";
                      }
                 $bcht="";
                 $resultpost[] = array(
                     'patient_id' => $user_id,
                     'booking_id' => $booking_id,
                     //'user_name' => $user_name,
                     //'patient_id' => $patient_id,
                     'patient_relation'=> $patient_relation,
                     'is_user' => $is_user,
                     'prescription_id' => $url,
                     'prescription_pdf' => $pdf,
                    // 'doctor_name' => $doctor_name,
                    "patient_name" => $patient_name,
                    "email"=>$email,
                    "gender"=>$gender,
                    "date_of_birth" => $date_of_birth,
                    "blood_group"=>$blood_group,
                    "address"=>$address,
                    "city" => $city,
                    "state"=>$state,
                    "pincode"=>$pincode,
                    "medical_profile"=>$medical,
                    "contact_no" => $contact_no,
                     'clinic_id' => $clinic_id,
                     'clinic_name'=> $clinic_name,
                     'booking_date' => $booking_date,
                     'consultation_type' => $consultation_type,
                     'consultation_charge' => $charge,
                     'from_time' => $from_time,
                     'to_time' => $to_time,
                     'description' => $description,
                     'status' => $status,
                     //'bachatcard'=>$bcht
                     );
             }
             
              
        
        }
         }
        }
        
        
        
         return $resultpost;
    }

  public function view_appointments_by_appoint_id_module($user_id,$booking_id)
    {
        $resultpost=array();
        // Doctor Panel
        $Appointment_dataList = $this->db->query("SELECT * FROM `doctor_booking_master` where `listing_id`= '$user_id' and booking_id='$booking_id' order by id desc");
        $count_appointment_slot = $Appointment_dataList->num_rows();
        if ($count_appointment_slot > 0) {
            foreach ($Appointment_dataList->result_array() as $row) 
          {
             $id =$row['id'];
            $booking_id = $row['booking_id'];
            $user_id  = $row['user_id'];
            $patient_id = $row['patient_id'];
            $listing_id = $row['listing_id'];
            $clinic_id  = $row['clinic_id'];
            $booking_date = $row['booking_date'];
            $consultation_type = $row['consultation_type'];
            $from_time = $row['from_time'];
            $to_time = $row['to_time'];
            $description = $row['description'];
            $status = $row['status'];
            
            $query12 =   $this->db->query("SELECT `name` FROM `users` where `id`= '$user_id' ");
            $doctor = $query12->row_array();
            $user_name = $doctor['name'];
           
           
             $query12 =   $this->db->query("SELECT `clinic_name` FROM `doctor_clinic` where  `id` = '$clinic_id'");
             $count_query12 = $query12->num_rows();
             if($count_query12 > 0)
             {
            $clinic = $query12->row_array();
           
            $clinic_name = $clinic['clinic_name'];
             }
             else
             {
                 $query12 =   $this->db->query("SELECT `name_of_hospital` FROM `hospitals` where  `user_id` = '$clinic_id'");
            
            $clinic = $query12->row_array();
           $clinic_name = $clinic['name_of_hospital'];
             }
           
            
            //echo "SELECT `id`,`relationship` FROM `health_record` where `user_id`= '$user_id' and `id`='$id'";
            
            $query12 =   $this->db->query("SELECT `id`,`relationship` FROM `health_record` where `relation_id`= '$patient_id'");
            $count_appointment_slot1 = $query12->num_rows();
            if($count_appointment_slot1 > 0 )
            {
            $Patient = $query12->row_array();
            $patient_relation = $Patient['relationship'];
            } 
          
           else
           {
                $patient_relation = "Myself";
           }
            if($patient_id=="0")
            {
                $patient_id=$user_id;
            }
           $is_user='1';
           
           $query12 =   $this->db->query("SELECT `id` FROM `doctor_prescription` where booking_id='$booking_id'");
            $data = $query12->row_array();
            
            $pr_id = $data['id'];
           
            $url="";
                $pdf="";
            if($pr_id == null)
            { 
                $pr_id = "";
                $url="";
                $pdf="";
            }
            
            else
            {
                $url=$this->get_url_version().$pr_id;
                $pdf=$this->get_pdf_version().$booking_id.".pdf";                           
                                          
            }
          
            
            
            //$patient_relation = $Patient['relationship'];
          // echo $user_id;
          // die;
            if(!empty($user_id))
            {
           $query1 = $this->db->query("SELECT *  FROM `doctor_patient` WHERE `user_id`='$user_id'");
            $count1 = $query1->num_rows();
            if ($count1 > 0) 
                {
                   
                     $row1=$query1->row_array();  
                      
                        $patient_name   = $row1['patient_name'];
                        $email          = $row1['email'];
                        $gender         = $row1['gender'];
                        $date_of_birth  = $row1['date_of_birth'];
                        $blood_group    = $row1['blood_group'];
                        $address        = $row1['address'];
                        $city           = $row1['city'];
                        $state          = $row1['state'];
                        $pincode        = $row1['pincode'];
                        $medical        = $row1['medical_profile'];
                        $contact_no     = $row1['contact_no'];
                        $user_id        = $row1['user_id'];
                }
            }
                 else
                 {$patient_name="";
                       
                            $email="";
                       
                            $gender="";
                         $date_of_birth="";
                        
                            $blood_group="";
                       
                            $address="";
                        
                            $city="";
                        
                            $state="";
                        
                            $pincode="";
                        
                            $medical="";
                       
                            $user_id="";
                            $contact_no="";
           
                     
                 }
                           
                    if(empty($patient_name))
                        {
                            $patient_name="";
                        }
                        if(empty($email))
                        {
                            $email="";
                        }
                        if(empty($gender))
                        {
                            $gender="";
                        }
                        if(empty($date_of_birth))
                        {
                            $date_of_birth="";
                        }
                        if(empty($blood_group))
                        {
                            $blood_group="";
                        }
                        if(empty($address))
                        {
                            $address="";
                        }
                        if(empty($city))
                        {
                            $city="";
                        }
                        if(empty($state))
                        {
                            $state="";
                        }
                        if(empty($pincode))
                        {
                            $pincode="";
                        }
                         if(empty($medical))
                        {
                            $medical="";
                        }
                         if(empty($user_id))
                        {
                            $user_id="";
                        }
                         if(empty($contact_no))
                        {
                            $contact_no="";
                        }
                             
                $sql2             = "SELECT id, clinic_name,consultation_charges FROM doctor_clinic WHERE `doctor_id`= '$listing_id' AND `id` = '$clinic_id'";
               $query_practices  = $this->db->query($sql2);
               $total_practices  = $query_practices->num_rows();
               if(count($total_practices) > 0)
               {
               $get_pract=$query_practices->row_array();
               $charge=$get_pract['consultation_charges'];
               }
               else
               {
                 $charge="0";  
               }
                       if(empty($charge))
                       {
                          $charge="0"; 
                       }
                       
              $sql2             = "SELECT id, clinic_name,consultation_charges FROM doctor_clinic WHERE `doctor_id`= '$listing_id' AND `id` = '$clinic_id'";
               $query_practices  = $this->db->query($sql2);
               $total_practices  = $query_practices->num_rows();
               if(count($total_practices) > 0)
               {
               $get_pract=$query_practices->row_array();
               $charge=$get_pract['consultation_charges'];
               }
               else
               {
                 $charge="0";  
               }           
                  if(empty($charge))
                  {
                      $charge="";
                  }
             $bcht="";
             $resultpost[] = array(
                 'patient_id' => $user_id,
                 'booking_id' => $booking_id,
                 //'user_name' => $user_name,
                 //'patient_id' => $patient_id,
                 'patient_relation'=> $patient_relation,
                 'is_user' => $is_user,
                 'prescription_id' => $url,
                 'prescription_pdf' => $pdf,
                // 'doctor_name' => $doctor_name,
                "patient_name" => $patient_name,
                "email"=>$email,
                "gender"=>$gender,
                "date_of_birth" => $date_of_birth,
                "blood_group"=>$blood_group,
                "address"=>$address,
                "city" => $city,
                "state"=>$state,
                "pincode"=>$pincode,
                "medical_profile"=>$medical,
                "contact_no" => $contact_no,
                 'clinic_id' => $clinic_id,
                 'clinic_name'=> $clinic_name,
                 'booking_date' => $booking_date,
                 'consultation_type' => $consultation_type,
                 'consultation_charge' => $charge,
                 'from_time' => $from_time,
                 'to_time' => $to_time,
                 'description' => $description,
                 'status' => $status,
                 //'bachatcard'=>$bcht
                 );
         }
        }
        
        // HBIOMS
        $doct=$this->db->query("SELECT * FROM `doctor_list` WHERE `user_id` = $user_id"); 
        $doct_id=$doct->row_array();
        $list=$doct_id['hospital_doctor_id'];
        if(!empty($list))
        {
         $blist=explode(",",$list);
         for($i=0;$i < count($blist); $i++)
         {
            $Appointment_dataList = $this->db->query("SELECT * FROM `hospital_booking_master` where `doctor_id`= '$blist[$i]' and booking_id='$booking_id' order by id desc");
            $count_appointment_slot = $Appointment_dataList->num_rows();
            if ($count_appointment_slot > 0) {
                foreach ($Appointment_dataList->result_array() as $row) 
              {
                 $id =$row['id'];
                $booking_id = $row['booking_id'];
                $user_id  = $row['user_id'];
                $patient_id = $row['patient_id'];
                $listing_id = $row['doctor_id'];
                $clinic_id  = $row['listing_id'];
                $booking_date = $row['booking_date'];
                $consultation_type = $row['consultation_type'];
                $from_time = $row['from_time'];
                $to_time = $row['to_time'];
                $description = $row['description'];
                $status = $row['status'];
                
                $query12 =   $this->db->query("SELECT `name` FROM `users` where `id`= '$user_id' ");
                $doctor = $query12->row_array();
                $user_name = $doctor['name'];
               
               
                 $query12 =   $this->db->query("SELECT `clinic_name` FROM `doctor_clinic` where  `id` = '$clinic_id'");
                 $count_query12 = $query12->num_rows();
                 if($count_query12 > 0)
                 {
                $clinic = $query12->row_array();
               
                $clinic_name = $clinic['clinic_name'];
                 }
                 else
                 {
                     $query12 =   $this->db->query("SELECT `name_of_hospital` FROM `hospitals` where  `user_id` = '$clinic_id'");
                
                $clinic = $query12->row_array();
               $clinic_name = $clinic['name_of_hospital'];
                 }
               
                
                //echo "SELECT `id`,`relationship` FROM `health_record` where `user_id`= '$user_id' and `id`='$id'";
                
                $query12 =   $this->db->query("SELECT `id`,`relationship` FROM `health_record` where `relation_id`= '$patient_id'");
                $count_appointment_slot1 = $query12->num_rows();
                if($count_appointment_slot1 > 0 )
                {
                $Patient = $query12->row_array();
                $patient_relation = $Patient['relationship'];
                } 
              
               else
               {
                    $patient_relation = "Myself";
               }
                if($patient_id=="0")
                {
                    $patient_id=$user_id;
                }
               $is_user='1';
               
                $query12 =   $this->db->query("SELECT `id` FROM `doctor_prescription` where booking_id='$booking_id'");
            $data = $query12->row_array();
            
            $pr_id = $data['id'];
           
            $url="";
                $pdf="";
            if($pr_id == null)
            { 
                $pr_id = "";
                $url="";
                $pdf="";
            }
            
            else
            {
                $url=$this->get_url_version().$pr_id;
                $pdf=$this->get_pdf_version().$booking_id.".pdf";                           
                                          
            }
              
                
                
                //$patient_relation = $Patient['relationship'];
              // echo $user_id;
              // die;
                if(!empty($user_id))
                {
               $query1 = $this->db->query("SELECT *  FROM `doctor_patient` WHERE `user_id`='$user_id'");
                $count1 = $query1->num_rows();
                if ($count1 > 0) 
                    {
                       
                         $row1=$query1->row_array();  
                          
                            $patient_name   = $row1['patient_name'];
                            $email          = $row1['email'];
                            $gender         = $row1['gender'];
                            $date_of_birth  = $row1['date_of_birth'];
                            $blood_group    = $row1['blood_group'];
                            $address        = $row1['address'];
                            $city           = $row1['city'];
                            $state          = $row1['state'];
                            $pincode        = $row1['pincode'];
                            $medical        = $row1['medical_profile'];
                            $contact_no     = $row1['contact_no'];
                            $user_id        = $row1['user_id'];
                    }
                }
                     else
                     {$patient_name="";
                           
                                $email="";
                           
                                $gender="";
                             $date_of_birth="";
                            
                                $blood_group="";
                           
                                $address="";
                            
                                $city="";
                            
                                $state="";
                            
                                $pincode="";
                            
                                $medical="";
                           
                                $user_id="";
                                $contact_no="";
               
                         
                     }
                               
                        if(empty($patient_name))
                            {
                                $patient_name="";
                            }
                            if(empty($email))
                            {
                                $email="";
                            }
                            if(empty($gender))
                            {
                                $gender="";
                            }
                            if(empty($date_of_birth))
                            {
                                $date_of_birth="";
                            }
                            if(empty($blood_group))
                            {
                                $blood_group="";
                            }
                            if(empty($address))
                            {
                                $address="";
                            }
                            if(empty($city))
                            {
                                $city="";
                            }
                            if(empty($state))
                            {
                                $state="";
                            }
                            if(empty($pincode))
                            {
                                $pincode="";
                            }
                             if(empty($medical))
                            {
                                $medical="";
                            }
                             if(empty($user_id))
                            {
                                $user_id="";
                            }
                             if(empty($contact_no))
                            {
                                $contact_no="";
                            }
                                 
                    $sql2             = "SELECT id, clinic_name,consultation_charges FROM doctor_clinic WHERE `doctor_id`= '$listing_id' AND `id` = '$clinic_id'";
                   $query_practices  = $this->db->query($sql2);
                   $total_practices  = $query_practices->num_rows();
                   if(count($total_practices) > 0)
                   {
                   $get_pract=$query_practices->row_array();
                   $charge=$get_pract['consultation_charges'];
                   }
                   else
                   {
                     $charge="0";  
                   }
                           if(empty($charge))
                           {
                              $charge="0"; 
                           }
                           
                  $sql2             = "SELECT id, clinic_name,consultation_charges FROM doctor_clinic WHERE `doctor_id`= '$listing_id' AND `id` = '$clinic_id'";
                   $query_practices  = $this->db->query($sql2);
                   $total_practices  = $query_practices->num_rows();
                   if(count($total_practices) > 0)
                   {
                   $get_pract=$query_practices->row_array();
                   $charge=$get_pract['consultation_charges'];
                   }
                   else
                   {
                     $charge="0";  
                   }           
                      if(empty($charge))
                      {
                          $charge="";
                      }
                 $bcht="";
                 $resultpost[] = array(
                     'patient_id' => $user_id,
                     'booking_id' => $booking_id,
                     //'user_name' => $user_name,
                     //'patient_id' => $patient_id,
                     'patient_relation'=> $patient_relation,
                     'is_user' => $is_user,
                     'prescription_id' => $url,
                     'prescription_pdf' => $pdf,
                    // 'doctor_name' => $doctor_name,
                    "patient_name" => $patient_name,
                    "email"=>$email,
                    "gender"=>$gender,
                    "date_of_birth" => $date_of_birth,
                    "blood_group"=>$blood_group,
                    "address"=>$address,
                    "city" => $city,
                    "state"=>$state,
                    "pincode"=>$pincode,
                    "medical_profile"=>$medical,
                    "contact_no" => $contact_no,
                     'clinic_id' => $clinic_id,
                     'clinic_name'=> $clinic_name,
                     'booking_date' => $booking_date,
                     'consultation_type' => $consultation_type,
                     'consultation_charge' => $charge,
                     'from_time' => $from_time,
                     'to_time' => $to_time,
                     'description' => $description,
                     'status' => $status,
                     //'bachatcard'=>$bcht
                     );
             }
             
              
        
        }
         }
        }
        
        return $resultpost;
    }


 public function insert_doctor_users_feedback($doctor_id,$user_id,$type,$feedback,$ratings,$recommend,$booking_id,$booking_type)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $feedback_array = array(
            'type' => $type,
            'user_id' => $user_id,
            'doctor_id' => $doctor_id,
            'feedback' => $feedback,
            'created_at' => $date,
            'ratings' => $ratings,
            'recommend' => $recommend
        );
        
    
        $this->db->insert('doctor_user_feedback', $feedback_array);
        
        if($booking_type == 'inperson')
           {
            //$query = $this->db->query("UPDATE doctor_booking_master SET status='7' WHERE booking_id =$booking_id  AND user_id='$patient_id'");
           }
           else
           {
            $query = $this->db->query("UPDATE doctor_booking_master SET status='7' WHERE booking_id =$booking_id");

           }
        
          //added by jakir on 17-july-2018 for notification on add prescription 
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $user_details = $this->db->query("SELECT name FROM users WHERE id='$doctor_id'"); 
                 $getdetails = $user_details ->row_array();
                 $user_name = $getdetails['name'];
                 
                $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', You got the feedback from doctor' . $user_name;
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                $title = $usr_name . ', YouYou got the feedback from doctor' . $user_name;
                $customer_token_count = $customer_token->num_rows();

                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = $token_status['token'];
                    $img_url = $userimage;
                    $tag = 'text';
                    $key_count = '1';
                    $this->send_gcm_notify_feedback($title, $reg_id, $msg, $img_url,$tag,$agent);
                }
        
        return array(
            'status' => 201,
            'message' => 'success'
        );
        
    }
    
    
    public function send_gcm_notify_feedback($title, $reg_id, $msg, $img_url,$tag,$agent) {
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
                "notification_type" => 'user_feedback',
                "notification_date" => $date,
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch
        , CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }
    
    public function view_patient_details($user_id){
       
             $data =   $this->db->query("SELECT * FROM `users` where `id`= '$user_id'");
           
            $userInfo =  $data->row_array();
            
            
            
            
            $data = array(
                'name' =>             $userInfo['name'],
                'gender' =>           $userInfo['gender'],
                'dob' =>              $userInfo['dob'] ,
                'height' =>           $userInfo['height'],
                'height_cm_ft' =>     $userInfo['height_cm_ft'],
                'weight' =>           $userInfo['weight'],
                'weight_date' =>      $userInfo['weight_date'],
                'height_date' =>      $userInfo['height_date'],
                'age' =>              $userInfo['age'],
                'activity_level' =>   $userInfo['activity_level'],
                'marital_status' =>   $userInfo['marital_status'],
                'blood_group' =>      $userInfo['blood_group'],
                'blood_is_active' =>  $userInfo['blood_is_active'],
                'bmi' =>              $userInfo['bmi'],
                'diet_fitness' =>     $userInfo['diet_fitness'],
                'organ_donor' =>      $userInfo['organ_donor'],
                'sex_history' =>      $userInfo['sex_history'],
                'exercise_level' =>   $userInfo['exercise_level'],
                'heradiatry_problem' => $userInfo['heradiatry_problem'],
                'health_condition' =>   $userInfo['health_condition'],
                'addiction' =>          $userInfo['addiction'],
                'health_insurance' =>   $userInfo['health_insurance'],
                'allergies' =>          $userInfo['allergies']
    );
            
            
     foreach($data as $key => $value){
                    if($value == null){
                        $data[$key] = "";
                    }
                }
                
        
       
        return $data;
        
    } 
    
    // doctor_add_timings
    public function doctor_add_timings($data) {
        $this->db->insert('doctor_consultation', $data);
        $timing_id = $this->db->insert_id();
        return $timing_id;
    }
    
    // 
    public function doctor_edit_timings($data, $doctor_id, $consultation_type) {
        $where =   "doctor_user_id='$doctor_id' AND consultation_name='$consultation_type'";
        // $clinic_id = 0;
        
        // $allRows = $this->db->delete('doctor_slot_details', array('clinic_id' => $clinic_id, 'doctor_id'=> $doctor_id, consultation_type=>'$consultation_type'));
         $query = $this->db->query("SELECT * FROM `doctor_consultation` WHERE doctor_user_id = '$doctor_id' AND consultation_name = '$consultation_type'");  
         $disCount = $query->num_rows();
        //  $row1 = $query->result_array();
        
      
            if ($disCount > 0) {
                $this->db->where($where)->update('doctor_consultation', $data);
            } else {
                $this->db->insert('doctor_consultation', $data);
            }
        
        
       
        return 1;
    }
    
    
    
     public function doctor_view_timings($doctor_id, $consultation_type) {
        $weekDay = "Monday";
        $discount_amount_min = "";
        $discount_amount_max = "";
        $discount_type = "";
        $discount_limit = "";
        $discount_cat = "";
        $discount_exp = "";
        $cosultation_time ="";
        $charges="";
        $is_active="";
        // doctor_consultation
        $discountQuery = $this->db->query("SELECT * FROM `doctor_consultation` WHERE `doctor_user_id` = '$doctor_id' AND `consultation_name` = '$consultation_type'");  
         $disCount = $discountQuery->num_rows();
         $row1 = $discountQuery->result_array();
        //  `print_r($row1); die();
        
        //added for consultation change 
      $CosultQuery = $this->db->query("SELECT * FROM `doctor_list` WHERE `user_id` = '$doctor_id'");  
      //  echo "SELECT consultation_type FROM `doctor_list` WHERE `user_id` = '$doctor_id'";
         $disCount = $CosultQuery->num_rows();
         $consultrow1 = $CosultQuery->row();
         
         if($disCount>0)
         {
              $consultation_video = $consultrow1->consultaion_video;
              $consultation_chat = $consultrow1->consultaion_chat;
              $video="0";
              if($consultation_video!='0')
              {
                  $video="1";
              }
              $chat="0";
              if($consultation_chat!='0')
              {
                  $chat="1";
              }
              
         }
         else
         {
              $consultation_video = "0";
              $consultation_chat = "0";
              $video="0";
              $chat="0";
              
         }
          
         //end
            if ($disCount > 0) {
                foreach ($discountQuery->result_array() as $rowDis) {
                    
                    
                    $cosultation_time = $rowDis['duration'];
                    $discount_amount_min = $rowDis['discount_min'];
                    $discount_amount_max = $rowDis['discount_max'];
                    $discount_type = $rowDis['discount_type'];
                    $discount_limit = $rowDis['discount_limit'];
                    $discount_cat = $rowDis['discount_type'];
                    $is_active = $rowDis['is_active'];
                   
                    	
                }
                
            }
            else
            {
                $cosultation_time = "0";
                    $discount_amount_min = "0";
                    $discount_amount_max = "0";
                    $discount_type = "0";
                    $discount_limit = "0";
                    $discount_cat = "0";
                    $is_active = "0";
            }
        
        $finalData['doctor_id'] = $doctor_id;
        $finalData['chat'] = $chat;
        $finalData['video'] = $video;
        $finalData['chat_fee'] = $consultation_chat;
        $finalData['video_fee'] = $consultation_video;
        $finalData['discount_min'] = $discount_amount_min;
        $finalData['discount_max'] = $discount_amount_max;
        $finalData['discount_type'] = $discount_type;
        $finalData['discount_limit'] = $discount_limit;
        $finalData['cosultation_time'] = $cosultation_time;
        $finalData['is_active'] = $is_active;
    
             
                $clinic_id = 0;
            
                $final_Day = array();
                $time_slots = array();
                
                $weekday ='Sunday';
                
                for($i=0;$i<7;$i++){
                    
                 $weekday = date('l', strtotime($weekday.'+1 day'));
                 
                $queryTiming = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE doctor_id = '$doctor_id' AND clinic_id = '$clinic_id' AND day = '$weekday' AND consultation_type = '$consultation_type'");
                
                $countTiming = $queryTiming->num_rows();
               //echo $countTiming;
                $time_array = array();
                $time_array1 = array();
                $time_array2 = array();
                $time_array3 = array();
                $time_slott = array();
               
                foreach($queryTiming->result_array() as $row1){
                  
                 
                    $timeSlotDay = $row1['day'];
                 
                    $timeSlot = $row1['time_slot'];
                    $from_time = $row1['from_time'];
                    $to_time = $row1['to_time'];
                    //  echo $timeSlot;
                   
                     if ($timeSlot == 'Morning') {
        				$time_array[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        				} else  if ($timeSlot == 'Afternoon') {
        				$time_array1[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        				} else if ($timeSlot == 'Evening') {
        				$time_array3[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        				} else if ($timeSlot == 'Night') {
        				$time_array2[] = array(
        					'from_time' => $from_time,
        					'to_time' => $to_time
        				);
        			}
        			
        			
                }
                
                $time_slott[] = array(
                    'time_slot'=> 'Morning',
        			'time' => $time_array
        		);
        		$time_slott[] = array(
        		    'time_slot'=> 'Afternoon',
        			'time' => $time_array1
        		);
        		
        		$time_slott[] = array(
        		    'time_slot'=> 'Evening',
        			'time' => $time_array3
        		);
        		$time_slott[] = array(
        		    'time_slot'=> 'Night',
        			'time' => $time_array2
        		);
        		
        		$time_slots[] = array(
        		    'day'=>$weekday,
                  'slots'=> $time_slott
                ); 
                
                }
     
       
               
             
        
      $timings = $time_slots;
      $finalData['timing'] = $timings;
       
        return $finalData;
    }
    
    
    public function get_vendor_ledger_details($user_id){
        $debit_list =array();
        $point_list =array();
        $credit_list =array();
        $failure_list = array();
        $bachat_list = array();
        $credit_list_trans = array();
        //echo "SELECT * FROM user_ledger_balance WHERE user_id='$user_id'";
        
        $query = $this->db->query("SELECT * FROM user_ledger_balance WHERE user_id='$user_id'");

        $row = $query->row();
        
        if (isset($row))
        {
                $ledger_balance =  $row->ledger_balance;
        }else{
                $ledger_balance = 0;
        }
        /*$query_balance = $this->db->query("SELECT * FROM user_ledger_balance WHERE user_id='$user_id'");
        $ledger_balance = $query_balance->num_rows();
       
        if ($ledger_balance > 0) {
            foreach ($ledger_balance->result_array() as $row) {
                
                $balance      = $row['ledger_balance'];
               
                 $ledger_balance[] = array(
                    'ledger_balance' => $balance
                );
            }
        }*/
        
        
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
                $order_id      = $row['order_id'];
                $trans_type      = $row['trans_type'];
                 
                //echo "SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC";
                // $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC");
                $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') and listing_id='$user_id' order by id DESC");
                
                $count_query_point_trans = $query_point_trans->num_rows();
                 
                 
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
                                $trans_time = $row_nest['trans_time'];
                                 $trans_time = date('m/d/Y h:i a', strtotime($trans_time));
                                array_push($ttl_pointds,$row_nest['amount']);
                                $points_list_trans[] = array(
                                    'order_id' => $order_id,
                                    'trans_id' => $trans_id,
                                    'amount' => $amount-$amount_saved,
                                    'trans_mode' => $trans_mode,
                                    'trans_time'=>$trans_time
                                );
                            }
                        
                    }
                      // print_r($ttl_pointds); 
                    
                }
                if($row_nest['trans_type']  == '5'){        
                     $convert_rs = array_sum($ttl_pointds)/$rate;
                     $point_list[] = array(
                        'order_id' => $order_id,
                        'trans_type'=>$trans_type,
                        'total_points'=>$amount-$amount_saved,
                        'point_rupee'=>array_sum($ttl_pointds),
                        'trans_details'=>$points_list_trans,
                    ); 
                }else if($row_nest['trans_type']  == '4'){
                    $convert_rs = round(array_sum($ttl_pointds)/$rate,2);
                     $point_list[] = array(
                        'order_id' => $order_id,
                        'trans_type'=>$trans_type,
                        'total_points'=>$amount-$amount_saved,
                        'point_rupee'=>$convert_rs,
                        'trans_details'=>$points_list_trans,
                    ); 
                }
               
            }
           
           
        }      
        
        
        $query_credit = $this->db->query("SELECT * FROM user_ledger WHERE listing_id='$user_id' and (trans_type='0' or trans_type='1') GROUP BY order_id order by id DESC");
        
        $count_credit = $query_credit->num_rows();
        if ($count_credit > 0) {

            foreach ($query_credit->result_array() as $row) {
                
                $order_id      = $row['order_id'];
                

                $query_debit_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='0' or trans_type='1') order by id DESC");
                $count_query_debit_trans = $query_debit_trans->num_rows();
                 
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
                        if($trans_type == '0'){
                            $trans_type = '1';
                        } else {
                            $trans_type = '0';
                        }
        
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
                    'trans_details'=>$credit_list_trans
                ); 
                
                
            }
        }  
        
        
        
       
        $query_failure = $this->db->query("SELECT * FROM user_ledger WHERE listing_id='$user_id' AND trans_type = '2' order by id DESC");
        $count_failure = $query_failure->num_rows();
        if ($count_failure > 0) {

            foreach ($query_failure->result_array() as $row) {
                
                $order_id      = $row['order_id'];
                $trans_id      = $row['trans_id'];
                $trans_type    = $row['trans_type'];
                $amount        = $row['amount'];
                
                $failure_list[] = array(
                    'order_id' => $order_id,
                    'trans_id' => $trans_id,
                    'trans_type' => $trans_type,
                    'amount' => $amount
                );
            }
           
           
        }
     
        
        $query_bachat = $this->db->query("SELECT * FROM user_ledger WHERE listing_id='$user_id' AND (trans_type='1') GROUP BY order_id order by id DESC");

        $count_bachat = $query_bachat->num_rows();
        // print_r($count_point); die();
        //$ttl_pointds = array();
        if ($count_bachat > 0) {

            foreach ($query_bachat->result_array() as $row) {
                $ttl_bachat = array();
                $order_id      = $row['order_id'];
                $trans_type      = $row['trans_type'];
                 
                // $query_bachat_trans = $this->db->query();
                
                 
                //echo "SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC";
                // $query_point_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='4' or trans_type='5') order by id DESC");
                $query_bachat_trans = $this->db->query("SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='1') order by id DESC");
               // echo "SELECT * FROM user_ledger WHERE order_id='$order_id' and (trans_type='1') order by id DESC";
                $count_query_bachat_trans = $query_bachat_trans->num_rows();
                //  print_r ($query_bachat_trans);
                //  die();
                 $discount = "";
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
                                'trans_time'=>$trans_time
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
        $ledger_details['balance'] = $ledger_balance;
        $ledger_details['total_points']   =  $total_points;
        
        return $ledger_details;
        
    }
    
      // add_user_service_amount
     public function add_user_service_amount($doctor_id, $user_id, $booking_id, $amount, $doctor_comment){
         
        $query = $this->db->query("UPDATE `doctor_booking_master` SET status = '7' WHERE booking_id = '$booking_id'");
        
        $trans_id_debit =  mt_rand(1000000000000, 9999999999999);
        $trans_id_credit =  mt_rand(1000000000000, 9999999999999);
        $trans_id_point =  mt_rand(1000000000000, 9999999999999);
        
        $trans_type_debit = 1;
        $trans_type_credit = 0;
        $trans_type_point = 4;
      
        $user_id = $user_id;
        $listing_id = $doctor_id;
        $order_id =  date("YmdHis");
        $trans_mode = 3;
        $amount =  $amount;
        $vendor_comment =  $doctor_comment;
        
        $debit_data = array(
            'order_id' => $order_id,
            'trans_id' => $trans_id_debit,
            'trans_type' => $trans_type_debit,
            'listing_id' => $listing_id,
            'trans_mode' => $trans_mode,
            'amount' => $amount,
            'vendor_comment' => $vendor_comment
        );
        
        $credit_data = array(
            'order_id' => $order_id,
            'trans_id' => $trans_id_credit,
            'trans_type' => $trans_type_credit,
            'listing_id' => $listing_id,
            'trans_mode' => $trans_mode,
            'amount' => $amount,
            // 'vendor_comment' => $vendor_comment
        );
        
        $point_data = array(
            'order_id' => $order_id,
            'trans_id' => $trans_id_point,
            'trans_type' => $trans_type_point,
            'listing_id' => $listing_id,
            'trans_mode' => $trans_mode,
            'amount' => $amount,
            // 'vendor_comment' => $vendor_comment
        );
        
        
        $credit = $this->db->insert('user_ledger', $credit_data);
        $debit = $this->db->insert('user_ledger', $debit_data);
        $point = $this->db->insert('user_ledger', $point_data);
         
        $resp= array('doctor_id' => $doctor_id, 'user_id'=>$user_id, 'booking_id' => $booking_id, 'amount' => $amount, 'doctor_comment' => $vendor_comment);
         return $resp;
     }
     
    public function appointment_invitation($doctor_id,$user_id)
     {
         //added by jakir on 17-july-2018 for notification on add prescription 
         
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$doctor_id'");
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', Has send you invitation for book an apointment';
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                $title = $usr_name . ',  Has send you invitation for book an apointment';
                $customer_token_count = $customer_token->num_rows();

                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = $token_status['token'];
                    $img_url = $userimage;
                    $tag = 'text';
                    $key_count = '1';
                    $this->send_gcm_notify_1($title, $reg_id, $msg, $img_url,$tag,$agent,$doctor_id);
                }
           // doctor_booking_master
            return array();
     }
     
     
      public function appointment_invitation_like($doctor_id,$user_id)
     {
         //added by jakir on 17-july-2018 for notification on add prescription 
         
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$doctor_id'");
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', Has send you invitation for book an apointment';
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                $title = $usr_name . ',  Has send you invitation for book an apointment';
                $customer_token_count = $customer_token->num_rows();

                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = 'dIe6dlsQSXc:APA91bFYOKi2fq0lkx5mIhBAt0aXbv-2LczxqY763LtUnaSXU2cwghGBtNHuvGmo3yh3g6Kt6sWFJqTuswhDsGStWV-HNQFhICYvnM-UyNmKoiTaA2TTM4x-OJZBqYuT071mWXNra7OA';
                    $img_url = $userimage;
                    $tag = 'text';
                    $key_count = '1';
                    
                    $this->send_gcm_notify_1($title, $reg_id, $msg, $img_url,$tag,$agent,$doctor_id);
                }
           // doctor_booking_master
            return array();
     }
     
     
     
     //for doctor_list entry
     
       public function doctor_consultation_entry($doctor_id,$consultation_entry) {
       
        $q = $this->db->select('id')->from('doctor_list')->where('user_id', $doctor_id)->get()->row();
      
         
        // print_r($q);
        if (!empty($q)) {
            $this->db->where('user_id', $doctor_id)->update('doctor_list', $consultation_entry);
            return array(
                'status' => 200,
                'result' => 1
                );
        }
    }
    
    
      public function QR_code_images($doctor_id) {
       
        $q = $this->db->select('vendor_qrcode')->from('qrcode_vendor')->where('vendor_id', $doctor_id)->get()->row();
        // print_r($q);
           $vendor_query = $this->db->query("SELECT vendor_qrcode FROM qrcode_vendor WHERE vendor_id='$doctor_id'");
         $count = $vendor_query->num_rows();
         $vendor_data = $vendor_query->row();
        // print_r($vendor_data);
        if ($count>0) {
            $vendor_image = 'https://s3.amazonaws.com/medicalwale/images/QR+Code/'.$vendor_data->vendor_qrcode.'.png';
            
            return array(
                'data' => $vendor_image
                );
        }
        else
        {
             return array(
                'status' => 200,
                'message' => "success",
                'data' => ""
                );
        }
    }
     
     
     //added for add stroy like insta
     
   /* public function Add_story($doctor_id,$stroy_text,$story_file)
     {
           date_default_timezone_set('Asia/Kolkata');
             
        $date = date('Y-m-d H:i:s');
           $query = $this->db->query("SELECT user_id FROM doctor_list WHERE user_id='$doctor_id'");
         $count = $query->num_rows();
          if($count>0)
          {
           
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF", "pdf");
            include('../s3_config.php'); 
           
            if($story_file > 0) {
                $flag = '1';
                $video_flag = '1';
            
                foreach ($_FILES['stroy']['tmp_name'] as $key => $tmp_name) {
                    $img_name = $key . $_FILES['stroy']['name'][$key];
                    $img_size = $_FILES['stroy']['size'][$key];
                    $img_tmp = $_FILES['stroy']['tmp_name'][$key];
                    $ext = getExtension($img_name);
                    if (strlen($img_name) > 0) {
                            if (in_array($ext, $img_format)) {
                                $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/story_images/' . $actual_image_name;
                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                    if ($flag > 0) {
                                     //   https://s3.amazonaws.com/medicalwale/images/invoice_images/story_image/
                                        $img_url = 'https://s3.amazonaws.com/medicalwale/images/story_images/' . $actual_image_name;
                                        $imagedetails = getimagesize($img_url);
                                         $add_story_attachment = array(
                                             'source'    => $actual_image_name,
                                            'doctor_id'  => $doctor_id,
                                                'type'   => 'image',
                                                'text'   => $stroy_text,
                                             'created_at'    => $date
                                         );
                                        $inserted = $this->db->insert('Doctor_story', $add_story_attachment);
                                        
                                    }
                                    
                                }
                            }
                        
                    }
                }
                
            }
               
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'story_link' =>$img_url,
                    'stroy_text' => $stroy_text
                );
           
            
        }
          else
          {
               return array(
                    'status' => 201,
                    'message' => 'faluire',
                    'story_link' => ''
                );
          }
     }*/
     
      public function Add_story($doctor_id,$stroy_text,$story_file)
     {
        
           date_default_timezone_set('Asia/Kolkata');
             
        $date = date('Y-m-d H:i:s');
           $query = $this->db->query("SELECT user_id FROM doctor_list WHERE user_id='$doctor_id'");
         $count = $query->num_rows();
          if($count>0)
          {
            //---------------------------image ATTACHMENT UPLOAD---------------------------
            //echo $invoice_file;;
           /* echo $_FILES['invoice_file']['name'];
            print_r($_FILES);*/
            $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF", "pdf");
            $video_format = array("MP4", "mp4", "FLV", "flv", "AVI", "WEBM", "JPG", "webm");
            $both_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF", "pdf", "MP4", "mp4", "FLV", "flv", "AVI", "WEBM", "JPG", "webm");
            /*---commented bu nikhil 
	    include('../s3_config.php');
	    */
	    /*added by nikhil - starts*/
	    include('s3_config.php'); 	  
	    /*added by nikhil - ends*/		  
            $img_url ="";
            $image_file_count = count($_FILES);
            
            if($image_file_count > 0) {
                $flag = '1';
                $video_flag = '1';
             
                for ($i = 0; $i < $image_file_count; $i++) 
                {
                     $img_name = $_FILES['stroy']['name'][$i];
                    $img_size = $_FILES['stroy']['size'][$i];
                    $img_tmp = $_FILES['stroy']['tmp_name'][$i];
                     $ext = getExtension($img_name);
                    
                   if (strlen($img_name) > 0) {
                            if (in_array($ext, $both_format)) {
                                $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/story_images/' . $actual_image_name;
                                if (in_array($ext, $img_format)) {
                                    $type= "image";
                                }
                                else if (in_array($ext, $video_format)) {
                                     $type= "video";
                                }
                                
                               
                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                   
                                     //   https://s3.amazonaws.com/medicalwale/images/invoice_images/story_image/
                                        $img_url = 'https://s3.amazonaws.com/medicalwale/images/story_images/' . $actual_image_name;
                                       // $imagedetails = getimagesize($img_url);
                                         $add_story_attachment = array(
                                             'source'    => $actual_image_name,
                                            'doctor_id'  => $doctor_id,
                                                'type'   => $type,
                                                'text'   => $stroy_text,
                                             'created_at'    => $date
                                         );
                                        
                                        $inserted = $this->db->insert('Doctor_story', $add_story_attachment);
                                        
                                    
                                    
                                }
                            }
                        
                    }
                }
                
            }
             
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'story_link' =>$img_url,
                    'stroy_text' => $stroy_text
                );
           
            
        }
          else
          {
               return array(
                    'status' => 201,
                    'message' => 'faluire',
                    'story_link' => ''
                );
          }
     }
     
     //added for check from earlier backup
     
     public function Add_story_details($doctor_id)
     {
           date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
         $query = $this->db->query("SELECT * FROM Doctor_story where doctor_id='$doctor_id' and status='0'");
         $count = $query->num_rows();
         $data = array();
         if($count>0)
         {
             foreach($query->result_array() as $row)
             {
                 $id = $row['id'];
                 $doctor_id = $row['doctor_id'];
                 $type = $row['type'];
                 $source = $row['source'];
                 $text = $row['text'];
                 $highlight = $row['highlight'];
                 $created_at = $row['created_at'];
                 
                                 $url = $row['url'];
                                 $sour_final="";
                                 $live=0;
                                if(empty($url))
                                {
                                    $url="";
                                    $sour_final='https://s3.amazonaws.com/medicalwale/images/story_images/'.$source;
                                    $live=0;
                                }
                                else
                                {
                                    $sour_final="";
                                    $live=1;
                                }
                     $hourdiff = round((strtotime($date) - strtotime($created_at)) / 3600, 1);
                    // echo $hourdiff;
             if($hourdiff >= 24)
                 {
               //   $data[] = array();
                 }
                 else
                    {
                 $data[] = array(
                         'id' => $id,
                         'doctor_id' => $doctor_id,
                         'type' => $type,
                         'source' => $sour_final,
                         'text' => $text,
                         'created_at' => $created_at,
                         'ago' => $hourdiff,
                         'highlight' => $highlight,
                         'live_url' => $url,
                         'is_live'=>$live
                      );
                     }
             }
              return $data;
              
              
         }
         return array();
       
     }
     
     public function get_story_list($doctor_id)
     {
         $user_id=$doctor_id;
           date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
          $sql   = "SELECT * from doctor_dating_status where (user_id='$doctor_id' OR dating_id='$doctor_id') AND status=3";
         $query1 = $this->db->query($sql);
          $count1 = $query1->num_rows();
         if ($count1 > 0) 
            {
                
             foreach ($query1->result_array() as $row1) 
                     {
                          $doctor_user_id        = $row1['user_id'];
                          $doctor_dating_id      = $row1['dating_id'];
                          
                             if($doctor_user_id == $doctor_id)
                                {
                                    $final_id = $doctor_dating_id;
                                }
                                else
                                {
                                    $final_id = $doctor_user_id;
                                } 
                         $query = $this->db->query("SELECT * FROM Doctor_story where doctor_id = '$final_id' and status='0' order by id Desc");
                        
                         $count = $query->num_rows();
                         $data = array();
                         if($count>0)
                         {
                             foreach($query->result_array() as $row)
                             {
                                 $id = $row['id'];
                                 $doctor_id = $row['doctor_id'];
                                 $type = $row['type'];
                                 $source = $row['source'];
                                 $text = $row['text'];
                                 $highlight = $row['highlight'];
                                 $created_at = $row['created_at'];
                                 $url = $row['url'];
                                 $sour_final="";
                                 $live=0;
                                if(empty($url))
                                {
                                    $url="";
                                    $sour_final='https://s3.amazonaws.com/medicalwale/images/story_images/'.$source;
                                    $live=0;
                                }
                                else
                                {
                                    $sour_final="";
                                    $live=1;
                                }
                                 $doctor_info = $this->db->query("SELECT doctor_name,image FROM `doctor_list` WHERE `user_id` = '$doctor_id'");  
                     
                         $dCount = $doctor_info->num_rows();
                         $d_info = $doctor_info->row();
                         
                         if($dCount>0)
                         {
                              $doctor_name = $d_info->doctor_name;
                              $doctor_image = $d_info->image;
                         }
                         else
                         {
                             $doctor_name = "";
                              $doctor_image = "";
                         }
                                 
                                  if ($doctor_image != '') {
                                    $profile_pic = $doctor_image;
                                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                                } else {
                                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                                }
                                 
                            $hourdiff = round((strtotime($date) - strtotime($created_at)) / 3600, 1);
                                    // echo $hourdiff;
                             if($hourdiff <= 24)
                                 {
                                    $sql2  = "SELECT * from doctor_story_history where user_id='$user_id' and doctor_id='$doctor_id' and FIND_IN_SET('" . $id . "',story_id)";
                                    $query12 = $this->db->query($sql2);
                                    $count12 = $query12->num_rows();  
                                    if($count12 > 0)
                                       {
                                           $row12=$query12->row_array(); 
                                           $coun_story=$row12['count'];
                                           $is_seen=1;
                                           $id_arr=$row12['id'];
                                       }
                                    else
                                       {
                                          
                                           $coun_story=0;
                                           $is_seen=0;
                                           $id_arr=0;
                                       }
                                     
                               $data[] = array(
                                         'id' => $id,
                                         'doctor_id' => $doctor_id,
                                         'type' => $type,
                                         'source' =>$sour_final ,
                                         'text' => $text,
                                         'created_at' => $created_at,
                                         'ago' => $hourdiff,
                                         'doctor_name' => $doctor_name,
                                         'doctor_image' => $profile_pic,
                                         'highlight' => $highlight,
                                         'live_url'=>$url,
                                         'is_live'=>$live,
                                         'count_story'=>(int)$coun_story,
                                         'is_seen'=>$is_seen,
                                         'track_id'=>(int)$id_arr
                                      );
                                 }
                               
                                     
                             }
                              return $data;
                              
                              
         }
                     }
            }         
         
         
         return array();
       
     }
     
       public function event_tracker($user_id,$doctor_id,$count,$seen,$story_id,$track_id)
     {
         date_default_timezone_set('Asia/Kolkata');
         $date = date('Y-m-d H:i:s');
         if($track_id==0)
         {
            $p_results = $this->db->query("INSERT INTO `doctor_story_history`(`user_id`, `doctor_id`, `count`, `story_id`, `datetime`) 
               VALUES ('$user_id','$doctor_id','$count','$story_id','$date')");
            $insert_id = $this->db->insert_id();
            if($insert_id!=0)
            {
               $data= array(
                'status' => 200,
                'message' => 'success'
                 ); 
            }
            else
            {
                $data= array(
                'status' => 201,
                'message' => 'failed'
                 ); 
            }
         }
         else
         {
            $p_results = $this->db->query("UPDATE `doctor_story_history` SET `count`='$count',`story_id`='$story_id' WHERE `id`='$track_id'");
            if($this->db->affected_rows() > 0)
            {
                $data= array(
                'status' => 200,
                'message' => 'success'
                 ); 
            }
            else
            {
                $data= array(
                'status' => 201,
                'message' => 'failed'
                 ); 
            }
             
         }
         
         
         
         
         return $data;
       
     }
     
      public function delete_story($event_id,$user_id)
    {
        
                    $this->db->where('id',$event_id);
                    $this->db->where('doctor_id',$user_id);
                    $query = $this->db->delete('Doctor_story');
                    if($query){
                    	 return array(
                            'status' => 200,
                            'message' => 'success'
                            );
                    	}else{
                    		return array(
                            'status' => 200,
                            'message' => 'Failure'
                            );
                    	}
               
          
    }
     //added by zak for add badge count details 
      public function get_Badge_count($user_id)
       {
           date_default_timezone_set('Asia/Kolkata');
             $date = date('Y-m-d H:i:s');
         
                    
                  $doc_list =  $this->db->query("SELECT category as cat_doc_list FROM doctor_list WHERE user_id='$user_id'");
                  $doc_list_categroy = $doc_list->row();
                  // $doc_list_categroy->cat_doc_list;
                  $question_postlist = array();
                $doc_list_count = $doc_list->num_rows();
                if($doc_list_count>0)
               {
                   $doct_list_id = $doc_list_categroy->cat_doc_list;
                   $doctor_id_array = explode(',',$doct_list_id);
                   $doc_id_list = "";
             foreach($doctor_id_array as $d_id){
                 $doc_id_list .= " FIND_IN_SET('$d_id',id) or ";
             }
             $doc_id_list = substr($doc_id_list, 0, -3);
             
               
                   $business_category = $this->db->query("SELECT doctors_type_id as b_id FROM business_category WHERE business_category.id<>'' AND $doc_id_list");
               
                  
                $business_category_list = $business_category->result_array();
                
                    $b_id = '';
                   foreach($business_category_list as $bid)
                   {
                        $b_id .=','.$bid['b_id'];
                   }
                
               $b_id = substr($b_id, 1);     
               $tb = explode(',',$b_id);
               
               $tb_id_list = "";
             foreach($tb as $tb_id){
                 $tb_id_list .= " FIND_IN_SET('$tb_id',id) or ";
             }
             $tb_id_list = substr($tb_id_list, 0, -3);
            $doctor_type_id = $this->db->query("SELECT healthwall_category as h_id FROM doctor_type WHERE id<>'' AND $tb_id_list");
             $doctor_type_list = $doctor_type_id->result_array();
                    
                    $tb1_id_list = "";
                    foreach($doctor_type_list as $tb1_id){
                        $vl = $tb1_id['h_id'];
                        $tb1_id_list .= " FIND_IN_SET('$vl',posts.healthwall_category) or ";
                    }
                      $tb1_id_list = substr($tb1_id_list, 0, -3);
                    $query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND ($tb1_id_list) AND posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' and posts.type='question' order by posts.id DESC");
                    $count_query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND ($tb1_id_list) AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' and posts.type='question' order by posts.id DESC");
                    $count_post = $count_query->num_rows();
                    
                      if ($count_post > 0) {
                           $resultpost = array();
                           $answered_count = 0;
                             $unanswered_count = 0;
                            foreach ($query->result_array() as $row) {
                                 $post_id = $row['post_id'];
                                 //comments
                $query_comment = $this->db->query("SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' order by comments.id desc LIMIT 0,3");
                $comments = array();
              //   echo "SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' order by comments.id desc LIMIT 0,3";
                $query_comment_answer = $this->db->query("SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' AND comments.user_id='$user_id' order by comments.id desc LIMIT 1");
                $query_comment_count  = $query_comment_answer->num_rows();
                if($query_comment_count>0)
                {
                    $answered_count = $answered_count+1;
                }
                else
                {
                    $unanswered_count = $unanswered_count+1;
                }
                   }
                   
                    $question_postlist = array(
               // "count" => $count_post,
                'answered_count' => $answered_count,
                'unanswered_count' => $unanswered_count,
            );
         }
       }
       
                //added by zak for appointment list 
                
                $query_appointment = $this->db->query("select doctor_booking_master.booking_date, doctor_booking_master.booking_time, doctor_booking_master.description,
                                   doctor_booking_master.listing_id, doctor_patient.patient_name, doctor_patient.date_of_birth, doctor_patient.type,
                                   doctor_booking_master.id
                                   from doctor_booking_master INNER JOIN doctor_patient ON(doctor_booking_master.id = doctor_patient.booking_id )
                                   where doctor_booking_master.listing_id = '$user_id'");
             $appointment_count = $query_appointment->num_rows();
            
       
           $resultpost = array(
                    'question_count' => $question_postlist,
                 'appointment_count' =>  $appointment_count
               );
         
          return $resultpost;
       }
     //end
     
    // public function doctor_view_timings($doctor_id, $consultation_type) {
//       $weekDay = "Monday";
//       $discount_amount_min = "";
//       $discount_amount_max = "";
//       $discount_type = "";
//       $discount_limit = "";
//       $discount_cat = "";
//       $discount_exp = "";
//         $discountQuery = $this->db->query("SELECT * FROM `doctor_consultation` WHERE `doctor_user_id` = '$doctor_id' AND `consultation_name` = '$consultation_type'");  
//         $disCount = $discountQuery->num_rows();
     
//           if ($disCount > 0) {
//               foreach ($discountQuery->result_array() as $rowDis) {
//                   // print_r($rowDis); die();
                   
//                   $cosultation_time = $rowDis['open_hours'];
//                   $discount_amount_min = $rowDis['discount_min'];
//                   $discount_amount_max = $rowDis['discount_max'];
//                   $discount_type = $rowDis['discount_type'];
//                   $discount_limit = $rowDis['discount_limit'];
//                   $discount_cat = $rowDis['discount_type'];
//                   $is_active = $rowDis['is_active'];
//                   $charges = $rowDis['charges'];
                       
                       
//               }
               
//           }
       
//       $finalData['doctor_id'] = $doctor_id;
//       $finalData['consultation_type'] = $consultation_type;
//       $finalData['discount_min'] = $discount_amount_min;
//       $finalData['discount_max'] = $discount_amount_max;
//       $finalData['discount_type'] = $discount_type;
//       $finalData['discount_limit'] = $discount_limit;
//       $finalData['open_hours'] = $cosultation_time;
//       $finalData['charges'] = $charges;
//         $finalData['is_active'] = $is_active;
   
//       for($i=0;$i<7;$i++){
//             $query = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE clinic_id = 0 AND doctor_id = '$doctor_id' AND consultation_type = '$consultation_type' AND day = '$weekDay'");
           
             
//             foreach ($query->result_array() as $row)  {
//               //   print_r($row);
                 
//                   $timeSlot = array(

//                       'from_time' =>          $row['from_time'],
//                       'to_time' =>            $row['to_time'] ,
                       
//                   );
//                   $timeSlots['time_slot'] = $row['time_slot'];
//                   $timeSlots['time'] = $timeSlot;
//                   $time[] = $timeSlots;
//              }
             
//               $slot['day'] =   $weekDay; // Monday
//               $slot['slots'] =   $time; // Monday
//              $allSlots[] = $slot;
//              $weekDay = date('l', strtotime($weekDay . ' +1 day'));
//       }
//       $timings = $allSlots;
//       $finalData['timing'] = $timings;
     
//       return $finalData;
//   }
    
    
   public function doctor_list_gender($mlat, $mlng, $user_id, $gender,$page)
    {
        $radius = '5';
        $resultpost = array();
        
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        
        function GetDrivingDistance($lat1, $lat2, $long1, $long2)
        {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
            $ch  = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist       = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return $dist;
        }
        
       /*  if ($gender=="male") {
                    $genderdata = 'female';
                } else {
                    $genderdata = 'male';
                }*/
        // $sql = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE FIND_IN_SET('" . $category_id . "', doctor_list.category)  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
        /*$sql   = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE gender='" . $genderdata . "' HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));*/
        $sql   = "SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id !='$user_id' ORDER BY  rand() LIMIT $start, $limit";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $doctor_user_id      = $row['user_id'];
                
                $count_query = $this->db->query("SELECT * from doctor_dating_status where (user_id='$user_id' and dating_id='$doctor_user_id') OR (user_id='$doctor_user_id' and dating_id='$user_id')");
                $count_status = $count_query->num_rows();
             
                if($count_status == 0)
                {
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $bio                 = $row['bio'];
                        $followers           = '0';
                        $following           = "0";
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = '';
                        }
              
                       // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                       if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status   = $row['relation_ship_status'];
                        }
                        if(empty($bio))
                        {
                            $bio="";
                        }
                        else
                        {
                            $bio;
                        }
                        
                         $language_selection  = rtrim($row['language_selection'],',');
                         
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$doctor_user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                           $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                         $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2 || $address_privacy == 1)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2 || $address_privacy == 1)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2 || $address_privacy == 1)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2 || $address_privacy == 1)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2 || $address_privacy == 1)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2 || $address_privacy == 1)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2 || $address_privacy == 1)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2 || $address_privacy == 1)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2 || $address_privacy == 1)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2 || $address_privacy == 1)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2 || $address_privacy == 1)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2 || $address_privacy == 1)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2 || $address_privacy == 1)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                         
                        $resultpost[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'qualification'=> $degree,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => "0",
                            'followers' => "0",
                            'following' => "0",
                            'profile_views' => $profile_views,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends
                        );
                    }
            }  
        } else {
            $resultpost = array();
        }
      /*  function array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
        {
            $sort_col = array();
            foreach ($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }
            array_multisort($sort_col, $dir, $arr);
        }
        if(!empty($resultpost)){
        array_sort_by_column($resultpost, 'distance'); }*/
        return $resultpost;
    }
    
    public function you_may_know_list($mlat, $mlng, $user_id,$page_no,$per_page,$type)
    {
        $sqlss   = "SELECT doctor_list.medical_affiliation,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id ='$user_id' ";
        $queryss = $this->db->query($sqlss);
        
        $original = $queryss->row_array();
        
        $odegree              = $original['qualification'];           //1
        $omedical_affiliation = $original['medical_affiliation'];     //1
        
        $oclinic_name         = $original['clinic_name'];             //2
        $oaddress             = $original['address'];                 //2
        $ocity                = $original['city'];                    //2
        $ostate               = $original['state'];                   //2
        $opincode             = $original['pincode'];                 //2
                        
        $odob                 = date('Y-m-d',strtotime($original['dob']));                     //3
        
        $ogender              = $original['gender'];                  //4
        
        $oexperience          = $original['experience'];              //5
       
        $ospeciality          = $original['speciality'];              //6
        $oservice             = $original['service'];                 //6
        
        //other  //7
        $oeducation_array = array();
        $oworkplace_array = array();
        /*$oage_array       = array();
        $ogender_array    = array();*/
        $oexperience_array = array();
        $ospeciality_array = array();
        $other_array   = array();
        
        $resultpost = array();
        
        $offset = $per_page*($page_no - 1);
        $limit = "LIMIT $per_page OFFSET $offset";
        
        $array_not_in = array();
        //--------------------education--------------------
        if($type=="") {
        $sql   = "SELECT doctor_list.medical_affiliation,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id !='$user_id' AND (doctor_list.qualification LIKE '%$odegree%' OR doctor_list.medical_affiliation LIKE '%$omedical_affiliation%') ORDER BY  rand() $limit";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $doctor_user_id      = $row['user_id'];
                
                $count_query = $this->db->query("SELECT * from doctor_dating_status where (user_id='$user_id' and dating_id='$doctor_user_id') OR (user_id='$doctor_user_id' and dating_id='$user_id')");
                $count_status = $count_query->num_rows();
             
                if($count_status == 0)
                {
                        $array_not_in[]=$doctor_user_id;
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $clinic_name         = $row['clinic_name'];
                        $medical_affiliation = $row['medical_affiliation'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $bio                 = $row['bio'];
                        $followers           = '0';
                        $following           = "0";
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = '';
                        }
              
                       // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                       if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status   = $row['relation_ship_status'];
                        }
                        if(empty($bio))
                        {
                            $bio="";
                        }
                        else
                        {
                            $bio;
                        }
                        
                         $language_selection  = rtrim($row['language_selection'],',');
                         
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$doctor_user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                           $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                         $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2 || $address_privacy == 1)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2 || $address_privacy == 1)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2 || $address_privacy == 1)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2 || $address_privacy == 1)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2 || $address_privacy == 1)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2 || $address_privacy == 1)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2 || $address_privacy == 1)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2 || $address_privacy == 1)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2 || $address_privacy == 1)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2 || $address_privacy == 1)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2 || $address_privacy == 1)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2 || $address_privacy == 1)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2 || $address_privacy == 1)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                         
                        $oeducation_array[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'qualification'=> $degree,
                            'medical_affiliation' => $medical_affiliation,
                            'speciality'          => $speciality,
                            'clinic_name'         => $clinic_name,
                            'service'             => $service,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => "0",
                            'followers' => "0",
                            'following' => "0",
                            'profile_views' => $profile_views,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends
                        );
                    }
            }  
        }
        }
        else if($type=="education"){
        $sql   = "SELECT doctor_list.medical_affiliation,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id !='$user_id' AND (doctor_list.qualification LIKE '%$odegree%' OR doctor_list.medical_affiliation LIKE '%$omedical_affiliation%') ORDER BY  rand() $limit";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $doctor_user_id      = $row['user_id'];
                
                $count_query = $this->db->query("SELECT * from doctor_dating_status where (user_id='$user_id' and dating_id='$doctor_user_id') OR (user_id='$doctor_user_id' and dating_id='$user_id')");
                $count_status = $count_query->num_rows();
             
                if($count_status == 0)
                {
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $medical_affiliation = $row['medical_affiliation'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $bio                 = $row['bio'];
                        $followers           = '0';
                        $following           = "0";
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = '';
                        }
              
                       // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                       if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status   = $row['relation_ship_status'];
                        }
                        if(empty($bio))
                        {
                            $bio="";
                        }
                        else
                        {
                            $bio;
                        }
                        
                         $language_selection  = rtrim($row['language_selection'],',');
                         
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$doctor_user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                           $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                         $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2 || $address_privacy == 1)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2 || $address_privacy == 1)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2 || $address_privacy == 1)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2 || $address_privacy == 1)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2 || $address_privacy == 1)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2 || $address_privacy == 1)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2 || $address_privacy == 1)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2 || $address_privacy == 1)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2 || $address_privacy == 1)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2 || $address_privacy == 1)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2 || $address_privacy == 1)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2 || $address_privacy == 1)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2 || $address_privacy == 1)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                         
                        $oeducation_array[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'qualification'=> $degree,
                            'medical_affiliation' => $medical_affiliation,
                            'service' > $service,
                            'speciality'          => $speciality,
                            'clinic_name'         => $clinic_name,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => "0",
                            'followers' => "0",
                            'following' => "0",
                            'profile_views' => $profile_views,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends
                        );
                    }
            }  
             return $oeducation_array;
        }
        
        }
       
        //--------------------workplace--------------------
        if($type=="") {
        $sql   = "SELECT doctor_list.medical_affiliation,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id !='$user_id' AND doctor_list.user_id NOT IN ( '" . implode( "', '" , $array_not_in ) . "') AND (doctor_clinic.clinic_name='$oclinic_name' || doctor_clinic.address  LIKE '%$oaddress%' || doctor_clinic.city LIKE '%$ocity%' ) ORDER BY  rand() $limit";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $doctor_user_id      = $row['user_id'];
                
                $count_query = $this->db->query("SELECT * from doctor_dating_status where (user_id='$user_id' and dating_id='$doctor_user_id') OR (user_id='$doctor_user_id' and dating_id='$user_id')");
                $count_status = $count_query->num_rows();
             
                if($count_status == 0)
                {
                        $array_not_in[]=$doctor_user_id;
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $medical_affiliation = $row['medical_affiliation'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $bio                 = $row['bio'];
                        $followers           = '0';
                        $following           = "0";
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = '';
                        }
              
                       // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                       if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status   = $row['relation_ship_status'];
                        }
                        if(empty($bio))
                        {
                            $bio="";
                        }
                        else
                        {
                            $bio;
                        }
                        
                         $language_selection  = rtrim($row['language_selection'],',');
                         
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$doctor_user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                           $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                         $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2 || $address_privacy == 1)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2 || $address_privacy == 1)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2 || $address_privacy == 1)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2 || $address_privacy == 1)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2 || $address_privacy == 1)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2 || $address_privacy == 1)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2 || $address_privacy == 1)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2 || $address_privacy == 1)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2 || $address_privacy == 1)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2 || $address_privacy == 1)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2 || $address_privacy == 1)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2 || $address_privacy == 1)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2 || $address_privacy == 1)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                         
                        $oworkplace_array[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'qualification'=> $degree,
                            'medical_affiliation' => $medical_affiliation,
                            'service' > $service,
                            'speciality'          => $speciality,
                            'clinic_name'         => $clinic_name,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => "0",
                            'followers' => "0",
                            'following' => "0",
                            'profile_views' => $profile_views,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends
                        );
                    }
            }  
        }
        }
        else if($type=="workplace"){
        $sql   = "SELECT doctor_list.medical_affiliation,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id !='$user_id' AND (doctor_clinic.clinic_name='$oclinic_name' || doctor_clinic.address  LIKE '%$oaddress%' || doctor_clinic.city LIKE '%$ocity%' || doctor_clinic.state='$ostate' || doctor_clinic.pincode='$opincode' ) ORDER BY  rand() $limit";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $doctor_user_id      = $row['user_id'];
                
                $count_query = $this->db->query("SELECT * from doctor_dating_status where (user_id='$user_id' and dating_id='$doctor_user_id') OR (user_id='$doctor_user_id' and dating_id='$user_id')");
                $count_status = $count_query->num_rows();
             
                if($count_status == 0)
                {
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $medical_affiliation = $row['medical_affiliation'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $bio                 = $row['bio'];
                        $followers           = '0';
                        $following           = "0";
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = '';
                        }
              
                       // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                       if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status   = $row['relation_ship_status'];
                        }
                        if(empty($bio))
                        {
                            $bio="";
                        }
                        else
                        {
                            $bio;
                        }
                        
                         $language_selection  = rtrim($row['language_selection'],',');
                         
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$doctor_user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                           $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                         $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2 || $address_privacy == 1)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2 || $address_privacy == 1)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2 || $address_privacy == 1)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2 || $address_privacy == 1)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2 || $address_privacy == 1)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2 || $address_privacy == 1)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2 || $address_privacy == 1)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2 || $address_privacy == 1)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2 || $address_privacy == 1)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2 || $address_privacy == 1)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2 || $address_privacy == 1)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2 || $address_privacy == 1)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2 || $address_privacy == 1)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                         
                        $oworkplace_array[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'qualification'=> $degree,
                            'medical_affiliation' => $medical_affiliation,
                            'service' > $service,
                            'speciality'          => $speciality,
                            'clinic_name'         => $clinic_name,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => "0",
                            'followers' => "0",
                            'following' => "0",
                            'profile_views' => $profile_views,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends
                        );
                    }
            }  
             return $oworkplace_array;
        }
        
        }
        
        //--------------------experience--------------------
        if($type=="") {
        $sql   = "SELECT doctor_list.medical_affiliation,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id !='$user_id' AND doctor_list.user_id NOT IN ( '" . implode( "', '" , $array_not_in ) . "') AND doctor_list.experience LIKE '%$oexperience%'  ORDER BY  rand() $limit";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $doctor_user_id      = $row['user_id'];
                
                $count_query = $this->db->query("SELECT * from doctor_dating_status where (user_id='$user_id' and dating_id='$doctor_user_id') OR (user_id='$doctor_user_id' and dating_id='$user_id')");
                $count_status = $count_query->num_rows();
             
                if($count_status == 0)
                {
                        $array_not_in[]=$doctor_user_id;
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $medical_affiliation = $row['medical_affiliation'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $bio                 = $row['bio'];
                        $followers           = '0';
                        $following           = "0";
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = '';
                        }
              
                       // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                       if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status   = $row['relation_ship_status'];
                        }
                        if(empty($bio))
                        {
                            $bio="";
                        }
                        else
                        {
                            $bio;
                        }
                        
                         $language_selection  = rtrim($row['language_selection'],',');
                         
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$doctor_user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                           $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                         $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2 || $address_privacy == 1)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2 || $address_privacy == 1)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2 || $address_privacy == 1)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2 || $address_privacy == 1)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2 || $address_privacy == 1)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2 || $address_privacy == 1)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2 || $address_privacy == 1)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2 || $address_privacy == 1)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2 || $address_privacy == 1)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2 || $address_privacy == 1)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2 || $address_privacy == 1)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2 || $address_privacy == 1)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2 || $address_privacy == 1)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                         
                        $oexperience_array[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'qualification'=> $degree,
                            'medical_affiliation' => $medical_affiliation,
                            'service' > $service,
                            'speciality'          => $speciality,
                            'clinic_name'         => $clinic_name,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => "0",
                            'followers' => "0",
                            'following' => "0",
                            'profile_views' => $profile_views,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends
                        );
                    }
            }  
        }
        }
        else if($type=="experience"){
        $sql   = "SELECT doctor_list.medical_affiliation,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id !='$user_id' AND doctor_list.experience='$oexperience'  ORDER BY  rand() $limit";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $doctor_user_id      = $row['user_id'];
                
                $count_query = $this->db->query("SELECT * from doctor_dating_status where (user_id='$user_id' and dating_id='$doctor_user_id') OR (user_id='$doctor_user_id' and dating_id='$user_id')");
                $count_status = $count_query->num_rows();
             
                if($count_status == 0)
                {
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $medical_affiliation = $row['medical_affiliation'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $bio                 = $row['bio'];
                        $followers           = '0';
                        $following           = "0";
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = '';
                        }
              
                       // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                       if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status   = $row['relation_ship_status'];
                        }
                        if(empty($bio))
                        {
                            $bio="";
                        }
                        else
                        {
                            $bio;
                        }
                        
                         $language_selection  = rtrim($row['language_selection'],',');
                         
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$doctor_user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                           $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                         $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2 || $address_privacy == 1)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2 || $address_privacy == 1)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2 || $address_privacy == 1)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2 || $address_privacy == 1)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2 || $address_privacy == 1)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2 || $address_privacy == 1)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2 || $address_privacy == 1)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2 || $address_privacy == 1)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2 || $address_privacy == 1)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2 || $address_privacy == 1)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2 || $address_privacy == 1)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2 || $address_privacy == 1)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2 || $address_privacy == 1)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                         
                        $oexperience_array[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'service' > $service,
                            'qualification'=> $degree,
                            'medical_affiliation' => $medical_affiliation,
                            'speciality'          => $speciality,
                            'clinic_name'         => $clinic_name,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => "0",
                            'followers' => "0",
                            'following' => "0",
                            'profile_views' => $profile_views,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends
                        );
                    }
            }  
             return $oexperience_array;
        }
        
        }
        
        //--------------------speciality--------------------
        if($type=="") {
        $sql   = "SELECT doctor_list.medical_affiliation,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id !='$user_id' AND doctor_list.user_id NOT IN ( '" . implode( "', '" , $array_not_in ) . "') AND (doctor_list.speciality LIKE '%$ospeciality%' || doctor_list.service  LIKE '%$oservice%' ) ORDER BY  rand() $limit";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                 $doctor_user_id      = $row['user_id'];
                
                $count_query = $this->db->query("SELECT * from doctor_dating_status where (user_id='$user_id' and dating_id='$doctor_user_id') OR (user_id='$doctor_user_id' and dating_id='$user_id')");
                $count_status = $count_query->num_rows();
             
                if($count_status == 0)
                {
                        $array_not_in[]=$doctor_user_id;
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $medical_affiliation = $row['medical_affiliation'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $bio                 = $row['bio'];
                        $followers           = '0';
                        $following           = "0";
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = '';
                        }
              
                       // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                       if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status   = $row['relation_ship_status'];
                        }
                        if(empty($bio))
                        {
                            $bio="";
                        }
                        else
                        {
                            $bio;
                        }
                        
                         $language_selection  = rtrim($row['language_selection'],',');
                         
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$doctor_user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                           $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                         $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2 || $address_privacy == 1)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2 || $address_privacy == 1)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2 || $address_privacy == 1)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2 || $address_privacy == 1)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2 || $address_privacy == 1)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2 || $address_privacy == 1)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2 || $address_privacy == 1)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2 || $address_privacy == 1)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2 || $address_privacy == 1)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2 || $address_privacy == 1)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2 || $address_privacy == 1)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2 || $address_privacy == 1)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2 || $address_privacy == 1)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                         
                        $ospeciality_array[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'qualification'=> $degree,
                            'medical_affiliation' => $medical_affiliation,
                            'service' > $service,
                            'speciality'          => $speciality,
                            'clinic_name'         => $clinic_name,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => "0",
                            'followers' => "0",
                            'following' => "0",
                            'profile_views' => $profile_views,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends
                        );
                    }
            }  
        }
        }
        else if($type=="field"){
        $sql   = "SELECT doctor_list.medical_affiliation,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id !='$user_id' AND (doctor_clinic.clinic_name='$oclinic_name' || doctor_clinic.address  LIKE '%$oaddress%' || doctor_clinic.city LIKE '%$ocity%' || doctor_clinic.state='$ostate' || doctor_clinic.pincode='$opincode' ) ORDER BY  rand() $limit";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $doctor_user_id      = $row['user_id'];
                
                $count_query = $this->db->query("SELECT * from doctor_dating_status where (user_id='$user_id' and dating_id='$doctor_user_id') OR (user_id='$doctor_user_id' and dating_id='$user_id')");
                $count_status = $count_query->num_rows();
             
                if($count_status == 0)
                {
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $medical_affiliation = $row['medical_affiliation'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $bio                 = $row['bio'];
                        $followers           = '0';
                        $following           = "0";
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = '';
                        }
              
                       // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                       if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status   = $row['relation_ship_status'];
                        }
                        if(empty($bio))
                        {
                            $bio="";
                        }
                        else
                        {
                            $bio;
                        }
                        
                         $language_selection  = rtrim($row['language_selection'],',');
                         
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$doctor_user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                           $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                         $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2 || $address_privacy == 1)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2 || $address_privacy == 1)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2 || $address_privacy == 1)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2 || $address_privacy == 1)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2 || $address_privacy == 1)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2 || $address_privacy == 1)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2 || $address_privacy == 1)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2 || $address_privacy == 1)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2 || $address_privacy == 1)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2 || $address_privacy == 1)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2 || $address_privacy == 1)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2 || $address_privacy == 1)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2 || $address_privacy == 1)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                         
                        $ospeciality_array[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'qualification'=> $degree,
                            'medical_affiliation' => $medical_affiliation,
                            'service' > $service,
                            'speciality'          => $speciality,
                            'clinic_name'         => $clinic_name,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => "0",
                            'followers' => "0",
                            'following' => "0",
                            'profile_views' => $profile_views,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends
                        );
                    }
            }  
             return $ospeciality_array;
        }
        
        }
        
        //--------------------other--------------------
        if($type=="") {
        $sql   = "SELECT doctor_list.medical_affiliation,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id !='$user_id' AND doctor_list.user_id NOT IN ( '" . implode( "', '" , $array_not_in ) . "') ORDER BY  rand() $limit";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                 $doctor_user_id      = $row['user_id'];
                
                $count_query = $this->db->query("SELECT * from doctor_dating_status where (user_id='$user_id' and dating_id='$doctor_user_id') OR (user_id='$doctor_user_id' and dating_id='$user_id')");
                $count_status = $count_query->num_rows();
             
                if($count_status == 0)
                {
                        $array_not_in[]=$doctor_user_id;
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $medical_affiliation = $row['medical_affiliation'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $bio                 = $row['bio'];
                        $followers           = '0';
                        $following           = "0";
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = '';
                        }
              
                       // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                       if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status   = $row['relation_ship_status'];
                        }
                        if(empty($bio))
                        {
                            $bio="";
                        }
                        else
                        {
                            $bio;
                        }
                        
                         $language_selection  = rtrim($row['language_selection'],',');
                         
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$doctor_user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                           $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                         $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2 || $address_privacy == 1)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2 || $address_privacy == 1)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2 || $address_privacy == 1)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2 || $address_privacy == 1)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2 || $address_privacy == 1)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2 || $address_privacy == 1)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2 || $address_privacy == 1)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2 || $address_privacy == 1)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2 || $address_privacy == 1)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2 || $address_privacy == 1)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2 || $address_privacy == 1)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2 || $address_privacy == 1)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2 || $address_privacy == 1)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                         
                        $other_array[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'qualification'=> $degree,
                            'medical_affiliation' => $medical_affiliation,
                            'service' > $service,
                            'speciality'          => $speciality,
                            'clinic_name'         => $clinic_name,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => "0",
                            'followers' => "0",
                            'following' => "0",
                            'profile_views' => $profile_views,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends
                        );
                    }
            }  
        }
        }
        else if($type=="others"){
        $sql   = "SELECT doctor_list.medical_affiliation,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id !='$user_id' ORDER BY  rand() $limit";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $doctor_user_id      = $row['user_id'];
                
                $count_query = $this->db->query("SELECT * from doctor_dating_status where (user_id='$user_id' and dating_id='$doctor_user_id') OR (user_id='$doctor_user_id' and dating_id='$user_id')");
                $count_status = $count_query->num_rows();
             
                if($count_status == 0)
                {
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $medical_affiliation = $row['medical_affiliation'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $bio                 = $row['bio'];
                        $followers           = '0';
                        $following           = "0";
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = '';
                        }
              
                       // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                       if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status   = $row['relation_ship_status'];
                        }
                        if(empty($bio))
                        {
                            $bio="";
                        }
                        else
                        {
                            $bio;
                        }
                        
                         $language_selection  = rtrim($row['language_selection'],',');
                         
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$doctor_user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                           $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                         $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2 || $address_privacy == 1)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2 || $address_privacy == 1)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2 || $address_privacy == 1)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2 || $address_privacy == 1)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2 || $address_privacy == 1)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2 || $address_privacy == 1)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2 || $address_privacy == 1)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2 || $address_privacy == 1)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2 || $address_privacy == 1)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2 || $address_privacy == 1)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2 || $address_privacy == 1)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2 || $address_privacy == 1)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2 || $address_privacy == 1)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                         
                        $other_array[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'qualification'=> $degree,
                            'medical_affiliation' => $medical_affiliation,
                            'service' > $service,
                            'speciality'          => $speciality,
                            'clinic_name'         => $clinic_name,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => "0",
                            'followers' => "0",
                            'following' => "0",
                            'profile_views' => $profile_views,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends
                        );
                    }
            }  
             return $other_array;
        }
        
        }
       // $others_array[]= array();
      // $resultpost = array('others'=> $other_array);
      $resultpost[]=array('type'=>"field",
                           'field'=>$ospeciality_array );
      $resultpost[]=array('type'=>"workplace",
                           'workplace'=>$oworkplace_array );
      
      $resultpost[]=array('type'=>"education",
                           'education'=>$oeducation_array );
      $resultpost[]=array('type'=>"experience",
                           'experience'=>$oexperience_array );    
      $resultpost[]=array('type'=>"others",
                           'others'=>$other_array );  
                           
      //  $resultpost = array('field'=>$ospeciality_array,'workplace'=>$oworkplace_array,'education'=>$oeducation_array,'experience'=>$oexperience_array,'others'=> $other_array);
        
        return $resultpost;
    }
    public function doctor_friends_list($user_id)
    {
        $radius = '5';
        $resultpost = array();
      
     
        $sql   = "SELECT * from doctor_dating_status where (user_id='$user_id' OR dating_id='$user_id') AND status=3";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row1) {
                
                $doctor_user_id        = $row1['user_id'];
                $doctor_dating_id      = $row1['dating_id'];
                
                $sql2   = "SELECT * from doctor_secret_friend_list where (user_id='$user_id' OR dating_id='$doctor_dating_id') AND status=1";
                $query2 = $this->db->query($sql2);
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    $res = $query2->row_array() ;
                    $secret_like = $res['status'];
                }
                else
                {
                     $secret_like = "0";
                }
                
                if($doctor_user_id == $user_id)
                {
                    $final_id = $doctor_dating_id;
                }
                else
                {
                    $final_id = $doctor_user_id;
                }
               
               if($final_id != $user_id)
               {
                   // $sql1   = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id='" . $final_id . "' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
                       $sql1   = "SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id='" . $final_id . "'  LIMIT 0 , 20";
                      $count_query = $this->db->query($sql1);
                      $count_status = $count_query->num_rows();
                
                        
                    if($count_status > 0)
                    {
                            $row = $count_query->row_array();
                            $lat                 = $row['lat'];
                            $lng                 = $row['lng'];
                            $doctor_name         = $row['doctor_name'];
                            $email               = $row['email'];
                            $gender              = $row['gender'];
                            $doctor_phone        = $row['telephone'];
                            $dob                 = $row['dob'];
                            $category            = $row['category'];
                            $speciality          = $row['speciality'];
                            $service             = $row['service'];
                            $degree              = $row['qualification'];
                            $experience          = $row['experience'];
                            $reg_council         = $row['reg_council'];
                            $reg_number          = $row['reg_number'];
                            $doctor_user_id      = $row['user_id'];
                            $clinic_name         = $row['clinic_name'];
                            $address             = $row['address'];
                            $city                = $row['city'];
                            $state               = $row['state'];
                            $pincode             = $row['pincode'];
                            $bio                 = $row['bio'];
                            $followers           = '0';
                            $following           = "0";
                            $profile_views       = '0';
                            $total_reviews       = '0';
                            $total_rating        = '0';
                            $total_profile_views = '0';
                             $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                            $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                            $discount            = $row['discount'];
                            
                          
                            $newdob = date("d-m-Y", strtotime($dob));
                           
                            $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                            $row_rating   = $query_rating->row_array();
                            $total_rating = $row_rating['total_rating'];
                            if ($total_rating === NULL || $total_rating === '') {
                                $total_rating = '0';
                            }
                            $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                            $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                            if ($row['image'] != '') {
                                $profile_pic = $row['image'];
                                $profile_pic = str_replace(' ', '%20', $profile_pic);
                                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                            } else {
                                $profile_pic = '';
                            }
                  
                           // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                           if($row['other_name'] == NULL)
                            {
                                $other_name          = "";
                            }
                            else
                            {
                                $other_name          = $row['other_name'];
                            }
                            
                            if($row['other_name_type'] == NULL)
                            {
                                $other_name_type          = "";
                            }
                            else
                            {
                                $other_name_type     = $row['other_name_type'];
                            }
                            
                            if($row['placeses'] == NULL)
                            {
                                $placeses          = "";
                            }
                            else
                            {
                                $placeses            = $row['placeses'];
                            }
                            
                            if($row['relation_ship_status'] == NULL)
                            {
                                $relation_ship_status          = "";
                            }
                            else
                            {
                                $relation_ship_status   = $row['relation_ship_status'];
                            }
                            if(empty($bio))
                            {
                                $bio="";
                            }
                            else
                            {
                                $bio;
                            }
                            
                             $language_selection  = rtrim($row['language_selection'],',');
                             
                          $sporst_array=array();
                          $movies_array=array();
                          $tv_show_array=array();
                          $books_array=array();
                             $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$final_id'");
                             foreach($query->result_array() as $moviesrow )
                             {
                               $pic_id=$moviesrow['pic_id'];
                               $pic_name = $moviesrow['pic_name'];
                               if($moviesrow['pic'] !='')
                                {
                                   $pic = $moviesrow['pic'];
                                }
                               else{
                                     $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                                 }
                             
                             if($moviesrow['pic_type_id']=='1'){
                                 $movies_type_id="Sports";
                                 
                                 $sporst_array[]=array(
                                                        'sports_id'=>$pic_id,
                                                        'sports_name' => $pic_name,
                                                        'sports_type' => $movies_type_id,
                                                        'sports_pic' =>  $pic
                                                       );
                                 
                                 
                             }
                             if($moviesrow['pic_type_id']=='2'){
                                 $movies_type_id1="Movies";
                                  $movies_array[]=array(
                                                        'movies_id'=>$pic_id,
                                                        'movies_name' => $pic_name,
                                                        'movies_type' => $movies_type_id1,
                                                        'movies_pic' =>  $pic
                                                       );
                                 
                             }
                             
                             if($moviesrow['pic_type_id']=='3'){
                                 $movies_type_id2="TV-Shows";
                                 $tv_show_array[]=array(
                                                        'tv_shows_id' =>$pic_id, 
                                                        'tv_shows_name' => $pic_name,
                                                        'tv_shows_type' => $movies_type_id2,
                                                        'tv_shows_pic' =>  $pic
                                                       );
                             }
                              if($moviesrow['pic_type_id']=='4'){
                                 $movies_type_id3="Books";
                                 $books_array[]=array(
                                                        'books_id'=>$pic_id,
                                                        'books_name' => $pic_name,
                                                        'books_type' => $movies_type_id3,
                                                        'books_pic' =>  $pic
                                                       );
                             }
                             
                             
                             }
                             
                              $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                             //$queryss = $this->db->query($total_friends1);
                              $total_friends = $total_friends1->num_rows();
                  
                             $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                             $total_privacyss = $total_privacy->num_rows();
                             if($total_privacyss > 0)
                             {
                                 
                                $address_privacy = $total_privacy->row()->address_privacy;
                                if($address_privacy == 2)
                                {
                                    $address ="hidden";
                                    $city = "hidden";
                                    $state = "hidden";
                                    $pincode = "hidden";
                                }
                               
                                $experience_privacy = $total_privacy->row()->experience_privacy;
                                if($experience_privacy == 2)
                                {
                                    $experience ="hidden";
                                   
                                }
                                
                                $dob_privacy = $total_privacy->row()->dob_privacy;
                                if($dob_privacy == 2)
                                {
                                    $newdob ="hidden";
                                   
                                }
                                
                                $email_privacy = $total_privacy->row()->email_privacy;
                                if($email_privacy == 2)
                                {
                                    $email ="hidden";
                                   
                                }
                                
                                $bio_privacy = $total_privacy->row()->bio_privacy;
                                if($bio_privacy == 2)
                                {
                                    $bio ="hidden";
                                   
                                }
                                
                                $language_privacy = $total_privacy->row()->language_privacy;
                                if($language_privacy == 2)
                                {
                                    $language_selection ="hidden";
                                   
                                }
                                
                                $relationship_privacy = $total_privacy->row()->relationship_privacy;
                                if($relationship_privacy == 2)
                                {
                                    $relation_ship_status ="hidden";
                                   
                                }
                                
                                $work_privacy = $total_privacy->row()->work_privacy;
                                if($work_privacy == 2)
                                {
                                    $placeses ="hidden";
                                   
                                }
                                
                                $education_privacy = $total_privacy->row()->education_privacy;
                                if($education_privacy == 2)
                                {
                                    $degree ="hidden";
                                   
                                }
                                
                                $onames_privacy = $total_privacy->row()->onames_privacy;
                                if($onames_privacy == 2)
                                {
                                    $other_name ="hidden";
                                   
                                }
                                
                                $place_privacy = $total_privacy->row()->place_privacy;
                                if($place_privacy == 2)
                                {
                                    $placeses ="hidden";
                                   
                                }
                                
                                $sports_privacy = $total_privacy->row()->sports_privacy;
                                if($sports_privacy == 2)
                                {
                                    $sporst_array =array();
                                   
                                }
                                
                                $movies_privacy = $total_privacy->row()->movies_privacy;
                                if($movies_privacy == 2)
                                {
                                    $movies_array =array();
                                   
                                }
                                
                                $shows_privacy = $total_privacy->row()->shows_privacy;
                                if($shows_privacy == 2)
                                {
                                    $tv_show_array =array();
                                   
                                }
                                
                                $books_privacy = $total_privacy->row()->books_privacy;
                                if($books_privacy == 2)
                                {
                                    $books_array =array();
                                   
                                }
                                
                             }
                             
                            $resultpost[] = array(
                                'doctor_user_id' => $doctor_user_id,
                                'listing_type' => "5",
                                'lat' => $lat,
                                'lng' => $lng,
                                'doctor_name' => $doctor_name,
                                'email' => $email,
                                'gender' => $gender,
                                'doctor_phone' => $doctor_phone,
                                'dob' => $newdob,
                                'experience' => $experience,
                                'qualification'=> $degree,
                                'reg_council' => $reg_council,
                                'reg_number' => $reg_number,
                                'profile_pic' => $profile_pic,
                                'address' => $address,
                                'city' => $city,
                                'state' => $state,
                                'pincode' => $pincode,
                                'rating' => $total_rating,
                                'review' => "0",
                                'followers' => "0",
                                'following' => "0",
                                'profile_views' => $profile_views,
                                'bio'=> $bio,
                                'other_name' => $other_name,
                                'other_name_type'  => $other_name_type,
                                'placeses' => $placeses,
                                'relation_ship_status'=> $relation_ship_status,
                                'language_selection'  => $language_selection,
                                'sport_details'=>$sporst_array,
                                'movies_details'=>$movies_array,
                                'tv_show_details'=>$tv_show_array,
                                'books_details'=>$books_array,
                                'total_friend' => $total_friends,
                                 'secret_status' => $secret_like
                            );
                        }   
               }
            }  
        } else {
            $resultpost = array();
        }
        /*function array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
        {
            $sort_col = array();
            foreach ($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }
            array_multisort($sort_col, $dir, $arr);
        }
        if(!empty($resultpost)){
        array_sort_by_column($resultpost, 'distance'); }*/
        return $resultpost;
    }
    
    public function doctor_secret_friends_list($user_id)
    {
        $resultpost = array();
       
         $sql   = "SELECT * from doctor_secret_friend_list where user_id='$user_id'  AND status=1";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row1) {
                
               // $doctor_user_id        = $row1['user_id'];
                $doctor_dating_id      = $row1['dating_id'];
                
                $sql2   = "SELECT * from doctor_secret_friend_list where user_id='$doctor_dating_id' and dating_id='$user_id' AND status=1";
                $query2 = $this->db->query($sql2);
                $count2 = $query2->num_rows();
                
                if ($count2 > 0) {
                    $res = $query2->row_array() ;
                    $secret_like = $res['status'];
               
               
                    $final_id = $doctor_dating_id;
              
                 $sql1   = "SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id='$final_id'";
                  $count_query = $this->db->query($sql1);
                  $count_status = $count_query->num_rows();
             
                if($count_status > 0)
                {
                        $row = $count_query->row_array();
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $bio                 = $row['bio'];
                        $followers           = '0';
                        $following           = "0";
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                        }
              
                       // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                       if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status   = $row['relation_ship_status'];
                        }
                        if(empty($bio))
                        {
                            $bio="";
                        }
                        else
                        {
                            $bio;
                        }
                        
                         $language_selection  = rtrim($row['language_selection'],',');
                         
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                           $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                         $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                         
                        $resultpost[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'qualification'=> $degree,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => "0",
                            'followers' => "0",
                            'following' => "0",
                            'profile_views' => $profile_views,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends,
                             'secret_status' => $secret_like
                        );
                    }   
                    
                    
                }
               
                     
                    
            }  
        } else {
            $resultpost = array();
        }
       /*  function array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
       {
            $sort_col = array();
            foreach ($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }
            array_multisort($sort_col, $dir, $arr);
        }
        if(!empty($resultpost)){
        array_sort_by_column($resultpost, 'distance'); }*/
        return $resultpost;
    }
    
    public function doctor_request_list($user_id)
    {
        $resultpost = array();
       
        $sql   = "SELECT * from doctor_dating_status where dating_id='$user_id'  AND status=1";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row1) {
                $final_id      = $row1['user_id'];
                $sql1   = "SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id='$final_id'";
                $count_query = $this->db->query($sql1);
                $count_status = $count_query->num_rows();
             
                if($count_status > 0)
                {
                        $row = $count_query->row_array();
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $followers           = '0';
                        $following           = '0';
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                        }
              
                        $resultpost[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => $total_reviews,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_views' => $profile_views
                          
                            
                        );
                        
                    }
                  
            }  
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function view_doctor_profile($user_id,$view_yourself,$self_user_id)
    {
        $radius = '5';
        $resultpost = array();
        function GetDrivingDistance($lat1, $lat2, $long1, $long2)
        {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
            $ch  = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist       = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return $dist;
        }
        $sql1   = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id='" . $user_id . "'");
                  $count_query = $this->db->query($sql1);
                  $count_status = $count_query->num_rows();
                  
             
            if($count_status > 0)
            {
                        $row = $count_query->row_array();
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_profiles     = $row['image'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status            = $row['relation_ship_status'];
                        }
                        
                        if($row['bio'] == NULL)
                        {
                            $bio          = "";
                        }
                        else
                        {
                            $bio                 = $row['bio'];
                        }
                        $language_selection  = rtrim($row['language_selection'],',');
                        
                        $followers           = '0';
                        $following           = '0';
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                        $total_friends       = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                        }
                      /***********************this is movies details for doctor_fav_details tables**************************/  
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                      //echo "SELECT * FROM `doctor_fav_details` WHERE user_id='$user_id'"; 
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                            $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                         $edu_array = array();
                          $edu = $this->db->query("SELECT * from doctor_education where user_id='$doctor_user_id'");
                          $total_edu = $edu->num_rows();
                          if($total_edu > 0)
                          {
                              foreach($edu->result_array() as $educ)
                              {
                                  $edu_array[] = array('id' => $educ['id'],
                                                     'college_name' => $educ['college_name'],
                                                     'qualification' => $educ['qualification'],
                                                     'from_date' => $educ['from_date'],
                                                     'to_date' => $educ['to_date']
                                                     );
                              }
                          }
                          
                          $work_array = array();
                          $work = $this->db->query("SELECT * from doctor_work where user_id='$doctor_user_id'");
                          $total_work = $work->num_rows();
                          if($total_work > 0)
                          {
                              foreach($work->result_array() as $educ)
                              {
                                  $work_array[] = array('id' => $educ['id'],
                                                     'work_place' => $educ['work_place'],
                                                     'position' => $educ['position'],
                                                     'city' => $educ['city'],
                                                     'current_status' => $educ['current_status'],
                                                     'from_date' => $educ['from_date'],
                                                     'to_date' => $educ['to_date']
                                                     );
                              }
                          }
                    /*****************************************************************/     
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                        $area_expertise = array();
                        $healthwall_category ="";
                        $query_sp       = $this->db->query("SELECT id,doctors_type_id FROM `business_category` WHERE  FIND_IN_SET(id,'" . $category . "')");
                        $total_category = $query_sp->num_rows();
                        if ($total_category > 0) {
                            foreach ($query_sp->result_array() as $get_sp) {
                                $id               = $get_sp['id'];
                                $area_expertised  = $get_sp['doctors_type_id'];
                                
                                $query_sp1       = $this->db->query("SELECT id FROM `doctor_type` WHERE  FIND_IN_SET(id,'" . $area_expertised . "')");
                                $total_category1 = $query_sp1->num_rows();
                                if ($total_category1 > 0) {
                                       foreach ($query_sp1->result_array() as $get_sp1) {
                                       $ids               = $get_sp1['id'];
                                       if(!in_array($ids, array_column($area_expertise, 'id')))
                                       {
                                          $healthwall_category .= $ids.",";
                                      
                                        $area_expertise[] = array(
                                            'id' => $ids
                                            
                                        );
                                       }
                                }
                            }
                            }
                        } else {
                            $area_expertise = array();
                            $healthwall_category = "";
                        }
                
                         $distances    = 0; //str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                        $secret_like = "0";
                        if($view_yourself == 0) 
                        {
                            //echo "SELECT * from doctor_privacy where user_id='$doctor_user_id'";
                             $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                             $total_privacyss = $total_privacy->num_rows();
                             if($total_privacyss > 0)
                             {
                                 
                                $address_privacy = $total_privacy->row()->address_privacy;
                                if($address_privacy == 2 || $address_privacy == 1)
                                {
                                    $address ="hidden";
                                    $city = "hidden";
                                    $state = "hidden";
                                    $pincode = "hidden";
                                }
                               
                                $experience_privacy = $total_privacy->row()->experience_privacy;
                                if($experience_privacy == 2 || $experience_privacy == 1)
                                {
                                    $experience ="hidden";
                                   
                                }
                                
                                $dob_privacy = $total_privacy->row()->dob_privacy;
                                if($dob_privacy == 2 || $dob_privacy == 1)
                                {
                                    $newdob ="hidden";
                                   
                                }
                                
                                $email_privacy = $total_privacy->row()->email_privacy;
                                if($email_privacy == 2 || $email_privacy == 1)
                                {
                                    $email ="hidden";
                                   
                                }
                                
                                $bio_privacy = $total_privacy->row()->bio_privacy;
                                if($bio_privacy == 2 || $bio_privacy == 1)
                                {
                                    $bio ="hidden";
                                   
                                }
                                
                                $language_privacy = $total_privacy->row()->language_privacy;
                                if($language_privacy == 2 || $language_privacy == 1)
                                {
                                    $language_selection ="hidden";
                                   
                                }
                                
                                $relationship_privacy = $total_privacy->row()->relationship_privacy;
                                if($relationship_privacy == 2 || $relationship_privacy == 1)
                                {
                                    $relation_ship_status ="hidden";
                                   
                                }
                                
                                $work_privacy = $total_privacy->row()->work_privacy;
                                if($work_privacy == 2 || $work_privacy == 1)
                                {
                                    $work_array =array();
                                   
                                }
                                
                                $education_privacy = $total_privacy->row()->education_privacy;
                                if($education_privacy == 2 || $education_privacy == 1)
                                {
                                    $degree ="hidden";
                                    //$edu_array=array();
                                   
                                }
                                
                                $onames_privacy = $total_privacy->row()->onames_privacy;
                                if($onames_privacy == 2 || $onames_privacy == 1)
                                {
                                    $other_name ="hidden";
                                   
                                }
                                
                                $place_privacy = $total_privacy->row()->place_privacy;
                                if($place_privacy == 2 || $place_privacy == 1)
                                {
                                    $placeses ="hidden";
                                   
                                }
                                
                                $sports_privacy = $total_privacy->row()->sports_privacy;
                                if($sports_privacy == "2" || $sports_privacy == "1")
                                {
                                   // echo "hello";
                                    $sporst_array =array();
                                   
                                }
                              
                                $movies_privacy = $total_privacy->row()->movies_privacy;
                                if($movies_privacy == 2 || $movies_privacy == 1)
                                {
                                    $movies_array =array();
                                   
                                }
                                
                                $shows_privacy = $total_privacy->row()->shows_privacy;
                                if($shows_privacy == 2 || $shows_privacy == 1)
                                {
                                    $tv_show_array =array();
                                   
                                }
                                
                                $books_privacy = $total_privacy->row()->books_privacy;
                                if($books_privacy == 2 || $books_privacy == 1)
                                {
                                    $books_array =array();
                                   
                                }
                                
                             }
                             
                            $sql2   = "SELECT * from doctor_secret_friend_list where (user_id='$self_user_id' ANd dating_id='$user_id') OR (user_id='$user_id' AND dating_id='$self_user_id') AND status=1";
                            $query2 = $this->db->query($sql2);
                            $count2 = $query2->num_rows();
                            if ($count2 > 0) {
                                $res = $query2->row_array() ;
                                $secret_like = $res['status'];
                            }
                            else
                            {
                                 $secret_like = "0";
                            }
                        }
                       
                         $resultpost[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'distance' => $distances,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'qualification'=> $degree,
                            'experience' => $experience,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'healthwall_category' => $healthwall_category,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'bio' => $bio,
                            'edu_array' => $edu_array,
                            'work' => $work_array,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'rating' => $total_rating,
                            'review' => $total_reviews,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_views' => $profile_views,
                            'total_friend' => $total_friends,
                            'secret_status' => $secret_like
                            
                            
                        );
                    
            }
            else
            {
                $sql1   = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_list.hospital_doctor_id FROM doctor_list WHERE doctor_list.user_id='" . $user_id . "'");
                  $count_query = $this->db->query($sql1);
                  $count_status = $count_query->num_rows();
                  
             
            if($count_status > 0)
            {
                        $row = $count_query->row_array();
                      
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_profiles     = $row['image'];
                        $doctor_user_id      = $row['user_id'];
                        $hospital_doctor_id  = $row['hospital_doctor_id'];
                        // hospitals
                        $sql12 = "SELECT hospital_id FROM hospital_doctor_list where FIND_IN_SET($hospital_doctor_id, id)";
                        $count_query2 = $this->db->query($sql12);
                        $count_status2 = $count_query2->num_rows();
                   if($count_status2 > 0)
                     {
                        $row2 = $count_query2->row_array(); 
                        $hospital_id=$row2['hospital_id'];
                        $sql13 = "SELECT * FROM hospitals where user_id='$hospital_id'";
                        $count_query3 = $this->db->query($sql13);
                        $row3 = $count_query3->row_array(); 
                        
                        $city1                = $row3['city'];
                        $state1               = $row3['state'];
                        $sql14 = "SELECT * FROM cities where city_id='$city1'";
                        $count_query4 = $this->db->query($sql14);
                        $row4 = $count_query4->row_array(); 
                        
                        $sql15 = "SELECT * FROM states where state_id='$state1'";
                        $count_query5 = $this->db->query($sql15);
                        $row5 = $count_query5->row_array(); 
                        $lat                 = $row3['lat'];
                        $lng                 = $row3['lng'];
                        $clinic_name         = $row3['name_of_hospital'];
                        $address             = $row3['address'];
                        $city                = $row4['city_name'];
                        $state               = $row5['state_name'];
                        $pincode             = $row3['pincode'];
                        if($row['other_name'] == NULL)
                        {
                            $other_name          = "";
                        }
                        else
                        {
                            $other_name          = $row['other_name'];
                        }
                        
                        if($row['other_name_type'] == NULL)
                        {
                            $other_name_type          = "";
                        }
                        else
                        {
                            $other_name_type     = $row['other_name_type'];
                        }
                        
                        if($row['placeses'] == NULL)
                        {
                            $placeses          = "";
                        }
                        else
                        {
                            $placeses            = $row['placeses'];
                        }
                        
                        if($row['relation_ship_status'] == NULL)
                        {
                            $relation_ship_status          = "";
                        }
                        else
                        {
                            $relation_ship_status            = $row['relation_ship_status'];
                        }
                        
                        if($row['bio'] == NULL)
                        {
                            $bio          = "";
                        }
                        else
                        {
                            $bio                 = $row['bio'];
                        }
                        $language_selection  = rtrim($row['language_selection'],',');
                        
                        $followers           = '0';
                        $following           = '0';
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                        $total_friends       = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                        }
                      /***********************this is movies details for doctor_fav_details tables**************************/  
                      $sporst_array=array();
                      $movies_array=array();
                      $tv_show_array=array();
                      $books_array=array();
                      //echo "SELECT * FROM `doctor_fav_details` WHERE user_id='$user_id'"; 
                         $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$user_id'");
                         foreach($query->result_array() as $moviesrow )
                         {
                            $pic_id=$moviesrow['pic_id'];
                           $pic_name = $moviesrow['pic_name'];
                           if($moviesrow['pic'] !='')
                            {
                               $pic = $moviesrow['pic'];
                            }
                           else{
                                 $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                             }
                         
                         if($moviesrow['pic_type_id']=='1'){
                             $movies_type_id="Sports";
                             
                             $sporst_array[]=array(
                                                    'sports_id'=>$pic_id,
                                                    'sports_name' => $pic_name,
                                                    'sports_type' => $movies_type_id,
                                                    'sports_pic' =>  $pic
                                                   );
                             
                             
                         }
                         if($moviesrow['pic_type_id']=='2'){
                             $movies_type_id1="Movies";
                              $movies_array[]=array(
                                                    'movies_id'=>$pic_id,
                                                    'movies_name' => $pic_name,
                                                    'movies_type' => $movies_type_id1,
                                                    'movies_pic' =>  $pic
                                                   );
                             
                         }
                         
                         if($moviesrow['pic_type_id']=='3'){
                             $movies_type_id2="TV-Shows";
                             $tv_show_array[]=array(
                                                    'tv_shows_id' =>$pic_id, 
                                                    'tv_shows_name' => $pic_name,
                                                    'tv_shows_type' => $movies_type_id2,
                                                    'tv_shows_pic' =>  $pic
                                                   );
                         }
                          if($moviesrow['pic_type_id']=='4'){
                             $movies_type_id3="Books";
                             $books_array[]=array(
                                                    'books_id'=>$pic_id,
                                                    'books_name' => $pic_name,
                                                    'books_type' => $movies_type_id3,
                                                    'books_pic' =>  $pic
                                                   );
                         }
                         
                         
                         }
                         
                         $edu_array = array();
                          $edu = $this->db->query("SELECT * from doctor_education where user_id='$doctor_user_id'");
                          $total_edu = $edu->num_rows();
                          if($total_edu > 0)
                          {
                              foreach($edu->result_array() as $educ)
                              {
                                  $edu_array[] = array('id' => $educ['id'],
                                                     'college_name' => $educ['college_name'],
                                                     'qualification' => $educ['qualification'],
                                                     'from_date' => $educ['from_date'],
                                                     'to_date' => $educ['to_date']
                                                     );
                              }
                          }
                          
                          $work_array = array();
                          $work = $this->db->query("SELECT * from doctor_work where user_id='$doctor_user_id'");
                          $total_work = $work->num_rows();
                          if($total_work > 0)
                          {
                              foreach($work->result_array() as $educ)
                              {
                                  $work_array[] = array('id' => $educ['id'],
                                                     'work_place' => $educ['work_place'],
                                                     'position' => $educ['position'],
                                                     'city' => $educ['city'],
                                                     'current_status' => $educ['current_status'],
                                                     'from_date' => $educ['from_date'],
                                                     'to_date' => $educ['to_date']
                                                     );
                              }
                          }
                    /*****************************************************************/     
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=3");
                         //$queryss = $this->db->query($total_friends1);
                          $total_friends = $total_friends1->num_rows();
              
                        $area_expertise = array();
                        $healthwall_category ="";
                        $query_sp       = $this->db->query("SELECT id,doctors_type_id FROM `business_category` WHERE  FIND_IN_SET(id,'" . $category . "')");
                        $total_category = $query_sp->num_rows();
                        if ($total_category > 0) {
                            foreach ($query_sp->result_array() as $get_sp) {
                                $id               = $get_sp['id'];
                                $area_expertised  = $get_sp['doctors_type_id'];
                                
                                $query_sp1       = $this->db->query("SELECT id FROM `doctor_type` WHERE  FIND_IN_SET(id,'" . $area_expertised . "')");
                                $total_category1 = $query_sp1->num_rows();
                                if ($total_category1 > 0) {
                                       foreach ($query_sp1->result_array() as $get_sp1) {
                                       $ids               = $get_sp1['id'];
                                       if(!in_array($ids, array_column($area_expertise, 'id')))
                                       {
                                          $healthwall_category .= $ids.",";
                                      
                                        $area_expertise[] = array(
                                            'id' => $ids
                                            
                                        );
                                       }
                                }
                            }
                            }
                        } else {
                            $area_expertise = array();
                            $healthwall_category = "";
                        }
                
                         $distances    = 0; //str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                        $secret_like = "0";
                        if($view_yourself == 0) 
                        {
                            //echo "SELECT * from doctor_privacy where user_id='$doctor_user_id'";
                             $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                             $total_privacyss = $total_privacy->num_rows();
                             if($total_privacyss > 0)
                             {
                                 
                                $address_privacy = $total_privacy->row()->address_privacy;
                                if($address_privacy == 2 || $address_privacy == 1)
                                {
                                    $address ="hidden";
                                    $city = "hidden";
                                    $state = "hidden";
                                    $pincode = "hidden";
                                }
                               
                                $experience_privacy = $total_privacy->row()->experience_privacy;
                                if($experience_privacy == 2 || $experience_privacy == 1)
                                {
                                    $experience ="hidden";
                                   
                                }
                                
                                $dob_privacy = $total_privacy->row()->dob_privacy;
                                if($dob_privacy == 2 || $dob_privacy == 1)
                                {
                                    $newdob ="hidden";
                                   
                                }
                                
                                $email_privacy = $total_privacy->row()->email_privacy;
                                if($email_privacy == 2 || $email_privacy == 1)
                                {
                                    $email ="hidden";
                                   
                                }
                                
                                $bio_privacy = $total_privacy->row()->bio_privacy;
                                if($bio_privacy == 2 || $bio_privacy == 1)
                                {
                                    $bio ="hidden";
                                   
                                }
                                
                                $language_privacy = $total_privacy->row()->language_privacy;
                                if($language_privacy == 2 || $language_privacy == 1)
                                {
                                    $language_selection ="hidden";
                                   
                                }
                                
                                $relationship_privacy = $total_privacy->row()->relationship_privacy;
                                if($relationship_privacy == 2 || $relationship_privacy == 1)
                                {
                                    $relation_ship_status ="hidden";
                                   
                                }
                                
                                $work_privacy = $total_privacy->row()->work_privacy;
                                if($work_privacy == 2 || $work_privacy == 1)
                                {
                                    $work_array =array();
                                   
                                }
                                
                                $education_privacy = $total_privacy->row()->education_privacy;
                                if($education_privacy == 2 || $education_privacy == 1)
                                {
                                    $degree ="hidden";
                                    //$edu_array=array();
                                   
                                }
                                
                                $onames_privacy = $total_privacy->row()->onames_privacy;
                                if($onames_privacy == 2 || $onames_privacy == 1)
                                {
                                    $other_name ="hidden";
                                   
                                }
                                
                                $place_privacy = $total_privacy->row()->place_privacy;
                                if($place_privacy == 2 || $place_privacy == 1)
                                {
                                    $placeses ="hidden";
                                   
                                }
                                
                                $sports_privacy = $total_privacy->row()->sports_privacy;
                                if($sports_privacy == "2" || $sports_privacy == "1")
                                {
                                   // echo "hello";
                                    $sporst_array =array();
                                   
                                }
                              
                                $movies_privacy = $total_privacy->row()->movies_privacy;
                                if($movies_privacy == 2 || $movies_privacy == 1)
                                {
                                    $movies_array =array();
                                   
                                }
                                
                                $shows_privacy = $total_privacy->row()->shows_privacy;
                                if($shows_privacy == 2 || $shows_privacy == 1)
                                {
                                    $tv_show_array =array();
                                   
                                }
                                
                                $books_privacy = $total_privacy->row()->books_privacy;
                                if($books_privacy == 2 || $books_privacy == 1)
                                {
                                    $books_array =array();
                                   
                                }
                                
                             }
                             
                            $sql2   = "SELECT * from doctor_secret_friend_list where (user_id='$self_user_id' ANd dating_id='$user_id') OR (user_id='$user_id' AND dating_id='$self_user_id') AND status=1";
                            $query2 = $this->db->query($sql2);
                            $count2 = $query2->num_rows();
                            if ($count2 > 0) {
                                $res = $query2->row_array() ;
                                $secret_like = $res['status'];
                            }
                            else
                            {
                                 $secret_like = "0";
                            }
                        }
                       
                         $resultpost[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'distance' => $distances,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'qualification'=> $degree,
                            'experience' => $experience,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'healthwall_category' => $healthwall_category,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'bio' => $bio,
                            'edu_array' => $edu_array,
                            'work' => $work_array,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'rating' => $total_rating,
                            'review' => $total_reviews,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_views' => $profile_views,
                            'total_friend' => $total_friends,
                            'secret_status' => $secret_like
                            
                            
                        );
                     }  
            }
            }
    
        return $resultpost;
    }
    
    public function edit_doctor_profile($user_id,$doctor_name,$dob,$qualification,$experience,$reg_council,$reg_number,$address,$city,$state,$pincode,$other_name,$other_name_type,$placeses,$relation_ship_status,$language_selection,$bio,$email)
    {
       // $profile_image =""; $user_id,$doctor_name,$dob,$qualification,$experience,$reg_council,$reg_number,$address,$city,$state,$pincode
       
        $sql = $this->db->query("SELECT image FROM doctor_list WHERE user_id='$user_id'");
        if(!empty($sql))
        {
            $profile_image = $sql->row()->image;
        }
                            if (!empty($_FILES)) {
                                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                                    include('s3_config.php');
            
                                    $img_name = $_FILES['profile_image']['name'];
                                    $img_size = $_FILES['profile_image']['size'];
                                    $img_tmp = $_FILES['profile_image']['tmp_name'];
                                    $ext = getExtension($img_name);
            
                                    if (strlen($img_name) > 0) {
                                        if ($img_size < (50000 * 50000)) {
                                            if (in_array($ext, $img_format)) {
                                                $profile_image = uniqid() . date("YmdHis") . "." . $ext;
                                                $actual_image_path = 'images/healthwall_avatar/' . $profile_image;
                                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                                   // $this->db->query("update doctor_clinic set image = '$clinic_image' where id = '$clinic_id'");
                                                }
                                            }
                                        }
                                    }
                                }
                 
        if(!empty($doctor_name))
        {
            $data = array(
                        'doctor_name'    => $doctor_name
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        if(!empty($dob))
        {
            $data = array(
                         'dob'            => $dob
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        if(!empty($address))
        {
            $data = array(
                        'address'       => $address,
                        'location'       => $address,
                        'person_address' => $address
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        
        if(!empty($qualification))
        {
            $data = array(
                         'qualification'  => $qualification
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        
        
        if(!empty($experience))
        {
            $data = array(
                        'experience'     => $experience
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        
        if(!empty($reg_council))
        {
            $data = array(
                        'reg_council'    => $reg_council
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        
        if(!empty($reg_number))
        {
            $data = array(
                       'reg_number'     => $reg_number
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        
       if(!empty($profile_image))
        {
            $data = array(
                        'image'          => $profile_image,
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        if(!empty($city))
        {
            $data = array(
                          'person_city'    => $city
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        
        if(!empty($pincode))
        {
            $data = array(
                        'person_pincode' => $pincode
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        
        if(!empty($state))
        {
            $data = array(
                         'person_state'   => $state
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        if(!empty($other_name))
        {
            $data = array(
                        'other_name'   => $other_name
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        
          if(!empty($placeses))
          {
            $data = array(
                        'placeses'   => $placeses
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        
        if(!empty($other_name_type))
        {
            $data = array(
                        'other_name_type'   => $other_name_type
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        if(!empty($relation_ship_status))
        {
            $data = array(
                         'relation_ship_status'   => $relation_ship_status
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        if(!empty($language_selection))
        {
            $data = array(
                        'language_selection'   => $language_selection
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        if(!empty($bio))
        {
            $data = array(
                        'bio'   => $bio
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
        
        if(!empty($doctor_name))
        {
            $data = array(
                       'email'   => $email
                        );
            $this->db->where('user_id',$user_id);
            $this->db->update('doctor_list',$data);
        }
     
   /*$data_clinic = array('address'   =>   $address,
                        'city'           => $city,
                        'state'          => $state,
                        'pincode'        => $pincode,
                        'lat'            => $lat,
                        'lng'            => $lng
                    ); 
                    
            $this->db->where('doctor_id',$user_id);
            $this->db->update('doctor_clinic',$data_clinic);  */   
            
             return array(
                    'status' => 200,
                    'message' => 'success'
                );
        }
        
    public function doctor_privacy_profile($user_id, $field, $privacy)
    {
        $sql = $this->db->query("SELECT * FROM doctor_privacy WHERE user_id='$user_id'");
        if($sql->num_rows()>0)
        {
            if($field == "address_privacy")
            {
                $data = array(
                            'address_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
            if($field == "experience_privacy")
            {
                $data = array(
                            'experience_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
             if($field == "dob_privacy")
            {
                $data = array(
                            'dob_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
             if($field == "email_privacy")
            {
                $data = array(
                            'email_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
             if($field == "bio_privacy")
            {
                $data = array(
                            'bio_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
             if($field == "language_privacy")
            {
                $data = array(
                            'language_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
             if($field == "relationship_privacy")
            {
                $data = array(
                            'relationship_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
             if($field == "work_privacy")
            {
                $data = array(
                            'work_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
             if($field == "education_privacy")
            {
                $data = array(
                            'education_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
             if($field == "onames_privacy")
            {
                $data = array(
                            'onames_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
             if($field == "place_privacy")
            {
                $data = array(
                            'place_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
             if($field == "sports_privacy")
            {
                $data = array(
                            'sports_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
             if($field == "movies_privacy")
            {
                $data = array(
                            'movies_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
             if($field == "shows_privacy")
            {
                $data = array(
                            'shows_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
            
             if($field == "books_privacy")
            {
                $data = array(
                            'books_privacy'    => $privacy
                            );
                $this->db->where('user_id',$user_id);
                $this->db->update('doctor_privacy',$data);
            }
         
            
                 return array(
                        'status' => 200,
                        'data' => 'updated'
                    );
        }
        else
        {
            $data = array(
                            'user_id' => $user_id,
                            "$field"    => $privacy
                            );
            $this->db->insert('doctor_privacy',$data);
            
            $sql = $this->db->query("SELECT * FROM doctor_privacy WHERE user_id='$user_id'");
            if($sql->num_rows()>0)
            {
                   return array(
                        'status' => 200,
                        'data' => 'inserted'
                    );
            }
            else
            {
                 return array(
                        'status' => 201,
                        'data' => 'Failed'
                    );
            }
        }
    }
    public function doctor_privacy_profile_list($user_id)
    {
        $sql = $this->db->query("SELECT * FROM doctor_privacy WHERE user_id='$user_id'");
        if($sql->num_rows()>0)
        {
                 return array(
                        'status' => 200,
                        'data' => $sql->result_array()
                    );
        }
        else
        {
             return array(
                        'status' => 201,
                        'data' => 'Failed'
                    );
            
        }
    }        
    public function create_doctor_album($user_id, $album_name,$description,$privacy)
    {
        $query =$this->db->query("SELECT * FROM doctor_photos_album WHERE user_id='$user_id' AND album_name='$album_name'");
        
        $count = $query->num_rows();
        if ($count == 0) 
        {
            $details = array('user_id'  => $user_id,
    	                                    'album_name'                => $album_name,
    	                                    'description'              => $description,
    	                                    'privacy'                   => $privacy,
    	                                    'created_at'               => date('Y-m-d H:i:s')
        	                                    );
    	                     $this->db->insert('doctor_photos_album',$details); 
    	                     $id= $this->db->insert_id();
    	         return array(
                    'status' => 200,
                    'message' => "success",
                    'album_id' => "$id"
                );               
        }
        else
        {
              return array(
                    'status' => 201,
                    'message' => "Failed"
                );
        }
    }
    
    public function edit_doctor_album($user_id,$album_id,$album_name,$description,$privacy)
    {
        $query =$this->db->query("SELECT * FROM doctor_photos_album WHERE user_id='$user_id' AND id='$album_id'");
        
        $count = $query->num_rows();
        if ($count > 0) 
        {
                  $details = array(
    	                                    'album_name'                => $album_name,
    	                                    'description'              => $description,
    	                                    'privacy'                   => $privacy,
    	                                    'updated_at'               => date('Y-m-d H:i:s')
        	                                    );                         
        	    $this->db->where('id',$album_id);
    	        $this->db->update('doctor_photos_album',$details);                         
    	         return array(
                    'status' => 200,
                    'message' => 'success'
                );               
        }
        else
        {
              return array(
                    'status' => 201,
                    'message' => 'Failed'
                );
        }
    } 
    
    public function add_doctor_album_photos($user_id, $album_id, $story_file) {
		date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        $img = array();
        $img1 = "";
        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF", "pdf");
            include('../s3_config.php'); 
            if($story_file > 0) {
                $flag = '1';
                $video_flag = '1';
                foreach ($_FILES['doctor_images']['tmp_name'] as $key => $tmp_name) {
                    $img_name = $key . $_FILES['doctor_images']['name'][$key];
                    $img_size = $_FILES['doctor_images']['size'][$key];
                    $img_tmp = $_FILES['doctor_images']['tmp_name'][$key];
                    $ext = getExtension($img_name);
                    if (strlen($img_name) > 0) {
                            if (in_array($ext, $img_format)) {
                                $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/Doctor_images/' . $actual_image_name;
                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                    $img[]=$actual_image_name;
                                }
                            }
                          $data = array(
                        'user_id' => $user_id,
                        'album_id' => $album_id,
                        'image' => $actual_image_name,
            			'created_at' => $created_date,
            			
                        );
               
                     $this->db->insert('doctor_images', $data);
                    }
                }
                
            }
          
            return array(
                'status' => 200,
                'message' => 'success'
               
            );
      
    }
    
    public function add_doctor_photos($user_id,$story_file) {
		date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        $img = array();
        $img1 = "";
        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF", "pdf");
            include('../s3_config.php'); 
            if($story_file > 0) {
                $flag = '1';
                $video_flag = '1';
                foreach ($_FILES['doctor_images']['tmp_name'] as $key => $tmp_name) {
                    $img_name = $key . $_FILES['doctor_images']['name'][$key];
                    $img_size = $_FILES['doctor_images']['size'][$key];
                    $img_tmp = $_FILES['doctor_images']['tmp_name'][$key];
                    $ext = getExtension($img_name);
                    if (strlen($img_name) > 0) {
                            if (in_array($ext, $img_format)) {
                                $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/Doctor_images/' . $actual_image_name;
                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                    $img[]=$actual_image_name;
                                }
                            }
                          $data = array(
                        'user_id' => $user_id,
                        'image' => $actual_image_name,
            			'created_at' => $created_date,
            			
                        );
               
                     $this->db->insert('doctor_images', $data);
                    }
                }
                
            }
          
            return array(
                'status' => 200,
                'message' => 'success'
               
            );
      
    }
    
    public function doctor_photos_list($user_id)
    {
        $resultpost = array();
            $im2 =array();   
            $sql   = "SELECT * from doctor_images where user_id='$user_id' AND album_id=0";
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row_img1) {
                                    $im2[] =  array('id'=>$row_img1['id'],'image'=> "https://medicalwale.s3.amazonaws.com/images/Doctor_images/".$row_img1['image']);
                                }
                      
                 
            } 
            $resultpost = array(
                        'status' => 200,
                        'data' => $im2
                    );
         
        return $resultpost;
    }
    
      public function doctor_album_list($user_id)
    {
        $album = array();
             $sql1   = $this->db->query("SELECT * FROM doctor_photos_album WHERE user_id='$user_id'");
                $count_status = $sql1->num_rows();
                if($count_status > 0)
                {
                         foreach ($sql1->result_array() as $row) {
                          //   $row = $sql1->row_array();
                        $im1 = array();
                        $album_id                   = $row['id'];
                        $album_name                 = $row['album_name'];
                        $description                = $row['description'];
                        $created_at                 = $row['created_at'];
                        $privacy                    = $row['privacy'];
                      
                        $album[] = array(
                            'album_id'   => $album_id,
                            'album_name' => $album_name,
                            'description' => $description,
                            'privacy'    => $privacy,
                            'created_at' => $created_at
                          
                        );
                         }   
                }
          $resultpost= array( 'status' => 200,
                        'data' => $album);
          
        return $resultpost;
    }
      public function doctor_album_photos_list($user_id,$album_id)
    {
        $album = array();
   
                $sql1   = $this->db->query("SELECT * FROM doctor_photos_album WHERE user_id='$user_id' AND id='$album_id'");
                $count_status = $sql1->num_rows();
                if($count_status > 0)
                {
                        $row = $sql1->row_array();
                        $im1 = array();
                        $album_id                   = $row['id'];
                        $album_name                 = $row['album_name'];
                        $description                = $row['description'];
                        
                        $sql2   = $this->db->query("SELECT * FROM doctor_images WHERE user_id='$user_id' AND album_id='$album_id'");
                        $count_status2 = $sql2->num_rows();
                        if($count_status2 > 0)
                        {
                            foreach ($sql2->result_array() as $row_img) {
                                $im1[] =  array('id'=>$row_img['id'],'image'=> "https://medicalwale.s3.amazonaws.com/images/Doctor_images/".$row_img['image']);
                            }
                        }
                        $album = array(
                            'album_id'   => $album_id,
                            'album_name' => $album_name,
                            'description' => $description,
                            'images'=> $im1
                           
                        );
                        
                }
          
            $resultpost = array(
                        'status' => 200,
                        
                        'data' => $album
                    );
         
        return $resultpost;
    }
    public function delete_doctor_photos($user_id,$photo_id)
    {
        $pics = explode(',',$photo_id);
        if(count($pics) > 0)
        {
            for($i=0;$i<count($pics);$i++)
            {
                if(!empty($pics[$i]))
                {
                    $photo = $pics[$i];
                    $sql2   = $this->db->query("SELECT * FROM doctor_images WHERE user_id='$user_id' AND id='$photo'");
                    $count_status2 = $sql2->num_rows();
                    if($count_status2 > 0)
                    {
                        $this->db->where('user_id',$user_id);
                        $this->db->where('id',$photo);
                        $this->db->delete('doctor_images');
                    }
                }
            }
            return array(
                            'status' => 200,
                            'message' => 'success' );
        }
        else
        {
            return array(
                        'status' => 400,
                        'message' => 'failed'
                    );
        }
    }
    public function delete_doctor_album($user_id,$album_id)
    {
        $sql2   = $this->db->query("SELECT * FROM doctor_photos_album WHERE user_id='$user_id' AND id='$album_id'");
        $count_status2 = $sql2->num_rows();
                    if($count_status2 > 0)
                    {
                        $this->db->where('user_id',$user_id);
                        $this->db->where('album_id',$album_id);
                        $this->db->delete('doctor_images');
                        
                        $this->db->where('user_id',$user_id);
                        $this->db->where('id',$album_id);
                        $this->db->delete('doctor_photos_album');
                        
                        return array(
                            'status' => 200,
                            'message' => 'success' );
                    }
                    else
                    {
                        return array(
                                    'status' => 400,
                                    'message' => 'failed'
                                );
                    }
    }
/**************************add movies Pic***********************************/
 public function add_doctor_pic_movies($user_id,$fav_details,$type)
    {
        
        $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE `user_id`='$user_id' AND `pic_type_id`='$type'");
        
       $count = $query->num_rows();
        /*if ($count > 0) 
        {
             $query = $this->db->query("DELETE FROM `doctor_fav_details` WHERE `user_id`='$user_id' AND `pic_type_id`='$type'");
        }*/
        
        
         $date=date('Y-m-d H:i:s');
         $movies_details_new = json_decode($fav_details,TRUE);
          foreach($movies_details_new['pic_details'] as $details)
    	            {
    	                if(!empty($details['pic_id']) && !empty($details['pic_name']))
    	                {
    	                    $details = array('user_id'              => $user_id,
    	                                    'pic_id'                => $details['pic_id'],
    	                                    'pic_name'              => $details['pic_name'],
    	                                    'pic'                   => $details['pic'],
        	                                'pic_type_id'           => $type,
        	                                'created_by'            => $user_id,
        	                                'created_at'            => $date
        	                                    );
    	                     $this->db->insert('doctor_fav_details',$details);   
    	               }         
    	           }
    	           
    
             return array(
                    'status' => 200,
                    'message' => 'success'
                );
        
        
  }
    
    
    
    
    
    
    
    public function add_doctor_works_details($user_id,$work_place,$position,$city,$from_date,$from_to,$current_status)
    {
      
    	       $data = array('user_id'=> $user_id,
    	                         'work_place'=> $work_place,
    	                         'position'=> $position,
    	                         'city'=> $city,
        	                     'from_date'=>$from_date,
        	                     'to_date' => $from_to,
        	                     'current_status'=>$current_status);
    	         $this->db->insert('doctor_work',$data);   
                  return array(
                    'status' => 200,
                    'message' => 'success'
                );
        
        
  }
  
  
     public function add_doctor_education_details($user_id,$college_name,$qualification,$from_date,$to_date)
    {
      
    	        $data = array('user_id'=> $user_id,
    	                         'college_name'=> $college_name,
    	                         'qualification'=> $qualification,
        	                     'from_date'=>$from_date,
        	                     'to_date' =>$to_date
        	                     );
    	         $this->db->insert('doctor_education',$data);   
                  return array(
                    'status' => 200,
                    'message' => 'success'
                );
        
        
  }
    
    /*****************************************************/
        
        
        
        
        
      /*  
          $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
            $ch  = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist       = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return $dist;*/
        
    /*public function doctor_like($user_id, $following_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from follow_user where user_id='$user_id' and parent_id='$following_id'");
        $count = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `follow_user` WHERE user_id='$user_id' and parent_id='$following_id'");
            $follow_query = $this->db->query("SELECT id from follow_user where parent_id='$following_id'");
            $total_follow = $follow_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'follow' => '0',
                'total_follow' => $total_follow
            );
        } else {
            $follow_user = array(
                'user_id' => $user_id,
                'parent_id' => $following_id,
                'created_at' => $created_at,
                'deleted_at' => $created_at
            );
            $this->db->insert('follow_user', $follow_user);
            $follow_query = $this->db->query("SELECT id from follow_user where parent_id='$following_id'");
            $total_follow = $follow_query->num_rows();
                
               
                $customer_token       = $this->db->query("SELECT name,token,agent,token_status FROM users WHERE id='$following_id'");
              
                $customer_token_count = $customer_token->num_rows();
                if ($customer_token_count > 0) {
                
                    $token_status = $customer_token->row_array();
                    
                        $vendor_token       = $this->db->query("SELECT u.name,u.token,u.agent,u.token_status,m.source FROM users as u LEFT JOIN media as m ON u.avatar_id=m.id WHERE u.id='$user_id'");
                        $vendor_token_count = $vendor_token->num_rows();
                        if ($vendor_token_count > 0) {
                            $token_status1 = $vendor_token->row_array();
                            $vendor_name     = $token_status1['name'];
                        }   
                   
                    $usr_name     = $token_status['name'];
                    $agent        = $token_status['agent'];
                    $reg_id       = $token_status['token'];
                    if(!empty($token_status['source'])) {
                        $img_url      = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$token_status['source'];
                    }else
                    {
                        $img_url      = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/user_avatar.jpg';
                    }
                    $tag          = 'text';
                    $key_count    = '1';
                    $title        =  $vendor_name.' Beats your Profile';
                    $msg          =  $vendor_name.' Beats your Profile';
                    
                    
                    
                    $this->send_gcm_notify_like_user($following_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent);
                }
               $reg_id = 'eRFMZRLWvDI:APA91bGSxrXWFj7mFJqHLlVTI_zUbw7KaOpNlV-bwwAxrIhi8A9fiHvcP3roV-7ADEkFlgLHNxh-p4DWCcMPKV8wYmCLjEJWAM_nTV3S-lv28oCYzAX6VrTIkb9P7d1sZ2_qXYrZMoxU';
               
                $msg = 'You Have Received a New Prescription Order';
                $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
                $tag = 'text';
                $key_count = '2';
                $title = 'New Order';
                $agent='android';
              
             function send_gcm_notify_usr($title, $reg_id, $msg, $img_url, $tag,  $agent) {

           
            if (!defined("GOOGLE_GCM_URL"))
                define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag, "notification_type" => "doctor_confirmation" )
            );
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
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
            
            print_r($fields);
           
            echo $result;
        }
            
             function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count,$agent) {
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url,"notifivation_image" => $img_url, "tag" => $tag, "notification_type" => "doctor_confirmation")
            );
            if ($key_count == '1') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
                );
            }
            if ($key_count == '2') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
                );
            }
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
             send_gcm_notify_usr($title, $reg_id, $msg, $img_url, $tag,  $agent);
            return array(
                'status' => 201,
                'message' => 'success',
                'follow' => '1',
                'total_follow' => $total_follow,
                
            );
            
              
        }
    }*/
   public function doctor_dating_status($user_id, $following_id, $status) {
   
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
      //  echo "SELECT id from doctor_dating_status where (user_id='$user_id' and dating_id='$following_id') OR (user_id='$following_id' and dating_id='$user_id')";
        $count_query = $this->db->query("SELECT id from doctor_dating_status where (user_id='$user_id' and dating_id='$following_id') OR (user_id='$following_id' and dating_id='$user_id') ");
        $count = $count_query->num_rows();
        if ($count > 0) {
                    $follow_query = $this->db->query("UPDATE `doctor_dating_status` SET status='$status', updated_at='$created_at' WHERE (user_id='$user_id' and dating_id='$following_id') OR (user_id='$following_id' and dating_id='$user_id')");
           if($follow_query){
                return array(
                    'status' => 201,
                    'message' => 'Updated'
                );
           }
           
            if($status=='1'){
               $customer_token       = $this->db->query("SELECT name,token,agent,token_status FROM users WHERE id='$following_id'");
              
                $customer_token_count = $customer_token->num_rows();
                if ($customer_token_count > 0) {
                
                    $token_status = $customer_token->row_array();
                    
                        $vendor_token       = $this->db->query("SELECT u.name,u.token,u.agent,u.token_status,m.source FROM users as u LEFT JOIN media as m ON u.avatar_id=m.id WHERE u.id='$user_id'");
                        $vendor_token_count = $vendor_token->num_rows();
                        if ($vendor_token_count > 0) {
                            $token_status1 = $vendor_token->row_array();
                            $vendor_name     = $token_status1['name'];
                        }   
                   
                    $usr_name     = $token_status['name'];
                    $agent        = $token_status['agent'];
                    $reg_id       = $token_status['token'];
                    if(!empty($token_status['source'])) {
                        $img_url      = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$token_status['source'];
                    }else
                    {
                        $img_url      = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/user_avatar.jpg';
                    }
                    $tag          = 'text';
                    $key_count    = '1';
                    $title        =  'Friend Request';
                    $msg          =  'you got request from '.$vendor_name.' to get added in his/her circle..';
                    $noti_type='doctor_profile';
                    
                    $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $following_id ,
                      'booking_id'  => "",
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => $noti_type,
                       'notification_date'  => date('Y-m-d H:i:s')
                       );
                       
                    $this->db->insert('myactivity_notification', $notification_array);
                    
                    
                    $this->send_gcm_notify_doctor_friend_request($following_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent,$noti_type);
                }
                
            }
            
                 
            if($status=='3'){
               $customer_token       = $this->db->query("SELECT name,token,agent,token_status FROM users WHERE id='$following_id'");
              
                $customer_token_count = $customer_token->num_rows();
                if ($customer_token_count > 0) {
                
                    $token_status = $customer_token->row_array();
                    
                        $vendor_token       = $this->db->query("SELECT u.name,u.token,u.agent,u.token_status,m.source FROM users as u LEFT JOIN media as m ON u.avatar_id=m.id WHERE u.id='$user_id'");
                        $vendor_token_count = $vendor_token->num_rows();
                        if ($vendor_token_count > 0) {
                            $token_status1 = $vendor_token->row_array();
                            $vendor_name     = $token_status1['name'];
                        }   
                   
                    $usr_name     = $token_status['name'];
                    $agent        = $token_status['agent'];
                    $reg_id       = $token_status['token'];
                    if(!empty($token_status['source'])) {
                        $img_url      = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$token_status['source'];
                    }else
                    {
                        $img_url      = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/user_avatar.jpg';
                    }
                    $tag          = 'text';
                    $key_count    = '1';
                    $title        =  $vendor_name.' added you';
                    $msg          =   $vendor_name.' added you in his/her circle';
                     $noti_type='accept_doctor_profile';
                    
                    $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  =>$following_id ,
                      'booking_id'  => "",
                       'invoice_no' => "",
                       'user_id'  => $user_id ,
                       'notification_type'  => $noti_type,
                       'notification_date'  => date('Y-m-d H:i:s')
                       );
                       
                    $this->db->insert('myactivity_notification', $notification_array);
                    
                    
                    $this->send_gcm_notify_doctor_friend_request($following_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent,$noti_type);
                }
                
            }
            
            
            
        
        } else {
            $follow_user = array(
                'user_id' => $user_id,
                'dating_id' => $following_id,
                'status' => $status,
                'created_at' => $created_at
            );
            $this->db->insert('doctor_dating_status', $follow_user);
            $customer_token       = $this->db->query("SELECT name,token,agent,token_status FROM users WHERE id='$following_id'");
              
                $customer_token_count = $customer_token->num_rows();
                if ($customer_token_count > 0) {
                
                    $token_status = $customer_token->row_array();
                    
                        $vendor_token       = $this->db->query("SELECT u.name,u.token,u.agent,u.token_status,m.source FROM users as u LEFT JOIN media as m ON u.avatar_id=m.id WHERE u.id='$user_id'");
                        $vendor_token_count = $vendor_token->num_rows();
                        if ($vendor_token_count > 0) {
                            $token_status1 = $vendor_token->row_array();
                            $vendor_name     = $token_status1['name'];
                        }   
                   
                    $usr_name     = $token_status['name'];
                    $agent        = $token_status['agent'];
                    $reg_id       = $token_status['token'];
                    if(!empty($token_status['source'])) {
                        $img_url      = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$token_status['source'];
                    }else
                    {
                        $img_url      = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/user_avatar.jpg';
                    }
                    $tag          = 'text';
                    $key_count    = '1';
                    $title        =  'Friend Request';
                    $msg          =  'you got request from '.$vendor_name.' to get added in his/her circle..';
                    
                    
                    $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $user_id,
                      'booking_id'  => "",
                       'invoice_no' => "",
                       'user_id'  => $following_id,
                       'notification_type'  => 'doctor_profile',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
            );
            $noti_type='doctor_profile';
         $this->db->insert('myactivity_notification', $notification_array);
                    
                    
                    $this->send_gcm_notify_doctor_friend_request($following_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent,$noti_type);
                }
               return array(
                    'status' => 201,
                    'message' => 'Updated'
                ); 
        }
    }
    
    
/***************************doctor secret friend list*****************************************/


public function doctor_secret_status($user_id, $following_id, $status) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from doctor_secret_friend_list where user_id='$user_id' and dating_id='$following_id'");
        $count = $count_query->num_rows();
        if ($count > 0) {
            $follow_query = $this->db->query("UPDATE `doctor_secret_friend_list` SET status='$status', updated_at='$created_at' WHERE user_id='$user_id' and dating_id='$following_id'");
           if($follow_query){
                return array(
                    'status' => 201,
                    'message' => 'Updated'
                );
           }
        } else {
            $follow_user = array(
                'user_id' => $user_id,
                'dating_id' => $following_id,
                'status' => $status,
                'created_at' => $created_at
            );
            $this->db->insert('doctor_secret_friend_list', $follow_user);
            $customer_token       = $this->db->query("SELECT name,token,agent,token_status FROM users WHERE id='$following_id'");
              
                $customer_token_count = $customer_token->num_rows();
                if ($customer_token_count > 0) {
                
                    $token_status = $customer_token->row_array();
                    
                         $vendor_token       = $this->db->query("SELECT u.name,u.token,u.agent,u.token_status,m.source FROM users as u LEFT JOIN media as m ON u.avatar_id=m.id WHERE u.id='$following_id'");
                         $vendor_token_count = $vendor_token->num_rows();
                        if ($vendor_token_count > 0) {
                            $token_status1 = $vendor_token->row_array();
                            $vendor_name     = $token_status1['name'];
                        }   
                   
                    $usr_name     = $token_status['name'];
                    $agent        = $token_status['agent'];
                    $reg_id       = $token_status['token'];
                    if(!empty($token_status['source'])) {
                        $img_url      = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$token_status['source'];
                    }else
                    {
                        $img_url      = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/user_avatar.jpg';
                    }
                    $tag          = 'text';
                    $key_count    = '1';
                    $title        =  $vendor_name.' Beats your Profile';
                    $msg          =  $vendor_name.' Beats your Profile';
                    
                    
                    
                   //$this->send_gcm_notify_like_user($following_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent);
                }
               return array(
                    'status' => 201,
                    'message' => 'Inserted'
                ); 
        }
    }

/************************************end*****************************************/
    
    /*public function send_gcm_notify_dietpackage($title, $reg_id, $msg, $img_url, $tag, $agent, $diet_booking_id) {

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
            $agent === 'android' ? 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        print_r($result);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    } */
    public function send_gcm_notify_like_user($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent)
    {
       
            /*    $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $listing_id,
                      'booking_id'  => "",
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'like_doctor_profile',
                       'notification_date'  => date('Y-m-d H:i:s')+
                       
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
      
        */
        
        
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
                    'following_id' => $user_id,
                    "notification_type" => 'like_doctor_profile',
                    "notification_date" => $date
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
           
           $agent === 'android' ? 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
        );
        $ch = curl_init();
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
    
    
     public function send_gcm_notify_doctor_friend_request($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent,$noti_type)
    {
       
        
        
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
                    'following_id' => $user_id,
                    "notification_type" => $noti_type,
                    "notification_date" => $date
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
           
           $agent === 'android' ? 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
        );
        $ch = curl_init();
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
    
    
    
    
     //added by zak for all event list 
    
    public function all_event_list($user_id,$find_date)
    {
          //profile details
          
        //  $date = "2010-08-12";
          $system_date = date('Y-m-d');
      $result_array = array();
         if($find_date != '')
         {
             $d = date_parse_from_format("Y-m-d", $find_date);
            $month =  $d["month"];
             $year =  $d["year"];
         }
         else
         {
              $d = date_parse_from_format("Y-m-d", $system_date);
             $month =  $d["month"];
             $year =  $d["year"];
         }
     
          $query = $this->db->query("SELECT * FROM events order by id desc");
          $count = $query->num_rows();
          if($count>0)
          {
              foreach($query->result_array() as $row)
              {
                    $event_start_date = $row['start_date'];
                    $event_end_date = $row['end_date'];
                    $event_start_time = $row['from_time'];
                    $event_end_time = $row['to_time'];
                    $lat=$row['lat'];
                    $lang=$row['lang'];
                    $event_d = date_parse_from_format("Y-m-d", $event_start_date);
                    $event_month =  $event_d["month"];
                    $event_year =  $event_d["year"];
                  if($event_month == $month &&  $event_year == $year)
                  {
                    //  echo $event_month;
                    $id = $row['id'];
                  
                  //added for event count 
                   $interested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '1' order by id desc");
                   $interested_count = $interested_query->num_rows();
                   
                   $notinterested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '2' order by id desc");
                   $notinterested_count = $notinterested_query->num_rows();
                   
                    $maybe_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '3' order by id desc");
                    $maybe_count = $maybe_query->num_rows();
                   
                    $event_attending = $this->db->query("SELECT interest FROM event_attending_list where event_id = '$id' and user_id = '$user_id' ");
                    $event_attending_count = $event_attending->num_rows();
                    if($event_attending_count>0)
                    {
                        $event_attending_count_f = $event_attending->row()->interest;
                    }
                    else
                    {
                        $event_attending_count_f = 0;
                    }
                   
                  $image = $row['image'];
                  $event_user_id = $row['user_id'];
                  
                      $vendor_status = $this->db->select('name')->from('users')->where('id', $event_user_id)->get()->row();
                if(!empty($vendor_status))
		      {
			  $vendor_name = $vendor_status->name;
		      }
			  else
			  {
				  $vendor_name = "";
			  }
                  
                  $title = $row['title'];
                  $description = $row['description'];
                  $vendor_type = $row['vendor_id'];
                  $venue = $row['venue'];
                
                  $event_status = $row['status'];
                  if($user_id == $event_user_id)
                  {
                      $is_user = 'yes';
                  }
                  else
                  {
                      $is_user = 'no';
                  }
            
                  $result_array[] = array(
                          'id' => $id,
                          'image' => $image,
                          'vendor_name' => $vendor_name,
                          'event_user_id' => $event_user_id,
                          'title' => $title,
                          'description' => $description,
                          'vendor_type' => $vendor_type,
                          'venue' => $venue,
                          'lat' => $lat,
                          'lng' => $lang,
                          'event_start_date' => $event_start_date,
                          'event_end_date' => $event_end_date,
                          'event_start_time' => $event_start_time,
                          'event_end_time' => $event_end_time,
                          'event_status' => $event_status,
                          'is_user'  => $is_user,
                          'interested_count' => $interested_count,
                          'notinterested_count' => $notinterested_count,
                          'maybe_count'  => $maybe_count,
                          'your_interest'  => $event_attending_count_f
                      );
                  }
              }
          }
          else
          {
              $result_array = array();
          }
         
          return $result_array;
          
          
    }
    
    
    
    
    
    
    public function all_event_list_new($user_id,$status)
    {
            $system_date = date('Y-m-d');
            $result_array = array();
            $where = "";
         if($status == 'upcoming')
         {
           /*  $d = date_parse_from_format("Y-m-d", $find_date);
             $month =  $d["month"];
             $year =  $d["year"];*/
             
              $where =" WHERE end_date >= '$system_date' ";
             
             
         }
         else if($status == 'all')
         {
             /* $d = date_parse_from_format("Y-m-d", $system_date);
              $month =  $d["month"];
              $year =  $d["year"];*/
              
              $where = "";
         }
         else if($status == 'my')
         {
             $where = "WHERE user_id = '$user_id' ";
         }
         date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
         /* $sql   = "SELECT * from follow_user where user_id='$user_id'";
         $query1 = $this->db->query($sql);
          $count1 = $query1->num_rows();
         if ($count1 > 0) 
            {
                
             foreach ($query1->result_array() as $row1) 
                     {
                         $final_id        = $row1['parent_id'];
                        //echo "SELECT * FROM events WHERE user_id ='$final_id' $where  order by id desc";
                        $query = $this->db->query("SELECT * FROM events WHERE user_id ='$final_id' $where  order by id desc");
                      $count = $query->num_rows();
                      if($count>0)
                      {
                          foreach($query->result_array() as $row)
                          {
                                $event_start_date = $row['start_date'];
                                $event_end_date = $row['end_date'];
                                $event_start_time = $row['from_time'];
                                $event_end_time = $row['to_time'];
                                $lat=$row['lat'];
                                $lang=$row['lang'];
                                $event_d = date_parse_from_format("Y-m-d", $event_start_date);
                                $event_month =  $event_d["month"];
                                $event_year =  $event_d["year"];
                                $vendor_name = $row["user_name"];
                            //  if($event_month == $month &&  $event_year == $year)                  {
                                //  echo $event_month;
                                $id = $row['id'];
                              
                              //added for event count 
                               $interested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '1' order by id desc");
                               $interested_count = $interested_query->num_rows();
                               
                               $notinterested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '2' order by id desc");
                               $notinterested_count = $notinterested_query->num_rows();
                               
                                $maybe_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '3' order by id desc");
                                $maybe_count = $maybe_query->num_rows();
                               
                                $event_attending = $this->db->query("SELECT interest FROM event_attending_list where event_id = '$id' and user_id = '$user_id' ");
                                $event_attending_count = $event_attending->num_rows();
                                if($event_attending_count>0)
                                {
                                    $event_attending_count_f = $event_attending->row()->interest;
                                }
                                else
                                {
                                    $event_attending_count_f = 0;
                                }
                               
                              $image = $row['image'];
                              if($image != "")
                              {
                                  $image= "https://s3.amazonaws.com/medicalwale/images/Event_images/".$image;
                              }
                              else
                              {
                                  $image ="";
                              }
                              $event_user_id = $row['user_id'];
                              
                            $vendor_status = $this->db->select('name')->from('users')->where('id', $event_user_id)->get()->row();
                            if(!empty($vendor_status)) {
                            $user_name = $vendor_status->name;
                            }
                            else
                            {
                                $user_name = "";
                            }
                              
                              $title = $row['title'];
                              $description = $row['description'];
                              $vendor_type = $row['vendor_id'];
                              $venue = $row['venue'];
                            
                              $event_status = $row['status'];
                              if($user_id == $event_user_id)
                              {
                                  $is_user = 'yes';
                              }
                              else
                              {
                                  $is_user = 'no';
                              }
                              if(empty($vendor_name))
                              {
                                  $vendor_name="";
                              }
                              $result_array[] = array(
                                      'id' => $id,
                                      'image' => $image,
                                      'vendor_name' => $vendor_name,
                                      'user_name' => $user_name,
                                      'event_user_id' => $event_user_id,
                                      'title' => $title,
                                      'description' => $description,
                                      'vendor_type' => $vendor_type,
                                      'venue' => $venue,
                                      'lat' => $lat,
                                      'lng' => $lang,
                                      'event_start_date' => $event_start_date,
                                      'event_end_date' => $event_end_date,
                                      'event_start_time' => $event_start_time,
                                      'event_end_time' => $event_end_time,
                                      'event_status' => $event_status,
                                      'is_user'  => $is_user,
                                      'interested_count' => $interested_count,
                                      'notinterested_count' => $notinterested_count,
                                      'maybe_count'  => $maybe_count,
                                      'your_interest'  => $event_attending_count_f
                                  );
                              
                          }
          }
                      else
                      {
                          $result_array = array();
                      }
                     }
            }
            
          $ids_old='63835,64250,64251,64680';
         $ids=explode(",",$ids_old);
         for($i=0;$i < count($ids);$i++)
         {
         $query = $this->db->query("SELECT * FROM events WHERE user_id ='$ids[$i]' $where  order by id desc");
                      $count = $query->num_rows();
                      if($count>0)
                      {
                          foreach($query->result_array() as $row)
                          {
                                $event_start_date = $row['start_date'];
                                $event_end_date = $row['end_date'];
                                $event_start_time = $row['from_time'];
                                $event_end_time = $row['to_time'];
                                $lat=$row['lat'];
                                $lang=$row['lang'];
                                $event_d = date_parse_from_format("Y-m-d", $event_start_date);
                                $event_month =  $event_d["month"];
                                $event_year =  $event_d["year"];
                                $vendor_name = $event_d["user_name"];
                            //  if($event_month == $month &&  $event_year == $year)                  {
                                //  echo $event_month;
                                $id = $row['id'];
                              
                              //added for event count 
                               $interested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '1' order by id desc");
                               $interested_count = $interested_query->num_rows();
                               
                               $notinterested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '2' order by id desc");
                               $notinterested_count = $notinterested_query->num_rows();
                               
                                $maybe_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '3' order by id desc");
                                $maybe_count = $maybe_query->num_rows();
                               
                                $event_attending = $this->db->query("SELECT interest FROM event_attending_list where event_id = '$id' and user_id = '$user_id' ");
                                $event_attending_count = $event_attending->num_rows();
                                if($event_attending_count>0)
                                {
                                    $event_attending_count_f = $event_attending->row()->interest;
                                }
                                else
                                {
                                    $event_attending_count_f = 0;
                                }
                               
                              $image = $row['image'];
                              if($image != "")
                              {
                                  $image= "https://s3.amazonaws.com/medicalwale/images/Event_images/".$image;
                              }
                              else
                              {
                                  $image ="";
                              }
                              $event_user_id = $row['user_id'];
                              
                            $vendor_status = $this->db->select('name')->from('users')->where('id', $event_user_id)->get()->row();
                             if(!empty($vendor_status)) {
                            $user_name = $vendor_status->name;
                            }
                            else
                            {
                                $user_name = "";
                            }
                              
                              $title = $row['title'];
                              $description = $row['description'];
                              $vendor_type = $row['vendor_id'];
                              $venue = $row['venue'];
                            
                              $event_status = $row['status'];
                              if($user_id == $event_user_id)
                              {
                                  $is_user = 'yes';
                              }
                              else
                              {
                                  $is_user = 'no';
                              }
                        if(empty($vendor_name))
                              {
                                  $vendor_name="";
                              }
                              $result_array[] = array(
                                      'id' => $id,
                                      'image' => $image,
                                      'vendor_name' => $vendor_name,
                                      'user_name' => $user_name,
                                      'event_user_id' => $event_user_id,
                                      'title' => $title,
                                      'description' => $description,
                                      'vendor_type' => $vendor_type,
                                      'venue' => $venue,
                                      'lat' => $lat,
                                      'lng' => $lang,
                                      'event_start_date' => $event_start_date,
                                      'event_end_date' => $event_end_date,
                                      'event_start_time' => $event_start_time,
                                      'event_end_time' => $event_end_time,
                                      'event_status' => $event_status,
                                      'is_user'  => $is_user,
                                      'interested_count' => $interested_count,
                                      'notinterested_count' => $notinterested_count,
                                      'maybe_count'  => $maybe_count,
                                      'your_interest'  => $event_attending_count_f
                                  );
                              
                          }
          }
                      
         }*/
         
          $query = $this->db->query("SELECT * FROM events $where  order by id desc");
                      $count = $query->num_rows();
                      if($count>0)
                      {
                          foreach($query->result_array() as $row)
                          {
                                $event_start_date = $row['start_date'];
                                $event_end_date = $row['end_date'];
                                $event_start_time = $row['from_time'];
                                $event_end_time = $row['to_time'];
                                $lat=$row['lat'];
                                $lang=$row['lang'];
                                $event_d = date_parse_from_format("Y-m-d", $event_start_date);
                                $event_month =  $event_d["month"];
                                $event_year =  $event_d["year"];
                                $vendor_name = $row["user_name"];
                            //  if($event_month == $month &&  $event_year == $year)                  {
                                //  echo $event_month;
                                $id = $row['id'];
                              
                              //added for event count 
                               $interested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '1' order by id desc");
                               $interested_count = $interested_query->num_rows();
                               
                               $notinterested_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '2' order by id desc");
                               $notinterested_count = $notinterested_query->num_rows();
                               
                                $maybe_query = $this->db->query("SELECT id FROM event_attending_list where event_id = '$id' and interest = '3' order by id desc");
                                $maybe_count = $maybe_query->num_rows();
                               
                                $event_attending = $this->db->query("SELECT interest FROM event_attending_list where event_id = '$id' and user_id = '$user_id' ");
                                $event_attending_count = $event_attending->num_rows();
                                if($event_attending_count>0)
                                {
                                    $event_attending_count_f = $event_attending->row()->interest;
                                }
                                else
                                {
                                    $event_attending_count_f = 0;
                                }
                               
                              $image = $row['image'];
                              if($image != "")
                              {
                                  $image= "https://s3.amazonaws.com/medicalwale/images/Event_images/".$image;
                              }
                              else
                              {
                                  $image ="";
                              }
                              $event_user_id = $row['user_id'];
                              
                            $vendor_status = $this->db->select('name')->from('users')->where('id', $event_user_id)->get()->row();
                            if(!empty($vendor_status)) {
                            $user_name = $vendor_status->name;
                            }
                            else
                            {
                                $user_name = "";
                            }
                              
                              $title = $row['title'];
                              $description = $row['description'];
                              $vendor_type = $row['vendor_id'];
                              $venue = $row['venue'];
                            
                              $event_status = $row['status'];
                              if($user_id == $event_user_id)
                              {
                                  $is_user = 'yes';
                              }
                              else
                              {
                                  $is_user = 'no';
                              }
                              if(empty($vendor_name))
                              {
                                  $vendor_name="";
                              }
                              $result_array[] = array(
                                      'id' => $id,
                                      'image' => $image,
                                      'vendor_name' => $vendor_name,
                                      'user_name' => $user_name,
                                      'event_user_id' => $event_user_id,
                                      'title' => $title,
                                      'description' => $description,
                                      'vendor_type' => $vendor_type,
                                      'venue' => $venue,
                                      'lat' => $lat,
                                      'lng' => $lang,
                                      'event_start_date' => $event_start_date,
                                      'event_end_date' => $event_end_date,
                                      'event_start_time' => $event_start_time,
                                      'event_end_time' => $event_end_time,
                                      'event_status' => $event_status,
                                      'is_user'  => $is_user,
                                      'interested_count' => $interested_count,
                                      'notinterested_count' => $notinterested_count,
                                      'maybe_count'  => $maybe_count,
                                      'your_interest'  => $event_attending_count_f
                                  );
                              
                          }
          }
                      else
                      {
                          $result_array = array();
                      }
            
     
        return $result_array;
          
          
    }
    
    public function update_event_list($user_id,$event_id,$intrested_status)
    {
         $system_date = date('Y-m-d');
          $query = $this->db->query("SELECT id FROM event_attending_list where user_id = '$user_id' and event_id = '$event_id' order by id desc");
          $count = $query->num_rows();
          if($count>0)
          {
              //$query_data = $query->rows();
                  
                   $this->db->where('event_id', $event_id)->where('user_id', $user_id)->update('event_attending_list', array(
                          'event_id' => $event_id,
                          'interest' => $intrested_status
                  ));
                  
               return array(
                'status' => 200,
                'message' => 'Updated Successfully'
            );
          }
          else
          {
                $event_list = array(
                'user_id' => $user_id,
                'event_id' => $event_id,
                'interest' => $intrested_status,
                'created_at' => $system_date
            );
            $this->db->insert('event_attending_list', $event_list);
            
              return array(
                'status' => 200,
                'message' => 'Inserted Successfully'
            );
          }
          
          
    }
    
    public function add_event_details($user_id,$start_date,$end_date,$start_time,$end_time,$title,$description,$venue,$lat,$lang,$story_file) {
		date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        $img = array();
        $img1 = "";
        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF", "pdf");
            include('../s3_config.php'); 
            if($story_file > 0) {
                $flag = '1';
                $video_flag = '1';
                foreach ($_FILES['event_images']['tmp_name'] as $key => $tmp_name) {
                    $img_name = $key . $_FILES['event_images']['name'][$key];
                    $img_size = $_FILES['event_images']['size'][$key];
                    $img_tmp = $_FILES['event_images']['tmp_name'][$key];
                    $ext = getExtension($img_name);
                    if (strlen($img_name) > 0) {
                            if (in_array($ext, $img_format)) {
                                $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/Event_images/' . $actual_image_name;
                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                    $img[]=$actual_image_name;
                                }
                            }
                        
                    }
                }
                
            }
            if(!empty($img)){
                $img1=implode(",",$img);
            }
        $data = array(
            'user_id' => $user_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'from_time' => $start_time,
            'to_time' => $end_time,
            'title' => htmlspecialchars($title),
            'description' => htmlspecialchars($description),
            'venue' => htmlspecialchars($venue),
            'lat' => $lat,
            'lang' => $lang,
            'image' => $img1,
			'created' => $created_date,
			
        );
     //   'type' => $consultation_type
        $event_insert = $this->db->insert('events', $data);
        $p_id = $this->db->insert_id();
        if ($event_insert) {
            return array(
                'status' => 200,
                'message' => 'success',
                'event_id' => $p_id
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure',
                'event_id' => "",
            );
        }
    }
    
    public function edit_event_details($user_id,$event_id,$start_date,$end_date,$start_time,$end_time,$title,$description,$venue,$lat,$lang,$story_file) {
		date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        $img = array();
        $img1 = "";
        $final_image = "";
        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF", "pdf");
            include('../s3_config.php'); 
            if($story_file > 0) {
                $flag = '1';
                $video_flag = '1';
                foreach ($_FILES['event_images']['tmp_name'] as $key => $tmp_name) {
                    $img_name = $key . $_FILES['event_images']['name'][$key];
                    $img_size = $_FILES['event_images']['size'][$key];
                    $img_tmp = $_FILES['event_images']['tmp_name'][$key];
                    $ext = getExtension($img_name);
                    if (strlen($img_name) > 0) {
                            if (in_array($ext, $img_format)) {
                                $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/Event_images/' . $actual_image_name;
                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                    $img[]=$actual_image_name;
                                }
                            }
                        
                    }
                }
                
            }
            if(!empty($img)){
                $img1=implode(",",$img);
                $final_image = $img1;
            }
            
             $query = $this->db->query("SELECT image FROM events WHERE id='$event_id'");
              $count = $query->num_rows();
              if($count>0)
              {
                   $event_image = $query->row()->image;
                   if(!empty($event_image) && !empty($img1))
                   {
                       $final_image = $event_image.','.$img1;
                   }
              }
              else
              {
                  $final_image = $img1;
              }
            
            $data = array(
           
            'start_date' => $start_date,
            'end_date' => $end_date,
            'from_time' => $start_time,
            'to_time' => $end_time,
            'title' => htmlspecialchars($title),
            'description' => htmlspecialchars($description),
            'venue' => htmlspecialchars($venue),
            'lat' => $lat,
            'lang' => $lang,
            'image' => $final_image,
			'created' => $created_date,
			
        );
     //   'type' => $consultation_type
        $this->db->where('id',$event_id);
        $this->db->where('user_id',$user_id);
        $event_update = $this->db->update('events', $data);
        if ($event_update) {
            return array(
                'status' => 200,
                'message' => 'success'
                
            );
        } else {
            return array(
                'status' => 200,
                'message' => 'failure'
                
            );
        }
    }
    
    public function delete_event_image($event_id,$image_name)
    {
         $system_date = date('Y-m-d');
          $query = $this->db->query("SELECT image FROM events where id = '$event_id'");
          $count = $query->num_rows();
          if($count>0)
          {
             
                $querys = $query->row()->image;
                 if(!empty($querys)) {
                    $image_1 = explode(',',$querys);
                    $pos = array_search($image_name, $image_1);
                    unset($image_1[$pos]);
                    
                    $imagesss = implode(',',$image_1);
                    $datas = array('image'=> $imagesss);
                    $this->db->where('id',$event_id);
                    $query = $this->db->update('events',$datas);
                    if($query){
                    	 return array(
                            'status' => 200,
                            'message' => 'Deleted'
                            );
                    	}else{
                    		return array(
                            'status' => 200,
                            'message' => 'Failure'
                            );
                    	}
                 }else
                 {
                      return array(
                        'status' => 200,
                        'message' => 'Failure'
                        );
                 }
          }
          else
          {
             return array(
                        'status' => 200,
                        'message' => 'Failure'
                        );
          }
          
          
    }
    
    public function delete_event($event_id,$user_id)
    {
        
                    $this->db->where('id',$event_id);
                    $this->db->where('user_id',$user_id);
                    $query = $this->db->delete('events');
                    if($query){
                    	 return array(
                            'status' => 200,
                            'message' => 'Deleted'
                            );
                    	}else{
                    		return array(
                            'status' => 200,
                            'message' => 'Failure'
                            );
                    	}
               
          
    }
    
   
    
    public function all_share_follow_list($user_id)
    {
          $sql = $this->db->query("SELECT follow_user.user_id, follow_user.parent_id, users.vendor_id, users.map_location, users.name FROM follow_user JOIN users ON (follow_user.user_id = users.id) where follow_user.parent_id = '$user_id'");
        //  echo "SELECT follow_user.user_id, follow_user.parent_id,users.vendor_id, users.name FROM users JOIN follow_user ON (follow_user.user_id = users.id) where follow_user.parent_id = '$user_id'";
            $count = $sql->num_rows();
          if($count>0)
            {
                        foreach($sql->result_array() as $row)
                        {
                        
                          $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $row['user_id'])->get()->num_rows();
                     if ($img_count > 0) {
                            $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $row['user_id'])->get()->row();
                            $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
    
                  if($row['map_location'] == '' || $row['map_location'] == 'null' || $row['map_location'] == NULL || $row['map_location'] == null)
                        {
                            $address = '';
                        }   
                        else
                        {
                            $address = $row['map_location'];
                        }
                        
                        $vendor_id = $row['vendor_id'];
                        if($vendor_id == '0')
                        {
                           $vendor_typ = 'user';
                        }
                        else
                        {
                        
                        $sq = $this->db->query("select vendor_name from vendor_type where id = '$vendor_id'")->row();
                          $vendor_typ =   $sq->vendor_name;
                        }
                         $result_array[] = array(    
                             'follower_id' => $row['user_id'],
                            'follower_name' => $row['name'],
                            'follower_type_id' => $row['vendor_id'],
                            'follower_type' => $vendor_typ,
                            'follower_image' => $userimage,
                            'follower_address' => $address
                             );
                        }
            }
            else
            {
                $result_array = array();
            }
           return $result_array;
          
          
    }
    
    public function doctor_prescription_list_invoice($did)
    {
        $sql    = "SELECT dl.doctor_name,dl.reg_number,dl.telephone,dpt.patient_name,dpt.date_of_birth,dpt.address as p_address,dpt.city as p_city,dpt.state as p_state,dpt.pincode as p_pincode,dpt.gender as p_gender,dpt.contact_no as p_phone,dpt.email as p_email,dpt.medical_profile,dp.doctor_id as user_id,dp.id as prescription_id,dm.id as medicine_id,dm.medicine_name,dm.instruction,dm.frequency_first,dm.frequency_second,dm.frequency_third,dm.dosage,dm.dosage_unit,dm.medicine_name,dc.clinic_name,dc.address,dc.state,dc.city,dc.pincode FROM doctor_prescription as dp LEFT JOIN doctor_clinic as dc ON(dc.id=dp.clinic_id) LEFT JOIN doctor_patient as dpt ON(dpt.user_id=dp.patient_id) LEFT JOIN doctor_prescription_medicine as dm ON(dm.prescription_id=dp.id) LEFT JOIN doctor_list as dl ON(dp.doctor_id=dl.user_id) LEFT JOIN users as u ON(dl.user_id=u.id) WHERE dp.id='" . $did . "'";
        $result = $this->db->query($sql)->row();
        return $result;
    }
    
    public function get_doctor_added_medicine($prescriptionID)
    {
        $result = array();
        $sql    = "SELECT * FROM doctor_prescription_medicine  WHERE prescription_id='" . $prescriptionID . "'";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count > 0)
        {
             $result = $this->db->query($sql)->result();
        }
       
        return $result;
    }
    
    public function get_doctor_added_tests($testID)
    {
       
        $result = array();
        $sql    = "SELECT dt.test_name,dst.sub_test_name,dst.test_id,dst.id,dpt.instruction FROM doctor_subtest as dst LEFT JOIN doctor_test as dt ON dt.id=dst.test_id LEFT JOIN doctor_prescription_test as dpt ON dst.id=dpt.category  WHERE dpt.`prescription_id`='". $testID ."'";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count > 0)
        {
             $result = $this->db->query($sql)->result();
        }
       
        return $result;
    }
    
    public function add_highlights($user_id,$event_id,$description,$img1,$img1_v)
    {
        $currentDate = date("Y-m-d H:i:s");
        $user_id= $user_id;
        $des = htmlspecialchars($description);
    	$insert = $this->db->query("INSERT INTO events_highlights (event_id,user_id,description,images,videos,datetime) VALUES ('$event_id','$user_id','$des','$img1','$img1_v','$currentDate')");  
    	return array('status' => 200,'message' =>  'Success');
    }
    
    public function event_highlights($user_id,$event_id,$status)
    {
        $data = array();
        $image = array();
        $video = array();
        
        $sql1    = "SELECT user_id FROM events WHERE id='$event_id'";
        $query1 = $this->db->query($sql1);
        $event_user_id = $query1->row()->user_id;
            
        if($status =="all")
        {
            $sql    = "SELECT * FROM events_highlights WHERE (user_id='$user_id' || user_id='$event_user_id' || user_id !='$event_user_id' ) AND event_id='$event_id'";
            $query = $this->db->query($sql);
        }
        else if($status =="organizer")
        {
            $sql    = "SELECT * FROM events_highlights WHERE user_id='$event_user_id' AND event_id='$event_id'";
            $query = $this->db->query($sql);
        }
        else if($status =="visitor")
        {
            $sql    = "SELECT * FROM events_highlights WHERE user_id !='$event_user_id' AND event_id='$event_id'";
            $query = $this->db->query($sql);
        }
        foreach($query->result_array() as $row)
        {
           
            if(!empty($row['images']))
            {
                $im = explode(',',$row['images']);
                for($i=0;$i<count($im);$i++)
                {
                    $image[]= array("image"=>"https://s3.amazonaws.com/medicalwale/images/Event_images/$im[$i]");
                }
            }
            
            if(!empty($row['videos']))
            {
                $imv = explode(',',$row['videos']); 
                for($i=0;$i<count($imv);$i++)
                {            
                    $video[] = array("video"=>"https://d2c8oti4is0ms3.cloudfront.net/images/Event_images/$imv[$i]");
                }
            }
            
           
            
        }
         $data[] = array(
                            'images' => $image,
                            'videos' => $video
                            );
        return $data;
    }
    
    public function story_highlight($user_id,$story_id,$status)
    {
         $system_date = date('Y-m-d');
          $query = $this->db->query("SELECT id FROM Doctor_story where  id = '$story_id'");
          $count = $query->num_rows();
          if($count>0)
          {
              //$query_data = $query->rows();
                  
                   $this->db->where('doctor_id', $user_id)->where('id', $story_id)->update('Doctor_story', array(
                          'highlight' => $status
                  ));
                  
               return array(
                'status' => 200,
                'message' => 'Updated Successfully',
                'highlight' => "$status"
            );
          }
          else
          {
            
              return array(
                'status' => 201,
                'message' => 'Something Went Wrong.'
            );
          }
          
          
    }
    
    public function story_highlight_list($user_id)
    {
           date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
         $query = $this->db->query("SELECT * FROM Doctor_story where doctor_id='$user_id' AND highlight='1'");
         $count = $query->num_rows();
         $data = array();
         if($count>0)
         {
             foreach($query->result_array() as $row)
             {
                 $id = $row['id'];
                 $doctor_id = $row['doctor_id'];
                 $type = $row['type'];
                 $source = $row['source'];
                 $text = $row['text'];
                 $highlight = $row['highlight'];
                 $created_at = $row['created_at'];
                 
                 $hourdiff = round((strtotime($date) - strtotime($created_at)) / 3600, 1);
              
                 $data[] = array(
                         'id' => $id,
                         'doctor_id' => $doctor_id,
                         'type' => $type,
                         'source' => 'https://s3.amazonaws.com/medicalwale/images/story_images/'.$source,
                         'text' => $text,
                         'created_at' => $created_at,
                         'ago' => $hourdiff,
                         'highlight' => $highlight
                      );
                     
             }
              return $data;
              
              
         }
         return array();
       
     }
    public function print_item($pid)
    {
        
        $this->load->library('pdf');
        $pdf = $this->pdf->load();
        
        $data['prescription_list'] = $this->doctor_prescription_list_invoice($pid);
      
        
        ini_set('memory_limit', '256M');
   
        $html= '<table  cellpadding="5" >
<tr>
<td colspan="4">
<h5>'.$data['prescription_list']->clinic_name.'</h5>
<p>'.$data['prescription_list']->address.','.$data['prescription_list']->city.','.$data['prescription_list']->state.'-'.$data['prescription_list']->pincode.'</p>
</td>
<td colspan="4">
<h5>'.$data['prescription_list']->doctor_name.'</h5>
										<p>Ragistration number : '.$data['prescription_list']->reg_number.'</p>
										<h5>Contact</h5>
										<p>Phone : '.$data['prescription_list']->telephone.'</p>
</td>

</tr>

<tr>
<td colspan="8">
<hr>
<h3 class="bot">Patient Details</h3>
										<h5 class="bottom">'.$data['prescription_list']->patient_name.' <span style="font-size: 13px;">'.$data['prescription_list']->date_of_birth.' '.$data['prescription_list']->p_gender.' </span></h5>
										<h5 class="bottom">Address : <span style="font-size: 13px;">'.$data['prescription_list']->p_address.', '.$data['prescription_list']->p_city.', '.$data['prescription_list']->p_state.' - '.$data['prescription_list']->p_pincode.'</span></h5>
										<h5 class="bottom">Mobile : <span style="font-size: 13px;">'.$data['prescription_list']->p_phone.'</span>&nbsp;&nbsp;&nbsp; Email : <span style="font-size: 13px;">'.$data['prescription_list']->p_email.'</span></h5>
										<h5 class="bottom">Medical History : <span style="font-size: 13px;">'.$data['prescription_list']->medical_profile.'</span></h5>
									    <img src="https://medicalwale.com/master_assets/images/rx.png" alt="" >	
</td>

</tr>
<hr>
<tr>
<td colspan="8">
<center>Medicines</center>
</td>
</tr>

<tr >
<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">Name</td>   
<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">Instruction</td>       
 <td colspan="6" style=" border: 1px solid #dddddd;">Frequency</td>   
   <td colspan="1" style=" border: 1px solid #dddddd;">Duration (Days)</td>    
</tr>';
$medicine_list = $this->get_doctor_added_medicine($data['prescription_list']->prescription_id);

if(!empty($medicine_list))
{
                                        foreach($medicine_list as $ml){ 
                                        $html .= '<tr>
												<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">'.$ml->medicine_name.'</td>
												<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">'.$ml->instruction.'</td>
												<td colspan="6" style=" border: 1px solid #dddddd;">
												   <div class="col-md-4">';
                												    if($ml->frequency_first==='before' || $ml->frequency_first==='after'){ $html .=$ml->dosage.' '.$ml->dosage_unit; } else { $html .='0'; }
                												    $html .=' Breakfast ';
                												    if($ml->frequency_first==='before'){ $html .=' - Before Meal'; } if($ml->frequency_first==='after'){ $html .=', After Meal'; }
                												    $html .='</div><div class="col-md-4">';
                												    if($ml->frequency_second==='before' || $ml->frequency_second==='after'){ $html .=$ml->dosage.' '.$ml->dosage_unit; } else { $html .='0'; }
                												    $html .=' Lunch ';
                												    if($ml->frequency_second==='before'){ $html .=' - Before Meal'; } if($ml->frequency_second==='after'){ $html .=', After Meal'; }
                												    $html .='</div><div class="col-md-4">';
                												    if($ml->frequency_third==='before' || $ml->frequency_third==='after'){ $html .=$ml->dosage.' '.$ml->dosage_unit; } else { $html .='0'; }
                												    $html .=' Dinner ';
                												    if($ml->frequency_third==='before'){ $html .=' - Before Meal'; } if($ml->frequency_third==='after'){ $html .=', After Meal'; }
												                    $html .='
												            </div>
												       
												</td>
												<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">'.$ml->day.'</td>
											</tr>';
                                        }

}
else
{
  $html .= '<tr>
											
												<td colspan="9" style=" border: 1px solid #dddddd;">No Record Found</td>
											</tr>';  
}



$html .= '
<tr>
<td colspan="9">
<hr>
</td>
</tr>
<tr>
<td colspan="8">
<center>Tests</center>
</td>
</tr>
<tr >
<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">Name</td>   
<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">Instruction</td>       
 <td colspan="6" style=" border: 1px solid #dddddd;">Frequency</td>   
   
</tr>';

$medicine_list1 = $this->get_doctor_added_tests($data['prescription_list']->prescription_id);
if(!empty($medicine_list1))
{
 foreach($medicine_list1 as $ml){ 
                                        $html .= '<tr>
												<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">'.$ml->test_name.'</td>
												<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">'.$ml->sub_test_name.'</td>
												<td colspan="6" style=" border: 1px solid #dddddd;">'.$ml->instruction.'</td>
											</tr>';
                                        }
}
else
{
  $html .= '<tr>
											
												<td colspan="8" style=" border: 1px solid #dddddd;">No Record Found</td>
											</tr>';  
}
$html .= '
</table>
        ';
        
        
        $pdf->WriteHTML($html); // write the HTML into the PDF
    
        
         $output =$pid.'.pdf';
        // $pdfFilePath ='/home/medicalwale/public_html/vendorsandbox.medicalwale.com/doctor/prescription/'.$output;
         $pdfFilePath = '/home/h8so2sh3q97n/public_html/vendorsandbox.medicalwale.com/doctor/prescription/'.$output;
        
         $pdf->Output($pdfFilePath, 'F'); 
        
    }
    
     public function doctor_prescription_list_invoice_old($did)
    {
        $sql    = "SELECT dl.doctor_name,dl.reg_number,dl.telephone,dpt.patient_name,dpt.date_of_birth,dpt.address as p_address,dpt.city as p_city,dpt.state as p_state,dpt.pincode as p_pincode,dpt.gender as p_gender,dpt.contact_no as p_phone,dpt.email as p_email,dpt.medical_profile,dp.doctor_id as user_id,dp.id as prescription_id,dm.id as medicine_id,dm.medicine_name,dm.instruction,dm.frequency_first,dm.frequency_second,dm.frequency_third,dm.dosage,dm.dosage_unit,dm.medicine_name,dc.clinic_name,dc.address,dc.state,dc.city,dc.pincode FROM doctor_prescription as dp LEFT JOIN doctor_clinic as dc ON(dc.id=dp.clinic_id) LEFT JOIN doctor_patient as dpt ON(dpt.user_id=dp.patient_id) LEFT JOIN doctor_prescription_medicine as dm ON(dm.prescription_id=dp.id) LEFT JOIN doctor_list as dl ON(dp.doctor_id=dl.user_id) LEFT JOIN users as u ON(dl.user_id=u.id) WHERE dp.id='" . $did . "'";
        $result = $this->db->query($sql)->row();
        return $result;
    }
     public function print_prescription($pid)
    {
        
        $this->load->library('pdf_dom');
        
        
        $data['prescription_list'] = $this->doctor_prescription_list_invoice_old($pid);
      //print_r($data['prescription_list']);
   $count= count($data['prescription_list']);    
        ini_set('memory_limit', '256M');
   if($count > 0){
        $html= '<table  cellpadding="5" >
<tr>
<td colspan="4">
<h5>'.$data['prescription_list']->clinic_name.'</h5>
<p>'.$data['prescription_list']->address.','.$data['prescription_list']->city.','.$data['prescription_list']->state.'-'.$data['prescription_list']->pincode.'</p>
</td>
<td colspan="4">
<h5>'.$data['prescription_list']->doctor_name.'</h5>
										<p>Ragistration number : '.$data['prescription_list']->reg_number.'</p>
										<h5>Contact</h5>
										<p>Phone : '.$data['prescription_list']->telephone.'</p>
</td>

</tr>

<tr>
<td colspan="8">
<hr>
<h3 class="bot">Patient Details</h3>
										<h5 class="bottom">'.$data['prescription_list']->patient_name.' <span style="font-size: 13px;">'.$data['prescription_list']->date_of_birth.' '.$data['prescription_list']->p_gender.' </span></h5>
										<h5 class="bottom">Address : <span style="font-size: 13px;">'.$data['prescription_list']->p_address.', '.$data['prescription_list']->p_city.', '.$data['prescription_list']->p_state.' - '.$data['prescription_list']->p_pincode.'</span></h5>
										<h5 class="bottom">Mobile : <span style="font-size: 13px;">'.$data['prescription_list']->p_phone.'</span>&nbsp;&nbsp;&nbsp; Email : <span style="font-size: 13px;">'.$data['prescription_list']->p_email.'</span></h5>
										<h5 class="bottom">Medical History : <span style="font-size: 13px;">'.$data['prescription_list']->medical_profile.'</span></h5>
									    <img src="https://medicalwale.com/master_assets/images/rx.png" alt="" style="height:20px;width:20px">	
</td>

</tr>
<hr>
<tr>
<td colspan="8">
<center>Medicines</center>
</td>
</tr>

<tr >
<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">Name</td>   
<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">Instruction</td>       
 <td colspan="6" style=" border: 1px solid #dddddd;">Frequency</td>   
   <td colspan="1" style=" border: 1px solid #dddddd;">Duration (Days)</td>    
</tr>';
$medicine_list = $this->get_doctor_added_medicine($data['prescription_list']->prescription_id);

if(!empty($medicine_list))
{
                                        foreach($medicine_list as $ml){ 
                                        $html .= '<tr>
												<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">'.$ml->medicine_name.'</td>
												<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">'.$ml->instruction.'</td>
												<td colspan="6" style=" border: 1px solid #dddddd;">
												   <div class="col-md-4">';
                												    if($ml->frequency_first==='before' || $ml->frequency_first==='after'){ $html .=$ml->dosage.' '.$ml->dosage_unit; } else { $html .='0'; }
                												    $html .=' Breakfast ';
                												    if($ml->frequency_first==='before'){ $html .=' - Before Meal'; } if($ml->frequency_first==='after'){ $html .=', After Meal'; }
                												    $html .='</div><div class="col-md-4">';
                												    if($ml->frequency_second==='before' || $ml->frequency_second==='after'){ $html .=$ml->dosage.' '.$ml->dosage_unit; } else { $html .='0'; }
                												    $html .=' Lunch ';
                												    if($ml->frequency_second==='before'){ $html .=' - Before Meal'; } if($ml->frequency_second==='after'){ $html .=', After Meal'; }
                												    $html .='</div><div class="col-md-4">';
                												    if($ml->frequency_third==='before' || $ml->frequency_third==='after'){ $html .=$ml->dosage.' '.$ml->dosage_unit; } else { $html .='0'; }
                												    $html .=' Dinner ';
                												    if($ml->frequency_third==='before'){ $html .=' - Before Meal'; } if($ml->frequency_third==='after'){ $html .=', After Meal'; }
												                    $html .='
												            </div>
												       
												</td>
												<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">'.$ml->day.'</td>
											</tr>';
                                        }

}
else
{
  $html .= '<tr>
											
												<td colspan="9" style=" border: 1px solid #dddddd;">No Record Found</td>
											</tr>';  
}



$html .= '
<tr>
<td colspan="9">
<hr>
</td>
</tr>
<tr>
<td colspan="8">
<center>Tests</center>
</td>
</tr>
<tr >
<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">Name</td>   
<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">Instruction</td>       
 <td colspan="6" style=" border: 1px solid #dddddd;">Frequency</td>   
   
</tr>';

$medicine_list1 = $this->get_doctor_added_tests($data['prescription_list']->prescription_id);
if(!empty($medicine_list1))
{
 foreach($medicine_list1 as $ml){ 
                                        $html .= '<tr>
												<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">'.$ml->test_name.'</td>
												<td colspan="1" style=" border: 1px solid #dddddd;border-right:none;">'.$ml->sub_test_name.'</td>
												<td colspan="6" style=" border: 1px solid #dddddd;">'.$ml->instruction.'</td>
											</tr>';
                                        }
}
else
{
  $html .= '<tr>
											
												<td colspan="8" style=" border: 1px solid #dddddd;">No Record Found</td>
											</tr>';  
}
$html .= '
</table>
        ';
        
        
       /* $pdf->WriteHTML($html); */// write the HTML into the PDF
    
        
         $output =$pid.'.pdf';
           $this->pdf_dom->set_paper("A4", "portrait");
    		            $this->pdf_dom->set_option('isHtml5ParserEnabled', TRUE);
    		            $this->pdf_dom->load_html($html);
    		            $this->pdf_dom->render();
                        $pdfname = $pid.".pdf";
        	            $output = $this->pdf_dom->output();
        	            include('s3_config.php');
        	            //$file_path = 'images/doctor_prescription_sandbox/' . $pdfname;
        	            $file_path = 'images/doctor_prescription_live/' . $pdfname;
        	            $s3->putPdfFile($output, $bucket, $file_path, S3::ACL_PUBLIC_READ);
                   
         
          $resultresponse=array("status"=> 200,
        "message"=> "success");
       
       return $resultresponse;
         
   }else{
       
       $resultresponse=array("status"=> 201,
        "message"=> "failed");
       
       return $resultresponse;
       
   }
        
    }
    
        public function print_prescriptionnew($pid)
    { 
       
        $this->load->library('pdf');
        $pdf = $this->pdf->load();
        
       $data['prescription_list'] = $this->doctor_prescription_list_invoice($pid);
       $dischargedata = $this->patient_info_prescription_opd($id);
       
       $symptoms= $this->symptoms(0);
        
      //  $medicine = $this->select_manage_medicine_detail();
      print_r($data['prescription_list']);
     
   $count= count($dischargedata);    
       
   if($count > 0){
        $html='<body class="text-capitalize">
	<div id="preloader">
		<div id="status">&nbsp;</div>
	</div>
	<!--TOP SEARCH SECTION-->
	<section>
		<div class="tz tz-invo-full" id="targetz">
			<!--CENTER SECTION-->
			<div class="tz-2 tz-invo-full1" >
				<div class="tz-2-com tz-2-main" id="target">
					<div class="db-list-com tz-db-table">
				        <div class="row" style="position: fixed;z-index: 9999;top: -4px;width: 100%;">
                    	    <div class="col-md-2">
                    	        <div id="google_translate_element" style="background: white;"></div>
                    	    </div>
                    	    
                    	</div>
						<div class="invoice">
						    	   
							<div class="invoice-1" id="'.$dischargedata["details_new"]["booking_id"].'">
							     <div id="background1">
  
                            </div>';	                    
                            
				    	    if($dischargedata['hospital_details']['is_header']=='0'){
				    	        $css = 'display:none;';
				    	        $css1 = 'display:block;';
				    	    }
				    	    else{
				    	        $css = 'display:block;';
				    	        $css1 = 'display:none;';
				    	    }
				    	  
	                            
	                      $html.='<div class="col-md-12 col-lg-12" style="margin-bottom: 10px;float:left;width:100%;border-bottom: 1px solid #2f2f2f;<?php echo $css; ?>">
									<div class="col-lg-2 col-md-2" style="margin-bottom: 0;" >
									    <!--<p></p>-->
								
										<p><img class="imgh" src="https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$dischargedata['hospital_details']['image'].'" style="width: 100%;height: 98px;object-fit: contain;"></p>
								    </div>
								    <div class="col-lg-10 col-md-10" style="margin-bottom: 0;" >
								        <p><b style="font-size:17px"> '.$this->session->userdata('uname').'</b></p>
										<p><b>Phone :</b> '.$dischargedata['hospital_details']['phone'].' </p>
										<p><b>Address :</b> '. $dischargedata['hospital_details']['address'].'</p>

									</div>
									
								</div>
				    	        <div class="col-md-12 col-lg-12" style="margin-bottom: 10px;float:left;width:100%;height: 166px;<?php echo $css1; ?>">
									
									
								</div>

			
								<div class="invoice-1-add-left col-lg-12 col-md-12" style="padding-top: 10px;margin-bottom: 0px;display:none">
								<p style="letter-spacing: 2px;color: #4a4747;font-weight: 600;text-align: center; font-size: initial">OPD Prescription</p>
								</div>
								<div class="invoice-1-add-left-no-margin col-lg-12 col-md-12" style="padding-top: 0px;margin-bottom: -37px;">
										<div class="invoice-1-add-left col-lg-6 col-md-6" >
									
										<p><b>Patient Name :</b> <span style="font-size: 13px;"> '.$dischargedata['details']['name'].'</span></p>
										<p><b>Age :</b> <span style="font-size: 13px;">'.$dischargedata['details']['age'].'</span></p>
										<p><b>Consulting Doctor :</b> <span style="font-size: 13px;">'.$dischargedata['details']['doctor_name'].'</span></p>
													
										</div>
										<div class="invoice-1-add-left col-lg-6 col-md-6" >
										
										<p><b>Sex :</b> <span style="font-size: 13px;"> '.$dischargedata['details']['sex'].'</span></p>
										<p><b>Booking Id :</b> <span style="font-size: 13px;">'.$dischargedata['details_new']['booking_id'].'</span></p>
										<p><b>Booking Date :</b> <span style="font-size: 13px;">'.$d2.'</span></p>
										<p><b>Booking time :</b> <span style="font-size: 13px;">'. $dischargedata['details']['booking_time'].'</span></p>
										</div>
								</div>
								
								
									<div class="invoice-1-add-left col-lg-12 col-md-12" style="padding-top: 10px;margin-bottom: 10px;display:none">
										
									<img src="https://medicalwale.com/master_assets/images/rx.png" alt="" style="height: 40px;margin-top: 15px;">		
									
								</div> ';
					
								
								 if(!empty($dischargedata['details']['complaints'])) { 
									$html.='<div class="row">
								    <div class="col-md-12">
								        
								        <div class="invoice-1-add-left tableRepeat"  style="margin-bottom: 10px;">
									<table class="">
											<thead>
											<tr>
												<th style="width:20%;border: 1px solid #2f2f2f;">
												    <center><b>Chief Complaint</b></center>
												</th>
												<td style="width:80%;text-align:center;border: 1px solid #2f2f2f;" colspan="3">
												     '.$dischargedata['details']['complaints'].'
												</td>
												</tr>
										</thead>
										
									</table>	
									<style>
									.bord { border-right: 1px solid; }
									    @media (max-width: 768px)
                                        { 
                                             .bord { 
                                                  border-right: none;
                                                 border-bottom: 1px solid; }
                                        }
									    
									</style>
								</div>
								    </div>
								</div>';
								 } 
								
								$html.='<div class="row">
								    <div class="col-md-12" style="margin-top: 5px;margin-bottom: -34px;">
                                       <div class="invoice-1-add-left tableRepeat">     
                                       <table >
                                            <tbody>
                                            <tr style="border: 1px solid #2f2f2f;text-align:center;"  >
        									    <!--<div class="invoice-1-add-left col-lg-12 col-md-12" style="border-top: 1px solid #2f2f2f;padding-top: 10px;margin-bottom: 10px;">-->
        									
        										<td style="padding: 5px;width:20%;border: 1px solid #2f2f2f;"><p><b>Medical History</b> </p></td>
        										
                                            <!--</div>-->
                                            </tr>
                                            <tr style="border: 1px solid #2f2f2f;text-align:center;" >
                                            	<!--<div class="invoice-1-add-left col-lg-12 col-md-12" style="border-top: 1px solid #2f2f2f;padding-top: 10px;">-->
                                <td style="padding: 5px;width:20%;border: 1px solid #2f2f2f;">';
                                    
                                if($dischargedata['details']['symptoms']!=''){
        					    $html.='<p class="bottom1" style="margin-bottom: 0px;padding: 1px 10px 0px 2px;"><b>Symptoms  :</b>
        					        <span style="font-size: 9px;position: relative;padding: 1px 10px 0px 0px;">'.
        					            
                                            $sy = explode(',',$dischargedata['details']['symptoms']);
                                            $s = '';
                                            foreach($symptoms as $symptoms){
                                                
                                                if(in_array($symptoms['id'],$sy)){ 
                                                    $s .=  $symptoms['category'].', '; 
                                                }
                                            
                                            } 
                                            echo $s.'
                                       
    					            </span>
					            </p>';
                                }
                                    
        						 if($dischargedata['details_new']['prescription_note']!=''){
        						$html.='<p class="bottom1" style="margin-bottom: 0px;padding: 7px 0px;"><b>Prescription Note :</b> 
            						<span style="font-size: 9px;position: absolute;padding: 1px 4px;">
            						    '.$dischargedata['details_new']['prescription_note'].'
        						    </span>
    						    </p>';
    						     }
                                
                                 if($dischargedata['details']['diagnosis']!=''){
								$html.='<p class="bottom1" style="margin-bottom: 0px;padding: 7px 0px;"><b>Diagnosis :</b> 
								    <span style="font-size: 9px;position: absolute;padding: 1px 10px 0px 2px;">
							           
                                       '. $d = explode(',',$dischargedata['details']['diagnosis']);
                                        $b = '';
                                        foreach($d as $dd){
                                            $d = $this->Doc_ipd_model->selectExistingDailyReport($dd);
                                           $b .= $d['description'].',';
                                        }
                                        echo rtrim($b,',')
									
									.'</span>
								</p>';
								 }
								
								 if($dischargedata['details']['investigation']!=''){
							   $html.=' <p class="bottom1"  style="margin-bottom: 0px;padding: 7px 0px;"><b>Investigation :</b> 
							        <span style="font-size: 9px;position: absolute;padding: 1px 10px 0px 2px;">';
							        $dischargedata['details']['investigation'];
									
                                        $d1 = explode(',',$dischargedata['details']['investigation']);
                                        $b1 = '';
                                        foreach($d1 as $dd){
                                            $d1 = $this->Doc_ipd_model->selectExistingDailyReport($dd);
                                           $b1 .= $d1['description'].',';
                                        }
                                         rtrim($b1,',');
                                        // changes by ghanshyam parihar ends
        								
    							$html.='	</span>
							    </p>';
							     } 
        									
        					if($dischargedata['details']['advised']!=''){			
							$html.='		<p class="bottom1" style="margin-bottom: 0px;padding: 7px 0px;"><b>Treatment Advised :</b> 
								    <span style="font-size: 9px;position: absolute;padding: 1px 10px 0px 2px;">';
                                        $d3 = explode(',',$dischargedata['details']['advised']);
                                        $b3 = '';
                                        foreach($d3 as $dd){
                                            $d3 = $this->Doc_ipd_model->selectExistingDailyReport($dd);
                                           $b3 .= $d3['description'].',';
                                        }
                                        echo rtrim($b3,',');
                                        // changes by ghanshyam parihar ends
										
								$html='</span>
								</p>';	
								} 
								
							 if($dischargedata['details']['treatment_given']!=''){
        					  $html.='	  <p class="bottom1" style="margin-bottom: 0px;padding: 7px 0px;"><b>Treatment Given :</b>
        					        <span style="font-size: 9px;position: absolute;padding: 1px 4px;">';
        					            $dischargedata['details']['treatment_given'];
    					           $html.=' </span>
					            </p>';
					           } 
        								
        							
        							$html.='		</td>
        								    </tr>
        								    </tbody>
    								    </table>
							    </div>
							    </div>
							    </div>';
								
							 if(($dischargedata['details']['vital_bp'])>0 ||
								($dischargedata['details']['vital_temperature'])>0 || 
								($dischargedata['details']['vital_pulse'])>0 ||
								($dischargedata['details']['vital_respiratory'])>0 ||
								($dischargedata['details']['vital_weight'])>0 ||
								($dischargedata['details']['vital_height'])>0)
								{
								
							$html.='		<div class="invoice-1-add-left col-lg-12 col-md-12" style="padding-top: 8px;margin-bottom: -8px;text-align:center">
								    <b style="letter-spacing: 2px;color: #4a4747;font-weight: 100;text-align: center; font-size: 17px">Vital Parameters</b>
								</div>
								<div class="row">
								    <div class="col-md-12">
								        
								        <div class="invoice-1-add-left tableRepeat" style="margin-bottom: 10px;">
									<table class="">
									<caption style="letter-spacing: 2px;color: #4a4747;font-weight: 600;text-align: center; font-size: initial;"></caption>
										<thead>
											<tr>
											    <th style="width:16%;border: 1px solid #2f2f2f;"><center><b>BP</b></center></th>
												<th style="width:16%;border: 1px solid #2f2f2f;"><center><b>Temperature(&#8451;)</b></center></th>
												<th style="width:16%;border: 1px solid #2f2f2f;"><center><b>Pulse</b></center></th>
												<th style="width:16%;border: 1px solid #2f2f2f;"><center><b>Respiratory Rate(&#37;)</b></center></th>
												<th style="width:16%;border: 1px solid #2f2f2f;"><center><b>Weight(Kgs)</b></center></th>
												<th style="width:16%;border: 1px solid #2f2f2f;"><center><b>Height(cms)</b></center></th>
												
												</tr>
										</thead>
										<tbody>
										   											<tr>
											    <td style="width:10%;text-align:center;border: 1px solid #2f2f2f;">'.$dischargedata['details']['vital_bp'].'</td>
												<td style="width:20%;border: 1px solid #2f2f2f;text-align:center;padding:2px;">'. $dischargedata['details']['vital_temperature'].'</td>
												<td style="width:8%;border: 1px solid #2f2f2f;padding:2px;text-align:center;">'. $dischargedata['details']['vital_pulse'].'</td>
												<td style="width:8%;border: 1px solid #2f2f2f;padding:2px;text-align:center;">'. $dischargedata['details']['vital_respiratory'].'</td>
												<td style="width:20%;border: 1px solid #2f2f2f;padding:2px;text-align:center;"> '. $dischargedata['details']['vital_weight'].'</td>
												<td style="width:20%;border: 1px solid #2f2f2f;padding:2px;text-align:center;">'. $dischargedata['details']['vital_height'].'</td>
											
											</tr>
									
										</tbody>
									</table>	
									<style>
									.bord { border-right: 1px solid; }
									    @media (max-width: 768px)
                                        { 
                                             .bord { 
                                                  border-right: none;
                                                 border-bottom: 1px solid; }
                                        }
									    
									</style>
								</div>
								    </div>
								</div>';
								
								 }
								
							
								 if(!empty($dischargedata['medicine'])) {
							$html.='	<div class="invoice-1-add-left col-lg-12 col-md-12" style="padding-top: 8px;margin-bottom: -7px;text-align:center">
									
										<b style="letter-spacing: 2px;color: #4a4747;font-weight: 100;text-align: center; font-size: 17px;">Medicine</b>
										
                                    </div>
								<div class="row">
								    <div class="col-md-12">
								        
								        <div class="invoice-1-add-left tableRepeat" style="margin-bottom: 10px;">
									<table class="">
									<caption style="letter-spacing: 2px;color: #4a4747;font-weight: 600;text-align: center; font-size: initial;"></caption>
										<thead>
											<tr>
											    <th style="width:4%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Sr.</b></center></th>
												<th style="width:22%;border: 1px solid #2f2f2f;"><center><b>Name</b></center></th>
												<th style="width:4%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Dosage</b></center></th>
												<th style="width:1%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Unit</b></center></th>
												<th style="width:15%;border: 1px solid #2f2f2f;"><center><b>Morning</b></center></th>
												<!--<th style="width:4%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Frequency</b></center></th>-->
												<th style="width:13%;border: 1px solid #2f2f2f;"><center><b>Afternoon</b></center></th>
												<!--<th style="width:4%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Frequency</b></center></th>-->
												<th style="width:13%;border: 1px solid #2f2f2f;"><center><b>Evening</b></center></th>
												<th style="width:4%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Frequency</b></center></th>
												<th style="width:8%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">No. of Days</b></center></th>
												</tr>
										</thead>
										<tbody>';
										   
										   $count=1;
										   foreach($dischargedata['medicine'] as $a){
											$html.='	<tr>
											    <td style="width:4%;border: 1px solid #2f2f2f;"><center style="padding:2px;"> '.$count.'</center></td>
												<td style="width:20%;border: 1px solid #2f2f2f;"><center> '.$a['medicine_name'].'</center></td>
												<td style="width:4%;border: 1px solid #2f2f2f;"><center style="padding:2px;">'. $a['dosage'].'</center></td>
												<td style="width:1%;border: 1px solid #2f2f2f;"><center style="padding:2px;">'. $a['dosage_unit'].'</center></td>
												<td style="width:15%;border: 1px solid #2f2f2f;"><center>'. $a['frequency_first'].'</center> </td>
												
												<td style="width:13%;border: 1px solid #2f2f2f;"><center>'. $a['frequency_second'].'</center></td>
											
												<td style="width:13%;border: 1px solid #2f2f2f;"><center>'.$a['frequency_third'].'</center></td>
												<td style="width:4%;border: 1px solid #2f2f2f;"><center style="padding:2px;">'. $a['frequency'].'</center></td>
												<td style="width:8%;border: 1px solid #2f2f2f;"><center style="padding:2px;">'. $a['day'].'</center></td>
											</tr>';
									
										$count++;
										}
									
										$html.='	</tbody>
									</table>	
									<style>
									.bord { border-right: 1px solid; }
									    @media (max-width: 768px)
                                        { 
                                             .bord { 
                                                  border-right: none;
                                                 border-bottom: 1px solid; }
                                        }
									    
									</style>
								</div>
								    </div>
								</div>';
								 }
								
							 if(!empty($dischargedata['test'])) {
    								
								   	$html.=' <div class="row">
								    <div class="col-md-12">
								        
								        <div class="invoice-1-add-left tableRepeat" style="margin-bottom: 10px;overflow: hidden;">
									<table class="">
									<caption style="letter-spacing: 2px;color: #4a4747;font-weight: 600;text-align: center; font-size: initial;"></caption>
										<thead>
											<tr>
											    <!--<th style="width:1%;border: 1px solid #2f2f2f;"><center><b>Sr.</b></center></th>-->
												<th style="width: 12%;border: 1px solid #2f2f2f;"><center><b style="padding:2px;">Test to be done</b></center></th>
												
												
												
												</tr>
										</thead>
										   
									</table>	
									<style>
									.bord { border-right: 1px solid; }
									    @media (max-width: 768px)
                                        { 
                                             .bord { 
                                                  border-right: none;
                                                 border-bottom: 1px solid; }
                                        }
									    
									</style>
								</div>
								    </div>
								</div>';
								 }
								
					             if($dischargedata['eyes']['vision_r_eye']!="") {
				                   	$html.='<div class="row ">
                     
                      	  <div class="invoice-1-add-left col-lg-12 col-md-12" style="margin-bottom: -38px;padding: 0;">
                            <div class="col-md-6">
                                <div class="invoice-1-add-left  tableRepeat">     
                                    <table >
                                             <tr>
                                                  <th style="width:55%;border: 1px solid #2f2f2f;text-align:center;"></th>
                                                  <th style="width:8%;border: 1px solid #2f2f2f;text-align:center;"><b>Right Eye</b></th>
                                                  <th style="width:8%;border: 1px solid #2f2f2f;text-align:center;"><b>Left Eye</b></th>
                                             
                                             </tr>
                                             <tr>
                                                 <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>Vision</b></th>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"> '.$dischargedata['eyes']['vision_r_eye'].'</td>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;">'. $dischargedata['eyes']['vision_l_eye'].'</td>
                                             </tr>
                                             <tr>
                                                 <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>Vision With Glass</b></th>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;">'. $dischargedata['eyes']['vision_with_glass_r_eye'].'</td>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;">'. $dischargedata['eyes']['vision_with_glass_l_eye'].'</td>
                                             </tr>
                                             <tr>
                                                 <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>Vision With P.H</b></th>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"> '.$dischargedata['eyes']['vision_with_ph_r_eye'].'</td>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"> '.$dischargedata['eyes']['vision_with_ph_l_eye'].'</td>
                                             </tr>
                                             <tr>
                                                 <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>I.O.P. Non Touch</b></th>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;">'. $dischargedata['eyes']['vision_with_iop_non_th_r_eye'].'</td>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;">'. $dischargedata['eyes']['vision_with_iop_non_th_l_eye'].'</td>
                                             </tr>
                                             <tr>
                                                 <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>I.O.P. Tono</b></th>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;">'. $dischargedata['eyes']['vision_with_iop_non_tono_r_eye'].'</td>
                                                 <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;">'.$dischargedata['eyes']['vision_with_iop_non_tono_l_eye'].'</td>
                                             </tr>
                                    </table>
                                </div>
                           
                            </div>
                            <div class="col-md-6">
                                    <div class="invoice-1-add-left tableRepeat">
                                        <table class="">
                                         <tr style="border: 1px solid #2f2f2f;text-align:center;">
                                             <th style="width:20%;border: 1px solid #2f2f2f;text-align:center;" colspan="7"><b>Accept</b> </th>      
                                         </tr>
                                         <tr style="border: 1px solid #2f2f2f;text-align:center;"  >
                                          <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;padding: 8px 0px 8px 0px;"></th>
                                          <th style="width:15%;border: 1px solid #2f2f2f;text-align:center;" ><b>Right Eye</b></th>
                                          <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;" style="border-style:hidden;" ></th>
                                          <th style="width:4%;border: 1px solid #2f2f2f;text-align:center;" style="border-right:solid 1px;"></th>
                                          <th style="width:15%;border: 1px solid #2f2f2f;text-align:center;"><b>Left Eye</b></th>
                                          <th style="padding: 5px;width:8%;border: 1px solid #2f2f2f;text-align:center;" style="border-style:hidden;"></th>
                                          <th style="padding: 5px;width:8%;border: 1px solid #2f2f2f;text-align:center;" style="border-style:hidden;"></th>
                                         </tr>
                                     
                                     <tr>
                                      <td style="width:4%;border: 1px solid #2f2f2f;text-align:center;padding: 8px 0px 8px 0px;"></td>
                                      <td style="width:15%;border: 1px solid #2f2f2f;text-align:center;"><b>SPH</b></td>
                                      <td style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>CYL</b></td>
                                      <td style="width:4%;border: 1px solid #2f2f2f;text-align:center;"><b>AXIS</b></td>
                                      
                                      <td style="width:15%;border: 1px solid #2f2f2f;text-align:center;"><b>SPH</b></td>
                                      <td style="padding-left: 5px;padding-right: 5px;width:8%;border: 1px solid #2f2f2f;text-align:center;"><b>CYL</b></td>
                                      <td style="padding-left: 5px;padding-right: 5px;width:8%;border: 1px solid #2f2f2f;text-align:center;"><b>AXIS</b></td>
                                     </tr>
                                     
                                     <tr>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><b>Dist</b></td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"> '.$dischargedata['eyes']['accepts_dis_r_sph'].'</td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"> '.$dischargedata['eyes']['accepts_dis_r_cyl'].'</td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"> '.$dischargedata['eyes']['accepts_dis_r_axis'].'</td>
                                      
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"> '.$dischargedata['eyes']['accepts_dis_l_sph'].'</td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"> '.$dischargedata['eyes']['accepts_dis_l_cyl'].'</td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"> '.$dischargedata['eyes']['accepts_dis_l_axis'].'</td>
                                      
                                      
                                     </tr>
                                      <tr>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;"><b>Near</b></td>
                                      
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;">'. $dischargedata['eyes']['accepts_near_r_sph'].'</td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;">'. $dischargedata['eyes']['accepts_near_r_cyl'].'</td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;">'. $dischargedata['eyes']['accepts_near_r_axis'].'</td>
                                      
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;">'. $dischargedata['eyes']['accepts_near_l_sph'].'</td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;">'. $dischargedata['eyes']['accepts_near_l_cyl'].'</td>
                                      <td style="width:20%;border: 1px solid #2f2f2f;text-align:center;">'. $dischargedata['eyes']['accepts_near_l_axis'].'</td>
                                      
                                     
                                     </tr>
                                </table>
                                    </div>
                            </div>
                        </div>
                        
                    </div>';
							    	}
								
							$html.='	<footer>
                                          
        							
        							<div class="invoice-1-add-left col-lg-12 col-md-12 auth" style="padding-top: 20px;margin-bottom: 20px;text-align: right;">
        							
        								<h5 class="bottom">Authorised Signatory</h5>
        								
                                    </div>
                                </footer>
							
						
					      
						</div>
					</div>
				</div>
			</div>
		</div>
		</div>
	</section>
	<!--END DASHBOARD-->
    
</body>';
echo $html;
   }else{
       
       $resultresponse=array("status"=> 201,
        "message"=> "failed");
       
       return $resultresponse;
       
   }
        
  
    }
    function symptoms($type)
    {
        $this->db->order_by("category","ASC");
        $this->db->select('*');
        $this->db->from('hospitals_symptoms');
        $this->db->where('category_id!=',$type);
        return $this->db->get()->result_array();
    }
    function select_manage_medicine_detail()
    {
        return $this->db->get('product')->result_array();
    }
    
      function patient_info_prescription_opd($id){
         
        $this->db->select('*');
        $this->db->from('doctor_prescription');
        $this->db->join('hospital_booking_master','hospital_booking_master.booking_id=doctor_prescription.booking_id','left');
        $this->db->join('hospital_ipd_patient','hospital_ipd_patient.id=doctor_prescription.patient_id','left');
        $this->db->join('hospital_doctor_list', 'hospital_doctor_list.id = doctor_prescription.doctor_id','left');
        $this->db->join('doctor_prescription_medicine', 'doctor_prescription_medicine.prescription_id = doctor_prescription.id','left');
       // $this->db->where('hospital_ipd_patient.listing_id','3142');
        $this->db->where('doctor_prescription.id',$id);
        $sqlquery= $this->db->get()->row_array();
         
        $this->db->select('doctor_prescription.booking_id,doctor_prescription.prescription_note,doctor_prescription.followupdate');
        $this->db->from('doctor_prescription');
        $this->db->join('hospital_booking_master','hospital_booking_master.booking_id=doctor_prescription.booking_id');
        $this->db->where('doctor_prescription.id',$id);
        $sqlquery8= $this->db->get()->row_array();
        
        $this->db->select('hospital_ipd_patient.name');
        $this->db->from('doctor_prescription');
        $this->db->join('hospital_ipd_patient','hospital_ipd_patient.id=doctor_prescription.patient_id');
      //  $this->db->where('hospital_ipd_patient.listing_id','3142');
        $this->db->where('doctor_prescription.id',$id);
        $sqlquery9= $this->db->get()->row_array();
         
        $this->db->select('doctor_prescription_medicine.frequency,doctor_prescription_medicine.id,doctor_prescription_medicine.dosage,doctor_prescription_medicine.dosage_unit,doctor_prescription_medicine.prescription_id,doctor_prescription_medicine.medicine_name,doctor_prescription_medicine.frequency_first,doctor_prescription_medicine.frequency_second,doctor_prescription_medicine.frequency_third,doctor_prescription_medicine.instruction,doctor_prescription_medicine.day');
        $this->db->from('doctor_prescription_medicine');
     
        $this->db->where('doctor_prescription_medicine.prescription_id',$id);
        $sqlquery1= $this->db->get()->result_array();
	     
	$this->db->select('*');
        $this->db->from('hospital_opd_test');
        $this->db->where('hospital_opd_test.prescription_id',$id);
        $sqlquery2=$this->db->get()->result_array();
	
	 $this->db->select('*');
        $this->db->from('eyes_opd_details');
        $this->db->where('eyes_opd_details.prescription_id',$id);
        $sqlquery3=$this->db->get()->row_array();    
            
        
        
        $this->db->select('*');
        $this->db->from('hospitals');
        $this->db->where('hospitals.user_id','3142');
        $sqlhospital= $this->db->get()->row_array();
        
        $pram=array(
            'details'=>$sqlquery,
            'details_new'=>$sqlquery8,
            'details_new_name'=>$sqlquery9,
            'medicine'=>$sqlquery1,
	    'test'=>$sqlquery2,	
	    'eyes'=>$sqlquery3,
            'hospital_details'=>$sqlhospital
            );
            // echo '<pre>';
        //  print_r($pram); die;
            
       return $pram; 
         
        }
    
     public function add_live_url($doctor_id, $url,$type,$id)
    {
        
        if($type=="add")
        {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $updated_at = date('Y-m-d H:i:s');
        $user_data = array(
                    'doctor_id' => $doctor_id,
                    'url' => $url,
                    'type' => "",
                    'source' => "",
                    'text' => "",
                    'highlight' => "",
                    'status' => 0,
                    'created_at' => $updated_at
                   
               
                );
                
                
                
                $success = $this->db->insert('Doctor_story', $user_data);
                $id      = $this->db->insert_id();
                if(!empty($id))
                {
                     return array(
                            'status' => 200,
                            'message' => 'success',
                            'id'=>$id
                        );
                }
                else
                {
                     return array(
                            'status' => 201,
                            'message' => 'failed',
                            'id'=>""
                        );
                }
        }
        else
        {
          
                
               $this->db->where('id', $id);
               $this->db->where('doctor_id', $doctor_id);
               $this->db->delete('Doctor_story');
              
                     return array(
                            'status' => 200,
                            'message' => 'success'
                        );
               
        }
    }
    
     public function dashboard_counter($doctor_id) 
     {
        $app="0";
        $patient=0;
        $prescription="0";
        $ledger="0";
        $events="0";
        $ask="0";
        
          $query12 = $this->db->query("SELECT doctor_name,email,telephone,image FROM `doctor_list` WHERE user_id='$doctor_id'");
        $count12 = $query12->num_rows();
        if ($count12 > 0) 
        {
            $row12=$query12->row_array();
            
            $d_name=$row12['doctor_name'];
            $d_phone=$row12['telephone'];
            $d_email=$row12['email'];
            
              if ($row12['image'] != '') {
                                $profile_pic = $row12['image'];
                                $profile_pic = str_replace(' ', '%20', $profile_pic);
                                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                            } else {
                                $profile_pic = '';
                            }
            
            
        }
        
        $query = $this->db->query("SELECT count(id) as ids  FROM `doctor_booking_master` WHERE listing_id='$doctor_id'");
        $count = $query->num_rows();
        if ($count > 0) 
        {
            $row=$query->row_array();
            $app=$row['ids'];
        }
        $query1 = $this->db->query("SELECT count(id) as ids  FROM `doctor_patient` WHERE doctor_id='$doctor_id' and status = 0 group by contact_no");
        $count1 = $query1->num_rows();
        
        if ($count1 > 0) 
        {
            $row1=$query1->row_array();
            $patient=$count1;
        }
         $query2 = $this->db->query("SELECT count(id) as ids  FROM `doctor_prescription` WHERE doctor_id='$doctor_id'");
        $count2 = $query2->num_rows();
        if ($count2 > 0) 
        {
            $row2=$query2->row_array();
            $prescription=$row2['ids'];
        }
         $query3 = $this->db->query("SELECT count(id) as ids  FROM `user_ledger` WHERE listing_id='$doctor_id' and  trans_type ='0'");
        $count3 = $query3->num_rows();
        if ($count3 > 0) 
        {
            $row3=$query3->row_array();
            $ledger=$row3['ids'];
        }
         $query4 = $this->db->query("SELECT count(id) as ids  FROM `events` WHERE user_id='$doctor_id' ");
        $count4 = $query4->num_rows();
        if ($count4 > 0) 
        {
            $row4=$query4->row_array();
            $events=$row4['ids'];
        }
        
        
        $query5=$this->db->query("SELECT *  FROM `doctor_online` WHERE status='0' and doctor_id='$doctor_id'");
        $count5 = $query5->num_rows();
        if($count5 > 0)
          {
             $row5=$query5->row_array(); 
            $resultpost[] = array('days'=>$row5['day'],
                                   'start_time'=>$row5['starttime'],
                                   'end_time'=>$row5['endtime']);  
          }
        else 
          {
            $resultpost = array();
          }
          
        $query6=$this->db->query("SELECT *  FROM `doctor_list` WHERE user_id='$doctor_id'");  
        $row6=$query6->row_array();
        $video_fee=$row6['consultaion_video'];
        $chat_fee=$row6['consultaion_chat'];
        $type=$row6['consultation_type'];
        if(empty($video_fee))
        {
            $video_fee="0";
        }
       if(empty($chat_fee))
        {
            $chat_fee="0";
        }
        $chat=0;
        if($chat_fee!=0)
        {
            $chat=1;
        }
        $video=0;
        if($video_fee!=0)
        {
            $video=1;
        }
        
        
        $online_fees=array(
                             "chat"=>$chat,
                             "video"=>$video,
                             "chat_fee"=>$chat_fee,
                             "video_fee"=>$video_fee
                             );
        
        $data[] = array(
                         'appointment' => (int)$app,
                         'patient' => (int)$patient,
                         'prescription' => (int)"0",
                         'ask' => (int)$ask,
                         'ledger' => (int)$ledger,
                         'events' => (int)$events,
                         'online_details'=>$resultpost,
                         'online_fees'=>$online_fees,
                         'user_details'=>array(
                             "name"=>"$d_name",
                             "phone"=>"$d_phone",
                             "email"=>"$d_email",
                             "image"=>$profile_pic
                             )
                      );
                      
        return $data;              
        
        
     }
     
      public function block_list($user_id)
    {
        $radius = '5';
        $resultpost = array();
      
     
        $sql   = "SELECT * from doctor_dating_status where (user_id='$user_id' OR dating_id='$user_id') AND status=5";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row1) {
                
                $doctor_user_id        = $row1['user_id'];
                $doctor_dating_id      = $row1['dating_id'];
                
                $sql2   = "SELECT * from doctor_secret_friend_list where (user_id='$user_id' OR dating_id='$doctor_dating_id') AND status=1";
                $query2 = $this->db->query($sql2);
                $count2 = $query2->num_rows();
                if ($count2 > 0) {
                    $res = $query2->row_array() ;
                    $secret_like = $res['status'];
                }
                else
                {
                     $secret_like = "0";
                }
                
                if($doctor_user_id == $user_id)
                {
                    $final_id = $doctor_dating_id;
                }
                else
                {
                    $final_id = $doctor_user_id;
                }
               
               if($final_id != $user_id)
               {
                   // $sql1   = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id='" . $final_id . "' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
                       $sql1   = "SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount, doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.bio,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id='" . $final_id . "'  LIMIT 0 , 20";
                      $count_query = $this->db->query($sql1);
                      $count_status = $count_query->num_rows();
                
                        
                    if($count_status > 0)
                    {
                            $row = $count_query->row_array();
                            $lat                 = $row['lat'];
                            $lng                 = $row['lng'];
                            $doctor_name         = $row['doctor_name'];
                            $email               = $row['email'];
                            $gender              = $row['gender'];
                            $doctor_phone        = $row['telephone'];
                            $dob                 = $row['dob'];
                            $category            = $row['category'];
                            $speciality          = $row['speciality'];
                            $service             = $row['service'];
                            $degree              = $row['qualification'];
                            $experience          = $row['experience'];
                            $reg_council         = $row['reg_council'];
                            $reg_number          = $row['reg_number'];
                            $doctor_user_id      = $row['user_id'];
                            $clinic_name         = $row['clinic_name'];
                            $address             = $row['address'];
                            $city                = $row['city'];
                            $state               = $row['state'];
                            $pincode             = $row['pincode'];
                            $bio                 = $row['bio'];
                            $followers           = '0';
                            $following           = "0";
                            $profile_views       = '0';
                            $total_reviews       = '0';
                            $total_rating        = '0';
                            $total_profile_views = '0';
                             $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                            $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                            $discount            = $row['discount'];
                            
                          
                            $newdob = date("d-m-Y", strtotime($dob));
                           
                            $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                            $row_rating   = $query_rating->row_array();
                            $total_rating = $row_rating['total_rating'];
                            if ($total_rating === NULL || $total_rating === '') {
                                $total_rating = '0';
                            }
                            $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                            $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                            if ($row['image'] != '') {
                                $profile_pic = $row['image'];
                                $profile_pic = str_replace(' ', '%20', $profile_pic);
                                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                            } else {
                                $profile_pic = '';
                            }
                  
                           // $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                           if($row['other_name'] == NULL)
                            {
                                $other_name          = "";
                            }
                            else
                            {
                                $other_name          = $row['other_name'];
                            }
                            
                            if($row['other_name_type'] == NULL)
                            {
                                $other_name_type          = "";
                            }
                            else
                            {
                                $other_name_type     = $row['other_name_type'];
                            }
                            
                            if($row['placeses'] == NULL)
                            {
                                $placeses          = "";
                            }
                            else
                            {
                                $placeses            = $row['placeses'];
                            }
                            
                            if($row['relation_ship_status'] == NULL)
                            {
                                $relation_ship_status          = "";
                            }
                            else
                            {
                                $relation_ship_status   = $row['relation_ship_status'];
                            }
                            if(empty($bio))
                            {
                                $bio="";
                            }
                            else
                            {
                                $bio;
                            }
                            
                             $language_selection  = rtrim($row['language_selection'],',');
                             
                          $sporst_array=array();
                          $movies_array=array();
                          $tv_show_array=array();
                          $books_array=array();
                             $query =$this->db->query("SELECT * FROM `doctor_fav_details` WHERE user_id='$user_id'");
                             foreach($query->result_array() as $moviesrow )
                             {
                               $pic_id=$moviesrow['pic_id'];
                               $pic_name = $moviesrow['pic_name'];
                               if($moviesrow['pic'] !='')
                                {
                                   $pic = $moviesrow['pic'];
                                }
                               else{
                                     $pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                                 }
                             
                             if($moviesrow['pic_type_id']=='1'){
                                 $movies_type_id="Sports";
                                 
                                 $sporst_array[]=array(
                                                        'sports_id'=>$pic_id,
                                                        'sports_name' => $pic_name,
                                                        'sports_type' => $movies_type_id,
                                                        'sports_pic' =>  $pic
                                                       );
                                 
                                 
                             }
                             if($moviesrow['pic_type_id']=='2'){
                                 $movies_type_id1="Movies";
                                  $movies_array[]=array(
                                                        'movies_id'=>$pic_id,
                                                        'movies_name' => $pic_name,
                                                        'movies_type' => $movies_type_id1,
                                                        'movies_pic' =>  $pic
                                                       );
                                 
                             }
                             
                             if($moviesrow['pic_type_id']=='3'){
                                 $movies_type_id2="TV-Shows";
                                 $tv_show_array[]=array(
                                                        'tv_shows_id' =>$pic_id, 
                                                        'tv_shows_name' => $pic_name,
                                                        'tv_shows_type' => $movies_type_id2,
                                                        'tv_shows_pic' =>  $pic
                                                       );
                             }
                              if($moviesrow['pic_type_id']=='4'){
                                 $movies_type_id3="Books";
                                 $books_array[]=array(
                                                        'books_id'=>$pic_id,
                                                        'books_name' => $pic_name,
                                                        'books_type' => $movies_type_id3,
                                                        'books_pic' =>  $pic
                                                       );
                             }
                             
                             
                             }
                             
                              $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=5");
                             //$queryss = $this->db->query($total_friends1);
                              $total_friends = $total_friends1->num_rows();
                  
                             $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                             $total_privacyss = $total_privacy->num_rows();
                             if($total_privacyss > 0)
                             {
                                 
                                $address_privacy = $total_privacy->row()->address_privacy;
                                if($address_privacy == 2)
                                {
                                    $address ="hidden";
                                    $city = "hidden";
                                    $state = "hidden";
                                    $pincode = "hidden";
                                }
                               
                                $experience_privacy = $total_privacy->row()->experience_privacy;
                                if($experience_privacy == 2)
                                {
                                    $experience ="hidden";
                                   
                                }
                                
                                $dob_privacy = $total_privacy->row()->dob_privacy;
                                if($dob_privacy == 2)
                                {
                                    $newdob ="hidden";
                                   
                                }
                                
                                $email_privacy = $total_privacy->row()->email_privacy;
                                if($email_privacy == 2)
                                {
                                    $email ="hidden";
                                   
                                }
                                
                                $bio_privacy = $total_privacy->row()->bio_privacy;
                                if($bio_privacy == 2)
                                {
                                    $bio ="hidden";
                                   
                                }
                                
                                $language_privacy = $total_privacy->row()->language_privacy;
                                if($language_privacy == 2)
                                {
                                    $language_selection ="hidden";
                                   
                                }
                                
                                $relationship_privacy = $total_privacy->row()->relationship_privacy;
                                if($relationship_privacy == 2)
                                {
                                    $relation_ship_status ="hidden";
                                   
                                }
                                
                                $work_privacy = $total_privacy->row()->work_privacy;
                                if($work_privacy == 2)
                                {
                                    $placeses ="hidden";
                                   
                                }
                                
                                $education_privacy = $total_privacy->row()->education_privacy;
                                if($education_privacy == 2)
                                {
                                    $degree ="hidden";
                                   
                                }
                                
                                $onames_privacy = $total_privacy->row()->onames_privacy;
                                if($onames_privacy == 2)
                                {
                                    $other_name ="hidden";
                                   
                                }
                                
                                $place_privacy = $total_privacy->row()->place_privacy;
                                if($place_privacy == 2)
                                {
                                    $placeses ="hidden";
                                   
                                }
                                
                                $sports_privacy = $total_privacy->row()->sports_privacy;
                                if($sports_privacy == 2)
                                {
                                    $sporst_array =array();
                                   
                                }
                                
                                $movies_privacy = $total_privacy->row()->movies_privacy;
                                if($movies_privacy == 2)
                                {
                                    $movies_array =array();
                                   
                                }
                                
                                $shows_privacy = $total_privacy->row()->shows_privacy;
                                if($shows_privacy == 2)
                                {
                                    $tv_show_array =array();
                                   
                                }
                                
                                $books_privacy = $total_privacy->row()->books_privacy;
                                if($books_privacy == 2)
                                {
                                    $books_array =array();
                                   
                                }
                                
                             }
                             
                            $resultpost[] = array(
                                'doctor_user_id' => $doctor_user_id,
                                'listing_type' => "5",
                                'lat' => $lat,
                                'lng' => $lng,
                                'doctor_name' => $doctor_name,
                                'email' => $email,
                                'gender' => $gender,
                                'doctor_phone' => $doctor_phone,
                                'dob' => $newdob,
                                'experience' => $experience,
                                'qualification'=> $degree,
                                'reg_council' => $reg_council,
                                'reg_number' => $reg_number,
                                'profile_pic' => $profile_pic,
                                'address' => $address,
                                'city' => $city,
                                'state' => $state,
                                'pincode' => $pincode,
                                'rating' => $total_rating,
                                'review' => "0",
                                'followers' => "0",
                                'following' => "0",
                                'profile_views' => $profile_views,
                                'bio'=> $bio,
                                'other_name' => $other_name,
                                'other_name_type'  => $other_name_type,
                                'placeses' => $placeses,
                                'relation_ship_status'=> $relation_ship_status,
                                'language_selection'  => $language_selection,
                                'sport_details'=>$sporst_array,
                                'movies_details'=>$movies_array,
                                'tv_show_details'=>$tv_show_array,
                                'books_details'=>$books_array,
                                'total_friend' => $total_friends,
                                'secret_status' => $secret_like
                            );
                        }   
               }
            }  
        } else {
            $resultpost = array();
        }
        /*function array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
        {
            $sort_col = array();
            foreach ($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }
            array_multisort($sort_col, $dir, $arr);
        }
        if(!empty($resultpost)){
        array_sort_by_column($resultpost, 'distance'); }*/
        return $resultpost;
    }
    
    public function doctor_update_block($user_id,$doctor_id,$status) 
    {
        
         $query = $this->db->query("UPDATE doctor_dating_status SET status='$status' WHERE user_id ='$user_id' and dating_id='$doctor_id'");
         $query1 = $this->db->query("UPDATE doctor_dating_status SET status='$status' WHERE user_id ='$doctor_id' and dating_id='$user_id'");
         $type="";
         if($status=="5")
         {
             $type="block";
         }
         elseif($status=="6")
         {
             $type="un-friend";
         }
         elseif($status=="3")
         {
              $type="un-block";
         }
          return array(
                            'status' => 200,
                            'message' => 'success',
                            'type'=>$type
                        );
    }
    public function doctor_feedback($user_id,$feedback,$email)
    {
        $data = array(
                            'user_id'           => $user_id,
                            'email'             => $email,
                            'feed_back'         => $feedback
	                        
                );  
                
                $this->db->insert('users_feedback',$data);      
                $id = $this->db->insert_id();
        if($id != "")        
                $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
            
         else {
            $resultpost =   array(
                                'status' => 201,
                                'message' => 'Failed'
                            );
        }
        return $resultpost;
        
    }
    
    public function doctor_status_change($user_id,$clinic_id,$from_date,$from_time,$to_time,$reason)
    {
        //echo "select id,status from doctor_booking_master where listing_id = '$user_id' AND clinic_id='$clinic_id' and booking_date ='$from_date' and from_time  >='$from_time' and to_time  <='$to_time'  ";
        $resultpost = array();
         $query = $this->db->query("select id,status from doctor_booking_master where listing_id = '$user_id' AND clinic_id='$clinic_id' and booking_date ='$from_date' and from_time  >='$from_time' and to_time  <='$to_time'  ");
        foreach ($query->result_array() as $row) {
            
              $id                = $row['id'];
              $status            = $row['status'];
              if($status=="5")
              {
                  $status="9";
              }
              elseif($status=="8")
              {
                  $status="10";
              }
              
              
               $query1 = $this->db->query("UPDATE doctor_booking_master SET status='$status',description='$reason' WHERE id ='$id'");
          
        }
        //print_r($resultpost);
       
        
        return $resultpost =   array(
                                'status' => 200,
                                'message' => 'Success'
                            );
        
    }
    
    
       public function doctor_request_($user_id,$status,$cat)
    {
        $resultpost = array();
       $wher="";
         $wher_cat="";
      if($status==1){
         
                $wher.="dating_id='$user_id' AND status=1";   
      
      }else{
                $wher.="user_id='$user_id' AND status=1";   
      }
      // echo "SELECT * from doctor_dating_status where $wher  AND status=1";
        $sql   = "SELECT * from doctor_dating_status where $wher  ";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row1) {
                
                 if($status==1){
                $final_id      = $row1['user_id'];  
             
                  if($cat!=""){
                      $wher_cat.="and FIND_IN_SET($cat, category) ";   
                     }else{
                    $wher_cat.="";   
                     }
             
                }else{
          
                $final_id      = $row1['dating_id'];
            
                   if($cat!=""){
                      $wher_cat.="and FIND_IN_SET($cat, category) ";   
                     }else{
                    $wher_cat.="";   
                     }
             
                } 
             
               
               // echo "SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id";
                $sql1   = "SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.category,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_list.bio,doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.medical_affiliation,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id='$final_id' $wher_cat ";
                $count_query = $this->db->query($sql1);
                $count_status = $count_query->num_rows();
             
                if($count_status > 0)
                {
                        $row = $count_query->row_array();
                        $lat                 = $row['lat'];
                        $lng                 = $row['lng'];
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $clinic_name         = $row['clinic_name'];
                        $address             = $row['address'];
                        $city                = $row['city'];
                        $state               = $row['state'];
                        $pincode             = $row['pincode'];
                        $followers           = '0';
                        $following           = '0';
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                         $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        $bio                = $row['bio'];
                        $other_name            = $row['other_name'];
                        $other_name_type    = $row['other_name_type'];
                        $placeses           = $row['placeses'];
                        $relation_ship_status  = $row['relation_ship_status'];
                        $language_selection = $row['language_selection'];
                        $degree             = $row['qualification'];
                        $medical_affiliation    = $row['medical_affiliation'];
                         
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                        }
                        
                        $sporst_array=array();
                        $movies_array=array();
                        $tv_show_array=array();
                        $books_array=array();
                        
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=5");
                             //$queryss = $this->db->query($total_friends1);
                              $total_friends = $total_friends1->num_rows();
                  
                        
                           $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2 || $address_privacy == 1)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2 || $address_privacy == 1)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2 || $address_privacy == 1)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2 || $address_privacy == 1)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2 || $address_privacy == 1)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2 || $address_privacy == 1)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2 || $address_privacy == 1)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2 || $address_privacy == 1)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2 || $address_privacy == 1)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2 || $address_privacy == 1)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2 || $address_privacy == 1)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2 || $address_privacy == 1)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2 || $address_privacy == 1)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                        
                        
              
                        $resultpost[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'category' => $category,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => $total_reviews,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_views' => $profile_views,
                            'qualification'=> $degree,
                            'medical_affiliation' => $medical_affiliation,
                            'speciality'          => $speciality,
                            'clinic_name'         => $clinic_name,
                            'service'             => $service,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends
                            
                        );
                        
                    }
                else
                {
                    $sql1   = "SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_list.bio,doctor_list.other_name,doctor_list.other_name_type,doctor_list.placeses,doctor_list.relation_ship_status,doctor_list.language_selection,doctor_list.medical_affiliation,hospital_doctor_id FROM doctor_list WHERE doctor_list.user_id='$final_id' $wher_cat ";
                   $count_query = $this->db->query($sql1);
                   $count_status = $count_query->num_rows();
                   if($count_status > 0)
                     {
                        $row = $count_query->row_array();
                        $doctor_name         = $row['doctor_name'];
                        $email               = $row['email'];
                        $gender              = $row['gender'];
                        $doctor_phone        = $row['telephone'];
                        $dob                 = $row['dob'];
                        $category            = $row['category'];
                        $speciality          = $row['speciality'];
                        $service             = $row['service'];
                        $degree              = $row['qualification'];
                        $experience          = $row['experience'];
                        $reg_council         = $row['reg_council'];
                        $reg_number          = $row['reg_number'];
                        $doctor_user_id      = $row['user_id'];
                        $hospital_doctor_id  = $row['hospital_doctor_id'];
                        // hospitals
                        $sql12 = "SELECT hospital_id FROM hospital_doctor_list where FIND_IN_SET($hospital_doctor_id, id)";
                        $count_query2 = $this->db->query($sql12);
                        $count_status2 = $count_query2->num_rows();
                   if($count_status2 > 0)
                     {
                        $row2 = $count_query2->row_array(); 
                        $hospital_id=$row2['hospital_id'];
                        $sql13 = "SELECT * FROM hospitals where user_id='$hospital_id'";
                        $count_query3 = $this->db->query($sql13);
                        $row3 = $count_query3->row_array(); 
                        
                        $city1                = $row3['city'];
                        $state1               = $row3['state'];
                        $sql14 = "SELECT * FROM cities where city_id='$city1'";
                        $count_query4 = $this->db->query($sql14);
                        $row4 = $count_query4->row_array(); 
                        
                        $sql15 = "SELECT * FROM states where state_id='$state1'";
                        $count_query5 = $this->db->query($sql15);
                        $row5 = $count_query5->row_array(); 
                        
                        $lat                 = $row3['lat'];
                        $lng                 = $row3['lng'];
                        $clinic_name         = $row3['name_of_hospital'];
                        $address             = $row3['address'];
                        $city                = $row4['city_name'];
                        $state               = $row5['state_name'];
                        $pincode             = $row3['pincode'];
                        
                        $followers           = '0';
                        $following           = '0';
                        $profile_views       = '0';
                        $total_reviews       = '0';
                        $total_rating        = '0';
                        $total_profile_views = '0';
                        $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                        $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                        $discount            = $row['discount'];
                        $bio                = $row['bio'];
                        $other_name            = $row['other_name'];
                        $other_name_type    = $row['other_name_type'];
                        $placeses           = $row['placeses'];
                        $relation_ship_status  = $row['relation_ship_status'];
                        $language_selection = $row['language_selection'];
                        $degree             = $row['qualification'];
                        $medical_affiliation    = $row['medical_affiliation'];
                         
                        
                      
                        $newdob = date("d-m-Y", strtotime($dob));
                       
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        $total_reviews       = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                        $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();
                        if ($row['image'] != '') {
                            $profile_pic = $row['image'];
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        } else {
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                        }
                        
                        $sporst_array=array();
                        $movies_array=array();
                        $tv_show_array=array();
                        $books_array=array();
                        
                          $total_friends1 = $this->db->query("SELECT * from doctor_dating_status where (user_id='$doctor_user_id' OR dating_id='$doctor_user_id') AND status=5");
                             //$queryss = $this->db->query($total_friends1);
                              $total_friends = $total_friends1->num_rows();
                  
                        
                           $total_privacy = $this->db->query("SELECT * from doctor_privacy where user_id='$doctor_user_id'");
                         $total_privacyss = $total_privacy->num_rows();
                         if($total_privacyss > 0)
                         {
                             
                            $address_privacy = $total_privacy->row()->address_privacy;
                            if($address_privacy == 2 || $address_privacy == 1)
                            {
                                $address ="hidden";
                                $city = "hidden";
                                $state = "hidden";
                                $pincode = "hidden";
                            }
                           
                            $experience_privacy = $total_privacy->row()->experience_privacy;
                            if($experience_privacy == 2 || $address_privacy == 1)
                            {
                                $experience ="hidden";
                               
                            }
                            
                            $dob_privacy = $total_privacy->row()->dob_privacy;
                            if($dob_privacy == 2 || $address_privacy == 1)
                            {
                                $newdob ="hidden";
                               
                            }
                            
                            $email_privacy = $total_privacy->row()->email_privacy;
                            if($email_privacy == 2 || $address_privacy == 1)
                            {
                                $email ="hidden";
                               
                            }
                            
                            $bio_privacy = $total_privacy->row()->bio_privacy;
                            if($bio_privacy == 2 || $address_privacy == 1)
                            {
                                $bio ="hidden";
                               
                            }
                            
                            $language_privacy = $total_privacy->row()->language_privacy;
                            if($language_privacy == 2 || $address_privacy == 1)
                            {
                                $language_selection ="hidden";
                               
                            }
                            
                            $relationship_privacy = $total_privacy->row()->relationship_privacy;
                            if($relationship_privacy == 2 || $address_privacy == 1)
                            {
                                $relation_ship_status ="hidden";
                               
                            }
                            
                            $work_privacy = $total_privacy->row()->work_privacy;
                            if($work_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $education_privacy = $total_privacy->row()->education_privacy;
                            if($education_privacy == 2 || $address_privacy == 1)
                            {
                                $degree ="hidden";
                               
                            }
                            
                            $onames_privacy = $total_privacy->row()->onames_privacy;
                            if($onames_privacy == 2 || $address_privacy == 1)
                            {
                                $other_name ="hidden";
                               
                            }
                            
                            $place_privacy = $total_privacy->row()->place_privacy;
                            if($place_privacy == 2 || $address_privacy == 1)
                            {
                                $placeses ="hidden";
                               
                            }
                            
                            $sports_privacy = $total_privacy->row()->sports_privacy;
                            if($sports_privacy == 2 || $address_privacy == 1)
                            {
                                $sporst_array =array();
                               
                            }
                            
                            $movies_privacy = $total_privacy->row()->movies_privacy;
                            if($movies_privacy == 2 || $address_privacy == 1)
                            {
                                $movies_array =array();
                               
                            }
                            
                            $shows_privacy = $total_privacy->row()->shows_privacy;
                            if($shows_privacy == 2 || $address_privacy == 1)
                            {
                                $tv_show_array =array();
                               
                            }
                            
                            $books_privacy = $total_privacy->row()->books_privacy;
                            if($books_privacy == 2 || $address_privacy == 1)
                            {
                                $books_array =array();
                               
                            }
                            
                         }
                        
                        
              
                        $resultpost[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'listing_type' => "5",
                            'lat' => $lat,
                            'lng' => $lng,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'category' => $category,
                            'doctor_phone' => $doctor_phone,
                            'dob' => $newdob,
                            'experience' => $experience,
                            'reg_council' => $reg_council,
                            'reg_number' => $reg_number,
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'rating' => $total_rating,
                            'review' => $total_reviews,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_views' => $profile_views,
                            'qualification'=> $degree,
                            'medical_affiliation' => $medical_affiliation,
                            'speciality'          => $speciality,
                            'clinic_name'         => $clinic_name,
                            'service'             => $service,
                            'bio'=> $bio,
                            'other_name' => $other_name,
                            'other_name_type'  => $other_name_type,
                            'placeses' => $placeses,
                            'relation_ship_status'=> $relation_ship_status,
                            'language_selection'  => $language_selection,
                            'sport_details'=>$sporst_array,
                            'movies_details'=>$movies_array,
                            'tv_show_details'=>$tv_show_array,
                            'books_details'=>$books_array,
                            'total_friend' => $total_friends
                            
                        );
                     }
                        
                    }
                }
                    
                    
                    
                  
            }  
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    function unique_multidim_array($array, $key) {
    $temp_array = array();
    $i = 0;
    $key_array = array();
    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}
    
           public function doctor_request_category($user_id,$status)
    {  
        $resultpost = array();
        $cat_new=array();
       $wher="";
      if($status==1){
        $wher.="dating_id='$user_id' AND status=1";   
      }else{
          
        $wher.="user_id='$user_id' AND status=1 ";   
      }
      // echo "SELECT * from doctor_dating_status where $wher  AND status=1";
        $sql   = "SELECT * from doctor_dating_status where $wher  ";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row1) {
                
                 if($status==1){
             $final_id      = $row1['user_id'];  
                }else{
          
            $final_id      = $row1['dating_id'];   
                } 
             
               
               // echo "SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id";
                 $sql1   = "SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.category FROM doctor_list WHERE doctor_list.user_id='$final_id'";
                $count_query = $this->db->query($sql1);
                $count_status = $count_query->num_rows();
          
                if($count_status > 0)
                {   $category=array();
                        $row = $count_query->row_array();
                        
                        $doctor_name        = $row['doctor_name'];
                        $category           = $row['category'];
                        $doctor_user_id     = $row['user_id'];
                     
                        
                        $cate=explode(",",$category);
                      
                       
                        $count=0;
                        foreach($cate as $as){
                        
                            if(!empty($as)){
                                    $sql2   = "SELECT * FROM business_category WHERE  id='$as' "; 
                                    $count_query1 = $this->db->query($sql2);
                                    $row2 = $count_query1->row_array();
                                    
                                    $cate_id=$row2['id'];
                                    $cate1=$row2['category'];
                                    //$count=1;
                                    if(in_array($as,$cat_new)){
                                        $count +=1; 
                                        
                                    }else{
                                        
                                        $count =1;
                                    }
                                     $count; 
                                     $cat_new[]=$as;
                                     $resultpost[]=array("id"=>$as,"category"=>$cate1,"count"=>$count);
                                     
                            }
                        }
           
                
                    
                    }
                     
                 
               
                  
            }
               $columns = array_column($resultpost, 'count');
                        array_multisort($columns, SORT_DESC, $resultpost);
                        $this->unique_multidim_array($resultpost,'id');
                        
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
      public function doctor_request_withdraw($user_id,$dating_id,$days)
    {
        $resultpost = array();
        $sql   = "SELECT * from doctor_dating_withdraw where user_id=$user_id and dating_id=$dating_id ";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
           
                    
                        $resultpost = array("status"=>"201","message"=>"failed");     
             
            } else {
            
                                $data = array(
                                    'user_id'=>$user_id,
                                    'dating_id' => $dating_id,
                                    'days'=>$days
                	            );
                	            
                        $patient_insert = $this->db->insert('doctor_dating_withdraw', $data);
                        
                        
                          $this->db->where('user_id',$user_id);
                          $this->db->where('dating_id',$dating_id);
                          $this->db->set('status','2');
                          $this->db->update('doctor_dating_status');
                        
                        
                    
                        $resultpost = array("status"=>"200","message"=>"success");  
                    
                    
        }
        return $resultpost;
    }
    
    
    
    public function delete_work_place($user_id,$work_id){
        
        $delete = $this->db->query("DELETE FROM `doctor_work` WHERE user_id='$user_id' and  id='$work_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
        
    }
    
    
    public function delete_fav_place($user_id,$fav_id){
        
        $delete = $this->db->query("DELETE FROM `doctor_fav_details` WHERE user_id='$user_id' and  pic_id='$fav_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
        
        
    }
    
    
      public function Delete_doctor_education($user_id,$edu_id){
        
        $delete = $this->db->query("DELETE FROM `doctor_education` WHERE user_id='$user_id' and  id='$edu_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
        
        
    }
    
     public function languages_known($user_id){
         
        $query = $this->db->query("SELECT DISTINCT(`name`) FROM `languages` ORDER BY name ASC");
        $resultpost = array();
        foreach ($query->result_array() as $row) {
           
            $name	 = $row['name'];
            $resultpost[] = array(
                'name' => $name
               
                );
        }
        return $resultpost;
     }
    
  public function doctor_online_connect($doctor_id,$user_id,$booking_id)
    {
          $this->un_block_doctor();
        $query2 = $this->db->query("select name,token,agent,token_status,web_token  from users where id = '$user_id'");

            $count2 = $query2->num_rows();
            if ($count2 > 0) 
            {
               $row=$query2->row_array();
               $usr_name = $row['name'];
               $agent = $row['agent'];
               $reg_id = $row['token'];
               $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/doctor.png';
               $tag = 'text';
               $key_count = '1';
               $title       = "Go Live - It`s appointment time!";
               $msg         = "You have an appointment in few seconds, tap here to go live. Please be in a good network area for seamless appointment.";
               
               $type="go_live";
               
              
                
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
                       'notification_type'  => $type,
                       'notification_date'  => date('Y-m-d H:i:s')
                       
                    );
                    $this->db->insert('All_notification_Mobile', $notification_array);
               
              
               
               $this->send_gcm_notify_user_connect($title, $reg_id, $msg, $img_url, $tag, $agent,$type,$booking_id);
           
               $return=array(
                            'status' => 200,
                            'message' => 'success'
                        );
              return $return;
            }
    }
  
      public function send_gcm_notify_user_connect($title, $reg_id, $msg, $img_url, $tag, $agent,$type, $booking_id) {
    
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
                "notification_type" => $type,
                "notification_date" => $date,
                "booking_id" => $booking_id
                
            )
        );
       
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
    
       public function add_bookings_prescription($doctor_id,$user_id,$clinic_id)
    {
          $this->un_block_doctor();
        date_default_timezone_set('Asia/Kolkata');
        $response=array();
        $booking_id           = date("YmdHis");
        $date                 = date('Y-m-d H:i:s');
        $booking_date=date('Y-m-d');
        $from_time = date("H:i:s" , strtotime("+1 minutes"));
        $to_time =  date("H:i:s", strtotime("+1 minutes",strtotime($from_time)));
        $booking_time=$from_time."-".$to_time;
         $type="";
        
        if($clinic_id!="0")
        {
          $type="visit"; 
            
        $sql2 ="SELECT `consultation_charges` FROM doctor_clinic WHERE doctor_id='$doctor_id' and id='$clinic_id'";
        $query_practices  = $this->db->query($sql2);
        $get_pract=$query_practices->row_array();
        $fee = $get_pract['consultation_charges'];    
        
        }
        else
        {
         $type="video";
         
         $sql2 ="SELECT `video_fee` FROM doctor_online WHERE doctor_id='$doctor_id'";
         $query_practices  = $this->db->query($sql2);
         $count = $query_practices->num_rows();
         if($count > 0)
         {
         $get_pract=$query_practices->row_array();
         $fee = $get_pract['video_fee']; 
         }
         else
         {
            $fee="0"; 
         }
        }
        
        
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $user_id,
            'listing_id' => $doctor_id,
            'clinic_id' => $clinic_id,
            'booking_date' => $booking_date,
            'booking_time' => $from_time."-".$to_time,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'status' => 5,
            'description' => "",
            'date' => $date,
            'patient_id' => $user_id,
            'consultation_type' => $type,
            'vendor_type'=>5,
            'consultation_charges'=>$fee
        );
       
        $insert = $this->db->insert('doctor_booking_master', $booking_master_array);
        $appointment_id       = $this->db->insert_id();
        if($insert)
            {
                
              $query_dr2           = $this->db->query("SELECT * FROM users  WHERE id='$user_id' ");
                $data22               = $query_dr2->row_array();
                
                $patient_master_array = array(
                    'user_id' => $user_id,
                    'clinic_id' =>$clinic_id, 
                    'booking_id' => $appointment_id,
                    'patient_name' => $data22['name'],   
                    'contact_no' => $data22['phone'],
                    'email' => $data22['email'],
                    'gender' => $data22['gender'],
                    'allergies' => $data22['allergies'],
                    'heradiatry_problem' => $data22['heradiatry_problem'],
                    'created_date' => $date,
                    'type' => $type,
                    'date_of_birth' => $data22['dob'],
                    'doctor_id' => $doctor_id
                   );
                $this->db->insert('doctor_patient', $patient_master_array);
                $doctor_patient_id = $this->db->insert_id();  
                
               
                $resultpost=array('status'=>200,
                                  'message'=>'success',
                                  'booking_id'=>$booking_id,
                                 
                              );  
            }
            else
            {
              $resultpost=array('status'=>201,
                                  'message'=>'failed'
                                );   
            }
            return $resultpost;
    }
    
}
