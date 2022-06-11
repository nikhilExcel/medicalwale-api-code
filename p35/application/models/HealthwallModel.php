<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class HealthwallModel extends CI_Model {

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
           $agent === 'android' ? 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E' : 'Authorization: key=AIzaSyC0w9oft45PiV9jCYJFf3KnVZbcy1UO9Vc'
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

    /* function base64Detect($str) {
      if ($str) {
      $check = str_split(base64_decode($str));
      $x = 0;
      foreach ($check as $char) if (ord($char) > 126) $x++;
      if ($x/count($check)*100 < 30) return base64_decode($str);
      }

      return $str;
      } */

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
            return intval($time_differnce / $years) . ' yrs';
        } elseif (intval($time_differnce / $years) > 0) {
            return intval($time_differnce / $years) . ' yr';
        } elseif (intval($time_differnce / $months) > 1) {
            return intval($time_differnce / $months) . ' months';
        } elseif (intval(($time_differnce / $months)) > 0) {
            return intval(($time_differnce / $months)) . ' month';
        } elseif (intval(($time_differnce / $days)) > 1) {
            return intval(($time_differnce / $days)) . ' days';
        } elseif (intval(($time_differnce / $days)) > 0) {
            return intval(($time_differnce / $days)) . ' day';
        } elseif (intval(($time_differnce / $hours)) > 1) {
            return intval(($time_differnce / $hours)) . ' hrs';
        } elseif (intval(($time_differnce / $hours)) > 0) {
            return intval(($time_differnce / $hours)) . ' hr';
        } elseif (intval(($time_differnce / $minutes)) > 1) {
            return intval(($time_differnce / $minutes)) . ' min';
        } elseif (intval(($time_differnce / $minutes)) > 0) {
            return intval(($time_differnce / $minutes)) . ' min';
        } elseif (intval(($time_differnce)) > 1) {
            return intval(($time_differnce)) . ' sec';
        } else {
            return 'few seconds';
        }
    }

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

    public function healthwall_category() {
        //  $query = $this->db->query("SELECT `id`, `category`, `image` FROM `healthwall_category` order by sort_by asc");

        $query = $this->db->query("SELECT  `id`, `category`, `image` from healthwall_category order by id=31 asc, CASE WHEN LEFT(category, 1) LIKE '[a-Z]' THEN 1 ELSE 2 END ,LEFT(category, 1)");

        foreach ($query->result_array() as $row) {
            $id = $row['id'];
            $category = $row['category'];
            $img_file = $row['image'];

            if ($img_file != '') {
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/category/' . $img_file;
            } else {
                $image = '';
            }

            $resultpost[] = array(
                "id" => $id,
                "category" => $category,
                "image" => $image
            );
        }
        return $resultpost;
    }

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

    public function add_post($user_id, $tag, $category, $post, $type, $is_anonymous, $caption, $article_title, $article_image, $article_domain_name) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $post = array(
            'description' => $post,
            'type' => $type,
            'tag' => $tag,
            'category' => $category,
            'is_anonymous' => $is_anonymous,
            'user_id' => $user_id,
            'article_title' => $article_title,
            'article_image' => $article_image,
            'article_domain_name' => $article_domain_name,
            'active' => '1',
            'created_at' => $created_at,
            'updated_at' => $created_at
        );
        $this->db->insert('posts', $post);
        $post_id = $this->db->insert_id();
        return $post_id;
    }

    public function post_list($user_id, $activity_user_id, $healthwall_category, $page, $type) {

        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }

        $start = ($page - 1) * $limit;
        if ($activity_user_id > 0) {
            
            
            $query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,posts.type as post_type,posts.caption,posts.description as post,IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time,posts.is_anonymous,IFNULL(posts.post_location,'') AS post_location,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.is_anonymous<>'1' and posts.user_id='$activity_user_id' AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1'   order by posts.id DESC limit $start, $limit");
            
            
            
            $count_query = $this->db->query("select posts.id as post_id from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.is_anonymous<>'1' and posts.user_id='$activity_user_id' AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC ");
            $count_post = $count_query->num_rows();
        } else {
            if ($healthwall_category == '0') {
                if ($type == 'question') {
                    
                   // echo "SELECT category as cat_doc_list FROM doctor_list WHERE user_id='$user_id'";
                    
                  $doc_list =  $this->db->query("SELECT category as cat_doc_list FROM doctor_list WHERE user_id='$user_id'");
                  $doc_list_categroy = $doc_list->row();
                   $doc_list_categroy->cat_doc_list;
                
                //   print_r($doc_list_categroy);
                   $doct_list_id = $doc_list_categroy->cat_doc_list;
                   $doctor_id_array = explode(',',$doct_list_id);
                   $doc_id_list = "";
             foreach($doctor_id_array as $d_id){
                 $doc_id_list .= " FIND_IN_SET('$d_id',id) or ";
             }
             $doc_id_list = substr($doc_id_list, 0, -3);
             
                  // echo $doct_list_id;
               //    posts.healthwall_category IN ($healthwall_category)
                   $business_category = $this->db->query("SELECT doctors_type_id as b_id FROM business_category WHERE business_category.id<>'' AND $doc_id_list");
                  //echo "SELECT doctors_type_id as b_id FROM business_category WHERE business_category.id<>'' AND $doc_id_list";
                  
                $business_category_list = $business_category->result_array();
                  //  print_r($business_category_list);
                    $b_id = '';
                   foreach($business_category_list as $bid)
                   {
                        $b_id .=','.$bid['b_id'];
                   }
                 //  echo 'fgdsdfs';
               $b_id = substr($b_id, 1);     
               $tb = explode(',',$b_id);
               
               $tb_id_list = "";
             foreach($tb as $tb_id){
                 $tb_id_list .= " FIND_IN_SET('$tb_id',id) or ";
             }
             $tb_id_list = substr($tb_id_list, 0, -3);
               
               
            //   print_r($tb);
            //   $exlp_b_id = array_unshift($tb);
            //   print_r($exlp_b_id);
                 $doctor_type_id = $this->db->query("SELECT healthwall_category as h_id FROM doctor_type WHERE id<>'' AND $tb_id_list");
                 //  echo "SELECT healthwall_category as h_id FROM doctor_type WHERE id='$b_id'";
                 
                // echo "SELECT healthwall_category as h_id FROM doctor_type WHERE id<>'' AND $tb_id_list";
                 
                    $doctor_type_list = $doctor_type_id->result_array();
                    
                    $tb1_id_list = "";
                    foreach($doctor_type_list as $tb1_id){
                        $vl = $tb1_id['h_id'];
                        $tb1_id_list .= " FIND_IN_SET('$vl',posts.healthwall_category) or ";
                    }
                      $tb1_id_list = substr($tb1_id_list, 0, -3);
                 //$h_id =  $doctor_type_list->h_id;
                  
                  
                  
                 //  echo $h_id; 
                    
                    
                    $query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time,posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND ($tb1_id_list) AND posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' and posts.type='$type' order by posts.id DESC limit $start, $limit");
                    
                    // echo "select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND ($tb1_id_list) AND posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' and posts.type='$type' order by posts.id DESC limit $start, $limit";
                    
                     
                    $count_query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time,posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND ($tb1_id_list) AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' and posts.type='$type' order by posts.id DESC");
                    $count_post = $count_query->num_rows();
                } else {
                    $query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time,posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC limit $start, $limit");
                    $count_query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time,posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC");
                    $count_post = $count_query->num_rows();
                }
            } else {
                $query = $this->db->query("select healthwall_category.category AS healthwall_category, posts.id as post_id,posts.type as post_type,healthwall_category.id as healthwall_category_id,IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id INNER JOIN doctor_type on doctor_type.healthwall_category=posts.healthwall_category where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  doctor_type.healthwall_category IN ($healthwall_category) AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC limit $start, $limit");
                $count_query = $this->db->query("select healthwall_category.category AS healthwall_category, posts.id as post_id,posts.type as post_type,healthwall_category.id as healthwall_category_id,IFNULL(posts.repost_user_id,'') AS repost_user_id,posts.repost_location,posts.is_repost,posts.repost_time,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id INNER JOIN doctor_type on doctor_type.healthwall_category=posts.healthwall_category where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  doctor_type.healthwall_category IN ($healthwall_category) AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC");
                $count_post = $count_query->num_rows();
            }
        }
 
        if ($count_post > 0) {
            $resultpost = array();
             //question answer count display count for unanswered question by zak 
                //start
                
               $answered_count = 0;
               $unanswered_count = 0;    
                //end 
            foreach ($query->result_array() as $row) {
                $post_id = $row['post_id'];
                $listing_type = $row['vendor_id'];
                $post = $row['post'];
                $post_location = $row['post_location'];
                $healthwall_category_id = $row['healthwall_category_id'];
                $post = preg_replace('~[\r\n]+~', '', $post);
               if($post!=''){
                if ($post_id >= '2938') {
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
                $post_type = $row['post_type'];
                if($post_type==''){
                    $post_type='story';
                }
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
                    $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/' . $media_type . '/' . $source;
                    if ($media_type == 'video') {
                        $thumb = 'https://d1g6k76aztls2p.cloudfront.net/healthwall_media/video/' . substr_replace($source, '_thumbnail.jpeg', strrpos($source, '.') + 0);
                    } else {
                        $thumb = '';
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
               
                $query_comment = $this->db->query("SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' order by comments.id desc LIMIT 0,3");

                $comments = array();
              
              //   echo "SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' order by comments.id desc LIMIT 0,3";
              
                $query_comment_answer = $this->db->query("SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' AND comments.user_id='$user_id' order by comments.id desc LIMIT 1");
                $query_comment_count  = $query_comment_answer->num_rows();
                if($query_comment_count>0)
                {
                    $answered_count = $answered_count+1;
                 
                }
                else
                {
                    $unanswered_count = $unanswered_count+1;
                }
                
                
                $comment_counts = $query_comment->num_rows();
                if ($comment_counts > 0) {

                    foreach ($query_comment->result_array() as $rows) {
                        $comment_id = $rows['id'];
                        $comment_post_id = $rows['post_id'];
                        $comment = $rows['comment'];
                        $comment = preg_replace('~[\r\n]+~', '', $comment);
                        if ($comment_id > '5569') {
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
                        
                        
                    //     //added by zak for answer question count 
                    //     if($user_id == $comment_post_user_id)
                    //     {
                    //       //  echo 'answer user'.$user_id;
                    //      //   echo 'answer comment'.$comment_post_user_id;
                            
                    //         $answered_count = $answered_count+1;
                    //   //     echo 'answer count'.$answered_count;
                    //     }else
                    //     {
                    //       //   echo 'unanswer user'.$user_id;
                    //      //   echo 'unanswer comment'.$comment_post_user_id;
                    //         $unanswered_count = $unanswered_count+1;
                    //      //   echo 'unanswer count'.$unanswered_count;
                            
                    //     }
                        
                        //end 

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
                        $listing_type = $get_type['vendor_id'];

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
                            'comment_date' => $comment_date
                        );
                    }
                } else {
                    $comments = array();
                }
                
                
                
                 $array_vlcc=array();
                
                 $query_is_vlcc = $this->db->query("SELECT * FROM `posts` inner join skin_hair_expert_question on posts.id=skin_hair_expert_question.post_id   where posts.id='$post_id'  and posts.user_id='$user_id'limit 1");
                $repost_vlcc__counts = $query_is_vlcc->num_rows();
                
                $query_is_vlcc_details = $this->db->query("SELECT * FROM `expert_fields_details`  where post_id='$post_id' ");
                $repost_vlcc__counts_details = $query_is_vlcc_details->num_rows();
               
           
              
                $newarray=$query_is_vlcc_details->result_array();
 
            $last_names = array_column($newarray,'details','variables');
           
  
           
                if ($repost_vlcc__counts > 0) {
                    
                    $row_vlcc=$query_is_vlcc->row_array();
                    
                    	$array_vlcc[]=array("user_id" =>$row_vlcc['user_id'],
                    	"post_id" => $post_id,
                    	"for_user_id"=> "",
                    	"user_name" => $row_vlcc['user_name'],
                    	"question" => $row_vlcc['description'],
                    	"details"=>$last_names
                    	);
                    
                  
                    
                } else {
                   $array_vlcc = array();
                }
                

                //comments
                
                $resultpost[] = array(
                    'id' => $post_id,
                    'post_user_id' => $post_user_id,
                    'listing_type' => $listing_type,
                    'post_location' => $post_location,
                    'healthwall_category' => $healthwall_category_name,
                    'healthwall_category_id' => $healthwall_category_id,
                    'username' => $username,
                    'userimage' => $userimage,
                    'post_type' =>  trim($post_type),
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
                     'vlcc'=>$array_vlcc
                );
            }
            $resultpost = array(
                "status" => 200,
                "message" => "success",
                "count" => $count_post,
                'answered_count' => $answered_count,
                'unanswered_count' => $unanswered_count,
                "data" => $resultpost
            );
        } else {
            $resultpost = array(
                "status" => 200,
                "message" => "success",
                "count" => 0,
                "data" => array()
            );
        }

        return $resultpost;
    }
    
    
    public function post_list_v2($user_id, $activity_user_id, $healthwall_category, $page, $type) {

        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }

        $start = ($page - 1) * $limit;
        if ($activity_user_id > 0) {
            
            
            $query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,posts.type as post_type,posts.caption,posts.description as post,posts.is_anonymous,IFNULL(posts.post_location,'') AS post_location,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.is_anonymous<>'1' and posts.user_id='$activity_user_id' AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1'   order by posts.id DESC limit $start, $limit");
            
            
            
            $count_query = $this->db->query("select posts.id as post_id from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.is_anonymous<>'1' and posts.user_id='$activity_user_id' AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC ");
            $count_post = $count_query->num_rows();
        } else {
            if ($healthwall_category == '0') {
                if ($type == 'question') 
                {
                    
                   // echo "SELECT category as cat_doc_list FROM doctor_list WHERE user_id='$user_id'";
                    
                  $doc_list =  $this->db->query("SELECT category as cat_doc_list FROM doctor_list WHERE user_id='$user_id'");
                  $doc_list_categroy = $doc_list->row();
                   $doc_list_categroy->cat_doc_list;
                
                //   print_r($doc_list_categroy);
                   $doct_list_id = $doc_list_categroy->cat_doc_list;
                   $doctor_id_array = explode(',',$doct_list_id);
                   $doc_id_list = "";
             foreach($doctor_id_array as $d_id){
                 $doc_id_list .= " FIND_IN_SET('$d_id',id) or ";
             }
             $doc_id_list = substr($doc_id_list, 0, -3);
             
                  // echo $doct_list_id;
               //    posts.healthwall_category IN ($healthwall_category)
                   $business_category = $this->db->query("SELECT doctors_type_id as b_id FROM business_category WHERE business_category.id<>'' AND $doc_id_list");
                  //echo "SELECT doctors_type_id as b_id FROM business_category WHERE business_category.id<>'' AND $doc_id_list";
                  
                $business_category_list = $business_category->result_array();
                  //  print_r($business_category_list);
                    $b_id = '';
                   foreach($business_category_list as $bid)
                   {
                        $b_id .=','.$bid['b_id'];
                   }
                 //  echo 'fgdsdfs';
               $b_id = substr($b_id, 1);     
               $tb = explode(',',$b_id);
               
               $tb_id_list = "";
             foreach($tb as $tb_id){
                 $tb_id_list .= " FIND_IN_SET('$tb_id',id) or ";
             }
             $tb_id_list = substr($tb_id_list, 0, -3);
               
               
            //   print_r($tb);
            //   $exlp_b_id = array_unshift($tb);
            //   print_r($exlp_b_id);
                 $doctor_type_id = $this->db->query("SELECT healthwall_category as h_id FROM doctor_type WHERE id<>'' AND $tb_id_list");
                 //  echo "SELECT healthwall_category as h_id FROM doctor_type WHERE id='$b_id'";
                 
                // echo "SELECT healthwall_category as h_id FROM doctor_type WHERE id<>'' AND $tb_id_list";
                 
                    $doctor_type_list = $doctor_type_id->result_array();
                    
                    $tb1_id_list = "";
                    foreach($doctor_type_list as $tb1_id){
                        $vl = $tb1_id['h_id'];
                        $tb1_id_list .= " FIND_IN_SET('$vl',posts.healthwall_category) or ";
                    }
                      $tb1_id_list = substr($tb1_id_list, 0, -3);
                 //$h_id =  $doctor_type_list->h_id;
                  
                  
                  
                 //  echo $h_id; 
                    
                    
                    $query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND ($tb1_id_list) AND posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' and posts.type='$type' order by posts.id DESC limit $start, $limit");
                    
                    // echo "select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND ($tb1_id_list) AND posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' and posts.type='$type' order by posts.id DESC limit $start, $limit";
                    
                     
                    $count_query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND ($tb1_id_list) AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' and posts.type='$type' order by posts.id DESC");
                    $count_post = $count_query->num_rows();} 
                else 
                {
                    
                    
                    $query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' and posts.type!='question' order by posts.id DESC limit $start, $limit");
                    $count_query = $this->db->query("select healthwall_category.category AS healthwall_category,healthwall_category.id as healthwall_category_id, posts.id as post_id,posts.type as post_type,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' and posts.type!='question' order by posts.id DESC");
                    $count_post = $count_query->num_rows();
                }
            } else {
                $query = $this->db->query("select healthwall_category.category AS healthwall_category, posts.id as post_id,posts.type as post_type,healthwall_category.id as healthwall_category_id,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id INNER JOIN doctor_type on doctor_type.healthwall_category=posts.healthwall_category where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  doctor_type.healthwall_category IN ($healthwall_category) AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC limit $start, $limit");
                $count_query = $this->db->query("select healthwall_category.category AS healthwall_category, posts.id as post_id,posts.type as post_type,healthwall_category.id as healthwall_category_id,IFNULL(posts.post_location,'') AS post_location,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id ,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url,IFNULL(posts.category,'') AS post_category from posts INNER JOIN users on users.id=posts.user_id LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id INNER JOIN doctor_type on doctor_type.healthwall_category=posts.healthwall_category where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND  doctor_type.healthwall_category IN ($healthwall_category) AND  posts.id NOT IN (SELECT post_id FROM posts_hide WHERE post_id=posts.id AND user_id='$user_id') and posts.active='1' order by posts.id DESC");
                $count_post = $count_query->num_rows();
            }
        }
 
        if ($count_post > 0) {
            $resultpost = array();
             //question answer count display count for unanswered question by zak 
                //start
                
               $answered_count = 0;
               $unanswered_count = 0;    
                //end 
            foreach ($query->result_array() as $row) {
                $post_id = $row['post_id'];
                $listing_type = $row['vendor_id'];
                $post = $row['post'];
                $post_location = $row['post_location'];
                $healthwall_category_id = $row['healthwall_category_id'];
                $post = preg_replace('~[\r\n]+~', '', $post);
               if($post!=''){
                if ($post_id >= '2938') {
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
                $post_type = $row['post_type'];
                if($post_type==''){
                    $post_type='story';
                }
                $date = $row['created_at'];
                $caption = $row['caption'];
                $username = $row['name'];
                $post_user_id = $row['post_user_id'];
                $article_title = $row['article_title'];
                $article_image = $row['article_image'];
                $article_domain_name = $row['article_domain_name'];
                $article_url = $row['article_url'];
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
                    $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/' . $media_type . '/' . $source;
                    if ($media_type == 'video') {
                        $thumb = 'https://d1g6k76aztls2p.cloudfront.net/healthwall_media/video/' . substr_replace($source, '_thumbnail.jpeg', strrpos($source, '.') + 0);
                    } else {
                        $thumb = '';
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
              
              //   echo "SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' order by comments.id desc LIMIT 0,3";
              
                $query_comment_answer = $this->db->query("SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' AND comments.user_id='$user_id' order by comments.id desc LIMIT 1");
                $query_comment_count  = $query_comment_answer->num_rows();
                if($query_comment_count>0)
                {
                    $answered_count = $answered_count+1;
                 
                }
                else
                {
                    $unanswered_count = $unanswered_count+1;
                }
                
                
                $comment_counts = $query_comment->num_rows();
                if ($comment_counts > 0) {

                    foreach ($query_comment->result_array() as $rows) {
                        $comment_id = $rows['id'];
                        $comment_post_id = $rows['post_id'];
                        $comment = $rows['comment'];
                        $comment = preg_replace('~[\r\n]+~', '', $comment);
                        if ($comment_id > '5569') {
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
                        
                        
                    //     //added by zak for answer question count 
                    //     if($user_id == $comment_post_user_id)
                    //     {
                    //       //  echo 'answer user'.$user_id;
                    //      //   echo 'answer comment'.$comment_post_user_id;
                            
                    //         $answered_count = $answered_count+1;
                    //   //     echo 'answer count'.$answered_count;
                    //     }else
                    //     {
                    //       //   echo 'unanswer user'.$user_id;
                    //      //   echo 'unanswer comment'.$comment_post_user_id;
                    //         $unanswered_count = $unanswered_count+1;
                    //      //   echo 'unanswer count'.$unanswered_count;
                            
                    //     }
                        
                        //end 

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
                        $listing_type = $get_type['vendor_id'];

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
                            'comment_date' => $comment_date
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
                    'comments' => $comments
                );
            }
            $resultpost = array(
                "status" => 200,
                "message" => "success",
                "count" => $count_post,
                'answered_count' => $answered_count,
                'unanswered_count' => $unanswered_count,
                "data" => $resultpost
            );
        } else {
            $resultpost = array(
                "status" => 200,
                "message" => "success",
                "count" => 0,
                "data" => array()
            );
        }

        return $resultpost;
    }

    public function single_post_list($user_id, $post_id) {

//echo "select  IFNULL(posts.post_location,'') AS post_location,healthwall_category.category AS healthwall_category, posts.id as post_id,posts.type as post_type,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url from posts INNER JOIN users on users.id=posts.user_id  LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND posts.id='$post_id' order by posts.id DESC ";
        $query = $this->db->query("select  IFNULL(posts.post_location,'') AS post_location,healthwall_category.category AS healthwall_category, posts.id as post_id,posts.type as post_type,posts.caption,posts.description as post,posts.is_anonymous,posts.tag,posts.created_at,users.name,posts.user_id as post_user_id,users.vendor_id,posts.article_title,posts.article_image,posts.article_domain_name,posts.article_url from posts INNER JOIN users on users.id=posts.user_id  LEFT JOIN healthwall_category ON posts.healthwall_category=healthwall_category.id where posts.user_id<>'' and posts.user_id<>'0' AND posts.healthwall_category<>'0' AND posts.id='$post_id' order by posts.id DESC ");
        $count_post = $query->num_rows();
        $result_post = array();

        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $post_id = $row['post_id'];
                $listing_type = $row['vendor_id'];
                $post = $row['post'];

                $post = preg_replace('~[\r\n]+~', '', $post);
                if ($post_id >= '2721') {
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
                    $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/' . $media_type . '/' . $source;
                    if ($media_type == 'video') {
                        $thumb = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_media/thumb/' . substr_replace($source, 'jpg', strrpos($source, '.') + 1);
                    } else {
                        $thumb = '';
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
                        if ($comment_id > '5569') {
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
                        $listing_type_comment = $get_type['vendor_id'];


                        $comments[] = array(
                            'id' => $comment_id,
                            'listing_type' => $listing_type_comment,
                            'comment_user_id' => $comment_post_user_id,
                            'username' => $comment_username,
                            'userimage' => $comment_userimage,
                            'like_count' => $comment_like_count,
                            'like_yes_no' => $comment_like_yes_no,
                            'post_id' => $comment_post_id,
                            'comment' => $comment,
                            'comment_date' => $comment_date
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
                    'comments' => $comments
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

    public function post_like($user_id, $post_id, $post_user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from post_likes where post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

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


            $customer_token = $this->db->query("SELECT token,agent, token_status FROM users WHERE id='$post_user_id' AND id<>'$user_id'");

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
                $title = $usr_name . ' Beats on your Post';
                $msg = $usr_name . ' Beats on your post click here to view post.';
                
                
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
                $this->db->insert('myactivity_notification', $notification_array);
                
                
                
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
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

    public function post_video_views($user_id, $media_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $post_video_views_array = array(
            'user_id' => $user_id,
            'media_id' => $media_id
        );
        $this->db->insert('post_video_views', $post_video_views_array);

        $video_views = $this->db->select('id')->from('post_video_views')->where('media_id', $media_id)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'video_views' => $video_views
        );
    }

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
                $title = $usr_name . ' commented on post that you have followed';
                $msg = $usr_name . ' commented on post that you have followed click here to view post.';
                
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
                $this->db->insert('myactivity_notification', $notification_array);
                
                
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
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
            $title = $usr_name . ' Commented on your Post ';
            $msg = $usr_name . ' Commented on your click here to view post.';
            
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
                $this->db->insert('myactivity_notification', $notification_array);
            
            
            $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
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
            $this->db->insert('myactivity_notification', $notification_array);
            //end


          /*  if ($agent == 'ios') {
                $this->send_gcm_notify_ios($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id, $comment_id);
            } else {*/
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
           // }
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
            $agent === 'android' ? 'Authorization: key=AIzaSyAPAKcKLABcHyRhiaf9LO_i2xcosgw3Y3E' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
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
                $msg = $usr_name . ' Beats on your comment click here to view post.';
                
                
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
                $this->db->insert('myactivity_notification', $notification_array);
                
                
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
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

    public function comment_list($user_id, $post_id) {

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

        $comment_count = $this->db->select('id')->from('comments')->where('post_id', $post_id)->get()->num_rows();

        if ($comment_count > 0) {

            $query = $this->db->query("SELECT comments.id,comments.post_id,comments.description as comment,comments.updated_at as date,users.name,comments.user_id as post_user_id FROM comments INNER JOIN users on users.id=comments.user_id WHERE comments.post_id='$post_id' order by comments.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
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


                $date = get_time_difference_php($date);
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
                    'comment_date' => $comment_date
                );
                //print_r($resultpost);
            }
            //die();
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

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

    public function post_delete($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
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
            $post_location1 = $query->post_location;
            if($post_location1 == NULL)
            {
                $post_location="";
            }
            else
            {
               $post_location = $post_location1;
            }
            $location1 = $query->location;
            if($location1 == NULL)
            {
                $location="";
            }
            else
            {
               $location=$location1 ;
            }
            
            $description = $query->description;
            $article_url = $query->article_url;
            $article_title = $query->article_title;
            $article_desc = $query->article_desc;
            $article_image = $query->article_image;
            $article_domain_name = $query->article_domain_name;
            $caption = $query->caption;
            $tag = $query->tag;
            $category = $query->category;
           // $location = $query->location;
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
                    'listing_id' => $repost_user_id,
                    'booking_id' => "",
                    'invoice_no' => "",
                    'user_id' => $post_user_id,
                    'notification_type' => 'healthwall_notifications8',
                    'notification_date' => date('Y-m-d H:i:s')
                );
                $this->db->insert('myactivity_notification', $notification_array);
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
}
