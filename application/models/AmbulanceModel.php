<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AmbulanceModel extends CI_Model
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



    public function ambulance_details($user_id,$listing_id)
    {
		$radius = '5';

		 $query = $this->db->query("SELECT id,`user_id`, `name`, `address`, `phone`, `state`, `city`, `pincode`, `vehicle_in_services`, `ac_available`, `all_24hrs_available`, `establishment_year`, `image`, `list_of_equipment`, `lat`, `lng`, `reviews`, `ratings` FROM ambulance where user_id='$listing_id' limit 1");

         $count=$query->num_rows();
         if($count>0){
		 foreach ($query->result_array() as $row) {
            $id                 = $row['id'];
            $name         		= $row['name'];
            $address       		= $row['address'];
            $phone           	= $row['phone'];
            $state            	= $row['state'];
            $city	         	= $row['city'];
            $pincode      		= $row['pincode'];
            $vehicle_in_services= $row['vehicle_in_services'];
            $ac_available       = $row['ac_available'];
            $all_24hrs_available    = $row['all_24hrs_available'];
            $establishment_year  = $row['establishment_year'];
            $list_of_equipment2  = $row['list_of_equipment'];
            $image              = $row['image'];
            $lat              = $row['lat'];
            $lng              = $row['lng'];
			$list_of_equipment = array();
			if($image!='')
			{
			$image='https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/'.$image;
			}
			else
			{
			$image='';
			}

            $ambulance_user_id = $row['user_id'];
            $rating             = $row['ratings'];
            $profile_views      = '441';
            $reviews            = $row['reviews'];


            $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $listing_id)->get()->num_rows();
            $following = $this->db->select('id')->from('follow_user')->where('user_id', $listing_id)->get()->num_rows();
            $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $listing_id)->get()->num_rows();

            if ($is_follow > 0) {
                $is_follow = 'Yes';
            } else {
                $is_follow = 'No';
            }



	   $list_of_equipment_query = $this->db->query("SELECT `name` FROM `ambulance_equipment_list` WHERE FIND_IN_SET(name,'".$list_of_equipment2."')");
        foreach($list_of_equipment_query->result_array() as $get_list){
        $name=$get_list['name'];
        $list_of_equipment[]=array(
			"equipment_name"=>$name
			);
        }

            $resultpost[] = array(
                'id' => $id,
                'ambulance_user_id' => $ambulance_user_id,
                'name' => $name,
                'address' => $address,
                'phone' => $phone,
                'state' => $state,
                'city' => $city,
                'pincode' => $pincode,
                'latitude' => $lat,
                'longitude' => $lng,
                'vehicle_in_services' => $vehicle_in_services,
                'ac_available' => $ac_available,
                '24hrs_available' => $all_24hrs_available,
                'establishment_year' => $establishment_year,
                'image' => $image,
                'list_of_equipment' => $list_of_equipment,
                'rating' => $rating,
                'followers' => $followers,
                'following' => $following,
                'profile_views' => $profile_views,
                'reviews' => $reviews,
                'is_follow' => $is_follow
            );
        }
		 }
		 else
		 {
		 $resultpost = array();
		 }


        return $resultpost;
    }



    public function ambulance_list($user_id,$latitude,$longitude)
    {
		$radius = '5';

		 $query = $this->db->query("SELECT id,`user_id`, `name`, `address`, `phone`, `state`, `city`, `pincode`, `vehicle_in_services`, `ac_available`, `all_24hrs_available`, `establishment_year`, `image`, `list_of_equipment`, `lat`, `lng`, `reviews`, `ratings`,( 6371 * acos( cos( radians($latitude) ) * cos( radians( lat ) ) * cos( radians( lng ) - radians($longitude) ) + sin( radians($latitude) ) * sin( radians( lat ) ) ) ) AS distance FROM ambulance HAVING distance <= '$radius' order by id ASC");

         $count=$query->num_rows();
         if($count>0){
		 foreach ($query->result_array() as $row) {
            $id                 = $row['id'];
            $name         		= $row['name'];
            $address       		= $row['address'];
            $phone           	= $row['phone'];
            $state            	= $row['state'];
            $city	         	= $row['city'];
            $pincode      		= $row['pincode'];
            $vehicle_in_services= $row['vehicle_in_services'];
            $ac_available       = $row['ac_available'];
            $all_24hrs_available    = $row['all_24hrs_available'];
            $establishment_year  = $row['establishment_year'];
            $list_of_equipment2  = $row['list_of_equipment'];
            $image              = $row['image'];
            $lat              = $row['lat'];
            $lng              = $row['lng'];
			$list_of_equipment = array();
			if($image!='')
			{
			$image='https://d2c8oti4is0ms3.cloudfront.net/images/ambulance_images/'.$image;
			}
			else
			{
			$image='';
			}

            $ambulance_user_id = $row['user_id'];
            $rating             = $row['ratings'];
            $profile_views      = '441';
            $reviews            = $row['reviews'];


            $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $ambulance_user_id)->get()->num_rows();
            $following = $this->db->select('id')->from('follow_user')->where('user_id', $ambulance_user_id)->get()->num_rows();
            $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $ambulance_user_id)->where('parent_id', $ambulance_user_id)->get()->num_rows();

            if ($is_follow > 0) {
                $is_follow = 'Yes';
            } else {
                $is_follow = 'No';
            }



	   $list_of_equipment_query = $this->db->query("SELECT `name` FROM `ambulance_equipment_list` WHERE FIND_IN_SET(name,'".$list_of_equipment2."')");
        foreach($list_of_equipment_query->result_array() as $get_list){
        $name=$get_list['name'];
        $list_of_equipment[]=array(
			"equipment_name"=>$name
			);
        }

            $resultpost[] = array(
                'id' => $id,
                'ambulance_user_id' => $ambulance_user_id,
                'name' => $name,
                'listing_type' => "21",
                'address' => $address,
                'phone' => $phone,
                'state' => $state,
                'city' => $city,
                'pincode' => $pincode,
                'latitude' => $lat,
                'longitude' => $lng,
                'vehicle_in_services' => $vehicle_in_services,
                'ac_available' => $ac_available,
                '24hrs_available' => $all_24hrs_available,
                'establishment_year' => $establishment_year,
                'image' => $image,
                'list_of_equipment' => $list_of_equipment,
                'rating' => $rating,
                'followers' => $followers,
                'following' => $following,
                'profile_views' => $profile_views,
                'reviews' => $reviews,
                'is_follow' => $is_follow
            );
        }
		 }
		 else
		 {
		 $resultpost = array();
		 }


        return $resultpost;
    }



  public function add_review($user_id, $listing_id, $rating, $review, $service)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date         = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'ambulance_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('ambulance_review', $review_array);
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
        $resultpost   = '';
        $review_count = $this->db->select('id')->from('ambulance_review')->where('ambulance_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT ambulance_review.id,ambulance_review.user_id,ambulance_review.ambulance_id,ambulance_review.rating,ambulance_review.review, ambulance_review.service,ambulance_review.date as review_date,users.id as user_id,users.name as firstname FROM `ambulance_review` INNER JOIN `users` ON ambulance_review.user_id=users.id WHERE ambulance_review.ambulance_id='$listing_id' order by ambulance_review.id desc");

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

                $like_count  = $this->db->select('id')->from('ambulance_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('ambulance_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('ambulance_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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
        $count_query = $this->db->query("SELECT id from ambulance_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `ambulance_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from ambulance_review_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $ambulance_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('ambulance_review_likes', $ambulance_review_likes);
            $like_query = $this->db->query("SELECT id from ambulance_review_likes WHERE post_id='$post_id'");
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

        $ambulance_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('ambulance_review_comment', $ambulance_review_comment);
        $ambulance_review_comment_query = $this->db->query("SELECT id from ambulance_review_comment WHERE post_id='$post_id'");
        $total_comment = $ambulance_review_comment_query->num_rows();
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
        $count_query = $this->db->query("SELECT id from ambulance_review_comment_like WHERE comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `ambulance_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from ambulance_review_comment_like WHERE comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment_like' => $total_comment
            );
        } else {
            $ambulance_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('ambulance_review_comment_like', $ambulance_review_comment_like);
            $comment_query      = $this->db->query("SELECT id from ambulance_review_comment_like WHERE comment_id='$comment_id'");
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
        $review_list_count = $this->db->select('id')->from('ambulance_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT ambulance_review_comment.id,ambulance_review_comment.post_id,ambulance_review_comment.comment as comment,ambulance_review_comment.date,users.name,ambulance_review_comment.user_id as post_user_id FROM ambulance_review_comment INNER JOIN users on users.id=ambulance_review_comment.user_id WHERE ambulance_review_comment.post_id='$post_id' order by ambulance_review_comment.id asc");

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

                $like_count   = $this->db->select('id')->from('ambulance_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('ambulance_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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
