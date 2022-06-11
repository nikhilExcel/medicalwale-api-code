<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class BloodbankModel extends CI_Model {

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

    public function bank_list($user_id, $latitude, $longitude) {
        $radius = '5';
        $query = $this->db->query("SELECT user_id,id,name as bank_name,year as established,phone as contact,address,about as about_us,hours_open as opening_hours,fda_no as fda_lic_no,bto as bto_name,component,image,reviews,lat,lng,ratings,state,city,pincode,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM blood_bank where is_active='1' HAVING distance <= '$radius' ");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $bank_name = $row['bank_name'];
                $established = $row['established'];
                $contact = $row['contact'];
                $address = $row['address'];
                $about_us = $row['about_us'];

                $fda_lic_no = $row['fda_lic_no'];
                $bto_name = $row['bto_name'];
                $component = $row['component'];
                $image = $row['image'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/bloodbank_images/' . $image;
                } else {
                    $image = '';
                }
                $blood_bank_user_id = $row['user_id'];
                $rating = $row['ratings'];
                $profile_views = '451';
                $reviews = $row['reviews'];
                $opening_hours = $row['opening_hours'];
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
                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $blood_bank_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $blood_bank_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $blood_bank_user_id)->get()->num_rows();
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                $profile_view = $this->db->select('id')->from('bloodbank_views')->where('listing_id', $blood_bank_user_id)->get()->num_rows();
                $review = $this->db->select('id')->from('blood_bank_review')->where('blood_bank_id', $blood_bank_user_id)->get()->num_rows();
                $resultpost[] = array(
                    'id' => $id,
                    'blood_bank_user_id' => $blood_bank_user_id,
                    'bank_name' => $bank_name,
                    'listing_type' => "4",
                    'established' => $established,
                    'contact' => $contact,
                    'address' => $address,
                    'about_us' => $about_us,
                    'fda_lic_no' => $fda_lic_no,
                    'bto_name' => $bto_name,
                    'component' => $component,
                    'opening_day' => $final_Day,
                    'image' => $image,
                    'rating' => $rating,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_view,
                    'reviews' => $review,
                    'is_follow' => $is_follow,
                    'lat' => $lat,
                    'lng' => $lng
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function bank_details($user_id, $listing_id) {
        $radius = '5';
        $query = $this->db->query("SELECT user_id,id,lat,lng,name as bank_name,year as established,phone as contact,address,about as about_us,hours_open as opening_hours,fda_no as fda_lic_no,bto as bto_name,component,image,reviews,ratings,state,city,pincode FROM blood_bank where user_id='$listing_id' order by id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $bank_name = $row['bank_name'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $established = $row['established'];
                $contact = $row['contact'];
                $address = $row['address'];
                $about_us = $row['about_us'];
                $opening_hours = $row['opening_hours'];
                $fda_lic_no = $row['fda_lic_no'];
                $bto_name = $row['bto_name'];
                $component = $row['component'];
                $image = $row['image'];
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
                $blood_bank_user_id = $row['user_id'];
                $rating = $row['ratings'];
                $profile_views = '451';
                $reviews = $row['reviews'];
                $open_days = '';
                date_default_timezone_set('Asia/Kolkata');
                $opening_hours = explode(',', $opening_hours);
                $flag = '1';
                $current_day = array();
                foreach ($opening_hours as $opening_hours) {
                    $array_hours = explode('-', $opening_hours);
                    $day = $array_hours[0];
                    $start_time = $array_hours[1];
                    $end_time = $array_hours[2];
                    $cur_time = date("H.i");
                    $cur_day = date("l");
                    $time = $start_time . ' - ' . $end_time;
                    if ($cur_day == $day && $flag > 0) {
                        $start_time_val = date("H.i", strtotime($start_time));
                        $end_time_val = date("H.i", strtotime($end_time));
                        if ($cur_time >= $start_time_val && $cur_time <= $end_time_val) {
                            $total_hr = $end_time_val - $start_time_val;
                            $current_time = $time;
                            if ($total_hr > 23.58) {
                                $current_time = 'Open 24 Hrs';
                            }
                            $day_ = 'Open';
                        } else {
                            $current_time = $time;
                            $day_ = 'Close';
                        }
                        $current_day = array(
                            'day' => $day_,
                            'time' => $current_time
                        );
                        $flag = '0';
                    }
                    $start_time_val = date("H.i", strtotime($start_time));
                    $end_time_val = date("H.i", strtotime($end_time));
                    $total_hr = $end_time_val - $start_time_val;
                    if ($total_hr > 23.58) {
                        $time = 'Open 24 Hrs';
                    }
                    $open_days[] = array(
                        'day' => $day,
                        'time' => $time
                    );
                }
                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $blood_bank_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $blood_bank_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $blood_bank_user_id)->get()->num_rows();
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                $resultpost[] = array(
                    'id' => $id,
                    'blood_bank_user_id' => $blood_bank_user_id,
                    'bank_name' => $bank_name,
                    'established' => $established,
                     'lat' => $lat,
                    'lng' => $lng,
                    'contact' => $contact,
                    'address' => $address,
                    'about_us' => $about_us,
                    'fda_lic_no' => $fda_lic_no,
                    'bto_name' => $bto_name,
                    'component' => $component,
                    'current_day' => $current_day,
                    'opening_day' => $open_days,
                    'image' => $image,
                    'rating' => $rating,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'reviews' => $reviews,
                    'is_follow' => $is_follow
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function blood_group_stock($blood_bank_id) {
        $query = $this->db->query("SELECT id,blood_group FROM `blood_group` order by id asc");
        $resultstock = array();
        foreach ($query->result_array() as $row) {
            $blood_group_id = $row['id'];
            $blood_group = $row['blood_group'];
            $stock_query = $this->db->query("SELECT exp_date,count(exp_date) as stock FROM `blood_group_stock` where blood_bank_id='$blood_bank_id' and blood_group_id='$blood_group_id' group by exp_date order by exp_date");
            $total_stock = $stock_query->num_rows();
            $resultstock = '';
            $in_stock = '';
            $exp_date = '';
            $stock = '';
            $stock_count = '0';
            if ($total_stock > 0) {
                foreach ($stock_query->result_array() as $stock_row) {
                    $exp_date = $stock_row['exp_date'];
                    $stock = $stock_row['stock'];
                    date_default_timezone_set('Asia/Calcutta');
                    $curr_date = date('Y-m-d');
                    if ($exp_date > $curr_date) {
                        $stock_count += $stock;
                        $in_stock = 'In Stock : ' . sprintf("%02d", $stock);
                        $resultstock[] = array(
                            'in_stock' => $in_stock,
                            'exp_date' => $exp_date
                        );
                    } else {
                        $resultstock = array();
                    }
                }
            } else {
                $resultstock = array();
            }
            $resultgroup[] = array(
                'blood_group_id' => $blood_group_id,
                'blood_group' => $blood_group,
                'count' => $stock_count,
                'stock_list' => $resultstock
            );
        }
        return $resultgroup;
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
        $review_count = $this->db->select('id')->from('blood_bank_review')->where('blood_bank_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT blood_bank_review.id,blood_bank_review.user_id,blood_bank_review.blood_bank_id,blood_bank_review.rating,blood_bank_review.review, blood_bank_review.service,blood_bank_review.date as review_date,users.id as user_id,users.name as firstname FROM `blood_bank_review` INNER JOIN `users` ON blood_bank_review.user_id=users.id WHERE blood_bank_review.blood_bank_id='$listing_id' order by blood_bank_review.id desc");

            foreach ($query->result_array() as $row) {
                $user_id = $row['user_id'];
                $id = $row['id'];
                $username = $row['firstname'];
                $post_user_id = $row['user_id'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '17') {
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

                $like_count  = $this->db->select('id')->from('blood_bank_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('blood_bank_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('blood_bank_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                
                $resultpost[] = array(
                    'user_id' => $user_id,
                    'id' => $id,
                    'username' => $username,
                    'userimage' => $userimage,
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
        $review_count = $this->db->select('id')->from('blood_bank_review')->where('blood_bank_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT blood_bank_review.id,blood_bank_review.user_id,blood_bank_review.blood_bank_id,blood_bank_review.rating,blood_bank_review.review, blood_bank_review.service,blood_bank_review.date as review_date,users.id as user_id,users.name as firstname FROM `blood_bank_review` INNER JOIN `users` ON blood_bank_review.user_id=users.id WHERE blood_bank_review.blood_bank_id='$listing_id' order by blood_bank_review.id desc");

            foreach ($query->result_array() as $row) {
                $user_id = $row['user_id'];
                $id = $row['id'];
                $username = $row['firstname'];
                $post_user_id = $row['user_id'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '17') {
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

                $like_count  = $this->db->select('id')->from('blood_bank_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('blood_bank_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('blood_bank_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                
                $review_list_count = $this->db->select('id')->from('blood_bank_review_comment')->where('post_id', $id)->get()->num_rows();
        if ($review_list_count) {
            $querycomment = $this->db->query("SELECT blood_bank_review_comment.id,blood_bank_review_comment.post_id,blood_bank_review_comment.comment as comment,blood_bank_review_comment.date,users.name,blood_bank_review_comment.user_id as post_user_id FROM blood_bank_review_comment INNER JOIN users on users.id=blood_bank_review_comment.user_id WHERE blood_bank_review_comment.post_id='$id' order by blood_bank_review_comment.id asc");
             $resultcomment = array();
            foreach ($querycomment->result_array() as $rowc) {
                $comment_id = $rowc['id'];
                $post_id = $rowc['post_id'];
                $comment = $rowc['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                $usernamec = $rowc['name'];
                $date = $rowc['date'];
                $post_user_id = $rowc['post_user_id'];
                $like_countc = $this->db->select('id')->from('blood_bank_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $like_yes_noc = $this->db->select('id')->from('blood_bank_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
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
                    'user_id' => $user_id,
                    'id' => $id,
                    'username' => $username,
                    'userimage' => $userimage,
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
        $count_query = $this->db->query("SELECT id from blood_bank_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `blood_bank_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from blood_bank_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $blood_bank_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('blood_bank_review_likes', $blood_bank_review_likes);
            $like_query = $this->db->query("SELECT id from blood_bank_review_likes where post_id='$post_id'");
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
            'blood_bank_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('blood_bank_review', $review_array);
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
           $this->db->update('blood_bank_review',$review_array);
        //$this->db->insert('fitness_center_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function review_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $blood_bank_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('blood_bank_review_comment', $blood_bank_review_comment);
        $blood_bank_review_comment_query = $this->db->query("SELECT id from blood_bank_review_comment where post_id='$post_id'");
        $total_comment = $blood_bank_review_comment_query->num_rows();
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
        $count_query = $this->db->query("SELECT id from blood_bank_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `blood_bank_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from blood_bank_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $blood_bank_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('blood_bank_review_comment_like', $blood_bank_review_comment_like);
            $comment_query = $this->db->query("SELECT id from blood_bank_review_comment_like where comment_id='$comment_id'");
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

        $review_list_count = $this->db->select('id')->from('blood_bank_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT blood_bank_review_comment.id,blood_bank_review_comment.post_id,blood_bank_review_comment.comment as comment,blood_bank_review_comment.date,users.name,blood_bank_review_comment.user_id as post_user_id FROM blood_bank_review_comment INNER JOIN users on users.id=blood_bank_review_comment.user_id WHERE blood_bank_review_comment.post_id='$post_id' order by blood_bank_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '41') {
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
                $like_count = $this->db->select('id')->from('blood_bank_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('blood_bank_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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
    
     public function blood_donate($user_id) {
     

        $query = $this->db->query("SELECT blood_group From users where id= '$user_id' ");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
              
                $blood_group = $row['blood_group'];
               if($blood_group !="")
               {
                  
                   $query1 = $this->db->query("SELECT blood_group From users where id= '$user_id' and blood_is_active=0 ");
                   $count1 = $query1->num_rows();
                   if ($count1 > 0)
                   {
                          $this->db->where('id', $user_id)->update('users', array(
                            
                            'blood_is_active' => 1
                        ));
                        
                        
                        $resultpost[] = array(
                           'Status' => "Successfully Done "
                        );
                   }
                   else
                   {
                     $resultpost[] = array(
                           'Status' => "Already Donate"
                        );  
                   }
               }
               else
               {
                 $resultpost[] = array(
                   'Status' => "No Blood Group"
                );  
               }
            }
        } else {
            $resultpost = $resultpost[] = array(
                   'Status' => "No Record Found"
                ); 
        }
        return $resultpost;
    }

    public function blood_donor_list($user_id, $lat, $lng, $radius, $blood_group, $page) {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        $blood_group2 = explode(',', $blood_group);
        $blood_group3 = implode("','", $blood_group2);
        $blood_group_final = "'$blood_group3'";

        $query = $this->db->query("SELECT users.id as user_id, users.gender, users.age, users.dob, users.blood_group, users.name, users.avatar_id, media.id,users.blood_is_active , media.source, media.title,( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance FROM users LEFT JOIN media ON users.avatar_id= media.id WHERE users.blood_group IN ($blood_group_final) HAVING distance <= '$radius' AND users.blood_is_active='1' AND users.id<>'$user_id' order by users.id desc limit $start, $limit");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $user_id_2 = $row['user_id'];
                $name = $row['name'];
                $age = $row['age'];
                $gender = $row['gender'];
                $dob = $row['dob'];
                $blood_group = $row['blood_group'];
                $image_source = $row['source'];
                $image = $row['title'];
                $query_blood_request = $this->db->query("SELECT blood_request.user_id,blood_request_donors.donor_id FROM `blood_request` INNER JOIN blood_request_donors ON blood_request.id=blood_request_donors.blood_request_id WHERE blood_request.user_id='$user_id' AND blood_request_donors.donor_id='$user_id_2'");
                $count_blood_request = $query_blood_request->num_rows();
                if ($age != "") {
                    list($day, $month, $year) = explode("/", $dob);
                    $age = date("Y") - $year;
                }
                $list_of_equipment = array();
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                } else {
                    $image = '';
                }
                $resultpost[] = array(
                    'user_id' => $user_id_2,
                    'name' => $name,
                    'age' => $age,
                    'gender' => $gender,
                    'blood_group' => $blood_group,
                    'image' => $image,
                    'is_blood_request' => $count_blood_request
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function blood_request($user_id, $donor_id) {
        date_default_timezone_set('Asia/Calcutta');
        $date = date('Y-m-d H:i:s');
        $blood_request_array = array(
            'user_id' => $user_id,
            'request_date' => $date
        );
        $this->db->insert('blood_request', $blood_request_array);
        $blood_request_id = $this->db->insert_id();
        if ($this->db->affected_rows() > 0) {

            function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $user_id) {
                date_default_timezone_set('Asia/Kolkata');
                $request_date = date('j M Y h:i A');
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
                        "notification_type" => "blood_request",
                        "notification_date" => $request_date,
                        "user_id" => $user_id
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
                // print_r($result);
                if ($result === FALSE) {
                    die('Problem occurred: ' . curl_error($ch));
                }
                curl_close($ch);
            }

            $donor_ids = explode(',', $donor_id);
            foreach ($donor_ids as $donor_ids_array) {
                $blood_request_donors_array = array(
                    'blood_request_id' => $blood_request_id,
                    'donor_id' => $donor_ids_array
                );
                $this->db->insert('blood_request_donors', $blood_request_donors_array);
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id='$donor_ids_array'");
                $customer_token_count = $customer_token->num_rows();
                if ($customer_token_count > 0) {
                    $token_status = $customer_token->row_array();
                    $agent = $token_status['agent'];
                    $reg_id = $token_status['token'];
                    $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
                    $tag = 'text';
                    $key_count = '1';
                    $title = 'Blood donation request';
                    $msg = 'Someone needs a blood donation near you. Lets them know if you can help.';
                    
                    
                      $notification_array = array(
                       'title' => $title,
                      'msg'  => $msg,
                      'img_url' => $img_url,
                    'tag' => $tag,
                       'order_status' => "",
                      'order_date' => "",
                      'order_id'   => "",
                      'post_id'  => "",
                      'listing_id'  => $blood_request_id,
                     'booking_id'  => "",
                      'invoice_no' => "",
                      'user_id'  => $user_id,
                      'notification_type'  => 'prescription',
                      'notification_date'  => date('Y-m-d H:i:s')
                       
            );
        $this->db->insert('All_notification_Mobile', $notification_array);
                    
                    send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $user_id);
                }
            }
        }
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    public function bloodbank_profile_views($user_id, $listing_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $bloodbank_views_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'date' => $date
        );
        $this->db->insert('bloodbank_views', $bloodbank_views_array);

        $bloodbank_views = $this->db->select('id')->from('bloodbank_views')->where('listing_id', $listing_id)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'bloodbank_views' => $bloodbank_views
        );
    }
    
     public function blood_profile_details($user_id, $listing_id) 
     {
         $query = $this->db->query("SELECT blood_group,phone  FROM users WHERE id = '$user_id'");
         $count = $query->num_rows();
        if ($count > 0) 
           {
            $row= $query->row_array();
            $blood_group = $row['blood_group'];
            $user_phone =$row['phone'];
            
             
                
                $q = $this->db->select('*')->from('users')->where('users.id', $listing_id)->get()->row();
               
                $name = $q->name;
                $phone = $q->phone;
              

                $img_count = $this->db->select('media.id')->from('media')->join('users', 'users.avatar_id=media.id','LEFT')->where('users.id', $listing_id)->get()->num_rows();

                if ($img_count > 0) {
                    $media = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $listing_id)->get()->row();
                    $img_file = $media->source;
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                    
                } else {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                   
                }
                
                
                
                
             
                $resultpost = array(
                    'user_id' => $user_id,
                    'listing_id' => $listing_id,
                    'user_phone' => $user_phone,
                    'listing_name' => $name,
                    'listing_phone' => $phone,
                    'blood_group' => $blood_group,
                    'image' => $image,
                    
                );
            
        } else {
            $resultpost = array();
        }
        return $resultpost;
      
     }
    
    
    
    

}
