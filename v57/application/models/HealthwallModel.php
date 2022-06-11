<?php

defined('BASEPATH') OR exit('No direct script access allowed');
 
class HealthwallModel extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";

    //validate auth key and client 
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

    //check api authentication
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
            //echo $q->expired_at."<".date('Y-m-d H:i:s');
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

    //send notification through firebase
    public function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
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
                "notification_type" => 'healthwall_notifications',
                "notification_date" => $date,
                "post_id" => $post_id
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
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }

//send notification through firebase
    public function send_gcm_notify_ios($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
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
                "notification_type" => 'healthwall_comment_notifications',
                "notification_date" => $date,
                "post_id" => $post_id,
                "comment_id" => $comment_id
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
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    //added for notification switch not to send for perticular user
    public function get_stop_notification_for_user($user_id) {
        $query = $this->db->query("SELECT * FROM `notification_switch_control` WHERE user_id = '$user_id' and category = 'Healthwall'");
        $count = $query->num_rows();
        if ($count > 0) {
            $query1 = $this->db->query("SELECT * FROM `notification_switch_control` WHERE user_id = '$user_id' and category = 'Healthwall' and status = 'on'");
            $count1 = $query1->num_rows();
            if ($count1 > 0) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }

    //get time difference
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
            return "on " . date('D, d F Y h:i a', strtotime($created_time));
        } elseif (intval($time_differnce / $years) > 0) {
            return "on " . date('D, d F Y h:i a', strtotime($created_time));
        } elseif (intval($time_differnce / $months) > 1) {
            return "on " . date('D, d F Y h:i a', strtotime($created_time));
        } elseif (intval(($time_differnce / $months)) > 0) {
            return "on " . date('D, d F h:i a', strtotime($created_time));
        } elseif (intval(($time_differnce / $days)) > 1) {
            return "on " . date('D, d F h:i a', strtotime($created_time));
        } elseif (intval(($time_differnce / $days)) > 0) {
            $var = date('D, d F h:i a', strtotime($created_time));
            return "on " . $var;
        } elseif (intval(($time_differnce / $hours)) > 1) {
            return intval(($time_differnce / $hours)) . ' hrs ' . 'ago';
        } elseif (intval(($time_differnce / $hours)) > 0) {
            return intval(($time_differnce / $hours)) . ' hr ' . 'ago';
        } elseif (intval(($time_differnce / $minutes)) > 1) {
            return intval(($time_differnce / $minutes)) . ' mins ' . 'ago';
        } elseif (intval(($time_differnce / $minutes)) > 0) {
            return intval(($time_differnce / $minutes)) . ' min ' . 'ago';
        } elseif (intval(($time_differnce)) > 1) {
            return "Just now";
        } else {
            return 'few seconds';
        }
    }

    //list hash tags
    public function hash_tag() {
        $query = $this->db->query("SELECT tag_name FROM `post_hash_tag` order by tag_name asc");
        foreach ($query->result_array() as $row) {
            $tag_name = $row['tag_name'];
            $resultpost[] = array(
                "tag_name" => $tag_name
            );
        }
        return $resultpost;
    }

    //get healthwalll category
    public function healthwall_category() {
        $query = $this->db->query("SELECT  `id`, `category`,`hindi_healthwall_categorys`, `image` from healthwall_category order by id=31 asc, CASE WHEN LEFT(category, 1) LIKE '[a-Z]' THEN 1 ELSE 2 END ,LEFT(category, 1)");

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $category = $row['category'];
            $hindi_healthwall_category = $row['hindi_healthwall_categorys'];
            $img_file = $row['image'];
            if ($img_file != '') {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/category/' . $img_file;
            } else {
                $image = '';
            }

            $resultpost[] = array(
                "id" => $id,
                "category" => $category,
                "hindi_healthwall_category" => $hindi_healthwall_category,
                "image" => $image
            );
        }
        return $resultpost;
    }

    //get healhwall doctor's category
    public function healthwall_doctor_category() {
        $query = $this->db->query("SELECT `id`, `name`, `healthwall_category`, `image`, `comments` FROM `doctor_type` WHERE 1");
        foreach ($query->result_array() as $row) {
            $category_id = $row['id'];
            $name = $row['name'];
            $healthwall_category = $row['healthwall_category'];
            $img_file = $row['image'];

            if ($img_file != '') {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/doctor_category/' . $img_file;
            } else {
                $image = '';
            }

            $resultpost[] = array(
                "category_id" => $category_id,
                "name" => $name,
                "healthwall_category" => $healthwall_category,
                "image" => $image
            );
        }
        return $resultpost;
    }

    //encrypt string to md5 base64
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

    //decrypt string from md5 base64
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

    //get healthwall's post list
    public function post_list($user_id, $activity_user_id, $healthwall_category, $page) {
        $current_date = date('Y-m-d H:i:s');
        $dates = date('Y-m-d');
        $avail_offer = "";
        $limit = 5;
        $start = 0;
        $ad_array='0,';
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;

        if ($dates <= '2019-03-31') {
            $avail_offer = "OFFER_AAROGYAM_1_7";
        }

        if ($activity_user_id > 0) {
            $query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id, posts.type as post_type,IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time, posts.caption,posts.description as post,posts.is_anonymous,IFNULL(posts.post_location,'') AS post_location,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' and ((posts.user_id='$activity_user_id' and is_repost='0') or posts.repost_user_id='$activity_user_id') AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC limit $start, $limit");
            $count_query = $this->db->query("select id from posts where user_id<>'' and user_id<>'0' and ((user_id='$activity_user_id' and is_repost='0') or repost_user_id='$activity_user_id') AND healthwall_category<>'0' AND  id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=id AND user_id='$user_id') and active='1' order by id DESC ");
            $count_post = $count_query->num_rows();
        } else {
            if ($healthwall_category == '0') {
                $query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id, IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time, posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC limit $start, $limit");
                $count_query = $this->db->query("select id from posts where user_id<>'' and user_id<>'0' AND healthwall_category<>'0' AND id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=id AND user_id='$user_id') and active='1' order by id DESC");
                $count_post = $count_query->num_rows();
            } else {
                $query = $this->db->query("select healthwall_category.category AS healthwall_category, posts.id as post_id, IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time, posts.type as post_type,healthwall_category.id as healthwall_category_id,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  posts.healthwall_category IN ($healthwall_category) AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC limit $start, $limit");
                $count_query = $this->db->query("select id from posts where user_id<>'' and user_id<>'0' AND healthwall_category<>'0' AND healthwall_category IN ($healthwall_category) AND id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=id AND user_id='$user_id') and active='1' order by id DESC");
                $count_post = $count_query->num_rows();
            }
        }

        if ($count_post > 0) {
            $resultpost=array();
            foreach ($query->result_array() as $row) {
                $post_id = $row['post_id'];
                $listing_type = $row['vendor_id'];
                $post = $row['post'];
                $post_location = $row['post_location'];
                $healthwall_category_id = $row['healthwall_category_id'];
                if ($post != '') {
                    $post = preg_replace('~[\r\n]+~', '', $post);
                    if ($post_id > '2938') {
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

                $category = $row['post_category'];
                $is_anonymous = $row['is_anonymous'];
                $tag = $row['tag'];
                $post_type = trim($row['post_type']);
                if (strlen($post_type) < 1) {
                    $post_type = 'story';
                }
                $date = $row['created_at'];
                $caption = $row['caption'];
                $username = $row['name'];
                $post_user_id = $row['post_user_id'];
                $article_title = $row['article_title'];
                $article_image = $row['article_image'];
                $article_domain_name = $row['article_domain_name'];
                $article_url = $row['article_url'];
                $repost_user_id = $row['repost_user_id'];
                $repost_location = $row['repost_location'];
                $is_repost = $row['is_repost'];
                $repost_time = $this->get_time_difference_php($row['repost_time']);
                $healthwall_category_name = $row['healthwall_category'];

                $profile_query = $this->db->query("select media.source from media INNER JOIN users on users.avatar_id=media.id where users.id='$post_user_id'");
                if ($profile_query->num_rows() > 0) {
                    $get_profile = $profile_query->row_array();
                    $img_file = $get_profile['source'];
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $media_query = $this->db->query("SELECT media.id AS media_id,media.views,media.type AS media_type,IFNULL(media.caption,'') AS caption,media.source,post_media.img_width,post_media.img_height,post_media.video_width,post_media.video_height FROM media INNER JOIN post_media on media.id=post_media.media_id where post_media.post_id='$post_id'");
                $img_val = '';
                $images = '';
                $img_comma = '';
                $img_width = '';
                $img_height = '';
                $video_width = '';
                $video_height = '';

                $media_array = array();
                $n = '1';
                foreach ($media_query->result_array() as $media_row) {
                    $media_id = $media_row['media_id'];
                    $media_type = $media_row['media_type'];
                    $source = $media_row['source'];
                    if ($media_type == 'video') {
                        $thumb = 'https://d1g6k76aztls2p.cloudfront.net/healthwall_media/video/' . substr_replace($source, '_thumbnail.jpeg', strrpos($source, '.') + 0);
                        $images = 'https://d2ua8z9537m644.cloudfront.net/healthwall_media/' . $media_type . '/' . $source;
                    } else {
                        $thumb = '';
                        $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/' . $media_type . '/' . $source;
                    }
                    $caption = $media_row['caption'];
                    $img_width = $media_row['img_width'];
                    $img_height = $media_row['img_height'];
                    $video_width = $media_row['video_width'];
                    $video_height = $media_row['video_height'];
                    //$media_query2 = $this->db->query("SELECT(SELECT count(id) FROM post_video_views where media_id='$media_id' LIMIT 1) AS view_media_count, (SELECT count(id) FROM post_video_views where media_id='$media_id' and user_id='561' LIMIT 1) AS view_media_yes_no");			
                    //$media_list  	    = $media_query2->row_array();
                    $view_media_count = $media_row['views'];
                    $view_media_yes_no = '0';

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

                $total_query = $this->db->query("SELECT(SELECT count(id) FROM post_likes where post_id='$post_id' LIMIT 1) AS like_count, (SELECT count(id) FROM post_likes where post_id='$post_id' and user_id='$user_id' LIMIT 1) AS like_yes_no,(SELECT count(id) FROM post_follows where post_id='$post_id' LIMIT 1) AS follow_count,(SELECT count(id) FROM post_follows where post_id='$post_id' and user_id='$user_id' LIMIT 1 LIMIT 1) AS follow_post_yes_no,(SELECT count(id) FROM comments where post_id='$post_id' LIMIT 1) AS comment_count,(SELECT count(id) FROM post_views where post_id='$post_id' LIMIT 1) AS view_count,(SELECT count(id) FROM post_views where post_id='$post_id' and user_id='$user_id' LIMIT 1) AS view_post_yes_no,(SELECT count(id) FROM post_report where post_id='$post_id' and reporter_id='$user_id' AND post_type='healthwall' LIMIT 1) AS is_reported,(SELECT count(id) FROM post_save where post_id='$post_id' and user_id='$user_id' AND post_type='healthwall' LIMIT 1) AS is_post_save");

                $get_list = $total_query->row_array();
                $like_count = $get_list['like_count'];
                $like_yes_no = $get_list['like_yes_no'];
                $follow_count = $get_list['follow_count'];
                $follow_post_yes_no = $get_list['follow_post_yes_no'];
                $comment_count = $get_list['comment_count'];
                $view_count = $get_list['view_count'];
                $view_post_yes_no = $get_list['view_post_yes_no'];
                $is_reported = $get_list['is_reported'];
                $is_post_save = $get_list['is_post_save'];

                $share_url = "https://medicalwale.com/share/healthwall/" . $post_id;
                $tag = str_replace('&nbsp;', '', $tag);
                $tag = str_replace('&nbs', '', $tag);
                $tag = rtrim(str_replace(' ', '', $tag), ",");
                $date = $this->get_time_difference_php($date);

                //comments
                $repost_user_name = "";
                $repost = array();
                if ($is_repost) {
                    if (!empty($repost_user_id) && $repost_user_id != "") {
                        $repost_query = $this->db->query("select users.vendor_id,users.name,media.title, media.source as source, users.avatar_id as listing_type from users LEFT JOIN media on users.avatar_id = media.id where users.id = '$repost_user_id'");
                        $get_repost = $repost_query->row_array();
                        $listing_type = $get_repost['vendor_id'];
                        $respost_sorce = $get_repost['source'];
                        $repost_user_name = $get_repost['name'];
                        if (empty($respost_sorce)) {
                            $respost_sorce = "user_avatar.jpg";
                        }
                        $repost[] = array(
                            'repost_user_id' => $repost_user_id,
                            'repost_user_name' => $repost_user_name,
                            'repost_location' => $repost_location,
                            'repost_time' => $repost_time,
                            'listing_type' => $listing_type,
                            'repost_user_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/" . $respost_sorce
                        );
                    }
                    if (is_null($repost_user_name) || $repost_user_name == "") {
                        $repost_user_name = '';
                    }
                } else {
                    $repost = [];
                }
                $query_is_repost = $this->db->query("SELECT id FROM `posts` WHERE `user_id`='$post_user_id' AND `repost_user_id`='$user_id' AND `id`='$post_id' limit 1");
                $repost_counts = $query_is_repost->num_rows();
                if ($repost_counts > 0) {
                    $flag = '1';
                } else {
                    $flag = '0';
                }
                $comments = array();

                
  

                $resultpost[] = array(
                    'id' => $post_id,
                    'post_user_id' => $post_user_id,
                    'listing_type' => $listing_type,
                    'post_location' => $post_location,
                    'healthwall_category' => $healthwall_category_name,
                    'healthwall_category_id' => $healthwall_category_id,
                    'username' => $username,
                    'userimage' => $userimage,
                    'post_type' => $post_type,
                    'post' => str_replace('\n', '', $post),
                    'article_title' => str_replace('null', '', $article_title),
                    'article_image' => str_replace('null', '', $article_image),
                    'article_domain_name' => str_replace('null', '', $article_domain_name),
                    'article_url' => str_replace('null', '', $article_url),
                    'is_anonymous' => $is_anonymous,
                    'tag' => $tag,
                    'category' => $category,
                    'like_count' => $like_count,
                    'follow_count' => $follow_count,
                    'like_yes_no' => $like_yes_no,
                    'follow_post_yes_no' => $follow_post_yes_no,
                    'comment_count' => $comment_count,
                    'views' => $view_count,
                    'view_yes_no' => $view_post_yes_no,
                    'media' => $media_array,
                    'share_url' => $share_url,
                    'date' => $date,
                    'is_reported' => $is_reported,
                    'is_post_save' => $is_post_save,
                    'comments' => $comments,
                    'is_repost' => $is_repost,
                    'repost' => $repost,
                    'repost_flag' => $flag,
                    'ad_title' => '',
                    'ad_description' => '',
                    'ad_link' => '',
                    'ad_image' => ''
                );
                if(count($resultpost) % 5 == 0 && count($resultpost)> 0)  {
                    if($page>1){
                        $lim_srt=$page-1;
                        $count_limit=$lim_srt.',1';
                    }
                    else{
                        $count_limit='1';
                    }
                    
                    $query = $this->db->query("SELECT id,vendor_type,ad_main_cat,main_cat_id,ad_title,ad_description,ad_link,ad_image FROM sponsored_advertisements where NOT FIND_IN_SET(id, '$ad_array') ORDER BY rand() limit $count_limit");
                    $count = $query->num_rows();
                    if ($count > 0) {
                        $row  = $query->row_array();
                        $ad_img      = $row['ad_image'];
                        $ad_main_cat = $row['ad_main_cat'];
                        
                        $ad_type = ($ad_main_cat == 1) ? "brand" : (($ad_main_cat == 2)  ? "product" : "category");
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/Sponsored_files/' . $ad_img;
                        
                        $ad_array.=$row['id'].',';
                        $resultpost[] = array(
                            'id' => $row['id'],
                            'vendor_type'    => $row['vendor_type'],
                            'ad_type'        => $ad_type,
                            'ad_cat_id'      => $row['main_cat_id'],
                            'ad_title'       => $row['ad_title'],
                            'ad_description' => $row['ad_description'],
                            'ad_link'  => $row['ad_link'],
                            'ad_image' => $image,
                            'post_type'=>'ad',
                            'post_user_id' => '0',
                            'listing_type' => '0',
                            'post_location' => '',
                            'healthwall_category' => '0',
                            'healthwall_category_id' => '0',
                            'username' => '0',
                            'userimage' => '0',
                            'post' => '0',
                            'article_title' => '0',
                            'article_image' => '0',
                            'article_domain_name' => '0',
                            'article_url' => '0',
                            'is_anonymous' => '0',
                            'tag' => '0',
                            'category' => '0',
                            'like_count' => '0',
                            'follow_count' => '0',
                            'like_yes_no' =>'0',
                            'follow_post_yes_no' => '0',
                            'comment_count' => '0',
                            'views' => '0',
                            'view_yes_no' => '0',
                            'media' => array(),
                            'share_url' => '0',
                            'date' => '0',
                            'is_reported' =>'0',
                            'is_post_save' => '0',
                            'comments' => array(),
                            'is_repost' => '0',
                            'repost' => array(),
                            'repost_flag' => '0'
                        );
                    }
                }

                
            }

            $resultpost = array(
                "status" => 200,
                "message" => "success",
                "count" => $count_post,
                "current_date" => $current_date,
                "avail_offer" => $avail_offer,
                "data" => $resultpost
            );
        } else {
            $resultpost = array(
                "status" => 200,
                "message" => "success",
                "count" => 0,
                "current_date" => $current_date,
                "avail_offer" => $avail_offer,
                "data" => array()
            );
        }
        return $resultpost;
    }

    public function post_list1($user_id, $activity_user_id, $healthwall_category, $page) {
        $current_date = date('Y-m-d H:i:s');
        $limit = 5;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        if ($activity_user_id > 0) {
            // echo "1";
            $query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id, posts.type as post_type,IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time, posts.caption,posts.description as post,posts.is_anonymous,IFNULL(posts.post_location,'') AS post_location,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0'  and ((posts.user_id='$activity_user_id' and is_repost='0') or posts.repost_user_id='$activity_user_id') AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC limit $start, $limit");
            $count_query = $this->db->query("select posts.id as post_id from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' and ((posts.user_id='$activity_user_id' and is_repost='0') or posts.repost_user_id='$activity_user_id') AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC ");
            $count_post = $count_query->num_rows();
        } else {
            if ($healthwall_category == '0') {
                // echo "2";echo "select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id, IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time, posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC limit $start, $limit";
                $query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id, IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time, posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC limit $start, $limit");
                $count_query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC");
                $count_post = $count_query->num_rows();
            } else {
                //echo "3";
                $query = $this->db->query("select healthwall_category.category AS healthwall_category, posts.id as post_id, IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time, posts.type as post_type,healthwall_category.id as healthwall_category_id,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  posts.healthwall_category IN ($healthwall_category) AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC limit $start, $limit");


                $count_query = $this->db->query("select healthwall_category.category AS healthwall_category, posts.id as post_id,posts.type as post_type,healthwall_category.id as healthwall_category_id,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  posts.healthwall_category IN ($healthwall_category) AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC");
                $count_post = $count_query->num_rows();
            }
        }

        $notIds = 1;

        $get_sponsored_ads = $this->HealthwallModel->get_sponsored_ads($user_id, $notIds);
        print_r($get_sponsored_ads);
        die();
        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $post_id = $row['post_id'];
                $listing_type = $row['vendor_id'];
                $post = $row['post'];
                $post_location = $row['post_location'];
                $healthwall_category_id = $row['healthwall_category_id'];
                if ($post != '') {
                    $post = preg_replace('~[\r\n]+~', '', $post);
                    if ($post_id > '2938') {
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

                $category = $row['post_category'];
                $is_anonymous = $row['is_anonymous'];
                $tag = $row['tag'];
                $post_type = trim($row['post_type']);
                if (strlen($post_type) < 1) {
                    $post_type = 'story';
                }
                $date = $row['created_at'];
                $caption = $row['caption'];
                $username = $row['name'];
                $post_user_id = $row['post_user_id'];
                $article_title = $row['article_title'];
                $article_image = $row['article_image'];
                $article_domain_name = $row['article_domain_name'];
                $article_url = $row['article_url'];
                $repost_user_id = $row['repost_user_id'];
                $repost_location = $row['repost_location'];
                $is_repost = $row['is_repost'];
                $repost_time = $this->get_time_difference_php($row['repost_time']);
                $healthwall_category_name = $row['healthwall_category'];

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $media_query = $this->db->query("SELECT media.id AS media_id,media.type AS media_type,IFNULL(media.caption,'') AS caption,media.source,post_media.img_width,post_media.img_height,post_media.video_width,post_media.video_height FROM media INNER JOIN post_media on media.id=post_media.media_id where post_media.post_id='$post_id'");
                $img_val = '';
                $images = '';
                $img_comma = '';
                $img_width = '';
                $img_height = '';
                $video_width = '';
                $video_height = '';

                $media_array = array();
                foreach ($media_query->result_array() as $media_row) {
                    $media_id = $media_row['media_id'];
                    $media_type = $media_row['media_type'];
                    $source = $media_row['source'];
                    if ($media_type == 'video') {
                        $thumb = 'https://d1g6k76aztls2p.cloudfront.net/healthwall_media/video/' . substr_replace($source, '_thumbnail.jpeg', strrpos($source, '.') + 0);
                        $images = 'https://d2ua8z9537m644.cloudfront.net/healthwall_media/' . $media_type . '/' . $source;
                    } else {
                        $thumb = '';
                        $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/' . $media_type . '/' . $source;
                    }
                    $caption = $media_row['caption'];
                    $img_width = $media_row['img_width'];
                    $img_height = $media_row['img_height'];
                    $video_width = $media_row['video_width'];
                    $video_height = $media_row['video_height'];
                    $view_media_count = $this->db->select('id')->from('post_video_views')->where('media_id', $media_id)->get()->num_rows();

                    $view_media_yes_no_query = $this->db->query("SELECT id from post_video_views WHERE media_id='$media_id' and user_id='$user_id'");
                    $view_media_yes_no = $view_media_yes_no_query->num_rows();

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

                $like_count = $this->db->select('id')->from('post_likes')->where('post_id', $post_id)->get()->num_rows();
                $comment_count = $this->db->select('id')->from('comments')->where('post_id', $post_id)->get()->num_rows();
                $follow_count = $this->db->select('id')->from('post_follows')->where('post_id', $post_id)->get()->num_rows();
                $follow_yes_no_query = $this->db->query("SELECT id from post_follows where post_id='$post_id' and user_id='$user_id'");
                $follow_post_yes_no = $follow_yes_no_query->num_rows();
                $view_count = $this->db->select('id')->from('post_views')->where('post_id', $post_id)->get()->num_rows();
                $like_yes_no_query = $this->db->query("SELECT id from post_likes where post_id='$post_id' and user_id='$user_id'");
                $like_yes_no = $like_yes_no_query->num_rows();
                $view_yes_no_query = $this->db->query("SELECT id from post_views where post_id='$post_id' and user_id='$user_id'");
                $view_post_yes_no = $view_yes_no_query->num_rows();

                $is_reported_query = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='healthwall'");
                $is_reported = $is_reported_query->num_rows();

                $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='healthwall'");
                $is_post_save = $is_post_save_query->num_rows();

                $share_url = "https://medicalwale.com/share/healthwall/" . $post_id;
                $tag = str_replace('&nbsp;', '', $tag);
                $tag = str_replace('&nbs', '', $tag);
                $tag = rtrim(str_replace(' ', '', $tag), ",");
                $date = $this->get_time_difference_php($date);
                //comments
                $query_comment = $this->db->query("SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' order by comments.id desc LIMIT 0,3");
                $comments = array();
                $comment_counts = $query_comment->num_rows();
                if ($comment_counts > 0) {
                    foreach ($query_comment->result_array() as $rows) {
                        $comment_id = $rows['id'];
                        $comment_post_id = $rows['post_id'];
                        $comment = $rows['comment'];
                        $comment = preg_replace('~[\r\n]+~', '', $comment);
                        if ($comment_id > '9547') {
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
                        $comment_username = $rows['name'];
                        $comment_date = $rows['date'];
                        $comment_post_user_id = $rows['post_user_id'];
                        $comment_like_count = $this->db->select('id')->from('comment_likes')->where('comment_id', $comment_id)->get()->num_rows();
                        $comment_like_yes_no = $this->db->select('id')->from('comment_likes')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                        $comment_date = $this->get_time_difference_php($comment_date);
                        $comment_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->num_rows();
                        if ($comment_img_count > 0) {
                            $comment_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->row();
                            $comment_img_file = $comment_profile_query->source;
                            $comment_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_img_file;
                        } else {
                            $comment_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $query_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_post_user_id'");
                        $get_type = $query_listing_type->row_array();
                        $com_listing_type = $get_type['vendor_id'];
                        $comment_reply_array = '';
                        $query_comment_reply = $this->db->query("SELECT comments_reply.id,comments_reply.comment_id,comments_reply.post_id,comments_reply.comment,comments_reply.updated_at as date,users.name,comments_reply.user_id FROM comments_reply INNER JOIN users on users.id=comments_reply.user_id WHERE comments_reply.post_id='$post_id' and comments_reply.comment_id='$comment_id' order by comments_reply.id desc LIMIT 0,3");
                        $comment_reply = array();
                        $comment_count_reply = $query_comment_reply->num_rows();
                        if ($comment_count_reply > 0) {
                            foreach ($query_comment_reply->result_array() as $rows_reply) {
                                $comment_id = $rows_reply['comment_id'];
                                $comment_reply_id = $rows_reply['id'];
                                $comment_reply_post_id = $rows_reply['post_id'];
                                $comment_reply_username = $rows_reply['name'];
                                $comment_reply_date = $rows_reply['date'];
                                $comment_reply_user_id = $rows_reply['user_id'];
                                $comment_reply = $rows_reply['comment'];
                                if ($comment_reply != '' && is_numeric($comment_reply)) {
                                    $comment_reply = preg_replace('~[\r\n]+~', '', $comment_reply);
                                    $comment_reply_decrypt = $this->decrypt($comment_reply);
                                    $comment_reply_encrypt = $this->encrypt($comment_reply_decrypt);
                                    if ($comment_reply_encrypt == $comment_reply) {
                                        $comment_reply = $comment_reply_decrypt;
                                    }
                                }

                                $comment_reply_like_count = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
                                $comment_reply_like_yes_no = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
                                $comment_reply_date = $this->get_time_difference_php($comment_reply_date);
                                $comment_reply_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->num_rows();
                                if ($comment_reply_img_count > 0) {
                                    $comment_reply_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->row();
                                    $comment_reply_img_file = $comment_profile_query->source;
                                    $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_reply_img_file;
                                } else {
                                    $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                                }
                                $query_reply_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
                                $get_reply_type = $query_reply_listing_type->row_array();
                                $com_reply_listing_type = $get_reply_type['vendor_id'];
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
                            'listing_type' => $com_listing_type,
                            'comment_user_id' => $comment_post_user_id,
                            'username' => $comment_username,
                            'userimage' => $comment_userimage,
                            'like_count' => $comment_like_count,
                            'like_yes_no' => $comment_like_yes_no,
                            'post_id' => $comment_post_id,
                            'comment' => $comment,
                            'comment_date' => $comment_date,
                            'comment_reply' => $comment_reply_array
                        );
                    }
                } else {
                    $comments = array();
                }
                //comments
                $repost_user_name = "";
                $repost = array();
                if ($is_repost) {
                    if (!empty($repost_user_id) && $repost_user_id != "") {
                        $listing_type = $this->db->query("select vendor_id from users where id = '$repost_user_id' ")->row()->vendor_id;
                        $result = $this->db->query("select users.vendor_id as listing_type, media.title, media.source as source, users.avatar_id from users join media on(users.avatar_id = media.id) where users.id = '$repost_user_id' ");
                        $this->db->select("users.vendor_id as listing_type, media.title, media.source as source, users.avatar_id");
                        $this->db->from('users');
                        $this->db->where("users.id ", $repost_user_id);
                        $this->db->join('media', 'users.avatar_id = media.id');
                        $respost_sorce = $this->db->get()->row();
                        if (!empty($respost_sorce)) {
                            $respost_sorce = $respost_sorce->source;
                        } else {
                            $respost_sorce = "user_avatar.jpg";
                        }
                        //$post_user_id = $row['repost_user_id'];
                        $repost_user_name = $this->db->select('*')->where('id', $repost_user_id)->get('users')->row()->name;
                        $repost[] = array(
                            'repost_user_id' => $repost_user_id,
                            'repost_user_name' => $repost_user_name,
                            'repost_location' => $repost_location,
                            'repost_time' => $repost_time,
                            'listing_type' => $listing_type,
                            'repost_user_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/" . $respost_sorce
                        );
                    }
                    if (is_null($repost_user_name) || $repost_user_name == "") {
                        $repost_user_name = '';
                    }
                } else {
                    $repost = [];
                }
                $query_is_repost = $this->db->query("SELECT * FROM `posts` WHERE `user_id`='$post_user_id' AND `repost_user_id`='$user_id' AND `id`='$post_id'");
                $repost_counts = $query_is_repost->num_rows();
                if ($repost_counts > 0) {
                    $flag = '1';
                } else {
                    $flag = '0';
                }
                $resultpost[] = array(
                    'id' => $post_id,
                    'post_user_id' => $post_user_id,
                    'listing_type' => $listing_type,
                    'post_location' => $post_location,
                    'healthwall_category' => $healthwall_category_name,
                    'healthwall_category_id' => $healthwall_category_id,
                    'username' => $username,
                    'userimage' => $userimage,
                    'post_type' => $post_type,
                    'post' => str_replace('\n', '', $post),
                    'article_title' => str_replace('null', '', $article_title),
                    'article_image' => str_replace('null', '', $article_image),
                    'article_domain_name' => str_replace('null', '', $article_domain_name),
                    'article_url' => str_replace('null', '', $article_url),
                    'is_anonymous' => $is_anonymous,
                    'tag' => $tag,
                    'category' => $category,
                    'like_count' => $like_count,
                    'follow_count' => $follow_count,
                    'like_yes_no' => $like_yes_no,
                    'follow_post_yes_no' => $follow_post_yes_no,
                    'comment_count' => $comment_count,
                    'views' => $view_count,
                    'view_yes_no' => $view_post_yes_no,
                    'media' => $media_array,
                    'share_url' => $share_url,
                    'date' => $date,
                    'is_reported' => $is_reported,
                    'is_post_save' => $is_post_save,
                    'comments' => $comments,
                    'is_repost' => $is_repost,
                    'repost' => $repost,
                    'repost_flag' => $flag
                );
            }
            $resultpost = array(
                "status" => 200,
                "message" => "success",
                "count" => $count_post,
                "current_date" => $current_date,
                "data" => $resultpost
            );
        } else {
            $resultpost = array(
                "status" => 200,
                "message" => "success",
                "count" => 0,
                "current_date" => $current_date,
                "data" => array()
            );
        }
        return $resultpost;
    }

    //get healthwall's single post list
    public function single_post_list($user_id, $post_id) {
        $query = $this->db->query("select  IFNULL(posts.post_location,'') AS post_location,IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time,healthwall_category.category AS healthwall_category, posts.id as post_id,posts.type as post_type,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url from posts INNER JOIN users on users.id=posts.user_id  LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND posts.id='$post_id' order by posts.id DESC ");
        $count_post = $query->num_rows();
        $result_post = array();
        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $post_id = $row['post_id'];
                $listing_type = $row['vendor_id'];
                $post = $row['post'];
                if ($post != '') {
                    $post = preg_replace('~[\r\n]+~', '', $post);
                    if ($post_id > '2938') {
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
                $category = '';
                $is_anonymous = $row['is_anonymous'];
                $tag = $row['tag'];
                $post_type = $row['post_type'];
                $post_location = $row['post_location'];
                $date = $row['created_at'];
                $caption = $row['caption'];
                $username = $row['name'];
                $post_user_id = $row['post_user_id'];
                $article_title = $row['article_title'];
                $article_image = $row['article_image'];
                $article_domain_name = $row['article_domain_name'];
                $article_url = $row['article_url'];
                $healthwall_category_name = $row['healthwall_category'];
                $repost_user_id = $row['repost_user_id'];
                $repost_location = $row['repost_location'];
                $is_repost = $row['is_repost'];
                $repost_time = $this->get_time_difference_php($row['repost_time']);
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $media_query = $this->db->query("SELECT media.id AS media_id,media.type AS media_type,media.caption,media.source,post_media.img_width,post_media.img_height,post_media.video_width,post_media.video_height FROM media INNER JOIN post_media on media.id=post_media.media_id where post_media.post_id='$post_id'");
                $img_val = '';
                $images = '';
                $img_comma = '';
                $img_width = '';
                $img_height = '';
                $video_width = '';
                $video_height = '';
                $media_array = array();
                foreach ($media_query->result_array() as $media_row) {
                    $media_id = $media_row['media_id'];
                    $media_type = $media_row['media_type'];
                    $source = $media_row['source'];
                    if ($media_type == 'video') {
                        $thumb = 'https://d1g6k76aztls2p.cloudfront.net/healthwall_media/video/' . substr_replace($source, '_thumbnail.jpeg', strrpos($source, '.') + 0);
                        $images = 'https://d2ua8z9537m644.cloudfront.net/healthwall_media/' . $media_type . '/' . $source;
                    } else {
                        $thumb = '';
                        $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/' . $media_type . '/' . $source;
                    }
                    $caption = $media_row['caption'];
                    $img_width = $media_row['img_width'];
                    $img_height = $media_row['img_height'];
                    $video_width = $media_row['video_width'];
                    $video_height = $media_row['video_height'];
                    $view_media_count = $this->db->select('id')->from('post_video_views')->where('media_id', $media_id)->get()->num_rows();
                    $view_media_yes_no_query = $this->db->query("SELECT id from post_video_views WHERE media_id='$media_id' and user_id='$user_id'");
                    $view_media_yes_no = $view_media_yes_no_query->num_rows();
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
                $like_count = $this->db->select('id')->from('post_likes')->where('post_id', $post_id)->get()->num_rows();
                $comment_count = $this->db->select('id')->from('comments')->where('post_id', $post_id)->get()->num_rows();
                $follow_count = $this->db->select('id')->from('post_follows')->where('post_id', $post_id)->get()->num_rows();
                $follow_yes_no_query = $this->db->query("SELECT id from post_follows where post_id='$post_id' and user_id='$user_id'");
                $follow_post_yes_no = $follow_yes_no_query->num_rows();
                $view_count = $this->db->select('id')->from('post_views')->where('post_id', $post_id)->get()->num_rows();
                $like_yes_no_query = $this->db->query("SELECT id from post_likes where post_id='$post_id' and user_id='$user_id'");
                $like_yes_no = $like_yes_no_query->num_rows();
                $view_yes_no_query = $this->db->query("SELECT id from post_views where post_id='$post_id' and user_id='$user_id'");
                $view_post_yes_no = $view_yes_no_query->num_rows();
                $is_reported_query = $this->db->query("SELECT id from post_report WHERE post_id='$post_id' AND reporter_id='$user_id' AND post_type='healthwall'");
                $is_reported = $is_reported_query->num_rows();
                $is_post_save_query = $this->db->query("SELECT id from post_save WHERE post_id='$post_id' AND user_id='$user_id' AND post_type='healthwall'");
                $is_post_save = $is_post_save_query->num_rows();
                $share_url = "https://medicalwale.com/share/healthwall/" . $post_id;
                $tag = str_replace('&nbsp;', '', $tag);
                $tag = str_replace('&nbs', '', $tag);
                $tag = rtrim(str_replace(' ', '', $tag), ",");
                $date = $this->get_time_difference_php($date);
                //comments
                $repost_user_name = "";
                $repost = array();
                if ($is_repost) {
                    if (!empty($repost_user_id) && $repost_user_id != "") {
                        $query = $this->db->query("SELECT media.title, media.source, users.avatar_id FROM media INNER JOIN users ON(users.avatar_id = media.id) WHERE users.id = '$repost_user_id'")->row();
                        if (!empty($query)) {
                            $source = $query->source;
                        } else {
                            $source = "";
                        }
                        $repost_user_name = $this->db->select('*')->where('id', $repost_user_id)->get('users')->row();
                        if (!empty($repost_user_name)) {
                            $repost_user_name = $repost_user_name->name;
                        } else {
                            $repost_user_name = "";
                        }
                        $repost[] = array(
                            'repost_user_id' => $repost_user_id,
                            'repost_user_name' => $repost_user_name,
                            'repost_location' => $repost_location,
                            'repost_time' => $repost_time,
                            //'title' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$query->title,
                            'repost_user_image' => "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/" . $source
                        );
                    }
                    if (is_null($repost_user_name) || $repost_user_name == "") {
                        $repost_user_name = '';
                    }
                } else {
                    $repost = [];
                }
                $query_comment = $this->db->query("SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' order by comments.id desc LIMIT 0,3");
                $comments = array();
                $comment_counts = $query_comment->num_rows();
                if ($comment_counts > 0) {
                    foreach ($query_comment->result_array() as $rows) {
                        $comment_id = $rows['id'];
                        $comment_post_id = $rows['post_id'];
                        $comment = $rows['comment'];
                        $comment = preg_replace('~[\r\n]+~', '', $comment);
                        if ($comment_id > '9547') {
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
                        $comment_username = $rows['name'];
                        $comment_date = $rows['date'];
                        $comment_post_user_id = $rows['post_user_id'];
                        $comment_like_count = $this->db->select('id')->from('comment_likes')->where('comment_id', $comment_id)->get()->num_rows();
                        $comment_like_yes_no = $this->db->select('id')->from('comment_likes')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                        $comment_date = $this->get_time_difference_php($comment_date);
                        $comment_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->num_rows();
                        if ($comment_img_count > 0) {
                            $comment_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->row();
                            $comment_img_file = $comment_profile_query->source;
                            $comment_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_img_file;
                        } else {
                            $comment_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $query_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_post_user_id'");
                        $get_type = $query_listing_type->row_array();
                        $com_listing_type = $get_type['vendor_id'];

                        $comment_reply_array = '';
                        $query_comment_reply = $this->db->query("SELECT comments_reply.id,comments_reply.comment_id,comments_reply.post_id,comments_reply.comment,comments_reply.updated_at as date,users.name,comments_reply.user_id FROM comments_reply INNER JOIN users on users.id=comments_reply.user_id WHERE comments_reply.post_id='$post_id' and comments_reply.comment_id='$comment_id' order by comments_reply.id desc LIMIT 0,3");
                        $this->db->last_query();
                        $comment_reply = array();
                        $comment_count_reply = $query_comment_reply->num_rows();
                        if ($comment_count_reply > 0) {
                            foreach ($query_comment_reply->result_array() as $rows_reply) {
                                $comment_id = $rows_reply['comment_id'];
                                $comment_reply_id = $rows_reply['id'];
                                $comment_reply_post_id = $rows_reply['post_id'];
                                $comment_reply_username = $rows_reply['name'];
                                $comment_reply_date = $rows_reply['date'];
                                $comment_reply_user_id = $rows_reply['user_id'];
                                $comment_reply = $rows_reply['comment'];

                                $comment_reply = preg_replace('~[\r\n]+~', '', $comment_reply);
                                //&& is_numeric($comment_reply)
                                if ($comment_reply != '') {
                                    $comment_decrypt = $this->decrypt($comment_reply);
                                    $comment_encrypt = $this->encrypt($comment_decrypt);
                                    if ($comment_encrypt == $comment_reply) {
                                        $comment_reply = $comment_decrypt;
                                    }
                                }

                                $comment_reply_like_count = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
                                $comment_reply_like_yes_no = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
                                $comment_reply_date = $this->get_time_difference_php($comment_reply_date);
                                $comment_reply_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_reply_user_id)->get()->num_rows();

                                if ($comment_reply_img_count > 0) {
                                    $comment_reply_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_reply_user_id)->get()->row();

                                    $comment_reply_img_file = $comment_reply_profile_query->source;
                                    $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_reply_img_file;
                                } else {
                                    $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                                }
                                $query_reply_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
                                $get_reply_type = $query_reply_listing_type->row_array();
                                $com_reply_listing_type = $get_reply_type['vendor_id'];

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
                            'listing_type' => $listing_type,
                            'comment_user_id' => $comment_post_user_id,
                            'username' => $comment_username,
                            'userimage' => $comment_userimage,
                            'like_count' => $comment_like_count,
                            'like_yes_no' => $comment_like_yes_no,
                            'post_id' => $comment_post_id,
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
                    'id' => $post_id,
                    'post_user_id' => $post_user_id,
                    'listing_type' => $listing_type,
                    'post_location' => $post_location,
                    'healthwall_category' => $healthwall_category_name,
                    'username' => $username,
                    'userimage' => $userimage,
                    'post_type' => $post_type,
                    'post' => str_replace('\n', '', $post),
                    'article_title' => str_replace('null', '', $article_title),
                    'article_image' => str_replace('null', '', $article_image),
                    'article_domain_name' => str_replace('null', '', $article_domain_name),
                    'article_url' => str_replace('null', '', $article_url),
                    'is_anonymous' => $is_anonymous,
                    'tag' => $tag,
                    'category' => $category,
                    'like_count' => $like_count,
                    'follow_count' => $follow_count,
                    'like_yes_no' => $like_yes_no,
                    'follow_post_yes_no' => $follow_post_yes_no,
                    'comment_count' => $comment_count,
                    'views' => $view_count,
                    'view_yes_no' => $view_post_yes_no,
                    'media' => $media_array,
                    'share_url' => $share_url,
                    'date' => $date,
                    'is_reported' => $is_reported,
                    'is_post_save' => $is_post_save,
                    'comments' => $comments,
                    'is_repost' => $is_repost,
                    'repost' => $repost
                );
                $result_post = array(
                    "status" => 200,
                    "message" => "success",
                    "count" => $count_post,
                    "data" => $resultpost
                );
            }
        } else {
            $resultpost = array();
            $result_post = array(
                "status" => 200,
                "message" => "success",
                "data" => $resultpost
            );
        }
        return $result_post;
    }

    //like-dislike healthwall's post
    public function post_like($user_id, $post_id, $post_user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from post_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();
        $repost_query = $this->db->query("SELECT user_id as origial_post_user_id ,repost_user_id, is_repost from posts where id = '$post_id' ")->row();
        $is_repost = $repost_query->is_repost;
        $repost_user_id = $repost_query->repost_user_id;
        $origial_post_user_id = $repost_query->origial_post_user_id;
        if ($count > 0) {
            $this->db->query("DELETE FROM `post_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from post_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $post_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'created_at' => $created_at,
                'updated_at' => $created_at
            );
            $this->db->insert('post_likes', $post_likes);
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file = $profile_query->source;
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr = $user_plike->row_array();
            $usr_name = $getusr['name'];
            if ($is_repost) {
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id='$repost_user_id' AND id<>'$user_id'");
                $title = $usr_name . ' Beats on your repost';
                $msg = $usr_name . ' Beats on your repost click here to view post.';
                $postid = $repost_user_id;
            } else {
                $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id='$post_user_id' AND id<>'$user_id'");
                $title = $usr_name . ' Beats on your Post';
                $msg = $usr_name . ' Beats on your post click here to view post.';
                $postid = $post_user_id;
            }
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent = $token_status['agent'];
                $reg_id = $token_status['token'];
                $img_url = $userimage;
                $tag = 'text';
                $key_count = '1';
                //if($this->get_stop_notification_for_user)
                //   {
                //added by zak for all notification therough services
                $notification_array = array(
                    'title' => $title,
                    'msg' => $msg,
                    'img_url' => $img_url,
                    'tag' => $tag,
                    'order_status' => "",
                    'order_date' => "",
                    'order_id' => "",
                    'post_id' => $post_id,
                    'listing_id' => $post_user_id,
                    'booking_id' => "",
                    'invoice_no' => "",
                    'user_id' => $user_id,
                    'notification_type' => 'healthwall_notifications',
                    'notification_date' => date('Y-m-d H:i:s')
                );
                $this->db->insert('All_notification_Mobile', $notification_array);
                //end

                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
                //   }
            }
            $like_query = $this->db->query("SELECT id from post_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    //follow healthwall's post
    public function follow_post($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from post_follows where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `post_follows` WHERE user_id='$user_id' and post_id='$post_id'");
            $follow_query = $this->db->query("SELECT id from post_follows where post_id='$post_id'");
            $total_follow = $follow_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'follow' => '0',
                'total_follow' => $total_follow
            );
        } else {
            $follow_post = array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'created_at' => $created_at,
                'updated_at' => $created_at
            );
            $this->db->insert('post_follows', $follow_post);
            $follow_query = $this->db->query("SELECT id from post_follows where post_id='$post_id'");
            $total_follow = $follow_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'follow' => '1',
                'total_follow' => $total_follow
            );
        }
    }

    //increase healthwall's video view
    public function post_video_views($user_id, $media_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $total_query = $this->db->query("SELECT views FROM `media` where id='$media_id' limit 1");
        $get_list = $total_query->row_array();
        $views = $get_list['views'];
        $video_views = $views + 1;
        $querys = $this->db->query("UPDATE `media` SET `views`='$video_views' WHERE id='$media_id'");
        return array(
            'status' => 200,
            'message' => 'success',
            'video_views' => $video_views
        );
    }

    //increase healthwall's post view
    public function post_views($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from post_views where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();
        if ($count > 0) {
            $view_query = $this->db->query("SELECT id from post_views where post_id='$post_id'");
            $total_view = $view_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'view' => '1',
                'total_view' => $total_view
            );
        } else {
            $view = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('post_views', $view);
            $view_query = $this->db->query("SELECT id from post_views where post_id='$post_id'");
            $total_view = $view_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'view' => '1',
                'total_view' => $total_view
            );
        }
    }

    //insert healthwall's comment
    public function post_comment($user_id, $post_id, $comment, $post_user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $comments = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'description' => $comment,
            'created_at' => $created_at,
            'updated_at' => $created_at
        );
        $this->db->insert('comments', $comments);
        $post_follows = $this->db->query("SELECT GROUP_CONCAT(user_id) AS follow_users FROM post_follows WHERE post_id='$post_id' AND user_id<>'$post_user_id'");
        $get_post_follows = $post_follows->row_array();
        $follow_users = $get_post_follows['follow_users'];
        $follow_user_ids = explode(',', $follow_users);
        $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
        if ($img_count > 0) {
            $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
            $img_file = $profile_query->source;
            $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
        } else {
            $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
        }
        //follow post users notifications
        foreach ($follow_user_ids as $follow_user_ids_array) {
            $user_pcommnent = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id='$follow_user_ids_array' AND id<>'$user_id'");
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $getusr = $user_pcommnent->row_array();
                $usr_name = $getusr['name'];
                $agent = $token_status['agent'];
                $reg_id = $token_status['token'];
                $img_url = $userimage;
                $tag = 'text';
                $key_count = '1';

                $check_type = $this->db->query("SELECT type FROM posts WHERE id='$post_id'");
                $ptype = $check_type->row_array();
                $IsType = $ptype['type'];

                if ($IsType == trim('question')) {
                    $title = 'Thank you for connecting with us,';
                    $msg = 'doctors will be answering your query within 48 hours.';
                } else {
                    $title = $usr_name . ' commented on the post that you have followed';
                    $msg = $usr_name . ' commented on the post that you have followed click here to view post.';
                }

                // $title = $usr_name . ' commented on the post that you have followed';
                // $msg = $usr_name . ' commented on the post that you have followed click here to view post.';
                // if($this->get_stop_notification_for_user)
                //   {
                //added by zak for all notification therough services
                $notification_array = array(
                    'title' => $title,
                    'msg' => $msg,
                    'img_url' => $img_url,
                    'tag' => $tag,
                    'order_status' => "",
                    'order_date' => "",
                    'order_id' => "",
                    'post_id' => $post_id,
                    'listing_id' => "",
                    'booking_id' => "",
                    'invoice_no' => "",
                    'user_id' => $user_id,
                    'notification_type' => 'healthwall_notifications',
                    'notification_date' => date('Y-m-d H:i:s')
                );
                $this->db->insert('All_notification_Mobile', $notification_array);
                //end

                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
                //   }
            }
        }
        //post users notifications
        $user_pcommnent2 = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
        $customer_token2 = $this->db->query("SELECT token,agent, token_status FROM users WHERE id='$post_user_id' AND $post_user_id<>$user_id");
        $customer_token_count2 = $customer_token2->num_rows();
        if ($customer_token_count2 > 0) {
            $token_status2 = $customer_token2->row_array();
            $getusr2 = $user_pcommnent2->row_array();
            $usr_name = $getusr2['name'];
            $agent = $token_status2['agent'];
            $reg_id = $token_status2['token'];
            $img_url = $userimage;
            $tag = 'text';
            $key_count = '1';

            $check_type = $this->db->query("SELECT type FROM posts WHERE id='$post_id'");
            $ptype = $check_type->row_array();
            $IsType = $ptype['type'];

            if ($IsType == trim('question')) {
                $title = 'Thank you for connecting with us,';
                $msg = 'doctors will be answering your query within 48 hours.';
            } else {
                $title = $usr_name . ' commented on the post that you have followed';
                $msg = $usr_name . ' commented on the post that you have followed click here to view post.';
            }

            // $title = $usr_name . ' Commented on your Post ';
            // $msg = $usr_name . ' Commented on your click here to view post.';
            //  if($this->get_stop_notification_for_user)
            //    {
            //added by zak for all notification therough services
            $notification_array = array(
                'title' => $title,
                'msg' => $msg,
                'img_url' => $img_url,
                'tag' => $tag,
                'order_status' => "",
                'order_date' => "",
                'order_id' => "",
                'post_id' => $post_id,
                'listing_id' => $post_user_id,
                'booking_id' => "",
                'invoice_no' => "",
                'user_id' => $user_id,
                'notification_type' => 'healthwall_notifications',
                'notification_date' => date('Y-m-d H:i:s')
            );
            $this->db->insert('All_notification_Mobile', $notification_array);
            //end

            $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
            //     }
        }
        $comments_query = $this->db->query("SELECT id from comments where post_id='$post_id'");
        $total_comment = $comments_query->num_rows();
        return array(
            'status' => 201,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    //insert comment's reply
    public function post_comment_reply($user_id, $post_id, $comment_id, $comment, $post_user_id, $post_comment_user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $comments = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment_id' => $comment_id,
            'comment' => $comment,
            'created_at' => $created_at,
            'updated_at' => $created_at
        );
        $this->db->insert('comments_reply', $comments);
        $id = $this->db->insert_id();

        $query_comment_reply = $this->db->query("SELECT comments_reply.id,comments_reply.comment_id,comments_reply.post_id,comments_reply.comment,comments_reply.updated_at as date,users.name,comments_reply.user_id FROM comments_reply INNER JOIN users on users.id=comments_reply.user_id WHERE comments_reply.id='$id'")->row();
        $comment_reply_id = $query_comment_reply->id;
        $comment_reply_username = $query_comment_reply->name;
        $comment_reply_post_id = $query_comment_reply->post_id;
        $comment_reply_date = $query_comment_reply->date;
        $comment_reply_user_id = $query_comment_reply->user_id;
        $comment_reply = $query_comment_reply->comment;

        $comment_reply = preg_replace('~[\r\n]+~', '', $comment_reply);
        if ($comment_reply != '') {
            $comment_decrypt = $this->decrypt($comment_reply);
            $comment_encrypt = $this->encrypt($comment_decrypt);
            if ($comment_encrypt == $comment_reply) {
                $comment_reply = $comment_decrypt;
            }
        }
        $comment_reply_like_count = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
        $comment_reply_like_yes_no = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
        $comment_reply_date = $this->get_time_difference_php($comment_reply_date);
        $comment_reply_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
        if ($comment_reply_img_count > 0) {
            $comment_reply_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
            $comment_reply_img_file = $comment_reply_profile_query->source;
            $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_reply_img_file;
        } else {
            $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
        }
        $query_reply_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
        $get_reply_type = $query_reply_listing_type->row_array();
        $com_reply_listing_type = $get_reply_type['vendor_id'];

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

        $post_follows = $this->db->query("SELECT GROUP_CONCAT(user_id) AS follow_users FROM post_follows WHERE post_id='$post_id' AND user_id<>'$post_user_id'");
        $get_post_follows = $post_follows->row_array();
        $follow_users = $get_post_follows['follow_users'];
        $follow_user_ids = explode(',', $follow_users);
        $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
        if ($img_count > 0) {
            $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
            $img_file = $profile_query->source;
            $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
        } else {
            $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
        }
        $user_pcommnent = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
        $getusr = $user_pcommnent->row_array();
        $usr_name = $getusr['name'];

        //comment user notifications
        $customer_token3 = $this->db->query("SELECT token,agent,token_status FROM users WHERE id='$post_comment_user_id' AND $post_comment_user_id<>$user_id");
        $customer_token_count3 = $customer_token3->num_rows();
        if ($customer_token_count3 > 0) {
            $token_status3 = $customer_token3->row_array();
            $agent = $token_status3['agent'];
            $reg_id = $token_status3['token'];
            $img_url = $userimage;
            $tag = 'text';
            $key_count = '1';
            $title = $usr_name . ' Replied on your Comment ';
            $msg = $usr_name . ' Replied on your Comment click here to view the post.';
            // if($this->get_stop_notification_for_user)
            //    {
            //added by zak for all notification therough services
            $notification_array = array(
                'title' => $title,
                'msg' => $msg,
                'img_url' => $img_url,
                'tag' => $tag,
                'order_status' => "",
                'order_date' => "",
                'order_id' => "",
                'post_id' => $post_id,
                'listing_id' => $post_comment_user_id,
                'booking_id' => "",
                'invoice_no' => "",
                'user_id' => $user_id,
                'notification_type' => 'healthwall_notifications',
                'notification_date' => date('Y-m-d H:i:s')
            );
            $this->db->insert('All_notification_Mobile', $notification_array);
            //end


            if ($agent == 'ios') {
                $this->send_gcm_notify_ios($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id, $comment_id);
            } else {
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
            }
            //   }
        }

        $comments_query = $this->db->query("SELECT id from comments where post_id='$post_id'");
        $total_comment = $comments_query->num_rows();
        return array(
            'status' => 201,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment,
            'comment_return' => $comment_reply_array
        );
    }

    //like-dislike healthwall's comment 
    public function comment_like($user_id, $comment_id, $post_id, $comment_user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from comment_likes where comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `comment_likes` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from comment_likes where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $comment_likes = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id,
                'created_at' => $created_at,
                'updated_at' => $created_at
            );
            $this->db->insert('comment_likes', $comment_likes);
            $user_comment_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");

            $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file = $profile_query->source;
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }

            $customer_token = $this->db->query("SELECT token, agent, token_status FROM users WHERE id='$comment_user_id' AND id<>'$user_id'");

            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $getusr = $user_comment_plike->row_array();
                $usr_name = $getusr['name'];
                $agent = $token_status['agent'];
                $reg_id = $token_status['token'];
                $img_url = $userimage;
                $tag = 'text';
                $key_count = '1';
                $title = $usr_name . ' Beats on your Comment';
                $msg = $usr_name . ' Beats on your comment click here to view comment.';
                //if($this->get_stop_notification_for_user)
                //   {
                //added by zak for all notification therough services
                $notification_array = array(
                    'title' => $title,
                    'msg' => $msg,
                    'img_url' => $img_url,
                    'tag' => $tag,
                    'order_status' => "",
                    'order_date' => "",
                    'order_id' => "",
                    'post_id' => $post_id,
                    'listing_id' => $comment_user_id,
                    'booking_id' => "",
                    'invoice_no' => "",
                    'user_id' => $user_id,
                    'notification_type' => 'healthwall_notifications',
                    'notification_date' => date('Y-m-d H:i:s')
                );
                $this->db->insert('All_notification_Mobile', $notification_array);
                //end

                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
                //  }
            }

            $comment_query = $this->db->query("SELECT id from comment_likes where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    //like-dislike healthwall's comment reply
    public function comment_reply_like($user_id, $post_id, $comment_reply_id, $comment_reply_user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from comment_reply_likes where comment_reply_id='$comment_reply_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `comment_reply_likes` WHERE user_id='$user_id' and comment_reply_id='$comment_reply_id'");
            $comment_query = $this->db->query("SELECT id from comment_reply_likes where comment_reply_id='$comment_reply_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment_like' => $total_comment
            );
        } else {
            $comment_reply_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'comment_reply_id' => $comment_reply_id,
                'created_at' => $created_at,
                'updated_at' => $created_at
            );
            $this->db->insert('comment_reply_likes', $comment_reply_likes);
            $user_comment_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file = $profile_query->source;
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $customer_token = $this->db->query("SELECT token, agent, token_status FROM users WHERE id='$comment_reply_user_id' AND id<>'$user_id'");
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $getusr = $user_comment_plike->row_array();
                $usr_name = $getusr['name'];
                $agent = $token_status['agent'];
                $reg_id = $token_status['token'];
                $img_url = $userimage;
                $tag = 'text';
                $key_count = '1';
                $title = $usr_name . ' Beats on your Reply';
                $msg = $usr_name . ' Beats on your Reply click here to view the Reply.';
                //  if($this->get_stop_notification_for_user)
                //    {
                //added by zak for all notification therough services
                $notification_array = array(
                    'title' => $title,
                    'msg' => $msg,
                    'img_url' => $img_url,
                    'tag' => $tag,
                    'order_status' => "",
                    'order_date' => "",
                    'order_id' => "",
                    'post_id' => $post_id,
                    'listing_id' => $comment_reply_user_id,
                    'booking_id' => "",
                    'invoice_no' => "",
                    'user_id' => $user_id,
                    'notification_type' => 'healthwall_notifications',
                    'notification_date' => date('Y-m-d H:i:s')
                );
                $this->db->insert('All_notification_Mobile', $notification_array);
                //end

                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
                //   }
            }
            $comment_query = $this->db->query("SELECT id from comment_reply_likes where comment_reply_id='$comment_reply_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    //get healthwall's comment list
    public function comment_list($user_id, $post_id) {

        $comment_count = $this->db->select('id')->from('comments')->where('post_id', $post_id)->get()->num_rows();
        if ($comment_count > 0) {
            $query = $this->db->query("SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments LEFT JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' order by comments.id asc");
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $comment_id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '9547') {
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
                $like_count = $this->db->select('id')->from('comment_likes')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('comment_likes')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = $this->get_time_difference_php($date);
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $query_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$post_user_id'");
                $get_type = $query_listing_type->row_array();
                $listing_type = $get_type['vendor_id'];
                $date = $this->get_time_difference_php($date);
                $comment_reply_array = '';
                $query_comment_reply = $this->db->query("SELECT comments_reply.id,comments_reply.comment_id,comments_reply.post_id,comments_reply.comment,comments_reply.updated_at as date,users.name,comments_reply.user_id FROM comments_reply INNER JOIN users on users.id=comments_reply.user_id WHERE comments_reply.post_id='$post_id' and comments_reply.comment_id='$comment_id' order by comments_reply.id desc LIMIT 0,3");
                $this->db->last_query();
                $comment_reply = array();
                $comment_count_reply = $query_comment_reply->num_rows();
                if ($comment_count_reply > 0) {
                    foreach ($query_comment_reply->result_array() as $rows_reply) {
                        $comment_id = $rows_reply['comment_id'];
                        $comment_reply_id = $rows_reply['id'];
                        $comment_reply_post_id = $rows_reply['post_id'];
                        $comment_reply_username = $rows_reply['name'];
                        $comment_reply_date = $rows_reply['date'];
                        $comment_reply_user_id = $rows_reply['user_id'];
                        $comment_reply = $rows_reply['comment'];


                        if ($comment_reply != '') {
                            $comment_reply = preg_replace('~[\r\n]+~', '', $comment_reply);
                            $comment_reply_decrypt = $this->decrypt($comment_reply);
                            $comment_reply_encrypt = $this->encrypt($comment_reply_decrypt);
                            if ($comment_reply_encrypt == $comment_reply) {
                                $comment_reply = $comment_reply_decrypt;
                            }
                        }

                        $comment_reply_like_count = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
                        $comment_reply_like_yes_no = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
                        $comment_reply_date = $this->get_time_difference_php($comment_reply_date);
                        $comment_reply_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_reply_user_id)->get()->num_rows();

                        if ($comment_reply_img_count > 0) {
                            $comment_reply_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_reply_user_id)->get()->row();

                            $comment_reply_img_file = $comment_reply_profile_query->source;
                            $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_reply_img_file;
                        } else {
                            $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $query_reply_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
                        $get_reply_type = $query_reply_listing_type->row_array();
                        $com_reply_listing_type = $get_reply_type['vendor_id'];


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
                    'id' => $id,
                    'listing_type' => $listing_type,
                    'comment_user_id' => $post_user_id,
                    'username' => $username,
                    'userimage' => $userimage,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'post_id' => $post_id,
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

    public function comment_reply_list($user_id, $post_id, $comment_id) {

        $comment_count = $this->db->select('id')->from('comments')->where('id', $comment_id)->get()->num_rows();
        if ($comment_count > 0) {
            $query = $this->db->query("SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments LEFT JOIN users on users.id=comments.user_id WHERE comments.id='$comment_id' order by comments.id ASC");
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $comment_id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '9547') {
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
                $like_count = $this->db->select('id')->from('comment_likes')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('comment_likes')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = $this->get_time_difference_php($date);
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $query_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$post_user_id'");
                $get_type = $query_listing_type->row_array();
                $listing_type = $get_type['vendor_id'];
                $date = $this->get_time_difference_php($date);
                $comment_reply_array = '';
                $query_comment_reply = $this->db->query("SELECT comments_reply.id,comments_reply.comment_id,comments_reply.post_id,comments_reply.comment,comments_reply.updated_at as date,users.name,comments_reply.user_id FROM comments_reply INNER JOIN users on users.id=comments_reply.user_id WHERE comments_reply.post_id='$post_id' and comments_reply.comment_id='$comment_id' order by comments_reply.id desc LIMIT 0,3");
                $this->db->last_query();
                $comment_reply = array();
                $comment_count_reply = $query_comment_reply->num_rows();
                if ($comment_count_reply > 0) {
                    foreach ($query_comment_reply->result_array() as $rows_reply) {
                        $comment_id = $rows_reply['comment_id'];
                        $comment_reply_id = $rows_reply['id'];
                        $comment_reply_post_id = $rows_reply['post_id'];
                        $comment_reply_username = $rows_reply['name'];
                        $comment_reply_date = $rows_reply['date'];
                        $comment_reply_user_id = $rows_reply['user_id'];
                        $comment_reply = $rows_reply['comment'];


                        if ($comment_reply != '') {
                            $comment_reply = preg_replace('~[\r\n]+~', '', $comment_reply);
                            $comment_reply_decrypt = $this->decrypt($comment_reply);
                            $comment_reply_encrypt = $this->encrypt($comment_reply_decrypt);
                            if ($comment_reply_encrypt == $comment_reply) {
                                $comment_reply = $comment_reply_decrypt;
                            }
                        }

                        $comment_reply_like_count = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
                        $comment_reply_like_yes_no = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
                        $comment_reply_date = $this->get_time_difference_php($comment_reply_date);
                        $comment_reply_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_reply_user_id)->get()->num_rows();

                        if ($comment_reply_img_count > 0) {
                            $comment_reply_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_reply_user_id)->get()->row();

                            $comment_reply_img_file = $comment_reply_profile_query->source;
                            $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_reply_img_file;
                        } else {
                            $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $query_reply_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
                        $get_reply_type = $query_reply_listing_type->row_array();
                        $com_reply_listing_type = $get_reply_type['vendor_id'];


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
                    'id' => $id,
                    'listing_type' => $listing_type,
                    'comment_user_id' => $post_user_id,
                    'username' => $username,
                    'userimage' => $userimage,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'post_id' => $post_id,
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

    //hide healthwall's post
    public function post_hide($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `posts_hide` WHERE  user_id='$user_id' and post_id='$post_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `posts_hide` WHERE user_id='$user_id' and post_id='$post_id'");
            return array(
                'status' => 200,
                'message' => 'deleted',
                'is_hide' => '0'
            );
        } else {
            $posts_hide = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('posts_hide', $posts_hide);

            return array(
                'status' => 200,
                'message' => 'success',
                'is_hide' => '1'
            );
        }
    }

    //delete healthwall's post
    public function post_delete($user_id, $post_id, $repost_user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        if ($repost_user_id > 0) {
            $count_query = $this->db->query("SELECT id from `posts` WHERE  repost_user_id='$repost_user_id' and id='$post_id'");
            $count = $count_query->num_rows();

            if ($count > 0) {
                $this->db->query("DELETE FROM `posts` WHERE repost_user_id='$repost_user_id' and id='$post_id'");

                $this->db->query("DELETE FROM `post_media` WHERE post_id='$post_id'");


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
        } else {
            $count_query = $this->db->query("SELECT id from `posts` WHERE  user_id='$user_id' and id='$post_id'");
            $count = $count_query->num_rows();

            if ($count > 0) {
                $this->db->query("DELETE FROM `posts` WHERE user_id='$user_id' and id='$post_id'");


                //unlink images
                $media_query = $this->db->query("SELECT media.type AS media_type,media.source FROM media INNER JOIN post_media on media.id=post_media.media_id WHERE post_media.post_id='$post_id'");

                foreach ($media_query->result_array() as $media_row) {
                    $source = $media_row['source'];
                    $media_type = $media_row['media_type'];
                    $file = 'images/healthwall_media/' . $media_type . '/' . $source;
                    @unlink(trim($file));
                    DeleteFromToS3($file);
                }
                //unlink images ends

                $this->db->query("DELETE FROM `post_media` WHERE post_id='$post_id'");


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
    }

    //repost healthwall's post
    public function repost($repost_user_id, $post_id, $repost_location) {
        $query = $this->db->query("SELECT id FROM users where id = '$repost_user_id' ");
        $count = $query->num_rows();
        if ($count > 0) {
            //$query = $this->db->query("SELECT posts.*, post_media.* FROM posts INNER JOIN post_media ON(posts.id = post_media.post_id) where posts.id = '$post_id';");
            $query = $this->db->query("SELECT * FROM posts where id = '$post_id' ")->row();
            $is_anonymous = $query->is_anonymous;
            $active = $query->active;
            $post_id = $query->id;
            $post_user_id = $query->user_id;
            $healthwall_category = $query->healthwall_category;
            $type = $query->type;
            $post_location = $query->post_location;
            $description = $query->description;
            $article_url = $query->article_url;
            $article_title = $query->article_title;
            $article_desc = $query->article_desc;
            $article_image = $query->article_image;
            $article_domain_name = $query->article_domain_name;
            $caption = $query->caption;
            $tag = $query->tag;
            $category = $query->category;
            $location = $query->location;
            $created_at = $query->created_at;
            $updated_at = $query->updated_at;
            $deleted_at = $query->deleted_at;
            $shared_post_id = $query->shared_post_id;
            $v_id = $query->v_id;

            $post = preg_replace('~[\r\n]+~', '', $description);
            if ($post_id > '2938') {
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

            $this->db->query("INSERT INTO posts(is_anonymous, active, user_id, healthwall_category, type, post_location, description, article_url, article_title, article_desc, article_image, article_domain_name, caption, tag, category, location, created_at, updated_at, deleted_at, shared_post_id, v_id, repost_user_id, repost_location,is_repost )
            VALUES ('$is_anonymous', '$active', '$post_user_id', '$healthwall_category', '$type', '$post_location', '$post', '$article_url', '$article_title', '$article_desc', '$article_image', '$article_domain_name', '$caption', '$tag', '$category', '$location', '$created_at','$updated_at', '$deleted_at', '$shared_post_id', '$v_id', '$repost_user_id', '$repost_location', '1')");

            $last_id = $this->db->insert_id();

            $this->db->query("insert into post_likes (post_id, user_id, created_at, updated_at) select '$last_id', user_id, created_at, updated_at from post_likes where post_id ='$post_id'");
            $this->db->query("insert into post_follows (post_id, user_id, created_at, updated_at) select '$last_id', user_id, created_at, updated_at from post_follows where post_id ='$post_id'");

            $query1 = $this->db->query("SELECT * FROM post_media where post_id = '$post_id' ");
            $result = "";
            foreach ($query1->result_array() as $row) {
                $media_id = $row['media_id'];
                $created_at = $row['created_at'];
                $updated_at = $row['updated_at'];
                $deleted_at = $row['deleted_at'];
                $img_width = $row['img_width'];
                $img_height = $row['img_height'];
                $video_width = $row['video_width'];
                $video_height = $row['video_height'];
                $this->db->query("INSERT INTO post_media(post_id, media_id, created_at, updated_at, deleted_at, img_width, img_height, video_width, video_height) VALUES ('$last_id', '$media_id', '$created_at', '$updated_at', '$deleted_at', '$img_width', '$img_height', '$video_width', '$video_height')");
                $result = $this->db->affected_rows();
            }


            $comment_query = $this->db->query("SELECT id,user_id,description,created_at,updated_at FROM comments where post_id='$post_id'");
            foreach ($comment_query->result_array() as $comment_row) {
                $comment_id = $comment_row['id'];
                $comment_user_id = $comment_row['user_id'];
                $comment_description = $comment_row['description'];
                $comment_created_at = $comment_row['created_at'];
                $comment_updated_at = $comment_row['updated_at'];
                $this->db->query("INSERT INTO comments (post_id, user_id, description, created_at, updated_at) VALUES ('$last_id','$comment_user_id','$comment_description','$comment_created_at','$comment_updated_at')");
                $comment_new_id = $this->db->insert_id();
                $comment_like_query = $this->db->query("SELECT user_id,comment_id,created_at,updated_at FROM comment_likes where comment_id='$comment_id'");
                foreach ($comment_like_query->result_array() as $comment_like_row) {
                    $comment_like_user_id = $comment_like_row['user_id'];
                    $comment_like_comment_id = $comment_like_row['comment_id'];
                    $comment_like_created_at = $comment_like_row['created_at'];
                    $comment_like_updated_at = $comment_like_row['updated_at'];
                    $this->db->query("insert into comment_likes (comment_id, user_id, created_at, updated_at) VALUES ('$comment_new_id', '$comment_like_user_id', '$comment_like_created_at', '$comment_like_updated_at')");
                }

                //comment reply
                $comment_reply_query = $this->db->query("SELECT id,user_id,comment,created_at,updated_at FROM comments_reply where comment_id='$comment_id'");
                foreach ($comment_reply_query->result_array() as $comment_reply_row) {
                    $comment_reply_id = $comment_reply_row['id'];
                    $comment_reply_user_id = $comment_reply_row['user_id'];
                    $comment_reply_comment = $comment_reply_row['comment'];
                    $comment_reply_created_at = $comment_reply_row['created_at'];
                    $comment_reply_updated_at = $comment_reply_row['updated_at'];
                    $this->db->query("INSERT INTO comments_reply(post_id, comment, user_id, created_at, updated_at, comment_id) VALUES ('$last_id','$comment_reply_comment','$comment_reply_user_id','$comment_reply_created_at','$comment_reply_updated_at','$comment_new_id')");
                    $comment_reply_new_id = $this->db->insert_id();

                    $comment_reply_like_query = $this->db->query("SELECT user_id,comment_reply_id,created_at,updated_at,post_id FROM comment_reply_likes where comment_reply_id='$comment_reply_id'");
                    foreach ($comment_reply_like_query->result_array() as $comment_reply_like_row) {
                        $comment_reply_like_user_id = $comment_reply_like_row['user_id'];
                        $comment_reply_like_comment_reply_id = $comment_reply_like_row['comment_reply_id'];
                        $comment_reply_like_created_at = $comment_reply_like_row['created_at'];
                        $comment_reply_like_updated_at = $comment_reply_like_row['updated_at'];
                        $this->db->query("INSERT INTO comment_reply_likes(user_id, comment_reply_id, post_id, created_at, updated_at) VALUES ('$comment_reply_like_user_id','$comment_reply_new_id','$last_id','$comment_reply_like_created_at','$comment_reply_like_updated_at')");
                    }
                }
            }


            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$repost_user_id'");

            $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                $img_file = $profile_query->source;
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }


            //$customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id='$post_user_id' AND id<>'$post_user_id'");
            $customer_token = $this->db->query("SELECT token,agent,token_status FROM users WHERE id='$post_user_id'");

            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $getusr = $user_plike->row_array();

                $usr_name = $getusr['name'];
                $agent = $token_status['agent'];
                $reg_id = $token_status['token'];
                $img_url = $userimage;
                $tag = 'text';
                $key_count = '1';
                $title = $usr_name . ' Reposted your Post';
                $msg = $usr_name . '  Reposted your post click here to view post.';
                //        if($this->get_stop_notification_for_user)
                //        {
                //added by zak for all notification therough services
                $notification_array = array(
                    'title' => $title,
                    'msg' => $msg,
                    'img_url' => $img_url,
                    'tag' => $tag,
                    'order_status' => "",
                    'order_date' => "",
                    'order_id' => "",
                    'post_id' => $post_id,
                    'listing_id' => "",
                    'booking_id' => "",
                    'invoice_no' => "",
                    'user_id' => $user_id,
                    'notification_type' => 'healthwall_notifications8',
                    'notification_date' => date('Y-m-d H:i:s')
                );
                $this->db->insert('All_notification_Mobile', $notification_array);
                //end

                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $last_id);
                //      }
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

    public function healthwall_post_comment_all_reply_list($comment_id, $user_id) {
        $query_comment_reply = $this->db->query("SELECT comments_reply.id,comments_reply.comment_id,comments_reply.post_id,comments_reply.comment,comments_reply.updated_at as date,users.name,comments_reply.user_id FROM comments_reply INNER JOIN users on users.id=comments_reply.user_id WHERE comments_reply.comment_id='$comment_id' order by comments_reply.id desc");
        $this->db->last_query();
        $comment_reply = array();
        $comment_count_reply = $query_comment_reply->num_rows();
        if ($comment_count_reply > 0) {
            foreach ($query_comment_reply->result_array() as $rows_reply) {
                $comment_id = $rows_reply['comment_id'];
                $comment_reply_id = $rows_reply['id'];
                $comment_reply_post_id = $rows_reply['post_id'];
                $comment_reply_username = $rows_reply['name'];
                $comment_reply_date = $rows_reply['date'];
                $comment_reply_user_id = $rows_reply['user_id'];
                $comment_reply = $rows_reply['comment'];
                $comment_reply = preg_replace('~[\r\n]+~', '', $comment_reply);
                if ($comment_reply != '') {
                    $comment_decrypt = $this->decrypt($comment_reply);
                    $comment_encrypt = $this->encrypt($comment_decrypt);
                    if ($comment_encrypt == $comment_reply) {
                        $comment_reply = $comment_decrypt;
                    }
                }
                $comment_reply_like_count = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
                $comment_reply_like_yes_no = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
                $comment_reply_date = $this->get_time_difference_php($comment_reply_date);
                $comment_reply_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_reply_user_id)->get()->num_rows();
                if ($comment_reply_img_count > 0) {
                    $comment_reply_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_reply_user_id)->get()->row();

                    $comment_reply_img_file = $comment_reply_profile_query->source;
                    $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_reply_img_file;
                } else {
                    $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $query_reply_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
                $get_reply_type = $query_reply_listing_type->row_array();
                $com_reply_listing_type = $get_reply_type['vendor_id'];

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
        return $comment_reply_array;
    }

    public function healthwall_comment_all_reply_list($comment_id, $user_id) {
        $query_ask_saheli_comment_reply = $this->db->query("SELECT comments_reply.id,comments_reply.comment_id,comments_reply.post_id,comments_reply.comment,comments_reply.updated_at as date,users.name,comments_reply.user_id FROM comments_reply INNER JOIN users on users.id=comments_reply.user_id WHERE comments_reply.comment_id='$comment_id' order by comments_reply.id desc");
        $comment_reply = array();
        $ask_saheli_comment_count_reply = $query_ask_saheli_comment_reply->num_rows();
        if ($ask_saheli_comment_count_reply > 0) {
            foreach ($query_ask_saheli_comment_reply->result_array() as $rows_reply) {
                $comment_id = $rows_reply['comment_id'];
                $comment_reply_id = $rows_reply['id'];
                $comment_reply_post_id = $rows_reply['post_id'];
                $comment_reply_username = $rows_reply['name'];
                $comment_reply_date = $rows_reply['date'];
                $comment_reply_user_id = $rows_reply['user_id'];
                $comment_reply = $rows_reply['comment'];
                $comment_reply = preg_replace('~[\r\n]+~', '', $comment_reply);
                $comment_decrypt = $this->decrypt($comment_reply);
                $comment_encrypt = $this->encrypt($comment_decrypt);
                if ($comment_encrypt == $comment_reply) {
                    $comment_reply = $comment_decrypt;
                }
                $comment_reply_like_count = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->get()->num_rows();
                $comment_reply_like_yes_no = $this->db->select('id')->from('comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
                $commencomment_listly_date = $this->get_time_difference_php($comment_reply_date);
                $comment_reply_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_reply_post_id)->get()->num_rows();
                if ($comment_reply_img_count > 0) {
                    $comment_reply_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_reply_user_id)->get()->row();
                    $comment_reply_img_file = $comment_reply_profile_query->source;
                    $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $comment_reply_img_file;
                } else {
                    $comment_reply_userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $query_reply_listing_type = $this->db->query("SELECT vendor_id FROM users WHERE id='$comment_reply_user_id'");
                $get_reply_type = $query_reply_listing_type->row_array();
                $com_reply_listing_type = $get_reply_type['vendor_id'];
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
        return $comment_reply_array;
    }

    public function check_time_format($time) {
        $time_filter = preg_replace('/\s+/', '', $time);
        $final_time = date("h:i A", strtotime($time_filter));
        return $final_time;
    }

    public function is_free_delivery_staus($free_start_time, $free_end_time) {
        $free_start_time_st = date("h:i A", strtotime($free_start_time));
        $free_end_time_st = date("h:i A", strtotime($free_end_time));
        $current_time_st = date('h:i A');


        $date1 = DateTime::createFromFormat('H:i a', $current_time_st);
        $date2 = DateTime::createFromFormat('H:i a', $free_start_time_st);
        $date3 = DateTime::createFromFormat('H:i a', $free_end_time_st);

        if ($date2 < $date3 && $date1 <= $date3) {
            $date3->modify('+1 day')->format('H:i a');
        } elseif ($date2 > $date3 && $date1 >= $date3) {
            $date3->modify('+1 day')->format('H:i a');
        }


        if ($date1 > $date2 && $date1 < $date3) {
            $is_free_delivery = 'Yes';
        } else {
            $is_free_delivery = 'No';
        }

        return $is_free_delivery;
    }

    public function check_day_status($day_type, $days_closed, $is_24hrs_available, $store_open, $store_close) {

        if ($is_24hrs_available === 'Yes') {
            if ($day_type == 'Monday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Tuesday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Wednesday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Thursday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Friday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Saturday') {
                $time = '12:00 AM-11:59 PM';
            }

            if ($day_type == 'Sunday') {
                $time = '12:00 AM-11:59 PM';
            }
        } else {
            if ($day_type == 'Monday') {
                if ($days_closed == 'Monday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Tuesday') {
                if ($days_closed == 'Tuesday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Wednesday') {
                if ($days_closed == 'Wednesday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Thursday') {
                if ($days_closed == 'Thursday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Friday') {
                if ($days_closed == 'Friday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Saturday') {
                if ($days_closed == 'Saturday Closed') {
                    $time = 'close-close';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }

            if ($day_type == 'Sunday') {
                if ($days_closed == 'Sunday Closed') {
                    $time = 'close-close';
                } elseif ($days_closed == 'Sunday Half Day') {
                    $time = $store_open . '-02:00 PM';
                } else {
                    $time = $store_open . '-' . $store_close;
                }
            }
        }

        return $time;
    }

    public function check_current_delivery_charges($is_24hrs_available, $day_night_delivery, $free_start_time, $free_end_time, $night_delivery_charge, $days_closed, $is_min_order_delivery, $min_order) {

        date_default_timezone_set('Asia/Kolkata');
        $free_start_time_st = date("h:i A", strtotime($free_start_time));
        $free_end_time_st = date("h:i A", strtotime($free_end_time));
        $current_time_st = date('h:i A');

        $current_time = DateTime::createFromFormat('H:i a', $current_time_st);
        $system_start_time = DateTime::createFromFormat('H:i a', $free_start_time_st);
        $system_end_time = DateTime::createFromFormat('H:i a', $free_end_time_st);

        if ($system_start_time < $system_end_time && $current_time <= $system_end_time) {
            $system_end_time->modify('+1 day')->format('H:i a');
        } elseif ($system_start_time > $system_end_time && $current_time >= $system_end_time) {
            $system_end_time->modify('+1 day')->format('H:i a');
        }

        if ($is_24hrs_available == 'Yes') {
            if ($day_night_delivery == 'Yes') {

                if ($current_time > $system_start_time && $current_time < $system_end_time) {
                    if ($is_min_order_delivery == 'Yes') {
                        $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                    } else {
                        $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                    }
                } else {
                    $current_delivery_charges = 'Delivery Charges Applied Rs ' . $night_delivery_charge;
                }
            } else {
                if ($current_time > $system_start_time && $current_time < $system_end_time) {
                    // $current_delivery_charges = 'Free Delivery Available';	
                    if ($is_min_order_delivery == 'Yes') {
                        $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                    } else {
                        $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                    }
                } else {
                    $current_delivery_charges = 'Delivery Not Available Now';
                }
            }
        } else {

            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                //$current_delivery_charges = 'Free Delivery Available';
                if ($is_min_order_delivery == 'Yes') {
                    $current_delivery_charges = 'Free Delivery Available Above Rs ' . $min_order;
                } else {
                    $current_delivery_charges = 'Delivery Not Available Below Rs ' . $min_order;
                }
            } else {
                $current_delivery_charges = 'Delivery Not Available Now';
            }
        }


        return $current_delivery_charges;
    }

    public function vendor_list($vendor_id, $mlat, $mlng) {
        $radius = '5';

        function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return $dist;
        }

        /* $sql_vendor = sprintf("SELECT vendor_id, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM users  WHERE is_active='1' HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
          $query_user = $this->db->query($sql_vendor); */
        echo $vendor_id;
        if ($vendor_id === '13') {
            $sql = sprintf("SELECT medicalwale_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE medicalwale_discount <>'0' AND discount <>'0' AND is_approval='1' AND is_active='1' HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
        }/*
          else if($vendor_id==='13'){

          } */ else if ($vendor_id === '5') {
            echo $sql = sprintf("SELECT medicalwale_discount,`id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,`discount`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
        } else if ($vendor_id === '8') {
            
        } else if ($vendor_id === '10') {
            
        } else if ($vendor_id === '6') {
            
        }

        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $lat = $row['lat'];
            $lng = $row['lng'];
            $medical_id = $row['user_id'];
            $medical_name = $row['medical_name'];
            $store_manager = $row['store_manager'];
            $address1 = $row['address1'];
            $address2 = $row['address2'];
            $pincode = $row['pincode'];
            $city = $row['city'];
            $state = $row['state'];
            $contact_no = $row['contact_no'];
            $whatsapp_no = $row['whatsapp_no'];
            $email = $row['email'];
            $store_since = $row['store_since'];
            $website = $row['website'];
            $discount = $row['discount'];
            $medicalwale_discount = $row['medicalwale_discount'];
            //print_r($discount);
            $reach_area = $row['reach_area'];
            $is_24hrs_available = $row['is_24hrs_available'];
            if ($is_24hrs_available == 'Yes') {
                $store_open = date("h:i A", strtotime("12:00 AM"));
                $store_close = date("h:i A", strtotime("11:59 PM"));
            } else {
                $store_open = $this->check_time_format($row['store_open']);
                $store_close = $this->check_time_format($row['store_close']);
            }
            $day_night_delivery = $row['day_night_delivery'];
            $free_start_time = $this->check_time_format($row['free_start_time']);
            $free_end_time = $this->check_time_format($row['free_end_time']);
            $is_free_delivery = $this->is_free_delivery_staus($free_start_time, $free_end_time);
            $days_closed = $row['days_closed'];
            $min_order = $row['min_order'];
            $is_min_order_delivery = $row['is_min_order_delivery'];
            $min_order_delivery_charge = $row['min_order_delivery_charge'];
            $night_delivery_charge = $row['night_delivery_charge'];
            $payment_type = $row['payment_type'];

            $online_offline = $row['online_offline'];
            $km = $row['distance'];
            $profile_pic = $row['profile_pic'];
            if ($row['profile_pic'] != '') {
                $profile_pic = $row['profile_pic'];
                $profile_pic = str_replace(' ', '%20', $profile_pic);
                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $profile_pic;
            } else {
                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
            }

            $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $medical_id)->get()->num_rows();
            $following = $this->db->select('id')->from('follow_user')->where('user_id', $medical_id)->get()->num_rows();
            $is_follow = $this->db->select('id')->from('follow_user')->where('parent_id', $medical_id)->get()->num_rows();

            if ($is_follow > 0) {
                $is_follow = 'Yes';
            } else {
                $is_follow = 'No';
            }

            $profile_view = $this->db->select('id')->from('pharmacy_view')->where('listing_id', $medical_id)->get()->num_rows();


            $activity_id = '0';

            $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM medical_stores_review WHERE medical_stores_id='$medical_id'");
            $row_pharma = $query_pharmacy->row_array();
            $rating = $row_pharma['avg_rating'];
            if ($rating === NULL) {
                $rating = '0';
            }

            $review = $this->db->select('id')->from('medical_stores_review')->where('medical_stores_id', $medical_id)->get()->num_rows();

            $chat_id = $row['user_id'];
            $chat_display = $row['medical_name'];
            $is_chat = 'Yes';

            $meter = '0';
            $distance = $km * 1000;
            $store_distance = round($distance, 2);
            if ($distance > 999) {
                $distance = $distance / 1000;
                $meter = round($distance, 2) . ' km';
            } else {
                $meter = round($distance) . ' meters away';
            }

            $reach_area = str_replace(" Mtr", "", $reach_area);
            $reach_area = str_replace(" Km", "", $reach_area);
            if ($reach_area > 10) {
                $ranges = ($reach_area / 1000);
            } else {
                $ranges = $reach_area;
            }

            $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));

            $Monday = $this->check_day_status('Monday', $days_closed, $is_24hrs_available, $store_open, $store_close);
            $Tuesday = $this->check_day_status('Tuesday', $days_closed, $is_24hrs_available, $store_open, $store_close);
            $Wednesday = $this->check_day_status('Wednesday', $days_closed, $is_24hrs_available, $store_open, $store_close);
            $Thursday = $this->check_day_status('Thursday', $days_closed, $is_24hrs_available, $store_open, $store_close);
            $Friday = $this->check_day_status('Friday', $days_closed, $is_24hrs_available, $store_open, $store_close);
            $Saturday = $this->check_day_status('Saturday', $days_closed, $is_24hrs_available, $store_open, $store_close);
            $Sunday = $this->check_day_status('Sunday', $days_closed, $is_24hrs_available, $store_open, $store_close);


            $opening_hours = "Monday>$Monday,Tuesday>$Tuesday,Wednesday>$Wednesday,Thursday>$Thursday,Friday>$Friday,Saturday>$Saturday,Sunday>$Sunday";

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
            $day_array_list = explode(',', $opening_hours);
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
                                $time = str_replace('close-close', 'close', $time_check);
                                if ($time == '12:00 AM-11:59 PM') {
                                    $time = '24 hrs open';
                                }
                                $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                $current_time = date('h:i A');


                                $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                $date3 = DateTime::createFromFormat('H:i a', $system_end_time);

                                if ($date2 < $date3 && $date1 <= $date3) {
                                    $date3->modify('+1 day')->format('H:i a');
                                } elseif ($date2 > $date3 && $date1 >= $date3) {
                                    $date3->modify('+1 day')->format('H:i a');
                                }


                                if ($date1 > $date2 && $date1 < $date3) {
                                    $open_close = 'open';
                                } else {
                                    $open_close = 'closed';
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

            $current_delivery_charges = $this->check_current_delivery_charges($is_24hrs_available, $day_night_delivery, $free_start_time, $free_end_time, $night_delivery_charge, $days_closed, $is_min_order_delivery, $min_order);

            $product_category_list = array();
            $query_category = $this->db->query("SELECT id,category,image FROM `category` WHERE id<>'3' order by id asc");
            foreach ($query_category->result_array() as $row) {
                $product_id = $row['id'];
                $product_category = $row['category'];
                $product_image = $row['image'];
                $product_image = str_replace(" ", "", $product_image);
                $product_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/product_category/' . $product_image;

                $product_category_list[] = array(
                    "id" => $product_id,
                    "category" => $product_category,
                    'image' => $product_image);
            }



            $resultpost[] = array(
                'id' => $medical_id,
                'medical_name' => $medical_name,
                'listing_id' => $chat_id,
                'listing_type' => '13',
                'latitude' => $lat,
                'longitude' => $lng,
                'store_manager' => $store_manager,
                'address1' => $address1,
                'address2' => $address2,
                'pincode' => $pincode,
                'city' => $city,
                'state' => $state,
                'contact_no' => $contact_no,
                'whatsapp_no' => $whatsapp_no,
                'email' => $email,
                'store_since' => $store_since,
                'website' => $website,
                'reach_area' => $ranges,
                'store_distance' => $store_distance,
                'distance' => $distances,
                'is_24hrs_available' => $is_24hrs_available,
                'store_open' => $store_open,
                'store_close' => $store_close,
                'day_night_delivery' => $day_night_delivery,
                'free_start_time' => $free_start_time,
                'free_end_time' => $free_end_time,
                'is_free_delivery' => $is_free_delivery,
                'days_closed' => $days_closed,
                'min_order' => $min_order,
                'is_min_order_delivery' => $is_min_order_delivery,
                'min_order_delivery_charge' => $min_order_delivery_charge,
                'night_delivery_charge' => $night_delivery_charge,
                'opening_day' => $final_Day,
                'current_delivery_charges' => $current_delivery_charges,
                'payment_type' => $payment_type,
                'online_offline' => $online_offline,
                'profile_pic' => $profile_pic,
                'rating' => (string) $rating,
                'followers' => $followers,
                'following' => $following,
                'profile_view' => $profile_view,
                'activity_id' => $activity_id,
                'is_follow' => $is_follow,
                'chat_id' => $chat_id,
                'chat_display' => $chat_display,
                'is_chat' => $is_chat,
                'review' => $review,
                'discount' => $discount,
                'medicalwale_discount' => $medicalwale_discount,
                'category_list' => $product_category_list
            );
        }

        function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
            $sort_col = array();
            foreach ($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }
            array_multisort($sort_col, $dir, $arr);
        }

        array_sort_by_column($resultpost, 'distance');
        return $resultpost;
    }

    // 	get_sponsored_ads
    public function get_sponsored_ads($user_id, $notIds) {
        $data = array();
        $today = date('Y-m-d');
        // $today = $datetime->format('Y-m-d');
        // print_r($today); die();
        $res = $this->db->query("SELECT sa.*, vt.`vendor_name` FROM `sponsored_advertisements` as sa LEFT JOIN `vendor_type` as vt ON (sa.listing_type = vt.id) WHERE `status` = 1 AND `expiry` >= '$today' ORDER BY RAND()")->result_array();


        foreach ($res as $r) {
            $row['ad_id'] = $r['ad_id'];
            $row['ad_for'] = $r['ad_for'];
            $row['id'] = $r['id'];
            $row['ad_image'] = $r['ad_image'];
            $row['listing_type_id'] = $r['listing_type'];
            $row['listing_type'] = $r['vendor_name'];

            foreach ($row as $key => $value) {
                if ($value == null) {
                    $row[$key] = "";
                }
            }
            $data[] = $row;
        }
        return $data;
    }

}
