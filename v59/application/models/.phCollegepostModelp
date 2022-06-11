<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CollegepostModel extends CI_Model {

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
                "notification_type" => 'medicalcollege_notifications',
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

    public function college_post_list($user_id, $page, $college_id) {

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
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;

        $query = $this->db->query("select college_post.id ,college_post.type,college_post.created_at,college_post.post,college_post.college_id,college_post.article_title,college_post.article_image,college_post.article_domain_name,college_post.article_url,college_post.user_id as post_user_id,users.name FROM college_post INNER JOIN users on users.id=college_post.user_id  WHERE college_id='$college_id' AND college_post.id NOT IN (SELECT post_id FROM college_post_hide WHERE post_id=college_post.id AND user_id='$user_id') order by college_post.id desc limit $start, $limit");
        $count_query = $this->db->query("select college_post.id ,college_post.type,college_post.created_at,college_post.post,college_post.article_title,college_post.article_image,college_post.article_domain_name,college_post.article_url,college_post.user_id as post_user_id,users.name FROM college_post INNER JOIN users on users.id=college_post.user_id  WHERE college_post.id NOT IN (SELECT post_id FROM college_post_hide WHERE post_id=college_post.id AND user_id='$user_id') order by college_post.id desc");

        $count_post = $count_query->num_rows();
        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post_id = $row['id'];
                $username = $row['name'];
                $post_user_id = $row['post_user_id'];
                $post = $row['post'];

                if(!empty($post)){
                $post = preg_replace('~[\r\n]+~', '', $post);
                if ($post_id >= '27') {
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

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $type = $row['type'];
                $article_title = $row['article_title'];
                $article_image = $row['article_image'];
                $article_domain_name = $row['article_domain_name'];
                $article_url = $row['article_url'];
                $views = 0;
                $video_views = 0;

                $query_media = $this->db->query("SELECT college_post_media.id AS media_id,college_post_media.source,college_post_media.type AS media_type,IFNULL(college_post_media.caption,'') AS caption,college_post_media.img_width,college_post_media.img_height,college_post_media.video_width,college_post_media.video_height FROM college_post_media INNER JOIN college_post on college_post_media.post_id=college_post.id WHERE college_post_media.post_id='$id'");
                $img_val = '';
                $images = '';
                $img_comma = '';
                $img_width = '';
                $img_height = '';
                $video_width = '';
                $video_height = '';

                $media_array = array();

                foreach ($query_media->result_array() as $media_row) {
                    $media_id = $media_row['media_id'];
                    $media_type = $media_row['media_type'];
                    $source = $media_row['source'];
                    $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/medical_college_images/' . $media_type . '/' . $source;
                    if ($media_type == 'video') {
                        $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/medical_college_images/' . $media_type . '/' . $source;
                        $thumb = 'http://medicalwale-thumbnails.s3.amazonaws.com/videothumbnail/images/medical_college_images/video/' . substr_replace($source, '_thumbnail.jpeg', strrpos($source, '.') + 0);
                    } else {
                        $thumb = '';
                    }
                    $caption = $media_row['caption'];
                    $img_width = $media_row['img_width'];
                    $img_height = $media_row['img_height'];
                    $video_width = $media_row['video_width'];
                    $video_height = $media_row['video_height'];

                    $view_media_count = $this->db->select('id')->from('college_video_views')->where('media_id', $media_id)->get()->num_rows();

                    $view_media_yes_no_query = $this->db->query("SELECT id from college_video_views WHERE media_id='$media_id' and user_id='$user_id'");
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


                $date = $row['created_at'];
                $date = get_time_difference_php($date);
                $like_count = $this->db->select('id')->from('college_post_likes')->where('post_id', $id)->get()->num_rows();
                $comment_count = $this->db->select('id')->from('college_post_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('college_post_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                $view_count = $this->db->select('id')->from('college_post_views')->where('post_id', $id)->get()->num_rows();
                $view_yes_no_query = $this->db->query("SELECT id from college_post_views WHERE post_id='$id' and user_id='$user_id'");
                $view_post_yes_no = $view_yes_no_query->num_rows();

                $follow_count = $this->db->select('id')->from('college_follow_post')->where('post_id', $id)->get()->num_rows();

                $follow_yes_no_query = $this->db->query("SELECT id from college_follow_post where post_id='$id' and user_id='$user_id'");
                $follow_post_yes_no = $follow_yes_no_query->num_rows();
                $share_url = "https://play.google.com/store/apps/details?id=com.medicalwale.medicalwale";
                //comments

                $query_comment = $this->db->query("SELECT college_post_comment.id,college_post_comment.comment,college_post_comment.date,college_post_comment.user_id as post_user_id,users.name FROM college_post_comment INNER JOIN users on users.id=college_post_comment.user_id WHERE college_post_comment.post_id='$post_id' order by college_post_comment.id DESC LIMIT 0,3");
                $comment_counts = $query_comment->num_rows();
                if ($comment_counts > 0) {
                    $comments = array();
                    foreach ($query_comment->result_array() as $rows) {
                        $comment_id = $rows['id'];
                        $comment_user_name = $rows['name'];
                        $comment = $rows['comment'];
                        $comment = preg_replace('~[\r\n]+~', '', $comment);
                        if ($comment_id > '8') {
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
                        $comment_date = $rows['date'];
                        $comment_post_user_id = $rows['post_user_id'];

                        $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->num_rows();

                        if ($img_count > 0) {
                            $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->row();
                            $img_file = $profile_query->source;
                            $comment_user_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                        } else {
                            $comment_user_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
                        $comment_date = get_time_difference_php($comment_date);
                        $comment_like_count = $this->db->select('id')->from('college_post_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                        $comment_like_yes_no = $this->db->select('id')->from('college_post_comment_like')->where('user_id', $user_id)->where('comment_id', $comment_id)->get()->num_rows();
                        $comments[] = array(
                            'id' => $comment_id,
                            'comment_user_id' => $comment_post_user_id,
                            'username' => $comment_user_name,
                            'userimage' => $comment_user_image,
                            'like_count' => $comment_like_count,
                            'like_yes_no' => $comment_like_yes_no,
                            'comment' => $comment,
                            'comment_date' => $comment_date
                        );
                    }
                } else {
                    $comments = array();
                }

                $resultpost[] = array(
                    'id' => $id,
                    'post_type' => $type,
                    'post_user_id' => $post_user_id,
                    'username' => $username,
                    'userimage' => $userimage,
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
                    'date' => $date,
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

    public function college_post_question($user_id, $post, $type, $college_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $college_post = array(
            'user_id' => $user_id,
            'post' => $post,
            'type' => $type,
            'college_id' => $college_id,
            'created_at' => $created_at
        );
        $this->db->insert('college_post', $college_post);
        return array(
            'status' => 200,
            'message' => 'success',
        );
    }

    public function college_post_details($user_id, $post_id) {

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

        $resultpost = array();

        $query = $this->db->query("select college_post.id,college_post.user_id,college_post.type,college_post.created_at,college_post.post,college_post.article_title, college_post.article_image, college_post.article_domain_name, college_post.article_url,users.name FROM college_post INNER JOIN users on users.id=college_post.user_id WHERE college_post.id='$post_id' order by college_post.id desc");

        $count_post = $query->num_rows();
        if ($count_post > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $post = $row['post'];
                $username = $row['name'];
                $post = preg_replace('~[\r\n]+~', '', $post);
                if ($post_id >= '27') {
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

                $post_user_id = $row['user_id'];
                $type = $row['type'];
                $article_title = $row['article_title'];
                $article_image = $row['article_image'];
                $article_domain_name = $row['article_domain_name'];
                $article_url = $row['article_url'];
                $views = 0;
                $video_views = 0;

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $query_media = $this->db->query("SELECT college_post_media.id AS media_id,college_post_media.source,college_post_media.type AS media_type,IFNULL(college_post_media.caption,'') AS caption,college_post_media.img_width,college_post_media.img_height,college_post_media.video_width,college_post_media.video_height FROM college_post_media INNER JOIN college_post on college_post_media.post_id=college_post.id WHERE college_post_media.post_id='$id'");
                $img_val = '';
                $images = '';
                $img_comma = '';
                $img_width = '';
                $img_height = '';
                $video_width = '';
                $video_height = '';

                $media_array = array();

                foreach ($query_media->result_array() as $media_row) {
                    $media_id = $media_row['media_id'];
                    $media_type = $media_row['media_type'];
                    $source = $media_row['source'];
                    $images = 'https://d2c8oti4is0ms3.cloudfront.net/images/college_media/' . $media_type . '/' . $source;
                    if ($media_type == 'video') {
                        $thumb = 'https://d2c8oti4is0ms3.cloudfront.net/images/college_media/thumb/' . substr_replace($source, 'jpg', strrpos($source, '.') + 1);
                    } else {
                        $thumb = '';
                    }
                    $caption = $media_row['caption'];
                    $img_width = $media_row['img_width'];
                    $img_height = $media_row['img_height'];
                    $video_width = $media_row['video_width'];
                    $video_height = $media_row['video_height'];

                    $view_media_count = $this->db->select('id')->from('college_video_views')->where('media_id', $media_id)->get()->num_rows();

                    $view_media_yes_no_query = $this->db->query("SELECT id from college_video_views WHERE media_id='$media_id' and user_id='$user_id'");
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


                $date = $row['created_at'];
                $date = get_time_difference_php($date);
                $like_count = $this->db->select('id')->from('college_post_likes')->where('post_id', $id)->get()->num_rows();
                $comment_count = $this->db->select('id')->from('college_post_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('college_post_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                // $character_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/college_media/character/' . $character_image;
                $view_count = $this->db->select('id')->from('college_post_views')->where('post_id', $id)->get()->num_rows();
                $view_yes_no_query = $this->db->query("SELECT id from college_post_views WHERE post_id='$id' and user_id='$user_id'");
                $view_post_yes_no = $view_yes_no_query->num_rows();
                $follow_count = $this->db->select('id')->from('college_follow_post')->where('post_id', $id)->get()->num_rows();
                $follow_yes_no_query = $this->db->query("SELECT id from college_follow_post where post_id='$id' and user_id='$user_id'");
                $follow_post_yes_no = $follow_yes_no_query->num_rows();
                $share_url = "https://play.google.com/store/apps/details?id=com.medicalwale.medicalwale";

                //comments
                $query_comment = $this->db->query("SELECT college_post_comment.id,college_post_comment.comment,college_post_comment.date,college_post_comment.user_id as post_user_id,users.name FROM college_post_comment INNER JOIN users on users.id=college_post_comment.user_id WHERE college_post_comment.post_id='$post_id' order by college_post_comment.id DESC LIMIT 0,3");
                $comment_counts = $query_comment->num_rows();
                if ($comment_counts > 0) {
                    $comments = array();
                    foreach ($query_comment->result_array() as $rows) {
                        $comment_id = $rows['id'];
                        $comment = $rows['comment'];
                        $comment = preg_replace('~[\r\n]+~', '', $comment);
                        if ($comment_id > '8') {
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
                        $comment_date = get_time_difference_php($comment_date);
                        $comment_like_count = $this->db->select('id')->from('college_post_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                        $comment_like_yes_no = $this->db->select('id')->from('college_post_comment_like')->where('user_id', $user_id)->where('comment_id', $comment_id)->get()->num_rows();
                        $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->num_rows();

                        if ($img_count > 0) {
                            $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $comment_post_user_id)->get()->row();
                            $img_file = $profile_query->source;
                            $comment_user_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                        } else {
                            $comment_user_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }

                        $comments[] = array(
                            'id' => $comment_id,
                            'comment_user_id' => $comment_post_user_id,
                            'username' => $comment_username,
                            'image' => $comment_user_image,
                            'like_count' => $comment_like_count,
                            'like_yes_no' => $comment_like_yes_no,
                            'comment' => $comment,
                            'comment_date' => $comment_date
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
                    'username' => $username,
                    'userimage' => $userimage,
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
                    'date' => $date,
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

    public function college_post_like($user_id, $post_id, $post_user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT * FROM `college_post_likes` WHERE user_id='$user_id' AND post_id='$post_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `college_post_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from `college_post_likes` WHERE post_id='$post_id'");
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
                'post_id' => $post_id
            );
            $this->db->insert('college_post_likes', $ask_saheli_likes);

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
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
            }

            $like_query = $this->db->query("SELECT id from `college_post_likes` WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function college_post_comment($user_id, $post_id, $comment, $post_user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $ask_saheli_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('college_post_comment', $ask_saheli_comment);


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
            $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
        }

        $ask_saheli_comment_query = $this->db->query("SELECT id from college_post_comment WHERE post_id='$post_id'");
        $total_comment = $ask_saheli_comment_query->num_rows();

        return array(
            'status' => 201,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }

    public function college_post_comment_list($user_id, $post_id) {

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
        $review_count = $this->db->select('id')->from('college_post_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT college_post_comment.id,college_post_comment.comment,college_post_comment.date,college_post_comment.user_id as post_user_id,users.name  FROM college_post_comment INNER JOIN users on users.id=college_post_comment.user_id WHERE college_post_comment.post_id='$post_id' order by college_post_comment.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $comment = $row['comment'];
                $username = $row['name'];
                if(!empty($comment) && $comment !==""){
                    $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '8') {
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
                }

                $date = $row['date'];
                $post_user_id = $row['post_user_id'];
                $comment_date = get_time_difference_php($date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }

                $like_count = $this->db->select('id')->from('college_post_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('college_post_comment_like')->where('user_id', $user_id)->where('comment_id', $id)->get()->num_rows();

                $resultpost[] = array(
                    'id' => $id,
                    'comment_user_id' => $post_user_id,
                    'username' => $username,
                    'userimage' => $userimage,
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

    public function college_post_comment_like($user_id, $comment_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from college_post_comment_like WHERE comment_id='$comment_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `college_post_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from college_post_comment_like WHERE comment_id='$comment_id'");
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
            );
            $this->db->insert('college_post_comment_like', $ask_saheli_comment_like);


            $comment_query = $this->db->query("SELECT id from `college_post_comment_like` WHERE comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }

    public function college_post_hide($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `college_post_hide` WHERE  user_id='$user_id' and post_id='$post_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `college_post_hide` WHERE user_id='$user_id' and post_id='$post_id'");
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
            $this->db->insert('college_post_hide', $ask_saheli_post_hide);

            return array(
                'status' => 200,
                'message' => 'success',
                'is_hide' => '1'
            );
        }
    }

    public function college_post_delete($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from `college_post` WHERE  user_id='$user_id' and id='$post_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `college_post` WHERE user_id='$user_id' and id='$post_id'");


            //unlink images
            $media_query = $this->db->query("SELECT college_post_media.source,college_post_media.type AS media_type FROM college_post_media INNER JOIN college_post on college_post_media.post_id=college_post.id WHERE college_post_media.post_id='$post_id'");

            foreach ($media_query->result_array() as $media_row) {
                $source = $media_row['source'];
                $media_type = $media_row['media_type'];
                $file = 'images/college_media/' . $media_type . '/' . $source;
                @unlink(trim($file));
                DeleteFromToS3($file);
            }

            //unlink images ends  
            $this->db->query("DELETE FROM `college_post_media` WHERE post_id='$post_id'");

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

    public function college_follow_post($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from college_follow_post WHERE post_id='$post_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `college_follow_post` WHERE user_id='$user_id' and post_id='$post_id'");
            $follow_query = $this->db->query("SELECT id from college_follow_post WHERE post_id='$post_id'");
            $total_follow = $follow_query->num_rows();
            return array(
                'status' => 200,
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
            $this->db->insert('college_follow_post', $follow_post);
            $follow_query = $this->db->query("SELECT id from college_follow_post WHERE post_id='$post_id'");
            $total_follow = $follow_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'follow' => '1',
                'total_follow' => $total_follow
            );
        }
    }

    public function college_video_views($user_id, $media_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $college_video_views_array = array(
            'user_id' => $user_id,
            'media_id' => $media_id,
            'view_date' => $date
        );
        $this->db->insert('college_video_views', $college_video_views_array);

        $video_views = $this->db->select('id')->from('college_video_views')->where('media_id', $media_id)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'college_video_views' => $video_views
        );
    }

   public function medical_college_question($user_id,$post,$post_id) {
        $query = $this->db->query("UPDATE `college_post` SET `user_id`='$user_id',`post`='$post' WHERE  id='$post_id'");
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
   public function medical_college_comment_reply($user_id, $post_id, $comment_id, $comment, $post_user_id, $post_comment_user_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $comments = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment_id' => $comment_id,
            'comment' => $comment,
            'created_at' => $created_at
            //'updated_at' => $created_at
        );
        $this->db->insert('medical_college_comment_reply', $comments);
        $id = $this->db->insert_id();

        $query_comment_reply = $this->db->query("SELECT medical_college_comment_reply.id,medical_college_comment_reply.comment_id,medical_college_comment_reply.post_id,medical_college_comment_reply.comment, users.name,medical_college_comment_reply.user_id FROM medical_college_comment_reply INNER JOIN users on users.id=medical_college_comment_reply.user_id WHERE medical_college_comment_reply.id ='$id'")->row();
        $comment_reply_id = $query_comment_reply->id;
        $comment_reply_username = $query_comment_reply->name;
        $comment_reply_post_id = $query_comment_reply->post_id;
        $comment_reply_date = $query_comment_reply->date;
        $comment_reply_user_id = $query_comment_reply->user_id;
        $comment_reply = $query_comment_reply->comment;

        $comment_reply = preg_replace('~[\r\n]+~', '', $comment_reply);
		if($comment_reply!=''){
			$comment_decrypt = $this->decrypt($comment_reply);
			$comment_encrypt = $this->encrypt($comment_decrypt);
			if ($comment_encrypt == $comment_reply) {
				$comment_reply = $comment_decrypt;
			}
		}
        $comment_reply_like_count = $this->db->select('id')->from('medical_college_comment_reply_likes')->where('medical_college_comment_reply_likes', $comment_reply_id)->get()->num_rows();
        $comment_reply_like_yes_no = $this->db->select('id')->from('medical_college_comment_reply_likes')->where('comment_reply_id', $comment_reply_id)->where('user_id', $user_id)->get()->num_rows();
        $comment_reply_date = $this->get_time_difference_php($comment_reply_date);
        $comment_reply_img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
        if ($comment_reply_img_count > 0) {
            $comment_reply_profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
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
        $customer_token3=$this->db->query("SELECT token,agent,token_status FROM users WHERE id='$post_comment_user_id' AND $post_comment_user_id<>$user_id");
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
            $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent, $post_id);
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


}
