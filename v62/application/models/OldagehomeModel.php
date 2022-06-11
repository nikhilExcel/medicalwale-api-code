<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class OldagehomeModel extends CI_Model {

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

    public function oldagehome_details($user_id, $listing_id) {
        $radius = '5';

        $query = $this->db->query("SELECT `id`, `user_id`, `name`, `about_us`, `establishment_year`, `address`, `phone`, `telephone`, `state`, `city`, `pincode`, `image`, `service_offered`, `lat`, `lng`, `reviews`, `ratings` FROM oldagehome where user_id='$listing_id' limit 1");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name = $row['name'];
                $about_us = $row['about_us'];
                $establishment_year = $row['establishment_year'];
                $address = $row['address'];
                $phone = $row['phone'];
                $state = $row['state'];
                $city = $row['city'];
                $pincode = $row['pincode'];
                $oldagehome_service_offered2 = $row['service_offered'];
                $image = $row['image'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $oldagehome_service_offered = array();
                $gallery_list = array();
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/oldagehome_images/' . $image;
                } else {
                    $image = '';
                }

                $oldagehome_user_id = $row['user_id'];
                $rating = $row['ratings'];
                $profile_views = '441';
                $reviews = $row['reviews'];


                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }



                $service_offered_query = $this->db->query("SELECT `name` FROM `oldagehome_service_offered` WHERE FIND_IN_SET(name,'" . $oldagehome_service_offered2 . "')");
                foreach ($service_offered_query->result_array() as $get_list) {
                    $service_offered = $get_list['name'];
                    $oldagehome_service_offered[] = array(
                        "service_offered" => $service_offered
                    );
                }

                $gallery_query = $this->db->query("SELECT `title` , CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/oldagehome_images/',source) AS media FROM `oldagehome_media` WHERE oldagehome_id='$id'");
                foreach ($gallery_query->result_array() as $get_list2) {
                    $gallery_title = $get_list2['title'];
                    $gallery_image = $get_list2['media'];
                    $gallery_list[] = array(
                        "title" => $gallery_title,
                        "image" => $gallery_image
                    );
                }

                $resultpost[] = array(
                    'id' => $id,
                    'oldagehome_user_id' => $oldagehome_user_id,
                    'name' => $name,
                    'about_us' => $about_us,
                    'establishment_year' => $establishment_year,
                    'address' => $address,
                    'phone' => $phone,
                    'state' => $state,
                    'city' => $city,
                    'pincode' => $pincode,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'image' => $image,
                    'service_offered' => $oldagehome_service_offered,
                    'gallery' => $gallery_list,
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

    public function oldagehome_list($user_id, $latitude, $longitude) {
        $radius = '5';

        $query = $this->db->query("SELECT `id`, `user_id`, `name`, `about_us`, `establishment_year`, `address`, `phone`, `telephone`, `state`, `city`, `pincode`, `image`, `service_offered`, `lat`, `lng`, `reviews`, `ratings`,`user_discount`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM oldagehome HAVING distance <= '$radius' order by id ASC");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $name = $row['name'];
                $about_us = $row['about_us'];
                $establishment_year = $row['establishment_year'];
                $address = $row['address'];
                $phone = $row['phone'];
                $state = $row['state'];
                $city = $row['city'];
                $pincode = $row['pincode'];
                $oldagehome_service_offered2 = $row['service_offered'];
                $image = $row['image'];
                $lat = $row['lat'];
                $lng = $row['lng'];
                $user_discount =$row['user_discount'];
                $oldagehome_service_offered = array();
                $gallery_list = array();
                if ($image != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/oldagehome_images/' . $image;
                } else {
                    $image = '';
                }

                $oldagehome_user_id = $row['user_id'];
                $rating = $row['ratings'];
                $profile_views = '441';
                $reviews = $row['reviews'];


                $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $oldagehome_user_id)->get()->num_rows();
                $following = $this->db->select('id')->from('follow_user')->where('user_id', $oldagehome_user_id)->get()->num_rows();
                $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $oldagehome_user_id)->where('parent_id', $oldagehome_user_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $resultpost = array();

                $service_offered_query = $this->db->query("SELECT `name` FROM `oldagehome_service_offered` WHERE FIND_IN_SET(name,'" . $oldagehome_service_offered2 . "')");
                foreach ($service_offered_query->result_array() as $get_list) {
                    $service_offered = $get_list['name'];
                    $oldagehome_service_offered[] = array(
                        "service_offered" => $service_offered
                    );
                }

                $gallery_query = $this->db->query("SELECT `title` , CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/oldagehome_images/',source) AS media FROM `oldagehome_media` WHERE oldagehome_id='$id'");
                foreach ($gallery_query->result_array() as $get_list2) {
                    $gallery_title = $get_list2['title'];
                    $gallery_image = $get_list2['media'];
                    $gallery_list[] = array(
                        "title" => $gallery_title,
                        "image" => $gallery_image
                    );
                }



                $resultpost[] = array(
                    'id' => $id,
                    'oldagehome_user_id' => $oldagehome_user_id,
                    'name' => $name,
                    'listing_type' => "18",
                    'about_us' => $about_us,
                    'establishment_year' => $establishment_year,
                    'address' => $address,
                    'phone' => $phone,
                    'state' => $state,
                    'city' => $city,
                    'pincode' => $pincode,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'image' => $image,
                    'service_offered' => $oldagehome_service_offered,
                    'gallery' => $gallery_list,
                    'rating' => $rating,
                    'followers' => $followers,
                    'following' => $following,
                    'profile_views' => $profile_views,
                    'reviews' => $reviews,
                    'is_follow' => $is_follow,
                    'user_discount' => $user_discount
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
            'oldagehome_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('oldagehome_review', $review_array);
        return array(
            'status' => 200,
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
           $this->db->update('oldagehome_review',$review_array);
        //$this->db->insert('fitness_center_review', $review_array);
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
        $review_count = $this->db->select('id')->from('oldagehome_review')->where('oldagehome_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT oldagehome_review.id,oldagehome_review.user_id,oldagehome_review.oldagehome_id,oldagehome_review.rating,oldagehome_review.review, oldagehome_review.service,oldagehome_review.date as review_date,users.id as user_id,users.name as firstname FROM `oldagehome_review` INNER JOIN `users` ON oldagehome_review.user_id=users.id WHERE oldagehome_review.oldagehome_id='$listing_id' order by oldagehome_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '11') {
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

                $like_count = $this->db->select('id')->from('oldagehome_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('oldagehome_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('oldagehome_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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
        $review_count = $this->db->select('id')->from('oldagehome_review')->where('oldagehome_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT oldagehome_review.id,oldagehome_review.user_id,oldagehome_review.oldagehome_id,oldagehome_review.rating,oldagehome_review.review, oldagehome_review.service,oldagehome_review.date as review_date,users.id as user_id,users.name as firstname FROM `oldagehome_review` INNER JOIN `users` ON oldagehome_review.user_id=users.id WHERE oldagehome_review.oldagehome_id='$listing_id' order by oldagehome_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
               /* if ($id > '11') {
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

                $like_count = $this->db->select('id')->from('oldagehome_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count = $this->db->select('id')->from('oldagehome_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('oldagehome_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

 $review_list_count = $this->db->select('id')->from('oldagehome_review_comment')->where('post_id', $id)->get()->num_rows();
        if ($review_list_count) {
             $resultcomment = array();
            $querycomment = $this->db->query("SELECT oldagehome_review_comment.id,oldagehome_review_comment.post_id,oldagehome_review_comment.comment as comment,oldagehome_review_comment.date,users.name,oldagehome_review_comment.user_id as post_user_id FROM oldagehome_review_comment INNER JOIN users on users.id=oldagehome_review_comment.user_id WHERE oldagehome_review_comment.post_id='$id' order by oldagehome_review_comment.id asc");

            foreach ($querycomment->result_array() as $row) {
                $comment_id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                /*if ($id > '11') {
                    $comment_decrypt = $this->decrypt($comment);
                    $comment_encrypt = $this->encrypt($comment_decrypt);
                    if ($comment_encrypt == $comment) {
                        $comment = $comment_decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($comment)) === $comment) {
                        $comment = base64_decode($comment);
                    }
                }*/
                $usernamec = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_countc = $this->db->select('id')->from('oldagehome_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $like_yes_noc = $this->db->select('id')->from('oldagehome_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
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
                    'id' => $comment_id,
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
                    'comments' => $resultcomment,
                    
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
        $count_query = $this->db->query("SELECT id from oldagehome_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `oldagehome_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from oldagehome_review_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $oldagehome_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('oldagehome_review_likes', $oldagehome_review_likes);
            $like_query = $this->db->query("SELECT id from oldagehome_review_likes WHERE post_id='$post_id'");
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

        $oldagehome_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('oldagehome_review_comment', $oldagehome_review_comment);
        $oldagehome_review_comment_query = $this->db->query("SELECT id from oldagehome_review_comment WHERE post_id='$post_id'");
        $total_comment = $oldagehome_review_comment_query->num_rows();
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
        $count_query = $this->db->query("SELECT id from oldagehome_review_comment_like WHERE comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `oldagehome_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from oldagehome_review_comment_like WHERE comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $oldagehome_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('oldagehome_review_comment_like', $oldagehome_review_comment_like);
            $comment_query = $this->db->query("SELECT id from oldagehome_review_comment_like WHERE comment_id='$comment_id'");
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

        $review_list_count = $this->db->select('id')->from('oldagehome_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT oldagehome_review_comment.id,oldagehome_review_comment.post_id,oldagehome_review_comment.comment as comment,oldagehome_review_comment.date,users.name,oldagehome_review_comment.user_id as post_user_id FROM oldagehome_review_comment INNER JOIN users on users.id=oldagehome_review_comment.user_id WHERE oldagehome_review_comment.post_id='$post_id' order by oldagehome_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '11') {
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

                $like_count = $this->db->select('id')->from('oldagehome_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('oldagehome_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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

}
