<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CuppingtherapyModel extends CI_Model {

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

    public function get_time_difference_php($created_time) {
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

    public function cuppingtherapy_list($user_id, $latitude, $longitude) {

        $radius = '5';
        $query = $this->db->query("SELECT `id`,`user_id`,`image`, `name`, `description`,`address`, `pincode`, `contact`, `city`, `state`, `whatsapp`, `email`, `opening_hours`, `image`, `lat`, `lng`, `date`, `is_active`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM cuppingtherapy HAVING distance <= '$radius' order by id ASC");

        $count = $query->num_rows();
        if ($count > 0) {

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name = $row['name'];
                $address = $row['address'];
                $pincode = $row['pincode'];
                $contact = $row['contact'];
                $city = $row['city'];
                $state = $row['state'];
                $whatsapp = $row['whatsapp'];
                $email = $row['email'];
                $opening_hours = $row['opening_hours'];
                $cuppingtherapy_user_id = $row['user_id'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $rating = '4.0';
                //$profile_views = '2448';
                $profile_views = $this->db->select('id')->from('cupping_therapy_views')->where('listing_id', $cuppingtherapy_user_id)->get()->num_rows();

                //$reviews = '1500';
                $reviews = $this->db->select('id')->from('cuppingtherapy_review')->where('cuppingtherapy_id', $id)->get()->num_rows();

                $description = $row['description'];
                $image = $row['image'];
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $image;

                $gallery_query = $this->db->query("SELECT * FROM `cuppingtherapy_media` WHERE `id`='$id'");
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




                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $cuppingtherapy_user_id)->get()->num_rows();

                $following = $this->db->select('id')->from('follow_user')->where('user_id', $cuppingtherapy_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $cuppingtherapy_user_id)->where('parent_id', $cuppingtherapy_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }



                $gallery_query = $this->db->query("SELECT GROUP_CONCAT(`name`) AS title, GROUP_CONCAT(CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/',name)) AS media FROM `cuppingtherapy_media`");
                if ($gallery_query) {
                    $row2 = $gallery_query->row();
                    $gallery_name = $row2->title;
                    $gallery_name = explode(",", $gallery_name);

                    $gallery = $row2->media;
                    $gallery = explode(",", $gallery);
                } else {
                    $gallery = '';
                    $gallery_name = '';
                }


                $resultpost[] = array(
                    "id" => $id,
                    "name" => $name,
                    "cuppingtherapy_user_id" => $cuppingtherapy_user_id,
                    'listing_type' => "16",
                    "address" => $address,
                    "pincode" => $pincode,
                    "contact" => $contact,
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
                    "opening_day" => $final_Day,
                    "image" => $image,
                    "cupping_therapy_gallery_name" => $gallery_name,
                    "cupping_therapy_gallery_media" => $gallery
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

   public function cuppingtherapy_packages($user_id) {
        
        $query = $this->db->query("SELECT * FROM `packages` WHERE user_id='$user_id' AND v_id = '16' ");
        $count = $query->num_rows();
        $resultpost = array();

        if ($count > 0) {
           
                foreach ($query->result_array() as $get_list) {
                    
                    $id= $get_list['id'];
                    $package_name = $get_list['package_name'];
                    $package_details = $get_list['package_details'];
                    $price = $get_list['price'];
                    $image = $get_list['image'];
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $image;

                    $resultpost[] = array(
                        "package_id" => $id,
                        "package_name" => $package_name,
                        "package_details" => $package_details,
                        'price' => $price,
                        'image' => $image
                    );

                }

        } else {

            $resultpost = array();
        }
        if(is_null($resultpost)){
            $resultpost = array();
        }
        return $resultpost;
    }

    public function add_review($user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'cuppingtherapy_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('cuppingtherapy_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function review_list($user_id, $listing_id) {

        $resultpost = '';
        $review_count = $this->db->select('id')->from('cuppingtherapy_review')->where('cuppingtherapy_id', $listing_id)->get()->num_rows();
        
        if ($review_count > 0) {
            $query = $this->db->query("SELECT cuppingtherapy_review.id,cuppingtherapy_review.user_id,cuppingtherapy_review.cuppingtherapy_id,cuppingtherapy_review.rating,cuppingtherapy_review.review, cuppingtherapy_review.service,cuppingtherapy_review.date as review_date,users.id as user_id,users.name as firstname FROM `cuppingtherapy_review` INNER JOIN `users` ON cuppingtherapy_review.user_id=users.id WHERE cuppingtherapy_review.cuppingtherapy_id='$listing_id' order by cuppingtherapy_review.id desc");

            foreach ($query->result_array() as $row) {
                
               
                
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '2') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('cuppingtherapy_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('cuppingtherapy_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('cuppingtherapy_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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

public function review_with_comment($user_id, $listing_id) {

        $resultpost = '';
        $review_count = $this->db->select('id')->from('cuppingtherapy_review')->where('cuppingtherapy_id', $listing_id)->get()->num_rows();
        
        if ($review_count > 0) {
            $query = $this->db->query("SELECT cuppingtherapy_review.id,cuppingtherapy_review.user_id,cuppingtherapy_review.cuppingtherapy_id,cuppingtherapy_review.rating,cuppingtherapy_review.review, cuppingtherapy_review.service,cuppingtherapy_review.date as review_date,users.id as user_id,users.name as firstname FROM `cuppingtherapy_review` INNER JOIN `users` ON cuppingtherapy_review.user_id=users.id WHERE cuppingtherapy_review.cuppingtherapy_id='$listing_id' order by cuppingtherapy_review.id desc");

            foreach ($query->result_array() as $row) {
                
               
                
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
               /* if ($id > '2') {
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
                $review_date = $this->get_time_difference_php($review_date);

                $like_count = $this->db->select('id')->from('cuppingtherapy_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('cuppingtherapy_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('cuppingtherapy_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

$review_list_count = $this->db->select('id')->from('cuppingtherapy_review_comment')->where('post_id', $id)->get()->num_rows();
        if ($review_list_count) {
             $resultcomment = array();
            $querycomment = $this->db->query("SELECT cuppingtherapy_review_comment.id,cuppingtherapy_review_comment.post_id,cuppingtherapy_review_comment.comment as comment,cuppingtherapy_review_comment.date,users.name,cuppingtherapy_review_comment.user_id as post_user_id FROM cuppingtherapy_review_comment INNER JOIN users on users.id=cuppingtherapy_review_comment.user_id WHERE cuppingtherapy_review_comment.post_id='$id' order by cuppingtherapy_review_comment.id asc");

            foreach ($querycomment->result_array() as $row) {
                $comment_id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
               
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_countc = $this->db->select('id')->from('cuppingtherapy_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $like_yes_noc = $this->db->select('id')->from('cuppingtherapy_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = $this->get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimagec = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimagec = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date = $this->get_time_difference_php($date);
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
            $resultcomment = '';
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
        $count_query = $this->db->query("SELECT id from cuppingtherapy_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `cuppingtherapy_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from cuppingtherapy_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $cuppingtherapy_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('cuppingtherapy_review_likes', $cuppingtherapy_review_likes);
            $like_query = $this->db->query("SELECT id from cuppingtherapy_review_likes where post_id='$post_id'");
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

        $cuppingtherapy_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('cuppingtherapy_review_comment', $cuppingtherapy_review_comment);
        $cuppingtherapy_review_comment_query = $this->db->query("SELECT id from cuppingtherapy_review_comment where post_id='$post_id'");
        $total_comment = $cuppingtherapy_review_comment_query->num_rows();
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
        $count_query = $this->db->query("SELECT id from cuppingtherapy_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `cuppingtherapy_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from cuppingtherapy_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $cuppingtherapy_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('cuppingtherapy_review_comment_like', $cuppingtherapy_review_comment_like);
            $comment_query = $this->db->query("SELECT id from cuppingtherapy_review_comment_like where comment_id='$comment_id'");
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

        $review_list_count = $this->db->select('id')->from('cuppingtherapy_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT cuppingtherapy_review_comment.id,cuppingtherapy_review_comment.post_id,cuppingtherapy_review_comment.comment as comment,cuppingtherapy_review_comment.date,users.name,cuppingtherapy_review_comment.user_id as post_user_id FROM cuppingtherapy_review_comment INNER JOIN users on users.id=cuppingtherapy_review_comment.user_id WHERE cuppingtherapy_review_comment.post_id='$post_id' order by cuppingtherapy_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '9') {
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

                $like_count = $this->db->select('id')->from('cuppingtherapy_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('cuppingtherapy_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = $this->get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $date = $this->get_time_difference_php($date);
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
            $resultpost = '';
        }
        return $resultpost;
    }

public function add_appointment($user_id,$listing_id,$package_id,$appointment_date,$user_name,$user_mobile,$user_email,$user_gender,$type) {
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
            'type' => $type
        );
                        
        $insert = $this->db->insert('user_appointments', $data);
        $order_id = $this->db->insert_id();

        $query_branch = $this->db->query("SELECT `name`,`contact` FROM cuppingtherapy WHERE user_id='$user_id'");
        $row_branch = $query_branch->row_array();
        $name = $row_branch['name'];
        $contact = $row_branch['contact'];
        $date_ = $appointment_date ;


        //web notification starts
        $cupping_booking_notifications = array(
            'listing_id' => $listing_id,
            'order_id' => $order_id,
            'title' => 'New Booking',
            'msg' => 'You Have Received a New booking',
            'image' => '',
            'notification_type' => 'cupping_bookings',
            'order_status' => '',
            'order_date' => $date,
            'invoice_no' => $booking_id
        );
        $this->db->insert('pharmacy_notifications', $cupping_booking_notifications);
        
        $cupping_booking_notifications = array(
            'user_id' => $listing_id,
            'order_id' => $order_id,
            'title' => 'New Booking',
            'msg' => 'You Have Received a New booking',
            'image' => '',
            'notification_type' => 'cupping_bookings',
            'order_status' => '',
            'order_date' => $date,
            'invoice_no' => $booking_id
        );
        $this->db->insert('All_notification_Mobile', $cupping_booking_notifications);
        //web notification ends   

        if ($insert) {
            $message = 'Dear customer, your cupping theraphy booking is confirmed' . $name . '. Booking id is ' . $booking_id . '. Date ' . $date_ . '. Thank you.';
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
    
     /*Doctor read slot Testing purpose written*/ 
    public function user_read_slot($clinic_id, $doctor_id, $consultation_type)
	{
    
	$todayDay = date('l');
	$todayDate = date('Y-m-d H:i:s');
	
	for($i=0;$i<15;$i++){
	    $time_array = array();
        $time_array1 = array();
        $time_array2 = array();
        $time_array3 = array();
        $time_slott = array();
	    
	    $todayDate = date('Y-m-d', strtotime($todayDate .' +1 day'));
        $todayDay = date('l', strtotime($todayDate));
        
        $day_time_slots = $this->db->query("SELECT `id`,`consultation_type`,`clinic_id`,`doctor_id`,`day`,`timeSlot`,`time`,`from_time`,`to_time`,`status` FROM `doctor_clinic_timing` WHERE `doctor_id` = '$doctor_id' AND `consultation_type` = '$consultation_type' AND `day` = '$todayDay'");
        
	    $count_day_time_slots = $day_time_slots->num_rows();
        
        foreach($day_time_slots->result_array() as $row){
            $timeSlot = $row['timeSlot'];
			$from_time = $row['from_time'];
			$to_time = $row['to_time'];
			$status = $row['status'];
			
			
			$day_time_status = $this->db->query("SELECT `id`,`clinic_id`,`listing_id`,`booking_date`,`from_time`,`to_time`,`status` FROM `doctor_booking_master` WHERE `listing_id` = '$doctor_id' AND  `clinic_id`='$clinic_id' AND `booking_date` = '$todayDate' AND `status` = '1' And `from_time` = '$from_time' And `to_time` = '$to_time'");
    
            $count_day_time_status = $day_time_status->num_rows();
			
			if($count_day_time_status){
			    if ($timeSlot == 'Afternoon') {
				$time_array1[] = array(
					'from_time' => $from_time,
					'to_time' => $to_time,
					'status' => '1'
				);
				} else if ($timeSlot == 'Morning') {
				$time_array[] = array(
					'from_time' => $from_time,
					'to_time' => $to_time,
					'status' => '1'
				);
				} else if ($timeSlot == 'Evening') {
				$time_array3[] = array(
					'from_time' => $from_time,
					'to_time' => $to_time,
					'status' => '1'
				);
				} else if ($timeSlot == 'Night') {
				$time_array2[] = array(
					'from_time' => $from_time,
					'to_time' => $to_time,
					'status' => '1'
				);
			}
			}else{
			    if ($timeSlot == 'Afternoon') {
    				$time_array1[] = array(
    					'from_time' => $from_time,
    					'to_time' => $to_time,
    					'status' => '0'
    				);
    				} else if ($timeSlot == 'Morning') {
    				$time_array[] = array(
    					'from_time' => $from_time,
    					'to_time' => $to_time,
    					'status' => '0'
    				);
    				} else if ($timeSlot == 'Evening') {
    				$time_array3[] = array(
    					'from_time' => $from_time,
    					'to_time' => $to_time,
    					'status' => '0'
    				);
    				} else if ($timeSlot == 'Night') {
    				$time_array2[] = array(
    					'from_time' => $from_time,
    					'to_time' => $to_time,
    					'status' => '0'
    				);
    			}
			}
			
			
        }
        
        $time_slott[] = array(
			'Morning' => $time_array
		);
		$time_slott[] = array(
			'Afternoon' => $time_array1
		);
		$time_slott[] = array(
			'Night' => $time_array2
		);
		$time_slott[] = array(
			'Evening' => $time_array3
		);
	
	
        $time_slots[] = array(
             'day'=>$todayDay,
               'date'=>$todayDate,
           'timings'=> $time_slott
            ); 
	
	}
	
	
	
	return $time_slots;
	}

    
}
