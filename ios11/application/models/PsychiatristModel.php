<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PsychiatristModel extends CI_Model {

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

    public function psychiatrist_list($latitude, $longitude, $user_id) {
        $radius = '5';

        $query = $this->db->query("SELECT id,lat,lng,user_id,doctor_name,about_us,speciality,address,telephone,medical_college,medical_affiliation,charitable_affiliation,awards_recognition,all_24_hrs_available,home_visit_available,qualification,consultation_fee,experience,website,location,days,timing,image,rating,review,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM psychiatrist_list ");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $doctor_name = $row['doctor_name'];
                $about_us = $row['about_us'];

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



                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $image;
                } else {
                    $image = '';
                }


                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();

                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $doctor_user_id)->get()->num_rows();
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                $result_hospital = '';

                $hospital_query = $this->db->query("select hospitals.id as hospital_id,doctor_list.id as doctor_id,hospitals.name_of_hospital,hospitals.address,hospitals.rating,hospitals.image,doctor_hospital_list.opening_days from doctor_list INNER JOIN doctor_hospital_list on doctor_hospital_list.doctor_id = doctor_list.id INNER JOIN hospitals on hospitals.id=doctor_hospital_list.hospital_id where doctor_list.id='$id'");
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
                    'listing_type' => "23",
                    'about_us' => $about_us,
                    'address' => $address,
                    'telephone' => $telephone,
                    'medical_college' => $medical_college,
                    'medical_affiliation' => $medical_affiliation,
                    'charitable_affiliation' => $charitable_affiliation,
                    'awards_recognition' => $awards_recognition,
                    'hrs_available' => $hrs_available,
                    'home_visit_available' => $home_visit_available,
                    'qualification' => $qualification,
                    'consultation_fee' => $consultation_fee,
                    'experience' => $experience,
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

    public function psychiatrist_details($user_id, $listing_id) {
        $radius = '5';

        $query = $this->db->query("SELECT id,lat,lng,user_id,doctor_name,about_us,speciality,address,telephone,medical_college,medical_affiliation,charitable_affiliation,awards_recognition,all_24_hrs_available,home_visit_available,qualification,consultation_fee,experience,website,location,days,timing,image,rating,review FROM psychiatrist_list WHERE user_id='$listing_id' limit 1");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $doctor_name = $row['doctor_name'];
                $about_us = $row['about_us'];

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


                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/doctor_images/' . $image;
                } else {
                    $image = '';
                }

                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $doctor_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $doctor_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $doctor_user_id)->get()->num_rows();
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                $result_hospital = '';

                $hospital_query = $this->db->query("select hospitals.id as hospital_id,doctor_list.id as doctor_id,hospitals.name_of_hospital,hospitals.address,hospitals.rating,hospitals.image,doctor_hospital_list.opening_days from doctor_list INNER JOIN doctor_hospital_list on doctor_hospital_list.doctor_id = doctor_list.id INNER JOIN hospitals on hospitals.id=doctor_hospital_list.hospital_id where doctor_list.id='$id'");
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
                    'address' => $address,
                    'telephone' => $telephone,
                    'medical_college' => $medical_college,
                    'medical_affiliation' => $medical_affiliation,
                    'charitable_affiliation' => $charitable_affiliation,
                    'awards_recognition' => $awards_recognition,
                    'hrs_available' => $hrs_available,
                    'home_visit_available' => $home_visit_available,
                    'qualification' => $qualification,
                    'consultation_fee' => $consultation_fee,
                    'experience' => $experience,
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
        $this->db->insert('psychiatrist_review', $review_array);
        return array(
            'status' => 201,
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

        $resultpost = '';
        $review_count = $this->db->select('id')->from('psychiatrist_review')->where('doctor_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT psychiatrist_review.id,psychiatrist_review.user_id,psychiatrist_review.doctor_id,psychiatrist_review.rating,psychiatrist_review.review, psychiatrist_review.service,psychiatrist_review.date as review_date,users.id as user_id,users.name as firstname FROM `psychiatrist_review` INNER JOIN `users` ON psychiatrist_review.user_id=users.id WHERE psychiatrist_review.doctor_id='$listing_id' order by psychiatrist_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '10') {
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

                $like_count = $this->db->select('id')->from('psychiatrist_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('psychiatrist_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('psychiatrist_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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
            $resultpost = '';
        }

        return $resultpost;
    }

    public function review_like($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from psychiatrist_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `psychiatrist_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from psychiatrist_review_likes where post_id='$post_id'");
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
            $this->db->insert('psychiatrist_review_likes', $doctors_review_likes);
            $like_query = $this->db->query("SELECT id from psychiatrist_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
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
        $this->db->insert('psychiatrist_review_comment', $doctors_review_comment);
        $doctors_review_comment_query = $this->db->query("SELECT id from psychiatrist_review_comment where post_id='$post_id'");
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
        $count_query = $this->db->query("SELECT id from psychiatrist_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `psychiatrist_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from psychiatrist_review_comment_like where comment_id='$comment_id'");
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
            $this->db->insert('psychiatrist_review_comment_like', $doctors_review_comment_like);
            $comment_query = $this->db->query("SELECT id from psychiatrist_review_comment_like where comment_id='$comment_id'");
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

        $query = $this->db->query("SELECT psychiatrist_review_comment.id,psychiatrist_review_comment.post_id,psychiatrist_review_comment.comment as comment,psychiatrist_review_comment.date,users.name,psychiatrist_review_comment.user_id as post_user_id FROM psychiatrist_review_comment INNER JOIN users on users.id=psychiatrist_review_comment.user_id WHERE psychiatrist_review_comment.post_id='$post_id' order by psychiatrist_review_comment.id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($comment_id > '12') {
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

                $like_count = $this->db->select('id')->from('psychiatrist_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('psychiatrist_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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
            $resultpost = '';
        }
    }

}
