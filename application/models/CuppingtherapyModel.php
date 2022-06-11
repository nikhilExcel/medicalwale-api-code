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
                $profile_views = '2448';
                $reviews = '1500';
                $description = $row['description'];
                $image = $row['image'];
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $image;

                $gallery_query = $this->db->query("SELECT * FROM `cuppingtherapy_media` WHERE `cuppingtherapy_id`='$id'");
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



                $gallery_query = $this->db->query("SELECT GROUP_CONCAT(`title`) AS title, GROUP_CONCAT(CONCAT('https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/',source)) AS media FROM `cuppingtherapy_media`");
                if ($gallery_query) {
                    $row2 = $gallery_query->row();
                    $gallery_name = $row2->title;
                    $gallery = $row2->media;
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
                    "image" => $image
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function cuppingtherapy_packages($cuppingtherapy_id) {
        $query = $this->db->query("SELECT id,packages FROM `cuppingtherapy` WHERE id='$cuppingtherapy_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {

                $packages = $row['packages'];
                $cuppingtherapy_packages = $this->db->query("SELECT * FROM `cuppingtherapy_packages` WHERE FIND_IN_SET(package_name,'" . $packages . "')");

                foreach ($cuppingtherapy_packages->result_array() as $get_list) {
                    $package_name = $get_list['package_name'];
                    $package_details = $get_list['package_details'];
                    $price = $get_list['price'];
                    $image = $get_list['image'];
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/cuppingtherapy_images/' . $image;

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
        $review_count = $this->db->select('id')->from('cuppingtherapy_review')->where('cuppingtherapy_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT cuppingtherapy_review.id,cuppingtherapy_review.user_id,cuppingtherapy_review.cuppingtherapy_id,cuppingtherapy_review.rating,cuppingtherapy_review.review, cuppingtherapy_review.service,cuppingtherapy_review.date as review_date,users.id as user_id,users.name as firstname FROM `cuppingtherapy_review` INNER JOIN `users` ON cuppingtherapy_review.user_id=users.id WHERE cuppingtherapy_review.cuppingtherapy_id='$listing_id' order by cuppingtherapy_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $rating = $row['rating'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if (base64_encode(base64_decode($review)) === $review) {
                    $review = base64_decode($review);
                }
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);

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

        $review_list_count = $this->db->select('id')->from('cuppingtherapy_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT cuppingtherapy_review_comment.id,cuppingtherapy_review_comment.post_id,cuppingtherapy_review_comment.comment as comment,cuppingtherapy_review_comment.date,users.name,cuppingtherapy_review_comment.user_id as post_user_id FROM cuppingtherapy_review_comment INNER JOIN users on users.id=cuppingtherapy_review_comment.user_id WHERE cuppingtherapy_review_comment.post_id='$post_id' order by cuppingtherapy_review_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if (base64_encode(base64_decode($comment)) === $comment) {
                    $comment = base64_decode($comment);
                }
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('cuppingtherapy_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('cuppingtherapy_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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
            $resultpost = '';
        }



        return $resultpost;
    }

}
