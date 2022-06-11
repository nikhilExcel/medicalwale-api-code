<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dental_clinic_model extends CI_Model {

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
    //------------------------------------------new------------------------------------
    public function featured_dental_clinic_list($user_id,$lat,$lng)
    {
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
        
        $sql = sprintf("SELECT *,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_clinic_list  WHERE is_active='1' AND featured='1'  HAVING distance < '5' ORDER BY distance", ($lat), ($lng), ($lat), ($radius));
        $query = $this->db->query($sql);
        $count = $query->num_rows();
          
        if($count>0)
        {
            foreach($query->result_array() as $row)
            {
                   $listing_id              = $row['user_id'];
                   $name_of_hospital        = $row['name_of_hospital'];
                   $about_us                = $row['about_us'];
                   $establishment_year      = $row['establishment_year'];
                   $speciality              = $row['speciality'];
                   $category                = $row['category'];
                   $surgery                 = $row['surgery'];
                   $services                = $row['services'];
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
                   $rating                  = $row['rating'];
                   $review                  = $row['review'];
                   $user_discount           = $row['medicalwale_discount'];
                   $discount_description    = $row['description'];
                   $opening_hours           = $row['opening_hours'];
                   $mba                     = $row['mba'];
                   $certified               = $row['certified'];
                   $recommended             = $row['recommended'];
                   $featured                = $row['featured'];
                   $free_consultancy        = $row['free_consultancy'];
                   
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
                
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
                
                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                
                  //Review Count
                $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$listing_id");
                $Review_count = $Review_query->num_rows();
                
                
                //Profile View
                //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$listing_id and vendor_type='8'");
                $Profile_count = $Profile_query->num_rows();
                
                
                    if($user_discount == null || $user_discount == '' || $user_discount == NULL)
                    {
                        $user_discount = '0';
                    }
                   
                   if($rating == null || $rating == '' || $rating == NULL)
                    {
                        $rating = '4';
                    }
                    
                    
                    
                      $speciallity_array = array();
                      $speciallity_data = explode(',', $speciality);
                        foreach ($speciallity_data as $speciallity_data) {
                    
                    if($speciallity_data != ""){
                        if(preg_match('/[0-9]/', $speciallity_data)){
                            $Query = $this->db->query("SELECT name FROM dentists_specialist WHERE id='$speciallity_data'");
                              $count = $Query->num_rows();
                            if($count>0)
                            {
                            $what_we_offer_value = $Query->row()->name;
                            $speciallity_array[] = $what_we_offer_value;
                            }
                            else
                            {
                                $speciallity_array[] = $speciallity_data;
                            }
                        }else{
                            $speciallity_array[] = $speciallity_data;
                        }
                        
                    }
                    
                }
                
               //-----------------------service-------------------------
                $exp1=explode(',',$services);
                $treatments = array();
                $sub_category_details = array();
                $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE branch_id='$listing_id' group by category_id");
                $ser = $Querys->result();
                if(!empty($ser))
                {
                    foreach ($ser as $ser1) {
                        $sub_category_details = array();
                        $s_id = $ser1->id;
                        $category_id = $ser1->category_id;
                        
                        $Query = $this->db->query("SELECT * FROM dentist_services_master WHERE id='$category_id' ");
                        $main_service = $Query->row()->name;
                      
                        $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE category_id='$category_id' AND branch_id='$listing_id'");
                        $m_service = $Querys->result();    
                            if(!empty($m_service))
                            {
                                foreach ($m_service as $mser1) {
                                    $ids = $mser1->id;
                                    $service_name = $mser1->service_name;
                                    $price = $mser1->price;
                                    $discount = $mser1->discount;
                                    
                                        if(in_array($ids,$exp1))
                                        {
                                                $sub_category_details[] = array(
                                                                      'id'         => $ids,
                                                                      'service_name' => $service_name,
                                                                      'price' => $price,
                                                                      'discount' => $discount
                                                                      );
                                        }
                                    
                                }
                                
                            }
                            
                            $treatments[] = array(
                                   
                                   'cat_name' => $main_service,
                                   'sub_catdata' => $sub_category_details
                            
                            );
                          
                    }
                }
                //print_r($treatments);
                
                //-----------------------packages-------------------------
                $packages = array();
                $sql_p = "SELECT * FROM packages WHERE user_id='$listing_id'";
                $query_p = $this->db->query($sql_p);
                $count_p = $query_p->num_rows();
                  
                if($count_p>0)
                {
                    foreach($query_p->result_array() as $row)
                    {
                         
                         $pid                   =  $row['id'];
                           $image                   =  $row['image'];
                           $package_name            = $row['package_name'];
                            $package_details        = $row['package_details'];
                            $price                  = $row['price'];
                            $discount               = $row['discount'];
                      
                        
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $image = '';
                        }
                        
            
               
                   
                $packages[] = array(
                       'id'                     => $pid,
                       'package_name'           => $package_name,
                        'package_details'       => $package_details,
                        'price'                 => $price,
                        'discount'              => $discount,
                        'image'                 => $image
                       );
               }
           }
          
             $fav_pharmacy = $this->db->select('id')->from('dentist_favourite')->where('user_id', $user_id)->where('listing_id', $listing_id)->get()->num_rows();
             
                $result_data[] = array(
                       'listing_id'             => $listing_id,
                       'listing_type'           => '39',
                       'name_of_clinic'         => $name_of_hospital,
                       'about_us'               => $about_us,
                       'establishment_year'     => $establishment_year,
                       'speciality'             => $speciallity_array,
                       'services'               => $services,
                       'packages'               => $packages,
                       'treatments'             => $treatments,
                       'address'                => $address,
                       'address_2'              => $address_2,
                       'pincode'                => $pincode,
                       'phone'                  => $phone,
                       'city'                   => $city,
                       'state'                  => $state,
                       'email'                  => $email,
                       'lat'                    => $lat,
                       'lng'                    => $lng,
                       'image'                  => $image,
                       'opening_day'            => $final_Day,
                       'rating'                 => $rating,
                       'review'                 => '0',
                       'followers'              => $followers,
                       'following'              => $following,
                        'profile_views'         => $Profile_count,
                        'is_follow'             => $is_follow,
                       'user_discount'          => $user_discount,
                       'discount_description'   => $discount_description,
                       'favourite'              => $fav_pharmacy,
                       'mba'                    => $mba,
                       'certified'              => $certified,
                       'recommended'            => $recommended,
                       'featured'               => $featured,
                       'free_consultancy'       => $free_consultancy
                       );
               }
           }
           else
           {
               return $result_data = array();
           }
           
           return $result_data;
     }
     
    public function special_packages_list($user_id,$lat,$lng,$page,$sort)
    {
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
        
        if($page==""){
         $limit = " LIMIT 0, 10";   
        }
        else
        {
            $limit = 10;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $limit = " LIMIT $start, $limit";   
        }
        
         $wh=" ORDER BY distance";
                if($sort=="low")
                {
                    $wh ="order by p.price asc";
                }
                elseif($sort=="high")
                {
                    $wh ="order by p.price desc";
                }
                
        $sql = sprintf("SELECT d.*,p.id as package_id,p.package_name,p.package_details,p.price,p.discount,( 6371 * acos( cos( radians('%s') ) * cos( radians( d.lat ) ) * cos( radians( d.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( d.lat ) ) ) ) AS distance FROM dentists_clinic_list as d  INNER JOIN packages as p ON (d.user_id=p.user_id AND p.v_id='39') WHERE d.is_active='1'  HAVING distance < '5'  $wh $limit", ($lat), ($lng), ($lat), ($radius));
        $query = $this->db->query($sql);
        $count = $query->num_rows();
          
        if($count>0)
        {
            foreach($query->result_array() as $row)
            {
                   $listing_id              = $row['user_id'];
                   $name_of_hospital        = $row['name_of_hospital'];
                   $about_us                = $row['about_us'];
                   $establishment_year      = $row['establishment_year'];
                  /* $speciality              = $row['speciality'];
                   $category                = $row['category'];
                   $surgery                 = $row['surgery'];
                   $services                = $row['services'];*/
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
                  // $rating                  = $row['rating'];
                  // $review                  = $row['review'];
                  // $user_discount           = $row['medicalwale_discount'];
                   //$discount_description    = $row['description'];
                  // $opening_hours     = $row['opening_hours'];
                  $package_id = $row['package_id'];
                   $package_name = $row['package_name'];
                    $package_details = $row['package_details'];
                    $price = $row['price'];
                    $discount = $row['discount'];
              
                
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
                
            
               
                   
                $result_data[] = array(
                       'listing_id'             => $listing_id,
                       'listing_type'           => '39',
                       'name_of_clinic'         => $name_of_hospital,
                       'about_us'               => $about_us,
                       'establishment_year'     => $establishment_year,
                       'address'                => $address,
                       'address_2'              => $address_2,
                       'pincode'                => $pincode,
                       'phone'                  => $phone,
                       'city'                   => $city,
                       'state'                  => $state,
                       'email'                  => $email,
                       'lat'                    => $lat,
                       'lng'                    => $lng,
                       'image'                  => $image,
                       'package_id'             => $package_id,
                       'package_name'           => $package_name,
                        'package_details'       => $package_details,
                        'price'                 => $price,
                        'discount'              => $discount
                       );
               }
           }
           else
           {
                $result_data = array();
           }
        
       
        $result_data1 = array();        
        $sql = sprintf("SELECT d.*,p.id as package_id,p.package_name,p.package_details,p.price,p.discount,( 6371 * acos( cos( radians('%s') ) * cos( radians( d.lat ) ) * cos( radians( d.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( d.lat ) ) ) ) AS distance FROM dentists_branch as d  INNER JOIN packages as p ON (d.dentists_branch_user_id=p.user_id AND p.v_id='39') WHERE d.is_active='1'  HAVING distance < '5'  $wh $limit", ($lat), ($lng), ($lat), ($radius));
        $query = $this->db->query($sql);
        $count = $query->num_rows();
          
        if($count>0)
        {
            foreach($query->result_array() as $row)
            {
                   $listing_id              = $row['dentists_branch_user_id'];
                   $name_of_hospital        = $row['name_of_hospital'];
                   $about_us                = $row['about_us'];
                   $establishment_year      = $row['establishment_year'];
                 
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
                 
                  $package_id = $row['package_id'];
                   $package_name = $row['package_name'];
                    $package_details = $row['package_details'];
                    $price = $row['price'];
                    $discount = $row['discount'];
              
                
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
                
                $result_data1[] = array(
                       'listing_id'             => $listing_id,
                       'listing_type'           => '39',
                       'name_of_clinic'         => $name_of_hospital,
                       'about_us'               => $about_us,
                       'establishment_year'     => $establishment_year,
                       'address'                => $address,
                       'address_2'              => $address_2,
                       'pincode'                => $pincode,
                       'phone'                  => $phone,
                       'city'                   => $city,
                       'state'                  => $state,
                       'email'                  => $email,
                       'lat'                    => $lat,
                       'lng'                    => $lng,
                       'image'                  => $image,
                       'package_id'             => $package_id,
                       'package_name'           => $package_name,
                        'package_details'       => $package_details,
                        'price'                 => $price,
                        'discount'              => $discount
                       );
               }
           }
           else
           {
                $result_data1 = array();
           }   
           
           $data = array_merge($result_data,$result_data1);
        
        if($sort == "low")
        {
                function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
                    $sort_col = array();
                    foreach ($arr as $key => $row) {
                        $sort_col[$key] = $row[$col];
                    }
                    array_multisort($sort_col, $dir, $arr);
                }
        }
        else
        {
                function array_sort_by_column(&$arr, $col, $dir = SORT_DESC) {
                    $sort_col = array();
                    foreach ($arr as $key => $row) {
                        $sort_col[$key] = $row[$col];
                    }
                    array_multisort($sort_col, $dir, $arr);
                }
        }


        array_sort_by_column($data, 'price');
           return $data;
    } 
    
    public function recommended_treatments_list($user_id,$lat,$lng,$page,$sort)
    {
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
        
        if($page==""){
         $limit = " LIMIT 0, 10";   
        }
        else
        {
            $limit = 10;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $limit = " LIMIT $start, $limit";   
        }
        
         $wh=" ORDER BY distance";
                if($sort=="low")
                {
                    $wh ="order by p.price asc";
                }
                elseif($sort=="high")
                {
                    $wh ="order by p.price desc";
                }
                
        $sql = sprintf("SELECT d.*,p.id as package_id,p.category_id,p.service_name,p.description,p.price,p.discount,( 6371 * acos( cos( radians('%s') ) * cos( radians( d.lat ) ) * cos( radians( d.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( d.lat ) ) ) ) AS distance FROM dentists_clinic_list as d  INNER JOIN dental_services_offered as p ON d.user_id=p.branch_id WHERE d.is_active='1'  HAVING distance < '5'  $wh $limit", ($lat), ($lng), ($lat), ($radius));
        $query = $this->db->query($sql);
        $count = $query->num_rows();
          
        if($count>0)
        {
            foreach($query->result_array() as $row)
            {
                   $listing_id              = $row['user_id'];
                   $name_of_hospital        = $row['name_of_hospital'];
                   $about_us                = $row['about_us'];
                   $establishment_year      = $row['establishment_year'];
                  /* $speciality              = $row['speciality'];
                   $category                = $row['category'];
                   $surgery                 = $row['surgery'];
                   $services                = $row['services'];*/
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
                  // $rating                  = $row['rating'];
                  // $review                  = $row['review'];
                  // $user_discount           = $row['medicalwale_discount'];
                   //$discount_description    = $row['description'];
                  // $opening_hours     = $row['opening_hours'];
                   $package_id              = $row['package_id'];
                   $package_name = $row['service_name'];
                    $package_details = $row['description'];
                    $price = $row['price'];
                    $discount = $row['discount'];
                    $category_id = $row['category_id'];
                 $sql2 = "SELECT * FROM dentist_services_master where id='$category_id'";
                $query2 = $this->db->query($sql2);
                $count2 = $query2->num_rows();
                  
                if($count2>0)
                {
                    $row2 = $query2->row_array();
                    $s_name = $row2['name'];
                }
                else
                {
                    $s_name = "";
                }
                
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
                
            
               
                   
                $result_data[] = array(
                       'listing_id'             => $listing_id,
                       'listing_type'           => '39',
                       'name_of_clinic'         => $name_of_hospital,
                       'about_us'               => $about_us,
                       'establishment_year'     => $establishment_year,
                       'address'                => $address,
                       'address_2'              => $address_2,
                       'pincode'                => $pincode,
                       'phone'                  => $phone,
                       'city'                   => $city,
                       'state'                  => $state,
                       'email'                  => $email,
                       'lat'                    => $lat,
                       'lng'                    => $lng,
                       'image'                  => $image,
                       'package_id'             => $package_id,
                       'package_name'           => $package_name,
                        'package_details'       => $package_details,
                        'price'                 => $price,
                        'discount'              => $discount,
                        'main_cat'              => $s_name
                       );
               }
           }
           else
           {
                $result_data = array();
           }
        
       
        $result_data1 = array();        
        $sql = sprintf("SELECT d.*,p.id as package_id,p.category_id,p.service_name,p.description,p.price,p.discount,( 6371 * acos( cos( radians('%s') ) * cos( radians( d.lat ) ) * cos( radians( d.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( d.lat ) ) ) ) AS distance FROM dentists_branch as d  INNER JOIN dental_services_offered as p ON d.dentists_branch_user_id=p.branch_id WHERE d.is_active='1'  HAVING distance < '5'  $wh $limit", ($lat), ($lng), ($lat), ($radius));
        $query = $this->db->query($sql);
        $count = $query->num_rows();
          
        if($count>0)
        {
            foreach($query->result_array() as $row)
            {
                   $listing_id              = $row['dentists_branch_user_id'];
                   $name_of_hospital        = $row['name_of_hospital'];
                   $about_us                = $row['about_us'];
                   $establishment_year      = $row['establishment_year'];
                 
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
                 
                  $package_id = $row['package_id'];
                   $package_name = $row['service_name'];
                    $package_details = $row['description'];
                    $price = $row['price'];
                    $discount = $row['discount'];
               $category_id = $row['category_id'];
                 $sql2 = "SELECT * FROM dentist_services_master where id='$category_id'";
                $query2 = $this->db->query($sql2);
                $count2 = $query2->num_rows();
                  
                if($count2>0)
                {
                    $row2 = $query2->row_array();
                    $s_name = $row2['name'];
                }
                else
                {
                    $s_name = "";
                }
                
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
                
                $result_data1[] = array(
                       'listing_id'             => $listing_id,
                       'listing_type'           => '39',
                       'name_of_clinic'         => $name_of_hospital,
                       'about_us'               => $about_us,
                       'establishment_year'     => $establishment_year,
                       'address'                => $address,
                       'address_2'              => $address_2,
                       'pincode'                => $pincode,
                       'phone'                  => $phone,
                       'city'                   => $city,
                       'state'                  => $state,
                       'email'                  => $email,
                       'lat'                    => $lat,
                       'lng'                    => $lng,
                       'image'                  => $image,
                       'package_id'             => $package_id,
                       'package_name'           => $package_name,
                        'package_details'       => $package_details,
                        'price'                 => $price,
                        'discount'              => $discount,
                        'main_cat'              => $s_name
                       );
               }
           }
           else
           {
                $result_data1 = array();
           }   
           
           $data = array_merge($result_data,$result_data1);
        
        if($sort == "low")
        {
                function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
                    $sort_col = array();
                    foreach ($arr as $key => $row) {
                        $sort_col[$key] = $row[$col];
                    }
                    array_multisort($sort_col, $dir, $arr);
                }
        }
        else
        {
                function array_sort_by_column(&$arr, $col, $dir = SORT_DESC) {
                    $sort_col = array();
                    foreach ($arr as $key => $row) {
                        $sort_col[$key] = $row[$col];
                    }
                    array_multisort($sort_col, $dir, $arr);
                }
        }


        array_sort_by_column($data, 'price');
           return $data;
    } 
    public function view_package($user_id,$listing_id,$package_id,$type)
    {
        $result_data = array();
        
        if($type == 1)
        {
            $sql1 = "SELECT * FROM packages where id='$package_id'";
            $query1 = $this->db->query($sql1);
            $count1 = $query1->num_rows();
              
            if($count1>0)
            {
                $row1 = $query1->row_array();
                $user_id = $row1['user_id'];
                $package_id = $row1['id'];
                $package_name = $row1['package_name'];
                $package_details = $row1['package_details'];
                $price = $row1['price'];
                $discount = $row1['discount'];
                $image = $row1['image'];
                  if ($image != '') {
                                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                                } else {
                                    $image = '';
                                }
                
                $packages[] = array( 'id'   => $package_id,
                                                           'package_name'           => $package_name,
                                                            'package_details'       => $package_details,
                                                            'price'                 => $price,
                                                            'discount'              => $discount,
                                                            'image'                =>$image
                                                            );
                                                            
                $sql = "SELECT d.* FROM dentists_clinic_list as d  WHERE d.user_id='$user_id'";
                $query = $this->db->query($sql);
                $count = $query->num_rows();
                  
                if($count>0)
                {
                           $row = $query->row_array(); 
                    
                           $listing_id              = $row['user_id'];
                           $name_of_hospital        = $row['name_of_hospital'];
                           $about_us                = $row['about_us'];
                           $establishment_year      = $row['establishment_year'];
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
                           $rating                  = "4.5";
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $image = '';
                        }
                        
                    
                        $result_data[] = array(
                               'listing_id'             => $listing_id,
                               'listing_type'           => '39',
                               'name_of_clinic'         => $name_of_hospital,
                               'about_us'               => $about_us,
                               'establishment_year'     => $establishment_year,
                               'address'                => $address,
                               'address_2'              => $address_2,
                               'pincode'                => $pincode,
                               'phone'                  => $phone,
                               'city'                   => $city,
                               'state'                  => $state,
                               'email'                  => $email,
                               'lat'                    => $lat,
                               'lng'                    => $lng,
                               'image'                  => $image,
                               'rating'                 => $rating,
                               'packages'               => $packages
                                
                               );
                       
                   }
                else
                {
                    $sql = "SELECT d.* FROM dentists_branch as d  WHERE d.dentists_branch_user_id='$user_id'";
                    $query = $this->db->query($sql);
                    $count = $query->num_rows();
                      
                    if($count>0)
                    {
                               $row = $query->row_array(); 
                        
                               $listing_id              = $row['dentists_branch_user_id'];
                               $name_of_hospital        = $row['name_of_hospital'];
                               $about_us                = $row['about_us'];
                               $establishment_year      = $row['establishment_year'];
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
                               $rating                  ="4.5";
                            if ($image != '') {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                            } else {
                                $image = '';
                            }
                            
                        
                            $result_data[] = array(
                                   'listing_id'             => $listing_id,
                                   'listing_type'           => '39',
                                   'name_of_clinic'         => $name_of_hospital,
                                   'about_us'               => $about_us,
                                   'establishment_year'     => $establishment_year,
                                   'address'                => $address,
                                   'address_2'              => $address_2,
                                   'pincode'                => $pincode,
                                   'phone'                  => $phone,
                                   'city'                   => $city,
                                   'state'                  => $state,
                                   'email'                  => $email,
                                   'lat'                    => $lat,
                                   'lng'                    => $lng,
                                   'image'                  => $image,
                                   'rating'                 => $rating,
                                   'packages'               => $packages
                                   );
                           
                       }
                }
                 
                }
        }
        if($type == 2)
        {
            $sql1 = "SELECT * FROM dental_services_offered where id='$package_id'";
            $query1 = $this->db->query($sql1);
            $count1 = $query1->num_rows();
              
            if($count1>0)
            {
                $row1 = $query1->row_array();
                $user_id = $row1['branch_id'];
                $category_id = $row1['category_id'];
                $package_id = $row1['id'];
                $package_name = $row1['service_name'];
                $package_details = $row1['description'];
                $price = $row1['price'];
                $discount = $row1['discount'];
                
                 $sql2 = "SELECT * FROM dentist_services_master where id='$category_id'";
                $query2 = $this->db->query($sql2);
                $count2 = $query2->num_rows();
                  
                if($count2>0)
                {
                    $row2 = $query2->row_array();
                    $s_name = $row2['name'];
                }
                else
                {
                    $s_name = "";
                }
                $sub_category_details[] = array( 'id'             => $package_id,
                                                           'service_name'           => $package_name,
                                                            'price'                 => $price,
                                                            'discount'              => $discount);
              
                $treatments[] = array(
                                   
                                   'cat_name' => $s_name,
                                   'sub_catdata' => $sub_category_details
                            
                            );
                                                                      
                $sql = "SELECT d.* FROM dentists_clinic_list as d  WHERE d.user_id='$user_id'";
                $query = $this->db->query($sql);
                $count = $query->num_rows();
                  
                if($count>0)
                {
                           $row = $query->row_array(); 
                    
                           $listing_id              = $row['user_id'];
                           $name_of_hospital        = $row['name_of_hospital'];
                           $about_us                = $row['about_us'];
                           $establishment_year      = $row['establishment_year'];
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
                           $rating                  = "4.5";
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $image = '';
                        }
                        
                    
                        $result_data[] = array(
                               'listing_id'             => $listing_id,
                               'listing_type'           => '39',
                               'name_of_clinic'         => $name_of_hospital,
                               'about_us'               => $about_us,
                               'establishment_year'     => $establishment_year,
                               'address'                => $address,
                               'address_2'              => $address_2,
                               'pincode'                => $pincode,
                               'phone'                  => $phone,
                               'city'                   => $city,
                               'state'                  => $state,
                               'email'                  => $email,
                               'lat'                    => $lat,
                               'lng'                    => $lng,
                               'image'                  => $image,
                               'rating'                 => $rating,
                               'treatments'             => $treatments
                               );
                       
                   }
                else
                {
                    $sql = "SELECT d.* FROM dentists_branch as d  WHERE d.dentists_branch_user_id='$user_id'";
                    $query = $this->db->query($sql);
                    $count = $query->num_rows();
                      
                    if($count>0)
                    {
                               $row = $query->row_array(); 
                        
                               $listing_id              = $row['dentists_branch_user_id'];
                               $name_of_hospital        = $row['name_of_hospital'];
                               $about_us                = $row['about_us'];
                               $establishment_year      = $row['establishment_year'];
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
                               $rating                  = "4.5";
                            if ($image != '') {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                            } else {
                                $image = '';
                            }
                            
                            $result_data[] = array(
                                   'listing_id'             => $listing_id,
                                   'listing_type'           => '39',
                                   'name_of_clinic'         => $name_of_hospital,
                                   'about_us'               => $about_us,
                                   'establishment_year'     => $establishment_year,
                                   'address'                => $address,
                                   'address_2'              => $address_2,
                                   'pincode'                => $pincode,
                                   'phone'                  => $phone,
                                   'city'                   => $city,
                                   'state'                  => $state,
                                   'email'                  => $email,
                                   'lat'                    => $lat,
                                   'lng'                    => $lng,
                                   'image'                  => $image,
                                   'rating'                 => $rating,
                                   'treatments'             => $treatments
                                   );
                           
                       }
                }
            }
        }
       
           return $result_data;
    } 
    
    public function dentist_branch_list($user_id,$listing_id,$lat,$lng,$page)
    {
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
        
        if($page==""){
         $limit = " LIMIT 0, 10";   
        }
        else
        {
            $limit = 10;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $limit = " LIMIT $start, $limit";   
        }
        $sql = "SELECT * FROM dentists_branch  WHERE hub_user_id='$listing_id' $limit";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
          
        if($count>0)
        {
            foreach($query->result_array() as $row)
            {
                   $ids                     = $row['id'];
                   $branch_id               = $row['dentists_branch_user_id'];
                   $name_of_hospital        = $row['name_of_hospital'];
                   $about_us                = $row['about_us'];
                   $establishment_year      = $row['establishment_year'];
                   $speciality              = $row['speciality'];
                   $category                = $row['category'];
                   $surgery                 = $row['surgery'];
                   $services                = $row['services'];
                   $address                 = $row['address'];
                   $address_2               = $row['address_2'];
                   $pincode                 = $row['pincode'];
                   $phone                   = $row['phone'];
                   $city                    = $row['city'];
                   $state                   = $row['state'];
                   $email                   = $row['email'];
                   $lat1                     = $row['lat'];
                   $lng1                     =  $row['lng'];
                   $image                   =  $row['image'];
                   $rating                  = $row['rating'];
                   $review                  = $row['review'];
                   $user_discount           = $row['medicalwale_discount'];
                   $discount_description    = $row['description'];
                   $opening_hours     = $row['opening_hours'];
                   
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
                
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
                
                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $branch_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $branch_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $branch_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                
                  //Review Count
                $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$branch_id");
                $Review_count = $Review_query->num_rows();
                
                
                //Profile View
                //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$branch_id and vendor_type='8'");
                $Profile_count = $Profile_query->num_rows();
                
                
                    if($user_discount == null || $user_discount == '' || $user_discount == NULL)
                    {
                        $user_discount = '0';
                    }
                   
                   if($rating == null || $rating == '' || $rating == NULL)
                    {
                        $rating = '4';
                    }
                    
                    
                    
                      $speciallity_array = array();
                      $speciallity_data = explode(',', $speciality);
                foreach ($speciallity_data as $speciallity_data) {
                    
                    if($speciallity_data != ""){
                        if(preg_match('/[0-9]/', $speciallity_data)){
                            $Query = $this->db->query("SELECT name FROM dentists_specialist WHERE id='$speciallity_data'");
                              $count = $Query->num_rows();
                            if($count>0)
                            {
                            $what_we_offer_value = $Query->row()->name;
                            $speciallity_array[] = $what_we_offer_value;
                            }
                            else
                            {
                                $speciallity_array[] = $speciallity_data;
                            }
                        }else{
                            $speciallity_array[] = $speciallity_data;
                        }
                        
                    }
                    
                }
                
                 //-----------------------service-------------------------
                $exp1=explode(',',$services);
                $treatments = array();
                $sub_category_details = array();
                $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE branch_id='$branch_id' group by category_id");
                $ser = $Querys->result();
                if(!empty($ser))
                {
                    foreach ($ser as $ser1) {
                        $sub_category_details = array();
                        $s_id = $ser1->id;
                        $category_id = $ser1->category_id;
                        
                        $Query = $this->db->query("SELECT * FROM dentist_services_master WHERE id='$category_id' ");
                        $main_service = $Query->row()->name;
                      
                        $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE category_id='$category_id' AND branch_id='$branch_id'");
                        $m_service = $Querys->result();    
                            if(!empty($m_service))
                            {
                                foreach ($m_service as $mser1) {
                                    $ids = $mser1->id;
                                    $service_name = $mser1->service_name;
                                    $price = $mser1->price;
                                    $discount = $mser1->discount;
                                    
                                        if(in_array($ids,$exp1))
                                        {
                                                $sub_category_details[] = array(
                                                    'id'         => $ids,
                                                                         'service_name' => $service_name,
                                                                      'price' => $price,
                                                                      'discount' => $discount
                                                                      );
                                        }
                                    
                                }
                                
                            }
                            
                            $treatments[] = array(
                                   
                                   'cat_name' => $main_service,
                                   'sub_catdata' => $sub_category_details
                            
                            );
                          
                    }
                }
                //print_r($treatments);
                
                //-----------------------packages-------------------------
                $packages = array();
                $sql_p = "SELECT * FROM packages WHERE user_id='$listing_id' AND FIND_IN_SET('" . $ids . "',branch)";
                $query_p = $this->db->query($sql_p);
                $count_p = $query_p->num_rows();
                  
                if($count_p>0)
                {
                    foreach($query_p->result_array() as $row)
                    {
                           $pid                   =  $row['id'];
                           $image                   =  $row['image'];
                           $package_name            = $row['package_name'];
                            $package_details        = $row['package_details'];
                            $price                  = $row['price'];
                            $discount               = $row['discount'];
                      
                        
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $image = '';
                        }
                        
            
               
                   
                $packages[] = array(
                       'id'                     => $pid,
                       'package_name'           => $package_name,
                        'package_details'       => $package_details,
                        'price'                 => $price,
                        'discount'              => $discount,
                        'image'                 => $image
                       );
               }
           }
           $fav_pharmacy = $this->db->select('id')->from('dentist_favourite')->where('user_id', $user_id)->where('listing_id', $branch_id)->get()->num_rows();
                            
                $distances = str_replace(',', '.', GetDrivingDistance($lat, $lat1, $lng, $lng1));
                
                $result_data[] = array(
                        'id'                    => $ids,
                       'listing_id'             => $branch_id,
                       'listing_type'           => '39',
                       'distance'               => $distances,
                       'name_of_clinic'         => $name_of_hospital,
                       'about_us'               => $about_us,
                       'establishment_year'     => $establishment_year,
                       'speciality'             => $speciallity_array,
                       'services'               => $services,
                       'packages'               => $packages,
                       'treatments'             => $treatments,
                       'address'                => $address,
                       'address_2'              => $address_2,
                       'pincode'                => $pincode,
                       'phone'                  => $phone,
                       'city'                   => $city,
                       'state'                  => $state,
                       'email'                  => $email,
                       'lat'                    => $lat,
                       'lng'                    => $lng,
                       'image'                  => $image,
                       'opening_day'            => $final_Day,
                       'rating'                 => $rating,
                       'review'                 => '0',
                       'followers'              => $followers,
                       'following'              => $following,
                        'profile_views'         => $Profile_count,
                        'is_follow'             => $is_follow,
                       'user_discount'          => $user_discount,
                       'discount_description'   => $discount_description,
                       'favourite'              => $fav_pharmacy,
                       'mba'                    => $mba,
                       'certified'              => $certified,
                       'recommended'            => $recommended,
                       'featured'               => $featured,
                       'free_consultancy'       => $free_consultancy
                       );
               }
           }
           else
           {
               return $result_data = array();
           }
           
             function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
                    $sort_col = array();
                    foreach ($arr as $key => $row) {
                        $sort_col[$key] = $row[$col];
                    }
                    array_multisort($sort_col, $dir, $arr);
                }
       

        array_sort_by_column($result_data, 'distance');
           return $result_data;
     }
     
    public function nearby_dental_clinic_list($user_id,$lat,$lng,$page,$listing_id1,$type,$keyword)
    {
       
               $radius = '5';
        $data = array();
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
        
         if($page==""){
         $limit = " LIMIT 0, 10";   
        }
        else
        {
            $limit = 10;
            $start = 0;
            if ($page > 0) {
                if (!is_numeric($page)) {
                    $page = 1;
                }
            }
            $start = ($page - 1) * $limit;
            $limit = " LIMIT $start, $limit";   
        }
        //---------------------------------------near by & free-consultancy ---------------------------------------
        if($type==1 || $type==3 || $type==4)
        {
            $wh="";
            if($type==3)
            {
                $wh = " free_consultancy='1' AND ";
            }
            elseif($type==4)
            {
                $wh = " mba='1' AND";
            }
            $where="";
            
            if($keyword!=""){
               $where=" name_of_hospital LIKE '%%$keyword%%' and";
               
            }else{
                $where =""; 
            }
            
            
                $result_data= array();
                $sql = sprintf("SELECT *,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_clinic_list  WHERE $wh $where is_active='1' HAVING distance < '5' ORDER BY distance  $limit", ($lat), ($lng), ($lat), ($radius));
                 
                 $query = $this->db->query($sql);
                $count = $query->num_rows();
                  
                if($count>0)
                {
                    foreach($query->result_array() as $row)
                    {
                          echo  $listing_id              = $row['user_id']; echo "<br>";
                           $name_of_hospital        = $row['name_of_hospital'];
                           $about_us                = $row['about_us'];
                           $establishment_year      = $row['establishment_year'];
                           $speciality              = $row['speciality'];
                           $category                = $row['category'];
                           $surgery                 = $row['surgery'];
                           $services                = $row['services'];
                           $address                 = $row['address'];
                           $address_2               = $row['address_2'];
                           $pincode                 = $row['pincode'];
                           $phone                   = $row['phone'];
                           $city                    = $row['city'];
                           $state                   = $row['state'];
                           $email                   = $row['email'];
                           $lat1                     = $row['lat'];
                           $lng1                     =  $row['lng'];
                           $image                   =  $row['image'];
                           $rating                  = $row['rating'];
                           $review                  = $row['review'];
                           $user_discount           = $row['medicalwale_discount'];
                           $discount_description    = $row['description'];
                           $opening_hours           = $row['opening_hours'];
                           $mba                     = $row['mba'];
                           $certified               = $row['certified'];
                           $recommended             = $row['recommended'];
                           $featured                = $row['featured'];
                           $free_consultancy        = $row['free_consultancy'];
                           $distance        = $row['distance'];
                           
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
					   // print_r($time_list1);
					    if(count($time_list1)>=2) {
						for ($l = 0; $l < count($time_list1) && $l < count($time_list2); $l++) {
						    $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
						    $time[]            = str_replace('close-close','', $time_check);
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
					    else
					    {
						    $open_close[] = 'close';
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
			   // print_r($final_Day);
                    foreach($final_Day as $key => $product)
                    {
                    
                    if($product['day']===$dfinal)
                    {
			    if(count($product['time'])>1)
			    {
                     		$current_day_final = $product['time'][0];
			    }
                    }
                    }    
                        
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $image = '';
                        }
                        
                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();
        
                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }
                        
                          //Review Count
                        $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$listing_id");
                        $Review_count = $Review_query->num_rows();
                        
                        
                        //Profile View
                        //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                        $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$listing_id and vendor_type='8'");
                        $Profile_count = $Profile_query->num_rows();
                        
                        
                            if($user_discount == null || $user_discount == '' || $user_discount == NULL)
                            {
                                $user_discount = '0';
                            }
                           
                           if($rating == null || $rating == '' || $rating == NULL)
                            {
                                $rating = '4';
                            }
                            
                            
                            
                              $speciallity_array = array();
                              $speciallity_data = explode(',', $speciality);
                                foreach ($speciallity_data as $speciallity_data) {
                            
                            if($speciallity_data != ""){
                                if(preg_match('/[0-9]/', $speciallity_data)){
                                    $Query = $this->db->query("SELECT name FROM dentists_specialist WHERE id='$speciallity_data'");
                                      $count = $Query->num_rows();
                                    if($count>0)
                                    {
                                    $what_we_offer_value = $Query->row()->name;
                                    $speciallity_array[] = $what_we_offer_value;
                                    }
                                    else
                                    {
                                        $speciallity_array[] = $speciallity_data;
                                    }
                                }else{
                                    $speciallity_array[] = $speciallity_data;
                                }
                                
                            }
                            
                        }
                        
                       //-----------------------service-------------------------
                        $exp1=explode(',',$services);
                        $treatments = array();
                        $sub_category_details = array();
                        $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE branch_id='$listing_id' group by category_id");
                        $ser = $Querys->result();
                        if(!empty($ser))
                        {
                            foreach ($ser as $ser1) {
                                $sub_category_details = array();
                                $s_id = $ser1->id;
                                $category_id = $ser1->category_id;
                                
                                $Query = $this->db->query("SELECT * FROM dentist_services_master WHERE id='$category_id' ");
                                $main_service = $Query->row()->name;
                              
                                $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE category_id='$category_id' AND branch_id='$listing_id'");
                                $m_service = $Querys->result();    
                                    if(!empty($m_service))
                                    {
                                        foreach ($m_service as $mser1) {
                                            $ids = $mser1->id;
                                            $service_name = $mser1->service_name;
                                            $price = $mser1->price;
                                            $discount = $mser1->discount;
                                            
                                                if(in_array($ids,$exp1))
                                                {
                                                        $sub_category_details[] = array('id'         => $ids,
                                                                                'service_name' => $service_name,
                                                                              'price' => $price,
                                                                              'discount' => $discount
                                                                              );
                                                }
                                            
                                        }
                                        
                                    }
                                    
                                    $treatments[] = array(
                                           
                                           'cat_name' => $main_service,
                                           'sub_catdata' => $sub_category_details
                                    
                                    );
                                  
                            }
                        }
                        //print_r($treatments);
                        
                        //-----------------------packages-------------------------
                        $packages = array();
                        $sql_p = "SELECT * FROM packages WHERE user_id='$listing_id'";
                        $query_p = $this->db->query($sql_p);
                        $count_p = $query_p->num_rows();
                          
                        if($count_p>0)
                        {
                            foreach($query_p->result_array() as $row)
                            {
                                   $pid                   =  $row['id'];
                                   $image                   =  $row['image'];
                                   $package_name            = $row['package_name'];
                                    $package_details        = $row['package_details'];
                                    $price                  = $row['price'];
                                    $discount               = $row['discount'];
                              
                                
                                if ($image != '') {
                                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                                } else {
                                    $image = '';
                                }
                                
                    
                       
                           
                        $packages[] = array(
                               'id'                     => $pid,
                               'package_name'           => $package_name,
                                'package_details'       => $package_details,
                                'price'                 => $price,
                                'discount'              => $discount,
                                'image'                 => $image
                               );
                       }
                   }
                  
                    $fav_pharmacy = $this->db->select('id')->from('dentist_favourite')->where('user_id', $user_id)->where('listing_id', $listing_id)->get()->num_rows();
                    $distances = str_replace(',', '.', GetDrivingDistance($lat, $lat1, $lng, $lng1));
                        $result_data[] = array(
                               'listing_id'             => $listing_id,
                               'listing_type'           => '39',
                               'name_of_clinic'         => $name_of_hospital,
                               'about_us'               => $about_us,
                               'establishment_year'     => $establishment_year,
                               'speciality'             => $speciallity_array,
                               'services'               => $services,
                               'packages'               => $packages,
                               'treatments'             => $treatments,
                               'address'                => $address,
                               'address_2'              => $address_2,
                               'pincode'                => $pincode,
                               'phone'                  => $phone,
                               'city'                   => $city,
                               'state'                  => $state,
                               'email'                  => $email,
                               'lat'                    => $lat1,
                               'lng'                    => $lng1,
                               'image'                  => $image,
                               'current_time'           => $current_day_final,
                               'opening_day'            => $final_Day,
                               'rating'                 => $rating,
                               'review'                 => '0',
                               'followers'              => $followers,
                               'following'              => $following,
                                'profile_views'         => $Profile_count,
                                'is_follow'             => $is_follow,
                               'user_discount'          => $user_discount,
                               'discount_description'   => $discount_description,
                               'favourite'              => $fav_pharmacy,
                               'mba'                    => $mba,
                               'certified'              => $certified,
                               'recommended'            => $recommended,
                               'featured'               => $featured,
                               'free_consultancy'       => $free_consultancy,
                               'distance'               =>$distances
                               );
                       }
                }
               
                $result_data1= array();
                $sql = sprintf("SELECT *,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE $wh is_active='1' HAVING distance < '5' ORDER BY distance $limit", ($lat), ($lng), ($lat), ($radius));
                $query = $this->db->query($sql);
                $count = $query->num_rows();
                  
                if($count>0)
                {
                    foreach($query->result_array() as $row)
                    {
                           $ids                     = $row['id'];
                           $listing_id              = $row['dentists_branch_user_id'];
                           $name_of_hospital        = $row['name_of_hospital'];
                           $about_us                = $row['about_us'];
                           $establishment_year      = $row['establishment_year'];
                           $speciality              = $row['speciality'];
                           $category                = $row['category'];
                           $surgery                 = $row['surgery'];
                           $services                = $row['services'];
                           $address                 = $row['address'];
                           $address_2               = $row['address_2'];
                           $pincode                 = $row['pincode'];
                           $phone                   = $row['phone'];
                           $city                    = $row['city'];
                           $state                   = $row['state'];
                           $email                   = $row['email'];
                           $lat1                     = $row['lat'];
                           $lng1                     =  $row['lng'];
                           $image                   =  $row['image'];
                           $rating                  = $row['rating'];
                           $review                  = $row['review'];
                           $user_discount           = $row['medicalwale_discount'];
                           $discount_description    = $row['description'];
                           $opening_hours           = $row['opening_hours'];
                           $mba                     = $row['mba'];
                           $certified               = $row['certified'];
                           $recommended             = $row['recommended'];
                           $featured                = $row['featured'];
                           $free_consultancy        = $row['free_consultancy'];
                           $distance       = $row['distance'];
                           
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
                                            $time[]            = str_replace('close-close', '', $time_check);
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
                        
                        $dfinal=date("l");
                        $current_day_final="";
                        foreach($final_Day as $key => $product)
                        {
                        
                            if($product['day']===$dfinal)
                            {
                             $current_day_final = $product['time'][0];
                            }
                        }    
                    
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $image = '';
                        }
                        
                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();
        
                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }
                        
                          //Review Count
                        $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$listing_id");
                        $Review_count = $Review_query->num_rows();
                        
                        
                        //Profile View
                        //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                        $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$listing_id and vendor_type='8'");
                        $Profile_count = $Profile_query->num_rows();
                        
                        
                            if($user_discount == null || $user_discount == '' || $user_discount == NULL)
                            {
                                $user_discount = '0';
                            }
                           
                           if($rating == null || $rating == '' || $rating == NULL)
                            {
                                $rating = '4';
                            }
                            
                            
                            
                              $speciallity_array = array();
                              $speciallity_data = explode(',', $speciality);
                                foreach ($speciallity_data as $speciallity_data) {
                            
                            if($speciallity_data != ""){
                                if(preg_match('/[0-9]/', $speciallity_data)){
                                    $Query = $this->db->query("SELECT name FROM dentists_specialist WHERE id='$speciallity_data'");
                                      $count = $Query->num_rows();
                                    if($count>0)
                                    {
                                    $what_we_offer_value = $Query->row()->name;
                                    $speciallity_array[] = $what_we_offer_value;
                                    }
                                    else
                                    {
                                        $speciallity_array[] = $speciallity_data;
                                    }
                                }else{
                                    $speciallity_array[] = $speciallity_data;
                                }
                                
                            }
                            
                        }
                        
                       //-----------------------service-------------------------
                        $exp1=explode(',',$services);
                        $treatments = array();
                        $sub_category_details = array();
                        $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE branch_id='$listing_id' group by category_id");
                        $ser = $Querys->result();
                        if(!empty($ser))
                        {
                            foreach ($ser as $ser1) {
                                $sub_category_details = array();
                                $s_id = $ser1->id;
                                $category_id = $ser1->category_id;
                                
                                $Query = $this->db->query("SELECT * FROM dentist_services_master WHERE id='$category_id' ");
                                $main_service = $Query->row()->name;
                              
                                $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE category_id='$category_id' AND branch_id='$listing_id'");
                                $m_service = $Querys->result();    
                                    if(!empty($m_service))
                                    {
                                        foreach ($m_service as $mser1) {
                                            $ids = $mser1->id;
                                            $service_name = $mser1->service_name;
                                            $price = $mser1->price;
                                            $discount = $mser1->discount;
                                            
                                                if(in_array($ids,$exp1))
                                                {
                                                        $sub_category_details[] = array('id'         => $ids,
                                                                              'service_name' => $service_name,
                                                                              'price' => $price,
                                                                              'discount' => $discount
                                                                              );
                                                }
                                            
                                        }
                                        
                                    }
                                    
                                    $treatments[] = array(
                                           
                                           'cat_name' => $main_service,
                                           'sub_catdata' => $sub_category_details
                                    
                                    );
                                  
                            }
                        }
                        //print_r($treatments);
                        
                        //-----------------------packages-------------------------
                        $packages = array();
                        $sql_p = "SELECT * FROM packages WHERE user_id='$listing_id' AND FIND_IN_SET('" . $ids . "',branch)";
                        $query_p = $this->db->query($sql_p);
                        $count_p = $query_p->num_rows();
                          
                        if($count_p>0)
                        {
                            foreach($query_p->result_array() as $row)
                            {
                                   $pid                   =  $row['id'];
                                   $image                    =  $row['image'];
                                   $package_name            = $row['package_name'];
                                    $package_details        = $row['package_details'];
                                    $price                  = $row['price'];
                                    $discount               = $row['discount'];
                              
                                
                                if ($image != '') {
                                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                                } else {
                                    $image = '';
                                }
                                
                    
                       
                           
                        $packages[] = array(
                               'id'                     => $pid,
                               'package_name'           => $package_name,
                                'package_details'       => $package_details,
                                'price'                 => $price,
                                'discount'              => $discount,
                                'image'                 => $image
                               );
                       }
                   }
                  
                     $fav_pharmacy = $this->db->select('id')->from('dentist_favourite')->where('user_id', $user_id)->where('listing_id', $listing_id)->get()->num_rows();
                     $distances = str_replace(',', '.', GetDrivingDistance($lat, $lat1, $lng, $lng1));
                        $result_data1[] = array(
                               'listing_id'             => $listing_id,
                               'listing_type'           => '39',
                               'name_of_clinic'         => $name_of_hospital,
                               'about_us'               => $about_us,
                               'establishment_year'     => $establishment_year,
                               'speciality'             => $speciallity_array,
                               'services'               => $services,
                               'packages'               => $packages,
                               'treatments'             => $treatments,
                               'address'                => $address,
                               'address_2'              => $address_2,
                               'pincode'                => $pincode,
                               'phone'                  => $phone,
                               'city'                   => $city,
                               'state'                  => $state,
                               'email'                  => $email,
                               'lat'                    => $lat1,
                               'lng'                    => $lng1,
                               'image'                  => $image,
                               'current_time'           => $current_day_final,
                               'opening_day'            => $final_Day,
                               'rating'                 => $rating,
                               'review'                 => '0',
                               'followers'              => $followers,
                               'following'              => $following,
                                'profile_views'         => $Profile_count,
                                'is_follow'             => $is_follow,
                               'user_discount'          => $user_discount,
                               'discount_description'   => $discount_description,
                               'favourite'              => $fav_pharmacy,
                               'mba'                    => $mba,
                               'certified'              => $certified,
                               'recommended'            => $recommended,
                               'featured'               => $featured,
                               'free_consultancy'       => $free_consultancy,
                               'distance'               =>$distances
                               );
                       }
                   }
                  
              $data = array_merge($result_data,$result_data1);  
        }  
        
        //---------------------------------------favourite---------------------------------------
        if($type==2)
        {
                $result_data= array();
                $result_data1= array();
                
                 $where="";
            
                if($where!=""){
                   $where=" and name_of_hospital like %$keyword% "; 
                }else{
                    $where =""; 
                }
                
                
                $favDetails = $this->db->query("SELECT * FROM dentist_favourite  WHERE user_id = '$user_id' $limit");
                $count_main = $favDetails->num_rows();   
                
                if($count_main > 0) {
                    foreach($favDetails->result_array() as $row) {
                        $list = $row['listing_id'];
                        $sql = sprintf("SELECT *,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_clinic_list  WHERE user_id = '$list' AND  is_active='1' HAVING distance < '5' ORDER BY distance $limit", ($lat), ($lng), ($lat), ($radius));
                        $query = $this->db->query($sql);
                        $count = $query->num_rows();
                          
                        if($count>0)
                        {
                            foreach($query->result_array() as $row)
                            {
                                   $listing_id              = $row['user_id'];
                                   $name_of_hospital        = $row['name_of_hospital'];
                                   $about_us                = $row['about_us'];
                                   $establishment_year      = $row['establishment_year'];
                                   $speciality              = $row['speciality'];
                                   $category                = $row['category'];
                                   $surgery                 = $row['surgery'];
                                   $services                = $row['services'];
                                   $address                 = $row['address'];
                                   $address_2               = $row['address_2'];
                                   $pincode                 = $row['pincode'];
                                   $phone                   = $row['phone'];
                                   $city                    = $row['city'];
                                   $state                   = $row['state'];
                                   $email                   = $row['email'];
                                   $lat1                     = $row['lat'];
                                   $lng1                     =  $row['lng'];
                                   $image                   =  $row['image'];
                                   $rating                  = $row['rating'];
                                   $review                  = $row['review'];
                                   $user_discount           = $row['medicalwale_discount'];
                                   $discount_description    = $row['description'];
                                   $opening_hours           = $row['opening_hours'];
                                   $mba                     = $row['mba'];
                                   $certified               = $row['certified'];
                                   $recommended             = $row['recommended'];
                                   $featured                = $row['featured'];
                                   $free_consultancy        = $row['free_consultancy'];
                                   $distance        = $row['distance'];
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
                                                    $time[]            = str_replace('close-close', '', $time_check);
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
                                
                                $dfinal=date("l");
                            $current_day_final="";
                            foreach($final_Day as $key => $product)
                            {
                            
                            if($product['day']===$dfinal)
                            {
                             $current_day_final = $product['time'][0];
                            }
                            }    
                                
                                if ($image != '') {
                                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                                } else {
                                    $image = '';
                                }
                                
                                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();
                
                                if ($is_follow > 0) {
                                    $is_follow = 'Yes';
                                } else {
                                    $is_follow = 'No';
                                }
                                
                                  //Review Count
                                $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$listing_id");
                                $Review_count = $Review_query->num_rows();
                                
                                
                                //Profile View
                                //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                                $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$listing_id and vendor_type='8'");
                                $Profile_count = $Profile_query->num_rows();
                                
                                
                                    if($user_discount == null || $user_discount == '' || $user_discount == NULL)
                                    {
                                        $user_discount = '0';
                                    }
                                   
                                   if($rating == null || $rating == '' || $rating == NULL)
                                    {
                                        $rating = '4';
                                    }
                                    
                                    
                                    
                                      $speciallity_array = array();
                                      $speciallity_data = explode(',', $speciality);
                                        foreach ($speciallity_data as $speciallity_data) {
                                    
                                    if($speciallity_data != ""){
                                        if(preg_match('/[0-9]/', $speciallity_data)){
                                            $Query = $this->db->query("SELECT name FROM dentists_specialist WHERE id='$speciallity_data'");
                                              $count = $Query->num_rows();
                                            if($count>0)
                                            {
                                            $what_we_offer_value = $Query->row()->name;
                                            $speciallity_array[] = $what_we_offer_value;
                                            }
                                            else
                                            {
                                                $speciallity_array[] = $speciallity_data;
                                            }
                                        }else{
                                            $speciallity_array[] = $speciallity_data;
                                        }
                                        
                                    }
                                    
                                }
                                
                               //-----------------------service-------------------------
                                $exp1=explode(',',$services);
                                $treatments = array();
                                $sub_category_details = array();
                                $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE branch_id='$listing_id' group by category_id");
                                $ser = $Querys->result();
                                if(!empty($ser))
                                {
                                    foreach ($ser as $ser1) {
                                        $sub_category_details = array();
                                        $s_id = $ser1->id;
                                        $category_id = $ser1->category_id;
                                        
                                        $Query = $this->db->query("SELECT * FROM dentist_services_master WHERE id='$category_id' ");
                                        $main_service = $Query->row()->name;
                                      
                                        $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE category_id='$category_id' AND branch_id='$listing_id'");
                                        $m_service = $Querys->result();    
                                            if(!empty($m_service))
                                            {
                                                foreach ($m_service as $mser1) {
                                                    $ids = $mser1->id;
                                                    $service_name = $mser1->service_name;
                                                    $price = $mser1->price;
                                                    $discount = $mser1->discount;
                                                    
                                                        if(in_array($ids,$exp1))
                                                        {
                                                                $sub_category_details[] = array('id'         => $ids,
                                                                                      'service_name' => $service_name,
                                                                                      'price' => $price,
                                                                                      'discount' => $discount
                                                                                      );
                                                        }
                                                    
                                                }
                                                
                                            }
                                            
                                            $treatments[] = array(
                                                   
                                                   'cat_name' => $main_service,
                                                   'sub_catdata' => $sub_category_details
                                            
                                            );
                                          
                                    }
                                }
                                //print_r($treatments);
                                
                                //-----------------------packages-------------------------
                                $packages = array();
                                $sql_p = "SELECT * FROM packages WHERE user_id='$listing_id'";
                                $query_p = $this->db->query($sql_p);
                                $count_p = $query_p->num_rows();
                                  
                                if($count_p>0)
                                {
                                    foreach($query_p->result_array() as $row)
                                    {
                                           $pid                   =  $row['id'];
                                           $image                   =  $row['image'];
                                           $package_name            = $row['package_name'];
                                            $package_details        = $row['package_details'];
                                            $price                  = $row['price'];
                                            $discount               = $row['discount'];
                                      
                                        
                                        if ($image != '') {
                                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                                        } else {
                                            $image = '';
                                        }
                                        
                            
                               
                                   
                                $packages[] = array(
                                       'id'                     => $pid,
                                       'package_name'           => $package_name,
                                        'package_details'       => $package_details,
                                        'price'                 => $price,
                                        'discount'              => $discount,
                                        'image'                 => $image
                                       );
                               }
                           }
                          
                            $fav_pharmacy = $this->db->select('id')->from('dentist_favourite')->where('user_id', $user_id)->where('listing_id', $listing_id)->get()->num_rows();
                            $distances = str_replace(',', '.', GetDrivingDistance($lat, $lat1, $lng, $lng1));
                                $result_data[] = array(
                                       'listing_id'             => $listing_id,
                                       'listing_type'           => '39',
                                       'name_of_clinic'         => $name_of_hospital,
                                       'about_us'               => $about_us,
                                       'establishment_year'     => $establishment_year,
                                       'speciality'             => $speciallity_array,
                                       'services'               => $services,
                                       'packages'               => $packages,
                                       'treatments'             => $treatments,
                                       'address'                => $address,
                                       'address_2'              => $address_2,
                                       'pincode'                => $pincode,
                                       'phone'                  => $phone,
                                       'city'                   => $city,
                                       'state'                  => $state,
                                       'email'                  => $email,
                                       'lat'                    => $lat1,
                                       'lng'                    => $lng1,
                                       'image'                  => $image,
                                       'current_time'           => $current_day_final,
                                       'opening_day'            => $final_Day,
                                       'rating'                 => $rating,
                                       'review'                 => '0',
                                       'followers'              => $followers,
                                       'following'              => $following,
                                        'profile_views'         => $Profile_count,
                                        'is_follow'             => $is_follow,
                                       'user_discount'          => $user_discount,
                                       'discount_description'   => $discount_description,
                                       'favourite'              => $fav_pharmacy,
                                       'mba'                    => $mba,
                                       'certified'              => $certified,
                                       'recommended'            => $recommended,
                                       'featured'               => $featured,
                                       'free_consultancy'       => $free_consultancy,
                                       'distance'       => $distances
                                       );
                               }
                        }
                       
                        $sql = sprintf("SELECT *,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE dentists_branch_user_id = '$list' AND is_active='1' HAVING distance < '5' ORDER BY distance $limit", ($lat), ($lng), ($lat), ($radius));
                        $query = $this->db->query($sql);
                        $count = $query->num_rows();
                          
                        if($count>0)
                        {
                            foreach($query->result_array() as $row)
                            {
                                   $ids                     = $row['id'];
                                   $listing_id              = $row['dentists_branch_user_id'];
                                   $name_of_hospital        = $row['name_of_hospital'];
                                   $about_us                = $row['about_us'];
                                   $establishment_year      = $row['establishment_year'];
                                   $speciality              = $row['speciality'];
                                   $category                = $row['category'];
                                   $surgery                 = $row['surgery'];
                                   $services                = $row['services'];
                                   $address                 = $row['address'];
                                   $address_2               = $row['address_2'];
                                   $pincode                 = $row['pincode'];
                                   $phone                   = $row['phone'];
                                   $city                    = $row['city'];
                                   $state                   = $row['state'];
                                   $email                   = $row['email'];
                                   $lat1                     = $row['lat'];
                                   $lng1                     =  $row['lng'];
                                   $image                   =  $row['image'];
                                   $rating                  = $row['rating'];
                                   $review                  = $row['review'];
                                   $user_discount           = $row['medicalwale_discount'];
                                   $discount_description    = $row['description'];
                                   $opening_hours           = $row['opening_hours'];
                                   $mba                     = $row['mba'];
                                   $certified               = $row['certified'];
                                   $recommended             = $row['recommended'];
                                   $featured                = $row['featured'];
                                   $free_consultancy        = $row['free_consultancy'];
                                   $distance        = $row['distance'];
                                   
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
                                                    $time[]            = str_replace('close-close', '', $time_check);
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
                                
                                $dfinal=date("l");
                                $current_day_final="";
                                foreach($final_Day as $key => $product)
                                {
                                
                                    if($product['day']===$dfinal)
                                    {
                                     $current_day_final = $product['time'][0];
                                    }
                                }    
                            
                                if ($image != '') {
                                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                                } else {
                                    $image = '';
                                }
                                
                                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();
                
                                if ($is_follow > 0) {
                                    $is_follow = 'Yes';
                                } else {
                                    $is_follow = 'No';
                                }
                                
                                  //Review Count
                                $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$listing_id");
                                $Review_count = $Review_query->num_rows();
                                
                                
                                //Profile View
                                //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                                $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$listing_id and vendor_type='8'");
                                $Profile_count = $Profile_query->num_rows();
                                
                                
                                    if($user_discount == null || $user_discount == '' || $user_discount == NULL)
                                    {
                                        $user_discount = '0';
                                    }
                                   
                                   if($rating == null || $rating == '' || $rating == NULL)
                                    {
                                        $rating = '4';
                                    }
                                    
                                    
                                    
                                      $speciallity_array = array();
                                      $speciallity_data = explode(',', $speciality);
                                        foreach ($speciallity_data as $speciallity_data) {
                                    
                                    if($speciallity_data != ""){
                                        if(preg_match('/[0-9]/', $speciallity_data)){
                                            $Query = $this->db->query("SELECT name FROM dentists_specialist WHERE id='$speciallity_data'");
                                              $count = $Query->num_rows();
                                            if($count>0)
                                            {
                                            $what_we_offer_value = $Query->row()->name;
                                            $speciallity_array[] = $what_we_offer_value;
                                            }
                                            else
                                            {
                                                $speciallity_array[] = $speciallity_data;
                                            }
                                        }else{
                                            $speciallity_array[] = $speciallity_data;
                                        }
                                        
                                    }
                                    
                                }
                                
                               //-----------------------service-------------------------
                                $exp1=explode(',',$services);
                                $treatments = array();
                                $sub_category_details = array();
                                $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE branch_id='$listing_id' group by category_id");
                                $ser = $Querys->result();
                                if(!empty($ser))
                                {
                                    foreach ($ser as $ser1) {
                                        $sub_category_details = array();
                                        $s_id = $ser1->id;
                                        $category_id = $ser1->category_id;
                                        
                                        $Query = $this->db->query("SELECT * FROM dentist_services_master WHERE id='$category_id' ");
                                        $main_service = $Query->row()->name;
                                      
                                        $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE category_id='$category_id' AND branch_id='$listing_id'");
                                        $m_service = $Querys->result();    
                                            if(!empty($m_service))
                                            {
                                                foreach ($m_service as $mser1) {
                                                    $ids = $mser1->id;
                                                    $service_name = $mser1->service_name;
                                                    $price = $mser1->price;
                                                    $discount = $mser1->discount;
                                                    
                                                        if(in_array($ids,$exp1))
                                                        {
                                                                $sub_category_details[] = array('id'         => $ids,
                                                                                      'service_name' => $service_name,
                                                                                      'price' => $price,
                                                                                      'discount' => $discount
                                                                                      );
                                                        }
                                                    
                                                }
                                                
                                            }
                                            
                                            $treatments[] = array(
                                                   
                                                   'cat_name' => $main_service,
                                                   'sub_catdata' => $sub_category_details
                                            
                                            );
                                          
                                    }
                                }
                                //print_r($treatments);
                                
                                //-----------------------packages-------------------------
                                $packages = array();
                                $sql_p = "SELECT * FROM packages WHERE user_id='$listing_id' AND FIND_IN_SET('" . $ids . "',branch)";
                                $query_p = $this->db->query($sql_p);
                                $count_p = $query_p->num_rows();
                                  
                                if($count_p>0)
                                {
                                    foreach($query_p->result_array() as $row)
                                    {
                                          $pid                   =  $row['id'];
                                           $image                   =  $row['image'];
                                           $package_name            = $row['package_name'];
                                            $package_details        = $row['package_details'];
                                            $price                  = $row['price'];
                                            $discount               = $row['discount'];
                                      
                                        
                                        if ($image != '') {
                                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                                        } else {
                                            $image = '';
                                        }
                                        
                            
                               
                                   
                                $packages[] = array(
                                       'id'                     => $pid,
                                       'package_name'           => $package_name,
                                        'package_details'       => $package_details,
                                        'price'                 => $price,
                                        'discount'              => $discount,
                                        'image'                 => $image
                                       );
                               }
                           }
                          
                             $fav_pharmacy = $this->db->select('id')->from('dentist_favourite')->where('user_id', $user_id)->where('listing_id', $listing_id)->get()->num_rows();
                            $distances = str_replace(',', '.', GetDrivingDistance($lat, $lat1, $lng, $lng1));
                                $result_data1[] = array(
                                       'listing_id'             => $listing_id,
                                       'listing_type'           => '39',
                                       'name_of_clinic'         => $name_of_hospital,
                                       'about_us'               => $about_us,
                                       'establishment_year'     => $establishment_year,
                                       'speciality'             => $speciallity_array,
                                       'services'               => $services,
                                       'packages'               => $packages,
                                       'treatments'             => $treatments,
                                       'address'                => $address,
                                       'address_2'              => $address_2,
                                       'pincode'                => $pincode,
                                       'phone'                  => $phone,
                                       'city'                   => $city,
                                       'state'                  => $state,
                                       'email'                  => $email,
                                       'lat'                    => $lat1,
                                       'lng'                    => $lng1,
                                       'image'                  => $image,
                                       'current_time'           => $current_day_final,
                                       'opening_day'            => $final_Day,
                                       'rating'                 => $rating,
                                       'review'                 => '0',
                                       'followers'              => $followers,
                                       'following'              => $following,
                                        'profile_views'         => $Profile_count,
                                        'is_follow'             => $is_follow,
                                       'user_discount'          => $user_discount,
                                       'discount_description'   => $discount_description,
                                       'favourite'              => $fav_pharmacy,
                                       'mba'                    => $mba,
                                       'certified'              => $certified,
                                       'recommended'            => $recommended,
                                       'featured'               => $featured,
                                       'free_consultancy'       => $free_consultancy,
                                       'distance'       => $distances
                                       );
                               }
                           }
                    }
                    $data = array_merge($result_data,$result_data1);  
                }
        }   
        
        //---------------------------------------------branch-----------------------------------------------
        if($type==5)
        {
             $where="";
            
      
            
            if($keyword!=""){
               $where=" name_of_hospital LIKE '%%$keyword%%' and";
               
            }else{
                $where =""; 
            }
            
            
            $sql = "SELECT * FROM dentists_branch  WHERE hub_user_id='$listing_id1' $where  $limit";
            $query = $this->db->query($sql);
            $count = $query->num_rows();
              
            if($count>0)
            {
                foreach($query->result_array() as $row)
                {
                       $ids                     = $row['id'];
                       $branch_id               = $row['dentists_branch_user_id'];
                       $name_of_hospital        = $row['name_of_hospital'];
                       $about_us                = $row['about_us'];
                       $establishment_year      = $row['establishment_year'];
                       $speciality              = $row['speciality'];
                       $category                = $row['category'];
                       $surgery                 = $row['surgery'];
                       $services                = $row['services'];
                       $address                 = $row['address'];
                       $address_2               = $row['address_2'];
                       $pincode                 = $row['pincode'];
                       $phone                   = $row['phone'];
                       $city                    = $row['city'];
                       $state                   = $row['state'];
                       $email                   = $row['email'];
                       $lat1                     = $row['lat'];
                       $lng1                     =  $row['lng'];
                       $image                   =  $row['image'];
                       $rating                  = $row['rating'];
                       $review                  = $row['review'];
                       $user_discount           = $row['medicalwale_discount'];
                       $discount_description    = $row['description'];
                       $opening_hours     = $row['opening_hours'];
                        $mba     = $row['mba'];
                        $certified               = $row['certified'];
                   $recommended             = $row['recommended'];
                   $featured                = $row['featured'];
                   $free_consultancy        = $row['free_consultancy'];
                       
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
                                        $time[]            = str_replace('close-close', '', $time_check);
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
                    
                    if ($image != '') {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                    } else {
                        $image = '';
                    }
                    
                    $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $branch_id)->get()->num_rows();
                    $following = $this->db->select('id')->from('follow_user')->where('user_id', $branch_id)->get()->num_rows();
                    $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $branch_id)->get()->num_rows();
    
                    if ($is_follow > 0) {
                        $is_follow = 'Yes';
                    } else {
                        $is_follow = 'No';
                    }
                    
                      //Review Count
                    $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$branch_id");
                    $Review_count = $Review_query->num_rows();
                    
                    
                    //Profile View
                    //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                    $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$branch_id and vendor_type='8'");
                    $Profile_count = $Profile_query->num_rows();
                    
                    
                        if($user_discount == null || $user_discount == '' || $user_discount == NULL)
                        {
                            $user_discount = '0';
                        }
                       
                       if($rating == null || $rating == '' || $rating == NULL)
                        {
                            $rating = '4';
                        }
                        
                        
                        
                          $speciallity_array = array();
                          $speciallity_data = explode(',', $speciality);
                    foreach ($speciallity_data as $speciallity_data) {
                        
                        if($speciallity_data != ""){
                            if(preg_match('/[0-9]/', $speciallity_data)){
                                $Query = $this->db->query("SELECT name FROM dentists_specialist WHERE id='$speciallity_data'");
                                  $count = $Query->num_rows();
                                if($count>0)
                                {
                                $what_we_offer_value = $Query->row()->name;
                                $speciallity_array[] = $what_we_offer_value;
                                }
                                else
                                {
                                    $speciallity_array[] = $speciallity_data;
                                }
                            }else{
                                $speciallity_array[] = $speciallity_data;
                            }
                            
                        }
                        
                    }
                    
                     //-----------------------service-------------------------
                    $exp1=explode(',',$services);
                    $treatments = array();
                    $sub_category_details = array();
                    $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE branch_id='$branch_id' group by category_id");
                    $ser = $Querys->result();
                    if(!empty($ser))
                    {
                        foreach ($ser as $ser1) {
                            $sub_category_details = array();
                            $s_id = $ser1->id;
                            $category_id = $ser1->category_id;
                            
                            $Query = $this->db->query("SELECT * FROM dentist_services_master WHERE id='$category_id' ");
                            $main_service = $Query->row()->name;
                          
                            $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE category_id='$category_id' AND branch_id='$branch_id'");
                            $m_service = $Querys->result();    
                                if(!empty($m_service))
                                {
                                    foreach ($m_service as $mser1) {
                                        $ids = $mser1->id;
                                        $service_name = $mser1->service_name;
                                        $price = $mser1->price;
                                        $discount = $mser1->discount;
                                        
                                            if(in_array($ids,$exp1))
                                            {
                                                    $sub_category_details[] = array(
                                                        'id'         => $ids,
                                                                             'service_name' => $service_name,
                                                                          'price' => $price,
                                                                          'discount' => $discount
                                                                          );
                                            }
                                        
                                    }
                                    
                                }
                                
                                $treatments[] = array(
                                       
                                       'cat_name' => $main_service,
                                       'sub_catdata' => $sub_category_details
                                
                                );
                              
                        }
                    }
                    //print_r($treatments);
                    
                    //-----------------------packages-------------------------
                    $packages = array();
                    $sql_p = "SELECT * FROM packages WHERE user_id='$listing_id1' AND FIND_IN_SET('" . $ids . "',branch)";
                    $query_p = $this->db->query($sql_p);
                    $count_p = $query_p->num_rows();
                      
                    if($count_p>0)
                    {
                        foreach($query_p->result_array() as $row)
                        {
                               $pid                   =  $row['id'];
                               $image                   =  $row['image'];
                               $package_name            = $row['package_name'];
                                $package_details        = $row['package_details'];
                                $price                  = $row['price'];
                                $discount               = $row['discount'];
                          
                            
                            if ($image != '') {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                            } else {
                                $image = '';
                            }
                            
                
                   
                       
                    $packages[] = array(
                           'id'                     => $pid,
                           'package_name'           => $package_name,
                            'package_details'       => $package_details,
                            'price'                 => $price,
                            'discount'              => $discount,
                            'image'                 => $image
                           );
                   }
               }
               $fav_pharmacy = $this->db->select('id')->from('dentist_favourite')->where('user_id', $user_id)->where('listing_id', $branch_id)->get()->num_rows();
                                
                    $distances = str_replace(',', '.', GetDrivingDistance($lat, $lat1, $lng, $lng1));
                    
                    $data[] = array(
                            'id'                    => $ids,
                           'listing_id'             => $branch_id,  
                           'listing_type'           => '39',
                           'distance'               => $distances,
                           'name_of_clinic'         => $name_of_hospital,
                           'about_us'               => $about_us,
                           'establishment_year'     => $establishment_year,
                           'speciality'             => $speciallity_array,
                           'services'               => $services,
                           'packages'               => $packages,
                           'treatments'             => $treatments,
                           'address'                => $address,
                           'address_2'              => $address_2,
                           'pincode'                => $pincode,
                           'phone'                  => $phone,
                           'city'                   => $city,
                           'state'                  => $state,
                           'email'                  => $email,
                           'lat'                    => $lat,
                           'lng'                    => $lng,
                           'image'                  => $image,
                           'opening_day'            => $final_Day,
                           'rating'                 => $rating,
                           'review'                 => '0',
                           'followers'              => $followers,
                           'following'              => $following,
                            'profile_views'         => $Profile_count,
                            'is_follow'             => $is_follow,
                           'user_discount'          => $user_discount,
                           'discount_description'   => $discount_description,
                           'favourite'              => $fav_pharmacy,
                           'mba'                    => $mba,
                           'certified'              => $certified,
                           'recommended'            => $recommended,
                           'featured'               => $featured,
                           'free_consultancy'       => $free_consultancy,
                           
                           );
                   }
               }
        }
           return $data;
    } 
    
    public function dental_clinic_profile($user_id,$listing_id,$lat,$lng)
    {
               $radius = '5';
        $result_data = array();
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
        
        $sql = "SELECT * FROM dentists_clinic_list  WHERE user_id='$listing_id' ";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
          
        if($count>0)
        {
            $row = $query->row_array() ;
            
                   $listing_id              = $row['user_id'];
                   $name_of_hospital        = $row['name_of_hospital'];
                   $about_us                = $row['about_us'];
                   $establishment_year      = $row['establishment_year'];
                   $speciality              = $row['speciality'];
                   $category                = $row['category'];
                   $surgery                 = $row['surgery'];
                   $services                = $row['services'];
                   $address                 = $row['address'];
                   $address_2               = $row['address_2'];
                   $pincode                 = $row['pincode'];
                   $phone                   = $row['phone'];
                   $city                    = $row['city'];
                   $state                   = $row['state'];
                   $email                   = $row['email'];
                   $lat1                     = $row['lat'];
                   $lng1                     =  $row['lng'];
                   $image                   =  $row['image'];
                   $rating                  = $row['rating'];
                   $review                  = $row['review'];
                   $user_discount           = $row['medicalwale_discount'];
                   $discount_description    = $row['description'];
                   $opening_hours           = $row['opening_hours'];
                   $mba                     = $row['mba'];
                   $certified               = $row['certified'];
                   $recommended             = $row['recommended'];
                   $featured                = $row['featured'];
                   $free_consultancy        = $row['free_consultancy'];
                   
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
                
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
                
                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                
                  //Review Count
                $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$listing_id");
                $Review_count = $Review_query->num_rows();
                
                
                //Profile View
                //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$listing_id and vendor_type='8'");
                $Profile_count = $Profile_query->num_rows();
                
                
                    if($user_discount == null || $user_discount == '' || $user_discount == NULL)
                    {
                        $user_discount = '0';
                    }
                   
                   if($rating == null || $rating == '' || $rating == NULL)
                    {
                        $rating = '4';
                    }
                    
                    
                    
                      $speciallity_array = array();
                      $speciallity_data = explode(',', $speciality);
                        foreach ($speciallity_data as $speciallity_data) {
                    
                    if($speciallity_data != ""){
                        if(preg_match('/[0-9]/', $speciallity_data)){
                            $Query = $this->db->query("SELECT name FROM dentists_specialist WHERE id='$speciallity_data'");
                              $count = $Query->num_rows();
                            if($count>0)
                            {
                            $what_we_offer_value = $Query->row()->name;
                            $speciallity_array[] = $what_we_offer_value;
                            }
                            else
                            {
                                $speciallity_array[] = $speciallity_data;
                            }
                        }else{
                            $speciallity_array[] = $speciallity_data;
                        }
                        
                    }
                    
                }
                
               //-----------------------service-------------------------
                $exp1=explode(',',$services);
                $treatments = array();
                $sub_category_details = array();
                $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE branch_id='$listing_id' group by category_id");
                $ser = $Querys->result();
                if(!empty($ser))
                {
                    foreach ($ser as $ser1) {
                        $sub_category_details = array();
                        $s_id = $ser1->id;
                        $category_id = $ser1->category_id;
                        
                        $Query = $this->db->query("SELECT * FROM dentist_services_master WHERE id='$category_id' ");
                        $main_service = $Query->row()->name;
                      
                        $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE category_id='$category_id' AND branch_id='$listing_id'");
                        $m_service = $Querys->result();    
                            if(!empty($m_service))
                            {
                                foreach ($m_service as $mser1) {
                                    $ids = $mser1->id;
                                    $service_name = $mser1->service_name;
                                    $price = $mser1->price;
                                    $discount = $mser1->discount;
                                    
                                        if(in_array($ids,$exp1))
                                        {
                                                $sub_category_details[] = array('id'         => $ids,
                                                                       'service_name' => $service_name,
                                                                      'price' => $price,
                                                                      'discount' => $discount
                                                                      );
                                        }
                                    
                                }
                                
                            }
                            
                            $treatments[] = array(
                                   
                                   'cat_name' => $main_service,
                                   'sub_catdata' => $sub_category_details
                            
                            );
                          
                    }
                }
                //print_r($treatments);
                
                //-----------------------packages-------------------------
                $packages = array();
                $sql_p = "SELECT * FROM packages WHERE user_id='$listing_id'";
                $query_p = $this->db->query($sql_p);
                $count_p = $query_p->num_rows();
                  
                if($count_p>0)
                {
                    foreach($query_p->result_array() as $row)
                    {
                           $pid                   =  $row['id'];
                           $image                   =  $row['image'];
                           $package_name            = $row['package_name'];
                            $package_details        = $row['package_details'];
                            $price                  = $row['price'];
                            $discount               = $row['discount'];
                      
                        
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $image = '';
                        }
                        
            
               
                   
                $packages[] = array(
                       'id'                     => $pid ,
                       'package_name'           => $package_name,
                        'package_details'       => $package_details,
                        'price'                 => $price,
                        'discount'              => $discount,
                        'image'                 => $image
                       );
               }
           }
          $fav_pharmacy = $this->db->select('id')->from('dentist_favourite')->where('user_id', $user_id)->where('listing_id', $listing_id)->get()->num_rows();
                            
             $distances = str_replace(',', '.', GetDrivingDistance($lat, $lat1, $lng, $lng1));
                $result_data[] = array(
                       'listing_id'             => $listing_id,
                       'distance'               => $distances,
                       'listing_type'           => '39',
                       'name_of_clinic'         => $name_of_hospital,
                       'about_us'               => $about_us,
                       'establishment_year'     => $establishment_year,
                       'speciality'             => $speciallity_array,
                       'services'               => $services,
                       'packages'               => $packages,
                       'treatments'             => $treatments,
                       'address'                => $address,
                       'address_2'              => $address_2,
                       'pincode'                => $pincode,
                       'phone'                  => $phone,
                       'city'                   => $city,
                       'state'                  => $state,
                       'email'                  => $email,
                       'lat'                    => $lat,
                       'lng'                    => $lng,
                       'image'                  => $image,
                       'opening_day'            => $final_Day,
                       'rating'                 => $rating,
                       'review'                 => '0',
                       'followers'              => $followers,
                       'following'              => $following,
                        'profile_views'         => $Profile_count,
                        'is_follow'             => $is_follow,
                       'user_discount'          => $user_discount,
                       'discount_description'   => $discount_description,
                       'favourite'              => $fav_pharmacy,
                       'mba'                    => $mba,
                       'certified'              => $certified,
                       'recommended'            => $recommended,
                       'featured'               => $featured,
                       'free_consultancy'       => $free_consultancy
                       );
               
           }
           else
           {
               $sql = "SELECT * FROM dentists_branch  WHERE dentists_branch_user_id='$listing_id' ";
                $query = $this->db->query($sql);
                $count = $query->num_rows();
                  
                if($count>0)
                {
                    $row = $query->row_array() ;
                           $listing_id              = $row['dentists_branch_user_id'];
                           $name_of_hospital        = $row['name_of_hospital'];
                           $about_us                = $row['about_us'];
                           $establishment_year      = $row['establishment_year'];
                           $speciality              = $row['speciality'];
                           $category                = $row['category'];
                           $surgery                 = $row['surgery'];
                           $services                = $row['services'];
                           $address                 = $row['address'];
                           $address_2               = $row['address_2'];
                           $pincode                 = $row['pincode'];
                           $phone                   = $row['phone'];
                           $city                    = $row['city'];
                           $state                   = $row['state'];
                           $email                   = $row['email'];
                           $lat1                     = $row['lat'];
                           $lng1                     =  $row['lng'];
                           $image                   =  $row['image'];
                           $rating                  = $row['rating'];
                           $review                  = $row['review'];
                           $user_discount           = $row['medicalwale_discount'];
                           $discount_description    = $row['description'];
                           $opening_hours           = $row['opening_hours'];
                           $mba                     = $row['mba'];
                           $certified               = $row['certified'];
                           $recommended             = $row['recommended'];
                           $featured                = $row['featured'];
                           $free_consultancy        = $row['free_consultancy'];
                           
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
                        
                        if ($image != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $image = '';
                        }
                        
                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();
        
                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }
                        
                          //Review Count
                        $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$listing_id");
                        $Review_count = $Review_query->num_rows();
                        
                        
                        //Profile View
                        //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                        $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$listing_id and vendor_type='8'");
                        $Profile_count = $Profile_query->num_rows();
                        
                        
                            if($user_discount == null || $user_discount == '' || $user_discount == NULL)
                            {
                                $user_discount = '0';
                            }
                           
                           if($rating == null || $rating == '' || $rating == NULL)
                            {
                                $rating = '4';
                            }
                            
                            
                            
                              $speciallity_array = array();
                              $speciallity_data = explode(',', $speciality);
                                foreach ($speciallity_data as $speciallity_data) {
                            
                            if($speciallity_data != ""){
                                if(preg_match('/[0-9]/', $speciallity_data)){
                                    $Query = $this->db->query("SELECT name FROM dentists_specialist WHERE id='$speciallity_data'");
                                      $count = $Query->num_rows();
                                    if($count>0)
                                    {
                                    $what_we_offer_value = $Query->row()->name;
                                    $speciallity_array[] = $what_we_offer_value;
                                    }
                                    else
                                    {
                                        $speciallity_array[] = $speciallity_data;
                                    }
                                }else{
                                    $speciallity_array[] = $speciallity_data;
                                }
                                
                            }
                            
                        }
                        
                       //-----------------------service-------------------------
                        $exp1=explode(',',$services);
                        $treatments = array();
                        $sub_category_details = array();
                        $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE branch_id='$listing_id' group by category_id");
                        $ser = $Querys->result();
                        if(!empty($ser))
                        {
                            foreach ($ser as $ser1) {
                                $sub_category_details = array();
                                $s_id = $ser1->id;
                                $category_id = $ser1->category_id;
                                
                                $Query = $this->db->query("SELECT * FROM dentist_services_master WHERE id='$category_id' ");
                                $main_service = $Query->row()->name;
                              
                                $Querys = $this->db->query("SELECT * FROM dental_services_offered WHERE category_id='$category_id' AND branch_id='$listing_id'");
                                $m_service = $Querys->result();    
                                    if(!empty($m_service))
                                    {
                                        foreach ($m_service as $mser1) {
                                            $ids = $mser1->id;
                                            $service_name = $mser1->service_name;
                                            $price = $mser1->price;
                                            $discount = $mser1->discount;
                                            
                                                if(in_array($ids,$exp1))
                                                {
                                                        $sub_category_details[] = array(
                                                                              'id'         => $ids,
                                                                              'service_name' => $service_name,
                                                                              'price' => $price,
                                                                              'discount' => $discount
                                                                              );
                                                }
                                            
                                        }
                                        
                                    }
                                    
                                    $treatments[] = array(
                                           
                                           'cat_name' => $main_service,
                                           'sub_catdata' => $sub_category_details
                                    
                                    );
                                  
                            }
                        }
                        //print_r($treatments);
                        
                        //-----------------------packages-------------------------
                        $packages = array();
                        $sql_p = "SELECT * FROM packages WHERE user_id='$listing_id'";
                        $query_p = $this->db->query($sql_p);
                        $count_p = $query_p->num_rows();
                          
                        if($count_p>0)
                        {
                            foreach($query_p->result_array() as $row)
                            {
                                   $pid                   =  $row['id'];
                                   $image                   =  $row['image'];
                                   $package_name            = $row['package_name'];
                                    $package_details        = $row['package_details'];
                                    $price                  = $row['price'];
                                    $discount               = $row['discount'];
                              
                                
                                if ($image != '') {
                                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                                } else {
                                    $image = '';
                                }
                                
                        $packages[] = array(
                               'id'                     => $pid,
                               'package_name'           => $package_name,
                                'package_details'       => $package_details,
                                'price'                 => $price,
                                'discount'              => $discount,
                                'image'                 => $image
                               );
                       }
                   }
                  $fav_pharmacy = $this->db->select('id')->from('dentist_favourite')->where('user_id', $user_id)->where('listing_id', $listing_id)->get()->num_rows();
           
                     $distances = str_replace(',', '.', GetDrivingDistance($lat, $lat1, $lng, $lng1));
                        $result_data[] = array(
                               'listing_id'             => $listing_id,
                               'distance'               => $distances,
                               'listing_type'           => '39',
                               'name_of_clinic'         => $name_of_hospital,
                               'about_us'               => $about_us,
                               'establishment_year'     => $establishment_year,
                               'speciality'             => $speciallity_array,
                               'services'               => $services,
                               'packages'               => $packages,
                               'treatments'             => $treatments,
                               'address'                => $address,
                               'address_2'              => $address_2,
                               'pincode'                => $pincode,
                               'phone'                  => $phone,
                               'city'                   => $city,
                               'state'                  => $state,
                               'email'                  => $email,
                               'lat'                    => $lat,
                               'lng'                    => $lng,
                               'image'                  => $image,
                               'opening_day'            => $final_Day,
                               'rating'                 => $rating,
                               'review'                 => '0',
                               'followers'              => $followers,
                               'following'              => $following,
                               'profile_views'         => $Profile_count,
                               'is_follow'             => $is_follow,
                               'user_discount'          => $user_discount,
                               'discount_description'   => $discount_description,
                               'favourite'              => $fav_pharmacy,
                               'mba'                    => $mba,
                               'certified'              => $certified,
                               'recommended'            => $recommended,
                               'featured'               => $featured,
                               'free_consultancy'       => $free_consultancy
                               );
                       
                   }
           }
           
           return $result_data;
     }
     
    public function favourite_dental_clinic($user_id, $listing_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $pharmacy_view = $this->db->select('id')->from('dentist_favourite')->where('user_id', $user_id)->where('listing_id', $listing_id)->get()->num_rows();
        if($pharmacy_view > 0)
        {
            $this->db->where('user_id', $user_id);
            $this->db->where('listing_id', $listing_id);
            $this->db->delete('dentist_favourite');    
            
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
            $this->db->insert('dentist_favourite', $data);  
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
       
       
       $resultpost[]   = array(
                'id' => "",
                'category_id' => "",
                'category' => "All",
                'details' => "",
                'category_hindi' => "",
                'details_hindi' => "",
                'image' => ""
            );
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
            $resultpost[]   = array(
                'id' => $id,
                'category_id' => $category_id,
                'category' => $category,
                'details' => $details,
                'category_hindi' => $category_hindi,
                'details_hindi' => $details_hindi,
                'image' => $image
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
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
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
      $Where = "";
      if($category_id == "")
      {
          $Where = ' CONCAT(",", doctor_list.category, ",") REGEXP ",(23|25|26|78|109|239|240),"';
      }
      else
      {
          $Where = " FIND_IN_SET('" . $category_id . "', doctor_list.category) ";
      }
      //echo "SELECT doctor_list.mba,doctor_list.consultaion_video,doctor_list.consultaion_chat,doctor_list.consultation_voice_call,doctor_list.recommended,doctor_list.certified,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE $Where  and  doctor_list.is_active = 1  HAVING distance < '%s' ORDER BY distance  LIMIT $startk, $limitk";
       if($keyword!=""){
        $sql   = sprintf("SELECT doctor_list.mba,doctor_list.consultaion_video,doctor_list.consultaion_chat,doctor_list.consultation_voice_call,doctor_list.recommended,doctor_list.certified,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE $Where and  doctor_list.doctor_name LIKE '%%$keyword%%' and  doctor_list.is_active = 1  HAVING distance < '%s' ORDER BY distance  LIMIT $startk, $limitk", ($mlat), ($mlng), ($mlat), ($radius));
       }else{
         $sql   = sprintf("SELECT doctor_list.mba,doctor_list.consultaion_video,doctor_list.consultaion_chat,doctor_list.consultation_voice_call,doctor_list.recommended,doctor_list.certified,doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_list.discount,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians($mlat) ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians($mlng) ) + sin( radians($mlat) ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE $Where and doctor_list.is_active = 1  HAVING distance < '%s' ORDER BY distance  LIMIT $start, $limit", ($mlat), ($mlng), ($mlat), ($radius));
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
    //------------------------------------------new------------------------------------
    public function dental_clinic_services($user_id)
     {
           $sql = sprintf("SELECT id,main_service from sabka_dentist_main_service_category");
           $query = $this->db->query($sql);
           $count = $query->num_rows();
           if($count > 0)
               {
                   foreach($query->result_array() as $row)
                   {
                        $service_id = $row['id'];
                        $main_service = $row['main_service'];
                        $sub_category_details = array();
                         $sql_details = sprintf("SELECT id,service_name,price,type from sabka_dentist_services where type = '$main_service'");
                         $query_details = $this->db->query($sql_details);
                         $count_details = $query_details->num_rows();
                         if($count_details > 0)
                         {
                             foreach($query_details->result_array() as $rowd)
                             {
                                 $sub_cat_id = $rowd['id'];
                                 $sub_cat_name = $rowd['service_name'];
                                 $price    = $rowd['price'];
                                 $cut_price = $price * 0.2;
                                 $discount_price = $price - $cut_price;
                                 
                                 $sub_category_details[] = array(
                                            'sub_cat_id' => $sub_cat_id,
                                            'service_name' => $sub_cat_name,
                                            'price' => $price,
                                            'discount_price' => $discount_price
                                     );
                             }
                             
                         }
                         else
                         {
                             $sub_category_details = array();
                         }
                         
                        $result_array[] = array(
                                   'cat_id'   => $service_id,
                                   'cat_name' => $main_service,
                                   'sub_catdata' => $sub_category_details
                            
                            );
                         
                   }
                   
                   return $result_array;
               }
               
               return array();
     }
     
     
    public function dental_clinic_list($user_id,$lat,$lng,$type)
    {
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
          
           if($type == 'one')
           {
                $sql = sprintf("SELECT id,hub_user_id,dentists_branch_user_id, name_of_hospital,about_us, establishment_year, speciality, address, address_2, pincode, phone, city, state, 	email, 	lat, lng, image, rating, review, medicalwale_discount,opening_hours, description,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1' AND hub_user_id='33586'  HAVING distance < '5' ORDER BY distance LIMIT 1", ($lat), ($lng), ($lat), ($radius));
           $query = $this->db->query($sql);
           $count = $query->num_rows();
           }
           else
           {
           $sql = sprintf("SELECT id,hub_user_id,dentists_branch_user_id, name_of_hospital,about_us, establishment_year, speciality, address, address_2, pincode, phone, city, state, 	email, 	lat, lng, image, rating, review, medicalwale_discount,opening_hours, description,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1' AND hub_user_id='33586'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($lat), ($lng), ($lat), ($radius));
           $query = $this->db->query($sql);
           $count = $query->num_rows();
           }
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
                   $rating                  = $row['rating'];
                   $review                  = $row['review'];
                   $user_discount           = $row['medicalwale_discount'];
                   $discount_description    = $row['description'];
                   $opening_hours     = $row['opening_hours'];
                   
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
                
                     if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
                
                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $hub_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $hub_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $hub_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                
                  //Review Count
                $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$hub_user_id");
                $Review_count = $Review_query->num_rows();
                
                
                //Profile View
                //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$hub_user_id and vendor_type='8'");
                $Profile_count = $Profile_query->num_rows();
                
                
                    if($user_discount == null || $user_discount == '' || $user_discount == NULL)
                    {
                        $user_discount = '0';
                    }
                   
                   if($rating == null || $rating == '' || $rating == NULL)
                    {
                        $rating = '4';
                    }
                    
                    
                    
                      $speciallity_array = array();
                      $speciallity_data = explode(',', $speciality);
                foreach ($speciallity_data as $speciallity_data) {
                    
                    if($speciallity_data != ""){
                        if(preg_match('/[0-9]/', $speciallity_data)){
                            $Query = $this->db->query("SELECT name FROM dentists_specialist WHERE id='$speciallity_data'");
                              $count = $Query->num_rows();
                            if($count>0)
                            {
                            $what_we_offer_value = $Query->row()->name;
                            $speciallity_array[] = $what_we_offer_value;
                            }
                            else
                            {
                                $speciallity_array[] = $speciallity_data;
                            }
                        }else{
                            $speciallity_array[] = $speciallity_data;
                        }
                        
                    }
                    
                }
                    
                   
                $result_data[] = array(
                       'branch_id'        => $branch_id,
                       'hub_user_id'      => $hub_user_id,
                       'listing_id'       => $dentists_branch_user_id,
                       'listing_type'     => '39',
                       'name_of_clinic'   => $name_of_hospital,
                       'about_us'         => $about_us,
                       'establishment_year' => $establishment_year,
                       'speciality'       => $speciallity_array,
                       'address'          => $address,
                       'address_2'        => $address_2,
                       'pincode'          => $pincode,
                       'phone'            => $phone,
                       'city'             => $city,
                       'state'            => $state,
                       'email'            => $email,
                       'lat'              => $lat,
                       'lng'              => $lng,
                       'image'            => $image,
                       'opening_day'      => $final_Day,
                       'rating'           => $rating,
                       'review'           => '0',
                       'followers' => $followers,
                        'following' => $following,
                        'profile_views' => $Profile_count,
                        'is_follow' => $is_follow,
                       'user_discount'    => $user_discount,
                       'discount_description'  => $discount_description
                       );
               }
           }
           else
           {
               return $result_data = array();
           }
           
           return $result_data;
     }
    public function dental_prescription_booking($user_id, $lat, $lng, $listing_id, $address_line1, $address_line2, $city, $state, $pincode, $user_name, $mobile, $email, $gender, $status, $payment_mode, $booking_date, $booking_location, $booking_address, $booking_mobile, $patient_id, $booking_id, $trail_booking_date, $trail_booking_time){
        date_default_timezone_set('Asia/Kolkata');
        $created_at   = date('Y-m-d H:i:s');
        //$joining_date = date('Y-m-d');
        if($patient_id == '')
        {
            $patient_id = '0';
        }
       
        $query        = $this->db->query("SELECT * FROM booking_master WHERE booking_id = '$booking_id'");
        //echo "SELECT * FROM booking_master WHERE booking_id = '$booking_id'";
        $count        = $query->num_rows();
        
        
        if ($count > 0) {
          //  echo 'ndsfsdfhjdsfhjds';
            $lab_booking = array(
                'user_id' => $user_id,
                'patient_id'            => $patient_id,
                'booking_id'            => $booking_id,
                'package_id'            => 0,
                'listing_id'            => $listing_id,
                'user_name'             => $user_name,
                'user_email'            => $email,
                'user_mobile'           => $mobile,
                'user_gender'           => $gender,
                'branch_id'             => 0,
                'vendor_id'             => 39,
                'booking_date'          => $created_at,
                'status'                => $status,
                'trail_booking_date'    => $trail_booking_date,
                'trail_booking_time'    => $trail_booking_time,
                'payment_mode'          => $payment_mode,
                'joining_date'          => $booking_date,
                'booking_location'      => $booking_location,
                'booking_address'       => $booking_address,
                'booking_mobile'        => $booking_mobile,
                'booking_type'          => "prescription"
            );
            $this->db->where('booking_id', $booking_id);
            $rst = $this->db->update('booking_master', $lab_booking);
        //   $appointment_id = $booking_id;
            
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
            $msg                  = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent,$booking_id,$listing_id,$user_id);
            }
            //end
            
        //      //added for mail to vendor 
        //     $vendor_plike = $this->db->query("SELECT email,name FROM users WHERE id='$listing_id'");
        //      $getvdr               = $vendor_plike->row_array();
        //     $vdr_email             = $getvdr['email'];
        //     $vdr_name              = $getvdr['name'];
        //     $this->dental_booking_sendmail($vdr_email, $usr_name, $booking_id, $vdr_name);
            
        //     ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
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
         
        //     /////////*******************************************excotel text message vendor ***************************************************
            $vendor_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
          //  $vendor_phone = $vendor_info->phone;
            $vendor_phone = '9890828800'; // temp added for testing by zak
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ', Dental Appointment has been booked by patient :' . $user_id . 'successfully and Booking Id :' . $booking_id . ' for future reference.';
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
            
            
        //       /////////*******************************************excotel text message to hub for sabka dentist***************************************************
        //     $hub_id = '33586';    //hub id for sabka dentist need to send text message when package book to any branch
        //     $hub_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $hub_id)->get()->row();
        //     $token_status = $hub_info->token_status;
        //   //  $hub_phone = $hub_info->phone;
        //      $hub_phone = '9890828800'; // temp added for testing by zak
        //     $hub_name  = $hub_info->name;
        //     $message      = $vendor_name . ',your Dental Appointment has been booked by patient :' . $user_name . ' successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $post_data    = array(
        //         'From' => '02233721563',
        //         'To' => $hub_phone,
        //         'Body' => $message
        //     );
        //     $exotel_sid   = "aegishealthsolutions";
        //     $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
        //     $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
        //     $ch           = curl_init();
        //     curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_POST, 1);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        //     $http_result = curl_exec($ch);
        //     curl_close($ch);
            
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id,
                    'trail_booking_date' => $trail_booking_date,
                    'trail_booking_time' => $trail_booking_time
                );
            }
        } else {
         //  echo 'insert';
            $lab_booking = array(
                'user_id'               => $user_id,
                'patient_id'            => $patient_id,
                'booking_id'            => $booking_id,
                'package_id'            => 0,
                'listing_id'            => $listing_id,
                'user_name'             => $user_name,
                'user_email'            => $email,
                'user_mobile'           => $mobile,
                'user_gender'           => $gender,
                'branch_id'             => 0,
                'vendor_id'             => 39,
                'booking_date'          => $created_at,
                'status'                => $status,
                'trail_booking_date'    => $trail_booking_date,
                'trail_booking_time'    => $trail_booking_time,
                'payment_mode'          => $payment_mode,
                'joining_date'          => $booking_date,
                'booking_location'      => $booking_location,
                'booking_address'       => $booking_address,
                'booking_mobile'        => $booking_mobile,
                'booking_type'          => "prescription"
            );
            $rst         = $this->db->insert('booking_master', $lab_booking);
            $appointment_id = $this->db->insert_id();
          
            $this->insert_notification_post_question_prescription($user_id, $appointment_id,$listing_id);
            
            
            
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
            $msg                  = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent,$booking_id,$listing_id,$user_id);
            }
            //end
            
        //      //added for mail to vendor 
        //     $vendor_plike = $this->db->query("SELECT email,name FROM users WHERE id='$listing_id'");
        //      $getvdr               = $vendor_plike->row_array();
        //     $vdr_email             = $getvdr['email'];
        //     $vdr_name              = $getvdr['name'];
        //     $this->dental_booking_sendmail($vdr_email, $usr_name, $booking_id, $vdr_name);
            
        //     ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
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
         
        //     /////////*******************************************excotel text message vendor ***************************************************
            $vendor_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
            $vendor_phone = $vendor_info->phone;
            //$vendor_phone = '9890828800'; // temp added for testing by zak
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ', Dental Appointment has been booked by patient : ' . $user_name . 'successfully and Booking Id :' . $booking_id . ' for future reference.';
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
            
            
        //       /////////*******************************************excotel text message to hub for sabka dentist***************************************************
        //     $hub_id = '33586';    //hub id for sabka dentist need to send text message when package book to any branch
        //     $hub_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $hub_id)->get()->row();
        //     $token_status = $hub_info->token_status;
        //   //  $hub_phone = $hub_info->phone;
        //     $hub_phone  = '9890828800'; // temp added for testing by zak
        //     $hub_name  = $hub_info->name;
        //     $message      = $vendor_name . ',your Dental Appointment has been booked by patient :' . $user_name . ' successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $post_data    = array(
        //         'From' => '02233721563',
        //         'To' => $hub_phone,
        //         'Body' => $message
        //     );
        //     $exotel_sid   = "aegishealthsolutions";
        //     $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
        //     $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
        //     $ch           = curl_init();
        //     curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_POST, 1);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        //     $http_result = curl_exec($ch);
        //     curl_close($ch);
            
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id,
                    'order_id' => $appointment_id,
                    'trail_booking_date' => $trail_booking_date,
                    'trail_booking_time' => $trail_booking_time
                );
            }
        }
    }
    
    public function dental_freeconsultancy_booking($user_id, $lat, $lng, $listing_id, $address_line1, $address_line2, $city, $state, $pincode, $user_name, $mobile, $email, $gender, $status, $payment_mode, $booking_date, $booking_location, $booking_address, $booking_mobile, $patient_id, $booking_id, $trail_booking_date, $trail_booking_time,$package_id,$type,$sub_type){
        date_default_timezone_set('Asia/Kolkata');
        $created_at   = date('Y-m-d H:i:s');
        //$joining_date = date('Y-m-d');
        if($patient_id == '')
        {
            $patient_id = '0';
        }
       
        $query        = $this->db->query("SELECT * FROM booking_master WHERE booking_id = '$booking_id'");
        //echo "SELECT * FROM booking_master WHERE booking_id = '$booking_id'";
        $count        = $query->num_rows();
        
        
        if ($count > 0) {
          //  echo 'ndsfsdfhjdsfhjds';
            $lab_booking = array(
                'user_id' => $user_id,
                'patient_id'            => $patient_id,
                'booking_id'            => $booking_id,
                'package_id'            => 0,
                'listing_id'            => $listing_id,
                'user_name'             => $user_name,
                'user_email'            => $email,
                'user_mobile'           => $mobile,
                'user_gender'           => $gender,
                'branch_id'             => 0,
                'vendor_id'             => 39,
                'booking_date'          => $created_at,
                'status'                => $status,
                'trail_booking_date'    => $trail_booking_date,
                'trail_booking_time'    => $trail_booking_time,
                'payment_mode'          => $payment_mode,
                'joining_date'          => $booking_date,
                'booking_location'      => $booking_location,
                'booking_address'       => $booking_address,
                'booking_mobile'        => $booking_mobile,
                'package_id'        => $package_id,
                'booking_type'          => $type,
                'sub_booking_type'          => $sub_type
            );
            $this->db->where('booking_id', $booking_id);
            $rst = $this->db->update('booking_master', $lab_booking);
        //   $appointment_id = $booking_id;
            
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
            $msg                  = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent,$booking_id,$listing_id,$user_id);
            }
            //end
            
        //      //added for mail to vendor 
        //     $vendor_plike = $this->db->query("SELECT email,name FROM users WHERE id='$listing_id'");
        //      $getvdr               = $vendor_plike->row_array();
        //     $vdr_email             = $getvdr['email'];
        //     $vdr_name              = $getvdr['name'];
        //     $this->dental_booking_sendmail($vdr_email, $usr_name, $booking_id, $vdr_name);
            
        //     ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
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
         
        //     /////////*******************************************excotel text message vendor ***************************************************
            $vendor_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
          //  $vendor_phone = $vendor_info->phone;
            $vendor_phone = '9890828800'; // temp added for testing by zak
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ', Dental Appointment has been booked by patient :' . $user_id . 'successfully and Booking Id :' . $booking_id . ' for future reference.';
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
            
            
        //       /////////*******************************************excotel text message to hub for sabka dentist***************************************************
        //     $hub_id = '33586';    //hub id for sabka dentist need to send text message when package book to any branch
        //     $hub_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $hub_id)->get()->row();
        //     $token_status = $hub_info->token_status;
        //   //  $hub_phone = $hub_info->phone;
        //      $hub_phone = '9890828800'; // temp added for testing by zak
        //     $hub_name  = $hub_info->name;
        //     $message      = $vendor_name . ',your Dental Appointment has been booked by patient :' . $user_name . ' successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $post_data    = array(
        //         'From' => '02233721563',
        //         'To' => $hub_phone,
        //         'Body' => $message
        //     );
        //     $exotel_sid   = "aegishealthsolutions";
        //     $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
        //     $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
        //     $ch           = curl_init();
        //     curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_POST, 1);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        //     $http_result = curl_exec($ch);
        //     curl_close($ch);
            
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id,
                    'trail_booking_date' => $trail_booking_date,
                    'trail_booking_time' => $trail_booking_time
                );
            }
        } else {
         //  echo 'insert';
            $lab_booking = array(
                'user_id'               => $user_id,
                'patient_id'            => $patient_id,
                'booking_id'            => $booking_id,
                'package_id'            => 0,
                'listing_id'            => $listing_id,
                'user_name'             => $user_name,
                'user_email'            => $email,
                'user_mobile'           => $mobile,
                'user_gender'           => $gender,
                'branch_id'             => 0,
                'vendor_id'             => 39,
                'booking_date'          => $created_at,
                'status'                => $status,
                'trail_booking_date'    => $trail_booking_date,
                'trail_booking_time'    => $trail_booking_time,
                'payment_mode'          => $payment_mode,
                'joining_date'          => $booking_date,
                'booking_location'      => $booking_location,
                'booking_address'       => $booking_address,
                'booking_mobile'        => $booking_mobile,
                'package_id'            => $package_id,
                'booking_type'          => $type,
                'sub_booking_type'          => $sub_type
            );
            $rst         = $this->db->insert('booking_master', $lab_booking);
            $appointment_id = $this->db->insert_id();
          
            $this->insert_notification_post_question_prescription($user_id, $appointment_id,$listing_id);
            
            
            
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
            $msg                  = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent,$booking_id,$listing_id,$user_id);
            }
            //end
            
        //      //added for mail to vendor 
        //     $vendor_plike = $this->db->query("SELECT email,name FROM users WHERE id='$listing_id'");
        //      $getvdr               = $vendor_plike->row_array();
        //     $vdr_email             = $getvdr['email'];
        //     $vdr_name              = $getvdr['name'];
        //     $this->dental_booking_sendmail($vdr_email, $usr_name, $booking_id, $vdr_name);
            
        //     ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
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
         
        //     /////////*******************************************excotel text message vendor ***************************************************
            $vendor_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
            $vendor_phone = $vendor_info->phone;
            //$vendor_phone = '9890828800'; // temp added for testing by zak
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ', Dental Appointment has been booked by patient : ' . $user_name . 'successfully and Booking Id :' . $booking_id . ' for future reference.';
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
            
            
        //       /////////*******************************************excotel text message to hub for sabka dentist***************************************************
        //     $hub_id = '33586';    //hub id for sabka dentist need to send text message when package book to any branch
        //     $hub_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $hub_id)->get()->row();
        //     $token_status = $hub_info->token_status;
        //   //  $hub_phone = $hub_info->phone;
        //     $hub_phone  = '9890828800'; // temp added for testing by zak
        //     $hub_name  = $hub_info->name;
        //     $message      = $vendor_name . ',your Dental Appointment has been booked by patient :' . $user_name . ' successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $post_data    = array(
        //         'From' => '02233721563',
        //         'To' => $hub_phone,
        //         'Body' => $message
        //     );
        //     $exotel_sid   = "aegishealthsolutions";
        //     $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
        //     $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
        //     $ch           = curl_init();
        //     curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_POST, 1);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        //     $http_result = curl_exec($ch);
        //     curl_close($ch);
            
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id,
                    'order_id' => $appointment_id,
                    'trail_booking_date' => $trail_booking_date,
                    'trail_booking_time' => $trail_booking_time
                );
            }
        }
    }
    
    public function dentist_time_slot($user_id)
    {
        $timings = array();
        $sql = "SELECT opening_hours FROM dentists_clinic_list  WHERE user_id = '$user_id'";
        
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
           $row = $query->row_array();
           $opening_hours = $row['opening_hours']; 
           
             $eachDays = explode('|',$opening_hours);
              $slots = array();
              
                foreach($eachDays as $days){
                   $time_slots = array();
                   $dailyInfo = explode('>',$days);
                   
                   $t['day'] = $dailyInfo[0];
                   $times = $dailyInfo[1];
                   
                   $time = explode('-',$times);
                   
                   $t['start_time'] = $time[0];
                   $t['end_time'] = $time[1];
                   
                   
                   
                          $StarttotalTime = $time[0];
                          $EndtotalTime = $time[1];
                          $stm = explode(',',$StarttotalTime);
                          $etm = explode(',',$EndtotalTime);
                          $start = $end = "";
                          $startTime = "0";
                          
                          for($i=0;$i<sizeof($stm);$i++){
                                if($i!=0){
                                    $start .= ", " ;
                                }
                                $start .= $stm[$i] ." - ". $etm[$i];
                                
                                // slots
                            
                                $s_slot = $stm[$i]; 
                                $e_slot = $etm[$i]; 
                            
                            if($s_slot != $e_slot){
                                $startTime = date("H:i", strtotime($s_slot));
                                $endTim = date("H:i", strtotime($e_slot));
                                  
                                while($startTime < $endTim){
                                    $slot = strtotime($stm[$i]); 
                                   
                                        $slots['time_slot']  =  date("g:i A", strtotime($startTime));
                                        $time_slots[] = $slots;
                                    $startTime = strtotime($startTime);
                                    $startTime = date("H:i", strtotime('+30 minutes', $startTime));
                                     
                                }
                                
                            } else {
                                $startTime = date("H:i", strtotime($s_slot));
                                $e_slot = strtotime($e_slot);
                                $endTim = date("H:i", strtotime('-1 minutes', $e_slot));
                               
                           $i = 0;    
                                  
                                while($startTime < $endTim && $i < 48){
                                    
                                     
                                    $slot = strtotime($s_slot); 
                                   
                                        $slots['time_slot']  =  date("g:i A", strtotime($startTime));
                                        $time_slots[] = $slots;
                                    $startTime = strtotime($startTime);
                                    $startTime = date("H:i", strtotime('+30 minutes', $startTime));
                                    

                                 
                               
                                     $i++;
                                }
                             
                            }
                                
                                
                                
                                
                          } 
                          
                            $t['time'] = $start;
                            $t['time_slots'] = $time_slots;
                                
                   
                   $timings[] = $t; 
                //   $time_slots = $slots;
               
               }
               
        }
        else
        {
              $sql = "SELECT opening_hours FROM dentists_branch  WHERE dentists_branch_user_id = '$user_id'";
        
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            if ($count > 0) {
           $row = $query->row_array();
           $opening_hours = $row['opening_hours']; 
           
             $eachDays = explode('|',$opening_hours);
              $slots = array();
              
                foreach($eachDays as $days){
                   $time_slots = array();
                   $dailyInfo = explode('>',$days);
                   
                   $t['day'] = $dailyInfo[0];
                   $times = $dailyInfo[1];
                   
                   $time = explode('-',$times);
                   
                   $t['start_time'] = $time[0];
                   $t['end_time'] = $time[1];
                   
                   
                   
                          $StarttotalTime = $time[0];
                          $EndtotalTime = $time[1];
                          $stm = explode(',',$StarttotalTime);
                          $etm = explode(',',$EndtotalTime);
                          $start = $end = "";
                          $startTime = "0";
                          
                          for($i=0;$i<sizeof($stm);$i++){
                                if($i!=0){
                                    $start .= ", " ;
                                }
                                $start .= $stm[$i] ." - ". $etm[$i];
                                
                                // slots
                            
                                $s_slot = $stm[$i]; 
                                $e_slot = $etm[$i]; 
                            
                            if($s_slot != $e_slot){
                                $startTime = date("H:i", strtotime($s_slot));
                                $endTim = date("H:i", strtotime($e_slot));
                                  
                                while($startTime < $endTim){
                                    $slot = strtotime($stm[$i]); 
                                   
                                        $slots['time_slot']  =  date("g:i A", strtotime($startTime));
                                        $time_slots[] = $slots;
                                    $startTime = strtotime($startTime);
                                    $startTime = date("H:i", strtotime('+30 minutes', $startTime));
                                     
                                }
                                
                            } else {
                                $startTime = date("H:i", strtotime($s_slot));
                                $e_slot = strtotime($e_slot);
                                $endTim = date("H:i", strtotime('-1 minutes', $e_slot));
                               
                           $i = 0;    
                                  
                                while($startTime < $endTim && $i < 48){
                                    
                                     
                                    $slot = strtotime($s_slot); 
                                   
                                        $slots['time_slot']  =  date("g:i A", strtotime($startTime));
                                        $time_slots[] = $slots;
                                    $startTime = strtotime($startTime);
                                    $startTime = date("H:i", strtotime('+30 minutes', $startTime));
                                    

                                 
                               
                                     $i++;
                                }
                             
                            }
                                
                                
                                
                                
                          } 
                          
                            $t['time'] = $start;
                            $t['time_slots'] = $time_slots;
                                
                   
                   $timings[] = $t; 
                //   $time_slots = $slots;
               
               }
               
            }
        }
        return $timings;
    } 
    public function dental_booking($user_id, $listing_id, $address_line1, $address_line2, $package_id,$user_name, $mobile, $email, $gender, $branch_id, $branch_name, $vendor_id, $status, $payment_mode, $trail_booking_date, $trail_booking_time, $joining_date, $booking_location, $booking_address, $booking_mobile, $city, $state, $pincode, $booking_id, $patient_id){
        date_default_timezone_set('Asia/Kolkata');
        $created_at   = date('Y-m-d H:i:s');
        //$joining_date = date('Y-m-d');
        if($patient_id == '')
        {
            $patient_id = '0';
        }
        
        // if($booking_id == '')
        // {
        //     $booking_id = 'zero';
        // }
        
        $query        = $this->db->query("SELECT * FROM booking_master WHERE booking_id = '$booking_id'");
        //echo "SELECT * FROM booking_master WHERE booking_id = '$booking_id'";
        $count        = $query->num_rows();
        
        
        if ($count > 0) {
          //  echo 'ndsfsdfhjdsfhjds';
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
            $this->db->where('booking_id', $booking_id);
            $rst = $this->db->update('booking_master', $lab_booking);
        //   $appointment_id = $booking_id;
            
            //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
        //     $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
        //     $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
        //     if ($img_count > 0) {
        //         $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
        //         $img_file      = $profile_query->source;
        //         $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
        //     } else {
        //         $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
        //     }
        //     $getusr               = $user_plike->row_array();
        //     $usr_name             = $getusr['name'];
        //     $msg                  = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
        //     $title                = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $customer_token_count = $customer_token->num_rows();
        //     if ($customer_token_count > 0) {
        //         $token_status = $customer_token->row_array();
        //         $agent        = $token_status['agent'];
        //         $reg_id       = $token_status['token'];
        //         $img_url      = $userimage;
        //         $tag          = 'text';
        //         $key_count    = '1';
        //         $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
        //     }
        //     //end
            
        //      //added for mail to vendor 
        //     $vendor_plike = $this->db->query("SELECT email,name FROM users WHERE id='$listing_id'");
        //      $getvdr               = $vendor_plike->row_array();
        //     $vdr_email             = $getvdr['email'];
        //     $vdr_name              = $getvdr['name'];
        //     $this->dental_booking_sendmail($vdr_email, $usr_name, $booking_id, $vdr_name);
            
        //     ////////////////////***********************************excotel text message user****************************************** 
        //     $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
        //     $token_status = $user_info->token_status;
        //     $user_phone   = $user_info->phone;
        //     $user_name    = $user_info->name;
        //     $message      = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $post_data    = array(
        //         'From' => '02233721563',
        //         'To' => $user_phone,
        //         'Body' => $message
        //     );
        //     $exotel_sid   = "aegishealthsolutions";
        //     $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
        //     $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
        //     $ch           = curl_init();
        //     curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_POST, 1);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        //     $http_result = curl_exec($ch);
        //     curl_close($ch);
         
        //     /////////*******************************************excotel text message vendor ***************************************************
        //     $vendor_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $listing_id)->get()->row();
        //     $token_status = $vendor_info->token_status;
        //   //  $vendor_phone = $vendor_info->phone;
        //     $vendor_phone = '9890828800'; // temp added for testing by zak
        //     $vendor_name  = $vendor_info->name;
        //     $message      = $vendor_name . ', Dental Appointment has been booked by patient :' . $user_id . 'successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $post_data    = array(
        //         'From' => '02233721563',
        //         'To' => $vendor_phone,
        //         'Body' => $message
        //     );
        //     $exotel_sid   = "aegishealthsolutions";
        //     $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
        //     $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
        //     $ch           = curl_init();
        //     curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_POST, 1);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        //     $http_result = curl_exec($ch);
        //     curl_close($ch);
            
            
        //       /////////*******************************************excotel text message to hub for sabka dentist***************************************************
        //     $hub_id = '33586';    //hub id for sabka dentist need to send text message when package book to any branch
        //     $hub_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $hub_id)->get()->row();
        //     $token_status = $hub_info->token_status;
        //   //  $hub_phone = $hub_info->phone;
        //      $hub_phone = '9890828800'; // temp added for testing by zak
        //     $hub_name  = $hub_info->name;
        //     $message      = $vendor_name . ',your Dental Appointment has been booked by patient :' . $user_name . ' successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $post_data    = array(
        //         'From' => '02233721563',
        //         'To' => $hub_phone,
        //         'Body' => $message
        //     );
        //     $exotel_sid   = "aegishealthsolutions";
        //     $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
        //     $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
        //     $ch           = curl_init();
        //     curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_POST, 1);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        //     $http_result = curl_exec($ch);
        //     curl_close($ch);
            
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id,
                    'trail_booking_date' => $trail_booking_date,
                    'trail_booking_time' => $trail_booking_time
                );
            }
        } else {
         //  echo 'insert';
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
          
            $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
            
            
            
            //////// notification for gcm ***********************************************************************
        //     //added by jakir on 01-june-2018 for notification on activation 
        //     $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
        //     $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
        //     if ($img_count > 0) {
        //         $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
        //         $img_file      = $profile_query->source;
        //         $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
        //     } else {
        //         $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
        //     }
        //     $getusr               = $user_plike->row_array();
        //     $usr_name             = $getusr['name'];
        //     $msg                  = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
        //     $title                = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $customer_token_count = $customer_token->num_rows();
        //     if ($customer_token_count > 0) {
        //         $token_status = $customer_token->row_array();
        //         $agent        = $token_status['agent'];
        //         $reg_id       = $token_status['token'];
        //         $img_url      = $userimage;
        //         $tag          = 'text';
        //         $key_count    = '1';
        //         $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
        //     }
        //     //end
            
        //     //added for mail to vendor 
        //     $vendor_plike = $this->db->query("SELECT email,name FROM users WHERE id='$listing_id'");
        //      $getvdr               = $vendor_plike->row_array();
        //     $vdr_email             = $getvdr['email'];
        //     $vdr_name              = $getvdr['name'];
        //     $this->dental_booking_sendmail($vdr_email, $usr_name, $booking_id, $vdr_name);
            
        //     ////////////////////***********************************excotel text message user****************************************** 
        //     $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
        //     $token_status = $user_info->token_status;
        //     $user_phone   = $user_info->phone;
        //     $user_name    = $user_info->name;
        //     $message      = $user_name . ',your Dental Appointment has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $post_data    = array(
        //         'From' => '02233721563',
        //         'To' => $user_phone,
        //         'Body' => $message
        //     );
        //     $exotel_sid   = "aegishealthsolutions";
        //     $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
        //     $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
        //     $ch           = curl_init();
        //     curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_POST, 1);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        //     $http_result = curl_exec($ch);
        //     curl_close($ch);
        //     /////////*******************************************excotel text message vendor ***************************************************
        //     $vendor_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $listing_id)->get()->row();
        //     $token_status = $vendor_info->token_status;
        //     //$vendor_phone = $vendor_info->phone;
        //     $vendor_phone  = '9890828800'; // temp added for testing by zak
        //     $vendor_name  = $vendor_info->name;
        //     $message      = $vendor_name . ',your Dental Appointment has been booked by patient :' . $user_name . ' successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $post_data    = array(
        //         'From' => '02233721563',
        //         'To' => $vendor_phone,
        //         'Body' => $message
        //     );
        //     $exotel_sid   = "aegishealthsolutions";
        //     $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
        //     $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
        //     $ch           = curl_init();
        //     curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_POST, 1);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        //     $http_result = curl_exec($ch);
        //     curl_close($ch);
            
        //       /////////*******************************************excotel text message to hub for sabka dentist***************************************************
        //     $hub_id = '33586';    //hub id for sabka dentist need to send text message when package book to any branch
        //     $hub_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $hub_id)->get()->row();
        //     $token_status = $hub_info->token_status;
        //   //  $hub_phone = $hub_info->phone;
        //     $hub_phone  = '9890828800'; // temp added for testing by zak
        //     $hub_name  = $hub_info->name;
        //     $message      = $vendor_name . ',your Dental Appointment has been booked by patient :' . $user_name . ' successfully and Booking Id :' . $booking_id . ' for future reference.';
        //     $post_data    = array(
        //         'From' => '02233721563',
        //         'To' => $hub_phone,
        //         'Body' => $message
        //     );
        //     $exotel_sid   = "aegishealthsolutions";
        //     $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
        //     $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
        //     $ch           = curl_init();
        //     curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //     curl_setopt($ch, CURLOPT_URL, $url);
        //     curl_setopt($ch, CURLOPT_POST, 1);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //     curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        //     $http_result = curl_exec($ch);
        //     curl_close($ch);
            
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id,
                    'trail_booking_date' => $trail_booking_date,
                    'trail_booking_time' => $trail_booking_time
                );
            }
        }
    }
    
    
     public function dental_prescription_add($user_id, $lat, $lng) {
        date_default_timezone_set('Asia/Kolkata');
        $order_date = date('Y-m-d H:i:s');
        $invoice_no = date("YmdHis");
        $order_status = 'Awaiting Confirmation';
        $action_by = 'customer';

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
        
           $sql = sprintf("SELECT id,hub_user_id,dentists_branch_user_id, name_of_hospital,about_us, establishment_year, speciality, address, address_2, pincode, phone, city, state, 	email, 	lat, lng, image, rating, review, medicalwale_discount,opening_hours, description,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1'  HAVING distance < '5' ORDER BY distance LIMIT 1", ($lat), ($lng), ($lat), ($radius));
           $query = $this->db->query($sql);
           $count = $query->num_rows();
        
        foreach ($query->result_array() as $row) {
            $listing_id = $row['dentists_branch_user_id'];
            $listing_name = $row['name_of_hospital'];
        }

        
        // define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        // function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent) {
        //     $fields = array(
        //         'to' => $reg_id,
        //         'priority' => "high",
        //         $agent === 'android' ? 'data' : 'notification' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
        //     );
        //     if ($key_count == '1') {
        //         $headers = array(
        //             GOOGLE_GCM_URL,
        //             'Content-Type: application/json',
        //             $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        //         );
        //     }
        //     if ($key_count == '2') {
        //         $headers = array(
        //             GOOGLE_GCM_URL,
        //             'Content-Type: application/json',
        //             $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        //         );
        //     }
        //     $ch = curl_init();
        //     curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        //     curl_setopt($ch, CURLOPT_POST, true);
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        //     $result = curl_exec($ch);
        //     if ($result === FALSE) {
        //         die('Problem occurred: ' . curl_error($ch));
        //     }
        //     curl_close($ch);
        // }
        // if ($listing_id > 0) {
        
            
        //     $order_date = date('j M Y h:i A', strtotime($order_date));
        //     $order_info = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
        //     $token_status = $order_info->token_status;
        //     if ($token_status > 0) {
        //         $reg_id = $order_info->token;
        //         $agent = $order_info->agent;
        //         $msg = 'Thanks uploading your prescription with ' . $listing_name;
        //         $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
        //         $tag = 'text';
        //         $key_count = '1';
        //         $title = 'Order Placed';
                
                
      
                
        //       //  send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
        //     }
           
        //     $partner_info = $this->db->query('SELECT token, agent, token_status,phone from users where id= '.$listing_id);
        //     $partner_token_status = $partner_info->num_rows();
        //     $partner_info = $partner_info->row();
        //     if ($partner_token_status > 0) {
        //         $partner_token_status = $partner_info->token_status;
        //         $partner_phone = $partner_info->phone;
        //         $reg_id = $partner_info->token;
        //         $agent = $partner_info->agent;
        //         $msg = 'You Have Received a New Prescription Order';
        //         $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
        //         $tag = 'text';
        //         $key_count = '2';
        //         $title = 'New Order';
        //         //web notification starts
        //         $pharmacy_notifications = array(
        //             'listing_id' => $listing_id,
        //             'order_id' => $order_id,
        //             'title' => $title,
        //             'msg' => $msg,
        //             'image' => $img_url,
        //             'notification_type' => 'prescription',
        //             'order_status' => $order_status,
        //             'order_date' => $order_date,
        //             'invoice_no' => $invoice_no
        //         );
        //         $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
        //         //web notification ends
                
                
                
                
        //         send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
            
        //         //sms same as order
        //         $message = 'You have received an order, kindly login to https://pharmacy.medicalwale.com';
        //         $post_data = array('From' => '02233721563', 'To' => $partner_phone, 'Body' => $message);
        //         $exotel_sid = "aegishealthsolutions";
        //         $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
        //         $url = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
        //         $ch = curl_init();
        //         curl_setopt($ch, CURLOPT_VERBOSE, 1);
        //         curl_setopt($ch, CURLOPT_URL, $url);
        //         curl_setopt($ch, CURLOPT_POST, 1);
        //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //         curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //         curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        //         $http_result = curl_exec($ch);
        //         curl_close($ch);
        //         //sms same to nyla,abdul, zaheer
        //         $message2 = 'There is new order in pharmacy store. Pharmacy Name-' . $listing_name . ', Pharmacy Mobile- ' . $mobile . ', Order Id-' . $order_id . ', Order Date-' . $order_date . '.';
        //         $post_data2 = array('From' => '02233721563', 'To' => '9619294702,7506908285', 'Body' => $message2);
        //         $exotel_sid2 = "aegishealthsolutions";
        //         $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
        //         $url2 = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
        //         $ch2 = curl_init();
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
        // }
        return $listing_id;
    }
    
  
    public function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent,$booking_id,$listing_id,$user_id){
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
                "notification_type" => 'dental_appointment_booking',
                "notification_date" => $date,
                "booking_id" => $booking_id,
                "listing_id" => $listing_id,
                "user_id" => $user_id
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,   
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );  
        $ch      = curl_init();
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
    
    public function insert_notification_post_question($user_id, $booking_id, $doctor_id){
      $data = array(
                           'user_id'        => $user_id,
                           'post_id'         => $booking_id,
                           'timeline_id'   => $user_id,
                           'type'          => 'Appointment',
                           'seen'          => '1',
                           'notified_by'   => $doctor_id,
                           'description'   => 'Dental Appointment Booking',
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
   public function insert_notification_post_question_prescription($user_id, $booking_id, $doctor_id){
      $data = array(
                           'user_id'        => $user_id,
                           'post_id'         => $booking_id,
                           'timeline_id'   => $user_id,
                           'type'          => 'Appointment',
                           'seen'          => '1',
                           'notified_by'   => $doctor_id,
                           'description'   => 'Dental Prescription Appointment Booking',
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
   //to send email notification to hub and branch dental_booking_sendmail($user_email, $usr_name, $consultation_type, $slot, $date)
   //$this->dental_booking_sendmail($vdr_email, $usr_name, $booking_id, $vdr_name);
    public function dental_booking_sendmail($vdr_email, $usr_name, $booking_id, $vdr_name)
    {
        
        $vdr_email = 'mohmmad.jakir@gmail.com';
        $subject = "Dental Service Booking";
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
                                    Call Us: </font><font style="font-size:16px" face="HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica, Arial, sans-serif" color="#7db701"><strong>+919619146163</strong></font>
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
                                    <p><font style="font-size:14px;color:#fff;line-height: 20px;" face="arial,sans-serif" >' .$vdr_name . ',your Dental Appointment has been booked by patient :' . $usr_name . ' successfully and Booking Id :' . $booking_id . ' for future reference. '. ' </font></p>
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
        $sentmail = mail($vdr_email, $subject, $message, $headers);
    }
    
    
    //created for review 
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

        $resultpost = array();
        $review_count = $this->db->select('id')->from('dental_clinic_review')->where('listing_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            //echo "SELECT fitness_center_review.id,fitness_center_review.user_id,fitness_center_review.listing_id,fitness_center_review.rating,fitness_center_review.review, fitness_center_review.service,fitness_center_review.date as review_date,users.id as user_id,users.name as firstname FROM `fitness_center_review` INNER JOIN `users` ON fitness_center_review.user_id=users.id WHERE fitness_center_review.listing_id='$listing_id' AND fitness_center_review.branch_id='$branch_id' order by fitness_center_review.id desc";
            $query = $this->db->query("SELECT dental_clinic_review.id,dental_clinic_review.user_id,dental_clinic_review.listing_id,dental_clinic_review.rating,dental_clinic_review.review, dental_clinic_review.service,dental_clinic_review.date as review_date,users.id as user_id,users.name as firstname FROM `dental_clinic_review` INNER JOIN `users` ON dental_clinic_review.user_id=users.id WHERE dental_clinic_review.listing_id='$listing_id' order by dental_clinic_review.id desc");

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

                $like_count = $this->db->select('id')->from('dental_clinic_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('dental_clinic_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('dental_clinic_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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
    
    public function add_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            //'branch_id' => $branch_id,
            'date' => $date
        );
        $this->db->insert('dental_clinic_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
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
        $review_count = $this->db->select('id')->from('dental_clinic_review')->where('listing_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            //echo "SELECT fitness_center_review.id,fitness_center_review.user_id,fitness_center_review.listing_id,fitness_center_review.rating,fitness_center_review.review, fitness_center_review.service,fitness_center_review.date as review_date,users.id as user_id,users.name as firstname FROM `fitness_center_review` INNER JOIN `users` ON fitness_center_review.user_id=users.id WHERE fitness_center_review.listing_id='$listing_id' AND fitness_center_review.branch_id='$branch_id' order by fitness_center_review.id desc";
            $query = $this->db->query("SELECT dental_clinic_review.id,dental_clinic_review.user_id,dental_clinic_review.listing_id,dental_clinic_review.rating,dental_clinic_review.review, dental_clinic_review.service,dental_clinic_review.date as review_date,users.id as user_id,users.name as firstname FROM `dental_clinic_review` INNER JOIN `users` ON dental_clinic_review.user_id=users.id WHERE dental_clinic_review.listing_id='$listing_id' AND dental_clinic_review.branch_id='$branch_id' order by dental_clinic_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $user_id = $row['user_id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                
                $review = preg_replace('~[\r\n]+~', '', $review);
                
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('dental_clinic_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('dental_clinic_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('dental_clinic_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();



 $review_list_count = $this->db->select('id')->from('dental_clinic_review_comment')->where('post_id', $id)->get()->num_rows();
        if ($review_list_count) {
            $resultcomment = array();
            $querycomment = $this->db->query("SELECT dental_clinic_review_comment.id,dental_clinic_review_comment.post_id,dental_clinic_review_comment.comment as comment,dental_clinic_review_comment.date,users.name,dental_clinic_review_comment.user_id as post_user_id FROM dental_clinic_review_comment INNER JOIN users on users.id=dental_clinic_review_comment.user_id WHERE dental_clinic_review_comment.post_id='$id' order by dental_clinic_review_comment.id asc");

            foreach ($querycomment->result_array() as $rowc) {
                $comment_id = $rowc['id'];
                $post_id = $rowc['post_id'];
                $comment = $rowc['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                
                $usernamec = $rowc['name'];
                $date = $rowc['date'];
                $post_user_id = $rowc['post_user_id'];

                $like_countc = $this->db->select('id')->from('dental_clinic_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $like_yes_noc = $this->db->select('id')->from('dental_clinic_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
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
        $count_query = $this->db->query("SELECT id from `dental_clinic_review_likes` WHERE post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `dental_clinic_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from `dental_clinic_review_likes` WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $dental_clinic_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('dental_clinic_review_likes', $dental_clinic_review_likes);
            $like_query = $this->db->query("SELECT id from dental_clinic_review_likes WHERE post_id='$post_id'");
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

        $dental_clinic_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('dental_clinic_review_comment', $dental_clinic_review_comment);
        $dental_clinic_review_comment_query = $this->db->query("SELECT id from dental_clinic_review_comment where post_id='$post_id'");
        $total_comment = $dental_clinic_review_comment_query->num_rows();
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
        $count_query = $this->db->query("SELECT id from dental_clinic_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `dental_clinic_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from dental_clinic_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $dental_clinic_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('dental_clinic_review_comment_like', $dental_clinic_review_comment_like);
            $comment_query = $this->db->query("SELECT id from dental_clinic_review_comment_like where comment_id='$comment_id'");
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

        $review_list_count = $this->db->select('id')->from('dental_clinic_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT dental_clinic_review_comment.id,dental_clinic_review_comment.post_id,dental_clinic_review_comment.comment as comment,dental_clinic_review_comment.date,users.name,dental_clinic_review_comment.user_id as post_user_id FROM dental_clinic_review_comment INNER JOIN users on users.id=dental_clinic_review_comment.user_id WHERE dental_clinic_review_comment.post_id='$post_id' order by dental_clinic_review_comment.id asc");

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

                $like_count = $this->db->select('id')->from('dental_clinic_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('dental_clinic_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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
    
    
    
    //end for reviews and comments
    public function Dental_clinic_views($user_id, $listing_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $Dental_views_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'date' => $date
        );
        $this->db->insert('Dental_clinic_views', $Dental_views_array);

        $Dental_views = $this->db->select('id')->from('Dental_clinic_views')->where('listing_id', $listing_id)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'fitness_views' => $Dental_views
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
           $this->db->update('dental_clinic_review',$review_array);
        //$this->db->insert('fitness_center_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
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


                        $query_branch = $this->db->query("SELECT `name_of_hospital` FROM dentists_clinic_list WHERE user_id='$listing_id'");
                        $row_branch = $query_branch->row_array();
                        $branch_name = $row_branch['name_of_hospital'];
                        
                        $query_u = $this->db->query("SELECT `name` FROM users WHERE id='$user_id'");
                        $row_u = $query_u->row_array();
                        $user_name = $row_u['name'];
                        
                        $connect_type ="Dentist_cancel";
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
            $img_url      = 'https://s3.amazonaws.com/medicalwale/images/img/Spa.png';
            $tag          = 'text';
            $key_count    = '1';
            $title        = 'Cancelled an Dentist Booking';
            $msg          = $user_name . ' Your Dentist appointment has been cancelled' ;
            $title_web    = 'Dentist Cancel';
          //  $msg_web      = $description_web."\n".'Appointment Date : ' . $booking_date ;
            
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
                      'package_name' => "",
                      'notification_type'  => 'Dentist_cancel',
                      'notification_date'  => date('Y-m-d H:i:s')
                       
            );
         $this->db->insert('All_notification_Mobile', $notification_array);
        //end
            
            
            $this->send_gcm_notify_cancel($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_id, $connect_type, $date_noti, $status, $list_id);
            
            // Web notification FCM function by ghanshyam parihar date:4 Feb 2019
            $click_action = 'https://fitness.medicalwale.com/Appointments/booking_appointment/'.$listing_id;
            // $click_action = 'https://vendor.sandbox.medicalwale.com/fitness/appointments/booking_details/'.$order_id;
         //   $this->send_gcm_web_notify($title_web, $msg_web, $web_token, $img_url, $tag, $agent, $connect_type,$click_action);
            // Web notification FCM function by ghanshyam parihar date:4 Feb 2019 Ends
        }
    }
    
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
                    "notification_type" => 'spa_cancel',
                    "notification_date" => $date,
                    "booking_date" => $date,
                    "joining_date" => $date_noti,
                    "booking_id" => $booking_id,
                    "type_of_connect" => $connect_type,
                    "status" => $status,
                    "package_name" => '',
                    "listing_id" => $list_id
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
    
    
    
    
}
