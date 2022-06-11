<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class FitnesscenterModel extends CI_Model {

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
            
             if ($q->expired_at < date("Y-m-d H:i:s", strtotime("-1 days"))) {
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
               // echo $this->db->last_query();
                return array(
                    'status' => 200,
                    'message' => 'Authorized.'
                );
            }
        }
    }

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

    public function check_time_format($time) {
        date_default_timezone_set('Asia/Kolkata');
        $time_filter = preg_replace('/\s+/', '', $time);
        $final_time = date("h:i A", strtotime($time_filter));
        $final_times = date("H:i", strtotime($final_time));
        return $final_times;
    }

    public function fitness_center_list($user_id, $latitude, $longitude, $category) {
        $radius = '5';

        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyCMHN__LczEziaYuKXQ-SKdiMfx5AiY66o&callback=initMap";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return $dist;
        }

        //echo $sql = sprintf("SELECT `id`, `user_id`, `branch_name`, `branch_image`, `branch_phone`, `branch_email`, `about_branch`, `branch_business_category`, `branch_offer`, `branch_facilities`, `branch_address`, `opening_hours`, `pincode`, `state`, `city`, `lat`, `lng`, `is_free_trail`, `from_trail_time`,`user_discount`, `to_trail_time`, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM fitness_center_branch  WHERE  FIND_IN_SET('" . $category . "', branch_business_category)  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
         $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time, ( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1' AND FIND_IN_SET('" . $category . "', branch_business_category)  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
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
                $branch_offer = $row['branch_offer'];
                $branch_facilities = $row['branch_facilities'];
                $branch_address = $row['branch_address'];
                $opening_hours = $row['opening_hours'];
                $pincode = $row['pincode'];
                $state = $row['state'];
                $city = $row['city'];
                $is_free_trail = $row['is_free_trail'];
                $from_trail_time = $row['from_trail_time'];
                $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '6';
                $rating = '4.5';
                $reviews = '0';
                $profile_views = '0';
                $user_discount = $row['user_discount'];
                
                /*$gallery_query = $this->db->query("SELECT is_active FROM `fitness_center` WHERE `user_id`='$listing_id'");
                $gallery_array = array();
                */

                if ($branch_image != '') {
                    $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                } else {
                    $branch_image = '';
                }


                $gallery_query = $this->db->query("SELECT `id`,`media_name`, `type` FROM `fitness_gallery` WHERE `branch_id`='$branch_id' AND `fitness_center_id`= '$listing_id' ");
                $gallery_array = array();
                $gallery_count = $gallery_query->num_rows();
                if ($gallery_count > 0) {
                    foreach ($gallery_query->result_array() as $rows) {
                        $media_name = $rows['media_name'];
                        $type = $rows['type'];
                        $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $type . '/' . $media_name;

                        $gallery_array[] = array(
                            "image" => $gallery,
                            "type" => $type
                        );
                    }
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



               /*$what_we_offer_array = '';
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    $what_we_offer_array[] = $what_we_offer;
                }

                $facilities_array = array();
                $facilities = explode(',', $branch_facilities);
                foreach ($facilities as $facilities) {
                    $facilities_array[] = $facilities;
                }*/
                
                $what_we_offer_array = array();
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                    if($what_we_offer != ""){
                        if(preg_match('/[0-9]/', $what_we_offer)){
                            $Query = $this->db->query("SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'");
                              $count = $Query->num_rows();
                            if($count>0)
                            {
                            $what_we_offer_value = $Query->row()->what_we_offer;
                            $what_we_offer_array[] = $what_we_offer_value;
                            }
                            else
                            {
                                $what_we_offer_array[] = $what_we_offer;
                            }
                        }else{
                            $what_we_offer_array[] = $what_we_offer;
                        }
                        
                    }
                    
                }

                $facilities_array = array();
                $facilities = explode(',', $branch_facilities);
                foreach ($facilities as $facilities) {
                    if($what_we_offer != "" && preg_match('/[0-9]/', $facilities)){
                        if(preg_match('/[0-9]/', $facilities)){
                            $Query = $this->db->query("SELECT * FROM fitness_facilities WHERE id='$facilities'");
                            $facilities_val = $Query->row()->facilities;
                            $facilities_array[] = $facilities_val;
                            
                        }
                        else{
                            $facilities_array[] = $facilities;
                        }
                    }
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $package_list = array();
                $package_query = $this->db->query("SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND categ_id='$category' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id = $package_row['id'];
                        $package_name = $package_row['package_name'];
                        $package_details = $package_row['package_details'];
                        $price = $package_row['price'];
                        $image = $package_row['image'];
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $package_list[] = array(
                            'package_id' => $package_id,
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price,
                            'image' => $image
                        );
                    }
                } else {
                    $package_list = array();
                }


                $booking_slot_array = array();
                if ($is_free_trail == '1') {
                    $from_trail_times = $this->check_time_format($from_trail_time);
                    $to_trail_times = $this->check_time_format($to_trail_time);
                    $time_difference = 60;
                    for ($i = strtotime($from_trail_times); $i <= strtotime($to_trail_times); $i = $i + $time_difference * 60) {
                        $booking_slot_array[] = array(
                            'time' => date("h:i A", $i)
                        );
                    }
                }

                $is_trial_order = 0;
                
                $is_order_query = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category' AND user_id='$user_id' AND package_id='100' AND status !='3' AND status !='9'");
                $is_order_count = $is_order_query->num_rows();

                if ($is_order_count > 0) {
                    $is_trial_order = 1;
                } 
                
                $is_order_query1 = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category' AND user_id='$user_id' AND package_id !='100' AND status !='3' AND status !='9'");
                $is_order_count1 = $is_order_query1->num_rows();

                if ($is_order_count1 > 0) {
                    $is_trial_order = 1;
                } 

                $profile_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();
                $reviews = $this->db->select('id')->from('fitness_center_review')->where('branch_id', $branch_id)->get()->num_rows();

                

                $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
                $enquiry_number = "9619146163";
                $resultpost[] = array(
                    'branch_id' => $branch_id,
                    'lat' => $lat,
                    'lng' => $lng,
                    'distance' => $distances,
                    'category_id' => $category,
                    'listing_id' => $listing_id,
                    'listing_type' => $listing_type,
                    'about_us' => $about_branch,
                    'center_name' => $branch_name,
                    'branch_name' => $branch_name,
                    'address' => $branch_address,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact' => $branch_phone,
                    'exotel_no' => '02233721563',
                    'enquiry_number' => $enquiry_number,
                    'email' => $branch_email,
                    'image' => $branch_image,
                    'is_free_trail' => $is_free_trail,
                    'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                    'is_trial_order' => $is_trial_order,
                    'profile_views' => $profile_views,
                    'user_discount' => $user_discount
                );
            }
        } else {
            $resultpost = array();
        }
      
        function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
            $sort_col = array();
            foreach ($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }
            array_multisort($sort_col, $dir, $arr);
        }

        array_sort_by_column($resultpost, 'distance');
       // print_r($resultpost);
        return $resultpost;
    }
     public function fitness_center_list_v2($user_id, $latitude, $longitude, $category,$term) {
        $radius = '5';
        if(empty($category))
        {
           $category=0; 
        }
        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyCMHN__LczEziaYuKXQ-SKdiMfx5AiY66o&callback=initMap";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return $dist;
        }

        if($category=="0" && !empty($term))
        {
            
             $query = $this->db->query("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time,( 6371 * acos( cos( radians($latitude) ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( fcb.lat ) ) ) ) AS distance  FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1' AND fcb.branch_name LIKE '%$term%'  HAVING distance <= '$radius' ORDER BY distance LIMIT 0 , 20");
           
        }
         elseif($category!="0" && empty($term))
        {
            
            
            
           $query =   $this->db->query("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time,( 6371 * acos( cos( radians($latitude) ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( fcb.lat ) ) ) ) AS distance  FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1' AND FIND_IN_SET('" . $category . "', branch_business_category) HAVING distance <= '$radius' ORDER BY distance LIMIT 0 , 20");
            
            
           
        }
        elseif($category=="0" && empty($term))
        {
            
           $query =    $this->db->query("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time,( 6371 * acos( cos( radians($latitude) ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( fcb.lat ) ) ) ) AS distance  FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'  HAVING distance <= '$radius' ORDER BY distance LIMIT 0 , 20");
            
        }
       
         elseif($category!="0" && !empty($term))
        {
             $query =       $this->db->query("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time,( 6371 * acos( cos( radians($latitude) ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( fcb.lat ) ) ) ) AS distance  FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fcb.branch_name LIKE '%$term%' AND  fc.is_active='1' AND FIND_IN_SET('" . $category . "', branch_business_category)  HAVING distance <= '$radius' ORDER BY distance LIMIT 0 , 20");
                 
           
        }
       
        
       
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
                $branch_offer = $row['branch_offer'];
                $branch_facilities = $row['branch_facilities'];
                $branch_address = $row['branch_address'];
                $opening_hours = $row['opening_hours'];
                $pincode = $row['pincode'];
                $state = $row['state'];
                $city = $row['city'];
                $is_free_trail = $row['is_free_trail'];
                $from_trail_time = $row['from_trail_time'];
                $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '6';
                $rating = '4.5';
                $reviews = '0';
                $profile_views = '0';
                $user_discount = $row['user_discount'];
                $category=$row['branch_business_category'];
                
                /*$gallery_query = $this->db->query("SELECT is_active FROM `fitness_center` WHERE `user_id`='$listing_id'");
                $gallery_array = array();
                */

                if ($branch_image != '') {
                    $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                } else {
                    $branch_image = '';
                }


                $gallery_query = $this->db->query("SELECT `id`,`media_name`, `type` FROM `fitness_gallery` WHERE `branch_id`='$branch_id' AND `fitness_center_id`= '$listing_id' ");
                $gallery_array = array();
                $gallery_count = $gallery_query->num_rows();
                if ($gallery_count > 0) {
                    foreach ($gallery_query->result_array() as $rows) {
                        $media_name = $rows['media_name'];
                        $type = $rows['type'];
                        $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $type . '/' . $media_name;

                        $gallery_array[] = array(
                            "image" => $gallery,
                            "type" => $type
                        );
                    }
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

               /*$what_we_offer_array = '';
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    $what_we_offer_array[] = $what_we_offer;
                }

                $facilities_array = array();
                $facilities = explode(',', $branch_facilities);
                foreach ($facilities as $facilities) {
                    $facilities_array[] = $facilities;
                }*/
                
                
                 $category_array = array();
                $categorys = explode(',', $category);
             
              for($i=0; $i < count($categorys); $i++)
                {
                    if($categorys[$i]!='')
                    {
                      $cat_new=$categorys[$i];
                
            $Query = $this->db->query("SELECT * FROM business_category WHERE id = '$cat_new' and category_id='6'");
                            $category_val = $Query->row()->category;
                            $category_array[] = $category_val;
                    }
                   
                }
                $what_we_offer_array = array();
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                    if($what_we_offer != ""){
                        if(preg_match('/[0-9]/', $what_we_offer)){
                            $Query = $this->db->query("SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'");
                              $count = $Query->num_rows();
                            if($count>0)
                            {
                            $what_we_offer_value = $Query->row()->what_we_offer;
                            $what_we_offer_array[] = $what_we_offer_value;
                            }
                            else
                            {
                                $what_we_offer_array[] = $what_we_offer;
                            }
                        }else{
                            $what_we_offer_array[] = $what_we_offer;
                        }
                        
                    }
                    
                }

                $facilities_array = array();
                $facilities = explode(',', $branch_facilities);
                foreach ($facilities as $facilities) {
                    if($what_we_offer != "" && preg_match('/[0-9]/', $facilities)){
                        if(preg_match('/[0-9]/', $facilities)){
                            $Query = $this->db->query("SELECT * FROM fitness_facilities WHERE id='$facilities'");
                            $facilities_val = $Query->row()->facilities;
                            $facilities_array[] = $facilities_val;
                            
                        }
                        else{
                            $facilities_array[] = $facilities;
                        }
                    }
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $package_list = array();
                //$package_query = $this->db->query("SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND FIND_IN_SET('" . $category . "', categ_id) order by id asc");
               $package_query = $this->db->query("SELECT id,package_name,package_details,price,image,discount FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND FIND_IN_SET('" . $branch_id . "', branch)  order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id = $package_row['id'];
                        $package_name = $package_row['package_name'];
                        $package_details = $package_row['package_details'];
                        $price = $package_row['price'];
                        $image = $package_row['image'];
                        $discount = $package_row['discount'];
                         if(empty($discount))
                            {
                                $discount="0";
                            }
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/'.$image;
                        $package_list[] = array(
                            'package_id' => $package_id,
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price,
                            'user_discount'=>$discount,
                            'image' => $image
                        );
                    }
                } else {
                    $package_list = array();
                }


                $booking_slot_array = array();
                if ($is_free_trail == '1') {
                    $from_trail_times = $this->check_time_format($from_trail_time);
                    $to_trail_times = $this->check_time_format($to_trail_time);
                    $time_difference = 60;
                    for ($i = strtotime($from_trail_times); $i <= strtotime($to_trail_times); $i = $i + $time_difference * 60) {
                        $booking_slot_array[] = array(
                            'time' => date("h:i A", $i)
                        );
                    }
                }

                $is_trial_order = 0;
                
                $is_order_query = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category' AND user_id='$user_id' AND package_id='100' AND status !='3' AND status !='9'");
                
                $is_order_count = $is_order_query->num_rows();

                if ($is_order_count > 0) {
                    $is_trial_order = 1;
                } 
               
                   $is_order_query1 = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category' AND user_id='$user_id' AND package_id !='100' AND status !='3' AND status !='9'");
                
                $is_order_count1 = $is_order_query1->num_rows();

                if ($is_order_count1 > 0) {
                    $is_trial_order = 1;
                } 

                $profile_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();
                $reviews = $this->db->select('id')->from('fitness_center_review')->where('branch_id', $branch_id)->get()->num_rows();



                $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
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
                if(empty($branch_email))
                {
                    $branch_email="";
                }
                else
                {
                    $branch_email;
                }
                
                 $sub_array = array();
            $personal_trainer = $this->db->select('*')->from('personal_trainers')->where('user_id', $listing_id)->get();
            $count = $personal_trainer->num_rows();
            if ($count > 0) 
            {
                foreach ($personal_trainer->result_array() as $row1) 
                {
                    $id1 = $row1['id'];
                    $category_id1 = $row1['category_id'];
                    $cat1 = explode(',',$category_id1);
                    $category_name1 = array();
                    for($i=0;$i<count($cat1);$i++)
                    {
                        if(!empty($cat1[$i]))
                        {
                        $sql = $this->db->select('category')->from('business_category')->where('id', $cat1[$i])->get()->row()->category;
                        $category_name1[]=$sql;
                        }
                    }
                    
                    $manager_name1 = $row1['manager_name'];
                    $address1 = $row1['address'];
                    $pincode1 = $row1['pincode'];
                    $contact1 = $row1['contact'];
                    $city1 = $row1['city'];
                    $state1 = $row1['state'];
                    $email1 = $row1['email'];
                    $qualifications1 = $row1['qualifications'];
                    $trainer_opening_hours1 = $row1['opening_hours'];
                    $experience1 = $row1['experience'];
                    $fitness_trainer_pic1 = $row1['fitness_trainer_pic'];
                    $gender1 = $row1['gender'];
                   
                    $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                     $session1[] = array(
                                'id'=>'1',
                                'package_name' => 'zumba',
                                'package_details' => 'Details about the package',
                                'price'=>'1,500',
                                'image'=> 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image
                            );
                         
                           $session1[] = array(
                                'id'=>'2',
                                'package_name' => 'zumba',
                                'package_details' => 'Details about the package1',
                                'price'=>'1,500',
                                'image'=> 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image
                            );
                         
                           
                         $final_session1=  array_merge($session1,$session2);
                         
                         
                     $branch_final_Day1      = array();
                        $day_array_list_branch1 = explode('|', $trainer_opening_hours1);
                        if (count($day_array_list_branch1) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch1); $i++) {
                                $day_list1 = explode('>', $day_array_list_branch1[$i]);
                                for ($j = 0; $j < count($day_list1); $j++) {
                                    $day_time_list1 = explode('-', $day_list1[$j]);
                                    for ($k = 1; $k < count($day_time_list1); $k++) {
                                        $time_list11 = explode(',', $day_time_list1[0]);
                                        $time_list21 = explode(',', $day_time_list1[1]);
                                        $time1       = array();
                                        $open_close1 = array();
                                        for ($l = 0; $l < count($time_list11); $l++) {
                                            $time_check1        = $time_list11[$l] . '-' . $time_list21[$l];
                                            $time1[]            = str_replace('close-close', 'close', $time_check1);
                                            $system_start_time1 = date("H.i", strtotime($time_list11[$l]));
                                            $system_end_time1   = date("H.i", strtotime($time_list21[$l]));
                                            $current_time1      = date('H.i');
                                            if ($current_time1 > $system_start_time1 && $current_time1 < $system_end_time1) {
                                                $open_close1[] = 'open';
                                            } else {
                                                $open_close1[] = 'close';
                                            }
                                        }
                                    }
                                }
                                $branch_final_Day1[] = array(
                                    'day' => $day_list1[0],
                                    'time' => $time1,
                                    'status' => $open_close1
                                );
                            }
                        } else {
                            $branch_final_Day1[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                    $sub_array1[] = array('id'=> $id1,
                                       'category' => $category_name1,
                                       'manager_name' => $manager_name1,
                                       'address' => $address1,
                                       'pincode' => $pincode1,
                                       'city' => $city1,
                                       'state' => $state1,
                                       'email' => $email1,
                                       'contact' => $contact1,
                                       'qualifications' => $qualifications1,
                                       'experience' => $experience1,
                                       'fitness_trainer_pic' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$fitness_trainer_pic1,
                                       'gender' => $gender1,
                                       'rating' => $rating,
                                       'package_trainer' => $final_session1
                                     );
                                       //'opening_hours' => $branch_final_Day);
                    
                }
                    
            }
            else {
               $sub_array1=array();
            }

                
                
                
                
                
                
                $resultpost[] = array(
                    'branch_id' => $branch_id,
                    'lat' => $lat,
                    'lng' => $lng,
                    'distance' => $distances,
                    'category_id' => $category,
                    'category_name'=>$category_array,
                    'listing_id' => $listing_id,
                    'listing_type' => $listing_type,
                    'about_us' => $about_branch,
                    'center_name' => $branch_name,
                    'branch_name' => $branch_name,
                    'address' => $branch_address,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact' => $branch_phone,
                    'exotel_no' => '02233721563',
                    'enquiry_number' => $enquiry_number,
                    'email' => $branch_email,
                    'image' => $branch_image,
                    'is_free_trail' => $is_free_trail,
                    'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'trainer_list' => $sub_array1,
                    'current_time'=>$current_day_final,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                    'is_trial_order' => $is_trial_order,
                    'profile_views' => $profile_views,
                    'user_discount' => $user_discount,
                    'new' => $new
                );
            }
        } else {
            $resultpost = array();
        }
      
        function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
            $sort_col = array();
            foreach ($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }
            array_multisort($sort_col, $dir, $arr);
        }

        array_sort_by_column($resultpost, 'new');
       // print_r($resultpost);
        return $resultpost;
    }
    function fitness_trainer_list($user_id,$listing_id)
    {
            $sub_array = array();
            $personal_trainer = $this->db->select('*')->from('personal_trainers')->where('user_id', $listing_id)->get();
            $count = $personal_trainer->num_rows();
            if ($count > 0) 
            {
                foreach ($personal_trainer->result_array() as $row) 
                {
                    $id = $row['id'];
                    $category_id = $row['category_id'];
                    $cat = explode(',',$category_id);
                    $category_name = array();
                    for($i=0;$i<count($cat);$i++)
                    {
                        $sql = $this->db->select('category')->from('business_category')->where('id', $cat[$i])->get()->row()->category;
                        $category_name[]=$sql;
                    }
                    
                    $manager_name = $row['manager_name'];
                    $address = $row['address'];
                    $pincode = $row['pincode'];
                    $contact = $row['contact'];
                    $city = $row['city'];
                    $state = $row['state'];
                    $email = $row['email'];
                    $qualifications = $row['qualifications'];
                    $trainer_opening_hours = $row['opening_hours'];
                    $experience = $row['experience'];
                    $fitness_trainer_pic = $row['fitness_trainer_pic'];
                    $gender = $row['gender'];
                    $price_session = $row['price_session'];
                    $price_monthly = $row['price_monthly'];
                    $sessions = $row['sessions'];
                    $session_period = $row['session_period'];
                    
                     $branch_final_Day      = array();
                        $day_array_list_branch = explode('|', $trainer_opening_hours);
                        if (count($day_array_list_branch) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch); $i++) {
                                $day_list = explode('>', $day_array_list_branch[$i]);
                                for ($j = 0; $j < count($day_list); $j++) {
                                    $day_time_list = explode('-', $day_list[$j]);
                                    for ($k = 1; $k < count($day_time_list); $k++) {
                                        $time_list1 = explode(',', $day_time_list[0]);
                                        $time_list2 = explode(',', $day_time_list[1]);
                                        $time       = array();
                                        $open_close = array();
                                        for ($l = 0; $l < count($time_list1); $l++) {
                                            $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
                                            $time[]            = str_replace('close-close', 'close', $time_check);
                                            $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                            $system_end_time   = date("H.i", strtotime($time_list2[$l]));
                                            $current_time      = date('H.i');
                                            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                                $open_close[] = 'open';
                                            } else {
                                                $open_close[] = 'close';
                                            }
                                        }
                                    }
                                }
                                $branch_final_Day[] = array(
                                    'day' => $day_list[0],
                                    'time' => $time,
                                    'status' => $open_close
                                );
                            }
                        } else {
                            $branch_final_Day[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                    $sub_array[] = array('id'=> $id,
                                       'category' => $category_name,
                                       'manager_name' => $manager_name,
                                       'address' => $address,
                                       'pincode' => $pincode,
                                       'city' => $city,
                                       'state' => $state,
                                       'email' => $email,
                                       'contact' => $contact,
                                       'qualifications' => $qualifications,
                                       'experience' => $experience,
                                       'fitness_trainer_pic' => $fitness_trainer_pic,
                                       'gender' => $gender,
                                       'price_session' => $price_session,
                                       'price_monthly' => $price_monthly,
                                       'sessions' => $sessions,
                                       'session_period' => $session_period,
                                       'opening_hours' => $branch_final_Day);
                    
                }
                return $sub_array;    
            }
            else {
                return array();
            }
    }
    //added by zak for branch branch details 
    public function fitness_center_branch_details($user_id, $listing_id, $branch_id)
    {
          $branch_list = array();
          /*echo"SELECT * FROM `fitness_center_branch` WHERE user_id='$listing_id' AND id='$branch_id' order by id asc";*/
         $branch_query = $this->db->query("SELECT * FROM `fitness_center_branch` WHERE user_id='$listing_id' AND id='$branch_id' order by id asc");
         $branch_count = $branch_query->num_rows();
           
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $row) {
                        $branch_id = $row['id'];
                        $branch_name = $row['branch_name'];
                       $branch_image = $row['branch_image'];
                       $branch_phone = $row['branch_phone'];
                    $branch_email = $row['branch_email'];
                    $about_branch = $row['about_branch'];
                    $branch_offer = $row['branch_offer'];
                    $branch_facilities = $row['branch_facilities'];
                    $branch_address = $row['branch_address'];
                    $opening_hours = $row['opening_hours'];
                    $pincode = $row['pincode'];
                    $state = $row['state'];
                    $city = $row['city'];
                    $is_free_trail = $row['is_free_trail'];
                    $from_trail_time = $row['from_trail_time'];
                    $to_trail_time = $row['to_trail_time'];
                    
                    $listing_id = $row['user_id'];
                    $listing_type = '6';
                    $rating = '4.5';
                    $reviews = '0';
                    $profile_views = '0';
                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    $gallery_query = $this->db->query("SELECT `id`,`media_name`, `type` FROM `fitness_gallery` WHERE `branch_id`='$branch_id' AND `fitness_center_id`= '$listing_id' ");
                    $gallery_array = array();
                    $gallery_count = $gallery_query->num_rows();
                    if ($gallery_count > 0) {
                        foreach ($gallery_query->result_array() as $rows) {
                            $media_name = $rows['media_name'];
                            $type = $rows['type'];
                            $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $type . '/' . $media_name;

                            $gallery_array[] = array(
                                "image" => $gallery,
                                "type" => $type
                            );
                        }
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
           
           $what_we_offer_array = '';
                    $what_we_offer = explode(',', $branch_offer);
                    foreach ($what_we_offer as $what_we_offer) {
                        $what_we_offer_array[] = $what_we_offer;
                    }

                    $facilities_array = array();
                    $facilities = explode(',', $branch_facilities);
                    foreach ($facilities as $facilities) {
                        $facilities_array[] = $facilities;
                    }

                    $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                    $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                    $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

                    if ($is_follow > 0) {
                        $is_follow = 'Yes';
                    } else {
                        $is_follow = 'No';
                    }

       

                    $package_list = array();
                    $package_query = $this->db->query("SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' order by id asc");
                    $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id = $package_row['id'];
                            $package_name = $package_row['package_name'];
                            $package_details = $package_row['package_details'];
                            $price = $package_row['price'];
                            $image = $package_row['image'];
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                            $package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'image' => $image
                            );
                        }
                    } else {
                        $package_list = array();
                    }

                    $booking_slot_array = array();
                    if ($is_free_trail == '1') {
                        $from_trail_times = $this->check_time_format($from_trail_time);
                        $to_trail_times = $this->check_time_format($to_trail_time);
                        $time_difference = 60;
                        for ($i = strtotime($from_trail_times); $i <= strtotime($to_trail_times); $i = $i + $time_difference * 60) {
                            $booking_slot_array[] = array(
                                'time' => date("h:i A", $i)
                            );
                        }
                    }

                    $is_order_query = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND user_id='$user_id' AND package_id='100'");
                    $is_order_count = $is_order_query->num_rows();

                    if ($is_order_count > 0) {
                        $is_trial_order = 1;
                    } else {
                        $is_trial_order = 0;
                    }
                    $profile_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();
                    $reviews = $this->db->select('id')->from('fitness_center_review')->where('branch_id', $branch_id)->get()->num_rows();

                    $other_branch[] = array(
                        'branch_id' => $branch_id,
                        'category_id' => "",
                        'category' => "",
                        'listing_id' => $listing_id,
                        'listing_type' => $listing_type,
                        'about_us' => $about_branch,
                        'branch_name' => $branch_name,
                        'address' => $branch_address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact' => $branch_phone,
                        'enquiry_number' => "",
                        'exotel_no' => '02233721563',
                        'email' => $branch_email,
                        'image' => $branch_image,
                        'is_free_trail' => $is_free_trail,
                        'booking_slot_array' => $booking_slot_array,
                        'gallery' => $gallery_array,
                        'what_we_offer' => $what_we_offer_array,
                        'facilities' => $facilities_array,
                        'package_list' => $package_list,
                        'opening_day' => $final_Day,
                        'rating' => $rating,
                        'review' => $reviews,
                        'followers' => $followers,
                        'following' => $following,
                        'is_follow' => $is_follow,
                        'is_trial_order' => $is_trial_order,
                        'profile_views' => $profile_views
                    );
                    
                    }
                    
                    return $other_branch;
                }
                else
                return array();
    }
    //end 

    public function fitness_center_other_branch($user_id, $listing_id, $category_id, $branch_id) {


        $query = $this->db->query("SELECT `id`, `user_id`, `branch_name`, `branch_image`, `branch_phone`, `branch_email`, `about_branch`, `branch_business_category`, `branch_offer`, `branch_facilities`, `branch_address`, `opening_hours`, `pincode`, `state`, `city`, `lat`, `lng`, `is_free_trail`, `from_trail_time`, `to_trail_time` FROM `fitness_center_branch` WHERE   FIND_IN_SET('" . $category_id . "', branch_business_category)  AND user_id='$listing_id' AND id<>'$branch_id'");
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
                $branch_offer = $row['branch_offer'];
                $branch_facilities = $row['branch_facilities'];
                $branch_address = $row['branch_address'];
                $opening_hours = $row['opening_hours'];
                $pincode = $row['pincode'];
                $state = $row['state'];
                $city = $row['city'];
                $is_free_trail = $row['is_free_trail'];
                $from_trail_time = $row['from_trail_time'];
                $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '6';
                $rating = '4.5';
                $reviews = '0';
                $profile_views = '0';


                if ($branch_image != '') {
                    $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                } else {
                    $branch_image = '';
                }


                $gallery_query = $this->db->query("SELECT `id`,`media_name`, `type` FROM `fitness_gallery` WHERE `branch_id`='$branch_id' AND `fitness_center_id`= '$listing_id' ");
                $gallery_array = array();
                $gallery_count = $gallery_query->num_rows();
                if ($gallery_count > 0) {
                    foreach ($gallery_query->result_array() as $rows) {
                        $media_name = $rows['media_name'];
                        $type = $rows['type'];
                        $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $type . '/' . $media_name;

                        $gallery_array[] = array(
                            "image" => $gallery,
                            "type" => $type
                        );
                    }
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

                $what_we_offer_array = '';
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    $what_we_offer_array[] = $what_we_offer;
                }

                $facilities_array = array();
                $facilities = explode(',', $branch_facilities);
                foreach ($facilities as $facilities) {
                    $facilities_array[] = $facilities;
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $package_list = array();
                $package_query = $this->db->query("SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND categ_id='$category_id' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id = $package_row['id'];
                        $package_name = $package_row['package_name'];
                        $package_details = $package_row['package_details'];
                        $price = $package_row['price'];
                        $image = $package_row['image'];
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $package_list[] = array(
                            'package_id' => $package_id,
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price,
                            'image' => $image
                        );
                    }
                } else {
                    $package_list = array();
                }

                $booking_slot_array = array();
                if ($is_free_trail == '1') {
                    $from_trail_times = $this->check_time_format($from_trail_time);
                    $to_trail_times = $this->check_time_format($to_trail_time);
                    $time_difference = 60;
                    for ($i = strtotime($from_trail_times); $i <= strtotime($to_trail_times); $i = $i + $time_difference * 60) {
                        $booking_slot_array[] = array(
                            'time' => date("h:i A", $i)
                        );
                    }
                }

                $is_order_query = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category_id' AND user_id='$user_id' AND package_id='100'");
                $is_order_count = $is_order_query->num_rows();

                if ($is_order_count > 0) {
                    $is_trial_order = 1;
                } else {
                    $is_trial_order = 0;
                }

                $profile_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();
                $reviews = $this->db->select('id')->from('fitness_center_review')->where('branch_id', $branch_id)->get()->num_rows();

                $resultpost[] = array(
                    'branch_id' => $branch_id,
                    'lat' => $lat,
                    'lng' => $lng,
                    'category_id' => $category_id,
                    'listing_id' => $listing_id,
                    'listing_type' => $listing_type,
                    'about_us' => $about_branch,
                    'branch_name' => $branch_name,
                    'address' => $branch_address,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact' => $branch_phone,
                    'email' => $branch_email,
                    'image' => $branch_image,
                    'is_free_trail' => $is_free_trail,
                    'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                    'is_trial_order' => $is_trial_order,
                    'profile_views' => $profile_views
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function fitness_center_details($user_id, $listing_id) {
        
       /// echo"SELECT fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number FROM `fitness_center` as fc LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) WHERE fc.user_id='$listing_id'";
        
        $query = $this->db->query("SELECT fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number FROM `fitness_center` as fc LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) WHERE fc.user_id='$listing_id'");
        //$query = $this->db->query("SELECT `user_id`, `center_name`, `contact`, `email`, `image` FROM `fitness_center` LEFT JOIN enquiry_number as en ON(en.co) WHERE user_id='$listing_id'");
        $count_main = $query->num_rows();
        if ($count_main > 0) {
            $list = $query->row_array();

            $center_name = $list['center_name'];
            $main_contact = $list['contact'];
            $enquiry_number = $list['enquiry_number'];
            $main_email = $list['email'];
            $main_image = $list['image'];
            $listing_type = '6';
            $total_rating = '4.5';
            $total_review = '0';
            $total_profile_views = '0';

            if ($main_image != '') {
                $main_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $main_image;
            } else {
                $main_image = '';
            }
            $query_fitness_tr = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM fitness_center_review WHERE listing_id='$listing_id'");
            $row_fitness_tr = $query_fitness_tr->row_array();
            $total_rating = $row_fitness_tr['total_rating'];
            if ($total_rating === NULL || $total_rating === '') {
                $total_rating = '0';
            }

            $total_review = $this->db->select('id')->from('fitness_center_review')->where('listing_id', $listing_id)->get()->num_rows();
            $total_profile_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();

            $main_followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
            $main_following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
            $main_is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

            if ($main_is_follow > 0) {
                $main_is_follow = 'Yes';
            } else {
                $main_is_follow = 'No';
            }


            $query_branch = $this->db->query("SELECT fcb.id, fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category AS category_id,business_category.category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time, fcb.to_trail_time FROM `fitness_center_branch` AS fcb LEFT JOIN business_category ON fcb.branch_business_category= business_category.id  WHERE user_id='$listing_id'");
            $count = $query_branch->num_rows();
            $other_branch = array();
            if ($count > 0) {
                foreach ($query_branch->result_array() as $row) {
                    $branch_id = $row['id'];
                    $category_ids = $row['category_id'];
                    $category_id = explode(",", $category_ids)[0];
                    $category = $row['category'];
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $branch_name = $row['branch_name'];
                    $branch_image = $row['branch_image'];
                    $branch_phone = $row['branch_phone'];
                    $branch_email = $row['branch_email'];
                    $about_branch = $row['about_branch'];
                    $branch_offer = $row['branch_offer'];
                    $branch_facilities = $row['branch_facilities'];
                    $branch_address = $row['branch_address'];
                    $opening_hours = $row['opening_hours'];
                    $pincode = $row['pincode'];
                    $state = $row['state'];
                    $city = $row['city'];
                    $is_free_trail = $row['is_free_trail'];
                    $from_trail_time = $row['from_trail_time'];
                    $to_trail_time = $row['to_trail_time'];
                    $listing_id = $row['user_id'];
                    $listing_type = '6';
                    $rating = '4.5';
                    $reviews = '0';
                    $profile_views = '0';
                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    $gallery_query = $this->db->query("SELECT `id`,`media_name`, `type` FROM `fitness_gallery` WHERE `branch_id`='$branch_id' AND `fitness_center_id`= '$listing_id' ");
                    $gallery_array = array();
                    $gallery_count = $gallery_query->num_rows();
                    if ($gallery_count > 0) {
                        foreach ($gallery_query->result_array() as $rows) {
                            $media_name = $rows['media_name'];
                            $type = $rows['type'];
                            $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $type . '/' . $media_name;

                            $gallery_array[] = array(
                                "image" => $gallery,
                                "type" => $type
                            );
                        }
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



                    $what_we_offer_array = '';
                    $what_we_offer = explode(',', $branch_offer);
                    foreach ($what_we_offer as $what_we_offer) {
                        $what_we_offer_array[] = $what_we_offer;
                    }

                    $facilities_array = array();
                    $facilities = explode(',', $branch_facilities);
                    foreach ($facilities as $facilities) {
                        $facilities_array[] = $facilities;
                    }

                    $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                    $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                    $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

                    if ($is_follow > 0) {
                        $is_follow = 'Yes';
                    } else {
                        $is_follow = 'No';
                    }



                    $package_list = array();
                    $package_query = $this->db->query("SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND categ_id='$category_id' order by id asc");
                    $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id = $package_row['id'];
                            $package_name = $package_row['package_name'];
                            $package_details = $package_row['package_details'];
                            $price = $package_row['price'];
                            $image = $package_row['image'];
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                            $package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'image' => $image
                            );
                        }
                    } else {
                        $package_list = array();
                    }

                    $booking_slot_array = array();
                    if ($is_free_trail == '1') {
                        $from_trail_times = $this->check_time_format($from_trail_time);
                        $to_trail_times = $this->check_time_format($to_trail_time);
                        $time_difference = 60;
                        for ($i = strtotime($from_trail_times); $i <= strtotime($to_trail_times); $i = $i + $time_difference * 60) {
                            $booking_slot_array[] = array(
                                'time' => date("h:i A", $i)
                            );
                        }
                    }

                    $is_order_query = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category_id' AND user_id='$user_id' AND package_id='100'");
                    $is_order_count = $is_order_query->num_rows();

                    if ($is_order_count > 0) {
                        $is_trial_order = 1;
                    } else {
                        $is_trial_order = 0;
                    }
                    $profile_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();
                    $reviews = $this->db->select('id')->from('fitness_center_review')->where('branch_id', $branch_id)->get()->num_rows();

                    $other_branch[] = array(
                        'branch_id' => $branch_id,
                        'lat' => $lat,
                        'lng' => $lng,
                        'category_id' => $category_id,
                        'category' => $category,
                        'listing_id' => $listing_id,
                        'listing_type' => $listing_type,
                        'about_us' => $about_branch,
                        'branch_name' => $branch_name,
                        'address' => $branch_address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact' => $branch_phone,
                        'enquiry_number' => $enquiry_number,
                        'exotel_no' => '02233721563',
                        'email' => $branch_email,
                        'image' => $branch_image,
                        'is_free_trail' => $is_free_trail,
                        'booking_slot_array' => $booking_slot_array,
                        'gallery' => $gallery_array,
                        'what_we_offer' => $what_we_offer_array,
                        'facilities' => $facilities_array,
                        'package_list' => $package_list,
                        'opening_day' => $final_Day,
                        'rating' => $rating,
                        'review' => $reviews,
                        'followers' => $followers,
                        'following' => $following,
                        'is_follow' => $is_follow,
                        'is_trial_order' => $is_trial_order,
                        'profile_views' => $profile_views
                    );
                }

                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "center_name" => $center_name,
                    "main_contact" => $main_contact,
                    "main_email" => $main_email,
                    "main_image" => $main_image,
                    "total_rating" => $total_rating,
                    "total_review" => $total_review,
                    "total_profile_views" => $total_profile_views,
                    "other_branch" => $other_branch
                );
            } else {
                $other_branch = array();
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "center_name" => $center_name,
                    "main_contact" => $main_contact,
                    "main_email" => $main_email,
                    "main_image" => $main_image,
                    "total_rating" => $total_rating,
                    "total_review" => $total_review,
                    "total_profile_views" => $total_profile_views,
                    "other_branch" => $other_branch
                );
            }
        } else {
            $resultpost = array(
                "status" => 200,
                "message" => "success",
                "other_branch" => array()
            );
        }

        return $resultpost;
    }
    
    
     public function fitness_center_details_v2($user_id, $listing_id,$branch_id) {
        
       /// echo"SELECT fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number FROM `fitness_center` as fc LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) WHERE fc.user_id='$listing_id'";
        
        $query = $this->db->query("SELECT fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number FROM `fitness_center` as fc LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) WHERE fc.user_id='$listing_id'");
        //$query = $this->db->query("SELECT `user_id`, `center_name`, `contact`, `email`, `image` FROM `fitness_center` LEFT JOIN enquiry_number as en ON(en.co) WHERE user_id='$listing_id'");
        $count_main = $query->num_rows();
        if ($count_main > 0) {
            $list = $query->row_array();

            $center_name = $list['center_name'];
            $main_contact = $list['contact'];
            $enquiry_number = $list['enquiry_number'];
            $main_email = $list['email'];
            $main_image = $list['image'];
            $listing_type = '6';
            $total_rating = '4.5';
            $total_review = '0';
            $total_profile_views = '0';

            if ($main_image != '') {
                $main_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $main_image;
            } else {
                $main_image = '';
            }
            $query_fitness_tr = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM fitness_center_review WHERE listing_id='$listing_id'");
            $row_fitness_tr = $query_fitness_tr->row_array();
            $total_rating = $row_fitness_tr['total_rating'];
            if ($total_rating === NULL || $total_rating === '') {
                $total_rating = '0';
            }

            $total_review = $this->db->select('id')->from('fitness_center_review')->where('listing_id', $listing_id)->get()->num_rows();
            $total_profile_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();

            $main_followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
            $main_following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
            $main_is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

            if ($main_is_follow > 0) {
                $main_is_follow = 'Yes';
            } else {
                $main_is_follow = 'No';
            }

            if($branch_id=='0')
            {
             $query_branch = $this->db->query("SELECT fcb.id, fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category AS category_id,business_category.category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time, fcb.to_trail_time FROM `fitness_center_branch` AS fcb LEFT JOIN business_category ON fcb.branch_business_category= business_category.id  WHERE fcb.user_id='$listing_id'");
            }
            else
            {
            $query_branch = $this->db->query("SELECT fcb.id, fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category AS category_id,business_category.category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time, fcb.to_trail_time FROM `fitness_center_branch` AS fcb LEFT JOIN business_category ON fcb.branch_business_category= business_category.id  WHERE fcb.user_id='$listing_id' and fcb.id='$branch_id'");
            }
            $count = $query_branch->num_rows();
            $other_branch = array();
            if ($count > 0) {
                foreach ($query_branch->result_array() as $row) {
                    $branch_id = $row['id'];
                   
                    $category_id = $row['category_id'];
                    $cat = explode(',',$category_id);
                    $category = array();
                    for($i=0;$i<count($cat);$i++)
                    {
                        if(!empty($cat[$i]))
                        {
                        $sql = $this->db->select('category')->from('business_category')->where('id', $cat[$i])->get()->row()->category;
                        $category[]=$sql;
                        }
                    }
                   
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $branch_name = $row['branch_name'];
                    $branch_image = $row['branch_image'];
                    $branch_phone = $row['branch_phone'];
                    $branch_email = $row['branch_email'];
                    $about_branch = $row['about_branch'];
                    $branch_offer = $row['branch_offer'];
                    $branch_facilities = $row['branch_facilities'];
                    $branch_address = $row['branch_address'];
                    $opening_hours = $row['opening_hours'];
                    $pincode = $row['pincode'];
                    $state = $row['state'];
                    $city = $row['city'];
                    $is_free_trail = $row['is_free_trail'];
                    $from_trail_time = $row['from_trail_time'];
                    $to_trail_time = $row['to_trail_time'];
                    $listing_id = $row['user_id'];
                    $listing_type = '6';
                    $rating = '4.5';
                    $reviews = '0';
                    $profile_views = '0';
                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    $gallery_query = $this->db->query("SELECT `id`,`media_name`, `type` FROM `fitness_gallery` WHERE `branch_id`='$branch_id' AND `fitness_center_id`= '$listing_id' ");
                    $gallery_array = array();
                    $gallery_count = $gallery_query->num_rows();
                    if ($gallery_count > 0) {
                        foreach ($gallery_query->result_array() as $rows) {
                            $media_name = $rows['media_name'];
                            $type = $rows['type'];
                            $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $type . '/' . $media_name;

                            $gallery_array[] = array(
                                "image" => $gallery,
                                "type" => $type
                            );
                        }
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



                    $what_we_offer_array = '';
                    $what_we_offer = explode(',', $branch_offer);
                    foreach ($what_we_offer as $what_we_offer) {
                        $what_we_offer_array[] = $what_we_offer;
                    }

                    $facilities_array = array();
                    $facilities = explode(',', $branch_facilities);
                    foreach ($facilities as $facilities) {
                        $facilities_array[] = $facilities;
                    }

                    $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                    $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                    $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

                    if ($is_follow > 0) {
                        $is_follow = 'Yes';
                    } else {
                        $is_follow = 'No';
                    }



                    $package_list = array();
                    $package_query = $this->db->query("SELECT id,package_name,package_details,price,image,discount FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND FIND_IN_SET('" . $branch_id . "', branch) AND categ_id='$category_id' order by id asc");
                    $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id = $package_row['id'];
                            $package_name = $package_row['package_name'];
                            $package_details = $package_row['package_details'];
                            $price = $package_row['price'];
                            $image = $package_row['image'];
                            $user_discount = $package_row['discount'];
                            if(empty($user_discount))
                            {
                                $user_discount="0";
                            }
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                            $package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'user_discount' => $user_discount,
                                'image' => $image
                            );
                        }
                    } else {
                        $package_list = array();
                    }

                    $booking_slot_array = array();
                    if ($is_free_trail == '1') {
                        $from_trail_times = $this->check_time_format($from_trail_time);
                        $to_trail_times = $this->check_time_format($to_trail_time);
                        $time_difference = 60;
                        for ($i = strtotime($from_trail_times); $i <= strtotime($to_trail_times); $i = $i + $time_difference * 60) {
                            $booking_slot_array[] = array(
                                'time' => date("h:i A", $i)
                            );
                        }
                    }

                    $is_order_query = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category_id' AND user_id='$user_id' AND package_id='100'");
                    $is_order_count = $is_order_query->num_rows();

                    if ($is_order_count > 0) {
                        $is_trial_order = 1;
                    } else {
                        $is_trial_order = 0;
                    }
                    $profile_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();
                    $reviews = $this->db->select('id')->from('fitness_center_review')->where('branch_id', $branch_id)->get()->num_rows();

            $sub_array = array();
            $personal_trainer = $this->db->select('*')->from('personal_trainers')->where('user_id', $listing_id)->get();
            $count = $personal_trainer->num_rows();
            if ($count > 0) 
            {
                foreach ($personal_trainer->result_array() as $row1) 
                {
                    $id1 = $row1['id'];
                    $category_id1 = $row1['category_id'];
                    $cat1 = explode(',',$category_id1);
                    $category_name1 = array();
                    for($i=0;$i<count($cat1);$i++)
                    {
                        if(!empty($cat1[$i]))
                        {
                        $sql = $this->db->select('category')->from('business_category')->where('id', $cat1[$i])->get()->row()->category;
                        $category_name1[]=$sql;
                        }
                    }
                    
                    $manager_name1 = $row1['manager_name'];
                    $address1 = $row1['address'];
                    $pincode1 = $row1['pincode'];
                    $contact1 = $row1['contact'];
                    $city1 = $row1['city'];
                    $state1 = $row1['state'];
                    $email1 = $row1['email'];
                    $qualifications1 = $row1['qualifications'];
                    $trainer_opening_hours1 = $row1['opening_hours'];
                    $experience1 = $row1['experience'];
                    $fitness_trainer_pic1 = $row1['fitness_trainer_pic'];
                    $gender1 = $row1['gender'];
                   
                      $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                  
                      $session1[] = array(
                                'id'=>'1',
                                'package_name' => 'zumba',
                                'package_details' => 'Details about the package',
                                'price'=>'1,500',
                                'image'=> 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image
                            );
                         
                           $session1[] = array(
                                'id'=>'2',
                                'package_name' => 'zumba',
                                'package_details' => 'Details about the package1',
                                'price'=>'1,500',
                                'image'=> 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image
                            );
                         
                           
                         $final_session1=  array_merge($session1,$session2);
                         
                         
                     $branch_final_Day1      = array();
                        $day_array_list_branch1 = explode('|', $trainer_opening_hours1);
                        if (count($day_array_list_branch1) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch1); $i++) {
                                $day_list1 = explode('>', $day_array_list_branch1[$i]);
                                for ($j = 0; $j < count($day_list1); $j++) {
                                    $day_time_list1 = explode('-', $day_list1[$j]);
                                    for ($k = 1; $k < count($day_time_list1); $k++) {
                                        $time_list11 = explode(',', $day_time_list1[0]);
                                        $time_list21 = explode(',', $day_time_list1[1]);
                                        $time1       = array();
                                        $open_close1 = array();
                                        for ($l = 0; $l < count($time_list11); $l++) {
                                            $time_check1        = $time_list11[$l] . '-' . $time_list21[$l];
                                            $time1[]            = str_replace('close-close', 'close', $time_check1);
                                            $system_start_time1 = date("H.i", strtotime($time_list11[$l]));
                                            $system_end_time1   = date("H.i", strtotime($time_list21[$l]));
                                            $current_time1      = date('H.i');
                                            if ($current_time1 > $system_start_time1 && $current_time1 < $system_end_time1) {
                                                $open_close1[] = 'open';
                                            } else {
                                                $open_close1[] = 'close';
                                            }
                                        }
                                    }
                                }
                                $branch_final_Day1[] = array(
                                    'day' => $day_list1[0],
                                    'time' => $time1,
                                    'status' => $open_close1
                                );
                            }
                        } else {
                            $branch_final_Day1[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                    $sub_array1[] = array('id'=> $id1,
                                       'category' => $category_name1,
                                       'manager_name' => $manager_name1,
                                       'address' => $address1,
                                       'pincode' => $pincode1,
                                       'city' => $city1,
                                       'state' => $state1,
                                       'email' => $email1,
                                       'contact' => $contact1,
                                       'qualifications' => $qualifications1,
                                       'experience' => $experience1,
                                       'fitness_trainer_pic' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$fitness_trainer_pic1,
                                       'gender' => $gender1,
                                        'rating' => $rating,
                                       'package_trainer' => $final_session1
                                     );
                                       //'opening_hours' => $branch_final_Day);
                    
                }
                    
            }
            else {
               $sub_array1=array();
            }



                    $other_branch[] = array(
                        'branch_id' => $branch_id,
                        'lat' => $lat,
                        'lng' => $lng,
                        'category_id' => $category_id,
                        'category' => $category,
                        'listing_id' => $listing_id,
                        'listing_type' => $listing_type,
                        'about_us' => $about_branch,
                        'branch_name' => $branch_name,
                        'address' => $branch_address,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'contact' => $branch_phone,
                        'enquiry_number' => $enquiry_number,
                        'exotel_no' => '02233721563',
                        'email' => $branch_email,
                        'image' => $branch_image,
                        'is_free_trail' => $is_free_trail,
                        'booking_slot_array' => $booking_slot_array,
                        'gallery' => $gallery_array,
                        'what_we_offer' => $what_we_offer_array,
                        'facilities' => $facilities_array,
                        'package_list' => $package_list,
                        'trainer_list' => $sub_array1,
                        'opening_day' => $final_Day,
                        'rating' => $rating,
                        'review' => $reviews,
                        'followers' => $followers,
                        'following' => $following,
                        'is_follow' => $is_follow,
                        'is_trial_order' => $is_trial_order,
                        'profile_views' => $profile_views
                    );
                }
                
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "center_name" => $center_name,
                    "main_contact" => $main_contact,
                    "main_email" => $main_email,
                    "main_image" => $main_image,
                    "total_rating" => $total_rating,
                    "total_review" => $total_review,
                    "total_profile_views" => $total_profile_views,
                    "other_branch" => $other_branch
                );
            } else {
                $other_branch = array();
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "center_name" => $center_name,
                    "main_contact" => $main_contact,
                    "main_email" => $main_email,
                    "main_image" => $main_image,
                    "total_rating" => $total_rating,
                    "total_review" => $total_review,
                    "total_profile_views" => $total_profile_views,
                    "other_branch" => $other_branch
                );
            }
        } else {
            $resultpost = array(
                "status" => 200,
                "message" => "success",
                "other_branch" => array()
            );
        }

        return $resultpost;
    }

    public function add_bookings($user_id, $listing_id, $package_id, $branch_id, $category_id, $trail_booking_date, $trail_booking_time, $joining_date, $user_name, $user_mobile, $user_email, $user_gender,$user_age,$user_height,$user_weight,$user_diet_preference,$user_exercise_level,$user_medical_condition,$user_ever_went_gym) {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id = date("YmdHis");
        $date = date('Y-m-d H:i:s');
         if($package_id ==100)
        {
            $status = "5";
        }
        else
        {
            $status = "1";
        }
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'package_id' => $package_id,
            'listing_id' => $listing_id,
            'user_id'    => $user_id,
            'branch_id'  => $branch_id,
            'vendor_id'  => '6',
            'category_id' => $category_id,
            'trail_booking_date' => $trail_booking_date,
            'trail_booking_time' => $trail_booking_time,
            'joining_date' => $joining_date,
            'user_name' => $user_name,
            'user_mobile' => $user_mobile,
            'user_email' => $user_email,
            'user_gender' => $user_gender,
            'booking_date' => $date,
            'status' => $status
        );
       
       $insert_user_data = $this->db->where('id', $user_id)->update('users', array(
                    'height' => $user_height,
                    'weight' => $user_weight,
                     'age'   => $user_age,
                     'diet_fitness' => $user_diet_preference,
                     'exercise_level'=> $user_exercise_level,
                     'health_condition'=> $user_medical_condition
                     
                     
                ));

        $insert = $this->db->insert('booking_master', $booking_master_array);
        $order_id = $this->db->insert_id();
        
          
        
        

        $query_branch = $this->db->query("SELECT `branch_name`,`branch_phone` FROM fitness_center_branch WHERE id='$branch_id'");
        $row_branch = $query_branch->row_array();
        $branch_name = $row_branch['branch_name'];
        $branch_phone = $row_branch['branch_phone'];
        if ($package_id == '100') {
            //free trial
            $date_ = $trail_booking_date . ' ' . $trail_booking_time;
        } else {
            $date_ = $joining_date;
        }

        //web notification starts
        $fitness_booking_notifications = array(
            'listing_id' => $listing_id,
            'order_id' => $order_id,
            'title' => 'New Booking',
            'msg' => 'You Have Received a New booking',
            'image' => '',
            'notification_type' => 'fitness_bookings',
            'order_status' => '',
            'order_date' => $date,
            'invoice_no' => $booking_id
        );
        $this->db->insert('pharmacy_notifications', $fitness_booking_notifications);
        //web notification ends   

        if ($insert) {
            $message = 'Dear customer, your booking is confirmed at Fitness Center ' . $branch_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.';
            $post_data = array('From' => '02233721563', 'To' => $user_mobile, 'Body' => $message);
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

            $type_of_order = 'booking';
            $login_url = 'https://fitness.medicalwale.com';
            $message2 = 'You have received ' . $type_of_order . ', kindly login to ' . $login_url . '. Thank you.';
            $post_data2 = array('From' => '02233721563', 'To' => $branch_phone, 'Body' => $message2);
            $exotel_sid2 = "aegishealthsolutions";
            $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
            $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
            $ch2 = curl_init();
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

        return array(
            'status' => 200,
            'message' => 'success',
            'booking_id' => $booking_id
        );
    }

    public function add_review($user_id, $listing_id, $rating, $review, $service, $branch_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'branch_id' => $branch_id,
            'date' => $date
        );
        $this->db->insert('fitness_center_review', $review_array);
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
           $this->db->update('fitness_center_review',$review_array);
        //$this->db->insert('fitness_center_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function review_list($user_id, $listing_id, $branch_id) {

        function get_time_difference_php($created_time) {
            date_default_timezone_set('Asia/Calcutta');
            $str = strtotime($created_time);
            $today = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years = 60 * 60 * 24 * 365;
            $months = 60 * 60 * 24 * 30;
            $days = 60 * 60 * 24;
            $hours = 60 * 60;
            $minutes = 60;
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

        $resultpost = array();
        $review_count = $this->db->select('id')->from('fitness_center_review')->where('listing_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            //echo "SELECT fitness_center_review.id,fitness_center_review.user_id,fitness_center_review.listing_id,fitness_center_review.rating,fitness_center_review.review, fitness_center_review.service,fitness_center_review.date as review_date,users.id as user_id,users.name as firstname FROM `fitness_center_review` INNER JOIN `users` ON fitness_center_review.user_id=users.id WHERE fitness_center_review.listing_id='$listing_id' AND fitness_center_review.branch_id='$branch_id' order by fitness_center_review.id desc";
            $query = $this->db->query("SELECT fitness_center_review.id,fitness_center_review.user_id,fitness_center_review.listing_id,fitness_center_review.rating,fitness_center_review.review, fitness_center_review.service,fitness_center_review.date as review_date,users.id as user_id,users.name as firstname FROM `fitness_center_review` INNER JOIN `users` ON fitness_center_review.user_id=users.id WHERE fitness_center_review.listing_id='$listing_id' AND fitness_center_review.branch_id='$branch_id' order by fitness_center_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $user_id = $row['user_id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                $review1 = str_replace('~','',$review);
                if ($id > '1' ) {
                    
                     if(!empty($review1))
                     {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                     }
                 } else {
                     if (base64_encode(base64_decode($review)) === $review) {
                     echo $review = base64_decode($review);
                     }
                 }
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('fitness_center_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('fitness_center_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('fitness_center_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

                $resultpost[] = array(
                    'id' => $id,
                    'user_id' => $user_id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $post_count
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

public function review_with_comment($user_id, $listing_id, $branch_id) {

        function get_time_difference_php($created_time) {
            date_default_timezone_set('Asia/Calcutta');
            $str = strtotime($created_time);
            $today = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years = 60 * 60 * 24 * 365;
            $months = 60 * 60 * 24 * 30;
            $days = 60 * 60 * 24;
            $hours = 60 * 60;
            $minutes = 60;
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

        $resultpost = array();
        $review_count = $this->db->select('id')->from('fitness_center_review')->where('listing_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            //echo "SELECT fitness_center_review.id,fitness_center_review.user_id,fitness_center_review.listing_id,fitness_center_review.rating,fitness_center_review.review, fitness_center_review.service,fitness_center_review.date as review_date,users.id as user_id,users.name as firstname FROM `fitness_center_review` INNER JOIN `users` ON fitness_center_review.user_id=users.id WHERE fitness_center_review.listing_id='$listing_id' AND fitness_center_review.branch_id='$branch_id' order by fitness_center_review.id desc";
            $query = $this->db->query("SELECT fitness_center_review.id,fitness_center_review.user_id,fitness_center_review.listing_id,fitness_center_review.rating,fitness_center_review.review, fitness_center_review.service,fitness_center_review.date as review_date,users.id as user_id,users.name as firstname FROM `fitness_center_review` INNER JOIN `users` ON fitness_center_review.user_id=users.id WHERE fitness_center_review.listing_id='$listing_id' AND fitness_center_review.branch_id='$branch_id' order by fitness_center_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $user_id = $row['user_id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                
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
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('fitness_center_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('fitness_center_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('fitness_center_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();



 $review_list_count = $this->db->select('id')->from('fitness_center_review_comment')->where('post_id', $id)->get()->num_rows();
        if ($review_list_count) {
            $resultcomment = array();
            $querycomment = $this->db->query("SELECT fitness_center_review_comment.id,fitness_center_review_comment.post_id,fitness_center_review_comment.comment as comment,fitness_center_review_comment.date,users.name,fitness_center_review_comment.user_id as post_user_id FROM fitness_center_review_comment INNER JOIN users on users.id=fitness_center_review_comment.user_id WHERE fitness_center_review_comment.post_id='$id' order by fitness_center_review_comment.id asc");

            foreach ($querycomment->result_array() as $rowc) {
                $comment_id = $rowc['id'];
                $post_id = $rowc['post_id'];
                $comment = $rowc['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                
                $usernamec = $rowc['name'];
                $date = $rowc['date'];
                $post_user_id = $rowc['post_user_id'];

                $like_countc = $this->db->select('id')->from('fitness_center_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $like_yes_noc = $this->db->select('id')->from('fitness_center_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimagec = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimagec = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date = get_time_difference_php($date);
                $resultcomment[] = array(
                    'id' => $comment_id,
                    'username' => $usernamec,
                    'userimage' => $userimagec,
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


                $resultpost[] = array(
                    'id' => $id,
                    'user_id' => $user_id,
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


    public function review_like($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `fitness_center_review_likes` WHERE post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `fitness_center_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from `fitness_center_review_likes` WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $fitness_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('fitness_center_review_likes', $fitness_review_likes);
            $like_query = $this->db->query("SELECT id from fitness_center_review_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $fitness_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('fitness_center_review_comment', $fitness_review_comment);
        $fitness_review_comment_query = $this->db->query("SELECT id from fitness_center_review_comment where post_id='$post_id'");
        $total_comment = $fitness_review_comment_query->num_rows();
        return array(
            'status' => 200,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    public function review_comment_like($user_id, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from fitness_center_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `fitness_center_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from fitness_center_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $fitness_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('fitness_center_review_comment_like', $fitness_review_comment_like);
            $comment_query = $this->db->query("SELECT id from fitness_center_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    public function review_comment_list($user_id, $post_id) {

        function get_time_difference_php($created_time) {
            date_default_timezone_set('Asia/Calcutta');
            $str = strtotime($created_time);
            $today = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years = 60 * 60 * 24 * 365;
            $months = 60 * 60 * 24 * 30;
            $days = 60 * 60 * 24;
            $hours = 60 * 60;
            $minutes = 60;
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

        $review_list_count = $this->db->select('id')->from('fitness_center_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT fitness_center_review_comment.id,fitness_center_review_comment.post_id,fitness_center_review_comment.comment as comment,fitness_center_review_comment.date,users.name,fitness_center_review_comment.user_id as post_user_id FROM fitness_center_review_comment INNER JOIN users on users.id=fitness_center_review_comment.user_id WHERE fitness_center_review_comment.post_id='$post_id' order by fitness_center_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '2') {
                    $comment_decrypt = $this->decrypt($comment);
                    $comment_encrypt = $this->encrypt($comment_decrypt);
                    if ($comment_encrypt == $comment) {
                        $comment = $comment_decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($comment)) === $comment) {
                        $comment = base64_decode($comment);
                    }
                }
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('fitness_center_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('fitness_center_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date = get_time_difference_php($date);
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
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function fitness_views($user_id, $listing_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $fitness_views_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'date' => $date
        );
        $this->db->insert('fitness_views', $fitness_views_array);

        $fitness_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'fitness_views' => $fitness_views
        );
    }
    // scheduled for next release 
    
    public function Fill_fitness_form_answers($user_id,$listing_id,$branch_id,$package_id,$id,$answer1,$answer2) {
       
       $d1 = array('user_id' =>$user_id,
                   'listing_id' => $listing_id,
                   'branch_id' => $branch_id,
                   'package_id' => $package_id ,
                   'question_id' => $id,
                   'answer1' => $answer1,
                   'answer2' => $answer2,
                   'datetime'=> date('Y-m-d H:i:s')
                   );
        $query = $this->db->insert("fitness_booking_details1",$d1);
      
    }
    public function delete_fitness_form_answers($user_id,$listing_id,$branch_id,$package_id) {
        $this->db->where('user_id',$user_id);
        $this->db->where('listing_id',$listing_id);
        $this->db->where('branch_id',$branch_id);
        $this->db->where('package_id',$package_id);
        $res = $this->db->delete("fitness_booking_details1"); 
       
    }
     public function fitness_form_questions($user_id) {
        $array =array();
         $book = $this->db->query("SELECT  * FROM fitness_booking_details1  WHERE user_id='$user_id'");
            $book_result = $book->row_array();
        if(!empty($book_result))  {    
        $query = $this->db->query("SELECT  * FROM fitness_form_quetions  WHERE is_active='0'");
        $result1 = $query->result_array();
      //  print_r($result1);die;
        foreach($result1 as $result)
        {
            $q_id = $result['id'];
            $query1 = $this->db->query("SELECT  * FROM fitness_booking_details1  WHERE user_id='$user_id' AND question_id='$q_id' order by id DESC");
            $results = $query1->row_array();
            if(!empty($results))
            {
                $array[] = array(
                                'id' => $result['id'],
                                'questions' => $result['questions'],
                                'subquestions' => $result['subquestions'],
                                'questionsans' => $results['answer1'],
                                'subquestionsans' => $results['answer2'],
                                'is_active' => $result['is_active'],
                                'datetime' => $result['datetime']
                );
            }
            else
            {
                $array[] = array('id' => $result['id'],
                                'questions' => $result['questions'],
                                'subquestions' => $result['subquestions'],
                                'questionsans' => "",
                                'subquestionsans' => "",
                                'is_active' => $result['is_active'],
                                'datetime' => $result['datetime']
                );
            }
        }
        }else
        {
             $query2 = $this->db->query("SELECT  * FROM fitness_form_quetions  WHERE is_active='0'");
             $result12 = $query2->result_array();
              foreach($result12 as $result)
            {
                $array[] = array('id' => $result['id'],
                                'questions' => $result['questions'],
                                'subquestions' => $result['subquestions'],
                                'questionsans' => "",
                                'subquestionsans' => "",
                                'is_active' => $result['is_active'],
                                'datetime' => $result['datetime']
                );
            }
        }
        return $array;
    }
    public function user_payment_approval($status, $booking_id, $user_id)
    {
        
        $this->db->where('booking_id', $booking_id)->update('booking_master', array(
                    'status' => $status
                ));
                if($this->db->affected_rows()>0)
                {
                    if($status == "3")
                    {
                        $sql="SELECT * FROM booking_master WHERE booking_id='".$booking_id."'";
                        $booking_id = $this->db->query($sql)->row()->booking_id;
                        $booking_date = $this->db->query($sql)->row()->booking_date;
                        $branch_id = $this->db->query($sql)->row()->branch_id;
                        $listing_id = $this->db->query($sql)->row()->listing_id;
                        $user_id = $this->db->query($sql)->row()->user_id;
                        $trail_booking_date = $this->db->query($sql)->row()->trail_booking_date;
                        $trail_booking_time = $this->db->query($sql)->row()->trail_booking_time;
                        $joining_date = $this->db->query($sql)->row()->joining_date;
                        $package_id = $this->db->query($sql)->row()->package_id;
                        $status = $this->db->query($sql)->row()->status;
                      
                        
                        $date_ = date('d-m-Y',strtotime($booking_date));
                        
                        if ($package_id == '100') {
                            $joining_date_ = $trail_booking_date . ' ' . $trail_booking_time;
    
                            $date_noti = date('j M Y | h:i A', strtotime($joining_date_));
                            $is_free_trial = 'Yes';
                        }
                        $date_noti = date('j M Y', strtotime($joining_date));


                        $query_branch = $this->db->query("SELECT `branch_name`,`branch_phone` FROM fitness_center_branch WHERE id='$branch_id'");
                        $row_branch = $query_branch->row_array();
                        $branch_name = $row_branch['branch_name'];
                        
                        $query_u = $this->db->query("SELECT `name` FROM users WHERE id='$user_id'");
                        $row_u = $query_u->row_array();
                        $user_name = $row_u['name'];
                        
                        $connect_type ="cancel_fitness_bookings";
                        $description1 ='Dear customer, your booking is Cancelled by ' . $branch_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.'; 
                        $description_web ='Dear customer, your booking is Cancelled by ' . $user_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.'; 
                        $this->notifyMethod_cancel($listing_id,$user_id, $description1, $date_, $booking_id, $connect_type, $branch_name, $date_noti, $status, $user_id,$user_name,$description_web);
                       
                    }
                     return array(
                    'status' => 200,
                    'message' => 'success'
                    );
                }
                else
                {
                    return array(
                    'status' => 400,
                    'message' => 'Booking data not found'
                    );
                }
    }
    public function notifyMethod_cancel($listing_id, $user_id, $description, $booking_date, $booking_id, $connect_type, $user_name, $date_noti, $status, $list_id,$user_name_web,$description_web)
    {
     
        $customer_token       = $this->db->query("SELECT name,token,agent,token_status,web_token FROM users WHERE id='$listing_id'");
        $customer_token_count = $customer_token->num_rows();
  
        if ($customer_token_count > 0) {
            $token_status = $customer_token->row_array();
           
            $usr_name     = $token_status['name'];
            $agent        = $token_status['agent'];
            $reg_id       = $token_status['token'];
            $web_token    = $token_status['web_token'];
            $img_url      = 'https://s3.amazonaws.com/medicalwale/images/img/fitnes.png';
            $tag          = 'text';
            $key_count    = '1';
            $title        = 'Cancelled an Fitness Booking';
            $msg          = $user_name . ' has Cancelled an Fitness Booking' ;
            $title_web    = 'Fitness Booking';
            $msg_web      = $description_web."\n".'Appointment Date : ' . $booking_date ;
            
            //added by zak for all notification therough services
                $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => $status,
                      'order_date' => $booking_date,
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $listing_id,
                      'booking_id'  => $booking_id,
                      'invoice_no' => "",
                      'user_id'  => $user_id,
                      'package_name' => "Free Trial",
                      'notification_type'  => 'cancel_fitness_bookings',
                      'notification_date'  => date('Y-m-d H:i:s')
                       
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
        //end
            
            
            $this->send_gcm_notify_cancel($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_id, $connect_type, $date_noti, $status, $list_id);
            
            // Web notification FCM function by ghanshyam parihar date:4 Feb 2019
            $click_action = 'https://fitness.medicalwale.com/Appointments/booking_appointment/'.$listing_id;
            // $click_action = 'https://vendor.sandbox.medicalwale.com/fitness/appointments/booking_details/'.$order_id;
            $this->send_gcm_web_notify($title_web, $msg_web, $web_token, $img_url, $tag, $agent, $connect_type,$click_action);
            // Web notification FCM function by ghanshyam parihar date:4 Feb 2019 Ends
        }
    }
    //send notification through firebase
    /* notification to send in the doctor app for appointment confirmation*/
    public function send_gcm_notify_cancel($title, $reg_id, $msg, $img_url, $tag, $agent, $date, $booking_id, $connect_type, $date_noti, $status, $list_id)
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
                    "notification_type" => 'cancel_fitness_bookings',
                    "notification_date" => $date,
                    "booking_date" => $date,
                    "joining_date" => $date_noti,
                    "booking_id" => $booking_id,
                    "type_of_connect" => $connect_type,
                    "status" => $status,
                    "package_name" => 'Free Trial',
                    "listing_id" => $list_id
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

    /*public function view_bookings_v30($user_id, $listing_id, $package_id, $branch_id, $category_id) {
       
        $query = $this->db->query("SELECT  u.name, u.phone, u.email, u.gender, u.age, u.height, u.weight, u.exercise_level, u.diet_fitness, u.health_condition, u.dob, u.blood_group, u.bmi, f.* FROM users as u LEFT JOIN fitness_booking_details as f ON f.user_id=u.id  WHERE u.id='$user_id'");
        $row_fitness = $query->row_array();
        $count = $query->num_rows();
        if ($count > 0) {
           

                $resultpost[] = array(
                    "user_id" => $user_id,
                	'listing_id' => $listing_id,
                	'package_id' =>$package_id,
                	'branch_id' => $branch_id,
                	'category_id' => $category_id,
                	'trail_booking_date' => "2018-07-25",
                	'trail_booking_time' => "06:00 AM",
                	'joining_date' => "2018-07-27",
                	'user_name' => $row_fitness['name'],
                	'user_mobile' => $row_fitness['phone'],
                	'user_email' => $row_fitness['email'],
                	'user_gender' => $row_fitness['gender'],
                	'age' => $row_fitness['age'],
                	'height' => $row_fitness['height'],
                	'weight' => $row_fitness['weight'],
                	'diet_preference' => $row_fitness['diet_fitness'],
                	'exercise_level' => $row_fitness['exercise_level'],
                	'medical_condition' => $row_fitness['health_condition'],
                	'dob' => $row_fitness['dob'],
                	'bmi' => $row_fitness['bmi'],
                	'blood_group' => $row_fitness['blood_group'], 
                	'parq1' => $row_fitness['parq1'],
                	'parq1_detail' => $row_fitness['parq1_detail'],
                	'parq2' => $row_fitness['parq2'],
                	'parq2_detail' => $row_fitness['parq2_detail'],
                	'parq3' => $row_fitness['parq3'],
                	'parq3_detail' => $row_fitness['parq3_detail'],
                	'parq4' => $row_fitness['parq4'],
                	'parq4_detail' => $row_fitness['parq4_detail'],
                	'parq5' => $row_fitness['parq5'],
                	'parq5_detail' => $row_fitness['parq5_detail'],
                	'parq6' => $row_fitness['parq6'],
                	'parq6_detail' => $row_fitness['parq6_detail'],
                	'parq7' => $row_fitness['parq7'],
                	'parq7_detail' => $row_fitness['parq7_detail'],
                	'parq8' => $row_fitness['parq8'],
                	'parq8_detail' => $row_fitness['parq8_detail'],
                	'parq9' => $row_fitness['parq9'],
                	'parq9_detail' => $row_fitness['parq9_detail'],
                	'parq10' => $row_fitness['parq10'],
                	'parq10_detail' => $row_fitness['parq10_detail'],
                	'parq11' => $row_fitness['parq11'],
                	'parq11_detail' => $row_fitness['parq11_detail'],
                	'accept' => $row_fitness['accept']
                );
            }
         else {
            $resultpost = array();
        }

        return $resultpost;
    } */
    
      public function view_bookings_v30($user_id, $listing_id, $package_id, $branch_id, $category_id) {
        $resultpost = array();
        $resultpost1 = array();
       
        $query = $this->db->query("SELECT  u.name, u.phone, u.email, u.gender, u.age, u.height, u.weight, u.exercise_level, u.diet_fitness, u.health_condition, u.dob, u.blood_group, u.bmi FROM users as u WHERE u.id='$user_id'");
        $row_fitness = $query->row_array();
        $count = $query->num_rows();
        if ($count > 0) {
                $resultpost[] = array(
                    "user_id" => $user_id,
                	'listing_id' => $listing_id,
                	'package_id' =>$package_id,
                	'branch_id' => $branch_id,
                	'category_id' => $category_id,
                	'trail_booking_date' => "2018-07-25",
                	'trail_booking_time' => "06:00 AM",
                	'joining_date' => "2018-07-27",
                	'user_name' => $row_fitness['name'],
                	'user_mobile' => $row_fitness['phone'],
                	'user_email' => $row_fitness['email'],
                	'user_gender' => $row_fitness['gender'],
                	'age' => $row_fitness['age'],
                	'height' => $row_fitness['height'],
                	'weight' => $row_fitness['weight'],
                	'diet_preference' => $row_fitness['diet_fitness'],
                	'exercise_level' => $row_fitness['exercise_level'],
                	'medical_condition' => $row_fitness['health_condition'],
                	'dob' => $row_fitness['dob'],
                	'bmi' => $row_fitness['bmi'],
                	'blood_group' => $row_fitness['blood_group'], 
                
                );
            }
          
            $query1 = $this->db->query("SELECT  f.*,q.questions,q.subquestions FROM fitness_booking_details1 as f LEFT JOIN fitness_form_quetions as q ON q.id=f.question_id WHERE f.user_id='$user_id' AND listing_id='$listing_id' AND package_id='$package_id' AND branch_id='$branch_id'");
             foreach ($query1->result_array() as $row) {
                  
                  $question = $row['questions'];
                  $subquestion = $row['subquestions'];
                  $answer1 = $row['answer1'];
                  $answer2 = $row['answer2'];
                  $resultpost1[] = array(
                    'question' => $question,
                    'subquestion' => $subquestion,
                   'answer1' => $answer1,
                    'answer2' => $answer2
                );
             }
            
        return array('status' => 200, 'message' => 'success', 'basic' => $resultpost, 'question' => $resultpost1);
    }
    
    public function add_bookings_v30($user_id, $listing_id, $package_id, $branch_id, $category_id, $trail_booking_date, $trail_booking_time, $joining_date, $user_name, $user_mobile, $user_email, $user_gender,$user_age,$user_height,$height_cm_ft,$user_weight,$user_diet_preference,$user_exercise_level,$user_medical_condition,$user_ever_went_gym,$user_bmi,$user_dob,$user_blood_group) {
      
     
        date_default_timezone_set('Asia/Kolkata');
        $booking_id = date("YmdHis");
        $date = date('Y-m-d H:i:s');
        if($package_id ==100)
        {
            $status = "5";
        }
        else
        {
            $status = "1";
        }
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'package_id' => $package_id,
            'listing_id' => $listing_id,
            'user_id'    => $user_id,
            'branch_id'  => $branch_id,
            'vendor_id'  => '6',
            'category_id' => $category_id,
            'trail_booking_date' => $trail_booking_date,
            'trail_booking_time' => $trail_booking_time,
            'joining_date' => $joining_date,
            'user_name' => $user_name,
            'user_mobile' => $user_mobile,
            'user_email' => $user_email,
            'user_gender' => $user_gender,
            'booking_date' => $date,
            'status' => $status
        );
       
       $insert_user_data = $this->db->where('id', $user_id)->update('users', array(
                    'height' => $user_height,
                    'height_cm_ft' => $height_cm_ft,
                    'weight' => $user_weight,
                    'age'   => $user_age,
                    'diet_fitness' => $user_diet_preference,
                    'exercise_level'=> $user_exercise_level,
                    'health_condition'=> $user_medical_condition,
                    'bmi'=> $user_bmi, 
                    'blood_group'=> $user_blood_group,
                    'dob'=> $user_dob
                ));
        
       // $this->db->insert('fitness_booking_details', $data);
        
        $insert = $this->db->insert('booking_master', $booking_master_array);
        $order_id = $this->db->insert_id();
        
    if($insert){
            
                // $data= $this->FitnesscenterModel->fitness_notification_confirm($booking_id);
                //  $message = 'Dear '.$data->name.', your booking has been confirm With  '.$data->center_name.'  Your '.'Booking ID is '.$data->booking_id.' '.'Thank you.';
                //  $to = $data->email;
                //  $subject = 'medicalwale Appointment List';
                //  $msg = $message;
                //  $this->Send_Email_Notification($to,$subject,$msg);
       
      
        }

        $query_branch = $this->db->query("SELECT `branch_name`,`branch_phone` FROM fitness_center_branch WHERE id='$branch_id'");
        $row_branch = $query_branch->row_array();
        $branch_name = $row_branch['branch_name'];
        $branch_phone = $row_branch['branch_phone'];
        $date_ = date('j M Y', strtotime($joining_date));
        if ($package_id == '100') {
            //free trial
            $date_ = $trail_booking_date . ' ' . $trail_booking_time;
            $date_noti = date('j M Y | h:i A', strtotime($date_));
            $pack_name= "Free Trial";
        } else {
           
            $date_noti = date('j M Y', strtotime($joining_date));
            $pack_name = "";
        }
        
        //web notification starts
        $fitness_booking_notifications = array(
            'listing_id' => $listing_id,
            'order_id' => $order_id,
            'title' => 'New Booking',
            'msg' => 'You Have Received a New booking',
            'image' => '',
            'notification_type' => 'fitness_bookings',
            'order_status' => '',
            'order_date' => $date,
            'invoice_no' => $booking_id
        );
      
        $connect_type ="fitness_bookings";
        $this->db->insert('pharmacy_notifications', $fitness_booking_notifications);
        
        //notification to fitness center
        $description ='Dear '.$branch_name.', booking is confirmed by ' . $user_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.';
        $this->notifyMethod($listing_id, $user_id, $description, $joining_date, $booking_id, $connect_type, $user_name, $user_id, $status,$date_noti,$pack_name,$order_id);
        
        //notification to customer
        $description1 ='Dear customer, your booking is confirmed at Fitness Center ' . $branch_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.'; 
        $this->notifyMethod($user_id, $listing_id, $description1, $joining_date, $booking_id, $connect_type, $branch_name, $listing_id, $status,$date_noti,$pack_name,$order_id);
        //web notification ends   

        if ($insert) {
            $message = 'Dear customer, your booking is confirmed at Fitness Center ' . $branch_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.';
            $post_data = array('From' => '02233721563', 'To' => $user_mobile, 'Body' => $message);
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

            $type_of_order = 'booking';
            $login_url = 'https://fitness.medicalwale.com';
            $message2 = 'You have received ' . $type_of_order . ', kindly login to ' . $login_url . '. Thank you.';
            $post_data2 = array('From' => '02233721563', 'To' => $branch_phone, 'Body' => $message2);
            $exotel_sid2 = "aegishealthsolutions";
            $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
            $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
            $ch2 = curl_init();
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
        
        return array(
            'status' => 200,
            'message' => 'success',
            'booking_id' => $booking_id
        );
    }
   
   
   
     public function add_bookings_v31($user_id, $listing_id, $package_id, $branch_id, $trainer_id,$category_id, $trail_booking_date, $trail_booking_time, $joining_date, $user_name, $user_mobile, $user_email, $user_gender,$user_age,$user_height,$height_cm_ft,$user_weight,$user_diet_preference,$user_exercise_level,$user_medical_condition,$user_ever_went_gym,$user_bmi,$user_dob,$user_blood_group) {
      
     
        date_default_timezone_set('Asia/Kolkata');
        $booking_id = date("YmdHis");
        $date = date('Y-m-d H:i:s');
        if($package_id ==100)
        {
            $status = "5";
        }
        else
        {
            $status = "1";
        }
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'trainer_package_id' => $package_id,
            'listing_id' => $listing_id,
            'user_id'    => $user_id,
            'branch_id' =>$branch_id,
            'trainer_id'  => $trainer_id,
            'vendor_id'  => '6',
            'category_id' => $category_id,
            'trail_booking_date' => $trail_booking_date,
            'trail_booking_time' => $trail_booking_time,
            'joining_date' => $joining_date,
            'user_name' => $user_name,
            'user_mobile' => $user_mobile,
            'user_email' => $user_email,
            'user_gender' => $user_gender,
            'booking_date' => $date,
            'status' => $status
        );
       
       $insert_user_data = $this->db->where('id', $user_id)->update('users', array(
                    'height' => $user_height,
                    'height_cm_ft' => $height_cm_ft,
                    'weight' => $user_weight,
                    'age'   => $user_age,
                    'diet_fitness' => $user_diet_preference,
                    'exercise_level'=> $user_exercise_level,
                    'health_condition'=> $user_medical_condition,
                    'bmi'=> $user_bmi, 
                    'blood_group'=> $user_blood_group,
                    'dob'=> $user_dob
                ));
        
       // $this->db->insert('fitness_booking_details', $data);
        
        $insert = $this->db->insert('booking_master', $booking_master_array);
        $order_id = $this->db->insert_id();
        
    if($insert){
            
                // $data= $this->FitnesscenterModel->fitness_notification_confirm($booking_id);
                //  $message = 'Dear '.$data->name.', your booking has been confirm With  '.$data->center_name.'  Your '.'Booking ID is '.$data->booking_id.' '.'Thank you.';
                //  $to = $data->email;
                //  $subject = 'medicalwale Appointment List';
                //  $msg = $message;
                //  $this->Send_Email_Notification($to,$subject,$msg);
       
      
        }

        $query_branch = $this->db->query("SELECT `branch_name`,`branch_phone` FROM fitness_center_branch WHERE id='$branch_id'");
        $row_branch = $query_branch->row_array();
        $branch_name = $row_branch['branch_name'];
        $branch_phone = $row_branch['branch_phone'];
        
        $query_branch1 = $this->db->query("SELECT `manager_name`,`contact` FROM personal_trainers WHERE id='$trainer_id'");
        $row_branch1 = $query_branch1->row_array();
        $branch_name1 = $row_branch1['manager_name'];
        $branch_phone1 = $row_branch1['contact']; 
        
        $date_ = date('j M Y', strtotime($joining_date));
        if ($package_id == '100') {
            //free trial
            $date_ = $trail_booking_date . ' ' . $trail_booking_time;
            $date_noti = date('j M Y | h:i A', strtotime($date_));
            $pack_name= "Free Trial";
        } else {
           
            $date_noti = date('j M Y', strtotime($joining_date));
            $pack_name = "";
        }
        
        //web notification starts
        $fitness_booking_notifications = array(
            'listing_id' => $listing_id,
            'order_id' => $order_id,
            'title' => 'New Booking',
            'msg' => 'You Have Received a New booking',
            'image' => '',
            'notification_type' => 'fitness_bookings',
            'order_status' => '',
            'order_date' => $date,
            'invoice_no' => $booking_id
        );
      
        $connect_type ="fitness_bookings";
        $this->db->insert('pharmacy_notifications', $fitness_booking_notifications);
        
        //notification to fitness center
        $description ='Dear '.$branch_name.', booking is confirmed by ' . $user_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.';
        $this->notifyMethod($listing_id, $user_id, $description, $joining_date, $booking_id, $connect_type, $user_name, $user_id, $status,$date_noti,$pack_name,$order_id);
        
        //notification to customer
        $description1 ='Dear customer, your booking is confirmed at Fitness Center ' . $branch_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.'; 
        $this->notifyMethod($user_id, $listing_id, $description1, $joining_date, $booking_id, $connect_type, $branch_name, $listing_id, $status,$date_noti,$pack_name,$order_id);
        //web notification ends   

        if ($insert) {
            $message = 'Dear customer, your booking is confirmed at Fitness Center ' . $branch_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.';
            $post_data = array('From' => '02233721563', 'To' => $user_mobile, 'Body' => $message);
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

            $type_of_order = 'booking';
            $login_url = 'https://fitness.medicalwale.com';
            $message2 = 'You have received ' . $type_of_order . ', kindly login to ' . $login_url . '. Thank you.';
            $post_data2 = array('From' => '02233721563', 'To' => $branch_phone, 'Body' => $message2);
            $exotel_sid2 = "aegishealthsolutions";
            $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
            $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
            $ch2 = curl_init();
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
        
        return array(
            'status' => 200,
            'message' => 'success',
            'booking_id' => $booking_id
        );
    }
   
   
   
    public function notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_id, $connect_type, $user_name, $list_id, $status,$date_noti,$pack_name,$order_id)
    {
        $customer_token       = $this->db->query("SELECT name,token,agent,token_status,web_token,vendor_id FROM users WHERE id='$listing_id'");
        $customer_token_count = $customer_token->num_rows();
        if ($customer_token_count > 0) {
            $token_status = $customer_token->row_array();
            $usr_name     = $token_status['name'];
            $agent        = $token_status['agent'];
            $reg_id       = $token_status['token'];
            $web_token    = $token_status['web_token'];
            $vendor_id    = $token_status['vendor_id'];
            $img_url      = 'https://s3.amazonaws.com/medicalwale/images/img/fitnes.png';
            $tag          = 'text';
            $key_count    = '1';
            $title        = 'Your Appointment with '.$user_name.' have been booked';
            $msg          =  'Appointment Date : ' . $booking_date ;
            
            $title_web        = $user_name . ' has booked an Fitness Center';
            $msg_web          = $description."\n".'Appointment Date : ' . $booking_date ;
            
          
                  //added by zak for all notification therough services
                $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => $status,
                      'order_date' => $booking_date,
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $listing_id,
                      'booking_id'  => $booking_id,
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'package_name' => $pack_name,
                       'notification_type'  => 'fitness_bookings',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
        //end
            if($vendor_id=='0'){
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_id, $connect_type, $list_id, $status,$date_noti,$pack_name);
            }
            if($vendor_id=='6'){
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019
                $click_action = 'https://fitness.medicalwale.com/appointments/booking_details/'.$order_id;
                // $click_action = 'https://vendor.sandbox.medicalwale.com/fitness/appointments/booking_details/'.$order_id;
                $this->send_gcm_web_notify($title_web, $msg_web, $web_token, $img_url, $tag, $agent, $connect_type,$click_action);
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019 Ends
            }
        }
    }
    //send notification through firebase
    /* notification to send in the doctor app for appointment confirmation*/
    
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
    
       public function Send_Email_Notification($to,$subject,$msg)
   {
    
        $headers = 'From: info@medicalwale.com' . "\r\n" .
           'Reply-To: donotreply@medicalwale.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();
        
        mail($to, $subject, $msg, $headers);
   }
    public function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $date, $booking_id, $connect_type, $list_id, $status,$date_noti,$pack_name)
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
                    'sound' => 'mysound',
                    "notification_type" => 'fitness_bookings',
                    "notification_date" => $date,
                    "booking_date" => $date,
                    "joining_date" => $date_noti,
                    "booking_id" => $booking_id,
                    "type_of_connect" => $connect_type,
                    "listing_id" => $list_id,
                    "status" => $status,
                    "package_name" => $pack_name
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
      //  print_r($result);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
      
        
    }
    
        public function fitness_notification_confirm($booking_id){
            
           /* echo"SELECT fitness_center.*,users.name,users.email,users.phone,
fitness_center.center_name,fitness_center.contact FROM fitness_center 
LEFT JOIN users ON (fitness_center.user_id=users.id) 
LEFT JOIN booking_master ON (booking_master.listing_id=fitness_center.user_id)
WHERE booking_master.booking_id='".$booking_id."'";*/
       $data="SELECT fitness_center.*,booking_master.booking_id,users.name,users.email,users.phone,
fitness_center.center_name,fitness_center.contact FROM fitness_center 
LEFT JOIN users ON (fitness_center.user_id=users.id) 
LEFT JOIN booking_master ON (booking_master.listing_id=fitness_center.user_id)
WHERE booking_master.booking_id='".$booking_id."'";
         
         $result = $this->db->query($data)->row();
            return $result;
}


 public function fitness_center_related_list($user_id, $latitude, $longitude,$listing_id) {
        $radius = '5';

        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyCMHN__LczEziaYuKXQ-SKdiMfx5AiY66o&callback=initMap";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return $dist;
        }

        if($latitude == '' || $longitude == '')
        {
              $doctor_lat       = $this->db->query("SELECT lat,lng FROM users WHERE id='$listing_id'");
        $doctor_lat_count = $doctor_lat->num_rows();
        if ($doctor_lat_count > 0) {
            $doctor_lat = $doctor_lat->row_array();
            $latitude       = $doctor_lat['lat'];
            $longitude        = $doctor_lat['lng'];
            
            if($latitude != '' || $longitude !='')
            {
         
             $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time, ( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'   HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
      
            }
            else
            {
                //mumbai lat long in worst condition
                $latitude       = '19.1286564';
                $longitude        = '72.8509587';
                $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time, ( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'   HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
      
                
             }
            }
        }
        else
        {
        // $sql = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE FIND_IN_SET('" . $category_id . "', doctor_list.category)  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
        $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time, ( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'   HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
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
                $branch_offer = $row['branch_offer'];
                $branch_facilities = $row['branch_facilities'];
                $branch_address = $row['branch_address'];
                $opening_hours = $row['opening_hours'];
                $pincode = $row['pincode'];
                $state = $row['state'];
                $city = $row['city'];
                $is_free_trail = $row['is_free_trail'];
                $from_trail_time = $row['from_trail_time'];
                $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '6';
                $rating = '4.5';
                $reviews = '0';
                $profile_views = '0';
                $user_discount = $row['user_discount'];
                $category=$row['branch_business_category'];
                
                /*$gallery_query = $this->db->query("SELECT is_active FROM `fitness_center` WHERE `user_id`='$listing_id'");
                $gallery_array = array();
                */

                if ($branch_image != '') {
                    $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                } else {
                    $branch_image = '';
                }


                $gallery_query = $this->db->query("SELECT `id`,`media_name`, `type` FROM `fitness_gallery` WHERE `branch_id`='$branch_id' AND `fitness_center_id`= '$listing_id' ");
                $gallery_array = array();
                $gallery_count = $gallery_query->num_rows();
                if ($gallery_count > 0) {
                    foreach ($gallery_query->result_array() as $rows) {
                        $media_name = $rows['media_name'];
                        $type = $rows['type'];
                        $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $type . '/' . $media_name;

                        $gallery_array[] = array(
                            "image" => $gallery,
                            "type" => $type
                        );
                    }
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



               /*$what_we_offer_array = '';
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    $what_we_offer_array[] = $what_we_offer;
                }

                $facilities_array = array();
                $facilities = explode(',', $branch_facilities);
                foreach ($facilities as $facilities) {
                    $facilities_array[] = $facilities;
                }*/
                
                
                 $category_array = array();
                $categorys = explode(',', $category);
             
              for($i=0; $i < count($categorys); $i++)
                {
                    if($categorys[$i]!='')
                    {
                      $cat_new=$categorys[$i];
                
            $Query = $this->db->query("SELECT * FROM business_category WHERE id = '$cat_new' and category_id='6'");
                            $category_val = $Query->row()->category;
                            $category_array[] = $category_val;
                    }
                   
                }
                $what_we_offer_array = array();
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                    if($what_we_offer != ""){
                        if(preg_match('/[0-9]/', $what_we_offer)){
                            $Query = $this->db->query("SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'");
                              $count = $Query->num_rows();
                            if($count>0)
                            {
                            $what_we_offer_value = $Query->row()->what_we_offer;
                            $what_we_offer_array[] = $what_we_offer_value;
                            }
                            else
                            {
                                $what_we_offer_array[] = $what_we_offer;
                            }
                        }else{
                            $what_we_offer_array[] = $what_we_offer;
                        }
                        
                    }
                    
                }

                $facilities_array = array();
                $facilities = explode(',', $branch_facilities);
                foreach ($facilities as $facilities) {
                    if($what_we_offer != "" && preg_match('/[0-9]/', $facilities)){
                        if(preg_match('/[0-9]/', $facilities)){
                            $Query = $this->db->query("SELECT * FROM fitness_facilities WHERE id='$facilities'");
                            $facilities_val = $Query->row()->facilities;
                            $facilities_array[] = $facilities_val;
                            
                        }
                        else{
                            $facilities_array[] = $facilities;
                        }
                    }
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $package_list = array();
                $package_query = $this->db->query("SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND categ_id='$category' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id = $package_row['id'];
                        $package_name = $package_row['package_name'];
                        $package_details = $package_row['package_details'];
                        $price = $package_row['price'];
                        $image = $package_row['image'];
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $package_list[] = array(
                            'package_id' => $package_id,
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price,
                            'image' => $image
                        );
                    }
                } else {
                    $package_list = array();
                }


                $booking_slot_array = array();
                if ($is_free_trail == '1') {
                    $from_trail_times = $this->check_time_format($from_trail_time);
                    $to_trail_times = $this->check_time_format($to_trail_time);
                    $time_difference = 60;
                    for ($i = strtotime($from_trail_times); $i <= strtotime($to_trail_times); $i = $i + $time_difference * 60) {
                        $booking_slot_array[] = array(
                            'time' => date("h:i A", $i)
                        );
                    }
                }

                $is_trial_order = 0;
                
                $is_order_query = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category' AND user_id='$user_id' AND package_id='100' AND status !='3' AND status !='9'");
                
                $is_order_count = $is_order_query->num_rows();

                if ($is_order_count > 0) {
                    $is_trial_order = 1;
                } 
               
                   $is_order_query1 = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category' AND user_id='$user_id' AND package_id !='100' AND status !='3' AND status !='9'");
                
                $is_order_count1 = $is_order_query1->num_rows();

                if ($is_order_count1 > 0) {
                    $is_trial_order = 1;
                } 

                $profile_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();
                $reviews = $this->db->select('id')->from('fitness_center_review')->where('branch_id', $branch_id)->get()->num_rows();



                $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
                $enquiry_number = "9619146163";
                if(empty($branch_email))
                {
                    $branch_email="";
                }
                else
                {
                    $branch_email;
                }
                $resultpost[] = array(
                    'branch_id' => $branch_id,
                    'lat' => $lat,
                    'lng' => $lng,
                    'distance' => $distances,
                    'category_id' => $category,
                    'category_name'=>$category_array,
                    'listing_id' => $listing_id,
                    'listing_type' => $listing_type,
                    'about_us' => $about_branch,
                    'center_name' => $branch_name,
                    'branch_name' => $branch_name,
                    'address' => $branch_address,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact' => $branch_phone,
                    'exotel_no' => '02233721563',
                    'enquiry_number' => $enquiry_number,
                    'email' => $branch_email,
                    'image' => $branch_image,
                    'is_free_trail' => $is_free_trail,
                    'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                    'is_trial_order' => $is_trial_order,
                    'profile_views' => $profile_views,
                    'user_discount' => $user_discount,
                    
                );
            }
        } else {
            $resultpost = array();
        }
      
        function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
            $sort_col = array();
            foreach ($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }
            array_multisort($sort_col, $dir, $arr);
        }

        array_sort_by_column($resultpost, 'distance');
       // print_r($resultpost);
        return $resultpost;
    }






}
