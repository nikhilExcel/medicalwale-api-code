<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class OpticModel extends CI_Model {

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

    public function check_day_status($day_type, $days_closed, $is_24hrs_available, $store_open, $store_close) {

        if ($is_24hrs_available === 'Yes') {
            if ($day_type == 'Monday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Tuesday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Wednesday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Thursday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Friday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Saturday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Sunday') {
                $time = '12:00 AM-11:59 PM';
            }
        } else {
            if ($day_type == 'Monday') {
                if ($days_closed == 'Monday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Tuesday') {
                if ($days_closed == 'Tuesday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Wednesday') {
                if ($days_closed == 'Wednesday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Thursday') {
                if ($days_closed == 'Thursday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Friday') {
                if ($days_closed == 'Friday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Saturday') {
                if ($days_closed == 'Saturday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Sunday') {
                if ($days_closed == 'Sunday Closed') {
                    $time = 'close-close';
                } elseif ($days_closed == 'Sunday Half Day') {
                    $time = $store_open . '-02:00 PM';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }
        }

        return $time;
    }

    public function check_current_delivery_charges($is_24hrs_available, $day_night_delivery, $free_start_time, $free_end_time, $night_delivery_charge, $days_closed, $is_min_order_delivery, $min_order) {

        date_default_timezone_set('Asia/Kolkata');
        $free_start_time_st = date("h:i A", strtotime($free_start_time));
        $free_end_time_st = date("h:i A", strtotime($free_end_time));
        $current_time_st = date('h:i A');

        $current_time = DateTime::createFromFormat('H:i a', $current_time_st);
        $system_start_time = DateTime::createFromFormat('H:i a', $free_start_time_st);
        $system_end_time = DateTime::createFromFormat('H:i a', $free_end_time_st);

        if ($system_start_time < $system_end_time && $current_time <= $system_end_time) {
            $system_end_time->modify('+1 day')->format('H:i a');
        } elseif ($system_start_time > $system_end_time && $current_time >= $system_end_time) {
            $system_end_time->modify('+1 day')->format('H:i a');
        }

        if ($is_24hrs_available == 'Yes') {
            if ($day_night_delivery == 'Yes') {

                if ($current_time > $system_start_time && $current_time < $system_end_time) {
                    if ($is_min_order_delivery == 'Yes') {
                        $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                    } else {
                        $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                    }
                } else {
                    $current_delivery_charges = 'Delivery Charges Applied Rs ' . $night_delivery_charge;
                }
            } else {
                if ($current_time > $system_start_time && $current_time < $system_end_time) {
                    // $current_delivery_charges = 'Free Delivery Available';	
                    if ($is_min_order_delivery == 'Yes') {
                        $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                    } else {
                        $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                    }
                } else {
                    $current_delivery_charges = 'Delivery Not Available Now';
                }
            }
        } else {

            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                //$current_delivery_charges = 'Free Delivery Available';
                if ($is_min_order_delivery == 'Yes') {
                    $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                } else {
                    $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                }
            } else {
                $current_delivery_charges = 'Delivery Not Available Now';
            }
        }


        return $current_delivery_charges;
    }

    public function check_time_format($time) {
        $time_filter = preg_replace('/\s+/', '', $time);
        $final_time = date("h:i A", strtotime($time_filter));
        return $final_time;
    }

    public function is_free_delivery_staus($free_start_time, $free_end_time) {
        $free_start_time_st = date("h:i A", strtotime($free_start_time));
        $free_end_time_st = date("h:i A", strtotime($free_end_time));
        $current_time_st = date('h:i A');


        $date1 = DateTime::createFromFormat('H:i a', $current_time_st);
        $date2 = DateTime::createFromFormat('H:i a', $free_start_time_st);
        $date3 = DateTime::createFromFormat('H:i a', $free_end_time_st);

        if ($date2 < $date3 && $date1 <= $date3) {
            $date3->modify('+1 day')->format('H:i a');
        } elseif ($date2 > $date3 && $date1 >= $date3) {
            $date3->modify('+1 day')->format('H:i a');
        }


        if ($date1 > $date2 && $date1 < $date3) {
            $is_free_delivery = 'Yes';
        } else {
            $is_free_delivery = 'No';
        }

        return $is_free_delivery;
    }

    public function optic_list($user_id, $mlat, $mlng, $radius) {
        $radius = $radius*50;
        // function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
        //     $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyCMHN__LczEziaYuKXQ-SKdiMfx5AiY66o&callback=initMap";
        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     $response = curl_exec($ch);
        //     curl_close($ch);
        //     $response_a = json_decode($response, true);
        //     $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
        //     return $dist;
        // }

        // $sql = sprintf("SELECT `id`, `user_id`, `name`, `year`, `phone`, `telephone`, `manager`, `designation`, `discount`, `about`, `hours_open`, `address`, `image`, `lat`, `lng`, `reviews`, `ratings`, `state`, `city`, `pincode`, `is_active`, `is_approval`, `reg_date`, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM optic_eyecare_list  WHERE is_active='1' HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
        $sql = "SELECT `id`, `user_id`, `name`, `year`, `phone`, `telephone`, `manager`, `designation`, `discount`, `about`, `hours_open`, `address`, `image`, `lat`, `lng`, `reviews`, `ratings`, `state`, `city`, `pincode`, `is_active`, `is_approval`, `reg_date` FROM `optic_eyecare_list` WHERE discount!='0'";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count>0){
            foreach ($query->result_array() as $row) {
                $lat = $row['lat'];
                $lng = $row['lng'];
                $optic_id = $row['user_id'];
                $manager = $row['manager'];
                $optic_name = $row['name'];
                $address = $row['address'];
                $pincode = $row['pincode'];
                $city = $row['city'];
                $state = $row['state'];
                $phone = $row['phone'];
                $telephone = $row['telephone'];
                $store_since = $row['year'];
                $discount = $row['discount'];
                $hours_open = $row['hours_open'];
                $profile_pic = $row['image'];
                $rating = $row['ratings'];
                
                $resultpost[] = array(
                    'id' => $optic_id,
                    'store_name' => $optic_name,
                    'listing_type' => '17',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'store_manager' => $manager,
                    'address1' => $address,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'phone' => $phone,
                    'telephone' => $telephone,
                    "exotel_no" => '02233721563',
                    'year' => $store_since,
                    'hours_open' => $hours_open,
                    'profile_pic' => $profile_pic,
                    'rating' => (string) $rating,
                    'discount' => $discount,
                );
            }
            return $resultpost;
        }
        else{
            return array();
        }
    }

    public function pharmacy_details($user_id, $listing_id) {
        $query = $this->db->query("SELECT `id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,IFNULL(online_offline,'') AS online_offline FROM medical_stores  WHERE  user_id='$listing_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
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
                $website = $row['website'];
                $reach_area = $row['reach_area'];
                $is_24hrs_available = $row['is_24hrs_available'];
                if ($is_24hrs_available == 'Yes') {
                    $store_open = date("h:i A", strtotime("12:00 AM"));
                    $store_close = date("h:i A", strtotime("11:59 PM"));
                } else {
                    $store_open = $this->check_time_format($row['store_open']);
                    $store_close = $this->check_time_format($row['store_close']);
                }
                $day_night_delivery = $row['day_night_delivery'];
                $free_start_time = $this->check_time_format($row['free_start_time']);
                $free_end_time = $this->check_time_format($row['free_end_time']);
                $is_free_delivery = $this->is_free_delivery_staus($free_start_time, $free_end_time);
                $days_closed = $row['days_closed'];
                $min_order = $row['min_order'];
                $is_min_order_delivery = $row['is_min_order_delivery'];
                $min_order_delivery_charge = $row['min_order_delivery_charge'];
                $night_delivery_charge = $row['night_delivery_charge'];
                $payment_type = $row['payment_type'];
                $online_offline = $row['online_offline'];
                $profile_pic = $row['profile_pic'];
                if ($row['profile_pic'] != '') {
                    $profile_pic = $row['profile_pic'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
                }
                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $medical_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $medical_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $medical_id)->get()->num_rows();
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                $profile_view = $this->db->select('id')->from('pharmacy_view')->where('listing_id', $medical_id)->get()->num_rows();
                $activity_id = '0';
                $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM medical_stores_review WHERE medical_stores_id='$medical_id'");
                $row_pharma = $query_pharmacy->row_array();
                $rating = $row_pharma['avg_rating'];
                if ($rating === NULL) {
                    $rating = '0';
                }
                $review = $this->db->select('id')->from('medical_stores_review')->where('medical_stores_id', $medical_id)->get()->num_rows();
                $chat_id = $row['user_id'];
                $chat_display = $row['medical_name'];
                $is_chat = 'Yes';
                $Monday = $this->check_day_status('Monday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                $Tuesday = $this->check_day_status('Tuesday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                $Wednesday = $this->check_day_status('Wednesday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                $Thursday = $this->check_day_status('Thursday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                $Friday = $this->check_day_status('Friday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                $Saturday = $this->check_day_status('Saturday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                $Sunday = $this->check_day_status('Sunday', $days_closed, $is_24hrs_available, $store_open, $store_close);
                $opening_hours = "Monday>$Monday,Tuesday>$Tuesday,Wednesday>$Wednesday,Thursday>$Thursday,Friday>$Friday,Saturday>$Saturday,Sunday>$Sunday";
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
                $day_array_list = explode(',', $opening_hours);
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
                                    $time = str_replace('close-close', 'close', $time_check);
                                    if ($time == '12:00 AM-11:59 PM') {
                                        $time = '24 hrs open';
                                    }
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
                                        $open_close = 'open';
                                    } else {
                                        $open_close = 'closed';
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
                $current_delivery_charges = $this->check_current_delivery_charges($is_24hrs_available, $day_night_delivery, $free_start_time, $free_end_time, $night_delivery_charge, $days_closed, $is_min_order_delivery, $min_order);
                $product_category_list = array();
                $query_category = $this->db->query("SELECT id,category,image FROM `category` WHERE id<>'3' order by id asc");
                foreach ($query_category->result_array() as $row) {
                    $product_id = $row['id'];
                    $product_category = $row['category'];
                    $product_image = $row['image'];
                    $product_image = str_replace(" ", "", $product_image);
                    $product_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/product_category/' . $product_image;

                    $product_category_list[] = array(
                        "id" => $product_id,
                        "category" => $product_category,
                        'image' => $product_image);
                }
                $resultpost[] = array(
                    'id' => $medical_id,
                    'medical_name' => $medical_name,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'store_manager' => $store_manager,
                    'address1' => $address1,
                    'address2' => $address2,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact_no' => $contact_no,
                    'whatsapp_no' => $whatsapp_no,
                    'email' => $email,
                    'store_since' => $store_since,
                    'website' => $website,
                    'is_24hrs_available' => $is_24hrs_available,
                    'store_open' => $store_open,
                    'store_close' => $store_close,
                    'day_night_delivery' => $day_night_delivery,
                    'free_start_time' => $free_start_time,
                    'free_end_time' => $free_end_time,
                    'is_free_delivery' => $is_free_delivery,
                    'days_closed' => $days_closed,
                    'min_order' => $min_order,
                    'is_min_order_delivery' => $is_min_order_delivery,
                    'min_order_delivery_charge' => $min_order_delivery_charge,
                    'night_delivery_charge' => $night_delivery_charge,
                    'opening_day' => $final_Day,
                    'current_delivery_charges' => $current_delivery_charges,
                    'payment_type' => $payment_type,
                    'online_offline' => $online_offline,
                    'profile_pic' => $profile_pic,
                    'rating' => (string) $rating,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_view' => $profile_view,
                    'activity_id' => $activity_id,
                    'is_follow' => $is_follow,
                    'chat_id' => $chat_id,
                    'chat_display' => $chat_display,
                    'is_chat' => $is_chat,
                    'review' => $review,
                    'category_list' => $product_category_list
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
}
