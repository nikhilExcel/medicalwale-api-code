<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PestcontrolModel extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";

    public function check_auth_client() {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
        }
    }

    public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
        } else {
            if ($q->expired_at < date('Y-m-d H:i:s')) {
                return json_output(401, array('status' => 401, 'message' => 'Your session has been expired.'));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = '2030-11-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array('expired_at' => $expired_at, 'updated_at' => $updated_at));
                return array('status' => 200, 'message' => 'Authorized.');
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

    public function pestcontrol_list() {
        $query = $this->db->query("SELECT id,name, contact,address,image,user_id FROM `pest_control` where is_active = '1' order by id asc");
        $count = $query->num_rows();


        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];

                $listing_id = $row['user_id'];
                $name = $row['name'];
                $total_reviews = 3.5;
                $contact = $row['contact'];
                $address = $row['address'];
                $total_views = '0';
                $image = $row['image'];
                if($image=="pest_control_icon.png")
                {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/pestcontrol_images/' . $image;
                }
                else
                {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                }


                $resultpost[] = array(
                    "id" => $id,
                    "name" => $name,
                    'listing_id' => $listing_id,
                    'listing_type' => "19",
                    'image' => $image,
                    'total_reviews' => $total_reviews,
                    'phone' => $contact,
                    'address' => $address,
                    'total_views' => $total_views
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function pestcontrol_details($pestcontrol_id, $user_id) {

        $query = $this->db->query("SELECT `id`,`user_id`,`image`, `name`, `description`,`address`, `pincode`, `contact`, `city`, `state`, IFNULL(whatsapp,'') AS whatsapp, `email`, `opening_hours`, `image`, IFNULL(lat,'') AS lat, IFNULL(lng,'') AS lng, `date`, `is_active` FROM `pest_control` WHERE id='$pestcontrol_id' order by id asc");
        $count = $query->num_rows();
        $resultpost = array();
        if ($count > 0) {

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $pestcontrol_user_id = $row['user_id'];
                $name = $row['name'];
                $address = $row['address'];
                $pincode = $row['pincode'];
                $contact = $row['contact'];
                $city = $row['city'];
                $state = $row['state'];
                $whatsapp = $row['whatsapp'];
                $email = $row['email'];
                $opening_hours = $row['opening_hours'];
                $pestcontrol_user_id = $row['user_id'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $rating = '4.5';
                //$profile_views = '0';
                $profile_views = $this->db->select('id')->from('pestcontrol_views')->where('listing_id', $pestcontrol_user_id)->get()->num_rows();
                

                //$reviews = '0';
                $reviews = $this->db->select('id')->from('pest_control_review')->where('pest_control_id', $id)->get()->num_rows();
                
                $description = $row['description'];
                $image = $row['image'];
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/pestcontrol_images/' . $image;

                $gallery_query = $this->db->query("SELECT * FROM `pest_control_media` WHERE `pest_control_id`='$id'");
                $gallery_array = array();
                $gallery_count = $gallery_query->num_rows();
                if ($gallery_count > 0) {
                    foreach ($gallery_query->result_array() as $rows) {
                        $media_name = $rows['title'];
                        $source = $rows['source'];
                        $gallery = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $source;

                        $media_name = str_replace(".jpg", "", $media_name);
                        $gallery_name = $media_name;

                        $cnt = count($gallery);

                        $gallery_array[] = array(
                            "title" => $gallery_name,
                            "image" => $gallery
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



                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $pestcontrol_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $pestcontrol_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $pestcontrol_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }


                $is_trial = $this->db->select('id')->from('pestcontrol_trials')->where('user_id', $user_id)->where('pestcontrol_user_id', $pestcontrol_user_id)->get()->num_rows();

                if ($is_trial > 0) {
                    $is_trial = 'Yes';
                } else {
                    $is_trial = 'No';
                }


                $gallery_query = $this->db->query("SELECT GROUP_CONCAT(`title`) AS title, GROUP_CONCAT(CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/pestcontrol_images/',source)) AS media FROM `pest_control_media`");
                if ($gallery_query) {
                    $row2 = $gallery_query->row();
                    $gallery_name = $row2->title;
                    $gallery = $row2->media;
                } else {
                    $gallery = '';
                    $gallery_name = '';
                }


                // $packages_query = $this->db->query("SELECT id,packages FROM `pest_control` WHERE id='$pestcontrol_id'");
                // $count = $packages_query->num_rows();

                // if ($count > 0) {
          //          foreach ($packages_query->result_array() as $row) {
           //             $packages = $row['packages'];


                        //$pestcontrol_packages = $this->db->query("SELECT id,package_name,package_details,price,image FROM `packages` WHERE FIND_IN_SET(  package_name,'" . $packages . "')");
                        
                         $pestcontrol_packages = $this->db->query("SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id = '$pestcontrol_user_id'");
                      //   echo "SELECT id,package_name,package_details,price,image FROM `packages` WHERE user_id = '$pestcontrol_user_id'";
                        $count2 = $pestcontrol_packages->num_rows();
                        if ($count2 > 0) {

                            foreach ($pestcontrol_packages->result_array() as $get_list) {
                                $id = $get_list['id'];
                                $package_name = $get_list['package_name'];
                                $package_details = $get_list['package_details'];
                                $price = $get_list['price'];
                                $image = $get_list['image'];
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/pestcontrol_images/' . $image;

                                $package[] = array(
                                    "package_id" => "$id",
                                    "package_name" => $package_name,
                                    "package_details" => $package_details,
                                    'price' => $price,
                                    'image' => $image
                                );
                            }
                        } else {

                            $package = array();
                        }
                   // }
                // } else {

                //     $package = array();
                // }

                $resultpost[] = array(
                    "id" => $id,
                    "pestcontrol_user_id" => $pestcontrol_user_id,
                    "name" => $name,
                    "address" => $address,
                    "pincode" => $pincode,
                    "contact" => $contact,
                    "exotel_no"=>'02233721563',
                    "city" => $city,
                    "state" => $state,
                    "whatsapp" => $whatsapp,
                    "email" => $email,
                    "gallery" => $gallery_array,
                    "description" => $description,
                    "rating" => $rating,
                    "followers" => $followers,
                    "following" => $following,
                    "profile_views" => $profile_views,
                    "reviews" => $reviews,
                    "is_follow" => $is_follow,
                    "lat" => $lat,
                    "lng" => $lng,
                    'opening_day' => $final_Day,
                    'is_trial' => $is_trial,
                    "image" => $image,
                    "package" => $package
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

  /*
    public function pestcontrol_packages($pestcontrol_id) {
        $query = $this->db->query("SELECT id,packages FROM `pest_control` WHERE id='$pestcontrol_id'");
        $count = $query->num_rows();


        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $packages = $row['packages'];


                $pestcontrol_packages = $this->db->query("SELECT * FROM `pestcontrol_packages` WHERE FIND_IN_SET(package_name,'" . $packages . "')");
                foreach ($pestcontrol_packages->result_array() as $get_list) {
                    $package_name = $get_list['package_name'];
                    $package_details = $get_list['package_details'];
                    $price = $get_list['price'];
                    $image = $get_list['image'];
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/pestcontrol_images/' . $image;

                    $resultpost[] = array(
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
*/


    public function pestcontrol_packages($pestcontrol_id) {
        $query = $this->db->query("SELECT id,packages FROM `pest_control` WHERE id='$pestcontrol_id'");
        $count = $query->num_rows();


        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $packages = $row['packages'];


                $pestcontrol_packages = $this->db->query("SELECT * FROM `packages` WHERE FIND_IN_SET(package_name,'" . $packages . "')");
                foreach ($pestcontrol_packages->result_array() as $get_list) {
                    $package_name = $get_list['package_name'];
                    $package_details = $get_list['package_details'];
                    $price = $get_list['price'];
                    $image = $get_list['image'];
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/pestcontrol_images/' . $image;

                    $resultpost[] = array(
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
    public function pestcontrol_trials($pestcontrol_user_id, $user_id, $name, $mobile, $trial_date) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');

        $pestcontrol_trials = array(
            'pestcontrol_user_id' => $pestcontrol_user_id,
            'user_id' => $user_id,
            'name' => $name,
            'mobile' => $mobile,
            'trial_date' => $trial_date,
            'date' => $date
        );
        $this->db->insert('pestcontrol_trials', $pestcontrol_trials);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function add_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'pest_control_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('pest_control_review', $review_array);
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
        $review_count = $this->db->select('id')->from('pest_control_review')->where('pest_control_id', $listing_id)->get()->num_rows();

        if ($review_count > 0) {
            //echo "SELECT media.source,pest_control_review.id,pest_control_review.user_id,pest_control_review.pest_control_id,pest_control_review.rating,pest_control_review.review, pest_control_review.service,pest_control_review.date as review_date,IFNULL(users.id,'') AS user_id,IFNULL(users.name,'') AS firstname FROM pest_control_review LEFT JOIN users ON pest_control_review.user_id=users.id LEFT JOIN media ON(media.id=users.avatar_id) WHERE pest_control_review.pest_control_id='$listing_id' order by pest_control_review.id DESC";
            //$query = $this->db->query("SELECT pest_control_review.id,pest_control_review.user_id,pest_control_review.pest_control_id,pest_control_review.rating,pest_control_review.review, pest_control_review.service,pest_control_review.date as review_date,users.id as user_id,users.name as firstname FROM `pest_control_review` INNER JOIN `users` ON pest_control_review.user_id=users.id WHERE pest_control_review.pest_control_id='$listing_id' order by pest_control_review.id desc");
            $query = $this->db->query("SELECT media.source,pest_control_review.id,pest_control_review.user_id,pest_control_review.pest_control_id,pest_control_review.rating,pest_control_review.review, pest_control_review.service,pest_control_review.date as review_date,IFNULL(users.id,'') AS user_id,IFNULL(users.name,'') AS firstname FROM pest_control_review LEFT JOIN users ON pest_control_review.user_id=users.id LEFT JOIN media ON(media.id=users.avatar_id) WHERE pest_control_review.pest_control_id='$listing_id' order by pest_control_review.id DESC");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                if($row['source']!='' || !empty($row['source'])){
                    $source = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $row['source'];
                }
                else{
                    $source = '';
                }
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '29') {
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

                $like_count = $this->db->select('id')->from('pest_control_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('pest_control_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('pest_control_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'userimage' => $source,
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
        $review_count = $this->db->select('id')->from('pest_control_review')->where('pest_control_id', $listing_id)->get()->num_rows();

        if ($review_count > 0) {
            //echo "SELECT media.source,pest_control_review.id,pest_control_review.user_id,pest_control_review.pest_control_id,pest_control_review.rating,pest_control_review.review, pest_control_review.service,pest_control_review.date as review_date,IFNULL(users.id,'') AS user_id,IFNULL(users.name,'') AS firstname FROM pest_control_review LEFT JOIN users ON pest_control_review.user_id=users.id LEFT JOIN media ON(media.id=users.avatar_id) WHERE pest_control_review.pest_control_id='$listing_id' order by pest_control_review.id DESC";
            //$query = $this->db->query("SELECT pest_control_review.id,pest_control_review.user_id,pest_control_review.pest_control_id,pest_control_review.rating,pest_control_review.review, pest_control_review.service,pest_control_review.date as review_date,users.id as user_id,users.name as firstname FROM `pest_control_review` INNER JOIN `users` ON pest_control_review.user_id=users.id WHERE pest_control_review.pest_control_id='$listing_id' order by pest_control_review.id desc");
            $query = $this->db->query("SELECT media.source,pest_control_review.id,pest_control_review.user_id,pest_control_review.pest_control_id,pest_control_review.rating,pest_control_review.review, pest_control_review.service,pest_control_review.date as review_date,IFNULL(users.id,'') AS user_id,IFNULL(users.name,'') AS firstname FROM pest_control_review LEFT JOIN users ON pest_control_review.user_id=users.id LEFT JOIN media ON(media.id=users.avatar_id) WHERE pest_control_review.pest_control_id='$listing_id' order by pest_control_review.id DESC");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $source=$row['source'];
                if($source!='' || !empty($source)){
                    $source = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $row['source'];
                }
                else{
                    $source = '';
                }
                $review = preg_replace('~[\r\n]+~', '', $review);
                /*if ($id > '29') {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($review)) === $review) {
                        $review = base64_decode($review);
                    }
                }*/
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('pest_control_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('pest_control_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('pest_control_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();


 $review_list_count = $this->db->select('id')->from('pest_control_review_comment')->where('post_id', $id)->get()->num_rows();
        if ($review_list_count) {
            $resultcomment = array();
            $querycomment = $this->db->query("SELECT pest_control_review_comment.id,pest_control_review_comment.post_id,pest_control_review_comment.comment as comment,pest_control_review_comment.date,users.name,pest_control_review_comment.user_id as post_user_id FROM pest_control_review_comment INNER JOIN users on users.id=pest_control_review_comment.user_id WHERE pest_control_review_comment.post_id='$id' order by pest_control_review_comment.id asc");

            foreach ($querycomment->result_array() as $rowc) {
                $comment_id = $rowc['id'];
                $post_id = $rowc['post_id'];
                $comment = $rowc['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
              

                $username = $rowc['name'];
                $date = $rowc['date'];
                $post_user_id = $rowc['post_user_id'];

                $like_countc = $this->db->select('id')->from('pest_control_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $like_yes_noc = $this->db->select('id')->from('pest_control_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
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
                    'username' => $username,
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
                    'userimage' => $source,
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
        $count_query = $this->db->query("SELECT id from pest_control_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `pest_control_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from pest_control_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $pest_control_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('pest_control_review_likes', $pest_control_review_likes);
            $like_query = $this->db->query("SELECT id from pest_control_review_likes where post_id='$post_id'");
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

        $pest_control_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('pest_control_review_comment', $pest_control_review_comment);
        $pest_control_review_comment_query = $this->db->query("SELECT id from pest_control_review_comment where post_id='$post_id'");
        $total_comment = $pest_control_review_comment_query->num_rows();
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
        $count_query = $this->db->query("SELECT id from pest_control_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `pest_control_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from pest_control_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $pest_control_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('pest_control_review_comment_like', $pest_control_review_comment_like);
            $comment_query = $this->db->query("SELECT id from pest_control_review_comment_like where comment_id='$comment_id'");
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

        $review_list_count = $this->db->select('id')->from('pest_control_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT pest_control_review_comment.id,pest_control_review_comment.post_id,pest_control_review_comment.comment as comment,pest_control_review_comment.date,users.name,pest_control_review_comment.user_id as post_user_id FROM pest_control_review_comment INNER JOIN users on users.id=pest_control_review_comment.user_id WHERE pest_control_review_comment.post_id='$post_id' order by pest_control_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($comment_id > '23') {
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

                $like_count = $this->db->select('id')->from('pest_control_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('pest_control_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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

    
 public function add_appointment($user_id,$listing_id,$package_id,$appointment_date,$user_name,$user_mobile,$user_email,$user_gender,$address,$pincode,$type,$status) {
        date_default_timezone_set('Asia/Kolkata');
        $booking_id = date("YmdHis");
        $date = date('Y-m-d H:i:s');
        
        $data = array(

            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'booking_id' => $booking_id,
            'package_id' => $package_id,
            'appointment_date' => $appointment_date,
            'user_name' => $user_name,
            'user_mobile' => $user_mobile,
            'user_email' => $user_email,
            'user_gender' => $user_gender,
            'address' => $address,
            'pincode' => $pincode,
            'type' => $type,
            'status'=>'54',//status 54=confirm,tbl=change_pharmacy_order_status
        );
                        
        $insert = $this->db->insert('pestcontrol_booking_master', $data);
        $order_id = $this->db->insert_id();

        $query_branch = $this->db->query("SELECT `name`,`contact` FROM pest_control WHERE user_id='$user_id'");
        $row_branch = $query_branch->row_array();
        $name = $row_branch['name'];
        $contact = $row_branch['contact'];
        $date_ = $appointment_date ;


        //web notification starts
        $pestcontrol_booking_notifications = array(
            'listing_id' => $listing_id,
            'order_id' => $order_id,
            'title' => 'New Booking',
            'msg' => 'You Have Received a New booking',
            'image' => '',
            'notification_type' => 'pestcontrol_bookings',
            'order_status' => '',
            'order_date' => $date,
            'invoice_no' => $booking_id
        );
        $this->db->insert('pharmacy_notifications', $pestcontrol_booking_notifications);
        //web notification ends   

        if ($insert) {
            $message = 'Dear customer, your booking is confirmed at pest control  ' . $name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.';
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

            $type_of_order = 'appointment';
            $login_url = 'https://pestcontrol.medicalwale.com';
            $message2 = 'You have received ' . $type_of_order . ', kindly login to ' . $login_url . '. Thank you.';
            $post_data2 = array('From' => '02233721563', 'To' => $contact, 'Body' => $message2);
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
    
    public function pestcontrol_profile_views($user_id, $listing_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $pestcontrol_views_array = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'date' => $date
        );
        $this->db->insert('pestcontrol_views', $pestcontrol_views_array);

        $pestcontrol_views = $this->db->select('id')->from('pestcontrol_views')->where('listing_id', $listing_id)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'fitness_views' => $pestcontrol_views
        );
    }
    
}
