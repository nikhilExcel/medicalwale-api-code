<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class LabcenterModel extends CI_Model
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
    public function labcenter_list($lat, $lng, $user_id, $category_id, $hospital_type)
    {
        if($hospital_type == ''){
            $radius = '5';
            $sql    = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE `is_active` = 1 AND FIND_IN_SET($category_id, cat_id) HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($lat), ($lng), ($lat), ($radius));
            $query  = $this->db->query($sql);
            $count  = $query->num_rows();
        }else{
            $radius = '5000';
            $sql    = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE `is_active` = 1 AND is_vendor = $hospital_type HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($lat), ($lng), ($lat), ($radius));
            $query  = $this->db->query($sql);
            $count  = $query->num_rows(); 
        }
       
        
        
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
                $opening_hours     = $row['opening_hours'];
                $lat               = $row['latitude'];
                $lng               = $row['longitude'];
                $listing_type      = '10';
                $rating            = '4.0';
                $profile_views     = '';
                $reviews           = '1000';
                $user_discount     = $row['user_discount'];
                $image             = $row['profile_pic'];
               // $image             = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;
               $image1             = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                $features_array    = array();
                $feature_query     = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $features . "')");
                foreach ($feature_query->result_array() as $get_list) {
                    $feature          = $get_list['feature'];
                    $features_array[] = array(
                        "name" => $feature
                    );
                }
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
                $followers   = $this->db->select('id')->from('follow_user')->where('parent_id', $labcenter_user_id)->get()->num_rows();
                $following   = $this->db->select('id')->from('follow_user')->where('user_id', $labcenter_user_id)->get()->num_rows();
                $is_follow   = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $labcenter_user_id)->get()->num_rows();
               
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                $package_list  = array();
                $package_query = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' and branch_id ='' order by id asc");
                //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id      = $package_row['id'];
                        $package_name    = $package_row['lab_name'];
                        $package_details = $package_row['lab_details'];
                        $price           = $package_row['Price'];
                        $image           = $package_row['image'];
                        $home_delivery   = $package_row['home_delivery'];
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $package_list[]  = array(
                            'package_id' => $package_id,
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price,
                            'home_delivery' => $home_delivery
                        );
                    }
                } else {
                    $package_list = array();
                }
                $branch_list  = array();
                $branch_query = $this->db->query("SELECT * FROM `lab_center_branch` WHERE user_id='$labcenter_user_id' order by id asc");
                //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                $branch_count = $branch_query->num_rows();
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $branch_row) {
                        $branch_id                              = $branch_row['id'];
                        $branch_listing_id                      = $branch_row['user_id'];
                        $branch_path_pick_up                    = $branch_row['path_pick_up'];
                        $branch_path_sample_pickup_price_option = $branch_row['path_sample_pickup_price_option'];
                        $branch_path_sample_pickup_price        = $branch_row['path_sample_pickup_price'];
                        $branch_path_delivery_charge            = $branch_row['path_delivery_charge'];
                        $branch_path_delivery_price_options     = $branch_row['path_delivery_price_option'];
                        $branch_path_sample_delivery_price      = $branch_row['path_sample_delivery_price'];
                        $branch_diag_delivery_charge            = $branch_row['diag_delivery_charge'];
                        $branch_diag_delivery_price_option      = $branch_row['diag_delivery_price_option'];
                        $branch_diag_sample_delivery_price      = $branch_row['diag_sample_delivery_price'];
                        $branch_details                         = $branch_row['fnac_pick_up'];
                        /* $branch_details = $branch_row['fnac_sample_pickup_price_option '];
                        $branch_details = $branch_row['fnac_sample_pickup_price '];
                        $branch_details = $branch_row['fnac_delivery_charge'];
                        $branch_details = $branch_row['fnac_sample_delivery_price_option'];
                        $branch_details = $branch_row['fnac_sample_delivery_price '];*/
                        $branch_reg_date                        = $branch_row['reg_date'];
                        $branch_lab_branch_name                 = $branch_row['lab_branch_name'];
                        /*$branch_packages = $branch_row['packages'];*/
                        $branch_store_manager                   = $branch_row['store_manager'];
                        $branch_latitude                        = $branch_row['latitude'];
                        $branch_map_location                    = $branch_row['map_location'];
                        $branch_longitude                       = $branch_row['longitude'];
                        $branch_address1                        = $branch_row['address1'];
                        $branch_address2                        = $branch_row['address2'];
                        $branch_pincode                         = $branch_row['pincode'];
                        $branch_features                        = $branch_row['features'];
                        $branch_city                            = $branch_row['city'];
                        $branch_state                           = $branch_row['state'];
                        $branch_contact_no                      = $branch_row['contact_no'];
                        $branch_whatsapp_no                     = $branch_row['whatsapp_no'];
                        $branch_email                           = $branch_row['email'];
                        $branch_profile_pic                     = $branch_row['profile_pic'];
                        $branch_about_us                        = $branch_row['about_us'];
                        $branch_reach_area                      = $branch_row['reach_area'];
                        $branch_distance                        = $branch_row['distance'];
                        $branch_store_since                     = $branch_row['store_since'];
                        $branch_opening_hours                   = $branch_row['opening_hours'];
                        $branch_home_deliverys                  = $branch_row['home_delivery'];
                        $branch_branch_profile                  = $branch_row['branch_profile'];
                        $branch_delivery_charges                = $branch_row['delivery_charges'];
                        $branch_payment_type                    = $branch_row['payment_type'];
                        $branch_discount                        = $branch_row['discount'];
                        $branch_services                        = $branch_row['services'];
                        $branch_home_visit                      = $branch_row['home_visit'];
                        $branch_features_array                  = array();
                        $feature_query                          = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $branch_features . "')");
                        foreach ($feature_query->result_array() as $get_list) {
                            $feature                 = $get_list['feature'];
                            $branch_features_array[] = array(
                                "name" => $feature
                            );
                        }
                        $branch_final_Day      = array();
                        $day_array_list_branch = explode('|', $branch_opening_hours);
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
                        $current_day         = "";
                        $package_branch_list = array();
                        $package_query       = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' and branch_id='$branch_id' order by id asc");
                        //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                        $package_count       = $package_query->num_rows();
                        if ($package_count > 0) {
                            foreach ($package_query->result_array() as $package_row) {
                                $package_id            = $package_row['id'];
                                $package_name          = $package_row['lab_name'];
                                $package_details       = $package_row['lab_details'];
                                $price                 = $package_row['Price'];
                                $image                 = $package_row['image'];
                                $home_delivery         = $package_row['home_delivery'];
                                //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                                $package_branch_list[] = array(
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price,
                                    'home_delivery' => $home_delivery
                                );
                            }
                        } else {
                            $package_branch_list = array();
                        }
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $branch_list[] = array(
                            'branch_id' => $branch_id,
                            'branch_path_pick_up' => $branch_path_pick_up,
                            'branch_path_sample_pickup_price_option' => $branch_path_sample_pickup_price_option,
                            'branch_path_sample_pickup_price' => $branch_path_sample_pickup_price,
                            'branch_path_delivery_charge' => $branch_path_delivery_charge,
                            'branch_path_delivery_price_options' => $branch_path_delivery_price_options,
                            'branch_path_sample_delivery_price' => $branch_path_sample_delivery_price,
                            'branch_diag_delivery_charge' => $branch_diag_delivery_charge,
                            'branch_diag_delivery_price_option' => $branch_diag_delivery_price_option,
                            'branch_diag_sample_delivery_price' => $branch_diag_sample_delivery_price,
                            'branch_details' => $branch_details,
                            'branch_reg_date' => $branch_reg_date,
                            'branch_lab_branch_name' => $branch_lab_branch_name,
                            'branch_store_manager' => $branch_store_manager,
                            'branch_latitude' => $branch_latitude,
                            'branch_map_location' => $branch_map_location,
                            'branch_longitude' => $branch_longitude,
                            'branch_address1' => $branch_address1,
                            'branch_address2' => $branch_address2,
                            'branch_pincode' => $branch_pincode,
                            'branch_features' => $branch_features_array,
                            'branch_city' => $branch_city,
                            'branch_state' => $branch_state,
                            'branch_contact_no' => $branch_contact_no,
                            'branch_whatsapp_no' => $branch_whatsapp_no,
                            'branch_email' => $branch_email,
                            'branch_opening_day' => $branch_final_Day,
                            'branch_profile_pic' => $branch_profile_pic,
                            'branch_about_us' => $branch_about_us,
                            'branch_reach_area' => $branch_reach_area,
                            'branch_distance' => $branch_distance,
                            'branch_store_since' => $branch_store_since,
                            'branch_package' => $package_branch_list
                        );
                    }
                } else {
                    $branch_list = array();
                }
                $test_in_lab_list = array();
                $test_in_home_list = array();
                $test_query       = $this->db->query("SELECT * FROM `lab_test_details` WHERE user_id='$labcenter_user_id' order by id asc");
                $test_count       = $test_query->num_rows();
                if ($test_count > 0) {
                    foreach ($test_query->result_array() as $test_row) {
                        $user_id        = $test_row['user_id'];
                        $test           = $test_row['test'];
                        $test_id        = $test_row['test_id'];
                        $price          = $test_row['price'];
                        $offer          = $test_row['offer'];
                        $executive_rate = $test_row['executive_rate'];
                        $home_delivery  = $test_row['home_delivery'];
                        if ($home_delivery == "0") {
                            $test_in_lab_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test,
                                'test' => $test_id,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "0"
                            );
                        } else {
                            $test_in_home_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test_id,
                                'test' => $test,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "1"
                            );
                        }
                    }
                } else {
                    $test_in_lab_list  = array();
                    $test_in_home_list = array();
                }
                
                 $query_count = $this->db->query("SELECT count(user_id) as total_view FROM profile_views_master WHERE listing_id='$labcenter_user_id' ");
                    $TTL_View = $query_count->row()->total_view;
        
        
                //Review Count
                //echo "SELECT * FROM `labcenter_review` WHERE labcenter_id='$id'";
                //$Review_query = $this->db->query("SELECT * FROM `labcenter_review` WHERE labcenter_id='$id'");
                $Review_query = $this->db->query("SELECT labcenter_review.id FROM `labcenter_review` INNER JOIN `users` ON labcenter_review.user_id=users.id WHERE labcenter_review.labcenter_id='$id'");
                $Review_count = $Review_query->num_rows();
        
                $resultpost[] = array(
                    "id" => $id,
                    "lab_user_id" => $labcenter_user_id,
                    "name" => $lab_name,
                    "listing_type" => '10',
                    "features" => $features_array,
                    "home_delivery" => $home_delivery,
                    "delivery_charges" => $delivery_charges,
                    "address1" => $address1,
                    "address2" => $address2,
                    "pincode" => $pincode,
                    "city" => $city,
                    "exotel_no" => '02233721563',
                    "state" => $state,
                    "contact_no" => $contact_no,
                    "whatsapp_no" => $whatsapp_no,
                    "email" => $email,
                    "rating" => $rating,
                    "followers" => $followers,
                    "following" => $following,
                    "profile_views" => $TTL_View,
                    "reviews" => $Review_count,
                    "is_follow" => $is_follow,
                    "lat" => $lat,
                    "lng" => $lng,
                    'opening_day' => $final_Day,
                    "image" => $image1,
                    "user_discount" => $user_discount,
                    "package_list" => $package_list,
                    "branch_list" => $branch_list,
                    "home_test_done" => $test_in_home_list,
                    "lab_test_done" => $test_in_lab_list
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    //added by zak for lab details 
    public function labcenter_details($user_id, $listing_id)
    {
        
                // Labs
               $sql = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `discount`,`lab_name`,`address1`,`address2`, `pincode`, `city`, `user_discount`,`state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`, IFNULL(delivery_charges,'') AS delivery_charges FROM lab_center where `is_active` = 1 AND user_id='$listing_id'");
               // $sql = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_branch_name`,`address1`,`address2`, `pincode`, `city`, `discount`,`state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`, IFNULL(delivery_charges,'') AS delivery_charges FROM lab_center_branch where user_id='$listing_id'");
                //echo "SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_branch_name`,`address1`,`address2`, `pincode`, `city`, `discount`,`state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`, IFNULL(delivery_charges,'') AS delivery_charges FROM lab_center_branch where user_id='$listing_id'";
                
                $query = $this->db->query($sql);
                $count = $query->num_rows();
          
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                        $labcenter_user_id = $row['user_id'];
                        $lab_name = $row['lab_name'];
                        $features = $row['features'];
                        $home_delivery = $row['home_delivery'];
                        $delivery_charges = $row['delivery_charges'];
                        $address1 = $row['address1'];
                        $address2 = $row['address2'];
                        $pincode = $row['pincode'];
                        $city = $row['city'];
                        $state = $row['state'];
                        $contact_no = $row['contact_no'];
                        $whatsapp_no = $row['whatsapp_no'];
                        $email = $row['email'];
                        $opening_hours = $row['opening_hours'];
                        $lat = $row['latitude'];
                        $lng = $row['longitude'];
                        $rating = '4.0';
                        $profile_views = '1548';
                        $reviews = '1000';
                        $user_discount = $row['discount'];
                        $image = $row['profile_pic'];
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;

                        $features_array = array();
                        if($features != ''){
                            $feature_query = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $features . "')");
                            foreach ($feature_query->result_array() as $get_list) {
    
                                $feature = $get_list['feature'];
                                $features_array[] = array(
                                    "name" => $feature
                                );
                            }
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



                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $labcenter_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $labcenter_user_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $labcenter_user_id)->get()->num_rows();
                       
                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }

 //********************************************added by jakir on 3 aug 2018 for lab changes*************************************************** 
                  $package_list = array();
                $package_query = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' and branch_id ='' order by id asc");
                //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id = $package_row['id'];
                        $package_name = $package_row['lab_name'];
                        $package_details = $package_row['lab_details'];
                        $price = $package_row['Price'];
                        $image = $package_row['image'];
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $package_list[] = array(
                            'package_id' => $package_id,
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price
                        );
                    }
                } else {
                    $package_list = array();
                }
                
                $branch_list = array();
                $branch_id ="";
                $branch_query = $this->db->query("SELECT * FROM `lab_center_branch` WHERE user_id='$labcenter_user_id' order by id asc");
                //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                $branch_count = $branch_query->num_rows();
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $branch_row) {
                        $branch_id                              = $branch_row['id'];
                        $branch_listing_id                      = $branch_row['user_id'];
                        $branch_path_pick_up                    = $branch_row['path_pick_up'];
                        $branch_path_sample_pickup_price_option = $branch_row['path_sample_pickup_price_option'];
                        $branch_path_sample_pickup_price        = $branch_row['path_sample_pickup_price'];
                        $branch_path_delivery_charge            = $branch_row['path_delivery_charge'];
                        $branch_path_delivery_price_options     = $branch_row['path_delivery_price_option'];
                        $branch_path_sample_delivery_price      = $branch_row['path_sample_delivery_price'];
                        
                        $branch_diag_delivery_charge            = $branch_row['diag_delivery_charge'];
                        $branch_diag_delivery_price_option      = $branch_row['diag_delivery_price_option'];
                        $branch_diag_sample_delivery_price      = $branch_row['diag_sample_delivery_price'];
                        
                        $branch_details                         = $branch_row['fnac_pick_up'];
                       /* $branch_details = $branch_row['fnac_sample_pickup_price_option '];
                        $branch_details = $branch_row['fnac_sample_pickup_price '];
                        $branch_details = $branch_row['fnac_delivery_charge'];
                        $branch_details = $branch_row['fnac_sample_delivery_price_option'];
                        $branch_details = $branch_row['fnac_sample_delivery_price '];*/
                        
                        $branch_reg_date                = $branch_row['reg_date'];
                        $branch_lab_branch_name         = $branch_row['lab_branch_name'];
                        /*$branch_packages = $branch_row['packages'];*/
                        $branch_store_manager           = $branch_row['store_manager'];
                        $branch_latitude                = $branch_row['latitude'];
                        $branch_map_location            = $branch_row['map_location'];
                        $branch_longitude               = $branch_row['longitude'];
                        $branch_address1                = $branch_row['address1'];
                        $branch_address2                = $branch_row['address2'];
                        $branch_pincode                 = $branch_row['pincode'];
                        $branch_features                = $branch_row['features'];
                        $branch_city                    = $branch_row['city'];
                        $branch_state                   = $branch_row['state'];
                        $branch_contact_no              = $branch_row['contact_no'];
                        $branch_whatsapp_no             = $branch_row['whatsapp_no'];
                        $branch_email                   = $branch_row['email'];
                        $branch_profile_pic             = $branch_row['profile_pic'];
                        $branch_about_us                = $branch_row['about_us'];
                        $branch_reach_area              = $branch_row['reach_area'];
                        $branch_distance                = $branch_row['distance'];
                        $branch_store_since             = $branch_row['store_since'];
                        $branch_opening_hours           = $branch_row['opening_hours'];
                        $branch_home_deliverys          = $branch_row['home_delivery'];
                        $branch_branch_profile          = $branch_row['branch_profile'];
                        $branch_delivery_charges        = $branch_row['delivery_charges'];
                        $branch_payment_type            = $branch_row['payment_type'];
                        $branch_discount                = $branch_row['discount'];
                        $branch_services                = $branch_row['services'];
                        $branch_home_visit              = $branch_row['home_visit'];
                        
                        
                        $branch_features_array = array();

                        $feature_query = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $branch_features . "')");
                        foreach ($feature_query->result_array() as $get_list) {
        
                            $feature = $get_list['feature'];
                            $branch_features_array[] = array(
                                "name" => $feature
                            );
                        }
        
                        $branch_final_Day = array();
                        $day_array_list_branch = explode('|', $branch_opening_hours);
                        if (count($day_array_list_branch) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch); $i++) {
                                $day_list = explode('>', $day_array_list_branch[$i]);
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
                        $current_day = "";
                        
                        
                        
                        $package_branch_list = array();
                        $package_query = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' and branch_id='$branch_id' order by id asc");
                        //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                        $package_count = $package_query->num_rows();
                        if ($package_count > 0) {
                            foreach ($package_query->result_array() as $package_row) {
                                $package_id = $package_row['id'];
                                $package_name = $package_row['lab_name'];
                                $package_details = $package_row['lab_details'];
                                $price = $package_row['Price'];
                                $image = $package_row['image'];
                                //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                                $package_branch_list[] = array(
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price
                                );
                            }
                        } else {
                            $package_branch_list = array();
                        }
                        
                        
                        
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $branch_list[] = array(
                            'branch_id' => $branch_id,
                            'branch_path_pick_up' => $branch_path_pick_up,
                            'branch_path_sample_pickup_price_option' => $branch_path_sample_pickup_price_option,
                            'branch_path_sample_pickup_price' => $branch_path_sample_pickup_price,
                            'branch_path_delivery_charge' => $branch_path_delivery_charge,
                            'branch_path_delivery_price_options' => $branch_path_delivery_price_options,
                            'branch_path_sample_delivery_price' => $branch_path_sample_delivery_price,
                            'branch_diag_delivery_charge' => $branch_diag_delivery_charge,
                            'branch_diag_delivery_price_option' => $branch_diag_delivery_price_option,
                            'branch_diag_sample_delivery_price' => $branch_diag_sample_delivery_price,
                            'branch_details' => $branch_details,
                            'branch_reg_date' => $branch_reg_date,
                            'branch_lab_branch_name' => $branch_lab_branch_name,
                            'branch_store_manager' =>$branch_store_manager, 
                            'branch_latitude'=>$branch_latitude,
                            'branch_map_location'=>$branch_map_location,
                            'branch_longitude'=>$branch_longitude,
                            'branch_address1'=>$branch_address1,
                            'branch_address2'=>$branch_address2,
                            'branch_pincode'=>$branch_pincode,
                            'branch_features'=>$branch_features_array,
                            'branch_city'=>$branch_city,
                            'branch_state'=>$branch_state,
                            'branch_contact_no'=>$branch_contact_no,
                            'branch_whatsapp_no'=>$branch_whatsapp_no,
                            'branch_email'=>$branch_email,
                            'branch_opening_day' => $branch_final_Day,
                            'branch_profile_pic'=>$branch_profile_pic,
                            'branch_about_us'=>$branch_about_us,
                            'branch_reach_area'=>$branch_reach_area,
                            'branch_distance'=>$branch_distance,
                            'branch_store_since'=>$branch_store_since,
                            'branch_package'=>$package_branch_list
                        );
                    }
                } else {
                    $branch_list = array();
                }
                
                
                ///Lab test added by ghanshyam parihar starts
                
                // $test_branch_list = array();
                // $test_query = $this->db->query("SELECT * FROM `lab_test_details` WHERE user_id='$labcenter_user_id' or branch_id='$branch_id' order by id asc");
                // //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                // $test_count = $test_query->num_rows();
                // if ($test_count > 0) {
                //     foreach ($test_query->result_array() as $test_row) {
                //         $test_id = $test_row['id'];
                //         $test_name = $test_row['test'];
                //         $price = $test_row['price'];
                //         $discount = $test_row['discount'];
                //         $offer = $test_row['offer'];
                //         $executive_rate = $test_row['executive_rate'];
                //         //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                //         $test_branch_list[] = array(
                //             'test_id' => $test_id,
                //             'test_name' => $test_name,
                //             'price' => $price,
                //             'offer' => $offer,
                //             'executive_rate' => $executive_rate,
                //             'discount' => $discount
                //         );
                //     }
                // } else {
                //     $test_branch_list = array();
                // }
                
                $test_in_lab_list = array();
                 $test_in_home_list = array();
                $test_query       = $this->db->query("SELECT * FROM `lab_test_details` WHERE user_id='$labcenter_user_id' order by id asc");
                $test_count       = $test_query->num_rows();
                if ($test_count > 0) {
                    foreach ($test_query->result_array() as $test_row) {
                        $user_id        = $test_row['user_id'];
                        $test           = $test_row['test'];
                        $test_id        = $test_row['test_id'];
                        $price          = $test_row['price'];
                        $offer          = $test_row['offer'];
                        $executive_rate = $test_row['executive_rate'];
                        $home_delivery  = $test_row['home_delivery'];
                        if ($home_delivery == "0") {
                            $test_in_lab_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test,
                                'test' => $test_id,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "0"
                            );
                        } else {
                            $test_in_home_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test_id,
                                'test' => $test,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "1"
                            );
                        }
                    }
                } else {
                    $test_in_lab_list  = array();
                    $test_in_home_list = array();
                }
                
                ///Lab test added by ghanshyam parihar ends

//********************************************************************************End by Jakir *******************************************
                        $resultpost[] = array(
                            "id" => $id,
                            "lab_user_id" => $labcenter_user_id,
                            "name" => $lab_name,
                            "features" => $features_array,
                            "home_delivery" => $home_delivery,
                            "delivery_charges" => $delivery_charges,
                            "address1" => $address1,
                            "address2" => $address2,
                            "pincode" => $pincode,
                            "city" => $city,
                            "state" => $state,
                            "contact_no" => $contact_no,
                            "whatsapp_no" => $whatsapp_no,
                            "exotel_no" => '02233721563',
                            "email" => $email,
                            "rating" => $rating,
                            "followers" => $followers,
                            "following" => $following,
                            "profile_views" => $profile_views,
                            "reviews" => $reviews,
                            "is_follow" => $is_follow,
                            "lat" => $lat,
                            "lng" => $lng,
                            'opening_day' => $final_Day,
                            "image" => $image,
                     "user_discount" => $user_discount,
                    "package_list"=> $package_list,
                    "branch_list"=>$branch_list,
                   // "test_list"=>$test_branch_list
                    "home_test_done" => $test_in_home_list,
                    "lab_test_done" => $test_in_lab_list
                        );
                    }
                } else {
                    $resultpost = array();
                }
            return $resultpost;
    }
    
    
    //added by zak for lab branch center 
    public function labcenter_branches_details($user_id, $listing_id,$branch_id)
    {
           $branch_list = array();
                $branch_query = $this->db->query("SELECT * FROM `lab_center_branch` WHERE user_id='$listing_id' AND id='$branch_id' order by id asc");
             //   echo "SELECT * FROM `lab_center_branch` WHERE user_id='$listing_id' AND id='$branch_id' order by id asc";
                $branch_count = $branch_query->num_rows();
            //    echo $branch_count;
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $branch_row) {
                        $branch_id                              = $branch_row['id'];
                        $branch_listing_id                      = $branch_row['user_id'];
                        $branch_path_pick_up                    = $branch_row['path_pick_up'];
                        $branch_path_sample_pickup_price_option = $branch_row['path_sample_pickup_price_option'];
                        $branch_path_sample_pickup_price        = $branch_row['path_sample_pickup_price'];
                        $branch_path_delivery_charge            = $branch_row['path_delivery_charge'];
                        $branch_path_delivery_price_options     = $branch_row['path_delivery_price_option'];
                        $branch_path_sample_delivery_price      = $branch_row['path_sample_delivery_price'];
                        
                        $branch_diag_delivery_charge            = $branch_row['diag_delivery_charge'];
                        $branch_diag_delivery_price_option      = $branch_row['diag_delivery_price_option'];
                        $branch_diag_sample_delivery_price      = $branch_row['diag_sample_delivery_price'];
                        
                        $branch_details                         = $branch_row['fnac_pick_up'];
                       /* $branch_details = $branch_row['fnac_sample_pickup_price_option '];
                        $branch_details = $branch_row['fnac_sample_pickup_price '];
                        $branch_details = $branch_row['fnac_delivery_charge'];
                        $branch_details = $branch_row['fnac_sample_delivery_price_option'];
                        $branch_details = $branch_row['fnac_sample_delivery_price '];*/
                        
                        $branch_reg_date                = $branch_row['reg_date'];
                        $branch_lab_branch_name         = $branch_row['lab_branch_name'];
                        /*$branch_packages = $branch_row['packages'];*/
                        $branch_store_manager           = $branch_row['store_manager'];
                        $branch_latitude                = $branch_row['latitude'];
                        $branch_map_location            = $branch_row['map_location'];
                        $branch_longitude               = $branch_row['longitude'];
                        $branch_address1                = $branch_row['address1'];
                        $branch_address2                = $branch_row['address2'];
                        $branch_pincode                 = $branch_row['pincode'];
                        $branch_features                = $branch_row['features'];
                        $branch_city                    = $branch_row['city'];
                        $branch_state                   = $branch_row['state'];
                        $branch_contact_no              = $branch_row['contact_no'];
                        $branch_whatsapp_no             = $branch_row['whatsapp_no'];
                        $branch_email                   = $branch_row['email'];
                        $branch_profile_pic             = $branch_row['profile_pic'];
                        $branch_about_us                = $branch_row['about_us'];
                        $branch_reach_area              = $branch_row['reach_area'];
                        $branch_distance                = $branch_row['distance'];
                        $branch_store_since             = $branch_row['store_since'];
                        $branch_opening_hours           = $branch_row['opening_hours'];
                        $branch_home_deliverys          = $branch_row['home_delivery'];
                        $branch_branch_profile          = $branch_row['branch_profile'];
                        $branch_delivery_charges        = $branch_row['delivery_charges'];
                        $branch_payment_type            = $branch_row['payment_type'];
                        $branch_discount                = $branch_row['discount'];
                        $branch_services                = $branch_row['services'];
                        $branch_home_visit              = $branch_row['home_visit'];
                        
                        
                        $branch_features_array = array();

                        $feature_query = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $branch_features . "')");
                        foreach ($feature_query->result_array() as $get_list) {
        
                            $feature = $get_list['feature'];
                            $branch_features_array[] = array(
                                "name" => $feature
                            );
                        }
        
                        $branch_final_Day = array();
                        $day_array_list_branch = explode('|', $branch_opening_hours);
                        if (count($day_array_list_branch) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch); $i++) {
                                $day_list = explode('>', $day_array_list_branch[$i]);
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
                        $current_day = "";
                        
                        
                        
                        $package_branch_list = array();
                        $package_query = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$listing_id' and branch_id='$branch_id' order by id asc");
                        //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                        $package_count = $package_query->num_rows();
                        if ($package_count > 0) {
                            foreach ($package_query->result_array() as $package_row) {
                                $package_id = $package_row['id'];
                                $package_name = $package_row['lab_name'];
                                $package_details = $package_row['lab_details'];
                                $price = $package_row['Price'];
                                $image = $package_row['image'];
                                //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                                $package_branch_list[] = array(
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price
                                );
                            }
                        } else {
                            $package_branch_list = array();
                        }
                        
                        
                        
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $branch_list[] = array(
                            'branch_id' => $branch_id,
                            'branch_path_pick_up' => $branch_path_pick_up,
                            'branch_path_sample_pickup_price_option' => $branch_path_sample_pickup_price_option,
                            'branch_path_sample_pickup_price' => $branch_path_sample_pickup_price,
                            'branch_path_delivery_charge' => $branch_path_delivery_charge,
                            'branch_path_delivery_price_options' => $branch_path_delivery_price_options,
                            'branch_path_sample_delivery_price' => $branch_path_sample_delivery_price,
                            'branch_diag_delivery_charge' => $branch_diag_delivery_charge,
                            'branch_diag_delivery_price_option' => $branch_diag_delivery_price_option,
                            'branch_diag_sample_delivery_price' => $branch_diag_sample_delivery_price,
                            'branch_details' => $branch_details,
                            'branch_reg_date' => $branch_reg_date,
                            'branch_lab_branch_name' => $branch_lab_branch_name,
                            'branch_store_manager' =>$branch_store_manager, 
                            'branch_latitude'=>$branch_latitude,
                            'branch_map_location'=>$branch_map_location,
                            'branch_longitude'=>$branch_longitude,
                            'branch_address1'=>$branch_address1,
                            'branch_address2'=>$branch_address2,
                            'branch_pincode'=>$branch_pincode,
                            'branch_features'=>$branch_features_array,
                            'branch_city'=>$branch_city,
                            'branch_state'=>$branch_state,
                            'branch_contact_no'=>$branch_contact_no,
                            'branch_whatsapp_no'=>$branch_whatsapp_no,
                            'branch_email'=>$branch_email,
                            'branch_opening_day' => $branch_final_Day,
                            'branch_profile_pic'=>$branch_profile_pic,
                            'branch_about_us'=>$branch_about_us,
                            'branch_reach_area'=>$branch_reach_area,
                            'branch_distance'=>$branch_distance,
                            'branch_store_since'=>$branch_store_since,
                            'branch_package'=>$package_branch_list
                        );
                    }
                } else {
                    $branch_list = array();
                }
                return $branch_list;
    }
    //end 
    
    public function labcenter_packages($labcenter_id)
    {
        $query = $this->db->query("SELECT id,packages,user_id FROM `lab_center` WHERE `is_active` = 1 AND id='$labcenter_id'");
        $count = $query->num_rows();
        //print_r($query->result_array());
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $packages           = $row['user_id'];
                //$labcenter_packages = $this->db->query("SELECT * FROM `lab_packages` WHERE FIND_IN_SET(lab_pack_name,'" . $packages . "')");
                $labcenter_packages = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='" . $packages . "'");
                foreach ($labcenter_packages->result_array() as $get_list) {
                    $package_id      = $get_list['id'];
                    $package_name    = $get_list['lab_pack_name'];
                    $package_details = $get_list['lab_pack_details'];
                    $price           = $get_list['price'];
                    $image           = $get_list['image'];
                    $image           = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;
                    $resultpost[]    = array(
                        "package_id" => $package_id,
                        "package_name" => $package_name,
                        "package_details" => $package_details,
                        'price' => $price,
                        'image' => $image
                    );
                }
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    public function lab_test_search($keyword, $category_id, $lab_user_id)
    {
        $resultpost = array();
        $query      = $this->db->query("SELECT id,user_id,cat_id,test,price FROM lab_test_details WHERE test LIKE '%$keyword%' AND cat_id='$category_id' AND user_id='$lab_user_id' limit 15");
        $count      = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $test_id       = $row['id'];
                $lab_id        = $row['user_id'];
                $test_name     = $row['test'];
                $product_price = $row['price'];
                $resultpost[]  = array(
                    "lab_test_id" => $test_id,
                    "test_name" => $test_name,
                    "test_price" => $product_price
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    public function add_review($user_id, $listing_id, $rating, $review, $service)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date         = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'labcenter_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('labcenter_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
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
        $review_count = $this->db->select('id')->from('labcenter_review')->where('labcenter_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT labcenter_review.id,labcenter_review.user_id,labcenter_review.labcenter_id,labcenter_review.rating,labcenter_review.review, labcenter_review.service,labcenter_review.date as review_date,users.id as user_id,users.name as firstname FROM `labcenter_review` INNER JOIN `users` ON labcenter_review.user_id=users.id WHERE labcenter_review.labcenter_id='$listing_id' order by labcenter_review.id desc");
            foreach ($query->result_array() as $row) {
                $id       = $row['id'];
                $user_id       = $row['user_id'];
                $username = $row['firstname'];
                $rating   = $row['rating'];
                $review   = $row['review'];
                $review   = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '9') {
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
                $like_count   = $this->db->select('id')->from('labcenter_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count   = $this->db->select('id')->from('labcenter_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('labcenter_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                $resultpost[] = array(
                    'id' => $id,
                    'user_id'=>$user_id,
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
        $review_count = $this->db->select('id')->from('labcenter_review')->where('labcenter_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT labcenter_review.id,labcenter_review.user_id,labcenter_review.labcenter_id,labcenter_review.rating,labcenter_review.review, labcenter_review.service,labcenter_review.date as review_date,users.id as user_id,users.name as firstname FROM `labcenter_review` INNER JOIN `users` ON labcenter_review.user_id=users.id WHERE labcenter_review.labcenter_id='$listing_id' order by labcenter_review.id desc");
            foreach ($query->result_array() as $row) {
                $id       = $row['id'];
                $username = $row['firstname'];
                $rating   = $row['rating'];
                $review   = $row['review'];
                $review   = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '9') {
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
                $like_count   = $this->db->select('id')->from('labcenter_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count   = $this->db->select('id')->from('labcenter_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('labcenter_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                        $review_list_count = $this->db->select('id')->from('labcenter_review_comment')->where('post_id', $id)->get()->num_rows();
                         if ($review_list_count) {
                              $resultcomment = array();
                        $querycomment = $this->db->query("SELECT labcenter_review_comment.id,labcenter_review_comment.post_id,labcenter_review_comment.comment as comment,labcenter_review_comment.date,users.name,labcenter_review_comment.user_id as post_user_id FROM labcenter_review_comment INNER JOIN users on users.id=labcenter_review_comment.user_id WHERE labcenter_review_comment.post_id='$id' order by labcenter_review_comment.id asc");
                        
                        foreach ($querycomment->result_array() as $rowc) {
                            $comment_id      = $rowc['id'];
                            $post_id = $rowc['post_id'];
                            $comment = $rowc['comment'];
                            $comment = preg_replace('~[\r\n]+~', '', $comment);
                           
                                $usernamec     = $rowc['name'];
                                $date         = $rowc['date'];
                                $post_user_id = $rowc['post_user_id'];
                                $like_countc   = $this->db->select('id')->from('labcenter_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                                $like_yes_noc  = $this->db->select('id')->from('labcenter_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                                $comment_date = get_time_difference_php($date);
                                $img_count    = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                                if ($img_count > 0) {
                                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                                    $img_file      = $profile_query->source;
                                    $userimagec     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                                } else {
                                    $userimagec = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                                }
                                $date         = get_time_difference_php($date);
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
        $count_query = $this->db->query("SELECT id from labcenter_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `labcenter_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from labcenter_review_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $labcenter_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('labcenter_review_likes', $labcenter_review_likes);
            $like_query = $this->db->query("SELECT id from labcenter_review_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }
    public function review_comment($user_id, $post_id, $comment)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at               = date('Y-m-d H:i:s');
        $labcenter_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('labcenter_review_comment', $labcenter_review_comment);
        $labcenter_review_comment_query = $this->db->query("SELECT id from labcenter_review_comment WHERE post_id='$post_id'");
        $total_comment                  = $labcenter_review_comment_query->num_rows();
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
        $count_query = $this->db->query("SELECT id from labcenter_review_comment_like WHERE comment_id='$comment_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `labcenter_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from labcenter_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $labcenter_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('labcenter_review_comment_like', $labcenter_review_comment_like);
            $comment_query      = $this->db->query("SELECT id from labcenter_review_comment_like where comment_id='$comment_id'");
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
        $review_list_count = $this->db->select('id')->from('labcenter_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT labcenter_review_comment.id,labcenter_review_comment.post_id,labcenter_review_comment.comment as comment,labcenter_review_comment.date,users.name,labcenter_review_comment.user_id as post_user_id FROM labcenter_review_comment INNER JOIN users on users.id=labcenter_review_comment.user_id WHERE labcenter_review_comment.post_id='$post_id' order by labcenter_review_comment.id asc");
            foreach ($query->result_array() as $row) {
                $id      = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '3') {
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
                $username     = $row['name'];
                $date         = $row['date'];
                $post_user_id = $row['post_user_id'];
                $like_count   = $this->db->select('id')->from('labcenter_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('labcenter_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    public function lab_booking($user_id, $listing_id, $address_line1, $address_line2, $package_id, $user_name, $mobile, $email, $gender, $branch_id, $branch_name, $vendor_id, $status, $payment_mode, $trail_booking_date, $trail_booking_time, $joining_date, $booking_location, $booking_address, $booking_mobile,$test_ids, $patient_id, $at_home, $city, $state, $pincode, $address_id,$booking_id){
        date_default_timezone_set('Asia/Kolkata');
        $created_at   = date('Y-m-d H:i:s');
        //$joining_date = date('Y-m-d');
        $query        = $this->db->query("SELECT * FROM booking_master WHERE booking_id = '$booking_id'");
        $count        = $query->num_rows();
        if ($count > 0) {
            $lab_booking = array(
                'user_id' => $user_id,
                'patient_id' => $patient_id,
                'booking_id' => $booking_id,
                'package_id' => $package_id,
                'listing_id' => $listing_id,
                'user_name' => $user_name,
                'user_email' => $email,
                'user_mobile' => $mobile,
                'user_gender' => $gender,
                'branch_id' => $branch_id,
                'vendor_id' => $vendor_id,
                'booking_date' => $created_at,
                'status' => $status,
                'trail_booking_date' => $joining_date,//$trail_booking_date,
                'trail_booking_time' => $trail_booking_time,
                'payment_mode' => $payment_mode,
                'joining_date' => $joining_date,
                'booking_location' => $booking_location,
                'booking_address' => $booking_address,
                'booking_mobile' => $booking_mobile
            );
            $this->db->where('booking_id', $booking_id);
            $rst = $this->db->update('booking_master', $lab_booking);
           
            if($package_id == ''){
         
                if ($test_ids != '') {
                    $Testids = explode(',', $test_ids);
                    foreach ($Testids as $tid) {
                        $test_data = array(
                            'booking_id' => $booking_id,
                            'test_id' => $tid
                        );
                        $this->db->where('booking_id', $booking_id);
                        $this->db->where('test_id', $tid);
                        $rst       = $this->db->update('booking_test', $test_data);
                    }
                }
            }
            
            
            $insertTolabBookingDetails = array(
                'user_id'=> $user_id,
                'patient_id'=> $patient_id,
                'listing_id'=> $listing_id,
                'vendor_type'=> $vendor_id,
                'branch_id'=> $branch_id,
                'branch_name'=> $branch_name,
                'at_home'=> $at_home,
                'address_line1'=> $address_line1,
                'address_line2'=> $address_line2,
                'city'=> $city,
                'state'=> $state,
                'pincode'=> $pincode,
                'mobile_no'=>$mobile ,
                'email_id'=> $email,
                'address_id'=> $address_id,
                'test_id'=> $test_ids,
                'package_id'=> $package_id,
                'booking_date'=> $joining_date,
                'booking_time'=> $trail_booking_time,
                'booking_id'=> $booking_id 
                );
            
            $this->db->where('booking_id', $booking_id);
            $rst_comp         = $this->db->update('lab_booking_details', $insertTolabBookingDetails);    
            
            //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file      = $profile_query->source;
                $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr               = $user_plike->row_array();
            $usr_name             = $getusr['name'];
            $msg                  = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
            }
            //end
            ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ', Your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
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
            /////////*******************************************excotel text message vendor ***************************************************
            $vendor_info  = $this->db->select('phone,name,token,token_status,agent,web_token')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
            $vendor_phone = $vendor_info->phone;
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ', Your lab package has been booked by patient ' . $user_name .' successfully and Booking Id is ' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $vendor_phone,
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
            
            // For lab center vendor website firebase push notification by ghanshyam parihar starts :: Added date: 05 Feb 2019
                $web_token      = $vendor_info->web_token;
                $title_web      = $user_name.' has booked an package from '.$vendor_name;
                $img_url        = $userimage;
                // $img_url      = 'https://medicalwale.com/img/medical_logo.png';
                $tag            = 'text';
                $agent          = $vendor_info->agent;;
                $connect_type   = 'lab_booking';
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019
                $click_action = 'https://labs.medicalwale.com/booking_controller/booking_appointment/'.$listing_id;
                // $click_action = 'https://vendor.sandbox.medicalwale.com/labs/booking_controller/booking_appointment/'.$listing_id;
                $this->send_gcm_web_notify($title_web, $message, $web_token, $img_url, $tag, $agent, $connect_type,$click_action);
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019 Ends
            // For lab center vendor website firebase push notification by ghanshyam parihar ends :: Added date: 05 Feb 2019
            
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id
                );
            }
        } else {
            $lab_booking = array(
                'user_id' => $user_id,
                'patient_id' => $patient_id,
                'booking_id' => $booking_id,
                'package_id' => $package_id,
                'listing_id' => $listing_id,
                'user_name' => $user_name,
                'user_email' => $email,
                'user_mobile' => $mobile,
                'user_gender' => $gender,
                'branch_id' => $branch_id,
                'vendor_id' => $vendor_id,
                'booking_date' => $created_at,
                'status' => $status,
                'trail_booking_date' => $trail_booking_date,
                'trail_booking_time' => $trail_booking_time,
                'payment_mode' => $payment_mode,
                'joining_date' => $joining_date,
                'booking_location' => $booking_location,
                'booking_address' => $booking_address,
                'booking_mobile' => $booking_mobile
            );
            $rst         = $this->db->insert('booking_master', $lab_booking);
            $appointment_id = $this->db->insert_id();
            if($package_id == ''){
                if ($test_ids != '') {
                    $Testids = explode(',', $test_ids);
                    foreach ($Testids as $tid) {
                        $test_data = array(
                            'booking_id' => $booking_id,
                            'test_id' => $tid
                        );
                        $rst       = $this->db->insert('booking_test', $test_data);
                    }
                }
            }
            
            $insertTolabBookingDetails = array(
                'user_id'=> $user_id,
                'patient_id'=> $patient_id,
                'listing_id'=> $listing_id,
                'vendor_type'=> $vendor_id,
                'branch_id'=> $branch_id,
                'branch_name'=> $branch_name,
                'at_home'=> $at_home,
                'address_line1'=> $address_line1,
                'address_line2'=> $address_line2,
                'city'=> $city,
                'state'=> $state,
                'pincode'=> $pincode,
                'mobile_no'=>$mobile ,
                'email_id'=> $email,
                'address_id'=> $address_id,
                'test_id'=> $test_ids,
                'package_id'=> $package_id,
                'booking_date'=> $trail_booking_date,
                'booking_time'=> $trail_booking_time,
                'booking_id'=> $booking_id 
                );
        
            $rst_comp         = $this->db->insert('lab_booking_details', $insertTolabBookingDetails);  
            $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
            //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file      = $profile_query->source;
                $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr               = $user_plike->row_array();
            $usr_name             = $getusr['name'];
            $msg                  = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
            }
            //end
            ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
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
            /////////*******************************************excotel text message vendor ***************************************************
            $vendor_info  = $this->db->select('phone,name,token,token_status,agent,web_token')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
            $vendor_phone = $vendor_info->phone;
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ', Your lab package has been booked by patient ' . $user_name . ' successfully and Booking Id is ' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $vendor_phone,
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
            
            
            // For lab center vendor website firebase push notification by ghanshyam parihar starts date: 05 Feb 2019
                $web_token      = $vendor_info->web_token;
                $title_web      = $user_name.' has booked an package from '.$vendor_name;
                $img_url        = $userimage;
                // $img_url      = 'https://medicalwale.com/img/medical_logo.png';
                $tag            = 'text';
                $agent          = $vendor_info->agent;;
                $connect_type   = 'lab_booking';
                
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019
                $click_action = 'https://labs.medicalwale.com/booking_controller/booking_appointment/'.$listing_id;
                // $click_action = 'https://vendor.sandbox.medicalwale.com/labs/booking_controller/booking_appointment/'.$listing_id;
                $this->send_gcm_web_notify($title_web, $message, $web_token, $img_url, $tag, $agent, $connect_type,$click_action);
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019 Ends
            
            // For lab center vendor website firebase push notification by ghanshyam parihar ends date: 05 Feb 2019
            
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id
                );
            }
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
                           'created_at'    => curr_date(),
                           'updated_at'    => curr_date()
                           
                   );
       //print_r($data);
       $this->db->insert("notifications", $data);        if($this->db->affected_rows() > 0)
       {
           
           return true; // to the controller
       }
       else{
           return false;
       }
   }  
    
    
    
    
    
     public function tyrocare_lab_booking($vendor_type, $user_id, $address, $amount, $report_code, $pincode, $bencount, $mobile, $email, $order_by, $service_type, $hc, $ref_code, $reports, $bendataxml, $booking_date, $booking_time, $payment_method, $product, $booking_id, $status,$reference_id,$leadId,$passon){
    
        date_default_timezone_set('Asia/Kolkata');
        $created_at   = date('Y-m-d H:i:s');
       /* echo"SELECT * FROM booking_master WHERE booking_id = '$booking_id'";*/
        $query        = $this->db->query("SELECT * FROM booking_master WHERE booking_id = '$booking_id'");
        $count        = $query->num_rows();
        if ($count > 0) {
            $lab_booking = array(
                'user_id' => $user_id,
                'booking_id' => $booking_id,
                'user_name' => $order_by,
                'user_email' => $email,
                'user_mobile' => $mobile,
                'booking_date' => $created_at,
                'status' => $status,
                'vendor_id' =>$vendor_type,
                'payment_mode' => $payment_method,
                'joining_date' => $booking_date
                
            );
            $this->db->where('booking_id', $booking_id);
            $rst = $this->db->update('booking_master', $lab_booking);
            
            $insertTolabBookingDetails = array(
                'user_id'=> $user_id,
                'amount'=> $amount,
                'report_code'=> $report_code,
                'becount'=> $bencount,
                'hc'=> $hc,
                'ref_code'=> $ref_code,
                'reports'=> $reports,
                'bendataxml'=> $bendataxml,
                'product'=> $product,
                'vendor_type'=> $vendor_type,
                'address_line1'=> $address,
                'address_line2'=>$address ,
                'pincode'=> $pincode,
                'mobile_no'=> $mobile,
                'email_id'=> $email,
                'booking_date'=> $booking_date,
                'booking_time'=> $booking_time,
                'booking_id'=> $booking_id,
                'reference_id' => $reference_id,
                'lead_id' => $leadId,
                'passon'=>$passon
                );
            
            $this->db->where('booking_id', $booking_id);
            $rst_comp         = $this->db->update('lab_booking_details', $insertTolabBookingDetails);    
            
            //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
            
            if($reference_id != '')
          {
            
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file      = $profile_query->source;
                $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr               = $user_plike->row_array();
            $usr_name             = $getusr['name'];
            $msg                  = $order_by . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $order_by . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
            }
            //end
            ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
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
            
          }
            /////////*******************************************excotel text message vendor ***************************************************
           /* $vendor_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
            $vendor_phone = $vendor_info->phone;
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ',your  lab package has been booked by patient :' . $user_id . 'successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $vendor_phone,
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
            curl_close($ch);*/
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id' => $booking_id
                );
            }
        } else {
            $lab_booking = array(
                'user_id' => $user_id,
                'booking_id' => $booking_id,
                'user_name' => $order_by,
                'user_email' => $email,
                'user_mobile' => $mobile,
                'booking_date' => $created_at,
                'status' => $status,
                'vendor_id' =>$vendor_type,
                'payment_mode' => $payment_method,
                'joining_date' => $booking_date
            );
            $rst         = $this->db->insert('booking_master', $lab_booking);
            
           
            
            $insertTolabBookingDetails = array(
                'user_id'=> $user_id,
                'amount'=> $amount,
                'report_code'=> $report_code,
                'becount'=> $bencount,
                'hc'=> $hc,
                'ref_code'=> $ref_code,
                'reports'=> $reports,
                'bendataxml'=> $bendataxml,
                'product'=> $product,
                'vendor_type'=> $vendor_type,
                'address_line1'=> $address,
                'address_line2'=>$address ,
                'pincode'=> $pincode,
                'mobile_no'=> $mobile,
                'email_id'=> $email,
                'booking_date'=> $booking_date,
                'booking_time'=> $booking_time,
                'booking_id'=> $booking_id,
                'reference_id' => $reference_id,
                'lead_id' => $leadId,
                'passon'=>$passon
                );
        
            $rst_comp         = $this->db->insert('lab_booking_details', $insertTolabBookingDetails);    
            //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
          if($reference_id != '')
          {
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file      = $profile_query->source;
                $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr               = $user_plike->row_array();
            $usr_name             = $getusr['name'];
            $msg                  = $order_by . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $order_by . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
            }
            //end
            ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
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
          }
            /////////*******************************************excotel text message vendor ***************************************************
            /*$vendor_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
            $vendor_phone = $vendor_info->phone;
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ',your  lab package has been booked by patient :' . $user_id . 'successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $vendor_phone,
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
            curl_close($ch);*/
            
            
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id' => $booking_id
                );
            }
        }
    }
    
    
    public function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent){
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        if (!defined("GOOGLE_GCM_URL"))
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
                "notification_type" => 'thyro_lab_booking',
                "notification_date" => $date
            )
        );
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
    public function lab_test_list($user_id, $page){
        $limit = 5;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $test_in_home_list = array();
        $test_in_lab_list = array();
        $start      = ($page - 1) * $limit;
        $query      = $this->db->query("SELECT * FROM `lab_test_details` WHERE `user_id` = '$user_id'");
        $total_user = $query->num_rows();
        $qRows      = $query->row();
        if ($total_user > 0) {
            //echo "SELECT * FROM `tbl_invoice_attachment` WHERE `invoice_id` = '$qRows->id'";
            $query        = $this->db->query("SELECT * FROM `lab_test_details` WHERE `user_id` = '$user_id' order by id DESC limit $start,$limit");
            $num_count    = $query->num_rows();
            $qRows_attach = $query->result();
            if ($num_count > 0) {
                foreach ($qRows_attach as $test_row) {
                   
                    $user_id        = $test_row->user_id;
                        $test           = $test_row->test;
                        $test_id        = $test_row->test_id;
                        $price          = $test_row->price;
                        $offer          = $test_row->offer;
                        $executive_rate = $test_row->executive_rate;
                        $home_delivery  = $test_row->home_delivery;
                        if ($home_delivery == "0") {
                            $test_in_lab_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test_id,
                                'test' => $test,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "0"
                            );
                        } else {
                            $test_in_home_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test_id,
                                'test' => $test,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "1"
                            );
                        }
                }
            } else {
               $test_in_home_list = array();
               $test_in_lab_list = array();
            }
            $tests = array(
                'total' => $total_user,
                'home_test_done' => $test_in_home_list,
                'lab_test_done' => $test_in_lab_list
            );
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $tests
            );
        } else {
            return array(
                'status' => 201,
                'message' => 'No data found!'
            );
        }
    }
    
    public function lab_booked_list($user_id, $listing_id){
        
        $count_query = $this->db->query("SELECT * from lab_booking_details where user_id='$user_id' and listing_id='$listing_id'");
        $lab_booked       = $count_query->num_rows();
        if ($lab_booked > 0) {
            foreach ($count_query->result_array() as $Lbooked) {
                    
                
                 $Lab_Bkooed_list[] = array(
                    'user_id'=> $Lbooked['user_id'],
                    'patient_id'=> $Lbooked['patient_id'],
                    'listing_id'=> $Lbooked['listing_id'],
                    'vendor_type'=> $Lbooked['vendor_type'],
                    'branch_id'=> $Lbooked['branch_id'],
                    'branch_name'=> $Lbooked['branch_name'],
                    'at_home'=> $Lbooked['at_home'],
                    'address_line1'=> $Lbooked['address_line1'],
                    'address_line2'=> $Lbooked['address_line2'],
                    'city'=> $Lbooked['city'],
                    'state'=> $Lbooked['state'],
                    'pincode'=> $Lbooked['pincode'],
                    'mobile_no'=> $Lbooked['mobile_no'],
                    'email_id'=> $Lbooked['email_id'],
                    'address_id'=> $Lbooked['address_id'],
                    'test_id'=> $Lbooked['test_id'],
                    'package_id'=> $Lbooked['package_id'],
                    'booking_date'=> $Lbooked['booking_date'],
                    'booking_time'=> $Lbooked['booking_time'],
                    'booking_id'=> $Lbooked['booking_id'] 
                );
            }
            return $Lab_Bkooed_list;
        }else{
            return $Lab_Bkooed_list= array();
        }
        
    }
    
    public function tyrocare_booked_list($vendor_type,$userid){
        
        $count_query = $this->db->query("SELECT * from lab_booking_details where vendor_type='$vendor_type' and user_id='$userid' and reference_id!=''");
                $lab_booked       = $count_query->num_rows();
        if ($lab_booked > 0) {
            foreach ($count_query->result_array() as $Lbooked) {
                    
                $uid =$Lbooked['user_id'];    
            $user_query = $this->db->query("SELECT email,phone,name FROM users WHERE id='$uid'");
                $email = $user_query->row()->email;
                $phone = $user_query->row()->phone;
                $user_name = $user_query->row()->name;        
                    
                $bk_id = $Lbooked['booking_id'];   
                /*echo"SELECT status FROM booking_master WHERE booking_id='$bk_id'";*/
            $book_query = $this->db->query("SELECT status FROM booking_master WHERE booking_id='$bk_id'");
            $status = $book_query->row()->status;
            
            
            //NAWAZ
            //echo "SELECT report_path FROM reports WHERE booking_id='$bk_id'";
            $report_path ='';
            $book_query_path = $this->db->query("SELECT report_path FROM reports WHERE booking_id='$bk_id'");
            $report_path_count =  $book_query_path->num_rows();;
            if($report_path_count > 0){
                $report_path = $book_query_path->row()->report_path;
            }
            
                      
                    
                $Lab_Bkooed_list[] = array(
                'user_id'=> $Lbooked['user_id'], 
                'user_name'=>$user_name,
                'amount'=> $Lbooked['amount'],
                'report_code'=> $Lbooked['report_code'],
                'becount'=> $Lbooked['becount'],
                'hc'=> $Lbooked['hc'],
                'ref_code'=> $Lbooked['ref_code'],
                'reports'=> $Lbooked['reports'],
                'bendataxml'=> $Lbooked['bendataxml'],
                'product'=> $Lbooked['product'],
                'vendor_type'=> $Lbooked['vendor_type'],
                'address_line1'=> $Lbooked['address_line1'],
                'pincode'=> $Lbooked['pincode'],
                'mobile_no'=> $Lbooked['mobile_no'],
                'email_id'=> $Lbooked['email_id'],
                'booking_date'=> $Lbooked['booking_date'],
                'booking_time'=> $Lbooked['booking_time'],
                'booking_id'=> $Lbooked['booking_id'],
                'reference_id'=> $Lbooked['reference_id'],
                'lead_id'=> $Lbooked['lead_id'],
                'status'=>$status,
                'report_path'=>$report_path
                );
            }
            return $Lab_Bkooed_list;
        }else{
            return $Lab_Bkooed_list= array();
        }
        
    }
    
    
    //to display instruction for perticular test
     public function lab_instruction_details($user_id,$id){
         $query = $this->db->query("SELECT * FROM labs_instruction WHERE id='$id'");
         $query_details = $query->row_array();
          $count = $query->num_rows();
          if($count>0)
          {
                $sample_type = $query_details['sample_type'];
                $test_name   = $query_details['test_name'];
                $instruction = $query_details['instruction'];
                
                $instruction_data = array(
                      'sample_type' => $sample_type,
                      'test_name' => $test_name,
                      'instruction' => $instruction
                    );
                
                return array(
                'status' => 201,
                'message' => 'success',
                'data' => $instruction_data
            );
          }     
          else
          {
               return array(
                'status' => 201,
                'message' => 'success',
                'data' => array()
            );
          }
     }
     
     
       
    //Added by Swapnali 
    public function lab_tests($user_id){
        $result = $this->db->query("SELECT * FROM `lab_all_test`")->result_array();
          return $result;
    }
    
    // lab_vendor_by_test
    public function lab_vendor_by_test1($user_id,$test_id){
        //SELECT * FROM `lab_test_details` LIMIT 10 OFFSET 10

        $testInfo = $this->db->query("SELECT * FROM `lab_all_test` WHERE `id` = '$test_id'")->row_array();
        $testName = $testInfo['test'];
        $results = $this->db->query("SELECT * FROM `lab_test_details` WHERE `test_id` = '$test_id' limit 20")->result_array();
        foreach($results as $r){
            $vendorId = $r['user_id'];
            $vendorInfo = $this->db->query("SELECT * FROM `lab_center` WHERE `is_active` = 1 AND `user_id` = '$vendorId'")->row_array();
            if(!empty($vendorInfo)){
                $r['vendor_info'] = $vendorInfo;
            } else {
                $r['vendor_info'] = (object)[];
            }
            
            $res[] = $r;
        }
        $test['test_name'] = $testName;
        $test['test_available'] = $res;
        
        // print_r($res);
        // die();
        return $test;
    }
    
      public function lab_vendor_by_test($user_id,$test_id,$per_page,$page_no){
        //SELECT * FROM `lab_test_details` LIMIT 10 OFFSET 10
       
            // $image1             = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
        if($per_page == 0 || $page_no == 0){
            $testInfo = $this->db->query("SELECT * FROM `lab_all_test` WHERE `id` = '$test_id'")->row_array();
            $testName = $testInfo['test'];
            // $results = $this->db->query("SELECT * FROM `lab_test_details` WHERE `test_id` = '$test_id' limit 20")->result_array();
            $results = $this->db->query("SELECT lt.* FROM `lab_test_details` as lt join lab_center as lc on (lt.user_id = lc.user_id ) WHERE lc.`is_active` = 1 AND `test_id` = '$test_id'")->result_array();
            
            foreach($results as $r){
                $vendorId = $r['user_id'];
                $vendorInfo = $this->db->query("SELECT * FROM `lab_center` WHERE `is_active` = 1 AND `user_id` = '$vendorId'")->row_array();
                if(!empty($vendorInfo)){
                    foreach($vendorInfo as $key => $value){
                       if($key == 'profile_pic' ){
                            $vendorInfo[$key] = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$vendorInfo['profile_pic'] ;
                        }
                    }
                    
                    $r['vendor_info'] = $vendorInfo;
                } else {
                    $r['vendor_info'] = (object)[];
                }
                
                $res[] = $r;
            }
            $test['test_name'] = $testName;
            $test['test_available'] = $res;
        } else {
            $offset = $per_page*($page_no - 1);
            
            $testInfo = $this->db->query("SELECT * FROM `lab_all_test` WHERE `id` = '$test_id'")->row_array();
            $testName = $testInfo['test'];
            // SELECT lt.* FROM `lab_test_details` as lt join lab_center as lc on (lt.user_id = lc.user_id ) WHERE `test_id` = '1' 
            // SELECT count(lt.id) FROM `lab_test_details` as lt join lab_center as lc on (lt.user_id = lc.user_id ) WHERE `test_id` = '1'

            //$results = $this->db->query("SELECT * FROM `lab_test_details` WHERE `test_id` = '$test_id' LIMIT $per_page OFFSET $offset")->result_array();
            // $totalRowCount = $this->db->query("SELECT COUNT(`id`) as count FROM `lab_test_details` WHERE `test_id` = '$test_id'")->row_array();
            
            $results = $this->db->query("SELECT lt.* FROM `lab_test_details` as lt join lab_center as lc on (lt.user_id = lc.user_id ) WHERE lc.`is_active` = 1 AND `test_id` = '$test_id' LIMIT $per_page OFFSET $offset")->result_array();
            $totalRowCount = $this->db->query("SELECT count(lt.id) as count FROM `lab_test_details` as lt join lab_center as lc on (lt.user_id = lc.user_id ) WHERE lc.`is_active` = 1 AND `test_id` = '$test_id'")->row_array();
            
            
            
            $totalData = $totalRowCount['count'];
            $last_page = ceil($totalData/$per_page);
            foreach($results as $r){
                $vendorId = $r['user_id'];
                $vendorInfo = $this->db->query("SELECT * FROM `lab_center` WHERE  `is_active` = 1 AND `user_id` = '$vendorId'")->row_array();
                if(!empty($vendorInfo)){
                     foreach($vendorInfo as $key => $value){
                       if($key == 'profile_pic' ){
                            $vendorInfo[$key] = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$vendorInfo['profile_pic'] ;
                        }
                    }
                    $r['vendor_info'] = $vendorInfo;
                } else {
                    $r['vendor_info'] = (object)[];
                }
                
                $res[] = $r;
            }
            $test['test_name'] = $testName;
            $test['data_count'] = $totalData;
            $test['per_page'] = $per_page;
            $test['current_page'] = $page_no;
            $test['first_page'] = 1;
            $test['last_page'] = $last_page;
            $test['test_available'] = $res;
        }
       
        return $test;
    }
    
    public function lab_test_by_vendor($vendor_id){
        $data = array();
        $data =  $this->db->query("SELECT * FROM `lab_test_details` WHERE `user_id` = '$vendor_id'")->result_array();
     
        return $data;
    }
    
    public function update_email($user_id,$email){
       $up=$this->db->query("UPDATE users SET email='$email' WHERE id='$user_id'");
       $data=  array(
                'status' => 201,
                'message' => 'success');
        return $data;
    }
    //end  
}




