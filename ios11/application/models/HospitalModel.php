<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class HospitalModel extends CI_Model {

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
     public function get_business_category()
    {
        $sql    = "SELECT *  FROM `business_category` WHERE category_id='5'";
        $result = $this->db->query($sql)->result();
        return $result;
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

     //public function hospital_list($latitude, $longitude, $user_id, $category_name) {
    //     $radius = '5';

    //     $query = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'");
       
    //     $count = $query->num_rows();
    //     if ($count > 0) {
            
    //         foreach ($query->result_array() as $row) {
                
    //         $id = $row['id'];
    //         $hospital_user_id = $row['user_id'];
    //         $hospital_email = $row['email'];
    //         $queryTocheck = $this->db->query("SELECT id,name,phone FROM users WHERE id='$hospital_user_id' and email='$hospital_email'");
    //         //echo "SELECT id,name,phone FROM users WHERE id='$hospital_user_id' and email='$hospital_email'";
    //         /*die();*/
    //         $query_count = $queryTocheck->num_rows();

    //         if($query_count>0)
    //           {
    //             $name_of_hospital = $row['name_of_hospital'];
    //             $mobile = $row['phone'];
    //             $about_us = $row['about_us'];
    //             $establishment_year = $row['establishment_year'];
    //             $certificates_accred = $row['certificates_accred'];
    //             $category = $row['category'];
    //             $speciality = $row['speciality'];
    //             $surgery = $row['surgery'];
    //             $services = $row['services'];
    //             $address = $row['address'];
    //             $lat = $row['lat'];
    //             $lng = $row['lng'];
    //             $pincode = $row['pincode'];

    //             $city = $row['city'];
    //             $state = $row['state'];
    //             $email = $row['email'];
    //             $image = $row['image'];
    //             $rating = $row['rating'];
    //             $reviews = $row['review'];
    //             $user_discount = $row['user_discount'];
                
    //             $profile_views = '0';

    //             if ($image != '') {
    //                 $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $image;
    //             } else {
    //                 $image = '';
    //             }

    //             $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $hospital_user_id)->get()->num_rows();
    //             $following = $this->db->select('id')->from('follow_user')->where('user_id', $hospital_user_id)->get()->num_rows();
    //             $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $hospital_user_id)->get()->num_rows();

    //             if ($is_follow > 0) {
    //                 $is_follow = 'Yes';
    //             } else {
    //                 $is_follow = 'No';
    //             }
                
    //             if($certificates_accred != ''){
    //                 $certificates_accred_query = $this->db->query("SELECT `name`, image FROM `hospital_certificates_accred` WHERE FIND_IN_SET(name,'" . $certificates_accred . "')");
    //                 foreach ($certificates_accred_query->result_array() as $get_clist) {
    //                     $certificates_name = $get_clist['name'];
    //                     $certificates_image = $get_clist['image'];
    
    //                     if ($certificates_image != '') {
    //                         $certificates_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $certificates_image;
    //                     } else {
    //                         $certificates_image = '';
    //                     }
    
    
    //                     $certificates_accred_list[] = array(
    //                         "certificates_name" => $certificates_name,
    //                         "certificates_image" => $certificates_image
    //                     );
    //                 }
    //             }
    //             $hospitals_surgery_list = array();
    //             if($surgery != ''){
    //                 $hospitals_surgery_query = $this->db->query("SELECT `id`,`surgery_name`,`surgery_rate`,`surgery_package`, image FROM `hospitals_surgery` WHERE FIND_IN_SET(surgery_name,'" . $surgery . "')");
    //                 foreach ($hospitals_surgery_query->result_array() as $get_slist) {
    //                     $surgery_id = $get_slist['id'];
    //                     $surgery_name = $get_slist['surgery_name'];
    //                     $surgery_rate = $get_slist['surgery_rate'];
    //                     $surgery_package = $get_slist['surgery_package'];
    //                     $surgery_image = $get_slist['image'];
    
    //                     if ($surgery_image != '') {
    //                         $surgery_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $surgery_image;
    //                     } else {
    //                         $surgery_image = '';
    //                     }
    
    
    //                     $hospitals_surgery_list[] = array(
    //                         "surgery_id" => $surgery_id,
    //                         "surgery_name" => $surgery_name,
    //                         "surgery_rate" => $surgery_rate,
    //                         "surgery_package" => $surgery_package,
    //                         "surgery_image" => $surgery_image
    //                     );
    //                 }
    //             }


    //             $gallery = array();
    //             $gallery_query = $this->db->query("SELECT `title`, CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/',source) AS source FROM `hospital_media` WHERE hospital_id='$id'");
    //             foreach ($gallery_query->result_array() as $get_glist) {
    //                 $title = $get_glist['title'];
    //                 $media_image = $get_glist['source'];
    //                 $gallery[] = array(
    //                     "title" => $title,
    //                     "image" => $media_image
    //                 );
    //             }

    //             $hospitals_service_list = array();
    //             if($services != ''){
    //                 $hospitals_services_query = $this->db->query("SELECT `service_name` FROM `hospital_services` WHERE FIND_IN_SET(service_name,'" . $services . "')");
    //                 foreach ($hospitals_services_query->result_array() as $get_serlist) {
    //                     $service_name = $get_serlist['service_name'];
    //                     $hospitals_service_list[] = array(
    //                         "service_name" => $service_name
    //                     );
    //                 }
    //             }

    //             $hospitals_speciality_list = array();
    //             if($speciality != ''){
    //                 $hospitals_speciality_query = $this->db->query("SELECT `id`,`doctors_category`,`name`, image FROM `hospitals_specialist` WHERE FIND_IN_SET(name,'" . $speciality . "')");
    //                 foreach ($hospitals_speciality_query->result_array() as $get_splist) {
    //                     $specialist_id = $get_splist['id'];
    //                     $specialist_name = $get_splist['name'];
    //                     $doctors_category = $get_splist['doctors_category'];
    //                     $specialist_image = $get_splist['image'];
    
    //                     if ($specialist_image != '') {
    //                         $specialist_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $specialist_image;
    //                     } else {
    //                         $specialist_image = '';
    //                     }
    
    //                     $hospitals_speciality_list[] = array(
    //                         "specialist_id" => $specialist_id,
    //                         "specialist_name" => $specialist_name,
    //                         "doctors_category" => $doctors_category,
    //                         "specialist_image" => $specialist_image
    //                     );
    //                 }
    //             }
                
    //             $branch_list  = array();
    //             $branch_query = $this->db->query("SELECT * FROM `hospitals_branch` WHERE hospital_id='$user_id' order by id asc");
    //             /*echo"SELECT * FROM `hospitals_branch` WHERE hospital_id='$user_id' order by id asc";
    //             */
    //             $branch_count = $branch_query->num_rows();
    //             if ($branch_count > 0) {
    //                 foreach ($branch_query->result_array() as $branch_row) {
    //                     $branch_id                              = $branch_row['id'];
    //                     $branch_listing_id                      = $branch_row['hospital_id'];
    //                     $branch_hospital_branch_name            = $branch_row['name_of_branch'];
    //                     $branch_about_us                        = $branch_row['about_us'];
    //                     $branch_establishment_year              = $branch_row['establishment_year'];
    //                     $certificates_accred_list               = $branch_row['certificates_accred'];
    //                     $branch_category                        = $branch_row['category'];
    //                     $hospitals_speciality_list              = $branch_row['speciality'];
    //                     $hospitals_surgery_list                 = $branch_row['surgery'];
    //                     $hospitals_service_list                 = $branch_row['services'];
    //                     $branch_address                         = $branch_row['address'];
    //                     $branch_pincode                         = $branch_row['pincode'];
    //                     $branch_phone                           = $branch_row['phone'];
    //                     $branch_city                            = $branch_row['city'];
    //                     $branch_state                           = $branch_row['state'];
    //                     $branch_email                           = $branch_row['email'];
    //                     $branch_lat                             = $branch_row['lat'];
    //                     $branch_lng                             = $branch_row['lng'];
    //                     $branch_map_location                    = $branch_row['map_location'];
    //                     $branch_image                           = $branch_row['image'];
    //                     $branch_rating                          = $branch_row['rating'];
    //                     $branch_review                          = $branch_row['review'];
    //                     $branch_date                            = $branch_row['date'];
    //                     $branch_is_active                        = $branch_row['is_active'];
    //                     $is_approval                             = $branch_row['is_approval'];
    //                     $approval_date                        = $branch_row['approval_date'];
    //                     $is_active_date                        = $branch_row['is_active_date'];
                        


    //                     $branch_list[] = array(
    //                         'hospital_id' => $branch_id,
    //                         'hospital_id' => $branch_listing_id,
    //                         'name_of_branch' => $branch_listing_id,
    //                         'name_of_hospital' =>$branch_hospital_branch_name,
    //                         'about_us' => $branch_about_us,
    //                         'establishment_year' => $branch_establishment_year,
    //                         'certificates_accred_list'=>$certificates_accred_list,
    //                         'category' => $branch_category,
    //                         'speciality' => $hospitals_speciality_list,
    //                         'surgery' => $hospitals_surgery_list,
    //                         'services' => $hospitals_service_list,
    //                         'address' => $branch_address,
    //                         'pincode' => $branch_pincode,
    //                         'phone' => $branch_phone,
    //                         'lat' => $branch_lat,
    //                         'lng' => $branch_lng,
    //                         'map_location' => $branch_map_location,
    //                         'city' => $city,
    //                         'state' => $state,
    //                         'email' => $branch_email,
    //                         'image' => $branch_image,
    //                         'review' => $reviews,
    //                         'rating' => $branch_review,
    //                         'date' => $branch_date,
    //                         'is_active' => $branch_is_active,
    //                         'is_approval' => $is_approval,
    //                         'approval_date' => $approval_date,
    //                         'is_active_date' => $is_active_date
    //                     );
    //                 }
    //             }
                

    //             $resultpost[] = array(
    //                 'hospital_id' => $id,
    //                 'hospital_user_id' => $hospital_user_id,
    //                 'name_of_hospital' => $name_of_hospital,
    //                 'listing_type' => "8",
    //                 'about_us' => $about_us,
    //                 'establishment_year' => $establishment_year,
    //                 'certificates_accred_list' => $certificates_accred_list,
    //                 'hospitals_surgery_list' => $hospitals_surgery_list,
    //                 'gallery' => $gallery,
    //                 'hospitals_service_list' => $hospitals_service_list,
    //                 'hospitals_speciality_list' => $hospitals_speciality_list,
    //                 'address' => $address,
    //                 'mobile' => $mobile,
    //                 'lat' => $lat,
    //                 'lng' => $lng,
    //                 'pincode' => $pincode,
    //                 'city' => $city,
    //                 'state' => $state,
    //                 'rating' => $rating,
    //                 'review' => $reviews,
    //                 'image' => $image,
    //                 'followers' => $followers,
    //                 'following' => $following,
    //                 'profile_views' => $profile_views,
    //                 'is_follow' => $is_follow,
    //                 'user_discount' => $user_discount,
    //                 'branch_list' => $branch_list
    //             );
    //         }
    //         else{
    //             $resultpost = array();
    //         }
            
    //         }
    //     } else {
    //         $resultpost = array();
    //     }

    //     return $resultpost;
    // }
    /*public function hospital_list($latitude, $longitude, $user_id, $category_name) {
        $radius = '5';

        $query = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'");
       //echo "SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'";
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name_of_hospital = $row['name_of_hospital'];
                $mobile = $row['phone'];
                $about_us = $row['about_us'];
                $establishment_year = $row['establishment_year'];
                $certificates_accred = $row['certificates_accred'];
                $category = $row['category'];
                $speciality = $row['speciality'];
                $surgery = $row['surgery'];
                $services = $row['services'];
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

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $hospital_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $hospital_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $hospital_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $certificates_accred_list = array();
                if($certificates_accred != ''){
                    $certificates_accred_query = $this->db->query("SELECT `name`, image FROM `hospital_certificates_accred` WHERE FIND_IN_SET(name,'" . $certificates_accred . "')");
                    foreach ($certificates_accred_query->result_array() as $get_clist) {
                        $certificates_name = $get_clist['name'];
                        $certificates_image = $get_clist['image'];
    
                        if ($certificates_image != '') {
                            $certificates_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $certificates_image;
                        } else {
                            $certificates_image = '';
                        }
    
    
                        $certificates_accred_list[] = array(
                            "certificates_name" => $certificates_name,
                            "certificates_image" => $certificates_image
                        );
                    }
                }
                
                
               
                $hospitals_surgery_list = array();
                if($surgery != ''){
                    $hospitals_surgery_query = $this->db->query("SELECT `id`,`surgery_name`,`surgery_rate`,`surgery_package`, image FROM `hospitals_surgery` WHERE FIND_IN_SET(surgery_name,'" . $surgery . "')");
                    foreach ($hospitals_surgery_query->result_array() as $get_slist) {
                        $surgery_id = $get_slist['id'];
                        $surgery_name = $get_slist['surgery_name'];
                        $surgery_rate = $get_slist['surgery_rate'];
                        $surgery_package = $get_slist['surgery_package'];
                        $surgery_image = $get_slist['image'];
    
                        if ($surgery_image != '') {
                            $surgery_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $surgery_image;
                        } else {
                            $surgery_image = '';
                        }
    
    
                        $hospitals_surgery_list[] = array(
                            "surgery_id" => $surgery_id,
                            "surgery_name" => $surgery_name,
                            "surgery_rate" => $surgery_rate,
                            "surgery_package" => $surgery_package,
                            "surgery_image" => $surgery_image
                        );
                    }
                }

                
                $gallery = array();
                $gallery_query = $this->db->query("SELECT `title`, CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/',source) AS source FROM `hospital_media` WHERE hospital_id='$id'");
                foreach ($gallery_query->result_array() as $get_glist) {
                    $title = $get_glist['title'];
                    $media_image = $get_glist['source'];
                    $gallery[] = array(
                        "title" => $title,
                        "image" => $media_image
                    );
                }

                
                $hospitals_service_list = array();
                if($services != ''){
                    $hospitals_services_query = $this->db->query("SELECT `service_name` FROM `hospital_services` WHERE FIND_IN_SET(service_name,'" . $services . "')");
                    foreach ($hospitals_services_query->result_array() as $get_serlist) {
                        $service_name = $get_serlist['service_name'];
                        $hospitals_service_list[] = array(
                            "service_name" => $service_name
                        );
                    }
                }


                $hospitals_speciality_list = array();
                if($speciality != ''){
                    $hospitals_speciality_query = $this->db->query("SELECT `id`,`doctors_category`,`name`, image FROM `hospitals_specialist` WHERE FIND_IN_SET(name,'" . $speciality . "')");
                    foreach ($hospitals_speciality_query->result_array() as $get_splist) {
                        $specialist_id = $get_splist['id'];
                        $specialist_name = $get_splist['name'];
                        $doctors_category = $get_splist['doctors_category'];
                        $specialist_image = $get_splist['image'];
    
                        if ($specialist_image != '') {
                            $specialist_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $specialist_image;
                        } else {
                            $specialist_image = '';
                        }
    
                        $hospitals_speciality_list[] = array(
                            "specialist_id" => $specialist_id,
                            "specialist_name" => $specialist_name,
                            "doctors_category" => $doctors_category,
                            "specialist_image" => $specialist_image
                        );
                    }
                }
                
                
                //added by zak for lab packages for hospitals 
                $package_list  = array();
                $package_query = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$hospital_user_id' and branch_id ='' order by id asc");
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
                
                //Review Count
                $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$hospital_user_id");
                $Review_count = $Review_query->num_rows();
                
                
                //Profile View
                //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'");
                $Profile_count = $Profile_query->num_rows();

                //end 
                $resultpost[] = array(
                    'hospital_id' => $id,
                    'hospital_user_id' => $hospital_user_id,
                    'name_of_hospital' => $name_of_hospital,
                    'listing_type' => "8",
                    'about_us' => $about_us,
                    'establishment_year' => $establishment_year,
                    'certificates_accred_list' => $certificates_accred_list,
                    'hospitals_surgery_list' => $hospitals_surgery_list,
                    'gallery' => $gallery,
                    'hospitals_service_list' => $hospitals_service_list,
                    'hospitals_speciality_list' => $hospitals_speciality_list,
                    'address' => $address,
                    'mobile' => $mobile,
                    'lat' => $lat,
                    'lng' => $lng,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'rating' => $rating,
                    'review' => $Review_count,
                    'image' => $image,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $Profile_count,
                    'is_follow' => $is_follow,
                    'user_discount' => $user_discount,
                    'lab_package_list' => $package_list
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }
*/


//  public function hospital_list($latitude, $longitude, $user_id, $category_name) {
//         $radius = '5';

//         $query = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'");
//       //echo "SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'";
//         $count = $query->num_rows();
//         if ($count > 0) {
//             foreach ($query->result_array() as $row) {
//                 $id = $row['id'];
//                 $name_of_hospital = $row['name_of_hospital'];
//                 $mobile = $row['phone'];
//                 $about_us = $row['about_us'];
//                 $establishment_year = $row['establishment_year'];
//                 $certificates_accred = $row['certificates_accred'];
//                 $category = $row['category'];
//                 $speciality = $row['speciality'];
//                 $surgery = $row['surgery'];
//                 $services = $row['services'];
//                 $address = $row['address'];
//                 $lat = $row['lat'];
//                 $lng = $row['lng'];
//                 $pincode = $row['pincode'];

//                 $city = $row['city'];
//                 $state = $row['state'];
//                 $email = $row['email'];
//                 $image = $row['image'];
//                 $rating = $row['rating'];
//                 $reviews = $row['review'];
//                 $user_discount = $row['user_discount'];
//                 $hospital_user_id = $row['user_id'];
//                 $profile_views = '0';

//                 if ($image != '') {
//                     $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
//                 } else {
//                     $image = '';
//                 }

//                 $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $hospital_user_id)->get()->num_rows();
//                 $following = $this->db->select('id')->from('follow_user')->where('user_id', $hospital_user_id)->get()->num_rows();
//                 $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $hospital_user_id)->get()->num_rows();

//                 if ($is_follow > 0) {
//                     $is_follow = 'Yes';
//                 } else {
//                     $is_follow = 'No';
//                 }

//                 $certificates_accred_list = array();
//                 if($certificates_accred != ''){
//                     $certificates_accred_query = $this->db->query("SELECT `name`, image FROM `hospital_certificates_accred` WHERE FIND_IN_SET(name,'" . $certificates_accred . "')");
//                     foreach ($certificates_accred_query->result_array() as $get_clist) {
//                         $certificates_name = $get_clist['name'];
//                         $certificates_image = $get_clist['image'];
    
//                         if ($certificates_image != '') {
//                             $certificates_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $certificates_image;
//                         } else {
//                             $certificates_image = '';
//                         }
    
    
//                         $certificates_accred_list[] = array(
//                             "certificates_name" => $certificates_name,
//                             "certificates_image" => $certificates_image
//                         );
//                     }
//                 }
                
                
               
//                 $hospitals_surgery_list = array();
//                 if($surgery != ''){
//                     $hospitals_surgery_query = $this->db->query("SELECT `id`,`surgery_name`,`surgery_rate`,`surgery_package`, image FROM `hospitals_surgery` WHERE FIND_IN_SET(surgery_name,'" . $surgery . "')");
//                     foreach ($hospitals_surgery_query->result_array() as $get_slist) {
//                         $surgery_id = $get_slist['id'];
//                         $surgery_name = $get_slist['surgery_name'];
//                         $surgery_rate = $get_slist['surgery_rate'];
//                         $surgery_package = $get_slist['surgery_package'];
//                         $surgery_image = $get_slist['image'];
    
//                         if ($surgery_image != '') {
//                             $surgery_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $surgery_image;
//                         } else {
//                             $surgery_image = '';
//                         }
    
    
//                         $hospitals_surgery_list[] = array(
//                             "surgery_id" => $surgery_id,
//                             "surgery_name" => $surgery_name,
//                             "surgery_rate" => $surgery_rate,
//                             "surgery_package" => $surgery_package,
//                             "surgery_image" => $surgery_image
//                         );
//                     }
//                 }

                
//                 $gallery = array();
//                 $gallery_query = $this->db->query("SELECT `title`, CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/gallery/',source) AS source FROM `hospital_media` WHERE hospital_id='$hospital_user_id'");
               
                
//                 foreach ($gallery_query->result_array() as $get_glist) {
                    
//                     $title = $get_glist['title'];
//                     $media_image = $get_glist['source'];
//                     $gallery[] = array(
//                         "title" => $title,
//                         "image" => $media_image
//                     );
//                 }

                
//                 $hospitals_service_list = array();
//                 if($services != ''){
//                     $hospitals_services_query = $this->db->query("SELECT `service_name` FROM `hospital_services` WHERE FIND_IN_SET(id,'" . $services . "')");
//                     foreach ($hospitals_services_query->result_array() as $get_serlist) {
//                         $service_name = $get_serlist['service_name'];
//                         $hospitals_service_list[] = array(
//                             "service_name" => $service_name
//                         );
//                     }
//                 }


//                 $hospitals_speciality_list = array();
//                 if($speciality != ''){
                    
//                     $hospitals_speciality_query = $this->db->query("SELECT `id`,`doctors_category`,`name`, image FROM `hospitals_specialist` WHERE FIND_IN_SET(id,'" . $speciality . "')");
//                     foreach ($hospitals_speciality_query->result_array() as $get_splist) {
//                         $specialist_id = $get_splist['id'];
//                         $specialist_name = $get_splist['name'];
//                         $doctors_category = $get_splist['doctors_category'];
//                         $specialist_image = $get_splist['image'];
    
//                         if ($specialist_image != '') {
//                             $specialist_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $specialist_image;
//                         } else {
//                             $specialist_image = '';
//                         }
    
//                         $hospitals_speciality_list[] = array(
//                             "specialist_id" => $specialist_id,
//                             "specialist_name" => $specialist_name,
//                             "doctors_category" => $doctors_category,
//                             "specialist_image" => $specialist_image
//                         );
//                     }
//                 }
                
                
//                 //added by zak for lab packages for hospitals 
//                 $package_list  = array();
//                 $package_query = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$hospital_user_id' and branch_id ='' order by id asc");
//                 //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
//                 $package_count = $package_query->num_rows();
//                 if ($package_count > 0) {
//                     foreach ($package_query->result_array() as $package_row) {
//                         $package_id      = $package_row['id'];
//                         $package_name    = $package_row['lab_name'];
//                         $package_details = $package_row['lab_details'];
//                         $price           = $package_row['Price'];
//                         $image           = $package_row['image'];
//                         $home_delivery   = $package_row['home_delivery'];
//                         //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
//                         $package_list[]  = array(
//                             'package_id' => $package_id,
//                             'package_name' => $package_name,
//                             'package_details' => $package_details,
//                             'price' => $price,
//                             'home_delivery' => $home_delivery
//                         );
//                     }
//                 } else {
//                     $package_list = array();
//                 }
                
//                 //Review Count
//                 $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$hospital_user_id");
//                 $Review_count = $Review_query->num_rows();
                
                
//                 //Profile View
//                 //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
//                 $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'");
//                 $Profile_count = $Profile_query->num_rows();

//                 //end 
//                 $resultpost[] = array(
//                     'hospital_id' => $id,
//                     'hospital_user_id' => $hospital_user_id,
//                     'name_of_hospital' => $name_of_hospital,
//                     'listing_type' => "8",
//                     'about_us' => $about_us,
//                     'establishment_year' => $establishment_year,
//                     'certificates_accred_list' => $certificates_accred_list,
//                     'hospitals_surgery_list' => $hospitals_surgery_list,
//                     'gallery' => $gallery,
//                     'hospitals_service_list' => $hospitals_service_list,
//                     'hospitals_speciality_list' => $hospitals_speciality_list,
//                     'address' => $address,
//                     'mobile' => $mobile,
//                     'lat' => $lat,
//                     'lng' => $lng,
//                     'pincode' => $pincode,
//                     'city' => $city,
//                     'state' => $state,
//                     'rating' => $rating,
//                     'review' => $Review_count,
//                     'image' => $image,
//                     'followers' => $followers,
//                     'following' => $following,
//                     'profile_views' => $Profile_count,
//                     'is_follow' => $is_follow,
//                     'user_discount' => $user_discount,
//                     'lab_package_list' => $package_list
//                 );
//             }
//         } else {
//             $resultpost = array();
//         }

//         return $resultpost;
//     }


 public function hospital_list($latitude, $longitude, $user_id, $category_name) {
        $radius = '5';

        $query = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'");
       
        $count = $query->num_rows();
        //dnt touch this code this will auto increment radius if data is not available
       $i=0;
         do{
             $radius = $radius + 5;
             
        $query = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'");
        
        $count = $query->num_rows();
        $i++;
         }while($count <= 5 && $i<20);
        
        
        

            if ($count > 0) {
                foreach ($query->result_array() as $row) {
                    $id = $row['id'];
                    $name_of_hospital = $row['name_of_hospital'];
                    $mobile = $row['phone'];
                    $about_us = $row['about_us'];
                    $establishment_year = $row['establishment_year'];
                    $certificates_accred = $row['certificates_accred'];
                    $category = $row['category'];
                    $speciality = $row['speciality'];
                    $surgery = $row['surgery'];
                    $services = $row['services'];
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
    
                    $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $hospital_user_id)->get()->num_rows();
                    $following = $this->db->select('id')->from('follow_user')->where('user_id', $hospital_user_id)->get()->num_rows();
                    $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $hospital_user_id)->get()->num_rows();
    
                    if ($is_follow > 0) {
                        $is_follow = 'Yes';
                    } else {
                        $is_follow = 'No';
                    }
    
                    $certificates_accred_list = array();
                    if($certificates_accred != ''){
                        $certificates_accred_query = $this->db->query("SELECT `name`, image FROM `hospital_certificates_accred` WHERE FIND_IN_SET(name,'" . $certificates_accred . "')");
                        foreach ($certificates_accred_query->result_array() as $get_clist) {
                            $certificates_name = $get_clist['name'];
                            $certificates_image = $get_clist['image'];
        
                            if ($certificates_image != '') {
                                $certificates_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $certificates_image;
                            } else {
                                $certificates_image = '';
                            }
        
        
                            $certificates_accred_list[] = array(
                                "certificates_name" => $certificates_name,
                                "certificates_image" => $certificates_image
                            );
                        }
                    }
                    
                    
                   
                    $hospitals_surgery_list = array();
                    if($surgery != ''){
                        $hospitals_surgery_query = $this->db->query("SELECT `id`,`surgery_name`,`surgery_rate`,`surgery_package`, image FROM `hospitals_surgery` WHERE FIND_IN_SET(surgery_name,'" . $surgery . "')");
                        foreach ($hospitals_surgery_query->result_array() as $get_slist) {
                            $surgery_id = $get_slist['id'];
                            $surgery_name = $get_slist['surgery_name'];
                            $surgery_rate = $get_slist['surgery_rate'];
                            $surgery_package = $get_slist['surgery_package'];
                            $surgery_image = $get_slist['image'];
        
                            if ($surgery_image != '') {
                                $surgery_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $surgery_image;
                            } else {
                                $surgery_image = '';
                            }
        
        
                            $hospitals_surgery_list[] = array(
                                "surgery_id" => $surgery_id,
                                "surgery_name" => $surgery_name,
                                "surgery_rate" => $surgery_rate,
                                "surgery_package" => $surgery_package,
                                "surgery_image" => $surgery_image
                            );
                        }
                    }
    
                    
                    $gallery = array();
                    $gallery_query = $this->db->query("SELECT `title`, CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/gallery/',source) AS source FROM `hospital_media` WHERE hospital_id='$hospital_user_id'");
                   
                    
                    foreach ($gallery_query->result_array() as $get_glist) {
                        
                        $title = $get_glist['title'];
                        $media_image = $get_glist['source'];
                        $gallery[] = array(
                            "title" => $title,
                            "image" => $media_image
                        );
                    }
    
                    
                    $hospitals_service_list = array();
                    if($services != ''){
                        $hospitals_services_query = $this->db->query("SELECT `service_name` FROM `hospital_services` WHERE FIND_IN_SET(id,'" . $services . "')");
                        foreach ($hospitals_services_query->result_array() as $get_serlist) {
                            $service_name = $get_serlist['service_name'];
                            $hospitals_service_list[] = array(
                                "service_name" => $service_name
                            );
                        }
                    }
    
    
                    $hospitals_speciality_list = array();
                    if($speciality != ''){
                        
                        $hospitals_speciality_query = $this->db->query("SELECT `id`,`doctors_category`,`name`, image FROM `hospitals_specialist` WHERE FIND_IN_SET(id,'" . $speciality . "')");
                        foreach ($hospitals_speciality_query->result_array() as $get_splist) {
                            $specialist_id = $get_splist['id'];
                            $specialist_name = $get_splist['name'];
                            $doctors_category = $get_splist['doctors_category'];
                            $specialist_image = $get_splist['image'];
        
                            if ($specialist_image != '') {
                                $specialist_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $specialist_image;
                            } else {
                                $specialist_image = '';
                            }
        
                            $hospitals_speciality_list[] = array(
                                "specialist_id" => $specialist_id,
                                "specialist_name" => $specialist_name,
                                "doctors_category" => $doctors_category,
                                "specialist_image" => $specialist_image
                            );
                        }
                    }
                    
                    
                    //added by zak for lab packages for hospitals 
                    $package_list  = array();
                    $package_query = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$hospital_user_id' and branch_id ='' order by id asc");
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
                    
                    //Review Count
                    $Review_query = $this->db->query("SELECT * FROM `hospital_review` WHERE hospital_id=$hospital_user_id");
                    $Review_count = $Review_query->num_rows();
                    
                    
                    //Profile View
                    //echo "SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'";
                    $Profile_query = $this->db->query("SELECT * FROM `profile_views_master` WHERE listing_id=$hospital_user_id and vendor_type='8'");
                    $Profile_count = $Profile_query->num_rows();
    
                    //end 
                    $resultpost[] = array(
                        'hospital_id' => $id,
                        'hospital_user_id' => $hospital_user_id,
                        'name_of_hospital' => $name_of_hospital,
                        'listing_type' => "8",
                        'about_us' => $about_us,
                        'establishment_year' => $establishment_year,
                        'certificates_accred_list' => $certificates_accred_list,
                        'hospitals_surgery_list' => $hospitals_surgery_list,
                        'gallery' => $gallery,
                        'hospitals_service_list' => $hospitals_service_list,
                        'hospitals_speciality_list' => $hospitals_speciality_list,
                        'address' => $address,
                        'mobile' => $mobile,
                        'lat' => $lat,
                        'lng' => $lng,
                        'pincode' => $pincode,
                        'city' => $city,
                        'state' => $state,
                        'rating' => $rating,
                        'review' => $Review_count,
                        'image' => $image,
                        'followers' => $followers,
                        'following' => $following,
                        'profile_views' => $Profile_count,
                        'is_follow' => $is_follow,
                        'user_discount' => $user_discount,
                        'lab_package_list' => $package_list
                    );
             }
            } else {
                $resultpost = array();
            }
    
            return $resultpost;
    
      
        
        

            
 
 }

    public function hospital_details($user_id, $listing_id) {
        $radius = '5';
        $query = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year` FROM hospitals WHERE user_id='$listing_id' limit 1");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name_of_hospital = $row['name_of_hospital'];
                $mobile = $row['phone'];
                $about_us = $row['about_us'];
                $establishment_year = $row['establishment_year'];
                $certificates_accred = $row['certificates_accred'];
                $category = $row['category'];
                $speciality = $row['speciality'];
                $surgery = $row['surgery'];
                $services = $row['services'];
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
                $hospital_user_id = $row['user_id'];
                $profile_views = '2458';

                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $hospital_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $hospital_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $hospital_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $certificates_accred_list = array();
                $certificates_accred_query = $this->db->query("SELECT `name`, image FROM `hospital_certificates_accred` WHERE FIND_IN_SET(name,'" . $certificates_accred . "')");
                foreach ($certificates_accred_query->result_array() as $get_clist) {
                    $certificates_name = $get_clist['name'];
                    $certificates_image = $get_clist['image'];

                    if ($certificates_image != '') {
                        $certificates_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $certificates_image;
                    } else {
                        $certificates_image = '';
                    }


                    $certificates_accred_list[] = array(
                        "certificates_name" => $certificates_name,
                        "certificates_image" => $certificates_image
                    );
                }

                $hospitals_surgery_list = array();
                $hospitals_surgery_query = $this->db->query("SELECT `id`,`surgery_name`,`surgery_rate`,`surgery_package`, image FROM `hospitals_surgery` WHERE FIND_IN_SET(surgery_name,'" . $surgery . "')");
                foreach ($hospitals_surgery_query->result_array() as $get_slist) {
                    $surgery_id = $get_slist['id'];
                    $surgery_name = $get_slist['surgery_name'];
                    $surgery_rate = $get_slist['surgery_rate'];
                    $surgery_package = $get_slist['surgery_package'];
                    $surgery_image = $get_slist['image'];

                    if ($surgery_image != '') {
                        $surgery_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $surgery_image;
                    } else {
                        $surgery_image = '';
                    }

                    $hospitals_surgery_list[] = array(
                        "surgery_id" => $surgery_id,
                        "surgery_name" => $surgery_name,
                        "surgery_rate" => $surgery_rate,
                        "surgery_package" => $surgery_package,
                        "surgery_image" => $surgery_image
                    );
                }

                $gallery = array();
                $gallery_query = $this->db->query("SELECT `title`, CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/',source) AS source FROM `hospital_media` WHERE hospital_id='$id'");
                foreach ($gallery_query->result_array() as $get_glist) {
                    $title = $get_glist['title'];
                    $image = $get_glist['source'];
                    $gallery[] = array(
                        "title" => $title,
                        "image" => $image
                    );
                }

                $hospitals_service_list = array();
                $hospitals_services_query = $this->db->query("SELECT `service_name` FROM `hospital_services` WHERE FIND_IN_SET(id,'" . $services . "')");
                foreach ($hospitals_services_query->result_array() as $get_serlist) {
                    $service_name = $get_serlist['service_name'];
                    $hospitals_service_list[] = array(
                        "service_name" => $service_name
                    );
                }

                $hospitals_speciality_list = array();
                $hospitals_speciality_query = $this->db->query("SELECT `id`,`doctors_category`,`name`, image FROM `hospitals_specialist` WHERE FIND_IN_SET(id,'" . $speciality . "')");
                foreach ($hospitals_speciality_query->result_array() as $get_splist) {
                    $specialist_id = $get_splist['id'];
                    $specialist_name = $get_splist['name'];
                    $doctors_category = $get_splist['doctors_category'];
                    $specialist_image = $get_splist['image'];

                    if ($specialist_image != '') {
                        $specialist_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $specialist_image;
                    } else {
                        $specialist_image = '';
                    }

                    $hospitals_speciality_list[] = array(
                        "specialist_id" => $specialist_id,
                        "specialist_name" => $specialist_name,
                        "doctors_category" => $doctors_category,
                        "specialist_image" => $specialist_image
                    );
                }


                $resultpost[] = array(
                    'hospital_id' => $id,
                    'hospital_user_id' => $hospital_user_id,
                    'name_of_hospital' => $name_of_hospital,
                    'about_us' => $about_us,
                    'establishment_year' => $establishment_year,
                    'certificates_accred_list' => $certificates_accred_list,
                    'hospitals_surgery_list' => $hospitals_surgery_list,
                    'gallery' => $gallery,
                    'hospitals_service_list' => $hospitals_service_list,
                    'hospitals_speciality_list' => $hospitals_speciality_list,
                    'address' => $address,
                    'mobile' => $mobile,
                    'lat' => $lat,
                    'lng' => $lng,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'rating' => $rating,
                    'review' => $reviews,
                    'image' => $image,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'is_follow' => $is_follow
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function doctor_list($hospital_id, $category_name) {


        $query = $this->db->query("SELECT doctor_list.id,doctor_list.consultation_fee,doctor_list.lat,doctor_list.lng,doctor_list.category,doctor_list.user_id,doctor_list.doctor_name,doctor_list.about_us,doctor_list.speciality,doctor_list.address,doctor_list.telephone,doctor_list.medical_college,doctor_list.medical_affiliation,doctor_list.charitable_affiliation,doctor_list.awards_recognition,doctor_list.all_24_hrs_available,doctor_list.home_visit_available,doctor_list.qualification,doctor_list.experience,doctor_list.website,doctor_list.location,doctor_list.days,doctor_list.timing,doctor_list.image,doctor_list.rating,doctor_list.review FROM doctor_list INNER JOIN doctor_hospital_list ON doctor_list.id=doctor_hospital_list.doctor_id WHERE doctor_hospital_list.hospital_id='$hospital_id' ");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $doctor_name = $row['doctor_name'];
                $about_us = $row['about_us'];
                $speciality = $row['category']; //changed by david
                $address = $row['address'];
                $telephone = $row['telephone'];
                $medical_college = $row['medical_college'];
                $medical_affiliation = $row['medical_affiliation'];
                $charitable_affiliation = $row['charitable_affiliation'];
                $awards_recognition = $row['awards_recognition'];
                $hrs_available = $row['all_24_hrs_available'];
                $home_visit_available = $row['home_visit_available'];
                $qualification = $row['qualification'];
                $consultation_fee = $row['consultation_fee'];
                $experience = $row['experience'];
                $website = $row['website'];
                $location = $row['location'];
                $days = $row['days'];
                $timing = $row['timing'];
                $rating = $row['rating'];
                $reviews = $row['review'];
                $image = $row['image'];
                $doctor_user_id = $row['user_id'];
                $profile_views = '2458';
                $is_follow = 'yes';

                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $image;
                } else {
                    $image = '';
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                $result_hospital = '';

                $hospital_query = $this->db->query("select hospitals.id as hospital_id,doctor_list.id as doctor_id,hospitals.name_of_hospital,hospitals.address,hospitals.rating,hospitals.image,doctor_hospital_list.opening_days from doctor_list INNER JOIN doctor_hospital_list on doctor_hospital_list.doctor_id = doctor_list.id INNER JOIN hospitals on hospitals.id=doctor_hospital_list.hospital_id WHERE doctor_list.id='$id'");
                $total_hospital = $hospital_query->num_rows();
                if ($total_hospital > 0) {
                    foreach ($hospital_query->result_array() as $hospital_row) {
                        $id = $hospital_row['hospital_id'];
                        $hospital_name = $hospital_row['name_of_hospital'];
                        $address = $hospital_row['address'];
                        $rating = $hospital_row['rating'];
                        $hospital_image = $hospital_row['image'];
                        $opening_days = $hospital_row['opening_days'];
                        if ($hospital_image != '') {
                            $hospital_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $hospital_image;
                        } else {
                            $hospital_image = '';
                        }


                        date_default_timezone_set('Asia/Kolkata');
                        $open_days = '';
                        $day = '';
                        $time = '';
                        $start_time = '';
                        $end_time = '';
                        $opening_hours = explode(',', $opening_days);
                        foreach ($opening_hours as $opening_hour) {
                            $array_hours = explode('-', $opening_hour);
                            $day = $array_hours[0];
                            $start_time = $array_hours[1];
                            $end_time = $array_hours[2];
                            $time = $start_time . ' - ' . $end_time;
                            $open_days[] = array(
                                'day' => $day,
                                'time' => $time
                            );
                        }

                        $result_hospital[] = array(
                            'id' => $id,
                            'hospital_name' => $hospital_name,
                            'address' => $address,
                            'rating' => $rating,
                            'image' => $hospital_image,
                            'opening_day' => $open_days
                        );
                    }
                } else {
                    $result_hospital = array();
                }
                $service = '';
                $result_services = '';
                $doctor_services_query = $this->db->query("SELECT service FROM `doctor_services`");
                foreach ($doctor_services_query->result_array() as $doctor_services) {
                    $service = $doctor_services['service'];
                    $result_services[] = array(
                        'service' => $service
                    );
                }
                $specialization = '';
                $result_specialization = '';
                $doctor_specialization_query = $this->db->query("SELECT specialization FROM `doctor_specialization`");
                foreach ($doctor_specialization_query->result_array() as $doctor_specialization) {
                    $specialization = $doctor_specialization['specialization'];
                    $result_specialization[] = array(
                        'specialization' => $specialization
                    );
                }


                $resultpost[] = array(
                    'doctor_id' => $id,
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'about_us' => $about_us,
                    'speciality' => $speciality,
                    'address' => $address,
                    'telephone' => $telephone,
                    'medical_college' => $medical_college,
                    'medical_affiliation' => $medical_affiliation,
                    'charitable_affiliation' => $charitable_affiliation,
                    'awards_recognition' => $awards_recognition,
                    'hrs_available' => $hrs_available,
                    'home_visit_available' => $home_visit_available,
                    'qualification' => $qualification,
                    'experience' => $experience,
                    'consultation_fee' => $consultation_fee,
                    'website' => $website,
                    'location' => $location,
                    'days' => $days,
                    'timing' => $timing,
                    'rating' => $rating,
                    'review' => $reviews,
                    'image' => $image,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'is_follow' => $is_follow,
                    'doctor_practices' => $result_hospital,
                    'doctor_services' => $result_services,
                    'doctor_specialization' => $result_specialization
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function hospitals_appointment($user_id, $hospital_id, $surgery_id, $patient_name, $gender, $age, $mobile, $ts1_date, $ts1_time, $ts2_date, $ts2_time, $medical_condition) {
        date_default_timezone_set('Asia/Calcutta');
        $booking_date = date('Y-m-d H:i:s');

        $review_array = array(
            'user_id' => $user_id,
            'hospital_id' => $hospital_id,
            'surgery_id' => $surgery_id,
            'patient_name' => $patient_name,
            'gender' => $gender,
            'age' => $age,
            'mobile' => $mobile,
            'ts1_date' => $ts1_date,
            'ts1_time' => $ts1_time,
            'ts2_date' => $ts2_date,
            'ts2_time' => $ts2_time,
            'medical_condition' => $medical_condition,
            'booking_date' => $booking_date
        );
        $this->db->insert('hospitals_appointment', $review_array);
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function add_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'hospital_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('hospital_review', $review_array);
        return array(
            'status' => 200,
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

        $resultpost = array();
        $review_count = $this->db->select('id')->from('hospital_review')->where('hospital_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT hospital_review.id,hospital_review.user_id,hospital_review.hospital_id,hospital_review.rating,hospital_review.review, hospital_review.service,hospital_review.date as review_date,users.id as user_id,users.name as firstname FROM `hospital_review` INNER JOIN `users` ON hospital_review.user_id=users.id WHERE hospital_review.hospital_id='$listing_id' order by hospital_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                 $user_id       = $row['user_id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '13') {
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

                $like_count = $this->db->select('id')->from('hospital_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('hospital_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('hospital_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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

        $resultpost = array();
        $review_count = $this->db->select('id')->from('hospital_review')->where('hospital_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT hospital_review.id,hospital_review.user_id,hospital_review.hospital_id,hospital_review.rating,hospital_review.review, hospital_review.service,hospital_review.date as review_date,users.id as user_id,users.name as firstname FROM `hospital_review` INNER JOIN `users` ON hospital_review.user_id=users.id WHERE hospital_review.hospital_id='$listing_id' order by hospital_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '13') {
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

                $like_count = $this->db->select('id')->from('hospital_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('hospital_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('hospital_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

                $review_list_count = $this->db->select('id')->from('hospital_review_comment')->where('post_id', $id)->get()->num_rows();
        if ($review_list_count) {
            $querycomment = $this->db->query("SELECT hospital_review_comment.id,hospital_review_comment.post_id,hospital_review_comment.comment as comment,hospital_review_comment.date,users.name,hospital_review_comment.user_id as post_user_id FROM hospital_review_comment INNER JOIN users on users.id=hospital_review_comment.user_id WHERE hospital_review_comment.post_id='$id' order by hospital_review_comment.id asc");
 $resultcomment = array();
            foreach ($querycomment->result_array() as $rowc) {
                $commentid = $rowc['id'];
                $post_id = $rowc['post_id'];
                $comment = $rowc['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                $usernamec = $rowc['name'];
                $date = $rowc['date'];
                $post_user_id = $rowc['post_user_id'];

                $like_countc = $this->db->select('id')->from('hospital_review_comment_like')->where('comment_id', $commentid)->get()->num_rows();
                $like_yes_noc = $this->db->select('id')->from('hospital_review_comment_like')->where('comment_id', $commentid)->where('user_id', $user_id)->get()->num_rows();
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
                $resultcomment[] = array(
                    'id' => $commentid,
                    'username' => $usernamec,
                    'userimage' => $userimage,
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
    

    public function review_like($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `hospital_review_likes` WHERE post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `hospital_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from `hospital_review_likes` WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $hospital_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('hospital_review_likes', $hospital_review_likes);
            $like_query = $this->db->query("SELECT id from hospital_review_likes WHERE post_id='$post_id'");
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

        $hospital_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('hospital_review_comment', $hospital_review_comment);
        $hospital_review_comment_query = $this->db->query("SELECT id from hospital_review_comment where post_id='$post_id'");
        $total_comment = $hospital_review_comment_query->num_rows();
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
        $count_query = $this->db->query("SELECT id from hospital_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `hospital_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from hospital_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $hospital_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('hospital_review_comment_like', $hospital_review_comment_like);
            $comment_query = $this->db->query("SELECT id from hospital_review_comment_like where comment_id='$comment_id'");
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

        $review_list_count = $this->db->select('id')->from('hospital_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT hospital_review_comment.id,hospital_review_comment.post_id,hospital_review_comment.comment as comment,hospital_review_comment.date,users.name,hospital_review_comment.user_id as post_user_id FROM hospital_review_comment INNER JOIN users on users.id=hospital_review_comment.user_id WHERE hospital_review_comment.post_id='$post_id' order by hospital_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '12') {
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

                $like_count = $this->db->select('id')->from('hospital_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('hospital_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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
    
    public function get_hospital_package($user_id){
        
        $query = $this->db->query("SELECT * FROM hospital_packages WHERE hospital_id = '$user_id' ");
        $num_count = $query->num_rows();
        $qRows = $query->result_array();
      
        if($num_count>0)
        {
            foreach($qRows as $qRow)
            {
                $package_id         = $qRow['id'];
                $hospital_id        = $qRow['hospital_id'];
                $package_name       = $qRow['package_name'];
                $package_desc       = $qRow['package_desc'];
                $package_amount     = $qRow['package_amount'];
                $package_inclusive  = $qRow['package_inclusive'];
                $package_exclusive  = $qRow['package_exclusive'];
                $type=  $qRow['type_room'];
                if($qRow['package_type'] == '0' || $qRow['package_type'] == 'Surgical Package' || $qRow['package_type'] == NULL || $qRow['package_type'] == '')
                {
                    $package_type = 'Treatment type - Surgical';
                }
                else
                {
                   $package_type =  'Treatment type - Non-Surgical';
                }
               // $package_type       = $qRow['package_type'];
                $package_benifit    = $qRow['package_benifit'];
                $created_at         = $qRow['created_at'];
               
              // echo "SELECT * FROM hospitals_surgery_new WHERE package_id = '$package_id'";
                $pkquery = $this->db->query("SELECT * FROM hospitals_surgery_new WHERE package_id = '$package_id'");
                $pk_num_count = $pkquery->num_rows();
                $hospital_surgery = array();
                if($pk_num_count > 0){
                    $pkRows = $pkquery->result_array();
                    foreach($pkRows as $pkRow){
                        $s_id = $pkRow['surgery_id'];
                        //echo "SELECT * FROM surgery_master WHERE id = '$s_id'";
                        $sgquery = $this->db->query("SELECT * FROM surgery_master WHERE id = '$s_id'");
                        $sgquerys = $sgquery->result_array();
                        
                        foreach($sgquerys as $sg){
                            $surgery_name       = $sg['surgery_name'];
                            $surgery_desc       = $sg['surgery_desc'];
                            if($sg['surgery_image'] != '')
                            {
                            $surgery_image     = 'https://s3.amazonaws.com/medicalwale/images/SurgeriesImages/'.$sg['surgery_image'];
                            }
                            else
                            {
                            $surgery_image     = 'https://s3.amazonaws.com/medicalwale/images/SurgeriesImages/';
                            }
                            $conditions         = $sg['conditions'];
                            $pre_procedure      = $sg['pre_procedure'];
                            $during_procedure   = $sg['during_procedure'];
                            $post_procedure     = $sg['post_procedure'];
                            $instruction        = $sg['instruction'];
                            $risk_complication  = $sg['risk_complication'];
                            $other_details      = $sg['other_details'];
                            
                            $hospital_surgery[] = array(
                                'surgery_name'       => $surgery_name,
                                'surgery_desc'       => $surgery_desc,
                                'surgery_image'      => $surgery_image,
                                'conditions'         => $conditions,
                                'pre_procedure'      => $pre_procedure,
                                'during_procedure'   => $during_procedure,
                                'post_procedure'     => $post_procedure,
                                'instruction'        => $instruction,
                                'risk_complication'  => $risk_complication,
                                'other_details'      => $other_details	
                            ); 
                        }
                        
                    }
                }
                
                //added by zak for ward list to display acourding to with_room / witout_room 
                 //----WADRS FOR HOSPITAL EXITS OR NOT CHECKING-------package_wards
              if($type=="with room")
                 {
            $hospital_wards_query = $this->db->query("SELECT p.*, h.id as ward,h.hospital_id,h.room_type,h.capacity,h.details,h.created_at,hp.package_amount FROM package_wards as p LEFT JOIN hospital_packages as hp ON (p.package_id=hp.id) LEFT JOIN hospital_wards as h ON (p.ward_id=h.id) WHERE p.hospital_id = '$user_id' and p.package_id='$package_id'");
            $hospital_wards_count = $hospital_wards_query->num_rows();
            $hospital_wards       = $hospital_wards_query->result_array();
            $hospital_wards_list= array();
            if($hospital_wards_count>0)
            {
               
                  
               
               
                foreach($hospital_wards as $wards)
                {
                    $wards_id    = $wards['ward'];
                    $hospital_id = $wards['hospital_id'];
                    $room_type   = $wards['room_type'];
                    $capacity    = $wards['capacity'];
                    $datails     = $wards['details'];
                    $price       = $wards['price'];
                    $created_at  = $wards['created_at'];
                    $package_amount= $wards['package_amount'];
                    $with_room = $wards['type_room'];
                    $final_amount=$price+$package_amount;
                    $hospital_wards_list[] = array(
                        'id'          => $wards_id,
                        'hospital_id' => $hospital_id,
                        'room_type'   => $room_type,
                        'capacity'    => $capacity,
                        'datails'     => $datails,
                        'price'       => $final_amount,
                        'created_at'  => $created_at,
                        'with_room'   => $with_room
                    ); 
                }
                
            }
            else
            {
                $hospital_wards_list = array();
            }
            
                 }   
          elseif($type=="without room")
          {
              $hospital_wards_query = $this->db->query("SELECT * FROM `hospital_wards` WHERE hospital_id = '$user_id' ");
            $hospital_wards_count = $hospital_wards_query->num_rows();
            $hospital_wards       = $hospital_wards_query->result_array();
            $hospital_wards_list= array();
            if($hospital_wards_count>0)
            {
               
                  
               
               
                foreach($hospital_wards as $wards)
                {
                    $wards_id    = $wards['id'];
                    $hospital_id = $wards['hospital_id'];
                    $room_type   = $wards['room_type'];
                    $capacity    = $wards['capacity'];
                    $datails     = $wards['details'];
                    $price       = $wards['price'];
                    $created_at  = $wards['created_at'];
                    //$package_amount= $wards['package_amount'];
                   // $with_room = $wards['type_room'];
                  //  $final_amount=$price+$package_amount;
                    $hospital_wards_list[] = array(
                        'id'          => $wards_id,
                        'hospital_id' => $hospital_id,
                        'room_type'   => $room_type,
                        'capacity'    => $capacity,
                        'datails'     => $datails,
                        'price'       => $price,
                        'created_at'  => $created_at,
                        'with_room'   => 'without room'
                    ); 
                }
                
            }
            else
            {
                $hospital_wards_list = array();
            }
          }
             else
             {
                 $hospital_wards_list = array();
             }
          
                
                
               
                $hospital_package[] = array(
                            'id'                 => $package_id,
                            'hospital_id'        => $hospital_id,
                            'package_name'       => $package_name,
                            'package_desc'       => $package_desc,
                            'package_amount'     => $package_amount,
                            'package_inclusive'  => $package_inclusive,
                            'package_exclusive'  => $package_exclusive,
                            'package_type'       => $package_type,
                            'package_benifit'    => $package_benifit,
                            'created_at'         => $created_at,
                            'Room_type'          =>$type,
                            'surgery_list'       => $hospital_surgery,
                            'wards_list' => $hospital_wards_list
                        ); 
            }
            $data = $hospital_package;
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $data,
            ); 
        }else{
            return array(
                'status' => 200,
                'message' => 'No Records Found!'
            );
        }
        
    }
    
    public function get_hospital_surgery($user_id){
        
        $query = $this->db->query("SELECT * FROM surgery_master WHERE hospital_id = '$user_id' ");
        $num_count = $query->num_rows();
        $qRows = $query->result_array();
      
        if($num_count>0)
        {
            foreach($qRows as $qRow)
            {
                $id                 = $qRow['id'];
                $hospital_id        = $qRow['hospital_id'];
                $surgery_name       = $qRow['surgery_name'];
                $surgery_desc       = $qRow['surgery_desc'];
                $conditions         = $qRow['conditions'];
                $pre_procedure      = $qRow['pre_procedure'];
                $during_procedure   = $qRow['during_procedure'];
                $post_procedure     = $qRow['post_procedure'];
                $instruction        = $qRow['instruction'];
                $risk_complication  = $qRow['risk_complication'];
                $other_details      = $qRow['other_details'];
                $created_at         = $qRow['created_at'];
               
                 $hospital_surgery[] = array(
                            'id'                 => $id,
                            'hospital_id'        => $hospital_id,
                            'surgery_name'       => $surgery_name,
                            'surgery_desc'       => $surgery_desc,
                            'conditions'         => $conditions,
                            'pre_procedure'      => $pre_procedure,
                            'during_procedure'   => $during_procedure,
                            'post_procedure'     => $post_procedure,
                            'instruction'        => $instruction,
                            'risk_complication'  => $risk_complication,
                            'other_details'      => $other_details,
                            'created_at'         => $created_at
                        ); 
            }
            $data = $hospital_surgery;
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $data,
            ); 
        }
        
    }
    
    public function get_hospital_wards($user_id,$package_id){
        //----WADRS FOR HOSPITAL EXITS OR NOT CHECKING-------package_wards
            $hospital_wards_query = $this->db->query("SELECT p.*, h.id as ward,h.hospital_id,h.room_type,h.capacity,h.details,h.created_at,hp.package_amount FROM package_wards as p LEFT JOIN hospital_packages as hp ON (p.package_id=hp.id) LEFT JOIN hospital_wards as h ON (p.ward_id=h.id) WHERE p.hospital_id = '$user_id' and p.package_id='$package_id'");
            $hospital_wards_count = $hospital_wards_query->num_rows();
            $hospital_wards       = $hospital_wards_query->result_array();
            $hospital_wards_list= array();
            if($hospital_wards_count>0)
            {
               
                  
               
               
                foreach($hospital_wards as $wards)
                {
                    $wards_id    = $wards['ward'];
                    $hospital_id = $wards['hospital_id'];
                    $room_type   = $wards['room_type'];
                    $capacity    = $wards['capacity'];
                    $datails     = $wards['details'];
                    $price       = $wards['price'];
                    $created_at  = $wards['created_at'];
                    $package_amount= $wards['package_amount'];
                    $with_room = $wards['type_room'];
                    $final_amount=$price+$package_amount;
                    $hospital_wards_list[] = array(
                        'id'          => $wards_id,
                        'hospital_id' => $hospital_id,
                        'room_type'   => $room_type,
                        'capacity'    => $capacity,
                        'datails'     => $datails,
                        'price'       => $final_amount,
                        'created_at'  => $created_at,
                        'with_room'   => $with_room
                    ); 
                }
                
            }
            return array(
                'status' => 200,
                'message' => 'success',
                'wards_list' => $hospital_wards_list,
            ); 
    }
    
    public function hospital_surgery_package($user_id){
        
        //----HOSPITAL EXITS OR NOT CHECKING-------
        $hospital_query = $this->db->query("SELECT * FROM hospitals WHERE user_id = '$user_id' ");
        $hospital_cont = $hospital_query->num_rows();
      
        if($hospital_cont>0)
        {
            //----SURGERY MASTER EXITS OR NOT CHECKING-------
            $surgery_query = $this->db->query("SELECT * FROM surgery_master WHERE hospital_id = '$user_id'");
            $surgery_count = $surgery_query->num_rows();
            $surgeries = $surgery_query->result_array();
          
            if($surgery_count>0)
            {  
                $hospital_surgery =array();
                foreach($surgeries as $surgery)
                {
                    $surgery_id         = $surgery['id'];
                    $hospital_id        = $surgery['hospital_id'];
                    $surgery_name       = $surgery['surgery_name'];
                    $surgery_desc       = $surgery['surgery_desc'];
                    $conditions         = $surgery['conditions'];
                    $pre_procedure      = $surgery['pre_procedure'];
                    $during_procedure   = $surgery['during_procedure'];
                    $post_procedure     = $surgery['post_procedure'];
                    $instruction        = $surgery['instruction'];
                    $risk_complication  = $surgery['risk_complication'];
                    $other_details      = $surgery['other_details'];
                    $created_at         = $surgery['created_at'];
                   
                            //----PACKAGE FOR SURGERY EXITS OR NOT CHECKING-------
                            $package_query = $this->db->query("SELECT * FROM hospital_packages WHERE surgery_id = '$surgery_id'");
                            $package_count = $package_query->num_rows();
                            $packages = $package_query->result_array();
                            
                            if($package_count>0)
                            {
                                $hospital_package =array();
                                foreach($packages as $pack)
                                {
                                    $package_id         = $pack['id'];
                                    $surgery_id         = $pack['surgery_id'];
                                    $hospital_id        = $pack['hospital_id'];
                                    $package_name       = $pack['package_name'];
                                    $package_desc       = $pack['package_desc'];
                                    $package_amount     = $pack['package_amount'];
                                    $package_inclusive  = $pack['package_inclusive'];
                                    $package_exclusive  = $pack['package_exclusive'];
                                    $package_type       = $pack['package_type'];
                                    $package_benifit    = $pack['package_benifit'];
                                    $created_at         = $pack['created_at'];
                                    $type               = $pack['type_room'];
                                     $hospital_package[] = array(
                                        'id'                 => $package_id,
                                        'surgery_id'         => $surgery_id,
                                        'hospital_id'        => $hospital_id,
                                        'package_name'       => $package_name,
                                        'package_desc'       => $package_desc,
                                        'package_amount'     => $package_amount,
                                        'package_inclusive'  => $package_inclusive,
                                        'package_exclusive'  => $package_exclusive,
                                        'package_type'       => $package_type,
                                        'package_benifit'    => $package_benifit,
                                        'created_at'         => $created_at,
                                        
                                    );
                                            
                                    
                                }
                                
                            }
                            
                             $hospital_surgery[] = array(
                                'id'                 => $surgery_id,
                                'hospital_id'        => $hospital_id,
                                'surgery_name'       => $surgery_name,
                                'surgery_desc'       => $surgery_desc,
                                'conditions'         => $conditions,
                                'pre_procedure'      => $pre_procedure,
                                'during_procedure'   => $during_procedure,
                                'post_procedure'     => $post_procedure,
                                'instruction'        => $instruction,
                                'risk_complication'  => $risk_complication,
                                'other_details'      => $other_details,
                                'created_at'         => $created_at,
                                'packages'           => $hospital_package
                            ); 
                            
                }
            }else{
                $hospital_surgery[] = array();
            }
            
            //----WADRS FOR HOSPITAL EXITS OR NOT CHECKING-------
            $hospital_wards_query = $this->db->query("SELECT * FROM hospital_wards WHERE hospital_id = '$user_id'");
            $hospital_wards_count = $hospital_wards_query->num_rows();
            $hospital_wards       = $hospital_wards_query->result_array();
            $hospital_wards_list= array();
            if($hospital_wards_count>0)
            {
               
                foreach($hospital_wards as $wards)
                {
                    $wards_id    = $wards['id'];
                    $hospital_id = $wards['hospital_id'];
                    $room_type   = $wards['room_type'];
                    $capacity    = $wards['capacity'];
                    $datails     = $wards['details'];
                    $price       = $wards['price'];
                    $created_at  = $wards['created_at'];
                   
                    $hospital_wards_list[] = array(
                        'id'          => $wards_id,
                        'hospital_id' => $hospital_id,
                        'room_type'   => $room_type,
                        'capacity'    => $capacity,
                        'datails'     => $datails,
                        'price'       => $price,
                        'created_at'  => $created_at
                    ); 
                }
                
            }
            
            return array(
                'status' => 200,
                'message' => 'success',
                'surgery_list' => $hospital_surgery,
                'wards_list' => $hospital_wards_list,
            ); 
            
        }
        
    }
    
    public function hospital_booking($user_id,$listing_id,$package_id,$ward_id,$name,$gender, $patient_name, $patient_relation, $patient_dob, $patient_gender, $amount, $patient_age, $patient_allergies, $emergency, $patient_preferred_date, $patient_addiction, $ambulance_pickup_address,$patient_id, $ambulance_drop_address) {
        $lastid ="";
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $booking_id  = date("YmdHis");
        $gallery_query = $this->db->query("SELECT email,phone,name FROM users WHERE id='$user_id' ");
        $email = $gallery_query->row()->email;
        $phone = $gallery_query->row()->phone;
        $user_name = $gallery_query->row()->name;
        
        
        
       
        $gallery_query = $this->db->query("SELECT * FROM booking_master WHERE user_id='$user_id' AND  listing_id = '$listing_id' AND vendor_id='8' AND status = 'Pnding'");
        $gallery_count = $gallery_query->num_rows();
        //die();
        if ($gallery_count == 0) {
             
             
             $Insert_health_record  = array(
            'user_id' => $user_id,
            'patient_name' => $patient_name,
            'patient_age' => $patient_age,
            'patient_city' =>"",
            'gender' => $patient_gender,
            'date_of_birth'=>$patient_dob,
            'relationship'=>$patient_relation,
            'allergies'=>$patient_allergies,
            'created_at'=>date('Y-m-d H:i:s'),
            
        );
        $patient_details = $this->db->query("SELECT * FROM health_record WHERE id='$patient_id'");
        $patinet_count = $patient_details->num_rows();
        if($patinet_count>0)
        {
      
        }
       else
       {
            $this->db->insert('health_record', $Insert_health_record);
            $patient_id=$this->db->insert_id();
           ////pass same patient id as existing patient id
       }
        $appointment_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'patient_id' => $patient_id,
            'booking_id' => $booking_id,
            'package_id'=>$package_id,
            'user_name' => $name,
            'user_gender' => $gender,
            'user_email' => $email,
            'user_mobile' => $phone,
            'booking_date'=>date('Y-m-d H:i:s'),
            'joining_date'=>date('H:i:s'),
            'status' => 'Pending',
            'vendor_id' => '8' 
        );
        
        $booking_details_array = array(
           
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'booking_id' => $booking_id,
            'package_id'=>$package_id,
            'ward_id'=>$ward_id,
            'emergency'=> $emergency,
            'patient_preferred_date'=>$patient_preferred_date,
            'patient_addiction'=>$patient_addiction,
            'ambulance_pickup_address'=>$ambulance_pickup_address,
            'ambulance_drop_address'=>$ambulance_drop_address,
            'patient_id' => $patient_id,
            'amount' => $amount,
            'patient_name' => $patient_name,
            'patient_gender' => $patient_gender,
            'patient_age' => $patient_age,
            'patient_allergies' => $patient_allergies,
            'user_name' => $name,
            'user_gender' => $gender,
            'user_email' => $email,
            'user_mobile' => $phone,
            'booking_date'=>date('Y-m-d H:i:s'),
            'booking_time'=>date('H:i:s'),
            'status' => 'Pending',
            'vendor_type' => '8' 
        );
        
        $this->db->insert('hospital_booking_details', $booking_details_array);         
        $this->db->insert('booking_master', $appointment_array);
        $lastid=$this->db->insert_id();
             if($lastid){
                 
                  //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
            //echo "SELECT name FROM users WHERE id='$user_id'";
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
                
                 $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $listing_id,
                      'booking_id'  => $booking_id,
                       'invoice_no' => "",
                       'user_id'  => $user_id,
                       'notification_type'  => 'Hospital_booking',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
                );
                $this->db->insert('All_notification_Mobile', $notification_array);
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
            
            //print_r($post_data);
            
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
            
            
            
            
            // For Hospitals vendor website firebase push notification by ghanshyam parihar starts :: Added date: 05 Feb 2019
            
            $vendor_info  = $this->db->select('phone,name,web_token,token_status,agent')->from('users')->where('id', $listing_id)->get()->row();
            // print_r($vendor_info);
            $vendor_name = $vendor_info->name;
            $web_message      = $vendor_name . ', Your hospital package has been booked by patient ' . $user_name . ' successfully and Booking Id is ' . $booking_id . ' for future reference.';
            
            $web_token      = $vendor_info->web_token;
            $title_web      = $user_name.' has booked an package';
            $img_url        = $userimage;
            // $img_url      = 'https://medicalwale.com/img/medical_logo.png';
            $tag            = 'text';
            $agent          = $vendor_info->agent;;
            $connect_type   = 'hospital_booking';
            // Web notification FCM function by ghanshyam parihar date:4 Feb 2019
            // $click_action = 'https://hospitals.medicalwale.com/Appointments/booking_appointment/'.$listing_id;
            $click_action = 'https://vendor.sandbox.medicalwale.com/hospitals/Appointments/booking_appointment/'.$listing_id;
            $this->send_gcm_web_notify($title_web, $web_message, $web_token, $img_url, $tag, $agent, $connect_type,$click_action);
            // Web notification FCM function by ghanshyam parihar date:4 Feb 2019 Ends

            // For Hospitals vendor website firebase push notification by ghanshyam parihar starts :: Added date: 05 Feb 2019     
                 
                 
                 
                 
                 $this->insert_notification_book_package($user_id, $lastid, 'user',$listing_id);
             }
        }   
            
            
            /*$arr_data =  array(
                'booking_id'=> $booking_id    
            );  */ 
          return  $booking_id;
    }
    
    // Web notification FCM function by ghanshyam parihar added date:05 Feb 2019
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
            // print_r($result);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
    }
    // Web notification FCM function by ghanshyam parihar added date:05 Feb 2019
    
    public function insert_notification_book_package($user_id, $post_id, $name, $listing_id){
        $data = array(
							'user_id'		=> $user_id,
							'post_id' 		=> $post_id,
							'timeline_id'   => $user_id,
							'type'          => 'booking',
							'seen'          => '1',
							'notified_by'   => $listing_id,
							'description'   => ' booked a package from Surgery Package',
							'created_at'	=> curr_date(), 
							'updated_at'	=> curr_date()
							
					);
		//print_r($data);
		$this->db->insert("notifications", $data);

		if($this->db->affected_rows() > 0)
		{
		    
		    return true; // to the controller
		}
		else{
			return false;
		}
	}
	
	public function hospital_doctor_list($hospital_id) {


    //echo "SELECT doctor_list.id,doctor_list.consultation_fee,doctor_list.lat,doctor_list.lng,doctor_list.category,doctor_list.user_id,doctor_list.doctor_name,doctor_list.about_us,doctor_list.speciality,doctor_list.address,doctor_list.telephone,doctor_list.medical_college,doctor_list.medical_affiliation,doctor_list.charitable_affiliation,doctor_list.awards_recognition,doctor_list.all_24_hrs_available,doctor_list.home_visit_available,doctor_list.qualification,doctor_list.experience,doctor_list.website,doctor_list.location,doctor_list.days,doctor_list.timing,doctor_list.image,doctor_list.rating,doctor_list.review FROM doctor_list INNER JOIN doctor_hospital_list ON doctor_list.id=doctor_hospital_list.doctor_id WHERE doctor_hospital_list.hospital_id='$hospital_id'";
        
        $query = $this->db->query("SELECT doctor_list.id,doctor_list.consultation_fee,doctor_list.lat,doctor_list.lng,doctor_list.category,doctor_list.user_id,doctor_list.doctor_name,doctor_list.about_us,doctor_list.speciality,doctor_list.address,doctor_list.telephone,doctor_list.medical_college,doctor_list.medical_affiliation,doctor_list.charitable_affiliation,doctor_list.awards_recognition,doctor_list.all_24_hrs_available,doctor_list.home_visit_available,doctor_list.qualification,doctor_list.experience,doctor_list.website,doctor_list.location,doctor_list.days,doctor_list.timing,doctor_list.image,doctor_list.rating,doctor_list.review FROM doctor_list INNER JOIN doctor_hospital_list ON doctor_list.id=doctor_hospital_list.doctor_id WHERE doctor_hospital_list.hospital_id='$hospital_id'");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $doctor_name = $row['doctor_name'];
                $about_us = $row['about_us'];
                $speciality = $row['category']; //changed by david
                $address = $row['address'];
                $telephone = $row['telephone'];
                $medical_college = $row['medical_college'];
                $medical_affiliation = $row['medical_affiliation'];
                $charitable_affiliation = $row['charitable_affiliation'];
                $awards_recognition = $row['awards_recognition'];
                $hrs_available = $row['24_hrs_available'];
                $home_visit_available = $row['home_visit_available'];
                $qualification = $row['qualification'];
                $consultation_fee = $row['consultation_fee'];
                $experience = $row['experience'];
                $website = $row['website'];
                $location = $row['location'];
                $days = $row['days'];
                $timing = $row['timing'];
                $rating = $row['rating'];
                $reviews = $row['review'];
                $image = $row['image'];
                $doctor_user_id = $row['user_id'];
                $profile_views = '2458';
                $is_follow = 'yes';

                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $image;
                } else {
                    $image = '';
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                $result_hospital = '';

                $hospital_query = $this->db->query("select hospitals.id as hospital_id,doctor_list.id as doctor_id,hospitals.name_of_hospital,hospitals.address,hospitals.rating,hospitals.image,doctor_hospital_list.opening_days from doctor_list INNER JOIN doctor_hospital_list on doctor_hospital_list.doctor_id = doctor_list.id INNER JOIN hospitals on hospitals.id=doctor_hospital_list.hospital_id WHERE doctor_list.id='$id'");
                $total_hospital = $hospital_query->num_rows();
                if ($total_hospital > 0) {
                    foreach ($hospital_query->result_array() as $hospital_row) {
                        $id = $hospital_row['hospital_id'];
                        $hospital_name = $hospital_row['name_of_hospital'];
                        $address = $hospital_row['address'];
                        $rating = $hospital_row['rating'];
                        $hospital_image = $hospital_row['image'];
                        $opening_days = $hospital_row['opening_days'];
                        if ($hospital_image != '') {
                            $hospital_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $hospital_image;
                        } else {
                            $hospital_image = '';
                        }


                        date_default_timezone_set('Asia/Kolkata');
                        $open_days = '';
                        $day = '';
                        $time = '';
                        $start_time = '';
                        $end_time = '';
                        $opening_hours = explode(',', $opening_days);
                        foreach ($opening_hours as $opening_hour) {
                            $array_hours = explode('-', $opening_hour);
                            $day = $array_hours[0];
                            $start_time = $array_hours[1];
                            $end_time = $array_hours[2];
                            $time = $start_time . ' - ' . $end_time;
                            $open_days[] = array(
                                'day' => $day,
                                'time' => $time
                            );
                        }

                        $result_hospital[] = array(
                            'id' => $id,
                            'hospital_name' => $hospital_name,
                            'address' => $address,
                            'rating' => $rating,
                            'image' => $hospital_image,
                            'opening_day' => $open_days
                        );
                    }
                } else {
                    $result_hospital = array();
                }
                $service = '';
                $result_services = '';
                $doctor_services_query = $this->db->query("SELECT service FROM `doctor_services`");
                foreach ($doctor_services_query->result_array() as $doctor_services) {
                    $service = $doctor_services['service'];
                    $result_services[] = array(
                        'service' => $service
                    );
                }
                $specialization = '';
                $result_specialization = '';
                $doctor_specialization_query = $this->db->query("SELECT specialization FROM `doctor_specialization`");
                foreach ($doctor_specialization_query->result_array() as $doctor_specialization) {
                    $specialization = $doctor_specialization['specialization'];
                    $result_specialization[] = array(
                        'specialization' => $specialization
                    );
                }


                $resultpost[] = array(
                    'doctor_id' => $id,
                    'doctor_user_id' => $doctor_user_id,
                    'doctor_name' => $doctor_name,
                    'about_us' => $about_us,
                    'speciality' => $speciality,
                    'address' => $address,
                    'telephone' => $telephone,
                    'medical_college' => $medical_college,
                    'medical_affiliation' => $medical_affiliation,
                    'charitable_affiliation' => $charitable_affiliation,
                    'awards_recognition' => $awards_recognition,
                    'hrs_available' => $hrs_available,
                    'home_visit_available' => $home_visit_available,
                    'qualification' => $qualification,
                    'experience' => $experience,
                    'consultation_fee' => $consultation_fee,
                    'website' => $website,
                    'location' => $location,
                    'days' => $days,
                    'timing' => $timing,
                    'rating' => $rating,
                    'review' => $reviews,
                    'image' => $image,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'is_follow' => $is_follow,
                    'doctor_practices' => $result_hospital,
                    'doctor_services' => $result_services,
                    'doctor_specialization' => $result_specialization
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }
    
    public function hospital_doctor_list_new($mlat, $mlng,$hospital_id) {


     $radius = '3';
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
            $dist       = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return $dist;
        }   
        
        $query = $this->db->query("SELECT hospital_doctor_list.*,speciality.name as speciality_name FROM hospital_doctor_list LEFT JOIN hospitals_specialist as speciality ON speciality.id = hospital_doctor_list.speciality  WHERE hospital_doctor_list.hospital_id='$hospital_id'");
      //  $result = $query->result_array(); 
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $doctor_id          = $row['id'];
                $profile_img        = $row['profile_img'];
                $hospital_id        = $row['hospital_id']; //changed by david
                $doctor_name        = $row['doctor_name'];
                $qualifications     = $row['qualifications'];
                $timing             = $row['timing'];
                $consultation       = $row['consultation'];
                $category           = $row['category'];
                $practice_at        = $row['practice_at'];
                $speciality         = $row['speciality'];
                $services           = $row['services'];
                $email              = $row['email'];
                $phone              = $row['phone'];
                $gender             = $row['gender'];
                $address            = $row['address'];
                $city               = $row['city'];
                $state              = $row['state'];
                $pincode            = $row['pincode'];
                $lat                = $row['lat'];
                $lng                = $row['lng'];
                $followers           = '0';
                $following           = '0';
                $profile_views       = '0';
                $total_reviews       = '0';
                $total_rating        = '0';
                $total_profile_views = '0';
                
                $followers           = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_id)->get()->num_rows();
                $following           = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_id)->get()->num_rows();
                $is_follow           = $this->db->select('id')->from('follow_user')->where('user_id', $hospital_id)->where('parent_id', $doctor_id )->get()->num_rows();
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                
                $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM hospital_review WHERE hospital_id='$doctor_id'");
               // echo "SELECT  ROUND(AVG(rating),1) AS total_rating FROM hospital_review WHERE user_id='$doctor_id'";
                $row_rating   = $query_rating->row_array();
                $total_rating = $row_rating['total_rating'];
                if ($total_rating === NULL || $total_rating === '') {
                    $total_rating = '0';
                }
                $total_reviews       = $this->db->select('id')->from('hospital_review')->where('user_id', $doctor_id)->get()->num_rows();
                $profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $hospital_id)->get()->num_rows();
                
                if ($row['profile_img'] != '') {
                    $profile_pic = $row['profile_img'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }
                
                
                $doctor_consultation1[] = array(
                            'charges' => $consultation,
                        );
                $doctor_consultation = $doctor_consultation1;
                $doctor_consultation1 = array();
                
                //Services
                $services_array = array();
                $query_sp2       = $this->db->query("SELECT service_name FROM hospital_services WHERE  FIND_IN_SET(id,'" . $services . "')");
                $total_services = $query_sp2->num_rows();
                if ($total_services > 0) {
                    foreach ($query_sp2->result_array() as $get_sp) {
                     
                        $area_services  = $get_sp['service_name'];
                        $services_array[] = array(
                         
                            'service' => $area_services
                        );
                    }
                } else {
                    $services_array = array();
                }
                $services_array1 = $services_array;
                $services_array =array();
                
                //categoty
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
                $area_expertise1 = $area_expertise;
                $area_expertise =array();
                
                //speciality
                $query_sp1       = $this->db->query("SELECT id,name FROM hospitals_specialist WHERE  FIND_IN_SET(id,'" . $speciality . "')");
                $total_speciality = $query_sp->num_rows();
                if ($total_speciality > 0) {
                    foreach ($query_sp1->result_array() as $get_sp) {
                      
                        $speciality_name  = $get_sp['name'];
                        $speciality_array[] = array(
                            'speciality' => $speciality_name
                        );
                    }
                } else {
                    $speciality_array = array();
                }
                $speciality_array1 = $speciality_array;
                $speciality_array =array();
               
                $opening_hours = explode('|', $timing);
                        foreach ($opening_hours as $opening_hour) {
                            $array_hours = explode('>', $opening_hour);
                            $day = $array_hours[0];
                            $start_time = $array_hours[1];
                            $time = $start_time ;
                            $open_days1[] = array(
                                'day' => $day,
                                'time' => $time
                            );
                        }
                        //$open_days =$open_days1;
                        $open_days=array();
                        $open_days1=array();
                        if($lat == "" || $lng == "" || $mlat == "" || $mlng == "")
                        {
                            $distances ="";
                        }
                        else
                        {
                            $distances    = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                        }
            $result_hospital[] = array(
                            'doctor_id' => $doctor_id,
                            'hospital_id' => $hospital_id,
                            'listing_type' => 8,
                            'lat' => $lat,
                            'lng' => $lng,
                            'distance' => $distances,
                            'doctor_name' => $doctor_name,
                            'email' => $email,
                            'gender' => $gender,
                            'doctor_phone' => $phone,
                            'dob' => "",
                            'experience' => "",
                            'reg_council' => "",
                            'reg_number' => "",
                            'profile_pic' => $profile_pic,
                            'address' => $address,
                            'city' => $city,
                            'state' => $state,
                            'pincode' => $pincode,
                            'doctor_consultation' =>$doctor_consultation, //$doctor_consultation,
                            'area_expertise' => $area_expertise1,
                            'doctor_specialization' => $speciality_array1,
                            'doctor_services' => $services_array1,
                            'degree' => $qualifications,
                            'practices' => array(),
                            'rating' => $total_rating,
                            'review' => $total_reviews,
                            'followers' => $followers,
                            'following' => $following,
                            'profile_views' => $profile_views,
                            'is_follow' => $is_follow,
                            'opening_day' => $open_days
                        );
                        
                        $timing =array();
            }            
         
            $resultpost = $result_hospital;
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }
    
     public function hospital_appointment_list($user_id)
    {
         $booking_details = $this->db->query("SELECT * FROM booking_master WHERE user_id='$user_id' and vendor_id='8' order by id DESC");
         
        $booking_count = $booking_details->num_rows();
        if($booking_count>0)
        {
           foreach($booking_details ->result_array() as $row )
           {
               $id = $row['id'];
               $booking_id = $row['booking_id'];
               $package_id = $row['package_id'];
              $listing_id = $row['listing_id'];
              $vendor_detaild = $this->db->query("SELECT name,phone FROM users WHERE id='$listing_id' ");
              $vendor_count = $vendor_detaild->num_rows();
              $vendor_name ='';
              if($vendor_count>0)
              {
             $vendor_name = $vendor_detaild->row()->name;
              }
              
              $hospital_details = $this->db->query("SELECT * FROM hospital_booking_details WHERE booking_id='$booking_id' and vendor_type='8' order by id DESC");
              $hospital_count = $hospital_details->num_rows();
              $hospital_name ='';
              if($hospital_count>0)
              {
             $ward_id = $hospital_details->row()->ward_id;
             $amount = $hospital_details->row()->amount;
             $patient_preferred_date = $hospital_details->row()->patient_preferred_date;
              }
              
              $ward_details = $this->db->query("SELECT * FROM hospital_wards WHERE hospital_id='$listing_id'");
              $ward_count = $ward_details->num_rows();
              $room_type ='';
                $capacity ='';
                  $price ='';
              if($ward_count>0)
              {
             $room_type = $ward_details->row()->room_type;
             $capacity = $ward_details->row()->capacity;
             $price =  $ward_details->row()->price;
              }
              
              $pack_details = $this->db->query("SELECT * FROM hospital_packages WHERE id='$package_id'");
              $pack_count = $pack_details->num_rows();
            
              if($pack_count>0)
              {
             $package_name = $pack_details->row()->package_name;
              }
              
              
              
              $user_id = $row['user_id'];
              $patient_id = $row['patient_id'];
              $name = $row['user_name'];
             $phone =$row['user_mobile'];
             $email = $row['user_email'];
             $gender = $row['user_gender'];
             $branch_id = $row['branch_id'];
              $vendor_id=$row['vendor_id'];
             $booking_date = $row['booking_date'];
            $status = $row['status'];
            $joining_date = $row['joining_date'];
            $category_id = $row['category_id'];
            $booking_address = $row['booking_address'];
            $booking_mobile = $row['booking_mobile'];
            
            
            $details[] = array(
                    'id' => $id,
                    'booking_id' => $booking_id,
                    'package_id'=> $package_id,
                    'listing_id' => $listing_id,
                    'user_id' => $user_id,
                    'patient_id' => $patient_id,
                    'package_name'=>$package_name,
                    'name' => $name,
                    'phone' => $phone,
                    'email' => $email,
                    'gender' => $gender,
                    'branch_id' =>$branch_id,
                    'vendor_id' =>$vendor_id,
                    'booking_date'=>$booking_date,
                    'status' => $status,
                    'joining_date' => $patient_preferred_date,
                    'category_id' => $category_id,
                    'booking_address' => $booking_address,
                    'booking_mobile' => $booking_mobile,
                    'vendor_name' => $vendor_name,
                    'ward_id' => $ward_id,
                    'amount' => $amount,
                    'room_type'=> $room_type,
                    'capacity' => $capacity,
                    'price' => $price
                );
            
           }
           return $result = array(
               'status' => 200,
               'message' => 'success',
               'data' => $details
               ); ;
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
                "notification_type" => 'Hospital_booking',
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
    
    
    //***************************added for wellness clinic module by zak**********************************
   public function wellness_clinic_list($latitude, $longitude, $user_id, $category_name)
   {
        $radius = '5';

       // $query = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'");
       
       $query = $this->db->query("SELECT `id`,`user_id`,`name_of_hospital`,`about_us`,`establishment_year`,`certificates_accred`,`category`,`speciality`,`surgery`,`services`,`address`,`pincode`,`phone`,`city`,`state`, `email`, `lat`, `lng`,`image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`, ( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM wellness_clinic HAVING distance <= '$radius'");
        $count = $query->num_rows();
         if ($count > 0) {
            
            foreach ($query->result_array() as $row) {
                
            $id = $row['id'];
            $wellness_user_id = $row['user_id'];
            $wellness_email = $row['email'];
            $queryTocheck = $this->db->query("SELECT id,name,phone FROM users WHERE id='$wellness_user_id' and email='$wellness_email'");
            //echo "SELECT id,name,phone FROM users WHERE id='$hospital_user_id' and email='$hospital_email'";
            /*die();*/
            $query_count = $queryTocheck->num_rows();

            if($query_count>0)
              {
                $name_of_wellness = $row['name_of_hospital'];
                $mobile = $row['phone'];
                $about_us = $row['about_us'];
                $establishment_year = $row['establishment_year'];
                $certificates_accred = $row['certificates_accred'];
                $category = $row['category'];
                $speciality = $row['speciality'];
                $surgery = $row['surgery'];
                $services = $row['services'];
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
                
                $profile_views = '0';

                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $image;
                } else {
                    $image = '';
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $wellness_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $wellness_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $wellness_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                //temp commented for wellness
                // if($certificates_accred != ''){
                //     $certificates_accred_query = $this->db->query("SELECT `name`, image FROM `hospital_certificates_accred` WHERE FIND_IN_SET(name,'" . $certificates_accred . "')");
                //     foreach ($certificates_accred_query->result_array() as $get_clist) {
                //         $certificates_name = $get_clist['name'];
                //         $certificates_image = $get_clist['image'];
    
                //         if ($certificates_image != '') {
                //             $certificates_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $certificates_image;
                //         } else {
                //             $certificates_image = '';
                //         }
    
    
                //         $certificates_accred_list[] = array(
                //             "certificates_name" => $certificates_name,
                //             "certificates_image" => $certificates_image
                //         );
                //     }
                // }
                $wellness_surgery_list = array();
                if($surgery != ''){
                    $wellness_surgery_query = $this->db->query("SELECT `id`,`surgery_name`,`surgery_rate`,`surgery_package`, image FROM `wellness_surgery` WHERE FIND_IN_SET(surgery_name,'" . $surgery . "')");
                    foreach ($wellness_surgery_query->result_array() as $get_slist) {
                        $surgery_id = $get_slist['id'];
                        $surgery_name = $get_slist['surgery_name'];
                        $surgery_rate = $get_slist['surgery_rate'];
                        $surgery_package = $get_slist['surgery_package'];
                        $surgery_image = $get_slist['image'];
    
                        if ($surgery_image != '') {
                            $surgery_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $surgery_image;
                        } else {
                            $surgery_image = '';
                        }
    
    
                        $wellness_surgery_list[] = array(
                            "surgery_id" => $surgery_id,
                            "surgery_name" => $surgery_name,
                            "surgery_rate" => $surgery_rate,
                            "surgery_package" => $surgery_package,
                            "surgery_image" => $surgery_image
                        );
                    }
                }


                $gallery = array();
                $gallery_query = $this->db->query("SELECT `title`, CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/',source) AS source FROM `wellness_media_img` WHERE hospital_id='$id'");
                foreach ($gallery_query->result_array() as $get_glist) {
                    $title = $get_glist['title'];
                    $media_image = $get_glist['source'];
                    $gallery[] = array(
                        "title" => $title,
                        "image" => $media_image
                    );
                }

                $wellness_service_list = array();
                if($services != ''){
                    $wellness_services_query = $this->db->query("SELECT `service_name` FROM `wellness_services` WHERE FIND_IN_SET(service_name,'" . $services . "')");
                    foreach ($wellness_services_query->result_array() as $get_serlist) {
                        $service_name = $get_serlist['service_name'];
                        $wellness_service_list[] = array(
                            "service_name" => $service_name
                        );
                    }
                }

                $wellness_speciality_list = array();
                if($speciality != ''){
                    $wellness_speciality_query = $this->db->query("SELECT `id`,`doctors_category`,`name`, image FROM `wellness_specialist` WHERE FIND_IN_SET(name,'" . $speciality . "')");
                    foreach ($wellness_speciality_query->result_array() as $get_splist) {
                        $specialist_id = $get_splist['id'];
                        $specialist_name = $get_splist['name'];
                        $doctors_category = $get_splist['doctors_category'];
                        $specialist_image = $get_splist['image'];
    
                        if ($specialist_image != '') {
                            $specialist_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $specialist_image;
                        } else {
                            $specialist_image = '';
                        }
    
                        $wellness_speciality_list[] = array(
                            "specialist_id" => $specialist_id,
                            "specialist_name" => $specialist_name,
                            "doctors_category" => $doctors_category,
                            "specialist_image" => $specialist_image
                        );
                    }
                }
                
                $branch_list  = array();
                $branch_query = $this->db->query("SELECT * FROM `wellness_branch` WHERE hospital_id='$wellness_user_id' order by id asc");
              //  echo"SELECT * FROM `wellness_branch` WHERE hospital_id='$wellness_user_id' order by id asc";
                //die();
                $branch_count = $branch_query->num_rows();
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $branch_row) {
                        $branch_id                              = $branch_row['id'];
                        $branch_listing_id                      = $branch_row['hospital_id'];
                        $branch_hospital_branch_name            = $branch_row['name_of_branch'];
                        $branch_about_us                        = $branch_row['about_us'];
                        $branch_establishment_year              = $branch_row['establishment_year'];
                        $certificates_accred_list               = $branch_row['certificates_accred'];
                        $branch_category                        = $branch_row['category'];
                        $hospitals_speciality_list              = $branch_row['speciality'];
                        $hospitals_surgery_list                 = $branch_row['surgery'];
                        $hospitals_service_list                 = $branch_row['services'];
                        $branch_address                         = $branch_row['address'];
                        $branch_pincode                         = $branch_row['pincode'];
                        $branch_phone                           = $branch_row['phone'];
                        $branch_city                            = $branch_row['city'];
                        $branch_state                           = $branch_row['state'];
                        $branch_email                           = $branch_row['email'];
                        $branch_lat                             = $branch_row['lat'];
                        $branch_lng                             = $branch_row['lng'];
                        $branch_map_location                    = $branch_row['map_location'];
                        $branch_image                           = $branch_row['image'];
                        $branch_rating                          = $branch_row['rating'];
                        $branch_review                          = $branch_row['review'];
                        $branch_date                            = $branch_row['date'];
                        $branch_is_active                        = $branch_row['is_active'];
                        $is_approval                             = $branch_row['is_approval'];
                        $approval_date                        = $branch_row['approval_date'];
                        $is_active_date                        = $branch_row['is_active_date'];
                        


                        $branch_list[] = array(
                            'hospital_id' => $branch_id,
                            'hospital_id' => $branch_listing_id,
                            'name_of_branch' => $branch_listing_id,
                            'name_of_hospital' =>$branch_hospital_branch_name,
                            'about_us' => $branch_about_us,
                            'establishment_year' => $branch_establishment_year,
                            'certificates_accred_list'=>$certificates_accred_list,
                            'category' => $branch_category,
                            'speciality' => $hospitals_speciality_list,
                            'surgery' => $hospitals_surgery_list,
                            'services' => $hospitals_service_list,
                            'address' => $branch_address,
                            'pincode' => $branch_pincode,
                            'phone' => $branch_phone,
                            'lat' => $branch_lat,
                            'lng' => $branch_lng,
                            'map_location' => $branch_map_location,
                            'city' => $city,
                            'state' => $state,
                            'email' => $branch_email,
                            'image' => $branch_image,
                            'review' => $reviews,
                            'rating' => $branch_review,
                            'date' => $branch_date,
                            'is_active' => $branch_is_active,
                            'is_approval' => $is_approval,
                            'approval_date' => $approval_date,
                            'is_active_date' => $is_active_date
                        );
                    }
                }
                

                $resultpost[] = array(
                    'hospital_id' => $id,
                    'hospital_user_id' => $wellness_user_id,
                    'name_of_hospital' => $name_of_wellness,
                    'listing_type' => "8",
                    'about_us' => $about_us,
                    'establishment_year' => $establishment_year,
                  //  'certificates_accred_list' => "$certificates_accred_list",
                  'certificates_accred_list' => "",
                    'hospitals_surgery_list' => $wellness_surgery_list,
                    'gallery' => $gallery,
                    'hospitals_service_list' => $wellness_service_list,
                    'hospitals_speciality_list' => $wellness_speciality_list,
                    'address' => $address,
                    'mobile' => $mobile,
                    'lat' => $lat,
                    'lng' => $lng,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'rating' => $rating,
                    'review' => $reviews,
                    'image' => $image,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'is_follow' => $is_follow,
                    'user_discount' => $user_discount,
                    'branch_list' => $branch_list
                );
            }
            else{
                $resultpost = array();
            }
            
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
   }
   
   //***************************added for wellness clinic details by zak***********************************
    public function wellness_clinic_details($user_id, $listing_id) {
        $radius = '5';
        $query = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year` FROM wellness_clinic WHERE user_id='$listing_id' limit 1");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name_of_hospital = $row['name_of_hospital'];
                $mobile = $row['phone'];
                $about_us = $row['about_us'];
                $establishment_year = $row['establishment_year'];
                $certificates_accred = $row['certificates_accred'];
                $category = $row['category'];
                $speciality = $row['speciality'];
                $surgery = $row['surgery'];
                $services = $row['services'];
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
                $hospital_user_id = $row['user_id'];
                $profile_views = '2458';

                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $image;
                } else {
                    $image = '';
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $hospital_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $hospital_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $hospital_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $certificates_accred_list = array();
                // $certificates_accred_query = $this->db->query("SELECT `name`, image FROM `hospital_certificates_accred` WHERE FIND_IN_SET(name,'" . $certificates_accred . "')");
                // foreach ($certificates_accred_query->result_array() as $get_clist) {
                //     $certificates_name = $get_clist['name'];
                //     $certificates_image = $get_clist['image'];

                //     if ($certificates_image != '') {
                //         $certificates_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $certificates_image;
                //     } else {
                //         $certificates_image = '';
                //     }


                //     $certificates_accred_list[] = array(
                //         "certificates_name" => $certificates_name,
                //         "certificates_image" => $certificates_image
                //     );
                // }

                $wellness_surgery_list = array();
                // $wellness_surgery_query = $this->db->query("SELECT `id`,`surgery_name`,`surgery_rate`,`surgery_package`, image FROM `wellness_surgery` WHERE FIND_IN_SET(surgery_name,'" . $surgery . "')");
                // foreach ($wellness_surgery_query->result_array() as $get_slist) {
                //     $surgery_id = $get_slist['id'];
                //     $surgery_name = $get_slist['surgery_name'];
                //     $surgery_rate = $get_slist['surgery_rate'];
                //     $surgery_package = $get_slist['surgery_package'];
                //     $surgery_image = $get_slist['image'];

                //     if ($surgery_image != '') {
                //         $surgery_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $surgery_image;
                //     } else {
                //         $surgery_image = '';
                //     }

                //     $wellness_surgery_list[] = array(
                //         "surgery_id" => $surgery_id,
                //         "surgery_name" => $surgery_name,
                //         "surgery_rate" => $surgery_rate,
                //         "surgery_package" => $surgery_package,
                //         "surgery_image" => $surgery_image
                //     );
                // }

                $gallery = array();
                $gallery_query = $this->db->query("SELECT `title`, CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/',source) AS source FROM `wellness_media_img` WHERE hospital_id='$id'");
                foreach ($gallery_query->result_array() as $get_glist) {
                    $title = $get_glist['title'];
                    $image = $get_glist['source'];
                    $gallery[] = array(
                        "title" => $title,
                        "image" => $image
                    );
                }

                $wellness_service_list = array();
                $wellness_services_query = $this->db->query("SELECT `service_name` FROM `wellness_services` WHERE FIND_IN_SET(service_name,'" . $services . "')");
                foreach ($wellness_services_query->result_array() as $get_serlist) {
                    $service_name = $get_serlist['service_name'];
                    $hospitals_service_list[] = array(
                        "service_name" => $service_name
                    );
                }

                $wellness_speciality_list = array();
                $wellness_speciality_query = $this->db->query("SELECT `id`,`doctors_category`,`name`, image FROM `wellness_specialist` WHERE FIND_IN_SET(name,'" . $speciality . "')");
                foreach ($wellness_speciality_query->result_array() as $get_splist) {
                    $specialist_id = $get_splist['id'];
                    $specialist_name = $get_splist['name'];
                    $doctors_category = $get_splist['doctors_category'];
                    $specialist_image = $get_splist['image'];

                    if ($specialist_image != '') {
                        $specialist_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $specialist_image;
                    } else {
                        $specialist_image = '';
                    }

                    $wellness_speciality_list[] = array(
                        "specialist_id" => $specialist_id,
                        "specialist_name" => $specialist_name,
                        "doctors_category" => $doctors_category,
                        "specialist_image" => $specialist_image
                    );
                }


                $resultpost[] = array(
                    'hospital_id' => $id,
                    'hospital_user_id' => $hospital_user_id,
                    'name_of_hospital' => $name_of_hospital,
                    'about_us' => $about_us,
                    'establishment_year' => $establishment_year,
                    'certificates_accred_list' => $certificates_accred_list,
                    //'hospitals_surgery_list' => $wellness_surgery_list,
                    'gallery' => $gallery,
                    'hospitals_service_list' => $wellness_service_list,
                    'hospitals_speciality_list' => $wellness_speciality_list,
                    'address' => $address,
                    'mobile' => $mobile,
                    'lat' => $lat,
                    'lng' => $lng,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'rating' => $rating,
                    'review' => $reviews,
                    'image' => $image,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'is_follow' => $is_follow
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }
    
    
    
    //*************************added for nursing maternity list (nursing home) by zak *********************
    
    public function nursing_home_list($latitude, $longitude, $user_id, $category_name)
   {
        $radius = '5';

       // $query = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'");
       
       $query = $this->db->query("SELECT `id`,`user_id`,`name_of_hospital`,`about_us`,`establishment_year`,`certificates_accred`,`category`,`speciality`,`surgery`,`services`,`address`,`pincode`,`phone`,`city`,`state`, `email`, `lat`, `lng`,`image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,`user_discount`, ( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM nursing_metarinity_list HAVING distance <= '$radius'");
        $count = $query->num_rows();
         if ($count > 0) {
            
            foreach ($query->result_array() as $row) {
                
            $id = $row['id'];
            $wellness_user_id = $row['user_id'];
            $wellness_email = $row['email'];
            $queryTocheck = $this->db->query("SELECT id,name,phone FROM users WHERE id='$wellness_user_id' and email='$wellness_email'");
            //echo "SELECT id,name,phone FROM users WHERE id='$hospital_user_id' and email='$hospital_email'";
            /*die();*/
            $query_count = $queryTocheck->num_rows();

            if($query_count>0)
              {
                $name_of_wellness = $row['name_of_hospital'];
                $mobile = $row['phone'];
                $about_us = $row['about_us'];
                $establishment_year = $row['establishment_year'];
                $certificates_accred = $row['certificates_accred'];
                $category = $row['category'];
                $speciality = $row['speciality'];
                $surgery = $row['surgery'];
                $services = $row['services'];
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
                
                $profile_views = '0';

                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $image;
                } else {
                    $image = '';
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $wellness_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $wellness_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $wellness_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                //temp commented for wellness
                // if($certificates_accred != ''){
                //     $certificates_accred_query = $this->db->query("SELECT `name`, image FROM `hospital_certificates_accred` WHERE FIND_IN_SET(name,'" . $certificates_accred . "')");
                //     foreach ($certificates_accred_query->result_array() as $get_clist) {
                //         $certificates_name = $get_clist['name'];
                //         $certificates_image = $get_clist['image'];
    
                //         if ($certificates_image != '') {
                //             $certificates_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $certificates_image;
                //         } else {
                //             $certificates_image = '';
                //         }
    
    
                //         $certificates_accred_list[] = array(
                //             "certificates_name" => $certificates_name,
                //             "certificates_image" => $certificates_image
                //         );
                //     }
                // }
                $wellness_surgery_list = array();
                // if($surgery != ''){
                //     $wellness_surgery_query = $this->db->query("SELECT `id`,`surgery_name`,`surgery_rate`,`surgery_package`, image FROM `wellness_surgery` WHERE FIND_IN_SET(surgery_name,'" . $surgery . "')");
                //     foreach ($wellness_surgery_query->result_array() as $get_slist) {
                //         $surgery_id = $get_slist['id'];
                //         $surgery_name = $get_slist['surgery_name'];
                //         $surgery_rate = $get_slist['surgery_rate'];
                //         $surgery_package = $get_slist['surgery_package'];
                //         $surgery_image = $get_slist['image'];
    
                //         if ($surgery_image != '') {
                //             $surgery_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $surgery_image;
                //         } else {
                //             $surgery_image = '';
                //         }
    
    
                //         $wellness_surgery_list[] = array(
                //             "surgery_id" => $surgery_id,
                //             "surgery_name" => $surgery_name,
                //             "surgery_rate" => $surgery_rate,
                //             "surgery_package" => $surgery_package,
                //             "surgery_image" => $surgery_image
                //         );
                //     }
                // }


                $gallery = array();
                $gallery_query = $this->db->query("SELECT `title`, CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/',source) AS source FROM `nursing_metarinity_image` WHERE id='$id'");
                foreach ($gallery_query->result_array() as $get_glist) {
                    $title = $get_glist['title'];
                    $media_image = $get_glist['source'];
                    $gallery[] = array(
                        "title" => $title,
                        "image" => $media_image
                    );
                }

                $wellness_service_list = array();
                if($services != ''){
                    $wellness_services_query = $this->db->query("SELECT `service_name` FROM `nursing_metarinity_services` WHERE FIND_IN_SET(service_name,'" . $services . "')");
                    foreach ($wellness_services_query->result_array() as $get_serlist) {
                        $service_name = $get_serlist['service_name'];
                        $wellness_service_list[] = array(
                            "service_name" => $service_name
                        );
                    }
                }

                $wellness_speciality_list = array();
                if($speciality != ''){
                    $wellness_speciality_query = $this->db->query("SELECT `id`,`doctors_category`,`name`, image FROM `nursing_metarinity_specialist` WHERE FIND_IN_SET(name,'" . $speciality . "')");
                    foreach ($wellness_speciality_query->result_array() as $get_splist) {
                        $specialist_id = $get_splist['id'];
                        $specialist_name = $get_splist['name'];
                        $doctors_category = $get_splist['doctors_category'];
                        $specialist_image = $get_splist['image'];
    
                        if ($specialist_image != '') {
                            $specialist_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $specialist_image;
                        } else {
                            $specialist_image = '';
                        }
    
                        $wellness_speciality_list[] = array(
                            "specialist_id" => $specialist_id,
                            "specialist_name" => $specialist_name,
                            "doctors_category" => $doctors_category,
                            "specialist_image" => $specialist_image
                        );
                    }
                }
                
                $branch_list  = array();
                $branch_query = $this->db->query("SELECT * FROM `nursing_metarinity_branch` WHERE hospital_id='$wellness_user_id' order by id asc");
              //  echo"SELECT * FROM `wellness_branch` WHERE hospital_id='$wellness_user_id' order by id asc";
                //die();
                $branch_count = $branch_query->num_rows();
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $branch_row) {
                        $branch_id                              = $branch_row['id'];
                        $branch_listing_id                      = $branch_row['hospital_id'];
                        $branch_hospital_branch_name            = $branch_row['name_of_branch'];
                        $branch_about_us                        = $branch_row['about_us'];
                        $branch_establishment_year              = $branch_row['establishment_year'];
                        $certificates_accred_list               = $branch_row['certificates_accred'];
                        $branch_category                        = $branch_row['category'];
                        $hospitals_speciality_list              = $branch_row['speciality'];
                        $hospitals_surgery_list                 = $branch_row['surgery'];
                        $hospitals_service_list                 = $branch_row['services'];
                        $branch_address                         = $branch_row['address'];
                        $branch_pincode                         = $branch_row['pincode'];
                        $branch_phone                           = $branch_row['phone'];
                        $branch_city                            = $branch_row['city'];
                        $branch_state                           = $branch_row['state'];
                        $branch_email                           = $branch_row['email'];
                        $branch_lat                             = $branch_row['lat'];
                        $branch_lng                             = $branch_row['lng'];
                        $branch_map_location                    = $branch_row['map_location'];
                        $branch_image                           = $branch_row['image'];
                        $branch_rating                          = $branch_row['rating'];
                        $branch_review                          = $branch_row['review'];
                        $branch_date                            = $branch_row['date'];
                        $branch_is_active                        = $branch_row['is_active'];
                        $is_approval                             = $branch_row['is_approval'];
                        $approval_date                        = $branch_row['approval_date'];
                        $is_active_date                        = $branch_row['is_active_date'];
                        


                        $branch_list[] = array(
                            'hospital_id' => $branch_id,
                            'hospital_id' => $branch_listing_id,
                            'name_of_branch' => $branch_listing_id,
                            'name_of_hospital' =>$branch_hospital_branch_name,
                            'about_us' => $branch_about_us,
                            'establishment_year' => $branch_establishment_year,
                            'certificates_accred_list'=>$certificates_accred_list,
                            'category' => $branch_category,
                            'speciality' => $hospitals_speciality_list,
                            'surgery' => $hospitals_surgery_list,
                            'services' => $hospitals_service_list,
                            'address' => $branch_address,
                            'pincode' => $branch_pincode,
                            'phone' => $branch_phone,
                            'lat' => $branch_lat,
                            'lng' => $branch_lng,
                            'map_location' => $branch_map_location,
                            'city' => $city,
                            'state' => $state,
                            'email' => $branch_email,
                            'image' => $branch_image,
                            'review' => $reviews,
                            'rating' => $branch_review,
                            'date' => $branch_date,
                            'is_active' => $branch_is_active,
                            'is_approval' => $is_approval,
                            'approval_date' => $approval_date,
                            'is_active_date' => $is_active_date
                        );
                    }
                }
                

                $resultpost[] = array(
                    'hospital_id' => $id,
                    'hospital_user_id' => $wellness_user_id,
                    'name_of_hospital' => $name_of_wellness,
                    'listing_type' => "8",
                    'about_us' => $about_us,
                    'establishment_year' => $establishment_year,
                  //  'certificates_accred_list' => "$certificates_accred_list",
                  'certificates_accred_list' => "",
                    'hospitals_surgery_list' => $wellness_surgery_list,
                    'gallery' => $gallery,
                    'hospitals_service_list' => $wellness_service_list,
                    'hospitals_speciality_list' => $wellness_speciality_list,
                    'address' => $address,
                    'mobile' => $mobile,
                    'lat' => $lat,
                    'lng' => $lng,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'rating' => $rating,
                    'review' => $reviews,
                    'image' => $image,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'is_follow' => $is_follow,
                    'user_discount' => $user_discount,
                    'branch_list' => $branch_list
                );
            }
            else{
                $resultpost = array();
            }
            
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
   }
   
   
    public function nursing_home_details($user_id, $listing_id) {
        $radius = '5';
        $query = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year` FROM nursing_metarinity_list WHERE user_id='$listing_id' limit 1");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name_of_hospital = $row['name_of_hospital'];
                $mobile = $row['phone'];
                $about_us = $row['about_us'];
                $establishment_year = $row['establishment_year'];
                $certificates_accred = $row['certificates_accred'];
                $category = $row['category'];
                $speciality = $row['speciality'];
                $surgery = $row['surgery'];
                $services = $row['services'];
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
                $hospital_user_id = $row['user_id'];
                $profile_views = '2458';

                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $image;
                } else {
                    $image = '';
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $hospital_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $hospital_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $hospital_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $certificates_accred_list = array();
                // $certificates_accred_query = $this->db->query("SELECT `name`, image FROM `hospital_certificates_accred` WHERE FIND_IN_SET(name,'" . $certificates_accred . "')");
                // foreach ($certificates_accred_query->result_array() as $get_clist) {
                //     $certificates_name = $get_clist['name'];
                //     $certificates_image = $get_clist['image'];

                //     if ($certificates_image != '') {
                //         $certificates_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $certificates_image;
                //     } else {
                //         $certificates_image = '';
                //     }


                //     $certificates_accred_list[] = array(
                //         "certificates_name" => $certificates_name,
                //         "certificates_image" => $certificates_image
                //     );
                // }

                $wellness_surgery_list = array();
                // $wellness_surgery_query = $this->db->query("SELECT `id`,`surgery_name`,`surgery_rate`,`surgery_package`, image FROM `wellness_surgery` WHERE FIND_IN_SET(surgery_name,'" . $surgery . "')");
                // foreach ($wellness_surgery_query->result_array() as $get_slist) {
                //     $surgery_id = $get_slist['id'];
                //     $surgery_name = $get_slist['surgery_name'];
                //     $surgery_rate = $get_slist['surgery_rate'];
                //     $surgery_package = $get_slist['surgery_package'];
                //     $surgery_image = $get_slist['image'];

                //     if ($surgery_image != '') {
                //         $surgery_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $surgery_image;
                //     } else {
                //         $surgery_image = '';
                //     }

                //     $wellness_surgery_list[] = array(
                //         "surgery_id" => $surgery_id,
                //         "surgery_name" => $surgery_name,
                //         "surgery_rate" => $surgery_rate,
                //         "surgery_package" => $surgery_package,
                //         "surgery_image" => $surgery_image
                //     );
                // }

                $gallery = array();
                $gallery_query = $this->db->query("SELECT `title`, CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/',source) AS source FROM `nursing_metarinity_media` WHERE hospital_id='$id'");
                foreach ($gallery_query->result_array() as $get_glist) {
                    $title = $get_glist['title'];
                    $image = $get_glist['source'];
                    $gallery[] = array(
                        "title" => $title,
                        "image" => $image
                    );
                }

                $wellness_service_list = array();
                $wellness_services_query = $this->db->query("SELECT `service_name` FROM `nursing_metarinity_services` WHERE FIND_IN_SET(service_name,'" . $services . "')");
                foreach ($wellness_services_query->result_array() as $get_serlist) {
                    $service_name = $get_serlist['service_name'];
                    $hospitals_service_list[] = array(
                        "service_name" => $service_name
                    );
                }

                $wellness_speciality_list = array();
                $wellness_speciality_query = $this->db->query("SELECT `id`,`doctors_category`,`name`, image FROM `nursing_metarinity_specialist` WHERE FIND_IN_SET(name,'" . $speciality . "')");
                foreach ($wellness_speciality_query->result_array() as $get_splist) {
                    $specialist_id = $get_splist['id'];
                    $specialist_name = $get_splist['name'];
                    $doctors_category = $get_splist['doctors_category'];
                    $specialist_image = $get_splist['image'];

                    if ($specialist_image != '') {
                        $specialist_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $specialist_image;
                    } else {
                        $specialist_image = '';
                    }

                    $wellness_speciality_list[] = array(
                        "specialist_id" => $specialist_id,
                        "specialist_name" => $specialist_name,
                        "doctors_category" => $doctors_category,
                        "specialist_image" => $specialist_image
                    );
                }


                $resultpost[] = array(
                    'hospital_id' => $id,
                    'hospital_user_id' => $hospital_user_id,
                    'name_of_hospital' => $name_of_hospital,
                    'about_us' => $about_us,
                    'establishment_year' => $establishment_year,
                   // 'certificates_accred_list' => $certificates_accred_list,
                    //'hospitals_surgery_list' => $wellness_surgery_list,
                    'gallery' => $gallery,
                    'hospitals_service_list' => $wellness_service_list,
                    'hospitals_speciality_list' => $wellness_speciality_list,
                    'address' => $address,
                    'mobile' => $mobile,
                    'lat' => $lat,
                    'lng' => $lng,
                    'pincode' => $pincode,
                    'city' => $city,
                    'state' => $state,
                    'rating' => $rating,
                    'review' => $reviews,
                    'image' => $image,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'is_follow' => $is_follow
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }
    
    
    
     //*********************************added by Dhaval for Hospital Timing Slot **********************
     public function user_read_slot($hospital_id, $doctor_id, $consultation_type)
    {
        $todayDay  = date('l');
        $todayDate = date('Y-m-d H:i:s');
        $time_slots=array();
        $data=array();
        $k=0;
      
        
        
        
      
            $day_time_slots       = $this->db->query("SELECT * FROM `hospital_doctor_list` WHERE `hospital_id` = '$hospital_id' AND `id` = '$doctor_id'");
           
            $opening_hours=array();
            $count_day_time_slots = $day_time_slots->num_rows();
            $row=$day_time_slots->row();
            $all_Day_Data = $row->timing;
             $day_array_list = explode('|', $all_Day_Data);
           
               for ($i = 0; $i < count($day_array_list); $i++) 
                  {
                        $day_list = explode('>', $day_array_list[$i]);
                    for ($j = 1; $j < count($day_list); $j++)  
                           {
                                 $data[$k] = $day_list[$j];
                              $k++; 
                           }
                             
                            }
             
            
               for ($i = 0; $i < 7; $i++) {
            $time_array           = array();
            $time_array1          = array();
            $time_array2          = array();
            $time_array3          = array();
            $time_slott           = array();
            $todayDate            = date('Y-m-d', strtotime($todayDate . ' +1 day'));
            $todayDay             = date('l', strtotime($todayDate));
            
           if($todayDay=="Monday")
             {
                            $monday = $data[0];
                            $monday = explode('-', $monday);
                            $monday_To =  $monday[1];
                            $monday_From =  $monday[0];
                            $monday_To= explode(',', $monday_To);
                            $monday_From = explode(',', $monday_From);
                            
                            
            
             
                            
                            
                            for($mon=0;$mon<count($monday_To);$mon++)
                            {
                                     if($monday_To[$mon]=="close")
                                   {
                                     
                                   }
                                   else
                                   {
                                 
                                     
                                 
                                   $mont=date("H:i:s", strtotime($monday_To[$mon]));
                                   $monf=date("H:i:s", strtotime($monday_From[$mon]));
                                   $mond=substr($mont,0,2);
                                   
                                   
                                   $day_time_status       = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `listing_id` = '$hospital_id' AND  `doctor_id`='$doctor_id' AND `booking_date` = '$todayDate' And `from_time` = '$monf' And `to_time` = '$mont' AND `status` != '3' ");
                                   $count_day_time_status = $day_time_status->num_rows();
                                     if($count_day_time_status)
                                     {
                                        $statusm=1; 
                                     }
                                     else
                                     {
                                       $statusm=0;   
                                     }
                                  if($mond <= 13)
                                  {
                                       $time_array[] = array(
                                                             'from_time' => $monf,
                                                             'to_time' => $mont,
                                                             'status' => $statusm
                                                            );
                                  }
                                   if($mond > 13 && $mond <= 16)
                                  {
                                       $time_array1[] = array(
                                                             'from_time' => $monf,
                                                             'to_time' => $mont,
                                                             'status' => $statusm
                                                             );
                                  }     
                                   if($mond > 16 && $mond <= 20)
                                  {
                                       $time_array2[] = array(
                                                             'from_time' => $monf,
                                                             'to_time' => $mont,
                                                             'status' => $statusm
                                                            );
                                  }  
                                   if($mond > 20 && $mond <= 24)
                                  {
                                       $time_array3[] = array(
                                                             'from_time' => $monf,
                                                             'to_time' => $mont,
                                                             'status' => $statusm
                                                            );
                                  } 
                                   }
            
                            }
             } 



             if($todayDay=="Tuesday")
             {
                            $tuesday = $data[1];
                            $tuesday = explode('-', $tuesday);
                            $tuesday_To =  $tuesday[1];
                            $tuesday_From =  $tuesday[0];
                            $tuesday_To= explode(',', $tuesday_To);
                            $tuesday_From = explode(',', $tuesday_From);
                            for($tue=0;$tue<count($tuesday_To);$tue++)
                            {
                                  if($tuesday_To[$tue]=="close")
                                   {
                                     
                                   }
                                   else
                                   {
                                
                                   $tuet=date("H:i:s", strtotime($tuesday_To[$tue]));
                                   $tuef=date("H:i:s", strtotime($tuesday_From[$tue]));
                                   $day_time_status       = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `listing_id` = '$hospital_id' AND  `doctor_id`='$doctor_id' AND `booking_date` = '$todayDate' And `from_time` = '$tuef' And `to_time` = '$tuet' AND `status` != '3' ");
                                   $count_day_time_status = $day_time_status->num_rows();
                                     if($count_day_time_status)
                                     {
                                        $statust=1; 
                                     }
                                     else
                                     {
                                       $statust=0;   
                                     }
                                  $tued=substr($tuet,0,2);
                                  if($tued <= 13)
                                  {
                                       $time_array[] = array(
                                                             'from_time' => $tuef,
                                                             'to_time' => $tuet,
                                                             'status' => $statust
                                                            );
                                  }
                                   if($tued > 13 && $tued <= 16)
                                  {
                                       $time_array1[] = array(
                                                             'from_time' => $tuef,
                                                             'to_time' => $tuet,
                                                             'status' => $statust
                                                            );
                                  }     
                                   if($tued > 16 && $tued <= 20)
                                  {
                                       $time_array2[] = array(
                                                             'from_time' => $tuef,
                                                             'to_time' => $tuet,
                                                             'status' => $statust
                                                            );
                                  }  
                                   if($tued > 20 && $tued <= 24)
                                  {
                                       $time_array3[] = array(
                                                             'from_time' => $tuef,
                                                             'to_time' => $tuet,
                                                             'status' => $statust
                                                            );
                                  } 
                                   }
            
                            }
             } 
             
             
             if($todayDay=="Wednesday")
             {
                            $wednesday = $data[2];
                            $wednesday = explode('-', $wednesday);
                            $wednesday_To =  $wednesday[1];
                            $wednesday_From =  $wednesday[0];
                            $wednesday_To= explode(',', $wednesday_To);
                            $wednesday_From = explode(',', $wednesday_From);
                            for($wed=0;$wed<count($wednesday_To);$wed++)
                            {
                                  if($wednesday_To[$wed]=="close")
                                   {
                                     
                                   }
                                   else
                                   {
                                   $wedt=date("H:i:s", strtotime($wednesday_To[$wed]));
                                   $wedf=date("H:i:s", strtotime($wednesday_From[$wed]));
                                   $wedd=substr($wedt,0,2);
                                   $day_time_status       = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `listing_id` = '$hospital_id' AND  `doctor_id`='$doctor_id' AND `booking_date` = '$todayDate' And `from_time` = '$wedf' And `to_time` = '$wedt' AND `status` != '3' ");
                                   $count_day_time_status = $day_time_status->num_rows();
                                     if($count_day_time_status)
                                     {
                                        $statusw=1; 
                                     }
                                     else
                                     {
                                       $statusw=0;   
                                     }
                                  if($wedd <= 13)
                                  {
                                       $time_array[] = array(
                                                             'from_time' => $wedf,
                                                             'to_time' => $wedt,
                                                             'status' => $statusw
                                                            );
                                  }
                                   if($wedd > 13 && $wedd <= 16)
                                  {
                                       $time_array1[] = array(
                                                             'from_time' => $wedf,
                                                             'to_time' => $wedt,
                                                             'status' => $statusw
                                                            );
                                  }     
                                   if($wedd > 16 && $wedd <= 20)
                                  {
                                       $time_array2[] = array(
                                                             'from_time' => $wedf,
                                                             'to_time' => $wedt,
                                                             'status' => $statusw
                                                            );
                                  }  
                                   if($wedd > 20 && $wedd <= 24)
                                  {
                                       $time_array3[] = array(
                                                             'from_time' => $wedf,
                                                             'to_time' => $wedt,
                                                             'status' => $statusw
                                                            );
                                  }  
                                   }
            
                            }
             }
             if($todayDay=="Thursday")
             {
                            $thursday = $data[3];
                            $thursday = explode('-', $thursday);
                            $thursday_To =  $thursday[1];
                            $thursday_From =  $thursday[0];
                            $thursday_To= explode(',', $thursday_To);
                            $thursday_From = explode(',', $thursday_From);
                            for($thu=0;$thu<count($thursday_To);$thu++)
                            {
                                   
                                   if($thursday_To[$thu]=="close")
                                   {
                                     
                                   }
                                   else
                                   {
                                    $thut=date("H:i:s", strtotime($thursday_To[$thu]));
                                    $thuf=date("H:i:s", strtotime($thursday_From[$thu]));
                                   $thud=substr($thut,0,2);
                                   $day_time_status       = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `listing_id` = '$hospital_id' AND  `doctor_id`='$doctor_id' AND `booking_date` = '$todayDate' And `from_time` = '$thuf' And `to_time` = '$thut' AND `status` != '3' ");
                                   $count_day_time_status = $day_time_status->num_rows();
                                     if($count_day_time_status)
                                     {
                                        $statustu=1; 
                                     }
                                     else
                                     {
                                       $statustu=0;   
                                     }
                                  if($thud <= 13)
                                  {
                                       $time_array[] = array(
                                                             'from_time' => $thuf,
                                                             'to_time' => $thut,
                                                             'status' => $statustu
                                                            );
                                  }
                                   if($thud > 13 && $thud <= 16)
                                  {
                                       $time_array1[] = array(
                                                             'from_time' => $thuf,
                                                             'to_time' => $thut,
                                                             'status' => $statustu
                                                            );
                                  }     
                                   if($thud > 16 && $thud <= 20)
                                  {
                                       $time_array2[] = array(
                                                             'from_time' => $thuf,
                                                             'to_time' => $thut,
                                                             'status' => $statustu
                                                            );
                                  }  
                                   if($thud > 20 && $thud <= 24)
                                  {
                                       $time_array3[] = array(
                                                             'from_time' => $thuf,
                                                             'to_time' => $thut,
                                                             'status' => $statustu
                                                            );
                                  } 
                                   }
            
                            }
             } 
             if($todayDay=="Friday")
             {
                            $friday= $data[4];
                            $friday = explode('-', $friday);
                            $friday_To =  $friday[1];
                            $friday_From =  $friday[0];
                            $friday_To= explode(',', $friday_To);
                            $friday_From = explode(',', $friday_From);
                            for($fri=0;$fri<count($friday_To);$fri++)
                            {
                                 if($friday_To[$fri]=="close")
                                   {
                                     
                                   }
                                   else
                                   {
                                   $frit=date("H:i:s", strtotime($friday_To[$fri]));
                                   $frif=date("H:i:s", strtotime($friday_From[$fri]));
                                   $frid=substr($frit,0,2);
                                   $day_time_status       = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `listing_id` = '$hospital_id' AND  `doctor_id`='$doctor_id' AND `booking_date` = '$todayDate' And `from_time` = '$frif' And `to_time` = '$frit' AND `status` != '3' ");
                                   $count_day_time_status = $day_time_status->num_rows();
                                     if($count_day_time_status)
                                     {
                                        $statusf=1; 
                                     }
                                     else
                                     {
                                       $statusf=0;   
                                     }
                                      if($frid <= 13)
                                      {
                                           $time_array[] = array(
                                                                 'from_time' => $frif,
                                                                 'to_time' => $frit,
                                                                 'status' => $statusf
                                                                );
                                      }
                                       if($frid > 13 && $frid <= 16)
                                      {
                                           $time_array1[] = array(
                                                                 'from_time' => $frif,
                                                                 'to_time' => $frit,
                                                                 'status' => $statusf
                                                                );
                                      }     
                                       if($frid > 16 && $frid <= 20)
                                      {
                                           $time_array2[] = array(
                                                                 'from_time' => $frif,
                                                                 'to_time' => $frit,
                                                                 'status' => $statusf
                                                                );
                                      }  
                                       if($frid > 20 && $frid <= 24)
                                      {
                                           $time_array3[] = array(
                                                                 'from_time' => $frif,
                                                                 'to_time' => $frit,
                                                                 'status' => $statusf
                                                                );
                                      } 
                                  
                                }  
            
                            }
             } 
             if($todayDay=="Saturday")
             {
                            $saturday = $data[5];
                            $saturday = explode('-', $saturday);
                            $saturday_To =  $saturday[1];
                            $saturday_From =  $saturday[0];
                            $saturday_To= explode(',', $saturday_To);
                            $saturday_From = explode(',', $saturday_From);
                            for($sat=0;$sat<count($saturday_To);$sat++)
                            {
                                
                                 if($saturday_To[$sat]=="close")
                                   {
                                     
                                   }
                                   else
                                   {
                                  $satt=date("H:i:s", strtotime($saturday_To[$sat]));
                                  $satf=date("H:i:s", strtotime($saturday_From[$sat]));
                                  $satd=substr($satt,0,2);
                                   $day_time_status       = $this->db->query("SELECT `id`,`doctor_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `hospital_booking_master` WHERE `listing_id` = '$hospital_id' AND  `doctor_id`='$doctor_id' AND `booking_date` = '$todayDate' And `from_time` = '$satf' And `to_time` = '$satt' AND `status` != '3' ");
                                   $count_day_time_status = $day_time_status->num_rows();
                                     if($count_day_time_status)
                                     {
                                        $statuss=1; 
                                     }
                                     else
                                     {
                                       $statuss=0;   
                                     }
                                  if($satd <= 13)
                                  {
                                       $time_array[] = array(
                                                             'from_time' => $satf,
                                                             'to_time' => $satt,
                                                             'status' => $statuss
                                                            );
                                  }
                                   if($satd > 13 && $satd <= 16)
                                  {
                                       $time_array1[] = array(
                                                             'from_time' => $satf,
                                                             'to_time' => $satt,
                                                             'status' => $statuss
                                                            );
                                  }     
                                   if($satd > 16 && $satd <= 20)
                                  {
                                       $time_array2[] = array(
                                                             'from_time' => $satf,
                                                             'to_time' => $satt,
                                                             'status' => $statuss
                                                            );
                                  }  
                                   if($satd > 20 && $satd <= 24)
                                  {
                                       $time_array3[] = array(
                                                             'from_time' => $satf,
                                                             'to_time' => $satt,
                                                             'status' => $statuss
                                                            );
                                  } 
                                  
                            } 
            
                            }
             }

           
             
              $time_slott[] = array(
                'Morning' => $time_array,
                'time_slot' => 'Morning'
            );
            $time_slott[] = array(
                'Afternoon' => $time_array1,
                'time_slot' => 'Afternoon'
            );
            $time_slott[] = array(
                'Evening' => $time_array2,
                'time_slot' => 'Evening'
            );
            $time_slott[] = array(
                'Night' => $time_array3,
                'time_slot' => 'Night'
            );
             
             
             $time_slots[] = array(
                'day' => $todayDay,
                'date' => $todayDate,
                'timings' =>  $time_slott
            );
              $k++;
                          
           } 
                
          
        
        return $time_slots;
    }
    
    public function add_bookings($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $from_time, $to_time, $user_email, $user_gender, $is_user, $is_patient_added, $patient_id, $health_condition, $allergies, $heradiatry_problem, $description, $relationship, $date_of_birth, $connect_type)
    {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id           = date("YmdHis");
        $date                 = date('Y-m-d H:i:s');
        $status               = '1'; //awaiting for confirmation  // pending
        $from_time = date("H:i:s", strtotime($from_time));
        $to_time =  date("H:i:s", strtotime($to_time));  
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'doctor_id' => $clinic_id,
            'booking_date' => $booking_date,
            'booking_time' => $booking_time,
            'from_time' => $from_time,
            'to_time' => $to_time,
            'status' => $status,
            'description' => $description,
            'created_date' => $date,
            'consultation_type' => $connect_type
        );
        
        
        $insert               = $this->db->insert('hospital_booking_master', $booking_master_array);
        $appointment_id       = $this->db->insert_id();
        
         if($insert){
            
                $data= $this->HospitalModel->hospitals_notification_confirm($booking_id);
                 $message = 'Dear '.$data->name.', your booking has been confirm With  '.$data->name_of_hospital.'  Your '.'Booking ID is '.$data->booking_id.' '.'Thank you.';
                 $to = $data->email;
                 $subject = 'medicalwale Appointment List';
                 $msg = $message;
                 $this->Send_Email_Notification($to,$subject,$msg);
       
      
        }
       
        
        if ($appointment_id > 0) {
            $booking_master_array_patient = array('patient_id' => $appointment_id);
               $this->db->where('booking_id', $booking_id)->update('hospital_booking_master', $booking_master_array_patient);
            $patient_master_array = array(
                'user_id' => $user_id,
                'doctor_id' =>$clinic_id, 
                'booking_id' => $appointment_id,
                'patient_name' => $user_name,   
                'contact_no' => $user_mobile,
                'email' => $user_email,
                'gender' => $user_gender,
                'health_condition' => $health_condition,
                'allergies' => $allergies,
                'heradiatry_problem' => $heradiatry_problem,
                'created_date' => $date,
                'type' => $connect_type,
                'date_of_birth' => $date_of_birth,
                'listing_id' => $listing_id
            );
            $this->db->insert('hospitals_patient', $patient_master_array);
            $doctor_patient_id = $this->db->insert_id();
            if ($doctor_patient_id < 0) {
                return array(
                    'status' => 208,
                    'message' => 'Database Insert Issue'
                );
            }
        } else {
            return array(
                'status' => 208,
                'message' => 'Database Insert Issue'
            );
        }
        
        
        
        if ($is_user != "1") {
            if ($is_patient_added != "1") {
                //insert query  
                $health_record = array(
                    'user_id' => $user_id,
                    'patient_name' => $user_name,
                    'relationship' => $relationship,
                    'date_of_birth' => $date_of_birth,
                    'gender' => $user_gender,
                    'created_at' => $date,
                    'health_condition' => $health_condition,
                    'allergies' => $allergies,
                    'heradiatry_problem' => $heradiatry_problem,
                );
                $this->db->insert('health_record', $health_record);
                $patient_id = $this->db->insert_id();
                

                //patient_id
                //added for healthrecord and patient detail record link
              //  $health_record = array('id' => $appointment_id);
              //  $this->db->where('id', $patient_id)->update('health_record', $health_record);
                
                 $booking_master_array_patient = array('patient_id' => $patient_id);
               $this->db->where('booking_id', $booking_id)->update('hospital_booking_master', $booking_master_array_patient);
                //end
                 // $doctor_booking_master = array('patient_id' => $patient_id);
              //  $updatePatientid  = $this->db->where('id', $appointment_id)->update('doctor_booking_master', $doctor_booking_master);
            } else {
                // update query
                $count_user = $this->db->select('id')->from('health_record')->where('id', $patient_id)->get()->num_rows();
                if ($count_user > 0) {
                    $health_record = array(
                        'user_id' => $user_id,
                        'patient_name' => $user_name,
                        'relationship' => $relationship,
                        'date_of_birth' => $date_of_birth,
                        'gender' => $user_gender,
                        'created_at' => $date,
                        'health_condition' => $health_condition,
                        'allergies' => $allergies,
                        'heradiatry_problem' => $heradiatry_problem,
                    );
                    $updateStatus  = $this->db->where('id', $patient_id)->update('health_record', $health_record);
              //added for healthrecord and patient detail record link
                $health_record = array('id' => $appointment_id);
                $this->db->where('id', $patient_id)->update('health_record', $health_record);
                
              //   $booking_master_array_patient = array('patient_id' => $patient_id);
              // $this->db->where('booking_id', $booking_id)->update('doctor_booking_master', $booking_master_array_patient);
                //end
               // $doctor_booking_master = array('patient_id' => $patient_id);
              //  $updatePatientid  = $this->db->where('id', $appointment_id)->update('doctor_booking_master', $doctor_booking_master);
                  
                    if (!$updateStatus) {
                        return array(
                            'status' => 204,
                            'message' => 'Update failed'
                        );
                    }
                } else {
                    return array(
                        'status' => 208,
                        'message' => 'Relative data not found'
                    );
                }
            }
            
        } else {
          
             $health_record = array(
                    'user_id' => $user_id,
                    'patient_name' => $user_name,
                    'relationship' => $relationship,
                    'date_of_birth' => $date_of_birth,
                    'gender' => $user_gender,
                    'created_at' => $date,
                    'health_condition' => $health_condition,
                    'allergies' => $allergies,
                    'heradiatry_problem' => $heradiatry_problem,
                );
                $this->db->insert('health_record', $health_record);
                $patient_id = $this->db->insert_id();
              
               $booking_master_array_patient = array('patient_id' => $patient_id);
               $this->db->where('booking_id', $booking_id)->update('hospital_booking_master', $booking_master_array_patient);
            
            $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
            if ($count_user > 0) {
                $user_data    = array(
                    'health_condition' => $health_condition,
                    'allergies' => $allergies,
                    'updated_at' => $date,
                    'heradiatry_problem' => $heradiatry_problem
                );
                $updateStatus = $this->db->where('id', $user_id)->update('users', $user_data);
                if (!$updateStatus) {
                    return array(
                        'status' => 204,
                        'message' => 'Update failed'
                    );
                }
            } else {
                return array(
                    'status' => 208,
                    'message' => 'User not found'
                );
            }
        }
        
        
        
        
        
        $query_dr           = $this->db->query("SELECT hospitals.name_of_hospital,hospital_doctor_list.phone,hospital_doctor_list.doctor_name FROM  hospital_doctor_list LEFT JOIN  hospitals ON hospital_doctor_list.hospital_id=hospitals.user_id  WHERE hospital_doctor_list.id='$clinic_id' AND hospital_doctor_list.hospital_id='$listing_id'");
        $data               = $query_dr->row_array();
        $doctor_name        = $data['doctor_name'];
        $clinic_name        = $data['name_of_hospital'];
        $doctor_phone       = $data['phone'];
        $final_booking_date = date('M-d', strtotime($booking_date));
        
        if ($insert) {
            $message      = 'Your appointment with ' . $doctor_name . ' at ' . $clinic_name . ' for ' . $final_booking_date . ', ' . $booking_time . ' is confirmed. Thanks Medicalwale.com';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_mobile,
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
            $type_of_order = 'booking';
            $login_url     = 'https://doctor.medicalwale.com';
            $message2      = 'You have received ' . $type_of_order . ', kindly login to ' . $login_url . '. Thank you.';
            $post_data2    = array(
                'From' => '02233721563',
                'To' => $doctor_phone,
                'Body' => $message2
            );
            $exotel_sid2   = "aegishealthsolutions";
            $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
            $url2          = "https://" . $exotel_sid2 . ":" . $exotel_token2 . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid2 . "/Sms/send";
            $ch2           = curl_init();
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
      $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
        $this->notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id, $connect_type, $user_name);
        return array(
            'booking_id' => $booking_id
        );
        
        
        
    }    
    
    
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
   
   
      public function notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $appointment_id, $connect_type, $user_name)
    {
        $customer_token       = $this->db->query("SELECT name,token,agent,token_status,web_token FROM users WHERE id='$listing_id'");
        $customer_token_count = $customer_token->num_rows();
        if ($customer_token_count > 0) {
            $token_status = $customer_token->row_array();
            $usr_name     = $token_status['name'];
            $agent        = $token_status['agent'];
            $reg_id       = $token_status['token'];
            $web_token = $token_status['web_token'];
            $img_url      = 'https://medicalwale.com/img/medical_logo.png';
            $tag          = 'text';
            $key_count    = '1';
            $title        = $user_name . ' has booked an appointment';
            $msg          = 'Date : ' . $booking_date . '  Time : ' . $booking_time;
            $type = 'hospitals_doctor_booking';
            $this->send_gcm_notify_booking($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_time, $appointment_id, $connect_type);
            
             $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => $booking_date,
                      'booking_time' => $booking_time,
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $user_id,
                      'booking_id'  => $appointment_id,
                       'invoice_no' => "",
                       'user_id'  => $listing_id,
                       'notification_type'  => 'hospitals_doctor_booking',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
                );
                $this->db->insert('All_notification_Mobile', $notification_array);
                
            // Web notification FCM function by ghanshyam parihar Added date:07 Feb 2019
            $click_action = 'https://hospitals.medicalwale.com/Appointments/booking_appointment_opd/'.$listing_id;
            // $click_action = 'https://vendor.sandbox.medicalwale.com/hospitals/Appointments/booking_appointment_opd/'.$listing_id;
            $this->send_gcm_web_notify($title, $msg, $web_token, $img_url, $tag, $agent, $type,$click_action);
            // Web notification FCM function by ghanshyam parihar Added date:07 Feb 2019 Ends
        }
    }
    
     public function send_gcm_notify_booking($title, $reg_id, $msg, $img_url, $tag, $agent, $date, $slot, $appointment_id, $consultation_type)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        if (!defined("GOOGLE_GCM_URL")) {
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
                    "notification_type" => 'appointment_notifications',
                    "notification_date" => $date,
                    "appointment_date" => $date,
                    "appointment_time" => $slot,
                    "appointment_id" => $appointment_id,
                    "type_of_connect" => $consultation_type
                )
            );
            /*IOS registration is left */
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AAAAMCq6aPA:APA91bFjtuwLeq5RK6_DFrXntyJdXCFSPI0JdJcvoXXi0xIGm7qgzQJmUl7aEq3HjUTV3pvlFr5iv5pOWtCoN3JIpMSVhU8ZFzusaibehi5MPwThRmx1pCnm3Tm-x6wI8tGwhc0eUj2U' : 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E'
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
    }
    
     public function user_payment_approval($doctor_id, $status, $booking_id, $user_id)
    {
        //echo $doctor_id . ',' . $status . ',' . $booking_id . ',' . $user_id;
        //die();
        date_default_timezone_set('Asia/Kolkata');
        $date         = date('Y-m-d H:i:s');
        //$status = strtolower($status);
        $status       = $status; // 2 - if doctor confirm timing ,4 doctor cancelled timing ,6 reschedule
        //echo "SELECT user_id,booking_id,listing_id FROM doctor_booking_master WHERE booking_id='$booking_id'";
        $table_record = $this->db->query("SELECT user_id,booking_id,listing_id FROM hospital_booking_master WHERE booking_id='$booking_id'");
        $count_user   = $table_record->num_rows();
        if ($count_user > 0) {
            $booking_array = array(
                'status' => $status,
                'created_date' => $date
            );
            $updateStatus  = $this->db->where('booking_id', $booking_id)->update('hospital_booking_master', $booking_array);
            if (!$updateStatus) {
                return array(
                    'status' => 204,
                    'message' => 'Update failed'
                );
            }
            if ($status == '5') //user confirm, payment pending
                {
                $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->confirm_status($user_id, $booking_id, $doctor_id);
            }
            else if ($status == '8')
            {
                 $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->confirm_cash_on_delivery_status($user_id, $booking_id, $doctor_id);
            }
            else if ($status == '3') //cancel appointment 
                {
                $row       = $table_record->row();
                $user_id   = $row->user_id;
                $doctor_id = $row->listing_id;
                $this->cancel_status($user_id, $booking_id, $doctor_id);
            }
        } else {
            return array(
                'status' => 208,
                'message' => 'Booking data not found'
            );
        }
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    
    
    
    
     public function confirm_status($user_id, $booking_id, $listing_id)
    {
        $appointment_status = '5';
        //$table_record       = $this->db->query("SELECT doctor_name FROM doctor_list WHERE user_id='$listing_id' ");
        $table_record       = $this->db->query("SELECT name as patient_name FROM users WHERE id='$user_id'");
        $count_user         = $table_record->num_rows();
        
          $bookingRecords =  $this->db->query("SELECT `booking_date`,`from_time`,`to_time`,`consultation_type` FROM `hospital_booking_master` WHERE `booking_id`='$booking_id'");
        $count_record  = $bookingRecords->num_rows();
        if($count_record > 0){
            $bookingRecord = $bookingRecords->row();
            
            $booking_date = $bookingRecord->booking_date;
            $from_time = $bookingRecord->from_time;
            $to_time = $bookingRecord->to_time;
            $booking_time = $from_time.'-'.$to_time;
            $consultation_type = $bookingRecord->consultation_type;
        }
        
        if ($count_user > 0) {
            $row         = $table_record->row();
            $patient_name = $row->patient_name;
            $title       = $patient_name . ' has Confirmed an Payment';
            $msg         = $patient_name . '  has Confirmed an Payment.';
           // $this->notifyDoctorMethod($listing_id, $appointment_status, $booking_id, $patient_name, $title, $msg);
            $this->notifyHospitalMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name);
            
        }
    }
    
    
      public function cancel_status($user_id, $booking_id, $listing_id)
    {
        $appointment_status = '3';
        $table_record       = $this->db->query("SELECT name as patient_name FROM users WHERE id='$user_id'");
        $count_user         = $table_record->num_rows();
        
          $bookingRecords =  $this->db->query("SELECT `booking_date`,`from_time`,`to_time`,`consultation_type` FROM `hospital_booking_master` WHERE `booking_id`='$booking_id'");
        $count_record  = $bookingRecords->num_rows();
        if($count_record > 0){
            $bookingRecord = $bookingRecords->row();
            
            $booking_date = $bookingRecord->booking_date;
            $from_time = $bookingRecord->from_time;
            $to_time = $bookingRecord->to_time;
            $booking_time = $from_time.'-'.$to_time;
            $consultation_type = $bookingRecord->consultation_type;
        }
        
        
        if ($count_user > 0) {
            $row         = $table_record->row();
            $patient_name = $row->patient_name;
            $title       = $patient_name . ' has Cancel an Payment';
            $msg         = $patient_name . '  has Cancel an Payment.';
            //$this->notifyDoctorMethod($listing_id, $appointment_status, $booking_id, $patient_name, $title, $msg);
             $this->notifyHospitalMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name);
        }
    }
    
   public function confirm_cash_on_delivery_status($user_id, $booking_id, $listing_id)
    {
        $appointment_status = '8';
        $table_record   = $this->db->query("SELECT name as patient_name FROM users WHERE id='$user_id'");
        
        $bookingRecords =  $this->db->query("SELECT `booking_date`,`from_time`,`to_time`,`consultation_type` FROM `hospital_booking_master` WHERE `booking_id`='$booking_id'");
        $count_record  = $bookingRecords->num_rows();
        if($count_record > 0){
            $bookingRecord = $bookingRecords->row();
            
            $booking_date = $bookingRecord->booking_date;
            $from_time = $bookingRecord->from_time;
            $to_time = $bookingRecord->to_time;
            $booking_time = $from_time.'-'.$to_time;
            $consultation_type = $bookingRecord->consultation_type;
        }
        
        $count_user         = $table_record->num_rows();
        if ($count_user > 0) {
            $row         = $table_record->row();
            $patient_name = $row->patient_name;
            $title       = $patient_name . ' has Confirmed an Payment on Cash on Delivery.' ;
             $msg         = $patient_name . '  has Confirmed an Payment on Cash on Delivery.';
           
            
            //$this->notifyDoctorMethod($listing_id, $appointment_status, $booking_id, $patient_name, $title, $msg);
            $this->notifyHospitalMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name);
        }
    }
    
    
      public function notifyHospitalMethod($listing_id, $user_id,$title,$msg, $booking_date, $booking_time, $booking_id, $consultation_type, $patient_name)
    {

          $customer_token = $this->db->query("SELECT name,token,agent,token_status FROM users WHERE id='$listing_id'");
          $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                // $getusr = $user_plike->row_array();

                $usr_name = $token_status['name'];
                $agent = $token_status['agent'];
                $reg_id = $token_status['token'];
                $img_url = 'https://medicalwale.com/img/medical_logo.png';
                $tag = 'text';
                $key_count = '1';
                $title = $title;
                $msg = $msg;
                $this->send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent,$booking_date,$booking_time,$booking_id,$consultation_type);
                
                $notification_array = array(
                      'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                      'tag' => $tag,
                      'order_status' => "",
                      'order_date' => $booking_date,
                      'booking_time' => $booking_time,
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $user_id,
                      'booking_id'  => $booking_id,
                       'invoice_no' => "",
                       'user_id'  => $listing_id,
                       'notification_type'  => 'appointment_notifications',
                       'notification_date'  => date('Y-m-d H:i:s')
                       
                );
                $this->db->insert('All_notification_Mobile', $notification_array);
            }
    }


		   public function Send_Email_Notification($to,$subject,$msg)
   {
    
        $headers = 'From: info@medicalwale.com' . "\r\n" .
           'Reply-To: donotreply@medicalwale.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();
        
        mail($to, $subject, $msg, $headers);
   }

        //send notification through firebase
        /* notification to send in the doctor app for appointment confirmation*/
    public function send_gcm_notify_user($title, $reg_id, $msg, $img_url, $tag, $agent,$booking_date,$booking_time,$booking_id,$consultation_type) {
     
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
                "notification_type" => 'appointment_notifications',
                "notification_date" => $date,
                "appointment_id" => $booking_id,
                "appointment_date" => $booking_date,
                "appointment_time" =>$booking_time,
                "type_of_connect" => $consultation_type
             //   app date app time  app it 
            )
        );
       
        $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AAAAMCq6aPA:APA91bFjtuwLeq5RK6_DFrXntyJdXCFSPI0JdJcvoXXi0xIGm7qgzQJmUl7aEq3HjUTV3pvlFr5iv5pOWtCoN3JIpMSVhU8ZFzusaibehi5MPwThRmx1pCnm3Tm-x6wI8tGwhc0eUj2U' : 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E'
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
    
    
    
      public function booking_details($booking_id)
    {
        //echo "SELECT `doctor_booking_master`.*,doctor_list.*,doctor_clinic.lat,doctor_clinic.lng,doctor_clinic.consultation_charges,doctor_clinic.map_location,doctor_clinic.address,vendor_discount.* FROM `doctor_booking_master` LEFT JOIN doctor_clinic ON(doctor_booking_master.clinic_id = doctor_clinic.id) LEFT JOIN doctor_list ON(doctor_booking_master.listing_id = doctor_list.user_id) LEFT JOIN vendor_discount ON(doctor_booking_master.listing_id = vendor_discount.vendor_id AND vendor_discount.discount_category = 'doctor_visit') WHERE doctor_booking_master.`booking_id` = '$booking_id' AND  ";
        $booking_details           = $this->db->query("SELECT `hospital_booking_master`.*,hospital_doctor_list.*,hospitals.image,hospitals.map_location as maps,hospitals.rating,hospitals.lat,hospitals.lng,hospital_doctor_list.consultation,hospitals.map_location,hospitals.address,vendor_discount.* FROM `hospital_booking_master` LEFT JOIN hospital_doctor_list ON(hospital_booking_master.doctor_id = hospital_doctor_list.id) LEFT JOIN hospitals ON(hospital_booking_master.listing_id = hospitals.user_id) LEFT JOIN vendor_discount ON(hospital_booking_master.doctor_id = vendor_discount.vendor_id AND vendor_discount.discount_category = 'hospital_doctor_visit') WHERE hospital_booking_master.`booking_id` = '$booking_id'");
        $booking_details           = $booking_details->row_array();
        $doctor_id                 = $booking_details['listing_id'];
        $clinic_id                 = $booking_details['doctor_id'];
        $booking_id                = $booking_details['booking_id'];
        $booking_date              = $booking_details['booking_date'];
        $booking_time              = $booking_details['booking_time'];
        $created_date              = $booking_details['created_date'];
        $consultation_type         = $booking_details['consultation_type'];
        $status                    = $booking_details['status'];
        $doctor_name               = $booking_details['doctor_name'];
        $doctor_email              = $booking_details['email'];
        $doctor_experience         = $booking_details['practice_at'];
        $speciality                = $booking_details['speciality'];
        $doctor_dob                = "";
        $doctor_telephone          = $booking_details['phone'];
        $doctor_lat                = $booking_details['lat'];
        $doctor_lng                = $booking_details['lng'];
        $doctor_address            = $booking_details['maps'];
        $doctor_consultation_visit = $booking_details['consultation'];
        $discount_amount           = $booking_details['discount_min'];
        $discount_type             = $booking_details['discount_type'];
        $discount_limit            = $booking_details['discount_limit'];
        $discount_category         = $booking_details['discount_category'];
        $visit_charge              = "";
        if ($discount_category == "hospital_doctor_visit") {
            if ($discount_type == "percent") {
                $visit_discount_amount = $doctor_consultation_visit * ($discount_amount / 100);
                if ($visit_discount_amount > $discount_limit) {
                    $visit_discount_amount = $discount_limit;
                } else {
                    $visit_discount_amount = $visit_discount_amount;
                }
                $visit_charge = $doctor_consultation_visit - $visit_discount_amount;
            } else if ($discount_type == "rupees") {
                $visit_discount_amount = $doctor_consultation_visit - $discount_amount;
                if ($visit_discount_amount > $discount_limit) {
                    $visit_discount_amount = $discount_limit;
                } else {
                    $visit_discount_amount = $visit_discount_amount;
                }
                $visit_charge = $doctor_consultation_visit - $visit_discount_amount;
            }
            if ($visit_charge < 0) {
                $visit_charge = 0;
            }
        }
        
        //doctor consultaion
        if($consultation_type == 'visit')
        {
            
        }
        else
        {
        $clinic_id = 0;
        }
        $results = array();
        $available_call = "0";
        $available_video = "0";
        $available_chat = "0";
        $results_call = array();
        $results_video = array();
        $results_chat = array();
        $q = $this->db->query("SELECT * FROM `doctor_consultation` WHERE `doctor_user_id` = '$doctor_id' AND consultation_name<>'visit'");
        
        $qRows = $q->result_array();
        
        foreach($qRows as $qRow){
            if($qRow['consultation_name'] == 'chat' && $qRow['is_active'] == 1){
                $available_call = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_call['consultation_type'] = 'call';
                $results_call['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
            if($qRow['consultation_name'] == 'video' && $qRow['is_active'] == 1){
                $available_video = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_video['consultation_type'] = 'video';
                $results_video['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
             if($qRow['consultation_name'] == 'chat' && $qRow['is_active'] == 1){
                $available_chat = $qRow['is_active'];
                $duration = $qRow['duration'];
                $charges = $qRow['charges'];
                
                $results_chat['consultation_type'] = 'chat';
                $results_chat['info'] = array(
                        'duration' => $duration,
                        'charges' => $charges
                    );    
            }
            
        }
      
      if($results_call){$results[] = $results_call;}
      if($results_video){$results[] = $results_video;}
      if($results_chat){$results[] = $results_chat;}
        
        
        // doctor_consultation
        if($clinic_id == "0"){
          
            $charges = $this->db->query("SELECT * FROM `doctor_consultation`  WHERE `doctor_user_id` = '$doctor_id' AND `consultation_name` = '$consultation_type'");
            // [charges]
            foreach($charges->result_array() as $charge ){
                $doctor_consultation_visit = $charge['charges'];
            }
              
        }
        $doctor_consultation_video      = "";
        $doctor_consultation_voice_call = "";
        $doctor_image                   = $booking_details['image'];
        if ($doctor_image != '') {
            $doctor_image = str_replace(' ', '%20', $doctor_image);
            $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
        } else {
            $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
        }
        $doctor_ratings       = $booking_details['rating'];
        $doctor_qualification = $booking_details['qualifications'];
        $doctor_category      = $booking_details['category'];
        $area_expertise       = array();
        $query_sp             = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $doctor_category . "')");
        $total_category       = $query_sp->num_rows();
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
        $degree_array  = array();
        $degree_       = explode(',', $doctor_qualification);
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
        $special_array = array();
        $speciality    = explode(",", $speciality);
        $specialitycnt = count($speciality);
        //$specialitycnt--;
        if ($specialitycnt > 0) {
            for ($j = 0; $j < $specialitycnt; $j++) {
                $special_array[] = array(
                    'specialization' => $speciality[$j]
                );
            }
        } else {
            $special_array = array();
        }
        // $special_ = explode(',', $speciality);
        // $count_special_ = count($special_);
        // if ($count_special_ > 1) {
        //     foreach ($special_ as $special_) {
        //         $special_array[] = array(
        //             'specialization' => $special_
        //         );
        //     }
        // } else {
        //     $special_array = array();
        // }
        $testDetails = array(
            'doctor_id' => $doctor_id,
            'clinic_id' => $clinic_id,
            'booking_id' => $booking_id,
            'booking_date' => $booking_date,
            'booking_time' => $booking_time,
            'created_date' => $created_date,
            'consultation_type' => $consultation_type,
            'doctor_name' => $doctor_name,
            'email' => $doctor_email,
            'experience' => $doctor_experience,
            'dob' => $doctor_dob,
            'telephone' => $doctor_telephone,
            'info' => $results,
            'lat' => $doctor_lat,
            'lng' => $doctor_lng,
            'doctor_address' => $doctor_address,
            'doctor_image' => $doctor_image,
            'rating' => $doctor_ratings,
            'degree' => $degree_array,
            'status' => $status,
            'doctor_specialization' => $special_array,
            'area_expertise' => $area_expertise,
            'consultation_charges' => $doctor_consultation_visit,
            'discount_amount_min' => $booking_details['discount_min'],
            'discount_amount_max' => $booking_details['discount_max'],
            'discount_type' => $booking_details['discount_type'],
            'discount_limit' => $booking_details['discount_limit'],
            'discount_category' => $booking_details['discount_category'],
            'payable_amount' => $visit_charge
        );
        return $testDetails;
    }
    
            public function hospitals_notification_confirm($booking_id){
       $data="SELECT hospital_booking_master.*,users.name,users.email,users.phone, hospitals.name_of_hospital,hospitals.phone FROM hospital_booking_master LEFT JOIN users ON (hospital_booking_master.user_id=users.id) 
       LEFT JOIN hospitals ON (hospital_booking_master.listing_id=hospitals.user_id)WHERE hospital_booking_master.booking_id='".$booking_id."'";
         
         $result = $this->db->query($data)->row();
            return $result;
}
   
}
