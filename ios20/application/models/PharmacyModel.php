<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PharmacyModel extends CI_Model {

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

 public function elasticsearchpharmacy($index_id,$keyword,$size){
        $returndoctor = array();
        $perc=array();
        $data = array("query"=>array("match"=>array("medical_name"=>"$keyword" )),"suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"medical_name")))); 
        $data1=json_encode($data);
        $returnresult = $this->elasticsearch->advancedquerypharmacy($index_id,$data1,$size);
        
           if($returnresult['hits']['total'] > 0){
               
                       foreach($returnresult['hits']['hits'] as $hi){
                     
                          $sim = similar_text($hi['_source']['medical_name'], $keyword, $perc[]);
                       }
                    }
                 @$dataperc=max($perc);
        if($dataperc >= 50){
               foreach($returnresult['hits']['hits'] as $hi){
                          $returndoctor[] =$hi['_source'];
                       }
                     
                       return $returndoctor;
                       
                       
                     }else{
     
                        $name=array();
                        foreach($returnresult['suggest']['my-suggestion'] as $a){
                                if(empty($a['options'])){
                                  $name[]=  $a['text'];
                                }else{
                                      $name[]=$a['options'][0]['text'];
                                }
                        }
                        $string_version = implode(' ', $name);
                        $data = array("query"=>array("match"=>array("medical_name"=>"$string_version")));
                        $data1=json_encode($data);
                        $returnresult = $this->elasticsearch->advancedquerypharmacy($index_id,$data1,$size);
                                foreach($returnresult['hits']['hits'] as $hi){
                                          $returndoctor[] = $hi['_source'];
                                       }
                                     
                      return $returndoctor;
            }
       
}


public function pharmacy_list_search($user_id, $mlat, $mlng,$keyword, $page) {
        //$radius = $page*500;
        $size = 10 * $page;
       
       $resultpost_branch=array();
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
            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            return $dist;
        }

        // $sql = sprintf("SELECT mba,recommended,certified,discount_description,medicalwale_discount,otc_discount,ethical_discount,surgical_discount,perscribed_discount,generic_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND visible <> '0' AND type <> 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
         $index_id="pharmacy_all";
         $pharmacy=$this->elasticsearchpharmacy($index_id,$keyword,$size);
        // print_r($pharmacy);
        //$query = $this->db->query($sql);
      //  $count = $query->num_rows();
         $count=count($pharmacy);
        if($count>0){
            foreach ($pharmacy as $row) {
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
                $website = $row['website'];
                $discount = $row['discount'];
                $medicalwale_discount = $row['medicalwale_discount'];
                $discount_description = $row['discount_description'];
                //added by zak for all discount 
                $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $surgical_discount = $row['surgical_discount'];
                $perscribed_discount = $row['perscribed_discount'];
                $mba = $row['mba'];
                $certified = $row['certified'];
                $recommended = $row['recommended'];
                //favourite Pharmacy
                
                $fav_pharmacy = $this->db->select('id')->from('pharmacy_favourite')->where('user_id', $user_id)->where('listing_id', $medical_id)->get()->num_rows();
                
                
              //  echo $otc_discount.$ethical_discount.$generic_discount;
                
                 $offer_dis = array(
                    $perscribed_discount,$generic_discount,$surgical_discount,$ethical_discount,$otc_discount
                    );
                    
                    $offer_key = array('Prescribed Medicines Discount','Generic Medicines','Surgical Product Discount','Wellness/Lifestyle','General Product Discount');
                    
                 //   echo 'array ='.$offer_dis;
                  //  echo 'key ='.$offer_key;
                    for($i = 0; count($offer_dis) > $i; $i++)
                    {
                        if($offer_dis[$i] == '' || $offer_dis[$i] == NULL)
                        {
                            $offer_dis[$i] = '0';
                        }
                        else
                        {
                            
                        }
                        
                        $offer_discount[] = array(
                             'discount_key' => $offer_key[$i],
                             'discount_value' => $offer_dis[$i]
                            );
                        
                    }
                    
                //print_r($discount);
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
                $km = $row['distance'];
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
                
                if($user_id == '')
                {
                    $is_follow = 'No';
                }
                else
                {
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $medical_id)->get()->num_rows();
    
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
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
    
                $meter = '0';
                $distance = $km * 1000;
                $store_distance = round($distance, 2);
                if ($distance > 999) {
                    $distance = $distance / 1000;
                    $meter = round($distance, 2) . ' km';
                } else {
                    $meter = round($distance) . ' meters away';
                }
    
                $reach_area = str_replace(" Mtr", "", $reach_area);
                $reach_area = str_replace(" Km", "", $reach_area);
                if ($reach_area > 10) {
                    $ranges = ($reach_area / 1000);
                } else {
                    $ranges = $reach_area;
                }
    
                $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                
               /* $Dis = explode(' ',$distances);
                
                if($Dis[1]=='m'){*/
           //    echo $distances.'';
                   $distance_root =  round(($distances/1000),1);
               //    echo "=>".$distance_root." , ";
                /*}else{
                    $distance_root = $distances;
                }*/
                
                
                
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
    
              /*  $product_category_list = array();
                $query_category = $this->db->query("SELECT id,category,image FROM `category` WHERE id<>'3' order by id asc");
                foreach ($query_category->result_array() as $row) {
                    $product_id = $row['id'];
                    $product_category = $row['category'];
                    $product_image = $row['image'];
                    $product_image = str_replace(" ", "", $product_image);
                    $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;
    
                    $product_category_list[] = array(
                        "id" => $product_id,
                        "category" => $product_category,
                        'image' => $product_image);
                }*/
  
    $offer_dis_new = array(
                    $generic_discount,$otc_discount,$ethical_discount, $perscribed_discount,$surgical_discount
                    );
       $product_category_list = array();            
    $query_category = $this->db->query("SELECT id,category,image FROM `category` where parent_id=0 order by id asc");
    $i=0;
        foreach ($query_category->result_array() as $row) {
            $product_id = $row['id'];
            $product_category = $row['category'];
            $product_image = $row['image'];
            $product_image = str_replace(" ", "", $product_image);
            $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;

           $sub_category=array();
           $query_category1 = $this->db->query("SELECT id,category,image FROM `category` where parent_id='$product_id' order by id asc");
        foreach ($query_category1->result_array() as $row1) {
            $product_id1 = $row1['id'];
            $product_category1 = $row1['category'];
            $product_image1 = $row1['image'];
            $product_image1 = str_replace(" ", "", $product_image1);
            $product_image1 = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image1;
            
           $sub_category[] = array(
                "id" => $product_id1,
                "category" => $product_category1,
                "image" => $product_image1
               
            ); 
        }


            $product_category_list[] = array(
                "id" => $product_id,
                "category" => $product_category,
                "image" => $product_image,
                "discount"=>$offer_dis_new[$i],
                "sub_category"=>$sub_category
            );
            $i++;
        }
    
    
    
    
    
    
    
    
    
    
                  $FINAL_RESULT = array();
                    
                $resultpost[] = array(
                    'id' => $medical_id,
                    'medical_name' => $medical_name,
                    'listing_id' => $chat_id,
                    'listing_type' => '13',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'store_manager' => $store_manager,
                    'address1' => $address1,
                    'address2' => "",
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact_no' => $contact_no,
                    'whatsapp_no' => $whatsapp_no,
                    "exotel_no" => '02233721563',
                    'email' => $email,
                    'store_since' => $store_since,
                    'website' => $website,
                    'reach_area' => $ranges,
                    'store_distance' => $store_distance,
                    'Description' => $discount_description,
                    'distance' => $distance_root,
                    'distance_root'=>$distance_root,
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
                    'discount' => $discount,
                    'medicalwale_discount' => $medicalwale_discount,
                    'category_list' => $product_category_list,
                    'offer_discount' => $offer_discount,
                    'favourite' => $fav_pharmacy,
                    'mba' => $mba,
                    'certified' => $certified,
                    'recommended' => $recommended
                );
            }
           
            //added for generico pharmacy branch 
            
            $radius_branch = '4';
            
           // $sql_branch = sprintf("SELECT medicalwale_discount,`id`, `pharmacy_branch_user_id` as user_id, `email`,`branch_name` as medical_name, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`,`branch_contact_no` as contact_no, IFNULL(branch_whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM pharmacy_branch  WHERE is_active='1' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius_branch));
  //  echo 'sql'.sprintf("SELECT discount_description,medicalwale_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_active= '1' AND visible = '1' AND user_type = 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius_branch));
     //$sql_branch = sprintf("SELECT mba,recommended,certified,user_id,discount_description,medicalwale_discount,`id`, surgical_discount,perscribed_discount,`pharmacy_branch_user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance,otc_discount,ethical_discount,generic_discount  FROM medical_stores  WHERE is_active= '1' AND visible = '1' AND type = 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius_branch));
      
       $index_id="pharmacy_type_branch";
         $pharmacy_gen=$this->elasticsearchpharmacy($index_id,$keyword,$size);
         $count_branch=count($pharmacy_gen);
       // $query_branch = $this->db->query($sql_branch);
        //$count_branch = $query_branch->num_rows();
        if($count_branch>0){
           foreach ($pharmacy_gen as $row) {
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
                $store_since = $row['store_since'];
                $website = $row['website'];
                $discount = $row['discount'];
                $medicalwale_discount = $row['medicalwale_discount'];
                //print_r($discount);
                
                  //added by zak for all discount 
                $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $surgical_discount = $row['surgical_discount'];
                $perscribed_discount = $row['perscribed_discount'];
                $mba = $row['mba'];
                $certified = $row['certified'];
                $recommended = $row['recommended'];
                 //favourite branch Pharmcy
                            $fav_branch_pharmacy = $this->db->select('id')->from('pharmacy_favourite')->where('user_id', $user_id)->where('listing_id', $medical_id)->get()->num_rows();
                            
              //  echo $otc_discount.$ethical_discount.$generic_discount;
                
                $offer_dis = array(
                    $perscribed_discount,$generic_discount,$surgical_discount,$ethical_discount,$otc_discount
                    );
                    
                    $offer_key = array('Prescribed Medicines Discount','Generic Medicines','Surgical Product Discount','Wellness/Lifestyle','General Product Discount');
                    
                 //   echo 'array ='.$offer_dis;
                  //  echo 'key ='.$offer_key;
                    for($i = 0; count($offer_dis) > $i; $i++)
                    {
                        if($offer_dis[$i] == '' || $offer_dis[$i] == NULL)
                        {
                            $offer_dis[$i] = '0';
                        }
                        else
                        {
                            
                        }
                        
                        $offer_discount[] = array(
                             'discount_key' => $offer_key[$i],
                             'discount_value' => $offer_dis[$i]
                            );
                        
                    }
                    
                
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
                $km = $row['distance'];
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
    
                $chat_id = $row['pharmacy_branch_user_id'];
                $chat_display = $row['medical_name'];
                $is_chat = 'Yes';
    
                $meter = '0';
                $distance = $km * 1000;
                $store_distance = round($distance, 2);
                if ($distance > 999) {
                    $distance = $distance / 1000;
                    $meter = round($distance, 2) . ' km';
                } else {
                    $meter = round($distance) . ' meters away';
                }
    
                $reach_area = str_replace(" Mtr", "", $reach_area);
                $reach_area = str_replace(" Km", "", $reach_area);
                if ($reach_area > 10) {
                    $ranges = ($reach_area / 1000);
                } else {
                    $ranges = $reach_area;
                }
    
                $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
    
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
    
                $offer_dis_new = array(
                    $generic_discount,$otc_discount,$ethical_discount, $perscribed_discount,$surgical_discount
                    );
       $product_category_list = array();            
    $query_category = $this->db->query("SELECT id,category,image FROM `category` where parent_id=0 order by id asc");
    $i=0;
        foreach ($query_category->result_array() as $row) {
            $product_id = $row['id'];
            $product_category = $row['category'];
            $product_image = $row['image'];
            $product_image = str_replace(" ", "", $product_image);
            $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;

           $sub_category=array();
           $query_category1 = $this->db->query("SELECT id,category,image FROM `category` where parent_id='$product_id' order by id asc");
        foreach ($query_category1->result_array() as $row1) {
            $product_id1 = $row1['id'];
            $product_category1 = $row1['category'];
            $product_image1 = $row1['image'];
            $product_image1 = str_replace(" ", "", $product_image1);
            $product_image1 = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image1;
            
           $sub_category[] = array(
                "id" => $product_id1,
                "category" => $product_category1,
                "image" => $product_image1
               
            ); 
        }


            $product_category_list[] = array(
                "id" => $product_id,
                "category" => $product_category,
                "image" => $product_image,
                "discount"=>$offer_dis_new[$i],
                "sub_category"=>$sub_category
            );
            $i++;
        }
    
    
    
                $resultpost_branch[] = array(
                    'id' => $medical_id,
                    'medical_name' => $medical_name,
                    'listing_id' => $chat_id,
                    'listing_type' => '13',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'store_manager' => $store_manager,
                    'address1' => $address1,
                    'address2' => "",
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact_no' => $contact_no,
                    'whatsapp_no' => $whatsapp_no,
                    "exotel_no" => '02233721563',
                    'email' => $email,
                    'store_since' => $store_since,
                    'website' => $website,
                    'reach_area' => $ranges,
                    'store_distance' => $store_distance,
                    'Description' => $discount_description,
                    'distance' => $distance_root,
                    'distance_root'=>$distance_root,
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
                    'discount' => $discount,
                    'medicalwale_discount' => $medicalwale_discount,
                    'category_list' => $product_category_list,
                    'offer_discount' => $offer_discount,
                    'favourite' => $fav_branch_pharmacy,
                    'mba' => $mba,
                    'certified' => $certified,
                    'recommended' => $recommended
                );
                
               
               // array_push($resultpost,$resultpost_branch);
            } 
        }
       
      
      
          
            //added by zak for distance sorting
                usort($resultpost, function($a, $b) {
                                $a = $a['distance_root'];
                                $b = $b['distance_root'];
                    if ($a == $b) { return 0; }
                        return ($a < $b) ? -1 : 1;
                    });
                    
                    //$resultpost = array_reverse($resultpost);
                    
       if($resultpost_branch!="")
      {
          //echo 'no branch';
          $resultpost  =  array_merge($resultpost_branch,$resultpost);
      }
      else
      {
        $resultpost  =  $resultpost;  
      }
               
            return $resultpost;
        }
        else{
          return array();
            
        }
    }

    //added by zak for generico pharmacy dnt touch 
    public function pharmacy_list($user_id, $mlat, $mlng, $page) {
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
            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            return $dist;
        }

         $sql = sprintf("SELECT mba,recommended,certified,discount_description,medicalwale_discount,otc_discount,ethical_discount,surgical_discount,perscribed_discount,generic_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND visible <> '0' AND type <> 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
     
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
                $website = $row['website'];
                $discount = $row['discount'];
                $medicalwale_discount = $row['medicalwale_discount'];
                $discount_description = $row['discount_description'];
                //added by zak for all discount 
                $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $surgical_discount = $row['surgical_discount'];
                $perscribed_discount = $row['perscribed_discount'];
                $mba = $row['mba'];
                $certified = $row['certified'];
                $recommended = $row['recommended'];
                //favourite Pharmacy
                
                $fav_pharmacy = $this->db->select('id')->from('pharmacy_favourite')->where('user_id', $user_id)->where('listing_id', $medical_id)->get()->num_rows();
                
                
              //  echo $otc_discount.$ethical_discount.$generic_discount;
                
                 $offer_dis = array(
                    $perscribed_discount,$generic_discount,$surgical_discount,$ethical_discount,$otc_discount
                    );
                    
                    $offer_key = array('Prescribed Medicines Discount','Generic Medicines','Surgical Product Discount','Wellness/Lifestyle','General Product Discount');
                    
                 //   echo 'array ='.$offer_dis;
                  //  echo 'key ='.$offer_key;
                    for($i = 0; count($offer_dis) > $i; $i++)
                    {
                        if($offer_dis[$i] == '' || $offer_dis[$i] == NULL)
                        {
                            $offer_dis[$i] = '0';
                        }
                        else
                        {
                            
                        }
                        
                        $offer_discount[] = array(
                             'discount_key' => $offer_key[$i],
                             'discount_value' => $offer_dis[$i]
                            );
                        
                    }
                    
                //print_r($discount);
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
                $km = $row['distance'];
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
                
                if($user_id == '')
                {
                    $is_follow = 'No';
                }
                else
                {
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $medical_id)->get()->num_rows();
    
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
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
    
                $meter = '0';
                $distance = $km * 1000;
                $store_distance = round($distance, 2);
                if ($distance > 999) {
                    $distance = $distance / 1000;
                    $meter = round($distance, 2) . ' km';
                } else {
                    $meter = round($distance) . ' meters away';
                }
    
                $reach_area = str_replace(" Mtr", "", $reach_area);
                $reach_area = str_replace(" Km", "", $reach_area);
                if ($reach_area > 10) {
                    $ranges = ($reach_area / 1000);
                } else {
                    $ranges = $reach_area;
                }
    
                $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                
               /* $Dis = explode(' ',$distances);
                
                if($Dis[1]=='m'){*/
           //    echo $distances.'';
                   $distance_root =  round(($distances/1000),1);
               //    echo "=>".$distance_root." , ";
                /*}else{
                    $distance_root = $distances;
                }*/
                
                
                
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
                    $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;
    
                    $product_category_list[] = array(
                        "id" => $product_id,
                        "category" => $product_category,
                        'image' => $product_image);
                }
    
    
                  $FINAL_RESULT = array();
                    
                $resultpost[] = array(
                    'id' => $medical_id,
                    'medical_name' => $medical_name,
                    'listing_id' => $chat_id,
                    'listing_type' => '13',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'store_manager' => $store_manager,
                    'address1' => $address1,
                    'address2' => "",
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact_no' => $contact_no,
                    'whatsapp_no' => $whatsapp_no,
                    "exotel_no" => '02233721563',
                    'email' => $email,
                    'store_since' => $store_since,
                    'website' => $website,
                    'reach_area' => $ranges,
                    'store_distance' => $store_distance,
                    'Description' => $discount_description,
                    'distance' => $distance_root,
                    'distance_root'=>$distance_root,
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
                    'discount' => $discount,
                    'medicalwale_discount' => $medicalwale_discount,
                    'category_list' => $product_category_list,
                    'offer_discount' => $offer_discount,
                    'favourite' => $fav_pharmacy,
                    'mba' => $mba,
                    'certified' => $certified,
                    'recommended' => $recommended
                );
            }
           
            //added for generico pharmacy branch 
            
            $radius_branch = '4';
            
           // $sql_branch = sprintf("SELECT medicalwale_discount,`id`, `pharmacy_branch_user_id` as user_id, `email`,`branch_name` as medical_name, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`,`branch_contact_no` as contact_no, IFNULL(branch_whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM pharmacy_branch  WHERE is_active='1' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius_branch));
  //  echo 'sql'.sprintf("SELECT discount_description,medicalwale_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_active= '1' AND visible = '1' AND user_type = 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius_branch));
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
                $store_since = $row['store_since'];
                $website = $row['website'];
                $discount = $row['discount'];
                $medicalwale_discount = $row['medicalwale_discount'];
                //print_r($discount);
                
                  //added by zak for all discount 
                $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $surgical_discount = $row['surgical_discount'];
                $perscribed_discount = $row['perscribed_discount'];
                $mba = $row['mba'];
                $certified = $row['certified'];
                $recommended = $row['recommended'];
                 //favourite branch Pharmcy
                            $fav_branch_pharmacy = $this->db->select('id')->from('pharmacy_favourite')->where('user_id', $user_id)->where('listing_id', $medical_id)->get()->num_rows();
                            
              //  echo $otc_discount.$ethical_discount.$generic_discount;
                
                $offer_dis = array(
                    $perscribed_discount,$generic_discount,$surgical_discount,$ethical_discount,$otc_discount
                    );
                    
                    $offer_key = array('Prescribed Medicines Discount','Generic Medicines','Surgical Product Discount','Wellness/Lifestyle','General Product Discount');
                    
                 //   echo 'array ='.$offer_dis;
                  //  echo 'key ='.$offer_key;
                    for($i = 0; count($offer_dis) > $i; $i++)
                    {
                        if($offer_dis[$i] == '' || $offer_dis[$i] == NULL)
                        {
                            $offer_dis[$i] = '0';
                        }
                        else
                        {
                            
                        }
                        
                        $offer_discount[] = array(
                             'discount_key' => $offer_key[$i],
                             'discount_value' => $offer_dis[$i]
                            );
                        
                    }
                    
                
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
                $km = $row['distance'];
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
    
                $chat_id = $row['pharmacy_branch_user_id'];
                $chat_display = $row['medical_name'];
                $is_chat = 'Yes';
    
                $meter = '0';
                $distance = $km * 1000;
                $store_distance = round($distance, 2);
                if ($distance > 999) {
                    $distance = $distance / 1000;
                    $meter = round($distance, 2) . ' km';
                } else {
                    $meter = round($distance) . ' meters away';
                }
    
                $reach_area = str_replace(" Mtr", "", $reach_area);
                $reach_area = str_replace(" Km", "", $reach_area);
                if ($reach_area > 10) {
                    $ranges = ($reach_area / 1000);
                } else {
                    $ranges = $reach_area;
                }
    
                $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
    
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
                    $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;
    
                    $product_category_list[] = array(
                        "id" => $product_id,
                        "category" => $product_category,
                        'image' => $product_image);
                }
    
    
    
                $resultpost_branch[] = array(
                    'id' => $medical_id,
                    'medical_name' => $medical_name,
                    'listing_id' => $chat_id,
                    'listing_type' => '13',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'store_manager' => $store_manager,
                    'address1' => $address1,
                    'address2' => "",
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact_no' => $contact_no,
                    'whatsapp_no' => $whatsapp_no,
                    "exotel_no" => '02233721563',
                    'email' => $email,
                    'store_since' => $store_since,
                    'website' => $website,
                    'reach_area' => $ranges,
                    'store_distance' => $store_distance,
                    'Description' => $discount_description,
                    'distance' => $distance_root,
                    'distance_root'=>$distance_root,
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
                    'discount' => $discount,
                    'medicalwale_discount' => $medicalwale_discount,
                    'category_list' => $product_category_list,
                    'offer_discount' => $offer_discount,
                    'favourite' => $fav_branch_pharmacy,
                    'mba' => $mba,
                    'certified' => $certified,
                    'recommended' => $recommended
                );
                
               
               // array_push($resultpost,$resultpost_branch);
            } 
        }
       
      
      
          
            //added by zak for distance sorting
                usort($resultpost, function($a, $b) {
                                $a = $a['distance_root'];
                                $b = $b['distance_root'];
                    if ($a == $b) { return 0; }
                        return ($a < $b) ? -1 : 1;
                    });
                    
                    //$resultpost = array_reverse($resultpost);
                    
       if($resultpost_branch!="")
      {
          //echo 'no branch';
          $resultpost  =  array_merge($resultpost_branch,$resultpost);
      }
      else
      {
        $resultpost  =  $resultpost;  
      }
               
            return $resultpost;
        }
        else{
          return array();
            
        }
    }






 public function pharmacy_list_v2($user_id, $mlat, $mlng, $page) {
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
            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            return $dist;
        }

         $sql = sprintf("SELECT mba,recommended,certified,discount_description,medicalwale_discount,otc_discount,ethical_discount,surgical_discount,perscribed_discount,generic_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND visible <> '0' AND type <> 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
     
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
                $website = $row['website'];
                $discount = $row['discount'];
                $medicalwale_discount = $row['medicalwale_discount'];
                $discount_description = $row['discount_description'];
                //added by zak for all discount 
                $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $surgical_discount = $row['surgical_discount'];
                $perscribed_discount = $row['perscribed_discount'];
                $mba = $row['mba'];
                $certified = $row['certified'];
                $recommended = $row['recommended'];
                //favourite Pharmacy
                
                $fav_pharmacy = $this->db->select('id')->from('pharmacy_favourite')->where('user_id', $user_id)->where('listing_id', $medical_id)->get()->num_rows();
                
                
              //  echo $otc_discount.$ethical_discount.$generic_discount;
                
                 $offer_dis = array(
                    $perscribed_discount,$generic_discount,$surgical_discount,$ethical_discount,$otc_discount
                    );
                    
                    $offer_key = array('Prescribed Medicines Discount','Generic Medicines','Surgical Product Discount','Wellness/Lifestyle','General Product Discount');
                    
                 //   echo 'array ='.$offer_dis;
                  //  echo 'key ='.$offer_key;
                    for($i = 0; count($offer_dis) > $i; $i++)
                    {
                        if($offer_dis[$i] == '' || $offer_dis[$i] == NULL)
                        {
                            $offer_dis[$i] = '0';
                        }
                        else
                        {
                            
                        }
                        
                        $offer_discount[] = array(
                             'discount_key' => $offer_key[$i],
                             'discount_value' => $offer_dis[$i]
                            );
                        
                    }
                    
                //print_r($discount);
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
                $km = $row['distance'];
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
                
                if($user_id == '')
                {
                    $is_follow = 'No';
                }
                else
                {
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $medical_id)->get()->num_rows();
    
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
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
    
                $meter = '0';
                $distance = $km * 1000;
                $store_distance = round($distance, 2);
                if ($distance > 999) {
                    $distance = $distance / 1000;
                    $meter = round($distance, 2) . ' km';
                } else {
                    $meter = round($distance) . ' meters away';
                }
    
                $reach_area = str_replace(" Mtr", "", $reach_area);
                $reach_area = str_replace(" Km", "", $reach_area);
                if ($reach_area > 10) {
                    $ranges = ($reach_area / 1000);
                } else {
                    $ranges = $reach_area;
                }
    
                $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                
               /* $Dis = explode(' ',$distances);
                
                if($Dis[1]=='m'){*/
           //    echo $distances.'';
                   $distance_root =  round(($distances/1000),1);
               //    echo "=>".$distance_root." , ";
                /*}else{
                    $distance_root = $distances;
                }*/
                
                
                
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
    
              /*  $product_category_list = array();
                $query_category = $this->db->query("SELECT id,category,image FROM `category` WHERE id<>'3' order by id asc");
                foreach ($query_category->result_array() as $row) {
                    $product_id = $row['id'];
                    $product_category = $row['category'];
                    $product_image = $row['image'];
                    $product_image = str_replace(" ", "", $product_image);
                    $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;
    
                    $product_category_list[] = array(
                        "id" => $product_id,
                        "category" => $product_category,
                        'image' => $product_image);
                }*/
  
    $offer_dis_new = array(
                    $generic_discount,$otc_discount,$ethical_discount, $perscribed_discount,$surgical_discount
                    );
       $product_category_list = array();            
    $query_category = $this->db->query("SELECT id,category,image FROM `category` where parent_id=0  order by id asc");
    $i=0;
        foreach ($query_category->result_array() as $row) {
            $product_id = $row['id'];
            $product_category = $row['category'];
            $product_image = $row['image'];
            $product_image = str_replace(" ", "", $product_image);
            $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;

           $sub_category=array();
           $query_category1 = $this->db->query("SELECT id,category,image FROM `category` where parent_id='$product_id' order by id asc");
        foreach ($query_category1->result_array() as $row1) {
            $product_id1 = $row1['id'];
            $product_category1 = $row1['category'];
            $product_image1 = $row1['image'];
            $product_image1 = str_replace(" ", "", $product_image1);
            $product_image1 = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image1;
            
           $sub_category[] = array(
                "id" => $product_id1,
                "category" => $product_category1,
                "image" => $product_image1
               
            ); 
        }


            $product_category_list[] = array(
                "id" => $product_id,
                "category" => $product_category,
                "image" => $product_image,
                "discount"=>$offer_dis_new[$i],
                "sub_category"=>$sub_category
            );
            $i++;
        }
    
    
    
    
    
    
    
    
    
    
                  $FINAL_RESULT = array();
                    
                $resultpost[] = array(
                    'id' => $medical_id,
                    'medical_name' => $medical_name,
                    'listing_id' => $chat_id,
                    'listing_type' => '13',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'store_manager' => $store_manager,
                    'address1' => $address1,
                    'address2' => "",
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact_no' => $contact_no,
                    'whatsapp_no' => $whatsapp_no,
                    "exotel_no" => '02233721563',
                    'email' => $email,
                    'store_since' => $store_since,
                    'website' => $website,
                    'reach_area' => $ranges,
                    'store_distance' => $store_distance,
                    'Description' => $discount_description,
                    'distance' => $distance_root,
                    'distance_root'=>$distance_root,
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
                    'discount' => $discount,
                    'medicalwale_discount' => $medicalwale_discount,
                    'category_list' => $product_category_list,
                    'offer_discount' => $offer_discount,
                    'favourite' => $fav_pharmacy,
                    'mba' => $mba,
                    'certified' => $certified,
                    'recommended' => $recommended
                );
            }
           
            //added for generico pharmacy branch 
            
            $radius_branch = '4';
            
           // $sql_branch = sprintf("SELECT medicalwale_discount,`id`, `pharmacy_branch_user_id` as user_id, `email`,`branch_name` as medical_name, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`,`branch_contact_no` as contact_no, IFNULL(branch_whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM pharmacy_branch  WHERE is_active='1' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius_branch));
  //  echo 'sql'.sprintf("SELECT discount_description,medicalwale_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_active= '1' AND visible = '1' AND user_type = 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius_branch));
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
                $store_since = $row['store_since'];
                $website = $row['website'];
                $discount = $row['discount'];
                $medicalwale_discount = $row['medicalwale_discount'];
                //print_r($discount);
                
                  //added by zak for all discount 
                $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $surgical_discount = $row['surgical_discount'];
                $perscribed_discount = $row['perscribed_discount'];
                $mba = $row['mba'];
                $certified = $row['certified'];
                $recommended = $row['recommended'];
                 //favourite branch Pharmcy
                            $fav_branch_pharmacy = $this->db->select('id')->from('pharmacy_favourite')->where('user_id', $user_id)->where('listing_id', $medical_id)->get()->num_rows();
                            
              //  echo $otc_discount.$ethical_discount.$generic_discount;
                
                $offer_dis = array(
                    $perscribed_discount,$generic_discount,$surgical_discount,$ethical_discount,$otc_discount
                    );
                    
                    $offer_key = array('Prescribed Medicines Discount','Generic Medicines','Surgical Product Discount','Wellness/Lifestyle','General Product Discount');
                    
                 //   echo 'array ='.$offer_dis;
                  //  echo 'key ='.$offer_key;
                    for($i = 0; count($offer_dis) > $i; $i++)
                    {
                        if($offer_dis[$i] == '' || $offer_dis[$i] == NULL)
                        {
                            $offer_dis[$i] = '0';
                        }
                        else
                        {
                            
                        }
                        
                        $offer_discount[] = array(
                             'discount_key' => $offer_key[$i],
                             'discount_value' => $offer_dis[$i]
                            );
                        
                    }
                    
                
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
                $km = $row['distance'];
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
    
                $chat_id = $row['pharmacy_branch_user_id'];
                $chat_display = $row['medical_name'];
                $is_chat = 'Yes';
    
                $meter = '0';
                $distance = $km * 1000;
                $store_distance = round($distance, 2);
                if ($distance > 999) {
                    $distance = $distance / 1000;
                    $meter = round($distance, 2) . ' km';
                } else {
                    $meter = round($distance) . ' meters away';
                }
    
                $reach_area = str_replace(" Mtr", "", $reach_area);
                $reach_area = str_replace(" Km", "", $reach_area);
                if ($reach_area > 10) {
                    $ranges = ($reach_area / 1000);
                } else {
                    $ranges = $reach_area;
                }
    
                $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
    
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
    
                $offer_dis_new = array(
                    $generic_discount,$otc_discount,$ethical_discount, $perscribed_discount,$surgical_discount
                    );
       $product_category_list = array();            
    $query_category = $this->db->query("SELECT id,category,image FROM `category` where parent_id=0 order by id asc");
    $i=0;
        foreach ($query_category->result_array() as $row) {
            $product_id = $row['id'];
            $product_category = $row['category'];
            $product_image = $row['image'];
            $product_image = str_replace(" ", "", $product_image);
            $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;

           $sub_category=array();
           $query_category1 = $this->db->query("SELECT id,category,image FROM `category` where parent_id='$product_id' order by id asc");
        foreach ($query_category1->result_array() as $row1) {
            $product_id1 = $row1['id'];
            $product_category1 = $row1['category'];
            $product_image1 = $row1['image'];
            $product_image1 = str_replace(" ", "", $product_image1);
            $product_image1 = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image1;
            
           $sub_category[] = array(
                "id" => $product_id1,
                "category" => $product_category1,
                "image" => $product_image1
               
            ); 
        }


            $product_category_list[] = array(
                "id" => $product_id,
                "category" => $product_category,
                "image" => $product_image,
                "discount"=>$offer_dis_new[$i],
                "sub_category"=>$sub_category
            );
            $i++;
        }
    
    
    
                $resultpost_branch[] = array(
                    'id' => $medical_id,
                    'medical_name' => $medical_name,
                    'listing_id' => $chat_id,
                    'listing_type' => '13',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'store_manager' => $store_manager,
                    'address1' => $address1,
                    'address2' => "",
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact_no' => $contact_no,
                    'whatsapp_no' => $whatsapp_no,
                    "exotel_no" => '02233721563',
                    'email' => $email,
                    'store_since' => $store_since,
                    'website' => $website,
                    'reach_area' => $ranges,
                    'store_distance' => $store_distance,
                    'Description' => $discount_description,
                    'distance' => $distance_root,
                    'distance_root'=>$distance_root,
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
                    'discount' => $discount,
                    'medicalwale_discount' => $medicalwale_discount,
                    'category_list' => $product_category_list,
                    'offer_discount' => $offer_discount,
                    'favourite' => $fav_branch_pharmacy,
                    'mba' => $mba,
                    'certified' => $certified,
                    'recommended' => $recommended
                );
                
               
               // array_push($resultpost,$resultpost_branch);
            } 
        }
       
      
      
          
            //added by zak for distance sorting
                usort($resultpost, function($a, $b) {
                                $a = $a['distance_root'];
                                $b = $b['distance_root'];
                    if ($a == $b) { return 0; }
                        return ($a < $b) ? -1 : 1;
                    });
                    
                    //$resultpost = array_reverse($resultpost);
                    
       if($resultpost_branch!="")
      {
          //echo 'no branch';
          $resultpost  =  array_merge($resultpost_branch,$resultpost);
      }
      else
      {
        $resultpost  =  $resultpost;  
      }

         
            //$resultpost1=$this->pharmacy_1mg($user_id, $mlat, $mlng, $page);
          
          //$resultpost  =  array_merge($resultpost1,$resultpost);




               
            return $resultpost;
        }
        else{
          return array();
            
        }
    }
    
    public function pharmacy_1mg($user_id, $mlat, $mlng, $page)
    {
          $sql1 = sprintf("SELECT mba,recommended,certified,discount_description,medicalwale_discount,otc_discount,ethical_discount,surgical_discount,perscribed_discount,generic_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline FROM medical_stores  WHERE user_id='50098'");
     
        $query1 = $this->db->query($sql1);
        $count1 = $query1->num_rows();
        
            $row=$query1->row_array() ;
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
                $website = $row['website'];
                $discount = $row['discount'];
                $medicalwale_discount = $row['medicalwale_discount'];
                $discount_description = $row['discount_description'];
                //added by zak for all discount 
                $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $surgical_discount = $row['surgical_discount'];
                $perscribed_discount = $row['perscribed_discount'];
                $mba = $row['mba'];
                $certified = $row['certified'];
                $recommended = $row['recommended'];
                //favourite Pharmacy
                
                $fav_pharmacy = $this->db->select('id')->from('pharmacy_favourite')->where('user_id', $user_id)->where('listing_id', $medical_id)->get()->num_rows();
                
                
              //  echo $otc_discount.$ethical_discount.$generic_discount;
                
                 $offer_dis = array(
                    $perscribed_discount,$generic_discount,$surgical_discount,$ethical_discount,$otc_discount
                    );
                    
                    $offer_key = array('Prescribed Medicines Discount','Generic Medicines','Surgical Product Discount','Wellness/Lifestyle','General Product Discount');
                    
                 //   echo 'array ='.$offer_dis;
                  //  echo 'key ='.$offer_key;
                    for($i = 0; count($offer_dis) > $i; $i++)
                    {
                        if($offer_dis[$i] == '' || $offer_dis[$i] == NULL)
                        {
                            $offer_dis[$i] = '0';
                        }
                        else
                        {
                            
                        }
                        
                        $offer_discount[] = array(
                             'discount_key' => $offer_key[$i],
                             'discount_value' => $offer_dis[$i]
                            );
                        
                    }
                    
                //print_r($discount);
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
                $km = 0.9;
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
                
                if($user_id == '')
                {
                    $is_follow = 'No';
                }
                else
                {
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $medical_id)->get()->num_rows();
    
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
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
    
                $meter = '0';
                $distance = $km * 1000;
                $store_distance = round($distance, 2);
                if ($distance > 999) {
                    $distance = $distance / 1000;
                    $meter = round($distance, 2) . ' km';
                } else {
                    $meter = round($distance) . ' meters away';
                }
    
                $reach_area = str_replace(" Mtr", "", $reach_area);
                $reach_area = str_replace(" Km", "", $reach_area);
                if ($reach_area > 10) {
                    $ranges = ($reach_area / 1000);
                } else {
                    $ranges = $reach_area;
                }
    
                $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                
               /* $Dis = explode(' ',$distances);
                
                if($Dis[1]=='m'){*/
           //    echo $distances.'';
                   $distance_root =  round(($distances/1000),1);
               //    echo "=>".$distance_root." , ";
                /*}else{
                    $distance_root = $distances;
                }*/
                
                
                
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
                    $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;
    
                    $product_category_list[] = array(
                        "id" => $product_id,
                        "category" => $product_category,
                        'image' => $product_image);
                }
    
    
                  $FINAL_RESULT = array();
                    
                $resultpost1[] = array(
                    'id' => $medical_id,
                    'medical_name' => $medical_name,
                    'listing_id' => $chat_id,
                    'listing_type' => '13',
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
                    "exotel_no" => '02233721563',
                    'email' => $email,
                    'store_since' => $store_since,
                    'website' => $website,
                    'reach_area' => $ranges,
                    'store_distance' => $store_distance,
                    'Description' => $discount_description,
                    'distance' => $distance_root,
                    'distance_root'=>$distance_root,
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
                    'discount' => $discount,
                    'medicalwale_discount' => $medicalwale_discount,
                    'category_list' => $product_category_list,
                    'offer_discount' => $offer_discount,
                    'favourite' => $fav_pharmacy,
                    'mba' => $mba,
                    'certified' => $certified,
                    'recommended' => $recommended
                );
                return $resultpost1;
    } 


   public function pharmacy_front_v1($user_id, $mlat, $mlng) 
   {
        $radius = 500;
        
       $insta_order=array();
       $select_order=array();
       $exclusives=array();
        $sql = "SELECT * FROM insta_order";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count>0)
          {
            foreach ($query->result_array() as $row) 
                    {
                      $id=$row['id'];    
                      $image=$row['image'];
                      //$image=""; 
                      $name=$row['name'];
                      $text=$row['text'];
                      $percent=$row['percent'];
                      $flat=$row['flat'];
                      $off=$row['off'];
                      $type=$row['type'];
                      $info=$row['info'];
                      $ftype=$row['flow_type'];
                      
                      if($row['insta_select']=="0")
                      {
                      $insta_order[] = array('id'=> $id,
                                             'image' => $image,
                                             'name' => $name,
                                             'message' => $text,
                                             'flat' => $flat,
                                             'percent' => $percent,
                                             'off' => $off,
                                             'type'=>$type,
                                             'info'=>$info
                                     );
                      }
                      elseif($row['insta_select']=="1")
                      {
                          $user_id='0';
                          $count2='0';
                         
                         if($type=="4")
                         {
                            $sql1="SELECT MAX(discount) as disc FROM medical_stores  WHERE is_active= '1'" ;    
                            $query1 = $this->db->query($sql1);
                            $row1=$query1->row_array();  
                           $percent=$row1['disc']." %";    
                         }
                          elseif($type=="6")
                         {
                           
                           $user_id="50098";    
                         }
                         elseif($type=="7")
                         {
                             
                              $sql2 = "SELECT `lat`, `lng`, `profile_pic`, `discount`,pharmacy_branch_user_id,(6371 * acos ( cos ( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) )+ sin ( radians($mlat) )* sin( radians( lat ) ))
) AS distance FROM medical_stores  WHERE user_id='33569' and pharmacy_branch_user_id!='0' HAVING distance < '100' ORDER BY distance LIMIT 0,1";
                             
                              $query2 = $this->db->query($sql2);
                               $count2 = $query2->num_rows();
                              if($count2>0)
                                {
                                   $row2=$query2->row_array();  
                                   $percent="80 %";  
                                   $flat="up to";
                                   $off="off";
                                   $user_id=$row2['pharmacy_branch_user_id'];
                                } 
                                else
                                {
                                    $percent="";
                                }
                         }
                         elseif($type=="11")
                          {
                            $user_id="60566";  
                          }
                          elseif($type=="12")
                         {
                             
                              $sql2 = "SELECT `lat`, `lng`, `profile_pic`, `discount`,pharmacy_branch_user_id,(6371 * acos ( cos ( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) )+ sin ( radians($mlat) )* sin( radians( lat ) ))
) AS distance FROM medical_stores  WHERE user_id='51788' and pharmacy_branch_user_id!='0' HAVING distance < '100' ORDER BY distance LIMIT 0,1";
                             
                              $query2 = $this->db->query($sql2);
                               $count2 = $query2->num_rows();
                              if($count2>0)
                                {
                                   $row2=$query2->row_array();  
                                   $percent="10 %";  
                                   $flat="up to";
                                   $off="off";
                                   $user_id=$row2['pharmacy_branch_user_id'];
                                } 
                                else
                                {
                                    $percent="";
                                }
                         } 
                        
                         $select_order[] = array('id'=> $id,
                                                 'image' => $image,
                                                 'name' => $name,
                                                 'message' => $text,
                                                 'flat' => $flat,
                                                 'percent' => $percent,
                                                 'off' => $off,
                                                 'type'=>$type,
                                                 'flow_type'=>$ftype,
                                                 'info'=>$info,
                                                 'user_id'=>$user_id
                                     ); 
                        
                      }
                       elseif($row['insta_select']=="2")
                      {
                         
                         
                         $exclusives[] = array('id'=> $id,
                                                 'image' => $image,
                                                 'name' => $name,
                                                 'message' => $text,
                                                 'flat' => $flat,
                                                 'percent' => $percent,
                                                 'off' => $off,
                                                 'type'=>$type,
                                                 'info'=>$info,
                                                
                                     ); 
                      }
                    }
                    
          }
          else
          {
             $insta_order=array(); 
             $select_order=array();
             $exclusives=array();
          }
        
        
        
               $resultpost = array(
                                  "Insta_order" => $insta_order,
                                  "Select_order"=>$select_order,
                                  "Our_exclusives"=>$exclusives
                                  );
          return $resultpost;
       
   }  
    
     public function pharmacy_front_v2($user_id, $mlat, $mlng,$re_mb,$page) 
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
            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            return $dist;
        }
        $wh = "";
        $join="";
        if($re_mb=="1")
        {
            $wh .="and ms.recommended ='1'";
        }
        elseif($re_mb=="2")
        {
            $wh .="";
        }

         elseif($re_mb=="3")
        {
            $wh .="and ms.mba ='1'";
        }
        
        elseif($re_mb=="4")
        {
            $join .="INNER JOIN pharmacy_favourite as pf ON ms.user_id = pf.listing_id OR ms.pharmacy_branch_user_id=pf.listing_id";
            $wh .="and pf.user_id ='$user_id'";
        }
         $sql = sprintf("SELECT ms.*, ( 6371 * acos( cos( radians('%s') ) * cos( radians( ms.lat ) ) * cos( radians( ms.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( ms.lat ) ) ) ) AS distance FROM medical_stores  as ms $join WHERE ms.is_approval='1' AND ms.is_active='1' AND ( ms.visible <> '0' AND ms.type <> 'branch' OR ms.visible = '1' AND ms.type = 'branch' ) $wh HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
        
           
        
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count>0){
            foreach ($query->result_array() as $row) {
                 $offer_discount = array();
                 $medical_id = "";
                 $chat_id = "";
                $lat = $row['lat'];
                $lng = $row['lng'];
                $medical_id1 = $row['user_id'];
                $medical_id2 = $row['pharmacy_branch_user_id'];
                if(empty($medical_id2))
                {
                    $medical_id=$medical_id1;
                }
                else
                {
                      $medical_id=$medical_id2;
                }

                $medical_name = $row['medical_name'];
                $store_manager = $row['store_manager'];
                $address1 = $row['address1'];
                $address2 = $row['address2'];
                $pincode = $row['pincode'];
                $city = $row['city'];
                $state = $row['state'];
                $contact_no = $row['contact_no'];
                $whatsapp_no = $row['whatsapp_no'];
                if(empty($whatsapp_no))
                {
                    $whatsapp_no="";
                }
                $email = $row['email'];
                $store_since = $row['store_since'];
                $website = $row['website'];
                 if(empty($website))
                {
                    $website="";
                }
                $discount = $row['discount'];
                $medicalwale_discount = $row['medicalwale_discount'];
                $discount_description = $row['discount_description'];
                //added by zak for all discount 
                $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $surgical_discount = $row['surgical_discount'];
                $perscribed_discount = $row['perscribed_discount'];
                $mba = $row['mba'];
                $certified = $row['certified'];
                $recommended = $row['recommended'];
                //favourite Pharmacy
                
                $fav_pharmacy = $this->db->select('id')->from('pharmacy_favourite')->where('user_id', $user_id)->where('listing_id', $medical_id)->get()->num_rows();
                
                
              //  echo $otc_discount.$ethical_discount.$generic_discount;
                
                 $offer_dis = array(
                    $perscribed_discount,$generic_discount,$surgical_discount,$ethical_discount,$otc_discount
                    );
                    
                    $offer_key = array('Prescribed Medicines Discount','Generic Medicines','Surgical Product Discount','Wellness/Lifestyle','General Product Discount');
                    
                 //   echo 'array ='.$offer_dis;
                  //  echo 'key ='.$offer_key;
                    for($i = 0; count($offer_dis) > $i; $i++)
                    {
                        if($offer_dis[$i] == '' || $offer_dis[$i] == NULL)
                        {
                            $offer_dis[$i] = '0';
                        }
                        else
                        {
                            
                        }
                        
                        $offer_discount[] = array(
                             'discount_key' => $offer_key[$i],
                             'discount_value' => $offer_dis[$i]
                            );
                        
                    }
                    
                //print_r($discount);
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
                $km = $row['distance'];
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
                
                if($user_id == '')
                {
                    $is_follow = 'No';
                }
                else
                {
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $medical_id)->get()->num_rows();
    
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
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
    
                
                if(empty($medical_id2))
                {
                    $chat_id=$medical_id1;
                }
                else
                {
                      $chat_id=$medical_id2;
                }  

                $chat_display = $row['medical_name'];
                $is_chat = 'Yes';
    
                $meter = '0';
                $distance = $km * 1000;
                $store_distance = round($distance, 2);
                if ($distance > 999) {
                    $distance = $distance / 1000;
                    $meter = round($distance, 2) . ' km';
                } else {
                    $meter = round($distance) . ' meters away';
                }
    
                $reach_area = str_replace(" Mtr", "", $reach_area);
                $reach_area = str_replace(" Km", "", $reach_area);
                if ($reach_area > 10) {
                    $ranges = ($reach_area / 1000);
                } else {
                    $ranges = $reach_area;
                }
    
                $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                $distance_root =  round(($distances/1000),1);
            
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
    
           
    $offer_dis_new = array(
                    $generic_discount,$otc_discount,$ethical_discount, $perscribed_discount,$surgical_discount
                    );
       $product_category_list = array();            
    $query_category = $this->db->query("SELECT id,category,image FROM `category` where parent_id=0  order by id asc");
    $i=0;
        foreach ($query_category->result_array() as $row) {
            $product_id = $row['id'];
            $product_category = $row['category'];
            $product_image = $row['image'];
            $product_image = str_replace(" ", "", $product_image);
            $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;

           $sub_category=array();
           $query_category1 = $this->db->query("SELECT id,category,image FROM `category` where parent_id='$product_id' order by id asc");
        foreach ($query_category1->result_array() as $row1) {
            $product_id1 = $row1['id'];
            $product_category1 = $row1['category'];
            $product_image1 = $row1['image'];
            $product_image1 = str_replace(" ", "", $product_image1);
            $product_image1 = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image1;
            
           $sub_category[] = array(
                "id" => $product_id1,
                "category" => $product_category1,
                "image" => $product_image1
               
            ); 
        }


            $product_category_list[] = array(
                "id" => $product_id,
                "category" => $product_category,
                "image" => $product_image,
                "discount"=>$offer_dis_new[$i],
                "sub_category"=>$sub_category
            );
            $i++;
        }
    
    
    
    
    
    
    
    
    
    
                  $FINAL_RESULT = array();
                    
                $resultpost[] = array(
                    'id' => $medical_id,
                    'medical_name' => $medical_name,
                    'listing_id' => $chat_id,
                    'listing_type' => '13',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'store_manager' => $store_manager,
                    'address1' => $address1,
                    'address2' => "",
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact_no' => $contact_no,
                    'whatsapp_no' => $whatsapp_no,
                    "exotel_no" => '02233721563',
                    'email' => $email,
                    'store_since' => $store_since,
                    'website' => $website,
                    'reach_area' => $ranges,
                    'store_distance' => $store_distance,
                    'Description' => $discount_description,
                    'distance' => $distance_root,
                    'distance_root'=>$distance_root,
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
                    'discount' => $discount,
                    'medicalwale_discount' => $medicalwale_discount,
                    'category_list' => $product_category_list,
                    'offer_discount' => $offer_discount,
                    'favourite' => $fav_pharmacy,
                    'mba' => $mba,
                    'certified' => $certified,
                    'recommended' => $recommended
                );
            }
           
            
            
         
          
            //added by zak for distance sorting
                usort($resultpost, function($a, $b) {
                                $a = $a['distance_root'];
                                $b = $b['distance_root'];
                    if ($a == $b) { return 0; }
                        return ($a < $b) ? -1 : 1;
                    });
                    
                    //$resultpost = array_reverse($resultpost);
                    
      
        $resultpost  =  $resultpost;  
      
               
            return $resultpost;
        }
        else{
          return array();
            
        }
       
   }  
    
    public function pharmacy_details($user_id, $listing_id) {
        $query = $this->db->query("SELECT `id`, `user_id`,`discount`,`medicalwale_discount`, `email`, `otc_discount`, `ethical_discount`, `generic_discount`,  `surgical_discount`,`perscribed_discount`,`medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,IFNULL(online_offline,'') AS online_offline FROM medical_stores  WHERE  user_id='$listing_id'");
        $count = $query->num_rows();
        
        //  $query_branch = $this->db->query("SELECT `id`, `pharmacy_branch_user_id` as user_id, `email`, `branch_name` as medical_name, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `branch_contact_no` as contact_no, IFNULL(branch_whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,IFNULL(online_offline,'') AS online_offline FROM pharmacy_branch  WHERE  pharmacy_branch_user_id='$listing_id'");
       $query_branch = $this->db->query("SELECT `id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`,`discount`,`medicalwale_discount` ,`otc_discount`, `ethical_discount`, `generic_discount`,  `surgical_discount`,`perscribed_discount`,`days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,IFNULL(online_offline,'') AS online_offline FROM medical_stores  WHERE  pharmacy_branch_user_id='$listing_id'");

        $count_branch = $query_branch->num_rows();
        if ($count > 0) {
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
                $website = $row['website'];
                $reach_area = $row['reach_area'];
                $is_24hrs_available = $row['is_24hrs_available'];
                $discount = $row['discount'];
                $medicalwale_discount = $row['medicalwale_discount'];
                  //added by zak for all discount 
                $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $surgical_discount = $row['surgical_discount'];
                $perscribed_discount = $row['perscribed_discount'];
                
              //  echo $otc_discount.$ethical_discount.$generic_discount;
                
                 $offer_dis = array(
                    $perscribed_discount,$generic_discount,$surgical_discount,$ethical_discount,$otc_discount
                    );
                    
                   
                    
                    $offer_key = array('Prescribed Medicines Discount','Generic Medicines','Surgical Product Discount','Wellness/Lifestyle','General Product Discount');
                    
                 //   echo 'array ='.$offer_dis;
                  //  echo 'key ='.$offer_key;
                    for($i = 0; count($offer_dis) > $i; $i++)
                    {
                        if($offer_dis[$i] == '' || $offer_dis[$i] == NULL)
                        {
                            $offer_dis[$i] = '0';
                        }
                        else
                        {
                            
                        }
                        
                        $offer_discount[] = array(
                             'discount_key' => $offer_key[$i],
                             'discount_value' => $offer_dis[$i]
                            );
                        
                    }
                    
                    
                
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
                
                if($user_id == '')
                {
                  $is_follow = 'No';  
                }
                else
                {
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $medical_id)->get()->num_rows();
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
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
                /* $product_category_list = array();
                $query_category = $this->db->query("SELECT id,category,image FROM `category` WHERE id<>'3' order by id asc");
                foreach ($query_category->result_array() as $row) {
                    $product_id = $row['id'];
                    $product_category = $row['category'];
                    $product_image = $row['image'];
                    $product_image = str_replace(" ", "", $product_image);
                    $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;

                    $product_category_list[] = array(
                        "id" => $product_id,
                        "category" => $product_category,
                        'image' => $product_image,
                        'discount'=> '0',
                        'sub_category'=> array(),
                        );
                }*/
                
                
                // new
                
                $offer_dis_new = array(
                    $generic_discount,$otc_discount,$ethical_discount, $perscribed_discount,$surgical_discount
                    );
                   $product_category_list = array();            
                $query_category = $this->db->query("SELECT id,category,image FROM `category` where parent_id=0  order by id asc");
                $i=0;
                
            //   $i=0; 
                
                 foreach ($query_category->result_array() as $row) {
                            $product_id = $row['id'];
                            $product_category = $row['category'];
                            $product_image = $row['image'];
                            $product_image = str_replace(" ", "", $product_image);
                            $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;
                
                           $sub_category=array();
                           $query_category1 = $this->db->query("SELECT id,category,image FROM `category` where parent_id='$product_id' order by id asc");
                                foreach ($query_category1->result_array() as $row1) {
                                    $product_id1 = $row1['id'];
                                    $product_category1 = $row1['category'];
                                    $product_image1 = $row1['image'];
                                    $product_image1 = str_replace(" ", "", $product_image1);
                                    $product_image1 = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image1;
                                    
                                   $sub_category[] = array(
                                        "id" => $product_id1,
                                        "category" => $product_category1,
                                        "image" => $product_image1
                                       
                                    ); 
                                }
                
                
                            $product_category_list[] = array(
                                "id" => $product_id,
                                "category" => $product_category,
                                "image" => $product_image,
                                "discount"=>$offer_dis_new[$i],
                                "sub_category"=>$sub_category
                            );
                            $i++;
                        }
                
                
                $resultpost[] = array(
                    'id' => $medical_id,
                    'medical_name' => $medical_name,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'store_manager' => $store_manager,
                    'address1' => $address1,
                    'address2' => "",
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
                    'medicalwale_discount' => $medicalwale_discount,
                    'discount' => $discount,
                    'category_list' => $product_category_list,
                    'offer_discount' => $offer_discount,
                    'exotel_no' => '02233721563'
                );
            }
        }
        else  if ($count_branch > 0) {
            foreach ($query_branch->result_array() as $row) {
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
                $website = $row['website'];
                $reach_area = $row['reach_area'];
                $discount = $row['discount'];
                $medicalwale_discount = $row['medicalwale_discount'];
                if(empty($medicalwale_discount))
                {
                    $medicalwale_discount="0";
                }
                else
                {
                    $medicalwale_discount;
                }
                $is_24hrs_available = $row['is_24hrs_available'];
                $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $surgical_discount = $row['surgical_discount'];
                $perscribed_discount = $row['perscribed_discount'];
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
                
                if($user_id == '')
                {
                  $is_follow = 'No';  
                }
                else
                {
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $medical_id)->get()->num_rows();
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
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
                
/*                $product_category_list = array();
                $query_category = $this->db->query("SELECT id,category,image FROM `category` WHERE id<>'3' order by id asc");
                foreach ($query_category->result_array() as $row) {
                    $product_id = $row['id'];
                    $product_category = $row['category'];
                    $product_image = $row['image'];
                    $product_image = str_replace(" ", "", $product_image);
                    $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;

                    $product_category_list[] = array(
                        "id" => $product_id,
                        "category" => $product_category,
                        'image' => $product_image);
                }*/
                
                 $offer_dis_new = array(
                    $generic_discount,$otc_discount,$ethical_discount, $perscribed_discount,$surgical_discount
                    );
       $product_category_list = array();            
    $query_category = $this->db->query("SELECT id,category,image FROM `category` where parent_id=0 order by id asc");
    $i=0;
        foreach ($query_category->result_array() as $row) {
            $product_id = $row['id'];
            $product_category = $row['category'];
            $product_image = $row['image'];
            $product_image = str_replace(" ", "", $product_image);
            $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;

           $sub_category=array();
           $query_category1 = $this->db->query("SELECT id,category,image FROM `category` where parent_id='$product_id' order by id asc");
        foreach ($query_category1->result_array() as $row1) {
            $product_id1 = $row1['id'];
            $product_category1 = $row1['category'];
            $product_image1 = $row1['image'];
            $product_image1 = str_replace(" ", "", $product_image1);
            $product_image1 = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image1;
            
           $sub_category[] = array(
                "id" => $product_id1,
                "category" => $product_category1,
                "image" => $product_image1
               
            ); 
        }


            $product_category_list[] = array(
                "id" => $product_id,
                "category" => $product_category,
                "image" => $product_image,
                "discount"=>$offer_dis_new[$i],
                "sub_category"=>$sub_category
            );
            $i++;
        }
                
                $resultpost[] = array(
                    'id' => $medical_id,
                    'medical_name' => $medical_name,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'store_manager' => $store_manager,
                    'address1' => $address1,
                    'address2' => "",
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
                     'medicalwale_discount' => $medicalwale_discount,
                    'discount' => $discount,
                    'category_list' => $product_category_list,
                    'offer_discount' => $offer_discount,
                    'exotel_no' => '02233721563'
                );
            }
        }
        else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function category_list() {
        $query = $this->db->query("SELECT id,category,image FROM `category` WHERE id<>'3' order by id asc");
        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $category = $row['category'];
            $image = $row['image'];
            $image = str_replace(" ", "", $image);
            $image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $image;

            $resultpost[] = array(
                "id" => $id,
                "category" => $category,
                'image' => $image
            );
        }
        return $resultpost;
    }

    public function category_list_v2() {
        
        
      $query_branch = $this->db->query("SELECT MAX(otc_discount) as otc,MAX(ethical_discount) as ethical,MAX(generic_discount) as generic,MAX(surgical_discount) as surgical,MAX(perscribed_discount) as perscribed  FROM medical_stores  WHERE is_active= '1'");

        $count_branch = $query_branch->num_rows();
        $row=$query_branch->row_array();
              
        
                $otc_discount = $row['otc'];
                $ethical_discount = $row['ethical'];
                $generic_discount = $row['generic'];
                $surgical_discount = $row['surgical'];
                $perscribed_discount = $row['perscribed'];
         $offer_dis_new = array(
                    $generic_discount,$otc_discount,$ethical_discount, $perscribed_discount,$surgical_discount
                    );
        $i=0;
        $query = $this->db->query("SELECT id,category,image FROM `category` where parent_id=0 and id <> 16 order by id asc");
        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $category = $row['category'];
            $image = $row['image'];
            $image = str_replace(" ", "", $image);
            $image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $image;

           $sub_category=array();
           $query1 = $this->db->query("SELECT id,category,image FROM `category` where parent_id='$id' order by id asc");
        foreach ($query1->result_array() as $row1) {
            $id1 = $row1['id'];
            $category1 = $row1['category'];
            $image1 = $row1['image'];
            $image1 = str_replace(" ", "", $image1);
            $image1 = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $image1;
            
           $sub_category[] = array(
                "id" => $id1,
                "category" => $category1,
                "image" => $image1
               
            ); 
        }


            $resultpost[] = array(
                "id" => $id,
                "category" => $category,
                "image" => $image,
                "discount"=>$offer_dis_new[$i],
                "sub_category"=>$sub_category
                
            );
            $i++;
        }
        return $resultpost;
    }
    
    public function sub_category($category_id) {
        $query = $this->db->query("SELECT id,category,sub_category FROM `product_sub_category` WHERE category='$category_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $sub_category = $row['sub_category'];
                $resultpost[] = array(
                    "id" => $id,
                    "sub_category" => $sub_category
                );
            }
        } else {
            $resultpost = '';
        }
        return $resultpost;
    }

    public function product_list($sub_category_id, $page) {
        
        $WHER ="";
        if($page != ""){
            $limit = 10;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            
            $WHER = 'limit '.$start.','.$limit;
        }
        $query = $this->db->query("SELECT id as product_id,product_name,is_prescription_needed,product_price,pack,image FROM `product` WHERE sub_category='$sub_category_id' $WHER");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $product_id = $row['product_id'];
                $product_name = $row['product_name'];
                $product_price = $row['product_price'];
                $image = $row['image'];
                $str = $row['pack'];

                if ($str == '') {
                    $pack = '1Others';
                } else {
                    if (preg_match('#[0-9]#', $str)) {

                        $pack = $str;
                    } else {
                        $pack = '1strip';
                    }
                }
                $product_name = $row['product_name'];
                $is_prescription_needed = $row['is_prescription_needed'];
                $product_image = $product_name . '.jpg';
                $product_image = str_replace(' ', '%20', $product_image);
                $product_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/' . $image;
                $resultpost[] = array(
                    "sub_category_id" => $sub_category_id,
                    "product_id" => $product_id,
                    "product_name" => $product_name,
                    "is_prescription_needed" => $is_prescription_needed,
                    "product_price" => $product_price,
                    'product_weight' => $pack,
                    'product_image' => $product_image
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function product_search($keyword) {
        $query = $this->db->query("SELECT id,sub_category,product_name,is_prescription_needed,pack,product_price FROM product WHERE product_name LIKE '%$keyword%' limit 15");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $product_id = $row['id'];
                $sub_category = $row['sub_category'];
                $product_name = $row['product_name'];
                $is_prescription_needed = $row['is_prescription_needed'];
                $product_price = $row['product_price'];
                $str = $row['pack'];

                if ($str == '') {
                    $pack = '1Others';
                } else {
                    if (preg_match('#[0-9]#', $str)) {

                        $pack = $str;
                    } else {
                        $pack = '1strip';
                    }
                }

                $product_image = $product_name . '.jpg';
                $product_image = str_replace(' ', '%20', $product_image);
                $product_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/' . $product_image;
                $resultpost[] = array(
                    "sub_category_id" => $sub_category,
                    "product_id" => $product_id,
                    "product_name" => $product_name,
                    "is_prescription_needed" => $is_prescription_needed,
                    "product_price" => $product_price,
                    'product_weight' => $pack,
                    'product_image' => $product_image
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function cart_order($user_id, $address_id, $medical_id, $payType, $product_id, $product_quantity, $product_price) {
        $status = "Pending";
        $product_status = 'Pending';
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $uni_id = date('YmdHis');
        $discount = '0';
        $grand_total = '0';
        $final_total = '0';
        $discount_rate = '0';

        $product_id = explode(",", $product_id);
        $product_quantity = explode(",", $product_quantity);
        $product_price = explode(",", $product_price);
        $cnt = count($product_id);
        for ($i = 0; $i < $cnt; $i++) {
            $final_total = $final_total + ($product_price[$i] * $product_quantity[$i]);
        }
        $discount_query = $this->db->query("SELECT discount FROM `discount` WHERE medical_id='$medical_id'");
        $discount_list = $discount_query->row();
        if ($discount_list) {
            $discount = $discount_list->discount;
            $discount_rate = ($final_total * $discount) / 100;
            $grand_total = $final_total - $discount_rate;
        } else {
            $grand_total = $final_total;
        }
        $cart_order_data = array(
            'medical_id' => $medical_id,
            'user_id' => $user_id,
            'address_id' => $address_id,
            'uni_id' => $uni_id,
            'date' => $date,
            'status' => $status,
            'store_status' => '0',
            'customer_status' => '0',
            'total' => $grand_total,
            'discount' => $discount,
            'payType' => $payType
        );
        $insert1 = $this->db->insert('cart_order', $cart_order_data);
        $order_id = $this->db->insert_id();
        $cnt = count($product_id);
        for ($i = 0; $i < $cnt; $i++) {
            $sub_total = $product_price[$i] * $product_quantity[$i];

            $cart_order_products_data = array(
                'order_id' => $order_id,
                'medical_id' => $medical_id,
                'product_id' => $product_id[$i],
                'product_quantity' => $product_quantity[$i],
                'product_price' => $product_price[$i],
                'sub_total' => $sub_total,
                'product_status' => 'pending',
                'product_status_type' => '',
                'product_status_value' => '',
                'uni_id' => $uni_id
            );
            $insert2 = $this->db->insert('cart_order_products', $cart_order_products_data);
        }
        if ($insert1 & $insert2) {
            $order_type = 'order';
            $noti_message = 'Thanks for placing your order with Medicalwale';
            $noti_message2 = 'New Order Has Been Placed To Your Store';
            $date_time = date('Y-m-d H:i:s');

            $insert_notification = array(
                'user_id' => $user_id,
                'order_id' => $order_id,
                'order_type' => $order_type,
                'message' => $noti_message,
                'status' => '0',
                'date' => $date_time
            );
            $this->db->insert('notification', $insert_notification);

            $store_notification = array(
                'medical_id' => $medical_id,
                'order_id' => $order_id,
                'order_type' => $order_type,
                'message' => $noti_message2,
                'status' => '0',
                'date' => $date_time
            );
            $this->db->insert('store_notification', $store_notification);
        }
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function cart_order_list($user_id) {
        $query = $this->db->query("SELECT cart_order.id AS order_id,cart_order.uni_id,cart_order.date,cart_order.status,medical_stores.medical_name  FROM `cart_order`
        INNER JOIN `cart_order_products`
        ON cart_order.id=cart_order_products.order_id
        INNER JOIN `medical_stores`
        ON medical_stores.user_id=cart_order_products.medical_id
        WHERE cart_order.user_id='$user_id' GROUP BY cart_order.uni_id");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $order_id = $row['order_id'];
                $order_no = $row['uni_id'];
                $medical_name = $row['medical_name'];
                $order_status = $row['status'];
                $order_date = $row['date'];

                $resultpost[] = array(
                    "order_id" => $order_id,
                    "order_no" => $order_no,
                    "medical_name" => $medical_name,
                    'order_status' => $order_status,
                    'order_date' => $order_date
                );
            }
        } else {
            $resultpost = '';
        }
        return $resultpost;
    }

    public function cart_order_details($user_id, $order_id) {
        $query = $this->db->query("SELECT cart_order.uni_id,cart_order.date,cart_order.status,medical_stores.medical_name,GROUP_CONCAT(product.product_name) AS product_name,GROUP_CONCAT(product.product_price) AS product_price,GROUP_CONCAT(cart_order_products.product_quantity) AS product_quantity,product.is_active,IFNULL(oc_address.address_id,'') AS address_id,IFNULL(oc_address.customer_id,'') AS customer_id,IFNULL(oc_address.firstname,'') AS firstname,IFNULL(oc_address.lastname,'') AS lastname,IFNULL(oc_address.email,'') AS email,IFNULL(oc_address.telephone,'') AS telephone,IFNULL(oc_address.lastname,'') AS lastname,IFNULL(oc_address.address_1,'') AS address_1,IFNULL(oc_address.address_2,'') AS address_2
            FROM `cart_order`
            INNER JOIN `cart_order_products`
            ON cart_order.id=cart_order_products.order_id
            INNER JOIN product
            ON product.id=cart_order_products.product_id
            INNER JOIN medical_stores
            ON medical_stores.user_id=cart_order_products.medical_id
            LEFT JOIN `oc_address`
            ON oc_address.address_id=cart_order.address_id
            WHERE cart_order.user_id='$user_id' AND cart_order.id='$order_id'");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $order_no = $row['uni_id'];
                $order_date = $row['date'];
                $order_status = $row['status'];
                $medical_name = $row['medical_name'];
                $product_name = $row['product_name'];
                $product_price = $row['product_price'];
                $product_quantity = $row['product_quantity'];
                $firstname = $row['firstname'];
                $lastname = $row['lastname'];
                $addr_patient_name = $firstname . ' ' . $lastname;
                $addr_address1 = $row['address_1'];
                $addr_address2 = $row['address_2'];
                $addr_landmark = $row['landmark'];
                $addr_mobile = $row['telephone'];
                $is_active = $row['is_active'];

                if ($is_active == 1) {
                    $product_availability = 'Available';
                } else {
                    $product_availability = 'Not Available';
                }
                $resultpost[] = array(
                    "order_no" => $order_no,
                    "order_date" => $order_date,
                    "order_status" => $order_status,
                    "medical_name" => $medical_name,
                    "product_name" => $product_name,
                    "product_price" => $product_price,
                    "product_quantity" => $product_quantity,
                    "product_availability" => $product_availability,
                    "addr_patient_name" => $addr_patient_name,
                    "addr_address1" => $addr_address1,
                    "addr_address2" => $addr_address2,
                    "addr_landmark" => $addr_landmark,
                    "addr_mobile" => $addr_mobile
                );
            }
        } else {
            $resultpost = '';
        }
        return $resultpost;
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
        $review_count = $this->db->select('id')->from('medical_stores_review')->where('medical_stores_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT medical_stores_review.id,medical_stores_review.user_id,medical_stores_review.medical_stores_id,medical_stores_review.rating,medical_stores_review.review, medical_stores_review.service,medical_stores_review.date as review_date,users.id as user_id,users.name as firstname FROM `medical_stores_review` INNER JOIN `users` ON medical_stores_review.user_id=users.id WHERE medical_stores_review.medical_stores_id='$listing_id' order by medical_stores_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $user_id = $row['user_id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
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

                $like_count = $this->db->select('id')->from('medical_stores_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('medical_stores_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('medical_stores_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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
    
    public function review_with_comment($user_id, $listing_id) {

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
        $review_count = $this->db->select('id')->from('medical_stores_review')->where('medical_stores_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT medical_stores_review.id,medical_stores_review.user_id,medical_stores_review.medical_stores_id,medical_stores_review.rating,medical_stores_review.review, medical_stores_review.service,medical_stores_review.date as review_date,users.id as user_id,users.name as firstname FROM `medical_stores_review` INNER JOIN `users` ON medical_stores_review.user_id=users.id WHERE medical_stores_review.medical_stores_id='$listing_id' order by medical_stores_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $user_id = $row['user_id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '18') {
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

                $like_count = $this->db->select('id')->from('medical_stores_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('medical_stores_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('medical_stores_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

 $review_list_count = $this->db->select('id')->from('medical_stores_review_comment')->where('post_id', $id)->get()->num_rows();
        if ($review_list_count) {
            $resultcomment=array();
            $querycomment = $this->db->query("SELECT medical_stores_review_comment.id,medical_stores_review_comment.post_id,medical_stores_review_comment.comment as comment,medical_stores_review_comment.date,users.name,medical_stores_review_comment.user_id as post_user_id FROM medical_stores_review_comment INNER JOIN users on users.id=medical_stores_review_comment.user_id WHERE medical_stores_review_comment.post_id='$id' order by medical_stores_review_comment.id asc");

            foreach ($querycomment->result_array() as $rowc) {
                $comment_id = $rowc['id'];
                $post_id = $rowc['post_id'];
                $comment = $rowc['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                
                $usernamec = $rowc['name'];
                $date = $rowc['date'];
                $post_user_id = $rowc['post_user_id'];
                $like_countc = $this->db->select('id')->from('medical_stores_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $like_yes_noc = $this->db->select('id')->from('medical_stores_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimagec = 'https://medicalwale.com/healthwall_avatar/' . $img_file;
                } else {
                    $userimagec = 'https://medicalwale.com/img/default_user.jpg';
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
        $count_query = $this->db->query("SELECT id from medical_stores_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `medical_stores_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from medical_stores_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $medical_stores_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('medical_stores_review_likes', $medical_stores_review_likes);
            $like_query = $this->db->query("SELECT id FROM medical_stores_review_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function add_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'medical_stores_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('medical_stores_review', $review_array);
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
           $this->db->update('medical_stores_review',$review_array);
        //$this->db->insert('fitness_center_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $medical_stores_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('medical_stores_review_comment', $medical_stores_review_comment);
        $medical_stores_review_comment_query = $this->db->query("SELECT id FROM medical_stores_review_comment WHERE post_id='$post_id'");
        $total_comment = $medical_stores_review_comment_query->num_rows();
        return array(
            'status' => 201,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    public function review_comment_like($user_id, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM medical_stores_review_comment_like WHERE comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `medical_stores_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id FROM medical_stores_review_comment_like WHERE comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $medical_stores_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('medical_stores_review_comment_like', $medical_stores_review_comment_like);
            $comment_query = $this->db->query("SELECT id FROM medical_stores_review_comment_like WHERE comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 201,
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

        $review_list_count = $this->db->select('id')->from('medical_stores_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT medical_stores_review_comment.id,medical_stores_review_comment.post_id,medical_stores_review_comment.comment as comment,medical_stores_review_comment.date,users.name,medical_stores_review_comment.user_id as post_user_id FROM medical_stores_review_comment INNER JOIN users on users.id=medical_stores_review_comment.user_id WHERE medical_stores_review_comment.post_id='$post_id' order by medical_stores_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '9') {
                    $decrypt = $this->decrypt($comment);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $comment) {
                        $comment = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($comment)) === $comment) {
                        $comment = base64_decode($comment);
                    }
                }
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];
                $like_count = $this->db->select('id')->from('medical_stores_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('medical_stores_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://medicalwale.com/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://medicalwale.com/img/default_user.jpg';
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

    public function pharmacy_view($listing_id, $user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $pharmacy_view_array = array(
            'listing_id' => $listing_id,
            'user_id' => $user_id
        );
        $this->db->insert('pharmacy_view', $pharmacy_view_array);

        $pharmacy_view = $this->db->select('id')->from('pharmacy_view')->where('listing_id', $listing_id)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'pharmacy_view' => $pharmacy_view
        );
    }
    
    public function update_pharmacy_list_medlife($order_id, $rxid, $imageindex, $imageId){
        
        $data = array(
            'rxId'=>$rxid,
            'imageIndex'=>$imageindex,
            'imageId'=>$imageId,
            'order_status'=>'Awaiting Confirmation'
            );
        
        $this->db->where('order_id', $order_id);
        $this->db->update('user_order', $data);    
        if($this->db->affected_rows() > 0){
            
        define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent) {
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag,"notification_image" => $img_url,"notification_date" => $order_date, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            if ($key_count == '1') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
                );
            }
            if ($key_count == '2') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
           // print_r($result);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
        }
            
             $query_pharmacy = $this->db->query("SELECT user_id,order_date,invoice_no FROM user_order WHERE order_id='$order_id'");
                $row_pharma = $query_pharmacy->row_array();
                
               $user_id = $row_pharma['user_id'];
               $order_date=$row_pharma['order_date'];
               $invoice_no=$row_pharma['invoice_no'];
               $order_status='Awaiting Confirmation';
               
             $order_date = date('j M Y h:i A', strtotime($order_date));
            $order_info = $this->db->select('name,token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $order_info->token_status;
            if ($token_status > 0) {
                $reg_id = $order_info->token;
                $agent = $order_info->agent;
                $name= $order_info->name;
              //  $msg = 'Thanks uploading your prescription with ' . $listing_name;
               $msg = 'Thanks for placing your order with Medlife';
                $img_url = 'https://medicalwale.com/img/noti_pharmacy.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Order Placed';
                
                
        //          //added by zak for all notification therough services
                 $notification_array = array(
                       'title' => $title,
                       'msg'  => $msg,
                       'img_url' => $img_url,
                       'tag' => $tag,
                       'order_status' => 'Awaiting Confirmation',
                       'order_date' => $order_date,
                       'order_id'   => $order_id,
                       'post_id'  => "",
                       'listing_id'  => "",
                       'booking_id'  => "",
                       'invoice_no' => $invoice_no,
                       'user_id'  => $user_id,
                       'notification_type'  => 'prescription',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
             );
          $this->db->insert('All_notification_Mobile', $notification_array);
        // //end 
                $listing_name='Medlife';
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
            }
            
            return array(
            'status' => 200,
            'message' => 'success'
            );
        }else{
            return array(
            'status' => 201,
            'message' => 'error'
            );
        }
        
    }
    
    
    
    
    public function add_favourite_pharmacy($user_id, $listing_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $pharmacy_view = $this->db->select('id')->from('pharmacy_favourite')->where('user_id', $user_id)->where('listing_id', $listing_id)->get()->num_rows();
        if($pharmacy_view > 0)
        {
            $this->db->where('user_id', $user_id);
            $this->db->where('listing_id', $listing_id);
            $this->db->delete('pharmacy_favourite');    
            
            return array(
            'status' => 200,
            'favourite' => '0'
            );
            
        }
        else
        {
            $data = array(
            'user_id'=>$user_id,
            'listing_id'=>$listing_id,
            'datetime'=>$date
            );
            $this->db->insert('pharmacy_favourite', $data);  
            $ids = $this->db->insert_id();
            if(!empty($ids))
            {
                return array(
                'status' => 200,
                'favourite' => '1'
                );
            }
            else
            {
                return array(
                'status' => 200,
                'favourite' => '0'
                );
            }
        }
    }
    
    public function favourite_pharmacy_list($user_id, $mlat, $mlng, $page) {
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
             $val=$response_a['rows'][0]['elements'][0]['status'];
           if($val!="ZERO_RESULTS")
           {
            $dist       = $response_a['rows'][0]['elements'][0]['distance']['text'];
           }
           else
           {
            $dist       ="";   
           }
        }

         $sql = sprintf("SELECT mba,recommended,certified,discount_description,medicalwale_discount,otc_discount,ethical_discount,surgical_discount,perscribed_discount,generic_discount,`id`, `user_id`, `pharmacy_branch_user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' AND visible <> '0' AND type <> 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
     
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count>0){
            foreach ($query->result_array() as $row) {
                 $offer_discount = array();
                $lat = $row['lat'];
                $lng = $row['lng'];
                $medical_id = $row['user_id'];
                $pharmacy_branch_user_id = $row['pharmacy_branch_user_id'];
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
                $discount = $row['discount'];
                $medicalwale_discount = $row['medicalwale_discount'];
                $discount_description = $row['discount_description'];
                //added by zak for all discount 
                 $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                $surgical_discount = $row['surgical_discount'];
                $perscribed_discount = $row['perscribed_discount'];
                $mba = $row['mba'];
                $certified = $row['certified'];
                $recommended = $row['recommended'];
                
                //favourite Pharmacy
               // $where_con = " (listing_id = '$medical_id' OR listing_id ='$pharmacy_branch_user_id') ";
                $fav_pharmacy = $this->db->select('id')->from('pharmacy_favourite')->where('user_id', $user_id)->where('listing_id', $medical_id)->get()->num_rows();
                
               // echo $this->db->last_query(); 
              //  echo $otc_discount.$ethical_discount.$generic_discount;
                
                 $offer_dis = array(
                    $perscribed_discount,$generic_discount,$surgical_discount,$ethical_discount,$otc_discount
                    );
                    
                    $offer_key = array('Prescribed Medicines Discount','Generic Medicines','Surgical Product Discount','Wellness/Lifestyle','General Product Discount');
                    
                 //   echo 'array ='.$offer_dis;
                  //  echo 'key ='.$offer_key;
                    for($i = 0; count($offer_dis) > $i; $i++)
                    {
                        if($offer_dis[$i] == '' || $offer_dis[$i] == NULL)
                        {
                            $offer_dis[$i] = '0';
                        }
                        else
                        {
                            
                        }
                        
                        $offer_discount[] = array(
                             'discount_key' => $offer_key[$i],
                             'discount_value' => $offer_dis[$i]
                            );
                        
                    }
                    
                    
                //print_r($discount);
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
                $km = $row['distance'];
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
                
                if($user_id == '')
                {
                    $is_follow = 'No';
                }
                else
                {
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $medical_id)->get()->num_rows();
    
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
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
    
                $meter = '0';
                $distance = $km * 1000;
                $store_distance = round($distance, 2);
                if ($distance > 999) {
                    $distance = $distance / 1000;
                    $meter = round($distance, 2) . ' km';
                } else {
                    $meter = round($distance) . ' meters away';
                }
    
                $reach_area = str_replace(" Mtr", "", $reach_area);
                $reach_area = str_replace(" Km", "", $reach_area);
                if ($reach_area > 10) {
                    $ranges = ($reach_area / 1000);
                } else {
                    $ranges = $reach_area;
                }
    
                 $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                 //$distance_root =  round(($distances/1000),1);
                 //$distance_root =  round($distances);
                 $distance_root =  round(($distances/1000),1);
                
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
    
                $offer_dis_new = array(
                    $generic_discount,$otc_discount,$ethical_discount, $perscribed_discount,$surgical_discount
                    );
       $product_category_list = array();            
    $query_category = $this->db->query("SELECT id,category,image FROM `category` where parent_id=0  order by id asc");
    $i=0;
        foreach ($query_category->result_array() as $row) {
            $product_id = $row['id'];
            $product_category = $row['category'];
            $product_image = $row['image'];
            $product_image = str_replace(" ", "", $product_image);
            $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;

           $sub_category=array();
           $query_category1 = $this->db->query("SELECT id,category,image FROM `category` where parent_id='$product_id' order by id asc");
        foreach ($query_category1->result_array() as $row1) {
            $product_id1 = $row1['id'];
            $product_category1 = $row1['category'];
            $product_image1 = $row1['image'];
            $product_image1 = str_replace(" ", "", $product_image1);
            $product_image1 = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image1;
            
           $sub_category[] = array(
                "id" => $product_id1,
                "category" => $product_category1,
                "image" => $product_image1
               
            ); 
        }


            $product_category_list[] = array(
                "id" => $product_id,
                "category" => $product_category,
                "image" => $product_image,
                "discount"=>$offer_dis_new[$i],
                "sub_category"=>$sub_category
            );
            $i++;
        }
    
    
                  $FINAL_RESULT = array();
                    
                $resultpost[] = array(
                    'id' => $medical_id,
                    'medical_name' => $medical_name,
                    'listing_id' => $chat_id,
                    'listing_type' => '13',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'store_manager' => $store_manager,
                    'address1' => $address1,
                    'address2' => "",
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact_no' => $contact_no,
                    'whatsapp_no' => $whatsapp_no,
                    "exotel_no" => '02233721563',
                    'email' => $email,
                    'store_since' => $store_since,
                    'website' => $website,
                    'reach_area' => $ranges,
                    'store_distance' => $store_distance,
                    'Description' => $discount_description,
                    'distance' => $distance_root,
                    'distance_root'=>$distance_root,
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
                    'discount' => $discount,
                    'medicalwale_discount' => $medicalwale_discount,
                    'category_list' => $product_category_list,
                    'offer_discount' => $offer_discount,
                    'favourite' => $fav_pharmacy,
                    'mba' => $mba,
                    'certified' => $certified,
                    'recommended' => $recommended
                );
            }
           
            //added for generico pharmacy branch 
            
            $radius_branch = '4';
            
           // $sql_branch = sprintf("SELECT medicalwale_discount,`id`, `pharmacy_branch_user_id` as user_id, `email`,`branch_name` as medical_name, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`,`branch_contact_no` as contact_no, IFNULL(branch_whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM pharmacy_branch  WHERE is_active='1' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius_branch));
  //  echo 'sql'.sprintf("SELECT discount_description,medicalwale_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_active= '1' AND visible = '1' AND user_type = 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius_branch));
     $sql_branch = sprintf("SELECT user_id,discount_description,medicalwale_discount,`id`, `pharmacy_branch_user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance,otc_discount,ethical_discount,generic_discount  FROM medical_stores  WHERE is_active= '1' AND visible = '1' AND type = 'branch' HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius_branch));
       
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
                $store_since = $row['store_since'];
                $website = $row['website'];
                $discount = $row['discount'];
                $medicalwale_discount = $row['medicalwale_discount'];
                //print_r($discount);
                
                  //added by zak for all discount 
                $otc_discount = $row['otc_discount'];
                $ethical_discount = $row['ethical_discount'];
                $generic_discount = $row['generic_discount'];
                
                 //favourite branch Pharmcy
                // $where_con = " (listing_id = '$medical_id' OR listing_id ='$pharmacy_branch_user_id') ";
                $fav_branch_pharmacy = $this->db->select('id')->from('pharmacy_favourite')->where('user_id', $user_id)->where('listing_id', $medical_id)->get()->num_rows();
                            
              //  echo $otc_discount.$ethical_discount.$generic_discount;
                
                $offer_dis = array(
                    $otc_discount,$ethical_discount,$generic_discount
                    );
                    
                    $offer_key = array('otc Discount','Ethical Discount','Generic Discount');
                    
                 //   echo 'array ='.$offer_dis;
                  //  echo 'key ='.$offer_key;
                    for($i = 0; count($offer_dis) > $i; $i++)
                    {
                        if($offer_dis[$i] == '' || $offer_dis[$i] == NULL)
                        {
                            $offer_dis[$i] = '0';
                        }
                        else
                        {
                            
                        }
                        
                        $offer_discount[] = array(
                             'discount_key' => $offer_key[$i],
                             'discount_value' => $offer_dis[$i]
                            );
                        
                    }
                    
                
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
                $km = $row['distance'];
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
    
                $chat_id = $row['pharmacy_branch_user_id'];
                $chat_display = $row['medical_name'];
                $is_chat = 'Yes';
    
                $meter = '0';
                $distance = $km * 1000;
                $store_distance = round($distance, 2);
                if ($distance > 999) {
                    $distance = $distance / 1000;
                    $meter = round($distance, 2) . ' km';
                } else {
                    $meter = round($distance) . ' meters away';
                }
    
                $reach_area = str_replace(" Mtr", "", $reach_area);
                $reach_area = str_replace(" Km", "", $reach_area);
                if ($reach_area > 10) {
                    $ranges = ($reach_area / 1000);
                } else {
                    $ranges = $reach_area;
                }
    
                $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
    
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
                    $product_image = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/product_category/' . $product_image;
    
                    $product_category_list[] = array(
                        "id" => $product_id,
                        "category" => $product_category,
                        'image' => $product_image);
                }
    
    
    
                $resultpost_branch[] = array(
                    'id' => $medical_id,
                    'medical_name' => $medical_name,
                    'listing_id' => $chat_id,
                    'listing_type' => '13',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'store_manager' => $store_manager,
                    'address1' => $address1,
                    'address2' => "",
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'contact_no' => $contact_no,
                    'whatsapp_no' => $whatsapp_no,
                    "exotel_no" => '02233721563',
                    'email' => $email,
                    'store_since' => $store_since,
                    'website' => $website,
                    'reach_area' => $ranges,
                    'store_distance' => $store_distance,
                    'Description' => $discount_description,
                    'distance' => $distance_root,
                    'distance_root'=>$distance_root,
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
                    'discount' => $discount,
                    'medicalwale_discount' => $medicalwale_discount,
                    'category_list' => $product_category_list,
                    'offer_discount' => $offer_discount,
                    'favourite' => $fav_branch_pharmacy
                );
                
               
               // array_push($resultpost,$resultpost_branch);
            } 
        }
       
      
      
          
            //added by zak for distance sorting
                
                 
                    //$resultpost = array_reverse($resultpost);
                    
       if($resultpost_branch!="")
      {
          //echo 'no branch';
          $resultpost  =  array_merge($resultpost_branch,$resultpost);
      }
      else
      {
        $resultpost  =  $resultpost;  
      }
               
               usort($resultpost, function($a, $b) {
                                $a = $a['favourite'];
                                $b = $b['favourite'];
                    if ($a == $b) { return 0; }
                        return ($a > $b) ? -1 : 1;
                    });
                    
            return $resultpost;
        }
        else{
          return array();
            
        }
    }
    
    public function e_prescription_list_bydoctor($user_id) {
        
        $prescription = "";
        function is_url_exist($url){
            $ch = curl_init($url);    
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
            if($code == 200){
               $status = true;
            }else{
              $status = false;
            }
            curl_close($ch);
           return $status;
        }
  $resultpost_doctor = array();
        $query = $this->db->query("SELECT booking_id FROM doctor_booking_master WHERE user_id='$user_id'");
        $list_count = $query->num_rows();
        if ($list_count > 0) {
            foreach ($query->result_array() as $row) {
                $prescription = "";
               $booking_id = $row['booking_id'];
              
                    $query_booking = $this->db->query("SELECT id FROM doctor_prescription WHERE booking_id='$booking_id'");
                    $list_count1 = $query_booking->num_rows();
                    
                    if ($list_count1 > 0) {
                         $id = $query_booking->row()->id;
                        $prescription = 'https://doctor.medicalwale.com/prescription/'.$id.'.pdf' ;
                       
                             
                             $resultpost_doctor[] = array(
                                'id' => $id,
                                'e_prescription' => $prescription
                             );
                       
                        
                    }
                  
                   
            } 
           
           
        } else {
            $resultpost_doctor = array();
        }
        
     //   $resultpost  =  array_merge($resultpost_doctor,$resultpost_user);
        return $resultpost_doctor;
    } 
    
     public function e_prescription_list_byuser($user_id) {
        
        $prescription = "";
        function is_url_exist($url){
            $ch = curl_init($url);    
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
            if($code == 200){
               $status = true;
            }else{
              $status = false;
            }
            curl_close($ch);
           return $status;
        }
        $querys = $this->db->query("SELECT id,for_whome,description,prescription_link,doctor_name,datetime FROM user_prescription WHERE user_id='$user_id' order by datetime desc");
        $list_counts = $querys->num_rows();
        if ($list_counts > 0) {
          
            foreach ($querys->result_array() as $row) {
                 
                $prescriptions = $row['prescription_link'];
                $description = $row['description'];
                $dates = date('Y-m-d',strtotime($row['datetime']));
                $id = $row['id'];
                if(empty($description))
                {
                    $description = "";
                }
                
                $for_whome = $row['for_whome'];
                if(empty($for_whome))
                {
                    $for_whome = "";
                }
                
                $doctor_name = $row['doctor_name'];
                if(empty($doctor_name))
                {
                    $doctor_name = "";
                }
                $prescriptions1= 'https://s3.amazonaws.com/medicalwale/images/prescription_images/'.$prescriptions;
                if(is_url_exist($prescriptions1))
                {  
                    $resultpost_user[] = array(
                                'id' => $id,
                                'e_prescription' => $prescriptions1,
                                'descrption' => $description,
                                'for_whome' => $for_whome,
                                'doctor_name' => $doctor_name,
                                'date' => $dates
                             );
                }
              /*  else
                {
                    $resultpost_user = array();
                }
                    */        
            }
                        
        }
        else
        {
            $resultpost_user = array();
        }
      
       // $resultpost  =  array_merge($resultpost_doctor,$resultpost_user);
        return $resultpost_user;
    }
    
    public function add_e_prescription($user_id,$story_file,$description,$for_whome,$doctor_name) {
        date_default_timezone_set('Asia/Kolkata');
        $created_date = date('Y-m-d H:i:s');
        $img = array();
        $img1 = "";
        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP", "PDF", "pdf");
            include('../s3_config.php'); 
           /* function getExtension($str)
            {
                $i = strrpos($str, ".");
                if (!$i)
                {
                    return "";
                }
                
                $l   = strlen($str) - $i;
                $ext = substr($str, $i + 1, $l);
                return $ext;
            }*/
            if($story_file > 0) {
                $flag = '1';
                $video_flag = '1';
                $desc = array();
                if(!empty($description))
                {
                    $description = $description.',';
                    $desc = explode(",",$description);
                }
                
                $for_w = array();
                if(!empty($for_whome))
                {
                    $for_whome = $for_whome.',';
                    $for_w = explode(",",$for_whome);
                }
                
                $doct_n = array();
                if(!empty($doctor_name))
                {
                    $doctor_name = $doctor_name.',';
                    $doct_n = explode(",",$doctor_name);
                }
                $i=0;
                foreach ($_FILES['pre_images']['tmp_name'] as $key => $tmp_name) {
                    $img_name = $key . $_FILES['pre_images']['name'][$key];
                    $img_size = $_FILES['pre_images']['size'][$key];
                    $img_tmp = $_FILES['pre_images']['tmp_name'][$key];
                    $ext = getExtension($img_name);
                    if (strlen($img_name) > 0) {
                            if (in_array($ext, $img_format)) {
                                $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/prescription_images/' . $actual_image_name;
                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                    if(!empty($desc))
                                    {
                                        $d = $desc[$i];
                                    }
                                    else
                                    {
                                        $d = "";
                                    }
                                    
                                    if(!empty($for_w))
                                    {
                                        $f = $for_w[$i];
                                    }
                                    else
                                    {
                                        $f = "";
                                    }
                                    
                                    if(!empty($doct_n))
                                    {
                                        $do = $doct_n[$i];
                                    }
                                    else
                                    {
                                        $do = "";
                                    }
                                    $data = array(
                                        'user_id' => $user_id,
                                        'prescription_link' => $actual_image_name,
                                        'description' => $d,
                                        'for_whome' => $f,
                                        'doctor_name' => $do,
                                        'datetime' => $created_date
                                    );

                                    $event_insert = $this->db->insert('user_prescription', $data);   
                                }
                                $i++;
                            }
                        
                    }
                }
                
            }
           
            return array(
                'status' => 200,
                'message' => 'success'
                
            );
        
    }
    
    public function update_email($user_id, $data){
      
        $this->db->where('id', $user_id);
        $this->db->update('users', $data);    
        if($this->db->affected_rows() > 0){
            return array(
            'status' => 200,
            'message' => 'success'
            );
        }else{
            return array(
            'status' => 201,
            'message' => 'error'
            );
        }
        
    }
    
    
     public function life_saving_drugs($user_id,$member_id,$qty,$Final,$number,$email_id)
    {
        include('s3_config.php');
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
         if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) 
            {
                $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                include('s3_config.php');
                $img_name = $_FILES['image']['name'];
                $img_size = $_FILES['image']['size'];
                $img_tmp = $_FILES['image']['tmp_name'];
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
        $invoice_no = date("YmdHis");
        $order_status = 'Awaiting Confirmation';
        $action_by = 'customer';
        $p_results = $this->db->query("INSERT INTO `life_saving_drugs`( `user_id`, `member_id`,`qty`, `urgent`, `mobile`, `email`, `image`, `created_at`,`invoice_no`,`order_status`,`action_by`) VALUES('$user_id','$member_id','$qty','$Final',$number,'$email_id','$profile_pic','$date','$invoice_no','$order_status','$action_by')");
        $insert_id = $this->db->insert_id();
               
           
                
                
               return $invoice_no; 
           
    }
   
     public function life_saving_drugs_cancel($order_id, $cancel_reason) 
     {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        $updated_at = date('Y-m-d H:i:s');
        $order_type_query = $this->db->query("select order_type,user_id,listing_type from user_order where invoice_no='$order_id' ");
        $get_order_info = $order_type_query->row_array();
        $order_type = $get_order_info['order_type'];
        $user_id = $get_order_info['user_id'];
        $listing_type = $get_order_info['listing_type'];
        $res_status = $this->db->query("select order_status from life_saving_drugs where invoice_no='$order_id' limit 1");
            $o_status = $res_status->row_array();
            $check_status = $o_status['order_status'];
            if ($check_status == 'Order Delivered') {
                return array(
                    'status' => 201,
                    'message' => 'Order Delivered'
                );
            } else {
                $update = $this->db->query("UPDATE `life_saving_drugs` SET `updated_at`='$updated_at',`order_status`='Order Cancelled',`cancel_reason`='$cancel_reason',`action_by`='customer' WHERE invoice_no='$order_id'");
                $update1=$this->db->affected_rows(); 
                if ($update1 > 0) {
                return array(
                            'status' => 200,
                            'message' => 'Order Cancelled'
                        );
                }
                else
                {
                     return array(
                        'status' => 201,
                        'message' => 'failed'
                    );
                }
            }
               
                }
                
                
                // SELECT uo.invoice_no,uo.user_id,uo.listing_id,uo.lat as customer_lat,uo.lng as customer_lng,ms.lat as store_lat,ms.lng as store_lng, ml.lat as mno_lat, ml.lng as mno_lng FROM `user_order` as uo left join medical_stores as ms on (ms.user_id = uo.listing_id ) left join mno_orders as mo on (uo.invoice_no = mo.invoice_no) left join mno_location as ml on (ml.mno_id = mo.mno_id) WHERE uo.`invoice_no` LIKE '20190724165059' AND uo.`user_id` = '38334' GROUP by uo.invoice_no ORDER BY ml.`id` DESC 
                
    // PharmacyModel
    public function order_tracking_mno($user_id, $invoice_no){
         $this->load->model('All_booking_model');
       $customer_lat = $customer_lng = $store_lat = $store_lng = $mno_lat = $mno_lng = $mno_name = $mno_contact = null;
         
        $tracker = array();
       
        $data = $details = array();
       
        $order_details = $this->db->query("SELECT uo.invoice_no,uo.user_id,uo.listing_id,uo.lat as customer_lat,uo.lng as customer_lng,ms.lat as store_lat,ms.lng as store_lng, msp.lat as suggested_pharmacy_lat, msp.lng as suggested_pharmacy_lng FROM `user_order` as uo left join medical_stores as ms on (ms.user_id = uo.listing_id ) left join mno_suggested_pharmacies as msp on (msp.id = uo.suggested_pharmacy_id)  WHERE uo.`invoice_no` LIKE '$invoice_no' AND uo.`user_id` = '$user_id'  GROUP by uo.invoice_no")->row_array();
        if(sizeof($order_details) > 0){
            $data['status'] = 1; // data found 
            
            $get_mno_details = $this->db->query("SELECT mo.* FROM `mno_orders` as mo WHERE mo.`invoice_no` LIKE '$invoice_no' AND mo.`ongoing` = 1 AND (mo.status = 'accepted' OR mo.status = '') order by mo.id desc")->row_array();
            
            $status =  $get_mno_details['status'];
            $mno_id = $get_mno_details['mno_id'];
            
            
            $customer_lat = $order_details['customer_lat'];
            $customer_lng = $order_details['customer_lng'];
                
            if($status == "accepted"){
                 
       
       
                $locationDEtails = $this->db->query("SELECT ml.lat, ml.lng, ml.mno_id, u.name, u.phone, u.email from mno_location as ml left join users as u on (ml.mno_id = u.id) where ml.mno_id = '$mno_id' order by ml.id desc limit 1 ")->row_array();
                
                $mno_lat = $locationDEtails['lat'];
                $mno_lng = $locationDEtails['lng'];
                
                $store_lat = $order_details['store_lat'];
                $store_lng = $order_details['store_lng'];
                
                $suggested_pharmacy_lat = $order_details['suggested_pharmacy_lat'];
                $suggested_pharmacy_lng = $order_details['suggested_pharmacy_lng'];
                
                if(($store_lat == null || $store_lng == null || $store_lat == "" || $store_lng == "") && ($suggested_pharmacy_lat != null ||
$suggested_pharmacy_lng != null ) && ($suggested_pharmacy_lat != "" ||
$suggested_pharmacy_lng != "" )){
                    $store_lat = $suggested_pharmacy_lat;
                    $store_lng = $suggested_pharmacy_lng;
                }
            
                $mno_name = $locationDEtails['name'];
                $mno_contact = $locationDEtails['phone'];
                
                
                
                // $data['data'] = $details;
                
            } else {
                // not accepted yet
                
                // $data['data'] = $details;
                $data['status'] = 3;
            }
            
        } else {
            $data['status'] = 1;  // no data found
        }
        
       
                $tracker = $this->All_booking_model->pharmacy_tracker($user_id, $invoice_no);
        
        $details['customer_lat'] = $customer_lat;
        $details['customer_lng'] = $customer_lng;
        $details['store_lat'] = $store_lat;
        $details['store_lng'] = $store_lng;
        $details['mno_lat'] = $mno_lat;
        $details['mno_lng'] = $mno_lng;
        $details['mno_name'] = $mno_name;
        $details['mno_contact'] = $mno_contact;
        $details['tracker'] = $tracker;     
        
       
     
        $data['data'] = $details;
        // print_r($data); die();
              
        return $data;
    }
    
    
    
      public function pharmacy_order_v1($user_id, $mlat, $mlng) 
   {
        $radius = 500;
        
       $insta_order=array();
       $select_order=array();
       $exclusives=array();
        $sql = "SELECT * FROM pharmacy_select";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if($count>0)
          {
            foreach ($query->result_array() as $row) 
                    {
                      $id=$row['id'];    
                      $image=$row['image'];
                      $name=$row['name'];
                      $info=$row['info'];
                      $type=$row['type'];
                      
                     
                     
                      
                          $user_id='0';
                          $count2='0';
                         
                       
                          if($type=="5")
                         {
                           
                           $user_id="50098";    
                         }
                 elseif($type=="7")
                         {
                             $user_id="60566";  
                                
                         } 
                         elseif($type=="6")
                         {
                             if($id=="4")
                               {
                                $user_id="60566";  
                               }
                               else
                               {

                               $sql2 = "SELECT `lat`, `lng`, `profile_pic`, `discount`,pharmacy_branch_user_id,(6371 * acos ( cos ( radians($mlat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($mlng) )+ sin ( radians($mlat) )* sin( radians( lat ) ))
) AS distance FROM medical_stores  WHERE user_id='51788' and pharmacy_branch_user_id!='0' HAVING distance < '100' ORDER BY distance LIMIT 0,1";
                             
                              $query2 = $this->db->query($sql2);
                               $count2 = $query2->num_rows();
                              if($count2>0)
                                {
                                   $row2=$query2->row_array();  
                                   
                                   $user_id=$row2['pharmacy_branch_user_id'];
                                } 
                               }
                         } 
                        
                         $resultpost[] = array('id'=> $id,
                                                 'image' => $image,
                                                 'name' => $name,
                                                 'type'=>$type,
                                                 'info'=>$info,
                                                 'user_id'=>$user_id
                                     ); 
                        
                      
                     
                    }
                    
          }
          else
          {
             $resultpost=array(); 
            
          }
        
        
          return $resultpost;
       
   } 
}

?>
