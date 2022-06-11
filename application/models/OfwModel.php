<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OfwModel extends CI_Model
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
    
    
        public function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        if (!defined("GOOGLE_GCM_URL"))
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields  = array(
            'to' => $reg_id,
            'priority' => "high",
            $agent === 'android' ? 'data' : 'notification' => array(
                "title" => $title,
                "message" => $msg,
                "notification_image" => $img_url,
                "tag" => $tag,
                'sound' => 'default',
                "notification_type" => 'saheli_notifications',
                "notification_date" => $date,
                "post_id" => $post_id
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
        $ch      = curl_init();
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
    
/*    function base64Detect($str) {
       if ($str) {
        $check = str_split(base64_decode($str));
        $x = 0;
        foreach ($check as $char) if (ord($char) > 126) $x++;
        if ($x/count($check)*100 < 30) return base64_decode($str);
    }
  
    return $str;
}
   */
    
    
    public function home_remedies($user_id, $category_id)
    {
        $query = $this->db->query("SELECT id, category_id, title, details, image, date FROM `home_remedies` where is_active='1' AND category_id='$category_id' order by id asc");
        
        foreach ($query->result_array() as $row) {
            $id          = $row['id'];
            $category_id = $row['category_id'];
            $title       = $row['title'];
            $details     = $row['details'];
            $image       = $row['image'];
            $date        = $row['date'];
            $image       = str_replace(" ", "", $image);
            $image       = 'https://d2c8oti4is0ms3.cloudfront.net/images/ofw_images/home_remedies/' . $image;
            
            $like_count  = $this->db->select('id')->from('home_remedies_likes')->where('home_remedies_id', $id)->get()->num_rows();
            $like_yes_no = $this->db->select('id')->from('home_remedies_likes')->where('user_id', $user_id)->where('home_remedies_id', $id)->get()->num_rows();
            $view_count  = $this->db->select('id')->from('home_remedies_views')->where('home_remedies_id', $id)->get()->num_rows();
            
            $bookmark_yes_no = $this->db->select('id')->from('home_remedies_bookmark')->where('user_id', $user_id)->where('home_remedies_id', $id)->get()->num_rows();
            
            $share = 'https://medicalwale.com/images/only_for_women/home_remedies/home%20remedies%20details.php?id=' . $id;
            
            $resultpost[] = array(
                "id" => $id,
                "title" => $title,
                'category_id' => $category_id,
                'description' => $details,
                'image' => $image,
                'date' => $date,
                'total_like' => $like_count,
                'views' => $view_count,
                'is_like' => $like_yes_no,
                'is_bookmark' => $bookmark_yes_no,
                'share' => $share
            );
        }
        return $resultpost;
    }
    
    public function home_remedies_likes($user_id, $home_remedies_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from home_remedies_likes where home_remedies_id='$home_remedies_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        
        if ($count > 0) {
            $this->db->query("DELETE FROM `home_remedies_likes` WHERE user_id='$user_id' and home_remedies_id='$home_remedies_id'");
            $like_query = $this->db->query("SELECT id from home_remedies_likes where home_remedies_id='$home_remedies_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $home_remedies_likes = array(
                'user_id' => $user_id,
                'home_remedies_id' => $home_remedies_id
            );
            $this->db->insert('home_remedies_likes', $home_remedies_likes);
            $like_query = $this->db->query("SELECT id from home_remedies_likes where home_remedies_id='$home_remedies_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }
    
    public function home_remedies_bookmark($user_id, $home_remedies_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from home_remedies_bookmark where home_remedies_id='$home_remedies_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        
        if ($count > 0) {
            $this->db->query("DELETE FROM `home_remedies_bookmark` WHERE user_id='$user_id' and home_remedies_id='$home_remedies_id'");
            $like_query = $this->db->query("SELECT id from home_remedies_bookmark where home_remedies_id='$home_remedies_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'bookmark' => '0'
            );
        } else {
            $home_remedies_bookmark = array(
                'user_id' => $user_id,
                'home_remedies_id' => $home_remedies_id
            );
            $this->db->insert('home_remedies_bookmark', $home_remedies_bookmark);
            $like_query = $this->db->query("SELECT id from home_remedies_bookmark where home_remedies_id='$home_remedies_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'bookmark' => '1'
            );
        }
    }
    
    
    public function ask_saheli_character()
    {
        $query = $this->db->query("SELECT id,image FROM `ask_saheli_character` WHERE id<>'10' order by id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id           = $row['id'];
                $image        = $row['image'];
                $image        = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/character/' . $image;
                $resultpost[] = array(
                    'id' => $id,
                    'image' => $image
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
    public function ask_saheli_post_category()
    {
        $query = $this->db->query("SELECT id,category FROM `asksaheli_main_category` order by id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id       = $row['id'];
                $category = $row['category'];   
                $image = '';
                
                $resultpost[] = array(
                     "id" => $id, 
                     "category" => $category, 
                     "image" => $image
                    );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
    public function ask_saheli_post_list($user_id,$saheli_category, $page)
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
                return intval($time_differnce / $years) . " yrs";
            } else if (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . " yr";
            } else if (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . " months";
            } else if (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . " month";
            } else if (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . " days";
            } else if (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . " day";
            } else if (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . " hrs";
            } else if (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . " hr";
            } else if (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . " min";
            } else if (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . " min";
            } else if (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . " sec";
            } else {
                return "few seconds";
            }
        }
        $resultpost = array();
        $limit      = 10;
        $start      = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
		
        $start = ($page - 1) * $limit;
	
       if($saheli_category==0){
        $query = $this->db->query("select IFNULL(asksaheli_main_category.id,'') AS saheli_category_id,IFNULL(asksaheli_main_category.category,'') AS saheli_category, ask_saheli_post.id ,ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location,ask_saheli_post.date,ask_saheli_post.post,ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,user_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post INNER JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category INNER JOIN user_character on user_character.id=ask_saheli_post.user_image LEFT JOIN asksaheli_main_category ON ask_saheli_post.saheli_category=asksaheli_main_category.id  WHERE ask_saheli_post.saheli_category<>'0' AND ask_saheli_post.id NOT IN (SELECT post_id FROM ask_saheli_post_hide WHERE post_id=ask_saheli_post.id AND user_id='$user_id') order by ask_saheli_post.id desc limit $start, $limit");        
        $count_query = $this->db->query("select IFNULL(asksaheli_main_category.id,'') AS saheli_category_id,IFNULL(asksaheli_main_category.category,'') AS saheli_category, ask_saheli_post.id ,ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location,ask_saheli_post.date,ask_saheli_post.post,ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,user_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post INNER JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category INNER JOIN user_character on user_character.id=ask_saheli_post.user_image LEFT JOIN asksaheli_main_category ON ask_saheli_post.saheli_category=asksaheli_main_category.id  WHERE ask_saheli_post.saheli_category<>'0' AND ask_saheli_post.id NOT IN (SELECT post_id FROM ask_saheli_post_hide WHERE post_id=ask_saheli_post.id AND user_id='$user_id') order by ask_saheli_post.id desc");
		}
		else{
		
		
		
        $query = $this->db->query("select IFNULL(asksaheli_main_category.id,'') AS saheli_category_id,IFNULL(asksaheli_main_category.category,'') AS saheli_category, ask_saheli_post.id ,ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location,ask_saheli_post.date,ask_saheli_post.post,ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,user_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post INNER JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category INNER JOIN user_character on user_character.id=ask_saheli_post.user_image LEFT JOIN asksaheli_main_category ON ask_saheli_post.saheli_category=asksaheli_main_category.id  WHERE ask_saheli_post.saheli_category<>'0' AND ask_saheli_post.saheli_category IN ($saheli_category) AND ask_saheli_post.id NOT IN (SELECT post_id FROM ask_saheli_post_hide WHERE post_id=ask_saheli_post.id AND user_id='$user_id') order by ask_saheli_post.id desc limit $start, $limit");        
        $count_query = $this->db->query("select IFNULL(asksaheli_main_category.id,'') AS saheli_category_id,IFNULL(asksaheli_main_category.category,'') AS saheli_category, ask_saheli_post.id ,ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location,ask_saheli_post.date,ask_saheli_post.post,ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,user_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post INNER JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category INNER JOIN user_character on user_character.id=ask_saheli_post.user_image LEFT JOIN asksaheli_main_category ON ask_saheli_post.saheli_category=asksaheli_main_category.id  WHERE ask_saheli_post.saheli_category<>'0' AND ask_saheli_post.saheli_category IN ($saheli_category) AND ask_saheli_post.id NOT IN (SELECT post_id FROM ask_saheli_post_hide WHERE post_id=ask_saheli_post.id AND user_id='$user_id') order by ask_saheli_post.id desc");
		}
        $count_post  = $count_query->num_rows();
        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $id              = $row['id']; 
                $post_id         = $row['id'];
                $post            = $row['post']; 
                 $post = preg_replace('~[\r\n]+~', '', $post);
                if(base64_encode(base64_decode($post)) === $post){
                    $post=base64_decode($post);
                }
                 $post_location     = $row['post_location'];
                $user_name       	= $row['user_name']; 
                $post_user_id      	= $row['post_user_id'];
                $tag             	= $row['tag'];
                $category        	= $row['category'];
                $character_image 	= $row['character_image'];
                $type            	= $row['type'];  
                $article_title   	= $row['article_title'];  
                $article_image   	= $row['article_image'];   
                $article_domain_name= $row['article_domain_name'];    
                $article_url        = $row['article_url'];
                $saheli_category    = $row['saheli_category'];
                $saheli_category_id = $row['saheli_category_id'];
               
                $views           = 0;
                $video_views     = 0;
              
                $query_media = $this->db->query("SELECT ask_saheli_post_media.id AS media_id,ask_saheli_post_media.source,ask_saheli_post_media.type AS media_type,IFNULL(ask_saheli_post_media.caption,'') AS caption,ask_saheli_post_media.img_width,ask_saheli_post_media.img_height,ask_saheli_post_media.video_width,ask_saheli_post_media.video_height FROM ask_saheli_post_media INNER JOIN ask_saheli_post on ask_saheli_post_media.post_id=ask_saheli_post.id WHERE ask_saheli_post_media.post_id='$id'");
                $img_val      = '';
                $images       = '';
                $img_comma    = '';
                $img_width    = '';
                $img_height   = '';
                $video_width  = '';
                $video_height = '';
				
                $media_array = array();
                
                foreach ($query_media->result_array() as $media_row) {
                    $media_id     = $media_row['media_id'];
                    $media_type   = $media_row['media_type'];
                    $source       = $media_row['source'];
                    $images       = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/'.$media_type.'/'.$source;   
					if($media_type=='video'){
                       $thumb = 'https://s3.amazonaws.com/medicalwale-thumbnails/videothumbnail/images/ask_saheli_images/video/'.substr_replace($source, '_thumbnail.jpeg', strrpos($source, '.') + 0);
                    }else{ $thumb=''; }
                    
                    
                    $caption      = $media_row['caption'];
                    $img_width    = $media_row['img_width'];
                    $img_height   = $media_row['img_height'];
                    $video_width  = $media_row['video_width'];
                    $video_height = $media_row['video_height'];
                    
                    $view_media_count = $this->db->select('id')->from('ask_saheli_video_views')->where('media_id', $media_id)->get()->num_rows();
                    
                    $view_media_yes_no_query = $this->db->query("SELECT id from ask_saheli_video_views WHERE media_id='$media_id' and user_id='$user_id'");
                    $view_media_yes_no       = $view_media_yes_no_query->num_rows();
                  
                    
                    $media_array[] = array(
                        'media_id' => $media_id,
                        'type' => $media_type,
                        'images' => str_replace(' ','%20',$images), 
						'thumb' => $thumb,
                        'caption' => $caption,
                        'img_height' => $img_height,
                        'img_width' => $img_width,
                        'video_height' => $video_height,
                        'video_width' => $video_width,
                        'video_views' => $view_media_count,
                        'video_view_yes_no' => $view_media_yes_no
                        
                    );
                }
                
                
                $date = $row['date'];
                $date = get_time_difference_php($date);
                
                $like_count      = $this->db->select('id')->from('ask_saheli_likes')->where('post_id', $id)->get()->num_rows();
                $comment_count   = $this->db->select('id')->from('ask_saheli_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no     = $this->db->select('id')->from('ask_saheli_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                $character_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $character_image;
                
                
                $view_count = $this->db->select('id')->from('ask_saheli_post_views')->where('post_id', $id)->get()->num_rows();
                
                $view_yes_no_query = $this->db->query("SELECT id from ask_saheli_post_views WHERE post_id='$id' and user_id='$user_id'");
                $view_post_yes_no  = $view_yes_no_query->num_rows();
                
                $follow_count = $this->db->select('id')->from('ask_saheli_follow_post')->where('post_id', $id)->get()->num_rows();
                
                $follow_yes_no_query = $this->db->query("SELECT id from ask_saheli_follow_post where post_id='$id' and user_id='$user_id'");
                $follow_post_yes_no  = $follow_yes_no_query->num_rows();
                $share_url           = "https://medicalwale.com/share/asksaheli/".$id;
                $is_reported_query   = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='asksaheli'");
                $is_reported    = $is_reported_query->num_rows();
                
                $is_post_save_query   = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='asksaheli'");
                $is_post_save    = $is_post_save_query->num_rows();
                
                  
			//comments
				  
			$query_comment = $this->db->query("SELECT ask_saheli_comment.id,ask_saheli_comment.comment,ask_saheli_comment.date,ask_saheli_comment.user_name,user_character.image as character_image,ask_saheli_comment.user_id as post_user_id  FROM ask_saheli_comment LEFT JOIN  user_character on user_character.id=ask_saheli_comment.user_image WHERE ask_saheli_comment.post_id='$post_id' order by ask_saheli_comment.id desc LIMIT 0,3");
            
			$comment_counts = $query_comment->num_rows(); 
			if ($comment_counts > 0) {
			$comments = array();
            foreach ($query_comment->result_array() as $rows) {
                $comment_id           = $rows['id'];
                $comment      = $rows['comment']; 
            
                  $comment = preg_replace('~[\r\n]+~', '', $comment);
                if(base64_encode(base64_decode($comment)) === $comment){
                    $comment=base64_decode($comment);
                }
                $comment_username     = $rows['user_name'];
                $comment_image        = $rows['character_image'];
                $comment_date         = $rows['date'];
                $comment_post_user_id = $rows['post_user_id'];
                $comment_date = get_time_difference_php($comment_date);
                $comment_image        = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $comment_image;
                
                $comment_like_count  = $this->db->select('id')->from('ask_saheli_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $comment_like_yes_no = $this->db->select('id')->from('ask_saheli_comment_like')->where('user_id', $user_id)->where('comment_id', $comment_id)->get()->num_rows();
                
                $comments[] = array(
                    'id' => $comment_id,  
                    'comment_user_id' => $comment_post_user_id,
                    'username' => $comment_username,
                    'image' => $comment_image,
                    'like_count' => $comment_like_count,
                    'like_yes_no' => $comment_like_yes_no,
                    'comment' => $comment,
                    'comment_date' => $comment_date
					);
                
				}
			} 
			else{
				$comments = array();
			}
			
			//comments
			
				  
                $resultpost[] = array(
                    'id' => $id,
                    'post_type' => $type, 
                    'post_location'=> $post_location,
                    'saheli_category'=> $saheli_category,
                    'saheli_category_id'=> $saheli_category_id,
                    'post_user_id' => $post_user_id,
                    'username' => $user_name,
                    'post' => $post,
                    'article_title' => str_replace('null', '', $article_title),
                    'article_image' => str_replace('null', '', $article_image),
                    'article_domain_name' => str_replace('null', '', $article_domain_name),
                    'article_url' => str_replace('null', '', $article_url),
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $comment_count,
                    'follow_count' => $follow_count,
                    'follow_post_yes_no' => $follow_post_yes_no,
                    'views' => $view_count,
                    'view_yes_no' => $view_post_yes_no,
                    'media' => $media_array,
                    'share_url' => $share_url,
                    'category' => $category,
                    'character_image' => $character_image,
                    'date' => $date,
                    'is_reported' => $is_reported, 
                    'is_post_save' => $is_post_save,
                    'comments' => $comments
                );
            }
        } else {
            $resultpost = array();
        }
        
        $result_post = array(
            "status" => 200,
            "message" => "success",
            "count" => $count_post,
            "data" => $resultpost
        );
        return $result_post;
    }
    
    
    
     
    public function ask_saheli_post_details($user_id,$post_id)
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
        $resultpost = array();

        $query = $this->db->query("select IFNULL(asksaheli_main_category.id,'') AS saheli_category_id,IFNULL(asksaheli_main_category.category,'') AS saheli_category, ask_saheli_post.id ,ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location,ask_saheli_post.date,ask_saheli_post.post,ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,user_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post INNER JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category INNER JOIN user_character on user_character.id=ask_saheli_post.user_image LEFT JOIN asksaheli_main_category ON ask_saheli_post.saheli_category=asksaheli_main_category.id  WHERE ask_saheli_post.id='$post_id' order by ask_saheli_post.id desc");
		
		
        $count_post  = $query->num_rows();
        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $id              = $row['id'];
                $post            = $row['post']; 
				$saheli_category    = $row['saheli_category'];
                $saheli_category_id = $row['saheli_category_id']; 
              $post = preg_replace('~[\r\n]+~', '', $post);
                if(base64_encode(base64_decode($post)) === $post){
                    $post=base64_decode($post);
                }
                 $post_location                = $row['post_location'];
                $user_name       = $row['user_name']; 
                $post_user_id      = $row['user_id'];
                $tag             = $row['tag'];
                $category        = $row['category'];
                $character_image = $row['character_image'];
                $type            = $row['type'];  
                $article_title   = $row['article_title'];  
                $article_image   = $row['article_image'];   
                $article_domain_name= $row['article_domain_name'];  
                $article_url        = $row['article_url'];
                $views           = 0;
                $video_views     = 0;
              
                $query_media = $this->db->query("SELECT ask_saheli_post_media.id AS media_id,ask_saheli_post_media.source,ask_saheli_post_media.type AS media_type,IFNULL(ask_saheli_post_media.caption,'') AS caption,ask_saheli_post_media.img_width,ask_saheli_post_media.img_height,ask_saheli_post_media.video_width,ask_saheli_post_media.video_height FROM ask_saheli_post_media INNER JOIN ask_saheli_post on ask_saheli_post_media.post_id=ask_saheli_post.id WHERE ask_saheli_post_media.post_id='$id'");
                $img_val      = '';
                $images       = '';
                $img_comma    = '';
                $img_width    = '';
                $img_height   = '';
                $video_width  = '';
                $video_height = '';
				
                $media_array = array();
                
                foreach ($query_media->result_array() as $media_row) {
                    $media_id     = $media_row['media_id'];
                    $media_type   = $media_row['media_type'];
                    $source       = $media_row['source'];
                    $images       = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/'.$media_type.'/'.$source;   
					if($media_type=='video'){
                        $thumb       = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/thumb/'.substr_replace($source,'jpg',strrpos($source,'.')+1);
                    }else{ $thumb=''; }
                    $caption      = $media_row['caption'];
                    $img_width    = $media_row['img_width'];
                    $img_height   = $media_row['img_height'];
                    $video_width  = $media_row['video_width'];
                    $video_height = $media_row['video_height'];
                    
                    $view_media_count = $this->db->select('id')->from('ask_saheli_video_views')->where('media_id', $media_id)->get()->num_rows();
                    
                    $view_media_yes_no_query = $this->db->query("SELECT id from ask_saheli_video_views WHERE media_id='$media_id' and user_id='$user_id'");
                    $view_media_yes_no       = $view_media_yes_no_query->num_rows();
                    
                    $media_array[] = array(
                        'media_id' => $media_id,
                        'type' => $media_type,
                        'images' => str_replace(' ','%20',$images), 
						'thumb' => $thumb,
                        'caption' => $caption,
                        'img_height' => $img_height,
                        'img_width' => $img_width,
                        'video_height' => $video_height,
                        'video_width' => $video_width,
                        'video_views' => $view_media_count,
                        'video_view_yes_no' => $view_media_yes_no
                        
                    );
                }
                
                
                $date = $row['date'];
                $date = get_time_difference_php($date);
                
                $like_count      = $this->db->select('id')->from('ask_saheli_likes')->where('post_id', $id)->get()->num_rows();
                $comment_count   = $this->db->select('id')->from('ask_saheli_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no     = $this->db->select('id')->from('ask_saheli_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                $character_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $character_image;
                
                
                $view_count = $this->db->select('id')->from('ask_saheli_post_views')->where('post_id', $id)->get()->num_rows();
                
                $view_yes_no_query = $this->db->query("SELECT id from ask_saheli_post_views WHERE post_id='$id' and user_id='$user_id'");
                $view_post_yes_no  = $view_yes_no_query->num_rows();
                
                $follow_count = $this->db->select('id')->from('ask_saheli_follow_post')->where('post_id', $id)->get()->num_rows();
                
                $follow_yes_no_query = $this->db->query("SELECT id from ask_saheli_follow_post where post_id='$id' and user_id='$user_id'");
                $follow_post_yes_no  = $follow_yes_no_query->num_rows();
               $share_url           = "https://medicalwale.com/share/asksaheli/".$id;
                
                $is_reported_query   = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='asksaheli'");
                $is_reported    = $is_reported_query->num_rows();   
                $is_post_save_query   = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='asksaheli'");
                $is_post_save    = $is_post_save_query->num_rows();

                  
			//comments
				  
			$query_comment = $this->db->query("SELECT ask_saheli_comment.id,ask_saheli_comment.comment,ask_saheli_comment.date,ask_saheli_comment.user_name,user_character.image as character_image,ask_saheli_comment.user_id as post_user_id  FROM ask_saheli_comment LEFT JOIN  user_character on user_character.id=ask_saheli_comment.user_image WHERE ask_saheli_comment.post_id='$post_id' order by ask_saheli_comment.id desc LIMIT 0,3");
            
			$comment_counts = $query_comment->num_rows(); 
			if ($comment_counts > 0) {
			$comments = array();
            foreach ($query_comment->result_array() as $rows) {
                $comment_id           = $rows['id'];
                $comment      = $rows['comment']; 
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if(base64_encode(base64_decode($comment)) === $comment){
                    $comment=base64_decode($comment);
                }
                $comment_username     = $rows['user_name'];
                $comment_image        = $rows['character_image'];
                $comment_date         = $rows['date'];
                $comment_post_user_id = $rows['post_user_id'];
                $comment_date = get_time_difference_php($comment_date);
                $comment_image        = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $comment_image;
                
                $comment_like_count  = $this->db->select('id')->from('ask_saheli_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $comment_like_yes_no = $this->db->select('id')->from('ask_saheli_comment_like')->where('user_id', $user_id)->where('comment_id', $comment_id)->get()->num_rows();
                
                $comments[] = array(
                    'id' => $comment_id,  
                    'comment_user_id' => $comment_post_user_id,
                    'username' => $comment_username,
                    'image' => $comment_image,
                    'like_count' => $comment_like_count,
                    'like_yes_no' => $comment_like_yes_no,
                    'comment' => $comment,
                    'comment_date' => $comment_date
					);
                
				}
			} 
			else{
				$comments = array();
			}
			
			//comments	
    
			
                $resultpost[] = array(
                    'id' => $id,
                    'post_type' => $type, 
                    'post_user_id' => $post_user_id,
                    'post_location'=> $post_location,
                    'saheli_category'=> $saheli_category,
                    'saheli_category_id'=> $saheli_category_id,
                    'username' => $user_name,
                    'post' => $post,
                     'article_title' => str_replace('null', '', $article_title),
                    'article_image' => str_replace('null', '', $article_image),
                    'article_domain_name' => str_replace('null', '', $article_domain_name),
                    'article_url' => str_replace('null', '', $article_url),
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $comment_count,
                    'follow_count' => $follow_count,
                    'follow_post_yes_no' => $follow_post_yes_no,
                    'views' => $view_count,
                    'view_yes_no' => $view_post_yes_no,
                    'media' => $media_array,
                    'share_url' => $share_url,
                    'category' => $category,
                    'character_image' => $character_image,
                    'date' => $date, 
                    'is_reported' => $is_reported, 
                    'is_post_save' => $is_post_save,
                    'comments' => $comments
                );
            }
        } else {
            $resultpost = array();
        }
        
        $result_post = array(
            "status" => 200,
            "message" => "success",
            "count" => $count_post,
            "data" => $resultpost
        );
        return $result_post;
    }
     
    
    
    
    public function ask_saheli_post_like($user_id, $post_id, $user_name, $user_image,$post_user_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT * FROM `ask_saheli_likes` WHERE user_id='$user_id' AND post_id='$post_id'");
        $count       = $count_query->num_rows();
        
        if ($count > 0) {
            $this->db->query("DELETE FROM `ask_saheli_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from `ask_saheli_likes` WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $ask_saheli_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'user_name' => $user_name,
                'user_image' => $user_image
            );
            $this->db->insert('ask_saheli_likes', $ask_saheli_likes);
            
            
            //$user_plike     = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            
           if($user_name=='0' || $user_name=='') 
           {
             $user_name='Someone'; 
            }
            
            if($user_image=='0')
            {
             $userimage='https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/ofw/ask_saheli.png';   
            }
            else
            {
            $img_query = $this->db->query("select user_character.image as character_image FROM ask_saheli_likes INNER JOIN user_character on user_character.id=ask_saheli_likes.user_image  WHERE  ask_saheli_likes.user_id='$user_id' AND ask_saheli_likes.post_id='$post_id'"); 
            $getimg = $img_query->row_array();
            $character_image = $getimg['character_image']; 
            $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $character_image;  
            }
            
            
            $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id='$post_user_id' AND id<>'$user_id'");
            
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $usr_name     = $user_name;
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $title        = $usr_name.' Beats on your Post';
                $msg          = $usr_name.' Beats on your post click here to view post.';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
            }
            
            $like_query = $this->db->query("SELECT id from `ask_saheli_likes` WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }
    
    
    public function ask_saheli_post_comment($user_id, $post_id, $comment, $user_name, $user_image, $post_user_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        
        $ask_saheli_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'user_name' => $user_name,
            'user_image' => $user_image,
            'date' => $created_at
        );
        $this->db->insert('ask_saheli_comment', $ask_saheli_comment);
        
                    
            if($user_name=='0' || $user_name=='') 
            {
             $user_name='Someone'; 
            }
            
            if($user_image=='0')
            {
             $userimage='https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/ofw/ask_saheli.png';   
            }
            else
            {
            $img_query = $this->db->query("select user_character.image as character_image FROM ask_saheli_comment INNER JOIN user_character on user_character.id=ask_saheli_comment.user_image  WHERE  ask_saheli_comment.user_id='$user_id' AND ask_saheli_comment.post_id='$post_id'"); 
            $getimg = $img_query->row_array();
            $character_image = $getimg['character_image']; 
            $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $character_image;  
            }
        
                
        $post_follows     = $this->db->query("SELECT  GROUP_CONCAT(user_id) AS follow_users FROM ask_saheli_follow_post WHERE post_id='$post_id' AND user_id<>'$post_user_id'");
        $get_post_follows = $post_follows->row_array();
        $follow_users     = $get_post_follows['follow_users'];
        $follow_user_ids  = explode(',', $follow_users);

        
        //follow post users notifications
        foreach ($follow_user_ids as $follow_user_ids_array) {
            //$user_pcommnent       = $this->db->query("SELECT name FROM users WHERE id='$user_id'");	
            
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id='$follow_user_ids_array' AND id<>'$user_id'");
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $title        =  $user_name.' commented on post that you have followed';
                $msg          =  $user_name.' commented on post that you have followed click here to view post.';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
            }
        }
        
        
        
        //post users notifications
        //$user_pcommnent2       = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
		
        $customer_token2       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id='$post_user_id' AND $post_user_id<>$user_id");
        $customer_token_count2 = $customer_token2->num_rows();
        if ($customer_token_count2 > 0) {
            $token_status2 = $customer_token2->row_array();
            //$getusr2       = $user_pcommnent2->row_array();
            //$usr_name      = $getusr2['name'];
            $agent         = $token_status2['agent'];
            $reg_id        = $token_status2['token'];
            $img_url      = $userimage;
            $tag          = 'text';
            $key_count    = '1';
            $title        =  $user_name.' commented on your post';
            $msg          =  $user_name.' commented on your post click here to view post.';
            $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
        }
        
        
        $ask_saheli_comment_query = $this->db->query("SELECT id from ask_saheli_comment WHERE post_id='$post_id'");
        $total_comment            = $ask_saheli_comment_query->num_rows();
        return array(
            'status' => 200,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }
    
    
    
    public function ask_saheli_post_comment_list($user_id, $post_id)
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
        $review_count = $this->db->select('id')->from('ask_saheli_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT ask_saheli_comment.id,ask_saheli_comment.comment,ask_saheli_comment.date,ask_saheli_comment.user_name,user_character.image as character_image,ask_saheli_comment.user_id as post_user_id  FROM ask_saheli_comment LEFT JOIN  user_character on user_character.id=ask_saheli_comment.user_image WHERE ask_saheli_comment.post_id='$post_id' order by ask_saheli_comment.id asc");
            
            foreach ($query->result_array() as $row) {
                $id           = $row['id'];
                $comment      = $row['comment']; 
              
              if(strtolower($comment)!='nice' || strtolower($comment)!='true'){
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if(base64_encode(base64_decode($comment)) === $comment){
                    $comment=base64_decode($comment);
                }
                }
                $username     = $row['user_name'];
                $image        = $row['character_image'];
                $date         = $row['date'];
                $post_user_id = $row['post_user_id'];
                $comment_date = get_time_difference_php($date);
                $image        = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $image;
                
                $like_count  = $this->db->select('id')->from('ask_saheli_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('ask_saheli_comment_like')->where('user_id', $user_id)->where('comment_id', $id)->get()->num_rows();
                
                $resultpost[] = array(
                    'id' => $id,  
                    'comment_user_id' => $post_user_id,
                    'username' => $username,
                    'image' => $image,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment' => $comment,
                    'comment_date' => $comment_date
                );
                
            }
        } else {
            $resultpost = array();
        }
        
        return $resultpost;
    }
    
  
    
    public function ask_saheli_post_comment_like($user_id, $comment_id, $user_name, $user_image,$post_id,$comment_user_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from ask_saheli_comment_like WHERE comment_id='$comment_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        
        if ($count > 0) {
            $this->db->query("DELETE FROM `ask_saheli_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from ask_saheli_comment_like WHERE comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $ask_saheli_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id,
                'user_name' => $user_name,
                'user_image' => $user_image
            );
            $this->db->insert('ask_saheli_comment_like', $ask_saheli_comment_like);
            
            

                    
            if($user_name=='0' || $user_name=='') 
            {
             $user_name='Someone'; 
            }
            
            if($user_image=='0')
            {
             $userimage='https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/ofw/ask_saheli.png';   
            }
            else
            {
            $img_query = $this->db->query("select user_character.image as character_image FROM ask_saheli_comment_like INNER JOIN user_character on user_character.id=ask_saheli_comment_like.user_image  WHERE  ask_saheli_comment_like.user_id='$user_id' AND ask_saheli_comment_like.comment_id='$comment_id'"); 
            $getimg = $img_query->row_array();
            $character_image = $getimg['character_image']; 
            $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $character_image;  
            }
			
            $customer_token     = $this->db->query("SELECT token, agent, token_status FROM users WHERE id='$comment_user_id' AND id<>'$user_id'");
            
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                //$getusr       = $user_comment_plike->row_array();
                //$usr_name     = $getusr['name'];
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $title        = $user_name.' Beats on your Comment';
                $msg          = $user_name.' Beats on your comment click here to view post.';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
            }
            
            
            $comment_query      = $this->db->query("SELECT id from `ask_saheli_comment_like` WHERE comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }
    
    
    
    public function ask_saheli_add_question($user_id, $user_name, $user_image, $question, $category,$post_location,$saheli_category)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date                    = date('Y-m-d H:i:s');
        $ask_saheli_add_question = array(
            'user_id' => $user_id,
            'user_name' => $user_name,
            'user_image' => $user_image,
            'post' => $question,
            'category' => $category,
            'type' => 'question', 
            'post_location' =>$post_location,  
            'saheli_category' =>$saheli_category,
            'date' => $date
        );
        $this->db->insert('ask_saheli_post', $ask_saheli_add_question);
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    
    
    public function ask_saheli_user_like_list($post_id)
    {
        $query = $this->db->query("SELECT ask_saheli_likes.id,ask_saheli_likes.user_image,ask_saheli_likes.user_name,ask_saheli_likes.user_id,user_character.image AS c_image  FROM  `ask_saheli_likes` INNER JOIN `user_character` ON ask_saheli_likes.user_image=	user_character.id WHERE ask_saheli_likes.post_id='$post_id' order by ask_saheli_likes.id desc");
        
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                
                $user_name = $row['user_name'];
                $images    = $row['c_image'];
                if ($images != '') {
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $images;
                } else {
                    $image = '';
                }
                
                $resultpost[] = array(
                    'user_name' => $user_name,
                    'image' => $image
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
    public function ask_saheli_video_views($user_id, $media_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date                         = date('Y-m-d H:i:s');
        $ask_saheli_video_views_array = array(
            'user_id' => $user_id,
            'media_id' => $media_id
        );
        $this->db->insert('ask_saheli_video_views', $ask_saheli_video_views_array);
        
        $video_views = $this->db->select('id')->from('ask_saheli_video_views')->where('media_id', $media_id)->get()->num_rows();
        
        return array(
            'status' => 200,
            'message' => 'success',
            'video_views' => $video_views
        );
    }
    
    
    public function ask_saheli_post_views($user_id, $post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date                   = date('Y-m-d H:i:s');
        $ask_saheli_views_array = array(
            'user_id' => $user_id,
            'post_id' => $post_id
        );
        $this->db->insert('ask_saheli_post_views', $ask_saheli_views_array);
        
        $post_views = $this->db->select('id')->from('ask_saheli_post_views')->where('post_id', $post_id)->get()->num_rows();
        
        return array(
            'status' => 200,
            'message' => 'success',
            'post_views' => $post_views
        );
    }
    
    
    public function ask_saheli_follow_post($user_id, $post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from ask_saheli_follow_post WHERE post_id='$post_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        
        if ($count > 0) {
            $this->db->query("DELETE FROM `ask_saheli_follow_post` WHERE user_id='$user_id' and post_id='$post_id'");
            $follow_query = $this->db->query("SELECT id from ask_saheli_follow_post WHERE post_id='$post_id'");
            $total_follow = $follow_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_follow
            );
        } else {
            $follow_post = array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'created_at' => $created_at,
                'updated_at' => $created_at
            );
            $this->db->insert('ask_saheli_follow_post', $follow_post);
            $follow_query = $this->db->query("SELECT id from ask_saheli_follow_post WHERE post_id='$post_id'");
            $total_follow = $follow_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_follow
            );
        }
    }
    
    
    public function ask_saheli_user_update($user_id, $user_name, $user_image)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM `ask_user` WHERE user_id='$user_id'");
        $count       = $count_query->num_rows();
        
        if ($count > 0) {
            $this->db->query("UPDATE `ask_user` SET `user_name`='$user_name',`user_image`='$user_image' WHERE user_id='$user_id'");
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            $ask_saheli_ask_user = array(
                'user_id' => $user_id,
                'user_name' => $user_name,
                'user_image' => $user_image
            );
            $this->db->insert('ask_user', $ask_saheli_ask_user);
            return array(
                'status' => 200,
                'message' => 'success'
            );
        }
    }
    
    
    
    public function ask_saheli_user_check($user_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM `ask_user` WHERE user_id='$user_id'");
        $count       = $count_query->num_rows();
        
        if ($count > 0) {
            $query = $this->db->query("SELECT ask_user.user_id, ask_user.user_name, ask_user.user_image, user_character.image AS c_image FROM `ask_user` INNER JOIN `user_character` ON ask_user.user_image=user_character.id WHERE ask_user.user_id='$user_id'");
            
            
            
            $row        = $query->row_array();
            $user_id    = $row['user_id'];
            $user_name  = $row['user_name'];
            $user_image = $row['user_image'];
            $images     = $row['c_image'];
            if ($images != '') {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/character/' . $images;
            } else {
                $image = '';
            }
            
            $resultpost[] = array(
                'user_id' => $user_id,
                'user_name' => $user_name,
                'user_image' => $user_image,
                'images' => $image
            );
        } else {
            
            $resultpost = array();
        }
        
        return $resultpost;
    }
    
    
    

     public function ask_saheli_post_hide($user_id,$post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `ask_saheli_post_hide` WHERE  user_id='$user_id' and post_id='$post_id'");
        $count= $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `ask_saheli_post_hide` WHERE user_id='$user_id' and post_id='$post_id'");
            return array(
                'status' => 200,
                'message' => 'deleted',
                'is_hide' => '0'
            );
        } else {
            $ask_saheli_post_hide = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('ask_saheli_post_hide', $ask_saheli_post_hide);


    

            return array(
                'status' => 200,
                'message' => 'success',
                'is_hide' => '1'
            );
        }
    }
    
            
    
     public function ask_saheli_post_delete($user_id,$post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `ask_saheli_post` WHERE  user_id='$user_id' and id='$post_id'");
        $count= $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `ask_saheli_post` WHERE user_id='$user_id' and id='$post_id'");
            
            
              //unlink images
               $media_query  = $this->db->query("SELECT ask_saheli_post_media.source,ask_saheli_post_media.type AS media_type FROM ask_saheli_post_media INNER JOIN ask_saheli_post on ask_saheli_post_media.post_id=ask_saheli_post.id WHERE ask_saheli_post_media.post_id='$post_id'");
                         
                foreach ($media_query->result_array() as $media_row) {
                    $source          = $media_row['source']; 
                    $media_type       = $media_row['media_type'];
                    $file = 'images/ask_saheli_images/'.$media_type.'/'.$source;
						@unlink(trim($file));
						DeleteFromToS3($file);
                }
     
               //unlink images ends  
               $this->db->query("DELETE FROM `ask_saheli_post_media` WHERE post_id='$post_id'");
            
            return array(
                'status' => 200,
                'message' => 'deleted'
            );
        } 
        
        else {
            return array(
                'status' => 401,
                'message' => 'unauthorized'
            );
        }
        
    }
    
    public function ask_saheli_edit_question($post_id,$user_id,$user_name,$user_image,$question,$category,$saheli_category)
    {
       
        $this->db->query("UPDATE `ask_saheli_post` SET `user_name`='$user_name',`user_image`='$user_image',`post`='$question',`category`='$category',`saheli_category`='$saheli_category' WHERE id='$post_id'");
        return array(
                'status' => 200,
                'message' => 'success'
            );
    }
    
    
    
    
}
