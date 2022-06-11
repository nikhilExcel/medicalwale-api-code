<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Testmodel extends CI_Model
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
   
        $this->key = '1234567890123456';
        $this->iv  = '1234567890123456';
        $encoded = base64_encode($str);
        $module    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad   = $block - (strlen($encoded) % $block);
        $encoded .= str_repeat(chr($pad), $pad);
        $encrypted = mcrypt_generic($module, $encoded);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        //  utf8_decode($encrypted);
        return utf8_encode(base64_encode($encrypted));
    }
     public function encrypt1($str)
    {

     $key = '1234567890123456';
    // For an easy iv, MD5 the salt again.
    $iv ='1234567890123456';
    // Encrypt the session ID.
    $encryptedSessionId = base64_encode($str);
    $encrypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $encryptedSessionId, MCRYPT_MODE_CBC, $iv);
    // Base 64 encode the encrypted session ID.
    //$encryptedSessionId = base64_encode($encrypt);
    // Return it.
    return utf8_encode(base64_encode($encrypt));
    } 
    
    public function decrypt1($str)
    {

    $key = '1234567890123456';
    $iv ='1234567890123456';
    $decoded = base64_decode($str);
    $decryptedSessionId = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $decoded, MCRYPT_MODE_CBC, $iv);
    $session_id = rtrim($decryptedSessionId, "\0");
    // Return it.
     return utf8_decode(base64_decode($session_id));
    
    }
    
      public function decrypt($str)
    {
        $this->key = '1234567890123456';
        $this->iv  = '1234567890123456';
        $module    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $str = mdecrypt_generic($module, base64_decode($str));
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        $slast = ord(substr($str, -1));
        $str   = substr($str, 0, strlen($str) - $slast);
       return  $decoded = utf8_decode(base64_decode($str));
         
    }
    
    
    
 public function mcrypt($key)
    {
      /*  $data=array("1"=>1,
        "2"=>"Two",
        "3"=>False);
        $data1=json_encode($data);
       // print_r($data1);
       // die();*/
            $data=$this->decrypt($key);
            $data1=$this->encrypt($data);
           $data=array("decrypt"=>$data,
        "encrypt"=>"$data1");
       
       // print_r($data1);  
        return $data;
        
    }
    
    
    
    
      public function doctor_list($mlat, $mlng, $user_id, $category_id,$page,$keyword)
    {
        //$radius = '5';
        if($page==""){
         $page=1;   
        }
        $radius = $page*5;
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
        
        function GetDrivingDistance($lat1, $lat2, $long1, $long2)
        {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyCMHN__LczEziaYuKXQ-SKdiMfx5AiY66o&callback=initMap";
            $ch  = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
           
            $val=$response_a['rows'][0]['elements'][0]['status'];
           if($val!="ZERO_RESULTS")
           {
            $dist       = $response_a['rows'][0]['elements'][0]['distance']['text'];
           }
           else
           {
            $dist       ="";   
           }
            return $dist;
        }
        // $sql = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE FIND_IN_SET('" . $category_id . "', doctor_list.category)  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
       if($keyword!=""){
        $sql   = sprintf("SELECT doctor_list.mba,doctor_list.consultaion_video,doctor_list.consultaion_chat,doctor_list.consultation_voice_call,doctor_list.recommended,doctor_list.certified,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE FIND_IN_SET('" . $category_id . "', doctor_list.category) and  doctor_list.doctor_name LIKE '%%$keyword%%' and  doctor_list.is_active = 1  HAVING distance < '%s' ORDER BY distance  LIMIT $startk, $limitk", ($mlat), ($mlng), ($mlat), ($radius));
       }else{
         $sql   = sprintf("SELECT doctor_list.mba,doctor_list.consultaion_video,doctor_list.consultaion_chat,doctor_list.consultation_voice_call,doctor_list.recommended,doctor_list.certified,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE FIND_IN_SET('" . $category_id . "', doctor_list.category) and doctor_list.is_active = 1  HAVING distance < '%s' ORDER BY distance  LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
       }
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
                $degree_array  = array();
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
                }
                $new_consultation_charges=array();
                $doctor_practices = array();
                $sql2             = sprintf("SELECT `id`, `doctor_id`,`contact_no`, `clinic_name`,`open_hours`,`consultation_charges`, `address`, `state`, `city`, `pincode` ,`lat`, `lng`,    `image`, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( lat ) ) ) ) AS clinic_distance FROM doctor_clinic WHERE doctor_id='$doctor_user_id' HAVING clinic_distance < '%s' ORDER BY clinic_distance  LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
                $query_practices  = $this->db->query($sql2);
                $total_practices  = $query_practices->num_rows();
                if ($total_practices > 0) {
                    foreach ($query_practices->result_array() as $get_pract) {
                        $clinic_id            = $get_pract['id'];
                        $clinic_lat           = $get_pract['lat'];
                        $clinic_lng           = $get_pract['lng'];
                        $clinic_name          = $get_pract['clinic_name'];
                        $clinic_phone         = $get_pract['contact_no'];
                        $clinic_address       = $get_pract['address'];
                        $clinic_state         = $get_pract['state'];
                        $clinic_city          = $get_pract['city'];
                        $clinic_pincode       = $get_pract['pincode'];
                        $clinic_image         = $get_pract['image'];
                        $opening_hours        = $get_pract['open_hours'];
                        $consultation_charges = $get_pract['consultation_charges'];
                        if ($clinic_image != '') {
                            $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $clinic_image;
                        } else {
                            $clinic_image = '';
                        }
                        $open_days         = '';
                        $day_array_list    = '';
                        $day_list          = '';
                        $day_time_list     = '';
                        $time_list1        = '';
                        $time_list2        = '';
                        $time              = '';
                        $system_start_time = '';
                        $system_end_time   = '';
                        $time_check        = '';
                        $current_time      = '';
                        $open_close        = array();
                        $time              = array();
                        date_default_timezone_set('Asia/Kolkata');
                        $data           = array();
                        $final_Day      = array();
                        $day_array_list = explode('|', $opening_hours);
                        if (count($day_array_list) > 1) {
                            for ($i = 0; $i < count($day_array_list); $i++) {
                                $day_list = explode('>', $day_array_list[$i]);
                                for ($j = 0; $j < count($day_list); $j++) {
                                    $day_time_list = explode('-', $day_list[$j]);
                                    for ($k = 1; $k < count($day_time_list); $k++) {
                                        $time_list1 = explode(',', $day_time_list[0]);
                                        $time_list2 = explode(',', $day_time_list[1]);
                                        $time       = array();
                                        $open_close = array();
                                        for ($l = 0; $l < count($time_list1); $l++) {
                                            $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
                                            $time[]            = str_replace('close-close', 'Close', $time_check);
                                            $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                            $system_end_time   = date("h:i A", strtotime($time_list2[$l]));
                                            $current_time      = date('h:i A');
                                            $date1             = DateTime::createFromFormat('H:i a', $current_time);
                                            $date2             = DateTime::createFromFormat('H:i a', $system_start_time);
                                            $date3             = DateTime::createFromFormat('H:i a', $system_end_time);
                                            if ($date2 < $date3 && $date1 <= $date3) {
                                                $date3->modify('+1 day')->format('H:i a');
                                            } elseif ($date2 > $date3 && $date1 >= $date3) {
                                                $date3->modify('+1 day')->format('H:i a');
                                            }
                                            if ($date1 > $date2 && $date1 < $date3) {
                                                $open_close[] = 'open';
                                            } else {
                                                $open_close[] = 'closed';
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
                        $queryTiming1 = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_user_id' and consultation_type='visit'" );
               
                $countTiming1 = $queryTiming1->num_rows();
                if(count($countTiming1) > 0 )
                {
                // die();
                $open_close1=array();
                $time1=array();
                $time_array1 = array();
                $time_array11 = array();
                $time_array21 = array();
                $time_array31 = array();
                $time_slott1 = array();
                $cuutent_dayy1 = date('l');
                foreach($queryTiming1->result_array() as $row11){
                // if($countTiming){
                   
                    $timeSlotDay1 = $row11['day'];
                    $timeSlot1 = $row11['time_slot'];
                    $from_time1 = $row11['from_time'];
                    $to_time1 = $row11['to_time'];
                     
                    // echo $timeSlot;
                   if ($timeSlotDay1 == $cuutent_dayy1)
					  {
					       	 $system_start_current1 = strtotime(date("h:i A"));
        				
        					
                          	$time_check1 = date("h:i A", strtotime($from_time1)) . '-' . date("h:i A", strtotime($to_time1));
        					$time1[] = str_replace('close-close', 'Close', $time_check1);
        				
        					$current_time1 = date('h:i A');
        					$system_start_time1 = date("h:i A", strtotime($from_time1));
							$system_end_time1 = date("h:i A", strtotime($to_time1));
                			$date11 = DateTime::createFromFormat('H:i a', $current_time1);
        					$date21 = DateTime::createFromFormat('H:i a', $system_start_time1);
        					$date31 = DateTime::createFromFormat('H:i a', $system_end_time1);
        					if ($date21 < $date31 && $date11 <= $date31)
        						{
        							$date31->modify('+1 day')->format('H:i a');
        						}
        					elseif ($date21 > $date31 && $date11 >= $date31)
        						{
        							$date31->modify('+1 day')->format('H:i a');
        						}
        					if ($date11 > $date21 && $date11 < $date31)
        						{
        							$open_close1 = 'open';
        						}
        				    else
        						{
        							$open_close1 = 'closed';
        				        }	
        					}
        					
        					    
        					
        					    
        					
        			
                }


	if (count($time1) > 0)
					{
					   
					$opening_hr_final1 = implode(',', $time1);
						
					}
				  else
					{
					$opening_hr_final1 = '';
					}
                }
                else
                {
                   $opening_hr_final1 = ''; 
                   $open_close1="";
                }
                        
                        
                        $new_consultation_charges[]=$consultation_charges;
                        
                        $doctor_practices[] = array(
                            'clinic_id' => $clinic_id,
                            'clinic_lat' => $clinic_lat,
                            'clinic_lng' => $clinic_lng,
                            'clinic_image' => $clinic_image,
                            'clinic_name' => $clinic_name,
                            'clinic_phone' => $clinic_phone,
                            'consultation_charges' => $consultation_charges,
                            'clinic_address' => $clinic_address,
                            'clinic_state' => $clinic_state,
                            'clinic_city' => $clinic_city,
                            'clinic_pincode' => $clinic_pincode,
                            'opening_day' => $final_Day,
                            'working_hour'=>$opening_hr_final1
                        );
                    }
                } else {
                    $doctor_practices = array();
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
                
                $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
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
                    'distance' => $distances,
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
                    'doctor_degree' => $degree_array,
                    'doctor_practices' => $doctor_practices,
                    'rating' => $total_rating,
                    'review' => $total_reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'is_follow' => $is_follow,
                    'discount' => $discount,
                    'mba' => $mba,
                    'certified' => $certified,
                    'recommended' => $recommended
                );
            }
        } else {
            $resultpost = array();
        }
        function array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
        {
            $sort_col = array();
            foreach ($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }
            array_multisort($sort_col, $dir, $arr);
        }
        array_sort_by_column($resultpost, 'distance');
        return $resultpost;
    }
    
}