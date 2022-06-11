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
    
    public function home_remedies($user_id, $category_id)
    {
        $query = $this->db->query("SELECT id, category_id, title, details, image, date FROM `home_remedies` where is_active='1' AND category_id='$category_id' order by id asc");
        
        foreach ($query->result_array() as $row) {
            $id              = $row['id'];
            $category_id     = $row['category_id'];
            $title           = $row['title'];
            $details         = $row['details'];
            $image           = $row['image'];
            $date            = $row['date'];
            $image           = str_replace(" ", "", $image);
            $image           = 'https://d2c8oti4is0ms3.cloudfront.net/images/ofw_images/home_remedies/' . $image;
            $like_count      = $this->db->select('id')->from('home_remedies_likes')->where('home_remedies_id', $id)->get()->num_rows();
            $like_yes_no     = $this->db->select('id')->from('home_remedies_likes')->where('user_id', $user_id)->where('home_remedies_id', $id)->get()->num_rows();
            $view_count      = $this->db->select('id')->from('home_remedies_views')->where('home_remedies_id', $id)->get()->num_rows();
            $bookmark_yes_no = $this->db->select('id')->from('home_remedies_bookmark')->where('user_id', $user_id)->where('home_remedies_id', $id)->get()->num_rows();
            $share           = 'https://medicalwale.com/images/only_for_women/home_remedies/home%20remedies%20details.php?id=' . $id;
            $resultpost[]    = array(
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
    
    public function get_time_difference_php($created_time)
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
            return "on " . date('D, d F Y', strtotime($created_time));
            //return intval($time_differnce / $years) . ' yrs';
        } elseif (intval($time_differnce / $years) > 0) {
            return "on " . date('D, d F Y', strtotime($created_time));
            //return intval($time_differnce / $years) . ' yr';
        } elseif (intval($time_differnce / $months) > 1) {
            return "on " . date('D, d F Y', strtotime($created_time));
            //return intval($time_differnce / $months) . ' months';
        } elseif (intval(($time_differnce / $months)) > 0) {
            return "on " . date('D, d F', strtotime($created_time));
            //return intval(($time_differnce / $months)) . ' month';
        } elseif (intval(($time_differnce / $days)) > 1) {
            return "on " . date('D, d F', strtotime($created_time));
            //return intval(($time_differnce / $days)) . ' days';
        } elseif (intval(($time_differnce / $days)) > 0) {
            $var = date('D, d F', strtotime($created_time));
            return "on " . $var;
            //return intval(($time_differnce / $days)) . ' day';
        } elseif (intval(($time_differnce / $hours)) > 1) {
            //return date('D, d F  ago', strtotime($created_time));
            return intval(($time_differnce / $hours)) . ' hrs ' . 'ago';
        } elseif (intval(($time_differnce / $hours)) > 0) {
            //return date('D, d F  ago', strtotime($created_time));
            return intval(($time_differnce / $hours)) . ' hr ' . 'ago';
        } elseif (intval(($time_differnce / $minutes)) > 1) {
            return intval(($time_differnce / $minutes)) . ' mins ' . 'ago';
        } elseif (intval(($time_differnce / $minutes)) > 0) {
            return intval(($time_differnce / $minutes)) . ' min ' . 'ago';
        } elseif (intval(($time_differnce)) > 1) {
            return "Just now";
            //return intval(($time_differnce)) . ' sec';
        } else {
            return 'few seconds';
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
        $query = $this->db->query("SELECT id,category,hindi_healthwall_categorys FROM `asksaheli_main_category` order by id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                         = $row['id'];
                $category                   = $row['category'];
                $image                      = '';
                $hindi_healthwall_categorys = $row['hindi_healthwall_categorys'];
                $resultpost[]               = array(
                    "id" => $id,
                    "category" => $category,
                    "image" => $image,
                    "hindi_healthwall_category" => $hindi_healthwall_categorys
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
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
    
    public function ask_saheli_post_list($user_id, $saheli_category, $page)
    {
        
        $resultpost = array();
        $limit      = 10;
        $start      = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        if ($saheli_category == '0') {
            $query       = $this->db->query("select IFNULL(asksaheli_main_category.id,'') AS saheli_category_id,IFNULL(asksaheli_main_category.category,'') AS saheli_category, ask_saheli_post.id , ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location,ask_saheli_post.date,ask_saheli_post.repost_user_id, ask_saheli_post.repost_location,ask_saheli_post.is_repost,ask_saheli_post.repost_time, ask_saheli_post.post,ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,user_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post INNER JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category INNER JOIN user_character on user_character.id=ask_saheli_post.user_image LEFT JOIN asksaheli_main_category ON ask_saheli_post.saheli_category=asksaheli_main_category.id  WHERE ask_saheli_post.saheli_category<>'0' AND ask_saheli_post.id NOT IN (SELECT post_id FROM ask_saheli_post_hide WHERE post_id=ask_saheli_post.id AND user_id='$user_id') order by ask_saheli_post.id desc limit $start, $limit");
            $count_query = $this->db->query("select IFNULL(asksaheli_main_category.id,'') AS saheli_category_id,IFNULL(asksaheli_main_category.category,'') AS saheli_category, ask_saheli_post.id ,ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location,ask_saheli_post.date,ask_saheli_post.post,ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,user_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post INNER JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category INNER JOIN user_character on user_character.id=ask_saheli_post.user_image LEFT JOIN asksaheli_main_category ON ask_saheli_post.saheli_category=asksaheli_main_category.id  WHERE ask_saheli_post.saheli_category<>'0' AND ask_saheli_post.id NOT IN (SELECT post_id FROM ask_saheli_post_hide WHERE post_id=ask_saheli_post.id AND user_id='$user_id') order by ask_saheli_post.id desc");
        } else {
            $query       = $this->db->query("select IFNULL(asksaheli_main_category.id,'') AS saheli_category_id,IFNULL(asksaheli_main_category.category,'') AS saheli_category, ask_saheli_post.id ,ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location,ask_saheli_post.date, ask_saheli_post.repost_user_id, ask_saheli_post.repost_location,ask_saheli_post.is_repost,ask_saheli_post.repost_time, ask_saheli_post.post,ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,user_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post INNER JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category INNER JOIN user_character on user_character.id=ask_saheli_post.user_image LEFT JOIN asksaheli_main_category ON ask_saheli_post.saheli_category=asksaheli_main_category.id  WHERE ask_saheli_post.saheli_category<>'0' AND ask_saheli_post.saheli_category IN ($saheli_category) AND ask_saheli_post.id NOT IN (SELECT post_id FROM ask_saheli_post_hide WHERE post_id=ask_saheli_post.id AND user_id='$user_id') order by ask_saheli_post.id desc limit $start, $limit");
            $count_query = $this->db->query("select IFNULL(asksaheli_main_category.id,'') AS saheli_category_id,IFNULL(asksaheli_main_category.category,'') AS saheli_category, ask_saheli_post.id ,ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location,ask_saheli_post.date,ask_saheli_post.post,ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,user_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post INNER JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category INNER JOIN user_character on user_character.id=ask_saheli_post.user_image LEFT JOIN asksaheli_main_category ON ask_saheli_post.saheli_category=asksaheli_main_category.id  WHERE ask_saheli_post.saheli_category<>'0' AND ask_saheli_post.saheli_category IN ($saheli_category) AND ask_saheli_post.id NOT IN (SELECT post_id FROM ask_saheli_post_hide WHERE post_id=ask_saheli_post.id AND user_id='$user_id') order by ask_saheli_post.id desc");
        }
        $count_post = $count_query->num_rows();
        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $id      = $row['id'];
                $post_id = $row['id'];
                $post    = $row['post'];
                $post    = preg_replace('~[\r\n]+~', '', $post);
                if ($post != '') {
                    if ($post_id > '606') {
                        $decrypt = $this->decrypt($post);
                        $encrypt = $this->encrypt($decrypt);
                        if ($encrypt == $post) {
                            $post = $decrypt;
                        }
                    } else {
                        if (base64_encode(base64_decode($post)) === $post) {
                            $post = base64_decode($post);
                        }
                    }
                }
                $post_location       = $row['post_location'];
                $user_name           = $row['user_name'];
                $post_user_id        = $row['post_user_id'];
                $tag                 = $row['tag'];
                $category            = $row['category'];
                $character_image     = $row['character_image'];
                $type                = $row['type'];
                $article_title       = $row['article_title'];
                $article_image       = $row['article_image'];
                $article_domain_name = $row['article_domain_name'];
                $article_url         = $row['article_url'];
                
                $repost_user_id  = $row['repost_user_id'];
                $repost_location = $row['repost_location'];
                
                $is_repost   = $row['is_repost'];
                $repost_time = $this->get_time_difference_php($row['repost_time']);
                
                $saheli_category    = $row['saheli_category'];
                $saheli_category_id = $row['saheli_category_id'];
                
                $views       = 0;
                $video_views = 0;
                
                $query_media  = $this->db->query("SELECT ask_saheli_post_media.id AS media_id,ask_saheli_post_media.source,ask_saheli_post_media.type AS media_type,IFNULL(ask_saheli_post_media.caption,'') AS caption,ask_saheli_post_media.img_width,ask_saheli_post_media.img_height,ask_saheli_post_media.video_width,ask_saheli_post_media.video_height FROM ask_saheli_post_media INNER JOIN ask_saheli_post on ask_saheli_post_media.post_id=ask_saheli_post.id WHERE ask_saheli_post_media.post_id='$id'");
                $img_val      = '';
                $images       = '';
                $img_comma    = '';
                $img_width    = '';
                $img_height   = '';
                $video_width  = '';
                $video_height = '';
                
                $media_array = array();
                
                foreach ($query_media->result_array() as $media_row) {
                    $media_id   = $media_row['media_id'];
                    $media_type = $media_row['media_type'];
                    $source     = $media_row['source'];
                    $images     = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/' . $media_type . '/' . $source;
                    if ($media_type == 'video') {
                        $thumb = 'https://s3.amazonaws.com/medicalwale-thumbnails/videothumbnail/images/ask_saheli_images/video/' . substr_replace($source, '_thumbnail.jpeg', strrpos($source, '.') + 0);
                    } else {
                        $thumb = '';
                    }
                    $caption                 = $media_row['caption'];
                    $img_width               = $media_row['img_width'];
                    $img_height              = $media_row['img_height'];
                    $video_width             = $media_row['video_width'];
                    $video_height            = $media_row['video_height'];
                    $view_media_count        = $this->db->select('id')->from('ask_saheli_video_views')->where('media_id', $media_id)->get()->num_rows();
                    $view_media_yes_no_query = $this->db->query("SELECT id from ask_saheli_video_views WHERE media_id='$media_id' and user_id='$user_id'");
                    $view_media_yes_no       = $view_media_yes_no_query->num_rows();
                    
                    $media_array[] = array(
                        'media_id' => $media_id,
                        'type' => $media_type,
                        'images' => str_replace(' ', '%20', $images),
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
                $date = $this->get_time_difference_php($date);
                
                $like_count          = $this->db->select('id')->from('ask_saheli_likes')->where('post_id', $id)->get()->num_rows();
                $comment_count       = $this->db->select('id')->from('ask_saheli_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no         = $this->db->select('id')->from('ask_saheli_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                $character_image     = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $character_image;
                $view_count          = $this->db->select('id')->from('ask_saheli_post_views')->where('post_id', $id)->get()->num_rows();
                $view_yes_no_query   = $this->db->query("SELECT id from ask_saheli_post_views WHERE post_id='$id' and user_id='$user_id'");
                $view_post_yes_no    = $view_yes_no_query->num_rows();
                $follow_count        = $this->db->select('id')->from('ask_saheli_follow_post')->where('post_id', $id)->get()->num_rows();
                $follow_yes_no_query = $this->db->query("SELECT id from ask_saheli_follow_post where post_id='$id' and user_id='$user_id'");
                $follow_post_yes_no  = $follow_yes_no_query->num_rows();
                $share_url           = "https://medicalwale.com/share/asksaheli/" . $id;
                $is_reported_query   = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='asksaheli'");
                $is_reported         = $is_reported_query->num_rows();
                $is_post_save_query  = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='asksaheli'");
                $is_post_save        = $is_post_save_query->num_rows();
                //comments
                $query_comment       = $this->db->query("SELECT ask_saheli_comment.id,ask_saheli_comment.comment,ask_saheli_comment.date,ask_saheli_comment.user_name,user_character.image as character_image,ask_saheli_comment.user_id as post_user_id  FROM ask_saheli_comment LEFT JOIN  user_character on user_character.id=ask_saheli_comment.user_image WHERE ask_saheli_comment.post_id='$post_id' order by ask_saheli_comment.id desc LIMIT 0,3");
                $comment_counts      = $query_comment->num_rows();
                if ($comment_counts > 0) {
                    $comments = array();
                    foreach ($query_comment->result_array() as $rows) {
                        $comment_id = $rows['id'];
                        $comment    = $rows['comment'];
                        $comment    = preg_replace('~[\r\n]+~', '', $comment);
                        if ($comment_id > '1255') {
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
                        $comment_username     = $rows['user_name'];
                        $comment_image        = $rows['character_image'];
                        $comment_date         = $rows['date'];
                        $comment_post_user_id = $rows['post_user_id'];
                        $comment_date         = $this->get_time_difference_php($comment_date);
                        $comment_image        = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $comment_image;
                        
                        $comment_like_count  = $this->db->select('id')->from('ask_saheli_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                        $comment_like_yes_no = $this->db->select('id')->from('ask_saheli_comment_like')->where('user_id', $user_id)->where('comment_id', $comment_id)->get()->num_rows();
                        
                        
                        $ask_saheli_comment_reply_array = '';
                        
                        
                        
                        $query_ask_saheli_comment_reply = $this->db->query("SELECT ask_saheli_comment_reply.*,user_character.image as character_image FROM `ask_saheli_comment_reply` LEFT JOIN  user_character on user_character.id=ask_saheli_comment_reply.user_image WHERE `comment_id` = '$comment_id'");
                        $comment_reply                  = array();
                        $ask_saheli_comment_count_reply = $query_ask_saheli_comment_reply->num_rows();
                        if ($ask_saheli_comment_count_reply > 0) {
                            foreach ($query_ask_saheli_comment_reply->result_array() as $rows_reply) {
                                $comment_id             = $rows_reply['comment_id'];
                                $image                  = $rows_reply['character_image'];
                                $comment_reply_id       = $rows_reply['id'];
                                $comment_reply_post_id  = $rows_reply['post_id'];
                                $comment_reply_username = $rows_reply['user_name'];
                                
                                $comment_reply_date    = $rows_reply['date'];
                                $comment_reply_user_id = $rows_reply['user_id'];
                                
                                
                                $comment_reply = $rows_reply['comment'];
                                $comment_reply = preg_replace('~[\r\n]+~', '', $comment_reply);
                                
                                if ($comment_reply != '' && !is_numeric($comment_reply)) {
                                    $comment_decrypt = $this->decrypt($comment_reply);
                                    $comment_encrypt = $this->encrypt($comment_decrypt);
                                    if ($comment_encrypt == $comment_reply) {
                                        $comment_reply = $comment_decrypt;
                                    }
                                }
                                $comment_reply_like_count  = $this->db->select('id')->from('ask_saheli_comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
                                $comment_reply_like_yes_no = $this->db->select('id')->from('ask_saheli_comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
                                $comment_reply_date        = $this->get_time_difference_php($comment_reply_date);
                                $comment_reply_img_count   = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                                $comment_reply_userimage   = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $image;
                                $query_reply_listing_type  = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
                                $get_reply_type            = $query_reply_listing_type->row_array();
                                $com_reply_listing_type    = $get_reply_type['vendor_id'];
                                
                                
                                $comment_reply_array[] = array(
                                    'id' => $comment_reply_id,
                                    'listing_type' => $com_reply_listing_type,
                                    'comment_user_id' => $comment_reply_user_id,
                                    'username' => $comment_reply_username,
                                    'userimage' => $comment_reply_userimage,
                                    'like_count' => $comment_reply_like_count,
                                    'like_yes_no' => $comment_reply_like_yes_no,
                                    'post_id' => $comment_reply_post_id,
                                    'comment' => $comment_reply,
                                    'comment_date' => $comment_reply_date
                                );
                            }
                        } else {
                            $comment_reply_array = array();
                        }
                        
                        $comments[] = array(
                            'id' => $comment_id,
                            'comment_user_id' => $comment_post_user_id,
                            'username' => $comment_username,
                            'userimage' => $comment_image,
                            'like_count' => $comment_like_count,
                            'like_yes_no' => $comment_like_yes_no,
                            'comment' => $comment,
                            'comment_date' => $comment_date,
                            'comment_reply' => $comment_reply_array
                        );
                    }
                } else {
                    $comments = array();
                }
                //repost
                $repost_user_name = "";
                
                $repost = array();
                if ($is_repost) {
                    if (!empty($repost_user_id) && $repost_user_id != "") {
                        
                        $query = $this->db->query("SELECT user_avatar.user_name, user_character.image FROM user_character INNER JOIN user_avatar ON(user_character.id = user_avatar.user_image) WHERE user_avatar.user_id = '$repost_user_id'")->row();
                        if (!empty($query)) {
                            $user_name_avatar = $query->user_name;
                            $image            = $query->image;
                        } else {
                            $user_name_avatar = "";
                            $image            = "";
                        }
                        $repost_user_name = $this->db->select('*')->where('id', $repost_user_id)->get('users')->row()->name;
                        
                        $repost[] = array(
                            'repost_user_id' => $repost_user_id,
                            'repost_user_name' => $user_name_avatar,
                            'repost_location' => $repost_location,
                            'repost_time' => $repost_time,
                            //'title' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$query->title,
                            'repost_user_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/" . $image
                        );
                    }
                    
                    if (is_null($repost_user_name) || $repost_user_name == "") {
                        $repost_user_name = '';
                    }
                } else {
                    $repost = array();
                }
                
                //comments
                $resultpost[] = array(
                    'id' => $id,
                    'post_type' => $type,
                    'post_location' => $post_location,
                    'saheli_category' => $saheli_category,
                    'saheli_category_id' => $saheli_category_id,
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
                    'user_image' => $character_image,
                    'character_image' => $character_image,
                    'date' => $date,
                    'is_reported' => $is_reported,
                    'is_post_save' => $is_post_save,
                    'comments' => $comments,
                    'is_repost' => $is_repost,
                    'repost' => $repost
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
    
    public function ask_saheli_post_details($user_id, $post_id)
    {
        $resultpost = array();
        $query      = $this->db->query("select IFNULL(asksaheli_main_category.id,'') AS saheli_category_id,IFNULL(asksaheli_main_category.category,'') AS saheli_category, ask_saheli_post.id ,ask_saheli_post.type,IFNULL(ask_saheli_post.post_location,'') AS post_location,ask_saheli_post.date,ask_saheli_post.post,ask_saheli_post.repost_user_id, ask_saheli_post.repost_location,ask_saheli_post.is_repost,ask_saheli_post.repost_time, ask_saheli_post.tag,ask_saheli_category.category,ask_saheli_post.user_name,user_character.image as character_image,ask_saheli_post.article_title,ask_saheli_post.article_image,ask_saheli_post.article_domain_name,ask_saheli_post.article_url,ask_saheli_post.user_id as post_user_id FROM ask_saheli_post INNER JOIN ask_saheli_category on ask_saheli_category.id=ask_saheli_post.category INNER JOIN user_character on user_character.id=ask_saheli_post.user_image LEFT JOIN asksaheli_main_category ON ask_saheli_post.saheli_category=asksaheli_main_category.id  WHERE ask_saheli_post.id='$post_id' order by ask_saheli_post.id desc");
        
        $count_post = $query->num_rows();
        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $id                 = $row['id'];
                $post               = $row['post'];
                $saheli_category    = $row['saheli_category'];
                $saheli_category_id = $row['saheli_category_id'];
                $post               = preg_replace('~[\r\n]+~', '', $post);
                if ($id >= '606') {
                    $decrypt = $this->decrypt($post);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $post) {
                        $post = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($post)) === $post) {
                        $post = base64_decode($post);
                    }
                }
                $post_location       = $row['post_location'];
                $user_name           = $row['user_name'];
                $post_user_id        = $row['post_user_id'];
                $tag                 = $row['tag'];
                $category            = $row['category'];
                $character_image     = $row['character_image'];
                $type                = $row['type'];
                $article_title       = $row['article_title'];
                $article_image       = $row['article_image'];
                $article_domain_name = $row['article_domain_name'];
                $article_url         = $row['article_url'];
                
                $repost_user_id  = $row['repost_user_id'];
                $repost_location = $row['repost_location'];
                $is_repost       = $row['is_repost'];
                $repost_time     = $this->get_time_difference_php($row['repost_time']);
                
                $views       = 0;
                $video_views = 0;
                
                $query_media  = $this->db->query("SELECT ask_saheli_post_media.id AS media_id,ask_saheli_post_media.source,ask_saheli_post_media.type AS media_type,IFNULL(ask_saheli_post_media.caption,'') AS caption,ask_saheli_post_media.img_width,ask_saheli_post_media.img_height,ask_saheli_post_media.video_width,ask_saheli_post_media.video_height FROM ask_saheli_post_media INNER JOIN ask_saheli_post on ask_saheli_post_media.post_id=ask_saheli_post.id WHERE ask_saheli_post_media.post_id='$id'");
                $img_val      = '';
                $images       = '';
                $img_comma    = '';
                $img_width    = '';
                $img_height   = '';
                $video_width  = '';
                $video_height = '';
                
                $media_array = array();
                
                foreach ($query_media->result_array() as $media_row) {
                    $media_id   = $media_row['media_id'];
                    $media_type = $media_row['media_type'];
                    $source     = $media_row['source'];
                    $images     = 'https://d2c8oti4is0ms3.cloudfront.net/images/ask_saheli_images/' . $media_type . '/' . $source;
                    if ($media_type == 'video') {
                        $thumb = 'https://s3.amazonaws.com/medicalwale-thumbnails/videothumbnail/images/ask_saheli_images/video/' . substr_replace($source, '_thumbnail.jpeg', strrpos($source, '.') + 0);
                    } else {
                        $thumb = '';
                    }
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
                        'images' => str_replace(' ', '%20', $images),
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
                $date = $this->get_time_difference_php($date);
                
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
                $share_url           = "https://medicalwale.com/share/asksaheli/" . $id;
                
                $is_reported_query  = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='asksaheli'");
                $is_reported        = $is_reported_query->num_rows();
                $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='asksaheli'");
                $is_post_save       = $is_post_save_query->num_rows();
                
                //repost
                $repost_user_name = "";
                
                $repost = array();
                if ($is_repost) {
                    if (!empty($repost_user_id) && $repost_user_id != "") {
                        
                        $query = $this->db->query("SELECT user_avatar.user_name, user_character.image FROM user_character INNER JOIN user_avatar ON(user_character.id = user_avatar.user_image) WHERE user_avatar.user_id = '$repost_user_id'")->row();
                        if (!empty($query)) {
                            $repost_user_name  = $query->user_name;
                            $repost_user_image = $query->image;
                        } else {
                            $repost_user_name  = "";
                            $repost_user_image = "user_avatar.jpg";
                        }
                        
                        $repost[] = array(
                            'repost_user_id' => $repost_user_id,
                            'repost_user_name' => $query->user_name,
                            'repost_location' => $repost_location,
                            'repost_time' => $repost_time,
                            //'title' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$query->title,
                            'repost_user_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/" . $query->image
                        );
                    }
                    
                    if (is_null($repost_user_name) || $repost_user_name == "") {
                        $repost_user_name = '';
                    }
                } else {
                    $repost = array();
                }
                
                //comments
                
                $query_comment = $this->db->query("SELECT ask_saheli_comment.id,ask_saheli_comment.comment,ask_saheli_comment.date,ask_saheli_comment.user_name,user_character.image as character_image,ask_saheli_comment.user_id as post_user_id  FROM ask_saheli_comment LEFT JOIN  user_character on user_character.id=ask_saheli_comment.user_image WHERE ask_saheli_comment.post_id='$post_id' order by ask_saheli_comment.id desc LIMIT 0,3");
                
                $comment_counts = $query_comment->num_rows();
                if ($comment_counts > 0) {
                    $comments = array();
                    foreach ($query_comment->result_array() as $rows) {
                        $comment_id = $rows['id'];
                        $comment    = $rows['comment'];
                        $comment    = preg_replace('~[\r\n]+~', '', $comment);
                        if ($comment_id > '1255') {
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
                        $comment_username     = $rows['user_name'];
                        $comment_image        = $rows['character_image'];
                        $comment_date         = $rows['date'];
                        $comment_post_user_id = $rows['post_user_id'];
                        $comment_date         = $this->get_time_difference_php($comment_date);
                        $comment_image        = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $comment_image;
                        
                        $comment_like_count  = $this->db->select('id')->from('ask_saheli_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                        $comment_like_yes_no = $this->db->select('id')->from('ask_saheli_comment_like')->where('user_id', $user_id)->where('comment_id', $comment_id)->get()->num_rows();
                        
                        
                        $query_ask_saheli_comment_reply = $this->db->query("SELECT ask_saheli_comment_reply.*,user_character.image as character_image FROM `ask_saheli_comment_reply` LEFT JOIN  user_character on user_character.id=ask_saheli_comment_reply.user_image WHERE `comment_id` = '$comment_id'");
                        $comment_reply                  = array();
                        $ask_saheli_comment_count_reply = $query_ask_saheli_comment_reply->num_rows();
                        if ($ask_saheli_comment_count_reply > 0) {
                            foreach ($query_ask_saheli_comment_reply->result_array() as $rows_reply) {
                                $comment_id             = $rows_reply['comment_id'];
                                $image                  = $rows_reply['character_image'];
                                $comment_reply_id       = $rows_reply['id'];
                                $comment_reply_post_id  = $rows_reply['post_id'];
                                $comment_reply_username = $rows_reply['user_name'];
                                
                                $comment_reply_date    = $rows_reply['date'];
                                $comment_reply_user_id = $rows_reply['user_id'];
                                $comment_reply         = $rows_reply['comment'];
                                $comment_reply         = preg_replace('~[\r\n]+~', '', $comment_reply);
                                $comment_decrypt       = $this->decrypt($comment_reply);
                                $comment_encrypt       = $this->encrypt($comment_decrypt);
                                if ($comment_encrypt == $comment_reply) {
                                    $comment_reply = $comment_decrypt;
                                }
                                $comment_reply_like_count  = $this->db->select('id')->from('ask_saheli_comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
                                $comment_reply_like_yes_no = $this->db->select('id')->from('ask_saheli_comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
                                $comment_reply_date        = $this->get_time_difference_php($comment_reply_date);
                                $comment_reply_img_count   = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                                $comment_reply_userimage   = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $image;
                                $query_reply_listing_type  = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
                                $get_reply_type            = $query_reply_listing_type->row_array();
                                $com_reply_listing_type    = $get_reply_type['vendor_id'];
                                
                                
                                $comment_reply_array[] = array(
                                    'id' => $comment_reply_id,
                                    'listing_type' => $com_reply_listing_type,
                                    'comment_user_id' => $comment_reply_user_id,
                                    'username' => $comment_reply_username,
                                    'userimage' => $comment_reply_userimage,
                                    'like_count' => $comment_reply_like_count,
                                    'like_yes_no' => $comment_reply_like_yes_no,
                                    'post_id' => $comment_reply_post_id,
                                    'comment' => $comment_reply,
                                    'comment_date' => $comment_reply_date
                                );
                            }
                        } else {
                            $comment_reply_array = array();
                        }
                        
                        
                        $comments[] = array(
                            'id' => $comment_id,
                            'comment_user_id' => $comment_post_user_id,
                            'username' => $comment_username,
                            'userimage' => $comment_image,
                            'like_count' => $comment_like_count,
                            'like_yes_no' => $comment_like_yes_no,
                            'comment' => $comment,
                            'comment_date' => $comment_date,
                            'comment_reply' => $comment_reply_array
                        );
                    }
                } else {
                    $comments = array();
                }
                
                //comments
                
                
                $resultpost[] = array(
                    'id' => $id,
                    'post_type' => $type,
                    'post_user_id' => $post_user_id,
                    'post_location' => $post_location,
                    'saheli_category' => $saheli_category,
                    'saheli_category_id' => $saheli_category_id,
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
                    'comments' => $comments,
                    'is_repost' => $is_repost,
                    'repost' => $repost
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
    
    public function ask_saheli_post_like($user_id, $post_id, $user_name, $user_image, $post_user_id)
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
            
            if ($user_name == '0' || $user_name == '') {
                $user_name = 'Someone';
            }
            
            if ($user_image == '0') {
                $userimage = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/ofw/ask_saheli.png';
            } else {
                $img_query       = $this->db->query("select user_character.image as character_image FROM ask_saheli_likes INNER JOIN user_character on user_character.id=ask_saheli_likes.user_image  WHERE  ask_saheli_likes.user_id='$user_id' AND ask_saheli_likes.post_id='$post_id'");
                $getimg          = $img_query->row_array();
                $character_image = $getimg['character_image'];
                $userimage       = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $character_image;
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
                $title        = $usr_name . ' Beats on your Post';
                $msg          = $usr_name . ' Beats on your post click here to view post.';
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
    
    public function insert_notification_post_follow($user_id, $post_id, $name, $doctor_id)
    {
        $data = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'timeline_id' => $user_id,
            'type' => 'comment',
            'seen' => '1',
            'notified_by' => $doctor_id,
            'description' => ' is commented your post',
            'created_at' => curr_date(),
            'updated_at' => curr_date()
            
        );
        //print_r($data);
        $this->db->insert("notifications", $data);
        
        if ($this->db->affected_rows() > 0) {
            
            return true; // to the controller
        } else {
            return false;
        }
    }
    
    public function ask_saheli_post_comment($user_id, $post_id, $comment, $user_name, $user_image, $post_user_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at         = date('Y-m-d H:i:s');
        $ask_saheli_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'user_name' => $user_name,
            'user_image' => $user_image,
            'date' => $created_at
        );
        //print_r($ask_saheli_comment);
        $this->db->insert('ask_saheli_comment', $ask_saheli_comment);
        
        // WEB NOTICATIONS
        $this->insert_notification_post_follow($user_id, $post_id, 'user', $user_id);
        if ($user_name == '0' || $user_name == '') {
            $user_name = 'Someone';
        }
        
        if ($user_image == '0') {
            $userimage = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/ofw/ask_saheli.png';
        } else {
            $img_query       = $this->db->query("select user_character.image as character_image FROM ask_saheli_comment INNER JOIN user_character on user_character.id=ask_saheli_comment.user_image  WHERE  ask_saheli_comment.user_id='$user_id' AND ask_saheli_comment.post_id='$post_id'");
            $getimg          = $img_query->row_array();
            $character_image = $getimg['character_image'];
            $userimage       = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $character_image;
        }
        
        
        //$post_follows = $this->db->query("SELECT  GROUP_CONCAT(user_id) AS follow_users FROM ask_saheli_follow_post WHERE post_id='$post_id' AND user_id<>'$post_user_id'");
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
                $title        = $user_name . ' commented on post that you have followed';
                $msg          = $user_name . ' commented on post that you have followed click here to view post.';
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
            $img_url       = $userimage;
            $tag           = 'text';
            $key_count     = '1';
            $title         = $user_name . ' commented on your post';
            $msg           = $user_name . ' commented on your post click here to view post.';
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
        
        $resultpost   = '';
        $review_count = $this->db->select('id')->from('ask_saheli_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT ask_saheli_comment.id,ask_saheli_comment.comment,ask_saheli_comment.date,ask_saheli_comment.user_name,user_character.image as character_image,ask_saheli_comment.user_id as post_user_id  FROM ask_saheli_comment LEFT JOIN  user_character on user_character.id=ask_saheli_comment.user_image WHERE ask_saheli_comment.post_id='$post_id' order by ask_saheli_comment.id asc");
            
            foreach ($query->result_array() as $row) {
                $comment_id = $row['id'];
                $comment    = $row['comment'];
                $comment    = preg_replace('~[\r\n]+~', '', $comment);
                if ($comment_id > '1255') {
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
                $username     = $row['user_name'];
                $image        = $row['character_image'];
                $date         = $row['date'];
                $post_user_id = $row['post_user_id'];
                $comment_date = $this->get_time_difference_php($date);
                $images       = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $image;
                
                $like_count  = $this->db->select('id')->from('ask_saheli_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('ask_saheli_comment_like')->where('user_id', $user_id)->where('comment_id', $comment_id)->get()->num_rows();
                
                
                $comment_reply_array = '';
                
                
                $query_ask_saheli_comment_reply = $this->db->query("SELECT ask_saheli_comment_reply.*,user_character.image as character_image FROM `ask_saheli_comment_reply` LEFT JOIN  user_character on user_character.id=ask_saheli_comment_reply.user_image WHERE `comment_id` = '$comment_id'");
                $comment_reply                  = array();
                $ask_saheli_comment_count_reply = $query_ask_saheli_comment_reply->num_rows();
                if ($ask_saheli_comment_count_reply > 0) {
                    foreach ($query_ask_saheli_comment_reply->result_array() as $rows_reply) {
                        $comment_id             = $rows_reply['comment_id'];
                        $image                  = $rows_reply['character_image'];
                        $comment_reply_id       = $rows_reply['id'];
                        $comment_reply_post_id  = $rows_reply['post_id'];
                        $comment_reply_username = $rows_reply['user_name'];
                        
                        $comment_reply_date    = $rows_reply['date'];
                        $comment_reply_user_id = $rows_reply['user_id'];
                        $comment_reply         = $rows_reply['comment'];
                        $comment_reply         = preg_replace('~[\r\n]+~', '', $comment_reply);
                        $comment_decrypt       = $this->decrypt($comment_reply);
                        $comment_encrypt       = $this->encrypt($comment_decrypt);
                        if ($comment_encrypt == $comment_reply) {
                            $comment_reply = $comment_decrypt;
                        }
                        $comment_reply_like_count  = $this->db->select('id')->from('ask_saheli_comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
                        $comment_reply_like_yes_no = $this->db->select('id')->from('ask_saheli_comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
                        $comment_reply_date        = $this->get_time_difference_php($comment_reply_date);
                        $comment_reply_img_count   = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                        $comment_reply_userimage   = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $image;
                        $query_reply_listing_type  = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
                        $get_reply_type            = $query_reply_listing_type->row_array();
                        $com_reply_listing_type    = $get_reply_type['vendor_id'];
                        
                        
                        $comment_reply_array[] = array(
                            'id' => $comment_reply_id,
                            'listing_type' => $com_reply_listing_type,
                            'comment_user_id' => $comment_reply_user_id,
                            'username' => $comment_reply_username,
                            'userimage' => $comment_reply_userimage,
                            'like_count' => $comment_reply_like_count,
                            'like_yes_no' => $comment_reply_like_yes_no,
                            'post_id' => $comment_reply_post_id,
                            'comment' => $comment_reply,
                            'comment_date' => $comment_reply_date
                        );
                    }
                } else {
                    $comment_reply_array = array();
                }
                
                
                
                
                $resultpost[] = array(
                    'id' => $comment_id,
                    'comment_user_id' => $post_user_id,
                    'username' => $username,
                    'post_id' => $post_id,
                    'userimage' => $images,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment' => $comment,
                    'comment_date' => $comment_date,
                    'comment_reply' => $comment_reply_array
                );
            }
        } else {
            $resultpost = array();
        }
        
        return $resultpost;
    }
    
    public function ask_saheli_post_comment_like($user_id, $comment_id, $user_name, $user_image, $post_id, $comment_user_id)
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
            
            
            
            
            if ($user_name == '0' || $user_name == '') {
                $user_name = 'Someone';
            }
            
            if ($user_image == '0') {
                $userimage = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/ofw/ask_saheli.png';
            } else {
                $img_query       = $this->db->query("select user_character.image as character_image FROM ask_saheli_comment_like INNER JOIN user_character on user_character.id=ask_saheli_comment_like.user_image  WHERE  ask_saheli_comment_like.user_id='$user_id' AND ask_saheli_comment_like.comment_id='$comment_id'");
                $getimg          = $img_query->row_array();
                $character_image = $getimg['character_image'];
                $userimage       = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $character_image;
            }
            
            $customer_token = $this->db->query("SELECT token, agent, token_status FROM users WHERE id='$comment_user_id' AND id<>'$user_id'");
            
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
                $title        = $user_name . ' Beats on your Comment';
                $msg          = $user_name . ' Beats on your comment click here to view post.';
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
    
    public function ask_saheli_add_question($user_id, $user_name, $user_image, $question, $category, $post_location, $saheli_category)
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
            'post_location' => $post_location,
            'saheli_category' => $saheli_category,
            'date' => $date
        );
        $this->db->insert('ask_saheli_post', $ask_saheli_add_question);
        $quetion_id = $this->db->insert_id();
        $doctor_id  = '1625';
        $this->insert_notification_add_post($user_id, $quetion_id, 'user', $doctor_id);
        
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    public function insert_notification_add_post($user_id, $post_id, $name, $doctor_id)
    {
        $data = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'timeline_id' => $user_id,
            'type' => 'comment',
            'seen' => '1',
            'notified_by' => $doctor_id,
            'description' => ' ask you a question?',
            'created_at' => curr_date(),
            'updated_at' => curr_date()
            
        );
        //print_r($data);
        $this->db->insert("notifications", $data);
        
        if ($this->db->affected_rows() > 0) {
            
            return true; // to the controller
        } else {
            return false;
        }
    }
    
    public function insert_notification($user_id, $post_id, $name, $doctor_id)
    {
        $data = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'timeline_id' => $user_id,
            'type' => 'comment',
            'seen' => '1',
            'notified_by' => $doctor_id,
            'description' => ' is replied your comment',
            'created_at' => curr_date(),
            'updated_at' => curr_date()
            
        );
        //print_r($data);
        $this->db->insert("notifications", $data);
        
        if ($this->db->affected_rows() > 0) {
            
            return true; // to the controller
        } else {
            return false;
        }
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
    
    public function ask_saheli_post_hide($user_id, $post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `ask_saheli_post_hide` WHERE  user_id='$user_id' and post_id='$post_id'");
        $count       = $count_query->num_rows();
        
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
    
    public function ask_saheli_post_delete($user_id, $post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        //$count_query = $this->db->query("SELECT id from `ask_saheli_post` WHERE  user_id='$user_id' and id='$post_id'");
        $count_query = $this->db->query("SELECT id from `ask_saheli_post` WHERE  id='$post_id'");
        
        $count = $count_query->num_rows();
        
        if ($count > 0) {
            $this->db->query("DELETE FROM `ask_saheli_post` WHERE id='$post_id'");
            
            //unlink images
            $media_query = $this->db->query("SELECT ask_saheli_post_media.source,ask_saheli_post_media.type AS media_type FROM ask_saheli_post_media INNER JOIN ask_saheli_post on ask_saheli_post_media.post_id=ask_saheli_post.id WHERE ask_saheli_post_media.post_id='$post_id'");
            
            foreach ($media_query->result_array() as $media_row) {
                $source     = $media_row['source'];
                $media_type = $media_row['media_type'];
                $file       = 'images/ask_saheli_images/' . $media_type . '/' . $source;
                @unlink(trim($file));
                DeleteFromToS3($file);
            }
            
            //unlink images ends
            $this->db->query("DELETE FROM `ask_saheli_post_media` WHERE post_id='$post_id'");
            
            return array(
                'status' => 200,
                'message' => 'deleted'
            );
        } else {
            return array(
                'status' => 401,
                'message' => 'unauthorized'
            );
        }
    }
    
    public function ask_saheli_edit_question($post_id, $user_id, $user_name, $user_image, $question, $category, $saheli_category)
    {
        
        $question = preg_replace('~[\r\n]+~', '', $question);
        if ($post_id > '2938') {
            $question = $this->encrypt($question);
        } else {
            $question = base64_encode($question);
        }
        $this->db->query("UPDATE `ask_saheli_post` SET `user_name`='$user_name',`user_image`='$user_image',`post`='$question',`category`='$category',`saheli_category`='$saheli_category' WHERE id='$post_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    public function repost($repost_user_id, $post_id, $repost_location)
    {
        
        $query = $this->db->query("SELECT id FROM users where id = '$repost_user_id' ");
        $count = $query->num_rows();
        
        if ($count > 0) {
            
            $query               = $this->db->query("SELECT * FROM ask_saheli_post where id = '$post_id' ")->row();
            $post_user_id        = $query->user_id;
            $category            = $query->category;
            $saheli_category     = $query->saheli_category;
            $post_id             = $query->id;
            $post                = $query->post;
            $article_desc        = $query->article_desc;
            $tag                 = $query->tag;
            $type                = $query->type;
            $post_location       = $query->post_location;
            $user_image          = $query->user_image;
            $user_name           = $query->user_name;
            $article_title       = $query->article_title;
            $article_image       = $query->article_image;
            $article_domain_name = $query->article_domain_name;
            $article_url         = $query->article_url;
            $date                = $query->date;
            //date_default_timezone_set('Asia/Calcutta');
            //$today = strtotime(date('Y-m-d H:i:s'));
            //$repost_time = $today;
            $repost_location     = $repost_location;
            
            $is_repost = $query->is_repost;
            
            $post = preg_replace('~[\r\n]+~', '', $post);
            if ($post_id > '606') {
                $decrypt = $this->decrypt($post);
                $encrypt = $this->encrypt($decrypt);
                if ($encrypt == $post) {
                    $post = $decrypt;
                }
            } else {
                if (base64_encode(base64_decode($post)) === $post) {
                    $post = base64_decode($post);
                }
            }
            $post = $this->encrypt($post);
            $this->db->query("INSERT INTO ask_saheli_post(user_id, category, saheli_category, post, article_desc, tag, type, post_location, user_image, user_name, article_title, article_image, article_domain_name, article_url, date, repost_user_id, repost_location, is_repost) VALUES ('$post_user_id', '$category', '$saheli_category', '$post', '$article_desc', '$tag', '$type', '$post_location', '$user_image', '$user_name', '$article_title', '$article_image', '$article_domain_name', '$article_url', '$date', '$repost_user_id', '$repost_location', '1')");
            
            $last_id = $this->db->insert_id();
            
            $query1 = $this->db->query("SELECT * FROM ask_saheli_post_media where post_id = '$post_id' ");
            $result = "";
            foreach ($query1->result_array() as $row) {
                
                $post_id      = $row['post_id'];
                $caption      = $row['caption'];
                $type         = $row['type'];
                $source       = $row['source'];
                $img_width    = $row['img_width'];
                $img_height   = $row['img_height'];
                $video_width  = $row['video_width'];
                $video_height = $row['video_height'];
                $created_at   = $row['created_at'];
                $updated_at   = $row['updated_at'];
                $deleted_at   = $row['deleted_at'];
                
                $this->db->query("INSERT INTO ask_saheli_post_media(post_id, caption, type, source, img_height, img_width, video_height, video_width, created_at, updated_at, deleted_at) VALUES ('$last_id', '$caption', '$type', '$source', '$img_height', '$img_width', '$video_height', '$video_width', '$created_at', '$updated_at', '$deleted_at')");
                if (count($row) > 0) {
                    $result = 1;
                }
                $result = $this->db->affected_rows();
            }
            
            //$user_plike = $this->db->query("SELECT name FROM users WHERE id='$repost_user_id'");
            $query = $this->db->query("SELECT user_avatar.user_name, user_character.image FROM user_character INNER JOIN user_avatar ON(user_character.id = user_avatar.user_image) WHERE user_avatar.user_id = '$repost_user_id'")->row();
            if (!empty($query)) {
                $user_name = $query->user_name;
                $img_file  = $query->image;
            } else {
                $user_name = "";
                $img_file  = "user_avatar.jpg";
            }
            //$img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
            if (!empty($query)) {
                //$profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            
            
            //$customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id='$post_user_id' AND id<>'$post_user_id'");
            $customer_token = $this->db->query("SELECT token,agent,token_status FROM users WHERE id='$post_user_id'");
            
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $usr_name     = $query->user_name;
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $title        = $usr_name . ' Reposted your Post';
                $msg          = $usr_name . ' Reposted your post click here to view post.';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $last_id);
            }
            if ($last_id) {
                
                return $result = array(
                    'status' => 200,
                    'message' => 'success'
                );
            } else {
                
                return $result = array(
                    'status' => 201,
                    'message' => 'failed'
                );
            }
        }
    }
    
    public function ask_saheli_post_comment_reply($user_id, $post_id, $comment_id, $comment, $post_user_id, $comment_user_id, $user_name, $user_image)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $comments   = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment_id' => $comment_id,
            'comment' => $comment,
            'date' => $created_at,
            'user_name' => $user_name,
            'user_image' => $user_image
            //'updated_at' => $created_at
        );
        $this->db->insert('ask_saheli_comment_reply', $comments);
        $id                  = $this->db->insert_id();
        $comment_reply_array = '';
        
        $query_comment_reply    = $this->db->query("SELECT ask_saheli_comment_reply.id,ask_saheli_comment_reply.comment_id,ask_saheli_comment_reply.post_id,ask_saheli_comment_reply.comment,ask_saheli_comment_reply.date,ask_saheli_comment_reply.user_id,ask_saheli_comment_reply.user_image,ask_saheli_comment_reply.user_name,user_character.id as image_id,user_character.image from ask_saheli_comment_reply LEFT JOIN user_character ON user_character.id = ask_saheli_comment_reply.user_image WHERE ask_saheli_comment_reply.id='$id'")->row();
        $comment_reply_id       = $query_comment_reply->id;
        $comment_reply_username = $query_comment_reply->user_name;
        $comment_reply_post_id  = $query_comment_reply->post_id;
        $comment_reply_date     = $query_comment_reply->date;
        $comment_reply_user_id  = $query_comment_reply->user_id;
        $comment_reply          = $query_comment_reply->comment;
        $image                  = $query_comment_reply->image;
        $comment_reply          = preg_replace('~[\r\n]+~', '', $comment_reply);
        $comment_decrypt        = $this->decrypt($comment_reply);
        $comment_encrypt        = $this->encrypt($comment_decrypt);
        if ($comment_encrypt == $comment_reply) {
            $comment_reply = $comment_decrypt;
        }
        $comment_reply_like_count  = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
        $comment_reply_like_yes_no = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
        $comment_reply_date        = $this->get_time_difference_php($comment_reply_date);
        $comment_reply_userimage   = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $image;
        $query_reply_listing_type  = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
        $get_reply_type            = $query_reply_listing_type->row_array();
        $com_reply_listing_type    = $get_reply_type['vendor_id'];
        
        $comment_reply_array[] = array(
            'id' => $comment_reply_id,
            'listing_type' => $com_reply_listing_type,
            'comment_user_id' => $comment_reply_user_id,
            'username' => $comment_reply_username,
            'userimage' => $comment_reply_userimage,
            'like_count' => $comment_reply_like_count,
            'like_yes_no' => $comment_reply_like_yes_no,
            'post_id' => $comment_reply_post_id,
            'comment' => $comment_reply,
            'comment_date' => $comment_reply_date
        );
        
        if ($user_name == '0' || $user_name == '') {
            $user_name = 'Someone';
        }
        
        if ($user_image == '0') {
            $userimage = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/ofw/ask_saheli.png';
        } else {
            $img_query       = $this->db->query("select user_character.image as character_image FROM ask_saheli_comment INNER JOIN user_character on user_character.id=ask_saheli_comment.user_image  WHERE  ask_saheli_comment.user_id='$user_id' AND ask_saheli_comment.post_id='$post_id'");
            $getimg          = $img_query->row_array();
            $character_image = $getimg['character_image'];
            $userimage       = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $character_image;
        }
        
        $post_follows     = $this->db->query("SELECT GROUP_CONCAT(user_id) AS follow_users FROM post_follows WHERE post_id='$post_id' AND user_id<>'$post_user_id'");
        $get_post_follows = $post_follows->row_array();
        $follow_users     = $get_post_follows['follow_users'];
        $follow_user_ids  = explode(',', $follow_users);
        
        //comment user notifications
        $customer_token3       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id='$comment_user_id' AND $comment_user_id<>$user_id");
        $customer_token_count3 = $customer_token3->num_rows();
        if ($customer_token_count3 > 0) {
            $token_status3 = $customer_token3->row_array();
            $agent         = $token_status3['agent'];
            $reg_id        = $token_status3['token'];
            $img_url       = $userimage;
            $tag           = 'text';
            $key_count     = '1';
            $title         = $user_name . ' Replied on your Comment ';
            $msg           = $user_name . ' Replied on your Comment click here to view the post.';
            $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
        }
        
        $comments_query = $this->db->query("SELECT id from ask_saheli_comment where post_id='$post_id'");
        $total_comment  = $comments_query->num_rows();
        return array(
            'status' => 201,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment,
            'comment_return' => $comment_reply_array
        );
    }
    
    public function ask_saheli_post_comment_reply_like($user_id, $comment_id, $user_name, $user_image, $post_id, $comment_user_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from ask_saheli_comment_reply_likes WHERE comment_reply_id='$comment_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `ask_saheli_comment_reply_likes` WHERE user_id='$user_id' and comment_reply_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from ask_saheli_comment_reply_likes WHERE comment_reply_id='$comment_id'");
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
                'comment_reply_id' => $comment_id,
                'user_name' => $user_name,
                'user_image' => $user_image
            );
            $this->db->insert('ask_saheli_comment_reply_likes', $ask_saheli_comment_like);
            if ($user_name == '0' || $user_name == '') {
                $user_name = 'Someone';
            }
            if ($user_image == '0') {
                $userimage = 'https://s3.amazonaws.com/medicalwale/images/medicalwale_mobile_app_icons/ofw/ask_saheli.png';
            } else {
                $img_query       = $this->db->query("select user_character.image as character_image FROM ask_saheli_comment_reply_likes INNER JOIN user_character on user_character.id=ask_saheli_comment_reply_likes.user_image  WHERE  ask_saheli_comment_reply_likes.user_id='$user_id' AND ask_saheli_comment_reply_likes.comment_reply_id='$comment_id'");
                $getimg          = $img_query->row_array();
                $character_image = $getimg['character_image'];
                $userimage       = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $character_image;
            }
            $customer_token       = $this->db->query("SELECT token, agent, token_status FROM users WHERE id='$comment_user_id' AND id<>'$user_id'");
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                //$getusr       = $user_comment_plike->row_array();
                $usr_name     = $user_name;
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $title        = $user_name . ' Beats on your Reply';
                $msg          = $user_name . ' Beats on your Reply click here to view the Reply.';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
            }
            
            $comment_query      = $this->db->query("SELECT id from `ask_saheli_comment_reply_likes`  WHERE comment_reply_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }
    
    public function ask_saheli_post_comment_all_reply_list($comment_id, $user_id)
    {
        
        $query_ask_saheli_comment_reply = $this->db->query("SELECT ask_saheli_comment_reply.*,user_character.image as character_image FROM `ask_saheli_comment_reply` LEFT JOIN  user_character on user_character.id=ask_saheli_comment_reply.user_image WHERE `comment_id` = '$comment_id'");
        $comment_reply                  = array();
        $ask_saheli_comment_count_reply = $query_ask_saheli_comment_reply->num_rows();
        if ($ask_saheli_comment_count_reply > 0) {
            foreach ($query_ask_saheli_comment_reply->result_array() as $rows_reply) {
                $comment_id             = $rows_reply['comment_id'];
                $image                  = $rows_reply['character_image'];
                $comment_reply_id       = $rows_reply['id'];
                $comment_reply_post_id  = $rows_reply['post_id'];
                $comment_reply_username = $rows_reply['user_name'];
                
                $comment_reply_date    = $rows_reply['date'];
                $comment_reply_user_id = $rows_reply['user_id'];
                $comment_reply         = $rows_reply['comment'];
                $comment_reply         = preg_replace('~[\r\n]+~', '', $comment_reply);
                $comment_decrypt       = $this->decrypt($comment_reply);
                $comment_encrypt       = $this->encrypt($comment_decrypt);
                if ($comment_encrypt == $comment_reply) {
                    $comment_reply = $comment_decrypt;
                }
                $comment_reply_like_count  = $this->db->select('id')->from('ask_saheli_comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
                $comment_reply_like_yes_no = $this->db->select('id')->from('ask_saheli_comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
                $comment_reply_date        = $this->get_time_difference_php($comment_reply_date);
                $comment_reply_img_count   = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
                $comment_reply_userimage   = 'https://d2c8oti4is0ms3.cloudfront.net/images/sex_education_images/characters/' . $image;
                $query_reply_listing_type  = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
                $get_reply_type            = $query_reply_listing_type->row_array();
                $com_reply_listing_type    = $get_reply_type['vendor_id'];
                $comment_reply_array[]     = array(
                    'id' => $comment_reply_id,
                    'listing_type' => $com_reply_listing_type,
                    'comment_user_id' => $comment_reply_user_id,
                    'username' => $comment_reply_username,
                    'userimage' => $comment_reply_userimage,
                    'like_count' => $comment_reply_like_count,
                    'like_yes_no' => $comment_reply_like_yes_no,
                    'post_id' => $comment_reply_post_id,
                    'comment' => $comment_reply,
                    'comment_date' => $comment_reply_date
                );
            }
        } else {
            $comment_reply_array = array();
        }
        
        
        return $comment_reply_array;
    }
    
    
    
    public function add_menstrual_cycle_data($user_id, $name, $relationship, $start_period_day, $cycle_length, $total_period_day, $born_of_year, $tutual_cycle, $tracking_resoan, $profile_id, $history_id, $pcod)
    {
        $query = $this->db->query("SELECT id,period_start_from from `menstural_cycle_data` WHERE user_id='$user_id' and profile_id='$profile_id' order by id desc limit 1");
        $count = $query->num_rows();
         $row1   = $query->row_array();
            
        date_default_timezone_set('Asia/Kolkata');
        $old_period_start_from = $row1['period_start_from'];
        $old_period_start_from       = date('d-m-Y', strtotime($old_period_start_from));
        $updated_at = date('Y-m-d h:i:s');
        if ($count > 0) {
            $query1 = $this->db->query("SELECT id,period_start_from from `menstural_cycle_data` WHERE user_id='$user_id' and profile_id='$profile_id' and start_periode_data='$start_period_day' order by id desc limit 1");
            $count1 = $query1->num_rows();
           
            if ($count1 > 0) {
                $history = 'Your period date was changed from '.$old_period_start_from.' to '.$start_period_day;
                $this->db->where('user_id', $user_id)->where('profile_id', $profile_id)->where('id', $history_id)->update('menstural_cycle_data', array(
                    'user_id' => $user_id,
                    'name' => $name,
                    'pcod' => $pcod,
                    'start_periode_data' => $start_period_day,
                    'period_start_from' => $start_period_day,
                    'cycle_length' => $cycle_length,
                    'total_periode_day' => $total_period_day,
                    'born_year' => $born_of_year,
                    'tutual_cycle' => $tutual_cycle,
                    'relationship' => $relationship,
                    'tracking_reson' => $tracking_resoan,
                    'history' => $history
                ));
                $menstural_cycle_history = array(
                    'user_id' => $user_id,
                    'profile_id' => $profile_id,
                    'history' => $history
                );
                $this->db->insert('menstural_cycle_history', $menstural_cycle_history);
            } else {
                $history = 'Your period date was changed from '.$old_period_start_from.' to '.$start_period_day;
                $menstural_cycle_data = array(
                    'user_id' => $user_id,
                    'name' => $name,
                    'pcod' => $pcod,
                    'profile_id' => $profile_id,
                    'start_periode_data' => $start_period_day,
                    'period_start_from' => $start_period_day,
                    'cycle_length' => $cycle_length,
                    'total_periode_day' => $total_period_day,
                    'born_year' => $born_of_year,
                    'tutual_cycle' => $tutual_cycle,
                    'relationship' => $relationship,
                    'tracking_reson' => $tracking_resoan,
                    'history' => $history,
                    'updated_at' => $updated_at
                );
                $this->db->insert('menstural_cycle_data', $menstural_cycle_data);
                
                $menstural_cycle_history = array(
                    'user_id' => $user_id,
                    'profile_id' => $profile_id,
                    'history' => $history
                );
                $this->db->insert('menstural_cycle_history', $menstural_cycle_history);
            }
            return array(
                'status' => 200,
                'message' => 'success',
                'profile_id' => $profile_id,
                'name' => $name,
                'pcod' => $pcod,
                'relation' => $relationship,
                'resoan' => $tracking_resoan,
                'startPdate' => $start_period_day,
                'totalPdate' => $total_period_day,
                'trackBorn' => $born_of_year,
                'trackCycle' => $cycle_length,
                'history' => $history,
                'updated_at' => $updated_at
            );
        } else {
            $menstural_cycle_profile = array(
                'user_id' => $user_id,
                'relationship' => $relationship,
                'name' => $name,
                'age' => '',
                'height' => '',
                'weight' => '',
                'sleep_cycle' => '',
                'birth_control' => '',
                'image' => ''
            );
            $this->db->insert('menstural_cycle_profile', $menstural_cycle_profile);
            $profile_id           = $this->db->insert_id();
            $history              = 'New cycle data created';
            $menstural_cycle_data = array(
                'user_id' => $user_id,
                'name' => $name,
                'pcod' => $pcod,
                'profile_id' => $profile_id,
                'start_periode_data' => $start_period_day,
                'period_start_from' => $start_period_day,
                'cycle_length' => $cycle_length,
                'total_periode_day' => $total_period_day,
                'born_year' => $born_of_year,
                'tutual_cycle' => $tutual_cycle,
                'relationship' => $relationship,
                'tracking_reson' => $tracking_resoan,
                'history' => $history,
                'updated_at' => $updated_at
            );
            $this->db->insert('menstural_cycle_data', $menstural_cycle_data);
            return array(
                'status' => 200,
                'message' => 'success',
                'profile_id' => $profile_id,
                'name' => $name,
                'pcod' => $pcod,
                'relation' => $relationship,
                'resoan' => $tracking_resoan,
                'startPdate' => $start_period_day,
                'totalPdate' => $total_period_day,
                'trackBorn' => $born_of_year,
                'trackCycle' => $cycle_length,
                'history' => $history,
                'updated_at' => $updated_at
            );
        }
    }
    
    public function add_menstural_cycle_profile($user_id, $relationship, $name, $age, $height, $weight, $sleep_cycle, $birth_control, $image, $profile_id, $history_id)
    {
        $query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $pro_query = $this->db->query("SELECT id from `menstural_cycle_profile` WHERE id='$profile_id' AND user_id = '$user_id' and id='$history_id' limit 1");
            $pro_count = $pro_query->num_rows();
            if ($pro_count > 0) {
                $data  = array(
                    'relationship' => $relationship,
                    'name' => $name,
                    'age' => $age,
                    'height' => $height,
                    'weight' => $weight,
                    'sleep_cycle' => $sleep_cycle,
                    'birth_control' => $birth_control,
                    'image' => $image
                );
                $where = array(
                    'user_id ' => $user_id,
                    'id ' => $profile_id
                );
                $this->db->where($where);
                $this->db->update('menstural_cycle_profile ', $data);
                return array(
                    'status' => 200,
                    'message' => 'success'
                );
            } else {
                $menstural_cycle_data = array(
                    'user_id' => $user_id,
                    'relationship' => $relationship,
                    'name' => $name,
                    'age' => $age,
                    'height' => $height,
                    'weight' => $weight,
                    'sleep_cycle' => $sleep_cycle,
                    'birth_control' => $birth_control,
                    'image' => $image
                );
                $this->db->insert('menstural_cycle_profile', $menstural_cycle_data);
                return array(
                    'status' => 200,
                    'message' => 'success'
                );
            }
        } else
            return $result = array(
                'status' => 201,
                'message' => 'failed'
            );
    }
    
    public function update_menstural_cycle_profile($user_id, $profile_id, $relationship, $name, $age, $height, $weight, $sleep_cycle, $birth_control, $image, $history_id)
    {
        $query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $data  = array(
                'relationship' => $relationship,
                'name' => $name,
                'age' => $age,
                'height' => $height,
                'weight' => $weight,
                'sleep_cycle' => $sleep_cycle,
                'birth_control' => $birth_control,
                'image' => $image
            );
            $where = array(
                'user_id ' => $user_id,
                'id ' => $profile_id
            );
            $this->db->where($where);
            $this->db->update('menstural_cycle_profile ', $data);
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else
            return $result = array(
                'status' => 201,
                'message' => 'failed'
            );
    }
    
    public function menstural_cycle_data_yes_no($user_id, $profile_id, $yes_no)
    {
        date_default_timezone_set('Asia/Kolkata');
        if ($yes_no == '1') {
            $ovulation_yes_date = date('Y-m-d H:i:s');
        } else {
            $ovulation_yes_date = '';
        }
        $query = $this->db->query("SELECT id from `users` WHERE id='$user_id' AND `vendor_id` = 0 limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $data  = array(
                'yes_no' => $yes_no,
                'ovulation_yes_date' => $ovulation_yes_date
            );
            $where = array(
                'user_id ' => $user_id,
                'profile_id ' => $profile_id
            );
            $this->db->where($where);
            $this->db->update('menstural_cycle_data ', $data);
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else
            return $result = array(
                'status' => 201,
                'message' => 'failed'
            );
    }
    
    /*public function get_menstural_cycle_profile($user_id)
    {
    $query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
    $count = $query->num_rows();
    if ($count > 0) {
    $query1 = $this->db->query("SELECT * from `menstural_cycle_profile` WHERE user_id='$user_id'");
    $count1 = $query1->num_rows();
    if ($count1 > 0) {
    foreach ($query1->result_array() as $row) {
    $user_id       = $row['user_id'];
    $profile_id    = $row['id'];
    $relationship  = $row['relationship'];
    $name          = $row['name'];
    $age           = $row['age'];
    $height        = $row['height'];
    $weight        = $row['weight'];
    $sleep_cycle   = $row['sleep_cycle'];
    $birth_control = $row['birth_control'];
    
    $result_array[] = array(
    'user_id' => $user_id,
    'profile_id' => $profile_id,
    'relationship' => $relationship,
    'name' => $name,
    'age' => $age,
    'height' => $height,
    'weight' => $weight,
    'sleep_cycle' => $sleep_cycle,
    'birth_control' => $birth_control
    );
    }
    return $result_array;
    } else
    return array();
    } else
    return array();
    }*/
    
    public function get_menstural_cycle_history($user_id, $profile_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $query1 = $this->db->query("SELECT * from `menstural_cycle_history` WHERE user_id='$user_id' and profile_id='$profile_id'");
            $count1 = $query1->num_rows();
            if ($count1 > 0) {
                foreach ($query1->result_array() as $row) {
                    $created_at       = $row['created_at'];
                    $created_at       = date('d-m-Y', strtotime($created_at));
                    $history       = $row['history'];
                    
                    $result_array[] = array(
                        'date' => $created_at,
                        'history' => $history
                    );
                }
                return $result_array;
            } else
                return array();
        } else
            return array();
    }
    
    
    
    public function get_menstural_cycle_profile($user_id, $profile_id)
    {
        $query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $query1 = $this->db->query("SELECT * from `menstural_cycle_profile` WHERE user_id='$user_id' and id='$profile_id'");
            $count1 = $query1->num_rows();
            if ($count1 > 0) {
                foreach ($query1->result_array() as $row) {
                    $user_id       = $row['user_id'];
                    $profile_id    = $row['id'];
                    $relationship  = $row['relationship'];
                    $name          = $row['name'];
                    $age           = $row['age'];
                    $height        = $row['height'];
                    $weight        = $row['weight'];
                    $sleep_cycle   = $row['sleep_cycle'];
                    $birth_control = $row['birth_control'];
                    $image         = $row['image'];
                    $period_in         = $row['period_in'];
                    $ovulation         = $row['ovulation'];
                    $ending_of_period         = $row['ending_of_period'];
                    $starting_of_period         = $row['starting_of_period'];
                    $pms         = $row['pms'];
                    $add_pill         = $row['add_pill'];
                    $safe_period         = $row['safe_period'];
                    
                    if ($image != '') {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/menstural_cycle/' . $image;
                    }
                    
                    $result_array[] = array(
                        'user_id' => $user_id,
                        'profile_id' => $profile_id,
                        'relationship' => $relationship,
                        'name' => $name,
                        'age' => $age,
                        'height' => $height,
                        'weight' => $weight,
                        'sleep_cycle' => $sleep_cycle,
                        'birth_control' => $birth_control,
                        'image' => $image,
                        'period_in' => (int)$period_in,
                        'ovulation' => (int)$ovulation,
                        'ending_of_period' => (int)$ending_of_period,
                        'starting_of_period' => (int)$starting_of_period,
                        'pms' => (int)$pms,
                        'add_pill' => (int)$add_pill,
                        'safe_period' => (int)$safe_period 
                    );
                }
                return $result_array;
            } else
                return array();
        } else
            return array();
    }
    
    
    
    public function get_menstural_cycle_reminder_list($user_id, $profile_id)
    {
        $query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $query1 = $this->db->query("SELECT * from `menstural_cycle_profile` WHERE user_id='$user_id' and id='$profile_id'");
            $count1 = $query1->num_rows();
            if ($count1 > 0) {
                foreach ($query1->result_array() as $row) {
                    $period_in         = $row['period_in'];
                    $ovulation         = $row['ovulation'];
                    $ending_of_period         = $row['ending_of_period'];
                    $starting_of_period         = $row['starting_of_period'];
                    $pms         = $row['pms'];
                    $add_pill         = $row['add_pill'];
                    $safe_period         = $row['safe_period'];
                    $medication         = $row['medication'];
                    $contraception         = $row['contraception'];
          
                    $period_in_array = array(
                        'key' => 'period_in',
                        'name' => 'Period In',
                        'value' => (int)$period_in
                    );
                    $ovulation_array = array(
                        'key' => 'ovulation',
                        'name' => 'Ovulation',
                        'value' => (int)$ovulation
                    );
                    $ending_of_period_array = array(
                        'key' => 'ending_of_period',
                        'name' => 'Ending of Period',
                        'value' => (int)$ending_of_period
                    );
                    $starting_of_period_array = array(
                        'key' => 'starting_of_period',
                        'name' => 'Starting of Period',
                        'value' => (int)$starting_of_period
                    );
                    $pms_array = array(
                        'key' => 'pms',
                        'name' => 'PMS',
                        'value' => (int)$pms
                    );
                    $medication_array = array(
                        'key' => 'medication',
                        'name' => 'Medication',
                        'value' => (int)$medication
                    );
                    $contraception_array = array(
                        'key' => 'contraception',
                        'name' => 'Contraception',
                        'value' => (int)$contraception
                    );
                    $add_pill_array = array(
                        'key' => 'add_pill',
                        'name' => 'Add a Pill Reminder',
                        'value' => (int)$add_pill
                    );
                    $safe_period_array = array(
                        'key' => 'safe_period',
                        'name' => 'Safe Sex Period',
                        'value' => (int)$safe_period
                    );
                    $result_array = array(
                        $period_in_array,$ovulation_array,$ending_of_period_array,$starting_of_period_array,$pms_array,$add_pill_array,$safe_period_array
                    );
                }
                return $result_array;
            } else
                return array();
        } else
            return array();
    }
    
    public function get_menstrual_cycle_data($user_id, $profile_id)
    {
        $query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $query1 = $this->db->query("SELECT * from `menstural_cycle_data` WHERE user_id='$user_id' AND profile_id='$profile_id' order by id desc");
            $count1 = $query1->num_rows();
            if ($count1 > 0) {
                foreach ($query1->result_array() as $row) {
                    $history_id       = $row['id'];
                    $user_id          = $row['user_id'];
                    $profile_id       = $row['profile_id'];
                    $name             = $row['name'];
                    $pcod             = $row['pcod'];
                    //$purpose_tracking = $row['purpose_tracking'];
                    $start_period_day = $row['start_periode_data'];
                    $cycle_length     = $row['cycle_length'];
                    $total_period_day = $row['total_periode_day'];
                    $born_of_year     = $row['born_year'];
                    $tutual_cycle     = $row['tutual_cycle'];
                    $tracking_resoan  = $row['tracking_reson'];
                    $relationship     = $row['relationship'];
                    $image            = "";
                    $query2           = $this->db->query("SELECT image from `menstural_cycle_profile` WHERE user_id='$user_id' and id='$profile_id'");
                    $count2           = $query2->num_rows();
                    if ($count2 > 0) {
                        $final = $query2->row()->image;
                        
                        if ($final != '') {
                            $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/menstural_cycle/' . $final;
                        }
                    }
                    
                    $result_array[] = array(
                        'history_id' => $history_id,
                        'user_id' => $user_id,
                        'profile_id' => $profile_id,
                        'pcod' => $pcod,
                        //'purpose_tracking' => $purpose_tracking,
                        'name' => $name,
                        'relation' => $relationship,
                        'resoan' => $tracking_resoan,
                        'startPdate' => $start_period_day,
                        'totalPdate' => $total_period_day,
                        'trackBorn' => $born_of_year,
                        'trackCycle' => $cycle_length,
                        'tutual_cycle' => $tutual_cycle,
                        'profile_image' => $image
                    );
                }
                return $result_array;
            } else
                return array();
        } else
            return array();
    }
    
    public function get_menstrual_cycle_data_calendar_wise1($user_id, $profile_id)
    {
        $query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $query_temp = $this->db->query("SELECT id,user_id,profile_id,name,pcod,start_periode_data,period_start_from,cycle_length,tracking_reson,total_periode_day,born_year,tutual_cycle,tutual_cycle,relationship from `menstural_cycle_data` WHERE user_id='$user_id' AND profile_id='$profile_id' order by id asc limit 1");
            $count_temp = $query_temp->num_rows();
            
            if ($count_temp > 0) {
                $row               = $query_temp->row_array();
                $history_id        = $row['id'];
                $user_id           = $row['user_id'];
                $profile_id        = $row['profile_id'];
                $name              = $row['name'];
                $pcod              = $row['pcod'];
                $start_period_day  = $row['start_periode_data'];
                $period_start_from = $row['period_start_from'];
                $cycle_length      = $row['cycle_length'];
                $total_period_day  = $row['total_periode_day'];
                $born_of_year      = $row['born_year'];
                $tutual_cycle      = $row['tutual_cycle'];
                $tracking_reson   = $row['tracking_reson'];
                $relationship      = $row['relationship'];
                $image             = "";
                $query2            = $this->db->query("SELECT image from `menstural_cycle_profile` WHERE user_id='$user_id' and id='$profile_id'");
                $count2            = $query2->num_rows();
                if ($count2 > 0) {
                    $final = $query2->row()->image;
                    
                    if ($final != '') {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/menstural_cycle/' . $final;
                    }
                }
                $period_start = $period_start_from = date('Y-m-d', strtotime($period_start_from));
                
                for ($j = 0; $j < 12; $j++) {                    
					if($this->session->userdata('status')==TRUE){
					    $period_start_from = $this->session->userdata('period_start_from');
					    $cycle_length      = $this->session->userdata('cycle_length');
						$period_start      = $this->session->userdata('period_start');
						$start_period_day      = $this->session->userdata('start_period_day');
						$this->session->set_userdata(array(
							'status'   => FALSE
						));
                        $period_days[] = $this->get_calendar1($period_start,$period_start_from,$total_period_day, $cycle_length,$user_id,$profile_id);	
						$period_start  = date('Y-m-d', strtotime($period_start . ' +' . $cycle_length . ' days'));
					}
					else{
					    $period_days[] = $this->get_calendar1($period_start,$period_start_from,$total_period_day, $cycle_length,$user_id,$profile_id);
						$period_start  = date('Y-m-d', strtotime($period_start . ' +' . $cycle_length . ' days'));			
					}
                }
                
                
                $result_array[] = array(
                    'history_id' => $history_id,
                    'user_id' => $user_id,
                    'profile_id' => $profile_id,
                    'pcod' => $pcod,
                    'name' => $name,
                    'relation' => $relationship,
                    'reason' => $tracking_reson,
                    'startPdate' => $start_period_day,
                    'totalPdate' => $total_period_day,
                    'trackBorn' => $born_of_year,
                    'trackCycle' => $cycle_length,
                    'tutual_cycle' => $tutual_cycle,
                    'profile_image' => $image,
                    'calendar' => $period_days
                );
                
                return $result_array;
            }
            
            
        } else
            return array();
    }
    
     public function get_menstrual_cycle_data_calendar_wise($user_id, $profile_id)
    {
        $query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $query_temp = $this->db->query("SELECT id,user_id,profile_id,name,pcod,start_periode_data,period_start_from,cycle_length,tracking_reson,total_periode_day,born_year,tutual_cycle,tutual_cycle,relationship from `menstural_cycle_data` WHERE user_id='$user_id' AND profile_id='$profile_id' order by id asc limit 1");
            $count_temp = $query_temp->num_rows();
            
            if ($count_temp > 0) {
                $row               = $query_temp->row_array();
                $history_id        = $row['id'];
                $user_id           = $row['user_id'];
                $profile_id        = $row['profile_id'];
                $name              = $row['name'];
                $pcod              = $row['pcod'];
                $start_period_day  = $row['start_periode_data'];
                $period_start_from = $row['period_start_from'];
                $cycle_length      = $row['cycle_length'];
                $total_period_day  = $row['total_periode_day'];
                $born_of_year      = $row['born_year'];
                $tutual_cycle      = $row['tutual_cycle'];
                $tracking_reson   = $row['tracking_reson'];
                $relationship      = $row['relationship'];
                $image             = "";
                $query2            = $this->db->query("SELECT image from `menstural_cycle_profile` WHERE user_id='$user_id' and id='$profile_id'");
                $count2            = $query2->num_rows();
                if ($count2 > 0) {
                    $final = $query2->row()->image;
                    
                    if ($final != '') {
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/menstural_cycle/' . $final;
                    }
                }
                $period_start = $period_start_from = date('Y-m-d', strtotime($period_start_from));
                for ($j = 0; $j < 12; $j++) {                    
					if($this->session->userdata('status')==TRUE){
					    $period_start_from = $this->session->userdata('period_start_from');
					    $cycle_length      = $this->session->userdata('cycle_length');
						$period_start      = $this->session->userdata('period_start');
						$start_period_day      = $this->session->userdata('start_period_day');
						$this->session->set_userdata(array(
							'status'   => FALSE
						));
                        $period_days = $this->get_calendar($period_start,$period_start_from,$total_period_day, $cycle_length,$user_id,$profile_id);	
						$period_start  = date('Y-m-d', strtotime($period_start . ' +' . $cycle_length . ' days'));
					}
					else{
					    $period_days = $this->get_calendar($period_start,$period_start_from,$total_period_day, $cycle_length,$user_id,$profile_id);
						$period_start  = date('Y-m-d', strtotime($period_start . ' +' . $cycle_length . ' days'));			
					}
					
						$after_ovulation = $period_days['after_ovulation']; 
					   
					if(empty($after_ovulation) && $j != 0){
					    $period = $period_days['period'];
					    $before_ovulation = $period_days['before_ovulation'];
					    $ovulation = $period_days['ovulation'];
					    $after_ovulation = $period_days['after_ovulation'];
					    
					    for($z=0;$z<sizeof($period);$z++){
					       // print_r($period[$z]); die();
					        array_push($period_cal_days[$j-1]['after_ovulation'],$period[$z]);
					    }
					    for($z=0;$z<sizeof($before_ovulation);$z++){
					        array_push($period_cal_days[$j-1]['after_ovulation'],$before_ovulation[$z]);
					    }
					    for($z=0;$z<sizeof($ovulation);$z++){
					        array_push($period_cal_days[$j-1]['after_ovulation'],$ovulation[$z]);
					    }
					    for($z=0;$z<sizeof($after_ovulation);$z++){
					        array_push($period_cal_days[$j-1]['after_ovulation'],$after_ovulation[$z]);
					    }
					   // die();
					    
					   
                         
                    } else {
                        $period_cal_days[] = $period_days;
                    }
                }
                
                //  print_r($period_cal_days); die();
                
                // die();
                $result_array[] = array(
                    'history_id' => $history_id,
                    'user_id' => $user_id,
                    'profile_id' => $profile_id,
                    'pcod' => $pcod,
                    'name' => $name,
                    'relation' => $relationship,
                    'reason' => $tracking_reson,
                    'startPdate' => $start_period_day,
                    'totalPdate' => $total_period_day,
                    'trackBorn' => $born_of_year,
                    'trackCycle' => $cycle_length,
                    'tutual_cycle' => $tutual_cycle,
                    'profile_image' => $image,
                    'calendar' => $period_cal_days
                );
                
                return $result_array;
            }
            
            
        } else
            return array();
    }
    public function get_calendar1($period_start,$period_start_from,$total_period_day, $cycle_length,$user_id,$profile_id)
    {
		$p_date           = array();
        $before_ovulation = array();
        $o_date           = array();
        $after_ovulation  = array();
        for ($i = 0; $i < $cycle_length; $i++) {
            $date_array = date('Y-m-d', strtotime($period_start . ' +' . $i . '  days'));
            $count_temp=0;
            $query_temp = $this->db->query("SELECT start_periode_data,period_start_from,cycle_length,total_periode_day,DATE_FORMAT(STR_TO_DATE(`period_start_from`, '%d-%m-%Y'), '%Y-%m-%d') AS start_period from `menstural_cycle_data` WHERE user_id='$user_id' AND profile_id='$profile_id' HAVING start_period='$date_array' and start_period<>'$period_start_from' order by id asc limit 1");
            $count_temp = $query_temp->num_rows();

            if ($count_temp>0) {			
				$row  = $query_temp->row_array();
                $start_period_day  = $row['start_periode_data'];
                $period_start_from = $row['period_start_from'];
                $cycle_length      = $row['cycle_length'];
                $total_period_day  = $row['total_periode_day'];
				$period_start = $period_start_from = date('Y-m-d', strtotime($period_start_from));
                $this->session->set_userdata(array(
					'start_period_day'  => $start_period_day,
					'period_start'  => $period_start,
					'period_start_from' => $period_start_from,
					'cycle_length' => $cycle_length,
					'status'   => TRUE
				));
				goto end;				
            } else {
			    $this->session->set_userdata(array(
					'status'   => FALSE
				));
                if ($i < $total_period_day) {
                    $p_date[] = array(
                        'date' => $date_array
                    );
                } else if ($i >= $total_period_day && $i < 10) {
                    $before_ovulation[] = array(
                        'date' => $date_array
                    );
                } else if ($i >= 10 && $i < 16) {
                    $o_date[] = array(
                        'date' => $date_array
                    );
                } else {
                    $after_ovulation[] = array(
                        'date' => $date_array
                    );
                }
                
               
				$period_days = array(
					'period' => $p_date,
					'before_ovulation' => $before_ovulation,
					'ovulation' => $o_date,
					'after_ovulation' => $after_ovulation
				);
            }
        }   
		end:		
        return $period_days;
    }
    
     public function get_calendar($period_start,$period_start_from,$total_period_day, $cycle_length,$user_id,$profile_id)
    {
		$p_date           = array();
        $before_ovulation = array();
        $o_date           = array();
        $after_ovulation  = array();
        for ($i = 0; $i < $cycle_length; $i++) {
            $date_array = date('Y-m-d', strtotime($period_start . ' +' . $i . '  days'));
            $count_temp=0;
            $query_temp = $this->db->query("SELECT start_periode_data,period_start_from,cycle_length,total_periode_day,DATE_FORMAT(STR_TO_DATE(`period_start_from`, '%d-%m-%Y'), '%Y-%m-%d') AS start_period from `menstural_cycle_data` WHERE user_id='$user_id' AND profile_id='$profile_id' HAVING start_period='$date_array' and start_period<>'$period_start_from' order by id asc limit 1");
            $count_temp = $query_temp->num_rows();

            if ($count_temp>0) {			
				$row  = $query_temp->row_array();
                $start_period_day  = $row['start_periode_data'];
                $period_start_from = $row['period_start_from'];
                $cycle_length      = $row['cycle_length'];
                $total_period_day  = $row['total_periode_day'];
				$period_start = $period_start_from = date('Y-m-d', strtotime($period_start_from));
                $this->session->set_userdata(array(
					'start_period_day'  => $start_period_day,
					'period_start'  => $period_start,
					'period_start_from' => $period_start_from,
					'cycle_length' => $cycle_length,
					'status'   => TRUE
				));
				goto end;				
            } else {
			    $this->session->set_userdata(array(
					'status'   => FALSE
				));
                if ($i < $total_period_day) {
                    $p_date[] = array(
                        'date' => $date_array
                    );
                } else if ($i >= $total_period_day && $i <= 10) {
                    $before_ovulation[] = array(
                        'date' => $date_array
                    );
                } else if ($i > 10 && $i < 16) {
                    $o_date[] = array(
                        'date' => $date_array
                    );
                } else {
                    $after_ovulation[] = array(
                        'date' => $date_array
                    );
                }
                
				$period_days = array(
					'period' => $p_date,
					'before_ovulation' => $before_ovulation,
					'ovulation' => $o_date,
					'after_ovulation' => $after_ovulation
				);
            }
        }   
		end:		
        return $period_days;
    }    
    
    public function delete_menstural_cycle_profile($user_id, $profile_id)
    {
        $count_query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count       = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `menstural_cycle_profile` WHERE user_id='$user_id' and id='$profile_id'");
            $this->db->query("DELETE FROM `menstural_cycle_data` WHERE user_id='$user_id' AND profile_id='$profile_id'");
            return array(
                'status' => 200,
                'message' => 'deleted'
            );
        } else {
            return array(
                'status' => 401,
                'message' => 'unauthorized'
            );
        }
    }
    
    public function delete_menstrual_cycle_data($user_id, $profile_id)
    {
        $count_query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count       = $count_query->num_rows();
        if ($count > 0) {
            
            $this->db->query("DELETE FROM `menstural_cycle_data` WHERE user_id='$user_id' AND profile_id='$profile_id'");
            $this->db->query("DELETE FROM `menstural_cycle_profile` WHERE user_id='$user_id' and id='$profile_id'");
            return array(
                'status' => 200,
                'message' => 'deleted'
            );
        } else {
            return array(
                'status' => 401,
                'message' => 'unauthorized'
            );
        }
    }
    
    
    public function get_menstrual_cycle_category($user_id, $profile_id)
    {
        $query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $query1 = $this->db->query("SELECT * from `Menstrual_cycle_category`");
            $count1 = $query1->num_rows();
            
            if ($count1 > 0) {
                
                foreach ($query1->result_array() as $row) {
                    $category_name = $row['Main_category'];
                    $category_id   = $row['id'];
                    
                    $query_sub = $this->db->query("SELECT * from `Menstrual_cycle_activity_info` where Main_category='$category_name' AND Category_id<>'10' AND Category_id<>'11'");
                    
                    
                    $query_sub_count = $query_sub->num_rows();
                    if ($query_sub_count > 0) {
                        $sub_catewgory_data = array();
                        foreach ($query_sub->result_array() as $row1) {
                            $sub_category_id   = $row1['Category_id'];
                            $sub_Category_name = $row1['Category_name'];
                            $Category_info     = $row1['Category_info'];
                            $Category_image    = $row1['Category_image'];
                            
                            //added for active or not logic 
                            $query_status       = $this->db->query("SELECT * from `Menstrual_cycle_activity_status` where main_category='$category_id' and sub_category='$sub_category_id' and user_id='$user_id' and profile_id = '$profile_id'");
                            // echo "SELECT * from `Menstrual_cycle_activity_status` where main_category='$category_id' and sub_category='$sub_category_id'";
                            $query_status_count = $query_status->num_rows();
                            if ($query_status_count > 0) {
                                //  echo 'cat'.$category_id;
                                // echo 'sub'.$sub_category_id;
                                foreach ($query_status->result_array() as $row2) {
                                    $Active_state = $row2['status'];
                                    //   echo $Active_state;
                                }
                            } else {
                                //$Active_state  = $row1['Active_state'];
                                $Active_state = 'off';
                            }
                            //  echo 'check kerneka tha';
                            
                            $queary_child_category       = $this->db->query("SELECT * from `Menstrual_cycle_subcategory` where sub_category='$sub_Category_name'");
                            $queary_child_category_count = $queary_child_category->num_rows();
                            $child_array                 = array();
                            if ($queary_child_category_count > 0) {
                                foreach ($queary_child_category->result_array() as $rowc) {
                                    $child_name  = $rowc['child_category'];
                                    $child_image = 'https://s3.amazonaws.com/medicalwale/images/MenstrualCycle/SubCategory/' . $rowc['image'];
                                    
                                    $child_array[] = array(
                                        'child_category_name' => $child_name,
                                        'child_category_image' => $child_image
                                    );
                                }
                            }
                            
                            $sub_catewgory_data[] = array(
                                'sub_category_id' => $sub_category_id,
                                'sub_category_name' => $sub_Category_name,
                                'Category_info' => $Category_info,
                                'Category_image' => 'https://s3.amazonaws.com/medicalwale/images/MenstrualCycle/MainCategory/' . $Category_image,
                                'Child_category_data' => $child_array,
                                'Active_state' => $Active_state
                            );
                        }
                    }
                    $main_category_data[] = array(
                        'category_id' => $category_id,
                        'category_name' => $category_name,
                        'sub_category' => $sub_catewgory_data
                    );
                }
                return $main_category_data;
            }
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => array()
            );
        } else
            return $result = array(
                'status' => 201,
                'message' => 'failed'
            );
    }
    
    
    ///added by zak
    public function get_menstrual_cycle_category_date($user_id, $date, $profile_id)
    {
        $query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $query1 = $this->db->query("SELECT * from `Menstrual_cycle_category`");
            $count1 = $query1->num_rows();
            
            if ($count1 > 0) {
                
                foreach ($query1->result_array() as $row) {
                    $category_name = $row['Main_category'];
                    $category_id   = $row['id'];
                    
                    $query_sub = $this->db->query("SELECT * from `Menstrual_cycle_activity_info` where Main_category='$category_name'");
                    
                    
                    $query_sub_count = $query_sub->num_rows();
                    if ($query_sub_count > 0) {
                        $sub_catewgory_data = array();
                        foreach ($query_sub->result_array() as $row1) {
                            $sub_category_id   = $row1['Category_id'];
                            $sub_Category_name = $row1['Category_name'];
                            $Category_info     = $row1['Category_info'];
                            $Category_image    = $row1['Category_image'];
                            
                            //added for active or not logic 
                            $query_status       = $this->db->query("SELECT * from `Menstrual_cycle_activity_status` where main_category='$category_id' and sub_category='$sub_category_id' and user_id='$user_id' and profile_id = '$profile_id'");
                            // echo "SELECT * from `Menstrual_cycle_activity_status` where main_category='$category_id' and sub_category='$sub_category_id'";
                            $query_status_count = $query_status->num_rows();
                            if ($query_status_count > 0) {
                                //  echo 'cat'.$category_id;
                                // echo 'sub'.$sub_category_id;
                                foreach ($query_status->result_array() as $row2) {
                                    $Active_state = $row2['status'];
                                    //   echo $Active_state;
                                }
                            } else {
                                //$Active_state  = $row1['Active_state'];
                                $Active_state = 'off';
                            }
                            //  echo 'check kerneka tha';
                            
                            $queary_child_category       = $this->db->query("SELECT * from `Menstrual_cycle_subcategory` where sub_category='$sub_Category_name'");
                            $queary_child_category_count = $queary_child_category->num_rows();
                            $child_array                 = array();
                            if ($queary_child_category_count > 0) {
                                foreach ($queary_child_category->result_array() as $rowc) {
                                    $child_name  = $rowc['child_category'];
                                    $child_image = 'https://s3.amazonaws.com/medicalwale/images/MenstrualCycle/SubCategory/' . $rowc['image'];
                                    
                                    $child_array[] = array(
                                        'child_category_name' => $child_name,
                                        'child_category_image' => $child_image
                                    );
                                }
                            }
                            
                            $sub_catewgory_data[] = array(
                                'sub_category_id' => $sub_category_id,
                                'sub_category_name' => $sub_Category_name,
                                'Category_info' => $Category_info,
                                'Category_image' => 'https://s3.amazonaws.com/medicalwale/images/MenstrualCycle/MainCategory/' . $Category_image,
                                'Child_category_data' => $child_array,
                                'Active_state' => $Active_state
                            );
                            
                        }
                        
                    }
                    $main_category_data[] = array(
                        'category_id' => $category_id,
                        'category_name' => $category_name,
                        'sub_category' => $sub_catewgory_data
                    );
                }
                return $main_category_data;
            }
            
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => array()
            );
        } else
            return $result = array(
                'status' => 201,
                'message' => 'failed'
            );
    }
    
    public function update_cycle_category_status($user_id, $main_category, $sub_category, $status, $profile_id)
    {
        $updated_at = date('Y-m-d H:i:s');
        $query      = $this->db->query("SELECT id FROM `Menstrual_cycle_activity_status` WHERE user_id = '$user_id' and main_category = '$main_category' and sub_category = '$sub_category' and profile_id = '$profile_id'");
        $count      = $query->num_rows();
        if ($count > 0) {
            $this->db->where('user_id', $user_id)->where('main_category', $main_category)->where('profile_id', $profile_id)->where('sub_category', $sub_category)->update('Menstrual_cycle_activity_status', array(
                'status' => $status
                // 'update_at' => $updated_at
            ));
            
            return array();
        } else {
            $insert_notfication = array(
                'user_id' => $user_id,
                'profile_id' => $profile_id,
                'main_category' => $main_category,
                'sub_category' => $sub_category,
                'status' => $status
                // 'update_at' => $updated_at
            );
            
            $this->db->insert('Menstrual_cycle_activity_status', $insert_notfication);
            if ($this->db->affected_rows()) {
                return array(
                // 'status' => 200,
                    
                // 'message' => 'success'
                    );
            } else {
                return array(
                // 'status' => 201,
                    
                // 'message' => 'error'
                    );
            }
        }
    }
    
    
    public function get_cycle_Child_category($user_id, $sub_category, $date, $profile_id)
    {
        $query = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $query1 = $this->db->query("SELECT * from `Menstrual_cycle_subcategory` where sub_category = '$sub_category'");
            $count1 = $query1->num_rows();
            
            if ($count1 > 0) {
                
                foreach ($query1->result_array() as $row) {
                    $category_name  = $row['sub_category'];
                    $child_category = $row['child_category'];
                    $image          = 'https://s3.amazonaws.com/medicalwale/images/MenstrualCycle/SubCategory/' . $row['image'];
                    
                    $query = $this->db->query("SELECT * FROM `Menstraul_cycle_subcategory_status` WHERE user_id = '$user_id' and sub_category = '$sub_category' and child_category = '$child_category' and date = '$date' and profile_id = '$profile_id'");
                    $count = $query->num_rows();
                    if ($count > 0) {
                        
                        foreach ($query->result_array() as $row2) {
                            $status = $row2['status'];
                        }
                    } else {
                        
                        $status = 'off';
                    }
                    
                    
                    
                    $main_category_data[] = array(
                        'category_name' => $category_name,
                        'child_category' => $child_category,
                        'status' => $status,
                        'image' => $image
                    );
                    
                    
                }
                
                return $main_category_data;
                
                
                
            } else {
                return array(
                    'status' => 200,
                    'message' => 'success',
                    'data' => array()
                );
            }
        } else
            return $result = array(
                'status' => 201,
                'message' => 'failed'
            );
    }
    
    public function get_cycle_Child_category_update($user_id, $sub_category, $child_category, $date, $status, $profile_id)
    {
        $query = $this->db->query("SELECT id from `users`  WHERE id='$user_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $updated_at = date('Y-m-d H:i:s');
            $query      = $this->db->query("SELECT * FROM `Menstraul_cycle_subcategory_status` WHERE user_id = '$user_id' and sub_category = '$sub_category' and child_category = '$child_category' and date = '$date' and profile_id = '$profile_id'");
            $count      = $query->num_rows();
            if ($count > 0) {
                $this->db->where('user_id', $user_id)->where('sub_category', $sub_category)->where('child_category', $child_category)->where('date', $date)->where('profile_id', $profile_id)->update('Menstraul_cycle_subcategory_status', array(
                    'status' => $status
                    //'update_at' => $updated_at
                ));
                
                return array();
            } else {
                $insert_notfication = array(
                    'user_id' => $user_id,
                    'profile_id' => $profile_id,
                    'child_category' => $child_category,
                    'sub_category' => $sub_category,
                    'status' => $status,
                    'date' => $date
                );
                
                $this->db->insert('Menstraul_cycle_subcategory_status', $insert_notfication);
                if ($this->db->affected_rows()) {
                    return array(
                    // 'status' => 200,                        
                        
                    // 'message' => 'success'
                        );
                } else {
                    return array(
                    // 'status' => 201,                        
                        
                    // 'message' => 'error'
                        );
                }
            }
        } else
            return $result = array(
                'status' => 201,
                'message' => 'failed'
            );
    }
    
    public function menstrual_cycle_article_list($user_id, $page)
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
        
        $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
        $lang       = $query_lang->language;
        $query      = $this->db->query("SELECT article.id,article.article_title,article.image,article.tag,article.author,article.article_description,article.posted,article.is_active,article.updated_date FROM article WHERE (id='371' or id='159' or id='469' or id='469') and article.language ='$lang' order by id desc limit $start,$limit");
        $count      = $query->num_rows();
        function RemoveBS($Str)
        {
            $StrArr = str_split($Str);
            $NewStr = '';
            foreach ($StrArr as $Char) {
                $CharNo = ord($Char);
                if ($CharNo == 163) {
                    $NewStr .= $Char;
                    continue;
                } // keep £
                if ($CharNo > 31 && $CharNo < 127) {
                    $NewStr .= $Char;
                }
            }
            return $NewStr;
        }
        if ($count > 0) {
            
            foreach ($query->result_array() as $row) {
                $article_id          = $row['id'];
                $article_title       = RemoveBS($row['article_title']);
                $article_description = $row['article_description'];
                //hindi = 1
                if ($lang == 1) {
                    $article_title       = $this->decrypt($row['article_title']);
                    $article_description = $this->decrypt($row['article_description']);
                    $str_hindi           = $this->decrypt($row['article_description']);
                    if (strlen($row['article_description']) > 80) {
                        $str_hindi = substr($row['article_description'], 0, 80);
                        $str_hindi = $this->decrypt($str_hindi);
                    }
                    $short_desc = $str_hindi;
                    
                    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $article_description;
                    
                } else {
                    $article_title       = RemoveBS($row['article_title']);
                    $article_description = $row['article_description'];
                    $article_description = str_replace('&nbsp;', '', $article_description);
                    $article_description = str_replace("&#39;", "'", $article_description);
                    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $article_description;
                    
                    
                    $str = $row['article_description'];
                    if (strlen($str) > 80) {
                        $str = substr($str, 0, 80);
                        $str = strip_tags(htmlspecialchars_decode($str));
                    }
                    $short_desc = $str;
                    
                }
                
                $article_image  = str_replace("itâ€™s", "it's", $row['image']);
                $article_date   = $row['posted'];
                $author         = 'Medicalwale.com';
                $article_image  = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
                $like_count     = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                $like_yes_no    = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $views_count    = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                $is_bookmark    = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();
                
                $post_followers = $this->db->select('id')->from('article_follow_post')->where('post_id', $article_id)->get()->num_rows();
                
                $is_follow = $this->db->select('id')->from('article_follow_post')->where('user_id', $user_id)->where('post_id', $article_id)->get()->num_rows();
                
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                
                $article_title_final = (str_replace(' ', '-', strtolower($article_title)));
                
                $resultpost[] = array(
                    'article_id' => $article_id,
                    'article_title' => $article_title,
                    'article_description' => $article_description,
                    'short_desc' => $article_title,
                    'article_image' => $article_image,
                    'article_date' => $article_date,
                    'author' => $author,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'total_views' => $views_count,
                    'is_bookmark' => $is_bookmark,
                    'total_bookmark' => $total_bookmark,
                    'is_follow' => $is_follow,
                    'total_follow' => $post_followers
                );
                
            }
            
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
    public function menstrual_cycle_profile_list($user_id)
    {
        $resultpost = array();
        $query      = $this->db->query("SELECT * FROM `menstural_cycle_profile` where user_id='$user_id' order by id asc");
        $count      = $query->num_rows();
        if ($count > 0) {
            
            foreach ($query->result_array() as $row) {
                $id     = $row['id'];
                $query1 = $this->db->query("SELECT * from `menstural_cycle_data`  WHERE user_id='$user_id' AND profile_id='$id'");
                $count1 = $query1->num_rows();
                if ($count1 > 0) {
                    $result_array = array();
                    foreach ($query1->result_array() as $row) {
                        $user_id          = $row['user_id'];
                        $profile_id       = $row['profile_id'];
                        $name             = $row['name'];
                        $start_period_day = $row['start_periode_data'];
                        $cycle_length     = $row['cycle_length'];
                        $total_period_day = $row['total_periode_day'];
                        $born_of_year     = $row['born_year'];
                        $tutual_cycle     = $row['tutual_cycle'];
                        $tracking_resoan  = $row['tracking_reson'];
                        $relationship     = $row['relationship'];
                        $image            = "";
                        $query2           = $this->db->query("SELECT image from `menstural_cycle_profile` WHERE user_id='$user_id' and id='$profile_id'");
                        $count2           = $query2->num_rows();
                        if ($count2 > 0) {
                            $final = $query2->row()->image;
                            
                            
                            if ($final != '') {
                                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/menstural_cycle/' . $final;
                            }
                            
                            
                            
                        }
                        
                        $resultpost[] = array(
                            'profile_id' => $profile_id,
                            'name' => $name,
                            'relation' => $relationship,
                            'resoan' => $tracking_resoan,
                            'startPdate' => $start_period_day,
                            'totalPdate' => $total_period_day,
                            'trackBorn' => $born_of_year,
                            'trackCycle' => $cycle_length,
                            'tutual_cycle' => $tutual_cycle,
                            'profile_image' => $image
                            
                        );
                    }
                }
                
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function menstrual_cycle_terms($user_id, $page)
    {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        $query = $this->db->query("SELECT * FROM `menstrual_cycle_terms` where is_active='1' order by term asc limit $start,$limit");
        $count = $query->num_rows();
        function RemoveBS($Str)
        {
            $StrArr = str_split($Str);
            $NewStr = '';
            foreach ($StrArr as $Char) {
                $CharNo = ord($Char);
                if ($CharNo == 163) {
                    $NewStr .= $Char;
                    continue;
                }
                if ($CharNo > 31 && $CharNo < 127) {
                    $NewStr .= $Char;
                }
            }
            return $NewStr;
        }
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $term_id     = $row['id'];
                $term        = RemoveBS($row['term']);
                $description = $row['description'];

                
                $resultpost[] = array(
                    'term_id' => $term_id,
                    'term' => $term,
                    'description' => $description
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function menstrual_cycle_tips($user_id, $page)
    {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        $query = $this->db->query("SELECT * FROM `menstural_cycle_tips` where is_active='1' order by id desc limit $start,$limit");
        $count = $query->num_rows();
        function RemoveBS($Str)
        {
            $StrArr = str_split($Str);
            $NewStr = '';
            foreach ($StrArr as $Char) {
                $CharNo = ord($Char);
                if ($CharNo == 163) {
                    $NewStr .= $Char;
                    continue;
                }
                if ($CharNo > 31 && $CharNo < 127) {
                    $NewStr .= $Char;
                }
            }
            return $NewStr;
        }
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $tips_id     = $row['id'];
                $tips        = RemoveBS($row['tips']);
                $description = $row['description'];
                
                $order_info = $this->db->select('status')->from('Menstraul_save_tips')->where('tips_id', $tips_id)->where('user_id', $user_id)->get()->row();
                if (count($order_info) > 0) {
                    $order_status = $order_info->status;
                } else {
                    $order_status = "";
                }
                
                $resultpost[] = array(
                    'tips_id' => $tips_id,
                    'tips' => $tips,
                    'order_status' => $order_status,
                    'description' => $description
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
    public function menstrual_cycle_save_tips($user_id, $tips_id, $status)
    {
        $query = $this->db->query("SELECT id from `users`  WHERE id='$user_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
            $updated_at = date('Y-m-d H:i:s');
            $query      = $this->db->query("SELECT * FROM `Menstraul_save_tips` WHERE user_id = '$user_id' and tips_id = '$tips_id' and status = '1'");
            $count      = $query->num_rows();
            if ($count > 0) {
                $this->db->where('user_id', $user_id)->where('tips_id', $tips_id)->update('Menstraul_save_tips', array(
                    'status' => $status
                ));
                
                return array();
            } else {
                $insert_notfication = array(
                    'user_id' => $user_id,
                    'tips_id' => $tips_id,
                    'status' => $status
                );
                
                $this->db->insert('Menstraul_save_tips', $insert_notfication);
                if ($this->db->affected_rows()) {
                    return array();
                } else {
                    return array();
                }
            }
        } else
            return $result = array(
                'status' => 201,
                'message' => 'failed'
            );
    }
    
    
    
    public function menstrual_cycle_user_exist($user_id)
    {
        
        $query = $this->db->query("SELECT * FROM `menstural_cycle_profile` where user_id='$user_id' order by id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            $is_exist = 'yes';
            foreach ($query->result_array() as $row) {
                $profile_id   = $row['id'];
                $name         = $row['name'];
                $image        = $row['image'];
                $relationship = $row['relationship'];
                if ($image != null) {
                    $imageURL = 'https://d2c8oti4is0ms3.cloudfront.net/images/menstural_cycle/' . $image;
                } else {
                    $imageURL = '';
                }
                $resultpost[] = array(
                    'profile_id' => $profile_id,
                    'name' => $name,
                    'image' => $imageURL,
                    'relationship' => $relationship
                );
            }
        } else {
            $is_exist   = 'no';
            $resultpost = array();
        }
        return $resultpost = array(
            'is_exist' => $is_exist,
            'profile_list' => $resultpost
        );
    }
    
    public function update_menstural_cycle_reminder($user_id, $profile_id, $period_in, $ovulation, $ending_of_period,$starting_of_period,$pms,$add_pill,$safe_period)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM `menstural_cycle_profile` WHERE user_id='$user_id' and id='$profile_id' limit 1");
        $count       = $count_query->num_rows();
        
        if ($count > 0) {
            $this->db->query("UPDATE `menstural_cycle_profile` SET `period_in`='$period_in',`ovulation`='$ovulation',`ending_of_period`='$ending_of_period',`starting_of_period`='$starting_of_period',`pms`='$pms',`add_pill`='$add_pill',`safe_period`='$safe_period' WHERE user_id='$user_id' and id='$profile_id'");
            return array(
                'status' => 200,
                'message' => 'success'
            );
        } else {
            return array(
                'status' => 208,
                'message' => 'No profile found'
            );
        }
    }
    
    public function get_cycle_all_child_category($user_id, $profile_id)
    {
        $main_category_data = array();
        $query              = $this->db->query("SELECT id from `users` WHERE id='$user_id' limit 1");
        $count              = $query->num_rows();
        if ($count > 0) {
            $query = $this->db->query("SELECT Menstraul_cycle_subcategory_status.user_id,Menstraul_cycle_subcategory_status.profile_id,Menstrual_cycle_subcategory.sub_category,Menstrual_cycle_subcategory.child_category,Menstrual_cycle_subcategory.image,Menstraul_cycle_subcategory_status.status,Menstraul_cycle_subcategory_status.date FROM Menstraul_cycle_subcategory_status INNER JOIN Menstrual_cycle_subcategory on Menstrual_cycle_subcategory.child_category=Menstraul_cycle_subcategory_status.child_category WHERE Menstraul_cycle_subcategory_status.user_id = '$user_id' and Menstraul_cycle_subcategory_status.profile_id = '$profile_id' and Menstraul_cycle_subcategory_status.status='on' order by Menstraul_cycle_subcategory_status.date asc");
            
            foreach ($query->result_array() as $row) {
                $status               = $row['status'];
                $date                 = $row['date'];
                $category_name        = $row['sub_category'];
                $child_category       = $row['child_category'];
                $image                = 'https://s3.amazonaws.com/medicalwale/images/MenstrualCycle/SubCategory/' . $row['image'];
                $date                 = date("d-m-Y", strtotime($date));
                $main_category_data[] = array(
                    'category_name' => $category_name,
                    'child_category' => $child_category,
                    'status' => $status,
                    'image' => $image,
                    'date' => $date
                );
            }
            return $main_category_data;
        } else
            return $result = array(
                'status' => 201,
                'message' => 'failed'
            );
    }
    

public function fetch_menstural_cycle_details($profile_id,$user_id){
  
//curl starts 
$data_fields = array('profile_id' => $profile_id,'user_id' => $user_id);
$data_array = json_encode($data_fields);
 $header = array(
    'Accept: application/json',
    'Content-Type: application/x-www-form-urlencoded',
    'Content-Type:application/json',
    'auth-key:medicalwalerestapi',
    'authorizations:25iwFyq/LSO1U',
    'client-service:frontend-client',
    'user-id:1'
);

$ch = curl_init('http://sandboxapi.medicalwale.com/v49/ofw/get_menstrual_cycle_data_calendar_wise');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_array);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
$result = curl_exec($ch);
curl_close($ch); 
//curl ends   
    
$array = json_decode($result,true);

$created_date= date('d-M-Y');	
$name=$array["data"][0]['name'];
$relation=$array["data"][0]['relation'];
$reason=$array["data"][0]['reason'];
$startPdate=$array["data"][0]['startPdate'];
$totalPdate=$array["data"][0]['totalPdate'];
$trackBorn=$array["data"][0]['trackBorn'];
$trackCycle=$array["data"][0]['trackCycle'];
$tutual_cycle=$array["data"][0]['tutual_cycle'];    
   
$output='
  <body>
    <header class="clearfix">
      <div id="logo">
        <img src="https://medicalwale.com/assets1/images/logo.svg">
		<h1>menstrual cycle report</h1>
      </div>

      </div>
    </header>
    <main>
		<table border="0" class="table-head" cellspacing="0" cellpadding="0">
         <h3 class="mb-0"><b>Patient Details:</b></h3>
           <tr>
            <td><b>Name:</b> '.$name.'</td>        
            <td><b>Relation:</b> '.$relation.'</td>        
          </tr>   
		  <tr>
            <td><b>Reason:</b> '.$reason.'</td>        
            <td><b>Start Period Date:</b> '.$startPdate.'</td>        
          </tr> 
		  <tr>
            <td><b>Total Period Date:</b> '.$totalPdate.'</td>        
            <td><b>Track Born:</b> '.$trackBorn.'</td>        
          </tr>  
		  <tr>
            <td><b>Track Cycle:</b> '.$trackCycle.'</td>        
            <td><b>Tutual Cycle:</b> '.$tutual_cycle.'</td>        
          </tr> 
          <tr>
            <td><b>Report Date:</b> '.$created_date.'</td>        
            <td></td>        
          </tr>   
      
      </table>';
    

 for($i=0; $i<count($array["data"][0]["calendar"]);$i++)
    {
      $j=$i+1;    
       
       $periodC = '';
       $ovulationC = '';  
         foreach($array["data"][0]["calendar"][$i]["period"] as $values)
         { 
             $period_date=$values['date'];
             if ($periodC) $periodC .= ', ';
             $periodC .= date("d-M-Y", strtotime($period_date));
         }
     
          foreach($array["data"][0]["calendar"][$i]["ovulation"] as $values2)
         {     
              $ovulation_date=$values2['date'];
             if ($ovulationC) $ovulationC .= ', ';
              $ovulationC .= date("d-M-Y", strtotime($ovulation_date));
         }
	  
  $output .='<table border="0" cellspacing="0" cellpadding="0">
        <thead>
          <tr>
            <th class="header">'.$this->addOrdinalNumberSuffix($j).' Month</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="desc"><b>Perod Day:</b> '.$periodC.'</td>        
          </tr>   
		  <tr>
            <td class="desc"><b>Ovulation Day:</b> '.$ovulationC.'</td>        
          </tr>  
        </tbody>
      </table>
	  ';
    $j++; 
    } 
    
    
  $output .='</main>
    <footer>
     Copyright © 2019 Aegis Health Solutions.
    </footer>
  </body>';
return $output;
}
    
 
 function addOrdinalNumberSuffix($num) {
    if (!in_array(($num % 100),array(11,12,13))){
      switch ($num % 10) {
        // Handle 1st, 2nd, 3rd
        case 1:  return $num.'<sup>st</sup>';
        case 2:  return $num.'<sup>nd</sup>';
        case 3:  return $num.'<sup>rd</sup>';
      }
    }
    return $num.'<sup>th</sup>';
  } 
    
}
