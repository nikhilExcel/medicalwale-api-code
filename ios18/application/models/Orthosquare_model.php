<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orthosquare_model extends CI_Model
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
                $expired_at = '2018-11-12 08:57:58';
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
    
    //testing
    public function get_data()
    {
        $query = $this->db->query("SELECT * FROM `users` where `id`='9' order by id DESC");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $vendor_name  = $row['name'];
                $vendor_id    = $row['id'];
                $resultpost[] = array(
                    "name" => $vendor_name,
                    "id" => $vendor_id
                );
            }
        } else {
            $resultpost = '';
        }
        return $resultpost;
    }
    
    public function dental_clinic_list($user_id,$lat,$lng,$type)
    {
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
          
           if($type == 'one')
           {
              
                $sql = sprintf("SELECT id,hub_user_id,dentists_branch_user_id, name_of_hospital,about_us, establishment_year, speciality, address, address_2, pincode, phone, city, state, 	email, 	lat, lng, image, rating, review, medicalwale_discount,opening_hours, description,( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM dentists_branch  WHERE is_active='1' AND hub_user_id='33586'  HAVING distance < '5' ORDER BY distance LIMIT 1", ($lat), ($lng), ($lat), ($radius));
           $query = $this->db->query($sql);
           echo $this->db->last_query();
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
    
    
    
}