<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Check_in_model extends CI_Model
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
   public function check_time_format($time) {
        $time_filter = preg_replace('/\s+/', '', $time);
        $final_time = date("h:i A", strtotime($time_filter));
        return $final_time;
    } 
  
    public function all_vendor_list($user_id,$mlat, $mlng,$page,$vendor_id,$keyword)
    {
          $radius = $page*500;
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
       $resultpost_branch=array();
        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            return $dist;
        }

        if($vendor_id == '13') 
        {
            
          if($keyword!="")
          {
               $sql   = sprintf("SELECT mba,recommended,certified,discount_description,medicalwale_discount,otc_discount,ethical_discount,surgical_discount,perscribed_discount,generic_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND visible <> '0' AND type <> 'branch' AND medical_name LIKE '%%$keyword%%'   HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
           }
          else
          {
            
            $sql = sprintf("SELECT mba,recommended,certified,discount_description,medicalwale_discount,otc_discount,ethical_discount,surgical_discount,perscribed_discount,generic_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND visible <> '0' AND type <> 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
          }
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            if($count>0){
                foreach ($query->result_array() as $row) {
                     $offer_discount = array();
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $medical_id = $row['user_id'];
                    $medical_name = $row['medical_name'];
                    $store_manager = $row['store_manager'];
                    $address1 = $row['address1'];
                    $address2 = $row['address2'];
                    $pincode = $row['pincode'];
                    $city = $row['city'];
                    $state = $row['state'];
                    $contact_no = $row['contact_no'];
                    $whatsapp_no = $row['whatsapp_no'];
                    $email = $row['email'];
                    $store_since = $row['store_since'];
                    $chat_id = $row['user_id'];
                     $is_24hrs_available = $row['is_24hrs_available'];
                if ($is_24hrs_available == 'Yes') {
                    $store_open = date("h:i A", strtotime("12:00 AM"));
                    $store_close = date("h:i A", strtotime("11:59 PM"));
                } else {
                    $store_open = $this->check_time_format($row['store_open']);
                    $store_close = $this->check_time_format($row['store_close']);
                }
                     $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                     $profile_pic = $row['profile_pic'];
                    if ($row['profile_pic'] != '') {
                        $profile_pic = $row['profile_pic'];
                        $profile_pic = str_replace(' ', '%20', $profile_pic);
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                    } else {
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                    }
                 
                       $distance_root =  round(($distances/1000),1);
                        if (strpos($distances, 'km') !== false) 
                    {
                        $rp = str_replace(" km","","$distances");
                         $new=$rp * 1000;
                    }
                    else
                    {
                        $rp = str_replace(" m","","$distances");
                $new=$rp;
                    }
                    $fav_pharmacy = $this->db->select('id')->from('pharmacy_favourite')->where('user_id', $user_id)->where('listing_id', $medical_id)->get()->num_rows();
                     $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM medical_stores_review WHERE medical_stores_id='$medical_id'");
                $row_pharma = $query_pharmacy->row_array();
                
               $rating = $row_pharma['avg_rating'];
               
                if ($rating === NULL) {
                    $rating = '0';
                }
               
                     
                      $FINAL_RESULT = array();
                        
                    $resultpost0[] = array(
                        'id' => $medical_id,
                        'name' => $medical_name,
                        'listing_id' => $medical_id,
                        'listing_type' => '13',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address1.','.$address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $contact_no,
                        'email' => $email,
                        'profile_pic' => $profile_pic,
                        'distance' => (string)$distance_root,
                        'favourite' => (string)$fav_pharmacy,
                        'rating' => (string) $rating,
                        'store_open' => $store_open,
                        'store_close' => $store_close,
                        
                    );
                }
                 
                //added for generico pharmacy branch 
                
                $radius_branch = '5';
                $sql_branch = sprintf("SELECT mba,recommended,certified,user_id,discount_description,medicalwale_discount,`id`, surgical_discount,perscribed_discount,`pharmacy_branch_user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance,otc_discount,ethical_discount,generic_discount  FROM medical_stores  WHERE is_active= '1' AND visible = '1' AND type = 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius_branch));
           
            $query_branch = $this->db->query($sql_branch);
            $count_branch = $query_branch->num_rows();
            if($count_branch>0){
               foreach ($query_branch->result_array() as $row) {
                   $offer_discount = array();
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $medical_id = $row['pharmacy_branch_user_id'];
                    $medical_user_id = $row['user_id'];
                    $medical_name = $row['medical_name'];
                    $store_manager = $row['store_manager'];
                    $address1 = $row['address1'];
                    $address2 = $row['address2'];
                    $pincode = $row['pincode'];
                    $city = $row['city'];
                    $state = $row['state'];
                    $contact_no = $row['contact_no'];
                    $whatsapp_no = $row['whatsapp_no'];
                    $email = $row['email'];
            
                         $is_24hrs_available = $row['is_24hrs_available'];
                if ($is_24hrs_available == 'Yes') {
                    $store_open = date("h:i A", strtotime("12:00 AM"));
                    $store_close = date("h:i A", strtotime("11:59 PM"));
                } else {
                    $store_open = $this->check_time_format($row['store_open']);
                    $store_close = $this->check_time_format($row['store_close']);
                }
                    $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
             
                    $distance_root =  round(($distances/1000),1);
                     $profile_pic = $row['profile_pic'];
                    if ($row['profile_pic'] != '') {
                        $profile_pic = $row['profile_pic'];
                        $profile_pic = str_replace(' ', '%20', $profile_pic);
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                    } else {
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                    }
                      if (strpos($distances, 'km') !== false) 
                    {
                        $rp = str_replace(" km","","$distances");
                         $new=$rp * 1000;
                    }
                    else
                    {
                        $rp = str_replace(" m","","$distances");
                $new=$rp;
                    }
                     $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM medical_stores_review WHERE medical_stores_id='$medical_id'");
                $row_pharma = $query_pharmacy->row_array();
                
               $rating = $row_pharma['avg_rating'];
               
                if ($rating === NULL) {
                    $rating = '0';
                }
                    $fav_pharmacy = $this->db->select('id')->from('pharmacy_favourite')->where('user_id', $user_id)->where('listing_id', $medical_id)->get()->num_rows();
                    $resultpost_branch[] = array(
                        'id' => $medical_id,
                        'name' => $medical_name,
                        'listing_id' => $medical_id,
                        'listing_type' => '13',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address1.','.$address2,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $contact_no,
                        'email' => $email,
                        
                        'profile_pic' => $profile_pic,
                        'distance' => (string)$distance_root,
                        'favourite' => (string)$fav_pharmacy,
                        'rating' => (string) $rating,
                        'store_open' => $store_open,
                        'store_close' => $store_close,
                       
                    );
                    
                   
                   // array_push($resultpost,$resultpost_branch);
                } 
            }
           
                    usort($resultpost0, function($a, $b) {
                                    $a = $a['distance'];
                                    $b = $b['distance'];
                        if ($a == $b) { return 0; }
                            return ($a < $b) ? -1 : 1;
                        });
                        
                        
           if($resultpost_branch!="")
          {
              //echo 'no branch';
             return $resultpost0  =  array_merge($resultpost_branch,$resultpost0);
          }
          else
          {
           return $resultpost0  =  $resultpost0;  
          }
                   
              
            }
            else{
            return  $resultpost0= array();
                
            }
        }
     
      
        else if($vendor_id=='6')
        {
             if($keyword!="")
          {
              
             $sql =   sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time, ( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1' AND fcb.branch_name LIKE '%%$keyword%%' HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
           }
          else
          {
             $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time, ( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
          }
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $branch_id = $row['id'];
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $branch_name = $row['branch_name'];
                    $branch_image = $row['branch_image'];
                    $branch_phone = $row['branch_phone'];
                    $branch_email = $row['branch_email'];
                    $about_branch = $row['about_branch'];
                    $branch_business_category = $row['branch_business_category'];
                    $branch_address = $row['branch_address'];
                    $pincode = $row['pincode'];
                    $opening_hours = $row['opening_hours'];
                    $state = $row['state'];
                    $city = $row['city'];
                    $listing_id = $row['user_id'];
                    $listing_type = '6';
    
                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
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

                                    $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                    $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                    $current_time = date('h:i A');

                                    $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                    $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                    $date3 = DateTime::createFromFormat('H:i a', $system_end_time);

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

$dfinal=date("l");
$current_day_final="";
foreach($final_Day as $key => $product)
{

if($product['day']===$dfinal)
{
 $current_day_final = $product['time'][0];
}
}
    
                    $distances = str_replace(',', '.', GetDrivingDistance($lat, $mlat, $lng, $mlng));
                    $distance_root =  round(($distances/1000),1);
                         if (strpos($distances, 'km') !== false) 
                    {
                        $rp = str_replace(" km","","$distances");
                         $new=$rp * 1000;
                    }
                    else
                    {
                        $rp = str_replace(" m","","$distances");
                $new=$rp;
                    }
                    $enquiry_number = "9619146163";
                    $open_store=explode("-",$current_day_final);
                    $new_open_store=$open_store[0];
                    $new_close_store=$open_store[1];
                    $resultpost1[] = array(
                       
                        'id' => $branch_id,
                        'name' => $branch_name,
                        'listing_id' => $listing_id,
                        'listing_type' => $listing_type,
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $branch_address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $branch_phone,
                        'email' => $branch_email,
                        'profile_pic' => $branch_image,
                        'distance' => (string)$distance_root,
                        'favourite' => (string)0,
                        'rating' => (string)4.5,
                        'store_open' => $new_open_store,
                        'store_close' =>$new_close_store,
                   
                    );
                }
                  function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
                $sort_col = array();
                foreach ($arr as $key => $row) {
                    $sort_col[$key] = $row[$col];
                }
                array_multisort($sort_col, $dir, $arr);
            }
    
            array_sort_by_column($resultpost1, 'distance');
            return $resultpost1;
            
            } else {
               return $resultpost1 = array();
            }
          
          
        }
      
     
      
      else if($vendor_id=='5')
      {
           if($keyword!="")
           {
               $sql   = sprintf("SELECT doctor_list.mba,doctor_list.recommended,doctor_list.certified,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng,doctor_clinic.id as clinic_id,  ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.doctor_name LIKE '%%$keyword%%'  and  doctor_list.is_active = 1  HAVING distance < '%s' ORDER BY distance  LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
           }
          else
          {
           $sql   = sprintf("SELECT doctor_list.mba,doctor_list.recommended,doctor_list.certified,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng,doctor_clinic.id as clinic_id, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.is_active = 1  HAVING distance < '%s' ORDER BY distance  LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
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
                    $clinic_id             = $row['clinic_id'];
                    if ($row['image'] != '') {
                        $profile_pic = $row['image'];
                        $profile_pic = str_replace(' ', '%20', $profile_pic);
                        $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                    } else {
                        $profile_pic = 'https://medicalwale.com/img/doctor_default.png';
                    }
                 
                    
                    $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                     $distance_root =  round(($distances/1000),1);
                         if (strpos($distances, 'km') !== false) 
                    {
                        $rp = str_replace(" km","","$distances");
                         $new=$rp * 1000;
                    }
                    else
                    {
                        $rp = str_replace(" m","","$distances");
                $new=$rp;
                    }
                    $resultpost2[] = array(
                    
                        'id' => $doctor_user_id,
                        'name' => $doctor_name,
                        'listing_id' => $doctor_user_id,
                        'listing_type' => "5",
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $doctor_phone,
                        'email' => $email,
                        'profile_pic' => $profile_pic,
                        'distance' => (string)$distance_root,
                        'favourite' => (string)0,
                        'rating' => (string) 1,
                        'store_open' => "12:00 AM",
                        'store_close' =>"12:00 PM",
                       
                    );
                }
                function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
                $sort_col = array();
                foreach ($arr as $key => $row) {
                    $sort_col[$key] = $row[$col];
                }
                array_multisort($sort_col, $dir, $arr);
            }
            array_sort_by_column($resultpost2, 'distance');
            return $resultpost2 ;
            
            } else {
               return $resultpost2 = array();
            }
        
      }
       
     
       
      else if($vendor_id == '10')
       {
            $resultpost_branch=array();
            if($keyword!="")
              {
             
               $sql    = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE `is_active` = 1 and lab_name LIKE '%%$keyword%%'   HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
               
           }
            else
              {
                $sql    = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE `is_active` = 1  HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
               }
                $query  = $this->db->query($sql);
                $count  = $query->num_rows();
            
              if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $id                = $row['id'];
                    $labcenter_user_id = $row['user_id'];
                    $lab_name          = $row['lab_name'];
                    $features          = $row['features'];
                    $home_delivery     = $row['home_delivery'];
                    $delivery_charges  = $row['delivery_charges'];
                    $address1          = $row['address1'];
                    $address2          = $row['address2'];
                    $pincode           = $row['pincode'];
                    $city              = $row['city'];
                    $state             = $row['state'];
                    $contact_no        = $row['contact_no'];
                    $whatsapp_no       = $row['whatsapp_no'];
                    $email             = $row['email'];
                    $lat               = $row['latitude'];
                    $lng               = $row['longitude'];
                    $listing_type      = '10';
                    //$rating            = '4.0';
                  
                    $image             = $row['profile_pic'];
                   // $image             = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;
                   $image1             = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                  
                    $resultpost4[] = array(
                     
                             'id' => $id,
                        'name' => $lab_name,
                        'listing_id' => $labcenter_user_id,
                        'listing_type' => '10',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address1,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $contact_no,
                        'email' => $email,
                        'profile_pic' => $image1,
                        'distance' => (string)0,
                        'favourite' => (string)0,
                        'rating' => (string) 1,
                        'store_open' => "12:00 AM",
                        'store_close' =>"12:00 PM",
                    
                    );
                }
                return $resultpost4;
            } else {
                return $resultpost4 = array();
            }
       }
      
        
      else if($vendor_id == '8')
       {
            
         if($keyword!="")
              {
                $sql = sprintf("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat) ) * cos( radians( lng) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat) ) ) ) AS distance FROM hospitals WHERE is_approval='1' AND is_active='1' and name_of_hospital LIKE '%%$keyword%%' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
              }
            else
              {  
                 $sql = sprintf("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat) ) * cos( radians( lng) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat) ) ) ) AS distance FROM hospitals WHERE is_approval='1' AND is_active='1' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
              }
        $query = $this->db->query($sql);
        $count = $query->num_rows();
         if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $id = $row['id'];
                    $name_of_hospital = $row['name_of_hospital'];
                    $mobile = $row['phone'];
                    $about_us = $row['about_us'];
                    $establishment_year = $row['establishment_year'];
                    $category = $row['category'];
                    $address = $row['address'];
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $pincode = $row['pincode'];
                    $city = $row['city'];
                    $state = $row['state'];
                    $email = $row['email'];
                    $image = $row['image'];
                    $rating = $row['rating'];
                    $reviews = $row['review'];
                    $user_discount = $row['user_discount'];
                    $hospital_user_id = $row['user_id'];
                    $profile_views = '0';
    
                    if ($image != '') {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                    } else {
                        $image = '';
                    }
    
    $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
     $distance_root =  round(($distances/1000),1);
                    //end 
                    $resultpost5[] = array(
                       
                        'id' => $id,
                        'name' => $name_of_hospital,
                        'listing_id' => $hospital_user_id,
                        'listing_type' => "8",
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $mobile,
                        'email' => $email,
                        'profile_pic' => $image,
                        'distance' => (string)$distance_root,
                        'favourite' => (string)0,
                        'rating' => (string) 1,
                        'store_open' => "12:00 AM",
                        'store_close' =>"12:00 PM",
                        
                    );
             }
             return $resultpost5;
            } else {
               return $resultpost5 = array();
            }
       }
      
      else   if($vendor_id == '12')
           {
                if($keyword!="")
              {
                
                $sql = sprintf("SELECT nursing_attendant.*,IFNULL(rating,'') AS rating, IFNULL(review,'') AS review, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM nursing_attendant  where is_active = '1' and nursing_attendant.name LIKE '%%$keyword%%' HAVING distance < '%s' or all_india='1' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
              }
            else
              {  
                 $sql = sprintf("SELECT nursing_attendant.*,IFNULL(rating,'') AS rating, IFNULL(review,'') AS review, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM nursing_attendant  where is_active = '1' HAVING distance < '%s' or all_india='1' ORDER BY distance", ($mlat), ($mlng), ($mlat), ($radius));
              }
                   $query = $this->db->query($sql);
                $count = $query->num_rows();
            
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                         $id = $row['id'];
                        $name = $row['name'];
                        
                        $about_us = $row['about_us'];
                        $establishment_year = $row['establishment_year'];
                        $certificates = $row['certificates'];
                        $address = $row['address'];
                        $lat = $row['lat'];
                        $lng = $row['lng'];
                        $pincode = $row['pincode'];
                        $mobile = $row['contact'];
                        $city = $row['city'];
                        $state = $row['state'];
                        $email = $row['email'];
                        $image = $row['image'];
                        $rating = $row['rating'];
                        //$reviews                = $row['review'];        
                         $nursingattendant_user_id = $row['user_id'];
                        //$profile_views          = '1558';
                    
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $image = '';
                        }
        
                        $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $nursingattendant_user_id)->get()->num_rows();
        
                        if ($img_count > 0) {
                            $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $nursingattendant_user_id)->get()->row();
                            $img_file = $profile_query->source;
                            $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                        } else {
                            $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
        
        
                        $resultpost3[] = array(
                      
                             'id' => $id,
                        'name' => $name,
                        'listing_id' => $nursingattendant_user_id,
                        'listing_type' => "12",
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $mobile,
                        'email' => $email,
                        'profile_pic' => $userimage,
                        'distance' => (string)0,
                        'favourite' => (string)0,
                        'rating' => (string) 1,
                        'store_open' => "12:00 AM",
                        'store_close' =>"12:00 PM",
                        );
                    }
                 return $resultpost3;   
                } else {
                    return $resultpost3 = array();
                }
           }
      
       else  if($vendor_id == '36')    
         {
                if($keyword!="")
              {
                
                
                 $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'  and fcb.branch_name LIKE '%%$keyword%%'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
              }
            else
              {  
            $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'   HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
              }
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $branch_id = $row['id'];
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $branch_name = $row['branch_name'];
                    $branch_image = $row['branch_image'];
                    $branch_phone = $row['branch_phone'];
                    $branch_email = $row['branch_email'];
                    $about_branch = $row['about_branch'];
                    $branch_address = $row['branch_address'];
                    $pincode = $row['pincode'];
                    $state = $row['state'];
                    $city = $row['city'];
                    $listing_id = $row['user_id'];
                    $listing_type = '36';
                    $rating = '4.5';
                    $reviews = '0';
                    $profile_views =  '0' ;
                    $user_discount = $row['user_discount'];
    
                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image =  'https://medicalwale.com/img/doctor_default.png';
                    }
    
                    $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                    $distance_root =  round(($distances/1000),1);
                    $enquiry_number = "9619146163";
                    $resultpost7[] = array(
                   
                        'id' => $branch_id,
                        'name' => $branch_name,
                        'listing_id' => $listing_id,
                        'listing_type' => $listing_type,
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $branch_address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $branch_phone,
                        'email' => $branch_email,
                        'profile_pic' => $branch_image,
                        'distance' => (string)$distance_root,
                        'favourite' => (string)0,
                        'rating' => (string) 1,
                        'store_open' => "12:00 AM",
                        'store_close' =>"12:00 PM",
                    );
                }
                
                  return $resultpost7 ;
            } else {
               return $resultpost7 = array();
            }
    
           
       }
       
      else if($vendor_id == '39')    
         {
                if($keyword!="")
              {
               $sql = sprintf("SELECT id,hub_user_id,dentists_branch_user_id, name_of_hospital,about_us, establishment_year, speciality, address, address_2, pincode, phone, city, state, 	email, 	lat, lng, image, rating, review, medicalwale_discount,opening_hours, description,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1' AND hub_user_id='33586' AND name_of_hospital LIKE '%%$keyword%%'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
              }
            else
              {  
        $sql = sprintf("SELECT id,hub_user_id,dentists_branch_user_id, name_of_hospital,about_us, establishment_year, speciality, address, address_2, pincode, phone, city, state, 	email, 	lat, lng, image, rating, review, medicalwale_discount,opening_hours, description,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1' AND hub_user_id='33586'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
              }
           $query = $this->db->query($sql);
           $count = $query->num_rows();
           
           if($count>0)
           {
               foreach($query->result_array() as $row)
               {
                   $branch_id               = $row['id'];
                   $hub_user_id             = $row['hub_user_id'];
                   $dentists_branch_user_id = $row['dentists_branch_user_id'];
                   $name_of_hospital        = $row['name_of_hospital'];
                   $about_us                = $row['about_us'];
                   $establishment_year      = $row['establishment_year'];
                   $speciality              = $row['speciality'];
                   $address                 = $row['address'];
                   $address_2               = $row['address_2'];
                   $pincode                 = $row['pincode'];
                   $phone                   = $row['phone'];
                   $city                    = $row['city'];
                   $state                   = $row['state'];
                   $email                   = $row['email'];
                   $lat                     = $row['lat'];
                   $lng                     =  $row['lng'];
                   $image                   =  $row['image'];
                   
              
                 if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
             
                $resultpost6[] = array(
                     
                      
                        'id' => $branch_id,
                        'name' => $name_of_hospital,
                        'listing_id' => $dentists_branch_user_id,
                        'listing_type' => "39",
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $phone,
                        'email' => $email,
                       
                        'profile_pic' => $image,
                        'distance' => (string)$distance_root,
                        'favourite' => (string)0,
                        'rating' => (string) 1,
                        'store_open' => "12:00 AM",
                        'store_close' =>"12:00 PM",
                       );
               }
               return  $resultpost6 ;
           }
           else
           {
              return  $resultpost6 = array();
           }
         }
         
       else  if($vendor_id == '17')   
       {
               if($keyword!="")
              {
             
               
               $sql = sprintf("SELECT `id`, `email`, `dentists_branch_user_id`, `name_of_hospital`, `establishment_year`, `phone`,   `user_discount`, `about_us`, `opening_hours`, `address`, `image`, `lat`, `lng`, `review`, `rating`, `state`, `city`, `pincode`, `is_active` ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1' AND hub_user_id='33586' AND name LIKE '%%$keyword%%'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
               
               
              }
            else
              {  
             $sql = sprintf("SELECT `id`, `email`, `dentists_branch_user_id`, `name_of_hospital`, `establishment_year`, `phone`,   `user_discount`, `about_us`, `opening_hours`, `address`, `image`, `lat`, `lng`, `review`, `rating`, `state`, `city`, `pincode`, `is_active`,  ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1' AND hub_user_id='33586'   HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
              }
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count>0){
            foreach ($query->result_array() as $row) {
                $lat = $row['lat'];
                $lng = $row['lng'];
                $optic_id = $row['dentists_branch_user_id'];
                
                $optic_name = $row['name_of_hospital'];
                $address = $row['address'];
                $pincode = $row['pincode'];
                $city = $row['city'];
                $state = $row['state'];
                $phone = $row['phone'];
                $telephone = $row['phone'];
                $store_since = $row['establishment_year'];
                $discount = $row['user_discount'];
                $email = $row['email'];
                $profile_pic = $row['image'];
               
                
                $resultpost8[] = array(
                     'id' => $optic_id,
                        'name' => $optic_name,
                        'listing_id' => $optic_id,
                        'listing_type' => "17",
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'address' => $address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact_no' => $phone,
                        'email' => $email,
                      
                        'profile_pic' => $profile_pic,
                        'distance' => (string)0,
                        'favourite' => (string)0,
                        'rating' => (string) 1,
                        'store_open' => "12:00 AM",
                        'store_close' =>"12:00 PM",
                    
                );
            }
          return   $resultpost8;
        }
        else{
         return   $resultpost8= array();
        }
         }
          
       else
       return $resultpost0 = array();
       
    }
    
    
    public function card_wallet_list($user_id, $card)
    {
        include('s3_config.php');
        date_default_timezone_set('Asia/Kolkata');
       
    	    $resultpost =array();
    	  
        $query = $this->db->query("SELECT * FROM `cardDetails` WHERE p_cardid='$card' and status=1");
        if($query->num_rows()>0)
        {
        foreach ($query->result_array() as $row) {

            $resultpost[] = array
                (
                   "card_id" => $row['card_id'],
                   "card_name" => $row['card_name'],
                  

                );
        }
        
    	        
    	        
             
                           return $resultpost;
                            
                    
    	    } 
    	    else
    	    {
    	      return  $resultpost; 
    	    }
    
    }
    
    
     public function addCheckInDetails($user_id,$vendor_id,$amount,$tags,$show_image,$listing_id)
    {
        include('s3_config.php');
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
         if (isset($_FILES["bill_image"]) AND ! empty($_FILES["bill_image"]["name"])) 
            {
                $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                include('s3_config.php');
                $img_name = $_FILES['bill_image']['name'];
                $img_size = $_FILES['bill_image']['size'];
                $img_tmp = $_FILES['bill_image']['tmp_name'];
                $ext = getExtension($img_name);
                if (strlen($img_name) > 0) 
                    {
                        if ($img_size < (50000 * 50000)) 
                           {
                                if (in_array($ext, $img_format)) 
                                {
                                    $profile_pic = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/Check_In_Image/' . $profile_pic;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }
                            }
                    }
            } 
        else 
                {
                    $profile_pic = '';
                }
      
         $p_results = $this->db->query("INSERT INTO `check_in_data`(`vendor_id`,`listing_id`, `user_id`, `bill_image`, `tags`, `amount`, `created_at`,`showimage`) VALUES('$vendor_id','$listing_id','$user_id','$profile_pic' ,'$tags', '$amount','$date','$show_image')");
             $insert_id = $this->db->insert_id();
    	    if($insert_id)
    	    {
    	       // multiple image/video
    	        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                $video_format = array("mp4", "avi", "flv", "wmv", "mov", "3gp", "MP4", "AVI", "FLV", "WMV", "MOV", "3GP");
                include('s3_config.php');
    	        //$image = count($_FILES['image']['name']); 
                if (! empty($_FILES["image"]["name"])) {
                    $flag = '1';
                    $video_flag = '1';
                    foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                        $img_name = $key . $_FILES['image']['name'][$key];
                        $img_size = $_FILES['image']['size'][$key];
                        $img_tmp = $_FILES['image']['tmp_name'][$key];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) 
                           {
                                if (in_array($ext, $img_format)) 
                                  {
                                    $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/Check_In_Image/' . $actual_image_name;
                                    if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                        
                                        $p_results = $this->db->query("INSERT INTO `check_in_image`(`check_in_id`, `image`, `type`, `created_at`) VALUES('$insert_id','$actual_image_name','image', '$date')");
                                    }
                                }
                                if (in_array($ext, $video_format)) {
                                    $uniqid = uniqid() . date("YmdHis");
                                    $actual_video_name = $uniqid . "." . $ext;
                                    $actual_video_path = 'images/Check_In_Image/' . $actual_video_name;
                                    if ($s3->putObjectFile($img_tmp, $bucket, $actual_video_path, S3::ACL_PUBLIC_READ)) {
                                       
                                       $p_results = $this->db->query("INSERT INTO `check_in_image`(`check_in_id`, `image`, `type`, `created_at`) VALUES('$insert_id','$actual_video_name','video', '$date')");
        
                                    }
                                }
                        }
                    }
                }    
                
           
    	        
    	        
    	        
               return array(
                            "status" => 200,
                            "message" => "success",
                            
                        );
    	    } 
    	    else
    	    {
    	      return array(
                            "status" => 201,
                            "message" => "fail",
                            
                        );  
    	    }
    
    }
    
    
      public function all_list($user_id,$mlat, $mlng,$page,$keyword)
    {
          $radius = $page*500;
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        
        
        $start = ($page - 1) * $limit;
      
       $resultpost_pharmacy=array();
       $resultpost_doctor=array();
       $resultpost_hospital=array();
       $resultpost_lab=array();
       $resultpost_spa=array();
       $resultpost_fitness=array();
        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            return $dist;
        }
          if($keyword!="")
          {
               $sql1   = sprintf("SELECT `user_id`,`pharmacy_branch_user_id` ,`medical_name`, `lat`, `lng`,( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND visible <> '0' AND type <> 'branch' AND medical_name LIKE '%%$keyword%%'   HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
           }
          else
          {
            
            $sql1 = sprintf("SELECT `user_id`,`pharmacy_branch_user_id`, `medical_name` ,`lat`, `lng`,( 6371 * acos( cos( radians($mlat) ) * cos( radians(lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians(lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND visible <> '0'  HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
          }
            $query1 = $this->db->query($sql1);
            $count1 = $query1->num_rows();
            if($count1>0){
                foreach ($query1->result_array() as $row) {
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $medical_id_1 = $row['user_id'];
                    $medical_id_2 = $row['pharmacy_branch_user_id'];
                    if($medical_id_2!='0')
                    {
                        $medical_id= $row['pharmacy_branch_user_id'];
                    }
                    else
                    {
                        $medical_id= $row['user_id'];
                    }
                    $medical_name = $row['medical_name'];
                    $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                    $distance_root =  round(($distances/1000),1);
                      if ($distances > 999) {
                                    $distance = $distances / 1000;
                                    $meter = round($distance, 2) . ' km';
                                } else {
                                    $meter = round($distances) . ' meters away';
                                }
                     
                    $resultpost_pharmacy[] = array(
                        'id' => $medical_id,
                        'name' => $medical_name,
                        'user_id' => $medical_id,
                        'clinic_name' => "",
                        'listing_type' => '13',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'distance' => $meter,
                        'distance_cal'=>$distance_root
                       
                    );
                }
                 
                //added for generico pharmacy branch 
               
       
                   
              
            
                
            }
            else{
              $resultpost_pharmacy= array();
                
            }
        
        
       
           if($keyword!="")
           {
               $sql2   = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_clinic.clinic_name,doctor_clinic.lat,doctor_clinic.lng,doctor_clinic.id as clinic_id, ( 6371 * acos( cos( radians($mlat) ) * cos( radians(doctor_clinic.lat ) ) * cos( radians(doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians(doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list LEFT JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.doctor_name LIKE '%%$keyword%%'  and  doctor_list.is_active = 1  HAVING distance < '%s' ORDER BY distance  LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
           }
          else
          {
             
           $sql2   = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_clinic.clinic_name,doctor_clinic.lat,doctor_clinic.lng,doctor_clinic.id as clinic_id, ( 6371 * acos( cos( radians($mlat) ) * cos( radians(doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians(doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list LEFT JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE  doctor_list.is_active = 1  HAVING distance < '%s' ORDER BY distance  LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
          }
            $query2 = $this->db->query($sql2);
            $count2 = $query2->num_rows();
            if ($count2 > 0) {
                foreach ($query2->result_array() as $row) {
                    $lat                 = $row['lat'];
                    $lng                 = $row['lng'];
                    $doctor_name         = $row['doctor_name'];
                    $doctor_user_id      = $row['user_id'];
                    $clinic_name         = $row['clinic_name'];
                    $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                    $distance_root =  round(($distances/1000),1);
                      if ($distances > 999) {
                                    $distance = $distances / 1000;
                                    $meter = round($distance, 2) . ' km';
                                } else {
                                    $meter = round($distances) . ' meters away';
                                }
                    $resultpost_doctor[] = array(
                        'id' => $doctor_user_id,
                        'name' => $doctor_name,
                        'user_id' => $doctor_user_id,
                        'clinic_name' => $clinic_name,
                        'listing_type' => '5',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'distance' => $meter,
                        'distance_cal'=>$distance_root
                       
                    );
                }
                
            
            } else {
               $resultpost_doctor = array();
            }
        
      
       
  
           if($keyword!="")
           {
              $sql3 = sprintf("SELECT user_id, name_of_hospital,lat, lng,( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians(lat ) ) ) ) AS distance FROM hospitals WHERE is_approval='1' AND is_active='1' AND name_of_hospital LIKE '%%$keyword%%'  HAVING distance < '%s'  LIMIT $start, $limit ", ($mlat), ($mlng), ($mlat), ($radius));
            }
          else
          {
             
            $sql3 = sprintf("SELECT user_id, name_of_hospital,lat, lng,( 6371 * acos( cos( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians(lat ) ) ) ) AS distance FROM hospitals WHERE is_approval='1' AND is_active='1'  HAVING distance < '%s'  LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
          }
            $query3 = $this->db->query($sql3);
            $count3 = $query3->num_rows();
            if ($count3 > 0) {
                foreach ($query3->result_array() as $row) {
                    $lat                 = $row['lat'];
                    $lng                 = $row['lng'];
                    $hospital_name       = $row['name_of_hospital'];
                    $user_id             = $row['user_id'];
                   
                    $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                    $distance_root =  round(($distances/1000),1);
                      if ($distances > 999) {
                                    $distance = $distances / 1000;
                                    $meter = round($distance, 2) . ' km';
                                } else {
                                    $meter = round($distances) . ' meters away';
                                }
                    $resultpost_hospital[] = array(
                        'id' => $user_id,
                        'name' => $hospital_name,
                        'user_id' => $user_id,
                        'clinic_name' => "",
                        'listing_type' => '8',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'distance' => $meter,
                        'distance_cal'=>$distance_root
                       
                    );
                }
               
            
            } else {
               $resultpost_hospital = array();
            }
        
      
        
    
            $resultpost_branch=array();
            if($keyword!="")
              {
             
               $sql4    = sprintf("SELECT `user_id`, `lab_name`, `latitude`, `longitude`, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( latitude) ) * cos( radians( longitude) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE `is_active` = 1 and lab_name LIKE '%%$keyword%%'   HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
               
           }
            else
              {
                $sql4    = sprintf("SELECT `user_id`, `lab_name`, `latitude`, `longitude`, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( latitude) ) * cos( radians( longitude) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE `is_active` = 1  HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
               }
                $query4  = $this->db->query($sql4);
                $count4  = $query4->num_rows();
            
              if ($count4 > 0) {
                foreach ($query4->result_array() as $row) {
                    $user_id           = $row['user_id'];
                    $lab_name          = $row['lab_name'];
                    $lat               = $row['latitude'];
                    $lng               = $row['longitude'];
                    $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                    $distance_root =  round(($distances/1000),1);
                      if ($distances > 999) {
                                    $distance = $distances / 1000;
                                    $meter = round($distance, 2) . ' km';
                                } else {
                                    $meter = round($distances) . ' meters away';
                                }
                  
                    $resultpost_lab[] = array(
                        'id' => $user_id,
                        'name' => $lab_name,
                        'user_id' => $user_id,
                        'clinic_name' => "",
                        'listing_type' => '10',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'distance' => $meter,
                        'distance_cal'=>$distance_root
                    
                    );
                }
               
            
            } else {
              $resultpost_lab = array();
            }
  
       
      
                if($keyword!="")
              {
                
                
                 $sql5 = sprintf("SELECT fcb.user_id, fcb.branch_name,fcb.lat, fcb.lng,( 6371 * acos( cos( radians($mlat) ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'  and fcb.branch_name LIKE '%%$keyword%%'  and fc.vendor_type='Spa' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
              }
            else
              {
                  
            $sql5 = sprintf("SELECT fcb.user_id, fcb.branch_name,fcb.lat, fcb.lng,( 6371 * acos( cos( radians($mlat) ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'  and  fc.vendor_type='Spa' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
              }
            $query5 = $this->db->query($sql5);
            $count5 = $query5->num_rows();
            if ($count5 > 0) {
                foreach ($query5->result_array() as $row) {
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $branch_name = $row['branch_name'];
                    $listing_id = $row['user_id'];
                  
                    $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                    $distance_root =  round(($distances/1000),1);
                      if ($distances > 999) {
                                    $distance = $distances / 1000;
                                    $meter = round($distance, 2) . ' km';
                                } else {
                                    $meter = round($distances) . ' meters away';
                                }
                  
                    $resultpost_spa[] = array(
                        'id' => $listing_id,
                        'name' => $branch_name,
                        'user_id' => $listing_id,
                        'clinic_name' => "",
                        'listing_type' => '36',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'distance' => $meter,
                        'distance_cal'=>$distance_root
                    
                    );
                }
               
            
            
            } else {
               $resultpost_spa = array();
            }
           
      
             if($keyword!="")
          {
              
             $sql6 =   sprintf("SELECT fcb.user_id, fcb.branch_name,fcb.lat, fcb.lng,  ( 6371 * acos( cos( radians($mlat) ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1' AND fcb.branch_name LIKE '%%$keyword%%' HAVING distance < '%s' ORDER BY distance LIMIT  $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
           }
          else
          {
             $sql6 = sprintf("SELECT fcb.user_id, fcb.branch_name,fcb.lat, fcb.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'  HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
          }
            $query6 = $this->db->query($sql6);
            $count6 = $query6->num_rows();
            if ($count6 > 0) {
                foreach ($query6->result_array() as $row) {
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $branch_name = $row['branch_name'];
                    $listing_id = $row['user_id'];
                  
                    $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                    $distance_root =  round(($distances/1000),1);
                      if ($distances > 999) {
                                    $distance = $distances / 1000;
                                    $meter = round($distance, 2) . ' km';
                                } else {
                                    $meter = round($distances) . ' meters away';
                                }
                  
                    $resultpost_fitness[] = array(
                        'id' => $listing_id,
                        'name' => $branch_name,
                        'user_id' => $listing_id,
                        'clinic_name' => "",
                        'listing_type' => '6',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'distance' => $meter,
                        'distance_cal'=>$distance_root
                    
                    );
                }
                
            
            } else {
               $resultpost_fitness = array();
            }
              
      
      $resultpost0=array_merge($resultpost_pharmacy,$resultpost_doctor,$resultpost_hospital,$resultpost_lab,$resultpost_spa,$resultpost_fitness);
      
       usort($resultpost0, function($a, $b) {
                                    $a = $a['distance_cal'];
                                    $b = $b['distance_cal'];
                        if ($a == $b) { return 0; }
                            return ($a < $b) ? -1 : 1;
                        });
                         return $resultpost0;
     
       
    }
   
}
