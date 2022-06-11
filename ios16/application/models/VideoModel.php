<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class VideoModel extends CI_Model
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
	//date
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
    
    public function encrypt($str)
    {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv  = hash('MD5', 'mdwale8655328655', true);
        $module    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad   = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $encrypted = mcrypt_generic($module, $str);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        return base64_encode($encrypted);
    }
    
    public function decrypt($str)
    {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv  = hash('MD5', 'mdwale8655328655', true);
        $module    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $str = mdecrypt_generic($module, base64_decode($str));
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        $slast = ord(substr($str, -1));
        $str   = substr($str, 0, strlen($str) - $slast);
        return $str;
    }
    
    public function med_tube_by_type($user_id,$tid,$type)
    {
        $yoga_query          = $this->db->query("SELECT id,video,details,video_thumbnail,title,views FROM exercise_details WHERE cat_id='2' order by id asc limit 10");
        $physiotherapy_query = $this->db->query("SELECT id,video,details,video_thumbnail,title,views FROM exercise_details WHERE cat_id='1' order by id asc limit 10");		
        $other_query         = $this->db->query("SELECT id,type,thumbnail,title,video,description,views FROM med_tube order by type asc");
		
        $yoga_count          = $yoga_query->num_rows();
        $physiotherapy_count = $physiotherapy_query->num_rows();
        $other_count         = $other_query->num_rows();
        if($type=="yoga"){
        if ($yoga_count > 0) {
            foreach ($yoga_query->result_array() as $row) {
                $id              = $row['id'];
                $video_thumbnail = $row['video_thumbnail'];
                $video           = $row['video'];
                $details         = $row['details'];
                $title           = $row['title'];
                if ($video != '') {
                    $video = 'https://d2ua8z9537m644.cloudfront.net/exercise_images/video/' . $video;
                } else { 
                    $video = '';
                }
                if ($video_thumbnail != '') {
                    $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/exercise_images/image/' . $video_thumbnail;
                } else {
                    $video_thumbnail = '';
                }				
        
				$total_query = $this->db->query("SELECT (SELECT count(id) FROM exercise_likes where post_id='$id' and user_id='$user_id' LIMIT 1) AS like_yes_no, (SELECT count(id) FROM exercise_likes where post_id='$id' LIMIT 1) AS like_count");
                
				$get_list = $total_query->row_array();
                $total_likes = $get_list['like_count'];
                $like_yes_no = $get_list['like_yes_no'];
                $total_views = $row['views'];
				
                if($id!=$tid){
                $medtuberesult[] = array(
                    'video_id' => $id,
                    'video_thumbnail' => $video_thumbnail,
                    'video' => $video,
                    'details' => $details,
                    'title' => $title,
                    'total_likes' => $total_likes,
                    'total_views' => $total_views,
                    'like_yes_no' => $like_yes_no,
                    'url' => 'https://medicalwale.com/appyoga/' . $id
                );
            }
            }
        } else {
            $medtuberesult = array();
        }
       }
        if($type=="physiotherapy"){
        if ($physiotherapy_count > 0) {
            foreach ($physiotherapy_query->result_array() as $row) {
                $id              = $row['id'];
                $video_thumbnail = $row['video_thumbnail'];
                $video           = $row['video'];
                $details         = $row['details'];
                $title           = $row['title'];
                if ($video != '') {
                    $video = 'https://d2ua8z9537m644.cloudfront.net/exercise_images/video/' . $video;
                } else {
                    $video = '';
                }
                if ($video_thumbnail != '') {
                    $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/exercise_images/image/' . $video_thumbnail;
                } else {
                    $video_thumbnail = '';
                }
             
				$total_query = $this->db->query("SELECT (SELECT count(id) FROM exercise_likes where post_id='$id' and user_id='$user_id' LIMIT 1) AS like_yes_no, (SELECT count(id) FROM exercise_likes where post_id='$id' LIMIT 1) AS like_count");
                
				$get_list = $total_query->row_array();
                $total_likes = $get_list['like_count'];
                $like_yes_no = $get_list['like_yes_no'];
                $total_views = $row['views'];
				
				
                if($id!=$tid){
                $medtuberesult[] = array(
                    'video_id' => $id,
                    'video_thumbnail' => $video_thumbnail,
                    'video' => $video,
                    'details' => $details,
                    'title' => $title,
                    'total_likes' => $total_likes,
                    'total_views' => $total_views,
                    'like_yes_no' => $like_yes_no,
                    'url' => 'https://medicalwale.com/appphysiotherapy/' . $id
                );
                }
            }
        } else {
            $medtuberesult = array();
        }
        }
          if($type=="social_events" || $type=="special_days" || $type=="advertising_campaign" || $type == "workouts" ){
        if ($other_count > 0) {
            $social_events = array();
            $special_days  = array();
            foreach ($other_query->result_array() as $row) {
                $id              = $row['id'];
                $types            = $row['type'];
                $video_thumbnail = $row['thumbnail'];
                $title           = $row['title'];
                $video           = $row['video'];
                $details         = $row['description'];
             
				$total_query = $this->db->query("SELECT (SELECT count(id) FROM exercise_likes where post_id='$id' and user_id='$user_id' LIMIT 1) AS like_yes_no, (SELECT count(id) FROM exercise_likes where post_id='$id' LIMIT 1) AS like_count");
                
				$get_list = $total_query->row_array();
                $total_likes = $get_list['like_count'];
                $like_yes_no = $get_list['like_yes_no'];
                $total_views = $row['views'];
				
                if($type == 'social_events'){
                if ($types == 'social_events') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/social_events/' . $video;
                        $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/social_events/thumb/' . $video_thumbnail;
                    } else {
                        $video_thumbnail = '';
                    }
                     if($id!=$tid){
                     $medtuberesult[] = array(
                        'video_id' => $id,
                        'video_thumbnail' => $video_thumbnail,
                        'video' => $video,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appsocialevents/' . $id
                     );
                     }
                }}
                if($type == 'special_days'){
                if ($types == 'special_days') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/special_days/' . $video;
                        $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/special_days/thumb/' . $video_thumbnail;
                    } else {
                        $video_thumbnail = '';
                    }
                     if($id!=$tid){
                    $medtuberesult[] = array(
                        'video_id' => $id,
                        'video' => $video,
                        'video_thumbnail' => $video_thumbnail,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appspecialdays/' . $id
                    );
                     }
                }}
                if($type == 'advertising_campaign'){
                if ($types == 'advertising_campaign') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/advertising_campaign/' . $video;
                        $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/advertising_campaign/thumb/' . $video_thumbnail;
                    } else {
                        $video_thumbnail = '';
                    }
                     if($id!=$tid){
                    $medtuberesult[] = array(
                        'video_id' => $id,
                        'video' => $video,
                        'video_thumbnail' => $video_thumbnail,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appadvertisingcampaign/' . $id
                    );
                     }
                }}
                if($type == 'workouts'){
                 if ($types == 'animation') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://s3.amazonaws.com/medicalwale/images/fitness_center/video/' . $video;
                        $video_thumbnail = 'https://s3.amazonaws.com/medicalwale/images/fitness_center/video/Thumbnails/' . $video_thumbnail;
                    } else {
                        $video           = 'https://s3.amazonaws.com/medicalwale/images/fitness_center/video/' . $video;
                        $video_thumbnail = '';
                    }
                      if($id!=$tid){
                    $medtuberesult[] = array(
                        'video_id' => $id,
                        'video' => $video,
                        'video_thumbnail' => $video_thumbnail,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appadvertisingcampaign/' . $id
                    );
                      }
                }
               
            }}
        } else {
            $medtuberesult = array();
        }
    }
        
        
        
        $result_list = array(
            "status" => 200,
            "message" => "success",
            "data" => $medtuberesult,
           
        );
        return $result_list;
    }
    
    
     public function med_tube($user_id)
    {
        $yoga_query          = $this->db->query("SELECT id,video,details,video_thumbnail,title,views FROM exercise_details WHERE cat_id='2' order by id asc limit 10");
        $physiotherapy_query = $this->db->query("SELECT id,video,details,video_thumbnail,title,views FROM exercise_details WHERE cat_id='1' order by id asc limit 10");		
        $other_query         = $this->db->query("SELECT id,type,thumbnail,title,video,description,views FROM med_tube order by type asc");		
        $yoga_count          = $yoga_query->num_rows();
        $physiotherapy_count = $physiotherapy_query->num_rows();
        $other_count         = $other_query->num_rows();
        if ($yoga_count > 0) {
            foreach ($yoga_query->result_array() as $row) {
                $id              = $row['id'];
                $video_thumbnail = $row['video_thumbnail'];
                $video           = $row['video'];
                $details         = $row['details'];
                $title           = $row['title'];
                if ($video != '') {
                    $video = 'https://d2ua8z9537m644.cloudfront.net/exercise_images/video/' . $video;
                } else {
                    $video = '';
                }
                if ($video_thumbnail != '') {
                    $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/exercise_images/image/' . $video_thumbnail;
                } else {
                    $video_thumbnail = '';
                }
        
				$total_query = $this->db->query("SELECT (SELECT count(id) FROM exercise_likes where post_id='$id' and user_id='$user_id' LIMIT 1) AS like_yes_no, (SELECT count(id) FROM exercise_likes where post_id='$id' LIMIT 1) AS like_count");
                
				$get_list = $total_query->row_array();
                $total_likes = $get_list['like_count'];
                $like_yes_no = $get_list['like_yes_no'];
                $total_views = $row['views'];
				
                $yoga_result[] = array(
                    'video_id' => $id,
                    'video_thumbnail' => $video_thumbnail,
                    'video' => $video,
                    'details' => $details,
                    'title' => $title,
                    'total_likes' => $total_likes,
                    'total_views' => $total_views,
                    'like_yes_no' => $like_yes_no,
                    'url' => 'https://medicalwale.com/appyoga/' . $id
                );
            }
        } else {
            $yoga_result = array();
        }
        
        if ($physiotherapy_count > 0) {
            foreach ($physiotherapy_query->result_array() as $row) {
                $id              = $row['id'];
                $video_thumbnail = $row['video_thumbnail'];
                $video           = $row['video'];
                $details         = $row['details'];
                $title           = $row['title'];
                if ($video != '') {
                    $video = 'https://d2ua8z9537m644.cloudfront.net/exercise_images/video/' . $video;
                } else {
                    $video = '';
                }
                if ($video_thumbnail != '') {
                    $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/exercise_images/image/' . $video_thumbnail;
                } else {
                    $video_thumbnail = '';
                }
            
				$total_query = $this->db->query("SELECT (SELECT count(id) FROM exercise_likes where post_id='$id' and user_id='$user_id' LIMIT 1) AS like_yes_no, (SELECT count(id) FROM exercise_likes where post_id='$id' LIMIT 1) AS like_count");
                
				$get_list = $total_query->row_array();
                $total_likes = $get_list['like_count'];
                $like_yes_no = $get_list['like_yes_no'];
                $total_views = $row['views'];
                
                $physiotherapy_result[] = array(
                    'video_id' => $id,
                    'video_thumbnail' => $video_thumbnail,
                    'video' => $video,
                    'details' => $details,
                    'title' => $title,
                    'total_likes' => $total_likes,
                    'total_views' => $total_views,
                    'like_yes_no' => $like_yes_no,
                    'url' => 'https://medicalwale.com/appphysiotherapy/' . $id
                );
            }
        } else {
            $physiotherapy_result = array();
        }
        
        if ($other_count > 0) {
            $social_events = array();
            $special_days  = array();
            foreach ($other_query->result_array() as $row) {  
                $id              = $row['id'];
                $type            = $row['type'];
                $video_thumbnail = $row['thumbnail'];
                $title           = $row['title'];
                $video           = $row['video'];
                $details         = $row['description'];
                
                $total_query = $this->db->query("SELECT (SELECT count(id) FROM med_tube_views where video_id='$id' and user_id='$user_id' LIMIT 1) AS like_yes_no, (SELECT count(id) FROM med_tube_views where video_id='$id' LIMIT 1) AS like_count");
                
				$get_list = $total_query->row_array();
                $total_likes = $get_list['like_count'];
                $like_yes_no = $get_list['like_yes_no'];
                $total_views = $row['views'];
                
                if ($type == 'social_events') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/social_events/' . $video;
                        $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/social_events/thumb/' . $video_thumbnail;
                    } else {
                        $video_thumbnail = '';
                    }
                    $social_events[] = array(
                        'video_id' => $id,
                        'video_thumbnail' => $video_thumbnail,
                        'video' => $video,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appsocialevents/' . $id
                    );
                }
                if ($type == 'special_days') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/special_days/' . $video;
                        $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/special_days/thumb/' . $video_thumbnail;
                    } else {
                        $video_thumbnail = '';
                    }
                    $special_days[] = array(
                        'video_id' => $id,
                        'video' => $video,
                        'video_thumbnail' => $video_thumbnail,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appspecialdays/' . $id
                    );
                }
                if ($type == 'advertising_campaign') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/advertising_campaign/' . $video;
                        $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/advertising_campaign/thumb/' . $video_thumbnail;
                    } else {
                        $video_thumbnail = '';
                    }
                    $advertising_campaign[] = array(
                        'video_id' => $id,
                        'video' => $video,
                        'video_thumbnail' => $video_thumbnail,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appadvertisingcampaign/' . $id
                    );
                }
                 if ($type == 'animation') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://s3.amazonaws.com/medicalwale/images/fitness_center/video/' . $video;
                        $video_thumbnail = 'https://s3.amazonaws.com/medicalwale/images/fitness_center/video/Thumbnails/' . $video_thumbnail;
                    } else {
                        $video           = 'https://s3.amazonaws.com/medicalwale/images/fitness_center/video/' . $video;
                        $video_thumbnail = '';
                    }
                    $animation[] = array(
                        'video_id' => $id,
                        'video' => $video,
                        'video_thumbnail' => $video_thumbnail,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appadvertisingcampaign/' . $id
                    );
                }
            }
        } else {
            $other_result = array();
        }
        
        $types = array(
            'yoga',
            'physiotherapy',
            'social_events',
            'special_days',
            'advertising_campaign'
        );
        
        $result_list = array(
            "status" => 200,
            "message" => "success",
            "types" => $types,
            "yoga" => $yoga_result,
            "physiotherapy" => $physiotherapy_result,
            "social_events" => $social_events,
            "special_days" => $special_days,
            "advertising_campaign" => $advertising_campaign,
             "work_out" => $animation
        );
        return $result_list;
    }
    
    
    
    
    //added for paggination 
     public function med_tube_pagination($user_id,$page,$category,$keyword)
    {
         //Pagination
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        
        
        //added for medtubes for category
        $social_events = array();
        $special_days  = array();
        $yoga_result = array();
        $physiotherapy_result = array();
        $animation = array();
        $advertising_campaign = array();
        
        if($category == 'yoga')
        {
            if($keyword != '')
            {
          $yoga_query  = $this->db->query("SELECT id,video,details,video_thumbnail,title,views FROM exercise_details WHERE cat_id='2' AND title LIKE '%$keyword%' order by id desc  limit $start,$limit");
            }
            else
            {
          $yoga_query  = $this->db->query("SELECT id,video,details,video_thumbnail,title,views FROM exercise_details WHERE cat_id='2' order by id desc  limit $start,$limit");      
            }
          $yoga_count          = $yoga_query->num_rows();
         if ($yoga_count > 0) {
            foreach ($yoga_query->result_array() as $row) {
                $id              = $row['id'];
                $video_thumbnail = $row['video_thumbnail'];
                $video           = $row['video'];
                $details         = $row['details'];
                $title           = $row['title'];
                if ($video != '') {
                    $video = 'https://d2ua8z9537m644.cloudfront.net/exercise_images/video/' . $video;
                } else {
                    $video = '';
                }
                if ($video_thumbnail != '') {
                    $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/exercise_images/image/' . $video_thumbnail;
                } else {
                    $video_thumbnail = '';
                }
                
                $total_views          = $row['views'];;
                
                $like_query  = $this->db->query("SELECT id from exercise_likes WHERE post_id='$id'");
                $total_likes = $like_query->num_rows();
                
                $like_yes_no = $this->db->select('id')->from('exercise_likes')->where('post_id', $id)->where('user_id', $user_id)->get()->num_rows();
                
                
                $yoga_result[] = array(
                    'video_id' => $id,
                    'video_thumbnail' => $video_thumbnail,
                    'video' => $video,
                    'details' => $details,
                    'title' => $title,
                    'total_likes' => $total_likes,
                    'total_views' => $total_views,
                    'like_yes_no' => $like_yes_no,
                    'url' => 'https://medicalwale.com/appyoga/' . $id
                );
            }
             } else {
                $yoga_result = array();
             }
        }
        else if($category == 'physiotherapy')
        {
            if($keyword != '')
            {
                $physiotherapy_query = $this->db->query("SELECT id,video,details,video_thumbnail,title,views FROM exercise_details WHERE cat_id='1' AND title LIKE '%$keyword%' order by id asc limit $start,$limit");
            }
            else
            {
        $physiotherapy_query = $this->db->query("SELECT id,video,details,video_thumbnail,title,views FROM exercise_details WHERE cat_id='1' order by id asc limit $start,$limit");
            } 
        $physiotherapy_count = $physiotherapy_query->num_rows();
      
           if ($physiotherapy_count > 0) {
            foreach ($physiotherapy_query->result_array() as $row) {
                $id              = $row['id'];
                $video_thumbnail = $row['video_thumbnail'];
                $video           = $row['video'];
                $details         = $row['details'];
                $title           = $row['title'];
                if ($video != '') {
                    $video = 'https://d2ua8z9537m644.cloudfront.net/exercise_images/video/' . $video;
                } else {
                    $video = '';
                }
                if ($video_thumbnail != '') {
                    $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/exercise_images/image/' . $video_thumbnail;
                } else {
                    $video_thumbnail = '';
                }
                
                $total_views          = $row['views'];
                
                $like_query  = $this->db->query("SELECT id from exercise_likes WHERE post_id='$id'");
                $total_likes = $like_query->num_rows();
                
                $like_yes_no = $this->db->select('id')->from('exercise_likes')->where('post_id', $id)->where('user_id', $user_id)->get()->num_rows();
                
                
                $physiotherapy_result[] = array(
                    'video_id' => $id,
                    'video_thumbnail' => $video_thumbnail,
                    'video' => $video,
                    'details' => $details,
                    'title' => $title,
                    'total_likes' => $total_likes,
                    'total_views' => $total_views,
                    'like_yes_no' => $like_yes_no,
                    'url' => 'https://medicalwale.com/appphysiotherapy/' . $id
                );
            }
        } else {
            $physiotherapy_result = array();
        }
        }
        else 
        {
            if($category == '')
            {
            if($keyword != '')
            {
                $other_query         = $this->db->query("SELECT * FROM med_tube AND title LIKE '%$keyword%' order by type desc ");
            }
            else
            {
                $other_query         = $this->db->query("SELECT * FROM med_tube order by type desc ");
            }
            }else
            {
               if($keyword != '')
            {
                $other_query         = $this->db->query("SELECT * FROM med_tube WHERE type = '$category' AND title LIKE '%$keyword%' order by type desc limit $start,$limit");
            }
            else
            {
                $other_query         = $this->db->query("SELECT * FROM med_tube WHERE type = '$category' order by type desc limit $start,$limit");
            } 
            }
        $other_count         = $other_query->num_rows();
      
        
      
        
        if ($other_count > 0) {
            $social_events = array();
            $special_days  = array();
            foreach ($other_query->result_array() as $row) {
                $id              = $row['id'];
                $type            = $row['type'];
                $video_thumbnail = $row['thumbnail'];
                $title           = $row['title'];
                $video           = $row['video'];
                $details         = $row['description'];
                
                $video_views_query = $this->db->query("SELECT id from med_tube_views WHERE video_id='$id'");
                $total_views       = $video_views_query->num_rows();
                
                $like_query  = $this->db->query("SELECT id from med_tube_likes WHERE video_id='$id'");
                $total_likes = $like_query->num_rows();
                
                $like_yes_no = $this->db->select('id')->from('med_tube_likes')->where('video_id', $id)->where('user_id', $user_id)->get()->num_rows();
                
                
                if ($type == 'social_events' && $category == 'social_events') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/social_events/' . $video;
                        $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/social_events/thumb/' . $video_thumbnail;
                    } else {
                        $video_thumbnail = '';
                    }
                    $social_events[] = array(
                        'video_id' => $id,
                        'video_thumbnail' => $video_thumbnail,
                        'video' => $video,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appsocialevents/' . $id
                    );
                }
               else if ($type == 'special_days'  && $category == 'special_days') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/special_days/' . $video;
                        $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/special_days/thumb/' . $video_thumbnail;
                    } else {
                        $video_thumbnail = '';
                    }
                    $special_days[] = array(
                        'video_id' => $id,
                        'video' => $video,
                        'video_thumbnail' => $video_thumbnail,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appspecialdays/' . $id
                    );
                }
               else if ($type == 'advertising_campaign' && $category == 'advertising_campaign') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/advertising_campaign/' . $video;
                        $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/advertising_campaign/thumb/' . $video_thumbnail;
                    } else {
                        $video_thumbnail = '';
                    }
                    $advertising_campaign[] = array(
                        'video_id' => $id,
                        'video' => $video,
                        'video_thumbnail' => $video_thumbnail,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appadvertisingcampaign/' . $id
                    );
                }
               else if ($type == 'animation' && $category == 'animation') {
                    if ($video_thumbnail != '') {
                        $video           = 'https://s3.amazonaws.com/medicalwale/images/fitness_center/video/' . $video;
                        $video_thumbnail = 'https://s3.amazonaws.com/medicalwale/images/fitness_center/video/Thumbnails/' . $video_thumbnail;
                    } else {
                        $video           = 'https://s3.amazonaws.com/medicalwale/images/fitness_center/video/' . $video;
                        $video_thumbnail = '';
                    }
                    $animation[] = array(
                        'video_id' => $id,
                        'video' => $video,
                        'video_thumbnail' => $video_thumbnail,
                        'title' => $title,
                        'details' => $details,
                        'total_likes' => $total_likes,
                        'total_views' => $total_views,
                        'like_yes_no' => $like_yes_no,
                        'url' => 'https://medicalwale.com/appadvertisingcampaign/' . $id
                    );
                }
            }
        } else {
            $other_result = array();
        }
        
        // $types = array(
        //     'yoga',
        //     'physiotherapy',
        //     'social_events',
        //     'special_days',
        //     'advertising_campaign'
        // );
        }
        $result_list = array(
            "status" => 200,
            "message" => "success",
            "yoga" => $yoga_result,
            "physiotherapy" => $physiotherapy_result,
            "social_events" => $social_events,
            "special_days" => $special_days,
            "advertising_campaign" => $advertising_campaign,
             "work_out" => $animation
        );
        return $result_list;
    }
    
    
    public function med_tube_likes($user_id, $video_id, $type)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        if ($type == 'yoga' || $type == 'physiotherapy' || $type == 'workouts') {
            $count_query = $this->db->query("SELECT id from exercise_likes WHERE post_id='$video_id' AND user_id='$user_id'");
            $count       = $count_query->num_rows();
            
            if ($count > 0) {
                $this->db->query("DELETE FROM `exercise_likes` WHERE user_id='$user_id' AND post_id='$video_id'");
                $like_query = $this->db->query("SELECT id from exercise_likes where post_id='$video_id'");
                $total_like = $like_query->num_rows();
                return array(
                    'status' => 201,
                    'message' => 'deleted',
                    'like' => 0,
                    'total_like' => $total_like
                );
            } else {
                $exercise_likes = array(
                    'user_id' => $user_id,
                    'post_id' => $video_id
                );
                $this->db->insert('exercise_likes', $exercise_likes);
                $like_query = $this->db->query("SELECT id from exercise_likes WHERE post_id='$video_id'");
                $total_like = $like_query->num_rows();
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'like' => 1,
                    'total_like' => $total_like
                );
            }
        }
        
        else if ($type == 'social_events' || $type == 'special_days' || $type == 'advertising_campaign') {
            $count_query = $this->db->query("SELECT id from med_tube_likes WHERE video_id='$video_id' AND user_id='$user_id'");
            $count       = $count_query->num_rows();
            
            if ($count > 0) {
                $this->db->query("DELETE FROM `med_tube_likes` WHERE user_id='$user_id' AND video_id='$video_id'");
                $like_query = $this->db->query("SELECT id from med_tube_likes where video_id='$video_id'");
                $total_like = $like_query->num_rows();
                return array(
                    'status' => 201,
                    'message' => 'deleted',
                    'like' => 0,
                    'total_like' => $total_like
                );
            } else {
                $med_tube_likes = array(
                    'user_id' => $user_id,
                    'video_id' => $video_id
                );
                $this->db->insert('med_tube_likes', $med_tube_likes);
                $like_query = $this->db->query("SELECT id from med_tube_likes WHERE video_id='$video_id'");
                $total_like = $like_query->num_rows();
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'like' => 1,
                    'total_like' => $total_like
                );
            }
        }
        
        else {
            return array(
                    'status' => 404,
                    'message' => 'success',
                    'like' => 1,
                    'total_like' => ""
                );
        }
    }
    
    public function med_tube_views($user_id, $video_id, $type)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        if ($type == 'yoga' || $type == 'physiotherapy') {	
			
			$total_query = $this->db->query("SELECT views FROM `exercise_details` where id='$video_id' limit 1");
			$get_list = $total_query->row_array();
			$views = $get_list['views'];
			$total_views = $views+1;
			$querys = $this->db->query("UPDATE `exercise_details` SET `views`='$total_views' WHERE id='$video_id'");
            
            return array(
                'status' => 200,
                'message' => 'success',
                'total_views' => $total_views
            );
        }
        
        if ($type == 'social_events' || $type == 'special_days' || $type == 'advertising_campaign') {
            
			$total_query = $this->db->query("SELECT views FROM `med_tube` where id='$video_id' limit 1");
			$get_list = $total_query->row_array();
			$views = $get_list['views'];
			$total_views = $views+1;
			$querys = $this->db->query("UPDATE `med_tube` SET `views`='$total_views' WHERE id='$video_id'");
            
            return array(
                'status' => 200,
                'message' => 'success',
                'total_views' => $total_views
            );
        }
        
    }
    
    public function med_spread($user_id,$page,$keyword)
    {
         //Pagination
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        if($keyword != '')
        {
        $med_spread_query = $this->db->query("SELECT * FROM med_spread where title LIKE '%$keyword%' order by id desc limit $start,$limit");
        }
        else
        {
        $med_spread_query = $this->db->query("SELECT * FROM med_spread order by id desc limit $start,$limit");    
        }
        $med_spread_count = $med_spread_query->num_rows();
        if ($med_spread_count > 0) {
            foreach ($med_spread_query->result_array() as $row) {
                $id              = $row['id'];
                $video           = $row['video'];
                $details         = $row['description'];
                $video_thumbnail = $row['thumbnail'];
                $title           = $row['title'];
                if ($video_thumbnail != '') {
                    $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/med_spread/' . $video;
                    $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/med_spread/thumbs/' . $video_thumbnail;
                } else {
                    $video_thumbnail = '';
                }
                $views_query = $this->db->query("SELECT id from med_spread_views WHERE video_id='$id'");
                $total_views          = $views_query->num_rows();
                
                $like_query  = $this->db->query("SELECT id from med_spread_likes WHERE video_id='$id'");
                $total_likes = $like_query->num_rows();
                
                $like_yes_no = $this->db->select('id')->from('med_spread_likes')->where('video_id', $id)->where('user_id', $user_id)->get()->num_rows();
                
                $med_spread_result[] = array(
                    'video_id' => $id,
                    'video' => $video,
                    'video_thumbnail' => $video_thumbnail,
                    'title' => $title,
                    'details' => $details,
                    'total_likes' => $total_likes,
                    'total_views' => $total_views,
                    'like_yes_no' => $like_yes_no,
                    'url' => 'https://medicalwale.com/appmedspread/' . $id
                );
            }
        } else {
            $med_spread_result = array();
        }
        
        $result_list = array(
            "status" => 200,
            "message" => "success",
            "med_spread" => $med_spread_result
        );
        return $result_list;
    }
    
    
    
     public function med_spread_view_more($user_id,$v_id)
    {
         //Pagination
      
        $med_spread_query = $this->db->query("SELECT * FROM med_spread where  id != '$v_id' order by id desc ");    
        
        $med_spread_count = $med_spread_query->num_rows();
        if ($med_spread_count > 0) {
            foreach ($med_spread_query->result_array() as $row) {
                $id              = $row['id'];
                $video           = $row['video'];
                $details         = $row['description'];
                $video_thumbnail = $row['thumbnail'];
                $title           = $row['title'];
                if ($video_thumbnail != '') {
                    $video           = 'https://d2c8oti4is0ms3.cloudfront.net/images/med_spread/' . $video;
                    $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/med_spread/thumbs/' . $video_thumbnail;
                } else {
                    $video_thumbnail = '';
                }
                $views_query = $this->db->query("SELECT id from med_spread_views WHERE video_id='$id'");
                $total_views          = $views_query->num_rows();
                
                $like_query  = $this->db->query("SELECT id from med_spread_likes WHERE video_id='$id'");
                $total_likes = $like_query->num_rows();
                
                $like_yes_no = $this->db->select('id')->from('med_spread_likes')->where('video_id', $id)->where('user_id', $user_id)->get()->num_rows();
                
                $med_spread_result[] = array(
                    'video_id' => $id,
                    'video' => $video,
                    'video_thumbnail' => $video_thumbnail,
                    'title' => $title,
                    'details' => $details,
                    'total_likes' => $total_likes,
                    'total_views' => $total_views,
                    'like_yes_no' => $like_yes_no,
                    'url' => 'https://medicalwale.com/appmedspread/' . $id
                );
            }
        } else {
            $med_spread_result = array();
        }
        
        $result_list = array(
            "status" => 200,
            "message" => "success",
            "med_spread" => $med_spread_result
        );
        return $result_list;
    }
    
    
    public function med_spread_likes($user_id, $video_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        
   
            $count_query = $this->db->query("SELECT id from med_spread_likes WHERE video_id='$video_id' AND user_id='$user_id'");
            $count       = $count_query->num_rows();
            
            if ($count > 0) {
                $this->db->query("DELETE FROM `med_spread_likes` WHERE user_id='$user_id' AND video_id='$video_id'");
                $like_query = $this->db->query("SELECT id from med_spread_likes where video_id='$video_id'");
                $total_like = $like_query->num_rows();
                return array(
                    'status' => 201,
                    'message' => 'deleted',
                    'like' => 0,
                    'total_like' => $total_like
                );
            } else {
                $med_spread_likes = array(
                    'user_id' => $user_id,
                    'video_id' => $video_id
                );
                $this->db->insert('med_spread_likes', $med_spread_likes);
                $like_query = $this->db->query("SELECT id from med_spread_likes WHERE video_id='$video_id'");
                $total_like = $like_query->num_rows();
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'like' => 1,
                    'total_like' => $total_like
                );
            }

        
    }
    
    public function med_spread_views($user_id, $video_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');

            $med_spread_views = array(
                'user_id' => $user_id,
                'video_id' => $video_id
            );
            $this->db->insert('med_spread_views', $med_spread_views);
            
            $total_views = $this->db->select('id')->from('med_spread_views')->where('video_id', $video_id)->get()->num_rows();
            
            return array(
                'status' => 200,
                'message' => 'success',
                'total_views' => $total_views
            );

        
    }
    
}
