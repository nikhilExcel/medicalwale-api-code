<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class DoctoronlineModel extends CI_Model { 
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
    
 /* public function doctor_online($doctor_id,$hours,$chat,$video,$chat_fee,$video_fee,$day) {
        $query = $this->db->query("SELECT id  FROM `doctor_online` WHERE doctor_id='$doctor_id'");
        $count = $query->num_rows();
       if($count > 0)
       {
          $new=explode(".",$hours);
           $hours1="+".$new[0]." hour";
           $hours2="+".$new[1]." minutes";
          
           date_default_timezone_set('Asia/Kolkata');
           $date = date('Y-m-d');
           $start_date = date('Y-m-d H:i:s');
           $end_date = date('Y-m-d H:i:s',strtotime( $hours1.$hours2 ,strtotime($start_date)));
          $query1 = $this->db->query("SELECT category  FROM `doctor_list` WHERE user_id='$doctor_id'");       
           
           $row1=$query1->row_array(); 
           $category=$row1['category'];
           
           $data = array(
                                  
                                   
                                    'date' => $date,
                                    'hours' => $hours,
                                    'day'=>$day,
                                    'starttime' => $start_date,
                                    'endtime' => $end_date,
                                    'chat' => $chat,
                                    'video' => $video,
                                    'chat_fee' => $chat_fee,
                                    'video_fee' => $video_fee,
                                    'status' => '0',
                                    'created_at' => $start_date,
                                     'category'=>$category
                	            );
                   
                    $this->db->where('doctor_id', $doctor_id);
                    $insert = $this->db->update('doctor_online', $data);
                    
                    $data1 = array(
                                  
                                    'doctor_id' => $doctor_id,
                                    'date' => $date,
                                    'hours' => $hours,
                                    'day'=>$day,
                                    'starttime' => $start_date,
                                    'endtime' => $end_date,
                                    'chat' => $chat,
                                    'video' => $video,
                                    'chat_fee' => $chat_fee,
                                    'video_fee' => $video_fee,
                                    'status' => '0',
                                    'created_at' => $start_date,
                                    'category'=>$category
                	            );
                    $patient_insert = $this->db->insert('doctor_online_history', $data1);
                    return array(
                    'status' => 200,
                    'message' => 'success'
                );      
       }
       else
       {
           $new=explode(".",$hours);
           $hours1="+".$new[0]." hour";
           $hours2="+".$new[1]." minutes";
          
           date_default_timezone_set('Asia/Kolkata');
           $date = date('Y-m-d');
           $start_date = date('Y-m-d H:i:s');
           $end_date = date('Y-m-d H:i:s',strtotime( $hours1.$hours2 ,strtotime($start_date)));
           $query1 = $this->db->query("SELECT category  FROM `doctor_list` WHERE user_id='$doctor_id'");       
           $row1=$query1->row_array(); 
           $category=$row1['category'];
          
          
           $data = array(
                                  
                                    'doctor_id' => $doctor_id,
                                    'date' => $date,
                                    'hours' => $hours,
                                    'day'=>$day,
                                    'starttime' => $start_date,
                                    'endtime' => $end_date,
                                    'chat' => $chat,
                                    'video' => $video,
                                    'chat_fee' => $chat_fee,
                                    'video_fee' => $video_fee,
                                    'status' => '0',
                                    'created_at' => $start_date,
                                    'category'=>$category
                	            );
                    $patient_insert = $this->db->insert('doctor_online', $data);
                    $patient_insert = $this->db->insert('doctor_online_history', $data);
                    return array(
                    'status' => 200,
                    'message' => 'success'
                );   
       }
            
        
        return $resultpost;
    }*/
   
   
   public function doctor_online($doctor_id,$hours,$chat,$video,$chat_fee,$video_fee,$day) {
        $query = $this->db->query("SELECT id  FROM `doctor_online` WHERE doctor_id='$doctor_id' and status='0'");
        $count = $query->num_rows();
       if($count > 0)
       {
          $new=explode(".",$hours);
           $hours1="+".$new[0]." hour";
           $hours2="+".$new[1]." minutes";
          
           date_default_timezone_set('Asia/Kolkata');
           $date = date('Y-m-d');
           $start_date = date('H:i:s');
           $end_date = date('H:i:s',strtotime( $hours1.$hours2 ,strtotime($start_date)));
          $query1 = $this->db->query("SELECT category  FROM `doctor_list` WHERE user_id='$doctor_id'");       
            $count1 = $query1->num_rows();
       if($count1 > 0)
       {
           $row1=$query1->row_array(); 
           $category=$row1['category'];
       }
       else
       {
           $category=0;
       }
           $data = array(
                                  
                                   
                                    'date' => $date,
                                    'hours' => $hours,
                                    'day'=>$day,
                                    'start_time' => $start_date,
                                    'end_time' => $end_date,
                                    'chat' => $chat,
                                    'video' => $video,
                                    'chat_fee' => $chat_fee,
                                    'video_fee' => $video_fee,
                                    'status' => '0',
                                    'created_at' => $start_date,
                                     'category'=>$category
                	            );
                   
                    $this->db->where('doctor_id', $doctor_id);
                    $insert = $this->db->update('doctor_online', $data);
                    	$consultation_type="";
            			if($chat=="1" && $video=="1")
            			{
            			   $consultation_type= "chat,video";
            			}
            			elseif($chat=="0" && $video=="1")
            			{
            			   $consultation_type= "video";
            			}
            			elseif($chat=="1" && $video=="0")
            			{
            			   $consultation_type= "chat";
            			}
            			elseif($chat=="0" && $video=="0")
            			{
            			   $consultation_type= "";
            			}
                    
                    
                    
                     $data_type=array
			            (
                            'consultaion_video' 	 => $video_fee,
                            'consultaion_chat' 	     => $chat_fee,
                            'consultation_type'      => $consultation_type
                        );
                    
                     $this->db->where('user_id',$doctor_id);
	            	$result = $this->db->update('doctor_list',$data_type);
                    
                    $data1 = array(
                                  
                                    'doctor_id' => $doctor_id,
                                    'date' => $date,
                                    'hours' => $hours,
                                    'day'=>$day,
                                    'starttime' => $start_date,
                                    'endtime' => $end_date,
                                    'chat' => $chat,
                                    'video' => $video,
                                    'chat_fee' => $chat_fee,
                                    'video_fee' => $video_fee,
                                    'status' => '0',
                                    'created_at' => $start_date,
                                    'category'=>$category
                	            );
                    $patient_insert = $this->db->insert('doctor_online_history', $data1);
                    $resultpost[] = array( 'days'=>$day,
                                    'start_time' => $start_date,
                                    'end_time' => $end_date); 
                    
                     $online_fees=array(
                             "chat"=>$chat,
                             "video"=>$video,
                             "chat_fee"=>$chat_fee,
                             "video_fee"=>$video_fee
                             );
                    return array(
                    'status' => 200,
                    'message' => 'success',
                    'online_details'=>$resultpost,
                    'online_fees'=>$online_fees
                );   
       }
       else
       {
           $new=explode(".",$hours);
           $hours1="+".$new[0]." hour";
           $hours2="+".$new[1]." minutes";
          
           date_default_timezone_set('Asia/Kolkata');
           $date = date('Y-m-d');
           $start_date = date('H:i:s');
            $end_date = date('H:i:s',strtotime( $hours1.$hours2 ,strtotime($start_date)));
           $query1 = $this->db->query("SELECT category  FROM `doctor_list` WHERE user_id='$doctor_id'");       
             $count1 = $query1->num_rows();
       if($count1 > 0)
       {
           $row1=$query1->row_array(); 
           $category=$row1['category'];
       }
       else
       {
           $category=0;
       }
          
          
           $data = array(
                                  
                                    'doctor_id' => $doctor_id,
                                    'date' => $date,
                                    'hours' => $hours,
                                    'day'=>$day,
                                    'starttime' => $start_date,
                                    'endtime' => $end_date,
                                    'chat' => $chat,
                                    'video' => $video,
                                    'chat_fee' => $chat_fee,
                                    'video_fee' => $video_fee,
                                    'status' => '0',
                                    'created_at' => $start_date,
                                    'category'=>$category
                	            );
                    $patient_insert = $this->db->insert('doctor_online', $data);
                    $patient_insert = $this->db->insert('doctor_online_history', $data);
                    $consultation_type="";
            		if($chat=="1" && $video=="1")
            		   {
            			   $consultation_type= "chat,video";
            		   }
            		elseif($chat=="0" && $video=="1")
            			{
            			   $consultation_type= "video";
            			}
            			elseif($chat=="1" && $video=="0")
            			{
            			   $consultation_type= "chat";
            			}
            			elseif($chat=="0" && $video=="0")
            			{
            			   $consultation_type= "";
            			}
                    
                    
                    
                     $data_type=array
			            (
                            'consultaion_video' 	 => $video_fee,
                            'consultaion_chat' 	     => $chat_fee,
                            'consultation_type'      => $consultation_type
                        );
                    
                     $this->db->where('user_id',$doctor_id);
	            	$result = $this->db->update('doctor_list',$data_type);
                    $resultpost[] = array( 'days'=>$day,
                                    'start_time' => $start_date,
                                    'end_time' => $end_date); 
                    
                      $online_fees=array(
                             "chat"=>$chat,
                             "video"=>$video,
                             "chat_fee"=>$chat_fee,
                             "video_fee"=>$video_fee
                             );
                    return array(
                    'status' => 200,
                    'message' => 'success',
                    'online_details'=>$resultpost,
                    'online_fees'=>$online_fees
                );   
       }
            
        
        return $resultpost;
    }
    
      public function doctor_online_user($doctor_id,$user_id,$fee,$online_type,$transaction){
        $query = $this->db->query("SELECT endtime  FROM `doctor_online` WHERE doctor_id='$doctor_id'");
        $data               = $query->row_array();
        $endtime        = $data['endtime'];
       
          
           date_default_timezone_set('Asia/Kolkata');
           $date = date('Y-m-d');
           $start_date = date('Y-m-d H:i:s');
          
           $data = array(
                                  
                                    'doctor_id' => $doctor_id,
                                    'user_id' => $user_id,
                                    'datetime' => $start_date,
                                    'endtime' => $endtime,
                                    'fee' => $fee,
                                    'transaction' => $transaction,
                                    'status' => '0',
                                    'online_type' => $online_type,
                                    'created_at' => $start_date,
                	            );
                    $patient_insert = $this->db->insert('doctor_online_user', $data);
                    return array(
                    'status' => 200,
                    'message' => 'success'
                );   
       
            
        
        return $resultpost;
    }
    
    
    
    public function doctor_online_list($user_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $current_date = date('Y-m-d H:i:s');
        
        $query=$this->db->query("SELECT doctor_id  FROM `doctor_online` WHERE starttime <= '$current_date'
AND endtime >= '$current_date'");
$count = $query->num_rows();
if($count > 0)
{
    foreach ($query->result_array() as $row) 
            {
                $doctor_id=$row['doctor_id'];
                 $sql   = sprintf("SELECT doctor_list.mba,doctor_list.consultaion_video,doctor_list.consultaion_chat,doctor_list.consultation_voice_call,doctor_list.recommended,doctor_list.certified,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE  doctor_list.is_active = 1 and doctor_list.user_id='$doctor_id'  group by doctor_list.user_id ");
                $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
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
                $followers           = '0';
                $following           = '0';
                $profile_views       = '0';
                $total_reviews       = '0';
                $total_rating        = '0';
                $total_profile_views = '0';
                $discount            = $row['discount'];
                $mba = $row['mba'];
                $certified = $row['certified'];
                $recommended = $row['recommended'];
                $video         = $row['consultaion_video'];
                $chat             = $row['consultaion_chat'];
                $call                = $row['consultation_voice_call'];
                
                $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                $is_follow           = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $doctor_user_id)->get()->num_rows();
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
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
                    $profile_pic = 'https://medicalwale.com/img/doctor_default.png';
                }
                $area_expertise = array();
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $category . "')");
                $total_category = $query_sp->num_rows();
                if ($total_category > 0) {
                    foreach ($query_sp->result_array() as $get_sp) {
                        $id               = $get_sp['id'];
                        $area_expertised  = $get_sp['area_expertise'];
                        $area_expertise[] = array(
                            'id' => $id,
                            'area_expertise' => $area_expertised
                        );
                    }
                } else {
                    $area_expertise = array();
                }
                $speciality_array = array();
                $speciality_      = explode(',', $speciality);
                $count_speciality = count($speciality_);
                if ($count_speciality > 1) {
                    foreach ($speciality_ as $speciality_) {
                        $speciality_array[] = array(
                            'speciality' => $speciality_
                        );
                    }
                } else {
                    $speciality_array = array();
                }
                $service_array = array();
                $service_      = explode(',', $service);
                $count_service = count($service_);
                if ($count_service > 1) {
                    foreach ($service_ as $service_) {
                        $service_array[] = array(
                            'service' => $service_
                        );
                    }
                } else {
                    $service_array = array();
                }
               /* $degree_array  = array();
                $degree_       = explode(',', $degree);
                $count_degree_ = count($degree_);
                if ($count_degree_ > 1) {
                    foreach ($degree_ as $degree_) {
                        $degree_array[] = array(
                            'degree' => $degree_
                        );
                    }
                } else {
                    $degree_array = array();
                }*/
                $new_consultation_charges=array();
                $doctor_practices = array();
                $sql2             = sprintf("SELECT `consultation_charges`, `lat`, `lng` FROM doctor_clinic WHERE doctor_id='$doctor_user_id' ");
                $query_practices  = $this->db->query($sql2);
                $total_practices  = $query_practices->num_rows();
                if ($total_practices > 0) {
                    foreach ($query_practices->result_array() as $get_pract) {
                       
                        $consultation_charges = $get_pract['consultation_charges'];
                       
                       
                        
                        
                        $new_consultation_charges[]=$consultation_charges;
                        
                     
                    }
                } else {
                   $new_consultation_charges=array();
                }
                $doctor_consultation = '';
                $consultaion         = $this->db->query("SELECT * FROM `doctor_consultation` WHERE `doctor_user_id` = '$doctor_user_id' AND is_active = '1'");
                $consult_count       = $consultaion->num_rows();
                if ($consult_count > 0) {
                    foreach ($consultaion->result_array() as $rows) {
                        $doctor_user_id        = $rows['doctor_user_id'];
                        $consultation_name     = $rows['consultation_name'];
                        $charges               = $rows['charges'];
                        $duration              = $rows['duration'];
                        $doctor_consultation[] = array(
                            'doctor_user_id' => $doctor_user_id,
                            'consultation_name' => $consultation_name,
                            'charges' => $charges,
                            'duration' => $duration
                        );
                    }
                } else {
                    $doctor_consultation = array();
                }
                
               
                if(sizeof($new_consultation_charges) > 1)
                {
                $max_consultation_charges=max($new_consultation_charges);
                $min_consultation_charges=min($new_consultation_charges);
                }
                else
                {
                  $max_consultation_charges= 0;
                  if(sizeof($new_consultation_charges) >= 1)
                  {
                  $min_consultation_charges=max($new_consultation_charges);
                  }
                  else
                  {
                     $min_consultation_charges=0; 
                  }
                }
                  if(empty($call))
                  {
                    $call="0";  
                  }
                   if(empty($chat))
                  {
                     $chat="0"; 
                  }
                   if(empty($video))
                  {
                      $video="0";
                  }
                
                $resultpost[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'listing_type' => "5",
                    'lat' => $lat,
                    'lng' => $lng,
                    'distance' => "",
                    'doctor_name' => $doctor_name,
                    'email' => $email,
                    'gender' => $gender,
                    'doctor_phone' => $doctor_phone,
                    'dob' => $dob,
                    'experience' => $experience,
                    'reg_council' => $reg_council,
                    'reg_number' => $reg_number,
                    'profile_pic' => $profile_pic,
                    'address' => $address,
                    'city' => $city,
                    'state' => $state,
                    'pincode' => $pincode,
                    'doctor_consultation' => $doctor_consultation,
                    'visit_charge'=>$min_consultation_charges.",".$max_consultation_charges,
                    'call_charge'=>$call,
                    'chat_charge'=>$chat,
                    'video_charge'=>$video,
                    'area_expertise' => $area_expertise,
                    'doctor_specialization' => $speciality_array,
                    'doctor_services' => $service_array,
                   
                    'rating' => $total_rating,
                    'review' => $total_reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'is_follow' => $is_follow,
                    'discount' => $discount,
                    'mba' => $mba,
                    'certified' => $certified,
                    'recommended' => $recommended,
                    'online_status'=>1
                );
            }
        } 
               
            }
}
else {
            $resultpost = array();
        }
        return $resultpost;

    }
    
    public function doctor_offline ($doctor_id)
    {
         $data = array(
                        'status' => '1',
                      );
                   
                    $this->db->where('doctor_id', $doctor_id);
                    $insert = $this->db->update('doctor_online', $data);
                     if($this->db->affected_rows() > 0)
            {
                $resp= array(
                'status' => 200,
                'message' => 'success'
                 ); 
            }
            else
            {
                $resp= array(
                'status' => 201,
                'message' => 'failed'
                 ); 
            }
            
        return $resp;    
            
    }
    
    
       public function doctor_complete($doctor_id,$booking_id)
    {
         $data = array(
                        'block' => '0',
                      );
                  
                    $this->db->where('doctor_id', $doctor_id);
                    $insert = $this->db->update('doctor_online', $data);
                     if($this->db->affected_rows() > 0)
            {
                $booking_array = array(
                        'status' => 1,
                        );
                 $updateStatus  = $this->db->where('doctor_id', $doctor_id)->update('doctor_online_block', $booking_array); 
                 
                 
                 $booking_array1 = array(
                        'status' => 6,
                        );
                 $updateStatus1  = $this->db->where('booking_id', $booking_id)->update('doctor_booking_master', $booking_array1); 
                $resp= array(
                'status' => 200,
                'message' => 'success'
                 ); 
            }
            else
            {
                $resp= array(
                'status' => 201,
                'message' => 'failed'
                 ); 
            }
            
        return $resp;    
            
    }
    
}
