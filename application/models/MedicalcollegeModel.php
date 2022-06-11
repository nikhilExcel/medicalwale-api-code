<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MedicalcollegeModel extends CI_Model { 
 
    var $client_service = "frontend-client";
    var $auth_key       = "medicalwalerestapi"; 

    public function check_auth_client(){
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key  = $this->input->get_request_header('Auth-Key', TRUE);
        if($client_service == $this->client_service && $auth_key == $this->auth_key){
            return true;
        } else {
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        }
    }

    public function auth()
    {
        date_default_timezone_set('Asia/Kolkata');
        $users_id  = $this->input->get_request_header('User-ID', TRUE);
        $token     = $this->input->get_request_header('Authorizations', TRUE);
        $q  = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id',$users_id)->where('token',$token)->get()->row();
        if($q == ""){
            return json_output(401,array('status' => 401,'message' => 'Unauthorized.'));
        } else {
            if($q->expired_at < date('Y-m-d H:i:s')){
                return json_output(401,array('status' => 401,'message' => 'Your session has been expired.'));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = '2018-11-12 08:57:58'; 
                $this->db->where('users_id',$users_id)->where('token',$token)->update('api_users_authentication',array('expired_at' => $expired_at,'updated_at' => $updated_at));
                return array('status' => 200,'message' => 'Authorized.');
            }
        }
    }
    
    public function college_cat_list()
    {
       return $this->db->select('id,cat_name')->from('medical_college_cat')->order_by('id', 'asc')->get()->result();
    }
    
    
    public function college_list($category_id)
    {
	
		$query = $this->db->query("SELECT id,user_id,college_name,cat_id,phone,address,banner,banner_source from medical_college_details where cat_id='$category_id'");
		 $count = $query->num_rows();
        if ($count > 0) {
	    
        foreach($query->result_array() as $row){
        $id=$row['id'];
        $cat_id=$row['cat_id']; 
        $listing_id=$row['user_id'];
		$college_name=$row['college_name'];		
		$phone=$row['phone'];
	    $address=$row['address'];
	    $banner=$row['banner'];
	    $banner_source=$row['banner_source'];
	    $ban_source='https://d2c8oti4is0ms3.cloudfront.net/images/medical_college_images/'.$banner_source;
        
        $resultpost[]=array(
			"id"=>$id,
			"listing_id"=>$listing_id,
			"listing_type"=>'11',
			"cat_id"=>$cat_id,
			"college_name"=>$college_name,			
			"phone"=>$phone,
			"address"=>$address,
			"banner"=>$banner,
			"ban_source"=>$ban_source
			);
        }
        
     }
     else {
            $resultpost = array();
        }
        return $resultpost;
    }
     

     public function college_details($college_id,$user_id)
    {
      
      	$query = $this->db->query("SELECT id,college_id,teacher_name,designation,qualification from medical_college_faculty where college_id='$college_id'");
        foreach($query->result_array() as $row){
        $id=$row['id'];
        $college_id=$row['college_id'];
		$teacher_name=$row['teacher_name'];		
		$designation=$row['designation'];
	    $qualification=$row['qualification'];
        
        $college_faculty[]=array(
			"id"=>$id,
			"college_id"=>$college_id,
			"teacher_name"=>$teacher_name,			
			"designation"=>$designation,
			"qualification"=>$qualification
			);
        }
      
       	$query = $this->db->query("SELECT id,college_id,course_name,type,duration from medical_college_courses where college_id='$college_id'");
        foreach($query->result_array() as $row){
        $id=$row['id'];
        $college_id=$row['college_id'];
		$course_name=$row['course_name'];		
		$type=$row['type'];
	    $duration=$row['duration'];
        
        $college_courses[]=array(
			"id"=>$id,
			"college_id"=>$college_id,
			"course_name"=>$course_name,			
			"type"=>$type,
			"duration"=>$duration
			);
        }
        
        $query = $this->db->query("SELECT * FROM `medical_college_details` WHERE `id`='$college_id'");
        foreach($query->result_array() as $row){
        $id=$row['id'];
        $cat_id=$row['cat_id'];
		$college_name=$row['college_name'];		
		$estabishment=$row['type'].' | '.$type=$row['estabishment'];;
		
	    $phone=$row['phone'];
	    $about=$row['about'];
	    $affiliation=$row['affiliation'];
	    $approved_by=$row['approved_by'];
	    $address=$row['address'];
	    $lat=$row['lat'];
	    $lng=$row['lng'];
	    $ammenties=$row['ammenties'];
	    $banner=$row['banner'];		
		$banner_source=$row['banner_source'];
		$ban_source='https://d2c8oti4is0ms3.cloudfront.net/images/medical_college_images/'.$banner_source;
        $brochure=$row['brochure'];
        $brochure_source='https://d2c8oti4is0ms3.cloudfront.net/images/medical_college_images/'.$brochure;
        $college_details[]=array(
			"id"=>$id,
			"cat_id"=>$cat_id,
			"college_name"=>$college_name,			
			"estabishment"=>$estabishment  ,
			"phone"=>$phone, 
			"about"=>$about,
			"affiliation"=>$affiliation,
			"approved_by"=>$approved_by,
			"address"=>$address,
			"lat"=>$lat,
			"lng"=>$lng,
			"ammenties"=>$ammenties,
			"banner"=>$banner,
			"ban_source"=>$ban_source,
			"brochure_source"=>$brochure_source,
			
			
			);
        }
		
		$query = $this->db->query("SELECT id,college_id,media,source FROM `medical_college_media` WHERE `college_id`='$college_id'");
        foreach($query->result_array() as $row){
        $id=$row['id'];
        $college_id=$row['college_id'];
		$media=$row['media'];		
		$source=$row['source'];
		$image_source='https://d2c8oti4is0ms3.cloudfront.net/images/medical_college_images/'.$source;
        
        $images[]=array(
			"id"=>$id,
			"college_id"=>$college_id,
			"media"=>$media,			
			"source"=>$image_source
			);
        }
        
        $result[] = array(
            'college_details' => $college_details,
            'course' => $college_courses,
            'faculty'  => $college_faculty,
            'images'  => $images,
        );
        
         return $result;
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
        $review_count = $this->db->select('id')->from('medical_college_review')->where('college_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT medical_college_review.id,medical_college_review.user_id,medical_college_review.college_id,medical_college_review.rating,medical_college_review.review, medical_college_review.service,medical_college_review.date as review_date,users.id as user_id,users.name as firstname FROM `medical_college_review` INNER JOIN `users` ON medical_college_review.user_id=users.id WHERE medical_college_review.college_id='$listing_id' order by medical_college_review.id desc");

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

                $like_count  = $this->db->select('id')->from('medical_college_review_like')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('medical_college_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('medical_college_review_like')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();

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
        $count_query = $this->db->query("SELECT id from medical_college_review_like where post_id='$post_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `medical_college_review_like` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from medical_college_review_like where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $medical_college_review_like = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('medical_college_review_like', $medical_college_review_like);
            $like_query = $this->db->query("SELECT id from medical_college_review_like where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function add_review($user_id, $listing_id, $rating, $review, $service)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date         = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'college_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('medical_college_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function review_comment($user_id, $post_id, $comment)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $medical_college_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('medical_college_review_comment', $medical_college_review_comment);
        $medical_college_review_comment_query = $this->db->query("SELECT id from medical_college_review_comment where post_id='$post_id'");
        $total_comment                   = $medical_college_review_comment_query->num_rows();
        return array(
            'status' => 201,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    public function review_comment_like($user_id, $comment_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from medical_college_review_comment_like where comment_id='$comment_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `medical_college_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from medical_college_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $medical_college_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('medical_college_review_comment_like', $medical_college_review_comment_like);
            $comment_query      = $this->db->query("SELECT id from medical_college_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 201,
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
        $review_list_count = $this->db->select('id')->from('medical_college_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT medical_college_review_comment.id,medical_college_review_comment.post_id,medical_college_review_comment.comment as comment,medical_college_review_comment.date,users.name,medical_college_review_comment.user_id as post_user_id FROM medical_college_review_comment INNER JOIN users on users.id=medical_college_review_comment.user_id WHERE medical_college_review_comment.post_id='$post_id' order by medical_college_review_comment.id asc");

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

                $like_count   = $this->db->select('id')->from('medical_college_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('medical_college_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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