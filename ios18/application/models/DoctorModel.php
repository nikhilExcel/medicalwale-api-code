<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class DoctorModel extends CI_Model
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
    public function doctor_category($doctors_type_id,$doctors_type_name)
    {
       
        if(empty($doctors_type_name))
        {
        $query = $this->db->query("SELECT * FROM `business_category` WHERE category_id = '5' AND  FIND_IN_SET('" . $doctors_type_id . "', doctors_type_id) order by id asc");
        }
        else
        {
          $query = $this->db->query("SELECT * FROM `business_category` WHERE category_id = '5' AND  FIND_IN_SET('" . $doctors_type_id . "', doctors_type_id) AND category LIKE '%$doctors_type_name%'    order by id asc");  
        }
       
       
       
        foreach ($query->result_array() as $row) {
            $id             = $row['id'];
            $category_id    = $row['category_id'];
            $category       = $row['category'];
            $details        = $row['details'];
            $category_hindi = $row['category_hindi'];
            if ($category_hindi != '') {
                $category_hindi = preg_replace('~[\r\n]+~', '', $category_hindi);
                $decrypt_title = $this->decrypt($category_hindi);
                $encrypt_title = $this->encrypt($decrypt_title);
                if ($encrypt_title == $category_hindi) {
                    $category_hindi = $decrypt_title;
                }
            }
            
            $details_hindi  = $row['details_hindi'];
            if ($details_hindi != '') {
                $details_hindi = preg_replace('~[\r\n]+~', '', $details_hindi);
                $decrypt_protects_against = $this->decrypt($details_hindi);
                $encrypt_protects_against = $this->encrypt($decrypt_protects_against);
                if ($encrypt_protects_against == $details_hindi) {
                    $details_hindi = $decrypt_protects_against;
                }
            }
            $image          = $row['image'];
            $image          = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/doctor_category/' . $image;
            
           
             $sql   =  sprintf("SELECT doctor_id FROM doctor_online  WHERE FIND_IN_SET('$id', category) ");
             $query = $this->db->query($sql);
             $count = $query->num_rows();
            
            $resultpost[]   = array(
                'id' => $id,
                'category_id' => $category_id,
                'category' => $category,
                'details' => $details,
                'category_hindi' => $category_hindi,
                'details_hindi' => $details_hindi,
                'image' => $image,
                'online'=> $count." Online"
            );
        }
        return $resultpost;
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
     public function doctor_list_v1($mlat, $mlng, $user_id, $category_id,$page,$keyword,$type)
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
        $type1="";
        if($type=="mba")
        {
            $type1 .="and doctor_list.mba = '1'";
        }
        $Where2 ='';
        if(!empty($category_id))
        {
            $cat=str_replace(",","|",$category_id);
            
            $Where2 .= ' AND CONCAT(",", doctor_list.category, ",") REGEXP ",('.$cat.'),"';
        }
        else
        {
            $Where2 .='';
        }
       if($keyword!=""){
        $sql   = sprintf("SELECT doctor_list.mba,doctor_list.consultaion_video,doctor_list.consultaion_chat,doctor_list.consultation_voice_call,doctor_list.recommended,doctor_list.certified,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.doctor_name LIKE '%%$keyword%%' and  doctor_list.is_active = 1 $Where2 $type1 group by doctor_list.user_id HAVING distance < '%s'  ORDER BY distance  LIMIT $startk, $limitk", ($mlat), ($mlng), ($mlat), ($radius));
       }else{
         $sql   = sprintf("SELECT doctor_list.mba,doctor_list.consultaion_video,doctor_list.consultaion_chat,doctor_list.consultation_voice_call,doctor_list.recommended,doctor_list.certified,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE  doctor_list.is_active = 1 $Where2 $type1 group by doctor_list.user_id HAVING distance < '%s'  ORDER BY distance  LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
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
                $sql2             = sprintf("SELECT `consultation_charges`, `lat`, `lng`, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( lat ) ) ) ) AS clinic_distance FROM doctor_clinic WHERE doctor_id='$doctor_user_id' HAVING clinic_distance < '%s' ORDER BY clinic_distance  LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
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
                  date_default_timezone_set('Asia/Kolkata');
                    $current_date = date('Y-m-d H:i:s');
                
                    $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                
                
                
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
                    'online_status'=>$countdi
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
 
     public function doctor_list_search($mlat, $mlng, $user_id,$page,$keyword)
    {
        //$radius = '5';
        $radius = $page*5;
        $limit = 20;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
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
        
        $sql   = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE   doctor_list.doctor_name LIKE '%%$keyword%%' and  doctor_list.is_active = 1  HAVING distance < '%s' ORDER BY distance  LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
       
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
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
                $doctor_practices = array();
                $sql2             = sprintf("SELECT `id`, `doctor_id`,`contact_no`, `clinic_name`,`open_hours`,`consultation_charges`, `address`, `state`, `city`, `pincode` ,`lat`, `lng`,    `image`, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS clinic_distance FROM doctor_clinic WHERE doctor_id='$doctor_user_id' HAVING clinic_distance < '%s' ORDER BY clinic_distance desc LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
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
                        $queryTiming1 = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_user_id' " );
               
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
                    'discount' => $discount
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
    
    
    
    //added by zak for related_doctor
    
      public function doctor_related_list($user_id,$mlat, $mlng,$doctor_id)
    {
        $radius = '5';
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
        
        if($mlat == '' || $mlng == '')
        {
              $doctor_lat       = $this->db->query("SELECT lat,lng FROM users WHERE id='$listing_id'");
        $doctor_lat_count = $doctor_lat->num_rows();
        if ($doctor_lat_count > 0) {
            $doctor_lat = $doctor_lat->row_array();
            $mlat       = $doctor_lat['lat'];
            $mlng        = $doctor_lat['lng'];
            
            if($mlat != '' || $mlng !='')
            {
         
            $sql   = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 10", ($mlat), ($mlng), ($mlat), ($radius));
        
            }
            else
            {
                //mumbai lat long in worst condition
                $mlat       = '19.1286564';
                $mlng        = '72.8509587';
            
                $sql   = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 10", ($mlat), ($mlng), ($mlat), ($radius));
            }
            }
        }
        else
        {
        // $sql = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE FIND_IN_SET('" . $category_id . "', doctor_list.category)  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
        $sql   = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 10", ($mlat), ($mlng), ($mlat), ($radius));
        }
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
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
                $doctor_practices = array();
                $sql2             = sprintf("SELECT `id`, `doctor_id`,`contact_no`, `clinic_name`,`open_hours`,`consultation_charges`, `address`, `state`, `city`, `pincode` ,`lat`, `lng`,    `image`, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS clinic_distance FROM doctor_clinic WHERE doctor_id='$doctor_user_id' HAVING clinic_distance < '%s' ORDER BY clinic_distance desc LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
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
                            'opening_day' => $final_Day
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
                    'discount' => $discount
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
    
    
     public function doctor_list_gender($mlat, $mlng, $user_id, $gender)
    {
        $radius = '5';
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
            $dist       = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return $dist;
        }
        
         if ($gender=="male") {
                    $genderdata = 'female';
                } else {
                    $genderdata = 'male';
                }
        // $sql = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE FIND_IN_SET('" . $category_id . "', doctor_list.category)  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
        $sql   = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE gender='" . $genderdata . "' HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
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
      
                $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
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
                    'rating' => $total_rating,
                    'review' => $total_reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    
                    
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
    
    
   /* public function doctor_details($user_id, $listing_id)
    {
       //added for succesfull complete appointments
       $success_query = $this->db->query("SELECT * FROM doctor_booking_master WHERE listing_id='$listing_id' AND status='8'");
        $success_count = $success_query->num_rows();
       $query = $this->db->query("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id='$user_id'");
     //     $query = $this->db->query("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id='$listing_id'");
      // echo "SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id='$listing_id'";
        $count = $query->num_rows();
           $user_query = $this->db->query("SELECT * FROM users WHERE id='$user_id'");
             $user_count = $user_query->num_rows();
             if($user_count>0)
               {
                  $user_name_details = $user_query->row_array(); 
                  $view_user_name = $user_name_details['name'];
               }
        if ($count > 0) {
            //added for notification by zak
             $noti_query = $this->db->query("SELECT * FROM doctor_view_notification WHERE listing_id='$listing_id'");
             $noti_count = $success_query->num_rows();
            if($noti_count>0)
            {
                 $noti_data = $noti_query->row_array();
               $total_count = $noti_data['count'];
                if($total_count<5)
                {
                     $total_count = $total_count+1;
                    $this->db->where('users_id', $users_id)->where('listing_id', $listing_id)->update('doctor_view_notification', array(
                    'count' => $total_count
                ));
                 $this->notifyMethod_doctor_view($listing_id, $user_id, $view_user_name,$total_count);
                }
                else 
                {
                   $total_count = $total_count+1;
                    $this->db->where('users_id', $users_id)->where('listing_id', $listing_id)->update('doctor_view_notification', array(
                    'count' => $total_count
                ));
                }
            }
            else
            {
                $total_count = '1';
                $this->notifyMethod_doctor_view($listing_id, $user_id, $view_user_name,$total_count);
                 $doctor_view_notification = array(
                'user_id' => $user_id,
                'listing_id' => $listing_id,
                'count' => 1
            );
            $this->db->insert('doctor_view_notification', $doctor_view_notification);
            }
            //end by zak for notification 
            $row                 = $query->row_array();
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
            $doctor_practices = array();
            $radius           = '5';
            $sql2             = sprintf("SELECT `id`, `doctor_id`,`contact_no`, `clinic_name`,`open_hours`,`consultation_charges`, `address`, `state`, `city`, `pincode` ,`lat`, `lng`,    `image`, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS clinic_distance FROM doctor_clinic WHERE doctor_id='$doctor_user_id' HAVING clinic_distance < '%s' ORDER BY clinic_distance desc LIMIT 0 , 20", ($lat), ($lng), ($lat), ($radius));
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
                        'opening_day' => $final_Day
                    );
                }
            } else {
                $doctor_practices = array();
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
                'dob' => $dob,
                'experience' => $experience,
                'reg_council' => $reg_council,
                'reg_number' => $reg_number,
                'profile_pic' => $profile_pic,
                'address' => $address,
                'city' => $city,
                'state' => $state,
                'pincode' => $pincode,
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
                'success_apointments' => $success_count
            );
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }*/
    
    
   public function doctor_details($user_id, $listing_id)
    {
          // Doctor
                $query = $this->db->query("  SELECT doctor_list.user_id,doctor_list.mba,doctor_list.consultaion_video,doctor_list.consultaion_chat,doctor_list.consultation_voice_call,doctor_list.certified,doctor_list.recommended,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list LEFT JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id='$listing_id' limit 1");

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
                if(empty($discount))
                {
                    $discount="0";
                }
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
                $sql2             = sprintf("SELECT `id`, `doctor_id`,`contact_no`, `clinic_name`,`open_hours`,`consultation_charges`, `address`, `state`, `city`, `pincode` ,`lat`, `lng`,    `image` FROM doctor_clinic WHERE doctor_id='$doctor_user_id' ");
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
                
                $distances    = 0;
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
             return $resultpost;
    }
    //added by zak for doctor details for search 
    public function doctor_search_details($user_id,$listing_id)
    {
                // Doctor
                $query = $this->db->query("SELECT id,lat,lng,category,user_id,doctor_name,about_us,speciality,address,telephone,medical_college,medical_affiliation,charitable_affiliation,awards_recognition,all_24_hrs_available,home_visit_available,qualification,consultation_fee,experience,website,location,days,timing,image,rating,review FROM doctor_list WHERE user_id='$listing_id' limit 1");

                $count = $query->num_rows();
                if ($count > 0) {
                    $row = $query->row_array();
                    $id = $row['id'];
                    $doctor_name = $row['doctor_name'];
                    $about_us = $row['about_us'];
                    $speciality = $row['category']; //changed by deepak
                    $address = $row['address'];
                    $telephone = $row['telephone'];
                    $medical_college = $row['medical_college'];
                    $medical_affiliation = $row['medical_affiliation'];
                    $charitable_affiliation = $row['charitable_affiliation'];
                    $awards_recognition = $row['awards_recognition'];
                    $hrs_available = $row['all_24_hrs_available'];
                    $home_visit_available = $row['home_visit_available'];
                    $qualification = $row['qualification'];
                    $consultation_fee = $row['consultation_fee'];
                    $experience = $row['experience'];
                    $website = $row['website'];
                    $location = $row['location'];
                    $days = $row['days'];
                    $timing = $row['timing'];
                    $rating = $row['rating'];
                    $reviews = $row['review'];
                    $doc_image = $row['image'];
                    $doctor_user_id = $row['user_id'];
                    $profile_views = '0';


                    if ($doc_image != '') {
                        $doc_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doc_image;
                    } else {
                        $doc_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                    }

                    $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                    $following = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();

                    $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $doctor_user_id)->get()->num_rows();

                    if ($is_follow > 0) {
                        $is_follow = 'Yes';
                    } else {
                        $is_follow = 'No';
                    }



                    $result_hospital = '';
      $hospital_query = $this->db->query("SELECT * FROM `doctor_clinic` WHERE doctor_id = '$listing_id' ORDER BY id desc ");
        
        

        $count = $hospital_query->num_rows();
        // echo $count; die(); 
        if ($count > 0) {
            foreach ($hospital_query->result_array() as $row) {
               
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
                
         $discountQuery = $this->db->query("SELECT * FROM `vendor_discount` WHERE vendor_id = '$id'");  
         $disCount = $discountQuery->num_rows();
      
            if ($disCount > 0) {
                foreach ($discountQuery->result_array() as $rowDis) {
                    // print_r($rowDis); die();
                    $discount_amount = $rowDis['discount_amount'];
                    $discount_type = $rowDis['discount_type'];
                    $discount_limit = $rowDis['discount_limit'];
                    $discount_cat = $rowDis['discount_category'];
                    
                }
            } else {
                $discount_amount = 0;
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
              
                if ($row['image'] != '') {
                    $profile_pic = $row['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://medicalwale.s3.amazonaws.com/images/doctor_images/' . $profile_pic;
                } else {
                    $profile_pic = 'https://medicalwale.com/img/doctor_default.png';
                }
                
                
              
                
                
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
                     $resultpostDetails['discount_amount'] = $discount_amount;
                $resultpostDetails['discount_type'] =  $discount_type;
                $resultpostDetails['discount_limit'] = $discount_limit;
                $resultpostDetails['discount_category'] = $discount_cat;
               
                    
               
                 $result_hospital[] = $resultpostDetails;
            }
            
            
        } else {
            $result_hospital = array();
        }
                    $service = '';
                    $result_services = '';
                    $doctor_services_query = $this->db->query("SELECT service FROM `doctor_services`");
                    foreach ($doctor_services_query->result_array() as $doctor_services) {
                        $service = $doctor_services['service'];
                        $result_services[] = array(
                            'service' => $service
                        );
                    }
                    $specialization = '';
                    $result_specialization = '';
                    $doctor_specialization_query = $this->db->query("SELECT specialization FROM `doctor_specialization`");
                    foreach ($doctor_specialization_query->result_array() as $doctor_specialization) {
                        $specialization = $doctor_specialization['specialization'];
                        $result_specialization[] = array(
                            'specialization' => $specialization
                        );
                    }

                    $resultpost[] = array(
                        'doctor_id' => $id,
                        'doctor_user_id' => $doctor_user_id,
                        'name' => $doctor_name,
                        'about_us' => $about_us,
                        'speciality' => $speciality,
                        'address' => $address,
                        'telephone' => $telephone,
                        "exotel_no" => '02233721563',
                        'medical_college' => $medical_college,
                        'medical_affiliation' => $medical_affiliation,
                        'charitable_affiliation' => $charitable_affiliation,
                        'awards_recognition' => $awards_recognition,
                        'hrs_available' => $hrs_available,
                        'home_visit_available' => $home_visit_available,
                        'qualification' => $qualification,
                        'consultation_fee' => $consultation_fee,
                        'experience' => $experience,
                        'website' => $website,
                        'location' => $location,
                        'days' => $days,
                        'timing' => $timing,
                        'rating' => $rating,
                        'review' => $reviews,
                        'image' => $doc_image,
                        'followers' => $followers,
                        'following' => $following,
                        'profile_view' => $profile_views,
                        'is_follow' => $is_follow,
                        'doctor_practices' => $result_hospital,
                        'doctor_services' => $result_services,
                        'doctor_specialization' => $result_specialization
                    );
                } else {
                    $resultpost = array();
                }
             return $resultpost;
  
    }
    //end 
    
    public function review_list($user_id, $listing_id)
    {
        function get_time_difference_php($created_time)
        {
            date_default_timezone_set('Asia/Calcutta');
            $str            = strtotime($created_time);
            $today          = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years          = 60 * 60 * 24 * 365;
            $months         = 60 * 60 * 24 * 30;
            $days           = 60 * 60 * 24;
            $hours          = 60 * 60;
            $minutes        = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . " yrs ago";
            } else if (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . " yr ago";
            } else if (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . " months ago";
            } else if (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . " month ago";
            } else if (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . " days ago";
            } else if (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . " day ago";
            } else if (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . " hrs ago";
            } else if (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . " hr ago";
            } else if (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . " sec ago";
            } else {
                return "few seconds ago";
            }
        }
        $resultpost   = '';
        $review_count = $this->db->select('id')->from('doctors_review')->where('doctor_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT doctors_review.id,doctors_review.user_id,doctors_review.doctor_id,doctors_review.rating,doctors_review.review, doctors_review.service,doctors_review.date as review_date,users.id as user_id,users.name as firstname FROM `doctors_review` INNER JOIN `users` ON doctors_review.user_id=users.id WHERE doctors_review.doctor_id='$listing_id' order by doctors_review.id desc");
        //    echo "SELECT doctors_review.id,doctors_review.user_id,doctors_review.doctor_id,doctors_review.rating,doctors_review.review, doctors_review.service,doctors_review.date as review_date,users.id as user_id,users.name as firstname FROM `doctors_review` INNER JOIN `users` ON doctors_review.user_id=users.id WHERE doctors_review.doctor_id='$listing_id' order by doctors_review.id desc";
            foreach ($query->result_array() as $row) {
                $id       = $row['id'];
                $username = $row['firstname'];
                $rating   = $row['rating'];
                $review   = $row['review'];
                $user = $row['user_id'];
                $review   = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '1') {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($review)) === $review) {
                        $review = base64_decode($review);
                    }
                }
                $service      = $row['service'];
                $review_date  = $row['review_date'];
                $review_date  = get_time_difference_php($review_date);
                $like_count   = $this->db->select('id')->from('doctors_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count   = $this->db->select('id')->from('doctors_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('doctors_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $post_count,
                    'user_id'=>$user
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
      public function review_with_comment($user_id, $listing_id)
    {
        function get_time_difference_php($created_time)
        {
            date_default_timezone_set('Asia/Calcutta');
            $str            = strtotime($created_time);
            $today          = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years          = 60 * 60 * 24 * 365;
            $months         = 60 * 60 * 24 * 30;
            $days           = 60 * 60 * 24;
            $hours          = 60 * 60;
            $minutes        = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . " yrs ago";
            } else if (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . " yr ago";
            } else if (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . " months ago";
            } else if (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . " month ago";
            } else if (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . " days ago";
            } else if (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . " day ago";
            } else if (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . " hrs ago";
            } else if (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . " hr ago";
            } else if (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . " sec ago";
            } else {
                return "few seconds ago";
            }
        }
        $resultpost   = '';
         $resultcomment   = '';
          
        $review_count = $this->db->select('id')->from('doctors_review')->where('doctor_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT doctors_review.id,doctors_review.user_id,doctors_review.doctor_id,doctors_review.rating,doctors_review.review, doctors_review.service,doctors_review.date as review_date,users.id as user_id,users.name as firstname FROM `doctors_review` INNER JOIN `users` ON doctors_review.user_id=users.id WHERE doctors_review.doctor_id='$listing_id' order by doctors_review.id desc");
            foreach ($query->result_array() as $row) {
                $id       = $row['id'];
                $username = $row['firstname'];
                $rating   = $row['rating'];
                $review   = $row['review'];
                $service      = $row['service']; 
                $review_date  = $row['review_date'];
                $review_date  = get_time_difference_php($review_date);
                $like_count   = $this->db->select('id')->from('doctors_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count   = $this->db->select('id')->from('doctors_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('doctors_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
               $querycomment = $this->db->query("SELECT doctors_review_comment.id,doctors_review_comment.post_id,doctors_review_comment.comment as comment,doctors_review_comment.date,users.name,doctors_review_comment.user_id as post_user_id FROM doctors_review_comment INNER JOIN users on users.id=doctors_review_comment.user_id WHERE doctors_review_comment.post_id='$id' order by doctors_review_comment.id asc");
           $count = $querycomment->num_rows();
                        if ($count > 0) {
                        $resultcomment = array();
                            foreach ($querycomment->result_array() as $rowc) {
                                $commentid       = $rowc['id'];
                                $post_id         = $rowc['post_id'];
                                $comment         = $rowc['comment'];
                                $comment         = preg_replace('~[\r\n]+~', '', $comment);
                              
                             
                                $usernamec     = $rowc['name'];
                                $date         = $rowc['date'];
                                $post_user_id = $rowc['post_user_id'];
                                $like_countc   = $this->db->select('id')->from('doctors_review_comment_like')->where('comment_id', $commentid)->get()->num_rows();
                                $like_yes_noc  = $this->db->select('id')->from('doctors_review_comment_like')->where('comment_id', $commentid)->where('user_id', $user_id)->get()->num_rows();
                                $comment_date = get_time_difference_php($date);
                                $img_count    = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                                if ($img_count > 0) {
                                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                                    $img_file      = $profile_query->source;
                                    $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                                } else {
                                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                                }
                                $date         = get_time_difference_php($date);
                                $resultcomment[] = array(
                                    'commentid' => $commentid,
                                    'username' => $usernamec,
                                     'userimage' => $userimage,
                                    'like_count' => $like_countc,
                                    'like_yes_no' => $like_yes_noc,
                                    'post_id' => $post_id,
                                    'comment' => $comment,
                                    'comment_date' => $comment_date
                                );
                            }
                     
                        } else {
                            $resultcomment = array();
                        }
                
                    /* if ($post_count > 0) {
                         $resultcomment = array();
                        foreach ($querycomment->result_array() as $rowc) {
                          
                          
                              $comment     = $rowc['comment'];
                              $date     = $rowc['date'];
                              $date  = get_time_difference_php($date);
                              $resultcomment[] = array(
                              'comment' =>$comment,
                              'date'=> $date
                                );
                            }
                     } else {
                $resultcomment = array();
            }  */
                  
               
                
                
                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $post_count,
                    'comments'=>$resultcomment
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function review_like($user_id, $post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from doctors_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `doctors_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from doctors_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $doctors_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('doctors_review_likes', $doctors_review_likes);
            $like_query = $this->db->query("SELECT id from doctors_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }
    public function add_review($user_id, $listing_id, $rating, $review, $service)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date         = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'doctor_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('doctors_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
      public function edit_review($review_id,$user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
        );
           $this->db->where('id',$review_id);
           $this->db->where('user_id',$user_id);
           $this->db->update('doctors_review',$review_array);
        //$this->db->insert('fitness_center_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
    public function review_comment($user_id, $post_id, $comment)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at             = date('Y-m-d H:i:s');
        $doctors_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('doctors_review_comment', $doctors_review_comment);
        $doctors_review_comment_query = $this->db->query("SELECT id from doctors_review_comment where post_id='$post_id'");
        $total_comment                = $doctors_review_comment_query->num_rows();
        return array(
            'status' => 201,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }
    public function review_comment_like($user_id, $comment_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from doctors_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `doctors_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from doctors_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $doctors_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('doctors_review_comment_like', $doctors_review_comment_like);
            $comment_query      = $this->db->query("SELECT id from doctors_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }
    public function review_comment_list($user_id, $post_id)
    {
        function get_time_difference_php($created_time)
        {
            date_default_timezone_set('Asia/Calcutta');
            $str            = strtotime($created_time);
            $today          = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years          = 60 * 60 * 24 * 365;
            $months         = 60 * 60 * 24 * 30;
            $days           = 60 * 60 * 24;
            $hours          = 60 * 60;
            $minutes        = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . ' yrs ago';
            } elseif (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . ' yr ago';
            } elseif (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . ' months ago';
            } elseif (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . ' month ago';
            } elseif (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . ' days ago';
            } elseif (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . ' day ago';
            } elseif (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . ' hrs ago';
            } elseif (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . ' hr ago';
            } elseif (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . ' min ago';
            } elseif (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . ' min ago';
            } elseif (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . ' sec ago';
            } else {
                return 'few seconds ago';
            }
        }
        $query = $this->db->query("SELECT doctors_review_comment.id,doctors_review_comment.post_id,doctors_review_comment.comment as comment,doctors_review_comment.date,users.name,doctors_review_comment.user_id as post_user_id FROM doctors_review_comment INNER JOIN users on users.id=doctors_review_comment.user_id WHERE doctors_review_comment.post_id='$post_id' order by doctors_review_comment.id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id              = $row['id'];
                $post_id         = $row['post_id'];
                $comment         = $row['comment'];
                $comment         = preg_replace('~[\r\n]+~', '', $comment);
                $comment_decrypt = $this->decrypt($comment);
                $comment_encrypt = $this->encrypt($comment_decrypt);
                if ($comment_encrypt == $comment) {
                    $comment = $comment_decrypt;
                }
                $username     = $row['name'];
                $date         = $row['date'];
                $post_user_id = $row['post_user_id'];
                $like_count   = $this->db->select('id')->from('doctors_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('doctors_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);
                $img_count    = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file      = $profile_query->source;
                    $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $date         = get_time_difference_php($date);
                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'userimage' => $userimage,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'post_id' => $post_id,
                    'comment' => $comment,
                    'comment_date' => $comment_date
                );
            }
            return $resultpost;
        } else {
            $resultpost = array();
        }
    }
    public function calculate_timing($start_time, $end_time, $time_difference)
    {
        for ($day_slot = strtotime($start_time); $day_slot <= strtotime($end_time); $day_slot = $day_slot + $time_difference * 60) {
            $ctime        = date("h:i A", $day_slot);
            $time_        = date("H", $day_slot);
            $current_time = date('H');
            if ($current_time < $time_) {
                $is_available = '1';
            } else {
                $is_available = '0';
            }
            $week_time[] = array(
                'time' => $ctime,
                'is_booked' => 0,
                'is_available' => $is_available
            );
        }
        return $week_time;
    }
    public function clinic_booking_slot($clinic_id, $doctor_id)
    {
        $ac_clinic_id = $clinic_id;
        if ($clinic_id == '0') {
            $query = $this->db->query("SELECT doctor_clinic.id, doctor_clinic.doctor_id, doctor_clinic.clinic_name, doctor_clinic.address, doctor_clinic.state, doctor_clinic.city, doctor_clinic.pincode, doctor_clinic.map_location, doctor_clinic.lat, doctor_clinic.lng, doctor_clinic.contact_no,IFNULL(doctor_clinic.consultation_charges,'') AS consultation_charges, doctor_clinic.appointment_time, doctor_clinic.open_hours, doctor_clinic.image AS clinic_image, IFNULL(doctor_list.doctor_name,'') AS doctor_name, IFNULL(doctor_list.experience,'') AS experience,doctor_list.qualification,doctor_list.category,doctor_list.image AS doctor_image FROM doctor_clinic LEFT JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.doctor_id='$doctor_id'");
        } else {
            $query = $this->db->query("SELECT doctor_clinic.id, doctor_clinic.doctor_id, doctor_clinic.clinic_name, doctor_clinic.address, doctor_clinic.state, doctor_clinic.city, doctor_clinic.pincode, doctor_clinic.map_location, doctor_clinic.lat, doctor_clinic.lng, doctor_clinic.contact_no,IFNULL(doctor_clinic.consultation_charges,'') AS consultation_charges, doctor_clinic.appointment_time, doctor_clinic.open_hours, doctor_clinic.image AS clinic_image, IFNULL(doctor_list.doctor_name,'') AS doctor_name, IFNULL(doctor_list.experience,'') AS experience,doctor_list.qualification,doctor_list.category,doctor_list.image AS doctor_image FROM doctor_clinic LEFT JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id'");
        }
        $count = $query->num_rows();
        if ($count > 0) {
            $row                  = $query->row_array();
            $clinic_id            = $row['id'];
            $doctor_name          = $row['doctor_name'];
            $doctor_image         = $row['doctor_image'];
            $category             = $row['category'];
            $consultation_charges = $row['consultation_charges'];
            $degree               = $row['qualification'];
            $experience           = $row['experience'];
            $address              = $row['address'];
            $state                = $row['state'];
            $city                 = $row['city'];
            $pincode              = $row['pincode'];
            $lat                  = $row['lat'];
            $lng                  = $row['lng'];
            $appointment_time     = $row['appointment_time'];
            $time_difference_     = explode(' ', $appointment_time);
            $opening_hours        = $row['open_hours'];
            $total_rating         = '0';
            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
            }
            $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_id'");
            $row_rating   = $query_rating->row_array();
            $total_rating = $row_rating['total_rating'];
            if ($total_rating === NULL || $total_rating === '') {
                $total_rating = '0';
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
            $morning           = array();
            $afternoon         = array();
            $evening           = array();
            $time              = array();
            date_default_timezone_set('Asia/Kolkata');
            $data           = array();
            $final_Day      = array();
            /*$opening_hours = 'Monday>09:00 AM,04:00 PM-11:00 AM,06:00 PM|Tuesday>08:00 AM,02:00 PM-09:00 AM,05:00 PM|
            Wednesday>09:00 AM,04:00 PM-11:00 AM,06:00 PM|Thursday>09:00 AM,04:00 PM-11:00 AM,06:00 PM|
            Friday>09:00 AM,04:00 PM-11:00 AM,06:00 PM|Saturday>09:00 AM,04:00 PM-11:00 AM,06:00 PM|Sunday>close-close';*/
            $day_array_list = explode('|', $opening_hours);
            if (count($day_array_list) > 1) {
                $date_cal            = '2';
                $time_difference     = '0';
                $date_list           = '';
                $day_time_array_list = '';
                $check_array         = array();
                for ($date_i = 0; $date_i <= $date_cal; $date_i++) {
                    $system_date_list   = date("Y-m-d", strtotime("+ $date_i day"));
                    $date_word          = date('l', strtotime($system_date_list));
                    $morning            = array();
                    $afternoon          = array();
                    $evening            = array();
                    $booking_slot_array = array();
                    $date_word_lower    = strtolower($date_word);
                    $query_time         = $this->db->query("SELECT time_shift,time FROM doctor_clinic_timing where clinic_id='$clinic_id' and doctor_id='$doctor_id' and day='$date_word_lower'");
                    $count_time         = $query_time->num_rows();
                    if ($count_time > 0) {
                        foreach ($query_time->result_array() as $get_time) {
                            if (strtolower($get_time['time_shift']) == 'morning') {
                                $morning_time = $get_time['time'];
                                $time_list    = explode('-', $morning_time);
                                $morning      = $this->calculate_timing($time_list[0], $time_list[1], $time_difference);
                            }
                            if (strtolower($get_time['time_shift']) == 'afternoon') {
                                $afternoon_time = $get_time['time'];
                                $time_list      = explode('-', $afternoon_time);
                                $afternoon      = $this->calculate_timing($time_list[0], $time_list[1], $time_difference);
                            }
                            if (strtolower($get_time['time_shift']) == 'evening') {
                                $evening_time = $get_time['time'];
                                $time_list    = explode('-', $evening_time);
                                $evening      = $this->calculate_timing($time_list[0], $time_list[1], $time_difference);
                            }
                        }
                    }
                    $booking_slot_array[] = array(
                        'morning' => $morning,
                        'afternoon' => $afternoon,
                        'evening' => $evening
                    );
                    $final_Day[]          = array(
                        'date' => $system_date_list,
                        'day' => $date_word,
                        'slot' => $booking_slot_array
                    );
                }
            } else {
                $final_Day[] = array(
                    'day' => 'close'
                );
            }
            $doctor_consultation = '';
            $video_consultaion   = $this->db->query("SELECT * FROM `doctor_consultation` WHERE `doctor_user_id` = '$doctor_id' AND is_active = '1' AND consultation_name = 'video'");
            //$query = $this->db->query($consultaion);
            $consult_count       = $video_consultaion->num_rows();
            if ($consult_count > 0) {
                $row                 = $video_consultaion->row_array();
                $consultation_name   = $row['consultation_name'];
                $is_active           = $row['is_active'];
                $charges             = $row['charges'];
                $open_hours          = $row['open_hours'];
                $consultation_timing = array();
                $day_array_list      = explode('|', $open_hours);
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
                        $consultation_timing[] = array(
                            'day' => $day_list[0],
                            'time' => $time,
                            'status' => $open_close
                        );
                    }
                } else {
                    $consultation_timing[] = array(
                        'day' => 'close',
                        'time' => array(),
                        'status' => array()
                    );
                }
                $doctor_consultation = $consultation_timing;
            }
            if ($ac_clinic_id == '0') {
                $resultpost[] = array(
                    'doctor_name' => $doctor_name,
                    'doctor_image' => $doctor_image,
                    'area_expertise' => $area_expertise,
                    'consultation_charges' => $consultation_charges,
                    'experience' => $experience,
                    'doctor_degree' => $degree_array,
                    'address' => $address,
                    'state' => $state,
                    'city' => $city,
                    'pincode' => $pincode,
                    'rating' => $total_rating,
                    'video_consultation_booking_slots' => $final_Day,
                    'call_consultation_booking_slots' => $final_Day,
                    'chat_consultation_booking_slots' => $final_Day
                );
            } else {
                $resultpost[] = array(
                    'clinic_id' => $clinic_id,
                    'doctor_name' => $doctor_name,
                    'doctor_image' => $doctor_image,
                    'area_expertise' => $area_expertise,
                    'consultation_charges' => $consultation_charges,
                    'experience' => $experience,
                    'doctor_degree' => $degree_array,
                    'address' => $address,
                    'state' => $state,
                    'city' => $city,
                    'pincode' => $pincode,
                    'rating' => $total_rating,
                    'inperson_booking_slots' => $final_Day,
                    'video_consultation_booking_slots' => $final_Day,
                    'call_consultation_booking_slots' => $final_Day,
                    'chat_consultation_booking_slots' => $final_Day
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
   
    public function doctor_views($user_id, $listing_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        if(($user_id != '1515' ) || ($user_id != '1625') || ($user_id != '11861') || ($user_id != '24351'))
        {
        $date               = date('Y-m-d H:i:s');
        $doctor_views_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'date' => $date
        );
        $this->db->insert('doctor_views', $doctor_views_array);
        
        
        $user_query = $this->db->query("SELECT * FROM users WHERE id='$user_id'");
             $user_count = $user_query->num_rows();
             if($user_count>0)
               {
                  $user_name_details = $user_query->row_array(); 
                  $view_user_name = $user_name_details['name'];
               }
        
          //added for motification by zak
             $noti_query = $this->db->query("SELECT * FROM doctor_view_notification WHERE listing_id='$listing_id'");
             $noti_count = $noti_query->num_rows();
            if($noti_count>0)
            {
                 $noti_data = $noti_query->row_array();
               $total_count = $noti_data['count'];
                if($total_count<5)
                {
                     $total_count = $total_count+1;
                    $this->db->where('user_id', $user_id)->where('listing_id', $listing_id)->update('doctor_view_notification', array(
                    'count' => $total_count
                ));
                 $this->notifyMethod_doctor_view($listing_id, $user_id, $view_user_name,$total_count);
                }
                else 
                {
                   $total_count = $total_count+1;
                    $this->db->where('user_id', $user_id)->where('listing_id', $listing_id)->update('doctor_view_notification', array(
                    'count' => $total_count
                ));
                }
            }
            else
            {
                $total_count = '1';
                $this->notifyMethod_doctor_view($listing_id, $user_id, $view_user_name,$total_count);
                 $doctor_view_notification = array(
                'user_id' => $user_id,
                'listing_id' => $listing_id,
                'count' => 1
            );
            $this->db->insert('doctor_view_notification', $doctor_view_notification);
            }
            //end by zak for notification 
        
        $doctor_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $listing_id)->get()->num_rows();
        
        return array(
            'status' => 200,
            'message' => 'success',
            'doctor_views' => $doctor_views
        );
        }
        else
        {
            return array(
            'status' => 200,
            'message' => 'success',
            'doctor_views' => ""
        );
        }
    }
    /*Gender don't update doubt in it.*/
   /* public function add_bookings($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $is_user, $is_patient_added, $patient_id, $health_condition, $allergies, $heradiatry_problem, $description, $relationship, $date_of_birth, $connect_type)
    {
        
        
        
        date_default_timezone_set('Asia/Kolkata');
        $booking_id           = date("YmdHis");
        $date                 = date('Y-m-d H:i:s');
        $status               = '1'; //awaiting for confirmation  // pending
        $from_time = date("H:i:s", strtotime($from_time));
        $to_time =  date("H:i:s", strtotime($to_time));
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'clinic_id' => $clinic_id,
            'booking_date' => $booking_date,
            'booking_time' => $booking_time,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'status' => $status,
            'description' => $description,
            'created_date' => $date,
            'consultation_type' => $connect_type
        );
        $insert               = $this->db->insert('doctor_booking_master', $booking_master_array);
        $appointment_id       = $this->db->insert_id();
        if ($appointment_id > 0) {
            $booking_master_array_patient = array('patient_id' => $appointment_id);
               $this->db->where('booking_id', $booking_id)->update('doctor_booking_master', $booking_master_array_patient);
            $patient_master_array = array(
                'user_id' => $user_id,
                'clinic_id' =>$clinic_id, 
                'booking_id' => $appointment_id,
                'patient_name' => $user_name,   
                'contact_no' => $user_mobile,
                'email' => $user_email,
                'gender' => $user_gender,
                'health_condition' => $health_condition,
                'allergies' => $allergies,
                'heradiatry_problem' => $heradiatry_problem,
                'created_date' => $date,
                'type' => $connect_type,
                'date_of_birth' => $date_of_birth,
                'doctor_id' => $listing_id
            );
            $this->db->insert('doctor_patient', $patient_master_array);
            $doctor_patient_id = $this->db->insert_id();
            if ($doctor_patient_id < 0) {
                return array(
                    'status' => 208,
                    'message' => 'Database Insert Issue'
                );
            }
        } else {
            return array(
                'status' => 208,
                'message' => 'Database Insert Issue'
            );
        }
        
        
        /* 0 for No 
        1 for Yes */
       
    //     if ($is_user != "1") {
    //         if ($is_patient_added != "1") {
    //             //insert query  
    //             $health_record = array(
    //                 'user_id' => $user_id,
    //                 'patient_name' => $user_name,
    //                 'relationship' => $relationship,
    //                 'date_of_birth' => $date_of_birth,
    //                 'gender' => $user_gender,
    //                 'created_at' => $date,
    //                 'health_condition' => $health_condition,
    //                 'allergies' => $allergies,
    //                 'heradiatry_problem' => $heradiatry_problem,
    //             );
    //             $this->db->insert('health_record', $health_record);
    //             $patient_id = $this->db->insert_id();
                

    //             //patient_id
    //             //added for healthrecord and patient detail record link
    //             $health_record = array('id' => $appointment_id);
    //             $this->db->where('id', $patient_id)->update('health_record', $health_record);
    //             //end
    //              // $doctor_booking_master = array('patient_id' => $patient_id);
    //           //  $updatePatientid  = $this->db->where('id', $appointment_id)->update('doctor_booking_master', $doctor_booking_master);
    //         } else {
    //             // update query
    //             $count_user = $this->db->select('id')->from('health_record')->where('id', $patient_id)->get()->num_rows();
    //             if ($count_user > 0) {
    //                 $health_record = array(
    //                     'user_id' => $user_id,
    //                     'patient_name' => $user_name,
    //                     'relationship' => $relationship,
    //                     'date_of_birth' => $date_of_birth,
    //                     'gender' => $user_gender,
    //                     'created_at' => $date,
    //                     'health_condition' => $health_condition,
    //                     'allergies' => $allergies,
    //                     'heradiatry_problem' => $heradiatry_problem,
    //                 );
    //                 $updateStatus  = $this->db->where('id', $patient_id)->update('health_record', $health_record);
    //           //added for healthrecord and patient detail record link
    //             $health_record = array('id' => $appointment_id);
    //             $this->db->where('id', $patient_id)->update('health_record', $health_record);
    //             //end
    //           // $doctor_booking_master = array('patient_id' => $patient_id);
    //           //  $updatePatientid  = $this->db->where('id', $appointment_id)->update('doctor_booking_master', $doctor_booking_master);
                  
    //                 if (!$updateStatus) {
    //                     return array(
    //                         'status' => 204,
    //                         'message' => 'Update failed'
    //                     );
    //                 }
    //             } else {
    //                 return array(
    //                     'status' => 208,
    //                     'message' => 'Relative data not found'
    //                 );
    //             }
    //         }
            
    //     } else {
    //         //update user fields
    //         //added for temp 
    //          $health_record = array(
    //                 'user_id' => $user_id,
    //                 'patient_name' => $user_name,
    //                 'relationship' => $relationship,
    //                 'date_of_birth' => $date_of_birth,
    //                 'gender' => $user_gender,
    //                 'created_at' => $date,
    //                 'health_condition' => $health_condition,
    //                 'allergies' => $allergies,
    //                 'heradiatry_problem' => $heradiatry_problem,
    //             );
    //             $this->db->insert('health_record', $health_record);
    //             $patient_id = $this->db->insert_id();
    //             //patient_id
    //             //added for healthrecord and patient detail record link
    //             $health_record = array('id' => $appointment_id);
    //             $this->db->where('id', $patient_id)->update('health_record', $health_record);
            
    //         $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
    //         if ($count_user > 0) {
    //             $user_data    = array(
    //                 'health_condition' => $health_condition,
    //                 'allergies' => $allergies,
    //                 'updated_at' => $date,
    //                 'heradiatry_problem' => $heradiatry_problem
    //             );
    //             $updateStatus = $this->db->where('id', $user_id)->update('users', $user_data);
    //             if (!$updateStatus) {
    //                 return array(
    //                     'status' => 204,
    //                     'message' => 'Update failed'
    //                 );
    //             }
    //         } else {
    //             return array(
    //                 'status' => 208,
    //                 'message' => 'User not found'
    //             );
    //         }
    //     }
    //     $query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name,doctor_list.telephone FROM doctor_clinic INNER JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$listing_id'");
    //     $data               = $query_dr->row_array();
    //     $doctor_name        = $data['doctor_name'];
    //     $clinic_name        = $data['clinic_name'];
    //     $doctor_phone       = $data['telephone'];
    //     $final_booking_date = date('M-d', strtotime($booking_date));
        
    //     if ($insert) {
    //         $message      = 'Your appointment with ' . $doctor_name . ' at ' . $clinic_name . ' for ' . $final_booking_date . ', ' . $booking_time . ' is confirmed. Thanks Medicalwale.com';
    //         $post_data    = array(
    //             'From' => '02233721563',
    //             'To' => $user_mobile,
    //             'Body' => $message
    //         );
    //         $exotel_sid   = "aegishealthsolutions";
    //         $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
    //         $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
    //         $ch           = curl_init();
    //         curl_setopt($ch, CURLOPT_VERBOSE, 1);
    //         curl_setopt($ch, CURLOPT_URL, $url);
    //         curl_setopt($ch, CURLOPT_POST, 1);
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //         curl_setopt($ch, CURLOPT_FAILONERROR, 0);
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //         curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    //         $http_result = curl_exec($ch);
    //         curl_close($ch);
    //         $type_of_order = 'booking';
    //         $login_url     = 'https://doctor.medicalwale.com';
    //         $message2      = 'You have received ' . $type_of_order . ', kindly login to ' . $login_url . '. Thank you.';
    //         $post_data2    = array(
    //             'From' => '02233721563',
    //             'To' => $doctor_phone,
    //             'Body' => $message2
    //         );
    //         $exotel_sid2   = "aegishealthsolutions";
    //         $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
    //         $url2          = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
    //         $ch2           = curl_init();
    //         curl_setopt($ch2, CURLOPT_VERBOSE, 1);
    //         curl_setopt($ch2, CURLOPT_URL, $url2);
    //         curl_setopt($ch2, CURLOPT_POST, 1);
    //         curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
    //         curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
    //         curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
    //         curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
    //         $http_result2 = curl_exec($ch2);
    //         curl_close($ch2);
    //     }
    //   $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
    //     $this->notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $user_name);
    //     return array(
    //         'booking_id' => $booking_id
    //     );
    // }*/
  
  /* public function add_bookings($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $is_user, $is_patient_added, $patient_id, $health_condition, $allergies, $heradiatry_problem, $description, $relationship, $date_of_birth, $connect_type,$status)
    {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id           = date("YmdHis");
        $date                 = date('Y-m-d H:i:s');
        $status               = '1'; //awaiting for confirmation  // pending
        $from_time = date("H:i:s", strtotime($from_time));
        $to_time =  date("H:i:s", strtotime($to_time));
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $user_id,
            'listing_id' => $listing_id,
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
        $insert = $this->db->insert('doctor_booking_master', $booking_master_array);
         $appointment_id       = $this->db->insert_id();
        
             if($insert){
            
                $data= $this->DoctorModel->doctor_notification_confirm($booking_id);
                 $message = 'Dear '.$data->name.', your booking has been confirm With  '.$data->doctor_name.'  Your '.'Booking ID is '.$data->booking_id.' '.'Thank you.';
                 $to = $data->email;
                 $subject = 'medicalwale Appointment List';
                 $msg = $message;
                 $this->Send_Email_Notification($to,$subject,$msg);
       
      
        }
       
        if ($appointment_id > 0) {
            $booking_master_array_patient = array('patient_id' => $patient_id);
               $this->db->where('booking_id', $booking_id)->update('doctor_booking_master', $booking_master_array_patient);
            $patient_master_array = array(
                'user_id' => $user_id,
                'clinic_id' =>$clinic_id, 
                'booking_id' => $appointment_id,
                'patient_name' => $user_name,   
                'contact_no' => $user_mobile,
                'email' => $user_email,
                'gender' => $user_gender,
                'health_condition' => $health_condition,
                'allergies' => $allergies,
                'heradiatry_problem' => $heradiatry_problem,
                'created_date' => $date,
                'type' => $connect_type,
                'date_of_birth' => $date_of_birth,
                'doctor_id' => $listing_id
            );
            $this->db->insert('doctor_patient', $patient_master_array);
            $doctor_patient_id = $this->db->insert_id();
            if ($doctor_patient_id < 0) {
                return array(
                    'status' => 208,
                    'message' => 'Database Insert Issue'
                );
            }
        } else {
            return array(
                'status' => 208,
                'message' => 'Database Insert Issue'
            );
        }
        
        
        
       
        if ($is_user != "1") {
            if ($is_patient_added != "1") {
                //insert query  
                $health_record = array(
                    'user_id' => $user_id,
                    'patient_name' => $user_name,
                    'relationship' => $relationship,
                    'date_of_birth' => $date_of_birth,
                    'gender' => $user_gender,
                    'created_at' => $date,
                    'health_condition' => $health_condition,
                    'allergies' => $allergies,
                    'heradiatry_problem' => $heradiatry_problem,
                );
                $this->db->insert('health_record', $health_record);
                $patient_id = $this->db->insert_id();
                

                //patient_id
                //added for healthrecord and patient detail record link
              //  $health_record = array('id' => $appointment_id);
              //  $this->db->where('id', $patient_id)->update('health_record', $health_record);
                
                 $booking_master_array_patient = array('patient_id' => $patient_id);
               $this->db->where('booking_id', $booking_id)->update('doctor_booking_master', $booking_master_array_patient);
                //end
                 // $doctor_booking_master = array('patient_id' => $patient_id);
              //  $updatePatientid  = $this->db->where('id', $appointment_id)->update('doctor_booking_master', $doctor_booking_master);
            } else {
                // update query
              /*  $count_user = $this->db->select('id')->from('health_record')->where('id', $patient_id)->get()->num_rows();
                if ($count_user > 0) {
                    $health_record = array(
                        'user_id' => $user_id,
                        'patient_name' => $user_name,
                        'relationship' => $relationship,
                        'date_of_birth' => $date_of_birth,
                        'gender' => $user_gender,
                        'created_at' => $date,
                        'health_condition' => $health_condition,
                        'allergies' => $allergies,
                        'heradiatry_problem' => $heradiatry_problem,
                    );
                    $updateStatus  = $this->db->where('id', $patient_id)->update('health_record', $health_record);
                //added for healthrecord and patient detail record link
                //temp comment by zak on 1 DECEmber 2018
                //  $health_record = array('id' => $appointment_id);
                //$this->db->where('id', $patient_id)->update('health_record', $health_record);
                //end
                 $booking_master_array_patient = array('patient_id' => $patient_id);
               $this->db->where('booking_id', $booking_id)->update('doctor_booking_master', $booking_master_array_patient);*/
                //end
               // $doctor_booking_master = array('patient_id' => $patient_id);
              //  $updatePatientid  = $this->db->where('id', $appointment_id)->update('doctor_booking_master', $doctor_booking_master);
                  
                    /*if (!$updateStatus) {
                        return array(
                            'status' => 204,
                            'message' => 'Update failed'
                        );
                    }*/
               /* } else {
                    return array(
                        'status' => 208,
                        'message' => 'Relative data not found'
                    );
                }
            }
            
        } else {
            //update user fields
            //added for temp 
             /*$health_record = array(
                    'user_id' => $user_id,
                    'patient_name' => $user_name,
                    'relationship' => $relationship,
                    'date_of_birth' => $date_of_birth,
                    'gender' => $user_gender,
                    'created_at' => $date,
                    'health_condition' => $health_condition,
                    'allergies' => $allergies,
                    'heradiatry_problem' => $heradiatry_problem,
                );
                $this->db->insert('health_record', $health_record);
                $patient_id = $this->db->insert_id();*/
                //patient_id
                //added for healthrecord and patient detail record link
             //   $health_record = array('id' => $appointment_id);
              //  $this->db->where('id', $patient_id)->update('health_record', $health_record);
              
             /*  $booking_master_array_patient = array('patient_id' => $patient_id);
               $this->db->where('booking_id', $booking_id)->update('doctor_booking_master', $booking_master_array_patient);
           
            $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
            if ($count_user > 0) {
                $user_data    = array(
                    'health_condition' => $health_condition,
                    'allergies' => $allergies,
                    'updated_at' => $date,
                    'heradiatry_problem' => $heradiatry_problem
                );
                $updateStatus = $this->db->where('id', $user_id)->update('users', $user_data);
                if (!$updateStatus) {
                    return array(
                        'status' => 204,
                        'message' => 'Update failed'
                    );
                }
            } else {
                return array(
                    'status' => 208,
                    'message' => 'User not found'
                );
            }
        }
        $query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name,doctor_list.telephone FROM doctor_clinic INNER JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$listing_id'");
        $data               = $query_dr->row_array();
        $doctor_name        = $data['doctor_name'];
        $clinic_name        = $data['clinic_name'];
        $doctor_phone       = $data['telephone'];
        $final_booking_date = date('M-d', strtotime($booking_date));
        
        if ($insert) {
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
        }
      $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
        $this->notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
        return array(
            'booking_id' => $booking_id
        );
    }
    */
    
    
    public function add_bookings($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $is_user, $is_patient, $patient_id,$allergies, $heradiatry_problem, $description, $relationship, $date_of_birth, $connect_type,$coupon_id,$status)
    {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id           = date("YmdHis");
        $date                 = date('Y-m-d H:i:s');
        $status               = '1'; //awaiting for confirmation  // pending
        $from_time = date("H:i:s", strtotime($from_time));
        $to_time =  date("H:i:s", strtotime($to_time));
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $user_id,
            'listing_id' => $listing_id,
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
        $insert = $this->db->insert('doctor_booking_master', $booking_master_array);
        $appointment_id       = $this->db->insert_id();
        if($insert)
            {
                $data= $this->DoctorModel->doctor_notification_confirm($booking_id);
                $message = 'Dear '.$data->name.', your booking has been confirm With  '.$data->doctor_name.'  Your '.'Booking ID is '.$data->booking_id.' '.'Thank you.';
                $to = $data->email;
                $subject = 'medicalwale Appointment List';
                $msg = $message;
                $this->Send_Email_Notification($to,$subject,$msg);
            }
        if ($appointment_id > 0) 
            {
                
                
                date_default_timezone_set('Asia/Kolkata');
        $date         = date('Y-m-d H:i:s');
        //$status = strtolower($status);
        $status       = $status; // 2 - if doctor confirm timing ,4 doctor cancelled timing ,6 reschedule
        //echo "SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'";
        $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$appointment_id'");
        $count_user   = $table_record->num_rows();
        if ($count_user > 0) {
            $booking_array = array(
                'status' => $status,
                'created_date' => $date
            );
            $updateStatus  = $this->db->where('booking_id', $appointment_id)->update('doctor_booking_master', $booking_array);
            if (!$updateStatus) {
                return array(
                    'status' => 204,
                    'message' => 'Update failed'
                );
            }
            if ($status == '5') //user confirm, payment pending
                {
                $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->confirm_status($user_id, $appointment_id, $doctor_id);
            }
            else if ($status == '8')
            {
                 $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->confirm_cash_on_delivery_status($user_id, $appointment_id, $doctor_id);
            }
            else if ($status == '3') //cancel appointment 
                {
                $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->cancel_status($user_id, $appointment_id, $doctor_id);
            }
        }
                
                
                
                $patient_master_array = array(
                    'user_id' => $user_id,
                    'clinic_id' =>$clinic_id, 
                    'booking_id' => $appointment_id,
                    'patient_name' => $user_name,   
                    'contact_no' => $user_mobile,
                    'email' => $user_email,
                    'gender' => $user_gender,
                    
                    'allergies' => $allergies,
                    'heradiatry_problem' => $heradiatry_problem,
                    'created_date' => $date,
                    'type' => $connect_type,
                    'date_of_birth' => $date_of_birth,
                    'doctor_id' => $listing_id
                   );
                $this->db->insert('doctor_patient', $patient_master_array);
                $doctor_patient_id = $this->db->insert_id();
                if ($doctor_patient_id < 0) 
                   {
                    return array(
                        'status' => 208,
                        'message' => 'Database Insert Issue'
                    );
                   }
            } 
        else 
           {
                return array(
                    'status' => 208,
                    'message' => 'Database Insert Issue'
                );
           }
        if($patient_id==0)
        {
        $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
            if ($count_user > 0) {
                $user_data    = array(
                   
                    'allergies' => $allergies,
                    'updated_at' => $date,
                    'heradiatry_problem' => $heradiatry_problem
                );
                $updateStatus = $this->db->where('id', $user_id)->update('users', $user_data);
                if (!$updateStatus) {
                    return array(
                        'status' => 204,
                        'message' => 'Update failed'
                    );
                }
            } else {
                return array(
                    'status' => 208,
                    'message' => 'User not found'
                );
            }
        }
        elseif(!empty($patient_id) || $patient_id!=0)
        {
            $count_user = $this->db->select('id')->from('users')->where('id', $patient_id)->get()->num_rows();
            if ($count_user > 0) {
                $user_data    = array(
                    //'health_condition' => $health_condition,
                    'allergies' => $allergies,
                    'updated_at' => $date,
                    'heradiatry_problem' => $heradiatry_problem
                );
                $updateStatus = $this->db->where('id', $patient_id)->update('users', $user_data);
                if (!$updateStatus) {
                    return array(
                        'status' => 204,
                        'message' => 'Update failed'
                    );
                }
            } else {
                return array(
                    'status' => 208,
                    'message' => 'User not found'
                );
            }  
        }
        $query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name,doctor_list.telephone FROM doctor_clinic INNER JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$listing_id'");
        $data               = $query_dr->row_array();
        $doctor_name        = $data['doctor_name'];
        $clinic_name        = $data['clinic_name'];
        $doctor_phone       = $data['telephone'];
        $final_booking_date = date('M-d', strtotime($booking_date));
        
        if ($insert) {
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
        }
      $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
        $this->notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
       // $this->notifyMethod_doctor($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
        if($coupon_id=="0" || empty($coupon_id))
        {
            
        }
        else
        {
            $user_data_dh = array(
                                    'use_status' => 1
                                 );
            $updateStatus = $this->db->where('coupon',$coupon_id)->update('use_coupon', $user_data_dh); 
        }
        return array(
            'booking_id' => $booking_id
        );
    }  
    
    public function add_bookings_v2($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $is_user, $is_patient, $patient_id,$allergies, $heradiatry_problem, $description, $relationship, $date_of_birth, $connect_type,$coupon_id,$status,$listing_type,$type)
    {
       if($type=="1")
       {
         date_default_timezone_set('Asia/Kolkata');
        $booking_id           = date("YmdHis");
        $date                 = date('Y-m-d H:i:s');
       // $status               = '1'; //awaiting for confirmation  // pending
        $from_time = date("H:i:s", strtotime($from_time));
        $to_time =  date("H:i:s", strtotime($to_time));
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $user_id,
            'listing_id' => $listing_id,
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
            'vendor_type'=>$listing_type
        );
        $insert = $this->db->insert('doctor_booking_master', $booking_master_array);
        $appointment_id       = $this->db->insert_id();
        if($insert)
            {
                $data= $this->DoctorModel->doctor_notification_confirm($booking_id);
                $message = 'Dear '.$data->name.', your booking has been confirm With  '.$data->doctor_name.'  Your '.'Booking ID is '.$data->booking_id.' '.'Thank you.';
                $to = $data->email;
                $subject = 'medicalwale Appointment List';
                $msg = $message;
                $this->Send_Email_Notification($to,$subject,$msg);
                
                
                   $query_dr1           = $this->db->query("SELECT name FROM users  WHERE id='$user_id'");
        $data1               = $query_dr1->row_array();
        $user_name        = $data1['name'];
       
        
         $this->notifyMethod_doctor($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $user_name,$from_time);
            }
        if ($appointment_id > 0) 
            {
                
                
                date_default_timezone_set('Asia/Kolkata');
                $date = date('Y-m-d H:i:s');
                $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'");
                $count_user   = $table_record->num_rows();
                if ($count_user > 0) {
                  
                    if ($status == '5') //user confirm, payment pending
                        {
                        $row       = $table_record->row();
                        $user_id   = $row->user_id;
                        $doctor_id = $row->listing_id;
                        $this->confirm_status($user_id, $booking_id, $doctor_id);
                    }
                    else if ($status == '8')
                    {
                         $row       = $table_record->row();
                        $user_id   = $row->user_id;
                        $doctor_id = $row->listing_id;
                        $this->confirm_cash_on_delivery_status($user_id, $booking_id, $doctor_id);
                    }
                    else if ($status == '3') //cancel appointment 
                        {
                        $row       = $table_record->row();
                        $user_id   = $row->user_id;
                        $doctor_id = $row->listing_id;
                        $this->cancel_status($user_id, $booking_id, $doctor_id);
                    }
        }
                
                
                
                $patient_master_array = array(
                    'user_id' => $user_id,
                    'clinic_id' =>$clinic_id, 
                    'booking_id' => $appointment_id,
                    'patient_name' => $user_name,   
                    'contact_no' => $user_mobile,
                    'email' => $user_email,
                    'gender' => $user_gender,
                    'allergies' => $allergies,
                    'heradiatry_problem' => $heradiatry_problem,
                    'created_date' => $date,
                    'type' => $connect_type,
                    'date_of_birth' => $date_of_birth,
                    'doctor_id' => $listing_id
                   );
                $this->db->insert('doctor_patient', $patient_master_array);
                $doctor_patient_id = $this->db->insert_id();
                if ($doctor_patient_id < 0) 
                   {
                    return array(
                        'status' => 208,
                        'message' => 'Database Insert Issue'
                    );
                   }
            } 
        else 
           {
                return array(
                    'status' => 208,
                    'message' => 'Database Insert Issue'
                );
           }
        if($patient_id==0)
        {
        $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
            if ($count_user > 0) {
                $user_data    = array(
                   
                    'allergies' => $allergies,
                    'updated_at' => $date,
                    'heradiatry_problem' => $heradiatry_problem
                );
                $updateStatus = $this->db->where('id', $user_id)->update('users', $user_data);
                if (!$updateStatus) {
                    return array(
                        'status' => 204,
                        'message' => 'Update failed'
                    );
                }
            } else {
                return array(
                    'status' => 208,
                    'message' => 'User not found'
                );
            }
        }
        elseif(!empty($patient_id) || $patient_id!=0)
        {
            $count_user = $this->db->select('id')->from('users')->where('id', $patient_id)->get()->num_rows();
            if ($count_user > 0) {
                $user_data    = array(
                    //'health_condition' => $health_condition,
                    'allergies' => $allergies,
                    'updated_at' => $date,
                    'heradiatry_problem' => $heradiatry_problem
                );
                $updateStatus = $this->db->where('id', $patient_id)->update('users', $user_data);
                if (!$updateStatus) {
                    return array(
                        'status' => 204,
                        'message' => 'Update failed'
                    );
                }
            } else {
                return array(
                    'status' => 208,
                    'message' => 'User not found'
                );
            }  
        }
        $query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name,doctor_list.telephone FROM doctor_clinic INNER JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$listing_id'");
        $data               = $query_dr->row_array();
        $doctor_name        = $data['doctor_name'];
        $clinic_name        = $data['clinic_name'];
        $doctor_phone       = $data['telephone'];
        $final_booking_date = date('M-d', strtotime($booking_date));
        
        if ($insert) 
        {
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
        }
        $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
        $this->notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
        if($coupon_id=="0" || empty($coupon_id))
        {
            
        }
        else
        {
           $user_data_dh    = array(
                   
                    'use_status' => 1
                   
                );
                $updateStatus = $this->db->where('coupon',$coupon_id)->update('use_coupon', $user_data_dh); 
        }
        return array(
            'booking_id' => $booking_id
        );  
       }
       elseif($type=="2")
       {
            $sql0 = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$listing_id'");
            $query_practices0  = $this->db->query($sql0);
            $total_practices0  = $query_practices0->num_rows();
            $get_pract0=$query_practices0->row_array();
            $ids=$get_pract0['hospital_doctor_id'];  
           if(!empty($ids))
           {
              $sql21             = "SELECT hd.*,hd.id as doctor_id,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.id IN ($ids) and h.user_id ='$clinic_id' ";
               //$query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name FROM hospitals INNER JOIN doctor_list ON doctor_list.=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$listing_id'");
                $query_practices0  = $this->db->query($sql21);
                 $count_user         = $query_practices0->num_rows();
                 if($count_user > 0)
                 {
                     
                    date_default_timezone_set('Asia/Kolkata');
            $booking_id           = date("YmdHis");
            $date                 = date('Y-m-d H:i:s');
           // $status               = '1'; //awaiting for confirmation  // pending
            $from_time = date("H:i:s", strtotime($from_time));
            $to_time =  date("H:i:s", strtotime($to_time));
            $booking_master_array = array(
                'booking_id' => $booking_id,
                'user_id' => $user_id,
                'listing_id' => $listing_id,
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
                'vendor_type'=>5
            );
            $insert = $this->db->insert('doctor_booking_master', $booking_master_array);
            $appointment_id       = $this->db->insert_id();
            $get_pract01=$query_practices0->row_array();
                    $id_doctor=$get_pract01['doctor_id'];
            
            $booking_master_array1 = array(
                'booking_id' => $booking_id,
                'user_id' => $user_id,
                'doctor_id' => $id_doctor,
                'listing_id' => $clinic_id,
                'booking_date' => $booking_date,
                'booking_time' => $booking_time,
                'from_time' => $from_time,
                'to_time' => $to_time,
                'status' => $status,
                'description' => $description,
                'created_date' => $date,
                'patient_id' => $patient_id,
                'consultation_type' => $connect_type,
                'vendor_type'=>50
            );
            $insert1 = $this->db->insert('hospital_booking_master', $booking_master_array1);
            if($insert)
                {
                    $data= $this->DoctorModel->doctor_notification_confirm($booking_id);
                    $message = 'Dear '.$data->name.', your booking has been confirm With  '.$data->doctor_name.'  Your '.'Booking ID is '.$data->booking_id.' '.'Thank you.';
                    $to = $data->email;
                    $subject = 'medicalwale Appointment List';
                    $msg = $message;
                    $this->Send_Email_Notification($to,$subject,$msg);
                    $query_dr1           = $this->db->query("SELECT name FROM users  WHERE id='$user_id'");
                    $data1               = $query_dr1->row_array();
                    $user_name        = $data1['name'];
                    $this->notifyMethod_doctor($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $user_name,$from_time);
                }
            if ($appointment_id > 0) 
                {
                    date_default_timezone_set('Asia/Kolkata');
                    $date = date('Y-m-d H:i:s');
                    $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'");
                    $count_user   = $table_record->num_rows();
                    if ($count_user > 0) {
                      
                        if ($status == '5') //user confirm, payment pending
                            {
                            $row       = $table_record->row();
                            $user_id   = $row->user_id;
                            $doctor_id = $row->listing_id;
                            $this->confirm_status($user_id, $booking_id, $doctor_id);
                        }
                        else if ($status == '8')
                        {
                             $row       = $table_record->row();
                            $user_id   = $row->user_id;
                            $doctor_id = $row->listing_id;
                            $this->confirm_cash_on_delivery_status($user_id, $booking_id, $doctor_id);
                        }
                        else if ($status == '3') //cancel appointment 
                            {
                            $row       = $table_record->row();
                            $user_id   = $row->user_id;
                            $doctor_id = $row->listing_id;
                            $this->cancel_status($user_id, $booking_id, $doctor_id);
                        }
            }
                    $patient_master_array = array(
                        'user_id' => $user_id,
                        'clinic_id' =>$clinic_id, 
                        'booking_id' => $appointment_id,
                        'patient_name' => $user_name,   
                        'contact_no' => $user_mobile,
                        'email' => $user_email,
                        'gender' => $user_gender,
                        'allergies' => $allergies,
                        'heradiatry_problem' => $heradiatry_problem,
                        'created_date' => $date,
                        'type' => $connect_type,
                        'date_of_birth' => $date_of_birth,
                        'doctor_id' => $listing_id
                       );
                    $this->db->insert('doctor_patient', $patient_master_array);
                    $doctor_patient_id = $this->db->insert_id();
                    if ($doctor_patient_id < 0) 
                       {
                        return array(
                            'status' => 208,
                            'message' => 'Database Insert Issue'
                        );
                       }
                } 
            else 
               {
                    return array(
                        'status' => 208,
                        'message' => 'Database Insert Issue'
                    );
               }
            if($patient_id==0)
            {
                $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
                if ($count_user > 0) 
                    {
                    $user_data    = array(
                       
                        'allergies' => $allergies,
                        'updated_at' => $date,
                        'heradiatry_problem' => $heradiatry_problem
                    );
                    $updateStatus = $this->db->where('id', $user_id)->update('users', $user_data);
                    if (!$updateStatus) {
                        return array(
                            'status' => 204,
                            'message' => 'Update failed'
                        );
                    }} 
                else 
                    {
                    return array(
                        'status' => 208,
                        'message' => 'User not found'
                    );
                }
            }
            elseif(!empty($patient_id) || $patient_id!=0)
            {
                $count_user = $this->db->select('id')->from('users')->where('id', $patient_id)->get()->num_rows();
                if ($count_user > 0)
                   {
                        $user_data    = array(
                            //'health_condition' => $health_condition,
                            'allergies' => $allergies,
                            'updated_at' => $date,
                            'heradiatry_problem' => $heradiatry_problem
                        );
                        $updateStatus = $this->db->where('id', $patient_id)->update('users', $user_data);
                        if (!$updateStatus) 
                        {
                            return array(
                                'status' => 204,
                                'message' => 'Update failed'
                            );
                    }
                    } 
                else
                {
                    return array(
                        'status' => 208,
                        'message' => 'User not found'
                    );
                }  
            }
            $query_dr           = $this->db->query("SELECT doctor_list.doctor_name,doctor_list.telephone FROM doctor_list where doctor_list.user_id='$listing_id'");
            $data               = $query_dr->row_array();
            $doctor_name        = $data['doctor_name'];
            $query_dr_2         = $this->db->query("SELECT name_of_hospital FROM hospitals where user_id='$listing_id'");
            $data_2             = $query_dr_2->row_array();
            $clinic_name        = $data_2['clinic_name'];
            $doctor_phone       = $data['telephone'];
            $final_booking_date = date('M-d', strtotime($booking_date));
            
            if ($insert) 
            {
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
            }
            $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
            $this->notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
            if($coupon_id=="0" || empty($coupon_id))
            {
                
            }
            else
            {
               $user_data_dh    = array(
                       
                        'use_status' => 1
                       
                    );
                    $updateStatus = $this->db->where('coupon',$coupon_id)->update('use_coupon', $user_data_dh); 
            }
              return array(
                'booking_id' => $booking_id
            );  
                 }
                 
           }
           else
                 {
                     
                            date_default_timezone_set('Asia/Kolkata');
            $booking_id           = date("YmdHis");
            $date                 = date('Y-m-d H:i:s');
           // $status               = '1'; //awaiting for confirmation  // pending
            $from_time = date("H:i:s", strtotime($from_time));
            $to_time =  date("H:i:s", strtotime($to_time));
          
           
             $booking_master_array1 = array(
                'booking_id' => $booking_id,
                'user_id' => $user_id,
                'doctor_id' => $listing_id,
                'listing_id' => $clinic_id,
                'booking_date' => $booking_date,
                'booking_time' => $booking_time,
                'from_time' => $from_time,
                'to_time' => $to_time,
                'status' => $status,
                'description' => $description,
                'created_date' => $date,
                'patient_id' => $patient_id,
                'consultation_type' => $connect_type,
                'vendor_type'=>$listing_type
            );
            $insert = $this->db->insert('hospital_booking_master', $booking_master_array1);
             $appointment_id       = $this->db->insert_id();
            if($insert)
                {
                    $data= $this->DoctorModel->hospitals_notification_confirm($booking_id);
                    $message = 'Dear '.$data->name.', your booking has been confirm With  '.$data->doctor_name.'  Your '.'Booking ID is '.$data->booking_id.' '.'Thank you.';
                    $to = $data->email;
                    $subject = 'medicalwale Appointment List';
                    $msg = $message;
                    $this->Send_Email_Notification($to,$subject,$msg);
                    $query_dr1           = $this->db->query("SELECT name FROM users  WHERE id='$user_id'");
                    $data1               = $query_dr1->row_array();
                    $user_name        = $data1['name'];
                    $this->notifyMethod_doctor($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $user_name,$from_time);
                }
            if ($appointment_id > 0) 
                {
                    date_default_timezone_set('Asia/Kolkata');
                    $date = date('Y-m-d H:i:s');
                    $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM hospital_booking_master WHERE booking_id='$booking_id'");
                    $count_user   = $table_record->num_rows();
                    if ($count_user > 0) {
                      
                        if ($status == '5') //user confirm, payment pending
                            {
                            $row       = $table_record->row();
                            $user_id   = $row->user_id;
                            $doctor_id = $row->listing_id;
                            //$this->confirm_status($user_id, $booking_id, $doctor_id);
                        }
                        else if ($status == '8')
                        {
                             $row       = $table_record->row();
                            $user_id   = $row->user_id;
                            $doctor_id = $row->listing_id;
                            //$this->confirm_cash_on_delivery_status($user_id, $booking_id, $doctor_id);
                        }
                        else if ($status == '3') //cancel appointment 
                            {
                            $row       = $table_record->row();
                            $user_id   = $row->user_id;
                            $doctor_id = $row->listing_id;
                            //$this->cancel_status($user_id, $booking_id, $doctor_id);
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
          
            $query_dr           = $this->db->query("SELECT hospital_doctor_list.doctor_name,hospital_doctor_list.telephone FROM hospital_doctor_list where hospital_doctor_list.id='$listing_id'");
            $data               = $query_dr->row_array();
            $doctor_name        = $data['doctor_name'];
            $query_dr_2         = $this->db->query("SELECT name_of_hospital FROM hospitals where user_id='$listing_id'");
            $data_2             = $query_dr_2->row_array();
            $clinic_name        = $data_2['clinic_name'];
            $doctor_phone       = $data['telephone'];
            $final_booking_date = date('M-d', strtotime($booking_date));
            
            if ($insert) 
            {
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
            }
            $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
            $this->notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
            if($coupon_id=="0" || empty($coupon_id))
            {
                
            }
            else
            {
               $user_data_dh    = array(
                       
                        'use_status' => 1
                       
                    );
                    $updateStatus = $this->db->where('coupon',$coupon_id)->update('use_coupon', $user_data_dh); 
            }
            return array(
                'booking_id' => $booking_id
            );   
                       
                 }
        
       }
       elseif($type=="3")
       {
           
        if($listing_type =="5")
        {
            date_default_timezone_set('Asia/Kolkata');
            $booking_id           = date("YmdHis");
            $date                 = date('Y-m-d H:i:s');
           // $status               = '1'; //awaiting for confirmation  // pending
            $from_time = date("H:i:s", strtotime($from_time));
            $to_time =  date("H:i:s", strtotime($to_time));
            $booking_master_array = array(
                'booking_id' => $booking_id,
                'user_id' => $user_id,
                'listing_id' => $listing_id,
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
                'vendor_type'=>5
            );
            $insert = $this->db->insert('doctor_booking_master', $booking_master_array);
            $appointment_id       = $this->db->insert_id();
             $sql0 = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$listing_id'");
            $query_practices0  = $this->db->query($sql0);
            $total_practices0  = $query_practices0->num_rows();
            $get_pract0=$query_practices0->row_array();
            $ids=$get_pract0['hospital_doctor_id'];  
                $sql21             = "SELECT hd.*,hd.id as doctor_id,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.id IN ($ids) and h.user_id ='$clinic_id' ";
               //$query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name FROM hospitals INNER JOIN doctor_list ON doctor_list.=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$listing_id'");
                $query_practices0  = $this->db->query($sql21);
                 $count_user         = $query_practices0->num_rows();
                 if($count_user > 0)
                 {
            $get_pract01=$query_practices0->row_array();
                    $id_doctor=$get_pract01['doctor_id'];
            $booking_master_array1 = array(
                'booking_id' => $booking_id,
                'user_id' => $user_id,
                'doctor_id' => $id_doctor,
                'listing_id' => $clinic_id,
                'booking_date' => $booking_date,
                'booking_time' => $booking_time,
                'from_time' => $from_time,
                'to_time' => $to_time,
                'status' => $status,
                'description' => $description,
                'created_date' => $date,
                'patient_id' => $patient_id,
                'consultation_type' => $connect_type,
                'vendor_type'=>50
            );
            $insert1 = $this->db->insert('hospital_booking_master', $booking_master_array1);
                 }
            if($insert)
                {
                    $data= $this->DoctorModel->doctor_notification_confirm($booking_id);
                    $message = 'Dear '.$data->name.', your booking has been confirm With  '.$data->doctor_name.'  Your '.'Booking ID is '.$data->booking_id.' '.'Thank you.';
                    $to = $data->email;
                    $subject = 'medicalwale Appointment List';
                    $msg = $message;
                    $this->Send_Email_Notification($to,$subject,$msg);
                    $query_dr1           = $this->db->query("SELECT name FROM users  WHERE id='$user_id'");
                    $data1               = $query_dr1->row_array();
                    $user_name        = $data1['name'];
                    $this->notifyMethod_doctor($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $user_name,$from_time);
                }
            if ($appointment_id > 0) 
                {
                    date_default_timezone_set('Asia/Kolkata');
                    $date = date('Y-m-d H:i:s');
                    $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'");
                    $count_user   = $table_record->num_rows();
                    if ($count_user > 0) {
                      
                        if ($status == '5') //user confirm, payment pending
                            {
                            $row       = $table_record->row();
                            $user_id   = $row->user_id;
                            $doctor_id = $row->listing_id;
                            $this->confirm_status($user_id, $booking_id, $doctor_id);
                        }
                        else if ($status == '8')
                        {
                             $row       = $table_record->row();
                            $user_id   = $row->user_id;
                            $doctor_id = $row->listing_id;
                            $this->confirm_cash_on_delivery_status($user_id, $booking_id, $doctor_id);
                        }
                        else if ($status == '3') //cancel appointment 
                            {
                            $row       = $table_record->row();
                            $user_id   = $row->user_id;
                            $doctor_id = $row->listing_id;
                            $this->cancel_status($user_id, $booking_id, $doctor_id);
                        }
            }
                    $patient_master_array = array(
                        'user_id' => $user_id,
                        'clinic_id' =>$clinic_id, 
                        'booking_id' => $appointment_id,
                        'patient_name' => $user_name,   
                        'contact_no' => $user_mobile,
                        'email' => $user_email,
                        'gender' => $user_gender,
                        'allergies' => $allergies,
                        'heradiatry_problem' => $heradiatry_problem,
                        'created_date' => $date,
                        'type' => $connect_type,
                        'date_of_birth' => $date_of_birth,
                        'doctor_id' => $listing_id
                       );
                    $this->db->insert('doctor_patient', $patient_master_array);
                    $doctor_patient_id = $this->db->insert_id();
                    if ($doctor_patient_id < 0) 
                       {
                        return array(
                            'status' => 208,
                            'message' => 'Database Insert Issue'
                        );
                       }
                } 
            else 
               {
                    return array(
                        'status' => 208,
                        'message' => 'Database Insert Issue'
                    );
               }
            if($patient_id==0)
            {
                $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
                if ($count_user > 0) 
                    {
                    $user_data    = array(
                       
                        'allergies' => $allergies,
                        'updated_at' => $date,
                        'heradiatry_problem' => $heradiatry_problem
                    );
                    $updateStatus = $this->db->where('id', $user_id)->update('users', $user_data);
                    if (!$updateStatus) {
                        return array(
                            'status' => 204,
                            'message' => 'Update failed'
                        );
                    }} 
                else 
                    {
                    return array(
                        'status' => 208,
                        'message' => 'User not found'
                    );
                }
            }
            elseif(!empty($patient_id) || $patient_id!=0)
            {
                $count_user = $this->db->select('id')->from('users')->where('id', $patient_id)->get()->num_rows();
                if ($count_user > 0)
                   {
                        $user_data    = array(
                            //'health_condition' => $health_condition,
                            'allergies' => $allergies,
                            'updated_at' => $date,
                            'heradiatry_problem' => $heradiatry_problem
                        );
                        $updateStatus = $this->db->where('id', $patient_id)->update('users', $user_data);
                        if (!$updateStatus) 
                        {
                            return array(
                                'status' => 204,
                                'message' => 'Update failed'
                            );
                    }
                    } 
                else
                {
                    return array(
                        'status' => 208,
                        'message' => 'User not found'
                    );
                }  
            }
            $query_dr           = $this->db->query("SELECT doctor_list.doctor_name,doctor_list.telephone FROM doctor_list where doctor_list.user_id='$listing_id'");
            $data               = $query_dr->row_array();
            $doctor_name        = $data['doctor_name'];
            $query_dr_2         = $this->db->query("SELECT name_of_hospital FROM hospitals where user_id='$listing_id'");
            $data_2             = $query_dr_2->row_array();
            $clinic_name        = $data_2['clinic_name'];
            $doctor_phone       = $data['telephone'];
            $final_booking_date = date('M-d', strtotime($booking_date));
            
            if ($insert) 
            {
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
            }
            $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
            $this->notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
            if($coupon_id=="0" || empty($coupon_id))
            {
                
            }
            else
            {
               $user_data_dh    = array(
                       
                        'use_status' => 1
                       
                    );
                    $updateStatus = $this->db->where('coupon',$coupon_id)->update('use_coupon', $user_data_dh); 
            }
              return array(
                'booking_id' => $booking_id
            );  
        } 
        elseif($listing_type =="50")
        {
            date_default_timezone_set('Asia/Kolkata');
            $booking_id           = date("YmdHis");
            $date                 = date('Y-m-d H:i:s');
           // $status               = '1'; //awaiting for confirmation  // pending
            $from_time = date("H:i:s", strtotime($from_time));
            $to_time =  date("H:i:s", strtotime($to_time));
          
           
             $booking_master_array1 = array(
                'booking_id' => $booking_id,
                'user_id' => $user_id,
                'doctor_id' => $listing_id,
                'listing_id' => $clinic_id,
                'booking_date' => $booking_date,
                'booking_time' => $booking_time,
                'from_time' => $from_time,
                'to_time' => $to_time,
                'status' => $status,
                'description' => $description,
                'created_date' => $date,
                'patient_id' => $patient_id,
                'consultation_type' => $connect_type,
                'vendor_type'=>50
            );
            $insert = $this->db->insert('hospital_booking_master', $booking_master_array1);
             $appointment_id       = $this->db->insert_id();
            if($insert)
                {
                    $data= $this->DoctorModel->hospitals_notification_confirm($booking_id);
                    $message = 'Dear '.$data->name.', your booking has been confirm With  '.$data->doctor_name.'  Your '.'Booking ID is '.$data->booking_id.' '.'Thank you.';
                    $to = $data->email;
                    $subject = 'medicalwale Appointment List';
                    $msg = $message;
                    $this->Send_Email_Notification($to,$subject,$msg);
                    $query_dr1           = $this->db->query("SELECT name FROM users  WHERE id='$user_id'");
                    $data1               = $query_dr1->row_array();
                    $user_name        = $data1['name'];
                    $this->notifyMethod_doctor($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $user_name,$from_time);
                }
            if ($appointment_id > 0) 
                {
                    date_default_timezone_set('Asia/Kolkata');
                    $date = date('Y-m-d H:i:s');
                    $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM hospital_booking_master WHERE booking_id='$booking_id'");
                    $count_user   = $table_record->num_rows();
                    if ($count_user > 0) {
                      
                        if ($status == '5') //user confirm, payment pending
                            {
                            $row       = $table_record->row();
                            $user_id   = $row->user_id;
                            $doctor_id = $row->listing_id;
                            //$this->confirm_status($user_id, $booking_id, $doctor_id);
                        }
                        else if ($status == '8')
                        {
                             $row       = $table_record->row();
                            $user_id   = $row->user_id;
                            $doctor_id = $row->listing_id;
                            //$this->confirm_cash_on_delivery_status($user_id, $booking_id, $doctor_id);
                        }
                        else if ($status == '3') //cancel appointment 
                            {
                            $row       = $table_record->row();
                            $user_id   = $row->user_id;
                            $doctor_id = $row->listing_id;
                            //$this->cancel_status($user_id, $booking_id, $doctor_id);
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
          
            $query_dr           = $this->db->query("SELECT hospital_doctor_list.doctor_name,hospital_doctor_list.telephone FROM hospital_doctor_list where hospital_doctor_list.id='$listing_id'");
            $data               = $query_dr->row_array();
            $doctor_name        = $data['doctor_name'];
            $query_dr_2         = $this->db->query("SELECT name_of_hospital FROM hospitals where user_id='$listing_id'");
            $data_2             = $query_dr_2->row_array();
            $clinic_name        = $data_2['clinic_name'];
            $doctor_phone       = $data['telephone'];
            $final_booking_date = date('M-d', strtotime($booking_date));
            
            if ($insert) 
            {
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
            }
            $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
            $this->notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
            if($coupon_id=="0" || empty($coupon_id))
            {
                
            }
            else
            {
               $user_data_dh    = array(
                       
                        'use_status' => 1
                       
                    );
                    $updateStatus = $this->db->where('coupon',$coupon_id)->update('use_coupon', $user_data_dh); 
            }
            return array(
                'booking_id' => $booking_id
            );  
        }
        
        
       }
       
       
    }  
    
    
    
     public function add_bookings_v3($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $is_user, $is_patient, $patient_id,$allergies, $heradiatry_problem, $description, $relationship, $date_of_birth, $connect_type,$coupon_id,$status,$listing_type,$type)
    {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id           = date("YmdHis");
        $date                 = date('Y-m-d H:i:s');
       // $status               = '1'; //awaiting for confirmation  // pending
        $from_time = date("H:i:s", strtotime($from_time));
        $to_time =  date("H:i:s", strtotime($to_time));
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $user_id,
            'listing_id' => $listing_id,
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
            'vendor_type'=>$listing_type
        );
        $insert = $this->db->insert('doctor_booking_master', $booking_master_array);
        $appointment_id       = $this->db->insert_id();
        if($insert)
            {
                $data= $this->DoctorModel->doctor_notification_confirm($booking_id);
                $message = 'Dear '.$data->name.', your booking has been confirm With  '.$data->doctor_name.'  Your '.'Booking ID is '.$data->booking_id.' '.'Thank you.';
                $to = $data->email;
                $subject = 'medicalwale Appointment List';
                $msg = $message;
                $this->Send_Email_Notification($to,$subject,$msg);
                
                
                   $query_dr1           = $this->db->query("SELECT name FROM users  WHERE id='$user_id'");
        $data1               = $query_dr1->row_array();
        $user_name        = $data1['name'];
       
        
         $this->notifyMethod_doctor($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $user_name,$from_time);
            }
        if ($appointment_id > 0) 
            {
                
                
                date_default_timezone_set('Asia/Kolkata');
        $date         = date('Y-m-d H:i:s');
      
        $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'");
        $count_user   = $table_record->num_rows();
        if ($count_user > 0) {
          
            if ($status == '5') //user confirm, payment pending
                {
                $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->confirm_status($user_id, $booking_id, $doctor_id);
            }
            else if ($status == '8')
            {
                 $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->confirm_cash_on_delivery_status($user_id, $booking_id, $doctor_id);
            }
            else if ($status == '3') //cancel appointment 
                {
                $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->cancel_status($user_id, $booking_id, $doctor_id);
            }
        }
                
                
                
                $patient_master_array = array(
                    'user_id' => $user_id,
                    'clinic_id' =>$clinic_id, 
                    'booking_id' => $appointment_id,
                    'patient_name' => $user_name,   
                    'contact_no' => $user_mobile,
                    'email' => $user_email,
                    'gender' => $user_gender,
                    'allergies' => $allergies,
                    'heradiatry_problem' => $heradiatry_problem,
                    'created_date' => $date,
                    'type' => $connect_type,
                    'date_of_birth' => $date_of_birth,
                    'doctor_id' => $listing_id
                   );
                $this->db->insert('doctor_patient', $patient_master_array);
                $doctor_patient_id = $this->db->insert_id();
                if ($doctor_patient_id < 0) 
                   {
                    return array(
                        'status' => 208,
                        'message' => 'Database Insert Issue'
                    );
                   }
            } 
        else 
           {
                return array(
                    'status' => 208,
                    'message' => 'Database Insert Issue'
                );
           }
        if($patient_id==0)
        {
        $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
            if ($count_user > 0) {
                $user_data    = array(
                   
                    'allergies' => $allergies,
                    'updated_at' => $date,
                    'heradiatry_problem' => $heradiatry_problem
                );
                $updateStatus = $this->db->where('id', $user_id)->update('users', $user_data);
                if (!$updateStatus) {
                    return array(
                        'status' => 204,
                        'message' => 'Update failed'
                    );
                }
            } else {
                return array(
                    'status' => 208,
                    'message' => 'User not found'
                );
            }
        }
        elseif(!empty($patient_id) || $patient_id!=0)
        {
            $count_user = $this->db->select('id')->from('users')->where('id', $patient_id)->get()->num_rows();
            if ($count_user > 0) {
                $user_data    = array(
                    //'health_condition' => $health_condition,
                    'allergies' => $allergies,
                    'updated_at' => $date,
                    'heradiatry_problem' => $heradiatry_problem
                );
                $updateStatus = $this->db->where('id', $patient_id)->update('users', $user_data);
                if (!$updateStatus) {
                    return array(
                        'status' => 204,
                        'message' => 'Update failed'
                    );
                }
            } else {
                return array(
                    'status' => 208,
                    'message' => 'User not found'
                );
            }  
        }
        $query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name,doctor_list.telephone FROM doctor_clinic INNER JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$listing_id'");
        $data               = $query_dr->row_array();
        $doctor_name        = $data['doctor_name'];
        $clinic_name        = $data['clinic_name'];
        $doctor_phone       = $data['telephone'];
        $final_booking_date = date('M-d', strtotime($booking_date));
        
        if ($insert) {
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
        }
      $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
        $this->notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
        // $this->notifyuserMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name,$clinic_name);
        
        
      
        if($coupon_id=="0" || empty($coupon_id))
        {
            
        }
        else
        {
           $user_data_dh    = array(
                   
                    'use_status' => 1
                   
                );
                $updateStatus = $this->db->where('coupon',$coupon_id)->update('use_coupon', $user_data_dh); 
        }
        return array(
            'booking_id' => $booking_id
        );
    }    
  
  	public function Send_Email_Notification($to,$subject,$msg)
     {
                //   $to      = 'jayesh@example.com';
                //     $subject = 'otp';
                //     $message = 'hello';
                $headers = 'From: info@medicalwale.com' . "\r\n" .
                           'Reply-To: donotreply@medicalwale.com' . "\r\n" .
                           'X-Mailer: PHP/' . phpversion();
                mail($to, $subject, $msg, $headers);
            }
  
    /*
    This method is used for notification.
    */
    
     public function insert_notification_post_question($user_id, $booking_id, $doctor_id)
       {
      $data = array(
                           'user_id'        => $user_id,
                           'post_id'         => $booking_id,
                           'timeline_id'   => $user_id,
                           'type'          => 'Appointment',
                           'seen'          => '1',
                           'notified_by'   => $doctor_id,
                           'description'   => 'Appointment Booking',
                           'created_at'    => curr_date(),
                           'updated_at'    => curr_date()
                           
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
    
    public function notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $appointment_id, $connect_type, $user_name)
    {
        $customer_token       = $this->db->query("SELECT name,token,agent,token_status,web_token FROM users WHERE id='$listing_id'");
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
            
            $this->send_gcm_notify($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_time, $appointment_id, $connect_type);
            
            // Web notification FCM function by ghanshyam parihar date:16 Jan 2019
            $click_action = 'https://doctor.medicalwale.com/booking_controller/booking_appointment/'.$listing_id;
            // $click_action = 'https://vendor.sandbox.medicalwale.com/doctor/booking_controller/booking_appointment/'.$listing_id;
            $this->send_gcm_web_notify($title, $msg, $web_token, $img_url, $tag, $agent, $type,$click_action);
            // Web notification FCM function by ghanshyam parihar date:16 Jan 2019 Ends
            
            
            
        }
    }
    //send notification through firebase
    
    
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
            $title        = "Your $connect_type with $user_name has been booked."; //$user_name . ' has booked an appointment'; 
            $msg          = 'Date : ' . $newdate . '  Time : ' . $newtime;
            
            $type = 'Booking';
            
            $this->send_gcm_notify_doctor($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_time, $appointment_id, $connect_type,$from);
            
           
            
            
            
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
    
    /* notification to send in the doctor app for appointment confirmation*/
    public function send_gcm_notify($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent, $date, $slot, $appointment_id, $consultation_type)
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
                $agent === 'android' ? 'Authorization: key=AAAAMCq6aPA:APA91bFjtuwLeq5RK6_DFrXntyJdXCFSPI0JdJcvoXXi0xIGm7qgzQJmUl7aEq3HjUTV3pvlFr5iv5pOWtCoN3JIpMSVhU8ZFzusaibehi5MPwThRmx1pCnm3Tm-x6wI8tGwhc0eUj2U' : 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E'
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
    
    
    //********************************* added by zak for doctor profile notifation send **********************************
    //start
     public function notifyMethod_doctor_view($listing_id, $user_id, $user_name, $notyCount)
    {
        $customer_token       = $this->db->query("SELECT name,token,agent,token_status FROM users WHERE id='$listing_id'");
        $customer_token_count = $customer_token->num_rows();
        if ($customer_token_count > 0) {
            $token_status = $customer_token->row_array();
            $usr_name     = $token_status['name'];
            $agent        = $token_status['agent'];
            $reg_id       = $token_status['token'];
            $img_url      = 'https://s3.amazonaws.com/medicalwale/images/img/doctor.png';
            $tag          = 'text';
            $key_count    = '1';
            $title        = $user_name . ' is interested in your profile';
            $msg          = $user_name . ' is interested in your profile';
            $this->send_gcm_notify_notifyMethod_doctor_view($title, $reg_id, $msg, $img_url, $tag, $agent,$listing_id,$user_id,$notyCount);
        }
    }
    //send notification through firebase
    /* notification to send in the doctor app for appointment confirmation*/
    public function send_gcm_notify_notifyMethod_doctor_view($title, $reg_id, $msg, $img_url, $tag, $agent,$listing_id,$user_id,$notyCount)
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
                      'listing_id'  => $user_id,
                      'booking_id'  => "",
                       'invoice_no' => "",
                       'user_id'  => $listing_id,
                       'notification_type'  => 'view_notifications',
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
                    "notification_type" => 'view_notifications',
                    "notification_date" => $date,
                    "notification_userid"=> $user_id,
                    "notification_listingid" => $listing_id,
                    "notification_count" => $notyCount
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
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
        }
    }
    
    
    //end 
    public function user_booking_profile($user_id, $user_rel_name, $user_relation, $rel_dob, $user_rel_gender, $user_rel_mobile, $user_rel_email, $user_medical_condition, $user_allergies, $hereditary_problems)
    {
        $count_exist_data       = $this->db->query("SELECT * FROM `health_record` WHERE `phone`='$user_rel_mobile'");
        $count_exist_data_count = $count_exist_data->num_rows();
        $date                   = date('Y-m-d H:i:s');
        if ($count_exist_data_count == 0) {
            $health_record = array(
                'user_id' => $user_id,
                'patient_name' => $user_rel_name,
                'relationship' => $user_relation,
                'date_of_birth' => $rel_dob,
                'gender' => $user_rel_gender,
                'created_at' => $date,
                'phone' => $user_rel_mobile,
                'email' => $user_rel_email
            );
            $this->db->insert('health_record', $health_record);
        }
        $query = $this->db->query("UPDATE users SET heradiatry_problem='$hereditary_problems',allergies='$user_allergies',health_condition='$user_medical_condition'  WHERE id='$user_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    public function user_booking_slot($user_id, $listing_id, $booking_id, $clinic_id, $consultation_type, $from_time, $to_time, $description, $status, $booking_date, $patient_id)
    {
        $date                = date('Y-m-d H:i:s');
        $insert_booking_slot = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'booking_id' => $booking_id,
            'clinic_id' => $clinic_id,
            'consultation_type' => $consultation_type,
            'patient_id' => $patient_id,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'created_date' => $date,
            'description' => $description,
            'status' => '1',
            'booking_date' => $booking_date
        );
        $this->db->insert('doctor_booking_master', $insert_booking_slot);
        $appointment_id       = $this->db->insert_id();
        //mobile app notification
        $customer_token       = $this->db->query("SELECT name,phone,email,token,agent,token_status FROM users WHERE id='$listing_id'");
        $customer_token_count = $customer_token->num_rows();
        if ($customer_token_count > 0) {
            $token_status = $customer_token->row_array();
            //    $getusr = $user_plike->row_array();
            $usr_name     = $token_status['name'];
            $user_email   = $token_status['email'];
            $agent        = $token_status['agent'];
            $reg_id       = $token_status['token'];
            $description  = "";
            $img_url      = 'https://s3.amazonaws.com/medicalwale/images/img/doctor.png';
            $tag          = 'text';
            $key_count    = '1';
            $title        = $usr_name . ' has booked an appointment';
            $msg          = $usr_name . '  has booked an appointment.\n' + $description;
            $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $date, $from_time, $to_time, $appointment_id, $consultation_type);
            //SMS notification        
            $message      = '' . $token_status['name'] . ' has Booked Appointment';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $token_status['phone'],
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
            $this->doctor_booking_sendmail($user_email, $usr_name, $consultation_type, $from_time, $to_time, $date);
        }
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    public function doctor_booking_sendmail($user_email, $usr_name, $consultation_type, $slot, $date)
    {
        $subject = "REGISTRATION INFORMATION";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Medicalwale.com <no-reply@medicalwale.com>' . "\r\n";
        $headers .= 'Cc: ' . "\r\n";
        $message  = '<div style="max-width: 700px;float: none;margin: 0px auto;">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
   <div id="styles_holder">
      <style>
         .ReadMsgBody { width: 100%; background-color: #ffffff; }
         .ExternalClass { width: 100%; background-color: #ffffff; }
         .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
         html { width: 100%; }
         body { -webkit-text-size-adjust: none; -ms-text-size-adjust: none; margin: 0; padding: 0; }
         table { border-spacing: 0; border-collapse: collapse; table-layout: fixed; margin:0 auto; }
         table table table { table-layout: auto; }
         img { display: block !important; }
         table td { border-collapse: collapse; }
         .yshortcuts a { border-bottom: none !important; }
         a { color: #1abc9c; text-decoration: none; }
         /*Responsive*/
         @media only screen and (max-width: 640px) {
         body { width: auto !important; }
         table[class="table-inner"] { width: 90% !important; }
         table[class="table-full"] { width: 100% !important; text-align: center !important; }
         /* Image */
         img[class="img1"] { width: 100% !important; height: auto !important; }
         }
         @media only screen and (max-width: 479px) {
         body { width: auto !important; }
         table[class="table-inner"] { width: 90% !important; }
         table[class="table-full"] { width: 100% !important; text-align: center !important; }
         /* image */
         img[class="img1"] { width: 100% !important; }
         }


      </style>
   </div>
  
   <div id="frame" class="ui-sortable">
      <table data-thumb="" data-module="header-bar" data-bgcolor="Main BG" align="center" width="100%" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class="">
         <tbody>
            <tr>
               <td data-border-top-color="Top Border" style="border-top:4px solid #049341;"></td>
            </tr>
            <tr>
               <td height="25"></td>
            </tr>
            <tr>
               <td align="center">
                  <table data-bgcolor="Container" width="600" class="table-inner" bgcolor="#ffffff" style="border-top-left-radius:5px;border-top-right-radius:5px;" border="0" align="center" cellpadding="0" cellspacing="0">
                     <tbody>
                        <tr>
                           <td align="center" style="border-bottom: 5px solid #049341;">
                              <table class="table-inner" width="520" border="0" align="center" cellpadding="0" cellspacing="0">
                                 <tbody>
                                    <tr>
                                       <td style="padding: 10px 0px;">
                                          <!--Logo-->
                                          <table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0">
                                             <tbody>
                                                <tr>
                                                   <td align="center" style="line-height:0px;">
                                                      <img data-crop="false" mc:edit="quinn-box-1" style="display:block; line-height:0px; font-size:0px; border:0px;height: 70px;" src="http://medicalwale.com/img/email-logo.png" alt="logo"   >
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                          <!--End Logo-->
                                          <!--social-->
                                          <table border="0" align="right" cellpadding="0" cellspacing="0" class="table-full">
                                             <tbody>
                                                <tr>
                                                   <td height="15"></td>
                                                </tr>
                                                <tr>
                                                   <td align="center">
                                                      <table border="0" align="center" cellpadding="0" cellspacing="0">
                                                         <tbody>
                                                            <tr>
                                                               <td align="center" style="">
                                                                   <font style="font-size:11px;line-height:16px" face="font-family: arial,sans-serif;" color="#666666">
                                    <b style="font-size: 12px;font-family: arial, sans-serif;"></b><br>
                                    </font>
                                    <font style="font-size:14px" face="HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue, Helvetica, Arial, sans-serif" color="#666666">
                                    Call Us: </font><font style="font-size:16px" face="HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica, Arial, sans-serif" color="#7db701"><strong>022-60123457</strong></font>
                                                               </td>
                                                            </tr>
                                                         </tbody>
                                                      </table>
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                          <!--End social-->
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
      
      
      <table data-thumb="" data-module="1-2-right" data-bgcolor="Main BG" width="100%" align="center" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class="">
         <tbody>
            <tr>
               <td align="center">
                  <table data-bgcolor="Container" bgcolor="#FFFFFF" class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="height: 250px;">
                     <tbody  style="background: url(https://medicalwale.com/img/mail_bg.jpg);background-size: cover;">
                        <tr>
                           <td height="20"></td>
                        </tr>
                        <tr>
                           <td align="center">
                              <table class="table-inner" width="570" border="0" align="center" cellpadding="0" cellspacing="0">
                                 <tbody >
                                    <tr>
                                       <td>
                                          <!-- img -->
                                          <table class="table-full" border="0" align="left" cellpadding="0" cellspacing="0" style="width: 320px;">
                                             <tbody>
                                                <tr>
                                                   <td align="left" style="padding-bottom: 10px;">
                                                       <p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >Welcome to </font><br>
                                       <font style="font-size:22px;color:#fff;" face="arial,sans-serif" ><span class="il">Medicalwale.com</span></font>
                                    </p>
                                    <p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >' . $usr_name . ' has Booked Appointment at ' . $consultation_type . ' at ' . $date . ' from ' . $slot . '. </font></p>
                                    <p><font style="font-size:14px;color:#fff;" face="arial,sans-serif" >For any clarification <br> Mail us on: <a href="mailto:partner@medicalwale.com" style="color:#fff">partner@medicalwale.com</a> </font></p>
                                 
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                          <table border="0" align="right" cellpadding="0" cellspacing="0" class="table-full" style="width: 250px;">
                                             <!-- Title -->
                                             <tbody>
                                                <tr>
                                                   <td>
                                                  <!--  <table style="background-color:rgb(249,246,246);border:0px solid rgb(213,210,210);width:100%;" cellpadding="0" cellspacing="0">
                                       <tbody>
                                       <tr>
                                             <td style="padding-left:20px;padding-top:10px;padding-bottom:10px;padding-right:10px;    background: #a8abaf;    text-align: center;" valign="top" align="left"><font style="font-size:16px" face="arial,sans-serif" color="#ffffff">Your Login Details</font></td>
                                          </tr>
                                          <tr>
                                             <td style="padding:18px 15px 4px;background: #fff;" align="left">
                                             <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Link</font>
                                                <font style="font-size:14px;line-height:21px" face="arial,sans-serif" color="#333333">
                                                <a href="" target="_blank" style="color: #656060;text-decoration: none;"></a></font>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="padding:18px 15px 4px;background: #fff;" align="left">
                                             <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Email:</font>
                                                <font style="font-size:14px;line-height:21px" face="arial,sans-serif" color="#333333">
                                                <a href="#" target="_blank" style="color: #656060;text-decoration: none;">' . $usr_name . '</a></font>
                                             </td>
                                          </tr>
                                          <tr>
                                             <td style="padding-bottom:18px;padding-top:4px;padding-left:15px;background: #fff;" align="left">
                                                <font style="font-size:14px;color: #656060;font-weight: 600;" face="arial,sans-serif">Password :</font>
                                                <font style="font-size:14px;line-height:21px;color: #656060" face="arial,sans-serif" ></font>
                                             </td>
                                          </tr>
                                       </tbody>
                                    </table> -->
                                                   </td>
                                                </tr>
                                                <!--End Title-->
                                           
                                               
                                                <!--Content-->
                                              <!--   <tr>
                                                   <td data-link-style="text-decoration:none; color:#1abc9c;" data-link-color="Content" data-size="Content" data-color="Content" mc:edit="quinn-box-25" align="left" style="font-family: Open Sans, Arial, sans-serif; font-size:14px; color:#fff; line-height:28px;">
                                                    <a href="" target="_blank"> <button type="button" style="width: 100%;margin-right: 5px;background: #3c98ed;font-size: 16px;font-weight: bold;color: #fff;font-family: Arial,Helvetica,sans-serif;padding: 6px 12px;font-size: 14px;font-weight: 400;line-height: 1.42857143;text-align: center;-ms-touch-action: manipulation;cursor: pointer;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;background-image: none;border: 1px solid transparent;cursor: pointer;">Login </button></a>
                                                     
                                                   </td>
                                                </tr> -->
                                                <!--End Content-->
                                             </tbody>
                                          </table>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                        <tr>
                           <td height="20"></td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
      

      
      <table data-thumb="" data-module="quote" data-bgcolor="Main BG" width="100%" align="center" bgcolor="#e7e7e7" border="0" cellspacing="0" cellpadding="0" class="" >
         <tbody>
            <tr>
               <td data-bgcolor="Feature BG" align="center" bgcolor="#e7e7e7">
                  <table data-bgcolor="Feature inner" bgcolor="#fff" class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0" style="border-bottom-left-radius: 5px;border-bottom-right-radius: 5px;"> 
                     <tbody>
                        <tr>
                           <td align="center">
                              <table class="table-inner" width="520" border="0" align="center" cellpadding="0" cellspacing="0">
                                 <tbody>
                                    <tr>
                                       <td height="35"></td>
                                    </tr>
                                    <!-- intro -->
                                   
                                    <!-- end intro -->
                                    <tr>
                                       <td height="5"></td>
                                    </tr>
                                    <!-- Quote -->
                                    <tr>
                                      <!--  <td data-link-style="text-decoration:none; color:#4a4a4a;" data-link-color="Quote" data-size="Quote" data-color="Quote" mc:edit="quinn-box-56" align="center" style="font-family: century Gothic, arial, sans-serif; font-size:16px; color:#4a4a4a; font-weight:bold;">"Your Resume has been Shorlisted"</td> -->
                                    </tr>
                                    <!-- end Quote -->
                                                                       <tr>
                                       <td height="35"></td>
                                    </tr>
                                  
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
          
         </tbody>
      </table>
      
      
          

      <table data-thumb="" data-module="footer" data-bgcolor="Main BG" width="100%" bgcolor="#e7e7e7" border="0" align="center" cellpadding="0" cellspacing="0" class="">
         <tbody>
            
            <tr>
               <td data-bgcolor="Footer Bar" bgcolor="#191919" align="center" style="font-family: Open Sans, Arial, sans-serif; font-size:11px; color:#ffffff;background: #e7e7e7;">
                  <table class="table-inner" width="600" border="0" align="center" cellpadding="0" cellspacing="0">
                     <tbody>
                        <tr>
                           <td>
                              <!-- copyright -->
                              <table class="table-full" bgcolor="#e7e7e7" border="0" align="left" cellpadding="0" cellspacing="0" style="width: 100%;">
                                 <tbody>
                                    <tr>
                                       <td data-link-style="text-decoration:none; color:#ffffff;" data-link-color="Footer Link" data-size="Content" data-color="Footer Text" mc:edit="quinn-box-81" height="30" style="font-family: Open Sans, Arial, sans-serif; font-size:12px; color: #6d6d6d;line-height: 19px;text-align:center;    padding: 10px 0px;">
By using these service, you agree that you are bound by the Terms of Service.<br/>Copyright 2017 AEGIS HEALTH SOLUTIONS PVT. LTD. All rights reserved.
                                       </td>
                                    </tr>
                                    <tr>
                                       <td height="15"></td>
                                    </tr>
                                 </tbody>
                              </table>
                             
                             
                           </td>
                        </tr>
               
                     </tbody>
                  </table>
               </td>
            </tr>
    
         </tbody>
      </table>

   </div>
</div>';
        $sentmail = mail($user_email, $subject, $message, $headers);
    }
    
    public function user_read_slot($clinic_id, $doctor_id, $consultation_type)
    {
        $todayDay  = date('l');
        $todayDate = date('Y-m-d H:i:s');
        for ($i = 0; $i < 7; $i++) {
            $time_array           = array();
            $time_array1          = array();
            $time_array2          = array();
            $time_array3          = array();
            $time_slott           = array();
            $todayDate            = date('Y-m-d', strtotime($todayDate . ' +1 day'));
            $todayDay             = date('l', strtotime($todayDate));
            
            // doctor_slot_details
            
            if($clinic_id == '0'){
                $day_time_slots       = $this->db->query("SELECT * FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                //  echo "SELECT * FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'";
            }else{
                $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                //  echo "SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'";
            }
            $count_day_time_slots = $day_time_slots->num_rows();
            foreach ($day_time_slots->result_array() as $row) {
                if($clinic_id == '0'){
                      $timeSlot              = $row['timeSlot'];
                } else {
                      $timeSlot              = $row['timeSlot'];
                }
                
              
                $from_time             = $row['from_time'];
                $to_time               = $row['to_time'];
                // $status                = $row['status'];
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
            $time_slott[] = array(
                'Morning' => $time_array,
                'time_slot' => 'Morning'
            );
            $time_slott[] = array(
                'Afternoon' => $time_array1,
                'time_slot' => 'Afternoon'
            );
            $time_slott[] = array(
                'Evening' => $time_array3,
                'time_slot' => 'Evening'
            );
            $time_slott[] = array(
                'Night' => $time_array2,
                'time_slot' => 'Night'
            );
            $time_slots[] = array(
                'day' => $todayDay,
                'date' => $todayDate,
                'timings' => $time_slott
            );
        }
        
    
        
        return $time_slots;
    }
    
    public function user_read_slot_v2($doctor_id, $consultation_type,$agent,$listing_type,$hospital_id)
    {
        if($agent=="ios")
        {
             $clinic1_slott=array();
            $clinic1_slott_1=array();
            $clinic1_slott_2=array();
            $sql2             = sprintf("SELECT `id`, `clinic_name`,consultation_charges FROM doctor_clinic WHERE doctor_id='$doctor_id' ");
            $query_practices  = $this->db->query($sql2);
            $total_practices  = $query_practices->num_rows();
            if ($total_practices > 0) 
                {
                        foreach ($query_practices->result_array() as $get_pract) {
                            $clinic_id            = $get_pract['id'];
                            
                            $clinic_name          = $get_pract['clinic_name'];
                            
                            $charge=$get_pract['consultation_charges'];
                           
                           if($consultation_type!='visit')
                           {
                               $sql12             = sprintf("SELECT `consultaion_video`, `consultaion_chat`,consultation_voice_call FROM doctor_list WHERE user_id='$doctor_id' ");
                               $query_practices1  = $this->db->query($sql12)->row_array();
                               $charge1=$query_practices1['consultaion_chat'];
                               $charge2=$query_practices1['consultaion_video'];
                               $charge3=$query_practices1['consultation_voice_call'];
                               
                           }
                             $fees=0;
            if($consultation_type=="visit")
            {
                
                $fees=$charge;
            }
            else if($consultation_type=="chat")
            {
                $consultation_type="online";
                $fees=$charge1;
            }
            else if($consultation_type=="video")
            {
                $consultation_type="online";
                $fees=$charge2;
            }
            else if($consultation_type=="call")
            {
                $consultation_type="online";
                $fees=$charge3;
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
                
               if($consultation_type=="online")
               {
                    $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '0' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
               }
               else
               {
                  $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
               }
            //         echo "SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'";
            // die();   
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
       $queryTiming1 = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
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
                    'listing_type'=>5,
                    'timings' => $time_slott
                    );
               
                      $clinic1_slott_1[] = array(
                        'day' => $todayDay,
                        'date' => $todayDate,
                        'clinic_array' => $time_slots
                    );
               
                $time_slots=array();  
                    
            }
          
            
          
            
            
            
                        }
                }
            $sql0 = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
            $query_practices0  = $this->db->query($sql0);
            $total_practices0  = $query_practices0->num_rows();
                       
            $get_pract0=$query_practices0->row_array();
            $ids=$get_pract0['hospital_doctor_id'];
            if(!empty($ids))
                {
                        $sql21             = "SELECT hd.*,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.id IN ($ids)";
                        $query_practices1  = $this->db->query($sql21);
                        $total_practices1  = $query_practices1->num_rows();
                         foreach ($query_practices1->result_array() as $get_pract2) {
                            $new_doctor_id        = $get_pract2['id'];
                            $clinic_id            = $get_pract2['hospital_id'];
                            $clinic_name          = $get_pract2['name_of_hospital'];
                            $charge=$get_pract2['consultation'];
                            $fees=$charge;
                            $consultation_type='visit';
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
                            for ($i = 0; $i < 7; $i++) 
                                {
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
                                    $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `hospital_doctor_timing` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                                    $count_day_time_slots = $day_time_slots->num_rows();
                                    foreach ($day_time_slots->result_array() as $row) 
                                            {
                                                $timeSlot = $row['timeSlot'];
                                                $from_time = $row['from_time'];
                                                $to_time = $row['to_time'];
                  if($dat_new== $todayDate)
                  {
                    $date2 =date('Y-m-d H:i:s', strtotime($from_time));
                    $date1;
                    if ($date2 > $date1)
                        {
                  $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
                    else
                        {
                    
                }
       
                 }
       else
       {
           $day_time_status       = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
       $queryTiming1 = $this->db->query("SELECT * FROM `hospital_doctor_slot_details` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
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
                    'listing_type'=>50,
                    'timings' => $time_slott
                    );
               
                      $clinic1_slott_2[] = array(
                        'day' => $todayDay,
                        'date' => $todayDate,
                        'clinic_array' => $time_slots
                    );
               
                $time_slots=array();  
                    
            }
          
            
          
            
            
            
                        }
                        
                        } 
                        else
                        {
                             $sql21             = "SELECT hd.*,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.id ='$doctor_id'";
                        $query_practices1  = $this->db->query($sql21);
                        $total_practices1  = $query_practices1->num_rows();
                         foreach ($query_practices1->result_array() as $get_pract2) {
                            $new_doctor_id        = $get_pract2['id'];
                            $clinic_id            = $get_pract2['hospital_id'];
                            $clinic_name          = $get_pract2['name_of_hospital'];
                            $charge=$get_pract2['consultation'];
                            $fees=$charge;
                            $consultation_type='visit';
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
                            for ($i = 0; $i < 7; $i++) 
                                {
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
                                    $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `hospital_doctor_timing` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                                    $count_day_time_slots = $day_time_slots->num_rows();
                                    foreach ($day_time_slots->result_array() as $row) 
                                            {
                                                $timeSlot = $row['timeSlot'];
                                                $from_time = $row['from_time'];
                                                $to_time = $row['to_time'];
                  if($dat_new== $todayDate)
                  {
                    $date2 =date('Y-m-d H:i:s', strtotime($from_time));
                    $date1;
                    if ($date2 > $date1)
                        {
                  $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
                    else
                        {
                    
                }
       
                 }
       else
       {
           $day_time_status       = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
       $queryTiming1 = $this->db->query("SELECT * FROM `hospital_doctor_slot_details` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
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
                    'listing_type'=>50,
                    'timings' => $time_slott
                    );
               
                      $clinic1_slott_2[] = array(
                        'day' => $todayDay,
                        'date' => $todayDate,
                        'clinic_array' => $time_slots
                    );
               
                $time_slots=array();  
                    
            }
          
            
          
            
            
            
                        }
                        }
            $clinic1_slott=array_merge($clinic1_slott_1,$clinic1_slott_2);
        }
        else
        {
            $clinic1_slott=array();
            $clinic1_slott_1=array();
            $clinic1_slott_2=array();
            $sql2             = sprintf("SELECT `id`, `clinic_name`,consultation_charges FROM doctor_clinic WHERE doctor_id='$doctor_id' ");
            $query_practices  = $this->db->query($sql2);
            $total_practices  = $query_practices->num_rows();
            if ($total_practices > 0) {
                    foreach ($query_practices->result_array() as $get_pract) {
                         $clinic_id            = $get_pract['id'];
                        
                        $clinic_name          = $get_pract['clinic_name'];
                        
                        $charge=$get_pract['consultation_charges'];
                       
                       if($consultation_type!='visit')
                       {
                           $sql12             = sprintf("SELECT `consultaion_video`, `consultaion_chat`,consultation_voice_call FROM doctor_list WHERE user_id='$doctor_id' ");
                           $query_practices1  = $this->db->query($sql12)->row_array();
                           $charge1=$query_practices1['consultaion_chat'];
                           $charge2=$query_practices1['consultaion_video'];
                           $charge3=$query_practices1['consultation_voice_call'];
                           
                       }
                         $fees=0;
       if($consultation_type=="visit")
        {
            
            $fees=$charge;
        }
        else if($consultation_type=="chat")
        {
            $consultation_type="online";
            $fees=$charge1;
        }
        else if($consultation_type=="video")
        {
            $consultation_type="online";
            $fees=$charge2;
        }
        else if($consultation_type=="call" || $consultation_type=="voice" || $consultation_type=="phone")
        {
            $consultation_type="online";
            $fees=$charge3;
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
           if($consultation_type=="online")
           {
                $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '0' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
           }
           else
           {
              $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
           }
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
             $queryTiming1 = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
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
            
           
          /*  if($agent=="ios")
            {
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
            }*/
          /*  else
            {*/
             $time_slots[] = array(
                'day' => $todayDay,
                'date' => $todayDate,
                'timings' => $time_slott
            ); 
           
           /* }*/
            //$time_slots=array();  
                
        }
      
        
          $clinic1_slott_1[] = array(
                'clinic_id' => $clinic_id,
                'clinic_name' => $clinic_name,
                'consultation_charge' => $fees,
                'listing_type'=>5,
                'time_slot' => $time_slots
            );
        
        
        // print_r($clinic1_slott); die();
                    }
                }
            $sql0 = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
            $query_practices0  = $this->db->query($sql0);
            $total_practices0  = $query_practices0->num_rows();
                       
            $get_pract0=$query_practices0->row_array();
            $ids=$get_pract0['hospital_doctor_id'];
            if(!empty($ids))
                {
                        $sql21             = "SELECT hd.*,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.id IN ($ids)";
                        $query_practices1  = $this->db->query($sql21);
                        $total_practices1  = $query_practices1->num_rows();
                         foreach ($query_practices1->result_array() as $get_pract2) {
                            $new_doctor_id        = $get_pract2['id'];
                            $clinic_id            = $get_pract2['hospital_id'];
                            $clinic_name          = $get_pract2['name_of_hospital'];
                            $charge=$get_pract2['consultation'];
                            $fees=$charge;
                            $consultation_type='visit';
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
           $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `hospital_doctor_timing` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                                    
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
             $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
        $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
             $queryTiming1 = $this->db->query("SELECT * FROM `hospital_doctor_slot_details` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
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
      
        
          $clinic1_slott_2[] = array(
                'clinic_id' => $clinic_id,
                'clinic_name' => $clinic_name,
                'consultation_charge' => $fees,
                'listing_type'=>50,
                'time_slot' => $time_slots
            );
            
          
            
            
            
                        }
                        
                        }
            else
            {
              $sql21             = "SELECT hd.*,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.id ='$doctor_id'";
                        $query_practices1  = $this->db->query($sql21);
                        $total_practices1  = $query_practices1->num_rows();
                         foreach ($query_practices1->result_array() as $get_pract2) {
                            $new_doctor_id        = $get_pract2['id'];
                            $clinic_id            = $get_pract2['hospital_id'];
                            $clinic_name          = $get_pract2['name_of_hospital'];
                            $charge=$get_pract2['consultation'];
                            $fees=$charge;
                            $consultation_type='visit';
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
           $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `hospital_doctor_timing` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                                    
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
             $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
        $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
             $queryTiming1 = $this->db->query("SELECT * FROM `hospital_doctor_slot_details` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
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
      
        
          $clinic1_slott_2[] = array(
                'clinic_id' => $clinic_id,
                'clinic_name' => $clinic_name,
                'consultation_charge' => $fees,
                'listing_type'=>50,
                'time_slot' => $time_slots
            );
            
          
            
            
            
                        }  
            }
            $clinic1_slott=array_merge($clinic1_slott_1,$clinic1_slott_2);    
                
        }
        return $clinic1_slott;
    }
     public function user_read_slot_v4($doctor_id, $consultation_type,$agent,$listing_type,$hospital_id)
    {
       if($agent=="ios")
        {
            $old_doctor=$doctor_id;
            $listing_type='';
            $sql0 = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
            $query_practices0  = $this->db->query($sql0);
            $total_practices0  = $query_practices0->num_rows();
            if($total_practices0 > 0)  
            {
                
            $listing_type='5';
            $get_pract0=$query_practices0->row_array();
            $ids=$get_pract0['hospital_doctor_id'];
            if(!empty($ids))
            {
                $doctor_id=$ids;
                
            }
            else
            {
                 $doctor_id;
            }
        }
        else
        {
            $listing_type='50';
            $doctor_id;
        }
        $clinic1_slott=array();
                         $sql2             = "SELECT hd.*,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.hospital_id='$hospital_id' and hd.id IN ($doctor_id) ";
                        $query_practices  = $this->db->query($sql2);
                        $total_practices  = $query_practices->num_rows();
                        if($total_practices > 0)
                        {
                         foreach ($query_practices->result_array() as $get_pract) {
                            $new_doctor_id        = $get_pract['id'];
                            $clinic_id            = $get_pract['hospital_id'];
                            $clinic_name          = $get_pract['name_of_hospital'];
                            $charge=$get_pract['consultation'];
                            $fees=$charge;
                            $consultation_type='visit';
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
                            for ($i = 0; $i < 7; $i++) 
                                {
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
                                    $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `hospital_doctor_timing` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                                    $count_day_time_slots = $day_time_slots->num_rows();
                                    foreach ($day_time_slots->result_array() as $row) 
                                            {
                                                $timeSlot = $row['timeSlot'];
                                                $from_time = $row['from_time'];
                                                $to_time = $row['to_time'];
                  if($dat_new== $todayDate)
                  {
                    $date2 =date('Y-m-d H:i:s', strtotime($from_time));
                    $date1;
                    if ($date2 > $date1)
                        {
                           
                  $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
                    else
                        {
                    
                }
       
                 }
       else
       {
          
           $day_time_status       = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
       $queryTiming1 = $this->db->query("SELECT * FROM `hospital_doctor_slot_details` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
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
                    'listing_type'=>$listing_type,
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
                        else
                        {
                            $clinic1_slott=array();
                        }
        }
        else
        {
             $old_doctor=$doctor_id;
             $listing_type='';
            $sql0 = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
            $query_practices0  = $this->db->query($sql0);
            $total_practices0  = $query_practices0->num_rows();
            if($total_practices0 > 0)  
            {
                
            $listing_type='5';
            $get_pract0=$query_practices0->row_array();
            $ids=$get_pract0['hospital_doctor_id'];
            if(!empty($ids))
            {
                $doctor_id=$ids;
                
            }
            else
            {
                 $doctor_id;
            }
        }
        else
        {
            $listing_type='50';
            $doctor_id;
        }
            $clinic1_slott=array();
                        $sql2             = "SELECT hd.*,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.hospital_id='$hospital_id' and hd.id IN ($doctor_id)";
                        $query_practices  = $this->db->query($sql2);
                        $total_practices  = $query_practices->num_rows();
                        if ($total_practices > 0) {
                    foreach ($query_practices->result_array() as $get_pract) {
                         $new_doctor_id        = $get_pract['id'];
                            $clinic_id            = $get_pract['hospital_id'];
                            $clinic_name          = $get_pract['name_of_hospital'];
                            $charge=$get_pract['consultation'];
                            $fees=$charge;
                            $consultation_type='visit';
        
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
          
             $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `hospital_doctor_timing` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                                    
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
   
     
              $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
        $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
            $queryTiming1 = $this->db->query("SELECT * FROM `hospital_doctor_slot_details` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
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
            
           
          /*  if($agent=="ios")
            {
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
            }*/
          /*  else
            {*/
             $time_slots[] = array(
                'day' => $todayDay,
                'date' => $todayDate,
                'timings' => $time_slott
            ); 
           
           /* }*/
            //$time_slots=array();  
                
        }
      
        
          $clinic1_slott[] = array(
                'clinic_id' => $clinic_id,
                'clinic_name' => $clinic_name,
                'consultation_charge' => $fees,
                'listing_type'=>$listing_type,
                'time_slot' => $time_slots
            );
        
        
        // print_r($clinic1_slott); die();
                    }
                }
                        else
                        {
                            $clinic1_slott=array();
                        }
        }
                 return $clinic1_slott;         
    
    }
    
     public function user_read_slot_v3($doctor_id, $consultation_type,$agent,$hospital_id,$booking_id)
    {
       if($agent=="ios")
        {
            $sql21             = "SELECT  id from hospital_booking_master WHERE booking_id='$booking_id'";
            $query_practices1  = $this->db->query($sql21);
            $total_practices1  = $query_practices1->num_rows();
            if($total_practices1 > 0)
            {
                 $old_doctor=$doctor_id;
            $listing_type='';
            $sql0 = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
            $query_practices0  = $this->db->query($sql0);
            $total_practices0  = $query_practices0->num_rows();
            if($total_practices0 > 0)  
            {
                
            $listing_type='5';
            $get_pract0=$query_practices0->row_array();
            $ids=$get_pract0['hospital_doctor_id'];
            if(!empty($ids))
            {
                $doctor_id=$ids;
                
            }
            else
            {
                 $doctor_id;
            }
        }
        else
        {
            $listing_type='50';
            $doctor_id;
        }
        $clinic1_slott=array();
                         $sql2             = "SELECT hd.*,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.hospital_id='$hospital_id' and hd.id IN ($doctor_id) ";
                        $query_practices  = $this->db->query($sql2);
                        $total_practices  = $query_practices->num_rows();
                        if($total_practices > 0)
                        {
                         foreach ($query_practices->result_array() as $get_pract) {
                            $new_doctor_id        = $get_pract['id'];
                            $clinic_id            = $get_pract['hospital_id'];
                            $clinic_name          = $get_pract['name_of_hospital'];
                            $charge=$get_pract['consultation'];
                            $fees=$charge;
                            $consultation_type='visit';
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
                            for ($i = 0; $i < 7; $i++) 
                                {
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
                                    $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `hospital_doctor_timing` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                                    $count_day_time_slots = $day_time_slots->num_rows();
                                    foreach ($day_time_slots->result_array() as $row) 
                                            {
                                                $timeSlot = $row['timeSlot'];
                                                $from_time = $row['from_time'];
                                                $to_time = $row['to_time'];
                  if($dat_new== $todayDate)
                  {
                    $date2 =date('Y-m-d H:i:s', strtotime($from_time));
                    $date1;
                    if ($date2 > $date1)
                        {
                           
                  $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
                    else
                        {
                    
                }
       
                 }
       else
       {
          
           $day_time_status       = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
       $queryTiming1 = $this->db->query("SELECT * FROM `hospital_doctor_slot_details` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
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
                    'listing_type'=>$listing_type,
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
                        else
                        {
                            $clinic1_slott=array();
                        }
            }
            else
            {
                 $clinic1_slott=array();
            $clinic1_slott_1=array();
            $clinic1_slott_2=array();
            $sql2             = sprintf("SELECT `id`, `clinic_name`,consultation_charges FROM doctor_clinic WHERE doctor_id='$doctor_id' ");
            $query_practices  = $this->db->query($sql2);
            $total_practices  = $query_practices->num_rows();
            if ($total_practices > 0) 
                {
                        foreach ($query_practices->result_array() as $get_pract) {
                            $clinic_id_new            = $get_pract['id'];
                            
                            $clinic_name          = $get_pract['clinic_name'];
                            
                            $charge=$get_pract['consultation_charges'];
                           
                           if($consultation_type!='visit')
                           {
                               $sql12             = sprintf("SELECT `consultaion_video`, `consultaion_chat`,consultation_voice_call FROM doctor_list WHERE user_id='$doctor_id' ");
                               $query_practices1  = $this->db->query($sql12)->row_array();
                               $charge1=$query_practices1['consultaion_chat'];
                               $charge2=$query_practices1['consultaion_video'];
                               $charge3=$query_practices1['consultation_voice_call'];
                               
                           }
                             $fees=0;
            if($consultation_type=="visit")
            {
                
                $fees=$charge;
            }
            else if($consultation_type=="chat")
            {
                $consultation_type="online";
                $fees=$charge1;
            }
            else if($consultation_type=="video")
            {
                $consultation_type="online";
                $fees=$charge2;
            }
            else if($consultation_type=="call")
            {
                $consultation_type="online";
                $fees=$charge3;
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
                
               if($consultation_type=="online")
               {
                    $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '0' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
               }
               else
               {
                  $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id_new' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
               }
            //         echo "SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'";
            // die();   
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
                  $day_time_status = $this->db->query("SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id_new' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
           $day_time_status       = $this->db->query("SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id_new' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
       $queryTiming1 = $this->db->query("SELECT * FROM `doctor_slot_details` WHERE `doctor_id` = '$doctor_id' AND `clinic_id` = '$clinic_id_new' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
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
                    'clinic_id' => $clinic_id_new,
                    'clinic_name' => $clinic_name,
                    'consultation_charge' => $fees,
                    'listing_type'=>5,
                    'timings' => $time_slott
                    );
               
                      $clinic1_slott_1[] = array(
                        'day' => $todayDay,
                        'date' => $todayDate,
                        'clinic_array' => $time_slots
                    );
               
                $time_slots=array();  
                    
            }
          
            
          
            
            
            
                        }
                }
            $sql0 = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
            $query_practices0  = $this->db->query($sql0);
            $total_practices0  = $query_practices0->num_rows();
                       
            $get_pract0=$query_practices0->row_array();
            $ids=$get_pract0['hospital_doctor_id'];
            if(!empty($ids))
                {
                        $sql21             = "SELECT hd.*,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.id IN ($ids)";
                        $query_practices1  = $this->db->query($sql21);
                        $total_practices1  = $query_practices1->num_rows();
                         foreach ($query_practices1->result_array() as $get_pract2) {
                            $new_doctor_id        = $get_pract2['id'];
                            $clinic_id            = $get_pract2['hospital_id'];
                            $clinic_name          = $get_pract2['name_of_hospital'];
                            $charge=$get_pract2['consultation'];
                            $fees=$charge;
                            $consultation_type='visit';
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
                            for ($i = 0; $i < 7; $i++) 
                                {
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
                                    $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `hospital_doctor_timing` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                                    $count_day_time_slots = $day_time_slots->num_rows();
                                    foreach ($day_time_slots->result_array() as $row) 
                                            {
                                                $timeSlot = $row['timeSlot'];
                                                $from_time = $row['from_time'];
                                                $to_time = $row['to_time'];
                  if($dat_new== $todayDate)
                  {
                    $date2 =date('Y-m-d H:i:s', strtotime($from_time));
                    $date1;
                    if ($date2 > $date1)
                        {
                  $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
                    else
                        {
                    
                }
       
                 }
       else
       {
           $day_time_status       = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
       $queryTiming1 = $this->db->query("SELECT * FROM `hospital_doctor_slot_details` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
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
                    'listing_type'=>50,
                    'timings' => $time_slott
                    );
               
                      $clinic1_slott_2[] = array(
                        'day' => $todayDay,
                        'date' => $todayDate,
                        'clinic_array' => $time_slots
                    );
               
                $time_slots=array();  
                    
            }
          
            
          
            
            
            
                        }
                        
                        } 
                        else
                        {
                             $sql21             = "SELECT hd.*,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.id ='$doctor_id'";
                        $query_practices1  = $this->db->query($sql21);
                        $total_practices1  = $query_practices1->num_rows();
                         foreach ($query_practices1->result_array() as $get_pract2) {
                            $new_doctor_id        = $get_pract2['id'];
                            $clinic_id_new        = $get_pract2['hospital_id'];
                            $clinic_name          = $get_pract2['name_of_hospital'];
                            $charge=$get_pract2['consultation'];
                            $fees=$charge;
                            $consultation_type='visit';
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
                            for ($i = 0; $i < 7; $i++) 
                                {
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
                                    $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `hospital_doctor_timing` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id_new' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                                    $count_day_time_slots = $day_time_slots->num_rows();
                                    foreach ($day_time_slots->result_array() as $row) 
                                            {
                                                $timeSlot = $row['timeSlot'];
                                                $from_time = $row['from_time'];
                                                $to_time = $row['to_time'];
                  if($dat_new== $todayDate)
                  {
                    $date2 =date('Y-m-d H:i:s', strtotime($from_time));
                    $date1;
                    if ($date2 > $date1)
                        {
                  $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id_new' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
                    else
                        {
                    
                }
       
                 }
       else
       {
           $day_time_status       = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id_new' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
       $queryTiming1 = $this->db->query("SELECT * FROM `hospital_doctor_slot_details` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id_new' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
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
                    'listing_type'=>50,
                    'timings' => $time_slott
                    );
               
                      $clinic1_slott_2[] = array(
                        'day' => $todayDay,
                        'date' => $todayDate,
                        'clinic_array' => $time_slots
                    );
               
                $time_slots=array();  
                    
            }
          
            
          
            
            
            
                        }
                        }
            $clinic1_slott=array_merge($clinic1_slott_1,$clinic1_slott_2);
            }
        }
        else
        {
             $old_doctor=$doctor_id;
             $listing_type='';
            $sql0 = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
            $query_practices0  = $this->db->query($sql0);
            $total_practices0  = $query_practices0->num_rows();
            if($total_practices0 > 0)  
            {
                
            $listing_type='5';
            $get_pract0=$query_practices0->row_array();
            $ids=$get_pract0['hospital_doctor_id'];
            if(!empty($ids))
            {
                $doctor_id=$ids;
                
            }
            else
            {
                 $doctor_id;
            }
        }
        else
        {
            $listing_type='50';
            $doctor_id;
        }
            $clinic1_slott=array();
                        $sql2             = "SELECT hd.*,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.hospital_id='$hospital_id' and hd.id IN ($doctor_id)";
                        $query_practices  = $this->db->query($sql2);
                        $total_practices  = $query_practices->num_rows();
                        if ($total_practices > 0) {
                    foreach ($query_practices->result_array() as $get_pract) {
                         $new_doctor_id        = $get_pract['id'];
                            $clinic_id_new            = $get_pract['hospital_id'];
                            $clinic_name          = $get_pract['name_of_hospital'];
                            $charge=$get_pract['consultation'];
                            $fees=$charge;
                            $consultation_type='visit';
        
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
          
             $day_time_slots       = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `hospital_doctor_timing` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
                                    
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
   
     
              $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
        $day_time_status = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `doctor_id` = '$new_doctor_id' AND  `listing_id`='$clinic_id' AND `booking_date` = '$todayDate' And `from_time` = '$from_time' And `to_time` = '$to_time' AND `status` != '3' ");
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
            $queryTiming1 = $this->db->query("SELECT * FROM `hospital_doctor_slot_details` WHERE `doctor_id` = '$new_doctor_id' AND `clinic_id` = '$clinic_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay' AND time_slot='$timeSlot'" );
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
            
           
          /*  if($agent=="ios")
            {
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
            }*/
          /*  else
            {*/
             $time_slots[] = array(
                'day' => $todayDay,
                'date' => $todayDate,
                'timings' => $time_slott
            ); 
           
           /* }*/
            //$time_slots=array();  
                
        }
      
        
          $clinic1_slott[] = array(
                'clinic_id' => $clinic_id,
                'clinic_name' => $clinic_name,
                'consultation_charge' => $fees,
                'listing_type'=>$listing_type,
                'time_slot' => $time_slots
            );
        
        
        // print_r($clinic1_slott); die();
                    }
                }
                        else
                        {
                            $clinic1_slott=array();
                        }
        }
                 return $clinic1_slott;         
    
    } 
    public function insert_doctor_users_feedback($doctor_id, $user_id, $type, $feedback, $ratings, $recommend,$booking_id,$booking_type)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date           = date('Y-m-d H:i:s');
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
                $user_plike = $this->db->query("SELECT name FROM users WHERE id='$doctor_id'");
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $doctor_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $doctor_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $user_details = $this->db->query("SELECT name FROM users WHERE id='$user_id'"); 
                 $getdetails = $user_details ->row_array();
                 $user_name = $getdetails['name'];
                 
                $getusr = $user_plike->row_array();
                $usr_name = $getusr['name'];
                $msg = $usr_name . ', You got the feedback from ' . $user_name;
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$doctor_id'");
                $title = $usr_name . ', You got the feedback from ' . $user_name;
                $customer_token_count = $customer_token->num_rows();

                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = $token_status['token'];
                    $img_url = $userimage;
                    $tag = 'text';
                    $key_count = '1';
                    $this->send_gcm_notify_feedback($title, $reg_id, $msg, $img_url,$tag,$agent);
                    
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
                      'listing_id'  => $doctor_id,
                      'booking_id'  => "",
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'user_feedback',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
        //end
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
            $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
    
    //view appointment list by jakir 
    public function view_appointments_module($user_id)
    {
        $Appointment_dataList   = $this->db->query("SELECT * FROM `doctor_booking_master` where `user_id`= '$user_id' ");
        $count_appointment_slot = $Appointment_dataList->num_rows();
        if ($count_appointment_slot > 0) {
            foreach ($Appointment_dataList->result_array() as $row) {
                $booking_id        = $row['booking_id'];
                $user_id           = $row['user_id'];
                $listing_id        = $row['listing_id'];
                $clinic_id         = $row['patient_id'];
                $booking_date      = $row['booking_date'];
                $consultation_type = $row['consultation_type'];
                $from_time         = $row['from_time'];
                $to_time           = $row['to_time'];
                $description       = $row['description'];
                $status            = $row['status'];
                $query12           = $this->db->query("SELECT `name` FROM `users` where `id`= '$listing_id' ");
                $doctor            = $query12->row_array();
                $doctor_name       = $doctor['name'];
                $query12           = $this->db->query("SELECT `clinic_name` FROM `doctor_clinic` where `doctor_id`= '$listing_id' AND `id` = '$clinic_id'");
                $clinic            = $query12->row_array();
                $clinic_name       = $clinic['clinic_name'];
                $resultpost[]      = array(
                    'booking_id' => $booking_id,
                    'user_id' => $user_id,
                    'doctor_name' => $doctor_name,
                    'clinic_name' => $clinic_name,
                    'booking_date' => $booking_date,
                    'consultation_type' => $consultation_type,
                    'from_time' => $from_time,
                    'to_time' => $to_time,
                    'description' => $description,
                    'status' => $status
                );
            }
            return $resultpost;
        } else
            return array();
    }
    public function get_doctor_name($doctor_id)
    {
        // doctor_list
        $doctorNameRow = $this->db->query("SELECT * FROM `doctor_list` where `id`= '$doctor_id' ");
        foreach ($doctorNameRow->result_array() as $row) {
            $doctorName = $row['doctor_name'];
        }
        return $doctorName;
    }
    // doctor_prescription
    public function get_doctor_prescription($patient_id)
    {
        // $doctorprescriptionRows = $this->db->query("SELECT * FROM `doctor_prescription` where `doctor_id`= '$doctor_id' AND `patient_id`= '$patient_id' ");
        $doctorprescriptionRows = $this->db->query("SELECT * FROM `doctor_prescription` where `patient_id`= '$patient_id' ");
        return $doctorprescriptionRows;
    }
    // get_clinic_name
    public function get_clinic_name($clinic_id)
    {
        // doctor_list
        $clinicNameRow = $this->db->query("SELECT * FROM `doctor_clinic` where `id`= '$clinic_id' ");
        foreach ($clinicNameRow->result_array() as $row) {
            $clinicName = $row['clinic_name'];
        }
        return $clinicName;
    }
    // get_doctor_prescription_medicine
    public function get_doctor_prescription_medicine($prescription_id)
    {
        // doctor_list
        $prescriptionIdRow = $this->db->query("SELECT * FROM `doctor_prescription_medicine` where `prescription_id`= '$prescription_id' ");
        foreach ($prescriptionIdRow->result_array() as $rowId) {
            $medicineDetails['medicine_name']    = $rowId['medicine_name'];
            $medicineDetails['dosage_unit']      = $rowId['dosage_unit'];
            $medicineDetails['frequency_first']  = $rowId['frequency_first'];
            $medicineDetails['frequency_second'] = $rowId['frequency_second'];
            $medicineDetails['frequency_third']  = $rowId['frequency_third'];
            $medicineDetails['instruction']      = $rowId['instruction'];
            // $medicineDetails['prescription_id'] = $rowId['id'];
            $allMedicine[]                       = $medicineDetails;
        }
        return $allMedicine;
    }
    // get_doctor_prescription_test
    public function get_doctor_prescription_test($prescription_id)
    {
        // doctor_list
        $prescriptionTestRow = $this->db->query("SELECT * FROM `doctor_prescription_test` where `prescription_id`= '$prescription_id' ");
        foreach ($prescriptionTestRow->result_array() as $rowRow) {
            $testDetailsCategory = $rowRow['category'];
            $testCatRow          = $this->db->query("SELECT * FROM `doctor_test` where `id`= '$testDetailsCategory' ");
            foreach ($testCatRow->result_array() as $cat) {
                $testCategory = $cat['test_name'];
            }
            $test['category'] = $testCategory;
            $test['test']     = $rowRow['test'];
            $testDetails[]    = $test;
        }
        return $testDetails;
    }
    public function booking_details($booking_id)
    {
        //echo "SELECT `doctor_booking_master`.*,doctor_list.*,doctor_clinic.lat,doctor_clinic.lng,doctor_clinic.consultation_charges,doctor_clinic.map_location,doctor_clinic.address,vendor_discount.* FROM `doctor_booking_master` LEFT JOIN doctor_clinic ON(doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN doctor_list ON(doctor_booking_master.listing_id = doctor_list.user_id) LEFT JOIN vendor_discount ON(doctor_booking_master.listing_id = vendor_discount.vendor_id AND vendor_discount.discount_category = 'doctor_visit') WHERE doctor_booking_master.`booking_id` = '$booking_id' AND  ";
     
        $booking_details           = $this->db->query("SELECT `doctor_booking_master`.*,doctor_booking_master.consultation_type as c_type,doctor_list.*,doctor_clinic.lat,doctor_clinic.lng,doctor_clinic.consultation_charges,doctor_clinic.map_location,doctor_clinic.address,vendor_discount.* FROM `doctor_booking_master` LEFT JOIN doctor_clinic ON(doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN doctor_list ON(doctor_booking_master.listing_id = doctor_list.user_id) LEFT JOIN vendor_discount ON(doctor_booking_master.listing_id = vendor_discount.vendor_id AND vendor_discount.discount_category = 'doctor_visit') WHERE doctor_booking_master.`booking_id` = '$booking_id'");
        $booking_details           = $booking_details->row_array();
        $doctor_id                 = $booking_details['listing_id'];
        $clinic_id                 = $booking_details['clinic_id'];
        $booking_id                = $booking_details['booking_id'];
        $booking_date              = $booking_details['booking_date'];
        $booking_time              = $booking_details['booking_time'];
        $created_date              = $booking_details['created_date'];
        $consultation_type         = $booking_details['c_type'];
        $status                    = $booking_details['status'];
        $doctor_name               = $booking_details['doctor_name'];
        $doctor_email              = $booking_details['email'];
        $doctor_experience         = $booking_details['experience'];
        $speciality                = $booking_details['speciality'];
        $doctor_dob                = $booking_details['dob'];
        $doctor_telephone          = $booking_details['telephone'];
        $doctor_lat                = $booking_details['lat'];
        $doctor_lng                = $booking_details['lng'];
        $doctor_address            = $booking_details['map_location'];
        $doctor_consultation_visit = $booking_details['consultation_charges'];
        $discount_amount           = $booking_details['discount_min'];
        $discount_type             = $booking_details['discount_type'];
        $discount_limit            = $booking_details['discount_limit'];
        $discount_category         = $booking_details['discount_category'];
        $visit_charge              = "";
        if ($discount_category == "doctor_visit") {
            if ($discount_type == "percent") {
                $visit_discount_amount = $doctor_consultation_visit * ($discount_amount / 100);
                if ($visit_discount_amount > $discount_limit) {
                    $visit_discount_amount = $discount_limit;
                } else {
                    $visit_discount_amount = $visit_discount_amount;
                }
                $visit_charge = $doctor_consultation_visit - $visit_discount_amount;
            } else if ($discount_type == "rupees") {
                $visit_discount_amount = $doctor_consultation_visit - $discount_amount;
                if ($visit_discount_amount > $discount_limit) {
                    $visit_discount_amount = $discount_limit;
                } else {
                    $visit_discount_amount = $visit_discount_amount;
                }
                $visit_charge = $doctor_consultation_visit - $visit_discount_amount;
            }
            if ($visit_charge < 0) {
                $visit_charge = 0;
            }
        }
        
        
        
        //doctor consultaion
        if($consultation_type == 'visit')
        {
            
        }
        else
        {
        $clinic_id = 0;
        }
        $results = array();
        $available_call = "0";
        $available_video = "0";
        $available_chat = "0";
        $results_call = array();
        $results_video = array();
        $results_chat = array();
        $q = $this->db->query("SELECT * FROM `doctor_consultation` WHERE `doctor_user_id` = '$doctor_id' AND consultation_name<>'visit'");
        
        $qRows = $q->result_array();
        
        foreach($qRows as $qRow){
            if($qRow['consultation_name'] == 'chat' && $qRow['is_active'] == 1){
                $available_call = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_call['consultation_type'] = 'call';
                $results_call['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
            if($qRow['consultation_name'] == 'video' && $qRow['is_active'] == 1){
                $available_video = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_video['consultation_type'] = 'video';
                $results_video['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
             if($qRow['consultation_name'] == 'chat' && $qRow['is_active'] == 1){
                $available_chat = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_chat['consultation_type'] = 'chat';
                $results_chat['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
        }
      
      if($results_call){$results[] = $results_call;}
      if($results_video){$results[] = $results_video;}
      if($results_chat){$results[] = $results_chat;}
        
        
        // doctor_consultation
        if($clinic_id == "0"){
          
            $charges = $this->db->query("SELECT * FROM `doctor_consultation`  WHERE `doctor_user_id` = '$doctor_id' AND `consultation_name` = '$consultation_type'");
            // [charges]
            foreach($charges->result_array() as $charge ){
                $doctor_consultation_visit = $charge['charges'];
            }
              
        }
        $doctor_consultation_video      = $booking_details['consultaion_video'];
        $doctor_consultation_voice_call = $booking_details['consultation_voice_call'];
        $doctor_image                   = $booking_details['image'];
        if ($doctor_image != '') {
            $doctor_image = str_replace(' ', '%20', $doctor_image);
            $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
        } else {
            $doctor_image = 'https://medicalwale.com/img/doctor_default.png';
        }
        $doctor_ratings       = $booking_details['rating'];
        $doctor_qualification = $booking_details['qualification'];
        $doctor_category      = $booking_details['category'];
        $area_expertise       = array();
        $query_sp             = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $doctor_category . "')");
        $total_category       = $query_sp->num_rows();
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
        $degree_array  = array();
        $degree_       = explode(',', $doctor_qualification);
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
        $special_array = array();
        $speciality    = explode(",", $speciality);
        $specialitycnt = count($speciality);
        //$specialitycnt--;
        if ($specialitycnt > 0) {
            for ($j = 0; $j < $specialitycnt; $j++) {
                $special_array[] = array(
                    'specialization' => $speciality[$j]
                );
            }
        } else {
            $special_array = array();
        }
        // $special_ = explode(',', $speciality);
        // $count_special_ = count($special_);
        // if ($count_special_ > 1) {
        //     foreach ($special_ as $special_) {
        //         $special_array[] = array(
        //             'specialization' => $special_
        //         );
        //     }
        // } else {
        //     $special_array = array();
        // }
        
        
        //added for to handle null
        if($doctor_id == null)
        {
            $doctor_id = '';
        }
        
        $doctor_id = ($doctor_id == null) ? '' : $doctor_id;
        
        $testDetails = array(
            'doctor_id' => $doctor_id,
            'clinic_id' => $clinic_id,
            'booking_id' => $booking_id,
            'booking_date' => $booking_date,
            'booking_time' => $booking_time,
            'created_date' => $created_date,
            'consultation_type' => $consultation_type,
            'doctor_name' => $doctor_name,
            'email' => $doctor_email,
            'experience' => $doctor_experience,
            'dob' => $doctor_dob,
            'telephone' => $doctor_telephone,
            'info' => $results,
            'lat' => $doctor_lat,
            'lng' => $doctor_lng,
            'doctor_address' => $doctor_address,
            'doctor_image' => $doctor_image,
            'rating' => $doctor_ratings,
            'degree' => $degree_array,
            'status' => $status,
            'doctor_specialization' => $special_array,
            'area_expertise' => $area_expertise,
            'consultation_charges' => $doctor_consultation_visit,
            'discount_amount_min' => $booking_details['discount_min'],
            'discount_amount_max' => $booking_details['discount_max'],
            'discount_type' => $booking_details['discount_type'],
            'discount_limit' => $booking_details['discount_limit'],
            'discount_category' => $booking_details['discount_category'],
            'payable_amount' => $visit_charge
        );
        return $testDetails;
    }
    // vendor_discount
    public function vendor_discount($vendor_id, $clinic_id)
    {
        $discount      = array();
        $call          = array();
        $text          = array();
        $video         = array();
        $clinic        = array();
        $today         = date('Y-m-d');
        $doctorCharges = $this->db->query("SELECT * FROM `doctor_list` WHERE id = '$vendor_id'");
        $count         = $doctorCharges->num_rows();
        if ($count > 0) {
            foreach ($doctorCharges->result_array() as $row) {
                $consultation_video     = $row['consultation_video'];
                $consultaion_chat       = $row['consultaion_chat'];
                $consultaion_voice_call = $row['consultaion_voice_call'];
            }
        }
        // $clinic_id
        if ($clinic_id > 0) {
            $doctorClinicCharges = $this->db->query("SELECT * FROM `doctor_clinic` WHERE id = '$clinic_id' AND doctor_id = '$vendor_id'");
            $doctorClinicCount   = $doctorClinicCharges->num_rows();
            if ($doctorClinicCount > 0) {
                foreach ($doctorClinicCharges->result_array() as $rowClinic) {
                    $consultation_visit = $rowClinic['consultation_charges'];
                }
            }
        }
        $vendorDiscount      = $this->db->query("SELECT * FROM `vendor_discount` WHERE vendor_id = '$vendor_id'");
        //  doctor_voice
        $vendorDiscountCount = $vendorDiscount->num_rows();
        if ($vendorDiscountCount > 0) {
            foreach ($vendorDiscount->result_array() as $row1) {
                // print_r($row1);
                $discountAmt   = $row1['discount_amount'];
                $discountLimit = $row1['discount_limit'];
                $discountExp   = $row1['discount_exp'];
                if (strtotime($today) <= strtotime($discountExp)) {
                    if ($row1['discount_category'] == "doctor_chat") {
                        if ($row1['discount_type'] == "percent") {
                            $chatDisCharge = $consultaion_chat * ($discountAmt / 100);
                            if ($chatDisCharge > $discountLimit) {
                                $chatDisCharge = $discountLimit;
                            } else {
                                $chatDisCharge = $chatDisCharge;
                            }
                            $chatCharge = $consultaion_chat - $chatDisCharge;
                        } else if ($row1['discount_type'] == "rupees") {
                            $chatDisCharge = $consultaion_chat - $discountAmt;
                            if ($chatDisCharge > $discountLimit) {
                                $chatDisCharge = $discountLimit;
                            } else {
                                $chatDisCharge = $chatDisCharge;
                            }
                            $chatCharge = $consultaion_chat - $chatDisCharge;
                        }
                        $details['amount']         = $consultaion_chat;
                        $details['payable_amount'] = $chatCharge;
                        $details['total_discount'] = $chatDisCharge;
                        $details['discount']       = $discountAmt;
                        $details['discount_type']  = $row1['discount_type'];
                        $details['discount_limit'] = $discountLimit;
                        $details['category']       = $row1['discount_category'];
                        $details['expiry']         = $discountExp;
                        $detailsAll[]              = $details;
                    }
                    if ($row1['discount_category'] == "doctor_call") {
                        if ($row1['discount_type'] == "percent") {
                            $voiceDisCharge = $consultaion_voice_call * ($discountAmt / 100);
                            if ($voiceDisCharge > $discountLimit) {
                                $voiceDisCharge = $discountLimit;
                            } else {
                                $voiceDisCharge = $voiceDisCharge;
                            }
                            $voiceCharge = $consultaion_voice_call - $voiceDisCharge;
                        } else if ($row1['discount_type'] == "rupees") {
                            $voiceDisCharge = $consultaion_voice_call - $discountAmt;
                            if ($voiceDisCharge > $discountLimit) {
                                $voiceDisCharge = $discountLimit;
                            } else {
                                $voiceDisCharge = $voiceDisCharge;
                            }
                            $voiceCharge = $consultaion_voice_call - $voiceDisCharge;
                        }
                        $details['amount']         = $consultaion_voice_call;
                        $details['payable_amount'] = $voiceCharge;
                        $details['total_discount'] = $voiceDisCharge;
                        $details['discount']       = $discountAmt;
                        $details['discount_type']  = $row1['discount_type'];
                        $details['discount_limit'] = $discountLimit;
                        $details['category']       = $row1['discount_category'];
                        $details['expiry']         = $discountExp;
                        $detailsAll[]              = $details;
                    }
                    if ($row1['discount_category'] == "doctor_video") {
                        if ($row1['discount_type'] == "percent") {
                            $videoDisCharge = $consultation_video * ($discountAmt / 100);
                            if ($videoDisCharge > $discountLimit) {
                                $videoDisCharge = $discountLimit;
                            } else {
                                $videoDisCharge = $videoDisCharge;
                            }
                            $videoCharge = $consultation_video - $videoDisCharge;
                        } else if ($row1['discount_type'] == "rupees") {
                            $videoDisCharge = $consultation_video - $discountAmt;
                            if ($videoDisCharge > $discountLimit) {
                                $videoDisCharge = $discountLimit;
                            } else {
                                $videoDisCharge = $videoDisCharge;
                            }
                            $videoCharge = $consultation_video - $videoDisCharge;
                        }
                        $details['amount']         = $consultation_video;
                        $details['payable_amount'] = $videoCharge;
                        $details['total_discount'] = $videoDisCharge;
                        $details['discount']       = $discountAmt;
                        $details['discount_type']  = $row1['discount_type'];
                        $details['discount_limit'] = $discountLimit;
                        $details['category']       = $row1['discount_category'];
                        $details['expiry']         = $discountExp;
                        $detailsAll[]              = $details;
                    }
                    if ($clinic_id > 0) {
                        if ($row1['discount_category'] == "doctor_visit") {
                            // $consultation_visit
                            /* $visitCharge = "";
                            $visitDisCharge = "";*/
                            if ($row1['discount_type'] == "percent") {
                                $visitDisCharge = $consultation_visit * ($discountAmt / 100);
                                if ($visitDisCharge > $discountLimit) {
                                    $visitDisCharge = $discountLimit;
                                } else {
                                    $visitDisCharge = $visitDisCharge;
                                }
                                $visitCharge = $consultation_visit - $visitDisCharge;
                            } else if ($row1['discount_type'] == "rupees") {
                                $visitDisCharge = $consultation_visit - $discountAmt;
                                if ($visitDisCharge > $discountLimit) {
                                    $visitDisCharge = $discountLimit;
                                } else {
                                    $visitDisCharge = $visitDisCharge;
                                }
                                $visitCharge = $consultation_visit - $visitDisCharge;
                            }
                            $details['amount']         = $consultation_visit;
                            $details['payable_amount'] = $visitCharge;
                            $details['total_discount'] = $visitDisCharge;
                            $details['discount']       = $discountAmt;
                            $details['discount_type']  = $row1['discount_type'];
                            $details['discount_limit'] = $discountLimit;
                            $details['category']       = $row1['discount_category'];
                            $details['expiry']         = $discountExp;
                            $detailsAll[]              = $details;
                        }
                    }
                } else {
                    $details['expiry'] = "discount expired";
                    $detailsAll[]      = $details;
                }
            }
        }
        //  die();
        $resp = array(
            "status" => 200,
            "data" => $detailsAll
        );
        return $resp;
    }
    /*
    User Approval Status
    1=Confirm 
    2=Reschedule
    2 task in this method
    1.Doctor Appointment Status Change
    2.Notification trigger
    
    
    1 = booked by user / awaiting confirmation from doctor
    2 = doctor confirm ( payment pending from user side)
    3 = doctor cancel (doctor cancel or user canceled)
    4 = rescheduled by doctor (awaiting confirmation from user)
    5 = user confirm (after payment done meeting is schedule for perticular date ) 
    6 = awaiting feedback 
    7 = completed (all process done completed all meetings)
    */
    public function user_payment_approval($doctor_id, $status, $booking_id, $user_id)
    {
        //echo $doctor_id . ',' . $status . ',' . $booking_id . ',' . $user_id;
        //die();
        date_default_timezone_set('Asia/Kolkata');
        $date         = date('Y-m-d H:i:s');
        //$status = strtolower($status);
        $status       = $status; // 2 - if doctor confirm timing ,4 doctor cancelled timing ,6 reschedule
        //echo "SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'";
        $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'");
        $count_user   = $table_record->num_rows();
        if ($count_user > 0) {
            $booking_array = array(
                'status' => $status,
                'created_date' => $date
            );
            $updateStatus  = $this->db->where('booking_id', $booking_id)->update('doctor_booking_master', $booking_array);
            if (!$updateStatus) {
                return array(
                    'status' => 204,
                    'message' => 'Update failed'
                );
            }
            if ($status == '5') //user confirm, payment pending
                {
                $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->confirm_status($user_id, $booking_id, $doctor_id);
            }
            else if ($status == '8')
            {
                 $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->confirm_cash_on_delivery_status($user_id, $booking_id, $doctor_id);
            }
            else if ($status == '3') //cancel appointment 
                {
                $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->cancel_status($user_id, $booking_id, $doctor_id);
            }
        } else {
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
    public function confirm_status($user_id, $booking_id, $listing_id)
    {
        $appointment_status = '5';
        //$table_record1       = $this->db->query("SELECT doctor_name FROM doctor_list WHERE user_id='$listing_id' ");
        $table_record       = $this->db->query("SELECT name as patient_name FROM users WHERE id='$user_id'");
        $count_user         = $table_record->num_rows();
        
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
        if ($count_user > 0) {
            $row         = $table_record->row();
            $patient_name = $row->patient_name;
            $title       = $patient_name . ' has Confirmed and Paid';
            $msg         = $patient_name . '  has Confirmed and Paid for'. $consultation_type .' appointment on '.$newdate .' at '.$newtime  ;
           // $this->notifyDoctorMethod($listing_id, $appointment_status, $booking_id, $patient_name, $title, $msg);
            $this->notifyDoctorMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name);
           
        }
    }
    /*
    Cancel Status 
    User has canceled the appointment.
    */
    public function cancel_status($user_id, $booking_id, $listing_id)
    {
        $appointment_status = '3';
        $table_record       = $this->db->query("SELECT name as patient_name FROM users WHERE id='$user_id'");
        $count_user         = $table_record->num_rows();
        
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
        
        
        if ($count_user > 0) {
            $row         = $table_record->row();
            $patient_name = $row->patient_name;
            $title       = $patient_name . ' has Cancel an Payment';
            $msg         = $patient_name . '  has Cancel an Payment.';
            //$this->notifyDoctorMethod($listing_id, $appointment_status, $booking_id, $patient_name, $title, $msg);
             $this->notifyDoctorMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name);
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

    /*
    This method is used for notification.
    Left doctor service.
    */
    // public function notifyDoctorMethod($user_id, $appointment_status, $booking_id, $doctor_name,$title,$msg)
    // {

    //       $customer_token = $this->db->query("SELECT name,token,agent,token_status FROM users WHERE id='$user_id'");
    //       $customer_token_count = $customer_token->num_rows();
    //         if ($customer_token_count > 0) {
    //             $token_status = $customer_token->row_array();
    //             // $getusr = $user_plike->row_array();

    //             $usr_name = $token_status['name'];
    //             $agent = $token_status['agent'];
    //             $reg_id = $token_status['token'];
    //             $img_url = 'https://s3.amazonaws.com/medicalwale/images/img/doctor.png';
    //             $tag = 'text';
    //             $key_count = '1';
    //             $title = $title;
    //             $msg = $msg;
    //             $this->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_id);
    //         }
    // }


    //     //send notification through firebase
    //     /* notification to send in the doctor app for appointment confirmation*/
    // public function send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_id) {
     
    //     date_default_timezone_set('Asia/Kolkata');
    //     $date = date('j M Y h:i A');
        
    //     if (!defined("GOOGLE_GCM_URL"))
    //         define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
    //     $fields = array(
    //         'to' => $reg_id,
    //         'priority' => "high",
    //         $agent === 'android' ? 'data' : 'notification' => array(
    //             "title" => $title,
    //             "message" => $msg,
    //             "notification_image" => $img_url,
    //             "tag" => $tag, 
    //             'sound' => 'default',
    //             "notification_type" => 'appointment_notifications',
    //             "notification_date" => $date,
    //             "booking_id" => $booking_id
    //          //   app date app time  app it 
    //         )
    //     );
       
    //     $headers = array(
    //             GOOGLE_GCM_URL,
    //             'Content-Type: application/json',
    //             $agent === 'android' ? 'Authorization: key=AAAAMCq6aPA:APA91bFjtuwLeq5RK6_DFrXntyJdXCFSPI0JdJcvoXXi0xIGm7qgzQJmUl7aEq3HjUTV3pvlFr5iv5pOWtCoN3JIpMSVhU8ZFzusaibehi5MPwThRmx1pCnm3Tm-x6wI8tGwhc0eUj2U' : 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E'
    //         );
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    //     $result = curl_exec($ch);
    //     if ($result === FALSE) {
    //          die('Problem occurred: ' . curl_error($ch));
    //     }
        
    //     curl_close($ch);
    // }
    
    
    
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


        //send notification through firebase
        /* notification to send in the doctor app for appointment confirmation*/
    public function send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent,$booking_date,$booking_time,$booking_id,$consultation_type) {
     
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
                "notification_type" => 'appointment_notifications',
                "notification_date" => $date,
                "appointment_id" => $booking_id,
                "appointment_date" => $booking_date,
                "appointment_time" =>$booking_time,
                "type_of_connect" => ''
             //   app date app time  app it 
            )
        );
       
        $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AAAAMCq6aPA:APA91bFjtuwLeq5RK6_DFrXntyJdXCFSPI0JdJcvoXXi0xIGm7qgzQJmUl7aEq3HjUTV3pvlFr5iv5pOWtCoN3JIpMSVhU8ZFzusaibehi5MPwThRmx1pCnm3Tm-x6wI8tGwhc0eUj2U' : 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E'
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
    
    
    public function send_gcm_notify_user_up($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_id)
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
                "notification_type" => 'appointment_notifications',
                "notification_date" => $date,
                "appointment_id" => $booking_id,
                "appointment_date" => '',
                "appointment_time" =>'',
                "type_of_connect" => ''
             //   app date app time  app it 
            )
        );
       
        $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AAAAMCq6aPA:APA91bFjtuwLeq5RK6_DFrXntyJdXCFSPI0JdJcvoXXi0xIGm7qgzQJmUl7aEq3HjUTV3pvlFr5iv5pOWtCoN3JIpMSVhU8ZFzusaibehi5MPwThRmx1pCnm3Tm-x6wI8tGwhc0eUj2U' : 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E'
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
    
    // get_doctor_consultation
    
    public function get_doctor_consultation($doctor_id) {
        $clinic_id = 0;
        $results = array();
        $available_call = "0";
        $available_video = "0";
        $available_chat = "0";
        $results_call = array();
        $results_video = array();
        $results_chat = array();
        $q = $this->db->query("SELECT * FROM `doctor_consultation` WHERE `doctor_user_id` = '$doctor_id' ");
        
        $qRows = $q->result_array();
        
        foreach($qRows as $qRow){
            if($qRow['consultation_name'] == 'chat' && $qRow['is_active'] == 1){
                $available_call = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_call['consultation_type'] = 'call';
                $results_call['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
            if($qRow['consultation_name'] == 'video' && $qRow['is_active'] == 1){
                $available_video = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_video['consultation_type'] = 'video';
                $results_video['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
             if($qRow['consultation_name'] == 'chat' && $qRow['is_active'] == 1){
                $available_chat = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_chat['consultation_type'] = 'chat';
                $results_chat['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
        }
      
      if($results_call){$results[] = $results_call;}
      if($results_video){$results[] = $results_video;}
      if($results_chat){$results[] = $results_chat;}

     
        $data = array ('doctor_id' => $doctor_id, 'available_call' => $available_call, 'available_video' => $available_video, 'available_chat' => $available_chat, 'info' => $results);
        return $data;
    }
    
    // edit_bookings
    public function edit_bookings($booking_id, $newdata) {
        // $data['booking_id'] = $booking_id;
        
        $updateStatus  = $this->db->where('booking_id', $booking_id)->update('doctor_booking_master', $newdata);
        $data = $newdata;
        return $data;
    }
    
    public function get_cart_details_list($user_id)
    {
          $query = $this->db->query("SELECT * FROM `cart` WHERE `user_id` = '$user_id' ");
        $num_count = $query->num_rows();
        $qRows = $query->result_array();
       // print_r ($num_count);
      //  print_r ($qRows);
      //  die();
        if($num_count>0)
        {
        foreach($qRows as $qRow)
        {
            $id = $qRow['id'];
            $user_id = $qRow['user_id'];
            $listing_id =$qRow['listing_id'];
            $product_id =$qRow['product_id'];
            $sub_category = $qRow['sub_category'];
            $quantity = $qRow['quantity'];
            $product_type = $qRow['product_type'];
            $status = $qRow['status'];
            //  echo "SELECT * FROM `product` WHERE `id` = '$id' and `sub_category` = '$sub_category' ";
              $product_query = $this->db->query("SELECT * FROM `product` WHERE `id` = '$product_id' and `sub_category` = '$sub_category' ");
               $nume_count = $product_query->num_rows();
              $product_Rows = $product_query->result_array();
            //  print_r ($nume_count);
            //  print_r ($product_Rows);
            //  die();
            if($nume_count>0)
            {
                foreach($product_Rows as $product_Row)
                {
                    $product_name = $product_Row['product_name'];
                   $product_price = $product_Row['product_price'];
                   $product_description = $product_Row['product_description'];
                   $product_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/'. $product_Row['image'];
                   $in_stock = $product_Row['in_stock'];
                   $product_weight = $product_Row['pack'];
                }
            }
            else
            {
                $product_name = "";
                   $product_price = "";
                   $product_description = "";
                   $product_image = "";
                   $in_stock = "";
                   $product_weight = "";
            }
            
             $results_cart[] = array(
                        'id' => $id,
                        'user_id' => $user_id,
                        'listing_id' =>$listing_id,
                        'product_id' =>$product_id,
                        'sub_category' =>$sub_category,
                        'quantity' =>$quantity,
                        'product_type'=>$product_type,
                        'status'=>$status,
                        'product_name' => $product_name,
                        'product_price' => $product_price,
                        'product_image' => $product_image,
                        'product_description' => $product_description,
                        'in_stock' => $in_stock,
                        'product_weight' => $product_weight
                    ); 
        }
            $data = $results_cart;
            return $data;
        }
        else
        {
           return array(
                'status' => 200,
                'data' => array(),
                'message' => 'data not found'
            ); 
        }
    }
    
    public function recent_doctor_list($user_id){
        
        //echo "SELECT * FROM doctor_booking_master WHERE user_id='$user_id' GROUP BY listing_id";
        $booking_master = $this->db->query("SELECT * FROM doctor_booking_master WHERE user_id='$user_id' GROUP BY listing_id");
        $booking_count = $booking_master->num_rows();
        $qRows = $booking_master->result_array();
        if($booking_count>0)
        {
            foreach($qRows as $qRow)
            {
                $listing_id = $qRow['listing_id'];
                //echo "SELECT * FROM doctor_list WHERE user_id='$listing_id'";
                $doctor_list = $this->db->query("SELECT * FROM doctor_list WHERE user_id='$listing_id'");
                $doctor_list_count = $doctor_list->num_rows();
                $qRows2 = $doctor_list->row();
                if($doctor_list_count>0)
                {
                    $doctor_name         = $qRows2->doctor_name;
                        $email               = $qRows2->email;
                        $gender              = $qRows2->gender;
                        $doctor_phone        = $qRows2->telephone;
                        $dob                 = $qRows2->dob;
                        $category            = $qRows2->category;
                        $speciality          = $qRows2->speciality;
                        $service             = $qRows2->service;
                        $degree              = $qRows2->qualification;
                        $experience          = $qRows2->experience;
                        $reg_council         = $qRows2->reg_council;
                        $reg_number          = $qRows2->reg_number;
                        $doctor_user_id      = $qRows2->user_id;
                        $address             = $qRows2->address;
                        
                        $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                        $row_rating   = $query_rating->row_array();
                        $total_rating = $row_rating['total_rating'];
                        if ($total_rating === NULL || $total_rating === '') {
                            $total_rating = '0';
                        }
                        
                        if ($qRows2->image != '') {
                            $profile_pic = $qRows2->image;
                            $profile_pic = str_replace(' ', '%20', $profile_pic);
                            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                        }else{
                            $profile_pic = 'https://medicalwale.com/img/doctor_default.png';
                        }
                        
                       $resultpost[] = array(
                            'doctor_user_id' => $doctor_user_id,
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
                            'rating' => $total_rating
                        );
                }
            }
            return $resultpost;
        }
        else{
            return $resultpost = array();
        }
        
        
    }
    
        public function doctor_notification_confirm($booking_id){
       $data="SELECT doctor_booking_master.*,users.name,users.email,users.phone,doctor_clinic.clinic_name,doctor_list.telephone,doctor_list.doctor_name FROM doctor_booking_master LEFT JOIN users ON (doctor_booking_master.user_id=users.id) LEFT JOIN doctor_clinic ON (doctor_booking_master.clinic_id=doctor_clinic.id) 
          LEFT JOIN doctor_list ON (doctor_booking_master.listing_id=doctor_list.user_id)WHERE doctor_booking_master.booking_id='".$booking_id."'";
         
         $result = $this->db->query($data)->row();
            return $result;
}

 public function hospitals_notification_confirm($booking_id){
       $data="SELECT hospital_booking_master.*,users.name,users.email,users.phone,hospitals.name_of_hospital,hospitals.phone,hospital_doctor_list.doctor_name FROM hospital_booking_master LEFT JOIN users ON (hospital_booking_master.user_id=users.id) LEFT JOIN hospitals ON (hospital_booking_master.listing_id=hospitals.user_id) 
          LEFT JOIN hospital_doctor_list ON (hospital_booking_master.doctor_id=hospital_doctor_list.id)WHERE hospital_booking_master.booking_id='".$booking_id."'";
         
         $result = $this->db->query($data)->row();
            return $result;
}
//added for doctor prescription list 

  public function doctor_prescription_list($user_id){
        
        //echo "SELECT * FROM doctor_booking_master WHERE user_id='$user_id' GROUP BY listing_id";
        //echo "SELECT dp.*,dbm.user_id FROM  doctor_booking_master as dbm LEFT JOIN  doctor_prescription as dp ON dbm.booking_id=dp.booking_id  where dbm.user_id= '$user_id' ";
         $resultpost = array();
         $doctorprescriptionRows = $this->db->query("SELECT dp.*,dbm.booking_id FROM  doctor_booking_master as dbm LEFT JOIN  doctor_prescription as dp ON dbm.booking_id=dp.booking_id  where dbm.user_id= '$user_id' ");
       
        $pre_count = $doctorprescriptionRows->num_rows();
        $qRows = $doctorprescriptionRows->result_array();
        if($pre_count>0)
        {
            foreach($qRows as $qRow)
            {
               
                //echo "SELECT * FROM doctor_list WHERE user_id='$listing_id'";
             
               $prescription_id = $qRow['id'];
             
             if($prescription_id!="")
             {
             
                
                     $prescription_id = $qRow['id'];
                     $doctor_id  = $qRow['doctor_id'];
                     $patient_id   = $qRow['patient_id'];
                     $prescription_note = $qRow['prescription_note'];
                     $prescription_date = $qRow['created_date'];
                     $doctor_list = $this->db->query("SELECT `doctor_name` FROM doctor_list WHERE user_id='$doctor_id'");
                 $doctor_list_count = $doctor_list->num_rows();
                $qRows2 = $doctor_list->row();
                if($doctor_list_count > 0)
                {
                    $doctor_name = $qRows2->doctor_name;
                }
                else
                {
                    $doctor_name = "";
                }
                       $resultpost[] = array(
                            'p_id'=>$prescription_id,
                            'prescription_id' => 'https://doctor.medicalwale.com/doctor_prescription.php?id='.$prescription_id,
                            'doctor_id' => $doctor_id,
                            'patient_id' => $patient_id,
                            'prescription_note' => $prescription_note,
                            'doctor_name' => $doctor_name,
                            'prescription_date' => $prescription_date,
                            'prescription_pdf'=> "https://doctor.medicalwale.com/prescription/".$prescription_id.".pdf"
                        );
                }
           
             
            }
           
            return $resultpost;
        }
        else{
            return $resultpost = array();
        }
        
        
    }
    
     public function exotel_call($http_result, $type,$user_id,$listing_id) {
        date_default_timezone_set("Asia/Kolkata");
        $date = date('Y-m-d H:i:s');

        $xml = simplexml_load_string($http_result);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);

        $arr_call = array('sid' => $array['Call']['Sid'],
            'call_from' => $array['Call']['From'],
            'call_to' => $array['Call']['To'],
            'PhoneNumberSid' => $array['Call']['PhoneNumberSid'],
            'StartTime' => $array['Call']['StartTime'],
            'status' => $array['Call']['Status'],
            'type' => $type,
            'datetime' => $date
        );

        $this->db->insert('exotel', $arr_call);
        
      /*  $customer_token       = $this->db->query("SELECT name,token,agent,token_status,web_token,phone FROM users WHERE id='$user_id'");
        $customer_token_count = $customer_token->num_rows();
        if ($customer_token_count > 0) {
            $token_status = $customer_token->row_array();
             $usr_name     = $token_status['name'];
            $agent        = $token_status['agent'];
            $reg_id       = $token_status['token'];
            $web_token    = $token_status['web_token'];
            $img_url      = 'https://s3.amazonaws.com/medicalwale/images/img/doctor.png';
            $tag          = 'text';
            $key_count    = '1';
            $title        = 'Testing';
            $msg          = 'Testing message';
            
            $type = 'Booking';
            
            
            // $this->send_gcm_notify_call($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent);
            $message      = 'has Booked Appointment';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $token_status['phone'],
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
            
        } */   
        
        
    }
    
      public function send_gcm_notify_call($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent)
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
                      'booking_id'  => "",
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'doctor_call_notifications',
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
                    "notification_type" => 'doctor_call_notifications',
                    "notification_date" => $date,
                    "listing_id"=>$listing_id
                    
                )
            );
            /*IOS registration is left */
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
    
     public function delete_question($user_id) {

        $this->db->where('user_id', $user_id);
        $this->db->delete('userprofile_question_answer');
    }
     public function update_userprofile_question($data2) {


        $this->db->insert('userprofile_question_answer', $data2);
    }
    
    
    // add_referral_code
   
  public function coupon_code($user_id,$coupon,$listing_id,$listing_type,$amount,$card_type)
  {
         date_default_timezone_set('Asia/Kolkata');
        $booking_id = date("YmdHis");
        $created_at = date('Y-m-d H:i:s');
        $current_date = date('Y-m-d');
        if($listing_type=="35")
        {
             // Bachat Health Card
        $query1 = $this->db->query("SELECT * FROM vendor_offers WHERE name='$coupon' and offer_on='$card_type' and end_date >= '$current_date'  ");
        $row_fitness1 = $query1->row_array();
        $count1 = $query1->num_rows();  
          if($count1 > 0)
               {
            $id=$row_fitness1['id'];
            $query = $this->db->query("SELECT  v.id, v.vendor_id,v.listing_id FROM vendor_offers as v LEFT JOIN use_coupon as us ON us.coupon=v.id  WHERE us.user_id='$user_id' AND us.coupon='$id' AND us.use_status ='1'");
            $row_fitness = $query->row_array();
            $count = $query->num_rows();
            if ($count > 0)
            {
                $resp = array(
                                'status' => 201,
                                'message' => 'Coupon Already Used'
                            );
            }
            else
            {
             
                  
                  $vendor_type=$row_fitness1['listing_id'];
                  $save_type=$row_fitness1['save_type'];
                  $price=$row_fitness1['price'];
                  $max=$row_fitness1['max_discound'];
                  $subamount=0;
                  $discount=0;
                  if($price > 0 && $amount > 0 )
                  {
                     
                      if($save_type=="rupee")
                      {
                          $subamount=$amount-$price;
                          $discount=$price;
                           
                      }
                      else
                      {
                          $discount=$amount*($price/100);
                          $subamount=$amount-$discount;
                         
                          
                      }
                      
                     
                      if($subamount > $max)
                      {
                        
                          $subamount=$max;
                      }
                      elseif($subamount < 0)
                      {
                          $subamount=0;
                      }
                  } 
                   //coupon
                   if($row_fitness['use_status'] == 0)
                  {
                      
                  }
                  else
                  {
                    $coupon_array = array(
                          'user_id' => $user_id,
                          'coupon'  => $id,
                          'vendor_id' => 0,
                          'vendor_type' => $vendor_type,
                          'created_at'  => date('Y-m-d H:i:s'),
                          'use_status' => 0
                          );
                    $this->db->insert('use_coupon', $coupon_array);
                  } 
                    
                    
                    $resp = array(
                                'status' => 200,
                                'message' => 'Success',
                                'totalamount'=>$amount,
                                'subtotal'=>strval($subamount),
                                'discount'=>$discount,
                                'coupon_id'=>$id
                            );
                  
                   
             
                
            }
          }
          else
               {
                      $resp = array(
                                'status' => 201,
                                'message' => 'Invalid Coupon Code'
                            );   
                }
        }
        if($listing_type=="5")
        {
           // Doctor 
          
                   
        $query1 = $this->db->query("SELECT * FROM vendor_offers WHERE name='$coupon' and (vendor_id='$listing_id' || vendor_id='0') and end_date >= '$current_date' and min_amount <= '$amount' ");
        $row_fitness1 = $query1->row_array();
        $count1 = $query1->num_rows();  
          if($count1 > 0)
               {
            $id=$row_fitness1['id'];
            $query = $this->db->query("SELECT  v.id, v.vendor_id,v.listing_id FROM vendor_offers as v LEFT JOIN use_coupon as us ON us.coupon=v.id  WHERE us.user_id='$user_id' AND us.coupon='$id' AND us.use_status ='1'");
            $row_fitness = $query->row_array();
            $count = $query->num_rows();
            if ($count > 0)
            {
                $resp = array(
                                'status' => 201,
                                'message' => 'Coupon Already Used'
                            );
            }
            else
            {
             
                  
                  $vendor_id=$row_fitness1['vendor_id'];
                  $vendor_type=$row_fitness1['listing_id'];
                  $save_type=$row_fitness1['save_type'];
                  $price=$row_fitness1['price'];
                  $max=$row_fitness1['max_discound'];
                  $subamount=0;
                  $discount=0;
                  if($price > 0 && $amount > 0 )
                  {
                      if($save_type=="rupee")
                      {
                          $subamount=$amount-$price;
                          $discount=$price;
                      }
                      else
                      {
                          $discount=$amount*$price/100;
                          $subamount=$amount-$discount;
                          
                      }
                      if($subamount > $max)
                      {
                          $subamount=$max;
                      }
                      elseif($subamount < 0)
                      {
                          $subamount=0;
                      }
                  }
                   //coupon
                     if($row_fitness['use_status'] == 0)
                  {
                      
                  }
                  else
                  {
                    $coupon_array = array(
                          'user_id' => $user_id,
                          'coupon'  => $id,
                          'vendor_id' => $vendor_id,
                          'vendor_type' => $vendor_type,
                          'created_at'  => date('Y-m-d H:i:s'),
                          'use_status' => 0
                          );
                    $this->db->insert('use_coupon', $coupon_array);
                  }
                    
                    
                    $resp = array(
                                'status' => 200,
                                'message' => 'Success',
                                'totalamount'=>$amount,
                                'subtotal'=>$subamount,
                                'discount'=>$discount,
                                'coupon_id'=>$id
                            );
                
                   
             
                
            }
          }
          else
               {
                      $resp = array(
                                'status' => 201,
                                'message' => 'Invalid Coupon Code'
                            );   
                }
              
        }        
        if($listing_type=="6")
        {
            
        // Fitness
       
        $query1 = $this->db->query("SELECT * FROM vendor_offers WHERE name='$coupon' and vendor_id='$listing_id'");
        $row_fitness1 = $query1->row_array();
        $count1 = $query1->num_rows();  
          if($count1 > 0)
               {
              $id=$row_fitness1['id'];
            $query = $this->db->query("SELECT  v.id, v.vendor_id,v.listing_id FROM vendor_offers as v LEFT JOIN use_coupon as us ON us.coupon=v.id  WHERE us.user_id='$user_id' AND us.coupon='$id'");
            $row_fitness = $query->row_array();
            $count = $query->num_rows();
            if ($count > 0)
            {
                 $resp = array(
                                'status' => 201,
                                'message' => 'Coupon Already Used'
                            );
            }
            else
            {
             
                  
                  $vendor_id=$row_fitness1['vendor_id'];
                  $vendor_type=$row_fitness1['listing_id'];
                
                  $query1_1 = $this->db->query("SELECT id FROM diet_leads WHERE user_id='$user_id'");
                  $row_fitness1_1 = $query1_1->row_array();
                  $count1_1 = $query1_1->num_rows();  
                  if($count1_1 > 0)
                  {
                        $lead_id=$row_fitness1_1['id'];
                         $query1_1_1 = $this->db->query("SELECT user_id,complete_status FROM diet_user_package_history WHERE user_id='$user_id' order by id desc LIMIT 1");
                          $row_fitness1_1_1 = $query1_1_1->row_array();
                          $count1_1_1 = $query1_1_1->num_rows();  
                          if($count1_1_1 > 0)
                          {
                              
                              if($row_fitness1_1_1['complete_status']==1)
                              {
                                  //coupon
                                    $coupon_array = array(
                                          'user_id' => $user_id,
                                          'coupon'  => $id,
                                          'vendor_id' => $vendor_id,
                                          'vendor_type' => $vendor_type,
                                          'created_at'  => date('Y-m-d H:i:s'),
                                          'use_status' => 1
                                          );
                                    $this->db->insert('use_coupon', $coupon_array);
                                    
                                 //missbelly package   
                                    $month = "";
                                    $this->db->select('month');
                                    $this->db->from('diet_master_package');
                                    $this->db->where('id', 1);
                                    $query1 = $this->db->get();
                                    $month = $query1->row()->month;
                    
                                    $month1 = "+$month";
                                    $effectiveDate1 = date('Y-m-d');
                                    $effectiveDate = date('Y-m-d', strtotime("$month1 months", strtotime($effectiveDate1)));
                    
                    
                                    $new_booking_lead = array(
                                        'user_id' => $user_id,
                                        'package_id' => '1',
                                        'leads_id' => $lead_id,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'booking_from_date' => date('Y-m-d'),
                                        'booking_to_date' => $effectiveDate,
                                        'created_by' => $user_id,
                                        'status' => '1'
                                    );
                    
                                    $this->db->insert('diet_packages_booked', $new_booking_lead);
                    
                                    $notification = array('user_id' => $user_id,
                                        'listing_id' => "",
                                        'package_id' => 1,
                                        'booking_id' => $booking_id,
                                        'order_id' => 0,
                                        'title' => "Diet Package Booking",
                                        'msg' => "Diet Package Booked Through Ecard.",
                                        'notification_type' => "Diet Booking",
                                        'created_at' => date('Y-m-d H:i:s')
                                    );
                                    $this->db->insert('diet_plan_notifications', $notification);
                    
                    
                                    $booking_history = array('user_id' => $user_id,
                                        'leads_id' => $lead_id,
                                        'package_id' => 1,
                                        'booking_id' => $booking_id,
                                        'booking_from' => '0000-00-00',
                                        'booking_to' => '0000-00-00',
                                        'created_at' => date('Y-m-d H:i:s')
                                    );
                    
                                    $this->db->insert('diet_user_package_history', $booking_history);
                                    $diet_booking_id = $this->db->insert_id();
                                    
                                    $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                                    $customer_token_count = $customer_token->num_rows();
                    
                                    if ($customer_token_count > 0) {
                                        $token_status = $customer_token->row_array();
                                        $agent = $token_status['agent'];
                                        $reg_id = $token_status['token'];
                                        $tag = 'text';
                                 
                                        $img_url = 'https://medicalwale.com/img/noti_icon_miss_belly.png';
                                        $title_package = "Free Diet Package";
                                        $msg_package = "Congratulations, You get " . $month . " month free package of Missbelly Diet Plan on Bachat Health Card.";
                                        $this->send_gcm_notify_dietpackage1($title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                                        
                                        $this->insert_all_notification_Mobile1($user_id,$title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                                        
                                    }
                                     $resp = array(
                                'status' => 200,
                                'message' => 'Success'
                            );   
                              }
                              else  if($row_fitness1_1_1['complete_status']==0)
                              {
                                   $resp = array(
                                        'status' => 201,
                                        'message' => 'Your Miss Belly Package is already Activated.'
                                    );
                              }
                          }
                        
                  }
                  else
                  {
                       //coupon
                                    $coupon_array = array(
                                          'user_id' => $user_id,
                                          'coupon'  => $id,
                                          'vendor_id' => $vendor_id,
                                          'vendor_type' => $vendor_type,
                                          'created_at'  => date('Y-m-d H:i:s'),
                                          'use_status' => 1
                                          );
                                    $this->db->insert('use_coupon', $coupon_array);
                                    
                                 //missbelly package   
                     $new_deit_entry = array(
                        'user_id' => $user_id,
                        'enroll_from' => "Coupon Code",
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $user_id
                    );
    
                    $this->db->insert('diet_leads', $new_deit_entry);
                    $lead_id = $this->db->insert_id();
                    
                    $month = "";
                    $this->db->select('month');
                    $this->db->from('diet_master_package');
                    $this->db->where('id', 1);
                    $query1 = $this->db->get();
                    $month = $query1->row()->month;
    
                    $month1 = "+$month";
                    $effectiveDate1 = date('Y-m-d');
                    $effectiveDate = date('Y-m-d', strtotime("$month1 months", strtotime($effectiveDate1)));
    
    
                    $new_booking_lead = array(
                        'user_id' => $user_id,
                        'package_id' => '1',
                        'leads_id' => $lead_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'booking_from_date' => date('Y-m-d'),
                        'booking_to_date' => $effectiveDate,
                        'created_by' => $user_id,
                        'status' => '1'
                    );
    
                    $this->db->insert('diet_packages_booked', $new_booking_lead);
    
                    $notification = array('user_id' => $user_id,
                        'listing_id' => "",
                        'package_id' => 1,
                        'booking_id' => $booking_id,
                        'order_id' => 0,
                        'title' => "Diet Package Booking",
                        'msg' => "Diet Package Booked Through Ecard.",
                        'notification_type' => "Diet Booking",
                        'created_at' => date('Y-m-d H:i:s')
                    );
                    $this->db->insert('diet_plan_notifications', $notification);
    
    
                    $booking_history = array('user_id' => $user_id,
                        'leads_id' => $lead_id,
                        'package_id' => 1,
                        'booking_id' => $booking_id,
                        'booking_from' => '0000-00-00',
                        'booking_to' => '0000-00-00',
                        'created_at' => date('Y-m-d H:i:s')
                    );
    
                    $this->db->insert('diet_user_package_history', $booking_history);
                    $diet_booking_id = $this->db->insert_id();
                    
                    $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
                    $customer_token_count = $customer_token->num_rows();
    
                    if ($customer_token_count > 0) {
                        $token_status = $customer_token->row_array();
                        $agent = $token_status['agent'];
                        $reg_id = $token_status['token'];
                        $tag = 'text';
                 
                        $img_url = 'https://medicalwale.com/img/noti_icon_miss_belly.png';
                        $title_package = "Free Diet Package";
                        $msg_package = "Congratulations, You get " . $month . " month free package of Missbelly Diet Plan on Bachat Health Card.";
                        $this->send_gcm_notify_dietpackage1($title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                        
                        $this->insert_all_notification_Mobile1($user_id,$title_package, $reg_id, $msg_package, $img_url, $tag, $agent, $diet_booking_id);
                        
                    }
                     $resp = array(
                                'status' => 200,
                                'message' => 'Success'
                            );   
                  }
                   
             
                
            }
          }
          else
               {
                      $resp = array(
                                'status' => 201,
                                'message' => 'Invalid Coupon Code'
                            );   
                }
        }        
            
                
    
       return $resp;
  }
 public function insert_all_notification_Mobile1($user_id,$title, $reg_id, $msg, $img_url, $tag, $agent, $diet_booking_id)
 {
$notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => "",
                      'booking_id'  => $diet_booking_id,
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'free_diet_plan',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
         return 1;
         
}

    public function send_gcm_notify_dietpackage1($title, $reg_id, $msg, $img_url, $tag, $agent, $diet_booking_id)
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
                "notification_type" => 'free_diet_plan',
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
             $msg = $doctor_name . '  has Reschedule an appointment for'.$consultation_type;
            $this->notifyMethod_1($user_id, $appointment_status, $booking_id, $doctor_name,$title,$msg);
       }
      
   }
      public function notifyMethod_1($user_id, $appointment_status, $booking_id, $doctor_name,$title,$msg)
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
                $this->send_gcm_notify_user_up($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_id);
                
            }
    }
       public function add_re_bookings($patient_id, $doctor_id, $clinic_id, $booking_date, $booking_time, $from_time, $to_time, $connect_type,$status,$appointment_id,$description,$listing_type)
     {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id           = $appointment_id;
        
        $sql21             = "SELECT  id from hospital_booking_master WHERE booking_id='$booking_id'";
            $query_practices1  = $this->db->query($sql21);
            $total_practices1  = $query_practices1->num_rows();
            if($total_practices1 > 0)
            {
        
        $date                 = date('Y-m-d H:i:s');
       // $status               = '1'; //awaiting for confirmation  // pending
        $from_time = date("H:i:s", strtotime($from_time));
        $to_time =  date("H:i:s", strtotime($to_time));
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $patient_id,
            
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
        
        
        $booking_master_array_d = array(
            'booking_id' => $booking_id,
            'user_id' => $patient_id,
            
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
        $insert = $this->db->update('hospital_booking_master', $booking_master_array_d);
        
     
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
        //$this->insert_notification_post_requestion($patient_id, $appointment_id,$doctor_id);
        //$this->notifyMethod1($doctor_id, $patient_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $doctor_name);
            }
            else
            {
              
              if($listing_type=="50")
              {
                  $date                 = date('Y-m-d H:i:s');
                  $from_time = date("H:i:s", strtotime($from_time));
                  $to_time =  date("H:i:s", strtotime($to_time));
                  $booking_master_array = array(
                        'booking_id' => $booking_id,
                        'user_id' => $patient_id,
                        'clinic_id'=>$clinic_id,
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
                    
                    $sql0 = sprintf("SELECT hospital_doctor_id FROM doctor_list WHERE user_id='$doctor_id'");
                    $query_practices0  = $this->db->query($sql0);
                    $total_practices0  = $query_practices0->num_rows();
                    $get_pract0=$query_practices0->row_array();
                    $ids=$get_pract0['hospital_doctor_id'];  
                    if(!empty($ids))
                       {
                      $sql21             = "SELECT hd.*,hd.id as doctor_id,h.name_of_hospital from hospital_doctor_list as hd LEFT JOIN hospitals as h on (h.user_id = hd.hospital_id ) WHERE hd.id IN ($ids) and h.user_id ='$clinic_id' ";
                       //$query_dr           = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name FROM hospitals INNER JOIN doctor_list ON doctor_list.=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$listing_id'");
                        $query_practices0  = $this->db->query($sql21);
                         $count_user         = $query_practices0->num_rows();
                         if($count_user > 0)
                         {
                            
                    $get_pract01=$query_practices0->row_array();
                            $id_doctor=$get_pract01['doctor_id'];
                    
                    $booking_master_array1 = array(
                        'booking_id' => $booking_id,
                        'user_id' => $patient_id,
                        'doctor_id' => $id_doctor,
                        'listing_id' => $clinic_id,
                        'booking_date' => $booking_date,
                        'booking_time' => $booking_time,
                        'from_time' => $from_time,
                        'to_time' => $to_time,
                        'status' => $status,
                        'description' => $description,
                        'created_date' => $date,
                        'patient_id' => $patient_id,
                        'consultation_type' => $connect_type,
                        'vendor_type'=>50
                    );
                    $insert1 = $this->db->insert('hospital_booking_master', $booking_master_array1);
                    
                         }
           }
                    
                    
                    
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
              }
              else
              {
                  $date                 = date('Y-m-d H:i:s');
                   // $status               = '1'; //awaiting for confirmation  // pending
                    $from_time = date("H:i:s", strtotime($from_time));
                    $to_time =  date("H:i:s", strtotime($to_time));
                    $booking_master_array = array(
                        'booking_id' => $booking_id,
                        'user_id' => $patient_id,
                        
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
              }
              
                
            }
        return array(
            'booking_id' => $id_doctor
        );
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
        $customer_token       = $this->db->query("SELECT name,token,agent,token_status,web_token FROM users WHERE id='$listing_id'");
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
            
            $this->send_gcm_notify($listing_id, $user_id, $title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_time, $appointment_id, $connect_type);
            
            // Web notification FCM function by ghanshyam parihar date:16 Jan 2019
            $click_action = 'https://doctor.medicalwale.com/booking_controller/booking_appointment/'.$listing_id;
            // $click_action = 'https://vendor.sandbox.medicalwale.com/doctor/booking_controller/booking_appointment/'.$listing_id;
            $this->send_gcm_web_notify($title, $msg, $web_token, $img_url, $tag, $agent, $type,$click_action);
            // Web notification FCM function by ghanshyam parihar date:16 Jan 2019 Ends
            
            
            
        }
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
 public function doctor_random_cat($user_id,$mlat,$mlng,$type)
    {
         $radius = '5';
         $ty="";
        if($type=="all")
        {
            $ty .="LIMIT 5";
        }
        elseif($type=="recommended")
        {
             $ty .="LIMIT 3";
        }
        
        date_default_timezone_set('Asia/Kolkata');
                    $current_date = date('Y-m-d H:i:s');
        // genral Doctor Start Here
        $sql   =  sprintf("SELECT doctor_list.*,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id  WHERE FIND_IN_SET('41', doctor_list.category) and  doctor_list.is_active = 1 ORDER BY RAND () $ty  ", ($mlat), ($mlng), ($mlat), ($radius));
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
                $category            = explode(",",$row['category']);
                $query_sp       = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  id='41' ");
                $total_category = $query_sp->num_rows();
                $get_sp=$query_sp->row_array();
                $cat_name               = $get_sp['area_expertise'];
               
                if(in_array("41", $category))
                {
                    $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $genral[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>"41",
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
                );
                }
            }
        } else {
            $genral = array();
        }
         // genral Doctor End Here
        
        
        
         // Gynecologist Doctor Start Here
        $Where = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(46|47),"';
        
        $sql   =  sprintf("SELECT doctor_list.*,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id  WHERE $Where and  doctor_list.is_active = 1 ORDER BY RAND () $ty  ", ($mlat), ($mlng), ($mlat), ($radius));
        
       
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $gy[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $gy[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
                );
                
               }
              
               
            }
        } else {
            $gy = array();
        }
        
        // Gynecologist Doctor End  Here
        
        // Dermatologist Start Here
        $sql   =  sprintf("SELECT doctor_list.*,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id  WHERE FIND_IN_SET('27', doctor_list.category) and  doctor_list.is_active = 1 ORDER BY RAND () $ty  ", ($mlat), ($mlng), ($mlat), ($radius));
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
                $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $dermatologist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>"27",
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
                );
            }
        } else {
            $dermatologist = array();
        }
        //Dermatologist End Here
        
        
         //Ayurvedic End Here
         $Where1 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(9|10|11|12|13|14|15|40),"';
         $doctor_data=array();
        $sql   =  sprintf("SELECT doctor_list.*,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id  WHERE $Where1 and  doctor_list.is_active = 1 ORDER BY RAND () $ty  ", ($mlat), ($mlng), ($mlat), ($radius));
        
       
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $ayur[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $ayur[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $ayur[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $ayur[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
               $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $ayur[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
               $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $ayur[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
               $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $ayur[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
               $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $ayur[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
         $sql   =  sprintf("SELECT doctor_list.*,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id  WHERE FIND_IN_SET('31', doctor_list.category) and  doctor_list.is_active = 1 ORDER BY RAND () $ty  ", ($mlat), ($mlng), ($mlat), ($radius));
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
                 $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $ent[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>"31",
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
                );
            }
        } else {
            $ent = array();
        }
        //ENT End Here
        
        //Dentist Start Here
         $Where2 = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(23|26|239|240),"';
        $doctor_data_1=array();
        $sql   =  sprintf("SELECT doctor_list.*,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id  WHERE $Where2 and  doctor_list.is_active = 1 ORDER BY RAND () $ty  ", ($mlat), ($mlng), ($mlat), ($radius));
        
       
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $dentist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $dentist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
               $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $dentist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
               $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $dentist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
        $sql   =  sprintf("SELECT doctor_list.*,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id  WHERE $Where2 and  doctor_list.is_active = 1 ORDER BY RAND () $ty  ", ($mlat), ($mlng), ($mlat), ($radius));
        
       
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
               $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $cardiologist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $cardiologist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
               $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $cardiologist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
        $sql   =  sprintf("SELECT doctor_list.*,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id  WHERE $Where2 and  doctor_list.is_active = 1 ORDER BY RAND () $ty  ", ($mlat), ($mlng), ($mlat), ($radius));
        
       
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $obstetrician[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $obstetrician[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
        $sql   =  sprintf("SELECT doctor_list.*,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id  WHERE $Where2 and  doctor_list.is_active = 1 ORDER BY RAND () $ty  ", ($mlat), ($mlng), ($mlat), ($radius));
        
       
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $neurologist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
               $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $neurologist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $neurologist[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
        $sql   =  sprintf("SELECT doctor_list.*,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id  WHERE $Where2 and  doctor_list.is_active = 1 ORDER BY RAND () $ty  ", ($mlat), ($mlng), ($mlat), ($radius));
        
       
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $homeopathic[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
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
              $sqldi   =  "SELECT * from doctor_online  WHERE doctor_id='$doctor_user_id' and doctor_online.starttime <= '$current_date'
                    AND doctor_online.endtime >= '$current_date' ";
        $querydi = $this->db->query($sqldi);
        $countdi = $querydi->num_rows();
                $homeopathic[] = array(
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'experience' => $experience,
                    'image'=>$doctor_image,
                    'category_id'=>$final_cat_id,
                    'category_name'=>$cat_name,
                    'online_status'=>$countdi
                );
                
               }
             
             
                }
                $doctor_data_4[]=$doctor_user_id;
             
            
            }
        } else {
            $obstetrician = array();
        }
         //Homeopathic End Here
         
         $resultpost[] = $genral;
         $resultpost[] = $gy; 
         $resultpost[] = $dermatologist;
         $resultpost[] = $ayur;
         $resultpost[] = $ent;
         $resultpost[] = $dentist;
         $resultpost[] = $cardiologist;
         $resultpost[] = $obstetrician;
         $resultpost[] = $neurologist;
         $resultpost[] = $homeopathic;
         
         
        return $resultpost;
    }
    
    
    public function doctor_problem($user_id)
    {
        // genral Doctor Start Here
        $sql   =  sprintf("SELECT * from doctor_problem where status='0'");
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) 
            {
                $id         = $row['id']; 
                $cat_name         = $row['cat_name'];
                $cat_type         = $row['cat_type'];

                 $image          = $row['image'];
                 if(!empty($image))
                 {
            $image          = 'https://d2c8oti4is0ms3.cloudfront.net/images/Doctor_problem/'.$image;
                 }
               
                $resultpost[] = array(
                    'id' => $id,
                    'cat_name' => $cat_name,
                    'cat_type' => $cat_type,
                    'image'=>$image
                   
                );
                
            }
        } else {
            $resultpost = array();
        }
         // genral Doctor End Here
      
         
         
        return $resultpost;
    }

}