<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HospitalModel extends CI_Model
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
    
    public function hospital_list($latitude, $longitude, $user_id, $category_name)
    {
        $radius = '5';
        
        $query = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM hospitals WHERE FIND_IN_SET('" . $category_name . "', category)  HAVING distance <= '$radius'");
        
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                  = $row['id'];
                $name_of_hospital    = $row['name_of_hospital'];
                $mobile              = $row['phone'];
                $about_us            = $row['about_us'];
                $establishment_year  = $row['establishment_year'];
                $certificates_accred = $row['certificates_accred'];
                $category            = $row['category'];
                $speciality          = $row['speciality'];
                $surgery             = $row['surgery'];
                $services            = $row['services'];
                $address             = $row['address'];
                $lat                 = $row['lat'];
                $lng                 = $row['lng'];
                $pincode             = $row['pincode'];
                
                $city             = $row['city'];
                $state            = $row['state'];
                $email            = $row['email'];
                $image            = $row['image'];
                $rating           = $row['rating'];
                $reviews          = $row['review'];
                $hospital_user_id = $row['user_id'];
                $profile_views    = '0';
                
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
                
                
                
                $certificates_accred_query = $this->db->query("SELECT `name`, image FROM `hospital_certificates_accred` WHERE FIND_IN_SET(name,'" . $certificates_accred . "')");
                foreach ($certificates_accred_query->result_array() as $get_clist) {
                    $certificates_name  = $get_clist['name'];
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
                
                $hospitals_surgery_query = $this->db->query("SELECT `id`,`surgery_name`,`surgery_rate`,`surgery_package`, image FROM `hospitals_surgery` WHERE FIND_IN_SET(surgery_name,'" . $surgery . "')");
                foreach ($hospitals_surgery_query->result_array() as $get_slist) {
                    $surgery_id      = $get_slist['id'];
                    $surgery_name    = $get_slist['surgery_name'];
                    $surgery_rate    = $get_slist['surgery_rate'];
                    $surgery_package = $get_slist['surgery_package'];
                    $surgery_image   = $get_slist['image'];
                    
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
                
                
                
                $gallery_query = $this->db->query("SELECT `title`, CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/',source) AS source FROM `hospital_media` WHERE hospital_id='$id'");
                foreach ($gallery_query->result_array() as $get_glist) {
                    $title       = $get_glist['title'];
                    $media_image = $get_glist['source'];
                    $gallery[]   = array(
                        "title" => $title,
                        "image" => $media_image
                    );
                }
                
                
                
                $hospitals_services_query = $this->db->query("SELECT `service_name` FROM `hospital_services` WHERE FIND_IN_SET(service_name,'" . $services . "')");
                foreach ($hospitals_services_query->result_array() as $get_serlist) {
                    $service_name             = $get_serlist['service_name'];
                    $hospitals_service_list[] = array(
                        "service_name" => $service_name
                    );
                }
                
                
                
                $hospitals_speciality_query = $this->db->query("SELECT `id`,`doctors_category`,`name`, image FROM `hospitals_specialist` WHERE FIND_IN_SET(name,'" . $speciality . "')");
                foreach ($hospitals_speciality_query->result_array() as $get_splist) {
                    $specialist_id    = $get_splist['id'];
                    $specialist_name  = $get_splist['name'];
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
    
    
    
    public function hospital_details($user_id, $listing_id)
    {
        $radius = '5';
        $query  = $this->db->query("SELECT `id`, `user_id`, `name_of_hospital`,`phone`,`about_us`, `certificates_accred`, `category`, `speciality`, `surgery`,`services`, `address`, `pincode`, `city`, `state`, `email`, `lat`, `lng`, `image`, `rating`, `review`,  `date`, `is_active`,`establishment_year` FROM hospitals WHERE user_id='$listing_id' limit 1");
        
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                  = $row['id'];
                $name_of_hospital    = $row['name_of_hospital'];
                $mobile              = $row['phone'];
                $about_us            = $row['about_us'];
                $establishment_year  = $row['establishment_year'];
                $certificates_accred = $row['certificates_accred'];
                $category            = $row['category'];
                $speciality          = $row['speciality'];
                $surgery             = $row['surgery'];
                $services            = $row['services'];
                $address             = $row['address'];
                $lat                 = $row['lat'];
                $lng                 = $row['lng'];
                $pincode             = $row['pincode'];
                
                $city             = $row['city'];
                $state            = $row['state'];
                $email            = $row['email'];
                $image            = $row['image'];
                $rating           = $row['rating'];
                $reviews          = $row['review'];
                $hospital_user_id = $row['user_id'];
                $profile_views    = '2458';
                
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
                
                $certificates_accred_list=array();
                $certificates_accred_query = $this->db->query("SELECT `name`, image FROM `hospital_certificates_accred` WHERE FIND_IN_SET(name,'" . $certificates_accred . "')");
                foreach ($certificates_accred_query->result_array() as $get_clist) {
                    $certificates_name  = $get_clist['name'];
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
                
                $hospitals_surgery_list=array();
                $hospitals_surgery_query = $this->db->query("SELECT `id`,`surgery_name`,`surgery_rate`,`surgery_package`, image FROM `hospitals_surgery` WHERE FIND_IN_SET(surgery_name,'" . $surgery . "')");
                foreach ($hospitals_surgery_query->result_array() as $get_slist) {
                    $surgery_id      = $get_slist['id'];
                    $surgery_name    = $get_slist['surgery_name'];
                    $surgery_rate    = $get_slist['surgery_rate'];
                    $surgery_package = $get_slist['surgery_package'];
                    $surgery_image   = $get_slist['image'];
                    
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
                
                $gallery=array();
                $gallery_query = $this->db->query("SELECT `title`, CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/',source) AS source FROM `hospital_media` WHERE hospital_id='$id'");
                foreach ($gallery_query->result_array() as $get_glist) {
                    $title     = $get_glist['title'];
                    $image     = $get_glist['source'];
                    $gallery[] = array(
                        "title" => $title,
                        "image" => $image
                    );
                }
                
                $hospitals_service_list=array();
                $hospitals_services_query = $this->db->query("SELECT `service_name` FROM `hospital_services` WHERE FIND_IN_SET(service_name,'" . $services . "')");
                foreach ($hospitals_services_query->result_array() as $get_serlist) {
                    $service_name             = $get_serlist['service_name'];
                    $hospitals_service_list[] = array(
                        "service_name" => $service_name
                    );
                }
                
                $hospitals_speciality_list=array();
                $hospitals_speciality_query = $this->db->query("SELECT `id`,`doctors_category`,`name`, image FROM `hospitals_specialist` WHERE FIND_IN_SET(name,'" . $speciality . "')");
                foreach ($hospitals_speciality_query->result_array() as $get_splist) {
                    $specialist_id    = $get_splist['id'];
                    $specialist_name  = $get_splist['name'];
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
                    //  'hospital_user_id' => $hospital_user_id,
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
    
    
    
    public function doctor_list($hospital_id, $category_name)
    {
        
        
        $query = $this->db->query("SELECT doctor_list.id,doctor_list.consultation_fee,doctor_list.lat,doctor_list.lng,doctor_list.category,doctor_list.user_id,doctor_list.doctor_name,doctor_list.about_us,doctor_list.speciality,doctor_list.address,doctor_list.telephone,doctor_list.medical_college,doctor_list.medical_affiliation,doctor_list.charitable_affiliation,doctor_list.awards_recognition,doctor_list.24_hrs_available,doctor_list.home_visit_available,doctor_list.qualification,doctor_list.experience,doctor_list.website,doctor_list.location,doctor_list.days,doctor_list.timing,doctor_list.image,doctor_list.rating,doctor_list.review FROM doctor_list INNER JOIN doctor_hospital_list ON doctor_list.id=doctor_hospital_list.doctor_id WHERE doctor_hospital_list.hospital_id='$hospital_id' AND FIND_IN_SET('" . $category_name . "', category)");
        
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                     = $row['id'];
                $doctor_name            = $row['doctor_name'];
                $about_us               = $row['about_us'];
                $speciality             = $row['category']; //changed by david
                $address                = $row['address'];
                $telephone              = $row['telephone'];
                $medical_college        = $row['medical_college'];
                $medical_affiliation    = $row['medical_affiliation'];
                $charitable_affiliation = $row['charitable_affiliation'];
                $awards_recognition     = $row['awards_recognition'];
                $hrs_available          = $row['24_hrs_available'];
                $home_visit_available   = $row['home_visit_available'];
                $qualification          = $row['qualification'];
                $consultation_fee       = $row['consultation_fee'];
                $experience             = $row['experience'];
                $website                = $row['website'];
                $location               = $row['location'];
                $days                   = $row['days'];
                $timing                 = $row['timing'];
                $rating                 = $row['rating'];
                $reviews                = $row['review'];
                $image                  = $row['image'];
                $doctor_user_id         = $row['user_id'];
                $profile_views          = '2458';
                $is_follow              = 'yes';
                
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $image;
                } else {
                    $image = '';
                }
                
                $followers       = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                $following       = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                $result_hospital = '';
                
                $hospital_query = $this->db->query("select hospitals.id as hospital_id,doctor_list.id as doctor_id,hospitals.name_of_hospital,hospitals.address,hospitals.rating,hospitals.image,doctor_hospital_list.opening_days from doctor_list INNER JOIN doctor_hospital_list on doctor_hospital_list.doctor_id = doctor_list.id INNER JOIN hospitals on hospitals.id=doctor_hospital_list.hospital_id WHERE doctor_list.id='$id'");
                $total_hospital = $hospital_query->num_rows();
                if ($total_hospital > 0) {
                    foreach ($hospital_query->result_array() as $hospital_row) {
                        $id             = $hospital_row['hospital_id'];
                        $hospital_name  = $hospital_row['name_of_hospital'];
                        $address        = $hospital_row['address'];
                        $rating         = $hospital_row['rating'];
                        $hospital_image = $hospital_row['image'];
                        $opening_days   = $hospital_row['opening_days'];
                        if ($hospital_image != '') {
                            $hospital_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/hospital_images/' . $hospital_image;
                        } else {
                            $hospital_image = '';
                        }
                        
                        
                        date_default_timezone_set('Asia/Kolkata');
                        $open_days     = '';
                        $day           = '';
                        $time          = '';
                        $start_time    = '';
                        $end_time      = '';
                        $opening_hours = explode(',', $opening_days);
                        foreach ($opening_hours as $opening_hour) {
                            $array_hours = explode('-', $opening_hour);
                            $day         = $array_hours[0];
                            $start_time  = $array_hours[1];
                            $end_time    = $array_hours[2];
                            $time        = $start_time . ' - ' . $end_time;
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
                $service               = '';
                $result_services       = '';
                $doctor_services_query = $this->db->query("SELECT service FROM `doctor_services`");
                foreach ($doctor_services_query->result_array() as $doctor_services) {
                    $service           = $doctor_services['service'];
                    $result_services[] = array(
                        'service' => $service
                    );
                }
                $specialization              = '';
                $result_specialization       = '';
                $doctor_specialization_query = $this->db->query("SELECT specialization FROM `doctor_specialization`");
                foreach ($doctor_specialization_query->result_array() as $doctor_specialization) {
                    $specialization          = $doctor_specialization['specialization'];
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
    
    
    
    public function hospitals_appointment($user_id, $hospital_id, $surgery_id, $patient_name, $gender, $age, $mobile, $ts1_date, $ts1_time, $ts2_date, $ts2_time, $medical_condition)
    {
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
    
    
    
    public function add_review($user_id, $listing_id, $rating, $review, $service)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date         = date('Y-m-d H:i:s');
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
        $resultpost   = array();
        $review_count = $this->db->select('id')->from('hospital_review')->where('hospital_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT hospital_review.id,hospital_review.user_id,hospital_review.hospital_id,hospital_review.rating,hospital_review.review, hospital_review.service,hospital_review.date as review_date,users.id as user_id,users.name as firstname FROM `hospital_review` INNER JOIN `users` ON hospital_review.user_id=users.id WHERE hospital_review.hospital_id='$listing_id' order by hospital_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if(base64_encode(base64_decode($review)) === $review){
                    $review=base64_decode($review);
                }		
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('hospital_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('hospital_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('hospital_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
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
    
    public function review_like($user_id, $post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `hospital_review_likes` WHERE post_id='$post_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        
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
    
    
    
    public function review_comment($user_id, $post_id, $comment)
    {
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
        $total_comment                 = $hospital_review_comment_query->num_rows();
        return array(
            'status' => 200,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }
    
    public function review_comment_like($user_id, $comment_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from hospital_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        
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
            $comment_query      = $this->db->query("SELECT id from hospital_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
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
        $review_list_count = $this->db->select('id')->from('hospital_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT hospital_review_comment.id,hospital_review_comment.post_id,hospital_review_comment.comment as comment,hospital_review_comment.date,users.name,hospital_review_comment.user_id as post_user_id FROM hospital_review_comment INNER JOIN users on users.id=hospital_review_comment.user_id WHERE hospital_review_comment.post_id='$post_id' order by hospital_review_comment.id asc");
            
            foreach ($query->result_array() as $row) {
                $id           = $row['id'];
                $post_id      = $row['post_id'];
                $comment      = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if(base64_encode(base64_decode($comment)) === $comment){
                    $comment=base64_decode($comment);
                }	
                $username     = $row['name'];
                $date         = $row['date'];
                $post_user_id = $row['post_user_id'];
                
                $like_count   = $this->db->select('id')->from('hospital_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('hospital_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);
                
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                
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
    
}