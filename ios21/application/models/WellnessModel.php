<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class WellnessModel extends CI_Model {

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

    public function check_time_format($time) {
        date_default_timezone_set('Asia/Kolkata');
        $time_filter = preg_replace('/\s+/', '', $time);
        $final_time = date("h:i A", strtotime($time_filter));
        $final_times = date("H:i", strtotime($final_time));
        return $final_times;
    }
    
    // Wellness Clinic listing function by ghanshyam parihar starts
    public function wellness_clinic_list($user_id, $latitude, $longitude, $category) {
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

        // $sql = sprintf("SELECT `id`, `user_id`, `branch_name`, `branch_image`, `branch_phone`, `branch_email`, `about_branch`, `branch_business_category`, `branch_offer`, `branch_facilities`, `branch_address`, `opening_hours`, `pincode`, `state`, `city`, `lat`, `lng`,`user_discount`, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM spa_branch  WHERE  FIND_IN_SET('" . $category . "', branch_business_category)  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
        $sql = sprintf("SELECT fcb.id,fcb.hospital_id user_id, fcb.name_of_branch branch_name, fcb.image branch_image, fcb.phone branch_phone, fcb.email branch_email, fcb.about_us about_branch, fc.category branch_business_category, fcb.address branch_address, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fc.user_discount,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM wellness_branch fcb LEFT JOIN wellness_clinic fc ON(fcb.hospital_id= fc.user_id) WHERE fc.is_active='1' AND FIND_IN_SET('" . $category . "', fc.category)  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
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
                // $branch_offer = $row['branch_offer'];
                // $branch_facilities = $row['branch_facilities'];
                $branch_address = $row['branch_address'];
                // $opening_hours = $row['opening_hours'];
                $pincode = $row['pincode'];
                $state = $row['state'];
                $city = $row['city'];
                $listing_id = $row['user_id'];
                $listing_type = '32';
                $rating = '4.5';
                $reviews = '0';
                $profile_views = '0';
                $user_discount = $row['user_discount'];
                // $branch_facilities = $row['user_discount'];

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


                // $open_days = '';
                // $day_array_list = '';
                // $day_list = '';
                // $day_time_list = '';
                // $time_list1 = '';
                // $time_list2 = '';
                // $time = '';
                // $system_start_time = '';
                // $system_end_time = '';
                // $time_check = '';
                // $current_time = '';
                // $open_close = array();
                // $time = array();
                // date_default_timezone_set('Asia/Kolkata');
                // $data = array();
                // $final_Day = array();
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

                //                     $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                //                     $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                //                     $current_time = date('h:i A');

                //                     $date1 = DateTime::createFromFormat('H:i a', $current_time);
                //                     $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                //                     $date3 = DateTime::createFromFormat('H:i a', $system_end_time);

                //                     if ($date2 < $date3 && $date1 <= $date3) {
                //                         $date3->modify('+1 day')->format('H:i a');
                //                     } elseif ($date2 > $date3 && $date1 >= $date3) {
                //                         $date3->modify('+1 day')->format('H:i a');
                //                     }

                //                     if ($date1 > $date2 && $date1 < $date3) {
                //                         $open_close[] = 'open';
                //                     } else {
                //                         $open_close[] = 'closed';
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



               /*$what_we_offer_array = '';
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    $what_we_offer_array[] = $what_we_offer;
                }*/

                // $facilities_array = array();
                // $facilities = explode(',', $branch_facilities);
                // foreach ($facilities as $facilities) {
                //     $facilities_array[] = $facilities;
                // }
                
                // $what_we_offer_array = array();
                // $what_we_offer = explode(',', $branch_offer);
                // foreach ($what_we_offer as $what_we_offer) {
                //     //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                //     if($what_we_offer != ""){
                //         if(preg_match('/[0-9]/', $what_we_offer)){
                //             $Query = $this->db->query("SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'");
                //             $what_we_offer_value = $Query->row()->what_we_offer;
                //             $what_we_offer_array[] = $what_we_offer_value;
                //         }else{
                //             $what_we_offer_array[] = $what_we_offer;
                //         }
                        
                //     }
                    
                // }

                // $facilities_array = array();
                // $facilities = explode(',', $branch_facilities);
                // foreach ($facilities as $facilities) {
                //     if($what_we_offer != "" && preg_match('/[0-9]/', $facilities)){
                //         if(preg_match('/[0-9]/', $facilities)){
                //             $Query = $this->db->query("SELECT * FROM fitness_facilities WHERE id='$facilities'");
                //             $facilities_val = $Query->row()->facilities;
                //             $facilities_array[] = $facilities_val;
                            
                //         }
                //         else{
                //             $facilities_array[] = $facilities;
                //         }
                //     }
                // }

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
            //   echo "SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND categ_id='$category' order by id asc";
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


                // $booking_slot_array = array();
                // if ($is_free_trail == '1') {
                //     $from_trail_times = $this->check_time_format($from_trail_time);
                //     $to_trail_times = $this->check_time_format($to_trail_time);
                //     $time_difference = 60;
                //     for ($i = strtotime($from_trail_times); $i <= strtotime($to_trail_times); $i = $i + $time_difference * 60) {
                //         $booking_slot_array[] = array(
                //             'time' => date("h:i A", $i)
                //         );
                //     }
                // }

                // $is_trial_order = 0;
                
                // $is_order_query = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category' AND user_id='$user_id' AND package_id='100' AND status !='3' AND status !='9'");
                // $is_order_count = $is_order_query->num_rows();

                // if ($is_order_count > 0) {
                //     $is_trial_order = 1;
                // } 
                
                // $is_order_query1 = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category' AND user_id='$user_id' AND package_id !='100' AND status !='3' AND status !='9'");
                // $is_order_count1 = $is_order_query1->num_rows();

                // if ($is_order_count1 > 0) {
                //     $is_trial_order = 1;
                // } 

                $profile_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();
                $reviews = $this->db->select('id')->from('fitness_center_review')->where('branch_id', $branch_id)->get()->num_rows();



                $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
                $enquiry_number = "9619146163";
                $resultpost[] = array(
                    'branch_id' => $branch_id,
                    'lat' => $lat,
                    'lng' => $lng,
                    'distance' => $distances,
                    'category_id' => $branch_business_category,
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
                    // 'is_free_trail' => $is_free_trail,
                    // 'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                    // 'what_we_offer' => $what_we_offer_array,
                    // 'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    // 'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                    // 'is_trial_order' => $is_trial_order,
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

        return $resultpost;
    }
    // Wellness Clinic listing function by ghanshyam parihar ends
    
    // Wellness Clinic bachat listing function by ghanshyam parihar starts
    
    public function wellness_clinic_bachat_list($user_id, $latitude, $longitude, $category) {
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

        // $sql = sprintf("SELECT `id`, `user_id`, `branch_name`, `branch_image`, `branch_phone`, `branch_email`, `about_branch`, `branch_business_category`, `branch_offer`, `branch_facilities`, `branch_address`, `opening_hours`, `pincode`, `state`, `city`, `lat`, `lng`,`user_discount`, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM spa_branch  WHERE  FIND_IN_SET('" . $category . "', branch_business_category)  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
        $sql = sprintf("SELECT fcb.id,fcb.hospital_id user_id, fcb.name_of_branch branch_name, fc.image branch_image, fcb.phone branch_phone, fcb.email branch_email, fcb.about_us about_branch, fc.category branch_business_category, fcb.address branch_address, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fc.user_discount,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM wellness_branch fcb LEFT JOIN wellness_clinic fc ON(fcb.hospital_id= fc.user_id) WHERE fc.user_discount!='0' AND fc.is_active='1' AND FIND_IN_SET('" . $category . "', fc.category)  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
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
                // $branch_offer = $row['branch_offer'];
                // $branch_facilities = $row['branch_facilities'];
                $branch_address = $row['branch_address'];
                // $opening_hours = $row['opening_hours'];
                $pincode = $row['pincode'];
                $state = $row['state'];
                $city = $row['city'];
                $listing_id = $row['user_id'];
                $listing_type = '32';
                $rating = '4.5';
                $reviews = '0';
                $profile_views = '0';
                $user_discount = $row['user_discount'];
                // $branch_facilities = $row['user_discount'];

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


                // $open_days = '';
                // $day_array_list = '';
                // $day_list = '';
                // $day_time_list = '';
                // $time_list1 = '';
                // $time_list2 = '';
                // $time = '';
                // $system_start_time = '';
                // $system_end_time = '';
                // $time_check = '';
                // $current_time = '';
                // $open_close = array();
                // $time = array();
                // date_default_timezone_set('Asia/Kolkata');
                // $data = array();
                // $final_Day = array();
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

                //                     $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                //                     $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                //                     $current_time = date('h:i A');

                //                     $date1 = DateTime::createFromFormat('H:i a', $current_time);
                //                     $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                //                     $date3 = DateTime::createFromFormat('H:i a', $system_end_time);

                //                     if ($date2 < $date3 && $date1 <= $date3) {
                //                         $date3->modify('+1 day')->format('H:i a');
                //                     } elseif ($date2 > $date3 && $date1 >= $date3) {
                //                         $date3->modify('+1 day')->format('H:i a');
                //                     }

                //                     if ($date1 > $date2 && $date1 < $date3) {
                //                         $open_close[] = 'open';
                //                     } else {
                //                         $open_close[] = 'closed';
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



               /*$what_we_offer_array = '';
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    $what_we_offer_array[] = $what_we_offer;
                }*/

                // $facilities_array = array();
                // $facilities = explode(',', $branch_facilities);
                // foreach ($facilities as $facilities) {
                //     $facilities_array[] = $facilities;
                // }
                
                // $what_we_offer_array = array();
                // $what_we_offer = explode(',', $branch_offer);
                // foreach ($what_we_offer as $what_we_offer) {
                //     //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                //     if($what_we_offer != ""){
                //         if(preg_match('/[0-9]/', $what_we_offer)){
                //             $Query = $this->db->query("SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'");
                //             $what_we_offer_value = $Query->row()->what_we_offer;
                //             $what_we_offer_array[] = $what_we_offer_value;
                //         }else{
                //             $what_we_offer_array[] = $what_we_offer;
                //         }
                        
                //     }
                    
                // }

                // $facilities_array = array();
                // $facilities = explode(',', $branch_facilities);
                // foreach ($facilities as $facilities) {
                //     if($what_we_offer != "" && preg_match('/[0-9]/', $facilities)){
                //         if(preg_match('/[0-9]/', $facilities)){
                //             $Query = $this->db->query("SELECT * FROM fitness_facilities WHERE id='$facilities'");
                //             $facilities_val = $Query->row()->facilities;
                //             $facilities_array[] = $facilities_val;
                            
                //         }
                //         else{
                //             $facilities_array[] = $facilities;
                //         }
                //     }
                // }

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
            //   echo "SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND categ_id='$category' order by id asc";
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


                // $booking_slot_array = array();
                // if ($is_free_trail == '1') {
                //     $from_trail_times = $this->check_time_format($from_trail_time);
                //     $to_trail_times = $this->check_time_format($to_trail_time);
                //     $time_difference = 60;
                //     for ($i = strtotime($from_trail_times); $i <= strtotime($to_trail_times); $i = $i + $time_difference * 60) {
                //         $booking_slot_array[] = array(
                //             'time' => date("h:i A", $i)
                //         );
                //     }
                // }

                // $is_trial_order = 0;
                
                // $is_order_query = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category' AND user_id='$user_id' AND package_id='100' AND status !='3' AND status !='9'");
                // $is_order_count = $is_order_query->num_rows();

                // if ($is_order_count > 0) {
                //     $is_trial_order = 1;
                // } 
                
                // $is_order_query1 = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category' AND user_id='$user_id' AND package_id !='100' AND status !='3' AND status !='9'");
                // $is_order_count1 = $is_order_query1->num_rows();

                // if ($is_order_count1 > 0) {
                //     $is_trial_order = 1;
                // } 

                $profile_views = $this->db->select('id')->from('fitness_views')->where('listing_id', $listing_id)->get()->num_rows();
                $reviews = $this->db->select('id')->from('fitness_center_review')->where('branch_id', $branch_id)->get()->num_rows();



                $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
                $enquiry_number = "9619146163";
                $resultpost[] = array(
                    'branch_id' => $branch_id,
                    'lat' => $lat,
                    'lng' => $lng,
                    'distance' => $distances,
                    'category_id' => $branch_business_category,
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
                    // 'is_free_trail' => $is_free_trail,
                    // 'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                    // 'what_we_offer' => $what_we_offer_array,
                    // 'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    // 'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                    // 'is_trial_order' => $is_trial_order,
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

        return $resultpost;
    }
    
    // Wellness Clinic bachat listing function by ghanshyam parihar ends
    
    public function spa_center_details($user_id, $listing_id) {
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


            $query_branch = $this->db->query("SELECT fcb.id, fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category AS category_id,business_category.category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time, fcb.to_trail_time FROM `spa_branch` AS fcb LEFT JOIN business_category ON fcb.branch_business_category= business_category.id  WHERE user_id='$listing_id'");
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
    
    
     public function spa_center_other_branch($user_id, $listing_id, $category_id, $branch_id) {


        $query = $this->db->query("SELECT `id`, `user_id`, `branch_name`, `branch_image`, `branch_phone`, `branch_email`, `about_branch`, `branch_business_category`, `branch_offer`, `branch_facilities`, `branch_address`, `opening_hours`, `pincode`, `state`, `city`, `lat`, `lng` FROM `spa_branch` WHERE   FIND_IN_SET('" . $category_id . "', branch_business_category)  AND user_id='$listing_id' AND id<>'$branch_id'");
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
                // $is_free_trail = $row['is_free_trail'];
                // $from_trail_time = $row['from_trail_time'];
                // $to_trail_time = $row['to_trail_time'];
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

                // $booking_slot_array = array();
                // if ($is_free_trail == '1') {
                //     $from_trail_times = $this->check_time_format($from_trail_time);
                //     $to_trail_times = $this->check_time_format($to_trail_time);
                //     $time_difference = 60;
                //     for ($i = strtotime($from_trail_times); $i <= strtotime($to_trail_times); $i = $i + $time_difference * 60) {
                //         $booking_slot_array[] = array(
                //             'time' => date("h:i A", $i)
                //         );
                //     }
                // }

                // $is_order_query = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category_id' AND user_id='$user_id' AND package_id='100'");
                // $is_order_count = $is_order_query->num_rows();

                // if ($is_order_count > 0) {
                //     $is_trial_order = 1;
                // } else {
                //     $is_trial_order = 0;
                // }

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
                   // 'is_free_trail' => $is_free_trail,
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
    
    public function spa_category(){
        $query = $this->db->query("SELECT * FROM business_category WHERE category_id='36'");
        $count = $query->num_rows();
        if ($count > 0) {
            
            foreach ($query->result_array() as $row) {
                $category_id = $row['id'];
                $category_name = $row['category'];
                $details = $row['details'];
                $result_list[] = array(
                                'category_id' => $category_id,
                                'category_name' => $category_name,
                                'category_detail' => $details
                );
            }
            $result_list = array(
                            'status' => 200,
                            'message' => 'success',
                            'data'=>$result_list
                        );
        }
        else{
            $result_list = array('status' => 201,
                            'message' => 'error');
        }
        return $result_list; 
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
        $this->db->insert('spa_center_review', $review_array);
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
        $review_count = $this->db->select('id')->from('spa_center_review')->where('listing_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            //echo "SELECT fitness_center_review.id,fitness_center_review.user_id,fitness_center_review.listing_id,fitness_center_review.rating,fitness_center_review.review, fitness_center_review.service,fitness_center_review.date as review_date,users.id as user_id,users.name as firstname FROM `fitness_center_review` INNER JOIN `users` ON fitness_center_review.user_id=users.id WHERE fitness_center_review.listing_id='$listing_id' AND fitness_center_review.branch_id='$branch_id' order by fitness_center_review.id desc";
            $query = $this->db->query("SELECT spa_center_review.id,spa_center_review.user_id,spa_center_review.listing_id,spa_center_review.rating,spa_center_review.review, spa_center_review.service,spa_center_review.date as review_date,users.id as user_id,users.name as firstname FROM `spa_center_review` INNER JOIN `users` ON spa_center_review.user_id=users.id WHERE spa_center_review.listing_id='$listing_id' AND spa_center_review.branch_id='$branch_id' order by spa_center_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $user_id = $row['user_id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                //if ($id > '11') {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                // } else {
                //     if (base64_encode(base64_decode($review)) === $review) {
                //         echo $review = base64_decode($review);
                //     }
                // }
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('spa_center_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('spa_center_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('spa_center_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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
    
     public function review_like($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `spa_center_review_likes` WHERE post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `spa_center_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from `spa_center_review_likes` WHERE post_id='$post_id'");
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
            $this->db->insert('spa_center_review_likes', $fitness_review_likes);
            $like_query = $this->db->query("SELECT id from spa_center_review_likes WHERE post_id='$post_id'");
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
        $this->db->insert('spa_center_review_comment', $fitness_review_comment);
        $fitness_review_comment_query = $this->db->query("SELECT id from spa_center_review_comment where post_id='$post_id'");
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
        $count_query = $this->db->query("SELECT id from spa_center_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `spa_center_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from spa_center_review_comment_like where comment_id='$comment_id'");
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
            $this->db->insert('spa_center_review_comment_like', $fitness_review_comment_like);
            $comment_query = $this->db->query("SELECT id from spa_center_review_comment_like where comment_id='$comment_id'");
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

        $review_list_count = $this->db->select('id')->from('spa_center_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT spa_center_review_comment.id,spa_center_review_comment.post_id,spa_center_review_comment.comment as comment,spa_center_review_comment.date,users.name,spa_center_review_comment.user_id as post_user_id FROM spa_center_review_comment INNER JOIN users on users.id=spa_center_review_comment.user_id WHERE spa_center_review_comment.post_id='$post_id' order by spa_center_review_comment.id asc");

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

                $like_count = $this->db->select('id')->from('spa_center_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('spa_center_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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
    
     public function spa_views($user_id, $listing_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $fitness_views_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'date' => $date
        );
        $this->db->insert('spa_views', $fitness_views_array);

        $fitness_views = $this->db->select('id')->from('spa_views')->where('listing_id', $listing_id)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'spa_views' => $fitness_views
        );
    }
}