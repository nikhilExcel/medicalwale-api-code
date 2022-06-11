<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class SurvivorstoryModel extends CI_Model {

    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";

    public function check_auth_client() {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
        }
    }

    public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            return json_output(401, array('status' => 401, 'message' => 'Unauthorized.'));
        } else {
            if ($q->expired_at < date('Y-m-d H:i:s')) {
                return json_output(401, array('status' => 401, 'message' => 'Your session has been expired.'));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = '2030-11-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array('expired_at' => $expired_at, 'updated_at' => $updated_at));
                return array('status' => 200, 'message' => 'Authorized.');
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

    public function story_list($user_id, $page) {

        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;


        $query_lang   = $this->db->query("SELECT language FROM users where id='$user_id'");
          $count20 = $query_lang->num_rows();
        if ($count20 > 0) {
            $new_lang=$query_lang->row();
        $lang=$new_lang->language;
        }
        else
        {
           $lang=0;
        }
        $query = $this->db->query("SELECT `id`, IFNULL(tag,'') AS tag, IFNULL(author,'') AS author, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date` FROM article where cat_id='37' AND language ='$lang' order by id desc limit $start,$limit");

        function RemoveBS($Str) {
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

        //$short_desc = '';
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $article_id = $row['id'];

                if($lang ==1)
                {
                    $tag = $this->decrypt($row['tag']);
                    $author = $this->decrypt($row['author']);
                    $article_title = $this->decrypt(($row['article_title']));
                    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);

                    $str = $this->decrypt($row['article_description']);
                    if (strlen($str) > 80) {
                        $str = substr($str, 0, 80);
                        $str = strip_tags(htmlspecialchars_decode($str));
                    }
                    $short_desc = $str;
                }
                else
                {
                    $tag = $row['tag'];
                    $author = $row['author'];
                    $article_title = RemoveBS($row['article_title']);
                    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];

                    $str = $row['article_description'];
                    if (strlen($str) > 80) {
                        $str = substr($str, 0, 80);
                        $str = strip_tags(htmlspecialchars_decode($str));
                    }
                    $short_desc = $str;
                }
                $article_image = str_replace("itâ€™s", "it's", $row['image']);
                $article_date = $row['posted'];
                $author = 'Medicalwale.com';
                $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
                $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();

                $post_followers = $this->db->select('id')->from('article_follow_post')->where('post_id', $article_id)->get()->num_rows();

                $is_follow = $this->db->select('id')->from('article_follow_post')->where('user_id', $user_id)->where('post_id', $article_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                 $article_title_final = (str_replace(' ', '-', strtolower($article_title)));

                $share_url = "https://medicalwale.com/inspiring_survivor_story/" . $article_id."/".$article_title_final;

                $resultpost[] = array(
                    'article_id' => $article_id,
                    'author' => $author,
                    'tag' => $tag,
                    'article_title' => $article_title,
                    'article_description' => $article_description,
                    'short_desc' => $article_title,
                    'article_image' => $article_image,
                    'article_date' => $article_date,
                    'author' => $author,
                    'share_url' => $share_url,
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


    public function article_story_list($user_id) {
        $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
        $lang=$query_lang->language;

        $query = $this->db->query("SELECT article.id,article.article_title,article.image,article.tag,article.author,article.article_description,article.posted,article.is_active,article.updated_date FROM article INNER JOIN article_category ON article.cat_id=article_category.id WHERE article_category.type='app_article' and article.language ='$lang' order by id desc");



        function RemoveBS($Str) {
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

        //$short_desc = '';
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $article_id = $row['id'];

                if($lang ==1)
                {
                    $tag = $this->decrypt($row['tag']);
                    $author = $this->decrypt($row['author']);
                    $article_title = $this->decrypt(($row['article_title']));
                    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);

                    $str = $this->decrypt($row['article_description']);
                    if (strlen($str) > 80) {
                        $str = substr($str, 0, 80);
                        $str = strip_tags(htmlspecialchars_decode($str));
                    }
                    $short_desc = $str;
                }
                else
                {
                    $tag = $row['tag'];
                    $author = $row['author'];
                    $article_title = RemoveBS($row['article_title']);
                    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];

                    $str = $row['article_description'];
                    if (strlen($str) > 80) {
                        $str = substr($str, 0, 80);
                        $str = strip_tags(htmlspecialchars_decode($str));
                    }
                    $short_desc = $str;
                }
                $article_image = str_replace("itâ€™s", "it's", $row['image']);
                $article_date = $row['posted'];
                $author = 'Medicalwale.com';
                $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
                $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();

                $post_followers = $this->db->select('id')->from('article_follow_post')->where('post_id', $article_id)->get()->num_rows();

                $is_follow = $this->db->select('id')->from('article_follow_post')->where('user_id', $user_id)->where('post_id', $article_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                 $article_title_final = (str_replace(' ', '-', strtolower($article_title)));

                $share_url = "https://medicalwale.com/inspiring_survivor_story/" . $article_id."/".$article_title_final;

                $resultpost[] = array(
                    'article_id' => $article_id,
                    'author' => $author,
                    'tag' => $tag,
                    'article_title' => $article_title,
                    'article_description' => $article_description,
                    'short_desc' => $article_title,
                    'article_image' => $article_image,
                    'article_date' => $article_date,
                    'author' => $author,
                    'share_url' => $share_url,
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

     public function story_details($user_id, $post_id) {
        $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
        $lang=$query_lang->language;

        $query = $this->db->query("SELECT `id`, IFNULL(tag,'') AS tag, IFNULL(author,'') AS author, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date` FROM article WHERE id='$post_id' AND language ='$lang'");

        function RemoveBS($Str) {
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

        //$short_desc = '';
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $article_id = $row['id'];
                if($lang ==1)
                {
                    $author = $this->decrypt($row['author']);
                    $tag = $this->decrypt($row['tag']);
                    $article_title = $this->decrypt($row['article_title']);
                    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);

                    $str = $this->decrypt($row['article_description']);
                    if (strlen($str) > 80) {
                        $str = substr($str, 0, 80);
                        $str = strip_tags(htmlspecialchars_decode($str));
                    }
                    $short_desc = $str;
                }
                else
                {
                    $author = $row['author'];
                    $tag = $row['tag'];
                    $article_title = RemoveBS($row['article_title']);
                    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];

                    $str = $row['article_description'];
                    if (strlen($str) > 80) {
                        $str = substr($str, 0, 80);
                        $str = strip_tags(htmlspecialchars_decode($str));
                    }
                    $short_desc = $str;
                }


                $article_image = str_replace("itâ€™s", "it's", $row['image']);
                $article_date = $row['posted'];
                $author = 'Medicalwale.com';
                $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
                $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();

                $post_followers = $this->db->select('id')->from('article_follow_post')->where('post_id', $article_id)->get()->num_rows();

                $is_follow = $this->db->select('id')->from('article_follow_post')->where('user_id', $user_id)->where('post_id', $article_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $share_url = "https://medicalwale.com/share/survivorstories/" . $article_id;

                $resultpost = array(
                    'article_id' => $article_id,
                    'author' => $author,
                    'tag' => $tag,
                    'article_title' => $article_title,
                    'article_description' => $article_description,
                    'short_desc' => $article_title,
                    'article_image' => $article_image,
                    'article_date' => $article_date,
                    'author' => $author,
                    'share_url' => $share_url,
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

    public function story_like($user_id, $article_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM `article_likes` WHERE user_id='$user_id' and article_id='$article_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `article_likes` WHERE user_id='$user_id' and article_id='$article_id'");
            $like_query = $this->db->query("SELECT id FROM `article_likes` WHERE article_id='$article_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $article_review_likes = array(
                'user_id' => $user_id,
                'article_id' => $article_id
            );
            $this->db->insert('article_likes', $article_review_likes);
            $like_query = $this->db->query("SELECT id FROM `article_likes` WHERE article_id='$article_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function add_review($user_id, $article_id, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'article_id' => $article_id,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('article_review', $review_array);
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }

    public function review_list($user_id, $article_id) {

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
        $review_count = $this->db->select('id')->from('article_review')->where('article_id', $article_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT article_review.id,article_review.user_id,article_review.article_id,article_review.review, article_review.service,article_review.date as review_date,users.id as user_id,users.name as firstname FROM `article_review` INNER JOIN `users` ON article_review.user_id=users.id WHERE article_review.article_id='$article_id' order by article_review.id desc");

            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $username = $row['firstname'];
                $review = $row['review'];
                $review = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '37') {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($review)) === $review) {
                        $review = base64_decode($review);
                    }
                }
                $service = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);

                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();

                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                    $img_file = $profile_query->source;
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }


                $count_query = $this->db->query("SELECT id FROM `article_review_likes` WHERE post_id='$id'");
                $like_count = $count_query->num_rows();


                $res3 = $this->db->query("SELECT id FROM `article_review_likes` WHERE user_id='$user_id' and post_id='$id'");
                $count3 = $res3->num_rows();
                if ($count3 > 0) {
                    $like_yes_no = 1;
                } else {
                    $like_yes_no = 0;
                }
                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'userimage' => $userimage,
                    'comment' => $review,
                    'service' => $service,
                    'comment_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no
                );
            }
        } else {
            $resultpost = array();
        }

        return $resultpost;
    }

    public function article_review_likes($user_id, $comment_id) {
        $post_id = $comment_id;
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $count_query = $this->db->query("SELECT id FROM `article_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `article_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id FROM `article_review_likes` WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $article_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('article_review_likes', $article_review_likes);
            $like_query = $this->db->query("SELECT id FROM `article_review_likes` WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }

    public function story_views($user_id, $article_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $article_views_array = array(
            'user_id' => $user_id,
            'article_id' => $article_id
        );
        $this->db->insert('article_views', $article_views_array);

        $article_views = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'article_views' => $article_views
        );
    }

    public function story_bookmark($user_id, $article_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $count_query = $this->db->query("SELECT id FROM `article_bookmark` WHERE user_id='$user_id' and article_id='$article_id'");
        $count = $count_query->num_rows();
        $cat_id = "37";
        if ($count > 0) {
            $this->db->query("DELETE FROM `article_bookmark` WHERE user_id='$user_id' and article_id='$article_id'");
            $like_query = $this->db->query("SELECT id FROM `article_bookmark` WHERE article_id='$article_id'");
            $total_bookmark = $like_query->num_rows();

            return array(
                'status' => 200,
                'message' => 'deleted',
                'bookmark' => '0',
                'total_bookmark' => $total_bookmark
            );
        } else {
            $article_bookmark = array(
                'user_id' => $user_id,
                'article_id' => $article_id,
                'cat_id' => $cat_id
            );
            $this->db->insert('article_bookmark', $article_bookmark);
            $like_query = $this->db->query("SELECT id FROM `article_bookmark` WHERE article_id='$article_id'");
            $total_bookmark = $like_query->num_rows();
            return array(
                'status' => 200,
                'message' => 'success',
                'bookmark' => '1',
                'total_bookmark' => $total_bookmark
            );
        }
    }

    public function related_story_list($article_id, $user_id) {

        $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
        $lang=$query_lang->language;

        $query = $this->db->query("SELECT `id`, IFNULL(tag,'') AS tag, IFNULL(author,'') AS author, `article_title`, `article_description`, `image`, `posted`, `is_active`, `updated_date` FROM article WHERE cat_id='37' AND language ='$lang' AND id<>'$article_id'");

        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $article_id = $row['id'];
                if($lang ==1)
                {
                    $tag = $this->decrypt($row['tag']);
                    $author = $this->decrypt($row['author']);
                    $article_title = $this->decrypt($row['article_title']);
                    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
                }
                else
                {
                     $tag = $row['tag'];
                    $author = $row['author'];
                    $article_title = $row['article_title'];
                    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];

                }
                //$article_description= $row['article_description'];
                $article_image = $row['image'];
                $article_date = $row['posted'];

                $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;


                $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $views_count = $this->db->select('id')->from('article_views')->where('article_id', $article_id)->get()->num_rows();
                $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();

                $post_followers = $this->db->select('id')->from('article_follow_post')->where('post_id', $article_id)->get()->num_rows();

                $is_follow = $this->db->select('id')->from('article_follow_post')->where('user_id', $user_id)->where('post_id', $article_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }


                $resultpost[] = array(
                    'article_id' => $article_id,
                    'article_title' => $article_title,
                    'article_description' => $article_description,
                    'article_image' => $article_image,
                    'article_date' => $article_date,
                    'author' => $author,
                    'tag' => $tag,
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

    public function story_follow($user_id, $post_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from article_follow_post where user_id='$user_id' and post_id='$post_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `article_follow_post` WHERE user_id='$user_id' and post_id='$post_id'");
            $follow_query = $this->db->query("SELECT id from article_follow_post where post_id='$post_id'");
            $total_follow = $follow_query->num_rows();

            return array(
                'status' => 201,
                'message' => 'deleted',
                'follow' => '0',
                'total_follow' => $total_follow
            );
        } else {
            $follow_user = array(
                'user_id' => $user_id,
                'post_id' => $post_id,
                'created_at' => $created_at,
                'deleted_at' => $created_at
            );
            $this->db->insert('article_follow_post', $follow_user);
            $follow_query = $this->db->query("SELECT id from article_follow_post where post_id='$post_id'");
            $total_follow = $follow_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'follow' => '1',
                'total_follow' => $total_follow
            );
        }
    }

    public function bookmark($user_id, $survival_stories_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from survival_stories_bookmark where survival_stories_id='$survival_stories_id' and user_id='$user_id'");
        $count = $count_query->num_rows();

        if ($count > 0) {
            $this->db->query("DELETE FROM `survival_stories_bookmark` WHERE user_id='$user_id' and survival_stories_id='$survival_stories_id'");
            $like_query = $this->db->query("SELECT id from survival_stories_bookmark where survival_stories_id='$survival_stories_id'");
            $total_like = $like_query->num_rows();
            return array('status' => 201, 'message' => 'deleted', 'bookmark' => '0');
        } else {
            $survival_stories_bookmark = array(
                'user_id' => $user_id,
                'survival_stories_id' => $survival_stories_id
            );
            $this->db->insert('survival_stories_bookmark', $survival_stories_bookmark);
            $like_query = $this->db->query("SELECT id from survival_stories_bookmark where survival_stories_id='$survival_stories_id'");
            $total_like = $like_query->num_rows();
            return array('status' => 201, 'message' => 'success', 'bookmark' => '1');
        }
    }

    public function story_bookmark_list($user_id) {

        $query = $this->db->query("SELECT  article_bookmark.id, article_bookmark.user_id, article_bookmark.article_id, article.id, article.article_title, article.article_description, article.tag, article.author, article.image, article.posted FROM `article`
            INNER JOIN article_bookmark ON article.id = article_bookmark.article_id
            WHERE article_bookmark.user_id = '$user_id' order by article_bookmark.id");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $title = $row['article_title'];
                $description = $row['article_description'];
                $tag = $row['tag'];
                $author = $row['author'];
                $image = $row['image'];
                $date = $row['posted'];
                $image = str_replace(' ', '%20', $image);
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $image;

                //	$bookmark_yes_no = $this->db->select('id')->from('survival_stories_bookmark')->where('user_id',$user_id)->where('survival_stories_id',$id)->get()->num_rows();


                $res2 = $this->db->query("SELECT id FROM `article_bookmark` WHERE user_id='$user_id' and article_id='$id'");
                $count2 = $res2->num_rows();
                if ($count2 > 0) {
                    $is_bookmark = 1;
                } else {
                    $is_bookmark = 0;
                }

                $res3 = $this->db->query("SELECT id FROM `article_likes` WHERE user_id='$user_id' and article_id='$id'");
                $count3 = $res3->num_rows();
                if ($count3 > 0) {
                    $is_like = 1;
                } else {
                    $is_like = 0;
                }

                $res4 = $this->db->query("SELECT count(id) as count FROM `article_views` WHERE article_id='$id'");
                $row_count4 = $res4->row();
                $views     = $row_count4->count;

                $res5 = $this->db->query("SELECT count(id) as count FROM `article_likes` WHERE article_id='$id'");
                $row_count5 = $res5->row();
                $total_like = $row_count5->count;

                $share = 'https://d2c8oti4is0ms3.cloudfront.net/images/';

                $survival_stories[] = array("id" => $id,
                    "title" => $title,
                    'description' => $description,
                    'tag' => $tag,
                    'author' => $author,
                    'image' => $image,
                    'date' => $date,
                    'share' => $share,
                    'is_bookmark' => $is_bookmark,
                    'is_like' => $is_like,
                    'total_like' => $total_like,
                    'views' => $views,
                    'type' => 'survival_stories');
            }
        } else {
            $survival_stories = array();
        }

        $query = $this->db->query("SELECT  home_remedies_bookmark.id, home_remedies_bookmark.user_id, home_remedies_bookmark.home_remedies_id, home_remedies.id, home_remedies.category_id, home_remedies.details, home_remedies.title,home_remedies.image, home_remedies.date FROM `home_remedies`
            INNER JOIN home_remedies_bookmark ON home_remedies.id = home_remedies_bookmark.home_remedies_id
            WHERE home_remedies_bookmark.user_id = '$user_id' order by home_remedies_bookmark.id");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id = $row['id'];
                $category_id = $row['category_id'];
                $title = $row['title'];
                $details = $row['details'];
                $image = $row['image'];
                $date = $row['date'];
                $image = str_replace(" ", "", $image);
                $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/home_remedies/' . $image;

                //	$bookmark_yes_no = $this->db->select('id')->from('survival_stories_bookmark')->where('user_id',$user_id)->where('survival_stories_id',$id)->get()->num_rows();

                $res2 = $this->db->query("SELECT id FROM `home_remedies_bookmark` WHERE user_id='$user_id' and home_remedies_id='$id'");
                $count2 = $res2->num_rows();
                if ($count2 > 0) {
                    $is_bookmark = 1;
                } else {
                    $is_bookmark = 0;
                }

                $res3 = $this->db->query("SELECT id FROM `home_remedies_likes` WHERE user_id='$user_id' and home_remedies_id='$id'");
                $count3 = $res3->num_rows();
                if ($count3 > 0) {
                    $is_like = 1;
                } else {
                    $is_like = 0;
                }

                $res4 = $this->db->query("SELECT id FROM `home_remedies_views` WHERE home_remedies_id='$id'");
                $views = $res4->num_rows();

                $res5 = $this->db->query("SELECT id FROM `home_remedies_likes` WHERE home_remedies_id='$id'");
                $total_like = $res5->num_rows();


                $share = 'https://d2c8oti4is0ms3.cloudfront.net/images/';

                $home_remedies[] = array("id" => $id,
                    "title" => $title,
                    "category_id" => $category_id,
                    "description" => $details,
                    'image' => $image,
                    'date' => $date,
                    'is_bookmark' => $is_bookmark,
                    'is_like' => $is_like,
                    'total_like' => $total_like,
                    'views' => $views,
                    'type' => 'home_remedies');
            }
        } else {
            $home_remedies = array();
        }
        $resultpost[] = array(
            array_merge($survival_stories, $home_remedies)
        );


        return $resultpost;
    }


    public function random_number_generation($user_id)
    {


     for  ($j=0; $j < 10000; $j++)
    {
         $token = "RGB";
     $codeAlphabet = "";
     //$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
     $codeAlphabet.= "0123456789";
     $max = strlen($codeAlphabet); // edited

    for ($i=0; $i < 9; $i++) {
        $token .= $codeAlphabet[random_int(0, $max-1)];
        }

        $associated_pin ="";
        $max1 = strlen($token);

        for ($i=0; $i < 9; $i++)
        {
            $associated_pin .=$token[random_int(0, $max1-1)];
        }

    //  echo ($token . "\n");

      $tokenArray[] = array (
               $token
          );

        $associated_pinArray[] = array    (
               $associated_pin
          );

    }
      echo (" tokken random\n");
    print_r ($tokenArray);
    echo ("\n");
    echo ("ssociated pin \n");
    print_r ($associated_pinArray);
    echo ("\n");
    $tokenArrayfinal = array
    (
               $tokenArray,
               $associated_pinArray
          );
    return $tokenArrayfinal;



    }



}
