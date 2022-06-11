<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class ArticleModel extends CI_Model {

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

    public function blog_article_list($user_id) {
        
        
        $ad_array='0,';
        $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
        $lang=$query_lang->language;
        
        $query = $this->db->query("SELECT article.id,article.views,article.cat_id, article.article_title, article.article_description, article.image, article.posted, article.is_active, article.updated_date ,article_category.name AS category_name,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id WHERE article_category.type='app_article' AND article.language='$lang' order by RAND() limit 10");

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
                $category_id = $row['cat_id'];
                $article_title = RemoveBS($row['article_title']);
                $article_description = $row['article_description'];
                $article_description = str_replace('&nbsp;', '', $article_description);
                $article_description = str_replace("&#39;", "'", $article_description);
                
                
                if($lang == 1)
                {
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
                    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $article_description;
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
                $views_count = $row['views'];
                $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();

                $post_followers = $this->db->select('id')->from('article_follow_post')->where('post_id', $article_id)->get()->num_rows();

                $is_follow = $this->db->select('id')->from('article_follow_post')->where('user_id', $user_id)->where('post_id', $article_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $cat_name = $row['category_name'];
                $category_name = (str_replace(' ', '-', strtolower($cat_name)));
                $type = $row['type'];
                $article_title_final = (str_replace(' ', '-', strtolower($article_title)));
                $share_url='';
                if ($type == 'app_article') {
                    $share_url = "https://medicalwale.com/related_article/".$cat_name ."/". $article_id."/".$article_title_final;
                } elseif ($type == 'home_remedies') {
                    $share_url = "https://medicalwale.com/mom_remedies/".$cat_name ."/". $article_id."/".$article_title_final;
                } elseif ($type == 'healthwall') {
                    $share_url = "https://medicalwale.com/share/healthwall/" . $article_id;
                }
                $short_desc = str_replace("&#39;", "'", $short_desc);
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
                    'article_id' => $article_id,
                    'category_id' => $category_id,
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
                    'total_follow' => $post_followers,
                    'ad_list' => $ad_post
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    //get all article list
    public function article_list($category_id, $user_id, $page) {
        $ad_array='0,';
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
        //echo "select('language')->from('users')->where('id', $user_id)";
        $lang=$query_lang->language;
         if($category_id == "")
         {
            $query = $this->db->query("SELECT article.id,article.views,article.article_title,article.image,article.tag,article.author,article.article_description,article.posted,article.is_active,article.updated_date FROM article  WHERE article.language ='$lang' order by id desc limit $start,$limit");  
         }
         else
         {
        $query = $this->db->query("SELECT article.id,article.views, article.cat_id, article.article_title, article.article_title_hindi, article.article_description, article.article_description_hindi, article.image, article.posted, article.is_active, article.updated_date ,article_category.name AS category_name,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id WHERE article.cat_id='$category_id' AND article.language='$lang' limit $start,$limit");
         }
      //  echo "SELECT article.id, article.cat_id, article.article_title, article.article_title_hindi, article.article_description, article.article_description_hindi, article.image, article.posted, article.is_active, article.updated_date ,article_category.name AS category_name,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id WHERE article.cat_id='$category_id' AND article.language='$lang' limit $start,$limit";
        $count = $query->num_rows();
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
      
        if ($count > 0) {
            
            foreach ($query->result_array() as $row) {
                $article_id = $row['id'];
                $article_title = RemoveBS($row['article_title']);
                $article_description = $row['article_description'];
               //hindi = 1
               if($lang == 1)   
               {
                  
                    $article_title = $this->decrypt($row['article_title']);
                    $article_description = $this->decrypt($row['article_description']);
            
                    $str_hindi = $this->decrypt($row['article_description']);
                    if (strlen($row['article_description']) > 80) {
                        $str_hindi = substr($row['article_description'], 0, 80);
                        $str_hindi = $this->decrypt($str_hindi);
                       // $str_hindi = strip_tags(htmlspecialchars_decode($str_hindi));
                    }
                    $short_desc = $str_hindi;
            
                    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $article_description;
             
               }
               else
               {
                    $article_title = RemoveBS($row['article_title']);
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
              
                $article_image = str_replace("itâ€™s", "it's", $row['image']);
                $article_date = $row['posted'];
                $author = 'Medicalwale.com';
                $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
                $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $views_count = $row['views'];
                $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();

                $post_followers = $this->db->select('id')->from('article_follow_post')->where('post_id', $article_id)->get()->num_rows();

                $is_follow = $this->db->select('id')->from('article_follow_post')->where('user_id', $user_id)->where('post_id', $article_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                if($category_id == "")
         {
               $cat_name = ""; 
         }
         else
         {
             $cat_name = $row['category_name'];
         }
                $category_name = (str_replace(' ', '-', strtolower($cat_name)));
                //$type = $row['type'];
                       if($category_id == "")
         {
              $type = "";
         }
         else
         {
              $type = $row['type'];
         }
                $article_title_final = (str_replace(' ', '-', strtolower($article_title)));
                $share_url='';
                if ($type == 'app_article') {
                    $share_url = "https://medicalwale.com/related_article/".$cat_name ."/". $article_id."/".$article_title_final;
                } elseif ($type == 'home_remedies') {
                    $share_url = "https://medicalwale.com/mom_remedies/".$cat_name ."/". $article_id."/".$article_title_final;
                } elseif ($type == 'healthwall') {
                    $share_url = "https://medicalwale.com/share/healthwall/" . $article_id;
                }
              //  $short_desc = str_replace("&#39;", "'", $short_desc);
                 
              //     echo $short_desc;
              
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
                    'article_id' => $article_id,
                    'category_id' => $category_id,
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
                    'total_follow' => $post_followers,
                    'ad_list' => $ad_post
                );
               
            }
            
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
    //get article list by keyword search  by zak
    
     public function article_list_by_keyword($category_id, $user_id, $page, $keyword) {
        
        //Pagination
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        $ad_array='0,';
        $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
        //echo "select('language')->from('users')->where('id', $user_id)";
        $lang=$query_lang->language;
         if($category_id == "")
         {
            $query = $this->db->query("SELECT article.id,article.views,article.article_title,article.image,article.tag,article.author,article.article_description,article.posted,article.is_active,article.updated_date FROM article  WHERE article.language ='$lang' AND (article.article_title LIKE '%$keyword%' OR article.article_description LIKE '%$keyword%') order by id desc limit $start,$limit");  
         }
         else
         {
        $query = $this->db->query("SELECT article.id,article.views, article.cat_id, article.article_title, article.article_title_hindi, article.article_description, article.article_description_hindi, article.image, article.posted, article.is_active, article.updated_date ,article_category.name AS category_name,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id WHERE article.cat_id='$category_id' AND article.language='$lang' AND (article.article_title LIKE '%$keyword%' OR article.article_description LIKE '%$keyword%') limit $start,$limit");
         }
      //  echo "SELECT article.id, article.cat_id, article.article_title, article.article_title_hindi, article.article_description, article.article_description_hindi, article.image, article.posted, article.is_active, article.updated_date ,article_category.name AS category_name,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id WHERE article.cat_id='$category_id' AND article.language='$lang' limit $start,$limit";
        $count = $query->num_rows();
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
      
        if ($count > 0) {
            
            foreach ($query->result_array() as $row) {
                $article_id = $row['id'];
                $article_title = RemoveBS($row['article_title']);
                $article_description = $row['article_description'];
               //hindi = 1
               if($lang == 1)   
               {
                  
                    $article_title = $this->decrypt($row['article_title']);
                    $article_description = $this->decrypt($row['article_description']);
            
                    $str_hindi = $this->decrypt($row['article_description']);
                    if (strlen($row['article_description']) > 80) {
                        $str_hindi = substr($row['article_description'], 0, 80);
                        $str_hindi = $this->decrypt($str_hindi);
                       // $str_hindi = strip_tags(htmlspecialchars_decode($str_hindi));
                    }
                    $short_desc = $str_hindi;
            
                    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $article_description;
             
               }
               else
               {
                    $article_title = RemoveBS($row['article_title']);
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
              
                $article_image = str_replace("itâ€™s", "it's", $row['image']);
                $article_date = $row['posted'];
                $author = 'Medicalwale.com';
                $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
                $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $views_count = $row['views'];
                $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();

                $post_followers = $this->db->select('id')->from('article_follow_post')->where('post_id', $article_id)->get()->num_rows();

                $is_follow = $this->db->select('id')->from('article_follow_post')->where('user_id', $user_id)->where('post_id', $article_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                if($category_id == "")
         {
               $cat_name = ""; 
         }
         else
         {
             $cat_name = $row['category_name'];
         }
                $category_name = (str_replace(' ', '-', strtolower($cat_name)));
                //$type = $row['type'];
                       if($category_id == "")
         {
              $type = "";
         }
         else
         {
              $type = $row['type'];
         }
                $article_title_final = (str_replace(' ', '-', strtolower($article_title)));
                $share_url='';
                if ($type == 'app_article') {
                    $share_url = "https://medicalwale.com/related_article/".$cat_name ."/". $article_id."/".$article_title_final;
                } elseif ($type == 'home_remedies') {
                    $share_url = "https://medicalwale.com/mom_remedies/".$cat_name ."/". $article_id."/".$article_title_final;
                } elseif ($type == 'healthwall') {
                    $share_url = "https://medicalwale.com/share/healthwall/" . $article_id;
                }
              //  $short_desc = str_replace("&#39;", "'", $short_desc);
                 
              //     echo $short_desc;
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
                    'article_id' => $article_id,
                    'category_id' => $category_id,
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
                    'total_follow' => $post_followers,
                    'ad_list' => $ad_post
                );
               
            }
            
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

    
    //get related article list
    public function related_article_list($article_id, $category_id, $user_id) {
        $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
        $lang=$query_lang->language;
        if($category_id=="")
        {
          
             $query_cat = $this->db->select('cat_id')->from('article')->where('id', $article_id)->get()->row();
             $category_id=$query_cat->cat_id;
          
            $query = $this->db->query("SELECT article.id,article.views, article.cat_id, article.article_title, article.article_title_hindi,article.article_description, article.article_description_hindi, article.image, article.posted, article.is_active, article.updated_date ,article_category.name AS category_name,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id WHERE article.cat_id='$category_id' AND article.id<>'$article_id' AND article.language='$lang' LIMIT 0,10");
        
        }
        else
        {
      
        $query = $this->db->query("SELECT article.id,article.views, article.cat_id, article.article_title, article.article_title_hindi,article.article_description, article.article_description_hindi, article.image, article.posted, article.is_active, article.updated_date ,article_category.name AS category_name,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id WHERE article.cat_id='$category_id' AND article.id<>'$article_id' AND article.language='$lang' LIMIT 0,10");
        }
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
        $ad_array='0,';
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $article_id = $row['id'];
                
                if($lang == 1)
                {
                    $article_title = $this->decrypt(($row['article_title']));
                    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
        
                }
                else
                {
                    $article_title = RemoveBS($row['article_title']);
                    $article_description = "<style>strong{color:#404547;font-weight:600;    font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $row['article_description'];
                }
            
                $article_image = $row['image'];
                $article_date = $row['posted'];
                $author = 'Medicalwale.com';
                $article_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/article_images/' . $article_image;
                $like_count = $this->db->select('id')->from('article_likes')->where('article_id', $article_id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('article_likes')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $views_count = $row['views'];
                $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();

                $post_followers = $this->db->select('id')->from('article_follow_post')->where('post_id', $article_id)->get()->num_rows();

                $is_follow = $this->db->select('id')->from('article_follow_post')->where('user_id', $user_id)->where('post_id', $article_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $type = $row['type'];
                if ($type == 'app_article') {
                    $share_url = "https://medicalwale.com/share/relatedarticles/" . $article_id;
                } elseif ($type == 'home_remedies') {
                    $share_url = "https://medicalwale.com/share/homeremedies/" . $article_id;
                }
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
                    'article_id' => $article_id,
                    'category_id' => $category_id,
                    'article_title' => $article_title,
                    'article_description' => $article_description,
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
                    'total_follow' => $post_followers,
                    'ad_list' => $ad_post
                );
               
            }
            
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

public function article_detail($user_id, $post_id) {
    
    $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
    $lang=$query_lang->language;
        
        $query = $this->db->query("SELECT article.id, article.views,article.cat_id, article.article_title,article.article_title_hindi, article.article_description,article.article_description_hindi, article.image, article.posted, article.is_active, article.updated_date ,article_category.name AS category_name,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id WHERE article.id='$post_id' AND article.language='$lang'");

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
            $row = $query->row_array();
            
                $article_id = $row['id'];
                $category_id = $row['cat_id'];
                
                if($lang == 1)
                {
                    $article_title = $this->decrypt(($row['article_title']));
                    $article_description = "<style>strong{color:#404547;font-weight:600;font-family: calibri;}p{color:#768188;text-align:justify;line-height: 1.5em;font-family: calibri;}</style>" . $this->decrypt($row['article_description']);
                
                    $str_hindi = $this->decrypt($row['article_description']);
                    if (strlen($str_hindi) > 80) {
                        $str_hindi = substr($str_hindi, 0, 80);
                        $str_hindi = strip_tags(htmlspecialchars_decode($str_hindi));
                    }
                    $short_desc = $str_hindi;
                
                }
                else
                {
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
                $views_count = $row['views'];
                $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();

                $post_followers = $this->db->select('id')->from('article_follow_post')->where('post_id', $article_id)->get()->num_rows();

                $is_follow = $this->db->select('id')->from('article_follow_post')->where('user_id', $user_id)->where('post_id', $article_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                $share_url='';
                $cat_name = $row['category_name'];
                $category_name = (str_replace(' ', '-', strtolower($cat_name)));
                $type = $row['type'];
                if ($type == 'app_article') {
                    $share_url = "https://medicalwale.com/share/relatedarticles/" . $article_id;
                } elseif ($type == 'home_remedies') {
                    $share_url = "https://medicalwale.com/share/homeremedies/" . $article_id;
                }
                
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
                
                $resultpost = array(
                    "status" => 200,
                    "message" => "success",
                    "count" => $count,  
                    'article_id' => $article_id,
                    'category_id' => $category_id,
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
                    'total_follow' => $post_followers,
                    'ad_list' => $ad_post
                );
        } else {
            $resultpost = array(
                "status" => 200,
                "message" => "success",
                "count" => 0
                );
        }
        return $resultpost;
    }


    //get article details
    public function article_details($user_id, $post_id) {
        $share_url ="";
        $query_lang = $this->db->select('language')->from('users')->where('id', $user_id)->get()->row();
        $lang=$query_lang->language;
       
        $query = $this->db->query("SELECT article.id,article.views, article.cat_id, article.article_title, article.article_description, article.image, article.posted, article.is_active, article.updated_date ,article_category.name AS category_name,article_category.type FROM article INNER JOIN article_category ON article.cat_id=article_category.id WHERE article.id='$post_id' AND article.language='$lang'");

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
                $category_id = $row['cat_id'];
                if($lang == 1)
                {
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
                $views_count = $row['views'];
                $is_bookmark = $this->db->select('id')->from('article_bookmark')->where('user_id', $user_id)->where('article_id', $article_id)->get()->num_rows();
                $total_bookmark = $this->db->select('id')->from('article_bookmark')->where('article_id', $article_id)->get()->num_rows();

                $post_followers = $this->db->select('id')->from('article_follow_post')->where('post_id', $article_id)->get()->num_rows();

                $is_follow = $this->db->select('id')->from('article_follow_post')->where('user_id', $user_id)->where('post_id', $article_id)->get()->num_rows();

                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                $cat_name = $row['category_name'];
                $category_name = (str_replace(' ', '-', strtolower($cat_name)));
                $type = $row['type'];
                if ($type == 'app_article') {
                    $share_url = "https://medicalwale.com/share/relatedarticles/" . $article_id;
                } elseif ($type == 'home_remedies') {
                    $share_url = "https://medicalwale.com/share/homeremedies/" . $article_id;
                }
                
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

                $resultpost = array(
                    'article_id' => $article_id,
                    'category_id' => $category_id,
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
                    'total_follow' => $post_followers,
                    'ad_list' => $ad_post
                );
                
            }
           
        } else {
            $resultpost = array();
            
        }
         
        return $resultpost;
    }

    //like-dislike article like
    public function article_like($user_id, $article_id) {
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

    //insert article's review
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

    //get all review list
    public function review_list($user_id, $article_id) {

        //calculate time difference
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

    //like-dislike article review
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

    //insert article view
	public function article_views($user_id, $post_id) {
		$total_query = $this->db->query("SELECT views FROM `article` where id='$post_id' limit 1");
        $get_list = $total_query->row_array();
        $views = $get_list['views'];
        $total_views = $views+1;
        $querys = $this->db->query("UPDATE `article` SET `views`='$total_views' WHERE id='$post_id'");

        return array(
            'status' => 200,
            'message' => 'success',
            'article_views' => $total_views
        );
    }

    //add article's bookmark
    public function article_bookmark($user_id, $article_id) {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');

        $count_query = $this->db->query("SELECT id FROM `article_bookmark` WHERE user_id='$user_id' and article_id='$article_id'");
        $count = $count_query->num_rows();

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
                'article_id' => $article_id
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

    //follow article
    public function article_follow($user_id, $post_id) {
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

    //insert new user's story
    public function your_story($user_id, $title, $description) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');

        $your_story = array(
            'user_id' => $user_id,
            'title' => $title,
            'description' => $description,
            'upload_date' => $date
        );
        $this->db->insert('your_story', $your_story);
        $post_id = $this->db->insert_id();
        return $post_id;
    }

}
