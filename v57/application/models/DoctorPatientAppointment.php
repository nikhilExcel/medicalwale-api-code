<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class DoctorPatientAppointment extends CI_Model {

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

    public function doctor_category($doctors_type_id) {

        $query = $this->db->query("SELECT * FROM `business_category` WHERE category_id = '5' AND  FIND_IN_SET('" . $doctors_type_id . "', doctors_type_id) order by id asc");

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $category_id = $row['category_id'];
            $category = $row['category'];
            $details = $row['details'];
            $image = $row['image'];
            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/doctor_category/' . $image;
            $resultpost[] = array(
                'id' => $id,
                'category_id' => $category_id,
                'category' => $category,
                'details' => $details,
                'image' => $image
            );
        }
        return $resultpost;
    }

    public function doctor_list($mlat, $mlng, $user_id, $category_id) {
        $radius = '3';

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

        $sql = sprintf("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( doctor_clinic.lat ) ) * cos( radians( doctor_clinic.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( doctor_clinic.lat ) ) ) ) AS distance FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE FIND_IN_SET('" . $category_id . "', doctor_list.category)  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
        $query = $this->db->query($sql);
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $lat = $row['lat'];
                $lng = $row['lng'];
                $doctor_name = $row['doctor_name'];
                $email = $row['email'];
                $gender = $row['gender'];
                $doctor_phone = $row['telephone'];
                $dob = $row['dob'];
                $category = $row['category'];
                $speciality = $row['speciality'];
                $service = $row['service'];
                $degree = $row['qualification'];
                $experience = $row['experience'];
                $reg_council = $row['reg_council'];
                $reg_number = $row['reg_number'];
                $doctor_user_id = $row['user_id'];
                $clinic_name = $row['clinic_name'];
                $address = $row['address'];
                $city = $row['city'];
                $state = $row['state'];
                $pincode = $row['pincode'];
                $followers = '0';
                $following = '0';
                $profile_views = '0';
                $total_reviews = '0';
                $total_rating = '0';
                $total_profile_views = '0';

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();

                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $doctor_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
                $row_rating = $query_rating->row_array();
                $total_rating = $row_rating['total_rating'];
                if ($total_rating === NULL || $total_rating === '') {
                    $total_rating = '0';
                }

                $total_reviews = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
                $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();


                if ($row['image'] != '') {
                    $profile_pic = $row['image'];
                    $profile_pic = str_replace(' ', '%20', $profile_pic);
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
                } else {
                    $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
                }


                $area_expertise = array();
                $query_sp = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $category . "')");

                $total_category = $query_sp->num_rows();
                if ($total_category > 0) {
                    foreach ($query_sp->result_array() as $get_sp) {
                        $id = $get_sp['id'];
                        $area_expertised = $get_sp['area_expertise'];
                        $area_expertise[] = array(
                            'id' => $id,
                            'area_expertise' => $area_expertised
                        );
                    }
                } else {
                    $area_expertise = array();
                }

                $speciality_array = array();
                $speciality_ = explode(',', $speciality);
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
                $service_ = explode(',', $service);
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

                $degree_array = array();
                $degree_ = explode(',', $degree);
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




                $doctor_practices = array();

                $sql2 = sprintf("SELECT `id`, `doctor_id`,`contact_no`, `clinic_name`,`open_hours`,`consultation_charges`, `address`, `state`, `city`, `pincode` ,`lat`, `lng`,	`image`, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS clinic_distance FROM doctor_clinic WHERE doctor_id='$doctor_user_id' HAVING clinic_distance < '%s' ORDER BY clinic_distance desc LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
                $query_practices = $this->db->query($sql2);
                $total_practices = $query_practices->num_rows();
                if ($total_practices > 0) {
                    foreach ($query_practices->result_array() as $get_pract) {

                        $clinic_id = $get_pract['id'];
                        $clinic_lat = $get_pract['lat'];
                        $clinic_lng = $get_pract['lng'];
                        $clinic_name = $get_pract['clinic_name'];
                        $clinic_phone = $get_pract['contact_no'];
                        $clinic_address = $get_pract['address'];
                        $clinic_state = $get_pract['state'];
                        $clinic_city = $get_pract['city'];
                        $clinic_pincode = $get_pract['pincode'];
                        $clinic_image = $get_pract['image'];
                        $opening_hours = $get_pract['open_hours'];
                        $consultation_charges = $get_pract['consultation_charges'];

                        if ($clinic_image != '') {
                            $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $clinic_image;
                        } else {
                            $clinic_image = '';
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
                                            $time[] = str_replace('close-close', 'Close', $time_check);

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
                            'opening_day' => $final_Day
                        );
                    }
                } else {
                    $doctor_practices = array();
                }
                
                
                $doctor_consultation = '';
                    $consultaion = $this->db->query("SELECT * FROM `doctor_consultation` WHERE `doctor_user_id` = '$doctor_user_id' AND is_active = '1'");
                 $consult_count = $consultaion->num_rows();
        if($consult_count>0){
        foreach ($consultaion->result_array() as $rows) {
            $doctor_user_id = $rows['doctor_user_id'];
            $consultation_name = $rows['consultation_name'];
            $charges = $rows['charges'];
            $duration = $rows['duration'];
           
            
            $doctor_consultation[] = array(
                'doctor_user_id' => $doctor_user_id,
                'consultation_name' => $consultation_name,
                'charges' => $charges,
                'duration' => $duration
            );
        }
       
        }
        else{
             $doctor_consultation = array();
        }
    


                $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));

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

    public function doctor_details($user_id, $listing_id) {


        $query = $this->db->query("SELECT doctor_list.user_id,doctor_list.doctor_name,doctor_list.email,doctor_list.gender,doctor_list.telephone,doctor_list.dob,doctor_list.category,doctor_list.speciality,doctor_list.service,doctor_list.qualification,doctor_list.experience,doctor_list.reg_council,doctor_list.reg_number,doctor_list.image,doctor_clinic.clinic_name,doctor_clinic.address,doctor_clinic.city,doctor_clinic.state,doctor_clinic.pincode,doctor_clinic.lat,doctor_clinic.lng FROM doctor_list INNER JOIN  doctor_clinic ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_list.user_id='$user_id'");

        $count = $query->num_rows();
        if ($count > 0) {
            $row = $query->row_array();

            $lat = $row['lat'];
            $lng = $row['lng'];
            $doctor_name = $row['doctor_name'];
            $email = $row['email'];
            $gender = $row['gender'];
            $doctor_phone = $row['telephone'];
            $dob = $row['dob'];
            $category = $row['category'];
            $speciality = $row['speciality'];
            $service = $row['service'];
            $degree = $row['qualification'];
            $experience = $row['experience'];
            $reg_council = $row['reg_council'];
            $reg_number = $row['reg_number'];
            $doctor_user_id = $row['user_id'];
            $clinic_name = $row['clinic_name'];
            $address = $row['address'];
            $city = $row['city'];
            $state = $row['state'];
            $pincode = $row['pincode'];
            $followers = '0';
            $following = '0';
            $profile_views = '0';
            $total_reviews = '0';
            $total_rating = '0';
            $total_profile_views = '0';

            $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
            $following = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();

            $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $doctor_user_id)->get()->num_rows();

            if ($is_follow > 0) {
                $is_follow = 'Yes';
            } else {
                $is_follow = 'No';
            }

            $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_user_id'");
            $row_rating = $query_rating->row_array();
            $total_rating = $row_rating['total_rating'];
            if ($total_rating === NULL || $total_rating === '') {
                $total_rating = '0';
            }

            $total_reviews = $this->db->select('id')->from('doctors_review')->where('doctor_id', $doctor_user_id)->get()->num_rows();
            $total_profile_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $doctor_user_id)->get()->num_rows();


            if ($row['image'] != '') {
                $profile_pic = $row['image'];
                $profile_pic = str_replace(' ', '%20', $profile_pic);
                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
            } else {
                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
            }


            $area_expertise = array();
            $query_sp = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $category . "')");

            $total_category = $query_sp->num_rows();
            if ($total_category > 0) {
                foreach ($query_sp->result_array() as $get_sp) {
                    $id = $get_sp['id'];
                    $area_expertised = $get_sp['area_expertise'];
                    $area_expertise[] = array(
                        'id' => $id,
                        'area_expertise' => $area_expertised
                    );
                }
            } else {
                $area_expertise = array();
            }



            $speciality_array = array();
            $speciality_ = explode(',', $speciality);
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
            $service_ = explode(',', $service);
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

            $degree_array = array();
            $degree_ = explode(',', $degree);
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




            $doctor_practices = array();
            $radius = '5';
            $sql2 = sprintf("SELECT `id`, `doctor_id`,`contact_no`, `clinic_name`,`open_hours`,`consultation_charges`, `address`, `state`, `city`, `pincode` ,`lat`, `lng`,	`image`, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS clinic_distance FROM doctor_clinic WHERE doctor_id='$doctor_user_id' HAVING clinic_distance < '%s' ORDER BY clinic_distance desc LIMIT 0 , 20", ($lat), ($lng), ($lat), ($radius));
            $query_practices = $this->db->query($sql2);
            $total_practices = $query_practices->num_rows();
            if ($total_practices > 0) {
                foreach ($query_practices->result_array() as $get_pract) {

                    $clinic_id = $get_pract['id'];
                    $clinic_lat = $get_pract['lat'];
                    $clinic_lng = $get_pract['lng'];
                    $clinic_name = $get_pract['clinic_name'];
                    $clinic_phone = $get_pract['contact_no'];
                    $clinic_address = $get_pract['address'];
                    $clinic_state = $get_pract['state'];
                    $clinic_city = $get_pract['city'];
                    $clinic_pincode = $get_pract['pincode'];
                    $clinic_image = $get_pract['image'];
                    $opening_hours = $get_pract['open_hours'];
                    $consultation_charges = $get_pract['consultation_charges'];

                    if ($clinic_image != '') {
                        $clinic_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $clinic_image;
                    } else {
                        $clinic_image = '';
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
                                        $time[] = str_replace('close-close', 'Close', $time_check);

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
                        'opening_day' => $final_Day
                    );
                }
            } else {
                $doctor_practices = array();
            }


            $resultpost[] = array(
                'doctor_user_id' => $doctor_user_id,
                'listing_type' => "5",
                'lat' => $lat,
                'lng' => $lng,
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
            );
        } else {
            $resultpost = array();
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
        $review_count = $this->db->select('id')->from('doctors_review')->where('doctor_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT doctors_review.id,doctors_review.user_id,doctors_review.doctor_id,doctors_review.rating,doctors_review.review, doctors_review.service,doctors_review.date as review_date,users.id as user_id,users.name as firstname FROM `doctors_review` INNER JOIN `users` ON doctors_review.user_id=users.id WHERE doctors_review.doctor_id='$listing_id' order by doctors_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
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

                $like_count = $this->db->select('id')->from('doctors_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('doctors_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('doctors_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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
        $count_query = $this->db->query("SELECT id from doctors_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `doctors_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from doctors_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $doctors_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('doctors_review_likes', $doctors_review_likes);
            $like_query = $this->db->query("SELECT id from doctors_review_likes where post_id='$post_id'");
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
            'doctor_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('doctors_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $doctors_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('doctors_review_comment', $doctors_review_comment);
        $doctors_review_comment_query = $this->db->query("SELECT id from doctors_review_comment where post_id='$post_id'");
        $total_comment = $doctors_review_comment_query->num_rows();
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
        $count_query = $this->db->query("SELECT id from doctors_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `doctors_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from doctors_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $doctors_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('doctors_review_comment_like', $doctors_review_comment_like);
            $comment_query = $this->db->query("SELECT id from doctors_review_comment_like where comment_id='$comment_id'");
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

        $query = $this->db->query("SELECT doctors_review_comment.id,doctors_review_comment.post_id,doctors_review_comment.comment as comment,doctors_review_comment.date,users.name,doctors_review_comment.user_id as post_user_id FROM doctors_review_comment INNER JOIN users on users.id=doctors_review_comment.user_id WHERE doctors_review_comment.post_id='$post_id' order by doctors_review_comment.id asc");
        $count = $query->num_rows();
        if ($count > 0) {
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

                $like_count = $this->db->select('id')->from('doctors_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('doctors_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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
            return $resultpost;
        } else {
            $resultpost = array();
        }
    }

    public function calculate_timing($start_time, $end_time, $time_difference) {
        for ($day_slot = strtotime($start_time); $day_slot <= strtotime($end_time); $day_slot = $day_slot + $time_difference * 60) {
            $ctime = date("h:i A", $day_slot);
            $time_ = date("H", $day_slot);
            $current_time = date('H');
            if ($current_time < $time_) {
                $is_available = '1';
            } else {
                $is_available = '0';
            }
            $week_time[] = array('time' => $ctime, 'is_booked' => 0, 'is_available' => $is_available);
        }
        return $week_time;
    }

    public function clinic_booking_slot($clinic_id, $doctor_id) {
        $ac_clinic_id = $clinic_id ;
        if($clinic_id=='0'){
         $query = $this->db->query("SELECT doctor_clinic.id, doctor_clinic.doctor_id, doctor_clinic.clinic_name, doctor_clinic.address, doctor_clinic.state, doctor_clinic.city, doctor_clinic.pincode, doctor_clinic.map_location, doctor_clinic.lat, doctor_clinic.lng, doctor_clinic.contact_no,IFNULL(doctor_clinic.consultation_charges,'') AS consultation_charges, doctor_clinic.appointment_time, doctor_clinic.open_hours, doctor_clinic.image AS clinic_image, IFNULL(doctor_list.doctor_name,'') AS doctor_name, IFNULL(doctor_list.experience,'') AS experience,doctor_list.qualification,doctor_list.category,doctor_list.image AS doctor_image FROM doctor_clinic LEFT JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.doctor_id='$doctor_id'");   
        }else{
        $query = $this->db->query("SELECT doctor_clinic.id, doctor_clinic.doctor_id, doctor_clinic.clinic_name, doctor_clinic.address, doctor_clinic.state, doctor_clinic.city, doctor_clinic.pincode, doctor_clinic.map_location, doctor_clinic.lat, doctor_clinic.lng, doctor_clinic.contact_no,IFNULL(doctor_clinic.consultation_charges,'') AS consultation_charges, doctor_clinic.appointment_time, doctor_clinic.open_hours, doctor_clinic.image AS clinic_image, IFNULL(doctor_list.doctor_name,'') AS doctor_name, IFNULL(doctor_list.experience,'') AS experience,doctor_list.qualification,doctor_list.category,doctor_list.image AS doctor_image FROM doctor_clinic LEFT JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id'");
        }
        $count = $query->num_rows();
        if ($count > 0) {
            $row = $query->row_array();
            $clinic_id = $row['id'];
            $doctor_name = $row['doctor_name'];
            $doctor_image = $row['doctor_image'];
            $category = $row['category'];
            $consultation_charges = $row['consultation_charges'];
            $degree = $row['qualification'];
            $experience = $row['experience'];
            $address = $row['address'];
            $state = $row['state'];
            $city = $row['city'];
            $pincode = $row['pincode'];
            $lat = $row['lat'];
            $lng = $row['lng'];
            $appointment_time = $row['appointment_time'];
            $time_difference_ = explode(' ', $appointment_time);
            $opening_hours = $row['open_hours'];
            $total_rating = '0';

            if ($doctor_image != '') {
                $doctor_image = str_replace(' ', '%20', $doctor_image);
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $doctor_image;
            } else {
                $doctor_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_profile_pic_user.png';
            }
            $query_rating = $this->db->query("SELECT  ROUND(AVG(rating),1) AS total_rating FROM doctors_review WHERE doctor_id='$doctor_id'");
            $row_rating = $query_rating->row_array();
            $total_rating = $row_rating['total_rating'];
            if ($total_rating === NULL || $total_rating === '') {
                $total_rating = '0';
            }
            $degree_array = array();
            $degree_ = explode(',', $degree);
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
            $area_expertise = array();
            $query_sp = $this->db->query("SELECT id,`category` AS area_expertise FROM `business_category` WHERE  FIND_IN_SET(id,'" . $category . "')");
            $total_category = $query_sp->num_rows();
            if ($total_category > 0) {
                foreach ($query_sp->result_array() as $get_sp) {
                    $id = $get_sp['id'];
                    $area_expertised = $get_sp['area_expertise'];
                    $area_expertise[] = array(
                        'id' => $id,
                        'area_expertise' => $area_expertised
                    );
                }
            } else {
                $area_expertise = array();
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
            $morning = array();
            $afternoon = array();
            $evening = array();
            $time = array();
            date_default_timezone_set('Asia/Kolkata');
            $data = array();
            $final_Day = array();
            /*$opening_hours = 'Monday>09:00 AM,04:00 PM-11:00 AM,06:00 PM|Tuesday>08:00 AM,02:00 PM-09:00 AM,05:00 PM|
            Wednesday>09:00 AM,04:00 PM-11:00 AM,06:00 PM|Thursday>09:00 AM,04:00 PM-11:00 AM,06:00 PM|
            Friday>09:00 AM,04:00 PM-11:00 AM,06:00 PM|Saturday>09:00 AM,04:00 PM-11:00 AM,06:00 PM|Sunday>close-close';*/
            $day_array_list = explode('|', $opening_hours);
            if (count($day_array_list) > 1) {
                $date_cal = '2';
                $time_difference = '60';
                $date_list = '';
                $day_time_array_list = '';
                $check_array = array();
                for ($date_i = 0; $date_i <= $date_cal; $date_i++) {
                    $system_date_list = date("Y-m-d", strtotime("+ $date_i day"));
                    $date_word = date('l', strtotime($system_date_list));
                    $morning = array();
                    $afternoon = array();
                    $evening = array();
                    $booking_slot_array = array();
                    $date_word_lower = strtolower($date_word);
                    $query_time = $this->db->query("SELECT time_shift,time FROM doctor_clinic_timing where clinic_id='$clinic_id' and doctor_id='$doctor_id' and day='$date_word_lower'");
                    $count_time = $query_time->num_rows();
                    if ($count_time > 0) {
                        foreach ($query_time->result_array() as $get_time) {
                            if (strtolower($get_time['time_shift']) == 'morning') {
                                $morning_time = $get_time['time'];
                                $time_list = explode('-', $morning_time);
                                $morning = $this->calculate_timing($time_list[0], $time_list[1], $time_difference);
                            }
                            if (strtolower($get_time['time_shift']) == 'afternoon') {
                                $afternoon_time = $get_time['time'];
                                $time_list = explode('-', $afternoon_time);
                                $afternoon = $this->calculate_timing($time_list[0], $time_list[1], $time_difference);
                            }
                            if (strtolower($get_time['time_shift']) == 'evening') {
                                $evening_time = $get_time['time'];
                                $time_list = explode('-', $evening_time);
                                $evening = $this->calculate_timing($time_list[0], $time_list[1], $time_difference);
                            }
                        }
                    }
                    $booking_slot_array[] = array(
                        'morning' => $morning,
                        'afternoon' => $afternoon,
                        'evening' => $evening,
                    );

                    $final_Day[] = array(
                        'date' => $system_date_list,
                        'day' => $date_word,
                        'slot' => $booking_slot_array,
                    );
                }
            } else {
                $final_Day[] = array(
                    'day' => 'close'
                );
            }
         $doctor_consultation = '';
         $video_consultaion = $this->db->query("SELECT * FROM `doctor_consultation` WHERE `doctor_user_id` = '$doctor_id' AND is_active = '1' AND consultation_name = 'video'");
                    //$query = $this->db->query($consultaion);
                 $consult_count = $video_consultaion->num_rows();
        if($consult_count>0){
            $row = $video_consultaion->row_array();
             $consultation_name = $row['consultation_name'];
             $is_active = $row['is_active'];
             $charges = $row['charges'];
             

            $open_hours = $row['open_hours'];
            $consultation_timing = array();
                    $day_array_list = explode('|', $open_hours);
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
                                        $time[] = str_replace('close-close', 'Close', $time_check);

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
                            $consultation_timing[] = array(
                                'day' => $day_list[0],
                                'time' => $time,
                                'status' => $open_close
                            );
                        }
                    } else {
                        $consultation_timing[] = array(
                            'day' => 'close',
                            'time' => array(),
                            'status' => array()
                        );
                    }
            $doctor_consultation = $consultation_timing;
        }
         if($ac_clinic_id=='0'){
             $resultpost[] = array(
              
                'doctor_name' => $doctor_name,
                'doctor_image' => $doctor_image,
                'area_expertise' => $area_expertise,
                'consultation_charges' => $consultation_charges,
                'experience' => $experience,
                'doctor_degree' => $degree_array,
                'address' => $address,
                'state' => $state,
                'city' => $city,
                'pincode' => $pincode,
                'rating' => $total_rating,
               
                'video_consultation_booking_slots' => $final_Day,
                'call_consultation_booking_slots' => $final_Day,
                'chat_consultation_booking_slots' => $final_Day
            );
         }
         else{
            $resultpost[] = array(
                'clinic_id' => $clinic_id,
                'doctor_name' => $doctor_name,
                'doctor_image' => $doctor_image,
                'area_expertise' => $area_expertise,
                'consultation_charges' => $consultation_charges,
                'experience' => $experience,
                'doctor_degree' => $degree_array,
                'address' => $address,
                'state' => $state,
                'city' => $city,
                'pincode' => $pincode,
                'rating' => $total_rating,
                'inperson_booking_slots' => $final_Day,
                'video_consultation_booking_slots' => $final_Day,
                'call_consultation_booking_slots' => $final_Day,
                'chat_consultation_booking_slots' => $final_Day
            );
             }
        } 
        
        else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function doctor_views($user_id, $listing_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $doctor_views_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'date' => $date
        );
        $this->db->insert('doctor_views', $doctor_views_array);

        $doctor_views = $this->db->select('id')->from('doctor_views')->where('listing_id', $listing_id)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'doctor_views' => $doctor_views
        );
    }
    
    /*Gender don't update doubt in it.*/
    public function add_bookings($user_id, $listing_id, $clinic_id, $booking_date, $booking_time, $user_name, $user_mobile, $user_email, $user_gender,
                                     $is_user, $is_patient_added, $patient_id, $health_condition, $allergies, $heradiatry_problem, $description, $relationship, 
                                     $date_of_birth, $connect_type) {
        date_default_timezone_set('Asia/Kolkata');
        
        $booking_id = date("YmdHis");
        $date = date('Y-m-d H:i:s');
        $status = 1;
        $booking_master_array = array(
            'booking_id' => $booking_id,
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'clinic_id' => $clinic_id,
            'booking_date' => $booking_date,
            'booking_time' => $booking_time,
            'status' => $status,
            'description' => $description,
            'created_date' => $date
        );

         $insert=$this->db->insert('doctor_booking_master', $booking_master_array);
         $appointment_id = $this->db->insert_id();
         if($appointment_id>0)
         {
                $patient_master_array = array(
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
                         'doctor_id' => $listing_id
                );
                $this->db->insert('doctor_patient', $patient_master_array);
                 $doctor_patient_id = $this->db->insert_id();
                 if($appointment_id<0)
                {
                     return array(
                     'status' => 208,
                      'message' => 'Database Insert Issue'
                   );
                }
         } else{
              return array(
                     'status' => 208,
                      'message' => 'Database Insert Issue'
                   );
         }
        /* 0 for No 
           1 for Yes */
        if($is_user!="1")
        {
            if($is_patient_added!="1")
            {
              //insert query  
                  $health_record = array(
                         'user_id' => $user_id,
                         'patient_name' => $user_name,
                         'relationship' => $relationship,
                         'date_of_birth' => $date_of_birth,
                         'gender' => $user_gender,
                         'created_at' => $date
                    );
                  $this->db->insert('health_record', $health_record);
                  $patient_id = $this->db->insert_id();
                  
            } else{
                // update query
                  $count_user = $this->db->select('id')->from('health_record')->where('id', $patient_id)->get()->num_rows();
                  if($count_user > 0){
                       $health_record = array(
                           'user_id' => $user_id,
                           'patient_name' => $user_name,
                           'relationship' => $relationship,
                           'date_of_birth' => $date_of_birth,
                           'gender' => $user_gender,
                           'created_at' => $date
                       );
                       $updateStatus=$this->db->where('id', $patient_id)->update('health_record', $health_record);
                    
                  }else {
                  return array(
                     'status' => 208,
                      'message' => 'Relative data not found'
                   );
               }
            }     
        }else{
            //update user fields
             $count_user = $this->db->select('id')->from('users')->where('id', $user_id)->get()->num_rows();
             if ($count_user > 0) {
              
                $user_data = array(
                    'health_condition' => $health_condition,
                    'allergies' => $allergies,
                    'updated_at' => $date,
                    'heradiatry_problem' => $heradiatry_problem,
                );
                  $updateStatus=$this->db->where('id', $user_id)->update('users', $user_data);
                  if(!$updateStatus)
                  {
                      return array(
                     'status' => 204,
                      'message' => 'Update failed'
                   );
                  }
             }else {
                  return array(
                     'status' => 208,
                      'message' => 'User not found'
                   );
               }
        }
        $query_dr = $this->db->query("SELECT doctor_clinic.clinic_name,doctor_list.doctor_name,doctor_list.telephone FROM doctor_clinic INNER JOIN doctor_list ON doctor_list.user_id=doctor_clinic.doctor_id WHERE doctor_clinic.id='$clinic_id' AND doctor_clinic.doctor_id='$listing_id'");
        $data = $query_dr->row_array();
        $doctor_name = $data['doctor_name'];
        $clinic_name = $data['clinic_name'];
        $doctor_phone = $data['telephone'];
        $final_booking_date = date('M-d', strtotime($booking_date));

        if ($insert) {
            $message = 'Your appointment with ' . $doctor_name . ' at ' . $clinic_name . ' for ' . $final_booking_date . ', ' . $booking_time . ' is confirmed. Thanks Medicalwale.com';
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
            $login_url = 'https://doctor.medicalwale.com';
            $message2 = 'You have received ' . $type_of_order . ', kindly login to ' . $login_url . '. Thank you.';
            $post_data2 = array('From' => '02233721563', 'To' => $doctor_phone, 'Body' => $message2);
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
         $this->notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $booking_id); 
        return array(
            'status' => 200,
            'message' => 'success',
            'booking_id' => $booking_id
        );
       
      
    }
    
    /*
    This method is used for notification.
    */
    public function notifyMethod($listing_id, $user_id, $description, $booking_date, $booking_time, $appointment_id)
    {

          $customer_token = $this->db->query("SELECT name,token,agent,token_status,web_token FROM users WHERE id='$listing_id'");
          $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
            //    $getusr = $user_plike->row_array();

                $usr_name = $token_status['name'];
                $agent = $token_status['agent'];
                $reg_id = $token_status['token'];
                $web_token = $token_status['web_token'];
                $img_url = 'https://medicalwale.com/img/medical_logo.png';
                $tag = 'text';
                $key_count = '1';
                $title = $usr_name . ' has booked an appointment';
                $msg = $usr_name . " has booked an appointment.\n"+ $description;
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_time, $appointment_id);
                
                // Web notification FCM function by ghanshyam parihar Added date:07 Feb 2019
                $click_action = 'https://hospitals.medicalwale.com/booking_controller/booking_appointment/'.$listing_id;
                // $click_action = 'https://vendor.sandbox.medicalwale.com/doctor/booking_controller/booking_appointment/'.$listing_id;
                $this->send_gcm_web_notify($title, $msg, $web_token, $img_url, $tag, $agent,$click_action);
                // Web notification FCM function by ghanshyam parihar Added date:07 Feb 2019 Ends
            }
    }


    // Web notification FCM function by ghanshyam parihar Added date:07 Feb 2019
    function send_gcm_web_notify($title, $msg, $reg_id, $img_url, $tag, $agent, $click_action) {
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
    // Web notification FCM function by ghanshyam parihar Added date:07 Feb 2019 Ends

        //send notification through firebase
        /* notification to send in the doctor app for appointment confirmation*/
    public function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $booking_date, $booking_time, $appointment_id) {
     
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
                "appointment_date" => $booking_date,
                "appointment_time" => $booking_time,
                "appointment_id" => $appointment_id,
                "type_of_connect" => 'Call'
            )
        );
        /*IOS registration is left */
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AAAAMCq6aPA:APA91bFjtuwLeq5RK6_DFrXntyJdXCFSPI0JdJcvoXXi0xIGm7qgzQJmUl7aEq3HjUTV3pvlFr5iv5pOWtCoN3JIpMSVhU8ZFzusaibehi5MPwThRmx1pCnm3Tm-x6wI8tGwhc0eUj2U' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
