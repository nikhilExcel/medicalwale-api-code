<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ExerciseModel extends CI_Model {

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

    public function exercise_subcategory($category_id) {
        $query = $this->db->query("SELECT `id`,`cat_id`, `name`, `image` FROM `exercise_subcategory` WHERE cat_id='$category_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $subcat_id = $row['id'];
                $image = $row['image'];
                if ($image != '') {
                    /* $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/exercise_images/icons/' . $image;*/
                    
                     $image = 'https://s3.amazonaws.com/medicalwale/images/exercise_images/icons/' . $image;
                } else {
                    $image = '';
                }
                $name = $row['name'];
                $resultpost[] = array(
                    "subcat_id" => $subcat_id,
                    "name" => $name,
                    "image" => $image
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    public function exercise_list($user_id, $cat_id, $subcat_id, $keyword, $page) {
        $limit = 10;
        $start = 0; 
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $ad_array='0,';
        $start = ($page - 1) * $limit;
        if($cat_id>0){
        if ($subcat_id == 0) {
            if ($keyword == '') {
                $query = $this->db->query("SELECT exercise_details.id, exercise_details.cat_id, exercise_details.subcat_id, exercise_details.video, exercise_details.video_hindi, exercise_details.video_thumbnail, exercise_details.title, exercise_details.details, exercise_details.date, exercise_category.name AS category_name,IfNull(exercise_subcategory.name, '') AS subcategory_name  FROM exercise_details LEFT JOIN exercise_category ON exercise_details.cat_id=exercise_category.id LEFT JOIN exercise_subcategory ON exercise_details.subcat_id=exercise_subcategory.id WHERE exercise_details.cat_id='$cat_id' order by exercise_details.id asc limit $start, $limit");
                $count_query = $this->db->query("SELECT exercise_details.id, exercise_details.cat_id, exercise_details.subcat_id, exercise_details.video, exercise_details.video_hindi, exercise_details.video_thumbnail, exercise_details.title, exercise_details.details, exercise_details.date, exercise_category.name AS category_name,IfNull(exercise_subcategory.name, '') AS subcategory_name  FROM exercise_details LEFT JOIN exercise_category ON exercise_details.cat_id=exercise_category.id LEFT JOIN exercise_subcategory ON exercise_details.subcat_id=exercise_subcategory.id WHERE exercise_details.cat_id='$cat_id' order by exercise_details.id asc");
            } else {
                $query = $this->db->query("SELECT exercise_details.id, exercise_details.cat_id, exercise_details.subcat_id, exercise_details.video, exercise_details.video_hindi, exercise_details.video_thumbnail, exercise_details.title, exercise_details.details, exercise_details.date, exercise_category.name AS category_name,IfNull(exercise_subcategory.name, '') AS subcategory_name  FROM exercise_details LEFT JOIN exercise_category ON exercise_details.cat_id=exercise_category.id LEFT JOIN exercise_subcategory ON exercise_details.subcat_id=exercise_subcategory.id WHERE exercise_details.cat_id='$cat_id' AND exercise_details.title like '%" . $keyword . "%' order by exercise_details.id asc limit $start, $limit");
                $count_query = $this->db->query("SELECT exercise_details.id, exercise_details.cat_id, exercise_details.subcat_id, exercise_details.video, exercise_details.video_hindi, exercise_details.video_thumbnail, exercise_details.title, exercise_details.details, exercise_details.date, exercise_category.name AS category_name,IfNull(exercise_subcategory.name, '') AS subcategory_name  FROM exercise_details LEFT JOIN exercise_category ON exercise_details.cat_id=exercise_category.id LEFT JOIN exercise_subcategory ON exercise_details.subcat_id=exercise_subcategory.id WHERE exercise_details.cat_id='$cat_id' AND exercise_details.title like '%" . $keyword . "%' order by exercise_details.id asc");
            }
        } else {
            if ($keyword == '') {
                $query = $this->db->query("SELECT exercise_details.id, exercise_details.cat_id, exercise_details.subcat_id, exercise_details.video, exercise_details.video_hindi, exercise_details.video_thumbnail, exercise_details.title, exercise_details.details, exercise_details.date, exercise_category.name AS category_name,IfNull(exercise_subcategory.name, '') AS subcategory_name  FROM exercise_details LEFT JOIN exercise_category ON exercise_details.cat_id=exercise_category.id LEFT JOIN exercise_subcategory ON exercise_details.subcat_id=exercise_subcategory.id WHERE exercise_details.cat_id='$cat_id' AND  exercise_details.subcat_id='$subcat_id' order by exercise_details.id asc limit $start, $limit");
                $count_query = $this->db->query("SELECT exercise_details.id, exercise_details.cat_id, exercise_details.subcat_id, exercise_details.video, exercise_details.video_hindi, exercise_details.video_thumbnail, exercise_details.title, exercise_details.details, exercise_details.date, exercise_category.name AS category_name,IfNull(exercise_subcategory.name, '') AS subcategory_name  FROM exercise_details LEFT JOIN exercise_category ON exercise_details.cat_id=exercise_category.id LEFT JOIN exercise_subcategory ON exercise_details.subcat_id=exercise_subcategory.id WHERE exercise_details.cat_id='$cat_id' AND  exercise_details.subcat_id='$subcat_id' order by exercise_details.id asc");
            } else {
                $query = $this->db->query("SELECT exercise_details.id, exercise_details.cat_id, exercise_details.subcat_id, exercise_details.video, exercise_details.video_hindi, exercise_details.video_thumbnail, exercise_details.title, exercise_details.details, exercise_details.date, exercise_category.name AS category_name,IfNull(exercise_subcategory.name, '') AS subcategory_name  FROM exercise_details LEFT JOIN exercise_category ON exercise_details.cat_id=exercise_category.id LEFT JOIN exercise_subcategory ON exercise_details.subcat_id=exercise_subcategory.id WHERE exercise_details.cat_id='$cat_id' AND  exercise_details.subcat_id='$subcat_id' AND exercise_details.title like '%" . $keyword . "%' order by exercise_details.id asc limit $start, $limit");
                $count_query = $this->db->query("SELECT exercise_details.id, exercise_details.cat_id, exercise_details.subcat_id, exercise_details.video, exercise_details.video_hindi, exercise_details.video_thumbnail, exercise_details.title, exercise_details.details, exercise_details.date, exercise_category.name AS category_name,IfNull(exercise_subcategory.name, '') AS subcategory_name  FROM exercise_details LEFT JOIN exercise_category ON exercise_details.cat_id=exercise_category.id LEFT JOIN exercise_subcategory ON exercise_details.subcat_id=exercise_subcategory.id WHERE exercise_details.cat_id='$cat_id' AND exercise_details.subcat_id='$subcat_id' AND exercise_details.title like '%" . $keyword . "%' order by exercise_details.id asc");
            }
        }
        }
        else{
            $query = $this->db->query("SELECT exercise_details.id, exercise_details.cat_id, exercise_details.subcat_id, exercise_details.video, exercise_details.video_hindi, exercise_details.video_thumbnail, exercise_details.title, exercise_details.details, exercise_details.date, exercise_category.name AS category_name,IfNull(exercise_subcategory.name, '') AS subcategory_name  FROM exercise_details LEFT JOIN exercise_category ON exercise_details.cat_id=exercise_category.id LEFT JOIN exercise_subcategory ON exercise_details.subcat_id=exercise_subcategory.id order by  RAND() limit $start, $limit");
            $count_query = $this->db->query("SELECT exercise_details.id, exercise_details.cat_id, exercise_details.subcat_id, exercise_details.video, exercise_details.video_hindi, exercise_details.video_thumbnail, exercise_details.title, exercise_details.details, exercise_details.date, exercise_category.name AS category_name,IfNull(exercise_subcategory.name, '') AS subcategory_name  FROM exercise_details LEFT JOIN exercise_category ON exercise_details.cat_id=exercise_category.id LEFT JOIN exercise_subcategory ON exercise_details.subcat_id=exercise_subcategory.id order by RAND()");
        }
        $count_post = $count_query->num_rows();
        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $cat_id = $row['cat_id'];
                $subcat_id = $row['subcat_id'];
                $video = $row['video'];
                $video_hindi = str_replace("null", "", $row['video_hindi']);
                $video_thumbnail = $row['video_thumbnail'];
                $title = $row['title'];
                $details = $row['details'];
                $details = str_replace('’', "'", $details);
                $details = "<style>strong{color:#404547;font-weight:400}p{color:#768188;}</style>" . $details;
                $date = $row['date'];
                $category_name = $row['category_name'];
                $subcategory_name = $row['subcategory_name'];
                if ($video != '') {
                    $video = 'https://d2ua8z9537m644.cloudfront.net/exercise_images/video/' . $video;
                } else {
                    $video = '';
                }
                if ($video_hindi != '') {
                    $video_hindi = 'https://d2ua8z9537m644.cloudfront.net/exercise_images/video/' . $video_hindi;
                } else {
                    $video_hindi = '';
                }
                if ($video_thumbnail != '') {
                    $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/exercise_images/image/' . $video_thumbnail;
                } else {
                    $video_thumbnail = '';
                }
                $exercise_views_query = $this->db->query("SELECT id from exercise_views WHERE post_id='$id'");
                $total_views = $exercise_views_query->num_rows();

                $like_query = $this->db->query("SELECT id from exercise_likes WHERE post_id='$id'");
                $total_likes = $like_query->num_rows();

                $like_yes_no = $this->db->select('id')->from('exercise_likes')->where('post_id', $id)->where('user_id', $user_id)->get()->num_rows();

                $exercise_comment_query = $this->db->query("SELECT id from exercise_comment WHERE post_id='$id'");
                $total_comment = $exercise_comment_query->num_rows();

$ad_post=array();
               
                    $query = $this->db->query("SELECT id,vendor_type,ad_main_cat,main_cat_id,ad_title,ad_description,ad_link,ad_image FROM sponsored_advertisements where NOT FIND_IN_SET(id, '$ad_array') ORDER BY rand() limit 1");
                    $count = $query->num_rows();
                    if ($count > 0) {
                        $row  = $query->row_array();
                        $ad_img      = $row['ad_image'];
                        $ad_main_cat = $row['ad_main_cat'];
                        
                        $ad_type = ($ad_main_cat == 1) ? "brand" : (($ad_main_cat == 2)  ? "product" : "category");
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/Sponsored_files/' . $ad_img;
                        
                        $ad_array.=$row['id'].',';
                        $ad_post[] = array(
                            'id' => $row['id'],
                            'ad_type'        => $ad_type,
                            'ad_cat_id'      => $row['main_cat_id'],
                            'ad_title'       => $row['ad_title'],
                            'ad_description' => $row['ad_description'],
                            'ad_link'  => $row['ad_link'],
                            'ad_image' => $image
                        );
                    }
                $resultpost[] = array(
                    'exercise_id' => $id,
                    'cat_id' => $cat_id,
                    'video' => $video,
                    'video_hindi' => $video_hindi,
                    'video_thumbnail' => $video_thumbnail,
                    'title' => $title,
                    'details' => $details,
                    'category_name' => $category_name,
                    'subcategory_name' => $subcategory_name,
                    'like_yes_no' => $like_yes_no,
                    'total_likes' => $total_likes,
                    'total_views' => $total_views,
                    'total_comment' => $total_comment,
                    'ad_list' => $ad_post
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
    
    
    public function exercise_details($user_id, $id) {
       
        $query = $this->db->query("SELECT exercise_details.id, exercise_details.cat_id, exercise_details.subcat_id, exercise_details.video, exercise_details.video_hindi, exercise_details.video_thumbnail, exercise_details.title, exercise_details.details, exercise_details.date, exercise_category.name AS category_name,IfNull(exercise_subcategory.name, '') AS subcategory_name  FROM exercise_details LEFT JOIN exercise_category ON exercise_details.cat_id=exercise_category.id LEFT JOIN exercise_subcategory ON exercise_details.subcat_id=exercise_subcategory.id WHERE exercise_details.id='$id' limit 1");
            $count_query = $this->db->query("SELECT exercise_details.id, exercise_details.cat_id, exercise_details.subcat_id, exercise_details.video, exercise_details.video_hindi, exercise_details.video_thumbnail, exercise_details.title, exercise_details.details, exercise_details.date, exercise_category.name AS category_name,IfNull(exercise_subcategory.name, '') AS subcategory_name  FROM exercise_details LEFT JOIN exercise_category ON exercise_details.cat_id=exercise_category.id LEFT JOIN exercise_subcategory ON exercise_details.subcat_id=exercise_subcategory.id WHERE exercise_details.id='$id' limit 1");
        $count_post = $count_query->num_rows();
        if ($count_post > 0) {
                $row = $query->row_array();
                $id = $row['id'];
                $cat_id = $row['cat_id'];
                $subcat_id = $row['subcat_id'];
                $video = $row['video'];
                $video_hindi = str_replace("null", "", $row['video_hindi']);
                $video_thumbnail = $row['video_thumbnail'];
                $title = $row['title'];
                $details = $row['details'];
                $details = str_replace('’', "'", $details);
                $details = "<style>strong{color:#404547;font-weight:400}p{color:#768188;}</style>" . $details;
                $date = $row['date'];
                $category_name = $row['category_name'];
                $subcategory_name = $row['subcategory_name'];
                if ($video != '') {
                    $video = 'https://d2ua8z9537m644.cloudfront.net/exercise_images/video/' . $video;
                } else {
                    $video = '';
                }
                if ($video_hindi != '') {
                    $video_hindi = 'https://d2ua8z9537m644.cloudfront.net/exercise_images/video/' . $video_hindi;
                } else {
                    $video_hindi = '';
                }
                if ($video_thumbnail != '') {
                    $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/exercise_images/image/' . $video_thumbnail;
                } else {
                    $video_thumbnail = '';
                }
                $exercise_views_query = $this->db->query("SELECT id from exercise_views WHERE post_id='$id'");
                $total_views = $exercise_views_query->num_rows();

                $like_query = $this->db->query("SELECT id from exercise_likes WHERE post_id='$id'");
                $total_likes = $like_query->num_rows();

                $like_yes_no = $this->db->select('id')->from('exercise_likes')->where('post_id', $id)->where('user_id', $user_id)->get()->num_rows();

                $exercise_comment_query = $this->db->query("SELECT id from exercise_comment WHERE post_id='$id'");
                $total_comment = $exercise_comment_query->num_rows();

                    $ad_post=array();
               
                    $query = $this->db->query("SELECT id,vendor_type,ad_main_cat,main_cat_id,ad_title,ad_description,ad_link,ad_image FROM sponsored_advertisements ORDER BY rand() limit 1");
                    $count = $query->num_rows();
                    if ($count > 0) {
                        $row  = $query->row_array();
                        $ad_img      = $row['ad_image'];
                        $ad_main_cat = $row['ad_main_cat'];
                        
                        $ad_type = ($ad_main_cat == 1) ? "brand" : (($ad_main_cat == 2)  ? "product" : "category");
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/Sponsored_files/' . $ad_img;
                        
                        //$ad_array.=$row['id'].',';
                        $ad_post[] = array(
                            'id' => $row['id'],
                            'ad_type'        => $ad_type,
                            'ad_cat_id'      => $row['main_cat_id'],
                            'ad_title'       => $row['ad_title'],
                            'ad_description' => $row['ad_description'],
                            'ad_link'  => $row['ad_link'],
                            'ad_image' => $image
                        );
                    }

                $result_post = array(
                "status" => 200,
                "message" => "success",
                "count" => $count_post,
               'exercise_id' => $id,
               'cat_id' => $cat_id,
               'video' => $video,
               'video_hindi' => $video_hindi,
               'video_thumbnail' => $video_thumbnail,
               'title' => $title,
               'details' => $details,
               'category_name' => $category_name,
               'subcategory_name' => $subcategory_name,
               'like_yes_no' => $like_yes_no,
               'total_likes' => $total_likes,
               'total_views' => $total_views,
               'total_comment' => $total_comment,
                    'ad_list' => $ad_post
        );
               
        } else {
             $result_post = array(
                "status" => 200,
                "message" => "success",
                "count" => 0
                );
        }
        return $result_post;
    }

    public function exercise_likes($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from exercise_likes WHERE post_id='$post_id' AND user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `exercise_likes` WHERE user_id='$user_id' AND post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from exercise_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $exercise_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('exercise_likes', $exercise_likes);
            $like_query = $this->db->query("SELECT id from exercise_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function exercise_comment($user_id, $post_id, $comment) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $exercise_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('exercise_comment', $exercise_comment);
        $exercise_comment_query = $this->db->query("SELECT id from exercise_comment WHERE post_id='$post_id'");
        $total_comment = $exercise_comment_query->num_rows();
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
        $count_query = $this->db->query("SELECT id from exercise_comment_like WHERE comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `exercise_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from exercise_comment_like WHERE comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $exercise_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('exercise_comment_like', $exercise_comment_like);
            $comment_query = $this->db->query("SELECT id from exercise_comment_like WHERE comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    public function exercise_comment_list($user_id, $post_id) {

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

        $review_list_count = $this->db->select('id')->from('exercise_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT exercise_comment.id,exercise_comment.post_id,exercise_comment.comment as comment,exercise_comment.date,users.name,exercise_comment.user_id as post_user_id FROM exercise_comment INNER JOIN users on users.id=exercise_comment.user_id WHERE exercise_comment.post_id='$post_id' order by exercise_comment.id asc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                $comment_decrypt = $this->decrypt($comment);
                $comment_encrypt = $this->encrypt($comment_decrypt);
                if ($comment_encrypt == $comment) {
                    $comment = $comment_decrypt;
                }
                $username = $row['name'];
                $date = $row['date'];
                $post_user_id = $row['post_user_id'];

                $like_count = $this->db->select('id')->from('exercise_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('exercise_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
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

    public function exercise_views($user_id, $post_id) {
		$total_query = $this->db->query("SELECT views FROM `exercise_details` where id='$post_id' limit 1");
        $get_list = $total_query->row_array();
        $views = $get_list['views'];
        $total_views = $views+1;
        $querys = $this->db->query("UPDATE `exercise_details` SET `views`='$total_views' WHERE id='$post_id'");

        return array(
            'status' => 200,
            'message' => 'success',
            'total_views' => $total_views
        );
    }

    public function exercise_upnext_list($user_id, $listing_id, $cat_id) {

        $query = $this->db->query("SELECT exercise_details.id, exercise_details.cat_id, exercise_details.subcat_id, exercise_details.video, exercise_details.video_hindi,  exercise_details.video_thumbnail, exercise_details.title, exercise_details.details, exercise_details.date, exercise_category.name AS category_name,IfNull(exercise_subcategory.name, '') AS subcategory_name  FROM exercise_details LEFT JOIN exercise_category ON exercise_details.cat_id=exercise_category.id LEFT JOIN exercise_subcategory ON exercise_details.subcat_id=exercise_subcategory.id WHERE exercise_details.cat_id='$cat_id' AND exercise_details.id<>'$listing_id' order by exercise_details.id desc LIMIT 0,10");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $cat_id = $row['cat_id'];
                $subcat_id = $row['subcat_id'];
                $video = $row['video'];
                $video_hindi = str_replace("null", "", $row['video_hindi']);
                $video_thumbnail = $row['video_thumbnail'];
                $title = $row['title'];
                $details = $row['details'];
                $details = str_replace('’', "'", $details);
                $details = "<style>strong{color:#404547;font-weight:400}p{color:#768188;}</style>" . $details;
                $date = $row['date'];
                $category_name = $row['category_name'];
                $subcategory_name = $row['subcategory_name'];

                if ($video != '') {
                    $video = 'https://d2c8oti4is0ms3.cloudfront.net/images/exercise_images/video/' . $video;
                } else {
                    $video = '';
                }

                if ($video_hindi != '') {
                    $video_hindi = 'https://d2c8oti4is0ms3.cloudfront.net/images/exercise_images/video/' . $video_hindi;
                } else {
                    $video_hindi = '';
                }

                if ($video_thumbnail != '') {
                    $video_thumbnail = 'https://d2c8oti4is0ms3.cloudfront.net/images/exercise_images/image/' . $video_thumbnail;
                } else {
                    $video_thumbnail = '';
                }

                $exercise_views_query = $this->db->query("SELECT id from exercise_views WHERE post_id='$id'");
                $total_views = $exercise_views_query->num_rows();

                $like_query = $this->db->query("SELECT id from exercise_likes WHERE post_id='$id'");
                $total_likes = $like_query->num_rows();

                $like_yes_no = $this->db->select('id')->from('exercise_likes')->where('post_id', $id)->where('user_id', $user_id)->get()->num_rows();

                $exercise_comment_query = $this->db->query("SELECT id from exercise_comment WHERE post_id='$id'");
                $total_comment = $exercise_comment_query->num_rows();

                $resultpost[] = array(
                    'exercise_id' => $id,
                    'cat_id' => $cat_id,
                    'video' => $video,
                    'video_hindi' => $video_hindi,
                    'video_thumbnail' => $video_thumbnail,
                    'title' => $title,
                    'details' => $details,
                    'category_name' => $category_name,
                    'subcategory_name' => $subcategory_name,
                    'like_yes_no' => $like_yes_no,
                    'total_likes' => $total_likes,
                    'total_views' => $total_views,
                    'total_comment' => $total_comment
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

}
