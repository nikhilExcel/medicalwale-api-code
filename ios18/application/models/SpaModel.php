<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SpaModel extends CI_Model {

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
   
    public function spa_center_list_category($user_id, $latitude, $longitude,$page,$api_type)
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
        $resultpost = array();
        function GetDrivingDistance($lat1, $lat2, $long1, $long2) 
        {
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
              $where1 = "";
               if($api_type ==1)
               {
                  $where1 .= "AND fcb.recommended='1' ";
               }
               elseif($api_type ==2) 
               {
                  $where1 .= "AND fcb.best_deals='1'"; 
               }
               
            $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.lat, fcb.lng,( 6371 * acos( cos( radians($latitude) ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON (fcb.user_id= fc.user_id)  WHERE fc.is_active='1'$where1 HAVING distance < '%s' ORDER BY distance LIMIT $start, $limit ", ($latitude), ($longitude), ($latitude), ($radius));
            
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $listing_id = $row['id'];
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $branch_name = $row['branch_name'];
                   
                    $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
               
                            
                           
                                $resultpost[] = array(
                                    'lat' => $lat,
                                    'lng' => $lng,
                                    'distance' => $distances,
                                    'listing_id' => $listing_id,
                                    'center_name' => $branch_name
                                    );
                    
                }
            }
    
           return $resultpost;

        
    }
    
    public function spa_package_list_categorywise($user_id,$main_category,$sub_category,$tab_id,$sort,$latitude,$longitude,$price)
    {
         $radius = '5';
        $resultpost = array();
       
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
        
         $package_list = array();
         $where = "";
                if(!empty($tab_id))
                {
                    $where .= " AND (deliverytype = '$tab_id' OR deliverytype ='2')";
                }
                if(!empty($main_category) || !empty($sub_category))
                {
                    $where .= " AND (FIND_IN_SET('" . $main_category . "', spa_package_category) OR FIND_IN_SET('" . $sub_category . "', spa_package_subcategory))";
                }
                $wh="";
                if($sort=="low")
                {
                    $wh .="order by price asc";
                }
                elseif($sort=="high")
                {
                    $wh .="order by price desc";
                }
                elseif($sort=="discount")
                {
                    $wh .="order by discount desc";
                }
                
                if($price !="")
                {
                    $final=explode(",",$price);
                    if(count($final) == 2)
                    $where .="AND price BETWEEN $final[0] AND $final[1]";
                    
                }
                $package_query = $this->db->query("SELECT * FROM `packages` WHERE user_id='$user_id' $where ");
                  //echo "SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND FIND_IN_SET('".$sub_category."',categ_id ) order by id asc";
                    $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id = $package_row['id'];
                            $package_name = $package_row['package_name'];
                            $package_details = $package_row['package_details'];
                            $price = $package_row['price'];
                            $discount = $package_row['discount'];
                            $image = $package_row['image'];
                            $package_times = $package_row['package_times'];
                            $delivery_type = $package_row['deliverytype'];
                            $categ_id = $package_row['categ_id'];
                            $spa_cat = $package_row['spa_package_category'];
                            
                            $user_id= $package_row['user_id'];
                            
                            $types = array();
                            if(!empty($categ_id))
                            {
                                $exp=explode(',',$categ_id);
                                for($i=0;$i<count($exp);$i++)
                                {
                                    $c_id = $exp[$i];
                                    $query = $this->db->query("SELECT * FROM business_category WHERE id='$c_id'");
                                    $count = $query->num_rows();
                                    if ($count > 0) {
                                        $rowss=$query->row_array();
                                        $cat_id = $rowss['id'];
                                        $category = $rowss['category'];
                                        
                                        $types[] = array('type_id' =>$cat_id,'type' => $category); 
                                    }
                                }
                            }
                            
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            
                            $imgs = array();
                            $packi_query = $this->db->query("SELECT * FROM spa_package_img WHERE package_id='$package_id'");
                            $count_im = $packi_query->num_rows();
                            if ($count_im > 0) {
                                foreach ($packi_query->result_array() as $row_p) {
                                    $packi_id = $row_p['id'];
                                    $package_image = $row_p['image'];
                                    
                                    $imgs[] = array('image' => 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' .$package_image);
                                }
                            }
                            
                            $categorys = array();
                            if(!empty($spa_cat))
                            {
                                $exp1=explode(',',$spa_cat);
                                for($i=0;$i<count($exp1);$i++)
                                {
                                    $c_id1 = $exp1[$i];
                                    $query1 = $this->db->query("SELECT * FROM spa_category WHERE id='$c_id1'");
                                    $count1 = $query1->num_rows();
                                    if ($count1 > 0) {
                                        $rowss1=$query1->row_array();
                                        $cat_id1 = $rowss1['id'];
                                        $category1 = $rowss1['category'];
                                        
                                        $categorys[] = array('cat_id' =>$cat_id1,'category' => $category1); 
                                    }
                                }
                            }
                           
                            
                            $sqls = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fcb.user_id='$user_id'  HAVING distance < '5' ORDER BY distance", ($latitude), ($longitude), ($latitude), ($radius));
            
            $querys = $this->db->query($sqls);
            $counts = $querys->num_rows();
            if ($counts > 0) {
                    $row = $querys->row_array();
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
                   
                    $listing_id = $row['user_id'];
                    $listing_type = '36';
                    $rating = '4.5';
                    $reviews = '0';
                    $profile_views =  '0' ;
                    $user_discount = $row['user_discount'];
    
                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    
                    $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM spa_center_review WHERE listing_id='$listing_id'");
                $row_pharma = $query_pharmacy->row_array();
                
               $rating = $row_pharma['avg_rating'];
               
                if ($rating === NULL) {
                    $rating = '0';
                }
                
                  
                    $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
                            
                $resultpost[] = array(
                                    'branch_id' => $branch_id,
                                    'lat' => $lat,
                                    'lng' => $lng,
                                    'distance' => $distances,
                                    'category_id' => $sub_category,
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
                                    'email' => $branch_email,
                                    'image' => $branch_image,
                                    'user_discount' => $user_discount,
                                    'rating' => $rating,
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price,
                                    'discount' => $discount,
                                    'package_image' => $imgs,
                                    'package_times' => $package_times,
                                    'delivery_type' => $delivery_type,
                                    'types'      => $types,
                                    'category'  => $categorys
                                );
                
            }
                  
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
                        if($sort == "Distance")
                        {
                            array_sort_by_column($resultpost, 'distance');
                        }
                        
                        if($sort == "Discount")
                        {
                            array_sort_by_column($resultpost, 'discount');
                        }
                        
                        if($sort == "Price")
                        {
                            array_sort_by_column($resultpost, 'price');
                        }
                return $resultpost;
    }
    public function spa_therapies_list($user_id){
        $query = $this->db->query("SELECT * FROM spa_category WHERE sub_category='0'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $category_id = $row['id'];
                $category_name = $row['category'];
                $image = $row['image'];
                if(!empty($image))
                {
                     $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                }
                else
                {
                     $image = "";
                }
                $result_list[] = array(
                                'category_id' => $category_id,
                                'category_name' => $category_name,
                                'category_image' => $image
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
    
    public function spa_common_therapies_list($user_id){
        $query = $this->db->query("SELECT * FROM spa_category WHERE  sub_category!='0'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $category_id = $row['id'];
                $category_name = $row['category'];
                $image = $row['image'];
                if(!empty($image))
                {
                     $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                }
                else
                {
                     $image = "";
                }
                $result_list[] = array(
                                'category_id' => $category_id,
                                'category_name' => $category_name,
                                'category_image' => $image
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
    
    public function spa_time_slot($user_id,$branch_id)
    {
        $timings = array();
        $sql = "SELECT fcb.opening_hours FROM spa_branch as fcb  WHERE fcb.id = '$branch_id' AND fcb.user_id = '$user_id'";
        
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
        return $timings;
    }
    
    
    public function spa_center_list_v2($user_id,$latitude, $longitude, $tab_id, $cat_type, $main_category, $sub_category, $sort, $page) {
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
        
        $where = "";
        if(!empty($tab_id))
        {
            $where .= " AND (fcb.massage_service='$tab_id' OR fcb.massage_service='2')";
        }
       /* if(!empty($cat_type))
        {
            $where .= " AND FIND_IN_SET('" . $cat_type . "', branch_business_category)";
        }*/
        
        
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
        
        $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1' $where HAVING distance < '5' ORDER BY distance $limit", ($latitude), ($longitude), ($latitude), ($radius));
        
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
                //  $is_free_trail = $row['is_free_trail'];
                //  $from_trail_time = $row['from_trail_time'];
                //  $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '36';
                $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM spa_center_review WHERE listing_id='$listing_id'");
                $row_pharma = $query_pharmacy->row_array();
                
               $rating = $row_pharma['avg_rating'];
               
                if ($rating === NULL) {
                    $rating = '0';
                }
                
                $reviews = $this->db->select('id')->from('spa_center_review')->where('listing_id', $listing_id)->get()->num_rows();
                
                $disc = $this->db->select('max(discount) as disc,max(price) as price')->from('packages')->where('user_id', $listing_id)->where('branch', $branch_id)->get()->row();
                if(!empty($disc))
                {
                    $disc1 = $disc->disc;
                    $price = $disc->price;
                }
                else
                {
                    $disc1 =0;
                    $price =0;
                }
                $profile_views =  '0' ;
                $user_discount = $row['user_discount'];

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
            
            
             
                $what_we_offer_array = array();
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                    if($what_we_offer != ""){
                        if(preg_match('/[0-9]/', $what_we_offer)){
                            $Query = $this->db->query("SELECT * FROM spa_services WHERE id='$what_we_offer'");
                            $what_we_offer_value = $Query->row()->services;
                            $what_we_offer_array[] = $what_we_offer_value;
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
                            $Query = $this->db->query("SELECT * FROM spa_facilites WHERE f_id='$facilities'");
                            $facilities_val = $Query->row()->facilities;
                            $facilities_array[] = $facilities_val;
                            
                        }
                        else{
                            $facilities_array[] = $facilities;
                        }
                    }
                }
                
                $branch_business_category_array = array();
                $branch_business = explode(',', $branch_business_category);
               
                foreach ($branch_business as $facilities) {
                    if($facilities !="")
                    {
                            $Query = $this->db->query("SELECT category,id FROM `business_category` WHERE `category_id` = 36 AND id='$facilities'");
                            $facilities_val = $Query->row()->category;
                            $branch_business_category_array[] = $facilities_val;
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
                $package_query = $this->db->query("SELECT * FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' order by id asc");
                $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id = $package_row['id'];
                            $package_name = $package_row['package_name'];
                            $package_details = $package_row['package_details'];
                            $price = $package_row['price'];
                            $discount = $package_row['discount'];
                            $image = $package_row['image'];
                            $package_times = $package_row['package_times'];
                            $delivery_type = $package_row['deliverytype'];
                            $categ_id = $package_row['categ_id'];
                            $spa_cat = $package_row['spa_package_category'];
                            
                            $types = array();
                            if(!empty($categ_id))
                            {
                                $exp=explode(',',$categ_id);
                                for($i=0;$i<count($exp);$i++)
                                {
                                    $c_id = $exp[$i];
                                    $query = $this->db->query("SELECT * FROM business_category WHERE id='$c_id'");
                                    $count = $query->num_rows();
                                    if ($count > 0) {
                                        $rowss=$query->row_array();
                                        $cat_id = $rowss['id'];
                                        $category = $rowss['category'];
                                        
                                        $types[] = array('type_id' =>$cat_id,'type' => $category); 
                                    }
                                }
                            }
                            
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            
                            $imgs = array();
                            $packi_query = $this->db->query("SELECT * FROM spa_package_img WHERE package_id='$package_id'");
                            $count_im = $packi_query->num_rows();
                            if ($count_im > 0) {
                                foreach ($packi_query->result_array() as $row_p) {
                                    $packi_id = $row_p['id'];
                                    $package_image = $row_p['image'];
                                    
                                    $imgs[] = array('image' => 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' .$package_image);
                                }
                            }
                            
                            $categorys = array();
                            if(!empty($spa_cat))
                            {
                                $exp1=explode(',',$spa_cat);
                                for($i=0;$i<count($exp1);$i++)
                                {
                                    $c_id1 = $exp1[$i];
                                    $query1 = $this->db->query("SELECT * FROM spa_category WHERE id='$c_id1'");
                                    $count1 = $query1->num_rows();
                                    if ($count1 > 0) {
                                        $rowss1=$query1->row_array();
                                        $cat_id1 = $rowss1['id'];
                                        $category1 = $rowss1['category'];
                                        
                                        $categorys[] = array('cat_id' =>$cat_id1,'category' => $category1); 
                                    }
                                }
                            }
                            
                            $package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'discount' => $discount,
                                'package_image' => $imgs,
                                'package_times' => $package_times,
                                'delivery_type' => $delivery_type,
                                'types'      => $types,
                                'category'  => $categorys
                            );
                    }
                } else {
                    $package_list = array();
                }



                $spa_views = $this->db->select('id')->from('spa_views')->where('listing_id', $listing_id)->get()->num_rows();
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
                    $language = $row1['language'];
                    
                    $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                  /*   $session1[] = array(
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
                         */
                           
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
                                        'language' => $language
                                       /*'package_trainer' => $final_session1*/
                                     );
                                       //'opening_hours' => $branch_final_Day);
                    
                }
                    
            }
            else {
               $sub_array1=array();
            }


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
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'therapist_list' => $sub_array1,
                    'speciality' => $branch_business_category_array,
                    'current_time'=>$current_day_final,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                   // 'is_trial_order' => $is_trial_order,
                    'profile_views' => $spa_views,
                    'user_discount' => $disc1,
                    'price'     => $price
                   
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

        if($sort == "Distance")
        {
            array_sort_by_column($resultpost, 'distance');
        }
        
        if($sort == "Discount")
        {
            array_sort_by_column($resultpost, 'user_discount');
        }
        
        if($sort == "Price")
        {
            array_sort_by_column($resultpost, 'price');
        }

        return $resultpost;
    }
    
    public function related_spa_center_list_v2($user_id,$latitude, $longitude, $listing_id, $page) {
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
        
        $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1' AND fcb.user_id != '$listing_id' AND fc.user_id != '$listing_id' HAVING distance < '5' ORDER BY distance $limit", ($latitude), ($longitude), ($latitude), ($radius));
        
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
                //  $is_free_trail = $row['is_free_trail'];
                //  $from_trail_time = $row['from_trail_time'];
                //  $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '36';
                $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM spa_center_review WHERE listing_id='$listing_id'");
                $row_pharma = $query_pharmacy->row_array();
                
               $rating = $row_pharma['avg_rating'];
               
                if ($rating === NULL) {
                    $rating = '0';
                }
                
                $reviews = $this->db->select('id')->from('spa_center_review')->where('listing_id', $listing_id)->get()->num_rows();
                
                $disc = $this->db->select('max(discount) as disc,max(price) as price')->from('packages')->where('user_id', $listing_id)->where('branch', $branch_id)->get()->row();
                if(!empty($disc))
                {
                    $disc1 = $disc->disc;
                    $price = $disc->price;
                }
                else
                {
                    $disc1 =0;
                    $price =0;
                }
                $profile_views =  '0' ;
                $user_discount = $row['user_discount'];

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
            
            
             
                $what_we_offer_array = array();
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                    if($what_we_offer != ""){
                        if(preg_match('/[0-9]/', $what_we_offer)){
                            $Query = $this->db->query("SELECT * FROM spa_services WHERE id='$what_we_offer'");
                            $what_we_offer_value = $Query->row()->services;
                            $what_we_offer_array[] = $what_we_offer_value;
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
                            $Query = $this->db->query("SELECT * FROM spa_facilites WHERE f_id='$facilities'");
                            $facilities_val = $Query->row()->facilities;
                            $facilities_array[] = $facilities_val;
                            
                        }
                        else{
                            $facilities_array[] = $facilities;
                        }
                    }
                }
                
                $branch_business_category_array = array();
                $branch_business = explode(',', $branch_business_category);
               
                foreach ($branch_business as $facilities) {
                    if($facilities !="")
                    {
                            $Query = $this->db->query("SELECT category,id FROM `business_category` WHERE `category_id` = 36 AND id='$facilities'");
                            $facilities_val = $Query->row()->category;
                            $branch_business_category_array[] = $facilities_val;
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
                $package_query = $this->db->query("SELECT * FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' order by id asc");
                $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id = $package_row['id'];
                            $package_name = $package_row['package_name'];
                            $package_details = $package_row['package_details'];
                            $price = $package_row['price'];
                            $discount = $package_row['discount'];
                            $image = $package_row['image'];
                            $package_times = $package_row['package_times'];
                            $delivery_type = $package_row['deliverytype'];
                            $categ_id = $package_row['categ_id'];
                            $spa_cat = $package_row['spa_package_category'];
                            
                            $types = array();
                            if(!empty($categ_id))
                            {
                                $exp=explode(',',$categ_id);
                                for($i=0;$i<count($exp);$i++)
                                {
                                    $c_id = $exp[$i];
                                    $query = $this->db->query("SELECT * FROM business_category WHERE id='$c_id'");
                                    $count = $query->num_rows();
                                    if ($count > 0) {
                                        $rowss=$query->row_array();
                                        $cat_id = $rowss['id'];
                                        $category = $rowss['category'];
                                        
                                        $types[] = array('type_id' =>$cat_id,'type' => $category); 
                                    }
                                }
                            }
                            
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            
                            $imgs = array();
                            $packi_query = $this->db->query("SELECT * FROM spa_package_img WHERE package_id='$package_id'");
                            $count_im = $packi_query->num_rows();
                            if ($count_im > 0) {
                                foreach ($packi_query->result_array() as $row_p) {
                                    $packi_id = $row_p['id'];
                                    $package_image = $row_p['image'];
                                    
                                    $imgs[] = array('image' => 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' .$package_image);
                                }
                            }
                            
                            $categorys = array();
                            if(!empty($spa_cat))
                            {
                                $exp1=explode(',',$spa_cat);
                                for($i=0;$i<count($exp1);$i++)
                                {
                                    $c_id1 = $exp1[$i];
                                    $query1 = $this->db->query("SELECT * FROM spa_category WHERE id='$c_id1'");
                                    $count1 = $query1->num_rows();
                                    if ($count1 > 0) {
                                        $rowss1=$query1->row_array();
                                        $cat_id1 = $rowss1['id'];
                                        $category1 = $rowss1['category'];
                                        
                                        $categorys[] = array('cat_id' =>$cat_id1,'category' => $category1); 
                                    }
                                }
                            }
                            
                            $package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'discount' => $discount,
                                'package_image' => $imgs,
                                'package_times' => $package_times,
                                'delivery_type' => $delivery_type,
                                'types'      => $types,
                                'category'  => $categorys
                            );
                    }
                } else {
                    $package_list = array();
                }



                $spa_views = $this->db->select('id')->from('spa_views')->where('listing_id', $listing_id)->get()->num_rows();
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
                    $language = $row1['language'];
                    
                    $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                  /*   $session1[] = array(
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
                         */
                           
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
                                        'language' => $language
                                       /*'package_trainer' => $final_session1*/
                                     );
                                       //'opening_hours' => $branch_final_Day);
                    
                }
                    
            }
            else {
               $sub_array1=array();
            }


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
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'therapist_list' => $sub_array1,
                    'speciality' => $branch_business_category_array,
                    'current_time'=>$current_day_final,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                   // 'is_trial_order' => $is_trial_order,
                    'profile_views' => $spa_views,
                    'user_discount' => $disc1,
                    'price'     => $price
                   
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }
    
     public function elasticsearchsize3($index_id,$keyword){
        $returndoctor = array();
        $perc=array();
     	$data = array("query"=>array("bool"=>array("should"=>array(array("match"=>array("branch_name"=>"$keyword" )),array("match"=>array("field1"=>"$keyword" ))))),"suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"branch_name")))); 
        $data1=json_encode($data);
      
        $returnresult = $this->elasticsearch->advancedquerysize3($index_id,$data1);
        
    	   if($returnresult['hits']['total'] > 0){
    	       
                	   foreach($returnresult['hits']['hits'] as $hi){
                	 
                	      $sim = similar_text($hi['_source']['branch_name'], $keyword, $perc[]);
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
                        $returnresult = $this->elasticsearch->query_allsize3($index_id,$string_version);
                                foreach($returnresult['hits']['hits'] as $hi){
                                	      $returndoctor[] = $hi['_source'];
                                	   }
                                	 
                      return $returndoctor;
            }
       
}
    
      public function elasticsearchpackage3($index_id,$keyword){
        $returndoctor = array();
        $perc=array();
     	$data = array("query"=>array("bool"=>array("should"=>array(array("match"=>array("package_name"=>"$keyword" )),array("match"=>array("field1"=>"$keyword" ))))),"suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"package_name")))); 
        $data1=json_encode($data);
      
        $returnresult = $this->elasticsearch->advancedquerysize3($index_id,$data1);
        
    	   if($returnresult['hits']['total'] > 0){
    	       
                	   foreach($returnresult['hits']['hits'] as $hi){
                	 
                	      $sim = similar_text($hi['_source']['branch_name'], $keyword, $perc[]);
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
                        $returnresult = $this->elasticsearch->query_allsize3($index_id,$string_version);
                                foreach($returnresult['hits']['hits'] as $hi){
                                	      $returndoctor[] = $hi['_source'];
                                	   }
                                	 
                      return $returndoctor;
            }
       
}
    
    public function elasticsearch($index_id,$keyword){
        $returndoctor = array();
        $perc=array();
     	$data = array("query"=>array("bool"=>array("should"=>array(array("match"=>array("center_name"=>"$keyword" ))))),"suggest"=>array("my-suggestion"=>array("text"=>$keyword, "term"=>array("field"=>"center_name")))); 
        $data1=json_encode($data);
    
        $returnresult = $this->elasticsearch->advancedquerysize3($index_id,$data1);
         print_r($returnresult); 
         
    	   if($returnresult['hits']['total'] > 0){ 
    	       
                	   foreach($returnresult['hits']['hits'] as $hi){
                	 
                	      $sim = similar_text($hi['_source']['center_name'], $keyword, $perc[]);
                	   }
                	}
                 @$dataperc=max($perc);
       // if($dataperc >= 50){
               foreach($returnresult['hits']['hits'] as $hi){
                	      $returndoctor[] =$hi['_source'];
                	   }
                     
                	   return $returndoctor;
                	   
       /*         	   
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
                        $returnresult = $this->elasticsearch->query_allsize3($index_id,$string_version);
                                foreach($returnresult['hits']['hits'] as $hi){
                                	      $returndoctor[] = $hi['_source'];
                                	   }
                                	 
                      return $returndoctor;
            }*/
       
}

    public function  spa_search($user_id,$keyword,$latitude,$longitude){
    
     
             
           // $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,p.price as package_price,p.discount as package_discount,p.package_name,p.package_details,p.image as package_image,p.package_times,p.deliverytype as package_deliverytype,p.categ_id as package_categ_id,p.spa_package_category,p.id as package_id, ( 6371 * acos( cos( radians($latitude) ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON (fcb.user_id= fc.user_id) INNER JOIN packages as p ON (fc.user_id=p.user_id AND p.v_id='36' AND p.branch=fcb.id) WHERE fc.is_active='1'  $where1 $where $cat_wh $pricesort HAVING distance < '%s' $wh $limit", ($latitude), ($longitude), ($latitude), ($radius));
           $sql =("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,p.price as package_price,p.discount as package_discount,p.package_name,p.package_details,p.image as package_image,p.package_times,p.deliverytype as package_deliverytype,p.categ_id as package_categ_id,p.spa_package_category,p.id as package_id FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON (fcb.user_id= fc.user_id) INNER JOIN packages as p ON (fc.user_id=p.user_id AND p.v_id='36' AND p.branch=fcb.id) WHERE fc.is_active='1' " );
             $query = $this->db->query($sql);
            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $branch_id = $row['package_id'];
            $data[]=$this->elasticsearch->add("spa_package", $branch_id, $row);
                }
            }
            print_r($data);
    
}
    
   
    public function spa_search_all_list($user_id,$keyword,$latitude,$longitude) {
      
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
      
        if ($user_id > 0) {
                    
            $index_id="spa_listing";
            $people=$this->elasticsearchsize3($index_id,$keyword);  

            foreach ($people as $row) {
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
                $listing_id = $row['user_id'];
                $listing_type = '36';
                $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM spa_center_review WHERE listing_id='$listing_id'");
                $row_pharma = $query_pharmacy->row_array();
                
               $rating = $row_pharma['avg_rating'];
               
                if ($rating === NULL) {
                    $rating = '0';
                }
                
                $reviews = $this->db->select('id')->from('spa_center_review')->where('listing_id', $listing_id)->get()->num_rows();
                
                $disc = $this->db->select('max(discount) as disc,max(price) as price')->from('packages')->where('user_id', $listing_id)->where('branch', $branch_id)->get()->row();
                if(!empty($disc))
                {
                    $disc1 = $disc->disc;
                    $price = $disc->price;
                }
                else
                {
                    $disc1 =0;
                    $price =0;
                }
                $profile_views =  '0' ;
                $user_discount = $row['user_discount'];

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
            
            
             
                $what_we_offer_array = array();
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                    if($what_we_offer != ""){
                        if(preg_match('/[0-9]/', $what_we_offer)){
                            $Query = $this->db->query("SELECT * FROM spa_services WHERE id='$what_we_offer'");
                            $what_we_offer_value = $Query->row()->services;
                            $what_we_offer_array[] = $what_we_offer_value;
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
                            $Query = $this->db->query("SELECT * FROM spa_facilites WHERE f_id='$facilities'");
                            $facilities_val = $Query->row()->facilities;
                            $facilities_array[] = $facilities_val;
                            
                        }
                        else{
                            $facilities_array[] = $facilities;
                        }
                    }
                }
                
                $branch_business_category_array = array();
                $branch_business = explode(',', $branch_business_category);
               
                foreach ($branch_business as $facilities) {
                    if($facilities !="")
                    {
                            $Query = $this->db->query("SELECT category,id FROM `business_category` WHERE `category_id` = 36 AND id='$facilities'");
                            $facilities_val = $Query->row()->category;
                            $branch_business_category_array[] = $facilities_val;
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
                $package_query = $this->db->query("SELECT * FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' order by id asc");
                $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id = $package_row['id'];
                            $package_name = $package_row['package_name'];
                            $package_details = $package_row['package_details'];
                            $price = $package_row['price'];
                            $discount = $package_row['discount'];
                            $image = $package_row['image'];
                            $package_times = $package_row['package_times'];
                            $delivery_type = $package_row['deliverytype'];
                            $categ_id = $package_row['categ_id'];
                            $spa_cat = $package_row['spa_package_category'];
                            
                            $types = array();
                            if(!empty($categ_id))
                            {
                                $exp=explode(',',$categ_id);
                                for($i=0;$i<count($exp);$i++)
                                {
                                    $c_id = $exp[$i];
                                    $query = $this->db->query("SELECT * FROM business_category WHERE id='$c_id'");
                                    $count = $query->num_rows();
                                    if ($count > 0) {
                                        $rowss=$query->row_array();
                                        $cat_id = $rowss['id'];
                                        $category = $rowss['category'];
                                        
                                        $types[] = array('type_id' =>$cat_id,'type' => $category); 
                                    }
                                }
                            }
                            
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            
                            $imgs = array();
                            $packi_query = $this->db->query("SELECT * FROM spa_package_img WHERE package_id='$package_id'");
                            $count_im = $packi_query->num_rows();
                            if ($count_im > 0) {
                                foreach ($packi_query->result_array() as $row_p) {
                                    $packi_id = $row_p['id'];
                                    $package_image = $row_p['image'];
                                    
                                    $imgs[] = array('image' => 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' .$package_image);
                                }
                            }
                            
                            $categorys = array();
                            if(!empty($spa_cat))
                            {
                                $exp1=explode(',',$spa_cat);
                                for($i=0;$i<count($exp1);$i++)
                                {
                                    $c_id1 = $exp1[$i];
                                    $query1 = $this->db->query("SELECT * FROM spa_category WHERE id='$c_id1'");
                                    $count1 = $query1->num_rows();
                                    if ($count1 > 0) {
                                        $rowss1=$query1->row_array();
                                        $cat_id1 = $rowss1['id'];
                                        $category1 = $rowss1['category'];
                                        
                                        $categorys[] = array('cat_id' =>$cat_id1,'category' => $category1); 
                                    }
                                }
                            }
                            
                            $package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'discount' => $discount,
                                'package_image' => $imgs,
                                'package_times' => $package_times,
                                'delivery_type' => $delivery_type,
                                'types'      => $types,
                                'category'  => $categorys
                            );
                    }
                } else {
                    $package_list = array();
                }



                $spa_views = $this->db->select('id')->from('spa_views')->where('listing_id', $listing_id)->get()->num_rows();
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
                    $language = $row1['language'];
                    
                    $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                           
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
                                        'language' => $language
                                       /*'package_trainer' => $final_session1*/
                                     );
                                       //'opening_hours' => $branch_final_Day);
                    
                }
                    
            }
            else {
               $sub_array1=array();
            }


                $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
                $enquiry_number = "9619146163";
                $spa_list[] = array(
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
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'therapist_list' => $sub_array1,
                    'speciality' => $branch_business_category_array,
                    'current_time'=>$current_day_final,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                   // 'is_trial_order' => $is_trial_order,
                    'profile_views' => $spa_views,
                    'user_discount' => $disc1,
                    'price'     => $price
                   
                );
            }
             if (!empty($spa_list)) {
                $spa_list_array[] = array(
                    'title' => 'Spa center',
                    'array' => $spa_list
                );
            } else {
                $spa_list_array = array();
            }
            
            $index_id="spa_package";
            $spa_package=$this->elasticsearchpackage3($index_id,$keyword);
         
            $count = count($spa_package);
            if ($count > 0) {
                
                foreach ($spa_package as $row) {
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
                   
                    $listing_id = $row['user_id'];
                    $listing_type = '36';
                    $rating = '4.5';
                    $reviews = '0';
                    $profile_views =  '0' ;
                    $user_discount = $row['user_discount'];
    
                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    
                    $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM spa_center_review WHERE listing_id='$listing_id'");
                $row_pharma = $query_pharmacy->row_array();
                
               $rating = $row_pharma['avg_rating'];
               
                if ($rating === NULL) {
                    $rating = '0';
                }
                
             
                    
                    $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
               
                   // $package_list = array();
                    
                //  echo "SELECT * FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' order by id asc";
                   /* $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {*/
                        
                       
                            $package_id = $row['package_id'];
                            $package_name = $row['package_name'];
                            $package_details = $row['package_details'];
                            $price = $row['package_price'];
                            $discount = $row['package_discount'];
                            $image = $row['package_image'];
                            $package_times = $row['package_times'];
                            $delivery_type = $row['package_deliverytype'];
                            $categ_id = $row['package_categ_id'];
                            $spa_cat = $row['spa_package_category'];
                            
                            $types = array();
                            if(!empty($categ_id))
                            {
                                $exp=explode(',',$categ_id);
                                for($i=0;$i<count($exp);$i++)
                                {
                                    $c_id = $exp[$i];
                                    $query = $this->db->query("SELECT * FROM business_category WHERE id='$c_id'");
                                    $count = $query->num_rows();
                                    if ($count > 0) {
                                        $rowss=$query->row_array();
                                        $cat_id = $rowss['id'];
                                        $category = $rowss['category'];
                                        
                                        $types[] = array('type_id' =>$cat_id,'type' => $category); 
                                    }
                                }
                            }
                            
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            
                            $imgs = array();
                            $packi_query = $this->db->query("SELECT * FROM spa_package_img WHERE package_id='$package_id'");
                            $count_im = $packi_query->num_rows();
                            if ($count_im > 0) {
                                foreach ($packi_query->result_array() as $row_p) {
                                    $packi_id = $row_p['id'];
                                    $package_image = $row_p['image'];
                                    if(!empty($package_image))
                                    {
                                    $ext = getExtension($package_image);
                                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                                    if(in_array($ext, $img_format))
                                    {
                                        $type="image";
                                        $image='https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/'.$package_image;
                                    }
                                    else
                                    {
                                        $image='https://sandboxapi.medicalwale.com/v51/video.mp4';
                                        $type="video";
                                    }
                                    
                                    $imgs[] = array('image' =>$image ,
                                                    'type' =>$type
                                                   );
                                    }
                                    else
                                    {
                                       $imgs[] = array('image' => '',
                                                    'type' =>""
                                                   ); 
                                    }
                                }
                            }
                            
                            $categorys = array();
                            if(!empty($spa_cat))
                            {
                                $exp1=explode(',',$spa_cat);
                                for($i=0;$i<count($exp1);$i++)
                                {
                                    $c_id1 = $exp1[$i];
                                    $query1 = $this->db->query("SELECT * FROM spa_category WHERE id='$c_id1'");
                                    $count1 = $query1->num_rows();
                                    if ($count1 > 0) {
                                        $rowss1=$query1->row_array();
                                        $cat_id1 = $rowss1['id'];
                                        $category1 = $rowss1['category'];
                                        
                                        $categorys[] = array('cat_id' =>$cat_id1,'category' => $category1); 
                                    }
                                }
                            }
                            
                            /*$package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'discount' => $discount,
                                'package_image' => $imgs,
                                'package_times' => $package_times,
                                'delivery_type' => $delivery_type,
                                'types'      => $types,
                                'category'  => $categorys
                            );*/
                            
                                $spa_package_1[] = array(
                                    'branch_id' => $branch_id,
                                    'lat' => $lat,
                                    'lng' => $lng,
                                    'distance' => $distances,
                                    'category_id' => "",
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
                                    'email' => $branch_email,
                                    'image' => $branch_image,
                                    'user_discount' => $user_discount,
                                    'rating' => $rating,
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price,
                                    'discount' => $discount,
                                    'package_image' => $imgs,
                                    'package_times' => $package_times,
                                    'delivery_type' => $delivery_type,
                                    'types'      => $types,
                                    'category'  => $categorys
                                );
                               
                      /*  }
                    } 
    */
                }
                  if (!empty($spa_package_1)) {
                $spa_package_array[] = array(
                    'title' => 'Spa Package',
                    'array' => $spa_package_1
                );
            } else {
                $spa_package_array = array();
            }
          // print_r($spa_package);
            }
        return  $resultpost = array_merge($spa_list_array, $spa_package_array);
           
        }else{
            
             return $resultpost = array(); 
        }
        
    }
   
   
    public function spa_center_list_v2_search($user_id,$keyword,$latitude,$longitude,$type) {
      
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
      
        if ($user_id > 0) {
                  if($type == 'spa_center')
                  {   
        $index_id="spa_listing";
        $people=$this->elasticsearchsize3($index_id,$keyword);  
        
      
        
    foreach ($people as $row) {
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
                $listing_id = $row['user_id'];
                $listing_type = '36';
                $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM spa_center_review WHERE listing_id='$listing_id'");
                $row_pharma = $query_pharmacy->row_array();
                
               $rating = $row_pharma['avg_rating'];
               
                if ($rating === NULL) {
                    $rating = '0';
                }
                
                $reviews = $this->db->select('id')->from('spa_center_review')->where('listing_id', $listing_id)->get()->num_rows();
                
                $disc = $this->db->select('max(discount) as disc,max(price) as price')->from('packages')->where('user_id', $listing_id)->where('branch', $branch_id)->get()->row();
                if(!empty($disc))
                {
                    $disc1 = $disc->disc;
                    $price = $disc->price;
                }
                else
                {
                    $disc1 =0;
                    $price =0;
                }
                $profile_views =  '0' ;
                $user_discount = $row['user_discount'];

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
            
            
             
                $what_we_offer_array = array();
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                    if($what_we_offer != ""){
                        if(preg_match('/[0-9]/', $what_we_offer)){
                            $Query = $this->db->query("SELECT * FROM spa_services WHERE id='$what_we_offer'");
                            $what_we_offer_value = $Query->row()->services;
                            $what_we_offer_array[] = $what_we_offer_value;
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
                            $Query = $this->db->query("SELECT * FROM spa_facilites WHERE f_id='$facilities'");
                            $facilities_val = $Query->row()->facilities;
                            $facilities_array[] = $facilities_val;
                            
                        }
                        else{
                            $facilities_array[] = $facilities;
                        }
                    }
                }
                
                $branch_business_category_array = array();
                $branch_business = explode(',', $branch_business_category);
               
                foreach ($branch_business as $facilities) {
                    if($facilities !="")
                    {
                            $Query = $this->db->query("SELECT category,id FROM `business_category` WHERE `category_id` = 36 AND id='$facilities'");
                            $facilities_val = $Query->row()->category;
                            $branch_business_category_array[] = $facilities_val;
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
                $package_query = $this->db->query("SELECT * FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' order by id asc");
                $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id = $package_row['id'];
                            $package_name = $package_row['package_name'];
                            $package_details = $package_row['package_details'];
                            $price = $package_row['price'];
                            $discount = $package_row['discount'];
                            $image = $package_row['image'];
                            $package_times = $package_row['package_times'];
                            $delivery_type = $package_row['deliverytype'];
                            $categ_id = $package_row['categ_id'];
                            $spa_cat = $package_row['spa_package_category'];
                            
                            $types = array();
                            if(!empty($categ_id))
                            {
                                $exp=explode(',',$categ_id);
                                for($i=0;$i<count($exp);$i++)
                                {
                                    $c_id = $exp[$i];
                                    $query = $this->db->query("SELECT * FROM business_category WHERE id='$c_id'");
                                    $count = $query->num_rows();
                                    if ($count > 0) {
                                        $rowss=$query->row_array();
                                        $cat_id = $rowss['id'];
                                        $category = $rowss['category'];
                                        
                                        $types[] = array('type_id' =>$cat_id,'type' => $category); 
                                    }
                                }
                            }
                            
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            
                            $imgs = array();
                            $packi_query = $this->db->query("SELECT * FROM spa_package_img WHERE package_id='$package_id'");
                            $count_im = $packi_query->num_rows();
                            if ($count_im > 0) {
                                foreach ($packi_query->result_array() as $row_p) {
                                    $packi_id = $row_p['id'];
                                    $package_image = $row_p['image'];
                                    
                                    $imgs[] = array('image' => 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' .$package_image);
                                }
                            }
                            
                            $categorys = array();
                            if(!empty($spa_cat))
                            {
                                $exp1=explode(',',$spa_cat);
                                for($i=0;$i<count($exp1);$i++)
                                {
                                    $c_id1 = $exp1[$i];
                                    $query1 = $this->db->query("SELECT * FROM spa_category WHERE id='$c_id1'");
                                    $count1 = $query1->num_rows();
                                    if ($count1 > 0) {
                                        $rowss1=$query1->row_array();
                                        $cat_id1 = $rowss1['id'];
                                        $category1 = $rowss1['category'];
                                        
                                        $categorys[] = array('cat_id' =>$cat_id1,'category' => $category1); 
                                    }
                                }
                            }
                            
                            $package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'discount' => $discount,
                                'package_image' => $imgs,
                                'package_times' => $package_times,
                                'delivery_type' => $delivery_type,
                                'types'      => $types,
                                'category'  => $categorys
                            );
                    }
                } else {
                    $package_list = array();
                }



                $spa_views = $this->db->select('id')->from('spa_views')->where('listing_id', $listing_id)->get()->num_rows();
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
                    $language = $row1['language'];
                    $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                  /*   $session1[] = array(
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
                         */
                           
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
                                        'language' => $language
                                       /*'package_trainer' => $final_session1*/
                                     );
                                       //'opening_hours' => $branch_final_Day);
                    
                }
                    
            }
            else {
               $sub_array1=array();
            }


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
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'therapist_list' => $sub_array1,
                    'speciality' => $branch_business_category_array,
                    'current_time'=>$current_day_final,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                   // 'is_trial_order' => $is_trial_order,
                    'profile_views' => $spa_views,
                    'user_discount' => $disc1,
                    'price'     => $price
                   
                );
            }
              return $resultpost;
}
                 
        else if($type =='package'){
           // echo "dinesh";
            $index_id="spa_package";
        $spa_package=$this->elasticsearchpackage3($index_id,$keyword);
           // print_r($spa_package);
           // exit();
            $count = count($spa_package);
            if ($count > 0) {
                foreach ($spa_package as $row) {
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
                   
                    $listing_id = $row['user_id'];
                    $listing_type = '36';
                    $rating = '4.5';
                    $reviews = '0';
                    $profile_views =  '0' ;
                    $user_discount = $row['user_discount'];
    
                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    
                    $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM spa_center_review WHERE listing_id='$listing_id'");
                $row_pharma = $query_pharmacy->row_array();
                
               $rating = $row_pharma['avg_rating'];
               
                if ($rating === NULL) {
                    $rating = '0';
                }
                
             
                    
                    $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
               
                   // $package_list = array();
                    
                //  echo "SELECT * FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' order by id asc";
                   /* $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {*/
                        
                       
                            $package_id = $row['package_id'];
                            $package_name = $row['package_name'];
                            $package_details = $row['package_details'];
                            $price = $row['package_price'];
                            $discount = $row['package_discount'];
                            $image = $row['package_image'];
                            $package_times = $row['package_times'];
                            $delivery_type = $row['package_deliverytype'];
                            $categ_id = $row['package_categ_id'];
                            $spa_cat = $row['spa_package_category'];
                            
                            $types = array();
                            if(!empty($categ_id))
                            {
                                $exp=explode(',',$categ_id);
                                for($i=0;$i<count($exp);$i++)
                                {
                                    $c_id = $exp[$i];
                                    $query = $this->db->query("SELECT * FROM business_category WHERE id='$c_id'");
                                    $count = $query->num_rows();
                                    if ($count > 0) {
                                        $rowss=$query->row_array();
                                        $cat_id = $rowss['id'];
                                        $category = $rowss['category'];
                                        
                                        $types[] = array('type_id' =>$cat_id,'type' => $category); 
                                    }
                                }
                            }
                            
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            
                            $imgs = array();
                            $packi_query = $this->db->query("SELECT * FROM spa_package_img WHERE package_id='$package_id'");
                            $count_im = $packi_query->num_rows();
                            if ($count_im > 0) {
                                foreach ($packi_query->result_array() as $row_p) {
                                    $packi_id = $row_p['id'];
                                    $package_image = $row_p['image'];
                                    if(!empty($package_image))
                                    {
                                    $ext = getExtension($package_image);
                                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                                    if(in_array($ext, $img_format))
                                    {
                                        $type="image";
                                        $image='https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/'.$package_image;
                                    }
                                    else
                                    {
                                        $image='https://sandboxapi.medicalwale.com/v51/video.mp4';
                                        $type="video";
                                    }
                                    
                                    $imgs[] = array('image' =>$image ,
                                                    'type' =>$type
                                                   );
                                    }
                                    else
                                    {
                                       $imgs[] = array('image' => '',
                                                    'type' =>""
                                                   ); 
                                    }
                                }
                            }
                            
                            $categorys = array();
                            if(!empty($spa_cat))
                            {
                                $exp1=explode(',',$spa_cat);
                                for($i=0;$i<count($exp1);$i++)
                                {
                                    $c_id1 = $exp1[$i];
                                    $query1 = $this->db->query("SELECT * FROM spa_category WHERE id='$c_id1'");
                                    $count1 = $query1->num_rows();
                                    if ($count1 > 0) {
                                        $rowss1=$query1->row_array();
                                        $cat_id1 = $rowss1['id'];
                                        $category1 = $rowss1['category'];
                                        
                                        $categorys[] = array('cat_id' =>$cat_id1,'category' => $category1); 
                                    }
                                }
                            }
                            
                            /*$package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'discount' => $discount,
                                'package_image' => $imgs,
                                'package_times' => $package_times,
                                'delivery_type' => $delivery_type,
                                'types'      => $types,
                                'category'  => $categorys
                            );*/
                            
                                $resultpost[] = array(
                                    'branch_id' => $branch_id,
                                    'lat' => $lat,
                                    'lng' => $lng,
                                    'distance' => $distances,
                                    'category_id' => "",
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
                                    'email' => $branch_email,
                                    'image' => $branch_image,
                                    'user_discount' => $user_discount,
                                    'rating' => $rating,
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price,
                                    'discount' => $discount,
                                    'package_image' => $imgs,
                                    'package_times' => $package_times,
                                    'delivery_type' => $delivery_type,
                                    'types'      => $types,
                                    'category'  => $categorys
                                );
                                 return $resultpost;
                      /*  }
                    } 
    */
                }
                
            }
        }
        }else{
            
             return $resultpost = array(); 
        }
        
    }
    
    
    
    
    public function spa_add(){
        $sql = "SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'";
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
              
               $branch_id = $row['id'];   
              $data[]=$this->elasticsearch->add("spa_listing", $branch_id, $row);  
            }
        }
    }
    
    public function spa_center_select_order($user_id,$latitude, $longitude, $tab_id, $cat_type, $main_category, $sub_category, $sort, $page) {
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

        $where = "";
        if(!empty($tab_id))
        {
            $where .= " AND (fcb.massage_service='$tab_id' OR fcb.massage_service='2')";
        }
        if(!empty($cat_type))
        {
            $where .= " AND FIND_IN_SET('" . $cat_type . "', branch_business_category)";
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
        
        $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1' AND fcb.advertisement = '1' $where $limit", ($latitude), ($longitude), ($latitude), ($radius));
        
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
                //  $is_free_trail = $row['is_free_trail'];
                //  $from_trail_time = $row['from_trail_time'];
                //  $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '36';
                 $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM spa_center_review WHERE listing_id='$listing_id'");
                $row_pharma = $query_pharmacy->row_array();
                
               $rating = $row_pharma['avg_rating'];
               
                if ($rating === NULL) {
                    $rating = '0';
                }
                
                $reviews = $this->db->select('id')->from('spa_center_review')->where('listing_id', $listing_id)->get()->num_rows();
               
                $disc = $this->db->select('max(discount) as disc,max(price) as price')->from('packages')->where('user_id', $listing_id)->where('branch', $branch_id)->get()->row();
                if(!empty($disc))
                {
                    $disc1 = $disc->disc;
                    $price = $disc->price;
                }
                else
                {
                    $disc1 =0;
                    $price =0;
                }
                $profile_views =  '0' ;
                $user_discount = $row['user_discount'];

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
                $what_we_offer_array = array();
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                    if($what_we_offer != ""){
                        if(preg_match('/[0-9]/', $what_we_offer)){
                            $Query = $this->db->query("SELECT * FROM spa_services WHERE id='$what_we_offer'");
                            $what_we_offer_value = $Query->row()->services;
                            $what_we_offer_array[] = $what_we_offer_value;
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
                            $Query = $this->db->query("SELECT * FROM spa_facilites WHERE f_id='$facilities'");
                            $facilities_val = $Query->row()->facilities;
                            $facilities_array[] = $facilities_val;
                            
                        }
                        else{
                            $facilities_array[] = $facilities;
                        }
                    }
                }
                
                $branch_business_category_array = array();
                $branch_business = explode(',', $branch_business_category);
               
                foreach ($branch_business as $facilities) {
                    if($facilities !="")
                    {
                            $Query = $this->db->query("SELECT category,id FROM `business_category` WHERE `category_id` = 36 AND id='$facilities'");
                            $facilities_val = $Query->row()->category;
                            $branch_business_category_array[] = $facilities_val;
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
                $package_query = $this->db->query("SELECT * FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' order by id asc");
                $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id = $package_row['id'];
                            $package_name = $package_row['package_name'];
                            $package_details = $package_row['package_details'];
                            $price = $package_row['price'];
                            $discount = $package_row['discount'];
                            $image = $package_row['image'];
                            $package_times = $package_row['package_times'];
                            $delivery_type = $package_row['deliverytype'];
                            $categ_id = $package_row['categ_id'];
                            $spa_cat = $package_row['spa_package_category'];
                            
                            $types = array();
                            if(!empty($categ_id))
                            {
                                $exp=explode(',',$categ_id);
                                for($i=0;$i<count($exp);$i++)
                                {
                                    $c_id = $exp[$i];
                                    $query = $this->db->query("SELECT * FROM business_category WHERE id='$c_id'");
                                    $count = $query->num_rows();
                                    if ($count > 0) {
                                        $rowss=$query->row_array();
                                        $cat_id = $rowss['id'];
                                        $category = $rowss['category'];
                                        
                                        $types[] = array('type_id' =>$cat_id,'type' => $category); 
                                    }
                                }
                            }
                            
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            
                            $imgs = array();
                            $packi_query = $this->db->query("SELECT * FROM spa_package_img WHERE package_id='$package_id'");
                            $count_im = $packi_query->num_rows();
                            if ($count_im > 0) {
                                foreach ($packi_query->result_array() as $row_p) {
                                    $packi_id = $row_p['id'];
                                    $package_image = $row_p['image'];
                                    
                                    $imgs[] = array('image' => 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' .$package_image);
                                }
                            }
                            
                            $categorys = array();
                            if(!empty($spa_cat))
                            {
                                $exp1=explode(',',$spa_cat);
                                for($i=0;$i<count($exp1);$i++)
                                {
                                    $c_id1 = $exp1[$i];
                                    $query1 = $this->db->query("SELECT * FROM spa_category WHERE id='$c_id1'");
                                    $count1 = $query1->num_rows();
                                    if ($count1 > 0) {
                                        $rowss1=$query1->row_array();
                                        $cat_id1 = $rowss1['id'];
                                        $category1 = $rowss1['category'];
                                        
                                        $categorys[] = array('cat_id' =>$cat_id1,'category' => $category1); 
                                    }
                                }
                            }
                            
                            $package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'discount' => $discount,
                                'package_image' => $imgs,
                                'package_times' => $package_times,
                                'delivery_type' => $delivery_type,
                                'types'      => $types,
                                'category'  => $categorys
                            );
                    }
                } else {
                    $package_list = array();
                }


                  $spa_views = $this->db->select('id')->from('spa_views')->where('listing_id', $listing_id)->get()->num_rows();
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
                    $language = $row1['language'];
                    $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                  /*   $session1[] = array(
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
                         */
                           
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
                                        'language' => $language
                                       /*'package_trainer' => $final_session1*/
                                     );
                                       //'opening_hours' => $branch_final_Day);
                    
                }
                    
            }
            else {
               $sub_array1=array();
            }


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
                  //  'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'therapist_list' => $sub_array1,
                    'speciality' => $branch_business_category_array,
                    'current_time'=>$current_day_final,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                   // 'is_trial_order' => $is_trial_order,
                    'profile_views' => $spa_views,
                    'user_discount' => $disc1,
                    'price' => $price
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

        if($sort == "Distance")
        {
            array_sort_by_column($resultpost, 'distance');
        }
        
        if($sort == "Discount")
        {
            array_sort_by_column($resultpost, 'user_discount');
        }
        
        if($sort == "Price")
        {
            array_sort_by_column($resultpost, 'price');
        }

        return $resultpost;
    }
  
    public function spa_center_list_recommended_v2($user_id, $latitude, $longitude,$api_type, $tab_id, $main_category, $sub_category, $page, $sort,$category_type,$price) {
        $radius = '5';
        $resultpost = array();
       
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
         //Recommended
      
                $where1 = "";
               if($api_type ==1)
               {
                  $where1 .= "AND fcb.recommended='1' ";
               }
               elseif($api_type ==2) 
               {
                  $where1 .= "AND fcb.best_deals='1'"; 
               }
        
                $wh="";
                if($sort=="low")
                {
                    $wh .="order by p.price asc";
                }
                elseif($sort=="high")
                {
                    $wh .="order by p.price desc";
                }
                elseif($sort=="discount")
                {
                    $wh .="order by p.discount desc";
                }
                else
                {
                    $wh .="ORDER BY distance";
                }
                $where = "";
                if(!empty($tab_id))
                {
                    $where .= " AND (deliverytype = '$tab_id' OR deliverytype ='2')";
                }
                if(!empty($main_category) || !empty($sub_category))
                {
                    $where .= " AND (FIND_IN_SET('" . $main_category . "', spa_package_category) OR FIND_IN_SET('" . $sub_category . "', spa_package_subcategory))";
                }
                
                
                $cat_wh="";
                if(!empty($category_type))
                {
                  $category_id1=$category_type;
                  $final=explode(",",$category_id1);
                  for($i=0;$i<count($final);$i++)
                  {
                      $category_id2[]="'".$final[$i]."'";
                     
                  }
                  $category_id = implode(",",$category_id2);
                  $cat_wh .= "AND fcb.id IN ($category_id)";
                }
                
                $pricesort ="";
                if(!empty($price))
                {
                
                $pricesort1 =$price;
                $final=explode(",",$pricesort1);
                $pricesort .="AND p.price BETWEEN $final[0] AND $final[1]";
                }
                
                
           
             
            $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,p.price as package_price,p.discount as package_discount,p.package_name,p.package_details,p.image as package_image,p.package_times,p.deliverytype as package_deliverytype,p.categ_id as package_categ_id,p.spa_package_category,p.id as package_id, ( 6371 * acos( cos( radians($latitude) ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON (fcb.user_id= fc.user_id) INNER JOIN packages as p ON (fc.user_id=p.user_id AND p.v_id='36' AND p.branch=fcb.id) WHERE fc.is_active='1'  $where1 $where $cat_wh $pricesort HAVING distance < '%s' $wh $limit", ($latitude), ($longitude), ($latitude), ($radius));
            
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
                   
                    $listing_id = $row['user_id'];
                    $listing_type = '36';
                    $rating = '4.5';
                    $reviews = '0';
                    $profile_views =  '0' ;
                    $user_discount = $row['user_discount'];
    
                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    
                    $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM spa_center_review WHERE listing_id='$listing_id'");
                $row_pharma = $query_pharmacy->row_array();
                
               $rating = $row_pharma['avg_rating'];
               
                if ($rating === NULL) {
                    $rating = '0';
                }
                
             
                    
                    $distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
               
                   // $package_list = array();
                    
                //  echo "SELECT * FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' order by id asc";
                   /* $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {*/
                        
                       
                            $package_id = $row['package_id'];
                            $package_name = $row['package_name'];
                            $package_details = $row['package_details'];
                            $price = $row['package_price'];
                            $discount = $row['package_discount'];
                            $image = $row['package_image'];
                            $package_times = $row['package_times'];
                            $delivery_type = $row['package_deliverytype'];
                            $categ_id = $row['package_categ_id'];
                            $spa_cat = $row['spa_package_category'];
                            
                            $types = array();
                            if(!empty($categ_id))
                            {
                                $exp=explode(',',$categ_id);
                                for($i=0;$i<count($exp);$i++)
                                {
                                    $c_id = $exp[$i];
                                    $query = $this->db->query("SELECT * FROM business_category WHERE id='$c_id'");
                                    $count = $query->num_rows();
                                    if ($count > 0) {
                                        $rowss=$query->row_array();
                                        $cat_id = $rowss['id'];
                                        $category = $rowss['category'];
                                        
                                        $types[] = array('type_id' =>$cat_id,'type' => $category); 
                                    }
                                }
                            }
                            
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            
                            $imgs = array();
                            $packi_query = $this->db->query("SELECT * FROM spa_package_img WHERE package_id='$package_id'");
                            $count_im = $packi_query->num_rows();
                            if ($count_im > 0) {
                                foreach ($packi_query->result_array() as $row_p) {
                                    $packi_id = $row_p['id'];
                                    $package_image = $row_p['image'];
                                    if(!empty($package_image))
                                    {
                                    $ext = getExtension($package_image);
                                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                                    if(in_array($ext, $img_format))
                                    {
                                        $type="image";
                                        $image='https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/'.$package_image;
                                    }
                                    else
                                    {
                                        $image='https://sandboxapi.medicalwale.com/v51/video.mp4';
                                        $type="video";
                                    }
                                    
                                    $imgs[] = array('image' =>$image ,
                                                    'type' =>$type
                                                   );
                                    }
                                    else
                                    {
                                       $imgs[] = array('image' => '',
                                                    'type' =>""
                                                   ); 
                                    }
                                }
                            }
                            
                            $categorys = array();
                            if(!empty($spa_cat))
                            {
                                $exp1=explode(',',$spa_cat);
                                for($i=0;$i<count($exp1);$i++)
                                {
                                    $c_id1 = $exp1[$i];
                                    $query1 = $this->db->query("SELECT * FROM spa_category WHERE id='$c_id1'");
                                    $count1 = $query1->num_rows();
                                    if ($count1 > 0) {
                                        $rowss1=$query1->row_array();
                                        $cat_id1 = $rowss1['id'];
                                        $category1 = $rowss1['category'];
                                        
                                        $categorys[] = array('cat_id' =>$cat_id1,'category' => $category1); 
                                    }
                                }
                            }
                            
                            /*$package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'discount' => $discount,
                                'package_image' => $imgs,
                                'package_times' => $package_times,
                                'delivery_type' => $delivery_type,
                                'types'      => $types,
                                'category'  => $categorys
                            );*/
                            
                                $resultpost[] = array(
                                    'branch_id' => $branch_id,
                                    'lat' => $lat,
                                    'lng' => $lng,
                                    'distance' => $distances,
                                    'category_id' => $sub_category,
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
                                    'email' => $branch_email,
                                    'image' => $branch_image,
                                    'user_discount' => $user_discount,
                                    'rating' => $rating,
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price,
                                    'discount' => $discount,
                                    'package_image' => $imgs,
                                    'package_times' => $package_times,
                                    'delivery_type' => $delivery_type,
                                    'types'      => $types,
                                    'category'  => $categorys
                                );
                      /*  }
                    } 
    */
                }
            }
    
           
       
        return $resultpost;
    }
    
    public function spa_center_list_recommended_v3($user_id, $latitude, $longitude,$api_type, $tab_id, $main_category, $sub_category, $page, $sort) {
        $radius = '5';
        $resultpost = array();
       
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
       
        if($api_type ==1)
        {
             $where = "";
                if(!empty($tab_id))
                {
                    $where .= " AND (deliverytype = '$tab_id' OR deliverytype ='2')";
                }
                if(!empty($main_category) || !empty($sub_category))
                {
                    $where .= " AND (FIND_IN_SET('" . $main_category . "', spa_package_category) OR FIND_IN_SET('" . $sub_category . "', spa_package_subcategory))";
                }    
            
             $wh="";
                if($sort=="low")
                {
                    $wh .="order by p.price asc";
                }
                elseif($sort=="high")
                {
                    $wh .="order by p.price desc";
                }
                elseif($sort=="discount")
                {
                    $wh .="order by p.discount desc";
                }
                else
                {
                    $wh .="ORDER BY distance";
                }
            
            $sql = sprintf("SELECT fcb.id,fcb.user_id,fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,p.package_name,p.package_details,p.price,p.discount,( 6371 * acos( cos( radians($latitude) ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) INNER JOIN packages as p ON (fc.user_id=p.user_id AND p.v_id='36' AND p.branch=fcb.id)  WHERE fc.is_active='1'  AND fcb.recommended='1' $where HAVING distance < '%s' $wh $limit", ($latitude), ($longitude), ($latitude), ($radius));
            
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $branch_id = $row['id'];
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $listing_id = $row['user_id'];
                    $package_name = $row['package_name'];
                    $package_details = $row['package_details'];
                    $price = $row['price'];
                    $discount = $row['discount'];
                    $resultpost[] = array(
                                    'branch_id' => $branch_id,
                                    'lat' => $lat,
                                    'lng' => $lng,
                                    'listing_id' => $listing_id,
                                    'listing_type' => 36,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price,
                                    'discount' => $discount,
                                    );
                        }
                    
    
                }
            }
     
        
        return $resultpost;
    }
    public function other_branch_packages($user_id, $listing_id, $package_id,$package_name, $package_price,$branch_id) {
        $resultpost = array();
        $query = $this->db->query("SELECT * FROM spa_branch WHERE  user_id='$listing_id' AND id<>'$branch_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $branch_id = $row['id'];
                
                $sql = "SELECT fcb.branch_image,fcb.branch_name,fcb.branch_address,fcb.id,fcb.user_id,fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,p.package_name,p.package_details,p.price,p.discount,p.id as package_id FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) INNER JOIN packages as p ON (fc.user_id=p.user_id AND p.v_id='36' AND p.branch=fcb.id)  WHERE fc.is_active='1' AND p.package_name='$package_name' AND p.price='$package_price' AND p.id != '$package_id'";
            
            $query = $this->db->query($sql);
            $count = $query->num_rows();
            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $branch_id = $row['id'];
                    $package_ids = $row['package_id'];
                    $branch_name = $row['branch_name'];
                    $branch_address = $row['branch_address'];
                    $lat = $row['lat'];
                    $lng = $row['lng'];
                    $listing_id = $row['user_id'];
                    $package_name = $row['package_name'];
                    $package_details = $row['package_details'];
                    $price = $row['price'];
                    $discount = $row['discount'];
                    $branch_image = $row['branch_image'];
                    $rating = '4.5';
                    if ($branch_image != '') {
                    $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    $resultpost[] = array(
                                    'branch_id' => $branch_id,
                                    'branch_name' => $branch_name,
                                    'branch_address' => $branch_address,
                                    'lat' => $lat,
                                    'lng' => $lng,
                                    'listing_id' => $listing_id,
                                    'listing_type' => 36,
                                    'image'        => $branch_image,
                                    'rating'      => $rating,
                                    'package_id' => $package_ids,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price,
                                    'discount' => $discount,
                                    );
                        }
                    
    
                }
            }
        }

        return $resultpost;
    }
    public function spa_center_details_v2($user_id, $listing_id,$branch_id) {
       $sql = "SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.user_id='$listing_id' AND fcb.id='$branch_id'";
        
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            $row = $query->row_array();
            
            $center_name = $row['branch_name'];
            $main_contact = $row['branch_phone'];
            $enquiry_number = '';
            $main_email = $row['branch_email'];
            $main_image = $row['branch_image'];
            $listing_type = '6';
            $total_rating = '4.5';
            $total_review = '0';
            $total_profile_views = '0';
            
                
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
                
                //  $is_free_trail = $row['is_free_trail'];
                //  $from_trail_time = $row['from_trail_time'];
                //  $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '36';
                $rating = '4.5';
                $reviews = '0';
                $profile_views =  '0' ;
                $user_discount = $row['user_discount'];

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
                $what_we_offer_array = array();
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                    if($what_we_offer != ""){
                        if(preg_match('/[0-9]/', $what_we_offer)){
                            $Query = $this->db->query("SELECT * FROM spa_services WHERE id='$what_we_offer'");
                            $what_we_offer_value = $Query->row()->services;
                            $what_we_offer_array[] = $what_we_offer_value;
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
                            $Query = $this->db->query("SELECT * FROM spa_facilites WHERE f_id='$facilities'");
                            $facilities_val = $Query->row()->facilities;
                            $facilities_array[] = $facilities_val;
                            
                        }
                        else{
                            $facilities_array[] = $facilities;
                        }
                    }
                }
                
                $branch_business_category_array = array();
                $branch_business = explode(',', $branch_business_category);
               
                foreach ($branch_business as $facilities) {
                    if($facilities !="")
                    {
                            $Query = $this->db->query("SELECT category,id FROM `business_category` WHERE `category_id` = 36 AND id='$facilities'");
                            $facilities_val = $Query->row()->category;
                            $branch_business_category_array[] = $facilities_val;
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
                $package_query = $this->db->query("SELECT * FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' order by id asc");
                      $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id = $package_row['id'];
                            $package_name = $package_row['package_name'];
                            $package_details = $package_row['package_details'];
                            $price = $package_row['price'];
                            $discount = $package_row['discount'];
                            $image = $package_row['image'];
                            $package_times = $package_row['package_times'];
                            $delivery_type = $package_row['deliverytype'];
                            $categ_id = $package_row['categ_id'];
                            $spa_cat = $package_row['spa_package_category'];
                            
                            $types = array();
                            if(!empty($categ_id))
                            {
                                $exp=explode(',',$categ_id);
                                for($i=0;$i<count($exp);$i++)
                                {
                                    $c_id = $exp[$i];
                                    $query = $this->db->query("SELECT * FROM business_category WHERE id='$c_id'");
                                    $count = $query->num_rows();
                                    if ($count > 0) {
                                        $rowss=$query->row_array();
                                        $cat_id = $rowss['id'];
                                        $category = $rowss['category'];
                                        
                                        $types[] = array('type_id' =>$cat_id,'type' => $category); 
                                    }
                                }
                            }
                            
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            
                            $imgs = array();
                            $packi_query = $this->db->query("SELECT * FROM spa_package_img WHERE package_id='$package_id'");
                            $count_im = $packi_query->num_rows();
                            if ($count_im > 0) {
                                foreach ($packi_query->result_array() as $row_p) {
                                    $packi_id = $row_p['id'];
                                    $package_image = $row_p['image'];
                                    
                                    $imgs[] = array('image' => 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' .$package_image);
                                }
                            }
                            
                            $categorys = array();
                            if(!empty($spa_cat))
                            {
                                $exp1=explode(',',$spa_cat);
                                for($i=0;$i<count($exp1);$i++)
                                {
                                    $c_id1 = $exp1[$i];
                                    $query1 = $this->db->query("SELECT * FROM spa_category WHERE id='$c_id1'");
                                    $count1 = $query1->num_rows();
                                    if ($count1 > 0) {
                                        $rowss1=$query1->row_array();
                                        $cat_id1 = $rowss1['id'];
                                        $category1 = $rowss1['category'];
                                        
                                        $categorys[] = array('cat_id' =>$cat_id1,'category' => $category1); 
                                    }
                                }
                            }
                            
                            $package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'discount' => $discount,
                                'package_image' => $imgs,
                                'package_times' => $package_times,
                                'delivery_type' => $delivery_type,
                                'types'      => $types,
                                'category'  => $categorys
                            );
                    }
                } else {
                    $package_list = array();
                }

                  $spa_views = $this->db->select('id')->from('spa_center_review')->where('listing_id', $listing_id)->get()->num_rows();
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
                    $language = $row1['language'];
                    $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                  /*   $session1[] = array(
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
                         */
                           
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
                                        'language' => $language,
                                       /*'package_trainer' => $final_session1*/
                                     );
                                       //'opening_hours' => $branch_final_Day);
                    
                }
                    
            }
            else {
               $sub_array1=array();
            }


                //$distances = str_replace(',', '.', GetDrivingDistance($latitude, $lat, $longitude, $lng));
                $enquiry_number = "9619146163";
                $resultpost1[] = array(
                    'branch_id' => $branch_id,
                    'lat' => $lat,
                    'lng' => $lng,
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
                  //  'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'therapist_list' => $sub_array1,
                    'speciality' => $branch_business_category_array,
                    'current_time'=>$current_day_final,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                   // 'is_trial_order' => $is_trial_order,
                    'profile_views' => $spa_views,
                    'user_discount' => $user_discount
                );
                
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
                    "other_branch" => $resultpost1
                );
            
        } else {
             $resultpost = array(
                "status" => 200,
                "message" => "success",
                "other_branch" => array()
            );
        }

      /*  function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
            $sort_col = array();
            foreach ($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }
            array_multisort($sort_col, $dir, $arr);
        }

        array_sort_by_column($resultpost, 'distance');*/

        return $resultpost;
    }
   
    public function spa_category_v2(){
       /* $query = $this->db->query("SELECT * FROM business_category WHERE category_id='36'");
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
        
        */
		$stmt = "SELECT * FROM business_category WHERE category_id='36' AND sub_category=0";
		
		$query = $this->db->query($stmt);
		
		foreach($query->result_array() as $row){
		     $data = array();
		      $category_id = $row['id'];
                $category_name = $row['category'];
                $details = $row['details'];
                $image = $row['image'];
               
		    $prods = "SELECT * FROM business_category WHERE sub_category = '$category_id'";
		    $query1 = $this->db->query($prods);
		    
		    if($query1->num_rows() > 0){
		        
		        foreach($query1->result_array() as $row1){
		        
		        $category_id1 = $row1['id'];
                $category_name1 = $row1['category'];
                $details1 = $row1['details'];
                $image1 = $row1['image'];
		        $data[] =  array(
                                'sub_category_id' => $category_id1,
                                'sub_category_name' => $category_name1,
                                'sub_category_detail' => $details1,
                                'sub_image' => $image1,
                                "discount"=>"10"
                );
		        }
		    }
		   
		     $result_list[] = array(
                                'category_id' => $category_id,
                                'category_name' => $category_name,
                                'category_detail' => $details,
                                'image' => $image,
                                'sub_category' =>  $data,
                                
                );
		   
		}
        
		return $result_list;
    }
    
    public function spa_package($user_id,$package_id) {
    
         
       
        $resultpost = array();
       
     

             
            $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,p.price as package_price,p.discount as package_discount,p.package_name,p.package_details,p.image as package_image,p.package_times,p.deliverytype as package_deliverytype,p.categ_id as package_categ_id,p.spa_package_category,p.id as package_id FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON (fcb.user_id= fc.user_id) INNER JOIN packages as p ON (fc.user_id=p.user_id AND p.v_id='36' AND p.branch=fcb.id) WHERE p.id='$package_id' and  fc.is_active='1'");
            
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
                   
                    $listing_id = $row['user_id'];
                    $listing_type = '36';
                    $rating = '4.5';
                    $reviews = '0';
                    $profile_views =  '0' ;
                    $user_discount = $row['user_discount'];
    
                    if ($branch_image != '') {
                        $branch_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_image;
                    } else {
                        $branch_image = '';
                    }
                    
                        $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM spa_center_review WHERE listing_id='$listing_id'");
                        $row_pharma = $query_pharmacy->row_array();
                        
                       $rating = $row_pharma['avg_rating'];
                       
                        if ($rating === NULL) {
                            $rating = '0';
                        }
                
             
                       
                            $package_id = $row['package_id'];
                            $package_name = $row['package_name'];
                            $package_details = $row['package_details'];
                            $price = $row['package_price'];
                            $discount = $row['package_discount'];
                            $image = $row['package_image'];
                            $package_times = $row['package_times'];
                            $delivery_type = $row['package_deliverytype'];
                            $categ_id = $row['package_categ_id'];
                            $spa_cat = $row['spa_package_category'];
                            
                            $types = array();
                            if(!empty($categ_id))
                            {
                                $exp=explode(',',$categ_id);
                                for($i=0;$i<count($exp);$i++)
                                {
                                    $c_id = $exp[$i];
                                    $query = $this->db->query("SELECT * FROM business_category WHERE id='$c_id'");
                                    $count = $query->num_rows();
                                    if ($count > 0) {
                                        $rowss=$query->row_array();
                                        $cat_id = $rowss['id'];
                                        $category = $rowss['category'];
                                        
                                        $types[] = array('type_id' =>$cat_id,'type' => $category); 
                                    }
                                }
                            }
                            
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            
                            $imgs = array();
                            $packi_query = $this->db->query("SELECT * FROM spa_package_img WHERE package_id='$package_id'");
                            $count_im = $packi_query->num_rows();
                            if ($count_im > 0) {
                                foreach ($packi_query->result_array() as $row_p) {
                                    $packi_id = $row_p['id'];
                                    $package_image = $row_p['image'];
                                    if(!empty($package_image))
                                    {
                                    $ext = getExtension($package_image);
                                    $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                                    if(in_array($ext, $img_format))
                                    {
                                        $type="image";
                                        $image='https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/'.$package_image;
                                    }
                                    else
                                    {
                                        $image='https://sandboxapi.medicalwale.com/v51/video.mp4';
                                        $type="video";
                                    }
                                    
                                    $imgs[] = array('image' =>$image ,
                                                    'type' =>$type
                                                   );
                                    }
                                    else
                                    {
                                       $imgs[] = array('image' => '',
                                                    'type' =>""
                                                   ); 
                                    }
                                }
                            }
                            
                            $categorys = array();
                            if(!empty($spa_cat))
                            {
                                $exp1=explode(',',$spa_cat);
                                for($i=0;$i<count($exp1);$i++)
                                {
                                    $c_id1 = $exp1[$i];
                                    $query1 = $this->db->query("SELECT * FROM spa_category WHERE id='$c_id1'");
                                    $count1 = $query1->num_rows();
                                    if ($count1 > 0) {
                                        $rowss1=$query1->row_array();
                                        $cat_id1 = $rowss1['id'];
                                        $category1 = $rowss1['category'];
                                        
                                        $categorys[] = array('cat_id' =>$cat_id1,'category' => $category1); 
                                    }
                                }
                            }
                            
                                $resultpost[] = array(
                                    'branch_id' => $branch_id,
                                    'lat' => $lat,
                                    'lng' => $lng,
                                    'distance' => "",
                                    'category_id' => "",
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
                                    'email' => $branch_email,
                                    'image' => $branch_image,
                                    'user_discount' => $user_discount,
                                    'rating' => $rating,
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price,
                                    'discount' => $discount,
                                    'package_image' => $imgs,
                                    'package_times' => $package_times,
                                    'delivery_type' => $delivery_type,
                                    'types'      => $types,
                                    'category'  => $categorys
                                );
                      /*  }
                    } 
    */
                }
            }
    
           
       
        return $resultpost;
    
  
        
    }
    
    public function view_all_list_typewise($user_id, $latitude, $longitude, $tab_id, $cat_type, $main_category, $sub_category, $sort, $page) {
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
  
        $where = "";
        if(!empty($tab_id))
        {
            $where .= " AND (fcb.massage_service='$tab_id' OR fcb.massage_service='2')";
        }
        if(!empty($cat_type))
        {
            $where .= " AND FIND_IN_SET('" . $cat_type . "', branch_business_category)";
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
        
        $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1' $where HAVING distance < '5' ORDER BY distance $limit", ($latitude), ($longitude), ($latitude), ($radius));
        
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
                //  $is_free_trail = $row['is_free_trail'];
                //  $from_trail_time = $row['from_trail_time'];
                //  $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '36';
                 $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM spa_center_review WHERE listing_id='$listing_id'");
                $row_pharma = $query_pharmacy->row_array();
                
               $rating = $row_pharma['avg_rating'];
               
                if ($rating === NULL) {
                    $rating = '0';
                }
                
                $reviews = $this->db->select('id')->from('spa_center_review')->where('listing_id', $listing_id)->get()->num_rows();
               
                $profile_views =  '0' ;
                $user_discount = $row['user_discount'];

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
                $what_we_offer_array = array();
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                    if($what_we_offer != ""){
                        if(preg_match('/[0-9]/', $what_we_offer)){
                            $Query = $this->db->query("SELECT * FROM spa_services WHERE id='$what_we_offer'");
                            $what_we_offer_value = $Query->row()->services;
                            $what_we_offer_array[] = $what_we_offer_value;
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
                            $Query = $this->db->query("SELECT * FROM spa_facilites WHERE f_id='$facilities'");
                            $facilities_val = $Query->row()->facilities;
                            $facilities_array[] = $facilities_val;
                            
                        }
                        else{
                            $facilities_array[] = $facilities;
                        }
                    }
                }
                
                $branch_business_category_array = array();
                $branch_business = explode(',', $branch_business_category);
               
                foreach ($branch_business as $facilities) {
                    if($facilities !="")
                    {
                            $Query = $this->db->query("SELECT category,id FROM `business_category` WHERE `category_id` = 36 AND id='$facilities'");
                            $facilities_val = $Query->row()->category;
                            $branch_business_category_array[] = $facilities_val;
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
                $package_query = $this->db->query("SELECT * FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' order by id asc");
                  //echo "SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND FIND_IN_SET('".$sub_category."',categ_id ) order by id asc";
                    $package_count = $package_query->num_rows();
                    if ($package_count > 0) {
                        foreach ($package_query->result_array() as $package_row) {
                            $package_id = $package_row['id'];
                            $package_name = $package_row['package_name'];
                            $package_details = $package_row['package_details'];
                            $price = $package_row['price'];
                            $discount = $package_row['discount'];
                            $image = $package_row['image'];
                            $package_times = $package_row['package_times'];
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            if ($image != '') {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                            } else {
                                $image = '';
                            }
                         
                            $package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'discount' => $discount,
                                'image' => $image,
                                'package_times' => $package_times
                            );
                    }
                } else {
                    $package_list = array();
                }



                  $spa_views = $this->db->select('id')->from('spa_views')->where('listing_id', $listing_id)->get()->num_rows();
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
                    $language = $row1['language'];
                    
                    $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                  /*   $session1[] = array(
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
                         */
                           
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
                                        'language' => $language
                                       /*'package_trainer' => $final_session1*/
                                     );
                                       //'opening_hours' => $branch_final_Day);
                    
                }
                    
            }
            else {
               $sub_array1=array();
            }


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
                  //  'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'therapist_list' => $sub_array1,
                    'speciality' => $branch_business_category_array,
                    'current_time'=>$current_day_final,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                   // 'is_trial_order' => $is_trial_order,
                    'profile_views' => $spa_views,
                    'user_discount' => $user_discount,
                    'price' => "100"
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
        
        if($sort == "Distance")
        {
            array_sort_by_column($resultpost, 'distance');
        }
        
        if($sort == "Discount")
        {
            array_sort_by_column($resultpost, 'user_discount');
        }
        
        if($sort == "Price")
        {
            array_sort_by_column($resultpost, 'price');
        }
        

        return $resultpost;
    }
   
    public function add_bookings($user_id, $patient_id, $listing_id, $package_id, $branch_id, $category_id, $trail_booking_date, $trail_booking_time, $user_name, $user_mobile, $user_email, $user_gender,$user_age,$trainer_id,$address_id,$instruction,$amt) {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id = date("YmdHis");
        $date = date('Y-m-d H:i:s');
        
       /* $questions = json_decode($question);
        $pre_final_que = $questions->quastion;
        $final_que = $pre_final_que[0];
        $q_user_id = $final_que->user_id;
        $final_q = $final_que->qas;
        $data2 = array();
        $resp = $this->delete_question($q_user_id);
        for ($i = 0; $i < sizeof($final_q); $i++) {

                $data2['user_id'] = $q_user_id;
                $data2['question_id'] = $final_q[$i]->qid;
                $data2['answer'] = $final_q[$i]->qans;

                $resp = $this->update_userprofile_question($data2);
        }
            */
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'package_id' => $package_id,
            'listing_id' => $listing_id,
            'user_id'    => $user_id,
            'patient_id' => $patient_id,
            'trainer_id' => $trainer_id,
            'branch_id'  => $branch_id,
            'vendor_id'  => '36',
            'category_id' => $category_id,
            'trail_booking_date' => $trail_booking_date,
            'trail_booking_time' => $trail_booking_time,
            'user_name' => $user_name,
            'user_mobile' => $user_mobile,
            'user_email' => $user_email,
            'user_gender' => $user_gender,
            'booking_date' => $date,
            'booking_address' => $address_id,
            'instruction'=>$instruction,
            'status'=>'1',
            'amt'=>$amt
            
        );
       
       if($patient_id != "")
       {
           $insert_user_data = $this->db->where('id', $patient_id)->update('users', array(
                         'age'   => $user_age
                    ));
       }
       else
       {
            $insert_user_data = $this->db->where('id', $user_id)->update('users', array(
                       
                         'age'   => $user_age,
                       
                         
                    ));
       }

        $insert = $this->db->insert('booking_master', $booking_master_array);
        $order_id = $this->db->insert_id();
        
        
        
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
        if ($order_id > 0) {
        
          
            $order_date = date('j M Y h:i A', strtotime($date));
            $order_info = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $order_info->token_status;
            if ($token_status > 0) {
                $reg_id = $order_info->token;
                $agent = $order_info->agent;
                $msg = 'Your Spa appointment has been booked Kindly wait for the confirmation';
                $img_url = '';
                $tag = 'text';
                $key_count = '1';
                $title = 'Appointment Booked';
                $order_status='Awaiting Confirmation';
                
        //          //added by zak for all notification therough services
                 $notification_array = array(
                       'title' => $title,
                       'msg'  => $msg,
                       'img_url' => $img_url,
                       'tag' => $tag,
                       'order_status' => $order_status,
                       'order_date' => $order_date,
                       'order_id'   => $order_id,
                       'post_id'  => "",
                       'listing_id'  => "",
                       'booking_id'  => "",
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'spa_booking',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
             );
          $this->db->insert('All_notification_Mobile', $notification_array);
        // //end 
                
                //send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $booking_id, $name, $listing_name, $agent);
            }
              
        }
        
        
        

        $query_branch = $this->db->query("SELECT `branch_name`,`branch_phone` FROM spa_branch WHERE id='$branch_id'");
        $row_branch = $query_branch->row_array();
        $branch_name = $row_branch['branch_name'];
        $branch_phone = $row_branch['branch_phone'];
       
        $date_ = $trail_booking_date . ' ' . $trail_booking_time;
       

        //web notification starts
        $fitness_booking_notifications = array(
            'listing_id' => $listing_id,
            'order_id' => $order_id,
            'title' => 'New Booking',
            'msg' => 'You Have Received a New booking',
            'image' => '',
            'notification_type' => 'spa_bookings',
            'order_status' => '',
            'order_date' => $date,
            'invoice_no' => $booking_id
        );
        $this->db->insert('pharmacy_notifications', $fitness_booking_notifications);
        //web notification ends   

        if ($insert) {
            $message = 'Dear customer, your booking is confirmed at Spa Center ' . $branch_name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.';
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
            $login_url = 'https://spa.medicalwale.com';
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
                        
                        $connect_type ="spa_cancel";
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
            $title        = 'Cancelled an Spa Booking';
            $msg          = $user_name . ' Your spa appointment has been cancelled' ;
            $title_web    = 'Spa Cancel';
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
                      'notification_type'  => 'spa_cancel',
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
   
   
   
   
    public function spa_trainer_list($user_id,$listing_id,$gender)
    {
            $sub_array = array();
            $where = "";
            if(!empty($gender))
            {
                $where .= " AND gender='$gender'";
            }
            
            $personal_trainer = $this->db->query("SELECT * FROM personal_trainers WHERE user_id='$listing_id' $where ");
            $count = $personal_trainer->num_rows();
            if ($count > 0) 
            {
                foreach ($personal_trainer->result_array() as $row) 
                {
                    $id = $row['id'];
                    $category_id = $row['category_id'];
                    
                    $category_name = array();
                    if($category_id != NULL)
                    {
                        $cat = explode(',',$category_id);
                        for($i=0;$i<count($cat);$i++)
                        {
                            $sql = $this->db->select('category')->from('business_category')->where('id', $cat[$i])->get()->row()->category;
                            $category_name[]=$sql;
                        }
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
                    $language = $row['language'];
                    $personal_trainer_lanaguge =$this->db->query("SELECT * from languages  where id IN ('$language')"); 
                    $row122_lang=$personal_trainer_lanaguge->row_array();
                    $language_name=$row122_lang['name'];
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
                                       'opening_hours' => $branch_final_Day,
                                       'language' => $language_name
                                       );
                                       
                    
                }
                return $sub_array;    
            }
            else {
                return array();
            }
    }
    //---------------------------------------------old ------------------------------------------
    
    public function spa_center_related_list($user_id, $latitude, $longitude,$listing_id) {
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
         
            // $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time, ( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'   HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
             $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
       
            }
            else
            {
                //mumbai lat long in worst condition
                $latitude       = '19.1286564';
                $longitude        = '72.8509587';
                //$sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng, fcb.is_free_trail, fcb.from_trail_time,fcb.user_discount, fcb.to_trail_time, ( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM fitness_center_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'   HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
                $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
       
                
             }
            }
        }
        else
        {
        $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,fcb.massage_service,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1'  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
       
            
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
                //  $is_free_trail = $row['is_free_trail'];
                //  $from_trail_time = $row['from_trail_time'];
                //  $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '36';
                $rating = '4.5';
                $reviews = '0';
                $profile_views =  '0' ;
                $user_discount = $row['user_discount'];

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
                $what_we_offer_array = array();
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                    if($what_we_offer != ""){
                        if(preg_match('/[0-9]/', $what_we_offer)){
                            $Query = $this->db->query("SELECT * FROM spa_services WHERE id='$what_we_offer'");
                            $what_we_offer_value = $Query->row()->services;
                            $what_we_offer_array[] = $what_we_offer_value;
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
                            $Query = $this->db->query("SELECT * FROM spa_facilites WHERE f_id='$facilities'");
                            $facilities_val = $Query->row()->facilities;
                            $facilities_array[] = $facilities_val;
                            
                        }
                        else{
                            $facilities_array[] = $facilities;
                        }
                    }
                }
                
                $branch_business_category_array = array();
                $branch_business = explode(',', $branch_business_category);
               
                foreach ($branch_business as $facilities) {
                    if($facilities !="")
                    {
                            $Query = $this->db->query("SELECT category,id FROM `business_category` WHERE `category_id` = 36 AND id='$facilities'");
                            $facilities_val = $Query->row()->category;
                            $branch_business_category_array[] = $facilities_val;
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
                  $package_query = $this->db->query("SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND FIND_IN_SET(categ_id,'" . $branch_business_category . "')  order by id asc");
              //echo "SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND categ_id='$category' order by id asc";
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



                  $spa_views = $this->db->select('id')->from('spa_views')->where('listing_id', $listing_id)->get()->num_rows();
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
                    $language = $row1['language'];
                    $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                  /*   $session1[] = array(
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
                         */
                           
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
                                        'rating' => $rating
                                       /*'package_trainer' => $final_session1*/
                                     );
                                       //'opening_hours' => $branch_final_Day);
                    
                }
                    
            }
            else {
               $sub_array1=array();
            }


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
                  //  'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'therapist_list' => $sub_array1,
                    'speciality' => $branch_business_category_array,
                    'current_time'=>$current_day_final,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                   // 'is_trial_order' => $is_trial_order,
                    'profile_views' => $spa_views,
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
    
    public function spa_center_list($user_id, $latitude, $longitude, $category) {
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

        // $sql = sprintf("SELECT `id`, `user_id`, `branch_name`, `branch_image`, `branch_phone`, `branch_email`, `about_branch`, `branch_business_category`, `branch_offer`, `branch_facilities`, `branch_address`, `opening_hours`, `pincode`, `state`, `city`, `lat`, `lng`,`user_discount`, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM spa_branch  WHERE  FIND_IN_SET('" . $category . "', branch_business_category)  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
           
        $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fc.image as branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1' AND FIND_IN_SET('" . $category . "', branch_business_category)  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
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
                //  $is_free_trail = $row['is_free_trail'];
                //  $from_trail_time = $row['from_trail_time'];
                //  $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '36';
                $rating = '4.5';
                $reviews = '0';
                $profile_views =  '0' ;
                $user_discount = $row['user_discount'];

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
                            $Query = $this->db->query("SELECT * FROM spa_services WHERE id='$what_we_offer'");
                            $what_we_offer_value = $Query->row()->services;
                            $what_we_offer_array[] = $what_we_offer_value;
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
                            $Query = $this->db->query("SELECT * FROM spa_facilites WHERE f_id='$facilities'");
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
              //echo "SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND categ_id='$category' order by id asc";
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

                  $spa_views = $this->db->select('id')->from('spa_views')->where('listing_id', $listing_id)->get()->num_rows();
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
                   // 'is_free_trail' => $is_free_trail,
                  //  'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                   // 'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                   // 'is_trial_order' => $is_trial_order,
                    'profile_views' => $spa_views,
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
    
    public function spa_center_bachat_list($user_id, $latitude, $longitude, $category, $type) {
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

        // $sql = sprintf("SELECT `id`, `user_id`, `branch_name`, `branch_image`, `branch_phone`, `branch_email`, `about_branch`, `branch_business_category`, `branch_offer`, `branch_facilities`, `branch_address`, `opening_hours`, `pincode`, `state`, `city`, `lat`, `lng`,`user_discount`, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM spa_branch  WHERE  FIND_IN_SET('" . $category . "', branch_business_category)  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
        //if($type==='bachat'){
            $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fcb.user_discount!='0' AND fc.is_active='1' AND FIND_IN_SET('" . $category . "', branch_business_category)  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
        //}
        //else {
        //    $sql = sprintf("SELECT fcb.id,fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng,fcb.user_discount,( 6371 * acos( cos( radians('%s') ) * cos( radians( fcb.lat ) ) * cos( radians( fcb.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( fcb.lat ) ) ) ) AS distance FROM spa_branch as fcb LEFT JOIN fitness_center as fc ON(fcb.user_id= fc.user_id) WHERE fc.is_active='1' AND FIND_IN_SET('" . $category . "', branch_business_category)  HAVING distance < '5' ORDER BY distance LIMIT 0 , 20", ($latitude), ($longitude), ($latitude), ($radius));
        //}
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
                //  $is_free_trail = $row['is_free_trail'];
                //  $from_trail_time = $row['from_trail_time'];
                //  $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '36';
                $rating = '4.5';
                $reviews = '0';
                $profile_views = '0';
                $user_discount = $row['user_discount'];

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
                            $Query = $this->db->query("SELECT * FROM spa_services WHERE id='$what_we_offer'");
                            $what_we_offer_value = $Query->row()->services;
                            $what_we_offer_array[] = $what_we_offer_value;
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
                            $Query = $this->db->query("SELECT * FROM spa_facilites WHERE f_id='$facilities'");
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
                   // 'is_free_trail' => $is_free_trail,
                  //  'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                   // 'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'opening_day' => $final_Day,
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


            $query_branch = $this->db->query("SELECT fcb.branch_business_category,fcb.id, fcb.user_id, fcb.branch_name, fcb.branch_image, fcb.branch_phone, fcb.branch_email, fcb.about_branch, fcb.branch_business_category AS category_id,business_category.category, fcb.branch_offer, fcb.branch_facilities, fcb.branch_address, fcb.opening_hours, fcb.pincode, fcb.state, fcb.city, fcb.lat, fcb.lng FROM `spa_branch` AS fcb LEFT JOIN business_category ON fcb.branch_business_category= business_category.id  WHERE user_id='$listing_id'");
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
                    $branch_business_category = $row['branch_business_category'];
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


  $dfinal=date("l");
            $current_day_final="";
            foreach($final_Day as $key => $product)
            {
            
            if($product['day']===$dfinal)
            {
             $current_day_final = $product['time'][0];
            }
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

  
                $branch_business_category_array = array();
                $branch_business = explode(',', $branch_business_category);
               
                foreach ($branch_business as $facilities) {
                    if($facilities !="")
                    {
                            $Query = $this->db->query("SELECT category,id FROM `business_category` WHERE `category_id` = 36 AND id='$facilities'");
                            $facilities_val = $Query->row()->category;
                            $branch_business_category_array[] = $facilities_val;
                    }
                    
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
                            $discount = $package_row['discount'];
                            $image = $package_row['image'];
                            $package_times = $package_row['package_times'];
                            $delivery_type = $package_row['deliverytype'];
                            $categ_id = $package_row['categ_id'];
                            $spa_cat = $package_row['spa_package_category'];
                            
                            $types = array();
                            if(!empty($categ_id))
                            {
                                $exp=explode(',',$categ_id);
                                for($i=0;$i<count($exp);$i++)
                                {
                                    $c_id = $exp[$i];
                                    $query = $this->db->query("SELECT * FROM business_category WHERE id='$c_id'");
                                    $count = $query->num_rows();
                                    if ($count > 0) {
                                        $rowss=$query->row_array();
                                        $cat_id = $rowss['id'];
                                        $category = $rowss['category'];
                                        
                                        $types[] = array('type_id' =>$cat_id,'type' => $category); 
                                    }
                                }
                            }
                            
                            if($package_times == NULL)
                            {
                                $package_times ="";
                            }
                            
                            $imgs = array();
                            $packi_query = $this->db->query("SELECT * FROM spa_package_img WHERE package_id='$package_id'");
                            $count_im = $packi_query->num_rows();
                            if ($count_im > 0) {
                                foreach ($packi_query->result_array() as $row_p) {
                                    $packi_id = $row_p['id'];
                                    $package_image = $row_p['image'];
                                    
                                    $imgs[] = array('image' => 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' .$package_image);
                                }
                            }
                            
                            $categorys = array();
                            if(!empty($spa_cat))
                            {
                                $exp1=explode(',',$spa_cat);
                                for($i=0;$i<count($exp1);$i++)
                                {
                                    $c_id1 = $exp1[$i];
                                    $query1 = $this->db->query("SELECT * FROM spa_category WHERE id='$c_id1'");
                                    $count1 = $query1->num_rows();
                                    if ($count1 > 0) {
                                        $rowss1=$query1->row_array();
                                        $cat_id1 = $rowss1['id'];
                                        $category1 = $rowss1['category'];
                                        
                                        $categorys[] = array('cat_id' =>$cat_id1,'category' => $category1); 
                                    }
                                }
                            }
                            
                            $package_list[] = array(
                                'package_id' => $package_id,
                                'package_name' => $package_name,
                                'package_details' => $package_details,
                                'price' => $price,
                                'discount' => $discount,
                                'package_image' => $imgs,
                                'package_times' => $package_times,
                                'delivery_type' => $delivery_type,
                                'types'      => $types,
                                'category'  => $categorys
                            );
                    }
                } else {
                    $package_list = array();
                }

                   /* $booking_slot_array = array();
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
*/
                    $is_order_query = $this->db->query("SELECT id FROM `booking_master` WHERE listing_id='$listing_id' AND branch_id='$branch_id' AND category_id='$category_id' AND user_id='$user_id' AND package_id='100'");
                    $is_order_count = $is_order_query->num_rows();

                    if ($is_order_count > 0) {
                        $is_trial_order = 1;
                    } else {
                        $is_trial_order = 0;
                    }
                    $profile_views = $this->db->select('id')->from('spa_views')->where('listing_id', $listing_id)->get()->num_rows();
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
                    $language = $row1['language'];
                    
                    $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                  /*   $session1[] = array(
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
                         */
                           
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
                                        'language' => $language
                                       /*'package_trainer' => $final_session1*/
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
                        'gallery' => $gallery_array,
                        'what_we_offer' => $what_we_offer_array,
                        'facilities' => $facilities_array,
                        'package_list' => $package_list,
                        'therapist_list' => $sub_array1,
                        'speciality' => $branch_business_category_array,
                        'current_time'=>$current_day_final,
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

//echo "SELECT `id`, `user_id`, `branch_name`, `branch_image`, `branch_phone`, `branch_email`, `about_branch`, `branch_business_category`, `branch_offer`, `branch_facilities`, `branch_address`, `opening_hours`, `pincode`, `state`, `city`, `lat`, `lng` FROM `spa_branch` WHERE   FIND_IN_SET('" . $category_id . "', branch_business_category)  AND user_id='$listing_id' AND id<>'$branch_id'";
       // FIND_IN_SET('" . $category_id . "', branch_business_category)  AND 
        $query = $this->db->query("SELECT * FROM spa_branch WHERE  user_id='$listing_id' AND id<>'$branch_id'");
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
                //  $is_free_trail = $row['is_free_trail'];
                //  $from_trail_time = $row['from_trail_time'];
                //  $to_trail_time = $row['to_trail_time'];
                $listing_id = $row['user_id'];
                $listing_type = '36';
                $rating = '4.5';
                $reviews = '0';
                $profile_views =  '0' ;
                $user_discount = $row['user_discount'];

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
                $what_we_offer_array = array();
                $what_we_offer = explode(',', $branch_offer);
                foreach ($what_we_offer as $what_we_offer) {
                    //echo "SELECT * FROM fitness_what_we_offer WHERE id='$what_we_offer'";
                    if($what_we_offer != ""){
                        if(preg_match('/[0-9]/', $what_we_offer)){
                            $Query = $this->db->query("SELECT * FROM spa_services WHERE id='$what_we_offer'");
                            $what_we_offer_value = $Query->row()->services;
                            $what_we_offer_array[] = $what_we_offer_value;
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
                            $Query = $this->db->query("SELECT * FROM spa_facilites WHERE f_id='$facilities'");
                            $facilities_val = $Query->row()->facilities;
                            $facilities_array[] = $facilities_val;
                            
                        }
                        else{
                            $facilities_array[] = $facilities;
                        }
                    }
                }
                
                $branch_business_category_array = array();
                $branch_business = explode(',', $branch_business_category);
               
                foreach ($branch_business as $facilities) {
                    if($facilities !="")
                    {
                            $Query = $this->db->query("SELECT category,id FROM `business_category` WHERE `category_id` = 36 AND id='$facilities'");
                            $facilities_val = $Query->row()->category;
                            $branch_business_category_array[] = $facilities_val;
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
                  $package_query = $this->db->query("SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND FIND_IN_SET(categ_id,'" . $branch_business_category . "')  order by id asc");
              //echo "SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id='$listing_id' AND v_id='$listing_type' AND branch='$branch_id' AND categ_id='$category' order by id asc";
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



                  $spa_views = $this->db->select('id')->from('spa_views')->where('listing_id', $listing_id)->get()->num_rows();
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
                    $language = $row1['language'];
                    
                    $final_session1=array();
                    $session1 = array();
                    $session2 = array();
                  /*   $session1[] = array(
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
                         */
                           
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
                                        'language' => $language
                                       /*'package_trainer' => $final_session1*/
                                     );
                                       //'opening_hours' => $branch_final_Day);
                    
                }
                    
            }
            else {
               $sub_array1=array();
            }


               
                $enquiry_number = "9619146163";
                $resultpost[] = array(
                    'branch_id' => $branch_id,
                    'lat' => $lat,
                    'lng' => $lng,
                   
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
                  //  'booking_slot_array' => $booking_slot_array,
                    'gallery' => $gallery_array,
                    'what_we_offer' => $what_we_offer_array,
                    'facilities' => $facilities_array,
                    'package_list' => $package_list,
                    'therapist_list' => $sub_array1,
                    'speciality' => $branch_business_category_array,
                    'current_time'=>$current_day_final,
                    'opening_day' => $final_Day,
                    'rating' => $rating,
                    'review' => $reviews,
                    'followers' => $followers,
                    'following' => $following,
                    'is_follow' => $is_follow,
                   // 'is_trial_order' => $is_trial_order,
                    'profile_views' => $spa_views,
                    'user_discount' => $user_discount
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
    
     public function edit_review($user_id, $listing_id, $rating, $review, $service, $review_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
        );
        $this->db->where('id',$review_id);
        $this->db->where('user_id',$user_id);
        $this->db->update('spa_center_review', $review_array);
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

 $review_list_count = $this->db->select('id')->from('spa_center_review_comment')->where('post_id', $id)->get()->num_rows();
        if ($review_list_count) {
            $resultcomment = array();
            $query = $this->db->query("SELECT spa_center_review_comment.id,spa_center_review_comment.post_id,spa_center_review_comment.comment as comment,spa_center_review_comment.date,users.name,spa_center_review_comment.user_id as post_user_id FROM spa_center_review_comment INNER JOIN users on users.id=spa_center_review_comment.user_id WHERE spa_center_review_comment.post_id='$id' order by spa_center_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $comment_id = $row['id'];
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
                $usernamec = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_countc = $this->db->select('id')->from('spa_center_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $like_yes_noc = $this->db->select('id')->from('spa_center_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
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
    
    
    public function update_price()
    {
         $personal_trainer =  $this->db->query("SELECT * FROM product_details_hm WHERE pd_added_v_id='126' ");

            $count = $personal_trainer->num_rows();
            if ($count > 0) 
            {
                foreach ($personal_trainer->result_array() as $row) 
                {
                  echo  $id = $row['pd_id'];
                    echo "<br>";
                    
                    
                    $pd_mrp_price = $row['pd_mrp_price'];
                    $pd_discount_percentage = '5';
                    $total = round(($pd_mrp_price*5)/100);
                    $pd_vendor_price = $pd_mrp_price-$total;
                    $this->db->query("update product_details_hm  set pd_discount_percentage='5',pd_vendor_price='$pd_vendor_price' WHERE pd_id='$id' ");
                }
            }
    }
    
    
    public function spa_question_list($user_id) {
        $query = $this->db->query("SELECT * FROM spa_question");
        $count = $query->num_rows();
        $items = $query->result_array();

        $id = '';
        $ar = array();
        foreach ($items as $item) {

            if ($item['question_type'] == 0) {


                $ar['question'] = $item['question'];
                $id = $item['id'];
                $ar['que_id'] = $item['id'];
                $query = $this->db->query("SELECT * FROM spa_question_answer WHERE question_id='$id' and user_id='$user_id'");

                $items2 = $query->row_array();

                $r = $this->sub($items, $id, $user_id);
                $ar['sub_que'] = $r;

                $data[] = $ar; 
               
                
            }
        }
        
        
        //   die();

        return  $data;
        
    }
    
     function sub($items, $id, $user_id) {
        $ar1 = array();
        $ar = array();
        foreach ($items as $item) {
            $ar = array();
            if ($item['question_type'] == $id) {

                $ar['question'] = $item['question'];
                $ID = $item['id'];
                $ar['que_id'] = $item['id'];
                
                $query = $this->db->query("SELECT * FROM spa_question_answer WHERE question_id='$ID' and user_id='$user_id'");

                //echo "SELECT * FROM userprofile_question_answer WHERE question_id='$ID'";
                $items2 = $query->row_array();

                $r = $this->sub($items, $item['id'], $user_id);

                if (!empty($r)) {
                    $ar['sub_que'] = $r;
                }
                if ($items2['answer'] == null) {
                    $ar['ans'] = "";
                    $ar['que_subans'] ="";
                } else {

                    $query12 = $this->db->query("SELECT id,user_id,question_id,GROUP_CONCAT(answer) as new_ans FROM spa_question_answer WHERE  question_id='$ID' and user_id='$user_id'");
                    //echo "SELECT * FROM userprofile_question_answer WHERE question_id='$ID'";
                    $items23 = $query12->row_array();
                    $ar['ans'] = $items23['new_ans'];
                    $ar['que_subans'] = $item['subanswer'];
                }
                $ar1[] = $ar;
            }
        }

        return $ar1;
    }
    
     public function update_userprofile_question($data2) {


        $this->db->insert('spa_question_answer', $data2);
        return $this->db->insert_id();
    }

    public function delete_question($user_id) {

        $this->db->where('user_id', $user_id);
        $this->db->delete('spa_question_answer');
    }
    
    

}