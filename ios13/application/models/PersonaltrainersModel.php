<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PersonaltrainersModel extends CI_Model {

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

    public function personal_trainers_list($user_id, $latitude, $longitude, $category) {
        $radius = '5';
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
            $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return $dist;
        }
        $query = $this->db->query("SELECT id,user_id,center_name,manager_name,address,pincode,contact,city,state,whatsapp,email,opening_hours,what_we_offer,facilities,lat,lng,date,image,user_discount,membership_plan,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM personal_trainers HAVING distance <= '$radius'");
        
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $center_name = $row['center_name'];
                $manager_name = $row['manager_name'];
                $about_us = '';
                $establishment_year = '2015';
                $address = $row['address'];
                $pincode = $row['pincode'];
                $contact = $row['contact'];
                $city = $row['city'];
                $state = $row['state'];
                $whatsapp = $row['whatsapp'];
                $email = $row['email'];
                $opening_hours = $row['opening_hours'];
                $what_we_offer = $row['what_we_offer'];
                $facilities = $row['facilities'];
                $listing_id = $row['user_id'];
                $image = $row['image'];
                $rating = '0';
                $reviews = '0';
                $profile_views = '0';
                $user_discount = $row['user_discount'];
                $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images1.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images2.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_video/Motivational_Workout_Video.mp4,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images3.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images4.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images5.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images6.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images7.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images8.jpg';
                $gallery_name = 'Fitness fever,Biceps Rips,Daily Routine,Gymming With Personal Trainer,Gym Equipments,Gold Gym Front View,Unlimited Cardio,Weight Loss With Cardio,Rocy Gym';

                $gallery = explode(",", $gallery);
                $gallery_name = explode(",", $gallery_name);
                $cnt = count($gallery);
                for ($i = 0; $i < $cnt; $i++) {
                    $gallery_array[] = array(
                        "title" => $gallery_name[$i],
                        "image" => $gallery[$i]
                    );
                }
                  $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
                
                // $query1 = $this->db->query("SELECT * FROM `fitness_gallery` WHERE `branch_id` = '207'");
                // // SELECT * FROM `fitness_center_branch` WHERE `id` = 207
                
                // foreach($query1->result_array() as $img){
                //     print_r($img) ; 
                // }
                
                // die();
                
              

                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $image;
                } else {
                    $image = '';
                }

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
                $current_day = "";

                $what_we_offer_array = '';
                $what_we_offer = explode(',', $what_we_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    $what_we_offer_array[] = $what_we_offer;
                }

                $facilities_array = '';
                $facilities = explode(',', $facilities);
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



                $package_list = '';
                $package_query = $this->db->query("SELECT id,package_name,package_details,price,image FROM `personal_trainers_membership_plan` where personal_trainers_id='$id' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_name = $package_row['package_name'];
                        $package_details = $package_row['package_details'];
                        $price = $package_row['price'];
                        $image = $package_row['image'];
                        $image = str_replace(" ", "", $image);
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/personal_trainers/' . $image;
                        $package_list[] = array(
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price,
                            'image' => $image
                        );
                    }
                } else {
                    $package_list = array();
                }


                $resultpost[] = array(
                    'id' => $id,
                    'lat' => $lat,
                    'lng' => $lng,
                    'id' => $id,
                    'distance' => $distances,
                    'listing_id' => $listing_id,
                    'about_us' => $about_us,
                    'center_name' => $center_name,
                    'manager_name' => $manager_name,
                    'address' => $address,
                    'establishment_year' => $establishment_year,
                    'pincode' => $pincode,
                    'contact' => $contact,
                    'exotel_no' => '02233721563',
                    'city' => $city,
                    'state' => $state,
                    'whatsapp' => $whatsapp,
                    'email' => $email,
                    'image' => $image,
                    'gallery' => $gallery_array,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                    'profile_views' => $profile_views,
                    'opening_day' => $final_Day,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
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
        return $resultpost;
    }

    public function personal_trainers_details($user_id, $listing_ids) {
        $radius = '5';
        $query = $this->db->query("SELECT id,user_id,center_name,manager_name,address,pincode,contact,city,state,whatsapp,email,opening_hours,what_we_offer,facilities,lat,lng,date,image,membership_plan FROM personal_trainers where user_id='$listing_ids' limit 1");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $center_name = $row['center_name'];
                $manager_name = $row['manager_name'];
                $about_us = $row['center_name'];
                $establishment_year = '2015';
                $address = $row['address'];
                $pincode = $row['pincode'];
                $contact = $row['contact'];
                $city = $row['city'];
                $state = $row['state'];
                $whatsapp = $row['whatsapp'];
                $email = $row['email'];
                $opening_hours = $row['opening_hours'];
                $what_we_offer = $row['what_we_offer'];
                $facilities = $row['facilities'];
                $listing_id = $row['user_id'];
                $image = $row['image'];
                $rating = '0';
                $reviews = '0';
                $profile_views = '0';

                $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images1.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images2.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_video/Motivational_Workout_Video.mp4,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images3.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images4.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images5.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images6.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images7.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images8.jpg';
                $gallery_name = 'Fitness fever,Biceps Rips,Daily Routine,Gymming With Personal Trainer,Gym Equipments,Gold Gym Front View,Unlimited Cardio,Weight Loss With Cardio,Rocy Gym';

                $gallery = explode(",", $gallery);
                $gallery_name = explode(",", $gallery_name);
                $cnt = count($gallery);
                for ($i = 0; $i < $cnt; $i++) {
                    $gallery_array[] = array(
                        "title" => $gallery_name[$i],
                        "image" => $gallery[$i]
                    );
                }

                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $image;
                } else {
                    $image = '';
                }

                $open_days = '';
                date_default_timezone_set('Asia/Kolkata');
                $opening_hours = explode(',', $opening_hours);
                $flag = '1';
                foreach ($opening_hours as $opening_hours) {

                    $array_hours = explode('-', $opening_hours);
                    $day = $array_hours[0];
                    $start_time = $array_hours[1];
                    $end_time = $array_hours[2];
                    $cur_time = date("H.i");
                    $cur_day = date("l");
                    $time = $start_time . ' - ' . $end_time;
                    if ($cur_day == $day && $flag > 0) {
                        $start_time_val = date("H.i", strtotime($start_time));
                        $end_time_val = date("H.i", strtotime($end_time));
                        if ($cur_time >= $start_time_val && $cur_time <= $end_time_val) {
                            $total_hr = $end_time_val - $start_time_val;
                            $current_time = $time;
                            if ($total_hr > 23.58) {
                                $current_time = 'Open 24 Hrs';
                            }
                            $day_ = 'Open';
                        } else {
                            $current_time = $time;
                            $day_ = 'Close';
                        }
                        $current_day = array(
                            'day' => $day_,
                            'time' => $current_time
                        );
                        $flag = '0';
                    }
                    $start_time_val = date("H.i", strtotime($start_time));
                    $end_time_val = date("H.i", strtotime($end_time));
                    $total_hr = $end_time_val - $start_time_val;
                    if ($total_hr > 23.58) {
                        $time = 'Open 24 Hrs';
                    }
                    $open_days[] = array(
                        'day' => $day,
                        'time' => $time
                    );
                }

                $what_we_offer_array = '';
                $what_we_offer = explode(',', $what_we_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    $what_we_offer_array[] = $what_we_offer;
                }

                $facilities_array = '';
                $facilities = explode(',', $facilities);
                foreach ($facilities as $facilities) {
                    $facilities_array[] = $facilities;
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_ids)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_ids)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_ids)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }



                $package_list = '';
                $package_query = $this->db->query("SELECT id,package_name,package_details,price,image FROM `personal_trainers_membership_plan` where personal_trainers_id='$id' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_name = $package_row['package_name'];
                        $package_details = $package_row['package_details'];
                        $price = $package_row['price'];
                        $image = $package_row['image'];
                        $image = str_replace(" ", "", $image);
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/personal_trainers/' . $image;
                        $package_list[] = array(
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price,
                            'image' => $image
                        );
                    }
                } else {
                    $package_list = array();
                }


                $resultpost[] = array(
                    'id' => $id,
                    'lat' => $lat,
                    'lng' => $lng,
                    'listing_id' => $listing_id,
                    'about_us' => $about_us,
                    'center_name' => $center_name,
                    'manager_name' => $manager_name,
                    'address' => $address,
                    'establishment_year' => $establishment_year,
                    'pincode' => $pincode,
                    'contact' => $contact,
                    'city' => $city,
                    'state' => $state,
                    'whatsapp' => $whatsapp,
                    'email' => $email,
                    'image' => $image,
                    'gallery' => $gallery_array,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                    'profile_views' => $profile_views,
                    'current_day' => $current_day,
                    'opening_day' => $open_days,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }
    
    
    


     public function personal_trainers_list_v2($user_id, $latitude, $longitude, $category_lid,$term) {
        $radius = '5';
        function GetDrivingDistance($lat1, $lat2, $long1, $long2) 
        {
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
            $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return $dist;
        }
         if($category_lid=="0" && empty($term))
        {
        $query = $this->db->query("SELECT p.id,p.category_id ,p.user_id,p.manager_name,p.whatsapp,p.email,p.opening_hours,p.what_we_offer,p.lat,p.lng,p.date,p.image,p.user_discount,p.membership_plan,p.qualifications,p.experience,p.fitness_trainer_pic,p.gender,fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number ,( 6371 * acos( cos( radians($latitude) ) * cos( radians( p.lat ) ) * cos( radians( p.lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( p.lat ) ) ) ) AS distance  FROM personal_trainers as p LEFT JOIN fitness_center as fc ON(p.user_id= fc.user_id) LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id)  HAVING distance <= '$radius'");
        }
        else if($category_lid=="0" && !empty($term))
        {
        $query = $this->db->query("SELECT p.id,p.category_id,p.user_id,p.manager_name,p.whatsapp,p.email,p.opening_hours,p.what_we_offer,p.lat,p.lng,p.date,p.image,p.user_discount,p.membership_plan,p.qualifications,p.experience,p.fitness_trainer_pic,p.gender,fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number ,( 6371 * acos( cos( radians($latitude) ) * cos( radians( p.lat ) ) * cos( radians( p.lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( p.lat ) ) ) ) AS distance  FROM personal_trainers as p LEFT JOIN fitness_center as fc ON(p.user_id= fc.user_id) LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) where p.manager_name LIKE '%$term%'  or fc.`center_name` LIKE '%$term%' HAVING distance <= '$radius'");
        }
         else if($category_lid!="0" && empty($term))
        {
            
        $query = $this->db->query("SELECT p.id,p.category_id ,p.user_id,p.manager_name,p.whatsapp,p.email,p.opening_hours,p.what_we_offer,p.lat,p.lng,p.date,p.image,p.user_discount,p.membership_plan,p.qualifications,p.experience,p.fitness_trainer_pic,p.gender,fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number ,( 6371 * acos( cos( radians($latitude) ) * cos( radians( p.lat ) ) * cos( radians( p.lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( p.lat ) ) ) ) AS distance  FROM personal_trainers as p LEFT JOIN fitness_center as fc ON(p.user_id= fc.user_id) LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) where FIND_IN_SET('" . $category_lid . "', p.category_id)  HAVING distance <= '$radius'");
        }
         else if($category_lid!="0" && !empty($term))
        {
            
        $query = $this->db->query("SELECT p.id,p.category_id ,p.user_id,p.manager_name,p.whatsapp,p.email,p.opening_hours,p.what_we_offer,p.lat,p.lng,p.date,p.image,p.user_discount,p.membership_plan,p.qualifications,p.experience,p.fitness_trainer_pic,p.gender,fc.`user_id`, fc.`center_name`, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number ,( 6371 * acos( cos( radians($latitude) ) * cos( radians( p.lat ) ) * cos( radians( p.lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( p.lat ) ) ) ) AS distance  FROM personal_trainers as p LEFT JOIN fitness_center as fc ON(p.user_id= fc.user_id) LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id) where FIND_IN_SET('" . $category_lid . "', p.category_id) and (p.manager_name LIKE '%$term%'  or fc.`center_name` LIKE '%$term%') HAVING distance <= '$radius'");
        }
        $count = $query->num_rows();
        if ($count > 0) 
           {
            foreach ($query->result_array() as $row) 
            {
                
               $listing_id=$row['user_id']; 
              
                
                $id = $row['id'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $center_name = $row['center_name'];
                $manager_name = $row['manager_name'];
                $opening_hours = $row['opening_hours'];
                
               
               
                $fitness_trainer_pic = $row['fitness_trainer_pic'];
               
                $rating = '4.5';
                 
                 $query_branch = $this->db->query("SELECT fcb.id, fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category AS category_id,business_category.category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time, fcb.to_trail_time FROM `fitness_center_branch` AS fcb LEFT JOIN business_category ON fcb.branch_business_category= business_category.id  WHERE user_id='$listing_id'");         
                 
                $list1 = $query_branch->row_array();
                  $branch_id = $list1['id'];
                  $branch_address = $list1['branch_address'];
                    $category_id = $row['category_id'];
                     $branch_phone = $list1['branch_phone'];
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

foreach($final_Day as $key => $product)
{

if($product['day']===$dfinal)
{
 $current_day_final = $product['time'][0];
}
}

                       $enquiry_number = "9619146163";
                       
                        $session1 = array();
                    
                     $session1[] = array(
                                'package_id'=>'1',
                                'package_name' => 'zumba',
                                'package_details' => 'Details about the package',
                                'price'=>'1,500',
                                'image'=> 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $fitness_trainer_pic
                            );
                         
                           $session1[] = array(
                                'package_id'=>'2',
                                'package_name' => 'zumba',
                                'package_details' => 'Details about the package1',
                                'price'=>'1,500',
                                'image'=> 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $fitness_trainer_pic
                            );
                         
                           
                         $package_list=  $session1;
                       
                    
                $resultpost[] = array(
                    'id' => $id,
                    'listing_id' => $listing_id,
                    'branch_id' =>$branch_id,
                    'lat' => $lat,
                    'lng' => $lng,
                    'distance' => $distances, 
                    'center_name' => $center_name,
                    'manager_name' => $manager_name,
                    'fitness_trainer_pic' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$fitness_trainer_pic,
                    'package_list' => $package_list,
                    'rating' => $rating,
                    'address' => $branch_address,
                    'category_id' => $category_id,
                    'category' => $category,
                    'contact' => $branch_phone,
                    'exotel_no' => '02233721563',
                    'enquiry_number' => $enquiry_number,
                    'current_time'=>$current_day_final,
                    'new' => $new
                    
                );
               
               
            }
           } 
        else 
            {
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
        return $resultpost;
        
        
        
        return $resultpost;
    }
    
    public function personal_trainers_details_v2($user_id, $listing_ids,$id) {
        $radius = '5';
       $query = $this->db->query("SELECT p.id,p.category_id ,p.user_id,p.manager_name,p.whatsapp,p.email,p.opening_hours,p.what_we_offer,p.lat,p.lng,p.date,p.image,p.user_discount,p.membership_plan,p.qualifications,p.experience,p.fitness_trainer_pic,p.gender,fc.`user_id`, fc.`center_name`,p.kyc_pic, fc.`contact`, fc.`email`, fc.`image`,en.contact_no as enquiry_number,p.facilities,p.language FROM personal_trainers as p LEFT JOIN fitness_center as fc ON(p.user_id= fc.user_id) LEFT JOIN users as u ON(u.id=fc.user_id) LEFT JOIN enquiry_number en ON(en.vendor_id=u.vendor_id)  where p.id ='$id' and p.user_id='$listing_ids'");

        $count = $query->num_rows();
        if ($count > 0) {
            $row=$query->row_array();
                $id = $row['id'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $center_name = $row['center_name'];
                $manager_name = $row['manager_name'];
                $about_us = $row['center_name'];
                $qualifications=$row['qualifications'];
                $establishment_year = '2015';
                   $query_branch = $this->db->query("SELECT fcb.id, fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category AS category_id,business_category.category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time, fcb.to_trail_time FROM `fitness_center_branch` AS fcb LEFT JOIN business_category ON fcb.branch_business_category= business_category.id  WHERE user_id='$listing_ids'");         
                 
                $list1 = $query_branch->row_array();
                $branch_id = $list1['id'];
                $branch_address = $list1['branch_address'];
                $pincode = $list1['pincode'];
                $contact = $row['contact'];
                $city = $list1['city'];
                $state = $list1['state'];
                $whatsapp = $row['whatsapp'];
                $email = $row['email'];
                $opening_hours = $row['opening_hours'];
                $certification1 = $row['kyc_pic'];
                $facilities = $row['facilities'];
                $listing_id = $row['user_id'];
                $image = $row['image'];
                $personal=$row['fitness_trainer_pic'];
                $rating = '0';
                $reviews = '0';
                $profile_views = '0';

                $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images1.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images2.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_video/Motivational_Workout_Video.mp4,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images3.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images4.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images5.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images6.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images7.jpg,https://d2c8oti4is0ms3.cloudfront.net/images/fitness_images/images8.jpg';
                $gallery_name = 'Fitness fever,Biceps Rips,Daily Routine,Gymming With Personal Trainer,Gym Equipments,Gold Gym Front View,Unlimited Cardio,Weight Loss With Cardio,Rocy Gym';

                $gallery = explode(",", $gallery);
                $gallery_name = explode(",", $gallery_name);
                $cnt = count($gallery);
                for ($i = 0; $i < $cnt; $i++) {
                    $gallery_array[] = array(
                        "title" => $gallery_name[$i],
                        "image" => $gallery[$i]
                    );
                }

                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'. $image;
                } else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
               if($personal!='')
               {
                    $personal_image_trainer = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'. $personal;
               }
               else
               {
                  $personal_image_trainer = "https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg"; 
               }

$certification_array=array();
  $certification = explode(",", $certification1);
               
                $cnt = count($certification);
                for ($i = 0; $i < $cnt; $i++) {
                    $certification_array[] = array(
                      
                        "image" =>"https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$certification[$i]
                    );
                }

                    $category_id1 = $row['category_id'];
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

   
                 $language1 = $row['language'];
                    $clang1 = explode(',',$language1);
                    $language_name1 = array();
                    for($i=0;$i<count($clang1);$i++)
                    {
                        if(!empty($clang1[$i]))
                        {
                        $sql = $this->db->select('name')->from('languages')->where('id', $clang1[$i])->get()->row()->name;
                        $language_name1[]=$sql;
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

               /* $what_we_offer_array = '';
                $what_we_offer = explode(',', $what_we_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    $what_we_offer_array[] = $what_we_offer;
                }*/

                $facilities_array = '';
                $facilities = explode(',', $facilities);
                foreach ($facilities as $facilities) {
                    $facilities_array[] = $facilities;
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_ids)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_ids)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_ids)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

$gender = $row['gender'];

               /* $package_list = '';
                $package_query = $this->db->query("SELECT id,package_name,package_details,price,image FROM `personal_trainers_membership_plan` where personal_trainers_id='$id' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_name = $package_row['package_name'];
                        $package_details = $package_row['package_details'];
                        $price = $package_row['price'];
                        $image = $package_row['image'];
                        $image = str_replace(" ", "", $image);
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/personal_trainers/' . $image;
                        $package_list[] = array(
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price,
                            'image' => $image
                        );
                    }
                } else {
                    $package_list = array();
                }*/
$final_session1=array();
                    $session1 = array();
                    
                     $session1[] = array(
                                'package_id'=>'1',
                                'package_name' => 'zumba',
                                'package_details' => 'Details about the package',
                                'price'=>'1,500',
                                'image'=> 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image
                            );
                         
                           $session1[] = array(
                                'package_id'=>'2',
                                'package_name' => 'zumba',
                                'package_details' => 'Details about the package1',
                                'price'=>'1,500',
                                'image'=> 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image
                            );
                         
                           
                         $package_list=  $session1;
if(empty($whatsapp))
{
    $whatsapp="";
}
else
{
    $whatsapp;
}
                $resultpost[] = array(
                    'id' => $id,
                    'lat' => $lat,
                    'lng' => $lng,
                    'listing_id' => $listing_id,
                    'branch_id' =>$branch_id,
                    'about_us' => $about_us,
                    'category_id'=>$category_id1,
                    'center_name' => $center_name,
                    'manager_name' => $manager_name,
                    'address' => $branch_address,
                    'establishment_year' => $establishment_year,
                    'pincode' => $pincode,
                    'contact' => $contact,
                    'city' => $city,
                    'state' => $state,
                    'whatsapp' => $whatsapp,
                    'email' => $email,
                    'personal'=>$personal_image_trainer,
                    'image' => $image,
                    'gallery' => $gallery_array,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                    'profile_views' => $profile_views,
                    'qualifications' =>$qualifications,
                    'gender' => $gender,
                    'opening_day' => $final_Day,
                    'certification' => $certification_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'category' => $category_name1,
                    'language'=>$language_name1
                );
            }
         else {
            $resultpost = array();
        }

        return $resultpost;
    }    
    

    public function add_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('personal_trainers_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function review_list($user_id, $listing_id) {

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

        $resultpost = '';
        $review_count = $this->db->select('id')->from('personal_trainers_review')->where('listing_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT personal_trainers_review.id,personal_trainers_review.user_id,personal_trainers_review.listing_id,personal_trainers_review.rating,personal_trainers_review.review, personal_trainers_review.service,personal_trainers_review.date as review_date,users.id as user_id,users.name as firstname FROM `personal_trainers_review` INNER JOIN `users` ON personal_trainers_review.user_id=users.id WHERE personal_trainers_review.listing_id='$listing_id' order by personal_trainers_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                $decrypt = $this->decrypt($review);
                $encrypt = $this->encrypt($decrypt);
                if ($encrypt == $review) {
                    $review = $decrypt;
                }
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('personal_trainers_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('personal_trainers_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('personal_trainers_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

                $resultpost[] = array(
                    'id' => $id,
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

    public function review_like($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `personal_trainers_review_likes` WHERE post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `personal_trainers_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from `personal_trainers_review_likes` WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $personal_trainers_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('personal_trainers_review_likes', $personal_trainers_review_likes);
            $like_query = $this->db->query("SELECT id from personal_trainers_review_likes WHERE post_id='$post_id'");
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

        $personal_trainers_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('personal_trainers_review_comment', $personal_trainers_review_comment);
        $personal_trainers_review_comment_query = $this->db->query("SELECT id from personal_trainers_review_comment where post_id='$post_id'");
        $total_comment = $personal_trainers_review_comment_query->num_rows();
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
        $count_query = $this->db->query("SELECT id from personal_trainers_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `personal_trainers_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from personal_trainers_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $personal_trainers_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('personal_trainers_review_comment_like', $personal_trainers_review_comment_like);
            $comment_query = $this->db->query("SELECT id from personal_trainers_review_comment_like where comment_id='$comment_id'");
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

        $review_list_count = $this->db->select('id')->from('personal_trainers_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT personal_trainers_review_comment.id,personal_trainers_review_comment.post_id,personal_trainers_review_comment.comment as comment,personal_trainers_review_comment.date,users.name,personal_trainers_review_comment.user_id as post_user_id FROM personal_trainers_review_comment INNER JOIN users on users.id=personal_trainers_review_comment.user_id WHERE personal_trainers_review_comment.post_id='$post_id' order by personal_trainers_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                $comment_decrypt = $this->decrypt($comment);
                $comment_encrypt = $this->encrypt($comment_decrypt);
                if ($comment_encrypt == $comment) {
                    $comment = $comment_decrypt;
                }
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('personal_trainers_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('personal_trainers_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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

}
